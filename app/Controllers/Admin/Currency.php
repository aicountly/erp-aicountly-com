<?php
namespace App\Controllers\Admin;
use App\Models\Admin\CurrencyModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Currency extends BaseController
{
  	function __construct()
    {  
	    helper(['form', 'url','text','custom_hepler']);
			$this->CurrencyModel = new CurrencyModel();		
			$this->auth_session  = new auth_session();					
			$this->auth_session->user_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      =  base_url().getenv('AdminPath');
			$this->folder_path   =  getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->company_id    =  $this->session->get('ses_company_id');
			$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		    $this->bo_id         =  $this->session->get('ses_boid');
    }
    
    public function index(){
			$data['message_output']  = $this->message_output;
			$data['folder_path']     = $this->folder_path;
			$data['base_url']        = $this->base_url;
			$data['session']         = $this->session;
			$data['company_id']      = $this->company_id;
			return view($this->folder_path.'currency/view', $data);		
     }

    public function ajax_list(){
    	$company_id = $this->request->getVar('company_id');
    	$result = $this->CurrencyModel->get_currencies_list($company_id);
    	return json_encode($result);
    }

    public function add(){
    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
			
					$rules = [				
						'curr_name' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Name is required',
						   ],
					  	],
						'curr_symbol' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Symbol is required',
						  ],
					  ],

					];
			
	        if(!$this->validate($rules)){
	        	$errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $post = $this->request->getPost();
			$data = [
	        	'cmp_id' 			 => $this->company_id,
	        	'cmp_fcy_name'	 	 => $post['curr_name'],
	        	'cmp_fcy_symbol' 	 => $post['curr_symbol'],
	        	'cmp_fcy_string' 	 => $post['curr_string'],
	        	'cmp_fcy_sub_string' => $post['curr_sub_string'],
	        	'cmp_fcy_initial' 	 => $post['curr_initial'],
	        	'cmp_fcy_forex_type' => $post['forex_type'],
	        ];
	        if($this->CurrencyModel->check_currency_name_exists($data)=="-1"){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Name already exists']]);
	        }
			if($this->CurrencyModel->check_currency_name_exists($data)=="-2"){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Symbol already exists']]);
	        }
	        
	        $comp_currency_id = $this->CurrencyModel->add_comp_currency($data);
	        return json_encode(['status' => true, 'message' => 'Data Inserted']);

	      }
			$data['message_output']  = $this->message_output;
			$data['folder_path']     = $this->folder_path;
			$data['base_url']        = $this->base_url;
			$data['session']         = $this->session;
			$data['currency_symbols'] = ['₹','$','€','£','¥','₣'];
			$data['company_id']      = $this->company_id;
			return view($this->folder_path.'currency/add', $data);		
    }

    public function edit($company_id, $comp_currency_id){
    	$currency	= $this->CurrencyModel->get_currency_data($company_id,$comp_currency_id);
    	if(empty($currency)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}
    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){			
					$rules = [				
						'curr_name' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Name is required',
						   ],
					  	],
						'curr_symbol' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Symbol is required',
						  ],
					  ],

					];
			
	        if(!$this->validate($rules)){
	        	$errors_list = $this->validator->getErrors();
				$errors      = array_values($errors_list);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }
			
	        $post = $this->request->getPost();
			$data = [
			    'cmp_id'                => $this->company_id,
	        	'cmp_fcy_name'	 		=> $post['curr_name'],
	        	'cmp_fcy_symbol' 		=> $post['curr_symbol'],
	        	'cmp_fcy_string' 		=> $post['curr_string'],
	        	'cmp_fcy_sub_string'    => $post['curr_sub_string'],
	        	'cmp_fcy_initial' 	    => $post['curr_initial']	        	
	          ];
	        if($this->CurrencyModel->check_currency_name_exists($data,$comp_currency_id)=="-1"){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Name already exists']]);
	        }
			if($this->CurrencyModel->check_currency_name_exists($data,$comp_currency_id)=="-2"){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Symbol already exists']]);
	        }
			 
	        $this->CurrencyModel->update_comp_currency($data, $comp_currency_id);
	        return json_encode(['status' => true, 'message' => 'Data Updated']);
	    }
			$data['message_output']   = $this->message_output;
			$data['folder_path']      = $this->folder_path;
			$data['base_url']         = $this->base_url;
			$data['session']          = $this->session;
			$data['currency'] 		  = $currency;
			$data['currency_symbols'] = ['₹','$','€','£','¥','₣'];
			$data['company_id']       = $company_id;

		return view($this->folder_path.'currency/edit', $data);		
    }

    public function delete_currency($company_id, $comp_currency_id){
    	$currency	= $this->CurrencyModel->get_currency_data($this->company_id,$comp_currency_id);
    	if(empty($currency)){
    		return json_encode(['status' => true, 'message' => 'Data Already Deleted']);
    	}
  		if($comp_currency_id == 1){
      	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Not Allowed']]);
      }

      $this->CurrencyModel->delete_comp_currency($comp_currency_id);

      return json_encode(['status' => true, 'message' => 'Data Deleted', 'reload' => 1]);
    }

}