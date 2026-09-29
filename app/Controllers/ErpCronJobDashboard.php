<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use Config\Database;
use App\Libraries\externaldb;

class ErpCronJobDashboard extends BaseController
{
	protected $externaldb;
	protected $db;
	function __construct(){           
       $this->externaldb     = new externaldb();
       $this->db = \Config\Database::connect(); // initialize DB connection	   
    } 
    /**
     * Daily cron log report (success + failures).
     *
     * GET:
     *   run_date=YYYY-MM-DD   (required)
     *   cmp_id=INT            (optional)
     *   status=OK|ERR|RUN     (optional filter)
     *   method=ALL|FIFO|LIFO|AVG (optional)
     *   pq_curpage / pq_rpp   (ParamQuery server paging; defaults 1 / 50)
     *
     * Returns JSON:
     * {
     *   curPage, totalRecords, data: [...],
     *   totals: { ok: <int>, err: <int>, run: <int>, rows_written_sum: <int> }
     * }
     */
	public function index(){
	  $data      = array();
	  $companies = $this->db->table('cmpmastern')->select('cmp_id, cmp_name')->orderBy('cmp_name','ASC')->get()->getResultArray();
	  $data['companies'] = $companies;
	 return view('cronjob_logs',$data);	
	} 
    public function ajax_daily_log()
    {
        $pg      = $this->externaldb->postgr_db();
        $runDate = $this->request->getVar('run_date');
		$cmpId = $this->request->getVar('company_id');
        if (!$runDate) return $this->json_encode(['ok'=>false,'error'=>'run_date required']);
		
		if($cmpId!=''){
		
        $status  = strtoupper($this->request->getVar('status') ?? '');
        $method  = strtoupper($this->request->getVar('method') ?? 'ALL');

        $pq_curPage = max(1, (int)($this->request->getVar('pq_curpage') ?? 1));
        $pq_rPP     = max(1, (int)($this->request->getVar('pq_rpp') ?? 50));
        if ($pq_rPP > 200) $pq_rPP = 200; // keep this light

        $where = ["run_date = ?"]; $params = [$runDate];

        if (!empty($cmpId)) { $where[] = "cmp_id = ?"; $params[] = (int)$cmpId; }
        if (in_array($status, ['OK','ERR','RUN'], true)) { $where[] = "status = ?"; $params[] = $status; }
        if (in_array($method, ['FIFO','LIFO','AVG'], true)) { $where[] = "val_method_id = ?"; $params[] = $this->methodId($method); }

        $whereSql = implode(" AND ", $where);

        // total records
        $totRow = $pg->query("SELECT COUNT(*) AS c FROM itmsnap_cronlog WHERE $whereSql", $params)->getFirstRow('array');
        $total  = (int)($totRow['c'] ?? 0);

        // page slice
        $offset = ($pq_rPP * ($pq_curPage - 1));
        if ($offset >= $total && $total > 0) {
            $pq_curPage = max(1, (int)ceil($total / $pq_rPP));
            $offset     = ($pq_rPP * ($pq_curPage - 1));
        }

        $rows = $pg->query(
            "SELECT run_id, run_date, cmp_id, cmpfymastr_id, itm_id_unit_id, val_method_id,
                    start_ts, end_ts, status, rows_written, COALESCE(msg,'') AS msg
               FROM itmsnap_cronlog
              WHERE $whereSql
           ORDER BY cmp_id, itm_id_unit_id, val_method_id, run_id
              OFFSET $offset LIMIT $pq_rPP",
            $params
        )->getResultArray();

        // quick totals by status
        $stats = $pg->query(
            "SELECT status, COUNT(*) AS cnt, SUM(rows_written) AS sum_rows
               FROM itmsnap_cronlog
              WHERE $whereSql
           GROUP BY status",
            $params
        )->getResultArray();

        $by = ['OK'=>0,'ERR'=>0,'RUN'=>0]; $rowsSum = 0;
        foreach ($stats as $s) { $by[$s['status']] = (int)$s['cnt']; $rowsSum += (int)($s['sum_rows'] ?? 0); }

        // map method id to text
        foreach ($rows as &$r) {
            $r['method'] = match((int)$r['val_method_id']) {1=>'FIFO',2=>'LIFO',3=>'AVG',default=>'AVG'};
        }

        return json_encode([
            'curPage'      => $pq_curPage,
            'totalRecords' => $total,
            'data'         => $rows,
            'totals'       => ['ok'=>$by['OK'], 'err'=>$by['ERR'], 'run'=>$by['RUN'], 'rows_written_sum'=>$rowsSum]
        ]);
		
	    }
    }

    private function methodId(string $method): int {
        return match ($method) { 'FIFO'=>1, 'LIFO'=>2, 'AVG'=>3, default=>3 };
    }
}
