<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Libraries\ERPtables;
use App\Libraries\externaldb;
class AccountsModel extends Model	{
  protected $CommonModel;
  protected $session;
  protected $company_id;
  protected $fy_id;
  protected $bo_id;

    public function __construct() {
       parent::__construct();        
       
	   $this->CommonModel   =  new CommonModel();
	   $this->externaldb    = new externaldb();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->fy_id         = $this->session->get('ses_comp_fy_id');
       $this->bo_id         = $this->session->get('ses_boid');
	   $this->contactaic_db = $this->externaldb->contactaic_db();
	   $this->univaictly    =  $this->externaldb->univaictly_db();
    } 
		
	function validate_account_bill_refs($account_names_array){
		$true_counter=0;
		if($account_names_array){
			foreach($account_names_array as $ref_name){
				$builder = $this->db->table('billmaster A');
				$builder->where('A.cmp_id', $this->company_id);
				$builder->where('A.bill_is_active', 1);
				$builder->where('LOWER(A.bill_ref_name)',strtolower(trim($ref_name)) );
				$total_records = $builder->countAllResults();
				
			//	echo $this->db->getlastquery();
				if($total_records==0)
				  $true_counter=$true_counter+1;				
			}
		}
		if($true_counter>0)
			$response =  true; // means fresh name
		else 
		  $response = false;	  // means data exists
		  
		  return json_encode($response);
	}
	
