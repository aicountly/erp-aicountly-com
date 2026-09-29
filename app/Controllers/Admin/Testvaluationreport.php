<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ReportsModel;
use App\Models\CommonModel;
use App\Models\Admin\TransactionModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\externaldb;

class Testvaluationreport extends BaseController{

  function __construct(){  
    helper(['form', 'url','text','einvoice']);
	$this->externaldb        = new externaldb();
    $this->CommonModel       = new CommonModel();		
    $this->TransactionModel  = new TransactionModel();
	$this->ReportsModel      = new ReportsModel();
    $this->auth_session      = new auth_session();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
    $this->auth_session->role_restrict('CS');
    $this->base_url       =  base_url().'/'.getenv('AdminPath');
    $this->folder_path    =  getenv('AdminPath');
    $this->session    	  =  \Config\Services::session();
    $this->forge          =  \Config\Database::forge();
    $this->company_id     =  $this->session->get('ses_company_id');
    $this->comp_code      =  $this->session->get('ses_company_code');
	$this->aicountly_db   =  $this->externaldb->aicountly_db();
    $this->ses_comp_fy_id =  $this->session->get('ses_comp_fy_id');	
  }
  

  public function ajax_rptbl_transactions(){
	$pq_curPage = (int)$_POST["pq_curpage"];
    $limit     = (int)$_POST["pq_rpp"];
    $search = '';
    $pq_filter    = $this->request->getVar('pq_filter');
    if(!empty($pq_filter)){
      $pq_filter = json_decode($pq_filter);
      $search    = $pq_filter->data[0]->value;
    }
    $from_date   = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	$to_date     = date('Y-m-d',strtotime($this->request->getVar('to_date')));
	$table_id    = $this->request->getVar('table_id');
	echo $this->ReportsModel->load_rptables_listing($pq_curPage, $limit, $from_date, $to_date, $table_id); 
   }	
 
  public function reporting_table($table_id=''){	
   if($table_id!=''){
   $from_date = $_GET['fromdate'] ?? '';
   $to_date   = $_GET['todate'] ?? '';   
   
   $from_date = validate_fy_from_date($from_date);
   $to_date   = validate_fy_to_date($to_date);
		
   $from_date_ymd     = date('d-m-Y', strtotime($from_date));
   $to_date_ymd        = date('d-m-Y', strtotime($to_date));
   $data['colModels']  = $this->ReportsModel->load_rptables_columns($table_id); 
   $data['from_date']  = $from_date_ymd;
   $data['to_date']    = $to_date_ymd;
   $data['table_id']   = $table_id;
   return view($this->folder_path.'testvaluationreport',$data); 
   }
		
 }	 
 
}