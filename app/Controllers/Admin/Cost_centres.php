<?php
namespace App\Controllers\Admin;
use App\Models\Admin\CostCentreModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Models\CommonModel;
use App\Libraries\auth_session;

class Cost_centres extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->CostCentreModel = new CostCentreModel();	
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
		$this->CommonModel     = new CommonModel();	
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
    }
    
    public  function ajax_cc()
	 {
		echo $response =  $this->CostCentreModel->ajax_cc();
	 } 
	 
   public function list()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'cost_centres/list',$data);		
    } 
    
    public function remove_cc($ids_info)
    {
        if(!$ids_info)
            return redirect()->to($this->base_url.'cost_centres/list');
    
        $errors = [];
		 $ids = urlSafeBase64Decode($ids_info); 
        $ids2 = explode(",",$ids);
        
        foreach($ids2 as $cc_id){
            $stat = true;
            $name = $this->CostCentreModel->get_cc_name($cc_id);            
            if ($this->CostCentreModel->check_cc_with_voucher($cc_id)){ 
                 $stat = false;
                 array_push($errors, 'Failed! Cost Centre "'.$name.'" has one or more associated Vouchers');
             }
            
            if($stat)
            {
                $log = [
                    'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
                    'log_date'          => date('Y-m-d'),
                    'log_time'          => date('H:i:s'),
                    'log_action_tags'   => 'delete',
                    'log_field_id'      => $cc_id,
                    'log_field_name'    => $name,
                    'log_field_type'    => 'Cost Centre',
                ];
                //$this->LogModel->add_log($log);                
                $this->CostCentreModel->remove_single_cc($cc_id);                			
            }
        }        
        if(count($errors))
            $this->session->setFlashdata('error_array_message', $errors);        
       return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
    }

	  
    public function add_cc()
    {
        if($this->request->getMethod() == 'POST'){	
		    $cc_name        = $this->request->getVar('cc_name'); 
			$cc_alias       = $this->request->getVar('cc_alias');
			$cc_print       = $this->request->getVar('cc_print');
			$cc_grp_id      = $this->request->getVar('cc_grp_id');
            $cc_op_bal      = $this->request->getVar('cc_op_bal');
            $cc_op_drcr     = $this->request->getVar('cc_op_drcr');
            $cc_py_bal      = $this->request->getVar('cc_py_bal');
            $cc_py_drcr     = $this->request->getVar('cc_py_drcr');
            $rules = [				
                'cc_name' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Name',
                    ]
                ]
            ];
            
            $errors = [];
	        if(!$this->validate($rules)){
	        	$errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }else{
				 $insert_data   = [
							'cmp_id'     => $this->company_id,
							'cc_name'     => clean($cc_name),
							'cc_alias'    => clean($cc_alias),
							'cc_print_name'    => clean($cc_print)
				  	];
               
                $cc_id = $this->CostCentreModel->add_cc($insert_data);	
				if($cc_id=='-1'){
					return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Name already exists.']]);			 
				}
                else{                   	
				 	$mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>9,"crs_mst_id"=>$cc_id,"under_crs_mst_id"=>$cc_grp_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				   $this->CostCentreModel->add_undercrsmt($mst_insert_data);
					$op_balance = 0;
                    if(!empty($cc_op_bal)){
                        if($cc_op_drcr == 'cr')
                            $op_balance = -floatval($cc_op_bal);
                        else
                            $op_balance = floatval($cc_op_bal);
                    }

                    $py_balance = 0;
                    if(!empty($cc_py_bal)){
                        if($cc_py_drcr == 'cr')
                            $py_balance = -floatval($cc_py_bal);
                        else
                            $py_balance = floatval($cc_py_bal);
                    }
                    $opn_bal_data = [
                        'cc_id'     => $cc_id,
						'cmp_id'    => $this->company_id,
						'cmpfymastr_id'=> $this->fy_id,                        
                        'cc_op_bal' => $op_balance,
                        'cc_py_bal' => $py_balance,
						'hobo_id'   => $this->bo_id
                     ];
					$this->CostCentreModel->insert_cc_op_bal($opn_bal_data);
					
					$cc_op_txn_dr_cr = ($cc_op_drcr === 'dr') ? 1 : (($cc_op_drcr === 'cr') ? 2 : 0);
					
					$acc_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
				    $txn_data = array("cmp_id"=>$this->company_id,"cc_id"=>$cc_id,"cc_txn_date"=>$acc_txn_date,
				                      "cc_txn_dr_cr"=>$cc_op_txn_dr_cr,"cc_txn_amt"=>abs($op_balance),"vch_txn_id"=>NULL,"txn_id"=>NULL,"hobo_id"=>$this->bo_id
							         );
				    $this->CostCentreModel->insert_cc_txn_entry($txn_data);
					
					$logd = array(
                        'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
                        'log_date'          => date('Y-m-d'),
                        'log_time'          => date('H:i:s'),
                        'log_action_tags'   => 'add',
                        'log_field_id'      => $cc_id,
                        'log_field_name'    => $_POST['cc_name'],
                        'log_field_type'    => 'cost centre',
                        );
					//$this->LogModel->add_log($log);	
                    return json_encode(['status' => true, 'message' => 'Data Inserted']);
                    
                }				 
            }					 									
        }		
		
        $data['message_output']           = $this->message_output;
        $data['base_url']                 = $this->base_url;	
        $data['folder_path']              = $this->folder_path;	
        $data['user_groups_dropdown']     = $this->CostCentreModel->cc_group_dropdown();	        
        return view($this->folder_path.'cost_centres/add_cc',$data);		
    }

   public function change_status(){
   	    $status_ids    = $this->request->getVar('pss_status_val'); 
		$ids_info      = $this->request->getVar('accidids');		
		$ids           = base64_decode($ids_info);  		 
		 // when activate master then check is it any master exisst ofthe same name or not 
		 $errors = [];
		 $ids2   = explode(",",$ids);
		 $ids2   = array_unique($ids2);
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat   = true;
		     $name   = $this->CostCentreModel->get_cc_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					   if ($this->CostCentreModel->check_cc_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Cost Centre "'.$name.'" already exists');
					    }
					  }
					 if($status==0){					  
						 if ($this->CostCentreModel->check_cc_txn_exists($account_id)){
							 $stat = false;
							 array_push($errors, 'Failed! cc_is_active "'.$name.'" has one or more associated vouchers');
						 } 					 
						 if ($this->CostCentreModel->check_cc_opn_exists($account_id)){
							 $stat = false;
							 array_push($errors, 'Failed! cc_is_active "'.$name.'" has default opening');
						 } 
					 }					 
			     if($stat)
			       {	   
		           $this->CostCentreModel->changestatus_single_accounts($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	}	
  
   public function modify_cc($cc_id)
    { 
		 if($this->request->getMethod() == 'POST'){
            $rules = [				
                'cc_name' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Name',
                    ]
                ]                
            ];			
            $errors = [];
	        if(!$this->validate($rules)){
	        	$errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }else{

                $cc_name       = $this->request->getVar('cc_name'); 
                $cc_alias      = $this->request->getVar('cc_alias');
                $cc_print      = $this->request->getVar('cc_print');
                $cc_grp_id     = $this->request->getVar('cc_grp_id');
                $cc_op_bal     = $this->request->getVar('cc_op_bal');
                $cc_op_drcr    = $this->request->getVar('cc_op_drcr');
                $cc_py_bal     = $this->request->getVar('cc_py_bal');
                $cc_py_drcr    = $this->request->getVar('cc_py_drcr');				
				$update_data   = [
                    'cc_name'       => ucwords(clean($cc_name)),
                    'cc_alias'      => clean($cc_alias),
                    'cc_print_name' => clean($cc_print)                    
                ];
				$response = $this->CostCentreModel->update_cc($update_data,$cc_id);				    
			    if(!$response['status']){
				    $this->message_output->set_error($response['message']);
			     }
			     else{
					 
					 $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>9,"crs_mst_id"=>$cc_id,"under_crs_mst_id"=>$cc_grp_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				   $this->CostCentreModel->update_undercrsmt($mst_update_data,$cc_id,9);
					$op_balance = 0;
                    if(!empty($cc_op_bal)){
                        if($cc_op_drcr == 'cr')
                            $op_balance = -floatval($cc_op_bal);
                        else
                            $op_balance = floatval($cc_op_bal);
                    }

                    $py_balance = 0;
                    if(!empty($cc_py_bal)){
                        if($cc_py_drcr == 'cr')
                            $py_balance = -floatval($cc_py_bal);
                        else
                            $py_balance = floatval($cc_py_bal);
                    }
                    $opn_bal_data = [
                        'cc_id'     => $cc_id,
						'cmp_id'    => $this->company_id,
						'cmpfymastr_id'=> $this->fy_id,                        
                        'cc_op_bal' => $op_balance,
                        'cc_py_bal' => $py_balance,
						'hobo_id'   => $this->bo_id
                     ];
					$this->CostCentreModel->update_cc_op_bal($opn_bal_data);
					
					$cc_op_txn_dr_cr = ($cc_op_drcr === 'dr') ? 1 : (($cc_op_drcr === 'cr') ? 2 : 0);
					
				    $txn_data = array( "cc_txn_dr_cr"=>$cc_op_txn_dr_cr,"cc_txn_amt"=>abs($op_balance));
				    $this->CostCentreModel->update_cc_txn_opbal_entry($txn_data,$cc_id);
					$log = [
				            'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
				            'log_date'          => date('Y-m-d'),
				            'log_time'          => date('H:i:s'),
				            'log_action_tags'   => 'update',
				            'log_field_id'      => $cc_id,
				            'log_field_name'    => $_POST['cc_name'],
				            'log_field_type'    => 'cost centre',
				          ];
				    //$this->LogModel->add_log($log);				    
			       return json_encode(['status' => true, 'message' => 'Data Updated']);  
			     }
		    }					 									
		}		


         $get_cc_info = $this->CostCentreModel->get_cc_info($cc_id);
		 
         if(!$get_cc_info)
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		   
		$data['message_output']           = $this->message_output;
		$data['folder_path']              = $this->folder_path;	
		$data['cc_info']                  = $get_cc_info;	
        $data['user_groups_dropdown']     = $this->CostCentreModel->cc_group_dropdown();	
		$data['base_url']                 = $this->base_url;	
		$data['cc_id']                    = $cc_id;		
	    return view($this->folder_path.'cost_centres/edit_cc',$data);		
    }		
	
    public function list_group()
    {
        $data['message_output']  = $this->message_output;
        $data['folder_path']     = $this->folder_path;	
        $data['base_url']        = $this->base_url;
        $data['session']         = $this->session;      
        $data['groups_list']     = $this->CostCentreModel->ajax_group_list();	        		
        return view($this->folder_path.'cost_centres/list_group',$data);		
    } 
   
    public function add_group()
    {
        if($this->request->getMethod() == 'POST'){
                $group_name         = $this->request->getVar('cc_grp_name'); 
				$group_name_alias   = $this->request->getVar('cc_grp_alias');
				$under_cc_grp_id    = $this->request->getVar('under_cc_grp_id') ?? 0;
                $rules = [				
                'cc_grp_name' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please Enter Group Name',
                      ]
                    ]
                 ];
                $errors = [];
				if(!$this->validate($rules)){
					$errors_list = $this->validator->getErrors();
					$errors      = array_values($errors_list);						
					return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$errors]]);
			    }else{	
			     try{
					$insert_data   = [
								'cmp_id'        => $this->company_id,
								'cc_grp_name'   => clean($group_name),
								'cc_grp_alias'  => clean($group_name_alias)
							   ];
					$group_id = $this->CostCentreModel->add_group($insert_data);
					if($group_id=='0'){									
								return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Group name already exists.']]);			 
					   }						
					else{
					    $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>10,"crs_mst_id"=>(int)$group_id,"under_crs_mst_id"=>(int)$under_cc_grp_id,
										          "crs_mst_parent_id"=>(int)0,"under_main_id"=>(int)0,
										          "cmpfymastr_id"=>(int)$this->fy_id,"crs_mst_is_primary"=>(int)0);
			           	$this->CostCentreModel->add_undercrsmt($mst_insert_data);	
				       $log = [
                        'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
                        'log_date'          => date('Y-m-d'),
                        'log_time'          => date('H:i:s'),
                        'log_action_tags'   => 'add',
                        'log_field_id'      => (int)$group_id,
                        'log_field_name'    => $_POST['cc_grp_name'],
                        'log_field_type'    => 'cc group',
                       ];
                    //$this->LogModel->add_log($log);  
					  return json_encode(['status' => true, 'message' => 'Data Inserted']);
					   }
					}
					catch (\Throwable $e) {
						log_message('error', 'DB Query Error: ' . $e->getMessage());
						return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
					} 
				}				 
            }					 									
        $data['folder_path']              = $this->folder_path;	
        $data['base_url']                 = $this->base_url;
        $data['user_groups_dropdown']     = $this->CostCentreModel->cc_group_dropdown();
        return view($this->folder_path.'cost_centres/add_group',$data);		
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
		     $name = $this->CostCentreModel->get_cc_group_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					 if ($this->CostCentreModel->check_cc_group_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Group "'.$name.'" already exists');
					 } 
					}
			     if($stat)
			       {	   
		           $this->CostCentreModel->changestatus_single_group($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	  
	public function modify_group($group_id)
    {
        if($this->request->getMethod() == 'POST'){	
            $rules = [				
                'cc_grp_name' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please Enter Group Name',
                    ]
                ],
                'cc_grp_alias' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please Enter Group Alias',
                    ]
                ],
            ];
			
            $errors = [];
				if(!$this->validate($rules)){
						$errors_list = $this->validator->getErrors();
						$errors  =array_values($errors_list);						
						return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
			}else{				
			    $group_name        = $this->request->getVar('cc_grp_name'); 
				$group_name_alias  = $this->request->getVar('cc_grp_alias');
				$under_cc_grp_id   = $this->request->getVar('under_cc_grp_id') ?? 0;
              try{
				  $update_data = [
							'cc_grp_name'     => html_entity_decode(clean($group_name)),
							'cc_grp_alias'    => clean($group_name_alias)
						];
                $this->CostCentreModel->update_group($update_data,$group_id);
                $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>10,"crs_mst_id"=>$group_id,"under_crs_mst_id"=>$under_cc_grp_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
										  
			    $this->CostCentreModel->update_undercrsmt($mst_insert_data,$group_id,10);
                $log = [
                'uuid_aicountly' => $this->session->get('uuid_aicountly'),
                'log_date' => date('Y-m-d'),
                'log_time' => date('H:i:s'),
                'log_action_tags' => 'update',
                'log_field_id' => $group_id,
                'log_field_name' => $_POST['cc_grp_name'],
                'log_field_type' => 'cc group',
                ];
                //$this->LogModel->add_log($log);
				return json_encode(['status' => true, 'message' => 'Data Updated']);
			   }
                catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				} 
            }					 									
        }	 
        
        $data['group_info']             = $this->CostCentreModel->cc_group_info($group_id,$this->company_id);
        $data['folder_path']            = $this->folder_path;
        $data['group_id']               = $group_id;		
        $data['base_url']               = $this->base_url;	
        $data['user_groups_dropdown']   = $this->CostCentreModel->cc_group_dropdown($group_id);
        return view($this->folder_path.'cost_centres/edit_group',$data);		
    }
   
    public function remove_groups($ids_info){
        
        $default_groups = [];
        $errors = [];
		$ids = urlSafeBase64Decode($ids_info); 
        $ids2 = explode(",",$ids);
        foreach($ids2 as $cc_grp_id){
        
            $stat = true;
            $name = $this->CostCentreModel->get_cc_group_name($cc_grp_id);;
            if (in_array($cc_grp_id, $default_groups)){ //check if group belongs to defaults
                $stat = false;
                array_push($errors, 'Failed! Cost Centre Group "'.$name.'" belongs to Default Group');
            }
            if ($this->CostCentreModel->check_group_with_group($cc_grp_id)){ //check if child group exist with that group
                $stat = false;
                array_push($errors, 'Failed! Cost Centre Group "'.$name.'" has one or more child groups');
            }
            if ($this->CostCentreModel->check_cc_with_group($cc_grp_id)){ //check if account exist with that group
                $stat = false;
                array_push($errors, 'Failed! Cost Centre Group "'.$name.'" has one or more associated cost centres');
            }
            
            if($stat)
            {
                $log = [
                    'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
                    'log_date'          => date('Y-m-d'),
                    'log_time'          => date('H:i:s'),
                    'log_action_tags'   => 'delete',
                    'log_field_id'      => $cc_grp_id,
                    'log_field_name'    => $name,
                    'log_field_type'    => 'cc group',
                ];
                //$this->LogModel->add_log($log);                
                $this->CostCentreModel->remove_single_groups($cc_grp_id);
				
            }
        
        }
        
        if(count($errors))
        {
            $this->session->setFlashdata('error_array_message', $errors);
        }

       return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		
    }   
}

