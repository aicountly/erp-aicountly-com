<?php
namespace App\Controllers\Admin;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Quotations  extends BaseController
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
		$this->SupplyTypes   =  SupplyTypesList();
		$this->TXNTypesList  =  TXNTypesList();
		$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
	}

	  public function item()
		{
			$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
			$voucher_type_id    = "17";
			$oth_txn_tag 				= 'QUOTENN';
			$bo_id             = $this->session->get('ses_boid');								 
		    $bo_state_code     = $this->session->get('ses_bostecd');
            $voucher_detail    = $this->TransactionModel->get_voucher_info($voucher_type_id);	
			if($this->request->getMethod() == 'post' && $this->request->isAjax())
			{	

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
			   'due_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Due Date is required'
				  ],
			  ],
			 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
        else
        {
        	 $invoice_type        = $this->request->getVar('invoice_type');				
			$voucher_date        = $this->request->getVar('voucher_date');
			$voucher_series      = $this->request->getVar('voucher_series'); 
			$party_id            = $this->request->getVar('party_id'); 
			$matrcntr_id         = $this->request->getVar('matrcntr_id'); 
			$narration           = $this->request->getVar('narration');  
			$supply_type         = $this->request->getVar('supply_type');
			$tax_type_id         = $this->request->getVar('tax_type_id');
			$ewb_sub_supply_desc = $this->request->getVar('supply_type_desc');
			$pos                 = $this->request->getVar('pos');
			$itmsdata            = json_decode($this->request->getVar('itmsdata'),true); 
			$voucher_date        = validate_date_by_fy($voucher_date);
			$delivery_challan_id = $this->request->getVar('delivery_challan_id');
			$currency_id         = $this->request->getVar('currency_id');
			$transporter         = $this->request->getVar('transporter');
			$transporter_name    = $this->request->getVar('transporter_name');			
			$transporter_doc_no  = $this->request->getVar('transporter_doc_no');
			$transport_mode      = $this->request->getVar('transport_mode');			
			$transport_distance  = $this->request->getVar('transport_distance');
			$transport_doc_date  = $this->request->getVar('transport_doc_date');
			$vehicle_no          = $this->request->getVar('vehicle_no');
			$vehicle_type        = $this->request->getVar('vehicle_type');			
			$billno              = $this->request->getVar('billno');
			$reverse_charges     = $this->request->getVar('reverse_charges');
			$gstpaidacc_id       = $this->request->getVar('gst_paidacc_id');
			$fcy_forex_rate      = $this->request->getVar('fcy_forex_rate');
			$memoCheck           = $this->request->getVar('memoCheck');			
			$taxInclusive        = $this->request->getVar('taxInclusive');
			$due_date              = $this->request->getVar('due_date');
			$outsup_eco              = $this->request->getVar('eco_id');
			if($outsup_eco=='')				
				$outsup_eco='0';
			
			$isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			 if($billno=="" || $billno=="0")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']]);
			$billno_duplicate = $this->TransactionModel->BillNoDuplicate($billno);
			if($billno_duplicate=="1")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']]);
			} 
					$batchdata = [];
				if(!empty($this->request->getVar('batchinfo_array')))
				 	$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);
			$bsd_comp_applytax_json = [];
			if(!empty($this->request->getVar('bsd_comp_applytax_json')))
				$bsd_comp_applytax_json    = json_decode($this->request->getVar('bsd_comp_applytax_json'),true);
			
			
			$total_tax_amount=0;
			if($bsd_comp_applytax_json){
				foreach($bsd_comp_applytax_json as $ttax){
					$total_tax_amount =$total_tax_amount+$ttax['billsundry_amount'];
				}
			}
			 			
			$billsndrydata = [];
			if(!empty($this->request->getVar('billsndrydata')))
				$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
			
			$taxsummarydata = [];
			if(!empty($this->request->getVar('taxsummarydata')))
				$taxsummarydata    = json_decode($this->request->getVar('taxsummarydata'),true); 
			
			$voucher_no = $this->TransactionModel->get_voucher_no($voucher_type_id);
			$voucher_txn_id   = 0;
			$sale_total       = array_sum(array_column($itmsdata, 'item_total_amount'));
			$sale_memo_total  = array_sum(array_column($itmsdata, 'memo_amount'));
			$txinc_amount     = array_sum(array_column($itmsdata, 'txinc_amount'));
			
			$billsundry_memo_total = 0;
			if(count($billsndrydata))
				$billsundry_memo_total = array_sum(array_column($billsndrydata, 'memo_amount'));
			
			$billsundry_total = 0;
			if(count($billsndrydata))
				$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
			
			
					 // add voucher consolidated entry
					 $voucher_txn_data     = array(
					 			"comp_id"				=> $this->company_id,
					 			"comp_vch_series_id"	=> $voucher_series,
					 			"comp_vch_no"			=> $voucher_no,
					 			"voucher_type_id"		=> $voucher_type_id,
						        "voucher_date"			=> $voucher_date,
						        "mat_cent_id"			=> $matrcntr_id,
						        "voucher_tag"			=> '',
						        "currency_id"			=> $currency_id,
						  );
					 $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($voucher_txn_data);
					 if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				  $bill_no_counter    = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,0);
				  $consodata          = array("vch_bill_ref_no"=>$bill_no_counter);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
				}
				//Save Eway Tables Data
				$ewbmstreqn_insert_data = array("ewb_supply_type"=>$supply_type,"ewb_sub_supply_desc"=>$ewb_sub_supply_desc,
				                               "ewb_doc_type"=>"","voucher_txn_id"=>$voucher_txn_id,"ewb_txn_type"=>$tax_type_id,
				                               "bo_id"=>$bo_id);
				$ewb_id = $this->TransactionModel->add_ewbmstreqn_data($ewbmstreqn_insert_data);	
				
				
				// Save Into ewbmstcons table
				$ewbmstcons_insert_data = array("conso_ewb_no"=>$voucher_no,"conso_ewb_date"=>$voucher_date,
				                               "ewb_id"=>$ewb_id,"ewb_no"=>0);
				$einv_id = $this->TransactionModel->add_ewbmstcons_data($ewbmstcons_insert_data);
				
				
				if($transport_distance!='' && $vehicle_no!=''){
				$transport_doc_date = date('Y-m-d',strtotime($transport_doc_date));	
				// save into ewbpartbdt table
				$ewbpartbdt_data = array("gsttpt_id"=>$transporter,"ewb_id"=>$ewb_id,"trans_veh_no"=>$vehicle_no,
							 "trans_veh_type"=>$vehicle_type,"trans_mode"=>$transport_mode,
							 "trans_doc_no"=>$transporter_doc_no,"trans_doc_date"=>$transport_doc_date,
							 "trans_dist"=>$transport_distance,"trans_frm_state_code"=>$pos,"voucher_txn_id"=>$voucher_txn_id);	
				$this->TransactionModel->add_ewbpartbdt($ewbpartbdt_data);			 
				}
							
				// save into gstroutsup table
				if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,$vch_bill_ref_no);
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"outsup_bill_ref_no"=>$bill_no_format,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				  $this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}
				else{
				$gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				$this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}
				
                // Save Tax Type data
				$this->TransactionModel->add_taxtype_data($tax_type_id,$ewb_id,$einv_id,$_POST);

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
					$acc_cess_fcy        = $rdata['cess_fcy_val'];	
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
									'acc_txn_date'         => date('Y-m-d',strtotime($voucher_date))
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
													  "acc_type"=>"bsd" );
					         $this->TransactionModel->add_acctgstfcy_data($acctgstfcy_insert_data);
					       }
				       }
				
				
				    }
				}
				
				/********** End Tax Summary Code   **********/		

					 // Add party oth txn entry 
			     $insert_data  = [
										'comp_id'            		 => $this->company_id,
										'acc_oth_txn_date'       => $voucher_date,
										'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
										'acc_oth_txn_fcy'        => ($currency_id>1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,						
										'acc_oth_txn_drcr'       => 'd',
										'voucher_type_id'    		 => $voucher_type_id,
										'comp_vch_series_no' 		 => $voucher_series,
										'acc_id'             		 => $party_id,						
										'voucher_txn_id'     		 => $voucher_txn_id,
										'acc_oth_txn_status' 		 => $voucher_no,
										'bo_id'						 => $this->session->get('ses_boid'),
										'txn_id'					 => $txn_id,
										'acc_oth_txn_duedate' 	     => $due_date,
										'acc_oth_txn_tag'			 => $oth_txn_tag,
								];
						$this->TransactionModel->add_acc_oth_data($insert_data);
				/**********  Memo Account Entry  ************/
				if($sale_memo_total >0){
				$insert_data  = [
							'comp_id'            => $this->company_id,
							'acc_id'             => $party_id,
							'acc_txn_date'       => $voucher_date,
							'acc_txn_amount'     => ($sale_memo_total + $billsundry_memo_total),
							'acc_txn_fcy'        => ($currency_id>1)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0,						
							'acc_txn_drcr'       => 'd',
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
				 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$item_row['memo_amount'],"acc_txn_drcr"=>"c","comp_vch_series_no"=>$voucher_series,"acc_id"=>$item_row['item_id'],"acc_type"=>"itm","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
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
							if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
				      $insert_data   = array(
					        'txn_id'              => $txn_id,
							'voucher_txn_id'      => $voucher_txn_id,
							'vch_txn_drcr'        => 'c',
							'vch_txn_incl'        => $item_row['txinc_amount'],
							'txn_type'            => 'itm'
					        );	
					$this->TransactionModel->add_taxinclusive_txn_data($insert_data);
				    }
							$insert_data   = array(
								'comp_id'              => $this->company_id,
								'item_oth_txn_date'    => $voucher_date,
								'item_oth_txn_amount'  => $item_row['item_total_amount'],
								'item_oth_txn_fcy'     => ($currency_id > 1)?$item_row['item_total_amount']*$fcy_forex_rate:0,
								'item_oth_txn_drcr'    => 'c',
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
								'acc_oth_txn_fcy'      => ($currency_id >1)?$amount*$fcy_forex_rate:0,						
								'acc_oth_txn_drcr'     => 'c',
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
			                        "item_txn_date"			=> $voucher_date,
															"item_id"						=> $value['item_id'],
															'item_txn_drcr'     => 'c',
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
								if(isset($value['billsundry_id']) && $value['billsundry_id']>0){
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
						$billsundry_memo_drcr = 'c';
					else
						$billsundry_memo_drcr = 'd';	 
					
				 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$value['memo_amount'],"acc_txn_drcr"=>$billsundry_memo_drcr,"comp_vch_series_no"=>$voucher_series,"acc_id"=>$value['billsundry_id'],"acc_type"=>"bsd","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
				 $this->TransactionModel->save_sale_memeo_txns($memo_data);
				 $this->TransactionModel->update_account_memo_balance($value['billsundry_id'],'bsd');
				}
				}
					 /**** End Save MEMO TXN *****/	
						      if($value['billsundry_amount'] >= 0)
						      		$billsundry_drcr = 'c';
						      else
						      	  $billsundry_drcr = 'd';

			           $insert_data  = [
										'comp_id'            	   => $this->company_id,
										'acc_oth_txn_date'       => $voucher_date,
										'acc_oth_txn_amount'     => abs($value['billsundry_amount']),
										'acc_oth_txn_fcy'        => ($currency_id >1)?abs($value['billsundry_amount'])*$fcy_forex_rate:0,						
										'acc_oth_txn_drcr'       => $billsundry_drcr,
										'voucher_type_id'    	   => $voucher_type_id,
										'comp_vch_series_no' 	   => $voucher_series,
										'acc_id'             	   => $value['billsundry_id'],						
										'voucher_txn_id'     	   => $voucher_txn_id,
										'acc_oth_txn_status' 	   => "",
										'bo_id'					  => $this->session->get('ses_boid'),
										'txn_id'				  => $txn_id,
										'acc_oth_txn_duedate' 	  => date('Y-m-d'),
										'acc_oth_txn_tag'		  => 'BILSDRY',
								];
								$this->TransactionModel->add_acc_oth_data($insert_data);
				} }
			    }

			    // return redirect()->to($this->base_url.'quotations/item');
			    return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
			  } // else validation
			} // input type post

		$data['message_output'] 				    = $this->message_output;
		$data['voucher_auto_no']  				    = $this->TransactionModel->get_voucher_no($voucher_type_id);
		$voucher_series_dropdown                    = $this->TransactionModel->voucher_series_dropdowns($voucher_type_id);
		$data['voucher_date']                       = $voucher_detail['last_entry'];
		$data['voucher_series_dropdown']            = $voucher_series_dropdown;
		$data['voucher_bill_no']                    = $this->TransactionModel->get_billno_format($voucher_type_id,17,$voucher_detail['last_entry'],1,'counter');		
		$data['base_url']       					= $this->base_url; 
		$data['folder_path']    					= $this->folder_path;
		$data['supply_type_list']     				= $this->SupplyTypes;
		$data['tax_type_list']                      = $this->TXNTypesList;		
		$data['units_list']     					= $this->TransactionModel->units_dropdown();
		
		$data['party_dropdown']     			= $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
		$data['sub_type_dropdown']  			= $this->TransactionModel->vch_subtype_dropdown($voucher_type_id);
		$data['sale_against_challan']           = $this->TransactionModel->against_dropdown('7',array('DCESDUE','DCESDEF','SEDCSUR'));
		$data['billsundry_items']  				= $this->TransactionModel->billsundry_items();
		$data['bills_method_list']				= ['','New Ref.','Adjustment'];
			
			$data['bills_method_list']				= ['','New Ref.','Adjustment'];
		
		
		$data['voucher_type_id']                = $voucher_type_id;
		
		
		$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
		$bsd_file_name   = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
	   
	   $data['item_json_file'] = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);	 
	  
	   $bsd_json_file = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
	   if($bsd_json_file)
	   $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
       else
		$data['bsd_json_file']                   ='[]';   

        $data['currency_list']  				= $this->TransactionModel->get_currency_list();
        $data['states_lists']                   = $this->TransactionModel->show_states_lists();
        $data['bo_state_code']                  = $bo_state_code;
        $data['transport_modes']                = transport_modes();
        $data['vehicle_types']                  = vehicle_types();
		$data['billfrm_data']                   = $this->TransactionModel->billfrm_info();
		$data['transporter_dropdown']           = $this->TransactionModel->transporter_dropdown();
		$data['bo_gstin_type']                  = $this->session->get('bo_gstin_type');
		$data['InvoiceTypeList']                = InvoiceTypeList();
		$data['eco_dropdown']                   = $this->TransactionModel->show_eco_lists();
        
			return view($this->folder_path.'quotations/item',$data);		
		}

		public function non_item()
		{
			$ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
			$voucher_type_id    = "17";
			$oth_txn_tag 				= 'QUOTENN';
			$bo_id             = $this->session->get('ses_boid');								 
		    $bo_state_code     = $this->session->get('ses_bostecd');
            $voucher_detail = $this->TransactionModel->get_voucher_info($voucher_type_id);
			if($this->request->getMethod() == 'post' && $this->request->isAjax())
			{	

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
			   'due_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Due Date is required'
				  ],
			  ],
			 ];
			
        if(!$this->validate($rules)){
        	$errors = $this->validator->getErrors();
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
        }
        else
        {
        	$invoice_type     = $this->request->getVar('invoice_type');
			 $voucher_date     = $this->request->getVar('voucher_date');
		     $voucher_date 	   = validate_date_by_fy($voucher_date);
		     $voucher_series   = $this->request->getVar('voucher_series'); 
		     $party_id         = $this->request->getVar('party_id');  
		     $narration        = $this->request->getVar('narration'); 
		     $itmsdata         = json_decode($this->request->getVar('itmsdata'),true); 
		     $currency_id 	   = $this->request->getVar('currency_id');			 
			 $billno 		   = $this->request->getVar('billno');
			 $supply_type      = $this->request->getVar('supply_type');
			 
			 $tax_type_id      = 0;
			 $ewb_sub_supply_desc = $this->request->getVar('supply_type_desc');
			 $reverse_charges  = $this->request->getVar('reverse_charges');
			 $pos              = $this->request->getVar('pos');
			 $gstpaidacc_id    = $this->request->getVar('gst_paidacc_id');
			 $fcy_forex_rate      = $this->request->getVar('fcy_forex_rate');
			 $memoCheck           = $this->request->getVar('memoCheck');			
			 $taxInclusive        = $this->request->getVar('taxInclusive');
			 $due_date           = $this->request->getVar('due_date');
			$outsup_eco              = $this->request->getVar('eco_id');
			if($outsup_eco=='')				
				$outsup_eco='0';
			
			$isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			 if($billno=="" || $billno=="0")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']]);
			$billno_duplicate = $this->TransactionModel->BillNoDuplicate($billno);
			if($billno_duplicate=="1")
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']]);
			}
		     
		     $billsndrydata = [];
			 if(!empty($this->request->getVar('billsndrydata')))
			 	$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);

			$taxsummarydata = [];
			if(!empty($this->request->getVar('taxsummarydata')))
				$taxsummarydata    = json_decode($this->request->getVar('taxsummarydata'),true); 
			
			$bsd_comp_applytax_json = [];
			if(!empty($this->request->getVar('bsd_comp_applytax_json')))
				$bsd_comp_applytax_json    = json_decode($this->request->getVar('bsd_comp_applytax_json'),true);
			
			$total_tax_amount=0;
			if($bsd_comp_applytax_json){
				foreach($bsd_comp_applytax_json as $ttax){
					$total_tax_amount =$total_tax_amount+$ttax['billsundry_amount'];
				}
			}
			$sale_memo_total  = array_sum(array_column($itmsdata, 'memo_amount'));
			$billsundry_memo_total = 0;
			if(count($billsndrydata))
			$billsundry_memo_total = array_sum(array_column($billsndrydata, 'memo_amount'));

			  $bbbdata = [];
			  if(!empty($this->request->getVar('bbbdata')))
			 	  $bbbdata = json_decode($this->request->getVar('bbbdata'),true);
			 if(!empty($this->request->getVar('prdata')))
		 		$prdata = json_decode($this->request->getVar('prdata'),true);
				 
					$voucher_no       = $this->TransactionModel->get_voucher_no($voucher_type_id);

				  $sale_total = array_sum(array_column($itmsdata, 'amount'));
				  
					$billsundry_total = 0;
					if(count($billsndrydata))
							$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
		     

				  $voucher_tag= '';
					 // add voucher consolidated entry
					 $voucher_txn_data     = array(
					 			"comp_id"							=> $this->company_id,
					 			"comp_vch_series_id"	=> $voucher_series,
					 			"comp_vch_no"					=> $voucher_no,
					 			"voucher_type_id"			=> $voucher_type_id,
						    "voucher_date"				=> $voucher_date,
						    "mat_cent_id"					=> 0,
						    "voucher_tag"					=> '',
						    "currency_id"				=> $currency_id,
						  );
					 $voucher_txn_id = $this->TransactionModel->add_voucher_cons_data($voucher_txn_data);
					 //Save Eway Tables Data
				$ewbmstreqn_insert_data = array("ewb_supply_type"=>$supply_type,"ewb_sub_supply_desc"=>$ewb_sub_supply_desc,
				                               "ewb_doc_type"=>"","voucher_txn_id"=>$voucher_txn_id,"ewb_txn_type"=>$tax_type_id,
				                               "bo_id"=>$bo_id);
				$ewb_id = $this->TransactionModel->add_ewbmstreqn_data($ewbmstreqn_insert_data);	
				 				  
				if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				  $bill_no_counter    = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,0);
				  $consodata          = array("vch_bill_ref_no"=>$bill_no_counter);
				  $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
				}
				  // save into gstroutsup table
				if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,$vch_bill_ref_no);
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"outsup_bill_ref_no"=>$bill_no_format,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				  $this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}else{  
				$gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				$this->TransactionModel->add_gstroutsup_data($gstroutsup_insert_data);
				}

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
					  if(isset($rdata['tax_cat_id']))
						$item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($rdata['tax_item_id_val'],$rdata['tax_cat_id']);		
					  else
						$item_tax_short_code = "";		  
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
									'acc_cess_rate'        => parseAmount($rdata['cess_rate_val']),
									'acc_cess'             => parseAmount($rdata['cess_val']),
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
					if($bo_state_code!=$pos){
						$acc_igst_rate = parseAmount($rdata['tax_rate_val']);
						$acc_igst      = parseAmount($rdata['igst_val']);
						$acc_igst_fcy  = parseAmount($rdata['igst_fcy_val']);
						$acc_cgst_rate = $acc_cgst=$acc_sgst_rate=$acc_sgst=$acc_cgst_fcy=$acc_sgst_fcy=$acc_cess_fcy=0;
					 }
					 else{
						$acc_igst_rate = $acc_igst=$acc_igst_fcy=0;
						$acc_cgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_cgst_fcy  = parseAmount($rdata['cgst_fcy_val']);
						$acc_cgst      = parseAmount($rdata['cgst_val']);
						$acc_sgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_sgst      = parseAmount($rdata['sgst_val']);
						$acc_sgst_fcy  = parseAmount($rdata['sgst_fcy_val']);
					   } 
					$bsd_txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);				
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
										'comp_id'            		 => $this->company_id,
										'acc_oth_txn_date'       => $voucher_date,
										'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
										'acc_oth_txn_fcy'        => ($currency_id>1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,						
										'acc_oth_txn_drcr'       => 'd',
										'voucher_type_id'    		 => $voucher_type_id,
										'comp_vch_series_no' 		 => $voucher_series,
										'acc_id'             		 => $party_id,						
										'voucher_txn_id'     		 => $voucher_txn_id,
										'acc_oth_txn_status' 		 => $voucher_no,
										'bo_id'						=> $this->session->get('ses_boid'),
										'txn_id'					=> $txn_id,
										'acc_oth_txn_duedate' 	    => $due_date,
										'acc_oth_txn_tag'				 => $oth_txn_tag,
								];
						$this->TransactionModel->add_acc_oth_data($insert_data);
