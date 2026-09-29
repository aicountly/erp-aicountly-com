<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\ItemSummaryModel;
use App\Models\Admin\StockStatusModel;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\ReportingModel;
use App\Models\Admin\GstrReportModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\ReportParams;
use App\Libraries\ReportSheetWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use \setasign\Fpdi\Fpdi;



use TCPDF;
 
require_once(APPPATH . 'ThirdParty/tcpdf/tcpdf.php'); // Make sure TCPDF is loaded

if (!class_exists('LedgerPDF')) {
    class LedgerPDF extends TCPDF {
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('dejavusans', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 
                        0, 0, 'C');
        }
    }
}

class Export extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text']);
		$this->AccountsModel     = new AccountsModel();
		$this->ItemsModel        = new ItemsModel();
		$this->ItemSummaryModel  = new ItemSummaryModel();
		$this->StockStatusModel  = new StockStatusModel();
		$this->VouchersModel     = new VouchersModel();
        $this->CommonModel       = new CommonModel();        
		$this->auth_session      = new auth_session();
		$this->ReportingModel    = new ReportingModel();
	    $this->auth_session->user_restrict();
	    $this->auth_session->is_company_opened();
	    $this->auth_session->role_restrict('CS');
		$this->base_url       = base_url().'/'.getenv('AdminPath');
		$this->folder_path    = getenv('AdminPath');
		$this->session    	  = \Config\Services::session();
		$this->GstrReportModel   = new GstrReportModel();
		
		$this->company_id     =  $this->session->get('ses_company_id');
		$this->fy_id          =  $this->session->get('ses_comp_fy_id');
		$this->bo_id          =  $this->session->get('ses_boid');
		$this->gstsummary_table_lists    = array("otax"=>"OUTPUT TAX",
		                       "otax_cr"=>"OUTPUT TAX (CR. NOTE)",
		                         "total_otax"=>"TOTAL OUTPUT TAX",
								 ""=>"",
							   "itax"=>"INPUT TAX",
							   "itax_dr"=>"INPUT TAX (DR. NOTE)",
                               "total_itax"=>"TOTAL INPUT TAX"							   
							   );
    }
	
	/**
 * Clean raw HTML cell data for spreadsheet output.
 * Strips HTML entities, currency symbols, sub/em tags, and box-prefix symbols.
 */
function clean($str)
{
    if ($str) {
        $str = utf8_decode($str);

        // Named HTML entities
        $str = str_replace('&raquo;',     '',  $str);  // » — strip (used as visual marker)
        $str = str_replace('&#8377;',     '',  $str);  // ₹
        $str = str_replace('&nbsp;',      '',  $str);  // non-breaking space

        // Box/square symbol HTML entities (pq-grid hierarchy markers)
        $boxEntities = [
            '&#9635;', '&#9634;', '&#9633;', '&#9632;',
            '&#9642;', '&#9643;', '&#9675;', '&#9670;',
        ];
        $str = str_replace($boxEntities, '', $str);

        // Sub/em tags
        $str = str_replace('<sub><em>',   ' ', $str);
        $str = str_replace('</em></sub>', ' ', $str);

        // Strip any remaining HTML tags
        $str = strip_tags($str);

        $str = trim($str);
    }
    return $str;
}

private function stripPrefixSymbols(string $str): string
{
    // First: decode any remaining HTML entities that survived clean()
    // (e.g. &raquo; may have been re-introduced by html_entity_decode
    //  operating on a string that still had the raw & in it)
    $str = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Strip all known prefix symbols in UTF-8 form
    $symbols = [
        // Right-angle quotation marks (» and ›) — most common in pq-grid
        '»',        // U+00BB  RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
        "\xC2\xBB", // » as raw UTF-8 bytes (belt-and-suspenders)
        '›',        // U+203A  SINGLE RIGHT-POINTING ANGLE QUOTATION MARK

        // Box/square characters
        '▣',        // U+25A3
        '▢',        // U+25A2
        '□',        // U+25A1
        '■',        // U+25A0
        '▪',        // U+25AA
        '▫',        // U+25AB
        '◻',        // U+25FB
        '◼',        // U+25FC

        // Triangle/arrow characters (some grids use these)
        '▸',        // U+25B8
        '▷',        // U+25B7
        '▶',        // U+25B6
        '▴',        // U+25B4

        // Bullet-style markers
        '‣',        // U+2023  TRIANGULAR BULLET
        '⁃',        // U+2043  HYPHEN BULLET

        // Latin-1 encoded » (after utf8_decode was applied upstream)
        "\xBB",     // » in latin-1 / ISO-8859-1
    ];

    $str = str_replace($symbols, '', $str);

    // Final trim to remove any leading/trailing whitespace left after removal
    return trim($str);
}
/*
 |--------------------------------------------------------------------------
 | Balance Sheet / Profit & Loss / Trial Balance -> Excel (.xlsx) or CSV
 |--------------------------------------------------------------------------
 | The parameters are normalised by ReportParams and the rows come from the model's load_*_view()
 | dispatchers - exactly the two calls Reportings.php makes for the page - so the file contains the
 | rows the page shows, cell for cell. ReportSheetWriter only lays them out: it computes no amount.
 | When the ledger itself is out of balance the file says so under the report (reconciliationNotes),
 | with the figures taken from the ledger; nothing is adjusted to make a report tally.
 */

/** Heading line, "Company (2025-2026)" - unchanged wording. */
private function reportTitle(): string
{
    $company_name = (string)$this->session->get('ses_company_name');
    $fy_begndt    = date('Y', strtotime($this->session->get('ses_company_fy_beginning')));
    $fy_end       = date('Y', strtotime($this->session->get('ses_company_fy_beginning') . ' + 1 year'));
    return $company_name . ' (' . $fy_begndt . '-' . $fy_end . ')';
}

/** "| Schedules view | Nil balances excluded | Consolidated (all branches)" - what the page's filters were set to. */
private function reportScope(array $p, array $viewNames, bool $withNil): string
{
    $s = '  |  ' . ($viewNames[$p['view']] ?? '') . ' view';
    if ($withNil) { $s .= '  |  Nil balances ' . ($p['nil_type'] === 0 ? 'included' : 'excluded'); }
    if ($p['consolidated']) { $s .= '  |  Consolidated (all branches)'; }
    return $s;
}

public function balance_sheet()
{
    $p     = ReportParams::balanceSheet($_GET);
    $rows  = $this->ReportingModel->load_balance_sheet_view(
                 $p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);
    $opt   = [
        'title'      => $this->reportTitle(),
        'subtitle'   => 'Balance Sheet as on ' . $p['to_date']
                        . $this->reportScope($p, [0 => 'Condensed', 1 => 'Schedules', 2 => 'Detailed'], true),
        'left_head'  => 'LIABILITIES',
        'right_head' => 'ASSETS',
        'detail'     => $p['view'] === 2,
        'sheet'      => 'Balance Sheet',
        'csv'        => $p['export'] === 'csv',
        'notes'      => $this->ReportingModel->reconciliationNotes($p['from_date'], $p['to_date'], $p['consolidated'], 'The Balance Sheet'),
    ];
    $book = ($p['format'] === 2)
        ? ReportSheetWriter::vertical($rows, $opt)
        : ReportSheetWriter::horizontal($rows, $opt);
    ReportSheetWriter::deliver($book, 'BalanceSheet', $p['export']);
}

public function profit_loss()
{
    $p     = ReportParams::profitLoss($_GET);
    $rows  = $this->ReportingModel->load_profit_loss_view(
                 $p['format'], $p['view'], $p['from_date'], $p['to_date'], $p['nil_type'], $p['consolidated']);
    $opt   = [
        'title'      => $this->reportTitle(),
        'subtitle'   => 'Profit & Loss Account for the period ' . $p['from_date'] . ' to ' . $p['to_date']
                        . $this->reportScope($p, [0 => 'Condensed', 1 => 'Schedules', 2 => 'Detailed'], true),
        'left_head'  => 'DEBITS',
        'right_head' => 'CREDITS',
        'detail'     => $p['view'] === 2,
        'sheet'      => 'Profit & Loss',
        'csv'        => $p['export'] === 'csv',
        'notes'      => [],
    ];
    $book = ($p['format'] === 2)
        ? ReportSheetWriter::vertical($rows, $opt)
        : ReportSheetWriter::horizontal($rows, $opt);
    ReportSheetWriter::deliver($book, 'ProfitLoss', $p['export']);
}

