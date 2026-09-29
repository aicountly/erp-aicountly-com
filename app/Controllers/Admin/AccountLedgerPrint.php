<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\AccountsModel;
use App\Models\CommonModel;
use App\Libraries\auth_session;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;

class AccountLedgerPrint extends BaseController
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
     * URL: /admin/AccountLedgerPrint/index/{acc_id}?from_date=YYYY-MM-DD&to_date=YYYY-MM-DD&p=1
     * p=1 shows browser preview; otherwise auto-download.
     */
	public function index($acc_id)
{
    $browser_preview = ($this->request->getVar('p') == 1);
    $from_date       = trim((string) $this->request->getGet('from_date'));
    $to_date         = trim((string) $this->request->getGet('to_date'));
    $from_date       = date('Y-m-d', strtotime(validate_fy_from_date($from_date)));
    $to_date         = date('Y-m-d', strtotime(validate_fy_to_date($to_date)));

    $account_info = $this->AccountsModel->account_info($acc_id);
    if (empty($account_info['acc_id'])) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Account not found');
    }

        $opening_balance = $this->AccountsModel->account_opening_balance(
        $acc_id, $from_date, 1, 0);
		
		$opening_balance_type ='DR.';
		$opening_balance_abs = abs($opening_balance);
		if($opening_balance<0)
			$opening_balance_type ='CR.';
    // export=1 to fetch full dataset without paging
    $ledgerPayload = $this->AccountsModel->load_accounts_ledger_condensed(
        $acc_id,
        $from_date,
        $to_date,
        1, // is_export
        1  // type
    );

    $records  = $ledgerPayload['data'] ?? [];
    $total_dr = 0;
    $total_cr = 0;
    foreach ($records as $row) {
        $total_dr += (float) ($row['debit_total']  ?? 0);
        $total_cr += (float) ($row['credit_total'] ?? 0);
    }

    // ✅ FIX: closing balance comes directly from the last record's
    //         running balance (balance_total = abs value, balance_type = DR./CR.)
    $closing_balance_abs  = 0;
    $closing_balance_type = 'DR.';

    if (!empty($records)) {
        $last_record          = end($records);
        $closing_balance_abs  = (float) ($last_record['balance_total'] ?? 0);
        $closing_balance_type = $last_record['balance_type'] ?? 'DR.';
    }

    $company_info       = $this->CommonModel->get_company_info($this->company_id);
    $company_address    = $this->CommonModel->get_comp_ho_adrs_info($this->company_id, $this->bo_id);
    $get_comp_taxt_info = $this->VouchersModel->get_branch_gstin_info($this->bo_id);
    $gstin              = $get_comp_taxt_info['hobo_gstin'] ?? '';
    // ✅ Create Opening Row
	$openingRow = [
		'txn_date'      => date('d/m/Y', strtotime($from_date)),
		'voucher_type'  => '',
		'voucher_no'    => '',
		'account_name'  => 'Opening Balance',
		'bill_ref_no'   => '',
		'short_narration' => '',
		'long_narration'  => '',
		'debit_total'   => ($opening_balance_type === 'DR.') ? $opening_balance_abs : 0,
		'credit_total'  => ($opening_balance_type === 'CR.') ? $opening_balance_abs : 0,
		'balance_total' => $opening_balance_abs,
		'balance_type'  => $opening_balance_type,
		'is_opening'    => 1 // 🔥 important flag
	];
	array_unshift($records, $openingRow);
    $data = [
        'browser_preview'      => $browser_preview,
        'company_info'         => $company_info,
        'company_address'      => $company_address,
        'gstin'                => $gstin,
        'account_info'         => $account_info,
        'from_date'            => $from_date,
        'to_date'              => $to_date,
        'records'              => $records,
        'total_debit'          => $total_dr,
        'total_credit'         => $total_cr,
        'closing_balance_abs'  => $closing_balance_abs,   // e.g. 62217.53
        'closing_balance_type' => $closing_balance_type,  // e.g. 'CR.'
		'opening_balance_abs'  => $opening_balance_abs,   // e.g. 62217.53
        'opening_balance_type' => $opening_balance_type,  // e.g. 'CR.'
        'rows_per_page'        => 27,
    ];
	
    return view('admin/printing_templates/account_ledger', $data);
} 
    
	}