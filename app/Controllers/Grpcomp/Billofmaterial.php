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
			 
           $this->BillofmaterialModel->save_data($bom_name,$item_consumed,$item_produced,$byproducts_produced,$additional_cost);
		   return redirect()->to($this->base_url.'billofmaterial');
		   die;
		  }							 
		$data['message_output']     = $this->message_output;
		$data['base_url']           = $this->base_url;	
		$data['folder_path']        = $this->folder_path;	
        $data['units_list']         = $this->BillofmaterialModel->units_dropdown($this->company_id);
		$data['expense_heads_list'] = $this->BillofmaterialModel->expense_heads_dropdown($this->company_id);
		 $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
        
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
			
           $this->BillofmaterialModel->update_bom_data($bom_name,$item_consumed,$item_produced,$byproducts_produced,$additional_cost,$billofmaterial_id);
		   return redirect()->to($this->base_url.'billofmaterial');
		   die;
		  }	

		  $billofmaterial_info = $this->BillofmaterialModel->billofmaterial_info($billofmaterial_id);
		  if(!$billofmaterial_info){
		  	throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
		  }

        $data['bom_info']            = $billofmaterial_info; 
		$data['message_output']      = $this->message_output;
		$data['base_url']            = $this->base_url;	
		$data['folder_path']         = $this->folder_path;	
		$data['billofmaterial_id']   = $billofmaterial_id;
        $data['units_list']          = $this->BillofmaterialModel->units_dropdown($this->company_id);
		$data['expense_heads_list']  = $this->BillofmaterialModel->expense_heads_dropdown($this->company_id);
		 $data['item_json_file']                  = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']                   = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
	    return view($this->folder_path.'bill_of_material/edit',$data);		
    }	
}