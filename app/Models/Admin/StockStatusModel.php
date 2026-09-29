<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;

class StockStatusModel extends Model {
    protected $externaldb;
    protected $univerpaic_db;
    protected $aicountly_db;
    protected $session;
    protected $company_id;
    protected $fy_id;
    protected $bo_id;
    protected $profile_id;

    public function __construct() {
        parent::__construct();
        $this->externaldb     = new externaldb();
        $this->univerpaic_db  = $this->externaldb->univerpaic_db();
        $this->aicountly_db   = $this->externaldb->aicountly_db();
        $this->session        = \Config\Services::session();
        $this->company_id     = $this->session->get('ses_company_id');
        $this->fy_id          = $this->session->get('ses_comp_fy_id');
        $this->bo_id          = $this->session->get('ses_boid');
        $this->profile_id     = $this->session->get('ses_cmp_prf_id');
    }

    /**
     * Case-insensitive text filter helper.
     */
    private function matchesFilter(?string $haystack, string $op, string $needle): bool {
        $h = mb_strtolower((string)$haystack);
        $n = mb_strtolower((string)$needle);
        if ($n === '') return true;
        switch ($op) {
            case 'equals':
                return $h === $n;
            case 'starts_with':
                return strpos($h, $n) === 0;
            case 'contains':
            default:
                return strpos($h, $n) !== false;
        }
    }

    // ════════════════════════════════════════════════════════════════
    // SHARED: Valuation calculation for a single item key
    //         Used by inventoryStatusPaged, closingStockTotal,
    //         and calculateTotalValuation — ONE place, no duplication.
    // ════════════════════════════════════════════════════════════════
    /**
     * @param float  $opQty       Opening quantity
     * @param float  $opVal       Opening value
     * @param array  $txns        Transactions from fetchTxnsWithVoucherInfo()
     * @param bool   $trackProfit Whether to calculate profit/loss
     * @return array ['running_qty', 'running_value', 'running_avg', 'total_profit']
     */
    private function computeValuation(float $opQty, float $opVal, array $txns, bool $trackProfit = false): array
    {
        $running_qty   = $opQty;
        $running_value = $opVal;
        $running_avg   = ($opQty > 0 && $opVal > 0) ? ($opVal / $opQty) : 0.0;
        $total_profit  = 0.0;

        // Group transactions by voucher
        $voucher_groups = [];
        foreach ($txns as $t) {
            $vch_id = $t['vch_txn_id'];
            if (!isset($voucher_groups[$vch_id])) {
                $voucher_groups[$vch_id] = [
                    'voucher_type' => $t['voucher_type'] ?? '',
                    'vch_type_id'  => $t['vch_type_id']  ?? '',
                    'date'         => $t['itm_txn_date']  ?? '',
                    'in_qty'       => 0,
                    'in_amt'       => 0,
                    'out_qty'      => 0,
                    'out_amt'      => 0,
                ];
            }

            if ((int)$t['itm_txn_dr_cr'] === 1) {
                $voucher_groups[$vch_id]['in_qty'] += (float)$t['itm_txn_qty'];
                $voucher_groups[$vch_id]['in_amt'] += (float)$t['itm_txn_amt'];
            } else {
                $voucher_groups[$vch_id]['out_qty'] += (float)$t['itm_txn_qty'];
                $voucher_groups[$vch_id]['out_amt'] += (float)$t['itm_txn_amt'];
            }
        }

        // Process each voucher group
        foreach ($voucher_groups as $vch_id => $vg) {
            $is_stock_journal = (strtolower($vg['voucher_type']) === 'stock journal' || $vg['vch_type_id'] == '20');

            if ($is_stock_journal && $vg['in_qty'] > 0 && $vg['out_qty'] > 0) {
                $cogs = $vg['out_qty'] * $running_avg;

                if ($trackProfit && abs($vg['out_qty'] - $vg['in_qty']) < 0.001) {
                    $total_profit += ($cogs - $vg['in_amt']);
                }

                $running_qty   -= $vg['out_qty'];
                $running_value -= $cogs;
                $running_qty   += $vg['in_qty'];
                $running_value += $vg['in_amt'];

                if ($running_qty > 0) {
                    $running_avg = $running_value / $running_qty;
                }
            } else {
                if ($vg['out_qty'] > 0) {
                    $cogs = $vg['out_qty'] * $running_avg;
                    if ($trackProfit) {
                        $total_profit += ($vg['out_amt'] - $cogs);
                    }
                    $running_qty   -= $vg['out_qty'];
                    $running_value -= $cogs;
                }

                if ($vg['in_qty'] > 0) {
                    $new_total_value = $running_value + $vg['in_amt'];
                    $new_total_qty   = $running_qty   + $vg['in_qty'];

                    if ($new_total_qty > 0) {
                        $running_avg = $new_total_value / $new_total_qty;
                    }

                    $running_qty   = $new_total_qty;
                    $running_value = $new_total_value;
                }
            }
        }

        return [
            'running_qty'   => $running_qty,
            'running_value' => $running_value,
            'running_avg'   => $running_avg,
            'total_profit'  => $total_profit,
        ];
    }

    // ════════════════════════════════════════════��═══════════════════
    // BATCH: Fetch transactions for MULTIPLE keys in one query
    //        instead of one query per key (N+1 → 1)
    // ════════════════════════════════════════════════════════════════
    
