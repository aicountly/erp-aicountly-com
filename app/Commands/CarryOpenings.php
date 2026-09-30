<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use App\Models\Admin\ReportingModel;

/**
 * Sets named ledgers' OPENING balances to what the previous year closed them at.
 *
 * WHY THIS IS NOT AUTOMATIC
 * The audit's year-end section lists every ledger whose opening differs from the previous closing, and it is
 * tempting to read that list as a work order. It is not. A ledger retired in favour of a new one shows up
 * there exactly like a mistake does: the old ledger's opening drops to nil, the new one's picks the balance
 * up, and the two differences are equal and opposite. On the books this was built for, three pairs are
 * legitimate migrations and one pair that looks identical - KARAN GUPTA and KARAN GUPTA CURRENT AC - is two
 * different accounts, both of them opened wrongly. No arithmetic can tell those apart.
 *
 * So nothing is touched unless it is named. Run without --accounts and the command only reports what differs,
 * with the figure each ledger would be set to, and leaves the database alone.
 *
 * WHAT IT DOES
 * For each ledger named, it sets `accoppybal.acc_op_bal` to that ledger's closing balance in the previous
 * financial year - read through the same engine the reports use, so the figure is the report's own. The
 * Profit & Loss Appropriation is different in kind: its opening is the previous closing less the previous
 * year's result, and that result may still be under discussion, so it is only ever set from an amount given
 * on the command line with --appropriation.
 *
 * Every run prints what "Difference in Opening" will read afterwards, and the change is kept only if it
 * reads back as predicted. Dry run unless --apply.
 *
 * TAKE A DATABASE BACKUP BEFORE USING --apply.
 */
class CarryOpenings extends BaseCommand
{
    protected $group       = 'Accounting';
    protected $name        = 'books:carry-openings';
    protected $description = "Sets named ledgers' opening balances to the previous year's closing (dry run unless --apply).";
    protected $usage       = 'books:carry-openings --company ID --fy ID --branch ID [--accounts ID,ID] [--appropriation AMOUNT] [--csv FILE] [--apply]';
    protected $options     = [
        '--company'        => 'Company id (cmp_id).',
        '--fy'             => 'Financial year id (cmpfymastr_id) whose openings are to be set.',
        '--branch'         => 'Branch id (hobo_id). Required: openings are held per branch.',
        '--accounts'       => "Ledger ids to set to the previous year's closing. Without this nothing is written.",
        '--appropriation'  => "Amount to set 'Profit & Loss Appropriation' to, written as the report does: 305221.27Dr or 200Cr (a bare number is a debit).",
        '--csv'            => 'Write one line per planned change to this file (with the value it replaces).',
        '--apply'          => 'Commit the changes. Without this nothing is written.',
    ];

    /** Balances closer than this are equal; every ledger is held to two decimals. */
    private const TOLERANCE = 0.005;

    private $db;
    private int $cmp = 0;
    private int $fy  = 0;
    private int $bo  = 0;
    private ?ReportingModel $m = null;

