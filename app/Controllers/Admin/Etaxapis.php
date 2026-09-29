<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\GstexportModel;
use App\Models\Admin\VouchersModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\externaldb;
class Etaxapis extends BaseController{
  function __construct(){  
    helper(['form', 'url','text','einvoice']);
	$this->externaldb        = new externaldb();
	$this->VouchersModel     = new VouchersModel();
    $this->CommonModel       = new CommonModel();		
	$this->GstexportModel    = new GstexportModel();
    $this->auth_session      = new auth_session();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
    $this->auth_session->role_restrict('CS');
    $this->base_url       =  base_url().'/'.getenv('AdminPath');
    $this->folder_path    =  getenv('AdminPath');
    $this->session    	  =  \Config\Services::session();
    $this->company_id     = $this->session->get('ses_company_id');
	$this->fy_id          = $this->session->get('ses_comp_fy_id');
    $this->bo_id          = $this->session->get('ses_boid');
	$this->profile_id     = $this->session->get('ses_cmp_prf_id');
	$this->isSandbox      =  env('EWAY_SANDBOX'); // 0 means production, 1 means sandbox
	$this->aicountly_db   =  $this->externaldb->aicountly_db();
	$this->contactaic_db   = $this->externaldb->contactaic_db();
    $this->ses_comp_fy_id =  $this->session->get('ses_comp_fy_id');
	$this->EwaySubscriptionKey     ='AL2h3J2u9M2c1e6u9c';
	$this->EinvoiceSubscriptionKey ='AL2h3J2u9M2c1e6u9c';
	$this->GstrOneSubscriptionKey  =($this->isSandbox=='1')?'ALSND3C9L9q7c4a3W1P8':'ALPRD7l6b5R1i1E9m8u0';
	
	$this->app_key             =  generateRandomStringVal();
	$this->gstr_app_key        =  gstr_random_appkey();    	
	$this->GSTRONEclientsecret = ($this->isSandbox=='1')?env('GSTRONE_SANDBOX_CLIENTSECRET'):env('GSTRONE_LIVE_CLIENTSECRET');
	$this->GSTRONEclientid     = ($this->isSandbox=='1')?env('GSTRONE_SANDBOX_CLIENTID'):env('GSTRONE_LIVE_CLIENTID');
	$this->table_lists         =  array("4A"=>"B2B REGULAR",
								   "4B"=>"B2B REVERSE CHARGE",
								   "5"=>"B2CL (LARGE)", 
								   "6A"=>"EXPORTS",
								   "6B"=>"SUPPLIES MADE TO SEZ UNIT",
								   "6C"=>"DEEMED EXPORT (DE)",
								   "7"=>"B2CS",
								   "8"=>"NIL RATED, EXEMPTED AND NON GST",
								   "9A,9C"=>"AMENDED INVOICES",
								   "9B"=>"CREDIT / DEBIT NOTES",
								   "11A(1),11A(2)"=>"TAX LIABILITY (ADVANCES RECEIVED)",
								   "11B(1),11B(2)"=>"ADJUSTMENTS OF ADVANCES",
								   "12"=>"HSN WISE SUMMARY OF OUTWARD SUPPLIES",
								   "13"=>"DOCUMENT ISSUED" ,
								   "14"=>"SUPPLIES MADE THROUGH ECO",
								   "15"=>"SUPPLIES U/S 9(5)");
	$this->table_lists_detailed = array("4A"=>"B2B REGULAR",
								   "4B"=>"B2B REVERSE CHARGE",
								   "5"=>"B2CL (LARGE)",
								   "6A"=>"EXPORTS",
								   "6A_EXPWP"=>"EXPWP",
								   "6A_EXPWOP"=>"EXPWOP",		
								   "6B"=>"SUPPLIES MADE TO SEZ UNIT",	
								   "6B_SEZWP"=>"SEZWP",
								   "6B_SEZWOP"=>"SEZWOP",
								   "6C"=>"DEEMED EXPORT (DE)",
								   "7"=>"B2CS",
								   "8"=>"NIL RATED, EXEMPTED AND NON GST",
								   "8_Nil"=>"NIL RATED",
								   "8_Exempted"=>"EXEMPTED",
								   "8_Non-GST"=>"NON GST",							   
								   "9A,9C"=>"AMENDED INVOICES",
								   "9B"=>"CREDIT / DEBIT NOTES REGISTERED", 
								   "9B_Registered"=>"REGISTERED",
								   "9B_Unregistered"=>"UNREGISTERED",
								   "11A(1),11A(2)"=>"TAX LIABILITY (ADVANCES RECEIVED)",
								   "11B(1),11B(2)"=>"ADJUSTMENTS OF ADVANCES",
								   "12"=>"HSN WISE SUMMARY OF OUTWARD SUPPLIES",
								   "13"=>"DOCUMENT ISSUED" ,
								   "13_candoc"=>"CANCELLED DOCS" ,
								   "13_netissued"=>"NET ISSUED DOCS" ,
								   "14"=>"SUPPLIES MADE THROUGH ECO",
								   "15"=>"SUPPLIES U/S 9(5)");
  }
  
