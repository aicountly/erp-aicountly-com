<?php
namespace App\Controllers\Admin;
use App\Models\Admin\BillbybillModel;
use App\Models\Admin\ERPLogModel;
use App\Controllers\BaseController;
use App\Models\CommonModel;
use App\Libraries\auth_session;

class Billbybill extends BaseController
{
  function __construct()
    {  
	    helper(['form', 'url','text']);
		$this->BillbybillModel = new BillbybillModel();	
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
    
    public  function ajax_bbb()
	 {
		echo $response =  $this->BillbybillModel->ajax_bbb();
	 } 
	 
   public function list()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'billbybill/list',$data);		
    } 
    
    public function remove_bbb($ids_info)
    {
        if(!$ids_info)
            return redirect()->to($this->base_url.'billbybill/list');
    
        $errors = [];
		 $ids = urlSafeBase64Decode($ids_info); 
        $ids2 = explode(",",$ids);
        
        foreach($ids2 as $bb_id){
            $stat = true;
            $name = $this->BillbybillModel->get_bbb_name($bb_id);            
            if ($this->BillbybillModel->check_bbb_with_voucher($bb_id)){ 
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
                    'log_field_id'      => $bb_id,
                    'log_field_name'    => $name,
                    'log_field_type'    => 'Cost Centre',
                ];
                //$this->LogModel->add_log($log);                
                $this->BillbybillModel->remove_single_bbb($cc_id);                			
            }
        }        
        if(count($errors))
            $this->session->setFlashdata('error_array_message', $errors);        
       return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
    } 
   
   public function change_status(){
   	    $status_ids    = $this->request->getVar('pss_status_val'); 
		$ids_info      = $this->request->getVar('accidids');		
		$ids           = base64_decode($ids_info);  		 
		 // when activate master then check is it any master exists ofthe same name or not 
		 $errors = [];
		 $ids2   = explode(",",$ids);
		 $ids2   = array_unique($ids2);
		 foreach($ids2 as $key => $account_id){
		     if($account_id >0){
		     $stat   = true;
		     $name   = $this->BillbybillModel->get_bbb_name($account_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					   if ($this->BillbybillModel->check_bbb_exists($account_id,$name)){
						 $stat = false;
						 array_push($errors, 'Failed! Bill By Bill "'.$name.'" already exists');
					    }
					  }
					if($status==0){					  
					 if ($this->BillbybillModel->check_bbb_txn_exists($account_id)){
						 $stat = false;
						 array_push($errors, 'Failed! Bill By Bill "'.$name.'" has one or more associated vouchers');
					 } 
					
					 } 					 
			     if($stat)
			       {	   
		           $this->BillbybillModel->changestatus_single_accounts($account_id,$status);					
			       }
		        }
			 }			 
		 }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	}	
  
   
}