    public function run(array $params)
    {
        $this->cmp = (int)($this->opt('company') ?? 0);
        $this->fy  = (int)($this->opt('fy') ?? 0);
        $this->bo  = (int)($this->opt('branch') ?? 0);
        if ($this->cmp <= 0 || $this->fy <= 0 || $this->bo <= 0) {
            CLI::error('--company, --fy and --branch are all required.');
            CLI::write('  ' . $this->usage);
            return 1;
        }
        $this->db = Database::connect();

        $fyRow = $this->fyRow($this->fy);
        if (!$fyRow) { CLI::error("Could not read financial year {$this->fy} of company {$this->cmp}."); return 1; }
        $prev = $this->previousFy($fyRow);
        if (!$prev) { CLI::error('There is no earlier financial year for this company.'); return 1; }

        $from  = date('Y-m-d', strtotime((string)$fyRow['fy_beg_date']));
        $to    = date('Y-m-d', strtotime((string)$fyRow['fy_end_date']));
        $pFy   = (int)$prev['cmpfymastr_id'];
        $pFrom = date('Y-m-d', strtotime((string)$prev['fy_beg_date']));
        $pTo   = date('Y-m-d', strtotime((string)$prev['fy_end_date']));
        $apply = $this->opt('apply') !== null;

        $this->heading('OPENING BALANCES  company ' . $this->cmp . '  fy ' . $this->fy . '  branch ' . $this->bo);
        CLI::write('  from fy ' . $pFy . ' (' . $pFrom . ' .. ' . $pTo . ')    mode: '
            . ($apply ? 'APPLY (changes are committed)' : 'DRY RUN (nothing is written)'));
        CLI::write("  a ledger is set to its own closing balance in the previous year, and only if it is named");
        CLI::newLine();

        // previous year and this year, each read through the engine the reports use
        $this->openSession($prev, $pFy, $pFrom, $pTo);
        $pSnap    = $this->m->engine()->snapshot($pFrom, $pTo, 0);
        [$pOp, $pCl] = $this->m->stockFigures($pFrom, $pTo, 0, true);
        $pProfit  = (float)$this->m->engine()->profitLoss($pSnap, $pOp, $pCl, 'cum')['net'];

        $this->openSession($fyRow, $this->fy, $from, $to);
        $snap     = $this->m->engine()->snapshot($from, $to, 0);
        [$opStock] = $this->m->stockFigures($from, $to, 0, true);
        $diffNow  = round((float)$this->m->engine()->openingTotal($snap) + $opStock, 2);

        CLI::write('  previous year: profit ' . $this->drcr(-$pProfit) . ' (profit = credit), closing stock ' . $this->n($pCl));
        CLI::write("  'Difference in Opening' as it stands : " . $this->drcr($diffNow));
        CLI::newLine();

        $plan = $this->plan($pSnap, $snap, $pProfit);
        $this->showDiffering($pSnap, $snap, $plan['named']);
        $this->showAppropriation($pSnap, $snap, $pProfit);

        if (!$plan['changes']) {
            CLI::write('  Nothing named, so nothing is written. Name the ledgers to set with --accounts,',
                $plan['named'] ? 'green' : 'yellow');
            CLI::write("  and give --appropriation for 'Profit & Loss Appropriation' if it is one of them.");
            return 0;
        }

        $this->showPlan($plan);
        if ($this->opt('csv') !== null) { $this->writeCsv((string)$this->opt('csv'), $plan); }

        $moved     = 0.0;
        foreach ($plan['changes'] as $c) { $moved = round($moved + ($c['to'] - ($c['was'] ?? 0.0)), 2); }
        $predicted = round($diffNow + $moved, 2);

        // ------------------------------------------------------------------------------------------------
        //  write it, read it back, and keep it only if it reads back right
        // ------------------------------------------------------------------------------------------------
        $this->db->transBegin();
        $counts = $this->write($plan['changes']);
        $this->openSession($fyRow, $this->fy, $from, $to);
        $after  = $this->m->engine()->snapshot($from, $to, 0);
        [$opStockAfter] = $this->m->stockFigures($from, $to, 0, true);
        $diffAfter = round((float)$this->m->engine()->openingTotal($after) + $opStockAfter, 2);
        $ok = abs(round($diffAfter - $predicted, 2)) < 0.02;

        $this->heading('RESULT');
        CLI::write('  rows inserted / updated : ' . $counts['inserted'] . ' / ' . $counts['updated']);
        CLI::write("  'Difference in Opening' before : " . $this->drcr($diffNow));
        CLI::write("  'Difference in Opening' after  : " . $this->drcr($diffAfter));
        CLI::write('  predicted from the changes     : ' . $this->drcr($predicted)
            . ($ok ? '   - they agree' : '   - THEY DO NOT AGREE'), $ok ? 'green' : 'yellow');
        if ($ok) {
            CLI::write(abs($diffAfter) < 0.02
                ? '  The opening data of this year now balances.'
                : '  ' . $this->drcr($diffAfter) . ' is still left; the list above says which ledgers it sits on.',
                abs($diffAfter) < 0.02 ? 'green' : 'yellow');
        }

        if (!$apply) {
            $this->db->transRollback();
            CLI::newLine();
            CLI::write('  DRY RUN: everything above was rolled back; the database is exactly as it was.', 'yellow');
            CLI::write('  Take a database backup, have the figures agreed, then run the same command with --apply.');
            return 0;
        }
        if (!$ok) {
            $this->db->transRollback();
            CLI::newLine();
            CLI::error('  Rolled back: the openings did not read back as the changes predicted, so nothing was changed.');
            return 1;
        }
        $this->db->transCommit();
        CLI::newLine();
        CLI::write('  APPLIED and committed. Re-run `php spark audit:books` to see the reports.', 'green');
        return 0;
    }

