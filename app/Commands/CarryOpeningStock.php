<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Sets a financial year's OPENING stock to what the previous year actually closed with.
 *
 * WHY THIS EXISTS
 * The year-end roll-over (FYModel::copy_itemoppybal_with_txn) copies the previous year's OPENING into the
 * new year instead of its CLOSING - despite its name it adds no transactions at all:
 *
 *      $data['itm_op_bal_qty'] = $row['itm_op_bal_qty'] ?? 0;   // last year's opening
 *      $data['itm_op_val_amt'] = $row['itm_op_val_amt'] ?? 0;   // not last year's closing
 *
 * So a company whose first year opened at nil opens at nil for ever, however much stock it carries. The
 * damage is not confined to the stock report: opening stock is a line of the Profit & Loss, closing stock
 * is derived from it, and "Difference in Opening" on the Balance Sheet carries the gap. Worse, the
 * valuation engine needs BOTH an opening quantity and an opening value before it will use an average
 * (StockStatusModel: `$running_avg = ($opQty > 0 && $opVal > 0) ? ($opVal / $opQty) : 0.0;`), so with the
 * opening missing every issue is relieved at nil, the quantity goes negative and the value does not move.
 *
 * WHAT THIS COMMAND DOES
 * It reads the previous year's closing stock item by item - through the same model the Stock Status screen
 * uses, so the figures are the report's own and not a second calculation - and writes them as this year's
 * opening:
 *
 *   itmoppybal.itm_op_bal_qty   the opening QUANTITY
 *   itmoppyval.itm_op_val_amt   the opening VALUE
 *
 * Both tables, always: the value alone leaves the quantity at nil, which leaves the average at nil, which
 * leaves the year worse off than before. An item's value goes on the row for the valuation method that item
 * is actually kept on, and any other method's row for the same item is set to nil, because the reports sum
 * every method row without filtering and would otherwise count the stock twice.
 *
 * Nothing is invented. Every figure written is a figure the previous year's own report produces, and an
 * item the previous year closed at nil is left alone. The command is a dry run unless --apply is given, and
 * even then it writes inside a transaction, re-reads the opening it has just written, and commits only if
 * that reads back as the previous year's closing.
 *
 * TAKE A DATABASE BACKUP BEFORE USING --apply.
 */
class CarryOpeningStock extends BaseCommand
{
    protected $group       = 'Accounting';
    protected $name        = 'books:carry-opening-stock';
    protected $description = "Sets a financial year's opening stock to the previous year's closing stock (dry run unless --apply).";
    protected $usage       = 'books:carry-opening-stock --company ID --fy ID --branch ID [--csv FILE] [--apply]';
    protected $options     = [
        '--company'  => 'Company id (cmp_id).',
        '--fy'       => 'Financial year id (cmpfymastr_id) whose OPENING stock is to be set.',
        '--branch'   => 'Branch id (hobo_id). Required: opening stock is held per branch.',
        '--prev-fy'  => 'Previous financial year id, if it cannot be worked out from the year master.',
        '--csv'      => 'Write one line per planned change to this file (with the value it replaces).',
        '--apply'    => 'Commit the changes. Without this nothing is written.',
    ];

    /** Quantities closer than this are equal; values are held to two decimals. */
    private const TOLERANCE = 0.005;

    private $db;
    private int $cmp = 0;
    private int $fy  = 0;
    private int $bo  = 0;

