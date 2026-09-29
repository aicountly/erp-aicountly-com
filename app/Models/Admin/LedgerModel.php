<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;


class LedgerModel extends Model{
	function __construct() {
		parent::__construct();        
		$this->session       =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->VouchersModel =  new VouchersModel();		
		$this->CommonModel   =  new CommonModel();	   		
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
	} 
	
	function sub_ledger_masters($account_id){
		$builder = $this->db->table("subacctmst"); 
		$builder->select('sub_acc_id,sub_acc_name');
		$builder->where('cmp_id', $this->company_id);
		$builder->where('sub_acc_is_active',1);
		$builder->where('acc_id',$account_id);
		$result = $builder->get()->getResultArray();
		return $result;
	}
	
	function get_group_details($id) {
	          	$builder = $this->db->table("accgrpmstn"); 
	          	$builder->where('cmp_id', $this->company_id);		
	          	$builder->where('acc_grp_id', $id);
	          	$builder->where('acc_grp_is_active',1);
	          	$result = $builder->get()->getRowArray();
	          	return $result;
	          }
	  
	function get_bill_details($id)
	{
		$builder = $this->db->table('billmaster bm')
			->select("
				bm.bill_ref_id,
				bm.bill_ref_name,
				bm.acc_id,
				bm.bill_due_date,
				bop.bill_op_bal,
				TO_CHAR(MIN(bt.bill_txn_date), 'DD-MM-YYYY') AS start_date
			", false)
			->join(
				'billtxnmst bt',
				'bt.bill_ref_id = bm.bill_ref_id AND bt.hobo_id = ' . (int)$this->bo_id,
				'left'
			)
			->join(
				'billoppybal bop',
				'bop.bill_ref_id = bm.bill_ref_id',
				'left'
			)
			->where('bm.bill_ref_id', $id)
			->groupBy('bm.bill_ref_id')
			->groupBy('bm.bill_ref_name')
			->groupBy('bm.acc_id')
			->groupBy('bm.bill_due_date')
			->groupBy('bop.bill_op_bal');

		$result = $builder->get()->getRowArray();

		return $result;
	}
	 
	 function get_sub_group_ids_list($array){    	
    	if(!empty($array)){
    	    $builder = $this->db->table("accgrpmstn acgrpmst");
    	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
    	    $builder->select('acgrpmst.acc_grp_id');
    	    $builder->whereIn('undercrsmt.under_main_id', $array);
			$data = $builder->get()->getResultArray();
			
	    	if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}
    	}
    	return $array;
    }
	
	 function get_sundry_groups()
{
    $array = [];

    // 1) Base groups (Trade Payable / Trade Receivables)
    $builder = $this->db->table('accgrpmstn');
    $builder->select('acc_grp_id');
    $builder->groupStart()
        ->like('LOWER(acc_grp_name)', 'trade payable', 'left')
        ->orLike('LOWER(acc_grp_name)', 'trade receivables', 'left')
        ->groupEnd();
    $builder->where('cmp_id', $this->company_id);
    $builder->where('acc_grp_is_active', 1);
    $data = $builder->get()->getResultArray();

    if (!empty($data)) {
        foreach ($data as $row) {
            $array[] = (int)$row['acc_grp_id'];
        }
    }

    // 2) Recursively include all sub-groups
    $allIds = array_values(array_unique($array));
    $queue  = $allIds;

    while (!empty($queue)) {
        $childBuilder = $this->db->table('undercrsmt u');
        $childBuilder->select('u.crs_mst_id AS acc_grp_id');
        $childBuilder->join(
            'accgrpmstn g',
            'g.acc_grp_id = u.crs_mst_id AND g.cmp_id = ' . $this->company_id,
            'inner'
        );
        $childBuilder->where('u.crs_mst_type', 2); // group mapping
        $childBuilder->where('u.cmp_id', $this->company_id);
        $childBuilder->whereIn('u.under_crs_mst_id', $queue);
        $childBuilder->where('g.acc_grp_is_active', 1);

        $childRows = $childBuilder->get()->getResultArray();

        $queue = [];
        foreach ($childRows as $child) {
            $gid = (int)$child['acc_grp_id'];
            if (!in_array($gid, $allIds, true)) {
                $allIds[] = $gid;
                $queue[]  = $gid;
            }
        }
    }

    return $allIds;
}
	 
	 function get_sundry_accounts()
{
    $groups_ids = array_values($this->get_sundry_groups()); // e.g. [16, 22]

    $builder = $this->db->table('acctmaster');
    $builder->join(
        'undercrsmt',
        'undercrsmt.crs_mst_id = acctmaster.acc_id 
         AND undercrsmt.crs_mst_type = 1 
         AND undercrsmt.cmp_id = ' . $this->company_id,
        'left'
    );

    $builder->select('acctmaster.acc_id AS id, acctmaster.acc_name AS label, acctmaster.acc_name AS value');
    $builder->where('acctmaster.cmp_id', $this->company_id);
    $builder->whereIn('undercrsmt.under_crs_mst_id', $groups_ids);
    $builder->where('acctmaster.acc_is_active', 1);

    // Ensure unique accounts even if multiple undercrsmt rows exist
    $builder->groupBy('acctmaster.acc_id');
    $builder->orderBy('acctmaster.acc_name', 'ASC');

    return $builder->get()->getResultArray();
}

function load_bills_management_one_account(){

    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"])     ? (int)$_POST["pq_rpp"]     : 10;

    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP    < 1) $pq_rPP     = 10;

    $from_date  = date('Y-m-d', strtotime($_POST["from_date"]));
    $to_date    = date('Y-m-d', strtotime($_POST["to_date"]));
    $bill_type  = $_POST["bill_type"];
    $account_id = (int) $_POST["account_id"];

    /* ------------------------------------------------------------------ */
    /*  Pre-aggregate billtxnmst per bill_ref_id to avoid Cartesian fan-out
        caused by the independent JOIN with billoppybal.
        Without this sub-query each billtxnmst row was multiplied by the
        number of billoppybal rows for the same bill_ref_id, doubling
        (or more) the SUM(bill_txn_amt).                                   */
    /* ------------------------------------------------------------------ */
    $txnSubSql = "
        SELECT
            bt_sub.bill_ref_id,
            MAX(bt_sub.bill_txn_date)  AS bill_txn_date,
            SUM(bt_sub.bill_txn_amt)   AS bill_txn_amt
        FROM billtxnmst bt_sub
        WHERE bt_sub.acc_id         = {$account_id}
          AND bt_sub.bill_txn_date <= '{$to_date}'
        GROUP BY bt_sub.bill_ref_id
    ";

    /* ---------------- MAIN QUERY ---------------- */
    $builder = $this->db->table('billmaster bm');