	function tax_category_dropdown($tax_cat_type=''){
	    $builder = $this->db->table("taxcatmstn t")
            ->select("t.tax_cat_is_active,t.tax_cat_mst_id, t.tax_cat_name, t.tax_cat_type, t.tax_cat_section, r.tax_cat_rate")
            ->join("taxcatrate r", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "left")
            ->where("t.cmp_id", $this->company_id)
            ->where("t.tax_cat_is_active", 1);
         if($tax_cat_type!='')   
            $builder->where("t.tax_cat_type", $tax_cat_type);
            $data  = $builder->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
		      $rate = parseAmount($row['tax_cat_rate']);
              $final_result[$row['tax_cat_mst_id']] = strtoupper(strtolower($row['tax_cat_name']));		   
	        }
        }
	 
	  return $final_result;	
     }
	
	function validate_account_sblgr_refs($account_names_array){
		$true_counter=0;
		if($account_names_array){
			foreach($account_names_array as $ref_name){
				$builder = $this->db->table('subacctmst A');
				$builder->where('A.cmp_id', $this->company_id);
				$builder->where('A.sub_acc_is_active', 1);
				$builder->where('LOWER(A.sub_acc_name)',strtolower(trim($ref_name)) );
				$total_records = $builder->countAllResults();
				
			//	echo $this->db->getlastquery();
				if($total_records==0)
				  $true_counter=$true_counter+1;				
			}
		}
		if($true_counter>0)
			$response =  true; // means fresh name
		else 
		  $response = false;	  // means data exists
		  
		  return json_encode($response);
	}
	
	function get_account_bill_refs(){
	 $data = $this->db->table('billmaster bm')
			->select('
			bm.bill_ref_id,
			bm.bill_ref_id as id,
			bm.bill_ref_name,
			bm.bill_ref_name as label,
			bm.bill_ref_name as value,			
			bm.bill_due_date,
			DATE_FORMAT(bm.bill_due_date, "%d-%m-%Y") as due_date,
			bop.bill_op_bal,
			CASE 
            WHEN bop.bill_op_bal < 0 THEN "cr"
            ELSE "dr"
        END as bill_drcr
		',false)
		->join('billoppybal bop', 'bop.bill_ref_id = bm.bill_ref_id', 'left') // left join to include all billmaster rows
		->where('bm.bill_ref_name !=', 'UNDEFINED')
		->where('bop.cmpfymastr_id',$this->fy_id)
		->where('bop.cmp_id',$this->company_id)
		->where('bop.cmp_id',$this->company_id)
		->where('bm.bill_is_active',1)
		->get()
		->getResultArray();
		return $data;
	}
	
	function get_account_sublgr_refs(){
		$data = $this->db->table('subacctmst bm')
			->select('
			bm.sub_acc_id,
			bm.sub_acc_id as id,
			bm.sub_acc_name,
			bm.sub_acc_name as label,
			bm.sub_acc_name as value,			
			bm.sub_due_date,
			DATE_FORMAT(bm.sub_due_date, "%d-%m-%Y") as due_date,
			bop.sub_acc_op_bal,
			CASE 
            WHEN bop.sub_acc_op_bal < 0 THEN "cr"
            ELSE "dr"
        END as sublgr_drcr
		',false)
		->join('suboppybal bop', 'bop.sub_acc_id = bm.sub_acc_id', 'left') // left join to include all billmaster rows
		->where('bm.sub_acc_name !=', 'UNDEFINED')
		->where('bop.cmpfymastr_id',$this->fy_id)
		->where('bop.cmp_id',$this->company_id)
		->where('bop.cmp_id',$this->company_id)
		->where('bm.sub_acc_is_active',1)
		->get()
		->getResultArray();
		return $data;
	}
	function check_account_isrectricted($account_id){
		$account_info = $this->account_info($account_id,'acc');
		if($account_info['acc_is_restrict']==2 || $account_info['acc_is_restrict']==3)
			return true;
		else 
			return false;
	}
	
	function get_sub_group_ids_list($array){
    if (!empty($array)) {
        $builder = $this->db->table("accgrpmstn acgrpmst");
        // Add join condition
        $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id", 'left');
        // Add conditions using the where() method for safety and clarity
        $builder->where('undercrsmt.crs_mst_type', 2);
        $builder->where('undercrsmt.cmp_id', $this->company_id);
        $builder->whereIn('undercrsmt.under_main_id', $array);
        
        $builder->select('acgrpmst.acc_grp_id');
        
        $data = $builder->get()->getResultArray();
        
        if ($data) {
            // Use array_column to get all 'acc_grp_id' values at once
            $new_ids = array_column($data, 'acc_grp_id');
            // Merge the new IDs with the existing ones
            $array = array_merge($array, $new_ids);
        }
    }
    return $array;
}
    
    function check_account_with_voucher($account_id){
        $builder = $this->db->table('accttxnmst A');
        $builder->where('A.cmp_id', $this->company_id);
		$builder->where('A.hobo_id', $this->bo_id);
		$builder->where('A.vch_txn_id >',0);
		$builder->where('A.vch_txn_id IS NOT NULL');
		$builder->where('A.txn_id IS NOT NULL');
		$builder->where('A.acc_id', $account_id);
		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);
		if($total_records)
		return true;
		else
		return false;        
    }
	
	function check_account_with_bbb($account_id){
        $builder = $this->db->table('billtxnmst A');
        $builder->where('A.cmp_id', $this->company_id);
		$builder->where('A.acc_id', $account_id);
		$builder->where('A.hobo_id', $this->bo_id);
		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);
		if($total_records)
		return true;
		else
		return false;
    }
	
	function check_account_with_sublger($account_id){
        $builder = $this->db->table('subacctxnm A');
        $builder->where('A.cmp_id', $this->company_id);
		$builder->where('A.acc_id', $account_id);
		$builder->where('A.hobo_id', $this->bo_id);
		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);
		if($total_records)
		return true;
		else
		return false;
    }
    
    function check_account_with_group($account_id){
        $builder = $this->db->table('acctmaster A');
        $builder->join("undercrsmt", "undercrsmt.crs_mst_id = A.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
        $builder->where('A.cmp_id', $this->company_id);
		$builder->where('undercrsmt.crs_mst_id', $account_id);
		$builder->where('A.acc_is_active',1);
		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);
		if($total_records)
		return true;
		else
		return false;
        
    }
    
    
     function get_cash_groups_list()
      {
        $groups = [23,21];
		$array = $this->get_sub_group_ids_list($groups); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','cash & cash equivalents','left')
				->orLike('LOWER(acc_grp_name)', 'bank od', 'left')
				->orLike('LOWER(acc_grp_name)', 'bank/od', 'left')
				->orLike('LOWER(acc_grp_name)', 'cash & cash', 'left')
				->where('cmp_id', $this->company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
        return array_unique($array);
      }
	  
	  function get_bbb_groups_list()
      {
        $groups = [16,22];
		$array = $this->get_sub_group_ids_list($groups); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')				
				->like('LOWER(acc_grp_name)','trade payable','left')
				->orlike('LOWER(acc_grp_name)','trade receivables','left')
				->where('cmp_id', $this->company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
		
        return array_unique($array);
      }
	  function get_cc_groups_list()
      {
        $groups = [7,11,13];
		$array = $this->get_sub_group_ids_list($groups); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','purchase','left')
				->like('LOWER(acc_grp_name)','direct expenses','left')
				->like('LOWER(acc_grp_name)','indirect expenses','left')
				->where('cmp_id', $this->company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
        return array_unique($array);
      }
	 
	function load_accounts_totals($account_id, $from_date, $to_date, $isconsoview = 0)
{
    // --- CORRECTED LINE ---
    // Pass the $isconsoview parameter to the opening balance function.
    $opening_balance = (float)$this->account_opening_balance($account_id, $from_date, 1, $isconsoview);

    $builder = $this->db->table('accttxnmst a');
    $builder->select("
        SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr_amount,
        SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr_amount,
        (
            {$opening_balance}
            +
            SUM(
                CASE
                    WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt
                    WHEN a.acc_txn_dr_cr = 2 THEN -a.acc_txn_amt
                    ELSE 0
                END
            )
        ) AS running_balance
    ", false);

    $builder->where('a.cmp_id', $this->company_id);
    $builder->where('a.acc_id', $account_id);
    $builder->where('a.acc_txn_type', 1);

    // This part is already correct and handles the consolidated view
    if ($isconsoview == 0) {
        $builder->where('a.hobo_id', $this->bo_id);
    }
    
    $builder->where('a.vch_txn_id >', 0);
    $builder->where('a.vch_txn_id IS NOT NULL', null, false);
    $builder->where('a.txn_id IS NOT NULL', null, false);

    $builder->where('a.acc_txn_date >=', $from_date);
    $builder->where('a.acc_txn_date <=', $to_date);

    $result = $builder->get()->getRowArray();

    $running_balance = (float) ($result['running_balance'] ?? $opening_balance); // Use opening balance as fallback
    $drcr = ($running_balance < 0) ? ' CR.' : ' DR.';

    return json_encode([
        'total_debit'     => formatAmount($result['dr_amount'] ?? 0),
        'total_credit'    => formatAmount($result['cr_amount'] ?? 0),
        'closing_balance' => formatAmount(abs($running_balance)) . $drcr,
    ]);
}
	
	public function load_accounts_ledger_condensed_with_columns(int $account_id, string $from_date, string $to_date, int $is_export, int $type,$compId=null,$boId=null,$fyId=null,$reportData=null)
{
	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
    // Helpers
    $sanitizeColKey = function (string $name, int $id): string {
        $key = preg_replace('/\s+/', '', $name);
        $key = preg_replace('/[^A-Za-z0-9_]/', '', $key);
        return $key . '_' . $id;
    };
    $fmt = function (float $v): string {
        if (function_exists('formatAmount')) return formatAmount($v);
        return '₹' . number_format($v, 2, '.', ',');
    };

    // Pagination
	 $is_export  = (int) $is_export;
	 if($reportData){
		 $pq_curPage = isset($reportData["pq_curpage"]) ? (int)$reportData["pq_curpage"] : 1;
    $pq_rPP     = isset($reportData["pq_rpp"])     ? (int)$reportData["pq_rpp"]     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;
   
    $offset     = ($pq_curPage - 1) * $pq_rPP;
    if ($offset < 0) $offset = 0;
	 }
	 else{
	$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"])     ? (int)$_POST["pq_rpp"]     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;  
    $offset     = ($pq_curPage - 1) * $pq_rPP;
    if ($offset < 0) $offset = 0;	 
	 }
    

    // Type & opening
    if ($type == 1) {
        $acc_txn_type    = 1;
        $opening_balance = $this->account_opening_balance($account_id, $from_date, 1,$sel_compId,$sel_boId,$sel_fyId);
    } else {
        $acc_txn_type    = 3;
        $opening_balance = $this->account_opening_balance($account_id, $from_date, 3,$sel_compId,$sel_boId,$sel_fyId);
    }

    // Voucher counter up to from_date
    $fy_start_date = date('Y', strtotime($from_date)) . '-04-01';
    $voucher_count = $this->db->table('vchtxnconso vc')
        ->select('vc.vch_txn_id')
        ->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
        ->where('vc.cmp_id',  $sel_compId)
        ->where('vc.hobo_id', $sel_boId)
        ->where('vc.vch_date >=', $fy_start_date)
        ->where('vc.vch_date <',  $from_date)
        ->where('cm.master_id', $account_id)
        ->where('cm.master_id_type', 'acc')
        ->distinct()
        ->countAllResults();

    // Main ledger: nested query to handle GROUP BY then window functions
    $ledgerSql = "
        SELECT *,
               SUM(net_amount) OVER (ORDER BY acc_txn_date, vch_txn_id) + ? AS running_balance
        FROM (
            SELECT
                a.vch_txn_id,
                a.acc_txn_date,
                c.vch_type_id,
                vt.vch_name   AS voucher_type,
                lnr.vch_long_narr AS long_narr,
                SUM(CASE WHEN a.acc_txn_dr_cr = 1 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS dr_amount,
                SUM(CASE WHEN a.acc_txn_dr_cr = 2 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS cr_amount,
                SUM(
                    CASE 
                        WHEN a.acc_txn_dr_cr = 1 AND a.acc_txn_type = ? THEN a.acc_txn_amt
                        WHEN a.acc_txn_dr_cr = 2 AND a.acc_txn_type = ? THEN -a.acc_txn_amt
                        ELSE 0
                    END
                ) AS net_amount,
                COUNT(*) OVER() AS window_total,
                ROW_NUMBER() OVER(ORDER BY a.acc_txn_date ASC, a.vch_txn_id ASC) AS rn
            FROM accttxnmst a
            LEFT JOIN vchtxnconso c ON c.vch_txn_id = a.vch_txn_id
            LEFT JOIN vchtypemst vt ON vt.vch_type_id = c.vch_type_id
            LEFT JOIN vchlongnar lnr ON lnr.vch_txn_id = c.vch_txn_id
            WHERE a.acc_id = ?
              AND a.acc_txn_type = ?
              AND a.cmp_id = ?
              AND a.hobo_id = ?
              AND a.vch_txn_id > 0
              AND a.vch_txn_id IS NOT NULL
              AND a.acc_txn_date >= ?
              AND a.acc_txn_date <= ?
            GROUP BY a.vch_txn_id, a.acc_txn_date, c.vch_type_id, vt.vch_name, lnr.vch_long_narr
        ) t
        WHERE (? = 1) OR (t.rn > ? AND t.rn <= (? + ?))
        ORDER BY t.acc_txn_date ASC, t.vch_txn_id ASC
    ";

    $ledgerRows = $this->db->query($ledgerSql, [
        $opening_balance,
        $acc_txn_type,
        $acc_txn_type,
        $acc_txn_type,
        $acc_txn_type,
        $account_id,
        $acc_txn_type,
        $sel_compId,
        $sel_boId,
        $from_date,
        $to_date,
        $is_export,
        $offset,
        $offset,
        $pq_rPP,
    ])->getResultArray();

    $total_rows = !empty($ledgerRows) ? (int) $ledgerRows[0]['window_total'] : 0;

    // Other accounts per voucher (primary account + bill ref + POS) limited to page vouchers
    $voucherIds = array_column($ledgerRows, 'vch_txn_id');
    $otherAccMap = $otherBillNoMap = $otherPOSMap = [];
    if (!empty($voucherIds)) {
        $otherBuilder = $this->db->table('cmptxnmstn c2');
        $otherBuilder->select("
            c2.vch_txn_id,
            (
                SELECT am2.acc_name
                FROM cmptxnmstn c3
                JOIN acctmaster am2 ON am2.acc_id = c3.master_id
                WHERE c3.master_id_type = 'acc'
                  AND c3.vch_txn_id = c2.vch_txn_id
                  AND c3.cmp_id = $sel_compId
                ORDER BY c3.txn_id ASC
                LIMIT 1
            ) AS primary_acc_name,
            MAX(
                CASE 
                    WHEN vc.vch_type_id IN (18,19,7,17,3) THEN gstroutsup.outsup_bill_ref_no
                    WHEN vc.vch_type_id IN (2,21,6,11,12) THEN gstrinwsup.inwsup_bill_ref_no
                    ELSE NULL
                END
            ) AS bill_ref_no,
            MAX(
                CASE 
                    WHEN vc.vch_type_id IN (18,19,7,17,3) THEN gstroutsup.outsup_pos
                    WHEN vc.vch_type_id IN (2,21,6,11,12) THEN gstrinwsup.inwsup_pos
                    ELSE NULL
                END
            ) AS pos_code
        ", false);

        $otherBuilder->join('acctmaster am', 'c2.master_id = am.acc_id', 'left');
        $otherBuilder->join('vchtxnconso vc', 'vc.vch_txn_id = c2.vch_txn_id', 'left');
        $otherBuilder->join('gstrinwsup',  'gstrinwsup.vch_txn_id  = vc.vch_txn_id', 'left');
        $otherBuilder->join('gstroutsup', 'gstroutsup.vch_txn_id = vc.vch_txn_id', 'left');

        $otherBuilder->where('c2.master_id_type', 'acc');
        $otherBuilder->where('c2.cmp_id', $sel_compId);
        $otherBuilder->where('vc.cmp_id',  $sel_compId);
        $otherBuilder->where('vc.hobo_id', $sel_boId);
        $otherBuilder->whereIn('c2.vch_txn_id', $voucherIds);
        $otherBuilder->where('am.acc_is_active', 1);
        $otherBuilder->groupBy('c2.vch_txn_id');

        $otherRows = $otherBuilder->get()->getResultArray();
        foreach ($otherRows as $row) {
            $otherAccMap[$row['vch_txn_id']]    = $row['primary_acc_name'] ?: '';
            $otherBillNoMap[$row['vch_txn_id']] = $row['bill_ref_no'];
            $otherPOSMap[$row['vch_txn_id']]    = $row['pos_code'];
        }
    }

    // Build main records
    $records         = [];
    $voucher_running = (int) $voucher_count;
    foreach ($ledgerRows as $idx => $row) {
        $dr = (float) $row['dr_amount'];
        $cr = (float) $row['cr_amount'];

        $voucher_running++;

        $other_acc_name = $otherAccMap[$row['vch_txn_id']] ?? '';
        $bill_ref_no    = $otherBillNoMap[$row['vch_txn_id']] ?? '';
        $pos_code       = $otherPOSMap[$row['vch_txn_id']] ?? '';

        $account_txn_amount = ($dr > 0) ? $dr : $cr;

        $records[] = [
            'voucher_txn_id'         => (int) $row["vch_txn_id"],
            'voucher_type_id'        => $row["vch_type_id"],
            'voucher_no'             => $voucher_running,
            'account_name'           => $other_acc_name,
            'txn_date'               => date('d-m-Y', strtotime($row["acc_txn_date"])),
            'voucher_type'           => $row["voucher_type"],
            'debit'                  => $dr > 0 ? $fmt($dr) : '',
            'debit_total'            => $dr,
            'credit'                 => $cr > 0 ? $fmt($cr) : '',
            'credit_total'           => $cr,
            'account_txn_amount'     => $fmt($account_txn_amount),
            'account_txn_amount_exp' => $account_txn_amount,
            'balance'                => $fmt(abs((float)$row['running_balance'])),
            'balance_total'          => abs((float)$row['running_balance']),
            'long_narration'         => $row['long_narr'],
            'balance_type'           => ((float)$row['running_balance'] < 0) ? 'CR.' : 'DR.',
            'bill_ref_no'            => $bill_ref_no,
            'pos_name'               => $pos_code
        ];
    }

    if (empty($records)) {
        return "{\"totalRecords\":0,\"curPage\":{$pq_curPage},\"data\":[],\"acc_columns\":[],\"acc_columnar_total\":[]}";
    }

    // Dynamic columnar part (only vouchers on current page)
    $voucherIds = array_column($records, 'voucher_txn_id');

    $cB = $this->db->table('cmptxnmstn c');
    $cB->select("
        c.vch_txn_id,
        c.master_id,
        am.acc_name AS col_name,
        am.bsd_id   AS am_bsd_id,
        CASE WHEN am.bsd_id IS NULL THEN 'acc' ELSE 'bsd' END AS mtype_derived,
        ax.acc_txn_amt,
        ax.acc_txn_dr_cr AS dr_cr_from_ax
    ");
    $cB->join('acctmaster am', 'am.acc_id = c.master_id', 'left');
    $cB->join(
        'accttxnmst ax',
        'ax.vch_txn_id = c.vch_txn_id
         AND ax.acc_id   = c.master_id
         AND ax.acc_txn_type = '.$acc_txn_type.'
         AND ax.cmp_id   = '.$sel_compId.'
         AND ax.hobo_id  = '.$sel_boId,
        'left'
    );
    $cB->whereIn('c.vch_txn_id', array_unique($voucherIds));
    $cB->where('c.master_id !=', $account_id); // exclude anchor
    $cB->orderBy('c.vch_txn_id', 'ASC')->orderBy('c.txn_id', 'ASC');

    $cmpRows = $cB->get()->getResultArray();

    $accColumns   = [];
    $cmpByVoucher = [];

    foreach ($cmpRows as $c) {
        $vid  = (int) $c['vch_txn_id'];
        $mid  = (int) $c['master_id'];
        $name = $c['col_name'] ?? '';
        if ($name === '') continue;

        $mtype = $c['mtype_derived'];
        $key   = $sanitizeColKey($name, $mid);

        if (!isset($accColumns[$key])) {
            $accColumns[$key] = [
                'accid'    => $mid,
                'type'     => $mtype,
                'acc_name' => $name,
            ];
        }

        $amt   = (float) $c['acc_txn_amt'];
        $drcr  = $c['dr_cr_from_ax'];
        if ($drcr === null) $drcr = 1;
        $signed = ($drcr == 2) ? -$amt : $amt;

        if (!isset($cmpByVoucher[$vid][$key])) $cmpByVoucher[$vid][$key] = 0.0;
        $cmpByVoucher[$vid][$key] += $signed;
    }

    $acc_columnar_total = [];
    foreach ($records as $i => $rec) {
        $vid = (int) $rec['voucher_txn_id'];
        $acc_columnar_total[$vid] = [];

        foreach ($accColumns as $key => $meta) {
            $signed = $cmpByVoucher[$vid][$key] ?? 0.0;
            if ($signed != 0.0) {
                $lbl  = ($signed < 0) ? 'Cr.' : 'Dr.';
                $records[$i][$key]       = $fmt(abs($signed)) . ' ' . $lbl;
                $records[$i]["{$key}_s"] = $signed;

                $acc_columnar_total[$vid][$key] = [
                    'actbal' => $signed,
                    'bal'    => abs($signed),
                    'crdr'   => $lbl,
                    'accid'  => $meta['accid'],
                ];
            }
        }
    }

    $totalRecordsOut = ($is_export == 0) ? $total_rows : count($records);

    if ($is_export == 0) {
        return "{\"totalRecords\":{$totalRecordsOut},\"acc_columns\":".json_encode($accColumns).",\"acc_columnar_total\":".json_encode($acc_columnar_total).",\"curPage\":{$pq_curPage},\"data\":".json_encode($records)."}";
    }

    return json_encode([
        'data'               => $records,
        'acc_columns'        => $accColumns,
        'acc_columnar_total' => $acc_columnar_total,
    ]);
}

	function load_accounts_sblgr_totals($account_id,$from_date,$to_date){
		$op = $this->db->table('suboppybal')
				->select('sub_acc_op_bal')
				->where('sub_acc_id', $account_id)
				->where('cmpfymastr_id', $this->fy_id)
				->where('hobo_id', $this->bo_id)				
				->get()
				->getRow();
		$opening_balance = $op->sub_acc_op_bal ?? 0;		
		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		
		$opening_balance = (float) $opening_balance; 
		$builder = $this->db->table('subacctxnm a');
		$builder->select("
			SUM(CASE WHEN sub_acc_txn_dr_cr = 1 THEN sub_acc_txn_amt ELSE 0 END) AS dr_amount,
			SUM(CASE WHEN sub_acc_txn_dr_cr = 2 THEN sub_acc_txn_amt ELSE 0 END) AS cr_amount,
			(
				$opening_balance +
				SUM(
					CASE 
						WHEN sub_acc_txn_dr_cr = 1   THEN sub_acc_txn_amt
						WHEN sub_acc_txn_dr_cr = 2  THEN -sub_acc_txn_amt
						ELSE 0
					END
				)
			) AS running_balance
		", false);
		$builder->where('a.cmp_id', $this->company_id);
		$builder->where('a.sub_acc_id', $account_id);
		$builder->where('a.vch_txn_id >',0);
		$builder->where('a.vch_txn_id IS NOT NULL');
		$builder->where('a.txn_id IS NOT NULL');
		$builder->where('a.sub_acc_txn_date >=', $from_date);
		$builder->where('a.sub_acc_txn_date <=', $to_date);				
		$builder->orderBy('a.sub_acc_txn_date', 'ASC');
		$builder->orderBy('a.txn_id', 'ASC');
		$query = $builder->get();
		$result = $query->getRowArray();		
		$drcr =($result['running_balance']<0)?' CR.':' DR.';
		 
		 return json_encode([
			'total_debit'     => formatAmount($result['dr_amount']) ?? formatAmount(0),
			'total_credit'    => formatAmount($result['cr_amount']) ?? formatAmount(0),
			'closing_balance' => formatAmount(abs($result['running_balance'])).$drcr,
		 ]); 
	}
	
	
	public function load_accounts_ledger_condensed(
    $account_id, $from_date, $to_date, $is_export, $type,
    $compId = null, $boId = null, $fyId = null, $reportData = null
) {
    // ──────────────────────────────────────────────────────────────
    // LOGGING SETUP
    // ──────────────────────────────────────────────────────────────
    $fn_start    = microtime(true);
    $timings     = []; // collects ['label' => ..., 'seconds' => ...]
    $logTag      = '[LedgerCondensed][acc=' . $account_id . ']';

    SaveErrorLog("$logTag ── FUNCTION START ── from=$from_date  to=$to_date  type=$type  is_export=$is_export  compId=$compId  boId=$boId  fyId=$fyId");

    // ──────────────────────────────────────────────────────────────
    // RESOLVE COMPANY / BO / FY IDS (no DB call)
    // ──────────────────────────────────────────────────────────────
     $sel_compId = $compId ?: $this->company_id;

 $sel_boId   = $boId   ?: $this->bo_id;
    $sel_fyId   = $fyId   ?: $this->fy_id;

    // ──────────────────────────────────────────────────────────────
    // PAGINATION
    // ──────────────────────────────────────────────────────────────
    $source     = $reportData ?: $_POST;
    $pq_curPage = max((int) ($source['pq_curpage'] ?? 1), 1);
    $pq_rPP     = max((int) ($source['pq_rpp']     ?? 10), 1);
    $offset      = ($pq_curPage - 1) * $pq_rPP;
    $is_export   = (int) $is_export;

    $fy_start_date = date('Y', strtotime($from_date)) . '-04-01';

    // ──────────────────────────────────────────────────────────────
    // QUERY 1 — Voucher counter up to from_date (exclusive)
    // ──────────────────────────────────────────────────────────────
    $t1 = microtime(true);

     $voucher_count = $this->db->table('vchtxnconso vc')
        ->select('vc.vch_txn_id')
        ->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
        ->where('vc.cmp_id',        $sel_compId)
        ->where('vc.hobo_id',       $sel_boId)
        ->where('vc.vch_date >=',   $fy_start_date)
        ->where('vc.vch_date <',    $from_date)
        ->where('cm.master_id',     $account_id)
        ->where('cm.master_id_type', 'acc')
        ->distinct()
        ->countAllResults();
     

    $timings[] = ['label' => 'Q1-VoucherCount', 'seconds' => microtime(true) - $t1];
    SaveErrorLog("$logTag Q1-VoucherCount => $voucher_count  |  " . round(end($timings)['seconds'], 4) . 's');

    // ──────────────────────────────────────────────────────────────
    // QUERY 2 — Opening balance
    // ──────────────────────────────────────────────────────────────
    $t2 = microtime(true);

    $acc_txn_type    = ($type == 1) ? 1 : 3;
    $ob_type         = ($type == 1) ? 1 : 3;
    $opening_balance = $this->account_opening_balance(
        $account_id, $from_date, $ob_type, 0,
        $sel_compId, $sel_boId, $sel_fyId
    );

    $timings[] = ['label' => 'Q2-OpeningBalance', 'seconds' => microtime(true) - $t2];
    SaveErrorLog("$logTag Q2-OpeningBalance => $opening_balance  |  " . round(end($timings)['seconds'], 4) . 's');

    // ──────────────────────────────────────────────────────────────
    // QUERY 3 — Main ledger (CTE with windowed balance + paging)
    // ──────────────────────────────────────────────────────────────
    $t3 = microtime(true);

    $ledgerSql = "
        WITH base AS (
            SELECT
                a.vch_txn_id,
                a.acc_txn_date,
                c.vch_type_id,
                vt.vch_name                    AS voucher_type,
                lnr.vch_long_narr              AS long_narr,
                string_agg(sn.vch_short_narr, '; ' ORDER BY sn.txn_id) AS short_narr,
                SUM(CASE WHEN a.acc_txn_dr_cr = 1 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS dr_amount,
                SUM(CASE WHEN a.acc_txn_dr_cr = 2 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS cr_amount
            FROM accttxnmst a
            LEFT JOIN vchtxnconso  c   ON c.vch_txn_id   = a.vch_txn_id
            LEFT JOIN vchtypemst   vt  ON vt.vch_type_id = c.vch_type_id
            LEFT JOIN vchlongnar   lnr ON lnr.vch_txn_id = c.vch_txn_id
            LEFT JOIN vchshrtnar   sn  ON sn.vch_txn_id  = a.vch_txn_id
                                       AND sn.txn_id      = a.txn_id
                                       AND sn.cmp_id      = a.cmp_id
            WHERE a.acc_id        = ?
              AND a.acc_txn_type  = ?
              AND a.cmp_id        = ?
              AND a.hobo_id       = ?
              AND a.vch_txn_id    > 0
              AND a.vch_txn_id   IS NOT NULL
              AND a.acc_txn_date >= ?
              AND a.acc_txn_date <= ?
            GROUP BY a.vch_txn_id, a.acc_txn_date, c.vch_type_id,
                     vt.vch_name, lnr.vch_long_narr
        ),
        numbered AS (
            SELECT
                b.*,
                COUNT(*) OVER()                                             AS window_total,
                ROW_NUMBER() OVER(ORDER BY b.acc_txn_date, b.vch_txn_id)   AS rn,
                SUM(b.dr_amount - b.cr_amount)
                    OVER (ORDER BY b.acc_txn_date, b.vch_txn_id
                          ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS delta_balance
            FROM base b
        )
        SELECT *
        FROM numbered
        WHERE (? = 1) OR (rn > ? AND rn <= (? + ?))
        ORDER BY acc_txn_date, vch_txn_id
    ";

    $ledgerRows = $this->db->query($ledgerSql, [
        $acc_txn_type,  // dr_amount CASE
        $acc_txn_type,  // cr_amount CASE
        $account_id,
        $acc_txn_type,
        $sel_compId,
        $sel_boId,
        $from_date,
        $to_date,
        $is_export,     // 1 = export (skip paging)
        $offset,
        $offset,
        $pq_rPP,
    ])->getResultArray();
    
   
    $total_rows  = !empty($ledgerRows) ? (int) $ledgerRows[0]['window_total'] : 0;
    $rowsFetched = count($ledgerRows);

    $timings[] = ['label' => 'Q3-MainLedger', 'seconds' => microtime(true) - $t3];
    SaveErrorLog( "$logTag Q3-MainLedger => rows=$rowsFetched  total=$total_rows  |  " . round(end($timings)['seconds'], 4) . 's');

    // ──────────────────────────────────────────────────────────────
    // QUERY 4 — Other accounts + bill ref (skip when no rows)
    // ──────────────────────────────────────────────────────────────
    $otherAccMap    = [];
    $otherBillNoMap = [];
    $voucherIds     = array_column($ledgerRows, 'vch_txn_id');

    if (!empty($voucherIds)) {
        $t4       = microtime(true);
        $curAccId = (int) $account_id;

        $otherRows = $this->db->table('cmptxnmstn c2')
            ->select("
                c2.vch_txn_id,
                (
                    SELECT array_to_string(
                             ARRAY(
                                 SELECT DISTINCT am2.acc_name
                                 FROM cmptxnmstn c3
                                 JOIN acctmaster am2 ON am2.acc_id = c3.master_id
                                 WHERE c3.master_id_type = 'acc'
                                   AND c3.vch_txn_id     = c2.vch_txn_id
                                   AND c3.cmp_id         = {$sel_compId}
                                   AND c3.master_id     <> {$curAccId}
                                 ORDER BY am2.acc_name
                                 LIMIT 2
                             ),
                             ', '
                           )
                ) AS other_accounts,
                MAX(
                    CASE
                        WHEN vc.vch_type_id IN (18,19,7,17,3)
                            THEN gstroutsup.outsup_bill_ref_no
                        WHEN vc.vch_type_id IN (2,21,6,11,12)
                            THEN gstrinwsup.inwsup_bill_ref_no
                        ELSE NULL
                    END
                ) AS bill_ref_no
            ", false)
            ->join('vchtxnconso vc',  'vc.vch_txn_id = c2.vch_txn_id',          'left')
            ->join('gstrinwsup',      'gstrinwsup.vch_txn_id = vc.vch_txn_id',  'left')
            ->join('gstroutsup',      'gstroutsup.vch_txn_id = vc.vch_txn_id',  'left')
            ->where('c2.master_id_type', 'acc')
            ->where('c2.cmp_id',         $sel_compId)
            ->where('vc.cmp_id',         $sel_compId)
            ->where('vc.hobo_id',        $sel_boId)
            ->whereIn('c2.vch_txn_id',   $voucherIds)
            ->groupBy('c2.vch_txn_id')
            ->get()
            ->getResultArray();

        foreach ($otherRows as $row) {
            $otherAccMap[$row['vch_txn_id']]    = $row['other_accounts'] ?: '';
            $otherBillNoMap[$row['vch_txn_id']] = $row['bill_ref_no'];
        }

        $timings[] = ['label' => 'Q4-OtherAccounts', 'seconds' => microtime(true) - $t4];
        SaveErrorLog( "$logTag Q4-OtherAccounts => " . count($otherRows) . " rows  |  " . round(end($timings)['seconds'], 4) . 's');
    } else {
        SaveErrorLog( "$logTag Q4-OtherAccounts => SKIPPED (no voucher rows)");
    }

    // ──────────────────────────────────────────────────────────────
    // BUILD RECORDS (pure in-memory — no DB)
    // ──────────────────────────────────────────────────────────────
    $t5      = microtime(true);
    $records = [];

    foreach ($ledgerRows as $row) {
        $dr = (float) $row['dr_amount'];
        $cr = (float) $row['cr_amount'];
        $vid = $row['vch_txn_id'];

        $running_balance_num = parseAmount($opening_balance + (float) $row['delta_balance']);
        $balance_type        = ($running_balance_num < 0) ? 'CR.' : 'DR.';
        $account_txn_amount  = ($dr > 0) ? $dr : $cr;

        $records[] = [
            'voucher_txn_id'     => $vid,
            'voucher_type_id'    => $row['vch_type_id'],
            'voucher_no'         => $voucher_count + (int) $row['rn'],
            'account_name'       => $otherAccMap[$vid]    ?? '',
            'txn_date'           => date('d-m-Y', strtotime($row['acc_txn_date'])),
            'voucher_type'       => $row['voucher_type'] . '(' . $vid . ')',
            'debit'              => $dr > 0 ? formatAmount($dr) : '',
            'debit_total'        => $dr,
            'credit'             => $cr > 0 ? formatAmount($cr) : '',
            'credit_total'       => $cr,
            'account_txn_amount' => formatAmount($account_txn_amount),
            'balance'            => formatAmount(abs($running_balance_num)),
            'balance_total'      => parseAmount(abs($running_balance_num)),
            'long_narration'     => $row['long_narr'],
            'short_narration'    => $row['short_narr'],
            'balance_type'       => $balance_type,
            'bill_ref_no'        => $otherBillNoMap[$vid] ?? '',
        ];
    }

    $timings[] = ['label' => 'BuildRecords', 'seconds' => microtime(true) - $t5];

    // ──────────────────────────────────────────────────────────────
    // FINAL TIMING SUMMARY
    // ──────────────────────────────────────────────────────────────
    $fn_total = microtime(true) - $fn_start;

    $summaryLines = ["$logTag ── TIMING SUMMARY ──"];
    foreach ($timings as $t) {
        $pct = ($fn_total > 0) ? round(($t['seconds'] / $fn_total) * 100, 1) : 0;
        $summaryLines[] = sprintf(
            "   %-25s %8.4fs   (%5.1f%%)",
            $t['label'], $t['seconds'], $pct
        );
    }
    $summaryLines[] = sprintf("   %-25s %8.4fs   (100%%)", 'TOTAL', $fn_total);
    $summaryLines[] = "$logTag ── FUNCTION END ──";

    SaveErrorLog( implode("\n", $summaryLines));

    // ──────────────────────────────────────────────────────────────
    // RETURN
    // ──────────────────────────────────────────────────────────────
    return [
        'totalRecords' => ($is_export == 0) ? $total_rows : count($records),
        'curPage'      => $pq_curPage,
        'data'         => $records,
    ];
}
	
	public function load_accounts_ledger_condensed_03_march_2026($account_id, $from_date, $to_date, $is_export, $type,$compId=null, $boId=null,$fyId=null,$reportData=null)
{
	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
		
    // Pagination
	 if($reportData){
		  $pq_curPage = isset($reportData['pq_curpage']) ? (int) $reportData['pq_curpage'] : (int) ($_POST['pq_curpage'] ?? 1);
    $pq_rPP     = isset($reportData['pq_rpp'])     ? (int) $reportData['pq_rpp']     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;
	$offset    = ($pq_curPage - 1) * $pq_rPP;
    if ($offset < 0) $offset = 0;
	 }
	 else{
		$pq_curPage = isset($_POST['pq_curpage']) ? (int) $_POST['pq_curpage'] : (int) ($_POST['pq_curpage'] ?? 1);
    $pq_rPP     = isset($_POST['pq_rpp'])     ? (int) $_POST['pq_rpp']     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;
	$offset    = ($pq_curPage - 1) * $pq_rPP;
    if ($offset < 0) $offset = 0; 
	 }
   

    $is_export = (int) $is_export;
    

    $fy_start_date = date('Y', strtotime($from_date)) . '-04-01';

    // 1) Voucher counter up to from_date (exclusive)
    $voucher_count = $this->db->table('vchtxnconso vc')
        ->select('vc.vch_txn_id')
        ->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
        ->where('vc.cmp_id',  $sel_compId)
        ->where('vc.hobo_id', $sel_boId)
        ->where('vc.vch_date >=', $fy_start_date)
        ->where('vc.vch_date <',  $from_date)
        ->where('cm.master_id', $account_id)
        ->where('cm.master_id_type', 'acc')
        ->distinct()
        ->countAllResults();

    // 2) Opening balance & acc_txn_type
    if ($type == 1) {
		$opening_balance = $this->account_opening_balance($account_id, $from_date, 1,0,$sel_compId,$sel_boId,$sel_fyId);
        $acc_txn_type    = 1;
    } else {
        $opening_balance = $this->account_opening_balance($account_id, $from_date, 3,0,$sel_compId,$sel_boId,$sel_fyId);
        $acc_txn_type    = 3;
    }

    // 3) Main ledger query with windowed totals and running balance; optional paging when not exporting
    $ledgerSql = "
        WITH base AS (
            SELECT
                a.vch_txn_id,
                a.acc_txn_date,
                c.vch_type_id,
                vt.vch_name        AS voucher_type,
                lnr.vch_long_narr  AS long_narr,
                string_agg(sn.vch_short_narr, '; ' ORDER BY sn.txn_id) AS short_narr,
                SUM(CASE WHEN a.acc_txn_dr_cr = 1 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS dr_amount,
                SUM(CASE WHEN a.acc_txn_dr_cr = 2 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS cr_amount
            FROM accttxnmst a
            LEFT JOIN vchtxnconso c ON c.vch_txn_id = a.vch_txn_id
            LEFT JOIN vchtypemst vt ON vt.vch_type_id = c.vch_type_id
            LEFT JOIN vchlongnar lnr ON lnr.vch_txn_id = c.vch_txn_id
            /* short narration only for THIS account row */
            LEFT JOIN vchshrtnar sn
                   ON sn.vch_txn_id = a.vch_txn_id
                  AND sn.txn_id     = a.txn_id
                  AND sn.cmp_id     = a.cmp_id
            WHERE a.acc_id = ?
              AND a.acc_txn_type = ?
              AND a.cmp_id = ?
              AND a.hobo_id = ?
              AND a.vch_txn_id > 0
              AND a.vch_txn_id IS NOT NULL
              AND a.acc_txn_date >= ?
              AND a.acc_txn_date <= ?
            GROUP BY a.vch_txn_id, a.acc_txn_date, c.vch_type_id, vt.vch_name, lnr.vch_long_narr
        ),
        numbered AS (
            SELECT
                b.*,
                COUNT(*) OVER() AS window_total,
                ROW_NUMBER() OVER(ORDER BY b.acc_txn_date, b.vch_txn_id) AS rn,
                SUM(b.dr_amount - b.cr_amount) OVER (ORDER BY b.acc_txn_date, b.vch_txn_id
                                                      ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS delta_balance
            FROM base b
        )
        SELECT *
        FROM numbered
        WHERE (? = 1) OR (rn > ? AND rn <= (? + ?))
        ORDER BY acc_txn_date, vch_txn_id
    ";

    $ledgerRows = $this->db->query($ledgerSql, [
        $acc_txn_type, // dr_amount
        $acc_txn_type, // cr_amount
        $account_id,
        $acc_txn_type,
        $sel_compId,
        $sel_boId,
        $from_date,
        $to_date,
        $is_export,
        $offset,
        $offset,
        $pq_rPP,
    ])->getResultArray();

    $total_rows = !empty($ledgerRows) ? (int) $ledgerRows[0]['window_total'] : 0;

    // 4) Other accounts: only two names (party + first other), fixed ORDER BY error
    $voucherIds     = array_column($ledgerRows, 'vch_txn_id');
    $otherAccMap    = [];
    $otherBillNoMap = [];

    if (!empty($voucherIds)) {
        $curAccId = (int) $account_id;
        $otherBuilder = $this->db->table('cmptxnmstn c2');

        $otherBuilder->select("
            c2.vch_txn_id,
            (
                SELECT array_to_string(
                         ARRAY(
                             SELECT DISTINCT am2.acc_name
                             FROM cmptxnmstn c3
                             JOIN acctmaster am2 ON am2.acc_id = c3.master_id
                             WHERE c3.master_id_type = 'acc'
                               AND c3.vch_txn_id = c2.vch_txn_id
                               AND c3.cmp_id = $sel_compId
                               AND c3.master_id <> {$curAccId}
                             ORDER BY am2.acc_name
                             LIMIT 2
                         ),
                         ', '
                       )
            ) AS other_accounts,
            MAX(
                CASE 
                    WHEN vc.vch_type_id IN (18,19,7,17,3)
                        THEN gstroutsup.outsup_bill_ref_no
                    WHEN vc.vch_type_id IN (2,21,6,11,12)
                        THEN gstrinwsup.inwsup_bill_ref_no
                    ELSE NULL
                END
            ) AS bill_ref_no
        ", false);

        $otherBuilder->join('vchtxnconso vc', 'vc.vch_txn_id = c2.vch_txn_id', 'left');
        $otherBuilder->join('gstrinwsup',  'gstrinwsup.vch_txn_id  = vc.vch_txn_id', 'left');
        $otherBuilder->join('gstroutsup', 'gstroutsup.vch_txn_id = vc.vch_txn_id', 'left');

        $otherBuilder->where('c2.master_id_type', 'acc');
        $otherBuilder->where('c2.cmp_id', $sel_compId);
        $otherBuilder->where('vc.cmp_id',  $sel_compId);
        $otherBuilder->where('vc.hobo_id',$sel_boId);
        $otherBuilder->whereIn('c2.vch_txn_id', $voucherIds);
        $otherBuilder->groupBy('c2.vch_txn_id');

        $otherRows = $otherBuilder->get()->getResultArray();
		
        foreach ($otherRows as $row) {
            $otherAccMap[$row['vch_txn_id']]    = $row['other_accounts'] ?: '';
            $otherBillNoMap[$row['vch_txn_id']] = $row['bill_ref_no'];
        }
    }

    // 5) Build records with continuous voucher_no and running balance
    $records = [];
    foreach ($ledgerRows as $row) {
        $dr = (float) $row['dr_amount'];
        $cr = (float) $row['cr_amount'];

        $other_acc_name = $otherAccMap[$row['vch_txn_id']] ?? '';
        $bill_ref_no    = $otherBillNoMap[$row['vch_txn_id']] ?? '';

        // Voucher no continues: vouchers before from_date + rn of this row
        $voucher_no = $voucher_count + (int)$row['rn'];

        // Running balance = opening_balance + cumulative delta
        $running_balance_num = parseAmount($opening_balance + (float)$row['delta_balance']);
        $balance_type        = ($running_balance_num < 0) ? 'CR.' : 'DR.';

        $account_txn_amount = ($dr > 0) ? $dr : $cr;

        $records[] = [
            'voucher_txn_id'     => $row["vch_txn_id"],
            'voucher_type_id'    => $row["vch_type_id"],
            'voucher_no'         => $voucher_no,
            'account_name'       => $other_acc_name, // up to 2 accounts (party + one more)
            'txn_date'           => date('d-m-Y', strtotime($row["acc_txn_date"])),
            'voucher_type'       => $row["voucher_type"].'('.$row["vch_txn_id"].')',
            'debit'              => $dr > 0 ? formatAmount($dr) : '',
            'debit_total'        => $dr,
            'credit'             => $cr > 0 ? formatAmount($cr) : '',
            'credit_total'       => $cr,
            'account_txn_amount' => formatAmount($account_txn_amount),
            'balance'            => formatAmount(abs($running_balance_num)),
            'balance_total'      => parseAmount(abs($running_balance_num)),
            'long_narration'     => $row['long_narr'],
            'short_narration'    => $row['short_narr'],
            'balance_type'       => $balance_type,
            'bill_ref_no'        => $bill_ref_no
        ];
    }

    // totalRecords: window_total for grid; for export use the full row count
    $totalRecordsOut = ($is_export == 0) ? $total_rows : count($records);

    return [
        "totalRecords" => $totalRecordsOut,
        "curPage"      => $pq_curPage,
        "data"         => $records
    ];
}


public function load_accounts_ledger_consolidated($account_id, $from_date, $to_date, $is_export)
	{
		// Pagination (from $_POST)
		$pq_curPage = isset($_POST['pq_curpage']) ? (int) $_POST['pq_curpage'] : 1;
		$pq_rPP     = isset($_POST['pq_rpp'])     ? (int) $_POST['pq_rpp']     : 10;
		if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1)     $pq_rPP     = 10;

		$is_export  = (int) $is_export;
		$offset     = ($pq_curPage - 1) * $pq_rPP;
		if ($offset < 0) $offset = 0;

		$type          = 1; // consolidated is always normal ledger, not memo
		$acc_txn_type  = 1;
		$fy_start_date = date('Y', strtotime($from_date)) . '-04-01';

		// 1) Voucher counter up to from_date (exclusive)
		$voucher_count = $this->db->table('vchtxnconso vc')
			->select('vc.vch_txn_id')
			->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
			->where('vc.cmp_id',  $this->company_id)
			->where('vc.vch_date >=', $fy_start_date)
			->where('vc.vch_date <',  $from_date)
			->where('cm.master_id', $account_id)
			->where('cm.master_id_type', 'acc')
			->distinct()
			->countAllResults();

		// 2) Opening balance
		$opening_balance = $this->account_opening_balance($account_id, $from_date, $acc_txn_type);

		// 3) Main ledger query (windowed total + SQL pagination, positional placeholders for Postgres)
		$ledgerSql = "
			SELECT *
			FROM (
				SELECT
					a.vch_txn_id,
					a.acc_txn_date,
					c.vch_type_id,
					vt.vch_name   AS voucher_type,
					lnr.vch_long_narr AS long_narr,
					hm.hobo_name  AS branch_name,
					SUM(CASE WHEN a.acc_txn_dr_cr = 1 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS dr_amount,
					SUM(CASE WHEN a.acc_txn_dr_cr = 2 AND a.acc_txn_type = ? THEN a.acc_txn_amt ELSE 0 END) AS cr_amount,
					COUNT(*) OVER() AS window_total,
					ROW_NUMBER() OVER(ORDER BY a.acc_txn_date ASC, a.vch_txn_id ASC) AS rn
				FROM accttxnmst a
				LEFT JOIN vchtxnconso c ON c.vch_txn_id = a.vch_txn_id
				LEFT JOIN vchtypemst vt ON vt.vch_type_id = c.vch_type_id
				LEFT JOIN vchlongnar lnr ON lnr.vch_txn_id = c.vch_txn_id
				LEFT JOIN hobomaster hm ON hm.hobo_id = c.hobo_id
				WHERE a.acc_id = ?
				  AND a.acc_txn_type = ?
				  AND a.cmp_id = ?
				  AND a.vch_txn_id > 0
				  AND a.vch_txn_id IS NOT NULL
				  AND a.acc_txn_date >= ?
				  AND a.acc_txn_date <= ?
				GROUP BY a.vch_txn_id, a.acc_txn_date, c.vch_type_id, vt.vch_name, lnr.vch_long_narr, hm.hobo_name
			) t
			WHERE (? = 1) OR (t.rn > ? AND t.rn <= (? + ?))
			ORDER BY t.acc_txn_date ASC, t.vch_txn_id ASC
		";

		$ledgerRows = $this->db->query($ledgerSql, [
			$acc_txn_type, // dr_amount
			$acc_txn_type, // cr_amount
			$account_id,
			$acc_txn_type,
			$this->company_id,
			$from_date,
			$to_date,
			$is_export,
			$offset,
			$offset,
			$pq_rPP,
		])->getResultArray();

		$total_rows = !empty($ledgerRows) ? (int) $ledgerRows[0]['window_total'] : 0;

		// 4) Other accounts (for vouchers on this page only)
		$voucherIds = array_column($ledgerRows, 'vch_txn_id');
		$otherAccMap    = [];
		$otherBillNoMap = [];

		if (!empty($voucherIds)) {
			$otherBuilder = $this->db->table('cmptxnmstn c2');
			$otherBuilder->select("
				c2.vch_txn_id,
				(
					SELECT am2.acc_name
					FROM cmptxnmstn c3
					JOIN acctmaster am2 ON am2.acc_id = c3.master_id
					WHERE c3.master_id_type = 'acc'
					  AND c3.vch_txn_id = c2.vch_txn_id
					  AND c3.cmp_id = {$this->company_id}
					ORDER BY c3.txn_id ASC
					LIMIT 1
				) AS primary_acc_name,
				MAX(
					CASE 
						WHEN vc.vch_type_id IN (18,19,7,17,3)
							THEN gstroutsup.outsup_bill_ref_no
						WHEN vc.vch_type_id IN (2,21,6,11,12)
							THEN gstrinwsup.inwsup_bill_ref_no
						ELSE NULL
					END
				) AS bill_ref_no
			", false);

			$otherBuilder->join('acctmaster am', 'c2.master_id = am.acc_id', 'left');
			$otherBuilder->join('vchtxnconso vc', 'vc.vch_txn_id = c2.vch_txn_id', 'left');
			$otherBuilder->join('gstrinwsup',  'gstrinwsup.vch_txn_id  = vc.vch_txn_id', 'left');
			$otherBuilder->join('gstroutsup', 'gstroutsup.vch_txn_id = vc.vch_txn_id', 'left');

			$otherBuilder->where('c2.master_id_type', 'acc');
			$otherBuilder->where('c2.cmp_id', $this->company_id);
			$otherBuilder->where('vc.cmp_id',  $this->company_id);
			$otherBuilder->whereIn('c2.vch_txn_id', $voucherIds);
			$otherBuilder->where('am.acc_is_active', 1);
			$otherBuilder->groupBy('c2.vch_txn_id');

			$otherRows = $otherBuilder->get()->getResultArray();

			foreach ($otherRows as $row) {
				$otherAccMap[$row['vch_txn_id']]    = $row['primary_acc_name'] ?: '';
				$otherBillNoMap[$row['vch_txn_id']] = $row['bill_ref_no'];
			}
		}

		// 5) Build records & running balance
		$records         = [];
		$running_balance = (float) $opening_balance;
		$voucher_running = (int) $voucher_count;

		foreach ($ledgerRows as $row) {
			$dr = (float) $row['dr_amount'];
			$cr = (float) $row['cr_amount'];

			if ($dr > 0) {
				$running_balance += $dr;
			} elseif ($cr > 0) {
				$running_balance -= $cr;
			}

			$voucher_running++;

			$other_acc_name = $otherAccMap[$row['vch_txn_id']] ?? '';
			$bill_ref_no    = $otherBillNoMap[$row['vch_txn_id']] ?? '';

			$account_txn_amount = ($dr > 0) ? $dr : $cr;

			$records[] = [
				'voucher_txn_id'     => $row["vch_txn_id"],
				'voucher_type_id'    => $row["vch_type_id"],
				'branch_name'        => $row['branch_name'],
				'voucher_no'         => $voucher_running,
				'account_name'       => $other_acc_name,
				'txn_date'           => date('d-m-Y', strtotime($row["acc_txn_date"])),
				'voucher_type'       => $row["voucher_type"],
				'debit'              => $dr > 0 ? formatAmount($dr) : '',
				'debit_total'        => $dr,
				'credit'             => $cr > 0 ? formatAmount($cr) : '',
				'credit_total'       => $cr,
				'account_txn_amount' => formatAmount($account_txn_amount),
				'balance'            => formatAmount(abs($running_balance)),
				'balance_total'      => abs($running_balance),
				'long_narration'     => $row['long_narr'],
				'balance_type'       => ($running_balance < 0) ? 'CR.' : 'DR.',
				'bill_ref_no'        => $bill_ref_no
			];
		}

		$totalRecordsOut = ($is_export == 0) ? $total_rows : count($records);

		// Return JSON-ready array
		return [
			"totalRecords" => $totalRecordsOut,
			"curPage"      => $pq_curPage,
			"data"         => $records
		];
	}

	 function load_accounts_subledger_condensed($account_id,$from_date, $to_date,$is_export,$type){
	    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
        if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;		
		$fy_start_date = date('Y', strtotime($from_date)) . '-04-01';
		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		
		
				
		
			$op = $this->db->table('suboppybal')
					->select('sub_acc_op_bal')
					->where('sub_acc_id', $account_id)
					->where('cmpfymastr_id', $this->fy_id)
					->where('hobo_id', $this->bo_id)				
					->get()
					->getRow();
			$opening_balance = $op->sub_acc_op_bal ?? 0;
			$acc_txn_type=1;
		
				
		$builder = $this->db->table('subacctxnm A');
		$builder->select("
				A.vch_txn_id,
				A.sub_acc_txn_date,
				A.acc_id,
				A.sub_acc_txn_dr_cr,
				A.sub_acc_txn_narr,
				A.sub_acc_txn_amt,
				CASE 
					WHEN A.sub_acc_txn_dr_cr = 1 THEN A.sub_acc_txn_amt
					ELSE 0
				END AS dr_amount,
				CASE 
					WHEN A.sub_acc_txn_dr_cr = 2  THEN A.sub_acc_txn_amt
					ELSE 0
				END AS cr_amount,								
				(
				$opening_balance +
				SUM(
					CASE 
						WHEN A.sub_acc_txn_dr_cr = 1  THEN A.sub_acc_txn_amt
						WHEN A.sub_acc_txn_dr_cr = 2 THEN -A.sub_acc_txn_amt
						ELSE 0
					END
				) OVER (
					ORDER BY A.sub_acc_txn_date, A.sub_acc_id
					ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
				) 
				)AS running_balance
			", false);
		$builder->join('vchtxnconso c', 'c.vch_txn_id = A.vch_txn_id', 'left');
		$builder->join('subacctmst AM', 'A.sub_acc_id = AM.sub_acc_id', 'left');
		$builder->where('A.sub_acc_id', $account_id); // Your main account		
		$builder->where('AM.sub_acc_is_active',1);
		$builder->where('A.cmp_id', $this->company_id);
		$builder->where('A.hobo_id', $this->bo_id);
		$builder->where('A.vch_txn_id >',0);
		$builder->where('A.vch_txn_id IS NOT NULL');
		$builder->where('A.sub_acc_txn_date >=', $from_date);
		$builder->where('A.sub_acc_txn_date <=', $to_date);
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
		$builder->orderBy('A.sub_acc_txn_date', 'ASC');
		$builder->orderBy('A.vch_txn_id', 'ASC');
		$builder->limit($pq_rPP, $offset);	
		$result = $builder->get()->getResultArray();
		
		$records = [];
			
		foreach($result as $key => $value)
        {  
		   
		    $account_txn_amount = $value["sub_acc_txn_amt"];
			$records[] = [
			  'voucher_txn_id'     => $value["vch_txn_id"],
			  'txn_date'           => $value["sub_acc_txn_date"],			 
			  'debit'              => $value["dr_amount"] >0 ? formatAmount($value["dr_amount"]):'',
			  'debit_total'        => $value["dr_amount"],
			  'credit'             => $value["cr_amount"]>0 ? formatAmount($value["cr_amount"]):'',
			  'credit_total'       => $value["cr_amount"],
			  'account_txn_amount' => formatAmount($account_txn_amount),
			  'balance'            => formatAmount(abs($value['running_balance'])),
			  'balance_total'      => abs($value['running_balance']),
			  'long_narration'	   => $value['sub_acc_txn_narr'],
			  'balance_type'       => ($value['running_balance']<0)?'CR.':'DR.'
			  ];
		}		
	   return  "{\"totalRecords\":" .$total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}";        
     }
	
  public function load_accounts_ledger_detailed($account_id, $from_date, $to_date, $is_export, $type,$compId=null,$boId=null,$fyId=null,$reportData=null)
{
	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
		
    // Pagination (from $_POST)
	if($reportData){
		 $pq_curPage = isset($reportData['pq_curpage']) ? (int) $reportData['pq_curpage'] : 1;
    $pq_rPP     = isset($reportData['pq_rpp'])     ? (int) $reportData['pq_rpp']     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;
	}
	else{
		 $pq_curPage = isset($_POST['pq_curpage']) ? (int) $_POST['pq_curpage'] : 1;
    $pq_rPP     = isset($_POST['pq_rpp'])     ? (int) $_POST['pq_rpp']     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;
	}
   

    $is_export  = (int) $is_export;
    $offset     = ($pq_curPage - 1) * $pq_rPP;
    if ($offset < 0) $offset = 0;

    // Type -> acc_txn_type
    if ($type == 1) {
        $acc_txn_type    = 1;
        $opening_balance = $this->account_opening_balance($account_id, $from_date, 1,$sel_compId,$sel_boId,$sel_fyId);
    } else {
        $acc_txn_type    = 3;
        $opening_balance = $this->account_opening_balance($account_id, $from_date, 3,$sel_compId,$sel_boId,$sel_fyId);
    }

    // Voucher counter up to from_date
    $fy_start_date = date('Y', strtotime($from_date)) . '-04-01';
    $voucher_count = $this->db->table('vchtxnconso vc')
        ->select('vc.vch_txn_id')
        ->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
        ->where('vc.cmp_id',  $sel_compId)
        ->where('vc.hobo_id', $sel_boId)
        ->where('vc.vch_date >=', $fy_start_date)
        ->where('vc.vch_date <',  $from_date)
        ->where('cm.master_id', $account_id)
        ->where('cm.master_id_type', 'acc')
        ->distinct()
        ->countAllResults();
    if ($voucher_count == 0) $voucher_count = 1;

    // Main query: windowed running balance & pagination in SQL (positional placeholders for Postgres)
    $sql = "
        SELECT *
        FROM (
            SELECT
                vc.vch_txn_id,
                vc.vch_type_id,
                ct.txn_id,
                vc.vch_date,
                ct.master_id,
                ct.master_id_type,
                at.acc_txn_amt,
                at.acc_txn_dr_cr,
                vtm.vch_name AS vch_type_name,
                vn.vch_long_narr,
                am.acc_name AS master_acc_name,
                CASE WHEN at.acc_txn_dr_cr = 1 AND at.acc_txn_type = ? THEN at.acc_txn_amt ELSE 0 END AS dr_amount,
                CASE WHEN at.acc_txn_dr_cr = 2 AND at.acc_txn_type = ? THEN at.acc_txn_amt ELSE 0 END AS cr_amount,
                CASE 
                  WHEN ct.master_id = ? THEN
                       ? + SUM(
                            CASE 
                              WHEN at.acc_txn_dr_cr = 1 AND at.acc_txn_type = ? AND ct.master_id = ? THEN at.acc_txn_amt
                              WHEN at.acc_txn_dr_cr = 2 AND at.acc_txn_type = ? AND ct.master_id = ? THEN -at.acc_txn_amt
                              ELSE 0
                            END
                       ) OVER (ORDER BY vc.vch_date, vc.vch_txn_id, ct.txn_id)
                  ELSE NULL
                END AS running_balance,
                CASE 
                   WHEN vc.vch_type_id IN (18,19,7,17,3) THEN gstroutsup.outsup_bill_ref_no
                   WHEN vc.vch_type_id IN (2,21,6,11,12) THEN gstrinwsup.inwsup_bill_ref_no
                   ELSE NULL
                END AS bill_ref_no,
                ROW_NUMBER() OVER(ORDER BY vc.vch_date, vc.vch_txn_id, ct.txn_id) AS rn,
                COUNT(*) OVER() AS window_total
            FROM vchtxnconso vc
            JOIN cmptxnmstn ct ON ct.vch_txn_id = vc.vch_txn_id
            LEFT JOIN accttxnmst at ON at.vch_txn_id = vc.vch_txn_id AND at.txn_id = ct.txn_id AND at.acc_id = ct.master_id
            LEFT JOIN vchtypemst vtm ON vtm.vch_type_id = vc.vch_type_id
            LEFT JOIN vchlongnar vn ON vn.vch_txn_id = vc.vch_txn_id
            LEFT JOIN acctmaster am ON am.acc_id = ct.master_id AND ct.master_id_type = 'acc'
            LEFT JOIN gstrinwsup  ON gstrinwsup.vch_txn_id  = vc.vch_txn_id
            LEFT JOIN gstroutsup ON gstroutsup.vch_txn_id = vc.vch_txn_id
            WHERE vc.cmp_id = ?
              AND vc.hobo_id = ?
              AND vc.vch_date >= ?
              AND vc.vch_date <= ?
              AND am.acc_is_active = 1
              AND at.acc_txn_type = ?
              AND EXISTS (
                    SELECT 1 FROM cmptxnmstn ctm
                    WHERE ctm.vch_txn_id = vc.vch_txn_id
                      AND ctm.master_id = ?
                      AND ctm.master_id_type = 'acc'
                      AND ctm.cmp_id = ?
              )
        ) t
        WHERE (? = 1) OR (t.rn > ? AND t.rn <= (? + ?))
        ORDER BY t.vch_date, t.vch_txn_id, t.txn_id
    ";

    $rows = $this->db->query($sql, [
        $acc_txn_type, // dr_amount
        $acc_txn_type, // cr_amount
        $account_id,   // CASE master_id = ?
        $opening_balance,
        $acc_txn_type, // sum dr
        $account_id,
        $acc_txn_type, // sum cr
        $account_id,
        $sel_compId,
        $sel_boId,
        $from_date,
        $to_date,
        $acc_txn_type,
        $account_id,
        $sel_compId,
        $is_export,
        $offset,
        $offset,
        $pq_rPP,
    ])->getResultArray();

    $total_records   = !empty($rows) ? (int) $rows[0]['window_total'] : 0;
    $records         = [];
    $running_balance = $opening_balance; // will be overridden per row when available
    $voucher_running = $voucher_count;
    $last_voucher_id = null;

    $color_array = ['#f9f9f9', '#ffffff'];
    $color_index = 0;

    foreach ($rows as $idx => $value) {
        $next_voucher_txn_id = $rows[$idx + 1]['vch_txn_id'] ?? null;
        if ($value['vch_txn_id'] !== $last_voucher_id) {
            $voucher_running++;
            if ($idx > 0) $color_index = !$color_index;
        }

        // Running balance: only set for the main account rows
        if ($value['running_balance'] !== null) {
            $running_balance = (float) $value['running_balance'];
        }

        $style = '';
        if ($value['master_id'] == $account_id) {
            $style = 'font-weight:500;';
        }
        $style .= 'background-color: ' . $color_array[$color_index] . ';';

        $records[] = [
            'voucher_txn_id'     => $value["vch_txn_id"],
            'voucher_type_id'    => $value["vch_type_id"],
            'voucher_no'         => ($value['vch_txn_id'] != $last_voucher_id) ? $voucher_running : '',
            'account_name'       => $value["master_acc_name"],
            'txn_date'           => ($value['vch_txn_id'] != $last_voucher_id) ? date('d-m-Y', strtotime($value["vch_date"])) : '',
            'voucher_type'       => ($value['vch_txn_id'] != $last_voucher_id) ? $value["vch_type_name"] : '',
            'debit'              => ($value["dr_amount"] > 0) ? formatAmount($value["dr_amount"]) : '',
            'debit_total'        => $value["dr_amount"],
            'credit'             => ($value["cr_amount"] > 0) ? formatAmount($value["cr_amount"]) : '',
            'credit_total'       => $value["cr_amount"],
            'account_txn_amount' => formatAmount($value["acc_txn_amt"]),
            'balance'            => ($value['running_balance'] !== null) ? formatAmount(abs($running_balance)) : '',
            'balance_total'      => ($value['running_balance'] !== null) ? abs($running_balance) : 0,
            'long_narration'     => ($value['master_id'] == $account_id) ? $value['vch_long_narr'] : '',
            'balance_type'       => ($value['running_balance'] !== null) ? (($running_balance < 0) ? 'CR.' : 'DR.') : '',
            'pq_rowattr'         => ['style' => $style],
            'bill_ref_no'        => $value['bill_ref_no'],
        ];

        // Blank row after voucher
        if ($value['vch_txn_id'] !== $next_voucher_txn_id) {
            $records[] = [
                'voucher_txn_id'      => '',
                'voucher_type_id'     => '',
                'voucher_no'          => '',
                'account_name'        => '',
                'txn_date'            => '',
                'voucher_type'        => '',
                'debit'               => '',
                'debit_total'         => '',
                'credit'              => '',
                'credit_total'        => '',
                'account_txn_amount'  => '',
                'balance'             => '',
                'balance_total'       => '',
                'long_narration'      => '',
                'balance_type'        => '',
                'bill_ref_no'         => ''
            ];
        }

        $last_voucher_id = $value['vch_txn_id'];
    }

    return [
        "totalRecords" => $total_records,
        "curPage"      => $pq_curPage,
        "data"         => $records
    ];
}

    
    function AccountTaxInfo($tax_cat_id){
        $response = $this->db->table('taxcatmstn tc')
                            ->select("
                                MAX(CASE WHEN ts.tax_cat_sub_type = 1 THEN ts.tax_cat_rate END) as igst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 2 THEN ts.tax_cat_rate END) as cgst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 3 THEN ts.tax_cat_rate END) as sgst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 4 THEN ts.tax_cat_rate END) as ugst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 5 THEN ts.tax_cat_rate END) as cess
                            ")
                            ->join('taxcatrate ts', 'ts.tax_cat_mst_id = tc.tax_cat_mst_id', 'left')
                            ->where('tc.tax_cat_mst_id', $tax_cat_id)
                            ->where('tc.tax_cat_is_active', 1)
                            ->get()
                            ->getRowArray();
                            
        return  $response ;        
        
    }
    
	function company_all_accounts(){
        $this->fy_id         = $this->session->get('ses_comp_fy_id');
	    $this->company_id    = $this->session->get('ses_company_id');
		$bbb_groups  = $this->get_bbb_groups_list();
	    $cc_groups   = $this->get_cc_groups_list();
	    $cash_groups = $this->get_cash_groups_list(); 
		
		$undercrsmt_tbl  = "undercrsmt";
		$acctmaster_tbl  = "acctmaster";		
		$acctmstdet_tbl  = "acctmstdet";
		
	    $builder = $this->db->table($acctmaster_tbl);
		$builder->join($undercrsmt_tbl, "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id AND $undercrsmt_tbl.crs_mst_type =1", 'left');
		$builder->join($acctmstdet_tbl, "$acctmstdet_tbl.acc_id = $acctmaster_tbl.acc_id", "left");
		$builder->select([
			"$undercrsmt_tbl.crs_mst_parent_id as acc_grp_parent_id",
			"$undercrsmt_tbl.under_crs_mst_id as acc_grp_id",
			"$acctmaster_tbl.acc_name as label",
			"$acctmaster_tbl.acc_name as value",
			"$acctmaster_tbl.acc_id",
			"$acctmaster_tbl.tax_cat_mst_id",
			"$acctmstdet_tbl.acc_gstin",
			"$acctmstdet_tbl.acc_sac"
		]);
		//$builder->where("$undercrsmt_tbl.crs_mst_parent_id !=",14);
		$builder->where("$acctmstdet_tbl.cmp_id ",$this->company_id);
		$builder->where("$undercrsmt_tbl.cmpfymastr_id ",$this->fy_id);
		$builder->orderBy('acc_name');
		$data = $builder->get()->getResultArray();		
	    $final_result = array();
		
	   foreach ($data as $key => $value) {		
         $account_info     = $this->account_info($value['acc_id']); 
		 $state_code   =0;
		 $acc_state    ='';
		 $acc_country  ='';
		 if($account_info){
			 if($account_info['address_info']){
				 $acc_country = $account_info['address_info']['contact_country'];
				 $acc_state = $account_info['address_info']['contact_state'];
				 
				 $state_info    =  $this->CommonModel->get_state_info($acc_country,$acc_state);
				 if($state_info)
					$state_code   = sprintf( '%02d', $state_info['state_code']);
				else
					$state_code   = 0;
			 }
		 }
		 
	     $account_tax_info = $this->AccountTaxInfo($value['tax_cat_mst_id']);
	     if($account_tax_info){
	          $igst_rate  = $account_tax_info['igst']; 
	          $cess_rate  = $account_tax_info['cess']; 
	          $cgst_rate  = $account_tax_info['cgst'];
	          $sgst_rate  = $account_tax_info['sgst'];
	     } else{
	          $igst_rate  = 0; 
	          $cess_rate  = 0; 
	          $cgst_rate  = 0;
	          $sgst_rate  = 0;
	       }   
	       
		  $cannotselected=0;
		  $acc_grp_parent_id = $value['acc_grp_parent_id'];
		  if($acc_grp_parent_id!=0){
			   if($acc_grp_parent_id==1){
				   $cannotselected=1;
			   }
			  
		  }else{
			 $main_group_info = $this->main_group_info($value['acc_grp_id']);  
			 if($main_group_info)
			 $acc_grp_parent_id = $main_group_info['acc_grp_parent_id'];
		     else 
			 $acc_grp_parent_id = 0;	 
		     $cannotselected=0;
             if($acc_grp_parent_id==1){
				 $cannotselected=1; 
			 }else{
				 
				 if($main_group_info){
				if($main_group_info['under_main_id']==0){
					if(in_array($value['acc_grp_id'],[3,15,16,22])){
						$cannotselected=1;
					}
				} else{
					if(in_array($main_group_info['under_main_id'],[3,15,16,22])){
						$cannotselected=1;
					}
				}
				 }
			 }			 
		  }		  
		  
		  	$is_bbb = 0;
	       	if(in_array($value['acc_grp_id'], $bbb_groups) || in_array($value['acc_grp_parent_id'], [16,22,4,5] )){
	           	$is_bbb = 1;
	       	}
	       	
	       $is_cc = 0;
	       	if(in_array($value['acc_grp_id'], $cc_groups) || in_array($value['acc_grp_parent_id'], [7,11,13])){
	          	$is_cc = 1;
	       	}
       	
	       	$is_cash = 0;
	       	if(in_array($value['acc_grp_id'], $cash_groups)){
	          	$is_cash = 1;
	       	}	       	
	     	$final_result[]    = array(
               			"label"       => ucwords($value['label']),
               			'value'       => $value['value'],
               			'id'          => $value['acc_id'],
               			'acc_id'      => $value['acc_id'],
						'is_sundry'   => 0,
						'is_acc'      => 1,
						'is_bbb'      => $is_bbb,
						'is_cc'	      => $is_cc,
						'is_cash'	  => $is_cash,
						'cannotselected' =>  $cannotselected,
						'igst_rate'      =>  $igst_rate,
						'cess_rate'      =>  $cess_rate,
						'cgst_rate'      =>  $cgst_rate,
						'sgst_rate'      =>  $sgst_rate,
						'tax_cat_id'     =>  $value['tax_cat_mst_id'] ?? 0 ,
						'item_hsn_sac'   =>  $value['acc_sac'],
						'acc_state'      =>  $acc_state,
						'acc_country'    =>  $acc_country,
						'state_code'     =>  $state_code,
						'cess_basis'     =>  1,
						'supply_type'    =>  '',
						'acc_short_code' =>  ''
                 	    );	
	   	}
	   	
       return json_encode($final_result, JSON_PRETTY_PRINT);
  } 



  
   function update_opening_bal_branchwise($account_id,$branch_id,$updata){
	$accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	
	// if exists opn balance in branch 
	$exists = $this->db->table($accoppybal_tbl)->where('bo_id',$branch_id)->where('acc_id',$account_id)->get()->getRowArray();
	if($exists)
	$this->db->table($accoppybal_tbl)->where('bo_id',$branch_id)->where('acc_id',$account_id)->update($updata);	   
    else{
	 $insrtdata = array("acc_id"=>$account_id,"bo_id"=>$branch_id,
	                    "acc_op_bal"=>$updata['acc_op_bal'],"acc_py_bal"=>$updata['acc_py_bal']);	
	 $this->db->table($accoppybal_tbl)->insert($insrtdata);	
	}
   
   }
    
    function insert_acc_op_bal($data){
	    $accoppybal_tbl = "accoppybal";
	   	$this->db->table($accoppybal_tbl)->insert($data);			
    }
	
	function insert_acc_txn_entry($txn_data){
	  	$this->db->table("accttxnmst")->insert($txn_data);	
    }
	

	 function Save_BillByBill_OnBalance($acc_id,$data){
		if($data){
		  foreach($data as $row){
		      $method   = $row['method'];
		      if($method=="New Ref."){
		          $reference = $row['reference'];
		          $amount    = $row['amount'];
		          $drcr      = $row['drcr'];
		          $due_date  = ($row['due_date']=='') ? validate_date_by_fy($row['due_date']):$row['due_date'];
		          
		          if($drcr=='C')
		            $amount = -$amount;
		        
		          $bill_master_data = [
						  'cmp_id'         => $this->company_id,
						  'bill_ref_name'  => $row['reference'],
						  'acc_id'         => $acc_id,
						  'bill_due_date'  => date('Y-m-d',strtotime($due_date)),
						  'bill_status'    => 0,
						 ];
				$this->db->table("billmaster")->insert($bill_master_data);
                $bills_ref_id = $this->db->insertID();
		      }
		      else{
		       $bills_ref_id =   $row['reference_id']; 
		         $amount    = $row['amount'];
		          $drcr      = $row['drcr'];   
		           if($drcr=='C')
		            $amount = -$amount;
		      }
		      $bill_mst = $this->db->table("billoppybal")
						->where('cmpfymastr_id', $this->fy_id)
						->where('cmp_id', $this->company_id)
						->where('hobo_id', $this->bo_id)
						->where('bill_ref_id', $bills_ref_id)
						->get()->getRowArray();
        		if($bill_mst){
        		$op_data = [
              		'bill_op_bal' 	=> $amount ?? 0
        		
              	];
              	$this->db->table("billoppybal")->where('cmpfymastr_id', $this->fy_id)
						->where('cmp_id', $this->company_id)
							->where('hobo_id', $this->bo_id)
						->where('bill_ref_id', $bills_ref_id)->update($op_data);
        		}
        		else{
        		    $op_data = [
        		    'cmp_id'        => $this->company_id,
        			'cmpfymastr_id' => $this->fy_id,
              		'bill_ref_id' 	=> $bills_ref_id,      		
              		'bill_op_bal' 	=> $amount ?? 0,
              		'bill_py_bal' 	=> 0,
        			'hobo_id' 		=> $this->bo_id,
              	];
              	$this->db->table("billoppybal")->insert($op_data);
        		}
		  }	
		}
    } 
	
	function Save_SubLedger_OnBalance($acc_id,$data){
		if($data){
		  foreach($data as $row){
		      $method   = $row['method'];
		      if($method=="New Ref."){
		          $reference = $row['reference'];
		          $amount    = $row['amount'];
		          $drcr      = $row['drcr'];
		          $due_date  = ($row['due_date']=='') ? validate_date_by_fy($row['due_date']):$row['due_date'];
		          
		          if($drcr=='C')
		            $amount = -$amount;
		        
		          $subledger_data = [
						  'cmp_id'           => $this->company_id,
						  'sub_acc_name'     => $row['reference'],
						  'acc_id'           => $acc_id,
						  'sub_due_date'     => date('Y-m-d',strtotime($due_date)),
						  'sub_acc_is_active'=> 0,
						 ];
				$this->db->table("subacctmst")->insert($subledger_data);
                $sub_acc_id = $this->db->insertID();
		      }
		      else{
		       $sub_acc_id   = $row['reference_id']; 
		         $amount     = $row['amount'];
		          $drcr      = $row['drcr'];   
		           if($drcr=='C')
		            $amount = -$amount;
		      }
		      $bill_mst = $this->db->table("suboppybal")
						->where('cmpfymastr_id', $this->fy_id)
						->where('cmp_id', $this->company_id)
						->where('hobo_id', $this->bo_id)
						->where('sub_acc_id', $sub_acc_id)
						->get()->getRowArray();
        		if($bill_mst){
        		$op_data = [
              		'sub_acc_op_bal' 	=> $amount ?? 0
        		
              	];
              	$this->db->table("suboppybal")->where('cmpfymastr_id', $this->fy_id)
						->where('cmp_id', $this->company_id)
						->where('hobo_id', $this->bo_id)
						->where('sub_acc_id', $sub_acc_id)->update($op_data);
        		}
        		else{
        		    $op_data = [
        		    'cmp_id'         => $this->company_id,
        			'cmpfymastr_id'  => $this->fy_id,
              		'sub_acc_id' 	 => $sub_acc_id,      		
              		'sub_acc_op_bal' => $amount ?? 0,
              		'sub_acc_py_bal' => 0,
        			'hobo_id' 		 => $this->bo_id,
					];
              	$this->db->table("suboppybal")->insert($op_data);
        		}
		  }	
		}
    } 
	
	function add_undercrsmt($txn_data){
	  	$this->db->table("undercrsmt")->insert($txn_data);	
    }
	
	function accounts_monthly_details($account_id, $opening_balance){
    // Use FY boundaries explicitly (April–March) instead of calendar year
    $fy_start_date = date('Y-m-d', strtotime(validate_fy_from_date(''))); // e.g., 2024-04-01
    $fy_end_date   = date('Y-m-d', strtotime(validate_fy_to_date('')));   // e.g., 2025-03-31

    /* ---------------- FY Months ---------------- */
    $monthsSql = "
        select
            gs::date as month_start,
            (gs + interval '1 month - 1 day')::date as month_end,
            row_number() over(order by gs) as month_num,
            trim(to_char(gs, 'Month')) as month_name
        from generate_series(
            date '{$fy_start_date}',
            date '{$fy_start_date}' + interval '11 months',
            interval '1 month'
        ) gs
    ";

    /* ---------------- Monthly Txn ---------------- */
    $txnSql = $this->db->table('accttxnmst at')
        ->select("
            extract(month from vc.vch_date)::int as txn_month,
            sum(case when at.acc_txn_dr_cr = 1 then at.acc_txn_amt else 0 end) as debit,
            sum(case when at.acc_txn_dr_cr = 2 then at.acc_txn_amt else 0 end) as credit
        ", false)
        ->join('vchtxnconso vc', 'vc.vch_txn_id = at.vch_txn_id')
        ->where([
            'at.acc_id'       => $account_id,
            'at.cmp_id'       => $this->company_id,
            'at.acc_txn_type' => 1
        ])
        ->where('vc.vch_date >=', $fy_start_date)
        ->where('vc.vch_date <=', $fy_end_date)
		->where('at.hobo_id', $this->bo_id)
		
        ->groupBy('extract(month from vc.vch_date)', false)
        ->getCompiledSelect();

    /* ---------------- Merge + Running Balance ---------------- */
    $finalSql = "
        select
            to_char(m.month_start,'DD-MM-YYYY') as from_date,
            to_char(m.month_end,'DD-MM-YYYY')   as to_date,
            {$account_id} as account_id,
            m.month_name,
            coalesce(t.debit,0)  as debit,
            coalesce(t.credit,0) as credit,
            (
                {$opening_balance}
                + sum(coalesce(t.debit,0) - coalesce(t.credit,0))
                  over (order by m.month_num)
            ) as running_balance
        from ({$monthsSql}) m
        left join ({$txnSql}) t
               on extract(month from m.month_start)::int = t.txn_month
        order by m.month_num
    ";

    $rows = $this->db->query($finalSql)->getResultArray();

    /* ---------------- EXACT SAME ARRAY AS ORIGINAL ---------------- */
    $finalResult = [];

    foreach ($rows as $r) {

        $balance_type = ($r['running_balance'] < 0) ? 'CR.' : 'DR.';

        $finalResult[] = [
            "from_date"     => $r['from_date'],
            "to_date"       => $r['to_date'],
            "account_id"    => $account_id,
            "month"         => $r['month_name'],

            "debit"         => formatAmount($r['debit']),
            "credit"        => formatAmount($r['credit']),

            "debit_total"   => parseAmount($r['debit']),
            "credit_total"  => parseAmount($r['credit']),

            "balance"       => formatAmount(abs($r['running_balance'])),
            "balance_total" => parseAmount(abs($r['running_balance'])),

            "balance_type"  => $balance_type
        ];
    }

    return $finalResult;	
}
	
	function accounts_quaterly_balance($account_id,$opening_balance){
			$cal = fy_calender_js();
    $fy_start_date = date('Y-m-d', strtotime($cal['from_date']));
    $fy_end_date   = date('Y-m-d', strtotime($cal['to_date']));

    /* ---------------- FY Quarters ---------------- */
    $quartersSql = "
        select
            qs::date as quarter_start,
            (qs + interval '3 months - 1 day')::date as quarter_end,
            row_number() over(order by qs) as q_no,
            upper(to_char(qs,'Mon')) || ' – ' || upper(to_char(qs + interval '2 months','Mon'))
                || ' (Q' || row_number() over(order by qs) || ')' as month_label
        from generate_series(
            date '{$fy_start_date}',
            date '{$fy_start_date}' + interval '9 months',
            interval '3 months'
        ) qs
    ";

    /* ---------------- Quarterly Txn ---------------- */
    $txnSql = $this->db->table('accttxnmst at')
        ->select("
            qs.q_no,
            sum(case when at.acc_txn_dr_cr = 1 then at.acc_txn_amt else 0 end) as debit,
            sum(case when at.acc_txn_dr_cr = 2 then at.acc_txn_amt else 0 end) as credit
        ", false)
        ->join('vchtxnconso vc', 'vc.vch_txn_id = at.vch_txn_id')
        ->join("({$quartersSql}) qs", 
               "vc.vch_date between qs.quarter_start and qs.quarter_end",
               'inner', false)
        ->where('at.acc_id', $account_id)
        ->where('at.acc_txn_type', 1)
		->where('at.hobo_id', $this->bo_id)
        ->groupBy('qs.q_no', false)
        ->getCompiledSelect();

    /* ---------------- Merge + Running Balance ---------------- */
    $finalSql = "
        select
            to_char(q.quarter_start,'DD-MM-YYYY') as from_date,
            to_char(q.quarter_end,'DD-MM-YYYY')   as to_date,
            {$account_id} as account_id,
            q.month_label as month,
            coalesce(t.debit,0)  as debit,
            coalesce(t.credit,0) as credit,
            (
                {$opening_balance}
                + sum(coalesce(t.debit,0) - coalesce(t.credit,0))
                  over(order by q.q_no)
            ) as running_balance
        from ({$quartersSql}) q
        left join ({$txnSql}) t on t.q_no = q.q_no
        order by q.q_no
    ";

    $rows = $this->db->query($finalSql)->getResultArray();

    /* ---------------- EXACT SAME ARRAY STRUCTURE ---------------- */
    $finalResult = [];

    foreach ($rows as $r) {

        $balance_type = ($r['running_balance'] < 0) ? 'CR.' : 'DR.';

        $finalResult[] = [
            "from_date"     => $r['from_date'],
            "to_date"       => $r['to_date'],
            "account_id"    => $account_id,
            "month"         => $r['month'],

            "debit"         => formatAmount($r['debit']),
            "credit"        => formatAmount($r['credit']),

            "debit_total"   => parseAmount($r['debit']),
            "credit_total"  => parseAmount($r['credit']),

            "balance"       => formatAmount(abs($r['running_balance'])),
            "balance_total" => parseAmount(abs($r['running_balance'])),

            "balance_type"  => $balance_type
        ];
    }

    return $finalResult;	
	}
	
	function accounts_yearly_balance($account_id,$opening_balance){
			$cal = fy_calender_js();
    $fy_start_date = date('Y-m-d', strtotime($cal['from_date']));
    $fy_end_date   = date('Y-m-d', strtotime($cal['to_date']));

    /* ---------------- Half-Year Periods (2 blocks) ---------------- */
    $halfYearSql = "
        select
            hs::date as period_start,
            (hs + interval '6 months - 1 day')::date as period_end,
            row_number() over(order by hs) as p_no,
            upper(to_char(hs,'Mon')) || ' – ' ||
            upper(to_char(hs + interval '5 months','Mon'))
            || ' (H' || row_number() over(order by hs) || ')' as month_label
        from generate_series(
            date '{$fy_start_date}',
            date '{$fy_start_date}' + interval '6 months',
            interval '6 months'
        ) hs
    ";

    /* ---------------- Half-Year Txn ---------------- */
    $txnSql = $this->db->table('accttxnmst at')
        ->select("
            h.p_no,
            sum(case when at.acc_txn_dr_cr = 1 then at.acc_txn_amt else 0 end) as debit,
            sum(case when at.acc_txn_dr_cr = 2 then at.acc_txn_amt else 0 end) as credit
        ", false)
        ->join('vchtxnconso vc', 'vc.vch_txn_id = at.vch_txn_id')
        ->join("({$halfYearSql}) h",
               "vc.vch_date between h.period_start and h.period_end",
               'inner', false)
        ->where('at.acc_id', $account_id)
        ->where('at.acc_txn_type', 1)
		->where('at.hobo_id', $this->bo_id)
        ->groupBy('h.p_no', false)
        ->getCompiledSelect();

    /* ---------------- Merge + Running Balance ---------------- */
    $finalSql = "
        select
            to_char(h.period_start,'DD-MM-YYYY') as from_date,
            to_char(h.period_end,'DD-MM-YYYY')   as to_date,
            {$account_id} as account_id,
            h.month_label as month,
            coalesce(t.debit,0)  as debit,
            coalesce(t.credit,0) as credit,
            (
                {$opening_balance}
                + sum(coalesce(t.debit,0) - coalesce(t.credit,0))
                  over(order by h.p_no)
            ) as running_balance
        from ({$halfYearSql}) h
        left join ({$txnSql}) t on t.p_no = h.p_no
        order by h.p_no
    ";

    $rows = $this->db->query($finalSql)->getResultArray();

    /* ---------------- EXACT SAME ARRAY STRUCTURE ---------------- */
    $finalResult = [];

    foreach ($rows as $r) {

        $balance_type = ($r['running_balance'] < 0) ? 'CR.' : 'DR.';

        $finalResult[] = [
            "from_date"     => $r['from_date'],
            "to_date"       => $r['to_date'],
            "account_id"    => $account_id,
            "month"         => $r['month'],

            "debit"         => formatAmount($r['debit']),
            "credit"        => formatAmount($r['credit']),

            "debit_total"   => parseAmount($r['debit']),
            "credit_total"  => parseAmount($r['credit']),

            "balance"       => formatAmount(abs($r['running_balance'])),
            "balance_total" => parseAmount(abs($r['running_balance'])),

            "balance_type"  => $balance_type
        ];
    }
			return $finalResult;		
	}
	
	function account_opening_balance($account_id, $from_date, $type = 1, $isconsoview = 0,$compId=null, $boId=null,$fyId=null)
{
	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
    $fy_start_date = date(
        'Y-m-d',
        strtotime(validate_fy_from_date(''))
    );

    /*
    |--------------------------------------------------------------------------
    | Opening Balance from 'accoppybal' table
    |--------------------------------------------------------------------------
    | If consoview is 1, SUM all opening balances for the account.
    | Otherwise, get the specific opening balance for the branch (hobo_id).
    */
	  $opBuilder = $this->db->table('accoppybal');
    $opBuilder->select('acc_op_bal as total_op_bal'); // Use SUM for consolidation
    $opBuilder->where('acc_id', $account_id);
	$opBuilder->where('cmp_id', $sel_compId);
    $opBuilder->where('cmpfymastr_id', $sel_fyId);

    // Conditionally add the hobo_id (branch) filter
    if ($isconsoview != 1) {
        $opBuilder->where('hobo_id', $sel_boId);
    }

    $op = $opBuilder->get()->getRowArray();
	$opening_balance = $op['total_op_bal'] ?? 0;

    // If the report starts from the beginning of the financial year, we're done.
 
   if (strtotime($from_date) === strtotime($fy_start_date)) {
	 
       return $opening_balance;
    }

    $prev_to_date = date('Y-m-d', strtotime($from_date . ' -1 day'));

    /*
    |--------------------------------------------------------------------------
    | Net Transaction Movement before the 'from_date'
    |--------------------------------------------------------------------------
    | Calculates the sum of all transactions between the start of the
    | financial year and the day before the selected from_date.
    */
    $movementBuilder = $this->db->table('accttxnmst at');
    $movementBuilder->select("
        SUM(
            CASE
                WHEN at.acc_txn_dr_cr = 1 THEN at.acc_txn_amt
                WHEN at.acc_txn_dr_cr = 2 THEN -at.acc_txn_amt
                ELSE 0
            END
        ) as net_amount
    ", false);
    $movementBuilder->join('vchtxnconso vc', 'vc.vch_txn_id = at.vch_txn_id');

    // Base conditions
    $movementBuilder->where('at.acc_id', $account_id);
    $movementBuilder->where('at.cmp_id', $sel_compId);
    $movementBuilder->where('at.acc_txn_type', 1); // Using hardcoded '1' as in your original code
    $movementBuilder->where('vc.vch_date >=', $fy_start_date);
    $movementBuilder->where('vc.vch_date <=', $prev_to_date);

    // Conditionally add the hobo_id (branch) filter
    if ($isconsoview != 1) {
        $movementBuilder->where('at.hobo_id', $sel_boId);
    }
    
    $row = $movementBuilder->get()->getRowArray();

    // Final balance is the starting opening balance plus the net movement
    return $opening_balance + (float)($row['net_amount'] ?? 0);
}
	
	function sblgr_account_opening_balance($account_id,$to_date,$type=1){
		$builder = $this->db->table('suboppybal');
		$builder->select('sub_acc_op_bal,sub_acc_py_bal')
				->where('sub_acc_id', $account_id)
				->where('cmp_id',$this->company_id)
				->where('hobo_id',$this->bo_id)
				->where('cmpfymastr_id',$this->fy_id);
		$query  = $builder->get();
		$result = $query->getRowArray();
		return ($result['sub_acc_op_bal'] ?? 0);
	}
	
	function get_pnl_groups()
    {
        
		$p_groups = [6,7,8,9,10,11,12,13];
		$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($tbl_name)
	   					 ->select('acc_grp_id')
	   					 ->whereIn('acc_grp_parent_id', $p_groups)
	   					 ->get()->getResultArray();
        $final = [];
        if($data){
        	$final = array_column($data, 'acc_grp_id');
        }
        
        return $final;
    
    }

    function check_op_change_pnl($account_id, $balance)
    {
    	 $get_account_info = $this->get_account_info($account_id);
		  if(in_array($get_account_info['acc_grp_parent_id'], [6,7,8,9,10,11,12,13])){
		  	if($balance != 0)
		  		return 0;
		  }

		  if(in_array($get_account_info['acc_grp_id'], $this->get_pnl_groups())){
		  	if($balance != 0)
		  		return 0;
		  }

		return 1;
    }
   
   
   function update_acc_op_bal_bulk($account_id,$data,$branchid){
		
	  $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	  $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
	  $billsoppyn_tbl = $this->company_id.'_billsoppyn_'.$this->session->get('ses_comp_fy_id');

	  $get_account_info = $this->get_account_info($account_id);
	  if(in_array($get_account_info['acc_grp_parent_id'], [6,7,8,9,10,11,12,13])){
	  	if($data['acc_op_bal'] != 0)
	  		return 0;
	  }
	  if(in_array($get_account_info['acc_grp_id'], $this->get_pnl_groups())){
	  	if($data['acc_op_bal'] != 0)
	  		return 0;
	  }
	  $accoppybal = $this->db->table($accoppybal_tbl)
							  ->where('acc_id',$account_id)
							  ->where('bo_id',$branchid)
							  ->get()->getRowArray();
	  if($accoppybal){

	  	  $old_acc_op_bal = $accoppybal['acc_op_bal'];

		  if(in_array($get_account_info['acc_grp_id'], $this->get_bbb_groups()))
		  {
		  	  $un_bills_ref_id = $this->TransactionModel->getUndefinedBillRefId($account_id);

			  if($data['acc_op_bal'] >= $old_acc_op_bal)
			  {

			  	$diff = parseAmount($data['acc_op_bal']) - parseAmount($old_acc_op_bal);

			  	$billsoppyn = $this->db->table($billsoppyn_tbl)
								->where('bills_ref_id',$un_bills_ref_id)
								->where('bo_id',$branchid)
								->get()->getRowArray();

			  	$undefined_balance = $billsoppyn['bills_op_bal'] + $diff;

			  	$this->db->table($billsoppyn_tbl)
					  	->where('bills_ref_id',$un_bills_ref_id)
					  	->where('bo_id',$branchid)
					  	->update(['bills_op_bal' => $undefined_balance]);

			  	$this->TransactionModel->update_all_acc_bill_ref_bal($account_id, true);
			  }

			  if($data['acc_op_bal'] < $old_acc_op_bal)
			  {

			  	$this->db->table($billsoppyn_tbl)
					  	->where('bills_ref_id',$un_bills_ref_id)
					  	->where('bo_id',$branchid)
					  	->update(['bills_op_bal' => $data['acc_op_bal']]);

			  	$result = $this->db->table($bill_mst_tbl)
							  	->where('bills_ref_name !=', 'UNDEFINED')
							  	->where('acc_id', $account_id)
							  	->get()->getResultArray();

				foreach ($result as $key => $value) {
					$this->db->table($billsoppyn_tbl)
					  	->where('bills_ref_id',$value['bills_ref_id'])
					  	->where('bo_id',$branchid)
					  	->update(['bills_op_bal' => $data['acc_op_bal']]);
				}
			  	
			  	$this->TransactionModel->update_all_acc_bill_ref_bal($account_id);
			  }
		  }

		  $this->db->table($accoppybal_tbl)
				  ->where('acc_id',$account_id)
				  ->where('bo_id',$branchid)
				  ->update($data);
	  }
	  	else{
		  	$data['acc_id']	= $account_id;
		  	$data['bo_id']	= $branchid;

		  	$this->db->table($accoppybal_tbl)->insert($data);

		  	$un_bills_ref_id = $this->TransactionModel->getUndefinedBillRefId($account_id);
	  		
	  		$this->db->table($billsoppyn_tbl)
			  		->where('bills_ref_id',$un_bills_ref_id)
			  		->where('bo_id',$branchid)
			  		->update(['bills_op_bal' => $data['acc_op_bal']]);

			$this->TransactionModel->update_bill_txn_balance($un_bills_ref_id);
	 	}
	}
	
    function update_acc_op_bal($account_id,$data){
		
	  	if($this->session->get('ses_boid')!='')
           $data['bo_id'] =$this->session->get('ses_boid');
	    else
		    $data['bo_id'] =1;
		
	  $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	  $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
	  $billsoppyn_tbl = $this->company_id.'_billsoppyn_'.$this->session->get('ses_comp_fy_id');

	  $get_account_info = $this->get_account_info($account_id);
	  if(in_array($get_account_info['acc_grp_parent_id'], [6,7,8,9,10,11,12,13])){
	  	if($data['acc_op_bal'] != 0)
	  		return 0;
	  }

	  if(in_array($get_account_info['acc_grp_id'], $this->get_pnl_groups())){
	  	if($data['acc_op_bal'] != 0)
	  		return 0;
	  }


	  $accoppybal = $this->db->table($accoppybal_tbl)
							  ->where('acc_id',$account_id)
							  ->where('bo_id',$this->bo_id)
							  ->get()->getRowArray();
	  if($accoppybal){

	  	  $old_acc_op_bal = $accoppybal['acc_op_bal'];

		  if(in_array($get_account_info['acc_grp_id'], $this->get_bbb_groups()))
		  {
		  	  $un_bills_ref_id = $this->TransactionModel->getUndefinedBillRefId($account_id);

			  if($data['acc_op_bal'] >= $old_acc_op_bal)
			  {

			  	$diff = parseAmount($data['acc_op_bal']) - parseAmount($old_acc_op_bal);

			  	$billsoppyn = $this->db->table($billsoppyn_tbl)
								->where('bills_ref_id',$un_bills_ref_id)
								->where('bo_id',$this->bo_id)
								->get()->getRowArray();
				if($billsoppyn && isset($billsoppyn['bills_op_bal']))
			  	$undefined_balance = $billsoppyn['bills_op_bal'] + $diff;
			    else
				$undefined_balance = $diff;	

			  	$this->db->table($billsoppyn_tbl)
					  	->where('bills_ref_id',$un_bills_ref_id)
					  	->where('bo_id',$this->bo_id)
					  	->update(['bills_op_bal' => $undefined_balance]);

			  	$this->TransactionModel->update_all_acc_bill_ref_bal($account_id, true);
			  }

			  if($data['acc_op_bal'] < $old_acc_op_bal)
			  {

			  	$this->db->table($billsoppyn_tbl)
					  	->where('bills_ref_id',$un_bills_ref_id)
					  	->where('bo_id',$this->bo_id)
					  	->update(['bills_op_bal' => $data['acc_op_bal']]);

			  	$result = $this->db->table($bill_mst_tbl)
							  	->where('bills_ref_name !=', 'UNDEFINED')
							  	->where('acc_id', $account_id)
							  	->get()->getResultArray();

				foreach ($result as $key => $value) {
					$this->db->table($billsoppyn_tbl)
					  	->where('bills_ref_id',$value['bills_ref_id'])
					  	->where('bo_id',$this->bo_id)
					  	->update(['bills_op_bal' => $data['acc_op_bal']]);
				}
			  	
			  	$this->TransactionModel->update_all_acc_bill_ref_bal($account_id);
			  }
		  }

		  $this->db->table($accoppybal_tbl)
				  ->where('acc_id',$account_id)
				  ->where('bo_id',$this->bo_id)
				  ->update($data);
	  }
	  	else{
		  	$data['acc_id']	= $account_id;
		  	$data['bo_id']	= $this->bo_id;

		  	$this->db->table($accoppybal_tbl)->insert($data);

		  	$un_bills_ref_id = $this->TransactionModel->getUndefinedBillRefId($account_id);
	  		
	  		$this->db->table($billsoppyn_tbl)
			  		->where('bills_ref_id',$un_bills_ref_id)
			  		->where('bo_id',$this->bo_id)
			  		->update(['bills_op_bal' => $data['acc_op_bal']]);

			$this->TransactionModel->update_bill_txn_balance($un_bills_ref_id);
	 	}
	}

	
   
    public function acc_opn_balance_info($acc_id){
       $accoppybal_tbl ='accoppybal';
	   return $this->db->table($accoppybal_tbl)
	         ->where('hobo_id', $this->session->get('ses_boid'))
			 ->where('cmp_id', $this->company_id)
			 ->where('cmpfymastr_id', $this->fy_id)
			 ->where('acc_id', $acc_id)->get()->getRowArray(); 
	  }  
   
   function exists_account($data,$account_id){
     $table = $this->db->table("acctmaster")
	          ->where('LOWER(acc_name)', strtolower(trim($data['acc_name'])))
			  ->where('acc_id !=',$account_id)
			  ->where('acc_is_active',1)
			  ->where('bsd_id IS NULL')
			  ->where('cmp_id', $this->company_id)
		      ->get()->getRowArray(); 	  
        if($table){
            return ['status' => false, 'message' => 'Name must be unique'];
        }        
        $table = $this->db->table("acctmaster")
		          ->where('LOWER(acc_alias)', strtolower(trim($data['acc_name_alias'])))
				  ->where('acc_id !=',$account_id)
				  ->where('acc_is_active',1)
				  ->where('bsd_id IS NULL')
				  ->where('cmp_id', $this->company_id)
	      	      ->get()->getRowArray();   	   
        if($table){
            return ['status' => false, 'message' => 'Alias must be unique'];
        }
		 return ['status' => true, 'message' => ''];
   }    
			
   	
	public function add_account($data){	
         
		 try{ 
		 $builder   =  $this->db->table("acctmaster") ; 	      
		 $builder->join(
			"undercrsmt",
			" undercrsmt.crs_mst_id = acctmaster.acc_id 
			AND 
			 undercrsmt.crs_mst_type = 1
			AND 
			undercrsmt.cmpfymastr_id = $this->fy_id
			",
			'left'
		);   	
		$builder->where('LOWER(acctmaster.acc_name)', strtolower(trim($data['acc_name'])));
		$builder->where('acctmaster.acc_is_active',1);
		$builder->where('acctmaster.bsd_id IS NULL');
		$builder->where('acctmaster.cmp_id',$this->company_id);
		$table = $builder->get()->getRowArray();    
	
        if($table){
            return ['status' => false, 'message' => 'Name must be unique'];
        }        
                
        $builder   =  $this->db->table("acctmaster") ; 	      
		 $builder->join(
			"undercrsmt",
			" undercrsmt.crs_mst_id = acctmaster.acc_id 
			AND 
			 undercrsmt.crs_mst_type = 1
			AND 
			undercrsmt.cmpfymastr_id = $this->fy_id
			",
			'left'
		   );   	
		
		$builder->where('LOWER(acctmaster.acc_alias)', strtolower(trim($data['acc_name_alias'])));
		$builder->where('acctmaster.acc_is_active',1);
		$builder->where('acctmaster.bsd_id IS NULL');
		$builder->where('acctmaster.cmp_id',$this->company_id);
		$table = $builder->get()->getRowArray(); 
         	   
        if($table){
            return ['status' => false, 'message' => 'Alias must be unique'];
        }
        
        $acc_insert_data   = [
							'cmp_id'            => (int)$data['comp_id'],
							'acc_name'          => $data['acc_name'],
							'acc_alias'         => $data['acc_name_alias'],
							'acc_print_name'    => $data['acc_name_print'],							
							'contact_id'        => (int)0,
							'tax_cat_mst_id'    => (int)$data['tax_cat_mst_id'],
							'acc_is_active'     => (int)1
							];
        $this->db->table("acctmaster")->insert($acc_insert_data);		 
        $account_id = $this->db->insertID();
		
		$acc_details_data   = [			
						'acc_id'         => (int)$account_id,
						'cmp_id'         => (int)$data['comp_id'],
						'acc_aadhaar'    => $data['acc_aadhar'],
						'acc_tan'        => $data['acc_tan'],
						'acc_pan'        => $data['acc_pan'],
						'acc_it_jurisd'  => (int)$data['acc_jurisd'] ?? 0,
						'acc_gstin'      => $data['acc_gstin'],
						'acc_sac'        => $data['acc_sac'],
						'acc_is_sys_acc' => (int)0,
						'acc_is_sez'     => (int)$data['sezunit'] ?? 0
					    ];
						
		$this->db->table("acctmstdet")->insert($acc_details_data);
						
		
		$acc_adrs_data = [
                  'acc_addr1'     => $data['acc_add1'],
                  'acc_addr2'     => $data['acc_add2'],
                  'acc_city'     => $data['acc_city'],
                  'acc_state '   => (int)$data['acc_state'],
                  'acc_country ' => (int)$data['acc_country'],
                  'acc_pin'      => (int)$data['acc_pin'],
				  'acc_id'       => (int)$account_id, 
				  'cmp_id'		 => (int)$data['comp_id']	  
            ];
            $this->db->table('acctmstadr')
                                ->insert($acc_adrs_data);					
			
			 
        return ['status' => true, 'account_id' => $account_id];
		 }
		 catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return ['status' => false,'account_id'=>0, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
				} 
    
    }
 
   function load_list_groups(){
    $undercrsmt_tbl = "undercrsmt";

    // --- Parse pq_filter ---
    $filters = [];
    $filter_mode = 'and'; // default AND
    if (!empty($_POST['pq_filter'])) {
        $filter_data = json_decode($_POST['pq_filter'], true);
        if (!empty($filter_data['data']) && is_array($filter_data['data'])) {
            $filters = $filter_data['data'];
        }
        if (!empty($filter_data['mode']) && strtolower($filter_data['mode']) === 'or') {
            $filter_mode = 'or';
        }
    }

    // Pagination
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"])     ? (int)$_POST["pq_rpp"]     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;

    $builder = $this->db->table("accgrpmstn");

    // Select fields
    $builder->select("
        accgrpmstn.*,
        $undercrsmt_tbl.under_crs_mst_id,
        $undercrsmt_tbl.crs_mst_parent_id,
        $undercrsmt_tbl.crs_mst_is_primary,
        $undercrsmt_tbl.crs_is_active,
        $undercrsmt_tbl.cmpfymastr_id
    ");

    // Join
    $builder->join(
        $undercrsmt_tbl,
        "$undercrsmt_tbl.crs_mst_id = accgrpmstn.acc_grp_id
         AND $undercrsmt_tbl.crs_mst_type = 2
         AND $undercrsmt_tbl.cmpfymastr_id = $this->fy_id",
        'left'
    );

    // Base filters
    $builder->where("$undercrsmt_tbl.cmpfymastr_id IS NOT NULL");
    $builder->where('accgrpmstn.cmp_id', $this->company_id);

    // --- Apply search filters (if any non-empty value) ---
    if (!empty($filters)) {
        $builder->groupStart();
        foreach ($filters as $f) {
            $col  = $f['dataIndx']  ?? '';
            $val  = isset($f['value']) ? trim($f['value']) : '';
            $cond = strtolower($f['condition'] ?? 'contain');

            // Skip empty search values
            if ($val === '') continue;

            // Map dataIndx to db column and type
            $map = [
                'group_id'    => ['col' => 'accgrpmstn.acc_grp_id',    'type' => 'int'],
                'group_name'  => ['col' => 'accgrpmstn.acc_grp_name',  'type' => 'str'],
                'print_name'  => ['col' => 'accgrpmstn.acc_grp_alias', 'type' => 'str'],
                'primary'     => ['col' => "$undercrsmt_tbl.crs_mst_is_primary", 'type' => 'bool'],   // YES/NO
                'grp_status'  => ['col' => "$undercrsmt_tbl.crs_is_active",      'type' => 'status'], // ACTIVE/INACTIVE
            ];
            if (!isset($map[$col])) continue;

            $dbCol = $map[$col]['col'];
            $type  = $map[$col]['type'];
            $valLower = strtolower($val);

            // Build condition expression
            $expr = null;
            switch ($type) {
                case 'int':
                    // Cast int to text for LIKE when needed
                    $castCol = "CAST($dbCol AS TEXT)";
                    if (is_numeric($val)) {
                        if ($cond === 'equal')       $expr = [$dbCol, (int)$val, '='];
                        elseif ($cond === 'notequal') $expr = [$dbCol, (int)$val, '!='];
                        elseif ($cond === 'begin')     $expr = [$castCol, $val, 'after'];
                        elseif ($cond === 'end')       $expr = [$castCol, $val, 'before'];
                        elseif ($cond === 'notcontain')$expr = [$castCol, $val, 'both_not'];
                        else                           $expr = [$castCol, $val, 'both'];
                    } else {
                        if ($cond === 'begin')        $expr = [$castCol, $val, 'after'];
                        elseif ($cond === 'end')      $expr = [$castCol, $val, 'before'];
                        elseif ($cond === 'notcontain')$expr = [$castCol, $val, 'both_not'];
                        elseif ($cond === 'equal')     $expr = [$castCol, $val, 'none'];
                        elseif ($cond === 'notequal')  $expr = [$castCol, $val, 'none_not'];
                        else                           $expr = [$castCol, $val, 'both'];
                    }
                    break;

                case 'bool': // YES/NO -> 1/0
                    if (strpos($valLower, 'yes') !== false)       $expr = [$dbCol, 1, '='];
                    elseif (strpos($valLower, 'no') !== false)    $expr = [$dbCol, 0, '='];
                    break;

                case 'status': // ACTIVE/INACTIVE -> 1/0
                    if (strpos($valLower, 'inactive') !== false)  $expr = [$dbCol, 0, '='];
                    elseif (strpos($valLower, 'active') !== false)$expr = [$dbCol, 1, '='];
                    break;

                case 'str':
                default:
                    $likeVal = $valLower;
                    $lowerCol = "LOWER($dbCol)";
                    if     ($cond === 'equal')       $expr = [$lowerCol, $likeVal, '='];
                    elseif ($cond === 'notequal')    $expr = [$lowerCol, $likeVal, '!='];
                    elseif ($cond === 'begin')       $expr = [$lowerCol, $likeVal, 'after'];
                    elseif ($cond === 'end')         $expr = [$lowerCol, $likeVal, 'before'];
                    elseif ($cond === 'notcontain')  $expr = [$lowerCol, $likeVal, 'both_not'];
                    elseif ($cond === 'empty')       $expr = ['__empty__', $dbCol, null];
                    elseif ($cond === 'notempty')    $expr = ['__notempty__', $dbCol, null];
                    else                             $expr = [$lowerCol, $likeVal, 'both'];
                    break;
            }

            if ($expr === null) continue;

            // Apply expression with AND/OR mode
            if ($expr[0] === '__empty__') {
                if ($filter_mode === 'or') {
                    $builder->orGroupStart()->where("$expr[1] IS NULL")->orWhere("TRIM($expr[1]) = ''")->groupEnd();
                } else {
                    $builder->groupStart()->where("$expr[1] IS NULL")->orWhere("TRIM($expr[1]) = ''")->groupEnd();
                }
            } elseif ($expr[0] === '__notempty__') {
                if ($filter_mode === 'or') {
                    $builder->orGroupStart()->where("$expr[1] IS NOT NULL")->where("TRIM($expr[1]) != ''")->groupEnd();
                } else {
                    $builder->groupStart()->where("$expr[1] IS NOT NULL")->where("TRIM($expr[1]) != ''")->groupEnd();
                }
            } else {
                list($colExpr, $v, $mode) = $expr;
                if ($mode === '=') {
                    ($filter_mode === 'or')
                        ? $builder->orWhere($colExpr, $v)
                        : $builder->where($colExpr, $v);
                } elseif ($mode === '!=') {
                    ($filter_mode === 'or')
                        ? $builder->orWhere("$colExpr !=", $v)
                        : $builder->where("$colExpr !=", $v);
                } elseif ($mode === 'none_not') {
                    ($filter_mode === 'or')
                        ? $builder->orNotLike($colExpr, $v, 'none')
                        : $builder->notLike($colExpr, $v, 'none');
                } elseif ($mode === 'both_not') {
                    ($filter_mode === 'or')
                        ? $builder->orNotLike($colExpr, $v, 'both')
                        : $builder->notLike($colExpr, $v, 'both');
                } else { // like variants
                    ($filter_mode === 'or')
                        ? $builder->orLike($colExpr, $v, $mode)
                        : $builder->like($colExpr, $v, $mode);
                }
            }
        }
        $builder->groupEnd();
    }

    // Count (before limit)
    $countBuilder = clone $builder;
    $countBuilder->select('1');
    $total_Records = $countBuilder->countAllResults(false);

    // Pagination
    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_Records) {
        $pq_curPage = ($total_Records > 0) ? (int)ceil($total_Records / $pq_rPP) : 1;
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    $builder->orderBy('accgrpmstn.acc_grp_name');
    $builder->limit($pq_rPP, $offset);
    $result = $builder->get()->getResultArray();

    // Build rows
    $records = [];
    foreach ($result as $values) {
        $data_confirmstatus = ($values['crs_is_active'] == 1) ? 'INACTIVE' : 'ACTIVE';
        $acc_status_vl      = ($values['crs_is_active'] == 1) ? 0 : 1;

        $records[] = [
            'chkbx' => '<input type="checkbox" name="group_id[]" class="checkbox groups_row" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus="'.$data_confirmstatus.'" dataid="'.$values['acc_grp_id'].'" value="'.$values['acc_grp_id'].'"> ',
            'acc_grp_id'    => $values['acc_grp_id'],
            'group_id'      => $values['acc_grp_id'],
            'group_name'    => ucwords($values['acc_grp_name']),
            'print_name'    => $values['acc_grp_alias'],
            'primary'       => ($values['crs_mst_is_primary']==1)?'YES':'NO',
            'under'         => '',
            'acc_status_vl' => $acc_status_vl,
            'grp_status'    => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
            'alert_acc_status' => $data_confirmstatus
        ];
    }

    echo json_encode([
        "totalRecords" => $total_Records,
        "curPage"      => $pq_curPage,
        "data"         => $records
    ]);
}



public function all_account_groups_export()
{
    $builder = $this->db->table('accgrpmstn ag');

    $builder->select("
        ag.acc_grp_id,
        ag.acc_grp_name,
        ag.acc_grp_alias,

        uc.crs_mst_is_primary,
        uc.crs_is_active,
        uc.crs_mst_parent_id,
        uc.under_crs_mst_id
    ");

    $builder->join(
        'undercrsmt uc',
        'uc.crs_mst_id = ag.acc_grp_id
         AND uc.crs_mst_type = 2
         AND uc.cmpfymastr_id = '.$this->fy_id,
        'left'
    );

    $builder->where('ag.cmp_id', $this->company_id);
    $builder->where('uc.cmpfymastr_id IS NOT NULL', null, false);

    $builder->orderBy('ag.acc_grp_name', 'ASC');

    $rows = $builder->get()->getResultArray();

    $primaryGroups = [
        1  => "Owner's Fund",
        2  => "Non Current Liabilities",
        3  => "Non Current Assets",
        4  => "Current Liabilities",
        5  => "Current Assets",
        6  => "Opening Stock",
        7  => "Purchase",
        8  => "Sales",
        9  => "Closing Stock",
        10 => "Direct Income",
        11 => "Direct Expenses",
        12 => "Indirect Income",
        13 => "Indirect Expenses"
    ];

    foreach ($rows as &$row)
    {
        $under_name = '';

        if ((int)$row['crs_mst_is_primary'] === 1)
        {
            $under_name = $primaryGroups[$row['crs_mst_parent_id']] ?? '';
        }
        else
        {
            if (!empty($row['under_crs_mst_id']))
            {
                $group = $this->db->table('accgrpmstn')
                    ->select('acc_grp_name')
                    ->where('acc_grp_id', $row['under_crs_mst_id'])
                    ->get()
                    ->getRowArray();

                $under_name = $group['acc_grp_name'] ?? '';
            }
        }

        $row['group_id']   = $row['acc_grp_id'];
        $row['group_name'] = $row['acc_grp_name'];
        $row['print_name'] = $row['acc_grp_alias'];
        $row['primary']    = ($row['crs_mst_is_primary'] == 1) ? 'YES' : 'NO';
        $row['under']      = $under_name;
        $row['grp_status'] = ($row['crs_is_active'] == 1) ? 'ACTIVE' : 'INACTIVE';
    }

    // Add 13 Static Parent Groups
    $staticGroups = [];

    foreach ($primaryGroups as $id => $name)
    {
        $staticGroups[] = [
            'group_id'   => $id,
            'group_name' => $name,
            'print_name' => $name,
            'primary'    => 'YES',
            'under'      => '',
            'grp_status' => 'ACTIVE'
        ];
    }

    // Static groups first, then ERP groups
    $rows = array_merge($staticGroups, $rows);

    return $rows;
}

   public function update_account($data,$account_id){	
	
   
	    $account_master_tbl = "acctmaster";	    
	    $table = $this->db->table($account_master_tbl)
	    		->where('LOWER(acc_name)',strtolower(trim($data['acc_name'])))
				 ->where('cmp_id',$this->company_id)
				->where('bsd_id IS NULL')
                ->where('acc_id !=',$account_id)
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table($account_master_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('acc_id !=',$account_id)
			   ->where('bsd_id IS NULL')
        	   ->Where('LOWER(acc_alias)',strtolower(trim($data['acc_alias'])))
        	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	    }
	    
		$account_info = $this->account_info($account_id);
		$acc_is_restrict = (int) $account_info['acc_is_restrict'];
		if ($acc_is_restrict != 2 && $acc_is_restrict != 3) {
	    $this->db->table($account_master_tbl)
		      ->where('cmp_id',$this->company_id)
			  ->where('bsd_id IS NULL')
			  ->where('acc_id',$account_id)->update($data);
		} 
			  
		return ['status' => true, 'account_id' => $account_id];	
   } 
   
  public function update_account_adrs_info($data, $account_id)
	{
		$acc_adrs_data = [
			'acc_addr1'   => $data['acc_add1'],
			'acc_addr2'   => $data['acc_add2'],
			'acc_city'    => $data['acc_city'],
			'acc_state'   => (int)$data['acc_state'],    
			'acc_country' => (int)$data['acc_country'],   
			'acc_pin'     => (int)$data['acc_pin'],
			'acc_email'   => $data['acc_email'],
			'acc_mobile'  => $data['acc_mobile'],
		];

		// Check if record exists
		$exists = $this->db->table('acctmstadr')
					->where('acc_id', (int)$account_id)
					->where('cmp_id', (int)$this->company_id)
					->countAllResults();

		if ($exists > 0) {
			//  Record exists → UPDATE
			$this->db->table('acctmstadr')
				->where('acc_id', (int)$account_id)
				->where('cmp_id', (int)$this->company_id)
				->update($acc_adrs_data);
		} else {
			//  Record does not exist → INSERT
			$acc_adrs_data['acc_id']  = (int)$account_id;
			$acc_adrs_data['cmp_id']  = (int)$this->company_id;

			$this->db->table('acctmstadr')
				->insert($acc_adrs_data);
		}
	}
	
	
	public function update_account_other_info($data, $account_id)
	{ 
	//acctmstdet
		$acc_other_data = [
			'acc_aadhaar'      => $data['acc_aadhaar'],
			'acc_pan'          => $data['acc_pan'],
			'acc_tan'          => $data['acc_tan'],
			'acc_it_jurisd'    => (int)$data['acc_it_jurisd'],    
			'acc_gstin'        => $data['acc_gstin'],   
			'acc_sac'          => $data['acc_sac'],
			'acc_is_sys_acc'   => (int)$data['acc_is_sys_acc'],
			'acc_is_sez'       => (int)$data['acc_is_sez'],
		];

		// Check if record exists
		$exists = $this->db->table('acctmstdet')
					->where('acc_id', (int)$account_id)
					->where('cmp_id', (int)$this->company_id)
					->countAllResults();

		if ($exists > 0) {
			//  Record exists → UPDATE
			$this->db->table('acctmstdet')
				->where('acc_id', (int)$account_id)
				->where('cmp_id', (int)$this->company_id)
				->update($acc_other_data);
		
				SaveErrorLog($this->db->getlastquery().'<br />');
			
		} else {
			//  Record does not exist → INSERT
			$acc_other_data['acc_id']  = (int)$account_id;
			$acc_other_data['cmp_id']  = (int)$this->company_id;

			$this->db->table('acctmstdet')->insert($acc_other_data);
			SaveErrorLog($this->db->getlastquery().'<br />');
		}
	}
	
   
   public function crsmst_info($account_id){
	   $undercrsmt_tbl = "undercrsmt";	 
	 return  $this->db->table($undercrsmt_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('crs_mst_id',$account_id)
			   ->where('crs_mst_type',1)
			   ->where('cmpfymastr_id ',$this->fy_id)
        	   ->get()->getRowArray();  
   }
   
   function get_parimary_groups($acc_grp_parent_id, $group_id = 0){	 
     $builder = $this->db->table('accgrpmstn');
	 $builder->join("undercrsmt", "undercrsmt.under_crs_mst_id = accgrpmstn.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     $builder->where('accgrpmstn.cmp_id',$this->company_id);
	 $builder->where('undercrsmt.crs_mst_parent_id', $acc_grp_parent_id);
	 $builder->where('undercrsmt.crs_mst_is_primary', 1);
	 $builder->where('accgrpmstn.acc_grp_id !=', $group_id);
     /* return $response = $builder->get()->getRowArray();
	  $account_grp_tbl ='accgrpmstn'; 
      $builder = $this->db->table($account_grp_tbl);
      $builder->where('acc_grp_primary', 1);
      $builder->where('acc_grp_parent_id', $acc_grp_parent_id);
      if($group_id != 0)
      	$builder->where('acc_grp_id !=', $group_id);
	  */
  	  return $builder->get()->getResultArray();   	   
   }
  
  public function moveGroupSubtree($moved_id, $new_parent_id, $new_root_id, $new_is_primary = 0)
{
    // If nothing changes, exit early
    if ((int)$new_parent_id === (int)$moved_id) {
        return true;
    }

    $cmpId = $this->company_id;
    $fyId  = $this->fy_id;

    $this->db->transStart();

    // 1) Update the moved node itself
    $this->db->table('undercrsmt')
        ->where('cmp_id', $cmpId)
        ->where('cmpfymastr_id', $fyId)      // fixed trailing space bug
        ->whereIn('crs_mst_type', [1, 2])
        ->where('crs_mst_id', $moved_id)
        ->set([
            'crs_mst_parent_id'  => $new_parent_id,
            'under_crs_mst_id'   => $new_parent_id,  // keep aligned with parent
            'under_main_id'      => $new_root_id,    // new ultimate root
            'crs_mst_is_primary' => (int)$new_is_primary, // 0 for moved node in your scenario
        ])
        ->update();

    // 2) Update all descendants’ under_main_id to the new root
    //    (parents of descendants remain as-is, matching scenario B)
    $sql = "
        WITH RECURSIVE cte AS (
            SELECT crs_mst_id
            FROM undercrsmt
            WHERE crs_mst_parent_id = :moved_id:
              AND cmp_id = :cmp_id:
              AND cmpfymastr_id = :fy_id:
              AND crs_mst_type IN (1,2)
            UNION ALL
            SELECT u.crs_mst_id
            FROM undercrsmt u
            JOIN cte ON u.crs_mst_parent_id = cte.crs_mst_id
            WHERE u.cmp_id = :cmp_id:
              AND u.cmpfymastr_id = :fy_id:
              AND u.crs_mst_type IN (1,2)
        )
        UPDATE undercrsmt u
           SET under_main_id = :new_root:
         WHERE u.cmp_id = :cmp_id:
           AND u.cmpfymastr_id = :fy_id:
           AND u.crs_mst_id IN (SELECT crs_mst_id FROM cte);
    ";

    $this->db->query($sql, [
        'moved_id' => $moved_id,
        'cmp_id'   => $cmpId,
        'fy_id'    => $fyId,
        'new_root' => $new_root_id,
    ]);

    $this->db->transComplete();
    return $this->db->transStatus();
}

   
   public function update_undercrsmt($data,$account_id,$crs_mst_type){		
	    $undercrsmt_tbl = "undercrsmt";	 
		$table = $this->db->table($undercrsmt_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('crs_mst_id',$account_id)
			   ->where('crs_mst_type',$crs_mst_type)
			   ->where('cmpfymastr_id ',$this->fy_id)
        	   ->get()->getRowArray(); 
		if($table){
		 $updata =array("under_crs_mst_id"=>$data['under_crs_mst_id'],"crs_mst_parent_id"=>$data['crs_mst_parent_id'],
		                "under_main_id"=>$data['under_main_id'],"crs_mst_is_primary"=>$data['crs_mst_is_primary']);	
		 $this->db->table($undercrsmt_tbl)
		      ->where('cmp_id',$this->company_id)
			  ->where('crs_mst_id',$account_id)
			  ->where('crs_mst_type',$crs_mst_type)
			  ->where('cmpfymastr_id',$this->fy_id)->update($updata);	
		}else{
		 $this->db->table($undercrsmt_tbl)->insert($data);		
		}
		
		return ['status' => true, 'account_id' => $account_id];	
   } 
      
   public function update_opbal_entry($data, $account_id)
{
    $accoppybal_tbl = "accoppybal";

    $existing = $this->db->table($accoppybal_tbl)
        ->where('cmp_id', $this->company_id)
        ->where('hobo_id', $this->bo_id)
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('acc_id', $account_id)
        ->get()
        ->getRow();

    if ($existing) {
        // Record exists — update it
        $this->db->table($accoppybal_tbl)
            ->where('cmp_id', $this->company_id)
            ->where('hobo_id', $this->bo_id)
            ->where('cmpfymastr_id', $this->fy_id)
            ->where('acc_id', $account_id)
            ->update($data);
    } else {
        // Record does not exist — insert it
        $data['cmp_id']        = $this->company_id;
        $data['hobo_id']       = $this->bo_id;
        $data['cmpfymastr_id'] = $this->fy_id;
        $data['acc_id']        = $account_id;

        $this->db->table($accoppybal_tbl)->insert($data);
    }
}  
   
  public function update_account_details($data, $account_id)
{
    $acctmstdet_tbl = "acctmstdet";

    $existing = $this->db->table($acctmstdet_tbl)
        ->where('cmp_id', $this->company_id)
        ->where('acc_id', $account_id)
        ->get()
        ->getRow();

    if ($existing) {
        // Record exists — update it
        $this->db->table($acctmstdet_tbl)
            ->where('cmp_id', $this->company_id)
            ->where('acc_id', $account_id)
            ->update($data);
    } else {
        // Record does not exist — insert it
        $data['cmp_id']  = $this->company_id;
        $data['acc_id']  = $account_id;

        $this->db->table($acctmstdet_tbl)->insert($data);
    }
}
      
   public function update_acc_txn_opbal_entry($txn_data,$account_id,$acc_txn_type){		
	    $accttxnmst_tbl = "accttxnmst";
	    $this->db->table($accttxnmst_tbl)->where('cmp_id',$this->company_id)
		->where('acc_id',$account_id)->where('acc_txn_type',$acc_txn_type)
		->where('vch_txn_id IS NULL')->update($txn_data);
		
   }
   
   public function opening_balances_info($account_id){
	   $builder =  $this->db->table("accoppybal");
	   $builder->join("acctmaster","acctmaster.acc_id=accoppybal.acc_id");
	   $builder->where('accoppybal.cmpfymastr_id', $this->fy_id);
	   $builder->where('accoppybal.cmp_id', $this->company_id);
	   $builder->where('accoppybal.acc_id', $account_id);
       $builder->where('acctmaster.bsd_id IS NULL'); 	   
	   $resposne = $builder->get()->getRowArray();
	   return $resposne;
    }
   
   public function update_account_adrs($data,$account_id){
	    $acctaddmst_tbl = $this->company_id.'_acctaddmst_'.$this->session->get('ses_comp_fy_id');
		$exists = $this->db->table($acctaddmst_tbl)->where('acc_id',$account_id)->countAllResults();
	    if($exists){
		   $this->db->table($acctaddmst_tbl)->where('acc_id',$account_id)->update($data);	    	
		}else{
		    $acc_adrs_insert_data   = [
						'acc_id'         => $account_id,
						'comp_id'        => $this->company_id,
						'acc_add1'       => $data['acc_add1'],
						'acc_add2'       => $data['acc_add2'],
						'acc_country'    => $data['acc_country'],
						'acc_state'      => $data['acc_state'],						
						'acc_city'       => $data['acc_city'],					
						'acc_pin'        => $data['acc_pin']						
					    ];
		    $this->db->table($acctaddmst_tbl)->insert($acc_adrs_insert_data);	
		}
	 	
   }
   
   public function remove_single_accounts($id){
		$this->db->table("accoppybal")->where('hobo_id',$this->bo_id)->where('cmpfymastr_id',$this->fy_id)->where('acc_id',$id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("acctmaster")->where('acc_id',$id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("acctmstdet")->where('acc_id',$id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("acctmstadr")->where('acc_id',$id)->where('cmp_id',$this->company_id)->delete();
		
		$this->db->table("accttxnmst")->where('hobo_id',$this->bo_id)->where('acc_id',$id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("undercrsmt")->where('crs_mst_type',1)->where('crs_mst_id',$id)->where('cmp_id',$this->company_id)->delete();
	    return TRUE;
	 }
 	 
  public function check_account_exists($id,$name){
        $data = $this->db->table("acctmaster")->select('acc_name')->where('acc_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(acc_name)', strtolower($name))->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
     
     
     
 public function changestatus_single_accounts($id,$status){
  $this->db->table("acctmaster")->where('cmp_id',$this->company_id)->where("acc_id",$id)->update(["acc_is_active"=>$status]);
  $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",1)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]);
 
 }     
	 
 public function check_account_txn_exists($id){
        $data = $this->db->table("accttxnmst")->select('acc_id')->where('vch_txn_id IS NOT NULL')->where('vch_txn_id >',0)->where('txn_id IS NOT NULL')->where('cmp_id',$this->company_id)->where('acc_id', $id)->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
     
     
     
 public function check_account_opn_exists($id){
        $data = $this->db->table("accoppybal")->select('acc_id')->where('acc_op_bal >',0)->where('cmpfymastr_id',$this->fy_id)->where('cmp_id',$this->company_id)->where('acc_id', $id)->where('hobo_id', $this->bo_id)->get()->getRowArray();
       
       if($data)
        return true; 
        else
        return false;
     }
     
     public function check_account_group_exists($id,$name){
        $data = $this->db->table("accgrpmstn")->select('acc_grp_name')->where('acc_grp_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(acc_grp_name)', strtolower($name))->get()->getRowArray();
     // echo $this->db->getlastquery();
      
        if($data)
        return true; 
        else
        return false;
     }	
     
     public function changestatus_single_group($id,$status){
  $this->db->table("accgrpmstn")->where('cmp_id',$this->company_id)->where("acc_grp_id",$id)->update(["acc_grp_is_active"=>$status]);
  $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",2)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]);
 
 } 
 
     
  public function get_account_name($id){
        $data = $this->db->table("acctmaster")->select('acc_name')->where('cmp_id',$this->company_id)->where('acc_id', $id)->get()->getRowArray();
        $acc_name = $data['acc_name'] ?? '';
        return $acc_name;
     }
	 
	 
public function all_accounts_export()
{
    $builder = $this->db->table('acctmaster am');

    $builder->select("
        am.acc_id,
        am.bsd_id,
        am.acc_name AS account_name,
        am.acc_alias,
        am.acc_print_name,
        am.acc_is_active,

        uc.under_crs_mst_id,
        uc.crs_mst_parent_id,

        ag.acc_grp_name,
        gp.acc_grp_parent_name,

        adr.acc_mobile,
        adr.acc_email,
        adr.acc_addr1,
        adr.acc_addr2,
        adr.acc_city,
        adr.acc_pin,
        adr.acc_state,
        adr.acc_country,

        det.acc_gstin,
        det.acc_pan,

        COALESCE(op.acc_op_bal,0) AS op_bal_export
    ");

    // Address
    $builder->join(
        'acctmstadr adr',
        'adr.acc_id = am.acc_id
        AND adr.cmp_id = am.cmp_id',
        'left'
    );

    // Details
    $builder->join(
        'acctmstdet det',
        'det.acc_id = am.acc_id
        AND det.cmp_id = am.cmp_id',
        'left'
    );

    // Opening Balance
    $builder->join(
        'accoppybal op',
        'op.acc_id = am.acc_id
        AND op.cmpfymastr_id = '.$this->fy_id.'
        AND op.hobo_id = '.$this->bo_id,
        'left'
    );

    // Account Group Mapping
    $builder->join(
        'undercrsmt uc',
        'uc.crs_mst_id = am.acc_id
        AND uc.crs_mst_type = 1
        AND uc.cmpfymastr_id = '.$this->fy_id.'
        AND uc.cmp_id = '.$this->company_id,
        'left'
    );

    $builder->join(
        'accgrpmstn ag',
        'ag.acc_grp_id = uc.under_crs_mst_id',
        'left'
    );

    $builder->join(
        'grpparentn gp',
        'gp.acc_grp_parent_id = uc.crs_mst_parent_id',
        'left'
    );

    $builder->where('am.cmp_id', $this->company_id);

    $builder->orderBy('am.acc_name', 'ASC');

    $result = $builder->get()->getResultArray();
    
    

    foreach ($result as &$row)
    {
        // ===================================
        // GROUP NAME
        // ===================================

        $row['group_name'] = '';

        // Bill Sundry Account
        if (!empty($row['bsd_id']) && $row['bsd_id'] > 0)
        {
            $bsd_group = $this->db->table('undercrsmt uc')
                ->select('gp.acc_grp_parent_name')
                ->join(
                    'grpparentn gp',
                    'gp.acc_grp_parent_id = uc.crs_mst_parent_id',
                    'left'
                )
                ->where('uc.crs_mst_id', $row['bsd_id'])
                ->orderBy('uc.under_crs_mt_id', 'DESC')
                ->get()
                ->getRowArray();

            if (!empty($bsd_group['acc_grp_parent_name']))
            {
                $row['group_name'] = $bsd_group['acc_grp_parent_name'];
            }
            else
            {
                $row['group_name'] = 'Duties & Taxes';
            }
        }
        else
        {
            $row['group_name'] =
                ((int)$row['under_crs_mst_id'] === 0)
                ? ($row['acc_grp_parent_name'] ?? '')
                : ($row['acc_grp_name'] ?? '');
        }

        // Final Safety
        if (empty(trim($row['group_name'])))
        {
            $row['group_name'] = 'Duties & Taxes';
        }

        // ===================================
        // OPENING BALANCE
        // ===================================

        $row['bal_type'] =
            (($row['op_bal_export'] ?? 0) < 0)
            ? 'CR.'
            : 'DR.';

        $row['op_bal_export'] =
            $row['op_bal_export'] ?? 0;

        // ===================================
        // STATUS
        // ===================================

        $row['acc_status'] =
            ($row['acc_is_active'] == 1)
            ? 'ACTIVE'
            : 'INACTIVE';

        // ===================================
        // STATE NAME
        // ===================================

        $row['state_name'] = '';

        if (!empty($row['acc_state']))
        {
            $state_info = $this->CommonModel->get_state_info(
                $row['acc_country'],
                $row['acc_state']
            );

            if (!empty($state_info))
            {
                $row['state_name'] = $state_info['state_name'];
            }
        }

        // ===================================
        // COUNTRY NAME
        // ===================================

        $row['country_name'] = '';

        if (!empty($row['acc_country']))
        {
            if ((int)$row['acc_country'] === 1)
            {
                $row['country_name'] = 'India';
            }
        }

        // ===================================
        // EXPORT FIELDS
        // ===================================

        $row['city_name']    = $row['acc_city'] ?? '';
        $row['vendor_code']  = '';
        $row['contact_name'] = '';
        $row['acc_phone']    = '';
    }

    return $result;
}


  public function ajax_accounts_list($is_export = 0)
{
    $account_master_tbl    = 'acctmaster';
    $account_groupn_tbl    = 'accgrpmstn';
    $account_opbalance_tbl = 'accoppybal';
    $group_parent_tbl      = 'grpparentn';
    $undercrsmt_tbl        = 'undercrsmt';

    $comp_id     = $this->session->get('ses_company_id');
    $show_hidden = !empty($_POST['show_hidden']) ? 1 : 0;

    // Pagination
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int) $_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"])     ? (int) $_POST["pq_rpp"]     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;

    // Parse pq_filter (search)
    $filters = [];
    $filter_mode = 'and';
    if (!empty($_POST['pq_filter'])) {
        $filter_data = json_decode($_POST['pq_filter'], true);
        if (!empty($filter_data['data']) && is_array($filter_data['data'])) {
            $filters = $filter_data['data'];
        }
        if (!empty($filter_data['mode']) && strtolower($filter_data['mode']) === 'or') {
            $filter_mode = 'or';
        }
    }

    // Base builder (for data)
    $builder = $this->db->table($account_master_tbl);

    // Select only needed columns
    $builder->select("
        {$account_master_tbl}.acc_id,
        {$account_master_tbl}.acc_name,
		{$account_master_tbl}.acc_is_restrict,
        {$account_opbalance_tbl}.acc_op_bal,
        {$undercrsmt_tbl}.under_crs_mst_id,
        {$undercrsmt_tbl}.crs_mst_parent_id,
        {$undercrsmt_tbl}.crs_is_active,
        {$account_groupn_tbl}.acc_grp_name AS acc_group_name,
        {$group_parent_tbl}.acc_grp_parent_name AS acc_parent_group_name
    ");

    // Joins
    $builder->join(
        $account_opbalance_tbl,
        "{$account_opbalance_tbl}.acc_id = {$account_master_tbl}.acc_id
         AND {$account_opbalance_tbl}.cmpfymastr_id = {$this->fy_id}
         AND {$account_opbalance_tbl}.hobo_id = " . $this->db->escape($this->bo_id),
        'left'
    );

    $builder->join(
        $undercrsmt_tbl,
        "{$undercrsmt_tbl}.crs_mst_id = {$account_master_tbl}.acc_id
         AND {$undercrsmt_tbl}.crs_mst_type = 1
         AND {$undercrsmt_tbl}.cmpfymastr_id = {$this->fy_id}
         AND {$undercrsmt_tbl}.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->join(
        $account_groupn_tbl,
        "{$account_groupn_tbl}.acc_grp_id = {$undercrsmt_tbl}.under_crs_mst_id",
        'left'
    );

    $builder->join(
        $group_parent_tbl,
        "{$group_parent_tbl}.acc_grp_parent_id = {$undercrsmt_tbl}.crs_mst_parent_id",
        'left'
    );

    // Mandatory filters
    $builder->where("{$account_master_tbl}.cmp_id", $comp_id);
    $builder->where("{$undercrsmt_tbl}.cmpfymastr_id IS NOT NULL");
    $builder->where("{$undercrsmt_tbl}.cmpfymastr_id", $this->fy_id);

    // Hide accounts with zero / null opening balance when requested
    if ($show_hidden == 1) {
        $builder->groupStart()
            ->where("{$account_opbalance_tbl}.acc_op_bal IS NULL")
            ->orWhere("{$account_opbalance_tbl}.acc_op_bal", 0)
        ->groupEnd();
    }

    // Apply search filters
    if (!empty($filters)) {
        $builder->groupStart();
        foreach ($filters as $f) {
            $col  = $f['dataIndx']  ?? '';
            $val  = isset($f['value']) ? trim($f['value']) : '';
            $cond = strtolower($f['condition'] ?? 'contain');

            if ($col === 'account_name' && $val !== '') {
                $likeVal = strtolower($val);
                if ($cond === 'begin') {
                    $expr = "{$account_master_tbl}.acc_name ILIKE " . $this->db->escape($likeVal . '%');
                } elseif ($cond === 'end') {
                    $expr = "{$account_master_tbl}.acc_name ILIKE " . $this->db->escape('%' . $likeVal);
                } elseif ($cond === 'equal') {
                    $expr = "{$account_master_tbl}.acc_name ILIKE " . $this->db->escape($likeVal);
                } else { // contain
                    $expr = "{$account_master_tbl}.acc_name ILIKE " . $this->db->escape('%' . $likeVal . '%');
                }

                if ($filter_mode === 'or') {
                    $builder->orWhere($expr, null, false);
                } else {
                    $builder->where($expr, null, false);
                }
            }
        }
        $builder->groupEnd();
    }

    // Count before limit (lightweight)
    $countBuilder = clone $builder;
    $countBuilder->select('1'); // lightweight select
    $total_Records = $countBuilder->countAllResults(false);

    // Pagination window
    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_Records) {
        $pq_curPage = max(1, (int) ceil($total_Records / $pq_rPP));
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    $builder->orderBy("{$account_master_tbl}.acc_name");
    if ($is_export == 0) {
        $builder->limit($pq_rPP, $offset);
    }

    $result  = $builder->get()->getResultArray();

    // Build records
    $records = [];
    foreach ($result as $values) {
        $show_group = ($values['under_crs_mst_id'] == 0)
            ? $values['acc_parent_group_name']
            : $values['acc_group_name'];

        $records[] = [
            'chkbx' => '<input name="account_ids[]" class="checkbox accounts_row" data-confirmstatus="' . (($values['crs_is_active'] == 1) ? 'INACTIVE' : 'ACTIVE') . '" data-id="' . $values['acc_id'] . '" type="checkbox" value="' . $values['acc_id'] . '">',
            'acc_id'          => $values['acc_id'],
            'account_name'    => ucwords($values['acc_name']),
            'group_name'      => $show_group,
            'op_bal'          => formatAmount(abs($values['acc_op_bal'])),
            'op_bal_export'   => abs($values['acc_op_bal']),
            'bal_type'        => ($values['acc_op_bal'] < 0) ? 'CR.' : 'DR.',
            'isedited'        => ($values['acc_is_restrict']==0 )? 1:0,
            'count_txn'       => 0,
			'acc_is_restrict' => $values['acc_is_restrict'],
            'acc_status_vl'   => ($values['crs_is_active'] == 1) ? 0 : 1,
            'acc_status'      => ($values['crs_is_active'] == 1) ? 'ACTIVE' : 'INACTIVE',
            'alert_acc_status'=> ($values['crs_is_active'] == 1) ? 'INACTIVE' : 'ACTIVE'
        ];
    }

    return "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($records) . "}";
} 
	
	public function ajax_accounts_address_list($is_export = 0)
{
    $account_master_tbl      = 'acctmaster';
    $account_groupn_tabl     = 'accgrpmstn';
    $account_opbalance_tabl  = 'accoppybal';
    $group_parent_tbl        = 'grpparentn';
    $undercrsmt_tbl          = "undercrsmt";
    $account_detail_tbl      = "acctmstdet";

    $comp_id  = $this->session->get('ses_company_id');

    $show_hidden = (!empty($_POST["show_hidden"])) ? 1 : 0;

    // Pagination params
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;

    $builder = $this->db->table($account_master_tbl);

    // SELECT
    $builder->select("
        $account_master_tbl.*, 
        $account_opbalance_tabl.acc_op_bal, 
        $undercrsmt_tbl.under_crs_mst_id,
        $undercrsmt_tbl.crs_mst_parent_id,
        $undercrsmt_tbl.crs_is_active,
        $undercrsmt_tbl.cmpfymastr_id,
        $account_groupn_tabl.acc_grp_name AS acc_group_name,
        $group_parent_tbl.acc_grp_parent_name AS acc_parent_group_name,
        $account_detail_tbl.acc_aadhaar,
        $account_detail_tbl.acc_pan,
        $account_detail_tbl.acc_tan,
        $account_detail_tbl.acc_it_jurisd,
        $account_detail_tbl.acc_gstin,
        $account_detail_tbl.acc_sac,
        $account_detail_tbl.acc_is_sys_acc,
        $account_detail_tbl.acc_is_sez
    ");

    // Joins
    $builder->join(
        $account_opbalance_tabl,
        "$account_opbalance_tabl.acc_id = $account_master_tbl.acc_id AND 
        $account_opbalance_tabl.cmpfymastr_id = $this->fy_id AND 
        $account_opbalance_tabl.hobo_id = " . $this->db->escape($this->bo_id),
        'left'
    );

    $builder->join(
        $undercrsmt_tbl,
        "$undercrsmt_tbl.crs_mst_id = $account_master_tbl.acc_id 
         AND $undercrsmt_tbl.crs_mst_type = 1
         AND $undercrsmt_tbl.cmpfymastr_id = $this->fy_id
         AND $undercrsmt_tbl.cmp_id = $this->company_id",
        'left'
    );

    // NEW JOIN ADDED (ACCOUNT DETAIL TABLE)
    $builder->join(
        $account_detail_tbl,
        "$account_detail_tbl.acc_id = $account_master_tbl.acc_id 
         AND $account_detail_tbl.cmp_id = $account_master_tbl.cmp_id",
        'left'
    );

    $builder->where("$undercrsmt_tbl.cmpfymastr_id IS NOT NULL");
    $builder->where("$undercrsmt_tbl.cmpfymastr_id", $this->fy_id);

    $builder->join(
        $account_groupn_tabl,
        "$account_groupn_tabl.acc_grp_id = $undercrsmt_tbl.under_crs_mst_id",
        'left'
    );

    $builder->join(
        $group_parent_tbl,
        "$group_parent_tbl.acc_grp_parent_id = $undercrsmt_tbl.crs_mst_parent_id",
        'left'
    );

    if ($show_hidden == 1) {
        $builder->groupStart()
                ->where("$account_opbalance_tabl.acc_op_bal IS NULL")
                ->orWhere("$account_opbalance_tabl.acc_op_bal", 0)
                ->groupEnd();
    }

    $builder->where("$account_master_tbl.cmp_id", $comp_id);

    $countBuilder   = clone $builder;
    $total_Records  = $countBuilder->countAllResults(false);

    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($pq_rPP > 0 && $offset > $total_Records) {
        $pq_curPage = (int)ceil($total_Records / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    $builder->orderBy("$account_master_tbl.acc_name");

    if ($is_export == 0) {
        $builder->limit($pq_rPP, $offset);
    }

    $result = $builder->get()->getResultArray();

    $records = [];

    foreach ($result as $values) {

        if ($values['under_crs_mst_id'] == 0) {
            $show_group = $values['acc_parent_group_name'];
        } else {
            $show_group = $values['acc_group_name'];
        }

        // Existing address logic
        $addrInfo = [];
        try {

            $accInfo = $this->account_info($values['acc_id']);

            if (!empty($accInfo['address_info'])) {

                $addrInfo = $accInfo['address_info'];

                $addr1      = $addrInfo['contact_add1'] ?? '';
                $addr2      = $addrInfo['contact_add2'] ?? '';
                $city       = $addrInfo['contact_city'] ?? '';
                $pincode    = ($addrInfo['contact_pin'] > 0) ? $addrInfo['contact_pin'] : '';
                $state_id   = $addrInfo['contact_state'] ?? '';
                $country_id = $addrInfo['contact_country'] ?? '';

                $mobile     = $addrInfo['contact_mobile'] ?? '';
                $wamobile   = $addrInfo['contact_wamobile'] ?? '';
                $email      = $addrInfo['contact_email'] ?? '';

            } else {

                $addr1 = $addr2 = $city = $pincode = $state_id = $country_id = '';
                $mobile = $wamobile = $email = '';
            }

        } catch (\Throwable $e) {
            $addrInfo = [];
        }

        $records[] = [

            'acc_id'       => $values['acc_id'],
            'account_name' => ucwords($values['acc_name']),
            'group_name'   => $show_group,

            'op_bal'        => formatAmount(abs($values['acc_op_bal'])),
            'op_bal_export' => abs($values['acc_op_bal']),
            'bal_type'      => ($values['acc_op_bal'] < 0) ? 'CR.' : 'DR.',

            'isedited'      => 1,
            'count_txn'     => 0,

            'acc_status_vl' => ($values['crs_is_active'] == 1) ? 0 : 1,
            'acc_status'    => ($values['crs_is_active'] == 1) ? 'ACTIVE' : 'INACTIVE',
            'alert_acc_status' => ($values['crs_is_active'] == 1) ? 'INACTIVE' : 'ACTIVE',

            // Address
            'addr1'      => $addr1,
            'addr2'      => $addr2,
            'city'       => $city,
            'pincode'    => $pincode,
            'state_id'   => $state_id,
            'country_id' => $country_id,
            'mobile'     => $mobile,
            'wamobile'   => $wamobile,
            'email'      => $email,
            'aadhaar'    => $values['acc_aadhaar'],
            'pan'        => $values['acc_pan'],
            'tan'        => $values['acc_tan'],
            'gstin'      => $values['acc_gstin'],
            'sac'        => $values['acc_sac'],
            'it_jurisd'  => $values['acc_it_jurisd'],
            'is_sys_acc' => $values['acc_is_sys_acc'],
            'is_sez'     => $values['acc_is_sez']
        ];
    }

    return "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($records) . "}";
}
	
	public function ajax_accounts_search($type,$search){
	   if($type=='acc'){
	    $response = $this->db->table('acctmaster')
					->select('acc_id, acc_name')
					->where('acc_is_active', 1)
					->where('bsd_id IS NULL')
					->where('cmp_id', $this->company_id)
					->groupStart() // start group for LIKE conditions
						->like('LOWER(acc_name)', strtolower(trim($search)))
						->orLike('LOWER(acc_alias)', strtolower(trim($search)))
					->groupEnd()   // end group
					->get()
					->getResultArray();
	       
	   } 
	   if($type=='bsd'){
	    $response = $this->db->table('acctmaster')
					->select('acc_id, acc_name')
					->where('acc_is_active', 1)
					->where('bsd_id IS NOT NULL')
					->where('cmp_id', $this->company_id)
					->groupStart() // start group for LIKE conditions
						->like('LOWER(acc_name)', strtolower(trim($search)))
						->orLike('LOWER(acc_alias)', strtolower(trim($search)))
					->groupEnd()   // end group
					->get()
					->getResultArray(); 
	       
	      } 	    
        $suggestions=[];
	   if($response){
		  foreach($response as $row){
			 $suggestions[] = array("label"=>ucwords($row['acc_name']),"id"=>$row['acc_id']); 
		  } 
	   }
	   return json_encode($suggestions);
     }
 	 
     public function get_account_group_name($id){
       $data = $this->db->table("accgrpmstn")->select('acc_grp_name')->where('cmp_id', $this->company_id)->where('acc_grp_id', $id)->get()->getRowArray();
       return $data['acc_grp_name'];
     }
     
  public function add_group($data){
       $account_grp_tbl = "accgrpmstn";	  
	   $exists1 = $this->db->table( $account_grp_tbl)
	              ->where('LOWER(acc_grp_name)', strtolower(trim($data['acc_grp_name'])))
	              ->where('acc_grp_is_active',1)
				  ->where('cmp_id',$this->company_id)
				  ->get()->getRowArray(); 
	   $exists2 = $this->db->table( $account_grp_tbl)
	              ->where('LOWER(acc_grp_alias)', strtolower(trim($data['acc_grp_alias'])))
	              ->where('acc_grp_is_active',1)
				  ->where('cmp_id',$this->company_id)
				  ->get()->getRowArray(); 
	    if($exists1)
		 return "-1";

	   else  if($exists2)
		 return "-2";
	   else{
		  $this->db->table($account_grp_tbl)->insert($data);
		  $group_id = $this->db->insertID();
		  return $group_id;
	     }
      }   

   public function remove_groups($ids,$comp_id){
       $ids = explode(",",$ids);
        foreach($ids as $group_id){			
			$this->db->table("accgrpmstn")->where('cmp_id',$this->company_id)->where('acc_grp_id',$group_id)->delete();
			$this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where('crs_mst_type',2)->where('crs_mst_id',$group_id)->delete();
		 }
		return TRUE;
	 } 	
	 
	public function remove_single_groups($id){
       $this->db->table("accgrpmstn")->where('cmp_id',$this->company_id)->where('acc_grp_id',$id)->delete();
	   $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where('crs_mst_type',2)->where('crs_mst_id',$id)->delete();	
	   return TRUE;
	 } 	
	 
	 
   public function update_group($update_data,$group_id){
        $this->db->table("accgrpmstn")->where('cmp_id',$this->company_id)->where('acc_grp_id',$group_id)->update($update_data);
		return true;	
   } 	
   
   public function get_acc_master_type($acc_master_id){
        return $this->db->table("acctmaster")->where('cmp_id',$this->company_id)->where('acc_id',$acc_master_id)->get()->getRowArray();
			
   } 
   
   
   function group_primary_dropdown(){		      
        $data =  $this->db->table("grpparentn")->where('acc_grp_parent_id !=',14)->orderBy('acc_grp_parent_name')->get()->getResultArray();
		$final_result = array();
		$final_result['']  = 'Choose';
		if($data){
		  foreach($data as $row){
		    $final_result[$row['acc_grp_parent_id']] = ucwords($row['acc_grp_parent_name']);			   
		  }
		}
	  return $final_result;	
     }

  function group_main_dropdownnn($group_id = 0){		      
            $builder =  $this->db->table("accgrpmstn acgrpmst");
			$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     		$builder->select("undercrsmt.crs_mst_parent_id as acc_grp_parent_id,acgrpmst.acc_grp_id,acgrpmst.acc_grp_name");
     		$builder->where('acgrpmst.cmp_id',$this->company_id);
			if($group_id != 0)
				$builder->where('undercrsmt.under_crs_mst_id !=', $group_id);
			$builder->where("undercrsmt.cmpfymastr_id",$this->fy_id);
			$builder->where("undercrsmt.cmpfymastr_id IS NOT NULL");	
            $builder->where('undercrsmt.crs_mst_parent_id !=',14);  // emit branch parent group id
			$builder->orderBy('acgrpmst.acc_grp_name');
			$data = $builder->get()->getResultArray();				
	  return $data;	
     }
	 
  function group_main_dropdown($group_id = 0){		      
            $builder =  $this->db->table("accgrpmstn acgrpmst");
			$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     		$builder->where('acgrpmst.cmp_id',$this->company_id);
			if($group_id != 0)
				$builder->where('undercrsmt.under_crs_mst_id !=', $group_id);
			$builder->where("undercrsmt.cmpfymastr_id",$this->fy_id);
			$builder->where("undercrsmt.cmpfymastr_id IS NOT NULL");
            $builder->where('undercrsmt.crs_mst_parent_id !=',14);  // emit branch parent group id
			$builder->orderBy('acgrpmst.acc_grp_name');
			$data = $builder->get()->getResultArray();
			$final_result = array();
			$final_result['']  = 'Choose';
			if($data){
			   foreach($data as $row){
					  $final_result[$row['acc_grp_id']] = ucwords($row['acc_grp_name']);			   
					}
				}
	  return $final_result;	
     }
  public function load_accounts_ledger_totals($account_id, $from_date, $to_date, $view)
	{
		return ["debit_total"=>0,"credit_total"=>0];
	}
	
	function account_info($account_id, $acc_type = '')
{
    $acctmaster_tbl  = "acctmaster";
    $acctmstdet_tbl  = "acctmstdet";
    $undercrsmt_tbl  = "undercrsmt";
    $uuid            = $this->session->get('uuid');

    // -------- Account core --------
    $builder = $this->db->table($acctmaster_tbl);
    $builder->join($acctmstdet_tbl, "$acctmstdet_tbl.acc_id = $acctmaster_tbl.acc_id", 'left');
    $builder->join(
        $undercrsmt_tbl,
        "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id
         AND $undercrsmt_tbl.cmpfymastr_id = $this->fy_id
         AND $undercrsmt_tbl.cmp_id = $this->company_id",
        'left'
    );

    $builder->where("$undercrsmt_tbl.cmpfymastr_id", $this->fy_id);
    $builder->where("$undercrsmt_tbl.cmpfymastr_id IS NOT NULL");
    $builder->where("$acctmaster_tbl.acc_id", $account_id);
    $builder->where("$acctmaster_tbl.cmp_id", $this->company_id);

    $response = $builder->get()->getRowArray();
    if (!$response) {
        return [];
    }

    $response['address_info'] = [];
     
	
    // -------- Address / contact from external DB --------
	$builderq = $this->db->table("acctmstadr");
	 $builderq->where("acc_id", $account_id);
     $builderq->where("cmp_id", $this->company_id);
	 $adrs_response = $builderq->get()->getRowArray();
	 if( $adrs_response){
		$response['address_info']=["contact_country"=>$adrs_response['acc_country'] ,
		                           "contact_state"=>$adrs_response['acc_state'],
								   "contact_city"=>$adrs_response['acc_city'],
								   "contact_add1"=>$adrs_response['acc_addr1'],
								   "contact_add2"=>$adrs_response['acc_addr2'],
								   "contact_pin"=>$adrs_response['acc_pin'],
								   "contact_email"=>$adrs_response['acc_email'],
								   "contact_mobile"=>$adrs_response['acc_mobile'],
								   "contact_wamobile"=>$adrs_response['acc_mobile']
								   ]; 
	 }else{
		$response['address_info']=["contact_country"=>1 ,
		                           "contact_state"=>0,
								   "contact_city"=>"",
								   "contact_add1"=>"",
								   "contact_add2"=>"",
								   "contact_pin"=>"",
								   "contact_email"=>"",
								   "contact_mobile"=>"",
								   "contact_wamobile"=>""
								   ];  
	 }
    
    return $response;
}

  
   
   // Sub Ledger Account
   function sublgr_account_info($account_id){	 
        $subacctmst_tbl  = "subacctmst";
		$acctmaster_tbl  = "acctmaster";
		$builder = $this->db->table($subacctmst_tbl);
		$builder->join("$acctmaster_tbl","$acctmaster_tbl.acc_id=$subacctmst_tbl.acc_id","left");
		$builder->select("$subacctmst_tbl.sub_acc_id,$subacctmst_tbl.sub_acc_name,$subacctmst_tbl.acc_id");
		if($account_id!='-1')
		$builder->where("$subacctmst_tbl.sub_acc_id", $account_id);
		$builder->where("$subacctmst_tbl.sub_acc_is_active", 1);
		$builder->where("$subacctmst_tbl.cmp_id",$this->company_id);		
		$response = $builder->get()->getRowArray();
	    return $response;
   }
   
   
   function main_group_info($acc_grp_id){
     $builder = $this->db->table('accgrpmstn acgrpmst');
	 $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     $builder->where('acgrpmst.cmp_id',$this->company_id);
	 $builder->where('undercrsmt.crs_mst_id', $acc_grp_id);
	 return $builder->get()->getRowArray();   	   
   }
   
   function sub_group_info($acc_grp_id){
	 $builder = $this->db->table('accgrpmstn acgrpmst');
	 $builder->join("undercrsmt", "undercrsmt.under_crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     $builder->where('acgrpmst.cmp_id',$this->company_id);
	 $builder->where('undercrsmt.under_main_id', $acc_grp_id);
	 return $builder->get()->getRowArray();
   }

   function check_group_restriction($acc_grp_id, $tag){	 
        $builder =  $this->db->table("accgrpmstn acgrpmst");
	    $builder->join("undercrsmt", "undercrsmt.under_crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     	$builder->where("undercrsmt.under_crs_mst_id",$acc_grp_id);	
       	$builder->where("acgrpmst.acc_grp_restrict",$tag);
        $row = $builder->get()->getRowArray(); 
        return $row;
        //return $this->db->table('accgrpmstn')->where('cmp_id',$this->company_id)->where('acc_grp_id', $acc_grp_id)->where('acc_grp_restrict', $tag)->get()->getRowArray();   	   
   }

   function group_parent_info($acc_grp_id){	 
      return $this->db->table("grpparentn")->where('acc_grp_parent_id', $acc_grp_id)->get()->getRowArray();   	   
   }
   
   function bill_by_bill_txn($account_id){
       $builder = $this->db->table('billoppybal txn');
		$builder->select("			
				bmst.acc_id,			
				txn.bill_ref_id,
				ABS(txn.bill_op_bal) as bill_txn_amt,
				bmst.bill_ref_name,
				bmst.bill_due_date,				
				CASE 
					WHEN txn.bill_op_bal > 0 THEN 'D'
					WHEN txn.bill_op_bal < 0 THEN 'C'
					ELSE 'D'
				END AS bill_txn_dr_cr	
			", false);
		$builder->join('billmaster bmst', 'bmst.bill_ref_id = txn.bill_ref_id', 'left');
		$builder->where('bmst.bill_is_active',1);
		$builder->where('bmst.cmp_id', $this->company_id);
		$builder->where('txn.cmp_id', $this->company_id);
		$builder->where('txn.hobo_id', $this->bo_id);
		$builder->where('cmpfymastr_id',$this->fy_id);
		$builder->where('bmst.acc_id', $account_id);
		$result = $builder->get()->getResultArray();
		$final = array();
		if($result){
			foreach($result as $row){
				$account_id = $row['acc_id'];
				$final[$account_id][]= $row;
			}
		}
		$master =[];
		foreach($final as $acc_id => $res){
			$master[] = [
                    'acc_id' => $acc_id,
                    'bills_txn_list' => $res
                ];
		}		
    	return $master;
   }
   
   function sblgr_txn($account_id){
       $builder = $this->db->table('suboppybal txn');
		$builder->select("			
				bmst.acc_id,			
				txn.sub_acc_id as bill_ref_id,
				ABS(txn.sub_acc_op_bal) as bill_txn_amt,
				bmst.sub_acc_name as bill_ref_name,
				bmst.sub_due_date as bill_due_date,				
				CASE 
					WHEN txn.sub_acc_op_bal > 0 THEN 'D'
					WHEN txn.sub_acc_op_bal < 0 THEN 'C'
					ELSE 'D'
				END AS bill_txn_dr_cr	
			", false);
		$builder->join('subacctmst bmst', 'bmst.sub_acc_id = txn.sub_acc_id', 'left');
		$builder->where('bmst.sub_acc_is_active',1);
		$builder->where('bmst.cmp_id', $this->company_id);
		$builder->where('txn.cmp_id', $this->company_id);
		$builder->where('txn.hobo_id', $this->bo_id);
		$builder->where('cmpfymastr_id',$this->fy_id);
		$builder->where('bmst.acc_id', $account_id);
		$result = $builder->get()->getResultArray();
		$final = array();
		
		if($result){
			foreach($result as $row){
				$account_id = $row['acc_id'];
				$final[$account_id][]= $row;
			}
		}
		$master =[];
		foreach($final as $acc_id => $res){
			$master[] = [
                    'acc_id' => $acc_id,
                    'sblgr_txn_list' => $res
                ];
		}		
    	return $master;
   }
   
   function check_parent_restriction($acc_grp_parent_id, $tag){	        	
    return $this->db->table("grpparentn")->where('acc_grp_parent_id', $acc_grp_parent_id)->where('acc_grp_parent_restrict', $tag)->get()->getRowArray();   	   
   }

public function load_accounts_trial_balance_groups($group_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1, $consoview = 0)
{
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
	
	$fy_start_date = date('Y-m-d', strtotime(validate_fy_from_date('')));
	$prev_to_date  = date('Y-m-d', strtotime($from_date . ' -1 day'));
	$isFyStart     = (strtotime($from_date) === strtotime($fy_start_date));
	$remote_address = $_SERVER['REMOTE_ADDR'];
	/* if($remote_address=='103.172.223.178' || $remote_address=='103.172.223.179'){
if($isFyStart)
	echo 'yes';
else
	echo 'no';

	} */

    $gid       = (int) $group_id;
    $is_consolidated = ($consoview == 1);

    $fmt   = function ($n) { return ($n != 0) ? formatAmount(abs($n)) : ''; };
    $label = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };

    // 1) Group hierarchy
    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->get()->getResultArray();

    $children = [];
    $grpName = [];
    foreach ($gRows as $r) {
        $pg = (int)($r['under_crs_mst_id'] ?? 0);
        $cg = (int)$r['acc_grp_id'];
        $grpName[$cg] = $r['acc_grp_name'];
        if ($pg > 0) { $children[$pg][] = $cg; }
    }

    $getAllDescendantGids = function(int $rootGid) use (&$children, &$getAllDescendantGids): array {
        $descendants = [];
        if (!empty($children[$rootGid])) {
            foreach ($children[$rootGid] as $childGid) {
                $descendants[] = $childGid;
                $descendants = array_merge($descendants, $getAllDescendantGids($childGid));
            }
        }
        return $descendants;
    };
    $allDesc = $getAllDescendantGids($gid);

    // 2) Accounts under the group (unique)
    $groupIdsForAccounts = array_merge([$gid], $allDesc);
    $accRows = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN (1, 14) AND u.cmp_id = {$this->company_id}", 'left')
        ->whereIn('u.under_crs_mst_id', $groupIdsForAccounts)
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->groupBy('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->get()->getResultArray();

    $accountsByGroup = [];
    $accIds = [];
    foreach ($accRows as $ar) {
        $gForAcc = (int)$ar['under_crs_mst_id'];
        $aid = (int)$ar['acc_id'];
        if (!isset($accountsByGroup[$gForAcc])) { $accountsByGroup[$gForAcc] = []; }
        if (!isset($accountsByGroup[$gForAcc][$aid])) {
            $accountsByGroup[$gForAcc][$aid] = ['id' => $aid, 'name' => $ar['acc_name']];
            $accIds[] = $aid;
        }
    }
    if (empty($accIds)) return [];

    // 3) Branch names
    $branch_names = [];
    $branch_rows = $this->univaictly->table('hobomaster')->select('hobo_id, hobo_name')->where('cmp_id', $this->company_id)->get()->getResultArray();
    foreach ($branch_rows as $r) {
        $branch_names[(int)$r['hobo_id']] = $r['hobo_name'];
    }
    if (!isset($branch_names[$this->bo_id])) {
        $branch_names[$this->bo_id] = 'Current Branch';
    }

    // 4) Opening balances (by branch if consolidated)
    $opMap = [];

/*
|--------------------------------------------------------------------------
| STEP 4A: Base Opening (same as now)
|--------------------------------------------------------------------------
*/
$opQB = $this->db->table('accoppybal ob')
    ->select('ob.acc_id, ob.hobo_id, SUM(ob.acc_op_bal) AS op_bal', false)
    ->where('ob.cmp_id', $this->company_id)
    ->where('ob.cmpfymastr_id', $this->fy_id)
    ->whereIn('ob.acc_id', $accIds)
    ->groupBy('ob.acc_id, ob.hobo_id');

if (!$is_consolidated) {
    $opQB->where('ob.hobo_id', $this->bo_id);
}

foreach ($opQB->get()->getResultArray() as $r) {
    $opMap[(int)$r['acc_id']][(int)$r['hobo_id']] = (float)$r['op_bal'];
}


/*
|--------------------------------------------------------------------------
| STEP 4B: ADD MOVEMENT BEFORE FROM DATE (CRITICAL FIX)
|--------------------------------------------------------------------------
*/
if (!$isFyStart) {

    $mvQB = $this->db->table('accttxnmst a')
        ->select("
            a.acc_id,
            vc.hobo_id,
            SUM(
                CASE
                    WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt
                    WHEN a.acc_txn_dr_cr = 2 THEN -a.acc_txn_amt
                    ELSE 0
                END
            ) AS net_move
        ", false)

        ->join('vchtxnconso vc', 'vc.vch_txn_id = a.vch_txn_id', 'inner')

        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
        ->where('a.acc_txn_type', 1)

        ->where('a.vch_txn_id >', 0)
        ->where('a.vch_txn_id IS NOT NULL', null, false)

        ->where('vc.vch_date >=', $fy_start_date)
        ->where('vc.vch_date <=', $prev_to_date)

        ->groupBy('a.acc_id, vc.hobo_id');

    if (!$is_consolidated) {
        $mvQB->where('vc.hobo_id', $this->bo_id);
    }

    foreach ($mvQB->get()->getResultArray() as $r) {
        $aid = (int)$r['acc_id'];
        $bid = (int)$r['hobo_id'];

        if (!isset($opMap[$aid][$bid])) {
            $opMap[$aid][$bid] = 0;
        }

        $opMap[$aid][$bid] += (float)$r['net_move'];
    }
}
	

    // 5) Transactions (use vchtxnconso.vch_date)
		
    $txMap = [];
    $txQB = $this->db->table('accttxnmst a');

$txQB->select("
    a.acc_id,
	vc.hobo_id,
    SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
    SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
", false)

->join('vchtxnconso vc', 'vc.vch_txn_id = a.vch_txn_id', 'inner') // ✅ IMPORTANT

->where('a.acc_txn_type', 1)
->where('a.cmp_id', $this->company_id)
->whereIn('a.acc_id', $accIds)

->where('a.vch_txn_id >', 0)
->where('a.vch_txn_id IS NOT NULL', null, false)

// ✅ USE VOUCHER DATE (MATCH LEDGER)
->where('vc.vch_date >=', $from_date)
->where('vc.vch_date <=', $to_date)

->groupBy('a.acc_id, vc.hobo_id');

if (!empty($this->bo_id)) {
    $txQB->where('vc.hobo_id', $this->bo_id); // ✅ MATCH LEDGER
}

    foreach ($txQB->get()->getResultArray() as $t) {
        $txMap[(int)$t['acc_id']][(int)$t['hobo_id']] = [
            'dr' => (float)($t['dr'] ?? 0.0),
            'cr' => (float)($t['cr'] ?? 0.0)
        ];
    }
	
	/* if($remote_address=='103.172.223.178' || $remote_address=='103.172.223.179'){
		echo $this->db->getlastquery();
		echo '<br>';
	} */

    // Helper: get branch ids that actually have data for a given acc_id
    $branchesWithDataForAcc = function($aid) use ($opMap, $txMap) {
        $set = [];
        if (isset($opMap[$aid])) { foreach ($opMap[$aid] as $bid => $v) { if ($v != 0) $set[$bid] = true; } }
        if (isset($txMap[$aid])) { foreach ($txMap[$aid] as $bid => $arr) { if (($arr['dr'] ?? 0) != 0 || ($arr['cr'] ?? 0) != 0) $set[$bid] = true; } }
        return array_keys($set);
    };

    $final = [];

    // Process groups
    foreach ($children[$gid] ?? [] as $sgid) {
        $gidsInSubtree = array_merge([$sgid], $getAllDescendantGids($sgid));
        $branch_totals = [];

        foreach ($gidsInSubtree as $gg) {
            foreach ($accountsByGroup[$gg] ?? [] as $acc) {
                $aid = $acc['id'];
                $bids = $is_consolidated ? $branchesWithDataForAcc($aid) : [$this->bo_id];
                foreach ($bids as $branch_id) {
                    $op = $opMap[$aid][$branch_id] ?? 0.0;
                    $dr = $txMap[$aid][$branch_id]['dr'] ?? 0.0;
                    $cr = $txMap[$aid][$branch_id]['cr'] ?? 0.0;
                    if ($op != 0 || $dr != 0 || $cr != 0) {
                        if (!isset($branch_totals[$branch_id])) { $branch_totals[$branch_id] = ['op' => 0.0, 'dr' => 0.0, 'cr' => 0.0]; }
                        $branch_totals[$branch_id]['op'] += $op;
                        $branch_totals[$branch_id]['dr'] += $dr;
                        $branch_totals[$branch_id]['cr'] += $cr;
                    }
                }
            }
        }

        foreach ($branch_totals as $branch_id => $totals) {
            $closing_sum = $totals['op'] + $totals['dr'] - $totals['cr'];
            if ((string)$nill_transaction === '0' && ($totals['dr'] == 0 && $totals['cr'] == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing_sum) < 0.01) continue;

            $final[] = [
                'entity_type' => 'grp', 'entity_name' => $grpName[$sgid] ?? '', 'entity_id' => $sgid,
                'branch_name' => $branch_names[$branch_id] ?? 'Unknown',
                'debit' => $fmt($totals['dr']), 'credit' => $fmt($totals['cr']), 'balance' => $fmt($closing_sum),
                'balance_type' => $label($closing_sum), 'op_balance' => $fmt($totals['op']),
                'op_balance_total' => $totals['op'], 'debit_total' => $totals['dr'], 'credit_total' => $totals['cr'], 'balance_total' => $closing_sum,
            ];
        }
    }

    // Process direct accounts
    foreach ($accountsByGroup[$gid] ?? [] as $acc) {
        $aid = $acc['id'];
        $bids = $is_consolidated ? $branchesWithDataForAcc($aid) : [$this->bo_id];

        foreach ($bids as $branch_id) {
            $opBal = $opMap[$aid][$branch_id] ?? 0.0;
            $dr    = $txMap[$aid][$branch_id]['dr'] ?? 0.0;
            $cr    = $txMap[$aid][$branch_id]['cr'] ?? 0.0;
            $closing = $opBal + $dr - $cr;

            if ($opBal == 0 && $dr == 0 && $cr == 0) continue;
            if ((string)$nill_transaction === '0' && ($dr == 0 && $cr == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing) < 0.01) continue;

            $final[] = [
                'entity_type' => 'acc', 'entity_name' => $acc['name'], 'entity_id' => $aid,
                'branch_name' => $branch_names[$branch_id] ?? 'Unknown',
                'debit' => $fmt($dr), 'credit' => $fmt($cr), 'balance' => $fmt($closing),
                'balance_type' => $label($closing), 'op_balance' => $fmt($opBal),
                'op_balance_total' => $opBal, 'debit_total' => $dr, 'credit_total' => $cr, 'balance_total' => $closing,
            ];
        }
    }

    return $final;
}
public function load_accounts_trial_balance_groups5thjan2026($group_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1)
{
    $account_master_tbl = 'acctmaster';
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $gid       = (int)$group_id;

    // 1) Get group hierarchy
    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->get()->getResultArray();

    $children = []; $grpName = [];
    foreach ($gRows as $r) {
        $pg = (int)($r['under_crs_mst_id'] ?? 0);
        $cg = (int)$r['acc_grp_id'];
        $grpName[$cg] = $r['acc_grp_name'];
        if ($pg > 0) { $children[$pg][] = $cg; }
    }

    $getAllDescendantGids = function(int $rootGid) use (&$children, &$getAllDescendantGids): array {
        $descendants = [];
        if (!empty($children[$rootGid])) {
            foreach ($children[$rootGid] as $childGid) {
                $descendants[] = $childGid;
                $descendants = array_merge($descendants, $getAllDescendantGids($childGid));
            }
        }
        return $descendants;
    };
    $allDesc = $getAllDescendantGids($gid);

    // 2) Get all accounts under the main group and all its descendants
    $groupIdsForAccounts = array_merge([$gid], $allDesc);
    $accRows = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN (1, 14) AND u.cmp_id = {$this->company_id}", 'left')
        ->whereIn('u.under_crs_mst_id', $groupIdsForAccounts)
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->get()->getResultArray();

    $accountsByGroup = []; $accIds = [];
    foreach ($accRows as $ar) {
        $gForAcc = (int)$ar['under_crs_mst_id'];
        $aid = (int)$ar['acc_id'];
        if (!isset($accountsByGroup[$gForAcc])) { $accountsByGroup[$gForAcc] = []; }
        $accountsByGroup[$gForAcc][] = ['id' => $aid, 'name' => $ar['acc_name']];
        $accIds[] = $aid;
    }

    // 3) Get Balances
    $opMap = [];
    if (!empty($accIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            // ->where('ob.bsd_id IS NULL', null, false) // <<< THIS WAS THE BUG. REMOVED.
            ->whereIn('ob.acc_id', $accIds)
            ->groupBy('ob.acc_id');
        if (!empty($this->bo_id)) { $opQB->where('ob.hobo_id', $this->bo_id); }

        foreach ($opQB->get()->getResultArray() as $r) {
            $opMap[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0.0);
        }
    }

    $txByAcc = [];
    if (!empty($accIds)) {
        $txQB = $this->db->table('accttxnmst a')
            ->select("a.acc_id, SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr, SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr", false)
            ->where('a.acc_txn_type', 1)->where('a.cmp_id', $this->company_id)->whereIn('a.acc_id', $accIds)
            ->where('a.vch_txn_id >', 0)->where('a.vch_txn_id IS NOT NULL', null, false)
            ->where('a.acc_txn_date >=', $from_date)->where('a.acc_txn_date <=', $to_date)->groupBy('a.acc_id');
        if (!empty($this->bo_id)) { $txQB->where('a.hobo_id', $this->bo_id); }
        
        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = ['dr' => (float)($t['dr'] ?? 0.0), 'cr' => (float)($t['cr'] ?? 0.0)];
        }
    }

    $fmt = function ($n) { return ($n != 0) ? formatAmount(abs($n)) : ''; };
    $label = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };
    
    $final = [];

    // 4) Process Direct Sub-Groups
    $directChildren = $children[$gid] ?? [];
    foreach ($directChildren as $sgid) {
        $gidsInSubtree = array_merge([$sgid], $getAllDescendantGids($sgid));
        $op_sum = 0.0; $dr_sum = 0.0; $cr_sum = 0.0;
        foreach ($gidsInSubtree as $gg) {
            foreach ($accountsByGroup[$gg] ?? [] as $acc) {
                $aid = $acc['id'];
                $op_sum += $opMap[$aid] ?? 0.0;
                $dr_sum += $txByAcc[$aid]['dr'] ?? 0.0;
                $cr_sum += $txByAcc[$aid]['cr'] ?? 0.0;
            }
        }
        $closing_sum = $op_sum + ($dr_sum - $cr_sum);
        if ((string)$nill_transaction === '0' && ($dr_sum == 0 && $cr_sum == 0)) continue;
        if ((string)$nill_balance === '0' && abs($closing_sum) < 0.01) continue;
        
        $final[] = [
            'entity_type' => 'grp', 'entity_name' => $grpName[$sgid], 'entity_id' => $sgid,
            'debit' => $fmt($dr_sum), 'credit' => $fmt($cr_sum), 'balance' => $fmt($closing_sum),
            'balance_type' => $label($closing_sum), 'op_balance' => $fmt($op_sum),
            'op_balance_total' => $op_sum, 'debit_total' => $dr_sum, 'credit_total' => $cr_sum, 'balance_total' => $closing_sum,
        ];
    }

    // 5) Process Direct Accounts
    foreach ($accountsByGroup[$gid] ?? [] as $acc) {
        $aid = $acc['id'];
        $opBal = $opMap[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0; $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closing = $opBal + ($dr - $cr);
        if ((string)$nill_transaction === '0' && ($dr == 0 && $cr == 0)) continue;
        if ((string)$nill_balance === '0' && abs($closing) < 0.01) continue;

        $final[] = [
            'entity_type' => 'acc', 'entity_name' => $acc['name'], 'entity_id' => $aid,
            'debit' => $fmt($dr), 'credit' => $fmt($cr), 'balance' => $fmt($closing),
            'balance_type' => $label($closing), 'op_balance' => $fmt($opBal),
            'op_balance_total' => $opBal, 'debit_total' => $dr, 'credit_total' => $cr, 'balance_total' => $closing,
        ];
    }
    return $final;
}

public function load_accounts_trial_balance_groups11dec($group_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1)
{
    $account_master_tbl = 'acctmaster';

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $gid       = (int)$group_id;

    /* ─────────────────────────────────────────────────────────────
     * 1) GROUP TREE (type=2) — parent pointer is under_crs_mst_id
     * ───────────────────────────────────────────────────────────── */
    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->get()->getResultArray();

    $children = [];               // parent_gid => [child_gid,...]
    $grpName  = [];               // gid => name
    foreach ($gRows as $r) {
        $pg = (int)($r['under_crs_mst_id'] ?? 0); // PARENT = under_crs_mst_id
        $cg = (int)$r['acc_grp_id'];              // CHILD  = current group id
        $grpName[$cg] = $r['acc_grp_name'];
        if ($pg > 0) {
            if (!isset($children[$pg])) $children[$pg] = [];
            $children[$pg][] = $cg;
        }
    }

    // Direct sub-groups of the selected group
    $directChildren = $children[$gid] ?? [];

    // All descendants of the selected group (for fetching accounts in one go)
    $allDesc = [];
    $stack = [$gid];
    while (!empty($stack)) {
        $curr = array_pop($stack);
        if (!empty($children[$curr])) {
            foreach ($children[$curr] as $c) {
                if (!in_array($c, $allDesc, true)) {
                    $allDesc[] = $c;
                    $stack[]   = $c;
                }
            }
        }
    }

    /* ─────────────────────────────────────────────────────────────
     * 2) ACCOUNTS: fetch those under parent + ALL its descendants
     *    (we won’t show sub-group accounts, but we need them to
     *     compute sub-group totals recursively)
     * ───────────────────────────────────────────────────────────── */
    $groupIdsForAccounts = array_merge([$gid], $allDesc);
    if (empty($groupIdsForAccounts)) $groupIdsForAccounts = [0];

    $accRows = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN(1,14) AND u.cmp_id = {$this->company_id}", 'left')
        ->whereIn('u.under_crs_mst_id', $groupIdsForAccounts)
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->orderBy('a.acc_name', 'asc')
        ->get()->getResultArray();

    $accountsByGroup = []; // [gid => [ ['id'=>acc_id,'name'=>acc_name], ... ]]
    foreach ($groupIdsForAccounts as $ig) $accountsByGroup[$ig] = [];
    $accIds = [];
    foreach ($accRows as $ar) {
        $gForAcc = (int)$ar['under_crs_mst_id'];
        $aid     = (int)$ar['acc_id'];
        $accountsByGroup[$gForAcc][] = ['id' => $aid, 'name' => $ar['acc_name']];
        $accIds[] = $aid;
    }

    /* ─────────────────────────────────────────────────────────────
     * 3) OPENINGS (accoppybal) + TRANSACTIONS (accttxnmst)
     * ───────────────────────────────────────────────────────────── */
    $opMap = []; // acc_id => opening (+DR, -CR)
    if (!empty($accIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            ->where('ob.bsd_id IS NULL', null, false)
            ->whereIn('ob.acc_id', $accIds)
            ->groupBy('ob.acc_id');
        if (!empty($this->bo_id)) $opQB->where('ob.hobo_id', $this->bo_id);

        foreach ($opQB->get()->getResultArray() as $r) {
            $opMap[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0.0);
        }
    }

    $txByAcc = []; // acc_id => ['dr'=>..,'cr'=>..]
    if (!empty($accIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
		->where('a.vch_txn_id >',0)
        ->where('a.vch_txn_id IS NOT NULL', null, false)
        ->where('a.acc_txn_date >=', $from_date)
        ->where('a.acc_txn_date <=', $to_date)
        ->groupBy('a.acc_id');
        if (!empty($this->bo_id)) $txQB->where('a.hobo_id', $this->bo_id);

        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0.0),
                'cr' => (float)($t['cr'] ?? 0.0),
            ];
        }
    }

    // helpers
    $fmt   = function ($n) { return ($n > 0) ? formatAmount($n) : ''; };
    $label = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };

    // closing per-account (opening + (DR − CR))
    $closingByAcc = [];
    foreach ($accIds as $aid) {
        $op = $opMap[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr);
    }

    // helper: get all group ids in subtree rooted at $root (including $root)
    $subtreeOf = function(int $root) use ($children): array {
        $out = [$root];
        $st  = [$root];
        while (!empty($st)) {
            $c = array_pop($st);
            if (!empty($children[$c])) {
                foreach ($children[$c] as $ch) {
                    $out[] = $ch;
                    $st[]  = $ch;
                }
            }
        }
        return $out;
    };

    $final = [];

    /* ─────────────────────────────────────────────────────────────
     * 4) SHOW ONLY DIRECT SUB-GROUP ROWS (no sub-group accounts)
     *    Each sub-group row totals all accounts in its subtree.
     * ───────────────────────────────────────────────────────────── */
    foreach ($directChildren as $sgid) {
        $sname = $grpName[$sgid] ?? ('Group ' . $sgid);

        $gidsInSubtree = $subtreeOf($sgid);

        $dr_sum = 0.0; $cr_sum = 0.0; $closing_sum = 0.0;
        foreach ($gidsInSubtree as $gg) {
            foreach ($accountsByGroup[$gg] ?? [] as $acc) {
                $aid        = $acc['id'];
                $dr_sum    += $txByAcc[$aid]['dr'] ?? 0.0;
                $cr_sum    += $txByAcc[$aid]['cr'] ?? 0.0;
                $closing_sum += $closingByAcc[$aid] ?? 0.0;
            }
        }

        $hasTxn = ($dr_sum > 0 || $cr_sum > 0);
        if ((string)$nill_transaction === '0' && !$hasTxn) {
            // hide
        } elseif ((string)$nill_balance === '0' && $closing_sum == 0.0) {
            // hide
        } else {
            $final[] = [
                'entity_type'      => 'grp',
                'entity_name'      => $sname,
                'entity_id'        => $sgid,

                'debit'            => $fmt($dr_sum),
                'credit'           => $fmt($cr_sum),

                'balance'          => $fmt(abs($closing_sum)),
                'balance_type'     => $label($closing_sum),
                'op_balance'       => 0,
            'op_balance_total' => 0,
                'debit_total'      => $dr_sum,
                'credit_total'     => $cr_sum,
                'balance_total'    => $closing_sum,
            ];
        }
    }

    /* ─────────────────────────────────────────────────────────────
     * 5) SHOW ONLY ACCOUNTS DIRECTLY UNDER THE SELECTED GROUP
     *    (do NOT show accounts under sub-groups)
     * ───────────────────────────────────────────────────────────── */
    foreach ($accountsByGroup[$gid] ?? [] as $acc) {
        $aid   = $acc['id'];
        $aname = $acc['name'];

        $dr      = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr      = $txByAcc[$aid]['cr'] ?? 0.0;
        $closing = $closingByAcc[$aid] ?? 0.0;
        $opBal   = $opMap[$aid] ?? 0.0;

        $hasTxnAcc = ($dr > 0 || $cr > 0);
        if ((string)$nill_transaction === '0' && !$hasTxnAcc) continue;
        if ((string)$nill_balance === '0' && $closing == 0.0) continue;

        $final[] = [
            'entity_type'      => 'acc',
            'entity_name'      => $aname,
            'entity_id'        => $aid,

            'debit'            => $fmt($dr),
            'credit'           => $fmt($cr),

            'balance'          => $fmt(abs($closing)),
            'balance_type'     => $label($closing),

            'debit_total'      => $dr,
            'credit_total'     => $cr,
            'balance_total'    => $closing,

            'op_balance'       => $fmt(abs($opBal)),
            'op_balance_total' => $opBal,
        ];
    }

    return $final;
}

   
  public function load_accounts_trial_balance_groupsoldee($group_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1)
{
    $account_master_tbl = 'acctmaster';

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    // ─────────────────────────────────────────────────────────────────────
    // 1) Subgroups (children of $group_id)
    // ─────────────────────────────────────────────────────────────────────
    $sgRows = $this->db->table('accgrpmstn acgrpmst')
        ->select('acgrpmst.acc_grp_id, acgrpmst.acc_grp_name')
        ->join("undercrsmt u", "u.crs_mst_id = acgrpmst.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.under_main_id', $group_id)
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->orderBy('acgrpmst.acc_grp_name', 'asc')
        ->get()->getResultArray();

    $subgroupIds = array_map(static fn($r) => (int)$r['acc_grp_id'], $sgRows);
	
    // ─────────────────────────────────────────────────────────────────────
    // 2) Accounts under each subgroup, plus accounts directly under $group_id
    // ─────────────────────────────────────────────────────────────────────
    $groupIdsForAccounts = $subgroupIds;
    $groupIdsForAccounts[] = (int)$group_id; // include parent group for its direct accounts
    if (empty($groupIdsForAccounts)) $groupIdsForAccounts = [0];

    $accRows = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name, u.under_crs_mst_id, u.crs_mst_is_primary')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type = 1 AND u.cmp_id = {$this->company_id}", 'left')
        ->whereIn('u.under_crs_mst_id', $groupIdsForAccounts)		
        ->orderBy('a.acc_name', 'asc')
        ->get()->getResultArray();

    $accountsByGroup = []; // [group_id => [ ['id'=>acc_id,'name'=>acc_name], ... ]]
    foreach ($groupIdsForAccounts as $gid) $accountsByGroup[(int)$gid] = [];
    $accIds = [];
    foreach ($accRows as $ar) {
        $gid = (int)$ar['under_crs_mst_id'];
        $accountsByGroup[$gid][] = ['id' => (int)$ar['acc_id'], 'name' => $ar['acc_name']];
        $accIds[] = (int)$ar['acc_id'];
    }

    // ─────────────────────────────────────────────────────────────────────
    // 3) Opening balances from accoppybal (for running/closing balance)
    // ─────────────────────────────────────────────────────────────────────
    $opMap = []; // acc_id => opening (signed; +DR, -CR)
    if (!empty($accIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            ->whereIn('ob.acc_id', $accIds)
            ->groupBy('ob.acc_id');
        if (!empty($this->bo_id)) {
            $opQB->where('ob.hobo_id', $this->bo_id);
        }
        foreach ($opQB->get()->getResultArray() as $r) {
            $opMap[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // 4) Posted transactions (DR/CR sums) from accttxnmst for the window
    // ─────────────────────────────────────────────────────────────────────
    $txByAcc = []; // acc_id => ['dr'=>..,'cr'=>..]
    if (!empty($accIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
        ->where('a.acc_txn_date >=', $from_date)
        ->where('a.acc_txn_date <=', $to_date)
		->where('a.vch_txn_id >',0)
        ->where('a.vch_txn_id IS NOT NULL', null, false)
        ->groupBy('a.acc_id');
        if (!empty($this->bo_id)) {
            $txQB->where('a.hobo_id', $this->bo_id);
        }
        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }
    }

    // helpers
    $fmt   = function ($n) { return ($n > 0) ? formatAmount($n) : ''; };
    $label = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };

    // compute closing per-account (running balance end)
    $closingByAcc = []; // acc_id => closing (signed)
    foreach ($accIds as $aid) {
        $op = $opMap[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr); // running closing
    }

    $final = [];

    // helper to emit a row
    $emitRow = function(array $row) use (&$final) { $final[] = $row; };

    // ─────────────────────────────────────────────────────────────────────
    // 5) SUBGROUP totals (sum of account closings) + their accounts
    // ─────────────────────────────────────────────────────────────────────
    foreach ($sgRows as $sg) {
        $sid   = (int)$sg['acc_grp_id'];
        $sname = $sg['acc_grp_name'];

        $dr_sum = 0.0; $cr_sum = 0.0; $closing_sum = 0.0;

        foreach ($accountsByGroup[$sid] ?? [] as $acc) {
            $aid = $acc['id'];
            $dr_sum     += $txByAcc[$aid]['dr'] ?? 0.0;
            $cr_sum     += $txByAcc[$aid]['cr'] ?? 0.0;
            $closing_sum += $closingByAcc[$aid] ?? 0.0;
        }

        // hide rules
        $hasTxn = ($dr_sum > 0 || $cr_sum > 0);
        if ((string)$nill_transaction === '0' && !$hasTxn) {
            // skip this subgroup
        } elseif ((string)$nill_balance === '0' && $closing_sum == 0.0) {
            // skip this subgroup
        } else {
            $emitRow([
                'entity_type'      => 'grp',
                'entity_name'      => $sname,
                'entity_id'        => $sid,

                'debit'            => $fmt($dr_sum),
                'credit'           => $fmt($cr_sum),

                'balance'          => $fmt(abs($closing_sum)),
                'balance_type'     => $label($closing_sum),

                'debit_total'      => $dr_sum,
                'credit_total'     => $cr_sum,
                'balance_total'    => $closing_sum,

                'op_balance'       => '',
                'op_balance_total' => 0,
            ]);
        }

        // Accounts under this subgroup
        foreach ($accountsByGroup[$sid] ?? [] as $acc) {
            $aid   = $acc['id'];
            $aname = $acc['name'];

            $dr = $txByAcc[$aid]['dr'] ?? 0.0;
            $cr = $txByAcc[$aid]['cr'] ?? 0.0;
            $closing = $closingByAcc[$aid] ?? 0.0;

            $hasTxnAcc = ($dr > 0 || $cr > 0);
            if ((string)$nill_transaction === '0' && !$hasTxnAcc) continue;
            if ((string)$nill_balance === '0' && $closing == 0.0) continue;

            $emitRow([
                'entity_type'      => 'acc',
                'entity_name'      => $aname,
                'entity_id'        => $aid,

                'debit'            => $fmt($dr),
                'credit'           => $fmt($cr),

                'balance'          => $fmt(abs($closing)),
                'balance_type'     => $label($closing),

                'debit_total'      => $dr,
                'credit_total'     => $cr,
                'balance_total'    => $closing,

                'op_balance'       => $fmt(abs($opMap[$aid] ?? 0.0)),
                'op_balance_total' => $opMap[$aid] ?? 0.0,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // 6) Accounts directly under the PARENT group (not in subgroups)
    // ─────────────────────────────────────────────────────────────────────
    foreach ($accountsByGroup[(int)$group_id] ?? [] as $acc) {
        $aid   = $acc['id'];
        $aname = $acc['name'];

        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closing = $closingByAcc[$aid] ?? 0.0;

        $hasTxnAcc = ($dr > 0 || $cr > 0);
        if ((string)$nill_transaction === '0' && !$hasTxnAcc) continue;
        if ((string)$nill_balance === '0' && $closing == 0.0) continue;

        $final[] = [
            'entity_type'      => 'acc',
            'entity_name'      => $aname,
            'entity_id'        => $aid,

            'debit'            => $fmt($dr),
            'credit'           => $fmt($cr),

            'balance'          => $fmt(abs($closing)),
            'balance_type'     => $label($closing),

            'debit_total'      => $dr,
            'credit_total'     => $cr,
            'balance_total'    => $closing,

            'op_balance'       => $fmt(abs($opMap[$aid] ?? 0.0)),
            'op_balance_total' => $opMap[$aid] ?? 0.0,
        ];
    }

    return $final;
}



public function load_accounts_trial_balance_parents($acc_grp_parent_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1, $consoview = 0)
{
    // Consolidated view: per-branch rows with branch_name
    // Non-consolidated: current branch ($this->bo_id) only

    $account_master_tbl = 'acctmaster';
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $parentId  = (int)$acc_grp_parent_id;
    $is_consolidated = ((int)$consoview === 1);

    $fmt   = function ($n) { return ($n != 0) ? formatAmount(abs($n)) : ''; };
    $label = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };
    $fmtOp = function ($n) use ($fmt, $label) { return ($n == 0) ? '' : ($fmt($n) . ' ' . $label($n)); };

    // 1) Direct children (strict rule)
    $grpRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->groupStart()
            ->where('u.under_crs_mst_id', $parentId)
            ->orGroupStart()
                ->where('u.under_crs_mst_id', 0)
                ->where('u.crs_mst_parent_id', $parentId)
            ->groupEnd()
        ->groupEnd()
        ->orderBy('g.acc_grp_name', 'asc')
        ->get()->getResultArray();

    $directGroups = [];
    foreach ($grpRows as $g) $directGroups[(int)$g['acc_grp_id']] = $g['acc_grp_name'];
    $directGroupIds = array_keys($directGroups);

    $primaryAccounts = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN(1,14) AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->groupStart()
            ->where('u.under_crs_mst_id', $parentId)
            ->orGroupStart()
                ->where('u.under_crs_mst_id', 0)
                ->where('u.crs_mst_parent_id', $parentId)
            ->groupEnd()
        ->groupEnd()
        ->orderBy('a.acc_name', 'asc')
        ->get()->getResultArray();

    // 2) Full hierarchy for calculations
    $allGroupsUnder = $this->db->table('undercrsmt')
        ->select('crs_mst_id, under_crs_mst_id')
        ->where('cmp_id', $this->company_id)
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('crs_mst_type', 2)
        ->get()->getResultArray();

    $childrenUnder = [];
    foreach ($allGroupsUnder as $row) {
        $pid = (int)$row['under_crs_mst_id'];
        $cid = (int)$row['crs_mst_id'];
        if ($pid > 0) $childrenUnder[$pid][] = $cid;
    }

    $descOf = function (int $root) use (&$childrenUnder): array {
        $out = [$root]; $st = [$root]; $seen = [$root => true];
        while (!empty($st)) {
            $g = array_pop($st);
            if (!empty($childrenUnder[$g])) {
                foreach ($childrenUnder[$g] as $ch) {
                    if (!isset($seen[$ch])) { $seen[$ch] = true; $out[] = $ch; $st[] = $ch; }
                }
            }
        }
        return $out;
    };

    $descendantsByDirect = [];
    foreach ($directGroupIds as $dgid) $descendantsByDirect[$dgid] = $descOf($dgid);

    // 3) Collect accounts for totals
    $allRelevantGroupIds = [];
    foreach ($descendantsByDirect as $arr) $allRelevantGroupIds = array_merge($allRelevantGroupIds, $arr);
    $allRelevantGroupIds = array_values(array_unique($allRelevantGroupIds));
    if (empty($allRelevantGroupIds)) $allRelevantGroupIds = [0];

    $accRows = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN(1,14) AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->whereIn('u.under_crs_mst_id', $allRelevantGroupIds)
        ->get()->getResultArray();

    $accountsByDirectGroup = [];
    foreach ($directGroupIds as $gid) $accountsByDirectGroup[$gid] = [];

    $accIds = [];
    foreach ($accRows as $ar) {
        $aid  = (int)$ar['acc_id'];
        $gUnder = (int)$ar['under_crs_mst_id'];
        foreach ($directGroupIds as $dgid) {
            if (in_array($gUnder, $descendantsByDirect[$dgid], true)) {
                $accountsByDirectGroup[$dgid][] = $aid;
                $accIds[] = $aid;
                break;
            }
        }
    }
    foreach ($primaryAccounts as $pa) $accIds[] = (int)$pa['acc_id'];
    $accIds = array_values(array_unique($accIds));
    if (empty($accIds)) return [];

    // 4) Branch names
    $branch_names = [];
    $branch_rows = $this->univaictly->table('hobomaster')->select('hobo_id, hobo_name')->where('cmp_id', $this->company_id)->get()->getResultArray();
    foreach ($branch_rows as $r) $branch_names[(int)$r['hobo_id']] = $r['hobo_name'];
    if (!isset($branch_names[$this->bo_id])) $branch_names[$this->bo_id] = 'Current Branch';

    $branchLabel = function($bid) use ($branch_names, $is_consolidated) {
        return $branch_names[$bid] ?? ($is_consolidated ? 'All Branches' : 'Current Branch');
    };

    // 5) Opening balances by branch
$opMap = []; // acc_id => [hobo_id => op]

/*
|--------------------------------------------------------------------------
| STEP 5A: Base Opening (same as your code)
|--------------------------------------------------------------------------
*/
$opQB = $this->db->table('accoppybal ob')
    ->select('ob.acc_id, ob.hobo_id, SUM(ob.acc_op_bal) AS op_bal', false)
    ->where('ob.cmp_id', $this->company_id)
    ->where('ob.cmpfymastr_id', $this->fy_id)
    ->whereIn('ob.acc_id', $accIds)
    ->groupBy('ob.acc_id, ob.hobo_id');

if (!$is_consolidated && !empty($this->bo_id)) {
    $opQB->where('ob.hobo_id', $this->bo_id);
}

foreach ($opQB->get()->getResultArray() as $r) {
    $opMap[(int)$r['acc_id']][(int)$r['hobo_id']] = (float)($r['op_bal'] ?? 0.0);
}


/*
|--------------------------------------------------------------------------
| STEP 5B: ADD MOVEMENT BEFORE FROM DATE (CRITICAL FIX)
|--------------------------------------------------------------------------
*/
$fy_start_date = date('Y-m-d', strtotime(validate_fy_from_date('')));
$prev_to_date  = date('Y-m-d', strtotime($from_date . ' -1 day'));
$isFyStart     = (strtotime($from_date) === strtotime($fy_start_date));

if (!$isFyStart) {

    $mvQB = $this->db->table('accttxnmst a')
        ->select("
            a.acc_id,
            v.hobo_id,
            SUM(
                CASE
                    WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt
                    WHEN a.acc_txn_dr_cr = 2 THEN -a.acc_txn_amt
                    ELSE 0
                END
            ) AS net_move
        ", false)

        ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')

        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
        ->where('a.acc_txn_type', 1)

        ->where('a.vch_txn_id >', 0)
        ->where('a.vch_txn_id IS NOT NULL', null, false)

        ->where('v.vch_date >=', $fy_start_date)
        ->where('v.vch_date <=', $prev_to_date)

        ->groupBy('a.acc_id, v.hobo_id');

    if (!$is_consolidated && !empty($this->bo_id)) {
        $mvQB->where('v.hobo_id', $this->bo_id);
    }

    foreach ($mvQB->get()->getResultArray() as $r) {
        $aid = (int)$r['acc_id'];
        $bid = (int)$r['hobo_id'];

        if (!isset($opMap[$aid][$bid])) {
            $opMap[$aid][$bid] = 0;
        }

        $opMap[$aid][$bid] += (float)$r['net_move'];
    }
}
    // 6) Transactions by branch (use vchtxnconso.vch_date)
    $txMap = []; // acc_id => [hobo_id => ['dr'=>..,'cr'=>..]]
    $txQB = $this->db->table('accttxnmst a')
        ->select("a.acc_id, a.hobo_id, SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr, SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr", false)
        ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
        ->where('a.vch_txn_id >', 0)
        ->where('a.vch_txn_id IS NOT NULL', null, false)
        ->where('v.vch_date >=', $from_date)
        ->where('v.vch_date <=', $to_date)
        ->groupBy('a.acc_id, a.hobo_id');
    if (!$is_consolidated && !empty($this->bo_id)) $txQB->where('a.hobo_id', $this->bo_id);
    foreach ($txQB->get()->getResultArray() as $t) {
        $txMap[(int)$t['acc_id']][(int)$t['hobo_id']] = [
            'dr' => (float)($t['dr'] ?? 0.0),
            'cr' => (float)($t['cr'] ?? 0.0),
        ];
    }

    // Helper: branches that have data for an account
    $branchesWithDataForAcc = function($aid) use ($opMap, $txMap, $is_consolidated, $branch_names, $consoview) {
        if (!$is_consolidated) return [$this->bo_id];
        $set = [];
        if (isset($opMap[$aid])) foreach ($opMap[$aid] as $bid => $v) if ($v != 0) $set[$bid] = true;
        if (isset($txMap[$aid])) foreach ($txMap[$aid] as $bid => $arr) if (($arr['dr'] ?? 0) != 0 || ($arr['cr'] ?? 0) != 0) $set[$bid] = true;
        return empty($set) ? [] : array_keys($set);
    };

    $final = [];

    // 7) Direct groups (per branch totals)
    foreach ($directGroupIds as $pgid) {
        $branch_totals = []; // bid => [op,dr,cr]

        foreach ($accountsByDirectGroup[$pgid] as $aid) {
            $bids = $branchesWithDataForAcc($aid);
            if (empty($bids)) continue;
            foreach ($bids as $bid) {
                $op = $opMap[$aid][$bid] ?? 0.0;
                $dr = $txMap[$aid][$bid]['dr'] ?? 0.0;
                $cr = $txMap[$aid][$bid]['cr'] ?? 0.0;
                if ($op == 0 && $dr == 0 && $cr == 0) continue;
                if (!isset($branch_totals[$bid])) $branch_totals[$bid] = ['op' => 0.0, 'dr' => 0.0, 'cr' => 0.0];
                $branch_totals[$bid]['op'] += $op;
                $branch_totals[$bid]['dr'] += $dr;
                $branch_totals[$bid]['cr'] += $cr;
            }
        }

        foreach ($branch_totals as $bid => $totals) {
            $closing = $totals['op'] + $totals['dr'] - $totals['cr'];
            if ((string)$nill_transaction === '0' && ($totals['dr'] == 0 && $totals['cr'] == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing) < 0.0001) continue;

            $final[] = [
                'entity_type'      => 'grp',
                'entity_name'      => $directGroups[$pgid] ?? '',
                'entity_id'        => $pgid,
                'branch_name'      => $branchLabel($bid),

                'debit'            => $fmt($totals['dr']),
                'credit'           => $fmt($totals['cr']),
                'balance'          => $fmt($closing),
                'balance_type'     => $label($closing),

                'debit_total'      => $totals['dr'],
                'credit_total'     => $totals['cr'],
                'balance_total'    => $closing,

                'op_balance'       => $fmtOp($totals['op']),
                'op_balance_total' => $totals['op'],
            ];
        }
    }

    // 8) Direct accounts under the parent
    foreach ($primaryAccounts as $pa) {
        $aid   = (int)$pa['acc_id'];
        $aname = $pa['acc_name'];

        $bids = $branchesWithDataForAcc($aid);
        if (!$is_consolidated && empty($bids)) $bids = [$this->bo_id]; // fallback

        foreach ($bids as $bid) {
            $opBal = $opMap[$aid][$bid] ?? 0.0;
            $dr    = $txMap[$aid][$bid]['dr'] ?? 0.0;
            $cr    = $txMap[$aid][$bid]['cr'] ?? 0.0;
            $closing = $opBal + $dr - $cr;

            if ($opBal == 0 && $dr == 0 && $cr == 0) continue;
            if ((string)$nill_transaction === '0' && ($dr == 0 && $cr == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing) < 0.0001) continue;

            $final[] = [
                'entity_type'      => 'acc',
                'entity_name'      => $aname,
                'entity_id'        => $aid,
                'branch_name'      => $branchLabel($bid),

                'debit'            => $fmt($dr),
                'credit'           => $fmt($cr),
                'balance'          => $fmt($closing),
                'balance_type'     => $label($closing),

                'debit_total'      => $dr,
                'credit_total'     => $cr,
                'balance_total'    => $closing,

                'op_balance'       => $fmtOp($opBal),
                'op_balance_total' => $opBal,
            ];
        }
    }

    // 9) Sort by name then branch
    usort($final, function($a, $b) {
        $c = strcmp($a['entity_name'], $b['entity_name']);
        if ($c !== 0) return $c;
        return strcmp($a['branch_name'], $b['branch_name']);
    });

    return $final;
}

public function load_accounts_trial_balance_parents11dec($acc_grp_parent_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1)
{
    $account_master_tbl = 'acctmaster';

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    // 1) Read ALL groups under the given parent, and build mappings
    $grpRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.crs_mst_is_primary, u.under_main_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.crs_mst_parent_id', $acc_grp_parent_id)
        ->orderBy('g.acc_grp_name', 'asc')
        ->get()->getResultArray();

    $primaryGroups     = []; // [pg_id => pg_name]
    $subgroupToPrimary = []; // [subgrp_id => pg_id]
    $allGroupIds       = [];

    foreach ($grpRows as $g) {
        $gid = (int)$g['acc_grp_id'];
        $allGroupIds[] = $gid;
        if ((int)$g['crs_mst_is_primary'] === 1) {
            $primaryGroups[$gid] = $g['acc_grp_name'];
        } else {
            $subgroupToPrimary[$gid] = (int)$g['under_main_id'];
        }
    }
    if (empty($allGroupIds)) $allGroupIds = [0];

    // 2) Pull ALL relevant accounts once:
    //    - accounts under any group in $allGroupIds
    //    - primary accounts (under_crs_mst_id = 0) directly under this parent
    $accRows = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name, ua.under_crs_mst_id, ua.crs_mst_parent_id')
        ->join("undercrsmt ua", "ua.crs_mst_id = a.acc_id AND ua.crs_mst_type = 1 AND ua.cmp_id = {$this->company_id}", 'left')
        ->where("(ua.under_crs_mst_id IN (" . implode(',', $allGroupIds) . ") OR (ua.under_crs_mst_id = 0 AND ua.crs_mst_parent_id = {$acc_grp_parent_id}))", null, false)
        ->orderBy('a.acc_name', 'asc')
        ->get()->getResultArray();

    $accountsByPG    = []; // [primary_group_id => [acc_id,...]]
    $primaryAccounts = []; // accounts directly under parent
    $accIds          = [];

    foreach ($primaryGroups as $pgid => $_) $accountsByPG[$pgid] = [];

    foreach ($accRows as $ar) {
        $accId  = (int)$ar['acc_id'];
        $accIds[] = $accId;

        $under = (int)$ar['under_crs_mst_id'];
        if ($under === 0) {
            $primaryAccounts[] = ['acc_id' => $accId, 'acc_name' => $ar['acc_name']];
        } else {
            // map subgroup accounts to their primary group
            $pgid = isset($subgroupToPrimary[$under]) ? $subgroupToPrimary[$under] : $under;
            if (!isset($accountsByPG[$pgid])) $accountsByPG[$pgid] = [];
            $accountsByPG[$pgid][] = $accId;
        }
    }

    // 3) Opening balances from accoppybal (for running/closing balance)
    $opMap = []; // acc_id => opening (signed; +DR, -CR)
    if (!empty($accIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            ->whereIn('ob.acc_id', $accIds)
            ->groupBy('ob.acc_id');

        if (!empty($this->bo_id)) {
            $opQB->where('ob.hobo_id', $this->bo_id);
        }

        foreach ($opQB->get()->getResultArray() as $r) {
            $opMap[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }
    }

    // 4) Aggregate transactions ONCE by account (accttxnmst) for the window
    $txByAcc = []; // acc_id => ['dr'=>..,'cr'=>..]
    if (!empty($accIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
        ->where('a.acc_txn_date >=', $from_date)
        ->where('a.acc_txn_date <=', $to_date)
        ->groupBy('a.acc_id');

        if (!empty($this->bo_id)) {
            $txQB->where('a.hobo_id', $this->bo_id);
        }

        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }
    }

    // helpers
    $fmt        = function ($n) { return ($n > 0) ? formatAmount($n) : ''; };
    $labelSide  = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };
    $fmtOp      = function ($n) { if ($n == 0) return ''; return formatAmount(abs($n)) . ' ' . ($n < 0 ? 'CR' : 'DR'); };

    // compute closing per-account (running/closing balance)
    $closingByAcc = []; // acc_id => closing (signed)
    foreach ($accIds as $aid) {
        $op = $opMap[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr); // running closing
    }

    $final = [];

    // 5) PRIMARY GROUP ROWS (entity_type = 'grp') using CLOSING balances
    foreach ($primaryGroups as $pgid => $pgname) {
        $dr_sum = 0.0;
        $cr_sum = 0.0;
        $op_sum = 0.0;
        $closing_sum = 0.0;

        if (!empty($accountsByPG[$pgid])) {
            foreach ($accountsByPG[$pgid] as $accId) {
                $dr_sum     += $txByAcc[$accId]['dr'] ?? 0.0;
                $cr_sum     += $txByAcc[$accId]['cr'] ?? 0.0;
                $op_sum     += $opMap[$accId]         ?? 0.0;
                $closing_sum += $closingByAcc[$accId] ?? 0.0;
            }
        }

        // Hide rules
        $hasTxn = ($dr_sum > 0 || $cr_sum > 0);
        if ((string)$nill_transaction === '0' && !$hasTxn) continue;
        if ((string)$nill_balance === '0' && $closing_sum == 0.0) continue;

        $final[] = [
            'entity_type'      => 'grp',
            'entity_name'      => $pgname,
            'entity_id'        => $pgid,

            // separate DR/CR period totals
            'debit'            => $fmt($dr_sum),
            'credit'           => $fmt($cr_sum),

            // closing balance (opening + DR − CR)
            'balance'          => $fmt(abs($closing_sum)),
            'balance_type'     => $labelSide($closing_sum),

            // numeric fields
            'debit_total'      => $dr_sum,
            'credit_total'     => $cr_sum,
            'balance_total'    => $closing_sum,

            // opening (aggregate)
            'op_balance'       => $fmtOp($op_sum),
            'op_balance_total' => $op_sum,
        ];
    }

    // 6) PRIMARY ACCOUNTS directly under the parent (entity_type = 'acc')
    foreach ($primaryAccounts as $pa) {
        $accId   = (int)$pa['acc_id'];
        $accName = $pa['acc_name'];

        $dr_sum  = $txByAcc[$accId]['dr'] ?? 0.0;
        $cr_sum  = $txByAcc[$accId]['cr'] ?? 0.0;
        $op_val  = $opMap[$accId]         ?? 0.0;
        $closing = $closingByAcc[$accId]  ?? 0.0;

        // Hide rules
        $hasTxn = ($dr_sum > 0 || $cr_sum > 0);
        if ((string)$nill_transaction === '0' && !$hasTxn) continue;
        if ((string)$nill_balance === '0' && $closing == 0.0) continue;

        $final[] = [
            'entity_type'      => 'acc',
            'entity_name'      => $accName,
            'entity_id'        => $accId,

            'debit'            => $fmt($dr_sum),
            'credit'           => $fmt($cr_sum),

            'balance'          => $fmt(abs($closing)),
            'balance_type'     => $labelSide($closing),

            'debit_total'      => $dr_sum,
            'credit_total'     => $cr_sum,
            'balance_total'    => $closing,

            'op_balance'       => $fmtOp($op_val),
            'op_balance_total' => $op_val,
        ];
    }

    return $final;
}



   
  public function getTopPrimaryParent($start_group_id){
    $builder = $this->db->table('accgrpmstn');
    $current_id = $start_group_id;
    while ($current_id > 0) {
        $row = $builder->where('acc_grp_id', $current_id)
                       ->get()
                       ->getRow();
        if (!$row) {
            return null; // Group not found
        }
        if ((int)$row->acc_grp_primary === 1) {
            return (int)$row->acc_grp_parent_id; // Top-most primary parent group found
        }
        // Move up in hierarchy (check both parent ID columns)
        $current_id = ($row->acc_grp_parent_id != 0) ? $row->acc_grp_parent_id : $row->under_acc_grp_id;
    }

    return null; // No primary parent group found
}	

public function load_accounts_trial_balance_parents_10_04_2026($acc_grp_parent_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1, $consoview = 0)
{
    // Consolidated view: per-branch rows with branch_name
    // Non-consolidated: current branch ($this->bo_id) only

    $account_master_tbl = 'acctmaster';
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $parentId  = (int)$acc_grp_parent_id;
    $is_consolidated = ((int)$consoview === 1);

    $fmt   = function ($n) { return ($n != 0) ? formatAmount(abs($n)) : ''; };
    $label = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };
    $fmtOp = function ($n) use ($fmt, $label) { return ($n == 0) ? '' : ($fmt($n) . ' ' . $label($n)); };

    // 1) Direct children (strict rule)
    $grpRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->groupStart()
            ->where('u.under_crs_mst_id', $parentId)
            ->orGroupStart()
                ->where('u.under_crs_mst_id', 0)
                ->where('u.crs_mst_parent_id', $parentId)
            ->groupEnd()
        ->groupEnd()
        ->orderBy('g.acc_grp_name', 'asc')
        ->get()->getResultArray();

    $directGroups = [];
    foreach ($grpRows as $g) $directGroups[(int)$g['acc_grp_id']] = $g['acc_grp_name'];
    $directGroupIds = array_keys($directGroups);

    $primaryAccounts = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN(1,14) AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->groupStart()
            ->where('u.under_crs_mst_id', $parentId)
            ->orGroupStart()
                ->where('u.under_crs_mst_id', 0)
                ->where('u.crs_mst_parent_id', $parentId)
            ->groupEnd()
        ->groupEnd()
        ->orderBy('a.acc_name', 'asc')
        ->get()->getResultArray();

    // 2) Full hierarchy for calculations
    $allGroupsUnder = $this->db->table('undercrsmt')
        ->select('crs_mst_id, under_crs_mst_id')
        ->where('cmp_id', $this->company_id)
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('crs_mst_type', 2)
        ->get()->getResultArray();

    $childrenUnder = [];
    foreach ($allGroupsUnder as $row) {
        $pid = (int)$row['under_crs_mst_id'];
        $cid = (int)$row['crs_mst_id'];
        if ($pid > 0) $childrenUnder[$pid][] = $cid;
    }

    $descOf = function (int $root) use (&$childrenUnder): array {
        $out = [$root]; $st = [$root]; $seen = [$root => true];
        while (!empty($st)) {
            $g = array_pop($st);
            if (!empty($childrenUnder[$g])) {
                foreach ($childrenUnder[$g] as $ch) {
                    if (!isset($seen[$ch])) { $seen[$ch] = true; $out[] = $ch; $st[] = $ch; }
                }
            }
        }
        return $out;
    };

    $descendantsByDirect = [];
    foreach ($directGroupIds as $dgid) $descendantsByDirect[$dgid] = $descOf($dgid);

    // 3) Collect accounts for totals
    $allRelevantGroupIds = [];
    foreach ($descendantsByDirect as $arr) $allRelevantGroupIds = array_merge($allRelevantGroupIds, $arr);
    $allRelevantGroupIds = array_values(array_unique($allRelevantGroupIds));
    if (empty($allRelevantGroupIds)) $allRelevantGroupIds = [0];

    $accRows = $this->db->table($account_master_tbl . ' a')
        ->select('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN(1,14) AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->whereIn('u.under_crs_mst_id', $allRelevantGroupIds)
        ->get()->getResultArray();

    $accountsByDirectGroup = [];
    foreach ($directGroupIds as $gid) $accountsByDirectGroup[$gid] = [];

    $accIds = [];
    foreach ($accRows as $ar) {
        $aid  = (int)$ar['acc_id'];
        $gUnder = (int)$ar['under_crs_mst_id'];
        foreach ($directGroupIds as $dgid) {
            if (in_array($gUnder, $descendantsByDirect[$dgid], true)) {
                $accountsByDirectGroup[$dgid][] = $aid;
                $accIds[] = $aid;
                break;
            }
        }
    }
    foreach ($primaryAccounts as $pa) $accIds[] = (int)$pa['acc_id'];
    $accIds = array_values(array_unique($accIds));
    if (empty($accIds)) return [];

    // 4) Branch names
    $branch_names = [];
    $branch_rows = $this->univaictly->table('hobomaster')->select('hobo_id, hobo_name')->where('cmp_id', $this->company_id)->get()->getResultArray();
    foreach ($branch_rows as $r) $branch_names[(int)$r['hobo_id']] = $r['hobo_name'];
    if (!isset($branch_names[$this->bo_id])) $branch_names[$this->bo_id] = 'Current Branch';

    $branchLabel = function($bid) use ($branch_names, $is_consolidated) {
        return $branch_names[$bid] ?? ($is_consolidated ? 'All Branches' : 'Current Branch');
    };

    // 5) Opening balances by branch
    $opMap = []; // acc_id => [hobo_id => op]
    $opQB = $this->db->table('accoppybal ob')
        ->select('ob.acc_id, ob.hobo_id, SUM(ob.acc_op_bal) AS op_bal', false)
        ->where('ob.cmp_id', $this->company_id)
        ->where('ob.cmpfymastr_id', $this->fy_id)
        ->whereIn('ob.acc_id', $accIds)
        ->groupBy('ob.acc_id, ob.hobo_id');
    if (!$is_consolidated && !empty($this->bo_id)) $opQB->where('ob.hobo_id', $this->bo_id);
    foreach ($opQB->get()->getResultArray() as $r) {
        $opMap[(int)$r['acc_id']][(int)$r['hobo_id']] = (float)($r['op_bal'] ?? 0.0);
    }

    // 6) Transactions by branch (use vchtxnconso.vch_date)
    $txMap = []; // acc_id => [hobo_id => ['dr'=>..,'cr'=>..]]
    $txQB = $this->db->table('accttxnmst a')
        ->select("a.acc_id, a.hobo_id, SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr, SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr", false)
        ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
        ->where('a.vch_txn_id >', 0)
        ->where('a.vch_txn_id IS NOT NULL', null, false)
        ->where('v.vch_date >=', $from_date)
        ->where('v.vch_date <=', $to_date)
        ->groupBy('a.acc_id, a.hobo_id');
    if (!$is_consolidated && !empty($this->bo_id)) $txQB->where('a.hobo_id', $this->bo_id);
    foreach ($txQB->get()->getResultArray() as $t) {
        $txMap[(int)$t['acc_id']][(int)$t['hobo_id']] = [
            'dr' => (float)($t['dr'] ?? 0.0),
            'cr' => (float)($t['cr'] ?? 0.0),
        ];
    }

    // Helper: branches that have data for an account
    $branchesWithDataForAcc = function($aid) use ($opMap, $txMap, $is_consolidated, $branch_names, $consoview) {
        if (!$is_consolidated) return [$this->bo_id];
        $set = [];
        if (isset($opMap[$aid])) foreach ($opMap[$aid] as $bid => $v) if ($v != 0) $set[$bid] = true;
        if (isset($txMap[$aid])) foreach ($txMap[$aid] as $bid => $arr) if (($arr['dr'] ?? 0) != 0 || ($arr['cr'] ?? 0) != 0) $set[$bid] = true;
        return empty($set) ? [] : array_keys($set);
    };

    $final = [];

    // 7) Direct groups (per branch totals)
    foreach ($directGroupIds as $pgid) {
        $branch_totals = []; // bid => [op,dr,cr]

        foreach ($accountsByDirectGroup[$pgid] as $aid) {
            $bids = $branchesWithDataForAcc($aid);
            if (empty($bids)) continue;
            foreach ($bids as $bid) {
                $op = $opMap[$aid][$bid] ?? 0.0;
                $dr = $txMap[$aid][$bid]['dr'] ?? 0.0;
                $cr = $txMap[$aid][$bid]['cr'] ?? 0.0;
                if ($op == 0 && $dr == 0 && $cr == 0) continue;
                if (!isset($branch_totals[$bid])) $branch_totals[$bid] = ['op' => 0.0, 'dr' => 0.0, 'cr' => 0.0];
                $branch_totals[$bid]['op'] += $op;
                $branch_totals[$bid]['dr'] += $dr;
                $branch_totals[$bid]['cr'] += $cr;
            }
        }

        foreach ($branch_totals as $bid => $totals) {
            $closing = $totals['op'] + $totals['dr'] - $totals['cr'];
            if ((string)$nill_transaction === '0' && ($totals['dr'] == 0 && $totals['cr'] == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing) < 0.0001) continue;

            $final[] = [
                'entity_type'      => 'grp',
                'entity_name'      => $directGroups[$pgid] ?? '',
                'entity_id'        => $pgid,
                'branch_name'      => $branchLabel($bid),

                'debit'            => $fmt($totals['dr']),
                'credit'           => $fmt($totals['cr']),
                'balance'          => $fmt($closing),
                'balance_type'     => $label($closing),

                'debit_total'      => $totals['dr'],
                'credit_total'     => $totals['cr'],
                'balance_total'    => $closing,

                'op_balance'       => $fmtOp($totals['op']),
                'op_balance_total' => $totals['op'],
            ];
        }
    }

    // 8) Direct accounts under the parent
    foreach ($primaryAccounts as $pa) {
        $aid   = (int)$pa['acc_id'];
        $aname = $pa['acc_name'];

        $bids = $branchesWithDataForAcc($aid);
        if (!$is_consolidated && empty($bids)) $bids = [$this->bo_id]; // fallback

        foreach ($bids as $bid) {
            $opBal = $opMap[$aid][$bid] ?? 0.0;
            $dr    = $txMap[$aid][$bid]['dr'] ?? 0.0;
            $cr    = $txMap[$aid][$bid]['cr'] ?? 0.0;
            $closing = $opBal + $dr - $cr;

            if ($opBal == 0 && $dr == 0 && $cr == 0) continue;
            if ((string)$nill_transaction === '0' && ($dr == 0 && $cr == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing) < 0.0001) continue;

            $final[] = [
                'entity_type'      => 'acc',
                'entity_name'      => $aname,
                'entity_id'        => $aid,
                'branch_name'      => $branchLabel($bid),

                'debit'            => $fmt($dr),
                'credit'           => $fmt($cr),
                'balance'          => $fmt($closing),
                'balance_type'     => $label($closing),

                'debit_total'      => $dr,
                'credit_total'     => $cr,
                'balance_total'    => $closing,

                'op_balance'       => $fmtOp($opBal),
                'op_balance_total' => $opBal,
            ];
        }
    }

    // 9) Sort by name then branch
    usort($final, function($a, $b) {
        $c = strcmp($a['entity_name'], $b['entity_name']);
        if ($c !== 0) return $c;
        return strcmp($a['branch_name'], $b['branch_name']);
    });

    return $final;
}

public function load_accounts_trial_balance_groups_10_04_2026($group_id, $from_date, $to_date, $nill_balance = 1, $nill_transaction = 1, $consoview = 0)
{
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $gid       = (int) $group_id;
    $is_consolidated = ($consoview == 1);

    $fmt   = function ($n) { return ($n != 0) ? formatAmount(abs($n)) : ''; };
    $label = function ($n) { return $n == 0 ? '' : ($n < 0 ? 'CR' : 'DR'); };

    // 1) Group hierarchy
    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->get()->getResultArray();

    $children = [];
    $grpName = [];
    foreach ($gRows as $r) {
        $pg = (int)($r['under_crs_mst_id'] ?? 0);
        $cg = (int)$r['acc_grp_id'];
        $grpName[$cg] = $r['acc_grp_name'];
        if ($pg > 0) { $children[$pg][] = $cg; }
    }

    $getAllDescendantGids = function(int $rootGid) use (&$children, &$getAllDescendantGids): array {
        $descendants = [];
        if (!empty($children[$rootGid])) {
            foreach ($children[$rootGid] as $childGid) {
                $descendants[] = $childGid;
                $descendants = array_merge($descendants, $getAllDescendantGids($childGid));
            }
        }
        return $descendants;
    };
    $allDesc = $getAllDescendantGids($gid);

    // 2) Accounts under the group (unique)
    $groupIdsForAccounts = array_merge([$gid], $allDesc);
    $accRows = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type IN (1, 14) AND u.cmp_id = {$this->company_id}", 'left')
        ->whereIn('u.under_crs_mst_id', $groupIdsForAccounts)
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->groupBy('a.acc_id, a.acc_name, u.under_crs_mst_id')
        ->get()->getResultArray();

    $accountsByGroup = [];
    $accIds = [];
    foreach ($accRows as $ar) {
        $gForAcc = (int)$ar['under_crs_mst_id'];
        $aid = (int)$ar['acc_id'];
        if (!isset($accountsByGroup[$gForAcc])) { $accountsByGroup[$gForAcc] = []; }
        if (!isset($accountsByGroup[$gForAcc][$aid])) {
            $accountsByGroup[$gForAcc][$aid] = ['id' => $aid, 'name' => $ar['acc_name']];
            $accIds[] = $aid;
        }
    }
    if (empty($accIds)) return [];

    // 3) Branch names
    $branch_names = [];
    $branch_rows = $this->univaictly->table('hobomaster')->select('hobo_id, hobo_name')->where('cmp_id', $this->company_id)->get()->getResultArray();
    foreach ($branch_rows as $r) {
        $branch_names[(int)$r['hobo_id']] = $r['hobo_name'];
    }
    if (!isset($branch_names[$this->bo_id])) {
        $branch_names[$this->bo_id] = 'Current Branch';
    }

    // 4) Opening balances (by branch if consolidated)
    $opMap = [];
    $opQB = $this->db->table('accoppybal ob')
        ->select('ob.acc_id, ob.hobo_id, SUM(ob.acc_op_bal) AS op_bal', false)
        ->where('ob.cmp_id', $this->company_id)
        ->where('ob.cmpfymastr_id', $this->fy_id)
        ->whereIn('ob.acc_id', $accIds)
        ->groupBy('ob.acc_id, ob.hobo_id');
    if (!$is_consolidated) { $opQB->where('ob.hobo_id', $this->bo_id); }
    foreach ($opQB->get()->getResultArray() as $r) {
        $opMap[(int)$r['acc_id']][(int)$r['hobo_id']] = (float)($r['op_bal'] ?? 0.0);
    }

    // 5) Transactions (use vchtxnconso.vch_date)
    $txMap = [];
    $txQB = $this->db->table('accttxnmst a')
        ->select("a.acc_id, a.hobo_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr", false)
        ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $accIds)
        ->where('a.vch_txn_id >', 0)
        ->where('a.vch_txn_id IS NOT NULL', null, false)
        ->where('v.vch_date >=', $from_date)
        ->where('v.vch_date <=', $to_date)
        ->groupBy('a.acc_id, a.hobo_id');

    if (!$is_consolidated) { $txQB->where('a.hobo_id', $this->bo_id); }

    foreach ($txQB->get()->getResultArray() as $t) {
        $txMap[(int)$t['acc_id']][(int)$t['hobo_id']] = [
            'dr' => (float)($t['dr'] ?? 0.0),
            'cr' => (float)($t['cr'] ?? 0.0)
        ];
    }

    // Helper: get branch ids that actually have data for a given acc_id
    $branchesWithDataForAcc = function($aid) use ($opMap, $txMap) {
        $set = [];
        if (isset($opMap[$aid])) { foreach ($opMap[$aid] as $bid => $v) { if ($v != 0) $set[$bid] = true; } }
        if (isset($txMap[$aid])) { foreach ($txMap[$aid] as $bid => $arr) { if (($arr['dr'] ?? 0) != 0 || ($arr['cr'] ?? 0) != 0) $set[$bid] = true; } }
        return array_keys($set);
    };

    $final = [];

    // Process groups
    foreach ($children[$gid] ?? [] as $sgid) {
        $gidsInSubtree = array_merge([$sgid], $getAllDescendantGids($sgid));
        $branch_totals = [];

        foreach ($gidsInSubtree as $gg) {
            foreach ($accountsByGroup[$gg] ?? [] as $acc) {
                $aid = $acc['id'];
                $bids = $is_consolidated ? $branchesWithDataForAcc($aid) : [$this->bo_id];
                foreach ($bids as $branch_id) {
                    $op = $opMap[$aid][$branch_id] ?? 0.0;
                    $dr = $txMap[$aid][$branch_id]['dr'] ?? 0.0;
                    $cr = $txMap[$aid][$branch_id]['cr'] ?? 0.0;
                    if ($op != 0 || $dr != 0 || $cr != 0) {
                        if (!isset($branch_totals[$branch_id])) { $branch_totals[$branch_id] = ['op' => 0.0, 'dr' => 0.0, 'cr' => 0.0]; }
                        $branch_totals[$branch_id]['op'] += $op;
                        $branch_totals[$branch_id]['dr'] += $dr;
                        $branch_totals[$branch_id]['cr'] += $cr;
                    }
                }
            }
        }

        foreach ($branch_totals as $branch_id => $totals) {
            $closing_sum = $totals['op'] + $totals['dr'] - $totals['cr'];
            if ((string)$nill_transaction === '0' && ($totals['dr'] == 0 && $totals['cr'] == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing_sum) < 0.01) continue;

            $final[] = [
                'entity_type' => 'grp', 'entity_name' => $grpName[$sgid] ?? '', 'entity_id' => $sgid,
                'branch_name' => $branch_names[$branch_id] ?? 'Unknown',
                'debit' => $fmt($totals['dr']), 'credit' => $fmt($totals['cr']), 'balance' => $fmt($closing_sum),
                'balance_type' => $label($closing_sum), 'op_balance' => $fmt($totals['op']),
                'op_balance_total' => $totals['op'], 'debit_total' => $totals['dr'], 'credit_total' => $totals['cr'], 'balance_total' => $closing_sum,
            ];
        }
    }

    // Process direct accounts
    foreach ($accountsByGroup[$gid] ?? [] as $acc) {
        $aid = $acc['id'];
        $bids = $is_consolidated ? $branchesWithDataForAcc($aid) : [$this->bo_id];

        foreach ($bids as $branch_id) {
            $opBal = $opMap[$aid][$branch_id] ?? 0.0;
            $dr    = $txMap[$aid][$branch_id]['dr'] ?? 0.0;
            $cr    = $txMap[$aid][$branch_id]['cr'] ?? 0.0;
            $closing = $opBal + $dr - $cr;

            if ($opBal == 0 && $dr == 0 && $cr == 0) continue;
            if ((string)$nill_transaction === '0' && ($dr == 0 && $cr == 0)) continue;
            if ((string)$nill_balance === '0' && abs($closing) < 0.01) continue;

            $final[] = [
                'entity_type' => 'acc', 'entity_name' => $acc['name'], 'entity_id' => $aid,
                'branch_name' => $branch_names[$branch_id] ?? 'Unknown',
                'debit' => $fmt($dr), 'credit' => $fmt($cr), 'balance' => $fmt($closing),
                'balance_type' => $label($closing), 'op_balance' => $fmt($opBal),
                'op_balance_total' => $opBal, 'debit_total' => $dr, 'credit_total' => $cr, 'balance_total' => $closing,
            ];
        }
    }

    return $final;
}
  	
}