<?php
namespace App\Controllers\Admin;
use App\Models\Admin\RegistersModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\VouchersModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
class Registerlog extends BaseController{
  function __construct(){  
  
	    helper(['form', 'url','text']);
		$this->session    	 = \Config\Services::session();
		$this->RegistersModel  = new RegistersModel();
	    $this->CommonModel     = new CommonModel();
		$this->VouchersModel     = new VouchersModel();		
		$this->auth_session    = new auth_session();
		$this->auth_session->user_restrict();
	    $this->auth_session->is_company_opened();	   
		$this->base_url      = base_url().getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');		
		$this->company_id    =  $this->session->get('ses_company_id');
        $this->bo_id         = $this->session->get('ses_boid');		
    }
    public function load_voucher_details(){
	    $vouchers =  $this->request->getVar('vouchers');
		$list =  $this->RegistersModel->ajax_load_vouchers_list($vouchers);	
	    return json_encode(['status' => true, 'list' => $list]);
	 } 
 
    public function other_register(){         
	  	if($this->request->getMethod() == 'POST'){	
            $register_type = $this->request->getVar('register_type');
            $from_date     = $this->request->getVar('from_date');
            $to_date       = $this->request->getVar('to_date');
            if($register_type == 'Memorandum Register'){
              return redirect()->to($this->base_url.'registerlog/memorandum_register?from_date='.$from_date.'&to_date='.$to_date);
            }
            if($register_type == 'Optional Register'){
              return redirect()->to($this->base_url.'registerlog/optional_register?from_date='.$from_date.'&to_date='.$to_date);
            }
			if($register_type == 'System Journal Register'){
              return redirect()->to($this->base_url.'registerlog/systemjournal_register?from_date='.$from_date.'&to_date='.$to_date);
            }
        }		
       	$data['base_url']           = $this->base_url;	
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;        
        return view($this->folder_path.'registers/other_acc_register',$data);  
    }
  
    public function ajax_voucher_register(){
		$voucher_type_id = $this->request->getVar("voucher_type_id");
		$view            = $this->request->getVar("view") ?? 0;
		$type            = $this->request->getVar("type") ?? 0;		
	  	$pq_curPage      = (int)$this->request->getVar("pq_curpage");
        $limit           = (int)$this->request->getVar("pq_rpp");        
		if($pq_curPage==0)
			$pq_curPage=1;
        $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
			
	    $response = $this->RegistersModel->load_voucher_register($from_date, $to_date, $voucher_type_id,$view,$type);	
   	    echo $response;
	}
 
