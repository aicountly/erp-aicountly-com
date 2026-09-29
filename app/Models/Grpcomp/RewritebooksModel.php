<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\Admin\TransactionModel;
use App\Libraries\externaldb;
class RewritebooksModel extends Model	{
	
 public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
       $this->TransactionModel  = new TransactionModel();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->erp_db        = $this->externaldb->erp_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');	   
    }


   public function get_account_details(){
	     
	   $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   $builder = $this->db->table($account_master_tbl); 
		$builder->select('acc_id as id, acc_name as name');
		$result = $builder->get()->getResultArray();
		
		return $result;
   }

   public function get_bill_sundry_details(){
      
	  	$billsundry_tbl  = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	  	$builder  =  $this->db->table($billsundry_tbl);
	   $builder->select('bill_sundry_name as name, bill_sundry_id as id');
	   $result = $builder->get()->getResultArray();
		
		return $result;
   }

   public function get_bill_by_bill_details(){
      
	  	$bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
	  	$builder  =  $this->db->table($bill_mst_tbl);
	   $builder->select('bills_ref_name as name, bills_ref_id as id');
	   $result = $builder->get()->getResultArray();
		
		return $result;
   }

   public function get_cost_center_details(){
      
	  	$cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
	  	$builder  =  $this->db->table($cc_mst_tbl);
	   $builder->select('cc_name as name, cc_id as id');
	   $result = $builder->get()->getResultArray();
		
		return $result;
   }

   public function get_item_details(){
      
	  	$item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	  	$builder  =  $this->db->table($item_master_tbl);
	   $builder->select('item_name as name, item_id as id');
	   $result = $builder->get()->getResultArray();
		
		return $result;
   }

   public function get_voucher_type_details(){
	     
	   $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	   $builder = $this->db->table($voucher_type_tbl); 
		$builder->select('voucher_type_id as id, comp_vch_type as name');
		$result = $builder->get()->getResultArray();
		
		return $result;
   }

   public function check_banking_vouchers($voucher_type_id)
   {
   	$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
   	$builder = $this->db->table($voucher_tbl); 
		$builder->where('voucher_type_id', $voucher_type_id);
		$result = $builder->get()->getResultArray();

		$errors = [];
		foreach ($result as $key => $value) 
		{
			$status = 1;
			$flag = 0;

			try{
				$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($comp_txn_tbl); 
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$builder->whereIn('master_id_type', ['acc','bsd','aco','bso']);
				$result2 = $builder->get()->getResultArray();

				$credit_total = 0;
				$debit_total = 0;
				
				foreach ($result2 as $key2 => $value2) {

					if($value2['master_id_type'] == 'acc')
					{
						$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl); 
						$builder->where('txn_id', $value2['txn_id']);
						$acc_txn = $builder->get()->getRowArray();
						if($acc_txn){
							if($acc_txn['acc_txn_drcr'] == 'c')
								$credit_total += parseAmount($acc_txn['acc_txn_amount']);
							else
								$debit_total += parseAmount($acc_txn['acc_txn_amount']);
						}
						else{
							$status = 0;
							$flag = 2;
						}
					}
					
					if($value2['master_id_type'] == 'aco')
					{
						$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
		    			$builder->where('txn_id', $value2['txn_id']);
		            $account = $builder->get()->getRowArray();
		            if($account){
		              	if($account['acc_oth_txn_drcr'] == 'c')
		                  $credit_total += parseAmount($account['acc_oth_txn_amount']);
		              	else
		                  $debit_total += parseAmount($account['acc_oth_txn_amount']);
	              	}
	              	else{
							$status = 0;
							$flag = 3;
						}
					}

					if($value2['master_id_type'] == 'bsd')
					{
						$bsd_txn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($bsd_txn_tbl); 
						$builder->where('txn_id', $value2['txn_id']);
						$acc_txn = $builder->get()->getRowArray();
						if($acc_txn){
							if($acc_txn['sundry_txn_drcr'] == 'c')
								$credit_total += parseAmount($acc_txn['sundry_txn_amount']);
							else
								$debit_total += parseAmount($acc_txn['sundry_txn_amount']);
						}
						else{
							$status = 0;
							$flag = 4;
						}
					}
					if($value2['master_id_type'] == 'bso')
					{
						$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
                  $builder = $this->db->table($table);
        				$builder->where('txn_id', $value2['txn_id']);
                  $account = $builder->get()->getRowArray();
                  if($account){
                     if($account['acc_oth_txn_drcr'] == 'c')
		                  $credit_total += parseAmount($account['acc_oth_txn_amount']);
		              	else
		                  $debit_total += parseAmount($account['acc_oth_txn_amount']);
						}
						else{
							$status = 0;
							$flag = 5;
						}
					}
				}

				if(parseAmount($credit_total) != parseAmount($debit_total)){
					$status = 0;
					$flag = 6;
				}
			}
			catch (\Exception $e) {
				$status = 0;
				$flag = 1;
			}

			if($status == 0){
				$errors[] = [
					'voucher_txn_id' => $value['voucher_txn_id'],
					'comp_vch_no' => $value['comp_vch_no'],
					'voucher_date' => date('d-m-Y', strtotime($value['voucher_date'])),
					'flag' => $flag,
					'credit_total' => $credit_total,
					'debit_total' => $debit_total,
				];
			}
			
		}
		return $errors;
   }

   public function check_single_voucher($voucher_txn_id)
   {
   	$status = 1;

   	$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
   	$builder = $this->db->table($voucher_tbl); 
		$builder->where('voucher_txn_id', $voucher_txn_id);
		$result = $builder->get()->getRowArray();

		if($result)
		{
			try{
				$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($comp_txn_tbl); 
				$builder->where('voucher_txn_id', $result['voucher_txn_id']);
				$builder->whereIn('master_id_type', ['acc','bsd']);
				$result2 = $builder->get()->getResultArray();

				$debit_total = 0;
				$credit_total = 0;
				foreach ($result2 as $key2 => $value2) {

					if($value2['master_id_type'] == 'acc')
					{
						$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl); 
						$builder->where('txn_id', $value2['txn_id']);
						$acc_txn = $builder->get()->getRowArray();
						if($acc_txn){
							if($acc_txn['acc_txn_drcr'] == 'c')
								$credit_total += parseAmount($acc_txn['acc_txn_amount']);
							else
								$debit_total += parseAmount($acc_txn['acc_txn_amount']);
						}
						else{
							$status = 0;
						}
					}
					
					if($value2['master_id_type'] == 'aco')
					{
						$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
		    			$builder->where('txn_id', $value2['txn_id']);
		            $account = $builder->get()->getRowArray();
		            if($account){
		              	if($account['acc_oth_txn_drcr'] == 'c')
		                  $credit_total += parseAmount($account['acc_oth_txn_amount']);
		              	else
		                  $debit_total += parseAmount($account['acc_oth_txn_amount']);
	              	}
	              	else{
							$status = 0;
						}
					}

					if($value2['master_id_type'] == 'bsd')
					{
						$bsd_txn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($bsd_txn_tbl); 
						$builder->where('txn_id', $value2['txn_id']);
						$acc_txn = $builder->get()->getRowArray();
						if($acc_txn){
							if($acc_txn['sundry_txn_drcr'] == 'c')
								$credit_total += parseAmount($acc_txn['sundry_txn_amount']);
							else
								$debit_total += parseAmount($acc_txn['sundry_txn_amount']);
						}
						else{
							$status = 0;
						}
					}
					if($value2['master_id_type'] == 'bso')
					{
						$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
                  $builder = $this->db->table($table);
        				$builder->where('txn_id', $value2['txn_id']);
                  $account = $builder->get()->getRowArray();
                  if($account){
                     if($account['acc_oth_txn_drcr'] == 'c')
		                  $credit_total += parseAmount($account['acc_oth_txn_amount']);
		              	else
		                  $debit_total += parseAmount($account['acc_oth_txn_amount']);
						}
						else{
							$status = 0;
						}
					}
				}

				if(parseAmount($credit_total) != parseAmount($debit_total)){
					$status = 0;
				}
			}
			catch (\Exception $e) {
			  $status = 0;
			}
		}
		else{
			$status = 2; //Voucher Deleted
		}

		
			
		return $status;
   }

   function get_master_db_details()
   {
   	$list = [];

		$builder = $this->erp_db->table("aictlyerp_compidgenr_univdb"); 
		$builder->select('aictlyerp_compidgenr_univdb.*, aictlyerp_compmastern_univdb.comp_code');
		$builder->join('aictlyerp_compmastern_univdb', 'aictlyerp_compmastern_univdb.comp_id = aictlyerp_compidgenr_univdb.comp_id');
		$builder->where('aictlyerp_compmastern_univdb.comp_id', 1);
		$result = $builder->get()->getRowArray();

		
		if($result)
		{
			$external_db    = $this->externaldb->single_company_db($result['comp_code']);

			$tables = $external_db->query('SHOW TABLES')->getResultArray();
			
			foreach ($tables as $key => $value) {
				$table_name = array_values($value)[0];

				if(substr_count($table_name, '_') <= 2){
					$list[] = $table_name;
				}
			}
		}

		// echo "<pre>";print_r(array_values($list));exit;
		return $list;
   }

	public function get_bbb_accounts($group_array){
	     
	   $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   $builder = $this->db->table($account_master_tbl); 
		$builder->select('acc_id as id, acc_name as name');
		$builder->whereIn('acc_grp_id', $group_array);
		$result = $builder->get()->getResultArray();
		
		return $result;
   }

   public function get_bbb_vouchers($account_id)
   {
   	$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

	    
		$builder = $this->db->table($voucher_tbl);
		$builder->select($voucher_tbl.'.voucher_txn_id as id, CONCAT(comp_vch_type, " Voucher No. ", comp_vch_no) AS name, '.$voucher_tbl.'.voucher_type_id, DATE_FORMAT(voucher_date, "%d-%m-%Y") as voucher_date');
		$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
		$builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		$builder->where('master_id', $account_id);
		$builder->where('master_id_type', "acc");
		$builder->orderBy('voucher_date');
		$builder->orderBy($voucher_tbl.'.voucher_txn_id');

		$list = $builder->get()->getResultArray();

		return $list;
   }

   public function verify_bbb_vouchers($account_id, $voucher_txn_id)
   {
   	$balance = 0;

   	$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
   	$builder = $this->db->table($voucher_tbl); 
		$builder->where('voucher_txn_id', $voucher_txn_id);
		$exists = $builder->get()->getRowArray();

		if(!$exists)
			return 2;

   	$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
   	$builder = $this->db->table($acc_txn_tbl);
   	$builder->select('*');
   	$builder->where('acc_id', $account_id);
   	$builder->where('voucher_txn_id', $voucher_txn_id);
   	$result = $builder->get()->getResultArray();

   	foreach ($result as $key => $value) {
   		if($value['acc_txn_drcr'] == 'c')
   			$balance += -parseAmount($value['acc_txn_amount']);
   		if($value['acc_txn_drcr'] == 'd')
   			$balance += parseAmount($value['acc_txn_amount']);
   	}

   	$balance2 = 0;
   	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
   	$builder = $this->db->table($bill_txn_tbl);
   	$builder->select('*');
   	$builder->where('acc_id', $account_id);
   	$builder->where('voucher_txn_id', $voucher_txn_id);
   	$result2 = $builder->get()->getResultArray();

   	foreach ($result2 as $key2 => $value2) {
   		if($value2['bills_txn_drcr'] == 'C')
   			$balance2 += -parseAmount($value2['bills_txn_amt']);
   		if($value2['bills_txn_drcr'] == 'D')
   			$balance2 += parseAmount($value2['bills_txn_amt']);
   	}

   	if($balance != $balance2)
   		return 0;

   	return 1;

   	
   }
}