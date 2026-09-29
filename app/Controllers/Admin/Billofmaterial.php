<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BillofmaterialModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;


class Billofmaterial extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->BillofmaterialModel = new BillofmaterialModel();	
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    = new enc_string();
    }
    
    public  function ajax_billofmaterial()
	 {
		echo $response =  $this->BillofmaterialModel->ajax_billofmaterial_list();	
		
	 }

  
   public function index()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['enc_string']      = $this->enc_string;	 
		return view($this->folder_path.'bill_of_material/view',$data);		
    }
	public function list_group()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['groups_list']      = $this->BillofmaterialModel->ajax_group_list();
		return view($this->folder_path.'bill_of_material/list_group',$data);		
    } 
  public function add_group()
    {
		 if($this->request->getMethod() == 'post'){	
		    $group_name        = $this->request->getVar('group_name'); 
		    $group_name_alias  = $this->request->getVar('group_name_alias');
			$primary_group     = $this->request->getVar('primary_group');	
			
		   	if($primary_group=='1'){
			  $acc_grp_parent_id    = 0;
			  $under_acc_grp_id     = 0;
		   	}
			elseif($primary_group=='0'){
			    $under_acc_grp_id    = $this->request->getVar('no_group_under');
			    $acc_grp_parent_id   =0;
			}
			
			
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
              $errors = $this->validator->getErrors();
	        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
				 $insert_data   = [
						'bom_grp_name'      => ucwords(clean($group_name)),
						'bom_grp_alias'     => ucwords(clean($group_name_alias)),
						'bom_grp_primary'   => $primary_group,
						'under_bom_grp_id'   => $under_acc_grp_id
					   ];				
			    $group_id = $this->BillofmaterialModel->add_group($insert_data);
			   if($group_id=="0"){

	           return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Group name already exists.']]);
				
			   }
				else{				 
					$log = [
				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
				            'log_date' => date('Y-m-d'),
				            'log_time' => date('H:i:s'),
				            'log_action_tags' => 'add',
				            'log_field_id' => $group_id,
				            'log_field_name' => $group_name,
				            'log_field_type' => 'bom group',
				        ];
				    $this->LogModel->add_log($log);
				    
					 return json_encode(['status' => true, 'message' => 'Data Added']);	
				  }				 
		    	}					 									
		   }			
		$data['message_output']           = $this->message_output;
		$data['folder_path']              = $this->folder_path;	
		$data['base_url']                 = $this->base_url;	
	    $data['user_groups_dropdown']     = $this->BillofmaterialModel->group_main_dropdown();		
	    return view($this->folder_path.'bill_of_material/add_group',$data);		
    }
   
   public function modify_group($group_id)
    {
		if(!$group_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 

		 if($this->request->getMethod() == 'post'){	
		    $group_name       = $this->request->getVar('group_name'); 
		    $group_name_alias = $this->request->getVar('group_name_alias');
				$primary_group    = $this->request->getVar('primary_group');
				$rules = [				
				'group_name' => [
					'label'  => 'Group Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter group name',
					   ]]				  			   
			       ];
			
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
                
                	if($primary_group=='1'){
        			  $acc_grp_parent_id    = 0;
        			  $under_acc_grp_id     = 0;
        		   	}
        			elseif($primary_group=='0'){
        			    $under_acc_grp_id    = $this->request->getVar('no_group_under');
        			    $acc_grp_parent_id   =0;
        			}
        			
			
				$update_data   = [
				        'bom_grp_name'    => ucwords(clean($group_name)),
						'bom_grp_alias'   => ucwords(clean($group_name_alias)),
						'bom_grp_primary' => $primary_group,
						'under_bom_grp_id'   => $under_acc_grp_id
					  ];
			        $this->BillofmaterialModel->update_group($update_data,$group_id);
			        
			        $log = [
				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
				            'log_date' => date('Y-m-d'),
				            'log_time' => date('H:i:s'),
				            'log_action_tags' => 'update',
				            'log_field_id' => $group_id,
				            'log_field_name' => $group_name,
				            'log_field_type' => 'item group',
				        ];
				    $this->LogModel->add_log($log);
				    
					return redirect()->to($this->base_url.'billofmaterial/list_group');
					die;		 
		    	}					 									
		   }	 


		 $get_group_info = $this->BillofmaterialModel->get_group_info($group_id);
		 if(!$get_group_info)
		 		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		   
		$data['get_info']         = $get_group_info;
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['group_id']         = $group_id;		
        $data['base_url']         = $this->base_url;	
        $data['enc_string']       = $this->enc_string;	
        $data['user_groups_dropdown']     = $this->BillofmaterialModel->group_main_dropdown();
	    return view($this->folder_path.'bill_of_material/edit_group',$data);		
    }
   
 public function remove_groups($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'bill_of_material/list_group'); 
		 
		 $default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $item_id){
		     
		    $stat = true;
		    $name = $this->BillofmaterialModel->get_item_group_name($item_id);;
		     if (in_array($item_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! BOM Group "'.$name.'" belongs to Default Group');
		     }
		     if ($this->BillofmaterialModel->check_item_with_group($item_id)){ //check if account exist with that group
		         $stat = false;
		         array_push($errors, 'Failed! BOM Group "'.$name.'" has one or more associated bom');
		     }
		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'bom group',
    		            ];
    		     $this->LogModel->add_log($log);
    		     
    		     $this->BillofmaterialModel->remove_single_groups($item_id);
				 
		     }
		     
		 }
		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		 
		 
		return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);		
	  }
   public function remove($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'billofmaterial');
	
	     $ids2 = explode(",",$ids);
		 foreach($ids2 as $bom_id){		     
		     $this->BillofmaterialModel->deletebom($bom_id);		   
		  }	 
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);		
	  }
	  
   public function add()
    {
		 if($this->request->getMethod() == 'post'){	
		     $item_consumed       = json_decode($this->request->getVar('grd1data'),true); 
			 $item_produced       = json_decode($this->request->getVar('grd2data'),true);  
			 $byproducts_produced = json_decode($this->request->getVar('grd3data'),true);  
			 $additional_cost     = json_decode($this->request->getVar('grd4data'),true);  
             $bom_name            = trim($this->request->getVar('bom_name'));
			 $consumption_pricing = trim($this->request->getVar('consumption_pricing'));
			 $bom_group            = trim($this->request->getVar('bom_group'));
			 
             $this->BillofmaterialModel->save_data($bom_group,$consumption_pricing,$bom_name,$item_consumed,$item_produced,$byproducts_produced,$additional_cost);
		     return redirect()->to($this->base_url.'billofmaterial');
		     die;
		  }							 
		$data['message_output']     = $this->message_output;
		$data['base_url']           = $this->base_url;	
		$data['folder_path']        = $this->folder_path;	
		$data['bom_group']         = $this->BillofmaterialModel->bom_group_dropdown();
        $data['units_list']         = $this->BillofmaterialModel->units_dropdown($this->company_id);
		$data['expense_heads_list'] = $this->BillofmaterialModel->expense_heads_dropdown($this->company_id);
		$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
		$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

		$data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
        
	    return view($this->folder_path.'bill_of_material/add',$data);		
    }	
  
   public function modify($billofmaterial_id)
    {
        	if(!$billofmaterial_id)
						throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 

			if($this->request->getMethod() == 'post'){	
			 
		     $item_consumed       = json_decode($this->request->getVar('grd1data'),true); 
			 $item_produced       = json_decode($this->request->getVar('grd2data'),true);  
			 $byproducts_produced = json_decode($this->request->getVar('grd3data'),true);  
			 $additional_cost     = json_decode($this->request->getVar('grd4data'),true);  
             $bom_name            = trim($this->request->getVar('bom_name'));
			 $bom_group            = trim($this->request->getVar('bom_group'));
			 $consumption_pricing = trim($this->request->getVar('consumption_pricing'));
			
           $this->BillofmaterialModel->update_bom_data($bom_group,$consumption_pricing,$bom_name,$item_consumed,$item_produced,$byproducts_produced,$additional_cost,$billofmaterial_id);
		   return redirect()->to($this->base_url.'billofmaterial');
		   die;
		  }	

		  $billofmaterial_info = $this->BillofmaterialModel->billofmaterial_info($billofmaterial_id);
		  if(!$billofmaterial_info){
		  	throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		  }

        $data['bom_info']            = $billofmaterial_info;
	    $data['bom_group']           = $this->BillofmaterialModel->bom_group_dropdown();		
		$data['message_output']      = $this->message_output;
		$data['base_url']            = $this->base_url;	
		$data['folder_path']         = $this->folder_path;	
		$data['billofmaterial_id']   = $billofmaterial_id;
        $data['units_list']          = $this->BillofmaterialModel->units_dropdown($this->company_id);
		$data['expense_heads_list']  = $this->BillofmaterialModel->expense_heads_dropdown($this->company_id);
		$item_file_name   = 'item'.$this->session->get('ses_comp_fy_id').'.json';
        $bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';
		 $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$item_file_name);
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name);
	    return view($this->folder_path.'bill_of_material/edit',$data);		
    }	
}