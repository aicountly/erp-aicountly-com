<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\ScriptModel;

class Script extends BaseController
{
    var	$folder_path;
	function __construct()
    {  
	    helper(['form', 'url']);
	     		
		$this->auth_session   = new auth_session();
	    $this->folder_path    = getenv('AdminPath');
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
        $this->session    	 =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');
		$this->ScriptModel   =  new ScriptModel();
    }

    // https://sandbox.aicountly.in/admin/script
    public function index()
    {
        $this->ScriptModel->all_companies();
        echo "executed";
        // return redirect()->to('https://sandbox.aicountly.in/admin/reports/balance_sheet');
    }
    
   
}