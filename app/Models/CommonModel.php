<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\enc_string;
use App\Libraries\externaldb;
use Config\Services;
class CommonModel extends Model{
	protected $db;
	protected $externaldb;
	protected $session;
	protected $aicountly_db;
	protected $company_id;
	protected $user_id;
	protected $univaictly;
	protected $erp_db;
	protected $bo_id;
	protected $univerpaic_db;
	public function __construct() {
		parent::__construct();        
		$this->db            = \Config\Database::connect();	
		$this->session       = \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid');
		$this->externaldb    =  new externaldb();		
		$this->univaictly    =  $this->externaldb->univaictly_db();
		$this->aicountly_db  =  $this->externaldb->aicountly_db();
		$this->erp_db        =  $this->externaldb->erp_db();
		$this->univerpaic_db =  $this->externaldb->univerpaic_db();	
		$this->uuidaictly    =  $this->externaldb->postgr_myaicountlydb();
		
		
		if($this->session->get('ses_boid')!='')
			$this->bo_id = $this->session->get('ses_boid');
		else 
			$this->bo_id =1;
		
	 }

	
	 
	function all_fy_list($company_id=''){
	  if($company_id!=''){	
		return $this->univaictly->table("cmpfymastr")
		->where('cmp_id',$company_id)->orderBy('fy_beg_date')->get()->getResultArray();
	  }else{
		return $this->univaictly->table("cmpfymastr")
		->where('cmp_id',$this->company_id)->orderBy('fy_beg_date')->get()->getResultArray();  
	  }
	}
		
	
	function all_bo_lists(){	
        if($this->session->get('ses_company_id'))	
	   	  $data = $this->univaictly->table("hobomaster")->select('hobo_id,hobo_name,mark_ho')
	              ->where("cmp_id",$this->session->get('ses_company_id'))
			      ->get()->getResultArray();	
        else{
		 $data = $this->univaictly->table("hobomaster")->select('hobo_id,hobo_name,mark_ho')
			      ->get()->getResultArray();		
		}				  
		return 	$data;		
	}
	
	function save_theme_preferences($config_id,$config_value){
			$userId  = 	$this->user_id;
			$builder = $this->univerpaic_db->table('erpdefpref');	
			$builder->where('erp_config_id', $config_id);
			$builder->where('uuid', $userId);
			$exists = $builder->get()->getRow();
			if ($exists) {
				$builder->where('erp_config_id', $config_id);
				$builder->where('uuid', $userId);
				$builder->update(['erp_config_value' => $config_value]);
			} else {
				$insertData = [
					'erp_config_id' => $config_id,
					'erp_config_value' => $config_value,
					'uuid' => $userId,
				];
				$builder->insert($insertData);
			  }
		 return json_encode(['status' => 'success']);
	}
	
	
	
	function get_comp_ho_adrs_info($cmp_id, $bo_id)
		{
			$builder = $this->univaictly->table('hobomaster hobo');
			$builder->select('hobo.*,adrsdtl.hobo_pin_zip,adrsdtl.hobo_addr1,adrsdtl.hobo_addr2,adrsdtl.hobo_city,adrsdtl.hobo_country,adrsdtl.hobo_state, gstin.hobo_gstin, gstin.hobo_gstin_type, gstin.hobo_gstin_sub_type, gstin.hobo_gstin_state_code');
			$builder->join('hoboaddrmt adrsdtl', 'adrsdtl.hobo_id = hobo.hobo_id AND adrsdtl.cmp_id = hobo.cmp_id', 'left');
			$builder->join('hobogstinm gstin', 'gstin.hobo_id = hobo.hobo_id AND gstin.cmp_id = hobo.cmp_id', 'left');
			
			$builder->where('hobo.cmp_id', $cmp_id);
			if ($bo_id > 0) {
				$builder->where('hobo.hobo_id', $bo_id);
			} /* else {
				$builder->where('hobo.mark_ho', 1);
			} */
			
			$row =  $builder->get()->getRowArray();
			if($row){
			
			$bstate_info   =  $this->get_state_info($row['hobo_country'],$row['hobo_state']);
	    	$bstate_code   = $bstate_info['state_code'] ?? 0;
			
		  
			$row['state_code']   = $bstate_code;
			return $row;
			}
			else
			return false;
		}
		
	
	
	function GetBoInfo(){
		$data         = $this->univaictly->table('hobomaster')
		->select('hobo_id,hobo_name,mark_ho')
		->where('hobo_id',$this->bo_id)
		->get()->getRowArray();
		return 	$data;			 
	}
	

	function cron_job($company_id) 
	{
		$this->deleteImportMaster($company_id);
	}

	function deleteImportMaster($company_id)
	{
		$date = date('Y-m-d', strtotime('-7 days'));

		$result = $this->aicountly_db->table('aicountly_impexpmstn_univdb')
								->where('comp_id',$company_id)
								->where('impexp_log <',$date)
								->get()->getResultArray();

		
		foreach ($result as $key => $value) {

			$impexp_sr_id = $value['impexp_sr_id'];
			$impexp_type = $value['impexp_type'];

			if($impexp_type == 1)
				$table = 'aicountly_impaccmstn_univdb';

			if($impexp_type == 2)
				$table = 'aicountly_impaccgrpn_univdb';

			if($impexp_type == 3)
				$table = 'aicountly_impitmmstn_univdb';

			if($impexp_type == 4)
				$table = 'aicountly_impitmgrpn_univdb';

			if($impexp_type == 5)
				$table = 'aicountly_impitmcatn_univdb';

			if(in_array($impexp_type, [6,7])){
				$table = 'aicountly_imptxnvchn_univdb';
				$sub_table = 'aicountly_imptxnvchd_univdb';
			}

			if(in_array($impexp_type, [8])){
				$table = 'aicountly_imptxnvchn_univdb';
				$sub_table = 'aicountly_imptxnvchd_univdb';
				$sub_side_table = 'aicountly_impoutsupd_univdb';
			}

			if(in_array($impexp_type, [9])){
				$table = 'aicountly_imptxnvchn_univdb';
				$sub_table = 'aicountly_imptxnvchd_univdb';
				$sub_table2 = 'aicountly_imptxnvchi_univdb';
				$sub_side_table = 'aicountly_impoutsupd_univdb';
			}

			if(!empty($table)){
				$this->aicountly_db->table($table)
								->where('impexp_sr_id',$impexp_sr_id)
								->delete();
			}
			if(!empty($sub_table)){
				$this->aicountly_db->table($sub_table)
								->where('impexp_sr_id',$impexp_sr_id)
								->delete();
			}
			if(!empty($sub_table2)){
				$this->aicountly_db->table($sub_table2)
								->where('impexp_sr_id',$impexp_sr_id)
								->delete();
			}
			if(!empty($sub_side_table)){
				$this->aicountly_db->table($sub_side_table)
								->where('impexp_sr_id',$impexp_sr_id)
								->delete();
			}

			// Master
			$this->aicountly_db->table('aicountly_impexpmstn_univdb')
								->where('impexp_sr_id',$impexp_sr_id)
								->delete();
		}
	}
	
	function get_bbb_groups_list($company_id,$comp_fy_id,$comp_code)
	{
		
		$groups = [16,22];
		$array = $this->get_sub_group_ids_list($company_id,$comp_fy_id,$comp_code,$groups);
		
		return $array;
	}
	function get_cash_groups_list($company_id,$comp_fy_id,$comp_code)
	{
		$groups = [23,21];
		$array = $this->get_sub_group_ids_list($company_id,$comp_fy_id,$comp_code,$groups); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','cash','left')
				->orLike('LOWER(acc_grp_name)', 'bank', 'left')
				->where('cmp_id', $company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
        return array_unique($array);
		/* 
		
		
		$groups = [23,21];
		$array = $this->get_sub_group_ids_list($company_id,$comp_fy_id,$comp_code,$groups);
		
		return $array; */
	}	
	
	function get_sub_group_ids_list($company_id,$comp_fy_id,$comp_code,$array){
		$external_db    = $this->externaldb->single_company_db($comp_code);
		if(!empty($array)){
			$tbl_name = $company_id.'_acctgroupn_'.$comp_fy_id;
			$data = $external_db->table($tbl_name)
			->select('acc_grp_id')
			->whereIn('under_main_grp_id', $array)
			->get()->getResultArray();
			
			
			if($data){
				foreach($data as $key => $value) {
					array_push($array, $value['acc_grp_id']);
				}
			}
		}
		return $array;
	}
	
	function get_cc_groups_list($company_id,$comp_fy_id,$comp_code)
	{
		$external_db    = $this->externaldb->single_company_db($comp_code);
		$p_groups = [7,11,13];
		$tbl_name = $company_id.'_acctgroupn_'.$comp_fy_id;
		$data =  $external_db->table($tbl_name)
		->select('acc_grp_id')
		->whereIn('acc_grp_parent_id', $p_groups)
		->get()->getResultArray();
		$final = [];
		if($data){
			$final = array_column($data, 'acc_grp_id');
		}		
		return $final;		
	}
	
	function item_unit_info($company_id,$comp_fy_id,$comp_code,$unit_id){
		$external_db    = $this->externaldb->single_company_db($comp_code);
		$item_unit_master_tbl =  $company_id.'_itmunitmst_'.$comp_fy_id;	 
		return  $external_db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $company_id)->orderBy('item_unit','ASC')->get()->getRowArray();   
	}
	
