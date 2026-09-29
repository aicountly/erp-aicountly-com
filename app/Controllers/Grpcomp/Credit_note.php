<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BalancesModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Credit_note  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
			$this->BalancesModel  = new BalancesModel();
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
   
   	public function ajax_sale_voucher_list($date)
    {
    	$date = date('Y-m-d', strtotime($date));
    	$data = $this->TransactionModel->against_voucher_dropdown('18',array(0,8,9,10),$date);
    	return json_encode($data);
    }
    public function item()
    {
    	$voucher_type_id = 2;
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
		    $voucher_date 		 = date("Y-m-d", strtotime($voucher_date));
		    $voucher_series   = $this->request->getVar('voucher_series'); 
		    $party_id         = $this->request->getVar('party_id'); 
		    $matrcntr_id      = $this->request->getVar('matrcntr_id');  
		    $narration        = $this->request->getVar('narration'); 
		    $itmsdata         = json_decode($this->request->getVar('itmsdata'),true); 
		    $currency_id = $this->request->getVar('currency_id');

		     	$batchdata = [];
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);

		    $billsndrydata = [];
			if(!empty($this->request->getVar('billsndrydata')))
			 	$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);

			 $bbbdata = [];
			if(!empty($this->request->getVar('bbbdata')))
			 	$bbbdata = json_decode($this->request->getVar('bbbdata'),true);
			 
			$vch_subtype_id       = $this->request->getVar('vch_subtype_id');
			$sale_voucher_id  = $this->request->getVar('sale_voucher_id');
			$voucher_no           = $this->TransactionModel->get_voucher_no($voucher_type_id);

			$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));
			  
			$billsundry_total = 0;
			if(count($billsndrydata))
				$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

			if($vch_subtype_id == 1) // with stock
			{
				
				$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18);
				$voucher_tag = $sale_voucher['voucher_tag']; //voucher tag of sale either SEDCDUE or DCESDUE

				// add voucher consolidated entry
			  	$insert_data  = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"comp_vch_no"			=> $voucher_no,
						"voucher_type_id"		=> $voucher_type_id,
						"voucher_date"			=> $voucher_date,
						"mat_cent_id"			=> $matrcntr_id,
						"vch_subtype_id"		=> $vch_subtype_id,
						"voucher_tag"			=> $voucher_tag,
						"currency_id"				=> $currency_id,
				);
			  	$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);
			

			  	$insert_data = [
						'acct_crs_id_type'		=> '',
						'comp_id'				=> $this->company_id,
						'txn_id'				=> '',
						'voucher_txn_id'		=> $voucher_txn_id,
						'bo_id'					=> 0,
						'acc_cross_ref_type'	=> 'SALEINV',
						'acc_cross_ref_data'	=> $sale_voucher_id,
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

				if($voucher_tag != '')
				{
				  // Add company txn master entry for party oth
				  $txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $party_id,
						'master_id_type' 		=> 'aco'
				 	);
			    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

			    $acc_oth_txn_tag = '';
			    if(str_contains($voucher_tag, 'SEDC')){
				    $acc_oth_txn_tag = 'SEDCDUE';
				    $acc_oth_txn_status = $sale_voucher_id;
				    $acc_oth_txn_drcr = 'c';
			    }
			    if(str_contains($voucher_tag, 'DCES')){
			    	$acc_oth_txn_tag = 'DCESDUE';
			    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL'); //delivery_challan_id
			    	$acc_oth_txn_drcr = 'd';
			    } 
				   	

				  // Add party oth txn entry
			    $insert_data  = [
					'comp_id'            	 	=> $this->company_id,
					'acc_oth_txn_date'       	=> $voucher_date,
					'acc_oth_txn_amount'     	=> ($sale_total + $billsundry_total),						
					'acc_oth_txn_drcr'       	=> $acc_oth_txn_drcr,
					'voucher_type_id'    	   	=> $voucher_type_id,
					'comp_vch_series_no' 	   	=> $voucher_series,
					'acc_id'             	   	=> $party_id,						
					'voucher_txn_id'     	   	=> $voucher_txn_id,
					'acc_oth_txn_status' 	   	=> $acc_oth_txn_status, 
					'bo_id'					 	=> 0,
					'txn_id'				 	=> $txn_id,
					'acc_oth_txn_duedate' 	 	=> date('Y-m-d'),
					'acc_oth_txn_tag'		    => $acc_oth_txn_tag, //what will be it ?
				  ];
				  $this->TransactionModel->add_acc_oth_data($insert_data);
				}

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

					$insert_data   = array(
							'comp_id'             => $this->company_id,
							'item_txn_date'       => $voucher_date,
							'item_txn_amount'     => $item_row['item_total_amount'],
							'item_txn_drcr'       => 'd',
							'item_txn_qty'        => $item_row['item_qty'],
							'description'       => $item_row['description'],
							'item_id'             => $item_row['item_id'],
							'voucher_txn_id'      => $voucher_txn_id,
							'voucher_type_id'     => $voucher_type_id,
							'mat_cent_id'         => $matrcntr_id,
							'bo_id'				  => 0,
							'txn_id'			  => $txn_id
					);	
					$item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);

					$get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
					$item_open_qty      =  !empty($get_item_info['op_bal_qty']) ? $get_item_info['op_bal_qty'] : 0;
          			$item_open_value    =  !empty($get_item_info['op_bal_val']) ? $get_item_info['op_bal_val'] : 0;
					

					$insert_data = array(
						"item_txn_date"			=> $voucher_date,
						"item_id"				=> $item_row['item_id'],
						'item_txn_drcr'         => 'd',
						"txn_id"				=> $txn_id,
						"item_txn_id"			=> $item_txn_id,
						"voucher_txn_id"		=> $voucher_txn_id,
						"bo_id"					=> 0,
						"mat_cent_id"			=> $matrcntr_id,
						"item_unit"				=> $item_row['item_unit_id'],
						"item_bal_qty"			=> $item_row['item_qty'],
						"item_avail"          => "1"
				   	);
					$itemtxnbal_id = $this->BalancesModel->add_itemtxnbal($item_row['item_id'],$insert_data,'d',$item_open_qty,$item_open_value);
					$this->TransactionModel->CalculateValuation($item_row['item_id'],$item_row['item_unit_id'],$matrcntr_id,0,$itemtxnbal_id,'d',$voucher_date);
					$this->TransactionModel->CalculateValuation($item_row['item_id'],$item_row['item_unit_id'],$matrcntr_id,1,$itemtxnbal_id,'d',$voucher_date);
					$this->TransactionModel->backdatevaluation_calculation($item_row['item_id'],$voucher_date);
					// $this->TransactionModel->update_item_balance($party_id);  //function not created in model

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
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                        "item_txn_date"			=> $voucher_date,
															"item_id"						=> $value['item_id'],
															'item_txn_drcr'     => 'c',
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
							"comp_id"					=> $this->company_id,
							"comp_vch_series_id"		=> $voucher_series,
							"voucher_txn_id" 			=> $voucher_txn_id,
							"master_id" 				=> $value['billsundry_id'],
							'master_id_type' 			=> 'bsd'
					 	);
			      		$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

						if($value['billsundry_amount'] >= 0)
							$billsundry_drcr = 'd';
						else
							$billsundry_drcr = 'c';

			      		$insert_data  = array(
				      		"comp_id"					=> $this->company_id,
				      		"sundry_txn_date"			=> $voucher_date,
				      		"sundry_txn_amount"			=> abs($value['billsundry_amount']),
			                "sundry_txn_drcr"			=> $billsundry_drcr,
			                "comp_vch_name"				=> $voucher_no, // ?
			                "comp_vch_series_no"		=> $voucher_no,
			                "bill_sundry_id"			=> $value['billsundry_id'],
			                "sundry_bal"				=> 0,
			                "voucher_txn_id"			=> $voucher_txn_id, 
			                "voucher_type_id"			=> $voucher_type_id,
			                'txn_id'					=> $txn_id,
			                'sundry_tag_rate'			=> $value['billsundry_rate']
	              		);
	           			$this->TransactionModel->add_sundry_txn_data($insert_data);	
					}
			    }

			    if(str_contains($voucher_tag, 'SEDC'))
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
			    if(str_contains($voucher_tag, 'DCES'))
			    {
			    	$delivery_challan_id = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL');
			    	$summary = $this->TransactionModel->get_party_oth_summary($delivery_challan_id,'DCES');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
			      
			      	if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

			      	if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
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
			if($vch_subtype_id == 2) // without stock
			{
				$voucher_tag = '';
				$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18);
				$voucher_tag = $sale_voucher['voucher_tag']; //voucher tag of sale either SEDCDUE or DCESDUE

				// add voucher consolidated entry
			  	$insert_data  = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"comp_vch_no"			=> $voucher_no,
						"voucher_type_id"		=> $voucher_type_id,
						"voucher_date"			=> $voucher_date,
						"mat_cent_id"			=> $matrcntr_id,
						"vch_subtype_id"		=> $vch_subtype_id,
						"voucher_tag"			=> $voucher_tag,
						"currency_id"				=> $currency_id,
				);
			  	$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);

			  	$insert_data = [
						'acct_crs_id_type'		=> '',
						'comp_id'				=> $this->company_id,
						'txn_id'				=> '',
						'voucher_txn_id'		=> $voucher_txn_id,
						'bo_id'					=> 0,
						'acc_cross_ref_type'	=> 'SALEINV',
						'acc_cross_ref_data'	=> $sale_voucher_id,
						'acc_cross_logdate'   	=> date('Y-m-d'),
				];
				$this->TransactionModel->add_acc_crsref_data($insert_data);

				$insert_data = [
						'acct_crs_id_type'		=> $voucher_type_id,
						'comp_id'				=> $this->company_id,
						'txn_id'				=> '',
						'voucher_txn_id'		=> $voucher_txn_id,
						'bo_id'					=> 0,
						'acc_cross_ref_type'	=> 'SESIDUE',
						'acc_cross_ref_data'	=> $sale_voucher_id,
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

				if($voucher_tag != '')
				{
				  // Add company txn master entry for party oth
				  $txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $party_id,
						'master_id_type' 		=> 'aco'
				 	);
			    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

			    $acc_oth_txn_tag = '';
			    if(str_contains($voucher_tag, 'SEDC')){
				    $acc_oth_txn_tag = 'SEDCDUE';
				    $acc_oth_txn_status = $sale_voucher_id;
				    $acc_oth_txn_drcr = 'c';
			    }
			    if(str_contains($voucher_tag, 'DCES')){
			    	$acc_oth_txn_tag = 'DCESDUE';
			    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL'); //delivery_challan_id
			    	$acc_oth_txn_drcr = 'd';
			    } 
				   	

				  // Add party oth txn entry
			    $insert_data  = [
					'comp_id'            	 	=> $this->company_id,
					'acc_oth_txn_date'       	=> $voucher_date,
					'acc_oth_txn_amount'     	=> ($sale_total + $billsundry_total),						
					'acc_oth_txn_drcr'       	=> $acc_oth_txn_drcr,
					'voucher_type_id'    	   	=> $voucher_type_id,
					'comp_vch_series_no' 	   	=> $voucher_series,
					'acc_id'             	   	=> $party_id,						
					'voucher_txn_id'     	   	=> $voucher_txn_id,
					'acc_oth_txn_status' 	   	=> $acc_oth_txn_status, 
					'bo_id'					 	=> 0,
					'txn_id'				 	=> $txn_id,
					'acc_oth_txn_duedate' 	 	=> date('Y-m-d'),
					'acc_oth_txn_tag'		    => $acc_oth_txn_tag, //what will be it ?
				  ];
				  $this->TransactionModel->add_acc_oth_data($insert_data);
				}

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
						'bo_id'				   => 0,
						'txn_id'			   => $txn_id,
						'item_unit'            => $item_row['item_unit_id'],
					);
					$this->TransactionModel->add_itm_oth_data($insert_data);
					// no need to update item qty balance

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
			                    
			                
			                if($batch_id)
			                {
			                    $insert_data = [
			                        "item_txn_date"			=> $voucher_date,
															"item_id"						=> $value['item_id'],
															'item_txn_drcr'     => 'c',
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
									"comp_id"					=> $this->company_id,
									"comp_vch_series_id"		=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 				=> $value['billsundry_id'],
									'master_id_type' 			=> 'bsd'
							 	);
					      		$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								if($value['billsundry_amount'] >= 0)
									$billsundry_drcr = 'd';
								else
									$billsundry_drcr = 'c';

					      		$insert_data  = array(
						      		"comp_id"					=> $this->company_id,
						      		"sundry_txn_date"			=> $voucher_date,
						      		"sundry_txn_amount"			=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "comp_vch_name"				=> $voucher_no, 
					                "comp_vch_series_no"		=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"				=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'					=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
			              		);
			           			$this->TransactionModel->add_sundry_txn_data($insert_data);	
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

			    if(str_contains($voucher_tag, 'SEDC'))
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
			    if(str_contains($voucher_tag, 'DCES'))
			    {
			    	$delivery_challan_id = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL');
			    	$summary = $this->TransactionModel->get_party_oth_summary($delivery_challan_id,'DCES');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
			      
			      	if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

			      	if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
			    } 
			}
			// return redirect()->to($this->base_url.'credit_note/item');
			return json_encode(['status' => true, 'message' => 'Data Inserted']);
		}
        
        $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
		$data['message_output']   = $this->message_output;
		$data['base_url']       = $this->base_url; 
		$data['folder_path']    = $this->folder_path;
		$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
		// $data['items_list']     = $this->TransactionModel->items_list();	
		$data['units_list']     = $this->TransactionModel->units_dropdown();
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['party_dropdown']     = $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
		$data['sub_type_dropdown']     = $this->TransactionModel->vch_subtype_dropdown($voucher_type_id);
		$data['sale_voucher_list'] = $this->TransactionModel->against_voucher_dropdown('18',array(0,8,9,10),date('Y-m-d'));
		$data['bills_method_list']				= ['','New Ref.','Adjustment'];
        
        $data['voucher_date']            = $voucher_detail['last_entry'];
        $data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
         $data['bsd_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
         $data['voucher_type_id']                = $voucher_type_id;
         $data['currency_list'] = $this->TransactionModel->get_currency_list();

		return view($this->folder_path.'credit_note/item_based',$data);
    }

    public function non_item()
    {
    	$voucher_type_id = 2;

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
		    $voucher_date 		 = date("Y-m-d", strtotime($voucher_date));
		    $voucher_series   = $this->request->getVar('voucher_series'); 
		    $party_id         = $this->request->getVar('party_id');  
		    $narration        = $this->request->getVar('narration'); 
		    $itmsdata         = json_decode($this->request->getVar('itmsdata'),true);
		    $currency_id = $this->request->getVar('currency_id'); 
		     
		    $billsndrydata = [];
			if(!empty($this->request->getVar('billsndrydata')))
			 	$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);

			 $bbbdata = [];
			if(!empty($this->request->getVar('bbbdata')))
			 	$bbbdata = json_decode($this->request->getVar('bbbdata'),true);
			 
			$sale_voucher_id  = $this->request->getVar('sale_voucher_id');
			$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);

			$sale_total = array_sum(array_column($itmsdata, 'amount'));
			  
			$billsundry_total = 0;
			if(count($billsndrydata))
				$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

			
			$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18);
			$voucher_tag = $sale_voucher['voucher_tag']; //voucher tag of sale either SEDCDUE or DCESDUE

			// add voucher consolidated entry
		  	$insert_data  = array(
					"comp_id"				=> $this->company_id,
					"comp_vch_series_id"	=> $voucher_series,
					"comp_vch_no"			=> $voucher_no,
					"voucher_type_id"		=> $voucher_type_id,
					"voucher_date"			=> $voucher_date,
					"mat_cent_id"			=> 0,
					"vch_subtype_id"		=> 0,
					"voucher_tag"			=> $voucher_tag,
					"currency_id"				=> $currency_id,
			);
		  	$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);

		  	$insert_data = [
					'acct_crs_id_type'		=> '',
					'comp_id'				=> $this->company_id,
					'txn_id'				=> '',
					'voucher_txn_id'		=> $voucher_txn_id,
					'bo_id'					=> 0,
					'acc_cross_ref_type'	=> 'SALEINV',
					'acc_cross_ref_data'	=> $sale_voucher_id,
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

			if($voucher_tag != '')
			{
			  // Add company txn master entry for party oth
			  $txn_data = array(
					"comp_id"				=> $this->company_id,
					"comp_vch_series_id"	=> $voucher_series,
					"voucher_txn_id" 		=> $voucher_txn_id,
					"master_id" 			=> $party_id,
					'master_id_type' 		=> 'aco'
			 	);
		    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

		    $acc_oth_txn_tag = '';
		    if(str_contains($voucher_tag, 'SEDC')){
			    $acc_oth_txn_tag = 'SEDCDUE';
			    $acc_oth_txn_status = $sale_voucher_id;
			    $acc_oth_txn_drcr = 'c';
		    }
		    if(str_contains($voucher_tag, 'DCES')){
		    	$acc_oth_txn_tag = 'DCESDUE';
		    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL'); //delivery_challan_id
		    	$acc_oth_txn_drcr = 'd';
		    } 
			   	

			  // Add party oth txn entry
		    $insert_data  = [
				'comp_id'            	 	=> $this->company_id,
				'acc_oth_txn_date'       	=> $voucher_date,
				'acc_oth_txn_amount'     	=> ($sale_total + $billsundry_total),						
				'acc_oth_txn_drcr'       	=> $acc_oth_txn_drcr,
				'voucher_type_id'    	   	=> $voucher_type_id,
				'comp_vch_series_no' 	   	=> $voucher_series,
				'acc_id'             	   	=> $party_id,						
				'voucher_txn_id'     	   	=> $voucher_txn_id,
				'acc_oth_txn_status' 	   	=> $acc_oth_txn_status, 
				'bo_id'					 	=> 0,
				'txn_id'				 	=> $txn_id,
				'acc_oth_txn_duedate' 	 	=> date('Y-m-d'),
				'acc_oth_txn_tag'		    => $acc_oth_txn_tag, //what will be it ?
			  ];
			  $this->TransactionModel->add_acc_oth_data($insert_data);
			}
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
								"comp_id"					=> $this->company_id,
								"comp_vch_series_id"		=> $voucher_series,
								"voucher_txn_id" 			=> $voucher_txn_id,
								"master_id" 				=> $value['billsundry_id'],
								'master_id_type' 			=> 'bsd'
						 	);
				      		$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

							if($value['billsundry_amount'] >= 0)
								$billsundry_drcr = 'd';
							else
								$billsundry_drcr = 'c';

				      		$insert_data  = array(
					      		"comp_id"					=> $this->company_id,
					      		"sundry_txn_date"			=> $voucher_date,
					      		"sundry_txn_amount"			=> abs($value['billsundry_amount']),
				                "sundry_txn_drcr"			=> $billsundry_drcr,
				                "comp_vch_name"				=> $voucher_no, // ?
				                "comp_vch_series_no"		=> $voucher_no,
				                "bill_sundry_id"			=> $value['billsundry_id'],
				                "sundry_bal"				=> 0,
				                "voucher_txn_id"			=> $voucher_txn_id, 
				                "voucher_type_id"			=> $voucher_type_id,
				                'txn_id'					=> $txn_id,
				                'sundry_tag_rate'			=> $value['billsundry_rate']
		              		);
		           			$this->TransactionModel->add_sundry_txn_data($insert_data);	
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

		    if(str_contains($voucher_tag, 'SEDC'))
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
		    if(str_contains($voucher_tag, 'DCES'))
		    {
		    	$delivery_challan_id = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL');
		    	$summary = $this->TransactionModel->get_party_oth_summary($delivery_challan_id,'DCES');
			    $cr_total = $summary['cr_total'];
			    $dr_total = $summary['dr_total'];

			    if($cr_total > $dr_total)  
		      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
		      
		      	if($cr_total < $dr_total)
		         	$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

		      	if($cr_total == $dr_total)
		      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
		    } 
			   
			 
	    	// return redirect()->to($this->base_url.'credit_note/non_item');
	    	return json_encode(['status' => true, 'message' => 'Data Inserted']);
    	}
    	   $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
    	   

			$data['message_output']   = $this->message_output;
			$data['base_url']       = $this->base_url; 
			$data['folder_path']    = $this->folder_path;
			$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
			// $data['items_list']     = $this->TransactionModel->items_list();
			$data['units_list']     = $this->TransactionModel->units_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['party_dropdown']     = $this->TransactionModel->party_dropdown();
			// $data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
			 $data['sale_voucher_list'] = $this->TransactionModel->against_voucher_dropdown('18',array(0,8,9,10),date('Y-m-d'));
			 $data['bills_method_list']	      = ['','New Ref.','Adjustment'];
			 $data['voucher_date']            = $voucher_detail['last_entry'];
			 $data['acc_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json');
             $data['bsd_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
         $data['voucher_type_id']                = $voucher_type_id;
         $data['currency_list'] = $this->TransactionModel->get_currency_list();

			return view($this->folder_path.'credit_note/invoice_based',$data);

    }

    public function edit($voucher_txn_id)
    {
    	$voucher_type_id    = 2;

		$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
		if(empty($voucher_info)){
			 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
		$vch_subtype_id = $voucher_info['vch_subtype_id'];

		if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		    // echo "<pre>";print_r($_POST);exit;

				$rules = [				
					// 'sale_date' => [
					// 	'rules'  => 'required',
					// 	'errors' => [
					// 		'required' => 'Voucher Date is required',
					//    ],
				  // 	],
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

		    // $voucher_date        = $this->request->getVar('sale_date'); //can't be changed
		    $voucher_date        = $voucher_info['voucher_date'];
		    $voucher_date 		 = date("Y-m-d", strtotime($voucher_date));
		    $voucher_series      = $this->request->getVar('voucher_series'); 
		    $party_id            = $this->request->getVar('party_id'); 
		    $matrcntr_id         = $this->request->getVar('matrcntr_id');  
		    $narration           = $this->request->getVar('narration'); 
		    $itmsdata            = json_decode($this->request->getVar('itmsdata'),true); 
				$currency_id = $this->request->getVar('currency_id');
		     
		    $billsndrydata = [];
			if(!empty($this->request->getVar('billsndrydata')))
			 	$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
          
          	$batchdata = []; // batch coding
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);
				 			
			 $bbbdata = [];
			if(!empty($this->request->getVar('bbbdata')))
			 	$bbbdata = json_decode($this->request->getVar('bbbdata'),true);
			 
			$sale_voucher_id  = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');
			$voucher_no = $voucher_info['comp_vch_no'];

			if($vch_subtype_id == 0) // sale non item
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
				$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
				$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);

				$this->TransactionModel->delete_bills_txn($voucher_txn_id);
				 $this->TransactionModel->delete_all_narrations($voucher_txn_id);

				$sale_total = array_sum(array_column($itmsdata, 'amount'));
				  
				$billsundry_total = 0;
				if(count($billsndrydata))
					$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

				$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18);
				$voucher_tag = $sale_voucher['voucher_tag']; //voucher tag of sale either SEDCDUE or DCESDUE

				// update voucher consolidated entry
				$insert_data  = array(
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_date"			=> $voucher_date,
						"voucher_tag"			=> $voucher_tag,
						"currency_id"				=> $currency_id,
				);
				$this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

				$insert_data = [
					'acct_crs_id_type'		=> '',
					'comp_id'				=> $this->company_id,
					'txn_id'				=> '',
					'voucher_txn_id'		=> $voucher_txn_id,
					'bo_id'					=> 0,
					'acc_cross_ref_type'	=> 'SALEINV',
					'acc_cross_ref_data'	=> $sale_voucher_id,
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

				if($voucher_tag != '')
				{
				  // Add company txn master entry for party oth
				  $txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $party_id,
						'master_id_type' 		=> 'aco'
				 	);
			    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

			    $acc_oth_txn_tag = '';
			    if(str_contains($voucher_tag, 'SEDC')){
				    $acc_oth_txn_tag = 'SEDCDUE';
				    $acc_oth_txn_status = $sale_voucher_id;
				    $acc_oth_txn_drcr = 'c';
			    }
			    if(str_contains($voucher_tag, 'DCES')){
			    	$acc_oth_txn_tag = 'DCESDUE';
			    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL'); //delivery_challan_id
			    	$acc_oth_txn_drcr = 'd';
			    } 
				   	

				  // Add party oth txn entry
			    $insert_data  = [
					'comp_id'            	 	=> $this->company_id,
					'acc_oth_txn_date'       	=> $voucher_date,
					'acc_oth_txn_amount'     	=> ($sale_total + $billsundry_total),						
					'acc_oth_txn_drcr'       	=> $acc_oth_txn_drcr,
					'voucher_type_id'    	   	=> $voucher_type_id,
					'comp_vch_series_no' 	   	=> $voucher_series,
					'acc_id'             	   	=> $party_id,						
					'voucher_txn_id'     	   	=> $voucher_txn_id,
					'acc_oth_txn_status' 	   	=> $acc_oth_txn_status, 
					'bo_id'					 	=> 0,
					'txn_id'				 	=> $txn_id,
					'acc_oth_txn_duedate' 	 	=> date('Y-m-d'),
					'acc_oth_txn_tag'		    => $acc_oth_txn_tag, //what will be it ?
				  ];
				  $this->TransactionModel->add_acc_oth_data($insert_data);
				}

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
									"comp_id"					=> $this->company_id,
									"comp_vch_series_id"		=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 				=> $value['billsundry_id'],
									'master_id_type' 			=> 'bsd'
							 	);
					      		$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								if($value['billsundry_amount'] >= 0)
									$billsundry_drcr = 'd';
								else
									$billsundry_drcr = 'c';

					      		$insert_data  = array(
						      		"comp_id"					=> $this->company_id,
						      		"sundry_txn_date"			=> $voucher_date,
						      		"sundry_txn_amount"			=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "comp_vch_name"				=> $voucher_no, // ?
					                "comp_vch_series_no"		=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"				=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'					=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
			              		);
			           			$this->TransactionModel->add_sundry_txn_data($insert_data);	
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

			    if(str_contains($voucher_tag, 'SEDC'))
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
			    if(str_contains($voucher_tag, 'DCES'))
			    {
			    	$delivery_challan_id = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL');
			    	$summary = $this->TransactionModel->get_party_oth_summary($delivery_challan_id,'DCES');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
			      
			      	if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

			      	if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
			    } 
				   
				 
		    	// return redirect()->to($this->base_url.'credit_note/non_item');
		    	return json_encode(['status' => true, 'message' => 'Data Updated']);
			}
			if($vch_subtype_id == 1) // debit note with item
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
				$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
				$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
				$this->TransactionModel->delete_bills_txn($voucher_txn_id);
				 $this->TransactionModel->delete_all_narrations($voucher_txn_id);

				$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

				$billsundry_total = 0;
				if(count($billsndrydata))
						$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

				$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18);
				$voucher_tag = $sale_voucher['voucher_tag']; //voucher tag of sale either SEDCDUE or DCESDUE

				// update voucher consolidated entry
			  	$insert_data     = array(
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_date"			=> $voucher_date,
						"mat_cent_id"			=> $matrcntr_id,
						"voucher_tag"			=> $voucher_tag,
						"currency_id"				=> $currency_id,
				);
			  	$this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

			  	$insert_data = [
						'acct_crs_id_type'		=> '',
						'comp_id'				=> $this->company_id,
						'txn_id'				=> '',
						'voucher_txn_id'		=> $voucher_txn_id,
						'bo_id'					=> 0,
						'acc_cross_ref_type'	=> 'SALEINV',
						'acc_cross_ref_data'	=> $sale_voucher_id,
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

				if($voucher_tag != '')
				{
					// Add company txn master entry for party oth
				  $txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $party_id,
						'master_id_type' 		=> 'aco'
				 	);
			    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

			    $acc_oth_txn_tag = '';
			    if(str_contains($voucher_tag, 'SEDC')){
				    $acc_oth_txn_tag = 'SEDCDUE';
				    $acc_oth_txn_status = $sale_voucher_id;
				    $acc_oth_txn_drcr = 'c';
			    }
			    if(str_contains($voucher_tag, 'DCES')){
			    	$acc_oth_txn_tag = 'DCESDUE';
			    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL'); //delivery_challan_id
			    	$acc_oth_txn_drcr = 'd';
			    } 
				   	

				  // Add party oth txn entry
			    $insert_data  = [
					'comp_id'            	 	=> $this->company_id,
					'acc_oth_txn_date'       	=> $voucher_date,
					'acc_oth_txn_amount'     	=> ($sale_total + $billsundry_total),						
					'acc_oth_txn_drcr'       	=> $acc_oth_txn_drcr,					
					'voucher_type_id'    	   	=> $voucher_type_id,
					'comp_vch_series_no' 	   	=> $voucher_series,
					'acc_id'             	   	=> $party_id,						
					'voucher_txn_id'     	   	=> $voucher_txn_id,
					'acc_oth_txn_status' 	   	=> $acc_oth_txn_status, 
					'bo_id'					 	=> 0,
					'txn_id'				 	=> $txn_id,
					'acc_oth_txn_duedate' 	 	=> date('Y-m-d'),
					'acc_oth_txn_tag'		    => $acc_oth_txn_tag, //what will be it ?
				  ];
				  $this->TransactionModel->add_acc_oth_data($insert_data);
				}

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

					$insert_data   = array(
							'comp_id'             => $this->company_id,
							'item_txn_date'       => $voucher_date,
							'item_txn_amount'     => $item_row['item_total_amount'],
							'item_txn_drcr'       => 'd',
							'item_txn_qty'        => $item_row['item_qty'],
							'description'       => $item_row['description'],
							'item_id'             => $item_row['item_id'],
							'voucher_txn_id'      => $voucher_txn_id,
							'voucher_type_id'     => $voucher_type_id,
							'mat_cent_id'         => $matrcntr_id,
							'bo_id'				  => 0,
							'txn_id'			  => $txn_id

					);	
					$item_txn_id = $this->TransactionModel->add_itm_txn_data($insert_data);

					$get_item_info      =  $this->BalancesModel->get_item_balance_info($item_row['item_id']);
					$item_open_qty      =  !empty($get_item_info['op_bal_qty']) ? $get_item_info['op_bal_qty'] : 0;
          			$item_open_value    =  !empty($get_item_info['op_bal_val']) ? $get_item_info['op_bal_val'] : 0;
					

					$insert_data = array(
						"item_txn_date"			=> $voucher_date,
						"item_id"				=> $item_row['item_id'],
						'item_txn_drcr'         => 'd',
						"txn_id"				=> $txn_id,
						"item_txn_id"			=> $item_txn_id,
						"voucher_txn_id"		=> $voucher_txn_id,
						"bo_id"					=> 0,
						"mat_cent_id"			=> $matrcntr_id,
						"item_unit"				=> $item_row['item_unit_id'],
						"item_bal_qty"			=> $item_row['item_qty'],
						"item_avail"          => "1"
				   	);
					$itemtxnbal_id = $this->BalancesModel->add_itemtxnbal($item_row['item_id'],$insert_data,'d',$item_open_qty,$item_open_value);
                    $this->TransactionModel->CalculateValuation($item_row['item_id'],$item_row['item_unit_id'],$matrcntr_id,0,$itemtxnbal_id,'d',$voucher_date);
					$this->TransactionModel->CalculateValuation($item_row['item_id'],$item_row['item_unit_id'],$matrcntr_id,1,$itemtxnbal_id,'d',$voucher_date);
					$this->TransactionModel->backdatevaluation_calculation($item_row['item_id'],$voucher_date);
					// $this->TransactionModel->update_item_balance($party_id);  //function not created in model

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
									"comp_id"					=> $this->company_id,
									"comp_vch_series_id"		=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 				=> $value['billsundry_id'],
									'master_id_type' 			=> 'bsd'
							 	);
					      		$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								if($value['billsundry_amount'] >= 0)
									$billsundry_drcr = 'd';
								else
									$billsundry_drcr = 'c';

					      		$insert_data  = array(
						      		"comp_id"					=> $this->company_id,
						      		"sundry_txn_date"			=> $voucher_date,
						      		"sundry_txn_amount"			=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "comp_vch_name"				=> $voucher_no, // ?
					                "comp_vch_series_no"		=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"				=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'					=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
			              		);
			           			$this->TransactionModel->add_sundry_txn_data($insert_data);	
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

			    if(str_contains($voucher_tag, 'SEDC'))
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
			    if(str_contains($voucher_tag, 'DCES'))
			    {
			    	$delivery_challan_id = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL');
			    	$summary = $this->TransactionModel->get_party_oth_summary($delivery_challan_id,'DCES');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    	if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
			      
			      	if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

			      	if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
			    } 
			    // return redirect()->to($this->base_url.'credit_note/item');
			    return json_encode(['status' => true, 'message' => 'Data Updated']);
			}
			if($vch_subtype_id == 2) // debit note without item
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
				$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
				$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
				$this->TransactionModel->delete_bills_txn($voucher_txn_id);
				 $this->TransactionModel->delete_all_narrations($voucher_txn_id);

				$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

				$billsundry_total = 0;
				if(count($billsndrydata))
					$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

				$sale_voucher = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, 18);
				$voucher_tag = $sale_voucher['voucher_tag']; //voucher tag of sale either SEDCDUE or DCESDUE

				// update voucher consolidated entry
			  	$insert_data     = array(
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_date"			=> $voucher_date,
						"mat_cent_id"			=> $matrcntr_id,
						"voucher_tag"			=> $voucher_tag,
						"currency_id"				=> $currency_id,
				);
			  	$this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

			  	$insert_data = [
						'acct_crs_id_type'		=> '',
						'comp_id'				=> $this->company_id,
						'txn_id'				=> '',
						'voucher_txn_id'		=> $voucher_txn_id,
						'bo_id'					=> 0,
						'acc_cross_ref_type'	=> 'SALEINV',
						'acc_cross_ref_data'	=> $sale_voucher_id,
						'acc_cross_logdate'   	=> date('Y-m-d'),
				];
				$this->TransactionModel->add_acc_crsref_data($insert_data);

				$insert_data = [
						'acct_crs_id_type'		=> $voucher_type_id,
						'comp_id'				=> $this->company_id,
						'txn_id'				=> '',
						'voucher_txn_id'		=> $voucher_txn_id,
						'bo_id'					=> 0,
						'acc_cross_ref_type'	=> 'SESIDUE',
						'acc_cross_ref_data'	=> $sale_voucher_id,
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

				if($voucher_tag != '')
				{
				  // Add company txn master entry for party oth
				  $txn_data = array(
						"comp_id"				=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 		=> $voucher_txn_id,
						"master_id" 			=> $party_id,
						'master_id_type' 		=> 'aco'
				 	);
			    $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

			    $acc_oth_txn_tag = '';
			    if(str_contains($voucher_tag, 'SEDC')){
				    $acc_oth_txn_tag = 'SEDCDUE';
				    $acc_oth_txn_status = $sale_voucher_id;
				    $acc_oth_txn_drcr = 'c';
			    }
			    if(str_contains($voucher_tag, 'DCES')){
			    	$acc_oth_txn_tag = 'DCESDUE';
			    	$acc_oth_txn_status = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL'); //delivery_challan_id
			    	$acc_oth_txn_drcr = 'd';
			    } 
				   	

				  // Add party oth txn entry
			    $insert_data  = [
					'comp_id'            	 	=> $this->company_id,
					'acc_oth_txn_date'       	=> $voucher_date,
					'acc_oth_txn_amount'     	=> ($sale_total + $billsundry_total),						
					'acc_oth_txn_drcr'       	=> $acc_oth_txn_drcr,
					'voucher_type_id'    	   	=> $voucher_type_id,
					'comp_vch_series_no' 	   	=> $voucher_series,
					'acc_id'             	   	=> $party_id,						
					'voucher_txn_id'     	   	=> $voucher_txn_id,
					'acc_oth_txn_status' 	   	=> $acc_oth_txn_status, 
					'bo_id'					 	=> 0,
					'txn_id'				 	=> $txn_id,
					'acc_oth_txn_duedate' 	 	=> date('Y-m-d'),
					'acc_oth_txn_tag'		    => $acc_oth_txn_tag, //what will be it ?
				  ];
				  $this->TransactionModel->add_acc_oth_data($insert_data);
				}

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
									"comp_id"					=> $this->company_id,
									"comp_vch_series_id"		=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 				=> $value['billsundry_id'],
									'master_id_type' 			=> 'bsd'
							 	);
					      		$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								if($value['billsundry_amount'] >= 0)
									$billsundry_drcr = 'd';
								else
									$billsundry_drcr = 'c';

					      		$insert_data  = array(
						      		"comp_id"					=> $this->company_id,
						      		"sundry_txn_date"			=> $voucher_date,
						      		"sundry_txn_amount"			=> abs($value['billsundry_amount']),
					                "sundry_txn_drcr"			=> $billsundry_drcr,
					                "comp_vch_name"				=> $voucher_no, // ?
					                "comp_vch_series_no"		=> $voucher_no,
					                "bill_sundry_id"			=> $value['billsundry_id'],
					                "sundry_bal"				=> 0,
					                "voucher_txn_id"			=> $voucher_txn_id, 
					                "voucher_type_id"			=> $voucher_type_id,
					                'txn_id'					=> $txn_id,
					                'sundry_tag_rate'			=> $value['billsundry_rate']
			              		);
			           			$this->TransactionModel->add_sundry_txn_data($insert_data);	
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

			    if(str_contains($voucher_tag, 'SEDC'))
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
			    if(str_contains($voucher_tag, 'DCES'))
			    {
			    	$delivery_challan_id = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL');
			    	$summary = $this->TransactionModel->get_party_oth_summary($delivery_challan_id,'DCES');
				    $cr_total = $summary['cr_total'];
				    $dr_total = $summary['dr_total'];

				    if($cr_total > $dr_total)  
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
			      
			      	if($cr_total < $dr_total)
			         	$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

			      	if($cr_total == $dr_total)
			      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
			    }
			    // return redirect()->to($this->base_url.'credit_note/item');
			    return json_encode(['status' => true, 'message' => 'Data Updated']);
			}
		}

		if($vch_subtype_id == 0) // debit note non item
		{
			$party_transaction 	= $this->TransactionModel->get_party_transaction($voucher_txn_id);
			$data['party_id'] 	= $party_transaction['acc_id'];
			$data['narration'] 	= $party_transaction['acc_txn_narr'];

			$data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
			$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id);

			$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);

			$data['message_output']     = $this->message_output;
			 $data['get_narration_info']     = $get_narration_info; 
			$data['base_url']       	= $this->base_url; 
			$data['folder_path']    	= $this->folder_path;
			$data['party_dropdown']     = $this->TransactionModel->party_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);

			$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
			$data['voucher_no'] 		= $voucher_info['comp_vch_no'];
			$data['voucher_date'] 		= date('d-m-Y', strtotime($voucher_info['voucher_date']));
			$data['voucher_txn_id']     = $voucher_txn_id;

			$sale_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');
						
			$p_voucher_info = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, '18');
			$p_voucher_no = $p_voucher_info['comp_vch_no'];
			$data['against'] = 'Voucher No. '.$p_voucher_no;
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
			$data['voucher_type_id']                = $voucher_type_id;
			$data['acc_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json');
             $data['bsd_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
      $data['currency_list'] = $this->TransactionModel->get_currency_list();
			$data['currency_id'] = $voucher_info['currency_id'];

			return view($this->folder_path.'credit_note/edit_invoice_based',$data);
		}
		if($vch_subtype_id == 1) // debit note with item
		{
			$party_transaction 	= $this->TransactionModel->get_party_transaction($voucher_txn_id);
			$data['party_id'] 					= $party_transaction['acc_id'];
			$data['narration'] 					= $party_transaction['acc_txn_narr'];
			$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];
			 $data['get_narration_info']     = $get_narration_info; 

			// $data['account_transactions']  = $this->TransactionModel->get_account_transactions($voucher_txn_id);
			$data['item_transactions']     = $this->TransactionModel->get_item_transactions($voucher_txn_id);
			$data['sundry_transactions']   = $this->TransactionModel->get_sundry_transactions($voucher_txn_id);

			$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);

			$data['message_output']           = $this->message_output;
			$data['base_url']       	      = $this->base_url; 
			$data['folder_path']    	      = $this->folder_path;
			$data['party_dropdown']           = $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  	  = $this->TransactionModel->matrcntr_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['items_list']     		  = $this->TransactionModel->items_list();	
			$data['units_list']     		  = $this->TransactionModel->units_dropdown();

			$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
			$data['voucher_no'] 		= $voucher_info['comp_vch_no'];
			$data['voucher_date'] 		= date('d-m-Y', strtotime($voucher_info['voucher_date']));
			$data['voucher_txn_id']     = $voucher_txn_id;
			$data['vch_subtype']     	= $this->TransactionModel->get_vch_subtype($vch_subtype_id);

			$sale_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');
						
			$p_voucher_info = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, '18');
			$p_voucher_no = $p_voucher_info['comp_vch_no'];
			$data['against'] = 'Voucher No. '.$p_voucher_no;
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
			$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
	        $data['voucher_type_id']                = $voucher_type_id;
	         $data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
              $data['bsd_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
      $data['currency_list'] = $this->TransactionModel->get_currency_list();
			$data['currency_id'] = $voucher_info['currency_id'];

			return view($this->folder_path.'credit_note/edit_item_based',$data);
		}
		if($vch_subtype_id == 2) // debit note without item
		{
			$party_transaction 	= $this->TransactionModel->get_party_transaction($voucher_txn_id);
			$data['party_id'] 					= $party_transaction['acc_id'];
			$data['narration'] 					= $party_transaction['acc_txn_narr'];
			$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];
			 $data['get_narration_info']     = $get_narration_info; 

			// $data['account_transactions']  = $this->TransactionModel->get_account_transactions($voucher_txn_id);
			$data['item_transactions']     = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
			$data['sundry_transactions']   = $this->TransactionModel->get_sundry_transactions($voucher_txn_id);

			$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);

			$data['message_output']           = $this->message_output;
			$data['base_url']       	      = $this->base_url; 
			$data['folder_path']    	      = $this->folder_path;
			$data['party_dropdown']           = $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  	  = $this->TransactionModel->matrcntr_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['items_list']     		  = $this->TransactionModel->items_list();	
			$data['units_list']     		  = $this->TransactionModel->units_dropdown();

			$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
			$data['voucher_no'] 		= $voucher_info['comp_vch_no'];
			$data['voucher_date'] 		= date('d-m-Y', strtotime($voucher_info['voucher_date']));
			$data['voucher_txn_id']     = $voucher_txn_id;
			$data['vch_subtype']     	= $this->TransactionModel->get_vch_subtype($vch_subtype_id);

			$sale_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');
						
			$p_voucher_info = $this->TransactionModel->get_voucher_cons_info($sale_voucher_id, '18');
			$p_voucher_no = $p_voucher_info['comp_vch_no'];
			$data['against'] = 'Voucher No. '.$p_voucher_no;
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
	         $data['voucher_type_id']                = $voucher_type_id;
	         $data['item_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
            
             $data['acc_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/acc.json');
             $data['bsd_json_file']           = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
      $data['currency_list'] = $this->TransactionModel->get_currency_list();
			$data['currency_id'] = $voucher_info['currency_id'];
			       
			return view($this->folder_path.'credit_note/edit_item_based',$data);
		}
    }

    public function delete($voucher_txn_id)
	{
 		$voucher_type_id    = 2;

		$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);

		if(!empty($voucher_info))
		{
			//check inward challan
			$check = $this->TransactionModel->check_crsref($voucher_txn_id, 'CRNTINV');
			if($check){
				return json_encode(['status' => false, 'message' => 'Credit Note has associated Inward Challan.']);
				echo "Credit Note has associated Inward Challan.";
				exit;
			}

			$voucher_tag = $voucher_info['voucher_tag'];
			$sale_voucher_id = $this->TransactionModel->get_crsref($voucher_txn_id, 'SALEINV');

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

			$this->TransactionModel->delete_bills_txn($voucher_txn_id);
			$this->TransactionModel->delete_all_narrations($voucher_txn_id);

			if(str_contains($voucher_tag, 'SEDC'))
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
		    if(str_contains($voucher_tag, 'DCES'))
		    {
		    	$delivery_challan_id = $this->TransactionModel->get_crsref($sale_voucher_id, 'DELCHAL');
		    	$summary = $this->TransactionModel->get_party_oth_summary($delivery_challan_id,'DCES');
			    $cr_total = $summary['cr_total'];
			    $dr_total = $summary['dr_total'];

			    if($cr_total > $dr_total)  
		      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
		      
		      	if($cr_total < $dr_total)
		         	$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

		      	if($cr_total == $dr_total)
		      		$this->TransactionModel->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
		    }

		}
		// return redirect()->to($this->base_url.'credit_note/item');
		return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
	} 
     
	
}