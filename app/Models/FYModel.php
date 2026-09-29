<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\Admin\ReportingModel;
use App\Models\Admin\AccountsModel;

class FYModel extends Model	{

  public function __construct() {
		parent::__construct();
		$this->session      = \Config\Services::session();
		$this->comp_id   = $this->session->get('ses_company_id');
		$this->fy_id   = $this->session->get('ses_comp_fy_id');
		$this->uuid  = $this->session->get('comp_uuid');
		$this->externaldb     = new externaldb();
        $this->bo_id         = $this->session->get('ses_boid');	
		$this->ReportingModel = new ReportingModel();	 
		$this->AccountsModel     = new AccountsModel();
		$this->univaictly    =  $this->externaldb->univaictly_db();
		
	}
    
     function recreate_missing_tables($fy_id){
		$ERPtables = new ERPtables($this->comp_id,$fy_id); 
		$response = $ERPtables->memo_txn_tables(0,1);
		$response = $ERPtables->vchaddinfo_tables(0,1);		 
	 }	
	 
	function checkLastFYStatus(){

		return $this->univaictly->table('cmpfymastr')
											->where('cmp_id', $this->comp_id)
											->orderBy('cmpfymastr_id','asc')
											->where('is_imported','0')
											->limit(1)
											->get()->getRowArray();   	      

  }

  public function updateFYStatus($fy_id)
  {
 
		$this->univaictly->table("cmpfymastr")
											 ->where('cmpfymastr_id',$fy_id)
											 ->where('cmp_id',$this->comp_id)
											 ->update(array('is_imported'=>'1'));
  
  }

   public function delete_fy($fy_id)
{
    $errors = [];

    // --- Step 1: Delete opening balances from master tables ---
    $opening_tables = [
        'accoppybal'   => 'cmpfymastr_id',
        'ccoppybaln'   => 'cmpfymastr_id',
        'billoppybal'   => 'cmpfymastr_id',
        'itmoppybal'   => 'cmpfymastr_id',
        'itmoppyval'   => 'cmpfymastr_id',
        'prjoppybal'   => 'cmpfymastr_id',
    ];

    foreach ($opening_tables as $table => $fy_col) {
        try {
            $this->db->table($table)
                     ->where($fy_col, $fy_id)
                     ->delete();
        } catch (\Exception $e) {
            $errors[] = "Error deleting from $table: " . $e->getMessage();
        }
    }

    // --- Step 2: Delete corresponding opening transactions ---
    $txn_mappings = [
        'accttxnmst'   => ['cmpfymastr_id_col' => 'acc_id', 'bal_table' => 'accoppybal', 'bal_id' => 'acc_id'],
        'cctxnmstnn'   => ['cmpfymastr_id_col' => 'cc_id', 'bal_table' => 'ccoppybaln', 'bal_id' => 'cc_id'],
        'billtxnmst'   => ['cmpfymastr_id_col' => 'bill_ref_id', 'bal_table' => 'billoppybal', 'bal_id' => 'bill_ref_id'],
        'itemtxnmst'   => ['cmpfymastr_id_col' => 'itm_id_unit_id', 'bal_table' => 'itmoppybal', 'bal_id' => 'itm_id_unit_id'], // Replace with your item txn table
        'prjtxnmstn'   => ['cmpfymastr_id_col' => 'project_id', 'bal_table' => 'prjoppybal', 'bal_id' => 'project_id'],
    ];

    foreach ($txn_mappings as $txn_table => $map) {
        try {
            $subquery = $this->db->table($map['bal_table'])
                                ->select($map['bal_id'])
                                ->where('cmpfymastr_id', $fy_id);

            $this->db->table($txn_table)
                     ->where('vch_txn_id', 0)   // Only opening balance transactions
                     ->where('txn_id', 0)
                     ->whereIn($map['cmpfymastr_id_col'], $subquery)
                     ->delete();
        } catch (\Exception $e) {
            $errors[] = "Error deleting from $txn_table: " . $e->getMessage();
        }
    }
	
	$this->db->table("undercrsmt")
											 ->where('cmpfymastr_id',$fy_id)
											 ->where('cmp_id',$this->comp_id)
											 ->delete();
											 
    $this->univaictly->table("cmpfymastr")
											 ->where('cmpfymastr_id',$fy_id)
											 ->where('cmp_id',$this->comp_id)
											 ->delete();
    return [
        'status'  => empty($errors) ? true : false,
        'message' => empty($errors) ? "FY $fy_id deleted successfully" : "Some errors occurred during deletion",
        'errors'  => $errors
    ];
}

   // delete it
  public function missing_fy($fy_id)
	{
		$errors = [];
		$ERPtables = new ERPtables($this->comp_id,$fy_id);

		$response = $ERPtables->default_tables(true,false);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $ERPtables->account_master_txn_tables(true,false);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $ERPtables->bill_sundry_master_txn_tables(true,false);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $ERPtables->item_master_txn_tables(true,false);
		foreach ($response as $error) { $errors[] = $error; }

		return $errors;
	}

  function createFY()
  {
	    $percentage = 0;
  	
		$percentage = 30;
		
		$last_FY_status = $this->checkLastFYStatus();
		if($last_FY_status){
			  return [
			'status' => false,
			'fy_id' => 0,
			'percentage' => $percentage,
			'message' => 'Last FY is not closed'
		];	
		}
		$percentage = 50;

		$result=$this->univaictly->table('cmpfymastr')
														->where('is_imported', '1')
														->where('cmp_id', $this->comp_id)
														->orderBy('cmpfymastr_id','DESC')
														->get()->getRowArray();

		$fy_begin     = $result['fy_beg_date'];
	    $fy_end       = $result['fy_end_date'];	
		$new_fy_begin = date('Y-m-d', strtotime("+12 months", strtotime($fy_begin)));
		$new_fy_end   = get_fy_end_date(date('d-m-Y', strtotime($new_fy_begin)));
        $def_val_method   = $result['def_val_method'];
		$fy_data      = [
			'cmp_id'						=> $this->comp_id,
			'fy_beg_date'					=> $new_fy_begin,
		    'fy_end_date'				   => $new_fy_end,
			'def_val_method'               => $def_val_method,
		    'is_imported'	               => '0'
		];		
		$this->univaictly->table('cmpfymastr')->insert($fy_data);
		$fy_id = $this->univaictly->insertID();
		$percentage = 100;
		return [
		'status' => true,
		'fy_id' => $fy_id,
		'percentage' => $percentage
	];
		
  }


	
	function voucher_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_cmpvchtype_','_vchsubtype_'];
		$array2 = ['_vchaddinfo_','_vhtxnconso_','_comptxnmst_','_vhtxntrail_','_long_narrn_','_short_narr_','_acctgstsum_'];
		$array3 = ['_pcklistmst_','_listcumast_','_cupackingn_','_pcklistqty_','_listpacked_','_leveltrack_','_itemlabeln_','_pckglistnn_','_pckqtylist_','_pcklistbeg_','_pcklistext_'];

		$all_tables = array_merge($array1, $array2, $array3);
	    $total_tables = count($all_tables);
		$completed_tables = 0;

	foreach ($array1 as $tbl_name) {
		$response = $this->copy($tbl_name);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}
   
	foreach ($array2 as $tbl_name) {
		$response = $this->copy($tbl_name, false);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}
	

	foreach ($array3 as $tbl_name) {
		$response = $this->copy($tbl_name, false);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}

	$percentage = intval(($completed_tables / $total_tables) * 100);

