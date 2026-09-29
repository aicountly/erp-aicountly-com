<?php
namespace App\Controllers\Admin;
use App\Models\Admin\RewritebooksModel;
use App\Models\FYModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Rewritebooks extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text','custom']);
		$this->RWBooksModel   = new RewritebooksModel();
		$this->FYModel   			= new FYModel();
		$this->CommonModel     =  new CommonModel();		
		$this->auth_session    = new auth_session();
		$this->auth_session->user_restrict();
		$this->auth_session->is_company_opened();
		$this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();		
		$this->bo_id         = $this->session->get('ses_boid');
		$this->fy_id         = $this->session->get('ses_comp_fy_id');
		$this->company_id    = $this->session->get('ses_company_id');
    }
   
     public  function index()
	 {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url; 
		$data['session']         = $this->session;
		$data['NextFyExists']    = $this->RWBooksModel->NextFyExists();		
		return view($this->folder_path.'rewritebooks',$data);
		
	 } 
	 
	
	 
	 public function load_details()
	 {
	 		$type = $this->request->getVar('type');
			$item_id = $this->request->getVar('item_id');
			if(!isset($item_id))
				$item_id='';
			if($type == 'account'){
				$this->FYModel->clear_next_fy_account_balance();

				$total = $this->RWBooksModel->get_account_count();

				return json_encode([
					'status' => true,
					'total'  => (int)$total,
					'batch'  => 100
				]);
			}
	 		/* if($type == 'account'){
	 				$list =  $this->RWBooksModel->get_account_details();
	 				if($list){
						// clear first next fy account opening
						$this->FYModel->clear_next_fy_account_balance();
	 					return json_encode(['status' => true, 'list' => $list]);
					}
	 		} */
			if ($type === 'item' || $type === 'item_valuation') {
				 if ($type === 'item')
				$this->FYModel->clear_next_fy_item_balance();
			 if ($type === 'item_valuation')
				$this->FYModel->clear_next_fy_item_valuation();
			
			// Return only the total count so the UI can show progress
			$total = $this->RWBooksModel->get_item_count();
			return json_encode([
				'status' => true,
				'total'  => (int)$total,
				'batch'  => 500
			]);
		}
			
	 		return json_encode(['status' => false, 'message' => 'No Account Found']);
	 }
	 /**
 * NEW: process items in batches (offset/limit)
 * Returns {status, processed, errors[], done}
 */
 public function update_item_valuation_batch()
{
    $offset = (int) $this->request->getPost('offset');
    $limit  = (int) $this->request->getPost('limit');
    if ($limit <= 0) $limit = 500;

    try {
        // Fetch a page of item ids (id + name only for logging)
        $items = $this->RWBooksModel->get_item_page($offset, $limit);

        if (!$items) {
            return $this->response->setJSON([
                'status'    => true,
                'processed' => 0,
                'errors'    => [],
                'done'      => true
            ]);
        }

        $errors = [];
        foreach ($items as $it) {
            $itemId = (int)$it['id'];
            try {
                // This function (below) computes closing value per center and upserts next FY
                $errs = $this->FYModel->update_fy_item_valuation($itemId);
                if (!empty($errs)) {
                    foreach ($errs as $e) {
                        $errors[] = ['name' => $it['name'], 'error' => $e];
                    }
                }
            } catch (\Throwable $ex) {
                $errors[] = ['name' => $it['name'], 'error' => $ex->getLine() . ' in ' . $ex->getFile() . ': ' . $ex->getMessage()];
            }
        }

        return $this->response->setJSON([
            'status'    => true,
            'processed' => count($items),
            'errors'    => $errors,
            'done'      => false
        ]);

    } catch (\Throwable $e) {
        return $this->response->setJSON([
            'status'  => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function update_item_batch()
{ 
    $offset = (int)$this->request->getPost('offset');
    $limit  = (int)$this->request->getPost('limit');

    if ($limit <= 0) $limit = 500;

    // Fetch a page of items (id+name only)
    $items = $this->RWBooksModel->get_item_page($offset, $limit);

    if (!$items) {
        return $this->response->setJSON([
            'status'    => true,
            'processed' => 0,
            'errors'    => [],
            'done'      => true
        ]);
    }

    // Process each item (call your existing FY method)
    $errors = [];
    foreach ($items as $it) {
        try {
            $errs = $this->FYModel->update_fy_item_balances((int)$it['id']);
            // $errs is expected to be an array of strings (or empty)
            if (!empty($errs)) {
                foreach ($errs as $e) {
                    $errors[] = ['name' => $it['name'], 'error' => $e];
                }
            }
        } catch (\Throwable $ex) {
            $errors[] = ['name' => $it['name'], 'error' => $ex->getLine() . ' in ' . $ex->getFile() . ': ' . $ex->getMessage()];
        }
    }

    return $this->response->setJSON([
        'status'    => true,
        'processed' => count($items),
        'errors'    => $errors,
        'done'      => false
    ]);
}	
	 
	 
	 
	 public function update_account_batch()
{
    $offset = (int)$this->request->getPost('offset');
    $limit  = (int)$this->request->getPost('limit');

    if ($limit <= 0) $limit = 100;

    // Total count (IMPORTANT)
    $total = $this->RWBooksModel->get_account_count();

    // Fetch accounts in batch
    $accounts = $this->RWBooksModel->get_account_page($offset, $limit);

    if (!$accounts) {
        return $this->response->setJSON([
            'status'    => true,
            'processed' => 0,
            'errors'    => [],
            'done'      => true
        ]);
    }

    $errors = [];
    $common = $this->FYModel->prepare_common_fy_data();
    foreach ($accounts as $acc) {
        try {
            $errs = $this->FYModel->update_fy_account_balance((int)$acc['id'],$common);

            if (!empty($errs)) {
                foreach ($errs as $e) {
                    $errors[] = ['name' => $acc['name'], 'error' => $e];
                }
            }

        } catch (\Throwable $ex) {
            $errors[] = [
                'name' => $acc['name'],
                'error' => $ex->getMessage()
            ];
        }
    }

    $processed = count($accounts);

    // ✅ CORE FIX: check if last batch
    $done = ($offset + $processed) >= $total;

    return $this->response->setJSON([
        'status'    => true,
        'processed' => $processed,
        'errors'    => $errors,
        'done'      => $done   // ✅ FIXED
    ]);
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
	 			if($id == 0)
	 				$this->RWBooksModel->update_bbb_opening_balance();
	 			else
	 				$this->TransactionModel->update_bill_txn_balance($id);
	 				return json_encode(['status' => true]);
	 		}
	 		if($type == 'cost_center'){
	 				$this->TransactionModel->update_cc_txn_balance($id);
	 				return json_encode(['status' => true]);
	 		}
	 		if($type == 'item'){
		 			$this->TransactionModel->update_item_balances_only($id);
		 			return json_encode(['status' => true]);
	 		}
	 		if($type == 'project'){
	 			$this->TransactionModel->update_project_lia_bal($id);
	 			$this->TransactionModel->update_project_ast_bal($id);
	 			$this->TransactionModel->update_project_exp_bal($id);
	 			$this->TransactionModel->update_project_rev_bal($id);
	 			return json_encode(['status' => true]);
	 		}
	 		return json_encode(['status' => false, 'message' => 'Something went wrong']);
	 }
	 
   
	
	public function verify_item_default_valuation(){
		$voucher_txn_id = $this->request->getVar('voucher_txn_id');
		$item_id        = $this->request->getVar('item_id');
		$method_id      = $this->request->getVar('method_id');
		$item_info       = $this->TransactionModel->get_item_info($item_id);
		
	    $item_default_val_method = $item_info['valmethod_id'];
		if( $item_default_val_method=='' ||  $item_default_val_method==0)
			  $item_default_val_method=1;
		if($item_default_val_method==1)
			$valuation_method='AVG';
		else if($item_default_val_method==2)
			$valuation_method='FIFO';
		else if($item_default_val_method==3)
			$valuation_method='LIFO';
	    else
          $valuation_method='AVG';			
		
		$item_name   = $item_info['item_name'];
		$response = $this->RWBooksModel->calculating_item_default_valuations($voucher_txn_id,$item_id,$item_default_val_method);
		if($response['status']==1)
		return json_encode(['status' => true,"item_id"=>$response['item_id'],"voucher_txn_id"=>$response['voucher_txn_id'],"name"=>$response['name']]);
	    else
		return json_encode(['status' => false,'message'=>'Calculate first default('.$valuation_method.') valuation method of item('.$item_name.')']);	
        }
	
	
	public function get_container_vouchers_listing(){
		$voucher_txn_id = $this->request->getVar('voucher_txn_id');
		$method_id      = $this->request->getVar('method_id');
		$item_id_unit_id      = $this->request->getVar('item_id_unit_id');

		$itmdntd = explode("_",$item_id_unit_id);
		$list           = $this->RWBooksModel->get_voucher_items_valuation_list($voucher_txn_id,$method_id,$item_id_unit_id);
		$this->RWBooksModel->empty_reporting_tables_data($item_id_unit_id,$itmdntd[0],$method_id);
		//SaveErrorLog("empty reporting tables ...".$itmdntd[0].','.$method_id);
		
		return json_encode(['status' => true,'method_id'=>$method_id, 'list' => $list]);
	    
	}/* public function calculate_container_voucher_txns(){
		$voucher_txn_id = $this->request->getVar('voucher_txn_id');
		$method_id      = $this->request->getVar('method_id');
		$item_id_unit_id      = $this->request->getVar('item_id_unit_id');	
		$list           = $this->RWBooksModel->get_voucher_items_valuation_list($voucher_txn_id,$method_id,$item_id_unit_id);
		return json_encode(['status' => true,'method_id'=>$method_id, 'list' => $list]);
	    
	} */
	
	
	public function verify_item_valuation(){
		$voucher_txn_id  = $this->request->getVar('voucher_txn_id');
		$item_id         = $this->request->getVar('itmiduntid');
		$method_id       = $this->request->getVar('method_id');		
		$from_date       = $_POST['from_date'] ?? '';
        $to_date         = $_POST['to_date'] ?? '';

        $from_date = date('Y-m-d',strtotime(validate_fy_from_date($from_date)));
        $to_date = date('Y-m-d',strtotime(validate_fy_to_date($to_date))); 
		$status = $this->RWBooksModel->calculating_item_valuations($voucher_txn_id,$item_id,$method_id,$from_date,$to_date);
	 	  
		return json_encode(['status' => true,'from_date'=>$from_date,'to_date'=>$to_date]);	    
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
 		$list_res =  $this->RWBooksModel->get_master_db_details();
 		if($list_res)
		 return json_encode(array('status' => true, 'list' => $list_res['list'],'columns'=>$list_res['columns'],'dstrng'=>$list_res['dbenc']));
	 
		return json_encode(array('status' => false, 'message' => 'Something went wrong'));
 	}

 	public function diagnose_db()
 	{
		$default_db_name ='aicountlyin_erp0000001';
 		$table_name      = $this->request->getVar('table_name');
		$db_name_info    = $this->request->getVar('dstring');
		$db_name_info    = unobfuscate_link($db_name_info); 
	    $current_db_name = $db_name_info[1];		
		
	    $table_name_data = unobfuscate_link($table_name); 
	    $table_name      = $table_name_data[1];  
		
	    $list            = $this->RWBooksModel->verify_table_details($table_name,$current_db_name,$default_db_name);
 		return json_encode(['status' => $list['status'],'queryes'=>$list['queries'],'errors'=>$list['message']]); 
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

 	public function verify_masters()
 	{
 		$type = $this->request->getVar('type');
 		$id = $this->request->getVar('id');

 		if($type == 'account'){
			$error =  $this->RWBooksModel->check_account_txn_tables($id);
 		}
 		if($type == 'bill_sundry'){
			$error =  $this->RWBooksModel->check_bill_sundry_txn_tables($id);
 		}
 		if($type == 'item'){
 			$error =  $this->RWBooksModel->check_item_txn_tables($id);
 		}

		return json_encode(['status' => true, 'error' => $error]);

 		
 	}

 	public function rectifyMaster()
 	{
 		$type = $this->request->getVar('type');
 		$id = $this->request->getVar('id');

 		if($type == 'account'){
			$error =  $this->RWBooksModel->rectify_account_txn_tables($id);
 		}
 		if($type == 'bill_sundry'){
			$error =  $this->RWBooksModel->rectify_bill_sundry_txn_tables($id);
 		}
 		if($type == 'item'){
 			$error =  $this->RWBooksModel->rectify_item_txn_tables($id);
 		}

 		if(count($error))
 			return json_encode(['status' => false, 'error' => $error]);

 		return json_encode(['status' => true]);
 	}

 	public function load_fy_master_details() //23 masters
	 {
	 		$type = $this->request->getVar('type');
			if($type == 'gsttinmaster'){
	 				$list =  $this->FYModel->get_branch_gstin_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}

	 		if($type == 'branch'){
	 				$list =  $this->FYModel->get_branch_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'currency'){
	 				$list =  $this->FYModel->get_currency_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'voucher_series'){
	 				$list =  $this->FYModel->get_voucher_series_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}

	 		if($type == 'account_group'){
	 				$list =  $this->FYModel->get_account_group_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'account'){
	 				$list =  $this->FYModel->get_account_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'bill_sundry'){
	 				$list =  $this->FYModel->get_bill_sundry_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'project_group'){
	 				$list =  $this->FYModel->get_project_group_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'project'){
	 				$list =  $this->FYModel->get_project_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}

	 		if($type == 'item_group'){
	 			$list =  $this->FYModel->get_item_group_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'item_category'){
	 			$list =  $this->FYModel->get_item_category_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'item'){
	 			$list =  $this->FYModel->get_item_details($item_id='');
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}
			
	 		if($type == 'item_batch'){
	 			$list =  $this->FYModel->get_item_batch_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'unit'){
	 			$list =  $this->FYModel->get_unit_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}

	 		if($type == 'mc_group'){
	 			$list =  $this->FYModel->get_mc_group_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'mc'){
	 			$list =  $this->FYModel->get_mc_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		
	 		if($type == 'barcode'){
	 			$list =  $this->FYModel->get_barcode_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'label'){
	 			$list =  $this->FYModel->get_label_details();
 				if($list)
 					return json_encode(['status' => true, 'list' => $list]);
	 		}

	 		if($type == 'cost_center_group'){
	 				$list =  $this->FYModel->get_cost_center_group_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'cost_center'){
	 				$list =  $this->FYModel->get_cost_center_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'bill_by_bill'){
	 				$list =  $this->FYModel->get_bill_by_bill_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'bill_of_material'){
	 				$list =  $this->FYModel->get_bill_by_bill_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		if($type == 'print'){
	 				$list =  $this->FYModel->get_print_details();
	 				if($list)
	 					return json_encode(['status' => true, 'list' => $list]);
	 		}
	 		
	 		
	 		return json_encode(['status' => false, 'message' => 'No Account Found']);
	 }

 	public function update_fy_masters()
	{
		$type = $this->request->getVar('type');
		$id 	= $this->request->getVar('id');
        
		if($type == 'gsttinmaster'){
			$response = $this->FYModel->update_gstin_masters($id);
 		}
		
		if($type == 'branch'){
			$response = $this->FYModel->update_branch_masters($id);
 		}
 		if($type == 'currency'){
			$response = $this->FYModel->update_currency_masters($id);
 		}
 		if($type == 'voucher_series'){
			$response = $this->FYModel->update_voucher_series_masters($id);
 		}

 		if($type == 'account_group'){
			$response = $this->FYModel->update_account_group_masters($id);
 		}
 		if($type == 'account'){
			$response = $this->FYModel->update_account_masters($id);
 		}
 		if($type == 'bill_sundry'){
			$response = $this->FYModel->update_bill_sundry_masters($id);
 		}
 		if($type == 'project_group'){
			$response = $this->FYModel->update_project_group_masters($id);
 		}
 		if($type == 'project'){
			$response = $this->FYModel->update_project_masters($id);
 		}

 		if($type == 'item_group'){
			$response = $this->FYModel->update_item_group_masters($id);
 		}
 		if($type == 'item_category'){
			$response = $this->FYModel->update_item_category_masters($id);
 		}
 		if($type == 'item'){
			$response = $this->FYModel->update_item_masters($id);
 		}
 		if($type == 'item_batch'){
			$response = $this->FYModel->update_item_batch_masters($id);
 		}
 		if($type == 'unit'){
			$response = $this->FYModel->update_unit_masters($id);
 		}

 		if($type == 'mc_group'){
			$response = $this->FYModel->update_mc_group_masters($id);
 		}
 		if($type == 'mc'){
			$response = $this->FYModel->update_mc_masters($id);
 		}

 		if($type == 'barcode'){
			$response = $this->FYModel->update_barcode_masters($id);
 		}
 		if($type == 'label'){
			$response = $this->FYModel->update_label_masters($id);
 		}

 		if($type == 'cost_center_group'){
			$response = $this->FYModel->update_cost_center_group_masters($id);
 		}
 		if($type == 'cost_center'){
			$response = $this->FYModel->update_cost_center_masters($id);
 		}
 		if($type == 'bill_by_bill'){
			$response = $this->FYModel->update_bill_by_bill_masters($id);
 		}
 		if($type == 'bill_of_material'){
			$response = $this->FYModel->update_bill_by_bill_masters($id);
 		}
 		if($type == 'print'){
			$response = $this->FYModel->update_print_masters($id);
 		}

 		if(!empty($response)){
 			return json_encode($response);
 		}
		return json_encode(['status' => false, 'message' => 'Something went wrong with type']);
	}

	public function update_fy_balance()
	{
			$type = $this->request->getVar('type');
			$id 	= $this->request->getVar('id');

			if($type == 'account'){
				    
					$errors = $this->FYModel->update_fy_account_balance($id);
					return json_encode(['status' => true, 'errors' => $errors]);
			}
		
		   /* if($type == 'item'){
	 			$errors = $this->FYModel->update_fy_item_balances($id);
	 			return json_encode(['status' => true, 'errors' => $errors]);
			} */
			
			return json_encode(['status' => false, 'message' => 'Something went wrong']);
	}

	function reset_mapping()
	{
		$this->RWBooksModel->reset_mapping();
	}
}