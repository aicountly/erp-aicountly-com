<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Libraries\externaldb;

class ItemSummaryModel extends Model
{
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
	    $this->contactaic_db = $this->externaldb->contactaic_db();
	   $this->univaictly    =  $this->externaldb->univaictly_db();
    } 
	
    /**
     * Entry point for monthly summary.
     *
     * @param array $p {
     *   @type int       cmp_id
     *   @type int       cmpfymasr_id
     *   @type string    fy_start_ymd  // 'Y-m-d'
     *   @type string    fy_end_ymd    // 'Y-m-d'
     *   @type string    itm_id_unit_id
     *   @type int|null  mat_cent_id   // null = All MC
     *   @type int|null  hobo_id       // null = All branches
     *   @type int       itm_val_method_id  // AVG,FIFO,LIFO
     *   @type string    uom           // for grid display
     * }
     * @return array { rows: [], totals: {} }
     */
	 
	 public function getMonthlySummary($p,$compId=null,$boId=null,$fyId=null,$reportData=null)
{
	if($reportData){
		$p = [
            'cmp_id'            => $compId,
            'cmpfymasr_id'      => $fyId,
            'fy_start_ymd'      => date('Y-m-d',strtotime($reportData['from_date'])),
            'fy_end_ymd'        => date('Y-m-d',strtotime($reportData['to_date'])),
            'itm_id_unit_id'    => $reportData['item_id'].'_'.$reportData['unit_id'],
			'itm_id'            => $reportData['item_id'],
            'itm_val_method_id' => $reportData['val_id'],
            'mat_cent_id'       => $reportData['mc_id'] ? (int)$reportData['mc_id'] : 0,
            'hobo_id'           => $boId,
            'uom'               => $reportData['unit_id'],
        ];		
	}
    // Validate required parameters
    foreach (['cmp_id','cmpfymasr_id','fy_start_ymd','fy_end_ymd','itm_id_unit_id','itm_val_method_id'] as $k) {
        if (!isset($p[$k]) || $p[$k]==='') {
            return ['rows'=>[], 'totals'=>[]];
        }
    }

    // Resolve FY bounds
    [$fyStart, $fyEnd] = $this->resolveFyBounds($p);
    
    // Build month list for the FY
    $months = $this->listMonths($fyStart, $fyEnd);
    if (count($months) < 12) {
        $anchor = $p['fy_start_ymd'] ?: $p['fy_end_ymd'];
        $bounds = $this->expandIndianFyFromDate($anchor);
        $months = $this->listMonths($bounds[0], $bounds[1]);
    }

    // Get opening balance (used for calculations only)
    $openingQty  = $this->getOpeningQty($p);
    $openingVal  = $this->getOpeningVal($p);
    $openingRate = ($openingQty > 0) ? ($openingVal / $openingQty) : 0.0;
    
    // Get unit name
    $uomName = $this->getUomNameByItmKey($p['itm_id_unit_id'], (int)$p['cmp_id']);
    
    // Get monthly transactions
    $p2 = $p; 
    $p2['fy_start_ymd'] = $fyStart; 
    $p2['fy_end_ymd'] = $fyEnd;
    $txnAggByMonth = $this->getMonthlyTxnAggregates($p2);
    
    // Initialize running totals with opening balance (backend calculation)
    $running_qty = $openingQty;
    $running_value = $openingVal;
    $running_avg = $openingRate;
    $cumulative_profit = 0.0;
    
    $rows = [];
    
    // Process each month
    foreach ($months as [$mStart, $mEnd, $label, $ym]) {
        // Get transactions for this month
        $dr = $txnAggByMonth[$ym]['dr'] ?? ['qty'=>0.0,'amt'=>0.0];
        $cr = $txnAggByMonth[$ym]['cr'] ?? ['qty'=>0.0,'amt'=>0.0];
        
        $month_profit = 0.0;
        
        // Check if this is a Stock Journal month (equal IN and OUT)
        $is_stock_journal_month = false;
        if ($dr['qty'] > 0 && $cr['qty'] > 0 && abs($dr['qty'] - $cr['qty']) < 0.001) {
            $is_stock_journal_month = true;
        }
        
        if ($is_stock_journal_month) {
            // Stock Journal: both IN and OUT in same month
            // Process OUT first (at current average)
            $cogs = $cr['qty'] * $running_avg;
            
            // Calculate loss/gain from stock replacement
            // Loss = New stock value - Old stock value (COGS)
            $stock_adjustment = $dr['amt'] - $cogs;
            $month_profit = -$stock_adjustment; // Negative if loss
            
            // Update running totals
            // Remove old stock at COGS
            $running_qty -= $cr['qty'];
            $running_value -= $cogs;
            
            // Add new stock at actual cost
            $running_qty += $dr['qty'];
            $running_value += $dr['amt'];
            
            // Recalculate average
            if ($running_qty > 0) {
                $running_avg = $running_value / $running_qty;
            }
        } else {
            // Normal month processing
            
            // Process OUTWARD (Credit) first
            if ($cr['qty'] > 0) {
                // Calculate COGS using current average
                $cogs = $cr['qty'] * $running_avg;
                
                // Profit = Sales value - COGS
                $month_profit = $cr['amt'] - $cogs;
                
                // Update running totals
                $running_qty -= $cr['qty'];
                $running_value -= $cogs;
            }
            
            // Process INWARD (Debit)
            if ($dr['qty'] > 0) {
                // Add new stock and recalculate average
                $new_total_value = $running_value + $dr['amt'];
                $new_total_qty = $running_qty + $dr['qty'];
                
                if ($new_total_qty > 0) {
                    $running_avg = $new_total_value / $new_total_qty;
                }
                
                $running_qty = $new_total_qty;
                $running_value = $new_total_value;
            }
        }
        
        $cumulative_profit += $month_profit;
        
        // Format profit display
        $profit_display = '';
        if (abs($month_profit) > 0.01) {
            if ($month_profit > 0) {
                $profit_display = $this->famt($month_profit);
            } else {
                $profit_display = '-' . $this->famt(abs($month_profit));
            }
        }
        
        // Create row for this month
        $rows[] = [
            'month'         => $label,
            'unit_name'     => $uomName,
            'item_id'       => $p['itm_id'] ?? '',
            'from_date'     => $fyStart,
            'to_date'       => $fyEnd,
            'unit_id'       => $p['uom'] ?? '',
            'mc_id'         => '',
            'mc_grp_id'     => '',
            'debit_qty'     => $this->blankIfZero($this->fqty($dr['qty'])),
            'debit_amount'  => $this->blankIfZero($this->famt($dr['amt'])),
            'credit_qty'    => $this->blankIfZero($this->fqty($cr['qty'])),
            'credit_amount' => $this->blankIfZero($this->famt($cr['amt'])),
            'balance_qty'   => $this->fqty($running_qty),      // Correct running qty
            'balance_value' => $this->famt($running_value),    // Correct running value  
            'profit'        => $profit_display
        ];
    }

    // Calculate totals
    $totals = [
        'debit_qty'     => $this->fqty(array_sum(array_map(fn($r)=> $this->parseNumeric($r['debit_qty']), $rows))),
        'debit_amount'  => $this->famt(array_sum(array_map(fn($r)=> $this->parseNumeric($r['debit_amount']), $rows))),
        'credit_qty'    => $this->fqty(array_sum(array_map(fn($r)=> $this->parseNumeric($r['credit_qty']), $rows))),
        'credit_amount' => $this->famt(array_sum(array_map(fn($r)=> $this->parseNumeric($r['credit_amount']), $rows))),
        'balance_qty'   => $this->fqty($running_qty),    // Final closing qty
        'balance_value' => $this->famt($running_value),  // Final closing value
        'profit'        => $this->famt($cumulative_profit),
    ];

    return ['rows'=>$rows, 'totals'=>$totals];
}

