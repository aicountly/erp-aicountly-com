<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\LedgerModel;

class Reportsprojects extends BaseController{
	
  function __construct(){  
    helper(['form', 'url','text']);
	$this->session    	 = \Config\Services::session();
	$this->CommonModel   =  new CommonModel();		
    $this->VouchersModel =  new VouchersModel();
    $this->auth_session  =  new auth_session();
	$this->LedgerModel   =  new LedgerModel();	
	$this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
	$this->base_url      = base_url().getenv('AdminPath');
    $this->folder_path   = getenv('AdminPath');
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	$this->bo_id         =  $this->session->get('ses_boid');
    }

	public function index(){        
       	$from_date = '';
        $to_date   = '';        
        if($this->request->getMethod() == 'POST'){
			$from_date    = $this->request->getVar('from_date');
            $to_date      = $this->request->getVar('to_date');
            $from_date    = validate_from_date($from_date);
            $to_date      = validate_to_date($to_date);
            $report_type  = $_POST['report_type'];
            $sub_type     = $_POST['sub_type'];   
            if($report_type == 'Projects Trial'){
                if($sub_type == 'ALL Projects'){
                    return redirect()->to($this->base_url.'reports/project_trial?from_date='.$from_date.'&to_date='.$to_date);
                }                
            }            
            return redirect()->to($this->base_url.'reports/project_reporting'); 
        }

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['pr_list']                = [];//$this->ReportsModel->get_pr_list();
        $data['pr_groups']              = [];//$this->ReportsModel->get_pr_groups();
        return view($this->folder_path.'reports/project_report_dashboard',$data);  
    }
	
	public function ajax_project_account_trial(){
        echo  $this->LedgerModel->load_project_account_trial();
    }
	
    public function project_account_trial(){
        $from_date = $_GET['from_date'] ?? '';
        $to_date   = $_GET['to_date'] ?? '';
        $type      = $_GET['type'] ?? 1;
        $from_date = validate_from_date($from_date);
        $to_date   = validate_to_date($to_date);

        $data['message_output']  = $this->message_output;
        $data['folder_path']     = $this->folder_path;
        $data['base_url']        = $this->base_url;
        $data['from_date']       = $from_date;
        $data['to_date']         = $to_date;
        $data['type']            = $type;        
        return view($this->folder_path.'reports/project_reporting_account_trial',$data);
    }
	
	public function ajax_project_account_ledger(){
	 echo $response = $this->LedgerModel->load_project_account_ledger();
	}
	
	public function project_account_ledger($project_id){
        $from_date    = $_GET['from_date'] ?? '';
        $to_date      = $_GET['to_date'] ?? '';
        $type         = $_GET['type'] ?? 1;
        $from_date    = validate_from_date($from_date);
        $to_date      = validate_to_date($to_date);
        $project_name = '';        
        $project_info = $this->LedgerModel->get_project_info($project_id);
        if($project_info){
            $project_name = $project_info['project_name'];
        }
        $project_op_bal  = $this->LedgerModel->project_opening_balance($project_id,$to_date,$type);        
        if($project_op_bal < 0)
            $project_opening = formatAmount(abs($project_op_bal)).' CR.';
        else
            $project_opening = formatAmount($project_op_bal).' DR.';

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['project_id']             = $project_id;
        $data['project_name']           = $project_name;
        $data['project_opening']        = $project_opening;
        $data['type']                   = $type;        
        return view($this->folder_path.'reports/project_reporting_account_ledger',$data);

    }

	
}