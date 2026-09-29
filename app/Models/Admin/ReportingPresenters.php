<?php
namespace App\Models\Admin;

use App\Libraries\AccountingEngine;
use App\Libraries\LedgerSnapshot;

/**
 * Trial Balance / Profit & Loss / Balance Sheet presenters.
 *
 * All numbers come from App\Libraries\AccountingEngine; the methods below only lay them
 * out in the row shapes the views (and the Excel writer) consume. Every amount that is
 * displayed is also carried as a number next to its formatted text:
 *   horizontal reports  l_amt / l_det / r_amt / r_det   (null = the cell is blank on screen)
 *   vertical reports    amt                             (null = blank)
 *   trial balance       debit_total / credit_total      (+ debit / credit text, '' = blank)
 * so an export can be written from exactly what the screen shows.
 *
 * The previous implementations are kept in ReportingModel as legacy_* (used only by the
 * audit command to show old-versus-new figures on real data).
 */
trait ReportingPresenters
{
    private ?AccountingEngine $engineInstance = null;
    /** @var array<int,string>|null */
    private ?array $parentNameCache = null;

    /** The shared engine bound to the session's company / FY / branch. */
    public function engine(): AccountingEngine
    {
        if ($this->engineInstance === null) {
            $this->engineInstance = new AccountingEngine(
                $this->db,
                (int)$this->company_id,
                (int)$this->fy_id,
                (int)$this->bo_id,
                (string)$this->session->get('ses_company_fy_beginning'),
                (string)$this->session->get('ses_company_fy_end')
            );
        }
        return $this->engineInstance;
    }

    /** @var array<string,array{0:float,1:float}> */
    private array $stockMemo = [];

    /** grpparentn names by category id (one query per request). */
    protected function parentNames(): array
    {
        if ($this->parentNameCache === null) {
            $this->parentNameCache = [];
            foreach ($this->db->table('grpparentn')->select('acc_grp_parent_id, acc_grp_parent_name')->get()->getResultArray() as $r) {
                $this->parentNameCache[(int)$r['acc_grp_parent_id']] = (string)$r['acc_grp_parent_name'];
            }
        }
        return $this->parentNameCache;
    }

    /**
     * Opening / closing stock for a report.
     *   $cumulative = false : opening as at `from` (period reports)
     *   $cumulative = true  : opening as at the FY start (balance sheet / trial balance, which are
     *                         balances as on `to` and therefore measure the year from its start)
     * The consolidated flag is passed to BOTH values so that stock always has the same scope as the ledgers.
     *
     * @return array{0:float,1:float} [opening, closing]
     */
    public function stockFigures(string $from, string $to, int $consolidated, bool $cumulative): array
    {
        $key = $from . '|' . $to . '|' . $consolidated . '|' . (int)$cumulative;      // the stock walk is expensive: once per request
        if (!isset($this->stockMemo[$key])) {
            $closing = (float)$this->StockStatusModel->closingStockTotal($from, $to, ['consolidated' => $consolidated]);
            $opening = $this->openingStockFigure($cumulative ? $this->engine()->fyStart() : $from, $consolidated);
            $this->stockMemo[$key] = [$opening, round($closing, 2)];
        }
        return $this->stockMemo[$key];
    }

    /** Opening stock as at a date (FY start = the itmoppyval total), scoped like the ledgers. */
    protected function openingStockFigure(string $onDate, int $consolidated): float
    {
        return round((float)$this->openingStockTotal(
            $onDate, $this->company_id, $this->fy_id, $this->session->get('ses_dflt_val_method'),
            ['consolidated' => $consolidated], $consolidated
        ), 2);
    }

    /** Text shown in a cell: blank for zero (exactly what the grids always did). */
    private function cellText(float $n): string
    {
        $n = round($n, 2);
        return $n != 0.0 ? formatAmount($n) : '';
    }

    /** Number shown in a cell (null when the cell is blank). */
    private function cellNum(float $n): ?float
    {
        $n = round($n, 2);
        return $n != 0.0 ? $n : null;
    }

    // =====================================================================================
    //  PROFIT & LOSS - horizontal
    // =====================================================================================

    public function load_profit_loss_horizontal($view, $from_date, $to_date, $nil_type, $consolidated)
    {
        $view = (int)$view;
        $nil  = (int)$nil_type;
        $cons = (int)$consolidated;

        $eng = $this->engine();
        $s   = $eng->snapshot((string)$from_date, (string)$to_date, (bool)$cons);
        [$opening, $closing] = $this->stockFigures($s->from, $s->to, $cons, false);

        return $this->plHorizontalRows($view, $nil, $s, $opening, $closing);
    }

    /** P&L category labels used by the Schedules / Detailed views (unchanged wording). */
    private function plLabels(): array
    {
        return [11 => 'Purchase', 7 => 'Direct Expenses', 13 => 'Indirect Expenses',
                8 => 'Sales', 10 => 'Direct Income', 12 => 'Indirect Income'];
    }

