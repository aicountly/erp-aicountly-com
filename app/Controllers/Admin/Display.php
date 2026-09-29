<?php
namespace App\Controllers\Admin;
use App\Models\Admin\DisplayModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
class Display extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->DisplayModel = new DisplayModel();	
        $this->CommonModel  = new CommonModel();		
		$this->auth_session = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
   }
 
   public function trial_balance()
    {		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;		
		return view($this->folder_path.'display/trial_balance_report',$data);		
    } 
   
  public  function ajax_trialbalance_view() 
	 {
		$company_account_groups =  $this->CommonModel->company_account_groups($this->comp_code);		
		echo $this->DisplayModel->ajax_trialbalance_list($company_account_groups);		
	 } 
 
}