<?php
namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * AccountingEngine - the ONE place where ledger balances are computed for the
 * Trial Balance, Balance Sheet and Profit & Loss, on screen and in Excel.
 *
 * Why it exists: the reports used to carry their own copies of the same arithmetic
 * (different date bases, different account universes, different treatment of
 * sub-groups, bill-sundry ledgers, unmapped ledgers, opening balances ...). Any
 * difference between two copies shows up as a Balance Sheet that does not tally or
 * as an Excel file that differs from the screen. Everything below is computed once,
 * from the ledger, and the presenters only lay it out.
 *
 * Rules (all of them come from the existing ledger design, nothing is invented):
 *  - Ledger rows: accttxnmst with acc_txn_type = 1, vch_txn_id > 0 (rows with
 *    vch_txn_id = 0 are the opening-balance mirror rows), dated inside the FY,
 *    dated by the ROW date (acc_txn_date), for the session branch unless consolidated.
 *  - Opening balance: accoppybal of the FY (all rows of the ledger, bill-sundry ledgers
 *    included) + the movement from the FY start up to the day before `from`.
 *  - Every ledger that has an opening balance or any movement is in the snapshot,
 *    whether or not it is mapped to a group for this FY. Nothing is dropped silently:
 *    ledgers the group structure cannot place are reported as "unclassified".
 *  - Debit balance = positive, credit balance = negative.
 *
 * No adjustment of any kind is made here: if the ledger itself does not balance the
 * difference is reported (see imbalance()), never hidden or forced.
 */
class AccountingEngine
{
    /** Category ids (grpparentn) as used by the report code. */
    public const BS_LIABILITY_CATS = [1, 2, 4];   // Owner's Fund, Non Current Liabilities, Current Liabilities
    public const BS_ASSET_CATS     = [3, 5];      // Non Current Assets, Current Assets
    public const PL_LEFT_STAGE1    = [11, 7];     // Purchase, Direct Expenses  (debit side, trading)
    public const PL_RIGHT_STAGE1   = [8, 10];     // Sales, Direct Income       (credit side, trading)
    public const PL_LEFT_STAGE2    = [13];        // Indirect Expenses
    public const PL_RIGHT_STAGE2   = [12];        // Indirect Income
    public const PL_CATS           = [11, 7, 8, 10, 13, 12];

    /** @var array<string,LedgerSnapshot> */
    private array $snapshots = [];

    private BaseConnection $db;
    private int $cmp;
    private int $fy;
    private int $bo;
    private string $fyStart;
    private string $fyEnd;

    public function __construct(BaseConnection $db, int $companyId, int $fyId, int $branchId, string $fyStart, string $fyEnd)
    {
        $this->db      = $db;
        $this->cmp     = $companyId;
        $this->fy      = $fyId;
        $this->bo      = $branchId;
        $this->fyStart = $this->ymd($fyStart, date('Y-04-01'));
        $this->fyEnd   = $this->ymd($fyEnd, date('Y-03-31', strtotime('+1 year')));
    }

    public function fyStart(): string { return $this->fyStart; }
    public function fyEnd(): string   { return $this->fyEnd; }

    // ------------------------------------------------------------------------------------
    // Snapshot
    // ------------------------------------------------------------------------------------

    public function snapshot(string $from, string $to, bool $consolidated): LedgerSnapshot
    {
        $from = $this->ymd($from, $this->fyStart);
        $to   = $this->ymd($to, $this->fyEnd);
        if ($from < $this->fyStart) { $from = $this->fyStart; }
        if ($to > $this->fyEnd)     { $to   = $this->fyEnd; }

        $key = $from . '|' . $to . '|' . (int)$consolidated;      // the reports of one request share one read of the ledger
        if (isset($this->snapshots[$key])) {
            return $this->snapshots[$key];
        }
        $s = new LedgerSnapshot($from, $to, $this->fyStart, $this->fyEnd, $consolidated);
        $this->loadGroups($s);
        $this->loadAccounts($s);
        $opening = $this->loadOpenings($s);
        $moves   = $this->loadMovements($s);
        $this->attachBalances($s, $opening, $moves);
        $this->classify($s);
        return $this->snapshots[$key] = $s;
    }