    public function run(array $params)
    {
        $this->cmp = (int)($this->opt('company') ?? 0);
        $this->fy  = (int)($this->opt('fy') ?? 0);
        $this->bo  = (int)($this->opt('branch') ?? 0);
        if ($this->cmp <= 0 || $this->fy <= 0 || $this->bo <= 0) {
            CLI::error('--company, --fy and --branch are all required.');
            CLI::write('  ' . $this->usage);
            CLI::write('  php spark audit:books --list   lists the companies and their financial years');
            return 1;
        }
        $this->db = Database::connect();

        $fyRow = $this->fyRow($this->fy);
        if (!$fyRow) {
            CLI::error("Could not read financial year {$this->fy} of company {$this->cmp}.");
            return 1;
        }
        $prev = $this->previousFy($fyRow);
        if (!$prev) {
            CLI::error('There is no earlier financial year for this company, so there is no closing stock to carry.');
            CLI::write('  give one explicitly with --prev-fy if the year master cannot be read from here.');
            return 1;
        }
        $prevFy    = (int)$prev['cmpfymastr_id'];
        $prevStart = date('Y-m-d', strtotime((string)$prev['fy_beg_date']));
        $prevEnd   = date('Y-m-d', strtotime((string)$prev['fy_end_date']));
        $apply     = $this->opt('apply') !== null;

        $this->heading('OPENING STOCK carried forward  company ' . $this->cmp . '  into fy ' . $this->fy . '  branch ' . $this->bo);
        CLI::write('  from fy ' . $prevFy . ' (' . $prevStart . ' .. ' . $prevEnd . ')    mode: '
            . ($apply ? 'APPLY (changes are committed)' : 'DRY RUN (nothing is written)'));
        CLI::write("  every figure written is one the previous year's own Stock Status report produces");
        CLI::newLine();

        $closing = $this->previousClosing($prevFy, $prevEnd, $prev);
        if ($closing === null) {
            CLI::error('The stock report could not be read from here, so there is nothing to carry.');
            return 1;
        }
        if (!$closing) {
            CLI::write('  the previous year closed with no stock; there is nothing to carry.', 'green');
            return 0;
        }

        $current = $this->currentOpening();
        $plan    = $this->plan($closing, $current);

        $prevTotal = 0.0; foreach ($closing as $c) { $prevTotal += $c['value']; }
        CLI::write('  previous year closed with : ' . count($closing) . ' item(s), value ' . $this->n($prevTotal));
        CLI::write('  this year opens with      : ' . $this->n($current['total']) . ' over ' . $current['rows'] . ' stored row(s)');
        CLI::write('  rows to change            : ' . count($plan['changes']));
        CLI::newLine();

        $this->showPlan($plan, $closing);
        if ($this->opt('csv') !== null) { $this->writeCsv((string)$this->opt('csv'), $plan); }

        if (!$plan['changes']) {
            CLI::write('  Nothing to change: this year already opens with the previous year\'s closing stock.', 'green');
            return 0;
        }

        // ------------------------------------------------------------------------------------------------
        //  write it, read it back, and keep it only if it reads back right
        // ------------------------------------------------------------------------------------------------
        $this->db->transBegin();
        $counts = $this->write($plan['changes']);
        $after    = $this->currentOpening();
        $expected = round($prevTotal + $plan['leftover_value'], 2);
        $ok       = abs(round($after['total'] - $expected, 2)) < 0.02;
        $qtyOk  = true;
        foreach ($closing as $key => $c) {
            if (abs(($after['qty'][$key] ?? 0.0) - $c['qty']) > self::TOLERANCE) { $qtyOk = false; break; }
        }

        $this->heading('RESULT');
        CLI::write('  rows inserted / updated : ' . $counts['inserted'] . ' / ' . $counts['updated']);
        CLI::write('  opening stock before    : ' . $this->n($current['total']));
        CLI::write('  opening stock after     : ' . $this->n($after['total']));
        CLI::write("  previous year's closing : " . $this->n($prevTotal));
        if (abs($plan['leftover_value']) > self::TOLERANCE) {
            CLI::write('  left alone on items the previous year did not close with : ' . $this->n($plan['leftover_value']));
        }
        CLI::write('  the two together        : ' . $this->n($expected)
            . ($ok ? '   - they agree' : '   - THEY DO NOT AGREE'), $ok ? 'green' : 'yellow');
        CLI::write('  opening quantities match the previous closing, item by item: ' . ($qtyOk ? 'yes' : 'NO'),
            $qtyOk ? 'green' : 'yellow');
        CLI::newLine();
        CLI::write('  What this does to the rest of the reports:');
        CLI::write('    - Profit & Loss: opening stock becomes ' . $this->n($after['total']) . ' instead of ' . $this->n($current['total'])
            . ', and the closing stock is revalued off it');
        CLI::write("    - Balance Sheet: 'Difference in Opening' loses the stock part of its gap");
        CLI::write('    Run `php spark audit:books --company ' . $this->cmp . ' --fy ' . $this->fy . ' --branch ' . $this->bo
            . '` afterwards to see both.');

        if (!$apply) {
            $this->db->transRollback();
            CLI::newLine();
            CLI::write('  DRY RUN: everything above was rolled back; the database is exactly as it was.', 'yellow');
            CLI::write('  Take a database backup, have the figures agreed, then run the same command with --apply.');
            return 0;
        }
        if (!$ok || !$qtyOk) {
            $this->db->transRollback();
            CLI::newLine();
            CLI::error('  Rolled back: what was written did not read back as the previous year\'s closing, so nothing was changed.');
            return 1;
        }
        $this->db->transCommit();
        CLI::newLine();
        CLI::write('  APPLIED and committed.', 'green');
        CLI::write('  The roll-over that caused this is not fixed by running this command: the next year will');
        CLI::write('  open at nil again unless it is run for that year too.');
        return 0;
    }

