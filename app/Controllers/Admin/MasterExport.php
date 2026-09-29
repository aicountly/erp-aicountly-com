<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\MaterialCentersModel;
use App\Models\Admin\BillofmaterialModel;
use App\Models\Admin\CostCentreModel;
use App\Models\Admin\BillsundryModel;
use App\Models\Admin\UnitsModel;
use App\Models\Admin\VoucherSeriesModel;

use App\Controllers\BaseController;
use App\Libraries\auth_session;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use TCPDF;

class MasterExport extends BaseController{
  function __construct(){  
		helper(['form', 'url','text']);
		$this->AccountsModel        = new AccountsModel();
		$this->ItemsModel           = new ItemsModel();
		$this->MaterialCentersModel = new MaterialCentersModel();
		$this->BillofmaterialModel  = new BillofmaterialModel();
		$this->CostCentreModel      = new CostCentreModel();
		$this->BillsundryModel      = new BillsundryModel();
		$this->UnitsModel           = new UnitsModel();
		$this->VoucherSeriesModel   = new VoucherSeriesModel();
		$this->CommonModel          = new CommonModel();	
		$this->auth_session    	    = new auth_session();
		$this->auth_session->user_restrict();
		$this->auth_session->is_company_opened();
		$this->auth_session->role_restrict('CS');
		$this->base_url       = base_url().'/'.getenv('AdminPath');
		$this->folder_path    = getenv('AdminPath');
		$this->session    	  = \Config\Services::session();		
		$this->company_id     =  $this->session->get('ses_company_id');
		$this->comp_code      =  $this->session->get('ses_company_code');		
		$this->ses_comp_fy_id =  $this->session->get('ses_comp_fy_id');		
    }
	
	function clean($str){ 
		$str = strip_tags($str);
		$str = utf8_decode($str);		
		$str = str_replace('&raquo;', '»',$str);
		$str = str_replace('&#8377;', '',$str);
		$str = str_replace('&nbsp;', '',$str);
		$str = str_replace('<sub><em>', ' ',$str);
		$str = str_replace('</em></sub>', ' ',$str);
		$str = trim($str);
		return $str;
	}

    function fetchImageFromUrl($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // handle redirects
		$data = curl_exec($ch);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);

