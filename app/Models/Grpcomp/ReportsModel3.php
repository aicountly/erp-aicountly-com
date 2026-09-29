<?php
namespace App\Models\Grpcomp;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Grpcomp\TransactionModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class ReportsModel3 extends Model	{
	
	public function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		$this->db 			= $this->externaldb->grp_comp_db();
		$this->erp_db   =  $this->externaldb->erp_db();
		$this->aicountly_db   =  $this->externaldb->aicountly_db();
		$this->session       = \Config\Services::session();

		$this->TransactionModel  = new TransactionModel();
		$this->CommonModel   = new CommonModel();	   
		$this->enc_string    = new enc_string();
		$this->bo_id = 0;
	} 

		// Cash & Cash Equivalents		23
		// Bank OD/OCC A/c				21
		// Trade Receivables				22 (sundry debitors)
		// Trade Payable					16 (sundry creditors)

		// PURCHASE 				7 (parent_id)
		// DIRECT EXPENSE			11 (parent_id)
		// INDIRECT EXPENSE		13 (parent_id)

	function get_master_list($type)
	{
		$grpcomstid_tbl = 'grpcomstid';

		$result = $this->db->table($grpcomstid_tbl)
												->select('crs_master_id as id, crs_master_name as label, crs_master_name as value')
												->where('crs_master_type', $type)
												->get()->getResultArray();
		
		return $result;
	}

	function get_master_name($type,$crs_master_id)
	{
    $name = '';

    $grpcomstid_tbl = 'grpcomstid';
    $result = $this->db->table($grpcomstid_tbl)
												->where('crs_master_type', $type)
												->where('crs_master_id', $crs_master_id)
												->get()->getRowArray();
	
		if($result){
			$name = $result['crs_master_name'];
		}
		
		return $name;
	}

	function get_master_name_comp($type,$comp_id,$comp_fy_id,$master_id)
	{
		$comp_db = $this->externaldb->comp_db($comp_id);

		$array = [
      'hobomaster' => ['id' => 'bo_id', 'name' => 'bo_name'],
      'compcurrcy' => ['id' => 'comp_currency_id', 'name' => 'curr_name'],
      'cmpvchseri' => ['id' => 'comp_vch_series_id', 'name' => 'comp_vch_series'],

      'acctgroupn' => ['id' => 'acc_grp_id', 'name' => 'acc_grp_name'],
      'acctmaster' => ['id' => 'acc_id', 'name' => 'acc_name'],
      'billsundry' => ['id' => 'bill_sundry_id', 'name' => 'bill_sundry_name'],
      'projectgrp' => ['id' => 'project_grp_id', 'name' => 'project_grp_name'],
      'projectmst' => ['id' => 'project_id', 'name' => 'project_name'],

      'itemgrpmst' => ['id' => 'item_grp_id', 'name' => 'item_grp_name'],
      'itemcatmst' => ['id' => 'icatgms_id', 'name' => 'item_cat'],
      'itemmaster' => ['id' => 'item_id', 'name' => 'item_name'],
      'itmbatchmt' => ['id' => 'batch_id', 'name' => 'batch_no'],
      'itmunitmst' => ['id' => 'unit_id', 'name' => 'item_unit'],

      'mcgrpmstnn' => ['id' => 'mc_grp_id', 'name' => 'mc_grp_name'],
      'mcmasternn' => ['id' => 'mat_cent_id', 'name' => 'mat_cent_name'],
      'mcstoremst' => ['id' => 'mc_store_id', 'name' => 'mc_store_name'],
      'barcodemst' => ['id' => 'barcode_id', 'name' => 'barcode'],
      'labelmastr' => ['id' => 'label_id', 'name' => 'label_name'],

      'costctgrup' => ['id' => 'cc_grp_id', 'name' => 'cc_grp_name'],
      'costctmstr' => ['id' => 'cc_id', 'name' => 'cc_name'],
      'billmaster' => ['id' => 'bills_ref_id', 'name' => 'bills_ref_name'],
      'billofmatn' => ['id' => 'bom_id', 'name' => 'bom_name'],
      'prntconfig' => ['id' => 'prntconfig_id', 'name' => 'prntconfig_id'],
    ];

    $name = '';
		$table = $comp_id.'_'.$type.'_'.$comp_fy_id;
		$result = $comp_db->table($table)
											->where($array[$type]['id'],$master_id)
											// ->where('mst_base_id',$master_id)
											->get()->getRowArray();
		if($result){
			$name = $result[$array[$type]['name']];
		}
		
		return $name;
	}

	function get_master_id_comp($type,$comp_id,$comp_fy_id,$crs_master_id)
	{
		$comp_db = $this->externaldb->comp_db($comp_id);

		$array = [
      'hobomaster' => ['id' => 'bo_id', 'name' => 'bo_name'],
      'compcurrcy' => ['id' => 'comp_currency_id', 'name' => 'curr_name'],
      'cmpvchseri' => ['id' => 'comp_vch_series_id', 'name' => 'comp_vch_series'],

      'acctgroupn' => ['id' => 'acc_grp_id', 'name' => 'acc_grp_name'],
      'acctmaster' => ['id' => 'acc_id', 'name' => 'acc_name'],
      'billsundry' => ['id' => 'bill_sundry_id', 'name' => 'bill_sundry_name'],
      'projectgrp' => ['id' => 'project_grp_id', 'name' => 'project_grp_name'],
      'projectmst' => ['id' => 'project_id', 'name' => 'project_name'],

      'itemgrpmst' => ['id' => 'item_grp_id', 'name' => 'item_grp_name'],
      'itemcatmst' => ['id' => 'icatgms_id', 'name' => 'item_cat'],
      'itemmaster' => ['id' => 'item_id', 'name' => 'item_name'],
      'itmbatchmt' => ['id' => 'batch_id', 'name' => 'batch_no'],
      'itmunitmst' => ['id' => 'unit_id', 'name' => 'item_unit'],

      'mcgrpmstnn' => ['id' => 'mc_grp_id', 'name' => 'mc_grp_name'],
      'mcmasternn' => ['id' => 'mat_cent_id', 'name' => 'mat_cent_name'],
      'mcstoremst' => ['id' => 'mc_store_id', 'name' => 'mc_store_name'],
      'barcodemst' => ['id' => 'barcode_id', 'name' => 'barcode'],
      'labelmastr' => ['id' => 'label_id', 'name' => 'label_name'],

      'costctgrup' => ['id' => 'cc_grp_id', 'name' => 'cc_grp_name'],
      'costctmstr' => ['id' => 'cc_id', 'name' => 'cc_name'],
      'billmaster' => ['id' => 'bills_ref_id', 'name' => 'bills_ref_name'],
      'billofmatn' => ['id' => 'bom_id', 'name' => 'bom_name'],
      'prntconfig' => ['id' => 'prntconfig_id', 'name' => 'prntconfig_id'],
    ];

    $grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', $type)
												->where('comp_id', $comp_id)
												->where('crs_master_id', $crs_master_id)
												->get()->getRowArray();

		$id = 0;
		if($result){
			$table = $comp_id.'_'.$type.'_'.$comp_fy_id;
			$result2 = $comp_db->table($table)
												->where('mst_base_id',$result['master_id'])
												->get()->getRowArray();

			if($result2){
				$id = $result2[$array[$type]['id']];
			}
		}

		return $id;
	}

	function master_id_comp($type,$comp_id,$comp_fy_id,$master_id)
	{
		$comp_db = $this->externaldb->comp_db($comp_id);

		$array = [
      'hobomaster' => ['id' => 'bo_id', 'name' => 'bo_name'],
      'compcurrcy' => ['id' => 'comp_currency_id', 'name' => 'curr_name'],
      'cmpvchseri' => ['id' => 'comp_vch_series_id', 'name' => 'comp_vch_series'],

      'acctgroupn' => ['id' => 'acc_grp_id', 'name' => 'acc_grp_name'],
      'acctmaster' => ['id' => 'acc_id', 'name' => 'acc_name'],
      'billsundry' => ['id' => 'bill_sundry_id', 'name' => 'bill_sundry_name'],
      'projectgrp' => ['id' => 'project_grp_id', 'name' => 'project_grp_name'],
      'projectmst' => ['id' => 'project_id', 'name' => 'project_name'],

      'itemgrpmst' => ['id' => 'item_grp_id', 'name' => 'item_grp_name'],
      'itemcatmst' => ['id' => 'icatgms_id', 'name' => 'item_cat'],
      'itemmaster' => ['id' => 'item_id', 'name' => 'item_name'],
      'itmbatchmt' => ['id' => 'batch_id', 'name' => 'batch_no'],
      'itmunitmst' => ['id' => 'unit_id', 'name' => 'item_unit'],

      'mcgrpmstnn' => ['id' => 'mc_grp_id', 'name' => 'mc_grp_name'],
      'mcmasternn' => ['id' => 'mat_cent_id', 'name' => 'mat_cent_name'],
      'mcstoremst' => ['id' => 'mc_store_id', 'name' => 'mc_store_name'],
      'barcodemst' => ['id' => 'barcode_id', 'name' => 'barcode'],
      'labelmastr' => ['id' => 'label_id', 'name' => 'label_name'],

      'costctgrup' => ['id' => 'cc_grp_id', 'name' => 'cc_grp_name'],
      'costctmstr' => ['id' => 'cc_id', 'name' => 'cc_name'],
      'billmaster' => ['id' => 'bills_ref_id', 'name' => 'bills_ref_name'],
      'billofmatn' => ['id' => 'bom_id', 'name' => 'bom_name'],
      'prntconfig' => ['id' => 'prntconfig_id', 'name' => 'prntconfig_id'],
    ];

		$id = 0;
	
		$table = $comp_id.'_'.$type.'_'.$comp_fy_id;
		$result2 = $comp_db->table($table)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();

		if($result2){
			$id = $result2[$array[$type]['id']];
		}
	

		return $id;
	}



	function load_accounts_ledger_condensed($crs_master_id,$from_date,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'acctmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {
			$responses = $this->load_accounts_ledger_condensed_by_comp($value['comp_id'],$value['master_id'],$from_date,$to_date);
	
			foreach ($responses as $response) {
				$final[] = $response;
			}
		}

		usort($final, function($a, $b) {
		    return $a['voucher_date'] <=> $b['voucher_date'];
		});

		//opening_balance
		$balance = 0;

		$acc_opn_bal = $this->get_acc_opn_bal_list($crs_master_id,$from_date);
		if(count($acc_opn_bal)){
		 $balance = $acc_opn_bal[count($acc_opn_bal)-1]['balance_total'];
		}

		foreach ($final as $key => $value) {
			$balance += $value['debit'] - $value['credit'];
			if($balance >= 0)
				$balance_type = 'DR';
			else
				$balance_type = 'CR';

			$final[$key]['balance'] = abs($balance);
			$final[$key]['balance_type'] = $balance_type;
		}

		return $final;
	}

	function load_accounts_ledger_condensed_by_comp($comp_id,$master_id,$from_date,$to_date)
	{
		$comp_details = grp_comp_details($comp_id);
		$comp_name = $comp_details->comp_name;
		$comp_fy_id = $comp_details->comp_fy_id;

		if($comp_fy_id == 0){
			return [];
		}

		$account_id = $this->master_id_comp('acctmaster',$comp_id,$comp_fy_id,$master_id);

		if($account_id == 0){
			return [];
		}

		$comp_db = $this->externaldb->comp_db($comp_id);
	
		$voucher_tbl = $comp_id.'_vhtxnconso_'.$comp_fy_id;
		$voucher_type_tbl = $comp_id.'_cmpvchtype_'.$comp_fy_id;
		$voucher_series_tbl = $comp_id.'_cmpvchseri_'.$comp_fy_id;
		$comp_txn_tbl = $comp_id.'_comptxnmst_'.$comp_fy_id;
		$acc_txn_tbl = $comp_id.'_accnttxnnn_'.$account_id.'_'.$comp_fy_id;
		$long_narr_tbl = $comp_id.'_long_narrn_'.$comp_fy_id;

		$builder = $comp_db->table($voucher_tbl);
		$builder->select($voucher_tbl.'.*');
		$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
		$builder->select($voucher_type_tbl.'.comp_vch_type');
		$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
		$builder->select($voucher_series_tbl.'.comp_vch_series');
		$builder->join($comp_txn_tbl.' ctm1', 'ctm1.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm1.master_id_type = "acc"');
		$builder->select('ctm1.master_id, ctm1.master_id_type');
		$builder->join($acc_txn_tbl, $acc_txn_tbl.'.txn_id  = ctm1.txn_id');		
		$builder->select('acc_id, acc_txn_date, acc_txn_drcr, acc_txn_amount, acc_bal');
		$builder->join($comp_txn_tbl.' ctm2', 'ctm2.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.master_id_type = "nrr"', 'left');
		$builder->join($long_narr_tbl, $long_narr_tbl.'.vch_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.txn_id = '.$long_narr_tbl.'.txn_id', 'left');
		$builder->select('vch_narr');	
		$builder->where('acc_id', $account_id);				
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);

		 // $builder->where($acc_txn_tbl.'.bo_id', $this->bo_id);
		 // $builder->where($voucher_tbl.'.bo_id', $this->bo_id);
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('acc_txn_id'); 
		$result = $builder->get()->getResultArray();

		$final = [];

		foreach($result as $key => $value){

			$voucher_type_id = $value['voucher_type_id'];
			$voucher_type = $value['comp_vch_type'];
			$voucher_no = $value['comp_vch_no'];
			$voucher_date      = $value['voucher_date'];
			$account_name = '';
			$short_narration = '';

			$debit = 0;
    	$credit = 0;

    	$balance  = '';
    	$balance_type = '';

    	if($value['acc_txn_drcr']=='d'){
				$debit = floatval($value['acc_txn_amount']);
			}
			if($value['acc_txn_drcr']=='c'){
				$credit = floatval($value['acc_txn_amount']);
			}

			// $balance_type = '';	
			// if($value['acc_bal'] < 0){
			// 	$balance = floatval(abs($value['acc_bal']));
			//   $balance_type = 'CR';
			// }
			// if($value['acc_bal'] >= 0){
			// 	$balance = floatval($value['acc_bal']);
			//  	$balance_type = 'DR';
			// }

			$builder = $comp_db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $value['voucher_txn_id']);
    	$builder->where('master_id_type', 'acc');
    	$builder->where('master_id !=', $account_id);
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txns = $builder->get()->getRowArray();

    	if($comp_txns){
    		$account_name = $this->get_master_name_comp('acctmaster',$comp_id,$comp_fy_id,$comp_txns['master_id']);
    	}


			$final[]    = [
				'comp_id'							=> $comp_id,
				'comp_fy_id'					=> $comp_fy_id,
				'bo_id'								=> $value['bo_id'],
				'comp_name'						=> $comp_name,
				'voucher_no'					=> $value['comp_vch_no'],
				'voucher_txn_id'			=> $value['voucher_txn_id'],
				'comp_vch_series_id' 	=> $value['comp_vch_series_id'],
				'voucher_type_id'			=> $value['voucher_type_id'],
				'voucher_date'				=> $voucher_date,
				'voucher_type'				=> $value['comp_vch_type'],
				'account_name'				=> $account_name,
				'credit'							=> $credit,
				'debit'								=> $debit,
				'balance'							=> $balance,
				'balance_type'				=> $balance_type,
				'short_narration'   	=> $value['vch_narr'],
			];
	  }
	

		return $final;
	}

	function load_bill_sundry_condensed($crs_master_id,$from_date,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'billsundry')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {
			$responses = $this->load_bill_sundry_condensed_by_comp($value['comp_id'],$value['master_id'],$from_date,$to_date);
	
			foreach ($responses as $response) {
				$final[] = $response;
			}
		}

		//opeing_balance
		$balance = 0;

		$bsd_opn_bal = $this->get_bsd_opn_bal_list($crs_master_id,$from_date);
		if(count($bsd_opn_bal)){
		 $balance = $bsd_opn_bal[count($bsd_opn_bal)-1]['balance_total'];
		}

		foreach ($final as $key => $value) {
			$balance += $value['debit'] - $value['credit'];
			if($balance >= 0)
				$balance_type = 'DR';
			else
				$balance_type = 'CR';


			$final[$key]['balance'] = abs($balance);
			$final[$key]['balance_type'] = $balance_type;
				
		}

		return $final;
	}

	function load_bill_sundry_condensed_by_comp($comp_id,$master_id,$from_date,$to_date)
	{
		$comp_details = grp_comp_details($comp_id);
		$comp_name = $comp_details->comp_name;
		$comp_fy_id = $comp_details->comp_fy_id;

		if($comp_fy_id == 0){
			return [];
		}

		$account_id = $this->master_id_comp('billsundry',$comp_id,$comp_fy_id,$master_id);

		if($account_id == 0){
			return [];
		}

		$comp_db = $this->externaldb->comp_db($comp_id);
	
		$voucher_tbl = $comp_id.'_vhtxnconso_'.$comp_fy_id;
		$voucher_type_tbl = $comp_id.'_cmpvchtype_'.$comp_fy_id;
		$voucher_series_tbl = $comp_id.'_cmpvchseri_'.$comp_fy_id;
		$comp_txn_tbl = $comp_id.'_comptxnmst_'.$comp_fy_id;
		$sundrytxnn_tbl = $comp_id.'_sundrytxnn_'.$account_id.'_'.$comp_fy_id;

		$builder = $comp_db->table($voucher_tbl);
		$builder->select($voucher_tbl.'.*');
		$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
		$builder->select($voucher_type_tbl.'.comp_vch_type');
		$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
		$builder->select($voucher_series_tbl.'.comp_vch_series');
		$builder->join($comp_txn_tbl.' ctm1', 'ctm1.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm1.master_id_type = "bsd"');
		$builder->select('ctm1.master_id, ctm1.master_id_type');

		$builder->join($sundrytxnn_tbl, $sundrytxnn_tbl.'.txn_id  = ctm1.txn_id');		
		$builder->select('bill_sundry_id, sundry_txn_date, sundry_txn_drcr, sundry_txn_amount, sundry_bal');
	
		$builder->where('bill_sundry_id', $account_id);				
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);

		 // $builder->where($sundrytxnn_tbl.'.bo_id', $this->bo_id);
		 // $builder->where($voucher_tbl.'.bo_id', $this->bo_id);
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('sundry_txn_id'); 
		$result = $builder->get()->getResultArray();

		$final = [];

		foreach($result as $key => $value){

			$voucher_type_id = $value['voucher_type_id'];
			$voucher_type = $value['comp_vch_type'];
			$voucher_no = $value['comp_vch_no'];
			$voucher_date = $value['voucher_date'];

			$debit = 0;
    	$credit = 0;

    	$balance  = 0;
    	$balance_type = '';

    	if($value['sundry_txn_drcr']=='d'){
				$debit = floatval($value['sundry_txn_amount']);
			}
			if($value['sundry_txn_drcr']=='c'){
				$credit = floatval($value['sundry_txn_amount']);
			}

			$balance_type = '';	
			if($value['sundry_bal'] < 0){
				$balance = floatval(abs($value['sundry_bal']));
			  $balance_type = 'CR';
			}
			if($value['sundry_bal'] >= 0){
				$balance = floatval($value['sundry_bal']);
			 	$balance_type = 'DR';
			}

			$final[]    = [
				'comp_id'							=> $comp_id,
				'comp_fy_id'					=> $comp_fy_id,
				'bo_id'								=> $value['bo_id'],
				'comp_name'						=> $comp_name,
				'voucher_no'					=> $value['comp_vch_no'],
				'voucher_txn_id'			=> $value['voucher_txn_id'],
				'comp_vch_series_id' 	=> $value['comp_vch_series_id'],
				'voucher_type_id'			=> $value['voucher_type_id'],
				'voucher_date'				=> $voucher_date,
				'voucher_type'				=> $value['comp_vch_type'],
	
				'credit'							=> $credit,
				'debit'								=> $debit,
				'balance'							=> $balance,
				'balance_type'				=> $balance_type,

			];
	  }
	

		return $final;
	}

	function load_account_group_ledger($crs_master_id,$from_date,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'acctgroupn')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {
			$responses = $this->load_account_group_ledger_by_comp($value['comp_id'],$crs_master_id,$from_date,$to_date);
	
			foreach ($responses as $response) {
				$final[] = $response;
			}
		}

		return $final;
	}

	function load_account_group_ledger_by_comp($comp_id,$crs_master_id,$from_date,$to_date)
	{
		$comp_details = grp_comp_details($comp_id);
		$comp_name = $comp_details->comp_name;
		$comp_fy_id = $comp_details->comp_fy_id;

		if($comp_fy_id == 0){
			return [];
		}

		$account_id = $this->get_master_id_comp('acctgroupn',$comp_id,$comp_fy_id,$crs_master_id);

		if($account_id == 0){
			return [];
		}

		$comp_db = $this->externaldb->comp_db($comp_id);
	
		$voucher_tbl = $comp_id.'_vhtxnconso_'.$comp_fy_id;
		$voucher_type_tbl = $comp_id.'_cmpvchtype_'.$comp_fy_id;
		$voucher_series_tbl = $comp_id.'_cmpvchseri_'.$comp_fy_id;
		$comp_txn_tbl = $comp_id.'_comptxnmst_'.$comp_fy_id;
		$acctmaster_tbl = $comp_id.'_acctmaster_'.$comp_fy_id;

		$builder = $comp_db->table($voucher_tbl);
		$builder->select($voucher_tbl.'.*');
		$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
		$builder->select($voucher_type_tbl.'.comp_vch_type');
		$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
		$builder->select($voucher_series_tbl.'.comp_vch_series');
		$builder->join($comp_txn_tbl.' ctm1', 'ctm1.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm1.master_id_type = "acc"');
		$builder->select('ctm1.master_id, ctm1.master_id_type, ctm1.txn_id');

		$builder->join($acctmaster_tbl, 'ctm1.master_id = '.$acctmaster_tbl.'.acc_id');
		
		$builder->where($acctmaster_tbl.'.acc_grp_id', $account_id);				
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);

		 // $builder->where($acc_txn_tbl.'.bo_id', $this->bo_id);
		 // $builder->where($voucher_tbl.'.bo_id', $this->bo_id);
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	
		$result = $builder->get()->getResultArray();

		$final = [];

		foreach($result as $key => $value){

			$voucher_type_id = $value['voucher_type_id'];
			$voucher_type = $value['comp_vch_type'];
			$voucher_no = $value['comp_vch_no'];
			$voucher_date      = $value['voucher_date'];
			$account_name = '';



			$account = $comp_db->table($acctmaster_tbl)
													->where('acc_id', $value['master_id'])
													->get()->getRowArray();
			if($account){
				$account_name = $account['acc_name'];
			}


			$debit = 0;
    	$credit = 0;

    	$balance  = 0;
    	$balance_type = '';

			$acc_txn_tbl = $comp_id.'_accnttxnnn_'.$value['master_id'].'_'.$comp_fy_id;
			$accnttxnnn = $comp_db->table($acc_txn_tbl)
													->where('acc_id', $value['master_id'])
													->where('txn_id', $value['txn_id'])
													->get()->getRowArray();

			if($accnttxnnn){
				if($accnttxnnn['acc_txn_drcr']=='d'){
					$debit = floatval($accnttxnnn['acc_txn_amount']);
				}
				if($accnttxnnn['acc_txn_drcr']=='c'){
					$credit = floatval($accnttxnnn['acc_txn_amount']);
				}

				$balance_type = '';	
				if($accnttxnnn['acc_bal'] < 0){
					$balance = floatval(abs($accnttxnnn['acc_bal']));
				  $balance_type = 'CR';
				}
				if($accnttxnnn['acc_bal'] >= 0){
					$balance = floatval($accnttxnnn['acc_bal']);
				 	$balance_type = 'DR';
				}
			}
	

			$final[]    = [
				'comp_id'							=> $comp_id,
				'comp_name'						=> $comp_name,
				'voucher_no'					=> $value['comp_vch_no'],
				'voucher_txn_id'			=> $value['voucher_txn_id'],
				'comp_vch_series_id' 	=> $value['comp_vch_series_id'],
				'voucher_type_id'			=> $value['voucher_type_id'],
				'voucher_date'				=> $voucher_date,
				'voucher_type'				=> $value['comp_vch_type'],
				'account_name'				=> $account_name,
				'credit'							=> $credit,
				'debit'								=> $debit,
				'balance'							=> $balance,
				'balance_type'				=> $balance_type,
			];
	  }
	

		return $final;
	}

	function get_acc_opn_bal_crs($crs_master_id)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'acctmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();

		$balance = 0;
		foreach ($result as $key => $value) {
			$comp_id = $value['comp_id'];
			$company = grp_comp_details($value['comp_id'])->comp_name;;

			$balance += $this->get_acc_opn_balance($value['comp_id'],$value['master_id']);
		}

		return $balance;
	}

	function get_bsd_opn_bal_crs($crs_master_id)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'billsundry')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();

		$balance = 0;
		foreach ($result as $key => $value) {
			$comp_id = $value['comp_id'];
			$company = grp_comp_details($value['comp_id'])->comp_name;;

			$balance += $this->get_bsd_opn_balance($value['comp_id'],$value['master_id']);
		}

		return $balance;
	}

	function get_acc_opn_bal_list($crs_master_id,$from_date)
	{
		$grpmapping_tbl = 'grpmapping';

		$result_ = $this->db->table($grpmapping_tbl)
											->select('comp_id')
											->where('master_type','acctmaster')
											->where('crs_master_id',$crs_master_id)
											->groupBy('comp_id')
											->get()->getResultArray();

		$final = [];
		$total_balance = 0;
		foreach ($result_ as $key_ => $value_) {

			$comp_id = $value_['comp_id'];
			$company = grp_comp_details($comp_id)->comp_name;

			$result = $this->db->table($grpmapping_tbl)
										->where('master_type', 'acctmaster')
										->where('crs_master_id',$crs_master_id)
										->where('comp_id',$comp_id)
										->orderBy('crs_master_id', 'asc')
										->get()->getResultArray();

			$balance = 0;
			foreach ($result as $key => $value) {
				$balance += $this->get_acc_opn_bal($value['comp_id'],$value['master_id'],$from_date);
			}
			$total_balance += $balance;

			$balance_type = 'DR';
			if($balance < 0)
				$balance_type = 'CR';
			

			$final[] = [
				'company'				=> $company,
				'balance'				=> formatAmount(abs($balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $balance,
			];
		}

		$balance_type = 'DR';
		if($total_balance < 0)
			$balance_type = 'CR';
		
		$final[] = [
				'company'				=> 'Total',
				'balance'				=> formatAmount(abs($total_balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $total_balance,
			];

		return $final;
	}

	function get_acc_clo_bal_list($crs_master_id,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';

		$result_ = $this->db->table($grpmapping_tbl)
											->select('comp_id')
											->where('master_type','acctmaster')
											->where('crs_master_id',$crs_master_id)
											->groupBy('comp_id')
											->get()->getResultArray();

		$final = [];
		$total_balance = 0;
		foreach ($result_ as $key_ => $value_) {

			$comp_id = $value_['comp_id'];
			$company = grp_comp_details($comp_id)->comp_name;

			$result = $this->db->table($grpmapping_tbl)
										->where('master_type', 'acctmaster')
										->where('crs_master_id',$crs_master_id)
										->where('comp_id',$comp_id)
										->orderBy('crs_master_id', 'asc')
										->get()->getResultArray();

			$balance = 0;
			foreach ($result as $key => $value) {
				$balance += $this->get_acc_clo_bal($value['comp_id'],$value['master_id'],$to_date);
			}
			$total_balance += $balance;

			$balance_type = 'DR';
			if($balance < 0)
				$balance_type = 'CR';
			

			$final[] = [
				'company'				=> $company,
				'balance'				=> formatAmount(abs($balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $balance,
			];
		}

		$balance_type = 'DR';
		if($total_balance < 0)
			$balance_type = 'CR';
		
		$final[] = [
				'company'				=> 'Total',
				'balance'				=> formatAmount(abs($total_balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $total_balance,
			];

		return $final;
	}

	function get_acc_opn_balance($comp_id,$master_id)
	{
		$balance = 0;

		$comp_db = $this->externaldb->comp_db($comp_id);
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

		$table = $comp_id.'_acctmaster_'.$comp_fy_id;
		$acctmaster = $comp_db->table($table)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();

		if($acctmaster){
			$acc_id = $acctmaster['acc_id'];
		}
		else{
			return 0;
		}

		$accoppybal_tbl =$comp_id.'_accoppybal_'.$comp_fy_id;

		$bo_array = [];
  	$result = $comp_db->table($accoppybal_tbl)
									    	->select('bo_id')
									    	->where('acc_id', $acc_id)
									    	->groupBy('bo_id')
									    	->get()->getResultArray();

		foreach ($result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

		foreach ($bo_array as $bo_id) {

			$accoppybal = $comp_db->table($accoppybal_tbl)
								    	->where('acc_id', $acc_id)
								    	->where('bo_id',$bo_id)
								    	->get()->getRowArray();
			if($accoppybal){
				$balance += floatval($accoppybal['acc_op_bal']);
			}
		}

		return $balance;
	}

	function get_acc_opn_bal($comp_id,$master_id,$from_date)
	{
		$balance = 0;

		$comp_db = $this->externaldb->comp_db($comp_id);
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

		$table = $comp_id.'_acctmaster_'.$comp_fy_id;
		$acctmaster = $comp_db->table($table)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();

		if($acctmaster){
			$acc_id = $acctmaster['acc_id'];
		}
		else{
			return 0;
		}

		$accoppybal_tbl =$comp_id.'_accoppybal_'.$comp_fy_id;
		$accnttxnnn_tbl =$comp_id.'_accnttxnnn_'.$acc_id.'_'.$comp_fy_id;

		$bo_array = [];
  	$result = $comp_db->table($accoppybal_tbl)
									    	->select('bo_id')
									    	->where('acc_id', $acc_id)
									    	->groupBy('bo_id')
									    	->get()->getResultArray();

		foreach ($result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

		foreach ($bo_array as $bo_id) {

			$builder = $comp_db->table($accnttxnnn_tbl);
			$builder->where('acc_id', $acc_id);
			$builder->where('bo_id', $bo_id);
			$builder->where('acc_txn_date <', $from_date);
			$builder->orderBy('acc_txn_date', 'desc');
			$builder->orderBy('voucher_txn_id', 'desc');
			$builder->orderBy('acc_txn_id', 'desc');
			$builder->limit(1);
			$transaction = $builder->get()->getRowArray();

			if($transaction){
				$balance += floatval($transaction['acc_bal']);
			}
			else{
				$accoppybal = $comp_db->table($accoppybal_tbl)
									    	->where('acc_id', $acc_id)
									    	->where('bo_id',$bo_id)
									    	->get()->getRowArray();
				if($accoppybal){
					$balance += floatval($accoppybal['acc_op_bal']);
				}
			}
		}

		return $balance;
	}

	function get_acc_clo_bal($comp_id,$master_id,$to_date)
	{
		$balance = 0;

		$comp_db = $this->externaldb->comp_db($comp_id);
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

		$table = $comp_id.'_acctmaster_'.$comp_fy_id;
		$acctmaster = $comp_db->table($table)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();

		if($acctmaster){
			$acc_id = $acctmaster['acc_id'];
		}
		else{
			return 0;
		}

		$accoppybal_tbl =$comp_id.'_accoppybal_'.$comp_fy_id;
		$accnttxnnn_tbl =$comp_id.'_accnttxnnn_'.$acc_id.'_'.$comp_fy_id;

		$bo_array = [];
  	$result = $comp_db->table($accoppybal_tbl)
									    	->select('bo_id')
									    	->where('acc_id', $acc_id)
									    	->groupBy('bo_id')
									    	->get()->getResultArray();

		foreach ($result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

		foreach ($bo_array as $bo_id) {

			$builder = $comp_db->table($accnttxnnn_tbl);
			$builder->where('acc_id', $acc_id);
			$builder->where('bo_id', $bo_id);
			$builder->where('acc_txn_date <=', $to_date);
			$builder->orderBy('acc_txn_date', 'desc');
			$builder->orderBy('voucher_txn_id', 'desc');
			$builder->orderBy('acc_txn_id', 'desc');
			$builder->limit(1);
			$transaction = $builder->get()->getRowArray();

			if($transaction){
				$balance += floatval($transaction['acc_bal']);
			}
			else{
				$accoppybal = $comp_db->table($accoppybal_tbl)
									    	->where('acc_id', $acc_id)
									    	->where('bo_id',$bo_id)
									    	->get()->getRowArray();
				if($accoppybal){
					$balance += floatval($accoppybal['acc_op_bal']);
				}
			}
		}

		return $balance;
	}

	function get_bsd_opn_bal_list($crs_master_id,$from_date)
	{
		$grpmapping_tbl = 'grpmapping';

		$result_ = $this->db->table($grpmapping_tbl)
											->select('comp_id')
											->where('master_type','billsundry')
											->where('crs_master_id',$crs_master_id)
											->groupBy('comp_id')
											->get()->getResultArray();

		$final = [];
		$total_balance = 0;
		foreach ($result_ as $key_ => $value_) {

			$comp_id = $value_['comp_id'];
			$company = grp_comp_details($comp_id)->comp_name;

			$result = $this->db->table($grpmapping_tbl)
										->where('master_type', 'billsundry')
										->where('crs_master_id',$crs_master_id)
										->where('comp_id',$comp_id)
										->orderBy('crs_master_id', 'asc')
										->get()->getResultArray();

			$balance = 0;
			foreach ($result as $key => $value) {
				$balance += $this->get_bsd_opn_bal($value['comp_id'],$value['master_id'],$from_date);
			}
			$total_balance += $balance;

			$balance_type = 'DR';
			if($balance < 0)
				$balance_type = 'CR';
			

			$final[] = [
				'company'				=> $company,
				'balance'				=> formatAmount(abs($balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $balance,
			];
		}

		$balance_type = 'DR';
		if($total_balance < 0)
			$balance_type = 'CR';
		
		$final[] = [
				'company'				=> 'Total',
				'balance'				=> formatAmount(abs($total_balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $total_balance,
			];

		return $final;
	}

	function get_bsd_clo_bal_list($crs_master_id,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';

		$result_ = $this->db->table($grpmapping_tbl)
											->select('comp_id')
											->where('master_type','billsundry')
											->where('crs_master_id',$crs_master_id)
											->groupBy('comp_id')
											->get()->getResultArray();

		$final = [];
		$total_balance = 0;
		foreach ($result_ as $key_ => $value_) {

			$comp_id = $value_['comp_id'];
			$company = grp_comp_details($comp_id)->comp_name;

			$result = $this->db->table($grpmapping_tbl)
										->where('master_type', 'billsundry')
										->where('crs_master_id',$crs_master_id)
										->where('comp_id',$comp_id)
										->orderBy('crs_master_id', 'asc')
										->get()->getResultArray();

			$balance = 0;
			foreach ($result as $key => $value) {
				$balance += $this->get_bsd_clo_bal($value['comp_id'],$value['master_id'],$to_date);
			}
			$total_balance += $balance;

			$balance_type = 'DR';
			if($balance < 0)
				$balance_type = 'CR';
			

			$final[] = [
				'company'				=> $company,
				'balance'				=> formatAmount(abs($balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $balance,
			];
		}

		$balance_type = 'DR';
		if($total_balance < 0)
			$balance_type = 'CR';
		
		$final[] = [
				'company'				=> 'Total',
				'balance'				=> formatAmount(abs($total_balance)),
				'balance_type'	=> $balance_type,
				'balance_total'	=> $total_balance,
			];

		return $final;
	}

	function get_bsd_opn_balance($comp_id,$master_id)
	{
		$balance = 0;

		$comp_db = $this->externaldb->comp_db($comp_id);
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

		$table = $comp_id.'_billsundry_'.$comp_fy_id;
		$billsundry = $comp_db->table($table)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();

		if($billsundry){
			$bsd_id = $billsundry['bill_sundry_id'];
		}
		else{
			return 0;
		}

		$bsdoppybal_tbl =$comp_id.'_bsdoppybal_'.$comp_fy_id;

		$bo_array = [];
  	$result = $comp_db->table($bsdoppybal_tbl)
									    	->select('bo_id')
									    	->where('bill_sundry_id', $bsd_id)
									    	->groupBy('bo_id')
									    	->get()->getResultArray();

		foreach ($result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

		foreach ($bo_array as $bo_id) {

			$bsdoppybal = $comp_db->table($bsdoppybal_tbl)
								    	->where('bill_sundry_id', $bsd_id)
								    	->where('bo_id',$bo_id)
								    	->get()->getRowArray();
			if($bsdoppybal){
				$balance += floatval($bsdoppybal['bsd_op_bal']);
			}
		}

		return $balance;
	}

	function get_bsd_opn_bal($comp_id,$master_id,$from_date)
	{
		$balance = 0;

		$comp_db = $this->externaldb->comp_db($comp_id);
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

		$table = $comp_id.'_billsundry_'.$comp_fy_id;
		$billsundry = $comp_db->table($table)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();

		if($billsundry){
			$bsd_id = $billsundry['bill_sundry_id'];
		}
		else{
			return 0;
		}

		$bsdoppybal_tbl =$comp_id.'_bsdoppybal_'.$comp_fy_id;
		$sundrytxnn_tbl =$comp_id.'_sundrytxnn_'.$bsd_id.'_'.$comp_fy_id;

		$bo_array = [];
  	$result = $comp_db->table($bsdoppybal_tbl)
									    	->select('bo_id')
									    	->where('bill_sundry_id', $bsd_id)
									    	->groupBy('bo_id')
									    	->get()->getResultArray();

		foreach ($result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

		foreach ($bo_array as $bo_id) {

			$builder = $comp_db->table($sundrytxnn_tbl);
			$builder->where('bill_sundry_id', $bsd_id);
			$builder->where('bo_id', $bo_id);
			$builder->where('sundry_txn_date <', $from_date);
			$builder->orderBy('sundry_txn_date', 'desc');
			$builder->orderBy('voucher_txn_id', 'desc');
			$builder->orderBy('sundry_txn_id', 'desc');
			$builder->limit(1);
			$transaction = $builder->get()->getRowArray();

			if($transaction){
				$balance += floatval($transaction['sundry_bal']);
			}
			else{
				$bsdoppybal = $comp_db->table($bsdoppybal_tbl)
									    	->where('bill_sundry_id', $bsd_id)
									    	->where('bo_id',$bo_id)
									    	->get()->getRowArray();
				if($bsdoppybal){
					$balance += floatval($bsdoppybal['bsd_op_bal']);
				}
			}
		}

		return $balance;
	}

	function get_bsd_clo_bal($comp_id,$master_id,$to_date)
	{
		$balance = 0;

		$comp_db = $this->externaldb->comp_db($comp_id);
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

		$table = $comp_id.'_billsundry_'.$comp_fy_id;
		$billsundry = $comp_db->table($table)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();

		if($billsundry){
			$bsd_id = $billsundry['bill_sundry_id'];
		}
		else{
			return 0;
		}

		$bsdoppybal_tbl =$comp_id.'_bsdoppybal_'.$comp_fy_id;
		$sundrytxnn_tbl =$comp_id.'_sundrytxnn_'.$bsd_id.'_'.$comp_fy_id;

		$bo_array = [];
  	$result = $comp_db->table($bsdoppybal_tbl)
									    	->select('bo_id')
									    	->where('bill_sundry_id', $bsd_id)
									    	->groupBy('bo_id')
									    	->get()->getResultArray();

		foreach ($result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

		foreach ($bo_array as $bo_id) {

			$builder = $comp_db->table($sundrytxnn_tbl);
			$builder->where('bill_sundry_id', $bsd_id);
			$builder->where('bo_id', $bo_id);
			$builder->where('sundry_txn_date <=', $to_date);
			$builder->orderBy('sundry_txn_date', 'desc');
			$builder->orderBy('voucher_txn_id', 'desc');
			$builder->orderBy('sundry_txn_id', 'desc');
			$builder->limit(1);
			$transaction = $builder->get()->getRowArray();

			if($transaction){
				$balance += floatval($transaction['sundry_bal']);
			}
			else{
				$bsdoppybal = $comp_db->table($bsdoppybal_tbl)
									    	->where('bill_sundry_id', $bsd_id)
									    	->where('bo_id',$bo_id)
									    	->get()->getRowArray();
				if($bsdoppybal){
					$balance += floatval($bsdoppybal['bsd_op_bal']);
				}
			}
		}

		return $balance;
	}

	function get_all_group_parents()
	{
		$acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
  	$result =  $this->erp_db->table($acc_grp_par_tbl)
									->select('acc_grp_parent_id as id, grp_name as label, grp_name as value')
								  ->where('acc_grp_parent_id !=', 14)
								  ->get()->getResultArray();

		return $result;
	}

	function load_balance_sheet($view,$from_date,$to_date,$nil_type)
	{
		// OWNER'S FUND	 1							NON CURRENT ASSETS 3	
		// NON CURRENT LIABILITIES 	2				CURRENT ASSETS 5
		// CURRENT LIABILITIES	4					DIFF. IN OP. BALANCE


		$array1 = [];
		$array2 = [];	

		$data = $this->get_parent_group_details(1, $view,$from_date,$to_date); // OWNER'S FUND
		if($data){
			foreach ($data as $key => $value) {
				if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
				{
					$array1[] = [
						'l_group_id'	   => $value['group_id'],
						'l_group_name'    => $value['group_name'],
						'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
						'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
						'l_type'		      => $value['type'],
						'l_style'		   => $value['style'],
					];
				}
			}
		}

  	$data = $this->get_pl_details($from_date,$to_date); // PROFIT & LOSS

		$array1[] = [
			'l_group_id'	  	=> 0,
			'l_group_name'    => 'Profit/ Loss',
			'l_balance'		   	=> 	formatAmount($data),
			'l_balance_total' => $data,
			'l_type'		  		=> 'pnl',
			'l_style'		   		=> 'font-weight:bold;',
		];
  

  	$data = $this->get_parent_group_details(2, $view,$from_date,$to_date); // NON CURRENT LIABILITIES
  	if($data){
  		foreach ($data as $key => $value) {
  			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
  			{
  				$array1[] = [
  					'l_group_id'	   => $value['group_id'],
  					'l_group_name'    => $value['group_name'],
  					'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
  					'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
  					'l_type'		      => $value['type'],
  					'l_style'		   => $value['style'],
  				];
  			}
  		}
  	}
  	$data = $this->get_parent_group_details(4, $view,$from_date,$to_date); // CURRENT LIABILITIES
  	if($data){
  		foreach ($data as $key => $value) {
  			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
  			{
  				$array1[] = [
  					'l_group_id'	   => $value['group_id'],
  					'l_group_name'    => $value['group_name'],
  					'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
  					'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
  					'l_type'		      => $value['type'],
  					'l_style'		   => $value['style'],
  				];
  			}
  		}
  	}

  	

  	//-----------------------------------------------------

  	$data = $this->get_parent_group_details(3, $view,$from_date,$to_date); // NON CURRENT ASSETS
  	if($data){
  		foreach ($data as $key => $value) {
  			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
  			{
  				$array2[] = [
  					'r_group_id'	  	=> $value['group_id'],
  					'r_group_name'    => $value['group_name'],
  					'r_balance'		  	=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
  					'r_balance_total'	=> $value['balance'] != '' ? $value['balance'] : '',
  					'r_type'		  		=> $value['type'],
  					'r_style'		   => $value['style'],
  				];
  			}
  		}
  	}
  	$data = $this->get_parent_group_details(5, $view,$from_date,$to_date); // CURRENT ASSETS 
  	if($data){
  		foreach ($data as $key => $value) {
  			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
  			{
  				$array2[] = [
  					'r_group_id'	  	=> $value['group_id'],
  					'r_group_name'    => $value['group_name'],
  					'r_balance'		  	=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
  					'r_balance_total'	=> $value['balance'] != '' ? $value['balance'] : '',
  					'r_type'		  		=> $value['type'],
  					'r_style'		   => $value['style'],
  				];
  			}
  		}
  	}

  	$data = $this->get_clo_stk($to_date);
  	if($view == 1 || $view == 2 || $view == 3){
  		$array2[] = [
  			'r_group_id'	  	=> 0,
  			'r_group_name'    => '&nbsp;&nbsp; &raquo; Inventories',
  			'r_balance'		  	=> $data != '' ? formatAmount($data) : '',
  			'r_balance_total'	=> $data != '' ? $data : '',
  			'r_type'		  		=> 'stk',
  			'r_style'		   => $view == 2 ? 'font-weight: 500;' : '',
  		];
  	}
  	
  	if($view == 0){
  		$r_balance_total = $array2[count($array2)-1]['r_balance_total'];
  		$r_balance_total += $data;

  		$array2[count($array2)-1]['r_balance'] = $r_balance_total > 0 ? formatAmount($r_balance_total) : '';
  		$array2[count($array2)-1]['r_balance_total'] = $r_balance_total;
  	}

  	//,6,7,8,9,10,11,12,13
  	$data = $this->get_parent_op_bal_diff(); // DIFF. IN OP. BALANCE
  	if($data){
  		$array2[] = [
  			'r_group_id'	  	=> 0,
  			'r_group_name'    => 'Difference in Openning',
  			'r_balance'		  	=> formatAmount($data),
  			'r_balance_total'	=> $data,
  			'r_type'		  		=> 'opn',
  			'r_style'		  		=> 'font-weight: bold;',
  		];
  	}

  	$l_total = array_sum(array_column($array1, 'l_balance_total'));
  	$r_total = array_sum(array_column($array2, 'r_balance_total'));

  	$final = []; 

  	$count = count($array1) > count($array2) ? count($array1) : count($array2);
  	for($i=0; $i<$count; $i++)
  	{
  		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
  		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

  		if(count($temp1) || count($temp2)){
  			$temp3 = array_merge($temp1,$temp2);
  			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
  			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
  			
  			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
  			$final[] = $temp3;
  		}

  		
  	}

  	$final[] = [
  		'l_group_id'	  => 0,
  		'l_group_name'    => '',
  		'l_balance'		  => formatAmount($l_total),
  		'r_group_id'	  => 0,
  		'r_group_name'    => '',
  		'r_balance'		  => formatAmount($r_total),
  		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
  	];
  	// echo "<pre>";print_r($final);exit;
  	return $final;
  }

  function get_parent_group_details_comp($crs_master_id,$from_date,$to_date,$type,$pnl=false)
  {
  	$balance = 0;
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('crs_master_id', $crs_master_id)
												->where('master_type', 'acctmaster')
												->get()->getResultArray();
		foreach ($result as $key => $value) {

			if($type == 'acctmaster'){
				$balance += $this->get_acc_clo_bal($value['comp_id'],$value['master_id'],$to_date);
				if($pnl){
					$balance -= $this->get_acc_opn_bal($value['comp_id'],$value['master_id'],$from_date);
				}
			}

			if($type == 'billsundry'){
				$balance += $this->get_bsd_clo_bal($value['comp_id'],$value['master_id'],$to_date);
				if($pnl){
					$balance -= $this->get_bsd_opn_bal($value['comp_id'],$value['master_id'],$from_date);
				}
			}
				
		}
		return $balance;
  }

  function get_parent_group_details($acc_grp_parent_id, $view, $from_date, $to_date, $pnl = false)
  {
  	$final = [];

  	$acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
  	$parent =  $this->erp_db->table($acc_grp_par_tbl)
									  ->where('acc_grp_parent_id', $acc_grp_parent_id)
									  ->get()->getRowArray();

  	if($parent)
  	{
  		$final[] = [
  			'group_id'	 	=> $parent['acc_grp_parent_id'],
  			'group_name' 	=> $parent['grp_name'],
  			'balance'	 	=> '',
  			'type'			=> 'prt',
  			'style'			=> 'font-weight:bold;'
  		];

  		$total_balance = 0;

			$grpcomstid_tbl = 'grpcomstid';
			$result = $this->db->table($grpcomstid_tbl)
												->select('crs_master_id, crs_master_name')
												->where('crs_master_type', 'acctmaster')
												->where('parent_id', $parent['acc_grp_parent_id'])
												->get()->getResultArray();

			foreach ($result as $key => $value) {
				$balance = $this->get_parent_group_details_comp($value['crs_master_id'],$from_date,$to_date,'acctmaster',$pnl);
				$total_balance += $balance;

				$final[] = [
	  			'group_id'	 	=> $value['crs_master_id'],
	  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$value['crs_master_name'],
	  			'balance'	 		=> $balance,
	  			'type'				=> 'acc',
	  			'style'				=> ''
	  		];
			}

			$result = $this->db->table($grpcomstid_tbl)
												->select('crs_master_id, crs_master_name')
												->where('crs_master_type', 'billsundry')
												->where('parent_id', $parent['acc_grp_parent_id'])
												->get()->getResultArray();

			foreach ($result as $key => $value) {
				$balance = $this->get_parent_group_details_comp($value['crs_master_id'],$from_date,$to_date,'billsundry',$pnl);
				$total_balance += $balance;
		
				$final[] = [
	  			'group_id'	 	=> $value['crs_master_id'],
	  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$value['crs_master_name'],
	  			'balance'	 		=> $balance,
	  			'type'				=> 'bsd',
	  			'style'				=> 'font-style:italic;'
	  		];
			}

			if($view == 0){
				$final = [ 0 => [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 		=> $total_balance,
	  			'type'				=> 'prt',
	  			'style'				=> 'font-weight:bold;'
	  		] ];
			}
		}

  	return $final;
  }


  function get_pl_details($from_date,$to_date)
  {
    	$array1 = [];
    	$array2 = [];

    	$l_step1_total = 0;
    	$r_step1_total = 0;

    	// Opening Stock
    	$l_step1_total += $this->get_opn_stk();

    	$data = $this->get_parent_group_details(7,0,$from_date,$to_date,true); // PURCHASES
    	if($data)
    		$l_step1_total += array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details(11,0,$from_date,$to_date,true); // DIRECTO EXPENSES
    	if($data)
    		$l_step1_total += array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details(8,0,$from_date,$to_date,true); // SALES
    	if($data)
    		$r_step1_total += -array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details(10,0,$from_date,$to_date,true); // DIRECT INCOMES
    	if($data)
    		$r_step1_total += -array_sum(array_column($data, 'balance'));


    	$data = $this->get_clo_stk($to_date); // CLOSING STOCK
    	if($data)
    		$r_step1_total += $data;


    	$l_step2_total = 0;
    	$r_step2_total = 0;


    	if($l_step1_total > $r_step1_total){
    		$diff = $l_step1_total - $r_step1_total;
    		$l_step2_total += $diff;
    	}
    	if($l_step1_total < $r_step1_total){
    		$diff = $r_step1_total - $l_step1_total;
    		$r_step2_total += $diff;
    	}

		$data = $this->get_parent_group_details(13,0,$from_date,$to_date,true); // INDIRECT EXPENSES
		if($data)
			$l_step2_total += array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details(12,0,$from_date,$to_date,true); // INDIRECT INCOMES
    	if($data)
    		$r_step2_total += -array_sum(array_column($data, 'balance'));

    	$balance = parseAmount($r_step2_total) - parseAmount($l_step2_total);

    	return $balance;
  }

  function load_profit_loss($view,$from_date,$to_date,$nil_type)
  {
    	$array1 = [];
    	$array2 = [];

    	$data = $this->get_opn_stk();

    		$array1[] = [
    			'l_group_id'	   	=> 0,
    			'l_group_name'    	=> 'Opening Stock',
    			'l_balance'		  		=> $data != '' ? formatAmount($data) : '',
    			'l_balance_total'		=> $data != '' ? $data : '',
    			'l_type'		  			=> 'stk',
    			'l_style'		  		=> 'font-weight:bold;',
    		];
    	

    	$data = $this->get_parent_group_details(7, $view,$from_date,$to_date,true);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
    					'l_group_id'	  		=> $value['group_id'],
    					'l_group_name'    	=> $value['group_name'],
    					'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
    					'l_balance_total'		=> $value['balance'],
    					'l_type'		  			=> $value['type'],
    					'l_style'		  		=> $value['style'],
    				];
    			}
    		}
    	}
    	$data = $this->get_parent_group_details(11, $view,$from_date,$to_date,true);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
    					'l_group_id'	  		=> $value['group_id'],
    					'l_group_name'    	=> $value['group_name'],
    					'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
    					'l_balance_total'		=> $value['balance'],
    					'l_type'		  			=> $value['type'],
    					'l_style'		  		=> $value['style'],
    				];
    			}
    		}
    	}


    	$data = $this->get_parent_group_details(8, $view,$from_date,$to_date,true);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array2[] = [
    					'r_group_id'	   	=> $value['group_id'],
    					'r_group_name'    	=> $value['group_name'],
    					'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
    					'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
    					'r_type'		  			=> $value['type'],
    					'r_style'		  		=> $value['style'],
    				];
    			}
    		}
    	}
    	$data = $this->get_parent_group_details(10, $view,$from_date,$to_date,true);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array2[] = [
    					'r_group_id'	   	=> $value['group_id'],
    					'r_group_name'    	=> $value['group_name'],
    					'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
    					'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
    					'r_type'		  			=> $value['type'],
    					'r_style'		  		=> $value['style'],
    				];
    			}
    		}
    	}



    	$data = $this->get_clo_stk($to_date);
  
    		$array2[] = [
    			'r_group_id'	   	=> 0,
    			'r_group_name'    	=> 'Closing Stock',
    			'r_balance'		  		=> $data != '' ? formatAmount($data) : '',
    			'r_balance_total'		=> $data != '' ? $data : '',
    			'r_type'		  			=> 'stk',
    			'r_style'		  		=> 'font-weight:bold;',
    		];
  

    	$final = [];

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total'));
    	$r_total = array_sum(array_column($array2, 'r_balance_total'));

    	$l_diff = 0;
    	$r_diff = 0;
    	$total = $l_total;

    	if($l_total > $r_total)
    	{
    		$l_diff = 0;
    		$r_diff = parseAmount($l_total) - parseAmount($r_total);
    		$total = $l_total;
    	}
    	if($l_total < $r_total)
    	{
    		$l_diff = parseAmount($r_total) - parseAmount($l_total);
    		$r_diff = 0;
    		$total = $r_total;
    	}

    	$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => 'Gross Profit C/F',
    		'l_balance'		  => formatAmount($l_diff),
    		'l_balance_total'		  => $l_diff,
    		'r_group_id'	  => 0,
    		'r_group_name'    => 'Gross Loss C/F',
    		'r_balance'		  => formatAmount($r_diff),
    		'r_balance_total'		  => $r_diff,
    	];

    	$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => '',
    		'l_balance'		  => formatAmount($total),
    		'l_balance_total'		  => $total,
    		'r_group_id'	  => 0,
    		'r_group_name'    => '',
    		'r_balance'		  => formatAmount($total),
    		'r_balance_total'		  => $total,
    		'step'			  => 0,
    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
    	];

    	$array1 = [];
    	$array2 = [];

    	$array1[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => 'Gross Loss B/D',
    		'l_balance'		  => formatAmount($r_diff),
    		'l_balance_total'		  => $r_diff,
    	];
    	$array2[] = [
    		'r_group_id'	  => 0,
    		'r_group_name'    => 'Gross Profit B/D',
    		'r_balance'		  => formatAmount($l_diff),
    		'r_balance_total'		  => $l_diff,
    	];

    	$data = $this->get_parent_group_details(13, $view,$from_date,$to_date,true);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
    					'l_group_id'	  		=> $value['group_id'],
    					'l_group_name'    	=> $value['group_name'],
    					'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
    					'l_balance_total'		=> $value['balance'],
    					'l_type'		  			=> $value['type'],
    					'l_style'		  		=> $value['style'],
    				];
    			}
    		}
    	}
    	$data = $this->get_parent_group_details(12, $view,$from_date,$to_date,true);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array2[] = [
    					'r_group_id'	   	=> $value['group_id'],
    					'r_group_name'    	=> $value['group_name'],
    					'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
    					'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
    					'r_type'		  			=> $value['type'],
    					'r_style'		  		=> $value['style'],
    				];
    			}
    		}
    	}

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total')); //10,000
    	$r_total = array_sum(array_column($array2, 'r_balance_total')); // 8,000

    	$l_diff = 0;
    	$r_diff = 0;
    	$total = $l_total;

    	if($l_total > $r_total)
    	{
    		$l_diff = 0;
    		$r_diff = parseAmount($l_total) - parseAmount($r_total);
    		$total = $l_total;
    	}
    	if($l_total < $r_total)
    	{
    		$l_diff = parseAmount($r_total) - parseAmount($l_total);
    		$r_diff = 0;
    		$total = $r_total;
    	}

    	$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => 'Net Profit C/D',
    		'l_balance'		  => formatAmount($l_diff),
    		'l_balance_total'		  => $l_diff,
    		'r_group_id'	  => 0,
    		'r_group_name'    => 'Net Loss C/D',
    		'r_balance'		  => formatAmount($r_diff),
    		'r_balance_total'		  => $r_diff,
    	];

    	$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => '',
    		'l_balance'		  => formatAmount($total),
    		'l_balance_total'		  => $total,
    		'r_group_id'	  => 0,
    		'r_group_name'    => '',
    		'r_balance'		  => formatAmount($total),
    		'r_balance_total'		  => $total,
    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
    	];

    	return $final;
  }


  function get_parent_op_bal_diff()
  {
  	$balance = 0;

  	$parent = [1,2,3,4,5,6,7,8,9,10,11,12,13];

  	foreach ($parent as $parent_id) {

			$grpcomstid_tbl = 'grpcomstid';
			$result = $this->db->table($grpcomstid_tbl)
												->select('crs_master_id, crs_master_name')
												->where('crs_master_type', 'acctmaster')
												->where('parent_id', $parent_id)
												->get()->getResultArray();

			foreach ($result as $key => $value) {
		
				$grpmapping_tbl = 'grpmapping';
				$result2 = $this->db->table($grpmapping_tbl)
														->where('crs_master_id', $value['crs_master_id'])
														->where('master_type', 'acctmaster')
														->get()->getResultArray();

				foreach ($result2 as $key2 => $value2) {
					$balance += $this->get_acc_opn_balance($value2['comp_id'],$value2['master_id'])	;
				}
			}

			$result = $this->db->table($grpcomstid_tbl)
												->select('crs_master_id, crs_master_name')
												->where('crs_master_type', 'billsundry')
												->where('parent_id', $parent_id)
												->get()->getResultArray();

			foreach ($result as $key => $value) {
		
				$grpmapping_tbl = 'grpmapping';
				$result2 = $this->db->table($grpmapping_tbl)
														->where('crs_master_id', $value['crs_master_id'])
														->where('master_type', 'billsundry')
														->get()->getResultArray();

				foreach ($result2 as $key2 => $value2) {
					$balance += $this->get_bsd_opn_balance($value2['comp_id'],$value2['master_id']);
				}
			}
		}

		$balance += $this->get_opn_stk();

  	return -$balance;
  }

  function load_trial_balance1($from_date, $to_date)
  {
  	$final = [];
  	$op_balance = 0;

		$grpcomstid_tbl = 'grpcomstid';
		$result = $this->db->table($grpcomstid_tbl)
											->select('crs_master_id, crs_master_name, parent')
											->where('crs_master_type', 'acctmaster')
											->get()->getResultArray();

		foreach ($result as $key => $value) {
			$balance = 0;
			$comp = [];
			$grpmapping_tbl = 'grpmapping';
			$result2 = $this->db->table($grpmapping_tbl)
													->where('crs_master_id', $value['crs_master_id'])
													->where('master_type', 'acctmaster')
													->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {
				$comp[] = grp_comp_details($value2['comp_id'])->comp_name;
				$op_balance += $this->get_acc_opn_balance($value2['comp_id'],$value2['master_id']);
				$balance += $this->get_acc_clo_bal($value2['comp_id'],$value2['master_id'],$to_date);
			}

			$credit = $debit = 0;
			if($balance >= 0)
				$debit = $balance;
			else
				$credit = abs($balance);

			$companies = implode(",",$comp);

			$final[] = [
  			'group_id'		=> $value['crs_master_id'],
  			'group_name'	=> $value['crs_master_name'],
  			'parent'			=> $value['parent'],
  			'companies'		=> $companies,
  			'credit'		=> !empty($credit) ? formatAmount($credit) : '',
  			'debit'			=> !empty($debit) ? formatAmount($debit) : '',
  			'credit_total'	=> $credit,
  			'debit_total'		=> $debit,
  			'transaction_status'	=> 1,
  			'type'	=> 'acc'
  		];
		}

		$result = $this->db->table($grpcomstid_tbl)
											->select('crs_master_id, crs_master_name, parent')
											->where('crs_master_type', 'billsundry')
											->get()->getResultArray();

		foreach ($result as $key => $value) {
			$balance = 0;
			$comp = [];
			$grpmapping_tbl = 'grpmapping';
			$result2 = $this->db->table($grpmapping_tbl)
													->where('crs_master_id', $value['crs_master_id'])
													->where('master_type', 'billsundry')
													->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {
				$comp[] = grp_comp_details($value2['comp_id'])->comp_name;
				$op_balance += $this->get_bsd_opn_balance($value2['comp_id'],$value2['master_id']);
				$balance += $this->get_bsd_clo_bal($value2['comp_id'],$value2['master_id'],$to_date);
			}

			$credit = $debit = 0;
			if($balance >= 0)
				$debit = $balance;
			else
				$credit = abs($balance);

			$companies = implode(",",$comp);

			$final[] = [
  			'group_id'		=> $value['crs_master_id'],
  			'group_name'	=> $value['crs_master_name'],
  			'parent'			=> $value['parent'],
  			'companies'		=> $companies,
  			'credit'		=> !empty($credit) ? formatAmount($credit) : '',
  			'debit'			=> !empty($debit) ? formatAmount($debit) : '',
  			'credit_total'	=> $credit,
  			'debit_total'		=> $debit,
  			'transaction_status'	=> 1,
  			'type'	=> 'bsd'
  		];
		}

		$credit = $debit = 0;
		if($op_balance >= 0)
			$credit = $op_balance;
		else
			$debit = abs($op_balance);

		$final[] = [
			'group_id'		=> 0,
			'group_name'	=> 'Op. Difference',
			'parent'			=> '',
			'companies'		=> '',
			'credit'		=> !empty($credit) ? formatAmount($credit) : '',
			'debit'			=> !empty($debit) ? formatAmount($debit) : '',
			'credit_total'	=> $credit,
			'debit_total'		=> $debit,
			'transaction_status'	=> 1,
			'type'	=> 'opn'
		];
		

  	return $final;
  }

  function load_trial_balance_op()
  {
  	$final = [];
  	$op_balance = 0;

		$grpcomstid_tbl = 'grpcomstid';
		$result = $this->db->table($grpcomstid_tbl)
											->select('crs_master_id, crs_master_name, parent')
											->where('crs_master_type', 'acctmaster')
											->get()->getResultArray();

		foreach ($result as $key => $value) {
			$balance = 0;
			$comp = [];
			$grpmapping_tbl = 'grpmapping';
			$result2 = $this->db->table($grpmapping_tbl)
													->where('crs_master_id', $value['crs_master_id'])
													->where('master_type', 'acctmaster')
													->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {
				$comp[] = grp_comp_details($value2['comp_id'])->comp_name;
				$op_balance += $this->get_acc_opn_balance($value2['comp_id'],$value2['master_id']);
				$balance += $this->get_acc_opn_balance($value2['comp_id'],$value2['master_id']);
			}

			$credit = $debit = 0;
			if($balance >= 0)
				$debit = $balance;
			else
				$credit = abs($balance);

			$companies = implode(",",$comp);

			$final[] = [
  			'group_id'		=> $value['crs_master_id'],
  			'group_name'	=> $value['crs_master_name'],
  			'parent'			=> $value['parent'],
  			'companies'		=> $companies,
  			'credit'		=> !empty($credit) ? formatAmount($credit) : '',
  			'debit'			=> !empty($debit) ? formatAmount($debit) : '',
  			'credit_total'	=> $credit,
  			'debit_total'		=> $debit,
  			'transaction_status'	=> 1,
  			'type'	=> 'acc'
  		];
		}

		$result = $this->db->table($grpcomstid_tbl)
											->select('crs_master_id, crs_master_name, parent')
											->where('crs_master_type', 'billsundry')
											->get()->getResultArray();

		foreach ($result as $key => $value) {
			$balance = 0;
			$comp = [];
			$grpmapping_tbl = 'grpmapping';
			$result2 = $this->db->table($grpmapping_tbl)
													->where('crs_master_id', $value['crs_master_id'])
													->where('master_type', 'billsundry')
													->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {
				$comp[] = grp_comp_details($value2['comp_id'])->comp_name;
				$op_balance += $this->get_bsd_opn_balance($value2['comp_id'],$value2['master_id']);
				$balance += $this->get_bsd_opn_balance($value2['comp_id'],$value2['master_id']);
			}

			$credit = $debit = 0;
			if($balance >= 0)
				$debit = $balance;
			else
				$credit = abs($balance);

			$companies = implode(",",$comp);

			$final[] = [
  			'group_id'		=> $value['crs_master_id'],
  			'group_name'	=> $value['crs_master_name'],
  			'parent'			=> $value['parent'],
  			'companies'		=> $companies,
  			'credit'		=> !empty($credit) ? formatAmount($credit) : '',
  			'debit'			=> !empty($debit) ? formatAmount($debit) : '',
  			'credit_total'	=> $credit,
  			'debit_total'		=> $debit,
  			'transaction_status'	=> 1,
  			'type'	=> 'bsd'
  		];
		}

		$op_stock = $this->get_opn_stk();
		$op_balance += $op_stock;

		$credit = $debit = 0;
		if($op_stock >= 0)
			$debit = $op_stock;
		else
			$credit = abs($op_stock);

		$final[] = [
			'group_id'		=> 0,
			'group_name'	=> 'Op. Stock',
			'parent'			=> '',
			'companies'		=> '',
			'credit'		=> !empty($credit) ? formatAmount($credit) : '',
			'debit'			=> !empty($debit) ? formatAmount($debit) : '',
			'credit_total'	=> $credit,
			'debit_total'		=> $debit,
			'transaction_status'	=> 1,
			'type'	=> 'stk'
		];

		$credit = $debit = 0;
		if($op_balance >= 0)
			$credit = $op_balance;
		else
			$debit = abs($op_balance);

		$final[] = [
			'group_id'		=> 0,
			'group_name'	=> 'Op. Difference',
			'parent'			=> '',
			'companies'		=> '',
			'credit'		=> !empty($credit) ? formatAmount($credit) : '',
			'debit'			=> !empty($debit) ? formatAmount($debit) : '',
			'credit_total'	=> $credit,
			'debit_total'		=> $debit,
			'transaction_status'	=> 1,
			'type'	=> 'opn'
		];
		

  	return $final;
  }


  function load_accounts_summary($crs_master_id)
	{
		$fy_months = grp_fy_calender();

		$debit = 0; 
		$credit = 0;
		$balance = 0;

		$final = [];
		foreach ($fy_months as $fy_month)
		{
			$from_date = $fy_month['from_date'];
			$to_date = $fy_month['to_date'];

			$month = $fy_month['month'];

			$data = $this->load_account_summary_details($crs_master_id,$from_date,$to_date);

			$debit = $data['debit'];
			$credit = $data['credit'];
			$balance = $data['balance'];
			$balance_type = $data['balance_type'];

			$final[] = [
				'from_date' 				=> date('d-m-Y', strtotime($from_date)),
				'to_date'   				=> date('d-m-Y', strtotime($to_date)),
				'month'   					=> $month,
				'credit'   					=> $credit,
				'debit'   					=> $debit,
				'balance'   				=> $balance,
				'balance_type'   		=> $balance_type,
			];
		}
		// echo "<pre>";print_r($final);exit;
		return $final;
	}

	function load_account_summary_details($crs_master_id,$from_date,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'acctmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();

		$debit = 0;
		$credit = 0;
		$balance = 0;

		foreach ($result as $key => $value) {

			$comp_id = $value['comp_id'];
			$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

			if($comp_fy_id){

					$account_id = $this->get_master_id_comp('acctmaster',$comp_id,$comp_fy_id,$crs_master_id);

				if($account_id){

					$comp_db = $this->externaldb->comp_db($comp_id);
				
					$accnttxnnn_tbl = $comp_id.'_accnttxnnn_'.$account_id.'_'.$comp_fy_id;
					$accnttxnnn = $comp_db->table($accnttxnnn_tbl)
														->select('SUM(acc_txn_amount) as total')
														->where('acc_id', $account_id)
														->where('acc_txn_date >=',$from_date)
														->where('acc_txn_date <=',$to_date)
														->where('acc_txn_drcr','d')
														->get()->getRowArray();
					$debit += floatval($accnttxnnn['total']);

					$accnttxnnn = $comp_db->table($accnttxnnn_tbl)
														->select('SUM(acc_txn_amount) as total')
														->where('acc_id', $account_id)
														->where('acc_txn_date >=',$from_date)
														->where('acc_txn_date <=',$to_date)
														->where('acc_txn_drcr','c')
														->get()->getRowArray();
					$credit += floatval($accnttxnnn['total']);
			
					$balance += $this->get_acc_clo_bal($comp_id,$value['master_id'],$to_date);
				}
			}
 		}

 		if($balance >= 0)
 			$balance_type = 'DR';
 		else
 			$balance_type = 'CR';

 		return [
 			'debit'	 				=> $debit,
 			'credit' 				=> $credit,
 			'balance'				=> abs($balance),
 			'balance_type' 	=> $balance_type
 		];
	}

	function load_bill_sundry_summary($crs_master_id)
	{
		$fy_months = grp_fy_calender();

		$debit = 0; 
		$credit = 0;
		$balance = 0;

		$final = [];
		foreach ($fy_months as $fy_month)
		{
			$from_date = $fy_month['from_date'];
			$to_date = $fy_month['to_date'];

			$month = $fy_month['month'];

			$data = $this->load_bill_sundry_summary_details($crs_master_id,$from_date,$to_date);

			$debit = $data['debit'];
			$credit = $data['credit'];
			$balance = $data['balance'];
			$balance_type = $data['balance_type'];

			$final[] = [
				'from_date' 				=> date('d-m-Y', strtotime($from_date)),
				'to_date'   				=> date('d-m-Y', strtotime($to_date)),
				'month'   					=> $month,
				'credit'   					=> $credit,
				'debit'   					=> $debit,
				'balance'   				=> $balance,
				'balance_type'   		=> $balance_type,
			];
		}
		// echo "<pre>";print_r($final);exit;
		return $final;
	}

	function load_bill_sundry_summary_details($crs_master_id,$from_date,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'billsundry')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();

		$debit = 0;
		$credit = 0;
		$balance = 0;

		foreach ($result as $key => $value) {

			$comp_id = $value['comp_id'];
			$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;

			if($comp_fy_id){

					$account_id = $this->get_master_id_comp('billsundry',$comp_id,$comp_fy_id,$crs_master_id);

				if($account_id){

					$comp_db = $this->externaldb->comp_db($comp_id);
				
					$accnttxnnn_tbl = $comp_id.'_sundrytxnn_'.$account_id.'_'.$comp_fy_id;
					$accnttxnnn = $comp_db->table($accnttxnnn_tbl)
													->select('SUM(sundry_txn_amount) as total')
													->where('bill_sundry_id', $account_id)
													->where('sundry_txn_date >=',$from_date)
													->where('sundry_txn_date <=',$to_date)
													->where('sundry_txn_drcr','d')
													->get()->getRowArray();
					$debit += floatval($accnttxnnn['total']);

					$accnttxnnn = $comp_db->table($accnttxnnn_tbl)
													->select('SUM(sundry_txn_amount) as total')
													->where('bill_sundry_id', $account_id)
													->where('sundry_txn_date >=',$from_date)
													->where('sundry_txn_date <=',$to_date)
													->where('sundry_txn_drcr','c')
													->get()->getRowArray();
					$credit += floatval($accnttxnnn['total']);
			
					$balance += $this->get_bsd_clo_bal($comp_id,$value['master_id'],$to_date);
				}
			}
 		}

 		if($balance >= 0)
 			$balance_type = 'DR';
 		else
 			$balance_type = 'CR';

 		return [
 			'debit'	 				=> $debit,
 			'credit' 				=> $credit,
 			'balance'				=> abs($balance),
 			'balance_type' 	=> $balance_type
 		];
	}

	function load_item_ledger($crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val_id, $from_date, $to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'itemmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {
			$responses = $this->load_item_ledger_by_comp($value['comp_id'],$crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val_id, $from_date, $to_date);
	
			foreach ($responses as $response) {
				$final[] = $response;
			}
		}

		return $final;
	}

	function get_mc_list_by_group($mc_grp_id,$comp_id,$comp_fy_id,$comp_db)
	{

		if($mc_grp_id == 0){
			return [];
		}

		$final = [];
		$mcmasternn_tbl  = $comp_id.'_mcmasternn_'.$comp_fy_id;

  	$builder = $comp_db->table($mcmasternn_tbl);
  	$builder->where('mat_cent_grp_id', $mc_grp_id);
  	$result = $builder->get()->getResultArray();

  	foreach ($result as $key => $value) {
  		$final[] = $value['mat_cent_id'];
  	}

  	return $final;
	}

	function load_item_ledger_by_comp($comp_id,$crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val, $from_date, $to_date)
	{
		$comp_details = grp_comp_details($comp_id);
		$comp_name = $comp_details->comp_name;
		$comp_fy_id = $comp_details->comp_fy_id;

		if($comp_fy_id == 0){
			return [];
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$crs_master_id);

		if($item_id == 0){
			return [];
		}

		$mc_id = $this->get_master_id_comp('mcmasternn',$comp_id,$comp_fy_id,$mc_id_crs);

		$mc_grp_id = $this->get_master_id_comp('mcgrpmstnn',$comp_id,$comp_fy_id,$mc_grp_id_crs);

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		$comp_db = $this->externaldb->comp_db($comp_id);

		$vhtxnconso_tbl = $comp_id.'_vhtxnconso_'.$comp_fy_id;
		$cmpvchtype_tbl = $comp_id.'_cmpvchtype_'.$comp_fy_id;
		$cmpvchseri_tbl = $comp_id.'_cmpvchseri_'.$comp_fy_id;
		$comptxnmst_tbl = $comp_id.'_comptxnmst_'.$comp_fy_id;

		$itmunitmst_tbl = $comp_id.'_itmunitmst_'.$comp_fy_id;
		$mcmasternn_tbl = $comp_id.'_mcmasternn_'.$comp_fy_id;

		$acctmaster_tbl = $comp_id.'_acctmaster_'.$comp_fy_id;
		$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;

		$itemtxnnnn_tbl = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
		$itemtxnval_tbl = $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;

	
		


		$item_info  = $comp_db->table($itemmaster_tbl)
														->where('item_id',$item_id)
														->get()->getRowArray();
		$pur_acc_name = '';
		$sal_acc_name = '';
		if($item_info){

			$account_info = $comp_db->table($acctmaster_tbl)
											->where('acc_id',$item_info['item_pur_acc'])
											->get()->getRowArray();

			$pur_acc_name = $account_info['acc_name'] ?? '';

			$account_info = $comp_db->table($acctmaster_tbl)
											->where('acc_id',$item_info['item_sales_acc'])
											->get()->getRowArray();

			$sal_acc_name = $account_info['acc_name'] ?? '';
		}

		if($val == 0 && $item_info){
			$val_id = $item_info['valmethod_id'];
		}
		else{
			$val_id = $val;
		}

		$units_arr = [];

		$mc_list = [];
		if($mc_id != 0)
			$mc_list = [$mc_id];
		if($mc_grp_id != 0)
			$mc_list = $this->get_mc_list_by_group($mc_grp_id,$comp_id,$comp_fy_id,$comp_db);
	
		

		$builder = $comp_db->table($vhtxnconso_tbl);
		$builder->select($vhtxnconso_tbl.'.*');
		$builder->join($cmpvchtype_tbl, $cmpvchtype_tbl.'.voucher_type_id ='.$vhtxnconso_tbl.'.voucher_type_id');
		$builder->select($cmpvchtype_tbl.'.comp_vch_type');
		$builder->join($cmpvchseri_tbl, $cmpvchseri_tbl.'.comp_vch_series_id  ='.$vhtxnconso_tbl.'.comp_vch_series_id');
		$builder->select($cmpvchseri_tbl.'.comp_vch_series');
		$builder->join($comptxnmst_tbl, $comptxnmst_tbl.'.voucher_txn_id  ='.$vhtxnconso_tbl.'.voucher_txn_id AND master_id_type = "itm"');
		$builder->select('master_id, master_id_type');
		$builder->join($itemtxnnnn_tbl, $itemtxnnnn_tbl.'.txn_id  = '.$comptxnmst_tbl.'.txn_id');		
		$builder->select('item_txn_id, item_id, item_txn_date, item_txn_drcr, item_txn_qty, item_bal_qty, item_txn_amount, item_unit, batch_id');

		$builder->where($itemtxnnnn_tbl.'.item_id', $item_id);
		if($unit_id != 0)
			$builder->where($itemtxnnnn_tbl.'.item_unit', $unit_id);
		if(count($mc_list))
			$builder->whereIn($itemtxnnnn_tbl.'.mat_cent_id', $mc_list);
		
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);


		// $builder->where($vhtxnconso_tbl.'.bo_id', $this->session->get('ses_boid'));

		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('item_txn_id');
		$result = $builder->get()->getResultArray();

		$final = [];

		foreach($result as $key => $value){

			$voucher_date  = $value['voucher_date'];

			$particulars = '';
			$unit_name = '';

			$inward_amount = 0;
    	$outward_amount = 0;

    	$inward_qty = 0;
    	$outward_qty = 0;

    	$closing_qty  = 0;
    	$valuation = 0;
    	$profit = 0;
    	$profit_string = '';

    	if($value['item_txn_drcr']=='d'){
				$inward_qty = floatval($value['item_txn_qty']);
				$inward_amount = floatval($value['item_txn_amount']);
			}
			if($value['item_txn_drcr']=='c'){
				$outward_qty = floatval($value['item_txn_qty']);
				$outward_amount = floatval($value['item_txn_amount']);
			}

			$closing_qty = floatval($value['item_bal_qty']);

			if($value['item_txn_drcr']=='d')
				$particulars = $pur_acc_name;

			if($value['item_txn_drcr']=='c')
				$particulars = $sal_acc_name;

			if(isset($units_arr[$value['item_unit']])){
				$unit_name = $units_arr[$value['item_unit']];
			}
			else{
				$item_unit_info = $comp_db->table($itmunitmst_tbl)
															->where('unit_id',$value['item_unit'])
															->get()->getRowarray();

				if($item_unit_info){
					$unit_name = $item_unit_info['item_unit'];
					$units_arr[$value['item_unit']] = $item_unit_info['item_unit'];
				}										
			}
	

			$builder = $comp_db->table($itemtxnval_tbl);
			$builder->where('item_id', $value['item_id']);
			$builder->where('item_txn_id', $value['item_txn_id']);
			$builder->where('method_id', $val_id);
			$itemtxnval = $builder->get()->getRowArray();
			if($itemtxnval){
				$valuation = floatval($itemtxnval['item_value']);
				$profit = floatval($itemtxnval['profit']);
				$profit_string = $itemtxnval['profit_string'];
			}

			$voucher = $value['comp_vch_type'] . ' (No. '.$value['comp_vch_no'].')';

			$final[]    = [
				'comp_id'				=> $comp_id,
				'comp_name'			=> $comp_name,
				'voucher_no'		=> $value['comp_vch_no'],
				'voucher_txn_id'	=> $value['voucher_txn_id'],
				'comp_vch_series_id'=> $value['comp_vch_series_id'],
				'voucher_type_id'	=> $value['voucher_type_id'],
				'voucher_date'		=> $voucher_date,
				'voucher_type'		=> $value['comp_vch_type'],
				'particulars'		=> $particulars,
				'unit_name'			=> $unit_name,
				'voucher'			=> $voucher,

				'inward_amount'			=> $inward_amount,
				'outward_amount'		=> $outward_amount,

				'inward_qty'			=> $inward_qty,
				'outward_qty'			=> $outward_qty,

				'item_txn_drcr'			=> $value['item_txn_drcr'],
				'balance_qty'			=> $closing_qty,
				'balance_amount'		=> $valuation,
				'profit'				=> $profit,
				'profit_string'			=> $profit_string,

			];

	  }

		return $final;
	}

	function get_item_units($item_id,$comp_id,$comp_fy_id,$comp_db)
	{
		$unit_result = [];

		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$builder = $comp_db->table($itmoppybal_tbl);
		$builder->select('item_unit');
		$builder->where('item_id', $item_id);
		$builder->groupBy('item_unit');
		$result2 = $builder->get()->getResultArray();
		if($result2){
			foreach ($result2 as $key2 => $value2) {
				if(!in_array($value2['item_unit'], $unit_result)){
					$unit_result[] = $value2['item_unit'];
				}
			}
		}

		return $unit_result;
	}

	function get_itm_opn_bal_list($crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val_id, $from_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'itemmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {
			$responses = $this->get_itm_opn_bal($value['comp_id'],$crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val_id, $from_date);
	
			foreach ($responses as $response) {
				$final[] = $response;
			}
		}

		return $final;
	}

	function get_itm_opn_bal($comp_id, $crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val, $from_date)
  {
  	$final = [];

  	$comp_details = grp_comp_details($comp_id);
		$comp_name = $comp_details->comp_name;
		$comp_fy_id = $comp_details->comp_fy_id;

		if($comp_fy_id == 0){
			return [];
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$crs_master_id);

		if($item_id == 0){
			return [];
		}

		$mc_id = $this->get_master_id_comp('mcmasternn',$comp_id,$comp_fy_id,$mc_id_crs);

		$mc_grp_id = $this->get_master_id_comp('mcgrpmstnn',$comp_id,$comp_fy_id,$mc_grp_id_crs);

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		$comp_db = $this->externaldb->comp_db($comp_id);

		$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
		if($val == 0){
			$itemmaster = $comp_db->table($itemmaster_tbl)
														->where('item_id',$item_id)
														->get()->getRowarray();

			$val_id = $itemmaster['valmethod_id'];
		}
		else{
			$val_id = $val;
		}

  	$method = '';
  	if($val_id == 1)
			$method = 'AVG';
		if($val_id == 2)
			$method = 'FIFO';
		if($val_id == 3)
			$method = 'LIFO';
		$units_arr = [];

		$itmunitmst_tbl = $comp_id.'_itmunitmst_'.$comp_fy_id;
  	$mcmasternn_tbl  = $comp_id.'_mcmasternn_'.$comp_fy_id;
  	$itemtxnnnn_tbl =  $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
  	$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
  	$itemtxnval_tbl =  $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;
  	$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;

  	$builder = $comp_db->table($mcmasternn_tbl);
  	if($mc_id != 0 && $mc_grp_id == 0)
  		$builder->where('mat_cent_id', $mc_id);
  	if($mc_id == 0 && $mc_grp_id != 0)
  		$builder->where('mat_cent_grp_id', $mc_grp_id);
  	$mc_result = $builder->get()->getResultArray();

  	$unit_result = [];
		if($unit_id != 0){
			$unit_result = [$unit_id];
		}
		else{
			$unit_result = $this->get_item_units($item_id,$comp_id,$comp_fy_id,$comp_db);
		}

		$bo_array = [];
		$bo_result = $comp_db->table($itmoppybal_tbl)
													->select('bo_id')
													->where('item_id', $item_id)
													->groupBy('bo_id')
													->get()->getResultArray();
		foreach ($bo_result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

  	foreach ($mc_result as $mc_key => $mc_value) {
  		$mc = $mc_value['mat_cent_name'];
  		
  		foreach ($unit_result as $unit_key => $unit_value) {
  			$balance = 0;
  			$item_value = 0;

  			$unit = '';
  			if(isset($units_arr[$unit_value])){
					$unit = $units_arr[$unit_value];
				}
				else{
					$item_unit_info = $comp_db->table($itmunitmst_tbl)
														->where('unit_id',$unit_value)
														->get()->getRowarray();

					if($item_unit_info){
						$unit = $item_unit_info['item_unit'];
						$units_arr[$unit_value] = $unit;
					}										
				}

				foreach ($bo_array as $bo_id) {

					$builder = $comp_db->table($itemtxnnnn_tbl);
		    	$builder->where('item_id', $item_id);
		    	$builder->where('item_unit', $unit_value);
		    	$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
		    	$builder->where('item_txn_date <', $from_date);
		    	$builder->where('batch_id', 0);
		    	$builder->where('item_avail', 1);
		    	$builder->where('bo_id', $bo_id);
		    	$builder->orderBy('item_txn_date', 'desc');
		    	$builder->orderBy('voucher_txn_id', 'desc');
		    	$builder->orderBy('item_txn_id', 'desc');
		    	$builder->limit(1);
		    	$transaction = $builder->get()->getRowArray();

		    	if($transaction){
		    		$balance += floatval($transaction['item_bal_qty']);

		    		$builder = $comp_db->table($itemtxnval_tbl);
						$builder->where('item_id', $item_id);
						$builder->where('item_txn_id', $transaction['item_txn_id']);
						$builder->where('method_id', $val_id);
						$itemtxnval = $builder->get()->getRowArray();
						if($itemtxnval){
							$item_value += floatval($itemtxnval['item_value']);
						}
		    	}
		    	else{
		    		$builder = $comp_db->table($itmoppybal_tbl); 
						$builder->where('item_id', $item_id);
						$builder->where('item_unit', $unit_value);
						$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
						$builder->where('bo_id', $bo_id);
						$builder->where('batch_id', 0);
			    	$itmoppybal = $builder->get()->getRowArray();
			    	if($itmoppybal){
			    		$balance += floatval($itmoppybal['op_bal_qty']);

			    		$builder = $comp_db->table($itmoppyval_tbl); 
							$builder->where('item_id', $item_id);
							$builder->where('item_unit', $unit_value);
							$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
							$builder->where('bo_id', $bo_id);
							$builder->where('batch_id', 0);
							$builder->where('method_id', $val_id);
							$itmoppyval = $builder->get()->getRowArray();

							if($itmoppyval){
								$item_value += floatval($itmoppyval['op_bal_val']);
							}
			    	}
		    	}
				}

	    	$final[] = [
	    		'comp_id'				=> $comp_id,
	    		'comp_name'			=> $comp_name,
	    		'item_id' 			=> $item_id,
	    		'item_unit' 		=> $unit_value,
	    		'unit_name'			=>	$unit,
	    		'mat_cent_id' 	=> $mc_value['mat_cent_id'],
	    		'mc_name' 			=> $mc,
	    		'balance' 			=> $balance,
	    		'item_value' 		=> formatValue($item_value),
	    		'method' 				=> $method,
	    	];
  		}
  	}

  	

  	return $final;
  }

  function get_itm_clo_bal_list($crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val_id, $to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'itemmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {
			$responses = $this->get_itm_clo_bal($value['comp_id'],$crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val_id, $to_date);
	
			foreach ($responses as $response) {
				$final[] = $response;
			}
		}

		return $final;
	}

	function get_itm_clo_bal($comp_id, $crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $val, $to_date)
  {
  	$final = [];

  	$comp_details = grp_comp_details($comp_id);
		$comp_name = $comp_details->comp_name;
		$comp_fy_id = $comp_details->comp_fy_id;

		if($comp_fy_id == 0){
			return [];
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$crs_master_id);

		if($item_id == 0){
			return [];
		}

		$mc_id = $this->get_master_id_comp('mcmasternn',$comp_id,$comp_fy_id,$mc_id_crs);

		$mc_grp_id = $this->get_master_id_comp('mcgrpmstnn',$comp_id,$comp_fy_id,$mc_grp_id_crs);

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		$comp_db = $this->externaldb->comp_db($comp_id);

		$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
		if($val == 0){
			$itemmaster = $comp_db->table($itemmaster_tbl)
														->where('item_id',$item_id)
														->get()->getRowarray();

			$val_id = $itemmaster['valmethod_id'];
		}
		else{
			$val_id = $val;
		}

  	$method = '';
  	if($val_id == 1)
			$method = 'AVG';
		if($val_id == 2)
			$method = 'FIFO';
		if($val_id == 3)
			$method = 'LIFO';

		$units_arr = [];

		$itmunitmst_tbl = $comp_id.'_itmunitmst_'.$comp_fy_id;
  	$mcmasternn_tbl  = $comp_id.'_mcmasternn_'.$comp_fy_id;
  	$itemtxnnnn_tbl =  $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
  	$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
  	$itemtxnval_tbl =  $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;
  	$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;

  	$builder = $comp_db->table($mcmasternn_tbl);
  	if($mc_id != 0 && $mc_grp_id == 0)
  		$builder->where('mat_cent_id', $mc_id);
  	if($mc_id == 0 && $mc_grp_id != 0)
  		$builder->where('mat_cent_grp_id', $mc_grp_id);
  	$mc_result = $builder->get()->getResultArray();

  	$unit_result = [];
		if($unit_id != 0){
			$unit_result = [$unit_id];
		}
		else{
			$unit_result = $this->get_item_units($item_id,$comp_id,$comp_fy_id,$comp_db);
		}

		$bo_array = [];
		$bo_result = $comp_db->table($itmoppybal_tbl)
													->select('bo_id')
													->where('item_id', $item_id)
													->groupBy('bo_id')
													->get()->getResultArray();
		foreach ($bo_result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

  	foreach ($mc_result as $mc_key => $mc_value) {
  		$mc = $mc_value['mat_cent_name'];
  		
  		foreach ($unit_result as $unit_key => $unit_value) {
  			$balance = 0;
  			$item_value = 0;

  			$unit = '';
  			if(isset($units_arr[$unit_value])){
					$unit = $units_arr[$unit_value];
				}
				else{
					$item_unit_info = $comp_db->table($itmunitmst_tbl)
														->where('unit_id',$unit_value)
														->get()->getRowarray();

					if($item_unit_info){
						$unit = $item_unit_info['item_unit'];
						$units_arr[$unit_value] = $unit;
					}										
				}

				foreach ($bo_array as $bo_id) {

					$builder = $comp_db->table($itemtxnnnn_tbl);
		    	$builder->where('item_id', $item_id);
		    	$builder->where('item_unit', $unit_value);
		    	$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
		    	$builder->where('item_txn_date <', $to_date);
		    	$builder->where('batch_id', 0);
		    	$builder->where('item_avail', 1);
		    	$builder->where('bo_id', $bo_id);
		    	$builder->orderBy('item_txn_date', 'desc');
		    	$builder->orderBy('voucher_txn_id', 'desc');
		    	$builder->orderBy('item_txn_id', 'desc');
		    	$builder->limit(1);
		    	$transaction = $builder->get()->getRowArray();

		    	if($transaction){
		    		$balance += floatval($transaction['item_bal_qty']);

		    		$builder = $comp_db->table($itemtxnval_tbl);
						$builder->where('item_id', $item_id);
						$builder->where('item_txn_id', $transaction['item_txn_id']);
						$builder->where('method_id', $val_id);
						$itemtxnval = $builder->get()->getRowArray();
						if($itemtxnval){
							$item_value += floatval($itemtxnval['item_value']);
						}
		    	}
		    	else{
		    		$builder = $comp_db->table($itmoppybal_tbl); 
						$builder->where('item_id', $item_id);
						$builder->where('item_unit', $unit_value);
						$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
						$builder->where('bo_id', $bo_id);
						$builder->where('batch_id', 0);
			    	$itmoppybal = $builder->get()->getRowArray();
			    	if($itmoppybal){
			    		$balance += floatval($itmoppybal['op_bal_qty']);

			    		$builder = $comp_db->table($itmoppyval_tbl); 
							$builder->where('item_id', $item_id);
							$builder->where('item_unit', $unit_value);
							$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
							$builder->where('bo_id', $bo_id);
							$builder->where('batch_id', 0);
							$builder->where('method_id', $val_id);
							$itmoppyval = $builder->get()->getRowArray();

							if($itmoppyval){
								$item_value += floatval($itmoppyval['op_bal_val']);
							}
			    	}
		    	}
				}

	    	$final[] = [
	    		'comp_id'				=> $comp_id,
	    		'comp_name'			=> $comp_name,
	    		'item_id' 			=> $item_id,
	    		'item_unit' 		=> $unit_value,
	    		'unit_name'			=>	$unit,
	    		'mat_cent_id' 	=> $mc_value['mat_cent_id'],
	    		'mc_name' 			=> $mc,
	    		'balance' 			=> $balance,
	    		'item_value' 		=> formatValue($item_value),
	    		'method' 				=> $method,
	    	];
  		}
  	}

  	

  	return $final;
  }


  function item_total_amount_list($crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $from_date, $to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'itemmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {
			$responses = $this->item_total_amount($value['comp_id'],$crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $from_date, $to_date);
	
			foreach ($responses as $response) {
				$final[] = $response;
			}
		}

		return $final;
	}

  function item_total_amount($comp_id, $crs_master_id, $unit_id_crs, $mc_id_crs, $mc_grp_id_crs, $from_date, $to_date)
  {

  	$final = [];

  	$comp_details = grp_comp_details($comp_id);
		$comp_name = $comp_details->comp_name;
		$comp_fy_id = $comp_details->comp_fy_id;

		if($comp_fy_id == 0){
			return [];
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$crs_master_id);

		if($item_id == 0){
			return [];
		}

		$mc_id = $this->get_master_id_comp('mcmasternn',$comp_id,$comp_fy_id,$mc_id_crs);

		$mc_grp_id = $this->get_master_id_comp('mcgrpmstnn',$comp_id,$comp_fy_id,$mc_grp_id_crs);

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		$comp_db = $this->externaldb->comp_db($comp_id);

		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$itmunitmst_tbl = $comp_id.'_itmunitmst_'.$comp_fy_id;
  	$itemtxnnnn_tbl =  $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
  	$mcmasternn_tbl  = $comp_id.'_mcmasternn_'.$comp_fy_id;

  	$builder = $comp_db->table($mcmasternn_tbl);
  	if($mc_id != 0 && $mc_grp_id == 0)
  		$builder->where('mat_cent_id', $mc_id);
  	if($mc_id == 0 && $mc_grp_id != 0)
  		$builder->where('mat_cent_grp_id', $mc_grp_id);
  	$mc_result = $builder->get()->getResultArray();

  	$unit_result = [];
		if($unit_id != 0){
			$unit_result = [$unit_id];
		}
		else{
			$unit_result = $this->get_item_units($item_id,$comp_id,$comp_fy_id,$comp_db);
		}
		$units_arr = [];

		$bo_array = [];
		$bo_result = $comp_db->table($itmoppybal_tbl)
													->select('bo_id')
													->where('item_id', $item_id)
													->groupBy('bo_id')
													->get()->getResultArray();
		foreach ($bo_result as $key => $value) {
			$bo_array[] = $value['bo_id'];
		}

		$credit_amount_total = 0;
		$debit_amount_total = 0;

		foreach ($mc_result as $mc_key => $mc_value) {
  		$mc = $mc_value['mat_cent_name'];
  		
  		foreach ($unit_result as $unit_key => $unit_value) {

				$balance = 0;
				$item_value = 0;

				$unit = '';
  			if(isset($units_arr[$unit_value])){
					$unit = $units_arr[$unit_value];
				}
				else{
					$item_unit_info = $comp_db->table($itmunitmst_tbl)
														->where('unit_id',$unit_value)
														->get()->getRowarray();

					if($item_unit_info){
						$unit = $item_unit_info['item_unit'];
						$units_arr[$unit_value] = $unit;
					}										
				}

				foreach ($bo_array as $bo_id) {

					$builder = $comp_db->table($itemtxnnnn_tbl);
					$builder->select('SUM(CASE WHEN `item_txn_drcr`="c" THEN `item_txn_qty` ELSE 0 END) AS credit_qty, SUM(CASE WHEN `item_txn_drcr`="d" THEN `item_txn_qty` ELSE 0 END) AS debit_qty, SUM(CASE WHEN `item_txn_drcr`="c" THEN `item_txn_amount` ELSE 0 END) AS credit_amount, SUM(CASE WHEN `item_txn_drcr`="d" THEN `item_txn_amount` ELSE 0 END) AS debit_amount');
			  	$builder->where('item_id', $item_id);
			  	$builder->where('item_unit', $unit_value);
			  	$builder->where('mat_cent_id', $mc_value['mat_cent_id']);
			  	$builder->where('item_txn_date >=', $from_date);
			  	$builder->where('item_txn_date <=', $to_date);
			  	$builder->where('batch_id', 0);
			  	$builder->where('item_avail', 1);
			  	$builder->where('bo_id', $bo_id);
			  	$transaction = $builder->get()->getRowArray();

			  	$credit_amount_total += floatval($transaction['credit_amount']);
					$debit_amount_total += floatval($transaction['debit_amount']);

			  	$final[] = [
			  		'comp_id'				=> $comp_id,
			  		'comp_name'			=> $comp_name,
		    		'item_id' 			=> $item_id,
		    		'item_unit' 		=> $unit_value,
		    		'unit_name'			=> $unit,
		    		'mat_cent_id' 	=> $mc_value['mat_cent_id'],
		    		'mc_name' 			=> $mc,
		    		'credit_qty' 		=> floatval($transaction['credit_qty']),
		    		'credit_amount' => formatAmount($transaction['credit_amount']),
		    		'debit_qty' 		=> floatval($transaction['debit_qty']),
		    		'debit_amount'	=> formatAmount($transaction['debit_amount']),
		    	];

		    }
		  }
		}

		$final[] = [
					'comp_id'				=> $comp_id,
			  	'comp_name'			=> $comp_name,
	    		'item_id' 			=> 0,
	    		'item_unit' 		=> 0,
	    		'unit_name'			=> '',
	    		'mat_cent_id' 	=> 0,
	    		'mc_name' 			=> 'Total Amount',
	    		'credit_qty' 		=> '',
	    		'credit_amount' => formatAmount($credit_amount_total),
	    		'debit_qty' 		=> '',
	    		'debit_amount'	=> formatAmount($debit_amount_total),
	    	];

		return $final;
  }

  function load_item_summary($crs_master_id,$unit_id_crs,$mc_id_crs,$mc_grp_id_crs,$val)
	{
		$fy_months = grp_fy_calender();

		$debit = 0; 
		$credit = 0;
		$balance = 0;

		$final = [];
		$color_array = ['#f9f9f9','#ffffff'];
		$color_index = 0;

		foreach ($fy_months as $fy_month)
		{
			$color_index = !$color_index;
			$style = 'background-color: '.$color_array[$color_index].';';

			$from_date = $fy_month['from_date'];
			$to_date = $fy_month['to_date'];

			$month = $fy_month['month'];

			$data = $this->load_item_summary_details($crs_master_id,$unit_id_crs,$mc_id_crs,$mc_grp_id_crs,$val,$from_date,$to_date);

				$final[] = [
					'from_date' 			=> date('d-m-Y', strtotime($from_date)),
					'to_date'   			=> date('d-m-Y', strtotime($to_date)),
					'month'   				=> $month,
				
					'debit_qty' 			=> $data['debit_qty'],
					'debit_amount' 		=> $data['debit_amount'],
					'credit_qty' 			=> $data['credit_qty'],
					'credit_amount' 	=> $data['credit_amount'],
					'balance_qty' 		=> $data['balance_qty'],
					'balance_amount' 	=> $data['balance_amount'],
					'profit' 					=> $data['profit'],
					'pq_rowattr'	    => ['style' => $style],
				];

			
		}
		// echo "<pre>";print_r($final);exit;
		return $final;
	}

	function load_item_summary_details($crs_master_id,$unit_id_crs,$mc_id_crs,$mc_grp_id_crs,$val,$from_date,$to_date)
	{
		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', 'itemmaster')
												->where('crs_master_id', $crs_master_id)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();

		$debit_qty = 0;
		$debit_amount = 0;
		$credit_qty = 0;
		$credit_amount = 0;
		$balance_qty = 0;
		$balance_amount = 0;
		$profit = 0;

		foreach ($result as $key => $value) {

			$comp_id = $value['comp_id'];
			$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;
			$comp_db = $this->externaldb->comp_db($comp_id);

			if($comp_fy_id){

				$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$crs_master_id);

				if($item_id == 0){
					continue;
				}

				$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
				if($val == 0){
					$itemmaster = $comp_db->table($itemmaster_tbl)
																->where('item_id',$item_id)
																->get()->getRowarray();

					$val_id = $itemmaster['valmethod_id'];
				}
				else{
					$val_id = $val;
				}

				$mc_id = $this->get_master_id_comp('mcmasternn',$comp_id,$comp_fy_id,$mc_id_crs);

				$mc_grp_id = $this->get_master_id_comp('mcgrpmstnn',$comp_id,$comp_fy_id,$mc_grp_id_crs);

				$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

				

				$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
				$itmunitmst_tbl = $comp_id.'_itmunitmst_'.$comp_fy_id;
		  	$itemtxnnnn_tbl =  $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
		  	$mcmasternn_tbl = $comp_id.'_mcmasternn_'.$comp_fy_id;


				$itemtxnval_tbl = $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;
				$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;

		  	$builder = $comp_db->table($mcmasternn_tbl);
		  	if($mc_id != 0 && $mc_grp_id == 0)
		  		$builder->where('mat_cent_id', $mc_id);
		  	if($mc_id == 0 && $mc_grp_id != 0)
		  		$builder->where('mat_cent_grp_id', $mc_grp_id);
		  	$mc_result = $builder->get()->getResultArray();

		  	$mc_list = [];
		  	foreach ($mc_result as $mc_key => $mc_value) {
		  		$mc_list[] = $mc_value['mat_cent_id'];
		  	}

		  	if(empty($mc_list)){
					continue;
				}

		  	$unit_result = [];
				if($unit_id == 0){
					continue;
				}

				$bo_array = [];
				$bo_result = $comp_db->table($itmoppybal_tbl)
															->select('bo_id')
															->where('item_id', $item_id)
															->groupBy('bo_id')
															->get()->getResultArray();
				foreach ($bo_result as $bo_key => $bo_value) {
					$bo_array[] = $bo_value['bo_id'];
				}
					
				$builder = $comp_db->table($itemtxnnnn_tbl);
				$builder->select('count(item_txn_id) as transactions, sum(item_txn_qty) as qty, sum(item_txn_amount) as amount');
				$builder->where('item_id', $item_id);
				$builder->where('item_unit', $unit_id);
				$builder->whereIn('mat_cent_id', $mc_list);
				$builder->where('batch_id', 0);
				$builder->where('item_avail', 1);
				$builder->where('item_txn_drcr', 'd');
				$builder->where('item_txn_date >=', $from_date);
				$builder->where('item_txn_date <=', $to_date);
				// $builder->where('bo_id', $this->bo_id);
				$result = $builder->get()->getRowArray();

				if($result['transactions'] > 0){
					$debit_qty += floatval($result['qty']);
					$debit_amount += floatval($result['amount']);
				}

				$builder = $comp_db->table($itemtxnnnn_tbl);
				$builder->select('count(item_txn_id) as transactions, sum(item_txn_qty) as qty, sum(item_txn_amount) as amount');
				$builder->where('item_id', $item_id);
				$builder->where('item_unit', $unit_id);
				$builder->whereIn('mat_cent_id', $mc_list);
				$builder->where('batch_id', 0);
				$builder->where('item_avail', 1);
				$builder->where('item_txn_drcr', 'c');
				$builder->where('item_txn_date >=', $from_date);
				$builder->where('item_txn_date <=', $to_date);
				// $builder->where('bo_id', $this->bo_id);
				$result = $builder->get()->getRowArray();

				if($result['transactions'] > 0){
					$credit_qty += floatval($result['qty']);
					$credit_amount += floatval($result['amount']);
				}

				foreach ($mc_list as $mc_key => $mc_value) {

					foreach ($bo_array as $bo_id) {

						$builder = $comp_db->table($itemtxnnnn_tbl);
						$builder->where('item_id', $item_id);
						$builder->where('item_unit', $unit_id);
						$builder->where('mat_cent_id', $mc_value);
						$builder->where('item_txn_date <=', $to_date);
						$builder->where('batch_id', 0);
						$builder->where('item_avail', 1);
						$builder->where('bo_id', $bo_id);	
						$builder->orderBy('item_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
						$builder->orderBy('item_txn_id', 'desc');
						$builder->limit(1);
						$transaction = $builder->get()->getRowArray();

						if($transaction){
							$balance_qty += floatval($transaction['item_bal_qty']);

							$builder = $comp_db->table($itemtxnval_tbl);
							$builder->where('item_id', $item_id);
							$builder->where('item_txn_id', $transaction['item_txn_id']);
							$builder->where('method_id', $val_id);
							$itemtxnval = $builder->get()->getRowArray();
							if($itemtxnval){
								$balance_amount += floatval($itemtxnval['item_value']);
							}
						}
						else{
							$builder = $comp_db->table($itmoppybal_tbl); 
							$builder->where('item_id', $item_id);
							$builder->where('item_unit', $unit_id);
							$builder->where('mat_cent_id', $mc_value);
							$builder->where('bo_id', $bo_id);
							$builder->where('batch_id', 0);	
							$itmoppybal = $builder->get()->getRowArray();
							if($itmoppybal){
								$balance_qty += floatval($itmoppybal['op_bal_qty']);

								$builder = $comp_db->table($itmoppyval_tbl); 
								$builder->where('item_id', $item_id);
								$builder->where('item_unit', $unit_id);
								$builder->where('mat_cent_id', $mc_value);
								
								$builder->where('bo_id', $bo_id);
								$builder->where('batch_id', 0);
								$builder->where('method_id', $val_id);
								$itmoppyval = $builder->get()->getRowArray();
								
								if($itmoppyval){
									$balance_amount += floatval($itmoppyval['op_bal_val']);
								}
							}
						}

					}
				}

				$builder = $comp_db->table($itemtxnval_tbl);
				$builder->select('sum(profit) as profit');
				$builder->where('item_id', $item_id);
				$builder->where('unit_id', $unit_id);
				$builder->whereIn('mat_cent_id', $mc_list);
				$builder->where('method_id', $val_id);
				$builder->where('voucher_date >=', $from_date);
				$builder->where('voucher_date <=', $to_date);
				$itemtxnval = $builder->get()->getRowArray();
				if($itemtxnval){
					$profit += floatval($itemtxnval['profit']);
				}
										
			}
		}

		$final = [
					'debit_qty' 			=> $debit_qty,
					'debit_amount' 		=> $debit_amount,
					'credit_qty' 			=> $credit_qty,
					'credit_amount' 	=> $credit_amount,
					'balance_qty' 		=> $balance_qty,
					'balance_amount' 	=> $balance_amount,
					'profit' 					=> $profit,
				];

		return $final;
	}

	function load_stock_status($mc_id_crs,$val,$to_date)
	{
		$item_list =$this->get_master_list('itemmaster');
		$units_list =$this->get_master_list('itmunitmst');
		$final = [];

		foreach ($item_list as $items_value) {

			foreach ($units_list as $units_value) {

				$grpmapping_tbl = 'grpmapping';
				$result = $this->db->table($grpmapping_tbl)
							->where('master_type', 'itemmaster')
							->where('crs_master_id', $items_value['id'])
							->orderBy('crs_master_id', 'asc')
							->get()->getResultArray(); 

				$status = false;
				$item_qty = 0;
				$item_value = 0;
				$op_item_qty = 0;
				$op_item_value = 0;
				$profit = 0;
				$comp = [];
				foreach ($result as $key => $value) {
					
					$comp_id = $value['comp_id'];

					$response = $this->load_stock_status_by_comp($comp_id,$items_value['id'],$units_value['id'],$mc_id_crs,$val,$to_date);

					if($response['status']){
						$status = true;
						$comp[] = grp_comp_details($comp_id )->comp_name;

						$item_qty 			+= $response['data']['item_qty'];
						$item_value 		+= $response['data']['item_value'];
						$op_item_qty 		+= $response['data']['op_item_qty'];
						$op_item_value 	+= $response['data']['op_item_value'];
						$profit 				+= $response['data']['profit'];
						
					}
						
				}

				if($status){

					$companies = implode(', ', $comp);

					$final[] = [
						'companies'	=> $companies,
						'item_id'		=> $items_value['id'],
						'item_name'	=> $items_value['label'],
						'unit_id'		=> $units_value['id'],
						'unit_name'	=> $units_value['label'],

						'item_qty' 			=> $item_qty,
						'item_value' 		=> $item_value,
						'op_item_qty' 	=> $op_item_qty,
						'op_item_value' => $op_item_value,
						'profit' 				=> $profit,
					];
				}
			}
		}

		return $final;
	}

	function load_stock_status_by_comp($comp_id,$item_id_crs,$unit_id_crs,$mc_id_crs,$val,$to_date)
	{
	
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;
		$comp_db = $this->externaldb->comp_db($comp_id);

		if($comp_fy_id == 0){
			return [
				'status'	=> false,
				'data'		=> []
			];
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$item_id_crs);

		if($item_id == 0){
			return [
				'status'	=> false,
				'data'		=> []
			];
		}

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		if($unit_id == 0){
			return [
				'status'	=> false,
				'data'		=> []
			];
		}

		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$exists = $comp_db->table($itmoppybal_tbl)
											->where('item_id',$item_id)
											->where('item_unit',$unit_id)
											->limit(1)
											->get()->getRowArray();
		if(!$exists){
			return [
				'status'	=> false,
				'data'		=> []
			];
		}

		$mc_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$mc_id_crs);

		$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;
		$mcmasternn_tbl = $comp_id.'_mcmasternn_'.$comp_fy_id;
		$itemtxnnnn_tbl = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
		$itemtxnval_tbl = $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;

		if($val == 0){
			$itemmaster = $comp_db->table($itemmaster_tbl)
														->where('item_id',$item_id)
														->get()->getRowarray();

			$val_id = $itemmaster['valmethod_id'];
		}
		else{
			$val_id = $val;
		}

		$builder = $comp_db->table($mcmasternn_tbl);
  	if($mc_id != 0)
  		$builder->where('mat_cent_id', $mc_id);
  	$mc_result = $builder->get()->getResultArray();

  	$bo_array = [];
		$bo_result = $comp_db->table($itmoppybal_tbl)
													->select('bo_id')
													->where('item_id', $item_id)
													->groupBy('bo_id')
													->get()->getResultArray();
		foreach ($bo_result as $bo_key => $bo_value) {
			$bo_array[] = $bo_value['bo_id'];
		}

  	$item_qty = 0;
		$item_value = 0;
		$op_item_qty = 0;
		$op_item_value = 0;
		$profit = 0;

  	foreach ($mc_result as $key => $value) {

  		foreach ($bo_array as $bo_id) {
  			$builder = $comp_db->table($itmoppybal_tbl); 
				$builder->where('item_id', $item_id);
				$builder->where('item_unit', $unit_id);
				$builder->where('mat_cent_id', $value['mat_cent_id']);
				$builder->where('bo_id', $bo_id);
				$builder->where('batch_id', 0);
				$itmoppybal = $builder->get()->getRowArray();
				if($itmoppybal){
					$op_item_qty += floatval($itmoppybal['op_bal_qty']);

					$builder = $comp_db->table($itmoppyval_tbl); 
					$builder->where('item_id', $item_id);
					$builder->where('item_unit', $unit_id);
					$builder->where('mat_cent_id', $value['mat_cent_id']);
					$builder->where('bo_id', $bo_id);
					$builder->where('batch_id', 0);
					$builder->where('method_id', $val_id);
					$itmoppyval = $builder->get()->getRowArray();

					if($itmoppyval){
						$op_item_value += floatval($itmoppyval['op_bal_val']);
					}
				}

				$builder = $comp_db->table($itemtxnnnn_tbl);
				$builder->where('item_id', $item_id);
				$builder->where('item_unit', $unit_id);
				$builder->where('mat_cent_id', $value['mat_cent_id']);
				$builder->where('item_txn_date <=', $to_date);
				$builder->where('batch_id', 0);
				$builder->where('item_avail', 1);
				$builder->where('bo_id', $bo_id);
				$builder->orderBy('item_txn_date', 'desc');
				$builder->orderBy('voucher_txn_id', 'desc');
				$builder->orderBy('item_txn_id', 'desc');
				$builder->limit(1);
				$transaction = $builder->get()->getRowArray();
				// echo "<pre>";print_r($transaction);exit;
				if($transaction){
					$transaction_status = 1;
					$item_qty += floatval($transaction['item_bal_qty']);

					$builder = $comp_db->table($itemtxnval_tbl);
					$builder->where('item_id', $item_id);
					$builder->where('item_txn_id', $transaction['item_txn_id']);
					$builder->where('method_id', $val_id);
					$itemtxnval = $builder->get()->getRowArray();
					if($itemtxnval){
						$item_value += floatval($itemtxnval['item_value']);
					}
				}
				else{
					if($itmoppybal){
						$item_qty += floatval($itmoppybal['op_bal_qty']);
						if($itmoppyval){
							$item_value += floatval($itmoppyval['op_bal_val']);
						}
					}
				}	

				$builder = $comp_db->table($itemtxnval_tbl);
				$builder->select('sum(profit) as profit');
				$builder->where('item_id', $item_id);
				$builder->where('unit_id', $unit_id);
				$builder->where('mat_cent_id', $value['mat_cent_id']);
				$builder->where('method_id', $val_id);
				$builder->where('voucher_date <=', $to_date);
				$itemtxnval = $builder->get()->getRowArray();
				if($itemtxnval){
					$profit += floatval($itemtxnval['profit']);
				}
  		}

	  		
  	}

		return [
				'status'	=> true,
				'data'		=> [
						'item_qty' => $item_qty,
						'item_value' => $item_value,
						'op_item_qty' => $op_item_qty,
						'op_item_value' => $op_item_value,
						'profit' => $profit,
				]
			];
	}

	function get_opn_stk($val = 0)
	{
		$item_list =$this->get_master_list('itemmaster');
		$units_list =$this->get_master_list('itmunitmst');
		$item_value = 0;

		foreach ($item_list as $items_value) {

			foreach ($units_list as $units_value) {

				$grpmapping_tbl = 'grpmapping';
				$result = $this->db->table($grpmapping_tbl)
							->where('master_type', 'itemmaster')
							->where('crs_master_id', $items_value['id'])
							->orderBy('crs_master_id', 'asc')
							->get()->getResultArray(); 

				

				foreach ($result as $key => $value) {
					
					$comp_id = $value['comp_id'];
					$item_value += $this->get_opn_stk_by_comp($comp_id,$items_value['id'],$units_value['id'],$val);
				}

			}
		}

		return $item_value;
	}

	function get_opn_stk_by_comp($comp_id,$item_id_crs,$unit_id_crs,$val)
	{
	
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;
		$comp_db = $this->externaldb->comp_db($comp_id);

		if($comp_fy_id == 0){
			return 0;
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$item_id_crs);

		if($item_id == 0){
			return 0;
		}

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		if($unit_id == 0){
			return 0;
		}

		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$exists = $comp_db->table($itmoppybal_tbl)
											->where('item_id',$item_id)
											->where('item_unit',$unit_id)
											->limit(1)
											->get()->getRowArray();
		if(!$exists){
			return 0;
		}


		$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;
		$mcmasternn_tbl = $comp_id.'_mcmasternn_'.$comp_fy_id;
		$itemtxnnnn_tbl = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
		$itemtxnval_tbl = $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;

		if($val == 0){
			$itemmaster = $comp_db->table($itemmaster_tbl)
														->where('item_id',$item_id)
														->get()->getRowarray();

			$val_id = $itemmaster['valmethod_id'];
		}
		else{
			$val_id = $val;
		}

		$builder = $comp_db->table($mcmasternn_tbl);
  	$mc_result = $builder->get()->getResultArray();

  	$bo_array = [];
		$bo_result = $comp_db->table($itmoppybal_tbl)
													->select('bo_id')
													->where('item_id', $item_id)
													->groupBy('bo_id')
													->get()->getResultArray();
		foreach ($bo_result as $bo_key => $bo_value) {
			$bo_array[] = $bo_value['bo_id'];
		}


		$op_item_qty = 0;
		$op_item_value = 0;

  	foreach ($mc_result as $key => $value) {

  		foreach ($bo_array as $bo_id) {
  			$builder = $comp_db->table($itmoppybal_tbl); 
				$builder->where('item_id', $item_id);
				$builder->where('item_unit', $unit_id);
				$builder->where('mat_cent_id', $value['mat_cent_id']);
				$builder->where('bo_id', $bo_id);
				$builder->where('batch_id', 0);
				$itmoppybal = $builder->get()->getRowArray();
				if($itmoppybal){
					$op_item_qty += floatval($itmoppybal['op_bal_qty']);

					$builder = $comp_db->table($itmoppyval_tbl); 
					$builder->where('item_id', $item_id);
					$builder->where('item_unit', $unit_id);
					$builder->where('mat_cent_id', $value['mat_cent_id']);
					$builder->where('bo_id', $bo_id);
					$builder->where('batch_id', 0);
					$builder->where('method_id', $val_id);
					$itmoppyval = $builder->get()->getRowArray();

					if($itmoppyval){
						$op_item_value += floatval($itmoppyval['op_bal_val']);
					}
				}
  		}
  	}

		return $op_item_value;
	}

	function get_clo_stk($to_date, $val = 0)
	{
		$item_list =$this->get_master_list('itemmaster');
		$units_list =$this->get_master_list('itmunitmst');
		$item_value = 0;

		foreach ($item_list as $items_value) {

			foreach ($units_list as $units_value) {

				$grpmapping_tbl = 'grpmapping';
				$result = $this->db->table($grpmapping_tbl)
							->where('master_type', 'itemmaster')
							->where('crs_master_id', $items_value['id'])
							->orderBy('crs_master_id', 'asc')
							->get()->getResultArray(); 

				foreach ($result as $key => $value) {
					
					$comp_id = $value['comp_id'];
					$item_value += $this->get_clo_stk_by_comp($comp_id,$items_value['id'],$units_value['id'],$val,$to_date);
				}

			}
		}

		return $item_value;
	}

	function get_clo_stk_by_comp($comp_id,$item_id_crs,$unit_id_crs,$val,$to_date)
	{
	
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;
		$comp_db = $this->externaldb->comp_db($comp_id);

		if($comp_fy_id == 0){
			return 0;
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$item_id_crs);

		if($item_id == 0){
			return 0;
		}

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		if($unit_id == 0){
			return 0;
		}

		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$exists = $comp_db->table($itmoppybal_tbl)
											->where('item_id',$item_id)
											->where('item_unit',$unit_id)
											->limit(1)
											->get()->getRowArray();
		if(!$exists){
			return 0;
		}


		$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;
		$mcmasternn_tbl = $comp_id.'_mcmasternn_'.$comp_fy_id;
		$itemtxnnnn_tbl = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
		$itemtxnval_tbl = $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;

		if($val == 0){
			$itemmaster = $comp_db->table($itemmaster_tbl)
														->where('item_id',$item_id)
														->get()->getRowarray();

			$val_id = $itemmaster['valmethod_id'];
		}
		else{
			$val_id = $val;
		}

		$builder = $comp_db->table($mcmasternn_tbl);
  	$mc_result = $builder->get()->getResultArray();

  	$bo_array = [];
		$bo_result = $comp_db->table($itmoppybal_tbl)
													->select('bo_id')
													->where('item_id', $item_id)
													->groupBy('bo_id')
													->get()->getResultArray();
		foreach ($bo_result as $bo_key => $bo_value) {
			$bo_array[] = $bo_value['bo_id'];
		}

  	$item_qty = 0;
		$item_value = 0;


  	foreach ($mc_result as $key => $value) {

  		foreach ($bo_array as $bo_id) {

				$builder = $comp_db->table($itemtxnnnn_tbl);
				$builder->where('item_id', $item_id);
				$builder->where('item_unit', $unit_id);
				$builder->where('mat_cent_id', $value['mat_cent_id']);
				$builder->where('item_txn_date <=', $to_date);
				$builder->where('batch_id', 0);
				$builder->where('item_avail', 1);
				$builder->where('bo_id', $bo_id);
				$builder->orderBy('item_txn_date', 'desc');
				$builder->orderBy('voucher_txn_id', 'desc');
				$builder->orderBy('item_txn_id', 'desc');
				$builder->limit(1);
				$transaction = $builder->get()->getRowArray();
	
				if($transaction){
					$transaction_status = 1;
					$item_qty += floatval($transaction['item_bal_qty']);

					$builder = $comp_db->table($itemtxnval_tbl);
					$builder->where('item_id', $item_id);
					$builder->where('item_txn_id', $transaction['item_txn_id']);
					$builder->where('method_id', $val_id);
					$itemtxnval = $builder->get()->getRowArray();
					if($itemtxnval){
						$item_value += floatval($itemtxnval['item_value']);
					}
				}
				else{

					$builder = $comp_db->table($itmoppybal_tbl); 
					$builder->where('item_id', $item_id);
					$builder->where('item_unit', $unit_id);
					$builder->where('mat_cent_id', $value['mat_cent_id']);
					$builder->where('bo_id', $bo_id);
					$builder->where('batch_id', 0);
					$itmoppybal = $builder->get()->getRowArray();
					if($itmoppybal){
						$item_qty += floatval($itmoppybal['op_bal_qty']);

						$builder = $comp_db->table($itmoppyval_tbl); 
						$builder->where('item_id', $item_id);
						$builder->where('item_unit', $unit_id);
						$builder->where('mat_cent_id', $value['mat_cent_id']);
						$builder->where('bo_id', $bo_id);
						$builder->where('batch_id', 0);
						$builder->where('method_id', $val_id);
						$itmoppyval = $builder->get()->getRowArray();

						if($itmoppyval){
							$item_value += floatval($itmoppyval['op_bal_val']);
						}
					}
					
				}	


  		}	
  	}

		return $item_value;
	}

	function get_itm_opn_balance($item_id_crs,$unit_id_crs,$mc_id_crs,$mc_grp_id_crs,$val)
	{
		$item_list =$this->get_master_list('itemmaster');
		$units_list =$this->get_master_list('itmunitmst');
		$item_qty = 0;
		$item_value = 0;

		$grpmapping_tbl = 'grpmapping';
		$result = $this->db->table($grpmapping_tbl)
					->where('master_type', 'itemmaster')
					->where('crs_master_id', $item_id_crs)
					->orderBy('crs_master_id', 'asc')
					->get()->getResultArray(); 

		foreach ($result as $key => $value) {
			
			$comp_id = $value['comp_id'];
			$itm_opn_bal = $this->get_itm_opn_balance_by_comp($comp_id,$item_id_crs,$unit_id_crs,$mc_id_crs,$mc_grp_id_crs,$val);

			$item_qty = $itm_opn_bal['item_qty'];
			$item_value = $itm_opn_bal['item_value'];
		}

		return [
			'item_qty'	 => $item_qty,
			'item_value' => $item_value,
		];
	}

	function get_itm_opn_balance_by_comp($comp_id,$item_id_crs,$unit_id_crs,$mc_id_crs,$mc_grp_id_crs,$val)
	{
	
		$comp_fy_id = grp_comp_details($comp_id)->comp_fy_id;
		$comp_db = $this->externaldb->comp_db($comp_id);

		if($comp_fy_id == 0){
			return 0;
		}

		$item_id = $this->get_master_id_comp('itemmaster',$comp_id,$comp_fy_id,$item_id_crs);

		if($item_id == 0){
			return 0;
		}

		$unit_id = $this->get_master_id_comp('itmunitmst',$comp_id,$comp_fy_id,$unit_id_crs);

		if($unit_id == 0){
			return 0;
		}

		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$exists = $comp_db->table($itmoppybal_tbl)
											->where('item_id',$item_id)
											->where('item_unit',$unit_id)
											->limit(1)
											->get()->getRowArray();
		if(!$exists){
			return 0;
		}

		$mc_id = $this->get_master_id_comp('mcmasternn',$comp_id,$comp_fy_id,$mc_id_crs);

		$mc_grp_id = $this->get_master_id_comp('mcgrpmstnn',$comp_id,$comp_fy_id,$mc_grp_id_crs);

		$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
		$itmoppybal_tbl = $comp_id.'_itmoppybal_'.$comp_fy_id;
		$itmoppyval_tbl = $comp_id.'_itmoppyval_'.$comp_fy_id;
		$mcmasternn_tbl = $comp_id.'_mcmasternn_'.$comp_fy_id;
		$itemtxnnnn_tbl = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
		$itemtxnval_tbl = $comp_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;

		if($val == 0){
			$itemmaster = $comp_db->table($itemmaster_tbl)
														->where('item_id',$item_id)
														->get()->getRowarray();

			$val_id = $itemmaster['valmethod_id'];
		}
		else{
			$val_id = $val;
		}

		$builder = $comp_db->table($mcmasternn_tbl);
		if($mc_id != 0 && $mc_grp_id == 0)
  		$builder->where('mat_cent_id', $mc_id);
  	if($mc_id == 0 && $mc_grp_id != 0)
  		$builder->where('mat_cent_grp_id', $mc_grp_id);
  	$mc_result = $builder->get()->getResultArray();

  	$bo_array = [];
		$bo_result = $comp_db->table($itmoppybal_tbl)
													->select('bo_id')
													->where('item_id', $item_id)
													->groupBy('bo_id')
													->get()->getResultArray();
		foreach ($bo_result as $bo_key => $bo_value) {
			$bo_array[] = $bo_value['bo_id'];
		}


		$op_item_qty = 0;
		$op_item_value = 0;

  	foreach ($mc_result as $key => $value) {

  		foreach ($bo_array as $bo_id) {
  			$builder = $comp_db->table($itmoppybal_tbl); 
				$builder->where('item_id', $item_id);
				$builder->where('item_unit', $unit_id);
				$builder->where('mat_cent_id', $value['mat_cent_id']);
				$builder->where('bo_id', $bo_id);
				$builder->where('batch_id', 0);
				$itmoppybal = $builder->get()->getRowArray();
				if($itmoppybal){
					$op_item_qty += floatval($itmoppybal['op_bal_qty']);

					$builder = $comp_db->table($itmoppyval_tbl); 
					$builder->where('item_id', $item_id);
					$builder->where('item_unit', $unit_id);
					$builder->where('mat_cent_id', $value['mat_cent_id']);
					$builder->where('bo_id', $bo_id);
					$builder->where('batch_id', 0);
					$builder->where('method_id', $val_id);
					$itmoppyval = $builder->get()->getRowArray();

					if($itmoppyval){
						$op_item_value += floatval($itmoppyval['op_bal_val']);
					}
				}
  		}
  	}

		return [
			'item_qty' 		=> $op_item_qty,
			'item_value' 	=> $op_item_value,
		];
	}


  function test()
  {
  	echo $this->get_opn_stk();	
  }

}