	private function fetchTxnsBatch($mysql, int $cmpId, int $boId, array $keys,
                                string $fromDate, string $toDate, $mcId,
                                int $isConsolidated = 0): array  // ← ADDED parameter
{
    if (empty($keys)) return [];

    $builder = $mysql->table('itemtxnmst t')
        ->select('
            t.itm_id_unit_id,
            t.itm_txn_date,
            t.itm_txn_dr_cr,
            t.itm_txn_qty,
            t.itm_txn_rate,
            t.itm_txn_amt,
            t.itm_txn_id,
            t.vch_txn_id,
            c.vch_type_id,
            vt.vch_name as voucher_type
        ')
        ->join('vchtxnconso c',  'c.vch_txn_id   = t.vch_txn_id',  'left')
        ->join('vchtypemst vt',  'vt.vch_type_id = c.vch_type_id', 'left')
        ->where('t.cmp_id', $cmpId)
        ->whereIn('t.itm_id_unit_id', $keys)
        ->where('t.itm_txn_date >=', $fromDate)
        ->where('t.itm_txn_date <=', $toDate);

    // ✅ FIX: skip hobo_id filter when consolidated — include ALL branches
    if ($isConsolidated === 0) {
        $builder->where('t.hobo_id', $boId);
    }

    if (!empty($mcId)) {
        $builder->where('t.mat_cent_id', $mcId);
    }

    $builder->orderBy('t.itm_id_unit_id', 'ASC')
            ->orderBy('t.itm_txn_date',    'ASC')
            ->orderBy('t.vch_txn_id',       'ASC')
            ->orderBy('t.itm_txn_id',       'ASC');

    $rows = $builder->get()->getResultArray();

    // Group by itm_id_unit_id — unchanged
    $map = [];
    foreach ($rows as $r) {
        $map[$r['itm_id_unit_id']][] = $r;
    }
    return $map;
}

	private function fetchTxnsBatch_11_04_2026($mysql, int $cmpId, int $boId, array $keys,
                                    string $fromDate, string $toDate, $mcId): array
    {
        if (empty($keys)) return [];

        $builder = $mysql->table('itemtxnmst t')
            ->select('
                t.itm_id_unit_id,
                t.itm_txn_date,
                t.itm_txn_dr_cr,
                t.itm_txn_qty,
                t.itm_txn_rate,
                t.itm_txn_amt,
                t.itm_txn_id,
                t.vch_txn_id,
                c.vch_type_id,
                vt.vch_name as voucher_type
            ')
            ->join('vchtxnconso c',  'c.vch_txn_id  = t.vch_txn_id', 'left')
            ->join('vchtypemst vt',  'vt.vch_type_id = c.vch_type_id', 'left')
            ->where('t.cmp_id',  $cmpId)
            ->where('t.hobo_id', $boId)
            ->whereIn('t.itm_id_unit_id', $keys)
            ->where('t.itm_txn_date >=', $fromDate)
            ->where('t.itm_txn_date <=', $toDate);

        if (!empty($mcId)) {
            $builder->where('t.mat_cent_id', $mcId);
        }

        $builder->orderBy('t.itm_id_unit_id', 'ASC')
                ->orderBy('t.itm_txn_date',    'ASC')
                ->orderBy('t.vch_txn_id',       'ASC')
                ->orderBy('t.itm_txn_id',       'ASC');

        $rows = $builder->get()->getResultArray();

        // Group by itm_id_unit_id
        $map = [];
        foreach ($rows as $r) {
            $map[$r['itm_id_unit_id']][] = $r;
        }
        return $map;
    }

    // ════════════════════════════════════════════════════════════════
    // closingStockTotal — now uses shared computeValuation()
    // ════════════════════════════════════════════════════════════════
    public function closingStockTotal($from_date, $to_date, array $filters = []): float
{
    $fn_start = microtime(true);
    $logTag   = '[ClosingStockTotal]';
    log_message('info', "$logTag ── START ── from=$from_date to=$to_date");

    $mysql = $this->db;

    $asOfStr = date('Y-m-d', strtotime($to_date));
    if (!$asOfStr) return 0.0;

    $asOf    = new \DateTimeImmutable($asOfStr);
    $fyStart = $this->fyStartFor($asOf)->format('Y-m-d');

    $cmpId = $this->company_id;
    $boId  = $this->bo_id;
    $fyId  = $this->fy_id;

    // ✅ FIX: read consolidated flag from filters
    $isConsolidated = (int)($filters['consolidated'] ?? 0);

    $valReq = strtoupper(trim($this->session->get('ses_dflt_val_method') ?? 'AVG'));
    if ($valReq === '' || $valReq === '0') $valReq = 'AVG';
    if (!in_array($valReq, ['AUTO', 'FIFO', 'LIFO', 'AVG'], true)) $valReq = 'AVG';

    $itemId = $filters['item_id'] ?? '';
    $unitId = $filters['unit_id'] ?? '';
    $mcId   = $filters['mc_id']   ?? '';

    $t1 = microtime(true);
    // ✅ FIX: pass isConsolidated to loadItemMethodMap
    $methodMap = $this->loadItemMethodMap($mysql, $cmpId, $boId, $fyId, $isConsolidated);
    $resolveMethod = function (string $key) use ($valReq, $methodMap): string {
        if ($valReq !== 'AUTO' && $valReq !== '') return $valReq;
        $m = $methodMap[$key] ?? 'AVG';
        if (is_numeric($m)) {
            return match ((int)$m) { 1 => 'FIFO', 2 => 'LIFO', 3 => 'AVG', default => 'AVG' };
        }
        return strtoupper($m);
    };
    log_message('info', "$logTag MethodMap => " . round(microtime(true) - $t1, 4) . 's');

    $t2 = microtime(true);
    // ✅ FIX: pass isConsolidated to findItemKeysForReport
    $allKeys = $this->findItemKeysForReport(
        $mysql, $cmpId, $boId, $fyId,
        $fyStart, $asOfStr,
        $itemId, $unitId, $mcId,
        '', '', '',
        $isConsolidated   // ← ADDED
    );
    log_message('info', "$logTag FindKeys => " . count($allKeys) . " keys | " . round(microtime(true) - $t2, 4) . 's');

    if (empty($allKeys)) {
        log_message('info', "$logTag ── END ── 0 keys, returning 0.0");
        return 0.0;
    }

    $t3 = microtime(true);
    // ✅ FIX: pass isConsolidated to loadOpenings
    [$opQtyMap, $opValMap] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $allKeys, $isConsolidated);
    log_message('info', "$logTag BatchOpenings => " . round(microtime(true) - $t3, 4) . 's');

    $t4 = microtime(true);
    // ✅ FIX: pass isConsolidated to fetchTxnsBatch
    $txnMap = $this->fetchTxnsBatch($mysql, $cmpId, $boId, $allKeys, $fyStart, $asOfStr, $mcId, $isConsolidated);
    log_message('info', "$logTag BatchTxns => " . round(microtime(true) - $t4, 4) . 's');

    $t5 = microtime(true);
    $totalValuation = 0.0;
    foreach ($allKeys as $key) {
        $opQty = (float)($opQtyMap[$key] ?? 0.0);
        $opVal = (float)($opValMap[$key] ?? 0.0);
        $txns  = $txnMap[$key] ?? [];
        $result = $this->computeValuation($opQty, $opVal, $txns, false);
        $totalValuation += $result['running_value'];
    }
    log_message('info', "$logTag ComputeLoop => " . round(microtime(true) - $t5, 4) . 's');

    $fn_total = round(microtime(true) - $fn_start, 4);
    log_message('info', "$logTag ── END ── valuation=$totalValuation | TOTAL: {$fn_total}s");

    return (float)parseAmount($totalValuation);
}
	public function closingStockTotal_11_04_2026($from_date, $to_date, array $filters = []): float
    {
        $fn_start = microtime(true);
        $logTag   = '[ClosingStockTotal]';
        log_message('info', "$logTag ── START ── from=$from_date to=$to_date");

        $mysql = $this->db;

        $asOfStr = date('Y-m-d', strtotime($to_date));
        if (!$asOfStr) return 0.0;

        $asOf    = new \DateTimeImmutable($asOfStr);
        $fyStart = $this->fyStartFor($asOf)->format('Y-m-d');

        $cmpId = $this->company_id;
        $boId  = $this->bo_id;
        $fyId  = $this->fy_id;

        $valReq = strtoupper(trim($this->session->get('ses_dflt_val_method') ?? 'AVG'));
        if ($valReq === '' || $valReq === '0') $valReq = 'AVG';
        if (!in_array($valReq, ['AUTO', 'FIFO', 'LIFO', 'AVG'], true)) $valReq = 'AVG';

        $itemId = $filters['item_id'] ?? '';
        $unitId = $filters['unit_id'] ?? '';
        $mcId   = $filters['mc_id']   ?? '';

        // Load method map
        $t1 = microtime(true);
        $methodMap = $this->loadItemMethodMap($mysql, $cmpId, $boId, $fyId);
        $resolveMethod = function (string $key) use ($valReq, $methodMap): string {
            if ($valReq !== 'AUTO' && $valReq !== '') return $valReq;
            $m = $methodMap[$key] ?? 'AVG';
            if (is_numeric($m)) {
                return match ((int)$m) { 1 => 'FIFO', 2 => 'LIFO', 3 => 'AVG', default => 'AVG' };
            }
            return strtoupper($m);
        };
        log_message('info', "$logTag MethodMap => " . round(microtime(true) - $t1, 4) . 's');

        // Find keys
        $t2 = microtime(true);
        $allKeys = $this->findItemKeysForReport(
            $mysql, $cmpId, $boId, $fyId,
            $fyStart, $asOfStr,
            $itemId, $unitId, $mcId,
            '', '', ''
        );
        log_message('info', "$logTag FindKeys => " . count($allKeys) . " keys | " . round(microtime(true) - $t2, 4) . 's');

        if (empty($allKeys)) {
            log_message('info', "$logTag ── END ── 0 keys, returning 0.0");
            return 0.0;
        }

        // Batch load openings
        $t3 = microtime(true);
        [$opQtyMap, $opValMap] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $allKeys);
        log_message('info', "$logTag BatchOpenings => " . round(microtime(true) - $t3, 4) . 's');

        // Batch load transactions
        $t4 = microtime(true);
        $txnMap = $this->fetchTxnsBatch($mysql, $cmpId, $boId, $allKeys, $fyStart, $asOfStr, $mcId);
        log_message('info', "$logTag BatchTxns => " . round(microtime(true) - $t4, 4) . 's');

        // Compute valuation
        $t5 = microtime(true);
        $totalValuation = 0.0;
        foreach ($allKeys as $key) {
            $opQty = (float)($opQtyMap[$key] ?? 0.0);
            $opVal = (float)($opValMap[$key] ?? 0.0);
            $txns  = $txnMap[$key] ?? [];

            $result = $this->computeValuation($opQty, $opVal, $txns, false);
            $totalValuation += $result['running_value'];
        }
        log_message('info', "$logTag ComputeLoop => " . round(microtime(true) - $t5, 4) . 's');

        $fn_total = round(microtime(true) - $fn_start, 4);
        log_message('info', "$logTag ── END ── valuation=$totalValuation | TOTAL: {$fn_total}s");

        return (float)parseAmount($totalValuation);
    }


