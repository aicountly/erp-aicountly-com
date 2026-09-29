<?php
namespace App\Controllers\Admin;
use App\Models\Admin\TaxcategoryModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\BillsundryModel;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
class Taxcategory extends BaseController{
function __construct(){  
	    helper(['form', 'url','text','custom']);
			$this->TaxcategoryModel  = new TaxcategoryModel();
			$this->AccountsModel     = new AccountsModel();
			$this->BillsundryModel   = new BillsundryModel();
			$this->ItemsModel        = new ItemsModel();			
			$this->LogModel          = new ERPLogModel();			
			$this->CommonModel       =  new CommonModel();		
			$this->auth_session      = new auth_session();
			$this->auth_session->user_restrict();
			$this->auth_session->is_company_opened();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().'/'.getenv('AdminPath');
			$this->folder_path   = getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
		    $this->bo_id         = $this->session->get('ses_boid');
	        $this->fy_id         = $this->session->get('ses_comp_fy_id');
	        $this->company_id    = $this->session->get('ses_company_id');
			$this->sub_types     = [1=>"TAXABLE", 2=>"EXEMPT",3=>"NIL RATED",4=>"NON GST"];
    }
   
  public  function ajax_category_view()
	 {
		echo $response =  $this->TaxcategoryModel->ajax_taxcategory_list();	
		
	 } 
  public function index()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;			
		return view($this->folder_path.'tax_category/view',$data);		
    } 

   public function add()
   {	   	   
	  
		 if($this->request->getMethod() == 'POST'){			     
		   try{		       
		   // echo'<pre>';print_r($_POST);die(); 		       
			$tax_cat_name      = $this->request->getVar('tax_cat_name'); 
			$tax_cat_type      = $this->request->getVar('tax_cat_type');
			$tax_cat_section   = $this->request->getVar('tax_cat_section');
			$tax_cat_rates     = $this->request->getVar('tax_cat_rates');
			$tax_cat_wef       = $this->request->getVar('tax_cat_wef');
			$sub_type_id       = $this->request->getVar('sub_type_id');
			  
			$rules = [				
				'tax_cat_name' => [
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
						'cmp_id'            => $this->company_id,
						'tax_cat_type'      => $tax_cat_type,
						'tax_cat_name'      => ucwords(clean($tax_cat_name)),
						'tax_cat_section'   => $tax_cat_section,
						'tax_cat_rates'     => $tax_cat_rates,
						'tax_cat_wef'       => $tax_cat_wef,
						'sub_type_id'       => $sub_type_id,
						'tax_cat_is_active' => 1
						];						
			    $response = $this->TaxcategoryModel->add_category($insert_data);
			   
			    if(!$response['status']){
				return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
		    	}
			    else{
			        /*
			        $tax_cat_id = $response['tax_cat_mst_id'];				
			        $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'add',
			            'log_field_id' => $tax_cat_id,
			            'log_field_name' => $tax_cat_name,
			            'log_field_type' => 'tax_category',
			        ];
			    $this->LogModel->add_log($log);*/
				
				$accounts_list  = $this->AccountsModel->company_all_accounts();
				CreateJsonFile($this->fy_id ,$this->company_id,'acc',$accounts_list);
				
				$items_list  = $this->ItemsModel->company_all_items();
				CreateJsonFile($this->fy_id,$this->company_id,'itm',$items_list); 						   
				
				$bsd_items = $this->BillsundryModel->company_all_bsd();
				CreateJsonFile($this->fy_id,$this->company_id,'bsd',$bsd_items);
				
				return json_encode(['status' => true, 'message' => 'Data Inserted']);
				die;					 
			    }
              }
		     }
				catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				} 		 									
	     }	 
	   
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['GSTTaxCategory']        = GSTTaxCategory();
		$data['GstTypes']              = GstTypes();
		$data['base_url']              = $this->base_url;
		$data['sub_types']             = $this->sub_types;
		
	    return view($this->folder_path.'tax_category/add',$data);		
    }
	
   public function change_status(){
   	     $status_ids    = $this->request->getVar('pss_status_val'); 
		 $txcatg_id      = $this->request->getVar('accidids');
		 $errors        =[];
		 if($txcatg_id >0){
		     $stat   = true;
		     $name   = $this->TaxcategoryModel->get_account_name($txcatg_id);
		     $status = $status_ids[0];
		     if($name != ''){			
					if($status==1){
					      if ($this->TaxcategoryModel->check_billsundry_exists($txcatg_id,$name)){
						    $stat = false;
						   array_push($errors, 'Failed! Account "'.$name.'" already exists');
					     } 
				   	   }
					 }
			     if($stat) {	   
		           $this->TaxcategoryModel->changestatus_single_accounts($txcatg_id,$status);
			       }
		        }
		  if(count($errors)){
		     return json_encode(['status' => false, 'message' => 'Status can not changed', 'errors' => $errors]);
		  }
		  return json_encode(['status' => true, 'message' => 'Status changed', 'reload' => 1]);
	  }
	
	public function modify($tax_cat_id)
    {
		if(!$tax_cat_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$category_info = $this->TaxcategoryModel->category_info($tax_cat_id);
		if(!$category_info)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
	   if($this->request->getMethod() == 'POST'  && $this->request->isAjax()){
	   // try{
			$tax_cat_name      = $this->request->getVar('tax_cat_name'); 
			$tax_cat_type      = $this->request->getVar('tax_cat_type');
			$tax_cat_section   = $this->request->getVar('tax_cat_section');
			$tax_cat_rates     = $this->request->getVar('tax_cat_rates');
			$tax_cat_wef       = $this->request->getVar('tax_cat_wef');
			$sub_type_id       = $this->request->getVar('sub_type_id');
					
			$rules = [				
				'tax_cat_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]]
				  			   
			       ];
			
            if(!$this->validate($rules)){
                $errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{	
				$data   = [
						'tax_cat_type'       => $tax_cat_type,
						'tax_cat_name'       => ucwords(clean($tax_cat_name)),
						'tax_cat_section'    => $tax_cat_section,
						'tax_cat_rates'      => $tax_cat_rates,
						'tax_cat_wef'        => $tax_cat_wef,
						'sub_type_id'        => $sub_type_id
					    ];
					   
			         $response = $this->TaxcategoryModel->update_category($data,$tax_cat_id);
			        if(!$response['status']){
			         	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
		         	}
        			else{/*
        			     $tax_cat_id = $response['tax_cat_id'];					
    			         $log = [
    				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    				            'log_date' => date('Y-m-d'),
    				            'log_time' => date('H:i:s'),
    				            'log_action_tags' => 'update',
    				            'log_field_id' => $tax_cat_id,
    				            'log_field_name' => $tax_cat_name,
    				            'log_field_type' => 'tax_category',
    				        ];
    				    $this->LogModel->add_log($log);    				    
						return redirect()->to($this->base_url.'taxcategory');  
						*/
						
						$accounts_list  = $this->AccountsModel->company_all_accounts();
						CreateJsonFile($this->fy_id  ,$this->company_id,'acc',$accounts_list);
						$company_all_bsd  = $this->BillsundryModel->company_all_bsd();
						CreateJsonFile($this->fy_id,$this->company_id,'bsd',$company_all_bsd);
						$items_list  = $this->ItemsModel->company_all_items();
						CreateJsonFile($this->fy_id,$this->company_id,'itm',$items_list);
						
						return json_encode(['status' => true, 'message' => 'Data Updated']);
				        die;
				 
        			}
				 			 
		    	}	
	/* }
				catch (\Throwable $e) {
					log_message('error', 'DB Query Error: ' . $e->getMessage());
					return json_encode(['status' => false, 'message' => 'Exception at line ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage()]);
				} */ 
				   	
		}	 
		
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['category_info']         = $category_info;
        $data['base_url']              = $this->base_url;
		$data['GSTTaxCategory']        = GSTTaxCategory();
		$data['tax_cat_id']            = $tax_cat_id;
		$data['sub_types']             = $this->sub_types;	
	    return view($this->folder_path.'tax_category/edit',$data);		
    }
	
	public function remove_category($tax_cat_id){		
		if(!$tax_cat_id)
			return redirect()->to($this->base_url.'taxcategory'); 		 	
		$response = $this->TaxcategoryModel->remove_tax_category($tax_cat_id);
		echo json_encode($response);
		die();
	  }
}