<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\MaterialCentersModel;
use App\Models\Grpcomp\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Material_centres extends BaseController
{
  function __construct()
    {  
	        helper(['form', 'url','text']);
		    $this->MaterialCentersModel = new MaterialCentersModel();
		    $this->LogModel        = new ERPLogModel();
            $this->CommonModel     = new CommonModel();	
		    $this->auth_session      = new auth_session();			
	   	    $this->auth_session->group_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().'/'.getenv('GroupPath');
			$this->admin_url      = base_url().'/'.getenv('AdminPath');
			$this->folder_path   = getenv('GroupPath');
			$this->session    	 = \Config\Services::session();
			$this->forge         = \Config\Database::forge();
			$this->company_id    =  $this->session->get('ses_company_id');
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->enc_string    = new enc_string();
    }
 	  public  function ajax_centres_view()
	 {
		echo $response =  $this->MaterialCentersModel->ajax_centres_list();	
		
	 }   

   public function list_centres()
    {
		$group_comp_list         = $this->CommonModel->all_group_companies();
		
		if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $this->CommonModel->choose_grp_company($comp_id_info[1]);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->remove('ses_company_id');
			$comp_id='';
			$grpcmpid='';
		}
		
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']        = $this->admin_url;
		$data['session']         = $this->session;
		$data['grpcmpid']        = $grpcmpid;
		
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
		
        $data['comp_list'] = $comp_list;
		$data['message_output']  = $this->message_output; 
		$data['folder_path']     = $this->folder_path;		
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session; 
		$data['enc_string']      = $this->enc_string;
        			
		return view($this->folder_path.'material_centres/list_centres',$data);		
    }  
  
	 public function ajax_mcstores_view()
	{
	    if(isset($_POST["pq_curpage"]))
		$pq_curPage = (int)$_POST["pq_curpage"];
	   else
		  $pq_curPage = 1;
	  
	  if(isset($_POST["pq_rpp"]))
		$limit     = (int)$_POST["pq_rpp"];
	  else
		 $limit     = 100; 
	 	$response = $this->MaterialCentersModel->ajax_mc_stores_list($limit,$pq_curPage);

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}
	 
	 
   public function mc_stores()
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
		$data['admin_url']        = $this->admin_url;
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
       			
		return view($this->folder_path.'material_centres/mc_stores',$data);		
    }    
    
   public function ledger_detail($mat_cent_id,$start_date=NULL,$end_date=NULL)
   {
       if(!$mat_cent_id) 
	    return redirect()->to($this->base_url.'material_centres/list_centres'); 
	
      $ses_comp_fy_id         =  $this->session->get('ses_comp_fy_id');	
	  $mc_name                =  $this->MaterialCentersModel->get_mc_name($mat_cent_id);
	  
	  $mc_transactions        = $this->MaterialCentersModel->load_mc_transactions($mat_cent_id,$start_date,$end_date);
	
	  $data['mc_name']         = $mc_name;
	  $data['mc_transactions'] = $mc_transactions;
     return view($this->folder_path.'material_centres/mc_ledger_view',$data);   
   }
   
   	 public function remove_mcstores($ids){
		if(!$ids)
		  return redirect()->to($this->base_url.'material_centres/mc_stores');
			
		 $default_groups = [];
		 $errors         = [];
		 $ids2           = explode(",",$ids);
		 foreach($ids2 as $id){
    		$this->MaterialCentersModel->remove_single_store($id);
		  }
		 return redirect()->to($this->base_url.'material_centres/mc_stores');
		 die;		
	  } 
	  
    public function add_mc_store()
    {
		 if($this->request->getMethod() == 'post'){	
		 
		    $mat_cent_id      = $this->request->getVar('mat_cent_id'); 
			$mc_store_name    = $this->request->getVar('mc_store_name'); 
			$mc_store_alias   = $this->request->getVar('mc_store_alias'); 
		
			$rules = [				
				    'mc_store_name' => [
					'label'  => 'Store Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter store name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
				 $insert_data   = [
						'mc_store_name'    => $mc_store_name,
						'mc_store_alias'   => $mc_store_alias,
						'mat_cent_id'      => $mat_cent_id							
					   ];				
    			     $exists = $this->MaterialCentersModel->add_mc_stores($insert_data);
    			    
    			    if($exists=="0"){
    				  $this->message_output->set_error('Mc store name already exists.');
    			     }
    				else{										
    					 return redirect()->to($this->base_url.'material_centres/mc_stores');
    					 die;	
    				  }				 
		    	}					 									
		   }			
				 
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;		
		$data['base_url']        = $this->base_url;	
       	$data['mc_centres_dropdown']   = $this->MaterialCentersModel->mc_centres_dropdown();
		
		
		return view($this->folder_path.'material_centres/add_mc_stores',$data);		
    }
    
    public function modify_mcstore($store_id)
     {
		 if($this->request->getMethod() == 'post'){	
		 
		    $mat_cent_id      = $this->request->getVar('mat_cent_id'); 
			$mc_store_name    = $this->request->getVar('mc_store_name'); 
			$mc_store_alias   = $this->request->getVar('mc_store_alias'); 
		
			$rules = [				
				    'mc_store_name' => [
					'label'  => 'Store Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter store name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
				 $update_data   = [
						'mc_store_name'    => $mc_store_name,
						'mc_store_alias'   => $mc_store_alias,
						'mat_cent_id'      => $mat_cent_id							
					   ];				
    			    $exists = $this->MaterialCentersModel->modify_mc_stores($store_id,$update_data);
    			  	return redirect()->to($this->base_url.'material_centres/mc_stores');
    			    die;
    					 
		    	}					 									
		   }			

		 $mc_store_info = $this->MaterialCentersModel->mc_store_info($store_id);
		 if(!$mc_store_info)
				 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

		$data['message_output']      = $this->message_output;
		$data['store_id']            = $store_id;
		$data['folder_path']         = $this->folder_path;		
		$data['base_url']            = $this->base_url;	
		$data['enc_string']          = $this->enc_string;	
       	$data['mc_centres_dropdown'] = $this->MaterialCentersModel->mc_centres_dropdown();
       	$data['mcstore_info']        = $mc_store_info;
		return view($this->folder_path.'material_centres/modify_mc_stores',$data);		
    }
    
   public function add_centres()
    {
		 if($this->request->getMethod() == 'post'){	
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
				 $insert_data   = [
						'comp_id'          => $this->company_id,
						'mat_cent_name'    => $this->enc_string->nc_string($mat_cent_name,'en'),
						'mat_cent_grp_id'  => $mat_cent_grp,
						'mat_cent_alias'   => $this->enc_string->nc_string($mat_cent_alias,'en'),
						'mat_cent_print'   => $this->enc_string->nc_string($mat_cent_print,'en'),
						'mat_cent_add1'    => $this->enc_string->nc_string($mat_cent_add1,'en'),
						'mat_cent_add2'    => $this->enc_string->nc_string($mat_cent_add2,'en'),
						'mat_cent_city'    => $this->enc_string->nc_string($mat_cent_city,'en'),
						'mat_cent_pin'     => $mat_cent_pin,
						'mat_cent_state'   => $mat_cent_state,
						'mat_cent_country' => $mat_cent_country							
					   ];				
			    $exists = $this->MaterialCentersModel->add_centres($insert_data);
			    if($exists=="0"){
				  $this->message_output->set_error('Centre name already exists.');
			     }
				else{										
					 return redirect()->to($this->base_url.'material_centres/list_centres');
					 die;	
				  }				 
		    	}					 									
		   }			
				 
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;		
		$data['base_url']        = $this->base_url;	
        $data['CountryDropdown']  = $this->CommonModel->CountryDropdown();
		$data['material_group']   = $this->MaterialCentersModel->centres_group_dropdown();
		
		
		return view($this->folder_path.'material_centres/add_material_centres',$data);		
    }
	
	 public function remove_centres($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'material_centres/list_centres');
			
		$default_groups = [];
		 $errors = [];
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
		     
		     if($stat)
		     {
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
		// return redirect()->to($this->base_url.'material_centres/list_centres');
		  die;		
	  } 
	  
	  
	public function modify_centre($centre_id)
    {
		if(!$centre_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		 if($this->request->getMethod() == 'post'){	
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
				 $update_data   = [
						'mat_cent_name'    => $this->enc_string->nc_string($mat_cent_name,'en'),
						'mat_cent_grp_id'  => $mat_cent_grp,
						'mat_cent_alias'   => $this->enc_string->nc_string($mat_cent_alias,'en'),
						'mat_cent_print'   => $this->enc_string->nc_string($mat_cent_print,'en'),
						'mat_cent_add1'    => $this->enc_string->nc_string($mat_cent_add1,'en'),
						'mat_cent_add2'    => $this->enc_string->nc_string($mat_cent_add2,'en'),
						'mat_cent_city'    => $this->enc_string->nc_string($mat_cent_city,'en'),
						'mat_cent_pin'     => $mat_cent_pin,
						'mat_cent_state'   => $mat_cent_state,
						'mat_cent_country' => $mat_cent_country							
					   ];				
			    $exists = $this->MaterialCentersModel->update_centres($centre_id,$update_data);
			    return redirect()->to($this->base_url.'material_centres/list_centres');
				die;	
				  			 
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
		$data['enc_string']       = $this->enc_string;		
	    return view($this->folder_path.'material_centres/edit',$data);		
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
	 	$response = $this->MaterialCentersModel->ajax_group_list($limit,$pq_curPage);

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}
	
   public function group_list()
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
		$data['admin_url']        = $this->admin_url;
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
		return view($this->folder_path.'material_centres/list_group',$data);		
    } 
   
   public function add_group()
    {
		 if($this->request->getMethod() == 'post'){	
		    $group_name        = $this->request->getVar('group_name'); 
		    $group_name_alias  = $this->request->getVar('group_name_alias');
			$primary_group     = $this->request->getVar('primary_group');
			$mat_cent_main     = $this->request->getVar('mat_cent_main');		    				
			$rules = [				
				    'group_name' => [
					'label'  => 'Group Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter group name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
				 $insert_data   = [
						'comp_id'          => $this->company_id,
						'mc_grp_name'      => $this->enc_string->nc_string($group_name,'en'),
						'mc_alias'         => $this->enc_string->nc_string($group_name_alias,'en'),
						'mc_primary'       => $primary_group,
					    'under_mc_grp_id'  => $mat_cent_main,		
					   ];	
					   
			    $exists = $this->MaterialCentersModel->add_group($insert_data);
			   if($exists=="0"){
				  $this->message_output->set_error('Group name already exists.');
			   }
				else{					
					 return redirect()->to($this->base_url.'material_centres/group_list');
					 die;	
				  }				 
		    	}					 									
		   }			
				 
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;			
		$data['base_url']         = $this->base_url;	
		$data['presdfnd_main']    = material_centres_grp_array();
	    return view($this->folder_path.'material_centres/add_group',$data);		
    }
  public function remove_centre_grps($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'material_centres/group_list');
			
		$default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
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
		
		 if($this->request->getMethod() == 'post'){	
		    $group_name        = $this->request->getVar('group_name'); 
		    $group_name_alias  = $this->request->getVar('group_name_alias');
			$primary_group     = $this->request->getVar('primary_group');
			$mat_cent_main     = $this->request->getVar('mat_cent_main');		    				
			$rules = [				
				    'group_name' => [
					'label'  => 'Group Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter group name',
					     ]
					   ]	   
			       ];
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
				 $update_data   = [
						'mc_grp_name'     => $this->enc_string->nc_string($group_name,'en'),
						'mc_alias'        => $this->enc_string->nc_string($group_name_alias,'en'),
						'mc_primary'      => $primary_group,
					    'under_mc_grp_id' => $mat_cent_main,		
					   ];				
			    $this->MaterialCentersModel->update_group($update_data,$group_id);
			   	return redirect()->to($this->base_url.'material_centres/group_list');
				die;	
				 			 
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
		$data['enc_string']       = $this->enc_string;		
		$data['presdfnd_main']    = material_centres_grp_array();
	    return view($this->folder_path.'material_centres/edit_group',$data);		
    }
  
}