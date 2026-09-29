<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables;
use App\Libraries\ERPtables;
use App\Models\Admin\TransactionModel;

class ImportExportModel extends Model	{

   public function __construct() {
		parent::__construct();    
		$this->TransactionModel  = new TransactionModel();	    
		$this->externaldb    = new externaldb();	
		$this->db            = $this->externaldb->get_company_db();
		$this->session       = \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
		$this->enc_string    = new enc_string();
		$this->SupplyTypes   =  SupplyTypesList();

		$this->bo_id = $this->session->get('ses_boid');
		$this->aicountly_db = $this->externaldb->aicountly_db();

		$this->item_goods_rate = 1;
		$this->item_services_rate = 6;
    }

  function GetTaxMastersInfo($voucher_type){
	  $billsundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	  $bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
	  $builder = $this->db->table($billsundry_master_tbl);
	  $builder->join($bdstaxmstn_tbl,$bdstaxmstn_tbl.'.bill_sundry_id='.$billsundry_master_tbl.'.bill_sundry_id');
	  if($voucher_type=='sale'){
		$builder->whereIn($bdstaxmstn_tbl.'.bill_input_output', [1,2]);  
	  }
	  if($voucher_type=='purchase'){
		$builder->whereIn($bdstaxmstn_tbl.'.bill_input_output', [0,2]);    
	  } 
	  $builder->whereIn($billsundry_master_tbl.'.sundry_nature', [33,34,35,36]);
	  $response = $builder->get()->getResultArray();
	  $igst_master_value='';
	  $cgst_master_value='';
	  $sgst_master_value='';
	  $cess_master_value='';
	  if($response){
		  foreach($response as $row){
			  if($row['sundry_nature']==33)
				  $cgst_master_value = trim($row['bill_sundry_name']);
			  if($row['sundry_nature']==34)
				  $sgst_master_value = trim($row['bill_sundry_name']);
			  if($row['sundry_nature']==35)
				  $igst_master_value = trim($row['bill_sundry_name']);
			  if($row['sundry_nature']==36)
				  $cess_master_value = trim($row['bill_sundry_name']);
		  }
	  }
	  $final= array('igst_master_value'=>$igst_master_value,'cgst_master_value'=>$cgst_master_value,
	                'sgst_master_value'=>$sgst_master_value,'cess_master_value'=>$cess_master_value
					);
	  return $final;
  }

  function create_mst_base_id($id,$type)
  {
  		$comp_uuid  = $this->session->get('comp_uuid');
		$UUIDtables = new UUIDtables($comp_uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
		$mst_base_id = $UUIDtables->get_mst_base_id($id,$type);
		return $mst_base_id;
  }

  function delete_comp_fy_mst_map($id,$type)
  {
  		$comp_uuid  = $this->session->get('comp_uuid');
		$UUIDtables = new UUIDtables($comp_uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
		$UUIDtables->delete_comp_fy_mst_map_by_id($id,$type);
  }

	function get_exp_type_details($id)
	{
		$array = [
			'1' => [
				'module' => 'Accounts', 
				'master' => 'Account Master', 
				'sub_master' => 'Account Master'],
			'2' => [
				'module' => 'Accounts', 
				'master' => 'Account Master', 
				'sub_master' => 'Account Group Master'],
			'3' => [
				'module' => 'Inventory', 
				'master' => 'Stock Master', 
				'sub_master' => 'Item Master'],
			'4' => [
				'module' => 'Inventory', 
				'master' => 'Stock Master', 
				'sub_master' => 'Item Group Master'],
			'5' => [
				'module' => 'Inventory', 
				'master' => 'Stock Master', 
				'sub_master' => 'Item Category'],
			'6' => [
				'module' => 'Transaction', 
				'master' => 'Others', 
				'sub_master' => 'Day Book'],
			'7' => [
				'module' => 'Transaction', 
				'master' => 'Bank Statement', 
				'sub_master' => 'Other Bank Statement'],
			'8' => [
				'module' => 'Transaction', 
				'master' => 'Sales', 
				'sub_master' => 'Sales w/out Item'],
			'9' => [
				'module' => 'Transaction', 
				'master' => 'Sales', 
				'sub_master' => 'Sales with Item'],
			'10' => [
				'module' => 'Transaction', 
				'master' => 'Purchase', 
				'sub_master' => 'Purchase w/out Item'],
			'11' => [
				'module' => 'Transaction', 
				'master' => 'Purchase', 
				'sub_master' => 'Purchase with Item'],	
			'12' => [
				'module' => 'GST', 
				'master' => 'Outward Supplies', 
				'sub_master' => 'GSTR-1'],		
		      ];

		if(isset($array[$id])){
			return $array[$id];
		}

		return ['module' => '', 'master' => '', 'sub_master' => ''];
	}

	function get_exp_type_id($data)
	{
		$array = [
			'1' => [
				'module' => 'Accounts', 
				'master' => 'Account Master', 
				'sub_master' => 'Account Master'],
			'2' => [
				'module' => 'Accounts', 
				'master' => 'Account Master', 
				'sub_master' => 'Account Group Master'],
			'3' => [
				'module' => 'Inventory', 
				'master' => 'Stock Master', 
				'sub_master' => 'Item Master'],
			'4' => [
				'module' => 'Inventory', 
				'master' => 'Stock Master', 
				'sub_master' => 'Item Group Master'],
			'5' => [
				'module' => 'Inventory', 
				'master' => 'Stock Master', 
				'sub_master' => 'Item Category'],
			'6' => [
				'module' => 'Transaction', 
				'master' => 'Others', 
				'sub_master' => 'Day Book'],
			'7' => [
				'module' => 'Transaction', 
				'master' => 'Bank Statement', 
				'sub_master' => 'Other Bank Statement'],
			'8' => [
				'module' => 'Transaction', 
				'master' => 'Sales', 
				'sub_master' => 'Sales w/out Item'],
			'9' => [
				'module' => 'Transaction', 
				'master' => 'Sales', 
				'sub_master' => 'Sales with Item'],
			'10' => [
				'module' => 'Transaction', 
				'master' => 'Purchase', 
				'sub_master' => 'Purchase w/out Item'],
			'11' => [
				'module' => 'Transaction', 
				'master' => 'Purchase', 
				'sub_master' => 'Purchase with Item'],
            '12' => [
				'module' => 'GST', 
				'master' => 'Outward Supplies', 
				'sub_master' => 'GSTR-1'],	
		];

		foreach ($array as $key => $value) {
			if($value['module'] == $data['module'] &&
					$value['master'] == $data['master'] &&
						$value['sub_master'] == $data['sub_master'])
			{
				return $key;
			}
		}

		return 0;
	}

	function insertExportMaster($post)
	{
		$impexp_type   = $this->get_exp_type_id($post);
        $impexp_format = $post['sub_master_format'];
		$insert = [
			'comp_id'			=> $this->company_id,
			'impexp_type' 		=> $impexp_type,
			'impexp_status' 	=> 0,
			'impexp_log'		=> date('Y-m-d H:i:s'),
			'impexp_format'		=> $impexp_format
		];

		$this->aicountly_db->table('aicountly_impexpmstn_univdb')
											->insert($insert);

		return $this->aicountly_db->insertID();
	}

	function updateExportMaster($data)
	{
		$this->aicountly_db->table('aicountly_impexpmstn_univdb')
								->where('impexp_sr_id',$data['impexp_sr_id'])
								->update($data);
	}

	function deleteExportMaster($impexp_sr_id)
	{
		$this->aicountly_db->table('aicountly_impexpmstn_univdb')
								->where('impexp_sr_id',$impexp_sr_id)
								->delete();
	}
	
	function GetimpexpmstnInfo($impexp_sr_id,$impexp_type){
		return $this->aicountly_db->table('aicountly_impexpmstn_univdb')
											->where('comp_id',$this->company_id)
											->where('impexp_sr_id', $impexp_sr_id)
											->where('impexp_type', $impexp_type)
											->get()->getRowArray();
	  }
	

	function get_export_master_list($type)
	{
		
		if($type=='exp'){
		$result = $this->aicountly_db->table('aicountly_impexpmstn_univdb')
											->where('comp_id',$this->company_id)
											->whereIn('impexp_type',[12])
											->orderBy('impexp_sr_id', 'desc')
											->get()->getResultArray();	
		}
		else{
		$result = $this->aicountly_db->table('aicountly_impexpmstn_univdb')
											->where('comp_id',$this->company_id)
											->whereNotIn('impexp_type',[12])
											->orderBy('impexp_sr_id', 'desc')
											->get()->getResultArray();
		}

		
		foreach ($result as $key => $value) {

			$type_details = $this->get_exp_type_details($value['impexp_type']);
			
			$result[$key]['module'] = $type_details['module'];
			$result[$key]['master'] = $type_details['master'];
			
			if($value['impexp_format']!='')
			$result[$key]['sub_master'] = $type_details['sub_master'].'('.$value['impexp_format'].')';
		    else 
			 $result[$key]['sub_master'] = $type_details['sub_master'];	
			
		

			$result[$key]['datetime'] = date('d-m-Y H:i:s', strtotime($value['impexp_log']));

			$status = '';
			if($value['impexp_status'] == 0)
				$status = 'INITIATE';
			if($value['impexp_status'] == 1)
				$status = 'SUCCESSFUL';
			if($value['impexp_status'] == 2)
				$status = 'PROCESS';

			$result[$key]['status'] = $status;
		}

		return $result;
	}

	function get_export_master_details($impexp_sr_id)
	{
		$result = $this->aicountly_db->table('aicountly_impexpmstn_univdb')
											->where('comp_id',$this->company_id)
											->where('impexp_sr_id', $impexp_sr_id)
											->get()->getRowArray();

		if($result){

			$total_records = 0;
			$imported_records = 0;			
			if($result['impexp_type'] == 1){
				$table = 'aicountly_impaccmstn_univdb';
			}
			if($result['impexp_type'] == 2){
				$table = 'aicountly_impaccgrpn_univdb';
			}
			if($result['impexp_type'] == 3){
				$table = 'aicountly_impitmmstn_univdb';
			}

			if($result['impexp_type'] == 4){
				$table = 'aicountly_impitmgrpn_univdb';
			}

			if($result['impexp_type'] == 5){
				$table = 'aicountly_impitmcatn_univdb';
			}

			if(in_array($result['impexp_type'], [6,7,8,9,10,11])){
				$table = 'aicountly_imptxnvchn_univdb';
				
			}
			

			if(!empty($table)){

				$total_records = $this->aicountly_db->table($table)
												->select('count(*) as total')
												->where('impexp_sr_id',$impexp_sr_id)
												->countAllResults();

				$imported_records=$this->aicountly_db->table($table)
												->select('count(*) as total')
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_status',1)
												->countAllResults();
			}

			$result['total_records'] = $total_records;
			$result['imported_records'] = $imported_records;
		}

		return $result;
	}

	function get_ttl_records($impexp_sr_id,$impexp_type)
	{
		if($impexp_type == 1){
			$table = 'aicountly_impaccmstn_univdb';
		}
		if($impexp_type == 2){
			$table = 'aicountly_impaccgrpn_univdb';
		}
		if($impexp_type == 3)
			$table = 'aicountly_impitmmstn_univdb';

		if($impexp_type == 4)
			$table = 'aicountly_impitmgrpn_univdb';

		if($impexp_type == 5)
			$table = 'aicountly_impitmcatn_univdb';

		if(in_array($impexp_type, [6,7,8,9,10,11]))
			$table = 'aicountly_imptxnvchn_univdb'; 

		$total_records = 0;

		if(!empty($table)){

			$total_records = $this->aicountly_db->table($table)
												->select('count(*) as total')
												->where('impexp_sr_id',$impexp_sr_id)
												->countAllResults();
		}

		return $total_records;								
	}

	function insertExcelMaster($impexp_type,$data)
	{
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

		if(in_array($impexp_type, [6,7,8,9,10,11]))
			$table = 'aicountly_imptxnvchn_univdb';

		$this->aicountly_db->table($table)->insert($data);

		return $this->aicountly_db->insertID();
	}

	function insertExcelSubMaster($impexp_type,$data)
	{
		if(in_array($impexp_type, [6,7,8,9,10,11]))
			$sub_table = 'aicountly_imptxnvchd_univdb';

		$this->aicountly_db->table($sub_table)->insert($data);
	}

	function insertExcelSubMaster2($impexp_type,$data)
	{
		if(in_array($impexp_type, [9,11]))
			$sub_table = 'aicountly_imptxnvchi_univdb';

		$this->aicountly_db->table($sub_table)->insert($data);
	}

	function insertExcelSubSideMaster($impexp_type,$data)
	{
		if(in_array($impexp_type, [8,9]))
			$sub_side_table = 'aicountly_impoutsupd_univdb';

		$this->aicountly_db->table($sub_side_table)
											->insert($data);
	}
	function insertExcelSubSidePurchaseMaster($impexp_type,$data)
	{
		if(in_array($impexp_type, [10,11]))
			$sub_side_table = 'aicountly_impinwsupd_univdb';

		$this->aicountly_db->table($sub_side_table)
											->insert($data);
	}

	// not used
	function insertExcelMaster2($impexp_type,$impexp_sr_id,$file_path)
	{
		if($impexp_type == 2)
			$table = 'aicountly_impaccgrpn_univdb';

		ini_set('memory_limit','512M');
	  ini_set('max_execution_time',30000);
	  $enclosed = '"';
	  $brn      = '\r\n';

		$sql ='LOAD DATA INFILE \''.$file_path.'\' REPLACE INTO TABLE '.$table.' CHARACTER SET  utf8mb4
		    FIELDS TERMINATED BY ","
		    OPTIONALLY ENCLOSED BY \'"\'
		    LINES TERMINATED BY \''.$brn.'\'
		    IGNORE 1 LINES (impaccgrp_name, impaccgrp_alias, impaccgrp_under_grp)
			SET impexp_sr_id = '.$impexp_sr_id.',imp_status = 0;';


		$this->aicountly_db->query($sql);
	}

	function get_imp_mst_list($impexp_sr_id,$impexp_type,$limit,$offset)
	{
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
		
		if(in_array($impexp_type, [10])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}

		if(in_array($impexp_type, [11])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_table2 = 'aicountly_imptxnvchi_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}


		$total = 0;
		$uploaded = 0;
		$data = [];

	
		if($offset == 0)
		{
			$total = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->countAllResults();

			$uploaded = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_status', 1)
												->countAllResults();
		}

		if(in_array($impexp_type, [1,2,3,4,5])){
			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
			foreach ($data as $key => $value) {
				$sno++;
				$data[$key]['sno'] = $sno;
			}
		}

		if(in_array($impexp_type, [6,7])){

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($temp2 as $key2 => $value2) {

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row						
						$data[] = array_merge($value,$value2);
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$data[] = $value2;
					}
				}

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}

		if(in_array($impexp_type, [8])){ // sale without item

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				
				$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';
				$SupplyTypesData  = $this->SupplyTypes;
				$StatesData  = $this->TransactionModel->show_states_lists(1);
				
				if(isset($StatesData[$temp2['impoutsup_pos']]))
				 $impout_pos_type = $StatesData[$temp2['impoutsup_pos']];
			  else 
				  $impout_pos_type ='';
				$value['imptxnvch_pos'] = $impout_pos_type;
				
				$value['imptxnvch_posid'] = $temp2['impoutsup_pos'] ?? '';
				
				if(isset($SupplyTypesData[$temp2['impout_supply_type']]))
				 $impout_supply_type = $SupplyTypesData[$temp2['impout_supply_type']];
			  else 
				  $impout_supply_type ='';
				$value['imptxnvch_supplytype'] = $impout_supply_type;
				$value['imptxnvch_supplytype_id'] = $temp2['impout_supply_type'] ?? '';

				$temp2 = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($temp2 as $key2 => $value2) {

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row						
						$data[] = array_merge($value,$value2);
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$data[] = $value2;
					}
				}

				$final = [];
				$final['imptxnvch_acc_bds'] = html_entity_decode($narration);
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}

		if(in_array($impexp_type, [9])){ // sale with item

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				
				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);
				
				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				$value['imptxnvch_posid'] = $temp2['impoutsup_pos'] ?? '';
				$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';
				$SupplyTypesData = $this->SupplyTypes;
				
				$StatesData  = $this->TransactionModel->show_states_lists(1);
				
				if(isset($StatesData[$temp2['impoutsup_pos']]))
				 $impout_pos_type = $StatesData[$temp2['impoutsup_pos']];
			   else 
				  $impout_pos_type ='';
				$value['imptxnvch_pos'] = $impout_pos_type;
				
				if(isset($SupplyTypesData[$temp2['impout_supply_type']]))
				 $impout_supply_type = $SupplyTypesData[$temp2['impout_supply_type']];
			  else 
				  $impout_supply_type ='';
				$value['imptxnvch_supplytype'] = $impout_supply_type;
				$value['imptxnvch_supplytype_id'] = $temp2['impout_supply_type'] ?? '';
				
				$result = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($result as $key2 => $value2) {

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row	
				
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						
						$data[] = array_merge($value,$value2);

						$result3 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$value['imp_id'])
												->get()->getResultArray();

						// Items 
						foreach ($result3 as $key3 => $value3) {

							$value3['imptxnvch_acc_bds'] = html_entity_decode($value3['imptxnvch_item']);
							$value3['imptxnvch_amt_dr'] = floatval($value3['imptxnvch_amt_dr']);
							$value3['imptxnvch_amt_cr'] = floatval($value3['imptxnvch_amt_cr']);

							$value3['imptxnvch_uqc'] = $value3['imptxnvch_uqc_cr'];
							$value3['imptxnvch_qty'] = floatval($value3['imptxnvch_qty_cr']);
							$value3['imptxnvch_mc'] = '';

							$value3['imp_id'] = $imp_id;
							$value3['imp_status'] = $imp_status;
							$value3['imptxnvch_supplytype_id'] = $temp2['impout_supply_type'] ?? '';
							$data[] = $value3;
							
						}
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						$value2['imptxnvch_mc'] = '';
						$value2['imptxnvch_supplytype_id'] = $temp2['impout_supply_type'] ?? '';
						$data[] = $value2;
					}
				}

						

