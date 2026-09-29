<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\AccountsModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class Bulk_updation extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text']);
	    $this->ItemsModel     = new ItemsModel();
	    $this->AccountsModel  = new AccountsModel();
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->is_company_opened();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->forge         = \Config\Database::forge();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->enc_string    = new enc_string();
		
    }
    
   public function index()
   {
		$data['base_url']           = $this->base_url;	
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['session']           = $this->session;
		
        return view($this->folder_path.'bulk_updation/view',$data);  
   }


  
}