    private function ymd(string $d, string $fallback): string
    {
        $t = strtotime($d);
        return $t === false ? $fallback : date('Y-m-d', $t);
    }

    private function loadGroups(LedgerSnapshot $s): void
    {
        $rows = $this->db->table('accgrpmstn g')
            ->select('g.acc_grp_id, g.acc_grp_name, u.crs_mst_is_primary, u.under_crs_mst_id, u.crs_mst_parent_id')
            ->join('undercrsmt u',
                'u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = ' . $this->cmp
                . ' AND u.cmpfymastr_id = ' . $this->fy, 'inner')
            ->orderBy('g.acc_grp_name', 'ASC')
            ->orderBy('g.acc_grp_id', 'ASC')
            ->get()->getResultArray();

        foreach ($rows as $r) {
            $gid = (int)$r['acc_grp_id'];
            if (isset($s->groups[$gid])) {
                $s->quality['duplicate_groups']++;
                continue;
            }
            $s->groups[$gid] = [
                'id'         => $gid,
                'name'       => (string)$r['acc_grp_name'],
                'is_primary' => (int)$r['crs_mst_is_primary'],
                'parent_gid' => (int)($r['under_crs_mst_id'] ?? 0),
                'cat'        => (int)($r['crs_mst_parent_id'] ?? 0),
            ];
        }
        foreach ($s->groups as $gid => $g) {
            if ($g['parent_gid'] > 0 && isset($s->groups[$g['parent_gid']])) {
                $s->children[$g['parent_gid']][] = $gid;
            }
        }
    }

    private function loadAccounts(LedgerSnapshot $s): void
    {
        $rows = $this->db->table('acctmaster a')
            ->select('a.acc_id, a.acc_name, a.bsd_id,'
                . ' m1.crs_mst_id AS m1_id, m1.under_crs_mst_id AS u1, m1.crs_mst_parent_id AS p1,'
                . ' m14.crs_mst_id AS m14_id, m14.under_crs_mst_id AS u14, m14.crs_mst_parent_id AS p14', false)
            ->join('undercrsmt m1',
                'm1.crs_mst_id = a.acc_id AND m1.crs_mst_type = 1 AND m1.cmp_id = ' . $this->cmp
                . ' AND m1.cmpfymastr_id = ' . $this->fy, 'left')
            ->join('undercrsmt m14',
                'm14.crs_mst_id = a.acc_id AND m14.crs_mst_type = 14 AND m14.cmp_id = ' . $this->cmp
                . ' AND m14.cmpfymastr_id = ' . $this->fy, 'left')
            ->where('a.cmp_id', $this->cmp)
            ->orderBy('a.acc_name', 'ASC')
            ->orderBy('a.acc_id', 'ASC')
            ->get()->getResultArray();

        foreach ($rows as $r) {
            $id = (int)$r['acc_id'];
            if (isset($s->accounts[$id])) {           // join fan-out from duplicate mapping rows
                $s->quality['duplicate_mappings']++;
                continue;
            }
            $isBsd = ($r['bsd_id'] !== null && $r['bsd_id'] !== '');
            if ($isBsd && $r['m14_id'] !== null) {     // bill-sundry mapping wins for bill-sundry ledgers
                $mapped = true; $group = (int)$r['u14']; $cat = (int)$r['p14'];
            } elseif ($r['m1_id'] !== null) {
                $mapped = true; $group = (int)$r['u1'];  $cat = (int)$r['p1'];
            } else {
                $mapped = false; $group = 0; $cat = 0;
            }
            $s->accounts[$id] = $this->newAccount($id, (string)$r['acc_name'], $isBsd, $mapped, $group, $cat, false);
        }
    }

