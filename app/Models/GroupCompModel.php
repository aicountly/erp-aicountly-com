<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\enc_string;
use App\Libraries\ERPtables;
use App\Libraries\GRPtables;
use App\Libraries\UUIDtables;
use App\Libraries\externaldb;
use App\Libraries\cPanelApi;

class GroupCompModel extends Model{

	public function __construct() {
		parent::__construct();
		$this->db            = \Config\Database::connect();
		$this->session       = \Config\Services::session();

		$this->uuid       =  $this->session->get('uuid');
		$this->enc_string    = new enc_string();
		$this->externaldb    = new externaldb();
		$this->cpanelapi     = new cPanelApi();
		$this->erp_db        = $this->externaldb->erp_db();
		$this->aicountly_db  = $this->externaldb->aicountly_db();
	}

	function check_name($grpco_name)
	{
		$exists=$this->aicountly_db->table('aicountly_grpmasternn_univdb')
									->where('grpco_name', $grpco_name)
									->get()->getRowArray();
		if($exists)
			return true;

		return false;
	}

	function insert_group_master($data){
		$this->aicountly_db->table('aicountly_grpmasternn_univdb')
		->insert($data); 

		return  $this->aicountly_db->insertID();
	}

	function update_group_master($data){

		$this->aicountly_db->table('aicountly_grpmasternn_univdb')
											->where('grpco_id',$data['grpco_id'])
											->update($data); 
	}

