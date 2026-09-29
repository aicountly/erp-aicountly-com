<?php
namespace App\Controllers;
use App\Models\CommonModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\auth_session;

class Archivecompany extends BaseController
{
	public function __construct()
    {  
	  helper(['form', 'url']);
	 $this->session 	         = \Config\Services::session();	 
	 $this->auth_session  = new auth_session();			
	 $this->auth_session->user_restrict();
	 $this->auth_session->role_restrict('CS');
	 $this->CommonModel          = new CommonModel();     
	 $this->folder_path          = getenv('AdminPath');
	 $this->base_url             = base_url();
	 $this->externaldb           = new externaldb();	 
	 $this->enc_string           = new enc_string();
	}
	
		
    public function ajax_archive_companies_list(){
	    die();
	   echo  $this->CommonModel->ajax_archive_companies_list();
	    
	}
	
	public  function index()
	 {
	     die();
	   	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('archive_company',$data); 
	 }
}