	return [
		'errors' => $errors,
		'percentage' => $percentage
	];
	}

	function voucher_series_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_vchseriesa_','_vchseriesm_','_cmpvchseri_','_cmpvchtype_','_vchsubtype_'];
		$array2 = ['_vhtxnconso_','_comptxnmst_','_vhtxntrail_','_long_narrn_','_short_narr_'];
		
		$all_tables = array_merge($array1, $array2);
	    $total_tables = count($all_tables);
		$completed_tables = 0;

	foreach ($array1 as $tbl_name) {
		$response = $this->copy($tbl_name);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}
   
	foreach ($array2 as $tbl_name) {
		$response = $this->copy($tbl_name, false);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}
	
	$percentage = intval(($completed_tables / $total_tables) * 100);

	return [
		'errors' => $errors,
		'percentage' => $percentage
	];
	}

	function branch_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_hobomaster_'];
       
	    $total_tables = count($array1);
		$completed_tables = 0;
		
		
		foreach ($array1 as $tbl_name) {
		$response = $this->copy($tbl_name);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	    }
		$percentage = intval(($completed_tables / $total_tables) * 100);

	  return [
		'errors' => $errors,
		'percentage' => $percentage
	  ];
	}

	function currency_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_compcurrcy_'];
		$array2 = ['_forexrates_'];

		$all_tables = array_merge($array1, $array2);
	    $total_tables = count($all_tables);
		$completed_tables = 0;

	foreach ($array1 as $tbl_name) {
		$response = $this->copy($tbl_name);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}
   
	foreach ($array2 as $tbl_name) {
		$response = $this->copy($tbl_name, false);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}
	
	$percentage = intval(($completed_tables / $total_tables) * 100);

	return [
		'errors' => $errors,
		'percentage' => $percentage
	];
	}

	function print_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_prntconfig_','_prntdesign_',];

		
	    $total_tables = count($array1);
		$completed_tables = 0;

	foreach ($array1 as $tbl_name) {
		$response = $this->copy($tbl_name);
		$completed_tables++;
		foreach ($response as $error) {
			$errors[] = $error;
		}
	}   	
	$percentage = intval(($completed_tables / $total_tables) * 100);

	return [
		'errors' => $errors,
		'percentage' => $percentage
	];
	}

    public function add_group($data){
       $account_grp_tbl = "accgrpmstn";	  
	   $exists1 = $this->db->table( $account_grp_tbl)
	              ->where('LOWER(acc_grp_name)', strtolower(trim($data['acc_grp_name'])))
	              ->where('acc_grp_is_active',1)
				  ->where('cmp_id',$this->comp_id)
				  ->get()->getRowArray(); 
	   $exists2 = $this->db->table( $account_grp_tbl)
	              ->where('LOWER(acc_grp_alias)', strtolower(trim($data['acc_grp_alias'])))
	              ->where('acc_grp_is_active',1)
				  ->where('cmp_id',$this->comp_id)
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
	function add_undercrsmt($txn_data){
	  	$this->db->table("undercrsmt")->insert($txn_data);	
    }  
	
	public function account_tables()
{
	$errors = [];

	$latestFYid = $this->latestFYid();
	if (!$latestFYid) {
		return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];
	}

	$newFYid = $this->newFYid();
	if (!$newFYid) {
		return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];
	}

	/*
	|--------------------------------------------------------------------------
	| STEP 0: Ensure undercrsmt mappings exist in NEW FY before any balance copy
	|--------------------------------------------------------------------------
	| copy_accoppybal_with_txn() depends on undercrsmt to decide whether an account
	| belongs to balance sheet (parent_id 1..5). Previously, mappings were copied
	| AFTER balances, so parent_id lookup returned NULL/0 causing incorrect carry-forward.
	*/

	// Copy account GROUP mappings (crs_mst_type = 2) from latest FY -> new FY
	$prevGroups = $this->db->table('undercrsmt')
		->where('cmp_id', $this->comp_id)
		->where('cmpfymastr_id', $latestFYid)
		->where('crs_mst_type', 2)
		->get()
		->getResultArray();

	if (!empty($prevGroups)) {
		foreach ($prevGroups as $row) {
			$exists = $this->db->table('undercrsmt')
				->where('cmp_id', $this->comp_id)
				->where('cmpfymastr_id', $newFYid)
				->where('crs_mst_type', 2)
				->where('crs_mst_id', $row['crs_mst_id'])
				->countAllResults();

			if ($exists == 0) {
				$insert = [
					'cmp_id'             => $this->comp_id,
					'crs_mst_type'       => 2,
					'crs_mst_id'         => $row['crs_mst_id'],
					'under_crs_mst_id'   => $row['under_crs_mst_id'],
					'crs_mst_parent_id'  => $row['crs_mst_parent_id'],
					'under_main_id'      => $row['under_main_id'],
					'crs_mst_is_primary' => $row['crs_mst_is_primary'],
					'cmpfymastr_id'      => $newFYid,
					'crs_is_active'      => $row['crs_is_active'] ?? 1,
				];
				$this->db->table('undercrsmt')->insert($insert);
			}
		}
	}

	// Copy account mappings (crs_mst_type = 1) from latest FY -> new FY
	$prevAccounts = $this->db->table('undercrsmt')
		->where('cmp_id', $this->comp_id)
		->where('cmpfymastr_id', $latestFYid)
		->where('crs_mst_type', 1)
		->get()
		->getResultArray();

	if (!empty($prevAccounts)) {
		foreach ($prevAccounts as $row) {
			$exists = $this->db->table('undercrsmt')
				->where('cmp_id', $this->comp_id)
				->where('cmpfymastr_id', $newFYid)
				->where('crs_mst_type', 1)
				->where('crs_mst_id', $row['crs_mst_id'])
				->countAllResults();

			if ($exists == 0) {
				$insert = [
					'cmp_id'             => $this->comp_id,
					'crs_mst_type'       => 1,
					'crs_mst_id'         => $row['crs_mst_id'],
					'under_crs_mst_id'   => $row['under_crs_mst_id'],
					'crs_mst_parent_id'  => $row['crs_mst_parent_id'],
					'under_main_id'      => $row['under_main_id'],
					'crs_mst_is_primary' => $row['crs_mst_is_primary'],
					'cmpfymastr_id'      => $newFYid,
					'crs_is_active'      => $row['crs_is_active'] ?? 1,
				];
				$this->db->table('undercrsmt')->insert($insert);
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| STEP 1: Copy opening balances for Balance Sheet accounts only
	|--------------------------------------------------------------------------
	*/
	$percentage  = 0;
	$total_steps = 2; // (1) copy openings (2) create/update P&L appropriation
	$completed   = 0;

	$copyResponse = $this->copy_accoppybal_with_txn('accoppybal', $latestFYid, $newFYid);
	if (!empty($copyResponse['errors'])) {
		$errors = array_merge($errors, $copyResponse['errors']);
	}
	$completed++;
	$percentage = intval(($completed / $total_steps) * 100);

	/*
	|--------------------------------------------------------------------------
	| STEP 2: Ensure "Reserves & Surplus" group exists (and mapped in both FYs)
	|--------------------------------------------------------------------------
	*/
	$group_name = "Reserves & Surplus";
	$group = $this->db->table('accgrpmstn')
		->where('cmp_id', $this->comp_id)
		->where('LOWER(acc_grp_name)', strtolower($group_name))
		->get()
		->getRow();

	if ($group) {
		$group_id = (int)$group->acc_grp_id;
	} else {
		$insert_data = [
			'cmp_id'            => $this->comp_id,
			'acc_grp_name'      => clean($group_name),
			'acc_grp_alias'     => clean($group_name),
			'acc_grp_is_active' => 1
		];
		$this->db->table('accgrpmstn')->insert($insert_data);
		$group_id = (int)$this->db->insertID();

		// Also insert into undercrsmt for BOTH FYs (latest + new)
		$crs_mst_is_primary = 1;
		$under_acc_grp_id   = 0;
		$under_main_grp_id  = 1;
		$acc_grp_parent_id  = 1;

		$mst_insert_data = [
			'cmp_id'             => $this->comp_id,
			'crs_mst_type'       => 2,
			'crs_mst_id'         => $group_id,
			'under_crs_mst_id'   => $under_acc_grp_id,
			'crs_mst_parent_id'  => $acc_grp_parent_id,
			'under_main_id'      => $under_main_grp_id,
			'cmpfymastr_id'      => $latestFYid,
			'crs_mst_is_primary' => $crs_mst_is_primary,
			'crs_is_active'      => 1
		];
		$this->db->table('undercrsmt')->insert($mst_insert_data);

		$mst_newinsert_data = $mst_insert_data;
		$mst_newinsert_data['cmpfymastr_id'] = $newFYid;
		$this->db->table('undercrsmt')->insert($mst_newinsert_data);
	}

	/*
	|--------------------------------------------------------------------------
	| STEP 3: Ensure "Profit & Loss Appropriation" account exists and mapped
	|--------------------------------------------------------------------------
	*/
	$crs_mst_is_primary = 0;

	$under_main_grp_id = 0;
	$under_acc_grp_id  = 0;
	$acc_grp_parent_id = 0;

	$main_group_info = $this->main_group_info($group_id);
	if ($main_group_info) {
		$acc_grp_parent_id = (int)$main_group_info['crs_mst_parent_id'];
		$under_acc_grp_id  = (int)$main_group_info['acc_grp_id'];

		if ((int)$main_group_info['crs_mst_is_primary'] === 1) {
			$under_main_grp_id = (int)$main_group_info['acc_grp_id'];
		} else {
			$under_main_grp_id = (int)$main_group_info['under_main_id'];
		}
	} else {
		// safe defaults (same behavior as your existing logic)
		$acc_grp_parent_id = 1;
		$under_acc_grp_id  = 0;
		$under_main_grp_id = 1;
	}

	$plAccountName = 'Profit & Loss Appropriation';
	$plAcc = $this->db->table('acctmaster')
		->where('acc_name', $plAccountName)
		->where('cmp_id', $this->comp_id)
		->get()
		->getRow();

	if (!$plAcc) {
		$plData = [
			'cmp_id'         => $this->comp_id,
			'acc_name'       => $plAccountName,
			'acc_alias'      => $plAccountName,
			'acc_print_name' => $plAccountName,
			'acc_is_active'  => 1
		];
		$this->db->table('acctmaster')->insert($plData);
		$plAccId = (int)$this->db->insertID();

		$pldetailData = [
			'cmp_id'         => $this->comp_id,
			'acc_id'         => $plAccId,
			'acc_is_sys_acc' => 1,
			'acc_is_sez'     => 0
		];
		$this->db->table('acctmstdet')->insert($pldetailData);
	} else {
		$plAccId = (int)$plAcc->acc_id;
	}

	// Ensure mapping in undercrsmt for BOTH FYs
	foreach ([$latestFYid, $newFYid] as $fyToMap) {
		$count = $this->db->table('undercrsmt')
			->where('cmp_id', $this->comp_id)
			->where('cmpfymastr_id', $fyToMap)
			->where('crs_mst_type', 1)
			->where('crs_mst_id', $plAccId)
			->countAllResults();

		if ($count == 0) {
			$mst_insert_data = [
				'cmp_id'             => $this->comp_id,
				'crs_mst_type'       => 1,
				'crs_mst_id'         => $plAccId,
				'under_crs_mst_id'   => $under_acc_grp_id,
				'crs_mst_parent_id'  => $acc_grp_parent_id,
				'under_main_id'      => $under_main_grp_id,
				'cmpfymastr_id'      => $fyToMap,
				'crs_mst_is_primary' => $crs_mst_is_primary,
				'crs_is_active'      => 1
			];
			$this->db->table("undercrsmt")->insert($mst_insert_data);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| STEP 4: Compute Net Profit/Loss for CURRENT FY and carry to NEW FY
	|--------------------------------------------------------------------------
	| IMPORTANT FIX: If both profit & loss totals exist, net = profit - loss.
	*/
	$from_date = date('Y-m-d', strtotime($this->session->get('ses_company_fy_beginning')));
	$to_date   = date('Y-m-d', strtotime($this->session->get('ses_company_fy_end')));

	$response = $this->ReportingModel->load_profit_loss_horizontal(0, $from_date, $to_date, 0, 0);
	$lastRow  = is_array($response) ? end($response) : [];

	$profitValue = isset($lastRow['l_balance_total']) ? (float)$lastRow['l_balance_total'] : 0.0;
	$lossValue   = isset($lastRow['r_balance_total']) ? (float)$lastRow['r_balance_total'] : 0.0;

	// ✅ FIX: net profit/loss
	$profitLossValue = $profitValue - $lossValue; // + = profit, - = loss

	// Upsert opening in new FY for P&L Appropriation
	$opening_exists = $this->db->table('accoppybal')->where([
		'cmp_id'        => $this->comp_id,
		'cmpfymastr_id' => $newFYid,
		'acc_id'        => $plAccId,
		'hobo_id'       => $this->bo_id
	])->get()->getRow();

SaveErrorLog("Profit loss id: -> ".$plAccId);
	if ($opening_exists) {
		$this->db->table('accoppybal')->where([
			'cmp_id'        => $this->comp_id,
			'cmpfymastr_id' => $newFYid,
			'acc_id'        => $plAccId,
			'hobo_id'       => $this->bo_id
		])->update([
			'acc_op_bal'   => $profitLossValue,
			'acc_py_bal'   => 0,
			'hobo_id'      => $this->bo_id ?? 0,
			'acc_memo_bal' => 0
		]);
	} else {
		$this->db->table('accoppybal')->insert([
			'cmp_id'        => $this->comp_id,
			'cmpfymastr_id' => $newFYid,
			'acc_id'        => $plAccId,
			'acc_op_bal'    => $profitLossValue,
			'acc_py_bal'    => 0,
			'hobo_id'       => $this->bo_id ?? 0,
			'acc_memo_bal'  => 0
		]);
	}

	// Optional: also write an opening txn row (as in your code)
	$fy_from_date = $this->validate_fy_from_date($newFYid);
	$this->db->table('accttxnmst')->insert([
		'cmp_id'        => $this->comp_id,
		'acc_id'        => $plAccId,
		'acc_txn_date'  => $fy_from_date,
		'acc_txn_dr_cr' => ($profitLossValue >= 0) ? 1 : 2,
		'acc_txn_amt'   => abs($profitLossValue),
		'acc_txn_fcy'   => 0,
		'vch_txn_id'    => 0,
		'txn_id'        => 0,
		'hobo_id'       => $this->bo_id ?? 0,
		'acc_txn_type'  => 1
	]);

	$completed++;
	$percentage = intval(($completed / $total_steps) * 100);

	return [
		'status'     => true,
		'message'    => 'Migration completed',
		'errors'     => $errors,
		'percentage' => $percentage
	];
}
	public function account_tables_bck(){
		$errors = [];
		$latestFYid = $this->latestFYid();
		if (!$latestFYid)
			return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if (!$newFYid)
			return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];

		$all_tables  = ['accoppybal']; // add more if needed
		$total_steps = count($all_tables);
		$completed   = 0;
		foreach ($all_tables as $table) {
			$copyResponse = $this->copy_accoppybal_with_txn($table, $latestFYid, $newFYid);
			if (!empty($copyResponse['errors'])) {
				$errors = array_merge($errors, $copyResponse['errors']);
			}
			$completed++;
		}		
		
	
    //try {
		// ðŸ”¹ Step 1: Ensure "Reserves & Surplus" group exists
        $group_name = "Reserves & Surplus";
        $group = $this->db->table('accgrpmstn')
            ->where('cmp_id', $this->comp_id)
            ->where('LOWER(acc_grp_name)', strtolower($group_name))
            ->get()
            ->getRow();
        if ($group) {
            $group_id = $group->acc_grp_id;
        } 
		else {
            // Create group if not found
            $group_name_alias = $group_name;
            $insert_data = [
                'cmp_id'           => $this->comp_id,
                'acc_grp_name'     => clean($group_name),
                'acc_grp_alias'    => clean($group_name_alias),
                'acc_grp_is_active'=> 1
            ];
			$this->db->table('accgrpmstn')->insert($insert_data);
			$group_id = $this->db->insertID();
           

            // Also insert into undercrsmt
            $crs_mst_is_primary = 1;
            $under_acc_grp_id   = 0;
            $under_main_grp_id  = 1;
            $acc_grp_parent_id  = 1;
			// Create In current FY
			$mst_insert_data = [
                'cmp_id'              => $this->comp_id,
                'crs_mst_type'        => 2,
                'crs_mst_id'          => $group_id,
                'under_crs_mst_id'    => $under_acc_grp_id,
                'crs_mst_parent_id'   => $acc_grp_parent_id,
                'under_main_id'       => $under_main_grp_id,
                'cmpfymastr_id'       => $latestFYid,
                'crs_mst_is_primary'  => $crs_mst_is_primary
            ];
			$this->db->table("undercrsmt")->insert($mst_insert_data);	
         
			
			// Create In newly FY
            $mst_newinsert_data = [
                'cmp_id'              => $this->comp_id,
                'crs_mst_type'        => 2,
                'crs_mst_id'          => $group_id,
                'under_crs_mst_id'    => $under_acc_grp_id,
                'crs_mst_parent_id'   => $acc_grp_parent_id,
                'under_main_id'       => $under_main_grp_id,
                'cmpfymastr_id'       => $newFYid,
                'crs_mst_is_primary'  => $crs_mst_is_primary
            ];
			$this->db->table("undercrsmt")->insert($mst_newinsert_data);
         }	
		
		 // âœ… Step 2: Create P&L Appropriation Account if not exists		
		$crs_mst_is_primary=0;
		$main_group_info     = $this->main_group_info($group_id);		
		if($main_group_info){
				$acc_grp_parent_id = $main_group_info['crs_mst_parent_id'];
				$under_acc_grp_id  = $main_group_info['acc_grp_id'];

				if($main_group_info['crs_mst_is_primary']==1)
						$under_main_grp_id= $main_group_info['acc_grp_id'];
				else
						$under_main_grp_id= $main_group_info['under_main_id'];
		}
        $plAccountName = 'Profit & Loss Appropriation';
        $plAcc = $this->db->table('acctmaster')
            ->where('acc_name', $plAccountName)
            ->where('cmp_id', $this->comp_id)
            ->get()
            ->getRow();
        if (!$plAcc) {
            // create new account
            $plData = [
                'cmp_id'          => $this->comp_id,
                'acc_name'        => $plAccountName,
                'acc_alias'       => $plAccountName,
				'acc_print_name'  => $plAccountName,
                'acc_is_active'   => 1
            ];
            $this->db->table('acctmaster')->insert($plData);
            $plAccId = $this->db->insertID();
			
			// insert into account details
			$pldetailData = [
                'cmp_id'          => $this->comp_id,
                'acc_id'          => $plAccId,
                'acc_is_sys_acc'  => 1,
                'acc_is_sez'      => 0				
            ];
            $this->db->table('acctmstdet')->insert($pldetailData);
			
			
        } else {
            $plAccId = $plAcc->acc_id;
        }
		// Mapping in current FY
		 $count = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $latestFYid)
					->where('crs_mst_type', 1)
					->where('crs_mst_id', $plAccId)
					->countAllResults();
		if($count==0){			
		$mst_insert_data  = array("cmp_id"=>$this->comp_id,"crs_mst_type"=>1,"crs_mst_id"=>$plAccId,"under_crs_mst_id"=>$under_acc_grp_id,
										  "crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
										  "cmpfymastr_id"=>$latestFYid,"crs_mst_is_primary"=>$crs_mst_is_primary);
	
		$this->db->table("undercrsmt")->insert($mst_insert_data);
		}
		// Mapping in newly FY
		$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 1)
					->where('crs_mst_id', $plAccId)
					->countAllResults();
		if($counts==0){	
		$mst_insert_data  = array("cmp_id"=>$this->comp_id,"crs_mst_type"=>1,"crs_mst_id"=>$plAccId,"under_crs_mst_id"=>$under_acc_grp_id,
										  "crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>$under_main_grp_id,
										  "cmpfymastr_id"=>$newFYid,"crs_mst_is_primary"=>$crs_mst_is_primary);
		
		$this->db->table("undercrsmt")->insert($mst_insert_data);
		}
        // âœ… Step 3: Calculate current FY profit/loss
  
        $from_date = date('Y-m-d',strtotime($this->session->get('ses_company_fy_beginning')));
        $to_date   = date('Y-m-d',strtotime($this->session->get('ses_company_fy_end')));
		$response = $this->ReportingModel->load_profit_loss_horizontal(0,$from_date,$to_date,0,0);
		$lastRow   = end($response);
		$profitLossValue = 0;
		if (!empty($lastRow)) {
			$profitValue = isset($lastRow['l_balance_total']) ? floatval($lastRow['l_balance_total']) : 0;
			$lossValue   = isset($lastRow['r_balance_total']) ? floatval($lastRow['r_balance_total']) : 0;

			$profitLossValue = $profitValue - $lossValue;
			// Positive = net profit (credit), Negative = net loss (debit)

			/* if ($profitValue > 0 && $lossValue == 0) {
				// Net Profit
				$profitLossValue = $profitValue; // positive
			} elseif ($lossValue > 0 && $profitValue == 0) {
				// Net Loss
				$profitLossValue = -$lossValue; // negative
			} else {
				$profitLossValue = 0; // no profit/loss
			} */
		}
		
		$opening_exists = $this->db->table('accoppybal')->where([
					'cmp_id'        => $this->comp_id,
					'cmpfymastr_id' => $newFYid,
					'acc_id'        => $plAccId,
					'hobo_id'       => $this->bo_id
				])->get()->getRow();
        
		if ($opening_exists) {
			// âœ… Update existing record
			$this->db->table('accoppybal')->where([
				'cmp_id'        => $this->comp_id,
				'cmpfymastr_id' => $newFYid,
				'acc_id'        => $plAccId,
				'hobo_id'       => $this->bo_id
			])->update([
				'acc_op_bal'   => $profitLossValue,
				'acc_py_bal'   => 0,
				'hobo_id'      => $this->bo_id,
				'acc_memo_bal' => 0
			]);
		} else {
			// âœ… Step 4: Insert into accoppybal for new FY
				$opening_balance_data = [
					'cmp_id'         => $this->comp_id,
					'cmpfymastr_id'  => $newFYid,
					'acc_id'         => $plAccId,
					'acc_op_bal'     => $profitLossValue,
					'acc_py_bal'     => 0,
					'hobo_id'        => $this->bo_id ?? 0,
					'acc_memo_bal'   => 0
				];
				$this->db->table('accoppybal')->insert($opening_balance_data);
		}
        // âœ… Step 5: Insert corresponding opening txn
        $fy_from_date = $this->validate_fy_from_date($newFYid);
        $txnData = [
            'cmp_id'        => $this->comp_id,
            'acc_id'        => $plAccId,
            'acc_txn_date'  => $fy_from_date,
            'acc_txn_dr_cr' => ($profitLossValue >= 0) ? 1 : 2, 
            'acc_txn_amt'   => abs($profitLossValue),
            'acc_txn_fcy'   => 0,
            'vch_txn_id'    => 0,
            'txn_id'        => 0,
            'hobo_id'       => $this->bo_id ?? 0,
            'acc_txn_type'  => 1
        ];
        $this->db->table('accttxnmst')->insert($txnData);
		/* } catch (\Throwable $e) {
			$errors[] = "Error creating P&L Appropriation Account: " . $e->getMessage();
		} */
		
		// copy all undercrsmt table data of account groups from last fy to new fy 
		// here we will change new fy id rest will be same
		$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 2)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 2)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>2,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		} 

        $prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 1)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 1)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>1,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		} 		
		   
	
		
		$completed++;	
		$percentage = intval(($completed / $total_steps) * 100);
		
		
		
		
		
		return [
			'status'     => true,
			'message'    => 'Migration completed',
			'errors'     => $errors,
			'percentage' => $percentage
		 ];
	}
	
	public function material_center_tables(){
		$errors = [];
		$latestFYid = $this->latestFYid();
		if (!$latestFYid)
			return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if (!$newFYid)
			return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];

		$completed   = 0;
		$total_steps =1;
		$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 7)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 7)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>7,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);	
                   $completed++;					
				}				
								
			}
		} 

        $prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 8)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 8)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>8,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);
					$completed++;						
				}				
								
			}
		} 		
		   
	
		
		
		$percentage = intval(($completed / $total_steps) * 100);
		
		
		return [
			'status'     => true,
			'message'    => 'Migration completed',
			'errors'     => $errors,
			'percentage' => $percentage
		 ];
	}
	
	public function bill_sundry_tables(){
		$errors = [];
		$latestFYid = $this->latestFYid();
		if (!$latestFYid)
			return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if (!$newFYid)
			return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];

		 $completed=0;
		 $total_steps =1;
		$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 14)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 14)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>14,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);
						$completed++;						
				}				
								
			}
		} 
		
		
		$percentage = intval(($completed / $total_steps) * 100);
		return [
			'status'     => true,
			'message'    => 'Migration completed',
			'errors'     => $errors,
			'percentage' => $percentage
		 ];
	}
	
	function main_group_info($acc_grp_id){
     $builder = $this->db->table('accgrpmstn acgrpmst');
	 $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->comp_id", 'left');
     $builder->where('acgrpmst.cmp_id',$this->comp_id);
	 $builder->where('undercrsmt.crs_mst_id', $acc_grp_id);
	 return $builder->get()->getRowArray();   	   
   }
	
	private function validate_fy_from_date($fy_id){
    $row = $this->univaictly->table('cmpfymastr')->select('fy_beg_date')->where('cmpfymastr_id', $fy_id)->get()->getRowArray();
    return $row ? $row['fy_beg_date'] : date('Y-m-d');
}
	private function copy_accoppybal_with_txn($table, $latestFYid, $newFYid){
    $errors = [];
    try {
        $builder = $this->db->table($table);
        $records = $builder->where('hobo_id', $this->bo_id)->where('cmpfymastr_id', $latestFYid)->get()->getResultArray();

        if (empty($records)) {
            return ['errors' => ["No records found in {$table} for FY {$latestFYid}"]];
        }
        $fy_from_date = $this->validate_fy_from_date($newFYid); // implement if not
        $acc_txn_date = date('Y-m-d', strtotime($fy_from_date));

        foreach ($records as $row) {
            // insert new opening balance
			$companyid = $row['cmp_id'];
			// balance of balance sheet accounts are transfer , profit and loss parent accounts balance will nt transfger 
			$builderac = $this->db->table('acctmaster A');
			$builderac->join("undercrsmt", "undercrsmt.crs_mst_id = A.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmpfymastr_id = $newFYid AND undercrsmt.cmp_id =$companyid", 'left');
			$builderac->where('A.cmp_id', $row['cmp_id']);
			$builderac->where('undercrsmt.crs_mst_id',$row['acc_id']);
		    $ac_records = $builderac->get()->getRowArray();
			$parent_id  = $ac_records['crs_mst_parent_id'];				
			if($ac_records['under_crs_mst_id'] != 0){
				    $undercrsmt_tbl = "undercrsmt";
					$builderaw = $this->db->table("accgrpmstn");
					// Select fields
					$builderaw->select("
						accgrpmstn.*,
						$undercrsmt_tbl.under_crs_mst_id,
						$undercrsmt_tbl.crs_mst_parent_id,
						$undercrsmt_tbl.crs_mst_is_primary,
						$undercrsmt_tbl.crs_is_active,
						$undercrsmt_tbl.cmpfymastr_id
					");					
					$builderaw->join(
						$undercrsmt_tbl,
						"$undercrsmt_tbl.crs_mst_id = accgrpmstn.acc_grp_id 
						AND 
						$undercrsmt_tbl.crs_mst_type = 2
						AND 
						$undercrsmt_tbl.cmpfymastr_id = $newFYid
						",
						'left'
					);	
                    $builderaw->where("$undercrsmt_tbl.crs_mst_id",$ac_records['under_crs_mst_id']);					
					$builderaw->where("$undercrsmt_tbl.cmpfymastr_id IS NOT NULL");
					$builderaw->where("$undercrsmt_tbl.cmp_id",$row['cmp_id']);
					$group_result  = $builderaw->get()->getRowArray();
					if($group_result){
						$parent_id = $group_result['crs_mst_parent_id'];
					} 
				
				}
				
				
			
            $newRow = $row;
            unset($newRow['acc_oppy_bal_id']); // remove PK if auto-increment
            $newRow['cmpfymastr_id'] = $newFYid;
			if(in_array($parent_id,[1,2,3,4,5])){
				$this->db->table('accoppybal')->insert($newRow);
				$newId = $this->db->insertID();

				
		    }
        }
    } catch (\Throwable $e) {
        $errors[] = "Error copying {$table}: " . $e->getMessage();
    }

    return ['errors' => $errors];
   }

	
	
	public function item_tables(){
		$errors = [];
		$latestFYid = $this->latestFYid();
		if (!$latestFYid)
			return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if (!$newFYid)
			return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];

		$all_tables  = ['itmoppybal','itmoppyval']; // add more if needed
		$total_steps = count($all_tables);
		$completed   = 0;
		foreach ($all_tables as $table) {
			$copyResponse = $this->copy_itemoppybal_with_txn($table, $latestFYid, $newFYid);
			if (!empty($copyResponse['errors'])) {
				$errors = array_merge($errors, $copyResponse['errors']);
			}
			$completed++;
		}
		
		$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 3)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 3)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>3,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		}
	$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 4)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 4)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>4,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		}
		
	$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 6)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 6)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>6,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		}	
		
		$percentage = intval(($completed / $total_steps) * 100);
		return [
			'status'     => true,
			'message'    => 'Migration completed',
			'errors'     => $errors,
			'percentage' => $percentage
		];
	}
	
	public function copy_itemoppybal_with_txn($table, $latestFYid, $newFYid){
    $errors = [];

    // Fetch records from latest FY
    $records = $this->db->table($table)
        ->where('cmpfymastr_id', $latestFYid)
		->where('hobo_id', $this->bo_id)
        ->get()
        ->getResultArray();

    if (empty($records)) {
        return ['errors' => [], 'message' => "No records found in {$table}"];
    }

    foreach ($records as $row) {

        // Common fields
        $data = [
            'cmp_id'        => $row['cmp_id'],
            'cmpfymastr_id' => $newFYid,
            'itm_id_unit_id'=> $row['itm_id_unit_id'],
            'mat_cent_id'   => $row['mat_cent_id'] ?? null,
            'hobo_id'       => $row['hobo_id'] ?? null
        ];

        // Specific fields per table
        if ($table == 'itmoppybal') {
            $data['itm_op_bal_qty'] = $row['itm_op_bal_qty'] ?? 0;
            $data['itm_py_bal_qty'] = $row['itm_py_bal_qty'] ?? 0;
        } elseif ($table == 'itmoppyval') {
            $data['itm_op_val_amt']  = $row['itm_op_val_amt'] ?? 0;
            $data['itm_py_val_amt']  = $row['itm_py_val_amt'] ?? 0;
            $data['itm_val_method_id']= $row['itm_val_method_id'];
        }

        try {
            $this->db->table($table)->insert($data);
        } catch (\Exception $e) {
            $errors[] = "Error in {$table} for itm_id_unit_id {$row['itm_id_unit_id']}: " . $e->getMessage();
        }
    }

    return ['errors' => $errors];
}

	function unit_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_itmunitmst_'];
		$total_tables = count($array1);
		$completed_tables = 0;
		foreach ($array1 as $tbl_name) {
			$response = $this->copy($tbl_name);
			$completed_tables++;
			foreach ($response as $error) {
				$errors[] = $error;
			}
		}
		
		$percentage = intval(($completed_tables / $total_tables) * 100);
		return [
			'errors' => $errors,
			'percentage' => $percentage
		];
	}

	function mc_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_mcgrpmstnn_','_mcmasternn_'];
		$total_tables = count($array1);		 
		$completed_tables = 0;
		
		foreach ($array1 as $tbl_name) {
		 $response = $this->copy($tbl_name);
		 $completed_tables++;
		  foreach ($response as $error) {
			$errors[] = $error;
		  }
	   }
	$percentage = intval(($completed_tables / $total_tables) * 100);
	return [
		'errors' => $errors,
		'percentage' => $percentage
	];
	}

	

	public function cost_center_tables()
{
    $errors = [];

    $latestFYid = $this->latestFYid();
    if (!$latestFYid)
        return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];

    $newFYid = $this->newFYid();
    if (!$newFYid)
        return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];

    $all_tables = ['ccoppybaln'];
    $total_steps = count($all_tables);
    $completed = 0;

    foreach ($all_tables as $table) {
        $copyResponse = $this->copy_ccoppybal_with_txn($table, $latestFYid, $newFYid);
        if (!empty($copyResponse['errors'])) {
            $errors = array_merge($errors, $copyResponse['errors']);
        }
        $completed++;
    }
	
	$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 9)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 9)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>9,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		}
	$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 10)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 10)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>10,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		}	
		
    $percentage = intval(($completed / $total_steps) * 100);

    return [
        'status' => true,
        'message' => 'Cost center migration completed',
        'errors' => $errors,
        'percentage' => $percentage
    ];
}
private function copy_ccoppybal_with_txn($table, $latestFYid, $newFYid)
{
    $errors = [];

    try {
        $builder = $this->db->table($table);
        $records = $builder->where('hobo_id', $this->bo_id)->where('cmpfymastr_id', $latestFYid)->get()->getResultArray();

        if (empty($records)) {
            return ['errors' => ["No records found in {$table} for FY {$latestFYid}"]];
        }

        $fy_from_date = $this->validate_fy_from_date($newFYid); 
        $cc_txn_date = date('Y-m-d', strtotime($fy_from_date));

        foreach ($records as $row) {

            // Insert new opening balance
            $newRow = $row;
            unset($newRow['cc_oppy_bal_id']); // remove PK if auto-increment
            $newRow['cmpfymastr_id'] = $newFYid;

            $this->db->table('ccoppybaln')->insert($newRow);
            $newId = $this->db->insertID();

            
        }
    } catch (\Throwable $e) {
        $errors[] = "Error copying {$table}: " . $e->getMessage();
    }

    return ['errors' => $errors];
}

	public function bill_by_bill_tables(){
    $errors     = [];
    $latestFYid = $this->latestFYid();
    if (!$latestFYid)
        return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];

    $newFYid   = $this->newFYid();
    if (!$newFYid)
        return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];

    $all_tables  = ['billoppybal'];
    $total_steps = count($all_tables);
    $completed   = 0;
    foreach ($all_tables as $table) {
        $copyResponse = $this->copy_biloppybal_with_txn($latestFYid, $newFYid);
        if (!empty($copyResponse['errors'])) {
            $errors = array_merge($errors, $copyResponse['errors']);
        }
        $completed++;
    }

    $percentage = intval(($completed / $total_steps) * 100);

    return [
        'status' => true,
        'message' => 'Bill-by-Bill migration completed',
        'errors' => $errors,
        'percentage' => $percentage
    ];
}