    // ════════════════════════════════════════════════════════════════
    // inventoryStatusPaged — OPTIMIZED
    // ════════════════════════════════════════════════════════════════
	public function inventoryStatusPaged($cmpId = null, $boId = null, $fyId = null, $input = null)
{
    $fn_start = microtime(true);
    $timings  = [];
    $logTag   = '[InventoryStatusPaged]';
    log_message('info', "$logTag ── START ── cmpId=$cmpId boId=$boId fyId=$fyId");

    $mysql     = $this->db;
    $pg        = $this->externaldb->postgr_db();
    $reqposted = !empty($input) ? $input : $_POST;

    $asOfStr = $reqposted['to_date_ymd']
               ?? date('Y-m-d', strtotime($reqposted['to_date'] ?? date('Y-m-d')));
    if (!$asOfStr) {
        return json_encode(['ok' => false, 'error' => 'as_of required']);
    }

    $asOf = new \DateTimeImmutable($asOfStr);

    $valReq = strtoupper(trim($reqposted['val_id'] ?? ''));
    if ($valReq === '' || $valReq === '0') $valReq = 'AVG';
    if (!in_array($valReq, ['AUTO', 'FIFO', 'LIFO', 'AVG'], true)) $valReq = 'AVG';

    $allowNeg = ((int)($reqposted['allowNegative'] ?? 1)) === 1;

    $itemId = $reqposted['item_id'] ?? '';
    $unitId = $reqposted['unit_id'] ?? '';
    $mcId   = $reqposted['mc_id']   ?? '';

    $filterText  = trim((string)($reqposted['filter_text']  ?? ''));
    $filterField = strtolower($reqposted['filter_field'] ?? '');
    $filterOp    = strtolower($reqposted['filter_op']    ?? 'contains');
    if (!in_array($filterField, ['item', 'unit', 'item_unit'], true)) $filterField = '';
    if (!in_array($filterOp, ['contains', 'starts_with', 'equals'], true)) $filterOp = 'contains';

    $includeNilBalances = ((int)($reqposted['nill']          ?? 0)) === 1;
    $showOpening        = ((int)($reqposted['show_opening']  ?? 0)) === 1;
    $showInwards        = ((int)($reqposted['show_inwards']  ?? 0)) === 1;
    $showOutwards       = ((int)($reqposted['show_outwards'] ?? 0)) === 1;

    $hasFilter = !empty($filterText) || !empty($itemId) || !empty($unitId) || !empty($mcId);

    $pq_curPage = max((int)($reqposted['pq_curpage'] ?? 1), 1);
    $pq_rPP     = max(min((int)($reqposted['pq_rpp'] ?? 100), 100), 1);

    $fyStart = $this->fyStartFor($asOf)->format('Y-m-d');
    $fyEnd   = $this->fyEndFor($asOf)->format('Y-m-d');

    $t1 = microtime(true);
    $methodMap = $this->loadItemMethodMap($mysql, $cmpId, $boId, $fyId);
    $resolveMethod = function (string $key) use ($valReq, $methodMap): string {
        if ($valReq !== 'AUTO' && $valReq !== '') return $valReq;
        $m = $methodMap[$key] ?? 'AVG';
        if (is_numeric($m)) {
            return match ((int)$m) { 1 => 'FIFO', 2 => 'LIFO', 3 => 'AVG', default => 'AVG' };
        }
        return strtoupper($m);
    };
    $timings[] = ['label' => 'Q1-MethodMap', 'seconds' => microtime(true) - $t1];
    log_message('info', "$logTag Q1-MethodMap => " . count($methodMap) . " entries | " . round(end($timings)['seconds'], 4) . 's');

    $t2 = microtime(true);
    $allKeysUnfiltered = $this->findItemKeysForReport(
        $mysql, $cmpId, $boId, $fyId,
        $fyStart, $asOfStr,
        '', '', '',
        '', '', ''
    );
    $timings[] = ['label' => 'Q2a-UnfilteredKeys', 'seconds' => microtime(true) - $t2];
    log_message('info', "$logTag Q2a-UnfilteredKeys => " . count($allKeysUnfiltered) . " keys | " . round(end($timings)['seconds'], 4) . 's');

    $t2b = microtime(true);
    $allKeysFiltered = $this->findItemKeysForReport(
        $mysql, $cmpId, $boId, $fyId,
        $fyStart, $asOfStr,
        $itemId, $unitId, $mcId,
        $filterField, $filterOp, $filterText
    );
    $timings[] = ['label' => 'Q2b-FilteredKeys', 'seconds' => microtime(true) - $t2b];
    log_message('info', "$logTag Q2b-FilteredKeys => " . count($allKeysFiltered) . " keys | " . round(end($timings)['seconds'], 4) . 's');

    $t3 = microtime(true);
    [$openQtyMapAll, $openValMapAll] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $allKeysUnfiltered);
    $timings[] = ['label' => 'Q3-BatchOpenings', 'seconds' => microtime(true) - $t3];
    log_message('info', "$logTag Q3-BatchOpenings => " . count($openQtyMapAll) . " entries | " . round(end($timings)['seconds'], 4) . 's');

    $t4 = microtime(true);
    $unitNameMapAll = $this->loadUnitNames($mysql, $allKeysFiltered);
    $itemNameMapAll = $this->loadItemNames($mysql, $allKeysFiltered);
    $itemHsnMapAll  = $this->loadItemHsn($cmpId, $allKeysFiltered);
    $timings[] = ['label' => 'Q4-Names+HSN', 'seconds' => microtime(true) - $t4];
    log_message('info', "$logTag Q4-Names+HSN => " . round(end($timings)['seconds'], 4) . 's');

    $ioAggAll = [];
    if ($showInwards || $showOutwards) {
        $t5 = microtime(true);
        $ioAggAll = $this->loadInOutAggregates($mysql, $cmpId, $boId, $fyStart, $asOfStr, $allKeysFiltered, $mcId);
        $timings[] = ['label' => 'Q5-InOutAgg', 'seconds' => microtime(true) - $t5];
        log_message('info', "$logTag Q5-InOutAgg => " . count($ioAggAll) . " entries | " . round(end($timings)['seconds'], 4) . 's');
    }

    $t6 = microtime(true);
    $txnMapAll = $this->fetchTxnsBatch($mysql, $cmpId, $boId, $allKeysUnfiltered, $fyStart, $asOfStr, $mcId);
    $timings[] = ['label' => 'Q6-BatchTxns', 'seconds' => microtime(true) - $t6];
    log_message('info', "$logTag Q6-BatchTxns => " . count($txnMapAll) . " item groups | " . round(end($timings)['seconds'], 4) . 's');

    $t7 = microtime(true);

    $valuationCache = [];
    $totalValuation = 0.0;

    // NEW: opening totals
    $totalOpeningValuation = 0.0;

    foreach ($allKeysUnfiltered as $key) {
        $opQty = (float)($openQtyMapAll[$key] ?? 0.0);
        $opVal = (float)($openValMapAll[$key] ?? 0.0);
        $txns  = $txnMapAll[$key] ?? [];

        $result = $this->computeValuation($opQty, $opVal, $txns, true);
        $valuationCache[$key] = $result;
        $totalValuation += $result['running_value'];

        // NEW
        $totalOpeningValuation += parseAmount($opVal);
    }

    $timings[] = ['label' => 'Compute-AllValuation', 'seconds' => microtime(true) - $t7];
    log_message('info', "$logTag Compute-AllValuation => " . count($valuationCache) . " items | totalVal=$totalValuation | totalOpening=$totalOpeningValuation | " . round(end($timings)['seconds'], 4) . 's');

    $t8 = microtime(true);

    $allRowsData = [];
    $nonZeroKeys = [];