		return [
			'data' => $data,
			'mime' => $contentType
		];
	}
	public function accounts(){
		ob_start(); 
		$final_result       = $this->AccountsModel->all_accounts_export(1);
	
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt    = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		$fy_end       = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
		
		if(isset($_GET['type']) && ($_GET['type']=='csv' || $_GET['type']=='excel')){
		$spreadsheet  = new Spreadsheet();      
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Account Master");
		// Merge and format header
		$sheet->mergeCells('A1:F1');
		
		$sheet->setCellValue('A1', $company_name . ' (' . $fy_begndt . ' - ' . $fy_end . ')');
		$sheet->getStyle("A1")->applyFromArray([
			'font' => [
				'bold'  => true,
				'color' => ['rgb' => '000000'],
				'size'  => 15
			],
			'alignment' => [
				'horizontal' => 'center',
				'vertical'   => 'center'
			]
	   	  ]);

		// Set table headers
		$headers = ['ACCOUNT NAME', 'VENDOR CODE', 'GROUP', 'STATUS', 'BALANCE', 'DR/CR'];
		$col = 'A';
		foreach ($headers as $header) {
			$sheet->setCellValue($col . '2', $header);
			$sheet->getStyle($col . '2')->applyFromArray([
				'font' => [
					'bold' => true,
					'color' => ['rgb' => '000000'],
					'size' => 13
				]
			]);
			$sheet->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}

		// Fill data
		$rowIndex = 3;
		if(isset($final_result)){
			foreach ($final_result as $row) {
				$sheet->setCellValue('A' . $rowIndex, $this->clean($row['account_name']));
				$sheet->setCellValue('B' . $rowIndex, '');
				$sheet->setCellValue('C' . $rowIndex, $this->clean($row['group_name']));
				$sheet->setCellValue('D' . $rowIndex, $this->clean($row['acc_status']));
				$sheet->setCellValue('E' . $rowIndex, $row['op_bal_export']);

				// Align STATUS column
				$sheet->getStyle('D' . $rowIndex)->getAlignment()->applyFromArray([
					'horizontal' => 'right',
					'vertical'   => 'center'
				]);

				$rowIndex++;
			}
		 }
		}
		// ================= ACCOUNT MASTER EXCEL / CSV ===================
if(isset($_GET['type']) && ($_GET['type']=='csv' || $_GET['type']=='excel'))
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Account Master");

    $sheet->mergeCells('A1:S1');

    $sheet->setCellValue(
        'A1',
        $company_name.' ('.$fy_begndt.'-'.$fy_end.')'
    );

    $sheet->getStyle('A1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 15
        ],
        'alignment' => [
            'horizontal' => 'center',
            'vertical'   => 'center'
        ]
    ]);

    $headers = [
        'ACCOUNT NAME',
        'VENDOR CODE',
        'GROUP NAME',
        'STATUS',
        'OPENING BALANCE',
        'DR/CR',
        'ALIAS',
        'PRINT NAME',
        'CONTACT PERSON',
        'MOBILE',
        'PHONE',
        'EMAIL',
        'ADDRESS',
        'CITY',
        'STATE',
        'PIN CODE',
        'COUNTRY',
        'GSTIN',
        'PAN'
    ];

    $col = 'A';
    foreach($headers as $header)
    {
        $sheet->setCellValue($col.'2', $header);

        $sheet->getStyle($col.'2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ]);

        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    $rowIndex = 3;

    if(isset($final_result))
    {
        foreach($final_result as $row)
        {
            $address = trim(
                ($row['acc_addr1'] ?? '') .
                ' ' .
                ($row['acc_addr2'] ?? '')
            );

            $sheet->setCellValue('A'.$rowIndex, $this->clean($row['account_name'] ?? ''));
            $sheet->setCellValue('B'.$rowIndex, $this->clean($row['vendor_code'] ?? ''));
            $sheet->setCellValue('C'.$rowIndex, $this->clean($row['group_name'] ?? ''));
            $sheet->setCellValue('D'.$rowIndex, $this->clean($row['acc_status'] ?? ''));
            $sheet->setCellValue('E'.$rowIndex, $row['op_bal_export'] ?? '');
            $sheet->setCellValue('F'.$rowIndex, '');

            $sheet->setCellValue('G'.$rowIndex, $this->clean($row['acc_alias'] ?? ''));
            $sheet->setCellValue('H'.$rowIndex, $this->clean($row['acc_print_name'] ?? ''));
            $sheet->setCellValue('I'.$rowIndex, $this->clean($row['contact_name'] ?? ''));

            $sheet->setCellValue('J'.$rowIndex, $this->clean($row['acc_mobile'] ?? ''));
            $sheet->setCellValue('K'.$rowIndex, $this->clean($row['acc_phone'] ?? ''));
            $sheet->setCellValue('L'.$rowIndex, $this->clean($row['acc_email'] ?? ''));

            $sheet->setCellValue('M'.$rowIndex, $this->clean($address));

            $sheet->setCellValue('N'.$rowIndex, $this->clean($row['city_name'] ?? ''));
            $sheet->setCellValue('O'.$rowIndex, $this->clean($row['state_name'] ?? ''));
            $sheet->setCellValue('P'.$rowIndex, $this->clean($row['acc_pin'] ?? ''));
            $sheet->setCellValue('Q'.$rowIndex, $this->clean($row['country_name'] ?? ''));

            $sheet->setCellValue('R'.$rowIndex, $this->clean($row['acc_gstin'] ?? ''));
            $sheet->setCellValue('S'.$rowIndex, $this->clean($row['acc_pan'] ?? ''));

            $rowIndex++;
        }
    }
}



		
	if(isset($_GET['type']) && $_GET['type'] == 'csv')
{
  
    $writer = IOFactory::createWriter($spreadsheet, 'Csv');
    $writer->setDelimiter(',');
    $writer->setEnclosure('"');

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Account Master.csv"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}
		if(isset($_GET['type']) && $_GET['type'] == 'excel')
{
  
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Account Master.xlsx"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}	  
		} 

	public function account_groups()
{
    $result = $this->AccountsModel->all_account_groups_export();

    $company_name = $this->session->get('ses_company_name');
    $fy_begndt = date('Y', strtotime($this->session->get('ses_company_fy_beginning')));
    $fy_end = date('Y', strtotime(date($fy_begndt . '-m-d') . ' + 1 year'));

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Account Groups");

    // Heading
    $sheet->mergeCells('A1:F1');

    $sheet->getStyle("A1")->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'],
            'size' => 15
        ]
    ]);

    $sheet->getStyle("A1")->getAlignment()->applyFromArray([
        "horizontal" => "center",
        "vertical"   => "center"
    ]);

    $sheet->setCellValue('A1', $company_name . ' (' . $fy_begndt . '-' . $fy_end . ')');

    // Column Headers
    $sheet->setCellValue('A2', 'Group ID');
    $sheet->setCellValue('B2', 'Group Name');
    $sheet->setCellValue('C2', 'Print Name');
    $sheet->setCellValue('D2', 'Primary');
    $sheet->setCellValue('E2', 'Under');
    $sheet->setCellValue('F2', 'Status');

    $sheet->getStyle("A2:F2")->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => '000000'],
            'size' => 13
        ]
    ]);

    foreach (range('A', 'F') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 3;

    foreach ($result as $row) {

        $sheet->setCellValue('A' . $counter, $row['group_id']);
        $sheet->setCellValue('B' . $counter, $this->clean($row['group_name']));
        $sheet->setCellValue('C' . $counter, $this->clean($row['print_name']));
        $sheet->setCellValue('D' . $counter, $this->clean($row['primary']));
        $sheet->setCellValue('E' . $counter, $this->clean($row['under']));
        $sheet->setCellValue('F' . $counter, $this->clean($row['grp_status']));

        $counter++;
    }

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Account Groups.xlsx"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}

		public function bill_sundry()
		{
			$result = $this->BillsundryModel->all_bsd_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Bill Sundry");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:F1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Name');
			$sheet->setCellValue('B2', 'Group');
			$sheet->setCellValue('C2', 'Type');
			$sheet->setCellValue('D2', 'Nature');
			$sheet->setCellValue('E2', 'Op. Balance');
			$sheet->setCellValue('F2', 'Dr/Cr');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:F2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','F') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['billsndry_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['billsundry_group_name']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['billsndry_type']));
				$sheet->setCellValue('D'.$counter , $this->clean($row['billsndry_nature']));
				$sheet->setCellValue('E'.$counter , $this->clean($row['bsd_op_bal']));
				$sheet->setCellValue('F'.$counter , $this->clean($row['bsd_op_bal_drcr']));			

				$spreadsheet->getActiveSheet()->getStyle('E'.$counter)->getAlignment()->applyFromArray(
					array(
						"horizontal" => "right", 
						"vertical" 	 => "center"
					)
				);	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Bill Sundry.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function items()
{
    $result = $this->ItemsModel->all_items_export();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Items Export');

    $headers = [
        'Item Name',
        'SKU',
        'UPC',
        'Alias Name',
        'Print Name',
        'Sales Account',
        'Purchase Account',
        'Item Group',
        'Item Category',
        'Unit',
        'Valuation Method',
        'Opening Qty',
        'Opening Value',
        'Opening Rate',
        'Conversion',
        'MC Qty Wise',
        'Tax Category',
        'HSN/SAC',
        'Status'
    ];

    $col = 'A';

    foreach ($headers as $header)
    {
        $sheet->setCellValue($col.'1', $header);

        $sheet->getStyle($col.'1')
            ->getFont()
            ->setBold(true);

        $sheet->getColumnDimension($col)
            ->setAutoSize(true);

        $col++;
    }

    $rowNo = 2;

    foreach ($result as $row)
    {
        $openingQty =
            (float)($row['itm_op_bal_qty'] ?? 0);

        $openingValue =
            (float)($row['itm_op_val_amt'] ?? 0);

        $openingRate = 0;

        if ($openingQty > 0)
        {
            $openingRate =
                round(
                    $openingValue / $openingQty,
                    2
                );
        }

        $valuationMethod = '';

        switch (strtoupper((string)$row['itm_val_method_id']))
        {
            case 'FIFO':
            case '2':
                $valuationMethod = 'FIFO';
                break;

            case 'LIFO':
            case '3':
                $valuationMethod = 'LIFO';
                break;

            default:
                $valuationMethod = 'AVG';
        }

        $sheet->setCellValue('A'.$rowNo, $row['itm_name']);
        $sheet->setCellValue('B'.$rowNo, $row['itm_sku']);
        $sheet->setCellValue('C'.$rowNo, $row['itm_upc']);
        $sheet->setCellValue('D'.$rowNo, $row['itm_alias']);
        $sheet->setCellValue('E'.$rowNo, $row['itm_print_name']);

        $sheet->setCellValue(
            'F'.$rowNo,
            $row['sales_acc_name']
        );

        $sheet->setCellValue(
            'G'.$rowNo,
            $row['purchase_acc_name']
        );

        $sheet->setCellValue(
            'H'.$rowNo,
            $row['itm_grp_name']
        );

        $sheet->setCellValue(
            'I'.$rowNo,
            $row['itm_cat_name']
        );

        $sheet->setCellValue(
            'J'.$rowNo,
            $row['itm_unit_name']
        );

        $sheet->setCellValue(
            'K'.$rowNo,
            $valuationMethod
        );

        $sheet->setCellValue(
            'L'.$rowNo,
            $openingQty
        );

        $sheet->setCellValue(
            'M'.$rowNo,
            $openingValue
        );

        $sheet->setCellValue(
            'N'.$rowNo,
            $openingRate
        );

        $sheet->setCellValue(
            'O'.$rowNo,
            1
        );

        $sheet->setCellValue(
            'P'.$rowNo,
            'No'
        );

        $sheet->setCellValue(
            'Q'.$rowNo,
            $row['tax_cat_name']
        );

        $sheet->setCellValue(
            'R'.$rowNo,
            $row['itm_hsn']
        );

        $sheet->setCellValue(
            'S'.$rowNo,
            ($row['crs_is_active'] == 1)
                ? 'ACTIVE'
                : 'INACTIVE'
        );

        $rowNo++;
    }

    $filename = 'Items_Master_Export.xlsx';

    header(
        'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    header(
        'Content-Disposition: attachment; filename="'.$filename.'"'
    );

    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter(
        $spreadsheet,
        'Xlsx'
    );

    $writer->save('php://output');
    exit;
}



		public function item_groups()
		{
			$result = $this->ItemsModel->all_item_group_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Item Groups");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:C1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Group Name');
			$sheet->setCellValue('B2', 'Alias Name');
			$sheet->setCellValue('C2', 'Primary');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:C2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','D') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['item_grp_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['item_grp_alias']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['item_grp_primary']));	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Item Groups.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function item_category()
		{
			$result = $this->ItemsModel->all_item_category_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Item Category");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:C1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Category Name');
			$sheet->setCellValue('B2', 'Alias Name');
			$sheet->setCellValue('C2', '');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:C2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','D') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['item_catg']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['item_cat_alias']));
				$sheet->setCellValue('C'.$counter , '');	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Item Category.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function units()
		{
			$result = $this->UnitsModel->all_units_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Unit Master");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:D1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Unit Name');
			$sheet->setCellValue('B2', 'Alias Name');
			$sheet->setCellValue('C2', 'Print Name');
			$sheet->setCellValue('D2', 'UQC');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:D2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','D') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['item_unit']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['item_unit_alias']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['item_unit_print']));
				$sheet->setCellValue('D'.$counter , $this->clean($row['item_unit_uqc']));
							
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Unit Master.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function material_centres()
		{
			$result = $this->MaterialCentersModel->all_mc_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Material Center");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:D1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Center Name');
			$sheet->setCellValue('B2', 'Alias Name');
			$sheet->setCellValue('C2', 'Print Name');
			$sheet->setCellValue('D2', 'Group Name');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:D2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','D') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['mat_cent_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['mat_cent_alias']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['mat_cent_print']));
				$sheet->setCellValue('D'.$counter , $this->clean($row['group_name']));	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Material Center.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function material_centre_stores()
		{
			$result = $this->MaterialCentersModel->all_mc_store_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Material Center Stores");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:C1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Material Center Name');
			$sheet->setCellValue('B2', 'Store Name');
			$sheet->setCellValue('C2', 'Alias Name');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:C2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','D') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['mc_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['mc_store_name']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['mc_store_alias']));	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Material Center Stores.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function material_centre_groups()
		{
			$result = $this->MaterialCentersModel->all_mc_group_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Material Center Groups");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:C1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Group Name');
			$sheet->setCellValue('B2', 'Alias Name');
			$sheet->setCellValue('C2', 'Primary');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:C2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','C') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['group_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['alias_name']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['grp_primary']));	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Material Center Groups.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function bill_of_material()
		{
			$result = $this->BillofmaterialModel->all_bom_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Bill Of Material");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:C1');
			$spreadsheet->getActiveSheet()->getStyle("A1")->applyFromArray(array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 10
			)));
			$spreadsheet->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
				array(
					"horizontal" => "center", 
					"vertical" => "center"
				)
			);	
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Name');
			$sheet->setCellValue('B2', '');
			$sheet->setCellValue('C2', '');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:C2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','C') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['bom_name']));
				$sheet->setCellValue('B'.$counter , '');
				$sheet->setCellValue('C'.$counter , '');	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Bill Of Material.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function cost_center()
		{
			$result = $this->CostCentreModel->all_cc_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Cost Center");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:E1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Cost Center');
			$sheet->setCellValue('B2', 'Alias');
			$sheet->setCellValue('C2', 'Group');
			$sheet->setCellValue('D2', 'Balance');
			$sheet->setCellValue('E2', 'Dr/Cr');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:E2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','E') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['cc_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['cc_alias']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['cc_grp']));
				$sheet->setCellValue('D'.$counter , $this->clean($row['cc_op_bal']));
				$sheet->setCellValue('E'.$counter , $this->clean($row['cc_op_drcr']));
							

				$spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
					array(
						"horizontal" => "right", 
						"vertical" 	 => "center"
					)
				);	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Cost Center.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function cost_center_groups()
		{
			$result = $this->CostCentreModel->all_cc_group_export();

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Cost Center Groups");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:C1');
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
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'Group Name');
			$sheet->setCellValue('B2', 'Alias Name');
			$sheet->setCellValue('C2', 'Under Group');
	
			$spreadsheet->getActiveSheet()->getStyle("A2:C2")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','C') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['cc_grp_name']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['cc_grp_alias']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['under_cc_grp']));	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Cost Center Groups.xlsx"');
				$writer->save('php://output');
				exit;
		}

		public function voucher_series($voucher_type_id)
		{
			$voucher_info = $this->VoucherSeriesModel->voucher_type_info($voucher_type_id);
			$voucher_type = $voucher_info['comp_vch_type'] ?? '';

			$result = $this->VoucherSeriesModel->get_series_list_export($voucher_type_id);

			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
		  $fy_end    = date('Y', strtotime(date($fy_begndt.'-m-d'). ' + 1 year'));
	    
	
			$spreadsheet = new Spreadsheet();      
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Voucher Series");

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A1:C1');
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

			$spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:C2');
			$spreadsheet->getActiveSheet()->getStyle("A2")->applyFromArray(array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 13
			)));
			$spreadsheet->getActiveSheet()->getStyle("A2")->getAlignment()->applyFromArray(
				array(
					"horizontal" => "center", 
					"vertical" => "center"
				)
			);	
					
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', $voucher_type.' Voucher Series');
			$sheet->setCellValue('A3', 'Series Name');
			$sheet->setCellValue('B3', 'Voucher Numbering');
			$sheet->setCellValue('C3', 'No. of Vouchers');
	
			$spreadsheet->getActiveSheet()->getStyle("A3:C3")->applyFromArray(array(
				'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '000000'),
				'size'  => 13
			)));

			foreach(range('A','C') as $columnID) {
				$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

      $counter = 4;	
			$spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );
		
			foreach($result as $row){
				
				$sheet->setCellValue('A'.$counter , $this->clean($row['comp_vch_series']));
				$sheet->setCellValue('B'.$counter , $this->clean($row['comp_vch_method']));
				$sheet->setCellValue('C'.$counter , $this->clean($row['no_of_vouchers']));	
			
				$counter++;
			}

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="Voucher Series.xlsx"');
				$writer->save('php://output');
				exit;
		}
}