    $builder->select("
        bm.bill_ref_id,
        bm.bill_ref_name,
        a.acc_name,
        COALESCE(bt.bill_txn_date, bm.bill_due_date)  AS dated,

        COALESCE(
            bt.bill_txn_amt,
            bpb.bill_op_bal,
            0
        ) AS bill_balance,

        CASE
            WHEN COALESCE(bt.bill_txn_amt, bpb.bill_op_bal, 0) > 0
            THEN COALESCE(bt.bill_txn_amt, bpb.bill_op_bal, 0)
            ELSE 0
        END AS receivable,

        CASE
            WHEN COALESCE(bt.bill_txn_amt, bpb.bill_op_bal, 0) < 0
            THEN ABS(COALESCE(bt.bill_txn_amt, bpb.bill_op_bal, 0))
            ELSE 0
        END AS payable,

        bm.bill_due_date,
        (CURRENT_DATE - bm.bill_due_date) AS overdue_days
    ", false);

    /* ---------------- JOINS ---------------- */
    $builder->join('acctmaster a', 'a.acc_id = bm.acc_id', 'left');

    /* Use the pre-aggregated sub-query instead of a raw table join
       so that one bill_ref_id always maps to exactly ONE row here.     */
    $builder->join(
        "({$txnSubSql}) bt",
        'bt.bill_ref_id = bm.bill_ref_id',
        'left',
        false
    );

    /* billoppybal: keep a single representative row per bill_ref_id.
       If the table can have multiple rows per bill_ref_id (as seen in
       image 4 – two rows for bill_ref_id 3483) we pick the latest one
       to avoid yet another fan-out.                                    */
    $bpbSubSql = "
        SELECT DISTINCT ON (bill_ref_id)
            bill_ref_id,
            bill_op_bal
        FROM billoppybal
        ORDER BY bill_ref_id, bill_oppy_bal_id DESC
    ";

    $builder->join(
        "({$bpbSubSql}) bpb",
        'bpb.bill_ref_id = bm.bill_ref_id',
        'left',
        false
    );

    /* ---------------- WHERE ---------------- */
    $builder->where('bm.acc_id', $account_id);

    /* ---------------- COUNT ---------------- */
    $countBuilder  = clone $builder;
    $total_records = $countBuilder->countAllResults(false);

    /* ---------------- PAGINATION ---------------- */
    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_records) {
        $pq_curPage = max(1, ceil($total_records / $pq_rPP));
        $offset     = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    $builder->orderBy('bm.bill_due_date', 'ASC');
    $builder->limit($pq_rPP, $offset);

    $records = $builder->get()->getResultArray();

    /* ---------------- FINAL FORMAT ---------------- */
    $final_records = [];

    foreach ($records as $value) {
        $final_records[] = [
            'bills_ref_id'     => $value['bill_ref_id'],
            'account_name'     => $value['acc_name'],
            'ref_no'           => $value['bill_ref_name'],
            'total_amount'     => $value['bill_balance'],
            'receivable'       => formatAmount($value['receivable']),
            'payable'          => formatAmount($value['payable']),
            'date'             => $value['dated'],
            'due_date'         => $value['bill_due_date'],
            'overdue_days'     => $value['overdue_days'] . '&nbsp;&nbsp;',
            'count_total'      => $value['bill_balance'],
            'count_receivable' => $value['receivable'],
            'count_payable'    => $value['payable'],
        ];
    }

    return json_encode([
        'totalRecords' => $total_records,
        'curPage'      => $pq_curPage,
        'data'         => $final_records
    ]);
}
	 function load_bills_management_one_account_olde(){
	     
	       $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
			$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;

			if ($pq_curPage < 1) $pq_curPage = 1;
			if ($pq_rPP < 1) $pq_rPP = 10;

			$from_date  = date('Y-m-d', strtotime($_POST["from_date"]));
			$to_date    = date('Y-m-d', strtotime($_POST["to_date"]));
			$bill_type  = $_POST["bill_type"];
			$account_id = (int) $_POST["account_id"];

			/* ---------------- MAIN QUERY ---------------- */
			$builder = $this->db->table('billmaster bm');

			$builder->select("
				bm.bill_ref_id,
				bm.bill_ref_name,
				a.acc_name,
				MAX(bt.bill_txn_date) AS dated,

				COALESCE(
					SUM(bt.bill_txn_amt),
					MAX(bpb.bill_op_bal),
					0
				) AS bill_balance,

				CASE
					WHEN COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0) > 0
					THEN COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0)
					ELSE 0
				END AS receivable,

