<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\UnitsModel;
use App\Controllers\BaseController;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use App\Models\Grpcomp\ERPLogModel;

class Units extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text','custom']);
		$this->UnitsModel = new UnitsModel();
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
		$this->CommonModel       =  new CommonModel();
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
 
 public function ajax_units_view()
	{
	    if(isset($_POST["pq_curpage"]))
		$pq_curPage = (int)$_POST["pq_curpage"];
	   else
		  $pq_curPage = 1;
	  
	  if(isset($_POST["pq_rpp"]))
		$limit     = (int)$_POST["pq_rpp"];
	  else
		 $limit     = 100; 
	 	$response = $this->UnitsModel->ajax_units_list($limit,$pq_curPage);

		return $response;
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
		return view($this->folder_path.'units/list',$data);		
    } 
    
  public function add()
    {
		 if($this->request->getMethod() == 'post'){	     	     	     
		     
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
		    	  $insert_data   = [
							'comp_id'         => $this->company_id,
							'item_unit'       => $this->enc_string->nc_string(ucwords(clean($item_unit)),'en'),
							'item_unit_alias' => $this->enc_string->nc_string(ucwords(clean($item_unit_alias)),'en'),
							'item_unit_print' => $this->enc_string->nc_string(ucwords(clean($Item_unit_print)),'en'),
							'item_unit_uqc'   => $this->enc_string->nc_string($Item_unit_uqc,'en')							
						   ];					
					  $result = $this->UnitsModel->add_units($insert_data);					
					  if( $result >0 ){
					  $this->message_output->set_success('Record Inserted successfully');					
					  }
					  else
						$this->message_output->set_error('Campaign already exists, Please try another one.');  
					    return redirect()->to($this->base_url.'units/list');
					    die;				    
					}					 
									
			     }			
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']           = $this->base_url;
	    return view($this->folder_path.'units/add',$data);		
    }
	
	public function remove_units($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'items/stock_category');
			
		 $default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
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
		     
		     if($stat)
		     {
    		     $log = [
    		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    		            'log_date' => date('Y-m-d'),
    		            'log_time' => date('H:i:s'),
    		            'log_action_tags' => 'delete',
    		            'log_field_id' => $item_unit_id,
    		            'log_field_name' => $name,
    		            'log_field_type' => 'item unit',
    		            ];
    		     $this->LogModel->add_log($log);
    		     
    		     $this->UnitsModel->remove_single_units($item_unit_id);
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
		
		if($this->request->getMethod() == 'post'){
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
						     'item_unit'      => $this->enc_string->nc_string(ucwords(clean($item_unit)),'en'),
							'item_unit_alias' => $this->enc_string->nc_string(ucwords(clean($item_unit_alias)),'en'),
							'item_unit_print' => $this->enc_string->nc_string(ucwords(clean($Item_unit_print)),'en'),
							'item_unit_uqc'   => $this->enc_string->nc_string($Item_unit_uqc,'en')	
                        ];		
					  
			     	   $this->UnitsModel->update_unit($unit_id,$update_data);		
			           $this->message_output->set_success('Record Updated successfully');
			           return redirect()->to($this->base_url.'units/list'); 
			           die;				
			     }			
		     }
		$data['get_info']           = $this->UnitsModel->get_info($unit_id);
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['enc_string']         = $this->enc_string;
		$data['unit_id']            = $unit_id;		
        $data['base_url']           = $this->base_url;		
	    return view($this->folder_path.'units/edit',$data);	
    }
	
}