    private function plHorizontalRows(int $view, int $nil, LedgerSnapshot $s, float $opening, float $closing): array
    {
        $eng    = $this->engine();
        $slotOf = fn(array $a) => $eng->plSlot($a);
        $labels = $this->plLabels();
        $dbName = $this->parentNames();

        // ---- left / right cell builders (formatted text + the number it shows) ------------
        $mkL = fn(int $id, string $name, string $type, ?float $bal = null, ?float $det = null, string $style = '') => [
            'l_group_id' => $id, 'l_group_name' => $name,
            'l_balance' => $bal === null ? '' : $this->cellText($bal), 'l_balance_total' => $bal ?? 0,
            'l_detail'  => $det === null ? '' : $this->cellText($det),
            'l_type' => $type, 'l_style' => $style,
            'l_amt' => $bal === null ? null : $this->cellNum($bal), 'l_det' => $det === null ? null : $this->cellNum($det),
        ];
        $mkR = fn(int $id, string $name, string $type, ?float $bal = null, ?float $det = null, string $style = '') => [
            'r_group_id' => $id, 'r_group_name' => $name,
            'r_balance' => $bal === null ? '' : $this->cellText($bal), 'r_balance_total' => $bal ?? 0,
            'r_detail'  => $det === null ? '' : $this->cellText($det),
            'r_type' => $type, 'r_style' => $style,
            'r_amt' => $bal === null ? null : $this->cellNum($bal), 'r_det' => $det === null ? null : $this->cellNum($det),
        ];

        $left = []; $right = [];

        // ---- one P&L category -> rows -----------------------------------------------------
        $block = function (int $cat, string $side) use ($view, $nil, $s, $eng, $slotOf, $labels, $dbName, $mkL, $mkR, &$left, &$right) {
            $items = $eng->categoryItems($s, $cat, 'mv', $slotOf);
            $sign  = $side === 'L' ? 1.0 : -1.0;
            $out   = [];
            $mk    = $side === 'L' ? $mkL : $mkR;

            if ($view === 0) {                                 // Condensed: one row per category
                $name = $dbName[$cat] ?? $labels[$cat];
                $out[] = $mk($cat, $name, 'prt', round($sign * $items['total'], 2), null, 'font-weight:bold;');
            } else {
                $out[] = $mk($cat, $labels[$cat], 'grp', null, null, 'font-weight:bold;');
                foreach ($items['groups'] as $g) {
                    $amt = round($sign * $g['total'], 2);
                    if (!($nil === 0 && $amt == 0.0)) {
                        $out[] = $mk($g['id'], '&nbsp;&nbsp;» ' . $g['name'], 'grp', $amt, null, $view === 2 ? 'font-weight:bold;' : '');
                    }
                    if ($view === 2) {                          // Detailed: list the ledgers below the group
                        foreach ($this->sortedAccounts($s, $g['accounts']) as $a) {
                            $aAmt = round($sign * (float)$a['mv'], 2);
                            if ($nil === 0 && $aAmt == 0.0) { continue; }
                            $out[] = $mk((int)$a['id'], '&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $a['name'], 'acc', null, $aAmt);
                        }
                    }
                }
                foreach ($items['accounts'] as $a) {
                    $amt = round($sign * $a['total'], 2);
                    if ($nil === 0 && $amt == 0.0) { continue; }
                    $tag = $a['kind'] === 'ungrouped' ? '(Ungrouped)' : '(Primary Account)';
                    $out[] = $mk((int)$a['id'], '&nbsp;&nbsp;» ' . $a['name'] . ' <sub><em>' . $tag . '</em></sub>', 'acc', $amt, null);
                }
            }
            if ($side === 'L') { foreach ($out as $r) { $left[] = $r; } }
            else               { foreach ($out as $r) { $right[] = $r; } }
        };

        // ---- stage 1 : trading account -----------------------------------------------------
        $left[]  = $mkL(0, 'Opening Stock', 'opn', $opening, null, 'font-weight:bold;');
        if ($view === 0) { $left[0]['l_parent_id'] = 0; }
        $block(11, 'L'); $block(7, 'L'); $block(8, 'R'); $block(10, 'R');
        $right[] = $mkR(0, 'Closing Stock', 'clo', $closing, null, 'font-weight:bold;');
        if ($view === 0) { $right[count($right) - 1]['r_parent_id'] = 0; }
        if ($view === 0) { $this->plTagParents($left, $right); }

        $final = $this->plMerge($view, $left, $right);

        $l1 = round(array_sum(array_column($left, 'l_balance_total')), 2);
        $r1 = round(array_sum(array_column($right, 'r_balance_total')), 2);
        $gp = $r1 > $l1 ? round($r1 - $l1, 2) : 0.0;
        $gl = $l1 > $r1 ? round($l1 - $r1, 2) : 0.0;

        $final[] = $this->plTotalRowFix($mkL(0, 'Gross Profit C/F', '', $gp) + $mkR(0, 'Gross Loss C/F', '', $gl));
        if ($view === 0) {
            $tot = max($l1, $r1);
            $final[] = $this->plTotalRowFix($mkL(0, '', '', $tot) + $mkR(0, '', '', $tot)) + ['step' => 0, 'pq_rowattr' => ['style' => 'background:#E6E6FA;font-weight:bold;']];
        }

        // ---- stage 2 : profit & loss account -----------------------------------------------
        $left = []; $right = [];
        $left[]  = $mkL(0, 'Gross Loss B/D', '', $gl);
        $right[] = $mkR(0, 'Gross Profit B/D', '', $gp);
        $block(13, 'L'); $block(12, 'R');
        if ($view === 0) { $this->plTagParents($left, $right); }

        foreach ($this->plMerge($view, $left, $right) as $r) { $final[] = $r; }

        $l2 = round(array_sum(array_column($left, 'l_balance_total')), 2);
        $r2 = round(array_sum(array_column($right, 'r_balance_total')), 2);
        $np = $r2 > $l2 ? round($r2 - $l2, 2) : 0.0;
        $nl = $l2 > $r2 ? round($l2 - $r2, 2) : 0.0;
        $last = $mkL(0, 'Net Profit C/D', '', $np) + $mkR(0, 'Net Loss C/D', '', $nl);
        $final[] = $this->plTotalRowFix($last);

        return $final;
    }

    /** The "C/F" / "C/D" rows carry both sides in one array: drop the helper 'style' keys the way the old rows looked. */
    private function plTotalRowFix(array $row): array
    {
        unset($row['l_style'], $row['r_style'], $row['l_type'], $row['r_type']);
        return $row;
    }

    /** Condensed view: mark the category rows with their parent id (drill-down attribute the grid expects). */
    private function plTagParents(array &$left, array &$right): void
    {
        foreach ($left as &$r)  { if (($r['l_type'] ?? '') === 'prt') { $r['l_parent_id'] = $r['l_group_id']; } }
        unset($r);
        foreach ($right as &$r) { if (($r['r_type'] ?? '') === 'prt') { $r['r_parent_id'] = $r['r_group_id']; } }
        unset($r);
    }

    /** Pair the left and right lists row by row, attaching the grid attributes the P&L views expect. */
    private function plMerge(int $view, array $left, array $right): array
    {
        $final = [];
        $count = max(count($left), count($right));
        for ($i = 0; $i < $count; $i++) {
            $l = $left[$i] ?? []; $r = $right[$i] ?? [];
            if (!$l && !$r) { continue; }
            $row = array_merge($l, $r);
            $lStyle = isset($row['l_style']) ? ['style' => $row['l_style']] : [];
            $rStyle = isset($row['r_style']) ? ['style' => $row['r_style']] : [];
            if ($view === 0) {
                $row['pq_cellattr'] = [
                    'r_balance' => ['data-group_name' => $row['r_group_name'] ?? '', 'data-group_id' => $row['r_group_id'] ?? 0,
                                    'data-dataIndx' => 'r_group_name', 'data-id' => $row['r_group_id'] ?? 0, 'data-type' => $row['r_type'] ?? ''],
                    'l_balance' => ['data-group_name' => $row['l_group_name'] ?? '', 'data-group_id' => $row['l_group_id'] ?? 0,
                                    'data-dataIndx' => 'l_group_name', 'data-id' => $row['l_group_id'] ?? 0, 'data-type' => $row['l_type'] ?? ''],
                    'l_group_name' => $lStyle, 'r_group_name' => $rStyle,
                ];
                $row['pq_cellcls'] = ['l_balance' => 'hover-cell', 'r_balance' => 'hover-cell'];
            } else {
                if ($lStyle || $rStyle) {
                    $row['pq_cellattr']['l_group_name'] = $lStyle;
                    $row['pq_cellattr']['r_group_name'] = $rStyle;
                }
                $row['pq_cellcls'] = ['l_balance' => 'hover-cell', 'r_balance' => 'hover-cell',
                                      'l_detail' => 'hover-cell', 'r_detail' => 'hover-cell'];
            }
            $final[] = $row;
        }
        return $final;
    }

    /** Ledger rows of a group ordered by name (natural, case-insensitive). */
    private function sortedAccounts(LedgerSnapshot $s, array $ids): array
    {
        $rows = [];
        foreach ($ids as $id) { $rows[$id] = $s->accounts[$id]; }
        uasort($rows, static fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
        return array_values($rows);
    }

    // =====================================================================================
    //  PROFIT & LOSS - vertical
    // =====================================================================================

    public function load_profit_loss_vertical($view, $from_date, $to_date, $nil_type, $consolidated)
    {
        $view = (int)$view;
        $nil  = (int)$nil_type;
        $cons = (int)$consolidated;

        $eng    = $this->engine();
        $s      = $eng->snapshot((string)$from_date, (string)$to_date, (bool)$cons);
        [$opening, $closing] = $this->stockFigures($s->from, $s->to, $cons, false);
        $slotOf = fn(array $a) => $eng->plSlot($a);
        $labels = $this->plLabels();
        $dbName = $this->parentNames();

        $hdr = fn(string $title) => [
            'group_id' => 0, 'group_name' => $title, 'type' => 'hdr', 'balance' => '', 'amt' => null,
            'pq_rowattr' => ['style' => 'background:#E6E6FA;font-weight:bold;'], 'pq_cellattr' => [], 'pq_cellcls' => [],
        ];
        $mkRow = function (string $name, float $amount, string $type, int $id, string $style, bool $always) {
            $amount = round($amount, 2);
            $text   = ($always || $amount != 0.0) ? formatAmount($amount) : '';
            return [
                'group_id' => $id, 'group_name' => $name, 'type' => $type, 'balance' => $text,
                'amt' => $text === '' ? null : $amount,
                'pq_rowattr'  => $style ? ['style' => $style] : [],
                'pq_cellattr' => ['balance' => ['data-group_name' => $name, 'data-group_id' => $id, 'data-dataIndx' => 'group_name',
                                                'data-id' => $id, 'data-type' => $type]],
                'pq_cellcls'  => ['balance' => 'hover-cell'],
            ];
        };

        // items: name, amt, id, type, style
        $left1 = []; $right1 = []; $left2 = []; $right2 = [];

        $cat = function (int $c, string $side) use ($view, $nil, $s, $eng, $slotOf, $labels, $dbName, &$left1, &$right1, &$left2, &$right2) {
            $items = $eng->categoryItems($s, $c, 'mv', $slotOf);
            $sign  = $side === 'L' ? 1.0 : -1.0;
            $stage1 = in_array($c, [11, 7, 8, 10], true);
            $list  = [];
            if ($view === 0) {
                $list[] = ['name' => $dbName[$c] ?? $labels[$c], 'amt' => round($sign * $items['total'], 2), 'id' => $c, 'type' => 'prt', 'style' => ''];
            } else {
                $list[] = ['name' => $labels[$c], 'amt' => 0.0, 'id' => 0, 'type' => 'prt_hd', 'style' => 'font-weight:bold;'];
                foreach ($items['groups'] as $g) {
                    $amt = round($sign * $g['total'], 2);
                    $show = $view === 1 ? $amt : 0.0;
                    if (!($view === 1 && $nil === 0 && $show == 0.0)) {
                        $list[] = ['name' => '» ' . $g['name'], 'amt' => $show, 'id' => $g['id'], 'type' => 'grp', 'style' => ''];
                    }
                    if ($view === 2) {
                        foreach ($this->sortedAccounts($s, $g['accounts']) as $a) {
                            $aAmt = round($sign * (float)$a['mv'], 2);
                            if ($nil === 0 && $aAmt == 0.0) { continue; }
                            $list[] = ['name' => '»» ' . $a['name'], 'amt' => $aAmt, 'id' => (int)$a['id'], 'type' => 'acc', 'style' => ''];
                        }
                    }
                }
                foreach ($items['accounts'] as $a) {
                    $amt = round($sign * $a['total'], 2);
                    if ($nil === 0 && $amt == 0.0) { continue; }
                    $tag = $a['kind'] === 'ungrouped' ? '(Ungrouped)' : '(Primary Account)';
                    $list[] = ['name' => '» ' . $a['name'] . ' <sub><em>' . $tag . '</em></sub>', 'amt' => $amt, 'id' => (int)$a['id'], 'type' => 'acc', 'style' => ''];
                }
            }
            foreach ($list as $it) {
                if ($stage1) { if ($side === 'L') { $left1[] = $it; } else { $right1[] = $it; } }
                else         { if ($side === 'L') { $left2[] = $it; } else { $right2[] = $it; } }
            }
        };

        $left1[] = ['name' => 'Opening Stock', 'amt' => $opening, 'id' => 0, 'type' => 'opn', 'style' => 'font-weight:bold;'];
        $cat(11, 'L'); $cat(7, 'L'); $cat(8, 'R'); $cat(10, 'R');
        $right1[] = ['name' => 'Closing Stock', 'amt' => $closing, 'id' => 0, 'type' => 'clo', 'style' => 'font-weight:bold;'];

        $l1 = round(array_sum(array_column($left1, 'amt')), 2);
        $r1 = round(array_sum(array_column($right1, 'amt')), 2);
        $gl = $l1 > $r1 ? round($l1 - $r1, 2) : 0.0;
        $gp = $r1 > $l1 ? round($r1 - $l1, 2) : 0.0;
        if ($gl > 0) { array_unshift($left2,  ['name' => 'Gross Loss B/D',   'amt' => $gl, 'id' => 0, 'type' => 'gr_bd', 'style' => '']); }
        if ($gp > 0) { array_unshift($right2, ['name' => 'Gross Profit B/D', 'amt' => $gp, 'id' => 0, 'type' => 'gp_bd', 'style' => '']); }

        $cat(13, 'L'); $cat(12, 'R');

        $l2 = round(array_sum(array_column($left2, 'amt')), 2);
        $r2 = round(array_sum(array_column($right2, 'amt')), 2);
        $np = $r2 > $l2 ? round($r2 - $l2, 2) : 0.0;
        $nl = $l2 > $r2 ? round($l2 - $r2, 2) : 0.0;

        // Condensed always prints the amounts (even 0.00); Schedules/Detailed blank the zeros.
        $always = ($view === 0);
        $final = [];
        $final[] = $hdr('CREDITS');
        foreach ($right1 as $r) { $final[] = $mkRow($r['name'], $r['amt'], $r['type'], $r['id'], $r['style'], $always); }
        $final[] = $mkRow('Gross Loss C/F', $gl, 'gr_cf', 0, '', $always);
        $final[] = $hdr('DEBITS');
        foreach ($left1 as $l) { $final[] = $mkRow($l['name'], $l['amt'], $l['type'], $l['id'], $l['style'], $always); }
        $final[] = $mkRow('Gross Profit C/F', $gp, 'gp_cf', 0, '', $always);
        $final[] = $hdr('CREDITS');
        foreach ($right2 as $r) { $final[] = $mkRow($r['name'], $r['amt'], $r['type'], $r['id'], $r['style'], $always); }
        $final[] = $mkRow('Net Loss C/D', $nl, 'nl_cd', 0, '', $always);
        $final[] = $hdr('DEBITS');
        foreach ($left2 as $l) { $final[] = $mkRow($l['name'], $l['amt'], $l['type'], $l['id'], $l['style'], $always); }
        $final[] = $mkRow('Net Profit C/D', $np, 'np_cd', 0, 'background:#e9ffe9;', $always);
        return $final;
    }

    // =====================================================================================
    //  BALANCE SHEET (shared figures)
    // =====================================================================================

    /**
     * Everything the two Balance Sheet layouts need besides the ledger snapshot.
     * The Balance Sheet is a statement "as on `to`": ledger balances are FY opening + all movement up
     * to `to`, and the current-year result is measured from the FY start (independent of `from`).
     * The result comes from the SAME engine call the P&L uses (no second P&L implementation).
     */
    private function bsFigures(LedgerSnapshot $s, int $cons): array
    {
        $eng = $this->engine();
        [$opStock, $clStock] = $this->stockFigures($s->from, $s->to, $cons, true);
        $pl = $eng->profitLoss($s, $opStock, $clStock, 'cum');
        return [
            'inventory'    => $clStock,
            'opening_stock' => $opStock,
            'pl_current'   => $pl['net'],                                  // profit +, loss - (FY start .. to)
            'pl_bf'        => $eng->profitBroughtForward($s),              // P&L ledgers' own opening balances (credit +)
            // debit-heavy (+) / credit-heavy (-) opening position of ALL ledgers plus opening stock
            'opening_diff' => round($eng->openingTotal($s) + $opStock, 2),
            'unclassified' => $eng->unclassified($s),
        ];
    }

    private function bsCategoryNames(): array
    {
        return [1 => "Owner's Fund", 2 => 'Non Current Liabilities', 4 => 'Current Liabilities',
                3 => 'Non Current Assets', 5 => 'Current Assets'];
    }

    // =====================================================================================
    //  BALANCE SHEET - horizontal
    // =====================================================================================

    public function load_balance_sheet_horizontal($view, $from_date, $to_date, $nil_type, $consolidated)
    {
        $view = (int)$view; $nil = (int)$nil_type; $cons = (int)$consolidated;
        $eng  = $this->engine();
        $s    = $eng->snapshot((string)$from_date, (string)$to_date, (bool)$cons);
        $fig  = $this->bsFigures($s, $cons);
        $names = $this->bsCategoryNames();

        $mkL = fn(int $id, string $name, string $type, ?float $bal = null, ?float $det = null, string $style = '') => [
            'l_group_id' => $id, 'l_group_name' => $name, 'l_detail' => $det === null ? '' : $this->cellText($det),
            'l_balance' => $bal === null ? '' : $this->cellText($bal), 'l_balance_total' => $bal ?? 0,
            'l_type' => $type, 'l_style' => $style,
            'l_amt' => $bal === null ? null : $this->cellNum($bal), 'l_det' => $det === null ? null : $this->cellNum($det),
        ];
        $mkR = fn(int $id, string $name, string $type, ?float $bal = null, ?float $det = null, string $style = '') => [
            'r_group_id' => $id, 'r_group_name' => $name, 'r_detail' => $det === null ? '' : $this->cellText($det),
            'r_balance' => $bal === null ? '' : $this->cellText($bal), 'r_balance_total' => $bal ?? 0,
            'r_type' => $type, 'r_style' => $style,
            'r_amt' => $bal === null ? null : $this->cellNum($bal), 'r_det' => $det === null ? null : $this->cellNum($det),
        ];

        $left = []; $right = [];
        $sumCat = fn(int $cat) => $eng->categoryItems($s, $cat, 'closing')['total'];

        // Unclassified ledgers: a credit balance is a liability-side line, a debit balance an asset-side line.
        $uncL = []; $uncR = [];
        foreach ($fig['unclassified'] as $a) {
            if ($a['closing'] < 0) { $uncL[] = $a; } else { $uncR[] = $a; }
        }
        $uncSum = function (array $list): float { $t = 0.0; foreach ($list as $a) { $t += (float)$a['closing']; } return round($t, 2); };

        if ($view === 0) {
            // ------------------------------------------------------------------ condensed
            $own = -$sumCat(1); $ncl = -$sumCat(2); $cl = -$sumCat(4);
            $nca = $sumCat(3);  $ca  = round($sumCat(5) + $fig['inventory'], 2);
            $left[]  = $mkL(1, $names[1], 'prt', round($own, 2));
            $left[]  = $mkL(0, 'Profit / Loss', 'pl', $fig['pl_current'], null, 'font-weight:bold;');
            if ($fig['pl_bf'] != 0.0) { $left[] = $mkL(0, 'Profit & Loss b/f (opening)', 'pl', $fig['pl_bf'], null, 'font-weight:bold;'); }
            $left[]  = $mkL(2, $names[2], 'prt', round($ncl, 2));
            $left[]  = $mkL(4, $names[4], 'prt', round($cl, 2));
            $right[] = $mkR(3, $names[3], 'prt', round($nca, 2));
            $right[] = $mkR(5, $names[5], 'prt', $ca);
            if ($uncL) { $left[]  = $mkL(0, 'Unclassified (credit balances)', 'unc', round(-$uncSum($uncL), 2), null, 'font-weight:bold;'); }
            if ($uncR) { $right[] = $mkR(0, 'Unclassified (debit balances)',  'unc', $uncSum($uncR), null, 'font-weight:bold;'); }
        } else {
            // ------------------------------------------------------------------ schedules / detailed
            $emit = function (int $cat, string $side) use ($view, $s, $eng, $names, $mkL, $mkR, $fig, &$left, &$right) {
                $items = $eng->categoryItems($s, $cat, 'closing');
                $sign  = $side === 'L' ? -1.0 : 1.0;
                $mk    = $side === 'L' ? $mkL : $mkR;
                $out   = [$mk($cat, $names[$cat], 'prt', null, null, 'font-weight:bold;')];
                foreach ($items['groups'] as $g) {
                    $out[] = $mk($g['id'], '&nbsp;&nbsp;» ' . $g['name'], 'grp', round($sign * $g['total'], 2), null, $view === 2 ? 'font-weight:bold;' : '');
                    if ($view === 2) {
                        foreach ($this->sortedAccounts($s, $g['accounts']) as $a) {
                            $out[] = $mk((int)$a['id'], '&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $a['name'], 'acc', null, round($sign * (float)$a['closing'], 2));
                        }
                    }
                }
                foreach ($items['accounts'] as $a) {
                    $tag = $a['kind'] === 'ungrouped' ? '(Ungrouped)' : ($a['is_bsd'] ? '(Bill Sundry)' : '(Primary Account)');
                    $out[] = $mk((int)$a['id'], '&nbsp;&nbsp;» ' . $a['name'] . ' <sub><em>' . $tag . '</em></sub>', 'acc', round($sign * $a['total'], 2));
                }
                if ($side === 'R' && $cat === 5) {
                    $out[] = ['r_group_id' => 0, 'r_group_name' => '&nbsp;&nbsp;» Inventories', 'r_balance' => $this->cellText($fig['inventory']),
                              'r_balance_total' => $fig['inventory'], 'r_detail' => '', 'r_type' => 'inv', 'r_amt' => $this->cellNum($fig['inventory']), 'r_det' => null];
                }
                foreach ($out as $r) { if ($side === 'L') { $left[] = $r; } else { $right[] = $r; } }
            };
            foreach ([1, 2, 4] as $cat) {
                $emit($cat, 'L');
                if ($cat === 1) {
                    $left[] = $mkL(0, 'Profit / Loss', 'pl', $fig['pl_current'], null, 'font-weight:bold;');
                    if ($fig['pl_bf'] != 0.0) { $left[] = $mkL(0, 'Profit & Loss b/f (opening)', 'pl', $fig['pl_bf'], null, 'font-weight:bold;'); }
                }
            }
            foreach ([3, 5] as $cat) { $emit($cat, 'R'); }

            $addUnc = function (array $list, string $side, string $title) use ($mkL, $mkR, $view, &$left, &$right) {
                if (!$list) { return; }
                $mk = $side === 'L' ? $mkL : $mkR; $sign = $side === 'L' ? -1.0 : 1.0; $t = 0.0;
                foreach ($list as $a) { $t += (float)$a['closing']; }
                $out = [$mk(0, $title, 'prt', null, null, 'font-weight:bold;')];
                $out[] = $mk(0, '&nbsp;&nbsp;» Total unclassified', 'grp', round($sign * $t, 2), null, $view === 2 ? 'font-weight:bold;' : '');
                foreach ($list as $a) {
                    $why = $a['missing_master'] ? '(no account master)' : (!$a['mapped'] ? '(not mapped to a group)' : '(group not in Balance Sheet / P&L)');
                    $out[] = $mk((int)$a['id'], '&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $a['name'] . ' <sub><em>' . $why . '</em></sub>', 'acc', null, round($sign * (float)$a['closing'], 2));
                }
                foreach ($out as $r) { if ($side === 'L') { $left[] = $r; } else { $right[] = $r; } }
            };
            $addUnc($uncL, 'L', 'Unclassified (credit balances)');
            $addUnc($uncR, 'R', 'Unclassified (debit balances)');
        }

        // ---- totals (of everything that is a Balance Sheet line; detail rows carry 0 in *_balance_total) ----
        $lTotal = round(array_sum(array_column($left, 'l_balance_total')), 2);
        $rTotal = round(array_sum(array_column($right, 'r_balance_total')), 2);

        // ---- pair rows, apply the nil filter exactly as before ----
        $final = [];
        $count = max(count($left), count($right));
        for ($i = 0; $i < $count; $i++) {
            $l = $left[$i] ?? []; $r = $right[$i] ?? [];
            if (!$l && !$r) { continue; }
            if ($nil === 0) {
                $lt = $l['l_balance_total'] ?? 0.0; $ld = $l['l_detail'] ?? '';
                $rt = $r['r_balance_total'] ?? 0.0; $rd = $r['r_detail'] ?? '';
                $isLZero = ($lt == 0.0 && ($ld === '' || $ld == '0.00'));
                $isRZero = ($rt == 0.0 && ($rd === '' || $rd == '0.00'));
                $isLHdr = ($l['l_type'] ?? '') === 'prt'; $isRHdr = ($r['r_type'] ?? '') === 'prt';
                if ($isLHdr && $isRZero) { continue; }
                if ($isRHdr && $isLZero) { continue; }
                if (!$isLHdr && !$isRHdr && $isLZero && $isRZero) { continue; }
            }
            $row = array_merge($l, $r);
            if (isset($row['l_style'])) { $row['pq_cellattr']['l_group_name'] = ['style' => $row['l_style']]; }
            if (isset($row['r_style'])) { $row['pq_cellattr']['r_group_name'] = ['style' => $row['r_style']]; }
            $row['pq_cellcls'] = ['l_balance' => 'hover-cell', 'r_balance' => 'hover-cell', 'l_detail' => 'hover-cell', 'r_detail' => 'hover-cell'];
            if (isset($row['l_group_id'])) {
                $row['pq_cellattr']['l_balance'] = ['data-group_id' => $row['l_group_id'], 'data-group_name' => $row['l_group_name'] ?? '',
                                                    'data-dataIndx' => 'l_group_name', 'data-type' => $row['l_type'] ?? ''];
            }
            if (isset($row['r_group_id'])) {
                $row['pq_cellattr']['r_balance'] = ['data-group_id' => $row['r_group_id'], 'data-group_name' => $row['r_group_name'] ?? '',
                                                    'data-dataIndx' => 'r_group_name', 'data-type' => $row['r_type'] ?? ''];
            }
            $final[] = $row;
        }

        // ---- Difference in Opening: the real opening imbalance of the ledger (+ opening stock), shown, not hidden ----
        $od = $fig['opening_diff']; $dl = 0.0; $dr = 0.0;
        if (abs($od) > 0.01) {
            if ($od > 0) { $dl = abs($od); } else { $dr = abs($od); }
            $row = ['l_group_id' => 0, 'l_group_name' => '', 'l_balance' => '', 'l_balance_total' => 0, 'l_amt' => null,
                    'r_group_id' => 0, 'r_group_name' => '', 'r_balance' => '', 'r_balance_total' => 0, 'r_amt' => null,
                    'l_type' => 'bal', 'r_type' => 'bal'];
            if ($dl > 0) {
                $row['l_group_name'] = 'Difference in Opening'; $row['l_balance'] = $this->cellText($dl);
                $row['l_balance_total'] = $dl; $row['l_amt'] = $this->cellNum($dl);
                $row['pq_cellattr']['l_group_name'] = ['style' => 'font-weight:bold;'];
            } else {
                $row['r_group_name'] = 'Difference in Opening'; $row['r_balance'] = $this->cellText($dr);
                $row['r_balance_total'] = $dr; $row['r_amt'] = $this->cellNum($dr);
                $row['pq_cellattr']['r_group_name'] = ['style' => 'font-weight:bold;'];
            }
            $final[] = $row;
        }

        $lTotal = round($lTotal + $dl, 2);
        $rTotal = round($rTotal + $dr, 2);
        $final[] = [
            'l_group_id' => 0, 'l_group_name' => '', 'l_balance' => $this->cellText($lTotal), 'l_balance_total' => $lTotal, 'l_amt' => $this->cellNum($lTotal),
            'r_group_id' => 0, 'r_group_name' => '', 'r_balance' => $this->cellText($rTotal), 'r_balance_total' => $rTotal, 'r_amt' => $this->cellNum($rTotal),
            'pq_rowattr' => ['style' => 'background:#E6E6FA;font-weight:bold;'],
        ];
        return $final;
    }

    // =====================================================================================
    //  BALANCE SHEET - vertical
    // =====================================================================================

    public function load_balance_sheet_vertical($view, $from_date, $to_date, $nil_type, $consolidated)
    {
        $view = (int)$view; $nil = (int)$nil_type; $cons = (int)$consolidated;
        $eng  = $this->engine();
        $s    = $eng->snapshot((string)$from_date, (string)$to_date, (bool)$cons);
        $fig  = $this->bsFigures($s, $cons);
        $names = $this->bsCategoryNames();

        $hdr = fn(string $title) => [
            'group_id' => 0, 'group_name' => $title, 'type' => 'hdr', 'balance' => '', 'balance_total' => 0, 'amt' => null,
            'pq_rowattr' => ['style' => 'background:#E6E6FA;font-weight:bold;'], 'pq_cellattr' => [], 'pq_cellcls' => [],
        ];
        $mk = function (string $name, float $amount, string $type = 'row', int $id = 0, string $style = '', bool $addToTotal = true) {
            $amount = round($amount, 2);
            return [
                'group_id' => $id, 'group_name' => $name, 'type' => $type,
                'balance' => $this->cellText($amount), 'balance_total' => $addToTotal ? $amount : 0.0, 'amt' => $this->cellNum($amount),
                'pq_rowattr' => $style ? ['style' => $style] : [],
                'pq_cellattr' => ['balance' => ['data-group_name' => $name, 'data-group_id' => $id, 'data-dataIndx' => 'group_name',
                                                'data-id' => $id, 'data-type' => $type]],
                'pq_cellcls' => ['balance' => 'hover-cell'],
            ];
        };
        $ttl = fn(string $title, float $total) => [
            'group_id' => 0, 'group_name' => $title, 'type' => 'ttl', 'balance' => $this->cellText($total), 'balance_total' => 0, 'amt' => $this->cellNum($total),
            'pq_rowattr' => ['style' => 'background:#E6E6FA;font-weight:bold;'],
        ];

        $uncL = []; $uncR = [];
        foreach ($fig['unclassified'] as $a) { if ($a['closing'] < 0) { $uncL[] = $a; } else { $uncR[] = $a; } }
        $uncSum = function (array $list): float { $t = 0.0; foreach ($list as $a) { $t += (float)$a['closing']; } return round($t, 2); };

        $od = $fig['opening_diff'];
        $diffRow = fn() => $mk('Difference in Opening', abs($od), 'opn', 0, 'font-weight:bold;', true);

        // ---------------------------------------------------------------- condensed
        if ($view === 0) {
            $sumCat = fn(int $cat) => $eng->categoryItems($s, $cat, 'closing')['total'];
            $rows = [$hdr('LIABILITIES')];
            $liab = [
                ["Owner's Fund", -$sumCat(1), 'cat', 1], ['Profit / Loss', $fig['pl_current'], 'pl', 0],
            ];
            if ($fig['pl_bf'] != 0.0) { $liab[] = ['Profit & Loss b/f (opening)', $fig['pl_bf'], 'pl', 0]; }
            $liab[] = ['Non Current Liabilities', -$sumCat(2), 'cat', 2];
            $liab[] = ['Current Liabilities', -$sumCat(4), 'cat', 4];
            if ($uncL) { $liab[] = ['Unclassified (credit balances)', -$uncSum($uncL), 'unc', 0]; }
            $liabTotal = 0.0;
            foreach ($liab as [$n, $a, $t, $id]) {
                $a = round($a, 2); $liabTotal += $a;
                if (!($nil === 0 && $a == 0.0)) { $rows[] = $mk($n, $a, $t, $id, 'font-weight:bold;', false); }
            }
            if ($od > 0.01)  { $rows[] = $diffRow(); $liabTotal += abs($od); }
            $rows[] = $ttl('TOTAL LIABILITIES', round($liabTotal, 2));

            $rows[] = $hdr('ASSETS');
            $assets = [
                ['Non Current Assets', $sumCat(3), 'cat', 3],
                ['Current Assets', round($sumCat(5) + $fig['inventory'], 2), 'cat', 5],
            ];
            if ($uncR) { $assets[] = ['Unclassified (debit balances)', $uncSum($uncR), 'unc', 0]; }
            $assetTotal = 0.0;
            foreach ($assets as [$n, $a, $t, $id]) {
                $a = round($a, 2); $assetTotal += $a;
                if (!($nil === 0 && $a == 0.0)) { $rows[] = $mk($n, $a, $t, $id, 'font-weight:bold;', false); }
            }
            if ($od < -0.01) { $rows[] = $diffRow(); $assetTotal += abs($od); }
            $rows[] = $ttl('TOTAL ASSETS', round($assetTotal, 2));
            return $rows;
        }

        // ---------------------------------------------------------------- schedules / detailed
        $block = function (int $cat, bool $liability) use ($view, $nil, $s, $eng, $names, $mk, $fig) {
            $items = $eng->categoryItems($s, $cat, 'closing');
            $sign  = $liability ? -1.0 : 1.0;
            $rows  = [$mk($names[$cat], 0.0, 'cat', $cat, 'font-weight:bold;', false)];
            foreach ($items['groups'] as $g) {
                $disp = round($sign * $g['total'], 2);
                if ($nil === 0 && $disp == 0.0) { continue; }
                $rows[] = $mk('&nbsp;&nbsp;» ' . $g['name'], $disp, 'grp', $g['id'], $view === 2 ? 'font-weight:bold;' : '', true);
                if ($view === 2) {
                    foreach ($this->sortedAccounts($s, $g['accounts']) as $a) {
                        $aDisp = round($sign * (float)$a['closing'], 2);
                        if ($nil === 0 && $aDisp == 0.0) { continue; }
                        $rows[] = $mk('&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $a['name'], $aDisp, 'acc', (int)$a['id'], '', false);
                    }
                }
            }
            foreach ($items['accounts'] as $a) {
                $disp = round($sign * $a['total'], 2);
                if ($nil === 0 && $disp == 0.0) { continue; }
                $tag = $a['kind'] === 'ungrouped' ? '(Ungrouped)' : ($a['is_bsd'] ? '(Bill Sundry)' : '(Primary Account)');
                $rows[] = $mk('&nbsp;&nbsp;» ' . $a['name'] . ' <sub><em>' . $tag . '</em></sub>', $disp, 'acc', (int)$a['id'], '', true);
            }
            if (!$liability && $cat === 5 && !($nil === 0 && round($fig['inventory'], 2) == 0.0)) {
                $rows[] = $mk('&nbsp;&nbsp;» Inventories', $fig['inventory'], 'inv', 0, '', true);
            }
            return $rows;
        };
        $uncBlock = function (array $list, bool $liability, string $title) use ($view, $mk) {
            if (!$list) { return []; }
            $sign = $liability ? -1.0 : 1.0; $t = 0.0;
            foreach ($list as $a) { $t += (float)$a['closing']; }
            $rows = [$mk($title, 0.0, 'cat', 0, 'font-weight:bold;', false),
                     $mk('&nbsp;&nbsp;» Total unclassified', round($sign * $t, 2), 'grp', 0, $view === 2 ? 'font-weight:bold;' : '', true)];
            foreach ($list as $a) {
                $why = $a['missing_master'] ? '(no account master)' : (!$a['mapped'] ? '(not mapped to a group)' : '(group not in Balance Sheet / P&L)');
                $rows[] = $mk('&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $a['name'] . ' ' . $why, round($sign * (float)$a['closing'], 2), 'acc', (int)$a['id'], '', false);
            }
            return $rows;
        };

        $liabRows = [$hdr('LIABILITIES')];
        $liabRows = array_merge($liabRows, $block(1, true));
        if (!($nil === 0 && $fig['pl_current'] == 0.0)) { $liabRows[] = $mk('Profit / Loss', $fig['pl_current'], 'pl', 0, 'font-weight:bold;', true); }
        if ($fig['pl_bf'] != 0.0) { $liabRows[] = $mk('Profit & Loss b/f (opening)', $fig['pl_bf'], 'pl', 0, 'font-weight:bold;', true); }
        $liabRows = array_merge($liabRows, $block(2, true), $block(4, true), $uncBlock($uncL, true, 'Unclassified (credit balances)'));
        if ($od > 0.01) { $liabRows[] = $diffRow(); }
        $liabTotal = round(array_sum(array_column($liabRows, 'balance_total')), 2);
        $liabRows[] = $ttl('TOTAL LIABILITIES', $liabTotal);

        $assetRows = [$hdr('ASSETS')];
        $assetRows = array_merge($assetRows, $block(3, false), $block(5, false), $uncBlock($uncR, false, 'Unclassified (debit balances)'));
        if ($od < -0.01) { $assetRows[] = $diffRow(); }
        $assetTotal = round(array_sum(array_column($assetRows, 'balance_total')), 2);
        $assetRows[] = $ttl('TOTAL ASSETS', $assetTotal);

        return array_merge($liabRows, $assetRows);
    }

    /**
     * Why a Balance Sheet / Trial Balance does not tally, straight from the ledger (no adjustment):
     * the difference of the two sides, the debit-minus-credit of all ledger rows, and the vouchers responsible.
     * `gst_paid_journals` is the part caused by the composition-scheme "GST PAID A/C" system journal
     * (voucher type 23), which the application posts as a single debit row.
     */
    public function reconciliation($from_date, $to_date, $consolidated = 0, int $voucherLimit = 200): array
    {
        $eng = $this->engine();
        $s   = $eng->snapshot((string)$from_date, (string)$to_date, (bool)(int)$consolidated);
        $imb = $eng->imbalance($s);
        $out = ['ledger_imbalance' => $imb, 'gst_paid_journals' => 0.0, 'other' => $imb, 'vouchers' => []];
        if (abs($imb) > 0.005) {
            $v = $eng->unbalancedVouchers($s, $voucherLimit);
            $gst = 0.0;
            foreach ($v as $row) { if ((int)$row['vch_type_id'] === 23) { $gst += $row['diff']; } }
            $out['vouchers']          = $v;
            $out['gst_paid_journals'] = round($gst, 2);
            $out['other']             = round($imb - $gst, 2);
        }
        return $out;
    }

    // =====================================================================================
    //  TRIAL BALANCE
    // =====================================================================================

    /** One Trial Balance grid row (same keys the grid and the Excel writer always used). */
    private function tbRow(int $id, string $name, string $parent, float $debit, float $credit, string $type, int $status = 1, ?array $attr = null): array
    {
        $debit  = round($debit, 2);
        $credit = round($credit, 2);
        $a      = $attr ?? ['id' => 0, 'name' => ''];
        $cell   = ['data-group_id' => $a['id'], 'data-groupname' => $a['name'], 'data-dataindx' => $a['name'], 'data-id' => $a['id'], 'data-type' => $type];
        return [
            'group_id' => $id, 'group_name' => $name, 'parent' => $parent,
            'credit' => $credit != 0.0 ? formatAmount($credit) : '', 'debit' => $debit != 0.0 ? formatAmount($debit) : '',
            'credit_total' => $credit, 'debit_total' => $debit,
            'transaction_status' => $status, 'type' => $type,
            'pq_cellcls'  => ['credit' => 'hover-cell', 'debit' => 'hover-cell'],
            'pq_cellattr' => ['credit' => $cell, 'debit' => $cell],
        ];
    }

    /** Debit / credit split of a signed balance. */
    private function drCr(float $signed): array
    {
        return $signed < 0 ? [0.0, abs($signed)] : [$signed, 0.0];
    }

    /** Stock rows, "Difference in Opening" row: shared by the Groups and Accounts views. */
    private function tbStockRows(float $opStock, float $clStock, bool $alwaysShow): array
    {
        $rows = [];
        if ($alwaysShow || $clStock != 0.0) {
            $rows[] = $this->tbRow(0, 'Closing Inventory', 'Current Assets', $clStock, 0.0, 'cls');
        }
        $diff = round($clStock - $opStock, 2);
        if ($alwaysShow || $diff != 0.0) {
            $rows[] = $this->tbRow(0, 'Inventory Difference', 'Profit & Loss A/C', $diff < 0 ? abs($diff) : 0.0, $diff > 0 ? $diff : 0.0, 'dfs');
        }
        return $rows;
    }

    private function tbOpeningDifferenceRow(float $openingDiff): ?array
    {
        if (abs($openingDiff) <= 0.01) { return null; }
        // debit-heavy opening position is balanced on the CREDIT side and vice-versa
        return $this->tbRow(0, 'Difference in Opening', '', $openingDiff < 0 ? abs($openingDiff) : 0.0, $openingDiff > 0 ? $openingDiff : 0.0, 'opn', 0);
    }

    /** Groups view (view = 0). */
    public function load_trial_balance_grps($from_date, $to_date, $consolidated = 0)
    {
        $cons = (int)$consolidated;
        $eng  = $this->engine();
        $s    = $eng->snapshot((string)$from_date, (string)$to_date, (bool)$cons);
        [$opStock, $clStock] = $this->stockFigures($s->from, $s->to, $cons, true);
        $catName = $this->parentNames();

        $final = $this->tbStockRows($opStock, $clStock, false);

        // top groups, ordered by category then name (the old query had no ORDER BY)
        $tops = [];
        foreach ($s->groups as $gid => $g) { if ($g['parent_gid'] === 0) { $tops[] = $g; } }
        usort($tops, static function ($a, $b) {
            return $a['cat'] === $b['cat'] ? strnatcasecmp($a['name'], $b['name']) : $a['cat'] <=> $b['cat'];
        });
        foreach ($tops as $g) {
            $closing = $s->groupSum($g['id'], 'closing');
            if ($closing == 0.0) { continue; }
            [$dr, $cr] = $this->drCr($closing);
            $final[] = $this->tbRow($g['id'], $g['name'], $catName[$g['cat']] ?? '', $dr, $cr, 'grp', 1, ['id' => $g['id'], 'name' => $g['name']]);
        }

        // ledgers that are not inside a top group: primary accounts, then anything the group tree cannot place
        $prim = []; $other = [];
        foreach ($s->accounts as $a) {
            if ($a['closing'] == 0.0) { continue; }
            if ($a['mapped'] && $a['group_id'] === 0)        { $prim[]  = $a; }
            elseif (!$a['mapped'] || !$a['chain_ok'])        { $other[] = $a; }
        }
        foreach ($prim as $a) {
            [$dr, $cr] = $this->drCr((float)$a['closing']);
            $label = $a['name'] . ' <sub><em>(' . ($a['is_bsd'] ? 'Bill Sundry' : 'Primary Account') . ')</em></sub>';
            $final[] = $this->tbRow((int)$a['id'], $label, $catName[$a['cat']] ?? '', $dr, $cr, 'acc', 1, ['id' => (int)$a['id'], 'name' => $label]);
        }
        foreach ($other as $a) {
            [$dr, $cr] = $this->drCr((float)$a['closing']);
            $tag   = !$a['mapped'] ? ($a['missing_master'] ? '(no account master)' : '(Unclassified: not mapped to a group)') : '(Ungrouped: group missing)';
            $label = $a['name'] . ' <sub><em>' . $tag . '</em></sub>';
            $final[] = $this->tbRow((int)$a['id'], $label, $a['mapped'] ? ($catName[$a['cat']] ?? 'Ungrouped') : 'Unclassified', $dr, $cr, 'acc', 1, ['id' => (int)$a['id'], 'name' => $label]);
        }

        $diffRow = $this->tbOpeningDifferenceRow(round($eng->openingTotal($s) + $opStock, 2));
        if ($diffRow) { $final[] = $diffRow; }
        return $final;
    }

    /** Accounts view (view = 1). */
    public function load_trial_balance_accnts($from_date, $to_date, $consolidated, $is_export = 0)
    {
        $cons = (int)$consolidated;
        $eng  = $this->engine();
        $s    = $eng->snapshot((string)$from_date, (string)$to_date, (bool)$cons);
        [$opStock, $clStock] = $this->stockFigures($s->from, $s->to, $cons, true);
        $catName = $this->parentNames();

        $rows = $this->tbStockRows($opStock, $clStock, true);

        foreach ($s->accounts as $a) {                      // already ordered by account name
            if ($a['op'] == 0.0 && $a['pre'] == 0.0 && $a['dr'] == 0.0 && $a['cr'] == 0.0) { continue; }
            if (!$a['mapped']) {
                $parent = 'Unclassified';
            } elseif ($a['group_id'] === 0) {
                $parent = $catName[$a['cat']] ?? '';
            } else {
                $parent = $s->groups[$a['group_id']]['name'] ?? 'Ungrouped';
            }
            $name = $a['name'] . ($a['missing_master'] ? ' <sub><em>(no account master)</em></sub>' : '');
            [$dr, $cr] = $this->drCr((float)$a['closing']);
            $rows[] = $this->tbRow((int)$a['id'], $name, $parent, $dr, $cr, 'dfs');
        }

        $diffRow = $this->tbOpeningDifferenceRow(round($eng->openingTotal($s) + $opStock, 2));
        if ($diffRow) { $rows[] = $diffRow; }
        return $rows;
    }

    // =====================================================================================
    //  DISPATCH - the screen controller and the Excel exporter both go through these, so
    //  they can never call different functions or pass different arguments.
    // =====================================================================================

    public function load_trial_balance_view(int $view, $from_date, $to_date, int $consolidated, int $nil_type): array
    {
        if ($view === 0) { return $this->load_trial_balance_grps($from_date, $to_date, $consolidated); }
        if ($view === 1) { return $this->load_trial_balance_accnts($from_date, $to_date, $consolidated); }
        return $this->load_trial_balance_opn($from_date, $to_date, $consolidated, $nil_type);
    }

    public function load_balance_sheet_view(int $format, int $view, $from_date, $to_date, int $nil_type, int $consolidated): array
    {
        return $format === 2
            ? $this->load_balance_sheet_vertical($view, $from_date, $to_date, $nil_type, $consolidated)
            : $this->load_balance_sheet_horizontal($view, $from_date, $to_date, $nil_type, $consolidated);
    }

    public function load_profit_loss_view(int $format, int $view, $from_date, $to_date, int $nil_type, int $consolidated): array
    {
        return $format === 2
            ? $this->load_profit_loss_vertical($view, $from_date, $to_date, $nil_type, $consolidated)
            : $this->load_profit_loss_horizontal($view, $from_date, $to_date, $nil_type, $consolidated);
    }

    /**
     * Notes printed under a report (banner on the page, rows under the Excel/CSV), taken from the ledger:
     *   warn - the ledger itself is out of balance (real, posted data) and by how much / which vouchers;
     *   info - facts about the stored data that explain lines of the report.
     * Nothing here changes a figure; an empty list means there is nothing to say.
     *
     * @param string $report 'balance_sheet' | 'trial_balance' | 'profit_loss'
     * @return array<int,array{level:string,text:string}>
     */
    public function reportNotes(string $report, $from_date, $to_date, int $consolidated): array
    {
        $eng   = $this->engine();
        $s     = $eng->snapshot((string)$from_date, (string)$to_date, (bool)$consolidated);
        $fmt   = static fn(float $n) => number_format(abs($n), 2, '.', ',') . ($n < 0 ? ' Cr' : ' Dr');
        $label = ['balance_sheet' => 'The Balance Sheet', 'trial_balance' => 'The Trial Balance'][$report] ?? '';
        $notes = [];

        if ($report !== 'profit_loss') {
            $rec = $this->reconciliation($from_date, $to_date, $consolidated);
            $imb = (float)$rec['ledger_imbalance'];
            if (abs($imb) >= 0.005) {
                $notes[] = ['level' => 'warn', 'text' => sprintf(
                    '%s does not tally because the ledger itself is out of balance: total debit minus total credit of all posted entries (FY start to %s) = %s.',
                    $label, date('d-m-Y', strtotime((string)$to_date)), $fmt($imb))];
                if (abs($rec['gst_paid_journals']) >= 0.005) {
                    $notes[] = ['level' => 'warn', 'text' => 'Of this, GST PAID A/C system journals (composition scheme, posted as a single debit row): ' . $fmt($rec['gst_paid_journals']) . '.'];
                }
                if (abs($rec['other']) >= 0.005) {
                    $notes[] = ['level' => 'warn', 'text' => 'Of this, other vouchers whose debit and credit rows differ (e.g. approval pending on one leg only): ' . $fmt($rec['other']) . '.'];
                }
            }
            [$opStock] = $this->stockFigures($s->from, $s->to, $consolidated, true);
            $od = round($eng->openingTotal($s) + $opStock, 2);
            if (abs($od) > 0.01) {
                $notes[] = ['level' => 'info', 'text' => sprintf(
                    "'Difference in Opening': the opening balances stored for this financial year (all ledgers plus opening stock) do not net to zero - they net to %s.", $fmt($od))];
            }
        }

        $n = 0; $sum = 0.0;
        foreach ($s->accounts as $a) {
            if (AccountingEngine::isPlCat((int)$a['cat']) && abs((float)$a['op']) >= 0.005) { $n++; $sum += (float)$a['op']; }
        }
        if ($n > 0) {
            $one  = $n === 1;
            $what = $report === 'profit_loss'
                ? ($one ? "it is not part of this year's result" : "they are not part of this year's result")
                : ($report === 'balance_sheet'
                    ? ($one ? "it is shown as 'Profit & Loss b/f (opening)' and is part of 'Difference in Opening'"
                            : "they are shown as 'Profit & Loss b/f (opening)' and are part of 'Difference in Opening'")
                    : ($one ? "it is listed with that opening balance included and is part of 'Difference in Opening'"
                            : "they are listed with those opening balances included and are part of 'Difference in Opening'"));
            $notes[] = ['level' => 'info', 'text' => sprintf(
                "%s (net %s). Profit & loss accounts start every year at zero and the application does not accept opening balances for them, so %s written by another routine (typically the year-end carry-forward); %s.",
                $n === 1 ? '1 profit & loss ledger carries an opening balance' : $n . ' profit & loss ledgers carry opening balances',
                $fmt(round($sum, 2)), $n === 1 ? 'this was' : 'these were', $what)];
        }
        return $notes;
    }
}