/**********  Memo Account Entry  ************/
				if($sale_memo_total >0){
				$insert_data  = [
							'comp_id'            => $this->company_id,
							'acc_id'             => $party_id,
							'acc_txn_date'       => $voucher_date,
							'acc_txn_amount'     => ($sale_memo_total + $billsundry_memo_total),
							'acc_txn_fcy'        => ($currency_id>1)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0,						
							'acc_txn_drcr'       => 'd',
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
						foreach($itmsdata as $item_row)
						{
						/**** Save MEMO TXN *****/
				if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){	
				 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$item_row['memo_amount'],"acc_txn_drcr"=>"c","comp_vch_series_no"=>$voucher_series,"acc_id"=>$item_row['account_id'],"acc_type"=>"acc","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
				 $this->TransactionModel->save_sale_memeo_txns($memo_data);
				}
							$txn_data = array(
								"comp_id"							=> $this->company_id,
								"comp_vch_series_id"	=> $voucher_series,
								"voucher_txn_id" 			=> $voucher_txn_id,
								"master_id" 					=> $item_row['account_id'],
								'master_id_type' 			=> 'aco'
						 	);
	           $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 
 if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
				      $insert_data   = array(
					        'txn_id'              => $txn_id,
							'voucher_txn_id'      => $voucher_txn_id,
							'vch_txn_drcr'        => 'c',
							'vch_txn_incl'        => $item_row['txinc_amount'],
							'txn_type'            => 'acc'
					        );	
					$this->TransactionModel->add_taxinclusive_txn_data($insert_data);
				    }
				     $insert_data  = [
											'comp_id'            		 => $this->company_id,
											'acc_oth_txn_date'       => $voucher_date,
											'acc_oth_txn_amount'     => $item_row['amount'],
											'acc_oth_txn_fcy'        => ($currency_id >1)?$item_row['amount']*$fcy_forex_rate:0,						
											'acc_oth_txn_drcr'       => 'c',
											'acc_oth_txn_narr'       => $item_row['description'],
											'voucher_type_id'    		 => $voucher_type_id,
											'comp_vch_series_no' 		 => $voucher_series,
											'acc_id'             		 => $item_row['account_id'],						
											'voucher_txn_id'     		 => $voucher_txn_id,
											'acc_oth_txn_status' 		 => $voucher_no,
											'bo_id'						 => $this->session->get('ses_boid'),
											'txn_id'					 => $txn_id,
											'acc_oth_txn_duedate' 	 => $due_date,
											'acc_oth_txn_tag'				 => $oth_txn_tag,
									];
							$this->TransactionModel->add_acc_oth_data($insert_data);
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

				    if(count($billsndrydata))
				    {
								foreach($billsndrydata as $value)
							{
								if(isset($value['billsundry_id']) && $value['billsundry_id']>0){
								
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
								$billsundry_memo_drcr = 'c';
							else
								$billsundry_memo_drcr = 'd';	 
						 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$value['memo_amount'],"acc_txn_drcr"=>$billsundry_memo_drcr,"comp_vch_series_no"=>$voucher_series,"acc_id"=>$value['billsundry_id'],"acc_type"=>"bsd","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
						 $this->TransactionModel->save_sale_memeo_txns($memo_data);
						 $this->TransactionModel->update_account_memo_balance($value['billsundry_id'],'bsd');
						}
					}
					 /**** End Save MEMO TXN *****/	
						      if($value['billsundry_amount'] >= 0)
						      		$billsundry_drcr = 'c';
						      else
						      	  $billsundry_drcr = 'd';

			           $insert_data  = [
										'comp_id'            	   => $this->company_id,
										'acc_oth_txn_date'       => $voucher_date,
										'acc_oth_txn_amount'     => abs($value['billsundry_amount']),
										'acc_oth_txn_fcy'        => ($currency_id >1)?abs($value['billsundry_amount'])*$fcy_forex_rate:0,						
										'acc_oth_txn_drcr'       => $billsundry_drcr,
										'voucher_type_id'    	   => $voucher_type_id,
										'comp_vch_series_no' 	   => $voucher_series,
										'acc_id'             	   => $value['billsundry_id'],						
										'voucher_txn_id'     	   => $voucher_txn_id,
										'acc_oth_txn_status' 	   => "",
										'bo_id'					   => $this->session->get('ses_boid'),
										'txn_id'				         => $txn_id,
										'acc_oth_txn_duedate' 	 => date('Y-m-d'),
										'acc_oth_txn_tag'		     => 'BILSDRY',
								];
								$this->TransactionModel->add_acc_oth_data($insert_data);
					} }
				    }

			    // return redirect()->to($this->base_url.'quotations/non_item');
				    return json_encode(['status' => true, 'message' => 'Voucher Inserted']);
				}

			}

			$data['message_output']   = $this->message_output;			
			$data['supply_type_list'] = $this->SupplyTypes;
	    	$data['tax_type_list']    = $this->TXNTypesList;
			$data['base_url']         = $this->base_url; 
			$data['folder_path']      = $this->folder_path;
			$data['voucher_auto_no']  = $this->TransactionModel->get_voucher_no($voucher_type_id);
			$data['voucher_bill_no']  = $this->TransactionModel->get_billno_format($voucher_type_id,17,$voucher_detail['last_entry'],1,'counter');
			$data['items_list']       = $this->TransactionModel->items_list();	
			$data['units_list']       = $this->TransactionModel->units_dropdown();
			$data['voucher_series_dropdown']  = $this->TransactionModel->voucher_series_dropdowns($voucher_type_id);
			$data['party_dropdown']     = $this->TransactionModel->party_dropdown();
			$data['matrcntr_dropdown']  = $this->TransactionModel->matrcntr_dropdown();
			$data['bills_method_list']	= ['','New Ref.','Adjustment'];
            $data['voucher_date']       = $voucher_detail['last_entry'];
			
			$acc_file_name   = 'acc'.$this->session->get('ses_comp_fy_id').'.json';
            $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
            $data['accounts_json_file']       = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$acc_file_name);
            $data['bsd_json_file']          = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
            
            $data['voucher_type_id']   = $voucher_type_id;
            $data['bo_state_code']     = $bo_state_code;
            $data['currency_list']     = $this->TransactionModel->get_currency_list();
			$data['states_lists']      = $this->TransactionModel->show_states_lists();
			$data['transport_modes']                = transport_modes();
			$data['vehicle_types']                  = vehicle_types();
			$data['bo_gstin_type']                 = $this->session->get('bo_gstin_type');
			$data['transporter_dropdown']           = $this->TransactionModel->transporter_dropdown();
			$data['InvoiceTypeList']                = InvoiceTypeList();
			$data['eco_dropdown']                   = $this->TransactionModel->show_eco_lists();
			
             
			return view($this->folder_path.'quotations/non_item',$data);		
		}

		public function edit($voucher_txn_id)
		{
				$voucher_type_id    = "17";
				$oth_txn_tag 				= 'QUOTENN';
				$bo_id             = $this->session->get('ses_boid');								 
				$bo_state_code     = $this->session->get('ses_bostecd');

				
			
			$istaxinc         =  $this->TransactionModel->check_sale_taxinc($voucher_txn_id);
			$ismemosale       =  $this->TransactionModel->check_sale_memoentry($voucher_txn_id);
			$voucher_detail    = $this->TransactionModel->get_voucher_info($voucher_type_id);	  
			$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
			if(empty($voucher_info)){
				 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}
			$isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$voucher_info['comp_vch_series_id']);
			$get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
			
			$vch_subtype_id = $voucher_info['vch_subtype_id'];
			$data['vch_subtype_id'] = $vch_subtype_id;
			$allbos           =  $this->TransactionModel->all_mig_bo_lists();
			$data['allbos']   = $allbos;
			/**************  Duplicate Voucher Code ***************/
			if(isset($_GET['duplc']) && $_GET['duplc']=='1'){
				$data['duplc'] 		     = 1;
				$page_label              = 'Duplicate';		
				$data['voucher_no']      = $this->TransactionModel->get_voucher_no($voucher_type_id);
				$data['voucher_bill_no'] = $this->TransactionModel->get_billno_format($voucher_type_id,17,$voucher_detail['last_entry'],1,'counter');
			}else{
				 $data['duplc'] 	 = 0;
                 $page_label         = 'Update';  		
			     $data['voucher_no'] = $voucher_info['comp_vch_no'];			  
			}
			$data['page_label'] = $page_label;	
			/**************  Duplicate Voucher Code ***************/
				if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
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
					   'due_date' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Due Date is required'
						  ],
					  ],
					 ];
					 $type = $this->request->getVar('type');
						if($type == 'item'){
							$rules['matrcntr_id'] = [
								'rules'  => 'required',
								'errors' => [
									'required' => 'Material Center is required'
							  ],
							];
						}
					
		        if(!$this->validate($rules)){
		        	$errors = $this->validator->getErrors();
		        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		        }
		        else
		        {
		        	 $voucher_date         = $this->request->getVar('voucher_date');
        	 			$voucher_date         = validate_date_by_fy($voucher_date);

		        	 $party_id          = $this->request->getVar('party_id'); 
					 $vch_subtype_id          = $this->request->getVar('vch_subtype_id');                  
							 $voucher_series    = $this->request->getVar('voucher_series');
							 $matrcntr_id       = $this->request->getVar('matrcntr_id'); 
							 $narration         = $this->request->getVar('narration'); 
							 $quotation_no   		= $this->request->getVar('quotation_no'); 
					 		 $sales_executive   = $this->request->getVar('sales_executive');; 
							 $due_date         = $this->request->getVar('due_date');
        	 			$due_date         = validate_date_by_fy($due_date);
							 $itmsdata          = json_decode($this->request->getVar('itmsdata'),true); 
							 $currency_id 			= $this->request->getVar('currency_id');

							 $voucher_no        = $this->TransactionModel->get_voucher_no($voucher_type_id);
							 $type 							= $this->request->getVar('type');
							 
							 
							 	$batchdata = []; // batch coding
				  		if(!empty($this->request->getVar('batchinfo_array')))
				 				$batchdata = json_decode($this->request->getVar('batchinfo_array'),true);
				 			
				 			
							 $billsndrydata = [];
							 if(!empty($this->request->getVar('billsndrydata')))
							 		$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
							 
		     	 
					     if($type == 'item')
					     {
							 $invoice_type        = $this->request->getVar('invoice_type');	
						  $voucher_date        = $this->request->getVar('voucher_date');
						  $voucher_series      = $this->request->getVar('voucher_series'); 
						  $party_id            = $this->request->getVar('party_id'); 
						  $matrcntr_id         = $this->request->getVar('matrcntr_id'); 
						  $narration           = $this->request->getVar('narration');  
						  $itmsdata            = json_decode($this->request->getVar('itmsdata'),true); 
						  $voucher_date        = validate_date_by_fy($voucher_date);
						  $delivery_challan_id = $this->request->getVar('delivery_challan_id');
						  $currency_id         = $this->request->getVar('currency_id');
						  $supply_type         = $this->request->getVar('supply_type');
						  
						  $tax_type_id         = $this->request->getVar('tax_type_id');
						  $ewb_sub_supply_desc = $this->request->getVar('supply_type_desc');
						  $transporter         = $this->request->getVar('transporter');		
						  $transporter_name    = $this->request->getVar('transporter_name');		
						  $transporter_doc_no  = $this->request->getVar('transporter_doc_no');
						  $transport_mode      = $this->request->getVar('transport_mode');			
						  $transport_distance  = $this->request->getVar('transport_distance');
						  $transport_doc_date  = $this->request->getVar('transport_doc_date');
						  $vehicle_no          = $this->request->getVar('vehicle_no');
						  $vehicle_type        = $this->request->getVar('vehicle_type');
						  $pos                 = $this->request->getVar('pos');
						  $billno              = $this->request->getVar('billno');
						  $reverse_charges     = $this->request->getVar('reverse_charges');
						  $ewb_id              = $this->request->getVar('ewb_id');
						  $gstpaidacc_id       = $this->request->getVar('gst_paidacc_id');
						  $fcy_forex_rate      = $this->request->getVar('fcy_forex_rate');
						  $memoCheck           = $this->request->getVar('memoCheck');
						  $taxInclusive        = $this->request->getVar('taxInclusive');
						  $outsup_eco              = $this->request->getVar('eco_id');
			if($outsup_eco=='')				
				$outsup_eco='0';
						
						  $isvoucher_autobillno   = $this->TransactionModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
						if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
						 if($billno=="" || $billno=="0")
						   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']]);
						$billno_duplicate = $this->TransactionModel->BillNoDuplicate($billno,$voucher_txn_id);
						if($billno_duplicate=="1")
						   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']]);
						 }
			 
			  $isdplc_vch =  $this->request->getVar('isdplc_vch');
			if($isdplc_vch==1){	
				if($vch_subtype_id == 0)	
                  echo $this->non_item();			
			    else 
				echo $this->item();
				die();
			} 		
			
					$bsd_comp_applytax_json = [];
			if(!empty($this->request->getVar('bsd_comp_applytax_json')))
				$bsd_comp_applytax_json    = json_decode($this->request->getVar('bsd_comp_applytax_json'),true);
						
			$taxsummarydata = [];
			if(!empty($this->request->getVar('taxsummarydata')))
				$taxsummarydata    = json_decode($this->request->getVar('taxsummarydata'),true); 
			
			
					     		 $sale_total = array_sum(array_column($itmsdata, 'item_total_amount'));
									 $billsundry_total = 0;
									 if(count($billsndrydata))
									 		$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
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
			  $voucher_no = $voucher_info['comp_vch_no'];
			
			  if( $transport_distance!='' && $vehicle_no!=''){
				 $transport_doc_date = date('Y-m-d',strtotime($transport_doc_date));	
				// save into ewbpartbdt table
				$ewbpartbdt_data = array("gsttpt_id"=>$transporter,"trans_veh_no"=>$vehicle_no,
							 "trans_veh_type"=>$vehicle_type,"trans_mode"=>$transport_mode,
							 "trans_doc_no"=>$transporter_doc_no,"trans_doc_date"=>$transport_doc_date,
							 "trans_dist"=>$transport_distance,"trans_frm_state_code"=>$pos,"voucher_txn_id"=>$voucher_txn_id);	
				$this->TransactionModel->add_ewbpartbdt($ewbpartbdt_data);			 
				}
				//Update Eway Tables Data
			     $ewbmstreqn_upd_data = array("ewb_supply_type"=>$supply_type,"ewb_sub_supply_desc"=>$ewb_sub_supply_desc,
				                               "ewb_doc_type"=>"","ewb_txn_type"=>$tax_type_id,
				                               "bo_id"=>$bo_id);
		         $this->TransactionModel->update_ewbmstreqn_data($voucher_txn_id,$ewbmstreqn_upd_data);
		       
				// Update Into ewbmstcons table
				$ewbmstcons_insert_data = array("conso_ewb_no"=>$voucher_no,"conso_ewb_date"=>$voucher_date,
				                               "ewb_no"=>$billno);
				$this->TransactionModel->update_ewbmstcons_data($ewb_id,$ewbmstcons_insert_data);
				
				
				if($transport_distance!='' && $vehicle_no!=''){
				$transport_doc_date = date('Y-m-d',strtotime($transport_doc_date));	
				// save into ewbpartbdt table
				$ewbpartbdt_data = array("gsttpt_id"=>$transporter,"ewb_id"=>$ewb_id,"trans_veh_no"=>$vehicle_no,
							 "trans_veh_type"=>$vehicle_type,"trans_mode"=>$transport_mode,
							 "trans_doc_no"=>$transporter_doc_no,"trans_doc_date"=>$transport_doc_date,
							 "trans_dist"=>$transport_distance,"trans_frm_state_code"=>$pos,"voucher_txn_id"=>$voucher_txn_id);	
				$this->TransactionModel->add_ewbpartbdt($ewbpartbdt_data);			 
				}
							
				
                // Save Tax Type data
				$einv_id =0;
				$this->TransactionModel->add_taxtype_data($tax_type_id,$ewb_id,$einv_id,$_POST);
				

									 // add voucher consolidated entry
									 $voucher_txn_data     = array(
									 			"comp_vch_series_id"	=> $voucher_series,
										    "voucher_date"				=> $voucher_date,
										    "mat_cent_id"					=> $matrcntr_id,
										    "voucher_tag"					=> '',
										    "currency_id"				  => $currency_id,
										  );
									 $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);

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
									 // delete comp txn master
									 $this->TransactionModel->delete_taxinc_txn_data($voucher_txn_id,'itm');
					$this->TransactionModel->delete_memo_txn_data($voucher_txn_id);
					$this->TransactionModel->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
									 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
									 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_all_narrations($voucher_txn_id);
									 					$this->TransactionModel->delete_acctgstsum_data($voucher_txn_id);