    // ==================================================================================================
    //  the previous year's closing stock
    // ==================================================================================================

    /**
     * The previous year's closing stock, item by item, read through the model the Stock Status screen uses so
     * the figures are the report's own. Items that closed at nil quantity AND nil value are left out: there is
     * nothing to carry, and writing a row for them would only invite the sum to be counted twice.
     *
     * @return array<string,array{qty: float, value: float, name: string, method: string}>|null  null when unreadable
     */
    private function previousClosing(int $prevFy, string $prevEnd, array $prevRow): ?array
    {
        $out = [];
        try {
            $model = new \App\Models\Admin\StockStatusModel();
            $this->openSession($prevRow, $prevFy);
            for ($page = 1; $page <= 200; $page++) {
                $raw = $model->inventoryStatusPaged($this->cmp, $this->bo, $prevFy, [
                    'to_date_ymd' => $prevEnd,
                    'val_id'      => (string)($prevRow['def_val_method'] ?? 'AVG'),
                    'nill'        => 1,                 // every item, so nothing is silently skipped
                    'pq_curpage'  => $page,
                    'pq_rpp'      => 100,
                ]);
                $resp = json_decode((string)$raw, true);
                if (!is_array($resp) || empty($resp['data'])) { break; }
                foreach ($resp['data'] as $r) {
                    $key = (string)($r['itm_id_unit_id'] ?? '');
                    if ($key === '') { continue; }
                    $qty = round((float)($r['item_qty_avail'] ?? 0), 4);
                    $val = round((float)($r['item_value_avail'] ?? 0), 2);
                    if (abs($qty) < self::TOLERANCE && abs($val) < self::TOLERANCE) { continue; }
                    $out[$key] = ['qty' => $qty, 'value' => $val,
                                  'name' => trim((string)($r['item_name'] ?? $key)),
                                  'method' => strtoupper(trim((string)($r['method'] ?? $r['val'] ?? 'AVG')))];
                }
                $total = (int)($resp['totalRecords'] ?? 0);
                if ($total <= $page * 100) { break; }
            }
        } catch (\Throwable $e) {
            CLI::write('  [WARN] ' . $e->getMessage(), 'yellow');
            return null;
        }
        // An item that closed on a negative quantity is the very defect this command exists to undo, and it
        // must not be carried into the next year as if it were a fact.
        foreach ($out as $key => $c) {
            if ($c['qty'] < -self::TOLERANCE) {
                CLI::write('  [WARN] ' . $c['name'] . ' closed the previous year on ' . $this->n($c['qty'])
                    . ' units, which no stock can do. Fix that year first; it is not carried.', 'yellow');
                unset($out[$key]);
            }
        }
        return $out;
    }

    // ==================================================================================================
    //  what this year currently opens with
    // ==================================================================================================