    public function ajax_draft_vouchers(){
		$voucher_type_id = $this->request->getVar("voucher_type_id");
		$view            = $this->request->getVar("view") ?? 0;
		$type          = $this->request->getVar("type") ?? 0;
	  	$pq_curPage      = (int)$this->request->getVar("pq_curpage");
        $limit           = (int)$this->request->getVar("pq_rpp");        
		if($pq_curPage==0)
			$pq_curPage=1;
        $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));

	     $response = $this->RegistersModel->load_draft_vouchers($from_date, $to_date, $voucher_type_id,$view);	
	     echo $response;
	}
 
    public function receipt_register(){		
        $voucher_type_id            = 13;
		$view                       = (int)($this->request->getGet('view') ?? 0);
		$type                       = (int)($this->request->getGet('type') ?? 0);		
        $from_date                  = $_GET['from_date'] ?? '';
        $to_date                    = $_GET['to_date'] ?? '';
        $from_date                  = validate_from_date($from_date);
        $to_date                    = validate_to_date($to_date);   		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['view']               = $view;
		$data['type']               = $type;
		$data['voucher_name']       = 'Receipt Register';
		if($type==1) // Draft Vouchers Layout
		 return view($this->folder_path.'registers/draft_register',$data);  
		 else 
	    return view($this->folder_path.'registers/receipt_register',$data);  
    } 
    
    public function payment_register(){		
        $voucher_type_id            = 9;
		$view                       = (int)($this->request->getGet('view') ?? 0);
		$type                       = (int)($this->request->getGet('type') ?? 0);
        $from_date                  = $_GET['from_date'] ?? '';
        $to_date                    = $_GET['to_date'] ?? '';
        $from_date                  = validate_from_date($from_date);
        $to_date                    = validate_to_date($to_date);   		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['view']               = $view;
		$data['type']               = $type;
		$data['voucher_name']       = 'Payment Register';
		if($type==1) // Draft Vouchers Layout
		 return view($this->folder_path.'registers/draft_register',$data);  
		else 
	    return view($this->folder_path.'registers/payment_register',$data);  
    }   

    public function ajax_sale_register(){
    		$voucher_type_id = $this->request->getVar("voucher_type_id");
    		$view            = $this->request->getVar("view");
    		$type            = $this->request->getVar("type");	
            $consoview       = $this->request->getVar("consoview");				
    	  	$pq_curPage      = (int)$this->request->getVar("pq_curpage");
            $limit           = (int)$this->request->getVar("pq_rpp");        
    		if($pq_curPage==0)
    			$pq_curPage=1;
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
    	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
    			
    	    $response = $this->RegistersModel->load_voucher_register($from_date, $to_date, $voucher_type_id,$view,$type,$consoview);	
       	    echo $response;
    	}
    public function ajax_sale_return_register(){
    		$voucher_type_id = $this->request->getVar("voucher_type_id");
    		$view            = $this->request->getVar("view");
    		$type            = $this->request->getVar("type");	
			$consoview       = $this->request->getVar("consoview");		
    	  	$pq_curPage      = (int)$this->request->getVar("pq_curpage");
            $limit           = (int)$this->request->getVar("pq_rpp");        
    		if($pq_curPage==0)
    			$pq_curPage=1;
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
    	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
    			
    	    $response = $this->RegistersModel->load_voucher_register($from_date, $to_date, $voucher_type_id,$view,$type,$consoview);	
       	    echo $response;
    	}
		
    public function ajax_purchase_register(){
    		$voucher_type_id = $this->request->getVar("voucher_type_id");
    		$view            = $this->request->getVar("view");
    		$type            = $this->request->getVar("type");	
			$consoview       = $this->request->getVar("consoview");
    	  	$pq_curPage      = (int)$this->request->getVar("pq_curpage");
            $limit           = (int)$this->request->getVar("pq_rpp");        
    		if($pq_curPage==0)
    			$pq_curPage=1;
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
    	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
    			
    	    $response = $this->RegistersModel->load_voucher_register($from_date, $to_date, $voucher_type_id,$view,$type,$consoview);	
       	    echo $response;
    	}
	public function ajax_purchase_return_register(){
    		$voucher_type_id = $this->request->getVar("voucher_type_id");
    		$view            = $this->request->getVar("view");
    		$type            = $this->request->getVar("type");	
 			$consoview       = $this->request->getVar("consoview");	
    	  	$pq_curPage      = (int)$this->request->getVar("pq_curpage");
            $limit           = (int)$this->request->getVar("pq_rpp");        
    		if($pq_curPage==0)
    			$pq_curPage=1;
            $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
    	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
    			
    	    $response = $this->RegistersModel->load_voucher_register($from_date, $to_date, $voucher_type_id,$view,$type,$consoview);	
       	    echo $response;
    	}	
    	
    
    public function sale_register_script()
    {   
        $this->RegistersModel->sale_register_script();
    }
	
	public function purchase_register_script()
    {   
        $this->RegistersModel->purchase_register_script();
    }
	
	public function sale_return_register_script()
    {   
        $this->RegistersModel->sale_return_register_script_all();
    }
	
	public function purchase_return_register_script()
    {   
        $this->RegistersModel->purchase_return_register_script();
    }

    public function sale_register(){		
            $voucher_type_id            = 18;
    		$view                       = (int) ($this->request->getGet('view') ?? 0);
    		$type 						= (int)($this->request->getGet('type') ?? 0);
            $from_date                  = $_GET['from_date'] ?? '';
            $to_date                    = $_GET['to_date'] ?? '';
			$consoview                  = (int)($this->request->getGet('consoview') ?? 0);
            $from_date                  = validate_from_date($from_date);
            $to_date                    = validate_to_date($to_date);   		
    		$data['base_url']           = $this->base_url;
    		$data['voucher_type_id']    = $voucher_type_id;		
    	   	$data['message_output']     = $this->message_output;
    		$data['folder_path']        = $this->folder_path;
    		$data['from_date']          = $from_date;
    		$data['to_date']            = $to_date;
    		$data['view']               = $view;
    		$data['type']               = $type;
			$data['consoview']          = $consoview;
    		$data['voucher_name']       = 'Sale Register';
    		if($type==1) // Draft Vouchers Layout
    		 return view($this->folder_path.'registers/draft_register',$data);  
    		else 
    	    return view($this->folder_path.'registers/sale_register',$data);  
        }
	 public function sale_return_register(){		
            $voucher_type_id            = 2;
    		$view                       = (int)($this->request->getGet('view') ?? 0);
    		$type 						= (int)($this->request->getGet('type') ?? 0);
            $from_date                  = $_GET['from_date'] ?? '';
            $to_date                    = $_GET['to_date'] ?? '';
			$consoview                  = (int)($this->request->getGet('consoview') ?? 0);
            $from_date                  = validate_from_date($from_date);
            $to_date                    = validate_to_date($to_date);   		
    		$data['base_url']           = $this->base_url;
    		$data['voucher_type_id']    = $voucher_type_id;		
    	   	$data['message_output']     = $this->message_output;
    		$data['folder_path']        = $this->folder_path;
    		$data['from_date']          = $from_date;
    		$data['to_date']            = $to_date;
    		$data['view']               = $view;
    		$data['type']               = $type;
			$data['consoview']          = $consoview;
    		$data['voucher_name']       = 'Sale Return Register';
    		if($type==1) // Draft Vouchers Layout
    		 return view($this->folder_path.'registers/draft_register',$data);  
    		else 
    	    return view($this->folder_path.'registers/sale_return_register',$data);  
        } 
    
    
    public function purchase_register(){		
            $voucher_type_id            = 11;
    		$view                       = (int)($this->request->getGet('view') ?? 0);
    		$type 						= (int)($this->request->getGet('type') ?? 0);
            $from_date                  = $_GET['from_date'] ?? '';
            $to_date                    = $_GET['to_date'] ?? '';
            $from_date                  = validate_from_date($from_date);
			$consoview                  = (int)($this->request->getGet('consoview') ?? 0);
            $to_date                    = validate_to_date($to_date);   		
    		$data['base_url']           = $this->base_url;
    		$data['voucher_type_id']    = $voucher_type_id;		
    	   	$data['message_output']     = $this->message_output;
    		$data['folder_path']        = $this->folder_path;
    		$data['from_date']          = $from_date;
    		$data['to_date']            = $to_date;
    		$data['view']               = $view;
    		$data['type']               = $type;
			$data['consoview']          = $consoview;
    		$data['voucher_name']       = 'Purchase Register';
    		if($type==1) // Draft Vouchers Layout
    		 return view($this->folder_path.'registers/draft_register',$data);  
    		else 
    	    return view($this->folder_path.'registers/purchase_register',$data);  
        }     
    
    public function purchase_return_register(){		
            $voucher_type_id            = 3;
    		$view                       = (int)($this->request->getGet('view') ?? 0);
    		$type 						= (int)($this->request->getGet('type') ?? 0);
            $from_date                  = $_GET['from_date'] ?? '';
            $to_date                    = $_GET['to_date'] ?? '';
			$consoview                  = (int)($this->request->getGet('consoview') ?? 0);
            $from_date                  = validate_from_date($from_date);
            $to_date                    = validate_to_date($to_date);   		
    		$data['base_url']           = $this->base_url;
    		$data['voucher_type_id']    = $voucher_type_id;		
    	   	$data['message_output']     = $this->message_output;
    		$data['folder_path']        = $this->folder_path;
    		$data['from_date']          = $from_date;
    		$data['to_date']            = $to_date;
    		$data['view']               = $view;
    		$data['type']               = $type;
			$data['consoview']          = $consoview;
    		$data['voucher_name']       = 'Purchase Return Register';
    		if($type==1) // Draft Vouchers Layout
    		 return view($this->folder_path.'registers/draft_register',$data);  
    		else 
    	    return view($this->folder_path.'registers/purchase_return_register',$data);  
        }
		
    public function contra_register(){		
        $voucher_type_id            = 1;
        $view                       = (int)($this->request->getGet('view') ?? 0);
		$type                       = (int)($this->request->getGet('type') ?? 0);
        $from_date                  = $_GET['from_date'] ?? '';
        $to_date                    = $_GET['to_date'] ?? '';
        $from_date                  = validate_from_date($from_date);
        $to_date                    = validate_to_date($to_date);   		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;	
		$data['view']    			= $view;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['type']               = $type;
		$data['voucher_name']       = 'Contra Register';
		if($type==1){ // Draft Vouchers Layout
		 return view($this->folder_path.'registers/draft_register',$data);  
		}
		 else 
	    return view($this->folder_path.'registers/contra_register',$data);  
    }
   
    public function journal_register(){
     	$voucher_type_id            = 5;
        $view                       = (int)($this->request->getGet('view') ?? 0);
		$type                       = (int)($this->request->getGet('type') ?? 0);	
        $from_date                  = $_GET['from_date'] ?? '';
        $to_date                    = $_GET['to_date'] ?? '';
        $from_date                  = validate_from_date($from_date);
        $to_date                    = validate_to_date($to_date);   		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['view']               = $view;
		$data['type']               = $type;
		$data['voucher_name']       = 'Journal Register';
		if($type==1) // Draft Vouchers Layout
		 return view($this->folder_path.'registers/draft_register',$data);  
		 else 
	    return view($this->folder_path.'registers/journal_register',$data);  
    }
	
	public function system_journal_register(){
     	$voucher_type_id            = 23;
        $view                       = (int)($this->request->getGet('view') ?? 0);
		$type                       = (int)($this->request->getGet('type') ?? 0);
        $from_date                  = $_GET['from_date'] ?? '';
        $to_date                    = $_GET['to_date'] ?? '';
        $from_date                  = validate_from_date($from_date);
        $to_date                    = validate_to_date($to_date);   		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['view']               = $view;
		$data['type']               = $type;
		$data['voucher_name']       = 'System Journal Register';
		if($type==1) // Draft Vouchers Layout
		 return view($this->folder_path.'registers/draft_register',$data);  
		 else 
	    return view($this->folder_path.'registers/journal_register',$data);  
    }
	
    public function memorandum_register(){
     	$voucher_type_id            = 8;
        $view                       = $_GET['view'] ?? 0;
        $type                       = 3;		
        $from_date                  = $_GET['from_date'] ?? '';
        $to_date                    = $_GET['to_date'] ?? '';
        $from_date                  = validate_from_date($from_date);
        $to_date                    = validate_to_date($to_date);   		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['view']               = $view;
		$data['type']               = $type;
		$data['voucher_name']       = 'Memorandum Register';		
	    return view($this->folder_path.'registers/memorandum_register',$data);  
    }
	
	public function ajax_stock_journal_register(){
		$pq_curPage      = (int)$this->request->getVar("pq_curpage");
        $limit           = (int)$this->request->getVar("pq_rpp");        
		if($pq_curPage==0)
			$pq_curPage=1;
        $from_date      = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date        = date('Y-m-d',strtotime($this->request->getVar('to_date')));
		$vch_subtype_id = 18;
		$vch_type_id    = 20;
		
		 echo $response =  $this->RegistersModel->ajax_stockjournal_register_list($from_date, $to_date,$vch_type_id,$vch_subtype_id);	
	 }

	public function optional_register(){
     	$voucher_type_id            = 0;
        $view                       = $_GET['view'] ?? 0;
        $type                       = 2;		
        $from_date                  = $_GET['from_date'] ?? '';
        $to_date                    = $_GET['to_date'] ?? '';
        $from_date                  = validate_from_date($from_date);
        $to_date                    = validate_to_date($to_date);   		
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['view']               = $view;
		$data['type']               = $type;
		$data['voucher_name']       = 'Optional Register';		
	    return view($this->folder_path.'registers/optional_register',$data);  
    }
	public function others(){
        $data['base_url']           = $this->base_url;
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$from_date                  = date('01-m-Y');
        $to_date                    = date('d-m-Y');
        if($this->request->getMethod() == 'GET'){	
         if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!='' && $_GET['register_type']!=''){
            $from_date             = $_GET['fromdate'];
			$to_date               = $_GET['todate'];  
			$register_type         = $_GET['register_type'];			
           	$data['from_date']     = $from_date;
	     	$data['to_date']       = $to_date;
	     	$data["voucher_trans"] = array();
			$data['ses_boid']      = $this->session->get('ses_boid');
		   
	        if($register_type=='pack')
	     	      return view($this->folder_path.'registers/pack_register',$data); 
	     	else if($register_type=='unpack')
	     	      return view($this->folder_path.'registers/unpack_register',$data);       
	     	else if($register_type=='stock_journal')
	     	      return view($this->folder_path.'registers/stock_journal_register',$data); 
	     	else
	     	 return view($this->folder_path.'registers/other_register',$data);
            }
       }
        return view($this->folder_path.'registers/other_register',$data);  
    }

}