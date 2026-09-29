<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\ReportsModel;
use App\Models\Grpcomp\ReportsModel2;
use App\Models\Grpcomp\ReportsModel3;

use App\Models\CommonModel;
use App\Models\Grpcomp\TransactionModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Reports extends BaseController{
  function __construct(){  
   helper(['form', 'url','text']);
   $this->ReportsModel    = new ReportsModel();
   $this->ReportsModel2   = new ReportsModel2();
   $this->ReportsModel3        = new ReportsModel3();
   $this->CommonModel     = new CommonModel();				
   $this->TransactionModel  = new TransactionModel();
   $this->auth_session    = new auth_session();
   $this->auth_session->group_restrict();	   
   $this->auth_session->role_restrict('CS');
   $this->base_url      = base_url().'/'.getenv('GroupPath');
   $this->folder_path   = getenv('GroupPath');
   $this->session    	 = \Config\Services::session();
   $this->forge         = \Config\Database::forge();
   $this->company_id    =  $this->session->get('ses_company_id');
   $this->comp_code     =  $this->session->get('ses_company_code');
   $this->enc_string    = new enc_string();
   $this->ses_comp_fy_id=  $this->session->get('ses_comp_fy_id');
   
 }

 function test()
 {
  $this->ReportsModel3->test();
 }

  function balance_sheet() 
  {
   $view = isset($_GET['view']) ? $_GET['view'] : 1;
   $format = isset($_GET['format']) ? $_GET['format'] : 1;
   $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;


   $from_date = $_GET['from_date'] ?? '';
   $to_date = $_GET['to_date'] ?? '';

   $from_date = grp_validate_fy_from_date($from_date);
   $to_date = grp_validate_fy_to_date($to_date);

   $from_date_ymd = date('Y-m-d', strtotime($from_date));
   $to_date_ymd = date('Y-m-d', strtotime($to_date)); 

   $balance_sheet = [];

   if($format == 1)
    $balance_sheet = $this->ReportsModel3->load_balance_sheet($view,$from_date_ymd,$to_date_ymd,$nil_type);

    if($format == 2)
      $balance_sheet = $this->ReportsModel3->load_balance_sheet2($view,$from_date_ymd,$to_date_ymd,$nil_type);

        // echo "<pre>";print_r($balance_sheet);exit;

    $data     = [
      'base_url'      => $this->base_url,
      'folder_path'   => $this->folder_path,
      'data'          => $balance_sheet,
      'view'          => $view,
      'format'        => $format,
      'nil_type'      => $nil_type
    ];

    $data['from_date'] = $from_date;
    $data['to_date']   = $to_date;

    if($format == 1)
      return view($this->folder_path.'reports/balance_sheet',$data);
    if($format == 2)
      return view($this->folder_path.'reports/balance_sheet2',$data);
  }

  function profit_loss()
  {
    $view = isset($_GET['view']) ? $_GET['view'] : 1;
    $format = isset($_GET['format']) ? $_GET['format'] : 1;
    $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;

    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    $from_date = grp_validate_fy_from_date($from_date);
    $to_date = grp_validate_fy_to_date($to_date);

    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd = date('Y-m-d', strtotime($to_date));

    $profit_loss = [];
    if($format == 1)
      $profit_loss = $this->ReportsModel3->load_profit_loss($view,$from_date_ymd,$to_date_ymd,$nil_type);
    if($format == 2)
      $profit_loss = $this->ReportsModel3->load_profit_loss2($view,$from_date_ymd,$to_date_ymd,$nil_type);

    $data = [
      'base_url'      => $this->base_url,
      'folder_path'   => $this->folder_path,
      'data'          => $profit_loss,
      'view'          => $view,
      'format'        => $format,
      'nil_type'      => $nil_type
    ]; 

    $data['from_date']   = $from_date;
    $data['to_date']     = $to_date;

    if($format == 1)
      return view($this->folder_path.'reports/profit_loss',$data);
    if($format == 2)
      return view($this->folder_path.'reports/profit_loss2',$data);
  }

  function trial_balance()
  {
    $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
    $view = isset($_GET['view']) ? $_GET['view'] : 0;

    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    $from_date = grp_validate_fy_from_date($from_date);
    $to_date = grp_validate_fy_to_date($to_date);

    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd = date('Y-m-d', strtotime($to_date));


    if($view == 0)
        $data['trial_balance_list'] = [];//$this->ReportsModel3->load_trial_balance($from_date_ymd,$to_date_ymd);
    else if($view == 1)
        $data['trial_balance_list'] = $this->ReportsModel3->load_trial_balance1($from_date_ymd,$to_date_ymd);
    else
        $data['trial_balance_list'] = $this->ReportsModel3->load_trial_balance_op();

    $data['from_date']   = $from_date;
    $data['to_date']     = $to_date; 
    $data['nil_type']    = $nil_type;
    $data['view']    = $view;
    $data['base_url']    = $this->base_url;
    $data['folder_path'] = $this->folder_path;
    return view($this->folder_path.'reports/trial_balance',$data);
  }

    public function accounts_trial($acc_grp_parent_id)
    {
        $data['from_date'] = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
        $data['to_date'] = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');

        $from_date_ymd = date('Y-m-d', strtotime($data['from_date']));
        $to_date_ymd = date('Y-m-d', strtotime($data['to_date']));

        $parent_info = $this->ReportsModel2->parent_info($acc_grp_parent_id);
        $parent_name = $parent_info['grp_name'] ?? '';
        
        $data['message_output'] = $this->message_output;
        $data['folder_path']    = $this->folder_path;
        $data['base_url']       = $this->base_url;
        $data['name']     = $parent_name;
        $data['data']        = $this->ReportsModel2->load_accounts_trial($acc_grp_parent_id,$from_date_ymd,$to_date_ymd);
        
        return view($this->folder_path.'reports/accounts_trial',$data);
    }


 
  public function ajax_exceptions_txns(){
       
       echo $response =  $this->ReportsModel->exceptions_txns_list();	
       
   }
   
   public function exceptions_txn(){
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;
        return view($this->folder_path.'reports/exceptions_txn',$data);  
    } 
   
    public function remove_exception_vouchers(){
         if($this->request->getMethod() == 'post'){	
            $success        = 0; 
            $vchrids        = $this->request->getVar('vchrids');
            $accttxnoth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
            if($vchrids){
                foreach($vchrids as $voucher_info){
                   $vchrids          = explode("||",$voucher_info);
                   $acc_oth_txn_ids  = $vchrids[0];
                   $voucher_txn_id   = $vchrids[1];
                   $this->ReportsModel->remove_exceptions_items($accttxnoth_tbl,$voucher_txn_id,$acc_oth_txn_ids);
                   $success++;   
                }
                
            }
            
            if($success>0)
              echo "1";
             else
             echo "0";
             die();
          
         }
    }
  
   public function ajax_get_exception_txn_items($acct_txn_id,$voucher_type_id){
      echo $response   =  $this->ReportsModel->get_exception_txn_items( $this->company_id,$acct_txn_id,$voucher_type_id);
     }
   
     public function exception_txn_items($acct_txn_id,$voucher_type_id){
       	$data['message_output'] = $this->message_output;
		$data['folder_path']    = $this->folder_path;
		$data['base_url']       = $this->base_url;
	    $data['acct_txn_id']      = $acct_txn_id;
        $data['voucher_type_id']        = $voucher_type_id;
        return view($this->folder_path.'reports/exception_txn_items',$data); 
     }
  
    public function delete_all_xyz()
    {
        $this->TransactionModel->delete_all_xyz();
        echo "done";
    }


  

	

	


 
	
  function account_ledger()
  {

    if($this->request->getMethod() == 'post')
    {	
      $type           = $this->request->getVar('type');
      $detail_type    = $this->request->getVar('detail');

      $from_date = $this->request->getVar('from_date');
      $to_date = $this->request->getVar('to_date');

      $from_date = grp_validate_fy_from_date($from_date);
      $to_date = grp_validate_fy_to_date($to_date);


      if($type == 'Account Ledger')
      {
        if($detail_type=='Account'){
          $id    = $this->request->getVar('id');
          return redirect()->to($this->base_url.'reports/account_ledger_detail/'.$id.'?from_date='.$from_date.'&to_date='.$to_date); 
        }
        else if($detail_type=='Account Group'){
          $id    = $this->request->getVar('id');
          return redirect()->to($this->base_url.'reports/account_group_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);  
        } 
        else if($detail_type=='Account/Group List'){
          $id    = $this->request->getVar('id');
          return redirect()->to($this->base_url.'reports/accounts_trial/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);  
        }
        else if($detail_type=='Account/Group Parent'){
          $id    = $this->request->getVar('id');
          return redirect()->to($this->base_url.'reports/parent_ledger_detail?parent_id='.$id.'?from_date='.$from_date.'&to_date='.$to_date);  
        }
      }
      if($type == 'Bill Sundry Ledger')
      {
        if($detail_type=='Bill Sundry Account'){
          $id    = $this->request->getVar('id');    
          return redirect()->to($this->base_url.'reports/bill_sundry_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);  
        }
      }
      if($type == 'Cost Centre Ledger')
      {
        if($detail_type == 'Cost Centre Account'){
          $id = $this->request->getVar('id');
          return redirect()->to($this->base_url.'reports/cost_centre_account_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
        }
        if($detail_type == 'Cost Centre Group'){
          $id = $this->request->getVar('id');
          return redirect()->to($this->base_url.'reports/cost_centre_group_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
        }
      }
    }

    $dropdown = [
      'Account Ledger'     => ['Account', 'Account Group','Account/Group List', 'Account/Group Parent'],
      'Bill Sundry Ledger' => ['Bill Sundry Account'],
      'Cost Centre Ledger' => ['Cost Centre Account', 'Cost Centre Group']
    ];

    $data['message_output']        = $this->message_output;
    $data['folder_path']           = $this->folder_path;
    $data['base_url']              = $this->base_url;

    $data['accounts_list'] = $this->ReportsModel3->get_master_list('acctmaster');
  	$data['groups_list'] = $this->ReportsModel3->get_master_list('acctgroupn');
    $data['group_parent_list'] = $this->ReportsModel3->get_all_group_parents();
    $data['bill_sundry_list'] = $this->ReportsModel3->get_master_list('billsundry');
    $data['cc_list'] = $this->ReportsModel3->get_master_list('costctmstr');
    $data['cc_groups'] = $this->ReportsModel3->get_master_list('costctgrup');
    $data['dropdown'] = $dropdown;

    return view($this->folder_path.'reports/account_ledger',$data);  
  }

  function account_ledger_detail($crs_master_id)
  {
    $m      = !empty($_GET['m']) ? $_GET['m'] : 0;
    $view   = !empty($_GET['view']) ? $_GET['view'] : 0;

    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    $from_date = grp_validate_fy_from_date($from_date);
    $to_date = grp_validate_fy_to_date($to_date);

    $from_date_ymd = date('Y-m-d',strtotime($from_date));
    $to_date_ymd   = date('Y-m-d',strtotime($to_date));
 
    $account_name = $this->ReportsModel3->get_master_name('acctmaster',$crs_master_id);

    $data['account_name']     = $account_name;
    $data['account_id']       = $crs_master_id;
    
    $data['from_date']        = $from_date;
    $data['to_date']          = $to_date;
    $data['m']                = $m;
    $data['view']             = $view;
    $data['message_output']   = $this->message_output;
    $data['folder_path']      = $this->folder_path;
    $data['base_url']         = $this->base_url;
    $data['opn_balance_list'] = $this->ReportsModel3->get_acc_opn_bal_list($crs_master_id,$from_date_ymd);
    $data['clo_balance_list'] = $this->ReportsModel3->get_acc_clo_bal_list($crs_master_id,$to_date_ymd);

    $data['accounts_list']    = $this->ReportsModel3->get_master_list('acctmaster');

    $data['transactions']    = $this->ReportsModel3->load_accounts_ledger_condensed($crs_master_id,$from_date_ymd,$to_date_ymd);

    return view($this->folder_path.'reports/account_ledger_view',$data); 
  }
    
  function bill_sundry_ledger($crs_master_id)
  {
    $view   = !empty($_GET['view']) ? $_GET['view'] : 0;

    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    $from_date = grp_validate_fy_from_date($from_date);
    $to_date = grp_validate_fy_to_date($to_date);

    $from_date_ymd = date('Y-m-d',strtotime($from_date));
    $to_date_ymd   = date('Y-m-d',strtotime($to_date));
 
    $account_name = $this->ReportsModel3->get_master_name('billsundry',$crs_master_id);

    $data['account_name']     = $account_name;
    $data['account_id']       = $crs_master_id;
    
    $data['from_date']        = $from_date;
    $data['to_date']          = $to_date;
    $data['view']             = $view;
    $data['message_output']   = $this->message_output;
    $data['folder_path']      = $this->folder_path;
    $data['base_url']         = $this->base_url;
    $data['opn_balance_list'] = $this->ReportsModel3->get_bsd_opn_bal_list($crs_master_id,$from_date_ymd);
    $data['clo_balance_list'] = $this->ReportsModel3->get_bsd_clo_bal_list($crs_master_id,$to_date_ymd);

    $data['accounts_list']    = $this->ReportsModel3->get_master_list('billsundry');

    $data['transactions']    = $this->ReportsModel3->load_bill_sundry_condensed($crs_master_id,$from_date_ymd,$to_date_ymd);

    return view($this->folder_path.'reports/bill_sundry_ledger',$data); 
  }

  function account_group_ledger($crs_master_id)
  {
    $view   = !empty($_GET['view']) ? $_GET['view'] : 0;

    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    $from_date = grp_validate_fy_from_date($from_date);
    $to_date = grp_validate_fy_to_date($to_date);

    $from_date_ymd = date('Y-m-d',strtotime($from_date));
    $to_date_ymd   = date('Y-m-d',strtotime($to_date));
 
    $account_name = $this->ReportsModel3->get_master_name('acctgroupn',$crs_master_id);

    $data['account_name']     = $account_name;
    $data['account_id']       = $crs_master_id;
    
    $data['from_date']        = $from_date;
    $data['to_date']          = $to_date;

    $data['view']             = $view;
    $data['message_output']   = $this->message_output;
    $data['folder_path']      = $this->folder_path;
    $data['base_url']         = $this->base_url;

    $data['accounts_list']    = $this->ReportsModel3->get_master_list('acctgroupn');

    $data['transactions']    = $this->ReportsModel3->load_account_group_ledger($crs_master_id,$from_date_ymd,$to_date_ymd);

    return view($this->folder_path.'reports/account_group_ledger',$data); 
  }

  function account_summaries(){
       
    if($this->request->getMethod() == 'post'){ 

      $summary_type    = $this->request->getVar('summary_type');
      $summary_detail    = $this->request->getVar('summary_detail');

 
      if($summary_type == 'Account Summary')
      {
          if($summary_detail == 'Ledger'){
              $id    = $this->request->getVar('id');    
              return redirect()->to($this->base_url.'reports/account_summary/'.$id);
          }
          if($summary_detail == 'Account Group'){
              $id    = $this->request->getVar('id');    
              return redirect()->to($this->base_url.'reports/account_group_summary/'.$id);
          }
      }
      if($summary_type == 'Cost Centre Summary')
      {
          if($summary_detail == 'Cost Centre'){
              $id    = $this->request->getVar('id');    
              return redirect()->to($this->base_url.'reports/cost_centre_account_summary/'.$id);
          }
          if($summary_detail == 'Cost Centre Group'){
              $id    = $this->request->getVar('id');    
              return redirect()->to($this->base_url.'reports/cost_centre_group_summary/'.$id);
          }
      }
      if($summary_type == 'Bill Sundry Summary')
      {
          if($summary_detail == 'Account'){
              $id    = $this->request->getVar('id');    
              return redirect()->to($this->base_url.'reports/bill_sundry_summary/'.$id);
          }
      }
      if($summary_type == 'Purchase Summary')
      {
          if($summary_detail == 'Purchases'){    
              return redirect()->to($this->base_url.'reports/purchase_summary');
          }
          if($summary_detail == 'Purchase Return'){    
              return redirect()->to($this->base_url.'reports/purchase_return_summary');
          }
          if($summary_detail == 'Purchase Requisition'){    
              return redirect()->to($this->base_url.'reports/purchase_requisition_summary');
          }
          if($summary_detail == 'Purchase Order'){    
              return redirect()->to($this->base_url.'reports/purchase_order_summary');
          }
      }
      if($summary_type == 'Sales Summary')
      {
          if($summary_detail == 'Sales'){    
              return redirect()->to($this->base_url.'reports/sales_summary');
          }
          if($summary_detail == 'Sales Return'){    
              return redirect()->to($this->base_url.'reports/sale_return_summary');
          }
          if($summary_detail == 'Quotation'){    
              return redirect()->to($this->base_url.'reports/quotation_summary');
          }
          if($summary_detail == 'Sales Order'){    
              return redirect()->to($this->base_url.'reports/sale_order_summary');
          }
      }
      if($summary_type == 'Banking Summary')
      {
          if($summary_detail == 'Payments'){    
              return redirect()->to($this->base_url.'reports/payments_summary');
          }
          if($summary_detail == 'Receipts'){    
              return redirect()->to($this->base_url.'reports/receipts_summary');
          }
          if($summary_detail == 'Contra'){    
              return redirect()->to($this->base_url.'reports/contra_summary');
          }
          if($summary_detail == 'Journal'){    
              return redirect()->to($this->base_url.'reports/journal_summary');
          }
      }    
    }
    
    $summary_detail_dropdown = [
      'Account Summary' => ['Ledger','Account Group'],
      'Sales Summary' => ['Sales','Sales Return','Sales Order','Quotation'],
      'Purchase Summary' => ['Purchases', 'Purchase Return', 'Purchase Order', 'Purchase Requisition'],
      'Banking Summary' => ['Payments', 'Receipts', 'Contra', 'Journal'],
      'Cost Centre Summary' => ['Cost Centre', 'Cost Centre Group'],
      'Bill Summary' => ['All Bills', 'Bills Receivable','BillPayable'],
      'Bill Sundry Summary' => ['Account'],
    ];
 
    $data['message_output'] = $this->message_output;
    $data['folder_path']    = $this->folder_path;
    $data['base_url']       = $this->base_url;
    $data['summary_detail_dropdown'] = $summary_detail_dropdown;

    $data['accounts_list'] = $this->ReportsModel3->get_master_list('acctmaster');
    $data['groups_list'] = $this->ReportsModel3->get_master_list('acctgroupn');
    $data['group_parent_list'] = $this->ReportsModel3->get_all_group_parents();
    $data['bill_sundry_list'] = $this->ReportsModel3->get_master_list('billsundry');
    $data['cc_list'] = $this->ReportsModel3->get_master_list('costctmstr');
    $data['cc_groups'] = $this->ReportsModel3->get_master_list('costctgrup');

    return view($this->folder_path.'reports/account_summaries',$data);   
  }

  function account_summary($crs_master_id)
  {
 
    $account_name = $this->ReportsModel3->get_master_name('acctmaster',$crs_master_id);

    $op_balance = $this->ReportsModel3->get_acc_opn_bal_crs($crs_master_id);
    $op_balance_type = 'DR';
    if($op_balance < 0)
        $op_balance_type = 'CR';

    $data['op_balance'] = formatAmount(abs($op_balance));
    $data['op_balance_type'] = $op_balance_type;

    $data['account_name']     = $account_name;
    $data['account_id']       = $crs_master_id;
    
    $data['message_output']   = $this->message_output;
    $data['folder_path']      = $this->folder_path;
    $data['base_url']         = $this->base_url;

    $data['summary'] = $this->ReportsModel3->load_accounts_summary($crs_master_id);

    return view($this->folder_path.'reports/account_summary',$data);
  }

  function bill_sundry_summary($crs_master_id)
  {
    $account_name = $this->ReportsModel3->get_master_name('billsundry',$crs_master_id);

    $op_balance = $this->ReportsModel3->get_bsd_opn_bal_crs($crs_master_id);
    $op_balance_type = 'DR';
    if($op_balance < 0)
        $op_balance_type = 'CR';

    $data['op_balance'] = formatAmount(abs($op_balance));
    $data['op_balance_type'] = $op_balance_type;

    $data['account_name']     = $account_name;
    $data['account_id']       = $crs_master_id;
    
    $data['message_output']   = $this->message_output;
    $data['folder_path']      = $this->folder_path;
    $data['base_url']         = $this->base_url;

    $data['summary'] = $this->ReportsModel3->load_bill_sundry_summary($crs_master_id);

    return view($this->folder_path.'reports/bill_sundry_summary',$data);
  }
 

   

  
  function stock_ledger(){
       
    if($this->request->getMethod() == 'post'){  

      $item_id        = $this->request->getVar('item_id'); 
      $unit_id        = $this->request->getVar('unit_id');
      $itm_criteria_type = $this->request->getVar('itm_criteria_type');  
        
      $criteria_type  = $this->request->getVar('criteria_type');
       
      if($criteria_type=='all_mc_cr'){
        $mc_id = 0;  
        $mc_grp_id = 0;
      }
      else if($criteria_type=='one_mc_cr'){
        $mc_id = $this->request->getVar('mat_cent_id'); 
        $mc_grp_id = 0;
      }            
      else if($criteria_type=='mc_group_cr'){
        $mc_grp_id = $this->request->getVar('mat_cent_grpid');
        $mc_id = 0;
      }else{
        $mc_id = 0;  
        $mc_grp_id = 0;
      }
       
      $module  = $this->request->getVar('module');
      $from_date = $this->request->getVar('from_date');
      $to_date = $this->request->getVar('to_date');

      $from_date = grp_validate_fy_from_date($from_date);
      $to_date = grp_validate_fy_to_date($to_date);

      if($module=='Item Account'){
              
        return redirect()->to($this->base_url.'reports/item_ledger_detail?item_id='.$item_id.'&from_date='.$from_date.'&to_date='.$to_date.'&unit_id='.$unit_id.'&mc_grp_id='.$mc_grp_id.'&mc_id='.$mc_id); 
      
      }
      
      if($module=='Item Tracking' && $itm_criteria_type=='all_tracking'){
        return redirect()->to($this->base_url.'reports/item_tracking/'.$item_id.'?type=all_item&from_date='.$from_date.'&to_date='.$to_date.'&untid='.$unit_id.'&mc_grp_id='.$mc_grp_id.'&mc_id='.$mc_id); 
           
      }
         
      elseif($module=='Item Tracking' && $itm_criteria_type=='one_tracking'){
          return redirect()->to($this->base_url.'reports/item_tracking/'.$item_id.'?type=item_wise&from_date='.$from_date.'&to_date='.$to_date.'&untid='.$unit_id.'&mc_grp_id='.$mc_grp_id.'&mc_id='.$mc_id); 
           
       }
      
      if($module=='Item Account' && $item_id != ''){
              
        return redirect()->to($this->base_url.'items/ledger_detail?item_id='.$item_id.'&from_date='.$from_date.'&to_date='.$to_date.'&unit_id='.$unit_id.'&mc_grp_id='.$mc_grp_id.'&mc_id='.$mc_id); 
      
      }
      if($module=='MC Account' && $item_id != ''){
         return redirect()->to($this->base_url.'material_centres/ledger_detail/'.$item_id.'/'.$from_date.'/'.$to_date.'/'.$unit_id.'/'.$mc_grp_id.'/'.$mc_id); 
      }
            
      return redirect()->to($_SERVER['HTTP_REFERER']);  
    }

    $dropdown = [
      'Stock Ledger' => [
        'Item Account','Item Group','Stock Category','Item Tracking'
      ],
      'MC Ledger' => [
        'MC Account','MC Group'
      ]
    ];

    $data['message_output']   = $this->message_output;
    $data['folder_path']      = $this->folder_path;
    $data['base_url']         = $this->base_url;
    $data['dropdown']         = $dropdown;

    $data['matrcntr_dropdown'] = $this->ReportsModel3->get_master_list('mcmasternn');
    $data['matrcntr_grp_dropdown'] = $this->ReportsModel3->get_master_list('mcgrpmstnn');
    $data['items_dropdown'] = $this->ReportsModel3->get_master_list('itemmaster');
    $data['units_dropdown'] = $this->ReportsModel3->get_master_list('itmunitmst');

    return view($this->folder_path.'reports/stock_ledger',$data);  
  } 

  function item_ledger_detail(){
      
    $crs_master_id  = $_GET['item_id'] ?? 0;    

    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    $from_date = grp_validate_fy_from_date($from_date);
    $to_date = grp_validate_fy_to_date($to_date);

    $unit_id = !empty($_GET['unit_id']) ? $_GET['unit_id'] : 0;
    $mc_id = !empty($_GET['mc_id']) ? $_GET['mc_id'] : 0;
    $mc_grp_id = !empty($_GET['mc_grp_id']) ? $_GET['mc_grp_id'] : 0;
    $val = !empty($_GET['val']) ? $_GET['val'] : 0;

    if(isset($_GET['mc_type'])){
      if($_GET['mc_type'] == 'all_mc'){
        $mc_id = 0;
        $mc_grp_id = 0;
      }
      if($_GET['mc_type'] == 'one_mc'){
        $mc_grp_id = 0;
      }
      if($_GET['mc_type'] == 'mc_group'){
        $mc_id = 0;
      }
    }

    $item_name  = $this->ReportsModel3->get_master_name('itemmaster',$crs_master_id);
 

    $data['item_name'] = $item_name;
    $data['val_id'] = $val;
    $data['val'] = $val;

    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd = date('Y-m-d', strtotime($to_date));

    $opening_balance_list = $this->ReportsModel3->get_itm_opn_bal_list($crs_master_id, $unit_id, $mc_id, $mc_grp_id,$data['val'],$from_date_ymd);
    

    $closing_balance_list = $this->ReportsModel3->get_itm_clo_bal_list($crs_master_id, $unit_id, $mc_id, $mc_grp_id,$data['val'],$to_date_ymd);
    

    $amount_list = $this->ReportsModel3->item_total_amount_list($crs_master_id, $unit_id, $mc_id, $mc_grp_id,$from_date_ymd,$to_date_ymd);
    

    $data['item_unit_list'] = $this->ReportsModel3->get_master_list('itmunitmst');

    

    $data['transactions'] = $this->ReportsModel3->load_item_ledger($crs_master_id, $unit_id, $mc_id, $mc_grp_id, $data['val'], $from_date_ymd, $to_date_ymd);
    
    
    $data['item_id'] = $crs_master_id;
    $data['from_date'] = $from_date;
    $data['to_date'] = $to_date;
    $data['unit_id'] = $unit_id;
    $data['mc_grp_id'] = $mc_grp_id;
    $data['mc_id'] = $mc_id;

    $data['opening_balance_list'] = $opening_balance_list;
    $data['closing_balance_list'] = $closing_balance_list;
    $data['amount_list'] = $amount_list;

    $data['base_url'] = $this->base_url;
    $data['folder_path'] = $this->folder_path;
    $data['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO'];

    $data['matrcntr_dropdown'] = $this->ReportsModel3->get_master_list('mcmasternn');
    $data['matrcntr_grp_dropdown'] = $this->ReportsModel3->get_master_list('mcgrpmstnn');
    $data['items_dropdown'] = $this->ReportsModel3->get_master_list('itemmaster');
    $data['units_dropdown'] = $this->ReportsModel3->get_master_list('itmunitmst');
  
    return view($this->folder_path.'reports/item_ledger',$data);   
  }

  function stock_summary()
  {

    if($this->request->getMethod() == 'post'){   

      $summary_type = $this->request->getVar('summary_type');
      $summary_sub_type =$this->request->getVar('stck_summary_type');
      $item_id = $this->request->getVar('item_id');
      $unit_id = $this->request->getVar('unit_id');

      $criteria_type = $this->request->getVar('criteria_type');

      if($criteria_type == 'all_mc_cr') {   
        $mc_id = 0;
        $mc_grp_id = 0;
      }
      else if($criteria_type == 'one_mc_cr'){     
        $mc_id = $this->request->getVar('mat_cent_id');
        $mc_grp_id = 0;
      }
      else if($criteria_type == 'mc_group_cr')  {   
        $mc_id = 0;
        $mc_grp_id = $this->request->getVar('mat_cent_grpid');
      }
      else{
        $mc_id = 0;
        $mc_grp_id = 0;
      }

      if($summary_type == 'stock_summary'){

        if($summary_sub_type == 'stock_item')
          return redirect()->to($this->base_url.'reports/item_summary/'.$item_id.'?mc_id='.$mc_id.'&unit_id='.$unit_id.'&mc_grp_id='.$mc_grp_id); 
      }
    
    }

    $data['base_url']           = $this->base_url;
    $data['message_output']     = $this->message_output;
    $data['folder_path']        = $this->folder_path;
    $data['matrcntr_dropdown'] = $this->ReportsModel3->get_master_list('mcmasternn');
    $data['matrcntr_grp_dropdown'] = $this->ReportsModel3->get_master_list('mcgrpmstnn');
    $data['items_dropdown'] = $this->ReportsModel3->get_master_list('itemmaster');
    $data['units_dropdown'] = $this->ReportsModel3->get_master_list('itmunitmst');

    return view($this->folder_path.'reports/stock_summary',$data);  
  }

  function stock_status(){
      
    $data['val'] = $_GET['val'] ?? 0;
    $data['mc_id'] = $_GET['mc_id'] ?? 0;
   
    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    $data['from_date'] = grp_validate_fy_from_date($from_date);
    $data['to_date'] = grp_validate_fy_to_date($to_date);
         
    $data['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO']; 
    $data['mc_list'] = $this->ReportsModel3->get_master_list('mcmasternn');

    $to_date_ymd = date('Y-m-d', strtotime($data['to_date']));
    $data['data'] = $this->ReportsModel3->load_stock_status($data['mc_id'],$data['val'],$to_date_ymd);

    $data['message_output']        = $this->message_output;
    $data['folder_path']           = $this->folder_path;
    $data['base_url']              = $this->base_url;

    return view($this->folder_path.'reports/stock_status',$data); 
  }

  function item_summary($crs_master_id)
  {
    $unit_id = (!empty($_GET['unit_id'])) ? $_GET['unit_id'] : 0;
    $mc_id = (!empty($_GET['mc_id'])) ? $_GET['mc_id'] : 0;
    $mc_grp_id=(!empty($_GET['mc_grp_id'])) ? $_GET['mc_grp_id'] : 0;
    $val = (!empty($_GET['val'])) ? $_GET['val'] : 0;
    
    if($mc_id != 0 && $mc_grp_id != 0){
      $mc_id = 0;
      $mc_grp_id = 0;
    }

    $item_name = $this->ReportsModel3->get_master_name('itemmaster',$crs_master_id);

    $item_qty = 0;
    $item_value = 0;

    $itm_opn_bal = $this->ReportsModel3->get_itm_opn_balance($crs_master_id,$unit_id,$mc_id,$mc_grp_id,$val);
    if($itm_opn_bal){
      $item_qty = $itm_opn_bal['item_qty'];
      $item_value = $itm_opn_bal['item_value'];
    }

    $summary = $this->ReportsModel3->load_item_summary($crs_master_id,$unit_id,$mc_id,$mc_grp_id,$val);

    $data['item_id']          = $crs_master_id;
    $data['item_name']        = $item_name;
    $data['unit_id']          = $unit_id;
    $data['mc_id']            = $mc_id;
    $data['mc_grp_id']        = $mc_grp_id;
    $data['val']              = $val;
    $data['summary']          = $summary;
    $data['item_qty']         = $item_qty;
    $data['item_value']       = formatAmount($item_value);

    $data['message_output']   = $this->message_output;
    $data['folder_path']      = $this->folder_path;
    $data['base_url']         = $this->base_url;

    $data['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO'];
    $data['matrcntr_dropdown'] = $this->ReportsModel3->get_master_list('mcmasternn');
    $data['matrcntr_grp_dropdown'] = $this->ReportsModel3->get_master_list('mcgrpmstnn');
    $data['units_dropdown'] = $this->ReportsModel3->get_master_list('itmunitmst');

    return view($this->folder_path.'reports/item_summary',$data);
  }

  
   
  
  
      
   
      



      
      public function ajax_day_book()
      {
            $pq_curPage = (int)$_POST["pq_curpage"];
            $limit     = (int)$_POST["pq_rpp"];

            $search = '';
            $pq_filter    = $this->request->getVar('pq_filter');
            if(!empty($pq_filter)){
                $pq_filter = json_decode($pq_filter);
                $search = $pq_filter->data[0]->value;
            }
               
            
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
		    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
		    $view     = $this->request->getVar('view');
		    
            echo $this->ReportsModel->load_day_book_condensed($pq_curPage, $limit, $from_date, $to_date, $view,$search);
      }
      public function day()
      {
            $pq_curPage = 1;
            $limit     = 10;
           

            $from_date     = date('Y-m-d',strtotime('01-11-2023'));
            $to_date       = date('Y-m-d',strtotime('30-11-2023'));
            $view     = $this->request->getVar('view');

            echo $this->ReportsModel->load_day_book_condensed($pq_curPage, $limit, $offset, $from_date, $to_date);
      }

       public function day_book(){
           
        $array = [];
        $from_date = '';
        $to_date = '';
        $view = isset($_GET['view']) ? $_GET['view'] : 0;
        
		$bo_id = isset($_GET['bo_id']) ? $_GET['bo_id'] :'';
		
		
		  $this->session->set('ses_boid',$bo_id); 
			 
       if(isset($_GET['fromdate']) && isset($_GET['todate'])){
           $from_date     = date('Y-m-d',strtotime($_GET['fromdate']));
		   $to_date       = date('Y-m-d',strtotime($_GET['todate']));
	   
           $from_date = date("d-m-Y", strtotime($from_date));
           $to_date = date("d-m-Y", strtotime($to_date));
           
           
           
       }
       else{
            $from_date = date("01-m-Y");
           $to_date = date("d-m-Y");
           
       }
       
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;
		$data['view']              = $view;
        $data['array']    = $array;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
		$data['ses_boid']              = $this->session->get('ses_boid');		
		$data['bo_dropdown']           = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'reports/day_book',$data); 
      }
      
      public function ajax_cash_bank_book()
      {
            $pq_curPage = (int)$_POST["pq_curpage"];
            $limit     = (int)$_POST["pq_rpp"];
            
            
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
		    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
            
            
            echo $this->ReportsModel->load_cash_bank_book($pq_curPage, $limit, $from_date, $to_date);
      }
      
      public function cash_bank_book(){
       
        $array = [];
        $from_date = date("01-m-Y");
        $to_date = date("d-m-Y");
        
       if(isset($_GET['fromdate']) && isset($_GET['todate'])){
           $from_date     = date('Y-m-d',strtotime($_GET['fromdate']));
		   $to_date       = date('Y-m-d',strtotime($_GET['todate']));
           
           $from_date = date("d-m-Y", strtotime($from_date));
           $to_date = date("d-m-Y", strtotime($to_date));
       }
       
       
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;
        $data['array']                  = $array;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        
        return view($this->folder_path.'reports/cash_bank_book',$data); 
      }

    public function outstanding(){
       
        if($this->request->getMethod() == 'post'){   
            
            $type   = $this->request->getVar('type');
            
            if($type=='Sales Order'){
                $account_id    = $this->request->getVar('id');
                $from_date     = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
                $to_date       = date('Y-m-d',strtotime($this->request->getVar('todate')));    
                return redirect()->to($this->base_url.'reports/sales_order?from_date='.$from_date.'&to_date='.$to_date);  
            }
            if($type=='Purchase Order'){
                $group_id    = $this->request->getVar('id');
                $from_date     = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
                $to_date       = date('Y-m-d',strtotime($this->request->getVar('todate')));
                return redirect()->to($this->base_url.'reports/purchase_order?from_date='.$from_date.'&to_date='.$to_date);  
            } 
            
          }
       
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;

        return view($this->folder_path.'reports/outstanding',$data);  
    }

    public function ajax_sales_order()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;

        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
        $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';


        echo '';//$this->ReportsModel->load_cash_bank_book($pq_curPage, $limit, $offset, $from_date, $to_date);
    }
      
    public function sales_order(){
       
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
            $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
      
      else if(isset($_GET['fromdate']) && isset($_GET['todate'])){
            $from_date     = $_GET['fromdate'] != '' ? date('d-m-Y',strtotime($_GET['fromdate'])) : '';
            $to_date       = $_GET['todate'] != '' ? date('d-m-Y',strtotime($_GET['todate'])) : '';
        }
       
       
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['array']                  = [];
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        
        return view($this->folder_path.'reports/sales_order',$data); 
    }

    public function ajax_purchase_order()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;

        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
        $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';


        echo '';//$this->ReportsModel->load_cash_bank_book($pq_curPage, $limit, $offset, $from_date, $to_date);
    }
      
    public function purchase_order(){
       
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
            $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
       
       
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['array']                  = [];
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        
        return view($this->folder_path.'reports/purchase_order',$data); 
    }
      
    public function bills_management()
    {
        $from_date = '';
        $to_date = '';
        
        if($this->request->getMethod() == 'post'){
            
            $bill_type      = $_POST['bill_type'];
            
            $from_date      = date('d-m-Y',strtotime($_POST['fromdate']));
            $to_date        = date('d-m-Y',strtotime($_POST['todate']));
			$bo_id          = $_POST['bo_id'];
            $this->session->set('ses_boid',$bo_id);	

            $criteria       = $_POST['criteria'];
            if($criteria == 'Bill Summary'){
                return redirect()->to($this->base_url.'reports/bills_management_accounts/'.$bill_type.'?from_date='.$from_date.'&to_date='.$to_date);
            }
            if($criteria == 'Account Group'){
                $id = $_POST['id'];
                return redirect()->to($this->base_url.'reports/bills_management_groups/'.$id.'/'.$bill_type.'?from_date='.$from_date.'&to_date='.$to_date);
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

        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['sundry_accounts']        = $this->ReportsModel->get_sundry_accounts();
        $data['sundry_groups']          = $this->ReportsModel->get_sundry_groups();
        $data['selboid']     = $this->session->get('ses_boid');
        $data['bo_dropdown'] = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'reports/bills_management',$data);  
    }
    
    function ajax_bills_management_accounts()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
       
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	    
        $bill_type     = $_POST['bill_type'];
        
        echo $this->ReportsModel->load_bills_management_accounts($pq_curPage, $limit,  $from_date, $to_date, $bill_type);
    }
    
    function bills_management_accounts($type)
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['bill_type']              = $type;
        
        return view($this->folder_path.'reports/bills_management_accounts',$data);
    }
    
    function ajax_bills_management_groups()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
	    $group_id = $_POST['group_id'];
        $bill_type = $this->request->getVar('bill_type');
        
        echo $this->ReportsModel->load_bills_management_groups($pq_curPage, $limit, $offset, $from_date, $to_date, $group_id,$bill_type);
    }
    
    function bills_management_groups($id, $type)
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        
        $group_name = '';
        $group = $this->ReportsModel->get_group_details($id);
        if($group){
            $group_name = $group['acc_grp_name'];
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['group_id']               = $id;
        $data['group_name']             = $group_name;
        $data['bill_type']              = $type;
        
        return view($this->folder_path.'reports/bills_management_groups',$data);
    }
    
    function ajax_bills_management_one_account()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
	    $account_id = $this->request->getVar('account_id');
	    $bill_type = $this->request->getVar('bill_type');
        $nil_type = $this->request->getVar('nil_type');
	   
        
        echo $this->ReportsModel->load_bills_management_one_account($pq_curPage, $limit, $offset, $from_date, $to_date, $account_id,$bill_type,$nil_type);
    }
    
    function bills_management_one_account($id, $type)
    {
        $from_date = '';
        $to_date = '';
        $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        $account_name = '';
        $account = $this->ReportsModel->account_info($id);
        if($account){
            $group = $this->ReportsModel->get_group_details($account['acc_grp_id']);
            $account_name = $account['acc_name'] . ' ('. $group['acc_grp_name'] .')';
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['account_id']             = $id;
        $data['account_name']           = $account_name;
        $data['bill_type']              = $type;
        $data['nil_type']           = $nil_type;
        
        return view($this->folder_path.'reports/bills_management_one_account',$data);
    }
    
    function ajax_bills_management_details()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
	    $bills_ref_id = $this->request->getVar('bills_ref_id');
        
        echo $this->ReportsModel->load_bills_management_details($pq_curPage, $limit, $offset, $from_date, $to_date, $bills_ref_id);
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
        $bill = $this->ReportsModel->get_bill_details($bills_ref_id);
        if($bill){
            $bill_ref = $bill['bills_ref_name'];
            $acc_id = $bill['acc_id'];
            $account = $this->ReportsModel->account_info($bill['acc_id']);
            $account_name = $account['acc_name'] ?? '';
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
        
        return view($this->folder_path.'reports/bills_management_details',$data);
    }
    
    function ajax_bills_management_statement()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	    
	    $bill_type = $this->request->getVar('bill_type');
        
        echo $this->ReportsModel->load_bills_management_statement($pq_curPage, $limit, $offset, $from_date, $to_date, $bill_type); 
    }
    
    function bills_management_statement($bill_type) 
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['bill_type']              = $bill_type;
        
        return view($this->folder_path.'reports/bills_management_statement',$data);
    }
    
    public function cost_centre()
    {
        $from_date = '';
        $to_date = '';
        
        if($this->request->getMethod() == 'post'){
            
            $from_date     = !empty($_POST['from_date']) ? date('d-m-Y',strtotime($_POST['from_date'])) : '';
	        $to_date       = !empty($_POST['to_date']) ? date('d-m-Y',strtotime($_POST['to_date'])) : '';
            
			 $bo_id = $this->request->getVar('bo_id'); 
		     $this->session->set('ses_boid',$bo_id);  
            $report_type       = $_POST['report_type'];
            $sub_type          = $_POST['sub_type'];
   
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
                if($sub_type == 'GROUP'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_trial_group/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
                }
            }
            if($report_type == 'COST CENTRE LEDGER'){
                if($sub_type == 'COST CENTRE GROUP'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_group_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
                }
                if($sub_type == 'COST CENTRE'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_account_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
                }
            }
            if($report_type == 'COST CENTRE SUMMARY'){
                if($sub_type == 'COST CENTRE GROUP SUMMARY'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_group_summary/'.$id);
                }
                if($sub_type == 'COST CENTRE SUMMARY'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_account_summary/'.$id);
                }
            }
            
            return redirect()->to($this->base_url.'reports/cost_centre'); 
        }

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['cc_list']                = $this->ReportsModel->get_cc_list();
        $data['cc_groups']              = $this->ReportsModel->get_cc_groups();
        $data['ses_boid']              = $this->session->get('ses_boid');		
		$data['bo_dropdown']           = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'reports/cost_centre',$data);  
    }
    function ajax_cost_centre_account_wise()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
       
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
        
        echo $this->ReportsModel->load_cost_centre_account_wise($pq_curPage, $limit, $from_date, $to_date);
    }
    function cost_centre_account_wise()
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        
        return view($this->folder_path.'reports/cost_centre_account_wise',$data);
    }
    function ajax_cost_centre_wise()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
        
        echo $this->ReportsModel->load_cost_centre_wise($pq_curPage, $limit, $offset, $from_date, $to_date);
    }
    function cost_centre_wise()
    {
        $from_date = '';
        $to_date = '';
        
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
    
    function ajax_cost_centre_trial()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
         
        echo $this->ReportsModel->load_cost_centre_trial($pq_curPage, $limit, $from_date, $to_date);
    }
    function cost_centre_trial()
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']             = $from_date;
        $data['to_date']               = $to_date;
        
        return view($this->folder_path.'reports/cost_centre_trial',$data);
    }
    
    function ajax_cost_centre_trial_group()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
	    $group_id = $_POST['group_id'];
        
        echo $this->ReportsModel->load_cost_centre_trial_group($pq_curPage, $limit,  $from_date, $to_date, $group_id);
    }
    function cost_centre_trial_group($id)
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        $group_name = '';
        $cc_group = $this->ReportsModel->cc_group_info($id);
        if($cc_group){
            $group_name = $cc_group['cc_grp_name'];
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['group_id']               = $id;
        $data['group_name']             = $group_name;
        
        return view($this->folder_path.'reports/cost_centre_trial_group',$data);
    }
    
    function ajax_cost_centre_group_ledger()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
    
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
	    $group_id = $_POST['group_id'];
        
        echo $this->ReportsModel->load_cost_centre_group_ledger($pq_curPage, $limit, $from_date, $to_date, $group_id);
    }
    function cost_centre_group_ledger($id)
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        $group_name = '';
        $cc_group = $this->ReportsModel->cc_group_info($id);
        if($cc_group){
            $group_name = $cc_group['cc_grp_name'];
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['group_id']               = $id;
        $data['group_name']             = $group_name;
        
        return view($this->folder_path.'reports/cost_centre_group_ledger',$data);
    }
    
    function ajax_cost_centre_account_ledger()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
	    $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
	
	    $cc_id = $_POST['cc_id'];
        
        echo $this->ReportsModel->load_cost_centre_account_ledger($pq_curPage, $limit, $from_date, $to_date, $cc_id);
    }
    function cost_centre_account_ledger($id)
    {
        $from_date = '';
        $to_date = '';
        
        if(isset($_GET['from_date']) && isset($_GET['to_date'])){
            $from_date     = $_GET['from_date'] != '' ? date('d-m-Y',strtotime($_GET['from_date'])) : '';
	        $to_date       = $_GET['to_date'] != '' ? date('d-m-Y',strtotime($_GET['to_date'])) : '';
        }
        $cc_name = '';
        $cc_opening = '';
        $cc = $this->ReportsModel->cc_info($id);
        if($cc){
            $cc_name = $cc['cc_name'];
            if($cc['cc_op_drcr'] == 'dr')
                $cc_opening = number_format($cc['cc_op_bal'],2).' DR';
            if($cc['cc_op_drcr'] == 'cr')
                $cc_opening = number_format($cc['cc_op_bal'],2).' CR';
        }
        if($from_date != '')
        {
            $cc_txn = $this->ReportsModel->get_last_cc_txn($id, date("Y-m-d", strtotime($from_date)));
            if($cc_txn)
            {
                if($cc_txn['cc_txn_bal'] >= 0)
                    $cc_opening = number_format($cc_txn['cc_txn_bal'],2).' DR';
                else
                    $cc_opening = number_format(abs($cc_txn['cc_txn_bal']),2).' CR';
            }
        }
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['cc_id']                  = $id;
        $data['cc_name']                = $cc_name;
        $data['cc_opening']             = $cc_opening;
        
        return view($this->folder_path.'reports/cost_centre_account_ledger',$data);
    }
    
    function cost_centre_group_summary($cc_grp_id)
    {
        $summary = $this->ReportsModel->load_cost_centre_group_summary($cc_grp_id);
        
        $group_name = '';
        $cc_group = $this->ReportsModel->cc_group_info($cc_grp_id);
        if($cc_group){
            $group_name = $cc_group['cc_grp_name'];
        }
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['group_name']             = $group_name;
        $data['summary']                = json_encode($summary);
        
        return view($this->folder_path.'reports/cost_centre_group_summary',$data);
    }
    function cost_centre_account_summary($cc_id)
    {
        $cc_name = '';
        $cc_opening = '';
        $cc = $this->ReportsModel->cc_info($cc_id);
        if($cc){
            $cc_name = $cc['cc_name'];
            if($cc['cc_op_drcr'] == 'dr')
                $cc_opening = number_format($cc['cc_op_bal'],2).' DR';
            if($cc['cc_op_drcr'] == 'cr')
                $cc_opening = number_format($cc['cc_op_bal'],2).' CR';
        }
        
        $summary = $this->ReportsModel->load_cost_centre_account_summary($cc_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['cc_name']                = $cc_name;
        $data['cc_opening']             = $cc_opening;
        $data['summary']                = json_encode($summary);
        
        return view($this->folder_path.'reports/cost_centre_account_summary',$data);
    }
   
}