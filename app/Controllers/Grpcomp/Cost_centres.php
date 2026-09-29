<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\CostCentreModel;
use App\Models\Grpcomp\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;


class Cost_centres extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->CostCentreModel      = new CostCentreModel();	
		$this->LogModel        = new ERPLogModel();
		$this->CommonModel       =  new CommonModel();
		$this->auth_session    = new auth_session();
	    $this->auth_session->group_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('GroupPath');
		$this->admin_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('GroupPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    = new enc_string();
    }
    
    public  function ajax_cc()
	 {
		echo $response =  $this->CostCentreModel->ajax_cc();	
		
	 } 
  
   public function list()
    {
		if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $comp_id       = $comp_id_info[1];			 
			 $this->CommonModel->choose_grp_company($comp_id);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->set('ses_company_id','');
			$grpcmpid='';
		}
		
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']       = $this->admin_url;
		$data['session']         = $this->session;
		$data['grpcmpid']        = $grpcmpid;
		$group_comp_list         = $this->CommonModel->all_group_companies();
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
        $data['comp_list'] = $comp_list;
		return view($this->folder_path.'cost_centres/list',$data);		
    } 
    
    
   
    public function remove_cc($ids)
    {
        if(!$ids)
            return redirect()->to($this->base_url.'cost_centres/list');
    
    
        $errors = [];
        $ids2 = explode(",",$ids);
        
        foreach($ids2 as $cc_id){
            $stat = true;
            $name = $this->CostCentreModel->get_cc_name($cc_id);;
            
            // if ($this->CostCentreModel->check_cc_with_voucher($cc_id)){ 
            //     $stat = false;
            //     array_push($errors, 'Failed! Cost Centre "'.$name.'" has one or more associated Vouchers');
            // }
            
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
                $this->LogModel->add_log($log);
                
                $this->CostCentreModel->remove_single_cc($cc_id);
            }
        }
        
        if(count($errors))
            $this->session->setFlashdata('error_array_message', $errors);
        
       return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
    }

	  
    public function add_cc()
    {
        if($this->request->getMethod() == 'post'){	

            $rules = [				
                'cc_name' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Name',
                    ]
                ],
                'cc_alias' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Alias Name',
                    ]
                ],
                'cc_print' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Print Name',
                    ]
                ],
                'cc_grp_id' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Group Name',
                    ]
                ],
            ];
            
            if(!$this->validate($rules)){
                $this->session->setFlashdata('error_message', $this->validator->listErrors());
            }else{
   
                $_POST['comp_id'] = $this->company_id;					 
                $response = $this->CostCentreModel->add_cc($_POST);	
                
                if(!$response['status']){
                    $this->message_output->set_error($response['message']);
                    $this->session->setFlashdata('error_message', $response['message']);
                }
                else{
                    $cc_id = $response['cc_id'];
                    
                    $log = [
                        'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
                        'log_date'          => date('Y-m-d'),
                        'log_time'          => date('H:i:s'),
                        'log_action_tags'   => 'add',
                        'log_field_id'      => $cc_id,
                        'log_field_name'    => $_POST['cc_name'],
                        'log_field_type'    => 'cost centre',
                    ];
                    $this->LogModel->add_log($log);
                    
                    
                    return redirect()->to($this->base_url.'cost_centres/list');
                    
                }				 
            }					 									
        }							 
        $data['message_output']           = $this->message_output;
        $data['base_url']                 = $this->base_url;	
        $data['folder_path']              = $this->folder_path;	
        $data['user_groups_dropdown']     = $this->CostCentreModel->cc_group_dropdown();	
        
        return view($this->folder_path.'cost_centres/add_cc',$data);		
    }	
  
    public function modify_cc($cc_id)
    { 
		 if($this->request->getMethod() == 'post'){	
		    
		     
            $rules = [				
                'cc_name' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Name',
                    ]
                ],
                'cc_alias' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Alias Name',
                    ]
                ],
                'cc_print' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Print Name',
                    ]
                ],
                'cc_grp_id' => [
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Please enter Group Name',
                    ]
                ],
            ];
			
            if(!$this->validate($rules)){
              $this->session->setFlashdata('error_message', $this->validator->listErrors());
            }else{				
				    					 
				    $response = $this->CostCentreModel->update_cc($_POST,$cc_id);
				    
				    if(!$response['status']){
    				    $this->message_output->set_error($response['message']);
    			     }
    			     else{
    				  
    				    $log = [
    				            'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
    				            'log_date'          => date('Y-m-d'),
    				            'log_time'          => date('H:i:s'),
    				            'log_action_tags'   => 'update',
    				            'log_field_id'      => $cc_id,
    				            'log_field_name'    => $_POST['cc_name'],
    				            'log_field_type'    => 'cost centre',
    				          ];
    				    $this->LogModel->add_log($log);
    				    
    			        return redirect()->to($this->base_url.'cost_centres/list');
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

        $data['enc_string']               = $this->enc_string;
		$data['base_url']                 = $this->base_url;	
		$data['cc_id']                    = $cc_id;		
		
	    return view($this->folder_path.'cost_centres/edit_cc',$data);		
    }		
	public function ajax_list_groups()
	{
	    if(isset($_POST["pq_curpage"]))
		$pq_curPage = (int)$_POST["pq_curpage"];
	   else
		  $pq_curPage = 1;
	  
	  if(isset($_POST["pq_rpp"]))
		$limit     = (int)$_POST["pq_rpp"];
	  else
		 $limit     = 100; 
	 	$response = $this->CostCentreModel->ajax_group_list($limit,$pq_curPage);

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}
    public function list_group()
    {
       if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $comp_id       = $comp_id_info[1];			 
			 $this->CommonModel->choose_grp_company($comp_id);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->set('ses_company_id','');
			$grpcmpid='';
		}
		
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']       = $this->admin_url;
		$data['session']         = $this->session;
		$data['grpcmpid']        = $grpcmpid;
		$group_comp_list         = $this->CommonModel->all_group_companies();
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
        $data['comp_list'] = $comp_list;     	        		
        return view($this->folder_path.'cost_centres/list_group',$data);		
    } 
   
    public function add_group()
    {
        if($this->request->getMethod() == 'post'){
        
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
            if(!$this->validate($rules)){
                $this->session->setFlashdata('error_message', $this->validator->listErrors());
            }else{				
                $_POST['comp_id']   =   $this->company_id;
                $exists = $this->CostCentreModel->add_group($_POST);
                
                if($exists=="0"){
                    $this->session->setFlashdata('error_message', 'Group name already exists.');
                }
                else{	
                    $log = [
                        'uuid_aicountly'    => $this->session->get('uuid_aicountly'),
                        'log_date'          => date('Y-m-d'),
                        'log_time'          => date('H:i:s'),
                        'log_action_tags'   => 'add',
                        'log_field_id'      => $exists,
                        'log_field_name'    => $_POST['cc_grp_name'],
                        'log_field_type'    => 'cc group',
                    ];
                    $this->LogModel->add_log($log);
                    
                    return redirect()->to($this->base_url.'cost_centres/list_group');
                }				 
            }					 									
        }
        
        $data['folder_path']              = $this->folder_path;	
        $data['base_url']                 = $this->base_url;
        
        $data['user_groups_dropdown']     = $this->CostCentreModel->cc_group_dropdown();
        return view($this->folder_path.'cost_centres/add_group',$data);		
    }
   
    public function modify_group($group_id)
    {
        if($this->request->getMethod() == 'post'){	
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
            if(!$this->validate($rules)){
                $this->session->setFlashdata('error_message', $this->validator->listErrors());
            }else{				
            
                $_POST['comp_id']   =   $this->company_id;
                $this->CostCentreModel->update_group($_POST,$group_id);
                
                $log = [
                'uuid_aicountly' => $this->session->get('uuid_aicountly'),
                'log_date' => date('Y-m-d'),
                'log_time' => date('H:i:s'),
                'log_action_tags' => 'update',
                'log_field_id' => $group_id,
                'log_field_name' => $_POST['cc_grp_name'],
                'log_field_type' => 'cc group',
                ];
                $this->LogModel->add_log($log);
                
                return redirect()->to($this->base_url.'cost_centres/list_group');
            }					 									
        }	 
        
        $data['group_info']             = $this->CostCentreModel->cc_group_info($group_id,$this->company_id);
        $data['folder_path']            = $this->folder_path;
        $data['group_id']               = $group_id;		
        $data['base_url']               = $this->base_url;	
        $data['user_groups_dropdown']   = $this->CostCentreModel->cc_group_dropdown($group_id);
        return view($this->folder_path.'cost_centres/edit_group',$data);		
    }
   
    public function remove_groups($ids){
        
        $default_groups = [];
        $errors = [];
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
                $this->LogModel->add_log($log);
                
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