public function copy_biloppybal_with_txn($latestFYid, $newFYid)
{
    $errors = [];

    // Step 1: Fetch all records from latest FY
    $records = $this->db->table('billoppybal')
        ->where('cmpfymastr_id', $latestFYid)
		 ->where('hobo_id', $this->bo_id)
        ->get()
        ->getResultArray();

    if (empty($records)) {
        return ['status' => 'NO_RECORDS', 'message' => 'No records found to copy'];
    }

    foreach ($records as $row) {
        try {
            // Step 2: Copy biloppybal to new FY
            $insertBal = [
                'cmp_id'        => $row['cmp_id'],
                'cmpfymastr_id' => $newFYid,
                'bill_ref_id'   => $row['bill_ref_id'],
                'bill_op_bal'   => $row['bill_op_bal'],
                'bill_py_bal'   => $row['bill_py_bal'],
                'hobo_id'       => $row['hobo_id'],
            ];
            $this->db->table('billoppybal')->insert($insertBal);

            // Step 3: Fetch acc_id from billmaster
            $acc_id = $this->db->table('billmaster')
                ->select('acc_id')
                ->where('bill_ref_id', $row['bill_ref_id'])
                ->get()
                ->getRow('acc_id') ?? 0;

            

        } catch (\Exception $e) {
            $errors[] = "Bill Ref ID {$row['bill_ref_id']}: " . $e->getMessage();
        }
    }

    return ['status' => 'OK', 'errors' => $errors];
}

	function bill_of_material_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];
 
		$array1 = ['_bomgrpmstn_','_billofmatn_','_bominputnn_','_bomoutputn_','_bomaddcost_'];
		
	    $total_tables = count($array1);
		$completed_tables = 0;

		foreach ($array1 as $tbl_name) {
			$response = $this->copy($tbl_name);
			$completed_tables++;
			foreach ($response as $error) {
				$errors[] = $error;
			}
		}
		
		$percentage = intval(($completed_tables / $total_tables) * 100);
		return [
			'errors' => $errors,
			'percentage' => $percentage
		];
	}

	public function project_tables(){
    $errors = [];

    $latestFYid = $this->latestFYid();
    if (!$latestFYid) {
        return ['status' => false, 'message' => 'Latest FY not found', 'percentage' => 0];
    }

    $newFYid = $this->newFYid();
    if (!$newFYid) {
        return ['status' => false, 'message' => 'New FY not found', 'percentage' => 0];
    }

    $all_tables = ['prjoppybal']; // add more if needed
    $total_steps = count($all_tables);
    $completed = 0;

    foreach ($all_tables as $table) {
        $copyResponse = $this->copy_prjoppybal_with_txn($latestFYid, $newFYid);
        if (!empty($copyResponse['errors'])) {
            $errors = array_merge($errors, $copyResponse['errors']);
        }
        $completed++;
    }
	
	$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 11)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 11)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>11,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		}
	$prev_undercrsmst_data = $this->db->table('undercrsmt')
           ->where('cmp_id', $this->comp_id)
		   ->where('cmpfymastr_id', $latestFYid)
		   ->where('crs_mst_type', 12)
           ->get()
           ->getResultArray();
		if($prev_undercrsmst_data){
			foreach($prev_undercrsmst_data as $lastdata){
				
				$counts = $this->db->table('undercrsmt')
					->where('cmp_id', $this->comp_id)
					->where('cmpfymastr_id', $newFYid)
					->where('crs_mst_type', 12)
					->where('crs_mst_id', $lastdata["crs_mst_id"])
					->countAllResults();
				if($counts==0){
					$insert_data = ["cmp_id"=>$this->comp_id,"crs_mst_type"=>12,"crs_mst_id"=>$lastdata["crs_mst_id"],"under_crs_mst_id"=>$lastdata["under_crs_mst_id"],
										"crs_mst_parent_id"=>$lastdata["crs_mst_parent_id"],"under_main_id"=>$lastdata["under_main_id"],"crs_mst_is_primary"=>$lastdata["crs_mst_is_primary"],
										"cmpfymastr_id"=>$newFYid,"crs_is_active"=>1
										];
					$this->db->table('undercrsmt')->insert($insert_data);					
				}				
								
			}
		}
		
    $percentage = intval(($completed / $total_steps) * 100);

    return [
        'status'     => true,
        'message'    => 'Project opening balances migration completed',
        'errors'     => $errors,
        'percentage' => $percentage
    ];
   }
   
   public function copy_prjoppybal_with_txn($latestFYid, $newFYid)
{
    $errors = [];

    // Step 1: Fetch opening balances from latest FY
    $records = $this->db->table('prjoppybal')
        ->where('cmpfymastr_id', $latestFYid)
		->where('hobo_id', $this->bo_id)
        ->get()
        ->getResultArray();

    if (empty($records)) {
        return ['status' => 'NO_RECORDS', 'message' => 'No records found to copy'];
    }

    foreach ($records as $row) {
        try {
            // Step 2: Copy to prjoppybal for new FY
            $insertBal = [
                'project_id'        => $row['project_id'],
                'cmp_id'            => $row['cmp_id'],
                'cmpfymastr_id'     => $newFYid,
                'project_op_bal'    => $row['project_op_bal'],
                'project_py_bal'    => $row['project_py_bal'],
                'hobo_id'           => $row['hobo_id'],
                'project_txn_type'  => $row['project_txn_type']
            ];
            $this->db->table('prjoppybal')->insert($insertBal);

            // Step 3: Insert opening transaction into prjtxnmstn
            $drcr = ($row['project_txn_type'] == 1) ? 2 : 1; // Asset = DR, Liability = CR
            $txnData = [
                'cmp_id'            => $row['cmp_id'],
                'project_id'        => $row['project_id'],
                'project_txn_date'  => $this->validate_fy_from_date($newFYid),
                'project_txn_dr_cr' => $drcr,
                'project_txn_amt'   => $row['project_op_bal'] ?? 0,
                'project_txn_fcy'   => $row['project_op_bal'] ?? 0,
                'project_txn_narr'  => 'Opening Balance',
                'vch_txn_id'        => 0,
                'txn_id'            => 0,
                'hobo_id'           => $row['hobo_id'],
                'project_txn_type'  => $row['project_txn_type'],
                'acc_bsd_id'        => $row['project_id'],
                'acc_bsd_type'      => 1
            ];
            $this->db->table('prjtxnmstn')->insert($txnData);

        } catch (\Exception $e) {
            $errors[] = "Project ID {$row['project_id']}: " . $e->getMessage();
        }
    }

    return ['status' => 'OK', 'errors' => $errors];
}

	function barcode_tables()
	{
		$errors = [];

		$latestFYid = $this->latestFYid();
		if(!$latestFYid)
			return ['staus'=>false,'mesage'=>'Latest FY not found', 'percentage' => 0];

		$newFYid = $this->newFYid();
		if(!$newFYid)
			return ['staus'=>false,'mesage'=>'New FY not found', 'percentage' => 0];

		$array1 = ['_barcodemst_','_labelmastr_','_labeldescn_'];
		
	    $total_tables = count($array1);
		$completed_tables = 0;

		foreach ($array1 as $tbl_name) {
			$response = $this->copy($tbl_name);
			$completed_tables++;
			foreach ($response as $error) {
				$errors[] = $error;
			}
		}
		$percentage = intval(($completed_tables / $total_tables) * 100);
		return [
			'errors' => $errors,
			'percentage' => $percentage
		];
	}

	function get_acc_data()
	{
		$data = [];
		$errors = [];

		$latestFYid = $this->latestFYid();

		$acctmaster_tbl = $this->comp_id.'_acctmaster_'.$latestFYid;
		$acctgroupn_tbl = $this->comp_id.'_acctgroupn_'.$latestFYid;
		$hobomaster_tbl = $this->comp_id.'_hobomaster_'.$latestFYid;
		$accoppybal_tbl = $this->comp_id.'_accoppybal_'.$latestFYid;
	  

	  $builder = $this->db->table($acctmaster_tbl);
		$builder->select('acc_id,acc_grp_id,acc_grp_parent_id');
		$result = $builder->get()->getResultArray();

		foreach ($result as $key => $value) {
	  		$acc_id = $value['acc_id'];
	  		$parent_id = $value['acc_grp_parent_id'];
				
				if($value['acc_grp_id'] != 0){
					$acctgroupn = $this->db->table($acctgroupn_tbl)
															->select('acc_grp_parent_id')
															->where('acc_grp_id',$value['acc_grp_id'])
															->get()->getRowArray();
					if($acctgroupn){
						$parent_id = $acctgroupn['acc_grp_parent_id'];
					}
				}

				$op_data = [];

				try{
					$accnttxnnn_tbl = $this->comp_id.'_accnttxnnn_'.$acc_id.'_'.$latestFYid;

					$result2 = $this->db->table($hobomaster_tbl)
								    					->select('bo_id,comp_id')
								    					->where('comp_id', $this->comp_id)
								    					->get()->getResultArray();

					foreach ($result2 as $key2 => $value2) {

						$bo_id = $value2['bo_id'];
						$balance = 0;

						if(in_array($parent_id,[1,2,3,4,5]))
						{

							$builder = $this->db->table($accnttxnnn_tbl);
						  $builder->where('comp_id',$this->comp_id);
						  $builder->where('acc_id',$acc_id);
						  $builder->where('bo_id',$bo_id);
						  $builder->orderBy('acc_txn_date','DESC');	 
						  $builder->orderBy('voucher_txn_id','DESC');	 
						  $builder->orderBy('acc_txn_id','DESC');	 		  
						  $builder->limit(1);
						  $accnttxnnn = $builder->get()->getRowArray();

						  if($accnttxnnn){
						  	$balance = floatval($accnttxnnn['acc_bal']);
						  }
						  else{
						  	$accoppybal = $this->db->table($accoppybal_tbl)
																			->select('bo_id,acc_id,acc_op_bal')
																			->where('acc_id', $acc_id)
																			->where('bo_id', $bo_id)
																			->get()->getRowArray();
								if($accoppybal){
									$balance = floatval($accoppybal['acc_op_bal']);
								}
						  }
						}

						$op_data[] = [
					  	'acc_id'			=> $acc_id,
					  	'bo_id'				=> $bo_id,
					  	'acc_py_bal'	=> $balance,
					  	'acc_op_bal'	=> $balance
					  ];
					}
				}
				catch (\Exception $e) {
			   		$errors[] = $e->getMessage();
				}

				$data[$acc_id] = $op_data;
	  }

	  return [
	  	'data' => $data,
	  	'errors' => $errors,
	  ];						  
	}

	function get_bsd_data()
	{
		$data = [];
		$errors = [];

		$latestFYid = $this->latestFYid();

		$billsundry_tbl = $this->comp_id.'_billsundry_'.$latestFYid;
		$acctgroupn_tbl = $this->comp_id.'_acctgroupn_'.$latestFYid;
		$hobomaster_tbl = $this->comp_id.'_hobomaster_'.$latestFYid;
		$bsdoppybal_tbl = $this->comp_id.'_bsdoppybal_'.$latestFYid;
	  

	  $builder = $this->db->table($billsundry_tbl);
		$builder->select('bill_sundry_id,acc_grp_id,acc_grp_parent_id');
		$result = $builder->get()->getResultArray();

		foreach ($result as $key => $value) {
	  		$bsd_id = $value['bill_sundry_id'];
	  		$parent_id = $value['acc_grp_parent_id'];
				
				if($value['acc_grp_id'] != 0){
					$acctgroupn = $this->db->table($acctgroupn_tbl)
															->select('acc_grp_parent_id')
															->where('acc_grp_id',$value['acc_grp_id'])
															->get()->getRowArray();
					if($acctgroupn){
						$parent_id = $acctgroupn['acc_grp_parent_id'];
					}
				}

				$op_data = [];

				try{
					$sundrytxnn_tbl = $this->comp_id.'_sundrytxnn_'.$bsd_id.'_'.$latestFYid;

					$result2 = $this->db->table($hobomaster_tbl)
								    					->select('bo_id,comp_id')
								    					->where('comp_id', $this->comp_id)
								    					->get()->getResultArray();

					foreach ($result2 as $key2 => $value2) {

						$bo_id = $value2['bo_id'];
						$balance = 0;

						if(in_array($parent_id,[1,2,3,4,5]))
						{

							$builder = $this->db->table($sundrytxnn_tbl);
						  $builder->where('comp_id',$this->comp_id);
						  $builder->where('bill_sundry_id',$bsd_id);
						  $builder->where('bo_id',$bo_id);
						  $builder->orderBy('sundry_txn_date','DESC');	 
						  $builder->orderBy('voucher_txn_id','DESC');	 
			        $builder->orderBy('sundry_txn_id','DESC');	 		  
						  $builder->limit(1);
						  $sundrytxnn = $builder->get()->getRowArray();

						  if($sundrytxnn){
						  	$balance = floatval($sundrytxnn['sundry_bal']);
						  }
						  else{
						  	$bsdoppybal = $this->db->table($bsdoppybal_tbl)
																->select('bo_id,bill_sundry_id,bsd_op_bal')
																->where('bill_sundry_id', $bsd_id)
																->where('bo_id', $bo_id)
																->get()->getRowArray();
								if($bsdoppybal){
									$balance = floatval($bsdoppybal['bsd_op_bal']);
								}
						  }
						}

						$op_data[] = [
					  	'bill_sundry_id'			=> $bsd_id,
					  	'bo_id'								=> $bo_id,
					  	'bsd_py_bal'					=> $balance,
					  	'bsd_op_bal'					=> $balance
					  ];
					}
				}
				catch (\Exception $e) {
			   		$errors[] = $e->getMessage();
				}

				$data[$bsd_id] = $op_data;
	  }

	  return [
	  	'data' => $data,
	  	'errors' => $errors,
	  ];						  
	}

	function account_txns($result)
	{
		$errors = [];
		$fy_id = $this->newFYid();

		$accoppybal_tbl = $this->comp_id.'_accoppybal_'.$fy_id;

		foreach ($result as $acc_id => $data) { 
			
			$ERPtables = new ERPtables($this->comp_id,$fy_id);
			$response = $ERPtables->account_txn_tables($acc_id);
			foreach ($response as $key => $value) { $errors[] = $value; }

			if($data)
				$this->db->table($accoppybal_tbl)->insertBatch($data);
		}
		return $errors;
	}

	function bill_sundry_txns($result)
	{
		$errors = [];
		$fy_id = $this->newFYid();

		$bsdoppybal_tbl = $this->comp_id.'_bsdoppybal_'.$fy_id;

		foreach ($result as $bsd_id => $data) { 
			
			$ERPtables = new ERPtables($this->comp_id,$fy_id);
			$response = $ERPtables->bill_sundry_txn_tables($bsd_id);
			foreach ($response as $key => $value) { $errors[] = $value; }

			if($data)
				$this->db->table($bsdoppybal_tbl)->insertBatch($data);
		}
		return $errors;
	}

	function get_item_data()
	{
		$data = [];
		$errors = [];

		$latestFYid = $this->latestFYid();

		$itemmaster_tbl = $this->comp_id.'_itemmaster_'.$latestFYid;
		$hobomaster_tbl = $this->comp_id.'_hobomaster_'.$latestFYid;
		$itmoppybal_tbl = $this->comp_id.'_itmoppybal_'.$latestFYid;
		$itmoppyval_tbl = $this->comp_id.'_itmoppyval_'.$latestFYid;
	  $mcmasternn_tbl = $this->comp_id.'_mcmasternn_'.$latestFYid;

	  $builder = $this->db->table($itemmaster_tbl);
	  $builder->select('item_id');
	  $result = $builder->get()->getResultArray();

		foreach ($result as $key => $value) {
	  		$item_id = $value['item_id'];

				$bal_data = [];
				$val_data = [];

				try{
					$itemtxnnnn_tbl = $this->comp_id.'_itemtxnnnn_'.$item_id.'_'.$latestFYid;
					$itemtxnval_tbl = $this->comp_id.'_itemtxnval_'.$item_id.'_'.$latestFYid;

					$units = $this->get_item_units($item_id);
					foreach ($units as $unit_id) {
						
						$result3 = $this->db->table($mcmasternn_tbl)
														->select('mat_cent_id')
														->where('comp_id', $this->comp_id)
														->get()->getResultArray();

						foreach ($result3 as $key3 => $value3) {
							$mat_cent_id = $value3['mat_cent_id'];

							$result2 = $this->db->table($hobomaster_tbl)
										    					->select('bo_id,comp_id')
										    					->where('comp_id', $this->comp_id)
										    					->get()->getResultArray();

							foreach ($result2 as $key2 => $value2) {

								$bo_id = $value2['bo_id'];
								$balance = 0;
								$value1 = 0;
								$value2 = 0;
								$value3 = 0;

								$builder = $this->db->table($itemtxnnnn_tbl);
							  $builder->where('comp_id',$this->comp_id);
							  $builder->where('item_id',$item_id);
							  $builder->where('item_unit', $unit_id);
			    			$builder->where('mat_cent_id', $mat_cent_id);
							  $builder->where('bo_id',$bo_id);
							  $builder->where('batch_id',0);
							  $builder->orderBy('item_txn_date','DESC');	 
							  $builder->orderBy('voucher_txn_id','DESC');	 
				        $builder->orderBy('item_txn_id','DESC');	 		  
							  $builder->limit(1);
							  $itemtxnnnn = $builder->get()->getRowArray();

							  if($itemtxnnnn){
							  	$balance = floatval($itemtxnnnn['item_bal_qty']);

							  	$itemtxnval = $this->db->table($itemtxnval_tbl)
												->where('item_id', $item_id)
												->where('item_txn_id',$itemtxnnnn['item_txn_id'])
												->where('method_id', 1)
												->get()->getRowArray();
									if($itemtxnval){
										$value1 = floatval($itemtxnval['item_value']);
									}

									$itemtxnval = $this->db->table($itemtxnval_tbl)
												->where('item_id', $item_id)
												->where('item_txn_id',$itemtxnnnn['item_txn_id'])
												->where('method_id', 2)
												->get()->getRowArray();
									if($itemtxnval){
										$value2 = floatval($itemtxnval['item_value']);
									}

									$itemtxnval = $this->db->table($itemtxnval_tbl)
												->where('item_id', $item_id)
												->where('item_txn_id',$itemtxnnnn['item_txn_id'])
												->where('method_id', 3)
												->get()->getRowArray();
									if($itemtxnval){
										$value3 = floatval($itemtxnval['item_value']);
									}
							  }
							  else{
							  	$itmoppybal = $this->db->table($itmoppybal_tbl)
																->select('op_bal_qty')
																->where('item_id', $item_id)
																->where('item_unit', $unit_id)
																->where('mat_cent_id', $mat_cent_id)
																->where('bo_id', $bo_id)
																->where('batch_id', 0)
																->get()->getRowArray();

									if($itmoppybal){
										$balance = floatval($itmoppybal['op_bal_qty']);
									}

									$itmoppyval = $this->db->table($itmoppyval_tbl) 
											->where('item_id', $item_id)
											->where('item_unit', $unit_id)
											->where('mat_cent_id', $mat_cent_id)
											->where('bo_id', $bo_id)
											->where('batch_id', 0)
											->where('method_id', 1)
											->get()->getRowArray();
										
									if($itmoppyval){
							    	$value1 = floatval($itmoppyval['op_bal_val']);
							    }

							    $itmoppyval = $this->db->table($itmoppyval_tbl)
											->where('item_id', $item_id)
											->where('item_unit', $unit_id)
											->where('mat_cent_id', $mat_cent_id)
											->where('bo_id', $bo_id)
											->where('batch_id', 0)
											->where('method_id', 2)
											->get()->getRowArray();
										
									if($itmoppyval){
							    	$value2 = floatval($itmoppyval['op_bal_val']);
							    }

							    $itmoppyval = $this->db->table($itmoppyval_tbl) 
											->where('item_id', $item_id)
											->where('item_unit', $unit_id)
											->where('mat_cent_id', $mat_cent_id)
											->where('bo_id', $bo_id)
											->where('batch_id', 0)
											->where('method_id', 3)
											->get()->getRowArray();
										
									if($itmoppyval){
							    	$value3 = floatval($itmoppyval['op_bal_val']);
							    }	
								  	
							  }

							  $bal_data[] = [
							  	'item_id'				=> $item_id,
							  	'mat_cent_id'		=> $mat_cent_id,
							  	'item_unit'			=> $unit_id,
							  	'batch_id'			=> 0,
							  	'bo_id'					=> $bo_id,
							  	'op_bal_qty'		=> $balance,
							  	'py_bal_qty'		=> $balance
							  ];

							  $val_data[] = [
							  	'item_id'				=> $item_id,
							  	'mat_cent_id'		=> $mat_cent_id,
							  	'item_unit'			=> $unit_id,
							  	'batch_id'			=> 0,
							  	'bo_id'					=> $bo_id,
							  	'op_bal_val'		=> $value1,
							  	'py_bal_val'		=> $value1,
							  	'method_id'			=> 1
							  ];
							  $val_data[] = [
							  	'item_id'				=> $item_id,
							  	'mat_cent_id'		=> $mat_cent_id,
							  	'item_unit'			=> $unit_id,
							  	'batch_id'			=> 0,
							  	'bo_id'					=> $bo_id,
							  	'op_bal_val'		=> $value2,
							  	'py_bal_val'		=> $value2,
							  	'method_id'			=> 2
							  ];
							  $val_data[] = [
							  	'item_id'				=> $item_id,
							  	'mat_cent_id'		=> $mat_cent_id,
							  	'item_unit'			=> $unit_id,
							  	'batch_id'			=> 0,
							  	'bo_id'					=> $bo_id,
							  	'op_bal_val'		=> $value3,
							  	'py_bal_val'		=> $value3,
							  	'method_id'			=> 3
							  ];
								
							}
						}
					}	
				}
				catch (\Exception $e) {
			   		$errors[] = $e->getMessage();
				}

				$data[$item_id] = [
					'bal_data' => $bal_data, 
					'val_data' => $val_data
				];
	  }

	  return [
	  	'data' => $data,
	  	'errors' => $errors,
	  ];						  
	}

	function get_item_units($item_id)
	{
		$latestFYid = $this->latestFYid();
	
		$unit_result = [];

		$itemmaster_tbl = $this->comp_id.'_itemmaster_'.$latestFYid;
		$itmoppybal_tbl = $this->comp_id.'_itmoppybal_'.$latestFYid;

		$itemmaster = $this->db->table($itemmaster_tbl)
													->where('item_id', $item_id)
													->get()->getRowArray();
		if($itemmaster){
				$unit_result[] = $itemmaster['item_unit'];

				$builder = $this->db->table($itmoppybal_tbl);
				$builder->select('item_unit');
				$builder->where('item_id', $item_id);
				$builder->groupBy('item_unit');
				$result = $builder->get()->getResultArray();
				if($result){
					foreach ($result as $key => $value) {
						if(!in_array($value['item_unit'], $unit_result)){
							$unit_result[] = $value['item_unit'];
						}
					}
				}
		}

		return $unit_result;
	}

	function item_txns($result)
	{
		$errors = [];
		$fy_id = $this->newFYid();

		$itmoppybal_tbl = $this->comp_id.'_itmoppybal_'.$fy_id;
		$itmoppyval_tbl = $this->comp_id.'_itmoppyval_'.$fy_id;

		foreach ($result as $item_id => $data) { 
			
			$ERPtables = new ERPtables($this->comp_id,$fy_id);
			$response = $ERPtables->item_txn_tables($item_id);

			foreach ($response as $key => $value) { $errors[] = $value; }

			if($data['bal_data'])
				$this->db->table($itmoppybal_tbl)->insertBatch($data['bal_data']);

			if($data['val_data'])
				$this->db->table($itmoppyval_tbl)->insertBatch($data['val_data']);
		}
		return $errors;
	}

	function get_cost_center_data()
  {
  	$final = [];

  	$latestFYid = $this->latestFYid();

    $costctmstr_tbl = $this->comp_id.'_costctmstr_'.$latestFYid;
    $costctoppy_tbl = $this->comp_id.'_costctoppy_'.$latestFYid;
    $costcttxnn_tbl = $this->comp_id.'_costcttxnn_'.$latestFYid;
    $hobomaster_tbl = $this->comp_id.'_hobomaster_'.$latestFYid;

    $builder = $this->db->table($costctmstr_tbl);
		$builder->select('cc_id');
		$result = $builder->get()->getResultArray();

  	foreach ($result as $key => $value) {
  		$cc_id = $value['cc_id'];

  		$result2 = $this->db->table($hobomaster_tbl)
								    					->select('bo_id')
								    					->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {

				$bo_id = $value2['bo_id'];
				$balance = 0;

				$builder = $this->db->table($costcttxnn_tbl);
	      $builder->select('cc_txn_bal');
	      $builder->where('cc_id', $cc_id);
	      $builder->where('bo_id', $bo_id);
	      $builder->orderBy('cc_txn_date', 'desc');
	      $builder->orderBy('voucher_txn_id', 'desc');
	      $builder->orderBy('cc_txn_id', 'desc');
	      $builder->limit(1);
	      $cc_txn = $builder->get()->getRowArray();
	      if($cc_txn)
	      {
	        $balance = floatval($cc_txn['cc_txn_bal']);
	      }
	      else{
	      	$costctoppy = $this->db->table($costctoppy_tbl)
                          ->where('cc_id', $cc_id)
                          ->where('bo_id', $bo_id)
                          ->get()->getRowArray();
          if($costctoppy){
          	$balance = floatval($costctoppy['cc_op_bal']);
          }               
	      }
	                    
	      $final[] = [
            'cc_id'           => $cc_id,
            'bo_id'           => $bo_id,
            'cc_op_bal'   		=> $balance,
            'cc_py_bal'   		=> $balance,
        ];
			} 
  	}
        
		return $final;
  }

  function cost_center_txns($data)
  {
  	$newFYid = $this->newFYid();

  	$costctoppy_tbl = $this->comp_id.'_costctoppy_'.$newFYid;
  	if(count($data))
			$this->db->table($costctoppy_tbl)->insertBatch($data);
  }

  function get_bill_by_bill_data()
  {
  	$final = [];
  	$latestFYid = $this->latestFYid();

    $billmaster_tbl = $this->comp_id.'_billmaster_'.$latestFYid;
    $billstxnnn_tbl = $this->comp_id.'_billstxnnn_'.$latestFYid;
    $billsoppyn_tbl = $this->comp_id.'_billsoppyn_'.$latestFYid;
    $hobomaster_tbl = $this->comp_id.'_hobomaster_'.$latestFYid;

    $builder = $this->db->table($billmaster_tbl);
		$builder->select('bills_ref_id');
		$result = $builder->get()->getResultArray();

  	foreach ($result as $key => $value) {
  		$bills_ref_id = $value['bills_ref_id'];
  		$result2 = $this->db->table($hobomaster_tbl)
								    					->select('bo_id')
								    					->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {

				$bo_id = $value2['bo_id'];
				$balance = 0;

				$builder = $this->db->table($billstxnnn_tbl); 
	      $builder->select('bills_txn_bal');
	      $builder->where('bills_ref_id', $bills_ref_id);
	      $builder->where('bo_id', $bo_id);
	      $builder->orderBy('bills_txn_date', 'desc');
	      $builder->orderBy('voucher_txn_id', 'desc');
	      $builder->orderBy('bills_txn_id', 'desc');
	      $builder->limit(1);
	      $bill_txn = $builder->get()->getRowArray();
	      
	      if($bill_txn){
	        $balance = floatval($bill_txn['bills_txn_bal']);
	      }
	      else{
	      	$billsoppyn = $this->db->table($billsoppyn_tbl)
                          ->where('bills_ref_id', $bills_ref_id)
                          ->where('bo_id', $bo_id)
                          ->get()->getRowArray();
          if($billsoppyn){
          	$balance = floatval($billsoppyn['bills_op_bal']);
          } 
	      }
	      
	                    
	      $final[] = [
	              'bills_ref_id'     	=>   $bills_ref_id,
	              'bo_id'     			 	=>   $bo_id,
	              'bills_op_bal'      =>   $balance,
	              'bills_py_bal'      =>   $balance,
	      ];

			}   
  	}
        
		return $final;
  }

  function bill_by_bill_txns($data)
  {
  	$newFYid = $this->newFYid();

  	$billsoppyn_tbl = $this->comp_id.'_billsoppyn_'.$newFYid;
  	if(count($data))
			$this->db->table($billsoppyn_tbl)->insertBatch($data);
  }

  function get_project_data()
  {
  	$final = [];
  	$latestFYid = $this->latestFYid();

    $projectmst_tbl = $this->comp_id.'_projectmst_'.$latestFYid;
    $prjoppybal_tbl = $this->comp_id.'_prjoppybal_'.$latestFYid;
    $hobomaster_tbl = $this->comp_id.'_hobomaster_'.$latestFYid;

    $prjliabtxn_tbl = $this->comp_id.'_prjliabtxn_'.$latestFYid;
    $projasttxn_tbl = $this->comp_id.'_projasttxn_'.$latestFYid;
    $projexptxn_tbl = $this->comp_id.'_projexptxn_'.$latestFYid;
    $projrevtxn_tbl = $this->comp_id.'_projrevtxn_'.$latestFYid;

    $builder = $this->db->table($projectmst_tbl);
		$builder->select('project_id');
		$result = $builder->get()->getResultArray();

  	foreach ($result as $key => $value) {
  		$project_id = $value['project_id'];

  		$result2 = $this->db->table($hobomaster_tbl)
								    					->select('bo_id')
								    					->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {

				$bo_id = $value2['bo_id'];
				$balance = 0;

				$builder = $this->db->table($prjliabtxn_tbl);
	      $builder->select('proj_txn_bal');
	      $builder->where('project_id', $project_id);
	      $builder->where('bo_id', $bo_id);
	      $builder->orderBy('proj_txn_date', 'desc');
	      $builder->orderBy('voucher_txn_id', 'desc');
	      $builder->orderBy('proj_txn_id', 'desc');
	      $builder->limit(1);
	      $proj_txn = $builder->get()->getRowArray();
	      if($proj_txn)
	      {
	        $balance = floatval($proj_txn['proj_txn_bal']);
	      }
	      else{
	      	$prjoppybal = $this->db->table($prjoppybal_tbl)
                          ->where('project_id', $project_id)
                          ->where('project_op_bal', 'lia')
                          ->where('bo_id', $bo_id)
                          ->get()->getRowArray();
          if($prjoppybal){
          	$balance = floatval($prjoppybal['project_op_bal']);
          }               
	      }
	                    
	      $final[] = [
            'project_id'          => $project_id,
            'bo_id'           		=> $bo_id,
            'project_op_bal'   		=> $balance,
            'project_py_bal'   		=> $balance,
            'project_bal_type'		=> 'lia',
        ];

        //---
        $builder = $this->db->table($projasttxn_tbl);
	      $builder->select('proj_txn_bal');
	      $builder->where('project_id', $project_id);
	      $builder->where('bo_id', $bo_id);
	      $builder->orderBy('proj_txn_date', 'desc');
	      $builder->orderBy('voucher_txn_id', 'desc');
	      $builder->orderBy('proj_txn_id', 'desc');
	      $builder->limit(1);
	      $proj_txn = $builder->get()->getRowArray();
	      if($proj_txn)
	      {
	        $balance = floatval($proj_txn['proj_txn_bal']);
	      }
	      else{
	      	$prjoppybal = $this->db->table($prjoppybal_tbl)
                          ->where('project_id', $project_id)
                          ->where('project_op_bal', 'ast')
                          ->where('bo_id', $bo_id)
                          ->get()->getRowArray();
          if($prjoppybal){
          	$balance = floatval($prjoppybal['project_op_bal']);
          }               
	      }
	                    
	      $final[] = [
            'project_id'          => $project_id,
            'bo_id'           		=> $bo_id,
            'project_op_bal'   		=> $balance,
            'project_py_bal'   		=> $balance,
            'project_bal_type'		=> 'ast',
        ];

        //---
        $builder = $this->db->table($projexptxn_tbl);
	      $builder->select('proj_txn_bal');
	      $builder->where('project_id', $project_id);
	      $builder->where('bo_id', $bo_id);
	      $builder->orderBy('proj_txn_date', 'desc');
	      $builder->orderBy('voucher_txn_id', 'desc');
	      $builder->orderBy('proj_txn_id', 'desc');
	      $builder->limit(1);
	      $proj_txn = $builder->get()->getRowArray();
	      if($proj_txn)
	      {
	        $balance = floatval($proj_txn['proj_txn_bal']);
	      }
	      else{
	      	$prjoppybal = $this->db->table($prjoppybal_tbl)
                          ->where('project_id', $project_id)
                          ->where('project_op_bal', 'exp')
                          ->where('bo_id', $bo_id)
                          ->get()->getRowArray();
          if($prjoppybal){
          	$balance = floatval($prjoppybal['project_op_bal']);
          }               
	      }
	                    
	      $final[] = [
            'project_id'          => $project_id,
            'bo_id'           		=> $bo_id,
            'project_op_bal'   		=> $balance,
            'project_py_bal'   		=> $balance,
            'project_bal_type'		=> 'exp',
        ];

        //---
        $builder = $this->db->table($projrevtxn_tbl);
	      $builder->select('proj_txn_bal');
	      $builder->where('project_id', $project_id);
	      $builder->where('bo_id', $bo_id);
	      $builder->orderBy('proj_txn_date', 'desc');
	      $builder->orderBy('voucher_txn_id', 'desc');
	      $builder->orderBy('proj_txn_id', 'desc');
	      $builder->limit(1);
	      $proj_txn = $builder->get()->getRowArray();
	      if($proj_txn)
	      {
	        $balance = floatval($proj_txn['proj_txn_bal']);
	      }
	      else{
	      	$prjoppybal = $this->db->table($prjoppybal_tbl)
                          ->where('project_id', $project_id)
                          ->where('project_op_bal', 'rev')
                          ->where('bo_id', $bo_id)
                          ->get()->getRowArray();
          if($prjoppybal){
          	$balance = floatval($prjoppybal['project_op_bal']);
          }               
	      }
	                    
	      $final[] = [
            'project_id'          => $project_id,
            'bo_id'           		=> $bo_id,
            'project_op_bal'   		=> $balance,
            'project_py_bal'   		=> $balance,
            'project_bal_type'		=> 'rev',
        ];
			} 
  	}
        
		return $final;
  }

  function project_txns($data)
  {
  	$newFYid = $this->newFYid();

  	$prjoppybal_tbl = $this->comp_id.'_prjoppybal_'.$newFYid;
  	if(count($data))
			$this->db->table($prjoppybal_tbl)->insertBatch($data);
  }

  function latestFYid()
  {
	$result = $this->univaictly->table('cmpfymastr')
		   ->select('cmpfymastr_id')
		   ->where('is_imported','1')
			->where('cmp_id', $this->comp_id)
			 ->orderBy('cmpfymastr_id','desc')
			 ->limit(1)
			 ->get()->getRowArray();
			if($result)
        return $result['cmpfymastr_id'];
	else
	return 0;
  }

  function newFYid()
  {
	  $result=$this->univaictly->table('cmpfymastr')
											   ->select('cmpfymastr_id')
											   ->where('is_imported','0')
			   	            	 ->where('cmp_id', $this->comp_id)
												 ->orderBy('cmpfymastr_id','asc')
												 ->limit(1)
												 ->get()->getRowArray();
			if($result)
        return $result['cmpfymastr_id'];

      return 0;
  }

	function copy($tbl,$insert=TRUE,$create=TRUE,$drop=TRUE)
	{
		$errors = [];

		$old_tbl = $this->comp_id . $tbl . $this->latestFYid();
		$new_tbl = $this->comp_id . $tbl . $this->newFYid();

		try{


			if($insert){
				$this->db->query("INSERT INTO ${new_tbl} SELECT * FROM ${old_tbl}");
			}
		}
		catch (\Exception $e) {
	   		$errors[] = $e->getMessage();
		}  

		return $errors;
  }

  //-------------------------------------------------------------
  
  function nextFYid()
  {
		$result=$this->univaictly->table('cmpfymastr')
			->select('cmpfymastr_id')
			->where('is_imported', '1')
			->where('cmp_id', $this->comp_id)
			->where('cmpfymastr_id >',$this->fy_id)
			->orderBy('cmpfymastr_id','asc')
			->limit(1)
			->get()->getRowArray();
		if($result)
        return $result['cmpfymastr_id'];
      return 0;
  }

  function get_branch_gstin_details()
  {
  	$nextFYid = $this->nextFYid();
  	$final = [];

  	if($nextFYid != 0)
  	{
  		$table = $this->comp_id.'_gstinmastr_'.$this->fy_id;
	  	$result = $this->db->table($table)
												 ->get()->getResultArray();

			foreach ($result as $key => $value) {
				$id = $value['gstinmastr_id'];
				$name = $value['comp_gstin'];

				$type = 'gstinmastr';
				
				$table = $this->comp_id.'_gstinmastr_'.$nextFYid;
				 $response = $this->db->table($table)
	              ->where('comp_gstin',trim($name))
				  ->get()->getNumRows();
				if($response == 0){
					$final[] = [
						'id'		=> $id,
						'name'	=> $name,
					];
				}
			}	
  	}

  	return $final;
  }

  

  
  //-----------------------------------------------------------

