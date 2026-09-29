<?php
namespace App\Controllers\Admin;

use App\Models\Admin\TransactionModel;

use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;

use \setasign\Fpdi\Fpdi;

use TCPDF;


class GeneratePDF extends BaseController
{
	function __construct()
	{  
		helper(['form', 'url','text','custom_hepler']);

		$this->TransactionModel  = new TransactionModel();			
		$this->auth_session  = new auth_session();		
		$this->CommonModel       =  new CommonModel();				
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->auth_session->is_company_opened();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
		$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
	}

	public function receipt_pdf($voucher_txn_id)
  {
      $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];

      $data['gst'] = $this->enc_string->nc_string($company_info['ho_gstin'],'de');
      $data['cin'] = $this->enc_string->nc_string($company_info['cin'],'de');

      $company_adrs1 = $this->enc_string->nc_string($company_info['ro_add1'],'de');
      $company_adrs2 = $this->enc_string->nc_string($company_info['ro_add2'],'de');
      $data['company_address']  = $company_adrs1;

      $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));

      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
      $data['narration'] = $get_narration_info['vch_narr'] ?? '';

      $account_transactions = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions = [];
      $debit_transactions = [];
      $amount = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= " (${short_narration})";
              }

              $credit_transactions[] = $string; 
          }
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= " (${short_narration})";
              }

              $debit_transactions[] = $string; 
          }
         
      }

      $data['credit_transactions'] = $credit_transactions;
      $data['debit_transactions'] = implode(', ', $debit_transactions);

      $data['amount'] = formatAmount($amount, false);
      $data['amount_words'] = getIndianCurrency(parseAmount($amount));

      $view = \Config\Services::renderer();
      return $view->setData($data)->render($this->folder_path.'vouchers/receipt_pdf');
  }

  public function receipt($voucher_txn_id)
  {
  	$data = $this->receipt_pdf($voucher_txn_id);
  	
		$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

		

		// $pdf->SetFont('times', 'B', 15); 
    // $pdf->SetTextColor(54, 52, 53);

    $pdf->SetPrintHeader(false);  
    $pdf->SetPrintFooter(false);

    $pdf->AddPage();

		// Write HTML content
		$pdf->writeHTML($data, false);

		// Output PDF
		$pdf->Output('google.pdf', 'D');

  }
  public function receipt_preview($voucher_txn_id)
  {
  	// return $this->receipt_pdf($voucher_txn_id);

      $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];

      $data['gst'] = $this->enc_string->nc_string($company_info['ho_gstin'],'de');
      $data['cin'] = $this->enc_string->nc_string($company_info['cin'],'de');

      $company_adrs1 = $this->enc_string->nc_string($company_info['ro_add1'],'de');
      $company_adrs2 = $this->enc_string->nc_string($company_info['ro_add2'],'de');
      $data['company_address']  = $company_adrs1;

      $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));

      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $get_narration_info = $this->TransactionModel->get_voucher_narration_info($voucher_txn_id,'long',0);
      $data['narration'] = $get_narration_info['vch_narr'] ?? '';

      $account_transactions = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions = [];
      $debit_transactions = [];
      $amount = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= " (${short_narration})";
              }

              $credit_transactions[] = $string; 
          }
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= " (${short_narration})";
              }

              $debit_transactions[] = $string; 
          }
      }

      $data['credit_transactions'] = $credit_transactions;
      $data['debit_transactions'] = implode(', ', $debit_transactions);

      $data['amount'] = formatAmount($amount, false);
      $data['amount_words'] = getIndianCurrency(parseAmount($amount));

      return view($this->folder_path.'vouchers/receipt_pdf', $data);
  }

  public function receipt2($voucher_txn_id)
  {
  	$data = $this->receipt_pdf($voucher_txn_id);
  	require_once(APPPATH.'Libraries/fpdi/autoload.php');
		require_once(APPPATH.'Libraries/fdf/Fpdf.php');
		require_once(APPPATH.'Libraries/fpdi/FpdiTrait.php');
		require_once(APPPATH.'Libraries/fpdi/FpdfTplTrait.php');
		require_once(APPPATH.'Libraries/fpdi/FpdfTpl.php');
		require_once(APPPATH.'Libraries/fpdi/Fpdi.php');
		require_once(APPPATH.'Libraries/fpdi/autoload.php');

		$pdf = new Fpdi;

		$pdf->AddPage();


		$pdf->SetFont('arial', 'B', 15); 
    $pdf->SetTextColor(54, 52, 53); // RGB	
    $pdf->WriteHTML($data);

		// Output PDF
		$pdf->Output('custom.pdf', 'D');
  }

  public function test()
  {
		require_once(APPPATH.'Libraries/fpdi/autoload.php');
		require_once(APPPATH.'Libraries/fdf/Fpdf.php');
		require_once(APPPATH.'Libraries/fpdi/FpdiTrait.php');
		require_once(APPPATH.'Libraries/fpdi/FpdfTplTrait.php');
		require_once(APPPATH.'Libraries/fpdi/FpdfTpl.php');
		require_once(APPPATH.'Libraries/fpdi/Fpdi.php');
		require_once(APPPATH.'Libraries/fpdi/autoload.php');

    $pdf = new Fpdi;

		$pdf->AddPage();
    $pdf->SetFont('arial', 'B', 8);

		$pdf->SetFont('arial', 'B', 15); 
    $pdf->SetTextColor(54, 52, 53); // RGB
    $pdf->SetLeftMargin(180);
    $pdf->SetXY(90,138);	
    $pdf->WriteHTML('<h1>Hello, World!</h1>');

		// Output PDF
		$pdf->Output('custom.pdf', 'D');
  }


}