    /**
     * This year's stored opening: the quantity rows, the value rows, and the total the reports read (which is
     * the sum of every value row, whatever its valuation method - they do not filter by it).
     *
     * A method is held here under its NAME, whichever form the row stores it in - this table is written with
     * 'AVG' / 'FIFO' / 'LIFO' by the item screens, and a row holding 1 / 2 / 3 means the same thing. The form
     * each row actually uses is kept alongside, so one written the other way can be put right.
     *
     * @return array{qty: array<string,float>, val: array<string,array<string,float>>, raw: array<string,array<string,string>>, total: float, rows: int}
     */
    private function currentOpening(): array
    {
        $qty = []; $val = []; $total = 0.0; $rows = 0;
        foreach ($this->db->table('itmoppybal')->where('cmp_id', $this->cmp)
                 ->where('cmpfymastr_id', $this->fy)->where('hobo_id', $this->bo)->get()->getResultArray() as $r) {
            $k = (string)$r['itm_id_unit_id'];
            $qty[$k] = round(($qty[$k] ?? 0.0) + (float)$r['itm_op_bal_qty'], 4);
            $rows++;
        }
        $raw = [];
        foreach ($this->db->table('itmoppyval')->where('cmp_id', $this->cmp)
                 ->where('cmpfymastr_id', $this->fy)->where('hobo_id', $this->bo)->get()->getResultArray() as $r) {
            $k = (string)$r['itm_id_unit_id'];
            $stored = (string)$r['itm_val_method_id'];
            $m = $this->methodName($stored);          // 'AVG' whether the row says AVG or 3
            $val[$k][$m] = round(($val[$k][$m] ?? 0.0) + (float)$r['itm_op_val_amt'], 2);
            $raw[$k][$m] = $stored;
            $total = round($total + (float)$r['itm_op_val_amt'], 2);
            $rows++;
        }
        return ['qty' => $qty, 'val' => $val, 'raw' => $raw, 'total' => $total, 'rows' => $rows];
    }

    // ==================================================================================================
    //  the plan
    // ==================================================================================================

    /**
     * One change per row that has to move. A value goes on the row for the method the item is kept on; any
     * other method's row for that item is cleared, because the reports add every method row together.
     *
     * @param array<string,array{qty: float, value: float, name: string, method: string}> $closing
     * @param array{qty: array<string,float>, val: array<string,array<int,float>>, total: float, rows: int} $current
     * @return array{changes: array<int,array<string,mixed>>}
     */
    private function plan(array $closing, array $current): array
    {
        $changes = [];
        foreach ($closing as $key => $c) {
            $was = $current['qty'][$key] ?? null;
            if ($was === null || abs($was - $c['qty']) > self::TOLERANCE) {
                $changes[] = ['table' => 'itmoppybal', 'key' => $key, 'name' => $c['name'], 'method' => null,
                              'to' => $c['qty'], 'was' => $was, 'what' => 'quantity'];
            }
            $method = $this->methodName($c['method']);
            $stored = $current['val'][$key] ?? [];
            $wasVal = $stored[$method] ?? null;
            $form   = $current['raw'][$key][$method] ?? null;
            if ($form !== null && $form !== $method) {
                // The row means the right method but says it in a form nothing else in this table uses.
                $changes[] = ['table' => 'itmoppyval', 'key' => $key, 'name' => $c['name'], 'method' => $form,
                              'to' => $c['value'], 'was' => $wasVal, 'form' => $method,
                              'what' => 'method held as "' . $form . '" where this table uses "' . $method . '"'];
                continue;
            }
            if ($wasVal === null || abs($wasVal - $c['value']) > self::TOLERANCE) {
                $changes[] = ['table' => 'itmoppyval', 'key' => $key, 'name' => $c['name'], 'method' => $method,
                              'to' => $c['value'], 'was' => $wasVal, 'what' => 'value (' . $method . ')'];
            }
            foreach ($stored as $m => $amount) {                     // the other methods must not add to the total
                if ($m !== $method && abs($amount) > self::TOLERANCE) {
                    $changes[] = ['table' => 'itmoppyval', 'key' => $key, 'name' => $c['name'],
                                  'method' => $current['raw'][$key][$m] ?? $m, 'to' => 0.0, 'was' => $amount,
                                  'what' => 'value (' . $m . ') cleared, it would be counted twice'];
                }
            }
        }
        // Stock this year opens with that the previous year did not close with is not this command's to
        // remove: it may be a deliberate entry rather than a leftover. It is reported, left alone, and
        // counted, because it is the exact reason the opening total will not equal the previous closing.
        $leftover = []; $leftoverValue = 0.0;
        foreach (array_unique(array_merge(array_keys($current['qty']), array_keys($current['val']))) as $key) {
            if (isset($closing[$key])) { continue; }
            $q = $current['qty'][$key] ?? 0.0;
            $v = 0.0; foreach ($current['val'][$key] ?? [] as $amount) { $v = round($v + $amount, 2); }
            if (abs($q) > self::TOLERANCE || abs($v) > self::TOLERANCE) {
                $leftover[$key] = ['qty' => $q, 'value' => $v];
                $leftoverValue  = round($leftoverValue + $v, 2);
            }
        }
        return ['changes' => $changes, 'leftover' => $leftover, 'leftover_value' => $leftoverValue];
    }

