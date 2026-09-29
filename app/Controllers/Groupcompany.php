<?php
namespace App\Controllers;
use App\Models\CommonModel;
use App\Models\GroupCompModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\auth_session;

class Groupcompany extends BaseController
{
	public function __construct()
	{  
		helper(['form', 'url']);
		$this->session 	         = \Config\Services::session();	 
		$this->auth_session  = new auth_session();			
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->CommonModel          = new CommonModel();
		$this->GroupCompModel       = new GroupCompModel();     
		$this->folder_path          = getenv('AdminPath');
		$this->base_url             = base_url();
		$this->externaldb           = new externaldb();	 
		$this->enc_string           = new enc_string();
	}

	public  function index()
	{
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
		$data['companies']		  = $this->GroupCompModel->group_companies_list();
		return view('group_company',$data); 

	}
	
	public function select_company($comp_id,$enc=1)
	{

		if($enc){
			$grpid_info  = unobfuscate_link($comp_id);
		$grp_comp_id = $grpid_info[1];
		}
		else{
			$grp_comp_id = $comp_id;
		}
	 		

		$comp_info = $this->GroupCompModel->group_comp_info($grp_comp_id);
		if(empty($comp_info)){
			echo json_encode(['status' => false, 'message' => 'Company does not exists']);
			exit;
		}

		$comp_fy_info = $this->GroupCompModel->group_comp_fy_info($grp_comp_id);
		if(empty($comp_fy_info)){
			echo json_encode(['status' => false, 'message' => 'Company fy does not exists']);
			exit;
		}

		$group_comp_list = $this->GroupCompModel->group_comp_list($grp_comp_id,$comp_fy_info['grp_fy_id']);

		$group_fy_list = $this->GroupCompModel->group_fy_list($grp_comp_id);
		

		if($comp_info['erp_db_status'] == 0){
			$response = $this->GroupCompModel->create_tables($grp_comp_id,$comp_fy_info['grp_fy_id']);
			if(count($response) > 0){
				echo json_encode(['status' => false, 'message' => 'Something wrong with database']);
				exit;
			}

			$data = [
					'grpco_id'	=> $grp_comp_id,
					'erp_db_status'	=> 1
			];
			$this->GroupCompModel->update_group_master($data);

			$this->GroupCompModel->save_group_comp_map($grp_comp_id,$comp_fy_info['grp_fy_id'],$group_comp_list);
		}

		
		$grp_comp_list = json_encode($group_comp_list);
		$grp_fy_list = json_encode($group_fy_list);

		$fy_name = get_fy_name($comp_fy_info['bgn_date'],$comp_fy_info['end_date']);

		clear_comp_sess();
		clear_grp_comp_sess();

		$this->session->set('grp_comp_id',$grp_comp_id);
		$this->session->set('grp_comp_name', $comp_info['grpco_name']);
		$this->session->set('grp_comp_alias', $comp_info['grpco_alias']);
		$this->session->set('grp_comp_code', $comp_info['comp_code']);
		$this->session->set('grp_comp_list', $grp_comp_list);
		$this->session->set('grp_comp_fy_id', $comp_fy_info['grp_fy_id']);
		$this->session->set('grp_comp_fy_bgn_date', $comp_fy_info['bgn_date']);
		$this->session->set('grp_comp_fy_end_date', $comp_fy_info['end_date']);
		$this->session->set('grp_comp_fy_name',$fy_name);
		$this->session->set('grp_comp_fy_list', $grp_fy_list);

		echo json_encode(['status' => true]);
		exit;
	}

	public function close_company()
 	{
 		clear_comp_sess();
		clear_grp_comp_sess();

		return redirect()->to($this->base_url.'/groupcompany');
 	}


