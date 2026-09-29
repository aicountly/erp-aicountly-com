<?php
namespace App\Controllers\Admin;
use App\Models\Admin\VouchersModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Traits\TransactionTrait;
class Stock_journal  extends BaseController
{
  use TransactionTrait;	
  function __construct()
  {   
	    helper(['form', 'url','text']);
			$this->VouchersModel  = new VouchersModel();		
			$this->auth_session  = new auth_session();			
			$this->auth_session->user_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      =  base_url().'/'.getenv('AdminPath');
			$this->folder_path   =  getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->auth_session->is_company_opened();
			$this->comp_code     =  $this->session->get('ses_company_code');
		    $this->bo_id         = $this->session->get('ses_boid');
        	$this->fy_id         = $this->session->get('ses_comp_fy_id');
	        $this->company_id    = $this->session->get('ses_company_id');
		
   }
   
   public function invoice($vch_subtype_id) {
        $voucher_type_id       = 20;		
	  	$voucher_subtype_array = [18,19,20];
		$taxes_list            = $this->VouchersModel->GetGSTTaxesList();
	    if(!in_array($vch_subtype_id, $voucher_subtype_array)){
	        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
	    }			
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
			  // echo "<pre>";print_r($_POST);exit;
				$rules = [				
					'voucher_date' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Voucher Date is required',
					   ],
					],
					'voucher_series' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Voucher Series is required',
					  ],
				  ],
			
					'matrcntr_id' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Material Center is required'
					  ],
					],
					'item_data_from' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Data From is required'
					  ],
				  ],
				  'item_data_to' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Data To is required'
					  ],
				  ],

				 ];
			
		if(!$this->validate($rules)){
			$errors = $this->validator->getErrors();
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		}		
	   $trans_result = $this->runTransaction(function($db) use ($vch_subtype_id,$voucher_type_id)
	    {
	   try{				
		 $voucher_date     = $this->request->getVar('voucher_date');
		 $voucher_series   = $this->request->getVar('voucher_series'); 
		 $voucher_no       = $this->VouchersModel->get_voucher_no($voucher_type_id);
		 $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		 $long_narration   = $this->request->getVar('narration'); 
		 $item_data_from   = json_decode($this->request->getVar('item_data_from'),true); 
		 $item_data_to     = json_decode($this->request->getVar('item_data_to'),true);		   
		 $voucher_date     = validate_date_by_fy($voucher_date);
		 $currency_id      = $this->request->getVar('currency_id') ?? 1;
		 $oCheck           = $this->request->getVar('oCheck') ?? 0;
		 $fcy_forex_rate   = $this->request->getVar('fcy_forex_rate') ?? 0;
		 $btnid            = $this->request->getVar('btnid');// submitbtn default save button
		 
		 $from_batchdata = [];
		  if(!empty($this->request->getVar('frombatchdata')))
			$from_batchdata = json_decode($this->request->getVar('frombatchdata'),true);
		
		 $to_batchdata = [];
		  if(!empty($this->request->getVar('tobatchdata')))
			$to_batchdata = json_decode($this->request->getVar('tobatchdata'),true);
		
		$is_batch=FALSE;
		 if($from_batchdata)
		 $is_batch=TRUE;
	 
	 
		 /*********  Save as Drasft *****************/
		 if($oCheck >0)
			$isoptional =TRUE;
         else  
			$isoptional =FALSE; 
		
		// Mark Dirty Transactions
		    $voucherDate = new \DateTime($voucher_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				 $this->VouchersModel->markSnapshotDirty($voucher_date);
			 }
		 
		 $voucherdata     = json_encode(["is_batch"=>$is_batch,"from_batchdata"=>$from_batchdata,"to_batchdata"=>$to_batchdata,"mat_cent_id"=>$matrcntr_id,"item_data_from"=>$item_data_from,"item_data_to"=>$item_data_to]);
		 $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount(0),"vch_fcy_amt"=>parseAmount(0),"vch_fcy_rate"=>parseAmount(0),
								 "vch_particulars"=>"N/A","uuid_aictly"=>$this->session->get('uuid'),
								 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>$isoptional,
								 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>0,"is_pr"=>0,"is_sblgr"=>0
					            );
	    $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
	    if($btnid=='submitbtn_drft'){
		  return ['status' => true, 'message' => 'Voucher saved in draft mode']; 
	     }	    
		$insert_data  = array(
			  "cmp_id"           => $this->company_id,		  
			  "vch_series_id"    => $voucher_series,          
			  "vch_type_id"      => $voucher_type_id,
			  "vch_sub_type_id"  => $vch_subtype_id,		   
			  "vch_date"         => $voucher_date,
			  "mat_cent_id"      => $matrcntr_id,
			  "draft_vch_rec_id" => $draft_vch_rec_id
			 );		
        $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data);
		if(parseAmount($fcy_forex_rate) >0){
		    $fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	 
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
		}
		
		if($oCheck >0)
		 $itm_txn_type = 2; //  2 for Optional 
		else
		 $itm_txn_type = 1;	//1 for Regular 
		foreach($item_data_from as $item_row){
			$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
			$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 2,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			 $this->VouchersModel->add_itm_txn_data($insert_data);
			 
			 $register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
								  'mat_cent_id'       => $matrcntr_id,
                				  'itm_txn_dr_qty'    => 0,
                				  'itm_txn_dr_rate'   => 0,
                				  'itm_txn_dr_amt'    => 0,
                				  'itm_txn_cr_qty'    => $item_row['item_qty'],				 
                				  'itm_txn_cr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_cr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);
			/***********   Save Item Batch  Data  *************/
			 if(count($from_batchdata)){		
			 $this->VouchersModel->SaveBatchData($from_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
			} 	
		} //for loop

		foreach($item_data_to as $item_row){
			$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
			$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 1,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			$this->VouchersModel->add_itm_txn_data($insert_data);
			
			$register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
								  'mat_cent_id'       => $matrcntr_id,
                				  'itm_txn_dr_qty'    => $item_row['item_qty'],
                				  'itm_txn_dr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_dr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'itm_txn_cr_qty'    => 0,				 
                				  'itm_txn_cr_rate'   => 0,
                				  'itm_txn_cr_amt'    => 0,
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);
		
			/***********   Save Item Batch  Data  *************/
			if(count($to_batchdata)){		
			 $this->VouchersModel->SaveBatchData($to_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
			}
		} 

		/******************* Start of Save Voucher Naration  ****************/
			 $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
			/******************* End of Save Voucher Naration  ****************/
			
			
			$this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
		    $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
			return ['status' => true, 'message' => 'Voucher Inserted'];
				
			}
			catch (\Throwable $e) {
				return  ['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
				// Get line, file and stack trace
                log_message('error', 'DB Error: ' . $e->getMessage());
                log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
    		 }
		});
		if(isset($trans_result['result']['status']) && $trans_result['result']['status']=='')
          	return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message']]);
       else
         return $this->response->setJSON($trans_result);		
	   }
		$voucher_name = '';
		if($vch_subtype_id == 18)
			$voucher_name = 'Stock Journal';
		if($vch_subtype_id == 19)
			$voucher_name = 'Pack/ Assemble';
		if($vch_subtype_id == 20)
			$voucher_name = 'Unpack/ Unassemble';
		
		$data['message_output']        = $this->message_output;
		$data['method_list']           = ['','New Ref.','Adjustment'];
		$data['taxes_list']            = $taxes_list;
		$data['base_url']              = $this->base_url; 
		$data['voucher_auto_no']       = $this->VouchersModel->get_voucher_no($voucher_type_id);
		$data['folder_path']           = $this->folder_path;	
		$data['units_list']     	   = $this->VouchersModel->units_grid();
		$data['voucher_series']        = $this->VouchersModel->comp_voucher_result($this->company_id,$voucher_type_id);		
		$data['matrcntr_dropdown']     = $this->VouchersModel->material_centre_dropdown();
		$data['voucher_date']          = $this->VouchersModel->getLastVoucherDate($voucher_type_id);
		$items_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'itm');		
		$data['item_json_file']        = $items_list; 
		$data['voucher_type_id'] 	   = $voucher_type_id;
		$data['vch_subtype_id'] 	   = $vch_subtype_id;
		$data['voucher_name'] 		   = $voucher_name;
		$data['currency_list']         = $this->VouchersModel->get_currency_list();
		return view($this->folder_path.'stock_journal/invoice',$data);	
   }

   public function edit($voucher_txn_id) {
	  	$taxes_list         = $this->VouchersModel->GetGSTTaxesList();
		$voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
		if(empty($voucher_info)){
		  throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
	    $voucher_type_id   = $voucher_info['vch_type_id'];
	    $vch_subtype_id    = $voucher_info['vch_sub_type_id'];
	    $voucher_ed_date   = $voucher_info['vch_date'];		
		$data['draft_vch_rec_id'] = 0;
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		// echo "<pre>";print_r($_POST);exit;		    
		$rules = [				
					'voucher_date' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Voucher Date is required',
					   ],
					],
					'voucher_series' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Voucher Series is required',
					  ],
				  ],
			
					'matrcntr_id' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Material Center is required'
					  ],
					],
					'item_data_from' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Data From is required'
					  ],
				  ],
				  'item_data_to' => [
						'rules'  => 'required',
						'errors' => [
							'required' => 'Data To is required'
					  ],
				  ],

				 ];			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
	$trans_result = $this->runTransaction(function($db) use ($voucher_txn_id,$voucher_type_id,$voucher_ed_date){  
	  try{	
		 $voucher_date     = $this->request->getVar('voucher_date');
		 $voucher_series   = $this->request->getVar('voucher_series'); 
		 $voucher_no       = $this->VouchersModel->get_voucher_no($voucher_type_id);
		 $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		 $long_narration   = $this->request->getVar('narration'); 
		 $item_data_from   = json_decode($this->request->getVar('item_data_from'),true); 
		 $item_data_to     = json_decode($this->request->getVar('item_data_to'),true);		   
		 $voucher_date     = validate_date_by_fy($voucher_date);
		 $currency_id      = $this->request->getVar('currency_id') ?? 1;
		 $oCheck           = $this->request->getVar('oCheck') ?? 0;
		 $fcy_forex_rate   = $this->request->getVar('fcy_forex_rate') ?? 0;
		 $btnid            = $this->request->getVar('btnid');// submitbtn default save button
		 $draft_vch_rec_id = $this->request->getVar('draft_vch_rec_id'); 
		 
		 // Mark Dirty Transactions
		    $voucherDate = new \DateTime($voucher_ed_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				 $this->VouchersModel->markSnapshotDirty($voucher_ed_date);
			 }
			
		 $from_batchdata = [];
		  if(!empty($this->request->getVar('frombatchdata')))
			$from_batchdata = json_decode($this->request->getVar('frombatchdata'),true);
		
		 $to_batchdata = [];
		  if(!empty($this->request->getVar('tobatchdata')))
			$to_batchdata = json_decode($this->request->getVar('tobatchdata'),true);
		
		$is_batch=FALSE;
		 if($from_batchdata)
		 $is_batch=TRUE;
	 
		 /*********  Save as Drasft *****************/
		 if($oCheck >0)
			$isoptional = TRUE;
         else  
			$isoptional = FALSE; 
		
		  $voucherdata     = json_encode(["is_batch"=>$is_batch,"from_batchdata"=>$from_batchdata,"to_batchdata"=>$to_batchdata,"mat_cent_id"=>$matrcntr_id,"item_data_from"=>$item_data_from,"item_data_to"=>$item_data_to]);
		 
			if($draft_vch_rec_id >0){
			 $pgrdata_drft    = array("log_date_time"=>$voucher_date,
									 "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount(0),"vch_fcy_amt"=>parseAmount(0),"vch_fcy_rate"=>parseAmount(0),
									 "vch_particulars"=>"N/A",
									 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>$isoptional,
									 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>0,"is_pr"=>0,"is_sblgr"=>0
									);
			$draft_vch_rec_id = $this->VouchersModel->UpdateDrftVoucher($draft_vch_rec_id,$pgrdata_drft);	
			}
			else{
			 $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
									 "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
									 "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount(0),"vch_fcy_amt"=>parseAmount(0),"vch_fcy_rate"=>parseAmount(0),
									 "vch_particulars"=>"N/A","uuid_aictly"=>$this->session->get('uuid'),
									 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>$isoptional,
									 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>0,"is_pr"=>0,"is_sblgr"=>0
									);
			$draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
			
			}
			 if($btnid=='submitbtn_drft'){
			  return ['status' => true, 'message' => 'Voucher saved in draft mode']; 
			 }	
			/* Remove Old Entries using vch_txn_id */
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');	
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itemtxnmst');
           $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
    	   $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_type_id);
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'billtxnmst');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cctxnmstnn');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'prjtxnmstn');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'subacctxnm');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchfcyrate');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchgstfcyn');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchgstsumn');  
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'gstroutsup');  
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'acctamtinc');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'batchtxnmt'); 
           
		   $update_data  = array(
			   "vch_series_id"    => $voucher_series,
			   "vch_date"         => $voucher_date,
			   "mat_cent_id"      => $matrcntr_id,
			   "draft_vch_rec_id" => $draft_vch_rec_id		  
				);
          $this->VouchersModel->update_voucher_cons_data($update_data,$voucher_txn_id,$this->company_id);
		  if(parseAmount($fcy_forex_rate) >0){
			$fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
			} else{				
			$this->VouchersModel->clear_voucher_fcyrate($voucher_txn_id);
			} 	
		   if($oCheck >0)
		   $itm_txn_type = 2; //  2 for Optional 
		  else
		   $itm_txn_type = 1;	//1 for Regular 
		
		foreach($item_data_from as $item_row){
			$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
			$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 2,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			 $this->VouchersModel->add_itm_txn_data($insert_data);
			 
			 $register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
                				  'itm_txn_dr_qty'    => 0,
                				  'itm_txn_dr_rate'   => 0,
                				  'itm_txn_dr_amt'    => 0,
                				  'itm_txn_cr_qty'    => $item_row['item_qty'],				 
                				  'itm_txn_cr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_cr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);
			/***********   Save Item Batch  Data  *************/
			 if(count($from_batchdata)){		
			 $this->VouchersModel->SaveBatchData($from_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
			} 	
		} //for loop

		foreach($item_data_to as $item_row){
			$insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
			$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 1,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => 0,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			$this->VouchersModel->add_itm_txn_data($insert_data);
			
			$register_insert_data  = [
                				  'itm_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'itm_id_unit_id'    => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                				  'vch_narr'          => $long_narration ?? '',
                				  'hobo_id'           => $this->bo_id,
                				  'itm_txn_type'      => $itm_txn_type
                				]; 								
            $itm_vch_reg_id =  $this->VouchersModel->add_item_register_txn_data($register_insert_data);	
			/************  Insert Register Data In Sub Table Also ************/
			$register_sub_insert_data  = [
                				  'itm_vch_reg_id'    => $itm_vch_reg_id,
                				  'itm_txn_dr_qty'    => $item_row['item_qty'],
                				  'itm_txn_dr_rate'   => parseAmountPrice($item_row['item_price'],4),
                				  'itm_txn_dr_amt'    => parseAmount($item_row['item_total_amount']),
                				  'itm_txn_cr_qty'    => 0,				 
                				  'itm_txn_cr_rate'   => 0,
                				  'itm_txn_cr_amt'    => 0,
                				  'vch_narr'          => $long_narration ?? '',              				 
                				]; 								
            $this->VouchersModel->add_item_sub_register_txn_data($register_sub_insert_data);
		
			/***********   Save Item Batch  Data  *************/
			 if(count($from_batchdata)){		
			 $this->VouchersModel->SaveBatchData($to_batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
			} 	
		} 

		/******************* Start of Save Voucher Naration  ****************/
			 $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);
			
			$this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
		    $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
			
			/******************* End of Save Voucher Naration  ****************/
		   return ['status' => true, 'message' => 'Voucher Updated'];
		}
		catch (\Throwable $e) {		      
		        log_message('error', 'DB Error: ' . $e->getMessage());
                log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
				return  ['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
		  }   
	});
	if(isset($trans_result['result']['status']) && $trans_result['result']['status']=='')
      return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message']]);
    else
         return $this->response->setJSON($trans_result);	
	}

		$voucher_name = '';
		if($vch_subtype_id == 18)
			$voucher_name = 'Stock Journal';
		if($vch_subtype_id == 19)
			$voucher_name = 'Pack/ Assemble';
		if($vch_subtype_id == 20)
			$voucher_name = 'Unpack/ Unassemble';
		
		 $from_transactions= [];
	     $to_transactions  = [];
		$transactions = $this->VouchersModel->grid_item_transactions($voucher_txn_id);
		foreach ($transactions as $key => $value) {
		    if($value['item_txn_drcr'] == 'C'){
			  $from_transactions[] = $value;
		 	}
		   if($value['item_txn_drcr'] == 'D'){
			 $to_transactions[] = $value;
			 }
		} 
	    $get_narration                    = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
		$data['taxes_list']               = $taxes_list;
		$data['method_list']              = ['','New Ref.','Adjustment'];
		$data['narration']                = $get_narration;
		$data['message_output']           = $this->message_output;
		$data['base_url']                 = $this->base_url; 
		$data['voucher_id']               = $voucher_type_id;
		$data['voucher_auto_no']          = $this->VouchersModel->get_voucher_no($voucher_type_id);
		$data['voucher_date']      		  = date('d-m-Y', strtotime($voucher_info['vch_date']));
		$data['sel_vch_series']           = $voucher_info['vch_series_id'];
		$data['matrcntr_id']              = $voucher_info['mat_cent_id'];
		$data['folder_path']              = $this->folder_path;	
		$data['units_list']     	      = $this->VouchersModel->units_grid();
		$data['voucher_series']           = $this->VouchersModel->comp_voucher_result($this->company_id,$voucher_type_id);	
		$data['matrcntr_dropdown']        = $this->VouchersModel->material_centre_dropdown();
		$data['from_transactions']        = $from_transactions;
		$data['to_transactions']       	  = $to_transactions;
		$items_list                       = GetJsonFileContent($this->fy_id,$this->company_id,'itm');		
		$data['item_json_file']           = $items_list; 
		$data['voucher_type_id']  	      = $voucher_type_id;
		$data['vch_subtype_id']  	      = $vch_subtype_id;
		$data['voucher_name'] 	          = $voucher_name;
		/* if(in_array(3, array_column($data['from_transactions'], 'vch_txn_type'))) {
			$data['isoptional'] = 1;			
		}else
			$data['isoptional'] = 0; */
		$data['currency_list']   =  $this->VouchersModel->get_currency_list();
		$data['currency_id']     =  $voucher_info['currency_id'];
		$data['forexcrncy_rate'] =  $voucher_info['forexcrncy_rate'];
		$data['item_batch_data'] = $this->VouchersModel->get_itembatch_txn_data($voucher_txn_id);
		$data['voucher_txn_id']  =  $voucher_txn_id;
		return view($this->folder_path.'stock_journal/edit',$data);		
    }
     
   public function delete_stock(){
		$voucher_type_id    = 20;
		if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		 try{ 
			$vc_txn_id_info     = unobfuscate_link($this->request->getVar('idvar'));
			$voucher_txn_id     = $vc_txn_id_info[1] ?? 0;		 
			$voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
			if(empty($voucher_info)){
			  throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}	
			
			// Mark Dirty Transactions
			 $voucher_date = $voucher_info['vch_date'];
		    $voucherDate = new \DateTime($voucher_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				 $this->VouchersModel->markSnapshotDirty($voucher_date);
			 }
			 
			$this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');
			$this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itemtxnmst');		
		    $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
		    $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_type_id);
			$this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itmvchregn'); 
			$this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchtxnconso'); 
			return $this->response->setJSON(['status' => true, 'message' => 'Voucher Deleted']);
			}
		  catch (\Throwable $e){
		  log_message('error', 'DB Query Error: ' . $e->getMessage());
		  return $this->response->setJSON(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
		  }	   	
		}
	}
	
}