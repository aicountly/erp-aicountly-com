<?php
namespace App\Controllers\Admin;
use App\Models\Admin\MaterialCentersModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;


class Material_centres extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->MaterialCentersModel = new MaterialCentersModel();		
		$this->LogModel        = new ERPLogModel();
        $this->CommonModel     = new CommonModel();				
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().getenv('AdminPath');
		$this->auth_session->is_company_opened();
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
    }
 	  
   public function list_centres()
    {
		$data['message_output']  = $this->message_output; 
		$data['folder_path']     = $this->folder_path;		
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session; 		
        $data['centre_list']     = $this->MaterialCentersModel->ajax_centres_list();			
		return view($this->folder_path.'material_centres/list_centres',$data);		
    }    
    
    
   public function ledger_detail($mat_cent_id,$start_date=NULL,$end_date=NULL)
    {
       if(!$mat_cent_id) 
	    return redirect()->to($this->base_url.'material_centres/list_centres'); 
	
      $ses_comp_fy_id          =  $this->session->get('ses_comp_fy_id');	
	  $mc_name                 =  $this->MaterialCentersModel->get_mc_name($mat_cent_id);	  
	  $mc_transactions         =  $this->MaterialCentersModel->load_mc_transactions($mat_cent_id,$start_date,$end_date);	
	  $data['mc_name']         =  $mc_name;
	  $data['mc_transactions'] =  $mc_transactions;
     return view($this->folder_path.'material_centres/mc_ledger_view',$data);   
    }   
   	
	 
   public function add_centres()
    {	
	     $bo_address = $this->CommonModel->get_comp_ho_adrs_info($this->company_id,$this->bo_id);		
		 if($this->request->getMethod() == 'POST'){	
		    $mat_cent_name      = $this->request->getVar('mat_cent_name'); 
			$mat_cent_grp       = $this->request->getVar('mat_cent_grp'); 
			$mat_cent_alias     = $this->request->getVar('mat_cent_alias'); 
			$mat_cent_print     = $this->request->getVar('mat_cent_print'); 
			$mat_cent_add1      = $this->request->getVar('mat_cent_add1'); 
			$mat_cent_add2      = $this->request->getVar('mat_cent_add2'); 
			$mat_cent_city      = $this->request->getVar('mat_cent_city'); 
			$mat_cent_pin       = $this->request->getVar('mat_cent_pin'); 
			$mat_cent_state     = $this->request->getVar('mat_cent_state');
			$mat_cent_country   = $this->request->getVar('mat_cent_country'); 
			
			$rules = [				
				    'mat_cent_name' => [
					'label'  => 'Centre Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter centre name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $errors = $this->validator->getErrors();
	        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{	
				try{	
				 $crs_mst_is_primary=0;
				 $insert_data   = [
						'cmp_id'              => $this->company_id,
						'mat_cent_name'       => $mat_cent_name,
						'mat_cent_alias'      => $mat_cent_alias,
						'mat_cent_print_name' => $mat_cent_print
					   ];										
			    $centre_id = $this->MaterialCentersModel->add_centres($insert_data);
			    if($centre_id=="-1"){
				 	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Centre name already exists.']]);
			     }	
				else if($centre_id=="-2"){
				 	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Alias name already exists.']]);
			     }
				else if($centre_id=="-3"){
				 	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Print name already exists.']]);
			     }	
				 else{			
				    $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>7,"crs_mst_id"=>$centre_id,"under_crs_mst_id"=>$mat_cent_grp,
											  "crs_mst_parent_id"=>0,"under_main_id"=>0,
											  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
					$this->MaterialCentersModel->add_undercrsmt($mst_insert_data);
					$insert_matcentdet_data   = [
						'cmp_id'            => $this->company_id,
						'mat_cent_id'       => $centre_id,
						'mat_cent_addr1'    => $mat_cent_add1,
						'mat_cent_addr2'    => $mat_cent_add2,
						'mat_cent_city'     => $mat_cent_city,
						'mat_cent_state'    => $mat_cent_state,
						'mat_cent_pin_zip'  => $mat_cent_pin,						
						'mat_cent_country'  => $mat_cent_country							
					   ];	
					 $this->MaterialCentersModel->add_centre_details($insert_matcentdet_data);  
					 return json_encode(['status' => true, 'message' => 'Data Inserted']);
				    }	
				 }
				 catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				  } 
				
		    	}					 									
		   }
		   
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;		
		$data['base_url']         = $this->base_url;	
        $data['CountryDropdown']  = $this->CommonModel->CountryDropdown();
		$data['material_group']   = $this->MaterialCentersModel->centres_group_dropdown();
		$data['bo_address']       = $bo_address;
		return view($this->folder_path.'material_centres/add_material_centres',$data);		
    }
	
    public function remove_centres($ids_info){
		if(!$ids_info)
			return redirect()->to($this->base_url.'material_centres/list_centres');
			
		 $default_groups = [];
		 $errors         = [];
		 $ids  = urlSafeBase64Decode($ids_info); 
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $id){		     
		    $stat = true;
		    $name = $this->MaterialCentersModel->get_mc_name($id);
		     if (in_array($id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Material Centre "'.$name.'" belongs to Default Group');
		     }
		     if ($this->MaterialCentersModel->check_mc_with_voucher($id)){ //check if account exist with that group
		         $stat = false;
		         array_push($errors, 'Failed! Material Centre "'.$name.'" has one or more associated Vouchers');
		     }		     
		     if($stat){
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'material centre',
    		            ];
    		      //   $this->LogModel->add_log($log);    		     
    		     $this->MaterialCentersModel->remove_single_centres($id);				
		     }
		 }
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		 return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
	} 
	  
	public function modify_centre($centre_id)
    {
		if(!$centre_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		 if($this->request->getMethod() == 'POST'){	
		    $mat_cent_name      = $this->request->getVar('mat_cent_name'); 
			$mat_cent_grp       = $this->request->getVar('mat_cent_grp'); 
			$mat_cent_alias     = $this->request->getVar('mat_cent_alias'); 
			$mat_cent_print     = $this->request->getVar('mat_cent_print'); 
			$mat_cent_add1      = $this->request->getVar('mat_cent_add1'); 
			$mat_cent_add2      = $this->request->getVar('mat_cent_add2'); 
			$mat_cent_city      = $this->request->getVar('mat_cent_city'); 
			$mat_cent_pin       = $this->request->getVar('mat_cent_pin'); 
			$mat_cent_state     = $this->request->getVar('mat_cent_state');
			$mat_cent_country   = $this->request->getVar('mat_cent_country'); 
			
			$rules = [				
				    'mat_cent_name' => [
					'label'  => 'Centre Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter centre name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
			    try{
				 $crs_mst_is_primary=0;	
				 $update_data   = [
						'mat_cent_name'       => $mat_cent_name,
						'mat_cent_alias'      => $mat_cent_alias,
						'mat_cent_print_name' => $mat_cent_print												
					   ];				
			    $response = $this->MaterialCentersModel->update_centres($centre_id,$update_data);
				if(!$response['status']){        				
					return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				}
                else{
				   $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>7,"crs_mst_id"=>$centre_id,"under_crs_mst_id"=>$mat_cent_grp,
											  "crs_mst_parent_id"=>0,"under_main_id"=>0,
											  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
					$this->MaterialCentersModel->update_undercrsmt($mst_update_data,$centre_id,7);
					$update_matcentdet_data   = [
						'cmp_id'            => $this->company_id,
						'mat_cent_id'       => $centre_id,
						'mat_cent_addr1'    => $mat_cent_add1,
						'mat_cent_addr2'    => $mat_cent_add2,
						'mat_cent_city'     => $mat_cent_city,
						'mat_cent_state'    => $mat_cent_state,
						'mat_cent_pin_zip'  => $mat_cent_pin,						
						'mat_cent_country'  => $mat_cent_country							
					   ];	
					 $this->MaterialCentersModel->update_centre_details($centre_id,$update_matcentdet_data);	
				     return json_encode(['status' => true, 'message' => 'Data Updated']);	
				}				

				die;	
			   }catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				  } 
				  			 
		    	}					 									
		   }			
				 
			$center_info = $this->MaterialCentersModel->center_info($centre_id);
			if(!$center_info)
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;		
		$data['base_url']         = $this->base_url;	
        $data['CountryDropdown']  = $this->CommonModel->CountryDropdown();
		$data['material_group']   = $this->MaterialCentersModel->centres_group_dropdown();
		$data['centre_info']      = $center_info;	
		$data['centre_id']        = $centre_id;
	    return view($this->folder_path.'material_centres/edit',$data);		
    }
	
	public function mc_change_status(){
   	     $status_ids    = $this->request->getVar('pss_status_val'); 
	 	 $ids_info      = $this->request->getVar('accidids');		
		 $ids           = base64_decode($ids_info);  		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 $ids2  = array_unique($ids2);
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat = true;
		     $name = $this->MaterialCentersModel->get_mc_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					 if ($this->MaterialCentersModel->check_mc_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Material Centre "'.$name.'" already exists');
					 } 
					}
					if($status==0){					  
						 if ($this->MaterialCentersModel->check_mc_txn_exists($account_id)){
							 $stat = false;
							 array_push($errors, 'Failed! Material centre "'.$name.'" has one or more associated vouchers');
						 } 					 						 
					 }
			     if($stat)
			       {	   
		           $this->MaterialCentersModel->changestatus_single_mc($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	  
    public function group_list()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;	
		$data['base_url']         = $this->base_url;
		$data['session']         = $this->session;
        $data['groups_list']     = $this->MaterialCentersModel->ajax_group_list();			
		return view($this->folder_path.'material_centres/list_group',$data);		
    } 
    public function group_change_status(){
   	     $status_ids    = $this->request->getVar('pss_status_val'); 
	 	 $ids_info      = $this->request->getVar('accidids');		
		 $ids           = base64_decode($ids_info);  		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 $ids2  = array_unique($ids2);
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat = true;
		     $name = $this->MaterialCentersModel->get_mc_group_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					 if ($this->MaterialCentersModel->check_mc_group_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Group "'.$name.'" already exists');
					 } 
					}
			     if($stat)
			       {	   
		           $this->MaterialCentersModel->changestatus_single_group($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  } 
    public function add_group()
    {
		 if($this->request->getMethod() == 'POST'){	
		    $group_name        = $this->request->getVar('group_name'); 
		    $group_name_alias  = $this->request->getVar('group_name_alias');
			$primary_group     = $this->request->getVar('primary_group');
			$mat_cent_main     = $this->request->getVar('mat_cent_main');		    				
			$rules = [				
					'group_name' => [
						'label'  => 'Group Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Group Name is required',
					   ],
				  ],
					'group_name_alias' => [
						'label'  => 'Alias Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Alias Name is required',
					  ],
				  ],
				  'primary_group' => [
						'label'  => 'Primary',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Primary is required'
					  ],
				  	],
			  ];
            if(!$this->validate($rules)){
              $errors = $this->validator->getErrors();
			  $errors  =array_values($errors_list);
	        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{		
				try{
					if($primary_group=='Y'){
						 $crs_mst_is_primary=1;
					}
					else{
						$crs_mst_is_primary=0;
					}

					$insert_data   = [
						'cmp_id'            => $this->company_id,
						'mat_cent_grp_name' => clean($group_name),
						'mat_cent_grp_alias'=> clean($group_name_alias)
					   ];	
					   
					$group_id = $this->MaterialCentersModel->add_group($insert_data);
				   if($group_id=="-1"){
					  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Group name already exists.']]);
				   }
				   else if($group_id=="-2"){
					  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Group alias already exists.']]);
				   }
					else{			
						$mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>8,"crs_mst_id"=>$group_id,"under_crs_mst_id"=>0,
											  "crs_mst_parent_id"=>0,"under_main_id"=>$mat_cent_main,
											  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
						$this->MaterialCentersModel->add_undercrsmt($mst_insert_data);
						 return json_encode(['status' => true, 'message' => 'Data Inserted']);	
					  }	
				  }	
					catch (\Throwable $e) {
						log_message('error', 'DB Query Error: ' . $e->getMessage());
						return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
					}	
		    	}					 									
		   }			
				 
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;			
		$data['base_url']         = $this->base_url;	
		$data['presdfnd_main']    = material_centres_grp_array();
		return view($this->folder_path.'material_centres/add_group',$data);		
    }
	
    public function remove_centre_grps($ids_info){
		if(!$ids_info)
			return redirect()->to($this->base_url.'material_centres/group_list');
			
		 $default_groups = [];
		 $errors = [];
		 $ids = urlSafeBase64Decode($ids_info); 
		 $ids2   = explode(",",$ids);
		 foreach($ids2 as $id){
		     
		    $stat = true;
		    $name = $this->MaterialCentersModel->get_mc_group_name($id);
		     if (in_array($id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Material Centre Group "'.$name.'" belongs to Default Group');
		     }
		     if ($this->MaterialCentersModel->check_mc_with_group($id)){ //check if account exist with that group
		         $stat = false;
		         array_push($errors, 'Failed! Material Centre Group "'.$name.'" has one or more associated Material Centres');
		     }		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'material centre group',
    		            ];
    		     //   $this->LogModel->add_log($log);    		     
    		    $this->MaterialCentersModel->remove_single_centre_grps($id);
				 
		     }
		     
		 }
		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		 return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  die;		
	  } 
	  
    public function modify_group($group_id)
    {
		if(!$group_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		 if($this->request->getMethod() == 'POST'){	
		    $group_name        = $this->request->getVar('group_name'); 
		    $group_name_alias  = $this->request->getVar('group_name_alias');
			$primary_group     = $this->request->getVar('primary_group');
			$mat_cent_main     = $this->request->getVar('mat_cent_main');		    				
			$rules = [				
					'group_name' => [
						'label'  => 'Group Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Group Name is required',
					   ],
				  ],
					'group_name_alias' => [
						'label'  => 'Alias Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Alias Name is required',
					  ],
				  ],
				  'primary_group' => [
						'label'  => 'Primary',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Primary is required'
					  ],
				  	],
			  ];
            if(!$this->validate($rules)){
              $errors = $this->validator->getErrors();
			  $errors  =array_values($errors_list);
			  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
			   if($primary_group == 'Y'){
				 $crs_mst_is_primary=1;
			   }else{		
			     $crs_mst_is_primary=0;
			    }
				$update_data   = [
						'mat_cent_grp_name'  => clean($group_name),
						'mat_cent_grp_alias' => clean($group_name_alias)								
					   ];				
			    $response = $this->MaterialCentersModel->update_group($update_data,$group_id);
				if(!$response['status']){        				
					return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				}
				else{
				$mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_id"=>$group_id,"crs_mst_type"=>8,"under_crs_mst_id"=>0,
							  "crs_mst_parent_id"=>0,"under_main_id"=>$mat_cent_main,
							 "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>$crs_mst_is_primary);
				$this->MaterialCentersModel->update_undercrsmt($mst_update_data,$group_id,8);
				return json_encode(['status' => true, 'message' => 'Data Updated']);		
				}	
				 			 
		     }					 									
		   }		

		$group_info = $this->MaterialCentersModel->group_info($group_id);
		if(!$group_info)
		 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
				 
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;		
		$data['base_url']         = $this->base_url;	
        $data['group_info']       = $group_info;	
		$data['group_id']         = $group_id;			
		$data['presdfnd_main']    = material_centres_grp_array();
	    return view($this->folder_path.'material_centres/edit_group',$data);		
    }
  
}