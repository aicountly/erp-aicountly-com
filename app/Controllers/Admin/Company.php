<?php
namespace App\Controllers\Admin;
use App\Models\Admin\CompanyModel;
use App\Models\FYModel;
use App\Models\Admin\CurrencyModel;

use App\Models\Admin\AccountsModel;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\BillsundryModel;

use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\externaldb;
use App\Helpers\AWSHelper;

class Company extends BaseController
{
  protected $CompanyModel;
  protected $CurrencyModel;
  protected $AccountsModel;
  protected $ItemsModel;
  protected $BillsundryModel;
  protected $FYModel;
  protected $CommonModel;
  protected $auth_session;
  protected $externaldb;
  protected $base_url;
  protected $folder_path;
  protected $session;
  protected $company_id;
  protected $erp_db;
  protected $aicountly_db;
  function __construct()
    {
	    helper(['form', 'url', 'AWS','mail_helper']);
		$this->CompanyModel  = new CompanyModel();
		$this->CurrencyModel = new CurrencyModel();		
		
		$this->AccountsModel   = new AccountsModel();
		$this->ItemsModel      = new ItemsModel();
		$this->BillsundryModel = new BillsundryModel();		
		
		$this->FYModel       = new FYModel();	
        $this->CommonModel   = new CommonModel();			
		$this->auth_session  = new auth_session();
        $this->externaldb    = new externaldb();		
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session 	     = \Config\Services::session();		
		$this->company_id    = $this->session->get('ses_company_id');
		$this->erp_db        = $this->externaldb->erp_db();
		$this->aicountly_db  = $this->externaldb->aicountly_db();
		$this->fy_id         = $this->session->get('ses_comp_fy_id');		
    } 
 
 public function item($item_id)
  {
  	$response = $this->CompanyModel->GetItemBalance($item_id);
  	echo $response;
  }

   public function index()
    {   
        $data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;		
		return view($this->folder_path.'company/view',$data);		
    } 
	
