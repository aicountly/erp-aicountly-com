<?php
namespace App\Controllers\Admin;

use App\Models\Admin\TransactionModel;
use App\Models\Admin\ReportsModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Ajax  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);

			$this->TransactionModel  = new TransactionModel();	
            $this->ReportsModel  = new ReportsModel();					
			$this->auth_session  = new auth_session();			
			$this->auth_session->user_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      =  base_url().'/'.getenv('AdminPath');
			$this->folder_path   =  getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->auth_session->is_company_opened();
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->company_id    =  $this->session->get('ses_company_id');
			
			$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
    }
   
      public function ajax_itemtxn_units(){
	    $itmunitmst_list = $this->ReportsModel->itmunitmst_list();
		if($this->request->getMethod() == 'post'){	            
		    $final_units = array();
		    $final_units[''] = 'All Units';
            $item_id  =  $this->request->getVar('item_id');		  
            $result   =  $this->ReportsModel->GetUnqUnits($item_id);
			if($result){
				foreach($result as $id => $row){
				  	if(isset($itmunitmst_list[$id]))
						$final_units[$id]= $itmunitmst_list[$id];
					
				}
				
			}
			
		echo form_dropdown('unitid',$final_units, '',' id="unitid" style="font-size:18px;border-radius: 6px 0px 0px 6px;" class="selectwidget form-control"'); 
			   	
		}	   
       
   }
    public function itemqtybalance($itemid,$voucher_date,$unit_id){
       echo  json_encode($this->TransactionModel->get_item_balance($itemid,$voucher_date,$unit_id));
       die();
     }
   
   public function accountbalance($accountid,$voucher_date){
      echo  $this->TransactionModel->get_account_balance($accountid,$voucher_date);
       die();
     }
   
    public function get_items()
		{
				$search = $_GET['term'];
				$data = $this->TransactionModel->ajax_items($search);
				return json_encode($data);
		}
	public function get_accounts()
		{
				$search = $_GET['term'];
				$array = [];

				if(isset($_GET['voucher_type_id']) && $_GET['voucher_type_id'] == '1'){
					$array = [23];
				}

				$data = $this->TransactionModel->ajax_accounts($search,$array);
				return json_encode($data);
		}
	
	public function get_acc_bsd_accounts()
		{
				$search = $_GET['term'];
				$data = $this->TransactionModel->ajax_acc_bsd_accounts($search);
				return json_encode($data);
		}
		
		
	public function get_billsundry_items()
    { 
        $search = $_GET['term'];
				$data = $this->TransactionModel->ajax_billsundry_items($search);
				return json_encode($data);
    }
    public function getCc()
    {
        if($this->request->getMethod() == 'post'){
            $data = $this->TransactionModel->get_cc();
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
    
     public function getItemTracking()
    {
        if($this->request->getMethod() == 'post'){
            $items_id_array = $_POST['items_id_array'];
            $data = [];
            foreach($items_id_array as $item_id)
            {
                $data[] = $this->TransactionModel->get_item_tracking_list($item_id);
            }
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
    
	
	public function ajax_tracking_items_list(){
		
		 if($this->request->getMethod() == 'post'){
            $tracking_no = $_POST['tracking_no'];
           echo  $data = $this->TransactionModel->tracking_items_trackno($tracking_no);
           
        }
	}
	
	
	 public function getItemBatch()
    {
        if($this->request->getMethod() == 'post'){
            $items_id_array = $_POST['items_id_array'];
            $data = [];
            foreach($items_id_array as $item_id)
            {
                $data[] = $this->TransactionModel->get_item_batch_list($item_id);
            }
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
	
    public function getAccountBillRefs()
    {
        if($this->request->getMethod() == 'post'){
            $account_id_array = $_POST['account_id_array'];
            $data = [];
            foreach($account_id_array as $account_id)
            {
                $data[] = $this->TransactionModel->get_account_bill_refs($account_id);
            }
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
    
    public function get_all_item_balances(){
     if($this->request->getMethod() == 'post' && $this->request->isAjax()){  

    		$voucher_date = $this->request->getVar('voucher_date');
    		$item_id_array = $this->request->getVar('item_id_array');
			if(!$item_id_array){
			 $status=false;	
				$account_balances = [];
			}
			else{
			
			$status=true;
    		$account_balances = [];
    		foreach ($item_id_array as $item_id => $unit_id) {
    			$item_balance =$this->TransactionModel->get_item_balance($item_id,$voucher_date,$unit_id);
    			$account_balances[] = [
    				 'item_id' => $item_id,
    			     'item_unit_id' => $unit_id,
    				 'balance' => $item_balance
    			];
    		}
			}
    		return json_encode(['status' => $status, 'item_balances' => $account_balances]);
    	}   
        
    }

    public function get_all_account_balances() 
    {
    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){  

    		$voucher_date = $this->request->getVar('voucher_date');
    		$account_id_array = $this->request->getVar('account_id_array');
			if(!$account_id_array){
			 $status=false;	
				$account_balances = [];
			}
			else{
			
			$status=true;

    		$account_balances = [];
    		foreach ($account_id_array as $account_id) {

    			$account_balance = $this->TransactionModel->get_account_balance($account_id,$voucher_date);
    			$account_balances[] = [
    				'account_id' => $account_id,
    				'balance' => $account_balance
    			];
    		}
        }
    		return json_encode(['status' => $status, 'account_balances' => $account_balances]);
    	}
    }

    public function getAccountBills()
    {
        if($this->request->getMethod() == 'post'){
            $account_id = $_POST['account_id'];

            $acc_name = '';
            $acc_op_bal = 0;
            $acc_op_bal_drcr = 'D';

            $get_account_info = $this->TransactionModel->get_account_info($account_id);
            if($get_account_info)
            	$acc_name = $get_account_info['acc_name'];

            $account_opn_balance_info = $this->TransactionModel->account_opn_balance_info($account_id);
            if($account_opn_balance_info){
            	$acc_op_bal = $account_opn_balance_info['acc_op_bal'];
            	if($acc_op_bal < 0){
            		$acc_op_bal = abs($acc_op_bal);
            		$acc_op_bal_drcr = 'C';
            	}
            	else{
            		$acc_op_bal = $acc_op_bal;
            		$acc_op_bal_drcr = 'D';
            	}
            }
            
            $data['acc_name'] = $acc_name;
            $data['acc_op_bal'] = $acc_op_bal;
            $data['acc_op_bal_drcr'] = $acc_op_bal_drcr;
            $data['grid'] = $this->TransactionModel->get_account_all_bill_refs($account_id);
            
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }

    public function setAccountBills()
    {
        if($this->request->getMethod() == 'post'){
            $bills_json_array = $_POST['bills_json_array'];

            foreach ($bills_json_array as $key => $value) {
            	$value['bill_due_date'] = $value['bill_due_date'] != '' ? date('Y-m-d', strtotime($value['bill_due_date'])) : '';

            	$this->TransactionModel->update_account_all_bill_refs($value);
            }
            
            echo json_encode(['status' => true, 'message' => 'Data Updated']);
        }
    }

}