public function trial_balance()
{
    $p        = ReportParams::trialBalance($_GET);
    $viewName = [0 => 'Groups', 1 => 'Accounts', 2 => 'Opening'][$p['view']];
    $rows     = $this->ReportingModel->load_trial_balance_view(
                    $p['view'], $p['from_date'], $p['to_date'], $p['consolidated'], $p['nil_type']);
    $opt      = [
        'title'    => $this->reportTitle(),
        'subtitle' => 'Trial Balance — ' . $viewName . ' View  |  ' . $p['from_date'] . ' to ' . $p['to_date']
                      . ($p['consolidated'] ? '  |  Consolidated' : ''),
        'sheet'    => 'Trial Balance',
        'csv'      => $p['export'] === 'csv',
        'notes'    => $this->ReportingModel->reconciliationNotes($p['from_date'], $p['to_date'], $p['consolidated'], 'The Trial Balance'),
    ];
    ReportSheetWriter::deliver(ReportSheetWriter::trialBalance($rows, $opt), 'TrialBalance_' . $viewName, $p['export']);
}
	
	public function gstr_report_detail(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	   	$view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 0;
		$from_date = $_GET['from_date'] ?? '';
		$to_date   = $_GET['to_date'] ?? '';

        $from_date     = validate_fy_from_date($from_date);
        $to_date       = validate_fy_to_date($to_date);
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd   = date('Y-m-d', strtotime($to_date));
		
		$show_month_name = date('M,Y',strtotime($from_date_ymd));
		if($view==1){
		 $table_lists   = $this->table_lists_detailed;	
		 $response = json_decode($this->ReportsModel->load_gstr_detailed($from_date_ymd,$to_date_ymd,$table_lists),true);
		 $response_final = array();
		 if(isset($response['data'])){
		 foreach($response['data'] as $row){
			  $exploded = explode("_",$row['tableno']);
			   if(isset($exploded[1])){
				  $tname=  "   &raquo; ".$row['table_name'];
				  $tableno ="";
				  $is_sub_row=1;
			      }
				  else{
				  $tname =  $row['table_name'];
				  $tableno =$row['tableno'];
				  $is_sub_row=0;				  
				  }
				  
			$response_final[]=array("is_sub_row"=>$is_sub_row,"from_date"=>$from_date_ymd,"to_date"=>$to_date_ymd,"htableno"=>$row['tableno'],"tableno"=>$tableno,"table_name"=>$tname,"total_records"=>$row['total_records'],
			                        "sm_invoice_value"=>$row['sm_invoice_value'],"sm_taxable_value"=>$row['sm_taxable_value'],"sm_igst"=>$row['sm_igst'],"sm_cgst"=>$row['sm_cgst'],
									"sm_sgst"=>$row['sm_sgst'],"sm_cess"=>$row['sm_cess'],"sm_total_tax"=>$row['sm_total_tax']
									);						
			 
		      }
		 }
		$response['data'] =$response_final;
		}
		else{
		$table_lists   = $this->table_lists;	
		$response = json_decode($this->ReportsModel->load_gstr_condensed($from_date_ymd,$to_date_ymd,$table_lists),true);
		}
       
		$spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("GSTR1 Report");
		$debit_total=0;
		$credit_total=0;
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:J1');
		
		
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');

		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:J2');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A2")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A2', 'GSTR1 Report- '.$show_month_name);
        $sheet->setCellValue('A3', 'TABLE NO');
		$sheet->setCellValue('B3', 'TABLE NAME');
		$sheet->setCellValue('C3', 'NO. OF RECORDS');
		$sheet->setCellValue('D3', 'INVOICE VALUE(₹)');
		$sheet->setCellValue('E3', 'TAXABLE VALUE(₹)');
        $sheet->setCellValue('F3', 'IGST(₹)');	
		$sheet->setCellValue('G3', 'CGST(₹)');
		$sheet->setCellValue('H3', 'SGST(₹)');		
		$sheet->setCellValue('I3', 'CESS(₹)');
		$sheet->setCellValue('J3', 'TOTAL TAX(₹)');		
		
		$spreadsheet->getActiveSheet()->getStyle("A3:J3")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','J') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}

        $counter = 4;	
		$spreadsheet->getActiveSheet()->getStyle("A1:J1")->getFont()->setBold( true );
		$spreadsheet->getActiveSheet()->getStyle("A2:J2")->getFont()->setBold( true );
		$result = $response['data'];
      
		if($result){
		foreach($result as $row){
			if($view==1 && $row['is_sub_row']==0){
		$spreadsheet->getActiveSheet()->getStyle("B".$counter)->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000')							
						)));	
		}
			$sheet->setCellValue('A'.$counter , (string)$this->clean($row['tableno']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['table_name']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['total_records']));
			$sheet->setCellValue('D'.$counter , ($row['tableno']==13)?'':$this->clean(ParseAmount($row['sm_invoice_value'])));
			$sheet->setCellValue('E'.$counter , ($row['tableno']==13)?'':$this->clean(ParseAmount($row['sm_taxable_value'])));
			$sheet->setCellValue('F'.$counter , ($row['tableno']==13)?'':$this->clean(ParseAmount($row['sm_igst'])));
			$sheet->setCellValue('G'.$counter , ($row['tableno']==13)?'':$this->clean(ParseAmount($row['sm_cgst'])));
			$sheet->setCellValue('H'.$counter , ($row['tableno']==13)?'':$this->clean(ParseAmount($row['sm_sgst'])));
            $sheet->setCellValue('I'.$counter , ($row['tableno']==13)?'':$this->clean($row['sm_cess']));				
			$sheet->setCellValue('J'.$counter , ($row['tableno']==13)?'':$this->clean($row['sm_total_tax']));
			
			$spreadsheet->getActiveSheet()->getStyle('A'.$counter.':C'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "left", 
				"vertical" => "center"
			)
		     );	
			 $spreadsheet->getActiveSheet()->getStyle('D'.$counter.':J'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );
			/* 
		   $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			$spreadsheet->getActiveSheet()->getStyle('H'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     ); */	
			$counter++;
		}
	}
		/* $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':E'.$counter);
		$sheet->setCellValue('A'.$counter , 'Total');
		$sheet->setCellValue('F'.$counter , html_entity_decode($this->clean(parseAmount($debit_total))));
		$sheet->setCellValue('G'.$counter , html_entity_decode($this->clean(parseAmount($credit_total))));			
		
        $spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );
        $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     ); */			 
				
		
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$filename = "GSTR1 Report ".$show_month_name.".xlsx";
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		$writer->save('php://output');
		die();	
	}
	
	public function gstrsummary_report_detail(){
				
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	   	$view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 0;
		$from_date = $_GET['from_date'] ?? '';
		$to_date   = $_GET['to_date'] ?? '';
		$summary_view   = isset($_GET['summary_view']) && ($_GET['summary_view']!='')  ? $_GET['summary_view'] : 0;

        $from_date     = validate_fy_from_date($from_date);
        $to_date       = validate_fy_to_date($to_date);
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd   = date('Y-m-d', strtotime($to_date));
		
		$show_month_name = date('M,Y',strtotime($from_date_ymd));
		$table_lists   = $this->gstsummary_table_lists;
		// Tax Summary - Detailed View
    if ($summary_view == 0 && $view == 1) {
        $response = $this->GstrReportModel->load_gstsummary_detailed($from_date_ymd, $to_date_ymd);
    }
    // Tax Summary - Condensed View
    else if ($summary_view == 0 && $view == 0) {
        $response_data = $this->GstrReportModel->load_gstsummary_condensed($from_date_ymd, $to_date_ymd, $table_lists); 
        
        $response_final = array();
        if (isset($response_data['data'])) {
            foreach ($response_data['data'] as $row) {
                $row['ishsn'] = 0;
                
                if ($row['table_name'] != '') {
                    if ($row['table_key'] == 'total_otax' || $row['table_key'] == 'total_itax') {
                        $tname = "<strong>" . $row['table_name'] . "</strong>";
                    } else {
                        $tname = $row['table_name'];
                    }
                } else {
                    $tname = "";
                }
                
                $response_final[] = array(
                    "ishsn"            => 0,
                    "table_key"        => $row['table_key'],
                    "from_date"        => $from_date_ymd,
                    "to_date"          => $to_date_ymd,
                    "table_name"       => $tname,
                    "invoice_value"    => $row['invoice_value'],
                    "taxable_value"    => $row['taxable_value'],
                    "igst"             => $row['igst'],
                    "cgst"             => $row['cgst'],
                    "sgst"             => $row['sgst'],
                    "cess"             => $row['cess'],
                    "total_tax"        => $row['total_tax'],
                    "sm_invoice_value" => $row['sm_invoice_value'],
                    "sm_taxable_value" => $row['sm_taxable_value'],
                    "sm_igst"          => $row['sm_igst'],
                    "sm_cgst"          => $row['sm_cgst'],
                    "sm_sgst"          => $row['sm_sgst'],
                    "sm_cess"          => $row['sm_cess'],
                    "sm_total_tax"     => $row['sm_total_tax']
                );
            }
        }
		
		$response = array(
            "totalRecords" => count($response_final),
            "curPage"      => "1",
            "data"         => $response_final
        );
        
    }
    // HSN Summary - Condensed View
    else if ($summary_view == 1 && $view == 0) {
        $response_final = $this->GstrReportModel->load_gstsummary_hsn_condensed($from_date_ymd, $to_date_ymd);
        
        $master_array = array();
        $master_array['totalRecords'] = $response_final['totalRecords'];
        $master_array['curPage']      = $response_final['curPage'];
        $master_array['data']         = array();
        
        foreach ($response_final['data'] as $row) {
            if ($row['table_name'] != '') {
                if ($row['table_key'] == 'total_inward_supply_hsn' || $row['table_key'] == 'total_outward_supply_hsn') {
                    $tname = "<strong>" . $row['table_name'] . "</strong>";
                } else {
                    $tname = $row['table_name'];
                }
            } else {
                $tname = "";
            }
            
            $row['table_name'] = $tname;
            $row['ishsn']      = 1;
            $master_array['data'][] = $row;
        }
        
        $response = $master_array;
    }
    // HSN Summary - Detailed View
    else if ($summary_view == 1 && $view == 1) {
        $response_final = $this->GstrReportModel->load_gstsummary_hsn_detailed($from_date_ymd, $to_date_ymd);
        
        $master_array = array();
        $master_array['totalRecords'] = $response_final['totalRecords'];
        $master_array['curPage']      = $response_final['curPage'];
        $master_array['data']         = array();
        
        foreach ($response_final['data'] as $row) {
            if ($row['table_name'] != '') {
                if ($row['table_key'] == 'total_inward_supply_hsn' || $row['table_key'] == 'total_outward_supply_hsn') {
                    $tname = "<strong>" . $row['table_name'] .  "</strong>";
                } else {
                    $tname = $row['table_name'];
                }
            } else {
                $tname = "";
            }
            
            $row['ishsn']      = 1;
            $row['table_name'] = $tname;
            $master_array['data'][] = $row;
        }
        
        $response = $master_array;
    }
	

	
	$spreadsheet = new Spreadsheet();      
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("GSTR Summary Report");

// ===== HEADER =====
$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', $company_name.' ('.$fy_begndt.' - '.$fy_end.')');

$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A2', 'GSTR Summary Report - '.$show_month_name);

// STYLE HEADER
$sheet->getStyle("A1:A2")->getFont()->setBold(true)->setSize(15);
$sheet->getStyle("A1:A2")->getAlignment()->setHorizontal('center');

// ===== COLUMN HEADER =====
$sheet->setCellValue('A3', 'PARTICULARS');
$sheet->setCellValue('B3', 'INVOICE VALUE(₹)');
$sheet->setCellValue('C3', 'TAXABLE VALUE(₹)');
$sheet->setCellValue('D3', 'IGST(₹)');
$sheet->setCellValue('E3', 'CGST(₹)');
$sheet->setCellValue('F3', 'SGST(₹)');
$sheet->setCellValue('G3', 'CESS(₹)');
$sheet->setCellValue('H3', 'TOTAL TAX(₹)');

$sheet->getStyle("A3:H3")->getFont()->setBold(true)->setSize(13);

// AUTO WIDTH
foreach(range('A','H') as $col){
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ===== DATA =====
$counter = 4;
$result  = $response['data'];

// TOTAL HOLDERS
$sm_invoice_value_out = $sm_taxable_value_out = $sm_igst_out = $sm_cgst_out = $sm_sgst_out = $sm_cess_out = $sm_total_tax_out = 0;
$sm_invoice_value_in  = $sm_taxable_value_in  = $sm_igst_in  = $sm_cgst_in  = $sm_sgst_in  = $sm_cess_in  = $sm_total_tax_in  = 0;

foreach($result as $row){

    // ===== BLANK ROW =====
    if(empty($row['table_key'])){
        $counter++;
        continue;
    }

    // ===== TOTAL CALC =====
    if($row['table_key'] == "total_otax"){
        $sm_invoice_value_out += $row['sm_invoice_value'];
        $sm_taxable_value_out += $row['sm_taxable_value'];
        $sm_igst_out          += $row['sm_igst'];
        $sm_cgst_out          += $row['sm_cgst'];
        $sm_sgst_out          += $row['sm_sgst'];
        $sm_cess_out          += $row['sm_cess'];
        $sm_total_tax_out     += $row['sm_total_tax'];
    }

    if($row['table_key'] == "total_itax"){
        $sm_invoice_value_in += $row['sm_invoice_value'];
        $sm_taxable_value_in += $row['sm_taxable_value'];
        $sm_igst_in          += $row['sm_igst'];
        $sm_cgst_in          += $row['sm_cgst'];
        $sm_sgst_in          += $row['sm_sgst'];
        $sm_cess_in          += $row['sm_cess'];
        $sm_total_tax_in     += $row['sm_total_tax'];
    }

    // ===== WRITE ROW =====
	$cleanName = strip_tags($row['table_name']);
    $sheet->setCellValue('A'.$counter , $cleanName);
    $sheet->setCellValue('B'.$counter , ParseAmount($row['sm_invoice_value']));
    $sheet->setCellValue('C'.$counter , ParseAmount($row['sm_taxable_value']));
    $sheet->setCellValue('D'.$counter , ParseAmount($row['sm_igst']));
    $sheet->setCellValue('E'.$counter , ParseAmount($row['sm_cgst']));
    $sheet->setCellValue('F'.$counter , ParseAmount($row['sm_sgst']));
    $sheet->setCellValue('G'.$counter , ParseAmount($row['sm_cess']));
    $sheet->setCellValue('H'.$counter , ParseAmount($row['sm_total_tax']));

    // ALIGNMENT
    $sheet->getStyle('A'.$counter)->getAlignment()->setHorizontal('left');
    $sheet->getStyle('B'.$counter.':H'.$counter)->getAlignment()->setHorizontal('right');

    // ===== BOLD TOTAL ROWS =====
    if(strpos($row['table_key'], 'total_') !== false){
        $sheet->getStyle("A{$counter}:H{$counter}")->getFont()->setBold(true);
    }

    $counter++;
}

// ===== NET GST =====
$sheet->setCellValue('A'.$counter , "NET GST");

$sheet->setCellValue('B'.$counter , ParseAmount($sm_invoice_value_out - $sm_invoice_value_in));
$sheet->setCellValue('C'.$counter , ParseAmount($sm_taxable_value_out - $sm_taxable_value_in));
$sheet->setCellValue('D'.$counter , ParseAmount($sm_igst_out - $sm_igst_in));
$sheet->setCellValue('E'.$counter , ParseAmount($sm_cgst_out - $sm_cgst_in));
$sheet->setCellValue('F'.$counter , ParseAmount($sm_sgst_out - $sm_sgst_in));
$sheet->setCellValue('G'.$counter , ParseAmount($sm_cess_out - $sm_cess_in));
$sheet->setCellValue('H'.$counter , ParseAmount($sm_total_tax_out - $sm_total_tax_in));

$sheet->getStyle("A{$counter}:H{$counter}")->getFont()->setBold(true);

// ===== DOWNLOAD =====
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

$filename = "GSTR Summary Report ".$show_month_name.".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');

$writer->save('php://output');
exit;
      
		
	}
	
	 
	public function account_ledger_condensed(){
	     $company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	    
		$account_id = isset($_GET['accid']) && ($_GET['accid']!='')  ? $_GET['accid'] : 0;
		
		 $account_info          =  $this->AccountsModel->account_info($account_id);
		 $show_account_name     =  $account_info['acc_name'];	

		$export_type = $this->request->getGet('export_type') ?? 'excel';
		$from        = validate_fy_from_date($this->request->getGet('from_date') ?? '');
		$to          = validate_fy_to_date($this->request->getGet('to_date') ?? '');
		$fromYmd     = date('Y-m-d', strtotime($from));
		$toYmd       = date('Y-m-d', strtotime($to));
	    $m           = isset($_GET['m']) && ($_GET['m']!='')  ? $_GET['m'] : 0;
		$view        = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 0;
		
		
		 
		 $from_date = $_GET['from_date'] ?? '';
		 $to_date   = $_GET['to_date'] ?? '';

        $from_date     = validate_fy_from_date($from_date);
        $to_date       = validate_fy_to_date($to_date);
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd   = date('Y-m-d', strtotime($to_date));
		
	    $result = $this->AccountsModel->load_accounts_ledger_condensed($account_id,$from_date_ymd, $to_date_ymd,1,1);
		
		$spreadsheet = new Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Account Ledger');

		// --------- helpers ----------
		$currencyFmt = '#,##0.00'; // change if you need a different mask
		$bold = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getFont()->setBold(true);
		};
		$center = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		};
		$right = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		};
		$safe = function(array $row, string $key, $default='') {
			return isset($row[$key]) ? $row[$key] : $default;
		};
		// --------- /helpers ----------

		// Top titles
		$sheet->mergeCells('A1:I1');
		$sheet->mergeCells('A2:I2');
		$sheet->setCellValue('A1', $company_name.' ('.$fy_begndt.'-'.$fy_end.')');
		$sheet->setCellValue('A2', 'Account Ledger - '.$show_account_name);
		$bold('A1'); $bold('A2');
		$sheet->getStyle('A1')->getFont()->setSize(15);
		$sheet->getStyle('A2')->getFont()->setSize(13);
		$center('A1'); $center('A2');

		// Header
		$sheet->setCellValue('A3', 'DATE');
		$sheet->setCellValue('B3', 'TYPE');
		$sheet->setCellValue('C3', 'VCH/BILL NO');
		$sheet->setCellValue('D3', 'ACCOUNT');
		$sheet->setCellValue('E3', 'NARRATION');
		$sheet->setCellValue('F3', 'DEBIT(₹)');
		$sheet->setCellValue('G3', 'CREDIT(₹)');
		$sheet->setCellValue('H3', 'BALANCE(₹)');
		$sheet->setCellValue('I3', 'TYPE'); // <- missing earlier

		$bold('A3:I3');
		$sheet->getStyle('A3:I3')->getFont()->setSize(12);
		$center('A3:I3');

		// Freeze header row
		$sheet->freezePane('A4');

		// Autosize
		foreach (range('A','I') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Data
		$counter      = 4;
		$debit_total  = 0.0;
		$credit_total = 0.0;

		// $result can be JSON or array
		if (is_string($result)) {
			$decoded = json_decode($result, true);
			$resultArr = is_array($decoded) ? ($decoded['data'] ?? []) : [];
		} elseif (is_array($result)) {
			// if it’s already the JSON-decoded payload
			$resultArr = isset($result['data']) ? $result['data'] : $result;
		} else {
			$resultArr = [];
		}

		if (!empty($resultArr)) {
			foreach ($resultArr as $row) {
				// numeric safe-casts
				$dr = (float)$safe($row,'debit_total',0);
				$cr = (float)$safe($row,'credit_total',0);
				$balExport = $safe($row,'balance_total',0);
				$balType   = (string)$safe($row,'balance_type','');

				$debit_total  += $dr > 0 ? $dr : 0;
				$credit_total += $cr > 0 ? abs($cr) : 0;

				// Write cells
				$sheet->setCellValue('A'.$counter, (string)$safe($row,'txn_date',''));          // keep raw string date
				$sheet->setCellValue('B'.$counter, (string)$safe($row,'voucher_type',''));
				$sheet->setCellValue('C'.$counter, (string)$safe($row,'voucher_no',''));
				$sheet->setCellValue('D'.$counter, (string)$safe($row,'account_name',''));
				$sheet->setCellValue('E'.$counter, (string)$safe($row,'short_narration',''));

				$sheet->setCellValue('F'.$counter, $dr);
				$sheet->setCellValue('G'.$counter, $cr);
				// Use your parseAmount if it returns numeric; else cast and format here
				$sheet->setCellValue('H'.$counter, is_numeric($balExport) ? (float)$balExport : (float)str_replace([','],[''],$balExport));
				$sheet->setCellValue('I'.$counter, $balType);

				// Align numeric right
				$right('F'.$counter.':H'.$counter);

				$counter++;
			}

			// Apply number format to all numeric rows at once
			$sheet->getStyle('F4:H'.($counter-1))->getNumberFormat()->setFormatCode($currencyFmt);
		}

		// Totals row
		$sheet->mergeCells('A'.$counter.':E'.$counter);
		$sheet->setCellValue('A'.$counter, 'Total');
		$sheet->setCellValue('F'.$counter, $debit_total);
		$sheet->setCellValue('G'.$counter, $credit_total);
		$right('F'.$counter.':G'.$counter);
		$bold('A'.$counter.':I'.$counter);
		$sheet->getStyle('F'.$counter.':G'.$counter)->getNumberFormat()->setFormatCode($currencyFmt);

		// (Optional) outline around header + totals for clarity
		//$sheet->getStyle('A3:I3')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
		//$sheet->getStyle('A'.$counter.':I'.$counter)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

		// Output
		// Ensure no previous output ruins the file stream
		if (ob_get_length()) { ob_end_clean(); }

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Account Ledger.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		exit;
	}
	
	public function banking_voucher_print(){

		$voucher_txn_id        = $this->request->getGet('voucher_txn_id');
		$voucher_date          = $this->request->getGet('voucher_date');		
		$voucher_name          = $this->request->getGet('voucher_name');
        $voucher_type_array    = [1,5,9,13];	
        $voucher_info          = $this->VouchersModel->get_voucher_cons_info($voucher_txn_id);
	    if(empty($voucher_info)){
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
		
		$vch_type_id   = $voucher_info['vch_type_id'];
		
		if(!in_array($vch_type_id,$voucher_type_array)){
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
		$response      = $this->VouchersModel->get_all_account_transactions($voucher_txn_id);
		$company_name  = ucwords($this->session->get('ses_company_name'));
		$pdf_name      = $voucher_name.'_' . time() . '_' . url_title($company_name, '_', true) . '.pdf';
		$voucher_no    = '';
		
		$header_html = '
		<table width="100%" style="font-family: Segoe UI, Arial, sans-serif; font-size: 9pt; border-collapse: collapse;">
			<tr style="height: 90px;">
				<!-- Left: Logo -->
				<td width="33%" style="text-align: left; vertical-align: middle;">
					<br/>
					<img src="' . base_url() . '/public/assets/img/logo.png" alt="Logo" width="100" height="80">
				</td>
				<!-- Center: Title -->
				<td width="34%" style="text-align: center; vertical-align: middle;">
					<div style="line-height: 1.2; display: inline-block; vertical-align: middle;">
						<div style="font-size: 12.5pt; font-weight: bold; text-decoration: underline;">'. htmlspecialchars($voucher_name)  .' Voucher</div>
						<div style="font-size: 10pt;">' . strtoupper($company_name ?? 'Company Name') . '</div>
					</div>
				</td>		
				<!-- Right: Nested table for perfect middle alignment -->
				<td width="33%" style="vertical-align: middle;">
					<table align="right" style="font-size: 8.5pt;">
						<tr><td style="height: 10px;"></td></tr> <!-- This creates the top spacing -->
						<br/>
						<tr>
							<td style="text-align: right;">GSTIN :</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';

	$tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $tcpdf->SetCreator('AICountly');
    $tcpdf->SetAuthor('Company Name');
    $tcpdf->SetTitle($voucher_name.' Voucher-' . $company_name);
    $tcpdf->SetMargins(0.5, 35, 0.5); 
    $tcpdf->SetHeaderMargin(0);
	$tcpdf->SetFooterMargin(45,0,0,0);
    $tcpdf->SetAutoPageBreak(true, 40);
    $tcpdf->setPrintHeader(true);	  
	$tcpdf->SetDisplayMode(90);
	$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html); 
    $tcpdf->SetFont('dejavusans', '', 9);
    $tcpdf->AddPage();
	ob_start(); 
	?>
<table width="100%" cellpadding="6" cellspacing="0" style="border-collapse: collapse; font-family: Arial; font-size: 10pt; border: 1px solid black;">
  <!-- Voucher Header -->
  <tr>
    <td style="border: 0.5px solid black; width: 50%; text-align: left;">Voucher No. : <?= htmlspecialchars($voucher_no) ?></td>
    <td style="border: 0.5px solid black; width: 50%; text-align: left;">Dated : <?= htmlspecialchars($voucher_date) ?></td>
  </tr>

  <!-- Table Heading -->
  <tr style="">
    <th style="border-left: 0.5px solid black; text-align: left; width: 45%; font-weight: bold;"><u>Particulars</u></th>
    <th style="text-align: left; width: 20%; font-family: dejavusans; font-weight: bold;"><u>Debit(₹)</u></th>
    <th style=" text-align: left; width: 20%; font-family: dejavusans; font-weight: bold;"><u>Credit(₹)</u></th>
    <th style=" text-align: left; width: 15%; font-weight: bold;"><u>Narration</u></th>
  </tr>
<?php
$debit_sum = 0;
$credit_sum = 0;
$total = 0;
foreach ($response as $row):
$debit = (float)  $row['debit'] ;
$credit = (float)  $row['credit'] ;
$debit_sum += $debit;
$credit_sum += $credit;	
?>
			<tr>
			<td style="border-left: 0.5px solid black; "><?= htmlspecialchars($row['account_name']) ?></td>
			<td style=" text-align: left;font-family: dejavusans;"><?= htmlspecialchars(parseAmount($row['debit'])) ?></td>
			<td style=" text-align: left;font-family: dejavusans;"><?= htmlspecialchars(parseAmount($row['credit'])) ?></td>
    		<td style=""><?= htmlspecialchars($row['description']) ?></td>
			</tr>
			<?php endforeach; ?>


</table>

	
<?php
$words = getIndianCurrency($debit_sum);
	
$footer_html = '
<table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse; font-family: Arial; font-size: 10pt;">
  <tr>
    <td style="border: 0.5px solid black; width: 50%;"></td>
	<td style="border-top: 0.5px solid black; border-bottom: 0.5px solid black; text-align: left; font-weight: bold; width: 20%;">' . 
    (isset($debit_sum) ? '₹' . number_format($debit_sum, 2) : '') . 
'</td>

    <td style="border-top: 0.5px solid black; border-bottom: 0.5px solid black; text-align: left; font-weight: bold; width: 20%;">' . 
    (isset($credit_sum) ? '₹' . number_format($credit_sum, 2) : '') . 
'</td>

	<td style="border-top: 0.5px solid black; border-bottom: 0.5px solid black; border-right: 0.5px solid black; border-bottom: 0.5px solid black; text-align: left; font-weight: bold; width: 10%;"></td>
  </tr>
</table>

<!-- Narration Row -->
<table width="100%" cellpadding="6" cellspacing="0" style="border-collapse: collapse; font-family: Arial; font-size: 10pt; border-left: 1px solid black; border-right: 1px solid black;">
  <tr>
    <td style="border-top: none; border-bottom: 1px solid black;">Being Salary Paid to (Name)</td>
  </tr>
</table>

<!-- Amount in Words + Signature -->
<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; font-family: Arial; border: 1px solid black;">
  <tr>
   <td style="width: 40%; font-weight: bold; font-size: 13pt;">' . htmlspecialchars($words) . '</td>

    <td style="width: 60%; text-align: right; border-left: 0.5px solid black; font-size: 10pt;">
      <span style="">For Company Name</span><br><br><br>
      Authorized Signatory
    </td>
  </tr>
</table>';


$tcpdf->setPrintFooter(true);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);

$html = ob_get_clean(); 

$html = str_replace('₹', '&#8377;', $html);
	
	

$tcpdf->writeHTML($html, true, false, true, false, '');
	$tcpdf->lastPage();
	ob_end_clean();
	

  $tcpdf->Output($pdf_name, 'D');
exit; // IMPORTANT: prevent any extra output from CI

	}
	
	
	public function account_ledger_columnar(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	    
		$account_id = isset($_GET['accid']) && ($_GET['accid']!='')  ? $_GET['accid'] : 0;
		
		 $account_info          =  $this->AccountsModel->account_info($account_id);
		 $show_account_name     =  $account_info['acc_name'];	  
		
		
		 $m = isset($_GET['m']) && ($_GET['m']!='')  ? $_GET['m'] : 0;
		 $view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 0;
		 $from_date = $_GET['from_date'] ?? '';
		 $to_date   = $_GET['to_date'] ?? '';

        $from_date     = validate_fy_from_date($from_date);
        $to_date       = validate_fy_to_date($to_date);
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd   = date('Y-m-d', strtotime($to_date));
		$extra_columns =array();
		$extra_columns_totals =array();
		$result = json_decode($this->AccountsModel->load_accounts_ledger_condensed_with_columns($account_id,$from_date_ymd,$to_date_ymd,1,1),true);	
		if($result){
			$extra_columns =$result['acc_columns'];
			$extra_columns_totals =$result['acc_columnar_total'];
		}
		
		 
		$spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Account Ledger Columnar");
		$debit_total=0;
		$credit_total=0;
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:H1');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');

		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:H2');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A2")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A2', 'Account Ledger Columnar- '.$show_account_name);
        $sheet->setCellValue('A3', 'DATE');
		$sheet->setCellValue('B3', 'TYPE');
		$sheet->setCellValue('C3', 'VCH/BILL NO');
		$sheet->setCellValue('D3', 'ACCOUNT');
		$sheet->setCellValue('E3', 'POS');
		$sheet->setCellValue('F3', 'LONG NARRATION');
        $sheet->setCellValue('G3', 'AMOUNT(₹)');	
		$sheet->setCellValue('H3', 'BALANCE(₹)');		
		$counter=8;
		if($extra_columns){
		  foreach($extra_columns as $excol){
			$counter++;  
			$columnindex= Coordinate::stringFromColumnIndex($counter);
			
			$sheet->setCellValue($columnindex.'3', strtoupper($excol['acc_name']).'(₹)');  
		  }	
		}
		
		
		
		$last_columnindex= Coordinate::stringFromColumnIndex($counter);
		$spreadsheet->getActiveSheet()->getStyle("A3:".$last_columnindex."3")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A',$last_columnindex) as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
       
		$spreadsheet->getActiveSheet()->getStyle("A1:".$last_columnindex."1")->getFont()->setBold( true );
		$spreadsheet->getActiveSheet()->getStyle("A2:".$last_columnindex."2")->getFont()->setBold( true );
		$AmountSum    = 0;
		$credit_total = 0;	
        $result_res   = $result['data'];
		$columnname   = array();
		$scounter     = 4;	
			
		$columnnamearray=array();
		if($result_res){
		//$extra_columns_totals = $result['acc_columnar_total'];

	
		foreach($result_res as $key => $row){		
              	
			$ExtraColumnsSum=0;	
			$voucher_txn_id = $row['voucher_txn_id'];
			// extra columns totals array 			
			$AmountSum +=parseAmount($row['account_txn_amount_exp']);
			$sheet->setCellValue('A'.$scounter , $this->clean($row['txn_date']));
			$sheet->setCellValue('B'.$scounter , $this->clean($row['voucher_type']));
			$sheet->setCellValue('C'.$scounter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('D'.$scounter , $this->clean($row['account_name']));
			$sheet->setCellValue('E'.$scounter , "");
			$sheet->setCellValue('F'.$scounter , $this->clean($row['long_narration']));
			$sheet->setCellValue('G'.$scounter , abs($this->clean($row['account_txn_amount_exp'])));
			$sheet->setCellValue('H'.$scounter , $this->clean($row['balance_total']));
			$bcounter=8;
			if($extra_columns){
		      foreach($extra_columns as $accid =>  $excol){
			   $bcounter++;  
			   $columnindex = Coordinate::stringFromColumnIndex($bcounter);
			  echo  $columnname  = $accid;
			   echo '<br>';
			   if(isset($extra_columns_totals[$voucher_txn_id][$columnname])){
				  $balance = $extra_columns_totals[$voucher_txn_id][$columnname]['actbal'];				  
				  $columnnamearray[$columnname][]=$balance;
			     }
				else{
				    $balance = 0;
					$columnnamearray[$columnname][]=$balance;	
				 }
				 
				$sheet->setCellValue($columnindex.$scounter, abs($balance)); 
							
			    }				 
			    
		      }	
		  
			$spreadsheet->getActiveSheet()->getStyle('F'.$scounter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('G'.$scounter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			$spreadsheet->getActiveSheet()->getStyle('H'.$scounter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			$scounter++;
		}
	
			
	} 
	    $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$scounter.':F'.$scounter);
		$sheet->setCellValue('A'.$scounter , 'Total');
		if($this->clean($AmountSum)<0)
			 $slabel='Cr.';
		 else
			$slabel='Dr.'; 
		$sheet->setCellValue('G'.$scounter , abs(html_entity_decode($this->clean($AmountSum))).' '.$slabel);
		 $bcounter=8;
			if($extra_columns){
		      foreach($extra_columns as $accid =>  $excol){
			   $bcounter++;  
			   $columnindex = Coordinate::stringFromColumnIndex($bcounter);
			   $columnname  = $accid;
			   $balancess  = array_sum($columnnamearray[$columnname]);
			   
			   if($balancess<0)
				 $sslabel='Cr.';
			 else
				$sslabel='Dr.'; 
			   
			   $sheet->setCellValue($columnindex.$scounter, abs($balancess).' '.$sslabel);
			   }
			} 
        $spreadsheet->getActiveSheet()->getStyle('F'.$scounter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );
        $spreadsheet->getActiveSheet()->getStyle('G'.$scounter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)); 		 
			
		$bcounter=8;
			if($extra_columns){
		      foreach($extra_columns as $accid =>  $excol){
			   $bcounter++;  
			   $columnindex = Coordinate::stringFromColumnIndex($bcounter);
			   $spreadsheet->getActiveSheet()->getStyle($columnindex.$scounter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)); 	
			  }
			}			  
		
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');		
		header('Content-Disposition: attachment;filename="'.$show_account_name.' - Account Ledger Columnar.xlsx"');
		$writer->save('php://output');
		die();	
	}

	public function account_ledger_detailed(){
			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
			$fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
			$account_id = isset($_GET['accid']) && ($_GET['accid']!='')  ? $_GET['accid'] : 0;
			$account_info          =  $this->AccountsModel->account_info($account_id);
			$show_account_name     =  $account_info['acc_name'];	  


			 $m = isset($_GET['m']) && ($_GET['m']!='')  ? $_GET['m'] : 0;
			 $from_date = $_GET['from_date'] ?? '';
			 $to_date   = $_GET['to_date'] ?? '';

			$from_date     = validate_fy_from_date($from_date);
			$to_date       = validate_fy_to_date($to_date);
			$from_date_ymd = date('Y-m-d', strtotime($from_date));
			$to_date_ymd   = date('Y-m-d', strtotime($to_date));

			if($m == 1){
				$result = [];
			}	
			else{
				$result = $this->AccountsModel->load_accounts_ledger_detailed($account_id,$from_date_ymd, $to_date_ymd,1,1);
			}
			
			
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet(); 
			$sheet->setTitle("Account Ledger");
			$debit_total=0;
			$credit_total=0;
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:J1');
			$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
					'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 15
			)));
			$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
					"horizontal" => "center", 
					"vertical" => "center"
			)
			);
			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:J2');
			$spreadsheet->getActiveSheet()->getStyle("A2")->applyFromArray(array(
					'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 15
			)));
			$spreadsheet->getActiveSheet()->getStyle("A2")->getAlignment()->applyFromArray(
			array(
					"horizontal" => "center", 
					"vertical" => "center"
			)
			);	
			$lastcell_style = array(
					'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 15
			));		
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Account Ledger Detailed- '.$show_account_name);
			$sheet->setCellValue('A3', 'DATE');
			$sheet->setCellValue('B3', 'TYPE');
			$sheet->setCellValue('C3', 'VCH/BILL NO');
			$sheet->setCellValue('D3', 'ACCOUNT');
			$sheet->setCellValue('E3', 'NARRATION');
			$sheet->setCellValue('F3', 'DEBIT(₹)');	
			$sheet->setCellValue('G3', 'CREDIT(₹)');
			$sheet->setCellValue('H3', 'BALANCE(₹)');		
			$spreadsheet->getActiveSheet()->getStyle("A3:H3")->applyFromArray(array(
					'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 13
			)));
			foreach(range('A','H') as $columnID) {
					$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

			$counter = 4;	
			$spreadsheet->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold( true );
			$spreadsheet->getActiveSheet()->getStyle("A2:H2")->getFont()->setBold( true );
			$debit_total=0;
			$credit_total=0;
			// $result can be JSON or array
			if (is_string($result)) {
				$decoded = json_decode($result, true);
				$resultArr = is_array($decoded) ? ($decoded['data'] ?? []) : [];
			} elseif (is_array($result)) {
				// if it’s already the JSON-decoded payload
				$resultArr = isset($result['data']) ? $result['data'] : $result;
			} else {
				$resultArr = [];
			}
			
			/* echo '<pre>';
			print_r($resultArr);
			die(); */
			foreach($resultArr as $row){
					$debit = '';
					$credit = '';

					if($row['debit_total'] > 0){
						$debit = $row['debit_total'];
						$debit_total=$debit_total+$row['debit_total'];
					}
					if($row['credit_total'] > 0){
						$credit = $row['credit_total'];
						$credit_total=$credit_total+$row['credit_total'];
					}

					$voucher_date = !empty($row['txn_date']) ? date('d-m-Y', strtotime($row['txn_date'])) : '';

					$sheet->setCellValue('A'.$counter , $this->clean($voucher_date));
					$sheet->setCellValue('B'.$counter , $this->clean($row['voucher_type']));
					$sheet->setCellValue('C'.$counter , $this->clean($row['voucher_no']));
					$sheet->setCellValue('D'.$counter , $this->clean($row['account_name']));
					$sheet->setCellValue('E'.$counter , $this->clean($row['long_narration']));
					$sheet->setCellValue('F'.$counter , $this->clean($debit));
					$sheet->setCellValue('G'.$counter , $this->clean($credit));
					if($row['balance_total']>0)
					$sheet->setCellValue('H'.$counter , $this->clean(abs($row['balance_total'])));
				    else
					$sheet->setCellValue('H'.$counter , '');
					$sheet->setCellValue('I'.$counter , $this->clean($row['balance_type']));				

					$spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
					array(
						"horizontal" => "right", 
						"vertical" => "center"
					)
					);	
					$spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
					array(
						"horizontal" => "right", 
						"vertical" => "center"
					)
					);	
					$spreadsheet->getActiveSheet()->getStyle('H'.$counter)->getAlignment()->applyFromArray(
					array(
						"horizontal" => "right", 
						"vertical" => "center"
					)
					);	
					$counter++;
			}

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':E'.$counter);
			$sheet->setCellValue('A'.$counter , 'Total');
			$sheet->setCellValue('F'.$counter , html_entity_decode($this->clean(parseAmount($debit_total))));
			$sheet->setCellValue('G'.$counter , html_entity_decode($this->clean(parseAmount($credit_total))));			

			$spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
					array(
						"horizontal" => "right", 
						"vertical" => "center"
					)
			);
			$spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
					array(
							"horizontal" => "right", 
							"vertical" => "center"
					)
			);			 


			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$show_account_name.' - Account Ledger Detailed.xlsx"');
			$writer->save('php://output');
			die();	
	}
	
	public function item_summary(){
	    $company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	    $fy_short_end    = date('y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	    
		
		$item_id   = !empty($_GET['item_id']) ? $_GET['item_id'] : 0;
        $unit_id   = (isset($_GET['unit_id']) && $_GET['unit_id'] != '') ? $_GET['unit_id'] : 0;
        $mc_id     = (isset($_GET['mc_id']) && $_GET['mc_id'] != '') ? $_GET['mc_id'] : 0;
        $mc_grp_id = (isset($_GET['mc_grp_id']) && $_GET['mc_grp_id'] != '') ? $_GET['mc_grp_id'] : 0;
        $val       = (isset($_GET['val']) && $_GET['val'] != '') ? $_GET['val'] : '';
        $invtp_id  = (isset($_GET['invtp_id']) && $_GET['invtp_id'] != '') ? $_GET['invtp_id'] : 1;
		
		
		 $item_info          =  $this->ItemsModel->item_info($item_id);
		 
		 $show_account_name     =  $item_info['itm_name'];	

		$export_type = $this->request->getGet('export_type') ?? 'excel';
		$from        = validate_fy_from_date($this->request->getGet('from_date') ?? '');
		$to          = validate_fy_to_date($this->request->getGet('to_date') ?? '');
		$fromYmd     = date('Y-m-d', strtotime($from));
		$toYmd       = date('Y-m-d', strtotime($to));
	   
		
		$p = [
            'cmp_id'            => $this->company_id,
            'cmpfymasr_id'      => $this->fy_id,
            'fy_start_ymd'      => $fromYmd,
            'fy_end_ymd'        => $toYmd,
            'itm_id_unit_id'    => $item_id.'_'.$unit_id,
			'itm_id'            => $item_id,
            'itm_val_method_id' => $invtp_id,
            'mat_cent_id'       => $mc_id ? (int)$mc_id : null,
            'hobo_id'           =>  $this->bo_id,
            'uom'               => $unit_id,
        ];		
		
        $result                = $this->ItemSummaryModel->getMonthlySummary($p);
				
		$spreadsheet = new Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Item Summary');

		// --------- helpers ----------
		$currencyFmt = '#,##0.00'; // change if you need a different mask
		$bold = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getFont()->setBold(true);
		};
		$center = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		};
		$right = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		};
		$safe = function(array $row, string $key, $default='') {
			return isset($row[$key]) ? $row[$key] : $default;
		};
		// --------- /helpers ----------

		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
		$sheet->setCellValue('A2', 'Item Summary - '.$show_account_name);
			
		$sheet->setCellValue('A3', 'Month (FY: '.$fy_begndt.'-'.$fy_short_end.')');
		$sheet->setCellValue('B3', 'UOM');
		$sheet->setCellValue('C3', 'Debit');
		$sheet->setCellValue('E3', 'Credit');
		$sheet->setCellValue('G3', 'Balance');

		// merge parent cells across their children
		$sheet->mergeCells('A1:I1');     // Month spans 2 rows
		$sheet->mergeCells('A2:I2');     // UOM spans 2 rows
		$sheet->mergeCells('C3:D3');     // Debit spans 2 cols
		$sheet->mergeCells('E3:F3');     // Credit spans 2 cols
		$sheet->mergeCells('G3:H3');     // Balance spans 2 cols

		// --- children (row 2) ---
		$sheet->setCellValue('C4', 'Qty');
		$sheet->setCellValue('D4', 'Amount');
		$sheet->setCellValue('E4', 'Qty');
		$sheet->setCellValue('F4', 'Amount');
		$sheet->setCellValue('G4', 'Qty');
		$sheet->setCellValue('H4', 'Value');

		$bold('A3:H3');
		$sheet->getStyle('A3:H3')->getFont()->setSize(12);
		$center('A3:H3');

		// Freeze header row
		$sheet->freezePane('A4');

		// Autosize
		foreach (range('A','H') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Data
		$counter      = 5;
		$debit_qty    = 0;
		$debit_amount = 0;
		$credit_qty   = 0;
		$credit_amount= 0;
		$balance_qty  = 0;
		$balance_value= 0;
		
		if (is_array($result)) {
			// if it’s already the JSON-decoded payload
			$resultArr = isset($result['rows']) ? $result['rows'] : $result;
			$debit_qty = $result['totals']['debit_qty'];
			$debit_amount = $result['totals']['debit_amount'];
			$credit_qty = $result['totals']['credit_qty'];
			$credit_amount = $result['totals']['credit_amount'];
			$balance_qty = $result['totals']['balance_qty'];
			$balance_value = $result['totals']['balance_value'];
		} else {
			$resultArr = [];
		}

		if (!empty($resultArr)) {
			foreach ($resultArr as $row) {
				
				$sheet->setCellValue('A'.$counter, (string)$safe($row,'month',''));          
				$sheet->setCellValue('B'.$counter, (string)$safe($row,'unit_name',''));
				$sheet->setCellValue('C'.$counter, (string)$safe($row,'debit_qty',''));
				$sheet->setCellValue('D'.$counter, (string)$safe($row,'debit_amount',''));
				$sheet->setCellValue('E'.$counter, (string)$safe($row,'credit_qty',''));

				$sheet->setCellValue('F'.$counter, (string)$safe($row,'credit_amount',''));
				$sheet->setCellValue('G'.$counter, (string)$safe($row,'balance_qty',''));
				// Use your parseAmount if it returns numeric; else cast and format here
				$sheet->setCellValue('H'.$counter, (string)$safe($row,'balance_value',''));
				
				// Align numeric right
				$right('F'.$counter.':H'.$counter);
                
				$counter++;
			}

			// Apply number format to all numeric rows at once
			//$sheet->getStyle('F4:H'.($counter-1))->getNumberFormat()->setFormatCode($currencyFmt);
		}

		// Totals row
		$sheet->mergeCells('A'.$counter.':B'.$counter);
		$sheet->setCellValue('A'.$counter, 'Total');
		$sheet->setCellValue('C'.$counter, $debit_qty);
		$sheet->setCellValue('D'.$counter, $debit_amount);
		$sheet->setCellValue('E'.$counter, $credit_qty);
		$sheet->setCellValue('F'.$counter, $credit_amount);
		$sheet->setCellValue('G'.$counter, $balance_qty);
		$sheet->setCellValue('H'.$counter, $balance_value);
		
		//$sheet->getStyle('F'.$counter.':G'.$counter)->getNumberFormat()->setFormatCode($currencyFmt);

		// Output
		// Ensure no previous output ruins the file stream
		if (ob_get_length()) { ob_end_clean(); }

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'.$show_account_name.' - Item Summary.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		exit;
	}
	
	
	public function account_summary(){
	    $company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	    $fy_short_end    = date('y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	    
		
		$acc_id   = !empty($_GET['acc_id']) ? $_GET['acc_id'] : 0;       
       
		
		$account_info          =  $this->AccountsModel->account_info($acc_id);
		$show_account_name     =  $account_info['acc_name'];
		 

		$export_type = $this->request->getGet('export_type') ?? 'excel';
		$from        = validate_fy_from_date($this->request->getGet('from_date') ?? '');
		$to          = validate_fy_to_date($this->request->getGet('to_date') ?? '');
		$fromYmd     = date('Y-m-d', strtotime($from));
		$toYmd       = date('Y-m-d', strtotime($to));
	   
		$account_name = $account_info['acc_name'];
		$view         = isset($_GET['view']) ? $_GET['view'] : 1;
		$to_date      = validate_to_date('');		
		$acc_op_bal   = $this->AccountsModel->account_opening_balance($acc_id,$toYmd);
		
	 	if($acc_op_bal < 0)
	 	  $acc_opn_balance = formatAmount(abs($acc_op_bal)).' CR';
	 	else
	 	  $acc_opn_balance = formatAmount($acc_op_bal).' DR';	
		
		
		if($view==1)
	     	$resultArr = $this->AccountsModel->accounts_monthly_details($acc_id,$acc_op_bal);
	    if($view==2)
	     	$resultArr = $this->AccountsModel->accounts_quaterly_balance($acc_id,$acc_op_bal);
	    if($view==3)
	     	$resultArr = $this->AccountsModel->accounts_yearly_balance($acc_id,$acc_op_bal);
		
				
		$spreadsheet = new Spreadsheet();
		$sheet       = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Account Summary');

		// --------- helpers ----------
		$currencyFmt = '#,##0.00'; // change if you need a different mask
		$bold = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getFont()->setBold(true);
		};
		$center = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		};
		$right = function(string $range) use ($sheet) {
			$sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
			$sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		};
		$safe = function(array $row, string $key, $default='') {
			return isset($row[$key]) ? $row[$key] : $default;
		};
		
		$setMergedHeader = function ($row, string $text, string $fromCol = 'A', string $toCol = 'E') use ($sheet) {
			$range = "{$fromCol}{$row}:{$toCol}{$row}";
			$sheet->mergeCells($range);
			$sheet->setCellValue("{$fromCol}{$row}", $text);
		};

		// --------- /helpers ----------
		
		// set values + merges
		$setMergedHeader(1, $company_name . " ({$fy_begndt}-{$fy_end})");
		$setMergedHeader(2, 'Account Summary - ' . $show_account_name);
		// shared style for both header rows
		$headerStyle = [
			'font'      => ['bold' => true, 'size' => 15, 'color' => ['rgb' => '000000']],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical'   => Alignment::VERTICAL_CENTER,
			],
		];

		// apply once to both merged rows
		$sheet->getStyle('A1:E2')->applyFromArray($headerStyle);


			
		$sheet->setCellValue('A3', 'Month (FY: '.$fy_begndt.'-'.$fy_short_end.')');		
		$sheet->setCellValue('B3', 'Debit');
		$sheet->setCellValue('C3', 'Credit');
		$sheet->setCellValue('D3', 'Balance');
		
		$bold('A3:H3');
		$sheet->getStyle('A3:H3')->getFont()->setSize(12);
		$center('A3:H3');

		// Freeze header row
		$sheet->freezePane('A4');

		// Autosize
		foreach (range('A','H') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Data
		$counter      = 5;		
		$debit_amount = 0;		
		$credit_amount= 0;		
		$balance_value= 0;
		
		if (is_array($resultArr)) {
			// if it’s already the JSON-decoded payload
						
			//$debit_amount = $result['totals']['debit_total'];		
			//$credit_amount = $result['totals']['credit_total'];			
			//$balance_value = $result['totals']['balance_total'];
		} else {
			$resultArr = [];
		}

		if (!empty($resultArr)) {
			foreach ($resultArr as $row) {
				
				$sheet->setCellValue('A'.$counter, (string)$safe($row,'month',''));          
				
	
				$sheet->setCellValue('B'.$counter, (string)$safe($row,'debit_total',''));
			

				$sheet->setCellValue('C'.$counter, (string)$safe($row,'credit_total',''));
			
		
				$sheet->setCellValue('D'.$counter, (string)$safe($row,'balance_total',''));
				
				// Align numeric right
				$right('B'.$counter.':D'.$counter);
                
				$counter++;
			}

			// Apply number format to all numeric rows at once
			//$sheet->getStyle('F4:H'.($counter-1))->getNumberFormat()->setFormatCode($currencyFmt);
		}

		// Totals row
		
		$sheet->setCellValue('A'.$counter, 'Total');
		$sheet->setCellValue('B'.$counter, $debit_amount);
		$sheet->setCellValue('C'.$counter, $credit_amount);
		$sheet->setCellValue('D'.$counter, $balance_value);
		$sheet->setCellValue('E'.$counter, '');
		
		// Output
		// Ensure no previous output ruins the file stream
		if (ob_get_length()) { ob_end_clean(); }

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'.$show_account_name.' - Account Summary.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		exit;
	}
	
	
	public function account_group_ledger(){
		 $company_name  = $this->session->get('ses_company_name');
		 $fy_begndt     = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	     $fy_end        = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	     $from_date     = $_GET['from_date'] ?? '';
		 $to_date       = $_GET['to_date'] ?? '';
		 $group_id      = $_GET['group_id'];
		 $view	        = $_GET['view'];
		 $export        = $_GET['export'];
         $from_date     = validate_fy_from_date($from_date);
         $to_date       = validate_fy_to_date($to_date);
		 $from_date_ymd = date('Y-m-d', strtotime($from_date));
		 $to_date_ymd   = date('Y-m-d', strtotime($to_date));
		
		if($view == 1){
		$result = $this->AccountsModel->load_account_group_ledger_detailed($group_id, -1, $from_date_ymd, $to_date_ymd,1,"");
		}
		else{
			
		$result = $this->AccountsModel->load_account_group_ledger($group_id, -1, $from_date_ymd, $to_date_ymd,1,"");
		}
			
		$spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Account Group Ledger");
		$debit_total=0;
		$credit_total=0;
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:H1');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');

		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:H2');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A2")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A2', 'Account Group Ledger');
        $sheet->setCellValue('A3', 'DATE');
		$sheet->setCellValue('B3', 'TYPE');
		$sheet->setCellValue('C3', 'VCH/BILL NO');
		$sheet->setCellValue('D3', 'ACCOUNT');
		$sheet->setCellValue('E3', 'NARRATION');
        $sheet->setCellValue('F3', 'DEBIT(₹)');	
		$sheet->setCellValue('G3', 'CREDIT(₹)');
		$sheet->setCellValue('H3', 'BALANCE(₹)');		
		$spreadsheet->getActiveSheet()->getStyle("A3:H3")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','H') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}

        $counter = 4;	
		$spreadsheet->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold( true );
		$spreadsheet->getActiveSheet()->getStyle("A2:H2")->getFont()->setBold( true );
		$debit_total=0;
		$credit_total=0;
		foreach($result as $row){
			if($row['debit_total']>0)
				$debit_total=$debit_total+$row['debit_total'];
			if($row['credit_total']>0)
				$credit_total=$credit_total+$row['credit_total'];
			
			$sheet->setCellValue('A'.$counter , $this->clean($row['voucher_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['voucher_type']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['account_name']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['short_narration']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['debit_total']));
			$sheet->setCellValue('G'.$counter , $this->clean($row['credit_total']));
			
			if($row['balance_export'] >0)
			$sheet->setCellValue('H'.$counter , $this->clean(abs($row['balance_export'])));
		    else
			$sheet->setCellValue('H'.$counter , '');	
		    
		    
            $sheet->setCellValue('I'.$counter , $this->clean($row['balance_type']));				

			$spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			$spreadsheet->getActiveSheet()->getStyle('H'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			$counter++;
		}
		
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':E'.$counter);
		$sheet->setCellValue('A'.$counter , 'Total');
		$sheet->setCellValue('F'.$counter , html_entity_decode($this->clean(parseAmount($debit_total))));
		$sheet->setCellValue('G'.$counter , html_entity_decode($this->clean(parseAmount($credit_total))));			
		
        $spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );
        $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );			 
				
		if($export=='excel'){
			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Account Group Ledger.xlsx"');			
		}
	    if($export=='csv'){
			$writer = IOFactory::createWriter($spreadsheet, 'Csv');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="ItemLedger.csv"');			
		}
		$writer->save('php://output');		
		
		die();	
	}
	
	public function daybook(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	    
		 $view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 0;
		 $from_date = $_GET['from_date'] ?? '';
		 $to_date   = $_GET['to_date'] ?? '';

        $from_date     = validate_fy_from_date($from_date);
        $to_date       = validate_fy_to_date($to_date);
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd   = date('Y-m-d', strtotime($to_date));
		$result = array();
		ob_start();
		if($view==0)
		  $result = $this->ReportsModel->load_day_book_condensed(1, 0, $from_date_ymd, $to_date_ymd, $view,'','');
		 if($view==1)
			 $result = $this->ReportsModel->load_day_book_detailed(1, 0, $from_date_ymd, $to_date_ymd, $view,'','');
			 
		 $output = ob_get_contents();
		 ob_end_clean();

		 if($output){
			$result = json_decode($output,true);
            $result = $result['data'];			
		 }
		
		$spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Day Book");
		$debit_total=0;
		$credit_total=0;
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:J1');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
        $sheet->setCellValue('A2', 'DATE');
		$sheet->setCellValue('B2', 'PARTICULARS');
		$sheet->setCellValue('C2', 'VOUCHER TYPE');
		$sheet->setCellValue('D2', 'VOUCHER No');
		$sheet->setCellValue('E2', 'DEBIT(₹)');	
		$sheet->setCellValue('F2', 'CREDIT(₹)');
		$spreadsheet->getActiveSheet()->getStyle("A2:H2")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','F') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
        $counter = 3;	
		$spreadsheet->getActiveSheet()->getStyle("A1:E1")->getFont()->setBold( true );
		
		foreach($result as $row){			
			$sheet->setCellValue('A'.$counter , $this->clean($row['date']));
			$sheet->setCellValue('B'.$counter , $this->clean(html_entity_decode($row['particulars'])));
			$sheet->setCellValue('C'.$counter , $this->clean($row['voucher_type']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['debit_total']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['credit_total']));
			
			 $spreadsheet->getActiveSheet()->getStyle('E'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );
        $spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			 
			$counter++;
		  }
		
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="DayBook.xlsx"');
		$writer->save('php://output');
		die();	
	}
	

	public function item_ledger(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));

		$item_id  = $_GET['item_id'] ?? 0;
		$export_type  = $_GET['export_type'] ?? 'excel';    

		$from_date = $_GET['from_date'] ?? '';
	
	  $to_date = $_GET['to_date'] ?? '';

	  $from_date = validate_from_date($from_date);
	  $to_date = validate_to_date($to_date);

		$unit_id    	= $_GET['unit_id'] ?? 0;
		$mc_id    		= $_GET['mc_id'] ?? 0;
		$mc_grp_id    = $_GET['mc_grp_id'] ?? 0;
		$val_id    		= $_GET['val_id'] ?? 1;	  
		
		$item_info = $this->ItemsModel->get_item_info($item_id,$this->company_id);
		$finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));


		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd = date('Y-m-d', strtotime($to_date));  
		 
		$result = $this->ItemsModel->load_item_ledger_export($item_id, $unit_id, $mc_id, $mc_grp_id, $val_id,$from_date_ymd, $to_date_ymd);

		$spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("ITEM Ledger");
		$debit_total=0;
		$credit_total=0;
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:J1');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
    $sheet->setCellValue('A2', 'DATE');
		$sheet->setCellValue('B2', 'PARTICULARS');
		$sheet->setCellValue('C2', 'VCH TYPE');
		$sheet->setCellValue('D2', 'VCH No.');
		$sheet->setCellValue('E2', 'INWARD');
		$sheet->setCellValue('F2', 'INWARD AMT');
		$sheet->setCellValue('G2', 'OUTWARD');
		$sheet->setCellValue('H2', 'OUTWARD AMT');
    $sheet->setCellValue('I2', 'CLOSING');	
		$sheet->setCellValue('J2', 'UOM');
		$sheet->setCellValue('K2', 'BALANCE');
		$sheet->setCellValue('L2', 'PROFIT');		


		$spreadsheet->getActiveSheet()->getStyle("A2:L2")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','L') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}

        $counter = 3;	
		$spreadsheet->getActiveSheet()->getStyle("A1:L1")->getFont()->setBold( true );
		
		foreach($result as $row){
			
			$sheet->setCellValue('A'.$counter , $this->clean($row['voucher_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['particulars']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['voucher_type']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['inward_qty']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['inward_amount']));
			$sheet->setCellValue('G'.$counter , $this->clean($row['outward_qty']));
			$sheet->setCellValue('H'.$counter , $this->clean($row['outward_amount']));
			$sheet->setCellValue('I'.$counter , $this->clean($row['balance_qty']));
			$sheet->setCellValue('J'.$counter , $this->clean($row['unit_name']));
			$sheet->setCellValue('K'.$counter , $this->clean($row['balance_amount']));
			$sheet->setCellValue('L'.$counter , $this->clean($row['pnl']));

			 $spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			 
			$counter++;
		}
		
		if($export_type=='excel'){
			
			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="ItemLedger.xlsx"'); 
		}
		else if($export_type=='csv'){
			$writer = IOFactory::createWriter($spreadsheet, 'Csv');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="ItemLedger.csv"');
		}
		
		$writer->save('php://output');
		die();	
	}

	public function stock_status(){

		$company_name = $this->session->get('ses_company_name');
		$export_type  = $_GET['export_type'] ?? 'excel';   
		$from_date    = $_GET['from_date'] ?? '';
	    $to_date      = $_GET['to_date'] ?? '';
		$from_date    = validate_from_date($from_date);
		$to_date      = validate_to_date($to_date);
          	
		$mc_id    		= $_GET['mc_id'] ?? 0;
		$val    		= $_GET['val'] ?? 0;
		$inw    		= $_GET['inw'] ?? 0;
		$outw    		= $_GET['outw'] ?? 0;			
		$hsn    		= $_GET['hsn'] ?? 0;
		$opbal    		= $_GET['opbal'] ?? 0;	
		$prft    		= $_GET['prft'] ?? 0;  
		
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd = date('Y-m-d', strtotime($to_date));  
		$isexport=1;
		$result = $this->StockStatusModel->inventoryStatusPaged($isexport);
		
		echo '<pre>';
		print_r($result);

die();

		$spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("STOCK STATUS");
		$debit_total=0;
		$credit_total=0;
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:I1');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.fy_calender()->name.')');
    $sheet->setCellValue('A2', 'ITEM');
		$sheet->setCellValue('B2', 'TYPE');
		$sheet->setCellValue('C2', 'UNIT');
		$sheet->setCellValue('D2', 'OP. QTY');
		$sheet->setCellValue('E2', 'OP. VALUE');
		$sheet->setCellValue('F2', 'QTY');
		$sheet->setCellValue('G2', 'VALUE');
		$sheet->setCellValue('H2', 'METHOD');
		$sheet->setCellValue('I2', 'PROFIT');		


		$spreadsheet->getActiveSheet()->getStyle("A2:G2")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','G') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}

        $counter = 3;	
		$spreadsheet->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold( true );
		
		if(!empty($result['data'])){
			foreach($result['data'] as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['item_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['item_type']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['unit_name']));
				$sheet->setCellValue('D'.$counter , $this->clean($row['op_item_qty']));
				$sheet->setCellValue('E'.$counter , $this->clean($row['op_item_value']));

				$sheet->setCellValue('F'.$counter , $this->clean($row['item_qty']));
				$sheet->setCellValue('G'.$counter , $this->clean($row['item_value']));
				$sheet->setCellValue('H'.$counter , $this->clean($row['method']));
				$sheet->setCellValue('I'.$counter , $this->clean($row['profit_total']));
		

				 $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
				array(
					"horizontal" => "right", 
					"vertical" => "center"
				));
				$spreadsheet->getActiveSheet()->getStyle('E'.$counter)->getAlignment()->applyFromArray(
				array(
					"horizontal" => "right", 
					"vertical" => "center"
				));

				$spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
				array(
					"horizontal" => "right", 
					"vertical" => "center"
				));
				$spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
				array(
					"horizontal" => "right", 
					"vertical" => "center"
				));
				$spreadsheet->getActiveSheet()->getStyle('I'.$counter)->getAlignment()->applyFromArray(
				array(
					"horizontal" => "right", 
					"vertical" => "center"
				)
			     );	
				 
				$counter++;
			}

		}
					
		if($export_type=='excel'){
			
			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="StockStatus.xlsx"'); 
		}
		else if($export_type=='csv'){
			$writer = IOFactory::createWriter($spreadsheet, 'Csv');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="StockStatus.csv"');
		}
		
		$writer->save('php://output');
		die();	
	}

	public function accounts_trial()
{
    $company_name = $this->session->get('ses_company_name');

    // ── Query parameters ──────────────────────────────────────────────────
    $export_type     = $_GET['export_type']     ?? 'excel';
    $group_id        = isset($_GET['group_id'])        && $_GET['group_id']        !== '' ? (int)$_GET['group_id']        : 0;
    $parent          = isset($_GET['parent'])          && $_GET['parent']          !== '' ? (int)$_GET['parent']          : 0;
    $consoview       = isset($_GET['consoview'])       && $_GET['consoview']       !== '' ? (int)$_GET['consoview']       : 0;
    $nilltransaction = isset($_GET['nilltransaction']) && $_GET['nilltransaction'] !== '' ? (int)$_GET['nilltransaction'] : 1;
    $nillbalance     = isset($_GET['nillbalance'])     && $_GET['nillbalance']     !== '' ? (int)$_GET['nillbalance']     : 1;

    // ── Date handling ─────────────────────────────────────────────────────
    $finyear   = $this->CommonModel->calculateFiscalYearForDate(date('m'));
    $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
    $to_date   = !empty($_GET['to_date'])   ? $_GET['to_date']   : $finyear['end_date'];

    $from_date     = validate_from_date($from_date);
    $to_date       = validate_to_date($to_date);
    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd   = date('Y-m-d', strtotime($to_date));

    // ── Detect group / parent (identical to ERP controller) ───────────────
    $group_info      = null;
    $is_parent_group = false;

    if ($parent == 1) {
        $group_info      = $this->AccountsModel->group_parent_info($group_id);
        $is_parent_group = true;
    } else {
        $group_info = $this->AccountsModel->main_group_info($group_id);
        if (empty($group_info)) {
            $maybe_parent = $this->AccountsModel->group_parent_info($group_id);
            if (!empty($maybe_parent)) {
                $group_info      = $maybe_parent;
                $is_parent_group = true;
            }
        }
    }

    // ── If group not found — send empty xlsx ──────────────────────────────
    if (empty($group_info)) {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No data found for the given group_id.');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="AccountsTrialBalance_Empty.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // ── Load data ─────────────────────────────────────────────────────────
    if ($is_parent_group) {
        $group_name = $group_info['acc_grp_parent_name'];
        $result     = $this->AccountsModel->load_accounts_trial_balance_parents(
            $group_id, $from_date_ymd, $to_date_ymd,
            $nillbalance, $nilltransaction, $consoview
        );
    } else {
        $group_name = $group_info['acc_grp_name'];
        $result     = $this->AccountsModel->load_accounts_trial_balance_groups(
            $group_id, $from_date_ymd, $to_date_ymd,
            $nillbalance, $nilltransaction, $consoview
        );
    }

    // ══════════════════════════════════════════════════════════════════════
    //  INLINE SANITISER
    // ══════════════════════════════════════════════════════════════════════
    $sanitiseName = function ($raw): string {
        if ($raw === null || $raw === '') return '';
        $s = (string)$raw;
        $htmlEntities = [
            '&raquo;','&#187;','&#xBB;','&#xbb;',
            '&rsaquo;','&#8250;','&#x203A;',
            '&#8377;','&#x20B9;','&INR;',
            '&nbsp;','&#160;','&#xA0;',
        ];
        $s = str_replace($htmlEntities, '', $s);
        $s = str_replace(['<sub><em>', '</em></sub>'], ' ', $s);
        $s = strip_tags($s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $utf8Symbols = ["\xC2\xBB", '»', "\xE2\x80\xBA", '›', "\xBB", '₹'];
        $s = str_replace($utf8Symbols, '', $s);
        $s = preg_replace('/\s{2,}/', ' ', $s);
        return trim($s);
    };

    // ── Spreadsheet bootstrap ──────────────────────────────────────────────
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Accounts Trial Balance');

    // ── Shared styles ──────────────────────────────────────────────────────
    $style_title = [
        'font'      => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 15],
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
    ];
    $style_subtitle = [
        'font'      => ['bold' => false, 'color' => ['rgb' => '595656'], 'size' => 11],
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
    ];
    $style_header = [
        'font'      => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 12],
        'fill'      => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E8E8E8'],
        ],
    ];
    $style_grp = [
        'font' => ['bold' => true, 'color' => ['rgb' => '1a3a5c'], 'size' => 11],
    ];
    $style_acc = [
        'font' => ['bold' => false, 'color' => ['rgb' => '333333'], 'size' => 11],
    ];
    $style_total = [
        'font'    => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 12],
        'fill'    => [
            'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'F2F2F2'],
        ],
        'borders' => [
            'top'    => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE],
        ],
    ];

    // ── Helper: right-align ────────────────────────────────────────────────
    $alignRight = function (string $cell) use ($spreadsheet): void {
        $spreadsheet->getActiveSheet()->getStyle($cell)->getAlignment()->applyFromArray([
            'horizontal' => 'right',
            'vertical'   => 'center',
        ]);
    };

    // ── Helper: write a numeric cell — blank when zero, number when non-zero
    //   For DEBIT and CREDIT columns we want blank on zero (matches ERP UI)
    //   For BALANCE column we ALWAYS write the value (even 0.00)
    $writeNumericOrBlank = function (string $cell, float $val) use ($sheet): void {
        if ($val == 0.0) {
            $sheet->setCellValue($cell, '');
        } else {
            $sheet->setCellValue($cell, round($val, 2));
        }
    };

    // ── Consolidated: show BRANCH column G ────────────────────────────────
    $has_branch_col = ($consoview == 1);
    $last_data_col  = $has_branch_col ? 'G' : 'F';

    // ── Title row 1 ────────────────────────────────────────────────────────
    $spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:' . $last_data_col . '1');
    $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($style_title);
    $sheet->setCellValue('A1', $company_name . ' (FY: ' . fy_calender()->name . ')');

    // ── Subtitle row 2 ─────────────────────────────────────────────────────
    $spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:' . $last_data_col . '2');
    $spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($style_subtitle);
    $consoLabel = $consoview ? '  |  Consolidated' : '';
    $sheet->setCellValue(
        'A2',
        'Trial Balance — ' . $group_name . '  |  ' . $from_date . ' to ' . $to_date . $consoLabel
    );

    // ── Column headers row 3 ───────────────────────────────────────────────
    $sheet->setCellValue('A3', 'ACCOUNT');
    $sheet->setCellValue('B3', 'OP. BALANCE');
    $sheet->setCellValue('C3', 'DEBIT (₹)');
    $sheet->setCellValue('D3', 'CREDIT (₹)');
    $sheet->setCellValue('E3', 'BALANCE (₹)');
    $sheet->setCellValue('F3', 'TYPE');
    if ($has_branch_col) {
        $sheet->setCellValue('G3', 'BRANCH');
    }
    $spreadsheet->getActiveSheet()
        ->getStyle('A3:' . $last_data_col . '3')
        ->applyFromArray($style_header);

    // ── Column widths ──────────────────────────────────────────────────────
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(35);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);
    if ($has_branch_col) {
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    }

    // ── Running totals (account rows only — avoid double-counting groups) ──
    $total_op_bal  = 0.0;
    $total_debit   = 0.0;
    $total_credit  = 0.0;
    $total_balance = 0.0; // net closing balance for total row

    $counter = 4;

    // ══════════════════════════════════════════════════════════════════════
    //  DATA LOOP
    //
    //  Model key reference:
    //    entity_type      → 'grp' | 'acc'
    //    entity_name      → display name
    //    op_balance_total → numeric opening balance (SIGNED: +DR / -CR)
    //    debit_total      → numeric debit for period
    //    credit_total     → numeric credit for period
    //    balance_total    → numeric closing balance (SIGNED: +DR / -CR)
    //                       = op_balance_total + debit_total - credit_total
    //    balance_type     → 'DR' | 'CR' | ''
    //    branch_name      → only when consoview=1
    // ══════════════════════════════════════════════════════════════════════
    foreach ($result as $row) {

        $entity_type = trim((string)($row['entity_type'] ?? 'acc'));

        // ── Name with indentation ──────────────────────────────────────────
        $raw_name     = $sanitiseName($row['entity_name'] ?? '');
        $display_name = ($entity_type === 'acc') ? ('      ' . $raw_name) : $raw_name;

        // ── Read numeric values from model ─────────────────────────────────
        // op_balance_total: signed (negative = CR opening)
        $op_bal_num  = (float)($row['op_balance_total'] ?? 0.0);

        // debit_total / credit_total: always positive amounts
        $debit_num   = (float)($row['debit_total']      ?? 0.0);
        $credit_num  = (float)($row['credit_total']     ?? 0.0);

        // balance_total: SIGNED closing balance
        // Positive  = DR balance
        // Negative  = CR balance
        $bal_signed  = (float)($row['balance_total']    ?? 0.0);

        // balance_type from model: 'DR' or 'CR' (may be empty for zero)
        $bal_type    = trim((string)($row['balance_type'] ?? ''));

        // ── Recalculate balance_type if model left it empty ────────────────
        if ($bal_type === '' && $bal_signed != 0.0) {
            $bal_type = ($bal_signed < 0) ? 'CR' : 'DR';
        }

        // ── Accumulate totals (account rows only) ─────────────────────────
        if ($entity_type === 'acc') {
            $total_op_bal  += $op_bal_num;
            $total_debit   += $debit_num;
            $total_credit  += $credit_num;
            $total_balance += $bal_signed;
        }

        // ── Apply row style ────────────────────────────────────────────────
        $style_range = 'A' . $counter . ':' . $last_data_col . $counter;
        if ($entity_type === 'grp') {
            $spreadsheet->getActiveSheet()->getStyle($style_range)->applyFromArray($style_grp);
        } else {
            $spreadsheet->getActiveSheet()->getStyle($style_range)->applyFromArray($style_acc);
        }

        // ── Column A: Account / Group name ──────────────��─────────────────
        $sheet->setCellValue('A' . $counter, $display_name);

        // ── Column B: Opening balance (signed numeric) ────────────────────
        // Write signed value so Excel shows negative for CR opening
        // (matches ERP display: ₹62,64,705.95 CR shown as negative in export)
        if ($op_bal_num == 0.0) {
            $sheet->setCellValue('B' . $counter, '');
        } else {
            $sheet->setCellValue('B' . $counter, round($op_bal_num, 2));
        }

        // ── Column C: Debit (blank when zero — matches ERP UI) ────────────
        $writeNumericOrBlank('C' . $counter, $debit_num);

        // ── Column D: Credit (blank when zero — matches ERP UI) ───────────
        $writeNumericOrBlank('D' . $counter, $credit_num);

        // ── Column E: Balance — ALWAYS write abs value, never blank ────────
        // This was the bug: balance was being written as empty when it
        // should always show the closing balance amount.
        //
        // The model's balance_total is SIGNED (+DR / -CR).
        // We display the ABSOLUTE value here; TYPE column (F) shows DR/CR.
        $bal_abs = round(abs($bal_signed), 2);
        $sheet->setCellValue('E' . $counter, $bal_abs);  // always write, even if 0.00

        // ── Column F: Type (DR / CR) ──────────────────────────────────────
        $sheet->setCellValue('F' . $counter, $bal_type);

        // ── Column G: Branch (consolidated only) ──────────────────────────
        if ($has_branch_col) {
            $sheet->setCellValue('G' . $counter, $sanitiseName($row['branch_name'] ?? ''));
        }

        // ── Alignment ─────────────────────────────────────────────────────
        $alignRight('B' . $counter);
        $alignRight('C' . $counter);
        $alignRight('D' . $counter);
        $alignRight('E' . $counter);

        $counter++;
    }

    // ── Total row ──────────────────────────────────────────────────────────
    $total_bal_abs  = round(abs($total_balance), 2);
    $total_bal_type = ($total_balance == 0.0) ? '' : (($total_balance < 0) ? 'CR' : 'DR');

    $sheet->setCellValue('A' . $counter, 'TOTAL');

    // OP BALANCE total (signed)
    if ($total_op_bal == 0.0) {
        $sheet->setCellValue('B' . $counter, '');
    } else {
        $sheet->setCellValue('B' . $counter, round($total_op_bal, 2));
    }

    $writeNumericOrBlank('C' . $counter, $total_debit);
    $writeNumericOrBlank('D' . $counter, $total_credit);

    $sheet->setCellValue('E' . $counter, $total_bal_abs);
    $sheet->setCellValue('F' . $counter, $total_bal_type);

    $spreadsheet->getActiveSheet()
        ->getStyle('A' . $counter . ':' . $last_data_col . $counter)
        ->applyFromArray($style_total);
    $alignRight('B' . $counter);
    $alignRight('C' . $counter);
    $alignRight('D' . $counter);
    $alignRight('E' . $counter);

    // ── Page footer ────────────────────────────────────────────────────────
    $spreadsheet->getActiveSheet()->getHeaderFooter()->setOddFooter('&R&F Page &P / &N');
    $spreadsheet->getActiveSheet()->getHeaderFooter()->setEvenFooter('&R&F Page &P / &N');

    // ── Flush stray output ─────────────────────────────────────────────────
    if (ob_get_length()) {
        ob_end_clean();
    }

    // ── Filename ───────────────────────────────────────────────────────────
    $safe_group_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $group_name);
    $filename        = 'AccountsTrial_' . $safe_group_name;

    // ── Send ──────────────────────────────────────────────────────────────
    if ($export_type === 'csv') {
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '.csv"');
    } else {
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
    }
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}
    public function sale_register(){
	  $company_name  = $this->session->get('ses_company_name');
	  $fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	  $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	
	  $export_type   = $_GET['export_type'] ?? 'excel';    
	  $from_date     = $_GET['from_date'] ?? '';
	  $to_date       = $_GET['to_date'] ?? '';	 
	  $view          = $_GET['view'];
	  $from_date_ymd = date('Y-m-d', strtotime($from_date));
	  $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
	  $response= array();
	if($view == 1){
		$excel_title = 'Sales Register - Outward Supplies (Sales)';
    	$response = $this->RegistersModel->load_sale_register_export($from_date_ymd, $to_date_ymd, $view);
    }else if($view == 2){
		$excel_title = 'Sales Register - Outward Supplies (Sales Due)';
    	$response = $this->RegistersModel->load_sale_due_register_export($from_date_ymd, $to_date_ymd, $view);
    }else if($view == 3){
		$excel_title = 'Sales Register - Outward Supplies (Expenses)';
    	$response = $this->RegistersModel->load_sale_outward_supplies_expenses_register_export($from_date_ymd, $to_date_ymd, $view);
    }else if($view == 4){
		$excel_title = 'Sales Register - Outward Supplies (Assets)';
    	$response = $this->RegistersModel->load_sale_outward_supplies_assets_register_export($from_date_ymd, $to_date_ymd, $view);
    }else if($view == 5){
		$excel_title = 'Sales Register - Outward Supplies (Other Revenue)';
    	$response = $this->RegistersModel->load_sale_outward_supplies_revenue_register_export($from_date_ymd, $to_date_ymd, $view);
    }else if($view == 6){
		$excel_title = 'Sales Register - All Outward Supplies';
    	$response = $this->RegistersModel->load_sale_condensed_register_export($from_date_ymd, $to_date_ymd, $view);
    } 
	
	$spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Register-OS');
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:H1');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');

		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:H2');
		$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						)));
		$spreadsheet->getActiveSheet()->getStyle("A2")->getAlignment()->applyFromArray(
			array(
				"horizontal" => "center", 
				"vertical" => "center"
			)
		);	
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));	
		 $counter = 4;	
		$spreadsheet->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold( true );
		$spreadsheet->getActiveSheet()->getStyle("A2:H2")->getFont()->setBold( true );
		
		$sheet->setCellValue('A2', $excel_title);
        $sheet->setCellValue('A3', 'DATE');
		$sheet->setCellValue('B3', 'VOUCHER NO');
		$sheet->setCellValue('C3', 'BILL NO');
		$sheet->setCellValue('D3', 'ACCOUNT');
		$sheet->setCellValue('E3', 'NARRATION');
        $sheet->setCellValue('F3', 'GSTIN');	
		$sheet->setCellValue('G3', 'MATERIAL CENTRE');
		$sheet->setCellValue('H3', 'AMOUNT(₹)');		
		$spreadsheet->getActiveSheet()->getStyle("A3:H3")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','H') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		$total_amount=0;
		foreach($response as $row){
			$total_amount =$total_amount+$this->clean($row['amount']);
			$sheet->setCellValue('A'.$counter , $this->clean($row['voucher_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['bill_no']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['account_name']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['narration']));
			$sheet->setCellValue('F'.$counter , "");
			$sheet->setCellValue('G'.$counter , $this->clean($row['mc_name']));
			$sheet->setCellValue('H'.$counter , $this->clean(abs($row['amount'])));
			$sheet->setCellValue('I'.$counter , $this->clean($row['balance_type']));

		
		$counter++;			
		}
		$final_crdr='DR';			
      	if($total_amount<0)
          $final_crdr='CR';			
	  
	    if($response){
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':E'.$counter);
		$sheet->setCellValue('A'.$counter , 'Total');
		$sheet->setCellValue('H'.$counter , html_entity_decode($this->clean(parseAmount(abs($total_amount)))));			
		$sheet->setCellValue('I'.$counter , html_entity_decode($final_crdr));			
		
        
        $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	}		
	if($export_type=='excel'){	
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="SaleRegister.xlsx"');
		$writer->save('php://output');
		die();			
	}
	else if($export_type=='csv'){
			$writer = IOFactory::createWriter($spreadsheet, 'Csv');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="SaleRegister.csv"');
			$writer->save('php://output');
		die();	
		}
  } 
  
    public function sale_return_register(){
	  $company_name  = $this->session->get('ses_company_name');
	  $fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	  $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	
	  $export_type   = $_GET['export_type'] ?? 'excel';    
	  $from_date     = $_GET['from_date'] ?? '';
	  $to_date       = $_GET['to_date'] ?? '';	 
	  $view          = $_GET['view'];
	  $from_date_ymd = date('Y-m-d', strtotime($from_date));
	  $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
	  $response= array();
	  // Define a mapping of view values to titles and corresponding methods
	$viewConfig = [
		1 => ['title' => 'Sales Return Register - Sale Return', 'method' => 'load_sale_return_register'],
		2 => ['title' => 'Sales Return Register - Outward Return (Expenses)', 'method' => 'load_sale_return_outward_supplies_expenses_register'],
		3 => ['title' => 'Sales Return Register - Outward Return (Assets)', 'method' => 'load_sale_return_outward_supplies_assets_register'],
		4 => ['title' => 'Sales Return Register - Condensed', 'method' => 'load_sale_return_condensed_register']		
	];
	
	if (array_key_exists($view, $viewConfig)) {
		$excel_title = $viewConfig[$view]['title'];
		$response = $this->RegistersModel->{$viewConfig[$view]['method']}(1,0, $from_date_ymd, $to_date_ymd, $view,'',1);
	} else {
		$response = array();
		
	}
	    $spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Return Register');
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:H1');
		$styleArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 15
			),
			'alignment' => array(
				'horizontal' => 'center',
				'vertical'   => 'center'
			)
		);
		$counter=4;
		$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:G2');
		$spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
		
		$sheet->setCellValue('A2', $excel_title);
        $sheet->setCellValue('A3', 'DATE');
		$sheet->setCellValue('B3', 'VOUCHER NO');
		$sheet->setCellValue('C3', 'BILL NO');
		$sheet->setCellValue('D3', 'ACCOUNT');
		$sheet->setCellValue('E3', 'GSTIN');	
		$sheet->setCellValue('F3', 'MATERIAL CENTRE');
		$sheet->setCellValue('G3', 'AMOUNT(₹)');		
		$spreadsheet->getActiveSheet()->getStyle("A3:G3")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','H') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		$total_amount=0;
		foreach($response['data'] as $row){
			if($row['amount_total']<0)
				$balance_type='CR';
			else 
			   $balance_type='DR'; 	
			
			$total_amount =$total_amount+$this->clean($row['amount_total']);
			$sheet->setCellValue('A'.$counter , $this->clean($row['voucher_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['bill_no']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['account_name']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['party_gst']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['mc_name']));
			$sheet->setCellValue('G'.$counter , $this->clean(abs($row['amount_total'])));
			$sheet->setCellValue('H'.$counter , $this->clean($balance_type));
		
		$counter++;			
		}
		$final_crdr='DR';			
      	if($total_amount<0)
          $final_crdr='CR';			
	  
	    if($response){
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':G'.$counter);
		$sheet->setCellValue('A'.$counter , 'Total');
		$sheet->setCellValue('G'.$counter , html_entity_decode($this->clean(parseAmount(abs($total_amount)))));			
		$sheet->setCellValue('H'.$counter , html_entity_decode($final_crdr));			
		
        
        $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	}		
	if($export_type=='excel'){	
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="SaleReturnRegister.xlsx"');
		$writer->save('php://output');
		die();			
	}
	
  }
  
    public function purchase_register(){
	  $company_name  = $this->session->get('ses_company_name');
	  $fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	  $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	
	  $export_type   = $_GET['export_type'] ?? 'excel';    
	  $from_date     = $_GET['from_date'] ?? '';
	  $to_date       = $_GET['to_date'] ?? '';	 
	  $view          = $_GET['view'];
	  $from_date_ymd = date('Y-m-d', strtotime($from_date));
	  $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
	  $response= array();
	  $viewConfig = [
			1 => ['title' => 'Purchase Register - Purchase', 'method' => 'load_purchase_register'],
			2 => ['title' => 'Purchase Register - Purchase Due', 'method' => 'load_purchase_due_register'],
			3 => ['title' => 'Purchase Register - Inward Supplies (Expenses)', 'method' => 'load_purchase_inward_supplies_expenses_register'],
			4 => ['title' => 'Purchase Register - Inward Supplies (Assets)', 'method' => 'load_purchase_inward_supplies_assets_register'],
			5 => ['title' => 'Purchase Register - Condensed', 'method' => 'load_purchase_condensed_register']
		];
	if (array_key_exists($view, $viewConfig)) {	
		$excel_title = $viewConfig[$view]['title'];
		$response = $this->RegistersModel->{$viewConfig[$view]['method']}(1,0, $from_date_ymd, $to_date_ymd, $view, [],1);
	} else {
	
		$response = array();
	}	
	    $spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Purchase Register');
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:G1');
		$styleArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 15
			),
			'alignment' => array(
				'horizontal' => 'center',
				'vertical'   => 'center'
			)
		);
		$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:G2');
		$spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
		$counter=4;
		$sheet->setCellValue('A2', $excel_title);
        $sheet->setCellValue('A3', 'DATE');
		$sheet->setCellValue('B3', 'VOUCHER NO');
		$sheet->setCellValue('C3', 'BILL NO');
		$sheet->setCellValue('D3', 'ACCOUNT');
        $sheet->setCellValue('E3', 'GSTIN');	
		$sheet->setCellValue('F3', 'MATERIAL CENTRE');
		$sheet->setCellValue('G3', 'AMOUNT(₹)');		
		$spreadsheet->getActiveSheet()->getStyle("A3:G3")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','G') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		$total_amount=0;
		foreach($response['data'] as $row){
			if($row['amount_total']<0)
				$balance_type='CR';
			else 
			   $balance_type='DR'; 	
			
			$total_amount =$total_amount+$this->clean($row['amount_total']);
			$sheet->setCellValue('A'.$counter , $this->clean($row['voucher_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['bill_no']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['account_name']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['party_gst']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['mc_name']));
			$sheet->setCellValue('G'.$counter , $this->clean(abs($row['amount_total'])));
			$sheet->setCellValue('H'.$counter , $this->clean($balance_type));

		
		$counter++;			
		}
		$final_crdr='DR';			
      	if($total_amount<0)
          $final_crdr='CR';			
	  
	    if($response){
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':F'.$counter);
		$sheet->setCellValue('A'.$counter , 'Total');
		$sheet->setCellValue('G'.$counter , html_entity_decode($this->clean(parseAmount(abs($total_amount)))));			
		$sheet->setCellValue('H'.$counter , html_entity_decode($final_crdr));			
		
        
        $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	}		
	if($export_type=='excel'){	
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="PurchaseRegister.xlsx"');
		$writer->save('php://output');
		die();			
	}
	
  }
    
	public function purchase_return_register(){
	  $company_name  = $this->session->get('ses_company_name');
	  $fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	  $fy_end    = date('Y', strtotime($this->session->get('ses_company_fy_beginning'). ' + 1 year'));
	
	  $export_type   = $_GET['export_type'] ?? 'excel';    
	  $from_date     = $_GET['from_date'] ?? '';
	  $to_date       = $_GET['to_date'] ?? '';	 
	  $view          = $_GET['view'];
	  $from_date_ymd = date('Y-m-d', strtotime($from_date));
	  $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
	  $response= array();
	  $viewConfig = [
			1 => ['title' => 'Purchase Return Register - Purchase Return', 'method' => 'load_purchase_return_register'],
			2 => ['title' => 'Purchase Return Register - Inward Return (Expenses)', 'method' => 'load_purchase_return_inward_supplies_expenses_register'],
			3 => ['title' => 'Purchase Return Register - Inward Return (Assets)', 'method' => 'load_purchase_return_inward_supplies_assets_register'],
			4 => ['title' => 'Purchase Return Register - Condensed', 'method' => 'load_purchase_return_condensed_register']
			
		];
	if (array_key_exists($view, $viewConfig)) {	
		$excel_title = $viewConfig[$view]['title'];
		$response = $this->RegistersModel->{$viewConfig[$view]['method']}(1,0, $from_date_ymd, $to_date_ymd, $view, [],1);
	} else {
	
		$response = array();
	}	
	    $spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Purchase Register');
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:G1');
		$styleArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 15
			),
			'alignment' => array(
				'horizontal' => 'center',
				'vertical'   => 'center'
			)
		);
		$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:G2');
		$spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
		$counter=4;
		$sheet->setCellValue('A2', $excel_title);
        $sheet->setCellValue('A3', 'DATE');
		$sheet->setCellValue('B3', 'VOUCHER NO');
		$sheet->setCellValue('C3', 'BILL NO');
		$sheet->setCellValue('D3', 'ACCOUNT');
        $sheet->setCellValue('E3', 'GSTIN');	
		$sheet->setCellValue('F3', 'MATERIAL CENTRE');
		$sheet->setCellValue('G3', 'AMOUNT(₹)');		
		$spreadsheet->getActiveSheet()->getStyle("A3:G3")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','G') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		$total_amount=0;
		foreach($response['data'] as $row){
			if($row['amount_total']<0)
				$balance_type='CR';
			else 
			   $balance_type='DR'; 	
			
			$total_amount =$total_amount+$this->clean($row['amount_total']);
			$sheet->setCellValue('A'.$counter , $this->clean($row['voucher_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['bill_no']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['account_name']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['party_gst']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['mc_name']));
			$sheet->setCellValue('G'.$counter , $this->clean(abs($row['amount_total'])));
			$sheet->setCellValue('H'.$counter , $this->clean($balance_type));

		
		$counter++;			
		}
		$final_crdr='DR';			
      	if($total_amount<0)
          $final_crdr='CR';			
	  
	    if($response){
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':F'.$counter);
		$sheet->setCellValue('A'.$counter , 'Total');
		$sheet->setCellValue('G'.$counter , html_entity_decode($this->clean(parseAmount(abs($total_amount)))));			
		$sheet->setCellValue('H'.$counter , html_entity_decode($final_crdr));			
		
        
        $spreadsheet->getActiveSheet()->getStyle('G'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	}		
	if($export_type=='excel'){	
	    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="PurchaseReturnRegister.xlsx"');
		$writer->save('php://output');
		die();			
	}
	
  }

    
  function formatIndianCurrency($amount) {
    $amount = number_format((float)$amount, 2, '.', '');
    $parts = explode('.', $amount);
    $intPart = $parts[0];
    $decimal = $parts[1];

    $lastThree = substr($intPart, -3);
    $restUnits = substr($intPart, 0, -3);

    if ($restUnits != '') {
        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
        $intPart = $restUnits . "," . $lastThree;
    }

    return '₹' . $intPart . '.' . $decimal;
}

}