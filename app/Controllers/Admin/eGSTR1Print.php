<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use App\Models\Admin\GstrReportModel;

class eGSTR1Print extends BaseController
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
    protected $table_lists;
    protected $table_lists_detailed;

    public function __construct()
    {
        helper(['form', 'url', 'text', 'custom_hepler']);
        $this->session        = session();
        $this->VouchersModel  = new VouchersModel();
        $this->AccountsModel  = new AccountsModel();
        $this->CommonModel    = new CommonModel();
        $this->auth_session   = new auth_session();
        $this->GstrReportModel= new GstrReportModel();
        $this->auth_session->user_restrict();
        $this->auth_session->is_company_opened();
        $this->base_url    = base_url() . getenv('AdminPath');
        $this->folder_path = getenv('AdminPath');
        $this->company_id  = $this->session->get('ses_company_id');
        $this->bo_id       = $this->session->get('ses_boid');
        $this->fy_id       = $this->session->get('ses_comp_fy_id');
        $this->profileid   = $this->session->get('ses_cmp_prf_id');

        $this->table_lists = [
            "4A"=>"B2B REGULAR","4B"=>"B2B REVERSE CHARGE","5"=>"B2CL (LARGE)",
            "6A"=>"EXPORTS","6B"=>"SUPPLIES MADE TO SEZ UNIT","6C"=>"DEEMED EXPORT (DE)",
            "7"=>"B2CS","8"=>"NIL RATED, EXEMPTED AND NON GST","9A,9C"=>"AMENDED INVOICES",
            "9B"=>"CREDIT NOTES","11A(1),11A(2)"=>"TAX LIABILITY (ADVANCES RECEIVED)",
            "11B(1),11B(2)"=>"ADJUSTMENTS OF ADVANCES","12"=>"HSN WISE SUMMARY OF OUTWARD SUPPLIES",
            "13"=>"DOCUMENT ISSUED","14"=>"SUPPLIES MADE THROUGH ECO","15"=>"SUPPLIES U/S 9(5)","16"=>"UNDEFINED SUPPLIES"
        ];
        $this->table_lists_detailed = [
            "4A"=>"B2B REGULAR","4B"=>"B2B REVERSE CHARGE","5"=>"B2CL (LARGE)",
            "6A"=>"EXPORTS","6A_EXPWP"=>"EXPWP","6A_EXPWOP"=>"EXPWOP",
            "6B"=>"SUPPLIES MADE TO SEZ UNIT","6B_SEZWP"=>"SEZWP","6B_SEZWOP"=>"SEZWOP",
            "6C"=>"DEEMED EXPORT (DE)","7"=>"B2CS","8"=>"NIL RATED, EXEMPTED AND NON GST",
            "8_Nil"=>"NIL RATED","8_Exempted"=>"EXEMPTED","8_Non-GST"=>"NON GST",
            "9A,9C"=>"AMENDED INVOICES","9B"=>"CREDIT NOTES REGISTERED",
            "9B_Registered"=>"REGISTERED","9B_Unregistered"=>"UNREGISTERED",
            "11A(1),11A(2)"=>"TAX LIABILITY (ADVANCES RECEIVED)","11B(1),11B(2)"=>"ADJUSTMENTS OF ADVANCES",
            "12"=>"HSN WISE SUMMARY OF OUTWARD SUPPLIES","13"=>"DOCUMENT ISSUED",
            "13_candoc"=>"CANCELLED DOCS","13_netissued"=>"NET ISSUED DOCS",
            "14"=>"SUPPLIES MADE THROUGH ECO","15"=>"SUPPLIES U/S 9(5)","16"=>"UNDEFINED SUPPLIES"
        ];
    }

    /**
     * Entry point: /admin/eGSTR1Print?from_date=YYYY-MM-DD&to_date=YYYY-MM-DD
     */
    public function index()
    {
        $from_date     = $this->request->getGet('from_date');
        $to_date       = $this->request->getGet('to_date');
        $from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd   = date('Y-m-d', strtotime($to_date));
        $table_lists   = $this->table_lists_detailed;

        $response  = $this->GstrReportModel->load_gstr1_detailed($from_date_ymd, $to_date_ymd, $table_lists);

        if (empty($response)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Voucher not found');
        }

        echo $this->print_egstr1_pdf($response, $from_date, $to_date);
    }

    /**
     * Render PDF-ready view
     */
    public function print_egstr1_pdf($response, $from_date, $to_date)
    {
		$data = [];
		$data['company_info']      = $this->CommonModel->get_company_info($this->company_id);       
		$data['company_address']   = $this->CommonModel->get_comp_ho_adrs_info($this->company_id, $this->bo_id);
        $data['get_comp_taxt_info']= $this->VouchersModel->get_branch_gstin_info($this->bo_id);		
        $data['response']          = $response;
        $data['from_date']         = $from_date;
        $data['to_date']           = $to_date;		
        return view('admin/printing_templates/egstr1', $data);
    }
}