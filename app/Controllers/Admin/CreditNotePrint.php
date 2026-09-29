<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use Mpdf\Mpdf;

class CreditNotePrint extends BaseController
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
     * URL: /admin/creditnoteprint/index/{voucher_txn_id}?p=1
     */
    public function index($voucher_txn_id)
    {   
        $browser_preview = ($this->request->getVar('p') == 1);

        $voucher_info = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);

        if (empty($voucher_info['vch_txn_id'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Voucher not found');
        }

        // Credit Note vch_sub_type_id values:
        // 1  => with item (stock)
        //  0 => without item (account-only)
        switch ($voucher_info['vch_sub_type_id']) {
            case 1:
                echo $this->print_credit_note_with_item_pdf($voucher_txn_id, $browser_preview);
                break;

            case 0:
                echo $this->print_credit_note_without_item_pdf($voucher_txn_id, $browser_preview);
                break;

            default:
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invalid voucher subtype');
        }
    }

    /**
     * Common data collector (mirrors SalePrint but also fetches the
     * original/against-voucher reference for the credit note header row).
     */
    private function _gatherCreditNoteData($voucher_txn_id)
    {
        $data = [];
		
        
        $data['voucher_txn_id']    = $voucher_txn_id;
        $data['voucher_info']      = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
        $data['company_info']      = $this->CommonModel->get_company_info($this->company_id);
        $data['company_address']   = $this->CommonModel->get_comp_ho_adrs_info($this->company_id, $this->bo_id);
        $data['get_comp_taxt_info']= $this->VouchersModel->get_branch_gstin_info($this->bo_id);

        $party_id                  = $data['voucher_info']['party_id'];
        $data['party_info']        = $this->VouchersModel->account_full_info($party_id);
        $data['party_address']     = $data['party_info']['address_info'] ?? [];

        $data['item_transactions']   = $this->VouchersModel->grid_item_transactions($voucher_txn_id);
        $data['sundry_transactions'] = $this->VouchersModel->grid_bsd_transactions($voucher_txn_id);
        $data['tax_summary']         = $this->VouchersModel->get_item_tax_summary($voucher_txn_id);
        $data['narration']           = $this->VouchersModel->get_voucher_long_narration($voucher_txn_id);

        // Credit note uses gstrinwsup (inward supply) for bill ref — adjust if
        // your credit note stores data in gstroutsup instead.
        $data['gstrinwsup_info']  = $this->VouchersModel->get_gstrinwsup_info($voucher_txn_id)  ?? [];
        // Fallback: some setups store credit-note refs in gstroutsup
        $data['gstroutsup_info']  = $this->VouchersModel->get_gstroutsup_info($voucher_txn_id)  ?? [];

        // POS / place of supply
        $pos_code = $data['gstrinwsup_info']['inwsup_pos']
                 ?? $data['gstroutsup_info']['outsup_pos']
                 ?? '';
        $data['pos_state_info'] = [];
        if (!empty($pos_code)) {
            $data['pos_state_info'] = $this->VouchersModel->pos_state_info($pos_code) ?? [];
        }

        // E-invoice & EWB
        $data['e_invoice']       = $this->VouchersModel->get_einvmaster_info($voucher_txn_id) ?? [];
        $data['ewbmstreqn_data'] = $this->VouchersModel->get_ewbmstreqn_data($voucher_txn_id) ?? [];
        $data['dispatch_from']   = $data['ewbmstreqn_data']['gstdispfrm_info'] ?? [];
        $data['ship_to']         = $data['ewbmstreqn_data']['gstshipton_info'] ?? [];

        // Bank details
        $data['bank_details'] = $this->VouchersModel->get_bank_info(
            $data['voucher_info']['vch_series_id'],
            $data['voucher_info']['vch_type_id']
        ) ?? [];

        // Transporter
        $data['transporter'] = $this->VouchersModel->transporter_info($voucher_txn_id) ?? [];

        // ── Original / against voucher info ───────────────────────────────
        // The bridge table (vchbridgen, vch_bridge_type = 1 for credit-against-sale)
        // stores the original sale voucher. Fetch it so we can print
        // "Original Invoice No / Date / Value" on the credit note.
        $bridge = $this->VouchersModel->get_voucher_bridge_info($voucher_txn_id, 1) ?? [];
        $data['original_voucher_info'] = [];
        if (!empty($bridge['vch_txn_id_src'])) {
            $orig = $this->VouchersModel->get_voucher_cons_info($bridge['vch_txn_id_src']);
            if ($orig) {
                $orig_bill = $this->VouchersModel->get_gstroutsup_info($bridge['vch_txn_id_src']) ?? [];
                $data['original_voucher_info'] = [
                    'vch_txn_id'   => $bridge['vch_txn_id_src'],
                    'bill_ref_no'  => $orig_bill['outsup_bill_ref_no'] ?? '',
                    'vch_date'     => $orig['vch_date']               ?? '',
                    'vch_amount'   => $orig['vch_total_amount']        ?? 0,
                ];
            }
        }
     
	 // ── ★ ORIGINAL SALE INVOICE DETAILS ★ ─────────────────────────────
        // vchbridgen bridge_type = 1 links Credit Note → original Sale voucher
        $sale_voucher_bridge = $this->VouchersModel->get_voucher_gstpaid_bridge_info(
            $voucher_txn_id,
            1   // bridge_type 1 = credit-note ↔ sale
        );

        $data['original_sale_inv_no']    = '';
        $data['original_sale_inv_date']  = '';
        $data['original_sale_inv_value'] = 0;

        if (!empty($sale_voucher_bridge['vch_txn_id_src'])) {
            $sale_voucher_txn_id = (int)$sale_voucher_bridge['vch_txn_id_src'];

            $orig = $this->VouchersModel->get_original_sale_invoice_details(
                $sale_voucher_txn_id
            );

            $data['original_sale_inv_no']    = $orig['original_inv_no'];
            $data['original_sale_inv_date']  = $orig['original_inv_date'];
            $data['original_sale_inv_value'] = $orig['original_inv_value'];
        }


        return $data;
    }

    /**
     * mPDF generator (identical helper to SalePrint).
     */
    private function _generatePdfFromHtml($html, $fileName, $browserPreview)
    {
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
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $fileName . '.pdf"');
            echo $mpdf->Output('', 'S');
            exit;
        } else {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '.pdf"');
            echo $mpdf->Output('', 'S');
            exit;
        }
    }

    // ──────────────────────────────────────────────────────────────────────
    //  CREDIT NOTE WITH ITEM  (sub_type 1)
    // ──────────────────────────────────────────────────────────────────────
    public function print_credit_note_with_item_pdf($voucher_txn_id, $browser_preview = false)
    {
        $data                  = $this->_gatherCreditNoteData($voucher_txn_id);
        $data['invoice_title'] = 'CREDIT NOTE';

        return view('admin/printing_templates/credit_note_with_item', $data);

        /* Uncomment to generate PDF via mPDF instead of browser view:
        $html = view('admin/printing_templates/credit_note_with_item', $data);
        return $this->_generatePdfFromHtml(
            $html,
            'CreditNote-' . $voucher_txn_id,
            $browser_preview
        ); */
    }

    // ──────────────────────────────────────────────────────────────────────
    //  CREDIT NOTE WITHOUT ITEM  (sub_type  0)
    // ──────────────────────────────────────────────────────────────────────
    public function print_credit_note_without_item_pdf($voucher_txn_id, $browser_preview = false)
	{
		$data = $this->_gatherCreditNoteData($voucher_txn_id);
      
		// Account transactions (Particulars) — credit side rows
		$data['account_transactions'] = $this->VouchersModel->grid_account_transactions($voucher_txn_id,$this->company_id);

		$data['invoice_title']   = 'CREDIT NOTE';
		$data['voucher_txn_id']  = $voucher_txn_id;

		return view('admin/printing_templates/credit_note_without_item', $data);
	}
}