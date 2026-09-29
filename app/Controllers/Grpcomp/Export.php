<?php
namespace App\Controllers\Admin;
use App\Models\Admin\ReportsModel;
use App\Models\Admin\ERPLogModel;
use App\Models\CommonModel;
use App\Models\Admin\BalancesModel;
use App\Models\Admin\AccountsModel;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ItemsModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class Export extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text']);
		$this->ReportsModel    = new ReportsModel();
		$this->AccountsModel   = new AccountsModel();
		$this->ItemsModel      = new ItemsModel();
		$this->LogModel        = new ERPLogModel();
        $this->CommonModel     = new CommonModel();	
		$this->BalancesModel   = new BalancesModel();	
        $this->TransactionModel  = new TransactionModel();
		$this->auth_session    = new auth_session();
	    $this->auth_session->user_restrict();
	    $this->auth_session->is_company_opened();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->forge         = \Config\Database::forge();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->enc_string    = new enc_string();
		$this->ses_comp_fy_id=  $this->session->get('ses_comp_fy_id');
		
    }
	
	function clean($str)
	{ 
		$str = utf8_decode($str);		
		$str = str_replace('&raquo;', '»',$str);
		$str = str_replace('&#8377;', '',$str);
		$str = str_replace('&nbsp;', '',$str);
		$str = str_replace('<sub><em>', ' ',$str);
		$str = str_replace('</em></sub>', ' ',$str);
		$str = trim($str);
		return $str;
	}

	public function balance_sheet(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($fy_begndt. ' + 1 year'));
	
	
		 $view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 1;
		 $nil_type = isset($_GET['nil_type'])  && ($_GET['nil_type']!='') ? $_GET['nil_type'] : 1;
		 $finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));

		$from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
		$to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : $finyear['end_date'];

		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd = date('Y-m-d', strtotime($to_date)); 
	
        $result = $this->ReportsModel->load_balance_sheet($view,$from_date_ymd,$to_date_ymd,$nil_type);

	    $spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Balance Sheet");
		
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
        $sheet->setCellValue('A2', 'LIABILITIES');
        $sheet->setCellValue('B2', 'AMT(₹)');
        $sheet->setCellValue('C2', 'ASSETS');
		$sheet->setCellValue('D2', 'AMT(₹)');	
		$spreadsheet->getActiveSheet()->getStyle("A2:D2")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		
						
						
		$boldcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 12
						));		
		$subboldcell_style = array(
						'font'  => array(
							'bold'  => false,
							'color' => array('rgb' => '2b2b2a'),
							'size'  => 12
						));						
        $italiccell_style = array(
						'font'  => array(
							'bold'  => false,
							'color' => array('rgb' => '595656'),
							'size'  => 12
						));	

						
        $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		
		foreach(range('A','D') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}

        $counter = 3;	
		$spreadsheet->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold( true );
		
		foreach($result as $row){
			if(isset($row['l_group_name'])){
				$l_group_name= html_entity_decode($this->clean($row['l_group_name']));
				
			}
			else
				$l_group_name = "";
			
			if(isset($row['l_balance_total']))
				$l_balance_total=$this->clean($row['l_balance_total']);
			else
				$l_balance_total = "";
		   
			if(isset($row['r_group_name'])){
				$r_group_name= html_entity_decode($this->clean($row['r_group_name']));
			}
			else
				$r_group_name = "";
		    
			if(isset($row['r_balance_total']))
				$r_balance_total=$this->clean($row['r_balance_total']);
			else
				$r_balance_total = "";
			
			if(isset($row['pq_cellattr']['l_group_name']['style']) && $row['pq_cellattr']['l_group_name']['style']=='font-weight:bold;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($boldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($boldcell_style);
			 }
			else if(isset($row['pq_cellattr']['l_group_name']['style']) && $row['pq_cellattr']['l_group_name']['style']=='font-weight:500;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($subboldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($subboldcell_style);
			 }
		    else if(isset($row['pq_cellattr']['l_group_name']['style']) && $row['pq_cellattr']['l_group_name']['style']=='font-style:italic;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($italiccell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($italiccell_style);
			 }
			 
		    else if(isset($row['pq_cellattr']['r_group_name']['style']) && $row['pq_cellattr']['r_group_name']['style']=='font-weight:bold;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($boldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($boldcell_style);
			 }
			 
			else if(isset($row['pq_cellattr']['r_group_name']['style']) && $row['pq_cellattr']['r_group_name']['style']=='font-weight:500;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($subboldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($subboldcell_style);
			 }
			else if(isset($row['pq_cellattr']['r_group_name']['style']) && $row['pq_cellattr']['r_group_name']['style']=='font-style:italic;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($italiccell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($italiccell_style);
			 } 
			 
			 else{
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->getFont()->setBold( false );
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->getFont()->setBold( false );
			 }
		
			
		    $sheet->setCellValue('A'.$counter , $l_group_name);
            $sheet->setCellValue('B'.$counter , $l_balance_total);
            $sheet->setCellValue('C'.$counter , $r_group_name);
			$sheet->setCellValue('D'.$counter , $r_balance_total);
			
			$spreadsheet->getActiveSheet()->getStyle('B'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );		 
		
		    $counter++;
		}
		
		//$spreadsheet->getActiveSheet()->getStyle("B".$counter-1)->applyFromArray($lastcell_style);
		//$spreadsheet->getActiveSheet()->getStyle("D".$counter-1)->applyFromArray($lastcell_style);
		
		$spreadsheet->getActiveSheet()->getHeaderFooter()->setOddFooter('&R&F Page &P / &N');
		$spreadsheet->getActiveSheet()->getHeaderFooter()->setEvenFooter('&R&F Page &P / &N');
	   
	   $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="BalanceSheet.xlsx"');
		$writer->save('php://output');
		die();

	   // $writer = new Xlsx($spreadsheet);
		//$writer->save('php://output');
        //$writer->save(WRITEPATH.'BalanceSheet.xlsx');
        //return $this->response->download(WRITEPATH.'BalanceSheet.xlsx', null);	
		die();
     
	}
	
	public function profit_loss(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($fy_begndt. ' + 1 year'));
	
		$view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 1;
		 $nil_type = isset($_GET['nil_type'])  && ($_GET['nil_type']!='') ? $_GET['nil_type'] : 1;
		 $finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));

		$from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
		$to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : $finyear['end_date'];

		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd = date('Y-m-d', strtotime($to_date)); 
	
        $result = $this->ReportsModel->load_profit_loss($view,$from_date_ymd,$to_date_ymd,$nil_type);

	    $spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Profit & Loss");
		
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
        $sheet->setCellValue('A2', 'DEBITS');
        $sheet->setCellValue('B2', 'AMT(₹)');
        $sheet->setCellValue('C2', 'CREDITS');
		$sheet->setCellValue('D2', 'AMT(₹)');	
		$spreadsheet->getActiveSheet()->getStyle("A2:D2")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		
		 $boldcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 12
						));		
		$subboldcell_style = array(
						'font'  => array(
							'bold'  => false,
							'color' => array('rgb' => '2b2b2a'),
							'size'  => 12
						));						
        $italiccell_style = array(
						'font'  => array(
							'bold'  => false,
							'color' => array('rgb' => '595656'),
							'size'  => 12
						));	

						
        $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		
		foreach(range('A','D') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}

        $counter = 3;	
		$spreadsheet->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold( true );
		
		foreach($result as $row){
			if(isset($row['l_group_name'])){
				$l_group_name= html_entity_decode($this->clean($row['l_group_name']));
				
			}
			else
				$l_group_name = "";
			
			if(isset($row['l_balance_total']))
				$l_balance_total=$this->clean($row['l_balance_total']);
			else
				$l_balance_total = "";
		   
			if(isset($row['r_group_name'])){
				$r_group_name= html_entity_decode($this->clean($row['r_group_name']));
			}
			else
				$r_group_name = "";
		    
			if(isset($row['r_balance_total']))
				$r_balance_total=$this->clean($row['r_balance_total']);
			else
				$r_balance_total = "";
			
			if(isset($row['pq_cellattr']['l_group_name']['style']) && $row['pq_cellattr']['l_group_name']['style']=='font-weight:bold;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($boldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($boldcell_style);
			 }
			else if(isset($row['pq_cellattr']['l_group_name']['style']) && $row['pq_cellattr']['l_group_name']['style']=='font-weight:500;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($subboldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($subboldcell_style);
			 }
		    else if(isset($row['pq_cellattr']['l_group_name']['style']) && $row['pq_cellattr']['l_group_name']['style']=='font-style:italic;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($italiccell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($italiccell_style);
			 }
			 
		    else if(isset($row['pq_cellattr']['r_group_name']['style']) && $row['pq_cellattr']['r_group_name']['style']=='font-weight:bold;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($boldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($boldcell_style);
			 }
			 
			else if(isset($row['pq_cellattr']['r_group_name']['style']) && $row['pq_cellattr']['r_group_name']['style']=='font-weight:500;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($subboldcell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($subboldcell_style);
			 }
			else if(isset($row['pq_cellattr']['r_group_name']['style']) && $row['pq_cellattr']['r_group_name']['style']=='font-style:italic;'){
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->applyFromArray($italiccell_style);
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->applyFromArray($italiccell_style);
			 } 
			 
			 else{
				$spreadsheet->getActiveSheet()->getStyle("A".$counter)->getFont()->setBold( false );
				$spreadsheet->getActiveSheet()->getStyle("C".$counter)->getFont()->setBold( false );
			 }
		
			
		    $sheet->setCellValue('A'.$counter , $l_group_name);
            $sheet->setCellValue('B'.$counter , $l_balance_total);
            $sheet->setCellValue('C'.$counter , $r_group_name);
			$sheet->setCellValue('D'.$counter , $r_balance_total);
		   
		   $spreadsheet->getActiveSheet()->getStyle('B'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			 
			 
		    $counter++;
		}
		
		
		
			
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="ProfitLoss.xlsx"');
		$writer->save('php://output');
		die();				
	}
	
   public function trial_balance(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt    = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end       = date('Y', strtotime($fy_begndt. ' + 1 year'));
	    
		$view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 0;
		 $nil_type = isset($_GET['nil_type'])  && ($_GET['nil_type']!='') ? $_GET['nil_type'] : 1;
        $finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));

        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
        $to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : $finyear['end_date'];

        $from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd = date('Y-m-d', strtotime($to_date));
	
       if($view == 0){		   
            $result = $this->ReportsModel->load_trial_balance($from_date_ymd,$to_date_ymd);
	   }
        else if($view == 1){		
           $result = $this->ReportsModel->load_trial_balance1($from_date_ymd,$to_date_ymd);
		}
        else{
		    $result = $this->ReportsModel->load_trial_balance2();
		}
		
	    $spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Trial Balance");
		$debit_total=0;
		$credit_total=0;
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
        $sheet->setCellValue('A2', 'GROUP/ACCOUNT/BSD');
		$sheet->setCellValue('B2', 'PARENT');
        $sheet->setCellValue('C2', 'DEBIT(₹)');	
		$sheet->setCellValue('D2', 'CREDIT(₹)');		
		$spreadsheet->getActiveSheet()->getStyle("A2:D2")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		
		 $boldcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 12
						));		
		
						
        $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		
		foreach(range('A','D') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}

        $counter = 3;	
		$spreadsheet->getActiveSheet()->getStyle("A1:D1")->getFont()->setBold( true );		
		
		$groups_record=array();
		$accounts_record=array();
		$opening_accoun=array();
		$opbalance_record=array();
		foreach($result as $row){
			
			if($row['debit_total']>0)
				$debit_total=$debit_total+$row['debit_total'];
			if($row['credit_total']>0)
				$credit_total=$credit_total+$row['credit_total'];
			
			
			
			if($row['type']=="opn" && $row['group_id']=="0"){ 			       
						 $opening_accoun[]=$row;
			}
			
			if($row['type']=="grp" || $row['type']=="acc"){ 
			        if($nil_type=="1" && ($row['debit_total']!='0' || $row['credit_total']!='0'))
			           $groups_record[]=$row;
				     if($nil_type=="0" && ($row['debit_total']=='0' || $row['credit_total']=='0'))
						 $groups_record[]=$row;
			}
			if($row['type']=="acc" || $row['type']=="bsd"){	
			     if($nil_type=="1" && ($row['debit_total']!='0' || $row['credit_total']!='0'))
			            $accounts_record[]=$row;
				     if($nil_type=="0" && ($row['debit_total']=='0' || $row['credit_total']=='0'))
						 $accounts_record[]=$row;
			
              						
			}
			if($row['type']=="aop" || $row['type']=="bop"){ 
			        if($nil_type=="1" && ($row['debit_total']!='0' || $row['credit_total']!='0'))
			           $opbalance_record[]=$row;
				     if($nil_type=="0" && ($row['debit_total']=='0' || $row['credit_total']=='0'))
						 $opbalance_record[]=$row;
			}
			
		}
		
		if($view==0){ // groups only 
			foreach($groups_record as $row){			
					$sheet->setCellValue('A'.$counter , html_entity_decode($this->clean($row['group_name'])));
					$sheet->setCellValue('B'.$counter , $this->clean($row['parent']));
					$sheet->setCellValue('C'.$counter , $this->clean($row['debit_total']));
					$sheet->setCellValue('D'.$counter , $this->clean($row['credit_total'])); 
					$spreadsheet->getActiveSheet()->getStyle('C'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );
					 $counter++;
			  }
			
		  }
		if($view==1){ // accounts only 
			foreach($accounts_record as $row){			
					$sheet->setCellValue('A'.$counter , html_entity_decode($this->clean($row['group_name'])));
					$sheet->setCellValue('B'.$counter , $this->clean($row['parent']));
					$sheet->setCellValue('C'.$counter , $this->clean($row['debit_total']));
					$sheet->setCellValue('D'.$counter , $this->clean($row['credit_total'])); 
					$spreadsheet->getActiveSheet()->getStyle('C'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );
					 $counter++;
			  }
			
		  }
		 if($view==2){ // opening balance only 
			foreach($opbalance_record as $row){			
					$sheet->setCellValue('A'.$counter , html_entity_decode($this->clean($row['group_name'])));
					$sheet->setCellValue('B'.$counter , $this->clean($row['parent']));
					$sheet->setCellValue('C'.$counter , $this->clean($row['debit_total']));
					$sheet->setCellValue('D'.$counter , $this->clean($row['credit_total']));
					$spreadsheet->getActiveSheet()->getStyle('C'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );		
					 $counter++;
			  }
			
		  } 
		  
		foreach($opening_accoun as $row){			
					$sheet->setCellValue('A'.$counter , html_entity_decode($this->clean($row['group_name'])));
					$sheet->setCellValue('B'.$counter , $this->clean($row['parent']));
					$sheet->setCellValue('C'.$counter , $this->clean($row['debit_total']));
					$sheet->setCellValue('D'.$counter , $this->clean($row['credit_total'])); 
					
					$spreadsheet->getActiveSheet()->getStyle('C'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			 
			 
					$counter++;
			  }  
		
		$spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.$counter.':B'.$counter);
		$sheet->setCellValue('A'.$counter , 'Total');
		$sheet->setCellValue('C'.$counter , html_entity_decode($this->clean(parseAmount($debit_total))));
		$sheet->setCellValue('D'.$counter , html_entity_decode($this->clean(parseAmount($credit_total))));			
					
		$spreadsheet->getActiveSheet()->getStyle('C'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
		   $spreadsheet->getActiveSheet()->getStyle('D'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			 
			 
		
			
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="TrialBalance.xlsx"');
		$writer->save('php://output');
		die();				
	}		
	
	public function account_ledger(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($fy_begndt. ' + 1 year'));
	    
		$account_id = isset($_GET['accid']) && ($_GET['accid']!='')  ? $_GET['accid'] : 0;
		
		 $account_info          =  $this->AccountsModel->account_info($account_id);
		 $show_account_name     =  $account_info['acc_name'];	  
		
		
		$m = isset($_GET['m']) && ($_GET['m']!='')  ? $_GET['m'] : 0;
		$finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));

		$from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
		$to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : $finyear['end_date'];

		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd = date('Y-m-d', strtotime($to_date)); 
		
		if($m == 1)
			$result = $this->AccountsModel->load_accounts_memo_ledger_export($account_id, $from_date_ymd, $to_date_ymd);
		else
			$result = $this->AccountsModel->load_accounts_ledger_export($account_id, $from_date_ymd, $to_date_ymd);
        
		
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
		 $lastcell_style = array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 15
						));		
		$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
        $sheet->setCellValue('A2', 'DATE');
		$sheet->setCellValue('B2', 'TYPE');
		$sheet->setCellValue('C2', 'VCH/BILL NO');
		$sheet->setCellValue('D2', 'ACCOUNT');
		$sheet->setCellValue('E2', 'NARRATION');
        $sheet->setCellValue('F2', 'DEBIT(₹)');	
		$sheet->setCellValue('G2', 'CREDIT(₹)');
		$sheet->setCellValue('H2', 'BALANCE(₹)');		
		$spreadsheet->getActiveSheet()->getStyle("A2:H2")->applyFromArray(array(
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' => '000000'),
							'size'  => 13
						)));
		foreach(range('A','H') as $columnID) {
			$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}

        $counter = 3;	
		$spreadsheet->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold( true );
		$debit_total=0;
		$credit_total=0;
		foreach($result as $row){
			if($row['debit_total']>0)
				$debit_total=$debit_total+$row['debit_total'];
			if($row['credit_total']>0)
				$credit_total=$credit_total+$row['credit_total'];
			
			$sheet->setCellValue('A'.$counter , $this->clean($row['txn_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['voucher_type']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['voucher_no']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['account_name']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['short_narration']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['debit_total']));
			$sheet->setCellValue('G'.$counter , $this->clean($row['credit_total']));
			$sheet->setCellValue('H'.$counter , $this->clean($row['balance_total']));
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
		header('Content-Disposition: attachment;filename="AccountLedger.xlsx"');
		$writer->save('php://output');
		die();	
	}

	public function account_ledger_detailed($account_id){
			$company_name = $this->session->get('ses_company_name');
			$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
			$fy_end    = date('Y', strtotime($fy_begndt. ' + 1 year'));

			$account_info          =  $this->AccountsModel->account_info($account_id);
			$show_account_name     =  $account_info['acc_name'];	  


			$m = isset($_GET['m']) && ($_GET['m']!='')  ? $_GET['m'] : 0;
			$finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));

			$from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
			$to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : $finyear['end_date'];

			$from_date_ymd = date('Y-m-d', strtotime($from_date));
			$to_date_ymd = date('Y-m-d', strtotime($to_date)); 

			if($m == 1){
				$result = [];
			}	
			else{
				$result = $this->AccountsModel->load_accounts_ledger_detailed_export($account_id,$from_date_ymd, $to_date_ymd);
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
			$lastcell_style = array(
					'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 15
			));		
			$sheet->setCellValue('A1', $company_name.'('.$fy_begndt.'-'.$fy_end.')');
			$sheet->setCellValue('A2', 'DATE');
			$sheet->setCellValue('B2', 'TYPE');
			$sheet->setCellValue('C2', 'VCH/BILL NO');
			$sheet->setCellValue('D2', 'ACCOUNT');
			$sheet->setCellValue('E2', 'NARRATION');
			$sheet->setCellValue('F2', 'DEBIT(₹)');	
			$sheet->setCellValue('G2', 'CREDIT(₹)');
			$sheet->setCellValue('H2', 'BALANCE(₹)');		
			$spreadsheet->getActiveSheet()->getStyle("A2:H2")->applyFromArray(array(
					'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '000000'),
					'size'  => 13
			)));
			foreach(range('A','H') as $columnID) {
					$spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
			}

			$counter = 3;	
			$spreadsheet->getActiveSheet()->getStyle("A1:H1")->getFont()->setBold( true );
			$debit_total=0;
			$credit_total=0;
			foreach($result as $row){
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
					$sheet->setCellValue('E'.$counter , $this->clean($row['short_narration']));
					$sheet->setCellValue('F'.$counter , $this->clean($debit));
					$sheet->setCellValue('G'.$counter , $this->clean($credit));
					$sheet->setCellValue('H'.$counter , $this->clean($row['balance']));
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
			header('Content-Disposition: attachment;filename="AccountLedger.xlsx"');
			$writer->save('php://output');
			die();	
	}
	
	
	public function daybook(){
		$company_name = $this->session->get('ses_company_name');
		$fy_begndt = date('Y',strtotime($this->session->get('ses_company_fy_beginning')));
	    $fy_end    = date('Y', strtotime($fy_begndt. ' + 1 year'));
	    
		$view = isset($_GET['view']) && ($_GET['view']!='')  ? $_GET['view'] : 0;
		$finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));

		$from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
		$to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : $finyear['end_date'];

		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd = date('Y-m-d', strtotime($to_date)); 
		$result = array();
		ob_start();
		 $result = $this->ReportsModel->load_day_book_condensed(1, 0, $from_date_ymd, $to_date_ymd, $view);
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
			$sheet->setCellValue('B'.$counter , $this->clean($row['particulars']));
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
	       $fy_end    = date('Y', strtotime($fy_begndt. ' + 1 year'));
	       $exprt_type = $_GET['exprt_type'];
		   $item_id    = $_GET['itmid'];    
		   $start_date = $_GET['frmdt'];
		   $end_date   = $_GET['todt'];
		   $item_id    = $_GET['itmid'];
		   $unitid     = $_GET['untid'];
		   $grpid      = $_GET['mcgrpid'];
		   $mcid       = $_GET['mcid'];
		   $valuation_type = $_GET['valuation_type'];	  
		
		 $item_info              =  $this->ItemsModel->get_item_info($item_id,$this->company_id);
		$finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));

		$from_date = !empty($_GET['frmdt']) ? $_GET['frmdt'] : $finyear['start_date'];
		$to_date = !empty($_GET['todt']) ? $_GET['todt'] : $finyear['end_date'];

		$from_date_ymd = date('Y-m-d', strtotime($from_date));
		$to_date_ymd = date('Y-m-d', strtotime($to_date)); 
		
		 $itm_transaction_table  =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');	 
		 
		$result = $this->ItemsModel->load_item_transactions($from_date_ymd,$to_date_ymd,$itm_transaction_table,$item_id,$this->company_id,$item_info,$unitid,$grpid,$mcid,$valuation_type);

		$spreadsheet = new Spreadsheet();      
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("iTEM Ledger");
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
		$sheet->setCellValue('D2', 'INWARD');
		$sheet->setCellValue('E2', 'OUTWARD');
        $sheet->setCellValue('F2', 'CLOSING');	
		$sheet->setCellValue('G2', 'UOM');		
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
		
		foreach($result as $row){
			
			$sheet->setCellValue('A'.$counter , $this->clean($row['txn_date']));
			$sheet->setCellValue('B'.$counter , $this->clean($row['party_name']));
			$sheet->setCellValue('C'.$counter , $this->clean($row['voucher_type']));
			$sheet->setCellValue('D'.$counter , $this->clean($row['inward_qty']));
			$sheet->setCellValue('E'.$counter , $this->clean($row['outward_qty']));
			$sheet->setCellValue('F'.$counter , $this->clean($row['closing_val']));
			$sheet->setCellValue('G'.$counter , $this->clean($row['unit_name']));

			 $spreadsheet->getActiveSheet()->getStyle('F'.$counter)->getAlignment()->applyFromArray(
			array(
				"horizontal" => "right", 
				"vertical" => "center"
			)
		     );	
			 
			$counter++;
		}
		
		if($exprt_type=='excel'){
			
			/* $writer = IOFactory::createWriter($spreadsheet, 'Mpdf');
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment;filename="ItemLedger.pdf"');
			header('Cache-Control: max-age=0'); */
			
			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="ItemLedger.xlsx"'); 
		}
		else if($exprt_type=='csv'){
			$writer = IOFactory::createWriter($spreadsheet, 'Csv');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="ItemLedger.csv"');
		}
		
		$writer->save('php://output');
		die();	
	}

	public function acc_ledger_detail(){

		echo 333;
	}
}