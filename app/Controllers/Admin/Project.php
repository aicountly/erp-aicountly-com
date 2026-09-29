<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ProjectModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Project extends BaseController
{
  	function __construct()
    {  
	    helper(['form', 'url','text','custom_hepler']);
		$this->ProjectModel = new ProjectModel();		
		$this->auth_session  = new auth_session();					
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
	    $this->auth_session->is_company_opened();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
	  
    }
    
    public function index()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'project/list', $data);		
    }

    public function ajax_list()
    {
    	echo $this->ProjectModel->get_project_list();
    }

    public function add()
    {
    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
		  	$rules = [				
				'project_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Project Name is required',
				   ],
			  	]		
			];
			
	        if(!$this->validate($rules)){
	        	$errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }
            try{
	        $post = $this->request->getPost();
			
	        if($this->ProjectModel->check_project_name_exists($post['project_name'],0)){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Project Name already exists']]);
	        }
	        $lia_project_op_bal = floatval($post['lia_project_op_bal']);
	        $lia_project_op_drcr = $post['lia_project_op_drcr'];
	        if($lia_project_op_drcr == 'C')
	        	$lia_project_op_bal = -$lia_project_op_bal;

	        $lia_project_py_bal = floatval($post['lia_project_py_bal']);
	        $lia_project_py_drcr = $post['lia_project_py_drcr'];
	        if($lia_project_py_drcr == 'C')
	        	$lia_project_py_bal = -$lia_project_py_bal;

	        //--------
	        $ast_project_op_bal = floatval($post['ast_project_op_bal']);
	        $ast_project_op_drcr = $post['ast_project_op_drcr'];
	        if($ast_project_op_drcr == 'C')
	        	$ast_project_op_bal = -$ast_project_op_bal;

	        $ast_project_py_bal = floatval($post['ast_project_py_bal']);
	        $ast_project_py_drcr = $post['ast_project_py_drcr'];
	        if($ast_project_py_drcr == 'C')
	        	$ast_project_py_bal = -$ast_project_py_bal;
			
			$map = ['D' => 1, 'C' => 2];
			$project_li_txn_dr_cr  = $map[$lia_project_op_drcr]  ?? 0;
			$project_ast_txn_dr_cr = $map[$ast_project_op_drcr] ?? 0;
			
	        $data = [
					'cmp_id' 			 => $this->company_id,
					'project_name' 		 => $post['project_name'],
					'project_alias' 	 => $post['project_alias'],
					'project_print_name' => $post['project_print']	        	
				  ];
				$project_id = $this->ProjectModel->add_project_data($data);
				if($project_id=="-1"){
					return json_encode(['status'=>false,'message'=>'Validation Error','errors'=>['Project name already exists.']]);				
				 }
				else{	
				    $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>11,"crs_mst_id"=>$project_id,"under_crs_mst_id"=>$post['project_grp_id'],
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				    $this->ProjectModel->add_undercrsmt($mst_insert_data);
					//1 for Asset  , 2 for Liability
					$data = [
						'project_id'		=> $project_id,
						'cmp_id'            => $this->company_id,
						'cmpfymastr_id'     => $this->fy_id,
						'project_op_bal'	=> $lia_project_op_bal,
						'project_py_bal'	=> $lia_project_py_bal,
						'project_txn_type'	=> 1,
						'hobo_id'			=> $this->bo_id,
					 ];
					$this->ProjectModel->add_project_op_data($data);

					$data = [
						'project_id'		=> $project_id,
						'cmp_id'            => $this->company_id,
						'cmpfymastr_id'     => $this->fy_id,
						'project_op_bal'	=> $ast_project_op_bal,
						'project_py_bal'	=> $ast_project_py_bal,
						'project_txn_type'	=> 2,
						'hobo_id'			=> $this->bo_id,
					];
					$this->ProjectModel->add_project_op_data($data);
					
					$project_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
					$txn_data = array("cmp_id"=>$this->company_id,"project_id"=>$project_id,"project_txn_date"=>$project_txn_date,
									  "project_txn_dr_cr"=>$project_ast_txn_dr_cr,"project_txn_amt"=>abs($ast_project_op_bal),"vch_txn_id"=>NULL,"hobo_id"=>$this->bo_id,
									  "project_txn_type"=>2
									  );
									
					$this->ProjectModel->insert_project_txn_entry($txn_data);
					
					$txn_datas = array("cmp_id"=>$this->company_id,"project_id"=>$project_id,"project_txn_date"=>$project_txn_date,
									  "project_txn_dr_cr"=>$project_li_txn_dr_cr,"project_txn_amt"=>abs($lia_project_op_bal),"vch_txn_id"=>NULL,"hobo_id"=>$this->bo_id,
									  "project_txn_type"=>1
									  );
										  
					$this->ProjectModel->insert_project_txn_entry($txn_datas);
					return json_encode(['status' => true, 'message' => 'Data Inserted']);
					}
			   }
			   catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				} 			
	    }
		
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['project_groups']  = $this->ProjectModel->get_project_groups();
		return view($this->folder_path.'project/add', $data);		
    }

    public function edit($project_id)
    {
    	$project	= $this->ProjectModel->get_project_data($project_id);
    	
	
    	if(empty($project)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
			
			$rules = [				
				'project_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Project Name is required',
				   ],
			  	]		
			];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $post = $this->request->getPost();

	        try{
	        $post = $this->request->getPost();
	        if($this->ProjectModel->check_project_name_exists($post['project_name'],$project['project_id'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Project Name already exists']]);
	        }

	        $lia_project_op_bal = floatval($post['lia_project_op_bal']);
	        $lia_project_op_drcr = $post['lia_project_op_drcr'];
	        if($lia_project_op_drcr == 'C')
	        	$lia_project_op_bal = -$lia_project_op_bal;

	        $lia_project_py_bal = floatval($post['lia_project_py_bal']);
	        $lia_project_py_drcr = $post['lia_project_py_drcr'];
	        if($lia_project_py_drcr == 'C')
	        	$lia_project_py_bal = -$lia_project_py_bal;

	        
	        $ast_project_op_bal = floatval($post['ast_project_op_bal']);
	        $ast_project_op_drcr = $post['ast_project_op_drcr'];
	        if($ast_project_op_drcr == 'C')
	        	$ast_project_op_bal = -$ast_project_op_bal;

	        $ast_project_py_bal = floatval($post['ast_project_py_bal']);
	        $ast_project_py_drcr = $post['ast_project_py_drcr'];
	        if($ast_project_py_drcr == 'C')
	        	$ast_project_py_bal = -$ast_project_py_bal;
			
			$map = ['D' => 1, 'C' => 2];
			$project_li_txn_dr_cr  = $map[$lia_project_op_drcr]  ?? 0;
			$project_ast_txn_dr_cr = $map[$ast_project_op_drcr] ?? 0;

			 $data = [
					'project_name' 		 => $post['project_name'],
					'project_alias' 	 => $post['project_alias'],
					'project_print_name' => $post['project_print'],
					'project_id'		=> $project_id,
				  ];
				$project_ids = $this->ProjectModel->update_project_data($data,$project_id);
				if($project_ids=="-1"){
					return json_encode(['status'=>false,'message'=>'Validation Error','errors'=>['Project name already exists.']]);				
				 }
				else{	
				    $mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>11,"crs_mst_id"=>$project_id,"under_crs_mst_id"=>$post['project_grp_id'],
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				    $this->ProjectModel->update_undercrsmt($mst_insert_data,$project_id,11);
					//1 for Asset  , 2 for Liability
					$data = [
						'project_id'		=> $project_id,
						'cmp_id'            => $this->company_id,
						'cmpfymastr_id'     => $this->fy_id,
						'project_op_bal'	=> $lia_project_op_bal,
						'project_py_bal'	=> $lia_project_py_bal,
						'project_txn_type'	=> 1,
						'hobo_id'			=> $this->bo_id,
					 ];
					$this->ProjectModel->update_project_op_data($data);

					$data = [
						'project_id'		=> $project_id,
						'cmp_id'            => $this->company_id,
						'cmpfymastr_id'     => $this->fy_id,
						'project_op_bal'	=> $ast_project_op_bal,
						'project_py_bal'	=> $ast_project_py_bal,
						'project_txn_type'	=> 2,
						'hobo_id'			=> $this->bo_id,
					];
					$this->ProjectModel->update_project_op_data($data);
					
					$project_txn_date = date('Y-m-d',strtotime(validate_fy_from_date('')));
					$txn_data = array("cmp_id"=>$this->company_id,"project_id"=>$project_id,"project_txn_date"=>$project_txn_date,
									  "project_txn_dr_cr"=>$project_ast_txn_dr_cr,"project_txn_amt"=>abs($ast_project_op_bal),"vch_txn_id"=>NULL,"hobo_id"=>$this->bo_id,
									  "project_txn_type"=>2
									  );
					$this->ProjectModel->update_project_txn_entry($txn_data,$project_id,2);
					
					$txn_data = array("cmp_id"=>$this->company_id,"project_id"=>$project_id,"project_txn_date"=>$project_txn_date,
									  "project_txn_dr_cr"=>$project_li_txn_dr_cr,"project_txn_amt"=>abs($lia_project_op_bal),"vch_txn_id"=>NULL,"hobo_id"=>$this->bo_id,
									  "project_txn_type"=>1
									  );
					$this->ProjectModel->update_project_txn_entry($txn_data,$project_id,1);
					return json_encode(['status' => true, 'message' => 'Data Updated']);
					}
			   }
			   catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				} 
				
				
	        

	       
	    }

		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['project_groups']  = $this->ProjectModel->get_project_groups();
		$data['project']		 = $project;

		return view($this->folder_path.'project/edit', $data);		
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
		     $name   = $this->ProjectModel->get_project_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					   if ($this->ProjectModel->check_project_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Project "'.$name.'" already exists');
					   }
					}
					 if($status==0){					  
						 if ($this->ProjectModel->check_project_txn_exists($account_id)){
							 $stat = false;
							 array_push($errors, 'Failed! Project "'.$name.'" has one or more associated vouchers');
						 } 					 
						 if ($this->ProjectModel->check_project_opn_exists($account_id)){
							 $stat = false;
							 array_push($errors, 'Failed! Project "'.$name.'" has default opening');
						 } 
					 }					 
			     if($stat)
			       {	   
		           $this->ProjectModel->changestatus_single_accounts($account_id,$status);					
			       }
		        }
			 }			 
		 }
	
		  
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	}
    public function delete()
    {
    	$project_id_arr = $this->request->getVar('project_id_arr');
    	$errors = [];

    	foreach ($project_id_arr as $key => $project_id) {
    		
    		$status = true;
    		$project	= $this->ProjectModel->get_project_data($project_id);
	    	if(!empty($project)){


	        if($this->ProjectModel->check_project_vouchers($project_id)){
	        	$errors[] = $project['project_name'] . ' has one or more linked vouchers';
	        	$status = false;
	        }


	        if($status){
	        	$this->ProjectModel->delete_project($project_id);	
	        }

	    	}
    	}

    	if(count($errors)) {
	        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]); 
    	}
    	else{
    		return json_encode(['status' => true, 'message' => 'Data Deleted']);
    	}
    }

    public function groups()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;

		return view($this->folder_path.'project/groups', $data);		
    }

    public function ajax_group_list()
    {
    	echo $this->ProjectModel->get_project_group_list();    	
    }

    public function add_group()
    {
    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
			
			$rules = [				
				'project_grp_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Project Group Name is required',
				   ],
			  	],
			];
	        $errors = [];
				if(!$this->validate($rules)){
						$errors_list = $this->validator->getErrors();
						$errors  =array_values($errors_list);						
						return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
				}
	        $post = $this->request->getPost();
	        if($this->ProjectModel->check_project_grp_name_exists($post['project_grp_name'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Project Name already exists']]);
	        }
			try{
				$under_main_grp_id = 0;
				if($post['under_project_grp_id']){
					$under_main_grp_id = $post['under_project_grp_id'];
				}
				$data = [
					'cmp_id' 				=> $this->company_id,
					'project_grp_name' 		=> $post['project_grp_name'],
					'project_grp_alias' 	=> $post['project_grp_alias']
				];
				$project_id = $this->ProjectModel->add_project_group($data);
				$mst_insert_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>12,"crs_mst_id"=>$project_id,"under_crs_mst_id"=>$under_main_grp_id,
										  "crs_mst_parent_id"=>0,"under_main_id"=>0,
										  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				$this->ProjectModel->add_undercrsmt($mst_insert_data);

				return json_encode(['status' => true, 'message' => 'Data Inserted']);
			
		   }catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				} 
	    }
		
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;	
		$data['project_groups']  = $this->ProjectModel->get_project_groups();
		return view($this->folder_path.'project/add_group', $data);		
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
		     $name = $this->ProjectModel->get_project_group_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					 if ($this->ProjectModel->check_project_group_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Group "'.$name.'" already exists');
					 } 
					}
			     if($stat)
			       {	   
		           $this->ProjectModel->changestatus_single_group($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	  
    public function edit_group($project_group_id)
    {
    	$project_group	= $this->ProjectModel->get_project_group_data($project_group_id);
		if(empty($project_group)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
			
			$rules = [				
				'project_grp_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Project Group Name is required',
				   ],
			  	],

			];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $post = $this->request->getPost();

	        if($this->ProjectModel->check_project_grp_name_exists($post['project_grp_name'],$post['project_grp_id'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Project Name already exists']]);
	        }
			try{
	        $under_main_grp_id = 0;
	        if($post['under_project_grp_id']){
	        	$under_main_grp_id = $post['under_project_grp_id'];
	        }
			
	        $data = [
	        	'cmp_id' 				=> $this->company_id,
	        	'project_grp_id' 		=> $post['project_grp_id'],
	        	'project_grp_name' 		=> $post['project_grp_name'],
	        	'project_grp_alias' 	=> $post['project_grp_alias']	        	
	            ];
				
	            $response = $this->ProjectModel->update_project_group($data);
				if(!$response['status']){        				
					 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
				    }
					else{
			    $mst_update_data  = array("cmp_id"=>$this->company_id,"crs_mst_type"=>12,"crs_mst_id"=>$project_group_id,"under_crs_mst_id"=>$under_main_grp_id,
									  "crs_mst_parent_id"=>0,"under_main_id"=>0,
									  "cmpfymastr_id"=>$this->fy_id,"crs_mst_is_primary"=>0);
				$this->ProjectModel->update_undercrsmt($mst_update_data,$project_group_id,12);

	            return json_encode(['status' => true, 'message' => 'Data Updated']);
					}
			}
			catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				} 
	    }
		
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['project_group']	 = $project_group;
		$data['project_groups']  = $this->ProjectModel->get_project_groups($project_group_id);
		return view($this->folder_path.'project/edit_group', $data);		
    }

    public function delete_group()
    {
    	$project_group_id_arr = $this->request->getVar('project_grp_id_arr');
    	$errors = [];

    	foreach ($project_group_id_arr as $key => $project_group_id) {
    		
    		$status = true;
    		$project_group	= $this->ProjectModel->get_project_group_data($project_group_id);
	    	if(!empty($project_group)){

	        if($this->ProjectModel->check_sub_groups($project_group_id)){
	        	$errors[] = $project_group['project_grp_name'] . ' has one or more linked sub groups';
	        	$status = false;
	        }

	        if($this->ProjectModel->check_project_under_group($project_group_id)){
	        	$errors[] = $project_group['project_grp_name'] . ' has one or more linked projects';
	        	$status = false;
	        }


	        if($status){
	        	$this->ProjectModel->delete_project_group($project_group_id);	
	        }

	    	}
    	}

    	if(count($errors)) {
	        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
    	}
    	else{
    		return json_encode(['status' => true, 'message' => 'Data Deleted']);
	    	
    	}
    }

}