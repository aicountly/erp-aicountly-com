<?php
namespace App\Models\Grpcomp;

use CodeIgniter\Model;
use App\Libraries\externaldb;

class CompanyModel extends Model	{

  public function __construct() {
		parent::__construct();
		$this->session     = \Config\Services::session();

		$this->externaldb = new externaldb();
		$this->db 			= $this->externaldb->grp_comp_db();
		$this->erp_db 		= $this->externaldb->erp_db();
		$this->aicountly_db = $this->externaldb->aicountly_db();

		$this->uuid       	  = $this->session->get('uuid');
		$this->grp_comp_id 	  = $this->session->get('grp_comp_id');
		$this->grp_comp_fy_id = $this->session->get('grp_comp_fy_id');
  }
    
  function next_group_comp_list()
	{
		$result=$this->aicountly_db->table('aicountly_grpfymastr_univdb')
															->select('grp_fy_id')
															->where('grpco_id',$this->grp_comp_id)
															->orderBy('grp_fy_id','desc')
															->limit(1)
															->get()->getRowArray();

		$grp_fy_id = $result['grp_fy_id'];

		$result=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$this->grp_comp_id)
											->where('grp_fy_id',$grp_fy_id)
											->get()->getResultArray();
		

		$final = [];
		foreach ($result as $key => $value) {

			$comp_name = '';
			$comp_code = '';

			$get_comp_info = $this->get_comp_info($value['comp_id']);
			if($get_comp_info){
				$comp_name = $get_comp_info['comp_name'];
				$comp_code = $get_comp_info['comp_code'];
			}

			$comp_fy_id = 0;
			$fy_name = '';
			$bgn_date = '';
			$end_date = '';

			$get_comp_fy_info = $this->next_get_comp_fy_info($value['comp_id'],$value['comp_fy_id']);
			
			if($get_comp_fy_info){
				$comp_fy_id = $get_comp_fy_info['comp_fy_id'];
				$bgn_date = $get_comp_fy_info['fy_begndt'];
				$end_date = $get_comp_fy_info['fy_end'];

				$fy_name = get_fy_name($bgn_date,$end_date);
				

			}

			$value['comp_fy_id'] = $comp_fy_id;
			$value['fy_name'] = $fy_name;
			$value['bgn_date'] = $bgn_date;
			$value['end_date'] = $end_date;

			$value['comp_name'] = $comp_name;
			$value['comp_code'] = $comp_code;

			$final[] = $value;
		}
		
		return $final;
	}

	function next_get_comp_fy_info($comp_id,$comp_fy_id)
	{
		$result=$this->aicountly_db->table("aicountly_cmpfymastr_univdb")
									->where('comp_id',$comp_id)
									->where('comp_fy_id >',$comp_fy_id)
									->where('is_imported',1)
									->orderBy('comp_fy_id', 'asc')
									->limit(1)
									->get()->getRowArray();

		return $result;
	}

	function get_comp_info($comp_id){

 		$result=$this->aicountly_db->table("aicountly_cmpmastern_univdb")
									->select('comp_name, comp_code')
									->where('comp_id',$comp_id)
									->where('comp_db_status','active')
									->get()->getRowArray();

		return $result;
 	}

 	function save_group_next_fy($bgn_date,$end_date){

		$result=$this->aicountly_db->table('aicountly_grpfymastr_univdb')
					->select('MAX(grp_fy_id) as grp_fy_id')
					->where('grpco_id', $this->grp_comp_id)
					->get()->getRowArray();

			
		if($result['grp_fy_id'] != NULL){
			$grp_fy_id = intval($result['grp_fy_id']) + 1;
		}
		else{
			return 0;
		}

		$data = [
			'grpco_id'		=> $this->grp_comp_id,
			'grp_fy_id'		=> $grp_fy_id,
			'bgn_date'		=> $bgn_date,
			'end_date'		=> $end_date,
			'default'			=> 0,
		];
		$this->aicountly_db->table('aicountly_grpfymastr_univdb')
							->insert($data);

		return $grp_fy_id;
	}

	function save_group_comp_next_fy($grp_fy_id,$data){

		$data = [
			'grpco_id'		=> $this->grp_comp_id,
			'grp_fy_id'   => $grp_fy_id,
			'comp_id'			=> $data['comp_id'],
			'comp_fy_id'	=> $data['comp_fy_id'],
		];
		$this->aicountly_db->table('aicountly_grpcompacs_univdb')
												->insert($data);
	}

	function group_comp_fy_info($grp_fy_id)
	{
		$result=$this->aicountly_db->table('aicountly_grpfymastr_univdb')
											->where('grpco_id',$this->grp_comp_id)
											->where('grp_fy_id', $grp_fy_id)
											->get()->getRowArray();
		return $result;
	}

	function group_comp_list($grp_fy_id)
	{
		$result=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$this->grp_comp_id)
											->where('grp_fy_id',$grp_fy_id)
											->get()->getResultArray();

		$final = [];
		foreach ($result as $key => $value) {

			$comp_name = '';
			$comp_code = '';

			$get_comp_info = $this->get_comp_info($value['comp_id']);
			if($get_comp_info){
				$comp_name = $get_comp_info['comp_name'];
				$comp_code = $get_comp_info['comp_code'];
			}

			$fy_name = '';
			$fy_str = '';

			$get_comp_fy_info = $this->get_comp_fy_info($value['comp_id'],$value['comp_fy_id']);
			if($get_comp_fy_info){
				$bgn_date = $get_comp_fy_info['fy_begndt'];
				$end_date = $get_comp_fy_info['fy_end'];

				$fy_name = get_fy_name($bgn_date,$end_date);
				$fy_str = date('d-m-Y',strtotime($bgn_date)).' -- '.date('d-m-Y',strtotime($end_date));

			}

			$value['fy_name'] = $fy_name;
			$value['fy_str'] = $fy_str;

			$value['comp_name'] = $comp_name;
			$value['comp_code'] = $comp_code;

			$final[$value['comp_id']] = $value;
		}

		return $final;
	}

	function get_comp_fy_info($comp_id,$comp_fy_id)
	{
		$result=$this->aicountly_db->table("aicountly_cmpfymastr_univdb")
									->where('comp_id',$comp_id)
									->where('comp_fy_id',$comp_fy_id)
									->get()->getRowArray();

		return $result;
	}

	function group_fy_list()
	{
		$final = [];
		$result=$this->aicountly_db->table('aicountly_grpfymastr_univdb')
											->where('grpco_id',$this->grp_comp_id)
											->get()->getResultArray();

		foreach ($result as $key => $value) {
			$bgn_date = $value['bgn_date'];
			$end_date = $value['end_date'];

			$fy_name = get_fy_name($bgn_date,$end_date);
			$fy_str = date('d-m-Y',strtotime($bgn_date)).' -- '.date('d-m-Y',strtotime($end_date));

			$final[] = [
				'grp_fy_id'	=> $value['grp_fy_id'],
				'fy_name'		=> $fy_name,
				'fy_str'		=> $fy_str,
			];
		}

		return $final;
	}

	function group_branch_list()
	{
		$grpcomstid_tbl = 'grpcomstid';

		$result = $this->db->table($grpcomstid_tbl)
												->select('crs_master_id, crs_master_name')
												->where('crs_master_type', 'hobomaster')
												->get()->getResultArray();
		
		return $result;
	}
   
}