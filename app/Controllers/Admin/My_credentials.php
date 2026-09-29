<?php
namespace App\Controllers\Admin;
use App\Models\Admin\CredentialsModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
class My_credentials extends BaseController{
  function __construct(){  
	        helper(['form', 'url','text','custom']);
			$this->CredentialsModel  = new CredentialsModel();
			$this->CommonModel       = new CommonModel();		
			$this->auth_session      = new auth_session();
			$this->auth_session->user_restrict();
			$this->auth_session->is_company_opened();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().getenv('AdminPath');
			$this->folder_path   = getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->company_id    = $this->session->get('ses_company_id');
			$this->uuid          = $this->session->get('uuid');	
    }
		
	public function index($type='0'){		
	    $data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url']         = $this->base_url;
		$data['session']          = $this->session;
		$data['type']             = $type;			
		return view($this->folder_path.'mycredentials/view',$data);	
	}
	
    public  function ajax_credentials_view(){
		echo $response =  $this->CredentialsModel->ajax_credentials_list();			
	 } 
	   
    public function add(){
	    $bo_id = $this->session->get('ses_boid');
		if($this->request->getMethod() == 'POST'){	
			$cred_site_id   = $this->request->getVar('cred_id'); 
			if($cred_site_id==1)
			  $cred_type = $this->request->getVar('cred_type_gst');
		    else if($cred_site_id==2)
			  $cred_type = $this->request->getVar('cred_type_incometax');
		    else if($cred_site_id==3)
			  $cred_type = $this->request->getVar('cred_type_tds');
		    else
			 $cred_type = 0;
		 
			$cred_user   = $this->request->getVar('cred_user');
			$cred_pass   = $this->request->getVar('cred_pass');
		    $cred_remark = $this->request->getVar('cred_remark');
			
			$client_id   = $this->request->getVar('client_id');
		    $secret_key  = $this->request->getVar('secret_key');
			
		
	    	$rules = [				
				'cred_id' => [
					'label'  => 'Cred. Site',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please choose site',
					   ]]				  			   
			       ];			
           if(!$this->validate($rules)){
              	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }
			else{
				    $credential_data   = array(
										   "cmp_id"          => (int)$this->company_id,
										   "erp_pass_site"   => (int)$cred_site_id,
										   "erp_pass_type"   => (int)$cred_type,
										   "erp_pass_user"   => $cred_user,
										   "erp_pass_pwd"    => $cred_pass
										   );
					 $response =  $this->CredentialsModel->add_credential($credential_data);
					 if(!$response['status']){
						 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
						}						
					 return json_encode(['status' => true, 'message' => 'Data Inserted']);
		        }
	     }
	    $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['base_url']              = $this->base_url;
		return view($this->folder_path.'mycredentials/add',$data);   
	}	
	
    function modify($credid){
		if(!$credid)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$credential_info = $this->CredentialsModel->credential_info($credid);
		if(!$credential_info)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	
	 $bo_id = $this->session->get('ses_boid');
		if($this->request->getMethod() == 'POST'){	
			$cred_site_id   = $this->request->getVar('cred_id'); 
			if($cred_site_id==1)
			  $cred_type = $this->request->getVar('cred_type_gst');
		    else if($cred_site_id==2)
			  $cred_type = $this->request->getVar('cred_type_incometax');
		    else if($cred_site_id==3)
			  $cred_type = $this->request->getVar('cred_type_tds');
		    else
			  $cred_type =0;	
			$cred_user   = $this->request->getVar('cred_user');
			$cred_pass   = $this->request->getVar('cred_pass');
		    $cred_remark = $this->request->getVar('cred_remark');			
			$client_id   = $this->request->getVar('client_id');
		    $secret_key  = $this->request->getVar('secret_key');				
	    	$rules = [				
				'cred_id' => [
					'label'  => 'Cred. Site',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please choose site',
					   ]]				  			   
			       ];			
           if(!$this->validate($rules)){
              	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{
				    $credential_data = [
						'erp_pass_site'   => $cred_site_id,
						'erp_pass_type'   => $cred_type,
						'erp_pass_user'   => $cred_user						
					];
					if ($cred_pass !== '') {
						$credential_data['erp_pass_pwd'] = $cred_pass;
					}

				 $response = $this->CredentialsModel->update_credential($credid, $credential_data);
			     if(!$response['status']){
	        	  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				 }
							
		    return json_encode(['status' => true, 'message' => 'Data Updated']);
		 }
	  }
	    $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['base_url']              = $this->base_url;
		$data['credid']                = $credid;
		$data['credential_info']       = $credential_info;
		return view($this->folder_path.'mycredentials/edit',$data);	
		
	}	
	
    function remove($ids){
		 if(!$ids)
		 return json_encode(['status' => false, 'message' => 'Error', 'reload' => 1]);	
	 
		 $b64 = strtr($ids, '-_', '+/');
         $b64 .= str_repeat('=', (4 - strlen($b64) % 4) % 4);
	     $decoded = base64_decode($b64, true);
		 if ($decoded === false) {
			 return json_encode(['status' => false, 'message' => 'Error', 'reload' => 1]);	
		 }
		 $ids2 = array_filter(array_unique(array_map('trim', explode(',', $decoded))));
		 $errors = [];
		 foreach($ids2 as $account_id){
			$this->CredentialsModel->remove_single($account_id); 
		 }
	    return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);	
	}	
}