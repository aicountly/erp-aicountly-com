<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\GstexportModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\externaldb;
use App\Models\Admin\GstrReportModel;
use App\Models\Admin\VouchersModel;

class Etaxes extends BaseController{

  function __construct(){  
    helper(['form', 'url','text','einvoice']);
	$this->externaldb        = new externaldb();
    $this->CommonModel       = new CommonModel();		
	$this->GstexportModel    = new GstexportModel();
	$this->auth_session      = new auth_session();	
	$this->GstrReportModel   = new GstrReportModel();
	$this->VouchersModel     = new VouchersModel();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();	
    $this->auth_session->role_restrict('CS');
    $this->base_url       =  base_url().'/'.getenv('AdminPath');
    $this->folder_path    =  getenv('AdminPath');
	
    $this->session    	  =  \Config\Services::session();
    $this->company_id = $this->session->get('ses_company_id');
    $this->bo_id      = $this->session->get('ses_boid');
    $this->fy_id      = $this->session->get('ses_comp_fy_id');
	$this->aicountly_db   =  $this->externaldb->aicountly_db();
    $this->ses_comp_fy_id =  $this->session->get('ses_comp_fy_id');
	$this->EwaySubscriptionKey='AL2h3J2u9M2c1e6u9c';
	$this->EinvoiceSubscriptionKey='AL2h3J2u9M2c1e6u9c';
	$this->isSandbox = env('EWAY_SANDBOX');// 0 means production, 1 means sandbox
	$this->app_key   = generateRandomStringVal();
	
	$this->table_lists    =  array("4A"=>"B2B REGULAR",
		                       "4B"=>"B2B REVERSE CHARGE",
		                       "5"=>"B2CL (LARGE)",
							   "6A"=>"EXPORTS",
							   "6B"=>"SUPPLIES MADE TO SEZ UNIT",
                               "6C"=>"DEEMED EXPORT (DE)",
							   "7"=>"B2CS",
							   "8"=>"NIL RATED, EXEMPTED AND NON GST",
							   "9A,9C"=>"AMENDED INVOICES",
							   "9B"=>"CREDIT NOTES",//"9B"=>"CREDIT / DEBIT NOTES",
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
							   "9B"=>"CREDIT NOTES REGISTERED",// "9B"=>"CREDIT / DEBIT NOTES REGISTERED",
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
  
  public function generate_einvoice(){
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($boid);
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(5);
	
	$this->Gstin  = $branch_gstin_info['comp_gstin'];
	if($this->isSandbox==1){
	$this->Gstin='07AGAPA5363L002';
	$this->username='AL001';
	$this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){
	$this->Gstin='04AAZCA3730B1ZW';
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
		if(isset($accesstoken_info) && $accesstoken_info['Status']==1){
			$accesstoken_resp = $accesstoken_info['Data'];
			$authtoken        = $accesstoken_resp['AuthToken'];
		
			$EncryptedSek     = $accesstoken_resp['Sek'];
			$options          = 0;
			$encryption       = $EncryptedSek;
			$ciphering        = "AES-256-ECB";
			$decryption_key   = base64_decode($AppKey);
			$decryption_iv    = ''; 
			$decryption       = openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv); 
			$DecryptedSek     = base64_encode($decryption); 
			
			$response_data    =  array();  
			$errors_counter   =  0;
			$bo_state_code    =  $this->session->get('ses_bostecd');  
			$vch_txn_id       =  $this->request->getVar('vch_txn_id');		 
			$response         =  json_decode($this->GstexportModel->get_einvoice_voucher_info($vch_txn_id),true);		           
		    $ItemListings     =  array();			
			if($response['data']){
			  foreach($response['data'] as $row){
				$voucher_txn_id   = $row['voucher_txn_id'];
				$voucher_type_id  = $row['voucher_type_id']; 
                $voucher_info     = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);		
				$party_trnsction  = $this->TransactionModel->get_party_transaction($voucher_txn_id);
				$party_id 	  	  = $party_trnsction['acc_id'];
				$party_gst_info   = $this->TransactionModel->party_gst_info($party_id);
				$narration 		  = $party_trnsction['acc_txn_narr'];
				$item_trnsctions  = $this->TransactionModel->get_item_transactions($voucher_txn_id);
				
				
				$sundry_transactions = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);
				$voucher_series   = $voucher_info['comp_vch_series_id'];
				$voucher_no 	  = $voucher_info['comp_vch_no'];
				$voucher_type 	  = $voucher_info['voucher_type_id'];
				$mc_centr_id 	  = $voucher_info['mat_cent_id'];
				$voucher_date 	  = date('d/m/Y', strtotime($voucher_info['voucher_date']));
				$billfrm_data     = $this->TransactionModel->billfrm_info();
				$bo_gstin_type    = $this->session->get('bo_gstin_type');
				$bo_id            = $this->session->get('ses_boid');	  
				$get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
				$get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
				$party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
				$party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
				if($get_account_info){
				  $party_phone = $get_account_info['acc_mobile'];
				  $party_name  = $get_account_info['acc_name'];
				  $party_email = $get_account_info['acc_email'];
				}	
				if($party_adrs_info){
				  $party_add1    = $party_adrs_info['acc_add1'];
				  $party_add2    = $party_adrs_info['acc_add2'];
				  $party_city    = $party_adrs_info['acc_city'];
				  $party_pin     = $party_adrs_info['acc_pin'];
				  $party_state   = $party_adrs_info['acc_state'];
				  $party_country = $party_adrs_info['acc_country'];
				  $party_state_info = $this->TransactionModel->get_state_info($party_country,$party_state);
				 if($party_state_info)
				  $party_state_code = sprintf( '%02d', $party_state_info['state_code']);
				 else
				  $party_state_code = 0;
			
				}else{
					$party_state_code = 0;
				}   
				
				$comp_ro_address    = $this->CommonModel->get_company_address_info($this->company_id,'1');
				$get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
				$gstroutsup_info    = $this->TransactionModel->gstroutsup_info($voucher_txn_id);		
				
				 if(isset($get_comp_taxt_info['comp_gstin']))
				  $comp_gst = $get_comp_taxt_info['comp_gstin'];
				 else
				  $comp_gst ='';
			  if(isset($get_comp_taxt_info['gstin_legal_name']))
				  $comp_legal_name = $get_comp_taxt_info['gstin_legal_name'];
				 else
				  $comp_legal_name ='';
			  if(isset($get_comp_taxt_info['gstin_trade_name']))
				  $comp_trade_name = $get_comp_taxt_info['gstin_trade_name'];
				 else
				  $comp_trade_name ='';
				
				if($party_gst_info){
				  $party_gst        = $party_gst_info['acc_gstin'];
				  $party_legal_name = $party_gst_info['acc_legal_name'];
				  $party_trade_name = $party_gst_info['acc_trade_name'];
			  }else{
				  $party_gst        = '';
				  $party_legal_name = '';
				  $party_trade_name = '';
			   } 
			   
				if($gstroutsup_info){
					$outsup_pos         = $gstroutsup_info['outsup_pos'];
					$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
					$outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];
					$outsup_inv_type    = $gstroutsup_info['outsup_inv_type'];	
					$outsup_eco         = $gstroutsup_info['outsup_eco'];
					if($outsup_eco >0){
					  $EcmGstin_info   = $this->TransactionModel->party_gst_info($party_id);
					   if($EcmGstin_info){
					     $EcmGstin        = ($EcmGstin_info['acc_gstin']!='')?$EcmGstin_info['acc_gstin']:NULL;		
					    }
					 }
					 else
						$EcmGstin        =NULL;
					
					$pos_state_info     = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));		  
				if($pos_state_info){
					$state_name         = $pos_state_info['state_name']; 
					$state_code         = $pos_state_info['state_code'];
					$statecode          = sprintf( '%02d', $state_code);
					}
				   $outsup_rev_chg_label = ($outsup_rev_chg==1)?'Y':'N';						
				 }
				else{
					$outsup_rev_chg_label ='N';
					$EcmGstin        ='';
					$outsup_pos = $outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode=$outsup_inv_type='';
				  }				  
				  $IgstOnIntra ='N';
				  /* if($bo_state_code==$statecode)
					  $IgstOnIntra ='N';
				  else
					 $IgstOnIntra ='Y'; */				  
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
			  
			      $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
				 	  
				  $shipto_addr1        = '';
				  $shipto_addr2        = '';
				  $shipto_place        = '';
				  $shipto_pin          = '';
				  $shipto_state_code   = '';		  
				  $dispfrm_addr1       = '';
				  $dispfrm_addr2       = '';
				  $dispfrm_place       = '';
				  $dispfrm_pin         = '';
				  $dispfrm_state_code  = '';		  
				  $supply_type         = "1";
                  $transactionType     = 1;	
				  $ewb_txn_type        = 1;
				  $shiptoAddr1         = '';
				  $shiptoAddr2         = '';
				  $shiptoPlace         = '';
				  $shiptoPincode       = '';
				  $actshiptoStateCode  = '';
				  $shiptoStateCode     = '';
				 if($get_ewbmstreqn_data){
					$ewb_txn_type        = $get_ewbmstreqn_data['ewb_txn_type']; 
					// if ewb_txn_type is 1 or 2 then fetch from address details from mc at the time of voucher save
					// and rest as per ewb_txn_type type value					
					if($ewb_txn_type==1 || $ewb_txn_type==2){
						$mc_info          = $this->TransactionModel->mc_info($mc_centr_id);						
						$fromAddr1        = $mc_info['mat_cent_add1'];
						$fromAddr2        = $mc_info['mat_cent_add2'];
						$fromPlace        = $mc_info['mat_cent_city'];
						$fromPincode      = $mc_info['mat_cent_pin'];
						$actFromStateCode = $mc_info['state_code'];
						$fromStateCode    = $mc_info['state_code'];
					}
					if($ewb_txn_type==3 || $ewb_txn_type==4){ 
					   $gstdispfrm_info     = $get_ewbmstreqn_data['gstdispfrm_info']; 
				       if($gstdispfrm_info){
						  $fromAddr1        = $gstdispfrm_info['dispfrm_addr1'];
						  $fromAddr2        = $gstdispfrm_info['dispfrm_addr2'];
						  $fromPlace        = $gstdispfrm_info['dispfrm_place'];
						  $fromPincode      = $gstdispfrm_info['dispfrm_pin'];
						  $actFromStateCode = sprintf( '%02d',$gstdispfrm_info['dispfrm_state_code']);
						  $fromStateCode    = sprintf( '%02d',$gstdispfrm_info['dispfrm_state_code']);
					    }
						$gstshipton_info     = $get_ewbmstreqn_data['gstshipton_info']; 
				       if($gstshipton_info){
						  $shiptoAddr1        = $gstshipton_info['dispfrm_addr1'];
						  $shiptoAddr2        = $gstshipton_info['dispfrm_addr2'];
						  $shiptoPlace        = $gstshipton_info['dispfrm_place'];
						  $shiptoPincode      = $gstshipton_info['dispfrm_pin'];
						  $actshiptoStateCode = sprintf( '%02d',$gstshipton_info['dispfrm_state_code']);
						  $shiptoStateCode    = sprintf( '%02d',$gstshipton_info['dispfrm_state_code']);
					    }
					}
				 }
			    $taxsummary        = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,'');
				$cgstValue		   = 0;
				$sgstValue 		   = 0;
				$igstValue 		   = 0;
				$cessValue 		   = 0;
				$cessNonAdvolValue = 0;
				$totInvValue       = 0;
				$bsdTotal          = 0;				
				$TotInvValFc       = 0;
				$bsdTotInvValFc    = 0;
								
