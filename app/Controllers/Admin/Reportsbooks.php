<?php
namespace App\Controllers\Admin;
use App\Models\Admin\LedgerModel;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Reportsbooks extends BaseController{
	
  function __construct(){  
    helper(['form', 'url','text']);
    $this->LedgerModel         = new LedgerModel();   
    $this->CommonModel   = new CommonModel();		
    $this->VouchersModel = new VouchersModel();
    $this->auth_session  = new auth_session();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
    $this->auth_session->role_restrict('CS');
    $this->base_url      = base_url().getenv('AdminPath');
    $this->folder_path   = getenv('AdminPath');
    $this->session    	 = \Config\Services::session();
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	$this->bo_id         =  $this->session->get('ses_boid');
    }
	
	public function ajax_day_book(){
		$view          = !empty($_GET['view']) ? $_GET['view'] : 0;         
        
		if($view==0)
        echo $this->LedgerModel->load_day_book_condensed();
		if($view==1)
	 	echo $this->LedgerModel->load_day_book_detailed($from_date, $to_date, $view);
      }
	  
	 public function day_book(){           
        $array = [];		
        $view      = isset($_GET['view']) ? $_GET['view'] : 0;
        $from_date = $_GET['fromdate'] ?? '';
        $to_date   = $_GET['todate'] ?? '';
        $from_date = validate_from_date($from_date);
        $to_date   = validate_to_date($to_date);
       
       	$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url']         = $this->base_url;
		$data['view']             = $view;
        $data['array']            = $array;
        $data['from_date']        = $from_date;
        $data['to_date']          = $to_date;
        return view($this->folder_path.'reports_books/day_book',$data); 
      }
}