    foreach ($allKeysFiltered as $key) {
        $val = $valuationCache[$key] ?? null;

        if ($val === null) {
            $opQty = (float)($openQtyMapAll[$key] ?? 0.0);
            $opVal = (float)($openValMapAll[$key] ?? 0.0);
            $txns  = $txnMapAll[$key] ?? [];
            $val   = $this->computeValuation($opQty, $opVal, $txns, true);
        }

        $method        = $resolveMethod($key);
        $running_qty   = $val['running_qty'];
        $running_value = $val['running_value'];
        $total_profit  = $val['total_profit'];

        $opQty = (float)($openQtyMapAll[$key] ?? 0.0);
        $opVal = (float)($openValMapAll[$key] ?? 0.0);

        $io = ['in_q' => 0, 'in_a' => 0, 'out_q' => 0, 'out_a' => 0];
        if ($showInwards || $showOutwards) {
            $io = $ioAggAll[$key] ?? $io;
        }

        $items_key    = explode("_", $key);
        $profit_value = (float)parseAmount($total_profit);

        $profit_display = '';
        if (abs($profit_value) > 0.01) {
            if ($profit_value > 0) {
                $profit_display = number_format(abs($profit_value), 2) . ' Profit';
            } else {
                $profit_display = number_format(abs($profit_value), 2) . ' Loss';
            }
        } else {
            $profit_display = '0.00';
        }

        $closingQty   = (float)($running_qty);
        $closingValue = parseAmount($running_value);

        $isZeroRecord = (abs($closingQty) < 0.001 && abs($closingValue) < 0.01);

        $rowData = [
            'itm_id_unit_id'     => $key,
            'item_id'            => $items_key[0] ?? '',
            'unit_id'            => $items_key[1] ?? '',
            'mc_id'              => $mcId,
            'val'                => $method,

            'item_name'          => $itemNameMapAll[$key] ?? '',
            'Itemhsn'            => $itemHsnMapAll[$key]  ?? '',
            'unit_name'          => $unitNameMapAll[$key] ?? '',

            'inward_qty'         => $showInwards  ? (float)($io['in_q'])     : 0,
            'inward_amount'      => $showInwards  ? parseAmount($io['in_a'])  : 0,

            'outward_qty'        => $showOutwards ? (float)($io['out_q'])     : 0,
            'outward_amount'     => $showOutwards ? parseAmount($io['out_a']) : 0,

            'op_item_qty'        => $showOpening  ? (float)($opQty)           : 0,
            'op_item_value'      => $showOpening  ? parseAmount($opVal)       : 0,

            'item_qty_avail'     => $closingQty,
            'item_value_avail'   => $closingValue,

            'item_qty_packed'    => 0,
            'item_value_packed'  => 0,

            'item_qty_obse'      => 0,
            'item_value_obse'    => 0,

            'item_qty_intras'    => 0,
            'item_value_intras'  => 0,

            'method'             => $method,
            'profit'             => $profit_display,
            'profit_raw'         => $profit_value,
            'is_zero_record'     => $isZeroRecord,
        ];

        $allRowsData[$key] = $rowData;

        if (!$isZeroRecord) {
            $nonZeroKeys[] = $key;
        }
    }

    $timings[] = ['label' => 'BuildFilteredRows', 'seconds' => microtime(true) - $t8];
    log_message('info', "$logTag BuildFilteredRows => " . count($allRowsData) . " rows | " . round(end($timings)['seconds'], 4) . 's');