// Helper function to parse numeric values from potentially blank strings
public function parseNumeric($value) 
{
    if ($value === '' || $value === null) {
        return 0.0;
    }
    return (float)$value;
}
   public function getMonthlySummary_24_nov_2025(array $p): array
{
	// Resolve full FY bounds
[$fyStart, $fyEnd] = $this->resolveFyBounds($p);


    foreach (['cmp_id','cmpfymasr_id','fy_start_ymd','fy_end_ymd','itm_id_unit_id','itm_val_method_id'] as $k) {
        if (!isset($p[$k]) || $p[$k]==='') {
            return ['rows'=>[], 'totals'=>[]];
        }
    }

    // Build months, and if range is too short (e.g., single month), expand to full Apr→Mar FY
   // Full FY month list Apr..Mar
    $months = $this->listMonths($fyStart, $fyEnd); // [ [start,end,label,ym], ... ]
    if (count($months) < 12) {
        $anchor = $p['fy_start_ymd'] ?: $p['fy_end_ymd'];
        $bounds = $this->expandIndianFyFromDate($anchor);
        $months = $this->listMonths($bounds[0], $bounds[1]);
    }

    // Preload opening (optional – still used by valuation fallback)
    $openingQty  = $this->getOpeningQty($p);
    $openingVal  = $this->getOpeningVal($p);

    // Use the same FY bounds for txn aggregation
	$p2 = $p; $p2['fy_start_ymd'] = $fyStart; $p2['fy_end_ymd'] = $fyEnd;
	$txnAggByMonth = $this->getMonthlyTxnAggregates($p2);

    $rows = [];
    $prevCumPnl = 0.0;

    foreach ($months as [$mStart, $mEnd, $label, $ym]) {
        $dr = $txnAggByMonth[$ym]['dr'] ?? ['qty'=>0.0,'amt'=>0.0];
        $cr = $txnAggByMonth[$ym]['cr'] ?? ['qty'=>0.0,'amt'=>0.0];

        // Valuation-first: get BOTH closing value AND closing qty as of month-end
        $valPack  = $this->valuationAtDate($mEnd, $p); // returns cl_value + cl_qty (+ cum_pnl)
		$clValue  = (float)($valPack['cl_value'] ?? 0.0);
		$clQty    = (float)($valPack['cl_qty']   ?? 0.0);
        $cumPnL   = (float)($valPack['cum_pnl']  ?? 0.0);
        $monthPnL = $cumPnL - $prevCumPnl;
        $prevCumPnl = $cumPnL;
        $uomName = $this->getUomNameByItmKey($p['itm_id_unit_id'], (int)$p['cmp_id']);
        $rows[] = [
            'month'         => $label,                 // 'Apr, 2024' etc.
            'unit_name'     => $uomName,
			'item_id'       => $p['itm_id'],
			'from_date'     => $p['fy_start_ymd'],
			'to_date'       => $p['fy_end_ymd'],
			'unit_id'       => $p['uom'],
			'mc_id'         => '',
			'mc_grp_id'     => '',
            'debit_qty'     => $this->blankIfZero($this->fqty($dr['qty'])),
            'debit_amount'  => $this->blankIfZero($this->famt($dr['amt'])),
            'credit_qty'    => $this->blankIfZero($this->fqty($cr['qty'])),
            'credit_amount' => $this->blankIfZero($this->famt($cr['amt'])),
            'balance_qty'   => $this->fqty($clQty),       // <-- from valuation state
            'balance_value' => $this->famt($clValue),     // from valuation state
            'profit'        => $this->blankIfZero($this->famt($monthPnL)),
        ];
    }

    // Totals (safe-cast blanks)
    $nr   = $this->numRows($rows);
    $last = !empty($nr) ? $nr[array_key_last($nr)] : null;

    $totals = [
        'debit_qty'     => $this->fqty(array_sum(array_map(fn($r)=> (float)($r['debit_qty'] ?? 0), $nr))),
        'debit_amount'  => $this->famt(array_sum(array_map(fn($r)=> (float)($r['debit_amount'] ?? 0), $nr))),
        'credit_qty'    => $this->fqty(array_sum(array_map(fn($r)=> (float)($r['credit_qty'] ?? 0), $nr))),
        'credit_amount' => $this->famt(array_sum(array_map(fn($r)=> (float)($r['credit_amount'] ?? 0), $nr))),
        'balance_qty'   => $this->fqty($last['balance_qty']   ?? 0.0),
        'balance_value' => $this->famt($last['balance_value'] ?? 0.0),
        'profit'        => $this->famt(array_sum(array_map(fn($r)=> (float)($r['profit'] ?? 0), $nr))),
    ];

    return ['rows'=>$rows, 'totals'=>$totals];
}
    /* ----------------------------- Helpers ----------------------------- */

    /**
     * Full month list inclusive of FY start/end.
     * Returns: [ [start,end,label,ym], ... ] where label='M, Y' (e.g., 'Apr, 2024') and ym='YYYY-MM'.
     */
    private function listMonths(string $fyStart, string $fyEnd): array
{
    $out = [];
    $start = new \DateTimeImmutable($fyStart);
    $end   = new \DateTimeImmutable($fyEnd);

    $d = $start->modify('first day of this month')->setTime(0,0,0);
    $endMonth = $end->modify('last day of this month')->setTime(0,0,0);

    while ($d <= $endMonth) {
        $mStart = $d->format('Y-m-01');
        $mEnd   = $d->modify('last day of this month')->format('Y-m-d');
        $label  = $d->format('M, Y');  // Apr, 2024
        $ym     = $d->format('Y-m');   // 2024-04
        $out[]  = [$mStart, $mEnd, $label, $ym];
        $d      = $d->modify('+1 month');
    }
    return $out;
}


    private function getOpeningQty(array $p): float
    {
        // Sum opening qty from itmoppybal (MC-wise; optional filters)
        $qb = $this->db->table('itmoppybal b')
            ->select('COALESCE(SUM(b.itm_op_bal_qty),0) AS qty', false)
            ->where('b.cmp_id', (int)$p['cmp_id'])
            ->where('b.cmpfymastr_id', (int)$p['cmpfymasr_id'])
            ->where('b.itm_id_unit_id', $p['itm_id_unit_id']);

        if (!empty($p['mat_cent_id'])) { $qb->where('b.mat_cent_id', (int)$p['mat_cent_id']); }
        if (!empty($p['hobo_id']))     { $qb->where('b.hobo_id', (int)$p['hobo_id']); }

        $row = $qb->get()->getRowArray();
        return (float)($row['qty'] ?? 0.0);
    }

    private function getOpeningVal(array $p): float
    {
        // Sum opening value from itmoppyval (method-specific; MC-wise)
        $qb = $this->db->table('itmoppyval v')
            ->select('COALESCE(SUM(v.itm_op_val_amt),0) AS amt', false)
            ->where('v.cmp_id', (int)$p['cmp_id'])
            ->where('v.cmpfymastr_id', (int)$p['cmpfymasr_id'])
            ->where('v.itm_id_unit_id', $p['itm_id_unit_id'])
            ->where('v.itm_val_method_id', $p['itm_val_method_id']);

        if (!empty($p['mat_cent_id'])) { $qb->where('v.mat_cent_id', (int)$p['mat_cent_id']); }
        if (!empty($p['hobo_id']))     { $qb->where('v.hobo_id', (int)$p['hobo_id']); }

        $row = $qb->get()->getRowArray();
        return (float)($row['amt'] ?? 0.0);
    }

    /**
     * Monthly DR/CR aggregates from itemtxnmst (MC-wise, single item).
     * Returns: [ "YYYY-MM" => ['dr'=>['qty'=>..,'amt'=>..], 'cr'=>['qty'=>..,'amt'=>..] ], ... ]
     */
	 private function getMonthlyTxnAggregates(array $p): array
	{
		// Use TO_CHAR for PostgreSQL date formatting
		$qb = $this->db->table('itemtxnmst t')
			->select("
				TO_CHAR(t.itm_txn_date, 'YYYY-MM') AS ym,
				SUM(CASE WHEN t.itm_txn_dr_cr=1 THEN t.itm_txn_qty ELSE 0 END) AS dr_qty,
				SUM(CASE WHEN t.itm_txn_dr_cr=1 THEN t.itm_txn_amt ELSE 0 END) AS dr_amt,
				SUM(CASE WHEN t.itm_txn_dr_cr=2 THEN t.itm_txn_qty ELSE 0 END) AS cr_qty,
				SUM(CASE WHEN t.itm_txn_dr_cr=2 THEN t.itm_txn_amt ELSE 0 END) AS cr_amt
			", false)
			->where('t.cmp_id', (int)$p['cmp_id'])
			->where('t.itm_id_unit_id', $p['itm_id_unit_id'])
			->where('t.itm_txn_date >=', $p['fy_start_ymd'])
			->where('t.itm_txn_date <=', $p['fy_end_ymd'])
			->groupBy('ym')
			->orderBy('ym', 'ASC');

		// These conditional filters are fine as-is
		if (!empty($p['mat_cent_id'])) {
			$qb->where('t.mat_cent_id', (int)$p['mat_cent_id']);
		}
		if (!empty($p['hobo_id'])) {
			$qb->where('t.hobo_id', (int)$p['hobo_id']);
		}

		$rows = $qb->get()->getResultArray();
		$out = [];
		foreach ($rows as $r) {
			$ym = $r['ym'];
			$out[$ym] = [
				'dr' => ['qty' => (float)$r['dr_qty'], 'amt' => (float)$r['dr_amt']],
				'cr' => ['qty' => (float)$r['cr_qty'], 'amt' => (float)$r['cr_amt']],
			];
		}
		return $out;
	}
   

    /**
     * Snapshot-first valuation (closing value & cumulative PnL) as of $date_ymd.
     * If snapshot on/<= date is clean → use it; otherwise seed from last clean and
     * compute forward "on the fly" using your existing valuation functions.
     */
    private function valuationAtDate(string $date_ymd, array $p): array
    {
        // 1) Try clean snapshot
        $snap = $this->findCleanSnapshotOnOrBefore($date_ymd, $p);
        if ($snap && empty($snap['is_dirty'])) {
            if (!empty($snap['snp_date']) && $snap['snp_date'] === $date_ymd) {
                return [
                    'cl_value' => (float)($snap['cl_value'] ?? 0.0),
                    'cum_pnl'  => (float)($snap['pnl_upto'] ?? 0.0),
                ];
            }
            // Seed forward from snapshot to month-end
            return $this->computeForwardFromSeed($snap, $snap['snp_date'], $date_ymd, $p);
        }

        // 2) No clean snapshot → seed at FY opening and compute until date
        $seed = [
            'seed_type' => 'opening',
            'cl_qty'    => $this->getOpeningQty($p),
            'cl_value'  => $this->getOpeningVal($p),
            'pnl_upto'  => 0.0,
        ];
        return $this->computeForwardFromSeed($seed, $p['fy_start_ymd'], $date_ymd, $p);
    }
	
	/**
     * Choose the seed snapshot within FY, ignoring dirty ranges.
     * Returns [seedDate, payloadArray, sumPnLToSeed]
     */
	  private function methodId(string $method): string
{
    $m = strtoupper(trim($method));
    return in_array($m, ['AVG','FIFO','LIFO'], true) ? $m : 'AVG';
}
    private function selectSeedWithinFY($pg, int $cmpId, string $itmKey, string $method, string $fyStart, string $asOf): array
    {
       $mid = $this->methodId($method); 

        // first dirty date in window?
        $dirty = $pg->query(
            "SELECT MIN(itm_snapshot_date) AS first_dirty
               FROM itmsnapsht
              WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=?
                AND itm_snapshot_date BETWEEN ? AND ? AND itm_snapshot_is_dirty=TRUE",
            [$cmpId, $itmKey, $mid, $fyStart, $asOf]
        )->getFirstRow('array');
        $firstDirty = $dirty && $dirty['first_dirty'] ? $dirty['first_dirty'] : null;

        // latest clean snapshot no later than min(asOf, firstDirty-1), and not before FY start
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

        // sum P&L from FY start to seedDate (clean only)
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
	

    /* --------------------- ADAPTERS to your existing code --------------------- */

   private function findCleanSnapshotOnOrBefore(string $date_ymd, array $p): ?array
{
    $pg = $this->externaldb->postgr_db();

    $qb = $pg->table('itmsnapsht s')
        ->select('s.itm_snapshot_date, s.itm_snapshot_is_dirty, s.itm_snapshot_value, s.itm_snapshot_pnl, s.itm_balls_snapshot')
        ->where('s.cmp_id', (int)$p['cmp_id'])
        ->where('s.itm_id_unit_id', $p['itm_id_unit_id'])
        ->where('s.val_method_id', $p['itm_val_method_id'])
        ->where('s.itm_snapshot_date <=', $date_ymd)
        ->orderBy('s.itm_snapshot_date','DESC')
        ->limit(1);

    $row = $qb->get()->getRowArray();
    if (!$row) return null;

    // Derive qty from JSON
    $clQty = 0.0;
    if (!empty($row['itm_balls_snapshot'])) {
        $snap = json_decode($row['itm_balls_snapshot'], true);
        if (is_array($snap)) {
            if (array_key_exists('qty', $snap)) {
                $clQty = (float)$snap['qty'];
            } elseif (!empty($snap['balls']) && is_array($snap['balls'])) {
                foreach ($snap['balls'] as $b) { $clQty += (float)($b['qty'] ?? 0.0); }
            }
        }
    }

    return [
        'snp_date' => $row['itm_snapshot_date'],
        'is_dirty' => (int)($row['itm_snapshot_is_dirty'] ?? 0),
        'cl_value' => (float)($row['itm_snapshot_value'] ?? 0.0),
        'cl_qty'   => $clQty,
        'pnl_upto' => (float)($row['itm_snapshot_pnl'] ?? 0.0), // cumulative P&L from snapshot
    ];
}

/** Prefer FY bounds from master; fallback to Indian FY Apr→Mar */
private function resolveFyBounds(array $p): array
{
    // 1) Try DB master (adjust table/column names if different)
    $row = $this->univaictly->table('cmpfymastr')
        ->select('fy_beg_date, fy_end_date') // <- change to your actual column names
        ->where('cmpfymastr_id', (int)$p['cmpfymasr_id'])
        ->get()->getRowArray();

    if (!empty($row['fy_beg_date']) && !empty($row['fy_end_date'])) {
        return [$row['fy_beg_date'], $row['fy_end_date']];
    }

    // 2) Fallback: expand Indian FY from provided start or end
    $anchor = $p['fy_beg_date'] ?: $p['fy_end_date'];
    return $this->expandIndianFyFromDate($anchor);
}

private function getUomNameByItmKey(string $itm_id_unit_id, int $cmp_id): string
{
    // itm_id_unit_id looks like "itemId_unitId" → take last segment as unit_id
    $parts  = explode('_', $itm_id_unit_id);
    $unitId = (int) end($parts);

    if ($unitId <= 0) return '';

    $row = $this->db->table('itmunitmst')
        ->select('itm_unit_print, itm_unit_name')
        ->where('cmp_id', $cmp_id)
        ->where('itm_unit_id', $unitId)
        ->limit(1)
        ->get()->getRowArray();

    if (!$row) return '';
    // Prefer print name, else fall back to unit name
    return $row['itm_unit_print'] ?: $row['itm_unit_name'] ?: '';
}

private function expandIndianFyFromDate(string $anchorYmd): array
{
    $d = new \DateTimeImmutable($anchorYmd);
    $y = (int)$d->format('Y'); $m = (int)$d->format('n');
    if ($m <= 3) { // Jan–Mar belong to previous FY
        return [($y-1).'-04-01', $y.'-03-31'];
    }
    return [$y.'-04-01', ($y+1).'-03-31'];
}
    /**
     * Compute valuation from seed (snapshot/opening) up to $to_ymd.
     * Default wires to your closingStockTotal() which already does snapshot + forward-play.
     * If you later expose a method that also returns cumulative PnL, plug it here.
     */
    private function computeForwardFromSeed(array $seed, string $from_ymd, string $to_ymd, array $p): array
{
    // Build valuation state for THIS item only; compute cl_qty, cl_value and cumulative P&L.
    $mysql   = $this->db;
    $pg      = $this->externaldb->postgr_db();
    $cmpId   = $this->company_id;
    $boId    = $this->bo_id;
    $itmKey  = $p['itm_id_unit_id'];
    $mcId    = $p['mat_cent_id'] ?? null;
    $method = ($m = strtoupper(trim((string)($p['itm_val_method'] ?? $p['val_method'] ?? $p['val_id'] ?? $this->session->get('ses_dflt_val_method') ?? 'AVG')))) && in_array($m,['AVG','FIFO','LIFO'],true) ? $m : 'AVG';
    $allowNeg = 1;

    // Opening state for item
    $opQty  = (float)$this->getOpeningQty($p);
    $opVal  = (float)$this->getOpeningVal($p);
    $opRate = ($opQty>0 && $opVal>0) ? $opVal/$opQty : 0.0;

    // Seed from last clean snapshot (qty/balls + pnl_upto) if any
    [$seedDate, $seedPayload, $seedCumPnL] = $this->selectSeedWithinFY($pg, $cmpId, $itmKey, $method, $from_ymd, $to_ymd);

    // State containers
    $val_balls = []; $avgState = [];
    if ($opQty != 0.0) {
        if ($method === 'AVG') $avgState[$itmKey] = ['qty'=>$opQty,'avg'=>$opRate];
        else $val_balls[$itmKey] = [ ['qty'=>$opQty,'cost'=>$opRate] ];
    }

    $txFrom = $from_ymd;
    if ($seedDate && $seedPayload) {
        if ($method==='AVG') $avgState[$itmKey] = ['qty'=>(float)($seedPayload['qty']??0),'avg'=>(float)($seedPayload['avg_rate']??0)];
        else $val_balls[$itmKey] = array_map(fn($b)=>['qty'=>(float)$b['qty'],'cost'=>(float)$b['rate']], $seedPayload['balls'] ?? []);
        $txFrom = (new \DateTimeImmutable($seedDate))->modify('+1 day')->format('Y-m-d');
    }

    // Helpers
    $pushBall = function(array &$val_balls, string $key, float $qty, float $cost): void {
        if (!isset($val_balls[$key])) $val_balls[$key] = [];
        if ($qty == 0.0) return;
        $n = count($val_balls[$key]);
        if ($n>0 && abs($val_balls[$key][$n-1]['cost'] - $cost) < 1e-10) $val_balls[$key][$n-1]['qty'] += $qty;
        else $val_balls[$key][] = ['qty'=>$qty,'cost'=>$cost];
        while (!empty($val_balls[$key]) && abs($val_balls[$key][0]['qty']) <= 1e-12) array_shift($val_balls[$key]);
        while (!empty($val_balls[$key]) && abs($val_balls[$key][count($val_balls[$key])-1]['qty']) <= 1e-12) array_pop($val_balls[$key]);
    };
    $consumeBalls = function(array &$val_balls, string $key, float $qty, string $method, bool $allowNeg): float {
        $value=0.0; $need=$qty;
        if (!isset($val_balls[$key])) $val_balls[$key]=[];
        if ($method==='FIFO') {
            while ($need>1e-12 && !empty($val_balls[$key])) {
                $take=min($need,$val_balls[$key][0]['qty']);
                if ($take>0){ $value+=$take*$val_balls[$key][0]['cost']; $val_balls[$key][0]['qty']-=$take; $need-=$take; }
                if ($val_balls[$key][0]['qty']<=1e-12) array_shift($val_balls[$key]);
            }
        } else { // LIFO
            while ($need>1e-12 && !empty($val_balls[$key])) {
                $i=count($val_balls[$key])-1; $take=min($need,$val_balls[$key][$i]['qty']);
                if ($take>0){ $value+=$take*$val_balls[$key][$i]['cost']; $val_balls[$key][$i]['qty']-=$take; $need-=$take; }
                if ($val_balls[$key][$i]['qty']<=1e-12) array_pop($val_balls[$key]);
            }
        }
        if ($need>1e-12) { if(!$allowNeg) throw new \RuntimeException('Insufficient stock'); $val_balls[$key][]=['qty'=>-$need,'cost'=>0.0]; }
        return $value; // issue cost
    };
    $avgIn = function(array &$avgState, string $key, float $inQty, float $inRate): void {
        $q0=(float)($avgState[$key]['qty']??0.0); $a0=(float)($avgState[$key]['avg']??0.0);
        $val=$q0*$a0 + $inQty*$inRate; $q1=$q0+$inQty; $avgState[$key]=['qty'=>$q1,'avg'=>$q1>0?$val/$q1:0.0];
    };
    $avgOut = function(array &$avgState, string $key, float $outQty, bool $allowNeg): float {
        $q0=(float)($avgState[$key]['qty']??0.0); $a0=(float)($avgState[$key]['avg']??0.0);
        $issue=$outQty*$a0; $q1=$q0-$outQty; if ($q1<-1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient AVG');
        $avgState[$key]=['qty'=>$q1,'avg'=>$a0]; return $issue; // issue cost
    };

    // Replay transactions and accumulate P&L since seed
    $pnlSinceSeed = 0.0;
    $txns = $this->fetchTxnsForReport($mysql, $cmpId, $boId, $itmKey, $txFrom, $to_ymd, $mcId);
    foreach ($txns as $t) {
        $qty  = (float)$t['itm_txn_qty'];
        $amt  = (float)$t['itm_txn_amt'];  // sales/purchase value recorded in txn
        $rate = ($qty!=0.0) ? $amt/$qty : (float)$t['itm_txn_rate'];

        if ((int)$t['itm_txn_dr_cr'] === 1) {
            // IN
            if ($method==='AVG') $avgIn($avgState,$itmKey,$qty,$rate);
            else $pushBall($val_balls,$itmKey,$qty,$rate);
        } else {
            // OUT → realize profit = sales amount - issue cost
            if ($method==='AVG') {
                $issue = $avgOut($avgState,$itmKey,$qty,$allowNeg);
                $pnlSinceSeed += ($amt - $issue);
            } else {
                $issue = $consumeBalls($val_balls,$itmKey,$qty,$method,$allowNeg);
                $pnlSinceSeed += ($amt - $issue);
            }
        }
    }

    // Closing qty + value + cum P&L (seed cumulative + post-seed)
    $cumPnL = (float)($seedCumPnL ?? 0.0) + $pnlSinceSeed;

    if ($method==='AVG') {
        $q=(float)($avgState[$itmKey]['qty'] ?? 0); $ar=(float)($avgState[$itmKey]['avg'] ?? 0);
        return ['cl_qty'=>$q, 'cl_value'=>$q*$ar, 'cum_pnl'=>$cumPnL];
    } else {
        $balls = $val_balls[$itmKey] ?? [];
        $q = 0.0; $v = 0.0;
        foreach ($balls as $b){ $q += $b['qty']; $v += $b['qty'] * $b['cost']; }
        return ['cl_qty'=>$q, 'cl_value'=>$v, 'cum_pnl'=>$cumPnL];
    }
}
    /* ------------------------- formatting utilities ------------------------- */

    private function famt(float $x): float { return round($x, 2); }
    private function fqty(float $x): float { return round($x, 4); }

    private function blankIfZero(float $v): string
	{
		return abs($v) < 1e-9 ? '' : (string)$v;
	}

    private function numRows(array $rows): array
    {
        // Rows already numeric; some cells may be blank strings for display.
        return $rows;
    }
	
	public function closingStockTotal($from_date, $to_date, $filters)
{
    $mysql = $this->db;
    $pg    = $this->externaldb->postgr_db();

    $cmpId = $this->company_id;  $boId = $this->bo_id;  $fyId = $this->fy_id;

    $asOfStr  = date('Y-m-d', strtotime($to_date));
    $allowNeg = 1;

    $mcId   = $filters['mat_cent_id'] ?? '';
    $asOf   = new \DateTimeImmutable($asOfStr);
    $fyStart= date('Y-m-d', strtotime($from_date));

    // Method request: allow override from filters (1 FIFO, 2 LIFO, 3 AVG), else session AUTO/FIFO/LIFO/AVG
    $valReq = $this->session->get('ses_dflt_val_method'); // AUTO|FIFO|LIFO|AVG
    if (!empty($filters['val_method_reqid'])) {
        $valReq = match((int)$filters['val_method_reqid']) {
            1 => 'FIFO', 2 => 'LIFO', 3 => 'AVG', default => $valReq
        };
    }

    // Universe of items
    if (!empty($filters['item_ids']) && is_array($filters['item_ids'])) {
        // Force just these keys (ensures single selected item shows even with no activity)
        $allKeys = array_values(array_unique(array_filter($filters['item_ids'])));
        if (empty($allKeys)) { return 0.0; }
    } else {
        $allKeys = $this->findItemKeysForReport($mysql, $cmpId, $boId, $fyId, $fyStart, $asOfStr, '', '', $mcId);
    }

    // Per-item default method map for AUTO
    $methodMap = $this->loadItemMethodMap($mysql, $cmpId, $boId, $fyId);
    $resolveMethod = function(string $key) use ($valReq, $methodMap): string {
        if ($valReq !== 'AUTO') return $valReq;
        $m = $methodMap[$key] ?? 3;
        return match ($m) {1=>'FIFO',2=>'LIFO',3=>'AVG',default=>'AVG'};
    };

    // Openings for keys
    [$openQtyAll, $openValAll] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $allKeys);

    // Helpers (unchanged) ...
    $ballsVal = fn(array $balls): float => array_reduce($balls, fn($v,$b)=>$v + ($b['qty']*$b['cost']), 0.0);
    $pushBall = function(array &$val_balls, string $key, float $qty, float $cost): void {
        if (!isset($val_balls[$key])) $val_balls[$key] = [];
        if ($qty == 0.0) return;
        $n = count($val_balls[$key]);
        if ($n>0 && abs($val_balls[$key][$n-1]['cost'] - $cost) < 1e-10) $val_balls[$key][$n-1]['qty'] += $qty;
        else $val_balls[$key][] = ['qty'=>$qty,'cost'=>$cost];
        while (!empty($val_balls[$key]) && abs($val_balls[$key][0]['qty']) <= 1e-12) array_shift($val_balls[$key]);
        while (!empty($val_balls[$key]) && abs($val_balls[$key][count($val_balls[$key])-1]['qty']) <= 1e-12) array_pop($val_balls[$key]);
    };
    $consumeBalls = function(array &$val_balls, string $key, float $qty, string $method, bool $allowNeg): float {
        $value=0.0; $need=$qty;
        if (!isset($val_balls[$key])) $val_balls[$key]=[];
        if ($method==='FIFO') {
            while ($need>1e-12 && !empty($val_balls[$key])) {
                $take=min($need,$val_balls[$key][0]['qty']);
                if ($take>0){ $value+=$take*$val_balls[$key][0]['cost']; $val_balls[$key][0]['qty']-=$take; $need-=$take; }
                if ($val_balls[$key][0]['qty']<=1e-12) array_shift($val_balls[$key]);
            }
        } else {
            while ($need>1e-12 && !empty($val_balls[$key])) {
                $i=count($val_balls[$key])-1; $take=min($need,$val_balls[$key][$i]['qty']);
                if ($take>0){ $value+=$take*$val_balls[$key][$i]['cost']; $val_balls[$key][$i]['qty']-=$take; $need-=$take; }
                if ($val_balls[$key][$i]['qty']<=1e-12) array_pop($val_balls[$key]);
            }
        }
        if ($need>1e-12) { if(!$allowNeg) throw new \RuntimeException('Insufficient stock'); $val_balls[$key][]=['qty'=>-$need,'cost'=>0.0]; }
        return $value;
    };
    $avgInit = fn(array &$avgState, string $key, float $qty, float $avg) => $avgState[$key]=['qty'=>$qty,'avg'=>$avg];
    $avgIn   = function(array &$avgState, string $key, float $inQty, float $inRate): void {
        $q0=(float)($avgState[$key]['qty']??0.0); $a0=(float)($avgState[$key]['avg']??0.0);
        $val=$q0*$a0 + $inQty*$inRate; $q1=$q0+$inQty; $avgState[$key]=['qty'=>$q1,'avg'=>$q1>0?$val/$q1:0.0];
    };
    $avgOut  = function(array &$avgState, string $key, float $outQty, bool $allowNeg): float {
        $q0=(float)($avgState[$key]['qty']??0.0); $a0=(float)($avgState[$key]['avg']??0.0);
        $issue=$outQty*$a0; $q1=$q0-$outQty; if ($q1<-1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient AVG');
        $avgState[$key]=['qty'=>$q1,'avg'=>$a0]; return $issue;
    };

    $grand_cl_value = 0.0;

    foreach ($allKeys as $key) {
        $method = $resolveMethod($key);

        $opQty = (float)($openQtyAll[$key] ?? 0.0);
        $opVal = (float)($openValAll[$key] ?? 0.0);
        $opRate = ($opQty>0 && $opVal>0) ? $opVal/$opQty : 0.0;

        $val_balls=[]; $avgState=[];
        if ($opQty!=0.0) { if ($method==='AVG') $avgInit($avgState,$key,$opQty,$opRate); else $pushBall($val_balls,$key,$opQty,$opRate); }

        // seed within FY (same)
        [$seedDate, $seedPayload] = $this->selectSeedWithinFY($pg, $cmpId, $key, $method, $fyStart, $asOfStr);
        $txFrom = $fyStart;
        if ($seedDate && $seedPayload) {
            if ($method==='AVG') $avgInit($avgState,$key,(float)($seedPayload['qty']??0),(float)($seedPayload['avg_rate']??0));
            else $val_balls[$key] = array_map(fn($b)=>['qty'=>(float)$b['qty'],'cost'=>(float)$b['rate']], $seedPayload['balls'] ?? []);
            $txFrom = (new \DateTimeImmutable($seedDate))->modify('+1 day')->format('Y-m-d');
        }

        // replay txns up to as-of (respect single item)
        $txns = $this->fetchTxnsForReport($mysql, $cmpId, $boId, $key, $txFrom, $asOfStr, $mcId);
        foreach ($txns as $t) {
            $qty  = (float)$t['itm_txn_qty'];
            $amt  = (float)$t['itm_txn_amt'];
            $rate = ($qty!=0.0) ? $amt/$qty : (float)$t['itm_txn_rate'];

            if ((int)$t['itm_txn_dr_cr'] === 1) {
                if ($method==='AVG') $avgIn($avgState,$key,$qty,$rate); else $pushBall($val_balls,$key,$qty,$rate);
            } else {
                if ($method==='AVG') $avgOut($avgState,$key,$qty,$allowNeg); else $consumeBalls($val_balls,$key,$qty,$method,$allowNeg);
            }
        }

        // accumulate closing valuation
        if ($method==='AVG') {
            $q=(float)($avgState[$key]['qty'] ?? 0); $ar=(float)($avgState[$key]['avg'] ?? 0);
            $grand_cl_value += $q*$ar;
        } else {
            $balls = $val_balls[$key] ?? [];
            $grand_cl_value += $ballsVal($balls);
        }
    }

    return parseAmount($grand_cl_value);
}
	
	private function loadOpenings($mysql, int $cmpId, int $boId, int $fyId, array $keys): array
    {
        if (empty($keys)) return [[], []];

        // Sum opening QTY across ALL MCs for each itm_id_unit_id
        $qRows = $mysql->table('itmoppybal')
            ->select('itm_id_unit_id, COALESCE(SUM(itm_op_bal_qty),0) AS qty_sum')
            ->where('cmpfymastr_id', $fyId)
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->whereIn('itm_id_unit_id', $keys)
            ->groupBy('itm_id_unit_id')
            ->get()->getResultArray();

        $qmap = [];
        foreach ($qRows as $r) {
            $qmap[$r['itm_id_unit_id']] = (float)$r['qty_sum'];
        }

        // Sum opening VALUE across ALL MCs for each itm_id_unit_id
        $vRows = $mysql->table('itmoppyval')
            ->select('itm_id_unit_id, COALESCE(SUM(itm_op_val_amt),0) AS val_sum')
            ->where('cmpfymastr_id', $fyId)
            ->where('cmp_id', $cmpId)
            ->where('hobo_id', $boId)
            ->whereIn('itm_id_unit_id', $keys)
            ->groupBy('itm_id_unit_id')
            ->get()->getResultArray();

        $vmap = [];
        foreach ($vRows as $r) {
            $vmap[$r['itm_id_unit_id']] = (float)$r['val_sum'];
        }

        return [$qmap, $vmap];
    }

	private function findItemKeysForReport($mysql, int $cmpId, int $boId, int $fyId,
                                           string $fromDate, string $toDate,
                                           $itemId, $unitId, $mcId): array
    {
        $keys = [];

        // From openings
        $op = $mysql->table('itmoppybal')
            ->select('itm_id_unit_id')
            ->where('cmp_id',$cmpId)->where('hobo_id',$boId)->where('cmpfymastr_id',$fyId)
            ->get()->getResultArray();
        foreach ($op as $r) $keys[$r['itm_id_unit_id']] = true;

        // From activity in FY window
        $b = $mysql->table('itemtxnmst')
		    ->distinct()
            ->select('itm_id_unit_id')
            ->where('cmp_id',$cmpId)->where('hobo_id',$boId)
            ->where('itm_txn_date >=', $fromDate)
            ->where('itm_txn_date <=', $toDate);
        if (!empty($itemId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", 1)', $itemId);
        if (!empty($unitId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", -1)', $unitId);
        if (!empty($mcId))   $b->where('mat_cent_id', $mcId);
        $tx = $b->get()->getResultArray();
        foreach ($tx as $r) $keys[$r['itm_id_unit_id']] = true;

        // If both item_id & unit_id supplied but no openings/txns, still include that key
        if (!empty($itemId) && !empty($unitId)) {
            $key = $itemId.'_'.$unitId;
            if (!isset($keys[$key])) $keys[$key] = true;
        }

        $arr = array_keys($keys);
        sort($arr, SORT_STRING); // stable ordering for paging
        return $arr;
    }

    private function fyEndFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        return $this->fyStartFor($dt)->modify('+1 year -1 day');
    }

    private function fetchTxnsForReport($mysql, int $cmpId, int $boId, string $itmKey, string $fromDate, string $toDate, $mcId): array
    {
        $b = $mysql->table('itemtxnmst t')
            ->select('t.itm_txn_date, t.itm_txn_dr_cr, t.itm_txn_qty, t.itm_txn_rate, t.itm_txn_amt, t.itm_txn_id')
            ->where('t.cmp_id', $cmpId)
            ->where('t.hobo_id', $boId)
            ->where('t.itm_id_unit_id', $itmKey)
            ->where('t.itm_txn_date >=', $fromDate)
            ->where('t.itm_txn_date <=', $toDate)
            ->orderBy('t.itm_txn_date','ASC')
            ->orderBy('t.itm_txn_id','ASC');
        if (!empty($mcId)) $b->where('t.mat_cent_id', $mcId);
        return $b->get()->getResultArray();
    }
	
	private function loadItemMethodMap($mysql, int $cmpId, int $boId, int $fyId): array {
        $rows = $mysql->table('itmoppyval')
            ->select('itm_id_unit_id, itm_val_method_id')
            ->where('cmp_id',$cmpId)->where('hobo_id',$boId)->where('cmpfymastr_id',$fyId)
            ->get()->getResultArray();
        $m=[]; foreach($rows as $r){ $m[$r['itm_id_unit_id']] = ($r['itm_val_method_id'] ?? 'AVG'); }
        return $m;
    }
}
