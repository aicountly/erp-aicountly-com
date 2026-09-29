<?php
namespace App\Controllers;
use App\Models\CronsModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\auth_session;
use App\Helper\custom_helper;

class Crons extends BaseController
{
	public function __construct()
    {  
	  helper(['form', 'url']);
	 $this->session 	         = \Config\Services::session();	 
	 $this->auth_session  = new auth_session();			
	 $this->auth_session->user_restrict();
	 $this->auth_session->role_restrict('CS');
	 $this->CronsModel           = new CronsModel();     
	 $this->folder_path          = getenv('AdminPath');
	 $this->base_url             = base_url();
	 $this->externaldb           = new externaldb();	 
	 $this->enc_string           = new enc_string();
	}
	
  public function auto_remove_comp(){
        $company_list = $this->CronsModel->ajax_recyclebin_company_list();
        if($company_list){
          mail("bhupimahey@gmail.com","Remove from recycle bin - Aicountly",'Company Db Deleted after 30 days from recycle bin');
          echo 'success';
          }
    }
    
  public function auto_remove_vouchers(){
      $company_list     = $this->CronsModel->ajax_recyclebin_vouchers_list();
      if($company_list){
          mail("bhupimahey@gmail.com","Remove from recycle bin - Aicountly",'Voucher Db Deleted after 30 days from recycle bin');
          echo 'success';
      }
    }
    
    
}