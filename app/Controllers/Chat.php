<?php
namespace App\Controllers;

use App\Models\CompanyAccessModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\auth_session;

class Chat extends BaseController
{
	public function __construct()
    {  
	  helper(['form', 'url']);
	 $this->session 	         = \Config\Services::session();	 
	 $this->auth_session  = new auth_session();			
	 $this->auth_session->user_restrict();
	 $this->auth_session->role_restrict('CS');	    
	 $this->folder_path          = getenv('AdminPath');
	 $this->base_url             = base_url();
	 $this->externaldb           = new externaldb();	 
	 $this->enc_string           = new enc_string();
	}
	

	public  function index()
	 {
		 if ($this->session->get('ses_company_id')!='' ){
			 $header_file='includes/header';
			 
		 }else{
			$header_file='includes/header2'; 
		 }
		 
	   	$data['message_output']   = $this->message_output;
		$data['header_file']   = $header_file;
		$data['folder_path']      = $this->folder_path;
		$data['base_url_path']    = $this->base_url;
	    return view('chat',$data); 
	 }

}