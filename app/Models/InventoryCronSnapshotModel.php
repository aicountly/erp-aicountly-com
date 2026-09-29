<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;

class InventoryCronSnapshotModel extends Model	{	
  protected $externaldb;
  protected $univerpaic_db;
  protected $aicountly_db;
 
public function __construct() {
       parent::__construct();        
       $this->externaldb     = new externaldb();	
	   $this->univerpaic_db  = $this->externaldb->univerpaic_db();
	   $this->aicountly_db   = $this->externaldb->aicountly_db();
    }
	
    /**
     * Cron endpoint:
     *   date_from=YYYY-MM-DD  (required)
     *   date_to=YYYY-MM-DD    (optional; defaults date_from)
     *   span=FY|RANGE         (optional; FY ⇒ expand [date_from..FY_END(date_from)])
     *   method=ALL|FIFO|LIFO|AVG (default ALL)
     *   item_id_unit_id=1_2   (optional; one item/unit)
     *   allowNegative=1|0     (default 1)
     *
     * Example:
     * /inventory-snapshot/run?date_from=2025-04-01&span=FY&method=ALL
     */
    public function runAll()
    {
        @set_time_limit(0);

         $mysql = $this->db;         // MySQL (source)
         $pg    = $this->externaldb->postgr_cron_db();     // PostgreSQL (snapshots + cron log)

        // Inputs
        $asOfStr = date('Y-m-d', strtotime('yesterday'));//$this->request->getVar('as_of') ?: date('Y-m-d');
        $asOf    = new \DateTimeImmutable($asOfStr);

        $methodsArg = 'ALL';//strtoupper($this->request->getVar('methods') ?? 'ALL');
        $methods    = ($methodsArg === 'ALL') ? ['FIFO','LIFO','AVG'] : [ $methodsArg ];

        $allowNeg =  1;
        $onlyCmpId = '';//$this->request->getVar('only_cmp_id');

        $batchSize     = max(1, (int)($_POST['batch_size'] ?? 250));
        $batchPauseMs  = max(0, (int)($_POST['batch_pause_ms'] ?? 300)); // 0.3s

        // Companies
        $cmpBuilder = $mysql->table('cmpmastern')
            ->select('cmp_id, cmp_status')
            ->orderBy('cmp_id', 'ASC');

        // If you want only active companies, uncomment:
         $cmpBuilder->where('cmp_status', 1);

        if (!empty($onlyCmpId)) $cmpBuilder->where('cmp_id', (int)$onlyCmpId);

        $companies = $cmpBuilder->get()->getResultArray();
 
        $runSummary = [];
        foreach ($companies as $cmp) {
            $cmpId = (int)$cmp['cmp_id'];

            // Last 2 FYs for this cmp
            $fys = $mysql->table('cmpfymastr')
                ->select('cmpfymastr_id, fy_beg_date, fy_end_date')
                ->where('cmp_id', $cmpId)
                ->orderBy('fy_end_date', 'DESC')
                ->limit(2)
                ->get()->getResultArray();

            if (empty($fys)) {
                $runSummary[] = ['cmp_id'=>$cmpId, 'ok'=>true, 'msg'=>'No FY rows'];
                continue;
            }

            foreach ($fys as $fy) {
                $fyBeg = new \DateTimeImmutable($fy['fy_beg_date']);
                $fyEnd = new \DateTimeImmutable($fy['fy_end_date']);
                $to    = ($asOf < $fyEnd) ? $asOf : $fyEnd;
                if ($to < $fyBeg) continue;

                $cmpFyId = (int)$fy['cmpfymastr_id'];

                // Determine items for this company + FY (across branches)
                $itemKeys = $this->findItemKeysCompanyFY($mysql, $cmpId, $cmpFyId, $fyBeg->format('Y-m-d'), $to->format('Y-m-d'));
                if (empty($itemKeys)) {
                    $runSummary[] = ['cmp_id'=>$cmpId,'fy'=>$fy['fy_beg_date'].'..'.$fy['fy_end_date'],'ok'=>true,'msg'=>'No items'];
                    continue;
                }

                // Preload FY openings (SUM across branches)
                [$openQtyMap, $openValMap] = $this->loadCompanyOpeningsSum($mysql, $cmpId, $cmpFyId, $itemKeys);

                // Process in batches to reduce peak memory/CPU
                $chunks = array_chunk($itemKeys, $batchSize);
                foreach ($chunks as $chunkIdx => $keysChunk) {
                    foreach ($keysChunk as $key) {
                        foreach ($methods as $method) {
                            // Per item+method logging start
                            $runId = $this->logStart($pg, [
                                'run_date'      => $asOfStr,
                                'cmp_id'        => $cmpId,
                                'cmpfymastr_id' => $cmpFyId,
                                'itm_id_unit_id'=> $key,
                                'val_method_id' => $this->methodId($method),
                            ]);
							SaveErrorLog("saving run id --> ".$runId);

                            try {
                                $rowsWritten = $this->computeItemSnapshots(
                                    $mysql, $pg,
                                    $cmpId, $cmpFyId, $key, $method,
                                    $fyBeg->format('Y-m-d'), $to->format('Y-m-d'),
                                    $openQtyMap[$key] ?? 0.0,
                                    $openValMap[$key] ?? 0.0,
                                    $allowNeg
                                );
                                $this->logEndOk($pg, $runId, $rowsWritten, 'OK');
                            } catch (\Throwable $e) {
                                $this->logEndErr($pg, $runId, $e->getMessage());
                            }
                        }
                    }

                    // Gentle pause between batches
                    if ($batchPauseMs > 0 && $chunkIdx < (count($chunks)-1)) {
                        usleep($batchPauseMs * 1000);
                    }
                }

                $runSummary[] = [
                    'cmp_id'  => $cmpId,
                    'fy'      => $fy['fy_beg_date'].'..'.$fy['fy_end_date'],
                    'ok'      => true,
                    'items'   => count($itemKeys),
                    'methods' => implode(',', $methods),
                    'batches' => count($chunks),
                    'batch_size' => $batchSize
                ];
            }
        }

        return json_encode(['ok'=>true, 'as_of'=>$asOfStr, 'summary'=>$runSummary]);
    }