function clear_next_fy_account_balance(){
	 $nextFYid = $this->nextFYid();
	 $cmpId        = (int) $this->comp_id;
	 $hoboId   = (int)$this->bo_id;
	 $this->db->table('accoppybal')
            ->where('cmp_id', $cmpId)
			->where('cmpfymastr_id', $nextFYid)
            ->where('hobo_id', $hoboId)
			->delete();
  }
  
function clear_next_fy_item_balance(){
	 $nextFYid = $this->nextFYid();
	 $hoboId   = (int)$this->bo_id;
	 $cmpId        = (int) $this->comp_id;
	 $this->db->table('itmoppybal')
            ->where('cmp_id', $cmpId)
            ->where('cmpfymastr_id', $nextFYid)
			->where('hobo_id', $hoboId)
			->delete();
  }
  
function clear_next_fy_item_valuation(){
	 $nextFYid = $this->nextFYid();
	 $cmpId        = (int) $this->comp_id;
	 $hoboId   = (int)$this->bo_id;
	 $this->db->table('itmoppyval')
            ->where('cmp_id', $cmpId)
            ->where('cmpfymastr_id', $nextFYid)
			->where('hobo_id', $hoboId)
			->delete();
  }
 
 public function prepare_common_fy_data()
{
    $cmpId    = $this->comp_id;
    $currFyId = $this->session->get('ses_comp_fy_id');
    $hoboId   = $this->bo_id;
	
    // Branch / HO name
	$hoboRow = $this->univaictly->table('hobomaster')
        ->select('hobo_name')
        ->where([
            'cmp_id'  => $cmpId,
            'hobo_id' => $hoboId
        ])
        ->get()->getRowArray();

    $hoboName = trim((string) ($hoboRow['hobo_name'] ?? 'UNKNOWN'));
    // FY dates
    $fyRow = $this->univaictly->table('cmpfymastr')
        ->select('fy_beg_date, fy_end_date')
        ->where([
            'cmp_id' => $cmpId,
            'cmpfymastr_id' => $currFyId
        ])->get()->getRowArray();

    $dtStart = $fyRow['fy_beg_date'];
    $dtEnd   = $fyRow['fy_end_date'];

    // P&L calculation (ONLY ONCE)
    $response = $this->ReportingModel->load_profit_loss_horizontal(0, $dtStart, $dtEnd, 0, 0);
    $lastRow  = is_array($response) ? end($response) : [];

   $profitValue = isset($lastRow['l_balance_total']) ? (float) $lastRow['l_balance_total'] : 0.0;
   $lossValue   = isset($lastRow['r_balance_total']) ? (float) $lastRow['r_balance_total'] : 0.0;
   $netProfitLoss = $profitValue - $lossValue; // signed

    return [
        'dtStart' => $dtStart,
        'dtEnd'   => $dtEnd,
        'netPL'   => $netProfitLoss,
		'hoboName' => $hoboName
    ];
}

