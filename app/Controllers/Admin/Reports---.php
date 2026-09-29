<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ReportingModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Reports extends BaseController{

  function __construct(){  
    helper(['form', 'url','text']);
    $this->ReportingModel    = new ReportingModel();
    $this->LogModel        = new ERPLogModel();
    $this->CommonModel     = new CommonModel();		
    $this->auth_session    = new auth_session();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
    $this->auth_session->role_restrict('CS');
    $this->base_url      = base_url().'/'.getenv('AdminPath');
    $this->folder_path   = getenv('AdminPath');
    $this->session    	 = \Config\Services::session();
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	$this->bo_id         = $this->session->get('ses_boid');
  }
   
    public function balance_sheet() 
    { 
     $view = isset($_GET['view']) ? $_GET['view'] : 1;
     $format = isset($_GET['format']) ? $_GET['format'] : 1;
     $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
     
     
     $from_date = $_GET['from_date'] ?? '';
     $to_date = $_GET['to_date'] ?? '';

      $from_date = validate_fy_from_date($from_date);
      $to_date = validate_fy_to_date($to_date);

      $from_date_ymd = date('Y-m-d', strtotime($from_date));
      $to_date_ymd = date('Y-m-d', strtotime($to_date)); 

      $balance_sheet = [];
     if($format == 1)
        $balance_sheet = $this->ReportsModel->load_balance_sheet($view,$from_date_ymd,$to_date_ymd,$nil_type);

      if($format == 2)
        $balance_sheet = $this->ReportsModel->load_balance_sheet2($view,$from_date_ymd,$to_date_ymd,$nil_type);

    //    echo "<pre>";print_r($balance_sheet);exit;

     $data     = [
            'base_url'      => $this->base_url,
            'data'          => $balance_sheet,
            'view'          => $view,
            'format'        => $format,
            'nil_type'      => $nil_type,
			'bo_id'         => $this->session->get('ses_boid')
        ];
     
     $data['from_date'] = $from_date;
     $data['to_date']   = $to_date;

     if($format == 1)
        return view($this->folder_path.'reports/balance_sheet',$data);
     if($format == 2)
        return view($this->folder_path.'reports/balance_sheet2',$data);
    }

    public function profit_loss()
    {
	//fastcgi_finish_request();
        $view = isset($_GET['view']) ? $_GET['view'] : 1;
        $format = isset($_GET['format']) ? $_GET['format'] : 1;
        $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
        
         $from_date = $_GET['from_date'] ?? '';
         $to_date = $_GET['to_date'] ?? '';

          $from_date = validate_fy_from_date($from_date);
          $to_date = validate_fy_to_date($to_date);

        $from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd = date('Y-m-d', strtotime($to_date));

        $profit_loss = [];
        if($format == 1)
             $profit_loss = $this->ReportsModel->load_profit_loss($view,$from_date_ymd,$to_date_ymd,$nil_type);
        if($format == 2)
             $profit_loss = $this->ReportsModel->load_profit_loss2($view,$from_date_ymd,$to_date_ymd,$nil_type);

        $data = [
            'base_url'      => $this->base_url,
            'data'          => $profit_loss,
            'view'          => $view,
            'format'        => $format,
            'nil_type'      => $nil_type
        ]; 
        
        $data['from_date']   = $from_date;
        $data['to_date']     = $to_date;
		$data['bo_id']       = $this->session->get('ses_boid');
        
        if($format == 1)
            return view($this->folder_path.'reports/profit_loss',$data);
         if($format == 2)
            return view($this->folder_path.'reports/profit_loss2',$data);
    }

    public function trial_balance()
    {  	
    	$nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
        $view = isset($_GET['view']) ? $_GET['view'] : 0;

        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $from_date = validate_fy_from_date($from_date);
        $to_date = validate_fy_to_date($to_date);

        $from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd = date('Y-m-d', strtotime($to_date));


        if($view == 0)
            $data['trial_balance_list'] = $this->ReportsModel->load_trial_balance($from_date_ymd,$to_date_ymd);
        else if($view == 1)
            $data['trial_balance_list'] = $this->ReportsModel->load_trial_balance1($from_date_ymd,$to_date_ymd);
        else
            $data['trial_balance_list'] = $this->ReportsModel->load_trial_balance2();

        // echo "<pre>";print_r($data['trial_balance_list']);exit;
     
        $data['from_date']   = $from_date;
        $data['to_date']     = $to_date; 
		$data['nil_type']    = $nil_type;
        $data['view']        = $view;
		$data['bo_id']       = $this->session->get('ses_boid');
		$data['base_url']    = $this->base_url; 
		
        return view($this->folder_path.'reports/trial_balance',$data);
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

    public function remove_daybook_vouchers(){  //permanant delete
         if($this->request->getMethod() == 'post'){ 
             $errors = [];
             $voucher_id_array = $this->request->getVar('vchrids');
			 $count = 0;
             $voucher_id_array = array_unique($voucher_id_array);
			 if(is_array($voucher_id_array)){
                 foreach($voucher_id_array as $voucher_txn_id){
					if(is_numeric($voucher_txn_id) && $voucher_txn_id>0){
                     $response = $this->TransactionModel->delete_transaction_voucher($voucher_txn_id);
                     if($response['status']){
                        $count++;
                      }
                      else{
                        $errors[] = $response['message'];
				       }
					}
                 }   
             }
            return json_encode(['status' => true, 'message' => $count.' Vouchers deleted', 'errors' => $errors]);
         }
        
    }
	
	public function item_tracking_ledger($item_id){
     $from_date               = $_GET['from_date'] ?? '';
     $to_date                 = $_GET['to_date'] ?? '';
     $from_date               = validate_from_date($from_date);
     $to_date                 = validate_to_date($to_date);
	 $item_info               = $this->ReportsModel->get_item_info($item_id,$this->company_id);
     $data['message_output']  = $this->message_output;
	 $data['folder_path']     = $this->folder_path;
	 $data['item_info']       = $item_info;
	 $data['base_url']        = $this->base_url;
	 $data['item_id']         = $item_id;
	 $data['from_date']       = $from_date;	
	 $data['to_date']         = $to_date;	     	 
	 return view($this->folder_path.'reports/item_tracking_ledger',$data);			
	}
		
	public function ajax_item_tracking(){
		$item_id     =  (int)$_POST["item_id"];
		$from_date   =  date('Y-m-d',strtotime($_POST['from_date']));
		$to_date     =  date('Y-m-d',strtotime($_POST['to_date']));
        echo $response   =  $this->ReportsModel->get_item_tracking_report($item_id,$from_date,$to_date);
	}
	public function ajax_bom_profitability(){
		$item_id     =  (int)$_POST["item_id"];
		$from_date   =  date('Y-m-d',strtotime($_POST['from_date']));
		$to_date     =  date('Y-m-d',strtotime($_POST['to_date']));
        echo $response   =  $this->ReportsModel->get_bom_profitability_report($to_date,$item_id);
		
	}
	
	public function ajax_item_tracking_ledger(){
		$item_id         =  (int)$_POST["item_id"];
		$from_date       =  date('Y-m-d',strtotime($_POST['from_date']));
		$to_date         =  date('Y-m-d',strtotime($_POST['to_date']));
        echo $response   =  $this->ReportsModel->get_item_tracking_ledger_report($item_id,$from_date,$to_date);
	}
	
    public function item_tracking($item_id=''){
	  if(isset($_GET['type']))	
		 $type = $_GET['type'];
	   else
		 $type = "item_wise";

	 $from_date               = $_GET['from_date'] ?? '';
     $to_date                 = $_GET['to_date'] ?? '';
     $from_date               = validate_from_date($from_date);
     $to_date                 = validate_to_date($to_date);				
	 $data['message_output']  = $this->message_output;
	 $data['folder_path']     = $this->folder_path;
	 $data['base_url']        = $this->base_url;
	 $data['item_id']         = $item_id;
	 $data['from_date']       = $from_date;	
	 $data['to_date']         = $to_date;
	 $data['type']            = $type;	 
	 if($type=='item_wise')
		 return view($this->folder_path.'reports/item_tracking',$data);	
	 else if($type=='all_item')
		 return view($this->folder_path.'reports/all_items_tracking',$data);		 
	}
	 public function bom_profitability($item_id=''){
	  if(isset($_GET['type']))	
		 $type = $_GET['type'];
	   else
		 $type = "bom_wise";

	 $from_date               = $_GET['from_date'] ?? '';
     $to_date                 = $_GET['to_date'] ?? '';
     $from_date               = validate_from_date($from_date);
     $to_date                 = validate_to_date($to_date);				
	 $data['message_output']  = $this->message_output;
	 $data['folder_path']     = $this->folder_path;
	 $data['base_url']        = $this->base_url;
	 $data['item_id']         = $item_id;
	 $data['from_date']       = $from_date;	
	 $data['to_date']         = $to_date;
	 $data['type']            = $type;	 	 
	 return view($this->folder_path.'reports/bom_profitability',$data);		 
	}
	
	public function other_stock_report(){       
       if($this->request->getMethod() == 'post'){	        
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
            
            if($type == 'Batch Report')
            {
                if($detail_type=='All Batches'){
                   // $account_id    = $this->request->getVar('id');
                   // return redirect()->to($this->base_url.'accounts/ledger_detail/'.$account_id.'?from_date='.$from_date.'&to_date='.$to_date); 
                }
                else if($detail_type=='Batch Wise'){
                   $group_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'accounts/accounts_trial/'.$group_id);  
                }
            }
            if($type == 'Item Tracking Report')
            {
                if($detail_type=='Item Wise'){
                    $id    = $this->request->getVar('id');    
                    return redirect()->to($this->base_url.'reports/item_tracking/'.$id.'?type=item_wise&from_date='.$from_date.'&to_date='.$to_date);  
                }
				else if($detail_type=='All Tracked Items'){
                    return redirect()->to($this->base_url.'reports/item_tracking/?type=all_item&from_date='.$from_date.'&to_date='.$to_date);  
                }				
            }
			if($type == 'BOM Profitability Report')
            {
                if($detail_type=='BOM Wise'){
                    $id    = $this->request->getVar('id');    
                    return redirect()->to($this->base_url.'reports/bom_profitability/'.$id.'?type=bom_wise&from_date='.$from_date.'&to_date='.$to_date);  
                }
				else if($detail_type=='All BOM'){
                    return redirect()->to($this->base_url.'reports/bom_profitability/?type=all_bom&from_date='.$from_date.'&to_date='.$to_date);  
                }				
            }
          }       
        $dropdown = [
                'Batch Report'     => ['All Batches', 'Batch Wise'],
                'Item Tracking Report' => ['All Tracked Items','Stock Category Wise','Item Group Wise','Item Wise','Untracked Item'],                
                'BOM Profitability Report'     => ['All BOM', 'BOM Wise'],
				];        
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['accounts_list']         = $this->ReportsModel->get_all_accounts();
		$data['batch_list']            = $this->ReportsModel->get_all_batches();
		$data['bom_list']              = $this->ReportsModel->get_all_bom();
		$data['items_list']            = $this->ReportsModel->get_all_items();
        $data['bill_sundry_list']      = $this->ReportsModel->get_all_bill_sundry_accounts();
        $data['item_groups_list']      = $this->ReportsModel->get_all_item_groups_list();
        $data['stock_category_list']   = $this->ReportsModel->get_all_stock_category();
        $data['base_url']              = $this->base_url;
        $data['dropdown']              = $dropdown;
        return view($this->folder_path.'reports/other_stock_report',$data);  
    }
	
	
    public function account_ledger(){
       
       if($this->request->getMethod() == 'post'){	
      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date       = date('Y-m-d',strtotime($this->request->getVar('todate')));

            
            if($type == 'Account Ledger')
            {
                if($detail_type=='Account'){
                    $account_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'accounts/ledger_detail/'.$account_id.'?from_date='.$from_date.'&to_date='.$to_date); 
                }
                else if($detail_type=='Account Group'){
                    $group_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'accounts/group_ledger_detail?group_id='.$group_id.'&from_date='.$from_date.'&to_date='.$to_date);  
                } 
                else if($detail_type=='Account/Group List'){
                   $group_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'accounts/accounts_trial/'.$group_id.'&from_date='.$from_date.'&to_date='.$to_date);  
                }
                else if($detail_type=='Account/Group Parent'){
                   $id    = $this->request->getVar('id');
                   return redirect()->to($this->base_url.'accounts/parent_ledger_detail?parent_id='.$id.'&from_date='.$from_date.'&to_date='.$to_date);  
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
		$data['accounts_list']         = $this->ReportsModel->get_all_accounts();
		$data['groups_list']           = $this->ReportsModel->get_all_groups();
        $data['group_parent_list']     = $this->ReportsModel->get_all_group_parents();
        $data['bill_sundry_list']      = $this->ReportsModel->get_all_bill_sundry_accounts();
        $data['cc_list']               = $this->ReportsModel->get_cc_list();
        $data['cc_groups']             = $this->ReportsModel->get_cc_groups();
        $data['base_url']              = $this->base_url;
        $data['dropdown']              = $dropdown;
        return view($this->folder_path.'reports/account_ledger',$data);  
    }

    public function ajax_bill_sundry_ledger()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        $offset = ($pq_curPage > 1) ? ($limit * ($pq_curPage - 1)) : 0;
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
        $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
    
        $bill_sundry_id = $_POST['bill_sundry_id'];
        
        echo $this->ReportsModel->load_bill_sundry_ledger($pq_curPage, $limit, $offset, $from_date, $to_date, $bill_sundry_id);
    }
    public function bill_sundry_ledger($id)
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $bill_sundry_name = '';
        $bill_sundry_opening = '';
        $bill_sundry = $this->ReportsModel->bill_sundry_info($id);
        if($bill_sundry){
            $bill_sundry_name = $bill_sundry['bill_sundry_name'];

            $bill_sundry_opening = '0.00 DR';
            $bill_sundry_op_balance = $this->ReportsModel->bill_sundry_op_balance($id);
            
            if(!empty($bill_sundry_op_balance['bsd_op_bal']))
            {
                if($bill_sundry_op_balance['bsd_op_bal'] > 0)
                    $bill_sundry_opening = formatAmount($bill_sundry_op_balance['bsd_op_bal']).' DR';
                if($bill_sundry_op_balance['bsd_op_bal'] < 0)
                    $bill_sundry_opening = formatAmount(abs($bill_sundry_op_balance['bsd_op_bal'])).' CR';
            }
            
        }
        if($from_date != '')
        {
            $bill_sundry_txn = $this->ReportsModel->get_last_bill_sundry_txn($id, date("Y-m-d", strtotime($from_date)));
            if(!empty($bill_sundry_txn['sundry_bal']))
            {
                if($bill_sundry_txn['sundry_bal'] >= 0)
                    $bill_sundry_opening = formatAmount($bill_sundry_txn['sundry_bal']).' DR';
                if($bill_sundry_txn['sundry_bal'] < 0)
                    $bill_sundry_opening = formatAmount(abs($bill_sundry_txn['sundry_bal'])).' CR';
            }
        }
        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['bill_sundry_id']         = $id;
        $data['bill_sundry_name']       = $bill_sundry_name;
        $data['bill_sundry_opening']    = $bill_sundry_opening;
        
        return view($this->folder_path.'reports/bill_sundry_ledger',$data);
    }

    public function account_summary(){
       
         if($this->request->getMethod() == 'post'){	
            $summary_type    = $this->request->getVar('summary_type');
            $summary_detail    = $this->request->getVar('summary_detail');
		
			 
            if($summary_type == 'Account Summary')
            {
                if($summary_detail == 'Ledger'){
                    $id    = $this->request->getVar('id');    
                    return redirect()->to($this->base_url.'accounts/monthly_detail/'.$id);
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

        $data['accounts_list'] = $this->ReportsModel->get_all_accounts();
        $data['groups_list']   = $this->ReportsModel->get_all_groups();
        $data['bill_sundry_list'] = $this->ReportsModel->get_all_bill_sundry_accounts();
        $data['cc_list']       = $this->ReportsModel->get_cc_list();
        $data['cc_groups']     = $this->ReportsModel->get_cc_groups();
		
        return view($this->folder_path.'reports/account_summary',$data);   
   }

   public function purchase_summary()
    {
        $view = $_GET['view'] ?? 1;
        $summary = $this->ReportsModel->load_purchase_summary($view);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;
        $data['view']                  = $view;
        
        return view($this->folder_path.'reports/summary_purchase',$data);
    }

    public function purchase_return_summary()
    {
        $view = $_GET['view'] ?? 1;
        $summary = $this->ReportsModel->load_purchase_return_summary($view);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;
        $data['view']                  = $view;
        
        return view($this->folder_path.'reports/summary_purchase_return',$data);
    }

    public function sales_summary()
    {
        $view = $_GET['view'] ?? 1;
        $summary = $this->ReportsModel->load_sales_summary($view);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;
        $data['view']                  = $view;

        return view($this->folder_path.'reports/summary_sales',$data);
    }

    public function sale_return_summary()
    {
        $view = $_GET['view'] ?? 1;
        $summary = $this->ReportsModel->load_sale_return_summary($view);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;
        $data['view']                  = $view;
        
        return view($this->folder_path.'reports/summary_sale_return',$data);
    }

    public function purchase_order_summary()
    {
        $voucher_type_id = 12;
        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;

        return view($this->folder_path.'reports/summary_purchase_order',$data);
    }

    public function sale_order_summary()
    {
        $voucher_type_id = 19;
        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;

        return view($this->folder_path.'reports/summary_sale_order',$data);
    }

    public function purchase_requisition_summary()
    {
        $voucher_type_id = 21;
        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;

        return view($this->folder_path.'reports/summary_purchase_requisition',$data);
    }

    public function quotation_summary()
    {
        $voucher_type_id = 17;
        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;

        return view($this->folder_path.'reports/summary_quotation',$data);
    }

    public function payments_summary()
    {
        $voucher_type_id = 9;
        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;

        return view($this->folder_path.'reports/summary_payments',$data);
    }
    public function receipts_summary()
    {
        $voucher_type_id = 13;
        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;

        return view($this->folder_path.'reports/summary_receipts',$data);
    }
    public function contra_summary()
    {
        $voucher_type_id = 1;
        $view = $_GET['view'] ?? 1;

        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id,$view);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;
        $data['view']                  = $view;

        return view($this->folder_path.'reports/summary_contra',$data);
    }
    public function journal_summary()
    {
        $voucher_type_id = 5;
        $summary = $this->ReportsModel->load_voucher_summary($voucher_type_id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['summary']               = $summary;

        return view($this->folder_path.'reports/summary_journal',$data);
    }

    

   function account_group_summary($acc_grp_id)
    {
        $group_info = $this->ReportsModel->main_group_info($acc_grp_id);
        $group_name = $group_info['acc_grp_name'] ?? '';
        
        $data['message_output'] = $this->message_output;
        $data['folder_path']    = $this->folder_path;
        $data['base_url']       = $this->base_url;
        $data['group_name']     = $group_name;
        $data['group_id']       = $acc_grp_id;
        $data['summary']        = $this->ReportsModel->load_account_group_summary($acc_grp_id);
        

        return view($this->folder_path.'reports/summary_account_group',$data);
    }
	
	function ajax_account_group_summary($acc_grp_id)
    {
       echo  json_encode(['data'=>$this->ReportsModel->load_account_group_summary($acc_grp_id)]);
	   die();
    }

    public function bill_sundry_summary($id)
    {
        $bill_sundry_name = '';
        $bill_sundry_opening = '';
        $bill_sundry = $this->ReportsModel->bill_sundry_info($id);
        if($bill_sundry){
            $bill_sundry_name = $bill_sundry['bill_sundry_name'];
            $bill_sundry_opening = '0.00 DR';
            $bill_sundry_op_balance = $this->ReportsModel->bill_sundry_op_balance($id);
            
            if(!empty($bill_sundry_op_balance['bsd_op_bal']))
            {
                if($bill_sundry_op_balance['bsd_op_bal'] > 0)
                    $bill_sundry_opening = formatAmount($bill_sundry_op_balance['bsd_op_bal']).' DR';
                if($bill_sundry_op_balance['bsd_op_bal'] < 0)
                    $bill_sundry_opening = formatAmount(abs($bill_sundry_op_balance['bsd_op_bal'])).' CR';
            }
        }
        
        $summary = $this->ReportsModel->load_bill_sundry_summary($id);
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['bill_sundry_name']      = $bill_sundry_name;
        $data['bill_sundry_opening']   = $bill_sundry_opening;
        $data['summary']               = json_encode($summary);
        
        return view($this->folder_path.'reports/bill_sundry_summary',$data);
    }


 
   public function item_ledger(){
       
       if($this->request->getMethod() == 'post'){	
            $item_id       = $this->request->getVar('item_id'); 
		    $from_date     = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
			$to_date       = date('Y-m-d',strtotime($this->request->getVar('todate')));    
            return redirect()->to($this->base_url.'items/ledger_detail/'.$item_id.'/'.$from_date.'/'.$to_date);    
       }
       
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['items_dropdown']        = $this->ReportsModel->items_dropdown();
        $data['base_url']              = $this->base_url;
        return view($this->folder_path.'reports/item_ledger',$data);  
   }
   
   public function get_items()
   {
       if($_GET['module']=='Item Account'){
            echo $this->ReportsModel->get_company_items();
            exit;
        }
       if($_GET['module']=='MC Account'){
           echo $this->ReportsModel->get_company_mc();
           exit;
        }
       echo json_encode([]);
   }
  
    public function stock_ledger(){
       
       if($this->request->getMethod() == 'post'){	

             $item_id        = $this->request->getVar('item_id'); 
			 $unit_id        = $this->request->getVar('unit_id');
			 $itm_criteria_type = $this->request->getVar('itm_criteria_type');	
			
			 
			 if(!$unit_id)
			  $unit_id       = $this->request->getVar('unitid');
			  
			 $criteria_type  = $this->request->getVar('criteria_type');
			 
			 if($criteria_type=='all_mc_cr')
			 {
			   $mcid=0;  
			   $mcgrpid=0;
			 }
            else if($criteria_type=='one_mc_cr'){
                $mcid    = $this->request->getVar('mat_cent_id'); 
                $mcgrpid = 0;
            }
            			 
            else if($criteria_type=='mc_group_cr'){
                $mcgrpid = $this->request->getVar('mat_cent_grpid');
                $mcid    =0;
            }else{
                $mcid=0;  
			   $mcgrpid=0;
                
            }
			 
			 if(! $unit_id)
				  $unit_id='0';
			 
             			 
            $module      = $this->request->getVar('module');
            $from_date   = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
			$to_date     = date('Y-m-d',strtotime($this->request->getVar('todate')));
			
			 if($module=='Item Tracking' && $itm_criteria_type=='all_tracking'){
			return redirect()->to($this->base_url.'reports/item_tracking/'.$item_id.'?type=all_item&from_date='.$from_date.'&to_date='.$to_date.'&untid='.$unit_id.'&mcgrpid='.$mcgrpid.'&mcid='.$mcid); 
        	 
			 }
				 
			 elseif($module=='Item Tracking' && $itm_criteria_type=='one_tracking'){
			return redirect()->to($this->base_url.'reports/item_tracking/'.$item_id.'?type=item_wise&from_date='.$from_date.'&to_date='.$to_date.'&untid='.$unit_id.'&mcgrpid='.$mcgrpid.'&mcid='.$mcid); 
        	 
			 }
			
            if($module=='Item Account' && $item_id != ''){
              
              return redirect()->to($this->base_url.'items/ledger_detail?item_id='.$item_id.'&from_date='.$from_date.'&to_date='.$to_date.'&unit_id='.$unit_id.'&mc_grp_id='.$mcgrpid.'&mc_id='.$mcid); 
        
              
              //   return redirect()->to($this->base_url.'items/ledger_detail/'.$item_id.'/'.$from_date.'/'.$to_date.'/'.$unit_id.'/'.$mcgrpid.'/'.$mcid); 
            }
            if($module=='MC Account' && $item_id != ''){
               return redirect()->to($this->base_url.'material_centres/ledger_detail/'.$item_id.'/'.$from_date.'/'.$to_date.'/'.$unit_id.'/'.$mcgrpid.'/'.$mcid); 
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
       
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		
        $data['base_url']              = $this->base_url;
        $data['dropdown']              = $dropdown;
		$data['matrcntr_dropdown']     = $this->ReportsModel->matrcntr_dropdown();
		$data['matrcntr_grp_dropdown']     = $this->ReportsModel->matrcntr_grp_dropdown();
        $data['items_dropdown']        = $this->ReportsModel->item_list();
		
        return view($this->folder_path.'reports/stock_ledger',$data);  
    }

    public function mc_ledger(){
       
       if($this->request->getMethod() == 'post'){	
            $mat_cent_id       = $this->request->getVar('mat_cent_id'); 
		    $from_date     = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
			$to_date       = date('Y-m-d',strtotime($this->request->getVar('todate')));    
            return redirect()->to($this->base_url.'material_centres/ledger_detail/'.$mat_cent_id.'/'.$from_date.'/'.$to_date);    
       }
       
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['matrcntr_dropdown']        = $this->ReportsModel->matrcntr_dropdown();
        $data['base_url']              = $this->base_url;
        return view($this->folder_path.'reports/mc_ledger',$data);  
   }

   public  function ajax_stock_status()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));

        $mc_id    = $this->request->getVar('mc_id');
        $val    = $this->request->getVar('val');

        $search = '';
        $pq_filter    = $this->request->getVar('pq_filter');
        if(!empty($pq_filter)){
            $pq_filter = json_decode($pq_filter);
            $search = $pq_filter->data[0]->value;
        }

        $response = $this->ReportsModel->load_stock_status_items($mc_id,$val, $limit, $to_date,$pq_curPage, $search);


        return json_encode([
            'totalRecords' => $response['total_records'],
            'data'  => $response['data'],
            'total_op_valuation'  => $response['total_op_valuation'],
            'total_valuation'  => $response['total_valuation'],
            'total_profit'  => $response['total_profit'],
            'curPage' => $pq_curPage
        ]);
        
    } 

  public  function ajax_stock_statuss()
	 {
		 $data['nill']  = $_POST['nill'] ?? 0;
		 $data['type']  = $_POST['type'] ?? 0;
		 $data['val']   = $_POST['val'] ?? 0;
         $data['mc_id'] = $_POST['mc_id'] ?? 0;
         $from_date     = $_POST['from_date'] ?? '';
         $to_date       = $_POST['to_date'] ?? '';
		 $to_date_ymd   = $_POST['to_date_ymd'] ?? '';

        $data['from_date'] = validate_from_date($from_date);
        $data['to_date']   = validate_to_date($to_date);
	    //$response          = $this->ReportsModel->load_stock_status_items2($data['mc_id'],$data['val'],$to_date_ymd,$data['nill'],$data['type']);	
		$response2          = $this->ReportsModel->load_stock_status_reporting($data['mc_id'],$data['val'],$to_date_ymd,$data['nill'],$data['type']);	
		
	
	 }
    public function stock_status(){
      
        $data['val'] = $_GET['val'] ?? 0;
        $data['mc_id'] = $_GET['mc_id'] ?? 0;
       
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $data['from_date'] = validate_from_date($from_date);
        $data['to_date'] = validate_to_date($to_date);
             
        $data['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO']; 
        $data['mc_list'] = $this->ReportsModel->get_mc_list();
 
        $to_date_ymd             = date('Y-m-d', strtotime($data['to_date']));
        //$data['valuation_info']      = $this->ReportsModel->calculate_valauation_modal($data);
		
		//$data['total_valuation']      = $this->ReportsModel->calculate_total_valauation($data);
		
		
		$data['to_date_ymd']     = $to_date_ymd;
        $data['message_output']  = $this->message_output;
        $data['folder_path']     = $this->folder_path;
        $data['base_url']        = $this->base_url;
        $data['ses_boid']        = $this->session->get('ses_boid');

        return view($this->folder_path.'reports/stock_status',$data); 
    }
 /*   public function update_item_valuation_method(){
     if($this->request->getMethod() == 'post' && $this->request->isAjax()){
         
          $item_id          = $this->request->getVar('item_id'); 
          $unit_id          = $this->request->getVar('item_unit'); 
          $default_method   = $this->request->getVar('default_method');
          $item_txn_id      = $this->request->getVar('item_txn_id');
          $vouher_txn_id    = $this->request->getVar('id');
          
     $this->ReportsModel->update_item_valuation_method($item_id,$unit_id,$default_method,$item_txn_id,$vouher_txn_id);
     return json_encode(['status' => true]);
     }
 }  */
 
 public function verify_itemledger_valuation(){
		$voucher_txn_id = $this->request->getVar('voucher_txn_id');
		$item_id        = $this->request->getVar('item_id');
		$method_id      = $this->request->getVar('method_id');	
        
		$from_date = $_POST['from_date'] ?? '';
        $to_date = $_POST['to_date'] ?? '';

        $from_date = date('Y-m-d',strtotime(validate_fy_from_date($from_date)));
        $to_date = date('Y-m-d',strtotime(validate_fy_to_date($to_date)));
		
		$status         = $this->ReportsModel->calculating_item_valuations($voucher_txn_id,$item_id,$method_id,$from_date,$to_date);
		 
		//$this->ReportsModel->save_valuations_log($voucher_txn_id,$item_id,$method_id);	
		return json_encode(['status' => true]);	
	    
	}
	
	
 public function verify_item_valuation(){
		$voucher_txn_id = $this->request->getVar('voucher_txn_id');
		$item_id        = $this->request->getVar('item_id');
		$method_id      = $this->request->getVar('method_id');	
        
		$from_date = $_POST['from_date'] ?? '';
        $to_date = $_POST['to_date'] ?? '';

        $from_date = date('Y-m-d',strtotime(validate_fy_from_date($from_date)));
        $to_date = date('Y-m-d',strtotime(validate_fy_to_date($to_date)));
		
		$status         = $this->ReportsModel->calculating_item_valuations($voucher_txn_id,$item_id,$method_id,$from_date,$to_date);
		if($status==1)
		return json_encode(['status' => true]);
	    else
		return json_encode(['status' => false]);	
	}
	
  public function get_voucher_items_details(){
	$jsonitems = $this->request->getVar('jsonitems');
	$method_id = $this->request->getVar('method_id');
	$list =  $this->ReportsModel->get_voucher_items_details($jsonitems,$method_id);
	if($list)
		return json_encode(['status' => true,'method_id'=>$method_id, 'list' => $list]);
	
    	return json_encode(['status' => false, 'message' => 'No Voucher Found']);  
	  
  }    
   public  function ajax_stock_status_category()
	 {
		echo $response =  json_encode(['curPage' => '1', 'data' => [['stock_category' => '', 'no_of_items' => '', 'item_value' => '']], 'totalRecords' => '1']);	
	 } 
    public function stock_status_category(){
       if(isset($_GET['fromdate']) && $_GET['fromdate']!=''){	
            $fromdate           = $_GET['fromdate'];    
            $stock_status_items = [];
         }
         else{
         $stock_status_items = [];
         $fromdate =date('d-m-Y');
             
         }
 
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;
		$data['fromdate']              = $fromdate;
		
        $data['stock_status_items']    = $stock_status_items;

        return view($this->folder_path.'reports/stock_status_category',$data); 
      }
      
    public  function ajax_stock_status_group()
	 {
		echo $response =  json_encode(['curPage' => '1', 'data' => [['group_name' => '', 'no_of_items' => '', 'item_value' => '']], 'totalRecords' => '1']);	
	 }
    public function stock_status_group(){
       if(isset($_GET['fromdate']) && $_GET['fromdate']!=''){	
            $fromdate           = $_GET['fromdate'];    
            $stock_status_items = [];
         }
         else{
         $stock_status_items = [];
         $fromdate =date('d-m-Y');
             
         }

			 
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;
		$data['fromdate']              = $fromdate;
		
        $data['stock_status_items']    = $stock_status_items;
		
        return view($this->folder_path.'reports/stock_status_group',$data); 
      }
    public  function ajax_stock_status_mc_old()
	 {
		 echo $response =  $this->ReportsModel->stock_status_mc_report();	
	 }

    public  function ajax_stock_status_mc()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));

        $val    = $this->request->getVar('val');

        $search = '';
        $pq_filter    = $this->request->getVar('pq_filter');
        if(!empty($pq_filter)){
            $pq_filter = json_decode($pq_filter);
            $search = $pq_filter->data[0]->value;
        }

        $response = $this->ReportsModel->load_stock_status_mc($val, $limit, $to_date,$pq_curPage, $search);

        return json_encode([
            'totalRecords' => $response['total_records'],
            'data'  => $response['data'],
            'curPage' => $pq_curPage
        ]);
        
    }

    public  function ajax_totalvaluation_mc()
    {
        $from_date = date('Y-m-d',strtotime($this->request->getVar('from_date')));
        $to_date   = date('Y-m-d',strtotime($this->request->getVar('to_date')));
        $val       = $this->request->getVar('val');
		$mc_id     = 0;//$this->request->getVar('mc_id');
		$method_id = $this->request->getVar('method_id');
        $response  = $this->ReportsModel->load_mcwise_totalvaluation($from_date,$to_date,$val,$mc_id,$method_id);
        $table_data = '';
		$table_data .= '<table class="table table-striped"><thead>
    <tr>
      <th scope="col">#</th>   
      <th scope="col">Item Available Value</th>
	  <th scope="col">Item Packed Value</th>
	  <th scope="col">Item Obsolete Value</th>
	  <th scope="col">Item In Transit Value</th>
    </tr>
  </thead>
  <tbody>
  <tr>
      <th scope="col"></th>   
      <th scope="col">'.formatAmount($response['available']).'</th>
	   <th scope="col">'.formatAmount($response['packed']).'</th>
	    <th scope="col">'.formatAmount($response['obsolete']).'</th>
		 <th scope="col">'.formatAmount($response['transit']).'</th>
    </tr>';
		
		$table_data .= '</tbody></table>';
		return json_encode([
            'status' => true,
            'data'  => $table_data
        ]); 
    }
	
    public  function ajax_stock_status_bw()
	 {
		 echo $response =  $this->ReportsModel->stock_status_bw_report();	
	 }
    public function stock_status_mc(){
        $data['val'] = $_GET['val'] ?? 0;
       
        $data['to_date'] = !empty($_GET['to_date']) ? date('d-m-Y',strtotime($_GET['to_date'])) : date('d-m-Y');
             
        $data['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO']; 
  
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['ses_boid']              = $this->session->get('ses_boid');		
		$data['bo_dropdown']           = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'reports/stock_status_mc',$data); 
      }
	  
	  public function stock_status_bw(){
       if(isset($_GET['fromdate']) && $_GET['fromdate']!=''){	
            $fromdate           = $_GET['fromdate'];    
            $stock_status_items = [];
         }
         else{
         $stock_status_items = [];
         $fromdate =date('d-m-Y');
             
         }
		 
			 
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;
		$data['fromdate']              = $fromdate;
		
        $data['stock_status_items']    = $stock_status_items;
        return view($this->folder_path.'reports/stock_status_bw',$data); 
      } 
	  
      
      public function ajax_items_summary(){
       
          echo $response =  $this->ReportsModel->item_summary_list();
      }
      
      public function item_summary(){

        $item_id = !empty($_GET['item_id']) ? $_GET['item_id'] : 0;
        $unit_id = (isset($_GET['unit_id']) && $_GET['unit_id'] != '') ? $_GET['unit_id'] : 0;
        $mc_id = (isset($_GET['mc_id']) && $_GET['mc_id'] != '') ? $_GET['mc_id'] : 0;
        $mc_grp_id = (isset($_GET['mc_grp_id']) && $_GET['mc_grp_id'] != '') ? $_GET['mc_grp_id'] : 0;
        $val = (isset($_GET['val']) && $_GET['val'] != '') ? $_GET['val'] : 0;
        $invtp_id = (isset($_GET['invtp_id']) && $_GET['invtp_id'] != '') ? $_GET['invtp_id'] : 1;
        
        if($mc_id != 0 && $mc_grp_id != 0){
          $mc_id = 0;
          $mc_grp_id = 0;
        }
       
        $get_item_info = $this->ReportsModel->get_item_info($item_id,$this->company_id);
        if(empty($get_item_info)){
          throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $item_name     = $get_item_info['item_name'];

        if($val == 0)
          $val_id = $get_item_info['valmethod_id'];
        else
          $val_id = $val;

		
        $balance = 0;
        $item_value = 0;
        $method = '';
        if($unit_id != 0 && $mc_id != 0 && $mc_grp_id == 0){
          $result = $this->ReportsModel->opening_balance($item_id, $unit_id, $mc_id ,$val_id);
          $balance = $result['balance'];
          $item_value = $result['item_value'];
          $method = $result['method'];
        }

        $clo_balance = 0;
        $clo_item_value = 0;
        $clo_method = '';
        if($unit_id != 0 && $mc_id != 0 && $mc_grp_id == 0){
          $result = $this->ReportsModel->opening_balance($item_id, $unit_id, $mc_id ,$val_id);
          $clo_balance = $result['balance'];
          $clo_item_value = $result['item_value'];
          $clo_method = $result['method'];
        }

        $opening_balance_list = [];
        $opening_balance_list = $this->ReportsModel->opening_balance_list($item_id, $unit_id, $mc_id, $mc_grp_id,$val_id);
       

        $closing_balance_list = [];
        $closing_balance_list = $this->ReportsModel->closing_balance_list($item_id, $unit_id, $mc_id, $mc_grp_id,$val_id);
       


        $mc_grp_name = '';
        if($mc_grp_id != 0){
          $get_mc_grp_info = $this->ReportsModel->get_mc_grp_info($mc_grp_id);
          $mc_grp_name = $get_mc_grp_info['mc_grp_name'] ?? '';
        }
		$from_date = '';
        $to_date = '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);
        $valdata = array("val"=>$val,"from_date"=>$from_date,"to_date"=>$to_date,"item_id"=>$item_id);
		
		//$data['valuation_info']      = $this->ReportsModel->calculate_valauation_modal($valdata);
      /// $data['total_valuation']      = $this->ReportsModel->calculate_total_valauation($valdata);
		

	   $summary = $this->ReportsModel->load_item_summary($item_id, $unit_id, $mc_id, $mc_grp_id, $val_id,$invtp_id);

        $data['item_unit_list'] = $this->ReportsModel->item_unit_list($item_id);
        $data['mc_list'] = $this->ReportsModel->mc_list();
        $data['valuation_list'] = ['DEFAULT', 'AVG', 'FIFO', 'LIFO'];
        $data['balance'] = $balance;
        $data['item_value'] = $item_value;
        $data['method'] = $method;
        $data['clo_balance'] = $clo_balance;
        $data['clo_item_value'] = $clo_item_value;
        $data['clo_method'] = $clo_method;
        $data['opening_balance_list'] = $opening_balance_list;
        $data['closing_balance_list'] = $closing_balance_list;
        $data['item_id'] = $item_id;
        $data['item_name'] = $item_name;
        $data['unit_id'] = $unit_id;
        $data['mc_id'] = $mc_id;
        $data['mc_grp_id'] = $mc_grp_id;
        $data['mc_grp_name'] = $mc_grp_name;
        $data['val'] = $val;
        $data['to_date'] = $to_date;
        $data['val_id'] = $val_id;
        $data['summary'] = $summary;
	    $data['invtp_id'] = $invtp_id;
        $data['inventory_type_list'] =array("1"=>"AVAILABLE","2"=>"PACKED","0"=>"OBSOLETE","3"=>"IN-TRANSIT");
        return view($this->folder_path.'reports/item_summary',$data); 
      }

      
      
      public function load_vouher_txn_history($from_date,$voucher_type_id){
         
         echo  $this->ReportsModel->load_ltst_voucher_txn(1, 10, 0, $from_date,$voucher_type_id);
         exit;
          
      }
      
      public function ajax_day_book()
      {
            $pq_curPage = (int)$_POST["pq_curpage"];
            $limit     = (int)$_POST["pq_rpp"];
            $view = !empty($_GET['view']) ? $_GET['view'] : 0; 
            $search = '';
			$search_column='';
            $pq_filter    = $this->request->getVar('pq_filter');
			
            if(!empty($pq_filter)){
                $pq_filter = json_decode($pq_filter);
                $search = $pq_filter->data[0]->value;
				$search_column = $pq_filter->data[0]->dataIndx;
				
            }
               
            
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
		    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
		    $view     = $this->request->getVar('view');
		    if($view==0)
            echo $this->ReportsModel->load_day_book_condensed($pq_curPage, $limit, $from_date, $to_date, $view,$search,$search_column);
		    if($view==1)
			echo $this->ReportsModel->load_day_book_detailed($pq_curPage, $limit, $from_date, $to_date, $view,$search,$search_column);
		    	
		
      }
    

       public function day_book(){
           
        $array = [];
        $view = isset($_GET['view']) ? $_GET['view'] : 0;
        
			 
        $from_date = $_GET['fromdate'] ?? '';
        $to_date = $_GET['todate'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);
       
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;
		$data['view']              = $view;
        $data['array']    = $array;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
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
        $from_date = $_GET['fromdate'] ?? '';
        $to_date = $_GET['todate'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);
       
       
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

        return view($this->folder_path.'reports/bills_management',$data);  
    }
    
    function bills_management_accounts($type)
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $from_date_ymd = date('Y-m-d',strtotime($from_date));
        $to_date_ymd   = date('Y-m-d',strtotime($to_date));

        $data['data'] = $this->ReportsModel->load_bills_management_accounts($from_date_ymd, $to_date_ymd, $type);
        
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
        
        return view($this->folder_path.'reports/bills_management_details',$data);
    }
    
    
    function bills_management_statement($bill_type) 
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $from_date_ymd = date('Y-m-d',strtotime($from_date));
        $to_date_ymd   = date('Y-m-d',strtotime($to_date));

        $data['data'] = $this->ReportsModel->load_bills_management_statement($from_date_ymd, $to_date_ymd, $bill_type);
        
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

    public function project_reporting()
    {
        $from_date = '';
        $to_date = '';
        
        if($this->request->getMethod() == 'post'){

            $from_date = $this->request->getVar('from_date');
            $to_date = $this->request->getVar('to_date');

            $from_date = validate_from_date($from_date);
            $to_date = validate_to_date($to_date);
            
  
            $report_type       = $_POST['report_type'];
            $sub_type          = $_POST['sub_type'];
   
            if($report_type == 'Account Wise Report'){
                if($sub_type == 'Projects Report- Account Wise'){
                    return redirect()->to($this->base_url.'reports/project_report_account_wise?from_date='.$from_date.'&to_date='.$to_date);
                }
                if($sub_type == 'Account Wise- Projects Report'){
                    return redirect()->to($this->base_url.'reports/project_report_wise?from_date='.$from_date.'&to_date='.$to_date);
                }
                
            }
            if($report_type == 'Projects Trial'){
                if($sub_type == 'ALL Projects'){
                    return redirect()->to($this->base_url.'reports/project_account_trial?from_date='.$from_date.'&to_date='.$to_date);
                }
                if($sub_type == 'Project Group'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/project_group_trial/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
                }
            }
            if($report_type == 'Projects Ledger'){
                if($sub_type == 'Project Group'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/project_group_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
                }
                if($sub_type == 'Project'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/project_account_ledger/'.$id.'?from_date='.$from_date.'&to_date='.$to_date);
                }
            }
            if($report_type == 'Projects Summary'){
                if($sub_type == 'Project Expense Wise Summary'){
                    $type = 3;
                }
                if($sub_type == 'Project Income Wise Summary'){
                    $type = 4;
                }
                if($sub_type == 'Project Liability Summary'){
                    $type = 1;
                }
                if($sub_type == 'Project Asset Summary'){
                    $type = 2;
                }

                $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                return redirect()->to($this->base_url.'reports/project_summary/'.$id.'?type='.$id);
            }
            if($report_type == 'Project Reporting'){
                if($sub_type == 'Project Expense Report'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_group_summary/'.$id);
                }
                if($sub_type == 'Project Income Report'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_account_summary/'.$id);
                }
                if($sub_type == 'Project Liability Report'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_account_summary/'.$id);
                }
                if($sub_type == 'Project Asset Report'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_account_summary/'.$id);
                }
                if($sub_type == 'Consolidated Project Reporting'){
                    $id = !empty($_POST['id']) ? $_POST['id'] : 0;
                    return redirect()->to($this->base_url.'reports/cost_centre_account_summary/'.$id);
                }
            }
            
            return redirect()->to($this->base_url.'reports/project_reporting'); 
        }

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['pr_list']                = $this->ReportsModel->get_pr_list();
        $data['pr_groups']              = $this->ReportsModel->get_pr_groups();

        return view($this->folder_path.'reports/project_reporting',$data);  
    }
   
    function project_report_account_wise()
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $from_date2 = date('Y-m-d',strtotime($from_date));
        $to_date2 = date('Y-m-d',strtotime($to_date));
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['data'] = $this->ReportsModel->load_project_report_account_wise($from_date2, $to_date2);
        
        return view($this->folder_path.'reports/project_reporting_account_wise',$data);
    }

    function project_report_wise()
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $from_date2 = date('Y-m-d',strtotime($from_date));
        $to_date2 = date('Y-m-d',strtotime($to_date));
        
        $data['message_output']        = $this->message_output;
        $data['folder_path']           = $this->folder_path;
        $data['base_url']              = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['data'] = $this->ReportsModel->load_project_report_wise($from_date2, $to_date2);
        
        return view($this->folder_path.'reports/project_reporting_wise',$data);
    }

    function ajax_project_account_trial()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date = date('Y-m-d',strtotime($from_date));
        $to_date = date('Y-m-d',strtotime($to_date));
    
        $type = $this->request->getVar('type');
        
        $response = $this->ReportsModel->load_project_account_trial($type,$pq_curPage,$limit,$from_date,$to_date);

        return json_encode($response);
    }
    function project_account_trial()
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';
        $type = $_GET['type'] ?? 1;

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

       

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['type']                   = $type;
        
        return view($this->folder_path.'reports/project_reporting_account_trial',$data);
    }

    function ajax_project_group_trial()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date = date('Y-m-d',strtotime($from_date));
        $to_date = date('Y-m-d',strtotime($to_date));
    
        $type = $this->request->getVar('type');
        $project_grp_id = $this->request->getVar('project_grp_id');
        
        $response = $this->ReportsModel->load_project_group_trial($project_grp_id,$type,$pq_curPage,$limit,$from_date,$to_date);

        return json_encode($response);
    }
    function project_group_trial($id)
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';
        $type = $_GET['type'] ?? 1;

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $project_grp_name = '';
        
        $group_info = $this->ReportsModel->get_project_group_info($id);
        if($group_info){
            $project_grp_name = $group_info['project_grp_name'];
        }

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['project_grp_id']         = $id;
        $data['project_grp_name']       = $project_grp_name;
        $data['type']                   = $type;
        
        return view($this->folder_path.'reports/project_reporting_group_trial',$data);
    }

    function ajax_project_account_ledger()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date = date('Y-m-d',strtotime($from_date));
        $to_date = date('Y-m-d',strtotime($to_date));
    
        $project_id = $this->request->getVar('project_id');
        $type = $this->request->getVar('type');
        
        $response = $this->ReportsModel->load_project_account_ledger($project_id,$type,$pq_curPage,$limit,$from_date,$to_date);

        return json_encode($response);
    }
    function project_account_ledger($id)
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';
        $type = $_GET['type'] ?? 1;

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $project_name = '';
        
        $project_info = $this->ReportsModel->get_project_info($id);
        if($project_info){
            $project_name = $project_info['project_name'];
        }

        $project_op_bal = $this->ReportsModel->get_project_op_bal($id,$type);

        if($from_date != '')
        {
            $date = date("Y-m-d", strtotime($from_date));
            $project_txn = $this->ReportsModel->get_last_proj_txn($id,$type, $date);

            if($project_txn){
                $project_op_bal = floatval($project_txn['proj_txn_bal']);
            }
        }

        if($project_op_bal < 0)
            $project_opening = formatAmount(abs($project_op_bal)).' CR';
        else
            $project_opening = formatAmount($project_op_bal).' DR';

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['project_id']             = $id;
        $data['project_name']           = $project_name;
        $data['project_opening']        = $project_opening;
        $data['type']                   = $type;
        
        return view($this->folder_path.'reports/project_reporting_account_ledger',$data);
    }

    function ajax_project_group_ledger()
    {
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date = date('Y-m-d',strtotime($from_date));
        $to_date = date('Y-m-d',strtotime($to_date));
    
        $project_grp_id = $this->request->getVar('project_grp_id');
        $type = $this->request->getVar('type');
        
        $response = $this->ReportsModel->load_project_group_ledger($project_grp_id,$type,$pq_curPage,$limit,$from_date,$to_date);

        return json_encode($response);
    }
    function project_group_ledger($id)
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';
        $type = $_GET['type'] ?? 1;

        $from_date = validate_from_date($from_date);
        $to_date = validate_to_date($to_date);

        $project_grp_name = '';
        
        $group_info = $this->ReportsModel->get_project_group_info($id);
        if($group_info){
            $project_grp_name = $group_info['project_grp_name'];
        }

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['from_date']              = $from_date;
        $data['to_date']                = $to_date;
        $data['project_grp_id']         = $id;
        $data['project_grp_name']       = $project_grp_name;
        $data['type']                   = $type;
        
        return view($this->folder_path.'reports/project_reporting_group_ledger',$data);
    }

    function project_summary($id)
    {
        $type = $_GET['type'] ?? 1;
        $project_name = '';
        
        $project_info = $this->ReportsModel->get_project_info($id);
        if($project_info){
            $project_name = $project_info['project_name'];
        }

        $project_op_bal = $this->ReportsModel->get_project_op_bal($id,$type);

        if($project_op_bal < 0)
            $project_opening = formatAmount(abs($project_op_bal)).' CR';
        else
            $project_opening = formatAmount($project_op_bal).' DR';

        $summary = $this->ReportsModel->load_project_summary($id,$type);

        $data['message_output']         = $this->message_output;
        $data['folder_path']            = $this->folder_path;
        $data['base_url']               = $this->base_url;
        $data['project_id']             = $id;
        $data['project_name']           = $project_name;
        $data['project_opening']        = $project_opening;
        $data['type']                   = $type;
        $data['summary']                = json_encode($summary); 
        
        return view($this->folder_path.'reports/project_reporting_account_summary',$data);
    }
}