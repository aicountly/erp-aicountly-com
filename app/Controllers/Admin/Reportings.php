<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ReportingModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;

class Reportings extends BaseController{

  function __construct(){  
    helper(['form', 'url','text']);
    $this->ReportingModel = new ReportingModel();	
    $this->CommonModel    = new CommonModel();		
    $this->auth_session   = new auth_session();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
    $this->base_url       = base_url().getenv('AdminPath');
    $this->folder_path    = getenv('AdminPath');
    $this->session    	  = \Config\Services::session();
    $this->company_id     = $this->session->get('ses_company_id');
    $this->fy_id          = $this->session->get('ses_comp_fy_id');
	$this->bo_id          = $this->session->get('ses_boid');
	$this->is_valid_url   = validate_web_url(current_url())['allowed'];
	if (!$this->is_valid_url) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
     }   	
   }
   
   public function balance_sheet() 
    { 
     $view          = isset($_GET['view']) ? $_GET['view'] : 1;
     $format        = isset($_GET['format']) ? $_GET['format'] : 1;
     $nil_type      = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
	 $consolidated  = isset($_GET['consolidated']) ? $_GET['consolidated'] :0;
     $from_date     = $_GET['from_date'] ?? '';
     $to_date       = $_GET['to_date'] ?? '';
     $from_date     = validate_fy_from_date($from_date);
     $to_date       = validate_fy_to_date($to_date);
     $balance_sheet = [];
	
     if($format == 1)
        $balance_sheet = $this->ReportingModel->load_balance_sheet_horizontal($view,$from_date,$to_date,$nil_type,$consolidated);
     if($format == 2)
        $balance_sheet = $this->ReportingModel->load_balance_sheet_vertical($view,$from_date,$to_date,$nil_type,$consolidated);
    
     $data     = [
            'base_url'      => $this->base_url,
            'data'          => $balance_sheet,
            'view'          => $view,
            'format'        => $format,
            'nil_type'      => $nil_type,
			'bo_id'         => $this->bo_id,
			'from_date'     => $from_date,
			'to_date'       => $to_date,
			'consolidated'  => $consolidated
        ];
     if($format == 1)
        return view($this->folder_path.'reports/balance_sheet_horizontal',$data);
    else  if($format == 2)
        return view($this->folder_path.'reports/balance_sheet_vertical',$data);
	 else 
		 return view($this->folder_path.'reports/balance_sheet_horizontal',$data); 
    }
	
   public function trial_balance(){  	
    	$nil_type  = isset($_GET['nil_type']) ? $_GET['nil_type'] : 0;    	
    	$consolidated  = isset($_GET['consolidated']) ? $_GET['consolidated'] :0;    	
        $view      = isset($_GET['view']) ? $_GET['view'] : 0;
        $from_date = $_GET['from_date'] ?? '';
        $to_date   = $_GET['to_date'] ?? '';
        $from_date = validate_fy_from_date($from_date);
        $to_date   = validate_fy_to_date($to_date);
        $response  =[];
        if($view == 0)
            $response = $this->ReportingModel->load_trial_balance_grps($from_date,$to_date,$consolidated);
        else if($view == 1)
            $response = $this->ReportingModel->load_trial_balance_accnts($from_date,$to_date,$consolidated);
        else
            $response= $this->ReportingModel->load_trial_balance_opn($from_date,$to_date,$consolidated,$nil_type);

        $data['from_date']       = $from_date;
        $data['to_date']         = $to_date; 
		$data['nil_type']        = $nil_type;
        $data['view']            = $view;
		$data['bo_id']           = $this->bo_id;
		$data['base_url']        = $this->base_url;
		$data['response']        = $response; 	
		$data['consolidated']    = $consolidated; 
        return view($this->folder_path.'reports/trial_balance',$data);
    }
	
   public function profit_loss(){
	    $view      = isset($_GET['view']) ? $_GET['view'] : 1;
        $format    = isset($_GET['format']) ? $_GET['format'] : 1;
        $nil_type  = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
        $consolidated  = isset($_GET['consolidated']) ? $_GET['consolidated'] :0;        
        $from_date = $_GET['from_date'] ?? '';
        $to_date   = $_GET['to_date'] ?? '';
        $from_date = validate_fy_from_date($from_date);
        $to_date   = validate_fy_to_date($to_date);
		$response  = [];
        if($format == 1)
             $response = $this->ReportingModel->load_profit_loss_horizontal($view,$from_date,$to_date,$nil_type,$consolidated);
        if($format == 2)
             $response = $this->ReportingModel->load_profit_loss_vertical($view,$from_date,$to_date,$nil_type,$consolidated);         
        $data = [
            'base_url'      => $this->base_url,
            'data'          => $response,
            'view'          => $view,
            'format'        => $format,
            'nil_type'      => $nil_type,
			'from_date'     => $from_date,
			'to_date'       => $to_date,
			'bo_id'         => $this->bo_id,
			'consolidated'  => $consolidated
			];
        
         if($format == 1)
            return view($this->folder_path.'reports/profit_loss_horizontal',$data);
         else if($format == 2)
            return view($this->folder_path.'reports/profit_loss_vertical',$data);
		 else 
			 return view($this->folder_path.'reports/profit_loss_horizontal',$data); 
    }
   
   public function ajax_voucher_approvals(){
		$voucher_type_id = $this->request->getVar("voucher_type_id");
		$type            = $this->request->getVar("view") ?? 4;
		$pq_curPage      = (int)$this->request->getVar("pq_curpage");
        $limit           = (int)$this->request->getVar("pq_rpp");        
		if($pq_curPage==0)
			$pq_curPage=1;
        $from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	    $to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
			
	    $response = $this->ReportingModel->load_voucher_approvals($from_date, $to_date, $voucher_type_id,$type);	
   	    echo $response;
	}
      
   public function txn_approvals(){  	
    	$voucher_type_id            = 9;
		$view                       = $_GET['view'] ?? 4;
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
        return view($this->folder_path.'reports/voucher_approvals',$data);
    }
	
	

}