public function update_fy_account_balance(int $accId, $common): array
{
    $errors   = [];
    $nextFYid = (int) $this->nextFYid();

    if ($nextFYid === 0) {
        SaveErrorLog("[FY MIGRATION] accId={$accId} | Next Financial Year does not exist");
        return ['Next Financial Year does not exist'];
    }

    $cmpId    = (int) $this->comp_id;
    $currFyId = (int) $this->session->get('ses_comp_fy_id');
    $hoboId   = (int) $this->bo_id;

    $dtStart = $common['dtStart'];
	$dtEnd   = $common['dtEnd'];
	$netProfitLoss = $common['netPL'];
	$hoboName = $common['hoboName'];

    $logPrefix = "[FY MIGRATION] cmp_id={$cmpId} hobo_id={$hoboId} hobo_name=\"{$hoboName}\" curr_fy={$currFyId} next_fy={$nextFYid} acc_id={$accId}";


    // Account info (for the accId currently being processed)
    $accRow = $this->db->table('acctmaster')
        ->select('acc_name, bsd_id')
        ->where([
            'cmp_id' => $cmpId,
            'acc_id' => $accId
        ])
        ->get()->getRowArray();

    if (!$accRow) {
        SaveErrorLog("{$logPrefix} | Account not found");
        return ["Account {$accId} not found"];
    }

    $accName      = trim((string) ($accRow['acc_name'] ?? ''));
    $bsdId        = $accRow['bsd_id'] ?? null;
    $isBillSundry = !empty($bsdId);

    /*
    |--------------------------------------------------------------------------
    | Always ensure P&L Appropriation carry-forward exists in NEXT FY:
    | - If P&L Appropriation exists in CURRENT FY, use its closing + net P&L
    | - If it does NOT exist in CURRENT FY, carry net P&L into NEXT FY under it
    | - If P&L Appropriation does NOT exist in NEXT FY, create it (and mapping)
    |--------------------------------------------------------------------------
    */
    try {
        // 1) Find P&L Appropriation account in CURRENT FY (acctmaster is common, not FY-specific)
        $plAccRow = $this->db->table('acctmaster')
            ->select('acc_id, acc_name')
            ->where('cmp_id', $cmpId)
            ->where('LOWER(acc_name)', strtolower('Profit & Loss Appropriation'))
            ->get()->getRowArray();

        $plAccId = (int)($plAccRow['acc_id'] ?? 0);
        $plExistsInCurr = ($plAccId > 0);

        // 2) Compute CURRENT FY net profit/loss from report (branch-wise)
       /*  $response = $this->ReportingModel->load_profit_loss_horizontal(0, $dtStart, $dtEnd, 0, 0);
        $lastRow  = is_array($response) ? end($response) : [];

        $profitValue = isset($lastRow['l_balance_total']) ? (float) $lastRow['l_balance_total'] : 0.0;
        $lossValue   = isset($lastRow['r_balance_total']) ? (float) $lastRow['r_balance_total'] : 0.0;
        $netProfitLoss = $profitValue - $lossValue; // signed */

        // 3) If P&L Appropriation exists in current FY, compute its closing (opening + movement)
        $openingPL = 0.0;
        $netMovePL = 0.0;
        $closingPL = 0.0;

        if ($plExistsInCurr) {
            $opRowPL = $this->db->table('accoppybal')
                ->select('COALESCE(SUM(acc_op_bal),0) as op', false)
                ->where([
                    'cmp_id'        => $cmpId,
                    'cmpfymastr_id' => $currFyId,
                    'acc_id'        => $plAccId,
                    'hobo_id'       => $hoboId
                ])
                ->get()->getRowArray();
            $openingPL = (float)($opRowPL['op'] ?? 0.0);

            $moveRowPL = $this->db->query("
                SELECT COALESCE(SUM(
                    CASE at.acc_txn_dr_cr
                        WHEN 1 THEN at.acc_txn_amt
                        WHEN 2 THEN -at.acc_txn_amt
                        ELSE 0
                    END
                ),0) AS net_move
                FROM accttxnmst at
                INNER JOIN vchtxnconso vc ON vc.vch_txn_id = at.vch_txn_id
                WHERE at.cmp_id = ?
                  AND at.acc_id = ?
                  AND at.hobo_id = ?
                  AND at.acc_txn_type = 1
                  AND vc.hobo_id = ?
                  AND vc.vch_date BETWEEN ? AND ?
            ", [$cmpId, $plAccId, $hoboId, $hoboId, $dtStart, $dtEnd])->getRowArray();

            $netMovePL = (float)($moveRowPL['net_move'] ?? 0.0);
            $closingPL = $openingPL + $netMovePL;
        }

        // 4) What to carry forward into NEXT FY opening for P&L Appropriation
        //    - If PL Acc exists in curr FY => (closingPL + netProfitLoss)
        //    - Else => only netProfitLoss
        $carryForwardPL = ($plExistsInCurr ? ($closingPL + $netProfitLoss) : $netProfitLoss);

        SaveErrorLog(
            "{$logPrefix} P&L Appropriation ensure-nextFY | plExistsInCurr=" . ($plExistsInCurr ? '1' : '0') .
            " plAccIdCurr={$plAccId} openingPL={$openingPL} netMovePL={$netMovePL} closingPL={$closingPL}" .
            " netProfitLoss={$netProfitLoss} carryForwardPL={$carryForwardPL}"
        );

        // 5) Ensure "Profit & Loss Appropriation" account exists (if not, create it)
        if ($plAccId === 0) {
            $this->db->table('acctmaster')->insert([
                'cmp_id'         => $cmpId,
                'acc_name'       => 'Profit & Loss Appropriation',
                'acc_alias'      => 'Profit & Loss Appropriation',
                'acc_print_name' => 'Profit & Loss Appropriation',
                'acc_is_active'  => 1
            ]);
            $plAccId = (int)$this->db->insertID();

            // create account details (as your earlier logic)
            $this->db->table('acctmstdet')->insert([
                'cmp_id'         => $cmpId,
                'acc_id'         => $plAccId,
                'acc_is_sys_acc' => 1,
                'acc_is_sez'     => 0
            ]);

            SaveErrorLog("{$logPrefix} P&L Appropriation account CREATED | new plAccId={$plAccId}");
        }

        // 6) Ensure undercrsmt mapping exists for NEXT FY for this account
        //    (We try to map under "Reserves & Surplus" group if present; else fallback to safe defaults)
        $under_acc_grp_id  = 0;
        $acc_grp_parent_id = 1;
        $under_main_grp_id = 1;
        $crs_mst_is_primary = 0;

        $group_name = "Reserves & Surplus";
        $group = $this->db->table('accgrpmstn')
            ->where('cmp_id', $cmpId)
            ->where('LOWER(acc_grp_name)', strtolower($group_name))
            ->get()->getRow();

        if ($group) {
            $group_id = (int)$group->acc_grp_id;

            $main_group_info = $this->main_group_info($group_id);
            if ($main_group_info) {
                $acc_grp_parent_id = (int)$main_group_info['crs_mst_parent_id'];
                $under_acc_grp_id  = (int)$main_group_info['acc_grp_id'];
                $under_main_grp_id = ((int)$main_group_info['crs_mst_is_primary'] === 1)
                    ? (int)$main_group_info['acc_grp_id']
                    : (int)$main_group_info['under_main_id'];
            }
        }

        $cntNextMap = $this->db->table('undercrsmt')
            ->where('cmp_id', $cmpId)
            ->where('cmpfymastr_id', $nextFYid)
            ->where('crs_mst_type', 1)
            ->where('crs_mst_id', $plAccId)
            ->countAllResults();

        if ($cntNextMap == 0) {
            $this->db->table('undercrsmt')->insert([
                'cmp_id'             => $cmpId,
                'crs_mst_type'       => 1,
                'crs_mst_id'         => $plAccId,
                'under_crs_mst_id'   => $under_acc_grp_id,
                'crs_mst_parent_id'  => $acc_grp_parent_id,
                'under_main_id'      => $under_main_grp_id,
                'cmpfymastr_id'      => $nextFYid,
                'crs_mst_is_primary' => $crs_mst_is_primary,
                'crs_is_active'      => 1
            ]);
            SaveErrorLog("{$logPrefix} P&L Appropriation undercrsmt CREATED for nextFY={$nextFYid}");
        }

        // 7) Upsert accoppybal in NEXT FY for P&L Appropriation (SIGNED as-is)
        $existingPLNext = $this->db->table('accoppybal')->where([
            'cmp_id'        => $cmpId,
            'cmpfymastr_id' => $nextFYid,
            'acc_id'        => $plAccId,
            'hobo_id'       => $hoboId
        ])->get()->getRow();

        if ($existingPLNext) {
            $this->db->table('accoppybal')->where([
                'cmp_id'        => $cmpId,
                'cmpfymastr_id' => $nextFYid,
                'acc_id'        => $plAccId,
                'hobo_id'       => $hoboId
            ])->update([
                'acc_op_bal'   => ($carryForwardPL < 0 ) ? abs($carryForwardPL):-$carryForwardPL,
                'acc_py_bal'   => ($carryForwardPL < 0 ) ? abs($carryForwardPL):-$carryForwardPL,
                'acc_memo_bal' => 0
            ]);
            SaveErrorLog("{$logPrefix} P&L Appropriation nextFY opening UPDATED | plAccId={$plAccId} bal={$carryForwardPL}");
        } else {
            $this->db->table('accoppybal')->insert([
                'cmp_id'        => $cmpId,
                'cmpfymastr_id' => $nextFYid,
                'acc_id'        => $plAccId,
                'acc_op_bal'    => ($carryForwardPL < 0 ) ? abs($carryForwardPL):-$carryForwardPL,
                'acc_py_bal'    => ($carryForwardPL < 0 ) ? abs($carryForwardPL):-$carryForwardPL,
                'hobo_id'       => $hoboId,
                'acc_memo_bal'  => 0
            ]);
            SaveErrorLog("{$logPrefix} P&L Appropriation nextFY opening INSERTED | plAccId={$plAccId} bal={$carryForwardPL}");
        }

        // If the current processing account *is* the P&L Appropriation account, we are done.
        if (strtolower($accName) === strtolower('Profit & Loss Appropriation')) {
            return $errors;
        }

    } catch (\Throwable $e) {
        // Do not break the whole rewrite-books run; log and continue normal logic
        $errors[] = "P&L Appropriation ensure-nextFY failed: " . $e->getMessage();
        SaveErrorLog("{$logPrefix} | P&L Appropriation ensure-nextFY failed: {$e->getMessage()} | {$e->getFile()}:{$e->getLine()}");
    }

    /*
    |--------------------------------------------------------------------------
    | Normal account balance carry-forward (all accounts EXCEPT P&L Appropriation)
    |--------------------------------------------------------------------------
    */

    // Correct FY/type-safe mapping
    $parentRow = $this->db->table('undercrsmt')
        ->select('crs_mst_parent_id')
        ->where([
            'crs_mst_id'    => $accId,
            'cmp_id'        => $cmpId,
            'cmpfymastr_id' => $currFyId,
            'crs_mst_type'  => $isBillSundry ? 14 : 1,
        ])
        ->get()->getRowArray();

    $parent_id = (int) ($parentRow['crs_mst_parent_id'] ?? 0);

    // Skip restricted P&L parent if needed
    $restrictedParentIds = [6, 7, 8, 9, 12, 13];
    if (in_array($parent_id, $restrictedParentIds, true)) {
        SaveErrorLog("{$logPrefix} acc_name=\"{$accName}\" | skipped due to restricted parent_id={$parent_id}");
        return $errors;
    }

    // Opening balance of current FY
    $opRow = $this->db->table('accoppybal')
        ->select('COALESCE(SUM(acc_op_bal),0) as op, COALESCE(SUM(acc_memo_bal),0) as memo', false)
        ->where([
            'cmp_id'        => $cmpId,
            'cmpfymastr_id' => $currFyId,
            'acc_id'        => $accId,
            'hobo_id'       => $hoboId
        ])
        ->get()->getRowArray();

    $opening     = (float) ($opRow['op'] ?? 0);
    $memoOpening = (float) ($opRow['memo'] ?? 0);

    // Normal movement within current FY
    $moveRow = $this->db->query("
        SELECT COALESCE(SUM(
            CASE at.acc_txn_dr_cr
                WHEN 1 THEN at.acc_txn_amt
                WHEN 2 THEN -at.acc_txn_amt
                ELSE 0
            END
        ),0) AS net_move
        FROM accttxnmst at
        INNER JOIN vchtxnconso vc
            ON vc.vch_txn_id = at.vch_txn_id
        WHERE at.cmp_id = ?
          AND at.acc_id = ?
          AND at.hobo_id = ?
          AND at.acc_txn_type = 1
          AND vc.hobo_id = ?
          AND vc.vch_date BETWEEN ? AND ?
    ", [$cmpId, $accId, $hoboId, $hoboId, $dtStart, $dtEnd])->getRowArray();

    $netMove = (float) ($moveRow['net_move'] ?? 0);
    $closing = $opening + $netMove;

    // Memo movement only within current FY
    $memoMove    = 0.0;
    $memoClosing = $memoOpening;

    if ($isBillSundry) {
        $memoMoveRow = $this->db->query("
            SELECT COALESCE(SUM(
                CASE at.acc_txn_dr_cr
                    WHEN 1 THEN at.acc_txn_amt
                    WHEN 2 THEN -at.acc_txn_amt
                    ELSE 0
                END
            ),0) AS net_move
            FROM accttxnmst at
            INNER JOIN vchtxnconso vc
                ON vc.vch_txn_id = at.vch_txn_id
            WHERE at.cmp_id = ?
              AND at.acc_id = ?
              AND at.hobo_id = ?
              AND at.acc_txn_type = 3
              AND vc.hobo_id = ?
              AND vc.vch_date BETWEEN ? AND ?
        ", [$cmpId, $accId, $hoboId, $hoboId, $dtStart, $dtEnd])->getRowArray();

        $memoMove     = (float) ($memoMoveRow['net_move'] ?? 0);
        $memoClosing += $memoMove;
    }

    $builder = $this->db->table('accoppybal')
        ->where('cmp_id', $cmpId)
        ->where('cmpfymastr_id', $nextFYid)
        ->where('acc_id', $accId)
        ->where('hobo_id', $hoboId);

    if ($bsdId === null) {
        $builder->where('bsd_id IS NULL', null, false);
    } else {
        $builder->where('bsd_id', $bsdId);
    }

    $exists = $builder->countAllResults();

    $data = [
        'cmp_id'        => $cmpId,
        'cmpfymastr_id' => $nextFYid,
        'acc_id'        => $accId,
        'hobo_id'       => $hoboId,
        'bsd_id'        => $bsdId,
        'acc_op_bal'    => $closing,
        'acc_py_bal'    => $closing,
        'acc_memo_bal'  => $memoClosing,
    ];

    $action = '';
    if ($exists > 0) {
        $this->db->table('accoppybal')
            ->where([
                'cmp_id'        => $cmpId,
                'cmpfymastr_id' => $nextFYid,
                'acc_id'        => $accId,
                'hobo_id'       => $hoboId,
            ])
            ->where($bsdId === null ? 'bsd_id IS NULL' : 'bsd_id = ' . $this->db->escape($bsdId), null, false)
            ->update($data);

        $action = 'UPDATED';
    } else {
        $this->db->table('accoppybal')->insert($data);
        $action = 'INSERTED';
    }

    SaveErrorLog(
        "{$logPrefix} acc_name=\"{$accName}\" parent_id={$parent_id} is_bsd=" . ($isBillSundry ? '1' : '0') .
        " fy_range={$dtStart} to {$dtEnd}" .
        " opening={$opening}" .
        " movement={$netMove}" .
        " closing_to_next_fy={$closing}" .
        " memo_opening={$memoOpening}" .
        " memo_movement={$memoMove}" .
        " memo_closing={$memoClosing}" .
        " action={$action}"
    );

    return $errors;
}
 
 public function update_fy_account_balance_bck(int $accId): array
{
    $errors   = [];
    $nextFYid = (int)$this->nextFYid();

    if ($nextFYid === 0) {
        SaveErrorLog("[FY MIGRATION] accId={$accId} | Next Financial Year does not exist");
        return ['Next Financial Year does not exist'];
    }

    $cmpId    = (int)$this->comp_id;
    $currFyId = (int)$this->session->get('ses_comp_fy_id');
    $hoboId   = (int)$this->bo_id;

    // Branch / HO name
    $hoboRow = $this->univaictly->table('hobomaster')
        ->select('hobo_name')
        ->where([
            'cmp_id'  => $cmpId,
            'hobo_id' => $hoboId
        ])
        ->get()->getRowArray();

    $hoboName = trim((string)($hoboRow['hobo_name'] ?? 'UNKNOWN'));

    $logPrefix = "[FY MIGRATION] cmp_id={$cmpId} hobo_id={$hoboId} hobo_name=\"{$hoboName}\" curr_fy={$currFyId} next_fy={$nextFYid} acc_id={$accId}";

    // Always derive FY dates from master
    $fyRow = $this->univaictly->table('cmpfymastr')
        ->select('fy_beg_date, fy_end_date')
        ->where([
            'cmp_id'        => $cmpId,
            'cmpfymastr_id' => $currFyId
        ])
        ->get()->getRowArray();

    if (!$fyRow) {
        SaveErrorLog("{$logPrefix} | Current FY master not found");
        return ['Current FY master not found'];
    }

    $dtStart = date('Y-m-d', strtotime($fyRow['fy_beg_date']));
    $dtEnd   = date('Y-m-d', strtotime($fyRow['fy_end_date']));

    // Account info
    $accRow = $this->db->table('acctmaster')
        ->select('acc_name, bsd_id')
        ->where([
            'cmp_id' => $cmpId,
            'acc_id' => $accId
        ])
        ->get()->getRowArray();

    if (!$accRow) {
        SaveErrorLog("{$logPrefix} | Account not found");
        return ["Account {$accId} not found"];
    }

    $accName      = trim((string)($accRow['acc_name'] ?? ''));
    $bsdId        = $accRow['bsd_id'] ?? null;
    $isBillSundry = !empty($bsdId);

    // Correct FY/type-safe mapping
    $parentRow = $this->db->table('undercrsmt')
        ->select('crs_mst_parent_id')
        ->where([
            'crs_mst_id'     => $accId,
            'cmp_id'         => $cmpId,
            'cmpfymastr_id'  => $currFyId,
            'crs_mst_type'   => $isBillSundry ? 14 : 1,
        ])
        ->get()->getRowArray();

    $parent_id = (int)($parentRow['crs_mst_parent_id'] ?? 0);

    // Skip restricted P&L parent if needed
    $restrictedParentIds = [6, 7, 8, 9,12, 13];
    if (in_array($parent_id, $restrictedParentIds, true)) {
        SaveErrorLog("{$logPrefix} acc_name=\"{$accName}\" | skipped due to restricted parent_id={$parent_id}");
        return $errors;
    }

    // Opening balance of current FY
    $opRow = $this->db->table('accoppybal')
        ->select('COALESCE(SUM(acc_op_bal),0) as op, COALESCE(SUM(acc_memo_bal),0) as memo', false)
        ->where([
            'cmp_id'        => $cmpId,
            'cmpfymastr_id' => $currFyId,
            'acc_id'        => $accId,
            'hobo_id'       => $hoboId
        ])
        ->get()->getRowArray();

    $opening     = (float)($opRow['op'] ?? 0);
    $memoOpening = (float)($opRow['memo'] ?? 0);

    // Normal movement within current FY
    $moveRow = $this->db->query("
        SELECT COALESCE(SUM(
            CASE at.acc_txn_dr_cr
                WHEN 1 THEN at.acc_txn_amt
                WHEN 2 THEN -at.acc_txn_amt
                ELSE 0
            END
        ),0) AS net_move
        FROM accttxnmst at
        INNER JOIN vchtxnconso vc
            ON vc.vch_txn_id = at.vch_txn_id
        WHERE at.cmp_id = ?
          AND at.acc_id = ?
          AND at.hobo_id = ?
          AND at.acc_txn_type = 1
          AND vc.hobo_id = ?
          AND vc.vch_date BETWEEN ? AND ?
    ", [$cmpId, $accId, $hoboId, $hoboId, $dtStart, $dtEnd])->getRowArray();

    $netMove = (float)($moveRow['net_move'] ?? 0);
    $closing = $opening + $netMove;

    // Memo movement only within current FY
    $memoMove = 0.0;
    $memoClosing = $memoOpening;

    if ($isBillSundry) {
        $memoMoveRow = $this->db->query("
            SELECT COALESCE(SUM(
                CASE at.acc_txn_dr_cr
                    WHEN 1 THEN at.acc_txn_amt
                    WHEN 2 THEN -at.acc_txn_amt
                    ELSE 0
                END
            ),0) AS net_move
            FROM accttxnmst at
            INNER JOIN vchtxnconso vc
                ON vc.vch_txn_id = at.vch_txn_id
            WHERE at.cmp_id = ?
              AND at.acc_id = ?
              AND at.hobo_id = ?
              AND at.acc_txn_type = 3
              AND vc.hobo_id = ?
              AND vc.vch_date BETWEEN ? AND ?
        ", [$cmpId, $accId, $hoboId, $hoboId, $dtStart, $dtEnd])->getRowArray();

        $memoMove = (float)($memoMoveRow['net_move'] ?? 0);
        $memoClosing += $memoMove;
    }

		
	$builder = $this->db->table('accoppybal')
				->where('cmp_id', $cmpId)
				->where('cmpfymastr_id', $nextFYid)
				->where('acc_id', $accId)
				->where('hobo_id', $hoboId);

			if ($bsdId === null) {
				$builder->where('bsd_id IS NULL', null, false);
			} else {
				$builder->where('bsd_id', $bsdId);
			}

			$exists = $builder->countAllResults();

	

    $data = [
        'cmp_id'        => $cmpId,
        'cmpfymastr_id' => $nextFYid,
        'acc_id'        => $accId,
        'hobo_id'       => $hoboId,
        'bsd_id'        => $bsdId,
        'acc_op_bal'    => $closing,
        'acc_py_bal'    => $closing,
        'acc_memo_bal'  => $memoClosing,
    ];

    $action = '';
    if ($exists > 0) {
        $this->db->table('accoppybal')
            ->where([
                'cmp_id'        => $cmpId,
                'cmpfymastr_id' => $nextFYid,
                'acc_id'        => $accId,
                'hobo_id'       => $hoboId,
            ])
            ->where($bsdId === null ? 'bsd_id IS NULL' : 'bsd_id = '.$this->db->escape($bsdId), null, false)
            ->update($data);

        $action = 'UPDATED';
    } else {
        $this->db->table('accoppybal')->insert($data);
        $action = 'INSERTED';
    }

    SaveErrorLog(
        "{$logPrefix} acc_name=\"{$accName}\" parent_id={$parent_id} is_bsd=" . ($isBillSundry ? '1' : '0') .
        " fy_range={$dtStart} to {$dtEnd}" .
        " opening={$opening}" .
        " movement={$netMove}" .
        " closing_to_next_fy={$closing}" .
        " memo_opening={$memoOpening}" .
        " memo_movement={$memoMove}" .
        " memo_closing={$memoClosing}" .
        " action={$action}"
    );

    return $errors;
}

	function update_fy_bill_sundry_balance($id)
	{
		$data = [];
		$errors = [];

		$nextFYid = $this->nextFYid();

  	if($nextFYid == 0){
  		$errors[] = 'Next Financial Year does not exists';
  		return $errors;
  	}

  	$UUIDtables = new UUIDtables($this->uuid,$this->comp_id,$this->fy_id);

		$billsundry_tbl = $this->comp_id.'_billsundry_'.$this->fy_id;
		$acctgroupn_tbl = $this->comp_id.'_acctgroupn_'.$this->fy_id;
		$hobomaster_tbl = $this->comp_id.'_hobomaster_'.$this->fy_id;
		$bsdoppybal_tbl = $this->comp_id.'_bsdoppybal_'.$this->fy_id;
	  

	  $builder = $this->db->table($billsundry_tbl);
		$builder->select('bill_sundry_id,acc_grp_id,acc_grp_parent_id');
		$builder->where('bill_sundry_id',$id);
		$result = $builder->get()->getResultArray();

		foreach ($result as $key => $value) {
	  		$bsd_id = $value['bill_sundry_id'];
	  		$fy_bsd_id = $UUIDtables->check_comp_fy_mst_map($bsd_id,'billsundry',$nextFYid);

	  		if($fy_bsd_id > 0){

		  		$parent_id = $value['acc_grp_parent_id'];
					
					if($value['acc_grp_id'] != 0){
						$acctgroupn = $this->db->table($acctgroupn_tbl)
																->select('acc_grp_parent_id')
																->where('acc_grp_id',$value['acc_grp_id'])
																->get()->getRowArray();
						if($acctgroupn){
							$parent_id = $acctgroupn['acc_grp_parent_id'];
						}
					}

					if(in_array($parent_id,[1,2,3,4,5]))
					{

						try{
							$sundrytxnn_tbl = $this->comp_id.'_sundrytxnn_'.$bsd_id.'_'.$this->fy_id;

							$result2 = $this->db->table($hobomaster_tbl)
										    					->select('bo_id,comp_id,bo_name')
										    					->where('comp_id', $this->comp_id)
										    					->get()->getResultArray();

							foreach ($result2 as $key2 => $value2) {

								$bo_id = $value2['bo_id'];
								$fy_bo_id = $UUIDtables->check_comp_fy_mst_map($bo_id,'hobomaster',$nextFYid);

								if($fy_bo_id > 0){

									$balance = 0;

									$builder = $this->db->table($sundrytxnn_tbl);
								  $builder->where('comp_id',$this->comp_id);
								  $builder->where('bill_sundry_id',$bsd_id);
								  $builder->where('bo_id',$bo_id);
								  $builder->orderBy('sundry_txn_date','DESC');	 
								  $builder->orderBy('voucher_txn_id','DESC');	 
					        $builder->orderBy('sundry_txn_id','DESC');	 		  
								  $builder->limit(1);
								  $sundrytxnn = $builder->get()->getRowArray();

								  if($sundrytxnn){
								  	$balance = floatval($sundrytxnn['sundry_bal']);
								  }
								  else{
								  	$bsdoppybal = $this->db->table($bsdoppybal_tbl)
																		->select('bo_id,bill_sundry_id,bsd_op_bal')
																		->where('bill_sundry_id', $bsd_id)
																		->where('bo_id', $bo_id)
																		->get()->getRowArray();
										if($bsdoppybal){
											$balance = floatval($bsdoppybal['bsd_op_bal']);
										}
								  }
									

									$fy_data = [
								  	'bill_sundry_id'			=> $fy_bsd_id,
								  	'bo_id'								=> $fy_bo_id,
								  	'bsd_py_bal'					=> $balance,
								  	'bsd_op_bal'					=> $balance
								  ];

								  $table = $this->comp_id.'_bsdoppybal_'.$nextFYid;

								  $exists = $this->db->table($table)
								  										->where('bill_sundry_id',$fy_bsd_id)
								  										->where('bo_id',$fy_bo_id)
								  										->get()->getRowArray();
								  if($exists){
								  	$this->db->table($table)
				  										->where('bill_sundry_id',$fy_bsd_id)
				  										->where('bo_id',$fy_bo_id)
				  										->update($fy_data);
								  }
								  else{
								  	$this->db->table($table)->insert($fy_data);
								  }

								}
								else{
									$errors[] = 'Branch- '.$value2['bo_name'].' does not exists in next financial year';
								}
							}
						}
						catch (\Exception $e) {
					   	$errors[] = 'Something went wrong';//$e->getMessage();
						}

					}
					else{
						$errors[] = 'Bill Sundry Opening Balance is restricted'; 
					}
				}
				else{
					$errors[] = 'Bill Sundry does not exists in next financial year';
				}
	  }

	  return $errors;						  
	}

//////////////////////////

public function update_fy_item_valuation(int $itemId): array
{
    $errors   = [];
    $nextFYid = $this->nextFYid();
    if ($nextFYid == 0) {
        $errors[] = 'Next Financial Year does not exist.';
        return $errors;
    }

    $cmpId     = (int) $this->comp_id;
    $currFyId  = (int) $this->session->get('ses_comp_fy_id');
    $dtStart   = date('Y-m-d', strtotime($this->session->get('ses_company_fy_beginning')));
    $dtEnd     = date('Y-m-d', strtotime($this->session->get('ses_company_fy_end')));
    $boId      = (int) $this->bo_id;

    $db = $this->db;
    $db->transStart();

    try {
        SaveErrorLog("ITEM VAL ROLLOVER START | item_id=$itemId | cmp_id=$cmpId | curr_fy=$currFyId | next_fy=$nextFYid" . PHP_EOL);

        // 1) Find all item-unit variants for this item
        $query = "
            SELECT DISTINCT itm_id_unit_id
            FROM itmoppyval
            WHERE cmp_id = ?
              AND cmpfymastr_id = ?
              AND hobo_id = ?
              AND CAST(split_part(itm_id_unit_id, '_', 1) AS INTEGER) = ?

            UNION

            SELECT DISTINCT it.itm_id_unit_id
            FROM itemtxnmst it
            JOIN vchtxnconso vc ON vc.vch_txn_id = it.vch_txn_id
            WHERE it.cmp_id = ?
              AND it.itm_txn_type = 1
              AND it.hobo_id = ?
              AND vc.vch_date >= ?
              AND vc.vch_date <= ?
              AND CAST(split_part(it.itm_id_unit_id, '_', 1) AS INTEGER) = ?

            ORDER BY itm_id_unit_id
        ";

        $result = $db->query($query, [
            $cmpId, $currFyId, $boId, $itemId,
            $cmpId, $boId, $dtStart, $dtEnd, $itemId
        ]);

        if (!$result) {
            $msg = 'Query failed: ' . $db->error()['message'];
            $errors[] = $msg;
            SaveErrorLog($msg . PHP_EOL);
            $db->transComplete();
            return $errors;
        }

        $unitRows = $result->getResultArray();
        SaveErrorLog("ITEM VAL ROLLOVER units found: " . json_encode($unitRows) . PHP_EOL);

        if (empty($unitRows)) {
            $db->transComplete();
            SaveErrorLog("ITEM VAL ROLLOVER no units for item_id=$itemId" . PHP_EOL);
            return $errors;
        }

        $unitList = array_column($unitRows, 'itm_id_unit_id');
        $defaultMethod = 'AVG';

        foreach ($unitList as $itmIdUnitId) {
            SaveErrorLog("UNIT VAL START | itm_id_unit_id=$itmIdUnitId" . PHP_EOL);

            // 2) Get opening QTY (sum across all centers — matches loadOpenings)
            $opQtyRow = $db->query("
                SELECT COALESCE(SUM(itm_op_bal_qty), 0) AS qty_sum
                FROM itmoppybal
                WHERE cmp_id = ? AND cmpfymastr_id = ? AND hobo_id = ? AND itm_id_unit_id = ?
            ", [$cmpId, $currFyId, $boId, $itmIdUnitId])->getRowArray();
            $opQty = (float)($opQtyRow['qty_sum'] ?? 0.0);

            // 3) Get opening VALUE (sum across all centers — matches loadOpenings)
            $opValRow = $db->query("
                SELECT COALESCE(SUM(itm_op_val_amt), 0) AS val_sum
                FROM itmoppyval
                WHERE cmp_id = ? AND cmpfymastr_id = ? AND hobo_id = ? AND itm_id_unit_id = ?
            ", [$cmpId, $currFyId, $boId, $itmIdUnitId])->getRowArray();
            $opVal = (float)($opValRow['val_sum'] ?? 0.0);

            $opRate = ($opQty > 0 && $opVal > 0) ? ($opVal / $opQty) : 0.0;

            // 4) Initialize running values (same as closingStockTotal)
            $running_qty   = $opQty;
            $running_value = $opVal;
            $running_avg   = $opRate;

            SaveErrorLog("UNIT VAL opening | $itmIdUnitId | opQty=$opQty | opVal=$opVal | opRate=$opRate" . PHP_EOL);

            // 5) Fetch transactions with voucher info (same as closingStockTotal)
            $txns = $db->table('itemtxnmst t')
                ->select('
                    t.itm_txn_date, t.itm_txn_dr_cr, t.itm_txn_qty,
                    t.itm_txn_rate, t.itm_txn_amt, t.itm_txn_id,
                    t.vch_txn_id, c.vch_type_id,
                    vt.vch_name as voucher_type
                ')
                ->join('vchtxnconso c', 'c.vch_txn_id = t.vch_txn_id', 'left')
                ->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left')
                ->where('t.cmp_id', $cmpId)
                ->where('t.hobo_id', $boId)
                ->where('t.itm_id_unit_id', $itmIdUnitId)
                ->where('t.itm_txn_date >=', $dtStart)
                ->where('t.itm_txn_date <=', $dtEnd)
                ->orderBy('t.itm_txn_date', 'ASC')
                ->orderBy('t.vch_txn_id', 'ASC')
                ->orderBy('t.itm_txn_id', 'ASC')
                ->get()
                ->getResultArray();

            // 6) Group by voucher (same as closingStockTotal)
            $voucher_groups = [];
            foreach ($txns as $t) {
                $vch_id = $t['vch_txn_id'];
                if (!isset($voucher_groups[$vch_id])) {
                    $voucher_groups[$vch_id] = [
                        'voucher_type' => $t['voucher_type'] ?? '',
                        'vch_type_id'  => $t['vch_type_id'] ?? '',
                        'in_qty' => 0, 'in_amt' => 0,
                        'out_qty' => 0, 'out_amt' => 0
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

            // 7) Process using Weighted Average (same as closingStockTotal)
            foreach ($voucher_groups as $vch_id => $vg) {
                $is_stock_journal = (strtolower($vg['voucher_type']) == 'stock journal' || $vg['vch_type_id'] == '20');

                if ($is_stock_journal && $vg['in_qty'] > 0 && $vg['out_qty'] > 0) {
                    $cogs = $vg['out_qty'] * $running_avg;
                    $running_qty -= $vg['out_qty'];
                    $running_value -= $cogs;
                    $running_qty += $vg['in_qty'];
                    $running_value += $vg['in_amt'];
                    if ($running_qty > 0) {
                        $running_avg = $running_value / $running_qty;
                    }
                } else {
                    if ($vg['out_qty'] > 0) {
                        $cogs = $vg['out_qty'] * $running_avg;
                        $running_qty -= $vg['out_qty'];
                        $running_value -= $cogs;
                    }
                    if ($vg['in_qty'] > 0) {
                        $new_total_value = $running_value + $vg['in_amt'];
                        $new_total_qty   = $running_qty + $vg['in_qty'];
                        if ($new_total_qty > 0) {
                            $running_avg = $new_total_value / $new_total_qty;
                        }
                        $running_qty   = $new_total_qty;
                        $running_value = $new_total_value;
                    }
                }
            }

            $closingVal = (float)$running_value;
            SaveErrorLog("UNIT VAL RESULT | $itmIdUnitId | closingVal=$closingVal | closingQty=$running_qty" . PHP_EOL);

            // 8) Get method from current FY
            $methodRow = $db->table('itmoppyval')
                ->select('itm_val_method_id')
                ->where('cmp_id', $cmpId)
                ->where('cmpfymastr_id', $currFyId)
                ->where('itm_id_unit_id', $itmIdUnitId)
                ->where('hobo_id', $boId)
                ->limit(1)
                ->get()
                ->getRowArray();
            $method = $methodRow['itm_val_method_id'] ?? $defaultMethod;

            // ✅ FIX: DELETE all existing rows for this item in next FY first
            //    This prevents duplicate rows across different mat_cent_ids
            $db->table('itmoppyval')
                ->where('cmp_id', $cmpId)
                ->where('cmpfymastr_id', $nextFYid)
                ->where('itm_id_unit_id', $itmIdUnitId)
                ->where('hobo_id', $boId)
                ->delete();

            SaveErrorLog("UNIT VAL DELETED old rows | $itmIdUnitId | next_fy=$nextFYid" . PHP_EOL);

            // ✅ FIX: INSERT single clean row with mat_cent_id = 0
            //    closingStockTotal uses SUM across all centers, so one row with total is correct
            $db->table('itmoppyval')->insert([
                'cmp_id'            => $cmpId,
                'cmpfymastr_id'     => $nextFYid,
                'itm_id_unit_id'    => $itmIdUnitId,
                'mat_cent_id'       => 0,
                'itm_op_val_amt'    => $closingVal,
                'itm_py_val_amt'    => $closingVal,
                'itm_val_method_id' => $method,
                'hobo_id'           => $boId
            ]);

            SaveErrorLog("UNIT VAL INSERT | $itmIdUnitId | center=0 | val=$closingVal | method=$method | next_fy=$nextFYid" . PHP_EOL);
            SaveErrorLog("UNIT VAL DONE | $itmIdUnitId" . PHP_EOL);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            $errors[] = 'DB transaction failed. Check logs.';
            SaveErrorLog("ITEM VAL ROLLOVER transaction failed for item_id=$itemId" . PHP_EOL);
        } else {
            SaveErrorLog("ITEM VAL ROLLOVER SUCCESS | item_id=$itemId | next_fy=$nextFYid" . PHP_EOL);
        }

    } catch (\Throwable $e) {
        $db->transRollback();
        $errors[] = 'Fatal Error: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ' on line ' . $e->getLine();
        SaveErrorLog('ITEM VAL ERROR: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL);
        \log_message('error', end($errors));
    }

    return $errors;
}

private function upsertItemValuation(
    $db,
    int $cmpId,
    int $fyId,
    string $itmIdUnitId,
    int $matCentId,
    float $opValAmt,
    float $pyValAmt,
    string $valMethod,
    int $hoboId
): bool {
    try {
        // Find if a record exists for this item-unit and center, regardless of the method.
        // This is more robust against unique key violations on (cmp_id, fy_id, item_id, center_id).
        $builder = $db->table('itmoppyval');
        $builder->select('itm_oppy_val_id ');
        $builder->where('cmp_id', $cmpId);
        $builder->where('cmpfymastr_id', $fyId);
        $builder->where('itm_id_unit_id', $itmIdUnitId);
        $builder->where('mat_cent_id', $matCentId);
        $query = $builder->get();

        if ($query === false) {
            $dbError = $db->error();
            throw new \Exception('Select failed in upsert: ' . ($dbError['message'] ?? 'Unknown DB error'));
        }
        $existing = $query->getRowArray();

        if ($existing) {
            // Record EXISTS for this center. UPDATE it.
            // This prevents trying to insert a duplicate for a different valuation method.
            $updateBuilder = $db->table('itmoppyval');
            // We update all values, including the valuation method.
            $updateData = [
                'itm_op_val_amt'    => $opValAmt,
                'itm_py_val_amt'    => $pyValAmt,
                'itm_val_method_id' => $valMethod,
                'hobo_id'           => $hoboId
            ];
            $updateBuilder->where('itm_oppy_val_id', $existing['itm_oppy_val_id']);
            
            if ($updateBuilder->update($updateData) === false) {
                $dbError = $db->error();
                throw new \Exception('Update failed: ' . ($dbError['message'] ?? 'Unknown DB error'));
            }
        } else {
            // Record does NOT exist for this center. INSERT a new one.
            $insertBuilder = $db->table('itmoppyval');
            $insertData = [
                'cmp_id'              => $cmpId,
                'cmpfymastr_id'       => $fyId,
                'itm_id_unit_id'      => $itmIdUnitId,
                'mat_cent_id'         => $matCentId,
                'itm_op_val_amt'      => $opValAmt,
                'itm_py_val_amt'      => $pyValAmt,
                'itm_val_method_id'   => $valMethod,
                'hobo_id'             => $hoboId
            ];
            if ($insertBuilder->insert($insertData) === false) {
                $dbError = $db->error();
                throw new \Exception('Insert failed: ' . ($dbError['message'] ?? 'Unknown DB error'));
            }
        }
        return true;

    } catch (\Throwable $e) {
        // This will log the true, underlying database error with file and line number.
        $errorMsg = 'Upsert valuation failed in ' . basename($e->getFile()) . ' on line ' . $e->getLine() . ': ' . $e->getMessage();
        \log_message('error', $errorMsg);
        // We re-throw the exception to make sure the main transaction fails.
        throw $e;
    }
}
////////////////////

public function update_fy_item_balances(int $itemId): array
{
    $errors   = [];
    $nextFYid = $this->nextFYid();
    if ($nextFYid == 0) {
        $errors[] = 'Next Financial Year does not exists';
        return $errors;
    }

    $cmpId     = (int) $this->comp_id;
    $currFyId  = (int) $this->session->get('ses_comp_fy_id');
    $dtStart   = date('Y-m-d', strtotime($this->session->get('ses_company_fy_beginning')));
    $dtEnd     = date('Y-m-d', strtotime($this->session->get('ses_company_fy_end')));
    $boId      = (int) $this->bo_id;

    $db = $this->db;
    $db->transStart();

    try {
        SaveErrorLog("ITEM BAL ROLLOVER START | item_id=$itemId | cmp_id=$cmpId | curr_fy=$currFyId | next_fy=$nextFYid" . PHP_EOL);

        // 0) Find ALL itm_id_unit_id variants for this item
        $query = "
            SELECT DISTINCT itm_id_unit_id
            FROM itmoppybal
            WHERE cmp_id = ?
              AND cmpfymastr_id = ?
              AND hobo_id = ?
              AND CAST(split_part(itm_id_unit_id, '_', 1) AS INTEGER) = ?

            UNION

            SELECT DISTINCT it.itm_id_unit_id
            FROM itemtxnmst it
            JOIN vchtxnconso vc ON vc.vch_txn_id = it.vch_txn_id
            WHERE it.cmp_id = ?
              AND it.itm_txn_type = 1
              AND it.hobo_id = ?
              AND vc.vch_date >= ?
              AND vc.vch_date <= ?
              AND CAST(split_part(it.itm_id_unit_id, '_', 1) AS INTEGER) = ?

            ORDER BY itm_id_unit_id
        ";

        $result = $db->query($query, [
            $cmpId, $currFyId, $boId, $itemId,
            $cmpId, $boId, $dtStart, $dtEnd, $itemId
        ]);

        if (!$result) {
            $msg = 'Query failed: ' . $db->error()['message'];
            $errors[] = $msg;
            SaveErrorLog($msg . PHP_EOL);
            $db->transComplete();
            return $errors;
        }

        $unitRows = $result->getResultArray();
        SaveErrorLog("ITEM BAL ROLLOVER units found: " . json_encode($unitRows) . PHP_EOL);

        if (empty($unitRows)) {
            $db->transComplete();
            SaveErrorLog("ITEM BAL ROLLOVER no units to process for item_id=$itemId" . PHP_EOL);
            return $errors;
        }

        $unitList = array_column($unitRows, 'itm_id_unit_id');

        foreach ($unitList as $itmIdUnitId) {
            SaveErrorLog("UNIT BAL START | itm_id_unit_id=$itmIdUnitId" . PHP_EOL);

            // 1) Opening qty by material center for CURRENT FY
            $openRows = $db->table('itmoppybal')
                ->select('mat_cent_id, itm_op_bal_qty')
                ->where('cmp_id', $cmpId)
                ->where('cmpfymastr_id', $currFyId)
                ->where('hobo_id', $boId)
                ->where('itm_id_unit_id', $itmIdUnitId)
                ->get()
                ->getResultArray();

            $openByCenter = [];
            foreach ($openRows as $r) {
                $openByCenter[(int)$r['mat_cent_id']] = (float)$r['itm_op_bal_qty'];
            }
            SaveErrorLog("UNIT BAL openings | itm_id_unit_id=$itmIdUnitId | " . json_encode($openByCenter) . PHP_EOL);

            // 2) Net movement by center using JOIN vchtxnconso for correct dates
            $mvQuery = "
                SELECT COALESCE(it.mat_cent_id, 0) AS mat_cent_id,
                       COALESCE(SUM(CASE
                           WHEN it.itm_txn_dr_cr = 1 THEN it.itm_txn_qty
                           WHEN it.itm_txn_dr_cr = 2 THEN -it.itm_txn_qty
                           ELSE 0
                       END), 0) AS net_qty
                FROM itemtxnmst it
                JOIN vchtxnconso vc ON vc.vch_txn_id = it.vch_txn_id
                WHERE it.cmp_id = ?
                  AND it.itm_id_unit_id = ?
                  AND it.hobo_id = ?
                  AND it.itm_txn_type = 1
                  AND vc.vch_date >= ?
                  AND vc.vch_date <= ?
                GROUP BY it.mat_cent_id
            ";

            $mvResult = $db->query($mvQuery, [$cmpId, $itmIdUnitId, $boId, $dtStart, $dtEnd]);

            if (!$mvResult) {
                $msg = 'Movement query failed: ' . $db->error()['message'];
                $errors[] = $msg;
                SaveErrorLog($msg . PHP_EOL);
                continue;
            }

            $mvRows = $mvResult->getResultArray();
            SaveErrorLog("UNIT BAL movements | itm_id_unit_id=$itmIdUnitId | " . json_encode($mvRows) . PHP_EOL);

            // Centers to touch = openings ∪ movements
            $centers = array_keys($openByCenter);
            foreach ($mvRows as $r) {
                $cid = (int)($r['mat_cent_id'] ?? 0);
                if (!in_array($cid, $centers, true)) {
                    $centers[] = $cid;
                }
            }

            // 3) Upsert NEXT FY openings
            foreach ($centers as $cid) {
                $opening = (float)($openByCenter[$cid] ?? 0.0);

                $net = 0.0;
                foreach ($mvRows as $r) {
                    if ((int)($r['mat_cent_id'] ?? 0) === $cid) {
                        $net = (float)($r['net_qty'] ?? 0.0);
                        break;
                    }
                }

                $closing = $opening + $net;

                $existingCount = $db->table('itmoppybal')
                    ->where('cmp_id', $cmpId)
                    ->where('cmpfymastr_id', $nextFYid)
                    ->where('itm_id_unit_id', $itmIdUnitId)
                    ->where('hobo_id', $boId)
                    ->where('mat_cent_id', $cid)
                    ->countAllResults();

                if ($existingCount > 0) {
                    $db->table('itmoppybal')
                        ->where('cmp_id', $cmpId)
                        ->where('cmpfymastr_id', $nextFYid)
                        ->where('itm_id_unit_id', $itmIdUnitId)
                        ->where('hobo_id', $boId)
                        ->where('mat_cent_id', $cid)
                        ->update([
                            'itm_op_bal_qty' => $closing,
                            'itm_py_bal_qty' => $closing,
                            'hobo_id'        => $boId
                        ]);
                    SaveErrorLog("UNIT BAL UPDATE | itm_id_unit_id=$itmIdUnitId | center=$cid | opening=$opening | net=$net | closing=$closing | next_fy=$nextFYid" . PHP_EOL);
                } else {
                    $db->table('itmoppybal')->insert([
                        'cmp_id'           => $cmpId,
                        'cmpfymastr_id'    => $nextFYid,
                        'itm_id_unit_id'   => $itmIdUnitId,
                        'mat_cent_id'      => $cid,
                        'itm_op_bal_qty'   => $closing,
                        'itm_py_bal_qty'   => $closing,
                        'hobo_id'          => $boId
                    ]);
                    SaveErrorLog("UNIT BAL INSERT | itm_id_unit_id=$itmIdUnitId | center=$cid | opening=$opening | net=$net | closing=$closing | next_fy=$nextFYid" . PHP_EOL);
                }
            }

            SaveErrorLog("UNIT BAL DONE | itm_id_unit_id=$itmIdUnitId" . PHP_EOL);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            $errors[] = 'DB transaction failed while updating item openings for next FY.';
            SaveErrorLog("ITEM BAL ROLLOVER transaction failed for item_id=$itemId" . PHP_EOL);
        } else {
            SaveErrorLog("ITEM BAL ROLLOVER SUCCESS | item_id=$itemId | next_fy=$nextFYid" . PHP_EOL);
        }

    } catch (\Throwable $e) {
        $db->transRollback();
        $errors[] = 'Error updating FY item balances: ' . $e->getMessage();
        SaveErrorLog('Error updating FY item balances: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine() . PHP_EOL);
        \log_message('error', 'FY Item Balance Update Error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
    }

    return $errors;
}

	function update_fy_cc_txn_balance($id)
	{
		$data = [];
		$errors = [];

		$nextFYid = $this->nextFYid();

  	if($nextFYid == 0){
  		$errors[] = 'Next Financial Year does not exists';
  		return $errors;
  	}

  	$UUIDtables = new UUIDtables($this->uuid,$this->comp_id,$this->fy_id);

		$costctmstr_tbl = $this->comp_id.'_costctmstr_'.$this->fy_id;
    $costctoppy_tbl = $this->comp_id.'_costctoppy_'.$this->fy_id;
    $costcttxnn_tbl = $this->comp_id.'_costcttxnn_'.$this->fy_id;
    $hobomaster_tbl = $this->comp_id.'_hobomaster_'.$this->fy_id;
	  

	  $builder = $this->db->table($costctmstr_tbl);
		$builder->select('cc_id,cc_grp_id');
		$builder->where('cc_id',$id);
		$result = $builder->get()->getRowArray();

		if($result){
	  		$cc_id = $id;
	  		$fy_cc_id = $UUIDtables->check_comp_fy_mst_map($cc_id,'costctmstr',$nextFYid);

	  		if($fy_cc_id > 0){

					try{
						
						$result2 = $this->db->table($hobomaster_tbl)
									    					->select('bo_id,bo_name')
									    					->get()->getResultArray();

						foreach ($result2 as $key2 => $value2) {

							$bo_id = $value2['bo_id'];
							$fy_bo_id = $UUIDtables->check_comp_fy_mst_map($bo_id,'hobomaster',$nextFYid);

							if($fy_bo_id > 0){

								$balance = 0;

								$builder = $this->db->table($costcttxnn_tbl);
					      $builder->select('cc_txn_bal');
					      $builder->where('cc_id', $cc_id);
					      $builder->where('bo_id', $bo_id);
					      $builder->orderBy('cc_txn_date', 'desc');
					      $builder->orderBy('voucher_txn_id', 'desc');
					      $builder->orderBy('cc_txn_id', 'desc');
					      $builder->limit(1);
					      $cc_txn = $builder->get()->getRowArray();
					      if($cc_txn)
					      {
					        $balance = floatval($cc_txn['cc_txn_bal']);
					      }
					      else{
					      	$costctoppy = $this->db->table($costctoppy_tbl)
				                          ->where('cc_id', $cc_id)
				                          ->where('bo_id', $bo_id)
				                          ->get()->getRowArray();
				          if($costctoppy){
				          	$balance = floatval($costctoppy['cc_op_bal']);
				          }               
					      }

								// code here
								$fy_data = [
								  	'cc_id'			=> $fy_cc_id,
								  	'bo_id'			=> $fy_bo_id,
								  	'cc_py_bal'	=> $balance,
								  	'cc_op_bal'	=> $balance
								  ];
							  $table = $this->comp_id.'_costctoppy_'.$nextFYid;

							  $exists = $this->db->table($table)
							  										->where('cc_id',$fy_cc_id)
							  										->where('bo_id',$fy_bo_id)
							  										->get()->getRowArray();
							  if($exists){
							  	$this->db->table($table)
			  										->where('cc_id',$fy_cc_id)
			  										->where('bo_id',$fy_bo_id)
			  										->update($fy_data);
							  }
							  else{
							  	$this->db->table($table)->insert($fy_data);
							  }
							}
							else{
								$errors[] = 'Branch- '.$value2['bo_name'].' does not exists in next financial year';
							}	
						}
					}
					catch (\Exception $e) {
				   	$errors[] = 'Something went wrong';//$e->getMessage();
					}
					
				}
				else{
					$errors[] = 'Account does not exists in next financial year';
				}
	  }

	  return $errors;						  
	}

	function update_fy_bbb_txn_balance($id)
	{
		$data = [];
		$errors = [];

		$nextFYid = $this->nextFYid();

  	if($nextFYid == 0){
  		$errors[] = 'Next Financial Year does not exists';
  		return $errors;
  	}

  	$UUIDtables = new UUIDtables($this->uuid,$this->comp_id,$this->fy_id);

		$billmaster_tbl = $this->comp_id.'_billmaster_'.$this->fy_id;
    $billstxnnn_tbl = $this->comp_id.'_billstxnnn_'.$this->fy_id;
    $billsoppyn_tbl = $this->comp_id.'_billsoppyn_'.$this->fy_id;
    $hobomaster_tbl = $this->comp_id.'_hobomaster_'.$this->fy_id;
	  

	  $builder = $this->db->table($billmaster_tbl);
		$builder->select('bills_ref_id');
		$builder->where('bills_ref_id',$id);
		$result = $builder->get()->getRowArray();

		if($result){
	  		$bills_ref_id = $id;
	  		$fy_bills_ref_id = $UUIDtables->check_comp_fy_mst_map($bills_ref_id,'billmaster',$nextFYid);

	  		if($fy_bills_ref_id > 0){

					try{
						
						$result2 = $this->db->table($hobomaster_tbl)
									    					->select('bo_id,bo_name')
									    					->get()->getResultArray();

						foreach ($result2 as $key2 => $value2) {

							$bo_id = $value2['bo_id'];
							$fy_bo_id = $UUIDtables->check_comp_fy_mst_map($bo_id,'hobomaster',$nextFYid);

							if($fy_bo_id > 0){

								$balance = 0;

								$builder = $this->db->table($billstxnnn_tbl); 
					      $builder->select('bills_txn_bal');
					      $builder->where('bills_ref_id', $bills_ref_id);
					      $builder->where('bo_id', $bo_id);
					      $builder->orderBy('bills_txn_date', 'desc');
					      $builder->orderBy('voucher_txn_id', 'desc');
					      $builder->orderBy('bills_txn_id', 'desc');
					      $builder->limit(1);
					      $bill_txn = $builder->get()->getRowArray();
					      
					      if($bill_txn){
					        $balance = floatval($bill_txn['bills_txn_bal']);
					      }
					      else{
					      	$billsoppyn = $this->db->table($billsoppyn_tbl)
				                          ->where('bills_ref_id', $bills_ref_id)
				                          ->where('bo_id', $bo_id)
				                          ->get()->getRowArray();
				          if($billsoppyn){
				          	$balance = floatval($billsoppyn['bills_op_bal']);
				          } 
					      }

								// code here
								$fy_data = [
								  	'bills_ref_id'			=> $fy_bills_ref_id,
								  	'bo_id'							=> $fy_bo_id,
								  	'bills_py_bal'			=> $balance,
								  	'bills_op_bal'			=> $balance
								  ];
							  $table = $this->comp_id.'_billsoppyn_'.$nextFYid;

							  $exists = $this->db->table($table)
				  										->where('bills_ref_id',$fy_bills_ref_id)
				  										->where('bo_id',$fy_bo_id)
				  										->get()->getRowArray();
							  if($exists){
							  	$this->db->table($table)
			  										->where('bills_ref_id',$fy_bills_ref_id)
			  										->where('bo_id',$fy_bo_id)
			  										->update($fy_data);
							  }
							  else{
							  	$this->db->table($table)->insert($fy_data);
							  }
							}
							else{
								$errors[] = 'Branch- '.$value2['bo_name'].' does not exists in next financial year';
							}	
						}
					}
					catch (\Exception $e) {
				   	$errors[] = 'Something went wrong';//$e->getMessage();
					}
					
				}
				else{
					$errors[] = 'Account does not exists in next financial year';
				}
	  }

	  return $errors;						  
	}

	function update_fy_project_txn_balance($id)
	{
		$data = [];
		$errors = [];

		$nextFYid = $this->nextFYid();

  	if($nextFYid == 0){
  		$errors[] = 'Next Financial Year does not exists';
  		return $errors;
  	}

  	$UUIDtables = new UUIDtables($this->uuid,$this->comp_id,$this->fy_id);

		$projectmst_tbl = $this->comp_id.'_projectmst_'.$this->fy_id;
    $prjoppybal_tbl = $this->comp_id.'_prjoppybal_'.$this->fy_id;
    $hobomaster_tbl = $this->comp_id.'_hobomaster_'.$this->fy_id;

    $prjliabtxn_tbl = $this->comp_id.'_prjliabtxn_'.$this->fy_id;
    $projasttxn_tbl = $this->comp_id.'_projasttxn_'.$this->fy_id;
    $projexptxn_tbl = $this->comp_id.'_projexptxn_'.$this->fy_id;
    $projrevtxn_tbl = $this->comp_id.'_projrevtxn_'.$this->fy_id;
	  

		$project_id = $id;
		$fy_project_id = $UUIDtables->check_comp_fy_mst_map($project_id,'projectmst',$nextFYid);

		if($fy_project_id > 0){

			try{
				$result2 = $this->db->table($hobomaster_tbl)
							    					->select('bo_id,bo_name')
							    					->get()->getResultArray();

				foreach ($result2 as $key2 => $value2) {

					$bo_id = $value2['bo_id'];
					$fy_bo_id = $UUIDtables->check_comp_fy_mst_map($bo_id,'hobomaster',$nextFYid);

					if($fy_bo_id > 0){

						$balance = 0;

						$builder = $this->db->table($prjliabtxn_tbl);
			      $builder->select('proj_txn_bal');
			      $builder->where('project_id', $project_id);
			      $builder->where('bo_id', $bo_id);
			      $builder->orderBy('proj_txn_date', 'desc');
			      $builder->orderBy('voucher_txn_id', 'desc');
			      $builder->orderBy('proj_txn_id', 'desc');
			      $builder->limit(1);
			      $proj_txn = $builder->get()->getRowArray();
			      if($proj_txn)
			      {
			        $balance = floatval($proj_txn['proj_txn_bal']);
			      }
			      else{
			      	$prjoppybal = $this->db->table($prjoppybal_tbl)
		                          ->where('project_id', $project_id)
		                          ->where('project_bal_type', 'lia')
		                          ->where('bo_id', $bo_id)
		                          ->get()->getRowArray();
		          if($prjoppybal){
		          	$balance = floatval($prjoppybal['project_op_bal']);
		          }               
			      }
			                    
			      $fy_data = [
		            'project_id'          => $fy_project_id,
		            'bo_id'           		=> $fy_bo_id,
		            'project_op_bal'   		=> $balance,
		            'project_py_bal'   		=> $balance,
		            'project_bal_type'		=> 'lia',
		        ];

					  $table = $this->comp_id.'_prjoppybal_'.$nextFYid;

					  $exists = $this->db->table($table)
					  										->where('project_id',$fy_project_id)
					  										->where('bo_id',$fy_bo_id)
					  										->where('project_bal_type','lia')
					  										->get()->getRowArray();
					  if($exists){
					  	$this->db->table($table)
	  										->where('project_id',$fy_project_id)
	  										->where('bo_id',$fy_bo_id)
	  										->where('project_bal_type','lia')
	  										->update($fy_data);
					  }
					  else{
					  	$this->db->table($table)->insert($fy_data);
					  }

					  //---
					  $balance = 0;

						$builder = $this->db->table($projasttxn_tbl);
			      $builder->select('proj_txn_bal');
			      $builder->where('project_id', $project_id);
			      $builder->where('bo_id', $bo_id);
			      $builder->orderBy('proj_txn_date', 'desc');
			      $builder->orderBy('voucher_txn_id', 'desc');
			      $builder->orderBy('proj_txn_id', 'desc');
			      $builder->limit(1);
			      $proj_txn = $builder->get()->getRowArray();
			      if($proj_txn)
			      {
			        $balance = floatval($proj_txn['proj_txn_bal']);
			      }
			      else{
			      	$prjoppybal = $this->db->table($prjoppybal_tbl)
		                          ->where('project_id', $project_id)
		                          ->where('project_bal_type', 'ast')
		                          ->where('bo_id', $bo_id)
		                          ->get()->getRowArray();
		          if($prjoppybal){
		          	$balance = floatval($prjoppybal['project_op_bal']);
		          }               
			      }
			                    
			      $fy_data = [
		            'project_id'          => $fy_project_id,
		            'bo_id'           		=> $fy_bo_id,
		            'project_op_bal'   		=> $balance,
		            'project_py_bal'   		=> $balance,
		            'project_bal_type'		=> 'ast',
		        ];

					  $table = $this->comp_id.'_prjoppybal_'.$nextFYid;

					  $exists = $this->db->table($table)
					  										->where('project_id',$fy_project_id)
					  										->where('bo_id',$fy_bo_id)
					  										->where('project_bal_type','ast')
					  										->get()->getRowArray();
					  if($exists){
					  	$this->db->table($table)
	  										->where('project_id',$fy_project_id)
	  										->where('bo_id',$fy_bo_id)
	  										->where('project_bal_type','ast')
	  										->update($fy_data);
					  }
					  else{
					  	$this->db->table($table)->insert($fy_data);
					  }

					  //---
					  $balance = 0;

						$builder = $this->db->table($projexptxn_tbl);
			      $builder->select('proj_txn_bal');
			      $builder->where('project_id', $project_id);
			      $builder->where('bo_id', $bo_id);
			      $builder->orderBy('proj_txn_date', 'desc');
			      $builder->orderBy('voucher_txn_id', 'desc');
			      $builder->orderBy('proj_txn_id', 'desc');
			      $builder->limit(1);
			      $proj_txn = $builder->get()->getRowArray();
			      if($proj_txn)
			      {
			        $balance = floatval($proj_txn['proj_txn_bal']);
			      }
			      else{
			      	$prjoppybal = $this->db->table($prjoppybal_tbl)
		                          ->where('project_id', $project_id)
		                          ->where('project_bal_type', 'exp')
		                          ->where('bo_id', $bo_id)
		                          ->get()->getRowArray();
		          if($prjoppybal){
		          	$balance = floatval($prjoppybal['project_op_bal']);
		          }               
			      }
			                    
			      $fy_data = [
		            'project_id'          => $fy_project_id,
		            'bo_id'           		=> $fy_bo_id,
		            'project_op_bal'   		=> $balance,
		            'project_py_bal'   		=> $balance,
		            'project_bal_type'		=> 'exp',
		        ];

					  $table = $this->comp_id.'_prjoppybal_'.$nextFYid;

					  $exists = $this->db->table($table)
					  										->where('project_id',$fy_project_id)
					  										->where('bo_id',$fy_bo_id)
					  										->where('project_bal_type','exp')
					  										->get()->getRowArray();
					  if($exists){
					  	$this->db->table($table)
	  										->where('project_id',$fy_project_id)
	  										->where('bo_id',$fy_bo_id)
	  										->where('project_bal_type','exp')
	  										->update($fy_data);
					  }
					  else{
					  	$this->db->table($table)->insert($fy_data);
					  }

					  //---
					  $balance = 0;

						$builder = $this->db->table($projrevtxn_tbl);
			      $builder->select('proj_txn_bal');
			      $builder->where('project_id', $project_id);
			      $builder->where('bo_id', $bo_id);
			      $builder->orderBy('proj_txn_date', 'desc');
			      $builder->orderBy('voucher_txn_id', 'desc');
			      $builder->orderBy('proj_txn_id', 'desc');
			      $builder->limit(1);
			      $proj_txn = $builder->get()->getRowArray();
			      if($proj_txn)
			      {
			        $balance = floatval($proj_txn['proj_txn_bal']);
			      }
			      else{
			      	$prjoppybal = $this->db->table($prjoppybal_tbl)
		                          ->where('project_id', $project_id)
		                          ->where('project_bal_type', 'rev')
		                          ->where('bo_id', $bo_id)
		                          ->get()->getRowArray();
		          if($prjoppybal){
		          	$balance = floatval($prjoppybal['project_op_bal']);
		          }               
			      }
			                    
			      $fy_data = [
		            'project_id'          => $fy_project_id,
		            'bo_id'           		=> $fy_bo_id,
		            'project_op_bal'   		=> $balance,
		            'project_py_bal'   		=> $balance,
		            'project_bal_type'		=> 'rev',
		        ];

					  $table = $this->comp_id.'_prjoppybal_'.$nextFYid;

					  $exists = $this->db->table($table)
					  										->where('project_id',$fy_project_id)
					  										->where('bo_id',$fy_bo_id)
					  										->where('project_bal_type','rev')
					  										->get()->getRowArray();
					  if($exists){
					  	$this->db->table($table)
	  										->where('project_id',$fy_project_id)
	  										->where('bo_id',$fy_bo_id)
	  										->where('project_bal_type','rev')
	  										->update($fy_data);
					  }
					  else{
					  	$this->db->table($table)->insert($fy_data);
					  }
					}
					else{
						$errors[] = 'Branch- '.$value2['bo_name'].' does not exists in next financial year';
					}	
				}
			}
			catch (\Exception $e) {
		   	$errors[] = 'Something went wrong';//$e->getMessage();
			}
			
		}
		else{
			$errors[] = 'Account does not exists in next financial year';
		}


	  return $errors;						  
	}
} 