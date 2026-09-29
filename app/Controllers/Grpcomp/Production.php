<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ProductionModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;


class Production extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->ProductionModel = new ProductionModel();	
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    = new enc_string();
    }
  
   
   public function ajax_post_grid(){
	  
	    if($this->request->getMethod() == 'post'){
	    	$rules = [				
						'bom_id' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'BOM is required',
						   ],
					  	],

					 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
		     $bom_id         = $this->request->getVar('bom_id');
			 $tbatch         = $this->request->getVar('tbatch'); 
			 $pstaction      = $this->request->getVar('pstaction');		
			  
			 if($pstaction=='add'){
			  if($bom_id){
				 $bom_info   = $this->ProductionModel->bom_info($bom_id);
				 if($bom_info){
					 $bom_id_val         = $bom_info['bom_id'];
					 $voucher_id         = "14";//Production voucher type
					 $item_consumed       = json_decode($this->request->getVar('itemconsumed_grid'),true); 
					 $item_produced       = json_decode($this->request->getVar('itemproduced_grid'),true);  
					 $byproducts_produced = json_decode($this->request->getVar('byproductproduced_grid'),true);  
					 $additional_cost     = json_decode($this->request->getVar('additionalcost_grid'),true); 
					 
					 $matrcntr_id         = $this->request->getVar('matrcntr_id');
					 $voucher_series      = $this->request->getVar('voucher_series');
					 $production_date     = date('Y-m-d',strtotime($this->request->getVar('production_date')));
					 
					 $voucher_no          = $this->ProductionModel->get_voucher_no($voucher_id);
					 
					  $voucher_txn_data   = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"comp_vch_no"=>$voucher_no,"voucher_type_id"=>$voucher_id,
											   "voucher_date"=>$production_date,"mat_cent_id"=>$matrcntr_id);
					 $voucher_txn_id      = $this->ProductionModel->add_voucher_cons_data($voucher_txn_data);
					  
					 
					 $this->ProductionModel->save_data($tbatch,$bom_id_val,$voucher_id,$voucher_txn_id,$matrcntr_id,$voucher_series,$production_date,$item_consumed,$item_produced,$byproducts_produced,$additional_cost);
					
					return json_encode(['status' => true, 'message' => 'Voucher Inserted']); 
					 
				 }
				 else{					 
					 return json_encode(['status' => false, 'message' => 'Something went wrong']);
				 }
				 
			 }else{					 
					 return json_encode(['status' => false, 'message' => 'Something went wrong']);
				 }	 
			 }
			 else if($pstaction=='edit'){

			 		$rules = [				
						'bom_id' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'BOM is required',
						   ],
					  	],

					 ];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

				 $voucher_txn_id         = $this->request->getVar('vxn_id');
				 if($bom_id){
				 $bom_info   = $this->ProductionModel->bom_info($bom_id);
			 
				 if($bom_info){
					 $bom_id_val         = $bom_info['bom_id'];
					 $voucher_id         = "14";//Production voucher type
					 $item_consumed       = json_decode($this->request->getVar('itemconsumed_grid'),true); 
					 $item_produced       = json_decode($this->request->getVar('itemproduced_grid'),true);  
					 $byproducts_produced = json_decode($this->request->getVar('byproductproduced_grid'),true);  
					 $additional_cost     = json_decode($this->request->getVar('additionalcost_grid'),true); 
					 
					 $matrcntr_id         = $this->request->getVar('matrcntr_id');
					 $voucher_series      = $this->request->getVar('voucher_series');
					 $production_date     = date('Y-m-d',strtotime($this->request->getVar('production_date')));
					
					  $this->ProductionModel->delete_accttxnoth($voucher_txn_id); 
					 // get voucher old entries item and account
					 
					 $old_entries = $this->ProductionModel->ajax_vouchers_transactions_list($voucher_txn_id );
					 
					 if($old_entries){
						 foreach($old_entries as $oldrow){
							 if($oldrow['master_id_type']=='itm'){
								 //delete item txn table entries
								 $this->ProductionModel->delete_itemtxnnnn($voucher_txn_id,$oldrow['master_id']);
								 $this->ProductionModel->delete_itemtxnbal($voucher_txn_id,$oldrow['master_id']); 
							 }  
						 }
					 }
					 //delete comptxn entries
                     $this->ProductionModel->delete_comp_txn($voucher_txn_id);
					
					 $voucher_txn_data   = array("comp_vch_series_id"=>$voucher_series,"voucher_date"=>$production_date,"mat_cent_id"=>$matrcntr_id);
					 $this->ProductionModel->update_voucher_cons_data($voucher_txn_data,$voucher_txn_id,$this->company_id);
					
					 $this->ProductionModel->save_data($tbatch,$bom_id_val,$voucher_id,$voucher_txn_id,$matrcntr_id,$voucher_series,$production_date,$item_consumed,$item_produced,$byproducts_produced,$additional_cost);
					
					return json_encode(['status' => true, 'message' => 'Voucher Updated']);
					 
				 }
				 else{					 
					 return json_encode(['status' => false, 'message' => 'Something went wrong']);
				 }
				 
			 }else{					 
					 return json_encode(['status' => false, 'message' => 'Something went wrong']); 
				 }
			 }
			 		    
		}
   }	

   
   public function add()
    {
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_id         = "14";//Production voucher type
		if(isset($_GET['bom_id']) && $_GET['bom_id']!='' && $_GET['total_batches']!=''){		
		$data['bom_info']  = $this->ProductionModel->billofmaterial_info($_GET['bom_id'],$_GET['total_batches']);
		$bom_id            = $_GET['bom_id'];
		$total_batches     = $_GET['total_batches'];
		}
		else{
		$data['bom_info'] = array('item_consumed_unit_labels'=>[],'item_produced_unit_labels'=>[],'byproduct_produced_unit_labels'=>[]);
		$total_batches     = '1';		
		$bom_id           ='';
		}
		$data['bom_id']   = $bom_id;
		$data['total_batches']   = $total_batches;
		
		$voucher_detail             = $this->ProductionModel->get_voucher_info($voucher_id,$this->company_id);
		$data['voucher_date']       = $voucher_detail['last_entry'];
		$data['message_output']     = $this->message_output;
		$data['base_url']           = $this->base_url;	
		$data['folder_path']        = $this->folder_path;	
        $data['voucher_auto_no']    = $this->ProductionModel->get_voucher_no($voucher_id);
		$data['bom_dropdown']       = $this->ProductionModel->bom_dropdown();
		$data['voucher_id']         = $voucher_id;
		$data['matrcntr_dropdown']  = $this->ProductionModel->matrcntr_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->ProductionModel->comp_voucher_series($this->company_id,$voucher_id);
		$data['expense_heads_list']       = $this->ProductionModel->expense_heads_dropdown($this->company_id);
	    $data['units_list']               = $this->ProductionModel->units_dropdown($this->company_id);
		$data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
		$data['voucher_type_id'] = $voucher_id;
		return view($this->folder_path.'production/add',$data);		
    }	
  
   public function modify($voucher_txn_id)
    {
		if(!$voucher_txn_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		else if(!isset($_GET['bom_id']) && $_GET['bom_id']=='')
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_id         = "14";//Production voucher type
		
		if(isset($_GET['total_batches'])){
			$total_batches = $_GET['total_batches'];
			
		}else
			$total_batches=1;
			
		
		if(isset($_GET['bom_id']) && $_GET['bom_id']!=''){		
		  $bom_id            = $_GET['bom_id'];
		  $data['bom_info']  =  $this->ProductionModel->saved_entries_production($bom_id,$voucher_txn_id,$total_batches);
		  
		}
		else{
		  $data['bom_info'] = array('item_consumed_unit_labels'=>[],'item_produced_unit_labels'=>[],'byproduct_produced_unit_labels'=>[]);
		
		  $bom_id           ='';
		}
		$data['bom_id']   = $bom_id;
		$data['total_batches']   = $total_batches;
		
		
		$data['message_output']     = $this->message_output;
		$data['base_url']           = $this->base_url;	
		$data['folder_path']        = $this->folder_path;	
        $data['voucher_auto_no']    = $this->ProductionModel->get_voucher_no($voucher_id);
		$data['bom_dropdown']       = $this->ProductionModel->bom_dropdown();
		$data['voucher_id']         = $voucher_id;
		$data['matrcntr_dropdown']  = $this->ProductionModel->matrcntr_dropdown($this->company_id);
		$data['voucher_series_dropdown']  = $this->ProductionModel->comp_voucher_series($this->company_id,$voucher_id);
		$data['expense_heads_list']       = $this->ProductionModel->expense_heads_dropdown($this->company_id);
	    $data['units_list']               = $this->ProductionModel->units_dropdown($this->company_id);
		$data['voucher_txn_id']           = $voucher_txn_id;
		$data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
		$data['voucher_type_id'] = $voucher_id;
		return view($this->folder_path.'production/edit',$data);			
    }	
}