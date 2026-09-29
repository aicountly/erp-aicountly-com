<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ReportingModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\ReportParams;

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
   
   /*
     * Balance Sheet / Trial Balance / Profit & Loss.
     *
     * The request is normalised by ReportParams and the rows come from the model's load_*_view() dispatchers -
     * the very same two calls the Excel/CSV exporter (Export.php) makes, so the file can only ever contain
     * what this page shows. `export_qs` is the normalised query string the page's Excel/CSV buttons send back.
     */
   public function balance_sheet() 
    { 
     $p = ReportParams::balanceSheet($_GET);
     $balance_sheet = $this->ReportingModel->load_balance_sheet_view(
         $p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);

     $data     = [
            'base_url'      => $this->base_url,
            'data'          => $balance_sheet,
            'view'          => $p['view'],
            'format'        => $p['format'],
            'nil_type'      => $p['nil_type'],
            'bo_id'         => $this->bo_id,
            'from_date'     => $p['from_date'],
            'to_date'       => $p['to_date'],
            'consolidated'  => $p['consolidated'],
            'export_qs'     => ReportParams::toQuery($p),
            'recon_notes'   => $this->ReportingModel->reconciliationNotes($p['from_date'], $p['to_date'], $p['consolidated'], 'The Balance Sheet'),
        ];
     return view($this->folder_path . ($p['format'] === 2 ? 'reports/balance_sheet_vertical' : 'reports/balance_sheet_horizontal'), $data);
    }
	
   public function trial_balance(){  	
        $p = ReportParams::trialBalance($_GET);
        $response = $this->ReportingModel->load_trial_balance_view(
            $p['view'], $p['from_date'], $p['to_date'], $p['consolidated'], $p['nil_type']);

        $data['from_date']       = $p['from_date'];
        $data['to_date']         = $p['to_date']; 
		$data['nil_type']        = $p['nil_type'];
        $data['view']            = $p['view'];
		$data['bo_id']           = $this->bo_id;
		$data['base_url']        = $this->base_url;
		$data['response']        = $response; 	
		$data['consolidated']    = $p['consolidated']; 
		$data['export_qs']       = ReportParams::toQuery($p);
		$data['recon_notes']     = $this->ReportingModel->reconciliationNotes($p['from_date'], $p['to_date'], $p['consolidated'], 'The Trial Balance');
        return view($this->folder_path.'reports/trial_balance',$data);
    }
	
   public function profit_loss(){
        $p = ReportParams::profitLoss($_GET);
        $response = $this->ReportingModel->load_profit_loss_view(
            $p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);
        $data = [
            'base_url'      => $this->base_url,
            'data'          => $response,
            'view'          => $p['view'],
            'format'        => $p['format'],
            'nil_type'      => $p['nil_type'],
			'from_date'     => $p['from_date'],
			'to_date'       => $p['to_date'],
			'bo_id'         => $this->bo_id,
			'consolidated'  => $p['consolidated'],
			'export_qs'     => ReportParams::toQuery($p),
			];
        
         return view($this->folder_path . ($p['format'] === 2 ? 'reports/profit_loss_vertical' : 'reports/profit_loss_horizontal'), $data);
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