if($isvoucher_autobillno==1){ // means voucher has auto bill numbering
				   /* $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				   $vch_bill_ref_no =$voucher_info['vch_bill_ref_no'];
				
				  $bill_no_format    = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,$vch_bill_ref_no); */
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				  $this->TransactionModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
				}
				else{
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				  $this->TransactionModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
				  }	
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
									'acc_cess_rate'        => parseAmount($rdata['cess_rate_val']),
									'acc_cess'             => parseAmount($rdata['cess_val']),
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
					$bsd_txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);				
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
					        $acctgstfcy_insert_data = array("acc_id"=>$rdata['tax_item_id_val'],"vch_txn_id"=>$voucher_txn_id,
													  "txn_id"=>$bsd_txn_id,"acc_igst_fcy"=>$acc_igst_fcy,
													  "acc_cgst_fcy"=>$acc_cgst_fcy,"acc_sgst_fcy"=>$acc_sgst_fcy,
													  "acc_cess_fcy"=>$acc_cess_fcy,"total_tax_fcy"=>$total_tax_fcy,
													  "acc_type"=>"bsd");
					       $this->TransactionModel->add_acctgstfcy_data($acctgstfcy_insert_data);
					      }
				       }
				
				
				    } 
				}
				/********** End Tax Summary Code   **********/
									 // Add party oth txn entry 
							     $insert_data  = [
														'comp_id'            		 => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
														'acc_oth_txn_fcy'        => ($currency_id>1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,						
														'acc_oth_txn_drcr'       => 'd',
														'voucher_type_id'    		 => $voucher_type_id,
														'comp_vch_series_no' 		 => $voucher_series,
														'acc_id'             		 => $party_id,						
														'voucher_txn_id'     		 => $voucher_txn_id,
														'acc_oth_txn_status' 		 => $voucher_no,
														'bo_id'									 => $this->session->get('ses_boid'),
														'txn_id'								 => $txn_id,
														'acc_oth_txn_duedate' 	 => $due_date,
														'acc_oth_txn_tag'				 => $oth_txn_tag,
												];
										$this->TransactionModel->add_acc_oth_data($insert_data);
/**********  Memo Account Entry  ************/
				if($sale_memo_total >0){
				$insert_data  = [
							'comp_id'            => $this->company_id,
							'acc_id'             => $party_id,
							'acc_txn_date'       => $voucher_date,
							'acc_txn_amount'     => ($sale_memo_total + $billsundry_memo_total),
							'acc_txn_fcy'        => ($currency_id>1)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0,						
							'acc_txn_drcr'       => 'd',
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
							    {/**** Save MEMO TXN *****/
				if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){		
				 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$item_row['memo_amount'],"acc_txn_drcr"=>"c","comp_vch_series_no"=>$voucher_series,"acc_id"=>$item_row['item_id'],"acc_type"=>"itm","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
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
if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
				      $insert_data   = array(
					        'txn_id'              => $txn_id,
							'voucher_txn_id'      => $voucher_txn_id,
							'vch_txn_drcr'        => 'c',
							'vch_txn_incl'        => $item_row['txinc_amount'],
							'txn_type'            => 'itm'
					        );	
					$this->TransactionModel->add_taxinclusive_txn_data($insert_data);
				    }
											$insert_data   = array(
												'comp_id'              => $this->company_id,
												'item_oth_txn_date'    => $voucher_date,
												'item_oth_txn_amount'  => $item_row['item_total_amount'],
												'item_oth_txn_fcy'     => ($currency_id > 1)?$item_row['item_total_amount']*$fcy_forex_rate:0,
												'item_oth_txn_drcr'    => 'c',
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
												'acc_oth_txn_fcy'      => ($currency_id >1)?$amount*$fcy_forex_rate:0,						
												'acc_oth_txn_drcr'     => 'c',
												'voucher_type_id'      => $voucher_type_id,
												'comp_vch_series_no'   => $voucher_series,
												'acc_id'               => $account_id,						
												'voucher_txn_id'       => $voucher_txn_id,
												'acc_oth_txn_status'   => $voucher_txn_id, // changed from $voucher_no
												'bo_id'				   => $this->session->get('ses_boid'),
												'txn_id'			   => $txn_id,
												'acc_oth_txn_duedate'  => date('Y-m-d'),
												'acc_oth_txn_tag'	   	 => $oth_txn_tag,
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
												if(isset($value['billsundry_id']) && $value['billsundry_id']>0){
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
											$billsundry_memo_drcr = 'c';
										else
											$billsundry_memo_drcr = 'd';	 
									 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$value['memo_amount'],"acc_txn_drcr"=>$billsundry_memo_drcr,"comp_vch_series_no"=>$voucher_series,"acc_id"=>$value['billsundry_id'],"acc_type"=>"bsd","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
									 $this->TransactionModel->save_sale_memeo_txns($memo_data);
									 $this->TransactionModel->update_account_memo_balance($value['billsundry_id'],'bsd');
									}
								}
									/**** End Save MEMO TXN *****/
										      if($value['billsundry_amount'] >= 0)
										      		$billsundry_drcr = 'c';
										      else
										      	  $billsundry_drcr = 'd';

							           $insert_data  = [
														'comp_id'            	   => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => abs($value['billsundry_amount']),
														'acc_oth_txn_fcy'        => ($currency_id>1)?abs($value['billsundry_amount'])*$fcy_forex_rate:0,						
														'acc_oth_txn_drcr'       => $billsundry_drcr,
														'voucher_type_id'    	   => $voucher_type_id,
														'comp_vch_series_no' 	   => $voucher_series,
														'acc_id'             	   => $value['billsundry_id'],						
														'voucher_txn_id'     	   => $voucher_txn_id,
														'acc_oth_txn_status' 	   => "",
														'bo_id'					   => $this->session->get('ses_boid'),
														'txn_id'				         => $txn_id,
														'acc_oth_txn_duedate' 	 => date('Y-m-d'),
														'acc_oth_txn_tag'		     => 'BILSDRY',
												];
												$this->TransactionModel->add_acc_oth_data($insert_data);
								} }
							    }

							    // return redirect()->to($this->base_url.'quotations/item');
					     }
					     if($type == 'non_item')
					     {
							 $isdplc_vch =  $this->request->getVar('isdplc_vch');
			if($isdplc_vch==1){	
				if($vch_subtype_id == 0)	
                  echo $this->non_item();			
			    else 
				echo $this->item();
				die();
			} 		
							 //Update Eway Tables Data
			    	$ewbmstreqn_upd_data = array("ewb_supply_type"=>$supply_type,"ewb_sub_supply_desc"=>$ewb_sub_supply_desc,
				                               "ewb_doc_type"=>"","ewb_txn_type"=>0,"bo_id"=>$bo_id);
		           $this->TransactionModel->update_ewbmstreqn_data($voucher_txn_id,$ewbmstreqn_upd_data);
		       
			   
					     		 $sale_total = array_sum(array_column($itmsdata, 'amount'));
									 $billsundry_total = 0;
									 if(count($billsndrydata))
									 		$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
									 

									 // add voucher consolidated entry
									 $voucher_txn_data     = array(
									 			"comp_vch_series_id"	=> $voucher_series,
										    "voucher_date"				=> $voucher_date,
										    "mat_cent_id"					=> 0,
										    "voucher_tag"					=> '',
										    "currency_id"				  => $currency_id,
										  );
									 $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$voucher_txn_data);

									 // delete comp txn master
									 $this->TransactionModel->delete_taxinc_txn_data($voucher_txn_id,'acc');
									 $this->TransactionModel->delete_memo_txn_data($voucher_txn_id);
									 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
									 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
									 $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
									 $this->TransactionModel->delete_all_narrations($voucher_txn_id);
									 $this->TransactionModel->delete_acctgstsum_data($voucher_txn_id);	
					                 $this->TransactionModel->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
									if($isvoucher_autobillno==1){
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				  $this->TransactionModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
				}
				else{
				  $gstroutsup_insert_data = array("outsup_rev_chg"=>$reverse_charges,"outsup_bill_ref_no"=>$billno,"voucher_txn_id"=>$voucher_txn_id,"outsup_pos"=>$pos,"outsup_inv_type"=>$invoice_type,"outsup_eco"=>$outsup_eco);
				  $this->TransactionModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
				  }		

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
				    if(isset($rdata['tax_cat_id']))
					$item_tax_short_code = $this->TransactionModel->GetItemTxShrtCd($rdata['tax_item_id_val'],$rdata['tax_cat_id']);		
					else
					$item_tax_short_code ='';
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
									'acc_cess_rate'        => parseAmount($rdata['cess_rate_val']),
									'acc_cess'             => parseAmount( $rdata['cess_val']),
									'acc_nonadv_cess_rate' => 0,
									'acc_nonadv_cess'      => 0,
									'acc_hsn_sac'          => $rdata['tax_hsn_sac_val'],
									'vch_txn_id'           => $voucher_txn_id,
									'txn_id'               => $oth_txn_id,
									'acc_type'             => 'acc',
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
					 if($bo_state_code!=$pos){
						$acc_igst_rate = parseAmount($rdata['tax_rate_val']);
						$acc_igst      = parseAmount($rdata['igst_val']);
						$acc_igst_fcy  = parseAmount($rdata['igst_fcy_val']);
						$acc_cgst_rate = $acc_cgst=$acc_sgst_rate=$acc_sgst=$acc_cgst_fcy=$acc_sgst_fcy=$acc_cess_fcy=0;
					 }
					 else{
						$acc_igst_rate = $acc_igst=$acc_igst_fcy=0;
						$acc_cgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_cgst_fcy  = parseAmount($rdata['cgst_fcy_val']);
						$acc_cgst      = parseAmount($rdata['cgst_val']);
						$acc_sgst_rate = parseAmount($rdata['tax_rate_val'])/2;
						$acc_sgst      = parseAmount($rdata['sgst_val']);
						$acc_sgst_fcy  = parseAmount($rdata['sgst_fcy_val']);
					   }
					$bsd_txn_id = $this->TransactionModel->add_comp_txn_data($insert_data);				
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
									
									);
					      $this->TransactionModel->add_taxsummary_data($bsd_insert_data);
						  if($currency_id >1 && $fcy_forex_rate!=''){
							  $total_tax_fcy =  $rdata['total_tax_fcy_val'];
					         $acctgstfcy_insert_data = array("acc_id"=>$rdata['tax_item_id_val'],"vch_txn_id"=>$voucher_txn_id,
													  "txn_id"=>$bsd_txn_id,"acc_igst_fcy"=>$acc_igst_fcy,
													  "acc_cgst_fcy"=>$acc_cgst_fcy,"acc_sgst_fcy"=>$acc_sgst_fcy,
													  "acc_cess_fcy"=>$acc_cess_fcy,"total_tax_fcy"=>$total_tax_fcy,
													  "acc_type"=>"bsd" );
					    $this->TransactionModel->add_acctgstfcy_data($acctgstfcy_insert_data);
					  }
				       }
				
				
				    }
				}
				/********** End Tax Summary Code   **********/ 
									 // Add party oth txn entry 
							     $insert_data  = [
														'comp_id'            		 => $this->company_id,
														'acc_oth_txn_date'       => $voucher_date,
														'acc_oth_txn_amount'     => ($sale_total+$billsundry_total),
														'acc_oth_txn_fcy'        => ($currency_id>1)?($sale_total+$billsundry_total)*$fcy_forex_rate:0,						
														'acc_oth_txn_drcr'       => 'd',
														'voucher_type_id'    		 => $voucher_type_id,
														'comp_vch_series_no' 		 => $voucher_series,
														'acc_id'             		 => $party_id,						
														'voucher_txn_id'     		 => $voucher_txn_id,
														'acc_oth_txn_status' 		 => $voucher_no,
														'bo_id'						 => $this->session->get('ses_boid'),
														'txn_id'					 => $txn_id,
														'acc_oth_txn_duedate' 	     => $due_date,
														'acc_oth_txn_tag'			 => $oth_txn_tag,
												];
										$this->TransactionModel->add_acc_oth_data($insert_data);
 /**********  Memo Account Entry  ************/
					if($sale_memo_total >0){
					$insert_data  = [
								'comp_id'            => $this->company_id,
								'acc_id'             => $party_id,
								'acc_txn_date'       => $voucher_date,
								'acc_txn_amount'     => ($sale_memo_total + $billsundry_memo_total),
								'acc_txn_fcy'        => ($currency_id>1)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0,						
								'acc_txn_drcr'       => 'd',
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
										foreach($itmsdata as $item_row)
										{
/**** Save MEMO TXN *****/
				if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){		
				 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$item_row['memo_amount'],"acc_txn_drcr"=>"c","comp_vch_series_no"=>$voucher_series,"acc_id"=>$item_row['account_id'],"acc_type"=>"acc","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
				 $this->TransactionModel->save_sale_memeo_txns($memo_data);
				}
											$txn_data = array(
												"comp_id"							=> $this->company_id,
												"comp_vch_series_id"	=> $voucher_series,
												"voucher_txn_id" 			=> $voucher_txn_id,
												"master_id" 					=> $item_row['account_id'],
												'master_id_type' 			=> 'aco'
										 	);
					           $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);	 
				 if($taxInclusive=="1" && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
				      $insert_data   = array(
					        'txn_id'              => $txn_id,
							'voucher_txn_id'      => $voucher_txn_id,
							'vch_txn_drcr'        => 'c',
							'vch_txn_incl'        => $item_row['txinc_amount'],
							'txn_type'            => 'acc'
					        );	
					$this->TransactionModel->add_taxinclusive_txn_data($insert_data);
				    }
								     $insert_data  = [
															'comp_id'            	 => $this->company_id,
															'acc_oth_txn_date'       => $voucher_date,
															'acc_oth_txn_amount'     => $item_row['amount'],
															'acc_oth_txn_fcy'        => ($currency_id >1)?$item_row['amount']*$fcy_forex_rate:0,						
															'acc_oth_txn_drcr'       => 'c',
															'acc_oth_txn_narr'       => $item_row['description'],
															'voucher_type_id'    		 => $voucher_type_id,
															'comp_vch_series_no' 		 => $voucher_series,
															'acc_id'             		 => $item_row['account_id'],						
															'voucher_txn_id'     		 => $voucher_txn_id,
															'acc_oth_txn_status' 		 => $voucher_no,
															'bo_id'						=> $this->session->get('ses_boid'),
															'txn_id'								 => $txn_id,
															'acc_oth_txn_duedate' 	 => $due_date,
															'acc_oth_txn_tag'				 => $oth_txn_tag,
													];
											$this->TransactionModel->add_acc_oth_data($insert_data);
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


								    if(count($billsndrydata))
								    {
												foreach($billsndrydata as $value)
												{
													if(isset($value['billsundry_id']) && $value['billsundry_id']>0){	
								
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
											$billsundry_memo_drcr = 'c';
										else
											$billsundry_memo_drcr = 'd';	 
									 $memo_data = array("comp_id"=>$this->company_id,"acc_txn_date"=>$voucher_date,"acc_txn_amount"=>$value['memo_amount'],"acc_txn_drcr"=>$billsundry_memo_drcr,"comp_vch_series_no"=>$voucher_series,"acc_id"=>$value['billsundry_id'],"acc_type"=>"bsd","acc_bal"=>0,"voucher_txn_id"=>$voucher_txn_id,"bo_id"=>$bo_id);
									 $this->TransactionModel->save_sale_memeo_txns($memo_data);
									 $this->TransactionModel->update_account_memo_balance($value['billsundry_id'],'bsd');
									}
								}
										 /**** End Save MEMO TXN *****/
											      if($value['billsundry_amount'] >= 0)
											      		$billsundry_drcr = 'c';
											      else
											      	  $billsundry_drcr = 'd';

								           $insert_data  = [
															'comp_id'            	   => $this->company_id,
															'acc_oth_txn_date'       => $voucher_date,
															'acc_oth_txn_amount'     => abs($value['billsundry_amount']),
															'acc_oth_txn_fcy'        => ($currency_id>1)?abs($value['billsundry_amount'])*$fcy_forex_rate:0,						
															'acc_oth_txn_drcr'       => $billsundry_drcr,
															'voucher_type_id'    	   => $voucher_type_id,
															'comp_vch_series_no' 	   => $voucher_series,
															'acc_id'             	   => $value['billsundry_id'],						
															'voucher_txn_id'     	   => $voucher_txn_id,
															'acc_oth_txn_status' 	   => "",
															'bo_id'					   => $this->session->get('ses_boid'),
															'txn_id'				         => $txn_id,
															'acc_oth_txn_duedate' 	 => date('Y-m-d'),
															'acc_oth_txn_tag'		     => 'BILSDRY',
													];
													$this->TransactionModel->add_acc_oth_data($insert_data);
									}}
								    }

							    // return redirect()->to($this->base_url.'quotations/non_item');
					     }
					     return json_encode(['status' => true, 'message' => 'Voucher Updated']);
			   	}
		   	}

				$data['isvoucher_autobillno']       = $isvoucher_autobillno;
             $data['states_lists']               = $this->TransactionModel->show_states_lists();
             $data['transport_modes']            = transport_modes();
             $data['vehicle_types']              = vehicle_types(); 			 
			 $data['transporter_dropdown']       = $this->TransactionModel->transporter_dropdown();			 
			 $data['gstroutsup_info']            = $this->TransactionModel->gstroutsup_info($voucher_txn_id);
			 $data['transport_edit_id']          = $this->TransactionModel->transport_single_info($voucher_txn_id);
			  // Get Forex Currency Rate		
			 $data['forexcrncy_rate']            = $this->TransactionModel->get_crsref($voucher_txn_id,'FOREXRT');
			  
			 $data['ismemosale']                 = $ismemosale;
			 $data['istaxinc']                   = $istaxinc;
			 $data['voucher_type_id']            = $voucher_type_id;
			 $data['InvoiceTypeList']                = InvoiceTypeList();
