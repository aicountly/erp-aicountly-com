<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BranchesModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
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
			$this->LogModel          = new ERPLogModel();
			$this->TransactionModel  = new TransactionModel();	
			$this->CommonModel       =  new CommonModel();		
			$this->auth_session      = new auth_session();
			$this->auth_session->user_restrict();
			$this->auth_session->is_company_opened();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().'/'.getenv('AdminPath');
			$this->folder_path   = getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->forge         = \Config\Database::forge();
			$this->company_id    =  $this->session->get('ses_company_id');
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->enc_string    = new enc_string();
		
    }
   
     public  function ajax_branches_view()
	 {
		echo $response =  $this->BranchesModel->ajax_branch_list();	
		
	 } 
   function update_opn_balances(){
	   
	    if($this->request->getMethod() == 'post'){	
		
		 $accbaldata       = $this->request->getVar('accbaldata'); 
		 if($accbaldata){
			 $accbaldata =  json_decode($accbaldata,true);
			  if($accbaldata){
				  foreach($accbaldata as $row){
					  $acc_id   = $row['acc_id'];
					  $op_bal   = abs($row['op_bal']);
					  $bal_type   = $row['bal_type'];					  
					  $isedited = $row['isedited'];
					  if($isedited=="1"){	
					  	if($bal_type == 'CR.')				  
								$updata  = array("acc_op_bal"=> -$op_bal);
							else
								$updata  = array("acc_op_bal"=> $op_bal);

							$this->BranchesModel->update_acc_op_bal($acc_id,$updata);
							$this->TransactionModel->update_account_balance($acc_id);
					  }
				  }
				  
				 return redirect()->to($this->base_url.'accounts/list');
				 die;  
			  }
		   }
		}else{
			return redirect()->to($this->base_url.'accounts/list');
			die;
		   }
   }	

   public function index()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'branches/view',$data);		
    } 

   public function ajax_list_groups()
	{
	  
		$pq_curPage = (int)$_POST["pq_curpage"];
		$limit     = (int)$_POST["pq_rpp"];
	 	$response = $this->BranchesModel->load_list_groups($limit,$pq_curPage);

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}

 public function list_group()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;		
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;	
		return view($this->folder_path.'branches/list_group',$data);		
    } 
	
   public function add()
   {
		 if($this->request->getMethod() == 'post'){			     
		  
			$account_name       = $this->request->getVar('account_name'); 
			$account_alias      = $this->request->getVar('account_alias');		
			$account_zone       = $this->request->getVar('account_zone');
			$account_group      = $this->request->getVar('account_group');
			$adrs1              = $this->request->getVar('acc_adrs1');
			$adrs2              = $this->request->getVar('acc_adrs2');
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('state_id');
			$city               = $this->request->getVar('acc_city');
			$pincode            = $this->request->getVar('acc_pincode');
			$opening_date       = $this->request->getVar('opening_date');	
			$closing_date       = $this->request->getVar('closing_date');	
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
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
			    $insert_data   = [
						'comp_id'        => $this->company_id,
						'bo_name'        => ucwords(clean($account_name)), //remove special characters
						'bo_alias'       => $account_alias,
						'acc_grp_id'     => $account_group,
						'bo_opdate'      => date('Y-m-d',strtotime($opening_date)),
						'bo_cldate'      => date('Y-m-d',strtotime($closing_date)),
						'bo_add1'        => $adrs1,
						'bo_add2'        => $adrs2,
						'bo_country'     => $country_id,
						'bo_state'       => $state_id,
						'bo_city'        => $city,						
						'bo_pin'         => $pincode,
						'bo_zone'        => $account_zone						
					    ];
				
			 $response = $this->BranchesModel->add_account($insert_data);		
			if(!$response['status']){
				  $this->message_output->set_error($response['message']);
			}
			else{
			    $account_id = $response['bo_id'];

			    $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'add',
			            'log_field_id' => $account_id,
			            'log_field_name' => $account_name,
			            'log_field_type' => 'branch',
			        ];
			    $this->LogModel->add_log($log);
				
				
			    return redirect()->to($this->base_url.'branches');
				 die;	
			    }				 
	    	}					 									
	   }	 
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;	
        $data['group_main_dropdown']   = $this->BranchesModel->branch_group_main_dropdown();
        $data['CountryDropdown']        = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']         = $this->CommonModel->StatesDropdown();
		$data['base_url']               = $this->base_url;
		$data['zones']                  = array(''=>'Choose','1'=>'Central','2'=>'East','3'=>'North','4'=>'South','5'=>'West');
	    return view($this->folder_path.'branches/add',$data);		
    }
	
	public function modify($account_id)
    {
		if(!$account_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$account_info = $this->BranchesModel->account_info($account_id);
		if(!$account_info){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}   
		if($this->request->getMethod() == 'post'  && $this->request->isAjax()){	
		
			$account_name       = $this->request->getVar('account_name'); 
			$account_alias      = $this->request->getVar('account_alias');		
			$account_zone       = $this->request->getVar('account_zone');
			$account_group      = $this->request->getVar('account_group');
			$adrs1              = $this->request->getVar('acc_adrs1');
			$adrs2              = $this->request->getVar('acc_adrs2');
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('state_id');
			$city               = $this->request->getVar('acc_city');
			$pincode            = $this->request->getVar('acc_pincode');
			$opening_date       = $this->request->getVar('opening_date');	
			$closing_date       = $this->request->getVar('closing_date');			
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
              // $this->message_output->set_error($this->validator->listErrors());
            	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
			    
				

				$update_data   = [
						'bo_name'        => ucwords(clean($account_name)), //remove special characters
						'bo_alias'       => $account_alias,
						'acc_grp_id'     => $account_group,
						'bo_opdate'      => date('Y-m-d',strtotime($opening_date)),
						'bo_cldate'      => date('Y-m-d',strtotime($closing_date)),
						'bo_add1'        => $adrs1,
						'bo_add2'        => $adrs2,
						'bo_country'     => $country_id,
						'bo_state'       => $state_id,
						'bo_city'        => $city,						
						'bo_pin'         => $pincode,
						'bo_zone'        => $account_zone
					    ];
			         $response = $this->BranchesModel->update_account($update_data,$account_id,$account_info['acc_id']);
			        if(!$response['status']){        				
        				  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
        			}
        			else{
        			    $account_id = $response['bo_id'];
						
    			         $log = [
    				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    				            'log_date' => date('Y-m-d'),
    				            'log_time' => date('H:i:s'),
    				            'log_action_tags' => 'update',
    				            'log_field_id' => $account_id,
    				            'log_field_name' => $account_name,
    				            'log_field_type' => 'branch',
    				        ];
    				    $this->LogModel->add_log($log);   				    
    				    
    				   
						return json_encode(['status' => true, 'message' => 'Data Updated']);    				
        			}
				 			 
		    	}					 									
		}	 
		   
		

		
       
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['group_main_dropdown']   = $this->BranchesModel->branch_group_main_dropdown();
     	$data['account_info']          = $account_info;       
        $data['CountryDropdown']       = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']        = $this->CommonModel->StatesDropdown();		
		$data['base_url']              = $this->base_url;		
		$data['account_id']            = $account_id;	
		$data['zones']                  = array(''=>'Choose','1'=>'Central','2'=>'East','3'=>'North','4'=>'South','5'=>'West');
	    return view($this->folder_path.'branches/edit',$data);		
    }
	
	public function mark_branch_ho($ids){
		
			if(!$ids)
			return redirect()->to($this->base_url.'branches'); 
		  $ids2   =  explode(",",$ids);
		 $ids2   =  array_unique($ids2);
		 
		  foreach($ids2 as $bo_id){
			        
		            $this->BranchesModel->mark_branch_ho($bo_id);
		  }
		   return json_encode(['status' => true, 'message' => 'Marked as HO', 'reload' => 1]);
	}
	
	public function remove_branches($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'branches'); 
		 
		 $default_groups = [];//[1,2,3,4,5];
		 $errors = [];
		 $ids2   =  explode(",",$ids);
		 $ids2   =  array_unique($ids2);
		
		 foreach($ids2 as $bo_id){
		     $stat = true;
			 $bo_info = $this->BranchesModel->get_branch_info($bo_id);
			 if($bo_id==1){
				$stat = false; 
				array_push($errors, 'Failed! HO "'.$bo_info['bo_name'].'" can not be deleted');
			 }
			    
		     
		     

		     if($bo_info != ''){
		     	if (in_array($bo_id, $default_groups)){ 
		         $stat = false;
		         array_push($errors, 'Failed! Branch "'.$bo_info['bo_name'].'" belongs to Default Accounts');
			     }
			     if ($this->BranchesModel->check_account_with_voucher($bo_info['acc_id'])){
			         $stat = false;
			         array_push($errors, 'Failed! Branch "'.$bo_info['bo_name'].'" has one or more associated Vouchers');
			     }
			     
			     if($stat)
			     {
			         $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'delete',
			            'log_field_id' => $bo_id,
			            'log_field_name' => $bo_info['bo_name'],
			            'log_field_type' => 'account group',
			        ];
		            $this->LogModel->add_log($log);
		            
		            $this->BranchesModel->remove_single_branches($bo_id);
					$this->BranchesModel->remove_single_accounts($bo_info['acc_id']);
			     }
		     }  
		 }
		 
		 
		 $file  =     WRITEPATH.'comp'.$this->company_id.'/acc.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $accounts_list  = $this->BranchesModel->company_all_accounts();
		            $comp_folder    = 'comp'.$this->company_id;
		            $f = fopen($file, 'a');
                    fwrite($f,$accounts_list);
			     }
			     
			     
		 if(count($errors)){
		         // $this->session->setFlashdata('error_array_message', $errors);
		         return json_encode(['status' => false, 'message' => 'Not Deleted', 'errors' => $errors]);
		  }
		 
		 
		 // return redirect()->to($this->base_url.'accounts/list');
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	
	public function add_group()
    {
		  if($this->request->getMethod() == 'post'){	
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
				$errors = [];
        if(!$this->validate($rules)){
        		$errors = $this->validator->getErrors();
        }

				$group_name       = $this->request->getVar('group_name'); 
				$group_name_alias = $this->request->getVar('group_name_alias');
				$primary_group    = $this->request->getVar('primary_group');

				if($primary_group=='Y'){
						$rules = [				
							'yes_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}
				else{
						$rules = [				
							'no_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}

				if(!$this->validate($rules)){
						$errors = $this->validator->getErrors();
        }


				if($primary_group=='Y'){
						$acc_grp_parent_id    = $this->request->getVar('yes_group_under');
						$under_acc_grp_id     = 0;
						$under_main_grp_id    = 0;

						if($this->BranchesModel->check_parent_restriction($acc_grp_parent_id, '2PRMACC')){
								$primary_groups = $this->BranchesModel->get_parimary_groups($acc_grp_parent_id);
								if(!empty($primary_groups) && count($primary_groups) > 0)
									$errors['yes_group_under'] = 'Parent Group can not have more than one Primary Groups';
						}
				}
				else{
						$no_group_under    = $this->request->getVar('no_group_under');
						$main_group_info     = $this->BranchesModel->main_group_info($no_group_under);
						if($main_group_info){
								$acc_grp_parent_id   = $main_group_info['acc_grp_parent_id'];
								$under_acc_grp_id  = $main_group_info['acc_grp_id'];

								if($main_group_info['acc_grp_primary']=='Y')
										$under_main_grp_id= $main_group_info['acc_grp_id'];
								else
										$under_main_grp_id= $main_group_info['under_main_grp_id'];
						}
						else{
							$errors['no_group_under'] = 'Something went wrong with Under Group';
						}
						
				}
        if(empty($errors))
        {
        		$insert_data   = [
			                'comp_id'            => $this->company_id,
							'acc_grp_name'       => ucwords(clean($group_name)),
							'acc_grp_alias'      => clean($group_name_alias),
							'acc_grp_primary'    => $primary_group,
							'acc_grp_parent_id'  => $acc_grp_parent_id,
							'under_acc_grp_id'   => $under_acc_grp_id,
							'under_main_grp_id'  => $under_main_grp_id,
				  	];
				  	$exists = $this->BranchesModel->add_group($insert_data);		
						if($exists==0){
								$this->message_output->set_error('Group name already exists.');				 
						}
						else{
								$log = [
										'uuid_aicountly' => $this->session->get('uuid_aicountly'),
										'log_date' => date('Y-m-d'),
										'log_time' => date('H:i:s'),
										'log_action_tags' => 'add',
										'log_field_id' => $exists,
										'log_field_name' => $group_name,
										'log_field_type' => 'account group',
								];
								$this->LogModel->add_log($log);

								return redirect()->to($this->base_url.'branches/list_group');
			    	}
        }
        else{
        	$error = '<ul>';
        	foreach ($errors as $key => $value) {
        		$error .= '<li>'.$value.'</li>';
        	}
        	$error .= '</ul>';
        		$this->message_output->set_error($error);
        }
							 									
			}			
				 
			$data['message_output']   = $this->message_output;
			$data['session']          = $this->session;
			$data['folder_path']      = $this->folder_path;	
			$data['group_primary_dropdown']   = $this->BranchesModel->group_primary_dropdown();
			$data['base_url']         = $this->base_url;
			$data['group_main']       = $this->BranchesModel->group_main_dropdown();
			$data['user_groups_dropdown']       = $this->BranchesModel->group_main_dropdown();
			return view($this->folder_path.'branches/add_group',$data);		
    }
	
	public function remove_groups($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'branches/list_group'); 
		 
		 $default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $account_id){
		     
		     $stat = true;
		     if (in_array($account_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Account Group ID = '.$account_id.' belongs to Default Group');
		      }
		      if($this->BranchesModel->check_group_restriction($account_id, 'DELREST')){
							$stat = false;
		         	$account_name = $this->BranchesModel->get_account_group_name($account_id);
		         	array_push($errors, 'Failed! Group "'.$account_name.'" has been restricted to delete');
					}
					if($this->BranchesModel->sub_group_info($account_id)){
							$stat = false;
		         	$account_name = $this->BranchesModel->get_account_group_name($account_id);
		         	array_push($errors, 'Failed! Group "'.$account_name.'" has one or more associated sub groups');
					}
		     if ($this->BranchesModel->check_account_with_group($account_id)){ //check if account exist with that group
		         $stat = false;
		         $account_name = $this->BranchesModel->get_account_group_name($account_id);
		         array_push($errors, 'Failed! Account Group "'.$account_name.'" has one or more associated accounts');
		     }
		     if ($this->BranchesModel->check_bill_sundry_with_group($account_id)){ //check if account exist with that group
		         $stat = false;
		         $account_name = $this->BranchesModel->get_account_group_name($account_id);
		         array_push($errors, 'Failed! Account Group "'.$account_name.'" has one or more associated bill sundry accounts');
		     }
		     
		     if($stat)
		     {
		         $log = [
		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
		            'log_date' => date('Y-m-d'),
		            'log_time' => date('H:i:s'),
		            'log_action_tags' => 'delete',
		            'log_field_id' => $account_id,
		            'log_field_name' => $this->BranchesModel->get_account_group_name($account_id),
		            'log_field_type' => 'account group',
		        ];
	            $this->LogModel->add_log($log);
	            
	            $this->BranchesModel->remove_single_groups($account_id);
		     }
		 }
		 if(count($errors)){
		         $this->session->setFlashdata('error_array_message', $errors);
		  }
		 // return redirect()->to($this->base_url.'accounts/list_group');
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
	  }
	  
	public function modify_group($group_id)
    {
		if(!$group_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();	
		  
		if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
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
				$errors = [];
	        if(!$this->validate($rules)){
	        		// $errors = $this->validator->getErrors();
        		$errors = $this->validator->getErrors();
        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $group_name       = $this->request->getVar('group_name'); 
				$group_name_alias = $this->request->getVar('group_name_alias');
				$primary_group    = $this->request->getVar('primary_group');

				if($primary_group=='Y'){
						$rules = [				
							'yes_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}
				else{
						$rules = [				
							'no_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}

				if(!$this->validate($rules)){
						$errors = $this->validator->getErrors();
	        }

        	$old_group_data = $this->BranchesModel->main_group_info($group_id);


				if($primary_group=='Y'){
						$acc_grp_parent_id    = $this->request->getVar('yes_group_under');
						$under_acc_grp_id     = 0;
						$under_main_grp_id    = 0;

						if($old_group_data['acc_grp_primary'] == $primary_group && $old_group_data['acc_grp_parent_id'] == $acc_grp_parent_id)
						{
							// no problem
						}
						else
						{
							// check sub groups
							$sub_group_data = $this->BranchesModel->sub_group_info($group_id);
							if($sub_group_data){
									$errors['yes_group_under'] = 'One or more Sub Group exists under this Group';
							}
						}

						if($this->BranchesModel->check_parent_restriction($acc_grp_parent_id, '2PRMACC')){
								$primary_groups = $this->BranchesModel->get_parimary_groups($acc_grp_parent_id, $group_id);
								if(!empty($primary_groups) && count($primary_groups) > 0)
									$errors['yes_group_under'] = 'Parent Group can not have more than one Primary Groups';
						}
				}
				else{
						$no_group_under    = $this->request->getVar('no_group_under');
						$main_group_info     = $this->BranchesModel->main_group_info($no_group_under);
						if($main_group_info){
								$acc_grp_parent_id   = $main_group_info['acc_grp_parent_id'];
								$under_acc_grp_id  = $main_group_info['acc_grp_id'];

								if($main_group_info['acc_grp_primary']=='Y')
										$under_main_grp_id= $main_group_info['acc_grp_id'];
								else
										$under_main_grp_id= $main_group_info['under_main_grp_id'];

								if($old_group_data['acc_grp_primary'] == $primary_group && $old_group_data['under_acc_grp_id'] == $under_acc_grp_id)
								{
									// no problem
								}
								else
								{
									// check sub groups
									$sub_group_data = $this->BranchesModel->sub_group_info($group_id);
									if($sub_group_data){
											$errors['no_group_under'] = 'One or more Sub Group exists under this Group';
									}
								}
						}
						else{
							$errors['no_group_under'] = 'Something went wrong with Under Group';
						}
						
				}

				if($group_id == $under_acc_grp_id || $group_id == $under_main_grp_id){
						$errors['group_name'] = 'Something went wrong';
				}
				
	        if(empty($errors))
	        {

						$update_data = [
							'acc_grp_name'      => ucwords(clean($group_name)),
							'acc_grp_alias'    => clean($group_name_alias),
							'acc_grp_primary'  => $primary_group,
							'under_acc_grp_id' => $under_acc_grp_id,
							'acc_grp_parent_id'  => $acc_grp_parent_id,
							'under_main_grp_id'  => $under_main_grp_id,
						];			
           
            	$this->BranchesModel->update_group($update_data,$group_id);	
		        
		        $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'update',
			            'log_field_id' => $group_id,
			            'log_field_name' => $group_name,
			            'log_field_type' => 'account group',
			      ];
			    	$this->LogModel->add_log($log);

					// return redirect()->to($this->base_url.'accounts/list_group');
			    	return json_encode(['status' => true, 'message' => 'Data Updated']);
			    	
	        }
	        else{
	        	// $error = '<ul>';
	        	// foreach ($errors as $key => $value) {
	        	// 	$error .= '<li>'.$value.'</li>';
	        	// }
	        	// $error .= '</ul>';
	        	// $this->message_output->set_error($error);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }
		   
		}	 
		   

			$main_group_info = $this->BranchesModel->main_group_info($group_id);
			if(!$main_group_info){
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}

			$data['get_info']         = $main_group_info;
			$data['message_output']   = $this->message_output;
			$data['session']          = $this->session;
			$data['folder_path']      = $this->folder_path;
			$data['group_id']         = $group_id;		
			$data['group_main']       = $this->BranchesModel->group_main_dropdown($group_id);
			$data['group_primary_dropdown']   = $this->BranchesModel->group_primary_dropdown();
			$data['base_url']         = $this->base_url;		
	    return view($this->folder_path.'branches/edit_group',$data);		
    }

}