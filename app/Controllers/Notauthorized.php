<?php
namespace App\Controllers;
class Notauthorized extends BaseController
{
	public function __construct()
    {  
	$this->session 	= \Config\Services::session();
	
	}
    public function index()
    {	
	   $s_user_type   = $this->session->get('user_type');
	   if($s_user_type=='A')
		 $back_to_dashboard = base_url().'/admin/dashboard';
	   else if($s_user_type=='S')
		 $back_to_dashboard = base_url().'/subadmin/dashboard'; 
	  else if($s_user_type=='C')
		 $back_to_dashboard = base_url().'/users/dashboard'; 
	  else
		 $back_to_dashboard = base_url();  
	 
	   $data['back_to_dashboard'] = $back_to_dashboard;
	    return view('notauthorized',$data);
	  
	}

}
