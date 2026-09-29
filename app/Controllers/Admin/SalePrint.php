<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use Mpdf\Mpdf;
class SalePrint extends BaseController
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
     * URL: /admin/saleprint/index/{voucher_txn_id}?p=1
     */
    public function index($voucher_txn_id)
    {
        $browser_preview = ($this->request->getVar('p') == 1);

        $voucher_info = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
		
        if (empty($voucher_info['vch_txn_id'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Voucher not found');
        }
		
        switch ($voucher_info['vch_sub_type_id']) {
            case 8:
                echo $this->print_sale_with_item_pdf($voucher_txn_id, $browser_preview);
                break;

            case 9:
                echo $this->print_sale_without_stock_pdf($voucher_txn_id, $browser_preview);
                break;

            case 10:
            case 0:
                echo $this->print_sale_without_item_pdf($voucher_txn_id, $browser_preview);
                break;

            default:
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invalid voucher subtype');
        }
    }

    /**
     * Common data collector
     */
    private function _gatherInvoiceData($voucher_txn_id)
    {
        $data = [];

        $data['voucher_info']      = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
        $data['company_info']      = $this->CommonModel->get_company_info($this->company_id);
       
		$data['company_address']   = $this->CommonModel->get_comp_ho_adrs_info($this->company_id, $this->bo_id);
        $data['get_comp_taxt_info']= $this->VouchersModel->get_branch_gstin_info($this->bo_id);

		
        $party_id                 = $data['voucher_info']['party_id'];
        $data['party_info']       = $this->VouchersModel->account_full_info($party_id);
        $data['party_address']    = $data['party_info']['address_info'] ?? [];

        $data['item_transactions']    = $this->VouchersModel->grid_item_transactions($voucher_txn_id);
        $data['sundry_transactions']  = $this->VouchersModel->grid_bsd_transactions($voucher_txn_id);
        $data['tax_summary']           = $this->VouchersModel->get_item_tax_summary($voucher_txn_id);
        $data['narration']             = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
        $data['item_type']             = 'items';

        $data['gstroutsup_info'] = $this->VouchersModel->get_gstroutsup_info($voucher_txn_id);

        if (!empty($data['gstroutsup_info']['outsup_pos'])) {
            $data['pos_state_info'] = $this->VouchersModel->pos_state_info($data['gstroutsup_info']['outsup_pos']);
        }

        $data['e_invoice']       = $this->VouchersModel->get_einvmaster_info($voucher_txn_id);
        $data['ewbmstreqn_data'] = $this->VouchersModel->get_ewbmstreqn_data($voucher_txn_id);
        $data['dispatch_from']   = $data['ewbmstreqn_data']['gstdispfrm_info'] ?? [];
        $data['ship_to']         = $data['ewbmstreqn_data']['gstshipton_info'] ?? [];

        $data['bank_details']    = $this->VouchersModel->get_bank_info(
            $data['voucher_info']['vch_series_id'],
            $data['voucher_info']['vch_type_id']
        );


        $data['transporter_info'] = $this->VouchersModel->transporter_info($voucher_txn_id);
        
        return $data;
    }

    /**
     * mPDF generator
     */
    private function _generatePdfFromHtml($html, $fileName, $browserPreview)
{
    // Clean ALL output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $mpdf = new \Mpdf\Mpdf([
        'mode'              => 'utf-8',
        'format'            => 'A4',
        'margin_left'       => 5,
        'margin_right'      => 5,
        'margin_top'        => 5,
        'margin_bottom'     => 5,
        'default_font'      => 'dejavusans',
        'default_font_size' => 8,
        'tempDir'           => WRITEPATH . 'mpdf',
    ]);

    $mpdf->WriteHTML($html);

    if ($browserPreview) {
        // INLINE PREVIEW
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.$fileName.'.pdf"');
        echo $mpdf->Output('', 'S');
        exit;
    } else {
        // DOWNLOAD
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'.pdf"');
        echo $mpdf->Output('', 'S');
        exit;
    }
}

    public function print_sale_with_item_pdf($voucher_txn_id, $browser_preview = false)
    {
        $data = $this->_gatherInvoiceData($voucher_txn_id);
        $data['invoice_title'] = 'TAX INVOICE'; 
		$data['voucher_txn_id'] = $voucher_txn_id;

       return view('admin/printing_templates/sales_invoice_with_item', $data);
/* 
        return $this->_generatePdfFromHtml(
            $html,
            'Sale-Invoice-' . $voucher_txn_id,
            $browser_preview
        ); */
    }

    public function print_sale_without_stock_pdf($voucher_txn_id, $browser_preview = false)
    {
        $data = $this->_gatherInvoiceData($voucher_txn_id);
        $data['item_transactions'] = $this->VouchersModel->get_item_transactions_oth($voucher_txn_id);
        $data['invoice_title'] = 'TAX INVOICE';
        $data['voucher_txn_id'] = $voucher_txn_id;

        $html = view('admin/printing_templates/sales_invoice_with_item', $data);

       /*  return $this->_generatePdfFromHtml(
            $html,
            'Sale-WithoutStock-Invoice-' . $voucher_txn_id,
            $browser_preview
        ); */
    }

    public function print_sale_without_item_pdf($voucher_txn_id, $browser_preview = false)
    {
        $data = $this->_gatherInvoiceData($voucher_txn_id);
        $data['account_transactions'] = $this->VouchersModel->grid_account_transactions($voucher_txn_id);
        
        $data['invoice_title'] = 'TAX INVOICE';
        $data['voucher_txn_id'] = $voucher_txn_id;
        return view('admin/printing_templates/sales_invoice_without_item', $data);
    }
}
