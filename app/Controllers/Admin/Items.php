<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Traits\TransactionTrait;
class Items extends BaseController
{
  use TransactionTrait;	
  function __construct()
    {  
	    helper(['form', 'url','text','custom']); 
	    
		$this->ItemsModel        = new ItemsModel();			
		$this->CommonModel       = new CommonModel();				
		$this->LogModel          = new ERPLogModel();
		$this->auth_session      = new auth_session();
		$this->auth_session->is_company_opened();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');		
		$this->item_supply_type = array("1"=>"Goods","2"=>"Services","3"=>"Capital Goods");
		$this->item_tax_basis   = array("1"=>"Taxable Value(%)","2"=>"MRP(Rs)");
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
		
		$this->default_valuation_method  =  $this->session->get('ses_dflt_val_method');
	}
    
    public  function ajax_items()	 {
		   
		echo $response =  $this->ItemsModel->ajax_items();	
		
	 }
	 
	 public  function get_last_qty_balance()	 {
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
			 $request   = service('request');
			 $postData  = $request->getPost();		
			 $balance  = $this->ItemsModel->get_last_qty_balance($postData);
			 $response = array();
			 $response['status'] = true;
	      	 $response['balance'] = $balance;
	      	 return json_encode($response);
		}   
	 } 
  
   public function list_items()    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;		
		return view($this->folder_path.'items/view',$data);		
    } 
    
	public function GetItemUnits(){
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
			 $request                = service('request');
			 $postData               = $request->getPost();		
			echo 	$this->ItemsModel->get_item_units($postData['item_id']);
		}
		
	}
	
 	public function ledger_detail($item_id){	
		$item_info  = $this->ItemsModel->item_info($item_id);
	    if(empty($item_info)){
           throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
		
		
		
		$from_date  = validate_fy_from_date($_GET['from_date'] ?? '');
		$to_date    = validate_fy_to_date($_GET['to_date'] ?? '');
		$invtp_id   = !empty($_GET['invtp_id']) ? $_GET['invtp_id'] : 1;
		$unit_id    = !empty($_GET['unit_id']) ? $_GET['unit_id'] : 0;
		if(empty($unit_id)){
           throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
		
		
		$mc_id      = !empty($_GET['mc_id']) ? $_GET['mc_id'] : 0;
		$mc_grp_id  = !empty($_GET['mc_grp_id']) ? $_GET['mc_grp_id'] : 0;
		$val        = !empty($_GET['val']) ? $_GET['val'] : $this->default_valuation_method;
		$view       = !empty($_GET['view']) ? $_GET['view'] : 0;
		$item_unit_info  = $this->ItemsModel->item_unit_info($unit_id);
		$opening_balance_list = [];		
		  
		$closing_balance_list = [];
		//$closing_balance_list = $this->ItemsModel->closing_balance_list($item_id, $unit_id, $mc_id, $mc_grp_id,date('Y-m-d', strtotime($to_date)),(int)$data['val_id'],$invtp_id);
		
	  	
	  $data['item_unit_list']   = $this->ItemsModel->get_item_units($item_id);
	  $data['valuation_info']   = [];//$this->ItemsModel->calculate_valauation_modal($valdata);
	  $data['item_dflt_val_id'] = 1;
      $data['item_name']        = $item_info['itm_name'];
	  $data['unit_name']        = !empty($item_unit_info) ? $item_unit_info['itm_unit_name'] : '';
	  $data['valuation_id']     = $val;
	  $data['item_id']          = $item_id;
	  $data['from_date']        = $from_date;
	  $data['to_date']          = $to_date;
	  $data['unit_id']          = $unit_id;
	  $data['mc_grp_id']        = $mc_grp_id;
	  $data['mc_id']            = $mc_id;	  	  
	  $data['method']           = 0;
	  $data['opn_bal_list']     = $opening_balance_list;
	  $data['cls_bal_list']     = $closing_balance_list;
	  $data['base_url']         = $this->base_url;
	  $data['view']             = $view;
	  $data['all_columns']      = [];	  
	  $data['valuation_list']   = ['AVG', 'FIFO', 'LIFO'];
	  $data['matrcntr_dropdown']     = $this->ItemsModel->material_centre_dropdown();
	  $data['matrcntr_grp_dropdown'] = [];//$this->ItemsModel->matrcntr_grp_dropdown();
      $data['items_dropdown']        = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
	  $data['inventory_type_list']   = [];//array("1"=>"AVAILABLE","2"=>"PACKED","0"=>"OBSOLETE","3"=>"IN-TRANSIT");
    return view($this->folder_path.'items/item_ledger_listing',$data);   
  }
  
  public function get_item_summary_totals($item_id){
	 echo  $this->ItemsModel->get_item_summary_totals($item_id); 
  }
  
  
   public function get_item_opening_balances($item_id){
	 echo  $this->ItemsModel->get_item_opening_balances($item_id); 
  }
  
   public function get_item_closing_balances($item_id){
	 echo  $this->ItemsModel->get_item_closing_totals($item_id); 
  }
  
   public function ajax_item_ledger(){
	echo  $this->ItemsModel->load_items_ledger();
	}
	
    public function ajax_get_item_units_list(){
	    if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		  $item_id  =  $this->request->getVar('item_id');
	      $list=$this->ItemsModel->get_item_units($item_id);
		  return json_encode(['status' => true, 'list' => $list]);
	    }
	}
   
   public function remove_items($ids_info){
		if(!$ids_info)
			return redirect()->to($this->base_url.'items/list_items');
		
		 $errors = [];
		 $ids = urlSafeBase64Decode($ids_info); 
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $item_id){		 
		    $stat = true;
		    $name = $this->ItemsModel->get_item_name($item_id);		     
		     if ($this->ItemsModel->check_item_with_voucher($item_id)){ 
		         $stat = false;
		         array_push($errors, 'Failed! Item "'.$name.'" has one or more associated Vouchers');
		     }		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'item',
    		            ];
    		     //$this->LogModel->add_log($log);    		     
    		    $this->ItemsModel->remove_single_items($item_id);
		     }		     
		 }
		 
		 $fy_id       = $this->fy_id;
		 $items_list  = $this->ItemsModel->company_all_items();
		 CreateJsonFile($fy_id,$this->company_id,'itm',$items_list);		 
		 if(count($errors)){
             $this->session->setFlashdata('error_array_message', $errors);
		 }		 
		 return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);	
	  }
    
   public function add_item(){  	     
		$material_centre_dropdown = $this->ItemsModel->material_centre_dropdown();  
		
		  if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		    //echo '<pre>';print_r($_POST); die();			  
			$rules = [				
				'item_name' => [
					'label'  => 'Item Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter item name',
					   ]]				  			   
			       ];
			
            if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }else{               
			 $request                = service('request');
			 $postData               = $request->getPost();
			 $trans_result           = $this->runTransaction(function($db) use ($postData,$material_centre_dropdown){
				 $item_name          = $postData['item_name']; 
				 $item_sku           = $postData['item_sku']; 
				 $item_alias         = $postData['item_alias'];
				 $item_printname     = $postData['item_printname'];
				 $item_shortname     = $item_name;
				 $item_group_id      = $postData['item_group_id']; 
				 $item_unit_id       = $postData['item_unit_id'];
				 $item_sales_acc     = $postData['item_sales_acc'];
				 $item_pur_acc       = $postData['item_pur_acc'];						
				 $item_catg_id       = $postData['item_catg_id'];
				 $item_unit_idm      = $postData['item_unit_idm'];
				 $item_op_bal_qtym   = $postData['item_op_bal_qtym'];
				 $unitdetail_array	 = $postData['unitdetail_array'];
				 $mcqtywise_qty      = $postData['mcqtywise_qty']; 
				 $mcqtywise_avg      = $postData['mcqtywise_avg']; 
				 $mcqtywise_fifo     = $postData['mcqtywise_fifo']; 
				 $mcqtywise_lifo     = $postData['mcqtywise_lifo']; 
				 $item_mrp           = $postData['item_mrp'];
				 $item_tax_cat_id    = $postData['tax_cat_mst_id'];
				 $item_hsn           = $postData['hsn'];
				 				 
				 $insert_data   = [
						'cmp_id'            => (int)$this->company_id,
						'itm_name'          => clean($item_name),
						'itm_alias'         => clean($item_alias),
						'itm_print_name'    => clean($item_printname),
						'itm_upc'           => clean($item_shortname),
						'itm_sku'           => clean($item_sku),
						'tax_cat_mst_id'    => (int)$item_tax_cat_id,
						'itm_is_active'     => (int)1
					  ];					 
				$response = $this->ItemsModel->add_item($insert_data);
				if(!$response['status']){
					return (['status' => false, 'message' => $response['message']]);
    			     }
    			 else{	
				   try{
					$item_id = $response['item_id'];
				   
				   $itmother_insert_data  = array("cmp_id"=>(int)$this->company_id,"itm_id"=>(int)$item_id,"itm_def_unit_id"=>(int)$item_unit_id,
				                            "itm_sales_acc_id"=>(int)$item_sales_acc,'itm_cat_id'=>(int)$item_catg_id,
										    "itm_pur_acc_id"=>(int)$item_pur_acc,"itm_hsn"=>$item_hsn
										  );
				   $this->ItemsModel->add_itm_others($itmother_insert_data);
				   
				   
				   $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>3,"crs_mst_id"=>$item_id,"under_crs_mst_id"=>$item_group_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				   $this->ItemsModel->add_undercrsmt($mst_insert_data);
				   
				   $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>4,"crs_mst_id"=>$item_id,"under_crs_mst_id"=>$item_catg_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				   $this->ItemsModel->add_undercrsmt($mst_insert_data);
				
				
				    if (!empty($item_unit_idm)) {
						foreach ($item_unit_idm as $unikey => $item_unit_val) {
							if ($item_unit_val <= 0) continue;
							$bo_avg = $bo_fifo = $bo_lifo = $bo_qty = 0;
							if (!empty($material_centre_dropdown)) {
								foreach ($material_centre_dropdown as $mcid => $mcname) {									
									if ($mcid <= 0) continue;
									$mc_qty  = !empty($mcqtywise_qty[$mcid][$unikey])  ? $mcqtywise_qty[$mcid][$unikey]  : 0;
									$mc_avg  = !empty($mcqtywise_avg[$mcid][$unikey])  ? $mcqtywise_avg[$mcid][$unikey]  : 0;
									$mc_fifo = !empty($mcqtywise_fifo[$mcid][$unikey]) ? $mcqtywise_fifo[$mcid][$unikey] : 0;
									$mc_lifo = !empty($mcqtywise_lifo[$mcid][$unikey]) ? $mcqtywise_lifo[$mcid][$unikey] : 0;

									if($mc_qty>0){									
									$bo_qty  += $mc_qty ?? 0;
									$bo_avg  += $mc_avg ?? 0;
									$bo_fifo += $mc_fifo ?? 0;
									$bo_lifo += $mc_lifo ?? 0;
									
										$base_data = [
											"itm_id_unit_id"  => $item_id."_".$item_unit_val,
											"cmp_id"          => (int)$this->company_id,
											"itm_py_val_amt"  => (float)0,
											"mat_cent_id"     => (int)$mcid,
											"hobo_id"         => (int)$this->bo_id,
											"cmpfymastr_id"   => (int)$this->fy_id
										];

										$valuations = [											
											'FIFO' => $mc_fifo ?? 0,
											'LIFO' => $mc_lifo ?? 0,
											'AVG' => $mc_avg ?? 0
										];

										foreach ($valuations as $method_id => $val) {
											$data                     = $base_data;
											$data['itm_op_val_amt']   = (float)$val ?? 0;
											$data['itm_val_method_id']= $method_id;
											SaveErrorLog("Saving item opeing data as ".json_encode($data));
											$this->ItemsModel->InsertOppVal($data);
											
											
										}
									  
									}
									
								}
							}
						}
					}
				  
					
					  if (!empty($item_unit_idm)) {
						foreach ($item_unit_idm as $kk => $item_unit_val) {
							if ($item_unit_val === '') continue;

							if (!empty($material_centre_dropdown)) {
								foreach ($material_centre_dropdown as $mcid => $mcname) {
									if ($mcid <= 0) continue;
									$mc_qty = $mcqtywise_qty[$mcid][$kk] ?? 0;
									
									 $bal_data = [
										'itm_id_unit_id'  => $item_id."_".$item_unit_val,
										'cmp_id'          => $this->company_id,
										'itm_op_bal_qty'  => (float)$mc_qty ?? 0,
										'itm_py_bal_qty'  => 0,
										'mat_cent_id'     => $mcid,
										'hobo_id'         => $this->bo_id,
										'cmpfymastr_id'   => $this->fy_id
									  ];
									
										$this->ItemsModel->InsertOppBal($bal_data);
										$itm_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
										$txn_data = array("cmp_id"=>$this->company_id,"itm_id_unit_id"=>$item_id."_".$item_unit_val,
										                  "itm_txn_qty"=>$mc_qty,"itm_txn_date"=>$itm_txn_date,
														  "itm_txn_dr_cr" =>1,"itm_txn_rate"=>0,"itm_txn_amt"=>0,
														  "vch_txn_id"=>0,"txn_id"=>0,"mat_cent_id"=>$mcid,
														  "hobo_id"=>$this->bo_id,"itm_txn_type"=>1
												         );
										$this->ItemsModel->insert_itm_txn_entry($txn_data);
										SaveErrorLog("InsertOppBal => ".json_encode($bal_data));
									
								}
							}
						}
					}
				 
				   
					
					$items_list  = $this->ItemsModel->company_all_items();
				    CreateJsonFile($this->fy_id,$this->company_id,'itm',$items_list);   
					
					return (['status' => true, 'message' => 'Item saved sucessfully']);  
				
				   }
				   catch (\Throwable $e) {
					helper('error');
					$error = formatDbException($e);
					log_message('error', 'DB Error: ' . json_encode($error));
					return json_encode($error);
				} 
						
						
			   }
			   
			   
			   
			});
			/* echo '<pre>';
			print_r($trans_result);
			die(); */
			 if(isset($trans_result['result']['status']) && $trans_result['result']['status']==''){            
					if(isset($trans_result['result']['errors']))
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>$trans_result['result']['errors']]);
					else
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>'']);
				}
		   else
         return $this->response->setJSON($trans_result);  
	 
			 }					 									
		 }						
		   
		$data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url;	
		$data['folder_path']              = $this->folder_path;
		$data['item_group']               = $this->ItemsModel->items_group_dropdown();	
        $data['item_units']               = $this->ItemsModel->units_dropdown();		
		$data['item_category']            = $this->ItemsModel->category_dropdown();
		$data['material_centre_dropdown'] = $material_centre_dropdown;
		$data['sales_acc_dropdown']       = $this->ItemsModel->sales_acc_dropdown();
		$data['purchase_acc_dropdown']    = $this->ItemsModel->purchase_acc_dropdown();	
		$data['tax_category']             = $this->ItemsModel->tax_category_dropdown(1); 
		
		return view($this->folder_path.'items/add_item',$data);		
  }	
  
  public function change_status(){
   	    $status_ids    = $this->request->getVar('pss_status_val'); 
		$ids_info      = $this->request->getVar('accidids');		
		$ids           = base64_decode($ids_info);  		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2   = explode(",",$ids);
		 $ids2   = array_unique($ids2);
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat   = true;
		     $name   = $this->ItemsModel->get_item_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					   if ($this->ItemsModel->check_item_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Item "'.$name.'" already exists');
					   }
					}
					 if($status==0){					  
						 if ($this->ItemsModel->check_item_txn_exists($account_id)){
							 $stat = false;
							 array_push($errors, 'Failed! Item "'.$name.'" has one or more associated vouchers');
						 } 					 
						 if ($this->ItemsModel->check_item_opn_exists($account_id)){
							 $stat = false;
							 array_push($errors, 'Failed! Item "'.$name.'" has default qty');
						 } 
					 }					 
			     if($stat)
			       {	   
		           $this->ItemsModel->changestatus_single_accounts($account_id,$status);					
			       }
		        }
			 }			 
		 }
	
		  
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	}	
   
  public function group_change_status(){
   	     $status_ids    = $this->request->getVar('pss_status_val'); 
	 	 $ids_info      = $this->request->getVar('accidids');		
		 $ids           = base64_decode($ids_info);  		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 $ids2  = array_unique($ids2);
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat = true;
		     $name = $this->ItemsModel->get_item_group_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					 if ($this->ItemsModel->check_item_group_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Group "'.$name.'" already exists');
					 } 
					}
			     if($stat)
			       {	   
		           $this->ItemsModel->changestatus_single_group($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	  
  public function modify_item($item_id){    
   	     $material_centre_dropdown = $this->ItemsModel->material_centre_dropdown();		 
		if(!$item_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
          //echo '<pre>';print_r($_POST); die();	 	
			$total_batch_balance=array();
		    $batch_total_difference=0;	
			$rules 	= [				
						'item_name' => [
							'label'  => 'Item Name',
							'rules'  => 'required',
							'rules'  => "required",
							'errors' => [
								'required' => 'Please enter item name',
							   ]]				  			   
					      ];			
            if(!$this->validate($rules)){
					$errors  = $this->validator->getErrors();
					$errors  = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
			  
            }else{		
                try{			
			     $request            = service('request');
			     $postData           = $request->getPost();
			     $item_name          = $postData['item_name']; 
				 $item_sku           = $postData['item_sku']; 
				 $item_alias         = $postData['item_alias'];
				 $item_printname     = $postData['item_printname'];
				 $item_shortname     = $item_name;
				 $item_group_id      = $postData['item_group_id']; 
				 $item_unit_id       = $postData['item_unit_id'];
				 $item_sales_acc     = $postData['item_sales_acc'];
				 $item_pur_acc       = $postData['item_pur_acc'];						
				 $item_catg_id       = $postData['item_catg_id'];
				 $item_qty_wise      = $postData['item_qty_wise'];
				 $unitdetail_array	 = $postData['unitdetail_array'];
				 $item_tax_cat_id    = $postData['tax_cat_mst_id'];
				 $item_hsn           = $postData['hsn'];
				 $update_data   = [
						'itm_name'          => clean($item_name),
						'itm_alias'         => clean($item_alias),
						'itm_print_name'    => clean($item_printname),
						'itm_upc'           => clean($item_shortname),
						'itm_sku'           => $item_sku,
						"tax_cat_mst_id"    => $item_tax_cat_id
					   ];	
					  
				   $response = $this->ItemsModel->update_item($update_data,$item_id);
				    if(!$response['status']){
    				    return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
    			     }
    			     else{
    			         
    			      $itmother_update_data  = array("itm_def_unit_id"=>$item_unit_id,"itm_cat_id"=>$item_catg_id,
				                            "itm_sales_acc_id"=>$item_sales_acc,
										    "itm_pur_acc_id"=>$item_pur_acc,"itm_hsn"=>$item_hsn
										  );
				      $this->ItemsModel->edit_itm_others($item_id,$itmother_update_data);
				   
					   $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>3,"crs_mst_id"=>$item_id,"under_crs_mst_id"=>$item_group_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				       $this->ItemsModel->update_undercrsmt($mst_update_data,$item_id,3);

						$mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>4,"crs_mst_id"=>$item_id,"under_crs_mst_id"=>$item_catg_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				       $this->ItemsModel->update_undercrsmt($mst_update_data,$item_id,4);
					   
					  
					   if ($item_qty_wise) {
						   $this->ItemsModel->clear_entries('itmoppybal',$item_id);
						   $this->ItemsModel->clear_entries('itmoppyval',$item_id);
						foreach ($item_qty_wise as $key => $value) {
							if (!empty($value['unit_id']) && !empty($value['mc'])) {
								$unit_id = $value['unit_id'];
								$itm_id_unit_id = $item_id . '_' . $unit_id;
								foreach ($value['mc'] as $key2 => $value2) {
									$op_bal_qty = max(floatval($value2['op_bal_qty']), 0);
									$mc_id = $value2['mc_id'];
									if($mc_id>0){
										// Update item quantity
										$bal_data = [
											'cmp_id'          => $this->company_id,
											'cmpfymastr_id'   => $this->fy_id,
											'itm_id_unit_id'  => $itm_id_unit_id,
											'itm_op_bal_qty'  => $op_bal_qty,
											'itm_py_bal_qty'  => 0,
											'mat_cent_id'     => $mc_id,
											'hobo_id'         => $this->bo_id
										   ];
										
										$this->ItemsModel->InsertOppBal($bal_data);
										$itm_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
										$txn_data = array("itm_txn_qty"=>$op_bal_qty);
										$this->ItemsModel->update_itm_txn_opbal_entry($txn_data,$itm_id_unit_id,1);
									
										$valuation_methods = [											
											'FIFO' => 'fifo',
											'LIFO' => 'lifo',
											'AVG' => 'avg',
										];

										foreach ($valuation_methods as $method_id => $val_key) {
											$op_bal_val = ($op_bal_qty > 0) ? floatval($value2[$val_key]) : 0;

											$val_data = [
												'cmp_id'            => $this->company_id,
												'cmpfymastr_id'     => $this->fy_id,
												'itm_id_unit_id'    => $itm_id_unit_id,
												'itm_op_val_amt'    => $op_bal_val,
												'itm_py_val_amt'    => 0,
												'itm_val_method_id' => $method_id,
												'mat_cent_id'       => $mc_id,
												'hobo_id'           => $this->bo_id
											];											
											$this->ItemsModel->InsertOppVal($val_data);
										}
									}
								}
							}
						}
					 }
					 
					 $items_list  = $this->ItemsModel->company_all_items();
				     CreateJsonFile($this->fy_id,$this->company_id,'itm',$items_list);
				   	 return json_encode (['status' => true, 'message' => 'Item updated successfully']);	 
					 }
				} catch (\Throwable $e) {
					helper('error');
					$error = formatDbException($e);
					return json_encode (['status' => false, 'message' => $error]);	 
					log_message('error', 'DB Error: ' . json_encode($error));
					return json_encode($error);
				} 
				
		      }		 
		}
		  $get_item_detail_info = $this->ItemsModel->get_item_detail_info($item_id,$this->company_id);
		  
		  /* echo '<pre>';
		  print_r($get_item_detail_info);
		  die(); */
		  if(!$get_item_detail_info)
		  	throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		 
		  	$data['GetOpnBalanItm']           = $this->ItemsModel->get_item_op_bal_list($item_id);
			
			$data['mc_list']                  = $this->ItemsModel->get_mc_list();
			
			$data['message_output']           = $this->message_output;
			$data['folder_path']              = $this->folder_path;	
			$data['item_info']                = $get_item_detail_info;	
			$data['item_group']               = $this->ItemsModel->items_group_dropdown();	
			$data['item_units']               = $this->ItemsModel->units_dropdown();		
			$data['item_category']            = $this->ItemsModel->category_dropdown();
			$data['material_centre_dropdown'] = $material_centre_dropdown;
			$data['base_url']                 = $this->base_url;	
			$data['item_id']                  = $item_id;		
			$data['sales_acc_dropdown']       = $this->ItemsModel->sales_acc_dropdown();
			$data['purchase_acc_dropdown']    = $this->ItemsModel->purchase_acc_dropdown();	
			$data['tax_category']             = $this->ItemsModel->tax_category_dropdown(1); 
			return view($this->folder_path.'items/edit_item',$data);		
    }		
	
  public function list_group()    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;	
        $data['base_url']        = $this->base_url;
        $data['session']         = $this->session;
	    $data['groups_list']     = $this->ItemsModel->ajax_group_list();	        		
		return view($this->folder_path.'items/list_group',$data);		
    } 
   
   public function add_group()    {
		 if($this->request->getMethod() == 'POST'){	
		    $group_name        = $this->request->getVar('group_name'); 
		    $group_name_alias  = $this->request->getVar('group_name_alias');
			$primary_group     = $this->request->getVar('primary_group');	
			
		   	if($primary_group=='Y'){
			   $crs_mst_is_primary=1;	
			   $acc_grp_parent_id    = 0;
			   $under_acc_grp_id     = 0;
		   	}
			elseif($primary_group=='N'){
				$crs_mst_is_primary=0; 
			    $under_acc_grp_id    = $this->request->getVar('no_group_under');
			    $acc_grp_parent_id   =0;
			}
			
			$rules = [				
				    'group_name' => [
					'label'  => 'Group Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter group name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
				    $errors = $this->validator->getErrors();
					$errors  =array_values($errors_list);
	        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
			    try{			   
				    $insert_data   = [
						'cmp_id'            => $this->company_id,
						'itm_grp_name'      => clean($group_name),
						'itm_grp_alias'     => clean($group_name_alias)						
					   ];				
					$group_id = $this->ItemsModel->add_group($insert_data);
				    if($group_id=="-1"){
					  return json_encode(['status'=>false,'message'=>'Validation Error','errors'=>['Group name already exists.']]);				
				    }
				    else if($group_id=="-2"){
					  return json_encode(['status'=> false,'message'=>'Validation Error','errors'=>['Group alias already exists.']]);				
				    }
					else{				 
							$mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>6,"crs_mst_id"=>$group_id,
							                          "under_crs_mst_id"=>$under_acc_grp_id,"crs_mst_parent_id"=>0,"under_main_id"=>0,
												      "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary
													  );
							$this->ItemsModel->add_undercrsmt($mst_insert_data);
							
						    $log = [
								'uuid_aicountly' => $this->session->get('uuid_aicountly'),
								'log_date' => date('Y-m-d'),
								'log_time' => date('H:i:s'),
								'log_action_tags' => 'add',
								'log_field_id' => $group_id,
								'log_field_name' => $group_name,
								'log_field_type' => 'item group',
							];
						   //$this->LogModel->add_log($log);				    
						  return json_encode(['status' => true, 'message' => 'Data Added']);	
					    }	
				}
				 catch (\Throwable $e) {
						log_message('error', 'DB Query Error: ' . $e->getMessage());
						return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
					}	
				  
		    	}					 									
		   }			
		$data['message_output']           = $this->message_output;
		$data['folder_path']              = $this->folder_path;	
		$data['base_url']                 = $this->base_url;	
	    $data['user_groups_dropdown']     = $this->ItemsModel->group_main_dropdown();
		
	    return view($this->folder_path.'items/add_group',$data);		
    }
   
   public function modify_group($group_id)    {
		if(!$group_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 

		 if($this->request->getMethod() == 'POST'){	
		    $group_name       = $this->request->getVar('group_name'); 
		    $group_name_alias = $this->request->getVar('group_name_alias');
			$primary_group    = $this->request->getVar('primary_group');
			$rules = [				
				'group_name' => [
					'label'  => 'Group Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter group name',
					   ]]				  			   
			       ];
			
            if(!$this->validate($rules)){
                $errors  = $this->validator->getErrors();
				$errors  = array_values($errors_list);
				return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);	
            }else{	
			   try{
                   if($primary_group=='Y'){
					  $crs_mst_is_primary   =1; 	
        			
        			  $under_acc_grp_id     = 0;
        		   	}
        			elseif($primary_group=='N'){
						$crs_mst_is_primary  = 0; 
        			    $under_acc_grp_id    = $this->request->getVar('no_group_under');
        			   
        			}
				    $update_data   = [
				        'itm_grp_name'    => clean($group_name),
						'itm_grp_alias'   => clean($group_name_alias)
					  ];
			        $response = $this->ItemsModel->update_group($update_data,$group_id);
					if(!$response['status']){        				
					 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				    }
					else{
						
						$old_group_data = $this->ItemsModel->item_group_info($group_id);
						if($primary_group=='Y'){
								$crs_mst_is_primary   = 1;
								$acc_grp_parent_id    = 0;
								
						}
						else{
								$crs_mst_is_primary=0;
								$no_group_under    = $this->request->getVar('no_group_under');
								$main_group_info     = $this->ItemsModel->item_group_info($no_group_under);
								
								if($main_group_info){
								  $acc_grp_parent_id   = $main_group_info['crs_mst_parent_id'];
								}
						}
					  // if group Under is changed then auto change all the masters 	
				      $this->ItemsModel->updateGroupParent($old_group_data['crs_mst_parent_id'], $acc_grp_parent_id);
					
						
			         $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>6,"crs_mst_id"=>$group_id,
							                   "under_crs_mst_id"=>$under_acc_grp_id,"crs_mst_parent_id"=>$acc_grp_parent_id,"under_main_id"=>0,
											   "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary
													  );
					 $this->ItemsModel->update_undercrsmt($mst_update_data,$group_id,6);
				   
			         $log = [
				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
				            'log_date' => date('Y-m-d'),
				            'log_time' => date('H:i:s'),
				            'log_action_tags' => 'update',
				            'log_field_id' => $group_id,
				            'log_field_name' => $group_name,
				            'log_field_type' => 'item group',
				            ];
				     //$this->LogModel->add_log($log);				    
					 return json_encode(['status' => true, 'message' => 'Data Updated']);
					}
			    }
			     catch (\Throwable $e) {
						log_message('error', 'DB Query Error: ' . $e->getMessage());
						return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
					}	
					
		    	}					 									
		   }	 


		 $item_group_info = $this->ItemsModel->item_group_info($group_id);
		 if(!$item_group_info)
		 		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		   
		$data['get_info']         = $item_group_info;
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['group_id']         = $group_id;		
        $data['base_url']         = $this->base_url;	
        $data['user_groups_dropdown']     = $this->ItemsModel->group_main_dropdown($group_id);
	    return view($this->folder_path.'items/edit_group',$data);		
    }
   
   public function remove_groups($ids_info){
		if(!$ids_info)
			return redirect()->to($this->base_url.'items/list_group'); 
		 
		 $default_groups = [];
		 $errors = [];
		 $ids = urlSafeBase64Decode($ids_info); 
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $item_id){
		     
		    $stat = true;
		    $name = $this->ItemsModel->get_item_group_name($item_id);;
		     if (in_array($item_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Item Group "'.$name.'" belongs to Default Group');
		     }
		     if ($this->ItemsModel->check_item_with_group($item_id)){ //check if item exist with that group
		         $stat = false;
		         array_push($errors, 'Failed! Item Group "'.$name.'" has one or more associated items');
		     }		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'item group',
    		            ];
    		    // $this->LogModel->add_log($log);    		     
    		     $this->ItemsModel->remove_single_groups($item_id);
		     }
		 }		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);		
	  }   
   public function stock_category()    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;		
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['category_list']   = $this->ItemsModel->ajax_category_list();
		return view($this->folder_path.'items/list_category',$data);		
    } 
      
   public function add_category()    {
		 if($this->request->getMethod() == 'POST'){	
		    $item_cat = $this->request->getVar('item_cat'); 
		    $item_cat_alias = $this->request->getVar('item_cat_alias'); 
			$rules = [				
				    'item_cat' => [
					'label'  => 'Category Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter category name',
					     ]
					   ]	   
			       ];
				   
            if(!$this->validate($rules)){
					$errors = $this->validator->getErrors();
					$errors  =array_values($errors_list);
	        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{	
				try{	
				 $insert_data   = [
						'cmp_id'         => $this->company_id,
						'itm_cat_name'   => clean($item_cat),
						'itm_cat_alias'  => clean($item_cat_alias),
						'itm_cat_is_active' =>1
					   ];				
			     $item_cat_id = $this->ItemsModel->add_category($insert_data);
			     if($item_cat_id=="-1"){
					  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Category name already exists.']]);
				   }
				   else if($item_cat_id=="-2"){
					  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Category alias already exists.']]);
				   }
					else{
					    $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>4,"crs_mst_id"=>$item_cat_id,"under_crs_mst_id"=>0,
											  "crs_mst_parent_id"=>0,"under_main_id"=>0,
											  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
						$this->ItemsModel->add_undercrsmt($mst_insert_data);
							
				 	    $log = [
            		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
            		            'log_date' => date('Y-m-d'),
            		            'log_time' => date('H:i:s'),
            		            'log_action_tags' => 'add',
            		            'log_field_id' => $item_cat_id,
            		            'log_field_name' => $this->ItemsModel->get_item_category_name($item_cat_id),
            		            'log_field_type' => 'stock category',
            		            ];
            		     //$this->LogModel->add_log($log);
					     return json_encode(['status' => true, 'message' => 'Data Inserted']);	
				      }
				}
				catch (\Throwable $e) {
						log_message('error', 'DB Query Error: ' . $e->getMessage());
						return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
					}	
				   
		    	}					 									
		   }							 
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url;
		$data['folder_path']      = $this->folder_path;	
		return view($this->folder_path.'items/add_category',$data);		
    }
	
	public function category_change_status(){
   	     $status_ids    = $this->request->getVar('pss_status_val'); 
	 	 $ids_info      = $this->request->getVar('accidids');		
		 $ids           = base64_decode($ids_info);  		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 $ids2  = array_unique($ids2);
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat = true;
		     $name = $this->ItemsModel->get_item_category_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					 if ($this->ItemsModel->check_item_category_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Category "'.$name.'" already exists');
					 } 
					}
			     if($stat)
			       {	   
		           $this->ItemsModel->changestatus_single_category($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	  
	 public function remove_catgeory($ids_info){
		if(!$ids_info)
			return redirect()->to($this->base_url.'items/stock_category');
			
		 $default_groups = [];
		 $errors = [];
		 $ids    = urlSafeBase64Decode($ids_info); 
		 $ids2   = explode(",",$ids);
		 foreach($ids2 as $item_cat_id){		     
		    $stat = true;
		    $name = $this->ItemsModel->get_item_category_name($item_cat_id);		    
		     if (in_array($item_cat_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Item Category "'.$name.'" belongs to Default Categories');
		     }
		     if ($this->ItemsModel->check_item_with_category($item_cat_id)){ //check if account exist with that group
		         $stat = false;
		         array_push($errors, 'Failed! Item Category "'.$name.'" has one or more associated items');
		     }		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_cat_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'stock category',
    		            ];
    		     //$this->LogModel->add_log($log);    		     
    		     $this->ItemsModel->remove_single_category($item_cat_id);				 
		     }		     
		 }
		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);		
	  } 
	  
	
	public function modify_category($category_id)    {
		 if($this->request->getMethod() == 'POST'){	
		    $item_cat        = $this->request->getVar('item_cat'); 
		    $item_cat_alias  = $this->request->getVar('item_cat_alias'); 
			$rules = [				
				    'item_cat' => [
					'label'  => 'Category Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter category name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
				   $errors = $this->validator->getErrors();
					$errors  =array_values($errors_list);
	        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }
			else{			
				try{	
				 $update_data   = [
						'itm_cat_name'   => clean($item_cat),
						'itm_cat_alias'  => clean($item_cat_alias)
					   ];				
				 $response = $this->ItemsModel->modify_category($update_data,$category_id);
				 if(!$response['status']){        				
					return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				}
				else{
					  $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_id"=>$category_id,"crs_mst_type"=>8,"under_crs_mst_id"=>0,
												"crs_mst_parent_id"=>0,"under_main_id"=>0,
												"cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
					  $this->ItemsModel->update_undercrsmt($mst_update_data,$category_id,4);
					
					  $log = [
							 'uuid_aicountly' => $this->session->get('uuid_aicountly'),
							'log_date' => date('Y-m-d'),
							'log_time' => date('H:i:s'),
							'log_action_tags' => 'update',
							'log_field_id' => $category_id,
							'log_field_name' => $this->ItemsModel->get_item_category_name($category_id),
							'log_field_type' => 'stock category',
							];
					  //  $this->LogModel->add_log($log);    		     
					  return json_encode(['status' => true, 'message' => 'Data Updated']);
				    }
				}
				catch (\Throwable $e) {
						log_message('error', 'DB Query Error: ' . $e->getMessage());
						return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
					}	  				 
		    	}					 									
		   }			
				 
		   $item_category_info = $this->ItemsModel->item_category_info($category_id);
		   if(!$item_category_info)
		   		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;	
		$data['base_url']         = $this->base_url;   
        $data['category_info']    = $item_category_info;	
		$data['category_id']      = $category_id;	        
	    return view($this->folder_path.'items/edit_category',$data);		
    }
}