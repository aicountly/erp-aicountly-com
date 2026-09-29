<?php
namespace App\Controllers\Admin;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\BalancesModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Purchase  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
			$this->BalancesModel  = new BalancesModel();
			$this->TransactionModel  = new TransactionModel();		
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
   

   public function getPurchaseCc()
   {
    	if($this->request->getMethod() == 'post'){

    			$itmsdata = $this->request->getVar('itmsdata'); 

    			$cc_accounts = [];
			    foreach($itmsdata as $key => $value) {
			    	 
			    		$item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
							$account_id  =  $item_info['item_pur_acc'];
							$amount = $value['item_total_amount'];

							$index = array_search($account_id, array_column($cc_accounts, 'account_id'));
							if($index != ''){
								$cc_accounts[$index]['amount'] += $amount;
							}
							else{
								$account = $this->TransactionModel->get_account_info($account_id);
								$account_name = $account['acc_name'];

									$cc_accounts[] = [
											"account_id"    => $account_id,
                      "account_name"  => $account_name,
                      "drcr"          => 'D',
                      "amount"        => $amount,
									];
							}                
			    }
			    $data['cc_accounts'] = $cc_accounts;
          $data['cc_list'] = $this->TransactionModel->get_cc();
          echo json_encode(['status' => true, 'data' => $data]);
      }
   }
   
      
    public function ajax_inward_challan($voucher_txn_id){
    		$status = $this->TransactionModel->check_party_oth_status($voucher_txn_id,'ICEPDUE');
    		if($status)
    		{
    				$data['status'] = true;
    				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, '6');
    				$data['mat_cent_id'] 	= $voucher_info['mat_cent_id'];
    				$data['voucher_series'] = $voucher_info['comp_vch_series_id']; // what to do with it
    				$party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
    				$data['party_id'] = $party_transaction['acc_id'];

    				$data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id);
						$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id);
						return json_encode($data);
    		}
    		return json_encode(['status' => false]);
    }

   	
  
    public function item()
    {
    	$voucher_type_id  = "11";

    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		   //  echo "<pre>";print_r($_POST);exit;

			
    			$rules = [				
						'purchase_date' => [
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

		      $voucher_date        = $this->request->getVar('purchase_date');
		      $voucher_series   = $this->request->getVar('voucher_series'); 
		      $party_id         = $this->request->getVar('party_id'); 
		      $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		      $narration        = $this->request->getVar('narration');  
		      $itmsdata         = json_decode($this->request->getVar('itmsdata'),true); 
		      $voucher_date        = date("Y-m-d", strtotime($voucher_date));
		      $inward_challan_id = $this->request->getVar('inward_challan_id');
		      $currency_id = $this->request->getVar('currency_id');

		      $billsndrydata = [];
				  if(!empty($this->request->getVar('billsndrydata')))
				 		$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
				 
					// echo "<pre>";print_r($billsndrydata);exit;
					$purchase_type    = $this->request->getVar('purchase_type'); // 5,6,7
					$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);

				  $voucher_txn_id = 0;
				  $sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

					$billsundry_total = 0;
					if(count($billsndrydata))
							$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

					$batchdata = [];
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
				 
					$ccdata = [];
				  if(!empty($this->request->getVar('ccdata')))
				 			$ccdata = json_decode($this->request->getVar('ccdata'),true);

				 	$bbbdata = [];
				  if(!empty($this->request->getVar('bbbdata')))
				 			$bbbdata = json_decode($this->request->getVar('bbbdata'),true);
					

			    if($purchase_type == 5) // purchase with stock inward
			    {
				
				     	$voucher_tag= '';

							// add voucher consolidated entry
						  $insert_data     = array(
									"comp_id"			=> $this->company_id,
									"comp_vch_series_id"=> $voucher_series,
									"comp_vch_no"		=> $voucher_no,
									"voucher_type_id"	=> $voucher_type_id,
									"voucher_date"		=> $voucher_date,
									"mat_cent_id"		=> $matrcntr_id,
									"vch_subtype_id"	=> $purchase_type,
									"voucher_tag"		=> $voucher_tag,
									"currency_id"				=> $currency_id,
							);
						  $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
						  // $this->TransactionModel->save_voucher_narration($voucher_txn_id,0,'long',$narration);

							$insert_data = [
				              "comp_id"             => $this->company_id,
				              "comp_vch_series_id"  => $voucher_series,
				              "voucher_txn_id"      => $voucher_txn_id,
				              "master_id"           => $party_id,
				              'master_id_type'      => 'acc'
				              ];

				      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				            
				           
				      $insert_data  = [
											'comp_id'            => $this->company_id,
											'acc_id'             => $party_id,
											'acc_txn_date'       => $voucher_date,
											'acc_txn_amount'     => $sale_total + $billsundry_total,						
											'acc_txn_drcr'       => 'c',
											'comp_vch_series_no' => $voucher_no,
											'posted_on'          => date('Y-m-d H:i:s'),						
											'voucher_txn_id'     => $voucher_txn_id,
											'voucher_type_id'    => $voucher_type_id,
											'txn_id'             => $txn_id,
											'acc_bal'            => 0
						  ];	
						   	   
					    $this->TransactionModel->add_acc_txn_data($insert_data);
					    $this->TransactionModel->update_account_balance($party_id);

					    $item_account_array = [];
					    foreach($itmsdata as $item_row)
					    {

									$txn_data = array(
										"comp_id"				=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 		=> $voucher_txn_id,
										"master_id" 			=> $item_row['item_id'],
										'master_id_type' 		=> 'itm'
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
														'txn_id'            => $txn_id,
														'item_id'  			  	=> $value['item_id'],
														'unit_id'           => $value['tracking_uom_id']
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
											'description'         => $item_row['description'],											
											'item_id'             => $item_row['item_id'],
											'voucher_txn_id'      => $voucher_txn_id,
											'voucher_type_id'     => $voucher_type_id,
											'mat_cent_id'         => $matrcntr_id,
											'bo_id'				  			=> 0,
											'txn_id'			  			=> $txn_id,
											"item_unit"						=> $item_row['item_unit_id'],
											"item_bal_qty"				=> $item_row['item_qty'],
											"item_avail"  				=> 1,
											"batch_id"  				  => 0,										

									   );	
									$this->TransactionModel->add_itm_txn_data($insert_data);

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
										'master_id_type' 		=> 'acc'
								 	);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

									$insert_data  = [
										'comp_id'            => $this->company_id,
										'acc_id'             => $account_id,
										'acc_txn_date'       => $voucher_date,
										'acc_txn_amount'     => $amount,						
										'acc_txn_drcr'       => 'd',
										'comp_vch_series_no' => $voucher_no,
										'posted_on'          => date('Y-m-d H:i:s'),						
										'voucher_txn_id'     => $voucher_txn_id,
										'voucher_type_id'    => $voucher_type_id,
										'txn_id'             => $txn_id,
										'acc_bal'            => 0
								    ];	
									$this->TransactionModel->add_acc_txn_data($insert_data);
									$this->TransactionModel->update_account_balance($account_id);
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
															'comp_id'             => $this->company_id,
															'item_txn_date'       => $voucher_date,
															'item_txn_amount'     => 0,
															'item_txn_drcr'       => 'd',
															'item_txn_qty'        => $value['batch_qty'],										
															'item_id'             => $value['item_id'],
															'voucher_txn_id'      => $voucher_txn_id,
															'voucher_type_id'     => $voucher_type_id,
															'mat_cent_id'         => $matrcntr_id,
															'bo_id'				  			=> 0,
															'txn_id'			  			=> $txn_id,
															"item_unit"						=> $value['batch_uom_id'],
															"item_bal_qty"				=> 0,
															"item_avail"  				=> 0,
															"batch_id"  				  => $batch_id,	
			                    ];
			                    $this->TransactionModel->add_batch_txn($insert_data);
			                }
				    			}
				    	}
				    	/*
				       	if(!empty($trackingdata))
				    	{
				    			foreach ($trackingdata as $key => $value) {

				    					$tracking_id = 0;
			                if($value['tracking_method'] == 'New Ref.')
			                {
			                    $tracking_master_data = [
			                        'tracking_no' 		  => $value['tracking_no'],
			                        'txn_id'              => 0,
			                        'item_id'  			  => $value['item_id'],
			                    ];
			                    $tracking_id = $this->TransactionModel->add_tracking_master($tracking_master_data);
			                }
			                if($value['tracking_method'] == 'Adjustment')
			                {
			                    if($value['tracking_id'] == 0 && $value['tracking_no'] == 'UNDEFINED'){
			                        $tracking_id = $this->TransactionModel->getUndefinedTrackingId($value['item_id']);  
			                    }
			                    else{
			                        $tracking_id = $value['tracking_id'];
			                        
			                    }
			                    
			                }
			                if($tracking_id)
			                {
			                    if($value['tracking_qty']=='')
			                      $tracking_qty = '1';
			                      else
			                      $tracking_qty = $value['tracking_qty'];
			                    $insert_data = [
			                                                "comp_id"			=> $this->company_id,
			                                                "item_oth_txn_date" => $voucher_date, 
															"item_id"			=> $value['item_id'],
															'item_oth_txn_drcr'     => 'd',
															"txn_id"			=> 0,
															"voucher_txn_id"	=> $voucher_txn_id,
															"bo_id"				=> 0,
															"mat_cent_id"		=> $matrcntr_id,
															"item_unit"			=> $value['tracking_uom_id'],
															"item_oth_txn_qty"		=> $tracking_qty,
															"tracking_id"		=> $tracking_id
															
			                    ];
			                    $this->TransactionModel->add_tracking_txn($insert_data);
			                }
				    			}
				    	}	*/
					
			    }

			    if($purchase_type == 6) // purchase without stock inward
			    {
			    	$voucher_tag = 'PESIDUE'.$voucher_no;

			    	// add voucher consolidated entry
						$insert_data     = array(
									"comp_id"			=> $this->company_id,
									"comp_vch_series_id"=> $voucher_series,
									"comp_vch_no"		=> $voucher_no,
									"voucher_type_id"	=> $voucher_type_id,
									"voucher_date"		=> $voucher_date,
									"mat_cent_id"		=> $matrcntr_id,
									"vch_subtype_id"	=> $purchase_type,
									"voucher_tag"		=> $voucher_tag,
									"currency_id"				=> $currency_id,
						);
						$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);

						$insert_data = [
								  "comp_id"             => $this->company_id,
								  "comp_vch_series_id"  => $voucher_series,
								  "voucher_txn_id"      => $voucher_txn_id,
								  "master_id"           => $party_id,
								  'master_id_type'      => 'acc'
								  ];

	            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
	            
	           
	            $insert_data  = [
									'comp_id'            => $this->company_id,
									'acc_id'             => $party_id,
									'acc_txn_date'       => $voucher_date,
									'acc_txn_amount'     => ($sale_total + $billsundry_total),						
									'acc_txn_drcr'       => 'c',
									'comp_vch_series_no' => $voucher_no,
									'posted_on'          => date('Y-m-d H:i:s'),						
									'voucher_txn_id'     => $voucher_txn_id,
									'voucher_type_id'    => $voucher_type_id,
									'txn_id'             => $txn_id,
									'acc_bal'            => 0
						   ];	
						   	   
					    $this->TransactionModel->add_acc_txn_data($insert_data);
					    $this->TransactionModel->update_account_balance($party_id);

					    // Add company txn master entry for party oth
							 $txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $party_id,
									'master_id_type' 			=> 'acc'
							 	);
			           	$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

							// Add party oth txn entry 
					    $insert_data  = [
									'comp_id'            	 => $this->company_id,
									'acc_oth_txn_date'     => $voucher_date,
									'acc_oth_txn_amount'   => ($sale_total + $billsundry_total),						
									'acc_oth_txn_drcr'     => 'c',
									'voucher_type_id'    	 => $voucher_type_id,
									'comp_vch_series_no' 	 => $voucher_series,
									'acc_id'             	 => $party_id,						
									'voucher_txn_id'     	 => $voucher_txn_id,
									'acc_oth_txn_status' 	 => $voucher_txn_id, // changed from $voucher_no
									'bo_id'					 			 => 0,
									'txn_id'				 			 => $txn_id,
									'acc_oth_txn_duedate'  => date('Y-m-d'),
									'acc_oth_txn_tag'		 	 => 'PESIDUE',
						];
						$this->TransactionModel->add_acc_oth_data($insert_data);

						$item_account_array = [];
				    foreach($itmsdata as $item_row)
				    {

								$txn_data = array(
									"comp_id"				=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 		=> $voucher_txn_id,
									"master_id" 			=> $item_row['item_id'],
									'master_id_type' 		=> 'ito'
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
									'item_oth_txn_tag'     => '',
									'bo_id'				   			 => 0,
									'txn_id'			   			 => $txn_id,
									'item_unit'            => $item_row['item_unit_id'],
								);
								$this->TransactionModel->add_itm_oth_data($insert_data);
								// no need to update item qty balance

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
								'master_id_type' 		=> 'acc'
						 	);
							$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

							$insert_data  = [
								'comp_id'            => $this->company_id,
								'acc_id'             => $account_id,
								'acc_txn_date'       => $voucher_date,
								'acc_txn_amount'     => $amount,						
								'acc_txn_drcr'       => 'd',
								'comp_vch_series_no' => $voucher_no,
								'posted_on'          => date('Y-m-d H:i:s'),						
								'voucher_txn_id'     => $voucher_txn_id,
								'voucher_type_id'    => $voucher_type_id,
								'txn_id'             => $txn_id,
								'acc_bal'            => 0
						    ];	
							$this->TransactionModel->add_acc_txn_data($insert_data);
							$this->TransactionModel->update_account_balance($account_id);
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

			    }

			    if($purchase_type == 7) // purchase against inward challan
			    {
				    	$inward_challan = $this->TransactionModel->get_voucher_cons_info($inward_challan_id, 6);
				    	$voucher_tag = 'ICEPDUE'.$inward_challan['comp_vch_no'];; //voucher no of inward challan

				    	// add voucher consolidated entry
							$insert_data     = array(
										"comp_id"			=> $this->company_id,
										"comp_vch_series_id"=> $voucher_series,
										"comp_vch_no"		=> $voucher_no,
										"voucher_type_id"	=> $voucher_type_id,
										"voucher_date"		=> $voucher_date,
										"mat_cent_id"		=> $matrcntr_id,
										"vch_subtype_id"	=> $purchase_type,
										"voucher_tag"		=> $voucher_tag,
										"currency_id"				=> $currency_id,
								);
							$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);

							$insert_data = [
									'acct_crs_id_type'		=> '',
									'comp_id'				=> $this->company_id,
									'txn_id'				=> '',
									'voucher_txn_id'		=> $voucher_txn_id,
									'bo_id'					=> 0,
									'acc_cross_ref_type'	=> 'INWCHAL',
									'acc_cross_ref_data'	=> $inward_challan_id,
									'acc_cross_logdate'   	=> date('Y-m-d'),
							];
							$this->TransactionModel->add_acc_crsref_data($insert_data);

							$insert_data = [
				              "comp_id"             => $this->company_id,
				              "comp_vch_series_id"  => $voucher_series,
				              "voucher_txn_id"      => $voucher_txn_id,
				              "master_id"           => $party_id,
				              'master_id_type'      => 'acc'
				              ];

				      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				            
				           
				      $insert_data  = [
												'comp_id'            => $this->company_id,
												'acc_id'             => $party_id,
												'acc_txn_date'       => $voucher_date,
												'acc_txn_amount'     => ($sale_total + $billsundry_total),						
												'acc_txn_drcr'       => 'c',
												'comp_vch_series_no' => $voucher_no,
												'posted_on'          => date('Y-m-d H:i:s'),						
												'voucher_txn_id'     => $voucher_txn_id,
												'voucher_type_id'    => $voucher_type_id,
												'txn_id'             => $txn_id,
												'acc_bal'            => 0
							 ];	
							   	   
						  $this->TransactionModel->add_acc_txn_data($insert_data);
						  $this->TransactionModel->update_account_balance($party_id);

						    // Add company txn master entry for party oth
							$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $party_id,
									'master_id_type' 			=> 'acc'
							 	);
				      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

							// Add party oth txn entry 
						    $insert_data  = [
								'comp_id'            	 	 => $this->company_id,
								'acc_oth_txn_date'       => $voucher_date,
								'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
								'acc_oth_txn_drcr'       => 'd',  // CHANGED
								'voucher_type_id'    	   => $voucher_type_id,
								'comp_vch_series_no' 	   => $voucher_series,
								'acc_id'             	   => $party_id,						
								'voucher_txn_id'     	   => $voucher_txn_id,
								'acc_oth_txn_status' 	   => $inward_challan_id, //should be voucher no of inward challan
								'bo_id'					 				 => 0,
								'txn_id'				 				 => $txn_id,
								'acc_oth_txn_duedate' 	 => date('Y-m-d'),
								'acc_oth_txn_tag'		     => 'ICEPDUE', //what will be it ?
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);


							$item_account_array = [];
					    foreach($itmsdata as $item_row)
					    {

									$txn_data = array(
										"comp_id"				=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 		=> $voucher_txn_id,
										"master_id" 			=> $item_row['item_id'],
										'master_id_type' 		=> 'ito'
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
										'item_oth_txn_tag'     => '',
										'bo_id'				   			 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
									);
									$this->TransactionModel->add_itm_oth_data($insert_data);
									// no need to update item qty balance

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
										'master_id_type' 		=> 'acc'
								 	);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

									$insert_data  = [
										'comp_id'            => $this->company_id,
										'acc_id'             => $account_id,
										'acc_txn_date'       => $voucher_date,
										'acc_txn_amount'     => $amount,						
										'acc_txn_drcr'       => 'd',
										'comp_vch_series_no' => $voucher_no,
										'posted_on'          => date('Y-m-d H:i:s'),						
										'voucher_txn_id'     => $voucher_txn_id,
										'voucher_type_id'    => $voucher_type_id,
										'txn_id'             => $txn_id,
										'acc_bal'            => 0
								    ];	
									$this->TransactionModel->add_acc_txn_data($insert_data);
									$this->TransactionModel->update_account_balance($account_id);
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

			    if(count($billsndrydata) && $voucher_txn_id)
			    {
							foreach($billsndrydata as $value)
							{
									$txn_data = array(
										"comp_id"							=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 			=> $voucher_txn_id,
										"master_id" 					=> $value['billsundry_id'],
										'master_id_type' 			=> 'bsd'
								 	);
						      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

						      if($value['billsundry_amount'] >= 0)
						      		$billsundry_drcr = 'd';
						      else
						      	  $billsundry_drcr = 'c';

						      $insert_data  = array(
						      		"comp_id"							=> $this->company_id,
						      		"sundry_txn_date"			=> $voucher_date,
						      		"sundry_txn_amount"		=> abs($value['billsundry_amount']),
									"sundry_txn_drcr"			=> $billsundry_drcr,			               
									"comp_vch_name"				=> $voucher_no, // ?
									"comp_vch_series_no"	=> $voucher_no,
									"bill_sundry_id"			=> $value['billsundry_id'],
									"sundry_bal"					=> 0,
									"voucher_txn_id"			=> $voucher_txn_id, 
									"voucher_type_id"			=> $voucher_type_id,
									'txn_id'							=> $txn_id,
									'sundry_tag_rate'			=> $value['billsundry_rate']
			              );
			           $this->TransactionModel->add_sundry_txn_data($insert_data);	
							}
			    }

			    if(empty($ccdata))
			    {
			    		$ccdata = [];
					    foreach($itmsdata as $key => $value) {
					    	 
					    		$item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
									$account_id  =  $item_info['item_pur_acc'];
									$amount = $value['item_total_amount'];

									$index = array_search($account_id, array_column($ccdata, 'account_id'));
									if($index != ''){
										$ccdata[$index]['cc_txn_amt'] += $amount;
									}
									else{
											$ccdata[] = [
				                  "cc_id"       => 1,
				                  "account_id"  => $account_id,
				                  "cc_name"     => 'UNDEFINED',
				                  "cc_txn_amt"  => $amount,
				                  "cc_txn_drcr" => 'D',
				                  "cc_txn_narr" => ''
											];
									}                
					    }
			    }
			    
			    if(count($ccdata))
			    {
							foreach($ccdata as $value)
							{
								  $cc_txn_data = [
	                    'cc_id'              => $value['cc_id'],
	                    'comp_id'            => $this->company_id,
	                    'acc_id'             => $value['account_id'],
	                    'voucher_txn_id'     => $voucher_txn_id,
	                    'voucher_type_id'    => $voucher_type_id,
	                    'comp_vch_series_id' => $voucher_series,
	                    'cc_txn_date'        => $voucher_date,
	                    'cc_txn_drcr'        => $value['cc_txn_drcr'],
	                    'cc_txn_amt'         => $value['cc_txn_amt'],
	                    'cc_txn_bal'         => 0,
	                    'cc_txn_narr'        => $value['cc_txn_narr'],
	                ];
	                $this->TransactionModel->add_cc_txn($cc_txn_data);
							}
					}

					$is_bbb = $this->TransactionModel->check_bbb_account($party_id); 
					if($is_bbb)
					{
							if(empty($bbbdata))
					    {
					    	$bbbdata = [];

								$bbbdata[] = [
		                "account_id"=> $party_id,
		                "method"    => 'Adjustment',
		                "reference" => 'UNDEFINED',
		                "reference_id" => 0,
		                "amount"    => ($sale_total+$billsundry_total),
		                "drcr"      => 'C',
		                "due_date"  => '',
		                "narration" => ''
								];
					    }
					    
					    if(count($bbbdata))
			        {
			            foreach($bbbdata as $key => $value)
			            {
			                $bill_ref_id = 0;
			                $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

			                if($value['method'] == 'New Ref.')
			                {
			                    $bill_master_data = [
			                        'bills_ref_name' => $value['reference'],
			                        'acc_id'         => $value['account_id'],
			                        'bills_status'   => 'pending',
			                        'bill_due_date'  => $bill_due_date,
			                    ];
			                    $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
			                }
			                if($value['method'] == 'Adjustment')
			                {
			                    if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
			                        $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
			                    }
			                    else{
			                        $bill_ref_id = $value['reference_id'];
			                        $bill_master_data = [
			                            'bill_due_date'  => $bill_due_date
			                        ];
			                        $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
			                    }
			                    
			                }
			                if($bill_ref_id)
			                {
			                    $bill_txn_data = [
			                        'bills_ref_id'       => $bill_ref_id,
			                        'comp_id'            => $this->company_id,
			                        'acc_id'             => $value['account_id'],
			                        'voucher_txn_id'     => $voucher_txn_id,
			                        'voucher_type_id'    => $voucher_type_id,
			                        'comp_vch_series_id' => $voucher_series,
			                        'bills_txn_date'     => $voucher_date,
			                        'bills_txn_drcr'     => $value['drcr'],
			                        'bills_txn_amt'      => $value['amount'],
			                        'bills_txn_bal'      => 0,
			                        'bills_txn_narr'     => $value['narration'],
			                    ];
			                    $this->TransactionModel->add_bill_txn($bill_txn_data);
			                }
			            }
			        }
					}

					
			    

		    // return redirect()->to($this->base_url.'purchase/item'); 
					return json_encode(['status' => true, 'message' => 'Data Inserted']);
			}
             $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
	        $data['message_output'] 					= $this->message_output;
			$data['voucher_auto_no']  				    = $this->TransactionModel->get_voucher_no($voucher_type_id);
			$data['base_url']       					= $this->base_url; 
			$data['folder_path']    					= $this->folder_path;
			// $data['items_list']     					= $this->TransactionModel->items_list();
			$data['units_list']     					= $this->TransactionModel->units_dropdown();
			$data['voucher_series_dropdown']          = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['party_dropdown']     			= $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
			$data['sub_type_dropdown']  			= $this->TransactionModel->vch_subtype_dropdown($voucher_type_id);
			$data['purchase_against_challan']       = $this->TransactionModel->against_dropdown('6',array('ICEPDUE','ICEPDEF','PESISUR'));
			$data['billsundry_items']  				= $this->TransactionModel->billsundry_items();
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
            $data['voucher_date']                    = $voucher_detail['last_entry'];
            $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
            $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
            $data['voucher_type_id']                = $voucher_type_id;
            $data['currency_list'] = $this->TransactionModel->get_currency_list();

			return view($this->folder_path.'purchase/add_invoice',$data);	
    }

    public function non_item()
    {
		 	$voucher_type_id       = "11";

		 	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		 		// echo "<pre>";print_r($_POST);exit;

		 			$rules = [				
						'purchase_date' => [
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
		     
		     $voucher_date        = $this->request->getVar('purchase_date');
		     $voucher_date 			 = date("Y-m-d", strtotime($voucher_date));
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $party_id         = $this->request->getVar('party_id');  
		     $narration        = $this->request->getVar('narration'); 
		     $itmsdata         = json_decode($this->request->getVar('itmsdata'),true); 
		     $currency_id = $this->request->getVar('currency_id');
		     
		     $billsndrydata = [];
				  if(!empty($this->request->getVar('billsndrydata')))
				 		$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);

				 	$ccdata = [];
				  if(!empty($this->request->getVar('ccdata')))
				 		$ccdata    = json_decode($this->request->getVar('ccdata'),true);

				 	$bbbdata = [];
				  if(!empty($this->request->getVar('bbbdata')))
				 			$bbbdata = json_decode($this->request->getVar('bbbdata'),true);
				 
					$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);

				  $sale_total = array_sum(array_column($itmsdata, 'amount'));
				  
					$billsundry_total = 0;
					if(count($billsndrydata))
							$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
		     

				  $voucher_tag= '';

					// add voucher consolidated entry
				  $insert_data  = array(
							"comp_id"							=> $this->company_id,
							"comp_vch_series_id"	=> $voucher_series,
							"comp_vch_no"					=> $voucher_no,
							"voucher_type_id"			=> $voucher_type_id,
							"voucher_date"				=> $voucher_date,
							"mat_cent_id"					=> 0,
							"vch_subtype_id"			=> 0,
							"voucher_tag"					=> '',
							"currency_id"					=> $currency_id,
					);
				  $voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
				  
				 	$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => $party_id,
			              'master_id_type'      => 'acc'
		              ];

		      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
		            
		           
		      $insert_data  = [
										'comp_id'            => $this->company_id,
										'acc_id'             => $party_id,
										'acc_txn_date'       => $voucher_date,
										'acc_txn_amount'     => ($sale_total + $billsundry_total),						
										'acc_txn_drcr'       => 'c',
										'comp_vch_series_no' => $voucher_no,
										'posted_on'          => date('Y-m-d H:i:s'),						
										'voucher_txn_id'     => $voucher_txn_id,
										'voucher_type_id'    => $voucher_type_id,
										'txn_id'             => $txn_id,
										'acc_bal'            => 0
				   ];	
				   	   
			    $this->TransactionModel->add_acc_txn_data($insert_data);
			    $this->TransactionModel->update_account_balance($party_id);
				 	
			    if($itmsdata)
			    {
			        foreach($itmsdata as $item_row)
			        {
			           
	              	$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => $item_row['account_id'],
			              'master_id_type'      => 'acc'
		              ];

		      				$txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);

			           
			           	$insert_data  = [
													'comp_id'            => $this->company_id,
													'acc_id'             => $item_row['account_id'],
													'acc_txn_date'       => $voucher_date,
													'acc_txn_amount'     => $item_row['amount'],						
													'acc_txn_drcr'       => 'd',
													'acc_txn_narr'       => $item_row['description'],
													'comp_vch_series_no' => $voucher_no,
													'posted_on'          => date('Y-m-d H:i:s'),						
													'voucher_txn_id'     => $voucher_txn_id,
													'voucher_type_id'    => $voucher_type_id,
													'txn_id'             => $txn_id,
													'acc_bal'            => 0
						   		];	
						   	   
						   	$this->TransactionModel->add_acc_txn_data($insert_data);
							 	$this->TransactionModel->update_account_balance($item_row['account_id']);
					   
		      		}
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

		    	if(count($ccdata))
			    {
							foreach($ccdata as $value)
							{
								  $cc_txn_data = [
	                    'cc_id'              => $value['cc_id'],
	                    'comp_id'            => $this->company_id,
	                    'acc_id'             => $value['account_id'],
	                    'voucher_txn_id'     => $voucher_txn_id,
	                    'voucher_type_id'    => $voucher_type_id,
	                    'comp_vch_series_id' => $voucher_series,
	                    'cc_txn_date'        => $voucher_date,
	                    'cc_txn_drcr'        => $value['cc_txn_drcr'],
	                    'cc_txn_amt'         => $value['cc_txn_amt'],
	                    'cc_txn_bal'         => 0,
	                    'cc_txn_narr'        => $value['cc_txn_narr'],
	                ];
	                $this->TransactionModel->add_cc_txn($cc_txn_data);
							}
					}

					$is_bbb = $this->TransactionModel->check_bbb_account($party_id); 
					if($is_bbb)
					{
							if(empty($bbbdata))
					    {
					    	$bbbdata = [];

								$bbbdata[] = [
		                "account_id"=> $party_id,
		                "method"    => 'Adjustment',
		                "reference" => 'UNDEFINED',
		                "reference_id" => 0,
		                "amount"    => ($sale_total+$billsundry_total),
		                "drcr"      => 'C',
		                "due_date"  => '',
		                "narration" => ''
								];
					    }
					    
					    if(count($bbbdata))
			        {
			            foreach($bbbdata as $key => $value)
			            {
			                $bill_ref_id = 0;
			                $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

			                if($value['method'] == 'New Ref.')
			                {
			                    $bill_master_data = [
			                        'bills_ref_name' => $value['reference'],
			                        'acc_id'         => $value['account_id'],
			                        'bills_status'   => 'pending',
			                        'bill_due_date'  => $bill_due_date,
			                    ];
			                    $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
			                }
			                if($value['method'] == 'Adjustment')
			                {
			                    if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
			                        $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
			                    }
			                    else{
			                        $bill_ref_id = $value['reference_id'];
			                        $bill_master_data = [
			                            'bill_due_date'  => $bill_due_date
			                        ];
			                        $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
			                    }
			                    
			                }
			                if($bill_ref_id)
			                {
			                    $bill_txn_data = [
			                        'bills_ref_id'       => $bill_ref_id,
			                        'comp_id'            => $this->company_id,
			                        'acc_id'             => $value['account_id'],
			                        'voucher_txn_id'     => $voucher_txn_id,
			                        'voucher_type_id'    => $voucher_type_id,
			                        'comp_vch_series_id' => $voucher_series,
			                        'bills_txn_date'     => $voucher_date,
			                        'bills_txn_drcr'     => $value['drcr'],
			                        'bills_txn_amt'      => $value['amount'],
			                        'bills_txn_bal'      => 0,
			                        'bills_txn_narr'     => $value['narration'],
			                    ];
			                    $this->TransactionModel->add_bill_txn($bill_txn_data);
			                }
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
										'master_id_type' 			=> 'bsd'
								 	);
						      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

						      if($value['billsundry_amount'] >= 0)
						      		$billsundry_drcr = 'd';
						      else
						      	  $billsundry_drcr = 'c';

						      $insert_data  = array(
						      		"comp_id"							=> $this->company_id,
						      		"sundry_txn_date"			=> $voucher_date,
						      		"sundry_txn_amount"		=> abs($value['billsundry_amount']),
			                "sundry_txn_drcr"			=> $billsundry_drcr,			               
			                "comp_vch_name"				=> $voucher_no, // ?
			                "comp_vch_series_no"	=> $voucher_no,
			                "bill_sundry_id"			=> $value['billsundry_id'],
			                "sundry_bal"					=> 0,
			                "voucher_txn_id"			=> $voucher_txn_id, 
			                "voucher_type_id"			=> $voucher_type_id,
			                'txn_id'							=> $txn_id,
			                'sundry_tag_rate'			=> $value['billsundry_rate']
			              );
			           $this->TransactionModel->add_sundry_txn_data($insert_data);	
							}
			    }
				 
		    	// return redirect()->to($this->base_url.'purchase/non_item');
		    	return json_encode(['status' => true, 'message' => 'Data Inserted']);
		  }
		    $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
			$data['message_output']   = $this->message_output;
			$data['base_url']       = $this->base_url; 
			$data['folder_path']    = $this->folder_path;
			$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
			$data['items_list']     = $this->TransactionModel->items_list();	
			$data['units_list']     = $this->TransactionModel->units_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['party_dropdown']     = $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
			
			$data['voucher_date']                    = $voucher_detail['last_entry'];
            $data['acc_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json');
            $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
            $data['voucher_type_id']                = $voucher_type_id;
            $data['currency_list'] = $this->TransactionModel->get_currency_list();

			return view($this->folder_path.'purchase/add_invoice_without_item',$data);		
    }

    public function edit($voucher_txn_id)
		{	
				$voucher_type_id    = "11";

				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				if(empty($voucher_info)){
					 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
				}
				
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
				
				$vch_subtype_id = $voucher_info['vch_subtype_id'];

				if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		            //echo "<pre>";print_r($_POST);exit;
					$rules = [				
						'purchase_date' => [
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

		      $voucher_date     = $this->request->getVar('purchase_date');
		      $voucher_series   = $this->request->getVar('voucher_series'); 
		      $party_id         = $this->request->getVar('party_id'); 
		      $matrcntr_id      = $this->request->getVar('matrcntr_id'); 
		      $narration        = $this->request->getVar('narration');  
		      $itmsdata         = json_decode($this->request->getVar('itmsdata'),true); 
		      $voucher_date        = date("Y-m-d", strtotime($voucher_date));
		      $inward_challan_id = $this->request->getVar('inward_challan_id');
			  	$currency_id = $this->request->getVar('currency_id');

			  	// $this->TransactionModel->update_voucher_narration($voucher_txn_id,0,'long',$narration);
			  

		      $billsndrydata = [];
				  if(!empty($this->request->getVar('billsndrydata')))
				 		$billsndrydata = json_decode($this->request->getVar('billsndrydata'),true);

				 	$ccdata = [];
				  if(!empty($this->request->getVar('ccdata')))
				 			$ccdata = json_decode($this->request->getVar('ccdata'),true);

				 	$bbbdata = [];
				  if(!empty($this->request->getVar('bbbdata')))
				 			$bbbdata = json_decode($this->request->getVar('bbbdata'),true);

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
				 		
				 		
					$voucher_no = $voucher_info['comp_vch_no'];

					if($vch_subtype_id == 0) // purchase non item
					{
							$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
							foreach ($data as $key => $value) {
									if($value['master_id_type'] == 'acc'){
											$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
									}
									if($value['master_id_type'] == 'itm'){
											$this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
											$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
											
										
									}
									if($value['master_id_type'] == 'bsd'){
											$this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
									}
							}
							$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
							$this->TransactionModel->delete_cc_txn($voucher_txn_id);
							$this->TransactionModel->delete_bills_txn($voucher_txn_id);
							
							$this->TransactionModel->delete_all_narrations($voucher_txn_id);

							$sale_total = array_sum(array_column($itmsdata, 'amount'));
				  
							$billsundry_total = 0;
							if(count($billsndrydata))
									$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
				     
							// update voucher consolidated entry
						  $insert_data  = array(
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_date"				=> $voucher_date,
									"currency_id"				=> $currency_id,
							);
						  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

						 	$insert_data = [
					              "comp_id"             => $this->company_id,
					              "comp_vch_series_id"  => $voucher_series,
					              "voucher_txn_id"      => $voucher_txn_id,
					              "master_id"           => $party_id,
					              'master_id_type'      => 'acc'
				              ];

				      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				            
				           
				      $insert_data  = [
												'comp_id'            => $this->company_id,
												'acc_id'             => $party_id,
												'acc_txn_date'       => $voucher_date,
												'acc_txn_amount'     => ($sale_total + $billsundry_total),						
												'acc_txn_drcr'       => 'c',
												'comp_vch_series_no' => $voucher_no,
												'posted_on'          => date('Y-m-d H:i:s'),						
												'voucher_txn_id'     => $voucher_txn_id,
												'voucher_type_id'    => $voucher_type_id,
												'txn_id'             => $txn_id,
												'acc_bal'            => 0
						   ];	
						   	   
					    $this->TransactionModel->add_acc_txn_data($insert_data);
					    $this->TransactionModel->update_account_balance($party_id);
						 	
					    if($itmsdata)
					    {
					        foreach($itmsdata as $item_row)
					        {
					           
			              	$insert_data = [
					              "comp_id"             => $this->company_id,
					              "comp_vch_series_id"  => $voucher_series,
					              "voucher_txn_id"      => $voucher_txn_id,
					              "master_id"           => $item_row['account_id'],
					              'master_id_type'      => 'acc'
				              ];

				      				$txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				      				
				      					

					           
					           	$insert_data  = [
															'comp_id'            => $this->company_id,
															'acc_id'             => $item_row['account_id'],
															'acc_txn_date'       => $voucher_date,
															'acc_txn_amount'     => $item_row['amount'],						
															'acc_txn_drcr'       => 'd',
															'acc_txn_narr'       => $item_row['description'],
															'comp_vch_series_no' => $voucher_no,
															'posted_on'          => date('Y-m-d H:i:s'),						
															'voucher_txn_id'     => $voucher_txn_id,
															'voucher_type_id'    => $voucher_type_id,
															'txn_id'             => $txn_id,
															'acc_bal'            => 0
								   		];	
								   	   
								   	$this->TransactionModel->add_acc_txn_data($insert_data);
									 	$this->TransactionModel->update_account_balance($item_row['account_id']);
							   
				      		}
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

				    	if(count($billsndrydata))
					    {
									foreach($billsndrydata as $value)
									{
											$txn_data = array(
												"comp_id"							=> $this->company_id,
												"comp_vch_series_id"	=> $voucher_series,
												"voucher_txn_id" 			=> $voucher_txn_id,
												"master_id" 					=> $value['billsundry_id'],
												'master_id_type' 			=> 'bsd'
										 	);
								      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								      if($value['billsundry_amount'] >= 0)
								      		$billsundry_drcr = 'd';
								      else
								      	  $billsundry_drcr = 'c';

								      $insert_data  = array(
								      		"comp_id"							=> $this->company_id,
								      		"sundry_txn_date"			=> $voucher_date,
								      		"sundry_txn_amount"		=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "sundry_txn_narr"			=> '',
					                "comp_vch_name"				=> $voucher_no, // ?
					                "comp_vch_series_no"	=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"					=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'							=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
					              );
					           $this->TransactionModel->add_sundry_txn_data($insert_data);	
									}
					    }

					    if(count($ccdata))
					    {
									foreach($ccdata as $value)
									{
										  $cc_txn_data = [
			                    'cc_id'              => $value['cc_id'],
			                    'comp_id'            => $this->company_id,
			                    'acc_id'             => $value['account_id'],
			                    'voucher_txn_id'     => $voucher_txn_id,
			                    'voucher_type_id'    => $voucher_type_id,
			                    'comp_vch_series_id' => $voucher_series,
			                    'cc_txn_date'        => $voucher_date,
			                    'cc_txn_drcr'        => $value['cc_txn_drcr'],
			                    'cc_txn_amt'         => $value['cc_txn_amt'],
			                    'cc_txn_bal'         => 0,
			                    'cc_txn_narr'        => $value['cc_txn_narr'],
			                ];
			                $this->TransactionModel->add_cc_txn($cc_txn_data);
									}
							}

							$is_bbb = $this->TransactionModel->check_bbb_account($party_id); 
							if($is_bbb)
							{
									if(empty($bbbdata))
							    {
							    	$bbbdata = [];

										$bbbdata[] = [
				                "account_id"=> $party_id,
				                "method"    => 'Adjustment',
				                "reference" => 'UNDEFINED',
				                "reference_id" => 0,
				                "amount"    => ($sale_total+$billsundry_total),
				                "drcr"      => 'C',
				                "due_date"  => '',
				                "narration" => ''
										];
							    }
							    
							    if(count($bbbdata))
					        {
					            foreach($bbbdata as $key => $value)
					            {
					                $bill_ref_id = 0;
					                $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

					                if($value['method'] == 'New Ref.')
					                {
					                    $bill_master_data = [
					                        'bills_ref_name' => $value['reference'],
					                        'acc_id'         => $value['account_id'],
					                        'bills_status'   => 'pending',
					                        'bill_due_date'  => $bill_due_date,
					                    ];
					                    $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
					                }
					                if($value['method'] == 'Adjustment')
					                {
					                    if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
					                        $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
					                    }
					                    else{
					                        $bill_ref_id = $value['reference_id'];
					                        $bill_master_data = [
					                            'bill_due_date'  => $bill_due_date
					                        ];
					                        $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
					                    }
					                    
					                }
					                if($bill_ref_id)
					                {
					                    $bill_txn_data = [
					                        'bills_ref_id'       => $bill_ref_id,
					                        'comp_id'            => $this->company_id,
					                        'acc_id'             => $value['account_id'],
					                        'voucher_txn_id'     => $voucher_txn_id,
					                        'voucher_type_id'    => $voucher_type_id,
					                        'comp_vch_series_id' => $voucher_series,
					                        'bills_txn_date'     => $voucher_date,
					                        'bills_txn_drcr'     => $value['drcr'],
					                        'bills_txn_amt'      => $value['amount'],
					                        'bills_txn_bal'      => 0,
					                        'bills_txn_narr'     => $value['narration'],
					                    ];
					                    $this->TransactionModel->add_bill_txn($bill_txn_data);
					                }
					            }
					        }
							}
					}

			    if($vch_subtype_id == 5) // purchase with stock inward
			    {
			    		$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
							foreach ($data as $key => $value) {
									if($value['master_id_type'] == 'acc'){
											$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
									}
									if($value['master_id_type'] == 'itm'){
											$this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
											$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
												$this->TransactionModel->delete_tracking_data($value['master_id'],$value['txn_id']);
									}
									if($value['master_id_type'] == 'bsd'){
											$this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
									}
							}
							$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
							$this->TransactionModel->delete_cc_txn($voucher_txn_id);
							$this->TransactionModel->delete_bills_txn($voucher_txn_id);
							$this->TransactionModel->delete_all_narrations($voucher_txn_id);

			    		$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

							$billsundry_total = 0;
							if(count($billsndrydata))
									$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));


							// update voucher consolidated entry
						  $insert_data     = array(
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_date"				=> $voucher_date,
									"mat_cent_id"					=> $matrcntr_id,
									"currency_id"				=> $currency_id,
							);
						  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);


							$insert_data = [
				              "comp_id"             => $this->company_id,
				              "comp_vch_series_id"  => $voucher_series,
				              "voucher_txn_id"      => $voucher_txn_id,
				              "master_id"           => $party_id,
				              'master_id_type'      => 'acc'
				              ];

				      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				            
				           
				      $insert_data  = [
											'comp_id'            => $this->company_id,
											'acc_id'             => $party_id,
											'acc_txn_date'       => $voucher_date,
											'acc_txn_amount'     => $sale_total + $billsundry_total,						
											'acc_txn_drcr'       => 'c',
											'comp_vch_series_no' => $voucher_no,
											'posted_on'          => date('Y-m-d H:i:s'),						
											'voucher_txn_id'     => $voucher_txn_id,
											'voucher_type_id'    => $voucher_type_id,
											'txn_id'             => $txn_id,
											'acc_bal'            => 0
						  ];	
						   	   
					    $this->TransactionModel->add_acc_txn_data($insert_data);
					    $this->TransactionModel->update_account_balance($party_id);

					    $item_account_array = [];
					    foreach($itmsdata as $item_row)
					    {

									$txn_data = array(
										"comp_id"				=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 		=> $voucher_txn_id,
										"master_id" 			=> $item_row['item_id'],
										'master_id_type' 		=> 'itm'
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
														'txn_id'            => $txn_id,
														'item_id'  			  	=> $value['item_id'],
														'unit_id'           => $value['tracking_uom_id']
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
											 'description'        => $item_row['description'],											
											'item_id'             => $item_row['item_id'],
											'voucher_txn_id'      => $voucher_txn_id,
											'voucher_type_id'     => $voucher_type_id,
											'mat_cent_id'         => $matrcntr_id,
											'bo_id'				  			=> 0,
											'txn_id'			  			=> $txn_id,
											"item_unit"						=> $item_row['item_unit_id'],
											"item_bal_qty"				=> $item_row['item_qty'],
											"item_avail"  				=> 1,
											"batch_id"  				  => 0,
									   );	
									$this->TransactionModel->add_itm_txn_data($insert_data);


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
									'master_id_type' 		=> 'acc'
							 	);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								$insert_data  = [
									'comp_id'            => $this->company_id,
									'acc_id'             => $account_id,
									'acc_txn_date'       => $voucher_date,
									'acc_txn_amount'     => $amount,						
									'acc_txn_drcr'       => 'd',
									'comp_vch_series_no' => $voucher_no,
									'posted_on'          => date('Y-m-d H:i:s'),						
									'voucher_txn_id'     => $voucher_txn_id,
									'voucher_type_id'    => $voucher_type_id,
									'txn_id'             => $txn_id,
									'acc_bal'            => 0
							    ];	
								$this->TransactionModel->add_acc_txn_data($insert_data);
								$this->TransactionModel->update_account_balance($account_id);
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
															'comp_id'             => $this->company_id,
															'item_txn_date'       => $voucher_date,
															'item_txn_amount'     => 0,
															'item_txn_drcr'       => 'd',
															'item_txn_qty'        => $value['batch_qty'],										
															'item_id'             => $value['item_id'],
															'voucher_txn_id'      => $voucher_txn_id,
															'voucher_type_id'     => $voucher_type_id,
															'mat_cent_id'         => $matrcntr_id,
															'bo_id'				  			=> 0,
															'txn_id'			  			=> $txn_id,
															"item_unit"						=> $value['batch_uom_id'],
															"item_bal_qty"				=> 0,
															"item_avail"  				=> 0,
															"batch_id"  				  => $batch_id,	
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
												'master_id_type' 			=> 'bsd'
										 	);
								      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								      if($value['billsundry_amount'] >= 0)
								      		$billsundry_drcr = 'd';
								      else
								      	  $billsundry_drcr = 'c';

								      $insert_data  = array(
								      		"comp_id"							=> $this->company_id,
								      		"sundry_txn_date"			=> $voucher_date,
								      		"sundry_txn_amount"		=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "comp_vch_name"				=> $voucher_no, // ?
					                "comp_vch_series_no"	=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"					=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'							=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
					              );
					           $this->TransactionModel->add_sundry_txn_data($insert_data);	
									}
					    }

					    if(empty($ccdata))
					    {
					    		$ccdata = [];
							    foreach($itmsdata as $key => $value) {
							    	 
							    		$item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
											$account_id  =  $item_info['item_pur_acc'];
											$amount = $value['item_total_amount'];

											$index = array_search($account_id, array_column($ccdata, 'account_id'));
											if($index != ''){
												$ccdata[$index]['cc_txn_amt'] += $amount;
											}
											else{
													$ccdata[] = [
						                  "cc_id"       => 1,
						                  "account_id"  => $account_id,
						                  "cc_name"     => 'UNDEFINED',
						                  "cc_txn_amt"  => $amount,
						                  "cc_txn_drcr" => 'D',
						                  "cc_txn_narr" => ''
													];
											}                
							    }
					    }
					    
					    if(count($ccdata))
					    {
									foreach($ccdata as $value)
									{
										  $cc_txn_data = [
			                    'cc_id'              => $value['cc_id'],
			                    'comp_id'            => $this->company_id,
			                    'acc_id'             => $value['account_id'],
			                    'voucher_txn_id'     => $voucher_txn_id,
			                    'voucher_type_id'    => $voucher_type_id,
			                    'comp_vch_series_id' => $voucher_series,
			                    'cc_txn_date'        => $voucher_date,
			                    'cc_txn_drcr'        => $value['cc_txn_drcr'],
			                    'cc_txn_amt'         => $value['cc_txn_amt'],
			                    'cc_txn_bal'         => 0,
			                    'cc_txn_narr'        => $value['cc_txn_narr'],
			                ];
			                $this->TransactionModel->add_cc_txn($cc_txn_data);
									}
							}

							$is_bbb = $this->TransactionModel->check_bbb_account($party_id); 
							if($is_bbb)
							{
									if(empty($bbbdata))
							    {
							    	$bbbdata = [];

										$bbbdata[] = [
				                "account_id"=> $party_id,
				                "method"    => 'Adjustment',
				                "reference" => 'UNDEFINED',
				                "reference_id" => 0,
				                "amount"    => ($sale_total+$billsundry_total),
				                "drcr"      => 'C',
				                "due_date"  => '',
				                "narration" => ''
										];
							    }
							    
							    if(count($bbbdata))
					        {
					            foreach($bbbdata as $key => $value)
					            {
					                $bill_ref_id = 0;
					                $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

					                if($value['method'] == 'New Ref.')
					                {
					                    $bill_master_data = [
					                        'bills_ref_name' => $value['reference'],
					                        'acc_id'         => $value['account_id'],
					                        'bills_status'   => 'pending',
					                        'bill_due_date'  => $bill_due_date,
					                    ];
					                    $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
					                }
					                if($value['method'] == 'Adjustment')
					                {
					                    if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
					                        $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
					                    }
					                    else{
					                        $bill_ref_id = $value['reference_id'];
					                        $bill_master_data = [
					                            'bill_due_date'  => $bill_due_date
					                        ];
					                        $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
					                    }
					                    
					                }
					                if($bill_ref_id)
					                {
					                    $bill_txn_data = [
					                        'bills_ref_id'       => $bill_ref_id,
					                        'comp_id'            => $this->company_id,
					                        'acc_id'             => $value['account_id'],
					                        'voucher_txn_id'     => $voucher_txn_id,
					                        'voucher_type_id'    => $voucher_type_id,
					                        'comp_vch_series_id' => $voucher_series,
					                        'bills_txn_date'     => $voucher_date,
					                        'bills_txn_drcr'     => $value['drcr'],
					                        'bills_txn_amt'      => $value['amount'],
					                        'bills_txn_bal'      => 0,
					                        'bills_txn_narr'     => $value['narration'],
					                    ];
					                    $this->TransactionModel->add_bill_txn($bill_txn_data);
					                }
					            }
					        }
							}
			    }

			    if($vch_subtype_id == 6) // purchase without stock
			    {
			    		$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
							foreach ($data as $key => $value) {
									if($value['master_id_type'] == 'acc'){
											$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
									}
									if($value['master_id_type'] == 'itm'){
											$this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
											$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
									}
									if($value['master_id_type'] == 'bsd'){
											$this->TransactionModel->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
									}
							}
							$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
							$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
							$this->TransactionModel->delete_cc_txn($voucher_txn_id);
							$this->TransactionModel->delete_bills_txn($voucher_txn_id);
                            $this->TransactionModel->delete_all_narrations($voucher_txn_id);
			    		$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

							$billsundry_total = 0;
							if(count($billsndrydata))
									$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

							$voucher_tag = 'PESIDUE'.$voucher_no;

							// update voucher consolidated entry
						  $insert_data     = array(
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_date"				=> $voucher_date,
									"mat_cent_id"					=> $matrcntr_id,
									"voucher_tag"				  => $voucher_tag,
									"currency_id"				=> $currency_id,
							);
						  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

						  $insert_data = [
	              "comp_id"             => $this->company_id,
	              "comp_vch_series_id"  => $voucher_series,
	              "voucher_txn_id"      => $voucher_txn_id,
	              "master_id"           => $party_id,
	              'master_id_type'      => 'acc'
	              ];

	            $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
	            
	           
	            $insert_data  = [
									'comp_id'            => $this->company_id,
									'acc_id'             => $party_id,
									'acc_txn_date'       => $voucher_date,
									'acc_txn_amount'     => ($sale_total + $billsundry_total),						
									'acc_txn_drcr'       => 'c',
									//'acc_txn_narr'       => $narration,
									'comp_vch_series_no' => $voucher_no,
									'posted_on'          => date('Y-m-d H:i:s'),						
									'voucher_txn_id'     => $voucher_txn_id,
									'voucher_type_id'    => $voucher_type_id,
									'txn_id'             => $txn_id,
									'acc_bal'            => 0
						   ];	
						   	   
					    $this->TransactionModel->add_acc_txn_data($insert_data);
					    $this->TransactionModel->update_account_balance($party_id);

					    // Add company txn master entry for party oth
							 $txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $party_id,
									'master_id_type' 			=> 'acc'
							 	);
			           	$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

							// Add party oth txn entry 
					    $insert_data  = [
									'comp_id'            	 => $this->company_id,
									'acc_oth_txn_date'     => $voucher_date,
									'acc_oth_txn_amount'   => ($sale_total + $billsundry_total),						
									'acc_oth_txn_drcr'     => 'c',
									//'acc_oth_txn_narr'     => $narration,
									'voucher_type_id'    	 => $voucher_type_id,
									'comp_vch_series_no' 	 => $voucher_series,
									'acc_id'             	 => $party_id,						
									'voucher_txn_id'     	 => $voucher_txn_id,
									'acc_oth_txn_status' 	 => $voucher_txn_id, // changed from $voucher_no
									'bo_id'					 			 => 0,
									'txn_id'				 			 => $txn_id,
									'acc_oth_txn_duedate'  => date('Y-m-d'),
									'acc_oth_txn_tag'		 	 => 'PESIDUE',
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);

							$item_account_array = [];
					    foreach($itmsdata as $item_row)
					    {

									$txn_data = array(
										"comp_id"				=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 		=> $voucher_txn_id,
										"master_id" 			=> $item_row['item_id'],
										'master_id_type' 		=> 'ito'
									);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);


									$insert_data   = array(
										'comp_id'              => $this->company_id,
										'item_oth_txn_date'    => $voucher_date,
										'item_oth_txn_amount'  => $item_row['item_total_amount'],
										'item_oth_txn_drcr'    => 'd',
										'item_oth_txn_qty'     => $item_row['item_qty'],
										'description'           => $item_row['description'],
										'comp_vch_series_no'   => $voucher_no,
										'item_id'              => $item_row['item_id'],
										'voucher_txn_id'       => $voucher_txn_id,
										'voucher_type_id'      => $voucher_type_id,
										'mat_cent_id'          => $matrcntr_id,
										'item_oth_txn_tag'     => '',
										'bo_id'				   			 => 0,
										'txn_id'			   			 => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
									);
									$this->TransactionModel->add_itm_oth_data($insert_data);
									// no need to update item qty balance

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
									'master_id_type' 		=> 'acc'
							 	);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								$insert_data  = [
									'comp_id'            => $this->company_id,
									'acc_id'             => $account_id,
									'acc_txn_date'       => $voucher_date,
									'acc_txn_amount'     => $amount,						
									'acc_txn_drcr'       => 'd',
									'comp_vch_series_no' => $voucher_no,
									'posted_on'          => date('Y-m-d H:i:s'),						
									'voucher_txn_id'     => $voucher_txn_id,
									'voucher_type_id'    => $voucher_type_id,
									'txn_id'             => $txn_id,
									'acc_bal'            => 0
							    ];	
								$this->TransactionModel->add_acc_txn_data($insert_data);
								$this->TransactionModel->update_account_balance($account_id);
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


						  if(count($billsndrydata))
					    {
									foreach($billsndrydata as $value)
									{
											$txn_data = array(
												"comp_id"							=> $this->company_id,
												"comp_vch_series_id"	=> $voucher_series,
												"voucher_txn_id" 			=> $voucher_txn_id,
												"master_id" 					=> $value['billsundry_id'],
												'master_id_type' 			=> 'bsd'
										 	);
								      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								      if($value['billsundry_amount'] >= 0)
								      		$billsundry_drcr = 'd';
								      else
								      	  $billsundry_drcr = 'c';

								      $insert_data  = array(
								      		"comp_id"							=> $this->company_id,
								      		"sundry_txn_date"			=> $voucher_date,
								      		"sundry_txn_amount"		=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "comp_vch_name"				=> $voucher_no, // ?
					                "comp_vch_series_no"	=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"					=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'							=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
					              );
					           $this->TransactionModel->add_sundry_txn_data($insert_data);	
									}
					    }

					    //check inward challans against this purchase voucher
							$count = $this->TransactionModel->count_vouchers_by_tag(6,$voucher_tag);
							if($count > 0)
							{
								
							  $summary = $this->TransactionModel->get_party_oth_summary($voucher_txn_id,'PESI');
						    $cr_total = $summary['cr_total'];
						    $dr_total = $summary['dr_total'];

						    if($cr_total > $dr_total)  
					      		$this->TransactionModel->update_accttxnoth_party_status($voucher_txn_id,'PESI',"PESIDEF");
					      
					      if($cr_total < $dr_total)
					         	$this->TransactionModel->update_accttxnoth_party_status($voucher_txn_id,'PESI',"PESISUR");

					      if($cr_total == $dr_total)
					      		$this->TransactionModel->update_accttxnoth_party_status($voucher_txn_id,'PESI',"PESIEXE");
							}

							if(empty($ccdata))
					    {
					    		$ccdata = [];
							    foreach($itmsdata as $key => $value) {
							    	 
							    		$item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
											$account_id  =  $item_info['item_pur_acc'];
											$amount = $value['item_total_amount'];

											$index = array_search($account_id, array_column($ccdata, 'account_id'));
											if($index != ''){
												$ccdata[$index]['cc_txn_amt'] += $amount;
											}
											else{
													$ccdata[] = [
						                  "cc_id"       => 1,
						                  "account_id"  => $account_id,
						                  "cc_name"     => 'UNDEFINED',
						                  "cc_txn_amt"  => $amount,
						                  "cc_txn_drcr" => 'D',
						                  "cc_txn_narr" => ''
													];
											}                
							    }
					    }
					    
					    if(count($ccdata))
					    {
									foreach($ccdata as $value)
									{
										  $cc_txn_data = [
			                    'cc_id'              => $value['cc_id'],
			                    'comp_id'            => $this->company_id,
			                    'acc_id'             => $value['account_id'],
			                    'voucher_txn_id'     => $voucher_txn_id,
			                    'voucher_type_id'    => $voucher_type_id,
			                    'comp_vch_series_id' => $voucher_series,
			                    'cc_txn_date'        => $voucher_date,
			                    'cc_txn_drcr'        => $value['cc_txn_drcr'],
			                    'cc_txn_amt'         => $value['cc_txn_amt'],
			                    'cc_txn_bal'         => 0,
			                    'cc_txn_narr'        => $value['cc_txn_narr'],
			                ];
			                $this->TransactionModel->add_cc_txn($cc_txn_data);
									}
							}

							$is_bbb = $this->TransactionModel->check_bbb_account($party_id); 
							if($is_bbb)
							{
									if(empty($bbbdata))
							    {
							    	$bbbdata = [];

										$bbbdata[] = [
				                "account_id"=> $party_id,
				                "method"    => 'Adjustment',
				                "reference" => 'UNDEFINED',
				                "reference_id" => 0,
				                "amount"    => ($sale_total+$billsundry_total),
				                "drcr"      => 'C',
				                "due_date"  => '',
				                "narration" => ''
										];
							    }
							    
							    if(count($bbbdata))
					        {
					            foreach($bbbdata as $key => $value)
					            {
					                $bill_ref_id = 0;
					                $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

					                if($value['method'] == 'New Ref.')
					                {
					                    $bill_master_data = [
					                        'bills_ref_name' => $value['reference'],
					                        'acc_id'         => $value['account_id'],
					                        'bills_status'   => 'pending',
					                        'bill_due_date'  => $bill_due_date,
					                    ];
					                    $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
					                }
					                if($value['method'] == 'Adjustment')
					                {
					                    if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
					                        $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
					                    }
					                    else{
					                        $bill_ref_id = $value['reference_id'];
					                        $bill_master_data = [
					                            'bill_due_date'  => $bill_due_date
					                        ];
					                        $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
					                    }
					                    
					                }
					                if($bill_ref_id)
					                {
					                    $bill_txn_data = [
					                        'bills_ref_id'       => $bill_ref_id,
					                        'comp_id'            => $this->company_id,
					                        'acc_id'             => $value['account_id'],
					                        'voucher_txn_id'     => $voucher_txn_id,
					                        'voucher_type_id'    => $voucher_type_id,
					                        'comp_vch_series_id' => $voucher_series,
					                        'bills_txn_date'     => $voucher_date,
					                        'bills_txn_drcr'     => $value['drcr'],
					                        'bills_txn_amt'      => $value['amount'],
					                        'bills_txn_bal'      => 0,
					                        'bills_txn_narr'     => $value['narration'],
					                    ];
					                    $this->TransactionModel->add_bill_txn($bill_txn_data);
					                }
					            }
					        }
							}
			    }

			    if($vch_subtype_id == 7) // purchase against inward challan
			    {
			    		$inward_challan_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'INWCHAL');

			    		$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
							foreach ($data as $key => $value) {
									if($value['master_id_type'] == 'acc'){
											$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
									}
									if($value['master_id_type'] == 'itm'){ //no need
											$this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
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
							$this->TransactionModel->delete_cc_txn($voucher_txn_id);
							$this->TransactionModel->delete_bills_txn($voucher_txn_id);
							$this->TransactionModel->delete_all_narrations($voucher_txn_id);

			    		$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

							$billsundry_total = 0;
							if(count($billsndrydata))
									$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));


							$inward_challan = $this->TransactionModel->get_voucher_cons_info($inward_challan_id, 6);
				    	$voucher_tag = 'ICEPDUE'.$inward_challan['comp_vch_no'];; //voucher no of inward challan

							// update voucher consolidated entry
						  $insert_data     = array(
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_date"				=> $voucher_date,
									"mat_cent_id"					=> $matrcntr_id,
									"voucher_tag"				  => $voucher_tag,
									"currency_id"				=> $currency_id,
							);
						  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);


							$insert_data = [
									'acct_crs_id_type'		=> '',
									'comp_id'				=> $this->company_id,
									'txn_id'				=> '',
									'voucher_txn_id'		=> $voucher_txn_id,
									'bo_id'					=> 0,
									'acc_cross_ref_type'	=> 'INWCHAL',
									'acc_cross_ref_data'	=> $inward_challan_id,
									'acc_cross_logdate'   	=> date('Y-m-d'),
							];
							$this->TransactionModel->add_acc_crsref_data($insert_data);

							$insert_data = [
				              "comp_id"             => $this->company_id,
				              "comp_vch_series_id"  => $voucher_series,
				              "voucher_txn_id"      => $voucher_txn_id,
				              "master_id"           => $party_id,
				              'master_id_type'      => 'acc'
				              ];

				      $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
				            
				           
				      $insert_data  = [
												'comp_id'            => $this->company_id,
												'acc_id'             => $party_id,
												'acc_txn_date'       => $voucher_date,
												'acc_txn_amount'     => ($sale_total + $billsundry_total),						
												'acc_txn_drcr'       => 'c',
												//'acc_txn_narr'       => $narration,
												'comp_vch_series_no' => $voucher_no,
												'posted_on'          => date('Y-m-d H:i:s'),						
												'voucher_txn_id'     => $voucher_txn_id,
												'voucher_type_id'    => $voucher_type_id,
												'txn_id'             => $txn_id,
												'acc_bal'            => 0
							 ];	
							   	   
						  $this->TransactionModel->add_acc_txn_data($insert_data);
						  $this->TransactionModel->update_account_balance($party_id);

						    // Add company txn master entry for party oth
							$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> $party_id,
									'master_id_type' 			=> 'acc'
							 	);
				      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

							// Add party oth txn entry 
						    $insert_data  = [
								'comp_id'            	 	 => $this->company_id,
								'acc_oth_txn_date'       => $voucher_date,
								'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
								'acc_oth_txn_drcr'       => 'd',  // CHANGED
								//'acc_oth_txn_narr'       => $narration,
								'voucher_type_id'    	   => $voucher_type_id,
								'comp_vch_series_no' 	   => $voucher_series,
								'acc_id'             	   => $party_id,						
								'voucher_txn_id'     	   => $voucher_txn_id,
								'acc_oth_txn_status' 	   => $inward_challan_id, // inward challan
								'bo_id'					 				 => 0,
								'txn_id'				 				 => $txn_id,
								'acc_oth_txn_duedate' 	 => date('Y-m-d'),
								'acc_oth_txn_tag'		     => 'ICEPDUE', //what will be it ?
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);


							$item_account_array = [];
					    foreach($itmsdata as $item_row)
					    {

									$txn_data = array(
										"comp_id"				=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 		=> $voucher_txn_id,
										"master_id" 			=> $item_row['item_id'],
										'master_id_type' 		=> 'ito'
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
										'item_oth_txn_tag'     => '',
										'bo_id'				   => 0,
										'txn_id'			   => $txn_id,
										'item_unit'            => $item_row['item_unit_id'],
									);
									$this->TransactionModel->add_itm_oth_data($insert_data);
									// no need to update item qty balance

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
									'master_id_type' 		=> 'acc'
							 	);
								$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								$insert_data  = [
									'comp_id'            => $this->company_id,
									'acc_id'             => $account_id,
									'acc_txn_date'       => $voucher_date,
									'acc_txn_amount'     => $amount,						
									'acc_txn_drcr'       => 'd',
									'comp_vch_series_no' => $voucher_no,
									'posted_on'          => date('Y-m-d H:i:s'),						
									'voucher_txn_id'     => $voucher_txn_id,
									'voucher_type_id'    => $voucher_type_id,
									'txn_id'             => $txn_id,
									'acc_bal'            => 0
							    ];	
								$this->TransactionModel->add_acc_txn_data($insert_data);
								$this->TransactionModel->update_account_balance($account_id);
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

					    $summary = $this->TransactionModel->get_party_oth_summary($inward_challan_id,'ICEP');
					    $cr_total = $summary['cr_total'];
					    $dr_total = $summary['dr_total'];

					    if($cr_total > $dr_total)  
				      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDEF");
				      
				      if($cr_total < $dr_total)
				         	$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPSUR");

				      if($cr_total == $dr_total)
				      		$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPEXE");


						  if(count($billsndrydata))
					    {
									foreach($billsndrydata as $value)
									{
											$txn_data = array(
												"comp_id"							=> $this->company_id,
												"comp_vch_series_id"	=> $voucher_series,
												"voucher_txn_id" 			=> $voucher_txn_id,
												"master_id" 					=> $value['billsundry_id'],
												'master_id_type' 			=> 'bsd'
										 	);
								      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								      if($value['billsundry_amount'] >= 0)
								      		$billsundry_drcr = 'd';
								      else
								      	  $billsundry_drcr = 'c';

								      $insert_data  = array(
								      		"comp_id"							=> $this->company_id,
								      		"sundry_txn_date"			=> $voucher_date,
								      		"sundry_txn_amount"		=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "sundry_txn_narr"			=> '',
					                "comp_vch_name"				=> $voucher_no, // ?
					                "comp_vch_series_no"	=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"					=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'							=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
					              );
					           $this->TransactionModel->add_sundry_txn_data($insert_data);	
									}
					    }

					    if(empty($ccdata))
					    {
					    		$ccdata = [];
							    foreach($itmsdata as $key => $value) {
							    	 
							    		$item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
											$account_id  =  $item_info['item_pur_acc'];
											$amount = $value['item_total_amount'];

											$index = array_search($account_id, array_column($ccdata, 'account_id'));
											if($index != ''){
												$ccdata[$index]['cc_txn_amt'] += $amount;
											}
											else{
													$ccdata[] = [
						                  "cc_id"       => 1,
						                  "account_id"  => $account_id,
						                  "cc_name"     => 'UNDEFINED',
						                  "cc_txn_amt"  => $amount,
						                  "cc_txn_drcr" => 'D',
						                  "cc_txn_narr" => ''
													];
											}                
							    }
					    }
					    
					    if(count($ccdata))
					    {
									foreach($ccdata as $value)
									{
										  $cc_txn_data = [
			                    'cc_id'              => $value['cc_id'],
			                    'comp_id'            => $this->company_id,
			                    'acc_id'             => $value['account_id'],
			                    'voucher_txn_id'     => $voucher_txn_id,
			                    'voucher_type_id'    => $voucher_type_id,
			                    'comp_vch_series_id' => $voucher_series,
			                    'cc_txn_date'        => $voucher_date,
			                    'cc_txn_drcr'        => $value['cc_txn_drcr'],
			                    'cc_txn_amt'         => $value['cc_txn_amt'],
			                    'cc_txn_bal'         => 0,
			                    'cc_txn_narr'        => $value['cc_txn_narr'],
			                ];
			                $this->TransactionModel->add_cc_txn($cc_txn_data);
									}
							}

							$is_bbb = $this->TransactionModel->check_bbb_account($party_id); 
							if($is_bbb)
							{
									if(empty($bbbdata))
							    {
							    	$bbbdata = [];

										$bbbdata[] = [
				                "account_id"=> $party_id,
				                "method"    => 'Adjustment',
				                "reference" => 'UNDEFINED',
				                "reference_id" => 0,
				                "amount"    => ($sale_total+$billsundry_total),
				                "drcr"      => 'C',
				                "due_date"  => '',
				                "narration" => ''
										];
							    }
							    
							    if(count($bbbdata))
					        {
					            foreach($bbbdata as $key => $value)
					            {
					                $bill_ref_id = 0;
					                $bill_due_date = $value['due_date'] != '' ? date("Y-m-d", strtotime($value['due_date'])) : $voucher_date;

					                if($value['method'] == 'New Ref.')
					                {
					                    $bill_master_data = [
					                        'bills_ref_name' => $value['reference'],
					                        'acc_id'         => $value['account_id'],
					                        'bills_status'   => 'pending',
					                        'bill_due_date'  => $bill_due_date,
					                    ];
					                    $bill_ref_id = $this->TransactionModel->add_bill_master($bill_master_data);
					                }
					                if($value['method'] == 'Adjustment')
					                {
					                    if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
					                        $bill_ref_id = $this->TransactionModel->getUndefinedBillRefId($value['account_id']);  
					                    }
					                    else{
					                        $bill_ref_id = $value['reference_id'];
					                        $bill_master_data = [
					                            'bill_due_date'  => $bill_due_date
					                        ];
					                        $this->TransactionModel->update_bill_master($bill_ref_id, $bill_master_data);
					                    }
					                    
					                }
					                if($bill_ref_id)
					                {
					                    $bill_txn_data = [
					                        'bills_ref_id'       => $bill_ref_id,
					                        'comp_id'            => $this->company_id,
					                        'acc_id'             => $value['account_id'],
					                        'voucher_txn_id'     => $voucher_txn_id,
					                        'voucher_type_id'    => $voucher_type_id,
					                        'comp_vch_series_id' => $voucher_series,
					                        'bills_txn_date'     => $voucher_date,
					                        'bills_txn_drcr'     => $value['drcr'],
					                        'bills_txn_amt'      => $value['amount'],
					                        'bills_txn_bal'      => 0,
					                        'bills_txn_narr'     => $value['narration'],
					                    ];
					                    $this->TransactionModel->add_bill_txn($bill_txn_data);
					                }
					            }
					        }
							}
			    }

			    // return redirect()->to($this->base_url.'purchase/item');
			    return json_encode(['status' => true, 'message' => 'Data Updated']);
			  }

				if($vch_subtype_id == 0) // purchase non item
				{
						$party_transaction 	= $this->TransactionModel->get_party_transaction($voucher_txn_id);
						$data['party_id'] 					= $party_transaction['acc_id'];
						$data['narration'] 					= $party_transaction['acc_txn_narr'];

						$data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
						$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id);
						
							$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
						$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);

						$data['cc_data'] = $this->TransactionModel->get_cc_txn_data($voucher_txn_id);
						$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);

						$data['message_output']   = $this->message_output;
						$data['base_url']       	= $this->base_url; 
						$data['folder_path']    	= $this->folder_path;
						$data['party_dropdown']   = $this->TransactionModel->party_dropdown();
						$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
                        $data['get_narration_info']     = $get_narration_info;  
						$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
						$data['voucher_no'] 				= $voucher_info['comp_vch_no'];
						$data['voucher_date'] 			= date('d-m-Y', strtotime($voucher_info['voucher_date']));
						$data['voucher_txn_id']     = $voucher_txn_id;
						$data['bills_method_list']				= ['','New Ref.','Adjustment'];		
						
						$data['acc_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json');
                        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
                        $data['voucher_type_id']                = $voucher_type_id;
            // $data['currency'] = $this->TransactionModel->get_currency($voucher_info['currency_id']);
            $data['currency_list'] = $this->TransactionModel->get_currency_list();
            $data['currency_id'] = $voucher_info['currency_id'];

						return view($this->folder_path.'purchase/edit_invoice_without_item',$data);
				}
				if($vch_subtype_id == 5) // purchase with stock inward
				{
						$party_transaction 	= $this->TransactionModel->get_party_transaction($voucher_txn_id);
						
						$data['party_id'] 					= $party_transaction['acc_id'];
						$data['narration'] 					= $party_transaction['acc_txn_narr'];
						$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];

						$data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
						$data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id);
						$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id);
						
						
						$data['cc_data'] = $this->TransactionModel->get_cc_txn_data($voucher_txn_id);
						$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);
						$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
						$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);

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
						$data['bills_method_list']				= ['','New Ref.','Adjustment'];
						
						$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
                         $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
                         $data['voucher_type_id']                = $voucher_type_id;
            // $data['currency'] = $this->TransactionModel->get_currency($voucher_info['currency_id']);
            $data['currency_list'] = $this->TransactionModel->get_currency_list();
            $data['currency_id'] = $voucher_info['currency_id'];
				
						return view($this->folder_path.'purchase/edit_invoice',$data);
				}
				if($vch_subtype_id == 6) // purchase without stock 
				{
						$party_transaction 	= $this->TransactionModel->get_party_transaction($voucher_txn_id);
						$data['party_id'] 					= $party_transaction['acc_id'];
						$data['narration'] 					= $party_transaction['acc_txn_narr'];
						$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];

						$data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
						$data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
						$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id);
						
							$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
						$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);

						$data['cc_data'] = $this->TransactionModel->get_cc_txn_data($voucher_txn_id);
						$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);

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
						$data['bills_method_list']				= ['','New Ref.','Adjustment'];

						// echo "<pre>";print_r($data['item_transactions']);exit;
						
						$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
                         $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
                         $data['voucher_type_id']                = $voucher_type_id;
             // $data['currency'] = $this->TransactionModel->get_currency($voucher_info['currency_id']);
             $data['currency_list'] = $this->TransactionModel->get_currency_list(); 
             $data['currency_id'] = $voucher_info['currency_id'];           
				
						return view($this->folder_path.'purchase/edit_invoice',$data);
				}
				if($vch_subtype_id == 7) // purchase against inward challan
				{
						$party_transaction 	= $this->TransactionModel->get_party_transaction($voucher_txn_id);
						$data['party_id'] 					= $party_transaction['acc_id'];
						$data['narration'] 					= $party_transaction['acc_txn_narr'];
						$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];

						$data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
						$data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
						$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id);
						
							$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
						$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);

						$data['cc_data'] = $this->TransactionModel->get_cc_txn_data($voucher_txn_id);
						$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);

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
						
						$data['get_narration_info']     = $get_narration_info;
						$data['vch_subtype']     		= $this->TransactionModel->get_vch_subtype($vch_subtype_id);

						$data['purchase_against_challan'] = $this->TransactionModel->against_dropdown('6',array('ICEPDUE','ICEPDEF','PESISUR'));
						$inward_challan_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'INWCHAL');
						
						$ic_voucher_info = $this->TransactionModel->get_voucher_cons_info($inward_challan_id, '6');
						$ic_voucher_no = $ic_voucher_info['comp_vch_no'];
						$data['against'] = 'Voucher No. '.$ic_voucher_no;
						$data['bills_method_list']				= ['','New Ref.','Adjustment'];
						
						$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
                         $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
                         
                         $data['voucher_type_id']                = $voucher_type_id;
            // $data['currency'] = $this->TransactionModel->get_currency($voucher_info['currency_id']);
            $data['currency_list'] = $this->TransactionModel->get_currency_list();
            $data['currency_id'] = $voucher_info['currency_id'];             

						return view($this->folder_path.'purchase/edit_invoice',$data);
				}
		}

		public function delete($voucher_txn_id)
		{
   	 	$voucher_type_id    = "11";

				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);

				if(!empty($voucher_info)){

					$check = $this->TransactionModel->check_crsref($voucher_txn_id, 'PURCINV');
					if($check){
						return json_encode(['status' => false, 'message' => 'Purchase Voucher has linked Debit Note or Inward Challan']);
						echo "Purchase Voucher has linked Debit Note or Inward Challan";
						exit;
					}

					$vch_subtype_id = $voucher_info['vch_subtype_id'];

					if($vch_subtype_id == 7){
						$inward_challan_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'INWCHAL');
					}

					$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
					foreach ($data as $key => $value) {
							if($value['master_id_type'] == 'acc'){
									$this->TransactionModel->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
							}
							if($value['master_id_type'] == 'itm'){
									$this->TransactionModel->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
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

					$this->TransactionModel->delete_cc_txn($voucher_txn_id);
					$this->TransactionModel->delete_bills_txn($voucher_txn_id);					
					$this->TransactionModel->delete_all_narrations($voucher_txn_id);
					
					if($vch_subtype_id == 7 && $inward_challan_id){
							$voucher_tag = $voucher_info['voucher_tag'];
							$count = $this->TransactionModel->count_vouchers_by_tag($voucher_type_id,$voucher_tag);
							if($count > 0)
							{
								
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
							else
							{
									$this->TransactionModel->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDUE");
							}
					}

				}

				
				// return redirect()->to($this->base_url.'purchase/item');
				return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
		} 
	
}