<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\LedgerModel;

class Reportsbills extends BaseController{
	
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
			$bill_type      = $_POST['bill_type'];            
            $from_date      = date('d-m-Y',strtotime($_POST['fromdate']));
            $to_date        = date('d-m-Y',strtotime($_POST['todate']));
            $criteria       = $_POST['criteria'];
			
            if($criteria == 'Account Wise Bill Summary'){
                return redirect()->to($this->base_url.'reports/bills_management_accounts/'.$bill_type.'?from_date='.$from_date.'&to_date='.$to_date);
            }            
            if($criteria == 'One Account'){
                $id = $_POST['id'];
                return redirect()->to($this->base_url.'reports/bills_management_one_account/'.$id.'/'.$bill_type.'?from_date='.$from_date.'&to_date='.$to_date);
            }
            if($criteria == 'Bill Wise Statement'){
                return redirect()->to($this->base_url.'reports/bills_management_statement/'.$bill_type.'?from_date='.$from_date.'&to_date='.$to_date);
            }
            
            return redirect()->to($this->base_url.'reports/bills_management'); 
        }
		
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['sundry_accounts']        = $this->LedgerModel->get_sundry_accounts();
		
        $data['sundry_groups']          = [];//$this->ReportsModel->get_pr_groups();
        return view($this->folder_path.'reports/bills_dashboard',$data);  
    }
	
	public function ajax_project_account_trial(){
        echo  $this->LedgerModel->load_project_account_trial();
    }
    
    public function ajax_billwise_acc_statement(){
        echo  $this->LedgerModel->load_bills_management_accounts();
    }
	 public function ajax_billwise_statement(){
        echo  $this->LedgerModel->load_bills_management_refs();
    }
	
	function bills_management_statement($bill_type) 
    {
        $from_date     = $_GET['from_date'] ?? '';
        $to_date       = $_GET['to_date'] ?? '';
        $from_date     = validate_from_date($from_date);
        $to_date       = validate_to_date($to_date);
        $from_date_ymd = date('Y-m-d',strtotime($from_date));
        $to_date_ymd   = date('Y-m-d',strtotime($to_date));

        $data['data'] = [];//$this->ReportsModel->load_bills_management_statement($from_date_ymd, $to_date_ymd, $bill_type);
        
        $data['message_output']   = $this->message_output;
        $data['folder_path']      = $this->folder_path;
        $data['base_url']         = $this->base_url;
        $data['from_date']        = $from_date;
        $data['to_date']          = $to_date;
        $data['bill_type']        = $bill_type;        
        return view($this->folder_path.'reports/bills_management_statement',$data);
    }
    
    public function ajax_bills_management_one_account(){
        echo  $this->LedgerModel->load_bills_management_one_account();
    }
    
    
     public function ajax_bills_management_details(){
        echo  $this->LedgerModel->load_bills_management_details();
    }
    
    
    function bills_management_details($bills_ref_id,$bill_type)
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        $bill_ref = '';
		$opening_balance_drcr='DR.';
		$opening_balance     = 0;
        $bill = $this->LedgerModel->get_bill_details($bills_ref_id);
		if($bill){
            $bill_ref = $bill['bill_ref_name'];
            $acc_id = $bill['acc_id'];
            $account = $this->LedgerModel->account_info($bill['acc_id']);
            $account_name = $account['acc_name'] ?? '';
			
			$opening_balance_drcr = ($bill['bill_op_bal']<0)?'CR.':'DR.';
			$opening_balance = formatAMount(abs($bill['bill_op_bal'])) ?? 0;
        }
        else{
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
        }
        
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['bills_ref_id']           = $bills_ref_id;
        $data['bills_ref_name']         = $bill_ref;
        $data['bill_type']              = $bill_type;
        $data['acc_id']                 = $acc_id;
        $data['account_name']           = $account_name;
		
		$data['opening_balance_drcr']   = $opening_balance_drcr;
		$data['opening_balance']        = $opening_balance;
        
        return view($this->folder_path.'reports/bills_management_details',$data);
    } 
    
    
    function bills_management_one_account($account_id,$bill_type) 
    {
        $from_date     = $_GET['from_date'] ?? '';
        $to_date       = $_GET['to_date'] ?? '';
        $from_date     = validate_from_date($from_date);
        $to_date       = validate_to_date($to_date);
        $from_date_ymd = date('Y-m-d',strtotime($from_date));
        $to_date_ymd   = date('Y-m-d',strtotime($to_date));
         $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
        $account_name = '';
        $account = $this->LedgerModel->account_info($account_id);
        if($account){
            $group = $this->LedgerModel->get_group_details($account['under_crs_mst_id']);
            $account_name = $account['acc_name'] . ' ('. $group['acc_grp_name'] .')';
        }
        

        $data['data'] = [];//$this->ReportsModel->load_bills_management_statement($from_date_ymd, $to_date_ymd, $bill_type);
        
        $data['message_output']   = $this->message_output;
        $data['folder_path']      = $this->folder_path;
        $data['base_url']         = $this->base_url;
        $data['from_date']        = $from_date;
        $data['to_date']          = $to_date;
        $data['bill_type']        = $bill_type;   
         $data['account_id']        = $account_id; 
         $data['account_name']        = $account_name;
         $data['nil_type']           = $nil_type;
        return view($this->folder_path.'reports/bills_management_one_account',$data);
    }
    
    

    public function bills_management_accounts($type){
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $from_date_ymd = date('Y-m-d',strtotime($from_date));
        $to_date_ymd   = date('Y-m-d',strtotime($to_date));

        $data['data'] = [];//$this->LedgerModel->load_bills_management_accounts($from_date_ymd, $to_date_ymd, $type);
        
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['bill_type']              = $type;
        
        return view($this->folder_path.'reports/bills_management_accounts',$data);
    }
    
        
	
}