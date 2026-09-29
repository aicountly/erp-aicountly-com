<?php
namespace App\Controllers\Admin;

use App\Models\CommonModel;
use App\Models\Admin\TransactionModel;

use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Stock_transfer  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);

		$this->CommonModel     = new CommonModel();	
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
   
    public function register(){
         
         $data['base_url']           = $this->base_url;
			$data['message_output']     = $this->message_output;
			$data['folder_path']        = $this->folder_path;
		
       if($this->request->getMethod() == 'get'){	
           
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!='' && $_GET['register_type']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];  
			$register_type = $_GET['register_type'];
			
			 $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
			
           	$data['from_date']           = $from_date;
	     	$data['to_date']             = $to_date;
	     	$data["voucher_trans"]       = array();
	     	$data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
	     	 if($register_type=='stock_transfer_mc')
	     	      return view($this->folder_path.'registers/stock_transfer_mc_register',$data); 
	     	else if($register_type=='stock_transfer_intransit')
	     	      return view($this->folder_path.'registers/stock_transfer_intransit_register',$data);       
	     		else if($register_type=='stock_transfer_receipt')
	     	      return view($this->folder_path.'registers/stock_transfer_receipt_register',$data); 
	     		else if($register_type=='stock_transfer_bo')
	     	      return view($this->folder_path.'registers/stock_transfer_bo_register',$data); 
	     	      
	     	 else
	     	 return view($this->folder_path.'registers/other_register',$data);
            }
       }
       
      
        return view($this->folder_path.'registers/stock_transfer_register',$data);  
    }
  
  public function mc() 
  {
			$voucher_type_id       = "15";
			$vch_subtype_id        = "16";// subtype id

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
						'frommc' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'From MC is required'
						  ],
						],
						'tomc' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'To MC is required'
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

			    $long_narration   = $this->request->getVar('long_narration');
					$voucher_date     = $this->request->getVar('sale_date');
					$voucher_date     = validate_date_by_fy($voucher_date);
					$frommc           = $this->request->getVar('frommc'); 
					$tomc             = $this->request->getVar('tomc'); 
					$voucher_series   = $this->request->getVar('voucher_series'); 					
					$itmsdata         = json_decode($this->request->getVar('itmsdata'),true); 
					$currency_id 			= $this->request->getVar('currency_id');

					$billsndrydata = [];
					if(!empty($this->request->getVar('billsndrydata')))
						$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true); 
		     	
		     	$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);
		     	
		     	$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

					$billsundry_total = 0;
					if(count($billsndrydata))
							$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));


					// add voucher consolidated entry
					$insert_data     = array(
							"comp_id"			       => $this->company_id,
							"comp_vch_series_id" => $voucher_series,
							"comp_vch_no"		     => $voucher_no,
							"voucher_type_id"	   => $voucher_type_id,
							"voucher_date"		   => $voucher_date,
							"mat_cent_id"		     => $frommc,
							"vch_subtype_id"	   => $vch_subtype_id,
							"voucher_tag"		     => '',
							"currency_id"				 => $currency_id,
					);
					
					$voucher_txn_id        = $this->TransactionModel->add_voucher_cons_data($insert_data);                    
					
					 // Add company txn master entry for party oth
					$txn_data = array(
							"comp_id"							=> $this->company_id,
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_txn_id" 			=> $voucher_txn_id,
							"master_id" 					=> 0,
							'master_id_type' 			=> 'aco'
					 	);
	        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

					// Add party oth txn entry 
			    $insert_data  = [
							'comp_id'            	   => $this->company_id,
							'acc_oth_txn_date'       => $voucher_date,
							'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
							'acc_oth_txn_drcr'       => 'c',							
							'voucher_type_id'    	   => $voucher_type_id,
							'comp_vch_series_no' 	   => $voucher_series,
							'acc_id'             	   => 0,						
							'voucher_txn_id'     	   => $voucher_txn_id,
							'acc_oth_txn_status' 	   => $voucher_txn_id, //changed from $voucher_no
							'bo_id'					 				 => 0,
							'txn_id'				         => $txn_id,
							'acc_oth_txn_duedate' 	 => date('Y-m-d'),
							'acc_oth_txn_tag'		     => '',
					];
					$this->TransactionModel->add_acc_oth_data($insert_data);

					 // Add company txn master entry for party oth
					$txn_data = array(
							"comp_id"							=> $this->company_id,
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_txn_id" 			=> $voucher_txn_id,
							"master_id" 					=> 0,
							'master_id_type' 			=> 'aco'
					 	);
	        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

					// Add party oth txn entry 
			    $insert_data  = [
							'comp_id'            	   => $this->company_id,
							'acc_oth_txn_date'       => $voucher_date,
							'acc_oth_txn_amount'     => $sale_total,						
							'acc_oth_txn_drcr'       => 'd',							
							'voucher_type_id'    	   => $voucher_type_id,
							'comp_vch_series_no' 	   => $voucher_series,
							'acc_id'             	   => 0,						
							'voucher_txn_id'     	   => $voucher_txn_id,
							'acc_oth_txn_status' 	   => $voucher_txn_id, //changed from $voucher_no
							'bo_id'					 				 => 0,
							'txn_id'				         => $txn_id,
							'acc_oth_txn_duedate' 	 => date('Y-m-d'),
							'acc_oth_txn_tag'		     => '',
					];
					$this->TransactionModel->add_acc_oth_data($insert_data);

					//Add crsref 
					$insert_data = [
							'acct_crs_id_type'		   => '',
							'comp_id'				         => $this->company_id,
							'txn_id'				         => '',
							'voucher_txn_id'		     => $voucher_txn_id,
							'bo_id'					         => 0,
							'acc_cross_ref_type'	   => 'TOMCSTK',
							'acc_cross_ref_data'	   => $tomc,
							'acc_cross_logdate'   	 => date('Y-m-d'),
					];
					$this->TransactionModel->add_acc_crsref_data($insert_data);

			    foreach($itmsdata as $item_row)
			    {

							$txn_data = array(
								"comp_id"				      => $this->company_id,
								"comp_vch_series_id"	=> $voucher_series,
								"voucher_txn_id" 		  => $voucher_txn_id,
								"master_id" 			    => $item_row['item_id'],
								'master_id_type' 		  => 'itm'
							);
							$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

							$insert_data   = array(
									'comp_id'             => $this->company_id,
									'item_txn_date'       => $voucher_date,
									'item_txn_amount'     => $item_row['item_total_amount'],
									'item_txn_drcr'       => 'c',
									'item_txn_qty'        => $item_row['item_qty'],
									'item_id'             => $item_row['item_id'],
									'description'         => $item_row['description'],
									'voucher_txn_id'      => $voucher_txn_id,
									'voucher_type_id'     => $voucher_type_id,
									'mat_cent_id'         => $frommc,
									'bo_id'				  			=> 0,
									'txn_id'			  			=> $txn_id,
									"item_unit"						=> $item_row['item_unit_id'],
									"item_bal_qty"				=> 0,
									"item_avail"  				=> 1,
									"batch_id"  				  => 0,	
							);	
							$this->TransactionModel->add_itm_txn_data($insert_data);

							//------------------------------------------------------------------------------------------
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
									'item_id'             => $item_row['item_id'],
									'description'         => $item_row['description'],
									'voucher_txn_id'      => $voucher_txn_id,
									'voucher_type_id'     => $voucher_type_id,
									'mat_cent_id'         => $tomc,
									'bo_id'				  			=> 0,
									'txn_id'			  			=> $txn_id,
									"item_unit"						=> $item_row['item_unit_id'],
									"item_bal_qty"				=> 0,
									"item_avail"  				=> 1,
									"batch_id"  				  => 0,	
							      );	
							$this->TransactionModel->add_itm_txn_data($insert_data);

		    	} //for loop 

		    	$insert_data = [
		              "comp_id"             => $this->company_id,
		              "comp_vch_series_id"  => $voucher_series,
		              "voucher_txn_id"      => $voucher_txn_id,
		              "master_id"           => 0,
		              'master_id_type'      => 'nrr'
            		];

			    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
			    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$long_narration);


		  	  if(count($billsndrydata) && $voucher_txn_id)
			    {
							foreach($billsndrydata as $value)
							{
									$txn_data = array(
										"comp_id"				=> $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id"    	=> $voucher_txn_id,
										"master_id" 			=> $value['billsundry_id'],
										'master_id_type' 		=> 'bso'
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
												'acc_oth_txn_narr'       => '',
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
			    // return redirect()->to($this->base_url.'stock_transfer/mc');
				return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
			}	
		
            $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
			$data['message_output'] 					= $this->message_output;
			$data['voucher_auto_no']  				= $this->TransactionModel->get_voucher_no($voucher_type_id);
			$data['base_url']       					= $this->base_url; 
			$data['folder_path']    					= $this->folder_path;
			// $data['items_list']     					= $this->TransactionModel->items_list();
			$data['units_list']     					= $this->TransactionModel->units_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
			$data['party_dropdown']     			= $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
			$data['sub_type_dropdown']  			= $this->TransactionModel->vch_subtype_dropdown($voucher_type_id);
			$data['billsundry_items']  				= $this->TransactionModel->billsundry_items();
			
			$data['voucher_date']                    = $voucher_detail['last_entry'];
			$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
			$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

            $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
            $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
            $data['voucher_type_id']                = $voucher_type_id;
		   $data['currency_list'] = $this->TransactionModel->get_currency_list();
	
		return view($this->folder_path.'stock_transfer/mc',$data);		
  }

  public function edit($voucher_txn_id)
	{
			$voucher_type_id    = "15";

			$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
			if(empty($voucher_info)){
				 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}
            $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
			$vch_subtype_id = $voucher_info['vch_subtype_id'];

			if($this->request->getMethod() == 'post' && $this->request->isAjax()){
		  		// echo "<pre>";print_r($_POST);exit;
			    
			  	if($vch_subtype_id == 16)
			  	{
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
								'frommc' => [
									'rules'  => 'required',
									'errors' => [
										'required' => 'From MC is required'
								  ],
								],
								'tomc' => [
									'rules'  => 'required',
									'errors' => [
										'required' => 'To MC is required'
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
							$frommc           = $this->request->getVar('frommc'); 
							$tomc             = $this->request->getVar('tomc'); 
							$voucher_series   = $this->request->getVar('voucher_series'); 
							$narration        = $this->request->getVar('narration'); 
							$itmsdata         = json_decode($this->request->getVar('itmsdata'),true); 
							$currency_id 			= $this->request->getVar('currency_id');

							$billsndrydata = [];
							if(!empty($this->request->getVar('billsndrydata')))
								$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true); 
				     	
				     	
				     	$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
							foreach ($data as $key => $value) {
									
									if($value['master_id_type'] == 'itm'){
											$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
									}
							}
							$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
							$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
							$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
							$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
							$this->TransactionModel->delete_all_narrations($voucher_txn_id);

							$sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

							$billsundry_total = 0;
							if(count($billsndrydata))
									$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));


							$voucher_no = $voucher_info['comp_vch_no'];

							$insert_data  = array(
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_date"				=> $voucher_date,
									'mat_cent_id'					=> $frommc,
									"currency_id"					=> $currency_id,
							);
					  	$this->TransactionModel->update_voucher_cons_data($voucher_txn_id, $insert_data);

					  	 // Add company txn master entry for party oth
							$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> 0,
									'master_id_type' 			=> 'aco'
							 	);
			        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

							// Add party oth txn entry 
					    $insert_data  = [
									'comp_id'            	   => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => ($sale_total + $billsundry_total),						
									'acc_oth_txn_drcr'       => 'c',									
									'voucher_type_id'    	   => $voucher_type_id,
									'comp_vch_series_no' 	   => $voucher_series,
									'acc_id'             	   => 0,						
									'voucher_txn_id'     	   => $voucher_txn_id,
									'acc_oth_txn_status' 	   => $voucher_txn_id, //changed from $voucher_no
									'bo_id'					 				 => 0,
									'txn_id'				         => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		     => '',
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);

							 // Add company txn master entry for party oth
							$txn_data = array(
									"comp_id"							=> $this->company_id,
									"comp_vch_series_id"	=> $voucher_series,
									"voucher_txn_id" 			=> $voucher_txn_id,
									"master_id" 					=> 0,
									'master_id_type' 			=> 'aco'
							 	);
			        $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

							// Add party oth txn entry 
					    $insert_data  = [
									'comp_id'            	   => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => $sale_total,						
									'acc_oth_txn_drcr'       => 'd',							
									'voucher_type_id'    	   => $voucher_type_id,
									'comp_vch_series_no' 	   => $voucher_series,
									'acc_id'             	   => 0,						
									'voucher_txn_id'     	   => $voucher_txn_id,
									'acc_oth_txn_status' 	   => $voucher_txn_id, //changed from $voucher_no
									'bo_id'					 				 => 0,
									'txn_id'				         => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		     => '',
							];
							$this->TransactionModel->add_acc_oth_data($insert_data);

							//Add crsref 
							$insert_data = [
									'acct_crs_id_type'		=> '',
									'comp_id'				=> $this->company_id,
									'txn_id'				=> '',
									'voucher_txn_id'		=> $voucher_txn_id,
									'bo_id'					=> 0,
									'acc_cross_ref_type'	=> 'TOMCSTK',
									'acc_cross_ref_data'	=> $tomc,
									'acc_cross_logdate'   	=> date('Y-m-d'),
							];
							$this->TransactionModel->add_acc_crsref_data($insert_data);


					    foreach($itmsdata as $item_row)
					    {

									$txn_data = array(
										"comp_id"			    => $this->company_id,
										"comp_vch_series_id"	=> $voucher_series,
										"voucher_txn_id" 	    => $voucher_txn_id,
										"master_id" 			=> $item_row['item_id'],
										'master_id_type' 		=> 'itm'
									);
									$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

									$insert_data   = array(
											'comp_id'             => $this->company_id,
											'item_txn_date'       => $voucher_date,
											'item_txn_amount'     => $item_row['item_total_amount'],
											'item_txn_drcr'       => 'c',
											'item_txn_qty'        => $item_row['item_qty'],
											'description'         => $item_row['description'],
											'item_id'             => $item_row['item_id'],
											'voucher_txn_id'      => $voucher_txn_id,
											'voucher_type_id'     => $voucher_type_id,
											'mat_cent_id'         => $frommc,
											'bo_id'				  			=> 0,
											'txn_id'			  			=> $txn_id,
											"item_unit"						=> $item_row['item_unit_id'],
											"item_bal_qty"				=> 0,
											"item_avail"  				=> 1,
											"batch_id"  				  => 0,										

									);	
									$this->TransactionModel->add_itm_txn_data($insert_data);


									//------------------------------------------------------------------------------------------
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
											'description'         => $item_row['description'],
											'item_id'             => $item_row['item_id'],
											'voucher_txn_id'      => $voucher_txn_id,
											'voucher_type_id'     => $voucher_type_id,
											'mat_cent_id'         => $tomc,
											'bo_id'				  			=> 0,
											'txn_id'			  			=> $txn_id,
											"item_unit"						=> $item_row['item_unit_id'],
											"item_bal_qty"				=> 0,
											"item_avail"  				=> 1,
											"batch_id"  				  => 0,
									    );	
									
									$this->TransactionModel->add_itm_txn_data($insert_data);

				    	} //for loop 

				    	$insert_data = [
			              "comp_id"             => $this->company_id,
			              "comp_vch_series_id"  => $voucher_series,
			              "voucher_txn_id"      => $voucher_txn_id,
			              "master_id"           => 0,
			              'master_id_type'      => 'nrr'
              		];

					    $txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
					    $this->TransactionModel->save_voucher_narration($voucher_txn_id,$txn_id,'long',$narration);
				      	  
				  	  if(count($billsndrydata) && $voucher_txn_id)
					    {
									foreach($billsndrydata as $value)
									{
											$txn_data = array(
												"comp_id"				=> $this->company_id,
												"comp_vch_series_id"	=> $voucher_series,
												"voucher_txn_id" 		=> $voucher_txn_id,
												"master_id" 			=> $value['billsundry_id'],
												'master_id_type' 		=> 'bso'
										 	);
								      $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

								      if($value['billsundry_amount'] >= 0)
								      		$billsundry_drcr = 'd';
								      else
								      	  $billsundry_drcr = 'c';

								       $insert_data  = [
														'comp_id'            	 => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => abs($value['billsundry_amount']),						
														'acc_oth_txn_drcr'       => $billsundry_drcr,
														'voucher_type_id'    	 => $voucher_type_id,
														'comp_vch_series_no' 	 => $voucher_series,
														'acc_id'             	 => $value['billsundry_id'],						
														'voucher_txn_id'     	 => $voucher_txn_id,
														'acc_oth_txn_status' 	 => $value['billsundry_rate'],
														'bo_id'					 				 => 0,
														'txn_id'				         => $txn_id,
														'acc_oth_txn_duedate' 	 => date('Y-m-d'),
														'acc_oth_txn_tag'		     => 'BILSDRY',
												];
												$this->TransactionModel->add_acc_oth_data($insert_data);	
									}
					    }
					    // return redirect()->to($this->base_url.'stock_transfer/mc');
			  	}
					

					return json_encode(['status' => true, 'message' => 'Voucher Updated']);
			}
	
	$allbos           =  $this->TransactionModel->all_mig_bo_lists();
			$data['allbos']   = $allbos;
			
			if($vch_subtype_id == 16)
			{
					// $party_transaction 	= $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
					// $data['party_id'] 					= $party_transaction['acc_id'];
					
					$data['get_narration_info']          = $get_narration_info;
					
					$data['narration'] 					= $get_narration_info['vch_narr'] ?? '';
					$data['from_mc'] 				    = $voucher_info['mat_cent_id'];
					$data['to_mc'] 				      = $this->TransactionModel->get_crsref($voucher_txn_id, 'TOMCSTK');
					
					$data['item_transactions'] = $this->TransactionModel->get_item_transactions_by_mc($voucher_txn_id, $data['from_mc']);
					$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id);

					$data['message_output'] 					= $this->message_output;
					$data['voucher_auto_no']  				= $this->TransactionModel->get_voucher_no($voucher_type_id);
					$data['base_url']       					= $this->base_url; 
					$data['folder_path']    					= $this->folder_path;
					// $data['items_list']     					= $this->TransactionModel->items_list();
					$data['units_list']     					= $this->TransactionModel->units_dropdown();
					$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
					$data['party_dropdown']     			= $this->TransactionModel->party_dropdown();
					$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
					$data['sub_type_dropdown']  			= $this->TransactionModel->vch_subtype_dropdown($voucher_type_id);
					$data['billsundry_items']  				= $this->TransactionModel->billsundry_items();

					 $data['voucher_series']      = $voucher_info['comp_vch_series_id'];
					 $data['voucher_no'] 		  = $voucher_info['comp_vch_no'];
					 $data['voucher_date'] 		  = date('d-m-Y', strtotime($voucher_info['voucher_date']));
					 $data['voucher_txn_id']      = $voucher_txn_id;
					 $data['vch_subtype']         = $this->TransactionModel->get_vch_subtype($vch_subtype_id);
					$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
                    $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

					 $data['item_json_file']      = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
                     $data['bsd_json_file']       = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
                     $data['voucher_type_id']                = $voucher_type_id;

          $data['currency_list'] = $this->TransactionModel->get_currency_list();
					$data['currency_id'] = $voucher_info['currency_id'];
					
					return view($this->folder_path.'stock_transfer/edit_mc',$data);
			}

	}

  public function delete($voucher_txn_id)
	{
	 		$voucher_type_id    = "15";

			$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);

			if(!empty($voucher_info)){

				$data = $this->TransactionModel->get_comp_txn_data($voucher_txn_id);
				foreach ($data as $key => $value) {
						if($value['master_id_type'] == 'itm'){
								$this->TransactionModel->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
						}
				}
				$this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
				$this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
				$this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
				$this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);
				$this->TransactionModel->delete_all_narrations($voucher_txn_id);
			}

			
			// return redirect()->to($this->base_url.'stock_transfer/mc');
			return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
	}
     public function add2()
    {
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']     					= $this->TransactionModel->items_list();
		$data['units_list']     					= $this->TransactionModel->units_dropdown();
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['party_dropdown']  = $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
		return view($this->folder_path.'stock_transfer/add2',$data);		
     }
     public function add3()
    {
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		
		$data['message_output']   = $this->message_output;
		$data['base_url']         = $this->base_url; 
		$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']     					= $this->TransactionModel->items_list();
		$data['units_list']     					= $this->TransactionModel->units_dropdown();
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['party_dropdown']  = $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
		return view($this->folder_path.'stock_transfer/add3',$data);		
     }
	
}