    /* ---------------- Dirty Markers (FY-bounded; company aware) ---------------- */

    // Back-dated txn ⇒ mark dirty from txn date → FY end (only that FY & company)
    public function markDirtyTxn()
    {
        $pg     = Database::connect('pg');
        $cmpId  = (int)($this->request->getVar('cmp_id') ?? 0);
        $date   = $this->request->getVar('dirty_date');
        $key    = $this->request->getVar('item_id_unit_id'); // optional
        $method = strtoupper($this->request->getVar('method') ?? 'ALL');

        if (!$cmpId || !$date) return $this->response->setJSON(['ok'=>false,'error'=>'cmp_id and dirty_date required']);

        $d   = new \DateTimeImmutable($date);
        $fyS = $this->fyStartFor($d)->format('Y-m-d');
        $fyE = $this->fyEndFor($d)->format('Y-m-d');

        $mids = ($method === 'ALL') ? ['AVG','FIFO','LIFO'] : [ $this->methodId($method) ];
		$methods = array_map(fn($m)=> strtoupper(trim((string)$m)), $mids);

		// Params (cmp/fy/date first, then methods)
		$params = array_merge([$cmpId, $fyS, $fyE, $date], $methods);

		// Make placeholders for the ARRAY[...] part
		$ph = implode(',', array_fill(0, count($methods), '?'));
        $sql =
          "UPDATE itmsnapsht
              SET itm_snapshot_is_dirty = TRUE
            WHERE cmp_id = ?
              AND itm_snapshot_date BETWEEN ? AND ?
              AND itm_snapshot_date >= ?
               AND val_method_id = ANY(ARRAY[$ph]::text[])";
        if ($key) { $sql .= " AND itm_id_unit_id = ?"; $params[] = $key; }

        $pg->query($sql, $params);
        return json_encode(['ok'=>true,'cmp_id'=>$cmpId,'from'=>$date,'fy_start'=>$fyS,'fy_end'=>$fyE,'methods'=>$mids,'item'=>$key ?: 'ALL']);
    }