    // ==================================================================================================
    //  writing
    // ==================================================================================================

    /** @param array<int,array<string,mixed>> $changes @return array{inserted: int, updated: int} */
    private function write(array $changes): array
    {
        $inserted = 0; $updated = 0;
        foreach ($changes as $c) {
            $where = ['cmp_id' => $this->cmp, 'cmpfymastr_id' => $this->fy,
                      'hobo_id' => $this->bo, 'itm_id_unit_id' => $c['key']];
            if ($c['table'] === 'itmoppyval') { $where['itm_val_method_id'] = $c['method']; }
            $column = $c['table'] === 'itmoppybal' ? 'itm_op_bal_qty' : 'itm_op_val_amt';

            if ($c['was'] === null) {
                $this->db->table($c['table'])->insert($where + [$column => $c['to']]);
                $inserted++;
            } else {
                $set = [$column => $c['to']];
                if (isset($c['form'])) { $set['itm_val_method_id'] = $c['form']; }
                $this->db->table($c['table'])->where($where)->update($set);
                $updated++;
            }
        }
        return ['inserted' => $inserted, 'updated' => $updated];
    }

    // ==================================================================================================
    //  output
    // ==================================================================================================

    /** @param array{changes: array<int,array<string,mixed>>} $plan */
    private function showPlan(array $plan, array $closing): void
    {
        if (!$plan['changes']) { return; }
        CLI::write('  the previous year closed with:');
        $tbl = [];
        foreach ($closing as $key => $c) {
            $tbl[] = ['item' => $c['name'], 'key' => $key, '>qty' => $this->n($c['qty']),
                      '>value' => $this->n($c['value']), 'method' => $c['method']];
        }
        $this->table($tbl, ['item' => 'item', 'key' => 'item_unit', '>qty' => '>closing qty',
                            '>value' => '>closing value', 'method' => 'method']);
        if (!empty($plan['leftover'])) {
            CLI::newLine();
            CLI::write('  left alone - this year opens with these, the previous year did not close with them:', 'yellow');
            $lt = [];
            foreach ($plan['leftover'] as $key => $l) {
                $lt[] = ['key' => $key, '>qty' => $this->n($l['qty']), '>value' => $this->n($l['value'])];
            }
            $this->table($lt, ['key' => 'item_unit', '>qty' => '>opening qty', '>value' => '>opening value']);
            CLI::write('         they may be deliberate, so nothing is removed; they are why the opening total');
            CLI::write('         below is the previous closing PLUS this amount rather than equal to it.');
        }
        CLI::newLine();
        CLI::write('  the rows this would write:');
        foreach ($plan['changes'] as $c) {
            CLI::write('         ' . str_pad($c['table'], 11) . ' ' . str_pad((string)$c['name'], 34)
                . ' ' . str_pad($c['what'], 46) . ' '
                . ($c['was'] === null ? '(new row)' : 'was ' . $this->n((float)$c['was']))
                . '  ->  ' . $this->n((float)$c['to']));
        }
        CLI::newLine();
    }

    /** @param array{changes: array<int,array<string,mixed>>} $plan */
    private function writeCsv(string $file, array $plan): void
    {
        $fh = @fopen($file, 'w');
        if (!$fh) { CLI::write('  [WARN] could not write ' . $file, 'yellow'); return; }
        fputcsv($fh, ['table', 'cmp_id', 'cmpfymastr_id', 'hobo_id', 'itm_id_unit_id', 'item',
                      'itm_val_method_id', 'what', 'was', 'to']);
        foreach ($plan['changes'] as $c) {
            fputcsv($fh, [$c['table'], $this->cmp, $this->fy, $this->bo, $c['key'], $c['name'],
                          $c['method'] ?? '', $c['what'],
                          $c['was'] === null ? '' : number_format((float)$c['was'], 4, '.', ''),
                          number_format((float)$c['to'], 4, '.', '')]);
        }
        fclose($fh);
        CLI::write('  [INFO] wrote ' . count($plan['changes']) . ' line(s) to ' . $file);
    }

    // ==================================================================================================
    //  helpers
    // ==================================================================================================

