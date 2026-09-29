<?php
namespace App\Models\Grpcomp;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Grpcomp\TransactionModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class ReportsModel2 extends Model	{
	
	public function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		
		$this->erp_db   =  $this->externaldb->erp_db();
		$this->aicountly_db   =  $this->externaldb->aicountly_db();
		$this->session       = \Config\Services::session();


		$this->TransactionModel  = new TransactionModel();
		$this->CommonModel   = new CommonModel();	   
		$this->enc_string    = new enc_string();
		$this->bo_id         =1;
	} 

		// Cash & Cash Equivalents		23
		// Bank OD/OCC A/c				21
		// Trade Receivables				22 (sundry debitors)
		// Trade Payable					16 (sundry creditors)

		// PURCHASE 				7 (parent_id)
		// DIRECT EXPENSE			11 (parent_id)
		// INDIRECT EXPENSE		13 (parent_id)

	function get_all_accounts()
	{
		$ses_grp_id           = $this->session->get('ses_grp_id');

		$builder = $this->erp_db->table('aictlyerp_grpcomstid_univdb grpcomst');
		$builder->select(array('grpcomst.crs_master_id as id','grpcomst.crs_master_name as label','grpcomst.crs_master_name as value'));
		$builder->where('grpcomst.grpco_id', $ses_grp_id);
		$builder->where('grpcomst.crs_master_type','acc');	
		$builder->orderBy('grpcomst.crs_master_name');
		$accounts = $builder->get()->getResultArray();

		return $accounts;
	}

	function get_all_mc(){

		$ses_grp_id           = $this->session->get('ses_grp_id');

		$builder = $this->erp_db->table('aictlyerp_grpcomstid_univdb grpcomst');
		$builder->select(array('grpcomst.crs_master_id as id','grpcomst.crs_master_name as label','grpcomst.crs_master_name as value'));
		$builder->where('grpcomst.grpco_id', $ses_grp_id);
		$builder->where('grpcomst.crs_master_type','mcmst');	
		$builder->orderBy('grpcomst.crs_master_name');
		$list = $builder->get()->getResultArray();

		return $list;	
	}

	function get_all_items(){

		$ses_grp_id           = $this->session->get('ses_grp_id');

		$builder = $this->erp_db->table('aictlyerp_grpcomstid_univdb grpcomst');
		$builder->select(array('grpcomst.crs_master_id as id','grpcomst.crs_master_name as label','grpcomst.crs_master_name as value'));
		$builder->where('grpcomst.grpco_id', $ses_grp_id);
		$builder->where('grpcomst.crs_master_type','itm');	
		$builder->orderBy('grpcomst.crs_master_name');
		$list = $builder->get()->getResultArray();

		return $list;
	}

	function get_grpmapping_info($crs_master_id, $comp_id, $master_type)
	{
		$ses_grp_id           = $this->session->get('ses_grp_id');

		$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb');
		$builder->where('grpco_id', $ses_grp_id);
		$builder->where('crs_master_id', $crs_master_id);
		$builder->where('comp_id', $comp_id);
		$builder->where('master_type', $master_type);	
		$result = $builder->get()->getRowArray();

		return $result;
	}

	function get_grpcomst_info($id, $type)
	{
		$ses_grp_id           = $this->session->get('ses_grp_id');

		$builder = $this->erp_db->table('aictlyerp_grpcomstid_univdb grpcomst');
		$builder->where('grpcomst.grpco_id', $ses_grp_id);
		$builder->where('grpcomst.crs_master_id', $id);
		$builder->where('grpcomst.crs_master_type', $type);	
		$result = $builder->get()->getRowArray();

		return $result;
	}

	function get_company_info($company_id){	 
		return $this->aicountly_db->table('aicountly_cmpmastern_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
	}

	public function load_accounts_ledger_condensed($crs_master_id, $limit, $from_date, $to_date,$pq_curPage, $search)
	{
		$get_grpmp_info          =  $this->get_grpcomst_info($crs_master_id, 'acc');    
		$comp_id_string          =  $get_grpmp_info['comp_id'] ?? '';

		$arr = explode(',', $comp_id_string);
		$companies = [];

		foreach ($arr as $key => $value) {
			if(trim($value) != ''){
				$company_info = $this->get_company_info($value);
				$grpmapping_info = $this->get_grpmapping_info($crs_master_id, $value, 'acc');


				$companies[] = array(
					'company_id'     	=> $value,
					'comp_code'   	 	=> $company_info['comp_code'],
					'comp_fy_id'  	 	=> $company_info['comp_fy_id'],
					'comp_name'  	 	=> $company_info['comp_name'],
					'comp_short_name'  => $company_info['comp_short_name'],
					'account_id'  		=> $grpmapping_info['master_id'] ?? 0,
				); 
			}
		}

		$final_result = [];

		foreach ($companies as  $company) {
			$db =  $this->externaldb->single_company_db($company['comp_code']);

			$account_id = $company['account_id'];
			if($account_id != 0){

				$voucher_tbl = $company['company_id'].'_vhtxnconso_'.$company['comp_fy_id'];
				$voucher_type_tbl = $company['company_id'].'_cmpvchtype_'.$company['comp_fy_id'];
				$voucher_series_tbl = $company['company_id'].'_cmpvchseri_'.$company['comp_fy_id'];
				$comp_txn_tbl = $company['company_id'].'_comptxnmst_'.$company['comp_fy_id'];
				$acc_txn_tbl = $company['company_id'].'_accnttxnnn_'.$account_id.'_'.$company['comp_fy_id'];
				$long_narr_tbl = $company['company_id'].'_long_narrn_'.$company['comp_fy_id'];

				$builder = $db->table($voucher_tbl);
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

				$builder->where($acc_txn_tbl.'.bo_id', $this->bo_id);

				if($search != ''){
					$builder->groupStart();
					$builder->like('acc_txn_date', $search);
					$builder->orLike('acc_txn_amount', $search);
					$builder->orLike('acc_bal', $search);
					$builder->orLike('comp_vch_type', $search);
					$builder->orLike('comp_vch_no', $search);
					$builder->orLike('vch_narr', $search);
					$builder->groupEnd();
				}

				$builder->orderBy('voucher_date');
				$builder->orderBy('voucher_txn_id');
				$builder->orderBy('acc_txn_id');
				$result = $builder->get()->getResultArray();

				if($result){
					foreach($result as $key => $value){

						$voucher_type_id = $value['voucher_type_id'];
						$voucher_type = $value['comp_vch_type'];
						$voucher_no = $value['comp_vch_no'];
						$voucher_date      = $value['voucher_date'];//date("d-m-Y", strtotime($value['voucher_date']));
						$account_name = '';
						$short_narration = '';

						$debit = '';
						$credit = '';
						$credit_total = 0;
						$debit_total = 0;

						$account_bal  = '';
						$balance_type = '';

						if($value['acc_txn_drcr']=='d'){
							$debit = formatAmount($value['acc_txn_amount']);
							$debit_total = $value['acc_txn_amount'];
						}
						if($value['acc_txn_drcr']=='c'){
							$credit = formatAmount($value['acc_txn_amount']);
							$credit_total = $value['acc_txn_amount'];
						}


						
						$balance_type = '';	
						if($value['acc_bal'] < 0){
							$account_bal = formatAmount(abs($value['acc_bal']));
							$balance_type = 'CR.';
						}
						if($value['acc_bal'] >= 0){
							$account_bal = formatAmount($value['acc_bal']);
							$balance_type = 'DR.';
						}

						$builder = $db->table($comp_txn_tbl);
						$builder->where('voucher_txn_id', $value['voucher_txn_id']);
						$builder->where('master_id_type', 'acc');
						$builder->where('master_id !=', $account_id);
						$builder->orderBy('txn_id', 'asc');
						$builder->limit(1);
						$comp_txns = $builder->get()->getRowArray();

						if($comp_txns){
							$account_master_tbl = $company['company_id'].'_acctmaster_'.$company['comp_fy_id'];
							$account_info = $db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();
							$account_name =  $account_info['acc_name'] ?? '';
						}

						$final_result[]    = array(
							"chkbx" => "<label><input name='voucher_ids[]' class='checkbox hidden accounts_row voucher_row' data-id='".$value['voucher_type_id'].'||'.$value['voucher_txn_id']."' value='".$value['voucher_type_id'].'||'.$value['voucher_txn_id']."' type='checkbox' /></label>",

							'voucher_no'				=> $value['comp_vch_no'],
							'voucher_txn_id'			=> $value['voucher_txn_id'],
							'comp_vch_series_id' 	=> $value['comp_vch_series_id'],
							'voucher_type_id'			=> $value['voucher_type_id'],
							'txn_date'					=> $voucher_date,
							'voucher_type'				=> $value['comp_vch_type'],
							'account_name'				=> $account_name,
							'credit'						=> $credit,
							'debit'						=> $debit,
							'balance'					=> $account_bal,
							'balance_type'				=> $balance_type,
							'credit_total'				=> $credit_total,
							'debit_total'				=> $debit_total,
							'short_narration'   		=> $value['vch_narr'],
							'company_id'   			=> $company['company_id'],
							'comp_name'   				=> $company['comp_name'],

						);
					}
				}

			}

		}

		return $final_result;
	}

	public function load_bill_sundry_ledger_condensed($crs_master_id, $limit, $from_date, $to_date,$pq_curPage, $search)
	{
		$get_grpmp_info          =  $this->get_grpcomst_info($crs_master_id, 'bsd');    
		$comp_id_string          =  $get_grpmp_info['comp_id'] ?? '';

		$arr = explode(',', $comp_id_string);
		$companies = [];

		foreach ($arr as $key => $value) {
			if(trim($value) != ''){
				$company_info = $this->get_company_info($value);
				$grpmapping_info = $this->get_grpmapping_info($crs_master_id, $value, 'acc');


				$companies[] = array(
					'company_id'     	=> $value,
					'comp_code'   	 	=> $company_info['comp_code'],
					'comp_fy_id'  	 	=> $company_info['comp_fy_id'],
					'comp_name'  	 	=> $company_info['comp_name'],
					'comp_short_name'  => $company_info['comp_short_name'],
					'account_id'  		=> $grpmapping_info['master_id'] ?? 0,
				); 
			}
		}

		$final_result = [];

		foreach ($companies as  $company) {
			$db =  $this->externaldb->single_company_db($company['comp_code']);

			$account_id = $company['account_id'];
			if($account_id != 0){

				$voucher_tbl = $company['company_id'].'_vhtxnconso_'.$company['comp_fy_id'];
				$voucher_type_tbl = $company['company_id'].'_cmpvchtype_'.$company['comp_fy_id'];
				$voucher_series_tbl = $company['company_id'].'_cmpvchseri_'.$company['comp_fy_id'];
				$comp_txn_tbl = $company['company_id'].'_comptxnmst_'.$company['comp_fy_id'];
				$acc_txn_tbl = $company['company_id'].'_sundrytxnn_'.$account_id.'_'.$company['comp_fy_id'];
				$long_narr_tbl = $company['company_id'].'_long_narrn_'.$company['comp_fy_id'];

				$builder = $db->table($voucher_tbl);
				$builder->select($voucher_tbl.'.*');
				$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
				$builder->select($voucher_type_tbl.'.comp_vch_type');
				$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
				$builder->select($voucher_series_tbl.'.comp_vch_series');
				$builder->join($comp_txn_tbl.' ctm1', 'ctm1.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm1.master_id_type = "bsd"');
				$builder->select('ctm1.master_id, ctm1.master_id_type');
				$builder->join($acc_txn_tbl, $acc_txn_tbl.'.txn_id  = ctm1.txn_id');		
				$builder->select('bill_sundry_id, bills_txn_date, bills_txn_drcr, bills_txn_amt, bills_txn_bal');
				$builder->join($comp_txn_tbl.' ctm2', 'ctm2.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.master_id_type = "nrr"', 'left');
				$builder->join($long_narr_tbl, $long_narr_tbl.'.vch_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.txn_id = '.$long_narr_tbl.'.txn_id', 'left');
				$builder->select('vch_narr');	
				$builder->where('bill_sundry_id', $account_id);				
				if($from_date!='')
					$builder->where('voucher_date >=', $from_date);
				if($to_date!='')
					$builder->where('voucher_date <=', $to_date);

				$builder->where($acc_txn_tbl.'.bo_id', $this->bo_id);

				

				$builder->orderBy('voucher_date');
				$builder->orderBy('voucher_txn_id');
				$builder->orderBy('bills_txn_id');
				$result = $builder->get()->getResultArray();

				if($result){
					foreach($result as $key => $value){

						$voucher_type_id = $value['voucher_type_id'];
						$voucher_type = $value['comp_vch_type'];
						$voucher_no = $value['comp_vch_no'];
						$voucher_date      = $value['voucher_date'];//date("d-m-Y", strtotime($value['voucher_date']));
						$account_name = '';
						$short_narration = '';

						$debit = '';
						$credit = '';
						$credit_total = 0;
						$debit_total = 0;

						$account_bal  = '';
						$balance_type = '';

						if($value['bills_txn_drcr']=='d'){
							$debit = formatAmount($value['bills_txn_amt']);
							$debit_total = $value['bills_txn_amt'];
						}
						if($value['bills_txn_drcr']=='c'){
							$credit = formatAmount($value['bills_txn_amt']);
							$credit_total = $value['bills_txn_amt'];
						}


						
						$balance_type = '';	
						if($value['bills_txn_bal'] < 0){
							$account_bal = formatAmount(abs($value['bills_txn_bal']));
							$balance_type = 'CR.';
						}
						if($value['bills_txn_bal'] >= 0){
							$account_bal = formatAmount($value['bills_txn_bal']);
							$balance_type = 'DR.';
						}

						

						$final_result[]    = array(
							"chkbx" => "<label><input name='voucher_ids[]' class='checkbox hidden accounts_row voucher_row' data-id='".$value['voucher_type_id'].'||'.$value['voucher_txn_id']."' value='".$value['voucher_type_id'].'||'.$value['voucher_txn_id']."' type='checkbox' /></label>",

							'voucher_no'				=> $value['comp_vch_no'],
							'voucher_txn_id'			=> $value['voucher_txn_id'],
							'comp_vch_series_id' 	=> $value['comp_vch_series_id'],
							'voucher_type_id'			=> $value['voucher_type_id'],
							'txn_date'					=> $voucher_date,
							'voucher_type'				=> $value['comp_vch_type'],

							'credit'						=> $credit,
							'debit'						=> $debit,
							'balance'					=> $account_bal,
							'balance_type'				=> $balance_type,
							'credit_total'				=> $credit_total,
							'debit_total'				=> $debit_total,
							'short_narration'   		=> $value['vch_narr'],
							'company_id'   			=> $company['company_id'],
							'comp_name'   				=> $company['comp_name'],

						);
					}
				}

			}

		}

		return $final_result;
	}

	public function load_item_ledger($crs_master_id, $unit_id, $mc_crs_master_id, $mc_grp_id, $val, $limit, $from_date, $to_date,$pq_curPage, $search)
	{	

		$get_grpmp_info          =  $this->get_grpcomst_info($crs_master_id,'itm');    
		$comp_id_string          =  $get_grpmp_info['comp_id'] ?? '';

		$arr = explode(',', $comp_id_string);
		$companies = [];

		foreach ($arr as $key => $value) {
			if(trim($value) != ''){
				$company_info = $this->get_company_info($value);
				$grpmapping_info = $this->get_grpmapping_info($crs_master_id, $value, 'itm');


				$companies[] = array(
					'company_id'     	=> $value,
					'comp_code'   	 	=> $company_info['comp_code'],
					'comp_fy_id'  	 	=> $company_info['comp_fy_id'],
					'comp_name'  	 	=> $company_info['comp_name'],
					'comp_short_name'  => $company_info['comp_short_name'],
					'item_id'  			=> $grpmapping_info['master_id'] ?? 0,
				); 
			}
		}

		$final_result = [];

		foreach ($companies as  $company) {
			$db =  $this->externaldb->single_company_db($company['comp_code']);

			$item_id = $company['item_id'];
			if($item_id != 0){

				$mc_id = 0;
				if($mc_crs_master_id != 0){
					$grpmapping_info = $this->get_grpmapping_info($mc_crs_master_id, $company['company_id'], 'mcmst');
					$mc_id = $grpmapping_info['master_id'] ?? 0;
				}

				$val_id = 0;
				if($val != 0){
					$val_id = $val;
				}
				else{
					$item_master_tbl = $company['company_id'].'_itemmaster_'.$company['comp_fy_id'];
					$item_info = $db->table($item_master_tbl)->where('item_id', $item_id)->get()->getRowArray();
					$val_id = $item_info['valmethod_id'] ?? 0;
				}

				$voucher_tbl = $company['company_id'].'_vhtxnconso_'.$company['comp_fy_id'];
				$voucher_type_tbl = $company['company_id'].'_cmpvchtype_'.$company['comp_fy_id'];
				$voucher_series_tbl = $company['company_id'].'_cmpvchseri_'.$company['comp_fy_id'];
				$comp_txn_tbl = $company['company_id'].'_comptxnmst_'.$company['comp_fy_id'];
				$itm_txn_tbl = $company['company_id'].'_itemtxnnnn_'.$item_id.'_'.$company['comp_fy_id'];
				$itemtxnval_tbl  = $company['company_id'].'_itemtxnval_'.$item_id.'_'.$company['comp_fy_id'];

				$itmoppybal_tbl = $company['company_id'].'_itmoppybal_'.$company['comp_fy_id'];
				$itmoppyval_tbl = $company['company_id'].'_itmoppyval_'.$company['comp_fy_id'];

				$builder = $db->table($voucher_tbl);
				$builder->select($voucher_tbl.'.*');
				$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
				$builder->select($voucher_type_tbl.'.comp_vch_type');
				$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
				$builder->select($voucher_series_tbl.'.comp_vch_series');
				$builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND master_id_type = "itm"');
				$builder->select('master_id, master_id_type');
				$builder->join($itm_txn_tbl, $itm_txn_tbl.'.txn_id  = '.$comp_txn_tbl.'.txn_id');		
				$builder->select('item_txn_id, item_id, item_txn_date, item_txn_drcr, item_txn_qty, item_bal_qty, item_txn_amount, item_unit, batch_id');

				$builder->where($itm_txn_tbl.'.item_id', $item_id);
				if($unit_id != 0)
					$builder->where($itm_txn_tbl.'.item_unit', $unit_id);
				if($mc_id != 0)
					$builder->where($itm_txn_tbl.'.mat_cent_id', $mc_id);

				if($from_date!='')
					$builder->where('voucher_date >=', $from_date);
				if($to_date!='')
					$builder->where('voucher_date <=', $to_date);

				$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
				$builder->orderBy('voucher_date');
				$builder->orderBy('voucher_txn_id');
				$builder->orderBy('item_txn_id');
				$result = $builder->get()->getResultArray();

				if($result){
					foreach($result as $key => $value){

							$voucher_date  = $value['voucher_date'];//date("d-m-Y", strtotime($value['voucher_date']));

							$particulars = '';
							$unit_name = '';

							$inward_amount = 0;
							$outward_amount = 0;

							$inward_qty = 0;
							$outward_qty = 0;

							$closing_qty  = 0;
							$valuation = 0;

							if($value['item_txn_drcr']=='d'){
								$inward_qty = floatval($value['item_txn_qty']);
								$inward_amount = floatval($value['item_txn_amount']);
							}
							if($value['item_txn_drcr']=='c'){
								$outward_qty = floatval($value['item_txn_qty']);
								$outward_amount = floatval($value['item_txn_amount']);
							}

							$closing_qty = floatval($value['item_bal_qty']);

							$item_master_tbl = $company['company_id'].'_itemmaster_'.$company['comp_fy_id'];
							$item_info = $db->table($item_master_tbl)->where('item_id', $item_id)->get()->getRowArray();
							if($item_info){
								$acc_id = 0;
								if($value['item_txn_drcr']=='d')
									$acc_id = $item_info['item_pur_acc'];

								if($value['item_txn_drcr']=='c')
									$acc_id = $item_info['item_sales_acc'];


								$account_master_tbl = $company['company_id'].'_acctmaster_'.$company['comp_fy_id'];
								$account_info = $db->table($account_master_tbl)->where('acc_id', $acc_id)->get()->getRowArray();
								$particulars = $account_info['acc_name'] ?? '';
							}

							$item_unit_tbl = $company['company_id'].'_itmunitmst_'.$company['comp_fy_id'];
							$item_unit_info = $db->table($item_unit_tbl)->where('unit_id', $value['item_unit'])->get()->getRowArray();
							if(isset($item_unit_info['item_unit']))
								$unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');

							$builder = $db->table($itemtxnval_tbl);
							$builder->where('item_id', $value['item_id']);
							$builder->where('item_txn_id', $value['item_txn_id']);
							$builder->where('method_id', $val_id);
							$itemtxnval = $builder->get()->getRowArray();
							if($itemtxnval){
								$valuation = floatval($itemtxnval['item_value']);
							}

							$voucher = $value['comp_vch_type'] . ' (No. '.$value['comp_vch_no'].')';

							$pnl = 0;
							$pnl2 = 0;
							if($val_id == 1 && $value['item_txn_drcr']=='c'){

								$last_quantity = 0;
								$last_value = 0;

								//check last debit transaction
								$builder = $db->table($itm_txn_tbl);
								$builder->select('item_txn_id, item_bal_qty'); 
								$builder->where('item_id', $value['item_id']);
								$builder->where('item_unit', $value['item_unit']);
								$builder->where('mat_cent_id', $value['mat_cent_id']);
								$builder->where('batch_id', $value['batch_id']);
								$builder->where('item_txn_drcr', 'd');
								$builder->where('item_avail', 1);

								$builder->groupStart();
								$builder->where('item_txn_date <', $value['item_txn_date']);
								$builder->orGroupStart();
								$builder->where('item_txn_date', $value['item_txn_date']);
								$builder->where('voucher_txn_id <', $value['voucher_txn_id']);
								$builder->orGroupStart();
								$builder->where('item_txn_date', $value['item_txn_date']);
								$builder->where('voucher_txn_id', $value['voucher_txn_id']);
								$builder->where('item_txn_id <', $value['item_txn_id']);
								$builder->groupEnd();
								$builder->groupEnd();
								$builder->groupEnd();


								$builder->where('bo_id', $this->bo_id);
								$builder->orderBy('item_txn_date', 'desc');
								$builder->orderBy('voucher_txn_id', 'desc');
								$builder->orderBy('item_txn_id', 'desc');
								$builder->limit(1);
								$transaction = $builder->get()->getRowArray();
								if($transaction){
									$last_quantity = floatval($transaction['item_bal_qty']);

									$builder = $db->table($itemtxnval_tbl);
									$builder->where('item_id', $value['item_id']);
									$builder->where('item_txn_id', $transaction['item_txn_id']);
									$builder->where('method_id', 1);
									$itemtxnvaln = $builder->get()->getRowArray();
									if($itemtxnvaln){
										$last_value = floatval($itemtxnvaln['item_value']);
									}
								}
								else{
									
									$builder = $db->table($itmoppybal_tbl); 
									$builder->where('item_id', $value['item_id']);
									$builder->where('item_unit', $value['item_unit']);
									$builder->where('mat_cent_id', $value['mat_cent_id']);
									$builder->where('bo_id', $this->bo_id);
									$builder->where('batch_id', $value['batch_id']);	
									$itmoppybal = $builder->get()->getRowArray();
									if($itmoppybal){
										$last_quantity = floatval($itmoppybal['op_bal_qty']);
									}


									$builder = $db->table($itmoppyval_tbl); 
									$builder->where('item_id', $value['item_id']);
									$builder->where('item_unit', $value['item_unit']);
									$builder->where('mat_cent_id', $value['mat_cent_id']);
									$builder->where('bo_id', $this->bo_id);
									$builder->where('batch_id', $value['batch_id']);
									$builder->where('method_id', 1);
									$itmoppyval = $builder->get()->getRowArray();
									
									if($itmoppyval){
										$last_value = floatval($itmoppyval['op_bal_val']);
									}
								}

								$cp = $last_quantity != 0 ? parseAmount($last_value / $last_quantity) : 0;
								$sp = $outward_qty != 0 ? parseAmount($outward_amount / $outward_qty) : 0;
								$pnl = parseAmount(($sp - $cp) * $outward_qty);

								$pnl2 = formatAmount($outward_amount).'/'.$outward_qty.'<br>'.formatAmount($last_value).'/'.$last_quantity.'<br>('.$sp.'-'.$cp.')'.'*'.$outward_qty.'<br>='.$pnl;
							}


							$final_result[]    = array(

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
								'outward_amount'			=> $outward_amount,

								'inward_qty'				=> $inward_qty,
								'outward_qty'				=> $outward_qty,

								'item_txn_drcr'			=> $value['item_txn_drcr'],
								'balance_qty'				=> $closing_qty,
								'balance_amount'			=> $valuation,
								'pnl'							=> $pnl,
								'company_id'   			=> $company['company_id'],
								'comp_name'   				=> $company['comp_name'],
							);
						}
					}
				}
			}




			return $final_result;
		// return [
		// 	'total_records'	=> $total_records,
		// 	'data'	=> $final_result
		// ];
		}

		public function parent_info($acc_grp_parent_id){
			return  $this->erp_db->table('aictlyerp_grpparentn_univdb')->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();
		}

		public function group_companies_list(){
			$ses_grp_id = $this->session->get('ses_grp_id');

			$this->erp_db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");

			$builder = $this->erp_db->table('aictlyerp_grpcompacs_univdb');
			$builder->where('aictlyerp_grpcompacs_univdb.grpco_id',$ses_grp_id);
			$builder->groupBy('aictlyerp_grpcompacs_univdb.comp_id');	
			$builder->orderBy('aictlyerp_grpcompacs_univdb.comp_id');	 
			$response =  $builder->get()->getResultArray();

			$final_companies=array();	 
			if($response){
				foreach($response as $row){
					$comp_id = $row['comp_id'];
					$grpco_id = $row['grpco_id'];
					$companyinfo = $this->getcompany_info($comp_id);
					$final_companies[] = array(
						'company_id'     	=> $comp_id,
						'comp_code'   		=> $companyinfo['comp_code'],
						'comp_fy_id'  		=> $companyinfo['comp_fy_id'],
						'comp_name'  			=> $companyinfo['comp_name'],
						'comp_short_name' => $companyinfo['comp_short_name'],
					);
				}
			}
			return $final_companies;
		}

		public function getcompany_info($company_id){	 
			return $this->aicountly_db->table('aicountly_cmpmastern_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
		}

		public function load_accounts_trial($acc_grp_parent_id, $from_date = '', $to_date = '')
		{
			$final_result = [];
			$group_companies_list = $this->group_companies_list();

			foreach($group_companies_list as $company){

				$company_id      =  $company['company_id'];
				$comp_fy_id      =  $company['comp_fy_id'];
				$comp_code       =  $company['comp_code'];
				$comp_name       =  ucwords(strtolower($company['comp_name']));
				$comp_short_name =  ucwords(strtolower($company['comp_short_name']));
				$company_name    =  $comp_name;
				$db           =  $this->externaldb->single_company_db($comp_code);

				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $this->session->get('ses_grp_id'));
				$builder->where('grpmp.master_type','acc');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$accounts = $builder->get()->getResultArray();

				foreach ($accounts as $key => $value) {

					$account_id = $value['master_id'];
					$crs_master_id = $value['crs_master_id'];
					$account_name = '';

					$debit = '';
					$credit = '';
					$balance = '';
					$op_balance = '';
					$debit_total = 0;
					$credit_total = 0;
					$balance_total = 0;
					$op_balance_total = 0;
					$balance_type = 0;

					$account_master_tbl = $company['company_id'].'_acctmaster_'.$company['comp_fy_id'];
					$account_info = $db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();
					$account_name =  $account_info['acc_name'] ?? '';

					$accoppybal_tbl = $company['company_id'].'_accoppybal_'.$company['comp_fy_id'];
					$get_opn_balance_info = $db->table($accoppybal_tbl)->where('bo_id', $this->bo_id)->where('acc_id', $account_id)->get()->getRowArray();
					if($get_opn_balance_info){
						$op_balance_total = floatval($get_opn_balance_info['acc_op_bal']);
					}

					$acc_txn_tbl = $company['company_id'].'_accnttxnnn_'.$account_id.'_'.$company['comp_fy_id'];

					$builder = $db->table($acc_txn_tbl);
					$builder->select('SUM(CASE WHEN `acc_txn_drcr`="c" THEN `acc_txn_amount` ELSE 0 END) AS credit, SUM(CASE WHEN `acc_txn_drcr`="d" THEN `acc_txn_amount` ELSE 0 END) AS debit');
					$builder->where('acc_id', $account_id);
					if($from_date != '')
						$builder->where('acc_txn_date >=', $from_date);
					if($to_date != '')
						$builder->where('acc_txn_date <=', $to_date);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$debit_total = floatval($acc_txns['debit']);
						$credit_total = floatval($acc_txns['credit']);	
					}

					$builder = $db->table($acc_txn_tbl);
					$builder->where('acc_id', $account_id);
					$builder->where('bo_id', $this->bo_id);
					if($to_date != '')
						$builder->where('acc_txn_date <=', $to_date);
					$builder->orderBy('acc_txn_date', 'desc');
					$builder->orderBy('voucher_txn_id', 'desc');
					$builder->orderBy('acc_txn_id', 'desc');
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$balance_total = floatval($acc_txns['acc_bal']);	
					}
					else{
						$balance_total = $op_balance_total;
					}

					if($debit_total > 0)
						$debit = formatAmount($debit_total);
					if($credit_total > 0)
						$credit = formatAmount($credit_total);

					if($balance_total >= 0)
						$balance = formatAmount($balance_total).' DR';
					else
						$balance = formatAmount(abs($balance_total)).' CR';

					if($op_balance_total > 0)
						$op_balance = formatAmount($op_balance_total).' DR';
					if($op_balance_total < 0)
						$op_balance = formatAmount(abs($op_balance_total)).' CR';

					$final_result[] = [
						'company_id'				=> $company_id,
						'company_name'			=> $company_name,
						'type'							=> 'acc',
						'account_id'				=> $account_id,
						'crs_master_id'			=> $crs_master_id,
						'account_name'			=> $account_name,
						'debit_total'				=> $debit_total,
						'credit_total'			=> $credit_total,
						'balance_total'			=> $balance_total,
						'op_balance_total'	=> $op_balance_total,
						'debit'							=> $debit,
						'credit'						=> $credit,
						'balance'						=> $balance,
						'op_balance'				=> $op_balance,
					];
				}

				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $this->session->get('ses_grp_id'));
				$builder->where('grpmp.master_type','bsd');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$bsd_accounts = $builder->get()->getResultArray();

				foreach ($bsd_accounts as $key => $value) {

					$account_id = $value['master_id'];
					$crs_master_id = $value['crs_master_id'];
					$account_name = '';

					$debit = '';
					$credit = '';
					$balance = '';
					$op_balance = '';
					$debit_total = 0;
					$credit_total = 0;
					$balance_total = 0;
					$op_balance_total = 0;

					$sundry_master_tbl = $company['company_id'].'_billsundry_'.$company['comp_fy_id'];
					$account_info = $db->table($sundry_master_tbl)->where('bill_sundry_id', $account_id)->get()->getRowArray();
					$account_name =  $account_info['bill_sundry_name'] ?? '';

					$bsdoppybal_tbl = $company['company_id'].'_bsdoppybal_'.$company['comp_fy_id'];
					$get_opn_balance_info = $db->table($bsdoppybal_tbl)->where('bo_id', $this->bo_id)->where('bill_sundry_id', $account_id)->get()->getRowArray();
					if($get_opn_balance_info){
						$op_balance_total = floatval($get_opn_balance_info['bsd_op_bal']);
					}

					$acc_txn_tbl = $company['company_id'].'_sundrytxnn_'.$account_id.'_'.$company['comp_fy_id'];

					$builder = $db->table($acc_txn_tbl);
					$builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN `sundry_txn_amount` ELSE 0 END) AS credit, SUM(CASE WHEN `sundry_txn_drcr`="d" THEN `sundry_txn_amount` ELSE 0 END) AS debit');
					$builder->where('bill_sundry_id', $account_id);
					if($from_date != '')
						$builder->where('sundry_txn_date >=', $from_date);
					if($to_date != '')
						$builder->where('sundry_txn_date <=', $to_date);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$debit_total = floatval($acc_txns['debit']);
						$credit_total = floatval($acc_txns['credit']);	
					}

					$builder = $db->table($acc_txn_tbl);
					$builder->where('bill_sundry_id', $account_id);
					$builder->where('bo_id', $this->bo_id);
					if($to_date != '')
						$builder->where('sundry_txn_date <=', $to_date);
					$builder->orderBy('sundry_txn_date', 'desc');
					$builder->orderBy('voucher_txn_id', 'desc');
					$builder->orderBy('sundry_txn_id', 'desc');
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$balance_total = floatval($acc_txns['sundry_bal']);	
					}
					else{
						$balance_total = $op_balance_total;
					}

					if($debit_total > 0)
						$debit = formatAmount($debit_total);
					if($credit_total > 0)
						$credit = formatAmount($credit_total);

					if($balance_total >= 0)
						$balance = formatAmount($balance_total).' DR';
					else
						$balance = formatAmount(abs($balance_total)).' CR';

					if($op_balance_total > 0)
						$op_balance = formatAmount($op_balance_total).' DR';
					if($op_balance_total < 0)
						$op_balance = formatAmount(abs($op_balance_total)).' CR';

					$final_result[] = [
						'company_id'				=> $company_id,
						'company_name'			=> $company_name,
						'type'							=> 'bsd',
						'crs_master_id'			=> $crs_master_id,
						'account_id'				=> $account_id,
						'account_name'			=> $account_name,
						'debit_total'				=> $debit_total,
						'credit_total'			=> $credit_total,
						'balance_total'			=> $balance_total,
						'op_balance_total'	=> $op_balance_total,
						'debit'							=> $debit,
						'credit'						=> $credit,
						'balance'						=> $balance,
						'op_balance'				=> $op_balance,
					];

				}

			}
		// echo "<pre>";print_r($final_result);exit;
			$final_result2 = [];
			foreach ($final_result as $key => $value) {
				$index = -1;

				if($final_result2){
					foreach ($final_result2 as $key2 => $value2) {
						if($value['crs_master_id'] == $value2['crs_master_id']){
							$index = $key2;
							break;
						}
					}
				}

				if($index >= 0){

					$debit = '';
					$credit = '';
					$balance = '';
					$op_balance = '';

					$debit_total = $final_result2[$index]['debit_total'] + $value['debit_total'];
					$credit_total = $final_result2[$index]['credit_total'] + $value['credit_total'];
					$balance_total = $final_result2[$index]['balance_total'] + $value['balance_total'];
					$op_balance_total = $final_result2[$index]['op_balance_total'] + $value['op_balance_total'];

					if($debit_total > 0)
						$debit = formatAmount($debit_total);
					if($credit_total > 0)
						$credit = formatAmount($credit_total);

					if($balance_total >= 0)
						$balance = formatAmount($balance_total).' DR';
					else
						$balance = formatAmount(abs($balance_total)).' CR';

					if($op_balance_total > 0)
						$op_balance = formatAmount($op_balance_total).' DR';
					if($op_balance_total < 0)
						$op_balance = formatAmount(abs($op_balance_total)).' CR';

					$account_id = $final_result2[$index]['account_id'] .','.$value['account_id'];
					$company_id = $final_result2[$index]['company_id'] .','.$value['company_id'];
					$company_name = $final_result2[$index]['company_name'] .','.$value['company_name'];

					$account_name = ucwords($final_result2[$index]['account_name']);

					$final_result2[$index] = [
						'company_id'				=> $company_id,
						'company_name'			=> $company_name,
						'type'							=> $final_result2[$index]['type'],
						'crs_master_id'			=> $final_result2[$index]['crs_master_id'],
						'account_id'				=> $account_id,
						'account_name'			=> $account_name,
						'debit_total'				=> $debit_total,
						'credit_total'			=> $credit_total,
						'balance_total'			=> $balance_total,
						'op_balance_total'	=> $op_balance_total,
						'debit'							=> $debit,
						'credit'						=> $credit,
						'balance'						=> $balance,
						'op_balance'				=> $op_balance,
					];
				}
				else{
					$final_result2[] = $value;
				}
			}

			return $final_result2;
		}

		public function load_accounts_summary($crs_master_id)
		{
		// $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
			$start_date     = strtotime('2023-04-01');
			$end_date       = strtotime('2024-03-31'); 

			$final = [];
			while( $start_date <= $end_date ) {
				$from_date = date( 'Y-m-01', $start_date );
				$to_date = date( 'Y-m-t', $start_date );
				$month = date("F", strtotime($from_date));

				$data = $this->load_account_summary_details($crs_master_id, $from_date, $to_date);

				if($data['balance_total'] >= 0)
					$balance_type = 'DR';
				else
					$balance_type = 'CR';

				$final[] = [
					'from_date' 				=> $from_date,
					'to_date'   				=> $to_date,
					'month'   					=> $month,
					'credit_total'   	=> $data['credit_total'],
					'debit_total'   		=> $data['debit_total'],
					'balance_total'   	=> $data['balance_total'],
					'credit'   				=> formatAmount($data['credit_total']),
					'debit'   					=> formatAmount($data['debit_total']),
					'balance'   				=> formatAmount(abs($data['balance_total'])),
					'balance_type'   	=> $balance_type,
					'crs_master_id'		=> $crs_master_id,
				];

				$start_date = strtotime('+1 month', $start_date);
			}

			return $final;
		}

		public function load_account_summary_details($crs_master_id, $from_date, $to_date)
		{

			$debit_total = 0;
			$credit_total = 0;
			$balance_total = 0;
			$op_balance_total = 0;

			$get_grpmp_info  =  $this->get_grpcomst_info($crs_master_id, 'acc');    
			$comp_id_string  =  $get_grpmp_info['comp_id'] ?? '';

			$arr = explode(',', $comp_id_string);
			$companies = [];

			foreach ($arr as $key => $value) {
				if(trim($value) != ''){
					$company_info = $this->get_company_info($value);
					$grpmapping_info = $this->get_grpmapping_info($crs_master_id, $value, 'acc');


					$companies[] = array(
						'company_id'     	=> $value,
						'comp_code'   	 	=> $company_info['comp_code'],
						'comp_fy_id'  	 	=> $company_info['comp_fy_id'],
						'comp_name'  	 		=> $company_info['comp_name'],
						'comp_short_name' => $company_info['comp_short_name'],
						'account_id'  		=> $grpmapping_info['master_id'] ?? 0,
					); 
				}
			}

			$final_result = [];

			foreach ($companies as  $company) {
				$db =  $this->externaldb->single_company_db($company['comp_code']);

				$account_id = $company['account_id'];
				if($account_id != 0){

					$accoppybal_tbl = $company['company_id'].'_accoppybal_'.$company['comp_fy_id'];
					$get_opn_balance_info = $db->table($accoppybal_tbl)->where('bo_id', $this->bo_id)->where('acc_id', $account_id)->get()->getRowArray();
					if($get_opn_balance_info){
						$op_balance_total = floatval($get_opn_balance_info['acc_op_bal']);
					}

					$acc_txn_tbl = $company['company_id'].'_accnttxnnn_'.$account_id.'_'.$company['comp_fy_id'];

					$builder = $db->table($acc_txn_tbl);
					$builder->select('SUM(CASE WHEN `acc_txn_drcr`="c" THEN `acc_txn_amount` ELSE 0 END) AS credit, SUM(CASE WHEN `acc_txn_drcr`="d" THEN `acc_txn_amount` ELSE 0 END) AS debit');
					$builder->where('acc_id', $account_id);
					if($from_date != '')
						$builder->where('acc_txn_date >=', $from_date);
					if($to_date != '')
						$builder->where('acc_txn_date <=', $to_date);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$debit_total += floatval($acc_txns['debit']);
						$credit_total += floatval($acc_txns['credit']);	
					}

					$builder = $db->table($acc_txn_tbl);
					$builder->where('acc_id', $account_id);
					$builder->where('bo_id', $this->bo_id);
					if($to_date != '')
						$builder->where('acc_txn_date <=', $to_date);
					$builder->orderBy('acc_txn_date', 'desc');
					$builder->orderBy('voucher_txn_id', 'desc');
					$builder->orderBy('acc_txn_id', 'desc');
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$balance_total += floatval($acc_txns['acc_bal']);	
					}
					else{
						$balance_total += $op_balance_total;
					}


				}
			}


			return [
				'debit_total'		=>	$debit_total,
				'credit_total'		=>	$credit_total,
				'balance_total'	=>	$balance_total,
			];
		}


		public function load_bill_sundry_summary($crs_master_id)
		{
		// $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
			$start_date     = strtotime('2023-04-01');
			$end_date       = strtotime('2024-03-31'); 

			$final = [];
			while( $start_date <= $end_date ) {
				$from_date = date( 'Y-m-01', $start_date );
				$to_date = date( 'Y-m-t', $start_date );
				$month = date("F", strtotime($from_date));

				$data = $this->bill_sundry_summary_details($crs_master_id, $from_date, $to_date);

				if($data['balance_total'] >= 0)
					$balance_type = 'DR';
				else
					$balance_type = 'CR';

				$final[] = [
					'from_date' 				=> $from_date,
					'to_date'   				=> $to_date,
					'month'   					=> $month,
					'credit_total'   	=> $data['credit_total'],
					'debit_total'   		=> $data['debit_total'],
					'balance_total'   	=> $data['balance_total'],
					'credit'   				=> formatAmount($data['credit_total']),
					'debit'   					=> formatAmount($data['debit_total']),
					'balance'   				=> formatAmount(abs($data['balance_total'])),
					'balance_type'   	=> $balance_type,
					'crs_master_id'		=> $crs_master_id,
				];

				$start_date = strtotime('+1 month', $start_date);
			}

			return $final;
		}

		public function load_bill_sundry_details($crs_master_id, $from_date, $to_date)
		{

			$debit_total = 0;
			$credit_total = 0;
			$balance_total = 0;
			$op_balance_total = 0;

			$get_grpmp_info  =  $this->get_grpcomst_info($crs_master_id, 'bsd');    
			$comp_id_string  =  $get_grpmp_info['comp_id'] ?? '';

			$arr = explode(',', $comp_id_string);
			$companies = [];

			foreach ($arr as $key => $value) {
				if(trim($value) != ''){
					$company_info = $this->get_company_info($value);
					$grpmapping_info = $this->get_grpmapping_info($crs_master_id, $value, 'acc');


					$companies[] = array(
						'company_id'     	=> $value,
						'comp_code'   	 	=> $company_info['comp_code'],
						'comp_fy_id'  	 	=> $company_info['comp_fy_id'],
						'comp_name'  	 		=> $company_info['comp_name'],
						'comp_short_name' => $company_info['comp_short_name'],
						'account_id'  		=> $grpmapping_info['master_id'] ?? 0,
					); 
				}
			}

			$final_result = [];

			foreach ($companies as  $company) {
				$db =  $this->externaldb->single_company_db($company['comp_code']);

				$account_id = $company['account_id'];
				if($account_id != 0){

					$bsdoppybal_tbl = $company['company_id'].'_bsdoppybal_'.$company['comp_fy_id'];
					$get_opn_balance_info = $db->table($bsdoppybal_tbl)->where('bo_id', $this->bo_id)->where('bill_sundry_id', $account_id)->get()->getRowArray();
					if($get_opn_balance_info){
						$op_balance_total = floatval($get_opn_balance_info['bsd_op_bal']);
					}

					$acc_txn_tbl = $company['company_id'].'_sundrytxnn_'.$account_id.'_'.$company['comp_fy_id'];

					$builder = $db->table($acc_txn_tbl);
					$builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN `sundry_txn_amount` ELSE 0 END) AS credit, SUM(CASE WHEN `sundry_txn_drcr`="d" THEN `sundry_txn_amount` ELSE 0 END) AS debit');
					$builder->where('bill_sundry_id', $account_id);
					if($from_date != '')
						$builder->where('sundry_txn_date >=', $from_date);
					if($to_date != '')
						$builder->where('sundry_txn_date <=', $to_date);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$debit_total += floatval($acc_txns['debit']);
						$credit_total += floatval($acc_txns['credit']);	
					}

					$builder = $db->table($acc_txn_tbl);
					$builder->where('bill_sundry_id', $account_id);
					$builder->where('bo_id', $this->bo_id);
					if($to_date != '')
						$builder->where('sundry_txn_date <=', $to_date);
					$builder->orderBy('sundry_txn_date', 'desc');
					$builder->orderBy('voucher_txn_id', 'desc');
					$builder->orderBy('sundry_txn_id', 'desc');
					$acc_txns = $builder->get()->getRowArray();
					if($acc_txns){
						$balance_total += floatval($acc_txns['sundry_bal']);	
					}
					else{
						$balance_total += $op_balance_total;
					}


				}
			}


			return [
				'debit_total'		=>	$debit_total,
				'credit_total'		=>	$credit_total,
				'balance_total'	=>	$balance_total,
			];
		}

		public function load_balance_sheet($view,$from_date,$to_date,$nil_type)
		{
			$group_companies_list = $this->group_companies_list(); 
		// OWNER'S FUND	 1							NON CURRENT ASSETS 3	
		// NON CURRENT LIABILITIES 	2				CURRENT ASSETS 5
		// CURRENT LIABILITIES	4					DIFF. IN OP. BALANCE


			$array1 = [];
			$array2 = [];	

		$data = $this->get_parent_group_details(1, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // OWNER'S FUND

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

    	$data = $this->get_pl_details($from_date,$to_date,$this->erp_db,$group_companies_list); // PROFIT & LOSS
    	if($data){

    		$array1[] = [
    			'l_group_id'	  	=> $data['group_id'],
    			'l_group_name'    => $data['group_name'],
    			'l_balance'		   => $data['balance'] != '' ? formatAmount(-$data['balance']) : '',
    			'l_balance_total' => $data['balance'] != '' ? -$data['balance'] : '',
    			'l_type'		  		=> $data['type'],
    			'l_style'		   => $data['style'],
    		];
    	}

    	$data = $this->get_parent_group_details(2, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // NON CURRENT LIABILITIES
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
    	$data = $this->get_parent_group_details(4, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // CURRENT LIABILITIES
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

    	$data = $this->get_parent_group_details(3, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // NON CURRENT ASSETS
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
    	$data = $this->get_parent_group_details(5, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // CURRENT ASSETS 
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
    	$data = $this->load_stock_status_items(0,0,$to_date);
    	if($view == 1 || $view == 2){
    		$array2[] = [
    			'r_group_id'	  	=> 0,
    			'r_group_name'    => '&nbsp;&nbsp; &raquo; Inventories',
    			'r_balance'		  	=> $data != '' ? formatAmount($data) : '',
    			'r_balance_total'	=> $data != '' ? $data : '',
    			'r_type'		  		=> 'clo',
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
    	$data = $this->get_op_diff_balance_details($this->erp_db,$group_companies_list,[1,2,3,4,5,6,7,8,9,10,11,12,13]); // DIFF. IN OP. BALANCE


    	if($data){
    		$array2[] = [
    			'r_group_id'	  => $data['group_id'],
    			'r_group_name'    => $data['group_name'],
    			'r_balance'		  => $data['balance'] != '' ? formatAmount($data['balance']) : '',
    			'r_balance_total'	=> $data['balance'] != '' ? $data['balance'] : '',
    			'r_type'		  => $data['type'],
    			'r_style'		  => $data['style'],
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

    public function get_parent_group_details($acc_grp_parent_id, $view, $from_date, $to_date)
    { 
    	$ses_grp_id = $this->session->get('ses_grp_id');
    	$group_companies_list = $this->group_companies_list();  
    	
    	$final = [];
    	$final1 = [];
    	$final2 = [];
    	$balance_total=0;
    	$acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
    	$parent =  $this->erp_db->table($acc_grp_par_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();

    	if($parent)
    	{
    		$final1[] = [
    			'group_id'	 	=> $parent['acc_grp_parent_id'],
    			'group_name' 	=> $parent['grp_name'],
    			'balance'	 		=> '',
    			'type'				=> 'prt',
    			'style'				=> 'font-weight:bold;'
    		];

    		$final2[] = [
    			'group_id'	 	=> $parent['acc_grp_parent_id'],
    			'group_name' 	=> $parent['grp_name'],
    			'balance'	 		=> '',
    			'type'				=> 'prt',
    			'style'				=> 'font-weight:bold;'
    		];

    		$debit_total = $credit_total = 0;

    		foreach($group_companies_list as $grprow){

    			$company_id  	=  $grprow['company_id'];
    			$comp_fy_id  	=  $grprow['comp_fy_id'];
    			$comp_code   	=  $grprow['comp_code'];
    			$company_name =  ucwords(strtolower($grprow['comp_name']));

    			$extdb  =  $this->externaldb->single_company_db($comp_code);

    			$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
    			$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
    			$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
    			$builder->where('grpmp.grpco_id', $ses_grp_id);
    			$builder->where('grpmp.master_type','acc');	
    			$builder->where('grpmp.crs_master_id >','0');
    			$builder->where('grpmp.comp_id',$company_id);	
    			$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
    			$builder->orderBy('grpcom.crs_master_name');
    			$accounts = $builder->get()->getResultArray();

    			if($accounts){
    				foreach ($accounts as $account) {
    					$crs_master_id = $account['crs_master_id'];
    					$group_id      = $account['master_id'];
    					$group_name    = $account['crs_master_name'].'('.$company_name.')';

    					$op_balance = 0;
    					$fr_balance = 0;
    					$to_balance = 0;

    					$credit_detail_total = 0;
    					$debit_detail_total = 0;

    					$get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);

    					if($get_opn_balance_info)
    						$op_balance  = $get_opn_balance_info['acc_op_bal'];

    					$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
    					$builder = $extdb->table($acc_txn_tbl);
    					$builder->where('acc_id', $account['master_id']);
    					$builder->where('acc_txn_date <=', $to_date);
    					$builder->where('bo_id', $this->bo_id);
    					$builder->orderBy('acc_txn_date', 'desc');
    					$builder->orderBy('voucher_txn_id', 'desc');
    					$builder->orderBy('acc_txn_id', 'desc');
    					$builder->limit(1);
    					$transaction = $builder->get()->getRowArray();
    					if($transaction)
    					{
    						if($transaction['acc_bal'] < 0){
    							$credit_total += abs($transaction['acc_bal']);
    							$credit_detail_total = abs($transaction['acc_bal']);
    						}
    						if($transaction['acc_bal'] >= 0){
    							$debit_total += $transaction['acc_bal'];
    							$debit_detail_total = $transaction['acc_bal'];
    						}
    					}
    					else
    					{
			    		//check opening balance
    						if($get_opn_balance_info){
    							$acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
    							if($acc_opn_balance<0){
    								$credit_total +=abs($acc_opn_balance);
    								$credit_detail_total = abs($acc_opn_balance);
    							}
    							if($acc_opn_balance>0){
    								$debit_total +=$acc_opn_balance;
    								$debit_detail_total = $acc_opn_balance;
    							}
    						}
    					}
    					$balance = $debit_detail_total - $credit_detail_total;
    					$balance_total += $balance;	
    					$final2[] = [
					  			'group_id'	 	=> $crs_master_id,//$account['master_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&raquo;&raquo; '.$account['crs_master_name'].'('.$company_name.')',
					  			'balance'	 	=> $balance,
					  			'type'			=> 'acc',
					  			'style'			=> 'font-style:italic;'
					  		];	

					  		if($crs_master_id == 16232){
					  			$temp = [
					  			'group_id'	 	=> $crs_master_id,//$account['master_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&raquo;&raquo; '.$account['crs_master_name'].'('.$company_name.')',
					  			'balance'	 	=> $balance,
					  			'type'			=> 'acc',
					  			'style'			=> 'font-style:italic;'
					  		];
					  		echo "<br>account_id ".$account['master_id'];
					  		echo "<pre>";print_r($temp);
					  	}

					  }								
					}



				//check sundry accounts
					$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
					$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
					$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
					$builder->where('grpmp.grpco_id', $ses_grp_id);
					$builder->where('grpmp.master_type','bsd');	
					$builder->where('grpmp.crs_master_id >','0');
					$builder->where('grpmp.comp_id',$company_id);	
					$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
					$builder->orderBy('grpcom.crs_master_name');
					$sundry_accounts = $builder->get()->getResultArray();
					if($sundry_accounts){
						foreach ($sundry_accounts as $sundry_account) {
							$crs_master_id = $sundry_account['crs_master_id'];
							$op_balance = 0;
							$fr_balance = 0;
							$to_balance = 0;

							$credit_detail_total = 0;
							$debit_detail_total = 0;
							


							$acc_txn_tbl = $company_id.'_sundrytxnn_'.$sundry_account['master_id'].'_'.$comp_fy_id;
							$builder = $extdb->table($acc_txn_tbl);
							$builder->where('bill_sundry_id', $sundry_account['master_id']);
							$builder->where('sundry_txn_date <=', $to_date);
							$builder->where('bo_id', $this->bo_id);
							$builder->orderBy('sundry_txn_date', 'desc');
							$builder->orderBy('voucher_txn_id', 'desc');
							$builder->orderBy('sundry_txn_id', 'desc');
							$builder->limit(1);
							$transaction = $builder->get()->getRowArray();
							if($transaction)
							{
								if($transaction['sundry_bal'] < 0){
									$credit_total += abs($transaction['sundry_bal']);
									$credit_detail_total = abs($transaction['sundry_bal']);
								}
								if($transaction['sundry_bal'] >= 0){
									$debit_total += $transaction['sundry_bal'];
									$debit_detail_total = $transaction['sundry_bal'];
								}
							}
							else
							{
								$bill_sundry_op_balance = $this->sundry_opn_balance_info($sundry_account['master_id'],$extdb,$company_id,$comp_fy_id);
								if($bill_sundry_op_balance)
								{
									if($bill_sundry_op_balance['bsd_op_bal'] < 0){
										$credit_total += abs($bill_sundry_op_balance['bsd_op_bal']);
										$credit_detail_total = abs($bill_sundry_op_balance['bsd_op_bal']);
									}
									if($bill_sundry_op_balance['bsd_op_bal'] >= 0){
										$debit_total += $bill_sundry_op_balance['bsd_op_bal'];
										$debit_detail_total = $bill_sundry_op_balance['bsd_op_bal'];
									}
								}

							}

							$balance = $debit_detail_total - $credit_detail_total;


							$balance_total += $balance;
							$final2[] = [
					  			'group_id'	 	=> $crs_master_id,//$sundry_account['master_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$sundry_account['crs_master_name'].'('.$company_name.')',
					  			'balance'	 	=> $balance,
					  			'type'			=> 'bsd',
					  			'style'			=> 'font-style:italic;'
					  		];

					  		






					  	}
					  }
					}
					$final = [0 => [
						'group_id'	 	=> $parent['acc_grp_parent_id'],
						'group_name' 	=> $parent['grp_name'],
						'balance'	 	=> $balance_total,
						'type'			=> 'prt',
						'style'			=> 'font-weight:bold;'
					] ];
				}
				exit;
				if($view == 0)
					return $final;
				if($view == 2)
					return $final2;

				return $final;
			}

			public function acc_opn_balance_info($acc_id,$db,$company_id,$comp_fy_id){
				$accoppybal_tbl =$company_id.'_accoppybal_'.$comp_fy_id;	   
				$res= $db->table($accoppybal_tbl)
				->where('bo_id', $this->bo_id)
				->where('acc_id', $acc_id)
				->get()->getRowArray(); 

				return $res;
			}

			public function sundry_opn_balance_info($acc_id,$db,$company_id,$comp_fy_id){
				$bsdoppybal_tbl = $company_id.'_bsdoppybal_'.$comp_fy_id;
				$result =$db->table($bsdoppybal_tbl)
				->where('bo_id', $this->bo_id)
				->where('bill_sundry_id', $acc_id)
				->get()->getRowArray();

				return $result;   
			}

		}
	?>