    // Opening change ⇒ mark entire FY dirty (only that FY & company)
    public function markDirtyOpeningFY()
    {
        $pg     = Database::connect('pg');
        $cmpId  = (int)($this->request->getVar('cmp_id') ?? 0);
        $fyDate = $this->request->getVar('fy_date');
        $key    = $this->request->getVar('item_id_unit_id'); // optional
        $method = strtoupper($this->request->getVar('method') ?? 'ALL');

        if (!$cmpId || !$fyDate) return $this->response->setJSON(['ok'=>false,'error'=>'cmp_id and fy_date required']);

        $d   = new \DateTimeImmutable($fyDate);
        $fyS = $this->fyStartFor($d)->format('Y-m-d');
        $fyE = $this->fyEndFor($d)->format('Y-m-d');

        $mids = ($method === 'ALL') ? ['AVG','FIFO','LIFO'] : [ $this->methodId($method) ];
		$methods = array_map(fn($m)=> strtoupper(trim((string)$m)), $mids);

		// Params (cmp/fy/date first, then methods)
		$params = [$cmpId, $fyS, $fyE];

		// Make placeholders for the ARRAY[...] part
		$ph = implode(',', array_fill(0, count($methods), '?'));

        $sql =
          "UPDATE itmsnapsht
              SET itm_snapshot_is_dirty = TRUE
            WHERE cmp_id = ?
              AND itm_snapshot_date BETWEEN ? AND ?
              AND val_method_id = ANY(ARRAY[$ph]::text[])";
        if ($key) { $sql .= " AND itm_id_unit_id = ?"; $params[] = $key; }

        $pg->query($sql, $params);
        return json_encode(['ok'=>true,'cmp_id'=>$cmpId,'fy_start'=>$fyS,'fy_end'=>$fyE,'methods'=>$mids,'item'=>$key ?: 'ALL']);
    }

    /* ---------------- Core compute: returns rowsWritten (days) ---------------- */