    /** @return array<string,mixed>|null */
    private function fyRow(int $fy): ?array
    {
        foreach ([fn() => $this->univ(), fn() => $this->db] as $conn) {
            try {
                $c = $conn();
                if (!$c) { continue; }
                $r = $c->table('cmpfymastr')->where('cmp_id', $this->cmp)->where('cmpfymastr_id', $fy)->get()->getRowArray();
                if ($r && !empty($r['fy_beg_date'])) { return $r; }
            } catch (\Throwable $e) { /* try the next connection */ }
        }
        return null;
    }

    /** The year that ends before this one begins - the one whose closing stock is this year's opening. */
    private function previousFy(array $fyRow): ?array
    {
        if ($this->opt('prev-fy') !== null) { return $this->fyRow((int)$this->opt('prev-fy')); }
        $start = date('Y-m-d', strtotime((string)$fyRow['fy_beg_date']));
        $best  = null;
        foreach ([fn() => $this->univ(), fn() => $this->db] as $conn) {
            try {
                $c = $conn();
                if (!$c) { continue; }
                foreach ($c->table('cmpfymastr')->where('cmp_id', $this->cmp)->orderBy('fy_beg_date')->get()->getResultArray() as $f) {
                    if (date('Y-m-d', strtotime((string)$f['fy_end_date'])) < $start) { $best = $f; }
                }
                if ($best) { return $best; }
            } catch (\Throwable $e) { /* try the next connection */ }
        }
        return null;
    }

    /** The separate company / financial-year database, or null when this installation has none. */
    private function univ()
    {
        static $u = null;
        if ($u === null) {
            try { $u = (new \App\Libraries\externaldb())->univaictly_db(); } catch (\Throwable $e) { $u = false; }
        }
        return $u ?: null;
    }

    /** The stock code reads the company, branch and year from the session, as it does on the screen. */
    private function openSession(array $fyRow, int $fy): void
    {
        $_SESSION = [
            'ses_company_id'           => $this->cmp,
            'ses_comp_fy_id'           => $fy,
            'ses_boid'                 => $this->bo,
            'ses_company_fy_beginning' => date('Y-m-d', strtotime((string)$fyRow['fy_beg_date'])),
            'ses_company_fy_end'       => date('Y-m-d', strtotime((string)$fyRow['fy_end_date'])),
            'ses_dflt_val_method'      => $fyRow['def_val_method'] ?? 'AVG',
        ];
    }

    /**
     * The valuation method's canonical name. The item screens write 'AVG' / 'FIFO' / 'LIFO' into
     * itm_val_method_id and the export code reads either that or 1 / 2 / 3, so both are understood here and
     * the name is what gets written.
     */
    private function methodName(string $m): string
    {
        return match (strtoupper(trim($m))) { 'FIFO', '1' => 'FIFO', 'LIFO', '2' => 'LIFO', default => 'AVG' };
    }

    private function n(float $v): string { return number_format($v, 2, '.', ','); }

    private function heading(string $t): void
    {
        CLI::newLine();
        CLI::write(str_repeat('=', 100));
        CLI::write($t);
        CLI::write(str_repeat('=', 100));
    }

    /** @param array<int,array<string,mixed>> $rows @param array<string,string> $cols */
    private function table(array $rows, array $cols): void
    {
        if (!$rows) { return; }
        $w = [];
        foreach ($cols as $k => $t) { $w[$k] = mb_strlen(ltrim($t, '>')); }
        foreach ($rows as $r) { foreach ($cols as $k => $t) { $w[$k] = max($w[$k], mb_strlen((string)($r[$k] ?? ''))); } }
        $line = function (array $r) use ($cols, $w): string {
            $cells = [];
            foreach ($cols as $k => $t) {
                $v = (string)($r[$k] ?? '');
                $cells[] = $t[0] === '>' ? str_pad($v, $w[$k], ' ', STR_PAD_LEFT) : str_pad($v, $w[$k]);
            }
            return '         ' . implode('  ', $cells);
        };
        $head = []; foreach ($cols as $k => $t) { $head[$k] = ltrim($t, '>'); }
        CLI::write($line($head));
        foreach ($rows as $r) { CLI::write($line($r)); }
    }

    private function opt(string $name): ?string
    {
        $v = CLI::getOption($name);
        return ($v === null || $v === false) ? null : (is_bool($v) ? '' : (string)$v);
    }
}
