<?php
namespace App\Controllers\Admin;
use App\Models\Admin\UnitsModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\ERPLogModel;

class Units extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text','custom']);
		$this->UnitsModel      = new UnitsModel();
		$this->LogModel        = new ERPLogModel();
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
 
   public function list()
    {
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['units_list']         = $this->UnitsModel->ajax_units_list();
		return view($this->folder_path.'units/list',$data);		
    } 
    
  public function add()
    {
		 if($this->request->getMethod() == 'POST'){	     	     	     
		     
		    $item_unit        = $this->request->getVar('item_unit');
		    $item_unit_alias  = $this->request->getVar('item_unit_alias');
	        $Item_unit_print  = $this->request->getVar('Item_unit_print');
			$Item_unit_uqc    = $this->request->getVar('Item_unit_uqc'); 
			$rules = [				
				'item_unit' => [
					'label'  => 'Item Unit',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter unit name',
					   ]],				
				  'item_unit_alias' => [
					'label'  => 'Alias',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter alias',
					], 
				   ],
				'Item_unit_print' => [
					'label'  => 'Print Name',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter print name',
					  ], 
				   ]				   
			    ];			
            if(!$this->validate($rules)){
              $errors  = $this->validator->getErrors();
			  $errors  = array_values($errors_list);
	          return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{  
				try{	
		    	  $insert_data   = [
							'cmp_id'         => $this->company_id,
							'itm_unit_name'  => clean($item_unit),
							'itm_unit_alias' => clean($item_unit_alias),
							'itm_unit_print' => clean($Item_unit_print),
							'itm_unit_uqc'   => $Item_unit_uqc							
						   ];					
					  $item_unit_id = $this->UnitsModel->add_units($insert_data);
					  if($item_unit_id=="-1"){
						 return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Unit name already exists.']]);
					   }
					  else if($item_unit_id=="-2"){
						  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Unit alias already exists.']]);
					   }
					  else if($item_unit_id=="-3"){
						  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Unit print already exists.']]);
					   }	
					  else{
						return json_encode(['status' => true, 'message' => 'Data Inserted']);					
					   }
					  	
				  }
				  catch (\Throwable $e) {
						log_message('error', 'DB Query Error: ' . $e->getMessage());
						return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
					}		
				}					 
									
			}			
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']           = $this->base_url;
	    return view($this->folder_path.'units/add',$data);		
    }
	
	public function remove_units($ids_info){
		if(!$ids_info)
			return redirect()->to($this->base_url.'units/list');
			
		 $default_groups = [];
		 $errors = [];
		 $ids    = urlSafeBase64Decode($ids_info);
		 $ids2   = explode(",",$ids);
		 foreach($ids2 as $item_unit_id){		     
		    $stat = true;
		    $name = $this->UnitsModel->get_item_unit_name($item_unit_id);		    
		     if (in_array($item_unit_id, $default_groups)){ //check if unit belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Item Unit "'.$name.'" belongs to Default Units');
		     }
		     if ($this->UnitsModel->check_item_with_unit($item_unit_id)){ //check if unit exist with that item
		         $stat = false;
		         array_push($errors, 'Failed! Item Unit "'.$name.'" has one or more associated items');
		     }		     
		     if($stat){
				 $this->UnitsModel->remove_single_units($item_unit_id);
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_unit_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'item unit',
    		            ];
    		     //$this->LogModel->add_log($log);    		     
    		     
		      }		     
		    }		 
		 if(count($errors))
		 {
             $this->session->setFlashdata('error_array_message', $errors);
		 }
		return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);		
	  } 
	  
	public function modify($unit_id)
    {
		if(!$unit_id)
			return redirect()->to($this->base_url.'units/list'); 		
		if($this->request->getMethod() == 'POST'){
			 $item_unit        = $this->request->getVar('item_unit');
		     $item_unit_alias  = $this->request->getVar('item_unit_alias');
	         $Item_unit_print  = $this->request->getVar('Item_unit_print');
			 $Item_unit_uqc    = $this->request->getVar('Item_unit_uqc'); 
			 $rules = [				
				'item_unit' => [
					'label'  => 'Item Unit',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter unit name',
					   ]],				
				  'item_unit_alias' => [
					'label'  => 'Alias',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter alias',
					], 
				   ],
				'Item_unit_print' => [
					'label'  => 'Print Name',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter print name',
					  ], 
				   ]				   
			    ];	
            if(!$this->validate($rules)){
               $this->message_output->set_error($this->validator->listErrors());
            }else{
				     $update_data = [
						    'itm_unit_name'  => clean($item_unit),
							'itm_unit_alias' => clean($item_unit_alias),
							'itm_unit_print' => clean($Item_unit_print),
							'itm_unit_uqc'   => $Item_unit_uqc	
                        ];							  
			     	  $response = $this->UnitsModel->update_unit($unit_id,$update_data);
					  if(!$response['status']){        				
							return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
						}
						else{
							return json_encode(['status' => true, 'message' => 'Data Updated']);
						}										  
			           			
			       }			
		     }
		$data['get_info']           = $this->UnitsModel->get_info($unit_id);
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['unit_id']            = $unit_id;		
        $data['base_url']           = $this->base_url;		
	    return view($this->folder_path.'units/edit',$data);	
    }
	
}