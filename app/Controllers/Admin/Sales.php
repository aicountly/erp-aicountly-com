<?php
namespace App\Controllers\Admin;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Traits\TransactionTrait;

class Sales extends BaseController{ 
  use TransactionTrait;
  function __construct(){   
	helper(['form', 'url','text','custom']);
	$this->AccountsModel     = new AccountsModel();
	$this->LogModel          = new ERPLogModel();
	$this->CommonModel       = new CommonModel();
	$this->VouchersModel     = new VouchersModel();
	$this->auth_session      = new auth_session();
	$this->auth_session->user_restrict();
	$this->auth_session->is_company_opened();
	$this->auth_session->role_restrict('CS');
	$this->base_url      = base_url().getenv('AdminPath');
	$this->folder_path   = getenv('AdminPath');
	$this->session    	 = \Config\Services::session();
	$this->bo_id         = $this->session->get('ses_boid');
	$this->fy_id         = $this->session->get('ses_comp_fy_id');
	$this->company_id    = $this->session->get('ses_company_id');
	$this->comp_code     = $this->session->get('ses_company_code');
	$this->ugst_states   = ['35','04','26','25','31','38','34','97'];
	$this->sale_types    = [ 8  => 'Sales With Stock Outward',];
	$this->TXNTypesList    =  TXNTypesList();
	$this->TXNTypesListWO  =  TXNTypesWOList();
	$this->compositionSupplyOptions = [
            ''  => 'Choose Composition Supply',
            '1' => 'MANUFACTURER',
            '2' => 'TRADER',
            '3' => 'RESTAURANT SERVICES',
            '4' => 'OTHER SERVICES'
        ];
	
	/*
    $this->is_valid_url     = validate_web_url(current_url())['allowed'];
	if (!$this->is_valid_url) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
     }   */	
	 
	 
    }
  public function item(){
	$voucher_type_id           = 18;
	$taxes_list                = $this->VouchersModel->GetGSTTaxesList();
	$Voucher_TxnApproval_Info  = $this->VouchersModel->Voucher_TxnApproval();
	$bo_state_code             = $this->session->get('ses_bostecd');
	$ugst_states               = $this->ugst_states;
	$Voucher_TxnApproval       = $Voucher_TxnApproval_Info['approval_amt'] ?? '';
	$Voucher_TxnApproval_UUID  = $Voucher_TxnApproval_Info['uuid_to'] ?? 0; // who will approve, reject voucher
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
	 /* if($_SERVER['REMOTE_ADDR']=='103.172.223.178' || $_SERVER['REMOTE_ADDR']=='103.172.223.179')
	  echo "<pre>";print_r($_POST);exit;  */
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
			 ];
			
