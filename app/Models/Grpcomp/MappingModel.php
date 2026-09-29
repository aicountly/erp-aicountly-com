<?php
namespace App\Models\Grpcomp;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class MappingModel extends Model	{
	public function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		$this->db       = $this->externaldb->grp_comp_db();
		$this->erp_db   =  $this->externaldb->erp_db();
		$this->aicountly_db   =  $this->externaldb->aicountly_db();
		$this->folder_path    = getenv('GroupPath');
		$this->base_url      =  base_url().'/'.getenv('GroupPath');
		$this->session       =  \Config\Services::session();
		$this->uuid       =  $this->session->get('uuid');
		$this->enc_string    =  new enc_string();

		$this->grp_comp_id = $this->session->get('grp_comp_id');
		$this->grp_comp_fy_id = $this->session->get('grp_comp_fy_id');
	}

	function mappedGroupData($master_type){

		$grpmapping_tbl = 'grpmapping';

		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', $master_type)
												->where('crs_master_id !=', 0)
												->get()->getResultArray();

		foreach ($result as $key => $value) {
			$comp_name = $this->get_comp_name($value['comp_id']);
			$comp_master_name = $this->get_comp_master_name($value['comp_id'],$master_type,$value['master_id']);

			$result[$key]['old_master_name'] = $value['master_name'];
			$result[$key]['comp_name'] = $comp_name;
			$result[$key]['comp_master_name'] = $comp_master_name; 
		}
		
		return $result;
 	}

 	function unmappedGroupData($master_type){

		$grpcomstid_tbl = 'grpcomstid';

		$result = $this->db->table($grpcomstid_tbl)
												->where('crs_master_type', $master_type)
												->where('comp_id_str','')
												->get()->getResultArray();
		
		return $result;
 	}

 	function mappedMemberData($master_type){

		$grpmapping_tbl = 'grpmapping';

		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', $master_type)
												->where('crs_master_id !=', 0)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();

		foreach ($result as $key => $value) {
			$comp_name = $this->get_comp_name($value['comp_id']);
			$comp_master_name = $this->get_comp_master_name($value['comp_id'],$master_type,$value['master_id']);

			$result[$key]['old_master_name'] = $value['master_name'];
			$result[$key]['comp_name'] = $comp_name;
			$result[$key]['comp_master_name'] = $comp_master_name; 
		}
		
		return $result;
 	}

 	function unmappedMemberData($master_type){

		$grpmapping_tbl = 'grpmapping';

		$result = $this->db->table($grpmapping_tbl)
												->where('master_type', $master_type)
												->where('crs_master_id', 0)
												->orderBy('crs_master_id', 'asc')
												->get()->getResultArray();

		foreach ($result as $key => $value) {
			$comp_name = $this->get_comp_name($value['comp_id']);
			$comp_master_name = $this->get_comp_master_name($value['comp_id'],$master_type,$value['master_id']);

			$result[$key]['crs_master_id'] = '';
			$result[$key]['comp_name'] = $comp_name;
			$result[$key]['comp_master_name'] = $comp_master_name; 
		}
		
		return $result;
 	}

 	function crs_master_list($type)
	{
		$grpcomstid_tbl = 'grpcomstid';

		$result = $this->db->table($grpcomstid_tbl)
												->select('crs_master_id, crs_master_name as label, crs_master_name as value')
												->where('crs_master_type', $type)
												->get()->getResultArray();
		
		return $result;
	}

 	function get_comp_name($comp_id){
 		$comp_name = '';
 		$comp = $this->aicountly_db->table("aicountly_cmpmastern_univdb")
									->select('comp_name')
									->where('comp_id',$comp_id)
									->where('comp_db_status','active')
									->get()->getRowArray();
		if($comp){
			$comp_name = $comp['comp_name'];
		}
		return $comp_name;
 	}

 	function get_comp_master_name($comp_id,$type,$master_id)
 	{
 		// mst base id get uuid

 		$master_name = '';

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

    $comp_fy_id = $this->get_comp_fy_id($comp_id);

 		$comp_db = $this->externaldb->comp_db($comp_id);

 		$table = $comp_id.'_'.$type.'_'.$comp_fy_id;
		$result = $comp_db->table($table)
											// ->where($array[$type]['id'],$master_id)
											->where('mst_base_id',$master_id)
											->get()->getRowArray();
		if($result){
			$master_name = $result[$array[$type]['name']];

		}

		return $master_name;
 	}

 	function get_comp_fy_id($comp_id)
 	{
 		$comp_fy_id = 0;
 		$result=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$this->grp_comp_id)
											->where('grp_fy_id',$this->grp_comp_fy_id)
											->where('comp_id', $comp_id)
											->get()->getRowArray();
		if($result)
			$comp_fy_id =  $result['comp_fy_id'];

		return $comp_fy_id;
 	}

 	function update_master_name($crs_master_id,$master_name)
 	{
 		$grpmapping_tbl = 'grpmapping';

		$this->db->table($grpmapping_tbl)
							->where('crs_master_id', $crs_master_id)
							->update(['master_name' => $master_name]);

		$grpcomstid_tbl = 'grpcomstid';

		$this->db->table($grpcomstid_tbl)
							->where('crs_master_id', $crs_master_id)
							->update(['crs_master_name' => $master_name]);
 	}

 	function delete_master_mapping($crs_master_id,$comp_id)
 	{
 		$grpmapping_tbl = 'grpmapping';

		$this->db->table($grpmapping_tbl)
							->where('crs_master_id', $crs_master_id)
							->where('comp_id', $comp_id)
							->update(['crs_master_id' => 0, 'master_name' => '', 'parent_id' => 0, 'parent' => '']);

		$grpcomstid_tbl = 'grpcomstid';

		$result = $this->db->table($grpcomstid_tbl)
												->where('crs_master_id', $crs_master_id)
												->get()->getRowArray();

		if($result){
			$comp_id_str = trim($result['comp_id_str']);

			if($comp_id_str != ''){
				$comp_id_arr = explode(',', $comp_id_str);
				$i = array_search($comp_id,$comp_id_arr);
				unset($comp_id_arr[$i]);
				$comp_id_str2 = implode(',', $comp_id_arr);
				$comp_id_str2 = trim($comp_id_str2);
				$this->db->table($grpcomstid_tbl)
									->where('crs_master_id', $crs_master_id)
									->update(['comp_id_str'=> $comp_id_str2]);
			}
		}
 	}

 	function update_master_mapping($crs_master_id,$comp_id,$grpmp_id)
 	{

		$grpcomstid_tbl = 'grpcomstid';

		$result = $this->db->table($grpcomstid_tbl)
												->where('crs_master_id', $crs_master_id)
												->get()->getRowArray();

		if($result){
			$comp_id_str = trim($result['comp_id_str']);

			if($comp_id_str != ''){
				$comp_id_arr = explode(',', $comp_id_str);
				$comp_id_arr[] = $comp_id;
				$comp_id_str2 = implode(',', $comp_id_arr);
				$comp_id_str2 = trim($comp_id_str2);
			}
			else{
				$comp_id_str2 = $comp_id;
			}
			$this->db->table($grpcomstid_tbl)
									->where('crs_master_id', $crs_master_id)
									->update(['comp_id_str'=> $comp_id_str2]);

			$grpmapping_tbl = 'grpmapping';

			$this->db->table($grpmapping_tbl)
							->where('grpmp_id', $grpmp_id)
							->update([
								'crs_master_id' => $crs_master_id, 
								'master_name' 	=> $result['crs_master_name'],
								'parent_id' 		=> $result['parent_id'],
								'parent' 				=> $result['parent']
							]);
		}
 	}
	
	function group_comp_list()
	{
		$final = [];
		$result=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$this->grp_comp_id)
											->where('grp_fy_id',$this->grp_comp_fy_id)
											->get()->getResultArray();

		foreach ($result as $key => $value) {
			$comp_id = $value['comp_id'];
			$comp_name = $this->get_comp_name($comp_id);
			$final[] = [
				'comp_id'	=> $comp_id,
				'label'		=> $comp_name,
				'value'		=> $comp_name,
			];
		}

		return $final;
	}

	function unmapped_master_list($type)
	{
		$final = [];
		$result=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$this->grp_comp_id)
											->where('grp_fy_id',$this->grp_comp_fy_id)
											->get()->getResultArray();

		foreach ($result as $key => $value) {
			$comp_id = $value['comp_id'];

			$grpmapping_tbl = 'grpmapping';

			$final2 = [];
			$result2 = $this->db->table($grpmapping_tbl)
												->where('master_type', $type)
												->where('comp_id', $comp_id)
												->where('crs_master_id', 0)
												->get()->getResultArray();

			foreach ($result2 as $key2 => $value2) {

				$master_name = $this->get_comp_master_name($comp_id,$type,$value2['master_id']);
				$final2[] = [
					'grpmp_id'	=> $value2['grpmp_id'],
					'label'			=> $master_name,
					'value'			=> $master_name,
				];
			}
		
			$final[$comp_id] = $final2;
		}

		return $final;
	}


}