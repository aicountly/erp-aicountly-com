<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\CommonModel;
use App\Libraries\auth_session;
class VoucherPrint extends BaseController
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
     * URL: /admin/voucherprint/index/{voucher_txn_id}?p=1
     */
    public function index($voucher_txn_id)
    {
        $browser_preview = ($this->request->getVar('p') == 1);

        $voucher_info = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
		
        if (empty($voucher_info['vch_txn_id'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Voucher not found');
        }
		
        switch ($voucher_info['vch_type_id']) {
            case 13:
                echo $this->print_receipt_pdf($voucher_txn_id, $browser_preview);
                break;

            case 9:
                echo $this->print_payment_pdf($voucher_txn_id, $browser_preview);
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

        $data['transactions']    = $this->VouchersModel->get_all_account_transactions($voucher_txn_id,1);
        $data['narration']             = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);
        
        return $data;
    }

   
    public function print_receipt_pdf($voucher_txn_id, $browser_preview = false)
    {
        $data = $this->_gatherInvoiceData($voucher_txn_id);
        $data['voucher_txn_id'] = $voucher_txn_id;
        return view('admin/printing_templates/receipt', $data);

    }
   public function print_payment_pdf($voucher_txn_id, $browser_preview = false)
    {
        $data = $this->_gatherInvoiceData($voucher_txn_id);
		
        $data['voucher_txn_id'] = $voucher_txn_id;
        return view('admin/printing_templates/payment', $data);

    }

  
}