	    if(!$this->validate($rules)){
	      	$errors = $this->validator->getErrors();
	       	return json_encode(['status' => false,'vchrtxnid'=>'', 'message' => 'Validation Error', 'errors' => $errors]);
	    }
		$trans_result = $this->runTransaction(function($db) use ($bo_state_code,$ugst_states,$taxes_list,$voucher_type_id,$Voucher_TxnApproval_UUID,$Voucher_TxnApproval)
		{
		 try{		
		    $gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);
			$sale_type           = $this->request->getVar('sale_type');
			$voucher_date        = $this->request->getVar('voucher_date');
			$voucher_date 	     = validate_date_by_fy($voucher_date);
			$voucher_series      = $this->request->getVar('voucher_series'); 
			$party_id            = $this->request->getVar('party_id');  
			$long_narration      = $this->request->getVar('narration');


            $sbnum      = $this->request->getVar('sbnum'); 
			$sbpcode    = $this->request->getVar('sbpcode');  
			$sbdt       = $this->request->getVar('sbdt');

			
			$itmsdata            = json_decode($this->request->getVar('itmsdata'),true); 
			
			
			$billsndrydata       = json_decode($this->request->getVar('billsndrydata'),true);
			$itemsbatchdata      = json_decode($this->request->getVar('itemsbatchdata'),true); 
			$currency_id 	     = $this->request->getVar('currency_id');			 
			$billno 		     = $this->request->getVar('billno');
			$supply_type         = $this->request->getVar('supply_type');
			// EWB fields
			$ewb_supply_type     = $this->request->getVar('supply_type');
			$ewb_sub_supply_type = $this->request->getVar('tax_type_id');
			$ewb_sub_supply_desc = '';//$this->request->getVar('supply_type_desc');			
			$EinvoiceCheck        = $this->request->getVar('EINVOICECheck');		
			
			$reverse_charges     = $this->request->getVar('reverse_charges');
			$pos                 = $this->request->getVar('pos');
			$fcy_forex_rate      = $this->request->getVar('fcy_forex_rate');
			$memoCheck           = $this->request->getVar('memoCheck');			
			$taxInclusive        = $this->request->getVar('taxInclusive');
			$outsup_eco          = $this->request->getVar('eco_id') ?? 0;
			$matrcntr_id         = $this->request->getVar('matrcntr_id'); 
            $tax_required_flag   = $this->request->getVar('tax_required_flag');
			$cmp_supply_type     = $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null	
			$btnid               = $this->request->getVar('btnid');// submitbtn default save button
			$isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			   if($billno=="")
				return ['status' => false, 'vchrtxnid'=>'','message' => 'Validation Error', 'errors' => ['Bill No. is required']];
				$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'sale');
				if($billno_duplicate=="1")
				return ['status' => false, 'vchrtxnid'=>'','message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']];
			}
			
			$batchdata = [];
		  if(!empty($this->request->getVar('itemsbatchdata')))
			$batchdata = json_decode($this->request->getVar('itemsbatchdata'),true);
		 
		 
		 $bbbdata = [];
		  if(!empty($this->request->getVar('bbbdata')))
			$bbbdata = json_decode($this->request->getVar('bbbdata'),true);
		 
		  $prdata = [];
		  if(!empty($this->request->getVar('prdata')))
			$prdata = json_decode($this->request->getVar('prdata'),true);
		
		$is_batch=FALSE;
		 if($batchdata)
		 $is_batch=TRUE;
	 
	 
		$is_bbb=FALSE;
		 if($bbbdata)
		 $is_bbb=TRUE;			
		 
		 $is_pr=FALSE;
		  if($prdata)
		   $is_pr=TRUE;
	       $vch_particulars='';
		    $party_is_sez =false;
			$party_has_gstin=false;
            if($party_id){
              $acc_info = $this->VouchersModel->account_detail_info($party_id);
			  $vch_particulars = $acc_info['acc_name']; 
			  $party_is_sez    = ($acc_info['acc_is_sez']==1)?true:false;
			  $party_has_gstin = ($acc_info['acc_gstin']!='')?true:false;
            }
			if($vch_particulars=='')
		    return ['status' => false, 'message' => 'Party field is not valid']; 
		
		     // Mark Dirty Transactions
		    $voucherDate = new \DateTime($voucher_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				//$this->VouchersModel->markSnapshotDirty($voucher_date);
			 }
			
			$voucher_no                = $this->VouchersModel->get_voucher_no($voucher_type_id);
			$sale_total                = array_sum(array_column($itmsdata, 'item_total_amount'));
			$sale_fcy_total            = array_sum(array_column($itmsdata, 'item_total_fcy_amount'));
			$billsundry_total          = array_sum(array_column($billsndrydata, 'billsundry_amount'));
			$billsundry_fcy_total      = array_sum(array_column($billsndrydata, 'billsundry_fcy_amount'));
			$sale_memo_total           = array_sum(array_column($itmsdata, 'memo_amount'));
			$billsundry_memo_total     = array_sum(array_column($billsndrydata, 'memo_amount'));
			$sale_fcy_memo_total       = 0;
			$billsundry_fcy_memo_total = 0;			
			$total_tax_amount          = 0;
			$total_fcy_tax_amount      = 0;
			
			
		    if($tax_required_flag==1){
	    	$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm')['fcy_tax_amount'];
	        
	        $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['fcy_tax_amount'];
			}
	        
		$voucherdata     = json_encode(["is_batch"=>$is_batch,"batchdata"=>$batchdata,"bbbdata"=>$bbbdata,"prdata"=>$prdata,"is_batch"=>$is_batch,"mat_cent_id"=>$matrcntr_id,"itmsdata"=>$itmsdata,"billsndrydata"=>$billsndrydata]);
	    $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                       "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					           "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($sale_total+$billsundry_total),"vch_fcy_amt"=>parseAmount($sale_fcy_total+$billsundry_fcy_total),"vch_fcy_rate"=>(float) $fcy_forex_rate ?? 0,
							   "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
							   "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
							   "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
					            );
	    $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
	    if($btnid=='submitbtn_drft'){
		  return ['status' => true,'vchrtxnid'=>'',  'message' => 'Voucher saved in draft mode']; 
	     }
		 
		 /************    Sales With Stock Outword  *****************/
		 
		if($sale_type == 8){
			
			if($isvoucher_autobillno==0){
			   $outsup_bill_ref_no =$billno;
			}
			else if($isvoucher_autobillno==1){			
			  $outsup_bill_ref_no = $this->VouchersModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,1,'counter');
			}
			
			$voucher_invoice_value = $sale_total + $billsundry_total + $total_tax_amount;			
		    $voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;				
			$voucher_memo_value = $sale_memo_total + $billsundry_memo_total;
			$voucher_fcy_memo_value = (parseAmount($fcy_forex_rate) >0)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0;
				
				
			/******* start of  if gstinType ==2 means composition then insert new voucher type 23  ********/
			if($gstinType==2){
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$cmp_supply_type)['fcy_tax_amount'];
			
				$comp_vch_type_id =23;
				$voucher_series_id =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				$insert_data  = array(
				  "cmp_id"           => $this->company_id,		  
				  "vch_series_id"    => $voucher_series_id,          
				  "vch_type_id"      => $comp_vch_type_id,
				  "vch_sub_type_id"  => 0,		   
				  "vch_date"         => $voucher_date,
				  "mat_cent_id"      => $matrcntr_id,
				  "draft_vch_rec_id" => $draft_vch_rec_id
				 );		
			  $comp_voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data);
			  $insert_data = [
					  "cmp_id"          => $this->company_id,
					  "vch_series_id"   => $voucher_series_id,
					  "vch_txn_id"      => $comp_voucher_txn_id,
					  "master_id"       => $gstpaidacc_id,
					  'master_id_type'  => 'acc'
					 ];
			  $comp_main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			  
			  $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series_id,
				  "vch_txn_id"      => $comp_voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($comp_voucher_txn_id,$comp_main_txn_id,'long',$long_narration);
			  
			  // now save txn entry to GSTIN PAID ACCOUNT, we will create this account at the time of company creation
			  $insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $gstpaidacc_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => $total_tax_amount, 
					  'acc_txn_fcy'       => $total_fcy_tax_amount,
					  'vch_txn_id'        => $comp_voucher_txn_id,
					  'txn_id'            => $comp_main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => 1
					];  				
					$this->VouchersModel->add_acc_txn_data($insert_data);		

				$long_register_insert = [
						'acct_vch_type'   => $comp_vch_type_id,
						'cmp_id'          => $this->company_id,
						'vch_txn_id'      => $comp_voucher_txn_id,
						'txn_id'          => NULL,
						'vch_date'        => $voucher_date,					
						'acc_id'          => $gstpaidacc_id,
						'acc_txn_dr_amt'  => $total_tax_amount,
						'acc_txn_cr_amt'  => 0,
						'vch_narr'        => $long_narration ?? '',
						'hobo_id'         => $this->bo_id,
						'acc_txn_type'    => 1
					];
					$this->VouchersModel->add_register_txn_data($long_register_insert);	
		     	
					
			}
			/******* end of  if gstinType ==2 means composition **********/
			
			
			
			
			
			 $insert_data  = array(
				  "cmp_id"           => $this->company_id,		  
				  "vch_series_id"    => $voucher_series,          
				  "vch_type_id"      => $voucher_type_id,
				  "vch_sub_type_id"  => $sale_type,		   
				  "vch_date"         => $voucher_date,
				  "mat_cent_id"      => $matrcntr_id,
				  "draft_vch_rec_id" => $draft_vch_rec_id
				 );		
			  $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data);
				// make entry in baridge table if composition is set as 2 
                if($gstinType==2){ 
					$brdge_insert_data  = array(
									  "cmp_id"           => $this->company_id,		  
									  "vch_bridge_type"    => 4,          
									  "vch_txn_id_src"      => $voucher_txn_id,
									  "vch_txn_id_dest"  => $comp_voucher_txn_id
									 );									 
					$this->VouchersModel->add_vchbridgen_txn_data($brdge_insert_data);
				}
			 if(parseAmount($fcy_forex_rate) >0){
				$fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
								   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
								  );	 
				$this->VouchersModel->save_voucher_fcyrate($fcy_data);	
				}
				

				if($isvoucher_autobillno==0){
				 $gstroutsup_insert_data = array("cmp_id"=> $this->company_id,"vch_txn_id"=>$voucher_txn_id,
												"outsup_pos"=>$pos,"outsup_bill_ref_no"=>$outsup_bill_ref_no,"outsup_rev_chg"=>$reverse_charges,
												"dr_note_inwsup_id"=>(int)0,"outsup_eco"=>(int)$outsup_eco,"gst_supply_type"=>(int)$cmp_supply_type,
												"exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode
												);
				 $this->VouchersModel->add_gstroutsup_data($gstroutsup_insert_data);
				}
				else if($isvoucher_autobillno==1){
				 $gstroutsup_insert_data = array("cmp_id"=> $this->company_id,"vch_txn_id"=>$voucher_txn_id,
												"outsup_pos"=>$pos,"outsup_bill_ref_no"=>$outsup_bill_ref_no,"outsup_rev_chg"=>$reverse_charges,
												 "dr_note_inwsup_id"=>(int)0,"outsup_eco"=>(int)$outsup_eco,"gst_supply_type"=>(int)$cmp_supply_type,
												 "exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode
												);
				 $this->VouchersModel->add_gstroutsup_data($gstroutsup_insert_data);

				 
				}
				/***************  Start of EWB + E-Invoice *****************/
				$ewb_insert_data=["hobo_id"=>$this->bo_id,"cmp_id" =>$this->company_id,"ewb_no"=>"","ewb_date"=>$voucher_date,"ewb_supply_type"=>$ewb_supply_type,
								 "ewb_sub_supply_type"=>$ewb_sub_supply_type,"vch_txn_id"=>$voucher_txn_id,"hobo_id"=>$this->bo_id];
				$ewb_id  = $this->VouchersModel->add_ewb_data($ewb_insert_data);
				if($EinvoiceCheck){
					$einvoice_insert_data=["cmp_id" =>$this->company_id,"einv_no"=>0,"env_date"=>$voucher_date,"vch_txn_id"=>$voucher_txn_id];
					$einv_id = $this->VouchersModel->add_einvoice_data($einvoice_insert_data);
				   
				    $this->VouchersModel->add_ewb_txntype_data($ewb_sub_supply_type,$ewb_id,$einv_id,$voucher_txn_id,$_POST);
					$this->VouchersModel->add_transport_data($ewb_sub_supply_type,$ewb_id,$einv_id,$voucher_txn_id,$_POST);
				
				}
				
				
				/***************  End of EWB + E-Invoice *****************/
				
				
				/*********** Party Account Entries  *****************/
				  if($Voucher_TxnApproval!='' &&  parseAmount($voucher_invoice_value) > parseAmount($Voucher_TxnApproval)){
					 $acc_txn_type = 4; // voucher is pending for approval
					}
					 else
					 $acc_txn_type = 1;
					
					 $insert_data = [
					  "cmp_id"          => $this->company_id,
					  "vch_series_id"   => $voucher_series,
					  "vch_txn_id"      => $voucher_txn_id,
					  "master_id"       => $party_id,
					  'master_id_type'  => 'acc'
					 ];
					$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
					
					$insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $party_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => $voucher_invoice_value, 
					  'acc_txn_fcy'       => $voucher_fcy_invoice_value,
					  'vch_txn_id'        => $voucher_txn_id,
					  'txn_id'            => $main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => $acc_txn_type
					];  				
					$this->VouchersModel->add_acc_txn_data($insert_data);
				 /**********************************************************/
				 /********************* Start of Save Memorandum Entry **********************/
				 if($voucher_memo_value >0){
				 $memo_insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $party_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => $voucher_memo_value, 
					  'acc_txn_fcy'       => $voucher_fcy_memo_value,
					  'vch_txn_id'        => $voucher_txn_id,
					  'txn_id'            => $main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => 3
					];  				
					$this->VouchersModel->add_acc_txn_data($memo_insert_data);
				 }
					$long_register_insert = [
						'acct_vch_type'   => $voucher_type_id,
						'cmp_id'          => $this->company_id,
						'vch_txn_id'      => $voucher_txn_id,
						'txn_id'          => NULL,
						'vch_date'        => $voucher_date,					
						'acc_id'          => $party_id,
						'acc_txn_dr_amt'  => $voucher_invoice_value,
						'acc_txn_cr_amt'  => 0,
						'vch_narr'        => $long_narration ?? '',
						'hobo_id'         => $this->bo_id,
						'acc_txn_type'    => $acc_txn_type
					];
					$this->VouchersModel->add_register_txn_data($long_register_insert);
					
				 /*********************End of Save Memorandum Entry **********************/  
		   
			/*********************Start of Save Item Wise Grid Entries **********************/
		$itm_txn_type  = 1;	//1 for Regular
		$tax_igst_amt  = 0;
	    $tax_cgst_amt  = 0;	
	    $tax_sgst_amt  = 0;	
	    $tax_utgst_amt = 0;	
	    $tax_cess_amt  = 0;				
        if($itmsdata){			
			$item_account_array = [];
			$item_account_fcy_array = [];
        foreach($itmsdata as $item_row){
		  $inv_supply_id   = $item_row['supply_type_id'];
          $txn_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
          $main_txn_id  = $this->VouchersModel->add_comp_txn_data($txn_data);
		  /********************* Start of Save Memorandum Entry **********************/
				 if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){  
				 $memo_insert_data   = array(
					'cmp_id'             => $this->company_id,
					'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
					'itm_txn_qty'        => $item_row['item_qty'],
					'itm_txn_date'       => $voucher_date,
					'itm_txn_dr_cr'      => 2,
					'itm_txn_rate'       => 1,
					'itm_txn_amt'        => parseAmount($item_row['memo_amount']),
					'itm_txn_fcy'        => 0,
					'vch_txn_id'         => $voucher_txn_id,
					'txn_id'             => $main_txn_id,
					'mat_cent_id'        => $matrcntr_id,
					'hobo_id'            => $this->bo_id,
					'itm_txn_type'		 => 3		
					);	
				$this->VouchersModel->add_itm_txn_data($memo_insert_data);
				 }
		/********************* End of Save Memorandum Entry **********************/		
          if(isset($item_row['item_qty']) && $item_row['item_qty'] >0){
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 2,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => parseAmount($item_row['item_total_fcy_amount']),
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			 $this->VouchersModel->add_itm_txn_data($insert_data);

			 $narration            = $item_row['description'];
             $this->VouchersModel->save_voucher_narration($voucher_txn_id,$main_txn_id,'short',$narration);
			 /****************  Start of Save Tax Inclusive Data **********************/
						if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
						    $taxinc_insert_data  = [
                				  'cmp_id'            => $this->company_id,
                				  'acc_itm_id'        => $item_row['item_id'],
                				  'acc_txn_inc_amt'   => $item_row['txinc_amount'], 
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'acc_txn_dr_cr'     => 2
                				]; 								
                				$this->VouchersModel->add_taxinc_txn_data($taxinc_insert_data);
						    
						}
						/****************  End of Save Tax Inclusive Data **********************/					
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
            
			$account_id  =  $item_row['item_sales_acc'];
            if(isset($item_account_array[$account_id]))
            $item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
           else
            $item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);
		   if(isset($item_account_fcy_array[$account_id]))
			$item_account_fcy_array[$account_id] += parseAmount($item_row['item_total_fcy_amount']);
		   else
		   $item_account_fcy_array[$account_id] = parseAmount($item_row['item_total_fcy_amount']);
		
		 /*******************Start of Save TAX SUMMARY ****************/
			$tax_cat_id       = $item_row['tax_cat_id'];
			$tax_detail_rates = $item_row['tax_details'];

			$amount     = parseAmount($item_row['item_total_amount']);      // taxable base
			$amount_fcy = parseAmount($item_row['item_total_fcy_amount']);  // taxable base (FCY)
			$pos_code   = sprintf('%02d', $pos);

			// Item details
			$iteminfo = $this->VouchersModel->get_item_details_info($item_row['item_id']);
			$item_hsn = $iteminfo['itm_hsn'] ?? '';

			// ------------------------------------------------------------------
			// Effective rates (regular vs composition)
			// ------------------------------------------------------------------
			

			// Defaults (regular)
			$igst_rate_eff  = parseAmount($item_row['igst_rate']);
			$cgst_rate_eff  = $igst_rate_eff / 2;
			$sgst_rate_eff  = $igst_rate_eff / 2;
			$utgst_rate_eff = $igst_rate_eff / 2;
			$cess_rate_eff  = isset($tax_detail_rates['cess']) ? parseAmount($tax_detail_rates['cess']) : 0;

			// Override for composition (bo_gstin_type == 2) via helper
			if ((int)$gstinType === 2) {
				$compsrates = get_composition_rates($cmp_supply_type);
				$igst_rate_eff  = $compsrates['igst'];
				$cgst_rate_eff  = $compsrates['cgst'];
				$sgst_rate_eff  = $compsrates['sgst'];
				$utgst_rate_eff = $compsrates['ut'];
				$cess_rate_eff  = $compsrates['cess'] ?? 0;
			}

			// Default summary
			$taxsummary_data = [
				'cmp_id'             => $this->company_id,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'acc_bsd_id'         => $item_row['item_id'],
				'acc_bsd_type'       => 3,
				'vch_igst'           => 0,
				'vch_igst_rate'      => 0,
				'vch_cgst'           => 0,
				'vch_cgst_rate'      => 0,
				'vch_sgst_ugst'      => 0,
				'vch_sgst_ugst_rate' => 0,
				'vch_cess'           => 0,
				'vch_cess_rate'      => 0,
				'vch_taxable_value'  => $amount,
				'vch_total_tax'      => 0,
			];

			// Case 1: IGST applies (different states)
			if ($bo_state_code != $pos_code) {
				$igst_value = ($amount * $igst_rate_eff) / 100;

				$taxsummary_data['vch_igst']      = $igst_value;
				$taxsummary_data['vch_igst_rate'] = $igst_rate_eff;
				$taxsummary_data['vch_total_tax'] = $igst_value;
				$tax_igst_amt += $igst_value;

			}
			// Case 2: CGST + SGST applies (same state)
			elseif ($bo_state_code == $pos_code) {
				$cgst_value = ($amount * $cgst_rate_eff) / 100;
				$sgst_value = ($amount * $sgst_rate_eff) / 100;

				$taxsummary_data['vch_cgst']           = $cgst_value;
				$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
				$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
				$taxsummary_data['vch_sgst_ugst_rate'] = $sgst_rate_eff;
				$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

				$tax_cgst_amt += $cgst_value;
				$tax_sgst_amt += $sgst_value;
			}
			// Case 3: UGST applies
			elseif (in_array($pos_code, $ugst_states)) {
				$cgst_value = ($amount * $cgst_rate_eff) / 100;
				$ugst_value = ($amount * $utgst_rate_eff) / 100;

				$taxsummary_data['vch_cgst']           = $cgst_value;
				$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
				$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
				$taxsummary_data['vch_sgst_ugst_rate'] = $utgst_rate_eff;
				$taxsummary_data['vch_total_tax']      = ($cgst_value + $ugst_value);

				$tax_cgst_amt += $cgst_value;
				$tax_utgst_amt += $ugst_value;
			}

			// Always add cess if applicable (effective rate already set)
			if ($cess_rate_eff > 0) {
				$cess_value = ($amount * $cess_rate_eff) / 100;
				$taxsummary_data['vch_cess']      = $cess_value;
				$taxsummary_data['vch_cess_rate'] = $cess_rate_eff;
				$taxsummary_data['vch_total_tax'] += $cess_value;
				$tax_cess_amt += $cess_value;
			}

			$gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
				? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
				: ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);

			$taxsummary_data['vch_date']      = $voucher_date;
			$taxsummary_data['is_outward']    = 1;
			$taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
			$taxsummary_data['vch_hsn_sac']   = $item_hsn;
			$taxsummary_data['inv_supply_id'] = $inv_supply_id;

			$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

			$hsnummary_data = [
				'cmp_id'                => $this->company_id,
				'vch_txn_id'            => $voucher_txn_id,
				'txn_id'                => $main_txn_id,
				'vch_hsn_sac'           => $item_hsn,
				'vch_hsn_sac_taxbl_val' => $amount,
				'vch_hsn_sac_qty'       => $item_row['item_qty'],
				'vch_hsn_sac_uom'       => $item_row['item_unit_id'],
				'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
				'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
				'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
				'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
				'inv_supply_id'         => $inv_supply_id,
				'vch_gst_sum_id'        => $vch_gst_sum_id
			];
			$this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);

			/*********  FCY Tax Summary ****/
			if (parseAmount($fcy_forex_rate) > 0) {
				$taxsummary_fcy_data = [
					'cmp_id'                  => $this->company_id,
					'vch_txn_id'              => $voucher_txn_id,
					'txn_id'                  => $main_txn_id,
					'acc_bsd_id'              => $item_row['item_id'],
					'acc_bsd_type'            => 3,
					'vch_igst_fcy'            => 0,
					'vch_cgst_fcy'            => 0,
					'vch_sgst_ugst_fcy'       => 0,
					'vch_cess_fcy'            => 0,
					'vch_taxable_value_fcy'   => $amount_fcy,
					'vch_total_tax_fcy'       => 0
				];

				if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
					$igst_value = ($amount_fcy * $igst_rate_eff) / 100;
					$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
				}
				elseif ($bo_state_code == $pos_code) {
					$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;
					$sgst_value = ($amount_fcy * $sgst_rate_eff) / 100;

					$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
					$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;
				}
				elseif (in_array($pos_code, $ugst_states)) {
					$ugst_value = ($amount_fcy * $utgst_rate_eff) / 100;
					$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;

					$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] = $ugst_value;
				}

				if ($cess_rate_eff > 0) {
					$cess_value = ($amount_fcy * $cess_rate_eff) / 100;
					$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
				}

				$this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
			}
			/*******************End of Save TAX SUMMARY ****************/
			}
		  }
		foreach ($item_account_array as $account_id => $amount) {	
			if(isset($item_account_fcy_array[$account_id]))
			  $amount_fcy=$item_account_fcy_array[$account_id];
              else
		      $amount_fcy=0;	

			$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $account_id,
							  'master_id_type'  => 'acc'
							 ];
			$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $account_id,
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => 2,
							  'acc_txn_amt'       => $amount, 
							  'acc_txn_fcy'       => $amount_fcy,
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
			$this->VouchersModel->add_acc_txn_data($acc_txn_data);	
		 }
		  
	   } 
	   /*********************End of Save Item Wise Grid Entries **********************/
		/*********************Start of Save Billsundry Wise Grid Entries **********************/
			   if($billsndrydata) {
			        foreach($billsndrydata as $item_row) {
						/******************* Save MEMO TXN ****************/
						$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $item_row['billsundry_id'],
							  'master_id_type'  => 'bsd'
							 ];
						$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
						if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){	
						        $memo_txn_data  = [
								  'cmp_id'            => $this->company_id,
								  'acc_id'            => $item_row['billsundry_id'],
								  'acc_txn_date'      => $voucher_date,
								  'acc_txn_dr_cr'     => ($item_row['memo_amount']<0) ? 2 : 1,
								  'acc_txn_amt'       => $item_row['memo_amount'], 
								  'acc_txn_fcy'       => (parseAmount($fcy_forex_rate) >0)?($item_row['memo_amount']*$fcy_forex_rate):0,
								  'vch_txn_id'        => $voucher_txn_id,
								  'txn_id'            => $txn_id,
								  'hobo_id'           => $this->bo_id,
								  'acc_txn_type'      => 3
								];  				
								$this->VouchersModel->add_acc_txn_data($memo_txn_data);
						}
						/******************* Save Account TXN ****************/
						$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $item_row['billsundry_id'],
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => ($item_row['billsundry_amount']<0) ? 2 : 1,
							  'acc_txn_amt'       => $item_row['billsundry_amount'], 
							  'acc_txn_fcy'       => $item_row['billsundry_fcy_amount'],
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
							$this->VouchersModel->add_acc_txn_data($acc_txn_data);
							
										
                		    $narration            = "";
                		    $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
							
						
						 /*******************Start of Save TAX SUMMARY ****************/
						 $tax_cat_id              = $item_row['tax_cat_id'];
						 $tax_detail_rates        = $item_row['tax_details'];						 
						 $amount                  = parseAmount($item_row['billsundry_amount']); // taxable base
						 $amount_fcy              = parseAmount($item_row['billsundry_fcy_amount']); // taxable base
                         $pos_code                = sprintf( '%02d', $pos );
                         $billsundry_info = $this->VouchersModel->get_bsd_details_info($item_row['billsundry_id']);
						 $bsd_hsn         = $billsundry_info['bsd_hsn_sac'] ?? '';		
                         $taxsummary_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst'           => 0,
                            'vch_igst_rate'      => 0,
                            'vch_cgst'           => 0,
                            'vch_cgst_rate'      => 0,
                            'vch_sgst_ugst'      => 0,
                            'vch_sgst_ugst_rate' => 0,
                            'vch_cess'           => 0,
                            'vch_cess_rate'      => 0,
                            'vch_taxable_value'  => $amount,
                            'vch_total_tax'      => 0
                        ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                            $igst_value = ($amount * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_data['vch_igst']      = $igst_value;
                            $taxsummary_data['vch_igst_rate'] = $item_row['igst_rate'];
                            $taxsummary_data['vch_total_tax'] = $igst_value;
                            
                            $tax_igst_amt +=$igst_value;
                           
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_data['vch_cgst']           = $cgst_value;
                            $taxsummary_data['vch_cgst_rate']      = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_sgst_ugst']      = $sgst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;
                            
                            $tax_cgst_amt += $cgst_value;
                            $tax_sgst_amt +=  $sgst_value;
                            
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount * $item_row['igst_rate']/2) / 100;
							$cgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                            $taxsummary_data['vch_sgst_ugst']      = $ugst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $ugst_value;
                            
							$tax_cgst_amt += $cgst_value;
                            $tax_utgst_amt += $ugst_value;
                           
                        }
                    
                        // Always add cess if applicable
                        $cess_rate=0;
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
							$cess_rate  =  $tax_detail_rates['cess'];
                            $cess_value = ($amount * $tax_detail_rates['cess']) / 100;
                            $taxsummary_data['vch_cess']      = $cess_value;
                            $taxsummary_data['vch_cess_rate'] = $tax_detail_rates['cess'];
                            $taxsummary_data['vch_total_tax'] += $cess_value;
                            
                            $tax_cess_amt += $cess_value;
                           
                        }
						
						
						$taxsummary_data['vch_date']      = $voucher_date;
					    $taxsummary_data['is_outward']    = 1;				 
					    $taxsummary_data['gst_rate_grp']  = '1,'.parseAmount($item_row['igst_rate']).','.parseAmount($cess_rate);
					    $taxsummary_data['vch_hsn_sac']   = $bsd_hsn;
                        //echo '<pre>';
                       // print_r($taxsummary_data);
						 $vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data,$tax_required_flag);   
						 $hsnummary_data = [ 
							'cmp_id'                => $this->company_id,
							'vch_txn_id'            => $voucher_txn_id,
							'txn_id'                => $txn_id,
							'vch_hsn_sac'           => $bsd_hsn,
							'vch_hsn_sac_taxbl_val' => $amount,
							'vch_hsn_sac_qty'       => 0,
							'vch_hsn_sac_uom'       => 0,
							'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
							'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
							'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
							'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
							'inv_supply_id '        => 0,
							'vch_gst_sum_id'        => $vch_gst_sum_id
							];			  
				   $this->VouchersModel->add_hsnsummary_data($hsnummary_data,$tax_required_flag);  
						  if(parseAmount($fcy_forex_rate) >0 ){
						   // Default summary
                        $taxsummary_fcy_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst_fcy'       => 0,
                            'vch_cgst_fcy'       => 0,
                            'vch_sgst_ugst_fcy'  => 0,
                            'vch_cess_fcy'       => 0,
                            'vch_taxable_value_fcy'  => $amount_fcy,
                            'vch_total_tax_fcy'      => 0
                        ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            $igst_value = ($amount_fcy * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_fcy_data['vch_igst_fcy'] = $igst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_fcy_data['vch_cgst_fcy']           = $cgst_value;
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $sgst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $cgst_value + $sgst_value;
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
							$cgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $ugst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $ugst_value;
                        }
                    
                        // Always add cess if applicable
                       
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
                            $cess_value = ($amount_fcy * $tax_detail_rates['cess']) / 100;
                            $taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
                        } 
						      
						   $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);    
						  }
						  
						 /*******************End of Save TAX SUMMARY ****************/
					}
				}		 
		/*********************End of Save Billsundry Wise Grid Entries **********************/
			 
		/***************Start of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			 // if gstinType is 2 means Composition then no record will insert in taxable AccountsModel
			if($gstinType==1 ){
				 if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_utgst_amt,4,$voucher_date,2);
				   }	
				 if($tax_cess_amt > 0)		
				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cess_amt,5,$voucher_date,2);
		    } 
			if($gstinType==2 ){
				 if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_utgst_amt,4,$voucher_date,2);
				   }		    
				 if($tax_cess_amt >0)  
				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cess_amt,5,$voucher_date,2);
		    }
			 /*************** End of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			
			
			
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
			   
		    /***********   Save Bill By Bill Data  *************/
		if(count($bbbdata)){		
		 $this->VouchersModel->SaveBillByBillData($bbbdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		/***********   Save Project Reporting  Data  *************/
		if(count($prdata)){		
		 $this->VouchersModel->SaveProjectReportingData($prdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		/***********   Save Item Batch  Data  *************/
		if(count($batchdata)){		
		 $this->VouchersModel->SaveBatchData($batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
		}
		
		$this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	   $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	   /************ Save Voucher Activity Log **********/
	    $voucher_messages = [
			18  => "New sale with item voucher has been added by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			18  => "sale with item voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
		];
		$erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been added by [USERNAME]([UUID])";
		$this->VouchersModel->SaveUserActivity($erp_activity_log,$voucher_txn_id);
		
		return ['status' => true,'vchrtxnid'=>$voucher_txn_id,  'message' =>$trans_result['result']['message']];
	
		   }
		   
		  
	    }
		catch (\Throwable $e) {
					helper('error');
					$error = formatDbException($e);
					log_message('error', 'DB Error: ' . json_encode($error));
					return json_encode($error);
				} 
		});
		
		
          if(isset($trans_result['result']['status']) && $trans_result['result']['status']==''){            
					if(isset($trans_result['result']['errors']))
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>$trans_result['result']['errors']]);
					else
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>'']);
				}
		   else
         return $this->response->setJSON($trans_result);  
		
	 }
	$data['voucher_type_id']       = $voucher_type_id;
	$data['SuppltSubSupplyTypes']  = json_encode($this->VouchersModel->SuppltSubSupplyTypes());
	$data['message_output']        = $this->message_output;	
	$data['taxes_list']            = $taxes_list;	
	$data['base_url']              = $this->base_url; 
	$data['folder_path']           = $this->folder_path;
	$data['sub_type_dropdown']     = $this->sale_types;
	$data['ugst_states']           = $this->ugst_states;
	$data['tax_type_list']         = $this->TXNTypesList;
	$data['transport_modes']       = transport_modes();
    $data['vehicle_types']         = vehicle_types();
	$data['sale_against_challan']  = [];	
	$data['voucher_date']          = $this->VouchersModel->getLastVoucherDate($voucher_type_id);
 	$data['bo_gstin_type']         = $this->session->get('bo_gstin_type') ?? 1;// regular
	$data['sess_cmp_suply_type']         = $this->session->get('cmp_suply_type') ?? 0;// composition supply type
	$data['comp_supply_types_dropdown'] = $this->compositionSupplyOptions;
	$data['voucher_auto_no']       = $this->VouchersModel->get_voucher_no($voucher_type_id);
	$data['voucher_series']        = $this->VouchersModel->comp_voucher_result($this->company_id,$voucher_type_id);
	$data['party_dropdown']        = $this->VouchersModel->party_dropdown();
	$data['bills_method_list']     = ['','New Ref.','Adjustment'];	
	$data['currency_list']         = $this->VouchersModel->get_currency_list();
	$data['states_lists']          = $this->VouchersModel->show_states_lists();
	$data['eco_dropdown']          = $this->VouchersModel->show_eco_lists();
    $data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();	
		
	$data['matrcntr_dropdown']     = $this->VouchersModel->material_centre_dropdown();
	
	$data['bo_state_code']         = $this->session->get('ses_bostecd');
	$items_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
	$data['item_json_file']        = $items_list;
	$accounts_list                 = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	$bsd_accounts                  = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	if(!$bsd_accounts){
	
	    $bsd_accounts   =[];
	}
	$data['accounts_json_file']    = $accounts_list;
	
	$data['bsd_json_file']         = $bsd_accounts;	
	
	
	$data['units_list']     	   = $this->VouchersModel->units_grid();
    $data['ses_company_name']      = $this->session->get('ses_company_name');
	$data['supply_type_list']      =  SupplyTypesList();
	$data['tax_type_list']         = $this->TXNTypesList;
	$data['billfrm_data']          = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
	return view($this->folder_path.'sales/add_item',$data);	 
   }
   
  public function non_item(){
	$voucher_type_id           = 18;
	$taxes_list                = $this->VouchersModel->GetGSTTaxesList();
	$Voucher_TxnApproval_Info  = $this->VouchersModel->Voucher_TxnApproval();
	$bo_state_code             = $this->session->get('ses_bostecd');
	$ugst_states               = $this->ugst_states;
	$Voucher_TxnApproval       = $Voucher_TxnApproval_Info['approval_amt'] ?? '';
	$Voucher_TxnApproval_UUID  = $Voucher_TxnApproval_Info['uuid_to'] ?? 0; // who will approve, reject voucher
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
      //echo '<pre>';print_r($_POST);
     // die();
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
			];
			
	       if(!$this->validate($rules)){
			$errors = $this->validator->getErrors();
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		   }

		 $trans_result = $this->runTransaction(function($db) use ($bo_state_code,$ugst_states,$taxes_list,$voucher_type_id,$Voucher_TxnApproval_UUID,$Voucher_TxnApproval)
		{
			$gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);
		 	$voucher_date        = $this->request->getVar('voucher_date');
			$voucher_date 	     = validate_date_by_fy($voucher_date);
			$voucher_series      = $this->request->getVar('voucher_series'); 
			$party_id            = $this->request->getVar('party_id');  
			$long_narration      = $this->request->getVar('narration'); 
			$itmsdata            = json_decode($this->request->getVar('itmsdata'),true); 
			$currency_id 	     = $this->request->getVar('currency_id');			 
			$billno 		     = $this->request->getVar('billno');
			$supply_type         = $this->request->getVar('supply_type');
			$tax_type_id         = $this->request->getVar('tax_type_id');
			$reverse_charges     = $this->request->getVar('reverse_charges');
			$pos                 = $this->request->getVar('pos');
			$fcy_forex_rate      = $this->request->getVar('fcy_forex_rate');
			$memoCheck           = $this->request->getVar('memoCheck');			
			$taxInclusive        = $this->request->getVar('taxInclusive');
			$outsup_eco          = $this->request->getVar('eco_id') ?? 0;
			$tax_required_flag   = $this->request->getVar('tax_required_flag');
			$cmp_supply_type     = $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null		
			
			$sbnum      = $this->request->getVar('sbnum'); 
			$sbpcode    = $this->request->getVar('sbpcode');  
			$sbdt       = $this->request->getVar('sbdt');
			
			// EWB fields
			$ewb_supply_type     = $this->request->getVar('supply_type');
			$ewb_sub_supply_type = $this->request->getVar('tax_type_id');
			$ewb_sub_supply_desc = '';//$this->request->getVar('supply_type_desc');			
			$EinvoiceCheck        = $this->request->getVar('EINVOICECheck');
			
			
			$btnid               = $this->request->getVar('btnid');// submitbtn default save button
			$isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			   if($billno=="" )
				return ['status' => false, 'vchrtxnid'=>'', 'message' => 'Validation Error', 'errors' => ['Bill No. is required']];
				$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'sale');
				if($billno_duplicate=="1")
				return ['status' => false, 'vchrtxnid'=>'','message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']];
			}
	try{		
			$billsndrydata = [];
			if(!empty($this->request->getVar('billsndrydata')))
			$billsndrydata    = json_decode($this->request->getVar('billsndrydata'),true);
			
			$bbbdata = [];
			if(!empty($this->request->getVar('bbbdata')))
			$bbbdata = json_decode($this->request->getVar('bbbdata'),true);

			$prdata = [];
			if(!empty($this->request->getVar('prdata')))
			 $prdata = json_decode($this->request->getVar('prdata'),true);
			 
			$is_bbb=FALSE;
	     	if($bbbdata)
              $is_bbb=TRUE;			
	   
	       $is_pr=FALSE;
		   if($prdata)
              $is_pr=TRUE;
           
            $vch_particulars='';
            if($party_id){
              $acc_info = $this->VouchersModel->account_detail_info($party_id);
			  $vch_particulars = $acc_info['acc_name']; 
            }
           
          
			$voucher_no = $this->VouchersModel->get_voucher_no($voucher_type_id);
			$sale_total = array_sum(array_column($itmsdata, 'amount'));
			$sale_fcy_total = array_sum(array_column($itmsdata, 'amountfc'));
			$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
			$billsundry_fcy_total = array_sum(array_column($billsndrydata, 'billsundry_fcy_amount'));
			$sale_memo_total  = array_sum(array_column($itmsdata, 'memo_amount'));
			$billsundry_memo_total = array_sum(array_column($billsndrydata, 'memo_amount'));
			$sale_fcy_memo_total = 0;
			$billsundry_fcy_memo_total = 0;
			
			$total_tax_amount =0;
			$total_fcy_tax_amount =0;
			if($tax_required_flag==1){
	    	$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc')['fcy_tax_amount'];	        
	        $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['fcy_tax_amount'];
	        }
			
	        $voucherdata     = json_encode(["itmsdata"=>$itmsdata,"billsndrydata"=>$billsndrydata]);
		  
		    $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($sale_total+$billsundry_total),"vch_fcy_amt"=>parseAmount($sale_fcy_total+$billsundry_fcy_total),"vch_fcy_rate"=>(float)$fcy_forex_rate ?? 0,
								 "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
								 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
								 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
					            ); 
					
	     	$draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
	     	if($btnid=='submitbtn_drft'){
		   return ['status' => true, 'message' => 'Voucher saved in draft mode']; 
	   }
		   if($isvoucher_autobillno==0){
			   $outsup_bill_ref_no =$billno;
			}
			else if($isvoucher_autobillno==1){			
			  $outsup_bill_ref_no = $this->VouchersModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,1,'counter');	
			}
			
			$voucher_invoice_value = $sale_total + $billsundry_total + $total_tax_amount;
		
			$voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;
            
			$voucher_memo_value = $sale_memo_total + $billsundry_memo_total;
			$voucher_fcy_memo_value = (parseAmount($fcy_forex_rate) >0)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0;
           
		   /******* start of  if gstinType ==2 means composition then insert new voucher type 23  ********/
			if($gstinType==2){
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['tax_amount'];
				$total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['fcy_tax_amount'];	        
				$total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$cmp_supply_type)['tax_amount'];
				$total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$cmp_supply_type)['fcy_tax_amount'];
			
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();				
				$comp_vch_type_id =23;
				$voucher_series_id =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				$insert_data  = array(
				  "cmp_id"           => $this->company_id,		  
				  "vch_series_id"    => $voucher_series_id,          
				  "vch_type_id"      => $comp_vch_type_id,
				  "vch_sub_type_id"  => 0,		   
				  "vch_date"         => $voucher_date,
				  "mat_cent_id"      => 0,
				  "draft_vch_rec_id" => $draft_vch_rec_id
				 );		
			  $comp_voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data);
			  $insert_data = [
					  "cmp_id"          => $this->company_id,
					  "vch_series_id"   => $voucher_series_id,
					  "vch_txn_id"      => $comp_voucher_txn_id,
					  "master_id"       => $gstpaidacc_id,
					  'master_id_type'  => 'acc'
					 ];
			  $comp_main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			  $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series_id,
				  "vch_txn_id"      => $comp_voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($comp_voucher_txn_id,$comp_main_txn_id,'long',$long_narration);
			  
			  // now save txn entry to GSTIN PAID ACCOUNT, we will create this account at the time of company creation
			  $insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $gstpaidacc_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => $total_tax_amount, 
					  'acc_txn_fcy'       => $total_fcy_tax_amount,
					  'vch_txn_id'        => $comp_voucher_txn_id,
					  'txn_id'            => $comp_main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => 1
					];  				
					$this->VouchersModel->add_acc_txn_data($insert_data);

				$long_register_insert = [
						'acct_vch_type'   => $comp_vch_type_id,
						'cmp_id'          => $this->company_id,
						'vch_txn_id'      => $comp_voucher_txn_id,
						'txn_id'          => NULL,
						'vch_date'        => $voucher_date,					
						'acc_id'          => $gstpaidacc_id,
						'acc_txn_dr_amt'  => $total_tax_amount,
						'acc_txn_cr_amt'  => 0,
						'vch_narr'        => $long_narration ?? '',
						'hobo_id'         => $this->bo_id,
						'acc_txn_type'    => 1
					];
					$this->VouchersModel->add_register_txn_data($long_register_insert);				
			}
			/******* end of  if gstinType ==2 means composition **********/
			
			$insert_data  = array(
			  "cmp_id"           => $this->company_id,		  
			  "vch_series_id"    => $voucher_series,          
			  "vch_type_id"      => $voucher_type_id,
			  "vch_sub_type_id"  => 0,		   
			  "vch_date"         => $voucher_date,
			  "mat_cent_id"      => 0,
			  "draft_vch_rec_id" => $draft_vch_rec_id
			 );		
          $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data);
		  // make entry in baridge table if composition is set as 2 
                if($gstinType==2){ 
					$brdge_insert_data  = array(
									  "cmp_id"           => $this->company_id,		  
									  "vch_bridge_type"    => 4,          
									  "vch_txn_id_src"      => $voucher_txn_id,
									  "vch_txn_id_dest"  => $comp_voucher_txn_id
									 );									 
					$this->VouchersModel->add_vchbridgen_txn_data($brdge_insert_data);
				}		
					
		  if(parseAmount($fcy_forex_rate) >0){
		    $fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	 
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
		    }
		    
			 if($isvoucher_autobillno==0){
			 $gstroutsup_insert_data = array("cmp_id"=> $this->company_id,"vch_txn_id"=>$voucher_txn_id,
											"outsup_pos"=>$pos,"outsup_bill_ref_no"=>$outsup_bill_ref_no,"outsup_rev_chg"=>$reverse_charges,
											"dr_note_inwsup_id"=>0,"outsup_eco"=>(int)$outsup_eco,
											"exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode,
											"gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->add_gstroutsup_data($gstroutsup_insert_data);
			}
			else if($isvoucher_autobillno==1){
			
			$gstroutsup_insert_data = array("cmp_id"=> $this->company_id,"vch_txn_id"=>$voucher_txn_id,
											"outsup_pos"=>$pos,"outsup_bill_ref_no"=>$outsup_bill_ref_no,"outsup_rev_chg"=>$reverse_charges,
											"dr_note_inwsup_id"=>(int)0,"outsup_eco"=>(int)$outsup_eco,
											"exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode,
											"gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->add_gstroutsup_data($gstroutsup_insert_data);	
			}
			
			
			/***************  Start of EWB + E-Invoice *****************/
				$ewb_insert_data=["hobo_id"=>$this->bo_id,"cmp_id" =>$this->company_id,"ewb_no"=>"","ewb_date"=>$voucher_date,"ewb_supply_type"=>$ewb_supply_type,
								 "ewb_sub_supply_type"=>$ewb_sub_supply_type,"vch_txn_id"=>$voucher_txn_id,"hobo_id"=>$this->bo_id];
				$ewb_id  = $this->VouchersModel->add_ewb_data($ewb_insert_data);
				if($EinvoiceCheck){
					$einvoice_insert_data=["cmp_id" =>$this->company_id,"einv_no"=>0,"env_date"=>$voucher_date,"vch_txn_id"=>$voucher_txn_id];
					$einv_id = $this->VouchersModel->add_einvoice_data($einvoice_insert_data);
				$this->VouchersModel->add_ewb_txntype_data($ewb_sub_supply_type,$ewb_id,$einv_id,$voucher_txn_id,$_POST);
				
				}
				/***************  End of EWB + E-Invoice *****************/
				
		 	 
			/*********** Party Account Entries  *****************/
			  if($Voucher_TxnApproval!='' &&  parseAmount($voucher_invoice_value) > parseAmount($Voucher_TxnApproval)){
				 $acc_txn_type = 4; // voucher is pending for approval
				}
				 else
				 $acc_txn_type = 1;
				
				 $insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $party_id,
				  'master_id_type'  => 'acc'
				 ];
				$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
				
				$insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $party_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 1,
				  'acc_txn_amt'       => $voucher_invoice_value, 
				  'acc_txn_fcy'       => $voucher_fcy_invoice_value,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $main_txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => $acc_txn_type
				];  				
				$this->VouchersModel->add_acc_txn_data($insert_data);
			 /**********************************************************/
			 /********************* Start of Save Memorandum Entry **********************/
			 if($voucher_memo_value >0){
			 $memo_insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $party_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 1,
				  'acc_txn_amt'       => $voucher_memo_value, 
				  'acc_txn_fcy'       => $voucher_fcy_memo_value,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $main_txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 3
				];  				
				$this->VouchersModel->add_acc_txn_data($memo_insert_data);
			 }
				$long_register_insert = [
					'acct_vch_type'   => $voucher_type_id,
					'cmp_id'          => $this->company_id,
					'vch_txn_id'      => $voucher_txn_id,
					'txn_id'          => NULL,
					'vch_date'        => $voucher_date,					
					'acc_id'          => $party_id,
					'acc_txn_dr_amt'  => $voucher_invoice_value,
					'acc_txn_cr_amt'  => 0,
					'vch_narr'        => $long_narration ?? '',
					'hobo_id'         => $this->bo_id,
					'acc_txn_type'    => $acc_txn_type
				];
				$this->VouchersModel->add_register_txn_data($long_register_insert);
				
			 /*********************End of Save Memorandum Entry **********************/
			 
			 /*********************Start of Save Account Wise Grid Entries **********************/
			  $tax_igst_amt  = 0;
			  $tax_cgst_amt  = 0;	
			  $tax_sgst_amt  = 0;	
			  $tax_utgst_amt = 0;	
			  $tax_cess_amt  = 0;	 	
			  if($itmsdata) {
			        foreach($itmsdata as $item_row) {
						$inv_supply_id   = $item_row['supply_type_id'];
						/******************* Save MEMO TXN ****************/
						$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $item_row['account_id'],
							  'master_id_type'  => 'acc'
							 ];
						$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
						if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){	
						        $memo_txn_data  = [
								  'cmp_id'            => $this->company_id,
								  'acc_id'            => $item_row['account_id'],
								  'acc_txn_date'      => $voucher_date,
								  'acc_txn_dr_cr'     => 2,
								  'acc_txn_amt'       => $item_row['memo_amount'], 
								  'acc_txn_fcy'       => (parseAmount($fcy_forex_rate) >0)?($item_row['memo_amount']*$fcy_forex_rate):0,
								  'vch_txn_id'        => $voucher_txn_id,
								  'txn_id'            => $txn_id,
								  'hobo_id'           => $this->bo_id,
								  'acc_txn_type'      => 3
								];  				
								$this->VouchersModel->add_acc_txn_data($memo_txn_data);
						}
						/******************* Save Account TXN ****************/
						$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $item_row['account_id'],
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => 2,
							  'acc_txn_amt'       => $item_row['amount'], 
							  'acc_txn_fcy'       => $item_row['amountfc'],
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
							$this->VouchersModel->add_acc_txn_data($acc_txn_data);
							
							$register_insert_data  = [
                				  'acct_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'acc_id'            => $item_row['account_id'],
                				  'acc_txn_cr_amt'    => $item_row['amount'], 
                				  'acc_txn_dr_amt'    => 0, 
                				  'vch_narr'          => $item_row['description'],
                				  'hobo_id'           => $this->bo_id,
                				  'acc_txn_type'      => $acc_txn_type
                				]; 								
                				$this->VouchersModel->add_register_txn_data($register_insert_data);				
                		        $narration            = $item_row['description'];
                		        $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
						
						/****************  Start of Save Tax Inclusive Data **********************/
						if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
						    $taxinc_insert_data  = [
                				  'cmp_id'            => $this->company_id,
                				  'acc_itm_id'        => $item_row['account_id'],                						 
                				  'acc_txn_inc_amt'   => $item_row['txinc_amount'], 
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $txn_id,
                				  'acc_txn_dr_cr'     => 2,                				  
                				]; 								
                				$this->VouchersModel->add_taxinc_txn_data($taxinc_insert_data);
						    
						}
						/****************  End of Save Tax Inclusive Data **********************/
						
						 							
						
						 /*******************Start of Save TAX SUMMARY ****************/
						$tax_cat_id       = $item_row['tax_cat_id'];
						$tax_detail_rates = $item_row['tax_details'];

						$amount     = parseAmount($item_row['amount']);    // taxable base
						$amount_fcy = parseAmount($item_row['amountfc']);  // taxable base (FCY)
						$pos_code   = sprintf('%02d', $pos);

						// Account/HSN details
						$iteminfo = $this->VouchersModel->get_acc_details_info($item_row['account_id']);
						$item_hsn = $iteminfo['acc_sac'] ?? '';

						// ------------------------------------------------------------------
						// Effective rates (regular vs composition)
						// ------------------------------------------------------------------
						$cmp_supply_type = $this->request->getPost('cmp_suply_type'); // "1","2","3","4" or null

						// Defaults (regular)
						$igst_rate_eff  = parseAmount($item_row['igst_rate']);
						$cgst_rate_eff  = $igst_rate_eff / 2;
						$sgst_rate_eff  = $igst_rate_eff / 2;
						$utgst_rate_eff = $igst_rate_eff / 2;
						$cess_rate_eff  = isset($tax_detail_rates['cess']) ? parseAmount($tax_detail_rates['cess']) : 0;

						// Override for composition (bo_gstin_type == 2) via helper
						if ((int)$gstinType === 2) {
							$comp = get_composition_rates($cmp_supply_type);
							$igst_rate_eff  = $comp['igst'];
							$cgst_rate_eff  = $comp['cgst'];
							$sgst_rate_eff  = $comp['sgst'];
							$utgst_rate_eff = $comp['ut'];
							$cess_rate_eff  = $comp['cess'] ?? 0;
						}

						// Default summary
						$taxsummary_data = [
							'cmp_id'             => $this->company_id,
							'vch_txn_id'         => $voucher_txn_id,
							'txn_id'             => $txn_id,
							'acc_bsd_id'         => $item_row['account_id'],
							'acc_bsd_type'       => 1,
							'vch_igst'           => 0,
							'vch_igst_rate'      => 0,
							'vch_cgst'           => 0,
							'vch_cgst_rate'      => 0,
							'vch_sgst_ugst'      => 0,
							'vch_sgst_ugst_rate' => 0,
							'vch_cess'           => 0,
							'vch_cess_rate'      => 0,
							'vch_taxable_value'  => $amount,
							'vch_total_tax'      => 0
						];

						// Case 1: IGST applies (different states)
						if ($bo_state_code != $pos_code) {
							$igst_value = ($amount * $igst_rate_eff) / 100;

							$taxsummary_data['vch_igst']      = $igst_value;
							$taxsummary_data['vch_igst_rate'] = $igst_rate_eff;
							$taxsummary_data['vch_total_tax'] = $igst_value;
							$tax_igst_amt += $igst_value;
						}
						// Case 2: CGST + SGST applies (same state)
						elseif ($bo_state_code == $pos_code) {
							$cgst_value = ($amount * $cgst_rate_eff) / 100;
							$sgst_value = ($amount * $sgst_rate_eff) / 100;

							$taxsummary_data['vch_cgst']           = $cgst_value;
							$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
							$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
							$taxsummary_data['vch_sgst_ugst_rate'] = $sgst_rate_eff;
							$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

							$tax_cgst_amt += $cgst_value;
							$tax_sgst_amt += $sgst_value;
						}
						// Case 3: UGST applies
						elseif (in_array($pos_code, $ugst_states)) {
							$cgst_value = ($amount * $cgst_rate_eff) / 100;
							$ugst_value = ($amount * $utgst_rate_eff) / 100;

							$taxsummary_data['vch_cgst']           = $cgst_value;
							$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
							$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
							$taxsummary_data['vch_sgst_ugst_rate'] = $utgst_rate_eff;
							$taxsummary_data['vch_total_tax']      = ($cgst_value + $ugst_value);

							$tax_cgst_amt += $cgst_value;
							$tax_utgst_amt += $ugst_value;
						}

						// Always add cess if applicable (effective rate already set)
						if ($cess_rate_eff > 0) {
							$cess_value = ($amount * $cess_rate_eff) / 100;
							$taxsummary_data['vch_cess']      = $cess_value;
							$taxsummary_data['vch_cess_rate'] = $cess_rate_eff;
							$taxsummary_data['vch_total_tax'] += $cess_value;
							$tax_cess_amt += $cess_value;
						}

						$gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
							? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : null))
							: ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);

						$taxsummary_data['vch_date']      = $voucher_date;
						$taxsummary_data['is_outward']    = 1;
						$taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
						$taxsummary_data['vch_hsn_sac']   = $item_hsn;
						$taxsummary_data['inv_supply_id'] = $inv_supply_id;

						$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

						$hsnummary_data = [
							'cmp_id'                => $this->company_id,
							'vch_txn_id'            => $voucher_txn_id,
							'txn_id'                => $txn_id,
							'vch_hsn_sac'           => $item_hsn,
							'vch_hsn_sac_taxbl_val' => $amount,
							'vch_hsn_sac_qty'       => 0,
							'vch_hsn_sac_uom'       => 0,
							'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
							'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
							'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
							'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
							'inv_supply_id'         => $inv_supply_id,
							'vch_gst_sum_id'        => $vch_gst_sum_id
						];
						$this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);

						/*********  FCY Tax Summary ****/
						if (parseAmount($fcy_forex_rate) > 0) {
							$taxsummary_fcy_data = [
								'cmp_id'                => $this->company_id,
								'vch_txn_id'            => $voucher_txn_id,
								'txn_id'                => $txn_id,
								'acc_bsd_id'            => $item_row['account_id'],
								'acc_bsd_type'          => 1,
								'vch_igst_fcy'          => 0,
								'vch_cgst_fcy'          => 0,
								'vch_sgst_ugst_fcy'     => 0,
								'vch_cess_fcy'          => 0,
								'vch_taxable_value_fcy' => $amount_fcy,
								'vch_total_tax_fcy'     => 0
							];

							if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
								$igst_value = ($amount_fcy * $igst_rate_eff) / 100;
								$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
							}
							elseif ($bo_state_code == $pos_code) {
								$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;
								$sgst_value = ($amount_fcy * $sgst_rate_eff) / 100;

								$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
								$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;
							}
							elseif (in_array($pos_code, $ugst_states)) {
								$ugst_value = ($amount_fcy * $utgst_rate_eff) / 100;
								$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;

								$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] = $ugst_value;
							}

							if ($cess_rate_eff > 0) {
								$cess_value = ($amount_fcy * $cess_rate_eff) / 100;
								$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
							}

							$this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
						}
						/*******************End of Save TAX SUMMARY ****************/
					}
				}		 
			 /*********************End of Save Account Wise Grid Entries **********************/
			 
			 /*********************Start of Save Billsundry Wise Grid Entries **********************/
			   if($billsndrydata) {
			        foreach($billsndrydata as $item_row) {
						/******************* Save MEMO TXN ****************/
						$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $item_row['billsundry_id'],
							  'master_id_type'  => 'bsd'
							 ];
						$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
						if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){	
						        $memo_txn_data  = [
								  'cmp_id'            => $this->company_id,
								  'acc_id'            => $item_row['billsundry_id'],
								  'acc_txn_date'      => $voucher_date,
								  'acc_txn_dr_cr'     => ($item_row['memo_amount']<0) ? 2 : 1,
								  'acc_txn_amt'       => $item_row['memo_amount'], 
								  'acc_txn_fcy'       => (parseAmount($fcy_forex_rate) >0)?($item_row['memo_amount']*$fcy_forex_rate):0,
								  'vch_txn_id'        => $voucher_txn_id,
								  'txn_id'            => $txn_id,
								  'hobo_id'           => $this->bo_id,
								  'acc_txn_type'      => 3
								];  				
								$this->VouchersModel->add_acc_txn_data($memo_txn_data);
						}
						/******************* Save Account TXN ****************/
						$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $item_row['billsundry_id'],
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => ($item_row['billsundry_amount']<0) ? 2 : 1,
							  'acc_txn_amt'       => $item_row['billsundry_amount'], 
							  'acc_txn_fcy'       => $item_row['billsundry_fcy_amount'],
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
							$this->VouchersModel->add_acc_txn_data($acc_txn_data);
							
										
                		    $narration            = "";
                		    $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
							
						
						 /*******************Start of Save TAX SUMMARY ****************/
						 $tax_cat_id              = $item_row['tax_cat_id'];
						 $tax_detail_rates        = $item_row['tax_details'];
						 
						 $amount                  = parseAmount($item_row['billsundry_amount']); // taxable base
						 $amount_fcy              = parseAmount($item_row['billsundry_fcy_amount']); // taxable base
                         $pos_code                = sprintf( '%02d', $pos );
						 $billsundry_info = $this->VouchersModel->get_bsd_details_info($item_row['billsundry_id']);
						 $bsd_hsn         = $billsundry_info['bsd_hsn_sac'] ?? '';
                         $taxsummary_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst'           => 0,
                            'vch_igst_rate'      => 0,
                            'vch_cgst'           => 0,
                            'vch_cgst_rate'      => 0,
                            'vch_sgst_ugst'      => 0,
                            'vch_sgst_ugst_rate' => 0,
                            'vch_cess'           => 0,
                            'vch_cess_rate'      => 0,
                            'vch_taxable_value'  => $amount,
                            'vch_total_tax'      => 0
                        ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                            $igst_value = ($amount * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_data['vch_igst']      = $igst_value;
                            $taxsummary_data['vch_igst_rate'] = $item_row['igst_rate'];
                            $taxsummary_data['vch_total_tax'] = $igst_value;
                            
                            $tax_igst_amt +=$igst_value;
                           
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_data['vch_cgst']           = $cgst_value;
                            $taxsummary_data['vch_cgst_rate']      = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_sgst_ugst']      = $sgst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;
                            
                            $tax_cgst_amt += $cgst_value;
                            $tax_sgst_amt +=  $sgst_value;
                            
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount * $item_row['igst_rate']/2) / 100;
							$cgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                            $taxsummary_data['vch_sgst_ugst']      = $ugst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $ugst_value;
                            
							$tax_cgst_amt += $cgst_value;
                            $tax_utgst_amt += $ugst_value;
                           
                        }
                    
                        // Always add cess if applicable
                        $cess_rate=0;
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
							$cess_rate=$tax_detail_rates['cess'];
                            $cess_value = ($amount * $tax_detail_rates['cess']) / 100;
                            $taxsummary_data['vch_cess']      = $cess_value;
                            $taxsummary_data['vch_cess_rate'] = $tax_detail_rates['cess'];
                            $taxsummary_data['vch_total_tax'] += $cess_value;                            
                            $tax_cess_amt += $cess_value;
                        }
						
						$taxsummary_data['vch_date']      = $voucher_date;
						$taxsummary_data['is_outward']    = 1;				 
						$taxsummary_data['gst_rate_grp']  = '1,'.parseAmount($item_row['igst_rate']).','.parseAmount($cess_rate);
						$taxsummary_data['vch_hsn_sac']   = $bsd_hsn;
						$taxsummary_data['inv_supply_id'] = 0;
                        //echo '<pre>';
                       // print_r($taxsummary_data);
						 $vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data,$tax_required_flag);   
						  
						$hsnummary_data = [ 
							'cmp_id'                => $this->company_id,
							'vch_txn_id'            => $voucher_txn_id,
							'txn_id'                => $txn_id,
							'vch_hsn_sac'           => $bsd_hsn,
							'vch_hsn_sac_taxbl_val' => $amount,
							'vch_hsn_sac_qty'       => 0,
							'vch_hsn_sac_uom'       => 0,
							'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
							'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
							'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
							'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
							'inv_supply_id'         => 0,
							'vch_gst_sum_id'        => $vch_gst_sum_id
							];			  
						$this->VouchersModel->add_hsnsummary_data($hsnummary_data,$tax_required_flag);  
						  
						  
						  
						  if(parseAmount($fcy_forex_rate) >0 ){
						   // Default summary
                          $taxsummary_fcy_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst_fcy'       => 0,
                            'vch_cgst_fcy'       => 0,
                            'vch_sgst_ugst_fcy'  => 0,
                            'vch_cess_fcy'       => 0,
                            'vch_taxable_value_fcy'  => $amount_fcy,
                            'vch_total_tax_fcy'      => 0
                        ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            $igst_value = ($amount_fcy * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_fcy_data['vch_igst_fcy'] = $igst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_fcy_data['vch_cgst_fcy']           = $cgst_value;
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $sgst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $cgst_value + $sgst_value;
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
							$cgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $ugst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $ugst_value;
                        }
                    
                        // Always add cess if applicable
                       
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
                            $cess_value = ($amount_fcy * $tax_detail_rates['cess']) / 100;
                            $taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
                        } 
						      
						   $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);    
						  }
						  
						 /*******************End of Save TAX SUMMARY ****************/
					}
				}		 
			 /*********************End of Save Billsundry Wise Grid Entries **********************/
			 
			 /***************Start of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			  // if gstinType is 2 means Composition then no record will insert in taxable AccountsModel
			  if($gstinType==1 ){	
				if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_utgst_amt,4,$voucher_date,2);
				   }		  
				if($tax_cess_amt > 0)		
				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cess_amt,5,$voucher_date,2);
			   }
			   if($gstinType==2 ){
				 if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_utgst_amt,4,$voucher_date,2);
				   }		    
				 if($tax_cess_amt > 0)  
				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cess_amt,5,$voucher_date,2);
		    }
			 /*************** End of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			
			
			
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
			
			
		/***********   Save Bill By Bill Data  *************/
		if(count($bbbdata)){		
		 $this->VouchersModel->SaveBillByBillData($bbbdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		/***********   Save Project Reporting  Data  *************/
		if(count($prdata)){		
		 $this->VouchersModel->SaveProjectReportingData($prdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		
		$this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	  $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  /************ Save Voucher Activity Log **********/
	    $voucher_messages = [
			18  => "New sale w/o item voucher has been added by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			18  => "sale w/o item voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
		];
		$erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been added by [USERNAME]([UUID])";
		$this->VouchersModel->SaveUserActivity($erp_activity_log,$voucher_txn_id);
	    
	    	return ['status' => true, 'vchrtxnid'=>$voucher_txn_id,'message' => 'saved', 'errors' => []];
       }
		  catch (\Throwable $e) {
				return  ['status' => false,'vchrtxnid'=>'', 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
				// Get line, file and stack trace
                log_message('error', 'DB Error: ' . $e->getMessage());
                log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
    			}
	     });
        if(isset($trans_result['result']['status']) && $trans_result['result']['status']==''){            
            if(isset($trans_result['result']['errors']))
            return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>$trans_result['result']['errors']]);
            else
            return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>'']);
        }
       else
         return $this->response->setJSON($trans_result); 
	}		
		
	$data['voucher_type_id']       = $voucher_type_id;
	$data['SuppltSubSupplyTypes']  = json_encode($this->VouchersModel->SuppltSubSupplyTypes());
	$data['message_output']        = $this->message_output;	
	$data['taxes_list']            = $taxes_list;	
	$data['base_url']              = $this->base_url; 
	$data['folder_path']           = $this->folder_path;
	
	$data['voucher_date']          = $this->VouchersModel->getLastVoucherDate($voucher_type_id);
	$data['bo_gstin_type']         = $this->session->get('bo_gstin_type') ?? 1;// regular
	$data['voucher_auto_no']       = $this->VouchersModel->get_voucher_no($voucher_type_id);
	$data['voucher_bill_no']       = $this->VouchersModel->get_billno_format($voucher_type_id,18,date('Y-m-d'),1,'counter');
	$data['voucher_series']        = $this->VouchersModel->comp_voucher_result($this->company_id,$voucher_type_id);
	$data['party_dropdown']        = $this->VouchersModel->party_dropdown();
	
	$data['bills_method_list']     = ['','New Ref.','Adjustment'];
	$accounts_list                 = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	$bsd_accounts                  = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	if(!$bsd_accounts){
	
	    $bsd_accounts   =[];
	}
	$data['sess_cmp_suply_type']         = $this->session->get('cmp_suply_type') ?? 0;// composition supply type
	$data['comp_supply_types_dropdown'] = $this->compositionSupplyOptions;
	$data['accounts_json_file']    = $accounts_list;
	$data['bsd_json_file']         = $bsd_accounts;		 
	$data['bo_state_code']         = $bo_state_code;
	$data['currency_list']         = $this->VouchersModel->get_currency_list();
	$data['states_lists']          = $this->VouchersModel->show_states_lists();
	$data['eco_dropdown']          = $this->VouchersModel->show_eco_lists();
    $data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();
    $data['ses_company_name']      = $this->session->get('ses_company_name');
	$data['supply_type_list']      =  SupplyTypesList();
	$data['tax_type_list']         = $this->TXNTypesListWO;	
	$data['billfrm_data']          = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
	return view($this->folder_path.'sales/add_non_item',$data);	  	    
  }
  
  public function edit($voucher_txn_id,$acc_id = 0){
	$voucher_type_id    = 18;
	$voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
	if(empty($voucher_info)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
	
	$vch_subtype_id            = $voucher_info['vch_sub_type_id'];	
	$taxes_list                = $this->VouchersModel->GetGSTTaxesList();
	$Voucher_TxnApproval_Info  = $this->VouchersModel->Voucher_TxnApproval();
	$get_narration             = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
	//$get_gstroutsup_info       = $this->VouchersModel->get_gstroutsup_info($voucher_txn_id);
	
	
	$istaxinc                  = $this->VouchersModel->check_sale_taxinc($voucher_txn_id);
	$ismemosale                = $this->VouchersModel->check_sale_memoentry($voucher_txn_id);
	$bo_state_code             = $this->session->get('ses_bostecd');
	$ugst_states               = $this->ugst_states;
	$Voucher_TxnApproval       = $Voucher_TxnApproval_Info['approval_amt'] ?? '';
	$Voucher_TxnApproval_UUID  = $Voucher_TxnApproval_Info['uuid_to'] ?? 0; // who will approve, reject voucher
	$data['draft_vch_rec_id'] = 0;
	$data['ses_company_name']  = $this->session->get('ses_company_name');
	$data['supply_type_list']  =  SupplyTypesList();
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		$ip = $_SERVER['REMOTE_ADDR'];
	    if($ip=='103.172.223.178' || $ip=='103.172.223.179' || $ip=='112.79.17.232'){
		//	$gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);
	   //echo "<pre>";print_r($_POST);die();
	   } 
		//echo $vch_subtype_id;
       //echo '<pre>';print_r($_POST);die();
		
	  if($vch_subtype_id==8){
       
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
			 ];
			
	    if(!$this->validate($rules)){
	      	$errors = $this->validator->getErrors();
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	    }
		$trans_result = $this->runTransaction(function($db) use ($voucher_txn_id,$bo_state_code,$ugst_states,$taxes_list,$voucher_type_id,$Voucher_TxnApproval_UUID,$Voucher_TxnApproval)
		{
			
		 try{		
		    $gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);
			$sale_type           = $this->request->getVar('sale_type');
			$invoice_type        = $this->request->getVar('invoice_type');
			$voucher_date        = $this->request->getVar('voucher_date');
			$voucher_date 	     = validate_date_by_fy($voucher_date);
			$voucher_series      = $this->request->getVar('voucher_series'); 
			$party_id            = $this->request->getVar('party_id');  
			$long_narration      = $this->request->getVar('narration'); 
			$itmsdata            = json_decode($this->request->getVar('itmsdata'),true); 
			$billsndrydata       = json_decode($this->request->getVar('billsndrydata'),true);
			$batchdata           = json_decode($this->request->getVar('itemsbatchdata'),true); 	
			$currency_id 	     = $this->request->getVar('currency_id');			 
			$billno 		     = trim($this->request->getVar('billno'));
			$supply_type         = $this->request->getVar('supply_type');
			$reverse_charges     = $this->request->getVar('reverse_charges');
			$pos                 = $this->request->getVar('pos');
			$fcy_forex_rate      = $this->request->getVar('fcy_forex_rate');
			$memoCheck           = $this->request->getVar('memoCheck');			
			$taxInclusive        = $this->request->getVar('taxInclusive');
			$ewb_invoice_type    = $this->request->getVar('ewb_invoice_type');
			
			$sbnum      = $this->request->getVar('sbnum'); 
			$sbpcode    = $this->request->getVar('sbpcode');  
			$sbdt       = $this->request->getVar('sbdt');
			
			// EWB fields
			$ewb_supply_type     = $this->request->getVar('supply_type');
			$ewb_sub_supply_type = $this->request->getVar('tax_type_id');
			$ewb_sub_supply_desc = '';//$this->request->getVar('supply_type_desc');			
			$EinvoiceCheck        = $this->request->getVar('EINVOICECheck');
			$EWAYCheck            = $this->request->getVar('EWAYCheck');
			
			$outsup_eco          = $this->request->getVar('eco_id') ?? 0;
			$matrcntr_id         = $this->request->getVar('matrcntr_id');   
			$draft_vch_rec_id    = $this->request->getVar('draft_vch_rec_id');
			$tax_required_flag   = $this->request->getVar('tax_required_flag');
			$cmp_supply_type     = $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null

			$btnid               = $this->request->getVar('btnid');// submitbtn default save button
		    $isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			
		
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			   if($billno=="")
				return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']];
				$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'sale',1,$voucher_txn_id);
				if($billno_duplicate=="1")
				return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']];
			}
			
			$batchdata = [];
		  if(!empty($this->request->getVar('itemsbatchdata')))
			$batchdata = json_decode($this->request->getVar('itemsbatchdata'),true);
		
			$is_batch=FALSE;
		 if($batchdata)
		 $is_batch=TRUE;
	 
			$bbbdata = [];
		  if(!empty($this->request->getVar('bbbdata')))
			$bbbdata = json_decode($this->request->getVar('bbbdata'),true);
		 
		  $prdata = [];
		  if(!empty($this->request->getVar('prdata')))
			$prdata = json_decode($this->request->getVar('prdata'),true);
		
		$is_bbb=FALSE;
		 if($bbbdata)
		 $is_bbb=TRUE;			
		 
		 $is_pr=FALSE;
		  if($prdata)
		   $is_pr=TRUE;
	       $vch_particulars='';
            if($party_id){
              $acc_info = $this->VouchersModel->account_detail_info($party_id);
			  $vch_particulars = $acc_info['acc_name']; 
            }
			if($vch_particulars=='')
		    return ['status' => false, 'message' => 'Party field is not valid']; 
		
		  // Mark Dirty Transactions
		    $voucherDate = new \DateTime($voucher_date);
			$today       = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				 //$this->VouchersModel->markSnapshotDirty($voucher_date);
			 }
			
			$voucher_no                = $this->VouchersModel->get_voucher_no($voucher_type_id);
			$sale_total                = array_sum(array_column($itmsdata, 'item_total_amount'));
			$sale_fcy_total            = array_sum(array_column($itmsdata, 'item_total_fcy_amount'));
			$billsundry_total          = array_sum(array_column($billsndrydata, 'billsundry_amount'));
			$billsundry_fcy_total      = array_sum(array_column($billsndrydata, 'billsundry_fcy_amount'));
			$sale_memo_total           = array_sum(array_column($itmsdata, 'memo_amount'));
			$billsundry_memo_total     = array_sum(array_column($billsndrydata, 'memo_amount'));
			$sale_fcy_memo_total       = 0;
			$billsundry_fcy_memo_total = 0;			
			$total_tax_amount          = 0;
			$total_fcy_tax_amount      = 0;
			$pos_code   = sprintf('%02d', $pos);
		    if($tax_required_flag==1){
	    	$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
	        
	        $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
			}
			
			
		 $voucherdata  = json_encode(["is_batch"=>$is_batch,"batchdata"=>$batchdata,"bbbdata"=>$bbbdata,"prdata"=>$prdata,"mat_cent_id"=>$matrcntr_id,"itmsdata"=>$itmsdata,"billsndrydata"=>$billsndrydata]);
	    
		if($draft_vch_rec_id >0){
	          $pgrdata_drft    = array(
		                         "log_date_time"=>$voucher_date,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($sale_total+$billsundry_total),"vch_fcy_amt"=>parseAmount($sale_fcy_total+$billsundry_fcy_total),"vch_fcy_rate"=>parseAmount($fcy_forex_rate),
								 "vch_particulars"=>$vch_particulars,
								 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
								 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
					            );  
	          $this->VouchersModel->UpdateDrftVoucher($draft_vch_rec_id,$pgrdata_drft);  
	        }
			else{
				$pgrdata_drft = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                       "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					           "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($sale_total+$billsundry_total),"vch_fcy_amt"=>parseAmount($sale_fcy_total+$billsundry_fcy_total),"vch_fcy_rate"=>(float)$fcy_forex_rate ?? 0,
							   "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
							   "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
							   "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
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
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'accttxnmst');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchhsnsacn'); 
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchgstsumn');  
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'acctamtinc');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'batchtxnmt'); 		   
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'einvmaster'); 
	       $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'ewbmastern'); 			
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'gstdispfrm'); 
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'gstshipton'); 
		   $this->VouchersModel->clear_system_journal_txn_data($voucher_txn_id,4);
		 /************    Sales With Stock Outword  *****************/
		 
		 if($isvoucher_autobillno==0){
			   $outsup_bill_ref_no =$billno;
			}
			else if($isvoucher_autobillno==1){			
			  $outsup_bill_ref_no = $this->VouchersModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,1,'counter');	
			}
			$voucher_invoice_value = $sale_total + $billsundry_total + $total_tax_amount;
			
				$voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;
				
				$voucher_memo_value = $sale_memo_total + $billsundry_memo_total;
				$voucher_fcy_memo_value = ($fcy_forex_rate >0)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0;
				
			/******* start of  if gstinType ==2 means composition then insert new voucher type 23  ********/
			if($gstinType==2){
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
			
			    $get_voucher_bridge_info = $this->VouchersModel->get_voucher_bridge_info($voucher_txn_id,4);
				if(!$get_voucher_bridge_info)
				$comp_voucher_txn_id = $voucher_txn_id;
			    else
			    $comp_voucher_txn_id    = $get_voucher_bridge_info['vch_txn_id_dest'];
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$comp_vch_type_id =23;
				$voucher_series_id =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				$update_data  = array( 				 	  
				  "vch_series_id"    => $voucher_series_id,          
				  "vch_type_id"      => $comp_vch_type_id,
				  "vch_sub_type_id"  => 0,		   
				  "vch_date"         => $voucher_date,
				  "mat_cent_id"      => $matrcntr_id,
				  "draft_vch_rec_id" => $draft_vch_rec_id
				 );		
			  $this->VouchersModel->update_comps_voucher_cons_data($update_data,$comp_voucher_txn_id,$this->company_id,$comp_vch_type_id);
			  $insert_data = [
					  "cmp_id"          => $this->company_id,
					  "vch_series_id"   => $voucher_series_id,
					  "vch_txn_id"      => $comp_voucher_txn_id,
					  "master_id"       => $gstpaidacc_id,
					  'master_id_type'  => 'acc'
					 ];
			  $comp_main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			  $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series_id,
				  "vch_txn_id"      => $comp_voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($comp_voucher_txn_id,$comp_main_txn_id,'long',$long_narration);
			  // now save txn entry to GSTIN PAID ACCOUNT, we will create this account at the time of company creation
			  $insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $gstpaidacc_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => $total_tax_amount, 
					  'acc_txn_fcy'       => $total_fcy_tax_amount,
					  'vch_txn_id'        => $comp_voucher_txn_id,
					  'txn_id'            => $comp_main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => 1
					];  				
					$this->VouchersModel->add_acc_txn_data($insert_data);
			  $long_register_insert = [
						'acct_vch_type'   => $comp_vch_type_id,
						'cmp_id'          => $this->company_id,
						'vch_txn_id'      => $comp_voucher_txn_id,
						'txn_id'          => NULL,
						'vch_date'        => $voucher_date,					
						'acc_id'          => $gstpaidacc_id,
						'acc_txn_dr_amt'  => $total_tax_amount,
						'acc_txn_cr_amt'  => 0,
						'vch_narr'        => $long_narration ?? '',
						'hobo_id'         => $this->bo_id,
						'acc_txn_type'    => 1
					];
					$this->VouchersModel->add_register_txn_data($long_register_insert);				
			}
			/******* end of  if gstinType ==2 means composition **********/
			
			
			 $update_data  = array(
			  "vch_series_id"   => $voucher_series,
			  "vch_date"         => $voucher_date,
			  "draft_vch_rec_id" => $draft_vch_rec_id,
			  "mat_cent_id"      => $matrcntr_id
			);
			$this->VouchersModel->update_voucher_cons_data($update_data,$voucher_txn_id,$this->company_id);
			
			// make entry in baridge table if composition is set as 2 
                if($gstinType==2){ 
					$brdge_update_data  = array(
									  "vch_txn_id_src"      => $voucher_txn_id
									 );									 
					$this->VouchersModel->update_voucher_bridge_data($brdge_update_data,$comp_voucher_txn_id,4);
				}
			
			
			if($fcy_forex_rate >0){
			$fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
						   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
						  );	
			$this->VouchersModel->save_voucher_fcyrate($fcy_data);	
			}
			else{				
				$this->VouchersModel->clear_voucher_fcyrate($voucher_txn_id);
		  	} 
				
			if($isvoucher_autobillno==0){
				 $gstroutsup_insert_data = array(
												"outsup_pos"=>$pos,"outsup_rev_chg"=>$reverse_charges,
												"dr_note_inwsup_id"=>(int)0,"outsup_eco"=>(int)$outsup_eco,
												"exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode,
												"gst_supply_type"=>(int)$cmp_supply_type
												);
				 $this->VouchersModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
				}
				else if($isvoucher_autobillno==1){
				 $gstroutsup_insert_data = array(
												"outsup_pos"=>$pos,"outsup_rev_chg"=>$reverse_charges,
												"dr_note_inwsup_id"=>(int)0,"outsup_eco"=>(int)$outsup_eco,
												"exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode,
												"gst_supply_type"=>(int)$cmp_supply_type
												);
				 $this->VouchersModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);	
				}
				/***************  Start of EWB + E-Invoice *****************/
				$ewb_insert_data=["hobo_id"=>$this->bo_id,"cmp_id" =>$this->company_id,"ewb_no"=>"","ewb_date"=>$voucher_date,"ewb_supply_type"=>$ewb_supply_type,
								 "ewb_sub_supply_type"=>$ewb_sub_supply_type,"vch_txn_id"=>$voucher_txn_id,"hobo_id"=>$this->bo_id];
				$ewb_id  = $this->VouchersModel->add_ewb_data($ewb_insert_data);
				
				if($EinvoiceCheck){
					$einvoice_insert_data=["einv_no"=>0,"env_date"=>$voucher_date,"vch_txn_id"=>$voucher_txn_id,"cmp_id"=>$this->company_id];
					$einv_id = $this->VouchersModel->add_einvoice_data($einvoice_insert_data);
				$this->VouchersModel->add_ewb_txntype_data($ewb_sub_supply_type,$ewb_id,$einv_id,$voucher_txn_id,$_POST);
				
				$this->VouchersModel->add_transport_data($ewb_sub_supply_type,$ewb_id,$einv_id,$voucher_txn_id,$_POST);
				
				}
				
				
				
				/***************  End of EWB + E-Invoice *****************/
				
				
				/*********** Party Account Entries  *****************/
				  if($Voucher_TxnApproval!='' &&  parseAmount($voucher_invoice_value) > parseAmount($Voucher_TxnApproval)){
					 $acc_txn_type = 4; // voucher is pending for approval
					}
					 else
					 $acc_txn_type = 1;
					
					 $insert_data = [
					  "cmp_id"          => $this->company_id,
					  "vch_series_id"   => $voucher_series,
					  "vch_txn_id"      => $voucher_txn_id,
					  "master_id"       => $party_id,
					  'master_id_type'  => 'acc' 
					 ];
					$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
					
					$insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $party_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => parseAmount($voucher_invoice_value), 
					  'acc_txn_fcy'       => $voucher_fcy_invoice_value,
					  'vch_txn_id'        => $voucher_txn_id,
					  'txn_id'            => $main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => $acc_txn_type
					];  				
					$this->VouchersModel->add_acc_txn_data($insert_data);
				 /**********************************************************/
				 /********************* Start of Save Memorandum Entry **********************/
				 if($voucher_memo_value >0){
				 $memo_insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $party_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => $voucher_memo_value, 
					  'acc_txn_fcy'       => $voucher_fcy_memo_value,
					  'vch_txn_id'        => $voucher_txn_id,
					  'txn_id'            => $main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => 3
					];  				
					$this->VouchersModel->add_acc_txn_data($memo_insert_data);
				 }
					$long_register_insert = [
						'acct_vch_type'   => $voucher_type_id,
						'cmp_id'          => $this->company_id,
						'vch_txn_id'      => $voucher_txn_id,
						'txn_id'          => NULL,
						'vch_date'        => $voucher_date,					
						'acc_id'          => $party_id,
						'acc_txn_dr_amt'  => $voucher_invoice_value,
						'acc_txn_cr_amt'  => 0,
						'vch_narr'        => $long_narration ?? '',
						'hobo_id'         => $this->bo_id,
						'acc_txn_type'    => $acc_txn_type
					];
					$this->VouchersModel->add_register_txn_data($long_register_insert);
					
				 /*********************End of Save Memorandum Entry **********************/  
		   
			/*********************Start of Save Item Wise Grid Entries **********************/
		$itm_txn_type  = 1;	//1 for Regular
		$tax_igst_amt  = 0;
	    $tax_cgst_amt  = 0;	
	    $tax_sgst_amt  = 0;	
	    $tax_utgst_amt = 0;	
	    $tax_cess_amt  = 0;				
        if($itmsdata){			
			$item_account_array = [];
			$item_account_fcy_array = [];
        foreach($itmsdata as $item_row){
		    $inv_supply_id   = $item_row['supply_type_id'];
          $txn_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $item_row['item_id'],
				  'master_id_type'  => 'itm'
				 ];
          $main_txn_id  = $this->VouchersModel->add_comp_txn_data($txn_data);
		  /********************* Start of Save Memorandum Entry **********************/
				 if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){  
				 $memo_insert_data   = array(
					'cmp_id'             => $this->company_id,
					'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
					'itm_txn_qty'        => $item_row['item_qty'],
					'itm_txn_date'       => $voucher_date,
					'itm_txn_dr_cr'      => 2,
					'itm_txn_rate'       => 1,
					'itm_txn_amt'        => parseAmount($item_row['memo_amount']),
					'itm_txn_fcy'        => 0,
					'vch_txn_id'         => $voucher_txn_id,
					'txn_id'             => $main_txn_id,
					'mat_cent_id'        => $matrcntr_id,
					'hobo_id'            => $this->bo_id,
					'itm_txn_type'		 => 3		
					);	
				$this->VouchersModel->add_itm_txn_data($memo_insert_data);
				 }
		/********************* End of Save Memorandum Entry **********************/		
          if(isset($item_row['item_qty']) && $item_row['item_qty'] >0){
			$insert_data   = array(
				'cmp_id'             => $this->company_id,
				'itm_id_unit_id'     => $item_row['item_id'].'_'.$item_row['item_unit_id'],
				'itm_txn_qty'        => $item_row['item_qty'],
				'itm_txn_date'       => $voucher_date,
				'itm_txn_dr_cr'      => 2,
				'itm_txn_rate'       => parseAmountPrice($item_row['item_price'],4),
				'itm_txn_amt'        => parseAmount($item_row['item_total_amount']),
				'itm_txn_fcy'        => parseAmount($item_row['item_total_fcy_amount']),
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'mat_cent_id'        => $matrcntr_id,
				'hobo_id'            => $this->bo_id,
				'itm_txn_type'		 => $itm_txn_type		
			    );	
			 $this->VouchersModel->add_itm_txn_data($insert_data);

			 $narration            = $item_row['description'];
             $this->VouchersModel->save_voucher_narration($voucher_txn_id,$main_txn_id,'short',$narration);
			 /****************  Start of Save Tax Inclusive Data **********************/
						if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
						    $taxinc_insert_data  = [
                				  'cmp_id'            => $this->company_id,
                				  'acc_itm_id'        => $item_row['item_id'],                				 		 
                				  'acc_txn_inc_amt'   => $item_row['txinc_amount'], 
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $main_txn_id,
                				  'acc_txn_dr_cr'     => 2
                				]; 								
                				$this->VouchersModel->add_taxinc_txn_data($taxinc_insert_data);
						    
						}
						/****************  End of Save Tax Inclusive Data **********************/					
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
            
			$account_id  =  $item_row['item_sales_acc'];
            if(isset($item_account_array[$account_id]))
            $item_account_array[$account_id] += parseAmount($item_row['item_total_amount']);
           else
            $item_account_array[$account_id] = parseAmount($item_row['item_total_amount']);
		   if(isset($item_account_fcy_array[$account_id]))
			$item_account_fcy_array[$account_id] += parseAmount($item_row['item_total_fcy_amount']);
		   else
		   $item_account_fcy_array[$account_id] = parseAmount($item_row['item_total_fcy_amount']);
		
			/*******************Start of Save TAX SUMMARY ****************/
			$tax_cat_id       = $item_row['tax_cat_id'];
			$tax_detail_rates = $item_row['tax_details'];

			$amount     = parseAmount($item_row['item_total_amount']);      // taxable base
			$amount_fcy = parseAmount($item_row['item_total_fcy_amount']);  // taxable base
			$pos_code   = sprintf('%02d', $pos);

			// Item details
			$iteminfo = $this->VouchersModel->get_item_details_info($item_row['item_id']);
			$item_hsn = $iteminfo['itm_hsn'] ?? '';

			// ------------------------------------------------------------------
			// Effective rates (regular vs composition)
			// ------------------------------------------------------------------
			
			// Defaults (regular)
			$igst_rate_eff  = parseAmount($item_row['igst_rate']);
			$cgst_rate_eff  = $igst_rate_eff / 2;
			$sgst_rate_eff  = $igst_rate_eff / 2;
			$utgst_rate_eff = $igst_rate_eff / 2;
			$cess_rate_eff  = isset($tax_detail_rates['cess']) ? parseAmount($tax_detail_rates['cess']) : 0;

			// Override for composition (bo_gstin_type == 2) via helper
			if ((int)$gstinType === 2) {
				$comp = get_composition_rates($cmp_supply_type);
				$igst_rate_eff  = $comp['igst'];
				$cgst_rate_eff  = $comp['cgst'];
				$sgst_rate_eff  = $comp['sgst'];
				$utgst_rate_eff = $comp['ut'];
				$cess_rate_eff  = $comp['cess'] ?? 0;
			}

			// Default summary
			$taxsummary_data = [
				'cmp_id'             => $this->company_id,
				'vch_txn_id'         => $voucher_txn_id,
				'txn_id'             => $main_txn_id,
				'acc_bsd_id'         => $item_row['item_id'],
				'acc_bsd_type'       => 3,
				'vch_igst'           => 0,
				'vch_igst_rate'      => 0,
				'vch_cgst'           => 0,
				'vch_cgst_rate'      => 0,
				'vch_sgst_ugst'      => 0,
				'vch_sgst_ugst_rate' => 0,
				'vch_cess'           => 0,
				'vch_cess_rate'      => 0,
				'vch_taxable_value'  => $amount,
				'vch_total_tax'      => 0
			];

			// Case 1: IGST applies (different states)
			if ($bo_state_code != $pos_code) {
				$igst_value = ($amount * $igst_rate_eff) / 100;

				$taxsummary_data['vch_igst']      = $igst_value;
				$taxsummary_data['vch_igst_rate'] = $igst_rate_eff;
				$taxsummary_data['vch_total_tax'] = $igst_value;
				$tax_igst_amt += $igst_value;
			}
			// Case 2: CGST + SGST applies (same state)
			elseif ($bo_state_code == $pos_code) {
				$cgst_value = ($amount * $cgst_rate_eff) / 100;
				$sgst_value = ($amount * $sgst_rate_eff) / 100;

				$taxsummary_data['vch_cgst']           = $cgst_value;
				$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
				$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
				$taxsummary_data['vch_sgst_ugst_rate'] = $sgst_rate_eff;
				$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

				$tax_cgst_amt += $cgst_value;
				$tax_sgst_amt += $sgst_value;
			}
			// Case 3: UGST applies
			elseif (in_array($pos_code, $ugst_states)) {
				$cgst_value = ($amount * $cgst_rate_eff) / 100;
				$ugst_value = ($amount * $utgst_rate_eff) / 100;

				$taxsummary_data['vch_cgst']           = $cgst_value;
				$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
				$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
				$taxsummary_data['vch_sgst_ugst_rate'] = $utgst_rate_eff;
				$taxsummary_data['vch_total_tax']      = ($cgst_value + $ugst_value);

				$tax_cgst_amt += $cgst_value;
				$tax_utgst_amt += $ugst_value;
			}

			// Always add cess if applicable (effective rate already set)
			if ($cess_rate_eff > 0) {
				$cess_value = ($amount * $cess_rate_eff) / 100;
				$taxsummary_data['vch_cess']      = $cess_value;
				$taxsummary_data['vch_cess_rate'] = $cess_rate_eff;
				$taxsummary_data['vch_total_tax'] += $cess_value;
				$tax_cess_amt += $cess_value;
			}

			$gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
				? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : null))
				: ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);

			$taxsummary_data['vch_date']      = $voucher_date;
			$taxsummary_data['is_outward']    = 1;
			$taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
			$taxsummary_data['vch_hsn_sac']   = $item_hsn;
			$taxsummary_data['inv_supply_id'] = $inv_supply_id;

			$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

			$hsnummary_data = [
				'cmp_id'                => $this->company_id,
				'vch_txn_id'            => $voucher_txn_id,
				'txn_id'                => $main_txn_id,
				'vch_hsn_sac'           => $item_hsn,
				'vch_hsn_sac_taxbl_val' => $amount,
				'vch_hsn_sac_qty'       => $item_row['item_qty'],
				'vch_hsn_sac_uom'       => $item_row['item_unit_id'],
				'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
				'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
				'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
				'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
				'inv_supply_id'         => $inv_supply_id,
				'vch_gst_sum_id'        => $vch_gst_sum_id
			];
			$this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);

			/*********  FCY Tax Summary ****/
			if ($fcy_forex_rate > 0) {
				$taxsummary_fcy_data = [
					'cmp_id'                => $this->company_id,
					'vch_txn_id'            => $voucher_txn_id,
					'txn_id'                => $main_txn_id,
					'acc_bsd_id'            => $item_row['item_id'],
					'acc_bsd_type'          => 3,
					'vch_igst_fcy'          => 0,
					'vch_cgst_fcy'          => 0,
					'vch_sgst_ugst_fcy'     => 0,
					'vch_cess_fcy'          => 0,
					'vch_taxable_value_fcy' => $amount_fcy,
					'vch_total_tax_fcy'     => 0
				];

				if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
					$igst_value = ($amount_fcy * $igst_rate_eff) / 100;
					$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
				}
				elseif ($bo_state_code == $pos_code) {
					$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;
					$sgst_value = ($amount_fcy * $sgst_rate_eff) / 100;

					$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
					$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;
				}
				elseif (in_array($pos_code, $ugst_states)) {
					$ugst_value = ($amount_fcy * $utgst_rate_eff) / 100;
					$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;

					$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] = $ugst_value;
				}

				if ($cess_rate_eff > 0) {
					$cess_value = ($amount_fcy * $cess_rate_eff) / 100;
					$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
					$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
				}

				$this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
			}
			/*******************End of Save TAX SUMMARY ****************/
			}
		  }
		foreach ($item_account_array as $account_id => $amount) {	
			if(isset($item_account_fcy_array[$account_id]))
			  $amount_fcy=$item_account_fcy_array[$account_id];
              else
		      $amount_fcy=0;	

			$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $account_id,
							  'master_id_type'  => 'acc'
							 ];
			$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $account_id,
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => 2,
							  'acc_txn_amt'       => $amount, 
							  'acc_txn_fcy'       => $amount_fcy,
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
			$this->VouchersModel->add_acc_txn_data($acc_txn_data);	
		 }
		  
	   } 
	   /*********************End of Save Item Wise Grid Entries **********************/
		/*********************Start of Save Billsundry Wise Grid Entries **********************/
			   if($billsndrydata) {
			        foreach($billsndrydata as $item_row) {
						/******************* Save MEMO TXN ****************/
						$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $item_row['billsundry_id'],
							  'master_id_type'  => 'bsd'
							 ];
						$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
						if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){	
						        $memo_txn_data  = [
								  'cmp_id'            => $this->company_id,
								  'acc_id'            => $item_row['billsundry_id'],
								  'acc_txn_date'      => $voucher_date,
								  'acc_txn_dr_cr'     => ($item_row['memo_amount']<0) ? 2 : 1,
								  'acc_txn_amt'       => $item_row['memo_amount'], 
								  'acc_txn_fcy'       => (parseAmount($fcy_forex_rate) >0)?($item_row['memo_amount']*$fcy_forex_rate):0,
								  'vch_txn_id'        => $voucher_txn_id,
								  'txn_id'            => $txn_id,
								  'hobo_id'           => $this->bo_id,
								  'acc_txn_type'      => 3
								];  				
								$this->VouchersModel->add_acc_txn_data($memo_txn_data);
						}
						/******************* Save Account TXN ****************/
						$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $item_row['billsundry_id'],
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => ($item_row['billsundry_amount']<0) ? 2 : 1,
							  'acc_txn_amt'       => $item_row['billsundry_amount'], 
							  'acc_txn_fcy'       => $item_row['billsundry_fcy_amount'],
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
							$this->VouchersModel->add_acc_txn_data($acc_txn_data);
							
										
                		    $narration            = "";
                		    $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
							
						
						 /*******************Start of Save TAX SUMMARY ****************/
						 $tax_cat_id              = $item_row['tax_cat_id'];
						 $tax_detail_rates        = $item_row['tax_details'];
						 
						 $amount                  = parseAmount($item_row['billsundry_amount']); // taxable base
						 $amount_fcy              = parseAmount($item_row['billsundry_fcy_amount']); // taxable base
                         $pos_code                = sprintf( '%02d', $pos );
                         $billsundry_info         = $this->VouchersModel->get_bsd_details_info($item_row['billsundry_id']);
						 $bsd_hsn                 = $billsundry_info['bsd_hsn_sac'] ?? '';
                         $taxsummary_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst'           => 0,
                            'vch_igst_rate'      => 0,
                            'vch_cgst'           => 0,
                            'vch_cgst_rate'      => 0,
                            'vch_sgst_ugst'      => 0,
                            'vch_sgst_ugst_rate' => 0,
                            'vch_cess'           => 0,
                            'vch_cess_rate'      => 0,
                            'vch_taxable_value'  => $amount,
                            'vch_total_tax'      => 0
                        ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                            $igst_value = ($amount * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_data['vch_igst']      = $igst_value;
                            $taxsummary_data['vch_igst_rate'] = $item_row['igst_rate'];
                            $taxsummary_data['vch_total_tax'] = $igst_value;
                            
                            $tax_igst_amt +=$igst_value;
                           
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_data['vch_cgst']           = $cgst_value;
                            $taxsummary_data['vch_cgst_rate']      = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_sgst_ugst']      = $sgst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;
                            
                            $tax_cgst_amt += $cgst_value;
                            $tax_sgst_amt +=  $sgst_value;
                            
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount * $item_row['igst_rate']/2) / 100;
							$cgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                            $taxsummary_data['vch_sgst_ugst']      = $ugst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $ugst_value;
                            
							$tax_cgst_amt += $cgst_value;
                            $tax_utgst_amt += $ugst_value;
                           
                        }
                    
                        // Always add cess if applicable
                       $cess_rate=0;
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
							$cess_rate=$tax_detail_rates['cess'];
                            $cess_value = ($amount * $tax_detail_rates['cess']) / 100;
                            $taxsummary_data['vch_cess']      = $cess_value;
                            $taxsummary_data['vch_cess_rate'] = $tax_detail_rates['cess'];
                            $taxsummary_data['vch_total_tax'] += $cess_value;
                            
                            $tax_cess_amt += $cess_value;
                           
                        }
                        //echo '<pre>';
                       // print_r($taxsummary_data);
					    $taxsummary_data['vch_date']      = $voucher_date;
						$taxsummary_data['is_outward']    = 1;				 
						$taxsummary_data['gst_rate_grp']  = '1,'.parseAmount($item_row['igst_rate']).','.parseAmount($cess_rate);
						$taxsummary_data['vch_hsn_sac']   = $bsd_hsn;
						$taxsummary_data['inv_supply_id']   = 0;
						
						$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data,$tax_required_flag);   
						$hsnummary_data = [ 
							'cmp_id'                => $this->company_id,
							'vch_txn_id'            => $voucher_txn_id,
							'txn_id'                => $txn_id,
							'vch_hsn_sac'           => $bsd_hsn,
							'vch_hsn_sac_taxbl_val' => $amount,
							'vch_hsn_sac_qty'       => 0,
							'vch_hsn_sac_uom'       => 0,
							'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
							'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
							'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
							'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
							'inv_supply_id '        => 0,
							'vch_gst_sum_id'        => $vch_gst_sum_id
							];			  
					    $this->VouchersModel->add_hsnsummary_data($hsnummary_data,$tax_required_flag);	  
						  
					    if(parseAmount($fcy_forex_rate) >0){
						// Default summary
                        $taxsummary_fcy_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst_fcy'       => 0,
                            'vch_cgst_fcy'       => 0,
                            'vch_sgst_ugst_fcy'  => 0,
                            'vch_cess_fcy'       => 0,
                            'vch_taxable_value_fcy'  => $amount_fcy,
                            'vch_total_tax_fcy'      => 0
                        ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            $igst_value = ($amount_fcy * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_fcy_data['vch_igst_fcy'] = $igst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_fcy_data['vch_cgst_fcy']           = $cgst_value;
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $sgst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $cgst_value + $sgst_value;
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
							$cgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $ugst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $ugst_value;
                        }
                    
                        // Always add cess if applicable
                       
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
                            $cess_value = ($amount_fcy * $tax_detail_rates['cess']) / 100;
                            $taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
                        } 
						      
						   $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);    
						}
						  
						 /*******************End of Save TAX SUMMARY ****************/
					}
				}		 
		/*********************End of Save Billsundry Wise Grid Entries **********************/
			 
		/***************Start of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			SaveErrorLog("Company is gstin type check => ".(int)$gstinType);
			if ((int)$gstinType === 1) { 
				if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_utgst_amt,4,$voucher_date,2);
				   }		
                 if($tax_cess_amt > 0)				   
				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cess_amt,5,$voucher_date,2);
            }		
			if($gstinType==2 ){
				 if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_utgst_amt,4,$voucher_date,2);
				   }		    
				 if($tax_cess_amt > 0)  
				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cess_amt,5,$voucher_date,2);
		    }		
		   /*************** End of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			
			
			
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
			   
		    /***********   Save Bill By Bill Data  *************/
		if(count($bbbdata)){		
		 $this->VouchersModel->SaveBillByBillData($bbbdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		/***********   Save Project Reporting  Data  *************/
		if(count($prdata)){		
		 $this->VouchersModel->SaveProjectReportingData($prdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		
		/***********   Save Item Batch  Data  *************/
		if(count($batchdata)){		
		 $this->VouchersModel->SaveBatchData($batchdata,$voucher_date,$voucher_txn_id,$main_txn_id,$matrcntr_id);
		}
		
		$this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	  $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  /************ Save Voucher Activity Log **********/
	    $voucher_messages = [
			18  => "New sale with item voucher has been added by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			18  => "sale with item voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
		];
		$erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been added by [USERNAME]([UUID])";
		$this->VouchersModel->SaveUserActivity($erp_activity_log,$voucher_txn_id);
		 
	    }
		 catch (\Throwable $e) {
					return  ['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
					// Get line, file and stack trace
					log_message('error', 'DB Error: ' . $e->getMessage());
					log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
					log_message('error', 'Trace: ' . $e->getTraceAsString());
			} 
	    });
		
		
		 $trans_result['vchrtxnid'] = $voucher_txn_id; 
		 
	 if(isset($trans_result['result']['status']) && $trans_result['result']['status']==''){            
					if(isset($trans_result['result']['errors']))
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>$trans_result['result']['errors']]);
					else
					return  $this->response->setJSON(['status' => false, 'message' =>$trans_result['result']['message'],'errors' =>'']);
				}

	  else
         return $this->response->setJSON($trans_result);  
	
	  }		  
      if($vch_subtype_id==0){  // For Non Item Code
	  $forceData = $this->request->getPost();
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
			];			
	       if(!$this->validateData($forceData,$rules)){
			$errors = $this->validator->getErrors();
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		   }		   
		    $items_data_val      = $forceData['itmsdata'] ?? $this->request->getVar('itmsdata');		
	        $trans_result = $this->runTransaction(function($db) use ($items_data_val,$voucher_txn_id,$bo_state_code,$ugst_states,$taxes_list,$voucher_type_id,$Voucher_TxnApproval_UUID,$Voucher_TxnApproval){
			$gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);	  
		    $invoice_type        = $forceData['invoice_type'] ?? $this->request->getVar('invoice_type');
			$voucher_date        = $forceData['voucher_date'] ?? $this->request->getVar('voucher_date');
		 	$voucher_date 	     = validate_date_by_fy($voucher_date);
		    $voucher_series      = $forceData['voucher_series'] ?? $this->request->getVar('voucher_series'); 
			$party_id            = $forceData['party_id'] ?? $this->request->getVar('party_id');  
			$long_narration      = $forceData['narration'] ?? $this->request->getVar('narration'); 
			$itmsdata            = json_decode($items_data_val,true);             		
			$currency_id 	     = $forceData['currency_id'] ?? $this->request->getVar('currency_id');			 
			$billno 		     = $forceData['billno'] ?? $this->request->getVar('billno');
			$supply_type         = $forceData['supply_type'] ?? $this->request->getVar('supply_type');
			$tax_type_id         = $forceData['tax_type_id'] ?? $this->request->getVar('tax_type_id');
			$reverse_charges     = $forceData['reverse_charges'] ?? $this->request->getVar('reverse_charges');
			$pos                 = $forceData['pos'] ?? $this->request->getVar('pos');
			$fcy_forex_rate      = $forceData['fcy_forex_rate'] ?? $this->request->getVar('fcy_forex_rate');
			$memoCheck           = $forceData['memoCheck'] ?? $this->request->getVar('memoCheck');			
			$taxInclusive        = $forceData['taxInclusive'] ?? $this->request->getVar('taxInclusive');
			$outsup_eco          = $forceData['eco_id'] ?? $this->request->getVar('eco_id') ?? 0;
			$draft_vch_rec_id    = $forceData['draft_vch_rec_id'] ?? $this->request->getVar('draft_vch_rec_id');
			$btnid               = $forceData['btnid'] ?? $this->request->getVar('btnid');// submitbtn default save button
			$tax_required_flag   = $forceData['tax_required_flag'] ?? $this->request->getVar('tax_required_flag'); 
			$cmp_supply_type     = $forceData['cmp_suply_type'] ??  $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null
				// EWB fields
			$ewb_supply_type     = $forceData['supply_type'] ?? $this->request->getVar('supply_type'); 
			$ewb_sub_supply_type = $forceData['tax_type_id'] ?? $this->request->getVar('tax_type_id');
			$ewb_sub_supply_desc = '';//$this->request->getVar('supply_type_desc');			
			$EinvoiceCheck        = $forceData['EINVOICECheck'] ?? $this->request->getVar('EINVOICECheck'); 
			
			$sbnum      = $forceData['sbnum'] ??  $this->request->getVar('sbnum'); 
			$sbpcode    = $forceData['sbpcode'] ??  $this->request->getVar('sbpcode');  
			$sbdt       = $forceData['sbdt'] ??  $this->request->getVar('sbdt');
			
			$isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			   if($billno=="")
				return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']];
				$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'sale',1,$voucher_txn_id);
				if($billno_duplicate=="1")
				return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']];
			}
	     try{ 
			$billsndrydata = [];
			if(!empty($forceData['billsndrydata'] ?? $this->request->getVar('billsndrydata')))
			$billsndrydata    = json_decode($forceData['billsndrydata'] ?? $this->request->getVar('billsndrydata'),true);
			
			$bbbdata = [];
			if(!empty($forceData['bbbdata'] ?? $this->request->getVar('bbbdata')))
			$bbbdata = json_decode($forceData['bbbdata'] ?? $this->request->getVar('bbbdata'),true);

			$prdata = [];
			if(!empty($forceData['prdata'] ?? $this->request->getVar('prdata')))
			 $prdata = json_decode($forceData['prdata'] ?? $this->request->getVar('prdata'),true);
			 
			 $is_bbb=FALSE;
	     	if($bbbdata)
              $is_bbb=TRUE;			
	   
	       $is_pr=FALSE;
		   if($prdata)
              $is_pr=TRUE;
           
            $vch_particulars='';
            if($party_id){
              $acc_info = $this->VouchersModel->account_detail_info($party_id);
			  $vch_particulars = $acc_info['acc_name']; 
            }
       

			$voucher_no = $this->VouchersModel->get_voucher_no($voucher_type_id);
			$sale_total = array_sum(array_column($itmsdata ?? [], 'amount'));
			//SaveErrorLog("📌 sale_total".$sale_total.'===='.json_encode($itmsdata));
			$sale_fcy_total = array_sum(array_column($itmsdata, 'amountfc'));
			$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
			$billsundry_fcy_total = array_sum(array_column($billsndrydata, 'billsundry_fcy_amount'));
			
			$sale_memo_total  = array_sum(array_column($itmsdata, 'memo_amount'));
			$billsundry_memo_total = array_sum(array_column($billsndrydata, 'memo_amount'));
			$sale_fcy_memo_total = 0;
			$billsundry_fcy_memo_total = 0;
			
			$total_tax_amount =0;
			$total_fcy_tax_amount =0;
		    if($tax_required_flag==1){
	    	$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc')['fcy_tax_amount'];
	        
	        $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['fcy_tax_amount'];
			}
	        
	        $voucherdata     = json_encode(["itmsdata"=>$itmsdata,"billsndrydata"=>$billsndrydata]);
	        
	        if($draft_vch_rec_id >0){
	          $pgrdata_drft    = array(
		                         "log_date_time"=>$voucher_date,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($sale_total+$billsundry_total),"vch_fcy_amt"=>parseAmount($sale_fcy_total+$billsundry_fcy_total),"vch_fcy_rate"=>$fcy_forex_rate,
								 "vch_particulars"=>$vch_particulars,
								 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
								 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
					            );  
	          $this->VouchersModel->UpdateDrftVoucher($draft_vch_rec_id,$pgrdata_drft);  
	        }
	        else{
	         $pgrdata_drft    = array("cmp_id"=>$this->company_id,"hobo_id"=>$this->bo_id,
		                         "log_date_time"=>$voucher_date,"vch_type_id"=>$voucher_type_id,
					             "vch_json_data"=>$voucherdata,"vch_amt"=>parseAmount($sale_total+$billsundry_total),"vch_fcy_amt"=>parseAmount($sale_fcy_total+$billsundry_fcy_total),"vch_fcy_rate"=>(float)$fcy_forex_rate ?? 0,
								 "vch_particulars"=>$vch_particulars,"uuid_aictly"=>$this->session->get('uuid'),
								 "long_narr"=>$long_narration,"vch_series_id"=>(int)$voucher_series,"isoptional"=>FALSE,
								 "vch_fcy_id"=>(int)$currency_id,"is_cc"=>0,"is_bbb"=>(int)$is_bbb,"is_pr"=>(int)$is_pr,"is_sblgr"=>0
					            ); 
						
	     	$draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);   
	            
	        }
		    if($btnid=='submitbtn_drft'){
		      return ['status' => true, 'message' => 'Voucher saved in draft mode']; 
	         }
	        
	        /* Remove Old Entries using vch_txn_id */
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');	
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'accttxnmst');
           $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
    	   $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_type_id);
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'billtxnmst');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cctxnmstnn');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'prjtxnmstn');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'subacctxnm');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchfcyrate');
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchgstfcyn');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchhsnsacn'); 
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchgstsumn');  
    	   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'acctamtinc'); 
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'batchtxnmt');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'einvmaster'); 
	       $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'ewbmastern'); 			
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'gstdispfrm'); 
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'gstshipton'); 
		   $this->VouchersModel->clear_system_journal_txn_data($voucher_txn_id,4);
	   /******************     ***********/
	    $voucher_invoice_value = $sale_total + $billsundry_total + $total_tax_amount;		
		$voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;            
		$voucher_memo_value = $sale_memo_total + $billsundry_memo_total;
		$voucher_fcy_memo_value = (parseAmount($fcy_forex_rate) >0)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0;
        $pos_code                = sprintf( '%02d', $pos );   
	   /******* start of  if gstinType ==2 means composition then insert new voucher type 23  ********/
			if($gstinType==2){
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
				$total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];	        
				$total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
				$total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,0,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
			    
				$get_voucher_bridge_info = $this->VouchersModel->get_voucher_bridge_info($voucher_txn_id,4);
				if(!$get_voucher_bridge_info)
				$comp_voucher_txn_id = $voucher_txn_id;
			    else
			    $comp_voucher_txn_id     = $get_voucher_bridge_info['vch_txn_id_dest'];
				$gstpaidacc_id           = $this->VouchersModel->getGstPaidAccountId();
				$comp_vch_type_id        = 23;
				$voucher_series_id       =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				$update_data  = array(
				 	  
				  "vch_series_id"    => $voucher_series_id,          
				  "vch_type_id"      => $comp_vch_type_id,
				  "vch_sub_type_id"  => 0,		   
				  "vch_date"         => $voucher_date,
				  "mat_cent_id"      => 0,
				  "draft_vch_rec_id" => $draft_vch_rec_id
				 );		
			  $this->VouchersModel->update_comps_voucher_cons_data($update_data,$comp_voucher_txn_id,$this->company_id,$comp_vch_type_id);
			  $insert_data = [
					  "cmp_id"          => $this->company_id,
					  "vch_series_id"   => $voucher_series_id,
					  "vch_txn_id"      => $comp_voucher_txn_id,
					  "master_id"       => $gstpaidacc_id,
					  'master_id_type'  => 'acc'
					 ];
			  $comp_main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
			  $nr_insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series_id,
				  "vch_txn_id"      => $comp_voucher_txn_id,
				  "master_id"       => 0,
				  'master_id_type'  => 'nrr'
				 ];
			 $txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
			 $this->VouchersModel->save_voucher_narration($comp_voucher_txn_id,$comp_main_txn_id,'long',$long_narration);
			  // now save txn entry to GSTIN PAID ACCOUNT, we will create this account at the time of company creation
			  $insert_data  = [
					  'cmp_id'            => $this->company_id,
					  'acc_id'            => $gstpaidacc_id,
					  'acc_txn_date'      => $voucher_date,
					  'acc_txn_dr_cr'     => 1,
					  'acc_txn_amt'       => $total_tax_amount, 
					  'acc_txn_fcy'       => $total_fcy_tax_amount,
					  'vch_txn_id'        => $comp_voucher_txn_id,
					  'txn_id'            => $comp_main_txn_id,
					  'hobo_id'           => $this->bo_id,
					  'acc_txn_type'      => 1
					];  				
					$this->VouchersModel->add_acc_txn_data($insert_data);	

				$long_register_insert = [
						'acct_vch_type'   => $comp_vch_type_id,
						'cmp_id'          => $this->company_id,
						'vch_txn_id'      => $comp_voucher_txn_id,
						'txn_id'          => NULL,
						'vch_date'        => $voucher_date,					
						'acc_id'          => $gstpaidacc_id,
						'acc_txn_dr_amt'  => $total_tax_amount,
						'acc_txn_cr_amt'  => 0,
						'vch_narr'        => $long_narration ?? '',
						'hobo_id'         => $this->bo_id,
						'acc_txn_type'    => 1
					];
					$this->VouchersModel->add_register_txn_data($long_register_insert);			
			}
			/******* end of  if gstinType ==2 means composition **********/
			
	   $update_data  = array(
          "vch_series_id"   => $voucher_series,
          "vch_date"         => $voucher_date,
          "draft_vch_rec_id" => $draft_vch_rec_id		  
        );
        $this->VouchersModel->update_voucher_cons_data($update_data,$voucher_txn_id,$this->company_id);
		// make entry in baridge table if composition is set as 2 
                if($gstinType==2){ 
					$brdge_update_data  = array(
									  "vch_txn_id_src"      => $voucher_txn_id
									 );									 
					$this->VouchersModel->update_voucher_bridge_data($brdge_update_data,$comp_voucher_txn_id,4);
				}		

		//SaveErrorLog("📌 update_voucher_cons_data".json_encode($update_data));		
		  if(parseAmount($fcy_forex_rate) >0){
			$fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
			                   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
							  );	
		    $this->VouchersModel->save_voucher_fcyrate($fcy_data);	
			//SaveErrorLog("📌 save_voucher_fcyrate".json_encode($fcy_data));	
		    } else{				
			$this->VouchersModel->clear_voucher_fcyrate($voucher_txn_id);
			//SaveErrorLog("📌 clear_voucher_fcyrate".$voucher_txn_id);	
			} 
			
			if($isvoucher_autobillno==0){
			 $gstroutsup_insert_data = array(
											"outsup_pos"=>$pos,"outsup_rev_chg"=>$reverse_charges,
											"dr_note_inwsup_id"=>0,"outsup_eco"=>(int)$outsup_eco,
											"exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode,
											"gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
			 
			}
			else if($isvoucher_autobillno==1){
			 $outsup_bill_ref_no = $this->VouchersModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,1,'counter');	
			 $gstroutsup_insert_data = array(
											"outsup_pos"=>$pos,"outsup_rev_chg"=>$reverse_charges,
											"dr_note_inwsup_id"=>0,"outsup_eco"=>(int)$outsup_eco,
											"exp_sb_no"=>$sbnum,"exp_sb_date"=>date("Y-m-d",strtotime($sbdt)),"exp_port_code"=>$sbpcode,
											"gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->update_gstroutsup_data($voucher_txn_id,$gstroutsup_insert_data);
				
			}		
			
			/***************  Start of EWB + E-Invoice *****************/
				$ewb_insert_data=["hobo_id"=>$this->bo_id,"cmp_id" =>$this->company_id,"ewb_no"=>"","ewb_date"=>$voucher_date,"ewb_supply_type"=>$ewb_supply_type,
								 "ewb_sub_supply_type"=>$ewb_sub_supply_type,"vch_txn_id"=>$voucher_txn_id,"hobo_id"=>$this->bo_id];
				$ewb_id  = $this->VouchersModel->add_ewb_data($ewb_insert_data);
				if($EinvoiceCheck){
					$einvoice_insert_data=["cmp_id" =>$this->company_id,"einv_no"=>0,"env_date"=>$voucher_date,"vch_txn_id"=>$voucher_txn_id];
					$einv_id = $this->VouchersModel->add_einvoice_data($einvoice_insert_data);
				    $this->VouchersModel->add_ewb_txntype_data($ewb_sub_supply_type,$ewb_id,$einv_id,$voucher_txn_id,$_POST);
				}
				
				/***************  End of EWB + E-Invoice *****************/
				
				
		 	 
			/*********** Party Account Entries  *****************/
			  if($Voucher_TxnApproval!='' && parseAmount($voucher_invoice_value) > parseAmount($Voucher_TxnApproval)){
				 $acc_txn_type = 4; // voucher is pending for approval
				}
				 else
				 $acc_txn_type = 1;
				
				 $insert_data = [
				  "cmp_id"          => $this->company_id,
				  "vch_series_id"   => $voucher_series,
				  "vch_txn_id"      => $voucher_txn_id,
				  "master_id"       => $party_id,
				  'master_id_type'  => 'acc'
				 ];
				$main_txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
				//SaveErrorLog("📌 add_comp_txn_data".json_encode($insert_data));
				$insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $party_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 1,
				  'acc_txn_amt'       => $voucher_invoice_value, 
				  'acc_txn_fcy'       => $voucher_fcy_invoice_value,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $main_txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => $acc_txn_type
				];  				
				$this->VouchersModel->add_acc_txn_data($insert_data);
				//SaveErrorLog("📌 add_acc_txn_data".json_encode($insert_data));
			 /**********************************************************/
			 /********************* Start of Save Memorandum Entry **********************/
			 $memo_insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $party_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 1,
				  'acc_txn_amt'       => $voucher_memo_value, 
				  'acc_txn_fcy'       => $voucher_fcy_memo_value,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $main_txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 3
				];  				
				$this->VouchersModel->add_acc_txn_data($memo_insert_data);
				//SaveErrorLog("📌memo_insert_data add_acc_txn_data".json_encode($memo_insert_data));
				$long_register_insert = [
					'acct_vch_type'   => $voucher_type_id,
					'cmp_id'          => $this->company_id,
					'vch_txn_id'      => $voucher_txn_id,
					'txn_id'          => NULL,
					'vch_date'        => $voucher_date,					
					'acc_id'          => $party_id,
					'acc_txn_dr_amt'  => $voucher_invoice_value,
					'acc_txn_cr_amt'  => 0,
					'vch_narr'        => $long_narration ?? '',
					'hobo_id'         => $this->bo_id,
					'acc_txn_type'    => $acc_txn_type
				];
				$this->VouchersModel->add_register_txn_data($long_register_insert);
			//	SaveErrorLog("📌add_register_txn_data add_acc_txn_data".json_encode($long_register_insert));
			 /*********************End of Save Memorandum Entry **********************/
			 
			 /*********************Start of Save Account Wise Grid Entries **********************/
			    $tax_igst_amt  = 0;
				$tax_cgst_amt  = 0;	
				$tax_sgst_amt  = 0;	
				$tax_utgst_amt = 0;	
				$tax_cess_amt  = 0;
				if($itmsdata) {
			        foreach($itmsdata as $item_row) {
						$inv_supply_id   = $item_row['supply_type_id'];
						/******************* Save MEMO TXN ****************/
						$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $item_row['account_id'],
							  'master_id_type'  => 'acc'
							 ];
						$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
						//SaveErrorLog("📌add_comp_txn_data".json_encode($insert_data));
						
						if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){	
						        $memo_txn_data  = [
								  'cmp_id'            => $this->company_id,
								  'acc_id'            => $item_row['account_id'],
								  'acc_txn_date'      => $voucher_date,
								  'acc_txn_dr_cr'     => 2,
								  'acc_txn_amt'       => $item_row['memo_amount'], 
								  'acc_txn_fcy'       => (parseAmount($fcy_forex_rate) >0)?($item_row['memo_amount']*$fcy_forex_rate):0,
								  'vch_txn_id'        => $voucher_txn_id,
								  'txn_id'            => $txn_id,
								  'hobo_id'           => $this->bo_id,
								  'acc_txn_type'      => 3
								];  				
								$this->VouchersModel->add_acc_txn_data($memo_txn_data);
								//SaveErrorLog("📌memo_amount  add_acc_txn_data".json_encode($memo_txn_data));
						}
						/******************* Save Account TXN ****************/
						$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $item_row['account_id'],
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => 2,
							  'acc_txn_amt'       => $item_row['amount'], 
							  'acc_txn_fcy'       => $item_row['amountfc'],
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
							$this->VouchersModel->add_acc_txn_data($acc_txn_data);
							
							$register_insert_data  = [
                				  'acct_vch_type'     => $voucher_type_id,
                				  'cmp_id'            => $this->company_id,
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $txn_id,
                				  'vch_date'          => $voucher_date,				 
                				  'acc_id'            => $item_row['account_id'],
                				  'acc_txn_cr_amt'    => $item_row['amount'], 
                				  'acc_txn_dr_amt'    => 0, 
                				  'vch_narr'          => $item_row['description'],
                				  'hobo_id'           => $this->bo_id,
                				  'acc_txn_type'      => $acc_txn_type
                				]; 								
                			$this->VouchersModel->add_register_txn_data($register_insert_data);				
                		    $narration            = $item_row['description'];
                		    $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
												
							/****************  Start of Save Tax Inclusive Data **********************/
						if($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] >0){ 
						    $taxinc_insert_data  = [
                				  'cmp_id'            => $this->company_id,
                				  'acc_itm_id'        => $item_row['account_id'],
                				  'acc_txn_inc_amt'   => $item_row['txinc_amount'], 
                				  'vch_txn_id'        => $voucher_txn_id,
                				  'txn_id'            => $txn_id,
                				  'acc_txn_dr_cr'     => 2
                				]; 								
                				$this->VouchersModel->add_taxinc_txn_data($taxinc_insert_data);
						    
						}
						/****************  End of Save Tax Inclusive Data **********************/
						
							
						
						 /*******************Start of Save TAX SUMMARY ****************/
						$tax_cat_id       = $item_row['tax_cat_id'];
						$tax_detail_rates = $item_row['tax_details'];

						$amount     = parseAmount($item_row['amount']);    // taxable base
						$amount_fcy = parseAmount($item_row['amountfc']);  // taxable base (FCY)
						$pos_code   = sprintf('%02d', $pos);

						// Account/HSN details
						$iteminfo = $this->VouchersModel->get_acc_details_info($item_row['account_id']);
						$item_hsn = $iteminfo['acc_sac'] ?? '';

						// ------------------------------------------------------------------
						// Effective rates (regular vs composition)
						// ------------------------------------------------------------------
						
						// Defaults (regular)
						$igst_rate_eff  = parseAmount($item_row['igst_rate']);
						$cgst_rate_eff  = $igst_rate_eff / 2;
						$sgst_rate_eff  = $igst_rate_eff / 2;
						$utgst_rate_eff = $igst_rate_eff / 2;
						$cess_rate_eff  = isset($tax_detail_rates['cess']) ? parseAmount($tax_detail_rates['cess']) : 0;

						// Override for composition (bo_gstin_type == 2) via helper
						if ((int)$gstinType === 2) {
							$comp = get_composition_rates($cmp_supply_type);
							$igst_rate_eff  = $comp['igst'];
							$cgst_rate_eff  = $comp['cgst'];
							$sgst_rate_eff  = $comp['sgst'];
							$utgst_rate_eff = $comp['ut'];
							$cess_rate_eff  = $comp['cess'] ?? 0;
						}

						// Default summary
						$taxsummary_data = [
							'cmp_id'             => $this->company_id,
							'vch_txn_id'         => $voucher_txn_id,
							'txn_id'             => $txn_id,
							'acc_bsd_id'         => $item_row['account_id'],
							'acc_bsd_type'       => 1,
							'vch_igst'           => 0,
							'vch_igst_rate'      => 0,
							'vch_cgst'           => 0,
							'vch_cgst_rate'      => 0,
							'vch_sgst_ugst'      => 0,
							'vch_sgst_ugst_rate' => 0,
							'vch_cess'           => 0,
							'vch_cess_rate'      => 0,
							'vch_taxable_value'  => $amount,
							'vch_total_tax'      => 0
						];

						// Case 1: IGST applies (different states)
						if ($bo_state_code != $pos_code) {
							$igst_value = ($amount * $igst_rate_eff) / 100;

							$taxsummary_data['vch_igst']      = $igst_value;
							$taxsummary_data['vch_igst_rate'] = $igst_rate_eff;
							$taxsummary_data['vch_total_tax'] = $igst_value;
							$tax_igst_amt += $igst_value;
						}
						// Case 2: CGST + SGST applies (same state)
						elseif ($bo_state_code == $pos_code) {
							$cgst_value = ($amount * $cgst_rate_eff) / 100;
							$sgst_value = ($amount * $sgst_rate_eff) / 100;

							$taxsummary_data['vch_cgst']           = $cgst_value;
							$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
							$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
							$taxsummary_data['vch_sgst_ugst_rate'] = $sgst_rate_eff;
							$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

							$tax_cgst_amt += $cgst_value;
							$tax_sgst_amt += $sgst_value;
						}
						// Case 3: UGST applies
						elseif (in_array($pos_code, $ugst_states)) {
							$cgst_value = ($amount * $cgst_rate_eff) / 100;
							$ugst_value = ($amount * $utgst_rate_eff) / 100;

							$taxsummary_data['vch_cgst']           = $cgst_value;
							$taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
							$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
							$taxsummary_data['vch_sgst_ugst_rate'] = $utgst_rate_eff;
							$taxsummary_data['vch_total_tax']      = ($cgst_value + $ugst_value);

							$tax_cgst_amt += $cgst_value;
							$tax_utgst_amt += $ugst_value;
						}

						// Always add cess if applicable (effective rate already set)
						if ($cess_rate_eff > 0) {
							$cess_value = ($amount * $cess_rate_eff) / 100;
							$taxsummary_data['vch_cess']      = $cess_value;
							$taxsummary_data['vch_cess_rate'] = $cess_rate_eff;
							$taxsummary_data['vch_total_tax'] += $cess_value;
							$tax_cess_amt += $cess_value;
						}

						$gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
							? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : null))
							: ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);

						$taxsummary_data['vch_date']      = $voucher_date;
						$taxsummary_data['is_outward']    = 1;
						$taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
						$taxsummary_data['vch_hsn_sac']   = $item_hsn;
						$taxsummary_data['inv_supply_id'] = $inv_supply_id;

						$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

						$hsnummary_data = [
							'cmp_id'                => $this->company_id,
							'vch_txn_id'            => $voucher_txn_id,
							'txn_id'                => $txn_id,
							'vch_hsn_sac'           => $item_hsn,
							'vch_hsn_sac_taxbl_val' => $amount,
							'vch_hsn_sac_qty'       => 0,
							'vch_hsn_sac_uom'       => 0,
							'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
							'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
							'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
							'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
							'inv_supply_id'         => $inv_supply_id,
							'vch_gst_sum_id'        => $vch_gst_sum_id
						];
						$this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);

						/*********  FCY Tax Summary ****/
						if (parseAmount($fcy_forex_rate) > 0) {
							$taxsummary_fcy_data = [
								'cmp_id'                => $this->company_id,
								'vch_txn_id'            => $voucher_txn_id,
								'txn_id'                => $txn_id,
								'acc_bsd_id'            => $item_row['account_id'],
								'acc_bsd_type'          => 1,
								'vch_igst_fcy'          => 0,
								'vch_cgst_fcy'          => 0,
								'vch_sgst_ugst_fcy'     => 0,
								'vch_cess_fcy'          => 0,
								'vch_taxable_value_fcy' => $amount_fcy,
								'vch_total_tax_fcy'     => 0
							];

							if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
								$igst_value = ($amount_fcy * $igst_rate_eff) / 100;
								$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
							}
							elseif ($bo_state_code == $pos_code) {
								$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;
								$sgst_value = ($amount_fcy * $sgst_rate_eff) / 100;

								$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
								$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;
							}
							elseif (in_array($pos_code, $ugst_states)) {
								$ugst_value = ($amount_fcy * $utgst_rate_eff) / 100;
								$cgst_value = ($amount_fcy * $cgst_rate_eff) / 100;

								$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] = $ugst_value;
							}

							if ($cess_rate_eff > 0) {
								$cess_value = ($amount_fcy * $cess_rate_eff) / 100;
								$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
								$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
							}

							$this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
						}
						/*******************End of Save TAX SUMMARY ****************/
					}
				}		 
			 /*********************End of Save Account Wise Grid Entries **********************/
			 
			 /*********************Start of Save Billsundry Wise Grid Entries **********************/
			   if($billsndrydata) {
			        foreach($billsndrydata as $item_row) {
						/******************* Save MEMO TXN ****************/
						$insert_data = [
							  "cmp_id"          => $this->company_id,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $item_row['billsundry_id'],
							  'master_id_type'  => 'bsd'
							 ];
						$txn_id = $this->VouchersModel->add_comp_txn_data($insert_data);
						if($memoCheck=="1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] >0){	
						        $memo_txn_data  = [
								  'cmp_id'            => $this->company_id,
								  'acc_id'            => $item_row['billsundry_id'],
								  'acc_txn_date'      => $voucher_date,
								  'acc_txn_dr_cr'     => ($item_row['memo_amount']<0) ? 2 : 1,
								  'acc_txn_amt'       => $item_row['memo_amount'], 
								  'acc_txn_fcy'       => (parseAmount($fcy_forex_rate) >0)?($item_row['memo_amount']*$fcy_forex_rate):0,
								  'vch_txn_id'        => $voucher_txn_id,
								  'txn_id'            => $txn_id,
								  'hobo_id'           => $this->bo_id,
								  'acc_txn_type'      => 3
								];  				
								$this->VouchersModel->add_acc_txn_data($memo_txn_data);
						}
						/******************* Save Account TXN ****************/
						$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $item_row['billsundry_id'],
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => ($item_row['billsundry_amount']<0) ? 2 : 1,
							  'acc_txn_amt'       => $item_row['billsundry_amount'], 
							  'acc_txn_fcy'       => $item_row['billsundry_fcy_amount'],
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $this->bo_id,
							  'acc_txn_type'      => 1
							];  				
							$this->VouchersModel->add_acc_txn_data($acc_txn_data);
							
										
                		        $narration            = "";
                		        $this->VouchersModel->save_voucher_narration($voucher_txn_id,$txn_id,'short',$narration);
							
						
						 /*******************Start of Save TAX SUMMARY ****************/
						 $tax_cat_id              = $item_row['tax_cat_id'];
						 $tax_detail_rates        = $item_row['tax_details'];
						 
						 $amount                  = parseAmount($item_row['billsundry_amount']); // taxable base
						 $amount_fcy              = parseAmount($item_row['billsundry_fcy_amount']); // taxable base
                         $pos_code                = sprintf( '%02d', $pos );
                         $billsundry_info = $this->VouchersModel->get_bsd_details_info($item_row['billsundry_id']);
						 $bsd_hsn         = $billsundry_info['bsd_hsn_sac'] ?? '';
                         $taxsummary_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst'           => 0,
                            'vch_igst_rate'      => 0,
                            'vch_cgst'           => 0,
                            'vch_cgst_rate'      => 0,
                            'vch_sgst_ugst'      => 0,
                            'vch_sgst_ugst_rate' => 0,
                            'vch_cess'           => 0,
                            'vch_cess_rate'      => 0,
                            'vch_taxable_value'  => $amount,
                            'vch_total_tax'      => 0
                        ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                            $igst_value = ($amount * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_data['vch_igst']      = $igst_value;
                            $taxsummary_data['vch_igst_rate'] = $item_row['igst_rate'];
                            $taxsummary_data['vch_total_tax'] = $igst_value;
                            
                            $tax_igst_amt +=$igst_value;
                            
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_data['vch_cgst']           = $cgst_value;
                            $taxsummary_data['vch_cgst_rate']      = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_sgst_ugst']      = $sgst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;
                            
                            $tax_cgst_amt += $cgst_value;
                            $tax_sgst_amt +=  $sgst_value;
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_data['vch_sgst_ugst']      = $ugst_value;
                            $taxsummary_data['vch_sgst_ugst_rate'] = $item_row['igst_rate']/2;
                            $taxsummary_data['vch_total_tax']      = $ugst_value;
                            
							$tax_cgst_amt += $cgst_value;
                            $tax_utgst_amt += $ugst_value;                            
                        }
                    
                        // Always add cess if applicable
                       $cess_rate=0;
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
							$cess_rate=$tax_detail_rates['cess'];
                            $cess_value = ($amount * $tax_detail_rates['cess']) / 100;
                            $taxsummary_data['vch_cess']      = $cess_value;
                            $taxsummary_data['vch_cess_rate'] = $tax_detail_rates['cess'];
                            $taxsummary_data['vch_total_tax'] += $cess_value;
                            
                            $tax_cess_amt += $cess_value;
                        }
						  $taxsummary_data['vch_date']      = $voucher_date;
						  $taxsummary_data['is_outward']    = 1;				 
						  $taxsummary_data['gst_rate_grp']  = '1,'.parseAmount($item_row['igst_rate']).','.parseAmount($cess_rate);
						  $taxsummary_data['vch_hsn_sac']   = $bsd_hsn;
						  $taxsummary_data['inv_supply_id'] = 0;
						  
						  $vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data,$tax_required_flag); 
						  $hsnummary_data = [ 
							'cmp_id'                => $this->company_id,
							'vch_txn_id'            => $voucher_txn_id,
							'txn_id'                => $txn_id,
							'vch_hsn_sac'           => $bsd_hsn,
							'vch_hsn_sac_taxbl_val' => $amount,
							'vch_hsn_sac_qty'       => 0,
							'vch_hsn_sac_uom'       => 0,
							'vch_hsn_sac_igst'      => $taxsummary_data['vch_igst'],
							'vch_hsn_sac_cgst'      => $taxsummary_data['vch_cgst'],
							'vch_hsn_sac_sgst_ugst' => $taxsummary_data['vch_sgst_ugst'],
							'vch_hsn_sac_cess'      => $taxsummary_data['vch_cess'],
							'inv_supply_id '        => 0,
							'vch_gst_sum_id'        => $vch_gst_sum_id
							];			  
						   $this->VouchersModel->add_hsnsummary_data($hsnummary_data,$tax_required_flag);
						  
						if(parseAmount($fcy_forex_rate) >0 ){
						  // Default summary
                          $taxsummary_fcy_data = [ 
                            'cmp_id'             => $this->company_id,
                            'vch_txn_id'         => $voucher_txn_id,
                            'txn_id'             => $txn_id,
                            'acc_bsd_id'         => $item_row['billsundry_id'],
                            'acc_bsd_type'       => 2,
                            'vch_igst_fcy'       => 0,
                            'vch_cgst_fcy'       => 0,
                            'vch_sgst_ugst_fcy'  => 0,
                            'vch_cess_fcy'       => 0,
                            'vch_taxable_value_fcy'  => $amount_fcy,
                            'vch_total_tax_fcy'      => 0
                            ];
                    
                        // Case 1: IGST applies (different states OR POS in UGST states)
                        if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            $igst_value = ($amount_fcy * $item_row['igst_rate']) / 100;
                    
                            $taxsummary_fcy_data['vch_igst_fcy'] = $igst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;
                        } 
                        // Case 2: CGST + SGST applies (same state)
                        elseif ($bo_state_code == $pos_code) {
                            $cgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                            $sgst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_fcy_data['vch_cgst_fcy']           = $cgst_value;
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $sgst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $cgst_value + $sgst_value;
                        } 
                        // Case 3: UGST applies
                        elseif (in_array($pos_code, $ugst_states)) {
                            $ugst_value = ($amount_fcy * $item_row['igst_rate']/2) / 100;
                    
                            $taxsummary_fcy_data['vch_sgst_ugst_fcy']      = $ugst_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy']      = $ugst_value;
                        }
                    
                        // Always add cess if applicable
                       
                        if(isset($tax_detail_rates['cess']) && $tax_detail_rates['cess'] >0){
                            $cess_value = ($amount_fcy * $tax_detail_rates['cess']) / 100;
                            $taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
                            $taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
                        } 
						      
						   $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);    
						}
						  
						 /*******************End of Save TAX SUMMARY ****************/
					}
				}		 
			 /*********************End of Save Billsundry Wise Grid Entries **********************/
			 
			 /***************Start of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			 if ((int)$gstinType === 1) {
				 if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_utgst_amt,4,$voucher_date,2);
				   }		
                 if($tax_cess_amt > 0 )				   
				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cess_amt,5,$voucher_date,2);
			   }
			   if($gstinType==2 ){
				 if($tax_igst_amt>0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_igst_amt,1,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_sgst_amt,3,$voucher_date,2);
				   }
				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,2);
				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_utgst_amt,4,$voucher_date,2);
				   }		
                 if($tax_cess_amt>0)				   
				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cess_amt,5,$voucher_date,2);
		    }
			 /*************** End of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
					    
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
			/***********   Save Bill By Bill Data  *************/
		if(count($bbbdata)){		
		 $this->VouchersModel->SaveBillByBillData($bbbdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		/***********   Save Project Reporting  Data  *************/
		if(count($prdata)){		
		 $this->VouchersModel->SaveProjectReportingData($prdata,$voucher_date,$voucher_txn_id,$main_txn_id);
		}
		
		$this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
	  $this->VouchersModel->UpdateConso($voucher_txn_id,["draft_vch_rec_id"=>0]);
	  /************ Save Voucher Activity Log **********/
	    $voucher_messages = [
			18  => "New sale w/o item voucher has been updated by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			18  => "sale w/o item voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
			8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
		];
		$erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been updated by [USERNAME]([UUID])";
		$this->VouchersModel->SaveUserActivity($erp_activity_log,$voucher_txn_id);
	 }
	 catch (\Throwable $e) {		      
		        log_message('error', 'DB Error: ' . $e->getMessage());
                log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
				return  ['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
		}				
    });
      $trans_result['vchrtxnid'] = $voucher_txn_id; 
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
	
	$is_voucher_editable="false";
	$data['sess_cmp_suply_type']         = $this->session->get('cmp_suply_type') ?? 0;// composition supply type
	$data['comp_supply_types_dropdown'] = $this->compositionSupplyOptions;
	if($vch_subtype_id==0){  // Sale Without Item Data
		$data['account_transactions']   = $this->VouchersModel->grid_account_transactions($voucher_txn_id);
		if(in_array(4, array_column($data['account_transactions'], 'vch_txn_type'))) {
				$data['pending_vch'] = 1;			
			}else
				$data['pending_vch'] = 0;
			if(in_array(5, array_column($data['account_transactions'], 'vch_txn_type'))) {
				$data['rejected_vch'] = 1;
			}else
				$data['rejected_vch'] = 0;  
			// Voucher can be approved/rejected by user(whose uuid is set in approver (Voucher_TxnApproval_UUID)
			if(($data['pending_vch']==1 || $data['rejected_vch']==1)){
				$is_voucher_editable="false";
			}
			else 
				$is_voucher_editable="true";
	}
	
	if($vch_subtype_id==8){  // Sale With Item Data
		$data['item_transactions']   = $this->VouchersModel->grid_item_transactions($voucher_txn_id);
		$data['tax_type_list']       = $this->TXNTypesList;
		$data['transport_modes']     = transport_modes();
        $data['vehicle_types']       = vehicle_types();
		
	}	
	$data['voucher_info']          = $voucher_info;
	
	
	$data['billfrm_data']          = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
	$data['system_journal_info']   = $this->VouchersModel->get_system_journal_vch_info($voucher_txn_id);
	$data['draft_vch_rec_id']      = $voucher_info['draft_vch_rec_id'];
	$data['sub_type_dropdown']     = $this->sale_types;
	$data['sale_against_challan']  = [];
	$data['transporter_dropdown']  = [];
	$data['ugst_states']           = $this->ugst_states;
	$data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();
		
	$data['matrcntr_dropdown']     = $this->VouchersModel->material_centre_dropdown();
	$data['units_list']     	   = $this->VouchersModel->units_grid();
	$data['bo_state_code']         = $this->session->get('ses_bostecd');
	$data['SuppltSubSupplyTypes']  = json_encode($this->VouchersModel->SuppltSubSupplyTypes());	
	$data['voucher_txn_id']         =  $voucher_txn_id;
	$data['istaxinc']               =  $istaxinc;
	$data['ismemosale']             =  $ismemosale;
	$data['vch_series_id']          =  $voucher_info['vch_series_id'];
	//$data['get_gstroutsup_info']    =  $get_gstroutsup_info;
	$data['narration']              =  $get_narration;	
	$data['voucher_type_id']        =  $voucher_type_id;
	$data['currency_id']            =  $voucher_info['currency_id'];
	$data['forexcrncy_rate']        =  $voucher_info['forexcrncy_rate'];
	$data['taxes_list']             = $taxes_list;	
	$data['base_url']               = $this->base_url; 
	$data['folder_path']            = $this->folder_path;
	$data['voucher_date']           = date('d-m-Y', strtotime($voucher_info['vch_date']));
	$data['bo_gstin_type']          = $this->session->get('bo_gstin_type') ?? 1;// regular
	$data['voucher_auto_no']        = $this->VouchersModel->get_voucher_no($voucher_type_id);
	$data['voucher_bill_no']        = $this->VouchersModel->get_billno_format($voucher_type_id,$voucher_info['vch_series_id'],date('Y-m-d'),1,'counter');
	$data['voucher_series']         = $this->VouchersModel->comp_voucher_result($this->company_id,$voucher_type_id);
	$data['party_dropdown']         = $this->VouchersModel->party_dropdown();	
	$data['Voucher_TxnApproval_UUID'] = $Voucher_TxnApproval_UUID ?? 0;
	$data['current_uuid']             = $this->session->get('uuid');	
	$data['is_voucher_editable']    = $is_voucher_editable;
	$data['gstroutsup_info']        = $this->VouchersModel->get_gstroutsup_info($voucher_txn_id);
	$data['vchfcyrate_info']        = $this->VouchersModel->vchfcyrate_info($voucher_txn_id,$this->fy_id);
	
	$data['bsd_transactions']       = $this->VouchersModel->grid_bsd_transactions($voucher_txn_id);
	$data['bbb_data']               = $this->VouchersModel->get_bills_txn_data($voucher_txn_id);
    $data['pr_data']                = $this->VouchersModel->get_pr_txn_data($voucher_txn_id);
	$data['item_batch_data']        = $this->VouchersModel->get_itembatch_txn_data($voucher_txn_id);
	
	$data['OverrideSupplyTypeId']    = $this->VouchersModel->getVoucherOverrideSupplyTypeId($voucher_txn_id);
	
	$data['bills_method_list']      = ['','New Ref.','Adjustment'];
	$accounts_list                  = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	$bsd_accounts                   = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