    $finalKeys     = $includeNilBalances ? $allKeysFiltered : $nonZeroKeys;
    $total_records = count($finalKeys);

    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset >= $total_records && $total_records > 0) {
        $pq_curPage = max(1, (int)ceil($total_records / $pq_rPP));
        $offset     = ($pq_rPP * ($pq_curPage - 1));
    }
    $pageKeys = array_slice($finalKeys, $offset, $pq_rPP);

    $rows = [];
    foreach ($pageKeys as $key) {
        if (isset($allRowsData[$key])) {
            $rowData = $allRowsData[$key];
            unset($rowData['is_zero_record']);
            $rows[] = $rowData;
        }
    }

    $adjustedValuation = 0.0;
    $adjustedOpeningValuation = 0.0; // NEW

    if ($hasFilter) {
        foreach ($finalKeys as $key) {
            if (isset($allRowsData[$key])) {
                $adjustedValuation += $allRowsData[$key]['item_value_avail'];
                $adjustedOpeningValuation += $allRowsData[$key]['op_item_value']; // NEW
            }
        }
    }

    $displayMethod = $valReq;
    if ($displayMethod === '' || $displayMethod === 'AUTO') {
        $displayMethod = 'DEFAULT';
    }

    $zeroRecordsCount = count($allKeysFiltered) - count($nonZeroKeys);

    $fn_total     = microtime(true) - $fn_start;
    $summaryLines = ["$logTag ── TIMING SUMMARY ──"];
    foreach ($timings as $t) {
        $pct = ($fn_total > 0) ? round(($t['seconds'] / $fn_total) * 100, 1) : 0;
        $summaryLines[] = sprintf("   %-25s %8.4fs   (%5.1f%%)", $t['label'], $t['seconds'], $pct);
    }
    $summaryLines[] = sprintf("   %-25s %8.4fs   (100%%)", 'TOTAL', $fn_total);
    $summaryLines[] = "$logTag ── END ──";
    log_message('info', implode("\n", $summaryLines));

    $resp = [
        'curPage'           => $pq_curPage,
        'totalRecords'      => $total_records,
        'data'              => $rows,
        'valuationSummary'  => [
            'method'                    => $displayMethod,
            'total_valuation'           => parseAmount($totalValuation),
            'total_opening_valuation'   => parseAmount($totalOpeningValuation),   // NEW
            'adjusted_valuation'        => parseAmount($adjustedValuation),
            'adjusted_opening_valuation'=> parseAmount($adjustedOpeningValuation), // NEW
            'has_filter'                => $hasFilter,
            'zero_records_excluded'     => $includeNilBalances ? 0 : $zeroRecordsCount,
            'include_nil_balances'      => $includeNilBalances,
        ],
        'dataFlags' => [
            'show_opening'  => $showOpening,
            'show_inwards'  => $showInwards,
            'show_outwards' => $showOutwards,
        ],
    ];

    return json_encode($resp);
}

    public function inventoryStatusPaged18032026($cmpId = null, $boId = null, $fyId = null, $input = null)
    {
		$fn_start = microtime(true);
        $timings  = [];
        $logTag   = '[InventoryStatusPaged]';
        log_message('info', "$logTag ── START ── cmpId=$cmpId boId=$boId fyId=$fyId");

        $mysql     = $this->db;
        $pg        = $this->externaldb->postgr_db();
        $reqposted = !empty($input) ? $input : $_POST;

        // ──────────────────────────────────────────────────────���───
        // PARSE INPUT
        // ──────────────────────────────────────────────────────────
        $asOfStr = $reqposted['to_date_ymd']
                   ?? date('Y-m-d', strtotime($reqposted['to_date'] ?? date('Y-m-d')));
        if (!$asOfStr) {
            return json_encode(['ok' => false, 'error' => 'as_of required']);
        }

        $asOf = new \DateTimeImmutable($asOfStr);

        $valReq = strtoupper(trim($reqposted['val_id'] ?? ''));
        if ($valReq === '' || $valReq === '0') $valReq = 'AVG';
        if (!in_array($valReq, ['AUTO', 'FIFO', 'LIFO', 'AVG'], true)) $valReq = 'AVG';

        $allowNeg = ((int)($reqposted['allowNegative'] ?? 1)) === 1;

        $itemId = $reqposted['item_id'] ?? '';
        $unitId = $reqposted['unit_id'] ?? '';
        $mcId   = $reqposted['mc_id']   ?? '';

        $filterText  = trim((string)($reqposted['filter_text']  ?? ''));
        $filterField = strtolower($reqposted['filter_field'] ?? '');
        $filterOp    = strtolower($reqposted['filter_op']    ?? 'contains');
        if (!in_array($filterField, ['item', 'unit', 'item_unit'], true)) $filterField = '';
        if (!in_array($filterOp, ['contains', 'starts_with', 'equals'], true)) $filterOp = 'contains';

        $includeNilBalances = ((int)($reqposted['nill']          ?? 0)) === 1;
        $showOpening        = ((int)($reqposted['show_opening']  ?? 0)) === 1;
        $showInwards        = ((int)($reqposted['show_inwards']  ?? 0)) === 1;
        $showOutwards       = ((int)($reqposted['show_outwards'] ?? 0)) === 1;

        $hasFilter = !empty($filterText) || !empty($itemId) || !empty($unitId) || !empty($mcId);

        $pq_curPage = max((int)($reqposted['pq_curpage'] ?? 1), 1);
        $pq_rPP     = max(min((int)($reqposted['pq_rpp'] ?? 100), 100), 1);

        $fyStart = $this->fyStartFor($asOf)->format('Y-m-d');
        $fyEnd   = $this->fyEndFor($asOf)->format('Y-m-d');

        // ───────────────────��──────────────────────────────────────
        // Q1: Load valuation method map
        // ──────────────────────────────────────────────────────────
        $t1 = microtime(true);
        $methodMap = $this->loadItemMethodMap($mysql, $cmpId, $boId, $fyId);
        $resolveMethod = function (string $key) use ($valReq, $methodMap): string {
            if ($valReq !== 'AUTO' && $valReq !== '') return $valReq;
            $m = $methodMap[$key] ?? 'AVG';
            if (is_numeric($m)) {
                return match ((int)$m) { 1 => 'FIFO', 2 => 'LIFO', 3 => 'AVG', default => 'AVG' };
            }
            return strtoupper($m);
        };
        $timings[] = ['label' => 'Q1-MethodMap', 'seconds' => microtime(true) - $t1];
        log_message('info', "$logTag Q1-MethodMap => " . count($methodMap) . " entries | " . round(end($timings)['seconds'], 4) . 's');

        // ──────────────────────────────────────────────────────────
        // Q2: Find item keys — unfiltered + filtered
        // ──────────────────────────────────────────────────────────
        $t2 = microtime(true);
        $allKeysUnfiltered = $this->findItemKeysForReport(
            $mysql, $cmpId, $boId, $fyId,
            $fyStart, $asOfStr,
            '', '', '',
            '', '', ''
        );
        $timings[] = ['label' => 'Q2a-UnfilteredKeys', 'seconds' => microtime(true) - $t2];
        log_message('info', "$logTag Q2a-UnfilteredKeys => " . count($allKeysUnfiltered) . " keys | " . round(end($timings)['seconds'], 4) . 's');

        $t2b = microtime(true);
        $allKeysFiltered = $this->findItemKeysForReport(
            $mysql, $cmpId, $boId, $fyId,
            $fyStart, $asOfStr,
            $itemId, $unitId, $mcId,
            $filterField, $filterOp, $filterText
        );
        $timings[] = ['label' => 'Q2b-FilteredKeys', 'seconds' => microtime(true) - $t2b];
        log_message('info', "$logTag Q2b-FilteredKeys => " . count($allKeysFiltered) . " keys | " . round(end($timings)['seconds'], 4) . 's');

        // ──────────────────────────────────────────────────────────
        // Q3: Batch load openings for ALL unfiltered keys
        //     (covers both filtered rows + total valuation)
        // ──────────────────────────────────────────────────────────
        $t3 = microtime(true);
        [$openQtyMapAll, $openValMapAll] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $allKeysUnfiltered);
        $timings[] = ['label' => 'Q3-BatchOpenings', 'seconds' => microtime(true) - $t3];
        log_message('info', "$logTag Q3-BatchOpenings => " . count($openQtyMapAll) . " entries | " . round(end($timings)['seconds'], 4) . 's');

        // ────────────────────────────────────────────���─────────────
        // Q4: Batch load names + HSN for filtered keys
        // ──────────────────────────────────────────────────────────
        $t4 = microtime(true);
        $unitNameMapAll = $this->loadUnitNames($mysql, $allKeysFiltered);
        $itemNameMapAll = $this->loadItemNames($mysql, $allKeysFiltered);
        $itemHsnMapAll  = $this->loadItemHsn($cmpId, $allKeysFiltered);
        $timings[] = ['label' => 'Q4-Names+HSN', 'seconds' => microtime(true) - $t4];
        log_message('info', "$logTag Q4-Names+HSN => " . round(end($timings)['seconds'], 4) . 's');

        // ──────────────────────────────────────────────────────────
        // Q5: Inward/Outward aggregates (conditional)
        // ──────────────────────────────────────────────────────────
        $ioAggAll = [];
        if ($showInwards || $showOutwards) {
            $t5 = microtime(true);
            $ioAggAll = $this->loadInOutAggregates($mysql, $cmpId, $boId, $fyStart, $asOfStr, $allKeysFiltered, $mcId);
            $timings[] = ['label' => 'Q5-InOutAgg', 'seconds' => microtime(true) - $t5];
            log_message('info', "$logTag Q5-InOutAgg => " . count($ioAggAll) . " entries | " . round(end($timings)['seconds'], 4) . 's');
        }

        // ──────────────────────────────────────────────────────────
        // Q6: Batch load ALL transactions (unfiltered keys)
        //     ONE query replaces N individual queries
        // ──────────────────────────────────────────────────────────
        $t6 = microtime(true);
        $txnMapAll = $this->fetchTxnsBatch($mysql, $cmpId, $boId, $allKeysUnfiltered, $fyStart, $asOfStr, $mcId);
        $timings[] = ['label' => 'Q6-BatchTxns', 'seconds' => microtime(true) - $t6];
        log_message('info', "$logTag Q6-BatchTxns => " . count($txnMapAll) . " item groups | " . round(end($timings)['seconds'], 4) . 's');

        // ──────────────────────────────────────────────────────────
        // COMPUTE: Valuation for ALL unfiltered keys (single pass)
        //   - Stores results so filtered rows + total valuation
        //     are both covered without re-querying
        // ──────────────────────────────────────────────────────────
        $t7 = microtime(true);

        // Cache: key => ['running_qty', 'running_value', 'running_avg', 'total_profit']
        $valuationCache = [];
        $totalValuation = 0.0;

        foreach ($allKeysUnfiltered as $key) {
            $opQty = (float)($openQtyMapAll[$key] ?? 0.0);
            $opVal = (float)($openValMapAll[$key] ?? 0.0);
            $txns  = $txnMapAll[$key] ?? [];

            $result = $this->computeValuation($opQty, $opVal, $txns, true);
            $valuationCache[$key] = $result;
            $totalValuation += $result['running_value'];
        }

        $timings[] = ['label' => 'Compute-AllValuation', 'seconds' => microtime(true) - $t7];
        log_message('info', "$logTag Compute-AllValuation => " . count($valuationCache) . " items | totalVal=$totalValuation | " . round(end($timings)['seconds'], 4) . 's');

        // ──────────────────────────────────────────────────────────
        // BUILD ROWS: Filtered keys only (using cached valuation)
        // ──────────────────────────────────────────────────────────
        $t8 = microtime(true);

        $allRowsData = [];
        $nonZeroKeys = [];

        foreach ($allKeysFiltered as $key) {
            // Use cached valuation (already computed above)
            $val = $valuationCache[$key] ?? null;

            if ($val === null) {
                // Edge case: filtered key not in unfiltered set
                // (shouldn't happen, but safety net)
                $opQty = (float)($openQtyMapAll[$key] ?? 0.0);
                $opVal = (float)($openValMapAll[$key] ?? 0.0);
                $txns  = $txnMapAll[$key] ?? [];
                $val   = $this->computeValuation($opQty, $opVal, $txns, true);
            }

            $method       = $resolveMethod($key);
            $running_qty  = $val['running_qty'];
            $running_value = $val['running_value'];
            $total_profit = $val['total_profit'];

            $opQty = (float)($openQtyMapAll[$key] ?? 0.0);
            $opVal = (float)($openValMapAll[$key] ?? 0.0);

            $io = ['in_q' => 0, 'in_a' => 0, 'out_q' => 0, 'out_a' => 0];
            if ($showInwards || $showOutwards) {
                $io = $ioAggAll[$key] ?? $io;
            }

            $items_key    = explode("_", $key);
            $profit_value = (float)parseAmount($total_profit);

            $profit_display = '';
            if (abs($profit_value) > 0.01) {
                if ($profit_value > 0) {
                    $profit_display = number_format(abs($profit_value), 2) . ' Profit';
                } else {
                    $profit_display = number_format(abs($profit_value), 2) . ' Loss';
                }
            } else {
                $profit_display = '0.00';
            }

            $closingQty   = (float)($running_qty);
            $closingValue = parseAmount($running_value);

            $isZeroRecord = (abs($closingQty) < 0.001 && abs($closingValue) < 0.01);

            $rowData = [
                'itm_id_unit_id'     => $key,
                'item_id'            => $items_key[0] ?? '',
                'unit_id'            => $items_key[1] ?? '',
                'mc_id'              => $mcId,
                'val'                => $method,

                'item_name'          => $itemNameMapAll[$key] ?? '',
                'Itemhsn'            => $itemHsnMapAll[$key]  ?? '',
                'unit_name'          => $unitNameMapAll[$key] ?? '',

                'inward_qty'         => $showInwards  ? (float)($io['in_q'])    : 0,
                'inward_amount'      => $showInwards  ? parseAmount($io['in_a']) : 0,

                'outward_qty'        => $showOutwards ? (float)($io['out_q'])    : 0,
                'outward_amount'     => $showOutwards ? parseAmount($io['out_a']) : 0,

                'op_item_qty'        => $showOpening  ? (float)($opQty)          : 0,
                'op_item_value'      => $showOpening  ? parseAmount($opVal)      : 0,

                'item_qty_avail'     => $closingQty,
                'item_value_avail'   => $closingValue,

                'item_qty_packed'    => 0,
                'item_value_packed'  => 0,

                'item_qty_obse'      => 0,
                'item_value_obse'    => 0,

                'item_qty_intras'    => 0,
                'item_value_intras'  => 0,

                'method'             => $method,
                'profit'             => $profit_display,
                'profit_raw'         => $profit_value,
                'is_zero_record'     => $isZeroRecord,
            ];

            $allRowsData[$key] = $rowData;

            if (!$isZeroRecord) {
                $nonZeroKeys[] = $key;
            }
        }

        $timings[] = ['label' => 'BuildFilteredRows', 'seconds' => microtime(true) - $t8];
        log_message('info', "$logTag BuildFilteredRows => " . count($allRowsData) . " rows | " . round(end($timings)['seconds'], 4) . 's');

        // ──────────────────────────────────────────────────────────
        // PAGINATE
        // ──────────────────────────────────────────────────────────
        $finalKeys     = $includeNilBalances ? $allKeysFiltered : $nonZeroKeys;
        $total_records = count($finalKeys);

        $offset = ($pq_rPP * ($pq_curPage - 1));
        if ($offset >= $total_records && $total_records > 0) {
            $pq_curPage = max(1, (int)ceil($total_records / $pq_rPP));
            $offset     = ($pq_rPP * ($pq_curPage - 1));
        }
        $pageKeys = array_slice($finalKeys, $offset, $pq_rPP);

        $rows = [];
        foreach ($pageKeys as $key) {
            if (isset($allRowsData[$key])) {
                $rowData = $allRowsData[$key];
                unset($rowData['is_zero_record']);
                $rows[] = $rowData;
            }
        }

        // ──────────────────────────────────────────────────────────
        // ADJUSTED VALUATION (filter-specific sum)
        // ──────────────────────────────────────────────────────────
        $adjustedValuation = 0.0;
        if ($hasFilter) {
            foreach ($finalKeys as $key) {
                if (isset($allRowsData[$key])) {
                    $adjustedValuation += $allRowsData[$key]['item_value_avail'];
                }
            }
        }

        $displayMethod = $valReq;
        if ($displayMethod === '' || $displayMethod === 'AUTO') {
            $displayMethod = 'DEFAULT';
        }

        $zeroRecordsCount = count($allKeysFiltered) - count($nonZeroKeys);

        // ──────────────────────────────────────────────────────────
        // TIMING SUMMARY
        // ──────────────────────────────────────────────────────────
        $fn_total     = microtime(true) - $fn_start;
        $summaryLines = ["$logTag ── TIMING SUMMARY ──"];
        foreach ($timings as $t) {
            $pct = ($fn_total > 0) ? round(($t['seconds'] / $fn_total) * 100, 1) : 0;
            $summaryLines[] = sprintf("   %-25s %8.4fs   (%5.1f%%)", $t['label'], $t['seconds'], $pct);
        }
        $summaryLines[] = sprintf("   %-25s %8.4fs   (100%%)", 'TOTAL', $fn_total);
        $summaryLines[] = "$logTag ── END ──";
        log_message('info', implode("\n", $summaryLines));

        // ──────────────────────────────────────────────────────────
        // RETURN (identical output format)
        // ──────────────────────────────────────────────────────────
        $resp = [
            'curPage'           => $pq_curPage,
            'totalRecords'      => $total_records,
            'data'              => $rows,
            'valuationSummary'  => [
                'method'                => $displayMethod,
                'total_valuation'       => parseAmount($totalValuation),
                'adjusted_valuation'    => parseAmount($adjustedValuation),
                'has_filter'            => $hasFilter,
                'zero_records_excluded' => $includeNilBalances ? 0 : $zeroRecordsCount,
                'include_nil_balances'  => $includeNilBalances,
            ],
            'dataFlags' => [
                'show_opening'  => $showOpening,
                'show_inwards'  => $showInwards,
                'show_outwards' => $showOutwards,
            ],
        ];

        return json_encode($resp);
    }


    // ════════════════════════════════════════════════════════════════
    // loadItemHsn — unchanged
    // ════════════════════════════════════════════════════════════════
    private function loadItemHsn(int $cmpId, array $keys): array {
        if (empty($keys)) return [];

        $itemIds = [];
        foreach ($keys as $k) {
            $parts = explode('_', $k);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $itemIds[] = (int)$parts[0];
            }
        }
        $itemIds = array_values(array_unique($itemIds));
        if (empty($itemIds)) return [];

        $id2hsn = [];
        try {
            $builder = $this->db->table('itmmstdetn');
            $builder->select('itm_id, itm_hsn');
            $builder->where('cmp_id', $cmpId);
            $builder->whereIn('itm_id', $itemIds);
            $query = $builder->get();
            if ($query) {
                $rows = $query->getResultArray();
                foreach ($rows as $r) {
                    $id2hsn[$r['itm_id']] = $r['itm_hsn'] ?? '';
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'loadItemHsn error: ' . $e->getMessage());
        }

        $map = [];
        foreach ($keys as $k) {
            $parts = explode('_', $k);
            $map[$k] = (isset($parts[0]) && isset($id2hsn[$parts[0]])) ? $id2hsn[$parts[0]] : '';
        }
        return $map;
    }

    // ════════════════════════════════════════════════════════════════
    // calculateTotalValuation — now uses shared methods
    // ════════════════════════════════════════════════════════════════
    private function calculateTotalValuation($mysql, int $cmpId, int $boId, int $fyId,
                                             string $fyStart, string $asOfStr,
                                             array $keys, callable $resolveMethod, $mcId): float
    {
        if (empty($keys)) return 0.0;

        [$openQtyMap, $openValMap] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $keys);
        $txnMap = $this->fetchTxnsBatch($mysql, $cmpId, $boId, $keys, $fyStart, $asOfStr, $mcId);

        $totalValue = 0.0;
        foreach ($keys as $key) {
            $opQty = (float)($openQtyMap[$key] ?? 0.0);
            $opVal = (float)($openValMap[$key] ?? 0.0);
            $txns  = $txnMap[$key] ?? [];

            $result = $this->computeValuation($opQty, $opVal, $txns, false);
            $totalValue += $result['running_value'];
        }

        return $totalValue;
    }

    // ════════════════════════════════════════════════════════════════
    // fetchTxnsWithVoucherInfo — kept for backward compatibility
    // (other code outside this file may call it)
    // ════════════════════════════════════════════════════════════════
    private function fetchTxnsWithVoucherInfo($mysql, int $cmpId, int $boId, string $itmKey,
                                              string $fromDate, string $toDate, $mcId): array
    {
        $builder = $mysql->table('itemtxnmst t')
            ->select('
                t.itm_txn_date,
                t.itm_txn_dr_cr,
                t.itm_txn_qty,
                t.itm_txn_rate,
                t.itm_txn_amt,
                t.itm_txn_id,
                t.vch_txn_id,
                c.vch_type_id,
                vt.vch_name as voucher_type
            ')
            ->join('vchtxnconso c',  'c.vch_txn_id  = t.vch_txn_id', 'left')
            ->join('vchtypemst vt',  'vt.vch_type_id = c.vch_type_id', 'left')
            ->where('t.cmp_id',  $cmpId)
            ->where('t.hobo_id', $boId)
            ->where('t.itm_id_unit_id', $itmKey)
            ->where('t.itm_txn_date >=', $fromDate)
            ->where('t.itm_txn_date <=', $toDate);

        if (!empty($mcId)) {
            $builder->where('t.mat_cent_id', $mcId);
        }

        $builder->orderBy('t.itm_txn_date', 'ASC')
                ->orderBy('t.vch_txn_id',   'ASC')
                ->orderBy('t.itm_txn_id',   'ASC');

        return $builder->get()->getResultArray();
    }


    /* ======================== helpers ======================== */

    private function fyStartFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        $y = (int)$dt->format('Y');
        $m = (int)$dt->format('n');
        $fyY = ($m >= 4) ? $y : $y - 1;
        return new \DateTimeImmutable("$fyY-04-01");
    }

    private function fyEndFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        return $this->fyStartFor($dt)->modify('+1 year -1 day');
    }

    private function loadItemMethodMap($mysql, int $cmpId, int $boId, int $fyId): array {
        $rows = $mysql->table('itmoppyval')
            ->select('itm_id_unit_id, itm_val_method_id')
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->where('cmpfymastr_id', $fyId)
            ->get()->getResultArray();
        $m = [];
        foreach ($rows as $r) {
            $m[$r['itm_id_unit_id']] = ($r['itm_val_method_id'] ?? 'AVG');
        }
        return $m;
    }

