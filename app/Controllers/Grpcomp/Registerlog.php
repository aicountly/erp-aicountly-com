<?php
namespace App\Controllers\Admin;
use App\Models\Admin\RegistersModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
class Registerlog extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text']);
		$this->RegistersModel  = new RegistersModel();
	    $this->CommonModel     = new CommonModel();		
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->is_company_opened();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->forge         = \Config\Database::forge();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->enc_string    = new enc_string();
    }
 
    public function ajax_physicalverification()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
		echo $response =  $this->RegistersModel->ajax_physicalverification_register_list();	
	 }  
	 
   public function physicalverification(){
        $voucher_type_id ='10';
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
			
			$data['voucher_trans']      = array();
            } 
          else{
           $data['voucher_trans']      = array();
           $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
           }    
                
        }
       else{
       $data['voucher_trans']  = array();
       $from_date = $to_date ='';
       }
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		
		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
        return view($this->folder_path.'registers/physical_verification',$data);  
   }
   
   
   public function ajax_sale_register()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
		 echo $response =  $this->RegistersModel->ajax_sale_register_list();	
		
	 }  
	 
  public function sale_register(){
	    $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);	 
	 
        $voucher_type_id ='18';
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
        	$from_date = date('01-m-Y');
            $to_date = date('d-m-Y');
       
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
			$data['voucher_trans']      = array();
            } 
        }
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		
		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown'] = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'registers/sale_register',$data);  
   }
      public function ajax_sale_return_register()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
		 echo $response =  $this->RegistersModel->ajax_sale_return_register_list();	
		
	 }  
	 
  public function sale_return_register(){
	  $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
        $voucher_type_id ='2'; //change it
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
        $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
       
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
			$data['voucher_trans']      = array();
            } 
        }
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		
		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'registers/sale_return_register',$data);  
   }

   	public function ajax_quotation_register()
	{
		
		
		$pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];

        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
        $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';


        
		$response =  $this->RegistersModel->load_quotation_register($pq_curPage, $limit, $from_date, $to_date);
		return json_encode($response);	
	}  
	 
	public function quotation_register(){
		$selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		$voucher_type_id ='17'; //change it
		$from_date = date('01-m-Y');
           $to_date = date('d-m-Y');

		if(!empty($_GET['fromdate']) && !empty($_GET['todate']))
		{
			$from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
		} 

		$data['voucher_trans']      = array();
		$data['get_voucher_detail'] = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;

		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
		return view($this->folder_path.'registers/quotation_register',$data);  
	}
	public function ajax_purchase_requisition_register()
	{
		$pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
        
        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
        $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
        
		$response =  $this->RegistersModel->load_purchase_requisition_register($pq_curPage, $limit, $from_date, $to_date);
		return json_encode($response);		
	}  
	 
	public function purchase_requisition_register(){
		$selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		$voucher_type_id ='21'; //change it
		$from_date = date('01-m-Y');
           $to_date = date('d-m-Y');

		if(!empty($_GET['fromdate']) && !empty($_GET['todate']))
		{
			$from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
		} 

		$data['voucher_trans']      = array();
		$data['get_voucher_detail'] = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;

		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
		return view($this->folder_path.'registers/purchase_requisition_register',$data);  
	}

	public function ajax_sales_order_register()
	{
		$pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
       

        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
        $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';


        
		$response =  $this->RegistersModel->load_sales_order_register($pq_curPage, $limit, $from_date, $to_date);
		return json_encode($response);	
	}  
	 
	public function sales_order_register(){
		
		$selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		$voucher_type_id ='19'; //change it
		$from_date = date('01-m-Y');
           $to_date = date('d-m-Y');

		if(!empty($_GET['fromdate']) && !empty($_GET['todate']))
		{
			$from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
		} 

		$data['voucher_trans']      = array();
		$data['get_voucher_detail'] = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;

		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
		return view($this->folder_path.'registers/sales_order_register',$data);  
	}

	public function ajax_purchase_order_register()
	{
		$pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];

        $from_date = $this->request->getVar('from_date');
        $to_date = $this->request->getVar('to_date');
        
        $from_date     = $from_date != '' ? date('Y-m-d',strtotime($from_date)) : '';
        $to_date       = $to_date != '' ? date('Y-m-d',strtotime($to_date)) : '';
        
		$response =  $this->RegistersModel->load_purchase_order_register($pq_curPage, $limit, $from_date, $to_date);
		return json_encode($response);		
	}  
	 
	public function purchase_order_register(){
		
		$selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
		$voucher_type_id ='12'; //change it
		$from_date = date('01-m-Y');
           $to_date = date('d-m-Y');

		if(!empty($_GET['fromdate']) && !empty($_GET['todate']))
		{
			$from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
		} 

		$data['voucher_trans']      = array();
		$data['get_voucher_detail'] = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;

		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
		return view($this->folder_path.'registers/purchase_order_register',$data);  
	}
   
   
    public function ajax_purchase_due_register()
	 {
	   $_POST['from_date'] =  date('Y-m-d',strtotime($_POST['from_date']));
	   $_POST['to_date']   =  date('Y-m-d',strtotime($_POST['to_date']));
	   echo $response      =  $this->RegistersModel->ajax_purchase_due_register_list();
	 } 
    
   public function ajax_purchase_register()
	 {
	   $_POST['from_date'] =  date('Y-m-d',strtotime($_POST['from_date']));
	   $_POST['to_date']   =  date('Y-m-d',strtotime($_POST['to_date']));
	   echo $response      =  $this->RegistersModel->ajax_purchase_register_list();
	 } 

    public function purchase_due_register(){
		
		$selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
        $voucher_type_id ='18';
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
        $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
			$data['voucher_trans'] = array();
            }            
        }       
       
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		
		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'registers/purchase_due_register',$data);  
   }
   
   
   public function purchase_register(){
	   
	   $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
      $voucher_type_id ='18';
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
       $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
			$data['voucher_trans']      = array();
            } 
           
        }
       
       
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		
		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'registers/purchase_register',$data);  
   }
   
     public function ajax_purchase_return_register()
	 {
	   $_POST['from_date'] =  date('Y-m-d',strtotime($_POST['from_date']));
	   $_POST['to_date']   =  date('Y-m-d',strtotime($_POST['to_date']));
	   echo $response      =  $this->RegistersModel->ajax_purchase_return_register_list();
	 } 
	 
   public function purchase_return_register(){
	   $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
      $voucher_type_id ='3'; //change 
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
       $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];    
			$data['voucher_trans']      = array();
            } 
           
        }
       
       
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		
		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();
		
        return view($this->folder_path.'registers/purchase_return_register',$data);  
   }

    public function journal_register(){
        $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
     	$voucher_type_id ='5';

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
		$to_date   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');    
		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

	    return view($this->folder_path.'registers/journal_register',$data);  
    }

    public function consignment_packing_register(){
       
	    $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
     	$voucher_type_id ='4';

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
		$to_date   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');    
		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		$data['ses_boid']             = $this->session->get('ses_boid');
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();


	    return view($this->folder_path.'registers/consignment_packing_register',$data);  
    }

    public function ajax_consignment_packing_register()
	{
		$voucher_type_id ='4';

	  	$pq_curPage = (int)$this->request->getVar("pq_curpage");
        $limit      = (int)$this->request->getVar("pq_rpp");
        
        $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));

		$response = $this->RegistersModel->load_consignment_packing_register($pq_curPage, $limit, $from_date, $to_date);

		return json_encode($response);
	}
    
    
     public function ajax_stock_journal_register()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
	  	 echo $response =  $this->RegistersModel->ajax_stockjournal_register_list();	
		
	 }
    
     public function others(){
          $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
         $data['base_url']           = $this->base_url;
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
       if($this->request->getMethod() == 'get'){	 
           
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!='' && $_GET['register_type']!=''){
            $from_date     = $_GET['fromdate'];
			$to_date       = $_GET['todate'];  
			$register_type = $_GET['register_type'];
			
           	$data['from_date']           = $from_date;
	     	$data['to_date']             = $to_date;
	     	$data["voucher_trans"]       = array();
			$data['ses_boid']             = $this->session->get('ses_boid');
		    $data['bo_dropdown']         = $this->CommonModel->bo_dropdown();


	     	
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

    public function other_register(){
         
		   $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
       	if($this->request->getMethod() == 'post'){	
        
            $register_type          = $this->request->getVar('register_type');
            $from_date     = $this->request->getVar('from_date');
            $to_date       = $this->request->getVar('to_date');

            if($register_type == 'Memorandum Register'){
              return redirect()->to($this->base_url.'registerlog/memorandum_register?from_date='.$from_date.'&to_date='.$to_date);
            }
            if($register_type == 'Optional Register'){
              return redirect()->to($this->base_url.'registerlog/optional_register?from_date='.$from_date.'&to_date='.$to_date);
            }

        }
       	$data['base_url']           = $this->base_url;	
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
        $data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

      
        return view($this->folder_path.'registers/other_acc_register',$data);  
    }

    public function memorandum_register(){
           $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
        $voucher_type_id ='13';

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
		$to_date   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');    
		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
        $data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

	    return view($this->folder_path.'registers/memorandum_register',$data);  
       }

    


    public function ajax_memorandum_register()
	{  $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		$voucher_type_id = 8;

	  	$pq_curPage = (int)$this->request->getVar("pq_curpage");
        $limit      = (int)$this->request->getVar("pq_rpp");
        
        $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));

		$response = $this->RegistersModel->load_memorandum_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id);

		return json_encode($response);
	}

	public function optional_register(){
           
        $voucher_type_id ='0';

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
		$to_date   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');    
		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
		 $data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

	    return view($this->folder_path.'registers/optional_register',$data);  
    }

    public function ajax_optional_register()
	{ 
	  	$pq_curPage = (int)$this->request->getVar("pq_curpage");
        $limit      = (int)$this->request->getVar("pq_rpp");
        
        $from_date     	 = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date       	 = date('Y-m-d',strtotime($this->request->getVar('to_date')));
	    $voucher_type_id = $this->request->getVar('voucher_type_id');

		$response = $this->RegistersModel->load_optional_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id);

		return json_encode($response);
	}
 
    public function receipt_register(){
           $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
        $voucher_type_id = 13;

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
		$to_date   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');    
		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
 $data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

	    return view($this->folder_path.'registers/receipt_register',$data);  
       } 
    public function ajax_voucher_register()
	{
		$voucher_type_id = $this->request->getVar("voucher_type_id");

	  	$pq_curPage = (int)$this->request->getVar("pq_curpage");
        $limit      = (int)$this->request->getVar("pq_rpp");
        
        $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));

		$response = $this->RegistersModel->load_voucher_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id);

		return json_encode($response);
	}

    public function payment_register(){
          $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
        $voucher_type_id ='9';

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
		$to_date   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');     
		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
$data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

	    return view($this->folder_path.'registers/payment_register',$data);  
   }     
       
    public function contra_register(){
         $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
        $voucher_type_id ='1';

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : date('01-m-Y');
		$to_date   = !empty($_GET['to_date']) ? $_GET['to_date'] : date('d-m-Y');    
		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
	   	$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
	   	$data['from_date']          = $from_date;
		$data['to_date']            = $to_date;
$data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

	    return view($this->folder_path.'registers/contra_register',$data);  
   }
   
      
   public function ajax_material_issue_register()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
		echo $response =  $this->RegistersModel->ajax_material_issue_register_list();	
	 }  
	 
  public function material_issue_register(){
	   $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
        $voucher_type_id ='7';
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
        $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
             
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
                $from_date     = $_GET['fromdate'];
    			$to_date       = $_GET['todate'];    
    			$data['voucher_trans']      = array();
            }     
                
        }

        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
        return view($this->folder_path.'registers/material_issue_register',$data);  
   }

 public function ajax_inward_challan_due_register()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
		echo $response =  $this->RegistersModel->ajax_inward_challan_due_list();	
		
	 } 
	 
   public function ajax_material_receipt_register()
	 {
	     $_POST['from_date']     = date('Y-m-d',strtotime($_POST['from_date']));
		 $_POST['to_date']       = date('Y-m-d',strtotime($_POST['to_date']));
		echo $response =  $this->RegistersModel->ajax_material_receipt_register_list();	
		
	 }  
	 
  public function material_receipt_register(){
	    $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
        $voucher_type_id ='6';
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
        $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
             
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
                $from_date     = $_GET['fromdate'];
    			$to_date       = $_GET['todate'];    
    			$data['voucher_trans']      = array();
            }     
                
        }
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
        return view($this->folder_path.'registers/material_receipt_register',$data);  
   }
   
   public function inward_challan_due_register(){
	   $selboid = isset($_GET['bo_id']) ? $_GET['bo_id'] : $this->session->get('ses_boid');
        $this->session->set('ses_boid',$selboid);
		
		
        $voucher_type_id ='6';
        $get_voucher_detail         = $this->RegistersModel->get_voucher_info($voucher_type_id,$this->company_id);
        $data['voucher_trans']      = array();
        $from_date = date('01-m-Y');
           $to_date = date('d-m-Y');
             
       if($this->request->getMethod() == 'get'){	
            if(isset($_GET['fromdate']) && $_GET['fromdate']!='' && $_GET['todate']!=''){
                $from_date     = $_GET['fromdate'];
    			$to_date       = $_GET['todate'];    
    			$data['voucher_trans']      = array();
            }     
                
        }
        $data['get_voucher_detail'] = $get_voucher_detail;		
		$data['base_url']           = $this->base_url;
		$data['voucher_type_id']    = $voucher_type_id;		
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['ses_boid']             = $this->session->get('ses_boid'); 
		$data['bo_dropdown']         = $this->CommonModel->bo_dropdown();

		$data['from_date']           = $from_date;
		$data['to_date']             = $to_date;
        return view($this->folder_path.'registers/inward_challan_due_register',$data);  
   }  
   
   
}