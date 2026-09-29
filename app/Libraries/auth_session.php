<?php
namespace App\Libraries;

class auth_session {
 public $session;
 function __construct(){			
		$this->session  = \Config\Services::session();						
	 }	

	
  function user_restrict(){	
      
	   if ($this->session->get('uuid')=='' )
			{
            header("Location:".base_url().'login');
			die();
			} 
	}

 function is_company_opened(){			 
	    if ($this->session->get('ses_company_id')=='' )
			{
            header("Location:".base_url().'companies');
			die();
			} 
	  	
	}	

function role_restrict($role_type){			 

       /*  if ( $this->session->get('user_type')!='' && $this->session->get('user_type')!=$role_type)
			{
          		 header("Location:".base_url()."notauthorized");            
			   die();
			} */
	}
	
	
}