$data['billfrm_data']                   = $this->TransactionModel->billfrm_info();
			
				$party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
				$data['party_id'] 					= $party_transaction['acc_id'];
				$data['due_date'] 					= $party_transaction['acc_oth_txn_duedate'];
				$data['narration'] 					= $party_transaction['acc_oth_txn_narr'];

				$data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
				$data['message_output']   				= $this->message_output;
				$data['base_url']         				= $this->base_url; 
				$data['voucher_auto_no']  				= $this->TransactionModel->get_voucher_no($voucher_type_id);
				$data['folder_path']      				= $this->folder_path;	
				

				if($data['item_transactions']){
				 $data['matrcntr_id'] 				= $voucher_info['mat_cent_id'];
					$data['item_batch_data'] = $this->TransactionModel->get_item_batch_data($voucher_txn_id);
					$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);

					$data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
					$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);
					$data['sundry_comp_transactions']='';
					if($this->session->get('bo_gstin_type')=="2"){				
				     $vouchertxn_id   = $this->TransactionModel->get_vchr_txnid($voucher_txn_id);
					 $data['sundry_comp_transactions'] = $this->TransactionModel->get_sundry_transactions($vouchertxn_id, true);
                    }
					$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);
					$data['pr_data'] = $this->TransactionModel->get_pr_txn_data($voucher_txn_id);
					$data['get_narration_info']     = $get_narration_info;  

					$data['message_output']   = $this->message_output;
					$data['base_url']       	= $this->base_url; 
					$data['folder_path']    	= $this->folder_path;
					$data['party_dropdown']   = $this->TransactionModel->party_dropdown();
					$data['matrcntr_dropdown']  			= $this->TransactionModel->matrcntr_dropdown();
					$data['voucher_series_dropdown']  = $this->TransactionModel->voucher_series_dropdowns($voucher_type_id);
					$data['items_list']     					= $this->TransactionModel->items_list();	
					$data['units_list']     					= $this->TransactionModel->units_dropdown();
					
					
					$data['voucher_series']     = $voucher_info['comp_vch_series_id'];
					
					$data['voucher_date'] 			= date('d-m-Y', strtotime($voucher_info['voucher_date']));
					$data['voucher_txn_id']     = $voucher_txn_id;
					$data['bills_method_list']				= ['','New Ref.','Adjustment'];
                    
					$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
					$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
					
					$data['item_json_file']                 = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
                    $bsd_json_file = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
            	   if($bsd_json_file)
            	   $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
                   else
            		$data['bsd_json_file']                   ='[]';  
                    $data['voucher_type_id']                = $voucher_type_id;
                    // $data['currency'] = $this->TransactionModel->get_currency($voucher_info['currency_id']);
                    $data['currency_list'] = $this->TransactionModel->get_currency_list();
            		$data['currency_id'] = $voucher_info['currency_id'];
					$data['bo_state_code'] = $bo_state_code;
					$data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,$vch_subtype_id);
					$data['supply_type_list']    = $this->SupplyTypes;
	               	$data['tax_type_list']       = $this->TXNTypesList;
					$data['voucher_type_id']     = $voucher_type_id;
	               	$data['get_ewbmstreqn_data'] = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
					$data['billfrm_data']                   = $this->TransactionModel->billfrm_info();
					$data['bo_gstin_type']                 = $this->session->get('bo_gstin_type');
					$data['eco_dropdown']                   = $this->TransactionModel->show_eco_lists();
						
					
					
				}
				else{
				 
				$data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
				$data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

				$data['bbb_data'] = $this->TransactionModel->get_bills_txn_data($voucher_txn_id);
				$data['pr_data'] = $this->TransactionModel->get_pr_txn_data($voucher_txn_id);
				$data['item_tracking_data'] = $this->TransactionModel->get_item_tracking_data($voucher_txn_id);

				$data['message_output']   = $this->message_output;
				 $data['get_narration_info']     = $get_narration_info;  
				$data['base_url']       	= $this->base_url; 
				$data['folder_path']    	= $this->folder_path;
				$data['party_dropdown']   = $this->TransactionModel->party_dropdown();
				$data['voucher_series_dropdown']  = $this->TransactionModel->voucher_series_dropdowns($voucher_type_id);

				$data['voucher_series']     = $voucher_info['comp_vch_series_id'];				
				$data['voucher_date'] 			= date('d-m-Y', strtotime($voucher_info['voucher_date']));
				$data['voucher_txn_id']     = $voucher_txn_id;
				$data['bills_method_list']				= ['','New Ref.','Adjustment'];
				$data['voucher_type_id']                = $voucher_type_id;
				// $data['currency'] = $this->TransactionModel->get_currency($voucher_info['currency_id']);
				$data['currency_list'] = $this->TransactionModel->get_currency_list();
            	$data['currency_id']   = $voucher_info['currency_id'];
				$data['bo_state_code'] = $bo_state_code;
				$acc_file_name    = 'acc'.$this->session->get('ses_comp_fy_id').'.json';
                $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

					$data['accounts_json_file']       = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$acc_file_name);
            $bsd_json_file = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
        	   if($bsd_json_file)
        	     $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
               else
        		$data['bsd_json_file']                   ='[]';  
				 $data['tax_summary']   = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);

				$data['supply_type_list']     				= $this->SupplyTypes;
	        	$data['tax_type_list']                      = $this->TXNTypesList;
				$data['transport_modes']                = transport_modes();
				$data['vehicle_types']                  = vehicle_types();
				$data['transporter_dropdown']           = $this->TransactionModel->transporter_dropdown();
	        	$data['get_ewbmstreqn_data'] = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
				
				$data['bo_gstin_type']                 = $this->session->get('bo_gstin_type');
				$data['sundry_comp_transactions']='';
					if($this->session->get('bo_gstin_type')=="2"){				
				     $vouchertxn_id   = $this->TransactionModel->get_vchr_txnid($voucher_txn_id);
					 $data['sundry_comp_transactions'] = $this->TransactionModel->get_sundry_transactions($vouchertxn_id, true);
                    }
				$data['eco_dropdown']                   = $this->TransactionModel->show_eco_lists();
					
					
				}	
				

				if(!empty($data['item_transactions'])) 
		    	return view($this->folder_path.'quotations/edit_item',$data);
        else
		    	return view($this->folder_path.'quotations/edit_non_item',$data);
	
		}

		public function delete($voucher_txn_id)
		{
				$voucher_type_id    = "17";

				$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
				if(!empty($voucher_info)){
					 $this->TransactionModel->delete_acc_oth_data($voucher_txn_id);
					 $this->TransactionModel->delete_acc_crsref_data($voucher_txn_id);
					 $this->TransactionModel->delete_itm_oth_data($voucher_txn_id);
					 $this->TransactionModel->delete_comp_txn_data($voucher_txn_id);
					 $this->TransactionModel->delete_voucher_conso_data($voucher_txn_id);
					 $this->TransactionModel->delete_all_narrations($voucher_txn_id);
					 $this->TransactionModel->delete_acctgstsum_data($voucher_txn_id);
				}

				// return redirect()->to($this->base_url.'quotations/item');
				return json_encode(['status' => true, 'message' => 'Voucher Deleted']);
		}
}