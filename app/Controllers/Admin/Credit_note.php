<?php
namespace App\Controllers\Admin;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Traits\TransactionTrait;
class Credit_note  extends BaseController{
    use TransactionTrait; 	
	protected $LogModel;
	protected $CommonModel;
	protected $VouchersModel;
	protected $auth_session;
	protected $session;	
	protected $bo_id;
	protected $fy_id;
	protected $company_id;
	protected $ugst_states;
	protected $base_url;
	protected $folder_path;
	
  function __construct(){   
    helper(['form', 'url','text','custom']);	
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
	$this->ugst_states   = ['35','04','26','25','31','38','34','97'];
	$this->sale_types    = [ 1  => 'Credit Note With Stock Inward',];
    // Voucher type id for Credit Note
    $this->voucher_type_id = 2;   
	$this->compositionSupplyOptions = [
            ''  => 'Choose Composition Supply',
            '1' => 'MANUFACTURER',
            '2' => 'TRADER',
            '3' => 'RESTAURANT SERVICES',
            '4' => 'OTHER SERVICES'
        ];
    }
   
    public function item(){
        $voucher_type_id           = $this->voucher_type_id; // 2
        $taxes_list                = $this->VouchersModel->GetGSTTaxesList();
        $Voucher_TxnApproval_Info  = $this->VouchersModel->Voucher_TxnApproval();
        $bo_state_code             = $this->session->get('ses_bostecd');
        $ugst_states               = $this->ugst_states;
        $Voucher_TxnApproval       = $Voucher_TxnApproval_Info['approval_amt'] ?? '';
        $Voucher_TxnApproval_UUID  = $Voucher_TxnApproval_Info['uuid_to'] ?? 0; // approver uuid
		 $gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);
        if ($this->request->getMethod() === 'POST' && $this->request->isAjax()) {
			$ip = $_SERVER['REMOTE_ADDR'];
	    if($ip=='103.172.223.178' || $ip=='103.172.223.179'){
	   //echo "<pre>";print_r($_POST);die();
	   } 
		   // echo '<pre>';print_r($_POST);die();
            $rules = [
                'voucher_date' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Voucher Date is required'],
                ],
                'voucher_series' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Voucher Series is required'],
                ],
                'party_id' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Party is required'],
                ], 
                'matrcntr_id' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Material Center is required'],
                ],
                'itmsdata' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Data is required'],
                ],
            ];

            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
				
                return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }

            // ----------------------- Transaction wrapper ----------------------------
           $trans_result = $this->runTransaction(function($db) use ($gstinType,$bo_state_code, $ugst_states, $taxes_list, $voucher_type_id, $Voucher_TxnApproval_UUID, $Voucher_TxnApproval) {
               try {
                    // ------------------------ Read POST vars --------------------------
                    $voucher_date       = $this->request->getVar('voucher_date');
                    $voucher_date       = validate_date_by_fy($voucher_date);

                    $voucher_series     = $this->request->getVar('voucher_series');
                    $party_id           = $this->request->getVar('party_id');
                    $matrcntr_id        = $this->request->getVar('matrcntr_id');
                    $long_narration     = $this->request->getVar('narration');

                    $itmsdata           = json_decode($this->request->getVar('itmsdata'), true);           // items grid
                    $billsndrydata      = json_decode($this->request->getVar('billsndrydata'), true) ?? []; // only bsd type 0 shown in UI
                    $itemsbatchdata     = json_decode($this->request->getVar('itemsbatchdata'), true) ?? [];
                    $bbbdata            = json_decode($this->request->getVar('bbbdata'), true) ?? [];
                    $prdata             = json_decode($this->request->getVar('prdata'), true) ?? [];
                    $tax_required_flag   = $this->request->getVar('tax_required_flag');
                    $currency_id        = $this->request->getVar('currency_id');
                    $billno             = $this->request->getVar('billno');
                    $vch_sub_type_id    = $this->request->getVar('sale_type');
                    $reverse_charges    = $this->request->getVar('reverse_charges');
                    $pos                = $this->request->getVar('pos');
                    $fcy_forex_rate     = $this->request->getVar('fcy_forex_rate');
                    $memoCheck          = $this->request->getVar('memoCheck'); // "1" if memo grid active
                    $taxInclusive       = $this->request->getVar('taxInclusive');
                    $outsup_eco         = $this->request->getVar('eco_id') ?? 0;
                    $btnid              = $this->request->getVar('btnid'); // submitbtn or submitbtn_drft
					$cmp_supply_type     = $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null	
					$pos_code   = sprintf('%02d', $pos);
					$isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
					if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
					   if($billno=="" || $billno==0 || $billno <0)
						return ['status' => false, 'vchrtxnid'=>'','message' => 'Validation Error', 'errors' => ['Bill No. is required']];
						$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'sale');
						if($billno_duplicate=="1")
						return ['status' => false, 'vchrtxnid'=>'','message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']];
					}
			
                    // link back to original SALE invoice (outsup_id) for CN (as per your workflow)
                    $cr_note_outsup_info = $this->request->getVar('sale_voucher_id');
					$cr_note_outsup_id       = 0;
					$cr_note_outsup_vchtxid = 0;
					$cr_note_type_tag       = 'R';   // default Regular

					if (!empty($cr_note_outsup_info)) {

						// Break: 2492||147885||R
						$parts = explode('||', $cr_note_outsup_info);

						$cr_note_outsup_id       = $parts[0] ?? 0;
						$cr_note_outsup_vchtxid = $parts[1] ?? 0;
						$cr_note_type_tag       = $parts[2] ?? 'R';   // R or C
					}
										
                    // Apply check to get is sale voucher is posted in Regular or Compostion , we will chekc this from table "vchbridgen" table if sale  voucher vch_txn_id exists or not to aplly further conditions to save credit note
                    $sale_in_reg_comp_info = $this->VouchersModel->get_voucher_bridge_info($cr_note_outsup_vchtxid,4);
                    if($sale_in_reg_comp_info)
                      $sale_against_status = 2;// composition
                     else
                      $sale_against_status = 1;// regular
                     
					
                    
                    $is_batch = !empty($itemsbatchdata);
                    $is_bbb   = !empty($bbbdata);
                    $is_pr    = !empty($prdata);
					
					// Mark Dirty Transactions
					$voucherDate = new \DateTime($voucher_date);
					$today       = new \DateTime('today');
					 if($voucherDate < $today){ 
						 // Call your dirty recalculation function
						 $this->VouchersModel->markSnapshotDirty($voucher_date);
					 }
			 
                    // -------------------- Party name for draft particulars -------------
                    $vch_particulars = '';
					$party_is_sez=0;
                    if ($party_id) {
                        $acc_info = $this->VouchersModel->account_detail_info($party_id);
                        $vch_particulars = $acc_info['acc_name'] ?? '';
						
                    }
                    if ($vch_particulars === '') {
                        return ['status' => false, 'message' => 'Party field is not valid'];
                    }

                    // -------------------- Totals (mirror Sales.php fields) -------------
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
					
				
                    // ---------------------- Section 1: Save Draft ----------------------
                    $voucherdata = json_encode([
                        "is_batch"     => $is_batch,
                        "batchdata"    => $itemsbatchdata,
                        "bbbdata"      => $bbbdata,
                        "prdata"       => $prdata,
                        "mat_cent_id"  => $matrcntr_id,
                        "itmsdata"     => $itmsdata,
                        "billsndrydata"=> $billsndrydata
                    ]);
                    $pgrdata_drft = [
                        "cmp_id"         => $this->company_id,
                        "hobo_id"        => $this->bo_id,
                        "log_date_time"  => $voucher_date,
                        "vch_type_id"    => $voucher_type_id,
                        "vch_json_data"  => $voucherdata,
                        "vch_amt"        => parseAmount($sale_total + $billsundry_total),
                        "vch_fcy_amt"    => parseAmount($sale_fcy_total + $billsundry_fcy_total),
                        "vch_fcy_rate"   => (float)$fcy_forex_rate ?? 0,
                        "vch_particulars"=> $vch_particulars,
                        "uuid_aictly"    => $this->session->get('uuid'),
                        "long_narr"      => $long_narration,
                        "vch_series_id"  => (int)$voucher_series,
                        "isoptional"     => FALSE,
                        "vch_fcy_id"     => (int)$currency_id,
                        "is_cc"          => 0,
                        "is_bbb"         => (int)$is_bbb,
                        "is_pr"          => (int)$is_pr,
                        "is_sblgr"       => 0
                    ];
                    $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
                    if ($btnid === 'submitbtn_drft') {
                        return ['status' => true, 'message' => 'Voucher saved in draft mode'];
                    }
			         
			if($isvoucher_autobillno==0){
			   $inwsup_bill_ref_no =$billno;
			}
			else if($isvoucher_autobillno==1){			
			 $inwsup_bill_ref_no = $this->VouchersModel->get_billno_format($voucher_type_id, $voucher_series, $voucher_date, 1, 'counter');	
			}
			
			
			/******* start of  if gstinType ==2 means composition then insert new voucher type 23  ********/
			// Sale is posted in Regular but now credit note is posted in COmposition then 
			// conditions : 
			/*
			
			 No Tax Summary
			 Blank entry in Taxable Accounts
			 No Need to creat Bridge
			 No S/J  No Created due to Non Availabity of The Refund Accounts
			 
			
			Case 1 if current is Compistion and Choosed Against Sale was in Regular
			
			
			*/
			 
			
			if($gstinType==2 && $sale_against_status==1){
			
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
					
			} 
			
			
		//	Case 2 if current is Regular and Choosed Against Sale was in Regular
			/* else if($gstinType==1 && $sale_against_status==1){
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
			
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
		     	
					
			} */
			
		//	Case 3 if current is Composition and Choosed Against Sale was in Compoition
			else if($gstinType==2 && $sale_against_status==2){
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
			
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
		     	
					
			}else{
				
				if($tax_required_flag==1){
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
				}
	   
			}
	
			
                    // ------------------ Section 2: Create voucher conso -----------------
                    $insert_data = [
                        "cmp_id"           => $this->company_id,
                        "vch_series_id"    => $voucher_series,
                        "vch_type_id"      => $voucher_type_id,
                        "vch_sub_type_id"  => $vch_sub_type_id, // 0 (consistent)
                        "vch_date"         => $voucher_date,
                        "mat_cent_id"      => $matrcntr_id,
                        "draft_vch_rec_id" => $draft_vch_rec_id
                    ];
                         
                    $voucher_txn_id = $this->VouchersModel->add_voucher_cons_data($insert_data);
					
					
					$brdge_insert_data  = array(
									  "cmp_id"           => $this->company_id,		  
									  "vch_bridge_type"    => 1,          //1 for Credit Note
									  "vch_txn_id_src"      => $cr_note_outsup_vchtxid,
									  "vch_txn_id_dest"  => $voucher_txn_id
									 );									 
					$this->VouchersModel->add_vchbridgen_txn_data($brdge_insert_data);
					
                    
                    // if current is Composition and Choosed Against Sale was in Composition
                if($gstinType==2 &&  $sale_against_status==2){ 
					$brdge_insert_data  = array(
									  "cmp_id"           => $this->company_id,		  
									  "vch_bridge_type"    => 5,          //5 for GST Red on CMP Credit Note
									  "vch_txn_id_src"      => $voucher_txn_id,
									  "vch_txn_id_dest"  => $comp_voucher_txn_id
									 );									 
					$this->VouchersModel->add_vchbridgen_txn_data($brdge_insert_data);
				}
				

                    // FCY rate (if enabled)
                   if(parseAmount($fcy_forex_rate) >0){
                        $fcy_data = [
                            "cmp_id"       => $this->company_id,
                            "vch_txn_id"   => $voucher_txn_id,
                            "vch_fcy_rate" => $fcy_forex_rate,
                            "cmp_fcy_mst_id" => $currency_id
                        ];
                        $this->VouchersModel->save_voucher_fcyrate($fcy_data);
                    }

                    // 2.4 — GST header (Credit Note goes to gstrinwsup with link to outsup_id)
                    if ($isvoucher_autobillno == 0) {
                        $gstrin_insert = [
                            "cmp_id"              => (int)$this->company_id,
                            "vch_txn_id"          => (int)$voucher_txn_id,
                            "inwsup_pos"          => $pos,
                            "inwsup_bill_ref_no"  => $inwsup_bill_ref_no,
                            "inwsup_rev_chg"      => (int)$reverse_charges,
                            "inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,
                            "inwsup_eco"          => (int)$outsup_eco,
                            "gst_supply_type"=>(int)$cmp_supply_type							
                        ];
                        $this->VouchersModel->add_gstrinwsup_data($gstrin_insert);
                    } else {
                        $gstrin_insert = [
                            "cmp_id"              => (int)$this->company_id,
                            "vch_txn_id"          => (int)$voucher_txn_id,
                            "inwsup_pos"          => $pos,
                            "inwsup_bill_ref_no"  => $inwsup_bill_ref_no,
                            "inwsup_rev_chg"      => (int)$reverse_charges,                          
                            "inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,
                            "inwsup_eco"          => (int)$outsup_eco,
							"gst_supply_type"=>(int)$cmp_supply_type
                        ];
                        $this->VouchersModel->add_gstrinwsup_data($gstrin_insert);
                    }

                    // Invoice totals
                    $voucher_invoice_value     = $sale_total + $billsundry_total + $total_tax_amount;
                    $voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;

                    $voucher_memo_value        = $sale_memo_total + $billsundry_memo_total;
                    $voucher_fcy_memo_value    = (parseAmount($fcy_forex_rate) >0) ? ($sale_memo_total + $billsundry_memo_total) * $fcy_forex_rate : 0;

                    // 2.5 — Party account (CR) + register (acc_txn_type per approval limit)
                    $acc_txn_type = ($Voucher_TxnApproval!='' && parseAmount($voucher_invoice_value) > parseAmount($Voucher_TxnApproval)) ? 4 : 1;

                    // link row in cmptxnmstn (master_id_type=acc)
                    $party_comp_txn = [
                        "cmp_id"          => $this->company_id,
                        "vch_series_id"   => $voucher_series,
                        "vch_txn_id"      => $voucher_txn_id,
                        "master_id"       => $party_id,
                        "master_id_type"  => 'acc'
                    ];
                    $party_txn_id = $this->VouchersModel->add_comp_txn_data($party_comp_txn);

                    // accttxnmst: CREDIT party (2)
                    $party_acc_txn = [
                        'cmp_id'        => $this->company_id,
                        'acc_id'        => $party_id,
                        'acc_txn_date'  => $voucher_date,
                        'acc_txn_dr_cr' => 2,
                        'acc_txn_amt'   => $voucher_invoice_value,
                        'acc_txn_fcy'   => $voucher_fcy_invoice_value,
                        'vch_txn_id'    => $voucher_txn_id,
                        'txn_id'        => $party_txn_id,
                        'hobo_id'       => $this->bo_id,
                        'acc_txn_type'  => $acc_txn_type
                    ];
                    $this->VouchersModel->add_acc_txn_data($party_acc_txn);

                    // Memo (party) if any — (same style as sales.php): type=3
                    if ($voucher_memo_value > 0) {
                        $memo_party_txn = [
                            'cmp_id'        => $this->company_id,
                            'acc_id'        => $party_id,
                            'acc_txn_date'  => $voucher_date,
                            'acc_txn_dr_cr' => 2,
                            'acc_txn_amt'   => $voucher_memo_value,
                            'acc_txn_fcy'   => $voucher_fcy_memo_value,
                            'vch_txn_id'    => $voucher_txn_id,
                            'txn_id'        => $party_txn_id,
                            'hobo_id'       => $this->bo_id,
                            'acc_txn_type'  => 3
                        ];
                        $this->VouchersModel->add_acc_txn_data($memo_party_txn);
                    }

                    // Register (long) for party
                    $long_register_insert = [
                        'acct_vch_type'  => $voucher_type_id,
                        'cmp_id'         => $this->company_id,
                        'vch_txn_id'     => $voucher_txn_id,
                        'txn_id'         => NULL,
                        'vch_date'       => $voucher_date,
                        'acc_id'         => $party_id,
                        'acc_txn_dr_amt' => 0,
                        'acc_txn_cr_amt' => $voucher_invoice_value,
                        'vch_narr'       => $long_narration ?? '',
                        'hobo_id'        => $this->bo_id,
                        'acc_txn_type'   => $acc_txn_type
                    ];
                    $this->VouchersModel->add_register_txn_data($long_register_insert);

                    // 2.6 — Item lines (Inventory DR; memo optional)
                    $itm_txn_type   = 1; // regular
                    $tax_igst_amt   = 0;
                    $tax_cgst_amt   = 0;
                    $tax_sgst_amt   = 0;
                    $tax_utgst_amt  = 0;
                    $tax_cess_amt   = 0;

                    $item_account_totals      = []; // account_id => amount (INR)
                    $item_account_fcy_totals  = []; // account_id => fcy amount

                    if (!empty($itmsdata)) {
                        foreach ($itmsdata as $item_row) {
							$inv_supply_id   = $item_row['supply_type_id'];	
                            // cmptxnmstn link for item (master_id_type=itm)
                            $itm_comp_txn = [
                                "cmp_id"         => $this->company_id,
                                "vch_series_id"  => $voucher_series,
                                "vch_txn_id"     => $voucher_txn_id,
                                "master_id"      => $item_row['item_id'],
                                "master_id_type" => 'itm'
                            ];
                            $itm_txn_id = $this->VouchersModel->add_comp_txn_data($itm_comp_txn);

                            // Memo entry for item (if opted)
                            if ($memoCheck == "1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] > 0) {
                                $memo_insert_data = [
                                    'cmp_id'         => $this->company_id,
                                    'itm_id_unit_id' => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                                    'itm_txn_qty'    => $item_row['item_qty'],
                                    'itm_txn_date'   => $voucher_date,
                                    'itm_txn_dr_cr'  => 1, // DR for inward memo
                                    'itm_txn_rate'   => 1,
                                    'itm_txn_amt'    => parseAmount($item_row['memo_amount']),
                                    'itm_txn_fcy'    => 0,
                                    'vch_txn_id'     => $voucher_txn_id,
                                    'txn_id'         => $itm_txn_id,
                                    'mat_cent_id'    => $matrcntr_id,
                                    'hobo_id'        => $this->bo_id,
                                    'itm_txn_type'   => 3
                                ];
                                $this->VouchersModel->add_itm_txn_data($memo_insert_data);
                            }

                            // Regular item DR (inventory inward)
                            if (isset($item_row['item_qty']) && $item_row['item_qty'] > 0) {
                                $itm_txn_insert = [
                                    'cmp_id'         => $this->company_id,
                                    'itm_id_unit_id' => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                                    'itm_txn_qty'    => $item_row['item_qty'],
                                    'itm_txn_date'   => $voucher_date,
                                    'itm_txn_dr_cr'  => 1, // DR
                                    'itm_txn_rate'   => parseAmountPrice($item_row['item_price'], 4),
                                    'itm_txn_amt'    => parseAmount($item_row['item_total_amount']),
                                    'itm_txn_fcy'    => parseAmount($item_row['item_total_fcy_amount']),
                                    'vch_txn_id'     => $voucher_txn_id,
                                    'txn_id'         => $itm_txn_id,
                                    'mat_cent_id'    => $matrcntr_id,
                                    'hobo_id'        => $this->bo_id,
                                    'itm_txn_type'   => $itm_txn_type
                                ];
                                $this->VouchersModel->add_itm_txn_data($itm_txn_insert);

                                // Short narration for item line
                                $this->VouchersModel->save_voucher_narration($voucher_txn_id, $itm_txn_id, 'short', $item_row['description']);

                                // Tax-inclusive aux data (acctamtinc)
                                if ($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] > 0) {
                                    $taxinc_insert = [
                                        'cmp_id'           => $this->company_id,
                                        'acc_itm_id'       => $item_row['account_id'],                                      
                                        'acc_txn_inc_amt'  => $item_row['txinc_amount'],
                                        'vch_txn_id'       => $voucher_txn_id,
                                        'txn_id'           => $itm_txn_id,
                                        'acc_txn_dr_cr'    => 1 // DR since item inward reduces revenue
                                    ];
                                    $this->VouchersModel->add_taxinc_txn_data($taxinc_insert);
                                }

                                // Item register (main)
                                $itm_reg_data = [
                                    'itm_vch_type'   => $voucher_type_id,
                                    'cmp_id'         => $this->company_id,
                                    'vch_txn_id'     => $voucher_txn_id,
                                    'txn_id'         => $itm_txn_id,
                                    'vch_date'       => $voucher_date,
                                    'itm_id_unit_id' => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                                    'vch_narr'       => $long_narration ?? '',
                                    'hobo_id'        => $this->bo_id,
                                    'itm_txn_type'   => $itm_txn_type
                                ];
                                $itm_vch_reg_id = $this->VouchersModel->add_item_register_txn_data($itm_reg_data);

                                // Item register (sub) — DR qty/amt for inward
                                $itm_reg_sub = [
                                    'itm_vch_reg_id'  => $itm_vch_reg_id,
                                    'mat_cent_id'     => $matrcntr_id,
                                    'itm_txn_dr_qty'  => $item_row['item_qty'],
                                    'itm_txn_dr_rate' => parseAmountPrice($item_row['item_price'], 4),
                                    'itm_txn_dr_amt'  => parseAmount($item_row['item_total_amount']),
                                    'itm_txn_cr_qty'  => 0,
                                    'itm_txn_cr_rate' => 0,
                                    'itm_txn_cr_amt'  => 0,
                                    'vch_narr'        => $long_narration ?? '',
                                ];
                                $this->VouchersModel->add_item_sub_register_txn_data($itm_reg_sub);

                                
                                $account_id = $item_row['item_sales_acc'];
                                $amount     = parseAmount($item_row['item_total_amount']);
                                $amount_fcy = parseAmount($item_row['item_total_fcy_amount']);
                                if (isset($item_account_totals[$account_id])) {
                                    $item_account_totals[$account_id] += $amount;
                                    $item_account_fcy_totals[$account_id] = ($item_account_fcy_totals[$account_id] ?? 0) + $amount_fcy;
                                } else {
                                    $item_account_totals[$account_id] = $amount;
                                    $item_account_fcy_totals[$account_id] = $amount_fcy;
                                }

                                // --------- Tax Summary for item row (and FCY) ----------
                                $amount_base = parseAmount($item_row['item_total_amount']);
                                $amount_fcyb = parseAmount($item_row['item_total_fcy_amount']);
                                $pos_code    = sprintf('%02d', $pos);
                                $iteminfo = $this->VouchersModel->get_item_details_info($item_row['item_id']);
                                $item_hsn = $iteminfo['itm_hsn'] ?? '';
                                
                                // ------------------------------------------------------------------
                                // Effective rates (regular vs composition) – CREDIT VOUCHER
                                // ------------------------------------------------------------------
                                
                                $igst_rate_eff=$cgst_rate_eff=$sgst_rate_eff=$utgst_rate_eff=$cess_rate_eff=0;
                                // Defaults (regular)
                                 if ((int)$gstinType === 1 && $sale_against_status==1) {
                                $igst_rate_eff  = parseAmount($item_row['igst_rate']);
                                $cgst_rate_eff  = $igst_rate_eff / 2;
                                $sgst_rate_eff  = $igst_rate_eff / 2;
                                $utgst_rate_eff = $igst_rate_eff / 2;
                                $cess_rate_eff  = isset($item_row['tax_details']['cess']) ? parseAmount($item_row['tax_details']['cess']) : 0;
                                 }
                                
                                // Override for composition (bo_gstin_type == 2)
                                if ((int)$gstinType === 2 && $sale_against_status==2) {
                                    $compsrates = get_composition_rates($cmp_supply_type);
                                    $igst_rate_eff  = $compsrates['igst'];
                                    $cgst_rate_eff  = $compsrates['cgst'];
                                    $sgst_rate_eff  = $compsrates['sgst'];
                                    $utgst_rate_eff = $compsrates['ut'];
                                    $cess_rate_eff  = $compsrates['cess'] ?? 0;
                                }
                                
                                // ------------------------------------------------------------------
                                // Default tax summary row
                                // ------------------------------------------------------------------
                                
                                $taxsummary_data = [
                                    'cmp_id'             => $this->company_id,
                                    'vch_txn_id'         => $voucher_txn_id,
                                    'txn_id'             => $itm_txn_id,
                                    'acc_bsd_id'         => $item_row['item_id'],
                                    'acc_bsd_type'       => 3, // item
                                    'vch_igst'           => 0,
                                    'vch_igst_rate'      => 0,
                                    'vch_cgst'           => 0,
                                    'vch_cgst_rate'      => 0,
                                    'vch_sgst_ugst'      => 0,
                                    'vch_sgst_ugst_rate' => 0,
                                    'vch_cess'           => 0,
                                    'vch_cess_rate'      => 0,
                                    'vch_taxable_value'  => $amount_base,
                                    'vch_total_tax'      => 0
                                ];
                                
                                // ------------------------------------------------------------------
                                // Case 1: IGST (Inter-state, not UT)
                                // ------------------------------------------------------------------
                                if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                                
                                    $igst_value = ($amount_base * $igst_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_igst']      = $igst_value;
                                    $taxsummary_data['vch_igst_rate'] = $igst_rate_eff;
                                    $taxsummary_data['vch_total_tax'] = $igst_value;
                                
                                    $tax_igst_amt += $igst_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // Case 2: CGST + SGST (Same state)
                                // ------------------------------------------------------------------
                                elseif ($bo_state_code == $pos_code) {
                                
                                    $cgst_value = ($amount_base * $cgst_rate_eff) / 100;
                                    $sgst_value = ($amount_base * $sgst_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_cgst']           = $cgst_value;
                                    $taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
                                    $taxsummary_data['vch_sgst_ugst']      = $sgst_value;
                                    $taxsummary_data['vch_sgst_ugst_rate'] = $sgst_rate_eff;
                                    $taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;
                                
                                    $tax_cgst_amt += $cgst_value;
                                    $tax_sgst_amt += $sgst_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // Case 3: UTGST applies
                                // ------------------------------------------------------------------
                                elseif (in_array($pos_code, $ugst_states)) {
                                
                                    $cgst_value = ($amount_base * $cgst_rate_eff) / 100;
                                    $ugst_value = ($amount_base * $utgst_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_cgst']           = $cgst_value;
                                    $taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
                                    $taxsummary_data['vch_sgst_ugst']      = $ugst_value;
                                    $taxsummary_data['vch_sgst_ugst_rate'] = $utgst_rate_eff;
                                    $taxsummary_data['vch_total_tax']      = ($cgst_value + $ugst_value);
                                
                                    $tax_cgst_amt  += $cgst_value;
                                    $tax_utgst_amt += $ugst_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // Always add CESS if applicable
                                // ------------------------------------------------------------------
                                if ($cess_rate_eff > 0) {
                                
                                    $cess_value = ($amount_base * $cess_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_cess']      = $cess_value;
                                    $taxsummary_data['vch_cess_rate'] = $cess_rate_eff;
                                    $taxsummary_data['vch_total_tax'] += $cess_value;
                                
                                    $tax_cess_amt += $cess_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // GST TAG LOGIC (same as Sale With Item)
                                // ------------------------------------------------------------------
                                
                                $gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
                                    ? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
                                    : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);
                                
                                // ------------------------------------------------------------------
                                // Final fields + Save TAX SUMMARY
                                // ------------------------------------------------------------------
                                
                                $taxsummary_data['vch_date']      = $voucher_date;
                                $taxsummary_data['is_outward']    = 2;   // CREDIT VOUCHER
                                $taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
                                $taxsummary_data['vch_hsn_sac']   = $item_hsn;
                                $taxsummary_data['inv_supply_id'] = $inv_supply_id;
                                
                                $vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);
                                
                                // ------------------------------------------------------------------
                                // HSN SUMMARY (same pattern as Sale)
                                // ------------------------------------------------------------------
                                
                                $hsnummary_data = [
                                    'cmp_id'                => $this->company_id,
                                    'vch_txn_id'            => $voucher_txn_id,
                                    'txn_id'                => $itm_txn_id,
                                    'vch_hsn_sac'           => $item_hsn,
                                    'vch_hsn_sac_taxbl_val' => $amount_base,
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
                                
                                // ------------------------------------------------------------------
                                // FCY TAX SUMMARY (same engine as Sale)
                                // ------------------------------------------------------------------
                                
                                if (parseAmount($fcy_forex_rate) > 0) {
                                
                                    $taxsummary_fcy_data = [
                                        'cmp_id'                 => $this->company_id,
                                        'vch_txn_id'             => $voucher_txn_id,
                                        'txn_id'                 => $itm_txn_id,
                                        'acc_bsd_id'             => $item_row['item_id'],
                                        'acc_bsd_type'           => 3,
                                        'vch_igst_fcy'           => 0,
                                        'vch_cgst_fcy'           => 0,
                                        'vch_sgst_ugst_fcy'      => 0,
                                        'vch_cess_fcy'           => 0,
                                        'vch_taxable_value_fcy'  => $amount_fcyb,
                                        'vch_total_tax_fcy'      => 0
                                    ];
                                
                                    if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                                
                                        $igst_value_fcy = ($amount_fcyb * $igst_rate_eff) / 100;
                                        $taxsummary_fcy_data['vch_igst_fcy']      = $igst_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value_fcy;
                                    }
                                    elseif ($bo_state_code == $pos_code) {
                                
                                        $cgst_value_fcy = ($amount_fcyb * $cgst_rate_eff) / 100;
                                        $sgst_value_fcy = ($amount_fcyb * $sgst_rate_eff) / 100;
                                
                                        $taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value_fcy;
                                        $taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value_fcy + $sgst_value_fcy;
                                    }
                                    elseif (in_array($pos_code, $ugst_states)) {
                                
                                        $ugst_value_fcy = ($amount_fcyb * $utgst_rate_eff) / 100;
                                        $cgst_value_fcy = ($amount_fcyb * $cgst_rate_eff) / 100;
                                
                                        $taxsummary_fcy_data['vch_sgst_ugst_fcy']  = $ugst_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] = $ugst_value_fcy + $cgst_value_fcy;
                                    }
                                
                                    if ($cess_rate_eff > 0) {
                                
                                        $cess_value_fcy = ($amount_fcyb * $cess_rate_eff) / 100;
                                        $taxsummary_fcy_data['vch_cess_fcy']      = $cess_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value_fcy;
                                    }
                                
                                    $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
                                }

                                // --------- END tax summary for item row ----------
                            }
                        } // foreach item_row
                    }

                    // 2.7 — Post item-paired account(s) (DEBIT)
                    foreach ($item_account_totals as $account_id => $amount) {
                        $amount_fcy = $item_account_fcy_totals[$account_id] ?? 0;

                        $acc_link_comp = [
                            "cmp_id"         => $this->company_id,
                            "vch_series_id"  => $voucher_series,
                            "vch_txn_id"     => $voucher_txn_id,
                            "master_id"      => $account_id,
                            "master_id_type" => 'acc'
                        ];
                        $acc_txn_link_id = $this->VouchersModel->add_comp_txn_data($acc_link_comp);

                        $acc_txn_data = [
                            'cmp_id'        => $this->company_id,
                            'acc_id'        => $account_id,
                            'acc_txn_date'  => $voucher_date,
                            'acc_txn_dr_cr' => 1, // DEBIT (sales return)
                            'acc_txn_amt'   => $amount,
                            'acc_txn_fcy'   => $amount_fcy,
                            'vch_txn_id'    => $voucher_txn_id,
                            'txn_id'        => $acc_txn_link_id,
                            'hobo_id'       => $this->bo_id,
                            'acc_txn_type'  => 1
                        ];
                        $this->VouchersModel->add_acc_txn_data($acc_txn_data);
                    }

                    // 2.8/2.9 — BillSundry (non-tax only in grid) + short narr + tax summary for BSD
                    if (!empty($billsndrydata)) {
                        foreach ($billsndrydata as $bsd_row) {
                            // cmptxnmstn link for BSD (master_id_type=bsd, using #billsundry_id as per your note)
                            $bsd_comp = [
                                "cmp_id"         => $this->company_id,
                                "vch_series_id"  => $voucher_series,
                                "vch_txn_id"     => $voucher_txn_id,
                                "master_id"      => $bsd_row['billsundry_id'],
                                "master_id_type" => 'bsd'
                            ];
                            $bsd_txn_id = $this->VouchersModel->add_comp_txn_data($bsd_comp);

                            // Memo on BSD (optional, same pattern as sale.php)
                            if ($memoCheck == "1" && isset($bsd_row['memo_amount']) && $bsd_row['memo_amount'] > 0) {
                                $memo_bsd_txn = [
                                    'cmp_id'        => $this->company_id,
                                    'acc_id'        => $bsd_row['billsundry_id'],
                                    'acc_txn_date'  => $voucher_date,
                                    'acc_txn_dr_cr' => ($bsd_row['memo_amount'] < 0) ? 2 : 1,
                                    'acc_txn_amt'   => $bsd_row['memo_amount'],
                                    'acc_txn_fcy'   => (parseAmount($fcy_forex_rate) >0) ? ($bsd_row['memo_amount'] * $fcy_forex_rate) : 0,
                                    'vch_txn_id'    => $voucher_txn_id,
                                    'txn_id'        => $bsd_txn_id,
                                    'hobo_id'       => $this->bo_id,
                                    'acc_txn_type'  => 3
                                ];
                                $this->VouchersModel->add_acc_txn_data($memo_bsd_txn);
                            }

                            // BSD account txn (dr or cr by sign exactly like sale.php)
                            $bsd_acc_txn = [
                                'cmp_id'        => $this->company_id,
                                'acc_id'        => $bsd_row['billsundry_id'],
                                'acc_txn_date'  => $voucher_date,
                                'acc_txn_dr_cr' => ($bsd_row['billsundry_amount'] < 0) ? 2 : 1,
                                'acc_txn_amt'   => $bsd_row['billsundry_amount'],
                                'acc_txn_fcy'   => $bsd_row['billsundry_fcy_amount'],
                                'vch_txn_id'    => $voucher_txn_id,
                                'txn_id'        => $bsd_txn_id,
                                'hobo_id'       => $this->bo_id,
                                'acc_txn_type'  => 1
                            ];
                            $this->VouchersModel->add_acc_txn_data($bsd_acc_txn);

                            // short narration for BSD line
                            $this->VouchersModel->save_voucher_narration($voucher_txn_id, $bsd_txn_id, 'short', "");

                            // 2.9A — Tax Summary for BSD (mirror sale.php logic)
                            $amount_bsd   = parseAmount($bsd_row['billsundry_amount']);
                            $amount_bsd_f = parseAmount($bsd_row['billsundry_fcy_amount']);
                            $pos_code = sprintf('%02d', $pos);
							$billsundry_info = $this->VouchersModel->get_bsd_details_info($bsd_row['billsundry_id']);
						    $bsd_hsn         = $billsundry_info['bsd_hsn_sac'] ?? '';	
						    
                            // ------------------------------------------------------------------
                            // Effective rates (regular vs composition) – BILL SUNDRY
                            // ------------------------------------------------------------------
                            
                            // Defaults (regular)
                            $igst_rate_eff  = parseAmount($bsd_row['igst_rate']);
                            $cgst_rate_eff  = $igst_rate_eff / 2;
                            $sgst_rate_eff  = $igst_rate_eff / 2;
                            $utgst_rate_eff = $igst_rate_eff / 2;
                            $cess_rate_eff  = isset($bsd_row['tax_details']['cess']) ? parseAmount($bsd_row['tax_details']['cess']) : 0;
                            
                            // Override for composition (bo_gstin_type == 2)
                            if ((int)$gstinType === 2) {
                                $compsrates = get_composition_rates($cmp_supply_type);
                                $igst_rate_eff  = $compsrates['igst'];
                                $cgst_rate_eff  = $compsrates['cgst'];
                                $sgst_rate_eff  = $compsrates['sgst'];
                                $utgst_rate_eff = $compsrates['ut'];
                                $cess_rate_eff  = $compsrates['cess'] ?? 0;
                            }
                            
                            // ------------------------------------------------------------------
                            // Default tax summary row (Bill Sundry)
                            // ------------------------------------------------------------------
                            
                            $taxsummary_data_bsd = [
                                'cmp_id'             => $this->company_id,
                                'vch_txn_id'         => $voucher_txn_id,
                                'txn_id'             => $bsd_txn_id,
                                'acc_bsd_id'         => $bsd_row['billsundry_id'],
                                'acc_bsd_type'       => 2, // billsundry
                                'vch_igst'           => 0,
                                'vch_igst_rate'      => 0,
                                'vch_cgst'           => 0,
                                'vch_cgst_rate'      => 0,
                                'vch_sgst_ugst'      => 0,
                                'vch_sgst_ugst_rate' => 0,
                                'vch_cess'           => 0,
                                'vch_cess_rate'      => 0,
                                'vch_taxable_value'  => $amount_bsd,
                                'vch_total_tax'      => 0
                            ];
                            
                            // ------------------------------------------------------------------
                            // Case 1: IGST (Inter-state, not UT)
                            // ------------------------------------------------------------------
                            if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                                $igst_value = ($amount_bsd * $igst_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_igst']      = $igst_value;
                                $taxsummary_data_bsd['vch_igst_rate'] = $igst_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax'] = $igst_value;
                            
                                $tax_igst_amt += $igst_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // Case 2: CGST + SGST (Same state)
                            // ------------------------------------------------------------------
                            elseif ($bo_state_code == $pos_code) {
                            
                                $cgst_value = ($amount_bsd * $cgst_rate_eff) / 100;
                                $sgst_value = ($amount_bsd * $sgst_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_cgst']           = $cgst_value;
                                $taxsummary_data_bsd['vch_cgst_rate']      = $cgst_rate_eff;
                                $taxsummary_data_bsd['vch_sgst_ugst']      = $sgst_value;
                                $taxsummary_data_bsd['vch_sgst_ugst_rate'] = $sgst_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax']      = $cgst_value + $sgst_value;
                            
                                $tax_cgst_amt += $cgst_value;
                                $tax_sgst_amt += $sgst_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // Case 3: UTGST applies
                            // ------------------------------------------------------------------
                            elseif (in_array($pos_code, $ugst_states)) {
                            
                                $cgst_value = ($amount_bsd * $cgst_rate_eff) / 100;
                                $ugst_value = ($amount_bsd * $utgst_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_cgst']           = $cgst_value;
                                $taxsummary_data_bsd['vch_cgst_rate']      = $cgst_rate_eff;
                                $taxsummary_data_bsd['vch_sgst_ugst']      = $ugst_value;
                                $taxsummary_data_bsd['vch_sgst_ugst_rate'] = $utgst_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax']      = ($cgst_value + $ugst_value);
                            
                                $tax_cgst_amt  += $cgst_value;
                                $tax_utgst_amt += $ugst_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // Always add CESS if applicable
                            // ------------------------------------------------------------------
                            if ($cess_rate_eff > 0) {
                            
                                $cess_value = ($amount_bsd * $cess_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_cess']      = $cess_value;
                                $taxsummary_data_bsd['vch_cess_rate'] = $cess_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax'] += $cess_value;
                            
                                $tax_cess_amt += $cess_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // GST TAG LOGIC (same engine as Sale / Credit Item)
                            // ------------------------------------------------------------------
                            
                            $gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
                                ? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
                                : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);
                            
                            // ------------------------------------------------------------------
                            // Final fields + Save TAX SUMMARY (Bill Sundry)
                            // ------------------------------------------------------------------
                            
                            $taxsummary_data_bsd['vch_date']      = $voucher_date;
                            $taxsummary_data_bsd['is_outward']    = 2;   // CREDIT VOUCHER
                            $taxsummary_data_bsd['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
                            $taxsummary_data_bsd['vch_hsn_sac']   = $bsd_hsn;
                            $taxsummary_data_bsd['inv_supply_id'] = $inv_supply_id;
                            
                            $vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data_bsd, $tax_required_flag);
                            
                            // ------------------------------------------------------------------
                            // HSN SUMMARY – BILL SUNDRY
                            // ------------------------------------------------------------------
                            
                            $hsnummary_data = [
                                'cmp_id'                => $this->company_id,
                                'vch_txn_id'            => $voucher_txn_id,
                                'txn_id'                => $bsd_txn_id,
                                'vch_hsn_sac'           => $bsd_hsn,
                                'vch_hsn_sac_taxbl_val' => $amount_bsd,
                                'vch_hsn_sac_qty'       => 0,
                                'vch_hsn_sac_uom'       => 0,
                                'vch_hsn_sac_igst'      => $taxsummary_data_bsd['vch_igst'],
                                'vch_hsn_sac_cgst'      => $taxsummary_data_bsd['vch_cgst'],
                                'vch_hsn_sac_sgst_ugst' => $taxsummary_data_bsd['vch_sgst_ugst'],
                                'vch_hsn_sac_cess'      => $taxsummary_data_bsd['vch_cess'],
                                'inv_supply_id'         => $inv_supply_id,
                                'vch_gst_sum_id'        => $vch_gst_sum_id
                            ];
                            
                            $this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);
                            
                            // ------------------------------------------------------------------
                            // FCY TAX SUMMARY – BILL SUNDRY
                            // ------------------------------------------------------------------
                            
                            if (parseAmount($fcy_forex_rate) > 0) {
                            
                                $taxsummary_fcy_bsd = [
                                    'cmp_id'                 => $this->company_id,
                                    'vch_txn_id'             => $voucher_txn_id,
                                    'txn_id'                 => $bsd_txn_id,
                                    'acc_bsd_id'             => $bsd_row['billsundry_id'],
                                    'acc_bsd_type'           => 2,
                                    'vch_igst_fcy'           => 0,
                                    'vch_cgst_fcy'           => 0,
                                    'vch_sgst_ugst_fcy'      => 0,
                                    'vch_cess_fcy'           => 0,
                                    'vch_taxable_value_fcy'  => $amount_bsd_f,
                                    'vch_total_tax_fcy'      => 0
                                ];
                            
                                if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                                    $igst_value_fcy = ($amount_bsd_f * $igst_rate_eff) / 100;
                                    $taxsummary_fcy_bsd['vch_igst_fcy']      = $igst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] = $igst_value_fcy;
                                }
                                elseif ($bo_state_code == $pos_code) {
                            
                                    $cgst_value_fcy = ($amount_bsd_f * $cgst_rate_eff) / 100;
                                    $sgst_value_fcy = ($amount_bsd_f * $sgst_rate_eff) / 100;
                            
                                    $taxsummary_fcy_bsd['vch_cgst_fcy']      = $cgst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_sgst_ugst_fcy'] = $sgst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] = $cgst_value_fcy + $sgst_value_fcy;
                                }
                                elseif (in_array($pos_code, $ugst_states)) {
                            
                                    $ugst_value_fcy = ($amount_bsd_f * $utgst_rate_eff) / 100;
                                    $cgst_value_fcy = ($amount_bsd_f * $cgst_rate_eff) / 100;
                            
                                    $taxsummary_fcy_bsd['vch_sgst_ugst_fcy']  = $ugst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] = $ugst_value_fcy + $cgst_value_fcy;
                                }
                            
                                if ($cess_rate_eff > 0) {
                            
                                    $cess_value_fcy = ($amount_bsd_f * $cess_rate_eff) / 100;
                                    $taxsummary_fcy_bsd['vch_cess_fcy']      = $cess_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] += $cess_value_fcy;
                                }
                            
                                $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_bsd);
                            }

                        } // foreach bsd_row
                    }

                    // 2.10 — Tax accounts postings (same helper as sale.php)
                     // if gstinType is 2 means Composition then no record will insert in taxable AccountsModel
    			if($gstinType==1 && $sale_against_status==1){//REGULAR TO REGULAR – NORMAL TAX (OUTPUT A/C DR.) 
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_igst_amt,1,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_sgst_amt,3,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_utgst_amt,4,$voucher_date,2);
    				   }	
    				 if($tax_cess_amt > 0)		
    				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_cess_amt,5,$voucher_date,2);
    		    } 
				// composition to Compoition
    			if ((int)$gstinType === 2 && $sale_against_status==2) {//COMPOSITION TO COMPOSITION – TAX A/C SPECIAL RATE (OUTPUT A/C DR.) 
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_igst_amt,1,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_sgst_amt,3,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_utgst_amt,4,$voucher_date,2);
    				   }		    
    				 if($tax_cess_amt >0)  
    				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_cess_amt,5,$voucher_date,2);
    		       }
               
               	if ((int)$gstinType === 2 && $sale_against_status==1) {
               	    // Tax Refund Accounts entry  will save later right now we are not creating Refubd Accounts Entry
               	    
               	} 
                    // 2.11 — Short narrations were already done per line (above)

                    // 2.12 — BBB / PR / Batch / CostCentre + cleanup of draft handled now
                    if (!empty($bbbdata)) {
                        $this->VouchersModel->SaveBillByBillData($bbbdata, $voucher_date, $voucher_txn_id, $party_txn_id);
                    }
                    if (!empty($prdata)) {
                        $this->VouchersModel->SaveProjectReportingData($prdata, $voucher_date, $voucher_txn_id, $party_txn_id);
                    }
                    
                    if (!empty($itemsbatchdata)) {
                        $this->VouchersModel->SaveBatchData($itemsbatchdata, $voucher_date, $voucher_txn_id, $party_txn_id, $matrcntr_id);
                    }

                    // 2.13 — Long narration
                    $nr_insert_data = [
                        "cmp_id"         => $this->company_id,
                        "vch_series_id"  => $voucher_series,
                        "vch_txn_id"     => $voucher_txn_id,
                        "master_id"      => 0,
                        "master_id_type" => 'nrr'
                    ];
                    $nr_txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
                    $this->VouchersModel->save_voucher_narration($voucher_txn_id, $nr_txn_id, 'long', $long_narration ?? '');

                    // 2.14 — Finalize: remove draft + clear pointer + activity log
                    $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
                    $this->VouchersModel->UpdateConso($voucher_txn_id, ["draft_vch_rec_id" => 0]);

                    $voucher_messages = [
                        2 => "New credit note with item voucher has been added by [USERNAME]([UUID])"
                    ];
                    $notification_approval_messages = [
                        2 => "credit note with item voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
                    ];
                    $notification_rejected_messages = [
                        9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
                    ];
                    $erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been added by [USERNAME]([UUID])";
                    $this->VouchersModel->SaveUserActivity($erp_activity_log, $voucher_txn_id);

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

        // -------------------- GET: prepare data for view (add_item) --------------------
        $data['voucher_type_id']       = $this->voucher_type_id;
        $data['SuppltSubSupplyTypes']  = json_encode($this->VouchersModel->SuppltSubSupplyTypes());
        $data['message_output']        = $this->message_output;
        $data['taxes_list']            = $taxes_list;
        $data['base_url']              = $this->base_url;
        $data['folder_path']           = $this->folder_path;
        $data['ugst_states']           = $this->ugst_states;

        $data['voucher_date']          = $this->VouchersModel->getLastVoucherDate($voucher_type_id);
        $data['bo_gstin_type']         = $gstinType; // regular or composition
        $data['voucher_auto_no']       = $this->VouchersModel->get_voucher_no($voucher_type_id);
        $data['voucher_series']        = $this->VouchersModel->comp_voucher_result($this->company_id, $voucher_type_id);
        $data['party_dropdown']        = $this->VouchersModel->party_dropdown();
        $data['bills_method_list']     = ['','New Ref.','Adjustment'];
		$data['sess_cmp_suply_type']         = $this->session->get('cmp_suply_type') ?? 0;// composition supply type
	    $data['comp_supply_types_dropdown'] = $this->compositionSupplyOptions;

        $data['currency_list']         = $this->VouchersModel->get_currency_list();
        $data['states_lists']          = $this->VouchersModel->show_states_lists();
        $data['eco_dropdown']          = $this->VouchersModel->show_eco_lists();
        $data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();

        $data['transporter_dropdown']  = [];
        $data['transport_modes']       = [];
        $data['vehicle_types']         = [];
        $data['matrcntr_dropdown']     = $this->VouchersModel->material_centre_dropdown();

        $data['units_list']            = $this->VouchersModel->units_dropdown();
        $data['bo_state_code']         = $this->session->get('ses_bostecd');

        $items_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
        $data['item_json_file']        = $items_list;
       
        $bsd_accounts                  = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
        if ($bsd_accounts) {
            $filter_bsd_accounts = array_filter(json_decode($bsd_accounts, true), function($row) {
                return $row['is_tax_account'] != 1; // hide tax (type 1) from grid
            });
        } else {
            $filter_bsd_accounts = [];
        }
       
        $data['bsd_json_file']         = json_encode($filter_bsd_accounts);
		$data['sub_type_dropdown']     = $this->sale_types;

        $data['units_list']            = $this->VouchersModel->units_grid();
		$data['against_dropdown']      = $this->VouchersModel->against_voucher_dropdown(18,array(0,8,9,10),date('Y-m-d'));

        // View should be analogous to sales/add_item but for credit note UI
        return view($this->folder_path.'credit_note/item_based', $data);
    }

    public function non_item(){
	  $gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);	// 1 means Regular , 2 means Composition
	
	$voucher_type_id           = $this->voucher_type_id;
	$taxes_list                = $this->VouchersModel->GetGSTTaxesList();
	$Voucher_TxnApproval_Info  = $this->VouchersModel->Voucher_TxnApproval();
	$bo_state_code             = $this->session->get('ses_bostecd');
	$ugst_states               = $this->ugst_states;
	$Voucher_TxnApproval       = $Voucher_TxnApproval_Info['approval_amt'];
	$Voucher_TxnApproval_UUID  = $Voucher_TxnApproval_Info['uuid_to']; // who will approve, reject voucher
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
    $ip = $_SERVER['REMOTE_ADDR'];
	    if($ip=='103.172.223.178' || $ip=='103.172.223.179'){
	  // echo "<pre>";print_r($_POST);die();
	   } 
   
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

		$trans_result = $this->runTransaction(function($db) use ($gstinType,$bo_state_code,$ugst_states,$taxes_list,$voucher_type_id,$Voucher_TxnApproval_UUID,$Voucher_TxnApproval){
		 	$invoice_type        = $this->request->getVar('invoice_type');
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
			$btnid               = $this->request->getVar('btnid');// submitbtn default save button
			// link back to original SALE invoice (outsup_id) for CN (as per your workflow)
            $cr_note_outsup_id   = $this->request->getVar('sale_voucher_id') ?? 0;
			$cmp_supply_type     = $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null			
			$isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			   if($billno=="" || $billno==0 || $billno <0)
				return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']];
				$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'purchase');
				if($billno_duplicate=="1")
				return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']];
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
           
		   // link back to original SALE invoice (outsup_id) for CN (as per your workflow)
                    $cr_note_outsup_info = $this->request->getVar('sale_voucher_id');
					$cr_note_outsup_id       = 0;
					$cr_note_outsup_vchtxid = 0;
					$cr_note_type_tag       = 'R';   // default Regular

					if (!empty($cr_note_outsup_info)) {

						// Break: 2492||147885||R
						$parts = explode('||', $cr_note_outsup_info);

						$cr_note_outsup_id       = $parts[0] ?? 0;
						$cr_note_outsup_vchtxid = $parts[1] ?? 0;
						$cr_note_type_tag       = $parts[2] ?? 'R';   // R or C
					}
										
                    // Apply check to get is sale voucher is posted in Regular or Compostion , we will chekc this from table "vchbridgen" table if sale  voucher vch_txn_id exists or not to aplly further conditions to save credit note
                    $sale_in_reg_comp_info = $this->VouchersModel->get_voucher_bridge_info($cr_note_outsup_vchtxid,4);
                    if($sale_in_reg_comp_info)
                      $sale_against_status = 2;// composition
                     else
                      $sale_against_status = 1;// regular
				  
				
          
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
		  /******* start of  if gstinType ==2 means composition then insert new voucher type 23  ********/
			// Sale is posted in Regular but now credit note is posted in COmposition then 
			// conditions : 
			/*
			
			 No Tax Summary
			 Blank entry in Taxable Accounts
			 No Need to creat Bridge
			 No S/J  No Created due to Non Availabity of The Refund Accounts
			 
			
			Case 1 if current is Compistion and Choosed Against Sale was in Regular
			
			
			*/
			
			
			if($gstinType==2 && $sale_against_status==1){
			    $total_tax_amount = $total_fcy_tax_amount=0;
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$cmp_supply_type)['fcy_tax_amount'];
					
			}
			//	Case 2 if current is Regular and Choosed Against Sale was in Regular
			/* else if($gstinType==1 && $sale_against_status==1){
				 $total_tax_amount = $total_fcy_tax_amount=0;
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['fcy_tax_amount'];
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
		     	
					
			} */
			
		//	Case 3 if current is Composition and Choosed Against Sale was in Compoition
			else if($gstinType==2 && $sale_against_status==2){
				 $total_tax_amount = $total_fcy_tax_amount=0;
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc',$gstinType,$cmp_supply_type)['fcy_tax_amount'];
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
		  
		  
		  $brdge_insert_data  = array(
									  "cmp_id"           => $this->company_id,		  
									  "vch_bridge_type"    => 1,          //1 for Credit Note
									  "vch_txn_id_src"      => $cr_note_outsup_vchtxid,
									  "vch_txn_id_dest"  => $voucher_txn_id
									 );									 
					$this->VouchersModel->add_vchbridgen_txn_data($brdge_insert_data);
					
			// if current is Composition and Choosed Against Sale was in Composition
                if($gstinType==2 &&  $sale_against_status==2){ 
					$brdge_insert_data  = array(
									  "cmp_id"           => $this->company_id,		  
									  "vch_bridge_type"    => 5,  //5 for GST Red on CMP Credit Note        
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
			 $gstrinwsup_insert_data = array("cmp_id"=> (int)$this->company_id,"vch_txn_id"=>(int)$voucher_txn_id,
											"inwsup_pos"=>$pos,"inwsup_bill_ref_no"=>$outsup_bill_ref_no,"inwsup_rev_chg"=>(int)$reverse_charges,
											"inwsup_eco"=>(int)$outsup_eco,
											 "inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,
											 "gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->add_gstrinwsup_data($gstrinwsup_insert_data);
			}
			else if($isvoucher_autobillno==1){
			 $gstrinwsup_insert_data = array("cmp_id"=> $this->company_id,"vch_txn_id"=>(int)$voucher_txn_id,
											"inwsup_pos"=>$pos,"inwsup_bill_ref_no"=>$outsup_bill_ref_no,"inwsup_rev_chg"=>(int)$reverse_charges,
											"inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,"inwsup_eco"=>(int)$outsup_eco,
											"gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->add_gstrinwsup_data($gstrinwsup_insert_data);	
			}
			
		 	$voucher_invoice_value = $sale_total + $billsundry_total + $total_tax_amount;
		
			$voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;
            
			$voucher_memo_value = $sale_memo_total + $billsundry_memo_total;
			$voucher_fcy_memo_value = (parseAmount($fcy_forex_rate) >0)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0;
            
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
				
				$insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $party_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 2,
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
				  'acc_txn_dr_cr'     => 2,
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
							  'acc_txn_dr_cr'     => 1,
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
                				  'acc_txn_dr_cr'     => 1
                				]; 								
                				$this->VouchersModel->add_taxinc_txn_data($taxinc_insert_data);
						    
						}
						/****************  End of Save Tax Inclusive Data **********************/
						
						 							
						
						 /*******************Start of Save TAX SUMMARY ****************/

							$tax_cat_id       = $item_row['tax_cat_id'];
							$tax_detail_rates = $item_row['tax_details'];

							$amount     = parseAmount($item_row['amount']);
							$amount_fcy = parseAmount($item_row['amountfc']);

							$pos_code   = sprintf('%02d', $pos);

							$iteminfo = $this->VouchersModel->get_acc_details_info($item_row['account_id']);
							$item_hsn  = $iteminfo['acc_sac'] ?? '';

							// --------------------------------------------------
							// 🔥 GST DECISION FLAGS (APPLY ONCE)
							// --------------------------------------------------
							$apply_tax             = true;
							$use_composition_rates = false;

							// Case: Regular → Regular → NO TAX
							if ((int)$gstinType === 1 && (int)$sale_against_status === 1) {
								$apply_tax = false;
							}

							// Case: Composition → Composition → COMPOSITION TAX
							elseif ((int)$gstinType === 2 && (int)$sale_against_status === 2) {
								$use_composition_rates = true;
							}

							// Case: Composition → Regular → NORMAL TAX (default)

							// --------------------------------------------------
							// Base rates
							// --------------------------------------------------
							$igst_rate = $item_row['igst_rate'] ?? 0;
							$cess_rate = $tax_detail_rates['cess'] ?? 0;

							// --------------------------------------------------
							// Composition override
							// --------------------------------------------------
							if ($use_composition_rates) {
								helper('composition');
								$compsrates = get_composition_rates($cmp_supply_type);
								$igst_rate  = $compsrates['igst'];
								$cess_rate  = $compsrates['cess'] ?? 0;
							}

							// --------------------------------------------------
							// DEFAULT TAX SUMMARY (INR)
							// --------------------------------------------------
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

							// --------------------------------------------------
							// 🔥 TAX CALCULATION (INR)
							// --------------------------------------------------
							if ($apply_tax && $igst_rate > 0) {

								if ($bo_state_code != $pos_code) {

									$igst_value = ($amount * $igst_rate) / 100;

									$taxsummary_data['vch_igst']      = $igst_value;
									$taxsummary_data['vch_igst_rate'] = $igst_rate;
									$taxsummary_data['vch_total_tax'] = $igst_value;

									$tax_igst_amt += $igst_value;

								} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$sgst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

									$tax_cgst_amt += $cgst_value;
									$tax_sgst_amt += $sgst_value;

								} elseif (in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$ugst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $ugst_value;

									$tax_cgst_amt  += $cgst_value;
									$tax_utgst_amt += $ugst_value;
								}

								if ($cess_rate > 0) {
									$cess_value = ($amount * $cess_rate) / 100;

									$taxsummary_data['vch_cess']      = $cess_value;
									$taxsummary_data['vch_cess_rate'] = $cess_rate;
									$taxsummary_data['vch_total_tax'] += $cess_value;

									$tax_cess_amt += $cess_value;
								}
							}

							// --------------------------------------------------
							// Remaining common fields
							// --------------------------------------------------
							$gst_tag = in_array($inv_supply_id, [1,2,3], true)
								? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : null))
								: ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);

							$taxsummary_data['vch_date']      = $voucher_date;
							$taxsummary_data['is_outward']    = 2;
							$taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate) . ',' . parseAmount($cess_rate);
							$taxsummary_data['vch_hsn_sac']   = $item_hsn;
							$taxsummary_data['inv_supply_id'] = $inv_supply_id;

							$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

							// --------------------------------------------------
							// HSN SUMMARY
							// --------------------------------------------------
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

							// --------------------------------------------------
							// 🔥 FCY TAX SUMMARY (SAME GST RULES)
							// --------------------------------------------------
							if (parseAmount($fcy_forex_rate) > 0) {

								$taxsummary_fcy_data = [
									'cmp_id'                    => $this->company_id,
									'vch_txn_id'                => $voucher_txn_id,
									'txn_id'                    => $txn_id,
									'acc_bsd_id'                => $item_row['account_id'],
									'acc_bsd_type'              => 1,
									'vch_igst_fcy'              => 0,
									'vch_cgst_fcy'              => 0,
									'vch_sgst_ugst_fcy'         => 0,
									'vch_cess_fcy'              => 0,
									'vch_taxable_value_fcy'     => $amount_fcy,
									'vch_total_tax_fcy'         => 0
								];

								if ($apply_tax && $igst_rate > 0) {

									if ($bo_state_code != $pos_code) {

										$igst_value = ($amount_fcy * $igst_rate) / 100;
										$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;

									} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$sgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;

									} elseif (in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$ugst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $ugst_value;
									}

									if ($cess_rate > 0) {
										$cess_value = ($amount_fcy * $cess_rate) / 100;
										$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
									}
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
							
						
						 /*******************Start of Save TAX SUMMARY (BILL SUNDRY) ****************/

							$tax_cat_id       = $item_row['tax_cat_id'];
							$tax_detail_rates = $item_row['tax_details'];

							$amount     = parseAmount($item_row['billsundry_amount']);
							$amount_fcy = parseAmount($item_row['billsundry_fcy_amount']);

							$pos_code = sprintf('%02d', $pos);

							$billsundry_info = $this->VouchersModel->get_bsd_details_info($item_row['billsundry_id']);
							$bsd_hsn         = $billsundry_info['bsd_hsn_sac'] ?? '';

							// --------------------------------------------------
							// 🔥 GST DECISION FLAGS
							// --------------------------------------------------
							$apply_tax             = true;
							$use_composition_rates = false;

							// Case: Regular → Regular → NO TAX
							if ((int)$gstinType === 1 && (int)$sale_against_status === 1) {
								$apply_tax = false;
							}
							// Case: Composition → Composition → COMPOSITION TAX
							elseif ((int)$gstinType === 2 && (int)$sale_against_status === 2) {
								$use_composition_rates = true;
							}
							// Case: Composition → Regular → NORMAL TAX (default)

							// --------------------------------------------------
							// Base rates
							// --------------------------------------------------
							$igst_rate = $item_row['igst_rate'] ?? 0;
							$cess_rate = $tax_detail_rates['cess'] ?? 0;

							// --------------------------------------------------
							// Composition override
							// --------------------------------------------------
							if ($use_composition_rates) {
								helper('composition');
								$compsrates = get_composition_rates($cmp_supply_type);
								$igst_rate  = $compsrates['igst'];
								$cess_rate  = $compsrates['cess'] ?? 0;
							}

							// --------------------------------------------------
							// DEFAULT TAX SUMMARY (INR)
							// --------------------------------------------------
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

							// --------------------------------------------------
							// 🔥 TAX CALCULATION (INR)
							// --------------------------------------------------
							if ($apply_tax && $igst_rate > 0) {

								if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {

									$igst_value = ($amount * $igst_rate) / 100;

									$taxsummary_data['vch_igst']      = $igst_value;
									$taxsummary_data['vch_igst_rate'] = $igst_rate;
									$taxsummary_data['vch_total_tax'] = $igst_value;

									$tax_igst_amt += $igst_value;

								} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$sgst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

									$tax_cgst_amt += $cgst_value;
									$tax_sgst_amt += $sgst_value;

								} elseif (in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$ugst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $ugst_value;

									$tax_cgst_amt  += $cgst_value;
									$tax_utgst_amt += $ugst_value;
								}

								if ($cess_rate > 0) {
									$cess_value = ($amount * $cess_rate) / 100;

									$taxsummary_data['vch_cess']      = $cess_value;
									$taxsummary_data['vch_cess_rate'] = $cess_rate;
									$taxsummary_data['vch_total_tax'] += $cess_value;

									$tax_cess_amt += $cess_value;
								}
							}

							// --------------------------------------------------
							// COMMON FIELDS
							// --------------------------------------------------
							$taxsummary_data['vch_date']      = $voucher_date;
							$taxsummary_data['is_outward']    = 2;
							$taxsummary_data['gst_rate_grp']  = '1,' . parseAmount($igst_rate) . ',' . parseAmount($cess_rate);
							$taxsummary_data['vch_hsn_sac']   = $bsd_hsn;
							$taxsummary_data['inv_supply_id'] = 0;

							// SAVE SUMMARY
							$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

							// --------------------------------------------------
							// HSN SUMMARY
							// --------------------------------------------------
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

							$this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);

							// --------------------------------------------------
							// 🔥 FCY TAX SUMMARY (SAME GST RULES)
							// --------------------------------------------------
							if (parseAmount($fcy_forex_rate) > 0) {

								$taxsummary_fcy_data = [
									'cmp_id'                => $this->company_id,
									'vch_txn_id'            => $voucher_txn_id,
									'txn_id'                => $txn_id,
									'acc_bsd_id'            => $item_row['billsundry_id'],
									'acc_bsd_type'          => 2,
									'vch_igst_fcy'          => 0,
									'vch_cgst_fcy'          => 0,
									'vch_sgst_ugst_fcy'     => 0,
									'vch_cess_fcy'          => 0,
									'vch_taxable_value_fcy' => $amount_fcy,
									'vch_total_tax_fcy'     => 0
								];

								if ($apply_tax && $igst_rate > 0) {

									if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {

										$igst_value = ($amount_fcy * $igst_rate) / 100;
										$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;

									} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$sgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;

									} elseif (in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$ugst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $ugst_value;
									}

									if ($cess_rate > 0) {
										$cess_value = ($amount_fcy * $cess_rate) / 100;
										$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
									}
								}

								$this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
							}

							/*******************End of Save TAX SUMMARY (BILL SUNDRY) ****************/
					}
				}		 
			 /*********************End of Save Billsundry Wise Grid Entries **********************/
			 
			 /***************Start of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			 // if gstinType is 2 means Composition then no record will insert in taxable AccountsModel
    			if($gstinType==1 && $sale_against_status==1){
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_igst_amt,1,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_sgst_amt,3,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_utgst_amt,4,$voucher_date,1);
    				   }	
    				 if($tax_cess_amt > 0)		
    				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cess_amt,5,$voucher_date,1);
    		    } 
    			if ((int)$gstinType === 2 && $sale_against_status==2) {
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_igst_amt,1,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_sgst_amt,3,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_utgst_amt,4,$voucher_date,1);
    				   }		    
    				 if($tax_cess_amt >0)  
    				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cess_amt,5,$voucher_date,1);
    		       }
               	if ((int)$gstinType === 2 && $sale_against_status==1) {
               	    // Tax Refund Accounts entry  will save later right now we are not creating Refund Accounts Entry
               	    
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
			2  => "New credit note w/o item voucher has been added by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			2  => "credit note w/o item voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			2  => "credit note voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",			
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
	$data['bo_gstin_type']         = $gstinType;// regular
	$data['voucher_auto_no']       = $this->VouchersModel->get_voucher_no($voucher_type_id);
	$data['voucher_bill_no']       = $this->VouchersModel->get_billno_format($voucher_type_id,0,date('Y-m-d'),1,'counter');
	$data['voucher_series']        = $this->VouchersModel->comp_voucher_result($this->company_id,$voucher_type_id);
	$data['party_dropdown']        = $this->VouchersModel->party_dropdown();
	$data['bills_method_list']     = ['','New Ref.','Adjustment'];
	$data['against_dropdown']      = $this->VouchersModel->against_voucher_dropdown(18,array(0,8,9,10),date('Y-m-d'));
	$accounts_list                 = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	$bsd_accounts                  = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	if($bsd_accounts){
	$filter_bsd_accounts    	   = array_filter(json_decode($bsd_accounts,true), function($row) {
										return $row['is_tax_account'] != 1;
										});
	}
	else{
	    $filter_bsd_accounts   =[];
	}
	$data['accounts_json_file']    = $accounts_list;
	$data['bsd_json_file']         = json_encode($filter_bsd_accounts);		 
	$data['bo_state_code']         = $bo_state_code;
	$data['currency_list']         = $this->VouchersModel->get_currency_list();
	$data['states_lists']          = $this->VouchersModel->show_states_lists();
	$data['eco_dropdown']          = $this->VouchersModel->show_eco_lists();
    $data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();
	
	$data['sess_cmp_suply_type']         = $this->session->get('cmp_suply_type') ?? 0;// composition supply type
	$data['comp_supply_types_dropdown']  = $this->compositionSupplyOptions;
    
	return view($this->folder_path.'credit_note/add_non_item',$data);	  	    
  }

    public function edit($voucher_txn_id,$acc_id = 0){
	$voucher_type_id    = $this->voucher_type_id;
	 $gstinType           = (int) ($this->session->get('bo_gstin_type') ?? 1);
	$voucher_info       = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
	if(empty($voucher_info)){
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
	$vch_subtype_id            = $voucher_info['vch_sub_type_id'];		
	$taxes_list                = $this->VouchersModel->GetGSTTaxesList();
	$Voucher_TxnApproval_Info  = $this->VouchersModel->Voucher_TxnApproval();
	$get_narration             = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
	$get_gstroutsup_info       = $this->VouchersModel->get_gstrinwsup_info($voucher_txn_id);
	$istaxinc                  = $this->VouchersModel->check_sale_taxinc($voucher_txn_id);
	$ismemosale                = $this->VouchersModel->check_sale_memoentry($voucher_txn_id);
	$bo_state_code             = $this->session->get('ses_bostecd');
	$ugst_states               = $this->ugst_states;
	$Voucher_TxnApproval       = $Voucher_TxnApproval_Info['approval_amt'];
	$Voucher_TxnApproval_UUID  = $Voucher_TxnApproval_Info['uuid_to']; // who will approve, reject voucher
	$data['draft_vch_rec_id'] = 0;
	$data['sess_cmp_suply_type']         = $this->session->get('cmp_suply_type') ?? 0;// composition supply type
	$data['comp_supply_types_dropdown'] = $this->compositionSupplyOptions;
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
	  if($vch_subtype_id==1){ // For Item Code
       //echo '<pre>';print_r($_POST);die();
	    $rules = [
                'voucher_date' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Voucher Date is required'],
                ],
                'voucher_series' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Voucher Series is required'],
                ],
                'party_id' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Party is required'],
                ],
                'matrcntr_id' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Material Center is required'],
                ],
                'itmsdata' => [
                    'rules'  => 'required',
                    'errors' => ['required' => 'Data is required'],
                ],
            ];

            if (!$this->validate($rules)) {
                $errors = $this->validator->getErrors();
                return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }

            // ----------------------- Transaction wrapper ----------------------------
            $trans_result = $this->runTransaction(function($db) use ($gstinType,$voucher_txn_id,$bo_state_code, $ugst_states, $taxes_list, $voucher_type_id, $Voucher_TxnApproval_UUID, $Voucher_TxnApproval) {                
                try {
                    // ------------------------ Read POST vars --------------------------
                    $voucher_date       = $this->request->getVar('voucher_date');
                    $voucher_date       = validate_date_by_fy($voucher_date);

                    $voucher_series     = $this->request->getVar('voucher_series');
                    $party_id           = $this->request->getVar('party_id');
                    $matrcntr_id        = $this->request->getVar('matrcntr_id');
                    $long_narration     = $this->request->getVar('narration');

                    $itmsdata           = json_decode($this->request->getVar('itmsdata'), true);           // items grid
                    $billsndrydata      = json_decode($this->request->getVar('billsndrydata'), true) ?? []; // only bsd type 0 shown in UI
                    $itemsbatchdata     = json_decode($this->request->getVar('itemsbatchdata'), true) ?? [];
                    $bbbdata            = json_decode($this->request->getVar('bbbdata'), true) ?? [];
                    $prdata             = json_decode($this->request->getVar('prdata'), true) ?? [];
                    $ccdata             = json_decode($this->request->getVar('ccdata'), true) ?? [];
					
                    $currency_id        = $this->request->getVar('currency_id');
                    $billno             = $this->request->getVar('billno');
                    $vch_sub_type_id    = $this->request->getVar('sale_type');
                    $reverse_charges    = $this->request->getVar('reverse_charges');
                    $pos                = $this->request->getVar('pos');
					$pos_code           = sprintf('%02d', $pos);	
                    $fcy_forex_rate     = $this->request->getVar('fcy_forex_rate');
                    $memoCheck          = $this->request->getVar('memoCheck'); // "1" if memo grid active
                    $taxInclusive       = $this->request->getVar('taxInclusive');
                    $outsup_eco         = $this->request->getVar('eco_id') ?? 0;
                    $btnid              = $this->request->getVar('btnid'); // submitbtn or submitbtn_drft
					$tax_required_flag   = $this->request->getVar('tax_required_flag');
					$cmp_supply_type     = $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null	
                    $isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
					
					
					
					
				if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
				   if($billno=="" || $billno==0 || $billno <0)
					return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']];
					$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'purchase',1,$voucher_txn_id);
					if($billno_duplicate=="1")
					return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. should be unique.']];
				}
					
					
					// link back to original SALE invoice (outsup_id) for CN (as per your workflow)
                    $cr_note_outsup_info = $this->request->getVar('sale_voucher_id');
					$cr_note_outsup_id       = 0;
					$cr_note_outsup_vchtxid = 0;
					$cr_note_type_tag       = 'R';   // default Regular

					if (!empty($cr_note_outsup_info)) {

						// Break: 2492||147885||R
						$parts = explode('||', $cr_note_outsup_info);

						$cr_note_outsup_id       = $parts[0] ?? 0;
						$cr_note_outsup_vchtxid = $parts[1] ?? 0;
						$cr_note_type_tag       = $parts[2] ?? 'R';   // R or C
					}
										
                    // Apply check to get is sale voucher is posted in Regular or Compostion , we will chekc this from table "vchbridgen" table if sale  voucher vch_txn_id exists or not to aplly further conditions to save credit note
                    $sale_in_reg_comp_info = $this->VouchersModel->get_voucher_bridge_info($cr_note_outsup_vchtxid,4);
                    if($sale_in_reg_comp_info)
                      $sale_against_status = 2;// composition
                     else
                      $sale_against_status = 1;// regular


                    $is_batch = !empty($itemsbatchdata);
                    $is_bbb   = !empty($bbbdata);
                    $is_pr    = !empty($prdata);

                    // -------------------- Party name for draft particulars -------------
                    $vch_particulars = '';
                    if ($party_id) {
                        $acc_info = $this->VouchersModel->account_detail_info($party_id);
                        $vch_particulars = $acc_info['acc_name'] ?? '';
                    }
                    if ($vch_particulars === '') {
                        return ['status' => false, 'message' => 'Party field is not valid'];
                    }
					
					// Mark Dirty Transactions
						$voucherDate = new \DateTime($voucher_date);
						$today       = new \DateTime('today');
						 if($voucherDate < $today){ 
							 // Call your dirty recalculation function
							 $this->VouchersModel->markSnapshotDirty($voucher_date);
						 }
			 
                    // -------------------- Totals (mirror Sales.php fields) -------------
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
					
					//echo $total_tax_amount;
					//die();
                    // ---------------------- Section 1: Save Draft ----------------------
                    $voucherdata = json_encode([
                        "is_batch"     => $is_batch,
                        "batchdata"    => $itemsbatchdata,
                        "bbbdata"      => $bbbdata,
                        "prdata"       => $prdata,
                        "mat_cent_id"  => $matrcntr_id,
                        "itmsdata"     => $itmsdata,
                        "billsndrydata"=> $billsndrydata
                    ]);
                    $pgrdata_drft = [
                        "cmp_id"         => $this->company_id,
                        "hobo_id"        => $this->bo_id,
                        "log_date_time"  => $voucher_date,
                        "vch_type_id"    => $voucher_type_id,
                        "vch_json_data"  => $voucherdata,
                        "vch_amt"        => parseAmount($sale_total + $billsundry_total),
                        "vch_fcy_amt"    => parseAmount($sale_fcy_total + $billsundry_fcy_total),
                        "vch_fcy_rate"   => (float)$fcy_forex_rate ?? 0,
                        "vch_particulars"=> $vch_particulars,
                        "uuid_aictly"    => $this->session->get('uuid'),
                        "long_narr"      => $long_narration,
                        "vch_series_id"  => (int)$voucher_series,
                        "isoptional"     => FALSE,
                        "vch_fcy_id"     => (int)$currency_id,
                        "is_cc"          => 0,
                        "is_bbb"         => (int)$is_bbb,
                        "is_pr"          => (int)$is_pr,
                        "is_sblgr"       => 0
                    ];
                    $draft_vch_rec_id = $this->VouchersModel->SaveDrftVoucher($pgrdata_drft);
                    if ($btnid === 'submitbtn_drft') {
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
			   $this->VouchersModel->clear_system_journal_txn_data($voucher_txn_id,5); 
                    // ------------------ Section 2: Create voucher conso -----------------
					
			  
					/******* start of  if gstinType ==2 means composition then insert new voucher type 23  ********/
			// Sale is posted in Regular but now credit note is posted in COmposition then 
			// conditions : 
			/*
			
			 No Tax Summary
			 Blank entry in Taxable Accounts
			 No Need to creat Bridge
			 No S/J  No Created due to Non Availabity of The Refund Accounts
			 
			
			Case 1 if current is Compistion and Choosed Against Sale was in Regular
			
			
			*/ 		
			if($gstinType==2 && $sale_against_status==1){
			
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
					
			}
		//	Case 2 if current is Regular and Choosed Against Sale was in Regular
			/* else if($gstinType==1 && $sale_against_status==1){
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
			
				//SaveErrorLog("item tax -> ".$this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount']);
				//SaveErrorLog("bsd tax -> ".$this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount']);
				
			
				$comp_vch_type_id =23;
				
				$voucher_series_id =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				 $get_voucher_bridge_info = $this->VouchersModel->get_voucher_bridge_info($voucher_txn_id,4);
			   if(!$get_voucher_bridge_info)
				$comp_voucher_txn_id = $voucher_txn_id;
			    else
			    $comp_voucher_txn_id     = $get_voucher_bridge_info['vch_txn_id_dest'];

				
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
		     	
					
			} */
			
		//	Case 3 if current is Composition and Choosed Against Sale was in Compoition
			else if($gstinType==2 && $sale_against_status==2){
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
			
				$comp_vch_type_id =23;
				$voucher_series_id =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				$get_voucher_bridge_info = $this->VouchersModel->get_voucher_bridge_info($voucher_txn_id,5);
			    if(!$get_voucher_bridge_info)
				$comp_voucher_txn_id = $voucher_txn_id;
			    else
			    $comp_voucher_txn_id     = $get_voucher_bridge_info['vch_txn_id_dest'];
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
		     	
					// make entry in baridge table if composition is set as 2 
                if($gstinType==2){ 
					$brdge_update_data  = array(
									  "vch_txn_id_src"      => $voucher_txn_id
									 );									 
					$this->VouchersModel->update_voucher_bridge_data($brdge_update_data,$comp_voucher_txn_id,5);
				}
			
			}
			else{
				
				
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type,$voucher_type_id,$pos_code)['fcy_tax_amount'];
				
	   
			}			
					
					
					
					
            $update_data  = array(
			  "vch_series_id"   => $voucher_series,
			  "vch_date"         => $voucher_date,
			  "draft_vch_rec_id" => $draft_vch_rec_id,
			  "mat_cent_id"      => $matrcntr_id
			);
			$this->VouchersModel->update_voucher_cons_data($update_data,$voucher_txn_id,$this->company_id);
			
			
			
			if(parseAmount($fcy_forex_rate) >0){
			$fcy_data = array("cmp_id"=>$this->company_id,"vch_txn_id"=>$voucher_txn_id,
						   "vch_fcy_rate"=>$fcy_forex_rate,"cmp_fcy_mst_id"=>$currency_id
						  );	
			$this->VouchersModel->save_voucher_fcyrate($fcy_data);	
			}
			else{				
				$this->VouchersModel->clear_voucher_fcyrate($voucher_txn_id);
		  	} 
                    // 2.4 — GST header (Credit Note goes to gstrinwsup with link to outsup_id)
                    if ($isvoucher_autobillno == 0) {
                        
                        $gstrin_insert = [
                            "inwsup_pos"          => $pos,
                            "inwsup_bill_ref_no"  => $billno,
                            "inwsup_rev_chg"      => (int)$reverse_charges,                            
                            "inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,
                            "inwsup_eco"          => (int)$outsup_eco,
							"gst_supply_type"=>(int)$cmp_supply_type		
                        ];
                        $this->VouchersModel->update_gstrinwsup_data((int)$voucher_txn_id,$gstrin_insert);
                    } else {
                        $gstrin_insert = [
                            "inwsup_pos"          => $pos,
                            "inwsup_rev_chg"      => (int)$reverse_charges,                            
                            "inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,
                            "inwsup_eco"          => (int)$outsup_eco,
							"gst_supply_type"=>(int)$cmp_supply_type
                        ];
                        $this->VouchersModel->update_gstrinwsup_data((int)$voucher_txn_id,$gstrin_insert);
                    }
                   
					 
                    // Invoice totals
                    $voucher_invoice_value     = $sale_total + $billsundry_total + $total_tax_amount;
                    $voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;

                    $voucher_memo_value        = $sale_memo_total + $billsundry_memo_total;
                    $voucher_fcy_memo_value    = (parseAmount($fcy_forex_rate) >0) ? ($sale_memo_total + $billsundry_memo_total) * $fcy_forex_rate : 0;

                    // 2.5 — Party account (CR) + register (acc_txn_type per approval limit)
                    $acc_txn_type = ($Voucher_TxnApproval!='' && parseAmount($voucher_invoice_value) > parseAmount($Voucher_TxnApproval)) ? 4 : 1;

                    // link row in cmptxnmstn (master_id_type=acc)
                    $party_comp_txn = [
                        "cmp_id"          => $this->company_id,
                        "vch_series_id"   => $voucher_series,
                        "vch_txn_id"      => $voucher_txn_id,
                        "master_id"       => $party_id,
                        "master_id_type"  => 'acc'
                    ];
                    $party_txn_id = $this->VouchersModel->add_comp_txn_data($party_comp_txn);

                    // accttxnmst: CREDIT party (2)
                    $party_acc_txn = [
                        'cmp_id'        => $this->company_id,
                        'acc_id'        => $party_id,
                        'acc_txn_date'  => $voucher_date,
                        'acc_txn_dr_cr' => 2,
                        'acc_txn_amt'   => $voucher_invoice_value,
                        'acc_txn_fcy'   => $voucher_fcy_invoice_value,
                        'vch_txn_id'    => $voucher_txn_id,
                        'txn_id'        => $party_txn_id,
                        'hobo_id'       => $this->bo_id,
                        'acc_txn_type'  => $acc_txn_type
                    ];
                    $this->VouchersModel->add_acc_txn_data($party_acc_txn);
					SaveErrorLog("add_acc_txn_data".json_encode($party_acc_txn));

                    // Memo (party) if any — (same style as sales.php): type=3
                    if ($voucher_memo_value > 0) {
                        $memo_party_txn = [
                            'cmp_id'        => $this->company_id,
                            'acc_id'        => $party_id,
                            'acc_txn_date'  => $voucher_date,
                            'acc_txn_dr_cr' => 2,
                            'acc_txn_amt'   => $voucher_memo_value,
                            'acc_txn_fcy'   => $voucher_fcy_memo_value,
                            'vch_txn_id'    => $voucher_txn_id,
                            'txn_id'        => $party_txn_id,
                            'hobo_id'       => $this->bo_id,
                            'acc_txn_type'  => 3
                        ];
                        $this->VouchersModel->add_acc_txn_data($memo_party_txn);
                    }

                    // Register (long) for party
                    $long_register_insert = [
                        'acct_vch_type'  => $voucher_type_id,
                        'cmp_id'         => $this->company_id,
                        'vch_txn_id'     => $voucher_txn_id,
                        'txn_id'         => NULL,
                        'vch_date'       => $voucher_date,
                        'acc_id'         => $party_id,
                        'acc_txn_dr_amt' => 0,
                        'acc_txn_cr_amt' => $voucher_invoice_value,
                        'vch_narr'       => $long_narration ?? '',
                        'hobo_id'        => $this->bo_id,
                        'acc_txn_type'   => $acc_txn_type
                    ];
                    $this->VouchersModel->add_register_txn_data($long_register_insert);

                    // 2.6 — Item lines (Inventory DR; memo optional)
                    $itm_txn_type   = 1; // regular
                    $tax_igst_amt   = 0;
                    $tax_cgst_amt   = 0;
                    $tax_sgst_amt   = 0;
                    $tax_utgst_amt  = 0;
                    $tax_cess_amt   = 0;

                    $item_account_totals      = []; // account_id => amount (INR)
                    $item_account_fcy_totals  = []; // account_id => fcy amount

                    if (!empty($itmsdata)) {
                        foreach ($itmsdata as $item_row) {
							$inv_supply_id   = $item_row['supply_type_id'];
                            // cmptxnmstn link for item (master_id_type=itm)
                            $itm_comp_txn = [
                                "cmp_id"         => $this->company_id,
                                "vch_series_id"  => $voucher_series,
                                "vch_txn_id"     => $voucher_txn_id,
                                "master_id"      => $item_row['item_id'],
                                "master_id_type" => 'itm'
                            ];
                            $itm_txn_id = $this->VouchersModel->add_comp_txn_data($itm_comp_txn);

                            // Memo entry for item (if opted)
                            if ($memoCheck == "1" && isset($item_row['memo_amount']) && $item_row['memo_amount'] > 0) {
                                $memo_insert_data = [
                                    'cmp_id'         => $this->company_id,
                                    'itm_id_unit_id' => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                                    'itm_txn_qty'    => $item_row['item_qty'],
                                    'itm_txn_date'   => $voucher_date,
                                    'itm_txn_dr_cr'  => 1, // DR for inward memo
                                    'itm_txn_rate'   => 1,
                                    'itm_txn_amt'    => parseAmount($item_row['memo_amount']),
                                    'itm_txn_fcy'    => 0,
                                    'vch_txn_id'     => $voucher_txn_id,
                                    'txn_id'         => $itm_txn_id,
                                    'mat_cent_id'    => $matrcntr_id,
                                    'hobo_id'        => $this->bo_id,
                                    'itm_txn_type'   => 3
                                ];
                                $this->VouchersModel->add_itm_txn_data($memo_insert_data);
                            }

                            // Regular item DR (inventory inward)
                            if (isset($item_row['item_qty']) && $item_row['item_qty'] > 0) {
                                $itm_txn_insert = [
                                    'cmp_id'         => $this->company_id,
                                    'itm_id_unit_id' => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                                    'itm_txn_qty'    => $item_row['item_qty'],
                                    'itm_txn_date'   => $voucher_date,
                                    'itm_txn_dr_cr'  => 1, // DR
                                    'itm_txn_rate'   => parseAmountPrice($item_row['item_price'], 4),
                                    'itm_txn_amt'    => parseAmount($item_row['item_total_amount']),
                                    'itm_txn_fcy'    => parseAmount($item_row['item_total_fcy_amount']),
                                    'vch_txn_id'     => $voucher_txn_id,
                                    'txn_id'         => $itm_txn_id,
                                    'mat_cent_id'    => $matrcntr_id,
                                    'hobo_id'        => $this->bo_id,
                                    'itm_txn_type'   => $itm_txn_type
                                ];
                                $this->VouchersModel->add_itm_txn_data($itm_txn_insert);

                                // Short narration for item line
                                $this->VouchersModel->save_voucher_narration($voucher_txn_id, $itm_txn_id, 'short', $item_row['description']);

                                // Tax-inclusive aux data (acctamtinc)
                                if ($taxInclusive && isset($item_row['txinc_amount']) && $item_row['txinc_amount'] > 0) {
                                    $taxinc_insert = [
                                        'cmp_id'           => $this->company_id,
                                        'acc_itm_id'       => $item_row['account_id'],                                       
                                        'acc_txn_inc_amt'  => $item_row['txinc_amount'],
                                        'vch_txn_id'       => $voucher_txn_id,
                                        'txn_id'           => $itm_txn_id,
                                        'acc_txn_dr_cr'    => 1 // DR since item inward reduces revenue
                                    ];
                                    $this->VouchersModel->add_taxinc_txn_data($taxinc_insert);
                                }

                                // Item register (main)
                                $itm_reg_data = [
                                    'itm_vch_type'   => $voucher_type_id,
                                    'cmp_id'         => $this->company_id,
                                    'vch_txn_id'     => $voucher_txn_id,
                                    'txn_id'         => $itm_txn_id,
                                    'vch_date'       => $voucher_date,
                                    'itm_id_unit_id' => $item_row['item_id'].'_'.$item_row['item_unit_id'],
                                    'vch_narr'       => $long_narration ?? '',
                                    'hobo_id'        => $this->bo_id,
                                    'itm_txn_type'   => $itm_txn_type
                                ];
                                $itm_vch_reg_id = $this->VouchersModel->add_item_register_txn_data($itm_reg_data);

                                // Item register (sub) — DR qty/amt for inward
                                $itm_reg_sub = [
                                    'itm_vch_reg_id'  => $itm_vch_reg_id,
                                    'mat_cent_id'     => $matrcntr_id,
                                    'itm_txn_dr_qty'  => $item_row['item_qty'],
                                    'itm_txn_dr_rate' => parseAmountPrice($item_row['item_price'], 4),
                                    'itm_txn_dr_amt'  => parseAmount($item_row['item_total_amount']),
                                    'itm_txn_cr_qty'  => 0,
                                    'itm_txn_cr_rate' => 0,
                                    'itm_txn_cr_amt'  => 0,
                                    'vch_narr'        => $long_narration ?? '',
                                ];
                                $this->VouchersModel->add_item_sub_register_txn_data($itm_reg_sub);

                                
                                $account_id = $item_row['item_sales_acc'];
                                $amount     = parseAmount($item_row['item_total_amount']);
                                $amount_fcy = parseAmount($item_row['item_total_fcy_amount']);
                                if (isset($item_account_totals[$account_id])) {
                                    $item_account_totals[$account_id] += $amount;
                                    $item_account_fcy_totals[$account_id] = ($item_account_fcy_totals[$account_id] ?? 0) + $amount_fcy;
                                } else {
                                    $item_account_totals[$account_id] = $amount;
                                    $item_account_fcy_totals[$account_id] = $amount_fcy;
                                }

                                // --------- Tax Summary for item row (and FCY) ----------
                                $amount_base = parseAmount($item_row['item_total_amount']);
                                $amount_fcyb = parseAmount($item_row['item_total_fcy_amount']);
                                $pos_code    = sprintf('%02d', $pos);
                                $iteminfo = $this->VouchersModel->get_item_details_info($item_row['item_id']);
                                $item_hsn = $iteminfo['itm_hsn'] ?? '';	
                                // ------------------------------------------------------------------
                                // Effective rates (regular vs composition) – CREDIT VOUCHER
                                // ------------------------------------------------------------------
                                
                                $igst_rate_eff=$cgst_rate_eff=$sgst_rate_eff=$utgst_rate_eff=$cess_rate_eff=0;
                                // Defaults (regular)
                                 if ((int)$gstinType === 1 && $sale_against_status==1) {
                                $igst_rate_eff  = parseAmount($item_row['igst_rate']);
                                $cgst_rate_eff  = $igst_rate_eff / 2;
                                $sgst_rate_eff  = $igst_rate_eff / 2;
                                $utgst_rate_eff = $igst_rate_eff / 2;
                                $cess_rate_eff  = isset($item_row['tax_details']['cess']) ? parseAmount($item_row['tax_details']['cess']) : 0;
                                 }
                                
                                // Override for composition (bo_gstin_type == 2)
                                if ((int)$gstinType === 2 && $sale_against_status==2) {
                                    $compsrates = get_composition_rates($cmp_supply_type);
                                    $igst_rate_eff  = $compsrates['igst'];
                                    $cgst_rate_eff  = $compsrates['cgst'];
                                    $sgst_rate_eff  = $compsrates['sgst'];
                                    $utgst_rate_eff = $compsrates['ut'];
                                    $cess_rate_eff  = $compsrates['cess'] ?? 0;
                                }
                                
                                // ------------------------------------------------------------------
                                // Default tax summary row
                                // ------------------------------------------------------------------
								
								$taxsummary_data = [
                                    'cmp_id'             => $this->company_id,
                                    'vch_txn_id'         => $voucher_txn_id,
                                    'txn_id'             => $itm_txn_id,
                                    'acc_bsd_id'         => $item_row['item_id'],
                                    'acc_bsd_type'       => 3, // item
                                    'vch_igst'           => 0,
                                    'vch_igst_rate'      => 0,
                                    'vch_cgst'           => 0,
                                    'vch_cgst_rate'      => 0,
                                    'vch_sgst_ugst'      => 0,
                                    'vch_sgst_ugst_rate' => 0,
                                    'vch_cess'           => 0,
                                    'vch_cess_rate'      => 0,
                                    'vch_taxable_value'  => $amount_base,
                                    'vch_total_tax'      => 0
                                ];

                                // ------------------------------------------------------------------
                                // Case 1: IGST (Inter-state, not UT)
                                // ------------------------------------------------------------------
                                if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                                
                                    $igst_value = ($amount_base * $igst_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_igst']      = $igst_value;
                                    $taxsummary_data['vch_igst_rate'] = $igst_rate_eff;
                                    $taxsummary_data['vch_total_tax'] = $igst_value;
                                
                                    $tax_igst_amt += $igst_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // Case 2: CGST + SGST (Same state)
                                // ------------------------------------------------------------------
                                elseif ($bo_state_code == $pos_code) {
                                
                                    $cgst_value = ($amount_base * $cgst_rate_eff) / 100;
                                    $sgst_value = ($amount_base * $sgst_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_cgst']           = $cgst_value;
                                    $taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
                                    $taxsummary_data['vch_sgst_ugst']      = $sgst_value;
                                    $taxsummary_data['vch_sgst_ugst_rate'] = $sgst_rate_eff;
                                    $taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;
                                
                                    $tax_cgst_amt += $cgst_value;
                                    $tax_sgst_amt += $sgst_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // Case 3: UTGST applies
                                // ------------------------------------------------------------------
                                elseif (in_array($pos_code, $ugst_states)) {
                                
                                    $cgst_value = ($amount_base * $cgst_rate_eff) / 100;
                                    $ugst_value = ($amount_base * $utgst_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_cgst']           = $cgst_value;
                                    $taxsummary_data['vch_cgst_rate']      = $cgst_rate_eff;
                                    $taxsummary_data['vch_sgst_ugst']      = $ugst_value;
                                    $taxsummary_data['vch_sgst_ugst_rate'] = $utgst_rate_eff;
                                    $taxsummary_data['vch_total_tax']      = ($cgst_value + $ugst_value);
                                
                                    $tax_cgst_amt  += $cgst_value;
                                    $tax_utgst_amt += $ugst_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // Always add CESS if applicable
                                // ------------------------------------------------------------------
                                if ($cess_rate_eff > 0) {
                                
                                    $cess_value = ($amount_base * $cess_rate_eff) / 100;
                                
                                    $taxsummary_data['vch_cess']      = $cess_value;
                                    $taxsummary_data['vch_cess_rate'] = $cess_rate_eff;
                                    $taxsummary_data['vch_total_tax'] += $cess_value;
                                
                                    $tax_cess_amt += $cess_value;
                                }
                                
                                // ------------------------------------------------------------------
                                // GST TAG LOGIC (same as Sale With Item)
                                // ------------------------------------------------------------------
                                
                                $gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
                                    ? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
                                    : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);
                                
                                // ------------------------------------------------------------------
                                // Final fields + Save TAX SUMMARY
                                // ------------------------------------------------------------------
                                
                                $taxsummary_data['vch_date']      = $voucher_date;
                                $taxsummary_data['is_outward']    = 2;   // CREDIT VOUCHER
                                $taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
                                $taxsummary_data['vch_hsn_sac']   = $item_hsn;
                                $taxsummary_data['inv_supply_id'] = $inv_supply_id;
                                
                                $vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);
                                
                                // ------------------------------------------------------------------
                                // HSN SUMMARY (same pattern as Sale)
                                // ------------------------------------------------------------------
                                
                                $hsnummary_data = [
                                    'cmp_id'                => $this->company_id,
                                    'vch_txn_id'            => $voucher_txn_id,
                                    'txn_id'                => $itm_txn_id,
                                    'vch_hsn_sac'           => $item_hsn,
                                    'vch_hsn_sac_taxbl_val' => $amount_base,
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
                                
                                // ------------------------------------------------------------------
                                // FCY TAX SUMMARY (same engine as Sale)
                                // ------------------------------------------------------------------
                                
                                if (parseAmount($fcy_forex_rate) > 0) {
                                
                                    $taxsummary_fcy_data = [
                                        'cmp_id'                 => $this->company_id,
                                        'vch_txn_id'             => $voucher_txn_id,
                                        'txn_id'                 => $itm_txn_id,
                                        'acc_bsd_id'             => $item_row['item_id'],
                                        'acc_bsd_type'           => 3,
                                        'vch_igst_fcy'           => 0,
                                        'vch_cgst_fcy'           => 0,
                                        'vch_sgst_ugst_fcy'      => 0,
                                        'vch_cess_fcy'           => 0,
                                        'vch_taxable_value_fcy'  => $amount_fcyb,
                                        'vch_total_tax_fcy'      => 0
                                    ];
                                
                                    if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                                
                                        $igst_value_fcy = ($amount_fcyb * $igst_rate_eff) / 100;
                                        $taxsummary_fcy_data['vch_igst_fcy']      = $igst_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value_fcy;
                                    }
                                    elseif ($bo_state_code == $pos_code) {
                                
                                        $cgst_value_fcy = ($amount_fcyb * $cgst_rate_eff) / 100;
                                        $sgst_value_fcy = ($amount_fcyb * $sgst_rate_eff) / 100;
                                
                                        $taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value_fcy;
                                        $taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value_fcy + $sgst_value_fcy;
                                    }
                                    elseif (in_array($pos_code, $ugst_states)) {
                                
                                        $ugst_value_fcy = ($amount_fcyb * $utgst_rate_eff) / 100;
                                        $cgst_value_fcy = ($amount_fcyb * $cgst_rate_eff) / 100;
                                
                                        $taxsummary_fcy_data['vch_sgst_ugst_fcy']  = $ugst_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] = $ugst_value_fcy + $cgst_value_fcy;
                                    }
                                
                                    if ($cess_rate_eff > 0) {
                                
                                        $cess_value_fcy = ($amount_fcyb * $cess_rate_eff) / 100;
                                        $taxsummary_fcy_data['vch_cess_fcy']      = $cess_value_fcy;
                                        $taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value_fcy;
                                    }
                                
                                    $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
                                }

                                // --------- END tax summary for item row ----------
                            }
                        } // foreach item_row
                    }

                    // 2.7 — Post item-paired account(s) (DEBIT)
                    foreach ($item_account_totals as $account_id => $amount) {
                        $amount_fcy = $item_account_fcy_totals[$account_id] ?? 0;

                        $acc_link_comp = [
                            "cmp_id"         => $this->company_id,
                            "vch_series_id"  => $voucher_series,
                            "vch_txn_id"     => $voucher_txn_id,
                            "master_id"      => $account_id,
                            "master_id_type" => 'acc'
                        ];
                        $acc_txn_link_id = $this->VouchersModel->add_comp_txn_data($acc_link_comp);

                        $acc_txn_data = [
                            'cmp_id'        => $this->company_id,
                            'acc_id'        => $account_id,
                            'acc_txn_date'  => $voucher_date,
                            'acc_txn_dr_cr' => 1, // DEBIT (sales return)
                            'acc_txn_amt'   => $amount,
                            'acc_txn_fcy'   => $amount_fcy,
                            'vch_txn_id'    => $voucher_txn_id,
                            'txn_id'        => $acc_txn_link_id,
                            'hobo_id'       => $this->bo_id,
                            'acc_txn_type'  => 1
                        ];
                        $this->VouchersModel->add_acc_txn_data($acc_txn_data);
                    }

                    // 2.8/2.9 — BillSundry (non-tax only in grid) + short narr + tax summary for BSD
                    if (!empty($billsndrydata)) {
                        foreach ($billsndrydata as $bsd_row) {
                            // cmptxnmstn link for BSD (master_id_type=bsd, using #billsundry_id as per your note)
                            $bsd_comp = [
                                "cmp_id"         => $this->company_id,
                                "vch_series_id"  => $voucher_series,
                                "vch_txn_id"     => $voucher_txn_id,
                                "master_id"      => $bsd_row['billsundry_id'],
                                "master_id_type" => 'bsd'
                            ];
                            $bsd_txn_id = $this->VouchersModel->add_comp_txn_data($bsd_comp);

                            // Memo on BSD (optional, same pattern as sale.php)
                            if ($memoCheck == "1" && isset($bsd_row['memo_amount']) && $bsd_row['memo_amount'] > 0) {
                                $memo_bsd_txn = [
                                    'cmp_id'        => $this->company_id,
                                    'acc_id'        => $bsd_row['billsundry_id'],
                                    'acc_txn_date'  => $voucher_date,
                                    'acc_txn_dr_cr' => ($bsd_row['memo_amount'] < 0) ? 2 : 1,
                                    'acc_txn_amt'   => $bsd_row['memo_amount'],
                                    'acc_txn_fcy'   => (parseAmount($fcy_forex_rate) >0) ? ($bsd_row['memo_amount'] * $fcy_forex_rate) : 0,
                                    'vch_txn_id'    => $voucher_txn_id,
                                    'txn_id'        => $bsd_txn_id,
                                    'hobo_id'       => $this->bo_id,
                                    'acc_txn_type'  => 3
                                ];
                                $this->VouchersModel->add_acc_txn_data($memo_bsd_txn);
                            }

                            // BSD account txn (dr or cr by sign exactly like sale.php)
                            $bsd_acc_txn = [
                                'cmp_id'        => $this->company_id,
                                'acc_id'        => $bsd_row['billsundry_id'],
                                'acc_txn_date'  => $voucher_date,
                                'acc_txn_dr_cr' => ($bsd_row['billsundry_amount'] < 0) ? 2 : 1,
                                'acc_txn_amt'   => $bsd_row['billsundry_amount'],
                                'acc_txn_fcy'   => $bsd_row['billsundry_fcy_amount'],
                                'vch_txn_id'    => $voucher_txn_id,
                                'txn_id'        => $bsd_txn_id,
                                'hobo_id'       => $this->bo_id,
                                'acc_txn_type'  => 1
                            ];
                            $this->VouchersModel->add_acc_txn_data($bsd_acc_txn);

                            // short narration for BSD line
                            $this->VouchersModel->save_voucher_narration($voucher_txn_id, $bsd_txn_id, 'short', "");

                            // 2.9A — Tax Summary for BSD (mirror sale.php logic)
                            $amount_bsd   = parseAmount($bsd_row['billsundry_amount']);
                            $amount_bsd_f = parseAmount($bsd_row['billsundry_fcy_amount']);
                            $pos_code = sprintf('%02d', $pos);
							$billsundry_info = $this->VouchersModel->get_bsd_details_info($bsd_row['billsundry_id']);
						    $bsd_hsn         = $billsundry_info['bsd_hsn_sac'] ?? '';	
						    
                            // ------------------------------------------------------------------
                            // Effective rates (regular vs composition) – BILL SUNDRY
                            // ------------------------------------------------------------------
                            
                            // Defaults (regular)
                            $igst_rate_eff  = parseAmount($bsd_row['igst_rate']);
                            $cgst_rate_eff  = $igst_rate_eff / 2;
                            $sgst_rate_eff  = $igst_rate_eff / 2;
                            $utgst_rate_eff = $igst_rate_eff / 2;
                            $cess_rate_eff  = isset($bsd_row['tax_details']['cess']) ? parseAmount($bsd_row['tax_details']['cess']) : 0;
                            
                            // Override for composition (bo_gstin_type == 2)
                            if ((int)$gstinType === 2) {
                                $compsrates = get_composition_rates($cmp_supply_type);
                                $igst_rate_eff  = $compsrates['igst'];
                                $cgst_rate_eff  = $compsrates['cgst'];
                                $sgst_rate_eff  = $compsrates['sgst'];
                                $utgst_rate_eff = $compsrates['ut'];
                                $cess_rate_eff  = $compsrates['cess'] ?? 0;
                            }
                            
                            // ------------------------------------------------------------------
                            // Default tax summary row (Bill Sundry)
                            // ------------------------------------------------------------------
                            
                            $taxsummary_data_bsd = [
                                'cmp_id'             => $this->company_id,
                                'vch_txn_id'         => $voucher_txn_id,
                                'txn_id'             => $bsd_txn_id,
                                'acc_bsd_id'         => $bsd_row['billsundry_id'],
                                'acc_bsd_type'       => 2, // billsundry
                                'vch_igst'           => 0,
                                'vch_igst_rate'      => 0,
                                'vch_cgst'           => 0,
                                'vch_cgst_rate'      => 0,
                                'vch_sgst_ugst'      => 0,
                                'vch_sgst_ugst_rate' => 0,
                                'vch_cess'           => 0,
                                'vch_cess_rate'      => 0,
                                'vch_taxable_value'  => $amount_bsd,
                                'vch_total_tax'      => 0
                            ];
                            
                            // ------------------------------------------------------------------
                            // Case 1: IGST (Inter-state, not UT)
                            // ------------------------------------------------------------------
                            if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                                $igst_value = ($amount_bsd * $igst_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_igst']      = $igst_value;
                                $taxsummary_data_bsd['vch_igst_rate'] = $igst_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax'] = $igst_value;
                            
                                $tax_igst_amt += $igst_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // Case 2: CGST + SGST (Same state)
                            // ------------------------------------------------------------------
                            elseif ($bo_state_code == $pos_code) {
                            
                                $cgst_value = ($amount_bsd * $cgst_rate_eff) / 100;
                                $sgst_value = ($amount_bsd * $sgst_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_cgst']           = $cgst_value;
                                $taxsummary_data_bsd['vch_cgst_rate']      = $cgst_rate_eff;
                                $taxsummary_data_bsd['vch_sgst_ugst']      = $sgst_value;
                                $taxsummary_data_bsd['vch_sgst_ugst_rate'] = $sgst_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax']      = $cgst_value + $sgst_value;
                            
                                $tax_cgst_amt += $cgst_value;
                                $tax_sgst_amt += $sgst_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // Case 3: UTGST applies
                            // ------------------------------------------------------------------
                            elseif (in_array($pos_code, $ugst_states)) {
                            
                                $cgst_value = ($amount_bsd * $cgst_rate_eff) / 100;
                                $ugst_value = ($amount_bsd * $utgst_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_cgst']           = $cgst_value;
                                $taxsummary_data_bsd['vch_cgst_rate']      = $cgst_rate_eff;
                                $taxsummary_data_bsd['vch_sgst_ugst']      = $ugst_value;
                                $taxsummary_data_bsd['vch_sgst_ugst_rate'] = $utgst_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax']      = ($cgst_value + $ugst_value);
                            
                                $tax_cgst_amt  += $cgst_value;
                                $tax_utgst_amt += $ugst_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // Always add CESS if applicable
                            // ------------------------------------------------------------------
                            if ($cess_rate_eff > 0) {
                            
                                $cess_value = ($amount_bsd * $cess_rate_eff) / 100;
                            
                                $taxsummary_data_bsd['vch_cess']      = $cess_value;
                                $taxsummary_data_bsd['vch_cess_rate'] = $cess_rate_eff;
                                $taxsummary_data_bsd['vch_total_tax'] += $cess_value;
                            
                                $tax_cess_amt += $cess_value;
                            }
                            
                            // ------------------------------------------------------------------
                            // GST TAG LOGIC (same engine as Sale / Credit Item)
                            // ------------------------------------------------------------------
                            
                            $gst_tag = in_array($inv_supply_id, [1, 2, 3], true)
                                ? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
                                : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);
                            
                            // ------------------------------------------------------------------
                            // Final fields + Save TAX SUMMARY (Bill Sundry)
                            // ------------------------------------------------------------------
                            
                            $taxsummary_data_bsd['vch_date']      = $voucher_date;
                            $taxsummary_data_bsd['is_outward']    = 2;   // CREDIT VOUCHER
                            $taxsummary_data_bsd['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate_eff) . ',' . parseAmount($cess_rate_eff);
                            $taxsummary_data_bsd['vch_hsn_sac']   = $bsd_hsn;
                            $taxsummary_data_bsd['inv_supply_id'] = $inv_supply_id;
                            
                            $vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data_bsd, $tax_required_flag);
                            
                            // ------------------------------------------------------------------
                            // HSN SUMMARY – BILL SUNDRY
                            // ------------------------------------------------------------------
                            
                            $hsnummary_data = [
                                'cmp_id'                => $this->company_id,
                                'vch_txn_id'            => $voucher_txn_id,
                                'txn_id'                => $bsd_txn_id,
                                'vch_hsn_sac'           => $bsd_hsn,
                                'vch_hsn_sac_taxbl_val' => $amount_bsd,
                                'vch_hsn_sac_qty'       => 0,
                                'vch_hsn_sac_uom'       => 0,
                                'vch_hsn_sac_igst'      => $taxsummary_data_bsd['vch_igst'],
                                'vch_hsn_sac_cgst'      => $taxsummary_data_bsd['vch_cgst'],
                                'vch_hsn_sac_sgst_ugst' => $taxsummary_data_bsd['vch_sgst_ugst'],
                                'vch_hsn_sac_cess'      => $taxsummary_data_bsd['vch_cess'],
                                'inv_supply_id'         => $inv_supply_id,
                                'vch_gst_sum_id'        => $vch_gst_sum_id
                            ];
                            
                            $this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);
                            
                            // ------------------------------------------------------------------
                            // FCY TAX SUMMARY – BILL SUNDRY
                            // ------------------------------------------------------------------
                            
                            if (parseAmount($fcy_forex_rate) > 0) {
                            
                                $taxsummary_fcy_bsd = [
                                    'cmp_id'                 => $this->company_id,
                                    'vch_txn_id'             => $voucher_txn_id,
                                    'txn_id'                 => $bsd_txn_id,
                                    'acc_bsd_id'             => $bsd_row['billsundry_id'],
                                    'acc_bsd_type'           => 2,
                                    'vch_igst_fcy'           => 0,
                                    'vch_cgst_fcy'           => 0,
                                    'vch_sgst_ugst_fcy'      => 0,
                                    'vch_cess_fcy'           => 0,
                                    'vch_taxable_value_fcy'  => $amount_bsd_f,
                                    'vch_total_tax_fcy'      => 0
                                ];
                            
                                if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {
                            
                                    $igst_value_fcy = ($amount_bsd_f * $igst_rate_eff) / 100;
                                    $taxsummary_fcy_bsd['vch_igst_fcy']      = $igst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] = $igst_value_fcy;
                                }
                                elseif ($bo_state_code == $pos_code) {
                            
                                    $cgst_value_fcy = ($amount_bsd_f * $cgst_rate_eff) / 100;
                                    $sgst_value_fcy = ($amount_bsd_f * $sgst_rate_eff) / 100;
                            
                                    $taxsummary_fcy_bsd['vch_cgst_fcy']      = $cgst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_sgst_ugst_fcy'] = $sgst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] = $cgst_value_fcy + $sgst_value_fcy;
                                }
                                elseif (in_array($pos_code, $ugst_states)) {
                            
                                    $ugst_value_fcy = ($amount_bsd_f * $utgst_rate_eff) / 100;
                                    $cgst_value_fcy = ($amount_bsd_f * $cgst_rate_eff) / 100;
                            
                                    $taxsummary_fcy_bsd['vch_sgst_ugst_fcy']  = $ugst_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] = $ugst_value_fcy + $cgst_value_fcy;
                                }
                            
                                if ($cess_rate_eff > 0) {
                            
                                    $cess_value_fcy = ($amount_bsd_f * $cess_rate_eff) / 100;
                                    $taxsummary_fcy_bsd['vch_cess_fcy']      = $cess_value_fcy;
                                    $taxsummary_fcy_bsd['vch_total_tax_fcy'] += $cess_value_fcy;
                                }
                            
                                $this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_bsd);
                            }

                        } // foreach bsd_row
                    }

                    // 2.10 — Tax accounts postings (same helper as sale.php)
                     // if gstinType is 2 means Composition then no record will insert in taxable AccountsModel
    							
				if($gstinType==1 && $sale_against_status==1){
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_igst_amt,1,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_sgst_amt,3,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_utgst_amt,4,$voucher_date,1);
    				   }	
    				 if($tax_cess_amt > 0)		
    				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,-$tax_cess_amt,5,$voucher_date,1);
    		    } 
    			if ((int)$gstinType === 2 && $sale_against_status==2) {
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_igst_amt,1,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_sgst_amt,3,$voucher_date,1);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cgst_amt,2,$voucher_date,1);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_utgst_amt,4,$voucher_date,1);
    				   }		    
    				 if($tax_cess_amt >0)  
    				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,-$tax_cess_amt,5,$voucher_date,1);
    		       }
               	if ((int)$gstinType === 2 && $sale_against_status==1) {
               	    // Tax Refund Accounts entry  will save later right now we are not creating Refubd Accounts Entry
               	    
               	} 
                    // 2.11 — Short narrations were already done per line (above)

                    // 2.12 — BBB / PR / Batch / CostCentre + cleanup of draft handled now
                    if (!empty($bbbdata)) {
                        $this->VouchersModel->SaveBillByBillData($bbbdata, $voucher_date, $voucher_txn_id, $party_txn_id);
                    }
                    if (!empty($prdata)) {
                        $this->VouchersModel->SaveProjectReportingData($prdata, $voucher_date, $voucher_txn_id, $party_txn_id);
                    }
                    if (!empty($ccdata)) {
                        $this->VouchersModel->SaveCostCentreData($ccdata, $voucher_date, $voucher_txn_id, $party_txn_id);
                    }
                    if (!empty($itemsbatchdata)) {
                        $this->VouchersModel->SaveBatchData($itemsbatchdata, $voucher_date, $voucher_txn_id, $party_txn_id, $matrcntr_id);
                    }

                    // 2.13 — Long narration
                    $nr_insert_data = [
                        "cmp_id"         => $this->company_id,
                        "vch_series_id"  => $voucher_series,
                        "vch_txn_id"     => $voucher_txn_id,
                        "master_id"      => 0,
                        "master_id_type" => 'nrr'
                    ];
                    $nr_txn_id = $this->VouchersModel->add_comp_txn_data($nr_insert_data);
                    $this->VouchersModel->save_voucher_narration($voucher_txn_id, $nr_txn_id, 'long', $long_narration ?? '');

                    // 2.14 — Finalize: remove draft + clear pointer + activity log
                    $this->VouchersModel->RemoveDrftVoucher($draft_vch_rec_id);
                    $this->VouchersModel->UpdateConso($voucher_txn_id, ["draft_vch_rec_id" => 0]);

                    $voucher_messages = [
                        2 => "New credit note with item voucher has been added by [USERNAME]([UUID])"
                    ];
                    $notification_approval_messages = [
                        2 => "credit note with item voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
                    ];
                    $notification_rejected_messages = [
                        9  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        13 => "Receipt voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        1  => "Contra voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        5  => "Journal voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])",
                        8  => "Memorandum voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"
                    ];
                    $erp_activity_log = $voucher_messages[$voucher_type_id] ?? "Voucher has been added by [USERNAME]([UUID])";
                    $this->VouchersModel->SaveUserActivity($erp_activity_log, $voucher_txn_id);

                } catch (\Throwable $e) {
                    return ['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()];
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
		
      if($vch_subtype_id==0){  // For Non Item Code
	  $forceData = $this->request->getPost();
	    // echo '<pre>';print_r($forceData);
       //die();
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
	      $trans_result = $this->runTransaction(function($db) use ($gstinType,$items_data_val,$voucher_txn_id,$bo_state_code,$ugst_states,$taxes_list,$voucher_type_id,$Voucher_TxnApproval_UUID,$Voucher_TxnApproval){
				  
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
			$tax_required_flag   = $forceData['tax_required_flag'] ?? $this->request->getVar('tax_required_flag');// submitbtn default save button
			// link back to original SALE invoice (outsup_id) for CN (as per your workflow)
            $cr_note_outsup_id   = $forceData['sale_voucher_id'] ?? $this->request->getVar('sale_voucher_id');
			$cmp_supply_type     = $forceData['cmp_suply_type'] ?? $this->request->getVar('cmp_suply_type'); // "1","2","3","4" or null
			$isvoucher_autobillno   = $this->VouchersModel->isvoucher_autobillno($voucher_type_id,$voucher_series);
			if($isvoucher_autobillno==0){ // means voucher has manual bill numbering
			   if($billno=="" || $billno==0 || $billno <0)
				return ['status' => false, 'message' => 'Validation Error', 'errors' => ['Bill No. is required']];
				$billno_duplicate = $this->VouchersModel->BillNoDuplicate($billno,'purchase',1,$voucher_txn_id);
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
       
             // link back to original SALE invoice (outsup_id) for CN (as per your workflow)
                    $cr_note_outsup_info = $this->request->getVar('sale_voucher_id');
					$cr_note_outsup_id       = 0;
					$cr_note_outsup_vchtxid = 0;
					$cr_note_type_tag       = 'R';   // default Regular

					if (!empty($cr_note_outsup_info)) {

						// Break: 2492||147885||R
						$parts = explode('||', $cr_note_outsup_info);

						$cr_note_outsup_id       = $parts[0] ?? 0;
						$cr_note_outsup_vchtxid = $parts[1] ?? 0;
						$cr_note_type_tag       = $parts[2] ?? 'R';   // R or C
					}
										
                    // Apply check to get is sale voucher is posted in Regular or Compostion , we will chekc this from table "vchbridgen" table if sale  voucher vch_txn_id exists or not to aplly further conditions to save credit note
                    $sale_in_reg_comp_info = $this->VouchersModel->get_voucher_bridge_info($cr_note_outsup_vchtxid,4);
                    if($sale_in_reg_comp_info)
                      $sale_against_status = 2;// composition
                     else
                      $sale_against_status = 1;// regular
				  
			$voucher_no = $this->VouchersModel->get_voucher_no($voucher_type_id);
			$sale_total = array_sum(array_column($itmsdata ?? [], 'amount'));
			$sale_fcy_total = array_sum(array_column($itmsdata, 'amountfc'));
			$billsundry_total = array_sum(array_column($billsndrydata, 'billsundry_amount'));
			$billsundry_fcy_total = array_sum(array_column($billsndrydata, 'billsundry_fcy_amount'));
			
			$sale_memo_total  = array_sum(array_column($itmsdata, 'memo_amount'));
			$billsundry_memo_total = array_sum(array_column($billsndrydata, 'memo_amount'));
			$sale_fcy_memo_total = 0;
			$billsundry_fcy_memo_total = 0;
			
			$total_tax_amount =0;
			$total_fcy_tax_amount =0;
		
	    	$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'acc')['fcy_tax_amount'];
	        
	        $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['tax_amount'];
	        $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd')['fcy_tax_amount'];
	        
	        
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
		   $this->VouchersModel->clear_system_journal_txn_data($voucher_txn_id,5);  
	   
	   
	   /******************     ***********/
	   
	   /*
			
			 No Tax Summary
			 Blank entry in Taxable Accounts
			 No Need to creat Bridge
			 No S/J  No Created due to Non Availabity of The Refund Accounts
			 
			
			Case 1 if current is Compistion and Choosed Against Sale was in Regular
			
			
			*/ 		
			if($gstinType==2 && $sale_against_status==1){
			
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
					
			}
		//	Case 2 if current is Regular and Choosed Against Sale was in Regular
			/* else if($gstinType==1 && $sale_against_status==2){
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
			
				$comp_vch_type_id =23;
				
				$voucher_series_id =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				 $get_voucher_bridge_info = $this->VouchersModel->get_voucher_bridge_info($voucher_txn_id,4);
			    if(!$get_voucher_bridge_info)
				$comp_voucher_txn_id = $voucher_txn_id;
			    else
			    $comp_voucher_txn_id     = $get_voucher_bridge_info['vch_txn_id_dest'];
				
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
		     	
					
			} */
			
		//	Case 3 if current is Composition and Choosed Against Sale was in Compoition
			else if($gstinType==2 && $sale_against_status==2){
				$gstpaidacc_id = $this->VouchersModel->getGstPaidAccountId();
				$total_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($itmsdata,'itm',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
	            $total_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['tax_amount'];
	            $total_fcy_tax_amount += $this->VouchersModel->GetTotalTax($billsndrydata,'bsd',$gstinType,$sale_against_status,$cmp_supply_type)['fcy_tax_amount'];
			
				$comp_vch_type_id =23;
				$voucher_series_id =  $this->VouchersModel->getSeriesId($comp_vch_type_id);
				$get_voucher_bridge_info = $this->VouchersModel->get_voucher_bridge_info($voucher_txn_id,5);
			    if(!$get_voucher_bridge_info)
				$comp_voucher_txn_id = $voucher_txn_id;
			    else
			    $comp_voucher_txn_id     = $get_voucher_bridge_info['vch_txn_id_dest'];
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
		     	
					// make entry in baridge table if composition is set as 2 
                if($gstinType==2){ 
					$brdge_update_data  = array(
									  "vch_txn_id_src"      => $voucher_txn_id
									 );									 
					$this->VouchersModel->update_voucher_bridge_data($brdge_update_data,$comp_voucher_txn_id,5);
				}
			
			}
	   
	   $update_data  = array(
          "vch_series_id"   => $voucher_series,
          "vch_date"         => $voucher_date,
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
			
			if($isvoucher_autobillno==0){
			 $gstrinwsup_insert_data = array(
											"inwsup_pos"=>$pos,"inwsup_bill_ref_no"=>$billno,"inwsup_rev_chg"=>$reverse_charges,
											"inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,"inwsup_eco"=>$outsup_eco,
											"gst_supply_type"=>(int)$cmp_supply_type,"gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->update_gstrinwsup_data($voucher_txn_id,$gstrinwsup_insert_data);
			}
			else if($isvoucher_autobillno==1){
			 $gstrinwsup_insert_data = array(
											"inwsup_pos"=>$pos,"inwsup_rev_chg"=>(int)$reverse_charges,
											"inwsup_cr_note_outsup_id"      =>(int)$cr_note_outsup_id,"inwsup_eco"=>(int)$outsup_eco,
											"gst_supply_type"=>(int)$cmp_supply_type,"gst_supply_type"=>(int)$cmp_supply_type
											);
			 $this->VouchersModel->update_gstrinwsup_data($voucher_txn_id,$gstrinwsup_insert_data);	
			}
			
		 	$voucher_invoice_value = $sale_total + $billsundry_total + $total_tax_amount;
		
			$voucher_fcy_invoice_value = $sale_fcy_total + $billsundry_fcy_total + $total_fcy_tax_amount;
            
			$voucher_memo_value = $sale_memo_total + $billsundry_memo_total;
			$voucher_fcy_memo_value = (parseAmount($fcy_forex_rate) >0)?($sale_memo_total + $billsundry_memo_total)*$fcy_forex_rate:0;
            
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
				$insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $party_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 2,
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
			 $memo_insert_data  = [
				  'cmp_id'            => $this->company_id,
				  'acc_id'            => $party_id,
				  'acc_txn_date'      => $voucher_date,
				  'acc_txn_dr_cr'     => 2,
				  'acc_txn_amt'       => $voucher_memo_value, 
				  'acc_txn_fcy'       => $voucher_fcy_memo_value,
				  'vch_txn_id'        => $voucher_txn_id,
				  'txn_id'            => $main_txn_id,
				  'hobo_id'           => $this->bo_id,
				  'acc_txn_type'      => 3
				];  				
				$this->VouchersModel->add_acc_txn_data($memo_insert_data);
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
						/******************* Save MEMO TXN ****************/
						$inv_supply_id   = $item_row['supply_type_id'];
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
								  'acc_txn_dr_cr'     => 1,
								  'acc_txn_amt'       => $item_row['memo_amount'], 
								  'acc_txn_fcy'       => (parseAmount($fcy_forex_rate) >0)?($item_row['memo_amount']*$fcy_forex_rate):0,
								  'vch_txn_id'        => $voucher_txn_id,
								  'txn_id'            => $txn_id,
								  'hobo_id'           => $this->bo_id,
								  'acc_txn_type'      => 3
								];  				
								$this->VouchersModel->add_acc_txn_data($memo_txn_data);
								SaveErrorLog("📌memo_amount  add_acc_txn_data".json_encode($memo_txn_data));
						}
						/******************* Save Account TXN ****************/
						$acc_txn_data  = [
							  'cmp_id'            => $this->company_id,
							  'acc_id'            => $item_row['account_id'],
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => 1,
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
                				  'acc_txn_dr_cr'     => 1
                				]; 								
                				$this->VouchersModel->add_taxinc_txn_data($taxinc_insert_data);
						    
						}
						/****************  End of Save Tax Inclusive Data **********************/
						
							
						
						/*******************Start of Save TAX SUMMARY ****************/

							$tax_cat_id       = $item_row['tax_cat_id'];
							$tax_detail_rates = $item_row['tax_details'];

							$amount     = parseAmount($item_row['amount']);
							$amount_fcy = parseAmount($item_row['amountfc']);

							$pos_code   = sprintf('%02d', $pos);

							$iteminfo = $this->VouchersModel->get_acc_details_info($item_row['account_id']);
							$item_hsn  = $iteminfo['acc_sac'] ?? '';

							// --------------------------------------------------
							// 🔥 GST DECISION FLAGS (APPLY ONCE)
							// --------------------------------------------------
							$apply_tax             = true;
							$use_composition_rates = false;

							// Case: Regular → Regular → NO TAX
							/* if ((int)$gstinType === 1 && (int)$sale_against_status === 1) {
								$apply_tax = false;
							} */

							// Case: Composition → Composition → COMPOSITION TAX
							if ((int)$gstinType === 2 && (int)$sale_against_status === 2) {
								$use_composition_rates = true;
							}

							// Case: Composition → Regular → NORMAL TAX (default)

							// --------------------------------------------------
							// Base rates
							// --------------------------------------------------
							$igst_rate = $item_row['igst_rate'] ?? 0;
							$cess_rate = $tax_detail_rates['cess'] ?? 0;

							// --------------------------------------------------
							// Composition override
							// --------------------------------------------------
							if ($use_composition_rates) {
								helper('composition');
								$compsrates = get_composition_rates($cmp_supply_type);
								$igst_rate  = $compsrates['igst'];
								$cess_rate  = $compsrates['cess'] ?? 0;
							}

							// --------------------------------------------------
							// DEFAULT TAX SUMMARY (INR)
							// --------------------------------------------------
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

							// --------------------------------------------------
							// 🔥 TAX CALCULATION (INR)
							// --------------------------------------------------
							SaveErrorLog('apply_tax='.$apply_tax.' && igst_rate > 0 == '.$igst_rate);
							if ($apply_tax && $igst_rate > 0) {

								if ($bo_state_code != $pos_code) {

									$igst_value = ($amount * $igst_rate) / 100;

									$taxsummary_data['vch_igst']      = $igst_value;
									$taxsummary_data['vch_igst_rate'] = $igst_rate;
									$taxsummary_data['vch_total_tax'] = $igst_value;

									$tax_igst_amt += $igst_value;

								} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$sgst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

									$tax_cgst_amt += $cgst_value;
									$tax_sgst_amt += $sgst_value;

								} elseif (in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$ugst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $ugst_value;

									$tax_cgst_amt  += $cgst_value;
									$tax_utgst_amt += $ugst_value;
								}

								if ($cess_rate > 0) {
									$cess_value = ($amount * $cess_rate) / 100;

									$taxsummary_data['vch_cess']      = $cess_value;
									$taxsummary_data['vch_cess_rate'] = $cess_rate;
									$taxsummary_data['vch_total_tax'] += $cess_value;

									$tax_cess_amt += $cess_value;
								}
							}

							// --------------------------------------------------
							// Remaining common fields
							// --------------------------------------------------
							$gst_tag = in_array($inv_supply_id, [1,2,3], true)
								? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : null))
								: ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$inv_supply_id] ?? 0);

							$taxsummary_data['vch_date']      = $voucher_date;
							$taxsummary_data['is_outward']    = 2;
							$taxsummary_data['gst_rate_grp']  = $gst_tag . ',' . parseAmount($igst_rate) . ',' . parseAmount($cess_rate);
							$taxsummary_data['vch_hsn_sac']   = $item_hsn;
							$taxsummary_data['inv_supply_id'] = $inv_supply_id;

							$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

							// --------------------------------------------------
							// HSN SUMMARY
							// --------------------------------------------------
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

							// --------------------------------------------------
							// 🔥 FCY TAX SUMMARY (SAME GST RULES)
							// --------------------------------------------------
							if (parseAmount($fcy_forex_rate) > 0) {

								$taxsummary_fcy_data = [
									'cmp_id'                    => $this->company_id,
									'vch_txn_id'                => $voucher_txn_id,
									'txn_id'                    => $txn_id,
									'acc_bsd_id'                => $item_row['account_id'],
									'acc_bsd_type'              => 1,
									'vch_igst_fcy'              => 0,
									'vch_cgst_fcy'              => 0,
									'vch_sgst_ugst_fcy'         => 0,
									'vch_cess_fcy'              => 0,
									'vch_taxable_value_fcy'     => $amount_fcy,
									'vch_total_tax_fcy'         => 0
								];

								if ($apply_tax && $igst_rate > 0) {

									if ($bo_state_code != $pos_code) {

										$igst_value = ($amount_fcy * $igst_rate) / 100;
										$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;

									} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$sgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;

									} elseif (in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$ugst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $ugst_value;
									}

									if ($cess_rate > 0) {
										$cess_value = ($amount_fcy * $cess_rate) / 100;
										$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
									}
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
							
						
						 /*******************Start of Save TAX SUMMARY (BILL SUNDRY) ****************/

							$tax_cat_id       = $item_row['tax_cat_id'];
							$tax_detail_rates = $item_row['tax_details'];

							$amount     = parseAmount($item_row['billsundry_amount']);
							$amount_fcy = parseAmount($item_row['billsundry_fcy_amount']);

							$pos_code = sprintf('%02d', $pos);

							$billsundry_info = $this->VouchersModel->get_bsd_details_info($item_row['billsundry_id']);
							$bsd_hsn         = $billsundry_info['bsd_hsn_sac'] ?? '';

							// --------------------------------------------------
							// 🔥 GST DECISION FLAGS
							// --------------------------------------------------
							$apply_tax             = true;
							$use_composition_rates = false;

							// Case: Regular → Regular → NO TAX
							if ((int)$gstinType === 1 && (int)$sale_against_status === 1) {
								$apply_tax = false;
							}
							// Case: Composition → Composition → COMPOSITION TAX
							elseif ((int)$gstinType === 2 && (int)$sale_against_status === 2) {
								$use_composition_rates = true;
							}
							// Case: Composition → Regular → NORMAL TAX (default)

							// --------------------------------------------------
							// Base rates
							// --------------------------------------------------
							$igst_rate = $item_row['igst_rate'] ?? 0;
							$cess_rate = $tax_detail_rates['cess'] ?? 0;

							// --------------------------------------------------
							// Composition override
							// --------------------------------------------------
							if ($use_composition_rates) {
								helper('composition');
								$compsrates = get_composition_rates($cmp_supply_type);
								$igst_rate  = $compsrates['igst'];
								$cess_rate  = $compsrates['cess'] ?? 0;
							}

							// --------------------------------------------------
							// DEFAULT TAX SUMMARY (INR)
							// --------------------------------------------------
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

							// --------------------------------------------------
							// 🔥 TAX CALCULATION (INR)
							// --------------------------------------------------
							if ($apply_tax && $igst_rate > 0) {

								if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {

									$igst_value = ($amount * $igst_rate) / 100;

									$taxsummary_data['vch_igst']      = $igst_value;
									$taxsummary_data['vch_igst_rate'] = $igst_rate;
									$taxsummary_data['vch_total_tax'] = $igst_value;

									$tax_igst_amt += $igst_value;

								} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$sgst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $sgst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $sgst_value;

									$tax_cgst_amt += $cgst_value;
									$tax_sgst_amt += $sgst_value;

								} elseif (in_array($pos_code, $ugst_states)) {

									$cgst_value = ($amount * ($igst_rate / 2)) / 100;
									$ugst_value = ($amount * ($igst_rate / 2)) / 100;

									$taxsummary_data['vch_cgst']           = $cgst_value;
									$taxsummary_data['vch_cgst_rate']      = $igst_rate / 2;
									$taxsummary_data['vch_sgst_ugst']      = $ugst_value;
									$taxsummary_data['vch_sgst_ugst_rate'] = $igst_rate / 2;
									$taxsummary_data['vch_total_tax']      = $cgst_value + $ugst_value;

									$tax_cgst_amt  += $cgst_value;
									$tax_utgst_amt += $ugst_value;
								}

								if ($cess_rate > 0) {
									$cess_value = ($amount * $cess_rate) / 100;

									$taxsummary_data['vch_cess']      = $cess_value;
									$taxsummary_data['vch_cess_rate'] = $cess_rate;
									$taxsummary_data['vch_total_tax'] += $cess_value;

									$tax_cess_amt += $cess_value;
								}
							}

							// --------------------------------------------------
							// COMMON FIELDS
							// --------------------------------------------------
							$taxsummary_data['vch_date']      = $voucher_date;
							$taxsummary_data['is_outward']    = 2;
							$taxsummary_data['gst_rate_grp']  = '1,' . parseAmount($igst_rate) . ',' . parseAmount($cess_rate);
							$taxsummary_data['vch_hsn_sac']   = $bsd_hsn;
							$taxsummary_data['inv_supply_id'] = 0;

							// SAVE SUMMARY
							$vch_gst_sum_id = $this->VouchersModel->add_taxsummary_data($taxsummary_data, $tax_required_flag);

							// --------------------------------------------------
							// HSN SUMMARY
							// --------------------------------------------------
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

							$this->VouchersModel->add_hsnsummary_data($hsnummary_data, $tax_required_flag);

							// --------------------------------------------------
							// 🔥 FCY TAX SUMMARY (SAME GST RULES)
							// --------------------------------------------------
							if (parseAmount($fcy_forex_rate) > 0) {

								$taxsummary_fcy_data = [
									'cmp_id'                => $this->company_id,
									'vch_txn_id'            => $voucher_txn_id,
									'txn_id'                => $txn_id,
									'acc_bsd_id'            => $item_row['billsundry_id'],
									'acc_bsd_type'          => 2,
									'vch_igst_fcy'          => 0,
									'vch_cgst_fcy'          => 0,
									'vch_sgst_ugst_fcy'     => 0,
									'vch_cess_fcy'          => 0,
									'vch_taxable_value_fcy' => $amount_fcy,
									'vch_total_tax_fcy'     => 0
								];

								if ($apply_tax && $igst_rate > 0) {

									if ($bo_state_code != $pos_code && !in_array($pos_code, $ugst_states)) {

										$igst_value = ($amount_fcy * $igst_rate) / 100;
										$taxsummary_fcy_data['vch_igst_fcy']      = $igst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $igst_value;

									} elseif ($bo_state_code == $pos_code && !in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$sgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $sgst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $sgst_value;

									} elseif (in_array($pos_code, $ugst_states)) {

										$cgst_value = ($amount_fcy * ($igst_rate / 2)) / 100;
										$ugst_value = ($amount_fcy * ($igst_rate / 2)) / 100;

										$taxsummary_fcy_data['vch_cgst_fcy']      = $cgst_value;
										$taxsummary_fcy_data['vch_sgst_ugst_fcy'] = $ugst_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] = $cgst_value + $ugst_value;
									}

									if ($cess_rate > 0) {
										$cess_value = ($amount_fcy * $cess_rate) / 100;
										$taxsummary_fcy_data['vch_cess_fcy']      = $cess_value;
										$taxsummary_fcy_data['vch_total_tax_fcy'] += $cess_value;
									}
								}

								$this->VouchersModel->add_taxsummary_fcy_data($taxsummary_fcy_data);
							}

							/*******************End of Save TAX SUMMARY (BILL SUNDRY) ****************/
					}
				}		 
			 /*********************End of Save Billsundry Wise Grid Entries **********************/
			 
			 /***************Start of  Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS ******/
			 // if gstinType is 2 means Composition then no record will insert in taxable AccountsModel
			 
			 SaveErrorLog('gstinType=='.$gstinType.' && sale_against_status =='.$sale_against_status.'& igst = '.$tax_igst_amt);
    			if($gstinType==1 && $sale_against_status==1){
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_igst_amt,1,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_sgst_amt,3,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_utgst_amt,4,$voucher_date,2);
    				   }	
    				 if($tax_cess_amt > 0)		
    				 $this->VouchersModel->save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_cess_amt,5,$voucher_date,2);
    		    } 
    			if ((int)$gstinType === 2 && $sale_against_status==2) {
    				 if($tax_igst_amt>0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_igst_amt,1,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_sgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_sgst_amt,3,$voucher_date,2);
    				   }
    				  else if($tax_cgst_amt>0 && $tax_utgst_amt >0){
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_cgst_amt,2,$voucher_date,2);
    				   $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_utgst_amt,4,$voucher_date,2);
    				   }		    
    				 if($tax_cess_amt >0)  
    				 $this->VouchersModel->save_taxacc_yes_out_data($comp_voucher_txn_id,$voucher_series_id,$tax_cess_amt,5,$voucher_date,2);
    		       }
               
               	if ((int)$gstinType === 2 && $sale_against_status==1) {
               	    // Tax Refund Accounts entry  will save later right now we are not creating Refund Accounts Entry
               	    
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
			2  => "New credit note invoice voucher has been updated by [USERNAME]([UUID])"
		];
		$notification_approval_messages = [
			2  => "credit note invoice voucher has been sent for your Approval of Rs.[VCHAMOUNT] by [USERNAME]([UUID])"
		];
		$notification_rejected_messages = [
			2  => "Payment voucher of Rs.[VCHAMOUNT] has been rejected by [USERNAME]([UUID])"			
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
	if($vch_subtype_id==0){  // Credit Note Without Items Data
		$data['account_transactions']   = $this->VouchersModel->grid_account_transactions($voucher_txn_id,$this->company_id);
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
	
	if($vch_subtype_id==1){  // Credit Note With Item Data
		$data['item_transactions']   = $this->VouchersModel->grid_item_transactions($voucher_txn_id);
		
	    $data['sub_type_dropdown']     = $this->sale_types;
		$data['item_batch_data']        = $this->VouchersModel->get_itembatch_txn_data($voucher_txn_id);
	}
	
	$data['against_dropdown']      = $this->VouchersModel->against_voucher_dropdown(18,array(0,8,9,10),date('Y-m-d'));
	$data['voucher_info']          = $voucher_info;
	$data['draft_vch_rec_id']      = $voucher_info['draft_vch_rec_id'];
	$data['sale_against_challan']  = [];
	$data['transporter_dropdown']  = [];
	$data['ugst_states']           = $this->ugst_states;
	$data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();	
	$data['transport_modes']       = [];
	$data['vehicle_types']         = [];	
	$data['matrcntr_dropdown']     = $this->VouchersModel->material_centre_dropdown();
	$data['units_list']            = $this->VouchersModel->units_dropdown();
	$data['bo_state_code']         = $this->session->get('ses_bostecd');
	$data['SuppltSubSupplyTypes']  = json_encode($this->VouchersModel->SuppltSubSupplyTypes());	
	$data['voucher_txn_id']         =  $voucher_txn_id;
	$data['istaxinc']               =  $istaxinc;
	$data['ismemosale']             =  $ismemosale;
	$data['vch_series_id']          =  $voucher_info['vch_series_id'];
	$data['gstroutsup_info']        =  $get_gstroutsup_info;
	$data['narration']              =  $get_narration;	
	$data['voucher_type_id']        =  $voucher_type_id;
	$data['currency_id']            =  $voucher_info['currency_id'];
	$data['forexcrncy_rate']        =  $voucher_info['forexcrncy_rate'];
	$data['SuppltSubSupplyTypes']   =  json_encode($this->VouchersModel->SuppltSubSupplyTypes());
	$data['taxes_list']             = $taxes_list;	
	$data['base_url']               = $this->base_url; 
	$data['folder_path']            = $this->folder_path;
	$data['voucher_date']           = date('d-m-Y', strtotime($voucher_info['vch_date']));
	$data['bo_gstin_type']          = $gstinType;// regular
	$data['voucher_auto_no']        = $this->VouchersModel->get_voucher_no($voucher_type_id);
	$data['voucher_bill_no']        = $get_gstroutsup_info['inwsup_bill_ref_no'] ?? '';
	$data['voucher_series']         = $this->VouchersModel->comp_voucher_result($this->company_id,$voucher_type_id);
	$data['party_dropdown']         = $this->VouchersModel->party_dropdown();	
	$data['Voucher_TxnApproval_UUID'] = $Voucher_TxnApproval_UUID ?? 0;
	$data['current_uuid']             = $this->session->get('uuid');	
	$data['is_voucher_editable']      = $is_voucher_editable;
	$data['vchfcyrate_info']          = $this->VouchersModel->vchfcyrate_info($voucher_txn_id,$this->fy_id);
	$data['bsd_transactions']         = $this->VouchersModel->grid_bsd_transactions($voucher_txn_id);
	$data['bbb_data']                 = $this->VouchersModel->get_bills_txn_data($voucher_txn_id);
    $data['pr_data']                  = $this->VouchersModel->get_pr_txn_data($voucher_txn_id);
	$data['bills_method_list']     = ['','New Ref.','Adjustment'];
	$accounts_list                 = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
	$filter_bsd_accounts           = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
	$data['accounts_json_file']    = $accounts_list;
	$data['bsd_json_file']         = json_encode($filter_bsd_accounts);		

    $items_list                    = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
	$data['item_json_file']        = $items_list;
	
	
	$data['bo_state_code']         = $bo_state_code;
	$data['currency_list']         = $this->VouchersModel->get_currency_list();
	$data['states_lists']          = $this->VouchersModel->show_states_lists();
	$data['eco_dropdown']          = $this->VouchersModel->show_eco_lists();
    $data['supply_types_dropdown'] = $this->VouchersModel->supply_types_list();	
	$data['OverrideSupplyTypeId']    = $this->VouchersModel->getVoucherOverrideSupplyTypeId($voucher_txn_id);
	
	if($vch_subtype_id==0){
	  return view($this->folder_path.'credit_note/edit_non_item',$data);	  
	}	
	if($vch_subtype_id==1){
	  return view($this->folder_path.'credit_note/edit_item_based',$data);	  
	}
  }

    
	public function delete_creditnote(){
		$voucher_type_id    = 2;
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
				// $this->VouchersModel->markSnapshotDirty($voucher_date);
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
		   $this->VouchersModel->clear_system_journal_bridge_data($voucher_txn_id,1);
		   $this->VouchersModel->clear_system_journal_bridge_data($voucher_txn_id,5);
			 
		   return $this->response->setJSON(['status' => true, 'message' => 'Voucher Deleted']);
		}
		catch (\Throwable $e){
		  log_message('error', 'DB Query Error: ' . $e->getMessage());
		  return $this->response->setJSON(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
	     }	   
	   }
	}
	
	
}