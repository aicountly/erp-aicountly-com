<?php
namespace App\Controllers\Admin;
use App\Models\Admin\VoucherSeriesModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\externaldb; 

class Voucher_series extends BaseController
{
	protected $VoucherSeriesModel;
	protected $externaldb;
	protected $auth_session;
	protected $base_url;
	protected $folder_path;
	protected $session;
	protected $comp_code;
	protected $company_id;
	protected $univerpaic_db;
	
  	function __construct()
    {  
	    helper(['form', 'url','text','custom_hepler']);
		$this->VoucherSeriesModel = new VoucherSeriesModel();
		$this->externaldb    = new externaldb();	
		$this->auth_session  = new auth_session();					
	    $this->auth_session->user_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
	    $this->auth_session->is_company_opened();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');	
		$this->univerpaic_db =  $this->externaldb->univerpaic_db();
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
    public function AutoSeriesExists(){
	 if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
	  $voucher_type_id = $this->request->getVar('voucher_type_id');
	  $series_format   = $this->request->getVar('series_format');
	  $series_id       = $this->request->getVar('series_id');
	
	  $response = $this->VoucherSeriesModel->AutoSeriesExists($voucher_type_id,$series_format,$series_id);
	  echo json_encode($response);
	 }
	 
   }
    public function add()
    {
    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
		   // echo "<pre>";print_r($_POST);exit;
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
			];
			
