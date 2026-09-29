<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\LedgerModel;

class Reportscc extends BaseController{
	
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
            $from_date      = !empty($_POST['from_date']) ? date('d-m-Y',strtotime($_POST['from_date'])) : '';
	        $to_date        = !empty($_POST['to_date']) ? date('d-m-Y',strtotime($_POST['to_date'])) : '';            
            $report_type    = $_POST['report_type'];
            $sub_type       = $_POST['sub_type'];   
            if($report_type == 'ACCOUNT WISE REPORT'){
                if($sub_type == 'COST CENTRE-ACCOUNT WISE'){
                    return redirect()->to($this->base_url.'reports/cost_centre_account_wise?from_date='.$from_date.'&to_date='.$to_date);
                }
                if($sub_type == 'ACCOUNT WISE-COST CENTRE'){
                    return redirect()->to($this->base_url.'reports/cost_centre_wise?from_date='.$from_date.'&to_date='.$to_date);
                }
                
            }
            if($report_type == 'COST CENTRE TRIAL'){
                if($sub_type == 'ALL COST CENTRES'){
                    return redirect()->to($this->base_url.'reports/cost_centre_trial?from_date='.$from_date.'&to_date='.$to_date);
                }
                
            }
            if($report_type == 'COST CENTRE LEDGER'){
                if($sub_type == 'COST CENTRE'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_account_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
                }
            }
            
            return redirect()->to($this->base_url.'reports/cost_centre'); 
        }
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['cc_list']                =  $this->LedgerModel->get_cc_list();
        $data['cc_groups']              = [];//$this->ReportsModel->get_pr_groups();
        return view($this->folder_path.'reports/cost_centre_dashboard',$data);  
    }
	
	public function ajax_cc_account_trial(){
        echo  $this->LedgerModel->load_project_account_trial();
    }
	
    public function cc_account_trial(){
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
        return view($this->folder_path.'reports/cost_centre_trial.php',$data);
    }
	
	public function ajax_cost_centre_account_ledger(){
	 echo $response = $this->LedgerModel->load_cc_account_ledger();
	}
	
	public function cost_centre_account_ledger($cc_id){
        $from_date    = $_GET['from_date'] ?? '';
        $to_date      = $_GET['to_date'] ?? '';
        $type         = $_GET['type'] ?? 1;
        $from_date    = validate_from_date($from_date);
        $to_date      = validate_to_date($to_date);
        $cc_name = '';        
        $project_info = $this->LedgerModel->get_cc_info($cc_id);
        if($project_info){
            $cc_name = $project_info['cc_name'];
        }
        $cc_op_bal  = $this->LedgerModel->cc_opening_balance($cc_id,$to_date,$type);        
        if($cc_op_bal < 0)
            $cc_opening = formatAmount(abs($cc_op_bal)).' CR.';
        else
            $cc_opening = formatAmount($cc_op_bal).' DR.';

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['cc_id']                  = $cc_id;
        $data['cc_name']                = $cc_name;
        $data['cc_opening']             = $cc_opening;
        $data['type']                   = $type;        
        return view($this->folder_path.'reports/cost_centre_account_ledger',$data);

    }
	
	function ajax_cost_centre_account_wise(){
        echo $this->LedgerModel->load_cost_centre_account_wise();
    }

	public function cost_centre_account_wise(){
        $from_date = '';
        $to_date   = '';        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']             = $from_date;
        $data['to_date']               = $to_date;
        
        return view($this->folder_path.'reports/cost_centre_account_wise',$data);

    }
	
	function ajax_cost_centre_wise(){       
        echo $this->LedgerModel->load_cost_centre_wise();
    }

	public function cost_centre_wise(){
        $from_date = '';
        $to_date   = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        
        return view($this->folder_path.'reports/cost_centre_wise',$data);

    }

	
}