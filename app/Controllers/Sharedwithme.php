<?php
namespace App\Controllers;

use App\Models\CompanyAccessModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\auth_session;

class Sharedwithme extends BaseController
{
	public function __construct()
    {  
	  helper(['form', 'url']);
	 $this->session 	         = \Config\Services::session();	 
	 $this->auth_session  = new auth_session();			
	 $this->auth_session->user_restrict();
	 $this->auth_session->role_restrict('CS');
	 $this->CompanyAccessModel   = new CompanyAccessModel();     
	 $this->folder_path          = getenv('AdminPath');
	 $this->base_url             = base_url();
	 $this->externaldb           = new externaldb();	 
	 $this->enc_string           = new enc_string();
	}
	

	public function ajax_shared_companies(){
	    
	   echo  $this->CompanyAccessModel->ajax_sharedcompany_list();
	    
	}
	
	public  function index()
	 {
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('sharedwithme',$data); 
	 }


/* 	public  function group()
	{
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
		$data['data']	= $this->CompanyAccessModel->shared_group_company_list();
	    return view('group_company_shared',$data); 
	} */
}