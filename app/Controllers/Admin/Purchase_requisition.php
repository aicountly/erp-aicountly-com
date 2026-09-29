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
		$this->LogModel          = new ERPLogModel();
		$this->auth_session      = new auth_session();			
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url         =  base_url().'/'.getenv('AdminPath');
		$this->folder_path      =  getenv('AdminPath');
		$this->session    	    = \Config\Services::session();
		$this->auth_session->is_company_opened();
		$this->comp_code        =  $this->session->get('ses_company_code');
		$this->company_id       =  $this->session->get('ses_company_id');
		$this->enc_string       =  new enc_string();
		$this->getReferrer      =  \Config\Services::request()->getUserAgent()->getReferrer();
	}
     
	public function with_amount()
	{
		$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
		$voucher_type_id    = "21"; // change it
		$oth_txn_tag 				= 'PURREQN';
		$bo_state_code    = $this->session->get('ses_bostecd');
		$bo_id            = $this->session->get('ses_boid');
        $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
		if($this->request->getMethod() == 'post' && $this->request->isAjax())
		{	
			 //echo "<pre>";print_r($_POST);exit;
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
	  $voucher_date         = $this->request->getVar('sale_date');
	  $voucher_date         = validate_date_by_fy($voucher_date);
	  $invoice_type         = $this->request->getVar('invoice_type');
	  $party_id             = $this->request->getVar('party_id');                  
	  $voucher_series       = $this->request->getVar('voucher_series'); 
	  $matrcntr_id          = $this->request->getVar('matrcntr_id'); 
	  $narration            = $this->request->getVar('narration');  
	  $itmsdata             = json_decode($this->request->getVar('itmsdata'),true); 
	  $currency_id 		    = $this->request->getVar('currency_id');
	  $voucher_no           = $this->TransactionModel->get_voucher_no($voucher_type_id);
	  $billno 		        = $this->request->getVar('billno');
	  $reverse_charges      = $this->request->getVar('reverse_charges');
	  $pos                  = $this->request->getVar('pos');
	  $transporter          = $this->request->getVar('transporter');
	  $transporter_name     = $this->request->getVar('transporter_name');			
	  $transporter_doc_no   = $this->request->getVar('transporter_doc_no');
	  $transport_mode       = $this->request->getVar('transport_mode');			
	  $transport_distance   = $this->request->getVar('transport_distance');
	  $transport_doc_date   = $this->request->getVar('transport_doc_date');
	  $vehicle_no           = $this->request->getVar('vehicle_no');
	  $vehicle_type         = $this->request->getVar('vehicle_type');			 
	  $reverse_charges      = $this->request->getVar('reverse_charges');
	  $gstpaidacc_id        = $this->request->getVar('gst_paidacc_id');
	  $fcy_forex_rate       = $this->request->getVar('fcy_forex_rate');
	  $memoCheck            = $this->request->getVar('memoCheck');			
	  $taxInclusive         = $this->request->getVar('taxInclusive');
	  $purchase_type        = $this->request->getVar('purchase_type'); // 5,6,7
	  $isvoucher_autobillno = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
	  $eco_id               = $this->request->getVar('eco_id');
	  if($eco_id!='')
		$outsup_eco='1';
	  else
		$outsup_eco='0';	
			
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			 if($billno=="" || $billno=="0")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']]);
			$billno_duplicate = $this->TransactionModel->PurchaseBillNoDuplicate($billno);
			if($billno_duplicate=="1")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']]);
			}	
				 $billsndrydata = [];
				  if(!empty($this->request->getVar('billsndrydata')))
				 		$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
				 
					// echo "<pre>";print_r($billsndrydata);exit;
					
					$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);

				  $voucher_txn_id = 0;
				  $sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));

					$billsundry_total = 0;
					if(count($billsndrydata))
							$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));

					$batchdata = [];
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);
						
				$taxsummarydata = [];
				if(!empty($this->request->getVar('taxsummarydata')))
					$taxsummarydata = json_decode($this->request->getVar('taxsummarydata'),true);
		
					$batchdata = [];
				  if(!empty($this->request->getVar('batchinfo_array')))
				 			$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);

			$sale_memo_total  = array_sum(array_column($itmsdata, 'memo_amount'));
			$txinc_amount     = array_sum(array_column($itmsdata, 'txinc_amount'));
			
			$billsundry_memo_total = 0;
			if(count($billsndrydata))
				$billsundry_memo_total = array_sum(array_column($billsndrydata, 'memo_amount'));
			
			
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
				if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
						  $bill_no_counter    = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,0);
						  $consodata          = array("vch_bill_ref_no"=>$bill_no_counter);
						  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
						}	
				if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,$vch_bill_ref_no);
				  $gstrinwsup_insert_data = array("inwsup_rev_chg"=>$reverse_charges,"inwsup_bill_ref_no"=>$bill_no_format,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$invoice_type,"inwsup_eco"=>$outsup_eco);
				  $this->TransactionModel->add_gstrinwsup_data($gstrinwsup_insert_data);
				}
				else{
				$gstrinwsup_insert_data = array("inwsup_rev_chg"=>$reverse_charges,"inwsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$invoice_type,"inwsup_eco"=>$outsup_eco);
				$this->TransactionModel->add_gstrinwsup_data($gstrinwsup_insert_data);
				}
								
				if($transport_distance!='' && $vehicle_no!=''){
					$transport_doc_date = date('Y-m-d',strtotime($transport_doc_date));
				// save into ewbpartbdt table
				$ewbpartbdt_data = array("gsttpt_id"=>$transporter,"trans_veh_no"=>$vehicle_no,
							 "trans_veh_type"=>$vehicle_type,"trans_mode"=>$transport_mode,
							 "trans_doc_no"=>$transporter_doc_no,"trans_doc_date"=>$transport_doc_date,
							 "trans_dist"=>$transport_distance,"trans_frm_state_code"=>$pos,"voucher_txn_id"=>$voucher_txn_id);	
				$this->TransactionModel->add_ewbpartbdt($ewbpartbdt_data);			 
				}
				// GSTIN TYPE Composition 
			if($this->session->get('bo_gstin_type')=="2"){
				$voucher_type_id = 22;
				$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);
				$insert_data     = array(
					"comp_id"			 => $this->company_id,
					"comp_vch_series_id" => $voucher_series,
					"comp_vch_no"		 => $voucher_no,
					"voucher_type_id"	 => $voucher_type_id,
					"voucher_date"		 => $voucher_date,
					"mat_cent_id"		 => $matrcntr_id,
					"vch_subtype_id"	 => $purchase_type,
					"voucher_tag"		 => $voucher_tag,
					"currency_id"		 => $currency_id,
				   );
				
				$vouchertxn_id           = $this->TransactionModel->add_voucher_cons_data($insert_data);	
				$insert_data = [
					"comp_id"             => $this->company_id,
					"comp_vch_series_id"  => $voucher_series,
					"voucher_txn_id"      => $vouchertxn_id,
					"master_id"           => $gstpaidacc_id,
					'master_id_type'      => 'acc'
				    ];
				$txn_id      = $this->TransactionModel->add_comp_txn_data($insert_data);	
				
				$gstpaid_acc_insert_data  = [
							'comp_id'            => $this->company_id,
							'acc_id'             => $gstpaidacc_id,
							'acc_txn_date'       => $voucher_date,
							'acc_txn_amount'     => parseAmount($total_tax_amount),						
							'acc_txn_drcr'       => 'd',
							'comp_vch_series_no' => $voucher_no,
							'posted_on'          => date('Y-m-d H:i:s'),						
							'voucher_txn_id'     => $vouchertxn_id,
							'voucher_type_id'    => $voucher_type_id,
							'txn_id'             => $txn_id,
							'acc_bal'            => 0
				           ];					   
				$this->TransactionModel->add_acc_txn_data($gstpaid_acc_insert_data);
				$this->TransactionModel->update_account_balance($gstpaidacc_id);
				
				$insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $txn_id,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> $txn_id,
					'voucher_txn_id'		=> $voucher_txn_id,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'SAGSTPD',
					'acc_cross_ref_data'	=> $vouchertxn_id,
					'acc_cross_logdate'   	=> date('Y-m-d'),
				    ];
					
				$this->TransactionModel->add_acc_crsref_data($insert_data); 
				if(count($bsd_comp_applytax_json) && $vouchertxn_id)
		           {
					foreach($bsd_comp_applytax_json as $value)
					   {
						   
						if(isset($value['billsundry_id'])){   
						$txn_data = array(
							"comp_id"				=> $this->company_id,
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_txn_id" 		=> $vouchertxn_id,
							"master_id" 			=> $value['billsundry_id'],
							'master_id_type' 		=> 'bsd'
						);
						$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);

						if($value['billsundry_amount'] >= 0)
							$billsundry_drcr = 'c';
						else
							$billsundry_drcr = 'd';

						 $insert_data  = array(
							"comp_id"				=> $this->company_id,
							"sundry_txn_date"		=> $voucher_date,
							"sundry_txn_amount"		=> abs($value['billsundry_amount']),
							"sundry_txn_drcr"		=> $billsundry_drcr,
							"comp_vch_name"			=> $voucher_no, // ?
							"comp_vch_series_no"	=> $voucher_no,
							"bill_sundry_id"		=> $value['billsundry_id'],
							"sundry_bal"			=> 0,
							"voucher_txn_id"		=> $vouchertxn_id, 
							"voucher_type_id"		=> $voucher_type_id,
							'txn_id'				=> $txn_id,
							'sundry_tag_rate'		=> 0
							);
						  
						 $this->TransactionModel->add_sundry_txn_data($insert_data);	
				   } }
		            } 
				
					
				}
				// END OF GSTIN TYPE Composition		
				 // Add company txn master entry for party
				 $txn_data = array(
						"comp_id"							=> $this->company_id,
						"comp_vch_series_id"	=> $voucher_series,
						"voucher_txn_id" 			=> $voucher_txn_id,
						"master_id" 					=> $party_id,
						'master_id_type' 			=> 'aco'
				 	);
         $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 
		 /****************   Forex Rates START    ****************/
			 if($currency_id >1 && $fcy_forex_rate!=''){
			 	 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $txn_id,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> $txn_id,
					'voucher_txn_id'		=> $voucher_txn_id,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'FOREXRT',
					'acc_cross_ref_data'	=> $fcy_forex_rate,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				 $this->TransactionModel->add_acc_crsref_data($insert_data);				 
			 }		
			/****************   Forex Rates END     ****************/
				  
				 /********** Start Tax Summary Code   **********/
				  if($taxsummarydata){ foreach($taxsummarydata as $rdata){
				   $item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($rdata['tax_item_id_val'],$rdata['tax_cat_id']);		
					$acc_cess_fcy = $rdata['cess_fcy_val'];
				   if($rdata['is_item']=='1'){
					$insert_data = [
						"comp_id"             => $this->company_id,
						"comp_vch_series_id"  => $voucher_series,
						"voucher_txn_id"      => $voucher_txn_id,
						"master_id"           => $rdata['tax_item_id_val'],
						'master_id_type'      => 'tax'
					 ];
					 if($rdata['cess_basis']=="1"){ // Percentage
						$acc_nonadv_cess_rate = $rdata['cess_rate_val'];
						$acc_nonadv_cess      = $rdata['cess_val'];
						$acc_cess_rate        = $rdata['cess_rate_val'];
						$acc_cess             =	$rdata['cess_val'];
					 }
					 else{ // MRP
						$acc_nonadv_cess_rate =$rdata['cess_rate_val'];
						$acc_nonadv_cess      =$rdata['cess_val'];
						$acc_cess_rate        =$rdata['cess_rate_val'];
						$acc_cess             =$rdata['cess_val'];
						
					 }
					 
					$oth_txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);
					if($bo_state_code!=$pos){
						$acc_igst_rate = parseAmount($rdata['tax_rate_val']);
						$acc_igst      = parseAmount($rdata['igst_val']);
						$acc_igst_fcy  = parseAmount($rdata['igst_fcy_val']);
						$acc_cgst_rate = $acc_cgst=$acc_sgst_rate=$acc_sgst=$acc_cgst_fcy=$acc_sgst_fcy=$acc_cess_fcy=0;
					 }
					 else{
						$acc_igst_rate = $acc_igst=$acc_igst_fcy=0;
						$acc_cgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_cgst      = parseAmount($rdata['cgst_val']);
						$acc_cgst_fcy  = parseAmount($rdata['cgst_fcy_val']);
						$acc_sgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_sgst      = parseAmount($rdata['sgst_val']);
						$acc_sgst_fcy  = parseAmount($rdata['sgst_fcy_val']);
					   }		
					$oth_insert_data   = array(
									'acc_id'               => $rdata['tax_item_id_val'],
									'acc_igst_rate'        => $acc_igst_rate,
									'acc_igst'             => $acc_igst,
									'acc_cgst_rate'        => $acc_cgst_rate,
									'acc_cgst'             => $acc_cgst,
									'acc_sgst_rate'        => $acc_sgst_rate,
									'acc_sgst'             => $acc_sgst,
									'acc_cess_rate'        => $acc_cess_rate,
									'acc_cess'             => $acc_cess,
									'acc_nonadv_cess_rate' => $acc_nonadv_cess_rate,
									'acc_nonadv_cess'      => $acc_nonadv_cess,
									'acc_hsn_sac'          => $rdata['tax_hsn_sac_val'],
									'vch_txn_id'           => $voucher_txn_id,
									'txn_id'               => $oth_txn_id,
									'acc_type'             => 'itm',
									'taxable_amt'          => $rdata['tax_amt_val'],
									'total_tax'            => $rdata['total_tax_val'],
									'acc_cess_basis'       => $rdata['cess_basis'],
									'cmp_tax_short_code'   => $item_tax_short_code,
									'acc_txn_date'         =>date('Y-m-d',strtotime($voucher_date))
									);
					      $this->TransactionModel->add_taxsummary_data($oth_insert_data);
						  if($currency_id >1 && $fcy_forex_rate!=''){
							 $taxable_val_fcy =  ($rdata['tax_amt_val']*$fcy_forex_rate);
							 $total_tax_fcy   =  $rdata['total_tax_fcy_val'];
							 $acctgstfcy_insert_data = array("acc_id"=>$rdata['tax_item_id_val'],"vch_txn_id"=>$voucher_txn_id,
														  "txn_id"=>$oth_txn_id,"acc_igst_fcy"=>$acc_igst_fcy,
														  "acc_cgst_fcy"=>$acc_cgst_fcy,"acc_sgst_fcy"=>$acc_sgst_fcy,
														  "acc_cess_fcy"=>$acc_cess_fcy,"total_tax_fcy"=>$total_tax_fcy,
														  "acc_type"=>"itm","acc_taxable_val_fcy"=>$taxable_val_fcy													 
														  );
							$this->TransactionModel->add_acctgstfcy_data($acctgstfcy_insert_data);
						  }
				     }
				   if($rdata['is_bsd']=='1'){
					$insert_data = [
						"comp_id"             => $this->company_id,
						"comp_vch_series_id"  => $voucher_series,
						"voucher_txn_id"      => $voucher_txn_id,
						"master_id"           => $rdata['tax_item_id_val'],
						'master_id_type'      => 'tax'
					 ];
					$bsd_txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);				
					if($bo_state_code!=$pos){
						$acc_igst_rate = parseAmount($rdata['tax_rate_val']);
						$acc_igst      = parseAmount($rdata['igst_val']);
						$acc_igst_fcy  = parseAmount($rdata['igst_fcy_val']);
						$acc_cgst_rate = $acc_cgst=$acc_sgst_rate=$acc_sgst=$acc_cgst_fcy=$acc_sgst_fcy=$acc_cess_fcy=0;
					 }
					 else{
						$acc_igst_rate = $acc_igst=$acc_igst_fcy=0;
						$acc_cgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_cgst      = parseAmount($rdata['cgst_val']);
						$acc_cgst_fcy  = parseAmount($rdata['cgst_fcy_val']);
						$acc_sgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_sgst      = parseAmount($rdata['sgst_val']);
						$acc_sgst_fcy  = parseAmount($rdata['sgst_fcy_val']);
					   }
					$bsd_insert_data   = array(
									'acc_id'               => $rdata['tax_item_id_val'],
									'acc_igst_rate'        => $acc_igst_rate,
									'acc_igst'             => $acc_igst,
									'acc_cgst_rate'        => $acc_cgst_rate,
									'acc_cgst'             => $acc_cgst,
									'acc_sgst_rate'        => $acc_sgst_rate,
									'acc_sgst'             => $acc_sgst,
									'acc_cess_rate'        => 0,
									'acc_nonadv_cess_rate' => 0,
									'acc_nonadv_cess'      => 0,
									'acc_hsn_sac'          => $rdata['tax_hsn_sac_val'],
									'vch_txn_id'           => $voucher_txn_id,
									'txn_id'               => $bsd_txn_id,
									'acc_type'             => 'bsd',
									'taxable_amt'          => $rdata['tax_amt_val'],
									'total_tax'            => $rdata['total_tax_val'],
									'cmp_tax_short_code'   => $item_tax_short_code,
									'acc_txn_date'         =>date('Y-m-d',strtotime($voucher_date))
									);
					      $this->TransactionModel->add_taxsummary_data($bsd_insert_data);
						  if($currency_id >1 && $fcy_forex_rate!=''){
							  $total_tax_fcy =  $rdata['total_tax_fcy_val'];
					          $acctgstfcy_insert_data = array("acc_id"=>$rdata['tax_item_id_val'],"vch_txn_id"=>$voucher_txn_id,
													  "txn_id"=>$bsd_txn_id,"acc_igst_fcy"=>$acc_igst_fcy,
													  "acc_cgst_fcy"=>$acc_cgst_fcy,"acc_sgst_fcy"=>$acc_sgst_fcy,
													  "acc_cess_fcy"=>$acc_cess_fcy,"total_tax_fcy"=>$total_tax_fcy,
													  "acc_type"=>"bsd"
													  );
					         $this->TransactionModel->add_acctgstfcy_data($acctgstfcy_insert_data);
					        }
				       }
				
				
				    }
				}
				
				/********** End Tax Summary Code   **********/	
				 // Add party oth txn entry 
		     $insert_data  = [
									'comp_id'            	 => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
									'acc_oth_txn_fcy'        => ($currency_id>1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,						
									'acc_oth_txn_drcr'       => 'c',
									'voucher_type_id'    	 => $voucher_type_id,
									'comp_vch_series_no' 	 => $voucher_series,
									'acc_id'             	 => $party_id,						
									'voucher_txn_id'     	 => $voucher_txn_id,
									'acc_oth_txn_status' 	 => $voucher_no,
									'bo_id'					 => $this->session->get('ses_boid'),
									'txn_id'				 => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		 => $oth_txn_tag,
							];
					$this->TransactionModel->add_acc_oth_data($insert_data);
/**********  Memo Account Entry  ************/
				if($sale_memo_total >0){
				$insert_data  = [
							'comp_id'            => $this->company_id,
							'acc_id'             => $party_id,
							'acc_txn_date'       => $voucher_date,
							'acc_txn_amount'     => ($sale_memo_total+$billsundry_memo_total),						
							'acc_oth_txn_fcy'    => ($currency_id>1)?($sale_memo_total+$billsundry_memo_total)*$fcy_forex_rate:0,						
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
				}
				/**********  Memo Account Entry  ************/
				$item_account_array = [];
		    foreach($itmsdata as $item_row)
		    {
				/**** Save MEMO TXN *****/
							 if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){  
							 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$item_row['memo_amount'],"acc_txn_drcr"=>"d","comp_vch_series_no"=>$voucher_series,"acc_id"=>$item_row['item_id'],"acc_type"=>"itm","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
							 $this->TransactionModel->save_sale_memeo_txns($memo_data);
							 }

						$txn_data = array(
							"comp_id"				=> $this->company_id,
							"comp_vch_series_id"	=> $voucher_series,
							"voucher_txn_id" 		=> $voucher_txn_id,
							"master_id" 			=> $item_row['item_id'],
							'master_id_type' 		=> 'ito'
						);
						$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
						if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
									    $insert_data   = array(
											'txn_id'              => $txn_id,
											'voucher_txn_id'      => $voucher_txn_id,
											'vch_txn_drcr'        => 'd',
											'vch_txn_incl'        => $item_row['txinc_amount'],
											'txn_type'            => 'itm'
											);	
									    $this->TransactionModel->add_taxinclusive_txn_data($insert_data);
									}
						$insert_data   = array(
							'comp_id'              => $this->company_id,
							'item_oth_txn_date'    => $voucher_date,
							'item_oth_txn_amount'  => $item_row['item_total_amount'],
							'item_oth_txn_fcy'     => ($currency_id>1)?$item_row['item_total_amount']*$fcy_forex_rate:0,
							'item_oth_txn_drcr'    => 'd',
							'item_oth_txn_qty'     => $item_row['item_qty'],
							'description'          => $item_row['description'],
							'comp_vch_series_no'   => $voucher_no,
							'item_id'              => $item_row['item_id'],
							'voucher_txn_id'       => $voucher_txn_id,
							'voucher_type_id'      => $voucher_type_id,
							'mat_cent_id'          => $matrcntr_id,
							'item_oth_txn_tag'     => $oth_txn_tag,
							'bo_id'				   => $this->session->get('ses_boid'),
							'txn_id'			   => $txn_id,
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
						'acc_oth_txn_fcy'      => ($currency_id>1)?$amount*$fcy_forex_rate:0,						
						'acc_oth_txn_drcr'     => 'd',
						'voucher_type_id'      => $voucher_type_id,
						'comp_vch_series_no'   => $voucher_series,
						'acc_id'               => $account_id,						
						'voucher_txn_id'       => $voucher_txn_id,
						'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
						'bo_id'				   => $this->session->get('ses_boid'),
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
			                        'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
															"bo_id"							=> $this->session->get('ses_boid'),
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
									'comp_id'            	 => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => abs($value['billsundry_amount']),
									'acc_oth_txn_fcy'        => ($currency_id>1)?abs($value['billsundry_amount']*$fcy_forex_rate):0,						
									'acc_oth_txn_drcr'       => $billsundry_drcr,
									'voucher_type_id'        => $voucher_type_id,
									'comp_vch_series_no'     => $voucher_series,
									'acc_id'                 => $value['billsundry_id'],						
									'voucher_txn_id'         => $voucher_txn_id,
									'acc_oth_txn_status'     => $value['billsundry_rate'],
									'bo_id'				     => $this->session->get('ses_boid'),
									'txn_id'			     => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'	     => 'BILSDRY',
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
		$data['voucher_bill_no']                    = $this->TransactionModel->get_billno_format($voucher_type_id,21,$voucher_detail['last_entry'],1,'counter');
		$data['folder_path']      = $this->folder_path;	
		$data['items_list']       = $this->TransactionModel->items_list();	
		$data['units_list']       = $this->TransactionModel->units_dropdown();
		$data['voucher_series_dropdown']  = $this->TransactionModel->comp_voucher_series($voucher_type_id);
		$data['party_dropdown']  = $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
		$data['bills_method_list']				= ['','New Ref.','Adjustment'];
		$data['voucher_date']                    = $voucher_detail['last_entry'];
		$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
		$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
        $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
        $data['voucher_type_id']                = $voucher_type_id;
        $data['currency_list'] = $this->TransactionModel->get_currency_list();
        $data['voucher_type_id']   = $voucher_type_id;
		$data['bo_state_code']     = $bo_state_code;
		$data['states_lists']      = $this->TransactionModel->show_states_lists();
		$data['transport_modes']                = transport_modes();
		$data['vehicle_types']                  = vehicle_types();
		$data['bo_gstin_type']                 = $this->session->get('bo_gstin_type');
		$data['transporter_dropdown']           = $this->TransactionModel->transporter_dropdown();
		$data['eco_dropdown']                   = $this->TransactionModel->show_eco_lists(); 
		
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
      	 $voucher_date         = $this->request->getVar('sale_date');
         $voucher_date         = validate_date_by_fy($voucher_date);

      	 $party_id         	= $this->request->getVar('party_id');                  
		 $voucher_series    = $this->request->getVar('voucher_series'); 
		 $matrcntr_id       = $this->request->getVar('matrcntr_id'); 
		 $narration         = $this->request->getVar('narration');  
		 $itmsdata          = json_decode($this->request->getVar('itmsdata'),true);
		 $currency_id 		= $this->request->getVar('currency_id'); 
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
									'comp_id'            	 => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
									'acc_oth_txn_fcy'        => ($currency_id > 1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,						
									'acc_oth_txn_drcr'       => 'c',
									'voucher_type_id'    	 => $voucher_type_id,
									'comp_vch_series_no' 	 => $voucher_series,
									'acc_id'             	 => $party_id,						
									'voucher_txn_id'     	 => $voucher_txn_id,
									'acc_oth_txn_status' 	 => $voucher_no,
									'bo_id'					 => $this->session->get('ses_boid'),
									'txn_id'				 => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		 => $oth_txn_tag,
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
							'item_oth_txn_amount'  => 0,
							'item_oth_txn_fcy'     => 0,
							'item_oth_txn_drcr'    => 'd',
							'item_oth_txn_qty'     => $item_row['item_qty'],
							'description'          => $item_row['description'],
							'comp_vch_series_no'   => $voucher_no,
							'item_id'              => $item_row['item_id'],
							'voucher_txn_id'       => $voucher_txn_id,
							'voucher_type_id'      => $voucher_type_id,
							'mat_cent_id'          => $matrcntr_id,
							'item_oth_txn_tag'     => $oth_txn_tag,
							'bo_id'				   => $this->session->get('ses_boid'),
							'txn_id'			   => $txn_id,
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
						'acc_oth_txn_fcy'      => ($currency_id > 1)?$amount*$fcy_forex_rate:0,						
						'acc_oth_txn_drcr'     => 'd',
						'voucher_type_id'      => $voucher_type_id,
						'comp_vch_series_no'   => $voucher_series,
						'acc_id'               => $account_id,						
						'voucher_txn_id'       => $voucher_txn_id,
						'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
						'bo_id'				   => $this->session->get('ses_boid'),
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
			                        'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
															"bo_id"							=> $this->session->get('ses_boid'),
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
									'comp_id'            	 => $this->company_id,
									'acc_oth_txn_date'       => $voucher_date,
									'acc_oth_txn_amount'     => abs($value['billsundry_amount']),
									'acc_oth_txn_fcy'        => ($currency_id >1)?abs($value['billsundry_amount'])*$fcy_forex_rate:0,						
									'acc_oth_txn_drcr'       => $billsundry_drcr,
									'voucher_type_id'    	 => $voucher_type_id,
									'comp_vch_series_no' 	 => $voucher_series,
									'acc_id'             	 => $value['billsundry_id'],						
									'voucher_txn_id'     	 => $voucher_txn_id,
									'acc_oth_txn_status' 	 => $value['billsundry_rate'],
									'bo_id'					 => $this->session->get('ses_boid'),
									'txn_id'				 => $txn_id,
									'acc_oth_txn_duedate' 	 => date('Y-m-d'),
									'acc_oth_txn_tag'		 => 'BILSDRY',
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
		$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
        $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
        $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
        $data['voucher_type_id']                = $voucher_type_id;
        $data['currency_list'] = $this->TransactionModel->get_currency_list();

		return view($this->folder_path.'purchase_requisition/without_amount',$data);		
	}
     
  	public function edit($voucher_txn_id)
		{
				$voucher_type_id    = "21"; // change it
				$oth_txn_tag 				= 'PURREQN';

				$bo_state_code    = $this->session->get('ses_bostecd');
			    $bo_id = $this->session->get('ses_boid');
				$istaxinc         =  $this->TransactionModel->check_sale_taxinc($voucher_txn_id);
			    $ismemosale       =  $this->TransactionModel->check_sale_memoentry($voucher_txn_id);
				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				
				
				$voucher_detail    = $this->TransactionModel->get_voucher_info($voucher_type_id);	  
			    $isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$voucher_info['comp_vch_series_id']);
				$vch_subtype_id = $voucher_info['vch_subtype_id'];
			
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
		        	 $voucher_date      = $this->request->getVar('sale_date');
					 $invoice_type      = $this->request->getVar('invoice_type');
        	 		 $voucher_date      = validate_date_by_fy($voucher_date);
					 $billno 		    = $this->request->getVar('billno');
					 $reverse_charges   = $this->request->getVar('reverse_charges');
					 $pos               = $this->request->getVar('pos');
		        	 $party_id          = $this->request->getVar('party_id');                  
					 $voucher_series    = $this->request->getVar('voucher_series');
					 $matrcntr_id       = $this->request->getVar('matrcntr_id'); 
					 $narration         = $this->request->getVar('narration');  
					 $itmsdata          = json_decode($this->request->getVar('itmsdata'),true);
					 $currency_id 			= $this->request->getVar('currency_id'); 

					 $voucher_no        = $this->TransactionModel->get_voucher_no($voucher_type_id);
					 $type 							= $this->request->getVar('type');
				$ewb_id              = $this->request->getVar('ewb_id');
			  $gstpaidacc_id       = $this->request->getVar('gst_paidacc_id');
			  $fcy_forex_rate      = $this->request->getVar('fcy_forex_rate');
			  $memoCheck           = $this->request->getVar('memoCheck');
			  $taxInclusive        = $this->request->getVar('taxInclusive');
			  $eco_id              = $this->request->getVar('eco_id');
			if($eco_id!='')
				$outsup_eco='1';
			else
				$outsup_eco='0';
			  $isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			 if($billno=="" || $billno=="0")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']]);
			$billno_duplicate = $this->TransactionModel->PurchaseBillNoDuplicate($billno,$voucher_txn_id);
			if($billno_duplicate=="1")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']]);
			 }			
			 $taxsummarydata = [];
			if(!empty($this->request->getVar('taxsummarydata')))
				$taxsummarydata    = json_decode($this->request->getVar('taxsummarydata'),true); 
		
		      $billsndrydata = [];
				  if(!empty($this->request->getVar('billsndrydata')))
				 		$billsndrydata = json_decode($this->request->getVar('billsndrydata'),true);
		 $total_tax_amount=0;
			$sale_memo_total  = array_sum(array_column($itmsdata, 'memo_amount'));
			
			$billsundry_memo_total = 0;
			if(count($billsndrydata))
				$billsundry_memo_total = array_sum(array_column($billsndrydata, 'memo_amount'));	
				
			$txinc_amount     = array_sum(array_column($itmsdata, 'txinc_amount'));
			
			if($this->session->get('bo_gstin_type')=="2"){
				
				
			if($bsd_comp_applytax_json){
				foreach($bsd_comp_applytax_json as $ttax){
					$total_tax_amount =$total_tax_amount+$ttax['billsundry_amount'];
				}
			}
			  }
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
									 $this->TransactionModel->delete_taxinc_txn_data($voucher_txn_id,'acc');
									 $this->TransactionModel->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
									 $this->TransactionModel->delete_memo_txn_data($voucher_txn_id);
								
									 // delete comp txn master
									 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
									 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_all_narrations($voucher_txn_id);
									 $this->TransactionModel->delete_acctgstsum_data($voucher_txn_id);	
							
									 // Add company txn master entry for party
									 $txn_data = array(
											"comp_id"							=> $this->company_id,
											"comp_vch_series_id"	=> $voucher_series,
											"voucher_txn_id" 			=> $voucher_txn_id,
											"master_id" 					=> $party_id,
											'master_id_type' 			=> 'aco'
									 	);
					         $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 
				    if($isvoucher_autobillno==1){ 
					 $gstrinwsup_update_data = array("inwsup_rev_chg"=>$reverse_charges,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$invoice_type,"inwsup_eco"=>$outsup_eco);
				     $this->TransactionModel->update_gstrinwsup_data($voucher_txn_id,$gstrinwsup_update_data);
					 }
					 else{
					$gstrinwsup_update_data = array("inwsup_rev_chg"=>$reverse_charges,"inwsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"inwsup_pos"=>$pos,"inwsup_inv_type"=>$invoice_type,"inwsup_eco"=>$outsup_eco);
					$this->TransactionModel->update_gstrinwsup_data($voucher_txn_id,$gstrinwsup_update_data);
					 }
					  /****************   Forex Rates START    ****************/
			     if($currency_id >1 && $fcy_forex_rate!=''){
			 	 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $txn_id,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> $txn_id,
					'voucher_txn_id'		=> $voucher_txn_id,
					'bo_id'					=> $this->session->get('ses_boid'),
					'acc_cross_ref_type'	=> 'FOREXRT',
					'acc_cross_ref_data'	=> $fcy_forex_rate,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				 $this->TransactionModel->add_acc_crsref_data($insert_data);				 
			 }		
			/****************   Forex Rates END     ****************/          
				      /********** Start Tax Summary Code   **********/
				  if($taxsummarydata){ foreach($taxsummarydata as $rdata){
			     	$item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($rdata['tax_item_id_val'],$rdata['tax_cat_id']);		
					$acc_cess_fcy = $rdata['cess_fcy_val'];
				   if($rdata['is_item']=='1'){
					$insert_data = [
						"comp_id"             => $this->company_id,
						"comp_vch_series_id"  => $voucher_series,
						"voucher_txn_id"      => $voucher_txn_id,
						"master_id"           => $rdata['tax_item_id_val'],
						'master_id_type'      => 'tax'
					 ];
					$oth_txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);				
					if($bo_state_code!=$pos){
						$acc_igst_rate = parseAmount($rdata['tax_rate_val']);
						$acc_igst      = parseAmount($rdata['igst_val']);
						$acc_igst_fcy  = parseAmount($rdata['igst_fcy_val']);
						$acc_cgst_rate = $acc_cgst=$acc_sgst_rate=$acc_sgst=$acc_cgst_fcy=$acc_sgst_fcy=$acc_cess_fcy=0;
					 }
					 else{
						$acc_igst_rate = $acc_igst=$acc_igst_fcy=0;
						$acc_cgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_cgst      = parseAmount($rdata['cgst_val']);
						$acc_cgst_fcy  = parseAmount($rdata['cgst_fcy_val']);
						$acc_sgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_sgst      = parseAmount($rdata['sgst_val']);
						$acc_sgst_fcy  = parseAmount($rdata['sgst_fcy_val']);
					   }
					$oth_insert_data   = array(
									'acc_id'               => $rdata['tax_item_id_val'],
									'acc_igst_rate'        => $acc_igst_rate,
									'acc_igst'             => $acc_igst,
									'acc_cgst_rate'        => $acc_cgst_rate,
									'acc_cgst'             => $acc_cgst,
									'acc_sgst_rate'        => $acc_sgst_rate,
									'acc_sgst'             => $acc_sgst,
									'acc_cess_rate'        => 0,
									'acc_nonadv_cess_rate' => 0,
									'acc_nonadv_cess'      => 0,
									'acc_hsn_sac'          => $rdata['tax_hsn_sac_val'],
									'vch_txn_id'           => $voucher_txn_id,
									'txn_id'               => $oth_txn_id,
									'acc_type'             => 'itm',
									'taxable_amt'          => $rdata['tax_amt_val'],
									'total_tax'            => $rdata['total_tax_val'],
									'cmp_tax_short_code'   => $item_tax_short_code,
									'acc_txn_date'         =>date('Y-m-d',strtotime($voucher_date))
									);
					      $this->TransactionModel->add_taxsummary_data($oth_insert_data);
						  if($currency_id >1 && $fcy_forex_rate!=''){
							 $taxable_val_fcy =  ($rdata['tax_amt_val']*$fcy_forex_rate);
							 $total_tax_fcy   =  $rdata['total_tax_fcy_val'];
							 $acctgstfcy_insert_data = array("acc_id"=>$rdata['tax_item_id_val'],"vch_txn_id"=>$voucher_txn_id,
														  "txn_id"=>$oth_txn_id,"acc_igst_fcy"=>$acc_igst_fcy,
														  "acc_cgst_fcy"=>$acc_cgst_fcy,"acc_sgst_fcy"=>$acc_sgst_fcy,
														  "acc_cess_fcy"=>$acc_cess_fcy,"total_tax_fcy"=>$total_tax_fcy,
														  "acc_type"=>"itm","acc_taxable_val_fcy"=>$taxable_val_fcy													 
														  );
							$this->TransactionModel->add_acctgstfcy_data($acctgstfcy_insert_data);
						  }
				     }
				   if($rdata['is_bsd']=='1'){
					$insert_data = [
						"comp_id"             => $this->company_id,
						"comp_vch_series_id"  => $voucher_series,
						"voucher_txn_id"      => $voucher_txn_id,
						"master_id"           => $rdata['tax_item_id_val'],
						'master_id_type'      => 'tax'
					 ];
					$bsd_txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);				
					if($bo_state_code!=$pos){
						$acc_igst_rate = parseAmount($rdata['tax_rate_val']);
						$acc_igst      = parseAmount($rdata['igst_val']);
						$acc_igst_fcy  = parseAmount($rdata['igst_fcy_val']);
						$acc_cgst_rate = $acc_cgst=$acc_sgst_rate=$acc_sgst=$acc_cgst_fcy=$acc_sgst_fcy=$acc_cess_fcy=0;
					 }
					 else{
						$acc_igst_rate = $acc_igst=$acc_igst_fcy=0;
						$acc_cgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_cgst      = parseAmount($rdata['cgst_val']);
						$acc_cgst_fcy  = parseAmount($rdata['cgst_fcy_val']);
						$acc_sgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_sgst      = parseAmount($rdata['sgst_val']);
						$acc_sgst_fcy  = parseAmount($rdata['sgst_fcy_val']);
					   }
					$bsd_insert_data   = array(
									'acc_id'               => $rdata['tax_item_id_val'],
									'acc_igst_rate'        => $acc_igst_rate,
									'acc_igst'             => $acc_igst,
									'acc_cgst_rate'        => $acc_cgst_rate,
									'acc_cgst'             => $acc_cgst,
									'acc_sgst_rate'        => $acc_sgst_rate,
									'acc_sgst'             => $acc_sgst,
									'acc_cess_rate'        => 0,
									'acc_nonadv_cess_rate' => 0,
									'acc_nonadv_cess'      => 0,
									'acc_hsn_sac'          => $rdata['tax_hsn_sac_val'],
									'vch_txn_id'           => $voucher_txn_id,
									'txn_id'               => $bsd_txn_id,
									'acc_type'             => 'bsd',
									'taxable_amt'          => $rdata['tax_amt_val'],
									'total_tax'            => $rdata['total_tax_val'],
									'cmp_tax_short_code'   => $item_tax_short_code,
									'acc_txn_date'         =>date('Y-m-d',strtotime($voucher_date))
									);
					      $this->TransactionModel->add_taxsummary_data($bsd_insert_data);
						  if($currency_id >1 && $fcy_forex_rate!=''){
							  $total_tax_fcy =  $rdata['total_tax_fcy_val'];
					          $acctgstfcy_insert_data = array("acc_id"=>$rdata['tax_item_id_val'],"vch_txn_id"=>$voucher_txn_id,
													  "txn_id"=>$bsd_txn_id,"acc_igst_fcy"=>$acc_igst_fcy,
													  "acc_cgst_fcy"=>$acc_cgst_fcy,"acc_sgst_fcy"=>$acc_sgst_fcy,
													  "acc_cess_fcy"=>$acc_cess_fcy,"total_tax_fcy"=>$total_tax_fcy,
													  "acc_type"=>"bsd"
													 
													  );
					         $this->TransactionModel->add_acctgstfcy_data($acctgstfcy_insert_data);
					       }
				       }
				
				
				    }
				}
				/********** End Tax Summary Code   **********/ 
									 // Add party oth txn entry 
							     $insert_data  = [
														'comp_id'            	 => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
														'acc_oth_txn_fcy'        => ($currency_id >1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,					
														'acc_oth_txn_drcr'       => 'c',
														'voucher_type_id'    	 => $voucher_type_id,
														'comp_vch_series_no' 	 => $voucher_series,
														'acc_id'             	 => $party_id,						
														'voucher_txn_id'     	 => $voucher_txn_id,
														'acc_oth_txn_status' 	 => $voucher_no,
														'bo_id'					 => $this->session->get('ses_boid'),
														'txn_id'				 => $txn_id,
														'acc_oth_txn_duedate' 	 => date('Y-m-d'),
														'acc_oth_txn_tag'		 => $oth_txn_tag,
												];
										$this->TransactionModel->add_acc_oth_data($insert_data);
/**********  Memo Account Entry  ************/
				if($sale_memo_total >0){
				$insert_data  = [
							'comp_id'            => $this->company_id,
							'acc_id'             => $party_id,
							'acc_txn_date'       => $voucher_date,
							'acc_txn_amount'     => ($sale_memo_total + $billsundry_memo_total),						
							'acc_oth_txn_fcy'    => ($currency_id >1)?($sale_memo_total+$billsundry_memo_total)*$fcy_forex_rate:0,
							'acc_txn_drcr'       => 'c',
							'comp_vch_series_no' => $voucher_no,
							'posted_on'          => date('Y-m-d H:i:s'),						
							'voucher_txn_id'     => $voucher_txn_id,
							'voucher_type_id'    => 11,
							'txn_id'             => $txn_id,
							'acc_bal'            => 0
				           ];					   
				$this->TransactionModel->add_acc_txn_data($insert_data);
				$this->TransactionModel->update_account_balance($party_id);
				}
				/**********  Memo Account Entry  ************/ 
									$item_account_array = [];
							    foreach($itmsdata as $item_row)
							    {/**** Save MEMO TXN *****/
				if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){		
				 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$item_row['memo_amount'],"acc_txn_drcr"=>"d","comp_vch_series_no"=>$voucher_series,"acc_id"=>$item_row['account_id'],"acc_type"=>"acc","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
				 $this->TransactionModel->save_sale_memeo_txns($memo_data);
				}     

											$txn_data = array(
												"comp_id"							=> $this->company_id,
												"comp_vch_series_id"	=> $voucher_series,
												"voucher_txn_id" 			=> $voucher_txn_id,
												"master_id" 					=> $item_row['item_id'],
												'master_id_type' 			=> 'ito'
											);
											$txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
if($taxInclusive=="1" && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
				      $insert_data   = array(
					        'txn_id'              => $txn_id,
							'voucher_txn_id'      => $voucher_txn_id,
							'vch_txn_drcr'        => 'd',
							'vch_txn_incl'        => $item_row['txinc_amount'],
							'txn_type'            => 'acc'
					        );	
					$this->TransactionModel->add_taxinclusive_txn_data($insert_data);
				    }		
											$insert_data   = array(
												'comp_id'              => $this->company_id,
												'item_oth_txn_date'    => $voucher_date,
												'item_oth_txn_amount'  => $item_row['item_total_amount'],
												'item_oth_txn_fcy'     => ($currency_id > 1)?$item_row['item_total_amount']*$fcy_forex_rate:0,
												'item_oth_txn_drcr'    => 'd',
												'item_oth_txn_qty'     => $item_row['item_qty'],
												'description'          => $item_row['description'],
												'comp_vch_series_no'   => $voucher_no,
												'item_id'              => $item_row['item_id'],
												'voucher_txn_id'       => $voucher_txn_id,
												'voucher_type_id'      => $voucher_type_id,
												'mat_cent_id'          => $matrcntr_id,
												'item_oth_txn_tag'     => $oth_txn_tag,
												'bo_id'				   => $this->session->get('ses_boid'),
												'txn_id'			   => $txn_id,
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
											'acc_oth_txn_fcy'      => ($currency_id > 1)?$amount*$fcy_forex_rate:0,						
											'acc_oth_txn_drcr'     => 'd',
											'voucher_type_id'      => $voucher_type_id,
											'comp_vch_series_no'   => $voucher_series,
											'acc_id'               => $account_id,						
											'voucher_txn_id'       => $voucher_txn_id,
											'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
											'bo_id'				   => $this->session->get('ses_boid'),
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
			                        'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
/**** Start Save MEMO TXN *****/
								if(isset($value['memo_amount']) && $value['memo_amount'] >0){
								if($value['memo_amount'] >0){	 
									if($value['memo_amount'] >= 0)
											$billsundry_memo_drcr = 'd';
										else
											$billsundry_memo_drcr = 'c';	 
									 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$value['memo_amount'],"acc_txn_drcr"=>$billsundry_memo_drcr,"comp_vch_series_no"=>$voucher_series,"acc_id"=>$value['billsundry_id'],"acc_type"=>"bsd","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
									 $this->TransactionModel->save_sale_memeo_txns($memo_data);
									 $this->TransactionModel->update_account_memo_balance($value['billsundry_id'],'bsd');
									}
								}
										 /**** End Save MEMO TXN *****/	
										      if($value['billsundry_amount'] >= 0)
										      		$billsundry_drcr = 'd';
										      else
										      	  $billsundry_drcr = 'c';

							           $insert_data  = [
														'comp_id'            	 => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => abs($value['billsundry_amount']),
														'acc_oth_txn_fcy'        => ($currency_id > 1)?abs($value['billsundry_amount'])*$fcy_forex_rate:0,						
														'acc_oth_txn_drcr'       => $billsundry_drcr,
														'voucher_type_id'    	 => $voucher_type_id,
														'comp_vch_series_no' 	 => $voucher_series,
														'acc_id'             	 => $value['billsundry_id'],						
														'voucher_txn_id'     	 => $voucher_txn_id,
														'acc_oth_txn_status' 	 => $value['billsundry_rate'],
														'bo_id'					 => $this->session->get('ses_boid'),
														'txn_id'				 => $txn_id,
														'acc_oth_txn_duedate' 	 => date('Y-m-d'),
														'acc_oth_txn_tag'		 => 'BILSDRY'
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
									 		"comp_vch_series_id"  => $voucher_series,
										    "voucher_date"		  => $voucher_date,
										    "mat_cent_id"		  => $matrcntr_id,
										    "currency_id"		  => $currency_id,
										  );
									 $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);

									 // delete comp txn master
									 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
									 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_all_narrations($voucher_txn_id);

									// Add company txn master entry for party
								 $txn_data = array(
										"comp_id"			  => $this->company_id,
										"comp_vch_series_id"  => $voucher_series,
										"voucher_txn_id" 	  => $voucher_txn_id,
										"master_id" 		  => $party_id,
										'master_id_type' 	  => 'aco'
								 	);
								 $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 

								 // Add party oth txn entry 
						         $insert_data  = [
													'comp_id'            	 => $this->company_id,
													'acc_oth_txn_date'       => $voucher_date,
													'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
													'acc_oth_txn_amount'     => ($currency_id > 1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,						
													'acc_oth_txn_drcr'       => 'c',
													'voucher_type_id'    	 => $voucher_type_id,
													'comp_vch_series_no' 	 => $voucher_series,
													'acc_id'             	 => $party_id,						
													'voucher_txn_id'     	 => $voucher_txn_id,
													'acc_oth_txn_status' 	 => $voucher_no,
													'bo_id'					 => $this->session->get('ses_boid'),
													'txn_id'				 => $txn_id,
													'acc_oth_txn_duedate' 	 => date('Y-m-d'),
													'acc_oth_txn_tag'		 => $oth_txn_tag,
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
											'item_oth_txn_amount'  => 0,
											'item_oth_txn_fcy'     => 0,
											'item_oth_txn_drcr'    => 'd',
											'item_oth_txn_qty'     => $item_row['item_qty'],
											'description'          => $item_row['description'],
											'comp_vch_series_no'   => $voucher_no,
											'item_id'              => $item_row['item_id'],
											'voucher_txn_id'       => $voucher_txn_id,
											'voucher_type_id'      => $voucher_type_id,
											'mat_cent_id'          => $matrcntr_id,
											'item_oth_txn_tag'     => $oth_txn_tag,
											'bo_id'				   => $this->session->get('ses_boid'),
											'txn_id'			   => $txn_id,
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
										'acc_oth_txn_fcy'      => ($currency_id > 1)?$amount*$fcy_forex_rate:0,						
										'acc_oth_txn_drcr'     => 'd',
										'voucher_type_id'      => $voucher_type_id,
										'comp_vch_series_no'   => $voucher_series,
										'acc_id'               => $account_id,						
										'voucher_txn_id'       => $voucher_txn_id,
										'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
										'bo_id'				   => $this->session->get('ses_boid'),
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
			                        'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
			                            'batch_mfr'      => validate_date_by_fy($value['manufacturing_date']),
			                        		'batch_expiry'   => validate_date_by_fy($value['expiry_date']),
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
													'acc_oth_txn_date'         => $voucher_date,
													'acc_oth_txn_amount'       => abs($value['billsundry_amount']),
													'acc_oth_txn_fcy'          => ($currency_id > 1)?abs($value['billsundry_amount'])*$fcy_forex_rate:0,						
													'acc_oth_txn_drcr'         => $billsundry_drcr,
													'voucher_type_id'    	   => $voucher_type_id,
													'comp_vch_series_no' 	   => $voucher_series,
													'acc_id'             	   => $value['billsundry_id'],						
													'voucher_txn_id'     	   => $voucher_txn_id,
													'acc_oth_txn_status' 	   => $value['billsundry_rate'],
													'bo_id'					   => $this->session->get('ses_boid'),
													'txn_id'				   => $txn_id,
													'acc_oth_txn_duedate' 	   => date('Y-m-d'),
													'acc_oth_txn_tag'		   => 'BILSDRY',
											       ];
											$this->TransactionModel->add_acc_oth_data($insert_data);
										}
						    }

						    // return redirect()->to($this->base_url.'purchase_requisition/without_amount');
					     }
					     return json_encode(['status' => true, 'message' => 'Voucher Updated']);
			   	}
		   	}
				$allbos           =  $this->TransactionModel->all_mig_bo_lists();
			    $data['allbos']   = $allbos;
				$data['isvoucher_autobillno']       = $isvoucher_autobillno;
				$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
				$data['voucher_no'] 				= $voucher_info['comp_vch_no'];
				$data['sale_date'] 			    = date('d-m-Y', strtotime($voucher_info['voucher_date']));
				$data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];
				$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);	
				$data['gstrinwsup_info']      = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);
			   $data['transport_modes']                = transport_modes();
               $data['vehicle_types']                  = vehicle_types();
		       $data['transporter_dropdown']           = $this->TransactionModel->transporter_dropdown();
			   $data['bo_gstin_type']                 = $this->session->get('bo_gstin_type');
			   // Get Forex Currency Rate		
			   $data['forexcrncy_rate']            = $this->TransactionModel->get_crsref($voucher_txn_id,'FOREXRT');
				  
			   $data['ismemosale']                 = $ismemosale;
			   $data['istaxinc']                   = $istaxinc;
			   $data['voucher_type_id']            = $voucher_type_id;
			   $data['get_ewbmstreqn_data']        = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
			   $data['voucher_series_dropdown']    = $this->TransactionModel->voucher_series_dropdowns($voucher_type_id);
			   
			   $data['sundry_comp_transactions']='';
				if($this->session->get('bo_gstin_type')=="2"){				
			     $vouchertxn_id   = $this->TransactionModel->get_vchr_txnid($voucher_txn_id);
				 $data['sundry_comp_transactions'] = $this->TransactionModel->get_sundry_transactions($vouchertxn_id, true);
                }
				$party_transaction            = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
				$data['party_id'] 			  = $party_transaction['acc_id'];
				$data['party_amount'] 		  = $party_transaction['acc_oth_txn_amount'];
				$data['narration'] 			  = $party_transaction['acc_oth_txn_narr'];
				$data['get_narration_info']   = $get_narration_info; 
				$data['item_transactions']    = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
				$data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
				$data['sundry_transactions']  = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id);

				$billsundry_total = 0;
				if(count($data['sundry_transactions']))
						$billsundry_total = array_sum(array_column($data['sundry_transactions'], 'billsundry_amount'));
				
				$data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,$vch_subtype_id);		
				$data['bo_state_code'] = $bo_state_code;
				$data['eco_dropdown']                   = $this->TransactionModel->show_eco_lists(); 				
				$data['message_output']   				= $this->message_output;
				$data['base_url']         				= $this->base_url; 
				$data['voucher_auto_no']  				= $this->TransactionModel->get_voucher_no($voucher_type_id);
				$data['folder_path']      				= $this->folder_path;	
				$data['items_list']       				= $this->TransactionModel->items_list();	
				$data['units_list']       				= $this->TransactionModel->units_dropdown();
				$data['voucher_series_dropdown']        = $this->TransactionModel->comp_voucher_series($voucher_type_id);
				$data['party_dropdown']  				= $this->TransactionModel->party_dropdown();
				$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
				$data['voucher_txn_id']     = $voucher_txn_id;
				$data['voucher_type_id']                = $voucher_type_id;
				$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
                $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
				$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
                $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
				$data['bills_method_list']				= ['','New Ref.','Adjustment'];
				$data['currency_list'] = $this->TransactionModel->get_currency_list();
				$data['currency_id'] = $voucher_info['currency_id'];
				$data['states_lists']      = $this->TransactionModel->show_states_lists();

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