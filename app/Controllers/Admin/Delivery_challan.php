<?php
namespace App\Controllers\Admin;

use App\Models\Admin\TransactionModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Delivery_challan  extends BaseController
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
  
    public function ajax_sale($voucher_txn_id){
    		$status = $this->TransactionModel->check_party_oth_status($voucher_txn_id,'SEDCDUE');
    		if($status)
    		{
    				$data['status'] = true;
    				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, '18');
    				$data['mat_cent_id'] 	= $voucher_info['mat_cent_id'];
    				$data['voucher_series'] = $voucher_info['comp_vch_series_id']; // what to do with it
    				$party_transaction = $this->TransactionModel->get_party_transaction($voucher_txn_id);
    				$data['party_id'] = $party_transaction['acc_id'];

    				$data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
						$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id,true);
						return json_encode($data);
    		}
    		return json_encode(['status' => false]);
   }
   public function ajax_debitnote($voucher_txn_id){
    		
				$data['status'] = true;
				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, '3');
				$data['mat_cent_id'] 	= $voucher_info['mat_cent_id'];
				$data['voucher_series'] = $voucher_info['comp_vch_series_id']; // what to do with it
				$party_transaction = $this->TransactionModel->get_party_transaction($voucher_txn_id);
				$data['party_id'] = $party_transaction['acc_id'];

				$data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
				$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id,true);
				return json_encode($data);
    		
   }

   public function add()
   {
	   	$voucher_type_id  = "7"; //Material Out voucher type

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

	   		  $voucher_date        = $this->request->getVar('sale_date');
	   		  $voucher_date        = validate_date_by_fy($voucher_date);
		      $party_id         = $this->request->getVar('party_id');      
              $voucher_series   = $this->request->getVar('voucher_series');  
		      $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		      $narration        = $this->request->getVar('narration'); 
		      $itmsdata         = json_decode($this->request->getVar('itmsdata'),true);  
		      $sale_voucher_id       = $this->request->getVar('sale_voucher_id');  
		      $debitnote_voucher_id     = $this->request->getVar('debitnote_voucher_id');
		      $currency_id = $this->request->getVar('currency_id');
			  $billno              = $this->request->getVar('billno');

		      	$batchdata = [];
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);

		     	$billsndrydata = [];
					if(!empty($this->request->getVar('billsndrydata')))
							$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
					$trackingdata = [];
				  if(!empty($this->request->getVar('trackinginfo_array')))
				 			$trackingdata = json_decode($this->request->getVar('trackinginfo_array'),true);
				 	$all_tracking_data = array();
				 	if($trackingdata){
				 	    foreach($trackingdata as $trrow){
				 	      $all_tracking_data[$trrow["item_id"]][] =  $trrow;
				 	    }
				 	    
				 	}	
		      
					$challan_type     = $this->request->getVar('challan_type');	//13,14
					$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);

					$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));
					$billsundry_total = 0;
					if(count($billsndrydata))
							$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
					
				
				
			if($challan_type == 13) // delivery challan without sale
		    	{
				    	$voucher_tag = 'DCESDUE'.$voucher_no;

				    	// add voucher consolidated entry
							$insert_data     = array(
						 			"comp_id"							=> $this->company_id,
						 			"comp_vch_series_id"	=> $voucher_series,
						 			"comp_vch_no"					=> $voucher_no,
						 			"voucher_type_id"			=> $voucher_type_id,
							    "voucher_date"				=> $voucher_date,
							    "mat_cent_id"					=> $matrcntr_id,
							    "vch_subtype_id"			=> $challan_type,
							    "voucher_tag"					=> $voucher_tag,
							    "currency_id"				=> $currency_id,
							);
							$voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);
						// save into gstroutsup table
						$gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>0);
						$this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				
					    // Add company txn master entry for party oth
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
									'comp_id'            	   => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
									'acc_oth_txn_drcr'       => 'd',
									'voucher_type_id'    	   => $voucher_type_id,
									'comp_vch_series_no' 	   => $voucher_series,
									'acc_id'             	   => $party_id,						
									'voucher_txn_id'     	   => $voucher_txn_id,
									'acc_oth_txn_status' 	   => $voucher_txn_id, //changed from $voucher_no
									'bo_id'					 				 => 0,
									'txn_id'				         => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		     => 'DCESDUE',
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);

							$item_account_array = [];
					    foreach($itmsdata as $item_row)
					    {
									$txn_data = array(
										"comp_id"								=> $this->company_id,
										"comp_vch_series_id"		=> $voucher_series,
										"voucher_txn_id" 				=> $voucher_txn_id,
										"master_id" 						=> $item_row['item_id'],
										'master_id_type' 				=> 'itm'
									);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
										// save tracking data 
									if(isset($all_tracking_data[$item_row['item_id']])){
						      $trackingdata = $all_tracking_data[$item_row['item_id']];
									if(!empty($trackingdata))
									{
										foreach ($trackingdata as $key => $value) {

												$tracking_id = 0;
												if($value['tracking_no'] !='UNDEFINED' && $value['tracking_no'] !='')
												{
													$tracking_master_data = [
														'tracking_no' 		  => $value['tracking_no'],
														'txn_id'              => $txn_id,
														'item_id'  			  => $value['item_id'],
														'unit_id'             => $value['tracking_uom_id']
													];
													$tracking_id = $this->TransactionModel->add_tracking_master($tracking_master_data);
												}

											}
										}
									}
									

									$insert_data   = array(
											'comp_id'             => $this->company_id,
											'item_txn_date'       => $voucher_date,
											'item_txn_amount'     => $item_row['item_total_amount'],
											'item_txn_drcr'       => 'c',
											'item_txn_qty'        => $item_row['item_qty'],
											'description'       => $item_row['description'],
											// 'comp_vch_series_no'  => $voucher_no,
											'item_id'             => $item_row['item_id'],
											'voucher_txn_id'      => $voucher_txn_id,
											'voucher_type_id'     => $voucher_type_id,
											'mat_cent_id'         => $matrcntr_id,
											'bo_id'				  			=> 0,
											'txn_id'			  			=> $txn_id,
											"item_unit"			  		=> $item_row['item_unit_id'],
											"item_bal_qty"		  	=> 0,
											"item_avail"  		  	=> 1,
											"batch_id"  		  		=> 0,
									   );	
									$this->TransactionModel->add_itm_txn_data($insert_data);

									$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
									$account_id  =  $item_info['item_sales_acc'];

									if(isset($item_account_array[$account_id]))
										$item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
									else
										$item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);
									
					    } //for loop 

					    foreach ($item_account_array as $account_id => $amount) {
					
							 	$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $account_id,
									'master_id_type' 			=> 'aco'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								// Add party oth txn entry 
								$insert_data  = [
									'comp_id'              => $this->company_id,
									'acc_oth_txn_date'     => $voucher_date,
									'acc_oth_txn_amount'   => $amount,						
									'acc_oth_txn_drcr'     => 'c',
									'voucher_type_id'      => $voucher_type_id,
									'comp_vch_series_no'   => $voucher_series,
									'acc_id'               => $account_id,						
									'voucher_txn_id'       => $voucher_txn_id,
									'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
									'bo_id'				   			 => 0,
									'txn_id'			   			 => $txn_id,
									'acc_oth_txn_duedate'  => date('Y-m-d'),
									'acc_oth_txn_tag'	     => '',
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
			                
			               
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                    	'comp_id'             => $this->company_id,
														'item_txn_date'       => $voucher_date,
														'item_txn_amount'     => 0,
														'item_txn_drcr'       => 'c',
														'item_txn_qty'        => $value['batch_qty'],
														'description'         => '',
														'item_id'             => $value['item_id'],
														'voucher_txn_id'      => $voucher_txn_id,
														'voucher_type_id'     => $voucher_type_id,
														'mat_cent_id'         => $matrcntr_id,
														'bo_id'				  			=> 0,
														'txn_id'			  			=> 0,
														"item_unit"			  		=> $value['batch_uom_id'],
														"item_bal_qty"		  	=> 0,
														"item_avail"  		  	=> 0,
														"batch_id"  		  		=> $batch_id,
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
								      		$billsundry_drcr = 'c';
								      else
								      	  $billsundry_drcr = 'd';

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
		    	}

		    if($challan_type == 14) // delivery challan against sale
		    	{
		    			$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18); //Voucher type id of sale
				    	$voucher_tag = 'SEDCDUE'.$sale_voucher['comp_vch_no'];; //voucher no of sale

				    	// add voucher consolidated entry
							$insert_data     = array(
										"comp_id"			       => $this->company_id,
										"comp_vch_series_id" => $voucher_series,
										"comp_vch_no"		     => $voucher_no,
										"voucher_type_id"	   => $voucher_type_id,
										"voucher_date"		   => $voucher_date,
										"mat_cent_id"		     => $matrcntr_id,
										"vch_subtype_id"	   => $challan_type,
										"voucher_tag"		     => $voucher_tag,
										"currency_id"				=> $currency_id,
								);
							$voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);
							// save into gstroutsup table
							$gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>0);
							$this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				
							//Add crsref 
							$insert_data = [
									'acct_crs_id_type'		   => '',
									'comp_id'				         => $this->company_id,
									'txn_id'				         => '',
									'voucher_txn_id'		     => $voucher_txn_id,
									'bo_id'					         => 0,
									'acc_cross_ref_type'	   => 'SALEINV',
									'acc_cross_ref_data'	   => $sale_voucher_id,
									'acc_cross_logdate'   	 => date('Y-m-d'),
							];
							$this->TransactionModel->add_acc_crsref_data($insert_data);

							// Add company txn master entry for party oth
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
								'comp_id'            	 	 => $this->company_id,
								'acc_oth_txn_date'       => $voucher_date,
								'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
								'acc_oth_txn_drcr'       => 'c',  // CHANGED
								'voucher_type_id'    	   => $voucher_type_id,
								'comp_vch_series_no' 	   => $voucher_series,
								'acc_id'             	   => $party_id,						
								'voucher_txn_id'     	   => $voucher_txn_id,
								'acc_oth_txn_status' 	   => $sale_voucher_id, //should be voucher no of delivery challan
								'bo_id'					 				 => 0,
								'txn_id'				 				 => $txn_id,
								'acc_oth_txn_duedate' 	 => date('Y-m-d'),
								'acc_oth_txn_tag'		     => 'SEDCDUE', //what will be it ?
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);

							$item_account_array = [];
							foreach($itmsdata as $item_row)
					    {
									$txn_data = array(
										"comp_id"								=> $this->company_id,
										"comp_vch_series_id"		=> $voucher_series,
										"voucher_txn_id" 				=> $voucher_txn_id,
										"master_id" 						=> $item_row['item_id'],
										'master_id_type' 				=> 'itm'
									);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
										// save tracking data 
									if(isset($all_tracking_data[$item_row['item_id']])){
									     $trackingdata = $all_tracking_data[$item_row['item_id']];
									    if(!empty($trackingdata))
				                          	{
				    			            foreach ($trackingdata as $key => $value) {

				    					$tracking_id = 0;
            			                if($value['tracking_no'] !='UNDEFINED' && $value['tracking_no'] !='')
            			                {
            			                    $tracking_master_data = [
            			                        'tracking_no' 		  => $value['tracking_no'],
            			                        'txn_id'              => $txn_id,
            			                        'item_id'  			  => $value['item_id'],
            			                        'unit_id'             => $value['tracking_uom_id']
            			                    ];
            			                    $tracking_id = $this->TransactionModel->add_tracking_master($tracking_master_data);
            			                      }
			               
            				    			   }
				                        	}
									}
									

									$insert_data   = array(
											'comp_id'             => $this->company_id,
											'item_txn_date'       => $voucher_date,
											'item_txn_amount'     => $item_row['item_total_amount'],
											'item_txn_drcr'       => 'c',
											'item_txn_qty'        => $item_row['item_qty'],
											'description'       => $item_row['description'],
											// 'comp_vch_series_no'  => $voucher_no,
											'item_id'             => $item_row['item_id'],
											'voucher_txn_id'      => $voucher_txn_id,
											'voucher_type_id'     => $voucher_type_id,
											'mat_cent_id'         => $matrcntr_id,
											'bo_id'				  			=> 0,
											'txn_id'			  => $txn_id,
											"item_unit"			  => $item_row['item_unit_id'],
											"item_bal_qty"		  => 0,
											"item_avail"  		  => 1,
											"batch_id"  		  => 0,

									   );	
									$this->TransactionModel->add_itm_txn_data($insert_data);


									$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
									$account_id  =  $item_info['item_sales_acc'];

									if(isset($item_account_array[$account_id]))
										$item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
									else
										$item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);

					    } //for loop 

					    foreach ($item_account_array as $account_id => $amount) {
					
							 	$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $account_id,
									'master_id_type' 			=> 'aco'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								// Add party oth txn entry 
								$insert_data  = [
									'comp_id'              => $this->company_id,
									'acc_oth_txn_date'     => $voucher_date,
									'acc_oth_txn_amount'   => $amount,						
									'acc_oth_txn_drcr'     => 'c',
									'voucher_type_id'      => $voucher_type_id,
									'comp_vch_series_no'   => $voucher_series,
									'acc_id'               => $account_id,						
									'voucher_txn_id'       => $voucher_txn_id,
									'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
									'bo_id'				   			 => 0,
									'txn_id'			   			 => $txn_id,
									'acc_oth_txn_duedate'  => date('Y-m-d'),
									'acc_oth_txn_tag'	   	 => '',
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
			                
			               
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                    	'comp_id'             => $this->company_id,
														'item_txn_date'       => $voucher_date,
														'item_txn_amount'     => 0,
														'item_txn_drcr'       => 'c',
														'item_txn_qty'        => $value['batch_qty'],
														'description'         => '',
														'item_id'             => $value['item_id'],
														'voucher_txn_id'      => $voucher_txn_id,
														'voucher_type_id'     => $voucher_type_id,
														'mat_cent_id'         => $matrcntr_id,
														'bo_id'				  			=> 0,
														'txn_id'			  			=> 0,
														"item_unit"			  		=> $value['batch_uom_id'],
														"item_bal_qty"		  	=> 0,
														"item_avail"  		  	=> 0,
														"batch_id"  		  		=> $batch_id,
			                    ];
			                    $this->TransactionModel->add_batch_txn($insert_data);
			                }
				    			}
				    	}
					    $summary = $this->TransactionModel->get_party_oth_summary($sale_voucher_id,'SEDC');
					    $cr_total = $summary['cr_total'];
					    $dr_total = $summary['dr_total'];

					    if($cr_total > $dr_total)  
				      		$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDEF");
				      
				      if($cr_total < $dr_total)
				         	$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCSUR");

				      if($cr_total == $dr_total)
				      		$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCEXE");

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
								      		$billsundry_drcr = 'c';
								      else
								      	  $billsundry_drcr = 'd';

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
		    	}

		    if($challan_type == 22) // delivery challan against debit note
		    	{
		    			$debitnote_voucher = $this->TransactionModel->get_voucher_cons_info($debitnote_voucher_id, 3); //V type id of debit note
				    	$voucher_tag = $debitnote_voucher['voucher_tag']; //voucher no of debitnote either PESIDUE or ICEPDUE or empty

				    	// add voucher consolidated entry
							$insert_data     = array(
										"comp_id"			       => $this->company_id,
										"comp_vch_series_id" => $voucher_series,
										"comp_vch_no"		     => $voucher_no,
										"voucher_type_id"	   => $voucher_type_id,
										"voucher_date"		   => $voucher_date,
										"mat_cent_id"		     => $matrcntr_id,
										"vch_subtype_id"	   => $challan_type,
										"voucher_tag"		     => $voucher_tag,
										"currency_id"				=> $currency_id,
								);
							$voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($insert_data);
							// save into gstroutsup table
							$gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>0);
							$this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				
				
							//Add crsref 
							$insert_data = [
									'acct_crs_id_type'		   => '',
									'comp_id'				         => $this->company_id,
									'txn_id'				         => '',
									'voucher_txn_id'		     => $voucher_txn_id,
									'bo_id'					         => 0,
									'acc_cross_ref_type'	   => 'DRNTINV',
									'acc_cross_ref_data'	   => $debitnote_voucher_id,
									'acc_cross_logdate'   	 => date('Y-m-d'),
							];
							$this->TransactionModel->add_acc_crsref_data($insert_data);

							$this->TransactionModel->delete_acc_crsref_data_by_tag($debitnote_voucher_id,'PESODUE');

							if($voucher_tag != '')
							{
									if(str_contains($voucher_tag, 'PESI')){
								    $acc_oth_txn_tag = 'PESIDUE';
								    $acc_oth_txn_status = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
								    $acc_oth_txn_drcr = 'c';
							    }
							    if(str_contains($voucher_tag, 'ICEP')){
							    	$acc_oth_txn_tag = 'ICEPDUE';
							    	$purchase_voucher_id = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
							    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($purchase_voucher_id, 'INWCHAL'); //inward_challan_id
							    	$acc_oth_txn_drcr = 'd';
							    }

									// Add company txn master entry for party oth
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
										'comp_id'            	 	 => $this->company_id,
										'acc_oth_txn_date'       => $voucher_date,
										'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
										'acc_oth_txn_drcr'       => $acc_oth_txn_drcr,  // CHANGED
										'voucher_type_id'    	   => $voucher_type_id,
										'comp_vch_series_no' 	   => $voucher_series,
										'acc_id'             	   => $party_id,						
										'voucher_txn_id'     	   => $voucher_txn_id,
										'acc_oth_txn_status' 	   => $acc_oth_txn_status,
										'bo_id'					 				 => 0,
										'txn_id'				 				 => $txn_id,
										'acc_oth_txn_duedate' 	 => date('Y-m-d'),
										'acc_oth_txn_tag'		     => $acc_oth_txn_tag, //what will be it ?
									];
									$this->TransactionModel->add_acc_oth_data($insert_data);
							}
							
							$item_account_array = [];
							foreach($itmsdata as $item_row)
					    {
									$txn_data = array(
										"comp_id"								=> $this->company_id,
										"comp_vch_series_id"		=> $voucher_series,
										"voucher_txn_id" 				=> $voucher_txn_id,
										"master_id" 						=> $item_row['item_id'],
										'master_id_type' 				=> 'itm'
									);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
									
										// save tracking data 
									if(isset($all_tracking_data[$item_row['item_id']])){
									     $trackingdata = $all_tracking_data[$item_row['item_id']];
									    if(!empty($trackingdata))
				                          	{
				    			            foreach ($trackingdata as $key => $value) {

				    					$tracking_id = 0;
            			                if($value['tracking_no'] !='UNDEFINED' && $value['tracking_no'] !='')
            			                {
            			                    $tracking_master_data = [
            			                        'tracking_no' 		  => $value['tracking_no'],
            			                        'txn_id'              => $txn_id,
            			                        'item_id'  			  => $value['item_id'],
            			                        'unit_id'             => $value['tracking_uom_id']
            			                    ];
            			                    $tracking_id = $this->TransactionModel->add_tracking_master($tracking_master_data);
            			                      }
			               
            				    			   }
				                        	}
									}
									
									

									$insert_data   = array(
											'comp_id'             => $this->company_id,
											'item_txn_date'       => $voucher_date,
											'item_txn_amount'     => $item_row['item_total_amount'],
											'item_txn_drcr'       => 'd',
											'item_txn_qty'        => $item_row['item_qty'],
											'description'       => $item_row['description'],
											// 'comp_vch_series_no'  => $voucher_no,
											'item_id'             => $item_row['item_id'],
											'voucher_txn_id'      => $voucher_txn_id,
											'voucher_type_id'     => $voucher_type_id,
											'mat_cent_id'         => $matrcntr_id,
											'bo_id'				  			=> 0,
											'txn_id'			  => $txn_id,
											"item_unit"			  => $item_row['item_unit_id'],
											"item_bal_qty"		  => 0,
											"item_avail"  		  => 1,
											"batch_id"  		  => 0,

									   );	
									$this->TransactionModel->add_itm_txn_data($insert_data);

									$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
									$account_id  =  $item_info['item_sales_acc'];

									if(isset($item_account_array[$account_id]))
										$item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
									else
										$item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);
									
					    } //for loop 

					    foreach ($item_account_array as $account_id => $amount) {
					
							 	$txn_data = array(
									"comp_id"						  => $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $account_id,
									'master_id_type' 			=> 'aco'
								);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								// Add party oth txn entry 
								$insert_data  = [
									'comp_id'              => $this->company_id,
									'acc_oth_txn_date'     => $voucher_date,
									'acc_oth_txn_amount'   => $amount,						
									'acc_oth_txn_drcr'     => 'c',
									'voucher_type_id'      => $voucher_type_id,
									'comp_vch_series_no'   => $voucher_series,
									'acc_id'               => $account_id,						
									'voucher_txn_id'       => $voucher_txn_id,
									'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
									'bo_id'				   			 => 0,
									'txn_id'			   			 => $txn_id,
									'acc_oth_txn_duedate'  => date('Y-m-d'),
									'acc_oth_txn_tag'	   	 => '',
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
			                
			               
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                    	'comp_id'             => $this->company_id,
														'item_txn_date'       => $voucher_date,
														'item_txn_amount'     => 0,
														'item_txn_drcr'       => 'c',
														'item_txn_qty'        => $value['batch_qty'],
														'description'         => '',
														'item_id'             => $value['item_id'],
														'voucher_txn_id'      => $voucher_txn_id,
														'voucher_type_id'     => $voucher_type_id,
														'mat_cent_id'         => $matrcntr_id,
														'bo_id'				  			=> 0,
														'txn_id'			  			=> 0,
														"item_unit"			  		=> $value['batch_uom_id'],
														"item_bal_qty"		  	=> 0,
														"item_avail"  		  	=> 0,
														"batch_id"  		  		=> $batch_id,
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
								      		$billsundry_drcr = 'c';
								      else
								      	  $billsundry_drcr = 'd';

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

					    if(str_contains($voucher_tag, 'PESI'))
					    {
					    	$purchase_voucher_id = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
								$summary = $this->TransactionModel->get_party_oth_summary($purchase_voucher_id,'PESI');
						    $cr_total = $summary['cr_total'];
						    $dr_total = $summary['dr_total'];

						    if($cr_total > $dr_total)  
					      		$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIDEF");
					      
					      	if($cr_total < $dr_total)
					         	$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESISUR");

					      	if($cr_total == $dr_total)
					      		$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIEXE");
					    }
					    if(str_contains($voucher_tag, 'ICEP'))
					    {
					    	$purchase_voucher_id = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
					    	$inward_challan_id = $this->TransactionModel->get_crsref($purchase_voucher_id, 'INWCHAL');

					    	$summary = $this->TransactionModel->get_party_oth_summary($inward_challan_id,'ICEP');
						    $cr_total = $summary['cr_total'];
						    $dr_total = $summary['dr_total'];

						    if($cr_total > $dr_total)  
					      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDEF");
					      
					      	if($cr_total < $dr_total)
					         	$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPSUR");

					      	if($cr_total == $dr_total)
					      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPEXE");
					    }
		    	}

			    // return redirect()->to($this->base_url.'delivery_challan/add');
			    return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
	   	}
	   	    $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);

            $data['voucher_date']                    = $voucher_detail['last_entry'];
			$data['message_output'] 					= $this->message_output;
			$data['base_url']       					= $this->base_url;
			$data['folder_path']    					= $this->folder_path;
			$data['voucher_auto_no']  				= $this->TransactionModel->get_voucher_no($voucher_type_id);
			$data['items_list']     					= $this->TransactionModel->items_list();	
			$data['units_list']     					= $this->TransactionModel->units_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['party_dropdown']     			= $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
			$data['challan_type_dropdown']  	= $this->TransactionModel->vch_subtype_dropdown($voucher_type_id);
			$data['against_sale_invoice'] = $this->TransactionModel->against_dropdown('18',array('SEDCDUE','SEDCDEF','DCESSUR'));
			$data['against_debitnote_invoice'] = $this->TransactionModel->against_crsref_dropdown('3',array('PESODUE'));
			$data['billsundry_items']  				= $this->TransactionModel->billsundry_items();
			$data['bills_method_list']	= ['','New Ref.','Adjustment'];
			
			$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
            $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

			$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
            $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
            $data['voucher_type_id']                = $voucher_type_id;
            $data['currency_list'] = $this->TransactionModel->get_currency_list();

    	return view($this->folder_path.'delivery_challan/add_challan',$data);	
   }
   
  public function edit($voucher_txn_id)
	{
		$voucher_type_id    = "7";

		$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
		if(empty($voucher_info)){
			 	throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
        $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
		$vch_subtype_id = $voucher_info['vch_subtype_id'];
		
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

			$voucher_date     = $this->request->getVar('sale_date');
	      	$voucher_date     = validate_date_by_fy($voucher_date);
			$party_id         = $this->request->getVar('party_id');      
			$voucher_series   = $this->request->getVar('voucher_series');  
			$matrcntr_id      = $this->request->getVar('matrcntr_id'); 
			$narration        = $this->request->getVar('narration'); 
			$itmsdata         = json_decode($this->request->getVar('itmsdata'),true);  
			$sale_voucher_id  = $this->request->getVar('sale_voucher_id'); 
			$billno           = $this->request->getVar('billno');
			$currency_id      = $this->request->getVar('currency_id'); 
            
            $batchdata = []; // batch coding
			  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);
		
		
			$trackingdata = [];
				  if(!empty($this->request->getVar('trackinginfo_array')))
				 			$trackingdata = json_decode($this->request->getVar('trackinginfo_array'),true);
				 	$all_tracking_data = array();
				 	if($trackingdata){
				 	    foreach($trackingdata as $trrow){
				 	      $all_tracking_data[$trrow["item_id"]][] =  $trrow;
				 	    }
				 	    
				 	}
				 	
				 	
			$billsndrydata = [];
			if(!empty($this->request->getVar('billsndrydata')))
					$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
			
			 

			$challan_type     = $this->request->getVar('challan_type');	//13,14
			$voucher_no       = $voucher_info['comp_vch_no'];

			$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));
			$billsundry_total = 0;
			if(count($billsndrydata))
					$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
			
			// save into gstroutsup table
			$gstroutsup_insert_data = array("outsup_rev_chg"=>0,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>0);
			$this->TransactionModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
				
				
			if($vch_subtype_id == 13) // without sale
			{
					$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
					foreach ($data as $key => $value) {
							if($value['master_id_type'] == 'acc'){
									$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
							}
							if($value['master_id_type'] == 'itm'){
									$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
										$this->TransactionModel->delete_tracking_data($value['master_id'],$value['txn_id']);
							}
							if($value['master_id_type'] == 'bsd'){
									$this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
							}
					}
					$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
					$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
					$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
					$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
					 $this->TransactionModel->delete_all_narrations($voucher_txn_id);


					$voucher_tag = 'DCESDUE'.$voucher_no;

					// update voucher consolidated entry
				  $insert_data     = array(
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_date"				=> $voucher_date,
							"mat_cent_id"					=> $matrcntr_id,
							"voucher_tag"				  => $voucher_tag,
							"currency_id"				=> $currency_id,
					);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

			  	// Add company txn master entry for party oth
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
							'comp_id'            	   => $this->company_id,
							'acc_oth_txn_date'       => $voucher_date,
							'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
							'acc_oth_txn_drcr'       => 'd',
							'voucher_type_id'    	   => $voucher_type_id,
							'comp_vch_series_no' 	   => $voucher_series,
							'acc_id'             	   => $party_id,						
							'voucher_txn_id'     	   => $voucher_txn_id,
							'acc_oth_txn_status' 	   => $voucher_txn_id, //changed from $voucher_no
							'bo_id'					 				 => 0,
							'txn_id'				         => $txn_id,
							'acc_oth_txn_duedate' 	 => date('Y-m-d'),
							'acc_oth_txn_tag'		     => 'DCESDUE',
					];
					$this->TransactionModel->add_acc_oth_data($insert_data);

					$item_account_array = [];
			    foreach($itmsdata as $item_row)
			    {
							$txn_data = array(
								"comp_id"								=> $this->company_id,
								"comp_vch_series_id"		=> $voucher_series,
								"voucher_txn_id" 				=> $voucher_txn_id,
								"master_id" 						=> $item_row['item_id'],
								'master_id_type' 				=> 'itm'
							);
							$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
							
								// save tracking data 
									if(isset($all_tracking_data[$item_row['item_id']])){
									     $trackingdata = $all_tracking_data[$item_row['item_id']];
									    if(!empty($trackingdata))
				                          	{
				    			            foreach ($trackingdata as $key => $value) {

				    					$tracking_id = 0;
            			                if($value['tracking_no'] !='UNDEFINED' && $value['tracking_no'] !='')
            			                {
            			                    $tracking_master_data = [
            			                        'tracking_no' 		  => $value['tracking_no'],
            			                        'txn_id'              => $txn_id,
            			                        'item_id'  			  => $value['item_id'],
            			                        'unit_id'             => $value['tracking_uom_id']
            			                    ];
            			                    $tracking_id = $this->TransactionModel->add_tracking_master($tracking_master_data);
            			                      }
			               
            				    			   }
				                        	}
									}
									

							$insert_data   = array(
									'comp_id'             => $this->company_id,
									'item_txn_date'       => $voucher_date,
									'item_txn_amount'     => $item_row['item_total_amount'],
									'item_txn_drcr'       => 'c',
									'item_txn_qty'        => $item_row['item_qty'],
									'description'       => $item_row['description'],
									// 'comp_vch_series_no'  => $voucher_no,
									'item_id'             => $item_row['item_id'],
									'voucher_txn_id'      => $voucher_txn_id,
									'voucher_type_id'     => $voucher_type_id,
									'mat_cent_id'         => $matrcntr_id,
									'bo_id'				  			=> 0,
									'txn_id'			  => $txn_id,
									"item_unit"			  => $item_row['item_unit_id'],
									"item_bal_qty"		  => 0,
									"item_avail"  		  => 1,
									"batch_id"  		  => 0,

							   );	
							$this->TransactionModel->add_itm_txn_data($insert_data);


							$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
							$account_id  =  $item_info['item_sales_acc'];

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
								'acc_oth_txn_drcr'     => 'c',
								'voucher_type_id'      => $voucher_type_id,
								'comp_vch_series_no'   => $voucher_series,
								'acc_id'               => $account_id,						
								'voucher_txn_id'       => $voucher_txn_id,
								'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
								'bo_id'				   => 0,
								'txn_id'			   => $txn_id,
								'acc_oth_txn_duedate'  => date('Y-m-d'),
								'acc_oth_txn_tag'	   => '',
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
			                
			               
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                    	'comp_id'             => $this->company_id,
														'item_txn_date'       => $voucher_date,
														'item_txn_amount'     => 0,
														'item_txn_drcr'       => 'c',
														'item_txn_qty'        => $value['batch_qty'],
														'description'         => '',
														'item_id'             => $value['item_id'],
														'voucher_txn_id'      => $voucher_txn_id,
														'voucher_type_id'     => $voucher_type_id,
														'mat_cent_id'         => $matrcntr_id,
														'bo_id'				  			=> 0,
														'txn_id'			  			=> 0,
														"item_unit"			  		=> $value['batch_uom_id'],
														"item_bal_qty"		  	=> 0,
														"item_avail"  		  	=> 0,
														"batch_id"  		  		=> $batch_id,
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
						      		$billsundry_drcr = 'c';
						      else
						      	  $billsundry_drcr = 'd';

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

			    //check sale vouchers against this delivery challan
					$count = $this->TransactionModel->count_vouchers_by_tag(18,$voucher_tag);
					if($count > 0)
					{
						
					  $summary = $this->TransactionModel->get_party_oth_summary($voucher_txn_id,'DCES');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($voucher_txn_id,'DCES',"DCESDEF");
			      
			      if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($voucher_txn_id,'DCES',"DCESSUR");

			      if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($voucher_txn_id,'DCES',"DCESEXE");
					}
			}
			if($vch_subtype_id == 14) // against sale
			{
					$sale_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');

					$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
					foreach ($data as $key => $value) {
							if($value['master_id_type'] == 'acc'){
									$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
							}
							if($value['master_id_type'] == 'itm'){
									$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
										$this->TransactionModel->delete_tracking_data($value['master_id'],$value['txn_id']);
							}
							if($value['master_id_type'] == 'bsd'){
									$this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
							}
					}
					$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
					$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
					$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
					$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
					 $this->TransactionModel->delete_all_narrations($voucher_txn_id);

					$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18);
	    		$voucher_tag = 'SEDCDUE'.$sale_voucher['comp_vch_no'];; //voucher no of sale

	    		// update voucher consolidated entry
				  $insert_data     = array(
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_date"				=> $voucher_date,
							"mat_cent_id"					=> $matrcntr_id,
							"voucher_tag"				  => $voucher_tag,
							"currency_id"				=> $currency_id,
					);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

				  //Add crsref 
				$insert_data = [
						'acct_crs_id_type'		   => '',
						'comp_id'				         => $this->company_id,
						'txn_id'				         => '',
						'voucher_txn_id'		     => $voucher_txn_id,
						'bo_id'					         => 0,
						'acc_cross_ref_type'	   => 'SALEINV',
						'acc_cross_ref_data'	   => $sale_voucher_id,
						'acc_cross_logdate'   	 => date('Y-m-d'),
				];
				$this->TransactionModel->add_acc_crsref_data($insert_data);

				// Add company txn master entry for party oth
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
					'comp_id'            	 	 => $this->company_id,
					'acc_oth_txn_date'       => $voucher_date,
					'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
					'acc_oth_txn_drcr'       => 'c',  // CHANGED
					'voucher_type_id'    	   => $voucher_type_id,
					'comp_vch_series_no' 	   => $voucher_series,
					'acc_id'             	   => $party_id,						
					'voucher_txn_id'     	   => $voucher_txn_id,
					'acc_oth_txn_status' 	   => $sale_voucher_id,
					'bo_id'					 				 => 0,
					'txn_id'				 				 => $txn_id,
					'acc_oth_txn_duedate' 	 => date('Y-m-d'),
					'acc_oth_txn_tag'		     => 'SEDCDUE', //what will be it ?
				];
				$this->TransactionModel->add_acc_oth_data($insert_data);

				$item_account_array = [];
				foreach($itmsdata as $item_row)
			    {
							$txn_data = array(
								"comp_id"								=> $this->company_id,
								"comp_vch_series_id"		=> $voucher_series,
								"voucher_txn_id" 				=> $voucher_txn_id,
								"master_id" 						=> $item_row['item_id'],
								'master_id_type' 				=> 'itm'
							);
							$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
								// save tracking data 
									if(isset($all_tracking_data[$item_row['item_id']])){
									     $trackingdata = $all_tracking_data[$item_row['item_id']];
									    if(!empty($trackingdata))
				                          	{
				    			            foreach ($trackingdata as $key => $value) {

				    					$tracking_id = 0;
            			                if($value['tracking_no'] !='UNDEFINED' && $value['tracking_no'] !='')
            			                {
            			                    $tracking_master_data = [
            			                        'tracking_no' 		  => $value['tracking_no'],
            			                        'txn_id'              => $txn_id,
            			                        'item_id'  			  => $value['item_id'],
            			                        'unit_id'             => $value['tracking_uom_id']
            			                    ];
            			                    $tracking_id = $this->TransactionModel->add_tracking_master($tracking_master_data);
            			                      }
			               
            				    			   }
				                        	}
									}

							$insert_data   = array(
									'comp_id'             => $this->company_id,
									'item_txn_date'       => $voucher_date,
									'item_txn_amount'     => $item_row['item_total_amount'],
									'item_txn_drcr'       => 'c',
									'item_txn_qty'        => $item_row['item_qty'],
									'description'       => $item_row['description'],
									// 'comp_vch_series_no'  => $voucher_no,
									'item_id'             => $item_row['item_id'],
									'voucher_txn_id'      => $voucher_txn_id,
									'voucher_type_id'     => $voucher_type_id,
									'mat_cent_id'         => $matrcntr_id,
									'bo_id'				  			=> 0,
									'txn_id'			  => $txn_id,
									"item_unit"			  => $item_row['item_unit_id'],
									"item_bal_qty"		  => 0,
									"item_avail"  		  => 1,
									"batch_id"  		  => 0,

							   );	
							$this->TransactionModel->add_itm_txn_data($insert_data);

							$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
							$account_id  =  $item_info['item_sales_acc'];

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
							'acc_oth_txn_drcr'     => 'c',
							'voucher_type_id'      => $voucher_type_id,
							'comp_vch_series_no'   => $voucher_series,
							'acc_id'               => $account_id,						
							'voucher_txn_id'       => $voucher_txn_id,
							'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
							'bo_id'				   => 0,
							'txn_id'			   => $txn_id,
							'acc_oth_txn_duedate'  => date('Y-m-d'),
							'acc_oth_txn_tag'	   => '',
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
			                
			               
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                    	'comp_id'             => $this->company_id,
														'item_txn_date'       => $voucher_date,
														'item_txn_amount'     => 0,
														'item_txn_drcr'       => 'c',
														'item_txn_qty'        => $value['batch_qty'],
														'description'         => '',
														'item_id'             => $value['item_id'],
														'voucher_txn_id'      => $voucher_txn_id,
														'voucher_type_id'     => $voucher_type_id,
														'mat_cent_id'         => $matrcntr_id,
														'bo_id'				  			=> 0,
														'txn_id'			  			=> 0,
														"item_unit"			  		=> $value['batch_uom_id'],
														"item_bal_qty"		  	=> 0,
														"item_avail"  		  	=> 0,
														"batch_id"  		  		=> $batch_id,
			                    ];
			                    $this->TransactionModel->add_batch_txn($insert_data);
			                }
				    			}
				    	}
				    	
			    $summary = $this->TransactionModel->get_party_oth_summary($sale_voucher_id,'SEDC');
			    $cr_total = $summary['cr_total'];
			    $dr_total = $summary['dr_total'];

			    if($cr_total > $dr_total)  
		      		$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDEF");
		      
				if($cr_total < $dr_total)
				 	$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCSUR");

				if($cr_total == $dr_total)
						$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCEXE");

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
								$billsundry_drcr = 'c';
						else
							  $billsundry_drcr = 'd';

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
			}
			if($vch_subtype_id == 22) // against dn
			{
				$debitnote_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'DRNTINV');

				$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
				foreach ($data as $key => $value) {
						if($value['master_id_type'] == 'acc'){
								$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
						}
						if($value['master_id_type'] == 'itm'){
								$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
									$this->TransactionModel->delete_tracking_data($value['master_id'],$value['txn_id']);
						}
						if($value['master_id_type'] == 'bsd'){
								$this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
						}
				}
				$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
				$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
				$this->TransactionModel->delete_all_narrations($voucher_txn_id);

				$debitnote_voucher = $this->TransactionModel->get_voucher_cons_info($debitnote_voucher_id, 3); //V type id of dn
    		$voucher_tag = $debitnote_voucher['voucher_tag']; //voucher no of creditnote either PESIDUE or ICEPDUE

    		// update voucher consolidated entry
			  $insert_data     = array(
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_date"				=> $voucher_date,
						"mat_cent_id"					=> $matrcntr_id,
						"voucher_tag"				  => $voucher_tag,
						"currency_id"				=> $currency_id,
				);
			  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

			  //Add crsref 
				$insert_data = [
						'acct_crs_id_type'		   => '',
						'comp_id'				         => $this->company_id,
						'txn_id'				         => '',
						'voucher_txn_id'		     => $voucher_txn_id,
						'bo_id'					         => 0,
						'acc_cross_ref_type'	   => 'DRNTINV',
						'acc_cross_ref_data'	   => $debitnote_voucher_id,
						'acc_cross_logdate'   	 => date('Y-m-d'),
				];
				$this->TransactionModel->add_acc_crsref_data($insert_data);

				$this->TransactionModel->delete_acc_crsref_data_by_tag($debitnote_voucher_id,'PESODUE');

				if($voucher_tag != '')
				{
						if(str_contains($voucher_tag, 'PESI')){
					    $acc_oth_txn_tag = 'PESIDUE';
					    $acc_oth_txn_status = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
					    $acc_oth_txn_drcr = 'c';
				    }
				    if(str_contains($voucher_tag, 'ICEP')){
				    	$acc_oth_txn_tag = 'ICEPDUE';
				    	$purchase_voucher_id = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
				    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($purchase_voucher_id, 'INWCHAL'); //inward_challan_id
				    	$acc_oth_txn_drcr = 'd';
				    }

						// Add company txn master entry for party oth
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
							'comp_id'            	 	 => $this->company_id,
							'acc_oth_txn_date'       => $voucher_date,
							'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
							'acc_oth_txn_drcr'       => $acc_oth_txn_drcr,  // CHANGED
							'voucher_type_id'    	   => $voucher_type_id,
							'comp_vch_series_no' 	   => $voucher_series,
							'acc_id'             	   => $party_id,						
							'voucher_txn_id'     	   => $voucher_txn_id,
							'acc_oth_txn_status' 	   => $acc_oth_txn_status,
							'bo_id'					 				 => 0,
							'txn_id'				 				 => $txn_id,
							'acc_oth_txn_duedate' 	 => date('Y-m-d'),
							'acc_oth_txn_tag'		     => $acc_oth_txn_tag, //what will be it ?
						];
						$this->TransactionModel->add_acc_oth_data($insert_data);
				}
				
				$item_account_array = [];
				foreach($itmsdata as $item_row)
		    {
						$txn_data = array(
							"comp_id"								=> $this->company_id,
							"comp_vch_series_id"		=> $voucher_series,
							"voucher_txn_id" 				=> $voucher_txn_id,
							"master_id" 						=> $item_row['item_id'],
							'master_id_type' 				=> 'itm'
						);
						$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
							// save tracking data 
									if(isset($all_tracking_data[$item_row['item_id']])){
									     $trackingdata = $all_tracking_data[$item_row['item_id']];
									    if(!empty($trackingdata))
				                          	{
				    			            foreach ($trackingdata as $key => $value) {

				    					$tracking_id = 0;
            			                if($value['tracking_no'] !='UNDEFINED' && $value['tracking_no'] !='')
            			                {
            			                    $tracking_master_data = [
            			                        'tracking_no' 		  => $value['tracking_no'],
            			                        'txn_id'              => $txn_id,
            			                        'item_id'  			  => $value['item_id'],
            			                        'unit_id'             => $value['tracking_uom_id']
            			                    ];
            			                    $tracking_id = $this->TransactionModel->add_tracking_master($tracking_master_data);
            			                      }
			               
            				    			   }
				                        	}
									}

						$insert_data   = array(
								'comp_id'             => $this->company_id,
								'item_txn_date'       => $voucher_date,
								'item_txn_amount'     => $item_row['item_total_amount'],
								'item_txn_drcr'       => 'd',
								'item_txn_qty'        => $item_row['item_qty'],
								'description'       => $item_row['description'],
								// 'comp_vch_series_no'  => $voucher_no,
								'item_id'             => $item_row['item_id'],
								'voucher_txn_id'      => $voucher_txn_id,
								'voucher_type_id'     => $voucher_type_id,
								'mat_cent_id'         => $matrcntr_id,
								'bo_id'				  			=> 0,
								'txn_id'			  => $txn_id,
								"item_unit"			  => $item_row['item_unit_id'],
								"item_bal_qty"		  => 0,
								"item_avail"  		  => 1,
								"batch_id"  		  => 0,

						   );	
						$item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);

						$item_info  =  $this->TransactionModel->get_item_info($item_row['item_id']);
						$account_id  =  $item_info['item_sales_acc'];

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
						'acc_oth_txn_drcr'     => 'c',
						'voucher_type_id'      => $voucher_type_id,
						'comp_vch_series_no'   => $voucher_series,
						'acc_id'               => $account_id,						
						'voucher_txn_id'       => $voucher_txn_id,
						'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
						'bo_id'				   => 0,
						'txn_id'			   => $txn_id,
						'acc_oth_txn_duedate'  => date('Y-m-d'),
						'acc_oth_txn_tag'	   => '',
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
			                
			               
			                    if($value['batch_id'] == 0 && $value['batch_no'] == 'UNDEFINED'){
			                        $batch_id = $this->TransactionModel->getUndefinedBatchId($value['item_id']);  
			                    }
			                    else{
			                        $batch_id = $value['batch_id'];
			                        $batch_master_data = [
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
			                        ];
			                        $this->TransactionModel->update_batch_master($batch_id, $batch_master_data);
			                    }
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                    	'comp_id'             => $this->company_id,
														'item_txn_date'       => $voucher_date,
														'item_txn_amount'     => 0,
														'item_txn_drcr'       => 'c',
														'item_txn_qty'        => $value['batch_qty'],
														'description'         => '',
														'item_id'             => $value['item_id'],
														'voucher_txn_id'      => $voucher_txn_id,
														'voucher_type_id'     => $voucher_type_id,
														'mat_cent_id'         => $matrcntr_id,
														'bo_id'				  			=> 0,
														'txn_id'			  			=> 0,
														"item_unit"			  		=> $value['batch_uom_id'],
														"item_bal_qty"		  	=> 0,
														"item_avail"  		  	=> 0,
														"batch_id"  		  		=> $batch_id,
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
					      		$billsundry_drcr = 'c';
					      else
					      	  $billsundry_drcr = 'd'; 

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

		    if(str_contains($voucher_tag, 'PESI'))
		    {
		    	$purchase_voucher_id = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
					$summary = $this->TransactionModel->get_party_oth_summary($purchase_voucher_id,'PESI');
			    $cr_total = $summary['cr_total'];
			    $dr_total = $summary['dr_total'];

			    if($cr_total > $dr_total)  
		      		$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIDEF");
		      
		      	if($cr_total < $dr_total)
		         	$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESISUR");

		      	if($cr_total == $dr_total)
		      		$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIEXE");
		    }
		    if(str_contains($voucher_tag, 'ICEP'))
		    {
		    	$purchase_voucher_id = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');
		    	$inward_challan_id = $this->TransactionModel->get_crsref($purchase_voucher_id, 'INWCHAL');

		    	$summary = $this->TransactionModel->get_party_oth_summary($inward_challan_id,'ICEP');
			    $cr_total = $summary['cr_total'];
			    $dr_total = $summary['dr_total'];

			    if($cr_total > $dr_total)  
		      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDEF");
		      
		      	if($cr_total < $dr_total)
		         	$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPSUR");

		      	if($cr_total == $dr_total)
		      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPEXE");
		    }
			}
			// return redirect()->to($this->base_url.'delivery_challan/add');
			return json_encode(['status' => true, 'message' => 'Voucher Updated']);
		}
         $data['gstroutsup_info']            = $this->TransactionModel->gstroutsup_info($voucher_txn_id);
		$allbos           =  $this->TransactionModel->all_mig_bo_lists();
			$data['allbos']   = $allbos;
		
		if($vch_subtype_id == 13) // without sale
		{
			$party_transaction 	= $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
			$data['party_id'] 					= $party_transaction['acc_id'];
			$data['narration'] 					= $party_transaction['acc_oth_txn_narr'];
			$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];

			// $data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
			$data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id);
			$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id,true);
			$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
				

			$data['message_output']   = $this->message_output;
			$data['base_url']       	= $this->base_url; 
			$data['folder_path']    	= $this->folder_path;
			$data['party_dropdown']   = $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['items_list']     					= $this->TransactionModel->items_list();	
			$data['units_list']     					= $this->TransactionModel->units_dropdown();
			 $data['get_narration_info']     = $get_narration_info;  

			$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
			$data['voucher_no'] 				= $voucher_info['comp_vch_no'];
			$data['voucher_date'] 			= date('d-m-Y', strtotime($voucher_info['voucher_date']));
			$data['voucher_txn_id']     = $voucher_txn_id;
			$data['vch_subtype']     		= $this->TransactionModel->get_vch_subtype($vch_subtype_id);
			 $data['get_narration_info']     = $get_narration_info; 
			 $data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);
	       $data['voucher_type_id']                = $voucher_type_id;
		   
		   $item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
           $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

	       	$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
            $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
      $data['currency_list'] = $this->TransactionModel->get_currency_list();
      $data['currency_id'] = $voucher_info['currency_id'];

			return view($this->folder_path.'delivery_challan/edit_challan',$data);
		}
		if($vch_subtype_id == 14) // against sale
		{
			$party_transaction 	= $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
			$data['party_id'] 					= $party_transaction['acc_id'];
			$data['narration'] 					= $party_transaction['acc_oth_txn_narr'];
			$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];

			// $data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
			$data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id);
			$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id,true);
			 $data['get_narration_info']     = $get_narration_info;  
          
			$data['message_output']   = $this->message_output;
			$data['base_url']       	= $this->base_url; 
			$data['folder_path']    	= $this->folder_path;
			$data['party_dropdown']   = $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['items_list']     					= $this->TransactionModel->items_list();	
			$data['units_list']     					= $this->TransactionModel->units_dropdown();

			$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
			$data['voucher_no'] 				= $voucher_info['comp_vch_no'];
			$data['voucher_date'] 			= date('d-m-Y', strtotime($voucher_info['voucher_date']));
			$data['voucher_txn_id']     = $voucher_txn_id;
			$data['vch_subtype']     		= $this->TransactionModel->get_vch_subtype($vch_subtype_id);

			$sale_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');
			$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);
				$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
			
			$p_voucher_info = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, '18');
			$p_voucher_no = $p_voucher_info['comp_vch_no'];
			$data['against'] = 'Voucher No. '.$p_voucher_no;
			 $data['get_narration_info']     = $get_narration_info; 
			 
			 $item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
			$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

			 $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
            $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
           $data['voucher_type_id']                = $voucher_type_id;
        $data['currency_list'] = $this->TransactionModel->get_currency_list();
      	$data['currency_id'] = $voucher_info['currency_id'];   

			return view($this->folder_path.'delivery_challan/edit_challan',$data);
		}
		if($vch_subtype_id == 22) // against dn
		{
			$party_transaction 	= $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
			$data['party_id'] 					= $party_transaction['acc_id'];
			$data['narration'] 					= $party_transaction['acc_oth_txn_narr'];
			$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];
			 $data['get_narration_info']     = $get_narration_info;  

			// $data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
			$data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id);
			$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id,true);

			$data['message_output']   = $this->message_output;
			$data['base_url']       	= $this->base_url; 
			$data['folder_path']    	= $this->folder_path;
			$data['party_dropdown']   = $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['items_list']     					= $this->TransactionModel->items_list();	
			$data['units_list']     					= $this->TransactionModel->units_dropdown();
			
				$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
			$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);

			$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
			$data['voucher_no'] 				= $voucher_info['comp_vch_no'];
			$data['voucher_date'] 			= date('d-m-Y', strtotime($voucher_info['voucher_date']));
			$data['voucher_txn_id']     = $voucher_txn_id;
			$data['vch_subtype']     		= $this->TransactionModel->get_vch_subtype($vch_subtype_id);

			$debitnote_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'DRNTINV');
						
			$dn_voucher_info = $this->TransactionModel->get_voucher_cons_info($debitnote_voucher_id, '3');
			$dn_voucher_no = $dn_voucher_info['comp_vch_no'];
			$data['against'] = 'Voucher No. '.$dn_voucher_no;
			 $data['get_narration_info']     = $get_narration_info; 

			$data['editable'] = false;
			$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
			$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

			$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
            $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
            $data['voucher_type_id']                = $voucher_type_id;
      $data['currency_list'] = $this->TransactionModel->get_currency_list();
      $data['currency_id'] = $voucher_info['currency_id'];

			return view($this->folder_path.'delivery_challan/edit_challan',$data);
		}
	}

  public function delete($voucher_txn_id)
	{
	 	$voucher_type_id    = "7";

		$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
		if(!empty($voucher_info)){

			$vch_subtype_id = $voucher_info['vch_subtype_id'];

			if($vch_subtype_id == 13){// check against sale
				$check = $this->TransactionModel->check_crsref($voucher_txn_id, 'DELCHAL');
				if($check){
					return json_encode(['status' => false, 'message' => 'Delivery Challan has linked Sale Voucher']);
					echo "Delivery Challan has linked Sale Voucher";
					exit;
				}
			}

			if($vch_subtype_id == 14){// against sale
				$sale_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');
			}
			if($vch_subtype_id == 22){// against debit note
				$debitnote_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'DRNTINV');
			}

			$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
			foreach ($data as $key => $value) {
					if($value['master_id_type'] == 'acc'){
							$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
					}
					if($value['master_id_type'] == 'itm'){
							$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
					}
					if($value['master_id_type'] == 'bsd'){
							$this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
					}
			}
			$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
			$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
			$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
			$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
			$this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);
			$this->TransactionModel->delete_all_narrations($voucher_txn_id);

			if($vch_subtype_id == 14 && $sale_voucher_id){// against sale
					$voucher_tag = $voucher_info['voucher_tag'];
					$count = $this->TransactionModel->count_vouchers_by_tag($voucher_type_id,$voucher_tag);
					if($count > 0)
					{
						
					  $summary = $this->TransactionModel->get_party_oth_summary($sale_voucher_id,'SEDC');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDEF");
			      
			      if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCSUR");

			      if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCEXE");
					}
					else
					{
							$this->TransactionModel->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDUE");
					}
			}
			if($vch_subtype_id == 22 && $debitnote_voucher_id){// against debit note
					$voucher_tag = $voucher_info['voucher_tag'];
					$purchase_voucher_id = $this->TransactionModel->get_crsref($debitnote_voucher_id, 'PURCINV');

				  if(str_contains($voucher_tag, 'PESI'))
			    {
						$summary = $this->TransactionModel->get_party_oth_summary($purchase_voucher_id,'PESI');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIDEF");
			      
			      	if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESISUR");

			      	if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIEXE");
			    }
			    if(str_contains($voucher_tag, 'ICEP'))
			    {
			    	$inward_challan_id = $this->TransactionModel->get_crsref($purchase_voucher_id, 'INWDCHAL');

			    	$summary = $this->TransactionModel->get_party_oth_summary($inward_challan_id,'ICEP');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDEF");
			      
			      	if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPSUR");

			      	if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPEXE");
			    }
			    $insert_data = [
							'acct_crs_id_type'		=> 3,
							'comp_id'							=> $this->company_id,
							'txn_id'							=> '',
							'voucher_txn_id'			=> $debitnote_voucher_id,
							'bo_id'								=> 0,
							'acc_cross_ref_type'	=> 'PESODUE',
							'acc_cross_ref_data'	=> $purchase_voucher_id,
							'acc_cross_logdate'   => date('Y-m-d'),
					];
					$this->TransactionModel->add_acc_crsref_data($insert_data);
			}
		}

		
		// return redirect()->to($this->base_url.'delivery_challan/add');
		return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
	}
	
}