<?php
namespace App\Controllers\Admin;
use App\Models\Admin\VoucherSeriesModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

class Voucher_series extends BaseController
{
  	function __construct()
    {  
	    helper(['form', 'url','text','custom_hepler']);
		$this->VoucherSeriesModel = new VoucherSeriesModel();		
		$this->auth_session  = new auth_session();					
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
	    $this->auth_session->is_company_opened();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
		$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
    }
    
    public function index()
    {
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;

		$data['voucher_types'] 	 = $this->VoucherSeriesModel->get_voucher_types();

		return view($this->folder_path.'voucher_series/view', $data);		
    }

    public function ajax_series_list()
    {
    	$pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];

        if($pq_curPage==0){ $pq_curPage=1;}
		$offset = ($limit * ($pq_curPage - 1));

    	$voucher_type_id = $this->request->getVar('voucher_type_id');
    	$result = $this->VoucherSeriesModel->get_series_list($pq_curPage, $limit, $offset, $voucher_type_id);

    	return json_encode($result);
    }

    public function add()
    {
    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		   //  echo "<pre>";print_r($_POST);exit;

			
			$rules = [				
				'comp_vch_series' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Series Name is required',
				   ],
			  	],
				'voucher_type_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Voucher Type is required',
				  ],
			  	],
			  	'comp_vch_method' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Voucher Numbering is required'
				  ],
				],
			];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $post = $this->request->getPost();

	        if($this->VoucherSeriesModel->check_series_name_exists($post['comp_vch_series'],$post['voucher_type_id'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Series Name already exists']]);
	        }
	        

	        $data = [
	        	'comp_id' => $this->company_id,
	        	'comp_vch_series' => $post['comp_vch_series'],
	        	'voucher_type_id' => $post['voucher_type_id'],
	        	'comp_vch_prefix' => $post['comp_vch_prefix'],
	        	'comp_vch_suffix' => $post['comp_vch_suffix'],
	        	'comp_vch_method' => $post['comp_vch_method'],
	        ];
	        $comp_vch_series_id = $this->VoucherSeriesModel->add_comp_vch_series($data);

	        //embed_type, embed_format -left



	        return json_encode(['status' => true, 'message' => 'Data Inserted']);
	    }
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['voucher_types'] 	 = $this->VoucherSeriesModel->get_voucher_types();

		return view($this->folder_path.'voucher_series/add', $data);		
    }

    public function edit($comp_vch_series_id)
    {
    	$series	= $this->VoucherSeriesModel->get_series_data($comp_vch_series_id);
    	if(empty($series)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
			
			$rules = [				
				'comp_vch_series' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Series Name is required',
				   ],
			  	],
				'voucher_type_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Voucher Type is required',
				  ],
			  	],
			  	'comp_vch_method' => [
					'rules'  => 'required',
					'errors' => [
						'required' => 'Voucher Numbering is required'
				  ],
				],
			];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }


	        $post = $this->request->getPost();

	        if($this->VoucherSeriesModel->check_series_name_exists($post['comp_vch_series'],$post['voucher_type_id'], $comp_vch_series_id)){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Series Name already exists']]);
	        }

	        $data = [
	        	'comp_id' => $this->company_id,
	        	'comp_vch_series' => $post['comp_vch_series'],
	        	'voucher_type_id' => $post['voucher_type_id'],
	        	'comp_vch_prefix' => $post['comp_vch_prefix'],
	        	'comp_vch_suffix' => $post['comp_vch_suffix'],
	        	'comp_vch_method' => $post['comp_vch_method'],
	        ];
	        $this->VoucherSeriesModel->update_comp_vch_series($comp_vch_series_id, $data);

	        //embed_type, embed_format -left


	        return json_encode(['status' => true, 'message' => 'Data Updated']);
	    }
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['voucher_types'] 	 = $this->VoucherSeriesModel->get_voucher_types();
		$data['series'] 		 = $series;

		// echo "<pre>";print_r($series);exit;

		return view($this->folder_path.'voucher_series/edit', $data);		
    }

    public function delete($comp_vch_series_id)
    {
    	$series	= $this->VoucherSeriesModel->get_series_data($comp_vch_series_id);
    	if(empty($series)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	if($this->VoucherSeriesModel->check_series_vouchers($comp_vch_series_id)){
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['This series has one or more linked vouchers']]);
        }

    	if($this->VoucherSeriesModel->check_series_count($series['voucher_type_id'])){
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Atleast one series is required']]);
        }

        $this->VoucherSeriesModel->delete_comp_vch_series($comp_vch_series_id);

        return json_encode(['status' => true, 'message' => 'Data Deleted', 'reload' => 1]);
    }

}