  public function generate_eway(){
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid                 = $this->session->get('ses_boid');
	$branch_gstin_info    = $this->VouchersModel->get_branch_gstin_info($boid);	
	$credntial_gstin_info = $this->VouchersModel->get_credntial_gstin_info(4);
	if($this->isSandbox==1){
	$this->Gstin='07AGAPA5363L002';
	$this->username='AL001';
	$this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){	
	 $this->Gstin  = $branch_gstin_info['comp_gstin']; 
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username ='';
		$this->password =''; 
	  }
	}
	if($this->request->getMethod() == 'POST'){
		$AppKey = $this->app_key;
		$postdata = array("action"=>"ACCESSTOKEN","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->EwaySubscriptionKey,
		                  "app_key"=>$AppKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox);
		$accesstoken_info = json_decode(get_eway_accesstoken_string($postdata),true);
		SaveErrorLog(json_encode($accesstoken_info));
		if(isset($accesstoken_info) && $accesstoken_info['status']=="1"){
			$authtoken      = $accesstoken_info['authtoken'];
			$EncryptedSek   = $accesstoken_info['sek'];
			$options        = 0;
			$encryption     = $EncryptedSek;
			$ciphering      = "AES-256-ECB";
			$decryption_key =  base64_decode($AppKey);
			$decryption_iv  = ''; 
			$decryption     = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek   = base64_encode($decryption);
			 $RequestPayload='{
	"supplyType": "O",
	"subSupplyType": "1",
	"subSupplyDesc": "",
	"docType": "INV",
	"docNo": "5526122024-3762",
	"docDate": "19/12/2025",
	"fromGstin": "07AGAPA5363L002",
	"fromTrdName": "welton",
	"fromAddr1": "2ND CROSS NO 59  19  A",
	"fromAddr2": "GROUND FLOOR OSBORNE ROAD",
	"fromPlace": "FRAZER TOWN",
	"fromPincode": 110055,
	"actFromStateCode": 07,
	"fromStateCode": 07,
	"toGstin": "02AAJFH7376P1ZS",
	"toTrdName": "sthuthya",
	"toAddr1": "Shree Nilaya",
	"toAddr2": "Dasarahosahalli",
	"toPlace": "Beml Nagar",
	"toPincode": 560090,
	"actToStateCode": 29,
	"toStateCode": 27,
	"transactionType": 4,
	"otherValue": "0",
	"totalValue": 500,
	"cgstValue": 0,
	"sgstValue": 0,
	"igstValue": 15,
	"cessValue": 15,
	"cessNonAdvolValue": 0,
	"totInvValue": 530,
	"transporterId": "",
	"transporterName": "",
	"transDocNo": "",
	"transMode": "1",
	"transDistance": "2145",
	"transDocDate": "",
	"vehicleNo": "PVC1234",
	"vehicleType": "R",
	"itemList": [{
		"productName": "Wheat",
		"productDesc": "Wheat",
		"hsnCode": 1001,
		"quantity": 4,
		"qtyUnit": "BOX",
		"cgstRate": 0,
		"sgstRate": 0,
		"igstRate": 3,
		"cessRate": 3,
		"cessNonadvol": 0,
		"taxableAmount": 500
	}]
}'; 	
        $response_data    = array();  
		$errors_counter   = 0;
		$bo_state_code    = $this->session->get('ses_bostecd');  
		$vch_txn_id       = $this->request->getVar('vch_txn_id');		 
		$response         = json_decode($this->GstexportModel->get_eway_voucher_info($vch_txn_id),true);
		
		
		if($response['data']){
			foreach($response['data'] as $row){
				$voucher_txn_id   = $row['voucher_txn_id'];
				$voucher_type_id  = $row['voucher_type_id']; 
                $voucher_info     = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);		
				$party_info       = $this->VouchersModel->get_comp_txn_party_data($voucher_txn_id);
				$party_id         = $party_info['master_id'];		
				$party_gst_info   = $this->VouchersModel->get_acc_details_info($party_id);
				$item_trnsctions  = $this->VouchersModel->grid_item_transactions($voucher_txn_id);
				$sundry_transactions = $this->VouchersModel->grid_bsd_transactions($voucher_txn_id);
				$voucher_series   = $voucher_info['vch_series_id'];
				$voucher_type 	  = $voucher_info['vch_type_id'];
				$mc_centr_id 	  = $voucher_info['mat_cent_id'];
				
				$voucher_date 	  = date('d/m/Y', strtotime($voucher_info['vch_date']));
				$ewbmstreqn_data  = $this->VouchersModel->get_ewbmstreqn_data($voucher_txn_id);
				//$billfrm_data     = $this->VouchersModel->billfrm_info();
				$bo_gstin_type    = $this->session->get('bo_gstin_type');
				$bo_id            = $this->session->get('ses_boid');	  
				//$get_party_info   = $this->VouchersModel->get_party_info($voucher_txn_id);
				$get_account_info  = $this->VouchersModel->account_full_info($party_info['master_id']);
				$party_adrs_info   = $get_account_info['address_info'];
				
				$party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
				
				if ($party_adrs_info) {
				$party_phone = '';
				if (!empty($party_adrs_info['contact_mobile'])) {
					$party_phone = $party_adrs_info['contact_mobile'];
				} elseif (!empty($party_adrs_info['contact_mobile2'])) {
					$party_phone = $party_adrs_info['contact_mobile2'];
				}

				$party_email = '';
				if (!empty($party_adrs_info['contact_email'])) {
					$party_email = $party_adrs_info['contact_email'];
				} elseif (!empty($party_adrs_info['contact_email2'])) {
					$party_email = $party_adrs_info['contact_email2'];
				}

				$party_phone = $party_phone;
				$party_name  = $get_account_info['acc_name'];
				$party_email = $party_email;
			}

			if ($party_adrs_info) {
				$party_add1  = $party_adrs_info['contact_add1'];
				$party_add2  = $party_adrs_info['contact_add2'];
				$party_city  = $party_adrs_info['contact_city'];
				$party_pin   = $party_adrs_info['contact_pin'];
				$party_state = $party_adrs_info['contact_state'];
				$party_country = $party_adrs_info['contact_country'];

				$party_state_info = $this->CommonModel->get_state_info($party_country, $party_state);
				$party_state_code = $party_state_info ? sprintf('%02d', $party_state_info['state_code']) : 0;
			} else {
				$party_state_code = 0;
			}
		  $comp_ro_address    = $this->CommonModel->get_company_address_info($this->company_id, '1');
          $gstroutsup_info    = $this->VouchersModel->get_gstroutsup_info($voucher_txn_id);
		  echo '<pre>';
		  print_r($gstroutsup_info);
		  die();
		  
		  $comp_state_info    = $this->CommonModel->get_state_info($comp_ro_address['cmp_country'], $comp_ro_address['cmp_state']);
          $comp_state_code    = $comp_state_info ? sprintf('%02d', $comp_state_info['state_code']) : 0;
		  $comp_gst           = $branch_gstin_info['hobo_gstin'] ?? '';
		  $comp_legal_name    = $branch_gstin_info['hobo_gstin_legal_name'] ?? '';
		  $comp_trade_name    = $branch_gstin_info['hobo_gstin_trade_name'] ?? '';
	      if ($party_gst_info) {
            $party_gst        = ($party_gst_info['acc_gstin'] != '') ? $party_gst_info['acc_gstin'] : 'URP';
            $party_legal_name = $get_account_info['acc_name'] ?? 'N/A';
            $party_trade_name = $get_account_info['acc_name'] ?? 'N/A';
        }
		// EWB details (simplified; retain original choices)
        $ewb_req     = $this->VouchersModel->get_ewbmstreqn_data($voucher_txn_id);
        $ewb_supply_type = GetSupplyTypeCode($ewb_req['ewb_supply_type']);
		
        if ($gstroutsup_info) {
            $outsup_pos         = $gstroutsup_info['outsup_pos'];
            $outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
            $outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];
            $outsup_inv_type    = $ewb_supply_type;
            $outsup_eco         = $gstroutsup_info['outsup_eco'];
            if ($outsup_eco > 0) {
                $EcmGstin_info = $this->VouchersModel->party_gst_info($party_id);
                $EcmGstin      = $EcmGstin_info ? (($EcmGstin_info['acc_gstin'] != '') ? $EcmGstin_info['acc_gstin'] : null) : null;
            }
            $pos_state_info = $this->VouchersModel->pos_state_info(ltrim($outsup_pos, 0));
            if ($pos_state_info) {
                $statecode = sprintf('%02d', $pos_state_info['state_code']);
            }
            $outsup_rev_chg_label = ($outsup_rev_chg == 1) ? 'Y' : 'N';
        }
		if($bo_state_code==$statecode)
		  $IgstOnIntra ='N';
		else
		  $IgstOnIntra ='Y';
		// doc type by voucher_type
        $voucher_type = $voucher_info['vch_type_id'];
		 $vch_subtype_id = $voucher_info['vch_sub_type_id'];
		
        if ($voucher_type == 18 || $voucher_type == 11) $docType = 'INV';
        elseif ($voucher_type == 2) $docType = 'CRN';
        elseif ($voucher_type == 3) $docType = 'DBN';
        else $docType = 'INV';

        
        $shiptoAddr1 = $shiptoAddr2 = $shiptoPlace = $shiptoPincode = $shiptoStateCode = $actshiptoStateCode = '';
        $ewb_txn_type = 1;
        if ($ewb_req) {
            $ewb_txn_type = $ewb_req['ewb_sub_supply_type'];
            if ($vch_subtype_id > 0 && ($ewb_txn_type == 1 || $ewb_txn_type == 2)) {
                $mc_info = $this->VouchersModel->mc_info($voucher_info['mat_cent_id']);
                if ($mc_info) {
                    $fromAddr1        = $mc_info['mat_cent_addr1']?? 'N/A';
                    $fromAddr2        = $mc_info['mat_cent_addr2']?? 'N/A';
                    $fromPlace        = $mc_info['mat_cent_city']?? 'N/A';
                    $fromPincode      = $mc_info['mat_cent_pin_zip']?? 'N/A';
                    $actFromStateCode = $mc_info['state_code']?? 'N/A';
                    $fromStateCode    = $mc_info['state_code']?? 'N/A';
                }
            }
            if ($vch_subtype_id > 0 && ($ewb_txn_type == 3 || $ewb_txn_type == 4)) {
                $disp = $ewb_req['gstdispfrm_info'];
                if ($disp) {
                    $fromAddr1        = $disp['dispfrm_addr1'] ?? 'N/A';
                    $fromAddr2        = $disp['dispfrm_addr2']?? 'N/A';
                    $fromPlace        = $disp['dispfrm_place']?? 'N/A';
                    $fromPincode      = $disp['dispfrm_pin']?? 'N/A';
                    $actFromStateCode = sprintf('%02d', $disp['dispfrm_state_code'])?? 'N/A';
                    $fromStateCode    = sprintf('%02d', $disp['dispfrm_state_code'])?? 'N/A';
                }
                $ship = $ewb_req['gstshipton_info'];
                if ($ship) {
                    $shiptoAddr1        = $ship['shipto_addr1']?? 'N/A';
                    $shiptoAddr2        = $ship['shipto_addr2']?? 'N/A';
                    $shiptoPlace        = $ship['shipto_place']?? 'N/A';
                    $shiptoPincode      = $ship['shipto_pin']?? 'N/A';
                    $actshiptoStateCode = sprintf('%02d', $ship['shipto_state_code'])?? 'N/A';
                    $shiptoStateCode    = sprintf('%02d', $ship['shipto_state_code'])?? 'N/A';
                }
            } elseif ($vch_subtype_id == 0) { // non-item use party address
                $fromAddr1        = $party_add1?? 'N/A';
                $fromAddr2        = $party_add2?? 'N/A';
                $fromPlace        = $party_city?? 'N/A';
                $fromPincode      = $party_pin?? 'N/A';
                $actFromStateCode = $party_state_code?? 'N/A';
                $fromStateCode    = $party_state_code?? 'N/A';
            }
        }
	   else{
		   // fetch address from HO 
			$headoffice_info = 	$this->VouchersModel->getHeadOfficeWithAddress();			
			$fromAddr1        = $headoffice_info['hobo_addr1'] ?? 'N/A';
			$fromAddr2        = $headoffice_info['hobo_addr2'] ?? 'N/A';
			$fromPlace        = $headoffice_info['hobo_city'] ?? 'N/A';
			$fromPincode      = $headoffice_info['hobo_pin_zip'] ?? 'N/A';
			$actFromStateCode = $headoffice_info['state_code'] ?? 'N/A';
			$fromStateCode    = $headoffice_info['state_code'] ?? 'N/A';
	     }
		  $transactionType            = 4;	
		  
	   	   $company_info      = $this->CommonModel->get_company_details_info($this->company_id);	  
		   $comp_email        = $company_info['cmp_email'];
		   $comp_name         = $company_info['cmp_name'];
		   $comp_phone        = $company_info['cmp_mobile'];
		   $comp_state_info   = $this->CommonModel->get_state_info($comp_ro_address['cmp_country'],$comp_ro_address['cmp_state']);
		   $comp_country_info = $this->CommonModel->get_country_info($comp_ro_address['cmp_country']);
		  if($comp_state_info)
			$comp_state_code  = sprintf( '%02d', $comp_state_info['state_code']);
		  else
			$comp_state_code  = 0;  
		   
		  if(isset($comp_ro_address['cmp_addr1']))
			$company_adrs1    = $comp_ro_address['cmp_addr1'];
		  else
			$company_adrs1    = '';  
		
		  if(isset($comp_ro_address['cmp_addr2']))
		   $company_adrs2     = $comp_ro_address['cmp_addr2'];
		  else
			$company_adrs2    = ''; 
		  if(isset($comp_ro_address['cmp_pin_zip']))
		   $company_pin       = $comp_ro_address['cmp_pin_zip'];
		  else
		   $company_pin       = ''; 
		 
		  if(isset($comp_ro_address['cmp_city']))
		   $company_city      = $comp_ro_address['cmp_city'];
		  else
		   $company_city      = '';
	   
		  if(isset($comp_country_info['countryname']))
		   $company_country   = $comp_country_info['countryname'];
		  else
		   $company_country   = '';
	   
		  if(isset($comp_country_info['country_code']))
		   $company_countrycd = $comp_country_info['country_code'];
		  else
		   $company_countrycd = '';
		   
		   
		   // Tax Totals
		$totals         = $this->VouchersModel->computeGstTotals($voucher_txn_id, $bo_state_code, $statecode, $sundry_transactions, false);
		$totInvValue    = $totals['totInvValue'];
		$TotInvValFc    = $totals['TotInvValFc'];
		$cgstValue      = $totals['cgstValue'];
		$sgstValue      = $totals['sgstValue'];
		$igstValue      = $totals['igstValue'];
		$cessValue      = $totals['cessValue'];
		$cessNonAdvolValue= 0;
		$bsdTotal         = 0;
			
			$itemList=array();
			if($item_trnsctions){
			  foreach($item_trnsctions as $itm){
				$tax      = $itm['tax_details'] ?? [];
				$cess     = parseAmount($tax['cess']  ?? 0);
				if($bo_state_code==$statecode){
					$igst    = 0;
					$cgst    = parseAmount($tax['cgst']  ?? 0);
					$sgst    = parseAmount($tax['sgst']  ?? ($tax['ut_tax'] ?? 0)); // UT tax if present
					
				}else{
					$igst    = parseAmount($tax['igst']  ?? 0);
					$cgst    = 0;
					$sgst    = 0;
					}	
				  $tax_amt = $igst + $cgst + $sgst + $cess;
				  $itemList[]=array("productName"=>$itm["item_name"],"productDesc"=>$itm["item_name"],"hsnCode"=>$itm["item_hsn"],"quantity"=>parseAmount($itm["item_qty"]),
								  "qtyUnit"=>$itm["item_unit_name"],"cgstRate"=>parseAmount($cgst),"sgstRate"=>parseAmount($sgst),"igstRate"=>parseAmount($igst),"cessRate"=>parseAmount($cess),
								  "cessNonadvol"=>parseAmount("0"),"taxableAmount"=>parseAmount($tax_amt)
								  );	
				 }
			}
			
			if($sundry_transactions){
			  foreach($sundry_transactions as $bsdrow){
				$bsdTotal=$bsdTotal+$bsdrow['billsundry_amount'];  
			  }
			}
			
			$transporter_info = $this->VouchersModel->transporter_info($voucher_txn_id);	
			if($transporter_info){
				$transporterId    = $transporter_info['tpt_gstin'];
				$transporterName  = 'dummy name';
				$transDocNo       = $transporter_info['trans_doc_no'];
				$transMode        = $transporter_info['trans_mode'];
				$transDistance    = $transporter_info['trans_dist'];
				$transDocDate     = $transporter_info['trans_doc_date'];
				$vehicleNo        = $transporter_info['trans_veh_no'];
				$vehicleType      = $transporter_info['trans_veh_type'];
			}
			else{
				$transporterId=$transporterName=$transDocNo=$transMode=$transDistance=$transDocDate=$vehicleNo=$vehicleType='';	
			}
		if($voucher_type==18 || $voucher_type==11){
			$docType ='INV';
		}
		else if($voucher_type==2){
			$docType ='CRN';
		}
		else if($voucher_type==3){
			$docType ='DBN';
		}else
		  $docType ='INV';	
	  
	  
	        $response_data = array("supplyType"=>"O","subSupplyType"=>"1","subSupplyDesc"=>"Transaction",
        	     				   "docType"=>$docType,"docNo"=>$outsup_bill_ref_no,"docDate"=>$voucher_date,
        						   "fromGstin"=>$comp_gst,"fromTrdName"=>$comp_trade_name,"fromAddr1"=>$fromAddr1,
        						   "fromAddr2"=>$fromAddr2,"fromPlace"=>$fromPlace,"fromPincode"=>(integer)$fromPincode,
        						   "actFromStateCode"=>$actFromStateCode,"fromStateCode"=>$fromStateCode,
        						   "toGstin"=>$party_gst,"toTrdName"=>$party_trade_name,"toAddr1"=>$party_add1,"toAddr1"=>$party_add2,
        						   "toPlace"=>$party_city,"toPincode"=>(integer)$party_pin,"actToStateCode"=>$party_state_code,"toStateCode"=>$party_state_code,
        						   "transactionType"=>$transactionType,"otherValue"=>"","totalValue"=>parseAmount($totInvValue+$bsdTotal),
        						   "cgstValue"=>parseAmount($cgstValue),"sgstValue"=>parseAmount($sgstValue),"igstValue"=>parseAmount($igstValue),"cessValue"=>parseAmount($cessValue),
        						   "cessNonAdvolValue"=>parseAmount($cessNonAdvolValue),"totInvValue"=>parseAmount($totInvValue+$bsdTotal),"transporterId"=>$transporterId,"transporterName"=>$transporterName,
        						   "transDocNo"=>$transDocNo,"transMode"=>$transMode,"transDistance"=>(integer)$transDistance,"transDocDate"=>$transDocDate,
        						   "vehicleNo"=>$vehicleNo,"vehicleType"=>$vehicleType,"itemList"=>$itemList
                          ); 
	 			
				
				if($transporterId==''){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Transport Document Number is required.";
				}
				if(strtotime($voucher_info['vch_date']) > strtotime(date('Y-m-d'))){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - The Document Date should be less than or equal to current date.";
				}
				if($transporterName==''){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Transport name is required.";
				}
				if($transDistance==''){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Transport distance is required.";
				}
				if($transDistance >4000){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Transport distance should be less than 4000 km.";
				}	
				if($transMode=='1' && ($vehicleNo=='' || $vehicleType=='')){ // if Road
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Vehicle No /VehicleType  is required if mode of transportation is Road.";
				}
				if(($transMode=='2' || $transMode=='3' || $transMode=='4' ) && ($transporterId=='' || $transDocDate=='')){ // if Ship, Air, Rail,
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Transport document number and date should be passed if mode of transportation is Ship, Air, Rail.";
				}	
				if(($transMode=='2' || $transMode=='3' || $transMode=='4' ) && (strtotime($transDocDate)>=strtotime($voucher_date) )){ // if Ship, Air, Rail,
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - The Transport document Date should be greater than equal to document date";
				}
				if($transMode=='4'  && $vehicleType!='O'){ // in Ship if not ODBC,
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Vehicle type should be ODC. if transport mode is Ship";
				 }		
			  }			
		   }		
		 
		   if($errors_counter>0){
		  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		}
		else{			
		    $RequestPayload_act =  json_encode($response_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);		
		  	$Base64RequestPayload = base64_encode(  $RequestPayload_act  );
			$reqpayload = encryptBySymmetricKey($Base64RequestPayload, $DecryptedSek);
		    SaveErrorLog($RequestPayload_act);
		    //Generate E-way bill			
			$postdata       = array("action"=>"GENEWAYBILL","SubscriptionKey"=>$this->EwaySubscriptionKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox);
			$bill_response  = json_decode(generate_eway_bill($reqpayload,$authtoken,$postdata),true);
			
			if($bill_response['status']=="1"){
				if(isset($bill_response['alert']) && $bill_response['alert']!=''){	
				 $alerts = array($bill_response['alert']);
				return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}else{					
					$response     = decryptBySymmetricKey($bill_response['data'],$DecryptedSek);
					$successData  = json_decode($response,true);		
					$this->VouchersModel->update_eway_data($voucher_txn_id,$successData);
					return json_encode(['ewayBillDate'=>$successData['ewayBillDate'],'ewayBillNo'=>$successData['ewayBillNo'],'status' => true,'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload_act,'errors'=>'','filename'=>'eway-invoice.json']);
				}
			}
			else{
			 if(isset($bill_response['alert']))	
				 $alerts = array($bill_response['alert']);
			  elseif(isset($bill_response['error'])){	
			     $error_info  = json_decode(base64_decode($bill_response['error']),true);
				 $errorCodes  = explode(",",rtrim($error_info['errorCodes'],","));
				 $errors_list = array();
				 if($errorCodes){
					 foreach($errorCodes as $errorCode){
						 if(Eway_errors_codes($errorCode))
						 $errors_list[] = Eway_errors_codes($errorCode).'('.$errorCode.')';
					 }					 
				  }							
				  if($errors_list)
					  $alerts = $errors_list;
				   else
				    $alerts = array('error in request');
			  }				 
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
			}	
			
		    }	
		}
		else{
			     if(isset($accesstoken_info)){
					 $error_info  = json_decode(base64_decode($accesstoken_info['error']),true);
					 $errorCodes  = explode(",",rtrim($error_info['errorCodes'],","));
					 $errors_list = array();
					 if($errorCodes){
						 foreach($errorCodes as $errorCode){
							 if(Eway_errors_codes($errorCode))
							 $errors_list[] = Eway_errors_codes($errorCode).'('.$errorCode.')';
						 }					 
					  }							
					  if($errors_list)
						  $alerts = $errors_list;
					   else
						$alerts = array('error in request'); 
				 }
				 else{
					$alerts = array('error in request'); 
				 }
				  
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
		 }	    
      }
  }
  
  
  public function get_eway_detail(){
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($boid);
	
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(4);
	if($this->isSandbox==1){
	$this->Gstin='07AGAPA5363L002';
	$this->username='AL001';
	$this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){	
	 $this->Gstin  = $branch_gstin_info['comp_gstin'];
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username ='';
		$this->password =''; 
	  }
	}
	if($this->request->getMethod() == 'post'){
		$AppKey = $this->app_key;
		$postdata = array("action"=>"ACCESSTOKEN","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->EwaySubscriptionKey,
		                  "app_key"=>$AppKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox);
						  
		//echo json_encode($postdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
		
		$accesstoken_info = json_decode(get_eway_accesstoken_string($postdata),true);
		//echo 'accesstoken_info
		//';
		//echo json_encode($accesstoken_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		//SaveErrorLog(json_encode($accesstoken_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		if(isset($accesstoken_info) && $accesstoken_info['status']=="1"){
			$authtoken      = $accesstoken_info['authtoken'];
			$EncryptedSek   = $accesstoken_info['sek'];
			$options        = 0;
			$encryption     = $EncryptedSek;
			$ciphering      = "AES-256-ECB";
			$decryption_key =  base64_decode($AppKey);
			$decryption_iv  = ''; 
			$decryption     = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek   = base64_encode($decryption);
			
			$response_data    = array();  
			$bo_state_code    = $this->session->get('ses_bostecd');  
			$vch_txn_id       = $this->request->getVar('vch_txn_id');		 
			$ewbno            = $this->request->getVar('table_id');
			$ewb_id           = $this->request->getVar('tablelkey');		 
			
		    //Get E-way bill			
			$postdata       = array("SubscriptionKey"=>$this->EwaySubscriptionKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,'ewbno'=>$ewbno);
			$bill_response  = json_decode(GetEwayBill($authtoken,$postdata),true);
			if($bill_response['status']=="1"){
				if(isset($bill_response['alert']) && $bill_response['alert']!=''){	
				 $alerts = array($bill_response['alert']);
				return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}else{					   	
					$EncryptedRek   = $bill_response['rek'];
					$options        = 0;
					$encryption     = $EncryptedRek;
					$ciphering      = "AES-256-ECB";
					$decryption_key =  base64_decode($DecryptedSek);
					$decryption_iv  = ''; 
					$decryption     = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
					$DecryptedRek   = base64_encode($decryption);
			
				    
					$options        = 0;
					$encryption     = $bill_response['data'];
					$ciphering      = "AES-256-ECB";
					$decryption_key =  base64_decode($DecryptedRek);
					$decryption_iv  = '';		
					$successData    = json_decode(openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv),true);
					if($successData)
					$this->TransactionModel->update_ewbmastern_info($ewb_id,$successData);
					return json_encode(['status' => true]);
				}
			}
			else{
			 if(isset($bill_response['alert']))	
				 $alerts = array($bill_response['alert']);
			  elseif(isset($bill_response['error'])){	
			     $error_info  = json_decode(base64_decode($bill_response['error']),true);
				 $errorCodes  = explode(",",rtrim($error_info['errorCodes'],","));
				 $errors_list = array();
				 if($errorCodes){
					 foreach($errorCodes as $errorCode){
						 if(Eway_errors_codes($errorCode))
						 $errors_list[] = Eway_errors_codes($errorCode).'('.$errorCode.')';
					 }					 
				  }							
				  if($errors_list)
					  $alerts = $errors_list;
				   else
				    $alerts = array('error in request');
			  }				 
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
			}	
				
		}
		else{
			     if(isset($accesstoken_info)){
					 $error_info  = json_decode(base64_decode($accesstoken_info['error']),true);
					 $errorCodes  = explode(",",rtrim($error_info['errorCodes'],","));
					 $errors_list = array();
					 if($errorCodes){
						 foreach($errorCodes as $errorCode){
							 if(Eway_errors_codes($errorCode))
							 $errors_list[] = Eway_errors_codes($errorCode).'('.$errorCode.')';
						 }					 
					  }							
					  if($errors_list)
						  $alerts = $errors_list;
					   else
						$alerts = array('error in request'); 
				 }
				 else{
					$alerts = array('error in request'); 
				 }
				  
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
		 }	    
      }
  }
  
  public function get_einvoice_detail(){
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($boid);
	
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(4);
	if($this->isSandbox==1){
	$this->Gstin='07AGAPA5363L002';
	$this->username='AL001';
	$this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){	
	 $this->Gstin  = $branch_gstin_info['comp_gstin'];
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username ='';
		$this->password =''; 
	  }
	}
	if($this->request->getMethod() == 'post'){
		$AppKey = $this->app_key;
		$postdata = array("action"=>"ACCESSTOKEN","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->EinvoiceSubscriptionKey,
		                  "app_key"=>$AppKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox);
		$accesstoken_info = json_decode(get_einvoice_accesstoken_string($postdata),true);
		
		SaveErrorLog(json_encode($accesstoken_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
		if(isset($accesstoken_info) && $accesstoken_info['Status']=="1"){
			$accesstoken_resp = $accesstoken_info['Data'];
			$authtoken      = $accesstoken_resp['AuthToken'];
			$EncryptedSek   = $accesstoken_resp['Sek'];
			$options        = 0;
			$encryption     = $EncryptedSek;
			$ciphering      = "AES-256-ECB";
			$decryption_key =  base64_decode($AppKey);
			$decryption_iv  = ''; 
			$decryption     = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek   = base64_encode($decryption);
			
			$response_data    = array();  
			$bo_state_code    = $this->session->get('ses_bostecd');  
			$vch_txn_id       = $this->request->getVar('vch_txn_id');		 
			$einvno           = $this->request->getVar('table_id');
			$einv_irn         = $this->request->getVar('tablelkey');
			$envid            = $this->request->getVar('envid');	
			
		    //Generate E-way bill			
			$postdata       = array("SubscriptionKey"=>$this->EinvoiceSubscriptionKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,'irnno'=>$einv_irn,"username"=>$this->username);
			$bill_response  = json_decode(GetEInvoiceBill($authtoken,$postdata),true);
			SaveErrorLog(json_encode($bill_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
			
			if($bill_response['Status']=="1"){
				if(isset($bill_response['alert']) && $bill_response['alert']!=''){	
				 $alerts = array($bill_response['alert']);
				return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}else{	
				    $response     = decryptBySymmetricKey($bill_response['Data'],$DecryptedSek);
					$successData  = json_decode($response,true);
					if($successData)
					$this->TransactionModel->update_einvmaster_info($envid,$successData);
					return json_encode(['status' => true]);
				}
			}
			else{
			 if(isset($bill_response['alert']))	
				 $alerts = array($bill_response['alert']);
			  elseif(isset($bill_response['error'])){	
			     $error_info  = json_decode(base64_decode($bill_response['error']),true);
				 $errorCodes  = explode(",",rtrim($error_info['errorCodes'],","));
				 $errors_list = array();
				 if($errorCodes){
					 foreach($errorCodes as $errorCode){
						 if(Eway_errors_codes($errorCode))
						 $errors_list[] = Eway_errors_codes($errorCode).'('.$errorCode.')';
					 }					 
				  }							
				  if($errors_list)
					  $alerts = $errors_list;
				   else
				    $alerts = array('error in request');
			  }				 
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
			}	
				
		}
		else{
			     if(isset($accesstoken_info)){
					 $error_info  = json_decode(base64_decode($accesstoken_info['error']),true);
					 $errorCodes  = explode(",",rtrim($error_info['errorCodes'],","));
					 $errors_list = array();
					 if($errorCodes){
						 foreach($errorCodes as $errorCode){
							 if(Eway_errors_codes($errorCode))
							 $errors_list[] = Eway_errors_codes($errorCode).'('.$errorCode.')';
						 }					 
					  }							
					  if($errors_list)
						  $alerts = $errors_list;
					   else
						$alerts = array('error in request'); 
				 }
				 else{
					$alerts = array('error in request'); 
				 }
				  
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
		 }	    
      }
  }
  
 public function generate_einvoice()
{
    ini_set('precision', 10);
    ini_set('serialize_precision', 10);

    if ($this->request->getMethod() !== 'POST') {
        return json_encode(['status' => false, 'message' => 'Invalid request']);
    }

    $boid               = $this->session->get('ses_boid');
    $bo_state_code      = $this->session->get('ses_bostecd');
    $branch_gstin_info  = $this->VouchersModel->get_branch_gstin_info($boid);
    $cred_gstin_info    = $this->VouchersModel->get_credntial_gstin_info(5);

    // Credentials & GSTIN
    if ($this->isSandbox == 1) {
        $this->Gstin    = '07AGAPA5363L002';
        $this->username = 'AL001';
        $this->password = 'Alankit@123';
    } else {
        $this->Gstin    = $branch_gstin_info['hobo_gstin'] ?? '';
        $this->username = $cred_gstin_info['erp_pass_user'] ?? '';
        $this->password = $cred_gstin_info['erp_pass_pwd'] ?? '';
    }

    // Auth token
    $appKey   = $this->app_key;
    $tokenReq = [
        'action'          => 'ACCESSTOKEN',
        'username'        => $this->username,
        'password'        => $this->password,
        'SubscriptionKey' => $this->EinvoiceSubscriptionKey,
        'app_key'         => $appKey,
        'Gstin'           => $this->Gstin,
        'is_sandbox'      => $this->isSandbox,
        ];
    $tokenResp = json_decode(get_einvoice_accesstoken_string($tokenReq), true);
	if (!isset($tokenResp['Status']) || $tokenResp['Status'] != 1) {
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['authtoken generation error']]);
    }

    $authData   = $tokenResp['Data'];
    $authToken  = $authData['AuthToken'];
    $encryptedSek = $authData['Sek'];

    // Decrypt SEK
    $decryption_key = base64_decode($appKey);
    $decryptedSek   = base64_encode(openssl_decrypt($encryptedSek, 'AES-256-ECB', $decryption_key, 0, ''));

    $vch_txn_id = $this->request->getVar('vch_txn_id');
    $voucherResp = json_decode($this->GstexportModel->get_einvoice_voucher_info($vch_txn_id), true);
 
   if (empty($voucherResp['data'])) {
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['voucher not found']]);
    }

    $ItemListings   = [];
    $totAssVal      = 0;
    $cgstValue      = 0;
    $sgstValue      = 0;
    $igstValue      = 0;
    $cessValue      = 0;
    $TotInvValFc    = 0;
    $bsdTotInvValFc = 0;
    $totInvValue    = 0;
    $outsup_inv_type = '';
    $outsup_rev_chg_label = 'N';
    $EcmGstin       = null;
    $IgstOnIntra    = 'N';
    $docType        = 'INV';
    $outsup_bill_ref_no = '';
    $outsup_pos     = '';
    $fromAddr1 = $fromAddr2 = $fromPlace = $fromPincode = $fromStateCode = '';
    $party_add1 = $party_add2 = $party_city = $party_pin = $party_state_code = '';
    $party_gst = 'URP';
    $party_legal_name = $party_trade_name = '';

    // Build payload from first (only) voucher data row
    foreach ($voucherResp['data'] as $row) {
        $voucher_txn_id  = $row['voucher_txn_id'];
        $voucher_type_id = $row['voucher_type_id'];
        $voucher_info    = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);

        $vch_subtype_id   = $voucher_info['vch_sub_type_id']; // 0 => non-item
        $party_info       = $this->VouchersModel->get_comp_txn_party_data($voucher_txn_id);
       $party_id          = $party_info['master_id'];		
		$party_gst_info   = $this->VouchersModel->get_acc_details_info($party_id);

        $item_trxns = ($vch_subtype_id > 0) ? $this->VouchersModel->grid_item_transactions($voucher_txn_id) : [];
        $acc_trxns  = ($vch_subtype_id == 0) ? $this->VouchersModel->grid_account_transactions($voucher_txn_id) : [];
        $sundry_txn = $this->VouchersModel->grid_bsd_transactions($voucher_txn_id);

        $voucher_date    = date('d/m/Y', strtotime($voucher_info['vch_date']));
        $bo_id           = $this->bo_id;
		
		$get_account_info  = $this->VouchersModel->account_full_info($party_info['master_id']);
		$party_adrs_info   = $get_account_info['address_info'];
		
        if ($party_adrs_info) {
			$party_phone = '';
			if (!empty($party_adrs_info['contact_mobile'])) {
				$party_phone = $party_adrs_info['contact_mobile'];
			} elseif (!empty($party_adrs_info['contact_mobile2'])) {
				$party_phone = $party_adrs_info['contact_mobile2'];
			}

			$party_email = '';
			if (!empty($party_adrs_info['contact_email'])) {
				$party_email = $party_adrs_info['contact_email'];
			} elseif (!empty($party_adrs_info['contact_email2'])) {
				$party_email = $party_adrs_info['contact_email2'];
			}

            $party_phone = $party_phone;
            $party_name  = $get_account_info['acc_name'];
            $party_email = $party_email;
        }

        if ($party_adrs_info) {
            $party_add1  = $party_adrs_info['contact_add1'];
            $party_add2  = $party_adrs_info['contact_add2'];
            $party_city  = $party_adrs_info['contact_city'];
            $party_pin   = $party_adrs_info['contact_pin'];
            $party_state = $party_adrs_info['contact_state'];
            $party_country = $party_adrs_info['contact_country'];

            $party_state_info = $this->CommonModel->get_state_info($party_country, $party_state);
            $party_state_code = $party_state_info ? sprintf('%02d', $party_state_info['state_code']) : 0;
        } else {
            $party_state_code = 0;
        }

        $comp_ro_address    = $this->CommonModel->get_company_address_info($this->company_id, '1');
		if (empty($comp_ro_address)) {
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['company address missing']]);
    }
        $gstroutsup_info    = $this->VouchersModel->get_gstroutsup_info($voucher_txn_id);


        $comp_state_info = $this->CommonModel->get_state_info($comp_ro_address['cmp_country'], $comp_ro_address['cmp_state']);
        $comp_state_code = $comp_state_info ? sprintf('%02d', $comp_state_info['state_code']) : 0;

        $comp_gst        = $branch_gstin_info['hobo_gstin'] ?? '';
        $comp_legal_name = $branch_gstin_info['hobo_gstin_legal_name'] ?? '';
        $comp_trade_name = $branch_gstin_info['hobo_gstin_trade_name'] ?? '';

        if ($party_gst_info) {
            $party_gst        = ($party_gst_info['acc_gstin'] != '') ? $party_gst_info['acc_gstin'] : 'URP';
            $party_legal_name = $get_account_info['acc_name'] ?? 'N/A';
            $party_trade_name = $get_account_info['acc_name'] ?? 'N/A';
        }
		// EWB details (simplified; retain original choices)
        $ewb_req     = $this->VouchersModel->get_ewbmstreqn_data($voucher_txn_id);
        $ewb_supply_type = GetSupplyTypeCode($ewb_req['ewb_supply_type']);
		
        if ($gstroutsup_info) {
            $outsup_pos         = $gstroutsup_info['outsup_pos'];
            $outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
            $outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];
            $outsup_inv_type    = $ewb_supply_type;
            $outsup_eco         = $gstroutsup_info['outsup_eco'];
            if ($outsup_eco > 0) {
                $EcmGstin_info = $this->VouchersModel->party_gst_info($party_id);
                $EcmGstin      = $EcmGstin_info ? (($EcmGstin_info['acc_gstin'] != '') ? $EcmGstin_info['acc_gstin'] : null) : null;
            }
            $pos_state_info = $this->VouchersModel->pos_state_info(ltrim($outsup_pos, 0));
            if ($pos_state_info) {
                $statecode = sprintf('%02d', $pos_state_info['state_code']);
            }
            $outsup_rev_chg_label = ($outsup_rev_chg == 1) ? 'Y' : 'N';
        }

        // doc type by voucher_type
        $voucher_type = $voucher_info['vch_type_id'];
        if ($voucher_type == 18 || $voucher_type == 11) $docType = 'INV';
        elseif ($voucher_type == 2) $docType = 'CRN';
        elseif ($voucher_type == 3) $docType = 'DBN';
        else $docType = 'INV';

        
        $shiptoAddr1 = $shiptoAddr2 = $shiptoPlace = $shiptoPincode = $shiptoStateCode = $actshiptoStateCode = '';
        $ewb_txn_type = 1;
        if ($ewb_req) {
            $ewb_txn_type = $ewb_req['ewb_sub_supply_type'];
            if ($vch_subtype_id > 0 && ($ewb_txn_type == 1 || $ewb_txn_type == 2)) {
                $mc_info = $this->VouchersModel->mc_info($voucher_info['mat_cent_id']);
                if ($mc_info) {
                    $fromAddr1        = $mc_info['mat_cent_addr1']?? 'N/A';
                    $fromAddr2        = $mc_info['mat_cent_addr2']?? 'N/A';
                    $fromPlace        = $mc_info['mat_cent_city']?? 'N/A';
                    $fromPincode      = $mc_info['mat_cent_pin_zip']?? 'N/A';
                    $actFromStateCode = $mc_info['state_code']?? 'N/A';
                    $fromStateCode    = $mc_info['state_code']?? 'N/A';
                }
            }
            if ($vch_subtype_id > 0 && ($ewb_txn_type == 3 || $ewb_txn_type == 4)) {
                $disp = $ewb_req['gstdispfrm_info'];
                if ($disp) {
                    $fromAddr1        = $disp['dispfrm_addr1'] ?? 'N/A';
                    $fromAddr2        = $disp['dispfrm_addr2']?? 'N/A';
                    $fromPlace        = $disp['dispfrm_place']?? 'N/A';
                    $fromPincode      = $disp['dispfrm_pin']?? 'N/A';
                    $actFromStateCode = sprintf('%02d', $disp['dispfrm_state_code'])?? 'N/A';
                    $fromStateCode    = sprintf('%02d', $disp['dispfrm_state_code'])?? 'N/A';
                }
                $ship = $ewb_req['gstshipton_info'];
                if ($ship) {
                    $shiptoAddr1        = $ship['shipto_addr1']?? 'N/A';
                    $shiptoAddr2        = $ship['shipto_addr2']?? 'N/A';
                    $shiptoPlace        = $ship['shipto_place']?? 'N/A';
                    $shiptoPincode      = $ship['shipto_pin']?? 'N/A';
                    $actshiptoStateCode = sprintf('%02d', $ship['shipto_state_code'])?? 'N/A';
                    $shiptoStateCode    = sprintf('%02d', $ship['shipto_state_code'])?? 'N/A';
                }
            } elseif ($vch_subtype_id == 0) { // non-item use party address
                $fromAddr1        = $party_add1?? 'N/A';
                $fromAddr2        = $party_add2?? 'N/A';
                $fromPlace        = $party_city?? 'N/A';
                $fromPincode      = $party_pin?? 'N/A';
                $actFromStateCode = $party_state_code?? 'N/A';
                $fromStateCode    = $party_state_code?? 'N/A';
            }
        }
	   else{
		   // fetch address from HO 
			$headoffice_info = 	$this->VouchersModel->getHeadOfficeWithAddress();
			
			$fromAddr1        = $headoffice_info['hobo_addr1'] ?? 'N/A';
			$fromAddr2        = $headoffice_info['hobo_addr2'] ?? 'N/A';
			$fromPlace        = $headoffice_info['hobo_city'] ?? 'N/A';
			$fromPincode      = $headoffice_info['hobo_pin_zip'] ?? 'N/A';
			$actFromStateCode = $headoffice_info['state_code'] ?? 'N/A';
			$fromStateCode    = $headoffice_info['state_code'] ?? 'N/A';
	   }
        // Tax Totals
		$totals         = $this->VouchersModel->computeGstTotals($voucher_txn_id, $bo_state_code, $statecode, $sundry_txn, false);
		$totInvValue    = $totals['totInvValue'];
		$TotInvValFc    = $totals['TotInvValFc'];
		$cgstValue      = $totals['cgstValue'];
		$sgstValue      = $totals['sgstValue'];
		$igstValue      = $totals['igstValue'];
		$cessValue      = $totals['cessValue'];
		
        // Items / Accounts
        if ($vch_subtype_id > 0 && $item_trxns) {
            $itmcntr = 1;
            foreach ($item_trxns as $itm) {
                $totAssVal += $itm['item_amount'];
                $IsServc    = 'N';// field not in ERP2.0  ($itm['supply_type'] == '2') ? 'Y' : 'N';
                $CgstAmt  = $SgstAmt = $IgstAmt = 0;
				$tax      = $itm['tax_details'] ?? [];
				$CessAmt  = parseAmount($tax['cess']  ?? 0);

                if ($bo_state_code == $statecode) {
                   $CgstAmt  = parseAmount($tax['cgst']  ?? 0);
                   $SgstAmt  = parseAmount($tax['sgst']  ?? ($tax['ut_tax'] ?? 0)); // UT tax if present
                } else {
                   $IgstAmt  = parseAmount($tax['igst']  ?? 0);
                }
                $totalTaxAmnt = $IgstAmt + $CgstAmt + $SgstAmt + $CessAmt;
                $ItemListings[] = [
                    'SlNo'          => (string)$itmcntr,
                    'PrdDesc'       => $itm['item_name'],
                    'IsServc'       => $IsServc,
                    'HsnCd'         => $itm['item_hsn'],
                    'Qty'           => $itm['item_qty'],
                    'Unit'          => $itm['item_unit_name'],
                    'UnitPrice'     => parseAmountPrice(($itm['item_amount']/$itm['item_qty']), 3),
                    'TotAmt'        => parseAmount($itm['item_amount']),
                    'Discount'      => 0,
                    'PreTaxVal'     => 1,
                    'AssAmt'        => parseAmount($itm['item_amount']),
                    'GstRt'         => $itm['igst_rate'],
                    'IgstAmt'       => parseAmount($IgstAmt),
                    'CgstAmt'       => parseAmount($CgstAmt),
                    'SgstAmt'       => parseAmount($SgstAmt),
                    'CesRt'         => 0,
                    'CesAmt'        => 0,
                    'CesNonAdvlAmt' => 0,
                    'StateCesRt'    => 0,
                    'StateCesAmt'   => 0,
                    'StateCesNonAdvlAmt' => 0,
                    'OthChrg'       => 0,
                    'TotItemVal'    => parseAmount($itm['item_amount'] + $totalTaxAmnt),
                    'OrgCntry'      => 'IN',
                ];
                $itmcntr++;
            }
        }

        if ($vch_subtype_id == 0 && $acc_trxns) { // non-items
            $itmcntr = 1;
            foreach ($acc_trxns as $itm) {
                $totAssVal += $itm['amount'];
                $IsServc    = 'Y';
                $CgstAmt = $SgstAmt = $IgstAmt = 0;
				$tax      = $itm['tax_details'] ?? [];
				$CessAmt  = parseAmount($tax['cess']  ?? 0);
                if ($bo_state_code == $statecode) {
                     $CgstAmt  = parseAmount($tax['cgst']  ?? 0);
                     $SgstAmt  = parseAmount($tax['sgst']  ?? ($tax['ut_tax'] ?? 0)); // UT tax if present
                } else {
                    $IgstAmt  = parseAmount($tax['igst']  ?? 0);
                }
                $totalTaxAmnt = $IgstAmt + $CgstAmt + $SgstAmt + $CessAmt;
                $ItemListings[] = [
                    'SlNo'          => (string)$itmcntr,
                    'PrdDesc'       => $itm['account_name'],
                    'IsServc'       => $IsServc,
                    'HsnCd'         => $itm['item_hsn_sac'],
                    'Qty'           => 0,
                    'Unit'          => 'OTH',
                    'UnitPrice'     => 0,
                    'TotAmt'        => parseAmount($itm['amount']),
                    'Discount'      => 0,
                    'PreTaxVal'     => 1,
                    'AssAmt'        => parseAmount($itm['amount']),
                    'GstRt'         => parseAmount($itm['igst_rate']),
                    'IgstAmt'       => parseAmount($IgstAmt),
                    'CgstAmt'       => parseAmount($CgstAmt),
                    'SgstAmt'       => parseAmount($SgstAmt),
                    'CesRt'         => 0,
                    'CesAmt'        => 0,
                    'CesNonAdvlAmt' => 0,
                    'StateCesRt'    => 0,
                    'StateCesAmt'   => 0,
                    'StateCesNonAdvlAmt' => 0,
                    'OthChrg'       => 0,
                    'TotItemVal'    => parseAmount($itm['amount'] + $totalTaxAmnt),
                    'OrgCntry'      => 'IN',
                ];
                $itmcntr++;
            }
        }
    } // end foreach voucher data

    $TranDtls = [
        'TaxSch'   => 'GST',
        'SupTyp'   => $outsup_inv_type,
        'RegRev'   => $outsup_rev_chg_label,
        'EcmGstin' => $EcmGstin,
        'IgstOnIntra' => $IgstOnIntra,
    ];
    $DocDtls = [
        'Typ' => $docType,
        'No'  => $outsup_bill_ref_no,
        'Dt'  => $voucher_date,
    ];
    $SellerDtls = [
        'Gstin' => $comp_gst,
        'LglNm' => $comp_legal_name,
        'TrdNm' => $comp_trade_name,
        'Addr1' => $fromAddr1,
        'Addr2' => $fromAddr2,
        'Loc'   => $fromPlace,
        'Pin'   => $fromPincode,
        'Stcd'  => $fromStateCode,
    ];
	
    $BuyerDtls = [
        'Gstin' => $party_gst,
        'LglNm' => $party_legal_name,
        'TrdNm' => $party_trade_name,
        'Pos'   => $outsup_pos,
        'Addr1' => $party_add1,
        'Loc'   => $party_city,
        'Pin'   => $party_pin,
        'Stcd'  => $party_state_code,
    ];

    $ValDtls = [
        'AssVal'      => parseAmount($totAssVal),
        'CgstVal'     => parseAmount($cgstValue),
        'SgstVal'     => parseAmount($sgstValue),
        'IgstVal'     => parseAmount($igstValue),
        'CesVal'      => parseAmount($cessValue),
        'StCesVal'    => 0,
        'Discount'    => 0,
        'OthChrg'     => 0,
        'RndOffAmt'   => 0,
        'TotInvVal'   => parseAmount($totInvValue),
        'TotInvValFc' => parseAmount($TotInvValFc),
    ];

    $payload = [
        'Version'   => '1.1',
        'TranDtls'  => $TranDtls,
        'DocDtls'   => $DocDtls,
        'SellerDtls'=> $SellerDtls,
        'BuyerDtls' => $BuyerDtls,
        'ItemList'  => $ItemListings,
        'ValDtls'   => $ValDtls,
    ];

    // Optional Disp/Ship for ewb_txn_type == 4
    if (isset($ewb_txn_type) && $ewb_txn_type == 4) {
        $payload['DispDtls'] = [
            'Nm'    => '',
            'Addr1' => $fromAddr1,
            'Addr2' => $fromAddr2,
            'Loc'   => $fromPlace,
            'Pin'   => $fromPincode,
            'Stcd'  => $fromStateCode,
        ];
        $payload['ShipDtls'] = [
            'Gstin' => '',
            'LglNm' => '',
            'TrdNm' => '',
            'Addr1' => '',
            'Addr2' => '',
            'Loc'   => '',
            'Pin'   => '',
            'Stcd'  => '',
        ];
    }

    $RequestPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    SaveErrorLog($RequestPayload);

    $Base64RequestPayload = base64_encode($RequestPayload);
    $reqpayload           = encryptBySymmetricKey($Base64RequestPayload, $decryptedSek);

    // Call IRN generation
    $postdata_irn = [
        'UserName'       => $this->username,
        'SubscriptionKey'=> $this->EwaySubscriptionKey,
        'Gstin'          => $this->Gstin,
        'is_sandbox'     => $this->isSandbox,
    ];
    $bill_response = json_decode(generate_einvoice_irn($reqpayload, $authToken, $postdata_irn), true);
    SaveErrorLog(json_encode($bill_response));

    if (isset($bill_response['Status']) && $bill_response['Status'] == 1) {
        if (!empty($bill_response['alert'])) {
            $alerts = [$bill_response['alert']];
            return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
        }

        $response    = decryptBySymmetricKey($bill_response['Data'], $decryptedSek);
        $successData = json_decode($response, true);
        $einv_id     = $this->VouchersModel->update_einvoice_data($vch_txn_id, $successData);
       /* 
        $save_data = [
            'einv_id'        => $einv_id,
            'einv_tax_schm'  => 'GST',
            'einv_supply_type'=> $outsup_inv_type,
            'einv_reg_rev'   => $outsup_rev_chg_label,
            'einv_ecm_gstin' => $EcmGstin,
            'einv_igst_intra'=> $IgstOnIntra,
            'einb_doc_type'  => $docType,
            'voucher_txn_id' => $vch_txn_id,
        ];
        $this->VouchersModel->add_einvmstreq_info($vch_txn_id, $save_data); */

        return json_encode([
            'AckNo'    => $successData['AckNo'],
            'Irn'      => $successData['Irn'],
            'status'   => true,
            'message'  => 'JSON file downloaded',
            'jsonfile' => $RequestPayload,
            'errors'   => '',
            'filename' => 'eway-invoice.json'
        ]);
    }

    // Error path
    $alerts = ['error in request'];
    if (isset($bill_response['ErrorDetails']) && $bill_response['ErrorDetails']) {
        $errors_list = [];
        foreach ($bill_response['ErrorDetails'] as $err) {
            $errors_list[] = $err['ErrorMessage'] . '(' . $err['ErrorCode'] . ')';
        }
        if ($errors_list) $alerts = $errors_list;
    }

    return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
}
  
  public function update_eway_partb(){ // Update E-WAY PART-B 
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info    = $this->TransactionModel->get_branch_gstin_info($boid);	
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(4);
	
	if($this->isSandbox==1){
	 $this->Gstin     = '07AGAPA5363L002';
	 $this->username  = 'AL001';
	 $this->password  = 'Alankit@123';  
	}	
	
	if($this->isSandbox==0){	
	 $this->Gstin       = $branch_gstin_info['comp_gstin'];
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username = '';
		$this->password = ''; 
	  }
	  
	}
	
	if($this->request->getMethod() == 'post' && $this->request->isAjax()){
	       $transporter_id      =  $this->request->getVar('transporter_id');
		   $gstin_id            =  $this->request->getVar('gstin_id');
           $transporter_name    =  $this->request->getVar('transporter_name');
           $transporter_doc_no  =  $this->request->getVar('transporter_doc_no');
           $transport_mode      =  $this->request->getVar('transport_mode');
           $transport_distance  =  $this->request->getVar('transport_distance');
           $transport_doc_date  =  $this->request->getVar('transport_doc_date');
           $vehicle_no          =  $this->request->getVar('vehicle_no');
           $vehicle_type        =  $this->request->getVar('vehicle_type');
		   $isedit              =  $this->request->getVar('isedit');		   
		   $voucher_txn_id      =  $this->request->getVar('voucher_txn_id');
		   $ewayno              =  $this->request->getVar('ewayno');
		   $ewayno              = '701008911755';
		   
		   $gstroutsup_info = $this->TransactionModel->gstroutsup_info($voucher_txn_id);
		   $gstrinwsup_info = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);				
		   if($gstroutsup_info){
			$state_code = $gstroutsup_info['outsup_pos'];	
		   }
		   if($gstrinwsup_info){
			$state_code =$gstrinwsup_info['inwsup_pos'];
		   }		   
		   $stcode     = ltrim($state_code,"0");
		   $state_info =  $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_id,state_name,country_id, state_code')->where('state_code',$stcode)->get()->getRowArray();
           $fromPlace ='';
		   if($state_info){
			   $fromPlace =$state_info['state_name'];
		   }
		   if($isedit=="0") // means fresh entry
		   {	
		      $exists_transporter = $this->TransactionModel->exists_transporter_master($transporter_name);
		      if($exists_transporter >0){
			    return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Transporter name already exists']]);
		       }
		   }
		   /* if($isedit=="0") // means fresh entry
		   {	
		   $exists_transporter = $this->TransactionModel->exists_transporter_master($transporter_name);
		   if($exists_transporter >0){
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Transporter name already exists']]);
		   }else{		   
			   $transporter_data = array("transporter_id"=>$transporter_id,"gstin_id"=>$gstin_id,
										 "transporter_name"=>$transporter_name
										 );									
			   $gsttpt_id = $this->TransactionModel->add_transporter_master($transporter_data);
			   
			   
			   
			   if($transport_distance!='' && $vehicle_no!=''){
			   $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
			    $ewb_id=0;
 			   if($get_ewbmstreqn_data)
			     $ewb_id = $get_ewbmstreqn_data['ewb_id'];
			     $conso_ewb_id = $this->TransactionModel->get_ewbmstcons_data($ewb_id);
			     $transport_doc_date = date('Y-m-d',strtotime($transport_doc_date));	
				 // save into ewbpartbdt table
				 $ewbpartbdt_data = array("conso_ewb_id"=>$conso_ewb_id,"gsttpt_id"=>$gsttpt_id,"ewb_id"=>$ewb_id,
				                      "trans_veh_no"=>$vehicle_no,"trans_veh_type"=>$vehicle_type,
									  "trans_mode"=>$transport_mode,"trans_doc_no"=>$transporter_doc_no,
									  "trans_doc_date"=>$transport_doc_date, "trans_dist"=>$transport_distance,
									  "trans_frm_state_code"=>$state_code,"voucher_txn_id"=>$voucher_txn_id);	
				 $this->TransactionModel->add_ewbpartbdt($ewbpartbdt_data);			 
				}
			   }  
		   }		   
		   else if($isedit=="1") // update entry
		   {
			   $transporter_master_id  =  $this->request->getVar('gsttpt_id');			   
			   $transporter_data = array(
									 "transporter_doc_no"=>$transporter_doc_no,
									 "transport_mode"=>$transport_mode,
									 "transport_distance"=>$transport_distance,
									 "transport_doc_date"=>date("Y-m-d",strtotime($transport_doc_date)),
									 "vehicle_no" =>$vehicle_no,
									 "vehicle_type"=>$vehicle_type,
									 "state_code"=>$state_code
									 );
			    $this->TransactionModel->upate_transporter_master($transporter_master_id,$transporter_data,$voucher_txn_id); 
			} */
	   
	    $AppKey   = $this->app_key;		
		$postdata = array("action"=>"ACCESSTOKEN","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->EwaySubscriptionKey,
		                  "app_key"=>$AppKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox);
		$accesstoken_info = json_decode(get_eway_accesstoken_string($postdata),true);
		SaveErrorLog(json_encode($postdata));
		if(isset($accesstoken_info) && $accesstoken_info['status']=="1"){
			$authtoken        = $accesstoken_info['authtoken'];
			$EncryptedSek     = $accesstoken_info['sek'];
			$options          = 0;
			$encryption       = $EncryptedSek;
			$ciphering        = "AES-256-ECB";
			$decryption_key   = base64_decode($AppKey);
			$decryption_iv    = ''; 
			$decryption       = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek     = base64_encode($decryption); 
			
			$response_data   = array("ewbNo"=>$ewayno,"vehicleNo"=>$vehicle_no,"fromPlace"=>$fromPlace,"fromState"=>$state_code,"reasonCode"=>"",
									"reasonRem"=>"","transDocNo"=>$transporter_doc_no,"transDocDate"=>date("Y-m-d",strtotime($transport_doc_date)),"transMode"=>$transport_mode,
									"vehicleType"=>$vehicle_type);
			$RequestPayload       = json_encode($response_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);		
            SaveErrorLog($RequestPayload);
		   
		    $Base64RequestPayload = base64_encode($RequestPayload);
			$reqpayload           = encryptBySymmetricKey($Base64RequestPayload, $DecryptedSek);			
		    //Generate E-Way Part-B`
			$postdata             = array("action"=>"VEHEWB","UserName"=>$this->username,"SubscriptionKey"=>$this->EwaySubscriptionKey,
			                             "Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox);
			//echo json_encode($postdata);
			//echo 'authtoken : '.$authtoken;
			//echo '<br>';
			//echo 'reqpayload: '.$reqpayload;
			//echo '<br>';
			$bill_response        = json_decode(generate_eway_partb($reqpayload,$authtoken,$postdata),true);
			
			if($bill_response['status']=="1"){
				if(isset($bill_response['alert']) && $bill_response['alert']!=''){	
				 $alerts = array($bill_response['alert']);
				return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}else{					
					//$response     = decryptBySymmetricKey($bill_response['data'],$DecryptedSek);
					//$successData  = json_decode($response,true);					
					//$this->TransactionModel->add_ewbmastern_info($ewb_id,$successData);
					
				   // return json_encode(['ewayBillDate'=>$successData['ewayBillDate'],'ewayBillNo'=>$successData['ewayBillNo'],'status' => true,'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload,'errors'=>'','filename'=>'eway-invoice.json']);
				}
			}
			else{
			 if(isset($bill_response['alert']))	
				 $alerts = array($bill_response['alert']);
			  elseif(isset($bill_response['error'])){	
			     $error_info  = json_decode(base64_decode($bill_response['error']),true);
				 $errorCodes  = explode(",",rtrim($error_info['errorCodes'],","));
				 $errors_list = array();
				 if($errorCodes){
					 foreach($errorCodes as $errorCode){
						 if(Eway_errors_codes($errorCode))
						 $errors_list[] = Eway_errors_codes($errorCode).'('.$errorCode.')';
					 }					 
				  }							
				  if($errors_list)
					  $alerts = $errors_list;
				   else
				    $alerts = array('error in request');
			  }				 
			   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $alerts]);
			}
			
		}
		else{
		
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['authtoken generation error']]);
		 }
	
	}

     	
	  
  }
  
  

 public function generate_otp_request(){
	 ini_set('precision', 10);
     ini_set('serialize_precision', 10);
	 $boid = $this->session->get('ses_boid');
	 $branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($boid);
	
	 $credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(1);
	
	 if($this->isSandbox==1){
	 $this->Gstin = '33AAZCA3730B7ZP';
	 $this->username='TN_NT4.2710';
	 $this->password='Alankit@123';  
	} 	
	if($this->isSandbox==0){	
	 $this->Gstin  = $branch_gstin_info['comp_gstin'];
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username ='';
		$this->password =''; 
	  } 
	}
	
	if($this->request->getMethod() == 'post'){			
		$state_cd = substr($this->Gstin, 0, 2);
		$postdata = array("action"=>"OTPREQUEST","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                  "app_key"=>$this->app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						  "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						  "state-cd"=>$state_cd,"client_secret"=>$this->GSTRONEclientsecret);
		
		//return json_encode(['status' => true, 'message' => '', 'errors' => [],'apptoken'=>$this->app_key]);		 
		$otptoken_info = json_decode(request_otp_gstrone($postdata),true);
		if(isset($otptoken_info) && $otptoken_info['status_cd']==1){
		   return json_encode(['status' => true, 'message' => '', 'errors' => [],'apptoken'=>$otptoken_info['appkeye']]);	
		}
		else{		
		   return json_encode(['status' => false, 'message' => 'Validation Error','apptoken'=>'', 'errors' => ['OTP request failed!!']]);
		 } 
	}		
		
 }
 
 public function fetch_gstr2ab_return_info(){
  try {
	
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info    = $this->TransactionModel->get_branch_gstin_info($boid);
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(1);
	
	if($this->isSandbox==1){
	 $this->Gstin='33AAZCA3730B7ZP';
	 $this->username='TN_NT4.2710';
	 $this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){	
	 $this->Gstin  = $branch_gstin_info['comp_gstin'];
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username ='';
		$this->password =''; 
	  }
	}
	if($this->request->getMethod() == 'post'){	   
	    $returntype    = $this->request->getVar('returntype'); // 2ab, 2b	   
		$from_date     = $this->request->getVar('from_date');
		$otp           = $this->request->getVar('otp');
		$to_date       = $this->request->getVar('to_date');
		$this->app_key = $this->request->getVar('apptoken');
		$key           = base64_decode($this->app_key);
        $cipher        = 'aes-256-ecb';
		$encrypted     = openssl_encrypt($otp, $cipher, $key, OPENSSL_RAW_DATA);
		$encrypted_otp = base64_encode($encrypted);		
		$ret_period    = date('mY',strtotime($to_date));//'122024'
		$state_cd      = substr($this->Gstin, 0, 2);
		
		$EncryptedSek     = "hAedBT8GfMjV0ayaH3pIdxLSdo/0OYdY5khbR4JxhzTLLk4F8FmytngeTN6cuBup";
			$options          = 0;
			$encryption       = $EncryptedSek;
			$ciphering        = "AES-256-ECB";
			$decryption_key   = base64_decode($key);
			$decryption_iv    = ''; 
			$decryption       = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek     = base64_encode($decryption);
			
		$a=array("status_cd" => 1,
    "data" => "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",
    "rek" => "SNmBhZsmEkzgskahHd5Hfp8FRUf7nlbR8QJJ1BcXZH0onmsjE2YMtx5UCf1ndy1l",
    "hmac" => "omhQWLpYvsYuC7SpStcmY41iGOs16CaIDUYS0g2d3dk="
		);
$response     = decryptBySymmetricKey($a['data'],$DecryptedSek);
					$successData  = json_decode($response,true);
					echo'<pre>';
					print_r($successData);
die();
		
		
		echo $this->app_key;
		echo '<br>'.$ret_period;
		$postdata      = array("action"=>"AUTHTOKEN","username"=>$this->username,
		                       "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                       "app_key"=>$this->app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						       "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						       "state-cd"=>$state_cd,"client_secret"=>$this->GSTRONEclientsecret,
						       "otp"=>$encrypted_otp);
							 
		$authtoken_info = json_decode(request_authtoken_gstrone($postdata),true);
		if(isset($authtoken_info) && $authtoken_info['status_cd']==1){
			$authtoken        = $authtoken_info['auth_token'];
			$EncryptedSek     = $authtoken_info['sek'];
			$options          = 0;
			$encryption       = $EncryptedSek;
			$ciphering        = "AES-256-ECB";
			$decryption_key   = base64_decode($AppKey);
			$decryption_iv    = ''; 
			$decryption       = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek     = base64_encode($decryption);
			$postdata = array("username"=>$this->username,
							  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
							  "app_key"=>$this->app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
							  "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
							  "state-cd"=>$state_cd,"client_secret"=>$this->GSTRONEclientsecret,
							  'ret_period'=>$ret_period,'auth_token'=>$authtoken,"ctin"=>"","from_time"=>"");
        if($returntype=='2ab2b')
		   $return_info = json_decode(GetGSTR2A_B2BInfo($postdata),true);
		if($returntype=='2b')
		   $return_info = json_decode(GetGSTR2BInfo($postdata),true);
	 	
		if($return_info['status_cd']==1){
				if(isset($return_info['alert']) && $return_info['alert']!=''){	
				 $alerts = array($return_info['alert']);
				  return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}
				else{					
					$response     = decryptBySymmetricKey($return_info['data'],$DecryptedSek);
					$successData  = json_decode($response,true);
					echo'<pre>';
					print_r($successData);
					//$this->TransactionModel->add_einvmaster_info($voucher_txn_id,$successData);					
				    //return json_encode(['show_preference_modal'=>$show_preference_modal,'AckNo'=>$successData['AckNo'],'Irn'=>$successData['Irn'],'status' => true,'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload,'errors'=>'','filename'=>'eway-invoice.json']);
				}				
				
			}		
			
			
	   echo '<br>';		 
		 echo '<pre>';
		   print_r($return_info);
		}
		else{		
		  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['authtoken generation error']]);
		 }
		
		
		
		
		
	}
	 }	
	catch(Exception $e) {
	  echo 'Message: ' .$e->getMessage();
	} 
 }
 
  
  public function save_gstrone_preferences(){
	 ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($boid);
	$ret_period        ='122024';
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(4);
	if($this->isSandbox==1){
	 $this->Gstin = '33AAZCA3730B7ZP';
	 $this->username='TN_NT4.2710';
	 $this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){	
	 $this->Gstin  = $branch_gstin_info['comp_gstin'];
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username ='';
		$this->password =''; 
	  }
	}
    $fy ='2024-25';
    if($this->request->getMethod() == 'post'){	
	
		$AppKey        = $this->app_key;
		$preferences   = $this->request->getVar('preferences');
		$from_date     = $this->request->getVar('from_date');
		$to_date       = $this->request->getVar('to_date');
	echo ">>>>".	$otp           = $this->session->get("sess_otp");
		$key           = base64_decode($this->gstr_app_key);
        $cipher        = 'aes-256-ecb';
		$encrypted     = openssl_encrypt($otp, $cipher, $key, OPENSSL_RAW_DATA);
		$encrypted_otp = base64_encode($encrypted);		
		$fp            ='122024';// date('mY',strtotime($to_date));
		
		$postdata = array("action"=>"AUTHTOKEN","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                  "app_key"=>$this->gstr_app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						  "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						  "state-cd"=>"33","client_secret"=>$this->GSTRONEclientsecret,
						  'otp'=>$encrypted_otp);
		$authtoken_info = json_decode(request_authtoken_gstrone($postdata),true);
		echo '<pre>';
		print_r($authtoken_info);
		
		echo 'gstr_app_key => '.$this->gstr_app_key;
		echo '<br>';
		
		    $authtoken        = $authtoken_info['auth_token'];		
			$EncryptedSek     = $authtoken_info['sek'];
			$options          = 0;
			$encryption       = $EncryptedSek;
			$ciphering        = "AES-256-ECB";
			$decryption_key   = base64_decode($AppKey);
			$decryption_iv    = ''; 
			$decryption       = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek     = base64_encode($decryption);
						
			$postdata = array("username"=>$this->username,
							  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                      "app_key"=>$this->gstr_app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						      "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						      "state-cd"=>"33","client_secret"=>$this->GSTRONEclientsecret,
						      "auth_token"=>$authtoken,"ret_period"=>$ret_period);
						  
			$reqpayload_data      = array("gstin"=>$this->Gstin,"fy"=>$fy,"quarter"=>"Q3","preference"=>"M");
		    $Base64RequestPayload = base64_encode(json_encode($reqpayload_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
			$reqpayload           = encryptBySymmetricKey($Base64RequestPayload, $DecryptedSek);
			$data                 = '';
			$hmac                 =  generateHMAC($data, $EncryptedSek);
			$request_payload = array('action'=>"SAVEPREF",
				  "data" => $reqpayload,
				  "hmac" => $hmac
                 );
				 echo json_encode($reqpayload_data);
				 echo '<br>';
				echo json_encode($request_payload); 
				echo '<br>';
			$preferences_info = json_decode(save_preference_string($request_payload,$postdata),true);
			echo '<pre>';
			print_r($preferences_info);
		
		
		
		/**********************************/
		die();
		
		$postdata = array("action"=>"AUTHTOKEN","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                  "app_key"=>$this->gstr_app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						  "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						  "state-cd"=>"33","client_secret"=>$this->GSTRONEclientsecret,
						  'otp'=>$encrypted_otp);
		$authtoken_info = json_decode(request_authtoken_gstrone($postdata),true);
		echo '<pre>';
		print_r($authtoken_info);
		if(isset($authtoken_info) && $authtoken_info['status_cd']==1){
			$authtoken        = $authtoken_info['auth_token'];		
			$EncryptedSek     = $authtoken_info['sek'];
			$options          = 0;
			$encryption       = $EncryptedSek;
			$ciphering        = "AES-256-ECB";
			$decryption_key   = base64_decode($AppKey);
			$decryption_iv    = ''; 
			$decryption       = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek     = base64_encode($decryption);
			
			
			$postdata = array("username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                  "app_key"=>$this->gstr_app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						  "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						  "state-cd"=>"33","client_secret"=>$this->GSTRONEclientsecret,
						  "auth_token"=>$authtoken,
						  "ret_period"=>$ret_period);
						  
			$reqpayload_data = array("gstin"=>$this->Gstin,"fy"=>$fy,"quarter"=>"Q1","preference"=>"Q");
		    $Base64RequestPayload =base64_encode(json_encode($reqpayload_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
			$reqpayload           = encryptBySymmetricKey($Base64RequestPayload, $DecryptedSek);
			
			$preferences_info = json_decode(save_preference_string($reqpayload,$postdata),true);
			echo '<pre>';
			print_r($preferences_info);
		}
		else{		
		  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['authtoken generation error']]);
		 }
		
	}
	
	  
  }
  
  public function generate_gstrone(){
	  try {
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($boid);
	$ret_period        ='032025';
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(4);
	if($this->isSandbox==1){	
	 $this->Gstin = '33AAZCA3730B5ZR';
	 $this->username='TN_NT4.2708';
	 $this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){	
	 $this->Gstin  = $branch_gstin_info['comp_gstin'];
	 if($credntial_gstin_info){
		$this->username = $credntial_gstin_info['cred_user'];
		$this->password = $credntial_gstin_info['cred_pass'];
	 }else{
		$this->username ='';
		$this->password =''; 
	  }
	}
	if($this->request->getMethod() == 'post'){	
	
		$AppKey        = $this->app_key;
		if(!$this->session->get("sess_otp")){
		$otp           = $this->request->getVar('otp');	//'575757
		$this->session->set("sess_otp",$otp);
		}else{
		$otp           = $this->session->get("sess_otp");	//'575757	
		}
		$otp		    ='575757';    
		$from_date     = $this->request->getVar('from_date');
		$to_date       = $this->request->getVar('to_date');
		$key           = base64_decode($this->gstr_app_key);
        $cipher        = 'aes-256-ecb';
		$encrypted     = openssl_encrypt($otp, $cipher, $key, OPENSSL_RAW_DATA);
		$encrypted_otp = base64_encode($encrypted);		
		$fp            ='122021';// date('mY',strtotime($to_date));
				
		
		$postdata = array("action"=>"AUTHTOKEN","username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                  "app_key"=>$this->gstr_app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						  "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						  "state-cd"=>"33","client_secret"=>$this->GSTRONEclientsecret,
						  'otp'=>$encrypted_otp);
		$authtoken_info = json_decode(request_authtoken_gstrone($postdata),true);
		echo '<pre>';
		print_r($authtoken_info);
		
		echo '============================================';
		
		echo 'gstr_app_key => '.$this->gstr_app_key;
		echo '<br>';
		if(isset($authtoken_info) && $authtoken_info['status_cd']==1){
			$authtoken        = $authtoken_info['auth_token'];		
			$EncryptedSek     = $authtoken_info['sek'];
			$options          = 0;
			/******* Code same as use in download json  in /etaxes url/generate_gstrone() *******/
			
		   $state_data    = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
		   $states= array();
		   foreach($state_data as $strow){
			$state_code = sprintf('%02d',$strow['state_code']);	
			$states[$state_code]=$strow['state_name'];
		   }
	     
	   
	     $tableinfo       = $this->table_lists_detailed;
	     $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	     if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = $get_comp_taxt_info['comp_gstin'];
	     else
		  $comp_gst ='';
			
		$ErrosList = array();
	    /* if(strlen(trim($comp_gst))<15 || strlen(trim($comp_gst))>15){
			$ErrosList[]="Company GSTIN should be Alphanumeric with 15 characters.<br>";
		}
	    if(strlen(trim($comp_gst))==15 && inputmask_gstin(trim($comp_gst))==false){
			$ErrosList[]="Company GSTIN not valid.<br>";
		} */
	    if(count($ErrosList) >0){
		    return json_encode(['status' => false, 'message' =>'Validation Error','errors'=>$ErrosList]);  					   
	    }	
	   else{		
	    $comp_gst                 = $this->Gstin;
	    $tableinfo                = array('ctin'=>'33AAZCA3730B5ZR','cmp_tax_short_code'=>array('REGSPLY'),'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>array('B2B'),'outsup_eco'=>'0','value'=>0);
		$final_result             = array();
	    $final_result             = array("gstin"=>$comp_gst,"fp"=>$fp,"gt"=>0,"cur_gt"=>0);	  
		$response_info            = $this->GstexportModel->gst_b2b_table_export($from_date, $to_date,$tableinfo,$states);			
		if(!empty($response_info))
		$final_result['b2b']      = $response_info;	
		 // pass b2b for testing rest will pass after testing done
		 
		/* $tableinfo                = array('cmp_tax_short_code'=>array('REGSPLY'),'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>array('B2CL'),'outsup_eco'=>'0','value'=>'','cnd'=>'>');
		$response_info            = $this->GstexportModel->gst_b2cl_table_export($from_date, $to_date,$tableinfo,$states);			
		if(!empty($response_info))
		$final_result['b2cl']     = $response_info;
		
		$tableinfo                = array('cmp_tax_short_code'=>array('REGSPLY'),'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>array('B2CS'),'outsup_eco'=>'0','value'=>'','cnd'=>'<');
		$response_info            = $this->GstexportModel->gst_b2cs_table_export($from_date, $to_date,$tableinfo,$states);			
		if(!empty($response_info))
		$final_result['b2cs']     = $response_info; */
		
		/* $response_info              = $this->GstexportModel->gst_nill_table_export($from_date, $to_date,$states);	
		if(!empty($response_info))
		$final_result['nil']        = $response_info['data'];
		$response_info              = $this->GstexportModel->gst_exports_table_export($from_date, $to_date,$states);	
		if(!empty($response_info['inv']))
		$final_result['exp']        = $response_info['inv']; 
		
		$response_info            = $this->GstexportModel->gst_cdnr_table_export($from_date, $to_date,$states);	
		if(!empty($response_info['inv']))
		$final_result['cdnr']     = $response_info['inv'];
		$response_info            = $this->GstexportModel->gst_cdnur_table_export($from_date, $to_date,$states);	
		if(!empty($response_info['inv']))
		$final_result['cdnur']    = $response_info['inv']; 
	
		$response_info            = $this->GstexportModel->gst_docs_table_export($from_date, $to_date,$states);
		$final_result['doc_issue']= $response_info['doc_det'];
		 */
		$ret_period ='122024';//date("mY",strtotime($to_date)
		//$response_info            = $this->GstexportModel->gst_hsn_table_export($from_date, $to_date,$states);			
		//$final_result['hsn']      = $response_info;		
		
		$final_resultn    =json_decode('{
  "gstin": "33AAZCA3730B5ZR",
  "fp": "122024",
  "gt": 0,
  "cur_gt": 0,
  "b2b": [
    {
      "ctin": "33AAZCA3730B7ZP",
      "inv": [
        {
          "inum": "S008400",
          "idt": "24-12-2024",
          "val": 729248.16,
          "pos": "06",
          "rchrg": "N",
          "etin": "",
          "inv_typ": "R",
          "diff_percent": 0.65,
          "itms": [
            {
              "num": 1,
              "itm_det": {
                "rt": 5,
                "txval": 10000,
                "iamt": 325,
                "csamt": 500
              }
            }
          ]
        }
      ]
    }
  ]
  }',true);
		$data            ='';     
		$options          = 0;
		$encryption       = $EncryptedSek;
		$ciphering        = "AES-256-ECB";
		$decryption_key   = base64_decode($AppKey);
		$decryption_iv    = ''; 
		$decryption       = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
		$DecryptedSek     = base64_encode($decryption); 
	echo	$request_payload =  json_encode($final_resultn,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);					
	echo '<br>';
		$Base64RequestPayload = base64_encode($request_payload);
		$reqpayload           = encryptBySymmetricKey($Base64RequestPayload, $DecryptedSek);			
		  
		
		$hmac            =  generateHMAC($data, $EncryptedSek);
		$request_payload = array('action'=>"RETSAVE",
				  "data" => $reqpayload,
				  "hmac" => $hmac
                 );
		
		echo '<br>';
		echo json_encode($request_payload);
		
		$postdata = array("username"=>$this->username,
		                  "password"=>$this->password,"SubscriptionKey"=>$this->GstrOneSubscriptionKey,
		                  "app_key"=>$this->gstr_app_key,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox,
						  "random_rxn_id"=>random_rxn_id(),"clientid"=>$this->GSTRONEclientid,
						  "state-cd"=>"33","client_secret"=>$this->GSTRONEclientsecret,
						  'request_payload'=>$request_payload,"auth_token"=>$authtoken,
						  "ret_period"=>$ret_period);		
		//echo '<pre>';	
		///echo '<br>';		
		$bill_response  = json_decode(save_gstrone_data($postdata),true);	
		//echo '<pre>';
		//echo '<br>';
		print_r($bill_response);
		//SaveErrorLog(json_encode($bill_response));	
		$show_preference_modal=0;
		if($bill_response['status_cd']==1){
				if(isset($bill_response['alert']) && $bill_response['alert']!=''){	
				 $alerts = array($bill_response['alert']);
				  return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}
				else{					
					$this->session->set("sess_otp","");
					//$response     = decryptBySymmetricKey($bill_response['Data'],$DecryptedSek);
					//$successData  = json_decode($response,true);
					//$this->TransactionModel->add_einvmaster_info($voucher_txn_id,$successData);					
				    //return json_encode(['show_preference_modal'=>$show_preference_modal,'AckNo'=>$successData['AckNo'],'Irn'=>$successData['Irn'],'status' => true,'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload,'errors'=>'','filename'=>'eway-invoice.json']);
				}				
				
			}
			else{
			
			   if(isset($bill_response['error'])){	
			     $errors_list = array();
				 if($bill_response['error']){					
					  if(strtolower($bill_response['error']['message'])=='please select the preference')
							$show_preference_modal=1; // first run preference api  */
					$errors_list[] = $bill_response['error']['message'].'('.$bill_response['error']['error_cd'].')';  
				  }				  
				  if($errors_list)
					  $alerts = $errors_list;
				   else
				    $alerts = array('error in request');
			  }			  
			   return json_encode(['status' => false, 'message' => 'Validation Error','show_preference_modal'=>$show_preference_modal, 'errors' => $alerts]);
			}
		
		
		
		}	
	  }
		else{		
		  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['authtoken generation error']]);
		 }
	}	
	  }
	catch(Exception $e) {
	  echo 'Message: ' .$e->getMessage();
	}	  
	  
  }
  
}