	        if(!$this->validate($rules)){
	        	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }else{
			$post = $this->request->getPost();

	        if($this->VoucherSeriesModel->check_series_name_exists($post['comp_vch_series'],$post['voucher_type_id'])){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Series Name already exists']]);
	        }
			else{
			    $prefix_seperator	 = $post['prefix_seperator'];
				$suffix_seperator	 = $post['suffix_seperator'];
				$comp_vch_start   	 = $post['comp_vch_start'];
				$comp_vch_no_length  = $post['comp_vch_no_length'];
				$comp_vch_no_padding = $post['comp_vch_no_padding'];
				if(isset($post['embed_type']))
				$embed_type          = $post['embed_type'];
			    else
				$embed_type          ='';
			
				$embed_format        = $post['embed_format'];	
				
			if(isset($post['allowblank_no']))
				$allowblank_no = 1;
			 else
				$allowblank_no = 0; 		
				
			$comp_bank_id = $post['comp_bank_id'] ?? 0;   
            $bank_id = ($comp_bank_id > 0) ? (int)$comp_bank_id : 0;			
	        $data = [
	        	'cmp_id'            => (int)$this->company_id,
	        	'vch_series_name'   => $post['comp_vch_series'],
	        	'vch_type_id'       => (int)$post['voucher_type_id'],	        	
	        	'vch_series_method' => (int)$post['comp_vch_method'],
				'bank_id'           =>  $bank_id,
	           ];
	        $comp_vch_series_id = $this->VoucherSeriesModel->add_comp_vch_series($data);
			if($post['comp_vch_method']=='0'){
				// Mannual 
			 $manual_data = array("cmp_id"=>$this->company_id,"vch_series_blank"=>$allowblank_no,
			                     "vch_series_id"=>$comp_vch_series_id);
			 $this->VoucherSeriesModel->add_vchseriesm($manual_data);	
			}
			  if($post['comp_vch_method']=='1'){
				  // Automatic
				$comp_vch_renum_freq = $post['comp_vch_renum_freq'];
				if($comp_vch_renum_freq >0){
					if($embed_format!='' && $embed_type=='P'){
						$comp_vch_prefix = 	$embed_format;
					}
					if($embed_format!='' && $embed_type=='S'){
						$comp_vch_suffix = $embed_format;
					}
				}else{
				$comp_vch_prefix	 = $post['comp_vch_prefix'];
				$comp_vch_suffix	 = $post['comp_vch_suffix'];	
				}
            
				$comp_vch_prefixval='';
				$comp_vch_suffixval='';
				if($comp_vch_renum_freq >0){
					if($embed_format!='' && $embed_type=='P'){
						$comp_vch_prefixval = 	$embed_format.$prefix_seperator;
						$comp_vch_suffixval = 	$suffix_seperator.$post['comp_vch_suffix'];
					}
					if($embed_format!='' && $embed_type=='S'){
						$comp_vch_suffixval = 	$suffix_seperator.$embed_format;
						 $comp_vch_prefixval = 	$post['comp_vch_prefix'].$prefix_seperator;
					}
					
				}
				if(isset($post['comp_vch_suffix']) && $post['comp_vch_suffix']!=''){
				  $comp_vch_suffixval = 	$suffix_seperator.$post['comp_vch_suffix'];
				}				
				if(isset($post['comp_vch_prefix']) && $post['comp_vch_prefix']!=''){
				  $comp_vch_prefixval = 	$post['comp_vch_prefix'].$prefix_seperator;
				}
				
				$auto_data = array("cmp_id"=>$this->company_id,"vch_series_id"=>$comp_vch_series_id,
				                   "vch_series_renum"=>$comp_vch_renum_freq,"vch_series_prefix"=>$comp_vch_prefixval,
								   "vch_series_suffix"=>$comp_vch_suffixval,"vch_series_start"=>$comp_vch_start,
								   "vch_series_length"=>$comp_vch_no_length,"vch_series_padding"=>$comp_vch_no_padding			                     );
			   
				$this->VoucherSeriesModel->add_vchseriesa($auto_data);  
			  }
	          return json_encode(['status' => true, 'message' => 'Data Inserted']);	
			}	
	 	}
	        
	    }
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['voucher_types'] 	 = $this->VoucherSeriesModel->get_voucher_types();
        $data['banks_dropdown']  = $this->VoucherSeriesModel->banks_dropdown();
		return view($this->folder_path.'voucher_series/add', $data);		
    }

    public function edit($comp_vch_series_id)
    {
    	$series	= $this->VoucherSeriesModel->get_series_data($comp_vch_series_id);
    	
		if(empty($series)){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){	
			
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

	        if($this->VoucherSeriesModel->check_series_name_exists(trim($post['comp_vch_series']),$post['voucher_type_id'], $comp_vch_series_id)){
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Series Name already exists']]);
	        }
			else{
			    $prefix_seperator	 = $post['prefix_seperator'];
				$suffix_seperator	 = $post['suffix_seperator'];
				$comp_vch_start   	 = $post['comp_vch_start'];
				$comp_vch_no_length  = $post['comp_vch_no_length'];
				$comp_vch_no_padding = $post['comp_vch_no_padding'];
				if(isset($post['embed_type']))
				$embed_type          = $post['embed_type'];
			    else
				$embed_type          ='';
			
				$embed_format        = $post['embed_format'];	
				
			if(isset($post['allowblank_no']))
				$allowblank_no = 1;
			 else
				$allowblank_no = 0; 
			 if(isset($post['comp_bank_id']))
				$comp_bank_id =$post['comp_bank_id'];
             else
             	$comp_bank_id ='0';			 
				
			$data = [
	        	'vch_series_name' => $post['comp_vch_series'],
	        	'vch_type_id' => $post['voucher_type_id'],	        	
	        	'vch_series_method' => $post['comp_vch_method'],
				'bank_id'    =>(int)$comp_bank_id,
	            ];
	         $this->VoucherSeriesModel->update_comp_vch_series($comp_vch_series_id, $data);		
			if($post['comp_vch_method']=='0'){
			   $this->VoucherSeriesModel->remove_manual_series($comp_vch_series_id);		
				// Mannual 
			   $manual_data = array("cmp_id"=>$this->company_id,"vch_series_blank"=>$allowblank_no,
			                     "vch_series_id"=>$comp_vch_series_id);
			   $this->VoucherSeriesModel->add_vchseriesm($manual_data);	
			}
			if($post['comp_vch_method']=='1'){
				  $this->VoucherSeriesModel->remove_auto_series($comp_vch_series_id);
				  // Automatic
				$comp_vch_renum_freq = $post['comp_vch_renum_freq'];
				if($comp_vch_renum_freq >0){
					if($embed_format!='' && $embed_type=='P'){
						$comp_vch_prefix = 	$embed_format;
					}
					if($embed_format!='' && $embed_type=='S'){
						$comp_vch_suffix = $embed_format;
					}
				}else{
				$comp_vch_prefix	 = $post['comp_vch_prefix'];
				$comp_vch_suffix	 = $post['comp_vch_suffix'];	
				}
            
				$comp_vch_suffixval='';
				 $comp_vch_prefixval ='';
				if($comp_vch_renum_freq >0){
					if($embed_format!='' && $embed_type=='P'){
						$comp_vch_prefixval = 	$embed_format.$prefix_seperator;
						$comp_vch_suffixval = 	$suffix_seperator.$post['comp_vch_suffix'];
					}
					if($embed_format!='' && $embed_type=='S'){
						$comp_vch_suffixval = 	$suffix_seperator.$embed_format;
						 $comp_vch_prefixval = 	$post['comp_vch_prefix'].$prefix_seperator;
					}
					
				}
				if(isset($post['comp_vch_suffix']) && $post['comp_vch_suffix']!=''){
				  $comp_vch_suffixval = 	$suffix_seperator.$post['comp_vch_suffix'];
				}				
				if(isset($post['comp_vch_prefix']) && $post['comp_vch_prefix']!=''){
				  $comp_vch_prefixval = 	$post['comp_vch_prefix'].$prefix_seperator;
				}				
				$auto_data = array("cmp_id"=>$this->company_id,"vch_series_id"=>$comp_vch_series_id,
				                   "vch_series_renum"=>$comp_vch_renum_freq,"vch_series_prefix"=>$comp_vch_prefixval,
								   "vch_series_suffix"=>$comp_vch_suffixval,"vch_series_start"=>$comp_vch_start,
								   "vch_series_length"=>$comp_vch_no_length,"vch_series_padding"=>$comp_vch_no_padding
								   );			   
				$this->VoucherSeriesModel->add_vchseriesa($auto_data);  
			  }
		       return json_encode(['status' => true, 'message' => 'Data Updated']);	
				
			}
	    }
		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['session']            = $this->session;
		$data['voucher_types'] 	    = $this->VoucherSeriesModel->get_voucher_types();
		$data['series_info'] 	    = $series;
		$data['comp_vch_series_id'] = $comp_vch_series_id;
		$data['banks_dropdown']     = $this->VoucherSeriesModel->banks_dropdown();	
		return view($this->folder_path.'voucher_series/edit', $data);		
    }

    public function delete($comp_vch_series_id)
    {
    	$series	= $this->VoucherSeriesModel->get_series_data($comp_vch_series_id);
    	if(empty($series['series_info'])){
    		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	}

    	if($this->VoucherSeriesModel->check_series_vouchers($comp_vch_series_id)){
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['This series has one or more linked vouchers']]);
        }

    	if($this->VoucherSeriesModel->check_series_count($series['series_info']['vch_type_id'])){
        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Atleast one series is required']]);
        }

        $this->VoucherSeriesModel->delete_comp_vch_series($comp_vch_series_id);

        return json_encode(['status' => true, 'message' => 'Data Deleted', 'reload' => 1]);
    }

}