    // ==================================================================================================
    //  the plan
    // ==================================================================================================

    /**
     * One change per named ledger whose opening is not already the previous year's closing. The Appropriation
     * is never derived here: its figure depends on the previous year's result, which is an accounting matter,
     * so it comes from --appropriation or it is not touched.
     *
     * @return array{changes: array<int,array<string,mixed>>, named: array<int,int>, app: int}
     */
    private function plan($pSnap, $snap, float $pProfit): array
    {
        $named = [];
        foreach (explode(',', (string)($this->opt('accounts') ?? '')) as $piece) {
            $id = (int)trim($piece);
            if ($id > 0) { $named[$id] = $id; }
        }
        $appId = $this->appropriationId();
        $appTo = $this->opt('appropriation');

        $held    = $this->storedOpenings();
        $changes = [];
        foreach ($named as $id) {
            if ($id === $appId && $appTo === null) {
                CLI::write('  [WARN] ledger ' . $id . " is 'Profit & Loss Appropriation'. Its opening is the previous"
                    . ' closing less that year\'s result, which is an accounting decision, so give the figure'
                    . ' with --appropriation. Left alone.', 'yellow');
                continue;
            }
            // from the table, not the snapshot: the snapshot carries every mapped ledger at 0.00 whether or
            // not a row exists, and "no row" and "a row saying nothing" are undone differently.
            $was = $held[$id] ?? null;
            $to  = $id === $appId
                 ? $this->amount($appTo)
                 : round((float)($pSnap->accounts[$id]['closing'] ?? 0.0), 2);
            $name = trim(strip_tags((string)($snap->accounts[$id]['name'] ?? $pSnap->accounts[$id]['name'] ?? ('acc ' . $id))));
            if ($was !== null && abs($was - $to) <= self::TOLERANCE) { continue; }
            $changes[] = ['acc' => $id, 'name' => $name, 'was' => $was, 'to' => $to,
                          'why' => $id === $appId ? 'given with --appropriation'
                                 : "the previous year's closing balance"];
        }
        if ($appTo !== null && !isset($named[$appId])) {
            CLI::write('  [WARN] --appropriation was given but ledger ' . $appId . ' is not in --accounts; ignored.', 'yellow');
        }
        return ['changes' => $changes, 'named' => $named, 'app' => $appId];
    }

    // ==================================================================================================
    //  writing
    // ==================================================================================================

    /** This year's stored openings, by ledger; a ledger with no row is absent rather than nil. @return array<int,float> */
    private function storedOpenings(): array
    {
        $out = [];
        foreach ($this->db->table('accoppybal')->where('cmp_id', $this->cmp)
                 ->where('cmpfymastr_id', $this->fy)->where('hobo_id', $this->bo)->get()->getResultArray() as $r) {
            $id = (int)$r['acc_id'];
            $out[$id] = round(($out[$id] ?? 0.0) + (float)$r['acc_op_bal'], 2);
        }
        return $out;
    }