    private function computeItemSnapshots(
        $mysql, $pg,
        int $cmpId, int $cmpFyId, string $itmKey, string $method,
        string $fyStart, string $asOf, float $opQty, float $opVal,
        bool $allowNeg
    ): int
    {
        $rowsWritten = 0;

        // Valuation lambdas
        $ballsQty = fn(array $balls): float => array_reduce($balls, fn($q,$b)=>$q + $b['qty'], 0.0);
        $ballsVal = fn(array $balls): float => array_reduce($balls, fn($v,$b)=>$v + ($b['qty'] * $b['cost']), 0.0);
        $ballsAvg = function(array $balls): float { $q=0.0;$v=0.0;foreach($balls as $b){$q+=$b['qty'];$v+=$b['qty']*$b['cost'];}return $q>0?$v/$q:0.0; };
        $pushBall = function(array &$val_balls, float $qty, float $cost): void {
            if ($qty == 0.0) return;
            $n = count($val_balls);
            if ($n>0 && abs($val_balls[$n-1]['cost'] - $cost) < 1e-10) $val_balls[$n-1]['qty'] += $qty;
            else $val_balls[] = ['qty'=>$qty, 'cost'=>$cost];
            while (!empty($val_balls) && abs($val_balls[0]['qty']) <= 1e-12) array_shift($val_balls);
            while (!empty($val_balls) && abs($val_balls[count($val_balls)-1]['qty']) <= 1e-12) array_pop($val_balls);
        };
        $consumeBalls = function(array &$val_balls, float $qty, string $method, bool $allowNeg) use ($ballsAvg): float {
            $value = 0.0; $need = $qty;
            if ($method === 'FIFO') {
                while ($need > 1e-12 && !empty($val_balls)) {
                    $take = min($need, $val_balls[0]['qty']);
                    if ($take > 0) { $value += $take*$val_balls[0]['cost']; $val_balls[0]['qty'] -= $take; $need -= $take; }
                    if ($val_balls[0]['qty'] <= 1e-12) array_shift($val_balls);
                }
            } else { // LIFO
                while ($need > 1e-12 && !empty($val_balls)) {
                    $i = count($val_balls)-1;
                    $take = min($need, $val_balls[$i]['qty']);
                    if ($take > 0) { $value += $take*$val_balls[$i]['cost']; $val_balls[$i]['qty'] -= $take; $need -= $take; }
                    if ($val_balls[$i]['qty'] <= 1e-12) array_pop($val_balls);
                }
            }
            if ($need > 1e-12) {
                if (!$allowNeg) throw new \RuntimeException("Insufficient stock for $method");
                $fallback = $ballsAvg($val_balls);
                $value += $need * $fallback;
                $val_balls[] = ['qty'=>-$need, 'cost'=>$fallback];
            }
            return $value;
        };

        // AVG state
        $avgState = ['qty'=>0.0,'avg'=>0.0];
        $avgIn  = function(float $inQty, float $inRate) use (&$avgState): void {
            $q0=$avgState['qty']; $a0=$avgState['avg'];
            $val=$q0*$a0 + $inQty*$inRate; $q1=$q0+$inQty; $a1=$q1>0 ? $val/$q1 : 0.0;
            $avgState=['qty'=>$q1,'avg'=>$a1];
        };
        $avgOut = function(float $outQty, bool $allowNeg) use (&$avgState): float {
            $q0=$avgState['qty']; $a0=$avgState['avg']; $issue=$outQty*$a0; $q1=$q0-$outQty;
            if ($q1<-1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient AVG');
            $avgState=['qty'=>$q1,'avg'=>$a0]; return $issue;
        };

        // Opening
        $opRate = ($opQty > 0 && $opVal > 0) ? $opVal / $opQty : 0.0;
        $val_balls = [];
        if ($opQty != 0.0) {
            if ($method === 'AVG') { $avgState = ['qty'=>$opQty, 'avg'=>$opRate]; }
            else                   { $pushBall($val_balls, $opQty, $opRate); }
        }

        // Compute start+seed
        [$startDate, $seedDate, $seedPayload] = $this->computeStartSeed($pg, $cmpId, $itmKey, $method, $fyStart, $asOf);

        // Warm from seed
        $warmFrom = $fyStart;
        if ($seedDate && $seedPayload) {
            if ($method === 'AVG') {
                $avgState = ['qty'=>(float)($seedPayload['qty'] ?? 0), 'avg'=>(float)($seedPayload['avg_rate'] ?? 0)];
            } else {
                $val_balls = array_map(fn($b)=>['qty'=>(float)$b['qty'], 'cost'=>(float)$b['rate']], $seedPayload['balls'] ?? []);
            }
            $warmFrom = (new \DateTimeImmutable($seedDate))->modify('+1 day')->format('Y-m-d');
        }

        // If starting later than FY start and no seed, pre-warm by silent replay of early txns
        if ($startDate > $fyStart && !$seedDate) {
            $earlyTxns = $this->fetchCompanyTxns($mysql, $cmpId, $itmKey, $fyStart, date('Y-m-d', strtotime($startDate.' -1 day')));
            foreach ($earlyTxns as $t) {
                $qty=(float)$t['itm_txn_qty']; $amt=(float)$t['itm_txn_amt']; $rate = ($qty!=0.0)? $amt/$qty : (float)$t['itm_txn_rate'];
                if ((int)$t['itm_txn_dr_cr'] === 1) { if ($method==='AVG') $avgIn($qty,$rate); else $pushBall($val_balls,$qty,$rate); }
                else                                { if ($method==='AVG') $avgOut($qty,true); else $consumeBalls($val_balls,$qty,$method,true); }
            }
            $warmFrom = $startDate;
        }

        // Txns for [warmFrom..asOf]
        $txns = $this->fetchCompanyTxns($mysql, $cmpId, $itmKey, $warmFrom, $asOf);

        // Day loop
        $cursorDate = new \DateTimeImmutable($warmFrom);
        $endDate    = new \DateTimeImmutable($asOf);
        $i = 0; $N = count($txns);

        while ($cursorDate <= $endDate) {
            $day = $cursorDate->format('Y-m-d');
            $dayPnL = 0.0;

            while ($i < $N && $txns[$i]['itm_txn_date'] === $day) {
                $qty  = (float)$txns[$i]['itm_txn_qty'];
                $amt  = (float)$txns[$i]['itm_txn_amt'];
                $rate = ($qty != 0.0) ? $amt / $qty : (float)$txns[$i]['itm_txn_rate'];
                if ((int)$txns[$i]['itm_txn_dr_cr'] === 1) {
                    if ($method==='AVG') { $avgIn($qty,$rate); } else { $pushBall($val_balls,$qty,$rate); }
                } else {
                    $issue = ($method==='AVG') ? $avgOut($qty,$allowNeg)
                                               : $consumeBalls($val_balls,$qty,$method,$allowNeg);
                    $dayPnL += ($amt - $issue);
                }
                $i++;
            }

            // closing valuation
            if ($method === 'AVG') {
                $q  = $avgState['qty']; $ar = $avgState['avg']; $v = $q * $ar;
                $ballsJson = json_encode(['method'=>'AVG','qty'=>$q,'avg_rate'=>$ar,'value'=>$v], JSON_UNESCAPED_UNICODE);
                $snapValue = $v;
            } else {
                $q  = $ballsQty($val_balls); $v = $ballsVal($val_balls);
                $ballsJson = json_encode([
                    'method'=>$method,'qty'=>$q,'value'=>$v,
                    'balls'=>array_map(fn($b)=>['qty'=>$b['qty'],'rate'=>$b['cost']], $val_balls)
                ], JSON_UNESCAPED_UNICODE);
                $snapValue = $v;
            }

            $this->upsertSnapshot($pg, [
                'cmp_id'               => $cmpId,
                'itm_id_unit_id'       => $itmKey,
                'val_method_id'        => $this->methodId($method),
                'itm_snapshot_date'    => $day,
                'itm_snapshot_value'   => $snapValue,
                'itm_snapshot_pnl'     => $dayPnL,
                'itm_snapshot_timestamp'    => date('Y-m-d H:i:s'),
                'itm_snapshot_is_dirty'=> false,
                'itm_balls_snapshot'   => $ballsJson,
            ]);
            $rowsWritten++;

            $cursorDate = $cursorDate->modify('+1 day');
        }

        return $rowsWritten;
    }

    /**
     * Start date + seed for (re)build
     * Returns [startDate, seedDate, seedPayload|null]
     */
    private function computeStartSeed($pg, int $cmpId, string $itmKey, string $method, string $fyStart, string $asOf): array
    {
        $mid = $this->methodId($method);

        $dirty = $pg->query(
            "SELECT MIN(itm_snapshot_date) AS first_dirty
               FROM itmsnapsht
              WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=? AND itm_snapshot_is_dirty=TRUE
                AND itm_snapshot_date BETWEEN ? AND ?",
            [$cmpId, $itmKey, $mid, $fyStart, $asOf]
        )->getFirstRow('array');
        $firstDirty = $dirty && $dirty['first_dirty'] ? $dirty['first_dirty'] : null;

        $seed = $pg->query(
            "SELECT itm_snapshot_date, itm_balls_snapshot
               FROM itmsnapsht
              WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=? AND itm_snapshot_is_dirty=FALSE
                AND itm_snapshot_date BETWEEN ? AND ?
           ORDER BY itm_snapshot_date DESC LIMIT 1",
            [$cmpId, $itmKey, $mid, $fyStart, $asOf]
        )->getFirstRow('array');

        $latestCleanDate = $seed['itm_snapshot_date'] ?? null;

        if ($firstDirty)        $startDate = $firstDirty;
        elseif ($latestCleanDate) $startDate = date('Y-m-d', strtotime($latestCleanDate.' +1 day'));
        else                    $startDate = $fyStart;

        $seedDate    = null;
        $seedPayload = null;
        if ($latestCleanDate && $latestCleanDate < $startDate) {
            $seedDate    = $latestCleanDate;
            $seedPayload = json_decode($seed['itm_balls_snapshot'], true);
        }
        return [$startDate, $seedDate, $seedPayload];
    }

    /* ---------------- Logging helpers ---------------- */

    private function logStart($pg, array $d): int
	{
		// schema-qualified to avoid search_path surprises
		$row = $pg->query(
			"INSERT INTO public.itmsnap_cronlog
			 (run_date, cmp_id, cmpfymastr_id, itm_id_unit_id, val_method_id, start_ts, status)
			 VALUES (?,?,?,?,?, now(), 'RUN')
			 RETURNING run_id",
			[ $d['run_date'], $d['cmp_id'], $d['cmpfymastr_id'], $d['itm_id_unit_id'], $d['val_method_id'] ]
		)->getFirstRow('array');

		return (int)($row['run_id'] ?? 0);
	}

    private function logEndOk($pg, int $runId, int $rowsWritten, string $msg): void
	{
		if ($runId <= 0) return;
		$pg->query(
			"UPDATE public.itmsnap_cronlog
				SET end_ts = now(), status = 'OK', rows_written = ?, msg = ?
			  WHERE run_id = ?",
			[ $rowsWritten, $msg, $runId ]
		);
	}

    private function logEndErr($pg, int $runId, string $err): void
	{
		if ($runId <= 0) return;
		$pg->query(
			"UPDATE public.itmsnap_cronlog
				SET end_ts = now(), status = 'ERR', msg = ?
			  WHERE run_id = ?",
			[ $err, $runId ]
		);
	}

    /* ---------------- Data helpers ---------------- */

    private function findItemKeysCompanyFY($mysql, int $cmpId, int $cmpFyId, string $fromDate, string $toDate): array
    {
        $keys = [];
        $openQ = $mysql->table('itmoppybal')
            ->select('itm_id_unit_id')
            ->where('cmp_id', $cmpId)
            ->where('cmpfymastr_id', $cmpFyId)
            ->get()->getResultArray();
        foreach ($openQ as $r) $keys[$r['itm_id_unit_id']] = true;

        $actQ = $mysql->table('itemtxnmst')
		    ->distinct()
            ->select('itm_id_unit_id')
            ->where('cmp_id', $cmpId)
            ->where('itm_txn_date >=', $fromDate)
            ->where('itm_txn_date <=', $toDate)
            ->get()->getResultArray();
        foreach ($actQ as $r) $keys[$r['itm_id_unit_id']] = true;

        $arr = array_keys($keys);
        sort($arr, SORT_STRING);
        return $arr;
    }

    private function loadCompanyOpeningsSum($mysql, int $cmpId, int $cmpFyId, array $keys): array
    {
        if (empty($keys)) return [[],[]];

        $qRows = $mysql->table('itmoppybal')
            ->select('itm_id_unit_id, SUM(itm_op_bal_qty) AS q')
            ->where('cmp_id', $cmpId)
            ->where('cmpfymastr_id', $cmpFyId)
            ->whereIn('itm_id_unit_id', $keys)
            ->groupBy('itm_id_unit_id')
            ->get()->getResultArray();
        $qmap=[]; foreach($qRows as $r) $qmap[$r['itm_id_unit_id']] = (float)$r['q'];

        $vRows = $mysql->table('itmoppyval')
            ->select('itm_id_unit_id, SUM(itm_op_val_amt) AS v')
            ->where('cmp_id', $cmpId)
            ->where('cmpfymastr_id', $cmpFyId)
            ->whereIn('itm_id_unit_id', $keys)
            ->groupBy('itm_id_unit_id')
            ->get()->getResultArray();
        $vmap=[]; foreach($vRows as $r) $vmap[$r['itm_id_unit_id']] = (float)($r['v'] ?? 0.0);

        return [$qmap, $vmap];
    }

    private function fetchCompanyTxns($mysql, int $cmpId, string $itmKey, string $fromDate, string $toDate): array
    {
        return $mysql->table('itemtxnmst t')
            ->select('t.itm_txn_date, t.itm_txn_dr_cr, t.itm_txn_qty, t.itm_txn_rate, t.itm_txn_amt, t.itm_txn_id')
            ->where('t.cmp_id', $cmpId)
            ->where('t.itm_id_unit_id', $itmKey)
            ->where('t.itm_txn_date >=', $fromDate)
            ->where('t.itm_txn_date <=', $toDate)
            ->orderBy('t.itm_txn_date', 'ASC')
            ->orderBy('t.itm_txn_id',   'ASC')
            ->get()->getResultArray();
    }

    /* ---------------- Utilities ---------------- */

    private function fyStartFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        $y=(int)$dt->format('Y'); $m=(int)$dt->format('n'); $fyY = ($m>=4)?$y:$y-1;
        return new \DateTimeImmutable("$fyY-04-01");
    }
    private function fyEndFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        return $this->fyStartFor($dt)->modify('+1 year -1 day');
    }
    private function methodId(string $method): string
{
    $m = strtoupper(trim($method));
    return in_array($m, ['AVG','FIFO','LIFO'], true) ? $m : 'AVG';
}
   private function upsertSnapshot($pg, array $row): void
{
    $pg->query(
        "UPDATE public.itmsnapsht
            SET itm_snapshot_value = ?, itm_snapshot_pnl = ?, itm_snapshot_timestamp = ?, itm_snapshot_is_dirty = ?, itm_balls_snapshot = ?
          WHERE cmp_id = ? AND itm_id_unit_id = ? AND val_method_id = ? AND itm_snapshot_date = ?",
        [
            $row['itm_snapshot_value'], $row['itm_snapshot_pnl'], $row['itm_snapshot_timestamp'],
            $row['itm_snapshot_is_dirty'], $row['itm_balls_snapshot'],
            $row['cmp_id'], $row['itm_id_unit_id'], $row['val_method_id'], $row['itm_snapshot_date']
        ]
    );
    $affected = $pg->affectedRows(); // CI4.6 portable across drivers

    if ($affected === 0) {
        $pg->query(
            "INSERT INTO public.itmsnapsht
             (cmp_id, itm_id_unit_id, val_method_id, itm_snapshot_date, itm_snapshot_value,
              itm_snapshot_pnl, itm_snapshot_timestamp, itm_snapshot_is_dirty, itm_balls_snapshot)
             VALUES (?,?,?,?,?,?,?,?,?)",
            [
                $row['cmp_id'], $row['itm_id_unit_id'], $row['val_method_id'], $row['itm_snapshot_date'],
                $row['itm_snapshot_value'], $row['itm_snapshot_pnl'], $row['itm_snapshot_timestamp'],
                $row['itm_snapshot_is_dirty'], $row['itm_balls_snapshot']
            ]
        );
    }
}
}