	function company_all_items($company_id,$comp_fy_id,$comp_code){
		//$tax_catg_exempted = array(16,14,8,15,18);
		$tax_catg_exempted = array('ZERSPLY','DEEMEXP','EXMSPLY','NONGSTS','UNDSPLY','NILSPLY');
		$external_db      = $this->externaldb->single_company_db($comp_code);
		$item_master_tbl  = $company_id.'_itemmaster_'.$comp_fy_id;
		$item_units_tbl   = $company_id.'_itmunitmst_'.$comp_fy_id;
		$itemvalmst_tbl   = $company_id.'_itemvalmst_'.$comp_fy_id;
		$itemtaxmst_tbl   = $company_id.'_itemtaxmst_'.$comp_fy_id;
		
		
		$builder = $external_db->table($item_master_tbl.' itmmst');
		$builder->join($item_units_tbl.' itmunits','itmunits.unit_id=itmmst.item_unit','LEFT');
		$builder->join($itemvalmst_tbl.' itmval','itmval.item_id=itmmst.item_id','LEFT');
		$builder->join($itemtaxmst_tbl.' itmtxmst','itmtxmst.item_id=itmmst.item_id','LEFT');
		
		
		$builder->select('itmmst.item_name as label,itmmst.bom_item_container,itmmst.bom_id,itmmst.item_pur_acc,itmmst.item_sales_acc,itmmst.valmethod_id, itmmst.item_name as value,itmmst.item_upc, itmmst.item_id, itmunits.item_unit, itmunits.unit_id,itmval.item_mrp,itmtxmst.cmp_tax_cat_id,itmtxmst.item_hsn_sac,itmtxmst.item_supply_type');
		$builder->orderBy('itmmst.item_name','ASC');
		$data  = $builder->get()->getResultArray(); 
		
		/* $data =  $external_db->table($item_master_tbl)
		->select('itmmst.item_name as label, itmmst.item_name as value,itmmst.item_upc, itmmst.item_id, itmunits.item_unit')
		->orderBy('item_name','ASC')
		->get()->getResultArray(); */
		
		$final_result = array();
		if($data){
			foreach($data as $row){
				$item_unit_name = $row['item_unit'];
				$item_unitid    = $row['unit_id'];
				$bom_id        = $row['bom_id'];
				$bom_batch_qty   = $row['bom_item_container'];
				if($row['item_mrp'])
				   $item_mrp = $row['item_mrp'];
			     else
				   $item_mrp = 0;
			   
			    $item_pur_acc   = $row['item_pur_acc'];
				$item_sales_acc = $row['item_sales_acc'];
			   			   
				/* $item_unit_info  = $this->item_unit_info($company_id,$comp_fy_id,$comp_code,$row['item_unit']); 
				if($item_unit_info)
					$item_unit_name = $item_unit_info['item_unit'];				 
				else
					$item_unit_name ='' ; */
				
			 // Get Item MRp from table itemvalmst
				/* $itemvalmst_tbl = $company_id.'_itemvalmst_'.$comp_fy_id;
				$item_mrp_data  = $external_db->table($itemvalmst_tbl)
				->where('comp_id',$company_id)
				->where('item_id',$row['item_id'])
				->get()->getRowArray();	
				if($item_mrp_data)
				   $item_mrp = $item_mrp_data['item_mrp'];
			     else
				   $item_mrp = 0;		 */		
				
			 // Get Item Last tax WEF 
				/* $itemtaxmst_tbl = $company_id.'_itemtaxmst_'.$comp_fy_id;
				$tax_data       = $external_db->table($itemtaxmst_tbl)
				->where('comp_id',$company_id)
				->where('item_id',$row['item_id'])
				->orderBy('cmp_tax_cat_id','DESC')						
				->get()->getRowArray(); */	
				if($row['cmp_tax_cat_id']){			
					$tax_rates          = $this->cmpgstcatn_info($company_id,$comp_fy_id,$comp_code,$row['cmp_tax_cat_id']);			
					$item_hsn_sac       = $row['item_hsn_sac'];
					$tax_cat_id         = $row['cmp_tax_cat_id'];
					$item_suply         = $row['item_supply_type'];
					
					if($tax_rates){
						$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
						$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
						$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
						$tax_short_code     = $tax_rates['cmp_tax_short_code'];
						if(isset($tax_rates['cmp_tax_cat_cess_basis']))
						$cess_basis         = $tax_rates['cmp_tax_cat_cess_basis'];
					    else
						 $cess_basis         = 1;		
					}
					else{
						$item_tax_igst_rate = 0;
						$item_tax_cess_rate = 0;
						$item_tax_wef       = 0;
						$cess_basis         = 1;	
						$tax_short_code     ='';
					}					
					
				}else{
					$item_hsn_sac       = '';
					$tax_cat_id         = '';
					$item_tax_igst_rate = 0;
					$item_tax_cess_rate = 0;
					$item_tax_wef       = '';
					$cess_basis         = 1;
					$item_suply         = '';	
					$tax_short_code     ='';
				} 
		if($tax_short_code!='' && in_array($tax_short_code,$tax_catg_exempted))
		   $tax_exempted='y'; 
		 else if(isset($tax_rates['cmp_tax_short_code']) && $tax_rates['cmp_tax_short_code']!='' && in_array($tax_rates['cmp_tax_short_code'],$tax_catg_exempted))
		   $tax_exempted='y'; 
		else
		  $tax_exempted='n'; 
		 
				$final_result[]    = array(
					"label"         => html_entity_decode(ucwords($row['label'])),
					'value'         => html_entity_decode($row['value']),
					'valmethod_id'  => $row['valmethod_id'],
					'item_id'       => $row['item_id'],
					'item_upc'      => $row['item_upc'],
					'item_unit'     => html_entity_decode($item_unit_name),
					'itemunitid'    => $item_unitid,
					"item_name"     => html_entity_decode(ucwords($row['value'])),
					'item_unit_id'  => $item_unitid,
					'item_hsn_sac'  => $item_hsn_sac,
					'tax_cat_id'    => $tax_cat_id,
					'igst_rate'     => parseAmount($item_tax_igst_rate),
					'cess_rate'     => parseAmount($item_tax_cess_rate),
					'item_mrp'      => $item_mrp,
					'wef'           => $item_tax_wef,
					'cess_basis'    => $cess_basis,
					'supply_type'   => $item_suply,
					'tax_exempted'  => $tax_exempted,
					'item_sales_acc'=> $item_sales_acc,
					'item_pur_acc'  => $item_pur_acc,
					'tax_short_code' => $tax_short_code,
					'bom_id'         => $bom_id,
					'bom_batch_qty' => $bom_batch_qty
				);
			}
		}

		return json_encode($final_result,JSON_UNESCAPED_SLASHES);
		
	}    
	function account_adrs_info($company_id,$comp_fy_id,$comp_code,$account_id){	 
		$external_db    = $this->externaldb->single_company_db($comp_code);
		
		$acctaddmst_tbl = $company_id.'_acctaddmst_'.$comp_fy_id;
		return $external_db->table($acctaddmst_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
	}
	
	function company_all_accounts($company_id,$comp_fy_id,$comp_code){
		$external_db    = $this->externaldb->single_company_db($comp_code);
		
		
		$bbb_groups = $this->get_bbb_groups_list($company_id,$comp_fy_id,$comp_code);
		$cc_groups = $this->get_cc_groups_list($company_id,$comp_fy_id,$comp_code);
		$cash_groups = $this->get_cash_groups_list($company_id,$comp_fy_id,$comp_code);
		
		
		$acctmaster_tbl = $company_id.'_acctmaster_'.$comp_fy_id;
		$tablresult     =  $external_db->query("SELECT acc_grp_parent_id,acc_grp_id,acc_short_code,acc_name as label, acc_name as value, acc_id, acc_grp_id, acc_grp_parent_id FROM `".$acctmaster_tbl."` order by `acc_name`  ");
		$data   = $tablresult->getResultArray();
		$final_result = array();
		foreach ($data as $key => $value) {
			$cannotselected="0";
			$acc_grp_parent_id = $value['acc_grp_parent_id'];
			 if($acc_grp_parent_id!="0"){
			   if($acc_grp_parent_id=="1"){
				   $cannotselected="1";
			   }
			  
		  }else{
			$main_group_info = $this->acc_main_group_info($value['acc_grp_id'],$external_db,$company_id,$comp_fy_id);  
			 $acc_grp_parent_id = $main_group_info['acc_grp_parent_id'];
             if($acc_grp_parent_id==1){
				 $cannotselected="1"; 
			 }else{
				if($main_group_info['under_main_grp_id']==0){
					if(in_array($value['acc_grp_id'],[3,15,16,22])){
						$cannotselected="1";
					}
				} else{
					if(in_array($main_group_info['under_main_grp_id'],[3,15,16,22])){
						$cannotselected="1";
					}
				}
			 }			 
		  } 
		 
			$acc_short_code =  $value['acc_short_code'];
			$adress_info    = $this->account_adrs_info($company_id,$comp_fy_id,$comp_code,$value['acc_id']);
			if($adress_info){
				$acc_country   =  $adress_info['acc_country'];
				$acc_state     =  $adress_info['acc_state'];
				$state_info    =  $this->get_state_info($acc_country,$acc_state);
				if($state_info)
					$state_code   = $state_info['state_code'];
				else
					$state_code   = '4';
				
			}
			else{
				$state_code    = 0;
				$acc_country   = "1";
				$acc_state     = 0;
			}
			
			$is_bbb = 0;
			if(in_array($value['acc_grp_id'], $bbb_groups)){
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
			
			
			$acctgstmst_tbl = $company_id.'_acctgstmst_'.$comp_fy_id;
			$acctgstmst_data       = $external_db->table($acctgstmst_tbl)
			->where('acc_id',$value['acc_id'])->get()->getRowArray();
			
			$dealer_type=0;
			if($acctgstmst_data)
			  $dealer_type=$acctgstmst_data['acc_dealer_type'];
			
			
			$itemtaxmst_tbl = $company_id.'_acctaxmstn_'.$comp_fy_id;
			$tax_data       = $external_db->table($itemtaxmst_tbl)
			->where('comp_id',$company_id)
			->where('acc_id',$value['acc_id'])
			->orderBy('cmp_tax_cat_id','DESC')						
			->get()->getRowArray();
			if($tax_data){			
				$tax_rates          = $this->cmpgstcatn_info($company_id,$comp_fy_id,$comp_code,$tax_data['cmp_tax_cat_id']);			
				$item_hsn_sac       = $tax_data['acc_hsn_sac'];
				$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
				if($tax_rates){
					$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
					$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
					$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
					$cess_basis         = $tax_rates['cmp_tax_cat_cess_basis'] ?? 1;
				}
				else{
					$item_tax_igst_rate = 0;
					$item_tax_cess_rate = 0;
					$item_tax_wef       = 0;
					$cess_basis         = 1;	
				}
				
			}else{
				$item_hsn_sac       = '';
				$tax_cat_id         = '';
				$item_tax_igst_rate = 0;
				$item_tax_cess_rate = 0;
				$item_tax_wef       = '';
				$cess_basis         = 1;		
			}
			
			$final_result[]    = array(
				"label"       => html_entity_decode(ucwords($value['label'])),
				'value'       => html_entity_decode($value['value']),
				'id'          => $value['acc_id'],
				'account_id'  => $value['acc_id'],
				'acc_id'      => $value['acc_id'],
				'dealer_type' => $dealer_type,
				'is_sundry'   => '0',
				'is_acc'      => '1',
				'is_bbb'      => $is_bbb,
				'is_cc'	      => $is_cc,
				'is_cash'	  => $is_cash,
				'acc_state'   => ($acc_state >0)?$acc_state:0,
				'acc_country' => ($acc_country >0)?$acc_country:1,
				'state_code'  => ($state_code>0)?$state_code:0,
				'item_hsn_sac'=> $item_hsn_sac,
				'tax_cat_id'  => $tax_cat_id,
				'igst_rate'   => parseAmount($item_tax_igst_rate),
				'cess_rate'   => parseAmount($item_tax_cess_rate),
				'wef'         => $item_tax_wef,
				'cess_basis'  => $cess_basis,
				'tax_exempted' => 'n',
				'cannotselected' => $cannotselected, 
				'acc_short_code' => $acc_short_code // use to show errors if system generated master account selected in voucher entry
			);	  	
			
			
			
		}
		
		return json_encode($final_result,JSON_UNESCAPED_SLASHES);
	}  
	
	public function cmpgstcatn_info($company_id,$comp_fy_id,$comp_code,$cmp_tax_cat_id){
		$external_db    = $this->externaldb->single_company_db($comp_code);
		
		$cmpgstcatn_tbl = $company_id.'_cmpgstcatn_'.$comp_fy_id;
		$response =  $external_db->table($cmpgstcatn_tbl)->where('cmp_tax_cat_id',$cmp_tax_cat_id)->where('comp_id',$company_id)->get()->getRowArray();
		return  $response;
	}
	
	function company_all_bsd($company_id,$comp_fy_id,$comp_code){

		$cc_groups = $this->get_cc_groups_list($company_id,$comp_fy_id,$comp_code);

		$final_result=array();
		$external_db    = $this->externaldb->single_company_db($comp_code);
		
		
		$billsundry_tbl  = $company_id.'_billsundry_'.$comp_fy_id;
		
		$bsd_data       = $external_db->table($billsundry_tbl)
		->select('sundry_nature,bill_sundry_name as label, bill_sundry_name as value, bill_sundry_id, acc_grp_id, acc_grp_parent_id')
		->orderBy('bill_sundry_name','ASC')
		->get()->getResultArray();
		
		
		
		if($bsd_data){
			foreach($bsd_data as $key => $row){
				$itemtaxmst_tbl = $company_id.'_bdstaxmstn_'.$comp_fy_id;
				$tax_data       = $external_db->table($itemtaxmst_tbl)
				->select('bill_hsn_sac,bill_tax_account,cmp_tax_cat_id,bill_input_output')
				->where('bill_sundry_id',$row['bill_sundry_id'])
				->orderBy('cmp_tax_cat_id','DESC')
				->limit(1)
				->get()->getRowArray();
				if($tax_data){			
			// get gst tax rates from  cmpgstcatn
					$tax_rates          = $this->cmpgstcatn_info($company_id,$comp_fy_id,$comp_code,$tax_data['cmp_tax_cat_id']);			
					$item_hsn_sac       = $tax_data['bill_hsn_sac'];
					$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
					
					if($tax_data['bill_tax_account']=="1")
						$is_tax_account     = "1"; 	
					else 
						$is_tax_account     = "0"; 				 
					if($tax_rates){
						$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
						$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
						$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
						$cess_basis         = $tax_rates['cmp_tax_cat_cess_basis'] ?? 1;
						
					}
					else{
						$item_tax_igst_rate = 0;
						$item_tax_cess_rate = 0;
						$item_tax_wef       = '';	
						$cess_basis         = 1;	
					}
					if(isset($tax_data['bill_input_output']))
						$bill_input_output  = $tax_data['bill_input_output'];
					else
						$bill_input_output  = '';
					if(isset($tax_data['bill_supply_type']))
						$bill_supply_type   = $tax_data['bill_supply_type'];
					else
						$bill_supply_type   = 0;	
				}else{
					$item_hsn_sac       = '';
					$tax_cat_id         = '';
					$item_tax_igst_rate = 0;
					$item_tax_cess_rate = 0;
					$item_tax_wef       = '';
					$bill_input_output  = 0;	
					$bill_supply_type   = 0;
					$cess_basis         = 1;
					$is_tax_account     = "0"; 		
				}

				$is_cc = 0;
				if(in_array($row['acc_grp_id'], $cc_groups) || in_array($row['acc_grp_parent_id'], [7,11,13])){
					$is_cc = 1;
				}

				$final_result[] = array(
					"label"         => html_entity_decode(ucwords($row['label'])),
					'value'         => html_entity_decode($row['value']),
					'id'            => $row['bill_sundry_id'],
					'account_id'    => $row['bill_sundry_id'],
					'acc_id'        => $row['bill_sundry_id'],
					'is_sundry'     => '1',
					'is_acc'        => '0',
					'is_bbb'        => 0,
					'is_cc'	        => $is_cc,						
					'tx_ct_id'	    => $tax_cat_id,
					'bl_sply_tpe'	=> $bill_supply_type,
					'bl_hsn_sac'	=> $item_hsn_sac,
					'bl_tx_igst_rte'=> parseAmount($item_tax_igst_rate),
					'b_tx_cess_rte'	=> parseAmount($item_tax_cess_rate),
					'bl_tx_wef'	    => $item_tax_wef,
					'bl_ipt_ott'	=> $bill_input_output,
					'bl_nature'     => $row['sundry_nature'],
					'is_tax_account'=> $is_tax_account,
					'cess_basis'    => $cess_basis,
					'acc_short_code' =>''
				);	
			}
		}
		
		
		
		
		return json_encode($final_result,JSON_UNESCAPED_SLASHES);
	}
	
	
	function get_company_fy_info($company_id){	 
		return $this->aicountly_db->table('aicountly_cmpfymastr_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
	}


	function get_master_groups(){
		$data =  $this->erp_db->table('aictlyerp_grpparentn_univdb')->orderBy('acc_grp_parent_id')->get()->getResultArray();
		$final_result = array();
		if($data){
			foreach($data as $row){
				$final_result[] = [
					'acc_grp_parent_id'	=>	$row['acc_grp_parent_id'],
					'grp_name'			=> 	$row['grp_name'],
					'acc_grp_restrict'		=> 	$row['acc_grp_restrict']
				];		   
			}
		}
		return $final_result;
	} 
	
	
	function get_common_comp_values($tag){
		$data =  $this->erp_db->table('aictlyerp_newcompval_univdb')->where('tag',$tag)->orderBy('id')->get()->getResultArray();
		$final_result = array();
		$final_result['']  = 'Choose';
		if($data){
			foreach($data as $row){
              $final_result[$row['id']] = $row['field_value'];//ucwords(strtolower($row['field_value']));			   
            }
          }
          return $final_result;
          
        } 
        
        function load_common_vouchers(){
        	$table_name = "aictlyerp_vchtypeidn_univdb";
        	$builder    = $this->erp_db->table($table_name);
        	$result = $builder->get()->getResultArray();
        	$final_list = array();
        	if($result){
        		foreach($result as $row){				
        			$final_list[$row['vch_type_id']] = ucwords(strtolower($row['vch_name']));	
        		}			
        	}
        	return $final_list;  
        }
        
        function get_company_gen_info($company_id){	 
        	return $this->aicountly_db->table('aicountly_compidgenr_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
        }
        
        
        

 
	public function get_business_id()
	{
		
		return "N/A";
		$sispl_uuid_db = $this->externaldb->sispl_uuid_db();

		$uuid = $this->session->get('uuid');
		$builder = $sispl_uuid_db->table('sispluuid_bussisplnn_univdb');
		$builder->select('sispluuid_bussisplnn_univdb.*');
		$builder->join('sispluuid_busuuidmap_univdb','sispluuid_busuuidmap_univdb.bisiness_id=sispluuid_bussisplnn_univdb.bisiness_id');
		$builder->where('sispluuid_busuuidmap_univdb.uuid', $uuid);
		$builder->where('user_domain','aicountly.com');
		$result = $builder->get()->getRowArray();		
		if($result){
			return $result['business_id'];
		}
		return 0;
	}
	
	function company_all_fy_list(){
		return $this->univaictly->table("cmpfymastr")
		->where('cmp_id',$this->company_id)->orderBy('fy_beg_date')->get()->getResultArray();
	}
	
	public function company_profile_info()
	{
	 $uuid = $this->session->get('uuid');//$uuid = $this->session->get('comp_uuid');
	 return $this->aicountly_db->table('aicountly_useraictly_univdb')->where('uuid', $uuid)->get()->getRowArray();
	}
	public function my_account($auth_code,$auth_time) 
	{
		$uuid = $this->session->get('uuid');
		$this->aicountly_db->table('aicountly_useraictly_univdb')->where('uuid', $uuid)
		->update(['auth_code'=> $auth_code, 'auth_time'=> $auth_time]);
	}

	public function auth() 
	{
		$user = [];
		 if($this->session->get('uuid'))
		{
			$uuid = $this->session->get('uuid');
			$user = $this->uuidaictly->table('useraictly')
			->select('uuid, user_firstname, user_lastname, user_regdemail, user_regdmobile')
			->where('uuid', $uuid)
			->get()->getRowArray();
		} 
		
		return $user;
	}

	public function add_business($data)
	{
		$sispl_uuid_db = $this->externaldb->sispl_uuid_db();
		$sispl_uuid_db->table('sispluuid_bussisplnn_univdb')->insert($data);
		return $this->db->insertID();
	}



	public function update_user_taburl($utab_id,$tab_name,$tab_url,$menuid){
		$s_ctab =$this->session->get('s_ctab');
		$visted_links_list  = $this->session->get('visted_links_list');
		$visited_links = array();
		
		if($_SERVER['QUERY_STRING'])
			$tab_url = $tab_url.'?'.$_SERVER['QUERY_STRING'];
		
		
		if($visted_links_list){
			
			
			foreach($visted_links_list as $key => $urls){
				foreach($urls as $ukey => $url){
					$visited_links[$key][] = $url;
				}
			}
		}
		else{
		           // first time add
			$visited_links[$s_ctab][] = $tab_url;  
		}
		
		
		if(!isset($visited_links[$s_ctab])){
			$visited_links[$s_ctab][] = $tab_url; 
			
		}
		else{
		     // if(!in_array($tab_url,$visited_links[$s_ctab]))  
			$visited_links[$s_ctab][] = $tab_url; 
			
		}
		
		$this->session->set('visted_links_list',$visited_links);
		$ses_menu_item  = $this->session->get('menu_item');
		$updated_menus = array();
		if($ses_menu_item){
			foreach($ses_menu_item as $row){
				$tab_id   = $row['menuid'];
				$tburl    = $row['url'];
				$tabname  = $row['name'];
				if($s_ctab==$tab_id)
					$updated_menus[] = array("name"=>$tab_name,"url"=>$tab_url,"quantity"=>"1","menuid"=>$tab_id);  
				else
					$updated_menus[] = array("name"=>$tabname,"url"=>$tburl,"quantity"=>"1","menuid"=>$tab_id);  
			}
		}
		
		$this->session->set('menu_item', $updated_menus);
	} 

	

	public function user_company_list()
{
    $uuid = $this->session->get('uuid');
    $records = [];

    // Fetch owned companies
    $records = array_merge($records, $this->getOwnedCompanies($uuid));

    // Fetch shared companies
    $records = array_merge($records, $this->getSharedCompanies($uuid));

    // Fetch owned groups
    $records = array_merge($records, $this->getOwnedGroups($uuid));

    // Fetch shared groups
    $records = array_merge($records, $this->getSharedGroups($uuid));

    return $records;
}

// ------------------- Reusable Private Methods ---------------------

private function getOwnedCompanies($uuid)
{   return [];

    /* $builder = $this->aicountly_db->table("aicountly_compidgenr_univdb");
    $builder->join('aicountly_cmpmastern_univdb', 'aicountly_compidgenr_univdb.comp_id = aicountly_cmpmastern_univdb.comp_id');
    $builder->select('aicountly_cmpmastern_univdb.comp_id, comp_name, comp_short_name, comp_code');
    $builder->where('aicountly_compidgenr_univdb.uuid', $uuid);
    $builder->where('aicountly_cmpmastern_univdb.comp_db_status', 'active');
    $builder->orderBy('comp_code', 'DESC');

    $result = $builder->get()->getResultArray();

    return array_map(function ($val) {
        return [
            'encomp_id'    => obfuscate_link($val['comp_id']),
            'ownership'    => 'owner',
            'comp_type'    => 'cmp',
            'comp_id'      => $val['comp_id'],
            'comp_name'    => $val['comp_name'],
            'company_name' => $val['comp_name'],
            'comp_code'    => $val['comp_code']
        ];
    }, $result); */
}

private function getSharedCompanies($uuid)
{
	return [];
    /* $builder = $this->aicountly_db->table("aicountly_cmpidacsnn_univdb");
    $builder->select('comp_id');
    $builder->where('uuid_access', $uuid);
    $builder->where('comp_type', 'cmp');
    $builder->groupBy('comp_id');
    $builder->orderBy('comp_id', 'DESC');

    $comp_ids = array_column($builder->get()->getResultArray(), 'comp_id');

    if (empty($comp_ids)) return [];

    $builder = $this->aicountly_db->table("aicountly_cmpmastern_univdb");
    $builder->select('comp_id, comp_name, comp_code');
    $builder->whereIn('comp_id', $comp_ids);
    $builder->where('comp_db_status', 'active');
    $companies = $builder->get()->getResultArray();

    return array_map(function ($comp) {
        return [
            'encomp_id'    => obfuscate_link($comp['comp_id']),
            'ownership'    => 'shared',
            'comp_type'    => 'cmp',
            'comp_id'      => $comp['comp_id'],
            'comp_name'    => $comp['comp_name'],
            'company_name' => $comp['comp_name'],
            'comp_code'    => $comp['comp_code']
        ];
    }, $companies); */
}

private function getOwnedGroups($uuid)
{   return [];
    /* $builder = $this->aicountly_db->table("aicountly_grpmasternn_univdb");
    $builder->select('grpco_id, grpco_name');
    $builder->where('uuid', $uuid);
    $builder->orderBy('grpco_id', 'DESC');

    $result = $builder->get()->getResultArray();

    return array_map(function ($val) {
        return [
            'encomp_id'    => obfuscate_link($val['grpco_id']),
            'ownership'    => 'owner',
            'comp_type'    => 'grp',
            'comp_id'      => $val['grpco_id'],
            'comp_name'    => $val['grpco_name'],
            'company_name' => $val['grpco_name'],
            'comp_code'    => 'grp' . str_pad($val['grpco_id'], 7, '0', STR_PAD_LEFT),
        ];
    }, $result); */
}

private function getSharedGroups($uuid)
{   return [];
    /* $builder = $this->aicountly_db->table("aicountly_cmpidacsnn_univdb");
    $builder->select('comp_id');
    $builder->where('uuid_access', $uuid);
    $builder->where('comp_type', 'grp');
    $builder->groupBy('comp_id');
    $builder->orderBy('comp_id', 'DESC');

    $grp_ids = array_column($builder->get()->getResultArray(), 'comp_id');

    if (empty($grp_ids)) return [];

    $builder = $this->aicountly_db->table("aicountly_grpmasternn_univdb");
    $builder->select('grpco_id, grpco_name');
    $builder->whereIn('grpco_id', $grp_ids);
    $groups = $builder->get()->getResultArray();

    return array_map(function ($grp) {
        return [
            'encomp_id'    => obfuscate_link($grp['grpco_id']),
            'ownership'    => 'shared',
            'comp_type'    => 'grp',
            'comp_id'      => $grp['grpco_id'],
            'comp_name'    => $grp['grpco_name'],
            'company_name' => $grp['grpco_name'],
            'comp_code'    => 'grp' . str_pad($grp['grpco_id'], 7, '0', STR_PAD_LEFT),
        ];
    }, $groups); */
}
	
	function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
		$dates = [];
		$current = strtotime( $first );
		$last = strtotime( $last );

		while( $current <= $last ) {

			$dates[] = date( $format, $current );
			$current = strtotime( $step, $current );
		}

		return $dates;
	}
	
	function calculateFiscalYearForDate($month)
	{	
		if($month > 4)
		{
			$y  = date('Y');
			$pt = date('Y', strtotime('+1 year'));
		// 		$fy = array("start_date"=>$y."-04-01" ,"end_date"=>$pt."-03-31");
			$fy = array("start_date"=>"01-04-".$y ,"end_date"=>"31-03-".$pt);
		}
		else
		{
			$y = date('Y', strtotime('-1 year'));
			$pt = date('Y');
		// 		$fy = array("start_date"=>$y."-04-01","end_date"=>$pt."-03-31");
			$fy = array("start_date"=>"01-04-".$y ,"end_date"=>"31-03-".$pt);
		}
		return $fy;
	}	
	
	public function mycompanies(){
		$uuid            =  $this->session->get('uuid');

		$my_companies= array();
		$builder = $this->aicountly_db->table("aicountly_compidgenr_univdb"); 
		$builder->where('uuid',$uuid);
		$result = $builder->get()->getResultArray();
		if($result){
			foreach($result as $row){
				$my_companies[$row['comp_id']]=$row['comp_id'];
				
			}
			
			
		}
		
		return $my_companies; 
	}    
	
	public function GetCmpProfileInfo($company_id){
		$uuid    =  $this->session->get('uuid');
		$builder = $this->univaictly->table('cmpacsmstr')
					->select('erp_acs_prof_id')
					->where('cmp_id', $company_id)
					->groupStart()
						->where('uuid_aictly_acs IS NULL')
						->where('uuid_aictly_by', $uuid)
					->groupEnd()
					->orGroupStart()
						->where('uuid_aictly_acs IS NOT NULL')
						->where('uuid_aictly_acs', $uuid)
					->groupEnd();

		return $builder->get()->getRowArray();
		
	}
	
	private function b64urlEncode(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }
	 // Encrypt a numeric ID to URL-safe token
    protected function encryptId(int $id): string
    {
        $encrypter = Services::encrypter(); // uses Config\Encryption
        $cipher    = $encrypter->encrypt((string) $id); // binary
        return $this->b64urlEncode($cipher);
    }
	
	public function ajax_recyclebin_company_list()
{
    $uuid    = $this->session->get('uuid');
    $records = [];

    // DB handles
    $pgMain = $this->db;                            // has cmpmastern, cmprecycle
    $pgAcs  = $this->univaictly;       // has cmpacsmstr

    // 1) Fetch company access rows for this user (uuid)
    $acsRows = $pgAcs->table('cmpacsmstr')
        ->select('cmp_id, uuid_acs_type, uuid_aictly_by')
        ->where('uuid_aictly_by', $uuid)
        ->get()
        ->getResultArray();

    if (empty($acsRows)) {
        return $records; // no companies for this uuid
    }

    // Map cmp_id => access info
    $acsMap   = [];
    $cmpIdArr = [];
    foreach ($acsRows as $r) {
        $cid = (int)$r['cmp_id'];
        $cmpIdArr[] = $cid;
        $acsMap[$cid] = [
            'uuid_acs_type' => (int)($r['uuid_acs_type'] ?? 0),
            'uuid'          => $r['uuid_aictly_by'] ?? '',
        ];
    }

    // 2) Fetch companies from cmpmastern + recycle info
    $builder = $pgMain->table('cmpmastern cmp');
    $builder->select('cmp.*, rcl.cmp_recycle_date');
    $builder->join('cmprecycle rcl', 'rcl.cmp_id = cmp.cmp_id', 'left');
    $builder->whereIn('cmp.cmp_id', $cmpIdArr);
    $builder->where('cmp.cmp_status', 0);
    $builder->orderBy('cmp.cmp_name', 'DESC');
    $result = $builder->get()->getResultArray();

    if (!empty($result)) {
        foreach ($result as $values) {
            $cmpId = (int)$values['cmp_id'];

            $finyear = '';
            $comp_fy_info = $this->get_comp_fy_info($cmpId);
            if ($comp_fy_info) {
                $bgn_date = date('d-m-Y', strtotime($comp_fy_info['fy_beg_date']));
                $end_date = date('d-m-Y', strtotime($comp_fy_info['fy_end_date']));
                $finyear  = $bgn_date . ' -- ' . $end_date;
            }

            $acsType = $acsMap[$cmpId]['uuid_acs_type'] ?? 0;
            $company_type = ($acsType == 1) ? 'owner' : 'shared';
            $uuidVal = $acsMap[$cmpId]['uuid'] ?? '';

            $records[] = [
                'encomp_id'          => $this->encryptId($cmpId),
                'uuid_aicountly'     => $uuidVal,
                'company_type'       => $company_type,
                'company_id'         => $cmpId,
                'companyname'        => $values['cmp_name'],
                'company_short_name' => $values['cmp_short_name'],
                'companycode'        => erp_compcode_format($cmpId),
                'finyear'            => $finyear,
                'recycled_date'      => !empty($values['cmp_recycle_date'])
                    ? date("d-m-Y", strtotime($values['cmp_recycle_date']))
                    : '',
            ];
        }
    }

    return $records;
}
	
	public function restore_company_info($comp_id){
	  $this->univaictly->table("cmprecycle")->where('cmp_id',$comp_id)->delete();
	  $this->univaictly->table('cmpmastern')->where('cmp_id', $comp_id)->update(["cmp_status"=>1]); 
	
	}
	
	public function remove_company_permanent($company_id){				
		$this->univaictly->table("cmpaddrmst")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("cmpbankmst")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("cmpfymastr")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("cmpmastern")->where('cmp_id',$company_id)->delete();		
		$this->univaictly->table("cmpmstdetn")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("hoboaddrmt")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("hobogstdet")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("hobogstinm")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("hobomaster")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("hobotanmst")->where('cmp_id',$company_id)->delete();
		$this->univaictly->table("cmprecycle")->where('cmp_id',$company_id)->delete();
	  }

	function get_comp_vouchers_count($comp_code,$comp_id,$fy_id)
	{
		$vhtxnconso_tbl = $comp_id.'_vhtxnconso_'.$fy_id;
		$comp_db = $this->externaldb->single_company_db($comp_code);

		$count = 0;
		$result = $comp_db->table($vhtxnconso_tbl)
											->select('count(*) as total')
											->get()->getRowArray();
		if(!empty($result['total']))
			$count = $result['total'];

		return $count;
	}

	function get_comp_size($comp_id,$comp_code)
	{
		$db_name  = 'aicountlyin_'.$comp_code;
		$comp_db  = $this->externaldb->single_company_db($comp_code);
		$response = $comp_db->query("SELECT table_schema AS 'Database', SUM(data_length + index_length) AS 'size' FROM information_schema.TABLES where table_schema='".$db_name."' GROUP BY table_schema;")->getRowArray();
        $size     = $response['size'];
		$convert_size_data = $this->convert_filesize($size);
        return $convert_size_data;
	}
	
	function convert_filesize($bytes, $decimals = 2){
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
	
	public function ajax_my_companies_list(){
		if(isset($_POST["pq_filter"])){
			$pq_filter     = $_POST["pq_filter"];	       
			$filter_data  = json_decode($_POST["pq_filter"],true);
			$pq_filters   = $filter_data['data'][0];
			$search_text = strtolower($pq_filters['value']);
			$dataIndx    = $pq_filters['dataIndx']; 
		}
		else{
			$pq_filter     ='';
			$search_text   ='';
			$dataIndx      ='';
			
		}
		
		
		$uuid =  $this->session->get('uuid');
		$records = array();
		
		$builder = $this->univaictly->table("cmpmastern cmp");
		$builder->select("cmp.*, acs.uuid_aictly_by,acs.uuid_acs_type,acs.uuid_aictly_by as uuid");
		$builder->join("cmpacsmstr acs", "cmp.cmp_id = acs.cmp_id", 'left');
		$builder->where('acs.uuid_aictly_by', $uuid);
		$builder->where('acs.uuid_acs_type', 1);
		$builder->where('cmp.cmp_status', 1);
		
		if ($search_text !== '') {
			$builder->groupStart();
			$builder->like('LOWER(cmp.cmp_name)', strtolower($search_text), 'both');
			$builder->orLike('LOWER(cmp.cmp_print_name)', strtolower($search_text), 'both');
			$builder->orLike('LOWER(cmp.cmp_short_name)', strtolower($search_text), 'both');
			$builder->groupEnd();
		}
		
		$builder->orderBy('cmp.cmp_name', 'DESC');
		$result = $builder->get()->getResultArray();
		if(!empty($result)){
			foreach($result as $values){

				$finyear = '';
				$comp_fy_info = $this->get_comp_fy_info($values['cmp_id']);
				if($comp_fy_info){
					$bgn_date = $comp_fy_info['fy_beg_date'];
					$end_date = $comp_fy_info['fy_end_date'];

					$bgn_date=date('d-m-Y',strtotime($bgn_date));
					$end_date=date('d-m-Y',strtotime($end_date));

					$finyear = $bgn_date.' -- '.$end_date;
				}
				if($values['uuid_acs_type']==1)
					$company_type ='owner';
				if($values['uuid_acs_type']==0)
					$company_type ='shared';
				$records[] = array(
					'encomp_id'          =>  obfuscate_link($values['cmp_id']),
					'uuid_aicountly'     =>  $values['uuid'],
					'company_type'       =>  $company_type,
					'company_id'         =>  $values['cmp_id'],
					'companyname'        =>  $values['cmp_name'],
					'company_short_name' =>  $values['cmp_short_name'],
					'companycode'        =>  erp_compcode_format($values['cmp_id']),
					'finyear'            =>  $finyear,
				);    
				
			} 	       
		}
		
		$total_Records = count($records);
		if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
		{
			$pq_curPage = (int)$_POST["pq_curpage"];
			$pq_rPP     = (int)$_POST["pq_rpp"];
		} 
		
		if($pq_curPage==0){$pq_curPage=1;}
		$offset = ($pq_rPP * ($pq_curPage - 1));
		if ($offset > $total_Records)
		{        
			$pq_curPage = ceil($total_Records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		} 
		
		
		$final_records = array_slice( $records, $offset, $pq_rPP );
		echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";  
		
	}

	public function ajax_my_companies_list_drive(){
		$mycompanies = $this->mycompanies();

		if(isset($_POST["pq_filter"])){
			$pq_filter     = $_POST["pq_filter"];	       
			$filter_data  = json_decode($_POST["pq_filter"],true);
			$pq_filters   = $filter_data['data'][0];
			$search_text = strtolower($pq_filters['value']);
			$dataIndx    = $pq_filters['dataIndx']; 
		}
		else{
			$pq_filter     ='';
			$search_text   ='';
			$dataIndx      ='';
		}		
		
		$records = array();
		$base_url = base_url().'/';            
		if(!empty($mycompanies)){
			$builder = $this->aicountly_db->table("aicountly_cmpmastern_univdb"); 
			
			
			
			$builder->whereIn('comp_id',$mycompanies);
			$builder->where('comp_db_status','active');
			if($search_text!=''){
				$builder->groupStart();
				$builder->Like('LOWER(comp_code)',$search_text,'both');
				$builder->orLike('LOWER(comp_name)',$search_text,'both');
				$builder->orLike('LOWER(comp_print_name)',$search_text,'both');
				$builder->groupEnd();
				
			}
			
			$builder->orderBy('comp_code','DESC');	
			$result = $builder->get()->getResultArray();
			
			foreach($result as $values){

				$fy_list = [];

				$comp_fy_list = $this->get_comp_fy_list($values['comp_id']);
				if($comp_fy_list){
					foreach ($comp_fy_list as $comp_fy)
					{
						$bgn_date = $comp_fy['fy_begndt'];
						$end_date = $comp_fy['fy_end'];

						$fy_name = get_fy_name($bgn_date,$end_date);
						$fy_str = date('d-m-Y',strtotime($bgn_date)).' -- '.date('d-m-Y',strtotime($end_date));

						$vouchers = $this->get_comp_vouchers_count($values['comp_code'],$values['comp_id'],$comp_fy['comp_fy_id']);

		        $fy_list[] = [
		        	'fy_id'				=> $comp_fy['comp_fy_id'],
		        	'fy_name'			=> $fy_name,
		        	'fy_str'			=> $fy_str,
		        	'vouchers'		=> $vouchers,
		        ];
					}
				}


				$finyear = '';
				$comp_fy_info = $this->get_comp_fy_info($values['comp_id']);
				if($comp_fy_info){
					$bgn_date = $comp_fy_info['fy_begndt'];
					$end_date = $comp_fy_info['fy_end'];

					$bgn_date=date('d-m-Y',strtotime($bgn_date));
					$end_date=date('d-m-Y',strtotime($end_date));

					$finyear = $bgn_date.' -- '.$end_date;

					$comp_fy_id = $comp_fy_info['comp_fy_id'];
				}

				$records[] = array(	
					'comp_id'	 => $values['comp_id'],
					'comp_type'	 => 'cmp',
					'comp_name'	 => $values['comp_name'],
					'comp_size'	 => '',
					'comp_code'  => $values['comp_code'],
					'fy_list' 	 => $fy_list,

					'company_id' =>$values['comp_id'],
					'encomp_id'       =>  obfuscate_link($values['comp_id']),
					'companyname'=>$values['comp_name'],
					'company_short_name'=>$values['comp_short_name'],
					'companycode'=>$values['comp_code'],
					'finyear'=>$finyear
				);    
				
			} 	       
		}  
		else{
			$total_Records =0;
			$pq_curPage    =1;
			$records       =[];
			
		}
		$total_Records = count($records);
		if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
		{
			$pq_curPage = (int)$_POST["pq_curpage"];
			$pq_rPP     = (int)$_POST["pq_rpp"];
		} 
		

		
		

		
		
		// echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";  

		return $records;
		
	}
	
	function recreate_missing_tables($company_id,$comp_fy_id){
		$ERPtables = new ERPtables($company_id,$comp_fy_id); 
		$response = $ERPtables->memo_txn_tables(0,1);
		$response = $ERPtables->vchaddinfo_tables(0,1);
		
	}
	
	public function ajax_archive_companies_list(){
		$mycompanies = $this->mycompanies();

		if(isset($_POST["pq_filter"])){
			$pq_filter     = $_POST["pq_filter"];	       
			$filter_data  = json_decode($_POST["pq_filter"],true);
			$pq_filters   = $filter_data['data'][0];
			$search_text = strtolower($pq_filters['value']);
			$dataIndx    = $pq_filters['dataIndx']; 
		}
		else{
			$pq_filter     ='';
			$search_text   ='';
			$dataIndx      ='';
		}		
		
		$records = array();
		$base_url = base_url().'/';            
		if(!empty($mycompanies)){
			$builder = $this->aicountly_db->table("aicountly_cmpmastern_univdb"); 
			
			
			
			$builder->whereIn('comp_id',$mycompanies);
			$builder->where('comp_db_status','active');
			if($search_text!=''){
				$builde->groupStart();
				$builder->Like('LOWER(comp_code)',$search_text,'both');
				$builder->orLike('LOWER(comp_name)',$search_text,'both');
				$builder->orLike('LOWER(comp_print_name)',$search_text,'both');	    
				$builde->groupEnd();
				
			}
			
			$builder->orderBy('comp_code','DESC');	
			$result = $builder->get()->getResultArray();
			
			foreach($result as $values){
				$records[] = array(	
					'company_id'=>$values['comp_id'],
					'encomp_id'       =>  obfuscate_link($values['comp_id']),
					'companyname'=>$values['comp_name'],
					'company_short_name'=>$values['comp_short_name'],
					'companycode'=>$values['comp_code'],
					'finyear'=>date("d-m-Y", strtotime($values['fy_begndt']))
				);    
				
			} 	       
		}  
		else{
			$total_Records =0;
			$pq_curPage    =1;
			$records       =[];
			
		}
		$total_Records = count($records);
		if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
		{
			$pq_curPage = (int)$_POST["pq_curpage"];
			$pq_rPP     = (int)$_POST["pq_rpp"];
		} 
		
		if($pq_curPage==0){$pq_curPage=1;}
		$offset = ($pq_rPP * ($pq_curPage - 1));
		if ($offset > $total_Records)
		{        
			$pq_curPage = ceil($total_Records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		} 
		
		
		$final_records = array_slice( $records, $offset, $pq_rPP );
		
		
		echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";  
		
	}
	
	
	
	public function ajax_company_list(){
		$mycompanies = $this->mycompanies();

		$records = array();
		$base_url = base_url().'/';            
		if(!empty($mycompanies)){
			$builder = $this->aicountly_db->table("aicountly_cmpmastern_univdb"); 
			$builder->whereIn('comp_id',$mycompanies);
			$builder->where('comp_db_status','active');
			$builder->orderBy('comp_code','DESC');		 
			$result = $builder->get()->getResultArray();
			
			foreach($result as $values){
				$records[] = array(	
					'company_id'=>$values['comp_id'],
					'encomp_id'       =>  obfuscate_link($values['comp_id']),
					'company_name'=>$values['comp_name'],
					'company_short_name'=>$values['comp_short_name'],
					'company_code'=>$values['comp_code'],
					'fy_begndt'=>date("d-m-Y", strtotime($values['fy_begndt']))
				);    
				
			} 	       
		}
		return $records;
	}
	
	public function mysharedcompanies(){
		

		$uuid = 	 $this->session->get('uuid');
		
		$my_companies= array();
		$builder = $this->aicountly_db->table("aicountly_cmpidacsnn_univdb"); 
		$builder->where('uuid_access',$uuid);
		$result = $builder->get()->getResultArray();
		if($result){
			foreach($result as $row){
				$my_companies[$row['comp_id']]=$row['comp_id'];
				
			}
			
			
		}
		
		return $my_companies; 
	}
	
	
	public function ajax_all_companies_list()
{
    // Get filter parameters
    if (isset($_POST["pq_filter"])) {
        $pq_filter    = $_POST["pq_filter"];
        $filter_data  = json_decode($_POST["pq_filter"], true);
        $pq_filters   = $filter_data['data'][0];
        $search_text  = strtolower($pq_filters['value']);
        $dataIndx     = $pq_filters['dataIndx'];
    } else {
        $pq_filter   = '';
        $search_text = '';
        $dataIndx    = '';
    }

    // Get pagination parameters
    $pq_curPage = 1;
    $pq_rPP     = 10;
    if (isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"])) {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $pq_rPP     = (int)$_POST["pq_rpp"];
    }
    if ($pq_curPage == 0) {
        $pq_curPage = 1;
    }

    $uuid    = $this->session->get('uuid');
    $records = array();

    // ============================================================
    // Step 1: Query MySQL database for access control data
    // ============================================================
    
    $acsBuilder = $this->univaictly->table("cmpacsmstr");
    $acsBuilder->select("cmp_id, uuid_aictly_by, uuid_acs_type, uuid_aictly_acs");
    $acsBuilder->groupStart();
    $acsBuilder->where('uuid_aictly_by', $uuid);       // Owned by user
    $acsBuilder->orWhere('uuid_aictly_acs', $uuid);    // Shared with user
    $acsBuilder->groupEnd();
    $acsRecords = $acsBuilder->get()->getResultArray();

    // If no access records found, return empty result
    if (empty($acsRecords)) {
        echo '{"totalRecords":0,"curPage":1,"data":[]}';
        return;
    }

    // Extract cmp_ids and index access records by cmp_id
    $cmpIds     = array();
    $acsIndexed = array();
    foreach ($acsRecords as $acs) {
        $cmpIds[] = $acs['cmp_id'];
        $acsIndexed[$acs['cmp_id']] = $acs;
    }

    // ============================================================
    // Step 2: Query PostgreSQL database for company data
    // ============================================================
    $builder = $this->univaictly->table("cmpmastern AS cmp");
    $builder->select("cmp.*");
    $builder->where('cmp.cmp_status', 1);
    $builder->whereIn('cmp. cmp_id', $cmpIds);

    // Apply search filter if provided
    if ($search_text !== '') {
        $builder->groupStart();
        $builder->where("LOWER(cmp.cmp_name) LIKE", '%' . strtolower($search_text) . '%');
        $builder->orWhere("LOWER(cmp. cmp_print_name) LIKE", '%' . strtolower($search_text) . '%');
        $builder->orWhere("LOWER(cmp. cmp_short_name) LIKE", '%' . strtolower($search_text) . '%');
        $builder->groupEnd();
    }

    $builder->orderBy('cmp. cmp_name', 'DESC');
    $cmpRecords = $builder->get()->getResultArray();

    // ============================================================
    // Step 3: Merge results and build response
    // ============================================================
    if (! empty($cmpRecords)) {
        foreach ($cmpRecords as $values) {
            $cmpId       = $values['cmp_id'];
            $uuidAcsType = isset($acsIndexed[$cmpId]) ? $acsIndexed[$cmpId]['uuid_acs_type'] : null;
            $uuidValue   = isset($acsIndexed[$cmpId]) ? $acsIndexed[$cmpId]['uuid_aictly_by'] : null;

            $company_type = '';
            if ($uuidAcsType == 1) {
                $company_type = 'owner';
            }
            if ($uuidAcsType == 0) {
                $company_type = 'shared';
            }

            // Get financial year info
            $finyear      = '';
            $comp_fy_info = $this->get_comp_fy_info($values['cmp_id']);
            if ($comp_fy_info) {
                $bgn_date = $comp_fy_info['fy_beg_date'];
                $end_date = $comp_fy_info['fy_end_date'];

                $bgn_date = date('d-m-Y', strtotime($bgn_date));
                $end_date = date('d-m-Y', strtotime($end_date));

                $finyear = $bgn_date . ' -- ' . $end_date;
            }

            $records[] = array(
                'encomp_id'          => obfuscate_link($values['cmp_id']),
                'uuid_aicountly'     => $uuidValue,
                'company_type'       => $company_type,
                'company_id'         => $values['cmp_id'],
                'companyname'        => $values['cmp_name'],
                'company_short_name' => $values['cmp_short_name'],
                'companycode'        => erp_compcode_format($values['cmp_id']),
                'finyear'            => $finyear,
            );
        }
    }

    // ============================================================
    // Step 4: Apply pagination
    // ============================================================
    $total_Records = count($records);

    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_Records) {
        $pq_curPage = ceil($total_Records / $pq_rPP);
        $offset     = ($pq_rPP * ($pq_curPage - 1));
    }

    $final_records = array_slice($records, $offset, $pq_rPP);

    echo '{"totalRecords":' . $total_Records . ',"curPage":' . $pq_curPage . ',"data":' . json_encode($final_records) .  '}';
}

	

	function get_comp_fy_list($comp_id)
	{
		$result=$this->univaictly->table("cmpfymastr")
					->where('cmp_id',$comp_id)
					->orderBy('cmpfymastr_id', 'asc')
					->get()->getResultArray();

		return $result;
	}
	
	public function ajax_all_company_list(){
    $uuid = $this->session->get('uuid');
    $records = array();
    // Step 1: Query MySQL database for access control data
    $acsBuilder = $this->univaictly->table("cmpacsmstr");
    $acsBuilder->select("cmp_id, uuid_aictly_by, uuid_acs_type, uuid_aictly_acs");
    $acsBuilder->groupStart();
    $acsBuilder->where('uuid_aictly_by', $uuid);       // Owned by user
    $acsBuilder->orWhere('uuid_aictly_acs', $uuid);    // Shared with user
    $acsBuilder->groupEnd();
    $acsRecords = $acsBuilder->get()->getResultArray();

    // If no access records found, return empty
    if (empty($acsRecords)) {
        return $records;
    }

    // Extract cmp_ids and index access records by cmp_id
    $cmpIds = array();
    $acsIndexed = array();
    foreach ($acsRecords as $acs) {
        $cmpIds[] = $acs['cmp_id'];
        $acsIndexed[$acs['cmp_id']] = $acs;
    }

    // Step 2: Query PostgreSQL database for company data
    $builder = $this->univaictly->table("cmpmastern AS cmp");
    $builder->select("cmp.*");
    $builder->where('cmp.cmp_status', 1);
    $builder->whereIn('cmp. cmp_id', $cmpIds);
    $builder->orderBy('cmp.cmp_name', 'DESC');
    $cmpRecords = $builder->get()->getResultArray();

    // Step 3: Merge results and build response
    if (!empty($cmpRecords)) {
        foreach ($cmpRecords as $values) {
            $cmpId = $values['cmp_id'];
            $uuidAcsType = isset($acsIndexed[$cmpId]) ? $acsIndexed[$cmpId]['uuid_acs_type'] : null;

            $company_type = '';
            if ($uuidAcsType == 1) {
                $company_type = 'owner';
            }
            if ($uuidAcsType == 0) {
                $company_type = 'shared';
            }

            $records[] = array(
                'encomp_id'          => obfuscate_link($values['cmp_id']),
                'comp_type'          => $company_type,
                'comp_id'            => $values['cmp_id'],
                'company_name'       => $values['cmp_name'],
                'company_short_name' => $values['cmp_short_name'],
                'comp_code'          => erp_compcode_format($values['cmp_id'])
            );
        }
    }
    return $records;
   }
	
	public function get_company_info($company_id)
{
    return $this->univaictly->table('cmpmastern cm')
        ->select([
            'cm.cmp_id',
            'cm.cmp_name',
            'cm.cmp_print_name',
            'cm.cmp_short_name',
            'cm.cmp_status',
            'cd.cmp_cin',
            'cd.cmp_pan',
            'cd.cmp_pan_jurisd',
            'cd.cmp_tel',
            'cd.cmp_mobile',
            'cd.cmp_wa_mobile',
            'cd.cmp_email',
            'cd.cmp_industry',
            'cd.cmp_work_nature',
            'cd.cmp_const_type'
        ])
        ->join('cmpmstdetn cd', 'cd.cmp_id = cm.cmp_id', 'left')
        ->where('cm.cmp_id', $company_id)
        ->get()
        ->getRowArray();
}
	public function get_company_details_info($company_id)
{
    return $this->univaictly->table('cmpmastern cm')
        ->select('cm.*, cd.*')              // or list only the columns you need
        ->join('cmpmstdetn cd', 'cd.cmp_id = cm.cmp_id', 'left')
        ->where('cm.cmp_id', $company_id)
        ->get()
        ->getRowArray();
}
	
	function get_country_info($country_id){
	   return $this->aicountly_db->table('aicountly_countrylst_univdb')->where('countryid',$country_id)->get()->getRowArray();	   		 	
    }
	
	function get_company_address_info($company_id,$comp_addr_type){	 
		return $this->univaictly->table('cmpaddrmst')->where('cmp_addr_type', $comp_addr_type)->where('cmp_id', $company_id)->get()->getRowArray();   	   
	}
	
	
	function CheckLastFYStatus($company_id){
		return $this->aicountly_db->table('aicountly_cmpfymastr_univdb')->where('comp_id', $company_id)->orderBy('comp_fy_id','DESC')->where('is_imported','0')->limit(1)->get()->getRowArray();   	      
		
	} 
	function get_company_max_fy_info($company_id,$comp_fy_id=0){      		
		$builder = $this->univaictly->table('cmpfymastr');		
		if($comp_fy_id != 0)
		$builder->where('cmpfymastr_id', $comp_fy_id);
	    $builder->where('cmp_id', $company_id);
		$builder->orderBy('cmpfymastr_id','DESC');
		$result = $builder->get()->getRowArray();		
		return $result;   	   
	}
	
	function update_company_info($company_id,$data){
		$this->univaictly->table('cmpmastern')->where('cmp_id',$company_id)->update($data);
	}
	
	function get_fy_info($comp_fy_id,$comp_id){		
		return $this->univaictly->table('cmpfymastr')->where('cmpfymastr_id', $comp_fy_id)->where('cmp_id', $comp_id)->get()->getRowArray();   	   
	}
	function get_comp_fy_info($comp_id){		
		return $this->univaictly->table('cmpfymastr')->where('cmp_id', $comp_id)->orderBy('cmpfymastr_id','DESC')->limit(1)->get()->getRowArray();   	   
	}
	function fy_info($comp_fy_id,$comp_id){		
		return $this->univaictly->table('cmpfymastr')->where('cmpfymastr_id', $comp_fy_id)->where('cmp_id', $comp_id)->get()->getRowArray();
	}
	
	function get_comp_taxt_info($comp_id,$bo_id){
		
		return $this->myaicountly_pg->table('comptaxmst')->where('comp_id', $comp_id)->where('bo_id', $bo_id)->get()->getRowArray();   	   
	}
	
	public function get_lastdb_version(){
		$shivansh_db = $this->externaldb->get_shivansherp_db(); 
		$row =  $shivansh_db->query('SELECT `db_version` from `erp_db_version` where `db_status`="4"  order by `db_version_id` DESC limit 1 ')->getRowArray();    
		if($row)
			return $row['db_version'];
		else
			return '';
	}
	public function check_company_tables_info($company_id,$fy_id){
		$table          = $company_id.'_vhtxntrail_'.$fy_id;	
		$company_info   =  $this->get_company_info($company_id);
        $external_db    = $this->externaldb->single_company_db($company_info['comp_code']);
		if ($external_db->tableExists($table)){}
		else{
		 $ERPtables = new ERPtables($company_id,$fy_id);
		$errors = [];
		$response = $ERPtables->default_tables();
		$this->session->set('comp_progress', 45); 
		foreach ($response as $error) { $errors[] = $error; }
			
		$response = $ERPtables->default_data();
		$this->session->set('comp_progress', 50); 
		foreach ($response as $error) { $errors[] = $error; }
		
		$response = $ERPtables->account_master_txn_tables(true,false);
		$this->session->set('comp_progress', 80); 
		foreach ($response as $error) { $errors[] = $error; }
		$response = $ERPtables->bill_sundry_master_txn_tables(true,false);
		$this->session->set('comp_progress', 85); 
		
		foreach ($response as $error) { $errors[] = $error; }
		$uuid           = $this->session->get('uuid');
		$UUIDtables = new UUIDtables($uuid,$company_id,$fy_id);
		$response = $UUIDtables->default_tables(true,false);
		$this->session->set('comp_progress', 90); 
		foreach ($response as $error) { $errors[] = $error; }
		$UUIDtables->update_base_id(); 
		}
	}
	
	
	function recycle_company_info($comp_id,$data){
		$this->univaictly->table('cmprecycle')->insert($data);
		$this->univaictly->table('cmpmastern')->where('cmp_id', $comp_id)->update(["cmp_status"=>0]); 
	}
	
	public function add_comp($data)
	{
		$exists = $this->univaictly->table('cmpmastern')
				->where('LOWER(cmp_name)', strtolower(trim($data['comp_name'])))
				->get()
				->getRowArray();

			if ($exists) {
				return ['company_id' => 0, 'status' => 0, 'message' => 'Company name already exists'];
			}

			$uuid = $this->session->get('uuid');
			$comp_data = [
				"cmp_name"          => $data["comp_name"],
				"cmp_print_name"    => $data["comp_print_name"],
				"cmp_short_name"    => $data["comp_short_name"],
				"cmp_status"        => 1,
				"cmp_last_accessed" => date("Y-m-d H:i:s"),
			];

			$this->univaictly->table('cmpmastern')->insert($comp_data);
			$company_id = $this->univaictly->insertID();			
			
			$access_data = [
				"cmp_id"            => $company_id,
				"uuid_acs_type"     => 1,
				"uuid_aictly_acs"   => null,
				"uuid_aictly_by"    => $uuid,
				"erp_acs_prof_id"   =>0,
				"uuid_acs_datetime" => date("Y-m-d H:i:s"),
			];
			// Insert Company Acces Info	
			
			// Check if access already exists
			$result = $this->univaictly->table('cmpacsmstr')
				->where("cmp_id", $company_id)
				->where("uuid_aictly_by", $uuid)
				->where("uuid_acs_type", 1)
				->get()
				->getRowArray();
			// If not exists, then insert
			if (empty($result)) {
				$this->univaictly->table('cmpacsmstr')->insert($access_data);
			} 
			

			$this->session->set('sscomp_id', $company_id);
			 

			// Insert Registered Office Address
			$comp_ro_adrs_data = [
				"cmp_id"        => $company_id,
				"cmp_addr_type" => "1",
				"cmp_addr1"     => $data["ro_add1"] ?? NULL,
				"cmp_addr2"     => $data["ro_add2"] ?? NULL,
				"cmp_city"      => $data["ro_city"],
				"cmp_state"     => $data["ro_state"],
				"cmp_pin_zip"   => $data["ro_pin"],
				"cmp_country"   => $data["ro_country"],
			];
			$this->univaictly->table('cmpaddrmst')->insert($comp_ro_adrs_data);

			// Insert Corporate Office Address
			$comp_co_adrs_data = [
				"cmp_id"        => $company_id,
				"cmp_addr_type" => "2",
				"cmp_addr1"     => $data["co_add1"] ?? NULL,
				"cmp_addr2"     => $data["co_add2"] ?? NULL,
				"cmp_city"      => $data["co_city"],
				"cmp_state"     => $data["co_state"],
				"cmp_pin_zip"   => $data["co_pin"],
				"cmp_country"   => $data["co_country"],
			];
			$this->univaictly->table('cmpaddrmst')->insert($comp_co_adrs_data);

			// Insert Company Detail Data
			if($data["comp_industry"]!='' && $data["comp_work_nature"]!=''){
			$company_detail_data = [
				'cmp_id'          => $company_id,
				'cmp_cin'         => NULL,
				'cmp_pan'         => NULL,
				'cmp_pan_jurisd'  => NULL,
				'cmp_tel'         => NULL,
				'cmp_mobile'      => NULL,
				'cmp_wa_mobile'   => NULL,
				'cmp_email'       => NULL,
				'cmp_industry'    => $data["comp_industry"] ?? NULL,
				'cmp_work_nature' => $data["comp_work_nature"] ?? NULL,
				'cmp_const_type'  => NULL,
			];
			$this->univaictly->table('cmpmstdetn')->insert($company_detail_data);
			}

			// Financial Year data insertion with validation for fy_enddt
			$fy_begndt = date('Y-m-d', strtotime($data['fy_begndt']));
			$fy_enddt  = $data['fy_enddt'] ?? NULL;
			$fy_end    = $fy_enddt ? date('Y-m-d', strtotime($fy_enddt)) : NULL;

			$fy_data = [
				'cmp_id'         => $company_id,
				'fy_beg_date'    => $fy_begndt,
				'fy_end_date'    => $fy_end,
				'def_val_method' => $data["valmethod_id"],
				"is_imported"    => 1
			];
			$this->univaictly->table('cmpfymastr')->insert($fy_data);
			
			$this->session->set('ses_dflt_val_method',$data["valmethod_id"]);

			$fy_id = $this->univaictly->insertID();
			$this->session->set('ssfy_id', $fy_id);
		
		$errors = [];
			
		   
		$bstate_info   =  $this->get_state_info($data['ro_country'],$data['ro_state']);
		$bstate_code   = $bstate_info['state_code'] ?? 0;		
		$this->session->set('ses_bostecd',$bstate_code);
		$bo_data = [
			'cmp_id'		=> $company_id,
			'mark_ho'		=> 1,
			'hobo_name'		=> 'HO',
			'hobo_alias'	=> 'HO'	,
			'hobo_op_date'  => $fy_begndt,	
			'hobo_cl_date'  => $fy_end
		   ];
		 $this->univaictly->table('hobomaster')->insert($bo_data); 
		 $hobo_id = $this->univaictly->insertID();
		 
		 $adrs_insert_data   = [
		                'cmp_id'      => $company_id,
		                'hobo_id'     => $hobo_id,
						'hobo_addr1'  => $data['ro_add1'],
						'hobo_addr2'  => $data['ro_add2'],
						'hobo_city'   => $data['ro_city'],
						'hobo_state'  => $data['ro_state'],
						'hobo_pin_zip'=> $data['ro_pin'],						
						'hobo_country'=> $data['ro_country']						
					    ];						
		$this->univaictly->table("hoboaddrmt")->insert($adrs_insert_data);
		// ──────────────────────────────────────────────────
		// ✅ ADD THIS LINE: Create default account groups
		// ──────────────────────────────────────────────────
		$this->create_default_groups($company_id, $fy_id);
		// ──────────────────────────────────────────────────
		// Create  default accounts
		// ──────────────────────────────────────────────────
		//$this->create_default_accounts($company_id, $fy_id, $hobo_id, $fy_begndt);
		//  Create default item units
		$this->create_default_units($company_id); 
		//  Create default item groups
		$this->create_default_item_groups($company_id, $fy_id);
		//  Create 23 default voucher series (replaces old single insert)
		$this->create_default_voucher_series($company_id); 
        //  Create default currency (Indian Rupee)
		$this->create_default_currency($company_id);
		 if(count($errors)){
		   return  array('company_id'=>0,'status'=>0,'message'=>'Server Error');				
		} 
		return  array('company_id'=>$company_id,'status'=>1,'message'=>'Company Created');
	   
	} 
	
	
	/**
 * Creates all default account groups when a new company is created.
 * Call this from add_comp() after company, FY, and HO branch are created.
 *
 * @param int $company_id  The newly created company ID
 * @param int $fy_id       The financial year ID (cmpfymastr_id)
 * @return bool
 */
private function create_default_groups(int $company_id, int $fy_id): bool
{
    // ─────────────────────────────────────────────────────────
    // Default groups with their hierarchy mapping
    // Format: group_name => [parent_id, under_main_id]
    //
    // Parent IDs from your chart of accounts:
    //   1 = Capital & Liabilities (Balance Sheet - Liabilities)
    //   2 = Assets (Balance Sheet - Assets)
    //   3 = Income (Profit & Loss)
    //   4 = Expenditure (Profit & Loss)
    //  13 = Current Liabilities (under 1)
    //  14 = Current Assets (under 2)
    //
    // Adjust parent_id values based on YOUR actual primary group IDs
    // ─────────────────────────────────────────────────────────

    $defaultGroups = [
        // Group Name                              => [crs_mst_parent_id, under_main_id]
        'Bank OD / OCC A/c'                        => ['parent' => 1,  'main' => 0],
        'Capital Work In Progress'                  => ['parent' => 2,  'main' => 0],
        'Cash & Cash Equivalents'                   => ['parent' => 2,  'main' => 0],
        'Current Investments'                       => ['parent' => 2,  'main' => 0],
        'Deferred Tax Assets'                       => ['parent' => 2,  'main' => 0],
        'Deferred Tax Liabilities'                  => ['parent' => 1,  'main' => 0],
        'Duties & Taxes'                            => ['parent' => 1,  'main' => 0],
        'Fixed Assets'                              => ['parent' => 2,  'main' => 0],
        'Intangible Assets'                         => ['parent' => 2,  'main' => 0],
        'Intangible Assets Under Development'       => ['parent' => 2,  'main' => 0],
        'Long Term Borrowings'                      => ['parent' => 1,  'main' => 0],
        'Long Term Loans & Advances'                => ['parent' => 2,  'main' => 0],
        'Long Term Provisions'                      => ['parent' => 1,  'main' => 0],
        'Main'                                      => ['parent' => 1,  'main' => 0],
        'Non Current Investments'                   => ['parent' => 2,  'main' => 0],
        'Other Current Assets'                      => ['parent' => 2,  'main' => 0],
        'Other Current Liabilities'                 => ['parent' => 1,  'main' => 0],
        'Other Long Term Liabilities'               => ['parent' => 1,  'main' => 0],
        'Other Non Current Assets'                  => ['parent' => 2,  'main' => 0],
        'Reserves & Surplus'                        => ['parent' => 1,  'main' => 1],
        'Short Term Borrowings'                     => ['parent' => 1,  'main' => 0],
        'Short Term Loans & Advances'               => ['parent' => 2,  'main' => 0],
        'Short Term Provisions'                     => ['parent' => 1,  'main' => 0],
        'Trade Payable'                             => ['parent' => 1,  'main' => 0],
        'Trade Receivables'                         => ['parent' => 2,  'main' => 0],
    ];

    $this->db->transStart();

    try {
        // ── Batch insert all groups into accgrpmstn ──
        $groupInsertData = [];
        foreach ($defaultGroups as $groupName => $hierarchy) {
            $groupInsertData[] = [
                'cmp_id'           => $company_id,
                'acc_grp_name'     => $groupName,
                'acc_grp_alias'    => $groupName,
                'acc_grp_restrict' => 0,
                'acc_grp_is_active'=> 1,
            ];
        }

        // Insert groups one by one to capture each auto-generated ID
        $groupIds = [];
        foreach ($groupInsertData as $grpData) {
            
                $this->db->table('accgrpmstn')->insert($grpData);
                $groupIds[$grpData['acc_grp_name']] = (int)$this->db->insertID();
           
        }

        // ── Insert undercrsmt rows for each group ──
        foreach ($defaultGroups as $groupName => $hierarchy) {
            $groupId = $groupIds[$groupName] ?? null;
            if (!$groupId) {
                continue;
            }

                $underMainId = $hierarchy['main'];
                // If under_main_id references itself (like Reserves & Surplus)
                if ($underMainId === 1 && $groupName === 'Reserves & Surplus') {
                    $underMainId = $groupId;
                }

                $this->db->table('undercrsmt')->insert([
                    'cmp_id'             => $company_id,
                    'crs_mst_type'       => 2,                      // Group type
                    'crs_mst_id'         => $groupId,               // Group ID
                    'under_crs_mst_id'   => 0,                      // Primary = no sub-group parent
                    'crs_mst_parent_id'  => $hierarchy['parent'],   // Parent group ID
                    'under_main_id'      => $underMainId,
                    'cmpfymastr_id'      => $fy_id,
                    'crs_mst_is_primary' => 1,                      // All are Primary = Y
                    'crs_is_active'      => 1,
                ]);
            
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', "Failed to create default groups for company ID: {$company_id}");
            return false;
        }

        return true;

    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', "Exception creating default groups: " . $e->getMessage());
        return false;
    }
}
	
	function industry_type_dropdown()
	{
		$result = $this->aicountly_db->table('aicountly_indstypenn_univdb')
		->get()->getResultArray();
		return $result;
	}

	function natureof_work_dropdown()
	{
		$result = $this->erp_db->table('aictlyerp_worknature_univdb')
		->get()->getResultArray();
		return $result;
	}

	function is_group_company_opened($company_id = 0)
	{
		return 0;
	}
	
	
	function CountryDropdown(){
		$data =  $this->aicountly_db->table('aicountly_countrylst_univdb')->orderBy('countryname')->get()->getResultArray();
		$final_result = array();
		$final_result['']  = 'Choose';
		if($data){
			foreach($data as $row){
				$final_result[$row['countryid']] = ucwords($row['countryname']);			   
			}
		}
		return $final_result;	
	}

	
	
	function get_state_info($country_id,$state_id){
		return $this->aicountly_db->table('aicountly_stateslist_univdb')->where('country_id',$country_id)->where('state_id',$state_id)->get()->getRowArray();	   		 	
	}
	
	function StatesDropdown($country_id=1,$state_id=NULL){
		$data =  $this->aicountly_db->table('aicountly_stateslist_univdb')->where('country_id',$country_id)->orderBy('state_name')->get()->getResultArray();
		$final_result      = array();
		$final_result['']  = 'Choose';
		$selectbox ='';
		if($state_id!=NULL)
			$sel_state = $state_id;
		else
			$sel_state = '25';	
		$selectbox  .='<select name="state_id" id="state_id" class="form-control select2">';
		$selectbox  .='<option value="" data-gstinh="" data-id="" selected>Choose</option>';
		if($country_id>1){
			$hidegstin=1;
		}
		else 
			$hidegstin=0;
		if($data){
			foreach($data as $row){
				$state_code = sprintf( '%02d', $row['state_code']);
				if($sel_state==$row['state_id'])
				$selectbox  .='<option value="'.$row['state_id'].'" data-gstinh="'.$hidegstin.'" data-id="'.$state_code.'" selected>'.ucwords(strtolower($row['state_name'])).'</option>';
			    else
				$selectbox  .='<option value="'.$row['state_id'].'"  data-gstinh="'.$hidegstin.'" data-id="'.$state_code.'">'.ucwords(strtolower($row['state_name'])).'</option>';
				
				$final_result[$row['state_id']] = ucwords(strtolower($row['state_name']));			   
			}
		}
		
		
		$selectbox  .='</select>';
		return $selectbox;
		
		//return form_dropdown('state_id', $final_result, $sel_state,'id="state_id" class="form-control" required');		 	
	}
	
	public function get_country_array()
	{
		$result = $this->aicountly_db->table('aicountly_countrylst_univdb')->get()->getResultArray();
		return $result;
	}
	
	public function get_state_array()
	{
		$result = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
		return $result;
	}

   public function user_preferences_list(){
	  
      $response['usr_Pref_Branch'] = $this->usr_Pref_Branch();	
      $response['usr_Pref_Fy']     = $this->usr_Pref_Fy();
      $response['usr_theme_pref']  = $this->usr_theme_pref();		  
	  
	  return $response;
   }
     public function usr_theme_pref(){
		 $userId  = $this->user_id;
		 $builder = $this->univerpaic_db->table('erpdefpref');	
		 $builder->where('uuid', $userId);
		 $response = $builder->get()->getResultArray();
		 return $response;
	 }
   
    public function usr_Pref_Branch(){
	$comp_id = $this->session->get('ses_company_id') ;
    $builder = $this->erp_db->table("aictlyerp_unverppref_univdb");
    $result = $builder
                ->select('defusr_config_value')
                ->where('defusr_config_id', 375) // It is for Branch Default selection
                ->where('comp_id', $comp_id)
                ->get()
                ->getRow();
    return $result ? $result->defusr_config_value : 1;
   }

 function company_last_fy($company_id){    
	 return $this->univaictly->table('cmpfymastr')->where('cmp_id', $company_id)->orderBy('cmpfymastr_id','DESC')->limit(1)->get()->getRowArray();   	   
  }
		
 public function usr_Pref_Fy()
 {
	$comp_id = $this->session->get('ses_company_id') ;
    $builder = $this->erp_db->table("aictlyerp_unverppref_univdb");
    $result = $builder
                ->select('defusr_config_value')
                ->where('defusr_config_id', 374) 
                ->where('comp_id', $comp_id)
                ->get()
                ->getRow();
    return $result ? $result->defusr_config_value : 1;
  }

  public function get_cmd($search)
  {

	$builder = $this->erp_db->table("aictlyerp_univerpcmd_univdb")
                ->like('erpaic_cmd', $search) 
                ->get()
                ->getResultArray();

				return $builder;
  }

function updateUserMaster($uuid,$data)
	{
		$this->aicountly_db->table('aicountly_useraictly_univdb')
												->where('uuid', $uuid)
												->update($data);

	}
	function saveUserDocument($data)
	{
		$this->aicountly_db->table('aicountly_aicdocidgr_univdb')
												->insert($data);

		return $this->aicountly_db->insertID();
	}


	function getUserLogo($uuid)
	{
		$file = [];
		$comp = $this->aicountly_db->table('aicountly_useraictly_univdb')
												->select('logo')
												->where('uuid', $uuid)
												->get()->getRowArray();

		if(!empty($comp['logo'])){
			$doc_id = $comp['logo'];

			$doc = $this->aicountly_db->table('aicountly_aicdocidgr_univdb')
										->where('doc_id', $doc_id)
										->get()->getRowArray();
			if($doc){
				$file['name'] = $doc['doc_name'];
				$file['ext'] = $doc['doc_file_ext'];
			}
		}

		return $file;
	}
	

// public function commands()
// {
//     return $this->erp_db
//         ->table("aictlyerp_univerpcmd_univdb u") // alias for main table
//         ->select("u.*, p.erppreval_data as crs_name") // get crs_name from join
//         ->join(
//             "aictlyerp_erpprevaln_univdb p", // alias for joined table
//             "u.erpaic_predefine_crstype_id = p.erppreval_id", // join condition
//             "left" // LEFT JOIN to retain unmatched rows
//         )
//         ->get()
//         ->getResultArray();
// }




// public function commands()
// {
//     return $this->dberpunvrsl
//         ->table("aictlyerp_univerpcmd_univdb u") 
//         ->select("u.*, p.erppreval_data as crs_name") 
//         ->join(
//             "aictlyerp_erpprevaln_univdb p", 
//             "u.erpaic_predefine_crstype_id = p.erppreval_id",
//             "left"
//         )
//         ->join(
//             "aictlyerp_uverptoken_univdb t", 
//             "u.token_id = t.token_id",
//             "left"
//         )
//         ->where("t.token_status", 1) 
//         ->get()
//         ->getResultArray();
// }

public function commands()
{
    $db1 = 'aicountlyin_erp';
    $db2 = 'erpaicountly_univerpaic';

    $builder = $this->db->table("{$db2}.erpcmdline u");
    $builder->select("u.*, p.erppreval_data AS crs_name");
    $builder->join("{$db1}.aictlyerp_erpprevaln_univdb p", "u.erp_crs_type_id = p.erppreval_id", "left");
   $builder->join("{$db2}.erpotpaprv t",
                   "u.erpotparpv_id = t.erp_otp_aprv_id",
                   "left");
    $builder->where("t.token_status", 1);

    return $builder->get()->getResultArray();
}

/**
 * Creates all default accounts when a new company is created.
 * Call this from add_comp() AFTER create_default_groups() has run.
 *
 * @param int    $company_id  The newly created company ID
 * @param int    $fy_id       The financial year ID (cmpfymastr_id)
 * @param int    $hobo_id     The HO branch ID
 * @param string $fy_begndt   Financial year start date (Y-m-d)
 * @return bool
 */
private function create_default_accounts(int $company_id, int $fy_id, int $hobo_id, string $fy_begndt): bool
{
    $defaultAccounts = [
        [
            'acc_name'        => 'Capital Account',
            'acc_alias'       => 'Capital Account',
            'acc_print_name'  => 'Capital Account',
            'is_primary'      => 1,
            'group_name'      => "Owner's Fund",
            'parent_id'       => 1,
            'acc_is_restrict'  => 0,
        ],
        [
            'acc_name'        => 'Cash In Hand',
            'acc_alias'       => 'Cash In Hand',
            'acc_print_name'  => 'Cash In Hand',
            'is_primary'      => 0,
            'group_name'      => 'Cash & Cash Equivalents',
            'parent_id'       => 2,
            'acc_is_restrict'  => 1,
        ],
        [
            'acc_name'        => 'GST PAID A/C',
            'acc_alias'       => 'GST PAID A/C',
            'acc_print_name'  => 'GST PAID A/C',
            'is_primary'      => 1,
            'group_name'      => 'Indirect Expenses',
            'parent_id'       => 13,
            'acc_is_restrict'  => 2,
        ],
        [
            'acc_name'        => 'Profit & Loss Appropriation',
            'acc_alias'       => 'Profit & Loss Appropriation',
            'acc_print_name'  => 'Profit & Loss Appropriation',
            'is_primary'      => 0,
            'group_name'      => 'Reserves & Surplus',
            'parent_id'       => 1,
            'acc_is_restrict'  => 3,
        ],
        [
            'acc_name'        => 'Purchase Account',
            'acc_alias'       => 'Purchase Account',
            'acc_print_name'  => 'Purchase Account',
            'is_primary'      => 1,
            'group_name'      => 'Purchase',
            'parent_id'       => 4,
            'acc_is_restrict'  => 1,
        ],
        [
            'acc_name'        => 'Sales Account',
            'acc_alias'       => 'Sales Account',
            'acc_print_name'  => 'Sales Account',
            'is_primary'      => 1,
            'group_name'      => 'Sales',
            'parent_id'       => 3,
            'acc_is_restrict'  => 1,
        ],
    ];

    $this->db->transStart();

    try {
        foreach ($defaultAccounts as $acct) {

            // ── 1. Check if account already exists ──
            $existingAcc = $this->db->table('acctmaster')
                ->select('acc_id')
                ->where('cmp_id', $company_id)
                ->where('UPPER(acc_name) = ' . $this->db->escape(strtoupper($acct['acc_name'])))
                ->get()
                ->getRowArray();

            if ($existingAcc) {
                $acc_id = (int)$existingAcc['acc_id'];
            } else {
                // ── 2. Insert into acctmaster ──
                $this->db->table('acctmaster')->insert([
                    'cmp_id'          => $company_id,
                    'acc_name'        => $acct['acc_name'],
                    'acc_alias'       => $acct['acc_alias'],
                    'acc_print_name'  => $acct['acc_print_name'],
                    'acc_is_active'   => 1,
                    'acc_is_restrict' => $acct['acc_is_restrict'],
                ]);
                $acc_id = (int)$this->db->insertID();
            }

            // ── 3. Resolve group ID from group_name ──
            $groupRow = $this->db->table('accgrpmstn')
                ->select('acc_grp_id')
                ->where('cmp_id', $company_id)
                ->where('UPPER(acc_grp_name) = ' . $this->db->escape(strtoupper($acct['group_name'])))
                ->get()
                ->getRowArray();

            $groupId = $groupRow ? (int)$groupRow['acc_grp_id'] : 0;

            // ── 4. Determine undercrsmt hierarchy ──
            if ($acct['is_primary'] === 1) {
                // PARENT account: sits directly under a parent group
                $under_crs_mst_id   = 0;
                $crs_mst_parent_id  = $acct['parent_id'];
                $under_main_id      = 0;
                $crs_mst_is_primary = 1;

                // If the parent group exists in accgrpmstn, resolve its hierarchy
                if ($groupId > 0) {
                    $grpUnderRow = $this->db->table('undercrsmt')
                        ->select('crs_mst_parent_id, under_main_id, crs_mst_is_primary')
                        ->where('cmp_id', $company_id)
                        ->where('crs_mst_type', 2)
                        ->where('crs_mst_id', $groupId)
                        ->get()
                        ->getRowArray();

                    if ($grpUnderRow) {
                        $crs_mst_parent_id = (int)$grpUnderRow['crs_mst_parent_id'];
                        if ((int)$grpUnderRow['crs_mst_is_primary'] === 1) {
                            $under_main_id = $groupId;
                        }
                    }
                }
            } else {
                // Non-Primary account: sits under a specific sub-group
                $under_crs_mst_id   = $groupId;
                $crs_mst_is_primary = 0;

                // Lookup the sub-group's parent info
                $grpUnderRow = $this->db->table('undercrsmt')
                    ->select('crs_mst_parent_id, under_main_id, crs_mst_is_primary')
                    ->where('cmp_id', $company_id)
                    ->where('crs_mst_type', 2)
                    ->where('crs_mst_id', $groupId)
                    ->get()
                    ->getRowArray();

                if ($grpUnderRow) {
                    $crs_mst_parent_id = (int)$grpUnderRow['crs_mst_parent_id'];
                    if ((int)$grpUnderRow['crs_mst_is_primary'] === 1) {
                        $under_main_id = $groupId;
                    } else {
                        $under_main_id = (int)$grpUnderRow['under_main_id'];
                    }
                } else {
                    $crs_mst_parent_id = $acct['parent_id'];
                    $under_main_id     = $groupId;
                }
            }

            // ── 5. Insert undercrsmt (account hierarchy) ──
            $existingUnder = $this->db->table('undercrsmt')
                ->select('under_crs_mt_id')
                ->where('cmp_id', $company_id)
                ->where('crs_mst_type', 1)
                ->where('crs_mst_id', $acc_id)
                ->get()
                ->getRowArray();

            if (!$existingUnder) {
                $this->db->table('undercrsmt')->insert([
                    'cmp_id'             => $company_id,
                    'crs_mst_type'       => 1,
                    'crs_mst_id'         => $acc_id,
                    'under_crs_mst_id'   => $under_crs_mst_id,
                    'crs_mst_parent_id'  => $crs_mst_parent_id,
                    'under_main_id'      => $under_main_id,
                    'cmpfymastr_id'      => $fy_id,
                    'crs_mst_is_primary' => $crs_mst_is_primary,
                    'crs_is_active'      => 1,
                ]);
            }

            // ── 6. Insert opening balance (zero) ──
            $existingOpBal = $this->db->table('accoppybal')
                ->select('acc_op_bal_id')
                ->where('cmp_id', $company_id)
                ->where('acc_id', $acc_id)
                ->where('cmpfymastr_id', $fy_id)
                ->where('hobo_id', $hobo_id)
                ->get()
                ->getRowArray();

            if (!$existingOpBal) {
                $this->db->table('accoppybal')->insert([
                    'cmp_id'        => $company_id,
                    'cmpfymastr_id' => $fy_id,
                    'acc_id'        => $acc_id,
                    'acc_op_bal'    => 0,
                    'acc_py_bal'    => 0,
                    'hobo_id'       => $hobo_id,
                    'acc_memo_bal'  => 0,
                ]);
            }

            // ── 7. Insert opening balance transaction entry ──
            $existingTxn = $this->db->table('accttxnmst')
                ->select('acc_txn_id')
                ->where('cmp_id', $company_id)
                ->where('acc_id', $acc_id)
                ->where('acc_txn_type', 1)
                ->where('vch_txn_id IS NULL')
                ->where('hobo_id', $hobo_id)
                ->get()
                ->getRowArray();

            if (!$existingTxn) {
                $this->db->table('accttxnmst')->insert([
                    'cmp_id'        => $company_id,
                    'acc_id'        => $acc_id,
                    'acc_txn_date'  => $fy_begndt,
                    'acc_txn_dr_cr' => 1,
                    'acc_txn_amt'   => 0,
                    'vch_txn_id'    => null,
                    'hobo_id'       => $hobo_id,
                    'acc_txn_type'  => 1,
                ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', "Failed to create default accounts for company ID: {$company_id}");
            return false;
        }

        return true;

    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', "Exception creating default accounts: " . $e->getMessage());
        return false;
    }
}

/**
 * Creates all 16 default item units when a new company is created.
 * Inserts into `itmunitmst` table only.
 * Call this from add_comp() AFTER create_default_groups() and create_default_accounts().
 *
 * @param int $company_id  The newly created company ID
 * @return bool
 */
private function create_default_units(int $company_id): bool
{
    $defaultUnits = [
        ['name' => 'CM',     'alias' => 'CM',     'print' => 'CM',     'uqc' => 'CMT'],
        ['name' => 'Dozen',  'alias' => 'Dozen',  'print' => 'Dozen',  'uqc' => 'DOZ'],
        ['name' => 'Ft',     'alias' => 'Ft',     'print' => 'Ft',     'uqc' => 'FOT'],
        ['name' => 'Gms',    'alias' => 'Gms',    'print' => 'Gms',    'uqc' => 'GMS'],
        ['name' => 'Inch',   'alias' => 'Inch',   'print' => 'Inch',   'uqc' => 'INH'],
        ['name' => 'Kgs',    'alias' => 'Kgs',    'print' => 'Kgs',    'uqc' => 'KGS'],
        ['name' => 'Metre',  'alias' => 'Metre',  'print' => 'Metre',  'uqc' => 'MTR'],
        ['name' => 'MM',     'alias' => 'MM',     'print' => 'MM',     'uqc' => 'MMT'],
        ['name' => 'NA',     'alias' => 'NA',     'print' => 'NA',     'uqc' => 'OTH'],
        ['name' => 'Packs',  'alias' => 'Packs',  'print' => 'Packs',  'uqc' => 'PAC'],
        ['name' => 'Pair',   'alias' => 'Pair',   'print' => 'Pair',   'uqc' => 'PRS'],
        ['name' => 'Pcs',    'alias' => 'Pcs',    'print' => 'Pcs',    'uqc' => 'PCS'],
        ['name' => 'Qtl',    'alias' => 'Qtl',    'print' => 'Qtl',    'uqc' => 'QTL'],
        ['name' => 'Set',    'alias' => 'Set',    'print' => 'Set',    'uqc' => 'SET'],
        ['name' => 'Tonne',  'alias' => 'Tonne',  'print' => 'Tonne',  'uqc' => 'TON'],
        ['name' => 'Units',  'alias' => 'Units',  'print' => 'Units',  'uqc' => 'UNT'],
    ];

    $this->db->transStart();

    try {
        foreach ($defaultUnits as $unit) {

                $this->db->table('itmunitmst')->insert([
                    'cmp_id'             => $company_id,
                    'itm_unit_name'      => $unit['name'],
                    'itm_unit_alias'     => $unit['alias'],
                    'itm_unit_print'     => $unit['print'],
                    'itm_unit_uqc'       => $unit['uqc'],
                    'itm_unit_is_active' => 1,
                ]);
           
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', "Failed to create default units for company ID: {$company_id}");
            return false;
        }

        return true;

    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', "Exception creating default units: " . $e->getMessage());
        return false;
    }
}

/**
 * Creates default item groups when a new company is created.
 * Inserts into `itemgrpmst` and `undercrsmt` tables.
 * Call this from add_comp() AFTER create_default_groups().
 *
 * @param int $company_id  The newly created company ID
 * @param int $fy_id       The financial year ID (cmpfymastr_id)
 * @return bool
 */
private function create_default_item_groups(int $company_id, int $fy_id): bool
{
    // Default Item Groups
    //
    // undercrsmt.crs_mst_type = 6 (Item Groups)
    //
    // crs_mst_parent_id for item groups:
    //   0 = Root level (no parent)
    // ─────────────────────────────────────────────────────────

    $defaultItemGroups = [
        [
            'name'              => 'Main',
            'alias'             => 'Main',
            'is_primary'        => 1,
            'crs_mst_parent_id' => 0,
            'under_crs_mst_id'  => 0,
            'under_main_id'     => 0,
        ],
    ];

    $this->db->transStart();

    try {
        foreach ($defaultItemGroups as $group) {

            
                // ── 2. Insert into itemgrpmst ──
                $this->db->table('itemgrpmst')->insert([
                    'cmp_id'           => $company_id,
                    'itm_grp_name'     => $group['name'],
                    'itm_grp_alias'    => $group['alias'],
                    'itm_grp_is_active'=> 1,
                ]);
                $grpId = (int)$this->db->insertID();
          
            
                $this->db->table('undercrsmt')->insert([
                    'cmp_id'             => $company_id,
                    'crs_mst_type'       => 6,                       // Item Groups
                    'crs_mst_id'         => $grpId,
                    'under_crs_mst_id'   => $group['under_crs_mst_id'],
                    'crs_mst_parent_id'  => $group['crs_mst_parent_id'],
                    'under_main_id'      => $group['under_main_id'],
                    'crs_mst_is_primary' => $group['is_primary'],
                    'cmpfymastr_id'      => $fy_id,
                    'crs_is_active'      => 1,
                ]);
           
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', "Failed to create default item groups for company ID: {$company_id}");
            return false;
        }

        return true;

    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', "Exception creating default item groups: " . $e->getMessage());
        return false;
    }
}


/**
 * Creates default "Main" voucher series for all 23 voucher types.
 * Inserts into `vchseriesn` and `vchseriesm` tables.
 * Call this from add_comp() as the final setup step.
 *
 * @param int $company_id  The newly created company ID
 * @return bool
 */
private function create_default_voucher_series(int $company_id): bool
{
    // ─────────────────────────────────────────────────────────
    // All 23 voucher types
    // Each gets a "Main" series with method=0, bank_id=0
    // Plus a manual numbering entry with blank_no=0
    // ─────────────────────────────────────────────────────────

    $voucherTypes = [
        1  => 'Contra',
        2  => 'Credit Note',
        3  => 'Debit Note',
        4  => 'Consignment Packing',
        5  => 'Journal',
        6  => 'Inward Challan',
        7  => 'Delivery Challan',
        8  => 'Memorandum',
        9  => 'Payment',
        10 => 'Physical Stock',
        11 => 'Purchase',
        12 => 'Purchase Order',
        13 => 'Receipt',
        14 => 'Production',
        15 => 'Stock Transfer',
        16 => 'Reverse Journal',
        17 => 'Quotation',
        18 => 'Sales',
        19 => 'Sales Order',
        20 => 'Stock Journal',
        21 => 'Purchase Requisition',
        22 => 'System Generated',
        23 => 'System Journal',
    ];

    $this->db->transStart();

    try {
        foreach ($voucherTypes as $typeId => $typeName) {

           
                // ── 2. Insert into vchseriesn ──
                $this->db->table('vchseriesn')->insert([
                    'cmp_id'           => $company_id,
                    'vch_series_name'  => 'Main',
                    'vch_type_id'      => $typeId,
                    'vch_series_method'=> 0,
                    'bank_id'          => 0,
                ]);
                $seriesId = (int)$this->db->insertID();
           
            
                $this->db->table('vchseriesm')->insert([
                    'cmp_id'           => $company_id,
                    'vch_series_id'    => $seriesId,
                    'vch_series_blank' => 0,
                ]);
           
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', "Failed to create default voucher series for company ID: {$company_id}");
            return false;
        }

        return true;

    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', "Exception creating default voucher series: " . $e->getMessage());
        return false;
    }
}

/**
 * Creates default currency (Indian Rupee) when a new company is created.
 * Inserts into `cmpfcymstn` table.
 * Call this from add_comp().
 *
 * @param int $company_id  The newly created company ID
 * @return bool
 */
private function create_default_currency(int $company_id): bool
{
    try {
       
            $this->db->table('cmpfcymstn')->insert([
                'cmp_id'             => $company_id,
                'cmp_fcy_name'       => 'Rupee',
                'cmp_fcy_symbol'     => '₹',
                'cmp_fcy_string'     => '',
                'cmp_fcy_sub_string' => '',
                'cmp_fcy_initial'    => 'INR',
                'cmp_fcy_forex_type' => 0,
            ]);
      
        return true;

    } catch (\Exception $e) {
        log_message('error', "Exception creating default currency for company ID: {$company_id} - " . $e->getMessage());
        return false;
    }
}

 }