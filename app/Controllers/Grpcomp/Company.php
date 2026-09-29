<?php

namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\CompanyModel;
use App\Models\GroupCompModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Company extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url']);
			$this->CompanyModel  = new CompanyModel();
			$this->GroupCompModel  = new GroupCompModel();			
			$this->auth_session  = new auth_session(); 
			$this->auth_session->user_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().'/'.getenv('GroupPath');;
			$this->folder_path   = getenv('GroupPath');
			$this->session 	     = \Config\Services::session();
			$this->enc_string    = new enc_string();
			$this->company_id    = $this->session->get('ses_company_id');

			$this->grp_comp_id = $this->session->get('grp_comp_id');
			$this->grp_comp_fy_id = $this->session->get('grp_comp_fy_id');
    }
 
  public function index()
  {
    $data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;	

		return view($this->folder_path.'company/view',$data);		
  }


  public function nextFY()
  {
  	if($this->request->getMethod() == 'post'){
 
			$groupdata    = $this->request->getVar('groupdata');
			$groupdata = json_decode($groupdata,true);
			
			$comp_id_arr = [];
			$minDate = [];
			$maxDate = [];
			$non_fy_id = 0;

		  foreach($groupdata as $value){
				if($value['comp_fy_id']){
					$comp_id_arr[] = $value;

					$minDate[] = $value['bgn_date'];
					$maxDate[] = $value['end_date'];
				}
				else
					$non_fy_id++;
			}

			if($non_fy_id > 0)
				return json_encode(["status" => false, "errors" => [$non_fy_id . ' FY Missing']]);

			$min = min($minDate);
			$max = max($maxDate);

			$grp_fy_id =$this->CompanyModel->save_group_next_fy($min,$max);
			if($grp_fy_id == 0)
				return json_encode(["status" => false, "errors" => ['Something went wrong']]);

			foreach ($comp_id_arr as $key => $value) {
			 $this->CompanyModel->save_group_comp_next_fy($grp_fy_id,$value);
			}		

			$this->GroupCompModel->save_group_comp_map($this->grp_comp_id,$grp_fy_id,$comp_id_arr);

			return json_encode(["status" => true, "message" => "FY Created Successfully", "errors" => []]);
		}

    $data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['comp_list']			 = $this->CompanyModel->next_group_comp_list();

		return view($this->folder_path.'company/next_fy',$data);		
  } 

  public function selectFY($grp_fy_id)
  {
  	$comp_fy_info=$this->CompanyModel->group_comp_fy_info($grp_fy_id);

		$group_comp_list=$this->CompanyModel->group_comp_list($grp_fy_id);
		$grp_comp_list = json_encode($group_comp_list);

		$group_fy_list=$this->CompanyModel->group_fy_list();
		$grp_fy_list = json_encode($group_fy_list);

		$fy_name = get_fy_name($comp_fy_info['bgn_date'],$comp_fy_info['end_date']);

		$this->session->remove('grp_comp_list');
		$this->session->remove('grp_comp_fy_id');
		$this->session->remove('grp_comp_fy_bgn_date');
		$this->session->remove('grp_comp_fy_end_date');
		$this->session->remove('grp_comp_fy_name');
		$this->session->remove('grp_comp_fy_list');

		$this->session->set('grp_comp_list', $grp_comp_list);
		$this->session->set('grp_comp_fy_id', $comp_fy_info['grp_fy_id']);
		$this->session->set('grp_comp_fy_bgn_date', $comp_fy_info['bgn_date']);
		$this->session->set('grp_comp_fy_end_date', $comp_fy_info['end_date']);
		$this->session->set('grp_comp_fy_name',$fy_name);
		$this->session->set('grp_comp_fy_list', $grp_fy_list);

		return redirect()->to(base_url().'/grpcomp');
  }

  

}