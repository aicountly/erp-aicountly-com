<?php
namespace App\Controllers\Admin;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Purchase_requisition  extends BaseController
{
  function __construct()
	{   
		helper(['form', 'url','text']);
		$this->TransactionModel  = new TransactionModel();
		$this->LogModel        = new ERPLogModel();
		$this->auth_session  = new auth_session();			
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->auth_session->is_company_opened();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
		$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
	}
   
  
	public function with_amount()
	{
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_type_id    = "21"; // change it
		$oth_txn_tag 				= 'PURREQN';
        $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
		if($this->request->getMethod() == 'post' && $this->request->isAjax())
		{	

			// echo "<pre>";print_r($_POST);exit;
			$rules = [				
				'sale_date' => [
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
			  'party_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Party is required'
				  ],
				],
				'matrcntr_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Material Center is required'
				  ],
				],
				'itmsdata' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Data is required'
				  ],
			  ],
			 ];
			
      if(!$this->validate($rules)){
      	$errors = $this->validator->getErrors();
      	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
      }
      else
      {
      	 $voucher_date         = date("Y-m-d", strtotime($this->request->getVar('sale_date')));
      	 $party_id         	= $this->request->getVar('party_id');                  
				 $voucher_series    = $this->request->getVar('voucher_series'); 
				 $matrcntr_id       = $this->request->getVar('matrcntr_id'); 
				 $narration         = $this->request->getVar('narration');  
				 $itmsdata          = json_decode($this->request->getVar('itmsdata'),true); 
				 $currency_id 			= $this->request->getVar('currency_id');

				 $voucher_no        = $this->TransactionModel->get_voucher_no($voucher_type_id);

				 $billsndrydata = [];
				 if(!empty($this->request->getVar('billsndrydata')))
				 		$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
				 

				 $sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));
				 $billsundry_total = 0;
				 if(count($billsndrydata))
				 		$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
				 
					$batchdata = [];
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);

				 // add voucher consolidated entry
				 $voucher_txn_data     = array(
				 			"comp_id"							=> $this->company_id,
				 			"comp_vch_series_id"	=> $voucher_series,
				 			"comp_vch_no"					=> $voucher_no,
				 			"voucher_type_id"			=> $voucher_type_id,
					    "voucher_date"				=> $voucher_date,
					    "mat_cent_id"					=> $matrcntr_id,
					    // "voucher_tag"					=> $voucher_tag,
					    "currency_id"				=> $currency_id,
					  );
				 $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($voucher_txn_data);
					
				 // Add company txn master entry for party
				 $txn_data = array(
						"comp_id"							=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 			=> $voucher_txn_id,
						"master_id" 					=> $party_id,
						'master_id_type' 			=> 'aco'
				 	);
         $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

				 // Add party oth txn entry 
		     $insert_data  = [
									'comp_id'            		 => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),						
									'acc_oth_txn_drcr'       => 'c',
									'voucher_type_id'    		 => $voucher_type_id,
									'comp_vch_series_no' 		 => $voucher_series,
									'acc_id'             		 => $party_id,						
									'voucher_txn_id'     		 => $voucher_txn_id,
									'acc_oth_txn_status' 		 => $voucher_no,
									'bo_id'									 => 0,
									'txn_id'								 => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'				 => $oth_txn_tag,
							];
					$this->TransactionModel->add_acc_oth_data($insert_data);

				$item_account_array = [];
		    foreach($itmsdata as $item_row)
		    {

						$txn_data = array(
							"comp_id"							=> $this->company_id,
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_txn_id" 			=> $voucher_txn_id,
							"master_id" 					=> $item_row['item_id'],
							'master_id_type' 			=> 'ito'
						);
						$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

						$insert_data   = array(
							'comp_id'              => $this->company_id,
							'item_oth_txn_date'    => $voucher_date,
							'item_oth_txn_amount'  => $item_row['item_total_amount'],
							'item_oth_txn_drcr'    => 'd',
							'item_oth_txn_qty'     => $item_row['item_qty'],
							'description'          => $item_row['description'],
							'comp_vch_series_no'   => $voucher_no,
							'item_id'              => $item_row['item_id'],
							'voucher_txn_id'       => $voucher_txn_id,
							'voucher_type_id'      => $voucher_type_id,
							'mat_cent_id'          => $matrcntr_id,
							'item_oth_txn_tag'     => $oth_txn_tag,
							'bo_id'								 => 0,
							'txn_id'							 => $txn_id,
							'item_unit'            => $item_row['item_unit_id'],
						);
						$this->TransactionModel->add_itm_oth_data($insert_data);

						$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
						$account_id  =  $item_info['item_pur_acc'];

						if(isset($item_account_array[$account_id]))
							$item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
						else
							$item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);

		    } //for loop

		    foreach ($item_account_array as $account_id => $amount) {
					
				 	$txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $account_id,
						'master_id_type' 		=> 'aco'
					);
					$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

					// Add party oth txn entry 
					$insert_data  = [
						'comp_id'              => $this->company_id,
						'acc_oth_txn_date'     => $voucher_date,
						'acc_oth_txn_amount'   => $amount,						
						'acc_oth_txn_drcr'     => 'd',
						'voucher_type_id'      => $voucher_type_id,
						'comp_vch_series_no'   => $voucher_series,
						'acc_id'               => $account_id,						
						'voucher_txn_id'       => $voucher_txn_id,
						'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
						'bo_id'				   => 0,
						'txn_id'			   => $txn_id,
						'acc_oth_txn_duedate'  => date('Y-m-d'),
						'acc_oth_txn_tag'	   => $oth_txn_tag,
					];
					$this->TransactionModel->add_acc_oth_data($insert_data);
				}

				$insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $voucher_series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => 0,
	              'master_id_type'      => 'nrr'
          		];

		    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

			if(!empty($batchdata))
				    	{
				    			foreach ($batchdata as $key => $value) {

				    					$batch_id = 0;
			                if($value['batch_method'] == 'New Ref.')
			                {
			                    $batch_master_data = [
			                        'batch_no' 		   => $value['batch_no'],
			                        'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        'item_id'  			 => $value['item_id'],
			                    ];
			                    $batch_id = $this->TransactionModel->add_batch_master($batch_master_data);
			                }
			                if($value['batch_method'] == 'Adjustment')
			                {
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        		'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                }
			                if($batch_id)
			                {
			                    $insert_data = [
			                        "item_txn_date"			=> $voucher_date,
															"item_id"						=> $value['item_id'],
															'item_txn_drcr'     => 'd',
															"txn_id"						=> 0,
															"item_txn_id"				=> 0,
															"voucher_txn_id"		=> $voucher_txn_id,
															"bo_id"							=> 0,
															"mat_cent_id"				=> $matrcntr_id,
															"item_unit"					=> $value['batch_uom_id'],
															"item_bal_qty"			=> $value['batch_qty'],
															"batch_id"					=> $batch_id,
															"item_avail"				=> 0,
			                    ];
			                    $this->TransactionModel->add_batch_txn($insert_data);
			                }
				    			}
				    	}

		    if(count($billsndrydata))
		    {
						foreach($billsndrydata as $value)
						{
								$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $value['billsundry_id'],
									'master_id_type' 			=> 'bso'
							 	);
					      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

					      if($value['billsundry_amount'] >= 0)
					      		$billsundry_drcr = 'd';
					      else
					      	  $billsundry_drcr = 'c';

		           $insert_data  = [
									'comp_id'            	   => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => abs($value['billsundry_amount']),						
									'acc_oth_txn_drcr'       => $billsundry_drcr,
									'voucher_type_id'    	   => $voucher_type_id,
									'comp_vch_series_no' 	   => $voucher_series,
									'acc_id'             	   => $value['billsundry_id'],						
									'voucher_txn_id'     	   => $voucher_txn_id,
									'acc_oth_txn_status' 	   => $value['billsundry_rate'],
									'bo_id'					 				 => 0,
									'txn_id'				         => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		     => 'BILSDRY',
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);
						}
		    }

		    // return redirect()->to($this->base_url.'purchase_requisition/with_amount');
		    return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
		  } // else validation
		} // input type post

		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->TransactionModel->items_list();	
		$data['units_list']       = $this->TransactionModel->units_dropdown();
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['party_dropdown']  = $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
		$data['bills_method_list']				= ['','New Ref.','Adjustment'];
		$data['voucher_date']                    = $voucher_detail['last_entry'];
        $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
        $data['voucher_type_id']                = $voucher_type_id;
        $data['currency_list'] = $this->TransactionModel->get_currency_list();
        
		return view($this->folder_path.'purchase_requisition/with_amount',$data);		
	}

	public function without_amount()
	{   
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_type_id    = "21"; // change it
		$oth_txn_tag 				= 'PURREQN';
        $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
		if($this->request->getMethod() == 'post' && $this->request->isAjax())
		{	

			// echo "<pre>";print_r($_POST);exit;
			$rules = [				
				'sale_date' => [
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
			  'party_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Party is required'
				  ],
				],
				'matrcntr_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Material Center is required'
				  ],
				],
				'itmsdata' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Data is required'
				  ],
			  ],
			 ];
			
      if(!$this->validate($rules)){
      	$errors = $this->validator->getErrors();
      	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
      }
      else
      {
      	 $voucher_date         = date("Y-m-d", strtotime($this->request->getVar('sale_date')));
      	 $party_id         	= $this->request->getVar('party_id');                  
				 $voucher_series    = $this->request->getVar('voucher_series'); 
				 $matrcntr_id       = $this->request->getVar('matrcntr_id'); 
				 $narration         = $this->request->getVar('narration');  
				 $itmsdata          = json_decode($this->request->getVar('itmsdata'),true);
				 $currency_id 			= $this->request->getVar('currency_id'); 

				 $voucher_no        = $this->TransactionModel->get_voucher_no($voucher_type_id);


				 $billsndrydata = [];
				 if(!empty($this->request->getVar('billsndrydata')))
				 		$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
				 
               	$batchdata = [];
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);
						
						
				 $sale_total = 0;
				 $billsundry_total = 0;
				 if(count($billsndrydata))
				 		$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
				 

				 // add voucher consolidated entry
				 $voucher_txn_data     = array(
				 			"comp_id"							=> $this->company_id,
				 			"comp_vch_series_id"	=> $voucher_series,
				 			"comp_vch_no"					=> $voucher_no,
				 			"voucher_type_id"			=> $voucher_type_id,
					    "voucher_date"				=> $voucher_date,
					    "mat_cent_id"					=> $matrcntr_id,
					    // "voucher_tag"					=> $voucher_tag,
					    "currency_id"				=> $currency_id,
					  );
				 $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($voucher_txn_data);
					
				 // Add company txn master entry for party
				 $txn_data = array(
						"comp_id"							=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 			=> $voucher_txn_id,
						"master_id" 					=> $party_id,
						'master_id_type' 			=> 'aco'
				 	);
         $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

				 // Add party oth txn entry 
		     $insert_data  = [
									'comp_id'            		 => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),						
									'acc_oth_txn_drcr'       => 'c',
									'voucher_type_id'    		 => $voucher_type_id,
									'comp_vch_series_no' 		 => $voucher_series,
									'acc_id'             		 => $party_id,						
									'voucher_txn_id'     		 => $voucher_txn_id,
									'acc_oth_txn_status' 		 => $voucher_no,
									'bo_id'									 => 0,
									'txn_id'								 => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'				 => $oth_txn_tag,
							];
					$this->TransactionModel->add_acc_oth_data($insert_data);

					$item_account_array = [];
		    foreach($itmsdata as $item_row)
		    {

						$txn_data = array(
							"comp_id"							=> $this->company_id,
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_txn_id" 			=> $voucher_txn_id,
							"master_id" 					=> $item_row['item_id'],
							'master_id_type' 			=> 'ito'
						);
						$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

						$insert_data   = array(
							'comp_id'              => $this->company_id,
							'item_oth_txn_date'    => $voucher_date,
							'item_oth_txn_amount'  => 0,
							'item_oth_txn_drcr'    => 'd',
							'item_oth_txn_qty'     => $item_row['item_qty'],
							'description'          => $item_row['description'],
							'comp_vch_series_no'   => $voucher_no,
							'item_id'              => $item_row['item_id'],
							'voucher_txn_id'       => $voucher_txn_id,
							'voucher_type_id'      => $voucher_type_id,
							'mat_cent_id'          => $matrcntr_id,
							'item_oth_txn_tag'     => $oth_txn_tag,
							'bo_id'								 => 0,
							'txn_id'							 => $txn_id,
							'item_unit'            => $item_row['item_unit_id'],
						);
						$this->TransactionModel->add_itm_oth_data($insert_data);

						$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
						$account_id  =  $item_info['item_pur_acc'];

						if(isset($item_account_array[$account_id]))
						$item_account_array[$account_id] += 0;
					else
						$item_account_array[$account_id] = 0;

		    } //for loop

		    foreach ($item_account_array as $account_id => $amount) {
					
				 	$txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $account_id,
						'master_id_type' 		=> 'aco'
					);
					$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

					// Add party oth txn entry 
					$insert_data  = [
						'comp_id'              => $this->company_id,
						'acc_oth_txn_date'     => $voucher_date,
						'acc_oth_txn_amount'   => $amount,						
						'acc_oth_txn_drcr'     => 'd',
						'voucher_type_id'      => $voucher_type_id,
						'comp_vch_series_no'   => $voucher_series,
						'acc_id'               => $account_id,						
						'voucher_txn_id'       => $voucher_txn_id,
						'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
						'bo_id'				   => 0,
						'txn_id'			   => $txn_id,
						'acc_oth_txn_duedate'  => date('Y-m-d'),
						'acc_oth_txn_tag'	   => $oth_txn_tag,
					];
					$this->TransactionModel->add_acc_oth_data($insert_data);
				}

				$insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $voucher_series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => 0,
	              'master_id_type'      => 'nrr'
          		];

		    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

			if(!empty($batchdata))
				    	{
				    			foreach ($batchdata as $key => $value) {

				    					$batch_id = 0;
			                if($value['batch_method'] == 'New Ref.')
			                {
			                    $batch_master_data = [
			                        'batch_no' 		   => $value['batch_no'],
			                        'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        'item_id'  			 => $value['item_id'],
			                    ];
			                    $batch_id = $this->TransactionModel->add_batch_master($batch_master_data);
			                }
			                if($value['batch_method'] == 'Adjustment')
			                {
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        		'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                }
			                if($batch_id)
			                {
			                    $insert_data = [
			                        "item_txn_date"			=> $voucher_date,
															"item_id"						=> $value['item_id'],
															'item_txn_drcr'     => 'd',
															"txn_id"						=> 0,
															"item_txn_id"				=> 0,
															"voucher_txn_id"		=> $voucher_txn_id,
															"bo_id"							=> 0,
															"mat_cent_id"				=> $matrcntr_id,
															"item_unit"					=> $value['batch_uom_id'],
															"item_bal_qty"			=> $value['batch_qty'],
															"batch_id"					=> $batch_id,
															"item_avail"				=> 0,
			                    ];
			                    $this->TransactionModel->add_batch_txn($insert_data);
			                }
				    			}
				    	}
		    if(count($billsndrydata))
		    {
						foreach($billsndrydata as $value)
						{
								$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $value['billsundry_id'],
									'master_id_type' 			=> 'bso'
							 	);
					      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

					      if($value['billsundry_amount'] >= 0)
					      		$billsundry_drcr = 'd';
					      else
					      	  $billsundry_drcr = 'c';

		           $insert_data  = [
									'comp_id'            	   => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => abs($value['billsundry_amount']),						
									'acc_oth_txn_drcr'       => $billsundry_drcr,
									'voucher_type_id'    	   => $voucher_type_id,
									'comp_vch_series_no' 	   => $voucher_series,
									'acc_id'             	   => $value['billsundry_id'],						
									'voucher_txn_id'     	   => $voucher_txn_id,
									'acc_oth_txn_status' 	   => $value['billsundry_rate'],
									'bo_id'					 				 => 0,
									'txn_id'				         => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		     => 'BILSDRY',
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);
						}
		    }

		    // return redirect()->to($this->base_url.'purchase_requisition/without_amount');
		    return json_encode(['status' => true, 'message' => 'Voucher Inserted']);

		  } // else validation
		} // input type post


		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->TransactionModel->items_list();	
		$data['units_list']       = $this->TransactionModel->units_dropdown();
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['party_dropdown']  = $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
		$data['bills_method_list']				= ['','New Ref.','Adjustment'];
		
		$data['voucher_date']                    = $voucher_detail['last_entry'];
        $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
        $data['voucher_type_id']                = $voucher_type_id;
        $data['currency_list'] = $this->TransactionModel->get_currency_list();

		return view($this->folder_path.'purchase_requisition/without_amount',$data);		
	}
     
  	public function edit($voucher_txn_id)
		{
				$voucher_type_id    = "21"; // change it
				$oth_txn_tag 				= 'PURREQN';

				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				if(empty($voucher_info)){
					 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
				}
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);

				if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		    	 // echo "<pre>";print_r($_POST);exit;
		     
		     		$rules = [				
							'sale_date' => [
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
						  'party_id' => [
								'rules'  => 'required',
								'errors' => [
									'required' => 'Party is required'
							  ],
							],
							'matrcntr_id' => [
								'rules'  => 'required',
								'errors' => [
									'required' => 'Material Center is required'
							  ],
							],
							'itmsdata' => [
								'rules'  => 'required',
								'errors' => [
									'required' => 'Data is required'
							  ],
						  ],
						 ];
						
			      if(!$this->validate($rules)){
			      	$errors = $this->validator->getErrors();
			      	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
			      }
		        else
		        {
		        	 $voucher_date         = date("Y-m-d", strtotime($this->request->getVar('sale_date')));
		        	 $party_id          = $this->request->getVar('party_id');                  
							 $voucher_series    = $this->request->getVar('voucher_series');
							 $matrcntr_id       = $this->request->getVar('matrcntr_id'); 
							 $narration         = $this->request->getVar('narration');  
							 $itmsdata          = json_decode($this->request->getVar('itmsdata'),true);
							 $currency_id 			= $this->request->getVar('currency_id'); 

							 $voucher_no        = $this->TransactionModel->get_voucher_no($voucher_type_id);
							 $type 							= $this->request->getVar('type');
				
							 $billsndrydata = [];
							 if(!empty($this->request->getVar('billsndrydata')))
							 		$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
							 
		     	 	$batchdata = []; // batch coding
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);
				 
					     if($type == 'with_amount')
					     {
					     		 $sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));
									 $billsundry_total = 0;
									 if(count($billsndrydata))
									 		$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
									 

									 // add voucher consolidated entry
									 $voucher_txn_data     = array(
									 			"comp_vch_series_id"	=> $voucher_series,
										    "voucher_date"				=> $voucher_date,
										    "mat_cent_id"					=> $matrcntr_id,
										    "currency_id"				  => $currency_id,
										  );
									 $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);

									 // delete comp txn master
									 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
									 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_all_narrations($voucher_txn_id);

									 // Add company txn master entry for party
									 $txn_data = array(
											"comp_id"							=> $this->company_id,
											"comp_vch_series_id"	=> $voucher_series,
											"voucher_txn_id" 			=> $voucher_txn_id,
											"master_id" 					=> $party_id,
											'master_id_type' 			=> 'aco'
									 	);
					         $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

									 // Add party oth txn entry 
							     $insert_data  = [
														'comp_id'            		 => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),						
														'acc_oth_txn_drcr'       => 'c',
														'voucher_type_id'    		 => $voucher_type_id,
														'comp_vch_series_no' 		 => $voucher_series,
														'acc_id'             		 => $party_id,						
														'voucher_txn_id'     		 => $voucher_txn_id,
														'acc_oth_txn_status' 		 => $voucher_no,
														'bo_id'									 => 0,
														'txn_id'								 => $txn_id,
														'acc_oth_txn_duedate' 	 => date('Y-m-d'),
														'acc_oth_txn_tag'				 => $oth_txn_tag,
												];
										$this->TransactionModel->add_acc_oth_data($insert_data);

									$item_account_array = [];
							    foreach($itmsdata as $item_row)
							    {

											$txn_data = array(
												"comp_id"							=> $this->company_id,
												"comp_vch_series_id"	=> $voucher_series,
												"voucher_txn_id" 			=> $voucher_txn_id,
												"master_id" 					=> $item_row['item_id'],
												'master_id_type' 			=> 'ito'
											);
											$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

											$insert_data   = array(
												'comp_id'              => $this->company_id,
												'item_oth_txn_date'    => $voucher_date,
												'item_oth_txn_amount'  => $item_row['item_total_amount'],
												'item_oth_txn_drcr'    => 'd',
												'item_oth_txn_qty'     => $item_row['item_qty'],
												'description'          => $item_row['description'],
												'comp_vch_series_no'   => $voucher_no,
												'item_id'              => $item_row['item_id'],
												'voucher_txn_id'       => $voucher_txn_id,
												'voucher_type_id'      => $voucher_type_id,
												'mat_cent_id'          => $matrcntr_id,
												'item_oth_txn_tag'     => $oth_txn_tag,
												'bo_id'								 => 0,
												'txn_id'							 => $txn_id,
												'item_unit'            => $item_row['item_unit_id'],
											);
											$this->TransactionModel->add_itm_oth_data($insert_data);

											$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
											$account_id  =  $item_info['item_pur_acc'];

											if(isset($item_account_array[$account_id]))
												$item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
											else
												$item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);

							    } //for loop

							    foreach ($item_account_array as $account_id => $amount) {
					
									 	$txn_data = array(
											"comp_id"				=> $this->company_id,
											"comp_vch_series_id"	=> $voucher_series,
											"voucher_txn_id" 		=> $voucher_txn_id,
											"master_id" 			=> $account_id,
											'master_id_type' 		=> 'aco'
										);
										$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

										// Add party oth txn entry 
										$insert_data  = [
											'comp_id'              => $this->company_id,
											'acc_oth_txn_date'     => $voucher_date,
											'acc_oth_txn_amount'   => $amount,						
											'acc_oth_txn_drcr'     => 'd',
											'voucher_type_id'      => $voucher_type_id,
											'comp_vch_series_no'   => $voucher_series,
											'acc_id'               => $account_id,						
											'voucher_txn_id'       => $voucher_txn_id,
											'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
											'bo_id'				   => 0,
											'txn_id'			   => $txn_id,
											'acc_oth_txn_duedate'  => date('Y-m-d'),
											'acc_oth_txn_tag'	   => $oth_txn_tag,
										];
										$this->TransactionModel->add_acc_oth_data($insert_data);
									}

									$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

						    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
						    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

								if(!empty($batchdata)) // batch data
				    	{
				    			foreach ($batchdata as $key => $value) {

				    					$batch_id = 0;
			                if($value['batch_method'] == 'New Ref.')
			                {
			                    $batch_master_data = [
			                        'batch_no' 		   => $value['batch_no'],
			                        'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        'item_id'  			 => $value['item_id'],
			                    ];
			                    $batch_id = $this->TransactionModel->add_batch_master($batch_master_data);
			                }
			                if($value['batch_method'] == 'Adjustment')
			                {
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        		'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                }
			                if($batch_id)
			                {
			                    $insert_data = [
			                        "item_txn_date"			=> $voucher_date,
															"item_id"						=> $value['item_id'],
															'item_txn_drcr'     => 'd',
															"txn_id"						=> 0,
															"item_txn_id"				=> 0,
															"voucher_txn_id"		=> $voucher_txn_id,
															"bo_id"							=> 0,
															"mat_cent_id"				=> $matrcntr_id,
															"item_unit"					=> $value['batch_uom_id'],
															"item_bal_qty"			=> $value['batch_qty'],
															"batch_id"					=> $batch_id,
															"item_avail"				=> 0,
			                    ];
			                    $this->TransactionModel->add_batch_txn($insert_data);
			                }
				    			}
				    	}

							    if(count($billsndrydata))
							    {
											foreach($billsndrydata as $value)
											{
													$txn_data = array(
														"comp_id"							=> $this->company_id,
														"comp_vch_series_id"	=> $voucher_series,
														"voucher_txn_id" 			=> $voucher_txn_id,
														"master_id" 					=> $value['billsundry_id'],
														'master_id_type' 			=> 'bso'
												 	);
										      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

										      if($value['billsundry_amount'] >= 0)
										      		$billsundry_drcr = 'd';
										      else
										      	  $billsundry_drcr = 'c';

							           $insert_data  = [
														'comp_id'            	   => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => abs($value['billsundry_amount']),						
														'acc_oth_txn_drcr'       => $billsundry_drcr,
														'voucher_type_id'    	   => $voucher_type_id,
														'comp_vch_series_no' 	   => $voucher_series,
														'acc_id'             	   => $value['billsundry_id'],						
														'voucher_txn_id'     	   => $voucher_txn_id,
														'acc_oth_txn_status' 	   => $value['billsundry_rate'],
														'bo_id'					 				 => 0,
														'txn_id'				         => $txn_id,
														'acc_oth_txn_duedate' 	 => date('Y-m-d'),
														'acc_oth_txn_tag'		     => 'BILSDRY',
												];
												$this->TransactionModel->add_acc_oth_data($insert_data);
											}
							    }

							    // return redirect()->to($this->base_url.'purchase_requisition/with_amount');
					     }
					     if($type == 'without_amount')
					     {
					     		 $sale_total = 0;
									 $billsundry_total = 0;
									 if(count($billsndrydata))
									 		$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
									 

									 // add voucher consolidated entry
									 $voucher_txn_data     = array(
									 			"comp_vch_series_id"	=> $voucher_series,
										    "voucher_date"				=> $voucher_date,
										    "mat_cent_id"					=> $matrcntr_id,
										    "currency_id"				  => $currency_id,
										  );
									 $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);

									 // delete comp txn master
									 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
									 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_all_narrations($voucher_txn_id);

									// Add company txn master entry for party
								 $txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $party_id,
										'master_id_type' 			=> 'aco'
								 	);
				         $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								 // Add party oth txn entry 
						     $insert_data  = [
													'comp_id'            		 => $this->company_id,
													'acc_oth_txn_date'       => $voucher_date,
													'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),						
													'acc_oth_txn_drcr'       => 'c',
													'voucher_type_id'    		 => $voucher_type_id,
													'comp_vch_series_no' 		 => $voucher_series,
													'acc_id'             		 => $party_id,						
													'voucher_txn_id'     		 => $voucher_txn_id,
													'acc_oth_txn_status' 		 => $voucher_no,
													'bo_id'									 => 0,
													'txn_id'								 => $txn_id,
													'acc_oth_txn_duedate' 	 => date('Y-m-d'),
													'acc_oth_txn_tag'				 => $oth_txn_tag,
											];
									$this->TransactionModel->add_acc_oth_data($insert_data);

								$item_account_array = [];
						    foreach($itmsdata as $item_row)
						    {

										$txn_data = array(
											"comp_id"							=> $this->company_id,
											"comp_vch_series_id"	=> $voucher_series,
											"voucher_txn_id" 			=> $voucher_txn_id,
											"master_id" 					=> $item_row['item_id'],
											'master_id_type' 			=> 'ito'
										);
										$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

										$insert_data   = array(
											'comp_id'              => $this->company_id,
											'item_oth_txn_date'    => $voucher_date,
											'item_oth_txn_amount'  => 0,
											'item_oth_txn_drcr'    => 'd',
											'item_oth_txn_qty'     => $item_row['item_qty'],
											'description'          => $item_row['description'],
											'comp_vch_series_no'   => $voucher_no,
											'item_id'              => $item_row['item_id'],
											'voucher_txn_id'       => $voucher_txn_id,
											'voucher_type_id'      => $voucher_type_id,
											'mat_cent_id'          => $matrcntr_id,
											'item_oth_txn_tag'     => $oth_txn_tag,
											'bo_id'								 => 0,
											'txn_id'							 => $txn_id,
											'item_unit'            => $item_row['item_unit_id'],
										);
										$this->TransactionModel->add_itm_oth_data($insert_data);

										$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
										$account_id  =  $item_info['item_pur_acc'];

										if(isset($item_account_array[$account_id]))
											$item_account_array[$account_id] += 0;
										else
											$item_account_array[$account_id] = 0;

						    } //for loop

						    foreach ($item_account_array as $account_id => $amount) {
					
								 	$txn_data = array(
										"comp_id"				=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 		=> $voucher_txn_id,
										"master_id" 			=> $account_id,
										'master_id_type' 		=> 'aco'
									);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

									// Add party oth txn entry 
									$insert_data  = [
										'comp_id'              => $this->company_id,
										'acc_oth_txn_date'     => $voucher_date,
										'acc_oth_txn_amount'   => $amount,						
										'acc_oth_txn_drcr'     => 'd',
										'voucher_type_id'      => $voucher_type_id,
										'comp_vch_series_no'   => $voucher_series,
										'acc_id'               => $account_id,						
										'voucher_txn_id'       => $voucher_txn_id,
										'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
										'bo_id'				   => 0,
										'txn_id'			   => $txn_id,
										'acc_oth_txn_duedate'  => date('Y-m-d'),
										'acc_oth_txn_tag'	   => $oth_txn_tag,
									];
									$this->TransactionModel->add_acc_oth_data($insert_data);
								}

								$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

						    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
						    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);

							if(!empty($batchdata)) // batch data
				    	{
				    			foreach ($batchdata as $key => $value) {

				    					$batch_id = 0;
			                if($value['batch_method'] == 'New Ref.')
			                {
			                    $batch_master_data = [
			                        'batch_no' 		   => $value['batch_no'],
			                        'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        'item_id'  			 => $value['item_id'],
			                    ];
			                    $batch_id = $this->TransactionModel->add_batch_master($batch_master_data);
			                }
			                if($value['batch_method'] == 'Adjustment')
			                {
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => date("Y-m-d", strtotime($value['manufacturing_date'])),
			                        		'batch_expiry'   => date("Y-m-d", strtotime($value['expiry_date'])),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                }
			                if($batch_id)
			                {
			                    $insert_data = [
			                        "item_txn_date"			=> $voucher_date,
															"item_id"						=> $value['item_id'],
															'item_txn_drcr'     => 'd',
															"txn_id"						=> 0,
															"item_txn_id"				=> 0,
															"voucher_txn_id"		=> $voucher_txn_id,
															"bo_id"							=> 0,
															"mat_cent_id"				=> $matrcntr_id,
															"item_unit"					=> $value['batch_uom_id'],
															"item_bal_qty"			=> $value['batch_qty'],
															"batch_id"					=> $batch_id,
															"item_avail"				=> 0,
			                    ];
			                    $this->TransactionModel->add_batch_txn($insert_data);
			                }
				    			}
				    	}
						    if(count($billsndrydata))
						    {
										foreach($billsndrydata as $value)
										{
												$txn_data = array(
													"comp_id"							=> $this->company_id,
													"comp_vch_series_id"	=> $voucher_series,
													"voucher_txn_id" 			=> $voucher_txn_id,
													"master_id" 					=> $value['billsundry_id'],
													'master_id_type' 			=> 'bso'
											 	);
									      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

									      if($value['billsundry_amount'] >= 0)
									      		$billsundry_drcr = 'd';
									      else
									      	  $billsundry_drcr = 'c';

						           $insert_data  = [
													'comp_id'            	   => $this->company_id,
													'acc_oth_txn_date'       => $voucher_date,
													'acc_oth_txn_amount'     => abs($value['billsundry_amount']),						
													'acc_oth_txn_drcr'       => $billsundry_drcr,
													'voucher_type_id'    	   => $voucher_type_id,
													'comp_vch_series_no' 	   => $voucher_series,
													'acc_id'             	   => $value['billsundry_id'],						
													'voucher_txn_id'     	   => $voucher_txn_id,
													'acc_oth_txn_status' 	   => $value['billsundry_rate'],
													'bo_id'					 				 => 0,
													'txn_id'				         => $txn_id,
													'acc_oth_txn_duedate' 	 => date('Y-m-d'),
													'acc_oth_txn_tag'		     => 'BILSDRY',
											];
											$this->TransactionModel->add_acc_oth_data($insert_data);
										}
						    }

						    // return redirect()->to($this->base_url.'purchase_requisition/without_amount');
					     }
					     return json_encode(['status' => true, 'message' => 'Voucher Updated']);
			   	}
		   	}

				$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
				$data['voucher_no'] 				= $voucher_info['comp_vch_no'];
				$data['sale_date'] 			    = date('d-m-Y', strtotime($voucher_info['voucher_date']));
				$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];
				$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);	

				$party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
				$data['party_id'] 					= $party_transaction['acc_id'];
				$data['party_amount'] 			= $party_transaction['acc_oth_txn_amount'];
				$data['narration'] 					= $party_transaction['acc_oth_txn_narr'];
				 $data['get_narration_info']     = $get_narration_info;  


				$data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
				$data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
				$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id);

				$billsundry_total = 0;
				if(count($data['sundry_transactions']))
						$billsundry_total = array_sum(array_column($data['sundry_transactions'], 'billsundry_amount'));
				

				$data['message_output']   				= $this->message_output;
				$data['base_url']         				= $this->base_url; 
				$data['voucher_auto_no']  				= $this->TransactionModel->get_voucher_no($voucher_type_id);
				$data['folder_path']      				= $this->folder_path;	
				$data['items_list']       				= $this->TransactionModel->items_list();	
				$data['units_list']       				= $this->TransactionModel->units_dropdown();
				$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
				$data['party_dropdown']  					= $this->TransactionModel->party_dropdown();
				$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
				$data['voucher_txn_id']     = $voucher_txn_id;
				$data['voucher_type_id']                = $voucher_type_id;
				$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
                $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
				$data['bills_method_list']				= ['','New Ref.','Adjustment'];
				$data['currency_list'] = $this->TransactionModel->get_currency_list();
				$data['currency_id'] = $voucher_info['currency_id'];

				if(($data['party_amount']-$billsundry_total) > 0) 
		    	return view($this->folder_path.'purchase_requisition/edit_with_amount',$data);
        else
		    	return view($this->folder_path.'purchase_requisition/edit_without_amount',$data);
	
		}
		public function delete($voucher_txn_id)
		{
				$voucher_type_id    = "21";

				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				if(!empty($voucher_info)){
					 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
					 $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
					 $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
					 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
					 $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);
					 $this->TransactionModel->delete_all_narrations($voucher_txn_id);
				}

				// return redirect()->to($this->base_url.'purchase_requisition/with_amount');
				return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
		}
	
}