  public function send_delfy_email($otp)
    {  
    	$company_profile_info = $this->CommonModel->company_profile_info();
		$company_email_id     = $company_profile_info['user_regdemail'];
		$company_fname        = $company_profile_info['user_firstname'];
		if($company_profile_info['user_regdmobile']!=''){
		$company_mobile       = $company_profile_info['user_regdmobile'];
		
		}
		else if($company_profile_info['user_wamobile']!=''){
		$company_mobile       = $company_profile_info['user_wamobile'];
		
		}
		if($company_email_id!=''){
        $subject = "FY Deletion OTP Confirmation";		        
		$html = "Hi, your otp is: ".$otp;
        $mailConfig = [
                        'mail_from_email'   => env('EMAIL_FROM_ADDRESS'),
                        'mail_from_name'    => env('EMAIL_FROM_NAME'),
                        'mail_to_email'     => $company_email_id,
                        'mail_to_name'      => ($company_fname!='')?ucwords($company_fname):'User',
                        'mail_subject'      => $subject,
                        'mail_body'         => $html,
                    ];             
					
        if(sendEmail($mailConfig)){
			$em   = explode("@",$company_email_id);
			$name = implode('@', array_slice($em, 0, count($em)-1));
			$len  = floor(strlen($name)/2);
			$mask_email = substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em); 
			
           return ['status' => 200, 'message' => 'OTP Sent','otp_send_to'=>$mask_email];
         }
		 
		}
         else{
          return ['status' => 400, 'message' => 'Failed!', 'hidden_message' => 'Mail not sent'];
        }
		die();
    }	
	
  public function confirmotp(){
	  if($this->request->getMethod() == 'POST'){
		  $rules = [				
				'delotp' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'OTP is required',
				   ],
			  	],
				
			 ];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }			
			$delotp     = trim($this->request->getVar('delotp'));
			$savedotp   = trim($this->session->get('delfy_otp'));
			if($savedotp!=$delotp){
			return json_encode(['status' => false, 'message' => 'OTP not matched']);	
			}else{
			 return json_encode(['status' => true, 'message' => 'OTP Verified']);
			}
		die();	
	  }
  }	
  
  public function sending_delfy_otp(){
	  if($this->request->getMethod() == 'POST'){
	     $otp = rand(100000, 999999); //generates random otp
		 $this->session->remove('delfy_otp');
		 $this->session->set('delfy_otp', $otp);
		 
         $response = $this->send_delfy_email($otp);
		 $response['dd']= $otp; 
         echo json_encode($response);
        	die();
	  }else
	   die('--');
  }	
	
  public function ajax_change_company_fy($comp_fy_id)
{
    // ──────────────────────────────────────────────────────────────
    // SESSION CHECK — return clean JSON if expired
    // ──────────────────────────────────────────────────────────────
    if (!$this->session->get('ses_company_id')) {
        return $this->response->setStatusCode(401)->setJSON([
            'status'  => 'session_expired',
            'message' => 'Your session has expired. Please login again.'
        ]);
    }

    $fn_start = microtime(true);
    $logTag   = '[FYChange][fy=' . $comp_fy_id . '][comp=' . $this->company_id . ']';
    log_message('info', "$logTag ── START ──");

    // ──────────────────────────────────────────────────────────────
    // 1. FETCH FY INFO (single DB call)
    // ──────────────────────────────────────────────────────────────
    $t1 = microtime(true);

    $fy_info = $this->CommonModel->fy_info($comp_fy_id, $this->company_id);

    if (empty($fy_info)) {
        log_message('error', "$logTag FY info not found for fy_id=$comp_fy_id");
        return $this->response->setStatusCode(400)->setJSON([
            'status'  => '0',
            'message' => 'Invalid Financial Year selected.'
        ]);
    }

    log_message('info', "$logTag Q1-FYInfo => " . round(microtime(true) - $t1, 4) . 's');

    // ──────────────────────────────────────────────────────────────
    // 2. SET ALL SESSION VALUES AT ONCE (single write instead of 8)
    // ──────────────────────────────────────────────────────────────
    $beg_date = $fy_info['fy_beg_date'];
    $end_date = $fy_info['fy_end_date'];

    // Remove old values first
    $this->session->remove([
        'ses_comp_fy_id',
        'ses_company_fy_beginning',
        'ses_company_fy_end',
        'ses_dflt_val_method',
        'allcompfy',
        'allcompfym'
    ]);

    // Set all new values in one call
    $this->session->set([
        'ses_comp_fy_id'          => $comp_fy_id,
        'ses_company_fy_beginning' => $beg_date,
        'ses_company_fy_end'       => $end_date,
        'ses_dflt_val_method'      => $fy_info['def_val_method'],
        'allcompfy'               => json_encode([
            date('Y', strtotime($beg_date)),
            date('Y', strtotime($end_date))
        ]),
        'allcompfym'              => json_encode([
            date('m', strtotime($beg_date)),
            date('m', strtotime($end_date))
        ]),
    ]);

    // ──────────────────────────────────────────────────────────────
    // 3. LOAD LISTS & CREATE JSON FILES
    //    (3 independent DB calls — logged individually)
    // ──────────────────────────────────────────────────────────────
    $company_id = $this->company_id;

    // --- Items ---
    $t2 = microtime(true);
    $items_list = $this->ItemsModel->company_all_items();
    //log_message('info', "$logTag Q2-ItemsList => " . count($items_list) . " rows | " . round(microtime(true) - $t2, 4) . 's');

    // --- Accounts ---
    $t3 = microtime(true);
    $accounts_list = $this->AccountsModel->company_all_accounts();
    //log_message('info', "$logTag Q3-AccountsList => " . count($accounts_list) . " rows | " . round(microtime(true) - $t3, 4) . 's');

    // --- Bill Sundry ---
    $t4 = microtime(true);
    $company_all_bsd = $this->BillsundryModel->company_all_bsd();
    //log_message('info', "$logTag Q4-BillSundry => " . count($company_all_bsd) . " rows | " . round(microtime(true) - $t4, 4) . 's');

    // --- Write JSON files ---
    $t5 = microtime(true);
    CreateJsonFile($comp_fy_id, $company_id, 'acc', $accounts_list);
    CreateJsonFile($comp_fy_id, $company_id, 'itm', $items_list);
    CreateJsonFile($comp_fy_id, $company_id, 'bsd', $company_all_bsd);
    log_message('info', "$logTag FileWrite => 3 JSON files | " . round(microtime(true) - $t5, 4) . 's');

    // ──────────────────────────────────────────────────────────────
    // TIMING SUMMARY
    // ──────────────────────────────────────────────────────────────
    $fn_total = round(microtime(true) - $fn_start, 4);
    log_message('info', "$logTag ── END ── TOTAL: {$fn_total}s");

    // ─────────────────────────────────────────────────────────────
    // RETURN PROPER JSON RESPONSE
    // ──────────────────────────────────────────────────────────────
    return $this->response->setJSON([
        'status'  => '1',
        'message' => 'FY Changed'
    ]);
}

 public function migrate_items_batch() {
    $offset = $this->request->getVar('offset');
    echo json_encode($this->FYModel->migrate_items_batch($offset));
}


  public function new_fy(){	
	    $type   = $this->request->getVar('type');	
		if($type == 'create_fy'){ 			 	
			$fy_id_info = $this->FYModel->createFY();
			if($fy_id_info['status'] == false){
			  return json_encode(['status' => false,'fy_id'=>0,'message'=>'Kindly retry last FY.','percentage'=>$fy_id_info['percentage']]);	
			}
			$fy_id = $fy_id_info['fy_id'];
	 	  return json_encode(['status' => 'done','percentage'=>$fy_id_info['percentage']]);	
	 	}
		
		if($type == 'accounts'){
			$respinfo = $this->FYModel->account_tables();
	 	  if(count($respinfo['errors'])){
	 	  	return json_encode(['status' => false,  'message'=>'Something went wrong', 'errors' => $respinfo['errors'],'percentage'=>$respinfo['percentage']]);
	 	  }
	 	  return json_encode(['status' => 'done','percentage'=>$respinfo['percentage']]);
	 	}
		if($type == 'material_center'){
			$respinfo = $this->FYModel->material_center_tables();
	 	  if(count($respinfo['errors'])){
	 	  	return json_encode(['status' => false,  'message'=>'Something went wrong', 'errors' => $respinfo['errors'],'percentage'=>$respinfo['percentage']]);
	 	  }
	 	  return json_encode(['status' => 'done','percentage'=>$respinfo['percentage']]);
	 	}
		if($type == 'bill_sundry'){
			$respinfo = $this->FYModel->bill_sundry_tables();
	 	  if(count($respinfo['errors'])){
	 	  	return json_encode(['status' => false,  'message'=>'Something went wrong', 'errors' => $respinfo['errors'],'percentage'=>$respinfo['percentage']]);
	 	  }
	 	  return json_encode(['status' => 'done','percentage'=>$respinfo['percentage']]);
	 	}
		
		if($type == 'cost_center'){
			$respinfo = $this->FYModel->cost_center_tables();
	 	  if(count($respinfo['errors'])){
	 	  	return json_encode(['status' => false,  'message'=>'Something went wrong', 'errors' => $respinfo['errors'],'percentage'=>$respinfo['percentage']]);
	 	  }
	 	  return json_encode(['status' => 'done','percentage'=>$respinfo['percentage']]);
	 	}
		if($type == 'bill_by_bill'){
			$respinfo = $this->FYModel->bill_by_bill_tables();
	 	  if(count($respinfo['errors'])){
	 	  	return json_encode(['status' => false,  'message'=>'Something went wrong', 'errors' => $respinfo['errors'],'percentage'=>$respinfo['percentage']]);
	 	  }
	 	  return json_encode(['status' => 'done','percentage'=>$respinfo['percentage']]);
	 	}
		if($type == 'project'){
			$respinfo = $this->FYModel->project_tables();
	 	  if(count($respinfo['errors'])){
	 	  	return json_encode(['status' => false,  'message'=>'Something went wrong', 'errors' => $respinfo['errors'],'percentage'=>$respinfo['percentage']]);
	 	  }
	 	  return json_encode(['status' => 'done','percentage'=>$respinfo['percentage']]);
	 	}
		
		if($type == 'items'){
			$respinfo = $this->FYModel->item_tables();
	 	  if(count($respinfo['errors'])){
	 	  	return json_encode(['status' => false,  'message'=>'Something went wrong', 'errors' => $respinfo['errors'],'percentage'=>$respinfo['percentage']]);
	 	  }
	 	  return json_encode(['status' => 'done','percentage'=>$respinfo['percentage']]);
	 	}
		
	 	if($type == 'final'){			
	 	  $fy_id            = $this->FYModel->newFYid();
		  		
		  $this->FYModel->updateFYStatus($fy_id);
		  $this->session->remove('ses_comp_fy_id');
		  $this->session->remove('ses_company_fy_beginning');
		  $this->session->remove('ses_company_fy_end');
		  $this->session->set('ses_comp_fy_id',$fy_id); 
		  
		  $company_last_fy       =  $this->CommonModel->company_last_fy($this->company_id);
		  $CmpProfileInfo		=  $this->CommonModel->GetCmpProfileInfo($this->company_id);
		  $def_val_method        =  $company_last_fy['def_val_method'];
		  $this->session->set('ses_company_fy_beginning',$company_last_fy['fy_beg_date']);
		  $this->session->set('ses_company_fy_end',$company_last_fy['fy_end_date']);
		  $this->session->set('ses_dflt_val_method',$def_val_method);
		  $this->session->set('ses_cmp_prf_id', $CmpProfileInfo['erp_acs_prof_id']);

		  $accounts_list  = $this->AccountsModel->company_all_accounts();
		  CreateJsonFile($fy_id,$this->company_id,'acc',$accounts_list);
			
			
		  $items_list  = $this->ItemsModel->company_all_items();
		  CreateJsonFile($fy_id,$this->company_id,'itm',$items_list);
			
		  $company_all_bsd  = $this->BillsundryModel->company_all_bsd();
		  CreateJsonFile($fy_id,$this->company_id,'bsd',$company_all_bsd);	
	 	  return json_encode(['status' => 'done','percentage'=>100]);
	 	}
	}

	public function delete_fy($fy_id)
	{
		$response = $this->FYModel->delete_fy($fy_id);
		echo "<pre>";print_r($response);exit;
	}
	
	public function remove_company_fy(){
	 $all_fy_list     = $this->CommonModel->company_all_fy_list();
     $company_last_fy = $this->CommonModel->company_last_fy($this->company_id);	 
	 if(count($all_fy_list) >1 && $company_last_fy>0){		 
	   $last_comp_fy_id = $company_last_fy['cmpfymastr_id'];	
	   $response = $this->FYModel->delete_fy($last_comp_fy_id);
	   if($response['status']){
	   $company_last_fy = $this->CommonModel->company_last_fy($this->company_id);
	   $last_comp_fy_id = $company_last_fy['cmpfymastr_id'];
       $def_val_method  =  $company_last_fy['def_val_method'];	 
      
	   $fy_info = $this->CommonModel->get_comp_fy_info($last_comp_fy_id,$this->company_id);	  
	 
	   $this->session->set('ses_company_fy_beginning',$fy_info['fy_beg_date']); 
	   $this->session->set('ses_company_fy_end',$fy_info['fy_end_date']);
	   
	   $adrs_info =  $this->CommonModel->get_comp_ho_adrs_info($this->company_id,0);
		  
		  if($adrs_info){			  
			 $bo_state_code = sprintf( '%02d', ($adrs_info['hobo_gstin_state_code'])?$adrs_info['hobo_gstin_state_code']:0); 
			 $boid          = $adrs_info['hobo_id']; 
			 $boname        = $adrs_info['hobo_name']; 
			 $bo_gstin_type = $adrs_info['hobo_gstin_type'] ?? 1;
		  }else{
			  $bo_state_code = 0;		  
			  $boid          = 1;
			  $bo_gstin_type = 1;
			  $boname        = "N/A";
		     } 
		
			$this->session->set('bo_gstin_type',$bo_gstin_type);		
			$this->session->set('ses_boid',$boid);
			$this->session->set('ses_boname',$boname);
			$this->session->set('ses_bostecd',$bo_state_code);			
			$this->session->remove('ses_comp_fy_id');
		  
		    $this->session->remove('ses_company_fy_beginning');
		    $this->session->remove('ses_company_fy_end');
		    $this->session->set('ses_comp_fy_id',$last_comp_fy_id); 
		    $this->session->set('ses_dflt_val_method',$def_val_method);
			
            echo json_encode(array("status"=>"1","message"=>"FY removed successfully."));
	   }
	   else
	     echo json_encode(array("status"=>"0","message"=>"There is an error in deleting FY."));
	   }
	   else
	  echo json_encode(array("status"=>"0","message"=>"There is an error in deleting FY."));
	 die();
	}

	public function missing_fy($fy_id)
	{
		$response = $this->FYModel->missing_fy($fy_id);
		echo "<pre>";print_r($response);exit;
	}
   
   public function modify($sel_company_id=NULL)
    {
          if($sel_company_id==NULL){
     	    $company_id     = $this->company_id;	
     	    $redirect_url   = $this->base_url.'dashboard';
          }
     	  else{
     	    $company_id     = $sel_company_id;
     	    $redirect_url   = base_url().'companies';
     	  }
	    
	      $company_fy_info = $this->CommonModel->get_company_max_fy_info($company_id);	
	      if(!$company_fy_info){
	        return redirect()->to($this->base_url.'companies');
			die;
	      }
	     $company_info = $this->CompanyModel->get_info($company_id);
		 $company_other_info = $this->CompanyModel->get_other_info($company_id);
		 $company_ro_info = $this->CompanyModel->get_company_address_info($company_id,1);
         $company_co_info = $this->CompanyModel->get_company_address_info($company_id,2);
		
		 if($this->session->get('ses_boid')!='')
			$bo_id = $this->session->get('ses_boid');
		 else 
		   $bo_id =1;
	   
		
		 $comp_fy_id   =  $company_fy_info['cmpfymastr_id'];	
		 
		 if($this->request->getMethod() == 'POST'){		     	     	     
		    $comnpany_name     = $this->request->getVar('comnpany_name'); 
		    $print_name        = $this->request->getVar('print_name');
			$short_name        = $this->request->getVar('short_name');
		    $adrs1             = $this->request->getVar('adrs1');
	        $adrs2             = $this->request->getVar('adrs2');			
			$country_id        = $this->request->getVar('country_id'); 
		    $state_id          = $this->request->getVar('state_id');
	        $city              = $this->request->getVar('city');
			$industry_id       = $this->request->getVar('industry_id');
	        $nature_ofwork     = $this->request->getVar('nature_ofwork');
	        $comp_tel          = $this->request->getVar('cmp_tel');
			$comp_email        = $this->request->getVar('cmp_email');
			$comp_mobile       = $this->request->getVar('cmp_mobile');
			$comp_wa_mobile    = $this->request->getVar('cmp_wa_mobile');
			$ro_add1           = $this->request->getVar('ro_add1');
	        $ro_add2           = $this->request->getVar('ro_add2');			
			$ro_country        = $this->request->getVar('ro_country'); 
		    $ro_state          = $this->request->getVar('ro_state');			
	        $ro_pin	           = $this->request->getVar('ro_pin');
			$ro_city           = $this->request->getVar('ro_city');
			$co_add1           = $this->request->getVar('co_add1');
			$co_add2           = $this->request->getVar('co_add2');			
			$co_country        = $this->request->getVar('co_country');
			$co_state          = $this->request->getVar('co_state');
			$co_city           = $this->request->getVar('co_city');
			$co_pin            = $this->request->getVar('co_pin');	
            $valmethod_id      = $this->request->getVar('valmethod_id');			
			$rules = [				
				'comnpany_name' => [
					'label'  => 'Company Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please company name',
					   ]]				  			   
			     ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{
                $update_data = [
						   	'cmp_name'          => $comnpany_name,
							'cmp_print_name'    => $print_name,
							'cmp_short_name'    => $short_name,
							'cmp_last_accessed' => date("Y-m-d H:i:s")
							];					
				$this->CompanyModel->update_company($company_id,$update_data);
			
				$details_data = [						  
							'cmp_work_nature'   => (int)($nature_ofwork ?: 0),							
						 	'cmp_tel'           => $comp_tel,
							'cmp_email'         => $comp_email,
							'cmp_mobile'        => $comp_mobile,
						 	'cmp_wa_mobile'     => $comp_wa_mobile,							
							'cmp_industry'      => (int)($industry_id ?: 0),
							];   
				$this->CompanyModel->update_company_details($company_id,$details_data);
					   					   
				$address_data = [
					        'ro_add1'            => $ro_add1,
							'ro_add2'            => $ro_add2,
							'ro_city'            => $ro_city,
							'ro_pin'             => $ro_pin,
							'ro_country'         => $ro_country,
							'ro_state'           => $ro_state,
							'co_add1'            => $co_add1,
							'co_add2'            => $co_add2,
						 	'co_city'            => $co_city,	
							'co_state'           => $co_state,
							'co_pin'             => $co_pin,
						 	'co_country'         => $co_country,
							'def_val_method'     => $valmethod_id
					        ];
						
				$this->CompanyModel->update_company_address($company_id,$address_data);			   
					  
				$this->session->remove('ses_company_short_name');
				$this->session->set('ses_company_short_name', $short_name);
				
				$this->session->set('ses_dflt_val_method', $valmethod_id);
					  
				$this->message_output->set_success('Record Updated successfully');					
				return redirect()->to($redirect_url);
				die;				    
		    }					 									
		}			
		
		$data['company_info']        = $company_info;	
		$data['company_id']          = $company_id;	
		$data['message_output']      = $this->message_output;
		$data['fin_year_list']       = $this->CommonModel->calculateFiscalYearForDate(date('m'));
		$data['folder_path']         = $this->folder_path;
		$data['base_url_path']       = $this->base_url;	
		$data['base_url']            = $this->base_url;	
        $data['StatesDropdown']      = $this->CommonModel->StatesDropdown();	
        $data['CountryDropdown']     = $this->CommonModel->CountryDropdown();
        $data['sel_company_id']      = $sel_company_id;
		$data['company_ro_info']     = $company_ro_info;
		$data['company_co_info']     = $company_co_info;
		$data['company_other_info']  = $company_other_info;		
		$data['industry_type_list']  = $this->CommonModel->industry_type_dropdown();
        $data['natureof_work_list']  = $this->CommonModel->natureof_work_dropdown();
        $company_last_fy             = $this->CommonModel->company_last_fy($company_id);
		$all_fy_list                 = $this->CommonModel->all_fy_list();
		$last_comp_fy_id             = $company_last_fy['cmpfymastr_id'];		
		$data['last_comp_fy_id']     = $last_comp_fy_id;
		$data['all_fy_list']         = $all_fy_list;		
		$data['company_last_fy']     = $company_last_fy;		
		$data['current_fy_id']       = $this->session->get('ses_comp_fy_id');
		$data['startendfy']          = date('y',strtotime($company_last_fy['fy_beg_date'])).'-'.date('y',strtotime($company_last_fy['fy_end_date']));		
		return view($this->folder_path.'company/edit',$data);		
  }

  public function upload_file()
	{
	 
  
		$rules = [ 

			'logo_file' => [
				'rules'  => 'uploaded[logo_file]|ext_in[logo_file,png,jpg,jpeg]|max_size[logo_file,1024]',
				'errors' => [
					'uploaded' 	=> 'File is required',
					'ext_in' => 'File must be of type jpg, jpeg or png',
					'max_size' 	=> 'Size must be less than 1024kb'
			  ],
			]
		];

		if(!$this->validate($rules)){
			$errors = $this->validator->getErrors();
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		}

		$comp_id = $this->request->getVar('comp_id');

		$file = $this->request->getFile('logo_file');
			if (!$file->isValid()) {
			return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Invalid File']]);
		}

		$file_ext = $file->getExtension();

		//(20) digit + (4 or 5)ext, max- 30
		$_name = 'logo_'.rand(1000,9999).'_'.time();
		$file_name = $_name.'.'.$file_ext;
		$original_size = $file->getSize();
		

    $folder_path = WRITEPATH.'uploads/comp_logo_temp';
    if(!file_exists($folder_path)) {
		  mkdir($folder_path, 0755);
		}
    $file_path =  $folder_path.'/'.$file_name;
    $file->move($folder_path, $file_name);

    $destination = $folder_path.'/'.$_name.'.jpg';
  
    $reduced_size = filesize($file_path);

    // echo '<br>original_size '.$original_size;
    // echo '<br>reduced_size '.$reduced_size;
    // exit;
	       
    $bucketName = 'usersetup.aicountly.com.active';
    $s3Key = 'uploads/'.$file_name;

    $aws = new AWSHelper('https://s3.de.perf.cloud.ovh.net/');

    try {
      if(!$aws->uploadFile($bucketName, $file_path, $s3Key))
      {
      	unlink($file_path);
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Internal Error']]);
      }
    } catch (\Exception $e) {
    	unlink($file_path);
      return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$e->getMessage()]]);
    } 

    unlink($file_path);

    $data = [
        'doc_name' 					=> $file_name,
        'doc_size_org' 				=> $original_size,
        'doc_size_optimize' 		=> $reduced_size,
        'doc_module_shortcode' 	    => 'CMPLOGO',
        'doc_status' 				=> 1,
        'doc_file_ext' 				=> $file_ext,
        'uuid' 						=> auth()->id,
        'comp_id'					=> comp()->id,
        'upload_date' 				=> date('Y-m-d'),
        'usr_config_id' 			=> 'ERP001000000'
    ];
    $doc_id = $this->CompanyModel->saveDocument($data);

   
    $this->CompanyModel->updateCompanyMaster($comp_id,['cmp_logo' => $doc_id]);
    
    return json_encode(['status' => true, 'message' => 'File Uploaded']);
	}

	function logo($comp_id = null)
	{
		if(!$comp_id)
			$comp_id = comp()->id;
		
		
		$file = $this->CompanyModel->getCompLogo($comp_id);

		if($file){
			$file_name = $file['name'];
			$file_ext = $file['ext'];

			$bucketName = 'usersetup.aicountly.com.active';
    	$s3Key = 'uploads/'.$file_name;

    	$aws = new AWSHelper('https://s3.de.perf.cloud.ovh.net/');

    	$response = $aws->download($bucketName, $s3Key);
    	if($response){
			 	$mime = mime($file_ext);
				header("Content-type: ".$mime);
				echo $response["Body"];
			}
		}
		exit;  
	}
	function aasasqw()
	{
		 // Compress the file
	        $zip = new \ZipArchive();
	        $zipFileName =  WRITEPATH . 'uploads/' . $file->getName() . '.zip';
	        if ($zip->open($zipFileName, \ZipArchive::CREATE) !== TRUE) {
	            echo 'Error: Cannot create ZIP archive.';
	            return;
	        }
	        
	        // Add the file to the ZIP archive
	        if (!$zip->addFile($file->getTempName(), $file->getName())) {
	            echo 'Error: Failed to add file to ZIP archive.';
	            $zip->close();
	            return;
	        }

	        // Close the ZIP archive
	        $zip->close();
	}

}