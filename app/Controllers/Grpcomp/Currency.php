<?php
namespace App\Controllers\Admin;
use App\Models\Admin\CurrencyModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Currency extends BaseController
{
  	function __construct()
    {  
	    helper(['form', 'url','text','custom_hepler']);
			$this->CurrencyModel = new CurrencyModel();		
			$this->auth_session  = new auth_session();					
			$this->auth_session->user_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      =  base_url().'/'.getenv('AdminPath');
			$this->folder_path   =  getenv('AdminPath');
			$this->session    	 = \Config\Services::session();
			// $this->auth_session->is_company_opened();
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->company_id    =  $this->session->get('ses_company_id');
			$this->enc_string    =  new enc_string();
			$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
    }
    
    public function index($company_id)
    {
			$data['message_output']  = $this->message_output;
			$data['folder_path']     = $this->folder_path;
			$data['base_url']        = $this->base_url;
			$data['session']         = $this->session;
			$data['company_id']      = $company_id;

			return view($this->folder_path.'currency/view', $data);		
    }

    public function ajax_list()
    {
    	$pq_curPage = (int)$_POST["pq_curpage"];
      $limit     = (int)$_POST["pq_rpp"];

      if($pq_curPage==0){ $pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));

    	$company_id = $this->request->getVar('company_id');
    	$result = $this->CurrencyModel->get_currencies_list($pq_curPage, $limit, $offset, $company_id);

    	return json_encode($result);
    }

    public function add($company_id)
    {
    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
			
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
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $post = $this->request->getPost();

	        if($this->CurrencyModel->check_currency_name_exists($company_id, $post['curr_name'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Name already exists']]);
	        }

	        $data = [
	        	'comp_id' 				=> $company_id,
	        	'curr_name'	 			=> $post['curr_name'],
	        	'curr_symbol' 		=> $post['curr_symbol'],
	        	'curr_string' 		=> $post['curr_string'],
	        	'curr_sub_string' => $post['curr_sub_string'],
	        	'curr_initial' 	  => $post['curr_initial'],
	        	'forex_type' 			=> $post['forex_type'],
	        ];

	        $comp_currency_id = $this->CurrencyModel->add_comp_currency($company_id, $data);

	        return json_encode(['status' => true, 'message' => 'Data Inserted']);

	    }
			$data['message_output']  = $this->message_output;
			$data['folder_path']     = $this->folder_path;
			$data['base_url']        = $this->base_url;
			$data['session']         = $this->session;
			$data['currency_symbols'] = ['₹','$','€','£','¥','₣'];
			$data['company_id']      = $company_id;

			return view($this->folder_path.'currency/add', $data);		
    }

    public function edit($company_id, $comp_currency_id)
    {
    	$currency	= $this->CurrencyModel->get_currency_data($company_id,$comp_currency_id);
    	if(empty($currency)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
			
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
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }


	        $post = $this->request->getPost();

	        if($this->CurrencyModel->check_currency_name_exists($company_id, $post['curr_name'], $comp_currency_id)){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Name already exists']]);
	        }

	        $data = [
	        	'curr_name'	 			=> $post['curr_name'],
	        	'curr_symbol' 		=> $post['curr_symbol'],
	        	'curr_string' 		=> $post['curr_string'],
	        	'curr_sub_string' => $post['curr_sub_string'],
	        	'curr_initial' 	  => $post['curr_initial'],
	        	'forex_type' 			=> $post['forex_type'],
	        ];

	        $this->CurrencyModel->update_comp_currency($company_id, $data, $comp_currency_id);

	        return json_encode(['status' => true, 'message' => 'Data Updated']);
	    }
			$data['message_output']  = $this->message_output;
			$data['folder_path']     = $this->folder_path;
			$data['base_url']        = $this->base_url;
			$data['session']         = $this->session;
			$data['currency'] 		 	 = $currency;
			$data['currency_symbols'] = ['₹','$','€','£','¥','₣'];
			$data['company_id']      = $company_id;

		return view($this->folder_path.'currency/edit', $data);		
    }

    public function delete($company_id, $comp_currency_id)
    {
    	$currency	= $this->CurrencyModel->get_currency_data($company_id,$comp_currency_id);
    	if(empty($currency)){
    		return json_encode(['status' => true, 'message' => 'Data Already Deleted']);
    	}

    	// if($this->CurrencyModel->check_series_vouchers($comp_currency_id)){
      // 	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['This series has one or more linked vouchers']]);
      // }

  		if($comp_currency_id == 1){
      	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Not Allowed']]);
      }

      $this->CurrencyModel->delete_comp_currency($company_id, $comp_currency_id);

      return json_encode(['status' => true, 'message' => 'Data Deleted', 'reload' => 1]);
    }

    public function ajax_forex_rate_list()
    {
    	$pq_curPage = (int)$_POST["pq_curpage"];
      $limit     = (int)$_POST["pq_rpp"];

      if($pq_curPage==0){ $pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));

    	$company_id = $this->request->getVar('company_id');
    	$comp_currency_id = $this->request->getVar('comp_currency_id');
    	$result = $this->CurrencyModel->get_forex_rate_list($pq_curPage, $limit, $offset, $company_id, $comp_currency_id);

    	return json_encode($result);
    }

    public function manage_forex_rates($company_id,$comp_currency_id)
    {
    	if($comp_currency_id == 1){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	$currency	= $this->CurrencyModel->get_currency_data($company_id,$comp_currency_id);
    	if(empty($currency)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	$data['message_output']  = $this->message_output;
			$data['folder_path']     = $this->folder_path;
			$data['base_url']        = $this->base_url;
			$data['session']         = $this->session;
			$data['company_id']      = $company_id;
			$data['currency'] 		 	 = $currency;
			$data['rates'] 		 	 		 = ['INR', $currency['curr_initial']];

			return view($this->folder_path.'currency/forex_rates', $data);	
    }

    public function add_forex_rates()
    {
    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
			
					$rules = [				
						'curr_date' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Date is required',
						   ],
					  	],
						'rate' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Rate is required',
						  ],
					  ],
					  'conversion_rate' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Conversion Rate is required',
						  ],
					  ],

					];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $post = $this->request->getPost();

	        $curr_date = date('Y-m-d', strtotime($post['curr_date']));

	        if($this->CurrencyModel->check_forex_date_exists($post['company_id'], $curr_date, $post['comp_currency_id'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Date already exists']]);
	        }

	        if($post['rate_type'] == 'INR'){
	        	$rate_per_inr = $post['rate'];
	        	$rate_per_fcy = $post['conversion_rate'];
	        }
	        else{
	        	$rate_per_inr = $post['conversion_rate'];
	        	$rate_per_fcy = $post['rate'];
	        }

	        $data = [
	        	'comp_currency_id'	=> $post['comp_currency_id'],
	        	'rate_per_inr' 			=> $rate_per_inr,
	        	'rate_per_fcy' 			=> $rate_per_fcy,
	        	'curr_date' 				=> $curr_date,
	        	'forex_type' 				=> 'M',
	        ];
	        $this->CurrencyModel->add_forex_rates($post['company_id'], $data);

	        return json_encode(['status' => true, 'message' => 'Data Inserted']);

	    }	
	    return json_encode(['status' => false, 'message' => 'Something went wrong']);	
    }

    public function update_forex_rates()
    {
    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
			
					$rules = [				
						'curr_date' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Date is required',
						   ],
					  	],
						'rate' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Currency Rate is required',
						  ],
					  ],
					  'conversion_rate' => [
							'rules'  => 'required',
							'errors' => [
								'required' => 'Conversion Rate is required',
						  ],
					  ],

					];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $post = $this->request->getPost();

	        $curr_date = date('Y-m-d', strtotime($post['curr_date']));

	        if($this->CurrencyModel->check_forex_date_exists($post['company_id'], $curr_date, $post['comp_currency_id'], $post['forex_rate_id'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Currency Date already exists']]);
	        }

	        if($post['rate_type'] == 'INR'){
	        	$rate_per_inr = $post['rate'];
	        	$rate_per_fcy = $post['conversion_rate'];
	        }
	        else{
	        	$rate_per_inr = $post['conversion_rate'];
	        	$rate_per_fcy = $post['rate'];
	        }

	        $data = [
	        	'comp_currency_id'	=> $post['comp_currency_id'],
	        	'rate_per_inr' 			=> $rate_per_inr,
	        	'rate_per_fcy' 			=> $rate_per_fcy,
	        	'curr_date' 				=> date('Y-m-d', strtotime($post['curr_date'])),
	        	'forex_type' 				=> 'M',
	        ];
	        $this->CurrencyModel->update_forex_rates($post['company_id'], $data, $post['forex_rate_id']);

	        return json_encode(['status' => true, 'message' => 'Data Updated']);

	    }	
	    return json_encode(['status' => false, 'message' => 'Something went wrong']);	
    }

}