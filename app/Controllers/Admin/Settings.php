<?php
namespace App\Controllers\Admin;
use App\Models\Admin\SettingsModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
class Settings extends BaseController
{ 
	function __construct()
	{  
		helper(['form', 'url','text']);
		$this->SettingsModel     = new SettingsModel();
		$this->auth_session    = new auth_session();
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
		
	}
    
	public function general()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
		
		return view($this->folder_path.'settings/general',$data);		
	}

	public function add_access_profile(){	

		$name        =  $this->request->getVar('name'); 
		$designation =  $this->request->getVar('designation'); 
		
		$insert_Array = array(
			'erp_acs_prof_name' => $name,
			'erp_acs_prof_desg' => $designation,
			"cmp_id"          => $this->company_id
		);
		
		$this->SettingsModel->insert_profile($insert_Array);

		return redirect()->to($this->base_url.'settings/access_profile');
	}
	public function checkRights(){
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
			$page_url = $_POST['url'];
			echo json_encode(validate_web_url($page_url));  
		    die();		   
		}
		
	}
	
	public function ajax_major_access_profile(){
        
		echo $this->SettingsModel->ajax_major_access_profile();  
		die();		   
  	}

	  public function ajax_major_level_3(){
		$id         =  $this->request->getVar('id');
		$profile_id =  $this->request->getVar('accesstype') ?? 0;    		
		echo json_encode($this->SettingsModel->ajax_major_level_3($id,$profile_id));  
		die();		   
  	}
  	
	public function save_access_permissions() {
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		$erp_acs_prof_id       = $this->request->getPost('user_id');
		$permissions           = $this->request->getPost('permissions');
		$erp_usr_right_menu_id = $this->request->getPost('menu_id');	
		if($permissions)
			
		foreach ($permissions as $menu_id => $right_type_id) {
		    
			 if(!empty($right_type_id)){
			     $this->SettingsModel->delete_permission($menu_id,$erp_acs_prof_id);
			     
			      $erp_usr_right_menu_id = $this->SettingsModel->get_erp_usr_right_menu_id($menu_id,$right_type_id);
			      
			      
			      $insertData = array(
				    "erp_acs_menu_id"       => $menu_id,
					"erp_acs_prof_id"       => $erp_acs_prof_id,
					"erp_usr_right_menu_id" => $erp_usr_right_menu_id,
				);
				$this->SettingsModel->add_permission($insertData);
			 }
		   }  
		}
      }
	  
	  public function save_backdate_entry() {
	   if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	  
		$erp_acs_prof_id    = $this->request->getPost('user_id');
		$vchseries          = $this->request->getPost('vchseries');
		$menu_id            = $this->request->getPost('menuid');
		if($vchseries){			
		foreach ($vchseries as $series_id => $erp_bde) {			
			     $this->SettingsModel->delete_backdateentries($series_id,$erp_acs_prof_id);			     
			      $insertData = array(
				    "vch_series_id"     => $series_id,
					"erp_bde"           => $erp_bde,
					"erp_acs_menu_id"   => $menu_id,
					"erp_acs_prof_id"   => $erp_acs_prof_id
				);
				$this->SettingsModel->add_backdate_entries($insertData);
			  }
		   } 
	      }		   
      }
	  
	public function save_txnaprvl() {
	  if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
	  	$erp_acs_prof_id    = $this->request->getPost('user_id');
		$usraccids          = $this->request->getPost('usraccids');
		$menu_id            = $this->request->getPost('menuid');
		$txnapprv           = $this->request->getPost('txnapprv');
		if($txnapprv==1 && $usraccids){			
		foreach ($usraccids as $userid => $txn_aprv_min_limit) {			
			     $this->SettingsModel->delete_txnaprvl_entries($userid,$menu_id,$erp_acs_prof_id);			     
			      $insertData = array(
				    "txn_aprv_uuid"       => $userid,
					"txn_aprv_min_limit"  => ($txn_aprv_min_limit>0) ? $txn_aprv_min_limit: NULL ,
					"erp_acs_menu_id"     => $menu_id,
					"erp_acs_prof_id"     => $erp_acs_prof_id
				    );
				$this->SettingsModel->add_txnaprval_entries($insertData);
			  }
		}
		else{
			  $comp_acces_users  =  $this->SettingsModel->comp_acces_users();
			    if($comp_acces_users){
				  foreach($comp_acces_users as $user_id => $user_name){
					  $this->SettingsModel->delete_txnaprvl_entries($user_id,$menu_id,$erp_acs_prof_id);			     
					  $insertData = array(
						"txn_aprv_uuid"       => $user_id,
						"txn_aprv_min_limit"  => NULL,
						"erp_acs_menu_id"     => $menu_id,
						"erp_acs_prof_id"     => $erp_acs_prof_id
						);
					  $this->SettingsModel->add_txnaprval_entries($insertData);
				    }
			    }
			   
		    }  
	    }
    }
  	
  	public function load_default_profiles(){
		try{
			$default_profile = $this->SettingsModel->save_default_profiles_company(); 
			if($default_profile){
				$response = array('status' => true,	'message' => "Profile's loaded successfully. ");
			}else{
				$response = array('status' => false,	'message' => "No Profile's loaded. ");
			}
			echo json_encode($response);		   
		}
		catch (\Throwable $e) {
			   // Catch any exception or fatal error
		  log_message('error', 'DB Error on line '.$e->getLine().': '.$e->getMessage());
		  $error_msg = "Error on line " . $e->getLine() . " in " . $e->getFile() . ": " . $e->getMessage();
		  $response = array('status' => false,	'message' => $error_msg);
		  echo json_encode($response);
		 
	   }
  	}  

	public function access_profile()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
		$data['access_manage']          = $this->SettingsModel->access_profile_list(); 
		$data['is_comp_owner']          = $this->SettingsModel->get_owner_of_company(); 
		$data['back_date']              = $this->SettingsModel->back_date_list(); 
        $data['voucher_series']         = json_encode($this->SettingsModel->comp_vchseries_list());
		$data['comp_acces_users']       = json_encode($this->SettingsModel->comp_acces_users()); 		
		$data['transaction']            = $this->SettingsModel->transaction_list();  
	
		return view($this->folder_path.'settings/access_profile',$data);		
	}

	public function inventory()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
		
		return view($this->folder_path.'settings/inventory',$data);		
	}

	public function advanced_settings()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
		
		return view($this->folder_path.'settings/advanced_settings',$data);		
	}

	public function transaction_limit()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
		
		return view($this->folder_path.'settings/transaction_limit',$data);		
	}


}