				$final = [];
				$final['imptxnvch_acc_bds'] = html_entity_decode($narration);
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}
		
		if(in_array($impexp_type, [10])){

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				
				$StatesData  = $this->TransactionModel->show_states_lists(1);
				
				if(isset($StatesData[$temp2['impinwsup_pos']]))
				 $impout_pos_type = $StatesData[$temp2['impinwsup_pos']];
			  else 
				  $impout_pos_type ='';
				$value['imptxnvch_pos'] = $impout_pos_type;
				
				$value['imptxnvch_posid'] = $temp2['impinwsup_pos'] ?? '';
				
				$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
				$SupplyTypesData = $this->SupplyTypes;
				if(isset($SupplyTypesData[$temp2['impinw_supply_type']]))
				 $impout_supply_type = $SupplyTypesData[$temp2['impinw_supply_type']];
			  else 
				  $impout_supply_type ='';
				$value['imptxnvch_supplytype'] = $impout_supply_type;
				$value['imptxnvch_supplytype_id'] = $temp2['impinw_supply_type'];
			

				$temp2 = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($temp2 as $key2 => $value2) {

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row						
						$data[] = array_merge($value,$value2);
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$data[] = $value2;
					}
				}

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}
		
		if(in_array($impexp_type, [11])){

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				
				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				
				$StatesData  = $this->TransactionModel->show_states_lists(1);
				
				if(isset($StatesData[$temp2['impinwsup_pos']]))
				 $impout_pos_type = $StatesData[$temp2['impinwsup_pos']];
			  else 
				  $impout_pos_type ='';
				$value['imptxnvch_pos'] = $impout_pos_type;
				
				$value['imptxnvch_posid'] = $temp2['impinwsup_pos'] ?? '';
				$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
				
				$SupplyTypesData = $this->SupplyTypes;
				if(isset($SupplyTypesData[$temp2['impinw_supply_type']]))
				 $impout_supply_type = $SupplyTypesData[$temp2['impinw_supply_type']];
			  else 
				  $impout_supply_type ='';
				$value['imptxnvch_supplytype'] = $impout_supply_type;
				$value['imptxnvch_supplytype_id'] = $temp2['impinw_supply_type'];
				$result = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($result as $key2 => $value2) {

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row	
				
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						
						$data[] = array_merge($value,$value2);

						$result3 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$value['imp_id'])
												->get()->getResultArray();

						// Items 
						foreach ($result3 as $key3 => $value3) {

							$value3['imptxnvch_acc_bds'] = $value3['imptxnvch_item'];
							$value3['imptxnvch_amt_dr'] = floatval($value3['imptxnvch_amt_dr']);
							$value3['imptxnvch_amt_cr'] = floatval($value3['imptxnvch_amt_cr']);

							$value3['imptxnvch_uqc'] = $value3['imptxnvch_uqc_cr'];
							$value3['imptxnvch_qty'] = floatval($value3['imptxnvch_qty_cr']);
							$value3['imptxnvch_mc'] = '';

							$value3['imp_id'] = $imp_id;
							$value3['imp_status'] = $imp_status;
							$data[] = $value3;
							
						}
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						$value2['imptxnvch_mc'] = '';
						$data[] = $value2;
					}
				}

						

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}
		

		$columns = [];
		if($impexp_type == 1){ // Account Master
			$columns = ['sno' => 'S.No.', 'impacc_name' => 'Name', 'impacc_alias' => 'Alias','impacc_print' => 'Print','impacc_primary' => 'Primary', 'impacc_prt' => 'Parent', 'impacc_grp' => 'Under Group'];
		}
		if($impexp_type == 2){ // Account Group Master
			$columns = ['sno' => 'S.No.', 'impaccgrp_name' => 'Group Name', 'impaccgrp_alias' => 'Group Alias', 'impaccgrp_under_prt' => 'Parent', 'impaccgrp_under_grp' => 'Under Group'];
		}
		if($impexp_type == 3){ // Item Master
			$columns = ['sno' => 'S.No.', 'impitm_name' => 'Name', 'impitm_alias' => 'Alias','impitm_print' => 'Print', 'impitm_grp' => 'Group', 'impitm_cat' => 'Category','impitm_upc' => 'UPC','impitm_sale_acc' => 'Sales Acc.','impitm_pur_acc' => 'Purchase Acc.', 'impitm_unit' => 'Unit', 'impitm_val_method' => 'Val. Method', 'impitm_mrp' => 'MRP'];
		}
		if($impexp_type == 4){ // Item Group Master
			$columns = ['sno' => 'S.No.', 'impitmgrp_name' => 'Group Name', 'impitmgrp_alias' => 'Group Alias', 'impitmgrp_under_grp' => 'Under Group'];
		}
		if($impexp_type == 5){ // Item Category Master
			$columns = ['sno' => 'S.No.', 'impitmcat_name' => 'Category Name', 'impitmcat_alias' => 'Category Alias'];
		}

		if($impexp_type == 6){ // Day Book
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_type' => 'Type','imptxnvch_series' => 'Series', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_acc_bds' => 'Account', 'imptxnvch_amt_dr' => 'Debit', 'imptxnvch_amt_cr' => 'Credit', 'imptxnvch_short_narr' => 'Short Narration'];
		}
		if($impexp_type == 7){ // Bank Statement
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_type' => 'Type','imptxnvch_series' => 'Series', 'imptxnvch_acc_bds' => 'Account', 'imptxnvch_amt_dr' => 'Debit', 'imptxnvch_amt_cr' => 'Credit'];
		}
		if($impexp_type == 8){ // Sale Without Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_type' => 'Type','imptxnvch_supplytype' => 'Supply Type','imptxnvch_series' => 'Series', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_acc_bds' => 'Account', 'imptxnvch_amt_dr' => 'Debit', 'imptxnvch_amt_cr' => 'Credit', 'imptxnvch_short_narr' => 'Short Narr.', 'imptxnvch_pos' => 'POS', 'imptxnvch_country_code' => 'Country Code'];
		}
		if($impexp_type == 9){ // Sale Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_type' => 'Type','imptxnvch_supplytype' => 'Supply Type','imptxnvch_series' => 'Series', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_uqc' => 'UQC', 'imptxnvch_acc_bds' => 'Account', 'imptxnvch_amt_dr' => 'Debit', 'imptxnvch_amt_cr' => 'Credit', 'imptxnvch_qty' => 'Qty', 'imptxnvch_short_narr' => 'Short Narr.', 'imptxnvch_mc' => 'MC', 'imptxnvch_pos' => 'POS', 'imptxnvch_country_code' => 'Country Code'];
		}
		if($impexp_type == 10){ // Purchase Without Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_type' => 'Type','imptxnvch_supplytype' => 'Supply Type','imptxnvch_series' => 'Series', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_acc_bds' => 'Account', 'imptxnvch_amt_dr' => 'Debit', 'imptxnvch_amt_cr' => 'Credit', 'imptxnvch_short_narr' => 'Short Narr.', 'imptxnvch_pos' => 'POS', 'imptxnvch_country_code' => 'Country Code'];
		}
		if($impexp_type == 11){ // Purchase With Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_type' => 'Type','imptxnvch_supplytype' => 'Supply Type','imptxnvch_series' => 'Series', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_uqc' => 'UQC', 'imptxnvch_acc_bds' => 'Account', 'imptxnvch_amt_dr' => 'Debit', 'imptxnvch_amt_cr' => 'Credit', 'imptxnvch_qty' => 'Qty', 'imptxnvch_short_narr' => 'Short Narr.', 'imptxnvch_mc' => 'MC', 'imptxnvch_pos' => 'POS', 'imptxnvch_country_code' => 'Country Code'];
		}	

		return ['columns' => $columns, 'total' => $total,'uploaded' => $uploaded, 'data' => $data];
	}
	
	function get_imp_mst_list_suggestion($impexp_sr_id,$impexp_type,$limit,$offset)
	{
		if($impexp_type == 1) // Account Master
			$table = 'aicountly_impaccmstn_univdb';

		if($impexp_type == 2)// Account Group Master
			$table = 'aicountly_impaccgrpn_univdb';

		if($impexp_type == 3)// Item  Master
			$table = 'aicountly_impitmmstn_univdb';

		if($impexp_type == 4)// Item Group Master
			$table = 'aicountly_impitmgrpn_univdb';

		if($impexp_type == 5) // Item Category Master
			$table = 'aicountly_impitmcatn_univdb';

		if(in_array($impexp_type, [6,7])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
		}

		if(in_array($impexp_type, [8])){ // Sale Without Item
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_side_table = 'aicountly_impoutsupd_univdb';
		}

		if(in_array($impexp_type, [9])){// Sale With Item
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_table2 = 'aicountly_imptxnvchi_univdb';
			$sub_side_table = 'aicountly_impoutsupd_univdb';
		}
		
		if(in_array($impexp_type, [10])){ // Purchase Without Item
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}

		if(in_array($impexp_type, [11])){// Purchase With Item
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_table2 = 'aicountly_imptxnvchi_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}

		$total = 0;
		$uploaded = 0;
		$data = [];

	
		if($offset == 0)
		{
			$total = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->countAllResults();

			$uploaded = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_status', 1)
												->countAllResults();
		}

		if(in_array($impexp_type, [1,2,3,4,5])){
			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
			foreach ($data as $key => $value) {
				$sno++;
				 
				if($impexp_type == 1){
					 $impacc_prt = strtolower($value['impacc_prt']);
					 $impacc_grp = strtolower($value['impacc_grp']);
					// find other relevant accounts from db according to excel master name
					$relevant_parent_list = $this->TransactionModel->relevant_parent_dropdown($impacc_prt);
				 	$relevant_undergroup_list = $this->TransactionModel->relevant_undergroup_dropdown($impacc_grp);
				 	 
					 if(count($relevant_parent_list)==0){
					  $data[$key]['tooltip_exists'] = 1;
					  $data[$key]["pq_cellattr"]["impacc_prt_sug"] =array("title"=>"Use suggested name to pick parent.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impacc_prt_sug"] =array("title"=>"Use suggested name to pick parent.");
					} 
					
					if(count($relevant_undergroup_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["impacc_grp_sug"] =array("title"=>"Use suggested name to pick under group.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impacc_grp_sug"] =array("title"=>"Use suggested name to pick under group.");
					} 
					
					$data[$key]['impacc_prt_sug_list'] = $relevant_parent_list;
					$data[$key]['impacc_grp_sug_list'] = $relevant_undergroup_list;
				   }
				if($impexp_type == 2){
					 $impaccgrp_under_prt = strtolower($value['impaccgrp_under_prt']);
					 $impaccgrp_under_prt_sug = strtolower($value['impaccgrp_under_grp']);					
					 $relevant_parent_list = $this->TransactionModel->relevant_parent_dropdown($impaccgrp_under_prt);
				 	 $relevant_undergroup_list = $this->TransactionModel->relevant_undergroup_dropdown($impaccgrp_under_prt_sug);
				 	 
					 if(count($relevant_parent_list)==0){
					  $data[$key]['tooltip_exists'] = 1;
					  $data[$key]["pq_cellattr"]["impaccgrp_under_prt_sug"] =array("title"=>"Use suggested name to pick parent.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impaccgrp_under_prt_sug"] =array("title"=>"Use suggested name to pick parent.");
					} 
					
					if(count($relevant_undergroup_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["impaccgrp_under_grp_sug"] =array("title"=>"Use suggested name to pick under group.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impaccgrp_under_grp_sug"] =array("title"=>"Use suggested name to pick under group.");
					} 
					
					$data[$key]['impaccgrp_under_prt_sug_list'] = $relevant_parent_list;
					$data[$key]['impaccgrp_under_grp_sug_list'] = $relevant_undergroup_list;
				   }
				   if($impexp_type == 3){
					 $impitm_cat      = strtolower($value['impitm_cat']);
					 $impitm_sale_acc = strtolower($value['impitm_sale_acc']);
					 $impitm_pur_acc  = strtolower($value['impitm_pur_acc']);						
					 $relevant_catg_list = $this->TransactionModel->relevant_catg_dropdown($impitm_cat);
				 	 $relevant_saleacc_list = $this->TransactionModel->relevant_saleacc_dropdown($impitm_sale_acc);
				 	 $relevant_purchaseacc_list = $this->TransactionModel->relevant_purchaseacc_dropdown($impitm_pur_acc);
				 	 
					
					if(count($relevant_catg_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["impitm_cat_sug"] =array("title"=>"Use suggested name to pick category.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impitm_cat_sug"] =array("title"=>"Use suggested name to pick category");
					} 
					
					if(count($relevant_saleacc_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["impitm_sale_acc_sug"] =array("title"=>"Use suggested name to pick sale account.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impitm_sale_acc_sug"] =array("title"=>"Use suggested name to pick sale account");
					} 
					if(count($relevant_purchaseacc_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["impitm_pur_acc_sug"] =array("title"=>"Use suggested name to pick purchase account.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impitm_pur_acc_sug"] =array("title"=>"Use suggested name to pick purchase account");
					} 
					
					$data[$key]['impitm_cat_sug_list'] = $relevant_catg_list;
					$data[$key]['impitm_sale_acc_list'] = $relevant_saleacc_list;
					$data[$key]['impitm_pur_acc_sug_list'] = $relevant_purchaseacc_list;
				   }
				   
				   if($impexp_type == 4){
					 $impitmgrp_under_grp = strtolower(html_entity_decode($value['impitmgrp_under_grp']));					
					 $relevant_undergroup_list = $this->TransactionModel->relevant_item_undergroup_dropdown($impitmgrp_under_grp);
				 	 
					
					if(count($relevant_undergroup_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["impitmgrp_under_grp_sug"] =array("title"=>"Use suggested name to pick under group.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["impitmgrp_under_grp_sug"] =array("title"=>"Use suggested name to pick under group.");
					} 
					
					$data[$key]['impitmgrp_under_grp_sug_list'] = $relevant_undergroup_list;
				   }
				   
				   
				$data[$key]['sno'] = $sno;
			}
		}

		if(in_array($impexp_type, [6,7])){

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
					$voucher_series_name = strtolower(html_entity_decode($value['imptxnvch_series']));
					 $relevant_vchseries_list = $this->TransactionModel->relevant_vchseries_dropdown($voucher_series_name);
					if(count($relevant_vchseries_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					} 
					
					$data[$key]['imptxnvch_series_sug_list'] = $relevant_vchseries_list;
				  
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($temp2 as $key2 => $value2) {
					$accbsd_name = strtolower(html_entity_decode($value2['imptxnvch_acc_bds']));					 
					 $relevant_accbsd_list = $this->TransactionModel->relevant_acc_bds_dropdown($accbsd_name);
					 
				 	if(count($relevant_accbsd_list)==0){
					 $value2['tooltip_exists'] = 1;
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					}
				    else{
					 $value2['tooltip_exists'] = 1;	
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					} 
					
					$value2['imptxnvch_acc_bds_sug_list'] = $relevant_accbsd_list;
				  
					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row						
						$data[] = array_merge($value,$value2);
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$data[] = $value2;
					}
				}

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}

		if(in_array($impexp_type, [8])){ // Sale Without Item

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
					
					$voucher_series_name = strtolower(html_entity_decode($value['imptxnvch_series']));
					 $relevant_vchseries_list = $this->TransactionModel->relevant_vchseries_dropdown($voucher_series_name);
					if(count($relevant_vchseries_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					} 
					
					$data[$key]['imptxnvch_series_sug_list'] = $relevant_vchseries_list;
				  
				}

			$temp = $data;
			$data = [];
			foreach ($temp as $key => $value) {

				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				$value['imptxnvch_pos'] = $temp2['impoutsup_pos'] ?? '';
				$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';

				$temp2 = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($temp2 as $key2 => $value2) {
					 $accbsd_name = strtolower(html_entity_decode($value2['imptxnvch_acc_bds']));					 
					 $relevant_accbsd_list = $this->TransactionModel->relevant_acc_bds_dropdown($accbsd_name);
					 
				 	if(count($relevant_accbsd_list)==0){
					 $value2['tooltip_exists'] = 1;
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					}
				    else{
					 $value2['tooltip_exists'] = 1;	
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					} 
					
					$value2['imptxnvch_acc_bds_sug_list'] = $relevant_accbsd_list;
				  

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row						
						$data[] = array_merge($value,$value2);
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$data[] = $value2;
					}
				}

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}

		if(in_array($impexp_type, [9])){// Sale With Item

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
					
					$voucher_series_name = strtolower(html_entity_decode($value['imptxnvch_series']));
					$relevant_vchseries_list = $this->TransactionModel->relevant_vchseries_dropdown($voucher_series_name);
					if(count($relevant_vchseries_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					} 
					
					$data[$key]['imptxnvch_series_sug_list'] = $relevant_vchseries_list;
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				
				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				$value['imptxnvch_pos'] = $temp2['impoutsup_pos'] ?? '';
				$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';
				$value['imptxnvch_supplytype'] = $temp2['impout_supply_type'] ?? '';
				
				$result = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($result as $key2 => $value2) {
					$accbsd_name = strtolower(html_entity_decode($value2['imptxnvch_acc_bds']));					 
					 $relevant_accbsd_list = $this->TransactionModel->relevant_acc_bds_dropdown($accbsd_name);
					 
				 	if(count($relevant_accbsd_list)==0){
					 $value2['tooltip_exists'] = 1;
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					}
				    else{
					 $value2['tooltip_exists'] = 1;	
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					} 
					
					$value2['imptxnvch_acc_bds_sug_list'] = $relevant_accbsd_list;
				  

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row	
				
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						
						$data[] = array_merge($value,$value2);

						$result3 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$value['imp_id'])
												->get()->getResultArray();

						// Items 
						foreach ($result3 as $key3 => $value3) {

							$value3['imptxnvch_acc_bds'] = $value3['imptxnvch_item'];
							$value3['imptxnvch_amt_dr'] = floatval($value3['imptxnvch_amt_dr']);
							$value3['imptxnvch_amt_cr'] = floatval($value3['imptxnvch_amt_cr']);

							$value3['imptxnvch_uqc'] = $value3['imptxnvch_uqc_cr'];
							$value3['imptxnvch_qty'] = floatval($value3['imptxnvch_qty_cr']);
							$value3['imptxnvch_mc'] = '';

							$value3['imp_id'] = $imp_id;
							$value3['imp_status'] = $imp_status;
							$data[] = $value3;
							
						}
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						$value2['imptxnvch_mc'] = '';
						$data[] = $value2;
					}
				}

						

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}
		
		if(in_array($impexp_type, [10])){ // Purchase Without Item

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
					
					$voucher_series_name = strtolower(html_entity_decode($value['imptxnvch_series']));
					 $relevant_vchseries_list = $this->TransactionModel->relevant_vchseries_dropdown($voucher_series_name);
					if(count($relevant_vchseries_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					} 
					
					$data[$key]['imptxnvch_series_sug_list'] = $relevant_vchseries_list;
				  
				}

			$temp = $data;
			$data = [];
			foreach ($temp as $key => $value) {

				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				$value['imptxnvch_posid'] = $temp2['impinwsup_pos'] ?? '';
				$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
				$value['imptxnvch_supplytype'] = $temp2['impinw_supply_type'] ?? '';
				$StatesData  = $this->TransactionModel->show_states_lists(1);
				
				if(isset($StatesData[$temp2['impinwsup_pos']]))
				 $impout_pos_type = $StatesData[$temp2['impinwsup_pos']];
			   else 
				  $impout_pos_type ='';
				$value['imptxnvch_pos'] = $impout_pos_type;

				$temp2 = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($temp2 as $key2 => $value2) {
					 $accbsd_name = strtolower(html_entity_decode($value2['imptxnvch_acc_bds']));					 
					 $relevant_accbsd_list = $this->TransactionModel->relevant_acc_bds_dropdown($accbsd_name);
					 
				 	if(count($relevant_accbsd_list)==0){
					 $value2['tooltip_exists'] = 1;
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					}
				    else{
					 $value2['tooltip_exists'] = 1;	
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					} 
					
					$value2['imptxnvch_acc_bds_sug_list'] = $relevant_accbsd_list;
				  

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row						
						$data[] = array_merge($value,$value2);
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$data[] = $value2;
					}
				}

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}
		
		if(in_array($impexp_type, [11])){// Purchase With Item

			$data = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

			$sno = $offset;
				foreach ($data as $key => $value) {
					$sno++;
					$data[$key]['sno'] = $sno;
					
					$voucher_series_name = strtolower(html_entity_decode($value['imptxnvch_series']));
					$relevant_vchseries_list = $this->TransactionModel->relevant_vchseries_dropdown($voucher_series_name);
					if(count($relevant_vchseries_list)==0){
					 $data[$key]['tooltip_exists'] = 1;
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					}
				    else{
					 $data[$key]['tooltip_exists'] = 1;	
					 $data[$key]["pq_cellattr"]["imptxnvch_series_sug"] =array("title"=>"Use suggested name to pick series.");
					} 
					
					$data[$key]['imptxnvch_series_sug_list'] = $relevant_vchseries_list;
				}

			$temp = $data;
			$data = [];

			foreach ($temp as $key => $value) {

				
				$imp_id = $value['imp_id'];
				$imp_status = $value['imp_status'];


				$narration = $value['imptxnvch_long_narr'];
				unset($value['imptxnvch_long_narr']);

				$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
				$value['imptxnvch_posid'] = $temp2['impinwsup_pos'] ?? '';
				$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
				$value['imptxnvch_supplytype'] = $temp2['impinw_supply_type'] ?? '';
				if(isset($StatesData[$temp2['impinwsup_pos']]))
				 $impout_pos_type = $StatesData[$temp2['impinwsup_pos']];
			   else 
				  $impout_pos_type ='';
				$value['imptxnvch_pos'] = $impout_pos_type;
				$result = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getResultArray();

				foreach ($result as $key2 => $value2) {
					$accbsd_name = strtolower(html_entity_decode($value2['imptxnvch_acc_bds']));					 
					 $relevant_accbsd_list = $this->TransactionModel->relevant_acc_bds_dropdown($accbsd_name);
					 
				 	if(count($relevant_accbsd_list)==0){
					 $value2['tooltip_exists'] = 1;
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					}
				    else{
					 $value2['tooltip_exists'] = 1;	
					 $value2["pq_cellattr"]["imptxnvch_acc_bds_sug"] =array("title"=>"Use suggested name to pick account/bill sundry.");
					} 
					
					$value2['imptxnvch_acc_bds_sug_list'] = $relevant_accbsd_list;
				  

					$value2['imptxnvch_amt_dr'] = floatval($value2['imptxnvch_amt_dr']);
					$value2['imptxnvch_amt_cr'] = floatval($value2['imptxnvch_amt_cr']);

					if($key2 == 0){ // first row	
				
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						
						$data[] = array_merge($value,$value2);

						$result3 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$value['imp_id'])
												->get()->getResultArray();

						// Items 
						foreach ($result3 as $key3 => $value3) {

							$value3['imptxnvch_acc_bds'] = $value3['imptxnvch_item'];
							$value3['imptxnvch_amt_dr'] = floatval($value3['imptxnvch_amt_dr']);
							$value3['imptxnvch_amt_cr'] = floatval($value3['imptxnvch_amt_cr']);

							$value3['imptxnvch_uqc'] = $value3['imptxnvch_uqc_cr'];
							$value3['imptxnvch_qty'] = floatval($value3['imptxnvch_qty_cr']);
							$value3['imptxnvch_mc'] = '';

							$value3['imp_id'] = $imp_id;
							$value3['imp_status'] = $imp_status;
							$data[] = $value3;
							
						}
					}
					else{
						$value2['imp_id'] = $imp_id;
						$value2['imp_status'] = $imp_status;
						$value2['imptxnvch_uqc'] = '';
						$value2['imptxnvch_qty'] = '';
						$value2['imptxnvch_mc'] = '';
						$data[] = $value2;
					}
				}

						

				$final = [];
				$final['imptxnvch_acc_bds'] = $narration;
				$final['imp_status'] = $imp_status;
				$final['imp_id'] = $imp_id;
				$final['imp_sub_id'] = -1;
				$data[] = $final;

				$data[] = ['imp_id' => 0];
			}
		}
		
		$columns = [];
		if($impexp_type == 1){ // Account Master
			$columns = ['sno' => 'S.No.', 'impacc_name' => 'Name','impacc_prt' => 'Parent','impacc_prt_sug' => 'Parent Suggestion', 'impacc_grp' => 'Under Group','impacc_grp_sug' => 'Under Group Suggestion'];
		}
		if($impexp_type == 2){ // Account Group Master
			$columns = ['sno' => 'S.No.', 'impaccgrp_name' => 'Group Name', 'impaccgrp_under_prt' => 'Parent', 'impaccgrp_under_prt_sug' => 'Parent Suggestion','impaccgrp_under_grp' => 'Under Group','impaccgrp_under_grp_sug' => 'Under Group Suggestion'];
		}
		if($impexp_type == 3){ // Item Master
			$columns = ['sno' => 'S.No.', 'impitm_name' => 'Name', 'impitm_cat' => 'Category','impitm_upc' => 'UPC','impitm_cat_sug' => 'Category Suggestion','impitm_sale_acc' => 'Sales Acc.','impitm_sale_acc_sug' => 'Sales Acc. Suggestion','impitm_pur_acc' => 'Purchase Acc.','impitm_pur_acc_sug' => 'Purchase Acc. Suggestion'];
		}
		if($impexp_type == 4){ // Item Group Master
			$columns = ['sno' => 'S.No.', 'impitmgrp_name' => 'Group Name', 'impitmgrp_under_grp' => 'Under Group','impitmgrp_under_grp_sug' => 'Under Group Suggestion'];
		}
		if($impexp_type == 5){ // Item Category Master
			$columns = ['sno' => 'S.No.', 'impitmcat_name' => 'Category Name','impitmcat_name_sug' => 'Suggested Category Name', 'impitmcat_alias' => 'Category Alias'];
		}

		if($impexp_type == 6){ // Day Book
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_series' => 'Series','imptxnvch_series_sug' => 'Series Suggestion', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_acc_bds' => 'Account','imptxnvch_acc_bds_sug' => 'Account Suggestion'];
		}
		if($impexp_type == 7){ // Bank Statement
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_series' => 'Series', 'imptxnvch_series_sug' => 'Series Suggestion','imptxnvch_acc_bds' => 'Account','imptxnvch_acc_bds_sug' => 'Account Suggestion'];
		}
		if($impexp_type == 8){ // Sale Without Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date','imptxnvch_series' => 'Series','imptxnvch_series_sug' => 'Series Suggestion', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_acc_bds' => 'Account','imptxnvch_acc_bds_sug' => 'Account Suggestion'];
		}
		if($impexp_type == 9){ // Sale With Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_series' => 'Series', 'imptxnvch_series_sug' => 'Series Suggestion', 'imptxnvch_bill_no' => 'Bill. No.','imptxnvch_acc_bds' => 'Account','imptxnvch_acc_bds_sug' => 'Account Suggestion'];
		}
		
        if($impexp_type == 10){ // Purchase Without Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date','imptxnvch_series' => 'Series','imptxnvch_series_sug' => 'Series Suggestion', 'imptxnvch_bill_no' => 'Bill. No.', 'imptxnvch_acc_bds' => 'Account','imptxnvch_acc_bds_sug' => 'Account Suggestion'];
		}
		if($impexp_type == 11){ // Purchase With Item
			$columns = ['sno' => 'S.No.', 'imptxnvch_date' => 'Date', 'imptxnvch_series' => 'Series', 'imptxnvch_series_sug' => 'Series Suggestion', 'imptxnvch_bill_no' => 'Bill. No.','imptxnvch_acc_bds' => 'Account','imptxnvch_acc_bds_sug' => 'Account Suggestion'];
		}		

		return ['columns' => $columns, 'total' => $total,'uploaded' => $uploaded, 'data' => $data];
	}

	function importAccountGroupMaster($impexp_sr_id,$limit,$offset)
	{

		$errors = [];
		$uploaded = [];

		$group_parents = [ 1 => 'Owner\'s Fund', 2 => 'Non Current Liabilities', 3 => 'Non Current Assets', 4 => 'Current Liabilities', 5 => 'Current Assets', 7 => 'Purchase', 8 => 'Sales', 10 => 'Direct Income', 11 => 'Direct Expenses', 12 => 'Indirect Income', 13 => 'Indirect Expenses'];

		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impaccgrpn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impaccgrp_name']));
			$alias = trim(html_entity_decode($value['impaccgrp_alias']));
			$parent = trim(html_entity_decode($value['impaccgrp_under_prt']));
			$under = trim(html_entity_decode($value['impaccgrp_under_grp']));
			$under_acc_grp_id = 0;

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Group Name is required';
			}

			if($status && $alias == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Group Alias is required';
			}

			if($status && $parent == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Parent is required';
			}

			if($status){
				$exists = $this->db->table($acctgroupn_tbl)
											->where('LOWER(acc_grp_name)', strtolower($name))
											->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Group Name already exists';
				}					
			}
			if($status){
				if(!in_array($parent, $group_parents)){
					$status = false;
					$errors[$imp_id] = $name.'- Invalid Parent';
				}
			}

			if($status && $under != ''){

				$exists = $this->db->table($acctgroupn_tbl)
											->where('LOWER(acc_grp_name)', strtolower($under))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Under Group "'.$under.'" does not exists';
				}
				else{
					$under_acc_grp_id = intval($exists['acc_grp_id']);
					$ug_parent_id =intval($exists['acc_grp_parent_id']);

					if(isset($group_parents[$ug_parent_id]) && $group_parents[$ug_parent_id] != $parent){
						$status = false;
						$errors[$imp_id] = $name.'- Parent does not match Under Group "'.$under.'" Parent';
					}
				}				
			}
	
			if($status){

				$acc_grp_parent_id = array_search($parent,$group_parents);

				$primary_group = 'Y';
				if($under_acc_grp_id != 0)
					$primary_group = 'N';

				$under_main_grp_id = 0;
				if($under_acc_grp_id != 0){
					$acctgroupn = $this->db->table($acctgroupn_tbl)
											->where('acc_grp_id',$under_acc_grp_id)
											->get()->getRowArray();

					if($acctgroupn){
						if($acctgroupn['acc_grp_primary'] == 'Y')
							$under_main_grp_id = intval($acctgroupn['acc_grp_id']);
						else
							$under_main_grp_id = intval($acctgroupn['under_main_grp_id']);
					}
				}



				$insert   = [
					'comp_id'            => $this->company_id,
					'acc_grp_name'       => ucwords($name),
					'acc_grp_alias'      => $alias,
					'acc_grp_primary'    => $primary_group,
					'acc_grp_parent_id'  => $acc_grp_parent_id,
					'under_acc_grp_id'   => $under_acc_grp_id,
					'under_main_grp_id'  => $under_main_grp_id,
		  	];

		  	$this->db->table($acctgroupn_tbl)->insert($insert);
		  	$group_id = $this->db->insertID();

		  	$mst_base_id = $this->create_mst_base_id($group_id,'acctgroupn');
				$this->db->table($acctgroupn_tbl)
								->where('acc_grp_id',$group_id)
								->update(['mst_base_id' => $mst_base_id]);

				$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->update(['imp_status' => 1]);

				if($under != '')
					$uploaded[$imp_id] = $name.' under "'.$under.'" ('.$parent.')';
				else
					$uploaded[$imp_id] = $name.' ('.$parent.')';

				 
			}

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateAccountGroupMaster($impexp_sr_id,$limit,$offset)
	{

		$errors = [];
		$uploaded = [];

		$group_parents = [ 1 => 'Owner\'s Fund', 2 => 'Non Current Liabilities', 3 => 'Non Current Assets', 4 => 'Current Liabilities', 5 => 'Current Assets', 7 => 'Purchase', 8 => 'Sales', 10 => 'Direct Income', 11 => 'Direct Expenses', 12 => 'Indirect Income', 13 => 'Indirect Expenses'];

		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impaccgrpn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impaccgrp_name']));
			$alias = trim(html_entity_decode($value['impaccgrp_alias']));
			$parent = trim(html_entity_decode($value['impaccgrp_under_prt']));
			$under = trim(html_entity_decode($value['impaccgrp_under_grp']));
			$under_acc_grp_id = 0;

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Group Name is required';
			}

			if($status && $alias == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Group Alias is required';
			}

			if($status && $parent == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Parent is required';
			}

			if($status){
				$exists = $this->db->table($acctgroupn_tbl)
											->where('LOWER(acc_grp_name)', strtolower($name))
											->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Group Name already exists';
				}					
			}
			if($status){
				if(!in_array($parent, $group_parents)){
					$status = false;
					$errors[$imp_id] = $name.'- Invalid Parent';
				}
			}

			if($status && $under != ''){

				$exists = $this->db->table($acctgroupn_tbl)
											->where('LOWER(acc_grp_name)', strtolower($under))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Under Group "'.$under.'" does not exists';
				}
				else{
					$under_acc_grp_id = intval($exists['acc_grp_id']);
					$ug_parent_id =intval($exists['acc_grp_parent_id']);

					if(isset($group_parents[$ug_parent_id]) && $group_parents[$ug_parent_id] != $parent){
						$status = false;
						$errors[$imp_id] = $name.'- Parent does not match Under Group "'.$under.'" Parent';
					}
				}				
			}
	
			if($status){
				if($under != '')
					$uploaded[$imp_id] = $name.' under "'.$under.'" ('.$parent.')';
				else
					$uploaded[$imp_id] = $name.' ('.$parent.')';
			    }

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

	function importAccountMaster($impexp_sr_id,$limit,$offset)
	{

		$errors = [];
		$uploaded = [];

		$group_parents = [ 1 => 'Owner\'s Fund', 2 => 'Non Current Liabilities', 3 => 'Non Current Assets', 4 => 'Current Liabilities', 5 => 'Current Assets', 7 => 'Purchase', 8 => 'Sales', 10 => 'Direct Income', 11 => 'Direct Expenses', 12 => 'Indirect Income', 13 => 'Indirect Expenses'];

		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impaccmstn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();
		 
		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impacc_name']));
			$alias = trim(html_entity_decode($value['impacc_alias']));
			$print = trim(html_entity_decode($value['impacc_print']));
			$vendor_code = trim(html_entity_decode($value['impacc_vendor_code']));
			$primary = trim($value['impacc_primary']);
			$parent = trim(html_entity_decode($value['impacc_prt']));
			$group = trim(html_entity_decode($value['impacc_grp']));
			$acc_grp_id = 0;

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Account Name is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}

			if($status && $print == ''){
				$print = $name;
			}

			if($status && !($primary == 'Y' || $primary == 'N')){
				$status = false;
				$errors[$imp_id] = $name.'- Primary "Y" or "N" invalid';
			}

			if($status && $group == '' && $primary == 'N'){
				$status = false;
				$errors[$imp_id] = $name.'- Account Group is required';
			}

			if($status && $parent == '' && $primary == 'Y'){
				$status = false;
				$errors[$imp_id] = $name.'- Parent is required';
			}

			if($status && $parent != ''){
				if(!in_array($parent, $group_parents)){
					$status = false;
					$errors[$imp_id] = $name.'- Invalid Parent';
				}
			}
			
			if($status && $group != ''){
				if(strtolower($group)=='duties & taxes'){
					$status = false;
					$errors[$imp_id] = $group."- account's restricted to import";
				}
			}
			

			if($status){
				$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower($name))
								->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Account Name already exists';
				}					
			}
			

			if($status && $group != ''){					
				$exists = $this->db->table($acctgroupn_tbl)
											->where('LOWER(acc_grp_name)', strtolower($group))
											->get()->getRowArray();											
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'-  Group "'.$group.'" does not exists';
				}
				else{
					$acc_grp_id = intval($exists['acc_grp_id']);
				}				
			}
	
			if($status){

				$acc_grp_parent_id = 0;

				if($primary == 'Y')
					$acc_grp_parent_id = array_search($parent,$group_parents);

				$insert   = [
					'comp_id'            	=> $this->company_id,
					'acc_name'       			=> ucwords($name),
					'acc_name_alias'      => $alias,
					'acc_name_print'    	=> $print,
					'vendor_code'  				=> $vendor_code,
					'acc_grp_id'  				=> $acc_grp_id,
					'acc_grp_parent_id'   => $acc_grp_parent_id,
					
		  	];

		  	$this->db->table($acctmaster_tbl)->insert($insert);
		  	$acc_id = $this->db->insertID();

		  	$ERPtables = new ERPtables($this->company_id,$this->session->get('ses_comp_fy_id'));
				$ERPtables->account_txn_tables($acc_id);

		  	$mst_base_id = $this->create_mst_base_id($acc_id,'acctmaster');
				$this->db->table($acctgroupn_tbl)
								->where('acc_grp_id',$acc_id)
								->update(['mst_base_id' => $mst_base_id]);

				$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->update(['imp_status' => 1]);

				if($primary == 'N')
					$uploaded[$imp_id] = $name.' under "'.$group;
				if($primary == 'Y')
					$uploaded[$imp_id] = $name.' ('.$parent.')';

				 
			}

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateAccountMaster($impexp_sr_id,$limit,$offset)
	{

		$errors = [];
		$uploaded = [];

		$group_parents = [ 1 => 'Owner\'s Fund', 2 => 'Non Current Liabilities', 3 => 'Non Current Assets', 4 => 'Current Liabilities', 5 => 'Current Assets', 7 => 'Purchase', 8 => 'Sales', 10 => 'Direct Income', 11 => 'Direct Expenses', 12 => 'Indirect Income', 13 => 'Indirect Expenses'];

		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impaccmstn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();
		 
		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impacc_name']));
			$alias = trim(html_entity_decode($value['impacc_alias']));
			$print = trim(html_entity_decode($value['impacc_print']));
			$vendor_code = trim(html_entity_decode($value['impacc_vendor_code']));
			$primary = trim($value['impacc_primary']);
			$parent = trim(html_entity_decode($value['impacc_prt']));
			$group = trim(html_entity_decode($value['impacc_grp']));
			$acc_grp_id = 0;

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Account Name is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}

			if($status && $print == ''){
				$print = $name;
			}

			if($status && !($primary == 'Y' || $primary == 'N')){
				$status = false;
				$errors[$imp_id] = $name.'- Primary "Y" or "N" invalid';
			}

			if($status && $group == '' && $primary == 'N'){
				$status = false;
				$errors[$imp_id] = $name.'- Account Group is required';
			}

			if($status && $parent == '' && $primary == 'Y'){
				$status = false;
				$errors[$imp_id] = $name.'- Parent is required';
			}

			if($status && $parent != ''){
				if(!in_array($parent, $group_parents)){
					$status = false;
					$errors[$imp_id] = $name.'- Invalid Parent';
				}
			}
			
			if($status && $group != ''){
				if(strtolower($group)=='duties & taxes'){
					$status = false;
					$errors[$imp_id] = $group."- account's restricted to import";
				}
			}
			

			if($status){
				$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower($name))
								->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Account Name already exists';
				}					
			}
			

			if($status && $group != ''){					
				$exists = $this->db->table($acctgroupn_tbl)
											->where('LOWER(acc_grp_name)', strtolower($group))
											->get()->getRowArray();											
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'-  Group "'.$group.'" does not exists';
				}
				else{
					$acc_grp_id = intval($exists['acc_grp_id']);
				}				
			}
	
			if($status){
				if($primary == 'N')
					$uploaded[$imp_id] = $name.' under "'.$group;
				if($primary == 'Y')
					$uploaded[$imp_id] = $name.' ('.$parent.')';

				 
			}

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

	function importItemMaster($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$val_arr = ['AVG','FIFO','LIFO'];

		$itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$itemgrpmst_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
		$itemcatmst_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');

		$itmunitmst_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');

		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impitmmstn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impitm_name']));
			$alias = trim(html_entity_decode($value['impitm_alias']));
			$print = trim(html_entity_decode($value['impitm_print']));

			$group = trim(html_entity_decode($value['impitm_grp']));
			$category = trim(html_entity_decode($value['impitm_cat']));

			$sal_acc = trim(html_entity_decode($value['impitm_sale_acc']));
			$pur_acc = trim(html_entity_decode($value['impitm_pur_acc']));

			$unit = trim(html_entity_decode($value['impitm_unit']));
			$val_method = trim(html_entity_decode($value['impitm_val_method']));
			$item_upc = trim(html_entity_decode($value['impitm_upc']));
			$mrp = trim($value['impitm_mrp']);

			$itm_grp_id = 0;
			$itm_cat_id = 0;
			$itm_sal_acc = 0;
			$itm_pur_acc = 0;
			$itm_unt_id = 0;
			

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Name is required';
			}

			if($status && $group == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Group Name is required';
			}
			if($status && $category == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Category Name is required';
			}
			if($status && $sal_acc == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Sales Account is required';
			}
			if($status && $pur_acc == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Purchase Account is required';
			}
			if($status && $unit == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Unit is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}
			if($status && $print == ''){
				$print = $name;
			}

			if($status){
				$exists = $this->db->table($itemmaster_tbl)
											->where('LOWER(item_name)', strtolower($name))
											->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Item Name already exists';
				}					
			}

			if($status && $group != ''){

				$exists = $this->db->table($itemgrpmst_tbl)
											->where('LOWER(item_grp_name)', strtolower($group))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Group "'.$group.'" does not exists';
				}
				else{
					$itm_grp_id = intval($exists['item_grp_id']);
				}				
			}

			if($status && $category != ''){
				$exists = $this->db->table($itemcatmst_tbl)
											->where('LOWER(item_cat)',strtolower($category))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Category "'.$category.'" does not exists';
				}
				else{
					$itm_cat_id = intval($exists['icatgms_id']);
				}					
			}

			if($status && $sal_acc != ''){
				$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower($sal_acc))
								->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Sales Account "'.$sal_acc.'" does not exists';
				}
				else{
					
					$acc_grp_parent_id = $exists['acc_grp_parent_id'];
					$acc_grp_id = $exists['acc_grp_id'];

					if($acc_grp_parent_id == 0 && $acc_grp_id == 0){
						$status = false;
						$errors[$imp_id] = $name.'- Sales Account "'.$sal_acc.'" not valid';
					}

					if($acc_grp_parent_id != 0){
						if($acc_grp_parent_id != 8){
							$status = false;
							$errors[$imp_id] = $name.'- Account "'.$sal_acc.'" does not come under Sales';
						}
					}

					if($acc_grp_id != 0){
						$acctgroupn = $this->db->table($acctgroupn_tbl)
												->where('acc_grp_id',$acc_grp_id)
												->get()->getRowArray();
						if($acctgroupn){
							if($acctgroupn['acc_grp_parent_id'] != 8){
								$status = false;
								$errors[$imp_id] = $name.'- Account "'.$sal_acc.'" does not come under Sales';
							}
						}
						else{
							$status = false;
							$errors[$imp_id] = $name.'- Sales Account "'.$sal_acc.'" not valid';
						}
					}
				}
				if($status!=false)
                  $itm_sal_acc = $exists['acc_id'];					
			}

			if($status && $pur_acc != ''){
				$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower($pur_acc))
								->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Purchase Account "'.$pur_acc.'" does not exists';
				}
				else{
					$acc_grp_parent_id = $exists['acc_grp_parent_id'];
					$acc_grp_id = $exists['acc_grp_id'];

					if($acc_grp_parent_id == 0 && $acc_grp_id == 0){
						$status = false;
						$errors[$imp_id] = $name.'- Purchase Account "'.$pur_acc.'" not valid';
					}

					if($acc_grp_parent_id != 0){
						if($acc_grp_parent_id != 7){
							$status = false;
							$errors[$imp_id] = $name.'- Account "'.$pur_acc.'" does not come under Purchase';
						}
					}

					if($acc_grp_id != 0){
						$acctgroupn = $this->db->table($acctgroupn_tbl)
												->where('acc_grp_id',$acc_grp_id)
												->get()->getRowArray();
						if($acctgroupn){
							if($acctgroupn['acc_grp_parent_id'] != 7){
								$status = false;
								$errors[$imp_id] = $name.'- Account "'.$pur_acc.'" does not come under Purchase';
							}
						}
						else{
							$status = false;
							$errors[$imp_id] = $name.'- Purchase Account "'.$pur_acc.'" not valid';
						}
					}
				}	
			 if($status!=false)	
              $itm_pur_acc = $exists['acc_id'];				
			}

			if($status && $unit != ''){
				$exists = $this->db->table($itmunitmst_tbl)
											->where('LOWER(item_unit)',strtolower($unit))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Item Unit "'.$unit.'" does not exists';
				}
				else{
					$itm_unt_id = intval($exists['unit_id']);
				}					
			}

			if($status && $val_method != ''){
				
				if(!in_array(strtoupper($val_method),$val_arr)){
					$status = false;
					$errors[$imp_id] = $name.'- Valuation Method "'.$val_method.'" not valid';
				}
				$val_method = strtoupper($val_method);
			}

			if($status && $mrp != ''){
				$mrp = parseAmount($mrp);
			}
			else{
				$mrp = 0;
			}
	
			if($status){

				$insert   = [
					'comp_id'          	=> $this->company_id,
					'item_name'       	=> ucwords($name),
					'item_alias'      	=> $alias,
					'item_print'    	=> $print,
					'item_grp_id'    	=> $itm_grp_id,
					'item_cat'    		=> $itm_cat_id,
					'item_sales_acc'  	=> $itm_sal_acc,
					'item_pur_acc'   	=> $itm_pur_acc,
					'item_unit' 		=> $itm_unt_id,
					'valmethod_id' 		=> $val_method,
					'item_upc'          => $item_upc
		  	];


		  	$this->db->table($itemmaster_tbl)->insert($insert);
		  	$insertID = $this->db->insertID();

		  	$ERPtables = new ERPtables($this->company_id,$this->session->get('ses_comp_fy_id'));
				$ERPtables->item_txn_tables($insertID);

		  	$mst_base_id = $this->create_mst_base_id($insertID,'itemmaster');
				$this->db->table($itemmaster_tbl)
								->where('item_id',$insertID)
								->update(['mst_base_id' => $mst_base_id]);

				$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->update(['imp_status' => 1]);

				$insert2 = [
		  		'comp_id'     => $this->company_id,
		  		'item_id'			=> $insertID,
		  		'item_mrp' 		=> $mrp
		  	];

		  	$itemvalmst_tbl = $this->company_id.'_itemvalmst_'.$this->session->get('ses_comp_fy_id');
		  	$this->db->table($itemvalmst_tbl)->insert($insert2);


				$uploaded[$imp_id] = $name;

			}

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateItemMaster($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$val_arr = ['AVG','FIFO','LIFO'];

		$itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$itemgrpmst_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
		$itemcatmst_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');

		$itmunitmst_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');

		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impitmmstn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impitm_name']));
			$alias = trim(html_entity_decode($value['impitm_alias']));
			$print = trim(html_entity_decode($value['impitm_print']));

			$group = trim(html_entity_decode($value['impitm_grp']));
			$category = trim(html_entity_decode($value['impitm_cat']));

			$sal_acc = trim(html_entity_decode($value['impitm_sale_acc']));
			$pur_acc = trim(html_entity_decode($value['impitm_pur_acc']));

			$unit = trim(html_entity_decode($value['impitm_unit']));
			$val_method = trim(html_entity_decode($value['impitm_val_method']));
			$item_upc = trim(html_entity_decode($value['impitm_upc']));
			$mrp = trim($value['impitm_mrp']);

			$itm_grp_id = 0;
			$itm_cat_id = 0;
			$itm_sal_acc = 0;
			$itm_pur_acc = 0;
			$itm_unt_id = 0;
			

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Name is required';
			}

			if($status && $group == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Group Name is required';
			}
			if($status && $category == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Category Name is required';
			}
			if($status && $sal_acc == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Sales Account is required';
			}
			if($status && $pur_acc == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Purchase Account is required';
			}
			if($status && $unit == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Item Unit is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}
			if($status && $print == ''){
				$print = $name;
			}

			if($status){
				$exists = $this->db->table($itemmaster_tbl)
											->where('LOWER(item_name)', strtolower($name))
											->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Item Name already exists';
				}					
			}

			if($status && $group != ''){

				$exists = $this->db->table($itemgrpmst_tbl)
											->where('LOWER(item_grp_name)', strtolower($group))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Group "'.$group.'" does not exists';
				}
				else{
					$itm_grp_id = intval($exists['item_grp_id']);
				}				
			}

			if($status && $category != ''){
				$exists = $this->db->table($itemcatmst_tbl)
											->where('LOWER(item_cat)',strtolower($category))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Category "'.$category.'" does not exists';
				}
				else{
					$itm_cat_id = intval($exists['icatgms_id']);
				}					
			}

			if($status && $sal_acc != ''){
				$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower($sal_acc))
								->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Sales Account "'.$sal_acc.'" does not exists';
				}
				else{
					
					$acc_grp_parent_id = $exists['acc_grp_parent_id'];
					$acc_grp_id = $exists['acc_grp_id'];

					if($acc_grp_parent_id == 0 && $acc_grp_id == 0){
						$status = false;
						$errors[$imp_id] = $name.'- Sales Account "'.$sal_acc.'" not valid';
					}

					if($acc_grp_parent_id != 0){
						if($acc_grp_parent_id != 8){
							$status = false;
							$errors[$imp_id] = $name.'- Account "'.$sal_acc.'" does not come under Sales';
						}
					}

					if($acc_grp_id != 0){
						$acctgroupn = $this->db->table($acctgroupn_tbl)
												->where('acc_grp_id',$acc_grp_id)
												->get()->getRowArray();
						if($acctgroupn){
							if($acctgroupn['acc_grp_parent_id'] != 8){
								$status = false;
								$errors[$imp_id] = $name.'- Account "'.$sal_acc.'" does not come under Sales';
							}
						}
						else{
							$status = false;
							$errors[$imp_id] = $name.'- Sales Account "'.$sal_acc.'" not valid';
						}
					}
				}
				if($status!=false)
                  $itm_sal_acc = $exists['acc_id'];					
			}

			if($status && $pur_acc != ''){
				$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower($pur_acc))
								->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Purchase Account "'.$pur_acc.'" does not exists';
				}
				else{
					$acc_grp_parent_id = $exists['acc_grp_parent_id'];
					$acc_grp_id = $exists['acc_grp_id'];

					if($acc_grp_parent_id == 0 && $acc_grp_id == 0){
						$status = false;
						$errors[$imp_id] = $name.'- Purchase Account "'.$pur_acc.'" not valid';
					}

					if($acc_grp_parent_id != 0){
						if($acc_grp_parent_id != 7){
							$status = false;
							$errors[$imp_id] = $name.'- Account "'.$pur_acc.'" does not come under Purchase';
						}
					}

					if($acc_grp_id != 0){
						$acctgroupn = $this->db->table($acctgroupn_tbl)
												->where('acc_grp_id',$acc_grp_id)
												->get()->getRowArray();
						if($acctgroupn){
							if($acctgroupn['acc_grp_parent_id'] != 7){
								$status = false;
								$errors[$imp_id] = $name.'- Account "'.$pur_acc.'" does not come under Purchase';
							}
						}
						else{
							$status = false;
							$errors[$imp_id] = $name.'- Purchase Account "'.$pur_acc.'" not valid';
						}
					}
				}	
			 if($status!=false)	
              $itm_pur_acc = $exists['acc_id'];				
			}

			if($status && $unit != ''){
				$exists = $this->db->table($itmunitmst_tbl)
											->where('LOWER(item_unit)',strtolower($unit))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Item Unit "'.$unit.'" does not exists';
				}
				else{
					$itm_unt_id = intval($exists['unit_id']);
				}					
			}

			if($status && $val_method != ''){
				
				if(!in_array(strtoupper($val_method),$val_arr)){
					$status = false;
					$errors[$imp_id] = $name.'- Valuation Method "'.$val_method.'" not valid';
				}
				$val_method = strtoupper($val_method);
			}

			if($status && $mrp != ''){
				$mrp = parseAmount($mrp);
			}
			else{
				$mrp = 0;
			}
	
			if($status){
				$uploaded[$imp_id] = $name;
			   }
			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

	function importItemGroupMaster($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];

		$itemgrpmst_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impitmgrpn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impitmgrp_name']));
			$alias = trim(html_entity_decode($value['impitmgrp_alias']));
			$under = trim(html_entity_decode($value['impitmgrp_under_grp']));
			$under_acc_grp_id = 0;

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Group Name is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}

			if($status){
				$exists = $this->db->table($itemgrpmst_tbl)
											->where('LOWER(item_grp_name)', strtolower($name))
											->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Group Name already exists';
				}					
			}

			if($status && $under != ''){

				$exists = $this->db->table($itemgrpmst_tbl)
											->where('LOWER(item_grp_name)', strtolower($under))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Under Group "'.$under.'" does not exists';
				}
				else{
					$under_acc_grp_id = intval($exists['item_grp_id']);
				}				
			}
	
			if($status){

				$primary = 'Y';
				if($under_acc_grp_id != 0)
					$primary = 'N';

				$insert   = [
					'comp_id'            	=> $this->company_id,
					'item_grp_name'       => ucwords($name),
					'item_grp_alias'      => $alias,
					'item_grp_primary'    => $primary,
					'item_grp_parent_id'  => 0,
					'under_acc_grp_id'   	=> $under_acc_grp_id,
		  	];

		  	$this->db->table($itemgrpmst_tbl)->insert($insert);
		  	$insertID = $this->db->insertID();

		  	$mst_base_id = $this->create_mst_base_id($insertID,'itemgrpmst');
				$this->db->table($itemgrpmst_tbl)
								->where('item_grp_id',$insertID)
								->update(['mst_base_id' => $mst_base_id]);

				$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->update(['imp_status' => 1]);

				if($under != '')
					$uploaded[$imp_id] = $name.' under "'.$under.'"';
				else
					$uploaded[$imp_id] = $name;

			}

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateItemGroupMaster($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];

		$itemgrpmst_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
		$table = 'aicountly_impitmgrpn_univdb';
		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impitmgrp_name']));
			$alias = trim(html_entity_decode($value['impitmgrp_alias']));
			$under = trim(html_entity_decode($value['impitmgrp_under_grp']));
			$under_acc_grp_id = 0;

			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Group Name is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}

			if($status){
				$exists = $this->db->table($itemgrpmst_tbl)
											->where('LOWER(item_grp_name)', strtolower($name))
											->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Group Name already exists';
				}					
			}

			if($status && $under != ''){

				$exists = $this->db->table($itemgrpmst_tbl)
											->where('LOWER(item_grp_name)', strtolower($under))
											->get()->getRowArray();
				if(!$exists){
					$status = false;
					$errors[$imp_id] = $name.'- Under Group "'.$under.'" does not exists';
				}
				else{
					$under_acc_grp_id = intval($exists['item_grp_id']);
				}				
			}
	
			if($status){
				if($under != '')
					$uploaded[$imp_id] = $name.' under "'.$under.'"';
				else
					$uploaded[$imp_id] = $name;
		        }
			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

	function importItemCategoryMaster($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];

		$itemcatmst_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impitmcatn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impitmcat_name']));
			$alias = trim(html_entity_decode($value['impitmcat_alias']));


			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Category Name is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}

			if($status){
				$exists = $this->db->table($itemcatmst_tbl)
					->where('LOWER(item_cat)',strtolower($name))
					->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Category Name already exists';
				}					
			}
	
			if($status){

				$insert   = [
					'comp_id'            => $this->company_id,
					'item_cat'       		=> ucwords($name),
					'item_cat_alias'     => $alias,
		  	];

		  	$this->db->table($itemcatmst_tbl)->insert($insert);
		  	$insertID = $this->db->insertID();

		  	$mst_base_id = $this->create_mst_base_id($insertID,'itemcatmst');
				$this->db->table($itemcatmst_tbl)
						->where('icatgms_id',$insertID)
						->update(['mst_base_id' => $mst_base_id]);

				$this->aicountly_db->table($table)
							->where('impexp_sr_id',$impexp_sr_id)
							->where('imp_id',$imp_id)
							->update(['imp_status' => 1]);

				
				$uploaded[$imp_id] = $name;

			}

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateItemCategoryMaster($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];

		$itemcatmst_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_impitmcatn_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;

		foreach ($result as $key => $value) {

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$name = trim(html_entity_decode($value['impitmcat_name']));
			$alias = trim(html_entity_decode($value['impitmcat_alias']));


			if($name == ''){
				$status = false;
				$errors[$imp_id] = $name.'- Category Name is required';
			}

			if($status && $alias == ''){
				$alias = $name;
			}

			if($status){
				$exists = $this->db->table($itemcatmst_tbl)
					->where('LOWER(item_cat)',strtolower($name))
					->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = $name.'- Category Name already exists';
				}					
			}
	
			if($status){
				$uploaded[$imp_id] = $name;

			}

			}

		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

	function importDayBookVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [9 => 'PYMT', 13 => 'RCPT', 1 => 'CNTR', 5 => 'JRNL', 8 => 'MEMO'];

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim(html_entity_decode($value['imptxnvch_long_narr']));

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}
			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}

			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			// if($status && $bill_no == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			// }

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					$drcr = 'd';
					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total does not match Credit Total';
			}
	
			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				if($long_nrr != ''){  
    			$long_nrr = str_replace(["\r","\n"],'',$long_nrr);
				}

				$insert_data  = array(
          "comp_id"               => $this->company_id,
          "comp_vch_series_id"    => $comp_vch_series_id,
          "comp_vch_no"           => $vno,
          "voucher_type_id"       => $voucher_type_id,
          "voucher_date"          => $date,
          "mat_cent_id"           => 0,
          "vch_subtype_id"        => 0,
          "voucher_tag"           => '',
          "currency_id"           => 1,
        );
        $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);

        if(in_array($voucher_type_id, [9,13,1,5]))
        {
        	foreach ($trans as $k => $val) 
        	{
	        	if($val['acc_type'] == 'acc')
	          {
	            $insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => $val['acc_id'],
	              'master_id_type'      => 'acc'
	            ];

	            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);


	            $insert_data  = [
	              'comp_id'            => $this->company_id,
	              'acc_id'             => $val['acc_id'],
	              'acc_txn_date'       => $date,
	              'acc_txn_amount'     => $val['amount'],                        
	              'acc_txn_drcr'       => $val['drcr'],
	              'acc_txn_narr'       => $val['short_nrr'],
	              'comp_vch_series_no' => $vno,
	              'posted_on'          => date('Y-m-d H:i:s'),
	              'voucher_txn_id'     => $voucher_txn_id,
	              'voucher_type_id'    => $voucher_type_id,
	              'txn_id'             => $txn_id,
	              'acc_bal'            => 0
	            ];  

	            $this->TransactionModel->add_acc_txn_data($insert_data);
	            $this->TransactionModel->update_account_balance($val['acc_id']);

	          
	          	$check_bbb_account = $this->TransactionModel->check_bbb_account($val['acc_id']);
              if($check_bbb_account)
              {
                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($val['acc_id']);
                if($bill_ref_id)
                {

                  $bill_txn_data = [
                    'bills_ref_id'       => $bill_ref_id,
                    'comp_id'           => $this->company_id,
                    'acc_id'             => $val['acc_id'],
                    'voucher_txn_id'     => $voucher_txn_id,
                    'voucher_type_id'    => $voucher_type_id,
                    'comp_vch_series_id' => $series,
                    'bills_txn_date'     => $date,
                    'bills_txn_drcr'     => strtoupper($val['drcr']),
                    'bills_txn_amt'      => $val['amount'],
                    'bills_txn_bal'      => 0,
                    'bills_txn_narr'     => '',
                  ];
                  $this->TransactionModel->add_bill_txn($bill_txn_data);
                }
              }
            

            	$check_cc_account = $this->TransactionModel->check_cc_account($val['acc_id']);
              if($check_cc_account)
              {
                $cc_txn_data = [
                  'cc_id'              => 1,
                  'comp_id'            => $this->company_id,
                  'acc_id'             => $val['acc_id'],
                  'acc_type'           => $val['acc_type'],
                  'voucher_txn_id'     => $voucher_txn_id,
                  'voucher_type_id'    => $voucher_type_id,
                  'comp_vch_series_id' => $series,
                  'cc_txn_date'        => $date,
                  'cc_txn_drcr'        => strtoupper($val['drcr']),
                  'cc_txn_amt'         => $val['amount'],
                  'cc_txn_bal'         => 0,
                  'cc_txn_narr'        => '',
                ];
                $this->TransactionModel->add_cc_txn($cc_txn_data);
              }
            

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }   
	          }

	          if($val['acc_type'] == 'bsd')
	          {
	            $txn_data = array(
	              "comp_id"               => $this->company_id,
	              "comp_vch_series_id"    => $series,
	              "voucher_txn_id"        => $voucher_txn_id,
	              "master_id"             => $val['acc_id'],
	              'master_id_type'        => 'bsd'
	            );
	            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

	            $insert_data  = array(
	              "comp_id"             => $this->company_id,
	              "sundry_txn_date"     => $date,
	              "sundry_txn_amount"   => $val['amount'],
	              "sundry_txn_drcr"     => $val['drcr'],
	              "comp_vch_name"       => $vno, // ?
	              "comp_vch_series_no"  => $vno,
	              "bill_sundry_id"      => $val['acc_id'],
	              "sundry_txn_narr"     => $val['short_nrr'],
	              "sundry_bal"          => 0,
	              "voucher_txn_id"      => $voucher_txn_id, 
	              "voucher_type_id"     => $voucher_type_id,
	              'txn_id'              => $txn_id,
	              'sundry_tag_rate'     => ''
	            );
	            $this->TransactionModel->add_sundry_txn_data($insert_data);

	            $check_cc_bill_sundry = $this->TransactionModel->check_cc_bill_sundry($val['acc_id']);
              if($check_cc_bill_sundry)
              {

                $cc_txn_data = [
                  'cc_id'              => 1,
                  'comp_id'            => $this->company_id,
                  'acc_id'             => $val['acc_id'],
                  'acc_type'           => $val['acc_type'],
                  'voucher_txn_id'     => $voucher_txn_id,
                  'voucher_type_id'    => $voucher_type_id,
                  'comp_vch_series_id' => $series,
                  'cc_txn_date'        => $date,
                  'cc_txn_drcr'        => strtoupper($val['drcr']),
                  'cc_txn_amt'         => $val['amount'],
                  'cc_txn_bal'         => 0,
                  'cc_txn_narr'        => '',
                ];
                $this->TransactionModel->add_cc_txn($cc_txn_data);
              }
            

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }     
	          }
	        }

	        $insert_data = [
		        "comp_id"             => $this->company_id,
		        "comp_vch_series_id"  => $series,
		        "voucher_txn_id"      => $voucher_txn_id,
		        "master_id"           => 0,
		        'master_id_type'      => 'nrr'
		      ];

		      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		      $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_nrr);
        } 

	      if($voucher_type_id == 8)
	      {
	      	foreach ($trans as $k => $val) 
	      	{
	      		$insert_data  = [
	            'comp_id'            => $this->company_id,
	            'acc_id'             => $val['acc_id'],
	            'acc_type'           => $val['acc_type'],
	            'acc_txn_date'       => $date,
	            'acc_txn_amount'     => $val['amount'],
	            'acc_txn_drcr'       => $val['drcr'],
	            'acc_txn_narr'       => $val['short_nrr'],
	            'comp_vch_series_no' => $vno,
	            'voucher_txn_id'     => $voucher_txn_id,
	            'acc_bal'            => 0
	        	];  
	               
	        	$this->TransactionModel->add_acc_memo_data($insert_data);
	      	}

	      	$insert_data = [
					    "comp_id"             => $this->company_id,
					    "comp_vch_series_id"  => $series,
					    "voucher_txn_id"      => $voucher_txn_id,
					    "master_id"           => 0,
					    'master_id_type'      => 'nrr'
					];

					$txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
					$this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_nrr);
	      }
				

				$this->aicountly_db->table($table)
							->where('impexp_sr_id',$impexp_sr_id)
							->where('imp_id',$imp_id)
							->update(['imp_status' => 1]);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;

			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateDayBookVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [9 => 'PYMT', 13 => 'RCPT', 1 => 'CNTR', 5 => 'JRNL', 8 => 'MEMO'];

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){
			
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim(html_entity_decode($value['imptxnvch_long_narr']));

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}
			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}

			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			// if($status && $bill_no == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			// }

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					$drcr = 'd';
					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total does not match Credit Total';
			}
	
			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;

			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

	function importSaleNonItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [18 => 'SALE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list();

		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
		
		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_side_table = 'aicountly_impoutsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impoutsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';
			$value['imptxnvch_supplytype'] = $temp2['impout_supply_type'] ?? '';
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim(html_entity_decode($value['imptxnvch_long_narr']));
			$pos = trim($value['imptxnvch_pos']);
			$country_code = trim($value['imptxnvch_country_code']);
			$voucher_supplytype = trim($value['imptxnvch_supplytype']);
			
			$supplyTypes = [
					3  => ['EXPWOP', 3],
					5  => ['SEZWOP', 5],
					14 => ['NILSPLY', 14],
					15 => ['EXMSPLY', 15],
					16 => ['NONGSTS', 16],
					2  => ['EXPWP', 2],
					4  => ['SEZWP', 4],
					13 => ['DE', 13]
				];
			   // Trim the voucher_supplytype value
				$voucher_supplytype = trim($voucher_supplytype);

				// Check if the supply type exists in the array, otherwise use the default 'B2B'
				if (isset($supplyTypes[$voucher_supplytype])) {
					list($outsup_inv_type, $ewb_supply_type) = $supplyTypes[$voucher_supplytype];
				} else {
					$outsup_inv_type = 'B2B';
					$ewb_supply_type = 1;
				}

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}
            $fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}
            if($status && $voucher_supplytype == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Supply Type is missing.';
			} 
			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }
			 if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstroutsup_tbl)
						->where('LOWER(outsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }

			if($status && $country_code == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			}

			if($status && $country_code != ''){
		
				if(!in_array($country_code,$country_code_list)){
					$status = false;
					$country_code_str=implode(", ",$country_code_list);

					$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
				}
			}

			// if($status && $pos == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Voucher POS is missing.';
			// }
			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);
					$is_party_acc = trim($value['imptxnvch_pary_acc']); // 0 or 1

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'SALE')
						$drcr = 'c';
					else
						$drcr = 'd';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
						'is_party_acc' => $is_party_acc
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total does not match Credit Total';
			}

			$tax_summary = [];
			if($status) // check tax
			{

				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){

						if($val['acc_type'] == 'acc' && $val['is_party_acc']=='1'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_parent_id != 0){ // Primary Account
								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Account '.$val['acc_name'].' can not come under Owner"s Fund';
								}
							}
							else{
								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								$acc_grp_parent_id = $acctgroupn['acc_grp_parent_id'];

								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Owners Fund';
								}

								if($status){
									if($acctgroupn['under_main_grp_id'] == 0){
										if(in_array($acc_grp_id, [3,15,16,22])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Long Term Borrowing, Short Term Borrowing, Trade Payable, Trade Receivable';
										}
									}
									else{
										if(in_array($acctgroupn['under_main_grp_id'], [3,15,16,22])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Long Term Borrowing, Short Term Borrowing, Trade Payable, Trade Receivable';
										}
									}
								}
							}
						}

						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd'){
						if($val['acc_type'] == 'acc' && $val['is_party_acc']=='1'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}

							if($status){

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if($acctgroupn['under_main_grp_id'] == 0){
									if(!in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(!in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
							}

						}
						if($val['acc_type'] == 'bsd'){
							$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' can not be Bill Sundry';
						}
					}
				}

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					$bo_state_code = intval($bo_state_code);
					// calculate tax
					$account_summary=array();
					foreach ($trans as $k => $val){

						if($val['drcr'] == 'c'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;

					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
								$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								}
							
							$tax_cat_id = $rates['tax_cat_id'];
							//tax  applicable
								if(!in_array($tax_cat_id, [16,14,8,15,18]) && !in_array(trim($voucher_supplytype), [3,5,14,15,16])){

							
							$igst_rate = parseAmount($rates['igst_rate']);
						    $cess_rate = parseAmount($rates['cess_rate']);
						    $cess_basis = $rates['cess_basis'];

					    	if($pos != $bo_state_code){
					    		// 2 taxes IGST n CESS
					    		$igst = ($val['amount'] * $igst_rate)/100;
	                $igst = parseAmount($igst);

	                $tax['IGST']['status'] = 1;
	                $tax['IGST']['value'] += $igst;

	                $summary['acc_igst_rate'] = $igst_rate;
	                $summary['acc_igst'] = $igst;
	                $total_tax += $igst;

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate)/100;
	                  $cess = parseAmount($cess);

	                  $tax['CESS']['status'] = 1;
	                  $tax['CESS']['value'] += $cess;

	                  $summary['acc_cess_basis'] = $cess_basis;
	                  $summary['acc_cess_rate'] = $cess_rate;
	                  $summary['acc_cess'] = $cess;
	                  $total_tax += $cess;
	                }
	                else{
	                  $err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
	                  $tax['CESS']['status'] = 2;
	                  $tax['CESS']['error'] = $err;
	                 }
					    	}
					    	else{
					    		// 3 taxes CESS,CGST, SGST or UT-TAX
					    		$cgst_rate = parseAmount($igst_rate / 2);
					    		$cgst = ($val['amount'] * $cgst_rate) / 100;
	                $cgst = parseAmount($cgst);

	                $tax['CGST']['status'] = 1;
	                $tax['CGST']['value'] += $cgst;

	                $summary['acc_cgst_rate'] = $cgst_rate;
	                $summary['acc_cgst'] = $cgst;
	                $total_tax += $cgst;

	                $ut_arr = [35,4,26,25,31,38,34,97];
	                if(!in_array($pos, $ut_arr)){
	               
	                  $tax['SGST']['status'] = 1;
	                  $tax['SGST']['value'] += $cgst;

	                  $summary['acc_sgst_rate'] = $cgst_rate;
	                	$summary['acc_sgst'] = $cgst;
	                	$total_tax += $cgst;
	                }
	                else{
	                  $ut_tax = $cgst;
	                  $tax['UT-TAX']['status'] = 1;
	                  $tax['UT-TAX']['value'] += $ut_tax;

	                  $summary['acc_sgst_rate'] = $cgst_rate;
	                	$summary['acc_sgst'] = $cgst;
	                	$total_tax += $cgst;
	                }

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate) / 100;
	                  $cess = parseAmount($cess);

	                  $tax['CESS']['status'] = 1;
	                  $tax['CESS']['value'] += $cess;

	                  $summary['acc_cess_basis'] = $cess_basis;
	                  $summary['acc_cess_rate'] = $cess_rate;
	                	$summary['acc_cess'] = $cess;
	                	$total_tax += $cess;
	                }
	                else{
	                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
	                  $tax['CESS']['status'] = 2;
	                  $tax['CESS']['error'] = $err;
	                }
					    	}

					    	$summary['total_tax'] = $total_tax;
					    	$account_summary[] = $summary;
							
						   }
					    } // else
					  }
					}
	
					if($status) // check for errors
					{
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									if($index != ''){
										$obj = $tax_accounts[$index];
				            if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
				            {
				              $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
				            }
									}
									else{
										$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									}
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}
	
			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				if($long_nrr != ''){  
    			$long_nrr = str_replace(["\r","\n"],'',$long_nrr);
				}

				$insert_data  = array(
          "comp_id"               => $this->company_id,
          "comp_vch_series_id"    => $comp_vch_series_id,
          "comp_vch_no"           => $vno,
          "voucher_type_id"       => $voucher_type_id,
          "voucher_date"          => $date,
          "mat_cent_id"           => 0,
          "vch_subtype_id"        => 0,
          "voucher_tag"           => '',
          "currency_id"           => 1,
        );
        $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);

        if(in_array($voucher_type_id, [18]))
        {
			
			
        	$isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$comp_vch_series_id);	
			if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				  $bill_no_counter    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,0);
				  $consodata          = array("vch_bill_ref_no"=>$bill_no_counter);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
				}
			
		// save into gstroutsup table
				if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,$vch_bill_ref_no);
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$bill_no_format,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$outsup_inv_type,"outsup_eco"=>0);
				  $this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}
				else{
				$gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$bill_no,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$outsup_inv_type,"outsup_eco"=>0);
				$this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}
			

			//Save Eway Tables Data
				$ewbmstreqn_insert_data = array("ewb_supply_type"=>$ewb_supply_type,"ewb_sub_supply_desc"=>"",
				                               "ewb_doc_type"=>"","voucher_txn_id"=>$voucher_txn_id,"ewb_txn_type"=>1,
				                               "bo_id"=>$this->bo_id);
				$ewb_id = $this->TransactionModel->add_ewbmstreqn_data($ewbmstreqn_insert_data);	
				
				// Save Into ewbmstcons table
				$ewbmstcons_insert_data = array("conso_ewb_no"=>$bill_no,"conso_ewb_date"=>$date,
				                               "ewb_id"=>$ewb_id,"ewb_no"=>0);
				$conso_ewb_id = $this->TransactionModel->add_ewbmstcons_data($ewbmstcons_insert_data);
				
        	foreach ($trans as $k => $val) 
        	{
	        	if($val['acc_type'] == 'acc')
	          {
	            $insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => $val['acc_id'],
	              'master_id_type'      => 'acc'
	            ];

	            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);


	            $insert_data  = [
	              'comp_id'            => $this->company_id,
	              'acc_id'             => $val['acc_id'],
	              'acc_txn_date'       => $date,
	              'acc_txn_amount'     => $val['amount'],                        
	              'acc_txn_drcr'       => $val['drcr'],
	              'acc_txn_narr'       => $val['short_nrr'],
	              'comp_vch_series_no' => $vno,
	              'posted_on'          => date('Y-m-d H:i:s'),
	              'voucher_txn_id'     => $voucher_txn_id,
	              'voucher_type_id'    => $voucher_type_id,
	              'txn_id'             => $txn_id,
	              'acc_bal'            => 0
	            ];  

	            $this->TransactionModel->add_acc_txn_data($insert_data);
	            $this->TransactionModel->update_account_balance($val['acc_id']);

	          
	          	$check_bbb_account = $this->TransactionModel->check_bbb_account($val['acc_id']);
              if($check_bbb_account)
              {
                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($val['acc_id']);
                if($bill_ref_id)
                {

                  $bill_txn_data = [
                    'bills_ref_id'       => $bill_ref_id,
                    'comp_id'           => $this->company_id,
                    'acc_id'             => $val['acc_id'],
                    'voucher_txn_id'     => $voucher_txn_id,
                    'voucher_type_id'    => $voucher_type_id,
                    'comp_vch_series_id' => $series,
                    'bills_txn_date'     => $date,
                    'bills_txn_drcr'     => strtoupper($val['drcr']),
                    'bills_txn_amt'      => $val['amount'],
                    'bills_txn_bal'      => 0,
                    'bills_txn_narr'     => '',
                  ];
                  $this->TransactionModel->add_bill_txn($bill_txn_data);
                }
              }

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }   
	          }

	          if($val['acc_type'] == 'bsd')
	          {
				  if($val['amount'] >0){
	            $txn_data = array(
	              "comp_id"               => $this->company_id,
	              "comp_vch_series_id"    => $series,
	              "voucher_txn_id"        => $voucher_txn_id,
	              "master_id"             => $val['acc_id'],
	              'master_id_type'        => 'bsd'
	            );
	            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
                
	            $insert_data  = array(
	              "comp_id"             => $this->company_id,
	              "sundry_txn_date"     => $date,
	              "sundry_txn_amount"   => $val['amount'],
	              "sundry_txn_drcr"     => $val['drcr'],
	              "comp_vch_name"       => $vno, // ?
	              "comp_vch_series_no"  => $vno,
	              "bill_sundry_id"      => $val['acc_id'],
	              "sundry_txn_narr"     => $val['short_nrr'],
	              "sundry_bal"          => 0,
	              "voucher_txn_id"      => $voucher_txn_id, 
	              "voucher_type_id"     => $voucher_type_id,
	              'txn_id'              => $txn_id,
	              'sundry_tag_rate'     => ''
	            );
	            $this->TransactionModel->add_sundry_txn_data($insert_data);
				}
	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }     
	          }
	        }

	        foreach ($account_summary as $k => $val) {
	        	$insert_data = [
							"comp_id"             => $this->company_id,
							"comp_vch_series_id"  => $series,
							"voucher_txn_id"      => $voucher_txn_id,
							"master_id"           => $val['acc_id'],
							'master_id_type'      => 'tax'
					 	];
						$txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
						$item_tax_info = $this->TransactionModel->get_account_taxinfo($val['acc_id']);
						$item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($val['acc_id'],$item_tax_info['tax_cat_id']);		
					    
						if(trim($voucher_supplytype)==13)
						  $item_tax_short_code ='DEEMEXP';
					    if(trim($voucher_supplytype)==2 || trim($voucher_supplytype)==3)
						  $item_tax_short_code ='ZERSPLY';
					    if(trim($voucher_supplytype)==4 || trim($voucher_supplytype)==5)
						  $item_tax_short_code ='SEZSPLY';
						if(trim($voucher_supplytype)==14)
						  $item_tax_short_code ='NILSPLY';
						if(trim($voucher_supplytype)==15)
						  $item_tax_short_code ='EXMSPLY';
						if(trim($voucher_supplytype)==16)
						  $item_tax_short_code ='NONGSTS';

						$insert_data   = [
							'acc_id'               => $val['acc_id'],
							'acc_igst_rate'        => $val['acc_igst_rate'] ?? 0,
							'acc_igst'             => $val['acc_igst'] ?? 0,
							'acc_cgst_rate'        => $val['acc_cgst_rate'] ?? 0,
							'acc_cgst'             => $val['acc_cgst'] ?? 0,
							'acc_sgst_rate'        => $val['acc_sgst_rate'] ?? 0,
							'acc_sgst'             => $val['acc_sgst'] ?? 0,
							'acc_cess_rate'        => 0,
							'acc_nonadv_cess_rate' => 0,
							'acc_nonadv_cess'      => 0,
							'acc_hsn_sac'          => $val['acc_hsn_sac'] ?? '',
							'vch_txn_id'           => $voucher_txn_id,
							'txn_id'               => $txn_id,
							'acc_type'             => $val['acc_type'],
							'taxable_amt'          => $val['taxable_amt'],
							'total_tax'            => $val['total_tax'],
							'acc_cess_basis'       => 1,
							'cmp_tax_short_code'   => $item_tax_short_code,
							'acc_txn_date'         => date('Y-m-d',strtotime($date))
						];
		      	$this->TransactionModel->add_taxsummary_data($insert_data);
	        }

	        $insert_data = [
		        "comp_id"             => $this->company_id,
		        "comp_vch_series_id"  => $series,
		        "voucher_txn_id"      => $voucher_txn_id,
		        "master_id"           => 0,
		        'master_id_type'      => 'nrr'
		      ];

		      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		      $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_nrr);


        } 
				

				$this->aicountly_db->table($table)
							->where('impexp_sr_id',$impexp_sr_id)
							->where('imp_id',$imp_id)
							->update(['imp_status' => 1]);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateSaleNonItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [18 => 'SALE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list();

		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_side_table = 'aicountly_impoutsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impoutsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';
			$value['imptxnvch_supplytype'] = $temp2['impout_supply_type'] ?? '';
			
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim(html_entity_decode($value['imptxnvch_long_narr']));
			$pos = trim($value['imptxnvch_pos']);
			$country_code = trim($value['imptxnvch_country_code']);
			$voucher_supplytype = trim($value['imptxnvch_supplytype']);

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}
            $fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}
			if($status && $voucher_supplytype == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Supply Type is missing.';
			}
			
			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			 if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }
			 if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstroutsup_tbl)
						->where('LOWER(outsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }

			if($status && $country_code == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			}

			if($status && $country_code != ''){
		
				if(!in_array($country_code,$country_code_list)){
					$status = false;
					$country_code_str=implode(", ",$country_code_list);

					$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
				}
			}

			// if($status && $pos == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Voucher POS is missing.';
			// }
			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'SALE')
						$drcr = 'c';
					else
						$drcr = 'd';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total does not match Credit Total';
			}

			$tax_summary = [];
			if($status) // check tax
			{

				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){

						if($val['acc_type'] == 'acc'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_parent_id != 0){ // Primary Account
								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Account '.$val['acc_name'].' can not come under Owner"s Fund';
								}
							}
							else{
								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								$acc_grp_parent_id = $acctgroupn['acc_grp_parent_id'];

								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Owners Fund';
								}

								if($status){
									if($acctgroupn['under_main_grp_id'] == 0){
										if(in_array($acc_grp_id, [3,15,16,22])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Long Term Borrowing, Short Term Borrowing, Trade Payable, Trade Receivable';
										}
									}
									else{
										if(in_array($acctgroupn['under_main_grp_id'], [3,15,16,22])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Long Term Borrowing, Short Term Borrowing, Trade Payable, Trade Receivable';
										}
									}
								}
							}
						}

						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd'){
						if($val['acc_type'] == 'acc'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}

							if($status){

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if($acctgroupn['under_main_grp_id'] == 0){
									if(!in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(!in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
							}

						}
						if($val['acc_type'] == 'bsd'){
							$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' can not be Bill Sundry';
						}
					}
				}

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					$bo_state_code = intval($bo_state_code);
					

					// calculate tax

					foreach ($trans as $k => $val){

						if($val['drcr'] == 'c'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;

					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
								$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								}
								$tax_cat_id = $rates['tax_cat_id'];
								
								if(!in_array($tax_cat_id, [16,14,8,15,18]) && !in_array(trim($voucher_supplytype), [3,5,14,15,16]) ){
								
								$igst_rate = parseAmount($rates['igst_rate']);
						    $cess_rate = parseAmount($rates['cess_rate']);
						    $cess_basis = $rates['cess_basis'];

					    	if($pos != $bo_state_code){
					    		// 2 taxes IGST n CESS
					    		$igst = ($val['amount'] * $igst_rate)/100;
	                $igst = parseAmount($igst);

	                $tax['IGST']['status'] = 1;
	                $tax['IGST']['value'] += $igst;

	                $summary['acc_igst_rate'] = $igst_rate;
	                $summary['acc_igst'] = $igst;
	                $total_tax += $igst;

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate)/100;
	                  $cess = parseAmount($cess);

	                  $tax['CESS']['status'] = 1;
	                  $tax['CESS']['value'] += $cess;

	                  $summary['acc_cess_basis'] = $cess_basis;
	                  $summary['acc_cess_rate'] = $cess_rate;
	                  $summary['acc_cess'] = $cess;
	                  $total_tax += $cess;
	                }
	                else{
	                  $err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
	                  $tax['CESS']['status'] = 2;
	                  $tax['CESS']['error'] = $err;
	                 }
					    	}
					    	else{
					    		// 3 taxes CESS,CGST, SGST or UT-TAX
					    		$cgst_rate = parseAmount($igst_rate / 2);
					    		$cgst = ($val['amount'] * $cgst_rate) / 100;
	                $cgst = parseAmount($cgst);

	                $tax['CGST']['status'] = 1;
	                $tax['CGST']['value'] += $cgst;

	                $summary['acc_cgst_rate'] = $cgst_rate;
	                $summary['acc_cgst'] = $cgst;
	                $total_tax += $cgst;

	                $ut_arr = [35,4,26,25,31,38,34,97];
	                if(!in_array($pos, $ut_arr)){
	               
	                  $tax['SGST']['status'] = 1;
	                  $tax['SGST']['value'] += $cgst;

	                  $summary['acc_sgst_rate'] = $cgst_rate;
	                	$summary['acc_sgst'] = $cgst;
	                	$total_tax += $cgst;
	                }
	                else{
	                  $ut_tax = $cgst;
	                  $tax['UT-TAX']['status'] = 1;
	                  $tax['UT-TAX']['value'] += $ut_tax;

	                  $summary['acc_sgst_rate'] = $cgst_rate;
	                	$summary['acc_sgst'] = $cgst;
	                	$total_tax += $cgst;
	                }

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate) / 100;
	                  $cess = parseAmount($cess);

	                  $tax['CESS']['status'] = 1;
	                  $tax['CESS']['value'] += $cess;

	                  $summary['acc_cess_basis'] = $cess_basis;
	                  $summary['acc_cess_rate'] = $cess_rate;
	                	$summary['acc_cess'] = $cess;
	                	$total_tax += $cess;
	                }
	                else{
	                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
	                  $tax['CESS']['status'] = 2;
	                  $tax['CESS']['error'] = $err;
	                }
					    	}

					    	$summary['total_tax'] = $total_tax;
					    	$account_summary[] = $summary;
							
						   }
					    } // else
					  }
					}
	
					if($status) // check for errors
					{
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									if($index != ''){
										$obj = $tax_accounts[$index];
				            if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
				            {
				              $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
				            }
									}
									else{
										$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									}
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}
	
			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function importPurchaseNonItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [11 => 'PURCHASE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list('purchase');


		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
        $gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_side_table = 'aicountly_impinwsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impinwsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
			$value['imptxnvch_supplytype'] = $temp2['impinw_supply_type'] ?? '';
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim(html_entity_decode($value['imptxnvch_long_narr']));
			$pos = trim($value['imptxnvch_pos']);
			$country_code = trim($value['imptxnvch_country_code']);
			$voucher_supplytype = trim($value['imptxnvch_supplytype']);
			$supplyTypes = [
					3  => ['EXPWOP', 3],
					5  => ['SEZWOP', 5],
					14 => ['NILSPLY', 14],
					15 => ['EXMSPLY', 15],
					16 => ['NONGSTS', 16],
					2  => ['EXPWP', 2],
					4  => ['SEZWP', 4],
					13 => ['DE', 13]
				];

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;
			// Check if the supply type exists in the array, otherwise use the default 'B2B'
				if (isset($supplyTypes[$voucher_supplytype])) {
					list($outsup_inv_type, $ewb_supply_type) = $supplyTypes[$voucher_supplytype];
				} else {
					$outsup_inv_type = 'B2B';
					$ewb_supply_type = 1;
				}
				
			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}

			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}
			if($status && $voucher_supplytype == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Supply Type is missing.';
			}
			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstrinwsup_tbl)
						->where('LOWER(inwsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }	
			 if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }

			if($status && $country_code == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			}

			if($status && $country_code != ''){
		
				if(!in_array($country_code,$country_code_list)){
					$status = false;
					$country_code_str=implode(", ",$country_code_list);

					$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
				}
			}

			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);
					$imptxnvch_mst_type = trim($value['imptxnvch_mst_type']);
					$is_party_acc = trim($value['imptxnvch_pary_acc']); // 0 or 1

					$acc_id = 0;
					$acc_type = ''; 

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'PURCHASE')
						$drcr = 'd';
					else
						$drcr = 'c';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
						'is_party_acc' => $is_party_acc
					];
				}
			}

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			/* if($status && $debit_total != $credit_total){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total does not match Credit Total';
			} */

			$tax_summary = [];
			if($status) // check tax
			{  
				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){
                        if($val['acc_type'] == 'bsd'){
							$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not be Bill Sundry';
						}
						if($val['acc_type'] == 'acc' && $val['is_party_acc']=='1'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_parent_id != 0){ // Primary Account
								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Account '.$val['acc_name'].' can not come under Owner"s Fund';
								}
							}
							else{
								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								$acc_grp_parent_id = $acctgroupn['acc_grp_parent_id'];

								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Owners Fund';
								}

								if($status){
									if($acctgroupn['under_main_grp_id'] == 0){
										if(!in_array($acc_grp_id, [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
										}
									}
									else{
										if(!in_array($acctgroupn['under_main_grp_id'], [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
										}
									}
								}
							}
						}
						if($val['acc_type'] == 'acc' && $val['is_party_acc']=='0'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							
							
								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								$acc_grp_parent_id = $acctgroupn['acc_grp_parent_id'];

								if($status){
									if($acctgroupn['under_main_grp_id'] == 0){
										if(in_array($acc_grp_id, [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should not come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
										}
									}
									else{
										if(in_array($acctgroupn['under_main_grp_id'], [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should not come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
										}
									}
								}
							
						}

						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd' && $val['is_party_acc']=="1"){
						if($val['acc_type'] == 'acc'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}

							if($status){

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if(isset($acctgroupn) && $acctgroupn['under_main_grp_id'] == 0){
									if(isset($acc_grp_id) && in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should not come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(isset($acctgroupn) && in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should not come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
							}

						}
						
					}
				}

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					
					
					// calculate tax
					$account_summary=array();
					foreach ($trans as $k => $val){

						if($val['drcr'] == 'c'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;
					    	/* $summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
							$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount']; */

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    	//	$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		//$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								 if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								} 
								
								 $tax_cat_id = $rates['tax_cat_id'];
								 
						
					      	    $igst_rate = parseAmount($rates['igst_rate']);
						        $cess_rate = parseAmount($rates['cess_rate']);
						         $cess_basis = $rates['cess_basis'];
													

					    	if($pos != $bo_state_code){
					    		// 2 taxes IGST n CESS
					    		$igst = ($val['amount'] * $igst_rate)/100;
								$igst = parseAmount($igst);

								$tax['IGST']['status'] = 1;
								$tax['IGST']['value'] += $igst;

								//$summary['acc_igst_rate'] = $igst_rate;
								//$summary['acc_igst'] = $igst;
								$total_tax += $igst;

							if($cess_basis == 1){
							  $cess = ($val['amount'] * $cess_rate)/100;
							  $cess = parseAmount($cess);

							  $tax['CESS']['status'] = 1;
							  $tax['CESS']['value'] += $cess;

							  //$summary['acc_cess_basis'] = $cess_basis;
							 // $summary['acc_cess_rate'] = $cess_rate;
							  //$summary['acc_cess'] = $cess;
							  $total_tax += $cess;
							}
							/* else{
							  $err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
							  $tax['CESS']['status'] = 2;
							  $tax['CESS']['error'] = $err;
							 } */
					    	}
					    	else{
					    		// 3 taxes CESS,CGST, SGST or UT-TAX
					    		$cgst_rate = parseAmount($igst_rate / 2);
					    		$cgst = ($val['amount'] * $cgst_rate) / 100;
								$cgst = parseAmount($cgst);

								$tax['CGST']['status'] = 1;
								$tax['CGST']['value'] += $cgst;

								//$summary['acc_cgst_rate'] = $cgst_rate;
								//$summary['acc_cgst'] = $cgst;
								$total_tax += $cgst;

								$ut_arr = [35,4,26,25,31,38,34,97];
							if(!in_array($pos, $ut_arr)){
						   
							  $tax['SGST']['status'] = 1;
							  $tax['SGST']['value'] += $cgst;

							  //$summary['acc_sgst_rate'] = $cgst_rate;
								//$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}
							else{
							  $ut_tax = $cgst;
							  $tax['UT-TAX']['status'] = 1;
							  $tax['UT-TAX']['value'] += $ut_tax;

							  //$summary['acc_sgst_rate'] = $cgst_rate;
								//$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate) / 100;
	                  $cess = parseAmount($cess);

	                  $tax['CESS']['status'] = 1;
	                  $tax['CESS']['value'] += $cess;

	                  //$summary['acc_cess_basis'] = $cess_basis;
	                  //$summary['acc_cess_rate'] = $cess_rate;
	                	//$summary['acc_cess'] = $cess;
	                	$total_tax += $cess;
	                }
	                /* else{
	                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
	                  $tax['CESS']['status'] = 2;
	                  $tax['CESS']['error'] = $err;
	                } */
					    	}

					    	//$summary['total_tax'] = $total_tax;
					    	
					    } // else
					  }
				  
				  
				    if($val['drcr'] == 'd'){
						$total_tax = 0;
					if(!in_array($tax_cat_id, [16,14,8,15,18]) && !in_array(trim($voucher_supplytype), [3,5,14,15,16])){

						 if($val['acc_type']=='acc'){
					    	
					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
							$summary['acc_type'] = 'bsd';
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
							
								$igst_rate = parseAmount($rates['igst_rate']);
						        $cess_rate = parseAmount($rates['cess_rate']);
						         $cess_basis = $rates['cess_basis'];

					    	if($pos != $bo_state_code){
					    		// 2 taxes IGST n CESS
					    		$igst = ($val['amount'] * $igst_rate)/100;
								$igst = parseAmount($igst);


								$summary['acc_igst_rate'] = $igst_rate;
								$summary['acc_igst'] = $igst;
								$total_tax += $igst;

							if($cess_basis == 1){
							  $cess = ($val['amount'] * $cess_rate)/100;
							  $cess = parseAmount($cess);

							  $summary['acc_cess_basis'] = $cess_basis;
							  $summary['acc_cess_rate'] = $cess_rate;
							  $summary['acc_cess'] = $cess;
							  $total_tax += $cess;
							}
							
					    	}
					    	else{
					    		// 3 taxes CESS,CGST, SGST or UT-TAX
					    		$cgst_rate = parseAmount($igst_rate / 2);
					    		$cgst = ($val['amount'] * $cgst_rate) / 100;
								$cgst = parseAmount($cgst);


								$summary['acc_cgst_rate'] = $cgst_rate;
								$summary['acc_cgst'] = $cgst;
								$total_tax += $cgst;

								$ut_arr = [35,4,26,25,31,38,34,97];
							if(!in_array($pos, $ut_arr)){
							  $summary['acc_sgst_rate'] = $cgst_rate;
								$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}
							else{
							  $ut_tax = $cgst;
							  $tax['UT-TAX']['status'] = 1;
							  $tax['UT-TAX']['value'] += $ut_tax;

							  $summary['acc_sgst_rate'] = $cgst_rate;
								$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate) / 100;
	                  $cess = parseAmount($cess);


	                  $summary['acc_cess_basis'] = $cess_basis;
	                  $summary['acc_cess_rate'] = $cess_rate;
	                	$summary['acc_cess'] = $cess;
	                	$total_tax += $cess;
	                }
	               
					    	}

					    	$summary['total_tax'] = $total_tax;
					    	$account_summary[] = $summary;
					     }
					} 
						 
					   }
					   
					   
					}
	
	
					if($status) // check for errors
					{ 
					
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									 if($index != ''){
										$obj = $tax_accounts[$index];
										
											if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
											{
											  $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
											}
									}
									else{
										//$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									} 
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}
	
	       SaveErrorLog("purchasse w/o item sale single row status ==> ".$status);
		   SaveErrorLog("errors ==> ".json_encode($errors));
		   
			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				if($long_nrr != ''){  
    			$long_nrr = str_replace(["\r","\n"],'',$long_nrr);
				}

				$insert_data  = array(
				  "comp_id"               => $this->company_id,
				  "comp_vch_series_id"    => $comp_vch_series_id,
				  "comp_vch_no"           => $vno,
				  "voucher_type_id"       => $voucher_type_id,
				  "voucher_date"          => $date,
				  "mat_cent_id"           => 0,
				  "vch_subtype_id"        => 0,
				  "voucher_tag"           => '',
				  "currency_id"           => 1,
				);
        $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);

        if(in_array($voucher_type_id, [11]))
        {
			$isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$comp_vch_series_id);	
			if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				  $bill_no_counter    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,0);
				  $consodata          = array("vch_bill_ref_no"=>$bill_no_counter);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
				}
			
		if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,$vch_bill_ref_no);
				  $gstrinwsup_insert_data = array("inwsup_rev_chg"=>0,"inwsup_bill_ref_no"=>$bill_no_format,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$outsup_inv_type,"inwsup_eco"=>0);
				  $this->TransactionModel->add_gstrinwsup_data($gstrinwsup_insert_data);
				}
				else{
				$gstrinwsup_insert_data = array("inwsup_rev_chg"=>0,"inwsup_bill_ref_no"=>$bill_no,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$outsup_inv_type,"inwsup_eco"=>0);
				$this->TransactionModel->add_gstrinwsup_data($gstrinwsup_insert_data);
				}
				
        	
        	foreach ($trans as $k => $val) 
        	{
	        	if($val['acc_type'] == 'acc')
	          {
	            $insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => $val['acc_id'],
	              'master_id_type'      => 'acc'
	            ];

	            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);


	            $insert_data  = [
	              'comp_id'            => $this->company_id,
	              'acc_id'             => $val['acc_id'],
	              'acc_txn_date'       => $date,
	              'acc_txn_amount'     => $val['amount'],                        
	              'acc_txn_drcr'       => $val['drcr'],
	              'acc_txn_narr'       => $val['short_nrr'],
	              'comp_vch_series_no' => $vno,
	              'posted_on'          => date('Y-m-d H:i:s'),
	              'voucher_txn_id'     => $voucher_txn_id,
	              'voucher_type_id'    => $voucher_type_id,
	              'txn_id'             => $txn_id,
	              'acc_bal'            => 0
	            ];  

	            $this->TransactionModel->add_acc_txn_data($insert_data);
	            $this->TransactionModel->update_account_balance($val['acc_id']);

	          
	          	$check_bbb_account = $this->TransactionModel->check_bbb_account($val['acc_id']);
              if($check_bbb_account)
              {
                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($val['acc_id']);
                if($bill_ref_id)
                {

                  $bill_txn_data = [
                    'bills_ref_id'       => $bill_ref_id,
                    'comp_id'           => $this->company_id,
                    'acc_id'             => $val['acc_id'],
                    'voucher_txn_id'     => $voucher_txn_id,
                    'voucher_type_id'    => $voucher_type_id,
                    'comp_vch_series_id' => $series,
                    'bills_txn_date'     => $date,
                    'bills_txn_drcr'     => strtoupper($val['drcr']),
                    'bills_txn_amt'      => $val['amount'],
                    'bills_txn_bal'      => 0,
                    'bills_txn_narr'     => '',
                  ];
                  $this->TransactionModel->add_bill_txn($bill_txn_data);
                }
              }

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }   
	          }

	          if($val['acc_type'] == 'bsd')
	          {
	            $txn_data = array(
	              "comp_id"               => $this->company_id,
	              "comp_vch_series_id"    => $series,
	              "voucher_txn_id"        => $voucher_txn_id,
	              "master_id"             => $val['acc_id'],
	              'master_id_type'        => 'bsd'
	            );
	            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

	            $insert_data  = array(
	              "comp_id"             => $this->company_id,
	              "sundry_txn_date"     => $date,
	              "sundry_txn_amount"   => $val['amount'],
	              "sundry_txn_drcr"     => $val['drcr'],
	              "comp_vch_name"       => $vno, // ?
	              "comp_vch_series_no"  => $vno,
	              "bill_sundry_id"      => $val['acc_id'],
	              "sundry_txn_narr"     => $val['short_nrr'],
	              "sundry_bal"          => 0,
	              "voucher_txn_id"      => $voucher_txn_id, 
	              "voucher_type_id"     => $voucher_type_id,
	              'txn_id'              => $txn_id,
	              'sundry_tag_rate'     => ''
	            );
	            $this->TransactionModel->add_sundry_txn_data($insert_data);

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }     
	          }
	        }

	        foreach ($account_summary as $k => $val) {
	        	$insert_data = [
							"comp_id"             => $this->company_id,
							"comp_vch_series_id"  => $series,
							"voucher_txn_id"      => $voucher_txn_id,
							"master_id"           => $val['acc_id'],
							'master_id_type'      => 'tax'
					 	    ];
						$txn_id        = $this->TransactionModel->add_comp_txn_data($insert_data);
						$item_tax_info = $this->TransactionModel->get_account_taxinfo($val['acc_id']);
						$item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($val['acc_id'],$item_tax_info['tax_cat_id']);		
					    if(trim($voucher_supplytype)==13)
						  $item_tax_short_code ='DEEMEXP';
					    if(trim($voucher_supplytype)==2 || trim($voucher_supplytype)==3)
						  $item_tax_short_code ='ZERSPLY';
					    if(trim($voucher_supplytype)==4 || trim($voucher_supplytype)==5)
						  $item_tax_short_code ='SEZSPLY';
						if(trim($voucher_supplytype)==14)
						  $item_tax_short_code ='NILSPLY';
						if(trim($voucher_supplytype)==15)
						  $item_tax_short_code ='EXMSPLY';
						if(trim($voucher_supplytype)==16)
						  $item_tax_short_code ='NONGSTS';
						$insert_data   = [
							'acc_id'               => $val['acc_id'],
							'acc_igst_rate'        => $val['acc_igst_rate'] ?? 0,
							'acc_igst'             => $val['acc_igst'] ?? 0,
							'acc_cgst_rate'        => $val['acc_cgst_rate'] ?? 0,
							'acc_cgst'             => $val['acc_cgst'] ?? 0,
							'acc_sgst_rate'        => $val['acc_sgst_rate'] ?? 0,
							'acc_sgst'             => $val['acc_sgst'] ?? 0,
							'acc_cess_rate'        => 0,
							'acc_nonadv_cess_rate' => 0,
							'acc_nonadv_cess'      => 0,
							'acc_hsn_sac'          => $val['acc_hsn_sac'] ?? '',
							'vch_txn_id'           => $voucher_txn_id,
							'txn_id'               => $txn_id,
							'acc_type'             => $val['acc_type'],
							'taxable_amt'          => $val['taxable_amt'],
							'total_tax'            => $val['total_tax'],
							'cmp_tax_short_code'   => $item_tax_short_code,
							'acc_txn_date'         => date('Y-m-d',strtotime($date))
						];
		      	$this->TransactionModel->add_taxsummary_data($insert_data);
	        }

	        $insert_data = [
		        "comp_id"             => $this->company_id,
		        "comp_vch_series_id"  => $series,
		        "voucher_txn_id"      => $voucher_txn_id,
		        "master_id"           => 0,
		        'master_id_type'      => 'nrr'
		      ];

		      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		      $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_nrr);


        } 
				

				$this->aicountly_db->table($table)
							->where('impexp_sr_id',$impexp_sr_id)
							->where('imp_id',$imp_id)
							->update(['imp_status' => 1]);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validatePurchaseNonItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [11 => 'PURCHASE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list('purchase');


		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		$gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');


		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_side_table = 'aicountly_impinwsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impinwsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
			
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim(html_entity_decode($value['imptxnvch_long_narr']));
			$pos = trim($value['imptxnvch_pos']);
			$country_code = trim($value['imptxnvch_country_code']);

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}

			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}

			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstrinwsup_tbl)
						->where('LOWER(inwsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }	
			 if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }

			if($status && $country_code == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			}

			if($status && $country_code != ''){
		
				if(!in_array($country_code,$country_code_list)){
					$status = false;
					$country_code_str=implode(", ",$country_code_list);

					$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
				}
			}

			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim(html_entity_decode($value['imptxnvch_acc_bds']));
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);
					$is_party_acc = trim($value['imptxnvch_pary_acc']); // 0 or 1
					
					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
								
								
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'PURCHASE')
						$drcr = 'd';
					else
						$drcr = 'c';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
						'is_party_acc' => $is_party_acc
					];
				}
			}

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			/* if($status && $debit_total != $credit_total){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total does not match Credit Total';
			} */

			$tax_summary = [];
			if($status) // check tax
			{  
				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){
                        if($val['acc_type'] == 'bsd'){
							$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not be Bill Sundry';
						}
						if($val['acc_type'] == 'acc' && $val['is_party_acc']=='1'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_parent_id != 0){ // Primary Account
								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Account '.$val['acc_name'].' can not come under Owner"s Fund';
								}
							}
							else{
								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								$acc_grp_parent_id = $acctgroupn['acc_grp_parent_id'];

								if(in_array($acc_grp_parent_id, [1])){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' can not come under Owners Fund';
								}

								if($status){
									if($acctgroupn['under_main_grp_id'] == 0){
										if(!in_array($acc_grp_id, [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
										}
									}
									else{
										if(!in_array($acctgroupn['under_main_grp_id'], [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
											
										}
									}
								}
							}
						}
						if($val['acc_type'] == 'acc' && $val['is_party_acc']=='0'){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							
								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								$acc_grp_parent_id = $acctgroupn['acc_grp_parent_id'];
								
									if($acctgroupn['under_main_grp_id'] == 0){
										if(in_array($acc_grp_id, [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should not come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
										}
									}
									else{
										if(in_array($acctgroupn['under_main_grp_id'], [22,16,23,21])){
											$status = false;
											$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' should not come under Trade Receivable, Trade Payable, Cash & Cash Equivalent, Bank OD/OCC';
											
										}
									}
								
							
						}

						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd'){
						if($val['acc_type'] == 'acc' && $val['is_party_acc']=="1"){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}
					

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();
							if($status){
								if($acctgroupn['under_main_grp_id'] == 0){
									if(!in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(!in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
							}

						}
						if($val['acc_type'] == 'acc' && $val['is_party_acc']=="0"){
							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if(isset($acctgroupn) && $acctgroupn['under_main_grp_id'] == 0){
									if(isset($acc_grp_id) && in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should not come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(isset($acctgroupn) &&  in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should not come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
							

						}
						
					}
				}

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					$bo_state_code = intval($bo_state_code);
					$pos = intval($pos);

					// calculate tax
					
					foreach ($trans as $k => $val){

						if($val['drcr'] == 'c'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;
					    	/* $summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
							$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount']; */

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    	//	$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		//$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								 if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								} 
								$igst_rate = parseAmount($rates['igst_rate']);
						        $cess_rate = parseAmount($rates['cess_rate']);
						         $cess_basis = $rates['cess_basis'];

					    	if($pos != $bo_state_code){
					    		// 2 taxes IGST n CESS
					    		$igst = ($val['amount'] * $igst_rate)/100;
								$igst = parseAmount($igst);

								$tax['IGST']['status'] = 1;
								$tax['IGST']['value'] += $igst;

								//$summary['acc_igst_rate'] = $igst_rate;
								//$summary['acc_igst'] = $igst;
								$total_tax += $igst;

							if($cess_basis == 1){
							  $cess = ($val['amount'] * $cess_rate)/100;
							  $cess = parseAmount($cess);

							  $tax['CESS']['status'] = 1;
							  $tax['CESS']['value'] += $cess;

							  //$summary['acc_cess_basis'] = $cess_basis;
							 // $summary['acc_cess_rate'] = $cess_rate;
							  //$summary['acc_cess'] = $cess;
							  $total_tax += $cess;
							}
							/* else{
							  $err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
							  $tax['CESS']['status'] = 2;
							  $tax['CESS']['error'] = $err;
							 } */
					    	}
					    	else{
					    		// 3 taxes CESS,CGST, SGST or UT-TAX
					    		$cgst_rate = parseAmount($igst_rate / 2);
					    		$cgst = ($val['amount'] * $cgst_rate) / 100;
								$cgst = parseAmount($cgst);

								$tax['CGST']['status'] = 1;
								$tax['CGST']['value'] += $cgst;

								//$summary['acc_cgst_rate'] = $cgst_rate;
								//$summary['acc_cgst'] = $cgst;
								$total_tax += $cgst;

								$ut_arr = [35,4,26,25,31,38,34,97];
							if(!in_array($pos, $ut_arr)){
						   
							  $tax['SGST']['status'] = 1;
							  $tax['SGST']['value'] += $cgst;

							  //$summary['acc_sgst_rate'] = $cgst_rate;
								//$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}
							else{
							  $ut_tax = $cgst;
							  $tax['UT-TAX']['status'] = 1;
							  $tax['UT-TAX']['value'] += $ut_tax;

							  //$summary['acc_sgst_rate'] = $cgst_rate;
								//$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate) / 100;
	                  $cess = parseAmount($cess);

	                  $tax['CESS']['status'] = 1;
	                  $tax['CESS']['value'] += $cess;

	                  //$summary['acc_cess_basis'] = $cess_basis;
	                  //$summary['acc_cess_rate'] = $cess_rate;
	                	//$summary['acc_cess'] = $cess;
	                	$total_tax += $cess;
	                }
	                /* else{
	                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
	                  $tax['CESS']['status'] = 2;
	                  $tax['CESS']['error'] = $err;
	                } */
					    	}

					    	//$summary['total_tax'] = $total_tax;
					    	
					    } // else
					  }
				    if($val['drcr'] == 'd'){
						 if($val['acc_type']=='acc'){
					    	$total_tax = 0;
					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
							$summary['acc_type'] = 'bsd';
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
							
								$igst_rate = parseAmount($rates['igst_rate']);
						        $cess_rate = parseAmount($rates['cess_rate']);
						         $cess_basis = $rates['cess_basis'];

					    	if($pos != $bo_state_code){
					    		// 2 taxes IGST n CESS
					    		$igst = ($val['amount'] * $igst_rate)/100;
								$igst = parseAmount($igst);


								$summary['acc_igst_rate'] = $igst_rate;
								$summary['acc_igst'] = $igst;
								$total_tax += $igst;

							if($cess_basis == 1){
							  $cess = ($val['amount'] * $cess_rate)/100;
							  $cess = parseAmount($cess);

							  $summary['acc_cess_basis'] = $cess_basis;
							  $summary['acc_cess_rate'] = $cess_rate;
							  $summary['acc_cess'] = $cess;
							  $total_tax += $cess;
							}
							
					    	}
					    	else{
					    		// 3 taxes CESS,CGST, SGST or UT-TAX
					    		$cgst_rate = parseAmount($igst_rate / 2);
					    		$cgst = ($val['amount'] * $cgst_rate) / 100;
								$cgst = parseAmount($cgst);


								$summary['acc_cgst_rate'] = $cgst_rate;
								$summary['acc_cgst'] = $cgst;
								$total_tax += $cgst;

								$ut_arr = [35,4,26,25,31,38,34,97];
							if(!in_array($pos, $ut_arr)){
							  $summary['acc_sgst_rate'] = $cgst_rate;
								$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}
							else{
							  $ut_tax = $cgst;
							  $tax['UT-TAX']['status'] = 1;
							  $tax['UT-TAX']['value'] += $ut_tax;

							  $summary['acc_sgst_rate'] = $cgst_rate;
								$summary['acc_sgst'] = $cgst;
								$total_tax += $cgst;
							}

	                if($cess_basis == 1){
	                  $cess = ($val['amount'] * $cess_rate) / 100;
	                  $cess = parseAmount($cess);


	                  $summary['acc_cess_basis'] = $cess_basis;
	                  $summary['acc_cess_rate'] = $cess_rate;
	                	$summary['acc_cess'] = $cess;
	                	$total_tax += $cess;
	                }
	               
					    	}

					    	$summary['total_tax'] = $total_tax;
					    	$account_summary[] = $summary;
					}
					}
					}
	
	
					if($status) // check for errors
					{ 
					
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									 if($index != ''){
										$obj = $tax_accounts[$index];
										
											if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
											{
											  $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
											}
									}
									else{
										//$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									} 
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}
	
	
			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

	function importSaleItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [18 => 'SALE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list();

		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$itemmaster_tbl   = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$itemtaxmst_tbl   = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
		$itmunitmst_tbl   = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');

		$itemvalmst_tbl = $this->company_id.'_itemvalmst_'.$this->session->get('ses_comp_fy_id');
		$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_table2 = 'aicountly_imptxnvchi_univdb';
		$sub_side_table = 'aicountly_impoutsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impoutsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';
			$value['imptxnvch_supplytype'] = $temp2['impout_supply_type'] ?? '';
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim($value['imptxnvch_long_narr']);
			$pos = trim($value['imptxnvch_pos']);
			$pos   = sprintf('%02d',$pos);
			$country_code = trim($value['imptxnvch_country_code']);
			$voucher_supplytype = trim($value['imptxnvch_supplytype']);
			$mc = trim($value['imptxnvch_mc']);

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;
			$mc_id = 0;
			$supplyTypes = [
					3  => ['EXPWOP', 3],
					5  => ['SEZWOP', 5],
					14 => ['NILSPLY', 14],
					15 => ['EXMSPLY', 15],
					16 => ['NONGSTS', 16],
					2  => ['EXPWP', 2],
					4  => ['SEZWP', 4],
					13 => ['DE', 13]
				];
			   // Trim the voucher_supplytype value
				$voucher_supplytype = trim($voucher_supplytype);

				// Check if the supply type exists in the array, otherwise use the default 'B2B'
				if (isset($supplyTypes[$voucher_supplytype])) {
					list($outsup_inv_type, $ewb_supply_type) = $supplyTypes[$voucher_supplytype];
				} else {
					$outsup_inv_type = 'B2B';
					$ewb_supply_type = 1;
				}
				

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}

			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid   = 0;
			$date_ymd     = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			
			
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}
			
			if($status && $voucher_supplytype == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Supply Type is missing.';
			}

			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }
			 if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstroutsup_tbl)
						->where('LOWER(outsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }

			if($status && $mc == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center is missing.';
			}

			if($status && $mc != ''){
				$mcmasternn = $this->db->table($mcmasternn_tbl)
													->where('LOWER(mat_cent_name)', strtolower($mc))
													->get()->getRowArray();
				if(!$mcmasternn){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center "'.$mc.'" does not exists.';
				}
				else{
					$mc_id = $mcmasternn['mat_cent_id'];
				}
			}
				

			// if($status && $country_code == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			// }

			// if($status && $country_code != ''){
		
			// 	if(!in_array($country_code,$country_code_list)){
			// 		$status = false;
			// 		$country_code_str=implode(", ",$country_code_list);

			// 		$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
			// 	}
			// }

			if($status && $pos == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher POS is missing.';
			}
			
			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		
			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'SALE')
						$drcr = 'c';
					else
						$drcr = 'd';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
					];
				} 

				$result2 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$item = trim($value['imptxnvch_item']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$uqc = trim($value['imptxnvch_uqc_cr']);
					$qty = trim($value['imptxnvch_qty_cr']);

					$item_id = 0;
					$unit_id = 0;
		

					if($status && $item == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Item Name is missing.';
						break;
					}
					if($status && $item != ''){

						$exists = $this->db->table($itemmaster_tbl)
								->where('LOWER(item_name)', strtolower($item))
								->get()->getRowArray();
						if($exists){
							$item_id = $exists['item_id'];
						}
						else{
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($item_id == 0){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($status && $uqc == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Unit is missing.';
							break;
						}

						if($status && $uqc != ''){
							$exists = $this->db->table($itmunitmst_tbl)
								->where('LOWER(item_unit)', strtolower($uqc))
								->get()->getRowArray();
							if($exists){
								$unit_id = $exists['unit_id'];
							}
							else{
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Item Unit "'.$uqc.'" does not exists.';
								break;
							}	
						}
						if($status && $qty == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Quantity is missing.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

				
					$drcr = 'c';
					$amount = $credit;
	
					$qty = parseValue($qty);
					if(($qty <= 0)){
						$qty = 1;
					}

					$trans[] = [
						'acc_id'	  => $item_id,
						'acc_name'  => $item,
						'acc_type'  => 'itm',
						'drcr'		  => 'c',
						'amount'	  => $credit,
						'short_nrr'	=> $short_nrr,
						'unit_id'		=> $unit_id,
						'qty'				=> $qty,
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total '.formatAmount($debit_total).' does not match Credit Total '.formatAmount($credit_total).'';
			}

			$tax_summary = [];
			$dr_acc_count = 0;
			if($status) // check tax
			{

				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){

						if($val['acc_type'] == 'acc'){

							$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' is invalid';

						}

						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd'){
						if($val['acc_type'] == 'acc'){

							$dr_acc_count++; 

							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}

							if($status){

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if($acctgroupn['under_main_grp_id'] == 0){
									if(!in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(!in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}

							}

						}
						if($val['acc_type'] == 'bsd'){
							$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' can not be Bill Sundry';
						}
					}
				}

				if($status && $dr_acc_count != 1){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher can have one debit account';
				}

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					
					$bo_gstin_type = $this->session->get('bo_gstin_type');
					$bo_gstin_type = intval($bo_gstin_type);

					// calculate tax
					$account_summary=array();
					foreach ($trans as $k => $val){

						if($val['drcr'] == 'c'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;

					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
							$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}
					    	if($val['acc_type']=='itm'){
					    		$itemtaxmst = $this->db->table($itemtaxmst_tbl)
					    						->where('item_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $itemtaxmst['item_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								}
								$tax_cat_id = $rates['tax_cat_id'];

								//tax  applicable
								if(!in_array($tax_cat_id, [16,14,8,15,18]) && !in_array(trim($voucher_supplytype), [3,5,14,15,16]))
								{

									$igst_rate = parseAmount($rates['igst_rate']);
							    $cess_rate = parseAmount($rates['cess_rate']);
							    $cess_basis = $rates['cess_basis'];
							    $supply_type = $rates['supply_type'];

							    if($val['acc_type'] != 'itm'){

							    	if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    	else{
							    		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) / 100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate) / 100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    }
							    else{
							    	if($bo_gstin_type == 1){
                     	$igst_rate = $igst_rate; // by tax cat id
	                  }
	                  else if($bo_gstin_type == 2){

	                    if($supply_type == 1 || $supply_type == 3){
	                      $igst_rate = $this->item_goods_rate;
	                    }
	                    else if($supply_type == 2){
	                      $igst_rate = $this->item_services_rate;
	                    }
	                  }

	                  if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;
		              	}
		              	else{
		              		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) /100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                // CESS
			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{

			                	$mrp = 0;
			                	$itemvalmst = $this->db->table($itemvalmst_tbl)
			                			->where('item_id', $item_id)
			                			->get()->getRowArray();
			                	if($itemvalmst){
			                		$mrp = parseAmount($itemvalmst['item_mrp']);
			                	}
			                	else{
			                		$err = $val['acc_name'].' Item MRP does not exists';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                	}

			                	$cess = ($mrp * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
		              	}
							    }

						    	$summary['total_tax'] = $total_tax;
						    	$account_summary[] = $summary;

						    }
					    } // else
					  }
					}
	
					if($status) // check for errors
					{
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									if($index != ''){
										$obj = $tax_accounts[$index];
				            if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
				            {
				              $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
				            }
									}
									else{
										$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									}
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}

			if($status) 
			{
			
				
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				if($long_nrr != ''){  
    			$long_nrr = str_replace(["\r","\n"],'',$long_nrr);
				}

				$insert_data  = array(
          "comp_id"               => $this->company_id,
          "comp_vch_series_id"    => $comp_vch_series_id,
          "comp_vch_no"           => $vno,
          "voucher_type_id"       => $voucher_type_id,
          "voucher_date"          => $date,
          "mat_cent_id"           => $mc_id,
          "vch_subtype_id"        => 8,
          "voucher_tag"           => '',
          "currency_id"           => 1,
        );
        $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);

        if(in_array($voucher_type_id, [18]))
        {
			
				
			$isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$comp_vch_series_id);	
			if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				  $bill_no_counter    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,0);
				  $consodata          = array("vch_bill_ref_no"=>$bill_no_counter);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
				}
			
		// save into gstroutsup table
				if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,$vch_bill_ref_no);
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$bill_no_format,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$outsup_inv_type,"outsup_eco"=>0);
				  $this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}
				else{
				$gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$bill_no,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$outsup_inv_type,"outsup_eco"=>0);
				$this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}
			
			//Save Eway Tables Data
				$ewbmstreqn_insert_data = array("ewb_supply_type"=>$ewb_supply_type,"ewb_sub_supply_desc"=>"",
				                               "ewb_doc_type"=>"","voucher_txn_id"=>$voucher_txn_id,"ewb_txn_type"=>1,
				                               "bo_id"=>$this->bo_id);
				$ewb_id = $this->TransactionModel->add_ewbmstreqn_data($ewbmstreqn_insert_data);	
				
				// Save Into ewbmstcons table
				$ewbmstcons_insert_data = array("conso_ewb_no"=>$bill_no,"conso_ewb_date"=>$date,
				                               "ewb_id"=>$ewb_id,"ewb_no"=>0);
				$conso_ewb_id = $this->TransactionModel->add_ewbmstcons_data($ewbmstcons_insert_data);
				
			$item_account_array=array();
        	foreach ($trans as $k => $val) 
        	{  
			   
	        	if($val['acc_type'] == 'acc')
	          {
	            $insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => $val['acc_id'],
	              'master_id_type'      => 'acc'
	            ];

	            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);


	            $insert_data  = [
	              'comp_id'            => $this->company_id,
	              'acc_id'             => $val['acc_id'],
	              'acc_txn_date'       => $date,
	              'acc_txn_amount'     => $val['amount'],                        
	              'acc_txn_drcr'       => $val['drcr'],
	              'acc_txn_narr'       => $val['short_nrr'],
	              'comp_vch_series_no' => $vno,
	              'posted_on'          => date('Y-m-d H:i:s'),
	              'voucher_txn_id'     => $voucher_txn_id,
	              'voucher_type_id'    => $voucher_type_id,
	              'txn_id'             => $txn_id,
	              'acc_bal'            => 0
	            ];  

	            $this->TransactionModel->add_acc_txn_data($insert_data);
	            $this->TransactionModel->update_account_balance($val['acc_id']);

	          
	          	$check_bbb_account = $this->TransactionModel->check_bbb_account($val['acc_id']);
              if($check_bbb_account)
              {
                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($val['acc_id']);
                if($bill_ref_id)
                {

                  $bill_txn_data = [
                    'bills_ref_id'       => $bill_ref_id,
                    'comp_id'           => $this->company_id,
                    'acc_id'             => $val['acc_id'],
                    'voucher_txn_id'     => $voucher_txn_id,
                    'voucher_type_id'    => $voucher_type_id,
                    'comp_vch_series_id' => $series,
                    'bills_txn_date'     => $date,
                    'bills_txn_drcr'     => strtoupper($val['drcr']),
                    'bills_txn_amt'      => $val['amount'],
                    'bills_txn_bal'      => 0,
                    'bills_txn_narr'     => '',
                  ];
                  $this->TransactionModel->add_bill_txn($bill_txn_data);
                }
              }

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }   
	          }

	          if($val['acc_type'] == 'bsd')
	          {
	            $txn_data = array(
	              "comp_id"               => $this->company_id,
	              "comp_vch_series_id"    => $series,
	              "voucher_txn_id"        => $voucher_txn_id,
	              "master_id"             => $val['acc_id'],
	              'master_id_type'        => 'bsd'
	            );
	            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

	            $insert_data  = array(
	              "comp_id"             => $this->company_id,
	              "sundry_txn_date"     => $date,
	              "sundry_txn_amount"   => $val['amount'],
	              "sundry_txn_drcr"     => $val['drcr'],
	              "comp_vch_name"       => $vno, // ?
	              "comp_vch_series_no"  => $vno,
	              "bill_sundry_id"      => $val['acc_id'],
	              "sundry_txn_narr"     => $val['short_nrr'],
	              "sundry_bal"          => 0,
	              "voucher_txn_id"      => $voucher_txn_id, 
	              "voucher_type_id"     => $voucher_type_id,
	              'txn_id'              => $txn_id,
	              'sundry_tag_rate'     => ''
	            );
	            $this->TransactionModel->add_sundry_txn_data($insert_data);

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }     
	          }

	          if($val['acc_type'] == 'itm')
	          {
	            $txn_data = array(
	              "comp_id"               => $this->company_id,
	              "comp_vch_series_id"    => $series,
	              "voucher_txn_id"        => $voucher_txn_id,
	              "master_id"             => $val['acc_id'],
	              'master_id_type'        => 'itm'
	            );
	            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

	            $insert_data   = array(
								'comp_id'             => $this->company_id,
								'item_txn_date'       => $date,
								'item_txn_amount'     => $val['amount'],
								'item_txn_drcr'       => $val['drcr'],
								'item_txn_qty'        => $val['qty'],
								'description'         => $val['short_nrr'],
								'item_id'             => $val['acc_id'],
								'voucher_txn_id'      => $voucher_txn_id,
								'voucher_type_id'     => $voucher_type_id,
								'mat_cent_id'         => $mc_id,
								'txn_id'			  => $txn_id,
								"item_unit"			  => $val['unit_id'],
								"item_bal_qty"		  => 0,
								"item_avail"  		  => 1,
								"batch_id"  		  => 0,	
					   	);	
							$this->TransactionModel->add_itm_txn_data($insert_data);
							$item_info   =  $this->TransactionModel->get_item_info($val['acc_id']);
							$account_id  =  $item_info['item_sales_acc'];
							if(isset($item_account_array[$account_id])){
						$item_account_array[$account_id] += parseAmount( $val['amount']);
					}
					else{
						$item_account_array[$account_id] = parseAmount( $val['amount']);
					}			
					
							
	            }
	        }
			// item sales acount entry
			foreach ($item_account_array as $account_id => $amount) {
					$amount_fcy=0;	
				 	$txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $comp_vch_series_id,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $account_id,
						'master_id_type' 		=> 'acc'
				 	);
					$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

					$insert_data  = [
						'comp_id'            => $this->company_id,
						'acc_id'             => $account_id,
						'acc_txn_date'       => $date,
						'acc_txn_amount'     => $amount,						
						'acc_txn_drcr'       => 'c',
						'comp_vch_series_no' => $vno,
						'posted_on'          => date('Y-m-d H:i:s'),						
						'voucher_txn_id'     => $voucher_txn_id,
						'voucher_type_id'    => $voucher_type_id,
						'txn_id'             => $txn_id,
						'acc_bal'            => 0,
						'acc_txn_fcy'        => $amount_fcy
				    ];	
					$this->TransactionModel->add_acc_txn_data($insert_data);
					$this->TransactionModel->update_account_balance($account_id);
				   }

	         if(is_array($account_summary) && count($account_summary) >0){
			foreach($account_summary as $k => $val) {
	        	$insert_data = [
							"comp_id"             => $this->company_id,
							"comp_vch_series_id"  => $series,
							"voucher_txn_id"      => $voucher_txn_id,
							"master_id"           => $val['acc_id'],
							'master_id_type'      => 'tax'
					 	];
						$txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
						$item_tax_info = $this->TransactionModel->get_item_taxinfo($val['acc_id']);
						$item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($val['acc_id'],$item_tax_info['tax_cat_id']);		
					    
						if(trim($voucher_supplytype)==13)
						  $item_tax_short_code ='DEEMEXP';
					    if(trim($voucher_supplytype)==2 || trim($voucher_supplytype)==3)
						  $item_tax_short_code ='ZERSPLY';
					    if(trim($voucher_supplytype)==4 || trim($voucher_supplytype)==5)
						  $item_tax_short_code ='SEZSPLY';
						if(trim($voucher_supplytype)==14)
						  $item_tax_short_code ='NILSPLY';
						if(trim($voucher_supplytype)==15)
						  $item_tax_short_code ='EXMSPLY';
						if(trim($voucher_supplytype)==16)
						  $item_tax_short_code ='NONGSTS';
						
						$insert_data   = [
							'acc_id'               => $val['acc_id'],
							'acc_igst_rate'        => $val['acc_igst_rate'] ?? 0,
							'acc_igst'             => $val['acc_igst'] ?? 0,
							'acc_cgst_rate'        => $val['acc_cgst_rate'] ?? 0,
							'acc_cgst'             => $val['acc_cgst'] ?? 0,
							'acc_sgst_rate'        => $val['acc_sgst_rate'] ?? 0,
							'acc_sgst'             => $val['acc_sgst'] ?? 0,
							'acc_cess_rate'        => 0,'acc_cess'        => 0,
							'acc_nonadv_cess_rate' => 0,
							'acc_nonadv_cess'      => 0,
							'acc_hsn_sac'          => $val['acc_hsn_sac'] ?? '',
							'vch_txn_id'           => $voucher_txn_id,
							'txn_id'               => $txn_id,
							'acc_type'             => $val['acc_type'],
							'taxable_amt'          => $val['taxable_amt'],
							'total_tax'            => $val['total_tax'],
							'acc_cess_basis'       => 1,
							'cmp_tax_short_code'   => $item_tax_short_code,
							'acc_txn_date'         => date('Y-m-d',strtotime($date))
						];
						
		      	$this->TransactionModel->add_taxsummary_data($insert_data);
	           }
			 }

	        $insert_data = [
		        "comp_id"             => $this->company_id,
		        "comp_vch_series_id"  => $series,
		        "voucher_txn_id"      => $voucher_txn_id,
		        "master_id"           => 0,
		        'master_id_type'      => 'nrr'
		      ];

		      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		      $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_nrr);
        } 
				

				$this->aicountly_db->table($table)
							->where('impexp_sr_id',$impexp_sr_id)
							->where('imp_id',$imp_id)
							->update(['imp_status' => 1]);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validateSaleItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [18 => 'SALE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list();

		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$itemmaster_tbl   = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$itemtaxmst_tbl   = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
		$itmunitmst_tbl   = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');

		$itemvalmst_tbl = $this->company_id.'_itemvalmst_'.$this->session->get('ses_comp_fy_id');
		$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');



		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_table2 = 'aicountly_imptxnvchi_univdb';
		$sub_side_table = 'aicountly_impoutsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impoutsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impoutsup_country_code'] ?? '';
			$value['imptxnvch_supplytype'] = $temp2['impout_supply_type'] ?? '';
			
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim($value['imptxnvch_long_narr']);
			$pos = trim($value['imptxnvch_pos']);
			$country_code = trim($value['imptxnvch_country_code']);
			$voucher_supplytype = trim($value['imptxnvch_supplytype']);
			
			$mc = trim($value['imptxnvch_mc']);

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;
			$mc_id = 0;

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}

			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  = strtotime($this->session->get('ses_company_fy_end'));
			$date_valid   = 0;
			$date_ymd     = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			
			
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}
			if($status && $voucher_supplytype == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Supply Type is missing.';
			}

			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}
			if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstroutsup_tbl)
						->where('LOWER(outsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }	
			 if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }

			if($status && $mc == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center is missing.';
			}

			if($status && $mc != ''){
				$mcmasternn = $this->db->table($mcmasternn_tbl)
													->where('LOWER(mat_cent_name)', strtolower($mc))
													->get()->getRowArray();
				if(!$mcmasternn){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center "'.$mc.'" does not exists.';
				}
				else{
					$mc_id = $mcmasternn['mat_cent_id'];
				}
			}
				

			// if($status && $country_code == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			// }

			// if($status && $country_code != ''){
		
			// 	if(!in_array($country_code,$country_code_list)){
			// 		$status = false;
			// 		$country_code_str=implode(", ",$country_code_list);

			// 		$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
			// 	}
			// }

			if($status && $pos == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher POS is missing.';
			}
			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'SALE')
						$drcr = 'c';
					else
						$drcr = 'd';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
					];
				} 

				$result2 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$item = trim($value['imptxnvch_item']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$uqc = trim($value['imptxnvch_uqc_cr']);
					$qty = trim($value['imptxnvch_qty_cr']);

					$item_id = 0;
					$unit_id = 0;
		

					if($status && $item == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Item Name is missing.';
						break;
					}
					if($status && $item != ''){

						$exists = $this->db->table($itemmaster_tbl)
								->where('LOWER(item_name)', strtolower($item))
								->get()->getRowArray();
						if($exists){
							$item_id = $exists['item_id'];
						}
						else{
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($item_id == 0){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($status && $uqc == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Unit is missing.';
							break;
						}

						if($status && $uqc != ''){
							$exists = $this->db->table($itmunitmst_tbl)
								->where('LOWER(item_unit)', strtolower($uqc))
								->get()->getRowArray();
							if($exists){
								$unit_id = $exists['unit_id'];
							}
							else{
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Item Unit "'.$uqc.'" does not exists.';
								break;
							}	
						}
						if($status && $qty == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Quantity is missing.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

				
					$drcr = 'c';
					$amount = $credit;
	
					$qty = parseValue($qty);
					if(($qty <= 0)){
						$qty = 1;
					}

					$trans[] = [
						'acc_id'	  => $item_id,
						'acc_name'  => $item,
						'acc_type'  => 'itm',
						'drcr'		  => 'c',
						'amount'	  => $credit,
						'short_nrr'	=> $short_nrr,
						'unit_id'		=> $unit_id,
						'qty'				=> $qty,
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total '.formatAmount($debit_total).' does not match Credit Total '.formatAmount($credit_total).'';
			}

			$tax_summary = [];
			$dr_acc_count = 0;
			if($status) // check tax
			{

				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){

						if($val['acc_type'] == 'acc'){

							$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' is invalid';

						}

						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd'){
						if($val['acc_type'] == 'acc'){

							$dr_acc_count++; 

							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}

							if($status){

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if($acctgroupn['under_main_grp_id'] == 0){
									if(!in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(!in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}

							}

						}
						if($val['acc_type'] == 'bsd'){
							$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' can not be Bill Sundry';
						}
					}
				}

				if($status && $dr_acc_count != 1){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher can have one debit account';
				}

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					$bo_state_code = intval($bo_state_code);
					$bo_gstin_type = $this->session->get('bo_gstin_type');
					$bo_gstin_type = intval($bo_gstin_type);

					$pos = intval($pos);

					// calculate tax

					foreach ($trans as $k => $val){

						if($val['drcr'] == 'c'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;

					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
								$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}
					    	if($val['acc_type']=='itm'){
					    		$itemtaxmst = $this->db->table($itemtaxmst_tbl)
					    						->where('item_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $itemtaxmst['item_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								}
								$tax_cat_id = $rates['tax_cat_id'];

								//tax  applicable
								
								
								if(!in_array($tax_cat_id, [16,14,8,15,18]) && !in_array(trim($voucher_supplytype), [3,5,14,15,16]) ){

									$igst_rate = parseAmount($rates['igst_rate']);
							    $cess_rate = parseAmount($rates['cess_rate']);
							    $cess_basis = $rates['cess_basis'];
							    $supply_type = $rates['supply_type'];

							    if($val['acc_type'] != 'itm'){

							    	if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    	else{
							    		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) / 100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate) / 100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    }
							    else{
							    	if($bo_gstin_type == 1){
                     	$igst_rate = $igst_rate; // by tax cat id
	                  }
	                  else if($bo_gstin_type == 2){

	                    if($supply_type == 1 || $supply_type == 3){
	                      $igst_rate = $this->item_goods_rate;
	                    }
	                    else if($supply_type == 2){
	                      $igst_rate = $this->item_services_rate;
	                    }
	                  }

	                  if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;
		              	}
		              	else{
		              		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) /100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                // CESS
			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{

			                	$mrp = 0;
			                	$itemvalmst = $this->db->table($itemvalmst_tbl)
			                			->where('item_id', $item_id)
			                			->get()->getRowArray();
			                	if($itemvalmst){
			                		$mrp = parseAmount($itemvalmst['item_mrp']);
			                	}
			                	else{
			                		$err = $val['acc_name'].' Item MRP does not exists';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                	}

			                	$cess = ($mrp * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
		              	}
							    }

						    	$summary['total_tax'] = $total_tax;
						    	$account_summary[] = $summary;

						    }
					    } // else
					  }
					}
					
					if($status) // check for errors
					{
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									if($index != ''){
										$obj = $tax_accounts[$index];
				            if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
				            {
				              $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
				            }
									}
									else{
										$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									}
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}

			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}

function importPurchaseItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [11 => 'PURCHASE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list('purchase');

		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$itemmaster_tbl   = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$itemtaxmst_tbl   = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
		$itmunitmst_tbl   = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');

		$itemvalmst_tbl = $this->company_id.'_itemvalmst_'.$this->session->get('ses_comp_fy_id');
		$gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');


		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_table2 = 'aicountly_imptxnvchi_univdb';
		$sub_side_table = 'aicountly_impinwsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impinwsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
			$value['imptxnvch_supplytype'] = $temp2['impinw_supply_type'] ?? '';
			
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim($value['imptxnvch_long_narr']);
			$pos = trim($value['imptxnvch_pos']);
			$country_code = trim($value['imptxnvch_country_code']);
			$mc = trim($value['imptxnvch_mc']);
			$voucher_supplytype = trim($value['imptxnvch_supplytype']);

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;
			$mc_id = 0;
			$supplyTypes = [
					3  => ['EXPWOP', 3],
					5  => ['SEZWOP', 5],
					14 => ['NILSPLY', 14],
					15 => ['EXMSPLY', 15],
					16 => ['NONGSTS', 16],
					2  => ['EXPWP', 2],
					4  => ['SEZWP', 4],
					13 => ['DE', 13]
				];
			// Check if the supply type exists in the array, otherwise use the default 'B2B'
				if (isset($supplyTypes[$voucher_supplytype])) {
					list($outsup_inv_type, $ewb_supply_type) = $supplyTypes[$voucher_supplytype];
				} else {
					$outsup_inv_type = 'B2B';
					$ewb_supply_type = 1;
				}	
			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}

			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  =strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}
			if($status && $voucher_supplytype == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Supply Type is missing.';
			}
			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstrinwsup_tbl)
						->where('LOWER(inwsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }	
			 if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }

			if($status && $mc == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center is missing.';
			}

			if($status && $mc != ''){
				$mcmasternn = $this->db->table($mcmasternn_tbl)
													->where('LOWER(mat_cent_name)', strtolower(html_entity_decode($mc)))
													->get()->getRowArray();
				if(!$mcmasternn){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center "'.$mc.'" does not exists.';
				}
				else{
					$mc_id = $mcmasternn['mat_cent_id'];
				}
			}
				

			// if($status && $country_code == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			// }

			// if($status && $country_code != ''){
		
			// 	if(!in_array($country_code,$country_code_list)){
			// 		$status = false;
			// 		$country_code_str=implode(", ",$country_code_list);

			// 		$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
			// 	}
			// }

			if($status && $pos == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher POS is missing.';
			}
			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'PURCHASE')
						$drcr = 'd';
					else
						$drcr = 'c';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
					];
				} 

				$result2 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$item = trim($value['imptxnvch_item']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$uqc = trim($value['imptxnvch_uqc_cr']);
					$qty = trim($value['imptxnvch_qty_cr']);

					$item_id = 0;
					$unit_id = 0;
		

					if($status && $item == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Item Name is missing.';
						break;
					}
					if($status && $item != ''){

						$exists = $this->db->table($itemmaster_tbl)
								->where('LOWER(item_name)', strtolower($item))
								->get()->getRowArray();
						if($exists){
							$item_id = $exists['item_id'];
						}
						else{
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($item_id == 0){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($status && $uqc == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Unit is missing.';
							break;
						}

						if($status && $uqc != ''){
							$exists = $this->db->table($itmunitmst_tbl)
								->where('LOWER(item_unit)', strtolower($uqc))
								->get()->getRowArray();
							if($exists){
								$unit_id = $exists['unit_id'];
							}
							else{
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Item Unit "'.$uqc.'" does not exists.';
								break;
							}	
						}
						if($status && $qty == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Quantity is missing.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

				
					$drcr = 'd';
					$amount = $credit;
	
					$qty = parseValue($qty);
					if(($qty <= 0)){
						$qty = 1;
					}

					$trans[] = [
						'acc_id'	  => $item_id,
						'acc_name'  => $item,
						'acc_type'  => 'itm',
						'drcr'		  => 'd',
						'amount'	  => $debit,
						'short_nrr'	=> $short_nrr,
						'unit_id'		=> $unit_id,
						'qty'				=> $qty,
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total '.formatAmount($debit_total).' does not match Credit Total '.formatAmount($credit_total).'';
			}

			$tax_summary = [];
			$dr_acc_count = 0;
			if($status) // check tax
			{

				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){

						if($val['acc_type'] == 'acc'){

							$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' is invalid';

						}


						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd'){
						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
						if($val['acc_type'] == 'acc'){

							$dr_acc_count++; 

							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}

							if($status){

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if($acctgroupn['under_main_grp_id'] == 0){
									if(!in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(!in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}

							}

						}
						
					}
				}

				/* if($status && $dr_acc_count != 1){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher can have one debit account';
				} */

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					//$bo_state_code = intval($bo_state_code);
					$bo_gstin_type = $this->session->get('bo_gstin_type');
					//$bo_gstin_type = intval($bo_gstin_type);

					
					// calculate tax
				    $account_summary=array();
					foreach ($trans as $k => $val){

						if($val['drcr'] == 'd'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;

					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
							$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}
					    	if($val['acc_type']=='itm'){
					    		$itemtaxmst = $this->db->table($itemtaxmst_tbl)
					    						->where('item_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $itemtaxmst['item_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								}
								$tax_cat_id = $rates['tax_cat_id'];

								//tax  applicable
								if(!in_array($tax_cat_id, [16,14,8,15,18]) && !in_array(trim($voucher_supplytype), [3,5,14,15,16])){

									$igst_rate = parseAmount($rates['igst_rate']);
							    $cess_rate = parseAmount($rates['cess_rate']);
							    $cess_basis = $rates['cess_basis'];
							    $supply_type = $rates['supply_type'];

							    if($val['acc_type'] != 'itm'){

							    	if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    	else{
							    		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) / 100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate) / 100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    }
							    else{
							    	if($bo_gstin_type == 1){
                     	$igst_rate = $igst_rate; // by tax cat id
	                  }
	                  else if($bo_gstin_type == 2){

	                    if($supply_type == 1 || $supply_type == 3){
	                      $igst_rate = $this->item_goods_rate;
	                    }
	                    else if($supply_type == 2){
	                      $igst_rate = $this->item_services_rate;
	                    }
	                  }

	                  if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;
		              	}
		              	else{
		              		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) /100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                // CESS
			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{

			                	$mrp = 0;
			                	$itemvalmst = $this->db->table($itemvalmst_tbl)
			                			->where('item_id', $item_id)
			                			->get()->getRowArray();
			                	if($itemvalmst){
			                		$mrp = parseAmount($itemvalmst['item_mrp']);
			                	}
			                	else{
			                		$err = $val['acc_name'].' Item MRP does not exists';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                	}

			                	$cess = ($mrp * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
		              	}
							    }

						    	$summary['total_tax'] = $total_tax;
						    	$account_summary[] = $summary;

						    }
					    } // else
					  }
					}
	
					if($status) // check for errors
					{
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									if($index != ''){
										$obj = $tax_accounts[$index];
				            if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
				            {
				              $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
				            }
									}
									else{
										$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									}
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}

			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				if($long_nrr != ''){  
    			$long_nrr = str_replace(["\r","\n"],'',$long_nrr);
				}

				$insert_data  = array(
          "comp_id"               => $this->company_id,
          "comp_vch_series_id"    => $comp_vch_series_id,
          "comp_vch_no"           => $vno,
          "voucher_type_id"       => $voucher_type_id,
          "voucher_date"          => $date,
          "mat_cent_id"           => $mc_id,
          "vch_subtype_id"        => 5,
          "voucher_tag"           => '',
          "currency_id"           => 1,
        );
        $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);

        if(in_array($voucher_type_id, [11]))
        {
			$isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$comp_vch_series_id);	
			if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				  $bill_no_counter    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,0);
				  $consodata          = array("vch_bill_ref_no"=>$bill_no_counter);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
				}
			
		if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$comp_vch_series_id,$date,$vch_bill_ref_no);
				  $gstrinwsup_insert_data = array("inwsup_rev_chg"=>0,"inwsup_bill_ref_no"=>$bill_no_format,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$outsup_inv_type,"inwsup_eco"=>0);
				  $this->TransactionModel->add_gstrinwsup_data($gstrinwsup_insert_data);
				}
				else{
				$gstrinwsup_insert_data = array("inwsup_rev_chg"=>0,"inwsup_bill_ref_no"=>$bill_no,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$outsup_inv_type,"inwsup_eco"=>0);
				$this->TransactionModel->add_gstrinwsup_data($gstrinwsup_insert_data);
				}
			
			

        	foreach ($trans as $k => $val) 
        	{
	        	if($val['acc_type'] == 'acc')
	          {
	            $insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => $val['acc_id'],
	              'master_id_type'      => 'acc'
	            ];

	            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);


	            $insert_data  = [
	              'comp_id'            => $this->company_id,
	              'acc_id'             => $val['acc_id'],
	              'acc_txn_date'       => $date,
	              'acc_txn_amount'     => $val['amount'],                        
	              'acc_txn_drcr'       => $val['drcr'],
	              'acc_txn_narr'       => $val['short_nrr'],
	              'comp_vch_series_no' => $vno,
	              'posted_on'          => date('Y-m-d H:i:s'),
	              'voucher_txn_id'     => $voucher_txn_id,
	              'voucher_type_id'    => $voucher_type_id,
	              'txn_id'             => $txn_id,
	              'acc_bal'            => 0
	            ];  

	            $this->TransactionModel->add_acc_txn_data($insert_data);
	            $this->TransactionModel->update_account_balance($val['acc_id']);

	          
	          	$check_bbb_account = $this->TransactionModel->check_bbb_account($val['acc_id']);
              if($check_bbb_account)
              {
                $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($val['acc_id']);
                if($bill_ref_id)
                {

                  $bill_txn_data = [
                    'bills_ref_id'       => $bill_ref_id,
                    'comp_id'           => $this->company_id,
                    'acc_id'             => $val['acc_id'],
                    'voucher_txn_id'     => $voucher_txn_id,
                    'voucher_type_id'    => $voucher_type_id,
                    'comp_vch_series_id' => $series,
                    'bills_txn_date'     => $date,
                    'bills_txn_drcr'     => strtoupper($val['drcr']),
                    'bills_txn_amt'      => $val['amount'],
                    'bills_txn_bal'      => 0,
                    'bills_txn_narr'     => '',
                  ];
                  $this->TransactionModel->add_bill_txn($bill_txn_data);
                }
              }

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }   
	          }

	          if($val['acc_type'] == 'bsd')
	          {
	            $txn_data = array(
	              "comp_id"               => $this->company_id,
	              "comp_vch_series_id"    => $series,
	              "voucher_txn_id"        => $voucher_txn_id,
	              "master_id"             => $val['acc_id'],
	              'master_id_type'        => 'bsd'
	            );
	            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

	            $insert_data  = array(
	              "comp_id"             => $this->company_id,
	              "sundry_txn_date"     => $date,
	              "sundry_txn_amount"   => $val['amount'],
	              "sundry_txn_drcr"     => $val['drcr'],
	              "comp_vch_name"       => $vno, // ?
	              "comp_vch_series_no"  => $vno,
	              "bill_sundry_id"      => $val['acc_id'],
	              "sundry_txn_narr"     => $val['short_nrr'],
	              "sundry_bal"          => 0,
	              "voucher_txn_id"      => $voucher_txn_id, 
	              "voucher_type_id"     => $voucher_type_id,
	              'txn_id'              => $txn_id,
	              'sundry_tag_rate'     => ''
	            );
	            $this->TransactionModel->add_sundry_txn_data($insert_data);

	            {

	              $proj_txn_data = [
	                'project_id'         => 0,
	                'comp_id'            => $this->company_id,
	                'acc_id'             => $val['acc_id'],
	                'acc_type'           => $val['acc_type'],
	                'voucher_txn_id'     => $voucher_txn_id,
	                'voucher_type_id'    => $voucher_type_id,
	                'comp_vch_series_id' => $series,
	                'proj_txn_date'        => $date,
	                'proj_txn_drcr'        => strtoupper($val['drcr']),
	                'proj_txn_amt'         => $val['amount'],
	                'proj_txn_bal'         => 0,
	                'proj_txn_narr'        => '',
	              ];
	              $this->TransactionModel->add_pr_txn_data($proj_txn_data);
              }     
	          }

	          if($val['acc_type'] == 'itm')
	          {
	            $txn_data = array(
	              "comp_id"               => $this->company_id,
	              "comp_vch_series_id"    => $series,
	              "voucher_txn_id"        => $voucher_txn_id,
	              "master_id"             => $val['acc_id'],
	              'master_id_type'        => 'itm'
	            );
	            $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

	            $insert_data   = array(
								'comp_id'             => $this->company_id,
								'item_txn_date'       => $date,
								'item_txn_amount'     => $val['amount'],
								'item_txn_drcr'       => $val['drcr'],
								'item_txn_qty'        => $val['qty'],
								'description'         => $val['short_nrr'],
								'item_id'             => $val['acc_id'],
								'voucher_txn_id'      => $voucher_txn_id,
								'voucher_type_id'     => $voucher_type_id,
								'mat_cent_id'         => $mc_id,
								'txn_id'			  			=> $txn_id,
								"item_unit"			  		=> $val['unit_id'],
								"item_bal_qty"		  	=> 0,
								"item_avail"  		  	=> 1,
								"batch_id"  		  		=> 0,	
					   	);	
							$this->TransactionModel->add_itm_txn_data($insert_data);
	          }
	        }

	        foreach ($account_summary as $k => $val) {
	        	$insert_data = [
							"comp_id"             => $this->company_id,
							"comp_vch_series_id"  => $series,
							"voucher_txn_id"      => $voucher_txn_id,
							"master_id"           => $val['acc_id'],
							'master_id_type'      => 'tax'
					 	];
						$txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
						$item_tax_info = $this->TransactionModel->get_item_taxinfo($val['acc_id']);
						$item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($val['acc_id'],$item_tax_info['tax_cat_id']);		
					    
						if(trim($voucher_supplytype)==13)
						  $item_tax_short_code ='DEEMEXP';
					    if(trim($voucher_supplytype)==2 || trim($voucher_supplytype)==3)
						  $item_tax_short_code ='ZERSPLY';
					    if(trim($voucher_supplytype)==4 || trim($voucher_supplytype)==5)
						  $item_tax_short_code ='SEZSPLY';
						if(trim($voucher_supplytype)==14)
						  $item_tax_short_code ='NILSPLY';
						if(trim($voucher_supplytype)==15)
						  $item_tax_short_code ='EXMSPLY';
						if(trim($voucher_supplytype)==16)
						  $item_tax_short_code ='NONGSTS';
						$insert_data   = [
							'acc_id'               => $val['acc_id'],
							'acc_igst_rate'        => $val['acc_igst_rate'] ?? 0,
							'acc_igst'             => $val['acc_igst'] ?? 0,
							'acc_cgst_rate'        => $val['acc_cgst_rate'] ?? 0,
							'acc_cgst'             => $val['acc_cgst'] ?? 0,
							'acc_sgst_rate'        => $val['acc_sgst_rate'] ?? 0,
							'acc_sgst'             => $val['acc_sgst'] ?? 0,
							'acc_cess_rate'        => 0,
							'acc_nonadv_cess_rate' => 0,
							'acc_nonadv_cess'      => 0,
							'acc_hsn_sac'          => $val['acc_hsn_sac'] ?? '',
							'vch_txn_id'           => $voucher_txn_id,
							'txn_id'               => $txn_id,
							'acc_type'             => $val['acc_type'],
							'taxable_amt'          => $val['taxable_amt'],
							'total_tax'            => $val['total_tax'],
							'cmp_tax_short_code'   => $item_tax_short_code,
							'acc_txn_date'         => date('Y-m-d',strtotime($date))
						];
		      	$this->TransactionModel->add_taxsummary_data($insert_data);
	        }

	        $insert_data = [
		        "comp_id"             => $this->company_id,
		        "comp_vch_series_id"  => $series,
		        "voucher_txn_id"      => $voucher_txn_id,
		        "master_id"           => 0,
		        'master_id_type'      => 'nrr'
		      ];

		      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		      $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_nrr);
        } 
				

				$this->aicountly_db->table($table)
							->where('impexp_sr_id',$impexp_sr_id)
							->where('imp_id',$imp_id)
							->update(['imp_status' => 1]);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function validatePurchaseItemVouchers($impexp_sr_id,$limit,$offset)
	{
		$errors = [];
		$uploaded = [];
		$vch_list = [11 => 'PURCHASE'];

		$country_code_list = ['IN'];
		$state_code_list = [];
		$tax_list = $this->TransactionModel->get_tax_list('purchase');

		$table = 'aicountly_stateslist_univdb';
		$result = $this->aicountly_db->table($table)
												->where('country_id',1)
												->get()->getResultArray();
		foreach ($result as $key => $value) {
			$state_code_list[] = intval($value['state_code']);
		}

		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
		$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

		$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$itemmaster_tbl   = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$itemtaxmst_tbl   = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
		$itmunitmst_tbl   = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');

		$itemvalmst_tbl = $this->company_id.'_itemvalmst_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');

		$table = 'aicountly_imptxnvchn_univdb';
		$sub_table = 'aicountly_imptxnvchd_univdb';
		$sub_table2 = 'aicountly_imptxnvchi_univdb';
		$sub_side_table = 'aicountly_impinwsupd_univdb';

		$result = $this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->limit($limit,$offset)
												->get()->getResultArray();

		$status = true;
		$sno = $offset;

		foreach ($result as $key => $value) {
			$sno++;

			if($value['imp_status'] == 0){

			$temp2 = $this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id',$value['imp_id'])
										->get()->getRowArray();
			$value['imptxnvch_pos'] =$temp2['impinwsup_pos'] ?? '';
			$value['imptxnvch_country_code'] = $temp2['impinwsup_country_code'] ?? '';
			
			$status = true;

			$imp_id = $value['imp_id'];
			$date = trim($value['imptxnvch_date']);
			$type = trim($value['imptxnvch_type']);
			$series = trim($value['imptxnvch_series']);
			$bill_no = trim($value['imptxnvch_bill_no']);
			$long_nrr = trim($value['imptxnvch_long_narr']);
			$pos = trim($value['imptxnvch_pos']);
			$country_code = trim($value['imptxnvch_country_code']);
			$mc = trim($value['imptxnvch_mc']);

			$comp_vch_series_id = 0;
			$voucher_type_id = 0;
			$mc_id = 0;

			if($date == null){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date is missing.';
			}

			$fy_bgn_date  = strtotime($this->session->get('ses_company_fy_beginning'));
			$fy_end_date  =strtotime($this->session->get('ses_company_fy_end'));
			$date_valid =0;
			$date_ymd  = strtotime($date);   
			if($date_ymd <= $fy_end_date)
				$date_valid =1;

			if($date_ymd >= $fy_bgn_date)
				$date_valid =1;
			
			if($status && $date_valid==0){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Date does not belong to this financial year.';
			}

			if($status && $type == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type is missing.';
			}

			if($status && $type != ''){
				if(!in_array(strtoupper($type),$vch_list)){
					$status = false;
					$type_str = implode(", ",$vch_list);
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Type "'.$type.'" must be '.$type_str;
				}
				else{
					$type = strtoupper($type);
					$voucher_type_id = array_search($type,$vch_list);
				}
			}

			if($status && $series == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series. is missing.';
			}

			if($status && $series != ''){
				
				$exists =  $this->db->table($cmpvchseri_tbl)
						->where('voucher_type_id',$voucher_type_id)
						->where('LOWER(comp_vch_series)',strtolower($series))
						->get()->getRowArray();
				if($exists){
					$comp_vch_series_id =$exists['comp_vch_series_id'];
				}
				else{
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Series "'.$series.'" does not exists.';
				}
			}

			if($status && $bill_no !=''){
			 	$exists =  $this->db->table($gstrinwsup_tbl)
						->where('LOWER(inwsup_bill_ref_no)',strtolower($bill_no))
						->get()->getRowArray();
				if($exists){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Bill No."'.$bill_no.'" already exists.';
				}		
			 }	
			 if($status && $bill_no == ''){
			 	$status = false;
			 	$errors[$imp_id] = 'S.No. '.$sno.': Bill No. is missing.';
			 }

			if($status && $mc == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center is missing.';
			}

			if($status && $mc != ''){
				$mcmasternn = $this->db->table($mcmasternn_tbl)
													->where('LOWER(mat_cent_name)', strtolower($mc))
													->get()->getRowArray();
				if(!$mcmasternn){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher Material Center "'.$mc.'" does not exists.';
				}
				else{
					$mc_id = $mcmasternn['mat_cent_id'];
				}
			}
				

			// if($status && $country_code == ''){
			// 	$status = false;
			// 	$errors[$imp_id] = 'S.No. '.$sno.': Voucher Country Code is missing.';
			// }

			// if($status && $country_code != ''){
		
			// 	if(!in_array($country_code,$country_code_list)){
			// 		$status = false;
			// 		$country_code_str=implode(", ",$country_code_list);

			// 		$errors[$imp_id] = 'S.No. '.$sno.': Invalid Country Code ';
			// 	}
			// }

			if($status && $pos == ''){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Voucher POS is missing.';
			}
			if($status && $pos != ''){
				if(!in_array($pos,$state_code_list)){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Invalid POS';
				}
			}
		

			$trans = [];
			$debit_total = 0;
			$credit_total = 0;

			if($status){
				$result2 = $this->aicountly_db->table($sub_table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$account = trim($value['imptxnvch_acc_bds']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$acc_id = 0;
					$acc_type = '';

					if($status && $account == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Account Name is missing.';
						break;
					}
					if($status && $account != ''){

						$acc_id = 0;
						$acc_type = '';

						$exists = $this->db->table($acctmaster_tbl)
								->where('LOWER(acc_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
						if($exists){
							$acc_id = $exists['acc_id'];
							$acc_type = 'acc';
						}
						else{
							$exists = $this->db->table($billsundry_tbl)
								->where('LOWER(bill_sundry_name)', strtolower(html_entity_decode($account)))
								->get()->getRowArray();
							if($exists){
								$acc_id = $exists['bill_sundry_id'];
								$acc_type = 'bsd';
							}
						}

						if($acc_id == 0 && $acc_type == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Account/Bill Sundry "'.$account.'" does not exists.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

					if($type == 'PURCHASE')
						$drcr = 'd';
					else
						$drcr = 'c';

					$amount = 0;
					if($debit > 0){
						$drcr = 'd';
						$amount = $debit;
					}
					if($credit > 0){
						$drcr = 'c';
						$amount = $credit;
					}

					$trans[] = [
						'acc_id'	 => $acc_id,
						'acc_name' => $account,
						'acc_type' => $acc_type,
						'drcr'		 => $drcr,
						'amount'	 => $amount,
						'short_nrr'	=>$short_nrr,
					];
				} 

				$result2 = $this->aicountly_db->table($sub_table2)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id',$imp_id)
												->get()->getResultArray();

				foreach ($result2 as $key => $value) {

					$item = trim($value['imptxnvch_item']);
					$debit = trim($value['imptxnvch_amt_dr']);
					$credit = trim($value['imptxnvch_amt_cr']);
					$short_nrr = trim($value['imptxnvch_short_narr']);

					$uqc = trim($value['imptxnvch_uqc_cr']);
					$qty = trim($value['imptxnvch_qty_cr']);

					$item_id = 0;
					$unit_id = 0;
		

					if($status && $item == ''){
						$status = false;
						$errors[$imp_id] = 'S.No. '.$sno.': Item Name is missing.';
						break;
					}
					if($status && $item != ''){

						$exists = $this->db->table($itemmaster_tbl)
								->where('LOWER(item_name)', strtolower($item))
								->get()->getRowArray();
						if($exists){
							$item_id = $exists['item_id'];
						}
						else{
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($item_id == 0){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item "'.$item.'" does not exists.';
							break;
						}

						if($status && $uqc == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Unit is missing.';
							break;
						}

						if($status && $uqc != ''){
							$exists = $this->db->table($itmunitmst_tbl)
								->where('LOWER(item_unit)', strtolower($uqc))
								->get()->getRowArray();
							if($exists){
								$unit_id = $exists['unit_id'];
							}
							else{
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Item Unit "'.$uqc.'" does not exists.';
								break;
							}	
						}
						if($status && $qty == ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Item Quantity is missing.';
							break;
						}
					}

					$debit = parseAmount($debit);
					$credit = parseAmount($credit);

					$debit_total += $debit;
					$credit_total += $credit;

				
					$drcr = 'd';
					$amount = $credit;
	
					$qty = parseValue($qty);
					if(($qty <= 0)){
						$qty = 1;
					}

					$trans[] = [
						'acc_id'	  => $item_id,
						'acc_name'  => $item,
						'acc_type'  => 'itm',
						'drcr'		  => 'd',
						'amount'	  => $debit,
						'short_nrr'	=> $short_nrr,
						'unit_id'		=> $unit_id,
						'qty'				=> $qty,
					];
				}
			}
				

			if($status && count($trans) == 0){
				$status = false;
				$errors[$imp_id]=$sno.'- No Transaction';
			}

			if($status && parseAmount($debit_total) != parseAmount($credit_total)){
				$status = false;
				$errors[$imp_id] = 'S.No. '.$sno.': Debit Total '.formatAmount($debit_total).' does not match Credit Total '.formatAmount($credit_total).'';
			}

			$tax_summary = [];
			$dr_acc_count = 0;
			if($status) // check tax
			{

				foreach ($trans as $k => $val){

					if($val['drcr'] == 'c'){

						if($val['acc_type'] == 'acc'){

							$errors[$imp_id] = 'S.No. '.$sno.': Credit Account '.$val['acc_name'].' is invalid';

						}


						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
					}
						
					if($val['drcr'] == 'd'){
						if($val['acc_type'] == 'bsd'){

							$billsundry = $this->db->table($billsundry_tbl)
													->where('bill_sundry_id',$val['acc_id'])
													->get()->getRowArray();
							$trans[$k]['sundry_type'] = intval($billsundry['sundry_type']);
						}
						if($val['acc_type'] == 'acc'){

							$dr_acc_count++; 

							$acctmaster = $this->db->table($acctmaster_tbl)
													->where('acc_id', $val['acc_id'])
													->get()->getRowArray();
							$acc_grp_id = $acctmaster['acc_grp_id'];
							$acc_grp_parent_id =$acctmaster['acc_grp_parent_id'];

							if($acc_grp_id == 0){ // Primary Account
								$status = false;
								$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
							}

							if($status){

								$acctgroupn=$this->db->table($acctgroupn_tbl)
													->where('acc_grp_id', $acc_grp_id)
													->get()->getRowArray();

								if($acctgroupn['under_main_grp_id'] == 0){
									if(!in_array($acc_grp_id, [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}
								else{
									if(!in_array($acctgroupn['under_main_grp_id'], [16,22,23,21])){
										$status = false;
										$errors[$imp_id] = 'S.No. '.$sno.': Debit Account '.$val['acc_name'].' should come under Trade Payable, Trade Receivable, Cash & Cash Equivalent, Bank OD/OCC';
									}
								}

							}

						}
						
					}
				}

				/* if($status && $dr_acc_count != 1){
					$status = false;
					$errors[$imp_id] = 'S.No. '.$sno.': Voucher can have one debit account';
				} */

				$tax = $tax_list;
				$tax_accounts = [];
				if($status)
				{
					$bo_state_code = $this->session->get('ses_bostecd');
					$bo_state_code = intval($bo_state_code);
					$bo_gstin_type = $this->session->get('bo_gstin_type');
					$bo_gstin_type = intval($bo_gstin_type);

					$pos = intval($pos);

					// calculate tax

					foreach ($trans as $k => $val){

						if($val['drcr'] == 'd'){

					    if($val['acc_type']=='bsd' && $val['sundry_type']=='1')
					    {
					    	$tax_accounts[] = $val;
					    }
					    else{
					    	$total_tax = 0;

					    	$summary = [];
					    	$summary['acc_id'] = $val['acc_id'];
							$summary['acc_type'] = $val['acc_type'];
					    	$summary['taxable_amt'] = $val['amount'];

					    	if($val['acc_type']=='acc'){
					    		$acctaxmstn = $this->db->table($acctaxmstn_tbl)
					    									->where('acc_id',$val['acc_id'])
					    									->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $acctaxmstn['acc_hsn_sac'] ?? '';						
					    	}
					    	if($val['acc_type']=='bsd'){
					    		$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
					    						->where('bill_sundry_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $bdstaxmstn['bill_hsn_sac'] ?? '';
					    	}
					    	if($val['acc_type']=='itm'){
					    		$itemtaxmst = $this->db->table($itemtaxmst_tbl)
					    						->where('item_id',$val['acc_id'])
					    						->get()->getRowArray();
					    		$summary['acc_hsn_sac']	= $itemtaxmst['item_hsn_sac'] ?? '';
					    	}


					    	$rates = $this->getTaxRates($val['acc_id'], $val['acc_type']);
								if(!$rates['status']){
									$status = false;
									$errors[$imp_id] = 'S.No. '.$sno.': Tax rate not defined for '.$val['acc_name'];
									break;
								}
								$tax_cat_id = $rates['tax_cat_id'];

								//tax  applicable
								if(!in_array($tax_cat_id, [16,14,8,15,18])){

									$igst_rate = parseAmount($rates['igst_rate']);
							    $cess_rate = parseAmount($rates['cess_rate']);
							    $cess_basis = $rates['cess_basis'];
							    $supply_type = $rates['supply_type'];

							    if($val['acc_type'] != 'itm'){

							    	if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    	else{
							    		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) / 100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate) / 100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{
			                	$err = $val['acc_name'].' TAX CATEGORY BELONGS NON-ADVOLEREM GST CESS WHICH CAN"T BE CALCULATED WITHOUT INVENTORY MRP';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                }
							    	}
							    }
							    else{
							    	if($bo_gstin_type == 1){
                     	$igst_rate = $igst_rate; // by tax cat id
	                  }
	                  else if($bo_gstin_type == 2){

	                    if($supply_type == 1 || $supply_type == 3){
	                      $igst_rate = $this->item_goods_rate;
	                    }
	                    else if($supply_type == 2){
	                      $igst_rate = $this->item_services_rate;
	                    }
	                  }

	                  if($pos != $bo_state_code){
							    		// 2 taxes IGST n CESS
							    		$igst = ($val['amount'] * $igst_rate)/100;
			                $igst = parseAmount($igst);

			                $tax['IGST']['status'] = 1;
			                $tax['IGST']['value'] += $igst;

			                $summary['acc_igst_rate'] = $igst_rate;
			                $summary['acc_igst'] = $igst;
			                $total_tax += $igst;
		              	}
		              	else{
		              		// 3 taxes CESS,CGST, SGST or UT-TAX
							    		$cgst_rate = parseAmount($igst_rate / 2);
							    		$cgst = ($val['amount'] * $cgst_rate) /100;
			                $cgst = parseAmount($cgst);

			                $tax['CGST']['status'] = 1;
			                $tax['CGST']['value'] += $cgst;

			                $summary['acc_cgst_rate'] = $cgst_rate;
			                $summary['acc_cgst'] = $cgst;
			                $total_tax += $cgst;

			                $ut_arr = [35,4,26,25,31,38,34,97];
			                if(!in_array($pos, $ut_arr)){
			               
			                  $tax['SGST']['status'] = 1;
			                  $tax['SGST']['value'] += $cgst;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }
			                else{
			                  $ut_tax = $cgst;
			                  $tax['UT-TAX']['status'] = 1;
			                  $tax['UT-TAX']['value'] += $ut_tax;

			                  $summary['acc_sgst_rate'] = $cgst_rate;
			                	$summary['acc_sgst'] = $cgst;
			                	$total_tax += $cgst;
			                }

			                // CESS
			                if($cess_basis == 1){
			                  $cess = ($val['amount'] * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
			                else{

			                	$mrp = 0;
			                	$itemvalmst = $this->db->table($itemvalmst_tbl)
			                			->where('item_id', $item_id)
			                			->get()->getRowArray();
			                	if($itemvalmst){
			                		$mrp = parseAmount($itemvalmst['item_mrp']);
			                	}
			                	else{
			                		$err = $val['acc_name'].' Item MRP does not exists';
			                  $tax['CESS']['status'] = 2;
			                  $tax['CESS']['error'] = $err;
			                	}

			                	$cess = ($mrp * $cess_rate)/100;
			                  $cess = parseAmount($cess);

			                  $tax['CESS']['status'] = 1;
			                  $tax['CESS']['value'] += $cess;

			                  $summary['acc_cess_basis'] = $cess_basis;
			                  $summary['acc_cess_rate'] = $cess_rate;
			                	$summary['acc_cess'] = $cess;
			                	$total_tax += $cess;
			                }
		              	}
							    }

						    	$summary['total_tax'] = $total_tax;
						    	$account_summary[] = $summary;

						    }
					    } // else
					  }
					}
	
					if($status) // check for errors
					{
						$err = '';
						foreach ($tax as $k => $t) {

							if($t['status'] == 1){

								if($t['id'] > 0){
									$index = array_search($t['id'], array_column($tax_accounts, 'acc_id'));

									if($index != ''){
										$obj = $tax_accounts[$index];
				            if(parseAmount($obj['amount']) < parseAmount($t['value']) || parseAmount($obj['amount']) > parseAmount($t['value']))
				            {
				              $err .= '<br>'.$t['name'].' should be equal to '. formatAmount($t['value']) .' CR';
				            }
									}
									else{
										$err .= '<br>'.$t['name'].' is required, MIN '. formatAmount($t['value']) .' CR';
									}
								}
								else{
									$err .= '<br>'.$k.' Master is not created, MIN '. formatAmount($t['value']) .' CR';
								}
							}
							if($t['status'] == 2){
								if($t['id'] > 0){
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
								else{
									$err .= '<br>'.$k.' MIN '. formatAmount($t['value']) .' CR but'.$t['error'] ;
								}
							}
						}

						if($err != ''){
							$status = false;
							$errors[$imp_id] = 'S.No. '.$sno.': Incorrect Tax '.$err;
						}

					}
				}
			}

			if($status) 
			{
				$date = validate_date_by_fy($date);
				$vno = $this->TransactionModel->get_voucher_no($voucher_type_id);

				$uploaded[$imp_id] = 'S.No. '.$sno.': '.$type.' ('.$vno.') Dated '.$date;
			}

			}
		}

		return ['uploaded' => $uploaded, 'errors' => $errors];
	}
	
	function getTaxRates($acc_id, $acc_type)
	{
		$igst_rate = 0;
		$cess_rate = 0;
		$cess_basis = 0;
		$supply_type = 1;
		$tax_cat_id = 0;
		$status = 0;

        $cmp_tax_cat_id = 0;
		if($acc_type == 'acc'){
			 
			$acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
			$acctaxmstn = $this->db->table($acctaxmstn_tbl)
													->where('acc_id', $acc_id)
													->get()->getRowArray();
													
			if(!empty($acctaxmstn['cmp_tax_cat_id'])){
				$cmp_tax_cat_id = $acctaxmstn['cmp_tax_cat_id'];
				$supply_type = $acctaxmstn['acc_supply_type'];
				$tax_cat_id = $acctaxmstn['tax_cat_id'];
			}
		}

		if($acc_type == 'bsd'){

			$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
			$bdstaxmstn = $this->db->table($bdstaxmstn_tbl)
													->where('bill_sundry_id', $acc_id)
													->get()->getRowArray();
															
			if(!empty($bdstaxmstn['cmp_tax_cat_id'])){
				$cmp_tax_cat_id = $bdstaxmstn['cmp_tax_cat_id'];
				$supply_type = $bdstaxmstn['bill_supply_type'];
				$tax_cat_id = $bdstaxmstn['tax_cat_id'];
			}
		}

		if($acc_type == 'itm'){

			$itemtaxmst_tbl = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
			$itemtaxmst = $this->db->table($itemtaxmst_tbl)
													->where('item_id', $acc_id)
													->get()->getRowArray();
			if(!empty($itemtaxmst['cmp_tax_cat_id'])){
				$cmp_tax_cat_id = $itemtaxmst['cmp_tax_cat_id'];
				$supply_type = $itemtaxmst['item_supply_type'];
				$tax_cat_id = $itemtaxmst['tax_cat_id'];
			}
		}

		if($cmp_tax_cat_id != 0){
			$cmpgstcatn_tbl = $this->company_id.'_cmpgstcatn_'.$this->session->get('ses_comp_fy_id');
			$cmpgstcatn = $this->db->table($cmpgstcatn_tbl)
													->where('cmp_tax_cat_id',$cmp_tax_cat_id)
													->get()->getRowArray();

			if($cmpgstcatn){
				$status = true;
				
				$igst_rate = floatval($cmpgstcatn['cmp_tax_cat_igst']);
				$cess_rate = floatval($cmpgstcatn['cmp_tax_cat_cess']);
				if($cmpgstcatn['cmp_tax_cat_cess_basis']!='')
				$cess_basis= intval($cmpgstcatn['cmp_tax_cat_cess_basis']);
			    else 
				$cess_basis=0;
			}
		}

		if($supply_type == 0){
			$supply_type = 1;
		}

		return [
			'igst_rate' 	=> $igst_rate,
			'cess_rate' 	=> $cess_rate,
			'cess_basis' 	=> $cess_basis,
			'tax_cat_id' 	=> $tax_cat_id,
			'supply_type'   => $supply_type,
			'status' 		=> $status,
		]; 
	}


	function deleteMaster($impexp_sr_id,$impexp_type)
	{ 

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
		
		
		if(in_array($impexp_type, [10])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}

		if(in_array($impexp_type, [11])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_table2 = 'aicountly_imptxnvchi_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
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
	}

	function save_editing_records($impexp_sr_id,$impexp_type,$data)
	{
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
		if(in_array($impexp_type, [10])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}
		if(in_array($impexp_type, [11])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_table2 = 'aicountly_imptxnvchi_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}

		
		unset($data['pq_rowcls']);
		unset($data['rd_status']);
		unset($data['pq_rowselect']);
		unset($data['pq_cellattr']);
		unset($data['impacc_name_sug_list']);
        unset($data['tooltip_exists']);
		unset($data['impacc_name_sug']);
		unset($data['impacc_name_sug_id']);

		if(in_array($impexp_type, [1,2,3,4,5])){
			unset($data['sno']);

			$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($data);
		}

		if(in_array($impexp_type, [6,7])){
			if(!empty($data['sno'])){

				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$final2 = [
					'impexp_sr_id'        => $impexp_sr_id,
          'imp_id'              => $data['imp_id'],
          'imp_sub_id'          => 1,
          'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
					$final = [
						'imptxnvch_long_narr' =>	$data['imptxnvch_acc_bds']
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					$exists = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->orderBy('imp_sub_id', 'desc')
										->limit(1)
										->get()->getRowArray();
					if($exists){
						$imp_sub_id = intval($exists['imp_sub_id'])+1;
						$imptxnvch_id = $exists['imptxnvch_id'];
					}
					else{
						$imp_sub_id = 1;
						$imptxnvch_id = 0;
					}
					$final2 = [
						'impexp_sr_id'        => $impexp_sr_id,
          	'imp_id'              => $data['imp_id'],
          	'imp_sub_id'          => $imp_sub_id,
          	'imptxnvch_id'        => $imptxnvch_id,
						'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
						'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
						'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
						'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
					];
					
					$this->aicountly_db->table($sub_table)
										->insert($final2);
				}
			}
		}

		if(in_array($impexp_type, [8])){
			if(!empty($data['sno'])){

				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impoutsup_pos' => $data['imptxnvch_posid'] ?? '',
					'impoutsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impout_supply_type' => $data['imptxnvch_supplytype_id'] ?? ''
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$final2 = [
					'impexp_sr_id'        => $impexp_sr_id,
					'imp_id'              => $data['imp_id'],
					'imp_sub_id'          => 1,
					'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
					$final = [
						'imptxnvch_long_narr' =>	$data['imptxnvch_acc_bds']
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					$exists = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->orderBy('imp_sub_id', 'desc')
										->limit(1)
										->get()->getRowArray();
					if($exists){
						$imp_sub_id = intval($exists['imp_sub_id'])+1;
						$imptxnvch_id = $exists['imptxnvch_id'];
					}
					else{
						$imp_sub_id = 1;
						$imptxnvch_id = 0;
					}
					$final2 = [
						'impexp_sr_id'        => $impexp_sr_id,
						'imp_id'              => $data['imp_id'],
						'imp_sub_id'          => $imp_sub_id,
						'imptxnvch_id'        => $imptxnvch_id,
						'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
						'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
						'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
						'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
					];
					
					$this->aicountly_db->table($sub_table)
										->insert($final2);
				}
			}
		}

		if(in_array($impexp_type, [9])){
			if(!empty($data['sno'])){

				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
					'imptxnvch_mc' => $data['imptxnvch_mc'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impoutsup_pos' => $data['imptxnvch_posid'] ?? '',
					'impoutsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impout_supply_type' => $data['imptxnvch_supplytype_id'] ?? ''
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$this->aicountly_db->table($sub_table2)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$final2 = [
					'impexp_sr_id'        => $impexp_sr_id,
					'imp_id'              => $data['imp_id'],
					'imp_sub_id'          => 1,
					'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
					$final = [
						'imptxnvch_long_narr' =>	$data['imptxnvch_acc_bds']
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					if(empty($data['imptxnvch_uqc']) || $data['imptxnvch_uqc'] == '')
					{
						$exists = $this->aicountly_db->table($sub_table)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}

						$final2 = [
							'impexp_sr_id'        => $impexp_sr_id,
							'imp_id'              => $data['imp_id'],
							'imp_sub_id'          => $imp_sub_id,
							'imptxnvch_id'        => $imptxnvch_id,
							'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table)
											->insert($final2);
					}
					else{
						$exists = $this->aicountly_db->table($sub_table2)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}


					$vch =	$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id', $data['imp_id'])
												->where('imp_status', 0)
												->get()->getRowArray();
					$mc = $vch['imptxnvch_mc'];

						$final2 = [
							'impexp_sr_id'        => $impexp_sr_id,
							'imp_id'              => $data['imp_id'],
							'imp_sub_id'          => $imp_sub_id,
							'imptxnvch_id'        => $imptxnvch_id,
							'imptxnvch_item' 			=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_uqc_dr' 		=> '',
							'imptxnvch_uqc_cr' 		=> $data['imptxnvch_uqc']?? '',
							'imptxnvch_qty_dr' 		=> '',
							'imptxnvch_qty_cr' 		=> $data['imptxnvch_qty'] ?? 1,
							'imptxnvch_mc_dr' 		=> '',
							'imptxnvch_mc_cr' 		=> $mc,
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table2)
											->insert($final2);
					}
						
				}
			}
		}
		if(in_array($impexp_type, [10])){
			if(!empty($data['sno'])){

				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impinwsup_pos' => $data['imptxnvch_posid'] ?? '',
					'impinwsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impinw_supply_type' => $data['imptxnvch_supplytype_id'] ?? ''
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$final2 = [
					'impexp_sr_id'        => $impexp_sr_id,
					  'imp_id'              => $data['imp_id'],
					  'imp_sub_id'          => 1,
					  'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
					$final = [
						'imptxnvch_long_narr' =>	$data['imptxnvch_acc_bds']
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					$exists = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->orderBy('imp_sub_id', 'desc')
										->limit(1)
										->get()->getRowArray();
					if($exists){
						$imp_sub_id = intval($exists['imp_sub_id'])+1;
						$imptxnvch_id = $exists['imptxnvch_id'];
					}
					else{
						$imp_sub_id = 1;
						$imptxnvch_id = 0;
					}
					$final2 = [
						'impexp_sr_id'        => $impexp_sr_id,
						'imp_id'              => $data['imp_id'],
						'imp_sub_id'          => $imp_sub_id,
						'imptxnvch_id'        => $imptxnvch_id,
						'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
						'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
						'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
						'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
					];
					
					$this->aicountly_db->table($sub_table)
										->insert($final2);
				}
			}
		}
		if(in_array($impexp_type, [11])){
			if(!empty($data['sno'])){

				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
					'imptxnvch_mc' => $data['imptxnvch_mc'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impinwsup_pos' => $data['imptxnvch_posid'] ?? '',
					'impinwsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impinw_supply_type' => $data['imptxnvch_supplytype_id'] ?? ''
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$this->aicountly_db->table($sub_table2)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$final2 = [
					'impexp_sr_id'        => $impexp_sr_id,
					  'imp_id'              => $data['imp_id'],
					  'imp_sub_id'          => 1,
					  'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
					$final = [
						'imptxnvch_long_narr' =>	$data['imptxnvch_acc_bds']
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					if(empty($data['imptxnvch_uqc']) || $data['imptxnvch_uqc'] == '')
					{
						$exists = $this->aicountly_db->table($sub_table)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}

						$final2 = [
							'impexp_sr_id'        => $impexp_sr_id,
							'imp_id'              => $data['imp_id'],
							'imp_sub_id'          => $imp_sub_id,
							'imptxnvch_id'        => $imptxnvch_id,
							'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table)
											->insert($final2);
					}
					else{
						$exists = $this->aicountly_db->table($sub_table2)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}


					$vch =	$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id', $data['imp_id'])
												->where('imp_status', 0)
												->get()->getRowArray();
					$mc = $vch['imptxnvch_mc'];

						$final2 = [
							'impexp_sr_id'        => $impexp_sr_id,
							'imp_id'              => $data['imp_id'],
							'imp_sub_id'          => $imp_sub_id,
							'imptxnvch_id'        => $imptxnvch_id,
							'imptxnvch_item' 			=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_uqc_dr' 		=> '',
							'imptxnvch_uqc_cr' 		=> $data['imptxnvch_uqc']?? '',
							'imptxnvch_qty_dr' 		=> '',
							'imptxnvch_qty_cr' 		=> $data['imptxnvch_qty'] ?? 1,
							'imptxnvch_mc_dr' 		=> '',
							'imptxnvch_mc_cr' 		=> $mc,
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table2)
											->insert($final2);
					}
						
				}
			}
		}
		
	}

  function save_editing_suggestion_records($impexp_sr_id,$impexp_type,$data)
	{$final_data=[];
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
		if(in_array($impexp_type, [10])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}
		if(in_array($impexp_type, [11])){
			$table = 'aicountly_imptxnvchn_univdb';
			$sub_table = 'aicountly_imptxnvchd_univdb';
			$sub_table2 = 'aicountly_imptxnvchi_univdb';
			$sub_side_table = 'aicountly_impinwsupd_univdb';
		}
		
		unset($data['pq_rowcls']);
		unset($data['rd_status']);
		unset($data['pq_rowselect']);
		unset($data['pq_cellattr']);
		unset($data['impacc_prt_sug_list']);
		unset($data['impacc_grp_sug_list']);
        unset($data['tooltip_exists']);
		unset($data['impacc_prt_sug']);
		unset($data['impacc_grp_sug']);		
		unset($data['impaccgrp_under_prt_sug']);
		unset($data['impaccgrp_under_grp_sug']);
		unset($data['impaccgrp_under_prt_sug_list']);
		unset($data['impaccgrp_under_grp_sug_list']);		
		unset($data['impitmgrp_under_grp_sug']);
		unset($data['impitmgrp_under_grp_sug_list']);		
		unset($data['impitm_cat_sug']);
		unset($data['impitm_sale_acc_sug']);
		unset($data['impitm_pur_acc_sug']);		
		unset($data['impitm_cat_sug_list']);
		unset($data['impitm_sale_acc_list']);
		unset($data['impitm_pur_acc_sug_list']);		
		unset($data['imptxnvch_acc_bds_sug']);
		unset($data['imptxnvch_acc_bds_sug_list']);		
		unset($data['imptxnvch_series_sug']);
		unset($data['imptxnvch_series_sug_list']);
		
		if(in_array($impexp_type, [1,2,3,4,5])){
			unset($data['sno']);
			if($impexp_type == 1){
			   foreach($data as $fldkey => $value){				
				if($fldkey=='impacc_prt_sug_id' && $value!=''){
				   $data['impacc_prt']	=$value;
				}
				 if($fldkey=='impacc_grp_sug_id' && $value!=''){
				   $data['impacc_grp']	=$value;
				  }			  
			    }
				unset($data['impacc_grp_sug_id']);
				unset($data['impacc_prt_sug_id']);
			  }
			  if($impexp_type == 2){
			   foreach($data as $fldkey => $value){				
				if($fldkey=='impaccgrp_under_prt_sug_id' && $value!=''){
				   $data['impaccgrp_under_prt']	=$value;
				}
				 if($fldkey=='impaccgrp_under_grp_sug_id' && $value!=''){
				   $data['impaccgrp_under_grp']	=$value;
				  }			  
			    }
				unset($data['impaccgrp_under_grp_sug_id']);
				unset($data['impaccgrp_under_prt_sug_id']);
			  }
			  if($impexp_type == 3){
			   foreach($data as $fldkey => $value){				
				if($fldkey=='impitm_cat_sug_id' && $value!=''){
				   $data['impitm_cat']	=$value;
				}
				 if($fldkey=='impitm_pur_acc_sug_id' && $value!=''){
				   $data['impitm_pur_acc']	=$value;
				  }
				if($fldkey=='impitm_sale_acc_sug_id' && $value!=''){
				   $data['impitm_sale_acc']	=$value;
				  }	  
			    }
				unset($data['impitm_cat_sug_id']);
				unset($data['impitm_pur_acc_sug_id']);
				unset($data['impitm_sale_acc_sug_id']);
			  }
			  if($impexp_type == 4){
			   foreach($data as $fldkey => $value){				
				if($fldkey=='impitmgrp_under_grp_sug_id' && $value!=''){
				   $data['impitmgrp_under_grp']	=$value;
				}				
			    }
				unset($data['impitmgrp_under_grp_sug_id']);				
			  }

			$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($data);
		}

		if(in_array($impexp_type, [6,7])){
			
			if(!empty($data['sno'])){
				
				if($impexp_type==6 || $impexp_type==7){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_series_sug_id' && $value!=''){
					   $data['imptxnvch_series']	=$value;
					}					
			      }
				 
				}
			  
				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();
										
				if($impexp_type==6 || $impexp_type==7){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}			
			    
				
				$final2 = [
					 'impexp_sr_id'        => $impexp_sr_id,
					  'imp_id'              => $data['imp_id'],
					  'imp_sub_id'          => 1,
					  'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
				  if(!empty($data['imptxnvch_acc_bds']))
					  $imptxnvch_acc_bds = $data['imptxnvch_acc_bds'];
				   else
					  $imptxnvch_acc_bds = '';
				  
					$final = [
						'imptxnvch_long_narr' =>	$imptxnvch_acc_bds
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					$exists = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->orderBy('imp_sub_id', 'desc')
										->limit(1)
										->get()->getRowArray();
					if($exists){
						$imp_sub_id = intval($exists['imp_sub_id'])+1;
						$imptxnvch_id = $exists['imptxnvch_id'];
					}
					else{
						$imp_sub_id = 1;
						$imptxnvch_id = 0;
					}
					
					if($impexp_type==6 || $impexp_type==7){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
				
					
					$final2 = [
						'impexp_sr_id'        => $impexp_sr_id,
						'imp_id'              => $data['imp_id'],
						'imp_sub_id'          => $imp_sub_id,
						'imptxnvch_id'        => $imptxnvch_id,
						'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
						'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
						'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
						'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
					];
					
					$this->aicountly_db->table($sub_table)
										->insert($final2);
				}
			}
		}

		if(in_array($impexp_type, [8])){
			if(!empty($data['sno'])){
				if($impexp_type==8){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_series_sug_id' && $value!=''){
					   $data['imptxnvch_series']	=$value;
					}					
			      }
				 
				}
				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impoutsup_pos' => $data['imptxnvch_pos'] ?? '',
					'impoutsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impout_supply_type' => $data['imptxnvch_supplytype'] ?? '',
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();
        
		if($impexp_type==8){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
				
		
		$final2 = [
				    'impexp_sr_id'         => $impexp_sr_id,
				    'imp_id'               => $data['imp_id'],
				    'imp_sub_id'           => 1,
				    'imptxnvch_id'         => $data['imptxnvch_id'],
					'imptxnvch_acc_bds'    => $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 	   => floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 	   => floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
				  if(isset($data['imptxnvch_acc_bds'])){
					$final=['imptxnvch_long_narr'=>$data['imptxnvch_acc_bds']];
				     }else{
					 $final=['imptxnvch_long_narr' =>""];	 
					 }
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					$exists = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->orderBy('imp_sub_id', 'desc')
										->limit(1)
										->get()->getRowArray();
					if($exists){
						$imp_sub_id = intval($exists['imp_sub_id'])+1;
						$imptxnvch_id = $exists['imptxnvch_id'];
					}
					else{
						$imp_sub_id = 1;
						$imptxnvch_id = 0;
					}
			if($impexp_type==8){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}		
					$final2 = [
						'impexp_sr_id'        => $impexp_sr_id,
          	'imp_id'              => $data['imp_id'],
          	'imp_sub_id'          => $imp_sub_id,
          	'imptxnvch_id'        => $imptxnvch_id,
						'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
						'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
						'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
						'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
					];
					
					$this->aicountly_db->table($sub_table)
										->insert($final2);
				}
			}
		}

		if(in_array($impexp_type, [9])){
			if(!empty($data['sno'])){
				if($impexp_type==9){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_series_sug_id' && $value!=''){
					   $data['imptxnvch_series']	=$value;
					}					
			      }
				 
				}
				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
					'imptxnvch_mc' => $data['imptxnvch_mc'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impoutsup_pos' => $data['imptxnvch_pos'] ?? '',
					'impoutsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impout_supply_type' => $data['imptxnvch_supplytype'] ?? ''					
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$this->aicountly_db->table($sub_table2)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();
				if($impexp_type==9){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
				$final2 = [
					'impexp_sr_id'        => $impexp_sr_id,
					  'imp_id'              => $data['imp_id'],
					  'imp_sub_id'          => 1,
					  'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
				if(!empty($data['imptxnvch_acc_bds']))
						$imptxnvch_acc_bds = $data['imptxnvch_acc_bds'];
					else
						$imptxnvch_acc_bds = '';
					$final = [
						'imptxnvch_long_narr' =>$imptxnvch_acc_bds
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					if(empty($data['imptxnvch_uqc']) || $data['imptxnvch_uqc'] == '')
					{
						$exists = $this->aicountly_db->table($sub_table)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}
					if($impexp_type==9){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
				
						$final2 = [
							'impexp_sr_id'        => $impexp_sr_id,
							'imp_id'              => $data['imp_id'],
							'imp_sub_id'          => $imp_sub_id,
							'imptxnvch_id'        => $imptxnvch_id,
							'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table)
											->insert($final2);
					}
					else{
						$exists = $this->aicountly_db->table($sub_table2)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}


					$vch =	$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id', $data['imp_id'])
												->where('imp_status', 0)
												->get()->getRowArray();
					$mc = $vch['imptxnvch_mc'];
					if($impexp_type==9){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
						$final2 = [
							'impexp_sr_id'          => $impexp_sr_id,
							'imp_id'                => $data['imp_id'],
							'imp_sub_id'            => $imp_sub_id,
							'imptxnvch_id'          => $imptxnvch_id,
							'imptxnvch_item' 		=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_uqc_dr' 		=> '',
							'imptxnvch_uqc_cr' 		=> $data['imptxnvch_uqc']?? '',
							'imptxnvch_qty_dr' 		=> '',
							'imptxnvch_qty_cr' 		=> $data['imptxnvch_qty'] ?? 1,
							'imptxnvch_mc_dr' 		=> '',
							'imptxnvch_mc_cr' 		=> $mc,
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table2)
											->insert($final2);
					}
						
				}
			}
		}
		
		if(in_array($impexp_type, [10])){
			if(!empty($data['sno'])){
				if($impexp_type==10){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_series_sug_id' && $value!=''){
					   $data['imptxnvch_series']	=$value;
					}					
			      }
				 
				}
				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impinwsup_pos' => $data['imptxnvch_pos'] ?? '',
					'impinwsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impinw_supply_type' => $data['imptxnvch_supplytype'] ?? '',
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();
        
		if($impexp_type==10){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
				
		
		$final2 = [
				    'impexp_sr_id'         => $impexp_sr_id,
				    'imp_id'               => $data['imp_id'],
				    'imp_sub_id'           => 1,
				    'imptxnvch_id'         => $data['imptxnvch_id'],
					'imptxnvch_acc_bds'    => $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 	   => floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 	   => floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
				  if(isset($data['imptxnvch_acc_bds'])){
					$final=['imptxnvch_long_narr'=>$data['imptxnvch_acc_bds']];
				     }else{
					 $final=['imptxnvch_long_narr' =>""];	 
					 }
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					$exists = $this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->orderBy('imp_sub_id', 'desc')
										->limit(1)
										->get()->getRowArray();
					if($exists){
						$imp_sub_id = intval($exists['imp_sub_id'])+1;
						$imptxnvch_id = $exists['imptxnvch_id'];
					}
					else{
						$imp_sub_id = 1;
						$imptxnvch_id = 0;
					}
			if($impexp_type==10){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}		
					$final2 = [
						'impexp_sr_id'        => $impexp_sr_id,
						'imp_id'              => $data['imp_id'],
						'imp_sub_id'          => $imp_sub_id,
						'imptxnvch_id'        => $imptxnvch_id,
						'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
						'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
						'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
						'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
					];
					
					$this->aicountly_db->table($sub_table)
										->insert($final2);
				}
			}
		}
		
		if(in_array($impexp_type, [11])){
			if(!empty($data['sno'])){
				if($impexp_type==11){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_series_sug_id' && $value!=''){
					   $data['imptxnvch_series']	=$value;
					}					
			      }
				 
				}
				$final = [
					'imptxnvch_date' => $data['imptxnvch_date'],
					'imptxnvch_type' => $data['imptxnvch_type'],
					'imptxnvch_series' => $data['imptxnvch_series'],
					'imptxnvch_bill_no' => $data['imptxnvch_bill_no'] ?? '',
					'imptxnvch_mc' => $data['imptxnvch_mc'] ?? '',
				];

				$this->aicountly_db->table($table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->where('imp_status', 0)
										->update($final);

				$final1 = [
					'impinwsup_pos' => $data['imptxnvch_pos'] ?? '',
					'impinwsup_country_code' => $data['imptxnvch_country_code'] ?? '',
					'impinw_supply_type' => $data['imptxnvch_supplytype'] ?? '',
				];
				$this->aicountly_db->table($sub_side_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->update($final1);

				$this->aicountly_db->table($sub_table)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();

				$this->aicountly_db->table($sub_table2)
										->where('impexp_sr_id',$impexp_sr_id)
										->where('imp_id', $data['imp_id'])
										->delete();
				if($impexp_type==11){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
				$final2 = [
					'impexp_sr_id'        => $impexp_sr_id,
					  'imp_id'              => $data['imp_id'],
					  'imp_sub_id'          => 1,
					  'imptxnvch_id'        => $data['imptxnvch_id'],
					'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
					'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
					'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
					'imptxnvch_short_narr' => $data['imptxnvch_short_narr'],
				];

				$this->aicountly_db->table($sub_table)
										->insert($final2);
			}
			else{
				if($data['imp_sub_id'] == -1){ // narration
				if(!empty($data['imptxnvch_acc_bds']))
						$imptxnvch_acc_bds = $data['imptxnvch_acc_bds'];
					else
						$imptxnvch_acc_bds = '';
					$final = [
						'imptxnvch_long_narr' =>$imptxnvch_acc_bds
						];
					$this->aicountly_db->table($table)
									->where('impexp_sr_id',$impexp_sr_id)
									->where('imp_id', $data['imp_id'])
									->where('imp_status', 0)
									->update($final);
				} 
				else{

					if(empty($data['imptxnvch_uqc']) || $data['imptxnvch_uqc'] == '')
					{
						$exists = $this->aicountly_db->table($sub_table)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}
					if($impexp_type==11){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
				
						$final2 = [
							'impexp_sr_id'        => $impexp_sr_id,
							'imp_id'              => $data['imp_id'],
							'imp_sub_id'          => $imp_sub_id,
							'imptxnvch_id'        => $imptxnvch_id,
							'imptxnvch_acc_bds' 	=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table)
											->insert($final2);
					}
					else{
						$exists = $this->aicountly_db->table($sub_table2)
											->where('impexp_sr_id',$impexp_sr_id)
											->where('imp_id', $data['imp_id'])
											->orderBy('imp_sub_id', 'desc')
											->limit(1)
											->get()->getRowArray();
						if($exists){
							$imp_sub_id = intval($exists['imp_sub_id'])+1;
							$imptxnvch_id = $exists['imptxnvch_id'];
						}
						else{
							$imp_sub_id = 1;
							$imptxnvch_id = 0;
						}


					$vch =	$this->aicountly_db->table($table)
												->where('impexp_sr_id',$impexp_sr_id)
												->where('imp_id', $data['imp_id'])
												->where('imp_status', 0)
												->get()->getRowArray();
					$mc = $vch['imptxnvch_mc'];
					if($impexp_type==11){
			     foreach($data as $fldkey => $value){				
					if($fldkey=='imptxnvch_acc_bds_sug_id' && $value!=''){
					   $data['imptxnvch_acc_bds']	=$value;
					}				
			      }
				}
						$final2 = [
							'impexp_sr_id'          => $impexp_sr_id,
							'imp_id'                => $data['imp_id'],
							'imp_sub_id'            => $imp_sub_id,
							'imptxnvch_id'          => $imptxnvch_id,
							'imptxnvch_item' 		=> $data['imptxnvch_acc_bds'],
							'imptxnvch_amt_dr' 		=> floatval($data['imptxnvch_amt_dr'] ?? 0),
							'imptxnvch_amt_cr' 		=> floatval($data['imptxnvch_amt_cr'] ?? 0),
							'imptxnvch_uqc_dr' 		=> '',
							'imptxnvch_uqc_cr' 		=> $data['imptxnvch_uqc']?? '',
							'imptxnvch_qty_dr' 		=> '',
							'imptxnvch_qty_cr' 		=> $data['imptxnvch_qty'] ?? 1,
							'imptxnvch_mc_dr' 		=> '',
							'imptxnvch_mc_cr' 		=> $mc,
							'imptxnvch_short_narr' => $data['imptxnvch_short_narr'] ?? '',
						];
						
						$this->aicountly_db->table($sub_table2)
											->insert($final2);
					}
						
				}
			}
		}
		
	}	

 }