private function findItemKeysForReport($mysql, int $cmpId, int $boId, int $fyId,
                                       string $fromDate, string $toDate,
                                       $itemId, $unitId, $mcId,
                                       string $filterField = '', string $filterOp = 'contains', string $filterText = '',
                                       int $isConsolidated = 0): array  // ← ADDED parameter
{
    $keys = [];

    // From openings
    $opQb = $mysql->table('itmoppybal')
        ->select('itm_id_unit_id')
        ->where('cmp_id', $cmpId)
        ->where('cmpfymastr_id', $fyId);
    // ✅ FIX: skip hobo_id when consolidated
    if ($isConsolidated === 0) {
        $opQb->where('hobo_id', $boId);
    }
    $op = $opQb->get()->getResultArray();
    foreach ($op as $r) $keys[$r['itm_id_unit_id']] = true;

    // From activity in FY window
    $b = $mysql->table('itemtxnmst')
        ->distinct()
        ->select('itm_id_unit_id')
        ->where('cmp_id', $cmpId)
        ->where('itm_txn_date >=', $fromDate)
        ->where('itm_txn_date <=', $toDate);
    // ✅ FIX: skip hobo_id when consolidated
    if ($isConsolidated === 0) {
        $b->where('hobo_id', $boId);
    }
    if (!empty($itemId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", 1)', $itemId);
    if (!empty($unitId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", -1)', $unitId);
    if (!empty($mcId))   $b->where('mat_cent_id', $mcId);
    $tx = $b->get()->getResultArray();
    foreach ($tx as $r) $keys[$r['itm_id_unit_id']] = true;

    // If both item_id & unit_id supplied but no openings/txns, still include that key
    if (!empty($itemId) && !empty($unitId)) {
        $key = $itemId . '_' . $unitId;
        if (!isset($keys[$key])) $keys[$key] = true;
    }

    $arr = array_keys($keys);
    sort($arr, SORT_STRING);

    // Apply advanced text filter on names (if provided) — unchanged
    if ($filterText !== '') {
        $itemNames = $this->loadItemNames($mysql, $arr);
        $unitNames = $this->loadUnitNames($mysql, $arr);

        $filtered = [];
        foreach ($arr as $k) {
            $itemName = $itemNames[$k] ?? '';
            $unitName = $unitNames[$k] ?? '';
            $match    = false;

            switch ($filterField) {
                case 'item':
                    $match = $this->matchesFilter($itemName, $filterOp, $filterText);
                    break;
                case 'unit':
                    $match = $this->matchesFilter($unitName, $filterOp, $filterText);
                    break;
                case 'item_unit':
                    $match = $this->matchesFilter($itemName . ' ' . $unitName, $filterOp, $filterText);
                    break;
                default:
                    $match = $this->matchesFilter($itemName, $filterOp, $filterText)
                          || $this->matchesFilter($unitName, $filterOp, $filterText);
                    break;
            }
            if ($match) $filtered[] = $k;
        }
        $arr = $filtered;
    }

    return $arr;
}
    private function findItemKeysForReport_11_04_2026($mysql, int $cmpId, int $boId, int $fyId,
                                           string $fromDate, string $toDate,
                                           $itemId, $unitId, $mcId,
                                           string $filterField = '', string $filterOp = 'contains', string $filterText = ''): array
    {
        $keys = [];

        // From openings
        $op = $mysql->table('itmoppybal')
            ->select('itm_id_unit_id')
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->where('cmpfymastr_id', $fyId)
            ->get()->getResultArray();
        foreach ($op as $r) $keys[$r['itm_id_unit_id']] = true;

        // From activity in FY window
        $b = $mysql->table('itemtxnmst')
            ->distinct()
            ->select('itm_id_unit_id')
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->where('itm_txn_date >=', $fromDate)
            ->where('itm_txn_date <=', $toDate);
        if (!empty($itemId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", 1)', $itemId);
        if (!empty($unitId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", -1)', $unitId);
        if (!empty($mcId))   $b->where('mat_cent_id', $mcId);
        $tx = $b->get()->getResultArray();
        foreach ($tx as $r) $keys[$r['itm_id_unit_id']] = true;

        // If both item_id & unit_id supplied but no openings/txns, still include that key
        if (!empty($itemId) && !empty($unitId)) {
            $key = $itemId . '_' . $unitId;
            if (!isset($keys[$key])) $keys[$key] = true;
        }

        $arr = array_keys($keys);
        sort($arr, SORT_STRING);

        // Apply advanced text filter on names (if provided)
        if ($filterText !== '') {
            $itemNames = $this->loadItemNames($mysql, $arr);
            $unitNames = $this->loadUnitNames($mysql, $arr);

            $filtered = [];
            foreach ($arr as $k) {
                $itemName = $itemNames[$k] ?? '';
                $unitName = $unitNames[$k] ?? '';
                $match = false;

                switch ($filterField) {
                    case 'item':
                        $match = $this->matchesFilter($itemName, $filterOp, $filterText);
                        break;
                    case 'unit':
                        $match = $this->matchesFilter($unitName, $filterOp, $filterText);
                        break;
                    case 'item_unit':
                        $match = $this->matchesFilter($itemName . ' ' . $unitName, $filterOp, $filterText);
                        break;
                    default:
                        $match = $this->matchesFilter($itemName, $filterOp, $filterText)
                              || $this->matchesFilter($unitName, $filterOp, $filterText);
                        break;
                }

                if ($match) {
                    $filtered[] = $k;
                }
            }
            $arr = $filtered;
        }

        return $arr;
    }

    private function loadOpenings($mysql, int $cmpId, int $boId, int $fyId, array $keys, int $isConsolidated = 0): array
{
    if (empty($keys)) return [[], []];

    $qb1 = $mysql->table('itmoppybal')
        ->select('itm_id_unit_id, COALESCE(SUM(itm_op_bal_qty),0) AS qty_sum')
        ->where('cmpfymastr_id', $fyId)
        ->where('cmp_id', $cmpId)
        ->whereIn('itm_id_unit_id', $keys)
        ->groupBy('itm_id_unit_id');
    // ✅ FIX: only filter by branch when NOT consolidated
    if ($isConsolidated === 0) {
        $qb1->where('hobo_id', $boId);
    }
    $qRows = $qb1->get()->getResultArray();
    SaveErrorLog("Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>" . $mysql->getlastquery());

    $qmap = [];
    foreach ($qRows as $r) {
        $qmap[$r['itm_id_unit_id']] = (float)$r['qty_sum'];
    }

    $qb2 = $mysql->table('itmoppyval')
        ->select('itm_id_unit_id, COALESCE(SUM(itm_op_val_amt),0) AS val_sum')
        ->where('cmpfymastr_id', $fyId)
        ->where('cmp_id', $cmpId)
        ->whereIn('itm_id_unit_id', $keys)
        ->groupBy('itm_id_unit_id');
    // ✅ FIX: only filter by branch when NOT consolidated
    if ($isConsolidated === 0) {
        $qb2->where('hobo_id', $boId);
    }
    $vRows = $qb2->get()->getResultArray();
    SaveErrorLog("Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>" . $mysql->getlastquery());

    $vmap = [];
    foreach ($vRows as $r) {
        $vmap[$r['itm_id_unit_id']] = (float)$r['val_sum'];
    }

    return [$qmap, $vmap];
}

    private function loadOpenings_11_04_2026($mysql, int $cmpId, int $boId, int $fyId, array $keys): array {
        if (empty($keys)) return [[], []];

        $qRows = $mysql->table('itmoppybal')
            ->select('itm_id_unit_id, COALESCE(SUM(itm_op_bal_qty),0) AS qty_sum')
            ->where('cmpfymastr_id', $fyId)
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->whereIn('itm_id_unit_id', $keys)
            ->groupBy('itm_id_unit_id')
            ->get()->getResultArray();
        SaveErrorLog("Sum opening QTY across ALL MCs for each itm_id_unit_id  <br>" . $mysql->getlastquery());
        $qmap = [];
        foreach ($qRows as $r) {
            $qmap[$r['itm_id_unit_id']] = (float)$r['qty_sum'];
        }

        $vRows = $mysql->table('itmoppyval')
            ->select('itm_id_unit_id, COALESCE(SUM(itm_op_val_amt),0) AS val_sum')
            ->where('cmpfymastr_id', $fyId)
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->whereIn('itm_id_unit_id', $keys)
            ->groupBy('itm_id_unit_id')
            ->get()->getResultArray();
        SaveErrorLog("Sum opening VALUE across ALL MCs for each itm_id_unit_id <br>" . $mysql->getlastquery());
        $vmap = [];
        foreach ($vRows as $r) {
            $vmap[$r['itm_id_unit_id']] = (float)$r['val_sum'];
        }

        return [$qmap, $vmap];
    }

    private function loadUnitNames($mysql, array $keys): array {
        if (empty($keys)) return [];

        $unitIds = [];
        foreach ($keys as $k) {
            $parts = explode('_', $k);
            if (isset($parts[1])) {
                $unitIds[] = $parts[1];
            }
        }
        $unitIds = array_values(
            array_unique(
                array_filter($unitIds, function ($id) {
                    return is_numeric($id) && $id > 0;
                })
            )
        );

        if (empty($unitIds)) return [];

        $rows = $mysql->table('itmunitmst')
            ->select('itm_unit_id, itm_unit_name')
            ->whereIn('itm_unit_id', $unitIds)
            ->get()
            ->getResultArray();

        $id2name = [];
        foreach ($rows as $r) {
            $id2name[$r['itm_unit_id']] = $r['itm_unit_name'];
        }

        $map = [];
        foreach ($keys as $k) {
            $parts = explode('_', $k);
            $map[$k] = (isset($parts[1]) && isset($id2name[$parts[1]])) ? $id2name[$parts[1]] : '';
        }
        return $map;
    }

    private function loadItemNames($mysql, array $keys): array {
        if (empty($keys)) return [];

        $itemIds = [];
        foreach ($keys as $k) {
            $parts = explode('_', $k);
            if (isset($parts[0])) {
                $itemIds[] = $parts[0];
            }
        }
        $itemIds = array_values(array_unique($itemIds));
        if (empty($itemIds)) return [];

        $rows = $mysql->table('itemmaster')
            ->select('itm_id, itm_name')
            ->whereIn('itm_id', $itemIds)
            ->get()
            ->getResultArray();

        $id2name = [];
        foreach ($rows as $r) {
            $id2name[$r['itm_id']] = $r['itm_name'];
        }

        $map = [];
        foreach ($keys as $k) {
            $parts = explode('_', $k);
            $map[$k] = (isset($parts[0]) && isset($id2name[$parts[0]])) ? $id2name[$parts[0]] : '';
        }
        return $map;
    }

    private function loadInOutAggregates($mysql, int $cmpId, int $boId, string $fromDate, string $toDate, array $keys, $mcId): array {
        if (empty($keys)) return [];
        $b = $mysql->table('itemtxnmst')
            ->select('itm_id_unit_id,
                      SUM(CASE WHEN itm_txn_dr_cr=1 THEN itm_txn_qty ELSE 0 END) AS in_q,
                      SUM(CASE WHEN itm_txn_dr_cr=1 THEN itm_txn_amt ELSE 0 END) AS in_a,
                      SUM(CASE WHEN itm_txn_dr_cr=2 THEN itm_txn_qty ELSE 0 END) AS out_q,
                      SUM(CASE WHEN itm_txn_dr_cr=2 THEN itm_txn_amt ELSE 0 END) AS out_a')
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->where('itm_txn_date >=', $fromDate)
            ->where('itm_txn_date <=', $toDate)
            ->whereIn('itm_id_unit_id', $keys)
            ->groupBy('itm_id_unit_id');
        if (!empty($mcId)) $b->where('mat_cent_id', $mcId);
        $rows = $b->get()->getResultArray();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['itm_id_unit_id']] = [
                'in_q'  => (float)$r['in_q'],
                'in_a'  => (float)$r['in_a'],
                'out_q' => (float)$r['out_q'],
                'out_a' => (float)$r['out_a'],
            ];
        }
        return $map;
    }

    private function methodId(string $method): string {
        $m = strtoupper(trim($method));
        return in_array($m, ['AVG', 'FIFO', 'LIFO'], true) ? $m : 'AVG';
    }

    private function selectSeedWithinFY($pg, int $cmpId, string $itmKey, string $method, string $fyStart, string $asOf): array {
        $mid = $this->methodId($method);

        $dirty = $pg->query(
            "SELECT MIN(itm_snapshot_date) AS first_dirty
               FROM itmsnapsht
              WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=?
                AND itm_snapshot_date BETWEEN ? AND ? AND itm_snapshot_is_dirty=TRUE",
            [$cmpId, $itmKey, $mid, $fyStart, $asOf]
        )->getFirstRow('array');
        $firstDirty = $dirty && $dirty['first_dirty'] ? $dirty['first_dirty'] : null;

        $seedUpper = $asOf;
        if ($firstDirty) {
            $seedUpper = (new \DateTimeImmutable($firstDirty))->modify('-1 day')->format('Y-m-d');
            if ($seedUpper < $fyStart) $seedUpper = $fyStart;
        }

        $seed = null;
        if ($seedUpper >= $fyStart) {
            $seed = $pg->query(
                "SELECT itm_snapshot_date, itm_balls_snapshot
                   FROM itmsnapsht
                  WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=?
                    AND itm_snapshot_is_dirty=FALSE
                    AND itm_snapshot_date BETWEEN ? AND ?
               ORDER BY itm_snapshot_date DESC
                  LIMIT 1",
                [$cmpId, $itmKey, $mid, $fyStart, $seedUpper]
            )->getFirstRow('array');
        }

        $seedDate   = $seed['itm_snapshot_date'] ?? null;
        $payloadArr = $seed ? json_decode($seed['itm_balls_snapshot'], true) : null;

        $sumPnL = 0.0;
        if ($seedDate) {
            $r = $pg->query(
                "SELECT COALESCE(SUM(itm_snapshot_pnl),0) AS s
                   FROM itmsnapsht
                  WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=?
                    AND itm_snapshot_is_dirty=FALSE
                    AND itm_snapshot_date BETWEEN ? AND ?",
                [$cmpId, $itmKey, $mid, $fyStart, $seedDate]
            )->getFirstRow('array');
            $sumPnL = (float)($r['s'] ?? 0.0);
        }

        return [$seedDate, $payloadArr, $sumPnL];
    }

    private function fetchTxnsForReport($mysql, int $cmpId, int $boId, string $itmKey, string $fromDate, string $toDate, $mcId): array {
        $b = $mysql->table('itemtxnmst t')
            ->select('t.itm_txn_date, t.itm_txn_dr_cr, t.itm_txn_qty, t.itm_txn_rate, t.itm_txn_amt, t.itm_txn_id')
            ->where('t.cmp_id', $cmpId)
            ->where('t.hobo_id', $boId)
            ->where('t.itm_id_unit_id', $itmKey)
            ->where('t.itm_txn_date >=', $fromDate)
            ->where('t.itm_txn_date <=', $toDate)
            ->orderBy('t.itm_txn_date', 'ASC')
            ->orderBy('t.itm_txn_id', 'ASC');
        if (!empty($mcId)) $b->where('t.mat_cent_id', $mcId);
        return $b->get()->getResultArray();
    }
}