	function add(){

		if($this->request->getMethod() == 'post'){

			$group_name   = $this->request->getVar('group_name'); 
			$group_alias  = $this->request->getVar('group_alias'); 
			$groupdata    = $this->request->getVar('groupdata');

			$status = $this->GroupCompModel->check_name($group_name);
			if($status)
				return json_encode(["status" => false, "errors" => ['Company name already exists']]);

			$uuid =  $this->session->get('uuid');
		  	$data = [
			   	'uuid'				=> $uuid,
			   	'grpco_name'	=> $group_name,
			   	'grpco_alias'	=> $group_alias,
			   	'erp_db_status' => 0
		  	];

			$grpco_id = $this->GroupCompModel->insert_group_master($data);

			$data = [
					'grpco_id'	=> $grpco_id,
					'comp_code'	=> grp_compcode_format($grpco_id)
			];
			$this->GroupCompModel->update_group_master($data);

			$groupdata = json_decode($groupdata,true);
			
			$comp_id_arr = [];
			$minDate = [];
			$maxDate = [];
		  	foreach($groupdata as $value){
				$comp_id_info  		  = unobfuscate_link($value['encomp_id']);
				$value['comp_id']   = $comp_id_info[1];
				$comp_id_arr[] 			= $value;

				$minDate[] = $value['bgn_date'];
				$maxDate[] = $value['end_date'];
			}

			$min = min($minDate);
			$max = max($maxDate);
			$grp_fy_id = $this->GroupCompModel->save_group_fy($grpco_id,$min,$max);

			foreach ($comp_id_arr as $key => $value) {
				$this->GroupCompModel->save_group_comp($grpco_id,$grp_fy_id,$value);
			}

			$response = $this->GroupCompModel->create_db($grpco_id,$grp_fy_id);

			if(count($response))
				return json_encode(["status" => true, "message" => "Company Created but something went wrong", "errors" => $response]);

			$data = [
					'grpco_id'	=> $grpco_id,
					'erp_db_status'	=> 1
			];
			$this->GroupCompModel->update_group_master($data);			

			$this->GroupCompModel->save_group_comp_map($grpco_id,$grp_fy_id,$comp_id_arr);

			return json_encode(["status" => true, "message" => "Company Created Successfully", "errors" => []]);
		}
		$data['message_output']   = $this->message_output;
		$data['fin_year_list']    = array();
		$data['folder_path']      = $this->folder_path;
		$data['base_url']    	  	= $this->base_url;
		$data['company_list']     = $this->GroupCompModel->company_list();	
		return view('add_group_company',$data);   
	}

	function modify($grpid){
		
		$obj = unobfuscate_link($grpid);
		$grp_comp_id   = $obj[1];

		$comp_info = $this->GroupCompModel->group_comp_info($grp_comp_id);
		if(empty($comp_info)){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		
		$grpco_name = $comp_info['grpco_name'];
		$grpco_alias = $comp_info['grpco_alias'];

		$comp_fy_info = $this->GroupCompModel->group_comp_fy_info($grp_comp_id);
		

		$group_comp_list = $this->GroupCompModel->group_comp_list2($grp_comp_id,$comp_fy_info['grp_fy_id']);
		
		$data['message_output']   		= $this->message_output;
		$data['folder_path']          = $this->folder_path;
		$data['base_url']             = $this->base_url;	
		$data['grpco_id']      				= $grp_comp_id;
		$data['grpco_name']  					= $grpco_name;
		$data['grpco_alias']  				= $grpco_alias;		
		$data['group_comp_list']      = $group_comp_list;
		$data['company_list']     = $this->GroupCompModel->company_list(); 

		return view('edit_group_company',$data);  
	}
	
	public function remove_group($grpid){
		if(!$grpid)
			return redirect()->to($this->base_url.'/groupcompany'); 
		
		$grpid_info = unobfuscate_link($grpid);
		$grpco_id   = $grpid_info[1];
		
		$this->GroupCompModel->delete_group_comp_db($grpco_id);
		$this->GroupCompModel->delete_group_comp($grpco_id);

		return redirect()->to($this->base_url.'/groupcompany');
		
	}
	
}