	function delete_group_comp($grpco_id)
	{
		$this->aicountly_db->table('aicountly_grpmasternn_univdb')
											->where('grpco_id',$grpco_id)
											->delete(); 

		$this->aicountly_db->table('aicountly_grpfymastr_univdb')
											->where('grpco_id',$grpco_id)
											->delete(); 

		$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$grpco_id)
											->delete(); 
	}

	function delete_group_comp_db($grpco_id)
	{
		$comp_code  = grp_compcode_format($grpco_id);

		$dbname  = getenv("DB_PREFIX_NAME").$comp_code;	
		$uname   = getenv("DB_PREFIX_NAME").'u'.$comp_code;		
		$pass  	 = getenv("DB_USR_PASSWORD");

		$this->cpanelapi->deleteDataBaseMySQL($dbname);
		$this->cpanelapi->deleteUserMySQL($uname);
	}

	function group_comp_info($grpco_id)
	{
		$result=$this->aicountly_db->table('aicountly_grpmasternn_univdb')
											->where('grpco_id',$grpco_id)
											->get()->getRowArray();
		return $result;
	}
	function group_comp_fy_info($grpco_id)
	{
		$result=$this->aicountly_db->table('aicountly_grpfymastr_univdb')
											->where('grpco_id',$grpco_id)
											->where('default', 1)
											->get()->getRowArray();
		return $result;
	}

	function group_fy_list2($grpco_id)
	{
		$result=$this->aicountly_db->table('aicountly_grpfymastr_univdb')
											->where('grpco_id',$grpco_id)
											->orderBy('grp_fy_id', 'asc')
											->get()->getResultArray();
		return $result;
	}

	function group_comp_list($grpco_id, $grp_fy_id)
	{
		$result=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$grpco_id)
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

	function group_comp_list2($grpco_id, $grp_fy_id)
	{
		$result=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->where('grpco_id',$grpco_id)
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
			$bgn_date = '';
			$end_date = '';

			$get_comp_fy_info = $this->get_comp_fy_info($value['comp_id'],$value['comp_fy_id']);
			if($get_comp_fy_info){
				$bgn_date = $get_comp_fy_info['fy_begndt'];
				$end_date = $get_comp_fy_info['fy_end'];

				$fy_name = get_fy_name($bgn_date,$end_date);

			}

			$value['fy_name'] = $fy_name;
			$value['bgn_date'] = $bgn_date;
			$value['end_date'] = $end_date;

			$value['comp_id']   = $value['comp_id'];
			$value['comp_name'] = $comp_name;
			$value['comp_code'] = $comp_code;

			$final[] = $value;
		}

		return $final;
	}

	function group_fy_list($grpco_id)
	{
		$final = [];
		$result=$this->aicountly_db->table('aicountly_grpfymastr_univdb')
											->where('grpco_id',$grpco_id)
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
				'bgn_date'	=> $bgn_date,
				'end_date'	=> $end_date,
			];
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

	function get_comp_info($comp_id){

 		$result=$this->aicountly_db->table("aicountly_cmpmastern_univdb")
									->select('comp_name, comp_code')
									->where('comp_id',$comp_id)
									->where('comp_db_status','active')
									->get()->getRowArray();

		return $result;
 	}

	function group_companies_list() 
	{
		$uuid =  $this->session->get('uuid');
		$result=$this->aicountly_db->table('aicountly_grpmasternn_univdb')
											->where('uuid',$uuid)
											->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {

			$comp=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->select('comp_id')
											->where('grpco_id',$value['grpco_id'])
											->groupBy('comp_id')
											->get()->getResultArray();
			$count = count($comp);


			$fy_list = [];

			$grp_fy_list=$this->group_fy_list2($value['grpco_id']);
			if($grp_fy_list){
				foreach ($grp_fy_list as $grp_fy)
				{
					$bgn_date = $grp_fy['bgn_date'];
					$end_date = $grp_fy['end_date'];

					$fy_name = get_fy_name($bgn_date,$end_date);
					$fy_str = date('d-m-Y',strtotime($bgn_date)).' -- '.date('d-m-Y',strtotime($end_date));

					$vouchers = '';

	        $fy_list[] = [
	        	'fy_id'				=> $grp_fy['grp_fy_id'],
	        	'fy_name'			=> $fy_name,
	        	'fy_str'			=> $fy_str,
	        	'vouchers'		=> $vouchers,
	        ];
				}
			}

			$final[] = [
				'encomp_id' 		=> obfuscate_link($value['grpco_id']),
				'comp_id'				=> $value['grpco_id'],
				'comp_type'			=> 'grp',
				'comp_name' 		=> $value['grpco_name'],
				'comp_alias' 		=> $value['grpco_alias'],
				'comp_code' 		=> $value['comp_code'],
				'comp_status'		=> $value['erp_db_status'],
				'count'					=> $count,
				'comp_size'			=> '',
				'fy_list'				=> $fy_list
			];

		}

		return $final;
	}

	function get_all_group_companies() 
	{
		$uuid =  $this->session->get('uuid');
		$result=$this->aicountly_db->table('aicountly_grpmasternn_univdb')
											->where('uuid',$uuid)
											->get()->getResultArray();
		$final = [];

		foreach ($result as $key => $value) {

			$comp=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->select('comp_id')
											->where('grpco_id',$value['grpco_id'])
											->groupBy('comp_id')
											->get()->getResultArray();
			$count = count($comp);


			$fy_str =  '';

			$grp_fy = $this->group_comp_fy_info($value['grpco_id']);
			if($grp_fy){

				$bgn_date = $grp_fy['bgn_date'];
				$end_date = $grp_fy['end_date'];

				$fy_str = date('d-m-Y',strtotime($bgn_date)).' -- '.date('d-m-Y',strtotime($end_date));
			}

			$final[] = [
				'encomp_id' 		=> obfuscate_link($value['grpco_id']),
				'comp_id'				=> $value['grpco_id'],
				'comp_type'			=> 'grp',
				'type'					=> 'owner',
				'comp_name' 		=> $value['grpco_name'],
				'comp_alias' 		=> $value['grpco_alias'],
				'comp_code' 		=> $value['comp_code'],
				'count'					=> $count,
				'comp_fy_str'		=> $fy_str
			];
		}

		$builder = $this->aicountly_db->table("aicountly_cmpidacsnn_univdb");
    $builder->select('comp_id');
    $builder->where('uuid_access',$uuid);
    $builder->where('comp_type','grp');
    $builder->groupBy('comp_id');
    $builder->orderBy('comp_id','DESC');
    $result = $builder->get()->getResultArray();

    foreach ($result as $key => $value) {

    	$result2 = $this->aicountly_db->table('aicountly_grpmasternn_univdb')
											->where('grpco_id',$value['comp_id'])
											->get()->getRowArray();
			if($result2){

				$comp=$this->aicountly_db->table('aicountly_grpcompacs_univdb')
											->select('comp_id')
											->where('grpco_id',$result2['grpco_id'])
											->groupBy('comp_id')
											->get()->getResultArray();
				$count = count($comp);


				$fy_str =  '';

				$grp_fy = $this->group_comp_fy_info($result2['grpco_id']);
				if($grp_fy){

					$bgn_date = $grp_fy['bgn_date'];
					$end_date = $grp_fy['end_date'];

					$fy_str = date('d-m-Y',strtotime($bgn_date)).' -- '.date('d-m-Y',strtotime($end_date));
				}

				$final[] = [
					'encomp_id' => obfuscate_link($result2['grpco_id']),
					'comp_id'				=> $result2['grpco_id'],
					'comp_type'			=> 'grp',
					'type'					=> 'shared',
					'comp_name' 		=> $result2['grpco_name'],
					'comp_alias' 		=> $result2['grpco_alias'],
					'comp_code' 		=> $result2['comp_code'],
					'count'					=> $count,
					'comp_fy_str'		=> $fy_str
				];
			}

		}

		return $final;
	}

	function save_group_comp($grpco_id,$grp_fy_id,$data){

		$data = [
			'grpco_id'		=> $grpco_id,
			'grp_fy_id'   => $grp_fy_id,
			'comp_id'			=> $data['comp_id'],
			'comp_fy_id'	=> $data['comp_fy_id'],
		];
		$this->aicountly_db->table('aicountly_grpcompacs_univdb')
												->insert($data);
	}

	function save_group_fy($grpco_id,$bgn_date,$end_date){

		$result = $this->aicountly_db->table('aicountly_grpfymastr_univdb')
					->select('MAX(grp_fy_id) as grp_fy_id')
					->where('grpco_id', $grpco_id)
					->get()->getRowArray();
		if($result['grp_fy_id'] != NULL){
			$grp_fy_id = intval($result['grp_fy_id']) + 1;
		}
		else{
			$grp_fy_id = 1;
		}

		$data = [
			'grpco_id'		=> $grpco_id,
			'grp_fy_id'		=> $grp_fy_id,
			'bgn_date'		=> $bgn_date,
			'end_date'		=> $end_date,
			'default'			=> 1,
		];
		$this->aicountly_db->table('aicountly_grpfymastr_univdb')
							->insert($data);

		return $grp_fy_id;
	}

	function create_db($grpco_id,$grp_fy_id)
	{
		$comp_code  = grp_compcode_format($grpco_id);

		$dbname  = getenv("DB_PREFIX_NAME").$comp_code;	
		$uname   = getenv("DB_PREFIX_NAME").'u'.$comp_code;		
		$pass  	 = getenv("DB_USR_PASSWORD");

		$db   =   $this->cpanelapi->createDataBaseMySQL($dbname);
		$user =   $this->cpanelapi->createUserMySQL($uname, $pass);
		$prev =   $this->cpanelapi->setPrivilegesMySQL($uname,$dbname);

		$db = $this->externaldb->single_company_db($comp_code);


		$response = $this->create_tables($grpco_id,$grp_fy_id);
		
		return $response;
	}

	function create_tables($grpco_id,$grp_fy_id)
	{
		$GRPtables = new GRPtables($grpco_id,$grp_fy_id);
		$response = $GRPtables->default_tables(true,true);
		
		return $response;
	}

	function get_comp_user_info($comp_id)
	{
		$uuid = 0;
		$comp = $this->aicountly_db->table("aicountly_compidgenr_univdb")
															->where('comp_id',$comp_id)
															->get()->getRowArray();
		if($comp){ $uuid = $comp['uuid'];}

		return $uuid;
	}

	function get_mapping($comp,$item)
	{
		$final = [];
		$comp_id = $comp['comp_id'];
		$comp_fy_id = $comp['comp_fy_id'];

		$uuid = $this->get_comp_user_info($comp_id);
		if($uuid){
			$uuid_db = $this->externaldb->connect_uuid_db($uuid);

			$baseidgenrt_tbl = $comp_id.'_baseidgenrt';
			$baseidgenrt = $uuid_db->table($baseidgenrt_tbl)
															->where('comp_id',$comp_id)
															->where('mst_type',$item['type'])
															->get()->getResultArray();
			if($baseidgenrt){
				foreach ($baseidgenrt as $baseid) {
					$mst_base_id = $baseid['mst_base_id'];
					
					$cofymstmap_tbl = $comp_id.'_cofymstmap';
					$cofymstmap = $uuid_db->table($cofymstmap_tbl)
															->where('comp_id',$comp_id)
															->where('mst_type',$item['type'])
															->where('mst_base_id',$mst_base_id)
															->where('comp_fy_id',$comp_fy_id)
															->limit(1)
															->get()->getRowArray();
					if($cofymstmap){
						
						$master_id = $cofymstmap['mst_fy_id'];
						$comp_db = $this->externaldb->comp_db($comp_id);

						$table = $comp_id.'_'.$item['type'].'_'.$comp_fy_id;
						$result = $comp_db->table($table)
															->where($item['id'],$master_id)
															->get()->getRowArray();
						if($result){
							$master_name = strtolower($result[$item['name']]);
							$master_name = trim($master_name);

							$parent_id = 0;
							$parent = '';
							$status = true;

					
							if($item['type'] == 'cmpvchseri'){
								$parent_id = $result['voucher_type_id'];

								$cmpvchtype_tbl = $comp_id.'_cmpvchtype_'.$comp_fy_id;
								$result2 = $comp_db->table($cmpvchtype_tbl)
															->where('voucher_type_id',$result['voucher_type_id'])
															->get()->getRowArray();
								if($result2){
									$parent = trim($result2['comp_vch_type']);
									$parent = strtolower($parent);
								}
							}
							if($item['type'] == 'billmaster'){
								$acctmaster_tbl = $comp_id.'_acctmaster_'.$comp_fy_id;
								$result2 = $comp_db->table($acctmaster_tbl)
															->where('acc_id',$result['acc_id'])
															->get()->getRowArray();
								if($result2){
									$parent = trim($result2['acc_name']);
									$parent = strtolower($parent);
								}
								else{
									$status = false;
								}
							}

							if($item['type'] == 'itmbatchmt'){
								$parent_id = $result['item_id'];

								$itemmaster_tbl = $comp_id.'_itemmaster_'.$comp_fy_id;
								$result2 = $comp_db->table($itemmaster_tbl)
															->where('item_id',$result['item_id'])
															->get()->getRowArray();
								if($result2){
									$parent = trim($result2['item_name']);
									$parent = strtolower($parent);
								}
								else{
									$status = false;
								}
							}
							
							if($item['type'] == 'acctmaster' || $item['type'] == 'billsundry'){
								$parent_id = $result['acc_grp_parent_id'];

								if($parent_id == 0){
									$acctgroupn_tbl = $comp_id.'_acctgroupn_'.$comp_fy_id;
									$result2 = $comp_db->table($acctgroupn_tbl)
												->where('acc_grp_id',$result['acc_grp_id'])
												->get()->getRowArray();
									if($result2){
										$parent_id = $result2['acc_grp_parent_id'];
									}
									else{
										$status = false;
									}
								}
								
								$grpparentn_tbl = $comp_id.'_grpparentn_'.$comp_fy_id;
								$result2 = $comp_db->table($grpparentn_tbl)
											->where('acc_grp_parent_id',$parent_id)
											->get()->getRowArray();
								if($result2){
									$parent = trim($result2['acc_grp_parent']);
									$parent = strtolower($parent);
								}
							}
							

							if($status){
								$final[] = [
									'comp_id'			=> $comp_id,
									'comp_fy_id'	=> $comp_fy_id,
									'master_id'		=> $mst_base_id,
									'master_name' => $master_name,
									'master_type' => $item['type'],
									'parent_id'		=> $parent_id,
									'parent'			=> $parent,
								];
							}
						}
					}
				}
			}
		}

		return $final;
	}

	function save_mapping($grpco_id,$grp_fy_id,$comp_arr,$item)
	{
		$result = [];
		$final = [];


		$comp_code = grp_compcode_format($grpco_id);
		$db = $this->externaldb->single_company_db($comp_code);

		foreach ($comp_arr as $comp) {
			$response = $this->get_mapping($comp,$item);
			foreach ($response as $key => $value) {
				$result[] = $value;
			}
		} 

		foreach ($result as $key => $value) {
			$status = true;

			foreach ($final as $key2 => $value2) {
				if($status){
					if($value['master_name'] == $value2['master_name'] && $value['parent_id'] == $value2['parent_id'] && $value['parent'] == $value2['parent']){
						$final[$key2]['comp_id_arr'][] = $value['comp_id'];
						$final[$key2]['comp_fy_id_arr'][] = $value['comp_fy_id'];
						$final[$key2]['master_id_arr'][] = $value['master_id'];
						$status = false;
					}
				}
			} 

			if($status){
				$value['comp_id_arr'] = [$value['comp_id']];
				$value['comp_fy_id_arr'] = [$value['comp_fy_id']];
				$value['master_id_arr'] = [$value['master_id']];
				unset($value['comp_id']);
				unset($value['comp_fy_id']);
				unset($value['master_id']);
				$final[] = $value;
			}
		}
		
		foreach ($final as $key => $value) {

			$crs_master_id = 0;
			$map_id_status_arr = [];
			foreach ($value['master_id_arr'] as $master_id) {
				
				$grpmapping_tbl = 'grpmapping';
				$exists = $db->table($grpmapping_tbl)
											->where('master_id', $master_id)
											->where('master_type',$value['master_type'])
											->get()->getRowArray();

				if($exists && $exists['crs_master_id'] != 0){
					$crs_master_id = $exists['crs_master_id'];
					$map_id_status_arr[] = 0;
				}
				else{
					$map_id_status_arr[] = 1; 
				}
			}

			if($crs_master_id == 0){
				$comp_id_str = implode(",",$value['comp_id_arr']);
				$data = [
					'grpco_id'				=> $grpco_id,
					'crs_master_type'	=> $value['master_type'],
					'crs_master_name'	=> ucwords($value['master_name']),
					'comp_id_str'			=> $comp_id_str,
					'parent_id'				=> $value['parent_id'],
					'parent'					=> $value['parent'],
				];

				$grpcomstid_tbl = 'grpcomstid';
				$db->table($grpcomstid_tbl)->insert($data);

				$crs_master_id = $db->insertID();
			}
			else{
				$comp_id_str = implode(",",$value['comp_id_arr']);
				$data = [
					'comp_id_str'			=> $comp_id_str,
				];

				$grpcomstid_tbl = 'grpcomstid';
				$db->table($grpcomstid_tbl)
							->where('crs_master_id',$crs_master_id)
							->update($data);
			}
				

			foreach ($value['comp_id_arr'] as $k => $comp_id) {

				if($map_id_status_arr[$k])
				{
					$data = [
						'grpco_id'				=> $grpco_id,
						'crs_master_id'		=> $crs_master_id,
						'master_id'				=> $value['master_id_arr'][$k],
						'master_type'			=> $value['master_type'],
						'master_name'			=> ucwords($value['master_name']),
						'comp_id'					=> $comp_id,
						// 'comp_fy_id'			=> $value['comp_fy_id_arr'][$k], // removed column
						'parent_id'				=> $value['parent_id'],
						'parent'					=> $value['parent'],
					];
					$grpmapping_tbl = 'grpmapping';
					$db->table($grpmapping_tbl)->insert($data);
				}
			}
		}
	}

	function save_group_comp_map($grpco_id,$grp_fy_id,$comp_arr){

		$array = [
      ['type' => 'hobomaster', 'id' => 'bo_id', 'name' => 'bo_name'],
      ['type' => 'compcurrcy', 'id' => 'comp_currency_id', 'name' => 'curr_name'],
      ['type' => 'cmpvchseri', 'id' => 'comp_vch_series_id', 'name' => 'comp_vch_series'],

      ['type' => 'acctgroupn', 'id' => 'acc_grp_id', 'name' => 'acc_grp_name'],
      ['type' => 'acctmaster', 'id' => 'acc_id', 'name' => 'acc_name'],
      ['type' => 'billsundry', 'id' => 'bill_sundry_id', 'name' => 'bill_sundry_name'],
      ['type' => 'projectgrp', 'id' => 'project_grp_id', 'name' => 'project_grp_name'],
      ['type' => 'projectmst', 'id' => 'project_id', 'name' => 'project_name'],

      ['type' => 'itemgrpmst', 'id' => 'item_grp_id', 'name' => 'item_grp_name'],
      ['type' => 'itemcatmst', 'id' => 'icatgms_id', 'name' => 'item_cat'],
      ['type' => 'itemmaster', 'id' => 'item_id', 'name' => 'item_name'],
      ['type' => 'itmbatchmt', 'id' => 'batch_id', 'name' => 'batch_no'],
      ['type' => 'itmunitmst', 'id' => 'unit_id', 'name' => 'item_unit'],

      ['type' => 'mcgrpmstnn', 'id' => 'mc_grp_id', 'name' => 'mc_grp_name'],
      ['type' => 'mcmasternn', 'id' => 'mat_cent_id', 'name' => 'mat_cent_name'],
      ['type' => 'barcodemst', 'id' => 'barcode_id', 'name' => 'barcode'],
      ['type' => 'labelmastr', 'id' => 'label_id', 'name' => 'label_name'],

      ['type' => 'costctgrup', 'id' => 'cc_grp_id', 'name' => 'cc_grp_name'],
      ['type' => 'costctmstr', 'id' => 'cc_id', 'name' => 'cc_name'],
      ['type' => 'billmaster', 'id' => 'bills_ref_id', 'name' => 'bills_ref_name'],
      ['type' => 'billofmatn', 'id' => 'bom_id', 'name' => 'bom_name'],
      ['type' => 'prntconfig', 'id' => 'prntconfig_id', 'name' => 'prntconfig_id'],
    ];

    foreach ($array as $key => $value) {
    	$this->save_mapping($grpco_id,$grp_fy_id,$comp_arr,$value);
    }	
  }


  function company_list(){
  	$uuid =  $this->session->get('uuid');
  	$records = array();

  	$builder = $this->aicountly_db->table("aicountly_compidgenr_univdb");
  	$builder->select('comp_id');
  	$builder->where('uuid',$uuid);
  	$result = $builder->get()->getResultArray();

  	foreach ($result as $key => $value) {
  		$builder = $this->aicountly_db->table("aicountly_cmpmastern_univdb");
  		$builder->select('comp_id, comp_name, comp_code');
  		$builder->where('comp_id',$value['comp_id']);
  		$builder->where('comp_db_status','active');
  		$result2 = $builder->get()->getRowArray();

  		if($result2){
  			$fy_list = $this->get_comp_fy_list($result2['comp_id']);

  			$records[] = array(
  				'encomp_id'    => obfuscate_link($result2['comp_id']),
  				'ownership'    => 'owner',
  				'comp_type'    => 'cmp',
  				'comp_id'      => $result2['comp_id'],
  				'comp_name'    => $result2['comp_name'],
  				'comp_code'    => $result2['comp_code'],
  				'fy_list'    	 => $fy_list,
  				'label'    		 => $result2['comp_name'],
  				'value'    		 => $result2['comp_name'],
  			);
  		}
  	}             


  	$builder = $this->aicountly_db->table("aicountly_cmpidacsnn_univdb");
  	$builder->select('comp_id');
  	$builder->where('uuid_access',$uuid);
  	$builder->where('comp_type','cmp');
  	$builder->groupBy('comp_id');
  	$builder->orderBy('comp_id','DESC');
  	$result = $builder->get()->getResultArray();

  	foreach ($result as $key => $value) {
  		$builder = $this->aicountly_db->table("aicountly_cmpmastern_univdb");
  		$builder->select('comp_id, comp_name, comp_code');
  		$builder->where('comp_id',$value['comp_id']);
  		$builder->where('comp_db_status','active');
  		$result2 = $builder->get()->getRowArray();

  		if($result2){
  			$fy_list = $this->get_comp_fy_list($result2['comp_id']);

  			$records[] = array(
  				'encomp_id'    => obfuscate_link($result2['comp_id']),
  				'ownership'    => 'shared',
  				'comp_type'    => 'cmp',
  				'comp_id'      => $result2['comp_id'],
  				'comp_name'    => $result2['comp_name'],
  				'comp_code'    => $result2['comp_code'],
  				'fy_list'    	 => $fy_list,
  				'label'    		 => $result2['comp_name'],
  				'value'    		 => $result2['comp_name'],
  			);
  		}
  	}
  	return $records; 
  }

  function get_comp_fy_list($comp_id)
  {
  	$final = [];
  	$result =$this->aicountly_db->table("aicountly_cmpfymastr_univdb")
  															->where('comp_id',$comp_id)
  															->where('is_imported', 1)
  															->orderBy('comp_fy_id','asc')
  															->get()->getResultArray();

  	if($result){
  		foreach ($result as $key => $value) {

        $fy_name = get_fy_name($value['fy_begndt'],$value['fy_end']);

        $final[] = [
        	'id'				=> $value['comp_fy_id'],
        	'name'			=> $fy_name,
        	'bgn_date'	=> $value['fy_begndt'],
        	'end_date'	=> $value['fy_end'],
        ];
  		}
  	}

  	return $final;
  }

}