    /** @param array<int,array<string,mixed>> $changes @return array{inserted: int, updated: int} */
    private function write(array $changes): array
    {
        $inserted = 0; $updated = 0;
        foreach ($changes as $c) {
            $where = ['cmp_id' => $this->cmp, 'cmpfymastr_id' => $this->fy,
                      'hobo_id' => $this->bo, 'acc_id' => $c['acc']];
            if ($c['was'] !== null) {
                $this->db->table('accoppybal')->where($where)->update(['acc_op_bal' => $c['to']]);
                $updated++;
            } else {
                $this->db->table('accoppybal')->insert($where + ['acc_op_bal' => $c['to'], 'acc_py_bal' => 0, 'acc_memo_bal' => 0]);
                $inserted++;
            }
        }
        return ['inserted' => $inserted, 'updated' => $updated];
    }

    // ==================================================================================================
    //  output
    // ==================================================================================================

    /** Every ledger whose opening differs from the previous closing, so the reader can see what was not named. */
    private function showDiffering($pSnap, $snap, array $named): void
    {
        $rows = [];
        foreach (array_unique(array_merge(array_keys($pSnap->accounts), array_keys($snap->accounts))) as $id) {
            $pa = $pSnap->accounts[$id] ?? null; $ca = $snap->accounts[$id] ?? null;
            $cat = (int)($pa['cat'] ?? $ca['cat'] ?? 0);
            if (\App\Libraries\AccountingEngine::isPlCat($cat)) { continue; }   // these start every year at zero
            $close = $pa ? round((float)$pa['closing'], 2) : 0.0;
            $open  = $ca ? round((float)$ca['op'], 2) : 0.0;
            if (abs($close - $open) < self::TOLERANCE) { continue; }
            $rows[] = ['id' => $id, 'name' => trim(strip_tags((string)($ca['name'] ?? $pa['name'] ?? ''))),
                       '>prev closing' => $this->drcr($close), '>opening' => $this->drcr($open),
                       '>would become' => isset($named[$id]) ? $this->drcr($close) : '-',
                       'named' => isset($named[$id]) ? 'yes' : ''];
        }
        if (!$rows) { CLI::write('  Every balance-sheet ledger already opens with the previous year\'s closing.', 'green'); return; }
        CLI::write('  ledgers whose opening is not the previous closing (only the named ones are touched):');
        $this->table($rows, ['id' => 'acc_id', 'name' => 'ledger', '>prev closing' => '>prev closing',
                             '>opening' => '>opening now', '>would become' => '>would become', 'named' => 'named']);
        CLI::write('         A retired ledger and its replacement look exactly like a pair of mistakes here, so');
        CLI::write('         read this list, do not run it. Nothing without "yes" is touched.');
        CLI::newLine();
    }

    /**
     * The Appropriation is the one ledger whose opening is NOT simply last year's closing: it is that closing
     * less last year's result. The arithmetic is printed so the figure does not have to be worked out by hand,
     * but the command still will not act on it without being told, because which result to carry is a matter
     * for whoever signs the accounts.
     */
    private function showAppropriation($pSnap, $snap, float $pProfit): void
    {
        $id = $this->appropriationId();
        if ($id <= 0) {
            CLI::write("  [INFO] this company has no 'Profit & Loss Appropriation' ledger, so last year's result"
                . ' of ' . $this->drcr(-$pProfit) . ' has nowhere to be carried.', 'yellow');
            CLI::newLine();
            return;
        }
        $open     = isset($snap->accounts[$id]) ? round((float)$snap->accounts[$id]['op'], 2) : 0.0;
        $pClose   = round((float)($pSnap->accounts[$id]['closing'] ?? 0.0), 2);
        $expected = round($pClose - $pProfit, 2);
        CLI::write("  'Profit & Loss Appropriation' (acc $id): opening " . $this->drcr($open)
            . '; the previous year closed it at ' . $this->drcr($pClose) . ' and made ' . $this->drcr(-$pProfit) . ',');
        CLI::write('         so carrying both would put it at ' . $this->drcr($expected)
            . (abs($open - $expected) < self::TOLERANCE ? ' - which is what it holds.'
               : '. Pass --appropriation ' . $this->n(abs($expected)) . ($expected < 0 ? 'Cr' : 'Dr') . ' to set it.'));
        CLI::newLine();
    }