				if($sundry_transactions){
				  foreach($sundry_transactions as $bsdrow){
					$bsdTotInvValFc = $bsdTotInvValFc+$bsdrow['billsundry_amountfc'];  
				  }
				}
				if($taxsummary){
				  foreach($taxsummary as $prow){ 
					  $totInvValue = $totInvValue+($prow['tax_amt']+$prow['total_tax']);
					  $TotInvValFc = $TotInvValFc+$prow['tax_amt_fcy'];					  
					  if($bo_state_code==$statecode){
							$cgstValue         = $cgstValue+$prow['cgst']; 
							$sgstValue         = $sgstValue+$prow['sgst']; 
							$igstValue         = 0; 
					   }else{
							$cgstValue         = 0; 
							$sgstValue         = 0; 
							$igstValue         = $igstValue+$prow['igst'];  
					   }
					  
					 $cessValue         = $cessValue+$prow['cess']; 
					 $cessNonAdvolValue = 0; 			 
				  }	
				}
				$totAssVal=0;
				if($item_trnsctions){
					$itmcntr=1;
				  foreach($item_trnsctions as $itmrow){
					  $totAssVal = $totAssVal+$itmrow['item_amount'];
					  if($itmrow['supply_type']=='2'){
						$IsServc='Y';  
					  }else
						$IsServc='N';	
					  $tax_rate = $itmrow['tax_rate'];
					  $IgstAmt=$CgstAmt=$SgstAmt=$totalTaxAmnt=0;
					  if($bo_state_code==$statecode){
						$CgstAmt = parseAmount(($itmrow['item_amount']*($itmrow['tax_rate']/2))/100);
						$SgstAmt = parseAmount(($itmrow['item_amount']*($itmrow['tax_rate']/2))/100);
					    $totalTaxAmnt = ($CgstAmt+$SgstAmt);
					  }else{
						$IgstAmt = parseAmount(($itmrow['item_amount']*$itmrow['tax_rate'])/100);
						$totalTaxAmnt = $IgstAmt; 	
					  }
					  
					  $ItemListings[]=array(
					                   'SlNo'=>(string)$itmcntr,'PrdDesc'=>$itmrow['item_name'],'IsServc'=>$IsServc,'HsnCd'=>$itmrow['item_hsn'],
									   'Qty'=>$itmrow['item_qty'],'Unit'=>$itmrow['item_unit'],'UnitPrice'=>parseAmountPrice($itmrow['item_price'],3),'TotAmt'=>parseAmount($itmrow['item_amount']),
									   'Discount'=>0,'PreTaxVal'=>1,'AssAmt'=>parseAmount($itmrow['item_amount']),'GstRt'=>$itmrow['tax_rate'],'IgstAmt'=>parseAmount($IgstAmt),
									   'CgstAmt'=>parseAmount($CgstAmt),'SgstAmt'=>parseAmount($SgstAmt),'CesRt'=>0,'CesAmt'=>0,'CesNonAdvlAmt'=>0,
									   'StateCesRt'=>0,'StateCesAmt'=>0,'StateCesNonAdvlAmt'=>0,'OthChrg'=>0,
									   'TotItemVal'=>parseAmount($itmrow['item_amount']+$totalTaxAmnt),'OrdLineRef'=>$itmrow['item_sku'],'OrgCntry'=>'IN','PrdSlNo'=>$itmrow['item_sku']
									   
									   );
						$itmcntr++;				
				       }
				   }				  
				
			    }
			}		
			
			$TranDtls         = array("TaxSch"=>"GST","SupTyp"=>$outsup_inv_type,"RegRev"=>$outsup_rev_chg_label,"EcmGstin"=>$EcmGstin,"IgstOnIntra"=>$IgstOnIntra);
			$DocDtls          = array("Typ"=>$docType,"No"=>$outsup_bill_ref_no,"Dt"=>$voucher_date);
			$SellerDtls       = array("Gstin"=>$comp_gst,"LglNm"=>$comp_legal_name,"TrdNm"=>$comp_trade_name,"Addr1"=>$fromAddr1,"Addr2"=>$fromAddr2,"Loc"=>$fromPlace,"Pin"=>$fromPincode,"Stcd"=>$fromStateCode);
			$BuyerDtls        = array("Gstin"=>$party_gst,"LglNm"=>$party_legal_name,"TrdNm"=>$party_trade_name,"Pos"=>$outsup_pos,"Addr1"=>$party_add1,"Addr2"=>$party_add2,"Loc"=>$party_city,"Pin"=>$party_pin,"Stcd"=>$party_state_code);
			if($ewb_txn_type==4){
			$DispDtls         = array("Nm"=>"","Addr1"=>$fromAddr1,"Addr2"=>$fromAddr2,"Loc"=>$fromPlace,"Pin"=>$fromPincode,"Stcd"=>$fromStateCode);
			$ShipDtls         = array("Gstin"=>"","LglNm"=>"","TrdNm"=>"","Addr1"=>"","Addr2"=>"","Loc"=>"","Pin"=>"","Stcd"=>"");
			}
			$ValDtls          = array("AssVal"=>parseAmount($totAssVal),"CgstVal"=>parseAmount($cgstValue),"SgstVal"=>parseAmount($sgstValue),"IgstVal"=>parseAmount($igstValue),"CesVal"=>parseAmount($cessValue),"StCesVal"=>0,"Discount"=>0,"OthChrg"=>0,"RndOffAmt"=>0,"TotInvVal"=>parseAmount($totInvValue),"TotInvValFc"=>parseAmount($TotInvValFc+$bsdTotInvValFc));
			$PayDtls          = array("Nm"=>"","AccDet"=>"","Mode"=>"","FinInsBr"=>"","PayTerm"=>"","PayInstr"=>"","CrTrn"=>"","DirDr"=>"","CrDay"=>"","PaidAmt"=>"","PaymtDue"=>"");
			if($ewb_txn_type==4){
			$response_data    = array("Version"=>"1.1","TranDtls"=>$TranDtls,"DocDtls"=>$DocDtls,"SellerDtls"=>$SellerDtls,"BuyerDtls"=>$BuyerDtls,"DispDtls"=>$DispDtls,"ShipDtls"=>$ShipDtls,"ItemList"=>$ItemListings,"ValDtls"=>$ValDtls);
			}
			else{
			$response_data    = array("Version"=>"1.1","TranDtls"=>$TranDtls,"DocDtls"=>$DocDtls,"SellerDtls"=>$SellerDtls,"BuyerDtls"=>$BuyerDtls,"ItemList"=>$ItemListings,"ValDtls"=>$ValDtls);
			}
			
			$RequestPayload    ='{
    "Version": "1.1",
    "TranDtls": {
        "TaxSch": "GST",
        "SupTyp": "B2B",
        "RegRev": "Y",
        "EcmGstin": null,
        "IgstOnIntra": "N"
    },
    "DocDtls": {
        "Typ": "INV",
        "No": "test010012025",
        "Dt": "10/01/2025"
    },
    "SellerDtls": {
        "Gstin": "07AGAPA5363L002",
        "LglNm": "NIC company pvt ltd",
        "TrdNm": "NIC Industries",
        "Addr1": "5th block, kuvempu layout",
        "Addr2": "kuvempu layout",
        "Loc": "GANDHINAGAR",
        "Pin": 110055,
        "Stcd": "07",
        "Ph": "9000000000",
        "Em": "abc@gmail.com"
    },
    "BuyerDtls": {
        "Gstin": "29AWGPV7107B1Z1",
        "LglNm": "XYZ company pvt ltd",
        "TrdNm": "XYZ Industries",
        "Pos": "12",
        "Addr1": "7th block, kuvempu layout",
        "Addr2": "kuvempu layout",
        "Loc": "GANDHINAGAR",
        "Pin": 562160,
        "Stcd": "29",
        "Ph": "91111111111",
        "Em": "xyz@yahoo.com"
    },    
    "ItemList": [
        {
            "SlNo": "1",
            "PrdDesc": "Rice",
            "IsServc": "N",
            "HsnCd": "1001",
            "Barcde": "123456",
            "Qty": 100.345,
            "FreeQty": 10,
            "Unit": "BAG",
            "UnitPrice": 99.545,
            "TotAmt": 9988.84,
            "Discount": 10,
            "PreTaxVal": 1,
            "AssAmt": 9978.84,
            "GstRt": 12,
            "IgstAmt": 1197.46,
            "CgstAmt": 0,
            "SgstAmt": 0,
            "CesRt": 5,
            "CesAmt": 498.94,
            "CesNonAdvlAmt": 10,
            "StateCesRt": 12,
            "StateCesAmt": 1197.46,
            "StateCesNonAdvlAmt": 5,
            "OthChrg": 10,
            "TotItemVal": 12897.7,
            "OrdLineRef": "3256",
            "OrgCntry": "AG",
            "PrdSlNo": "12345"
        }
    ],
    "ValDtls": {
        "AssVal": 9978.84,
        "CgstVal": 0,
        "SgstVal": 0,
        "IgstVal": 1197.46,
        "CesVal": 508.94,
        "StCesVal": 1202.46,
        "Discount": 10,
        "OthChrg": 20,
        "RndOffAmt": 0.3,
        "TotInvVal": 12908,
        "TotInvValFc": 12897.7
    }
}';
			
		  $RequestPayload       = json_encode($response_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);		
         SaveErrorLog($RequestPayload);
		 //   echo '<br>';
		  $Base64RequestPayload = base64_encode($RequestPayload);
			$reqpayload           = encryptBySymmetricKey($Base64RequestPayload, $DecryptedSek);			
		    //Generate E-invoice irn
			$postdata             = array("UserName"=>$this->username,"SubscriptionKey"=>$this->EwaySubscriptionKey,"Gstin"=>$this->Gstin,'is_sandbox'=>$this->isSandbox);
			//echo json_encode($postdata);
			//echo 'authtoken : '.$authtoken;
			//echo '<br>';
			//echo 'reqpayload: '.$reqpayload;
			//echo '<br>';
			$bill_response        = json_decode(generate_einvoice_irn($reqpayload,$authtoken,$postdata),true);
			SaveErrorLog(json_encode($bill_response));
			if($bill_response['Status']==1){
				if(isset($bill_response['alert']) && $bill_response['alert']!=''){	
				 $alerts = array($bill_response['alert']);
				  return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}
				else{					
					$response     = decryptBySymmetricKey($bill_response['Data'],$DecryptedSek);
					$successData  = json_decode($response,true);
					$this->TransactionModel->add_einvmaster_info($voucher_txn_id,$successData);					
				    return json_encode(['AckNo'=>$successData['AckNo'],'Irn'=>$successData['Irn'],'status' => true,'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload,'errors'=>'','filename'=>'eway-invoice.json']);
				}				
				
			}
			else{
			   if(isset($bill_response['ErrorDetails'])){	
			     $errors_list = array();
				 if($bill_response['ErrorDetails']){
					 foreach($bill_response['ErrorDetails'] as $errorCodes){
					    $errors_list[] = $errorCodes['ErrorMessage'].'('.$errorCodes['ErrorCode'].')';
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
  
  public function generate_eway(){	  
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
	//$this->Gstin='04AAZCA3730B1ZW';
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
		$accesstoken_info = json_decode(get_eway_accesstoken_string($postdata),true);
		
		if($accesstoken_info['status']==1){
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
	"docNo": "1226122024-2",
	"docDate": "26/12/2024",
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
		$from_date        = $this->request->getVar('fromdate');
		$to_date          = $this->request->getVar('todate');		
		$from_date        = date('Y-m-d', strtotime($from_date));
        $to_date          = date('Y-m-d', strtotime($to_date));   
		$response         = json_decode($this->GstexportModel->load_eway_listing(1, -1, $from_date, $to_date, 0,''),true);
		
		if($response['data']){
			foreach($response['data'] as $row){
				$voucher_txn_id   = $row['voucher_txn_id'];
				$voucher_type_id  = $row['voucher_type_id']; 
                $voucher_info     = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);		
				$party_trnsction  = $this->TransactionModel->get_party_transaction($voucher_txn_id);
				$party_id 	  	  = $party_trnsction['acc_id'];
				$party_gst_info   = $this->TransactionModel->party_gst_info($party_id);
				$narration 		  = $party_trnsction['acc_txn_narr'];
				$item_trnsctions  = $this->TransactionModel->get_item_transactions($voucher_txn_id);
				$sundry_transactions = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);
				$voucher_series   = $voucher_info['comp_vch_series_id'];
				$voucher_no 	  = $voucher_info['comp_vch_no'];
				$voucher_type 	  = $voucher_info['voucher_type_id'];
				
				$voucher_date 	  = date('d/m/Y', strtotime($voucher_info['voucher_date']));
				$ewbmstreqn_data  = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
				$billfrm_data     = $this->TransactionModel->billfrm_info();
				$bo_gstin_type    = $this->session->get('bo_gstin_type');
				$bo_id            = $this->session->get('ses_boid');	  
				$get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
				$get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
				$party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
				$party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
				if($get_account_info){
				  $party_phone = $get_account_info['acc_mobile'];
				  $party_name  = $get_account_info['acc_name'];
				  $party_email = $get_account_info['acc_email'];
				}	
				if($party_adrs_info){
				  $party_add1    = $party_adrs_info['acc_add1'];
				  $party_add2    = $party_adrs_info['acc_add2'];
				  $party_city    = $party_adrs_info['acc_city'];
				  $party_pin     = $party_adrs_info['acc_pin'];
				  $party_state   = $party_adrs_info['acc_state'];
				  $party_country = $party_adrs_info['acc_country'];
				  $party_state_info = $this->TransactionModel->get_state_info($party_country,$party_state);
				 if($party_state_info)
				  $party_state_code = sprintf( '%02d', $party_state_info['state_code']);
				 else
				  $party_state_code = 0;
			
				}else{
					$party_state_code = 0;
				}   
		
				$comp_ro_address    = $this->CommonModel->get_company_address_info($this->company_id,'1');
				$get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
				$gstroutsup_info    = $this->TransactionModel->gstroutsup_info($voucher_txn_id);		
				if($gstroutsup_info){
					$outsup_pos         = $gstroutsup_info['outsup_pos'];
					$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
					$outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];			
					$pos_state_info     = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));		  
				if($pos_state_info){
					$state_name = $pos_state_info['state_name']; 
					$state_code = $pos_state_info['state_code'];
					$statecode  = sprintf( '%02d', $state_code);
					}		
				}
				else{
					$outsup_pos = $outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
				  }
				  
				  if($bo_state_code==$statecode)
					  $IgstOnIntra ='N';
				  else
					 $IgstOnIntra ='Y';
		 
				  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
				  $shipto_addr1        = '';
				  $shipto_addr2        = '';
				  $shipto_place        = '';
				  $shipto_pin          = '';
				  $shipto_state_code   = '';		  
				  $dispfrm_addr1       = '';
				  $dispfrm_addr2       = '';
				  $dispfrm_place       = '';
				  $dispfrm_pin         = '';
				  $dispfrm_state_code  = '';		  
				  $supply_type         ="1";
                  $transactionType        = 1;				  
				 if($get_ewbmstreqn_data){
					$supply_type         = $get_ewbmstreqn_data['ewb_supply_type'];
					$transactionType     = $get_ewbmstreqn_data['ewb_txn_type']; 
				   $gstshipton_info      = $get_ewbmstreqn_data['gstshipton_info']; 
				   if($gstshipton_info){
					  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
					  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
					  $shipto_place      = $gstshipton_info['shipto_place'];
					  $shipto_pin        = $gstshipton_info['shipto_pin'];
					  $shipto_state_code = sprintf( '%02d',$gstshipton_info['shipto_state_code']);
					}		  
				   $gstdispfrm_info       = $get_ewbmstreqn_data['gstdispfrm_info']; 
				   if($gstdispfrm_info){
					  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
					  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
					  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
					  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
					  $dispfrm_state_code = sprintf( '%02d',$gstdispfrm_info['dispfrm_state_code']);
					}
				}	 
		
			  $gsttpt_id               = $this->TransactionModel->transport_single_info($voucher_txn_id);
			  $transporter_info        = $this->TransactionModel->transporter_info($gsttpt_id,$voucher_txn_id);	 
			  $transport_name          = '';	  
			  $transport_veh_no        = '';
			  $transport_veh_type      = '';
			  $transport_gstin         = ''; 
			  $transport_enrollment_id = '';
	    
			  if($transporter_info){
				$transport_name          = $transporter_info['gsttpt_name'];	  
				$transport_veh_no        = $transporter_info['trans_veh_no'];
				$transport_veh_type      = $transporter_info['trans_veh_type'];
				$transport_gstin         = $transporter_info['gsttpt_gstin'];
				$transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
			  }	 
	   
			  if(isset($get_comp_taxt_info['comp_gstin']))
				  $comp_gst = $get_comp_taxt_info['comp_gstin'];
				 else
				  $comp_gst ='';
			  if(isset($get_comp_taxt_info['gstin_legal_name']))
				  $comp_legal_name = $get_comp_taxt_info['gstin_legal_name'];
				 else
				  $comp_legal_name ='';
			  if(isset($get_comp_taxt_info['gstin_trade_name']))
				  $comp_trade_name = $get_comp_taxt_info['gstin_trade_name'];
				 else
				  $comp_trade_name ='';
	   
			   $company_info      = $this->CommonModel->get_company_info($this->company_id);	  
			   $comp_email        = $company_info['corp_email'];
			   $comp_name         = $company_info['comp_name'];
			   $comp_phone        = $company_info['corp_tel'];
			   $comp_state_info   = $this->TransactionModel->get_state_info($comp_ro_address['comp_country'],$comp_ro_address['comp_state']);
			   $comp_country_info = $this->TransactionModel->get_country_info($comp_ro_address['comp_country']);
			  if($comp_state_info)
				$comp_state_code  = sprintf( '%02d', $comp_state_info['state_code']);
			  else
				$comp_state_code  = 0;  
			   
			  if(isset($comp_ro_address['comp_addr1']))
				$company_adrs1    = $comp_ro_address['comp_addr1'];
			  else
				$company_adrs1    = '';  
			
			  if(isset($comp_ro_address['comp_addr2']))
			   $company_adrs2     = $comp_ro_address['comp_addr2'];
			  else
				$company_adrs2    = ''; 
			  if(isset($comp_ro_address['comp_pin']))
			   $company_pin       = $comp_ro_address['comp_pin'];
			  else
			   $company_pin       = ''; 
			 
			  if(isset($comp_ro_address['comp_city']))
			   $company_city      = $comp_ro_address['comp_city'];
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
					  
			  if($party_gst_info){
				  $party_gst        = $party_gst_info['acc_gstin'];
				  $party_legal_name = $party_gst_info['acc_legal_name'];
				  $party_trade_name = $party_gst_info['acc_trade_name'];
			  }else{
				  $party_gst        = '';
				  $party_legal_name = '';
				  $party_trade_name = '';
			   } 
				$taxsummary = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,'');
				$cgstValue		  = 0;
				$sgstValue 		  = 0;
				$igstValue 		  = 0;
				$cessValue 		  = 0;
				$cessNonAdvolValue= 0;
				$totInvValue      = 0;
				$bsdTotal         = 0;
				
				if($taxsummary){
				  foreach($taxsummary as $prow){
					   if($bo_state_code==$statecode){
							$cgstValue         = $cgstValue+$prow['cgst']; 
							$sgstValue         = $sgstValue+$prow['sgst']; 
							$igstValue         = 0; 
					   }else{
							$cgstValue         = 0; 
							$sgstValue         = 0; 
							$igstValue         = $igstValue+$prow['igst'];  
					   }
					  
					 $cessValue         = $cessValue+$prow['cess']; 
					 $cessNonAdvolValue = 0; 			 
				  }	
				}
				
				$itemList=array();
				if($item_trnsctions){
				  foreach($item_trnsctions as $itmrow){
					$totInvValue = $totInvValue+$itmrow['item_amount']; 					  
					$cess        = $itmrow['cess_rate']; 
					if($bo_state_code==$statecode){
						$igst    = 0;
						$cgst    = parseAmount($itmrow['igst_rate']/2);
						$sgst    = parseAmount($itmrow['igst_rate']/2);
						
					}else{
						$igst    = parseAmount($itmrow['igst_rate']);
						$cgst    = 0;
						$sgst    = 0;
					  }	
					$tax_amt = $itmrow['tax_amt'];
					$itemList[]=array("productName"=>$itmrow["item_name"],"productDesc"=>$itmrow["description"],"hsnCode"=>$itmrow["tax_hsn_sac"],"quantity"=>parseAmount($itmrow["item_qty"]),
									  "qtyUnit"=>$itmrow["item_unit"],"cgstRate"=>parseAmount($cgst),"sgstRate"=>parseAmount($sgst),"igstRate"=>parseAmount($igst),"cessRate"=>parseAmount($cess),
									  "cessNonadvol"=>parseAmount("0"),"taxableAmount"=>parseAmount($tax_amt)
									  );	
					 }
				}
				
				if($sundry_transactions){
				  foreach($sundry_transactions as $bsdrow){
					$bsdTotal=$bsdTotal+$bsdrow['billsundry_amount'];  
				  }
				}
				
				$gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
				if($gsttpt_id){
					$transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);			
					$transporterId    = $transporter_info['gsttpt_gstin'];
					$transporterName  = $transporter_info['gsttpt_name'];
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
						   "fromGstin"=>$comp_gst,"fromTrdName"=>$comp_trade_name,"fromAddr1"=>$company_adrs1,
						   "fromAddr2"=>$company_adrs2,"fromPlace"=>$company_city,"fromPincode"=>(integer)$company_pin,
						   "actFromStateCode"=>$comp_state_code,"fromStateCode"=>$comp_state_code,
						   "toGstin"=>$party_gst,"toTrdName"=>$party_trade_name,"toAddr1"=>$party_add1,"toAddr1"=>$party_add2,
						   "toPlace"=>$party_city,"toPincode"=>(integer)$party_pin,"actToStateCode"=>$party_state_code,"toStateCode"=>$party_state_code,
						   "transactionType"=>$transactionType,"otherValue"=>"","totalValue"=>parseAmount($totInvValue+$bsdTotal),
						   "cgstValue"=>parseAmount($cgstValue),"sgstValue"=>parseAmount($sgstValue),"igstValue"=>parseAmount($igstValue),"cessValue"=>parseAmount($cessValue),
						   "cessNonAdvolValue"=>parseAmount($cessNonAdvolValue),"totInvValue"=>parseAmount($totInvValue),"transporterId"=>$transporterId,"transporterName"=>$transporterName,
						   "transDocNo"=>$transDocNo,"transMode"=>$transMode,"transDistance"=>(integer)$transDistance,"transDocDate"=>$transDocDate,
						   "vehicleNo"=>$vehicleNo,"vehicleType"=>$vehicleType,"itemList"=>$itemList
                          ); 
	 			
				if($transporterId==''){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Transport Document Number is required.";
				}
				if(strtotime($voucher_date) > strtotime(date('Y-m-d'))){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - The Document Date should be less than or equal to current date.";
				}
				if($transport_name==''){
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
		    $RequestPayload =  json_encode($response_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);		
		  	$Base64RequestPayload = base64_encode(  $RequestPayload  );
			$reqpayload = encryptBySymmetricKey($Base64RequestPayload, $DecryptedSek);
		
		    //Generate E-way bill
			$postdata       = array("action"=>"GENEWAYBILL","SubscriptionKey"=>$this->EwaySubscriptionKey,"Gstin"=>$this->Gstin);
			$bill_response  = json_decode(generate_eway_bill($reqpayload,$authtoken,$postdata),true);
			/* echo '<pre>';
			print_r($bill_response);
			die(); */
			if($bill_response['status']==1){
				if(isset($bill_response['alert']) && $bill_response['alert']!=''){	
				 $alerts = array($bill_response['alert']);
				return json_encode(['status' => true, 'message' => 'Validation Error', 'errors' => $alerts]);
				}else
				 return json_encode(['status' => true, 'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload,'errors'=>'','filename'=>'eway-invoice.json']);
				
				
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
						 $errors_list[] = Eway_errors_codes($errorCode);
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
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['authtoken generation error']]);
		 }	    
      }
  }
  public function generate_einvoice_json(){
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	$boid = $this->session->get('ses_boid');
	$branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($boid);
	$credntial_gstin_info = $this->TransactionModel->get_credntial_gstin_info(5);
	$this->Gstin  = $branch_gstin_info['comp_gstin'];
	if($this->isSandbox==1){
	$this->Gstin='07AGAPA5363L002';
	$this->username='AL001';
	$this->password='Alankit@123';  
	}	
	if($this->isSandbox==0){
	$this->Gstin='04AAZCA3730B1ZW';
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
		    $response_data    =  array();  
			$errors_counter   =  0;
			$bo_state_code    =  $this->session->get('ses_bostecd');  
			$vch_txn_id       =  $this->request->getVar('vch_txn_id');		 
			$response         =  json_decode($this->GstexportModel->get_einvoice_voucher_info($vch_txn_id),true);		           
		    $ItemListings     =  array();			
			if($response['data']){
			  foreach($response['data'] as $row){
				$voucher_txn_id   = $row['voucher_txn_id'];
				$voucher_type_id  = $row['voucher_type_id']; 
                $voucher_info     = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);		
				$party_trnsction  = $this->TransactionModel->get_party_transaction($voucher_txn_id);
				$party_id 	  	  = $party_trnsction['acc_id'];
				$party_gst_info   = $this->TransactionModel->party_gst_info($party_id);
				$narration 		  = $party_trnsction['acc_txn_narr'];
				$item_trnsctions  = $this->TransactionModel->get_item_transactions($voucher_txn_id);
				
				
				$sundry_transactions = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);
				$voucher_series   = $voucher_info['comp_vch_series_id'];
				$voucher_no 	  = $voucher_info['comp_vch_no'];
				$voucher_type 	  = $voucher_info['voucher_type_id'];
				$mc_centr_id 	  = $voucher_info['mat_cent_id'];
				
				$voucher_date 	  = date('d/m/Y', strtotime($voucher_info['voucher_date']));
				$billfrm_data     = $this->TransactionModel->billfrm_info();
				$bo_gstin_type    = $this->session->get('bo_gstin_type');
				$bo_id            = $this->session->get('ses_boid');	  
				$get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
				$get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
				$party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
				$party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
				if($get_account_info){
				  $party_phone = $get_account_info['acc_mobile'];
				  $party_name  = $get_account_info['acc_name'];
				  $party_email = $get_account_info['acc_email'];
				}	
				if($party_adrs_info){
				  $party_add1    = $party_adrs_info['acc_add1'];
				  $party_add2    = $party_adrs_info['acc_add2'];
				  $party_city    = $party_adrs_info['acc_city'];
				  $party_pin     = $party_adrs_info['acc_pin'];
				  $party_state   = $party_adrs_info['acc_state'];
				  $party_country = $party_adrs_info['acc_country'];
				  $party_state_info = $this->TransactionModel->get_state_info($party_country,$party_state);
				 if($party_state_info)
				  $party_state_code = sprintf( '%02d', $party_state_info['state_code']);
				 else
				  $party_state_code = 0;
			
				}else{
					$party_state_code = 0;
				}   
				
				$comp_ro_address    = $this->CommonModel->get_company_address_info($this->company_id,'1');
				$get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
				$gstroutsup_info    = $this->TransactionModel->gstroutsup_info($voucher_txn_id);		
				
				 if(isset($get_comp_taxt_info['comp_gstin']))
				  $comp_gst = $get_comp_taxt_info['comp_gstin'];
				 else
				  $comp_gst ='';
			  if(isset($get_comp_taxt_info['gstin_legal_name']))
				  $comp_legal_name = $get_comp_taxt_info['gstin_legal_name'];
				 else
				  $comp_legal_name ='';
			  if(isset($get_comp_taxt_info['gstin_trade_name']))
				  $comp_trade_name = $get_comp_taxt_info['gstin_trade_name'];
				 else
				  $comp_trade_name ='';
				
				if($party_gst_info){
				  $party_gst        = ($party_gst_info['acc_gstin']!='')?$party_gst_info['acc_gstin']:'URP';
				  $party_legal_name = $party_gst_info['acc_legal_name'];
				  $party_trade_name = $party_gst_info['acc_trade_name'];
			  }else{
				  $party_gst        = 'URP';
				  $party_legal_name = '';
				  $party_trade_name = '';
			   } 
			   
				if($gstroutsup_info){
					$outsup_pos         = $gstroutsup_info['outsup_pos'];
					$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
					$outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];
					$outsup_inv_type    = $gstroutsup_info['outsup_inv_type'];	
					$outsup_eco         = $gstroutsup_info['outsup_eco'];
					if($outsup_eco >0){
					  $EcmGstin_info   = $this->TransactionModel->party_gst_info($party_id);
					   if($EcmGstin_info){
					     $EcmGstin        = ($EcmGstin_info['acc_gstin']!='')?$EcmGstin_info['acc_gstin']:NULL;		
					    }
					 }
					 else
						$EcmGstin        =NULL;
					
					$pos_state_info     = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));		  
				if($pos_state_info){
					$state_name         = $pos_state_info['state_name']; 
					$state_code         = $pos_state_info['state_code'];
					$statecode          = sprintf( '%02d', $state_code);
					}
				   $outsup_rev_chg_label = ($outsup_rev_chg==1)?'Y':'N';						
				 }
				else{
					$outsup_rev_chg_label ='N';
					$EcmGstin        ='';
					$outsup_pos = $outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode=$outsup_inv_type='';
				  }				  
				  $IgstOnIntra ='N';
				  /* if($bo_state_code==$statecode)
					  $IgstOnIntra ='N';
				  else
					 $IgstOnIntra ='Y'; */				  
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
			  
			      $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
				 	  
				  $shipto_addr1        = '';
				  $shipto_addr2        = '';
				  $shipto_place        = '';
				  $shipto_pin          = '';
				  $shipto_state_code   = '';		  
				  $dispfrm_addr1       = '';
				  $dispfrm_addr2       = '';
				  $dispfrm_place       = '';
				  $dispfrm_pin         = '';
				  $dispfrm_state_code  = '';		  
				  $supply_type         = "1";
                  $transactionType     = 1;	
				  $ewb_txn_type        = 1;
				  $shiptoAddr1         = '';
				  $shiptoAddr2         = '';
				  $shiptoPlace         = '';
				  $shiptoPincode       = '';
				  $actshiptoStateCode  = '';
				  $shiptoStateCode     = '';
				 if($get_ewbmstreqn_data){
					$ewb_txn_type        = $get_ewbmstreqn_data['ewb_txn_type']; 
					// if ewb_txn_type is 1 or 2 then fetch from address details from mc at the time of voucher save
					// and rest as per ewb_txn_type type value					
					if($ewb_txn_type==1 || $ewb_txn_type==2){
					  if($mc_centr_id!=''){	
						$mc_info          = $this->TransactionModel->mc_info($mc_centr_id);						
						$fromAddr1        = $mc_info['mat_cent_add1'];
						$fromAddr2        = $mc_info['mat_cent_add2'];
						$fromPlace        = $mc_info['mat_cent_city'];
						$fromPincode      = $mc_info['mat_cent_pin'];
						$actFromStateCode = $mc_info['state_code'];
						$fromStateCode    = $mc_info['state_code'];
					  }else{
						$fromAddr1        = '';
						$fromAddr2        = '';
						$fromPlace        = '';
						$fromPincode      = '';
						$actFromStateCode = '';
						$fromStateCode    = '';  
					  }
					}
					if($ewb_txn_type==3 || $ewb_txn_type==4){ 
					   $gstdispfrm_info     = $get_ewbmstreqn_data['gstdispfrm_info']; 
				       if($gstdispfrm_info){
						  $fromAddr1        = $gstdispfrm_info['dispfrm_addr1'];
						  $fromAddr2        = $gstdispfrm_info['dispfrm_addr2'];
						  $fromPlace        = $gstdispfrm_info['dispfrm_place'];
						  $fromPincode      = $gstdispfrm_info['dispfrm_pin'];
						  $actFromStateCode = sprintf( '%02d',$gstdispfrm_info['dispfrm_state_code']);
						  $fromStateCode    = sprintf( '%02d',$gstdispfrm_info['dispfrm_state_code']);
					    }
						$gstshipton_info     = $get_ewbmstreqn_data['gstshipton_info']; 
				       if($gstshipton_info){
						  $shiptoAddr1        = $gstshipton_info['dispfrm_addr1'];
						  $shiptoAddr2        = $gstshipton_info['dispfrm_addr2'];
						  $shiptoPlace        = $gstshipton_info['dispfrm_place'];
						  $shiptoPincode      = $gstshipton_info['dispfrm_pin'];
						  $actshiptoStateCode = sprintf( '%02d',$gstshipton_info['dispfrm_state_code']);
						  $shiptoStateCode    = sprintf( '%02d',$gstshipton_info['dispfrm_state_code']);
					    }
					}
				 }
			    $taxsummary        = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,'');
				$cgstValue		   = 0;
				$sgstValue 		   = 0;
				$igstValue 		   = 0;
				$cessValue 		   = 0;
				$cessNonAdvolValue = 0;
				$totInvValue       = 0;
				$bsdTotal          = 0;				
				$TotInvValFc       = 0;
				$bsdTotInvValFc    = 0;
								
				if($sundry_transactions){
				  foreach($sundry_transactions as $bsdrow){
					$bsdTotInvValFc = $bsdTotInvValFc+$bsdrow['billsundry_amountfc'];  
				  }
				}
				if($taxsummary){
				  foreach($taxsummary as $prow){ 
					  $totInvValue = $totInvValue+($prow['tax_amt']+$prow['total_tax']);
					  $TotInvValFc = $TotInvValFc+$prow['tax_amt_fcy'];					  
					  if($bo_state_code==$statecode){
							$cgstValue         = $cgstValue+$prow['cgst']; 
							$sgstValue         = $sgstValue+$prow['sgst']; 
							$igstValue         = 0; 
					   }else{
							$cgstValue         = 0; 
							$sgstValue         = 0; 
							$igstValue         = $igstValue+$prow['igst'];  
					   }
					  
					 $cessValue         = $cessValue+$prow['cess']; 
					 $cessNonAdvolValue = 0; 			 
				  }	
				}
				$totAssVal=0;
				if($item_trnsctions){
					$itmcntr=1;
				  foreach($item_trnsctions as $itmrow){
					  $totAssVal = $totAssVal+$itmrow['item_amount'];
					  if($itmrow['supply_type']=='2'){
						$IsServc='Y';  
					  }else
						$IsServc='N';	
					  $tax_rate = $itmrow['tax_rate'];
					  $IgstAmt=$CgstAmt=$SgstAmt=$totalTaxAmnt=0;
					  if($bo_state_code==$statecode){
						$CgstAmt = parseAmount(($itmrow['item_amount']*($itmrow['tax_rate']/2))/100);
						$SgstAmt = parseAmount(($itmrow['item_amount']*($itmrow['tax_rate']/2))/100);
					    $totalTaxAmnt = ($CgstAmt+$SgstAmt);
					  }else{
						$IgstAmt = parseAmount(($itmrow['item_amount']*$itmrow['tax_rate'])/100);
						$totalTaxAmnt = $IgstAmt; 	
					  }
					  
					  $ItemListings[]=array(
					                   'SlNo'=>(string)$itmcntr,'PrdDesc'=>$itmrow['item_name'],'IsServc'=>$IsServc,'HsnCd'=>$itmrow['item_hsn'],
									   'Qty'=>$itmrow['item_qty'],'Unit'=>$itmrow['item_unit'],'UnitPrice'=>$itmrow['item_price'],'TotAmt'=>parseAmount($itmrow['item_amount']),
									   'Discount'=>0,'PreTaxVal'=>1,'AssAmt'=>parseAmount($itmrow['item_amount']),'GstRt'=>$itmrow['tax_rate'],'IgstAmt'=>parseAmount($IgstAmt),
									   'CgstAmt'=>parseAmount($CgstAmt),'SgstAmt'=>parseAmount($SgstAmt),'CesRt'=>0,'CesAmt'=>0,'CesNonAdvlAmt'=>0,
									   'StateCesRt'=>0,'StateCesAmt'=>0,'StateCesNonAdvlAmt'=>0,'OthChrg'=>0,
									   'TotItemVal'=>parseAmount($itmrow['item_amount']+$totalTaxAmnt),'OrgCntry'=>'IN'
									   
									   );
						$itmcntr++;				
				       }
				   }				  
				
			    }
			}		
			
			
			$TranDtls         = array("TaxSch"=>"GST","SupTyp"=>$outsup_inv_type,"RegRev"=>$outsup_rev_chg_label,"EcmGstin"=>$EcmGstin,"IgstOnIntra"=>$IgstOnIntra);
			$DocDtls          = array("Typ"=>$docType,"No"=>$outsup_bill_ref_no,"Dt"=>$voucher_date);
			$SellerDtls       = array("Gstin"=>$comp_gst,"LglNm"=>$comp_legal_name,"TrdNm"=>$comp_trade_name,"Addr1"=>$fromAddr1,"Addr2"=>$fromAddr2,"Loc"=>$fromPlace,"Pin"=>$fromPincode,"Stcd"=>$fromStateCode);
			$BuyerDtls        = array("Gstin"=>$party_gst,"LglNm"=>$party_legal_name,"TrdNm"=>$party_trade_name,"Pos"=>$outsup_pos,"Addr1"=>$party_add1,"Addr2"=>$party_add2,"Loc"=>$party_city,"Pin"=>$party_pin,"Stcd"=>$party_state_code);
			if($ewb_txn_type==4){
			$DispDtls         = array("Nm"=>"","Addr1"=>$fromAddr1,"Addr2"=>$fromAddr2,"Loc"=>$fromPlace,"Pin"=>$fromPincode,"Stcd"=>$fromStateCode);
			$ShipDtls         = array("Gstin"=>"","LglNm"=>"","TrdNm"=>"","Addr1"=>"","Addr2"=>"","Loc"=>"","Pin"=>"","Stcd"=>"");
			}
			$ValDtls          = array("AssVal"=>parseAmount($totAssVal),"CgstVal"=>parseAmount($cgstValue),"SgstVal"=>parseAmount($sgstValue),"IgstVal"=>parseAmount($igstValue),"CesVal"=>parseAmount($cessValue),"StCesVal"=>0,"Discount"=>0,"OthChrg"=>0,"RndOffAmt"=>0,"TotInvVal"=>parseAmount($totInvValue),"TotInvValFc"=>parseAmount($TotInvValFc+$bsdTotInvValFc));
			$PayDtls          = array("Nm"=>"","AccDet"=>"","Mode"=>"","FinInsBr"=>"","PayTerm"=>"","PayInstr"=>"","CrTrn"=>"","DirDr"=>"","CrDay"=>"","PaidAmt"=>"","PaymtDue"=>"");
			if($ewb_txn_type==4){
			$response_data    = array("Version"=>"1.1","TranDtls"=>$TranDtls,"DocDtls"=>$DocDtls,"SellerDtls"=>$SellerDtls,"BuyerDtls"=>$BuyerDtls,"DispDtls"=>$DispDtls,"ShipDtls"=>$ShipDtls,"ItemList"=>$ItemListings,"ValDtls"=>$ValDtls);
			}
			else{
			$response_data    = array("Version"=>"1.1","TranDtls"=>$TranDtls,"DocDtls"=>$DocDtls,"SellerDtls"=>$SellerDtls,"BuyerDtls"=>$BuyerDtls,"ItemList"=>$ItemListings,"ValDtls"=>$ValDtls);
			}
			
			$RequestPayload    ='{
    "Version": "1.1",
    "TranDtls": {
        "TaxSch": "GST",
        "SupTyp": "B2B",
        "RegRev": "Y",
        "EcmGstin": null,
        "IgstOnIntra": "N"
    },
    "DocDtls": {
        "Typ": "INV",
        "No": "test010012025",
        "Dt": "10/01/2025"
    },
    "SellerDtls": {
        "Gstin": "07AGAPA5363L002",
        "LglNm": "NIC company pvt ltd",
        "TrdNm": "NIC Industries",
        "Addr1": "5th block, kuvempu layout",
        "Addr2": "kuvempu layout",
        "Loc": "GANDHINAGAR",
        "Pin": 110055,
        "Stcd": "07",
        "Ph": "9000000000",
        "Em": "abc@gmail.com"
    },
    "BuyerDtls": {
        "Gstin": "29AWGPV7107B1Z1",
        "LglNm": "XYZ company pvt ltd",
        "TrdNm": "XYZ Industries",
        "Pos": "12",
        "Addr1": "7th block, kuvempu layout",
        "Addr2": "kuvempu layout",
        "Loc": "GANDHINAGAR",
        "Pin": 562160,
        "Stcd": "29",
        "Ph": "91111111111",
        "Em": "xyz@yahoo.com"
    },    
    "ItemList": [
        {
            "SlNo": "1",
            "PrdDesc": "Rice",
            "IsServc": "N",
            "HsnCd": "1001",
            "Barcde": "123456",
            "Qty": 100.345,
            "FreeQty": 10,
            "Unit": "BAG",
            "UnitPrice": 99.545,
            "TotAmt": 9988.84,
            "Discount": 10,
            "PreTaxVal": 1,
            "AssAmt": 9978.84,
            "GstRt": 12,
            "IgstAmt": 1197.46,
            "CgstAmt": 0,
            "SgstAmt": 0,
            "CesRt": 5,
            "CesAmt": 498.94,
            "CesNonAdvlAmt": 10,
            "StateCesRt": 12,
            "StateCesAmt": 1197.46,
            "StateCesNonAdvlAmt": 5,
            "OthChrg": 10,
            "TotItemVal": 12897.7,
            "OrdLineRef": "3256",
            "OrgCntry": "AG",
            "PrdSlNo": "12345"
        }
    ],
    "ValDtls": {
        "AssVal": 9978.84,
        "CgstVal": 0,
        "SgstVal": 0,
        "IgstVal": 1197.46,
        "CesVal": 508.94,
        "StCesVal": 1202.46,
        "Discount": 10,
        "OthChrg": 20,
        "RndOffAmt": 0.3,
        "TotInvVal": 12908,
        "TotInvValFc": 12897.7
    }
}';
			
		  $RequestPayload       = json_encode($response_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);		
         return json_encode(['status' => true, 'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload,'errors'=>'','filename'=>'einvoice-'.$outsup_bill_ref_no.'.json']);
	}	
  }
  
  public function generate_eway_json(){
	ini_set('precision', 10);
    ini_set('serialize_precision', 10);
	if($this->request->getMethod() == 'post'){			
        $response_data    = array();  
		$errors_counter   = 0;
		$bo_state_code    = $this->session->get('ses_bostecd');  
		$vch_txn_id       = $this->request->getVar('vch_txn_id');
		$from_date        = $this->request->getVar('fromdate');
		$to_date          = $this->request->getVar('todate');		
		$from_date        = date('Y-m-d', strtotime($from_date));
        $to_date          = date('Y-m-d', strtotime($to_date));   
		$response         = json_decode($this->GstexportModel->load_eway_listing(1, -1, $from_date, $to_date, 0,'',$vch_txn_id),true);
		if($response['data']){
			foreach($response['data'] as $row){
				$voucher_txn_id   = $row['voucher_txn_id'];
				$voucher_type_id  = $row['voucher_type_id']; 
                $voucher_info     = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);		
				$party_trnsction  = $this->TransactionModel->get_party_transaction($voucher_txn_id);
				$party_id 	  	  = $party_trnsction['acc_id'];
				$party_gst_info   = $this->TransactionModel->party_gst_info($party_id);
				$narration 		  = $party_trnsction['acc_txn_narr'];
				$item_trnsctions  = $this->TransactionModel->get_item_transactions($voucher_txn_id);
				$sundry_transactions = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);
				$voucher_series   = $voucher_info['comp_vch_series_id'];
				$voucher_no 	  = $voucher_info['comp_vch_no'];
				$voucher_type 	  = $voucher_info['voucher_type_id'];
				$mc_centr_id 	  = $voucher_info['mat_cent_id'];
				
				$voucher_date 	  = date('d/m/Y', strtotime($voucher_info['voucher_date']));
				$ewbmstreqn_data  = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
				$billfrm_data     = $this->TransactionModel->billfrm_info();
				$bo_gstin_type    = $this->session->get('bo_gstin_type');
				$bo_id            = $this->session->get('ses_boid');	  
				$get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
				$get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
				$party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
				$party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
				if($get_account_info){
				  $party_phone = $get_account_info['acc_mobile'];
				  $party_name  = $get_account_info['acc_name'];
				  $party_email = $get_account_info['acc_email'];
				}	
				if($party_adrs_info){
				  $party_add1    = $party_adrs_info['acc_add1'];
				  $party_add2    = $party_adrs_info['acc_add2'];
				  $party_city    = $party_adrs_info['acc_city'];
				  $party_pin     = $party_adrs_info['acc_pin'];
				  $party_state   = $party_adrs_info['acc_state'];
				  $party_country = $party_adrs_info['acc_country'];
				  $party_state_info = $this->TransactionModel->get_state_info($party_country,$party_state);
				 if($party_state_info)
				  $party_state_code = sprintf( '%02d', $party_state_info['state_code']);
				 else
				  $party_state_code = 0;
			
				}else{
					$party_state_code = 0;
				}   
		
				$comp_ro_address    = $this->CommonModel->get_company_address_info($this->company_id,'1');
				$get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
				$gstroutsup_info    = $this->TransactionModel->gstroutsup_info($voucher_txn_id);		
				if($gstroutsup_info){
					$outsup_pos         = $gstroutsup_info['outsup_pos'];
					$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
					$outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];			
					$pos_state_info     = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));		  
				if($pos_state_info){
					$state_name = $pos_state_info['state_name']; 
					$state_code = $pos_state_info['state_code'];
					$statecode  = sprintf( '%02d', $state_code);
					}		
				}
				else{
					$outsup_pos = $outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
				  }
				  
				  if($bo_state_code==$statecode)
					  $IgstOnIntra ='N';
				  else
					 $IgstOnIntra ='Y';
		 
				  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
				  $shipto_addr1        = '';
				  $shipto_addr2        = '';
				  $shipto_place        = '';
				  $shipto_pin          = '';
				  $shipto_state_code   = '';		  
				  $dispfrm_addr1       = '';
				  $dispfrm_addr2       = '';
				  $dispfrm_place       = '';
				  $dispfrm_pin         = '';
				  $dispfrm_state_code  = '';		  
				  $supply_type         ="1";
                  $transactionType        = 1;				  
				 if($get_ewbmstreqn_data){
					$ewb_txn_type        = $get_ewbmstreqn_data['ewb_txn_type']; 
					// if ewb_txn_type is 1 or 2 then fetch from address details from mc at the time of voucher save
					// and rest as per ewb_txn_type type value					
					if($ewb_txn_type==1 || $ewb_txn_type==2){
						$mc_info          = $this->TransactionModel->mc_info($mc_centr_id);						
						$fromAddr1        = $mc_info['mat_cent_add1'];
						$fromAddr2        = $mc_info['mat_cent_add2'];
						$fromPlace        = $mc_info['mat_cent_city'];
						$fromPincode      = $mc_info['mat_cent_pin'];
						$actFromStateCode = $mc_info['state_code'];
						$fromStateCode    = $mc_info['state_code'];
					}
					if($ewb_txn_type==3 || $ewb_txn_type==4){ 
					   $gstdispfrm_info     = $get_ewbmstreqn_data['gstdispfrm_info']; 
				       if($gstdispfrm_info){
						  $fromAddr1        = $gstdispfrm_info['dispfrm_addr1'];
						  $fromAddr2        = $gstdispfrm_info['dispfrm_addr2'];
						  $fromPlace        = $gstdispfrm_info['dispfrm_place'];
						  $fromPincode      = $gstdispfrm_info['dispfrm_pin'];
						  $actFromStateCode = sprintf( '%02d',$gstdispfrm_info['dispfrm_state_code']);
						  $fromStateCode    = sprintf( '%02d',$gstdispfrm_info['dispfrm_state_code']);
					    }
					} 
					$supply_type         = $get_ewbmstreqn_data['ewb_supply_type'];
					$transactionType     = $get_ewbmstreqn_data['ewb_txn_type']; 
				   $gstshipton_info      = $get_ewbmstreqn_data['gstshipton_info']; 
				   if($gstshipton_info){
					  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
					  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
					  $shipto_place      = $gstshipton_info['shipto_place'];
					  $shipto_pin        = $gstshipton_info['shipto_pin'];
					  $shipto_state_code = sprintf( '%02d',$gstshipton_info['shipto_state_code']);
					}		  
				   
				}	 
		
			  $gsttpt_id               = $this->TransactionModel->transport_single_info($voucher_txn_id);
			  $transporter_info        = $this->TransactionModel->transporter_info($gsttpt_id,$voucher_txn_id);	 
			  $transport_name          = '';	  
			  $transport_veh_no        = '';
			  $transport_veh_type      = '';
			  $transport_gstin         = ''; 
			  $transport_enrollment_id = '';
	    
			  if($transporter_info){
				$transport_name          = $transporter_info['gsttpt_name'];	  
				$transport_veh_no        = $transporter_info['trans_veh_no'];
				$transport_veh_type      = $transporter_info['trans_veh_type'];
				$transport_gstin         = $transporter_info['gsttpt_gstin'];
				$transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
			  }	 
	   
			  if(isset($get_comp_taxt_info['comp_gstin']))
				  $comp_gst = $get_comp_taxt_info['comp_gstin'];
				 else
				  $comp_gst ='';
			  if(isset($get_comp_taxt_info['gstin_legal_name']))
				  $comp_legal_name = $get_comp_taxt_info['gstin_legal_name'];
				 else
				  $comp_legal_name ='';
			  if(isset($get_comp_taxt_info['gstin_trade_name']))
				  $comp_trade_name = $get_comp_taxt_info['gstin_trade_name'];
				 else
				  $comp_trade_name ='';
	   
			   $company_info      = $this->CommonModel->get_company_info($this->company_id);	  
			   $comp_email        = $company_info['corp_email'];
			   $comp_name         = $company_info['comp_name'];
			   $comp_phone        = $company_info['corp_tel'];
			   $comp_state_info   = $this->TransactionModel->get_state_info($comp_ro_address['comp_country'],$comp_ro_address['comp_state']);
			   $comp_country_info = $this->TransactionModel->get_country_info($comp_ro_address['comp_country']);
			  if($comp_state_info)
				$comp_state_code  = sprintf( '%02d', $comp_state_info['state_code']);
			  else
				$comp_state_code  = 0;  
			   
			  if(isset($comp_ro_address['comp_addr1']))
				$company_adrs1    = $comp_ro_address['comp_addr1'];
			  else
				$company_adrs1    = '';  
			
			  if(isset($comp_ro_address['comp_addr2']))
			   $company_adrs2     = $comp_ro_address['comp_addr2'];
			  else
				$company_adrs2    = ''; 
			  if(isset($comp_ro_address['comp_pin']))
			   $company_pin       = $comp_ro_address['comp_pin'];
			  else
			   $company_pin       = ''; 
			 
			  if(isset($comp_ro_address['comp_city']))
			   $company_city      = $comp_ro_address['comp_city'];
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
					  
			  if($party_gst_info){
				  $party_gst        = $party_gst_info['acc_gstin'];
				  $party_legal_name = $party_gst_info['acc_legal_name'];
				  $party_trade_name = $party_gst_info['acc_trade_name'];
			  }else{
				  $party_gst        = '';
				  $party_legal_name = '';
				  $party_trade_name = '';
			   } 
				$taxsummary = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,'');
				$cgstValue		  = 0;
				$sgstValue 		  = 0;
				$igstValue 		  = 0;
				$cessValue 		  = 0;
				$cessNonAdvolValue= 0;
				$totInvValue      = 0;
				$bsdTotal         = 0;
				
				if($taxsummary){
				  foreach($taxsummary as $prow){
					   if($bo_state_code==$statecode){
							$cgstValue         = $cgstValue+$prow['cgst']; 
							$sgstValue         = $sgstValue+$prow['sgst']; 
							$igstValue         = 0; 
					   }else{
							$cgstValue         = 0; 
							$sgstValue         = 0; 
							$igstValue         = $igstValue+$prow['igst'];  
					   }
					  
					 $cessValue         = $cessValue+$prow['cess']; 
					 $cessNonAdvolValue = 0; 			 
				  }	
				}
				
				$itemList=array();
				if($item_trnsctions){
				  foreach($item_trnsctions as $itmrow){
					$totInvValue = $totInvValue+$itmrow['item_amount']; 					  
					$cess        = $itmrow['cess_rate']; 
					if($bo_state_code==$statecode){
						$igst    = 0;
						$cgst    = parseAmount($itmrow['igst_rate']/2);
						$sgst    = parseAmount($itmrow['igst_rate']/2);
						
					}else{
						$igst    = parseAmount($itmrow['igst_rate']);
						$cgst    = 0;
						$sgst    = 0;
					  }	
					$tax_amt = $itmrow['tax_amt'];
					$itemList[]=array("productName"=>$itmrow["item_name"],"productDesc"=>$itmrow["description"],"hsnCode"=>$itmrow["tax_hsn_sac"],"quantity"=>parseAmount($itmrow["item_qty"]),
									  "qtyUnit"=>$itmrow["item_unit"],"cgstRate"=>parseAmount($cgst),"sgstRate"=>parseAmount($sgst),"igstRate"=>parseAmount($igst),"cessRate"=>parseAmount($cess),
									  "cessNonadvol"=>parseAmount("0"),"taxableAmount"=>parseAmount($tax_amt)
									  );	
					 }
				}
				
				if($sundry_transactions){
				  foreach($sundry_transactions as $bsdrow){
					$bsdTotal=$bsdTotal+$bsdrow['billsundry_amount'];  
				  }
				}
				
				$gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
				if($gsttpt_id){
					$transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);			
					$transporterId    = $transporter_info['gsttpt_gstin'];
					$transporterName  = $transporter_info['gsttpt_name'];
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
	 			
				/* if($transporterId==''){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - Transport Document Number is required.";
				}
				if(strtotime($voucher_date) > strtotime(date('Y-m-d'))){
					$errors_counter++;
					$errors[]=$outsup_bill_ref_no." - The Document Date should be less than or equal to current date.";
				}
				if($transport_name==''){
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
				 } */		
			  }			
		   }		
		   /* if($errors_counter>0){
		  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		} */
		$RequestPayload =  json_encode($response_data,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);		
		return json_encode(['status' => true, 'message' =>'JSON file downloaded','jsonfile'=>$RequestPayload,'errors'=>'','filename'=>'eway-invoice-'.$outsup_bill_ref_no.'.json']);
	  }
  }
  

  public function download_gstr1_json()
{
    if ($this->request->getMethod() !== 'POST') {
        return json_encode([
            'status' => false,
            'message' => 'Invalid request method',
            'jsonfile' => '',
            'filename' => '',
            'errors' => ['Only POST method allowed.']
        ]);
    }

    // =============================
    // STATE MAP
    // =============================
    $state_data = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
    $states = [];
    foreach ($state_data as $strow) {
        $state_code = sprintf('%02d', $strow['state_code']);
        $states[$state_code] = $strow['state_name'];
    }

    // =============================
    // DATE HANDLING
    // =============================
    if (isset($_POST['wenc']) && $_POST['wenc'] == "0") {
        $from_date = date('Y-m-d', strtotime($this->request->getVar('fromdate')));
        $to_date   = date('Y-m-d', strtotime($this->request->getVar('todate')));
    } else {
        $fromdate_val = unobfuscate_link($this->request->getVar('fromdate'));
        $todate_val   = unobfuscate_link($this->request->getVar('todate'));
        $from_date    = $fromdate_val[1] ?? '';
        $to_date      = $todate_val[1] ?? '';
    }

    $fp = date('mY', strtotime($to_date));

    // =============================
    // COMPANY GSTIN
    // =============================
    $get_comp_taxt_info = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
    $comp_gst = $get_comp_taxt_info['hobo_gstin'] ?? '';

    $ErrosList = [];

    if (strlen(trim($comp_gst)) != 15) {
        $ErrosList[] = "Company GSTIN should be 15 characters.";
    }

    if (strlen(trim($comp_gst)) == 15 && inputmask_gstin(trim($comp_gst)) == false) {
        $ErrosList[] = "Invalid GSTIN.";
    }

    if (empty($from_date) || empty($to_date)) {
        $ErrosList[] = "Invalid date range.";
    }

    if (!empty($from_date) && !empty($to_date) && strtotime($from_date) > strtotime($to_date)) {
        $ErrosList[] = "From date cannot be greater than To date.";
    }

    if (!empty($ErrosList)) {
        return json_encode([
            'status' => false,
            'message' => 'Validation Error',
            'jsonfile' => '',
            'filename' => '',
            'errors' => $ErrosList
        ]);
    }

    // =============================
    // BUILD JSON ROOT
    // =============================
    $final_result = [
        "gstin"   => trim($comp_gst),
        "fp"      => $fp,
        "version" => "GST3.2.4", // ✅ FIXED
        "hash"    => "hash"
    ];

    // B2B
    $tableinfo = [
        'cmp_tax_short_code' => ['REGSPLY'],
        'outsup_dr_note'     => '0',
        'outsup_cr_note'     => '0',
        'outsup_rev_chg'     => '0',
        'outsup_inv_type'    => ['B2B'],
        'outsup_eco'         => '0',
        'value'              => 0
    ];
    $response_info = $this->GstexportModel->gst_b2b_table_export($from_date, $to_date, $tableinfo, $states);
    if (!empty($response_info)) {
        $final_result['b2b'] = $response_info;
    }

    // B2CL
    $tableinfo = [
        'cmp_tax_short_code' => ['REGSPLY'],
        'outsup_dr_note'     => '0',
        'outsup_cr_note'     => '0',
        'outsup_rev_chg'     => '0',
        'outsup_inv_type'    => ['B2CL'],
        'outsup_eco'         => '0',
        'value'              => '',
        'cnd'                => '>'
    ];
    $response_info = $this->GstexportModel->gst_b2cl_table_export($from_date, $to_date, $tableinfo, $states);
    if (!empty($response_info)) {
        $final_result['b2cl'] = $response_info;
    }

    // B2CS
    $tableinfo = [
        'cmp_tax_short_code' => ['REGSPLY'],
        'outsup_dr_note'     => '0',
        'outsup_cr_note'     => '0',
        'outsup_rev_chg'     => '0',
        'outsup_inv_type'    => ['B2CS'],
        'outsup_eco'         => '0',
        'value'              => '',
        'cnd'                => '<'
    ];
    $response_info = $this->GstexportModel->gst_b2cs_table_export($from_date, $to_date, $tableinfo, $states);
    if (!empty($response_info)) {
        $final_result['b2cs'] = $response_info;
    }

    // NIL
    $response_info = $this->GstexportModel->gst_nill_table_export($from_date, $to_date, $states);
    if (!empty($response_info)) {
        $final_result['nil'] = $response_info;
    }

    // EXPORTS
    $response_info = $this->GstexportModel->gst_exports_table_export($from_date, $to_date, $states);
    if (!empty($response_info)) {
        $final_result['exp'] = $response_info;
    }

    // CDNR
    $response_info = $this->GstexportModel->gst_cdnr_table_export($from_date, $to_date, $states);
    if (!empty($response_info)) {
        $final_result['cdnr'] = $response_info;
    }

    // CDNUR
    $response_info = $this->GstexportModel->gst_cdnur_table_export($from_date, $to_date, $states);
    if (!empty($response_info)) {
        $final_result['cdnur'] = $response_info;
    }

    // DOC ISSUE
    $response_info = $this->GstexportModel->gst_docs_table_export($from_date, $to_date, $states);
    if (!empty($response_info)) {
        $final_result['doc_issue'] = $response_info;
    }

    // HSN
    $response_info = $this->GstexportModel->gst_hsn_table_export($from_date, $to_date, $states);
    if (!empty($response_info)) {
        $final_result['hsn'] = $response_info;
    }
   ini_set('serialize_precision', -1);
   ini_set('precision', 14);
   
   
    // =============================
// STRICT VALIDATION
// =============================
$validationErrors = $this->GstexportModel->validate_gstr1_json_strict($final_result);

// Count total errors
$totalErrors = 0;

if (!empty($validationErrors['general'])) {
    $totalErrors += count($validationErrors['general']);
}

if (!empty($validationErrors['hsn'])) {
    $totalErrors += count($validationErrors['hsn']);
}

if (!empty($validationErrors['invoices'])) {
    foreach ($validationErrors['invoices'] as $invErrors) {
        $totalErrors += count($invErrors);
    }
}

if ($totalErrors > 0) {

    return json_encode([
        'status'        => false,
        'message'       => "GST Validation Failed ({$totalErrors} errors found)",
        'jsonfile'      => json_encode($final_result, JSON_PRETTY_PRINT),
        'filename'      => '',
        'errors'        => $validationErrors, // 🔥 IMPORTANT (GROUPED)
        'total_errors'  => $totalErrors
    ]);
}
$jsonfile = json_encode($final_result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

if ($jsonfile === false) {
    return json_encode([
        'status' => false,
        'message' => 'JSON generation failed',
        'jsonfile' => '',
        'filename' => '',
        'errors' => [json_last_error_msg()]
    ]);
}

return json_encode([
    'status'   => true,
    'message'  => 'JSON Generated Successfully',
    'jsonfile' => $jsonfile,
    'filename' => 'gstr1file.json'
]);
}
  public function download_gstr1_json_old(){ 
	  if($this->request->getMethod() == 'POST'){		
	     $state_data    = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
	     $states        = array();
	   foreach($state_data as $strow){
		 $state_code          = sprintf('%02d',$strow['state_code']);	
		 $states[$state_code] = $strow['state_name'];
	   } 	  
	   if(isset($_POST['wenc']) && $_POST['wenc']=="0"){
		 $from_date          = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
	     $to_date            = date('Y-m-d',strtotime($this->request->getVar('todate'))); 	   
	   }else{
		   $fromdate_val    = unobfuscate_link($this->request->getVar('fromdate'));
		   $todate_val      = unobfuscate_link($this->request->getVar('todate')); 			   
		   $from_date       = $fromdate_val[1];
		   $to_date         = $todate_val[1]; 	
	    }
       $result          = array();
	   $limit           = -1;
	   $pq_curPage      = -1;
	   
	   $fp = date('mY',strtotime($to_date));
	   
	   $tableinfo       = $this->table_lists_detailed;
	   
	   $get_comp_taxt_info = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
	    if(isset($get_comp_taxt_info['hobo_gstin']))
          $comp_gst = $get_comp_taxt_info['hobo_gstin'];
	     else
		  $comp_gst ='';
	 
	  $ErrosList = array();
	 if(strlen(trim($comp_gst))<15 || strlen(trim($comp_gst))>15){
			$ErrosList[]="Company GSTIN should be Alphanumeric with 15 characters.<br>";
		}
	  if(strlen(trim($comp_gst))==15 && inputmask_gstin(trim($comp_gst))==false){
			$ErrosList[]="Company GSTIN not valid.<br>";
		}
	  if(count($ErrosList) >0){
		return json_encode(['status' => false, 'message' =>'Validation Error','jsonfile'=>'','filename'=>'','errors'=>$ErrosList]);  					   
	  }	
	   else{		
	    $tableinfo                = array('cmp_tax_short_code'=>array('REGSPLY'),'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>array('B2B'),'outsup_eco'=>'0','value'=>0);
		$final_result             = array();
	    $final_result             = array("gstin"=>$comp_gst,"fp"=>$fp,"version"=>"GST3.2","hash"=>"hash");	  
		$response_info            = $this->GstexportModel->gst_b2b_table_export($from_date, $to_date,$tableinfo,$states);			
		 if(!empty($response_info))
		$final_result['b2b']      = $response_info;	
		 
		$tableinfo                = array('cmp_tax_short_code'=>array('REGSPLY'),'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>array('B2CL'),'outsup_eco'=>'0','value'=>'','cnd'=>'>');
		$response_info            = $this->GstexportModel->gst_b2cl_table_export($from_date, $to_date,$tableinfo,$states);			
		 if(!empty($response_info))
		$final_result['b2cl']     = $response_info; 
		
		$tableinfo                = array('cmp_tax_short_code'=>array('REGSPLY'),'outsup_dr_note'=>'0','outsup_cr_note'=>'0','outsup_rev_chg'=>'0','outsup_inv_type'=>array('B2CS'),'outsup_eco'=>'0','value'=>'','cnd'=>'<');
		$response_info            = $this->GstexportModel->gst_b2cs_table_export($from_date, $to_date,$tableinfo,$states);			
		if(!empty($response_info))
		$final_result['b2cs']     = $response_info;
		
		$response_info              = $this->GstexportModel->gst_nill_table_export($from_date, $to_date,$states);	
		
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
		
		$response_info            = $this->GstexportModel->gst_hsn_table_export($from_date, $to_date,$states);			
		$final_result['hsn']      = $response_info; 
		
		
		$jsonfile =  json_encode($final_result,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		return json_encode(['status' => true, 'message' =>'JSON file downloaded','jsonfile'=>$jsonfile,'filename'=>'gstr1file.json']);   
	
		}
		  
	  }
	  
  }
  
  public function download_gstr1iff_json()
{
    if ($this->request->getMethod() !== 'POST') {
        return json_encode([
            'status'   => false,
            'message'  => 'Invalid request',
            'jsonfile' => '',
            'filename' => '',
            'errors'   => ['Invalid method']
        ]);
    }

    // State map
    $state_rows = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
    $states = [];
    foreach ($state_rows as $strow) {
        $code = sprintf('%02d', $strow['state_code']);
        $states[$code] = $strow['state_name'];
    }

    // Dates (obfuscated inputs)
    $from_dec  = unobfuscate_link($this->request->getVar('fromdate'));
    $to_dec    = unobfuscate_link($this->request->getVar('todate'));
    $from_date = $from_dec[1];
    $to_date   = $to_dec[1];
    $fp        = date('mY', strtotime($from_date));

    // Company GSTIN
    $get_comp_taxt_info = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
	    if(isset($get_comp_taxt_info['hobo_gstin']))
          $comp_gst = $get_comp_taxt_info['hobo_gstin'];
	     else
		  $comp_gst ='';

    $errors = [];
    if (strlen(trim($comp_gst)) !== 15) {
        $errors[] = "Company GSTIN should be Alphanumeric with 15 characters.<br>";
    } elseif (!inputmask_gstin(trim($comp_gst))) {
        $errors[] = "Company GSTIN not valid.<br>";
    }
    if (!empty($errors)) {
        return json_encode([
            'status'   => false,
            'message'  => 'Validation Error',
            'jsonfile' => '',
            'filename' => '',
            'errors'   => $errors
        ]);
    }

    // Build IFF JSON (only B2B and CDNR)
    $final_result = [
        "gstin"   => $comp_gst,
        "fp"      => $fp,
        "version" => "GST3.2",
        "hash"    => "hash",
    ];

    // B2B
    $tableinfo = [
        'cmp_tax_short_code' => ['REGSPLY'],
        'outsup_dr_note'     => '0',
        'outsup_cr_note'     => '0',
        'outsup_rev_chg'     => '0',
        'outsup_inv_type'    => ['B2B'],
        'outsup_eco'         => '0',
        'value'              => 0
    ];
    $resp = $this->GstexportModel->gst_b2b_table_export($from_date, $to_date, $tableinfo, $states);
    if (!empty($resp)) {
        $final_result['b2b'] = $resp;
    }

    // CDNR
    $resp = $this->GstexportModel->gst_cdnr_table_export($from_date, $to_date, $states);
    if (!empty($resp['inv'])) {
        $final_result['cdnr'] = $resp['inv'];
    }

    $jsonfile = json_encode($final_result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    return json_encode([
        'status'   => true,
        'message'  => 'JSON file downloaded',
        'jsonfile' => $jsonfile,
        'filename' => 'gstrifffile.json'
    ]);
}
  
   public function load_eway_voucher_details()
 	{   $vouchers_listings = $this->request->getVar('vouchers_listings');
		if($vouchers_listings){
		  return json_encode(['status' => true,'list' => $vouchers_listings]);
		}
		return json_encode(['status' => false, 'message' => 'No Voucher Found']);
 	}
 
public function load_einvoice_voucher_details()
 	{   $vouchers_listings = $this->request->getVar('vouchers_listings');
		if($vouchers_listings){
		  return json_encode(['status' => true,'list' => $vouchers_listings]);
		}
		return json_encode(['status' => false, 'message' => 'No Voucher Found']);
 	}
	
  public function egstr_one(){
       
       if($this->request->getMethod() == 'post'){	      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
          } 
       
        $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;        
        return view($this->folder_path.'etaxes/dashboard_gstrone',$data);  
    }
 public function eway(){
       
       if($this->request->getMethod() == 'post'){	      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
          } 
       
        $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;        
        return view($this->folder_path.'etaxes/eway_dashboard',$data);  
    }	

  public function einvoice(){
       
       if($this->request->getMethod() == 'post'){	      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
          } 
       
        $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;        
        return view($this->folder_path.'etaxes/einvoice_dashboard',$data);  
    }
	
  public function preview_gstr1_report(){	
   $from_date = $_GET['fromdate'] ?? '';
   $to_date   = $_GET['todate'] ?? '';
   $fltrtypes = $_GET['fltrtype'] ?? '';
   if($fltrtypes=='')
	throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
   else{
    $fltrtypes_info = unobfuscate_link($fltrtypes);
    if(!isset($fltrtypes_info[1]))
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
	$fltrtype = $fltrtypes_info[1];
	}
   if(!in_array($fltrtype,array('mnth','yrs','qtr')))
	  return redirect()->to($this->base_url.'gst/outwardsupplies');
	   
   $from_date = validate_fy_from_date($from_date);
   $to_date   = validate_fy_to_date($to_date);
		
   $from_date_ymd = date('Y-m-d', strtotime($from_date));
   $to_date_ymd   = date('Y-m-d', strtotime($to_date));
   $table_lists   = $this->table_lists_detailed;
   $response      = $this->GstrReportModel->load_gstr1_detailed($from_date_ymd,$to_date_ymd,$table_lists);
   
   $data['show_date'] = date('M, Y',strtotime($from_date_ymd));
   $data['response']  = $response;
   $data['from_date'] = $from_date_ymd;
   $data['to_date']   = $to_date_ymd;
   $data['fltrtype']  = $fltrtype;
   $data['fltrtype_label']  = $fltrtypes;
   return view($this->folder_path.'etaxes/preview_gstr1_report',$data); 
		
 }	
  public function ajax_eway_transactions(){
	$pq_curPage   = (int)$_POST["pq_curpage"];
    $limit        = (int)$_POST["pq_rpp"];
    $search       = '';
    $pq_filter    = $this->request->getVar('pq_filter');
	$ewbval       = $this->request->getVar('ewbval');
	
    if(!empty($pq_filter)){
      $pq_filter = json_decode($pq_filter);
      $search    = $pq_filter->data[0]->value;
    }
    $from_date   = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	$to_date     = date('Y-m-d',strtotime($this->request->getVar('to_date')));
	$view        = $this->request->getVar('view');
	echo $this->GstexportModel->load_eway_listing($from_date, $to_date, $view,$search,0,$ewbval); 
   }

   public function ajax_einvoice_transactions(){
	$pq_curPage = (int)$_POST["pq_curpage"];
    $limit     = (int)$_POST["pq_rpp"];
    $search = '';
    $pq_filter    = $this->request->getVar('pq_filter');
	$ewbval       = $this->request->getVar('ewbval');
    if(!empty($pq_filter)){
      $pq_filter = json_decode($pq_filter);
      $search    = $pq_filter->data[0]->value;
    }
    $from_date   = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	$to_date     = date('Y-m-d',strtotime($this->request->getVar('to_date')));
	$view        = $this->request->getVar('view');
	echo $this->GstexportModel->load_einvoice_listing($pq_curPage, $limit, $from_date, $to_date, $view,$search,0,$ewbval); 
   }   
 
  public function eway_summary(){	
   $from_date     = $_GET['fromdate'] ?? '';
   $to_date       = $_GET['todate'] ?? '';
   $from_date     = validate_fy_from_date($from_date);
   $to_date       = validate_fy_to_date($to_date);		
   $from_date_ymd = date('d-m-Y', strtotime($from_date));
   $to_date_ymd   = date('d-m-Y', strtotime($to_date));
  
   $data['from_date'] = $from_date_ymd;
   $data['to_date']   = $to_date_ymd;
   $data['view']      = "0";
   $data['transport_modes']  = transport_modes();
   $data['vehicle_types']    = vehicle_types(); 
   return view($this->folder_path.'etaxes/eway_summary',$data); 		
 }	 
 
 public function einvoice_summary(){	
   $from_date = $_GET['fromdate'] ?? '';
   $to_date   = $_GET['todate'] ?? '';   
   
   $from_date = validate_fy_from_date($from_date);
   $to_date   = validate_fy_to_date($to_date);
		
   $from_date_ymd = date('d-m-Y', strtotime($from_date));
   $to_date_ymd   = date('d-m-Y', strtotime($to_date));
  
   $data['from_date'] = $from_date_ymd;
   $data['to_date']   = $to_date_ymd;
   $data['view']      = "0";
   return view($this->folder_path.'etaxes/einvoice_summary',$data); 
		
 }
 
}