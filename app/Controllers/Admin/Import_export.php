<?php
namespace App\Controllers\Admin;

use App\Models\Admin\ImportExportModel;
use App\Models\Admin\TransactionModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class Import_export extends BaseController{
  function __construct(){  

    helper(['form', 'url','text']);

    $this->ImportExportModel  = new ImportExportModel();
    $this->TransactionModel  = new TransactionModel();
    $this->auth_session    = new auth_session();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
    $this->auth_session->role_restrict('CS');
    $this->base_url      = base_url().'/'.getenv('AdminPath');
    $this->folder_path   = getenv('AdminPath');
    $this->session    	 = \Config\Services::session();
    $this->company_id    =  $this->session->get('ses_company_id');
	$this->SupplyTypes   =  SupplyTypesList();
  }
    
  function index(){
    		
		$data['base_url']           = $this->base_url;	
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['base_url']           = $this->base_url;
		$data['session']            = $this->session;
	
    return view($this->folder_path.'import_export/view',$data);  
   }

  function import(){
      
    $data['base_url']           = $this->base_url;  
    $data['message_output']     = $this->message_output;
    $data['folder_path']        = $this->folder_path;
    $data['base_url']           = $this->base_url;
    $data['bo_state_code']      = $this->session->get('ses_bostecd');
    $data['bo_gstin_type']      = $this->session->get('bo_gstin_type');
    $data['goods_rate']         = 1;
    $data['services_rate']      = 6;
	$SupplyTypes                = $this->SupplyTypes;
	$SupplyTypesLabels=array();
	foreach($SupplyTypes as $id => $label){
	$SupplyTypesLabels[]= array("label"=>$label,"value"=>$label,"id"=>$id);	
	}
	$data['SupplyTypesLabels'] = json_encode($SupplyTypesLabels);
	
	$states_lists = $this->TransactionModel->show_states_lists(1);
	$POSTypesData    =array();
		foreach($states_lists as $id => $value){
			$POSTypesData[]= array("id"=>$id,"value"=>$value,"label"=>$value);
		}		
	$data['StatesLabels'] = json_encode($POSTypesData);
	
    return view($this->folder_path.'import_export/import',$data);  
  }

  function export(){
   
    $data['base_url']           = $this->base_url;  
    $data['message_output']     = $this->message_output;
    $data['folder_path']        = $this->folder_path;
    $data['base_url']           = $this->base_url;
    $data['session']            = $this->session;
  
    return view($this->folder_path.'import_export/export',$data);  
  }

  function account_group_master_sample()
  {
    $group_parents = ['Owner\'s Fund', 'Non Current Liabilities', 'Non Current Assets', 'Current Liabilities', 'Current Assets', 'Purchase', 'Sales', 'Direct Income', 'Direct Expenses', 'Indirect Income', 'Indirect Expenses'];

    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Account Groups");

    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Alias');
    $sheet->setCellValue('C1', 'Parent');
    $sheet->setCellValue('D1', 'Under Group');    

    $spreadsheet->getActiveSheet()->getStyle("A1:D1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','D') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold( true );

    for($i=1; $i<=1000; $i++){
      
      $sheet->setCellValue('A'.($i+1) , 'Group '.$i);
      $sheet->setCellValue('B'.($i+1) , 'Grp Alias '.$i);

      $sheet->setCellValue('C'.($i+1) ,$group_parents[$i%11]);

      if($i%3 == 0)
        $sheet->setCellValue('D'.($i+1) ,''); 
      else
        $sheet->setCellValue('D'.($i+1) ,'Group '.rand(1,10));
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Account Groups Sample.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }

  function account_master_sample()
  {
    $group_parents = ['Owner\'s Fund', 'Non Current Liabilities', 'Non Current Assets', 'Current Liabilities', 'Current Assets', 'Purchase', 'Sales', 'Direct Income', 'Direct Expenses', 'Indirect Income', 'Indirect Expenses'];

    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Account Master");

    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Alias');
    $sheet->setCellValue('C1', 'Print');
    $sheet->setCellValue('D1', 'Vendor Code');
    $sheet->setCellValue('E1', 'Primary');
    $sheet->setCellValue('F1', 'Parent');
    $sheet->setCellValue('G1', 'Under Group');    

    $spreadsheet->getActiveSheet()->getStyle("A1:G1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','G') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold( true );

    for($i=1; $i<=1000; $i++){
      
      $sheet->setCellValue('A'.($i+1),'Account '.$i);
      $sheet->setCellValue('B'.($i+1),'Account '.$i.' Alias');
      $sheet->setCellValue('C'.($i+1),'Account '.$i.' Print');
      $sheet->setCellValue('D'.($i+1),'Vendor '.rand(10,99));

      if($i%3 == 0){
        $sheet->setCellValue('E'.($i+1),'Y');
       $sheet->setCellValue('F'.($i+1),$group_parents[$i%11]);
        $sheet->setCellValue('G'.($i+1),'');
      }
      else{
        $sheet->setCellValue('E'.($i+1),'N');
        $sheet->setCellValue('F'.($i+1),'');
        $sheet->setCellValue('G'.($i+1),'Group '.rand(1,10));
      }
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Account Master Sample.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }

  function item_group_master_sample()
  {
    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Item Groups");

    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Alias');
    $sheet->setCellValue('C1', 'Under Group');    

    $spreadsheet->getActiveSheet()->getStyle("A1:C1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','C') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold( true );

    for($i=1; $i<=1000; $i++){
      
      $sheet->setCellValue('A'.($i+1) , 'Group '.$i);
      $sheet->setCellValue('B'.($i+1) , 'Grp Alias '.$i);

      if($i%3 == 0)
        $sheet->setCellValue('C'.($i+1) ,''); 
      else
        $sheet->setCellValue('C'.($i+1) ,'Group '.rand(1,10));
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Item Groups Sample.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }

  function item_category_master_sample()
  {

    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Item Categories");

    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Alias');    

    $spreadsheet->getActiveSheet()->getStyle("A1:B1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','B') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:B1")->getFont()->setBold( true );

    for($i=1; $i<=1000; $i++){
      
      $sheet->setCellValue('A'.($i+1) , 'Category '.$i);
      $sheet->setCellValue('B'.($i+1) , 'Category Alias '.$i);

    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Item Categories Sample.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }

  function item_master_sample()
  {
    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Item Master");

    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Alias');
    $sheet->setCellValue('C1', 'Print');
    $sheet->setCellValue('D1', 'Group');
    $sheet->setCellValue('E1', 'Category');
    $sheet->setCellValue('F1', 'Sales Account');
    $sheet->setCellValue('G1', 'Purchase Account');
    $sheet->setCellValue('H1', 'Unit');
    $sheet->setCellValue('I1', 'Val. Method');
    $sheet->setCellValue('J1', 'MRP'); 
    $sheet->setCellValue('K1', 'UPC');	

    $spreadsheet->getActiveSheet()->getStyle("A1:K1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','K') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold( true );

    $val_arr = ['AVG','FIFO','LIFO'];
    $units_arr = ['Packs','Pair','Dozen','Pcs','NA'];

    for($i=1; $i<=1000; $i++){
      
      $sheet->setCellValue('A'.($i+1) , 'Item '.$i);
      $sheet->setCellValue('B'.($i+1) , 'Item Alias '.$i);
      $sheet->setCellValue('C'.($i+1) , 'Item Print '.$i);
      $sheet->setCellValue('D'.($i+1) , 'Group '.rand(1,10));
      $sheet->setCellValue('E'.($i+1) ,'Category '.rand(1,9));
      $sheet->setCellValue('F'.($i+1) , 'Account 6');
      $sheet->setCellValue('G'.($i+1) , 'Account 27');
      $sheet->setCellValue('H'.($i+1) , $units_arr[$i%5]);
      $sheet->setCellValue('I'.($i+1) , $val_arr[$i%3]);
      $sheet->setCellValue('J'.($i+1) , rand(1,10).'0.'.rand(1,9).'0');
	  $sheet->setCellValue('K'.($i+1) , 'item upc'.$i);

    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Item Master Sample.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }


  function day_book_sample()
  {
    $types = ['PYMT','RCPT','JRNL','CNTR','MEMO'];
    $amount = 0;

    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Day Book");

    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Type');
    $sheet->setCellValue('C1', 'Vch Series');
    $sheet->setCellValue('D1', 'Bill No.');
    $sheet->setCellValue('E1', 'Account');
    $sheet->setCellValue('F1', 'Debit');
    $sheet->setCellValue('G1', 'Credit');
    $sheet->setCellValue('H1', 'Short Narration');    

    $spreadsheet->getActiveSheet()->getStyle("A1:H1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','H') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold( true );

    $account1 = '';
    $account2 = '';
    for($i=1; $i<=2000; $i++){

      $date = '';
      $vno = '';
      $type = '';
      $series = '';
      $account = '';
      $debit = 0;
      $credit = 0;
      $short = 'Excepteur sint occaecat cupidatat non proide';

      if(($i-1)%5 == 0){
        $amt = [1000,2000,3000,4000,5000];
        $amount = $amt[rand(0,4)];

        $account1 = 'Account '.rand(1,5);
        $account2 = 'Account '.rand(6,10);

        $date=date('Y-m-d',strtotime('- '.rand(0,2).' days'));
        $vno = $i.'/ 2023-24';
        $type = $types[rand(0,4)];
        $series = 'Main';
        $account = $account1;
        $debit = $amount + $amount*0.1;
        $credit = 0;
      }

      if(($i-1)%5 == 1){
        $account = $account2;
        $credit = $amount;
      }
      if(($i-1)%5 == 2){
        $account = 'Tax';
        $credit = $amount*0.1;
      }
      if(($i-1)%5 == 3){
        $account = '### '.$account1.' @ '.$amount;
        $short = '';
      }
      if(($i-1)%5 == 4){
        $account = '### '.$account2.' @ '.($amount - $amount*0.1);
        $short = '';
      }

      $sheet->setCellValue('A'.($i+1) , $date);
	  $sheet->getStyle('A'.($i+1))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
      $sheet->setCellValue('B'.($i+1) , $type);
      $sheet->setCellValue('C'.($i+1) , $series);
      $sheet->setCellValue('D'.($i+1) , $vno);
      $sheet->setCellValue('E'.($i+1) , $account);
      $sheet->setCellValue('F'.($i+1) , $debit);
      $sheet->setCellValue('G'.($i+1) , $credit);
      $sheet->setCellValue('H'.($i+1) , $short);
      
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Day Book Sample.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }

  function bank_statement_sample()
  {
    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Bank Statement");

    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Description');
    $sheet->setCellValue('C1', 'DR AMT (Bank)');
    $sheet->setCellValue('D1', 'CR AMT (Bank)');
    $sheet->setCellValue('E1', 'Corresponding Account (optional)'); 
    $sheet->setCellValue('F1', 'Vch Series');   

    $spreadsheet->getActiveSheet()->getStyle("A1:F1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','F') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:F1")->getFont()->setBold( true );


    $amt = [1000,2000,3000,4000,5000];

    for($i=1; $i<=500; $i++){

      $date = '';
      $account = '';
      $dr_amt = '';
      $cr_amt = '';
      $desc = 'Excepteur sint occaecat cupidatat non proide';

      if(($i-1)%5 == 0){
        $account = 'Account '.rand(1,10);
      }

      if(($i-1)%2 == 0){
        $dr_amt = $amt[rand(0,4)];
      }
      if(($i-1)%2 == 1){
        $cr_amt = $amt[rand(0,4)];
      }


      $date=date('Y-m-d',strtotime('- '.rand(0,2).' days'));

      $sheet->setCellValue('A'.($i+1) , $date);
	  $sheet->getStyle('A'.($i+1))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
      $sheet->setCellValue('B'.($i+1) , $desc);
      $sheet->setCellValue('C'.($i+1) , $dr_amt);
      $sheet->setCellValue('D'.($i+1) , $cr_amt);
      $sheet->setCellValue('E'.($i+1) , $account);
      $sheet->setCellValue('F'.($i+1) , 'Main');
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Bank Statement Sample.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }

 
  
  function purchase_non_item_sample()
  {  
  $states_lists =   $this->TransactionModel->show_states_lists(1); 	
	$state_dropdownOptions = array_values($states_lists);  
	$validStatesValues = implode(',', $state_dropdownOptions);
	
    $pos_arr = ['Delhi(07)','Haryana(06)','Punjab(03)','Sikkim(11)','Manipur(14)'];
	
   $dropdownOptions = array_values($this->SupplyTypes);
		$validValues = implode(',', $dropdownOptions);
    //$pos_arr = ['01','02','03','04','05'];
    $amount = 0;

    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Purchase Without Item");
	
	// Create a new worksheet for the states list
	$statesSheet = $spreadsheet->createSheet();
	$statesSheet->setTitle('States');
    // Populate the "States" worksheet with the list of states in column A
	foreach ($state_dropdownOptions as $index => $state) {
		$statesSheet->setCellValue('A' . ($index + 1), $state);
	}
	

    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Type');
	$sheet->setCellValue('C1', 'Supply Type');
    $sheet->setCellValue('D1', 'Vch Series');
    $sheet->setCellValue('E1', 'Bill No.');
    $sheet->setCellValue('F1', 'Account');
    $sheet->setCellValue('G1', 'Debit');
    $sheet->setCellValue('H1', 'Credit');
    $sheet->setCellValue('I1', 'Short Narration');
    $sheet->setCellValue('J1', 'POS');
    $sheet->setCellValue('K1', 'Country Code');    

    $spreadsheet->getActiveSheet()->getStyle("A1:K1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','K') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold( true );

    $account1 = '';
    $account2 = '';
	
	$dataValidation_state = $sheet->getCell('J2')->getDataValidation();
	$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
	$sheet->setDataValidation('J2:J2000', $dataValidation_state);
	
	$dataValidation = $sheet->getCell('C2')->getDataValidation();
    $dataValidation->setType(DataValidation::TYPE_LIST)
               ->setFormula1('"' . $validValues . '"')
               ->setShowDropDown(true); // To show the dropdown arrow
    $sheet->setDataValidation('C2:C2000', $dataValidation);
    for($i=1; $i<=2000; $i++){

      $date = '';
      $vno = '';
      $type = '';
	   $supply_type = '';
      $series = '';
      $account = '';
      $debit = '';
      $credit = '';
      $short ='Excepteur sint occaecat cupidatat non proide';
      $pos = '';
      $code = '';

      if(($i-1)%5 == 0){
        $amt = [1000,2000,3000,4000,5000];
        $amount = $amt[rand(0,4)];

        $account1 = 'Cash In Hand';
        $account2 = 'Account '.rand(1,2);

        $date=date('Y-m-d',strtotime('- '.rand(0,2).' days'));
        $vno = $i.'/ 2023-24';
        $type = 'PURCHASE'; 
		$supply_type = 'SUPPLY (REGULAR)';
        $series = 'Main';
        $account = $account1;
        $debit =  0;
        $credit = $amount + $amount*0.1;
        $short = '';

        $pos = $pos_arr[rand(0,4)];
        $code = 'IN';
      }

      if(($i-1)%5 == 1){
        $account = $account2;
        $debit = $amount;
        $credit = 0; 
      }
      if(($i-1)%5 == 2){
        $account = 'Tax';
        $debit = $amount*0.1;
        $credit = 0;
        $short = '';
      }
      if(($i-1)%5 == 3){
        $account = '### '.$account1.' @ '.$amount;
        $short = '';
      }
      if(($i-1)%5 == 4){
        $account = '### '.$account2.' @ '.($amount - $amount*0.1);
        $short = '';
      }

      $sheet->setCellValue('A'.($i+1) , $date);
	  $sheet->getStyle('A'.($i+1))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
      $sheet->setCellValue('B'.($i+1) , $type); 
	  $sheet->setCellValue('C'.($i+1) , $supply_type);
      $sheet->setCellValue('D'.($i+1) , $series);
      $sheet->setCellValue('E'.($i+1) , $vno);
      $sheet->setCellValue('F'.($i+1) , $account);
      $sheet->setCellValue('G'.($i+1) , $debit);
      $sheet->setCellValue('H'.($i+1) , $credit);
      $sheet->setCellValue('I'.($i+1) , $short);
      $sheet->setCellValue('J'.($i+1) , $pos);
      $sheet->setCellValue('K'.($i+1) , $code);
      
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Purchase Without Item.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  function purchase_item_sample()
  {
	  $states_lists =   $this->TransactionModel->show_states_lists(1); 	
	 $state_dropdownOptions = array_values($states_lists);  
	 $validStatesValues = implode(',', $state_dropdownOptions);
     $dropdownOptions = array_values($this->SupplyTypes);
		$validValues = implode(',', $dropdownOptions);
  
	$pos_arr = ['Delhi(07)','Haryana(06)','Punjab(03)','Sikkim(11)','Manipur(14)'];
    $units_arr = ['Packs','Pair','Dozen','Pcs','NA'];
    $qty_arr = [10,20,30,40,50]; 
    $amount = 0;

    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Purchase Item");
// Create a new worksheet for the states list
	$statesSheet = $spreadsheet->createSheet();
	$statesSheet->setTitle('States');
    // Populate the "States" worksheet with the list of states in column A
	foreach ($state_dropdownOptions as $index => $state) {
		$statesSheet->setCellValue('A' . ($index + 1), $state);
	}
	
    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Type');
	$sheet->setCellValue('C1', 'Supply Type');
    $sheet->setCellValue('D1', 'Vch Series');
    $sheet->setCellValue('E1', 'Bill No.');
    $sheet->setCellValue('F1', 'UQC');
    $sheet->setCellValue('G1', 'Account/Item');
    $sheet->setCellValue('H1', 'Debit');
    $sheet->setCellValue('I1', 'Credit');
    $sheet->setCellValue('J1', 'QTY');
    $sheet->setCellValue('K1', 'Short Narration');
    $sheet->setCellValue('L1', 'MC');
    $sheet->setCellValue('M1', 'POS');
    $sheet->setCellValue('N1', 'Country Code');    

    $spreadsheet->getActiveSheet()->getStyle("A1:N1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','N') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:N1")->getFont()->setBold( true );

    $account1 = '';
    $account2 = '';
	
	$dataValidation_state = $sheet->getCell('M2')->getDataValidation();
	$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
	$sheet->setDataValidation('M2:M2000', $dataValidation_state);
	
	
	$dataValidation = $sheet->getCell('C2')->getDataValidation();
$dataValidation->setType(DataValidation::TYPE_LIST)
               ->setFormula1('"' . $validValues . '"')
               ->setShowDropDown(true); // To show the dropdown arrow
$sheet->setDataValidation('C2:C2000', $dataValidation);
    for($i=1; $i<=2000; $i++){

      $date = '';
      $vno = '';
      $type = '';
	  $supply_type = '';
      $series = '';
      $account = '';
      $debit = '';
      $credit = '';
      $short ='Excepteur sint occaecat cupidatat non proide';
      $pos = '';
      $code = '';

      $unit = '';
      $mc = '';
      $qty = '';

      if(($i-1)%10 == 0){
        $amt = [1000,2000,3000,4000,5000];
        $amount = $amt[rand(0,4)];

        $date=date('Y-m-d',strtotime('- '.rand(0,2).' days'));
        $vno = $i.'/ 2023-24';
        $type = 'PURCHASE';
		$supply_type = 'SUPPLY (REGULAR)';
        $series = 'Main';
        $account = 'Cash In hand';
        $debit = 0;
        $credit = $amount + $amount*0.1 + $amount*0.01 + $amount*0.01;;
        $short = '';
        $pos = $pos_arr[rand(0,4)];
        $code = 'IN';

        $unit = '';
        $qty = '';
        $mc = 'Main';

        $item1 = 'Item '.rand(1,2);
        $item2 = 'Item '.rand(3,4);
        $qty1 = $qty_arr[rand(0,4)];
        $qty2 = $qty_arr[rand(0,4)];

      }
      
      if(($i-1)%10 == 1){
        $account = 'Item '.rand(1,2);
        $debit = $amount/2;
        $credit = 0;
        $qty = $qty1;
        $unit = $units_arr[rand(0,4)];
      }
      if(($i-1)%10 == 2){
        $account = 'Item '.rand(3,4);
        $debit = $amount/2;
        $credit = 0;
        $qty = $qty2;
        $unit = $units_arr[rand(0,4)];
      }

      if(($i-1)%10 == 3){
        $account = 'TAX';
        $debit = $amount*0.1;
        $credit = 0; 
        $short = '';
      }
      if(($i-1)%10 == 4){
        $account = 'IGST';
        $debit = $amount*0.01;
        $credit = 0;
        $short = '';
      }
      if(($i-1)%10 == 5){
        $account = 'CESS';
        $debit = $amount*0.01;
        $credit = 0; 
        $short = '';
      }

      if(($i-1)%10 == 6){
        $account = '### Cash In Hand @ '.$amount + $amount*0.1 + $amount*0.01 + $amount*0.01;
        $short = '';
      }
      if(($i-1)%10 == 7){
        $account = '### TAX @ '.($amount*0.1);
        $short = '';
      }
      if(($i-1)%10 == 8){
        $account = '### '.$item1.' @ '.$qty1.' = '.($amount/2);
        $short = '';
      }
      if(($i-1)%10 == 9){
        $account = '### '.$item2.' @ '.$qty2.' = '.($amount/2);
        $short = '';
      }

      $sheet->setCellValue('A'.($i+1) , $date);
	  $sheet->getStyle('A'.($i+1))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
      $sheet->setCellValue('B'.($i+1) , $type);
	  $sheet->setCellValue('C'.($i+1) , $supply_type);
      $sheet->setCellValue('D'.($i+1) , $series);
      $sheet->setCellValue('E'.($i+1) , $vno);
      $sheet->setCellValue('F'.($i+1) , $unit);
      $sheet->setCellValue('G'.($i+1) , $account);
      $sheet->setCellValue('H'.($i+1) , $debit);
      $sheet->setCellValue('I'.($i+1) , $credit);
      $sheet->setCellValue('J'.($i+1) , $qty);
      $sheet->setCellValue('K'.($i+1) , $short);
      $sheet->setCellValue('L'.($i+1) , $mc);
      $sheet->setCellValue('M'.($i+1) , $pos);
      $sheet->setCellValue('N'.($i+1) , $code);
      
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Purchase Item.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  
  
  function sale_item_sample()
  {
    $states_lists =   $this->TransactionModel->show_states_lists(1); 	
	$state_dropdownOptions = array_values($states_lists);  
	$validStatesValues = implode(',', $state_dropdownOptions);
	
    $pos_arr = ['Delhi(07)','Haryana(06)','Punjab(03)','Sikkim(11)','Manipur(14)'];
    $units_arr = ['Packs','Pair','Dozen','Pcs','NA'];
    $qty_arr = [10,20,30,40,50]; 
    $amount = 0;
	$dropdownOptions = array_values($this->SupplyTypes);
	$validValues = implode(',', $dropdownOptions);	
    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Sales Item");
	
	// Create a new worksheet for the states list
	$statesSheet = $spreadsheet->createSheet();
	$statesSheet->setTitle('States');
    // Populate the "States" worksheet with the list of states in column A
	foreach ($state_dropdownOptions as $index => $state) {
		$statesSheet->setCellValue('A' . ($index + 1), $state);
	}

    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Type');
	$sheet->setCellValue('C1', 'Supply Type');
    $sheet->setCellValue('D1', 'Vch Series');
    $sheet->setCellValue('E1', 'Bill No.');
    $sheet->setCellValue('F1', 'UQC');
    $sheet->setCellValue('G1', 'Account/Item');
    $sheet->setCellValue('H1', 'Debit');
    $sheet->setCellValue('I1', 'Credit');
    $sheet->setCellValue('J1', 'QTY');
    $sheet->setCellValue('K1', 'Short Narration');
    $sheet->setCellValue('L1', 'MC');
    $sheet->setCellValue('M1', 'POS');
    $sheet->setCellValue('N1', 'Country Code');    

    $spreadsheet->getActiveSheet()->getStyle("A1:N1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','J') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:N1")->getFont()->setBold( true );

    $account1 = '';
    $account2 = '';
	
	$dataValidation_state = $sheet->getCell('M2')->getDataValidation();
	$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
	$sheet->setDataValidation('M2:M2000', $dataValidation_state);
	
	
	$dataValidation = $sheet->getCell('C2')->getDataValidation();
    $dataValidation->setType(DataValidation::TYPE_LIST)
               ->setFormula1('"' . $validValues . '"')
               ->setShowDropDown(true); // To show the dropdown arrow
    $sheet->setDataValidation('C2:C2000', $dataValidation);
    for($i=1; $i<=2000; $i++){

      $date = '';
      $vno = '';
      $type = '';
	  $supply_type = '';
      $series = '';
      $account = '';
      $debit = '';
      $credit = '';
      $short ='Excepteur sint occaecat cupidatat non proide';
      $pos = '';
      $code = '';

      $unit = '';
      $mc = '';
      $qty = '';

      if(($i-1)%10 == 0){
        $amt = [1000,2000,3000,4000,5000];
        $amount = $amt[rand(0,4)];

        $date=date('Y-m-d',strtotime('- '.rand(0,2).' days'));
        $vno = $i.'/ 2023-24';
        $type = 'SALE';
		$supply_type = 'SUPPLY (REGULAR)';
        $series = 'Main';
        $account = 'Cash In hand';
        $debit = $amount + $amount*0.1 + $amount*0.01 + $amount*0.01;
        $credit = 0;
        $short = '';
        $pos = $pos_arr[rand(0,4)];
        $code = 'IN';

        $unit = '';
        $qty = '';
        $mc = 'Main';

        $item1 = 'Item '.rand(1,2);
        $item2 = 'Item '.rand(3,4);
        $qty1 = $qty_arr[rand(0,4)];
        $qty2 = $qty_arr[rand(0,4)];

      }
      
      if(($i-1)%10 == 1){
        $account = 'Item '.rand(1,2);
        $debit = 0;
        $credit = $amount/2;
        $qty = $qty1;
        $unit = $units_arr[rand(0,4)];
      }
      if(($i-1)%10 == 2){
        $account = 'Item '.rand(3,4);
        $debit = 0;
        $credit = $amount/2;
        $qty = $qty2;
        $unit = $units_arr[rand(0,4)];
      }

      if(($i-1)%10 == 3){
        $account = 'TAX';
        $debit = 0;
        $credit = $amount*0.1;
        $short = '';
      }
      if(($i-1)%10 == 4){
        $account = 'IGST';
        $debit = 0;
        $credit = $amount*0.01;
        $short = '';
      }
      if(($i-1)%10 == 5){
        $account = 'CESS';
        $debit = 0;
        $credit = $amount*0.01;
        $short = '';
      }

      if(($i-1)%10 == 6){
        $account = '### Cash In Hand @ '.$amount + $amount*0.1 + $amount*0.01 + $amount*0.01;
        $short = '';
      }
      if(($i-1)%10 == 7){
        $account = '### TAX @ '.($amount*0.1);
        $short = '';
      }
      if(($i-1)%10 == 8){
        $account = '### '.$item1.' @ '.$qty1.' = '.($amount/2);
        $short = '';
      }
      if(($i-1)%10 == 9){
        $account = '### '.$item2.' @ '.$qty2.' = '.($amount/2);
        $short = '';
      }

      $sheet->setCellValue('A'.($i+1) , $date);
	  $sheet->getStyle('A'.($i+1))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
      $sheet->setCellValue('B'.($i+1) , $type);
	  $sheet->setCellValue('C'.($i+1) , $supply_type);
      $sheet->setCellValue('D'.($i+1) , $series);
      $sheet->setCellValue('E'.($i+1) , $vno);
      $sheet->setCellValue('F'.($i+1) , $unit);
      $sheet->setCellValue('G'.($i+1) , $account);
      $sheet->setCellValue('H'.($i+1) , $debit);
      $sheet->setCellValue('I'.($i+1) , $credit);
      $sheet->setCellValue('J'.($i+1) , $qty);
      $sheet->setCellValue('K'.($i+1) , $short);
      $sheet->setCellValue('L'.($i+1) , $mc);
      $sheet->setCellValue('M'.($i+1) , $pos);
      $sheet->setCellValue('N'.($i+1) , $code);
      
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Sales Item.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  
   function sale_non_item_sample()
  {
	$states_lists =   $this->TransactionModel->show_states_lists(1); 	
	$state_dropdownOptions = array_values($states_lists);  
	$validStatesValues = implode(',', $state_dropdownOptions);	
	  
    $dropdownOptions = array_values($this->SupplyTypes);
	$validValues = implode(',', $dropdownOptions);
   $pos_arr = ['Delhi(07)','Haryana(06)','Punjab(03)','Sikkim(11)','Manipur(14)'];
    $amount = 0;

    $spreadsheet = new Spreadsheet();      
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Sales Without Item");
	
	// Create a new worksheet for the states list
	$statesSheet = $spreadsheet->createSheet();
	$statesSheet->setTitle('States');
    // Populate the "States" worksheet with the list of states in column A
	foreach ($state_dropdownOptions as $index => $state) {
		$statesSheet->setCellValue('A' . ($index + 1), $state);
	}

    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Type');
	$sheet->setCellValue('C1', 'Supply Type');	
    $sheet->setCellValue('D1', 'Vch Series');
    $sheet->setCellValue('E1', 'Bill No.');
    $sheet->setCellValue('F1', 'Account');
    $sheet->setCellValue('G1', 'Debit');
    $sheet->setCellValue('H1', 'Credit');
    $sheet->setCellValue('I1', 'Short Narration');
    $sheet->setCellValue('J1', 'POS');
    $sheet->setCellValue('K1', 'Country Code');    
    
    $spreadsheet->getActiveSheet()->getStyle("A1:K1")->applyFromArray(array(
            'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => '000000'),
              'size'  => 13
            )));

    foreach(range('A','K') as $columnID) {
      $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    }

    $counter = 2; 
    $spreadsheet->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold( true );

    $account1 = '';
    $account2 = '';
	
	$dataValidation_state = $sheet->getCell('J2')->getDataValidation();
	$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
	$sheet->setDataValidation('J2:J2000', $dataValidation_state);
	
	
	$dataValidation = $sheet->getCell('C2')->getDataValidation();
$dataValidation->setType(DataValidation::TYPE_LIST)
               ->setFormula1('"' . $validValues . '"')
               ->setShowDropDown(true); // To show the dropdown arrow
$sheet->setDataValidation('C2:C2000', $dataValidation);
    for($i=1; $i<=2000; $i++){

      $date = '';
      $vno = '';
      $type = '';
	  $supply_type = '';
      $series = '';
      $account = '';
      $debit = '';
      $credit = '';
      $short ='Excepteur sint occaecat cupidatat non proide';
      $pos = '';
      $code = '';

      if(($i-1)%5 == 0){
        $amt = [1000,2000,3000,4000,5000];
        $amount = $amt[rand(0,4)];

        $account1 = 'Cash In Hand';
        $account2 = 'Account '.rand(1,2);

        $date=date('Y-m-d',strtotime('- '.rand(0,2).' days'));
        $vno = $i.'/ 2023-24';
        $type = 'SALE';
		$supply_type = 'SUPPLY (REGULAR)';
        $series = 'Main';
        $account = $account1;
        $debit = $amount + $amount*0.1;
        $credit = 0;
        $short = '';

        $pos = $pos_arr[rand(0,4)];
        $code = 'IN';
      }

      if(($i-1)%5 == 1){
        $account = $account2;
        $debit = 0;
        $credit = $amount;
      }
      if(($i-1)%5 == 2){
        $account = 'Tax';
        $debit = 0;
        $credit = $amount*0.1;
        $short = '';
      }
      if(($i-1)%5 == 3){
        $account = '### '.$account1.' @ '.$amount;
        $short = '';
      }
      if(($i-1)%5 == 4){
        $account = '### '.$account2.' @ '.($amount - $amount*0.1);
        $short = '';
      }

      $sheet->setCellValue('A'.($i+1) , $date);
	  $sheet->getStyle('A'.($i+1))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
      $sheet->setCellValue('B'.($i+1) , $type);
	  $sheet->setCellValue('C'.($i+1) , $supply_type);
      $sheet->setCellValue('D'.($i+1) , $series);
      $sheet->setCellValue('E'.($i+1) , $vno);
      $sheet->setCellValue('F'.($i+1) , $account);
      $sheet->setCellValue('G'.($i+1) , $debit);
      $sheet->setCellValue('H'.($i+1) , $credit);
      $sheet->setCellValue('I'.($i+1) , $short);
      $sheet->setCellValue('J'.($i+1) , $pos);
      $sheet->setCellValue('K'.($i+1) , $code);
	  
      
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Sales Without Item.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  
  function sale_item_single_sample()
  {	
	$states_lists =   $this->TransactionModel->show_states_lists(1); 	
	$state_dropdownOptions = array_values($states_lists);  
	$validStatesValues = implode(',', $state_dropdownOptions);	
	

	$dropdownOptions = array_values($this->SupplyTypes);
	$validValues = implode(',', $dropdownOptions);
		
		
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setTitle("Sales Item");
	
	// Create a new worksheet for the states list
	$statesSheet = $spreadsheet->createSheet();
	$statesSheet->setTitle('States');
    // Populate the "States" worksheet with the list of states in column A
	foreach ($state_dropdownOptions as $index => $state) {
		$statesSheet->setCellValue('A' . ($index + 1), $state);
	}


	$sheet->setCellValue('A1', 'Date');
	$sheet->setCellValue('B1', 'Type');
	$sheet->setCellValue('C1', 'Supply Type');
	$sheet->setCellValue('D1', 'Vch Series');
	$sheet->setCellValue('E1', 'Bill No.');
	$sheet->setCellValue('F1', 'MC');
	$sheet->setCellValue('G1', 'POS');
	$sheet->setCellValue('H1', 'Country Code');
	$sheet->setCellValue('I1', 'Long Narration');
	$sheet->setCellValue('J1', 'Party Account');
	$sheet->setCellValue('K1', 'Dr. Amount');
	$sheet->setCellValue('L1', 'IGST');
	$sheet->setCellValue('M1', 'CGST');
	$sheet->setCellValue('N1', 'SGST');
	$sheet->setCellValue('O1', 'Cess');
	$sheet->getStyle('A1:O1')->getFont()->setBold(true); // ID column bold
	$startingIndex = 16; // Start after the static columns

	// Function to generate random data for the Item columns
	function generateRandomData() {
		return [
			'Item Name' => 'Item ' . rand(1, 1000),
			'Amount' => 1000,
			'QTY' => rand(1, 100),
			'UQC' => 'Packs ' . rand(1, 50),
			'Short Narration' => 'Short Narration ' . rand(1, 1000)
		];
	}
	 function getColumnLetter($index) {
			$letters = '';
			while ($index >= 0) {
				$letters = chr($index % 26 + 65) . $letters;
				$index = floor($index / 26) - 1;
			}
			return $letters;
		}
	// Function to generate random data for Bill Sundry columns
	function generateRandombsdData() {
		return [
			'Bill Sundry' => 'Bill Sundry ' . rand(1, 1000),
			'Amount' => rand(1, 100),
			'Cr./Dr.' => rand(0, 1) == 0 ? 'Dr' : 'Cr',
		];
	}

	// Create the dynamic array based on the number of rows and groups (columns)
	$dataArray = [];
	$numRows = 2; // Set number of rows you want to generate
	$bsdnumGroups = 2; // Number of Bill Sundry groups
	$numGroups = 15; // Number of item groups

	for ($i = 0; $i < $numRows; $i++) {
		$rowData = [];
	   
		// Add Item data for each group
		for ($k = 0; $k < $numGroups; $k++) {
			$randomData = generateRandomData();
			$rowData[] = $randomData['Item Name'];
			$rowData[] = $randomData['Amount'];
			$rowData[] = $randomData['QTY'];
			$rowData[] = $randomData['UQC'];
			$rowData[] = $randomData['Short Narration'];
		}
		
		 // Add Bill Sundry data for each group
		for ($g = 0; $g < $bsdnumGroups; $g++) {
			$randomData = generateRandombsdData();
			$rowData[] = $randomData['Bill Sundry'];
			$rowData[] = $randomData['Amount'];
			$rowData[] = $randomData['Cr./Dr.'];
		}
		$dataArray[] = $rowData;
	}

	$columns = ['Item Name', 'Item Amount', 'Item QTY', 'Item UQC', 'Item Short Narration'];
	$bsd_columns = ['Bill Sundry Name', 'Bill Sundry Amount', 'Bill Sundry Cr./Dr.'];

	$col = $startingIndex-1;

	for ($g = 0; $g < $numGroups; $g++) {
		foreach ($columns as $header) {
			$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
			$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
			$col++;
		}
	}


	for ($g = 0; $g < $bsdnumGroups; $g++) {
		foreach ($bsd_columns as $header) {
			$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
			$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
			$col++;
		}
	}

	
	$dataValidation_state = $sheet->getCell('G2')->getDataValidation();
	$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
	$sheet->setDataValidation('G2:G2000', $dataValidation_state);
	
	 $dataValidation = $sheet->getCell('C2')->getDataValidation();
	$dataValidation->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('"' . $validValues . '"')
				   ->setShowDropDown(true);
	$sheet->setDataValidation('C2:C2000', $dataValidation);
	// Fill data in the rows
	for ($k = 0; $k < count($dataArray); $k++) {
		
		$sheet->setCellValue('A' . ($k + 2), date('Y-m-d', strtotime('+ ' . $k . ' days')));
		$sheet->getStyle('A'.($k + 2))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
		$sheet->setCellValue('B' . ($k + 2), 'SALE');
		$sheet->setCellValue('C' . ($k + 2), 'SUPPLY (REGULAR)');
		$sheet->setCellValue('D' . ($k + 2), 'Main');
		$sheet->setCellValue('E' . ($k + 2), '1991' . $k . '/ 2023-24');
		$sheet->setCellValue('F' . ($k + 2), 'Main');
		$sheet->setCellValue('G' . ($k + 2), 'Punjab(03)');
		$sheet->setCellValue('H' . ($k + 2), 'IN');
		$sheet->setCellValue('I' . ($k + 2), 'Excepteur sint occaecat cupidatat non proide');
		$sheet->setCellValue('J' . ($k + 2), 'Party Account');
		$sheet->setCellValue('K' . ($k + 2), 'Dr. Amount');
		$sheet->setCellValue('L' . ($k + 2), 'IGST');
		$sheet->setCellValue('M' . ($k + 2), 'CGST');
		$sheet->setCellValue('N' . ($k + 2), 'SGST');
		$sheet->setCellValue('O' . ($k + 2), 'Cess');

	// Set Item data
	$colOffset = 0; // For Bill Sundry columns
		
		for ($g = 0; $g < $numGroups; $g++) {
			// Fill Item data
			for ($c = 0; $c < count($columns); $c++) {
				$columnValue = $dataArray[$k][$colOffset + $c];
				$currentColumn = $startingIndex + $colOffset + $c;
				$columnLetter = getColumnLetter($currentColumn - 1);
				$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
				
			}
			$colOffset += count($columns); // Move offset after Item columns
		}
		
		// Set Bill Sundry and Item data for each row
		$colOffset = $colOffset; // Continue from where Bill Sundry left off
		for ($g = 0; $g < $bsdnumGroups; $g++) {
			// Fill Bill Sundry data
			for ($c = 0; $c < count($bsd_columns); $c++) {
				$columnValue = $dataArray[$k][$colOffset + $c];
				$currentColumn = $startingIndex + $colOffset + $c;
				$columnLetter = getColumnLetter($currentColumn - 1);
				$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
				
			}
			$colOffset += count($bsd_columns); // Move offset after Bill Sundry columns
		}

		
	}
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Sales Item Single Row Format.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  
  function sale_non_item_single_sample()
  {
	    $states_lists =   $this->TransactionModel->show_states_lists(1); 	
	    $state_dropdownOptions = array_values($states_lists);  
	    $validStatesValues = implode(',', $state_dropdownOptions);
	
	    $dropdownOptions = array_values($this->SupplyTypes);
		$validValues = implode(',', $dropdownOptions);
		
			$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Sales Item");
		// Create a new worksheet for the states list
		$statesSheet = $spreadsheet->createSheet();
		$statesSheet->setTitle('States');
		// Populate the "States" worksheet with the list of states in column A
		foreach ($state_dropdownOptions as $index => $state) {
			$statesSheet->setCellValue('A' . ($index + 1), $state);
		}
	
		$sheet->setCellValue('A1', 'Date');
		$sheet->setCellValue('B1', 'Type');
		$sheet->setCellValue('C1', 'Supply Type');
		$sheet->setCellValue('D1', 'Vch Series');
		$sheet->setCellValue('E1', 'Bill No.');
		$sheet->setCellValue('F1', 'POS');
		$sheet->setCellValue('G1', 'Country Code');
		$sheet->setCellValue('H1', 'Long Narration');
		$sheet->setCellValue('I1', 'Party Account');
		$sheet->setCellValue('J1', 'Party Amount');
		$sheet->setCellValue('K1', 'IGST');
		$sheet->setCellValue('L1', 'CGST');
		$sheet->setCellValue('M1', 'SGST');
		$sheet->setCellValue('N1', 'Cess');
		$sheet->getStyle('A1:N1')->getFont()->setBold(true); // ID column bold
		$startingIndex = 15; // Start after the static columns

		// Function to generate random data for the Item columns
		function generateRandomData() {
			return [
				'Account' => 'Account Name' . rand(1, 1000),
				'Amount' => rand(1000, 10000),
				'Short Narration' => 'Short Narration ' . rand(1, 1000)
			];
		}
		 function getColumnLetter($index) {
				$letters = '';
				while ($index >= 0) {
					$letters = chr($index % 26 + 65) . $letters;
					$index = floor($index / 26) - 1;
				}
				return $letters;
			}
		// Function to generate random data for Bill Sundry columns
		function generateRandombsdData() {
			return [
				'Bill Sundry' => 'Bill Sundry ' . rand(1, 1000),
				'Amount' => rand(1, 100),
				'Cr./Dr.' => rand(0, 1) == 0 ? 'Dr' : 'Cr',
			];
		}

		// Create the dynamic array based on the number of rows and groups (columns)
		$dataArray = [];
		$numRows = 2; // Set number of rows you want to generate
		$bsdnumGroups = 2; // Number of Bill Sundry groups
		$numGroups = 15; // Number of item groups

		for ($i = 0; $i < $numRows; $i++) {
			$rowData = [];
		   
			// Add Item data for each group
			for ($k = 0; $k < $numGroups; $k++) {
				$randomData = generateRandomData();
				$rowData[] = $randomData['Account'];
				$rowData[] = $randomData['Amount'];
				$rowData[] = $randomData['Short Narration'];
			}
			
			 // Add Bill Sundry data for each group
			for ($g = 0; $g < $bsdnumGroups; $g++) {
				$randomData = generateRandombsdData();
				$rowData[] = $randomData['Bill Sundry'];
				$rowData[] = $randomData['Amount'];
				$rowData[] = $randomData['Cr./Dr.'];
			}
			$dataArray[] = $rowData;
		}

		$columns = ['Account Name', 'Account Amount','Account Short Narration'];
		$bsd_columns = ['Bill Sundry Name', 'Bill Sundry Amount', 'Bill Sundry Cr./Dr.'];

		$col = $startingIndex-1;

		for ($g = 0; $g < $numGroups; $g++) {
			foreach ($columns as $header) {
				$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
				$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
				$col++;
			}
		}


		for ($g = 0; $g < $bsdnumGroups; $g++) {
			foreach ($bsd_columns as $header) {
				$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
				$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
				$col++;
			}
		}
		
		$dataValidation_state = $sheet->getCell('F2')->getDataValidation();
		$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
		$sheet->setDataValidation('F2:F2000', $dataValidation_state);
	

		$dataValidation = $sheet->getCell('C2')->getDataValidation();
		$dataValidation->setType(DataValidation::TYPE_LIST)
					   ->setFormula1('"' . $validValues . '"')
					   ->setShowDropDown(true); // To show the dropdown arrow
		$sheet->setDataValidation('C2:C2000', $dataValidation);

		// Fill data in the rows
		for ($k = 0; $k < count($dataArray); $k++) {
			// Set static values (columns A to N)
			$sheet->setCellValue('A' . ($k + 2), date('Y-m-d', strtotime('+ ' . $k . ' days')));
			$sheet->getStyle('A'.($k + 2))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
			$sheet->setCellValue('B' . ($k + 2), 'SALE');
			$sheet->setCellValue('C' . ($k + 2), 'SUPPLY (REGULAR)');
			$sheet->setCellValue('D' . ($k + 2), 'Main');
			$sheet->setCellValue('E' . ($k + 2), '1991' . $k . '/ 2023-24');
			$sheet->setCellValue('F' . ($k + 2), 'Punjab(03)');
			$sheet->setCellValue('G' . ($k + 2), 'IN');
			$sheet->setCellValue('H' . ($k + 2), 'Excepteur sint occaecat cupidatat non proide');
			$sheet->setCellValue('I' . ($k + 2), 'Party Account');
			$sheet->setCellValue('J' . ($k + 2), 'Party Amount');
			$sheet->setCellValue('K' . ($k + 2), 'IGST');
			$sheet->setCellValue('L' . ($k + 2), 'CGST');
			$sheet->setCellValue('M' . ($k + 2), 'SGST');
			$sheet->setCellValue('N' . ($k + 2), 'Cess');

		// Set Item data
		$colOffset = 0; // For Bill Sundry columns
			
			for ($g = 0; $g < $numGroups; $g++) {
				// Fill Item data
				for ($c = 0; $c < count($columns); $c++) {
					$columnValue = $dataArray[$k][$colOffset + $c];
					$currentColumn = $startingIndex + $colOffset + $c;
					$columnLetter = getColumnLetter($currentColumn - 1);
					$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
					
				}
				$colOffset += count($columns); // Move offset after Item columns
			}
			
			// Set Bill Sundry and Item data for each row
			$colOffset = $colOffset; // Continue from where Bill Sundry left off
			for ($g = 0; $g < $bsdnumGroups; $g++) {
				// Fill Bill Sundry data
				for ($c = 0; $c < count($bsd_columns); $c++) {
					$columnValue = $dataArray[$k][$colOffset + $c];
					$currentColumn = $startingIndex + $colOffset + $c;
					$columnLetter = getColumnLetter($currentColumn - 1);
					$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
					
				}
				$colOffset += count($bsd_columns); // Move offset after Bill Sundry columns
			}

			
		}

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Sales Non Item Single Row Format.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  

  function purchase_item_single_sample()
  {
	  $states_lists =   $this->TransactionModel->show_states_lists(1); 	
	$state_dropdownOptions = array_values($states_lists);  
	$validStatesValues = implode(',', $state_dropdownOptions);	
	
	  $dropdownOptions = array_values($this->SupplyTypes);
		$validValues = implode(',', $dropdownOptions);
		
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Purchase Item");
		// Create a new worksheet for the states list
		$statesSheet = $spreadsheet->createSheet();
		$statesSheet->setTitle('States');
		// Populate the "States" worksheet with the list of states in column A
		foreach ($state_dropdownOptions as $index => $state) {
			$statesSheet->setCellValue('A' . ($index + 1), $state);
		}
		$sheet->setCellValue('A1', 'Date');
		$sheet->setCellValue('B1', 'Type');
		$sheet->setCellValue('C1', 'Supply Type');
		$sheet->setCellValue('D1', 'Vch Series');
		$sheet->setCellValue('E1', 'Bill No.');
		$sheet->setCellValue('F1', 'MC');
		$sheet->setCellValue('G1', 'POS');
		$sheet->setCellValue('H1', 'Country Code');
		$sheet->setCellValue('I1', 'Long Narration');
		$sheet->setCellValue('J1', 'Party Account');
		$sheet->setCellValue('K1', 'Cr. Amount');
		$sheet->setCellValue('L1', 'IGST');
		$sheet->setCellValue('M1', 'CGST');
		$sheet->setCellValue('N1', 'SGST');
		$sheet->setCellValue('O1', 'Cess');
		$sheet->getStyle('A1:O1')->getFont()->setBold(true); // ID column bold
		$startingIndex = 16; // Start after the static columns

		// Function to generate random data for the Item columns
		function generateRandomData() {
			return [
				'Item Name' => 'Item ' . rand(1, 1000),
				'Amount' => 1000,
				'QTY' => rand(1, 100),
				'UQC' => 'Packs ' . rand(1, 50),
				'Short Narration' => 'Short Narration ' . rand(1, 1000)
			];
		}
		 function getColumnLetter($index) {
				$letters = '';
				while ($index >= 0) {
					$letters = chr($index % 26 + 65) . $letters;
					$index = floor($index / 26) - 1;
				}
				return $letters;
			}
		// Function to generate random data for Bill Sundry columns
		function generateRandombsdData() {
			return [
				'Bill Sundry' => 'Bill Sundry ' . rand(1, 1000),
				'Amount' => rand(1, 100),
				'Cr./Dr.' => 'Dr',
			];
		}

		// Create the dynamic array based on the number of rows and groups (columns)
		$dataArray = [];
		$numRows = 2; // Set number of rows you want to generate
		$bsdnumGroups = 2; // Number of Bill Sundry groups
		$numGroups = 15; // Number of item groups

		for ($i = 0; $i < $numRows; $i++) {
			$rowData = [];
		   
			// Add Item data for each group
			for ($k = 0; $k < $numGroups; $k++) {
				$randomData = generateRandomData();
				$rowData[] = $randomData['Item Name'];
				$rowData[] = $randomData['Amount'];
				$rowData[] = $randomData['QTY'];
				$rowData[] = $randomData['UQC'];
				$rowData[] = $randomData['Short Narration'];
			}
			
			 // Add Bill Sundry data for each group
			for ($g = 0; $g < $bsdnumGroups; $g++) {
				$randomData = generateRandombsdData();
				$rowData[] = $randomData['Bill Sundry'];
				$rowData[] = $randomData['Amount'];
				$rowData[] = $randomData['Cr./Dr.'];
			}
			$dataArray[] = $rowData;
		}

		$columns = ['Item Name', 'Item Amount', 'Item QTY', 'Item UQC', 'Item Short Narration'];
		$bsd_columns = ['Bill Sundry Name', 'Bill Sundry Amount', 'Bill Sundry Cr./Dr.'];

		$col = $startingIndex-1;

		for ($g = 0; $g < $numGroups; $g++) {
			foreach ($columns as $header) {
				$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
				$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
				$col++;
			}
		}


		for ($g = 0; $g < $bsdnumGroups; $g++) {
			foreach ($bsd_columns as $header) {
				$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
				$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
				$col++;
			}
		}
		
		$dataValidation_state = $sheet->getCell('G2')->getDataValidation();
		$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
		$sheet->setDataValidation('G2:G2000', $dataValidation_state);

		$dataValidation = $sheet->getCell('C2')->getDataValidation();
		$dataValidation->setType(DataValidation::TYPE_LIST)
					   ->setFormula1('"' . $validValues . '"')
					   ->setShowDropDown(true); // To show the dropdown arrow
		$sheet->setDataValidation('C2:C2000', $dataValidation);	

		// Fill data in the rows
		for ($k = 0; $k < count($dataArray); $k++) {
			// Set static values (columns A to N)
			$sheet->setCellValue('A' . ($k + 2), date('Y-m-d', strtotime('+ ' . $k . ' days')));
			$sheet->getStyle('A'.($k + 2))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
			$sheet->setCellValue('B' . ($k + 2), 'PURCHASE');
			$sheet->setCellValue('C' . ($k + 2), 'SUPPLY (REGULAR)');
			$sheet->setCellValue('D' . ($k + 2), 'Main');
			$sheet->setCellValue('E' . ($k + 2), '1991' . $k . '/ 2023-24');
			$sheet->setCellValue('F' . ($k + 2), 'Main');
			$sheet->setCellValue('G' . ($k + 2), 'Punjab(03)');
			$sheet->setCellValue('H' . ($k + 2), 'IN');
			$sheet->setCellValue('I' . ($k + 2), 'Excepteur sint occaecat cupidatat non proide');
			$sheet->setCellValue('J' . ($k + 2), 'Party Account');
			$sheet->setCellValue('K' . ($k + 2), rand(1200,2500));
			$sheet->setCellValue('L' . ($k + 2), 'IGST');
			$sheet->setCellValue('M' . ($k + 2), 'CGST');
			$sheet->setCellValue('N' . ($k + 2), 'SGST');
			$sheet->setCellValue('O' . ($k + 2), 'Cess');

		// Set Item data
		$colOffset = 0; // For Bill Sundry columns
			
			for ($g = 0; $g < $numGroups; $g++) {
				// Fill Item data
				for ($c = 0; $c < count($columns); $c++) {
					$columnValue = $dataArray[$k][$colOffset + $c];
					$currentColumn = $startingIndex + $colOffset + $c;
					$columnLetter = getColumnLetter($currentColumn - 1);
					$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
					
				}
				$colOffset += count($columns); // Move offset after Item columns
			}
			
			// Set Bill Sundry and Item data for each row
			$colOffset = $colOffset; // Continue from where Bill Sundry left off
			for ($g = 0; $g < $bsdnumGroups; $g++) {
				// Fill Bill Sundry data
				for ($c = 0; $c < count($bsd_columns); $c++) {
					$columnValue = $dataArray[$k][$colOffset + $c];
					$currentColumn = $startingIndex + $colOffset + $c;
					$columnLetter = getColumnLetter($currentColumn - 1);
					$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
					
				}
				$colOffset += count($bsd_columns); // Move offset after Bill Sundry columns
			}			
		}

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Purchase Item Single Row Format.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  
  function purchase_non_item_single_sample()
  {
	  $states_lists =   $this->TransactionModel->show_states_lists(1); 	
	$state_dropdownOptions = array_values($states_lists);  
	$validStatesValues = implode(',', $state_dropdownOptions);	
	
	  $dropdownOptions = array_values($this->SupplyTypes);
		$validValues = implode(',', $dropdownOptions);
		
	 	$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Purchase Item");
		
		// Create a new worksheet for the states list
		$statesSheet = $spreadsheet->createSheet();
		$statesSheet->setTitle('States');
		// Populate the "States" worksheet with the list of states in column A
		foreach ($state_dropdownOptions as $index => $state) {
			$statesSheet->setCellValue('A' . ($index + 1), $state);
		}

		$sheet->setCellValue('A1', 'Date');
		$sheet->setCellValue('B1', 'Type');
		$sheet->setCellValue('C1', 'Supply Type');
		$sheet->setCellValue('D1', 'Vch Series');
		$sheet->setCellValue('E1', 'Bill No.');
		$sheet->setCellValue('F1', 'POS');
		$sheet->setCellValue('G1', 'Country Code');
		$sheet->setCellValue('H1', 'Long Narration');
		$sheet->setCellValue('I1', 'Party Account');
		$sheet->setCellValue('J1', 'Party Amount');
		$sheet->setCellValue('K1', 'IGST');
		$sheet->setCellValue('L1', 'CGST');
		$sheet->setCellValue('M1', 'SGST');
		$sheet->setCellValue('N1', 'Cess');
		$sheet->getStyle('A1:N1')->getFont()->setBold(true); // ID column bold
		$startingIndex = 15; // Start after the static columns

		// Function to generate random data for the Item columns
		function generateRandomData() {
			return [
				'Account' => 'Account Name' . rand(1, 1000),
				'Amount' => rand(1000, 10000),
				'Short Narration' => 'Short Narration ' . rand(1, 1000)
			];
		}
		 function getColumnLetter($index) {
				$letters = '';
				while ($index >= 0) {
					$letters = chr($index % 26 + 65) . $letters;
					$index = floor($index / 26) - 1;
				}
				return $letters;
			}
		// Function to generate random data for Bill Sundry columns
		function generateRandombsdData() {
			return [
				'Bill Sundry' => 'Bill Sundry ' . rand(1, 1000),
				'Amount' => rand(1, 100),
				'Cr./Dr.' => 'Dr',
			];
		}

		// Create the dynamic array based on the number of rows and groups (columns)
		$dataArray = [];
		$numRows = 2; // Set number of rows you want to generate
		$bsdnumGroups = 2; // Number of Bill Sundry groups
		$numGroups = 15; // Number of item groups

		for ($i = 0; $i < $numRows; $i++) {
			$rowData = [];
		   
			// Add Item data for each group
			for ($k = 0; $k < $numGroups; $k++) {
				$randomData = generateRandomData();
				$rowData[] = $randomData['Account'];
				$rowData[] = $randomData['Amount'];
				$rowData[] = $randomData['Short Narration'];
			}
			
			 // Add Bill Sundry data for each group
			for ($g = 0; $g < $bsdnumGroups; $g++) {
				$randomData = generateRandombsdData();
				$rowData[] = $randomData['Bill Sundry'];
				$rowData[] = $randomData['Amount'];
				$rowData[] = $randomData['Cr./Dr.'];
			}
			$dataArray[] = $rowData;
		}

		$columns = ['Account Name', 'Account Amount','Account Short Narration'];
		$bsd_columns = ['Bill Sundry Name', 'Bill Sundry Amount', 'Bill Sundry Cr./Dr.'];

		$col = $startingIndex-1;

		for ($g = 0; $g < $numGroups; $g++) {
			foreach ($columns as $header) {
				$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
				$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
				$col++;
			}
		}


		for ($g = 0; $g < $bsdnumGroups; $g++) {
			foreach ($bsd_columns as $header) {
				$sheet->setCellValue(getColumnLetter($col) . '1', ($g+1).' '.$header);
				$sheet->getStyle(getColumnLetter($col) . '1')->getFont()->setBold(true); // Make headers bold
				$col++;
			}
		}
		
		$dataValidation_state = $sheet->getCell('F2')->getDataValidation();
		$dataValidation_state->setType(DataValidation::TYPE_LIST)
				   ->setFormula1('=States!$A$1:$A$' . count($state_dropdownOptions))  // Reference the "States" sheet
				   ->setShowDropDown(true);
		$sheet->setDataValidation('F2:F2000', $dataValidation_state);
		
		$dataValidation = $sheet->getCell('C2')->getDataValidation();
		$dataValidation->setType(DataValidation::TYPE_LIST)
					   ->setFormula1('"' . $validValues . '"')
					   ->setShowDropDown(true); // To show the dropdown arrow
		$sheet->setDataValidation('C2:C2000', $dataValidation);

		// Fill data in the rows
		for ($k = 0; $k < count($dataArray); $k++) {
			// Set static values (columns A to N)
			$sheet->setCellValue('A' . ($k + 2), date('Y-m-d', strtotime('+ ' . $k . ' days')));
			$sheet->getStyle('A'.($k + 2))->getNumberFormat()->setFormatCode('yyyy-mm-dd');
			$sheet->setCellValue('B' . ($k + 2), 'PURCHASE');
			$sheet->setCellValue('C' . ($k + 2), 'SUPPLY (REGULAR)');
			$sheet->setCellValue('D' . ($k + 2), 'Main');
			$sheet->setCellValue('E' . ($k + 2), '1991' . $k . '/ 2023-24');
			$sheet->setCellValue('F' . ($k + 2), 'Punjab(03)');
			$sheet->setCellValue('G' . ($k + 2), 'IN');
			$sheet->setCellValue('H' . ($k + 2), 'Excepteur sint occaecat cupidatat non proide');
			$sheet->setCellValue('I' . ($k + 2), 'Party Account');
			$sheet->setCellValue('J' . ($k + 2), rand(1500,8450));
			$sheet->setCellValue('K' . ($k + 2), 'IGST');
			$sheet->setCellValue('L' . ($k + 2), 'CGST');
			$sheet->setCellValue('M' . ($k + 2), 'SGST');
			$sheet->setCellValue('N' . ($k + 2), 'Cess');

		// Set Item data
		$colOffset = 0; // For Bill Sundry columns
			
			for ($g = 0; $g < $numGroups; $g++) {
				// Fill Item data
				for ($c = 0; $c < count($columns); $c++) {
					$columnValue = $dataArray[$k][$colOffset + $c];
					$currentColumn = $startingIndex + $colOffset + $c;
					$columnLetter = getColumnLetter($currentColumn - 1);
					$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
					
				}
				$colOffset += count($columns); // Move offset after Item columns
			}
			
			// Set Bill Sundry and Item data for each row
			$colOffset = $colOffset; // Continue from where Bill Sundry left off
			for ($g = 0; $g < $bsdnumGroups; $g++) {
				// Fill Bill Sundry data
				for ($c = 0; $c < count($bsd_columns); $c++) {
					$columnValue = $dataArray[$k][$colOffset + $c];
					$currentColumn = $startingIndex + $colOffset + $c;
					$columnLetter = getColumnLetter($currentColumn - 1);
					$sheet->setCellValue($columnLetter . ($k + 2), $columnValue);
					
				}
				$colOffset += count($bsd_columns); // Move offset after Bill Sundry columns
			}

			
		}

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Purchase Non Item Single Row Format.xlsx"'); 

    $writer->save('php://output'); 
    die();  
  }
  
  
  function createMaster()
  {
    if($this->request->getMethod() == 'post')
    {
      $rules = [        
        'module' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Module is required',
          ],
        ],
        'master' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Master is required',
          ],
        ],
        'sub_master' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Sub Master is required',
          ],
        ],
      ];
      
      if(!$this->validate($rules)){
        $errors = $this->validator->getErrors();
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
      }

      $post = $this->request->getPost();
      $impexp_sr_id = $this->ImportExportModel->insertExportMaster($post);

      $impexp_type = $this->ImportExportModel->get_exp_type_id($post);

      return json_encode(['status' => true, 'impexp_sr_id' => $impexp_sr_id, 'impexp_type' => $impexp_type]);
    }
  }

  function get_export_master_list($type='')
  {
    $list =$this->ImportExportModel->get_export_master_list($type);

    return json_encode(['status' => true, 'list' => $list]);
  }

  function upload_master_data()
  {
    if($this->request->getMethod() == 'post')
    {

      $rules = [        
        'import_file' => [
          'rules'  => 'uploaded[import_file]|max_size[import_file,1024]',
          'errors' => [
            'uploaded'  => 'File size must be less than 1024kb',
            'ext_in'=>'File must be of type xlsx, xls or csv',
            'max_size'  => 'Size must be less than 1024kb'
          ],
        ],
        'impexp_sr_id' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Something went wrong...',
          ],
        ],
        'impexp_type' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Something went wrong...',
          ],
        ],
        
      ];
      
    if(!$this->validate($rules)){
      $errors = $this->validator->getErrors();
      return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
    }

    $impexp_sr_id = $this->request->getVar('impexp_sr_id');
    $impexp_type = $this->request->getVar('impexp_type');
    $account = $this->request->getVar('account'); // type-7

    $arr = explode(".", $_FILES["import_file"]["name"]);
    $extension = end($arr); 
    $allowed_extension = array("xls", "xlsx", "csv");

    if(!in_array($extension, $allowed_extension)){
      return json_encode(['status' => false, 'message' => 'Invalid file extension']);
    }

    $file = $_FILES["import_file"]["tmp_name"];
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($file);
    $worksheet = $spreadsheet->getActiveSheet();  
    $result = $worksheet->toArray();
	
	
	

    $this->ImportExportModel->deleteMaster($impexp_sr_id,$impexp_type);

    if($impexp_type == 1)
    {
      $i = 1;
      foreach($result as $key => $value)
      {
        if($key != 0)
        {   
           $final = [
              'impexp_sr_id'       => $impexp_sr_id,
              'imp_id'             => $i,
              'impacc_name'        => clean($value[0] ?? ''),  
              'impacc_alias'       => clean($value[1] ?? ''),
              'impacc_print'       => clean($value[2] ?? ''),
              'impacc_vendor_code' => clean($value[3] ?? ''), 
              'impacc_primary'     => clean($value[4] ?? ''),  
              'impacc_prt'         => clean($value[5] ?? ''), 
              'impacc_grp'         => clean($value[6] ?? ''),
              'imp_status'         => 0 
          ];
          $i++;

          $this->ImportExportModel->insertExcelMaster($impexp_type,$final);    
        }
      }
    }

    if($impexp_type == 2)
    {
      $i = 1;
      foreach($result as $key => $value)
      {
        if($key != 0)
        {   
          $final = [
            'impexp_sr_id'            => $impexp_sr_id,
            'imp_id'                  => $i,
            'impaccgrp_name'          => clean($value[0] ?? ''), 
            'impaccgrp_alias'         => clean($value[1] ?? ''),
            'impaccgrp_under_prt'     => clean($value[2] ?? ''), 
            'impaccgrp_under_grp'     => clean($value[3] ?? ''),
            'imp_status'              => 0 
          ];
          $i++;

          $this->ImportExportModel->insertExcelMaster($impexp_type,$final);    
        }
      }
    }

    if($impexp_type == 3)
    {
      $i = 1;
      foreach($result as $key => $value)
      {
        if($key != 0)
        {   
           $final = [
              'impexp_sr_id'         => $impexp_sr_id,
              'imp_id'               => $i,
              'impitm_name'          => clean($value[0] ?? ''),  
              'impitm_alias'         => clean($value[1] ?? ''), 
              'impitm_print'         => clean($value[2] ?? ''),
              'impitm_grp'           => clean($value[3] ?? ''),
              'impitm_cat'           => clean($value[4] ?? ''),
              'impitm_sale_acc'      => clean($value[5] ?? ''),
              'impitm_pur_acc'       => clean($value[6] ?? ''),
              'impitm_unit'          => clean($value[7] ?? ''),
              'impitm_val_method'    => clean($value[8] ?? ''),
              'impitm_mrp'           => clean($value[9] ?? ''),
			  'impitm_upc'           => clean($value[10] ?? ''),
              'imp_status'           => 0 
          ];
          $i++;

          $this->ImportExportModel->insertExcelMaster($impexp_type,$final);    
        }
      }
    }

    if($impexp_type == 4)
    {
      $i = 1;
      foreach($result as $key => $value)
      {
        if($key != 0)
        {   
          $final = [
            'impexp_sr_id'            => $impexp_sr_id,
            'imp_id'                  => $i,
            'impitmgrp_name'          => clean($value[0] ?? ''), 
            'impitmgrp_alias'         => clean($value[1] ?? ''), 
            'impitmgrp_under_grp'     => clean($value[2] ?? ''),
            'imp_status'              => 0 
          ];
          $i++;

          $this->ImportExportModel->insertExcelMaster($impexp_type,$final);    
        }
      }
    }

    if($impexp_type == 5)
    {
      $i = 1;
      foreach($result as $key => $value)
      {
        if($key != 0)
        {   
          $final = [
            'impexp_sr_id'            => $impexp_sr_id,
            'imp_id'                  => $i,
            'impitmcat_name'          => clean($value[0] ?? ''), 
            'impitmcat_alias'         => clean($value[1] ?? ''),
            'imp_status'              => 0 
          ];
          $i++;

          $this->ImportExportModel->insertExcelMaster($impexp_type,$final);    
        }
      }
    }

    if($impexp_type == 6) // day book
    {
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $details_arr = [];

      array_shift($result); // remove first array element
      foreach($result as $key => $value)
      {
        
        $date = clean($value[0] ?? '');
        $type = clean($value[1] ?? '');
        $series = clean($value[2] ?? '');
        $no = clean($value[3] ?? '');

        $account = clean($value[4] ?? '');
        $debit = clean($value[5] ?? '');
        $credit = clean($value[6] ?? '');
        $short_narr = clean($value[7] ?? '');

        $debit = str_replace(",","",$debit);
        $credit = str_replace(",","",$credit);

        if($date != ''){

          if(isset($result[$key-1])){
            $final[] = [
              'imptxnvch_date'      => $imptxnvch_date,
              'imptxnvch_type'      => $imptxnvch_type,
              'imptxnvch_series'    => $imptxnvch_series,
              'imptxnvch_bill_no'   => $imptxnvch_bill_no,
              'imptxnvch_long_narr' => $imptxnvch_long_narr,
              'details_arr'         => $details_arr,
            ];

            $imptxnvch_date = '';
            $imptxnvch_type = '';
            $imptxnvch_series = '';
            $imptxnvch_bill_no = '';
            $imptxnvch_long_narr = '';
            $details_arr = [];
          }

          $imptxnvch_date = $date;
          $imptxnvch_type = $type;
          $imptxnvch_series = $series;
          $imptxnvch_bill_no = $no;
        }

        if((empty($debit) || $debit == 0) && (empty($credit) || $credit == 0)){
          if(!empty($account)){
            $imptxnvch_long_narr .= $account.' ';
          }
        }
        else{
          $details_arr[] = [
            'imptxnvch_acc_bds'     => $account,
            'imptxnvch_amt_dr'      => floatval($debit),
            'imptxnvch_amt_cr'      => floatval($credit),
            'imptxnvch_short_narr'  => $short_narr,
          ];
        }
      }

      $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imp_id'             => $i,
            'imptxnvch_date'     => $value['imptxnvch_date'],
            'imptxnvch_type'     => $value['imptxnvch_type'],
            'imptxnvch_series'   => $value['imptxnvch_series'],
            'imptxnvch_bill_no'  => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],
            'imp_status'         => 0 
        ];
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert); 

        $j = 1;
        foreach ($value['details_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }   
        $i++;
      }
    }

    if($impexp_type == 7) // bank statement
    {
      $final = [];
  
      array_shift($result); // remove first array element
      $account1 = $account; // post

      foreach($result as $key => $value)
      {
        $date = clean($value[0] ?? '');
        $desc = clean($value[1] ?? '');
        $credit = clean($value[2] ?? ''); // dr_amt
        $debit = clean($value[3] ?? ''); // cr_amt
        $account2 = clean($value[4] ?? '');
        $series = clean($value[5] ?? '');
        $type = '';

        $debit = str_replace(",","",$debit);
        $credit = str_replace(",","",$credit);

        if(!empty($credit) && $credit != 0){
          $type = 'PYMT';
          $debit = '';
        }
        if(!empty($debit) && $debit != 0){
          $type = 'RCPT';
          $credit = '';
        }

        $details_arr = [];

        $details_arr[] = [
          'imptxnvch_acc_bds'     => $account1,
          'imptxnvch_amt_dr'      => floatval($debit),
          'imptxnvch_amt_cr'      => floatval($credit),
          'imptxnvch_short_narr'  => '',
        ];
        $details_arr[] = [
          'imptxnvch_acc_bds'     => $account2,
          'imptxnvch_amt_dr'      => floatval($credit),
          'imptxnvch_amt_cr'      => floatval($debit),
          'imptxnvch_short_narr'  => '',
        ];

        
        $final[] = [
          'imptxnvch_date'      => $date,
          'imptxnvch_type'      => $type,
          'imptxnvch_series'    => $series,
          'imptxnvch_bill_no'   => '',
          'imptxnvch_long_narr' => $desc,
          'details_arr'         => $details_arr,
        ];
      }

      $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imp_id'             => $i,
            'imptxnvch_date'     => $value['imptxnvch_date'],
            'imptxnvch_type'     => $value['imptxnvch_type'],
            'imptxnvch_series'   => $value['imptxnvch_series'],
            'imptxnvch_bill_no'  => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],
            'imp_status'         => 0 
        ];
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert); 

        $j = 1;
        foreach ($value['details_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }   
        $i++;
      }
    }

    if($impexp_type == 8) // sale without item
    {
		$SupplyTypesData = $this->SupplyTypes;
		$states_lists    = $this->TransactionModel->show_states_lists(1);
		
		$taxmasters = $this->ImportExportModel->GetTaxMastersInfo('sale');
		$igst_column_value = clean($taxmasters['igst_master_value'] ?? '');
		$cgst_column_value = clean($taxmasters['cgst_master_value'] ?? '');
		$sgst_column_value = clean($taxmasters['sgst_master_value'] ?? '');
		$cess_column_value = clean($taxmasters['cess_master_value'] ?? '');
		
	   $import_format_info = $this->ImportExportModel->GetimpexpmstnInfo($impexp_sr_id,$impexp_type);	
	  if($import_format_info['impexp_format']=='Single Row Format'){
		
		
		$finalData = [];
		$finalData = [];
	$itemIndexes = [
		'item_name' => [],
		'amount' => [],
		'short_narration' => []
	];
	$bsdIndexes = [
		'bill_sundry_name' => [],
		'amount'           => [],
		'cr_dr'           => []
	];


    foreach ($result as $rowIndex => $row) {
		if ($rowIndex === 0) {
       // Identifying column indexes based on header names
        foreach ($row as $colIndex => $colValue) {
			if($colValue!=''){
            $colValueLower = strtolower($colValue); // Convert to lowercase for case-insensitive search
			// Item-related headers
            if ( strpos($colValueLower, 'account name') !== false) {
                $itemIndexes['item_name'][] = $colIndex;
            } elseif ( strpos($colValueLower, 'account amount') !== false) {
                $itemIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'account short narration') !== false) {
                $itemIndexes['short_narration'][] = $colIndex;
            }
			
		 // Bill Sundry headers
            if (strpos($colValueLower, 'bill sundry name') !== false) {
                $bsdIndexes['bill_sundry_name'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry amount') !== false ) {
                $bsdIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry cr./dr.') !== false) {
                $bsdIndexes['cr_dr'][] = $colIndex;
            }	
        }
		}
        continue; // Skip header row      
    }
        $rowData = [];
		$supplytype = '';
    if (isset($row[2]) && $row[2] != '') {
        $supplytype = clean(array_search(trim($row[2]), $SupplyTypesData) ?? '');
    }
    $pos = '';
    if (isset($row[5]) && $row[5] != '') {
        $pos = clean(array_search(trim($row[5]), $states_lists) ?? '');
    }
        $rowData = [
			'date'           => clean($row[0] ?? ''),
			'type'           => clean($row[1] ?? ''),
			'supply_type'    => $supplytype,
			'vch_series'     => clean($row[3] ?? ''),
			'bill_no'        => clean($row[4] ?? ''),
			'pos'            => $pos,
			'country_code'   => clean($row[6] ?? ''),
			'long_narration' => clean($row[7] ?? ''),		
			'party_account'  => clean($row[8] ?? ''),
			'party_amount'   => clean($row[9] ?? ''),
			'igst'           => clean($row[10] ?? ''),
			'cgst'           => clean($row[11] ?? ''),
			'sgst'           => clean($row[12] ?? ''),
			'cess'           => clean($row[13] ?? '')
		];


// Extract Item columns dynamically
    $items = [];
    foreach ($itemIndexes['item_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $items[] = [
                'item'            => clean($row[$index] ?? ''),
                'amount'          => clean($row[$itemIndexes['amount'][$i] ?? ''] ?? ''),
                'short_narration' => clean($row[$itemIndexes['short_narration'][$i] ?? ''] ?? '')
            ];
        }
    }
	
	

    // Extract bill sundry data dynamically
    $billSundryNames = [];
    foreach ($bsdIndexes['bill_sundry_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $billSundryNames[] = [
                'name'             => clean($row[$index] ?? ''),
                'amount'           => clean($row[$bsdIndexes['amount'][$i] ?? ''] ?? ''),
                'cr_dr'            => clean($row[$bsdIndexes['cr_dr'][$i] ?? ''] ?? '')
            ];
        }
    }
	
	
       

        // Adding both items and billSundryNames arrays to the main row data
        $rowData['items'] = $items;
        $rowData['bill_sundry_names'] = $billSundryNames;

        // Store the row data
        $finalData[] = $rowData;
    }
	
	
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';
      $imptxnvch_uqc = '';
      $imptxnvch_qty = '';
      $imptxnvch_mc = '';
     
      
   if($finalData){
	  foreach($finalData as $key => $value){
		 $acc_arr = []; 
         $itm_arr = [];		 
		$imptxnvch_date         = $value['date'];
		$imptxnvch_type         = $value['type'];
		$imptxnvch_supply_type  = $value['supply_type'];
		$imptxnvch_series       = $value['vch_series'];
		$imptxnvch_bill_no      = $value['bill_no'];
		$imptxnvch_long_narr    = $value['long_narration'];
		$imptxnvch_pos          = $value['pos'];
		$imptxnvch_country_code = $value['country_code'];
		$party_account          = $value['party_account'];	
		$party_amount           = $value['party_amount'];	
		$igst         		    = $value['igst'];	
		$cgst         		    = $value['cgst'];
		$sgst         		    = $value['sgst'];
		$cess         		    = $value['cess'];		
		$items_data             = $value['items'];
		$bsd_data               = $value['bill_sundry_names'];
						
		$acc_arr[] = [
		  'imptxnvch_acc_bds'     => $party_account,
		  'imptxnvch_amt_dr'      => floatval($party_amount),
		  'imptxnvch_amt_cr'      => floatval(0),
		  'imptxnvch_short_narr'  => ' ',
		  'imptxnvch_mst_type'    => 'acc',
		  'imptxnvch_pary_acc'    => '1'
		];
		if($items_data){
			foreach($items_data as $item_row){				
					$acc_arr[] = [
					 'imptxnvch_acc_bds'     => $item_row['item'],
					 'imptxnvch_amt_dr'      => floatval(0),
					 'imptxnvch_amt_cr'      => floatval($item_row['amount']),
					 'imptxnvch_short_narr'  => $item_row['short_narration'],
					 'imptxnvch_mst_type'    => 'acc',
					 'imptxnvch_pary_acc'    => '0'
					]; 
			   }
		   }
		if($bsd_data){
			foreach($bsd_data as $bsd_row){		
                    if(strtolower($bsd_row['cr_dr'])=='cr'){
						$imptxnvch_amt_dr = floatval(0);
						$imptxnvch_amt_cr = floatval($bsd_row['amount']);
					}
				    if(strtolower($bsd_row['cr_dr'])=='dr'){
						$imptxnvch_amt_dr = floatval($bsd_row['amount']);
						$imptxnvch_amt_cr = floatval(0);
					}					
					$acc_arr[] = [
							  'imptxnvch_acc_bds'     => $bsd_row['name'],
							  'imptxnvch_amt_dr'      => $imptxnvch_amt_dr,
							  'imptxnvch_amt_cr'      => $imptxnvch_amt_cr,
							  'imptxnvch_short_narr'  => ' ',
							  'imptxnvch_mst_type'    => 'bsd',
							  'imptxnvch_pary_acc'    => '0'
							];
			   }
		   }   
		   
		    $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $igst_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($igst),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'    => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
	
			 $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cgst_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($cgst),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'    => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
					
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $sgst_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($sgst),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'    => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cess_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($cess),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'    => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
					
				$final[] = [
				  'imptxnvch_date'      => $imptxnvch_date,
				  'imptxnvch_type'      => $imptxnvch_type,
				  'imptxnvch_series'    => $imptxnvch_series,
				  'imptxnvch_bill_no'   => $imptxnvch_bill_no,
				  'imptxnvch_long_narr' => $imptxnvch_long_narr,
				  'imptxnvch_pos'       => $imptxnvch_pos,
				  'imptxnvch_country_code' => $imptxnvch_country_code,
				  'imptxnvch_supply_type' => $imptxnvch_supply_type,
				  'acc_arr'             => $acc_arr,
				  'itm_arr'             => $itm_arr,
				];
			
	       } 
        }

	  $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imptxnvch_date'      => $value['imptxnvch_date'],
            'imptxnvch_type'      => $value['imptxnvch_type'],
            'imptxnvch_series'    => $value['imptxnvch_series'],
            'imptxnvch_bill_no'   => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],           
            'imp_status'          => 0 
          ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impoutsup_pos'      => $value['imptxnvch_pos'],
            'impoutsup_country_code' => $value['imptxnvch_country_code'],
			'impout_supply_type' => $value['imptxnvch_supply_type'],
        ];

        $this->ImportExportModel->insertExcelSubSideMaster($impexp_type,$insert1);

        $j = 1;
        foreach ($value['acc_arr'] as $key2 => $value2) {
          $insert2 = [
		    'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
			'imptxnvch_mst_type'  => $value2['imptxnvch_mst_type'],
			'imptxnvch_pary_acc'  => $value2['imptxnvch_pary_acc'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }

        $i++;
      }

	  }	
	if($import_format_info['impexp_format']=='Multi Row Format'|| $import_format_info['impexp_format']==''){	
		
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
	  $imptxnvch_supplytype = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';
      $details_arr = [];

      array_shift($result); // remove first array element
      foreach ($result as $key => $value) {
    // Extract and clean values from the array
    $date = clean($value[0] ?? '');
    $type = clean($value[1] ?? '');
    $supplytype = '';
    if (isset($value[2]) && $value[2] != '') {
        $supplytype = clean(array_search(trim($value[2]), $SupplyTypesData) ?? '');
    }
    $pos = '';
    if (isset($value[9]) && $value[9] != '') {
        $pos = clean(array_search(trim($value[9]), $states_lists) ?? '');
    }
    $series = clean($value[3] ?? '');
    $no = clean($value[4] ?? '');
    $account = clean($value[5] ?? '');
    $debit = clean($value[6] ?? '');
    $credit = clean($value[7] ?? '');
    $short_narr = clean($value[8] ?? '');
    $country_code = clean($value[10] ?? '');

    // Clean debit and credit values to ensure they are in numeric format
    $debit = str_replace(",", "", $debit);
    $credit = str_replace(",", "", $credit);

    // If the date is not empty, process the transaction
    if ($date != '') {
        // If this is not the first row and the date has changed, save the previous record and reset
        if (!empty($imptxnvch_date) && $imptxnvch_date !== $date) {
            // Only add if we have valid data for this record
            if ($imptxnvch_date && $imptxnvch_type) {
                $final[] = [
                    'imptxnvch_date' => $imptxnvch_date,
                    'imptxnvch_type' => $imptxnvch_type,
                    'imptxnvch_supplytype' => $imptxnvch_supplytype,
                    'imptxnvch_series' => $imptxnvch_series,
                    'imptxnvch_bill_no' => $imptxnvch_bill_no,
                    'imptxnvch_long_narr' => $imptxnvch_long_narr,
                    'imptxnvch_pos' => $imptxnvch_pos,
                    'imptxnvch_country_code' => $imptxnvch_country_code,
                    'details_arr' => $details_arr,
                ];
            }

            // Reset the variables for the next record
            $details_arr = [];
        }

        // Update the variables for the current record
        $imptxnvch_date = $date;
        $imptxnvch_type = $type;
        $imptxnvch_supplytype = $supplytype;
        $imptxnvch_series = $series;
        $imptxnvch_bill_no = $no;
        $imptxnvch_pos = $pos;
        $imptxnvch_country_code = $country_code;
    }

    // Handle narrative data and account details
    if ((empty($debit) || $debit == 0) && (empty($credit) || $credit == 0)) {
        // If both debit and credit are 0, accumulate narrative (if any)
        if (!empty($account)) {
            $imptxnvch_long_narr .= $account . ' ';
        }
    } else {
        // Otherwise, add the account details to the details array
        $details_arr[] = [
            'imptxnvch_acc_bds' => $account,
            'imptxnvch_amt_dr' => floatval($debit),
            'imptxnvch_amt_cr' => floatval($credit),
            'imptxnvch_short_narr' => $short_narr,
        ];
    }
}

// After the loop, add the last record to the final array
if (!empty($imptxnvch_date) && !empty($imptxnvch_type)) {
    $final[] = [
        'imptxnvch_date' => $imptxnvch_date,
        'imptxnvch_type' => $imptxnvch_type,
        'imptxnvch_supplytype' => $imptxnvch_supplytype,
        'imptxnvch_series' => $imptxnvch_series,
        'imptxnvch_bill_no' => $imptxnvch_bill_no,
        'imptxnvch_long_narr' => $imptxnvch_long_narr,
        'imptxnvch_pos' => $imptxnvch_pos,
        'imptxnvch_country_code' => $imptxnvch_country_code,
        'details_arr' => $details_arr,
    ];
}

      $i = 1;	 
	  
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'         => $impexp_sr_id,
            'imp_id'               => $i,
            'imptxnvch_date'       => $value['imptxnvch_date'],
            'imptxnvch_type'       => $value['imptxnvch_type'],
			'imptxnvch_series'     => $value['imptxnvch_series'],
            'imptxnvch_bill_no'    => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr'  => $value['imptxnvch_long_narr'],
            'imp_status'           => 0 
        ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impoutsup_pos'      => $value['imptxnvch_pos'],
            'impoutsup_country_code' => $value['imptxnvch_country_code'],
			'impout_supply_type' => $value['imptxnvch_supplytype'],
        ];

        $this->ImportExportModel->insertExcelSubSideMaster($impexp_type,$insert1);

        $j = 1;
        foreach ($value['details_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }   
        $i++;
        }
      }
	}

    if($impexp_type == 9) // sale item
    {
		$SupplyTypesData = $this->SupplyTypes;		
		$states_lists    = $this->TransactionModel->show_states_lists(1);
		
		$taxmasters = $this->ImportExportModel->GetTaxMastersInfo('sale');
		$igst_column_value = clean($taxmasters['igst_master_value'] ?? '');
		$cgst_column_value = clean($taxmasters['cgst_master_value'] ?? '');
		$sgst_column_value = clean($taxmasters['sgst_master_value'] ?? '');
		$cess_column_value = clean($taxmasters['cess_master_value'] ?? '');
		
		
	  $import_format_info = $this->ImportExportModel->GetimpexpmstnInfo($impexp_sr_id,$impexp_type);
	  if($import_format_info['impexp_format']=='Single Row Format'){
		
    $finalData = [];
	$itemIndexes = [
		'item_name' => [],
		'amount' => [],
		'qty' => [],
		'uqc' => [],
		'short_narration' => []
	];
	$bsdIndexes = [
		'bill_sundry_name' => [],
		'amount'           => [],
		'cr_dr'           => []
	];
foreach ($result as $rowIndex => $row) {
    if ($rowIndex === 0) {
		
		
       // Identifying column indexes based on header names
        foreach ($row as $colIndex => $colValue) {
			if($colValue!=''){
            $colValueLower = strtolower($colValue); // Convert to lowercase for case-insensitive search
			// Item-related headers
            if (strpos($colValueLower, 'item name') !== false) {
                $itemIndexes['item_name'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item amount') !== false) {
                $itemIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item qty') !== false) {
                $itemIndexes['qty'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item uqc') !== false) {
                $itemIndexes['uqc'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item short narration') !== false) {
                $itemIndexes['short_narration'][] = $colIndex;
            }
			
		 // Bill Sundry headers
            if (strpos($colValueLower, 'bill sundry name') !== false) {
                $bsdIndexes['bill_sundry_name'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry amount') !== false ) {
                $bsdIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry cr./dr.') !== false) {
                $bsdIndexes['cr_dr'][] = $colIndex;
            }	
        }}
        continue; // Skip header row      
    }
	
if (isset($row[2]) && $row[2] != '') {
        $supplytype = clean(array_search(trim($row[2]), $SupplyTypesData) ?? '');
    }
    $pos = '';
    if (isset($row[6]) && $row[6] != '') {
        $pos = clean(array_search(trim($row[6]), $states_lists) ?? '');
    }
	
    $rowData = [
        'date'           => clean($row[0] ?? ''),
        'type'           => clean($row[1] ?? ''),
        'supply_type'    => $supplytype,
        'vch_series'     => clean($row[3] ?? ''),
        'bill_no'        => clean($row[4] ?? ''),
        'mc'             => clean($row[5] ?? ''),
        'pos'            => $pos,
        'country_code'   => clean($row[7] ?? ''),
        'long_narration' => clean($row[8] ?? ''),
        'party_account'  => clean($row[9] ?? ''),
        'party_amount'   => clean($row[10] ?? ''),
        'igst'           => clean($row[11] ?? ''),
        'cgst'           => clean($row[12] ?? ''),
        'sgst'           => clean($row[13] ?? ''),
        'cess'           => clean($row[14] ?? '')
    ];

    // Extract Item columns dynamically
    $items = [];
    foreach ($itemIndexes['item_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $items[] = [
                'item_name'       => clean($row[$index] ?? ''),
                'amount'          => clean($row[$itemIndexes['amount'][$i] ?? ''] ?? ''),
                'qty'             => clean($row[$itemIndexes['qty'][$i] ?? ''] ?? ''),
                'uqc'             => clean($row[$itemIndexes['uqc'][$i] ?? ''] ?? ''),
                'short_narration' => clean($row[$itemIndexes['short_narration'][$i] ?? ''] ?? '')
            ];
        }
    }
	
	

    // Extract bill sundry data dynamically
    $billSundryNames = [];
    foreach ($bsdIndexes['bill_sundry_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $billSundryNames[] = [
                'bill_sundry_name' => clean($row[$index] ?? ''),
                'amount'           => clean($row[$bsdIndexes['amount'][$i] ?? ''] ?? ''),
                'cr_dr'            => strtolower(clean($row[$bsdIndexes['cr_dr'][$i] ?? ''] ?? ''))
            ];
        }
    }

    // Adding both items and billSundryNames arrays to the main row data
    $rowData['items'] = $items;
    $rowData['bill_sundry_names'] = $billSundryNames;

    // Store the row data
    $finalData[] = $rowData;
}
	
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';
      $imptxnvch_uqc = '';
      $imptxnvch_qty = '';
      $imptxnvch_mc = '';
     
      
   if($finalData){
	  foreach($finalData as $key => $value){
		 $acc_arr = []; 
         $itm_arr = [];		 
		$imptxnvch_date         = $value['date'];
		$imptxnvch_type         = $value['type'];
		$imptxnvch_supply_type  = $value['supply_type'];
		$imptxnvch_series       = $value['vch_series'];
		$imptxnvch_bill_no      = $value['bill_no'];
		$imptxnvch_long_narr    = $value['long_narration'];
		$imptxnvch_pos          = $value['pos'];
		$imptxnvch_country_code = $value['country_code'];
		$mc                     = $value['mc'];	
		$party_account          = $value['party_account'];	
		$party_amount           = $value['party_amount'];	
		$igst         		    = $value['igst'];	
		$cgst         		    = $value['cgst'];
		$sgst         		    = $value['sgst'];
		$cess         		    = $value['cess'];		
		$items_data             = $value['items'];
		$bsd_data               = $value['bill_sundry_names'];
						
		$acc_arr[] = [
		  'imptxnvch_acc_bds'     => $party_account,
		  'imptxnvch_amt_dr'      => floatval($party_amount),
		  'imptxnvch_amt_cr'      => floatval(0),
		  'imptxnvch_short_narr'  => ' ',
		  'imptxnvch_mst_type'        => 'acc'
		];
		if($items_data){
			foreach($items_data as $item_row){				
					$itm_arr[] = [
					  'imptxnvch_item'        => $item_row['item_name'],
					  'imptxnvch_amt_dr'      => '',
					  'imptxnvch_amt_cr'      => floatval($item_row['amount']),
					  'imptxnvch_uqc_dr'      => '',
					  'imptxnvch_uqc_cr'      => $item_row['uqc'],
					  'imptxnvch_qty_dr'      => '',
					  'imptxnvch_qty_cr'      => floatval($item_row['qty']),
					  'imptxnvch_mc_dr'       => '',
					  'imptxnvch_mc_cr'       => $mc,
					  'imptxnvch_short_narr'  => $item_row['short_narration'],
					]; 
			   }
		   }
		if($bsd_data){
			foreach($bsd_data as $bsd_row){		
                    if(strtolower($bsd_row['cr_dr'])=='cr'){
						$imptxnvch_amt_dr = floatval(0);
						$imptxnvch_amt_cr = floatval($bsd_row['amount']);
					}
				   else if(strtolower($bsd_row['cr_dr'])=='dr'){
						$imptxnvch_amt_dr = floatval($bsd_row['amount']);
						$imptxnvch_amt_cr = floatval(0);
					}else{
					$imptxnvch_amt_dr = floatval(0);
						$imptxnvch_amt_cr = floatval(0);	
					}					
					$acc_arr[] = [
							  'imptxnvch_acc_bds'     => $bsd_row['bill_sundry_name'],
							  'imptxnvch_amt_dr'      => $imptxnvch_amt_dr,
							  'imptxnvch_amt_cr'      => $imptxnvch_amt_cr,
							  'imptxnvch_short_narr'  => 'n/a',
							  'imptxnvch_mst_type'        => 'bsd'
							];
			   }
		   }   
		   
		    $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $igst_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($igst),
					  'imptxnvch_short_narr'  => ' ',
					   'imptxnvch_mst_type'        => 'tax'
					];
	
			 $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cgst_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($cgst),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax'
					];
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $sgst_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($sgst),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax'
					];
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cess_column_value,
					  'imptxnvch_amt_dr'      => floatval(0),
					  'imptxnvch_amt_cr'      => floatval($cess),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax'
					];
					
				$final[] = [
				  'imptxnvch_date'      => $imptxnvch_date,
				  'imptxnvch_type'      => $imptxnvch_type,
				  'imptxnvch_series'    => $imptxnvch_series,
				  'imptxnvch_bill_no'   => $imptxnvch_bill_no,
				  'imptxnvch_long_narr' => $imptxnvch_long_narr,
				  'imptxnvch_pos'       => $imptxnvch_pos,
				  'imptxnvch_country_code' => $imptxnvch_country_code, 
				  'imptxnvch_supply_type' => $imptxnvch_supply_type,
				  'imptxnvch_mc'        => $mc,
				  'acc_arr'             => $acc_arr,
				  'itm_arr'             => $itm_arr,
				];
			
	       } 
        }

	  $i = 1;
	 
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imptxnvch_date'      => $value['imptxnvch_date'],
            'imptxnvch_type'      => $value['imptxnvch_type'],
            'imptxnvch_series'    => $value['imptxnvch_series'],
            'imptxnvch_bill_no'   => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],
            'imptxnvch_mc'        => $value['imptxnvch_mc'],
            'imp_status'          => 0 
          ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impoutsup_pos'      => $value['imptxnvch_pos'],
            'impoutsup_country_code' => $value['imptxnvch_country_code'],
			'impout_supply_type' => $value['imptxnvch_supply_type'],
        ];

        $this->ImportExportModel->insertExcelSubSideMaster($impexp_type,$insert1);

        $j = 1;
        foreach ($value['acc_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
			'imptxnvch_mst_type'=> $value2['imptxnvch_mst_type'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }

        $k = 1;
        foreach ($value['itm_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $k,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_item'      => $value2['imptxnvch_item'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_uqc_dr'    => $value2['imptxnvch_uqc_dr'],
            'imptxnvch_uqc_cr'    => $value2['imptxnvch_uqc_cr'],
            'imptxnvch_qty_dr'    => $value2['imptxnvch_qty_dr'],
            'imptxnvch_qty_cr'    => $value2['imptxnvch_qty_cr'],
            'imptxnvch_mc_dr'     => $value2['imptxnvch_mc_dr'],
            'imptxnvch_mc_cr'     => $value2['imptxnvch_mc_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
          ];
          $this->ImportExportModel->insertExcelSubMaster2($impexp_type,$insert2);

          $k++;
        }   

        $i++;
      }

	  }	
	if($import_format_info['impexp_format']=='Multi Row Format'|| $import_format_info['impexp_format']==''){ 
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
	   $imptxnvch_supplytype = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';

      $imptxnvch_uqc = '';
      $imptxnvch_qty = '';
      $imptxnvch_mc = '';
      $acc_arr = [];
      $itm_arr = [];

      array_shift($result); // remove first array element
	
      foreach ($result as $key => $value) {
    $supplytype = '';
    if (isset($value[2]) && $value[2] != '') {
        $supplytype = clean(array_search(trim($value[2]), $SupplyTypesData) ?? '');
    }
    $pos = '';
    if (isset($value[12]) && $value[12] != '') {
        $pos = clean(array_search(trim($value[12]), $states_lists) ?? '');
    }

    // Clean up the values from the input
    $date = clean($value[0] ?? '');
    $type = clean($value[1] ?? '');
    $series = clean($value[3] ?? '');
    $no = clean($value[4] ?? '');
    $uqc = clean($value[5] ?? '');
    $account = clean($value[6] ?? '');
    $debit = clean($value[7] ?? '');
    $credit = clean($value[8] ?? '');
    $qty = clean($value[9] ?? '');
    $short_narr = clean($value[10] ?? '');
    $mc = clean($value[11] ?? '');
    $country_code = clean($value[13] ?? '');

    // Remove commas from the debit/credit values
    $debit = str_replace(",", "", $debit);
    $credit = str_replace(",", "", $credit);
    
	
					
    // Begin processing data if there's a date
    if ($date != '') {
        if (isset($result[$key - 1])) {
            // Add to final array when the transaction changes or when we've finished a group of entries
            $final[] = [
                'imptxnvch_date' => $imptxnvch_date,
                'imptxnvch_type' => $imptxnvch_type,
                'imptxnvch_supplytype' => $imptxnvch_supplytype,
                'imptxnvch_series' => $imptxnvch_series,
                'imptxnvch_bill_no' => $imptxnvch_bill_no,
                'imptxnvch_long_narr' => $imptxnvch_long_narr,
                'imptxnvch_pos' => $imptxnvch_pos,
                'imptxnvch_country_code' => $imptxnvch_country_code,
                'imptxnvch_mc' => $imptxnvch_mc,
                'acc_arr' => $acc_arr,
                'itm_arr' => $itm_arr,
            ];

            // Reset variables after pushing to final array
            $imptxnvch_date = '';
            $imptxnvch_type = '';
            $imptxnvch_series = '';
            $imptxnvch_bill_no = '';
            $imptxnvch_long_narr = '';
            $imptxnvch_pos = '';
            $imptxnvch_country_code = '';
            $imptxnvch_supplytype = '';
            $imptxnvch_mc = '';
            $acc_arr = [];
            $itm_arr = [];
        }

        // Set the current record's data
        $imptxnvch_date = $date;
        $imptxnvch_type = $type;
        $imptxnvch_series = $series;
        $imptxnvch_bill_no = $no;
        $imptxnvch_pos = $pos;
        $imptxnvch_country_code = $country_code;
        $imptxnvch_supplytype = $supplytype;
        $imptxnvch_mc = $mc;
    }

    // Handle the narrative when debit and credit are both zero or empty
    if ((empty($debit) || $debit == 0) && (empty($credit) || $credit == 0)) {
        if (!empty($account)) {
            $imptxnvch_long_narr .= $account . ' ';
        }
    } else {
        if (!empty($uqc) && $uqc != '') {
            // Handle items with quantity and unit of measurement (uqc)
            $itm_arr[] = [
                'imptxnvch_item' => $account,
                'imptxnvch_amt_dr' => '',
                'imptxnvch_amt_cr' => floatval($credit),
                'imptxnvch_uqc_dr' => '',
                'imptxnvch_uqc_cr' => $uqc,
                'imptxnvch_qty_dr' => '',
                'imptxnvch_qty_cr' => floatval($qty),
                'imptxnvch_mc_dr' => '',
                'imptxnvch_mc_cr' => $imptxnvch_mc,
                'imptxnvch_short_narr' => $short_narr,
            ];
        } else {
            // If no quantity, push to acc_arr
            $acc_arr[] = [
                'imptxnvch_acc_bds' => $account,
                'imptxnvch_amt_dr' => floatval($debit),
                'imptxnvch_amt_cr' => floatval($credit),
                'imptxnvch_short_narr' => $short_narr,
				'imptxnvch_mst_type'        => 'acc'
            ];
			
		
			
        }
    }
}

// After the loop, if the last record needs to be saved
if (!empty($imptxnvch_date)) {
	
    $final[] = [
        'imptxnvch_date' => $imptxnvch_date,
        'imptxnvch_type' => $imptxnvch_type,
        'imptxnvch_supplytype' => $imptxnvch_supplytype,
        'imptxnvch_series' => $imptxnvch_series,
        'imptxnvch_bill_no' => $imptxnvch_bill_no,
        'imptxnvch_long_narr' => $imptxnvch_long_narr,
        'imptxnvch_pos' => $imptxnvch_pos,
        'imptxnvch_country_code' => $imptxnvch_country_code,
        'imptxnvch_mc' => $imptxnvch_mc,
        'acc_arr' => $acc_arr,
        'itm_arr' => $itm_arr,
    ];
}

      $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imp_id'             => $i,
            'imptxnvch_date'     => $value['imptxnvch_date'],
            'imptxnvch_type'     => $value['imptxnvch_type'],
            'imptxnvch_series'   => $value['imptxnvch_series'],
            'imptxnvch_bill_no'  => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],
            'imptxnvch_mc'   => $value['imptxnvch_mc'],
            'imp_status'         => 0 
        ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impoutsup_pos'      => $value['imptxnvch_pos'],
            'impoutsup_country_code' => $value['imptxnvch_country_code'],
			'impout_supply_type' => $value['imptxnvch_supplytype'],
        ];

        $this->ImportExportModel->insertExcelSubSideMaster($impexp_type,$insert1);

        $j = 1;
		
        foreach ($value['acc_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
			'imptxnvch_mst_type'=> $value2['imptxnvch_mst_type'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }

        $k = 1;
        foreach ($value['itm_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $k,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_item'      => $value2['imptxnvch_item'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_uqc_dr'    => $value2['imptxnvch_uqc_dr'],
            'imptxnvch_uqc_cr'    => $value2['imptxnvch_uqc_cr'],
            'imptxnvch_qty_dr'    => $value2['imptxnvch_qty_dr'],
            'imptxnvch_qty_cr'    => $value2['imptxnvch_qty_cr'],
            'imptxnvch_mc_dr'     => $value2['imptxnvch_mc_dr'],
            'imptxnvch_mc_cr'     => $value2['imptxnvch_mc_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
          ];
          $this->ImportExportModel->insertExcelSubMaster2($impexp_type,$insert2);

          $k++;
        }   

        $i++;
      }
    
	}
	}
	
	
	
	if($impexp_type == 10) // purchase without item
    {
	 $SupplyTypesData = $this->SupplyTypes;	
	 $states_lists    = $this->TransactionModel->show_states_lists(1);
	 $taxmasters = $this->ImportExportModel->GetTaxMastersInfo('purchase');
		$igst_column_value = clean($taxmasters['igst_master_value'] ?? '');
		$cgst_column_value = clean($taxmasters['cgst_master_value'] ?? '');
		$sgst_column_value = clean($taxmasters['sgst_master_value'] ?? '');
		$cess_column_value = clean($taxmasters['cess_master_value'] ?? '');
		
	 $import_format_info = $this->ImportExportModel->GetimpexpmstnInfo($impexp_sr_id,$impexp_type);	
	  if($import_format_info['impexp_format']=='Single Row Format'){
		
		
		$finalData = [];
		$itemIndexes = [
		'item_name' => [],
		'amount' => [],
		'short_narration' => []
	];
	$bsdIndexes = [
		'bill_sundry_name' => [],
		'amount'           => [],
		'cr_dr'           => []
	];


    foreach ($result as $rowIndex => $row) {
		if ($rowIndex === 0) {
       // Identifying column indexes based on header names
        foreach ($row as $colIndex => $colValue) {
			if($colValue!=''){
            $colValueLower = strtolower($colValue); // Convert to lowercase for case-insensitive search
			// Item-related headers
            if ( strpos($colValueLower, 'account name') !== false) {
                $itemIndexes['item_name'][] = $colIndex;
            } elseif ( strpos($colValueLower, 'account amount') !== false) {
                $itemIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'account short narration') !== false) {
                $itemIndexes['short_narration'][] = $colIndex;
            }
			
		 // Bill Sundry headers
            if (strpos($colValueLower, 'bill sundry name') !== false) {
                $bsdIndexes['bill_sundry_name'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry amount') !== false ) {
                $bsdIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry cr./dr.') !== false) {
                $bsdIndexes['cr_dr'][] = $colIndex;
            }	
        } }
        continue; // Skip header row      
    }
        $rowData = [];
		$supplytype = '';
		if (isset($row[2]) && $row[2] != '') {
			$supplytype = clean(array_search(trim($row[2]), $SupplyTypesData) ?? '');
		}
		$pos = '';
		if (isset($row[5]) && $row[5] != '') {
			$pos = clean(array_search(trim($row[5]), $states_lists) ?? '');
		}
	
        $rowData = [
			'date'           => clean($row[0] ?? ''),
			'type'           => clean($row[1] ?? ''),
			'supply_type'    => $supplytype,
			'vch_series'     => clean($row[3] ?? ''),
			'bill_no'        => clean($row[4] ?? ''),
			'pos'            => $pos,
			'country_code'   => clean($row[6] ?? ''),
			'long_narration' => clean($row[7] ?? ''),		
			'party_account'  => clean($row[8] ?? ''),
			'party_amount'   => clean($row[9] ?? ''),
			'igst'           => clean($row[10] ?? ''),
			'cgst'           => clean($row[11] ?? ''),
			'sgst'           => clean($row[12] ?? ''),
			'cess'           => clean($row[13] ?? '')
		];

        // Extract Item columns dynamically
    $items = [];
    foreach ($itemIndexes['item_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $items[] = [
                'item'            => clean($row[$index] ?? ''),
                'amount'          => clean($row[$itemIndexes['amount'][$i] ?? ''] ?? ''),
                'short_narration' => clean($row[$itemIndexes['short_narration'][$i] ?? ''] ?? '')
            ];
        }
    }
	
	

    // Extract bill sundry data dynamically
    $billSundryNames = [];
    foreach ($bsdIndexes['bill_sundry_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $billSundryNames[] = [
                'name'             => clean($row[$index] ?? ''),
                'amount'           => clean($row[$bsdIndexes['amount'][$i] ?? ''] ?? ''),
                'cr_dr'            => clean($row[$bsdIndexes['cr_dr'][$i] ?? ''] ?? '')
            ];
        }
    }
        // Adding both items and billSundryNames arrays to the main row data
        $rowData['items'] = $items;
        $rowData['bill_sundry_names'] = $billSundryNames;

        // Store the row data
        $finalData[] = $rowData;
    }
	
	
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';
      $imptxnvch_uqc = '';
      $imptxnvch_qty = '';
      $imptxnvch_mc = '';
     
      
   if($finalData){
	  foreach($finalData as $key => $value){
		 $acc_arr = []; 
         $itm_arr = [];		 
		$imptxnvch_date         = $value['date'];
		$imptxnvch_type         = $value['type'];
		$imptxnvch_supply_type  = $value['supply_type'];
		$imptxnvch_series       = $value['vch_series'];
		$imptxnvch_bill_no      = $value['bill_no'];
		$imptxnvch_long_narr    = $value['long_narration'];
		$imptxnvch_pos          = $value['pos'];
		$imptxnvch_country_code = $value['country_code'];
		$party_account          = $value['party_account'];	
		$party_amount           = $value['party_amount'];	
		$igst         		    = $value['igst'];	
		$cgst         		    = $value['cgst'];
		$sgst         		    = $value['sgst'];
		$cess         		    = $value['cess'];		
		$items_data             = $value['items'];
		$bsd_data               = $value['bill_sundry_names'];
						
		$acc_arr[] = [
		  'imptxnvch_acc_bds'     => $party_account,
		  'imptxnvch_amt_dr'      => floatval(0),
		  'imptxnvch_amt_cr'      => floatval($party_amount),
		  'imptxnvch_short_narr'  => ' ',
		  'imptxnvch_mst_type'    => 'acc',
		  'imptxnvch_pary_acc'    => '1'		  
		];
		if($items_data){
			foreach($items_data as $item_row){				
					$acc_arr[] = [
					 'imptxnvch_acc_bds'     => $item_row['item'],
					 'imptxnvch_amt_dr'      => floatval($item_row['amount']),
					 'imptxnvch_amt_cr'      => floatval(0),
					 'imptxnvch_short_narr'  => $item_row['short_narration'],
					 'imptxnvch_mst_type'        => 'acc',
					 'imptxnvch_pary_acc'    => '0'
					]; 
			   }
		   }
		if($bsd_data){
			foreach($bsd_data as $bsd_row){		
                    if(strtolower($bsd_row['cr_dr'])=='cr'){
						$imptxnvch_amt_dr = floatval(0);
						$imptxnvch_amt_cr = floatval($bsd_row['amount']);
					}
				   else if(strtolower($bsd_row['cr_dr'])=='dr'){
						$imptxnvch_amt_dr = floatval($bsd_row['amount']);
						$imptxnvch_amt_cr = floatval(0);
					}
					else{
					    $imptxnvch_amt_dr = floatval(0);
						$imptxnvch_amt_cr = floatval(0);	
					 }					
					$acc_arr[] = [
							  'imptxnvch_acc_bds'     => $bsd_row['name'],
							  'imptxnvch_amt_dr'      => $imptxnvch_amt_dr,
							  'imptxnvch_amt_cr'      => $imptxnvch_amt_cr,
							  'imptxnvch_short_narr'  => ' ',
							  'imptxnvch_mst_type'        => 'bsd',
							  'imptxnvch_pary_acc'    => '0'
							];
			   }
		   }   
		   
		    $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $igst_column_value,
					  'imptxnvch_amt_dr'      => floatval($igst),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					   'imptxnvch_mst_type'        => 'tax',
					   'imptxnvch_pary_acc'    => '0'
					];
	
			 $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cgst_column_value,
					  'imptxnvch_amt_dr'      => floatval($cgst),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $sgst_column_value,
					  'imptxnvch_amt_dr'      => floatval($sgst),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cess_column_value,
					  'imptxnvch_amt_dr'      => floatval($cess),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
					
				$final[] = [
				  'imptxnvch_date'      => $imptxnvch_date,
				  'imptxnvch_type'      => $imptxnvch_type,
				  'imptxnvch_series'    => $imptxnvch_series,
				  'imptxnvch_bill_no'   => $imptxnvch_bill_no,
				  'imptxnvch_long_narr' => $imptxnvch_long_narr,
				  'imptxnvch_pos'       => $imptxnvch_pos,
				  'imptxnvch_country_code' => $imptxnvch_country_code,
				  'imptxnvch_supply_type' => $imptxnvch_supply_type,
				  'acc_arr'             => $acc_arr,
				  'itm_arr'             => $itm_arr,
				];
			
	       } 
        }

	  $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imptxnvch_date'      => $value['imptxnvch_date'],
            'imptxnvch_type'      => $value['imptxnvch_type'],
            'imptxnvch_series'    => $value['imptxnvch_series'],
            'imptxnvch_bill_no'   => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],           
            'imp_status'          => 0 
          ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impinwsup_pos'      => $value['imptxnvch_pos'],
            'impinwsup_country_code' => $value['imptxnvch_country_code'],
			'impinw_supply_type' => $value['imptxnvch_supply_type'],
        ];

        $this->ImportExportModel->insertExcelSubSidePurchaseMaster($impexp_type,$insert1);

        $j = 1;
        foreach ($value['acc_arr'] as $key2 => $value2) {
          $insert2 = [
		    'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
			'imptxnvch_mst_type'  => $value2['imptxnvch_mst_type'],
			'imptxnvch_pary_acc'  => $value2['imptxnvch_pary_acc']
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }

        $i++;
      }
	}	
	if($import_format_info['impexp_format']=='Multi Row Format'|| $import_format_info['impexp_format']==''){	
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = ''; $imptxnvch_supplytype = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
	  $imptxnvch_supplytype = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';
      $details_arr = [];
      array_shift($result); // remove first array element
	  	  
      foreach($result as $key => $value)
      {     
		$supplytype = '';
		if (isset($value[2]) && $value[2] != '') {
			$supplytype = clean(array_search(trim($value[2]), $SupplyTypesData) ?? '');
		}
		$pos = '';
		if (isset($value[9]) && $value[9] != '') {
			$pos = clean(array_search(trim($value[9]), $states_lists) ?? '');
		}
	
        $date = clean($value[0] ?? '');
        $type = clean($value[1] ?? '');
		
        $series = clean($value[3] ?? '');
        $no = clean($value[4] ?? '');

        $account = clean($value[5] ?? '');
        $debit = clean($value[6] ?? '');
        $credit = clean($value[7] ?? '');
        $short_narr = clean($value[8] ?? '');
        
        $country_code = clean($value[10] ?? '');

        $debit = str_replace(",","",$debit);
        $credit = str_replace(",","",$credit);

        if($date != ''){
          if(isset($result[$key-1])){
			$final[] = [
              'imptxnvch_date'      => $imptxnvch_date,
              'imptxnvch_type'      => $imptxnvch_type,
			  'imptxnvch_supplytype'      => $imptxnvch_supplytype,
              'imptxnvch_series'    => $imptxnvch_series,
              'imptxnvch_bill_no'   => $imptxnvch_bill_no,
              'imptxnvch_long_narr' => $imptxnvch_long_narr,
              'imptxnvch_pos'       => $imptxnvch_pos,
              'imptxnvch_country_code' => $imptxnvch_country_code,
              'details_arr'         => $details_arr,
            ];
			
            $imptxnvch_date = '';
            $imptxnvch_type = ''; 
			$imptxnvch_supplytype = '';
            $imptxnvch_series = '';
            $imptxnvch_bill_no = '';
            $imptxnvch_long_narr = '';
            $imptxnvch_pos = '';
            $imptxnvch_country_code = '';
            $details_arr = [];
          }

          $imptxnvch_date = $date;
          $imptxnvch_type = $type;
          $imptxnvch_series = $series;
          $imptxnvch_bill_no = $no;
          $imptxnvch_pos = $pos;
          $imptxnvch_country_code = $country_code; 
		  $imptxnvch_supplytype = $supplytype;
        }

        if((empty($debit) || $debit == 0) && (empty($credit) || $credit == 0)){
          if(!empty($account)){
            $imptxnvch_long_narr .= $account.' ';
          }
        }
        else{
          $details_arr[] = [
            'imptxnvch_acc_bds'     => $account,
            'imptxnvch_amt_dr'      => floatval($debit),
            'imptxnvch_amt_cr'      => floatval($credit),
            'imptxnvch_short_narr'  => $short_narr,
			'imptxnvch_mst_type'        => 'acc'
          ];
        }
      }
	  
	  // After the loop, if the last record needs to be saved
if (!empty($imptxnvch_date)) {
	
   $final[] = [
              'imptxnvch_date'      => $imptxnvch_date,
              'imptxnvch_type'      => $imptxnvch_type,
			  'imptxnvch_supplytype'      => $imptxnvch_supplytype,
              'imptxnvch_series'    => $imptxnvch_series,
              'imptxnvch_bill_no'   => $imptxnvch_bill_no,
              'imptxnvch_long_narr' => $imptxnvch_long_narr,
              'imptxnvch_pos'       => $imptxnvch_pos,
              'imptxnvch_country_code' => $imptxnvch_country_code,
              'details_arr'         => $details_arr,
            ];
}


      $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imp_id'             => $i,
            'imptxnvch_date'     => $value['imptxnvch_date'],
            'imptxnvch_type'     => $value['imptxnvch_type'],
            'imptxnvch_series'   => $value['imptxnvch_series'],
            'imptxnvch_bill_no'  => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],
            'imp_status'         => 0 
        ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impinwsup_pos'      => $value['imptxnvch_pos'],
            'impinwsup_country_code' => $value['imptxnvch_country_code'],
			'impinw_supply_type' => $value['imptxnvch_supplytype'],
        ];

        $this->ImportExportModel->insertExcelSubSidePurchaseMaster($impexp_type,$insert1);

        $j = 1;
        foreach ($value['details_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
			'imptxnvch_mst_type'=> $value2['imptxnvch_mst_type'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }   
        $i++;
      }
	 }
    }

    if($impexp_type == 11) // purchase item
    {
		$SupplyTypesData = $this->SupplyTypes;
		$states_lists    = $this->TransactionModel->show_states_lists(1);
		$taxmasters = $this->ImportExportModel->GetTaxMastersInfo('purchase');
		$igst_column_value = clean($taxmasters['igst_master_value'] ?? '');
		$cgst_column_value = clean($taxmasters['cgst_master_value'] ?? '');
		$sgst_column_value = clean($taxmasters['sgst_master_value'] ?? '');
		$cess_column_value = clean($taxmasters['cess_master_value'] ?? '');
		
	 $import_format_info = $this->ImportExportModel->GetimpexpmstnInfo($impexp_sr_id,$impexp_type);
	  if($import_format_info['impexp_format']=='Single Row Format'){
		
	 $finalData = [];
	 $itemIndexes = [
		'item_name' => [],
		'amount' => [],
		'qty' => [],
		'uqc' => [],
		'short_narration' => []
	];
$bsdIndexes = [
    'bill_sundry_name' => [],
    'amount'           => [],
    'cr_dr'           => []
];
   
	
    foreach ($result as $rowIndex => $row) {
		if ($rowIndex === 0) {
       // Identifying column indexes based on header names
        foreach ($row as $colIndex => $colValue) {
			if($colValue!=''){
            $colValueLower = strtolower($colValue); // Convert to lowercase for case-insensitive search
			// Item-related headers
            if (strpos($colValueLower, 'item name') !== false) {
                $itemIndexes['item_name'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item amount') !== false) {
                $itemIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item qty') !== false) {
                $itemIndexes['qty'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item uqc') !== false) {
                $itemIndexes['uqc'][] = $colIndex;
            } elseif (strpos($colValueLower, 'item short narration') !== false) {
                $itemIndexes['short_narration'][] = $colIndex;
            }
			
		 // Bill Sundry headers
            if (strpos($colValueLower, 'bill sundry name') !== false) {
                $bsdIndexes['bill_sundry_name'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry amount') !== false ) {
                $bsdIndexes['amount'][] = $colIndex;
            } elseif (strpos($colValueLower, 'bill sundry cr./dr.') !== false) {
                $bsdIndexes['cr_dr'][] = $colIndex;
            }	
        } }
        continue; // Skip header row      
    }
        $rowData = [];
		$supplytype = '';
		if (isset($row[2]) && $row[2] != '') {
			$supplytype = array_search(trim($row[2]), $SupplyTypesData);
		}
		$pos = '';
		if (isset($row[6]) && $row[6] != '') {
			$pos = clean(array_search(trim($row[6]), $states_lists) ?? '');
		}
		
        $rowData = [
        'date'           => clean($row[0] ?? ''),
        'type'           => clean($row[1] ?? ''),
		'supply_type'    => $supplytype,
        'vch_series'     => clean($row[3] ?? ''),
        'bill_no'        => clean($row[4] ?? ''),
        'mc'             => clean($row[5] ?? ''),
        'pos'            => $pos,
        'country_code'   => clean($row[7] ?? ''),
        'long_narration' => clean($row[8] ?? ''),		
		'party_account'  => clean($row[9] ?? ''),
		'party_amount'   => clean($row[10] ?? ''),
		'igst'           => clean($row[11] ?? ''),
		'cgst'           => clean($row[12] ?? ''),
		'sgst'           => clean($row[13] ?? ''),
		'cess'           => clean($row[14] ?? '')
		];

        // Extract Item columns dynamically
    $items = [];
    foreach ($itemIndexes['item_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $items[] = [
                'item'            => clean($row[$index] ?? ''),
                'amount'          => clean($row[$itemIndexes['amount'][$i] ?? ''] ?? ''),
                'qty'             => clean($row[$itemIndexes['qty'][$i] ?? ''] ?? ''),
                'uqc'             => clean($row[$itemIndexes['uqc'][$i] ?? ''] ?? ''),
                'short_narration' => clean($row[$itemIndexes['short_narration'][$i] ?? ''] ?? '')
            ];
        }
    }
	
	

    // Extract bill sundry data dynamically
    $billSundryNames = [];
    foreach ($bsdIndexes['bill_sundry_name'] as $i => $index) {
        if (!empty($row[$index])) {
            $billSundryNames[] = [
                'name' => clean($row[$index] ?? ''),
                'amount'           => clean($row[$bsdIndexes['amount'][$i] ?? ''] ?? ''),
                'cr_dr'            => clean($row[$bsdIndexes['cr_dr'][$i] ?? ''] ?? '')
            ];
        }
    }
        // Adding both items and billSundryNames arrays to the main row data
        $rowData['items'] = $items;
        $rowData['bill_sundry_names'] = $billSundryNames;

        // Store the row data
        $finalData[] = $rowData;
    }
	
	
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';
      $imptxnvch_uqc = '';
      $imptxnvch_qty = '';
      $imptxnvch_mc = '';
     
      
   if($finalData){
	  foreach($finalData as $key => $value){
		 $acc_arr = []; 
         $itm_arr = [];		 
		$imptxnvch_date         = $value['date'];
		$imptxnvch_type         = $value['type'];
		$imptxnvch_supply_type  = $value['supply_type'];
		$imptxnvch_series       = $value['vch_series'];
		$imptxnvch_bill_no      = $value['bill_no'];
		$imptxnvch_long_narr    = $value['long_narration'];
		$imptxnvch_pos          = $value['pos'];
		$imptxnvch_country_code = $value['country_code'];
		$mc                     = $value['mc'];	
		$party_account          = $value['party_account'];	
		$party_amount           = $value['party_amount'];	
		$igst         		    = $value['igst'];	
		$cgst         		    = $value['cgst'];
		$sgst         		    = $value['sgst'];
		$cess         		    = $value['cess'];		
		$items_data             = $value['items'];
		$bsd_data               = $value['bill_sundry_names'];
						
		$acc_arr[] = [
		  'imptxnvch_acc_bds'     => $party_account,
		  'imptxnvch_amt_dr'      => floatval(0),
		  'imptxnvch_amt_cr'      => floatval($party_amount),
		  'imptxnvch_short_narr'  => ' ',
		  'imptxnvch_mst_type'    => 'acc',
		  'imptxnvch_pary_acc'    => '1'
		];
		if($items_data){
			foreach($items_data as $item_row){				
					$itm_arr[] = [
					  'imptxnvch_item'        => $item_row['item'],
					  'imptxnvch_amt_dr'      => floatval($item_row['amount']),
					  'imptxnvch_amt_cr'      => '',
					  'imptxnvch_uqc_dr'      => $item_row['uqc'],
					  'imptxnvch_uqc_cr'      => '',
					  'imptxnvch_qty_dr'      => floatval($item_row['qty']),
					  'imptxnvch_qty_cr'      => '',
					  'imptxnvch_mc_dr'       => $mc,
					  'imptxnvch_mc_cr'       => '',
					  'imptxnvch_short_narr'  => $item_row['short_narration'],
					]; 
			   }
		   }
		if($bsd_data){
			foreach($bsd_data as $bsd_row){		
                    if(strtolower($bsd_row['cr_dr'])=='cr'){
						$imptxnvch_amt_dr = floatval(0);
						$imptxnvch_amt_cr = floatval($bsd_row['amount']);
					}
				    else if(strtolower($bsd_row['cr_dr'])=='dr'){
						$imptxnvch_amt_dr = floatval($bsd_row['amount']);
						$imptxnvch_amt_cr = floatval(0);
					}else{
					$imptxnvch_amt_dr =floatval(0);
						$imptxnvch_amt_cr = floatval(0);	
					}					
					$acc_arr[] = [
							  'imptxnvch_acc_bds'     => $bsd_row['name'],
							  'imptxnvch_amt_dr'      => $imptxnvch_amt_dr,
							  'imptxnvch_amt_cr'      => $imptxnvch_amt_cr,
							  'imptxnvch_short_narr'  => 'n/a',
							  'imptxnvch_mst_type'        => 'bsd',
							  'imptxnvch_pary_acc'    => '0'
							];
			   }
		   }   
		   
		    $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $igst_column_value,
					  'imptxnvch_amt_dr'      => floatval($igst),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					   'imptxnvch_mst_type'        => 'tax',
					   'imptxnvch_pary_acc'    => '0'
					];
	
			 $acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cgst_column_value,
					  'imptxnvch_amt_dr'      => floatval($cgst),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $sgst_column_value,
					  'imptxnvch_amt_dr'      => floatval($sgst),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
			$acc_arr[] = [
					  'imptxnvch_acc_bds'     => $cess_column_value,
					  'imptxnvch_amt_dr'      => floatval($cess),
					  'imptxnvch_amt_cr'      => floatval(0),
					  'imptxnvch_short_narr'  => ' ',
					  'imptxnvch_mst_type'        => 'tax',
					  'imptxnvch_pary_acc'    => '0'
					];
					
				$final[] = [
				  'imptxnvch_date'      => $imptxnvch_date,
				  'imptxnvch_type'      => $imptxnvch_type,
				  'imptxnvch_series'    => $imptxnvch_series,
				  'imptxnvch_bill_no'   => $imptxnvch_bill_no,
				  'imptxnvch_long_narr' => $imptxnvch_long_narr,
				  'imptxnvch_pos'       => $imptxnvch_pos,
				  'imptxnvch_country_code' => $imptxnvch_country_code,
				  'imptxnvch_supply_type' => $imptxnvch_supply_type,
				  'imptxnvch_mc'        => $mc,
				  'acc_arr'             => $acc_arr,
				  'itm_arr'             => $itm_arr,
				];
			
	       } 
        }


	  $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imptxnvch_date'      => $value['imptxnvch_date'],
            'imptxnvch_type'      => $value['imptxnvch_type'],
            'imptxnvch_series'    => $value['imptxnvch_series'],
            'imptxnvch_bill_no'   => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],
            'imptxnvch_mc'        => $value['imptxnvch_mc'],
            'imp_status'          => 0 
          ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impinwsup_pos'      => $value['imptxnvch_pos'],
            'impinwsup_country_code' => $value['imptxnvch_country_code'],
			'impinw_supply_type' => $value['imptxnvch_supply_type'],
        ];

        $this->ImportExportModel->insertExcelSubSidePurchaseMaster($impexp_type,$insert1);

        $j = 1;
        foreach ($value['acc_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
			'imptxnvch_mst_type'  => $value2['imptxnvch_mst_type'],
			'imptxnvch_pary_acc'  => $value2['imptxnvch_pary_acc']
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }

        $k = 1;
        foreach ($value['itm_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $k,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_item'      => $value2['imptxnvch_item'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_uqc_dr'    => $value2['imptxnvch_uqc_dr'],
            'imptxnvch_uqc_cr'    => $value2['imptxnvch_uqc_cr'],
            'imptxnvch_qty_dr'    => $value2['imptxnvch_qty_dr'],
            'imptxnvch_qty_cr'    => $value2['imptxnvch_qty_cr'],
            'imptxnvch_mc_dr'     => $value2['imptxnvch_mc_dr'],
            'imptxnvch_mc_cr'     => $value2['imptxnvch_mc_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
          ];
          $this->ImportExportModel->insertExcelSubMaster2($impexp_type,$insert2);

          $k++;
        }   

        $i++;
      }

	  }	
if($import_format_info['impexp_format']=='Multi Row Format'|| $import_format_info['impexp_format']==''){	  
      $final = [];
      $imptxnvch_date = '';
      $imptxnvch_type = '';
	  $imptxnvch_supplytype = '';
      $imptxnvch_series = '';
      $imptxnvch_bill_no = '';
      $imptxnvch_long_narr = '';
      $imptxnvch_pos = '';
      $imptxnvch_country_code = '';

      $imptxnvch_uqc = '';
      $imptxnvch_qty = '';
      $imptxnvch_mc = '';
      $acc_arr = [];
      $itm_arr = [];

      array_shift($result); // remove first array element
      foreach($result as $key => $value)
      {
		$supplytype = '';
		if (isset($value[2]) && $value[2] != '') {
			$supplytype = clean(array_search(trim($value[2]), $SupplyTypesData) ?? '');
		}
		$pos = '';
		if (isset($value[12]) && $value[12] != '') {
			$pos = clean(array_search(trim($value[12]), $states_lists) ?? '');
		}  
        
        $date = clean($value[0] ?? '');
        $type = clean($value[1] ?? '');		
        $series = clean($value[3] ?? '');
        $no = clean($value[4] ?? '');
        $uqc = clean($value[5] ?? '');
        $account = clean($value[6] ?? '');
        $debit = clean($value[7] ?? '');
        $credit = clean($value[8] ?? '');
        $qty = clean($value[9] ?? '');
        $short_narr = clean($value[10] ?? '');
        $mc = clean($value[11] ?? '');       
        $country_code = clean($value[13] ?? '');

        $debit = str_replace(",","",$debit);
        $credit = str_replace(",","",$credit);

        if($date != ''){

          if(isset($result[$key-1])){
            $final[] = [
              'imptxnvch_date'      => $imptxnvch_date,
              'imptxnvch_type'      => $imptxnvch_type, 
			  'imptxnvch_supplytype'      => $imptxnvch_supplytype,
              'imptxnvch_series'    => $imptxnvch_series,
              'imptxnvch_bill_no'   => $imptxnvch_bill_no,
              'imptxnvch_long_narr' => $imptxnvch_long_narr,
              'imptxnvch_pos'       => $imptxnvch_pos,
              'imptxnvch_country_code' => $imptxnvch_country_code,
              'imptxnvch_mc'        => $mc,
              'acc_arr'             => $acc_arr,
              'itm_arr'             => $itm_arr,
            ];

            $imptxnvch_date = '';
            $imptxnvch_type = '';$imptxnvch_supplytype = '';
            $imptxnvch_series = '';
            $imptxnvch_bill_no = '';
            $imptxnvch_long_narr = '';
            $imptxnvch_pos = '';
            $imptxnvch_country_code = '';
            $imptxnvch_mc = '';
            $acc_arr = [];
            $itm_arr = [];
          }

          $imptxnvch_date = $date;
          $imptxnvch_type = $type;
          $imptxnvch_series = $series;
          $imptxnvch_bill_no = $no;
          $imptxnvch_pos = $pos;
          $imptxnvch_country_code = $country_code;
		  $imptxnvch_supplytype = $supplytype;
          $imptxnvch_mc = $mc;
        }

        if((empty($debit) || $debit == 0) && (empty($credit) || $credit == 0)){
          if(!empty($account)){
            $imptxnvch_long_narr .= $account.' ';
          }
        }
        else{
          if(!empty($uqc) && $uqc != ''){
            $itm_arr[] = [
              'imptxnvch_item'        => $account,
              'imptxnvch_amt_dr'      => floatval($debit),
              'imptxnvch_amt_cr'      => '',
              'imptxnvch_uqc_dr'      => '',
              'imptxnvch_uqc_cr'      => $uqc,
              'imptxnvch_qty_dr'      => '',
              'imptxnvch_qty_cr'      => floatval($qty),
              'imptxnvch_mc_dr'       => '',
              'imptxnvch_mc_cr'       => $imptxnvch_mc,
              'imptxnvch_short_narr'  => $short_narr,
            ]; 
          }
          else{
            $acc_arr[] = [
              'imptxnvch_acc_bds'     => $account,
              'imptxnvch_amt_dr'      => floatval($debit),
              'imptxnvch_amt_cr'      => floatval($credit),
              'imptxnvch_short_narr'  => $short_narr,
			  'imptxnvch_mst_type'        => 'acc'
            ];
          }
            
        }
      }

// After the loop, if the last record needs to be saved
if (!empty($imptxnvch_date)) {
	
    $final[] = [
			  'imptxnvch_date'      => $imptxnvch_date,
              'imptxnvch_type'      => $imptxnvch_type, 
			  'imptxnvch_supplytype'      => $imptxnvch_supplytype,
              'imptxnvch_series'    => $imptxnvch_series,
              'imptxnvch_bill_no'   => $imptxnvch_bill_no,
              'imptxnvch_long_narr' => $imptxnvch_long_narr,
              'imptxnvch_pos'       => $imptxnvch_pos,
              'imptxnvch_country_code' => $imptxnvch_country_code,
              'imptxnvch_mc'        => $mc,
              'acc_arr'             => $acc_arr,
              'itm_arr'             => $itm_arr,
			  ];
}


      $i = 1;
      foreach($final as $key => $value)
      { 
        $insert = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imptxnvch_date'      => $value['imptxnvch_date'],
            'imptxnvch_type'      => $value['imptxnvch_type'],
            'imptxnvch_series'    => $value['imptxnvch_series'],
            'imptxnvch_bill_no'   => $value['imptxnvch_bill_no'],
            'imptxnvch_long_narr' => $value['imptxnvch_long_narr'],
            'imptxnvch_mc'        => $value['imptxnvch_mc'],
            'imp_status'          => 0 
        ]; 
        
        $insertID = $this->ImportExportModel->insertExcelMaster($impexp_type,$insert);

        $insert1 = [
            'impexp_sr_id'       => $impexp_sr_id,
            'imptxnvch_id'       => $insertID,
            'imp_id'             => $i,
            'impinwsup_pos'      => $value['imptxnvch_pos'],
            'impinwsup_country_code' => $value['imptxnvch_country_code'],
			'impinw_supply_type' => $value['imptxnvch_supplytype'],
        ];

        $this->ImportExportModel->insertExcelSubSidePurchaseMaster($impexp_type,$insert1);

        $j = 1;
        foreach ($value['acc_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $j,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_acc_bds'   => $value2['imptxnvch_acc_bds'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
			'imptxnvch_mst_type'=> $value2['imptxnvch_mst_type'],
          ];
          $this->ImportExportModel->insertExcelSubMaster($impexp_type,$insert2);

          $j++;
        }

        $k = 1;
        foreach ($value['itm_arr'] as $key2 => $value2) {
          $insert2 = [
            'impexp_sr_id'        => $impexp_sr_id,
            'imp_id'              => $i,
            'imp_sub_id'          => $k,
            'imptxnvch_id'        => $insertID,
            'imptxnvch_item'      => $value2['imptxnvch_item'],
            'imptxnvch_amt_dr'    => $value2['imptxnvch_amt_dr'],
            'imptxnvch_amt_cr'    => $value2['imptxnvch_amt_cr'],
            'imptxnvch_uqc_dr'    => $value2['imptxnvch_uqc_dr'],
            'imptxnvch_uqc_cr'    => $value2['imptxnvch_uqc_cr'],
            'imptxnvch_qty_dr'    => $value2['imptxnvch_qty_dr'],
            'imptxnvch_qty_cr'    => $value2['imptxnvch_qty_cr'],
            'imptxnvch_mc_dr'     => $value2['imptxnvch_mc_dr'],
            'imptxnvch_mc_cr'     => $value2['imptxnvch_mc_cr'],
            'imptxnvch_short_narr'=> $value2['imptxnvch_short_narr'],
          ];
          $this->ImportExportModel->insertExcelSubMaster2($impexp_type,$insert2);

          $k++;
        }   

        $i++;
        }
      }
    }
	

    $data = [
      'impexp_sr_id'        => $impexp_sr_id,
      'impexp_status'       => 2
    ];
    $this->ImportExportModel->updateExportMaster($data);
    
    return json_encode(['status' => true]);

    }
  }

  // delete it
  function upload_master_data2()
  {
    if($this->request->getMethod() == 'post')
    {

      $rules = [        
        'import_file' => [
          'rules'  => 'uploaded[import_file]|max_size[import_file,1024]',
          'errors' => [
            'uploaded'  => 'File is required',
            'ext_in'=>'File must be of type xlsx, xls or csv',
            'max_size'  => 'Size must be less than 1024kb'
          ],
        ],
        'impexp_sr_id' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Something went wrong...',
          ],
        ],
        'impexp_type' => [
          'rules'  => 'required',
          'errors' => [
            'required' => 'Something went wrong...',
          ],
        ],
        
      ];
      
      if(!$this->validate($rules)){
        $errors = $this->validator->getErrors();
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
      }

      $impexp_sr_id = $this->request->getVar('impexp_sr_id');
      $impexp_type = $this->request->getVar('impexp_type');

      $arr = explode(".", $_FILES["import_file"]["name"]);
      $extension = end($arr); 
      $allowed_extension = array("xls", "xlsx", "csv");

      if(!in_array($extension, $allowed_extension)){
        return json_encode(['status' => false, 'message' => 'Invalid file extension']);
      }

      $file = $this->request->getFile('import_file');
        if (!$file->isValid()) {
        return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Invalid File']]);
      }

      $file_ext = $file->getExtension();

      $_name = 'impexp_'.rand(10,99).'_'.time();
      $file_name = $_name.'.'.$file_ext;

      $folder_path = WRITEPATH.'uploads/comp_impexp_temp';
      if(!file_exists($folder_path)) {
        mkdir($folder_path, 0755);
      }
      $file_path =  $folder_path.'/'.$file_name;
      $file->move($folder_path, $file_name);

      $this->ImportExportModel->insertExcelMaster2($impexp_type,$impexp_sr_id,$file_path);

      unlink($file_path);

      return json_encode(['status' => true]);
    }
  }

  function get_preview_records()
  {
    if($this->request->getMethod() == 'post')
    {
      
      $impexp_sr_id = $this->request->getVar('impexp_sr_id');
      $impexp_type = $this->request->getVar('impexp_type');
      $offset = $this->request->getVar('offset');
      $limit = 20;

      $result = $this->ImportExportModel->get_imp_mst_list($impexp_sr_id,$impexp_type,$limit,$offset);

      return json_encode(['status' => true, 'result' => $result, 'limit' => $limit]);
    }
  }
  
   function get_preview_suggestion_records()
  {
    if($this->request->getMethod() == 'post')
    {
      
      $impexp_sr_id = $this->request->getVar('impexp_sr_id');
      $impexp_type = $this->request->getVar('impexp_type');
      $offset = $this->request->getVar('offset');
      $limit = 20; 

      $result = $this->ImportExportModel->get_imp_mst_list_suggestion($impexp_sr_id,$impexp_type,$limit,$offset);

      return json_encode(['status' => true, 'result' => $result, 'limit' => $limit]);
    }
  }
  
  function process_records()
  {
    if($this->request->getMethod() == 'post')
    {
      $limit = 20;

      $impexp_sr_id = $this->request->getVar('impexp_sr_id');
      $impexp_type = $this->request->getVar('impexp_type');
      $point_index = $this->request->getVar('point_index');

      if($point_index == 0){

        $details = $this->ImportExportModel->get_export_master_details($impexp_sr_id);

        if(empty($details))
          return json_encode(['status' => false, 'message' => 'Data does not exists']);

        if($details['impexp_status'] == 1)
          return json_encode(['status' => false, 'message' => 'Import is already completed']);

        $details['total_index'] = ceil($details['total_records']/$limit);

        if($details['total_index'] == 0)
          return json_encode(['status' => false, 'message' => 'No records']);

        $details['limit'] = $limit;
        return json_encode(['status' => true, 'data' => $details]);
      }

      
      $total_records = $this->ImportExportModel->get_ttl_records($impexp_sr_id,$impexp_type);

      $offset = ($point_index-1) * $limit;
      if($offset < $total_records){

        $response = [];
        if($impexp_type == 1){
          $response = $this->ImportExportModel->importAccountMaster($impexp_sr_id,$limit,$offset);
        }
        if($impexp_type == 2){
          $response = $this->ImportExportModel->importAccountGroupMaster($impexp_sr_id,$limit,$offset);
        }
        if($impexp_type == 3){
          $response = $this->ImportExportModel->importItemMaster($impexp_sr_id,$limit,$offset);
        }
        if($impexp_type == 4){
          $response = $this->ImportExportModel->importItemGroupMaster($impexp_sr_id,$limit,$offset);
        }
        if($impexp_type == 5){
          $response = $this->ImportExportModel->importItemCategoryMaster($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 6){
          $response = $this->ImportExportModel->importDayBookVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 7){
          $response = $this->ImportExportModel->importDayBookVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 8){
          $response = $this->ImportExportModel->importSaleNonItemVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 9){
          $response = $this->ImportExportModel->importSaleItemVouchers($impexp_sr_id,$limit,$offset); 
        }		
		if($impexp_type == 10){
          $response = $this->ImportExportModel->importPurchaseNonItemVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 11){
          $response = $this->ImportExportModel->importPurchaseItemVouchers($impexp_sr_id,$limit,$offset); 
        }		
        return json_encode(['status' => true, 'complete' => false, 'data' => $response]);
      }
      else{
        return json_encode(['status' => true, 'complete' => true]);
      }
    }
  }
  
  function validate_records()
  {
    if($this->request->getMethod() == 'post')
    {
      $limit = 20;

      $impexp_sr_id = $this->request->getVar('impexp_sr_id');
      $impexp_type = $this->request->getVar('impexp_type');
      $point_index = $this->request->getVar('point_index');

      if($point_index == 0){

        $details = $this->ImportExportModel->get_export_master_details($impexp_sr_id);

        if(empty($details))
          return json_encode(['status' => false, 'message' => 'Data does not exists']);

        if($details['impexp_status'] == 1)
          return json_encode(['status' => false, 'message' => 'Import is already completed']);

        $details['total_index'] = ceil($details['total_records']/$limit);

        if($details['total_index'] == 0)
          return json_encode(['status' => false, 'message' => 'No records']);

        $details['limit'] = $limit;
        return json_encode(['status' => true, 'data' => $details]);
      }

      
      $total_records = $this->ImportExportModel->get_ttl_records($impexp_sr_id,$impexp_type);

      $offset = ($point_index-1) * $limit;
      if($offset < $total_records){
 
        $response = [];
        if($impexp_type == 1){
          $response = $this->ImportExportModel->validateAccountMaster($impexp_sr_id,$limit,$offset);
        }
        if($impexp_type == 2){
          $response = $this->ImportExportModel->validateAccountGroupMaster($impexp_sr_id,$limit,$offset);
        }

        if($impexp_type == 3){
          $response = $this->ImportExportModel->validateItemMaster($impexp_sr_id,$limit,$offset);
        }
        if($impexp_type == 4){
          $response = $this->ImportExportModel->validateItemGroupMaster($impexp_sr_id,$limit,$offset);
        }
        if($impexp_type == 5){
          $response =$this->ImportExportModel->validateItemCategoryMaster($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 6){
          $response = $this->ImportExportModel->validateDayBookVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 7){
          $response = $this->ImportExportModel->validateDayBookVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 8){
          $response = $this->ImportExportModel->validateSaleNonItemVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 9){
          $response = $this->ImportExportModel->validateSaleItemVouchers($impexp_sr_id,$limit,$offset); 
        }		
		if($impexp_type == 10){
          $response = $this->ImportExportModel->validatePurchaseNonItemVouchers($impexp_sr_id,$limit,$offset); 
        }
        if($impexp_type == 11){
          $response = $this->ImportExportModel->validatePurchaseItemVouchers($impexp_sr_id,$limit,$offset); 
        }		

        return json_encode(['status' => true, 'complete' => false, 'data' => $response]);
      }
      else{
        return json_encode(['status' => true, 'complete' => true]);
      }
    }
  }

  function finalize()
  {

    if($this->request->getMethod() == 'post')
    {
      $impexp_sr_id = $this->request->getVar('impexp_sr_id');
      $impexp_type = $this->request->getVar('impexp_type');

      
      $this->ImportExportModel->deleteMaster($impexp_sr_id,$impexp_type);

      $data = [
        'impexp_sr_id'        => $impexp_sr_id,
        'impexp_status'       => 1
      ];
      $this->ImportExportModel->updateExportMaster($data);
    

      return json_encode(['status' => true]);
    }
  }

  function delete_inpexp_master()
  {
    if($this->request->getMethod() == 'post')
    {
      $impexp_sr_id = $this->request->getVar('impexp_sr_id');
      $impexp_type = $this->request->getVar('impexp_type');

      
      $this->ImportExportModel->deleteMaster($impexp_sr_id,$impexp_type);

      $this->ImportExportModel->deleteExportMaster($impexp_sr_id);

      return json_encode(['status' => true, 'message' => 'Master Deleted']);
    }
  }
  
  function save_suggestion_editing_records()
  {
    if($this->request->getMethod() == 'post')
    {
     $impexp_sr_id=$this->request->getVar('impexp_sr_id');
     $impexp_type =$this->request->getVar('impexp_type');
     $data =$this->request->getVar('data');
     $data = json_decode($data);
	

     foreach ($data as $key => $value) {

      $value = (array)$value;

       $this->ImportExportModel->save_editing_suggestion_records($impexp_sr_id,$impexp_type,$value);
     }

     return json_encode(['status' => true, 'message' => 'Data Updated']);

    }
  }
  
   
  function save_editing_records()
  {
    if($this->request->getMethod() == 'post')
    {
     $impexp_sr_id=$this->request->getVar('impexp_sr_id');
     $impexp_type =$this->request->getVar('impexp_type');
     $data =$this->request->getVar('data');
     $data = json_decode($data);
	
     foreach ($data as $key => $value) {

      $value = (array)$value;

       $this->ImportExportModel->save_editing_records($impexp_sr_id,$impexp_type,$value);
     }

     return json_encode(['status' => true, 'message' => 'Data Updated']);

    }
  }

  function get_acc_bsd_list()
  {
    $list = $this->TransactionModel->get_acc_bsd_list();

    return json_encode(['status' => true, 'data' => $list]);
  }

  function get_item_list()
  {
    $list = $this->TransactionModel->get_itm_list();

    return json_encode(['status' => true, 'data' => $list]);
  }

  function get_unit_list()
  {
    $list = $this->TransactionModel->units_dropdown();

    return json_encode(['status' => true, 'data' => $list]);
  }

  function get_tax_list()
  {
    $list = $this->TransactionModel->get_tax_list();

    return json_encode(['status' => true, 'data' => $list]);
  }
    
}