if(!$bsd_accounts){
	
	    $bsd_accounts   =[];
	}
	$data['accounts_json_file']    = $accounts_list;
	$data['bsd_json_file']         = $bsd_accounts;		

    $items_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
	$data['item_json_file']        = $items_list;
	$data['bo_state_code']         = $bo_state_code;
	$data['currency_list']         = $this->VouchersModel->get_currency_list();
	$data['states_lists']          = $this->VouchersModel->show_states_lists();
	$data['eco_dropdown']          = $this->VouchersModel->show_eco_lists();
    $data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();	
	
	if($vch_subtype_id==0){
	    $data['tax_type_list']         = $this->TXNTypesListWO;
	  return view($this->folder_path.'sales/edit_non_item',$data);	  
	}	
	if($vch_subtype_id==8){
	  return view($this->folder_path.'sales/edit_item',$data);	  
	}
  } 
   
  public function sales_voucher_resave($voucher_txn_id){
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		$voucher_type_id    = 18;
	    $voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
		if(!$voucher_info){
		return $this->response->setJSON([
				'status' => false,
				'message' => 'Voucher details not found!!!'
			]);
		}
		$account_transactions      = $this->VouchersModel->grid_account_transactions($voucher_txn_id);
		$bsd_transactions          = $this->VouchersModel->grid_bsd_transactions($voucher_txn_id);
		$istaxinc                  = $this->VouchersModel->check_sale_taxinc($voucher_txn_id);
		$ismemosale                = $this->VouchersModel->check_sale_memoentry($voucher_txn_id);
		$get_gstroutsup_info       = $this->VouchersModel->get_gstroutsup_info($voucher_txn_id);
		
		$postData = [
			'voucher_date'     => $voucher_info['vch_date'],
			'voucher_series'   => $voucher_info['vch_series_id'],
			'party_id'         => $voucher_info['party_id'],
			'itmsdata'         => json_encode($account_transactions),
			'billsndrydata'    => json_encode($bsd_transactions),
			'narration'        => $this->VouchersModel->get_voucher_long_narration($voucher_txn_id),
			'currency_id'      => $voucher_info['currency_id'],
			'billno'           => $get_gstroutsup_info['outsup_bill_ref_no'],
			'supply_type'      => $get_gstroutsup_info['inv_type_id'],
			'tax_type_id'      => '',
			'reverse_charges'  => $get_gstroutsup_info['outsup_rev_chg'],
			'pos'              => $get_gstroutsup_info['outsup_pos'],
			'fcy_forex_rate'   => $voucher_info['forexcrncy_rate'],
			'memoCheck'        => $ismemosale,
			'taxInclusive'     => $istaxinc,
			'eco_id'           => $get_gstroutsup_info['outsup_eco'],
			'invoice_type'     => $get_gstroutsup_info['inv_type_id'],
			'draft_vch_rec_id' => $voucher_info['draft_vch_rec_id'],
			'bbbdata'          => json_encode($this->VouchersModel->get_bills_txn_data($voucher_txn_id)),
			'prdata'           => json_encode($this->VouchersModel->get_pr_txn_data($voucher_txn_id))
	     ];	
		$this->request->setGlobal('post', $postData);
		$_SERVER['REQUEST_METHOD'] = 'POST';	   
		$salesController           = new \App\Controllers\Admin\Sales();
		$salesController->request  = $this->request;
		$salesController->response = $this->response;		
      	$response                  = $salesController->edit($voucher_txn_id);	   
	     echo  $response->getBody();    // raw JSON string
		die();
		//$data = json_decode($json, true); // convert to PHP array
		//echo '<pre>';
		//print_r($data);
		//die();

	}
  }

  public function delete_sale(){
		$voucher_type_id    = 18;
	  if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		try{  		  
			$vc_txn_id_info     = unobfuscate_link($this->request->getVar('idvar'));
			$voucher_txn_id     = (int)$vc_txn_id_info[1] ?? 0; 			
			$voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
			if(empty($voucher_info)){
			  return $this->response->setJSON(['status' => false, 'message' => 'Voucher Not Found']);
			}
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cmptxnmstn');	
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'accttxnmst');
		   $this->VouchersModel->clear_table_narrations_data($voucher_txn_id);
		   $this->VouchersModel->clear_register_txn_data($voucher_txn_id,$voucher_type_id);
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'billtxnmst');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'cctxnmstnn');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'prjtxnmstn');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'subacctxnm');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchfcyrate');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchgstfcyn');
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchhsnsacn');  
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchgstsumn');  
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'gstrinwsup');  
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'gstroutsup');  
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'acctamtinc'); 
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'vchtxnconso'); 
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'itemtxnmst'); 		   
		   $this->VouchersModel->clear_table_txn_data($voucher_txn_id,'batchtxnmt');
		   $this->VouchersModel->clear_system_journal_bridge_data($voucher_txn_id,4);  	
			 
		   // Mark Dirty Transactions
			$voucher_date = $voucher_info['vch_date'];
		    $voucherDate  = new \DateTime($voucher_date);
			$today        = new \DateTime('today');
           	 if($voucherDate < $today){ 
				 // Call your dirty recalculation function
				 //$this->VouchersModel->markSnapshotDirty($voucher_date);
			 }	 
		   return $this->response->setJSON(['status' => true, 'message' => 'Voucher Deleted']);
		 }
		catch (\Throwable $e){
		  log_message('error', 'DB Query Error: ' . $e->getMessage());
		  return $this->response->setJSON(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
	     }	   
	   }
	}
}    