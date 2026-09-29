<?php
namespace App\Controllers\Admin;
use App\Models\Admin\RewritebooksModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Rewritebooks extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text','custom']);
		$this->RWBooksModel   = new RewritebooksModel();
		$this->TransactionModel   = new TransactionModel();
		$this->LogModel        = new ERPLogModel();
		$this->CommonModel     =  new CommonModel();		
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
		
    }
   
     public  function index()
	 {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url; 
		$data['session']         = $this->session;
		return view($this->folder_path.'rewritebooks',$data);
		
	 } 

	 public function load_details()
	 {
	 		$type = $this->request->getVar('type');

	 		if($type == 'account'){
	 				$list =  $this->RWBooksModel->get_account_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'bill_sundry'){
	 				$list =  $this->RWBooksModel->get_bill_sundry_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'bill_by_bill'){
	 				$list =  $this->RWBooksModel->get_bill_by_bill_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'cost_center'){
	 				$list =  $this->RWBooksModel->get_cost_center_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'item'){
	 			$list =  $this->RWBooksModel->get_item_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}

	 		return json_encode(['status' => false, 'message' => 'No Account Found']);
	 }

	 public function update_balance()
	 {
	 		$type = $this->request->getVar('type');
	 		$id 	= $this->request->getVar('id');

	 		if($type == 'account'){
	 				$this->TransactionModel->update_account_balance($id);
	 				return json_encode(['status' => true]);
	 		}
	 		if($type == 'bill_sundry'){
	 				$this->TransactionModel->update_bill_sundry_balance($id);
	 				return json_encode(['status' => true]);
	 		}
	 		if($type == 'bill_by_bill'){
	 				$this->TransactionModel->update_bill_txn_balance($id);
	 				return json_encode(['status' => true]);
	 		}
	 		if($type == 'cost_center'){
	 				$this->TransactionModel->update_cc_txn_balance($id);
	 				return json_encode(['status' => true]);
	 		}
	 		if($type == 'item'){
		 			$this->TransactionModel->update_item_balances($id);
		 			return json_encode(['status' => true]);
	 		}
	 		return json_encode(['status' => false, 'message' => 'Something went wrong']);
	 }

 	public function load_voucher_type_details()
 	{
		$list =  $this->RWBooksModel->get_voucher_type_details();
		if($list)
			return json_encode(['status' => true, 'list' => $list]);
	

		return json_encode(['status' => false, 'message' => 'No Voucher Type  Found']);
 	}

 	public function verify_vouchers()
 	{
			$voucher_type_id = $this->request->getVar('id');
			$list = [];

			// if($voucher_type_id == 1 || $voucher_type_id == 5 || $voucher_type_id == 9 || $voucher_type_id == 13)
			// {
					$list = $this->RWBooksModel->check_banking_vouchers($voucher_type_id);
			// }
			
			return json_encode(['status' => true, 'list' => $list]);
 	}

 	public function edit($voucher_txn_id)
 	{
 		$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);

		if(!empty($voucher_info)){
			if($voucher_info['voucher_type_id']=='18')
	             return redirect()->to(base_url().'/admin/sales/edit/'.$voucher_txn_id);
	         else if($voucher_info['voucher_type_id']=='11')
                 return redirect()->to(base_url().'/admin/purchase/edit/'.$voucher_txn_id); 
             else if($voucher_info['voucher_type_id']=='10')
                 return redirect()->to(base_url().'/admin/physical_verification/edit/'.$voucher_txn_id.'/'.$voucher_info['voucher_type_id']);
             else if($voucher_info['voucher_type_id']=='14')
                 return redirect()->to(base_url().'/admin/production/modify/'.$voucher_txn_id.'/?bom_id=0');
             else if($voucher_info['voucher_type_id']=='6')
                 return redirect()->to(base_url().'/admin/inward_challan/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='7')
                 return redirect()->to(base_url().'/admin/delivery_challan/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='2')
                 return redirect()->to(base_url().'/admin/credit_note/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='3')
                 return redirect()->to(base_url().'/admin/debit_note/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='12')
                 return redirect()->to(base_url().'/admin/purchase_order/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='15')
                 return redirect()->to(base_url().'/admin/stock_transfer/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='16')
                 return redirect()->to(base_url().'/admin/vouchers/reverse_journal/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='17')
                 return redirect()->to(base_url().'/admin/quotations/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='19')
                 return redirect()->to(base_url().'/admin/sales_order/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='21')
                 return redirect()->to(base_url().'/admin/purchase_requisition/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='20')
                 return redirect()->to(base_url().'/admin/packing_unpacking/modify_stock_journal/'.$voucher_txn_id."/".$voucher_info['voucher_type_id']);

             else if($voucher_info['voucher_type_id']=='1' || $voucher_info['voucher_type_id']=='5' || $voucher_info['voucher_type_id']=='9' || $voucher_info['voucher_type_id']=='13')
                 return redirect()->to(base_url().'/admin/vouchers/edit/'.$voucher_txn_id);
             else if($voucher_info['voucher_type_id']=='8')
                 return redirect()->to(base_url().'/admin/memorandum/edit/'.$voucher_txn_id);
		}
 	}
 	public function verify_single_voucher()
 	{
 		$voucher_txn_id = $this->request->getVar('id');
 		$account_id = $this->request->getVar('account_id');
 		$type = $this->request->getVar('type');
 		$status = -1;

 		if($type == 'all')
 			$status = $this->RWBooksModel->check_single_voucher($voucher_txn_id);
 		if($type == 'bbb')
 			$status = $this->RWBooksModel->verify_bbb_vouchers($account_id, $voucher_txn_id);

 		return json_encode(['status' => $status]);
 	}

 	public function load_db_details()
 	{
 		$list =  $this->RWBooksModel->get_master_db_details();
		if($list)
			return json_encode(['status' => true, 'list' => $list]);
	

		return json_encode(['status' => false, 'message' => 'Something went wrong']);
 	}

 	public function diagnose_db()
 	{
 		$table_name = $this->request->getVar('table_name');
 		return json_encode(['status' => true, 'list' => []]); 
 	}

	public function get_bbb_accounts()
	{
		$bbb_groups = $this->TransactionModel->get_bbb_groups();

		$list =  $this->RWBooksModel->get_bbb_accounts($bbb_groups);
		if($list)
			return json_encode(['status' => true, 'list' => $list]);

		return json_encode(['status' => false, 'message' => 'Something went wrong']);
	}

	public function get_bbb_vouchers()
 	{
 		$account_id = $this->request->getVar('account_id');
 		$list = $this->RWBooksModel->get_bbb_vouchers($account_id);
 		if($list)
			return json_encode(['status' => true, 'list' => $list]);

 		return json_encode(['status' => false, 'message' => 'No Voucher Found']);
 	}

 	public function verify_bbb_vouchers()
 	{
 		$account_id = $this->request->getVar('account_id');
 		$voucher_txn_id = $this->request->getVar('voucher_txn_id');
 		$status = $this->RWBooksModel->verify_bbb_vouchers($account_id, $voucher_txn_id);
 		return json_encode(['status' => $status]);
 	}
}