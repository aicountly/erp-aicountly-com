<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ItemsModel;
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
		
		
		    $item_id          = $this->request->getVar('item_id'); 
		    $barcode_type     = $this->request->getVar('barcode_type'); 
		    $barcode_value    = $this->request->getVar('barcode_value');
			
			
			$rules = [				
				'item_id' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please choose item name',
					   ]
					],
				'barcode_type' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter barcode type',
					   ]
					],
				'barcode_value' => [
					'label'  => 'Item',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter barcode value',
					   ]
					]	
			       ];
			
            if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }else{
				
				 // check 
				//$exits = $this->TransactionModel->CreateBarcode();
				
				// save barcode
				$insert_data = array("barcode"=>$barcode_value,"barcode_standard"=>$barcode_type,"master_id"=>$item_id,"master_id_type"=>"itm",
				                     "barcode_wef"=>date('Y-m-d'),'barcode_expiry'=>'','barcode_status'=>'0');
				
				//$this->TransactionModel->CreateBarcode($insert_data)
				 return json_encode(['status' => true, 'message' => 'Data Added']);
			}
		}
		
		
		
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
	    $data['units_list']     		= $this->TransactionModel->units_dropdown();
		$data['party_dropdown']     	= $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  	= $this->TransactionModel->matrcntr_dropdown();
		$data['billsundry_items']  		= $this->TransactionModel->billsundry_items();		
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']          = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
		return view($this->folder_path.'bar_code/create_item_barcode',$data);		
	}
	public function create_batchwise_barcode()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
	    $data['units_list']     		= $this->TransactionModel->units_dropdown();
		$data['party_dropdown']     	= $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  	= $this->TransactionModel->matrcntr_dropdown();
		$data['billsundry_items']  		= $this->TransactionModel->billsundry_items();		
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']          = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
		return view($this->folder_path.'bar_code/create_batchwise_barcode',$data);		
	} 
  public function create_tracking_barcode()
	{
		$data['message_output']         = $this->message_output;
		$data['folder_path']            = $this->folder_path;
		$data['base_url']               = $this->base_url;
		$data['session']                = $this->session;
	    $data['units_list']     		= $this->TransactionModel->units_dropdown();
		$data['party_dropdown']     	= $this->TransactionModel->party_dropdown();
		$data['matrcntr_dropdown']  	= $this->TransactionModel->matrcntr_dropdown();
		$data['billsundry_items']  		= $this->TransactionModel->billsundry_items();		
        $data['item_json_file']         = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');
        $data['bsd_json_file']          = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/bsd.json');
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
   public function manage()
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

		return view($this->folder_path.'bar_code/manage',$data);		
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

		return view($this->folder_path.'bar_code/manage_batchwise',$data);		
	}
   public function manage_trackingwise()
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

		return view($this->folder_path.'bar_code/manage_trackingwise',$data);		
	}	
}