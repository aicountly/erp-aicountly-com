<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\ReportingModel;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use App\Libraries\ReportParams;
class BalanceSheetPrint extends BaseController
{
    protected $session;
    protected $VouchersModel;
    protected $AccountsModel;
    protected $CommonModel;
    protected $auth_session;
    protected $base_url;
    protected $folder_path;
    protected $company_id;
    protected $bo_id;
    protected $fy_id;
    protected $profileid;

    public function __construct()
    {
        helper(['form', 'url', 'text', 'custom_hepler']);

        $this->session        = session();
        $this->VouchersModel  = new VouchersModel();
        $this->AccountsModel  = new AccountsModel();
        $this->CommonModel    = new CommonModel();
        $this->auth_session   = new auth_session();
		$this->ReportingModel = new ReportingModel();

        $this->auth_session->user_restrict();
        $this->auth_session->is_company_opened();

        $this->base_url    = base_url() . getenv('AdminPath');
        $this->folder_path = getenv('AdminPath');

        $this->company_id = $this->session->get('ses_company_id');
        $this->bo_id      = $this->session->get('ses_boid');
        $this->fy_id      = $this->session->get('ses_comp_fy_id');
        $this->profileid  = $this->session->get('ses_cmp_prf_id');
    }

    /**
     * Entry point
     * URL: /admin/balanceshhetprint?format=1&from_date=01-04-2025&to_date=31-03-2026&view=1
     */
    public function index()
    {
        // Same parameter handling as the page and the Excel export (defaults, FY clamp), so the printout
        // shows the figures the page shows. The two print templates are horizontal layouts, so the
        // horizontal rows are always used (vertical rows would print blank in them).
        $p            = ReportParams::balanceSheet($this->request->getGet());
        $consolidated = $p['consolidated'];
        $nil_type     = $p['nil_type'];
        $view         = $p['view'];
        $from_date    = $p['from_ymd'];
        $to_date      = $p['to_ymd'];
		
		$company_info       = $this->CommonModel->get_company_info($this->company_id);
        $company_address    = $this->CommonModel->get_comp_ho_adrs_info($this->company_id, $this->bo_id);
        $get_comp_taxt_info = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
        $gstin              = $get_comp_taxt_info['hobo_gstin'] ?? '';


     $data = $this->ReportingModel->load_balance_sheet_view(1, $view, $p['from_date'], $p['to_date'], $nil_type, $consolidated);
 
		
        switch ($view) {
            case 1:
                echo $this->print_balancesheet_horizontal_pdf($data,$from_date,$to_date,$company_info,$company_address,$get_comp_taxt_info,$gstin);
                break;
			 case 0:
                echo $this->print_balancesheet_horizontal_pdf($data,$from_date,$to_date,$company_info,$company_address,$get_comp_taxt_info,$gstin);
                break;
            case 2:
                echo $this->print_balancesheet_detailed_pdf($data,$from_date,$to_date,$company_info,$company_address,$get_comp_taxt_info,$gstin);
                break;
            
            default:
                echo $this->print_balancesheet_horizontal_pdf($data,$from_date,$to_date,$company_info,$company_address,$get_comp_taxt_info,$gstin);
                break;
        }
    }

    
   
    public function print_balancesheet_horizontal_pdf($response,$from_date,$to_date,$company_info,$company_address,$get_comp_taxt_info,$gstin)
    {
		$data =array();
        $data['from_date'] = $from_date;
	    $data['to_date']  = $to_date;
	    $data['data']    = $response;
		$data['company_info']    =$company_info;
		$data['company_address']    =$company_address;
		$data['get_comp_taxt_info']    =$get_comp_taxt_info;
		$data['gstin']    =$gstin;
		$data['bo_name'] = $this->session->get('ses_boname');
	   
        return view('admin/printing_templates/balance_sheet_horizontal', $data);

    }
   public function print_balancesheet_detailed_pdf($response,$from_date,$to_date,$company_info,$company_address,$get_comp_taxt_info,$gstin)
    {
		$data =array();
        $data['from_date'] = $from_date;
	    $data['to_date']  = $to_date;
	    $data['data']    = $response;
		$data['company_info']    =$company_info;
		$data['company_address']    =$company_address;
		$data['get_comp_taxt_info']    =$get_comp_taxt_info;
		$data['gstin']    =$gstin;
		$data['bo_name'] = $this->session->get('ses_boname');
		
        return view('admin/printing_templates/balance_sheet_detailed', $data);

    }

  
}
