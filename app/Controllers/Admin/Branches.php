<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BranchesModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
class Branches extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text','custom']);
			$this->BranchesModel     = new BranchesModel();
			$this->CommonModel       =  new CommonModel();		
			$this->auth_session      = new auth_session();
			$this->auth_session->user_restrict();
			$this->auth_session->is_company_opened();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().getenv('AdminPath');
			$this->folder_path   = getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->company_id    =  $this->session->get('ses_company_id');
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->compositionSupplyOptions = [
            ''  => 'Choose Composition Supply',
            '1' => 'MANUFACTURER',
            '2' => 'TRADER',
            '3' => 'RESTAURANT SERVICES',
            '4' => 'OTHER SERVICES'
        ];
    }
   
     public  function ajax_branches_view()
	 {
		echo $response =  $this->BranchesModel->ajax_branch_list();	
		
	 } 
	 
	 public function validate_remove_gstin($boid,$gstinid){
	 $response = $this->BranchesModel->verify_gstin_vouchers_exists($boid,$gstinid);
	 echo json_encode($response);
	 }
	 
	 
   public function add_more_gstin($branch_id)
    {
		$branch_state_code      = sprintf( '%02d', $this->session->get('ses_bostecd'));
		$company_id             = $this->company_id;
		$states_lists           = $this->UniversalModel->show_states_lists();
		if(!$branch_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		 try{
		
		if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){				
            $comp_gstin         = $this->request->getVar('mdl_comp_gstin');
			$comp_gst_jurisd_st = $this->request->getVar('mdl_comp_gst_jurisd_st');
			$comp_gst_jurisd_ct = $this->request->getVar('mdl_comp_gst_jurisd_ct');
			$comp_tan_jurisd    = $this->request->getVar('mdl_comp_tan_jurisd');   
			$legal_name         = $this->request->getVar('mdl_legal_name');
			$trade_name         = $this->request->getVar('mdl_trade_name');
			$gstintype          = $this->request->getVar('mdl_gstintype');						
			$gstin_state_code   = $this->request->getVar("mdl_gstin_state_code");
			$gstin_wefdate      = $this->request->getVar("mdl_gstin_wef_date");
		    $gstin_wefdate      = date('Y-m-d',strtotime($gstin_wefdate));
		  	$gstin_inactivedate    ="";
			
			$gstin_wefdate_for_db      = empty($gstin_wefdate) ? null : $gstin_wefdate;
		    $gstin_inactivedate_for_db = empty($gstin_inactivedate) ? null : $gstin_inactivedate;


			$rules = [				
				'mdl_comp_gstin' => [
					'label'  => 'GSTIN',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter GSTIN',
					   ]]				  			   
			       ];			
            if(!$this->validate($rules)){
             	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
			
		     $validate_gstin = json_decode($this->BranchesModel->validate_addmore_branch_gstin($company_id, $comp_gstin,$gstintype,$gstin_wefdate,$branch_id),true);	
			 if(!$validate_gstin['status'])
			  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$validate_gstin['message']]]);	
			 else{				
			 
			  preg_match('/\d+/', $comp_gstin, $comp_gstin_matches);
					  if (!empty($comp_gstin_matches)) {
							$firstTwoDigits = substr($comp_gstin_matches[0], 0, 2); // Get first two digits
					        if (array_key_exists($firstTwoDigits, $states_lists)) {
						     	$gstinstate_code_val =  $firstTwoDigits;
						    } else {
						   	    $gstinstate_code_val = $branch_state_code;
						   }
					  }
					  else
						  $gstinstate_code_val = $branch_state_code;
				   
			
			 $gstin_update_data = array(
				                         "hobo_gstin"         => $comp_gstin,
				                         "hobo_gstin_jurisd_st"  => $comp_gst_jurisd_st,
										 "hobo_gstin_jurisd_ct"  => $comp_gst_jurisd_ct,
										 "hobo_gstin_type"    => $gstintype,
										 "hobo_gstin_wef_act"      => $gstin_wefdate_for_db,
										 "hobo_gstin_inact_date" =>  $gstin_inactivedate_for_db,
										 "hobo_gstin_legal_name"   => $legal_name,
										 "hobo_gstin_trade_name"   => $trade_name,
										 "hobo_gstin_state_code"   => $gstinstate_code_val
										 );
				// --- Data for the 'hobogstinn' table ---
			$hobogstinn_data = [
				'hobo_id' => $branch_id,
				'cmp_id' => $this->company_id, // Assuming $this->company_id is available
				'hobo_gstin' => $gstin_update_data['hobo_gstin'],
				'hobo_gstin_type' => $gstin_update_data['hobo_gstin_type'],
				'hobo_gstin_state_code' => $gstin_update_data['hobo_gstin_state_code']
			];

			// --- Data for the 'hobogstdet' table ---
			// This will be used AFTER the first insert
			
			$hobogstdet_data = [
				'hobo_id' => $branch_id,
				'cmp_id' => $this->company_id, // Assuming $this->company_id is available
				'hobo_gstin_jurisd' => $gstin_update_data['hobo_gstin_jurisd_st'], // Mapping 'st' to the main jurisdiction field
				'hobo_gstin_jurisd_ct' => $gstin_update_data['hobo_gstin_jurisd_ct'],
				'hobo_gstin_legal_name' => $gstin_update_data['hobo_gstin_legal_name'],
				'hobo_gstin_trade_name' => $gstin_update_data['hobo_gstin_trade_name'],
				'hobo_gstin_wef_act' => $gstin_update_data['hobo_gstin_wef_act'] ?? NULL,
				'hobo_gstin_inact_date' => $gstin_update_data['hobo_gstin_inact_date'] ?? NULL
			];
			
				
				$this->BranchesModel->add_gstinmastr($hobogstinn_data,$hobogstdet_data);
				return json_encode(['status' => true, 'message' => 'Data Inserted']);
			 }	 
		 }					 									
	 }

		 }
		 catch (\Throwable $e) {
				return  json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				// Get line, file and stack trace
                log_message('error', 'DB Error: ' . $e->getMessage());
                log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());
                log_message('error', 'Trace: ' . $e->getTraceAsString());
    	}
		 
			
    }
	
	public function modify_gstin($branch_id,$gstin_id)
    {
		$branch_state_code      = sprintf( '%02d', $this->session->get('ses_bostecd'));
		$company_id             = $this->company_id;
		$states_lists           = $this->UniversalModel->show_states_lists();
		if(!$branch_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		 
		if(!$gstin_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		$branch_gsttins_info = $this->BranchesModel->branch_gsttins_info($branch_id);
		if(!$branch_gsttins_info){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}   
		
		if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){	
		
			$comp_gstin         = $this->request->getVar('comp_gstin');
			$hobo_gstin_sub_type  = $this->request->getVar('mdl_prm_cmp_suply');
			$comp_gst_jurisd_st = $this->request->getVar('comp_gst_jurisd_st');
			$comp_gst_jurisd_ct = $this->request->getVar('comp_gst_jurisd_ct');
			$legal_name         = $this->request->getVar('legal_name');
			$trade_name         = $this->request->getVar('trade_name');
			$gstin_state_code   = $this->request->getVar("gstin_state_code");
			$gstin_wefdate      = $this->request->getVar("gstin_wef_date");
			if($gstin_wefdate !='')
			  $gstin_wefdate    = date('Y-m-d',strtotime($gstin_wefdate));
			 else
			  $gstin_wefdate    = "";
		  
			$gstin_inactivedate = $this->request->getVar("gstin_inactive_date");
			if($gstin_inactivedate !='')
			  $gstin_inactivedate    = date('Y-m-d',strtotime($gstin_inactivedate));
			 else
			  $gstin_inactivedate    = "";
		  
	
			$inactive_date_for_db = empty($gstin_inactivedate) ? null : $gstin_inactivedate;

			
			$rules = [				
				'comp_gstin' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter GSTIN',
					   ]]				  			   
			       ];			
            if(!$this->validate($rules)){
              // $this->message_output->set_error($this->validator->listErrors());
            	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
			
			     $validate_modify_gstin = json_decode($this->BranchesModel->validate_modify_branch_gstin($gstin_id,$comp_gstin,$gstin_wefdate,$branch_id),true);		
			     $validate_gstin = json_decode($this->BranchesModel->validate_gstin_inactive_date($gstin_id,$gstin_wefdate,$gstin_inactivedate),true);	
				
				 if($validate_modify_gstin['status']==false)
				  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$validate_modify_gstin['message']]]);	
				
				  else if($validate_gstin['status']==false)
				   return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$validate_gstin['message']]]);	
				 else{		
				     preg_match('/\d+/', $comp_gstin, $comp_gstin_matches);
					  if (!empty($comp_gstin_matches)) {
							$firstTwoDigits = substr($comp_gstin_matches[0], 0, 2); // Get first two digits
					        if (array_key_exists($firstTwoDigits, $states_lists)) {
						     	$gstinstate_code_val =  $firstTwoDigits;
						    } else {
						   	    $gstinstate_code_val = $branch_state_code;
						   }
					  }
					  else
						  $gstinstate_code_val = $branch_state_code;
					      $this->session->set('ses_bostecd',$branch_state_code);
				        
					$hobogstinn_data = [
						'hobo_gstin' => $comp_gstin,
						'hobo_gstin_state_code' => $gstinstate_code_val,
						'hobo_gstin_sub_type'    => $hobo_gstin_sub_type
						// Note: gstin_type is usually not editable after creation, but if it is, it goes here.
						// 'hobo_gstin_type' => $gstintype 
					    ];

					// --- Data for the 'hobogstdet' table ---
					$hobogstdet_data = [
						'hobo_gstin_jurisd_st' => $comp_gst_jurisd_st,
						'hobo_gstin_jurisd_ct' => $comp_gst_jurisd_ct,
						'hobo_gstin_legal_name' => $legal_name,
						'hobo_gstin_trade_name' => $trade_name,
						'hobo_gstin_wef_act' => $gstin_wefdate,
						'hobo_gstin_inact_date' => $inactive_date_for_db // Use the null-checked variable
					];
		
			
			   $response = $this->BranchesModel->update_gstin_info($gstin_id, $hobogstinn_data, $hobogstdet_data);
			   if($response)
				    return json_encode(['status' => true, 'message' => 'Data Updated']);
				else 
					  return json_encode(['status' => false, 'message' => 'Error']);
			        }
		    	}					 									
		}	 
		
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['CountryDropdown']       = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']        = $this->CommonModel->StatesDropdown();		
		$data['base_url']              = $this->base_url;		
		$data['gstin_id']              = $gstin_id;	
		$data['branch_id']             = $branch_id;	
		$data['gsttin_info']           = $branch_gsttins_info;
		$data['gstintypes_list']       = gstintypes_list();
		$data['states_lists']          = $states_lists;
		$data['compositionSupplyOptions'] =  $this->compositionSupplyOptions;
		$data['branch_state_code']        =  $branch_state_code;
		
	    return view($this->folder_path.'branches/edit_gstin',$data);		
    }
	
   public function index()
    {		
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'branches/view',$data);		
    } 

   public function add()
   {
	 	 $branch_state_code      = sprintf( '%02d', $this->session->get('ses_bostecd'));
		 $StatesDropdown         = $this->UniversalModel->show_states_lists();
		 $company_id             = $this->company_id;
		 $states_json_array      = json_encode($StatesDropdown);
		 if($this->request->getMethod() == 'POST'){   
		  	$account_name       = $this->request->getVar('account_name'); 
			$account_alias      = $this->request->getVar('account_alias');		
			$account_zone       = $this->request->getVar('account_zone');
			$adrs1              = $this->request->getVar('acc_adrs1');
			$adrs2              = $this->request->getVar('acc_adrs2');
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('state_id');
			$city               = $this->request->getVar('acc_city');
			$pincode            = $this->request->getVar('acc_pincode');
			$opening_date       = $this->request->getVar('opening_date');			
			$closing_date       = $this->request->getVar('closing_date');
			$comp_gstin         = $this->request->getVar('comp_gstin');
			$comp_gst_jurisd_st = $this->request->getVar('comp_gst_jurisd_st');
            $comp_gst_jurisd_ct = $this->request->getVar('comp_gst_jurisd_ct');						
			$legal_name         = $this->request->getVar('legal_name');
			$trade_name         = $this->request->getVar('trade_name');
			$gstintype          = $this->request->getVar('gstintype');
			$mdl_prm_cmp_suply  = $this->request->getVar('mdl_prm_cmp_suply');
			$gstin_wef_date     = $this->request->getVar('gstin_wef_date');
			$gstin_state_code   = $this->request->getVar('gstin_state_code');		
			$comp_tan_jurisd    = $this->request->getVar('comp_tan_jurisd');			
			$bo_tan             = $this->request->getVar('comp_tan');
			$bo_tan_jurid       = $this->request->getVar('tan_jurisd');
			$tan_wefdate        = $this->request->getVar('tan_wef_date');
			if($tan_wefdate !='')
			  $tan_wefdate    = date('Y-m-d',strtotime($tan_wefdate));
			 else
			  $tan_wefdate    = NULL;
		  
			$tan_inactivedate   = $this->request->getVar('tan_inactive_date');
			if($tan_inactivedate !='')
			  $tan_inactivedate    = date('Y-m-d',strtotime($tan_inactivedate));
			 else
			  $tan_inactivedate    = NULL;
		  
			$rules = [				
				'account_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]]				  			   
			       ];
			
            if(!$this->validate($rules)){
			   $errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);	
            }else{			
			 $gstin_number   = $comp_gstin[0];
			 $gstin_type     = $gstintype[0];
			 $mdl_prm_cmp_suply     = $mdl_prm_cmp_suply[0];
			 $wef_date       = $gstin_wef_date[0];
			 $data           = array("bo_name"=>ucwords(clean($account_name)),"bo_alias"=>$account_alias);
		     $validate_gstin = ['status'=>true];//json_decode($this->BranchesModel->validate_new_branch_gstin($company_id, $gstin_number,$gstin_type,$wef_date,$data),true);	
		     if($validate_gstin['status']==false)
			    return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$validate_gstin['message']]]);	
			 else{				
			    $state_info      =  $this->CommonModel->get_state_info($country_id,$state_id);
				if($state_info)
				  $state_code    = $state_info['state_code'];
			   else
				 $state_code    = '';
			     $insert_data   = [
						'cmp_id'        => $this->company_id,
						'hobo_name'     => ucwords(clean($account_name)), //remove special characters
						'hobo_alias'    => $account_alias,
						'hobo_op_date'  => date('Y-m-d',strtotime($opening_date)),
						'hobo_cl_date'  => ($closing_date!='')?date('Y-m-d',strtotime($closing_date)):NULL,
						'hobo_zone'     => $account_zone,
						'hobo_addr1'    => $adrs1,
						'hobo_addr2'    => $adrs2,
						'hobo_country'  => $country_id,
						'hobo_state'    => $state_id,
						'hobo_city'     => $city,						
						'hobo_pin_zip'  => $pincode,
						'hobo_zone'     => $account_zone,
						'hobo_tan'      => $bo_tan,
						'hobo_tan_jurisd' => $bo_tan_jurid,
						'hobo_tan_wef_act'=> $tan_wefdate,
						'hobo_tan_inact_date'=> $tan_inactivedate
					    ];				
			 $response = $this->BranchesModel->add_account($insert_data);		
			if(!$response['status']){
				return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => array($response['message'])]);
        	}
			else{
			  $bo_id = $response['bo_id'];
			  $gstin_insert_data = array();
			 if($comp_gstin){
			 foreach($comp_gstin as $key =>  $comp_gstin_value){	
			   if($comp_gstin_value!=''){
				   preg_match('/\d+/', $comp_gstin_value, $comp_gstin_matches);
					  if (!empty($comp_gstin_matches)) {
							$firstTwoDigits = substr($comp_gstin_matches[0], 0, 2); // Get first two digits
					        if (array_key_exists($firstTwoDigits, $StatesDropdown)) {
						     	$gstinstate_code_val = $firstTwoDigits;
						    } else {
						   	    $gstinstate_code_val = $branch_state_code;
						   }
					  }
				   
				   $gstintype_val       = $gstintype[$key];
				   $hobo_gstin_sub_type       = $mdl_prm_cmp_suply[$key];
				   
				   if($key==0){					   
				      if($gstintype_val!='' && ($gstintype_val==1 || $gstintype_val==2) )
					    $this->session->set('bo_gstin_type',$gstintype_val);
					  else
					    $this->session->set('bo_gstin_type',1);
		             }
				    $gst_jurisd_st_val      = $comp_gst_jurisd_st[$key];
					$gst_jurisd_ct_val      = $comp_gst_jurisd_ct[$key];
					$legal_name_val         = $legal_name[$key];
					$trade_name_val         = $trade_name[$key];			
					$gstin_wef_date_val     = ($gstin_wef_date[$key]!='')?date('Y-m-d',strtotime($gstin_wef_date[$key])):'';   					 
					$gstin_insert_data      =  array(	
			          'cmp_id'                => $this->company_id,
                      'hobo_id'               => $bo_id,
					  'hobo_gstin'            => trim($comp_gstin_value),
					  'hobo_gstin_type'       => $gstintype_val,
					  'hobo_gstin_sub_type'   => $hobo_gstin_sub_type,
					  'hobo_gstin_state_code' => $gstinstate_code_val,
					  'hobo_gstin_jurisd_st'  => $gst_jurisd_st_val,
                      'hobo_gstin_jurisd_ct'  => $gst_jurisd_ct_val,				                    
                      'hobo_gstin_wef_act'    => $gstin_wef_date_val,
					  'hobo_gstin_inact_date' => NULL,					                    
                      'hobo_gstin_legal_name' => $legal_name_val,
					  'hobo_gstin_trade_name' => $trade_name_val
 					  );
			       }
			    }
			}	
				$this->BranchesModel->save_gstinmastr($gstin_insert_data);
			    $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'add',
			            'log_field_id' => $bo_id,
			            'log_field_name' => $account_name,
			            'log_field_type' => 'branch',
			        ];
			    // $this->LogModel->add_log($log);
			   return json_encode(['status' => true,'redirectto'=>base_url().'admin/branches/modify/'.$bo_id, 'message' => 'Data Inserted']); 		
			    }				 
	    	}					 									
		 }
	   }	 
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;	
        $data['CountryDropdown']        = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']         = $StatesDropdown;
		$data['base_url']               = $this->base_url;
		$data['company_addresses']      = $this->BranchesModel->get_compadrs_info();
		$data['gstintypes_list']        = gstintypes_list();
		$data['branch_state_code']      =  $branch_state_code;
		$data['states_json_array']      =  $states_json_array;
		$data['compositionSupplyOptions']      =  $this->compositionSupplyOptions;
		$data['zones']                  = array(''=>'Choose','1'=>'Central','2'=>'East','3'=>'North','4'=>'South','5'=>'West');
	    return view($this->folder_path.'branches/add',$data);		
    }

	
	public function modify($account_id)
    {
		 $branch_state_code      = sprintf( '%02d', $this->session->get('ses_bostecd'));
		 
		if(!$account_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$account_info = $this->BranchesModel->account_info($account_id);
		if(!$account_info){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}   
		$branch_gsttins_info = $this->BranchesModel->branch_gsttins_info($account_id);
		
		if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){	
		
			$account_name       = $this->request->getVar('account_name'); 
			$account_alias      = $this->request->getVar('account_alias');		
			$account_zone       = $this->request->getVar('account_zone');
			$adrs1              = $this->request->getVar('acc_adrs1');
			$adrs2              = $this->request->getVar('acc_adrs2');
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('state_id');
			$city               = $this->request->getVar('acc_city');
			$pincode            = $this->request->getVar('acc_pincode');
			$opening_date       = $this->request->getVar('opening_date');	
			$closing_date       = $this->request->getVar('closing_date');
            $comp_gstin         = $this->request->getVar('comp_gstin');
			$mdl_prm_cmp_suply  = $this->request->getVar('mdl_prm_cmp_suply');
			$comp_tan           = $this->request->getVar('comp_tan');
			$comp_gst_jurisd_st = $this->request->getVar('comp_gst_jurisd_st');
			$comp_gst_jurisd_ct = $this->request->getVar('comp_gst_jurisd_ct');
			$comp_tan_jurisd    = $this->request->getVar('comp_tan_jurisd');   
			$legal_name         = $this->request->getVar('legal_name');
			$trade_name         = $this->request->getVar('trade_name');
			$gstintype          = $this->request->getVar('gstintype');			
			$bo_tan             = $this->request->getVar('comp_tan');
			$bo_tan_jurid       = $this->request->getVar('tan_jurisd');
			$tan_wefdate        = $this->request->getVar('tan_wef_date');
			if($tan_wefdate !='')
			  $tan_wefdate    = date('Y-m-d',strtotime($tan_wefdate));
			 else
			  $tan_wefdate    = NULL;
		  
			$tan_inactivedate   = $this->request->getVar('tan_inactive_date');
			if($tan_inactivedate !='')
			  $tan_inactivedate    = date('Y-m-d',strtotime($tan_inactivedate));
			 else
			  $tan_inactivedate    = NULL;
		  
		  if($gstintype!='' && ($gstintype=='1' || $gstintype=='2') ){
		     $this->session->set('bo_gstin_type',$gstintype);
			 $comp_gstin_type =$gstintype; 
		  }
		     else{
			  $this->session->set('bo_gstin_type',1);
			  $comp_gstin_type =1;
			 }
		  
			$gstin_state_code   = $this->request->getVar("gstin_state_code");
			$gstin_wefdate      = $this->request->getVar("gstin_wef_date");
			if($gstin_wefdate !='')
			  $gstin_wefdate    = date('Y-m-d',strtotime($gstin_wefdate));
			 else
			  $gstin_wefdate    = "";
		  
		  
			$gstin_inactivedate = $this->request->getVar("gstin_inactive_date");
			if($gstin_inactivedate !='')
			  $gstin_inactivedate    = date('Y-m-d',strtotime($gstin_inactivedate));
			 else
			  $gstin_inactivedate    = "";
			
			$rules = [				
				'account_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]]				  			   
			       ];			
            if(!$this->validate($rules)){
             	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
			
				$state_info      =  $this->CommonModel->get_state_info($country_id,$state_id);
				
				
				if($state_info)
				  $state_code    = $state_info['state_code'];
			   else
				 $state_code    = 0;
			    $state_code = sprintf( '%02d', $state_code);
			 
				$update_data   = [
						'hobo_name'     => ucwords(clean($account_name)), //remove special characters
						'hobo_alias'    => $account_alias,
						'hobo_op_date'  => date('Y-m-d',strtotime($opening_date)),
						'hobo_cl_date'  => ($closing_date!='')?date('Y-m-d',strtotime($closing_date)):NULL,
						'hobo_zone'     => $account_zone,
						'hobo_addr1'    => $adrs1,
						'hobo_addr2'    => $adrs2,
						'hobo_country'  => $country_id,
						'hobo_state'    => $state_id,
						'hobo_city'     => $city,						
						'hobo_pin_zip'  => $pincode,
						'hobo_zone'     => $account_zone,
						'hobo_tan'      => $bo_tan,
						'hobo_tan_jurisd' => $bo_tan_jurid,
						'hobo_tan_wef_act'=> $tan_wefdate,
						'hobo_tan_inact_date'=> $tan_inactivedate
					    ];
					$this->session->set('ses_bostecd',$state_code);	
					$this->session->set('ses_boname',$account_name);
					$account_acc_id=0;
					if(isset($account_info['acc_id']))
						$account_acc_id = $account_info['acc_id'];
					
			         $response = $this->BranchesModel->update_account($update_data,$account_id,$account_acc_id);
			        if(!$response['status']){        				
        				  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
        			}
        			else{
        			     $account_id = $response['bo_id'];
						 $tanmastern_update_data = array("hobo_tan"=>$bo_tan,"hobo_tan_jurisd"=>$bo_tan_jurid,
												         "hobo_tan_wef_act"=>$tan_wefdate,
														 "hobo_tan_inact_date"=>$tan_inactivedate);
				         $this->BranchesModel->update_tanmastern($tanmastern_update_data,$account_id);
				
    			         $log = [
    				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    				            'log_date' => date('Y-m-d'),
    				            'log_time' => date('H:i:s'),
    				            'log_action_tags' => 'update',
    				            'log_field_id'    => $account_id,
    				            'log_field_name'  => $account_name,
    				            'log_field_type'  => 'branch',
    				        ];
    				    //$this->LogModel->add_log($log);
						return json_encode(['status' => true, 'message' => 'Data Updated']);    				
        			}
				 			 
		    	}					 									
		}	 
		
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['account_info']          = $account_info;       
        $data['CountryDropdown']       = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']        = $this->CommonModel->StatesDropdown();		
		$data['base_url']              = $this->base_url;		
		$data['account_id']            = $account_id;	
		$data['branch_gsttins_info']   = $branch_gsttins_info;
		$data['tan_info']              = $this->BranchesModel->get_tan_info($account_id);		
		$data['gstintypes_list']       = gstintypes_list();
		$data['states_lists']          = $this->UniversalModel->show_states_lists();		
		$data['company_addresses']     = $this->BranchesModel->get_compadrs_info();
		$data['branch_state_code']     = $branch_state_code;
		$data['compositionSupplyOptions']      =  $this->compositionSupplyOptions;
		$data['zones']                 = array(''=>'Choose','1'=>'Central','2'=>'East','3'=>'North','4'=>'South','5'=>'West');
	    return view($this->folder_path.'branches/edit',$data);		
    }
	
	public function mark_branch_ho(){
		
		if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){	
		  $ids       = array_unique($this->request->getVar('hoid'));
		  if(!$ids)
		return json_encode(['status' => false, 'message' => 'Something wrong', 'reload' => 1]);
		  foreach($ids as $bo_id){			        
		            $this->BranchesModel->mark_branch_ho($bo_id);
		  }
		   return json_encode(['status' => true, 'message' => 'Marked as HO', 'reload' => 1]);
		}
	}
	
	public function remove_branches(){
		
		if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){
			$ids       = array_unique($this->request->getVar('hoid'));
			$errors = [];
			foreach($ids as $bo_id){
		     $stat    = true;
			 $bo_info = $this->BranchesModel->get_branch_info($bo_id);
			 if($bo_info['mark_ho']==1){
				$stat = false; 
				array_push($errors, 'Failed! HO "'.$bo_info['hobo_name'].'" can not be deleted');
			   }else{
				 $this->BranchesModel->remove_single_branches($bo_id);  
			   }			 
			}
		if(count($errors)){
		         return json_encode(['status' => false, 'message' => 'Not Deleted', 'errors' => $errors]);
		  }
		 
		 
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  		
		}	
		 
	  }

}