				CASE
					WHEN COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0) < 0
					THEN ABS(COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0))
					ELSE 0
				END AS payable,

				bm.bill_due_date,
				(CURRENT_DATE - bm.bill_due_date) AS overdue_days
			", false);

			/* ---------------- JOINS ---------------- */
			$builder->join('acctmaster a', 'a.acc_id = bm.acc_id', 'left');

			$builder->join(
				'billtxnmst bt',
				"bt.bill_ref_id = bm.bill_ref_id
				 AND bt.acc_id = {$account_id}
				 AND bt.bill_txn_date <= '{$to_date}'",
				'left',
				false
			);

			$builder->join(
				'billoppybal bpb',
				'bpb.bill_ref_id = bm.bill_ref_id',
				'left'
			);

			/* ---------------- WHERE ---------------- */
			$builder->where('bm.acc_id', $account_id);

			/* ---------------- GROUP ---------------- */
			$builder->groupBy([
				'bm.bill_ref_id',
				'bm.bill_ref_name',
				'a.acc_name',
				'bm.bill_due_date'
			]);

			/* ---------------- COUNT ---------------- */
			$countBuilder   = clone $builder;
			$total_records  = $countBuilder->countAllResults(false);

			/* ---------------- PAGINATION ---------------- */
			$offset = ($pq_rPP * ($pq_curPage - 1));
			if ($offset > $total_records) {
				$pq_curPage = max(1, ceil($total_records / $pq_rPP));
				$offset = ($pq_rPP * ($pq_curPage - 1));
			}
			if ($offset < 0) $offset = 0;

			$builder->orderBy('bm.bill_due_date', 'ASC');
			$builder->limit($pq_rPP, $offset);

			$records = $builder->get()->getResultArray();

			/* ---------------- FINAL FORMAT ---------------- */
			$final_records = [];

			foreach ($records as $value) {
				$final_records[] = [
					'bills_ref_id'     => $value['bill_ref_id'],
					'account_name'     => $value['acc_name'],
					'ref_no'           => $value['bill_ref_name'],
					'total_amount'     => $value['bill_balance'],
					'receivable'       => formatAmount($value['receivable']),
					'payable'          => formatAmount($value['payable']),
					'date'             => $value['dated'],
					'due_date'         => $value['bill_due_date'],
					'overdue_days'     => $value['overdue_days'] . '&nbsp;&nbsp;',
					'count_total'      => $value['bill_balance'],
					'count_receivable' => $value['receivable'],
					'count_payable'    => $value['payable'],
				];
			}

			return json_encode([
				'totalRecords' => $total_records,
				'curPage'      => $pq_curPage,
				'data'         => $final_records
			]);
	 } 
	 
	 public function load_batches_listings($from_date, $to_date)
		{
			$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
			$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
			if ($pq_curPage < 1) $pq_curPage = 1;
			if ($pq_rPP < 1) $pq_rPP = 10;

			$builder = $this->db->table('batchmastr bm', false);
			$builder->select("
					bm.batch_no,
					bm.batch_master_id,
					bm.item_id_unit_id,
					im.itm_name AS item_name,
					iu.itm_unit_name AS unit_name,
					bm.batch_mfr_date AS mfr_date,
					bm.batch_expiry_date AS exp_date,
					SUM(bm.batch_qty) AS balance
				", false); // Use false to protect the raw SELECT

			// Use split_part and ::int cast for PostgreSQL in JOIN conditions
			$builder->join('itemmaster im', "im.itm_id = split_part(bm.item_id_unit_id::text, '_', 1)::int", 'left', false);
			$builder->join('itmunitmst iu', "iu.itm_unit_id = split_part(bm.item_id_unit_id::text, '_', 2)::int", 'left', false);

			$builder->where('bm.batch_is_active', 1);
			$builder->where('bm.cmp_id', $this->company_id);

			// Add all non-aggregated columns to GROUP BY for PostgreSQL compatibility
			$builder->groupBy([
				'bm.batch_no', 
				'bm.item_id_unit_id', 
				'bm.batch_master_id', 
				'im.itm_name', 
				'iu.itm_unit_name', 
				'bm.batch_mfr_date', 
				'bm.batch_expiry_date'
			]);

			$countBuilder  = clone $builder;
			$total_records = $countBuilder->countAllResults(false);

			// Pagination logic (no changes needed)
			if ($pq_curPage == 0) $pq_curPage = 1;
			$offset = ($pq_rPP * ($pq_curPage - 1));

			if ($offset > $total_records && $total_records > 0) {
				$pq_curPage = (int)ceil($total_records / $pq_rPP);
				$offset = ($pq_rPP * ($pq_curPage - 1));
			}
			if ($offset < 0) $offset = 0;

			$builder->orderBy('bm.batch_no', 'asc');
			$builder->limit($pq_rPP, $offset);
			$records = $builder->get()->getResultArray();
			
			// Data processing loop (no changes needed)
			$final_records = [];
			foreach ($records as $value) {
				$mfr_date  = date('d M, Y', strtotime($value['mfr_date']));
				$exp_date  = date('d M, Y', strtotime($value['exp_date']));

				$enc_batch = $value['batch_no'] . '|||' . $value['item_id_unit_id'] . '|||' . $mfr_date . '|||' . $exp_date;
				$final_records[] = [
					'enc_batch'  => urlencode(base64_encode($enc_batch)),
					'batch_id'   => $value['batch_master_id'],
					'batch_no'   => $value['batch_no'],
					'item_name'  => $value['item_name'],
					'unit_name'  => $value['unit_name'],
					'mfr_date'   => $mfr_date,
					'exp_date'   => $exp_date,
					'balance'    => $value['balance'],
				];
			}
			
			return "{\"totalRecords\":" . $total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($final_records) . "}";
		}
	 
	 public function load_batchwise_listings($batch_master_id, $from_date, $to_date)
        {
            $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
            $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
        
            if ($pq_curPage < 1) $pq_curPage = 1;
            if ($pq_rPP < 1) $pq_rPP = 10;
        
            /* ---------------- MAIN QUERY ---------------- */
            $builder = $this->db->table('batchtxnmt bt');
        
            $builder->select("
                bt.batch_txn_date,
        
                SUM(
                    CASE 
                        WHEN bt.batch_txn_dr_cr = 1 THEN bt.batch_txn_qty 
                        ELSE 0 
                    END
                ) AS qty_in,
        
                SUM(
                    CASE 
                        WHEN bt.batch_txn_dr_cr = 2 THEN bt.batch_txn_qty 
                        ELSE 0 
                    END
                ) AS qty_out,
        
                (
                    SUM(
                        CASE 
                            WHEN bt.batch_txn_dr_cr = 1 THEN bt.batch_txn_qty 
                            ELSE 0 
                        END
                    ) -
                    SUM(
                        CASE 
                            WHEN bt.batch_txn_dr_cr = 2 THEN bt.batch_txn_qty 
                            ELSE 0 
                        END
                    )
                ) AS balance_qty,
        
                mc.mat_cent_name
            ", false);
        
            $builder->join('batchmastr bm', 'bm.batch_master_id = bt.batch_master_id', 'left');
            $builder->join('matcentmst mc', 'mc.mat_cent_id = bt.mat_cent_id', 'left');
        
            $builder->where('bt.batch_master_id', $batch_master_id);
            $builder->where('bt.batch_txn_date >=', $from_date);
            $builder->where('bt.batch_txn_date <=', $to_date);
            $builder->where('bt.cmp_id', $this->company_id);
            $builder->where('bt.hobo_id', $this->bo_id);
        
            $builder->groupBy([
                'bt.batch_txn_date',
                'bt.mat_cent_id',
                'mc.mat_cent_name'
            ]);
        
            /* ---------------- COUNT ---------------- */
            $countBuilder  = clone $builder;
            $total_records = $countBuilder->countAllResults(false);
        
            /* ---------------- PAGINATION ---------------- */
            $offset = ($pq_rPP * ($pq_curPage - 1));
        
            if ($offset > $total_records) {
                $pq_curPage = max(1, ceil($total_records / $pq_rPP));
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            if ($offset < 0) $offset = 0;
        
            $builder->orderBy('bt.batch_txn_date', 'ASC');
            $builder->limit($pq_rPP, $offset);
        
            $records = $builder->get()->getResultArray();
        
            /* ---------------- FORMAT OUTPUT ---------------- */
            $final_records = [];
        
            foreach ($records as $value) {
                $final_records[] = [
                    'batch_date'  => $value['batch_txn_date'],
                    'mcname'      => $value['mat_cent_name'],
                    'branch_name' => '',
                    'qty_in'      => (float) $value['qty_in'],
                    'qty_out'     => (float) $value['qty_out'],
                    'balance'     => (float) $value['balance_qty'],
                ];
            }
        
            return json_encode([
                'totalRecords' => $total_records,
                'curPage'      => $pq_curPage,
                'data'         => $final_records
            ]);
        }
	          
	 function account_info($account_id){	 
		$acctmaster_tbl  = "acctmaster";
		$acctmstdet_tbl  = "acctmstdet";
		$undercrsmt_tbl  = "undercrsmt";
		$builder = $this->db->table($acctmaster_tbl);
		$builder->join($acctmstdet_tbl, "$acctmstdet_tbl.acc_id = $acctmaster_tbl.acc_id", 'left');
		$builder->join($undercrsmt_tbl, "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id AND $undercrsmt_tbl.crs_mst_type =1", 'left');
		$builder->where("$acctmaster_tbl.acc_id", $account_id);
		$response = $builder->get()->getRowArray();  	   
	   return $response;  	   
	   }
	   
	function batch_info($batch_master_id){	 
		$builder = $this->db->table('batchmastr bm');
        $builder->select('
            bm.batch_master_id,
            bm.batch_no,
            bm.batch_mfr_date,
            bm.batch_expiry_date,
            im.itm_name,
            iu.itm_unit_name
        ', false);
        /*
          item_id_unit_id format assumed: itemId_unitId (e.g. 12_5)
        */
        $builder->join(
            'itemmaster im',
            "im.itm_id = CAST(split_part(bm.item_id_unit_id, '_', 1) AS INTEGER)",
            'left',
            false
        );
        $builder->join(
            'itmunitmst iu',
            "iu.itm_unit_id = CAST(split_part(bm.item_id_unit_id, '_', 2) AS INTEGER)",
            'left',
            false
        );
        $builder->where('bm.batch_master_id', $batch_master_id);
        $query = $builder->get();
        $batch_info = $query->getRowArray(); // single row
        return $batch_info;
	   }
	
	function load_bills_management_details()
{
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;

    $from_date  = date('Y-m-d', strtotime($_POST["from_date"]));
    $to_date    = date('Y-m-d', strtotime($_POST["to_date"]));
    $bills_ref_id = $_POST["bills_ref_id"];

    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    // Opening balance
    $op = $this->db->table('billoppybal')
        ->select('bill_op_bal')
        ->where('bill_ref_id', $bills_ref_id)
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('hobo_id', $this->bo_id)
        ->get()
        ->getRow();

    $opening_balance = $op->bill_op_bal ?? 0;

    $builder = $this->db->table('billtxnmst a');

    $builder->select("
        a.vch_txn_id,
        a.txn_id,
        a.bill_txn_date,
        conso.vch_date,
        a.acc_id,
        a.bill_txn_dr_cr,
        conso.vch_type_id,
        vt.vch_name AS voucher_type,
        lnr.vch_long_narr AS long_narr,
        a.bill_txn_amt,

        CASE 
            WHEN a.bill_txn_dr_cr = 1 THEN a.bill_txn_amt
            ELSE 0
        END AS dr_amount,

        CASE 
            WHEN a.bill_txn_dr_cr = 2 THEN a.bill_txn_amt
            ELSE 0
        END AS cr_amount,

        am.acc_name AS other_acc_name,

        (
            $opening_balance +
            (
                SELECT COALESCE(SUM(
                    CASE 
                        WHEN b.bill_txn_dr_cr = 1 THEN b.bill_txn_amt
                        WHEN b.bill_txn_dr_cr = 2 THEN -b.bill_txn_amt
                        ELSE 0
                    END
                ),0)
                FROM billtxnmst b
                WHERE b.bill_ref_id = a.bill_ref_id
                AND b.cmp_id = a.cmp_id
                AND b.hobo_id = a.hobo_id
                AND (
                    b.bill_txn_date < a.bill_txn_date
                    OR (b.bill_txn_date = a.bill_txn_date AND b.txn_id <= a.txn_id)
                )
            )
        ) AS running_balance
    ", false);

    $builder->join('vchtxnconso conso', 'conso.vch_txn_id = a.vch_txn_id', 'left');
    $builder->join('vchtypemst vt', 'vt.vch_type_id = conso.vch_type_id', 'left');
    $builder->join('vchlongnar lnr', 'lnr.vch_txn_id = conso.vch_txn_id', 'left');

    $builder->join(
        'cmptxnmstn c',
        "a.vch_txn_id = c.vch_txn_id AND c.master_id_type = 'acc' AND c.master_id <> a.acc_id",
        'left'
    );

    $builder->join('acctmaster am', 'c.master_id = am.acc_id', 'left');

    $builder->where('a.bill_ref_id', $bills_ref_id);
    $builder->where('am.acc_is_active', 1);
    $builder->where('a.cmp_id', $this->company_id);
    $builder->where('a.hobo_id', $this->bo_id);
    $builder->where('a.vch_txn_id !=', null);
    $builder->where('a.bill_txn_date >=', $from_date);
    $builder->where('a.bill_txn_date <=', $to_date);

    // count query
    $countBuilder = clone $builder;
    $total_records = $countBuilder->countAllResults();

    $offset = ($pq_rPP * ($pq_curPage - 1));

    if ($offset > $total_records) {
        $pq_curPage = ceil($total_records / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }

    if ($offset < 0) {
        $offset = 0;
    }

    $builder->orderBy('a.bill_txn_date', 'ASC');
    $builder->orderBy('a.vch_txn_id', 'ASC');
    $builder->limit($pq_rPP, $offset);

    $result = $builder->get()->getResultArray();

    $final_records = [];

    foreach ($result as $row) {
        $final_records[] = [
            'voucher_type'     => $row['voucher_type'],
            'debit'            => formatAmount($row['dr_amount']),
            'credit'           => formatAmount($row['cr_amount']),
            'balance'          => $row['running_balance'].'&nbsp;&nbsp;',
            'voucher_txn_id'   => $row['vch_txn_id'],
            'voucher_type_id'  => $row['vch_type_id'],
            'voucher_date'     => $row['vch_date'],
            'debit_total'      => $row['dr_amount'],
            'credit_total'     => $row['cr_amount'],
        ];
    }

    return json_encode([
        "totalRecords" => $total_records,
        "curPage" => $pq_curPage,
        "data" => $final_records
    ]);
}
	function load_bills_management_refs(){
		//$sundry_creditors = $this->get_sundry_creditors();
		//$sundry_debitors = $this->get_sundry_debitors();
		$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;

		if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;

		$from_date = date('Y-m-d', strtotime($_POST["from_date"]));
		$to_date   = date('Y-m-d', strtotime($_POST["to_date"]));
		$bill_type = $_POST["bill_type"];

		/* ---------------- MAIN QUERY ---------------- */
		$builder = $this->db->table('billmaster bm');

		$builder->select("
			bm.bill_ref_id,
			bm.bill_ref_name,
			MAX(a.acc_name) AS acc_name,
			MAX(bt.bill_txn_date) AS dated,

			COALESCE(
				SUM(bt.bill_txn_amt),
				MAX(bpb.bill_op_bal),
				0
			) AS bill_balance,

			CASE 
				WHEN COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0) > 0
				THEN COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0)
				ELSE 0
			END AS receivable,

			CASE 
				WHEN COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0) < 0
				THEN ABS(COALESCE(SUM(bt.bill_txn_amt), MAX(bpb.bill_op_bal), 0))
				ELSE 0
			END AS payable,

			bm.bill_due_date,
			(CURRENT_DATE - bm.bill_due_date) AS overdue_days
		", false);

		/* ---------------- JOINS ---------------- */
		$builder->join('acctmaster a', 'a.acc_id = bm.acc_id', 'left');

		$builder->join(
			'billtxnmst bt',
			"bt.bill_ref_id = bm.bill_ref_id 
			 AND bt.bill_txn_date <= '{$to_date}'",
			'left',
			false
		);

		$builder->join(
			'billoppybal bpb',
			'bpb.bill_ref_id = bm.bill_ref_id',
			'left'
		);

		/* ---------------- GROUP BY ---------------- */
		$builder->groupBy([
			'bm.bill_ref_id',
			'bm.bill_ref_name',
			'bm.bill_due_date'
		]);
        $builder->where('bm.cmp_id', $this->company_id);
		/* ---------------- COUNT ---------------- */
		$countBuilder  = clone $builder;
		$total_records = $countBuilder->countAllResults(false);

		/* ---------------- PAGINATION ---------------- */
		$offset = ($pq_rPP * ($pq_curPage - 1));

		if ($offset > $total_records) {
			$pq_curPage = max(1, ceil($total_records / $pq_rPP));
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}

		if ($offset < 0) $offset = 0;

		/* ---------------- FINAL FETCH ---------------- */
		
		$builder->orderBy('bm.bill_due_date', 'ASC');
		$builder->limit($pq_rPP, $offset);

		$records = $builder->get()->getResultArray();

		/* ---------------- OUTPUT FORMAT ---------------- */
		$final_records = [];

		foreach ($records as $value) {
			$final_records[] = [
				'bills_ref_id'     => $value['bill_ref_id'],
				'account_name'     => $value['acc_name'],
				'ref_no'           => $value['bill_ref_name'],
				'total_amount'     => $value['bill_balance'],
				'receivable'       => $value['receivable'],
				'payable'          => $value['payable'],
				'date'             => $value['dated'],
				'due_date'         => $value['bill_due_date'],
				'overdue_days'     => $value['overdue_days'] . '&nbsp;&nbsp;',
				'count_total'      => $value['bill_balance'],
				'count_receivable' => $value['receivable'],
				'count_payable'    => $value['payable'],
			];
		}

		return json_encode([
			'totalRecords' => $total_records,
			'curPage'      => $pq_curPage,
			'data'         => $final_records
		]);

	}
	
	
	function load_bills_management_accounts(){
	   // $groups = $this->get_sundry_groups();
      //	$groups_ids = array_column($groups, 'id');
	    
	    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
        if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;	
		
		$from_date     = date('Y-m-d',strtotime($_POST["from_date"]));
		$to_date       = date('Y-m-d',strtotime($_POST["to_date"]));
		$bill_type      = date('Y-m-d',strtotime($_POST["bill_type"]));
		
		$groups = [];//$this->get_sundry_groups();
  	    $groups_ids = [];//array_column($groups, 'id');
		$account_master_tbl = 'acctmaster';

/* ---------------- FETCH ACCOUNTS ---------------- */
$builder = $this->db->table($account_master_tbl);
$builder->select('acc_id as id, acc_name as account_name');
$builder->where('cmp_id', $this->company_id);
$builder->where('acc_is_active', 1);

/* ---------------- PAGINATION ---------------- */
$countBuilder  = clone $builder;
$total_records = $countBuilder->countAllResults(false);

if ($pq_curPage < 1) $pq_curPage = 1;
$offset = ($pq_rPP * ($pq_curPage - 1));

if ($offset > $total_records) {
    $pq_curPage = max(1, ceil($total_records / $pq_rPP));
    $offset = ($pq_rPP * ($pq_curPage - 1));
}
if ($offset < 0) $offset = 0;

$builder->limit($pq_rPP, $offset);
$records = $builder->get()->getResultArray();

/* ---------------- LOOP ACCOUNTS ---------------- */
$final = [];

foreach ($records as $value) {

    $bal_undefined = 0;
    $receivable    = 0;
    $payable       = 0;

    /* ---------- CHECK BILL-MASTER EXISTS ---------- */
    $bill_exists = $this->db->table('billmaster')
        ->where('bill_is_active', 1)
        ->where('acc_id', $value['id'])
        ->where('cmp_id', $this->company_id)
        ->countAllResults();

    /* ---------- ON-ACCOUNT BALANCE ---------- */
    if ($bill_exists == 0) {
        $row = $this->db->table('accttxnmst')
            ->select("
                SUM(
                    CASE
                        WHEN acc_txn_dr_cr = 1 AND acc_txn_type = 1 THEN acc_txn_amt
                        WHEN acc_txn_dr_cr = 2 AND acc_txn_type = 1 THEN -acc_txn_amt
                        ELSE 0
                    END
                ) AS running_balance
            ", false)
            ->where('acc_id', $value['id'])
            ->get()
            ->getRowArray();

        $bal_undefined = $row ? (float)$row['running_balance'] : 0;
    }

    $drcr_undefined = ($bal_undefined < 0) ? ' CR' : ' DR';

    /* ---------- ACTIVE BILLS ---------- */
    $bill_masters = $this->db->table('billmaster')
        ->select('bill_ref_id')
        ->where('bill_is_active', 1)
        ->where('acc_id', $value['id'])
        ->where('cmp_id', $this->company_id)
        ->get()
        ->getResultArray();

    foreach ($bill_masters as $bill_master) {

        /* ---------- BILL TRANSACTIONS ---------- */
        $bill_txn = $this->db->table('billtxnmst')
            ->select("
                SUM(
                    CASE
                        WHEN bill_txn_dr_cr = 1 THEN bill_txn_amt
                        WHEN bill_txn_dr_cr = 2 THEN -bill_txn_amt
                        ELSE 0
                    END
                ) AS bills_txn_bal
            ", false)
            ->where('bill_ref_id', $bill_master['bill_ref_id'])
            ->where('bill_txn_date <=', $to_date)
            ->get()
            ->getRowArray();

        if ($bill_txn && $bill_txn['bills_txn_bal'] !== null) {
            $balance = (float)$bill_txn['bills_txn_bal'];
        } 
        else {
            /* ---------- BILL OPENING BAL ---------- */
            $op = $this->db->table('billoppybal')
                ->select('bill_op_bal')
                ->where('hobo_id', $this->bo_id)
                ->where('bill_ref_id', $bill_master['bill_ref_id'])
                ->get()
                ->getRowArray();

            $balance = (float)($op['bill_op_bal'] ?? 0);
        }

        if ($balance >= 0) {
            $receivable += $balance;
        } else {
            $payable += $balance;
        }
    }

    /* ---------- BILL TYPE FILTER ---------- */
    if ($receivable == 0 && $bill_type === 'R') continue;
    if ($payable == 0 && $bill_type === 'P') continue;

    /* ---------- NET BILL ---------- */
    $net_bill_amount = $receivable + $payable;
    $net_bill_drcr   = ($net_bill_amount < 0) ? ' CR' : ' DR';

    $net_bill_os      = $bal_undefined + $net_bill_amount;
    $net_bill_os_drcr = ($net_bill_os < 0) ? ' CR' : ' DR';

    /* ---------- LEDGER BAL ---------- */
    $row = $this->db->table('accttxnmst')
        ->select("
            SUM(
                CASE
                    WHEN acc_txn_dr_cr = 1 AND acc_txn_type = 1 THEN acc_txn_amt
                    WHEN acc_txn_dr_cr = 2 AND acc_txn_type = 1 THEN -acc_txn_amt
                    ELSE 0
                END
            ) AS running_balance
        ", false)
        ->where('acc_id', $value['id'])
        ->get()
        ->getRowArray();

    $ledger_bal      = $row ? (float)$row['running_balance'] : 0;
    $ledger_bal_drcr = ($ledger_bal < 0) ? ' CR' : ' DR';

    /* ---------- FINAL ROW ---------- */
    $final[] = [
        'account_id'       => $value['id'],
        'account_name'     => $value['account_name'],

        'bills_receivable' => $receivable ? formatAmount(abs($receivable)) . ' DR' : '',
        'bills_payable'    => $payable ? formatAmount(abs($payable)) . ' CR' : '',

        'net_bill_amount'  => formatAmount(abs($net_bill_amount)) . $net_bill_drcr,
        'on_account'       => $bal_undefined ? formatAmount(abs($bal_undefined)) . $drcr_undefined : '',
        'net_bill_os'      => formatAmount(abs($net_bill_os)) . $net_bill_os_drcr,
        'ledger_bal'       => formatAmount(abs($ledger_bal)) . $ledger_bal_drcr . '&nbsp;&nbsp;',

        'count_receivable' => $receivable,
        'count_payable'    => abs($payable),
        'count_net_bill'   => $net_bill_amount,
        'count_undefined'  => $bal_undefined,
        'count_net_os'     => $net_bill_os,
        'count_ledger'     => $ledger_bal
    ];
}

/* ---------------- RESPONSE ---------------- */
return json_encode([
    'totalRecords' => $total_records,
    'curPage'      => $pq_curPage,
    'data'         => $final
]);
	}
	
	public function load_day_book_condensed($compId = null, $bo_id = null, $fy_id = null, $input = null)
{
    $reqposted  = !empty($input) ? $input : $_POST;
    $pq_curPage = max(1, (int)($reqposted['pq_curpage'] ?? 1));
    $pq_rPP     = max(1, (int)($reqposted['pq_rpp']     ?? 10));

    $from_date = !empty($reqposted['from_date'])
        ? date('Y-m-d', strtotime($reqposted['from_date'])) : null;
    $to_date   = !empty($reqposted['to_date'])
        ? date('Y-m-d', strtotime($reqposted['to_date']))   : null;

    $search        = '';
    $search_column = '';
    if (!empty($reqposted['pq_filter'])) {
        $pq_filter     = json_decode($reqposted['pq_filter']);
        $search        = $pq_filter->data[0]->value    ?? '';
        $search_column = $pq_filter->data[0]->dataIndx ?? '';
    }

    $cid    = !empty($compId) ? $compId : $this->company_id;
    $hoboId = !empty($bo_id)  ? $bo_id  : $this->bo_id;

    /*
    |--------------------------------------------------------------------------
    | FY start date — correct April-to-March calculation
    |
    | If from_date is Jan–Mar (month < 4), the FY started the PREVIOUS April.
    | e.g. from_date = 2026-02-01  → FY start = 2025-04-01  ✅
    |      from_date = 2026-06-01  → FY start = 2026-04-01  ✅
    |--------------------------------------------------------------------------
    */
    $refDate      = $from_date ?: date('Y-m-d');
    $refMonth     = (int)date('m', strtotime($refDate));
    $refYear      = (int)date('Y', strtotime($refDate));
    $fyStartYear  = ($refMonth < 4) ? $refYear - 1 : $refYear;
    $fy_start_date = $fyStartYear . '-04-01';

    /*
    |--------------------------------------------------------------------------
    | BASE CONDITIONS
    |
    | ROOT CAUSE FIX (Issue 1):
    |   The original INNER JOIN included `a.hobo_id = v.hobo_id`.
    |   accttxnmst legs can carry different hobo_id values from vchtxnconso,
    |   so this condition silently excluded vouchers where NO leg matched
    |   vchtxnconso.hobo_id — causing February 2026 sales to disappear.
    |
    |   Fix: scope hobo_id ONLY on vchtxnconso (v.hobo_id = $hoboId in WHERE).
    |        Remove a.hobo_id = v.hobo_id from the JOIN condition entirely.
    |        Keep a.acc_txn_type = 1 in the JOIN (that is safe — it is a
    |        property of the transaction leg, not of the voucher header).
    |--------------------------------------------------------------------------
    */

    // COUNT — wraps a grouped subquery so DISTINCT is implicit per group
    $countBuilder = $this->db->table('vchtxnconso v')
        ->select('COUNT(*) as cnt', false)
        ->join(
            'accttxnmst a',
            // ✅ FIXED: removed a.hobo_id = v.hobo_id
            'a.vch_txn_id = v.vch_txn_id AND a.cmp_id = v.cmp_id AND a.acc_txn_type = 1',
            'inner', false
        )
        ->join('vchtypemst vt',  'vt.vch_type_id = v.vch_type_id',  'left', false)
        ->join('acctmaster ac',  'ac.acc_id = a.acc_id',             'left', false)
        ->where('v.cmp_id',   $cid)
        ->where('v.hobo_id',  $hoboId);   // ✅ branch scope on header only

    if ($from_date) $countBuilder->where('v.vch_date >=', $from_date);
    if ($to_date)   $countBuilder->where('v.vch_date <=', $to_date);

    $this->applySearchFilter($countBuilder, $search, $search_column);

    // Group first, then count groups — avoids DISTINCT on a joined set
    $countBuilder->groupBy('v.vch_txn_id, v.vch_type_id, v.vch_series_id, v.vch_date, vt.vch_name');
    $countSql     = $countBuilder->getCompiledSelect(false);
    $total_Records = (int)$this->db->query(
        "SELECT COUNT(*) AS total FROM ({$countSql}) AS count_wrap"
    )->getRow()->total;

    $offset = $pq_rPP * ($pq_curPage - 1);
    if ($offset >= $total_Records && $total_Records > 0) {
        $pq_curPage = (int)ceil($total_Records / $pq_rPP);
        $offset     = $pq_rPP * ($pq_curPage - 1);
    }
    if ($offset < 0) $offset = 0;

    // MAIN QUERY
    $mainQuery = $this->db->table('vchtxnconso v')
        ->select("
            v.vch_txn_id,
            v.vch_type_id,
            v.vch_series_id,
            v.vch_date,
            vt.vch_name,
            COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END), 0) AS debit_total,
            COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END), 0) AS credit_total,
            string_agg(DISTINCT ac.acc_name, ', ' ORDER BY ac.acc_name)                    AS acc_names
        ", false)
        ->join('vchtypemst vt', 'vt.vch_type_id = v.vch_type_id', 'left', false)
        ->join(
            'accttxnmst a',
            // ✅ FIXED: removed a.hobo_id = v.hobo_id
            'a.vch_txn_id = v.vch_txn_id AND a.cmp_id = v.cmp_id AND a.acc_txn_type = 1',
            'inner', false
        )
        ->join('acctmaster ac', 'ac.acc_id = a.acc_id', 'left', false)
        ->where('v.cmp_id',  $cid)
        ->where('v.hobo_id', $hoboId);   // ✅ branch scope on header only

    if ($from_date) $mainQuery->where('v.vch_date >=', $from_date);
    if ($to_date)   $mainQuery->where('v.vch_date <=', $to_date);

    $this->applySearchFilter($mainQuery, $search, $search_column);

    $vouchers = $mainQuery
        ->groupBy('v.vch_txn_id, v.vch_type_id, v.vch_series_id, v.vch_date, vt.vch_name')
        ->orderBy('v.vch_date',    'ASC')
        ->orderBy('v.vch_txn_id',  'ASC')
        ->limit($pq_rPP, $offset)
        ->get()
        ->getResultArray();

    if (empty($vouchers)) {
        return json_encode([
            'totalRecords' => $total_Records,
            'curPage'      => $pq_curPage,
            'data'         => [],
        ]);
    }

    $voucherIds = array_column($vouchers, 'vch_txn_id');

    // Party name — first cmptxnmstn entry per voucher
    $partyQuery = "
        SELECT vch_txn_id, party_name FROM (
            SELECT
                cm.vch_txn_id,
                acp.acc_name AS party_name,
                ROW_NUMBER() OVER (PARTITION BY cm.vch_txn_id ORDER BY cm.txn_id ASC) AS rn
            FROM cmptxnmstn cm
            INNER JOIN acctmaster acp ON acp.acc_id = cm.master_id
            WHERE cm.master_id_type = 'acc'
              AND cm.cmp_id = ?
              AND cm.vch_txn_id IN (" . implode(',', array_fill(0, count($voucherIds), '?')) . ")
        ) sub WHERE rn = 1
    ";
    $partyRows = $this->db->query(
        $partyQuery, array_merge([$cid], $voucherIds)
    )->getResultArray();
    $partyMap  = array_column($partyRows, 'party_name', 'vch_txn_id');

    /*
    |--------------------------------------------------------------------------
    | Voucher serial number baseline from FY start
    |
    | FIX (Issue 3 + 4):
    |   - Use the corrected $fy_start_date (April of the correct year).
    |   - Remove the hardcoded vch_type_id != 8 exclusion — inconsistent.
    |   - Do NOT add a.hobo_id = v.hobo_id here either.
    |--------------------------------------------------------------------------
    */
    $voucher_count = $offset; // default: position within the current filtered set

    if ($from_date && $from_date > $fy_start_date) {
        $baseline = $this->db->table('vchtxnconso vc')
            ->select('COUNT(DISTINCT vc.vch_txn_id) AS cnt', false)
            ->join(
                'accttxnmst a2',
                // ✅ FIXED: no a2.hobo_id = vc.hobo_id
                'a2.vch_txn_id = vc.vch_txn_id AND a2.cmp_id = vc.cmp_id AND a2.acc_txn_type = 1',
                'inner', false
            )
            ->where('vc.cmp_id',   $cid)
            ->where('vc.hobo_id',  $hoboId)
            ->where('vc.vch_date >=', $fy_start_date)
            ->where('vc.vch_date <',  $from_date)
            ->get()
            ->getRow()
            ->cnt;

        $voucher_count = (int)$baseline + $offset;
    }

    // Voucher type display rules
    $zeroCreditTypes = [18, 13, 3];
    $zeroDebitTypes  = [11,  9, 2];

    $data = [];
    foreach ($vouchers as $v) {
        $voucher_count++;
        $vch_type_id  = (int)$v['vch_type_id'];
        $debit_total  = (float)$v['debit_total'];
        $credit_total = (float)$v['credit_total'];

        if (in_array($vch_type_id, $zeroCreditTypes, true)) {
            $credit_total = 0.0;
        } elseif (in_array($vch_type_id, $zeroDebitTypes, true)) {
            $debit_total  = 0.0;
        }

        $particulars = $partyMap[$v['vch_txn_id']] ?? $v['acc_names'];

        $data[] = [
            'checkbox'        => '<input name="voucher_ids[]" class="checkbox hidden voucher_row"'
                               . ' data-id="'  . $v['vch_series_id'] . '||' . $v['vch_txn_id'] . '"'
                               . ' type="checkbox"'
                               . ' value="'    . $v['vch_series_id'] . '||' . $v['vch_txn_id'] . '">',
            'particulars'     => $particulars . '(' . $v['vch_txn_id'] . ')',
            'date'            => date('d-m-Y', strtotime($v['vch_date'])),
            'credit'          => ($credit_total > 0) ? formatAmount($credit_total) : '',
            'debit'           => ($debit_total  > 0) ? formatAmount($debit_total)  : '',
            'credit_total'    => parseAmount($credit_total),
            'debit_total'     => parseAmount($debit_total),
            'voucher_no'      => $voucher_count,
            'voucher_type'    => $v['vch_name'],
            'voucher_type_id' => $vch_type_id,
            'voucher_txn_id'  => $v['vch_txn_id'],
            'bom_id'          => 0,
            'bom_batches'     => 0,
        ];
    }

    return json_encode([
        'totalRecords' => $total_Records,
        'curPage'      => $pq_curPage,
        'data'         => $data,
    ]);
}

/**
 * Helper method to apply search filters (DRY principle)
 */
private function applySearchFilter($builder, string $search, string $search_column): void
{
    if ($search === '') {
        return;
    }

    $builder->groupStart();
    switch ($search_column) {
        case 'date':
            $builder->like('v.vch_date::text', date('Y-m-d', strtotime($search)));
            break;
        case 'voucher_type':
            $builder->like('LOWER(vt.vch_name)', strtolower($search), 'both', null, true);
            break;
    }
    $builder->groupEnd();
}
	
	function project_opening_balance($project_id,$to_date,$type){
		$to_date       = date('Y-m-d',strtotime($to_date));
		$builder = $this->db->table('prjtxnmstn');
		$builder->select("SUM(CASE 
                           WHEN project_txn_dr_cr = 1 AND project_txn_type = $type THEN project_txn_amt 
                           WHEN project_txn_dr_cr = 2 AND project_txn_type = $type THEN -project_txn_amt 
                           ELSE 0 
                          END) AS opening_balance", false
					)
				->where('project_id', $project_id)
				->where('vch_txn_id IS NULL')
				->where('txn_id IS NULL')
				->where('project_txn_type',$type);					
		if (!empty($to_date)) {
			$builder->where('project_txn_date <=', $to_date);
		}
		$query = $builder->get();
		$result = $query->getRowArray();
		
		return ($result['opening_balance'] ?? 0);
	}
	
	function load_project_account_ledger(){
	    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
        if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;		
		
		$project_id        = $_POST['project_id'];
		$project_txn_type  = $_POST['type'];
		$from_date         = date('Y-m-d',strtotime($_POST['from_date']));
		$to_date           = date('Y-m-d',strtotime($_POST['to_date']));
		
		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		
		$op = $this->db->table('prjoppybal')
					->select('project_op_bal')
					->where('project_id', $project_id)
					->where('cmpfymastr_id', $this->fy_id)
					->where('hobo_id', $this->bo_id)				
					->get()
					->getRow();
		$opening_balance = $op->project_op_bal ?? 0;
		$builder = $this->db->table('prjtxnmstn A');
		$builder->select("
				A.vch_txn_id,
				A.project_txn_date,
				A.project_id,
				A.project_txn_dr_cr,
				c.vch_type_id As vch_type_id,
				vt.vch_name AS voucher_type,
				A.project_txn_amt,
				CASE 
					WHEN A.project_txn_dr_cr = 1 AND A.project_txn_type = $project_txn_type THEN A.project_txn_amt
					ELSE 0
				END AS dr_amount,
				CASE 
					WHEN A.project_txn_dr_cr = 2 AND A.project_txn_type = $project_txn_type THEN A.project_txn_amt
					ELSE 0
				END AS cr_amount,
				(
				$opening_balance +
				SUM(
					CASE 
						WHEN A.project_txn_dr_cr = 1 AND A.project_txn_type = $project_txn_type THEN A.project_txn_amt
						WHEN A.project_txn_dr_cr = 2 AND A.project_txn_type = $project_txn_type THEN -A.project_txn_amt
						ELSE 0
					END
				) OVER (
					ORDER BY A.project_txn_date, A.vch_txn_id
					ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
				) 
				)AS running_balance
			", false);
		$builder->join('vchtxnconso c', 'c.vch_txn_id = A.vch_txn_id', 'left');
		$builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left');
		$builder->where('A.project_id', $project_id); // Your main account
		$builder->where('A.project_txn_type', $project_txn_type); 
		$builder->where('A.cmp_id', $this->company_id);
		$builder->where('A.hobo_id', $this->bo_id);
		$builder->where('A.vch_txn_id IS NOT NULL');
		$builder->where('A.project_txn_date >=', $from_date);
		$builder->where('A.project_txn_date <=', $to_date);
		// Clone for total count before applying limit
		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);

		// Pagination logic
		if ($pq_curPage == 0) $pq_curPage = 1;
		$offset = ($pq_rPP * ($pq_curPage - 1));

		if ($offset > $total_records) {
			$pq_curPage = ceil($total_records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}
		if ($offset < 0) {
			$offset = 0;
		}
		$builder->groupBy('A.vch_txn_id');
		$builder->orderBy('A.project_txn_date', 'ASC');
		$builder->orderBy('A.vch_txn_id', 'ASC');
		$builder->limit($pq_rPP, $offset);	
		$result = $builder->get()->getResultArray();
		$final = [];
		foreach($result as $key => $value)
		{
			$voucher_date = date("d-m-Y", strtotime($value['project_txn_date']));
			$final[] = [
				'voucher_txn_id'    => $value['vch_txn_id'],
				'voucher_type_id'   => $value['vch_type_id'],
				'voucher_type'      => $value['voucher_type'],
				'voucher_date'      => $voucher_date,
				'debit'             => $value['dr_amount'],
				'credit'            => $value['cr_amount'],
				'balance'           => $value['running_balance'],
				'balance_type'      => ($value['running_balance']<0)?'CR.':'DR.',
			];
		}
		return json_encode([
			'totalRecords'	=> $total_records,
			'curPage'	=> $pq_curPage,
			'data'	=> $final
		]);
	}
	
	function load_cost_centre_account_wise(){
	    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
        if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;							
		$from_date         = date('Y-m-d',strtotime($_POST['from_date']));
		$to_date           = date('Y-m-d',strtotime($_POST['to_date']));		
		$builder = $this->db->table('cctxnmstnn a', false);
       
		$builder->select("
			a.vch_txn_id,
			a.cc_txn_date,
			a.cc_id,
			a.cc_txn_dr_cr,
			c.vch_type_id AS vch_type_id,
			vt.vch_name AS voucher_type,
			a.cc_txn_amt,
			acc.acc_name,
			cms.cc_name,
			CASE 
				WHEN a.cc_txn_dr_cr = 1 THEN a.cc_txn_amt
				ELSE 0
			END AS dr_amount,
			CASE 
				WHEN a.cc_txn_dr_cr = 2 THEN a.cc_txn_amt
				ELSE 0
			END AS cr_amount,
			SUM(
				CASE 
					WHEN a.cc_txn_dr_cr = 1 THEN a.cc_txn_amt
					WHEN a.cc_txn_dr_cr = 2 THEN -a.cc_txn_amt
					ELSE 0
				END
			) OVER (
				ORDER BY a.cc_txn_date, a.vch_txn_id
				ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
			) AS running_balance
		", false);

		$builder->join('vchtxnconso c', 'c.vch_txn_id = a.vch_txn_id', 'left', false);
		$builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left', false);
		$builder->join('acctmaster acc', 'acc.acc_id = a.acc_id', 'left', false);
		$builder->join('ccmasternn cms', 'cms.cc_id = a.cc_id', 'left', false);

		$builder->where('a.cmp_id', $this->company_id);
		$builder->where('a.hobo_id', $this->bo_id);
		$builder->where('a.vch_txn_id IS NOT NULL', null, false);
		$builder->where('a.cc_txn_date >=', $from_date);
		$builder->where('a.cc_txn_date <=', $to_date);

		// Clone for total count before limit/offset/order
		$countBuilder  = clone $builder;
		$total_records = $countBuilder->countAllResults(false);

		// Pagination
		if ($pq_curPage == 0) $pq_curPage = 1;
		$offset = ($pq_rPP * ($pq_curPage - 1));
		if ($offset > $total_records && $total_records > 0) {
			$pq_curPage = (int) ceil($total_records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}
		if ($offset < 0) $offset = 0;

		// Window function doesn’t require GROUP BY
		$builder->orderBy('a.cc_txn_date', 'ASC');
		$builder->orderBy('a.vch_txn_id', 'ASC');
		$builder->limit($pq_rPP, $offset);

		$result = $builder->get()->getResultArray();

		$final = [];
		foreach ($result as $value) {
			$voucher_date = date("d-m-Y", strtotime($value['cc_txn_date']));
			$final[] = [
				'voucher_txn_id'  => $value['vch_txn_id'],
				'voucher_type_id' => $value['vch_type_id'],
				'voucher_type'    => $value['voucher_type'],
				'acc_name'        => $value['acc_name'],
				'cc_name'         => $value['cc_name'],
				'voucher_date'    => $voucher_date,
				'debit'           => formatAmount($value['dr_amount']),
				'credit'          => formatAmount($value['cr_amount']),
				'debit_total'     => parseAmount($value['dr_amount']),
				'credit_total'    => parseAmount($value['cr_amount']),
				'balance'         => formatAmount($value['running_balance']),
				'balance_type'    => ($value['running_balance'] < 0) ? 'CR.' : 'DR.',
			];
		}

		return json_encode([
			'totalRecords' => $total_records,
			'curPage'      => $pq_curPage,
			'data'         => $final
		]);
	}
	

public function load_cost_centre_wise()
{
    $pq_curPage = isset($_POST['pq_curpage']) ? (int)$_POST['pq_curpage'] : 1;
    $pq_rPP     = isset($_POST['pq_rpp']) ? (int)$_POST['pq_rpp'] : 10;

    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $from_date = date('Y-m-d', strtotime($_POST['from_date']));
    $to_date   = date('Y-m-d', strtotime($_POST['to_date']));

    /* =========================
       MAIN QUERY
    ========================= */

    $builder = $this->db->table('cctxnmstnn a');

    $builder->select("
        a.vch_txn_id,
        a.cc_txn_date,
        a.cc_id,
        a.cc_txn_dr_cr,
        c.vch_type_id,
        vt.vch_name AS voucher_type,
        a.cc_txn_amt,
        acc.acc_name,
        cms.cc_name,

        CASE 
            WHEN a.cc_txn_dr_cr = 1 THEN a.cc_txn_amt 
            ELSE 0 
        END AS dr_amount,

        CASE 
            WHEN a.cc_txn_dr_cr = 2 THEN a.cc_txn_amt 
            ELSE 0 
        END AS cr_amount,

        SUM(
            CASE 
                WHEN a.cc_txn_dr_cr = 1 THEN a.cc_txn_amt
                WHEN a.cc_txn_dr_cr = 2 THEN -a.cc_txn_amt
                ELSE 0
            END
        ) OVER (
            ORDER BY a.cc_txn_date, a.vch_txn_id
            ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
        ) AS running_balance
    ", false);

    /* =========================
       JOINS
    ========================= */

    $builder->join('vchtxnconso c', 'c.vch_txn_id = a.vch_txn_id', 'left');
    $builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left');
    $builder->join('acctmaster acc', 'acc.acc_id = a.acc_id', 'left');
    $builder->join('ccmasternn cms', 'cms.cc_id = a.cc_id', 'left');

    /* =========================
       WHERE CONDITIONS
    ========================= */

    $builder->where('a.cmp_id', $this->company_id);
    $builder->where('a.hobo_id', $this->bo_id);
    $builder->where('a.vch_txn_id IS NOT NULL', null, false);
    $builder->where('a.cc_txn_date >=', $from_date);
    $builder->where('a.cc_txn_date <=', $to_date);

    /* =========================
       TOTAL COUNT (for ParamQuery)
    ========================= */

    $countBuilder  = clone $builder;
    $total_records = $countBuilder->countAllResults(false);

    /* =========================
       PAGINATION
    ========================= */

    $offset = ($pq_curPage - 1) * $pq_rPP;
    if ($offset < 0) $offset = 0;

    $builder->orderBy('a.cc_txn_date', 'ASC');
    $builder->orderBy('a.vch_txn_id', 'ASC');
    $builder->limit($pq_rPP, $offset);

    $rows = $builder->get()->getResultArray();

    /* =========================
       FORMAT RESULT
    ========================= */

    $final = [];
    foreach ($rows as $row) {
        $final[] = [
            'voucher_txn_id'  => $row['vch_txn_id'],
            'voucher_type_id' => $row['vch_type_id'],
            'voucher_type'    => $row['voucher_type'],
            'acc_name'        => $row['acc_name'],
            'cc_name'         => $row['cc_name'],
            'voucher_date'    => date('d-m-Y', strtotime($row['cc_txn_date'])),
            'debit'           => formatAmount($row['dr_amount']),
            'credit'          => formatAmount($row['cr_amount']),
            'debit_total'     => parseAmount($row['dr_amount']),
            'credit_total'    => parseAmount($row['cr_amount']),
            'balance'         => formatAmount(abs($row['running_balance'])),
            'balance_type'    => ($row['running_balance'] < 0) ? 'CR.' : 'DR.',
        ];
    }

    return json_encode([
        'totalRecords' => $total_records,
        'curPage'      => $pq_curPage,
        'data'         => $final
    ]);
}


	public function load_cc_account_ledger()
{
    $pq_curPage = isset($_POST['pq_curpage']) ? (int)$_POST['pq_curpage'] : 1;
    $pq_rPP     = isset($_POST['pq_rpp']) ? (int)$_POST['pq_rpp'] : 10;

    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $cc_id     = (int) $_POST['cc_id'];
    $from_date = date('Y-m-d', strtotime($_POST['from_date']));
    $to_date   = date('Y-m-d', strtotime($_POST['to_date']));

    /* =========================
       OPENING BALANCE
    ========================= */

    $op = $this->db->table('ccoppybaln')
        ->select('cc_op_bal')
        ->where('cc_id', $cc_id)
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('hobo_id', $this->bo_id)
        ->get()
        ->getRow();

    $opening_balance = $op->cc_op_bal ?? 0;

    /* =========================
       MAIN QUERY
    ========================= */

    $builder = $this->db->table('cctxnmstnn a');

    $builder->select("
        a.vch_txn_id,
        a.cc_txn_date,
        a.cc_id,
        a.cc_txn_dr_cr,
        c.vch_type_id,
        vt.vch_name AS voucher_type,
        a.cc_txn_amt,

        CASE 
            WHEN a.cc_txn_dr_cr = 1 THEN a.cc_txn_amt 
            ELSE 0 
        END AS dr_amount,

        CASE 
            WHEN a.cc_txn_dr_cr = 2 THEN a.cc_txn_amt 
            ELSE 0 
        END AS cr_amount,

        (
            {$opening_balance} +
            SUM(
                CASE 
                    WHEN a.cc_txn_dr_cr = 1 THEN a.cc_txn_amt
                    WHEN a.cc_txn_dr_cr = 2 THEN -a.cc_txn_amt
                    ELSE 0
                END
            ) OVER (
                ORDER BY a.cc_txn_date, a.vch_txn_id
                ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
            )
        ) AS running_balance
    ", false);

    /* =========================
       JOINS
    ========================= */

    $builder->join('vchtxnconso c', 'c.vch_txn_id = a.vch_txn_id', 'left');
    $builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left');

    /* =========================
       WHERE CONDITIONS
    ========================= */

    $builder->where('a.cc_id', $cc_id);
    $builder->where('a.cmp_id', $this->company_id);
    $builder->where('a.hobo_id', $this->bo_id);
    $builder->where('a.vch_txn_id IS NOT NULL', null, false);
    $builder->where('a.cc_txn_date >=', $from_date);
    $builder->where('a.cc_txn_date <=', $to_date);

    /* =========================
       TOTAL RECORD COUNT
    ========================= */

    $countBuilder  = clone $builder;
    $total_records = $countBuilder->countAllResults(false);

    /* =========================
       PAGINATION
    ========================= */

    $offset = ($pq_curPage - 1) * $pq_rPP;
    if ($offset < 0) $offset = 0;

    $builder->orderBy('a.cc_txn_date', 'ASC');
    $builder->orderBy('a.vch_txn_id', 'ASC');
    $builder->limit($pq_rPP, $offset);

    $rows = $builder->get()->getResultArray();

    /* =========================
       FORMAT OUTPUT
    ========================= */

    $final = [];
    foreach ($rows as $row) {
        $final[] = [
            'voucher_txn_id'  => $row['vch_txn_id'],
            'voucher_type_id' => $row['vch_type_id'],
            'voucher_type'    => $row['voucher_type'],
            'voucher_date'    => date('d-m-Y', strtotime($row['cc_txn_date'])),
            'debit'           => formatAmount($row['dr_amount']),
            'credit'          => formatAmount($row['cr_amount']),
            'debit_total'     => parseAmount($row['dr_amount']),
            'credit_total'    => parseAmount($row['cr_amount']),
            'balance'         => formatAmount(abs($row['running_balance'])),
            'balance_type'    => ($row['running_balance'] < 0) ? 'CR.' : 'DR.',
        ];
    }

    return json_encode([
        'totalRecords' => $total_records,
        'curPage'      => $pq_curPage,
        'data'         => $final
    ]);
}

	
	
	function get_project_info($id){
	    $data = $this->db->table("projectmst")->select('project_id,project_name')->where('cmp_id',$this->company_id)->where('project_id', $id)->where('project_is_active',1)->get()->getRowArray();
        return $data;	
	}
	function get_cc_info($id){
	    $data = $this->db->table("ccmasternn")->select('cc_id,cc_name')->where('cmp_id',$this->company_id)->where('cc_id', $id)->where('cc_is_active',1)->get()->getRowArray();
        return $data;	
	}
	function cc_opening_balance($cc_id,$to_date,$type){
		$to_date       = date('Y-m-d',strtotime($to_date));
		$builder = $this->db->table('cctxnmstnn');
		$builder->select("SUM(CASE 
                           WHEN cc_txn_dr_cr = 1  THEN cc_txn_amt 
                           WHEN cc_txn_dr_cr = 2  THEN -cc_txn_amt 
                           ELSE 0 
                          END) AS opening_balance", false
					)
				->where('cc_id', $cc_id)
				->where('vch_txn_id IS NULL')
				->where('txn_id IS NULL');					
		if (!empty($to_date)) {
			$builder->where('cc_txn_date <=', $to_date);
		}
		$query = $builder->get();
		$result = $query->getRowArray();
		
		return ($result['opening_balance'] ?? 0);
	}
	function load_project_account_trial()
{
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $from_date        = date('Y-m-d', strtotime($_POST["from_date"]));
    $to_date          = date('Y-m-d', strtotime($_POST["to_date"]));
    $project_txn_type = (int)$_POST["type"];

    $search_column = '';
    $search        = '';
    if (!empty($_POST["pq_filter"])) {
        $pq_filter = json_decode($_POST["pq_filter"]);
        if (!empty($pq_filter->data[0])) {
            $search        = $pq_filter->data[0]->value ?? '';
            $search_column = $pq_filter->data[0]->dataIndx ?? '';
        }
    }

    // Data builder
    $builder = $this->db->table('prjtxnmstn a');
    $builder->select("
        a.project_id,
        pm.project_name,
        SUM(
            CASE
                WHEN a.vch_txn_id IS NULL AND a.project_txn_dr_cr = 1 AND a.project_txn_type = {$project_txn_type} THEN a.project_txn_amt
                WHEN a.vch_txn_id IS NULL AND a.project_txn_dr_cr = 2 AND a.project_txn_type = {$project_txn_type} THEN -a.project_txn_amt
                ELSE 0
            END
        ) AS opening_balance,
        SUM(
            CASE
                WHEN a.vch_txn_id IS NOT NULL AND a.project_txn_dr_cr = 1 AND a.project_txn_type = {$project_txn_type} THEN a.project_txn_amt
                ELSE 0
            END
        ) AS total_dr,
        SUM(
            CASE
                WHEN a.vch_txn_id IS NOT NULL AND a.project_txn_dr_cr = 2 AND a.project_txn_type = {$project_txn_type} THEN a.project_txn_amt
                ELSE 0
            END
        ) AS total_cr,
        (
            SUM(
                CASE
                    WHEN a.vch_txn_id IS NULL AND a.project_txn_dr_cr = 1 AND a.project_txn_type = {$project_txn_type} THEN a.project_txn_amt
                    WHEN a.vch_txn_id IS NULL AND a.project_txn_dr_cr = 2 AND a.project_txn_type = {$project_txn_type} THEN -a.project_txn_amt
                    ELSE 0
                END
            )
            +
            SUM(
                CASE
                    WHEN a.vch_txn_id IS NOT NULL AND a.project_txn_dr_cr = 1 AND a.project_txn_type = {$project_txn_type} THEN a.project_txn_amt
                    ELSE 0
                END
            )
            -
            SUM(
                CASE
                    WHEN a.vch_txn_id IS NOT NULL AND a.project_txn_dr_cr = 2 AND a.project_txn_type = {$project_txn_type} THEN a.project_txn_amt
                    ELSE 0
                END
            )
        ) AS final_balance
    ", false);

    $builder->join('projectmst pm', 'a.project_id = pm.project_id', 'left');
    $builder->where('a.project_txn_type', $project_txn_type);
    $builder->where('pm.project_is_active', 1);
    $builder->where('a.cmp_id', $this->company_id);
    $builder->where('a.hobo_id', $this->bo_id);
    $builder->where('a.project_txn_date >=', $from_date);
    $builder->where('a.project_txn_date <=', $to_date);

    if ($search !== '' && $search_column !== '') {
        $builder->like($search_column, $search);
    }

    // Count builder (distinct projects) with same aliases
    $countBuilder = $this->db->table('prjtxnmstn a');
    $countBuilder->select('COUNT(DISTINCT a.project_id) AS cnt', false);
    $countBuilder->join('projectmst pm', 'a.project_id = pm.project_id', 'left');
    $countBuilder->where('a.project_txn_type', $project_txn_type);
    $countBuilder->where('pm.project_is_active', 1);
    $countBuilder->where('a.cmp_id', $this->company_id);
    $countBuilder->where('a.hobo_id', $this->bo_id);
    $countBuilder->where('a.project_txn_date >=', $from_date);
    $countBuilder->where('a.project_txn_date <=', $to_date);
    if ($search !== '' && $search_column !== '') {
        $countBuilder->like($search_column, $search);
    }
    $total_records = (int)($countBuilder->get()->getRow()->cnt ?? 0);

    // Pagination
    $offset = $pq_rPP * ($pq_curPage - 1);
    if ($offset > $total_records) {
        $pq_curPage = ($total_records > 0) ? (int)ceil($total_records / $pq_rPP) : 1;
        $offset = $pq_rPP * ($pq_curPage - 1);
    }
    if ($offset < 0) $offset = 0;

    $builder->groupBy(['a.project_id', 'pm.project_name']);
    $builder->orderBy('pm.project_name', 'ASC');
    $builder->limit($pq_rPP, $offset);

    $result = $builder->get()->getResultArray();
    $final  = [];
    foreach ($result as $row) {
        $final[] = [
            'project_name'    => $row['project_name'],
            'project_id'      => $row['project_id'],
            'type'            => $project_txn_type,
            'debit'           => abs($row['total_dr']),
            'credit'          => abs($row['total_cr']),
            'balance'         => abs($row['final_balance']),
            'balance_type'    => ($row['final_balance'] < 0) ? 'CR.' : 'DR.',
            'op_balance'      => abs($row['opening_balance']),
            'op_balance_type' => ($row['opening_balance'] < 0) ? 'CR.' : 'DR.',
        ];
    }

    return json_encode([
        'totalRecords' => $total_records,
        'curPage'      => $pq_curPage,
        'data'         => $final,
    ]);
}
		
	function get_cc_list(){
		$builder = $this->db->table("ccmasternn"); 
		$builder->select('cc_id as id, cc_name as label, cc_name as value');
		$builder->where('cmp_id', $this->company_id);
		$builder->where('cc_is_active', 1);
		$result = $builder->get()->getResultArray();
		return $result;
	  }

}