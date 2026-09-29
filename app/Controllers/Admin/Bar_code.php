<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\BarCodeModel;
use App\Models\Admin\ERPLogModel;
use App\Models\Admin\TransactionModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;


class Bar_code extends BaseController
{
	function __construct()
	{  
		helper(['form', 'url','text']);
		$this->ItemsModel      = new ItemsModel();
		$this->BarCodeModel    = new BarCodeModel();	
		$this->LogModel        = new ERPLogModel();
		$this->auth_session    = new auth_session();
		$this->TransactionModel  = new TransactionModel();
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
	}
    
	public function create()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
	    $data['units_list']     		= $this->TransactionModel->units_dropdown();
		$data['all_items']              = $this->ItemsModel->all_items_list();			
		return view($this->folder_path.'bar_code/create',$data);		
	}
	public function create_item_barcode()
	{
		
		if($this->request->getMethod() == 'post'){	
		   
			$rules = [				
				'item_id' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please choose item name',
					   ]
					],
				'barcode_standard' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode type',
					   ]
					],
				'barcode' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode value',
					   ]
					],	
			       ];
			
	            if(!$this->validate($rules)){
		        	$errors = $this->validator->getErrors();
		        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
		        }
					
				$data = $this->request->getPost();
				$data['barcode_status'] = 1;
				
				$barcode_id = $this->BarCodeModel->add_barcode($data);
				if($barcode_id == 0){
					return json_encode(['status' => false, 'message' => 'Duplicate Entry']);
				}
			
			$this->TransactionModel->CreateFyMasterEntry($barcode_id,'brc',$data);	
			return json_encode(['status' => true, 'message' => 'Data Added']);
			
		}
		
		$data['types'] = ['EAN-13','UPC-A','ISBN','ISSN','GS1-128','QR','SSCC'];
		
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;		
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
		return view($this->folder_path.'bar_code/create_item_barcode',$data);		
	}

	public function add_barcode()
	{
		
		if($this->request->getMethod() == 'post'){

			$rules = [				
				'item_id' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please choose item name',
					   ]
					],
				'barcode_standard' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode type',
					   ]
					],
				'barcode' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode value',
					]
				],	
		    ];
		
            if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $data = $this->request->getPost();
			$data['barcode_status'] = 1;
			
			$barcode_id = $this->BarCodeModel->add_barcode($data);
			if($barcode_id == 0){
				return json_encode(['status' => false, 'message' => 'Duplicate Entry']);
			}

			return json_encode(['status' => true, 'message' => 'Data Added', 'barcode_id' => $barcode_id]);
		}
	}

	public function update_barcode()
	{
		
		if($this->request->getMethod() == 'post'){

			$rules = [				
				'barcode_id' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode id',
					   ]
					],
				'barcode_standard' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode type',
					   ]
					],
				'barcode' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode value',
					]
				],	
		    ];
		
            if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $data = $this->request->getPost();
			
			$this->BarCodeModel->update_barcode2($data);

			return json_encode(['status' => true, 'message' => 'Data Updated']);
		}
	}

	public function add_bulk_barcode()
	{
		
		if($this->request->getMethod() == 'post'){

	        $data = $this->request->getVar('data');
	        foreach ($data as $key => $value) {
	        	
	        	$value['barcode_status'] = 1;
				$this->BarCodeModel->add_barcode($value);
	        }

			return json_encode(['status' => true, 'message' => 'Data Saved']);
		}
	}

	public function update_bulk_barcode()
	{
		
		if($this->request->getMethod() == 'post'){

	        $data = $this->request->getVar('data');

	        foreach ($data as $key => $value) {
	        	
	        	if($value['barcode_id'] == 0){
	        		$value['barcode_status'] = 1;
					$this->BarCodeModel->add_barcode($value);
	        	}
	        	if($value['barcode_id'] != 0){
	        		$this->BarCodeModel->update_barcode2($value);
	        	}
	        	
	        }

			return json_encode(['status' => true, 'message' => 'Data Saved']);
		}
	}

	public function create_item_batches()
	{
		
		if($this->request->getMethod() == 'post'){

	        $data = $this->request->getVar('data');
	        foreach ($data as $key => $value) {

	        	$insert_data = [
	        		'item_id' 	=> $value['item_id'],
	        		'batch_no' 	=> $value['batch_no'],
	        		'batch_mfr' 	=> date('Y-m-d', strtotime($value['manufacturing_date'])),
	        		'batch_expiry' 	=> date('Y-m-d', strtotime($value['expiry_date'])),
	        	];
	        	
				$this->BarCodeModel->add_item_batch($insert_data);
	        }

			return json_encode(['status' => true, 'message' => 'Data Saved']);
		}
	}

	public function create_batchwise_barcode()
	{
		if($this->request->getMethod() == 'post'){	
		   	
			$data = $this->request->getPost();
			$response = $this->BarCodeModel->get_item_batches($data);
			
			return json_encode(['status' => true, 'data' => $response]);
			
		}

		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
		
		$data['types'] = ['EAN-13','UPC-A','ISBN','ISSN','GS1-128','QR','SSCC'];
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
    
		return view($this->folder_path.'bar_code/create_batchwise_barcode',$data);		
	}

  	public function create_tracking_barcode()
	{
		if($this->request->getMethod() == 'post'){	
		   	
			$data = $this->request->getPost();
			$response = $this->BarCodeModel->get_item_trackings($data);
			
			return json_encode(['status' => true, 'data' => $response]);
			
		}

		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;

	    $data['types'] = ['EAN-13','UPC-A','ISBN','ISSN','GS1-128','QR','SSCC'];
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');

		return view($this->folder_path.'bar_code/create_tracking_barcode',$data);		
	} 
  
	public function index()
	{
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']        = $this->session;

		return view($this->folder_path.'bar_code/view',$data);		
	} 


	public function ajax_item_barcodes()
	{
		$pq_curPage   = (int)$_POST["pq_curpage"];
        $limit        = (int)$_POST["pq_rpp"];
        $search       = '';
        $pq_filter    = $this->request->getVar('pq_filter');
        if(!empty($pq_filter)){
            $pq_filter = json_decode($pq_filter);
            $search    = $pq_filter->data[0]->value;
        }		
        $data_type     = $this->request->getVar('data_type');	    
        $response      = $this->BarCodeModel->load_item_barcode($pq_curPage, $limit, $search, $data_type);
        return json_encode($response);
	}

   	public function manage()
	{
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		return view($this->folder_path.'bar_code/manage',$data);		
	}

	public function manage_item_barcode($item_id)
	{
		
		if($this->request->getMethod() == 'post'){

			$rules = [				
				'item_id' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please choose item name',
					   ]
					],
				'barcode_standard' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode type',
					   ]
					],
				'barcode' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'errors' => [
						'required' => 'Please enter barcode value',
					]
				],	
		    ];
		
            if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $data = $this->request->getPost();
			
			$this->BarCodeModel->update_barcode($data);
		

			return json_encode(['status' => true, 'message' => 'Data Updated']);
		}

		$details = $this->BarCodeModel->get_item_barcode(['item_id' => $item_id]);
		if(empty($details)){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$data['item_barcode'] = $details;
		$data['types'] = ['EAN-13','UPC-A','ISBN','ISSN','GS1-128','QR','SSCC'];
		
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;		
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
		return view($this->folder_path.'bar_code/manage_itemwise',$data);
	}
	
	public function manage_itemwise()
	{
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']        = $this->session;
		$data['units_list']     		= $this->TransactionModel->units_dropdown();
		$data['party_dropdown']     	= $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  	= $this->TransactionModel->matrcntr_dropdown();
		$data['billsundry_items']  		= $this->TransactionModel->billsundry_items();		
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']          = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');

		return view($this->folder_path.'bar_code/manage_itemwise',$data);		
	}
	
	public function manage_batchwise()
	{
		if($this->request->getMethod() == 'post'){	
		   	
			$data = $this->request->getPost();
			$response = $this->BarCodeModel->get_item_batches($data);
			
			return json_encode(['status' => true, 'data' => $response]);
			
		}

		$item_id = $_GET['item_id'] ?? 0;
		$item_name = '';

		if($item_id != 0){
			$get_item_info = $this->BarCodeModel->get_item_info($item_id);
			$item_name = $get_item_info['item_name'] ?? '';
		}

		$data['item_id'] = $item_id;
		$data['item_name'] = $item_name;

		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']        = $this->session;

		$data['types'] = ['EAN-13','UPC-A','ISBN','ISSN','GS1-128','QR','SSCC'];
        $data['item_json_file'] = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');

		return view($this->folder_path.'bar_code/manage_batchwise',$data);		
	}


   	public function manage_trackingwise()
	{
		if($this->request->getMethod() == 'post'){	
		   	
			$data = $this->request->getPost();
			$response = $this->BarCodeModel->get_item_trackings($data);
			
			return json_encode(['status' => true, 'data' => $response]);
			
		}

		$item_id = $_GET['item_id'] ?? 0;
		$item_name = '';

		if($item_id != 0){
			$get_item_info = $this->BarCodeModel->get_item_info($item_id);
			$item_name = $get_item_info['item_name'] ?? '';
		}

		$data['item_id'] = $item_id;
		$data['item_name'] = $item_name;

		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']        = $this->session;

		$data['types'] = ['EAN-13','UPC-A','ISBN','ISSN','GS1-128','QR','SSCC'];
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');

		return view($this->folder_path.'bar_code/manage_trackingwise',$data);		
	}	
}