<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BanksModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
class Banks extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text','custom']);
			$this->BanksModel     = new BanksModel();
			$this->CommonModel       =  new CommonModel();		
			$this->auth_session      = new auth_session();
			$this->auth_session->user_restrict();
			$this->auth_session->is_company_opened();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().'/'.getenv('AdminPath');
			$this->folder_path   = getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->company_id    =  $this->session->get('ses_company_id');
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->bo_id         = $this->session->get('ses_boid');
    }
   
     public  function ajax_banks_view()
	 {
		echo $response =  $this->BanksModel->ajax_banks_list();	
		
	 } 
    public function index()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'banks/view',$data);		
    } 

 
   public function add()
   {
		 if($this->request->getMethod() == 'POST'){			     
		    $rules = [				
				'comp_bank_name' => [
					'label'  => 'Bank Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]],
				'comp_bank_acc_no' => [
					'label'  => 'Bank Acc. No.',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter acc. no.',
					   ]]		
			       ];
			
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
			    $insert_data   = [
						'cmp_id'        => $this->company_id,
						'bank_name'     => $this->request->getVar('comp_bank_name'),
						'bank_acc_no'   => $this->request->getVar('comp_bank_acc_no'),
						'bank_ifsc'     => $this->request->getVar('comp_bank_ifsc'),
						'bank_micr'     => $this->request->getVar('comp_bank_micr'),
						'bank_branch'   => $this->request->getVar('comp_bank_branch'),
						'bank_addr'     => $this->request->getVar('comp_bank_adrs'),
						'bank_city'     => $this->request->getVar('comp_bank_city'),
						'bank_state'    => $this->request->getVar('state_id'),
						'bank_country'  => $this->request->getVar('comp_bank_country'),						
						'bank_pin_code' => $this->request->getVar('comp_bank_pin_code'),
						'bank_acc_type' => $this->request->getVar('comp_bank_type')
					    ];				
			    $response = $this->BanksModel->add_bank($insert_data);		
			    if(!$response['status']){
				  $this->message_output->set_error($response['message']);
			}
			else{
				$bank_id =$response['bank_id']; 
			    $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'add',
			            'log_field_id' => $bank_id,
			            'log_field_name' => $this->request->getVar('comp_bank_name'),
			            'log_field_type' => 'bank',
			        ];
			   // $this->LogModel->add_log($log);
			    return redirect()->to($this->base_url.'banks');
				 die;	
			    }				 
	    	}					 									
	   }	 
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;	
        $data['CountryDropdown']        = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']         = $this->CommonModel->StatesDropdown();
		$data['base_url']               = $this->base_url;
		$data['states_lists']           = $this->UniversalModel->show_states_lists();
		return view($this->folder_path.'banks/add',$data);		
    }
	
	public function modify($bank_id)
    {
		if(!$bank_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$bank_info = $this->BanksModel->get_bank_info($bank_id);
		if(!$bank_info){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}   
		if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){	
			 $rules = [				
				'comp_bank_name' => [
					'label'  => 'Bank Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]],
				'comp_bank_acc_no' => [
					'label'  => 'Bank Acc. No.',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter acc. no.',
					   ]]		
			       ];
				   
            if(!$this->validate($rules)){
             	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
				
				$updated_data   = [
				       'bank_name'     => $this->request->getVar('comp_bank_name'),
						'bank_acc_no'   => $this->request->getVar('comp_bank_acc_no'),
						'bank_ifsc'     => $this->request->getVar('comp_bank_ifsc'),
						'bank_micr'     => $this->request->getVar('comp_bank_micr'),
						'bank_branch'   => $this->request->getVar('comp_bank_branch'),
						'bank_addr'     => $this->request->getVar('comp_bank_adrs'),
						'bank_city'     => $this->request->getVar('comp_bank_city'),
						'bank_state'    => $this->request->getVar('state_id'),
						'bank_country'  => $this->request->getVar('comp_bank_country'),						
						'bank_pin_code' => $this->request->getVar('comp_bank_pin_code'),
						'bank_acc_type' => $this->request->getVar('comp_bank_type')
					    ];
					$response = $this->BanksModel->update_bank($updated_data,$bank_id,$bank_info['bank_id']);
			        if(!$response['status']){        				
        				  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
        			}
        			else{
        			    $bank_id = $response['bank_id'];
						$log = [
    				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    				            'log_date' => date('Y-m-d'),
    				            'log_time' => date('H:i:s'),
    				            'log_action_tags' => 'update',
    				            'log_field_id'    => $bank_id,
    				            'log_field_name'  => $this->request->getVar('comp_bank_name'),
    				            'log_field_type'  => 'bank',
    				        ];
    				    //$this->LogModel->add_log($log);
						return json_encode(['status' => true, 'message' => 'Data Updated']);    				
        			  }
				 			 
		    	}					 									
		}
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['bank_info']             = $bank_info;       
        $data['CountryDropdown']       = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']        = $this->CommonModel->StatesDropdown();		
		$data['base_url']              = $this->base_url;		
		$data['bank_id']               = $bank_id;
        $data['states_lists']          = $this->UniversalModel->show_states_lists();
		 return view($this->folder_path.'banks/edit',$data);		
    }
	
	public function remove_banks(){
		
		if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){	
		   $bnkid     = $this->request->getVar('bnkid');
		   foreach($bnkid as $bank_id){
				$this->BanksModel->remove_single_bank($bank_id);
		   }
		}
		 return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
	  }
	
}