<?php
namespace App\Controllers\Admin;

use App\Models\Admin\TransactionModel;
use App\Models\Admin\ReportsModel;
use App\Controllers\BaseController;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use App\Models\Admin\ItemsModel;

class Ajax  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text','einvoice_helper']);

			$this->TransactionModel  = new TransactionModel();	
            $this->ReportsModel  = new ReportsModel();					
			$this->auth_session  = new auth_session();
			$this->CommonModel   =  new CommonModel();
		    $this->ItemsModel   =  new ItemsModel();				
			$this->auth_session->user_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      =  base_url().'/'.getenv('AdminPath');
			$this->folder_path   =  getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->auth_session->is_company_opened();
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->company_id    =  $this->session->get('ses_company_id');
			
			$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
    }
   
      public function ajax_itemtxn_units(){
	    $itmunitmst_list = $this->ReportsModel->itmunitmst_list();
		if($this->request->getMethod() == 'post'){	            
		    $final_units = array();
		    $final_units[''] = 'All Units';
            $item_id  =  $this->request->getVar('item_id');		  
            $result   =  $this->ReportsModel->GetUnqUnits($item_id);
			if($result){
				foreach($result as $id => $row){
				  	if(isset($itmunitmst_list[$id]))
						$final_units[$id]= $itmunitmst_list[$id];
					
				}
				
			}
			
		echo form_dropdown('unitid',$final_units, '',' id="unitid" style="font-size:18px;border-radius: 6px 0px 0px 6px;" class="selectwidget form-control"'); 
			   	
		}	   
       
   }
  public function modify_taxcatg_info(){
	 if($this->request->getMethod() == 'post'){
			$cmp_tax_cat_id = $this->request->getVar('local_tax');
			$tax_cat_act        =  $this->request->getVar('tax_cat_act');
			$tax_cat_name        =  $this->request->getVar('tax_cat_name');
   			$tax_cat_section     =  $this->request->getVar('tax_cat_section');
			$item_tax_igst_rate  =  $this->request->getVar('item_tax_igst_rate');
   			$item_tax_cess_rate  =  $this->request->getVar('item_tax_cess_rate');			
			$item_tax_wef        =  $this->request->getVar('item_tax_wef');
   			$item_tax_basis      =  $this->request->getVar('item_tax_basis');
			$cmp_tax_cat_type    =  $this->request->getVar('cmp_tax_cat_type');
			$tax_short_code      =  $this->request->getVar('tax_short_code');
			
			
			$comp_tax_mst_data = array(
			                         "cmp_tax_cat_act"=>$tax_cat_act,
									 "cmp_tax_cat_name"=>$tax_cat_name,
									 "cmp_tax_cat_section"=>$tax_cat_section,
									 "cmp_tax_cat_basis"=>$item_tax_basis,
									 "cmp_tax_cat_type"=>$cmp_tax_cat_type);
			 $this->ItemsModel->update_comp_tax_mst($cmp_tax_cat_id,$comp_tax_mst_data); 


			$cmpgstcatn_data = array(
			                         "cmp_tax_cat_igst"=>$item_tax_igst_rate,
									 "cmp_tax_cat_cess"=>$item_tax_cess_rate,
									 "cmp_tax_cat_wef"=>date("Y-m-d",strtotime($item_tax_wef)),
									 "cmp_tax_short_code"=>$tax_short_code,
									 "tax_cat_id"=>0,
									 "cmp_tax_cat_cess_basis"=>$item_tax_basis);
		$this->ItemsModel->update_comp_cmpgstcatn_mst($cmp_tax_cat_id,$cmpgstcatn_data); 

        $local_tax_category = $this->ItemsModel->local_tax_category_dropdown();		
		$local_tax_category['0']='Others';			   
        echo form_dropdown('local_tax', $local_tax_category,$cmp_tax_cat_id,'id="local_tax" class="form-control"');
			
	  }		 
	  
  }  
   
  public function create_gstpaid_acc(){
    // create GST PAID A/C DR. (UNDER INDIRECT EXPENSES IN PARENT) in case of composition of gstin_id
    $response = $this->TransactionModel->create_gstpaid_account();  
    if(!$response['status']){
			  echo  json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
			}	
   echo json_encode(['status' => true,'acc_id'=>$response['acc_id'],'message' => '']);
  }
  
  public function migrate_voucher_bo(){
    if($this->request->getMethod() == 'post' && $this->request->isAjax()){ 
	 $voucher_txn_id  = $this->request->getVar('vchtxn_id');
	 $migrate_to_boid = $this->request->getVar('migrate_to_bo');
     $response = $this->TransactionModel->migration_voucher_boid($voucher_txn_id,$migrate_to_boid);  
     if(!$response['status']){
			  echo  json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
			}	
      echo json_encode(['status' => true,'message' => '']);
	}
  }
  
  public function clear_valuation_log(){
	  if($this->request->getMethod() == 'post' && $this->request->isAjax()){ 
	     $item_id  = $this->request->getVar('item_id');
	     $method_id = $this->request->getVar('method_id');
		 $this->ReportsModel->clear_valuations_log($item_id,$method_id);
	  }
  }
  
  public function ajax_get_transporter_info($trnid,$voucher_txn_id=0){
	echo json_encode($this->TransactionModel->transporter_info($trnid,$voucher_txn_id));  	   
   }
   
  public function party_gst_info($party_id){
	$response = $this->TransactionModel->party_gst_info($party_id);
	echo json_encode($response);  
   }
   public function BillNoRequired($voucher_type_id,$series_id){
	$response = $this->TransactionModel->BillNoRequired($voucher_type_id,$series_id);
	echo json_encode($response);
   }
   
    
   
    public function AutoSeriesExists(){
	 if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
	  $voucher_type_id = $this->request->getVar('voucher_type_id');
	  $series_format   = $this->request->getVar('series_format');
	  $series_id       = $this->request->getVar('series_id');
	
	  $response = $this->TransactionModel->AutoSeriesExists($voucher_type_id,$series_format,$series_id);
	  echo json_encode($response);
	 }
	 
   }
   
    public function mc_info($mc_id){
	echo json_encode($this->TransactionModel->mc_info($mc_id));  
   }
   
  public function generate_eway(){
	$response_data  = array();  
	 if($this->request->getMethod() == 'post'){
		$bo_state_code    = $this->session->get('ses_bostecd');  
		$vchrtxnid_val    = unobfuscate_link($this->request->getVar('vchrtxnid'));
		$vchrtypeid_val   = unobfuscate_link($this->request->getVar('vchrtypeid')); 		
		$voucher_txn_id   = $vchrtxnid_val[1];
		$voucher_type_id  = $vchrtypeid_val[1]; 		
		$voucher_info     = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);		
		$party_trnsction  = $this->TransactionModel->get_party_transaction($voucher_txn_id);
		$party_id 	  	  = $party_trnsction['acc_id'];
		$party_gst_info   = $this->TransactionModel->party_gst_info($party_id);
		$narration 		  = $party_trnsction['acc_txn_narr'];
		$item_trnsctions  = $this->TransactionModel->get_item_transactions($voucher_txn_id);
		$sundry_transactions = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);
		$voucher_series   = $voucher_info['comp_vch_series_id'];
		$voucher_no 	  = $voucher_info['comp_vch_no'];
		$voucher_date 	  = date('m/m/Y', strtotime($voucher_info['voucher_date']));
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
		  
	     if($get_ewbmstreqn_data){
		   $gstshipton_info      = $get_ewbmstreqn_data['gstshipton_info']; 
		   if($gstshipton_info){
			  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
			  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
			  $shipto_place      = $gstshipton_info['shipto_place'];
			  $shipto_pin        = $gstshipton_info['shipto_pin'];
			  $shipto_state_code = $gstshipton_info['shipto_state_code'];
		    }		  
		   $gstdispfrm_info       = $get_ewbmstreqn_data['gstdispfrm_info']; 
		   if($gstdispfrm_info){
			  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
			  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
			  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
			  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
			  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
		    }
		}	 
		
      $gsttpt_id               = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info        = $this->TransactionModel->transporter_info($gsttpt_id);	 
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
			$totInvValue=$totInvValue+$itmrow['item_amount']; 
			  
			$cess    = $itmrow['cess_rate']; 
			if($bo_state_code==$statecode){
				$igst    = $itmrow['igst_rate'];
				$cgst    = parseAmount($igst/2);
				$sgst    = parseAmount($igst/2);
				
			}else{
				$igst    = $itmrow['igst_rate'];
				$cgst    = 0;
				$sgst    = 0;
			}			
			
			$tax_amt = $itmrow['tax_amt'];
			$itemList[]=array("productName"=>$itmrow["item_name"],"productDesc"=>$itmrow["description"],"hsnCode"=>$itmrow["tax_hsn_sac"],"quantity"=>$itmrow["item_qty"],
			                  "qtyUnit"=>$itmrow["item_unit"],"cgstRate"=>$cgst,"sgstRate"=>$sgst,"igstRate"=>$igst,"cessRate"=>$cess,
							  "cessNonadvol"=>"0","taxableAmount"=>$tax_amt
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
			$transporterId    = $transporter_info['gsttpt_enrl_id'];
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
        
		$response_data = array("supplyType"=>"O","subSupplyType"=>"1","subSupplyDesc"=>"Transaction",
							   "docType"=>"INV","docNo"=>$outsup_bill_ref_no,"docDate"=>$voucher_date,
							   "fromGstin"=>$comp_gst,"fromTrdName"=>$comp_trade_name,"fromAddr1"=>$company_adrs1,
							   "fromAddr2"=>$company_adrs2,"fromPlace"=>$company_city,"fromPincode"=>$company_pin,
							   "actFromStateCode"=>$comp_state_code,"fromStateCode"=>$comp_state_code,
							   "toGstin"=>$party_gst,"toTrdName"=>$party_trade_name,"toAddr1"=>$party_add1,"toAddr1"=>$party_add2,
							   "toPlace"=>$party_city,"toPincode"=>$party_pin,"actToStateCode"=>$party_state_code,"toStateCode"=>$party_state_code,
							   "transactionType"=>"","otherValue"=>"","totalValue"=>parseAmount($totInvValue+$bsdTotal),
							   "cgstValue"=>parseAmount($cgstValue),"sgstValue"=>parseAmount($sgstValue),"igstValue"=>parseAmount($igstValue),"cessValue"=>parseAmount($cessValue),
							   "cessNonAdvolValue"=>parseAmount($cessNonAdvolValue),"totInvValue"=>parseAmount($totInvValue),"transporterId"=>$transporterId,"transporterName"=>$transporterName,
							   "transDocNo"=>$transDocNo,"transMode"=>$transMode,"transDistance"=>$transDistance,"transDocDate"=>$transDocDate,
							   "vehicleNo"=>$vehicleNo,"vehicleType"=>$vehicleType,"itemList"=>$itemList
                               ); 
		 
	      }	
	
	$jsonfile =  json_encode($response_data);   
    $errors_counter=0;	
	if(!preg_match('/[^a-z_\-0-9]/i',$transporterId)){
        $errors_counter++;
		$errors[]="Transport Document Number (Alphanumeric with / and – are allowed).";
	}
	if(strtotime($voucher_date) > strtotime(date('Y-m-d'))){
		$errors_counter++;
		$errors[]="The Document Date should be less than or equal to current date.";
	}
	if($transport_name==''){
		$errors_counter++;
		$errors[]="Transport name is required.";
	}
	if($transDistance==''){
		$errors_counter++;
		$errors[]="Transport distance is required.";
	}
	if($transDistance >'4000'){
		$errors_counter++;
		$errors[]="Transport distance should be less than 4000 km.";
	}	
	if($transMode=='1' && ($vehicleNo=='' || $vehicleType=='')){ // if Road
		$errors_counter++;
		$errors[]="Vehicle No /VehicleType  is required if mode of transportation is Road.";
	}
	if(($transMode=='2' || $transMode=='3' || $transMode=='4' ) && ($transporterId=='' || $transDocDate=='')){ // if Ship, Air, Rail,
		$errors_counter++;
		$errors[]="transport document number and date should be passed if mode of transportation is Ship, Air, Rail.";
	}	
	if(($transMode=='2' || $transMode=='3' || $transMode=='4' ) && (strtotime($transDocDate)>=strtotime($voucher_date) )){ // if Ship, Air, Rail,
		$errors_counter++;
		$errors[]="The Transport document Date should be greater than equal to document date";
	}
	if($transMode=='4'  && $vehicleType!='O'){ // in Ship if not ODBC,
		$errors_counter++;
		$errors[]="Vehicle type should be ODC. if transport mode is Ship";
	}	
	if($errors_counter>0){
	 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	}
   else
	  return json_encode(['status' => true, 'message' =>'JSON file downloaded','jsonfile'=>$jsonfile,'errors'=>'','filename'=>'eway-invoice.json']);  
  } 
  
  
  
  public function generate_einvoice(){
	  ini_set('precision', 10);
      ini_set('serialize_precision', 10);
	  if($this->request->getMethod() == 'post'){
		$bo_state_code    = $this->session->get('ses_bostecd');  
		$vchrtxnid_val    = unobfuscate_link($this->request->getVar('vchrtxnid'));
		$vchrtypeid_val   = unobfuscate_link($this->request->getVar('vchrtypeid')); 		
		$voucher_txn_id   = $vchrtxnid_val[1];
		$voucher_type_id  = $vchrtypeid_val[1]; 		
		$voucher_info     = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id, $voucher_type_id);
		$party_trnsction  = $this->TransactionModel->get_party_transaction($voucher_txn_id);
		$party_id 	  	  = $party_trnsction['acc_id'];
		$party_gst_info   = $this->TransactionModel->party_gst_info($party_id);
		$narration 		  = $party_trnsction['acc_txn_narr'];
		$item_trnsctions  = $this->TransactionModel->get_item_transactions($voucher_txn_id);
		$voucher_series   = $voucher_info['comp_vch_series_id'];
		$voucher_no 	  = $voucher_info['comp_vch_no'];
		$voucher_date 	  = date('m/m/Y', strtotime($voucher_info['voucher_date']));		
		$billfrm_data     = $this->TransactionModel->billfrm_info();
		$bo_gstin_type    = $this->session->get('bo_gstin_type');
		$bo_id            = $this->session->get('ses_boid');	  
		
		$get_acctgstsum_info   = $this->TransactionModel->get_acctgstsum_info($voucher_txn_id);
	    
		
		
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
		
		$RegRev='N';
		if($gstroutsup_info){
			$outsup_pos         = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];	
            if($outsup_rev_chg=='1')
              $RegRev='Y';            		
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
		  $supply_type         = "1";
	      if($get_ewbmstreqn_data){
		   $supply_type          = $get_ewbmstreqn_data['ewb_supply_type']; 			 
		   $gstshipton_info      = $get_ewbmstreqn_data['gstshipton_info']; 
		  if($gstshipton_info){
			  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
			  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
			  $shipto_place      = $gstshipton_info['shipto_place'];
			  $shipto_pin        = $gstshipton_info['shipto_pin'];
			  $shipto_state_code = $gstshipton_info['shipto_state_code'];
		    }		  
		   $gstdispfrm_info       = $get_ewbmstreqn_data['gstdispfrm_info']; 
		  if($gstdispfrm_info){
			  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
			  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
			  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
			  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
			  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
		    }
		}	 
		
      $gsttpt_id               = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info        = $this->TransactionModel->transporter_info($gsttpt_id);	 
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
	
	
	   if($voucher_type_id=='18' || $voucher_type_id=='3'){ // sale & debit
		if(isset($gstroutsup_info) && $party_gst!='' && $supply_type=='1' && $gstroutsup_info['outsup_rev_chg']=="0"){
			 $invoice_type = 'B2B';				
		   }
	  else if(isset($gstroutsup_info) && $party_gst!='' && $gstroutsup_info['outsup_rev_chg']=="1" && $supply_type=='1'){  
		  	 $invoice_type = 'B2BRCM';							  
	   }			
	 else  if(isset($gstroutsup_info) && $party_gst=='' && $supply_type=='1' && $gstroutsup_info['outsup_rev_chg']=="0"){
	 $invoice_type = 'B2C';				 
	}
	else if($supply_type=='2'){
		$invoice_type = 'EXPWP'; 
	 }			 
	elseif($supply_type=='3'){
		$invoice_type = 'EXPWOP'; 
	 }
	 elseif($supply_type=='13'){
		$invoice_type = 'DE'; 
	 }	
	 elseif($supply_type=='4'){
		$invoice_type = 'SEZWP'; 
	 }
	 elseif($supply_type=='5'){
		$invoice_type = 'SEZWOP'; 
	 }else
	$invoice_type = 'CBW'; 
   }
	else if($voucher_type_id=='11' || $voucher_type_id=='2'){ // purchase & credit
		$gstrinwsup_info     = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);
	  if(isset($gstrinwsup_info) && $party_gst!='' && $supply_type=='1' && $gstrinwsup_info['inwsup_rev_chg']=="0"){
		 $invoice_type = 'B2B';				
	   }
  	 else if(isset($gstrinwsup_info) && $supply_type=='1' && $party_gst!=''&& $gstrinwsup_info['inwsup_rev_chg']=="1"){  
	 	 $invoice_type = 'B2BRCM';							  
	  }					
	 else if(isset($gstrinwsup_info) && $party_gst=='' && $supply_type=='1' && $gstrinwsup_info['inwsup_rev_chg']=="0"){
		 $invoice_type = 'B2C';				 
	  }
	 else if($supply_type=='2'){
		$invoice_type = 'EXPWP'; 
	 }			 
	 elseif($supply_type=='3'){
		$invoice_type = 'EXPWOP'; 
	 }
	 elseif($supply_type=='13'){
		$invoice_type = 'DE'; 
	 }	
	 elseif($supply_type=='4'){
		$invoice_type = 'SEZWP'; 
	 }
	 elseif($supply_type=='5'){
		$invoice_type = 'SEZWOP'; 
	 }else
		$invoice_type = 'CBW';
	}
		
		$access_token  = '1';//einv_getAccessToken();
		if($access_token==''){
		  return json_encode(['status' => false, 'message' =>'Error in generating access token']);
		}else{
		$TranDtls      = array("TaxSch"=>"GST","SupTyp"=>$invoice_type,"RegRev"=>$RegRev,"IgstOnIntra"=>$IgstOnIntra);
		$DocDtls       = array("Typ"=>"INV","No"=>$outsup_bill_ref_no,"Dt"=>$voucher_date);
		$SellerDtls    = array("Gstin"=>$comp_gst,"LglNm"=>$comp_legal_name,"TrdNm"=>$comp_trade_name,"Addr1"=>$company_adrs1,"Addr2"=>$company_adrs2,"Loc"=>$company_city,
		                       "Pin"=>(integer)$company_pin,"Stcd"=>(integer)$comp_state_code,"Ph"=>$comp_phone,"Em"=>$comp_email
							   );
		$BuyerDtls     = array("Gstin"=>$party_gst,"LglNm"=>$party_legal_name,"TrdNm"=>$party_trade_name,"Pos"=>$outsup_pos,"Addr1"=>$party_add1,"Addr2"=>$party_add2,"Loc"=>$party_city,
		                       "Pin"=>(integer)$party_pin,"Stcd"=>(integer)$party_state_code,"Ph"=>$party_phone,"Em"=>$party_email
							   );
		$ItemList  = array();
        $itmcountr = 0;		
		if($item_trnsctions){
		  foreach($item_trnsctions as $item_row){
			$itmcountr++;  
			if($item_row['supply_type']=='2') // Services
			  $IsServc ='Y'; 
		     else
			  $IsServc ='N'; 
		    $UnitPrice   = $item_row["item_amount"]/$item_row["item_qty"];		
			$ItemList[]  = array("SlNo"=>$itmcountr,"PrdDesc"=>$item_row['description'],"IsServc"=>$IsServc,"HsnCd"=>$item_row['item_hsn'],
								 "Barcde"=>"","Qty"=>$item_row["item_qty"],"FreeQty"=>round($item_row["item_qty"]),"Unit"=>$item_row["item_unit"],"UnitPrice"=>parseAmount($UnitPrice),
								 "TotAmt"=>parseAmount($item_row["item_amount"]),"Discount"=>parseAmount("0"),"PreTaxVal"=>parseAmount("0"),"AssAmt"=>parseAmount($item_row["item_amount"]),
								 "GstRt"=>parseAmount($item_row["igst_rate"]),"IgstAmt"=>parseAmount($item_row["igst"]),"CgstAmt"=>parseAmount($item_row["cgst"]),"SgstAmt"=>parseAmount($item_row["sgst"]),"CesRt"=>parseAmount($item_row["cess_rate"]),
								 "CesAmt"=>parseAmount($item_row["cess"]),"CesNonAdvlAmt"=>parseAmount(0),"StateCesRt"=>parseAmount($item_row["cess_rate"]),"StateCesAmt"=>parseAmount($item_row["cess"]),
								 "StateCesNonAdvlAmt"=>0,"OthChrg"=>0,"TotItemVal"=>0,
								 "OrdLineRef"=>"","OrgCntry"=>$company_countrycd,"PrdSlNo"=>$item_row["item_sku"]
								);  
		        }
		    }			   
	
        $ValDtls  = array("AssVal"=>0,"CgstVal"=>$get_acctgstsum_info['CgstVal'],"SgstVal"=>$get_acctgstsum_info['SgstVal'],"IgstVal"=>$get_acctgstsum_info['IgstVal'],
		                  "CesVal"=>$get_acctgstsum_info['CesVal'],"StCesVal"=>0,"Discount"=>0,"OthChrg"=>0,
						  "RndOffAmt"=>0,"TotInvVal"=>$get_acctgstsum_info['TotInvVal'],"TotInvValFc"=>0
						  );
		$DispDtls = array("Nm"=>$comp_name,"Addr1"=>$dispfrm_addr1,"Addr2"=>$dispfrm_addr2,"Loc"=>$dispfrm_place,"Pin"=>(integer)$dispfrm_pin,"Stcd"=>(integer)$dispfrm_state_code);	
		$ShipDtls = array("Gstin"=>$party_gst,"LglNm"=>$party_legal_name,"TrdNm"=>$party_trade_name,"Addr1"=>$shipto_addr1,"Addr2"=>$shipto_addr2,
		                  "Loc"=>$shipto_place,"Pin"=>(integer)$shipto_pin,"Stcd"=>(integer)$shipto_state_code);
		$ExpDtls       = array("ShipBNo"=>"","ShipBDt"=>"","Port"=>"","RefClm"=>"","ForCur"=>"","CntCode"=>"");
		$response_data = array( "TranDtls"   => $TranDtls,
							    "DocDtls"    => $DocDtls,
						        "SellerDtls" => $SellerDtls,
						        "BuyerDtls"  => $BuyerDtls,
						        "ItemList"   => $ItemList,
						        "ValDtls"    => $ValDtls,
						        "DispDtls"   => $DispDtls,
						        "ShipDtls"   => $ShipDtls,
						        "ExpDtls"    => $ExpDtls
							   );							  
		//$response = einv_generateIRN($access_token,$response_data);
		
		$jsonfile =  json_encode($response_data,JSON_UNESCAPED_SLASHES);
		return json_encode(['status' => true, 'message' =>'JSON file downloaded','jsonfile'=>$jsonfile,'filename'=>'e-invoice.json']);					   
		}		
	  }
  } 
  public function ajax_save_transporter_route(){       
    if($this->request->getMethod() == 'post'){
		   $gstin_id            =  $this->request->getVar('gsttpt_id');
           $transporter_doc_no  =  $this->request->getVar('transporter_doc_no');
           $transport_mode      =  $this->request->getVar('transport_mode');
           $transport_distance  =  $this->request->getVar('transport_distance');
           $transport_doc_date  =  $this->request->getVar('transport_doc_date');
           $vehicle_no          =  $this->request->getVar('vehicle_no');
           $vehicle_type        =  $this->request->getVar('vehicle_type');
           $state_code          =  $this->request->getVar('state_code');
		   $voucher_txn_id      =  $this->request->getVar('voucher_txn_id');
		   if($transport_distance!='' && $vehicle_no!=''){
				$transport_doc_date = date('Y-m-d',strtotime($transport_doc_date));	
				// save into ewbpartbdt table
				
				
				$ewbpartbdt_data = array("gsttpt_id"=>$gstin_id,"conso_ewb_id"=>0,"ewb_id"=>0,"trans_veh_no"=>$vehicle_no,
							 "trans_veh_type"=>$vehicle_type,"trans_mode"=>$transport_mode,
							 "trans_doc_no"=>$transporter_doc_no,"trans_doc_date"=>$transport_doc_date,
							 "trans_dist"=>$transport_distance,"trans_frm_state_code"=>$state_code,"voucher_txn_id"=>$voucher_txn_id);	
				$this->TransactionModel->add_ewbpartbdt($ewbpartbdt_data);			 
			}
			
		   $transporter_dropdown = $this->TransactionModel->transporter_dropdown();
           $transporter_dropdown['0']='Add New';			   
           echo form_dropdown('transporter', $transporter_dropdown,$gstin_id,'id="transporter" class="form-control"'); 
        }	  
  }
   
  public function ajax_save_transporter(){       
       if($this->request->getMethod() == 'post'){
		 
           $transporter_id      =  $this->request->getVar('transporter_id');
		   $gstin_id            =  $this->request->getVar('gstin_id');
           $transporter_name    =  $this->request->getVar('transporter_name');
           $transporter_doc_no  =  $this->request->getVar('transporter_doc_no');
           $transport_mode      =  $this->request->getVar('transport_mode');
           $transport_distance  =  $this->request->getVar('transport_distance');
           $transport_doc_date  =  $this->request->getVar('transport_doc_date');
           $vehicle_no          =  $this->request->getVar('vehicle_no');
           $vehicle_type        =  $this->request->getVar('vehicle_type');
           $state_code          =  $this->request->getVar('state_code');
		   $isedit              =  $this->request->getVar('isedit');		   
		   $voucher_txn_id      =  $this->request->getVar('voucher_txn_id');
		   
		   
		   
		  
			if($isedit=="0") // means fresh entry
		   {	
		   $exists_transporter = $this->TransactionModel->exists_transporter_master($transporter_name);
		   if($exists_transporter >0){
			 echo "0";  
		   }else{
		   
		   $transporter_data = array("transporter_id"=>$transporter_id,"gstin_id"=>$gstin_id,
			                         "transporter_name"=>$transporter_name
									 );									
		   $transporter_master_id     = $this->TransactionModel->add_transporter_master($transporter_data); 
		   $transporter_dropdown      = $this->TransactionModel->transporter_dropdown();
           $transporter_dropdown['0'] = 'Add New';			   
           echo form_dropdown('transporter', $transporter_dropdown,$transporter_master_id,'id="transporter" class="form-control"'); 
		   }  
		   }
		   
		   else if($isedit=="1") // update entry
		   {
			   $transporter_master_id              =  $this->request->getVar('gsttpt_id');
			   
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
			$transporter_dropdown = $this->TransactionModel->transporter_dropdown();
           $transporter_dropdown['0']='Add New';			   
           echo form_dropdown('transporter', $transporter_dropdown,$transporter_master_id,'id="transporter" class="form-control"');              
			   
		   }   
		 
           
       }	  
   }
   
   public function ajax_item_units_list()
   {
   		if($this->request->getMethod() == 'post'){

   			$item_id  =  $this->request->getVar('item_id');
   			$list = $this->ReportsModel->item_unit_list($item_id);

   			$final_units[''] = 'All Units';
   			foreach($list as $key => $value){
			  
				$final_units[$value['unit_id']] = $value['unit_name'];
				
			}

			echo form_dropdown('unitid',$final_units, '',' id="unitid" style="font-size:18px;border-radius: 6px 0px 0px 6px;" class="selectwidget form-control"'); 
   		}
   }

   public function get_item_unit_list()
   {
   		if($this->request->getMethod() == 'post'){

   			$item_id  =  $this->request->getVar('item_id');

   			$list = $this->ReportsModel->item_unit_list($item_id);

   			return json_encode(['status' => true, 'list' => $list]); 
   		}
   }

    public function itemqtybalance($itemid,$voucher_date,$unit_id){
       echo  json_encode($this->TransactionModel->get_item_balance($itemid,$voucher_date,$unit_id));
       die();
     }

    public function get_item_last_balance($item_id, $unit_id, $mc_id, $date){

    	$date = date('Y-m-d', strtotime($date));
      	$response = $this->TransactionModel->get_item_last_balance($item_id, $unit_id, $mc_id, $date);

      	return json_encode($response);
    }
    public function get_item_last_qty_balance(){

    	if($this->request->getMethod() == 'post'){
    		$item_id = $this->request->getVar('item_id');
    		$unit_id = $this->request->getVar('unit_id');
    		$mc_id = $this->request->getVar('mc_id');
    		$date = $this->request->getVar('date');
    		$voucher_txn_id = $this->request->getVar('voucher_txn_id');

    		$date = date('Y-m-d', strtotime($date));
	      	$balance = $this->TransactionModel->get_item_last_balance($item_id, $unit_id, $mc_id, $date, $voucher_txn_id);

	      	$response['status'] = true;
	      	$response['balance'] = $balance;
	      	return json_encode($response);
    	}
    	
    }

    public function get_all_item_last_qty_balances()
    {
    	if($this->request->getMethod() == 'post'){
    		$item_balances = [];
    		$items_data = $this->request->getVar('items_data');
    		$voucher_txn_id = $this->request->getVar('voucher_txn_id');

    		foreach ($items_data as $key => $value) {
    			$date = date('Y-m-d', strtotime($value['date']));
	      		$balance = $this->TransactionModel->get_item_last_balance($value['item_id'], $value['unit_id'], $value['mc_id'], $date, $voucher_txn_id);
				
				$itemvalmst_info   = $this->TransactionModel->itemvalmst_info($value['item_id']);
				if($itemvalmst_info)
				   $item_mrp = $itemvalmst_info['item_mrp'];
				else 
				   $item_mrp =0;
			   
	      		$item_balances[] = [
	      			"item_id" => $value['item_id'],
	      			"unit_id" => $value['unit_id'],
					"item_mrp"=> $item_mrp,					
	      			"balance" => $balance,
	      		];
    		}

    		return json_encode(['status' => true, 'item_balances' => $item_balances]);
    	}
    }
   
   public function accountbalance($accountid,$voucher_date){
      echo  $this->TransactionModel->get_account_balance($accountid,$voucher_date);
       die();
     }
   
    public function get_items()
		{
				$search = $_GET['term'];
				$data = $this->TransactionModel->ajax_items($search);
				return json_encode($data);
		}
	public function get_accounts()
		{
				$search = $_GET['term'];
				$array = [];

				if(isset($_GET['voucher_type_id']) && $_GET['voucher_type_id'] == '1'){
					$array = [23];
				}

				$data = $this->TransactionModel->ajax_accounts($search,$array);
				return json_encode($data);
		}
	
	public function get_acc_bsd_accounts()
		{
				$search = $_GET['term'];
				$data = $this->TransactionModel->ajax_acc_bsd_accounts($search);
				return json_encode($data);
		}
		
		
	public function get_billsundry_items()
    { 
        $search = $_GET['term'];
				$data = $this->TransactionModel->ajax_billsundry_items($search);
				return json_encode($data);
    }
    public function getCc()
    {
        if($this->request->getMethod() == 'post'){
            $data = $this->TransactionModel->get_cc();
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }

    public function getPr()
    {
        if($this->request->getMethod() == 'post'){
            $data = $this->TransactionModel->get_pr();

            $item_accounts = [];
            $item_data = $this->request->getVar('item_data');
            $type = $this->request->getVar('type');
            if(!empty($item_data)){
                foreach($item_data as $key => $value) {

                    $item_info  =  $this->TransactionModel->get_item_info($value['item_id']);
                    if($type == 'SALE')
                       $account_id  =  $item_info['item_sales_acc'];
                    else if($type == 'PURCHASE')
                        $account_id  =  $item_info['item_pur_acc'];
                    else{
                        if($value['item_drcr'] == 'C')
                            $account_id  =  $item_info['item_sales_acc'];
                        else
                          $account_id  =  $item_info['item_pur_acc'];  
                    }     
                    
                    $amount = floatval($value['item_total_amount']);

                    $index = array_search($account_id, array_column($item_accounts, 'account_id'));
                    if($index != ''){
                        $item_accounts[$index]['amount'] += $amount;
                    }
                    else{
                        $account = $this->TransactionModel->get_account_info($account_id);
                        $account_name = $account['acc_name'];

                        $item_accounts[] = [
                            "account_id"    => $account_id,
                            "account_name"  => $account_name,
                            "drcr"          => $value['item_drcr'],
                            "amount"        => $amount,
                            "acc_type"      => 'acc',
                        ];
                    }                
                }
            } 

            echo json_encode(['status' => true, 'data' => $data, 'item_accounts' => $item_accounts]);
        }
    }
    
     public function getItemTracking()
    {
        if($this->request->getMethod() == 'post'){
            $items_id_array = $_POST['items_id_array'];
            $data = [];
            foreach($items_id_array as $item_id)
            {
                $data[] = $this->TransactionModel->get_item_tracking_list($item_id);
            }
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
    
	
	public function ajax_tracking_items_list(){
		
		 if($this->request->getMethod() == 'post'){
            $tracking_no = $_POST['tracking_no'];
           echo  $data = $this->TransactionModel->tracking_items_trackno($tracking_no);
           
        }
	}
	
	
	 public function getItemBatch()
    {
        if($this->request->getMethod() == 'post'){
            $items_id_array = $_POST['items_id_array'];
            $data = [];
            foreach($items_id_array as $item_id)
            {
                $data[] = $this->TransactionModel->get_item_batch_list($item_id);
            }
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }
	
    public function getAccountBillRefs()
    {
        if($this->request->getMethod() == 'post'){
            $account_id_array = $_POST['account_id_array'];
            $data = [];
            foreach($account_id_array as $account_id)
            {
                $data[] = $this->TransactionModel->get_account_bill_refs($account_id);
            }
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }

    public function deleteBillReference()
    {
        if($this->request->getMethod() == 'post'){
            $bills_ref_id = $_POST['bills_ref_id'];

            $status = $this->TransactionModel->delete_bill_master($bills_ref_id);
            
            
            echo json_encode(['status' => $status]);
        }
    }
    
    public function get_all_item_balances(){
     if($this->request->getMethod() == 'post' && $this->request->isAjax()){  

    		$voucher_date = $this->request->getVar('voucher_date');
    		$item_id_array = $this->request->getVar('item_id_array');
			if(!$item_id_array){
			 $status=false;	
				$account_balances = [];
			}
			else{
			
			$status=true;
    		$account_balances = [];
    		foreach ($item_id_array as $item_id => $unit_id) {
    			$item_balance =$this->TransactionModel->get_item_balance($item_id,$voucher_date,$unit_id);
    			$account_balances[] = [
    				 'item_id' => $item_id,
    			     'item_unit_id' => $unit_id,
    				 'balance' => $item_balance
    			];
    		}
			}
    		return json_encode(['status' => $status, 'item_balances' => $account_balances]);
    	}   
        
    }

    public function get_all_account_balances() 
    {
    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){  

    		$voucher_date = $this->request->getVar('voucher_date');
    		$account_id_array = $this->request->getVar('account_id_array');
			if(!$account_id_array){
			 $status=false;	
				$account_balances = [];
			}
			else{
			
			$status=true;

    		$account_balances = [];
    		foreach ($account_id_array as $account_id) {

    			$account_balance = $this->TransactionModel->get_account_balance($account_id,$voucher_date);
    			$account_balances[] = [
    				'account_id' => $account_id,
    				'balance' => $account_balance
    			];
    		}
        }
    		return json_encode(['status' => $status, 'account_balances' => $account_balances]);
    	}
    }

    public function getAccountBills()
    {
        if($this->request->getMethod() == 'post'){
            $account_id = $_POST['account_id'];

            $acc_name = '';
            $acc_op_bal = 0;
            $acc_op_bal_drcr = 'D';

            $get_account_info = $this->TransactionModel->get_account_info($account_id);
            if($get_account_info)
            	$acc_name = $get_account_info['acc_name'];

            $account_opn_balance_info = $this->TransactionModel->account_opn_balance_info($account_id);
            if($account_opn_balance_info){
            	$acc_op_bal = $account_opn_balance_info['acc_op_bal'];
            	if($acc_op_bal < 0){
            		$acc_op_bal = abs($acc_op_bal);
            		$acc_op_bal_drcr = 'C';
            	}
            	else{
            		$acc_op_bal = $acc_op_bal;
            		$acc_op_bal_drcr = 'D';
            	}
            }
            
            $data['acc_name'] = $acc_name;
            $data['acc_op_bal'] = $acc_op_bal;
            $data['acc_op_bal_drcr'] = $acc_op_bal_drcr;
            //$data['grid'] = $this->TransactionModel->get_account_all_bill_refs($account_id);
            $data['grid'] = $this->TransactionModel->get_account_all_bill_refs_new($account_id,$acc_op_bal);
            
            
            echo json_encode(['status' => true, 'data' => $data]);
        }
    }

    public function setAccountBills()
    {
        if($this->request->getMethod() == 'post'){
            $bills_json_array = $this->request->getVar('bills_json_array');
            $account_id = $this->request->getVar('account_id');

            $this->TransactionModel->reset_acc_bill_refs_op_balance($account_id);

            foreach ($bills_json_array as $key => $value) {

               $value['bill_due_date'] = validate_date_by_fy($value['bill_due_date']);

            	if($value['bills_ref_id']){
            		$this->TransactionModel->update_bill_master($value['bills_ref_id'], $value);
            	}
            	else{
                    $value['bills_status'] = 'pending';
                    $bill_ref_id = $this->TransactionModel->add_bill_master($value);
            	}
            	
            }
            
            echo json_encode(['status' => true, 'message' => 'Data Updated']);
        }
    }


  public function cmd()
    {
		$search = $this->request->getVar('search');
		echo json_encode($this->CommonModel->get_cmd($search));
	}
}