    private function newAccount(int $id, string $name, bool $isBsd, bool $mapped, int $group, int $cat, bool $missing): array
    {
        return [
            'id' => $id, 'name' => $name, 'is_bsd' => $isBsd, 'mapped' => $mapped,
            'group_id' => $group, 'own_cat' => $cat, 'cat' => $cat, 'root_gid' => 0,
            'chain_ok' => true, 'missing_master' => $missing,
            'op' => 0.0, 'pre' => 0.0, 'dr' => 0.0, 'cr' => 0.0, 'mv' => 0.0, 'cum' => 0.0, 'closing' => 0.0,
        ];
    }

    /** @return array<int,float> acc_id => FY opening balance (+Dr / -Cr) */
    private function loadOpenings(LedgerSnapshot $s): array
    {
        $qb = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op', false)
            ->where('ob.cmp_id', $this->cmp)
            ->where('ob.cmpfymastr_id', $this->fy)
            ->groupBy('ob.acc_id');
        if (!$s->consolidated) {
            $qb->where('ob.hobo_id', $this->bo);
        }
        $out = [];
        foreach ($qb->get()->getResultArray() as $r) {
            $out[(int)$r['acc_id']] = (float)($r['op'] ?? 0);
        }
        return $out;
    }

    /** @return array<int,array{pre:float,dr:float,cr:float}> */
    private function loadMovements(LedgerSnapshot $s): array
    {
        $from = $this->db->escape($s->from);
        $qb   = $this->db->table('accttxnmst a')
            ->select("a.acc_id,
                SUM(CASE WHEN a.acc_txn_date <  $from THEN (CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt WHEN a.acc_txn_dr_cr = 2 THEN -a.acc_txn_amt ELSE 0 END) ELSE 0 END) AS pre,
                SUM(CASE WHEN a.acc_txn_date >= $from AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
                SUM(CASE WHEN a.acc_txn_date >= $from AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr", false)
            ->where('a.cmp_id', $this->cmp)
            ->where('a.acc_txn_type', 1)
            ->where('a.vch_txn_id > 0', null, false)
            ->where('a.acc_txn_date >=', $s->fyStart)
            ->where('a.acc_txn_date <=', $s->to)
            ->groupBy('a.acc_id');
        if (!$s->consolidated) {
            $qb->where('a.hobo_id', $this->bo);
        }
        $out = [];
        foreach ($qb->get()->getResultArray() as $r) {
            $out[(int)$r['acc_id']] = [
                'pre' => (float)($r['pre'] ?? 0),
                'dr'  => (float)($r['dr'] ?? 0),
                'cr'  => (float)($r['cr'] ?? 0),
            ];
        }
        return $out;
    }

    private function attachBalances(LedgerSnapshot $s, array $opening, array $moves): void
    {
        // Ledgers that have balances/movement but are not in the account master (orphans) are kept.
        foreach (array_unique(array_merge(array_keys($opening), array_keys($moves))) as $id) {
            if (!isset($s->accounts[$id])) {
                $s->accounts[$id] = $this->newAccount($id, 'Account #' . $id . ' (missing from account master)', false, false, 0, 0, true);
                $s->quality['missing_masters']++;
            }
        }
        foreach ($s->accounts as $id => &$a) {
            $a['op']  = round($opening[$id] ?? 0.0, 2);
            $a['pre'] = round($moves[$id]['pre'] ?? 0.0, 2);
            $a['dr']  = round($moves[$id]['dr'] ?? 0.0, 2);
            $a['cr']  = round($moves[$id]['cr'] ?? 0.0, 2);
            $a['mv']  = round($a['dr'] - $a['cr'], 2);
            $a['cum'] = round($a['pre'] + $a['mv'], 2);
            $a['closing'] = round($a['op'] + $a['pre'] + $a['mv'], 2);
        }
        unset($a);
    }

    /** Places every account in a category by walking its group chain up to the top group. */
    private function classify(LedgerSnapshot $s): void
    {
        foreach ($s->accounts as $id => &$a) {
            $a['chain_ok'] = true;
            $a['root_gid'] = 0;

            if (!$a['mapped']) {
                $a['cat'] = 0;
                $a['chain_ok'] = false;
                if ($this->hasActivity($a)) { $s->quality['unmapped']++; }
                continue;
            }
            if ($a['group_id'] === 0) {                 // ledger placed directly under a category
                $a['cat'] = $a['own_cat'];
                continue;
            }
            $g = $a['group_id']; $root = 0; $seen = [];
            while ($g > 0 && isset($s->groups[$g]) && !isset($seen[$g])) {
                $seen[$g] = true;
                $root     = $g;
                $g        = $s->groups[$g]['parent_gid'];
            }
            if ($root > 0 && $g === 0) {                // reached the top group: chain is intact
                $a['root_gid'] = $root;
                $topCat        = $s->groups[$root]['cat'];
                $a['cat']      = $topCat > 0 ? $topCat : $a['own_cat'];
                $s->accByGroup[$a['group_id']][] = $id;
            } else {                                     // group missing / cycle: keep the ledger's own category
                $a['chain_ok'] = false;
                $a['cat']      = $a['own_cat'];
                if ($this->hasActivity($a)) { $s->quality['broken_chains']++; }
            }
        }
        unset($a);
    }

    private function hasActivity(array $a): bool
    {
        return $a['op'] != 0.0 || $a['pre'] != 0.0 || $a['dr'] != 0.0 || $a['cr'] != 0.0;
    }

    // ------------------------------------------------------------------------------------
    // Classification helpers
    // ------------------------------------------------------------------------------------

    public static function isBsCat(int $cat): bool { return in_array($cat, array_merge(self::BS_LIABILITY_CATS, self::BS_ASSET_CATS), true); }
    public static function isPlCat(int $cat): bool { return in_array($cat, self::PL_CATS, true); }

    /**
     * P&L section a ledger is presented in (0 = not a P&L ledger).
     * Ledgers placed directly under a category keep the long-standing name based
     * re-slotting between Purchase(11) and Direct Expenses(7); it only moves them between
     * P&L sections, so it can never change the profit.
     */
    public function plSlot(array $a): int
    {
        $cat = (int)$a['cat'];
        if (!self::isPlCat($cat)) {
            return 0;
        }
        if ($a['group_id'] === 0 && $a['mapped']) {
            $n = strtoupper(trim((string)$a['name']));
            if (strpos($n, 'FOC UNDER RCM') !== false || strpos($n, 'FOC EXPENSE') !== false) {
                return 7;
            }
            if (strpos($n, 'PURCHASE ACCOUNT') !== false || strpos($n, 'OPENING STOCK') !== false || preg_match('/\bPURCHASE\b/', $n)) {
                return 11;
            }
        }
        return $cat;
    }

    /**
     * Everything a presenter needs to lay out ONE category.
     *
     * @param callable|null $slotOf  fn(array $account): int  (default: the account's category)
     * @return array{groups:array<int,array>,accounts:array<int,array>,total:float}
     *   groups   top groups of the category: id, name, total, accounts (ids below the group)
     *   accounts ledgers shown on their own row: kind = 'primary' (directly under the category)
     *            or 'ungrouped' (group chain broken)
     *   total    signed sum (debit +) of the field over the whole category
     */
    public function categoryItems(LedgerSnapshot $s, int $cat, string $field, ?callable $slotOf = null): array
    {
        $groups = []; $accounts = []; $total = 0.0;

        foreach ($s->groups as $gid => $g) {
            if ($g['parent_gid'] === 0 && $g['cat'] === $cat) {
                $t = $s->groupSum($gid, $field);
                $groups[] = ['id' => $gid, 'name' => $g['name'], 'total' => $t, 'accounts' => $s->accountsUnder($gid)];
                $total += $t;
            }
        }
        foreach ($s->accounts as $id => $a) {
            $slot = $slotOf ? (int)$slotOf($a) : (int)$a['cat'];
            if ($slot !== $cat || !$a['mapped']) {
                continue;
            }
            if ($a['group_id'] === 0) {
                $kind = 'primary';
            } elseif (!$a['chain_ok']) {
                $kind = 'ungrouped';
            } else {
                continue;                               // already inside one of the groups above
            }
            $accounts[] = ['id' => $id, 'name' => $a['name'], 'total' => (float)$a[$field], 'kind' => $kind, 'is_bsd' => $a['is_bsd']];
            $total += (float)$a[$field];
        }
        return ['groups' => $groups, 'accounts' => $accounts, 'total' => round($total, 2)];
    }

    /**
     * Ledgers that are neither Balance Sheet nor Profit & Loss ledgers (no mapping for the FY,
     * category outside 1-5 / 7,8,10-13, missing from the account master ...) and have a
     * balance or movement. Presenters list them explicitly; they are never dropped.
     *
     * @return array<int,array>
     */
    public function unclassified(LedgerSnapshot $s): array
    {
        $out = [];
        foreach ($s->accounts as $a) {
            $cat = (int)$a['cat'];
            if (self::isBsCat($cat) || self::isPlCat($cat)) {
                continue;
            }
            if (!$this->hasActivity($a) && $a['closing'] == 0.0) {
                continue;
            }
            $out[] = $a;
        }
        return $out;
    }

    // ------------------------------------------------------------------------------------
    // Profit & Loss (one implementation for Balance Sheet, P&L and Trial Balance)
    // ------------------------------------------------------------------------------------

    /**
     * @param string $field  'mv'  = movement inside [from,to]      (the P&L report)
     *                       'cum' = movement FY start .. to         (the Balance Sheet's current-year result)
     * @return array<string,float>
     *   slot    => amount per P&L section (debit sections positive as-is, credit sections sign flipped)
     *   l1,r1   trading account totals; gross_profit, gross_loss
     *   l2,r2   profit & loss account totals; net_profit, net_loss, net (profit positive)
     */
    public function profitLoss(LedgerSnapshot $s, float $openingStock, float $closingStock, string $field = 'mv'): array
    {
        $signed = array_fill_keys(self::PL_CATS, 0.0);
        foreach ($s->accounts as $a) {
            $slot = $this->plSlot($a);
            if ($slot) {
                $signed[$slot] += (float)$a[$field];
            }
        }
        $res = [];
        foreach (self::PL_LEFT_STAGE1 as $c)  { $res[$c] = round($signed[$c], 2); }
        foreach (self::PL_LEFT_STAGE2 as $c)  { $res[$c] = round($signed[$c], 2); }
        foreach (self::PL_RIGHT_STAGE1 as $c) { $res[$c] = round(-$signed[$c], 2); }
        foreach (self::PL_RIGHT_STAGE2 as $c) { $res[$c] = round(-$signed[$c], 2); }

        $l1 = round($openingStock + $res[11] + $res[7], 2);
        $r1 = round($closingStock + $res[8] + $res[10], 2);
        $gp = $r1 > $l1 ? round($r1 - $l1, 2) : 0.0;
        $gl = $l1 > $r1 ? round($l1 - $r1, 2) : 0.0;
        $l2 = round($gl + $res[13], 2);
        $r2 = round($gp + $res[12], 2);
        $np = $r2 > $l2 ? round($r2 - $l2, 2) : 0.0;
        $nl = $l2 > $r2 ? round($l2 - $r2, 2) : 0.0;

        return $res + [
            'opening_stock' => round($openingStock, 2), 'closing_stock' => round($closingStock, 2),
            'l1' => $l1, 'r1' => $r1, 'gross_profit' => $gp, 'gross_loss' => $gl,
            'l2' => $l2, 'r2' => $r2, 'net_profit' => $np, 'net_loss' => $nl,
            'net' => round($np - $nl, 2),
        ];
    }

    /** Sum of the FY opening balances of all P&L ledgers (credit-positive), i.e. profit brought forward. */
    public function profitBroughtForward(LedgerSnapshot $s): float
    {
        $t = 0.0;
        foreach ($s->accounts as $a) {
            if (self::isPlCat((int)$a['cat'])) {
                $t += (float)$a['op'];
            }
        }
        return round(-$t, 2);
    }

    // ------------------------------------------------------------------------------------
    // Reconciliation
    // ------------------------------------------------------------------------------------

    /** Same figure as openingTotal() without building a snapshot (one SUM over accoppybal). */
    public function openingTotalDirect(bool $consolidated): float
    {
        $qb = $this->db->table('accoppybal ob')->select('COALESCE(SUM(ob.acc_op_bal),0) AS t', false)
            ->where('ob.cmp_id', $this->cmp)->where('ob.cmpfymastr_id', $this->fy);
        if (!$consolidated) { $qb->where('ob.hobo_id', $this->bo); }
        $row = $qb->get()->getRowArray();
        return round((float)($row['t'] ?? 0), 2);
    }

    /** Sum of all FY opening balances of every ledger (+Dr / -Cr). */
    public function openingTotal(LedgerSnapshot $s): float
    {
        $t = 0.0;
        foreach ($s->accounts as $a) { $t += (float)$a['op']; }
        return round($t, 2);
    }

    /**
     * Debit minus credit of ALL ledger rows FY start .. to. Zero when every voucher balances.
     * A non-zero value is a real defect in the posted data (see unbalancedVouchers()).
     */
    public function imbalance(LedgerSnapshot $s): float
    {
        $t = 0.0;
        foreach ($s->accounts as $a) { $t += (float)$a['cum']; }
        return round($t, 2);
    }

    /**
     * Vouchers whose debit and credit rows differ (FY start .. to), biggest first.
     * `vch_type_id` 23 is the composition-scheme "GST PAID A/C" system journal. It is posted next to
     * a purchase or a sale (link table vchbridgen) and the two belong together, so a voucher that is
     * out of balance can be balanced by its journal, or the other way round.
     *
     * @return array<int,array{vch_txn_id:int,dr:float,cr:float,diff:float,vch_type_id:?int,vch_date:?string}>
     */
    public function unbalancedVouchers(LedgerSnapshot $s, int $limit = 200): array
    {
        $qb = $this->db->table('accttxnmst a')
            ->select("a.vch_txn_id,
                SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
                SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr", false)
            ->where('a.cmp_id', $this->cmp)
            ->where('a.acc_txn_type', 1)
            ->where('a.vch_txn_id > 0', null, false)
            ->where('a.acc_txn_date >=', $s->fyStart)
            ->where('a.acc_txn_date <=', $s->to)
            ->groupBy('a.vch_txn_id')
            ->having('ABS(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) - SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END)) > 0.005', null, false);
        if (!$s->consolidated) {
            $qb->where('a.hobo_id', $this->bo);
        }
        $rows = $qb->get()->getResultArray();

        $out = [];
        foreach ($rows as $r) {
            $dr = round((float)$r['dr'], 2); $cr = round((float)$r['cr'], 2);
            $out[(int)$r['vch_txn_id']] = ['vch_txn_id' => (int)$r['vch_txn_id'], 'dr' => $dr, 'cr' => $cr,
                                           'diff' => round($dr - $cr, 2), 'vch_type_id' => null, 'vch_date' => null];
        }
        if ($out) {                                       // label them with the voucher header (never used to filter)
            $hdr = $this->db->table('vchtxnconso')->select('vch_txn_id, vch_type_id, vch_date')
                ->where('cmp_id', $this->cmp)->whereIn('vch_txn_id', array_keys($out))->get()->getResultArray();
            foreach ($hdr as $h) {
                $id = (int)$h['vch_txn_id'];
                if (isset($out[$id])) {
                    $out[$id]['vch_type_id'] = (int)$h['vch_type_id'];
                    $out[$id]['vch_date']    = (string)$h['vch_date'];
                }
            }
        }
        usort($out, static fn($x, $y) => abs($y['diff']) <=> abs($x['diff']));
        return array_slice($out, 0, $limit);
    }
}