    /** @param array{changes: array<int,array<string,mixed>>} $plan */
    private function showPlan(array $plan): void
    {
        CLI::write('  the openings this would write:');
        foreach ($plan['changes'] as $c) {
            CLI::write('         acc ' . str_pad((string)$c['acc'], 6) . ' ' . str_pad($c['name'], 30)
                . ' ' . str_pad($c['why'], 36) . ' '
                . ($c['was'] === null ? '(no row)' : 'was ' . $this->drcr((float)$c['was']))
                . '  ->  ' . $this->drcr((float)$c['to']));
        }
        CLI::newLine();
    }

    /** @param array{changes: array<int,array<string,mixed>>} $plan */
    private function writeCsv(string $file, array $plan): void
    {
        $fh = @fopen($file, 'w');
        if (!$fh) { CLI::write('  [WARN] could not write ' . $file, 'yellow'); return; }
        fputcsv($fh, ['cmp_id', 'cmpfymastr_id', 'hobo_id', 'acc_id', 'ledger', 'why', 'was', 'to']);
        foreach ($plan['changes'] as $c) {
            fputcsv($fh, [$this->cmp, $this->fy, $this->bo, $c['acc'], $c['name'], $c['why'],
                          $c['was'] === null ? '' : number_format((float)$c['was'], 2, '.', ''),
                          number_format((float)$c['to'], 2, '.', '')]);
        }
        fclose($fh);
        CLI::write('  [INFO] wrote ' . count($plan['changes']) . ' line(s) to ' . $file);
    }

    // ==================================================================================================
    //  helpers
    // ==================================================================================================

    /**
     * An amount as the reports write it: "200Cr" is a credit, "305221.27Dr" and a bare number are debits.
     * Spelling a credit out avoids a leading minus, which a command line reads as the start of an option.
     */
    private function amount(string $raw): float
    {
        $t = strtoupper(preg_replace('/[\s,]/', '', trim($raw)));
        $sign = 1.0;
        if (str_ends_with($t, 'CR')) { $sign = -1.0; $t = substr($t, 0, -2); }
        elseif (str_ends_with($t, 'DR')) { $t = substr($t, 0, -2); }
        return round($sign * (float)$t, 2);
    }

    private function appropriationId(): int
    {
        $r = $this->db->query("SELECT acc_id FROM acctmaster WHERE cmp_id = ? AND LOWER(TRIM(acc_name)) = 'profit & loss appropriation' ORDER BY acc_id LIMIT 1",
            [$this->cmp])->getRowArray();
        return $r ? (int)$r['acc_id'] : 0;
    }

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

    /** @return array<string,mixed>|null */
    private function previousFy(array $fyRow): ?array
    {
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

    private function univ()
    {
        static $u = null;
        if ($u === null) {
            try { $u = (new \App\Libraries\externaldb())->univaictly_db(); } catch (\Throwable $e) { $u = false; }
        }
        return $u ?: null;
    }

    /** The reports read the company, branch and year from the session, as they do on the screen. */
    private function openSession(array $fyRow, int $fy, string $start, string $end): void
    {
        $_SESSION = [
            'ses_company_id'           => $this->cmp,
            'ses_comp_fy_id'           => $fy,
            'ses_boid'                 => $this->bo,
            'ses_company_fy_beginning' => $start,
            'ses_company_fy_end'       => $end,
            'ses_dflt_val_method'      => $fyRow['def_val_method'] ?? 'AVG',
        ];
        $this->m = new ReportingModel();
    }

    private function n(float $v): string { return number_format($v, 2, '.', ','); }

    private function drcr(float $v): string
    {
        if (abs($v) < self::TOLERANCE) { return '0.00'; }
        return $this->n(abs($v)) . ($v > 0 ? ' Dr' : ' Cr');
    }

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
