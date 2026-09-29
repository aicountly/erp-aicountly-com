<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\AccountsModel;
use App\Models\Grpcomp\ERPLogModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Style;
class Accounts extends BaseController{
  function __construct(){  
	    helper(['form', 'url','text','custom']);
			$this->AccountsModel     = new AccountsModel();
			$this->LogModel          = new ERPLogModel();		
			$this->CommonModel       =  new CommonModel();		
			$this->auth_session      = new auth_session();
			$this->auth_session->group_restrict();
			$this->auth_session->role_restrict('CS');
			$this->base_url      = base_url().'/'.getenv('GroupPath');
			$this->admin_url      = base_url().'/'.getenv('AdminPath');
			$this->folder_path   = getenv('GroupPath');
			$this->session    	 = \Config\Services::session();
			$this->forge         = \Config\Database::forge();
			$this->company_id    =  $this->session->get('ses_company_id');
			$this->comp_code     =  $this->session->get('ses_company_code');
			$this->enc_string    = new enc_string();
		
    }
   
     public  function ajax_accounts_view()
	 {
		echo $response =  $this->AccountsModel->ajax_accounts_list();	
		
	 } 
	public function list()
    {
			echo $this->index();
	}		

	public function acc_ledger_detail(){

		echo 333;
		die();
	}


	public function index()
    {
		if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $comp_id       = $comp_id_info[1];			 
			 $this->CommonModel->choose_grp_company($comp_id);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->set('ses_company_id','');
			$grpcmpid='';
		}
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']        = $this->admin_url;
		$data['session']         = $this->session;
		$data['grpcmpid']        = $grpcmpid;
		$group_comp_list         = $this->CommonModel->all_group_companies();
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
        $data['comp_list'] = $comp_list;
		return view($this->folder_path.'accounts/view',$data);		
    }  
   function update_opn_balances(){
	   
	    if($this->request->getMethod() == 'post'){	
		
		 $accbaldata       = $this->request->getVar('accbaldata'); 
		 if($accbaldata){
			 $accbaldata =  json_decode($accbaldata,true);
			  if($accbaldata){
				  foreach($accbaldata as $row){
					  $acc_id   = $row['acc_id'];
					  $op_bal   = abs($row['op_bal']);
					  $bal_type   = $row['bal_type'];					  
					  $isedited = $row['isedited'];
					  if($isedited=="1"){	
					  	if($bal_type == 'CR.')				  
								$updata  = array("acc_op_bal"=> -$op_bal);
							else
								$updata  = array("acc_op_bal"=> $op_bal);

							$this->AccountsModel->update_acc_op_bal($acc_id,$updata);
							$this->TransactionModel->update_account_balance($acc_id);
					  }
				  }
				  
				 return redirect()->to($this->base_url.'accounts/list');
				 die;  
			  }
		   }
		}else{
			return redirect()->to($this->base_url.'accounts/list');
			die;
		   }
   }	
	
	public function accounts_trial($group_id, $parent = 0)
    {
    	$finyear  = $this->CommonModel->calculateFiscalYearForDate(date('m'));
    	// $nil_type = isset($_GET['nil_type']) ? $_GET['nil_type'] : 1;
        $from_date = !empty($_GET['from_date']) ? $_GET['from_date'] : $finyear['start_date'];
        $to_date = !empty($_GET['to_date']) ? $_GET['to_date'] : $finyear['end_date'];

        $from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd = date('Y-m-d', strtotime($to_date));

    	if($parent == 1)
    	{
    			$group_info = 	$this->AccountsModel->group_parent_info($group_id);
    			$data['name'] = 	$group_info['acc_grp_parent'];
    			$data['ac_trial_balance_list'] = $this->AccountsModel->load_accounts_trial_balance3($group_id,$from_date_ymd,$to_date_ymd);
    	}
    	else
    	{
    			$group_info = 	$this->AccountsModel->main_group_info($group_id);
    			$data['name'] = 	$group_info['acc_grp_name'];
    			$data['ac_trial_balance_list'] = $this->AccountsModel->load_accounts_trial_balance2($group_id,$from_date_ymd,$to_date_ymd);
    	}
	 $data['from_date'] = $from_date;
     $data['to_date']   = $to_date;
     // $data['nil_type']  = $nil_type;
	 return view($this->folder_path.'accounts/accounts_trial',$data);
	} 

   public function group_report($group_id)
    { 
	  if(!$group_id)
		 return redirect()->to($this->base_url.'accounts/list');
	 $group_info = 	$this->AccountsModel->main_group_info($group_id);
	 if(!$group_info)
		 return redirect()->to($this->base_url.'accounts/list');
	$data = array('base_url'=>$this->base_url,'groups_list'=>$this->AccountsModel->groups_transactions($group_id,$this->company_id),
	              'group_info'=>$group_info
				  ); 		

	return view($this->folder_path.'accounts/group_report',$data);
	}
	
	 
 
   public function monthly_detail($acc_id)
    {
	$ses_comp_fy_id = $this->session->get('ses_comp_fy_id');	
	 if(!$acc_id)
	   return redirect()->to($this->base_url.'balance_sheet'); 

     $account_info = 	$this->AccountsModel->account_info($acc_id); 
	 if(!$account_info)
		 return redirect()->to($this->base_url.'balance_sheet'); 
	
	
	//get account opening balance
	  $get_opn_balance_info = $this->AccountsModel->acc_opn_balance_info($acc_id);
	  // echo "<pre>";print_r($get_opn_balance_info);exit;
	 	if(!$get_opn_balance_info){
		  $acc_opn_balance = '0';
		  $balance_type     ='Dr.';
	     }
	     else{
		 $acc_opn_balance  = abs($get_opn_balance_info['acc_op_bal']);
		 $balance_type      = ($get_opn_balance_info['acc_op_bal']<0)?'Cr.':'Dr.';
		 } 
	  
	 $account_name       = $account_info['acc_name'];
	 $account_tablename  =  'acc'.$account_info['acc_id'];
	 $fy_months_list     =  $this->CommonModel->get_fy_info($ses_comp_fy_id,$this->company_id); 
	 $data               = array(
	 	'acc_opn_balance'=>$acc_opn_balance.' '.$balance_type,
	 	'account_tablename'=>$account_tablename,
	 	'fy_months_list'=>$fy_months_list,
	 	'account_name'=>$account_name,
	 	'acc_months_list'=>$this->AccountsModel->accounts_monthly_balance($acc_id,$this->company_id,$fy_months_list,$account_tablename)
	 ); 
	
	return view($this->folder_path.'accounts/accounts_monthly',$data);
	}    

	public function ajax_accounts_ledger()
	{
		$pq_curPage = (int)$_POST["pq_curpage"];
		$limit     = (int)$_POST["pq_rpp"];
	 	

		$from_date     = date('Y-m-d',strtotime($this->request->getVar('from_date')));
		$to_date       = date('Y-m-d',strtotime($this->request->getVar('to_date')));
		$account_id    = $this->request->getVar('account_id');
		$m    = $this->request->getVar('m');
		$view    = $this->request->getVar('view');

		$search = '';
		$pq_filter    = $this->request->getVar('pq_filter');
		if(!empty($pq_filter)){
			$pq_filter = json_decode($pq_filter);
			$search = $pq_filter->data[0]->value;
		}
		

		// echo $this->ReportsModel->load_day_book($pq_curPage, $limit, $offset, $from_date, $to_date,$view_type);
		
		
		//echo $limit .', '.$offset;
		//echo '<br>';
		if($m == 1){
			$response = $this->AccountsModel->load_accounts_memo_ledger($account_id, $limit, $from_date, $to_date,$pq_curPage);
		}
		else{
			if($view == 1)
				$response = $this->AccountsModel->load_accounts_ledger_detailed($account_id, $limit, $from_date, $to_date,$pq_curPage,$search);
			else
				$response = $this->AccountsModel->load_accounts_ledger_condensed($account_id, $limit, $from_date, $to_date,$pq_curPage,$search);
		}

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}
	
	
	public function ajax_list_groups()
	{
	    if(isset($_POST["pq_curpage"]))
		$pq_curPage = (int)$_POST["pq_curpage"];
	   else
		  $pq_curPage = 1;
	  
	  if(isset($_POST["pq_rpp"]))
		$limit     = (int)$_POST["pq_rpp"];
	  else
		 $limit     = 100; 
	 	$response = $this->AccountsModel->load_list_groups($limit,$pq_curPage);

		return json_encode([
					'totalRecords' => $response['total_records'],
					'data'	=> $response['data'],
					'curPage' => $pq_curPage
			]);
	}
	
	
   
   public function ledger_detail($account_id){
    
       $m = !empty($_GET['m']) ? $_GET['m'] : 0;
       $view = !empty($_GET['view']) ? $_GET['view'] : 0;

       $request = \Config\Services::request();
       
       
    if(!$account_id)
	    return redirect()->to($this->base_url.'accounts/ledger'); 
	  
	
      if($request->uri->setSilent()->getSegment(5)){
           $from_date  = date('d-m-Y',strtotime($request->uri->setSilent()->getSegment(5)));
      }
      else
	  $from_date = !empty($_GET['from_date']) ? date('d-m-Y',strtotime($_GET['from_date'])) : date('01-m-Y');
	  
	   if($request->uri->setSilent()->getSegment(6)){
	        $to_date  = date('d-m-Y',strtotime($request->uri->setSilent()->getSegment(6)));
	   }
	   else
		$to_date   = !empty($_GET['to_date']) ? date('d-m-Y',strtotime($_GET['to_date'])) : date('d-m-Y');
		
     
	
      $ses_comp_fy_id        =  $this->session->get('ses_comp_fy_id');	
	  $account_info          =  $this->AccountsModel->account_info($account_id);	
	  $account_name          =  url_title($account_info['acc_name'],'',true);
	  $show_account_name     =  $account_info['acc_name'];	  
	  $acc_transaction_table =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;
	  
	  //get account opening balance
	  $get_opn_balance_info = $this->AccountsModel->acc_opn_balance_info($account_id);
	  // echo "<pre>";print_r($get_opn_balance_info);exit;
		if(!$get_opn_balance_info){
		  $acc_op_bal = 0;
	     }
	     else{
		  // $acc_op_bal  = formatAmount($get_opn_balance_info['acc_op_bal']);
		   $acc_op_bal  = $get_opn_balance_info['acc_op_bal'];
	     } 

	//   $account_opn_balance = $this->AccountsModel->account_opn_balance($account_id,$from_date,$acc_op_bal);
	//   if($account_opn_balance<0)
    //     $show_acc_opn_balance = formatAmount(abs($account_opn_balance)).' Cr';
    // else
    //     $show_acc_opn_balance = formatAmount($account_opn_balance).' Dr';

	$last_transaction = $this->AccountsModel->last_transaction($account_id,date('Y-m-d', strtotime($from_date)));
	// echo "<pre>";print_r($last_transaction);exit;
	if($last_transaction){
		$acc_op_bal = $last_transaction['acc_bal'];
	}

	if($acc_op_bal<0)
        $show_acc_opn_balance = formatAmount(abs($acc_op_bal)).' Cr';
    else
        $show_acc_opn_balance = formatAmount($acc_op_bal).' Dr';

    if($m == 1){
    	$show_acc_opn_balance = '0.00 Dr';
    }
	 
	  $data = array(
			'show_account_name'=>$show_account_name,
			'account_name'=>$account_name,
			'account_id'=>$account_id,
			'show_acc_opn_balance' => $show_acc_opn_balance,
			'from_date'=>$from_date,
			'to_date'=>$to_date,
			'm'	=> $m,
			'view'	=> $view,
	  );

	  $data['accounts_list'] = $this->AccountsModel->get_all_accounts();
	                
      return view($this->folder_path.'accounts/account_ledger_view',$data);   
       
   }

    
   
   
   
   public function group_ledger_detail($group_id,$start_date=NULL,$end_date=NULL){
       
    if(!$group_id)
	    return redirect()->to($this->base_url.'accounts/ledger'); 
	
      $ses_comp_fy_id        =  $this->session->get('ses_comp_fy_id');	
	  $group_info          =  $this->AccountsModel->main_group_info($group_id);	
	  $name     =  $group_info['acc_grp_name'];
	  
	  
	  $data = array(
	  				'name'=>$name,
	  				'account_transactions'=>$this->AccountsModel->load_group_transactions($start_date,$end_date,$group_id),
	       		'from_date'=>$start_date
	     			);
	                
      return view($this->folder_path.'accounts/group_ledger_view',$data);   
       
   }
   
	public function export($extension="excel"){
		$spreadsheet = new Spreadsheet();
		$fileNames    = 'accountslist'; 
		
		$DealerTypes = DealerTypes();
		$company_info = $this->CommonModel->get_company_info($this->company_id);
		$company_name = $this->enc_string->nc_string($company_info['comp_name'],'de');
		$company_adrs1 = $this->enc_string->nc_string($company_info['ro_add1'],'de');
		$company_adrs2 = $this->enc_string->nc_string($company_info['ro_add2'],'de');
		$company_adrs  = $company_adrs1;
		
		$accounts_list   = $this->AccountsModel->ajax_accounts_list();	
		    // set Header
        $sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->getActiveSheet()->setTitle('Accounts');

		$sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
		$sheet->mergeCells('A3:H3');
		$sheet->setCellValue('A1', strtoupper($company_name));
		$sheet->setCellValue('A2', strtoupper($company_adrs));
		$sheet->setCellValue('A3',"List of Accounts");
		
		$sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
		$sheet->getStyle('A2:H2')->getAlignment()->setHorizontal('center'); 
		$sheet->getStyle('A3:H3')->getAlignment()->setHorizontal('center'); 

		$sheet->getStyle('A1:H1')->getFont()->setName('Times New roman')->setBold( true )->setSize(18);
		$sheet->getStyle('A2:H2')->getFont()->setName('Times New roman')->setBold( true )->setSize(12); 
		$sheet->getStyle('A3:H3')->getFont()->setName('Times New roman')->setBold( true )->setSize(14); 
		 
		$sheet->setCellValue('A5', 'Name');
		$sheet->setCellValue('B5', 'Alias');
		$sheet->setCellValue('C5', 'Parent Group');
		$sheet->setCellValue('D5', 'Op. Bal.(Dr)');
		$sheet->setCellValue('E5', 'Op.Bal.(Cr)');
		$sheet->setCellValue('F5', 'Type of Dealer');  
		$sheet->setCellValue('G5', 'GSTIN'); 
	    $sheet->setCellValue('H5', 'Filling Frequency');  
		
		foreach(range('A','H') as $columnID) {
		  $sheet->getColumnDimension($columnID)->setAutoSize(true);
		 } 
		$sheet->getStyle('A5:H5')->getFont()->setBold( true )->setUnderline(true);
		 
 	    $rowCount = 6;
		$OpBalDr=0;
		$OpBalCr=0;
		$dr_balance=$cr_balance=0;
        foreach ($accounts_list as $row) {
			$acc_dealer_type = $row['acc_dealer_type'];
		  if(isset($DealerTypes[$acc_dealer_type])){
			$show_acc_dealer_type = $DealerTypes[$acc_dealer_type];
		   }	
		   else
			$show_acc_dealer_type ='';
		
		   if($row['acc_op_drcr']=='dr'){
		     $OpBalDr = $OpBalDr+$row['acc_op_bal'];
			 $dr_balance = $row['acc_op_bal'];
             $cr_balance =0;			 
		   }
		 else if($row['acc_op_drcr']=='cr'){
		     $OpBalCr = $OpBalCr+$row['acc_op_bal'];
             $cr_balance = $row['acc_op_bal'];
             $dr_balance =0;			 
		   }
			
           $sheet->setCellValue('A'.$rowCount, $row['account_name']);
		   $sheet->setCellValue('B'.$rowCount, $row['acc_name_alias']);
		   $sheet->setCellValue('C'.$rowCount, $row['group_name']);
		   $sheet->setCellValue('D'.$rowCount, $dr_balance);
		   $sheet->setCellValue('E'.$rowCount, $cr_balance);
		   $sheet->setCellValue('F'.$rowCount, $show_acc_dealer_type);
		   $sheet->setCellValue('G'.$rowCount, $row['acc_gstin']);
		   $sheet->setCellValue('H'.$rowCount, 'Not Known');
          
		   
		   $rowCount++;
		}
		
		$sheet->setCellValue('C'.$rowCount , "");
		$sheet->setCellValue('D'.$rowCount , "");
		$sheet->setCellValue('E'.$rowCount , "");
		
		$sheet->setCellValue('C'.($rowCount+1) , "Total");
		$sheet->setCellValue('D'.($rowCount+1),$OpBalDr);
		$sheet->setCellValue('E'.($rowCount+1),$OpBalCr);
		
		$sheet->getStyle('C'.($rowCount+1))->getFont()->setBold( true );
		$sheet->getStyle('D'.($rowCount+1))->getFont()->setBold( true );
		$sheet->getStyle('E'.($rowCount+1))->getFont()->setBold( true );
		
	  if($extension == 'csv'){          
		  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
		  $fileName = $fileNames.'.csv';
		} elseif($extension == 'xlsx') {
		  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		  $fileName = $fileNames.'.xlsx';
		} 
		elseif($extension == 'pdf') {
		 $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
		  $fileName = $fileNames.'.pdf';
		 } 
		else {
		  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		  $fileName = $fileNames.'.xls';
	    }	
	  $writer->save(WRITEPATH."uploads/".$fileName);
      $spreadsheet->disconnectWorksheets();
	  unset($spreadsheet);




/*
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, $_POST["file_type"]);

  $file_name = time() . '.' . strtolower($_POST["file_type"]);

  $writer->save($file_name);

  header('Content-Type: application/x-www-form-urlencoded');

  header('Content-Transfer-Encoding: Binary');

  header("Content-disposition: attachment; filename=\"".$file_name."\"");

  readfile($file_name);

  unlink($file_name);

  exit;

*/	
	}   
    public function list_group()
    {
		if(isset($_GET['grpcmpid']) && $_GET['grpcmpid']!=''){
			 $comp_id_info  = unobfuscate_link($_GET['grpcmpid']);
			 $comp_id       = $comp_id_info[1];			 
			 $this->CommonModel->choose_grp_company($comp_id);
			 $grpcmpid     = $_GET['grpcmpid'];
		}
		else{
			$this->session->set('ses_company_id','');
			$grpcmpid='';
		}
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['admin_url']        = $this->admin_url;
		$data['session']         = $this->session;
		$data['grpcmpid']        = $grpcmpid;
		$group_comp_list         = $this->CommonModel->all_group_companies();
		$comp_list = array();
		$comp_list[''] = 'Choose Group Company';
		if($group_comp_list){
			foreach($group_comp_list as $row){
				$comp_list[$row['encomp_id']]=$row['company_name'];	
			}
		}
        $data['comp_list'] = $comp_list;
		return view($this->folder_path.'accounts/list_group',$data);		
    } 
	
   public function add()
   {if($this->session->get('ses_boid')!='')
									$bo_id = $this->session->get('ses_boid');
								else 
									 $bo_id =1;
		 if($this->request->getMethod() == 'post'){	
		     
		  
			$account_name       = $this->request->getVar('account_name'); 
			$account_alias      = $this->request->getVar('account_alias');
			$account_print_name = $this->request->getVar('account_print_name');
			$account_primary    = $this->request->getVar('account_primary');
			$account_group      = $this->request->getVar('account_group');
			$parent_group       = $this->request->getVar('parent_group');			
			$acc_symbol         = $this->request->getVar('acc_symbol'); 
			$acct_opp_bal       = $this->request->getVar('acct_opp_bal');
			$acct_opp_bal_dr_cr = $this->request->getVar('acct_opp_bal_dr_cr');
			$acct_prv_bal       = $this->request->getVar('acct_prv_bal');	
			$acct_prv_dr_cr     = $this->request->getVar('acct_prv_dr_cr');			
			$adrs1              = $this->request->getVar('acc_adrs1');
			$adrs2              = $this->request->getVar('acc_adrs2');
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('acc_state_id');
			$city               = $this->request->getVar('acc_city');
			$pincode            = $this->request->getVar('acc_pincode');           
			$acc_dealer_type    = $this->request->getVar('acc_dealer_type');			
			$acct_gstin         = $this->request->getVar('acct_gstin');
			$acct_aadhar        = $this->request->getVar('acct_aadhar');
			$acct_tan           = $this->request->getVar('acct_tan');
			$acct_pan           = $this->request->getVar('acct_pan');
			$acc_jurisdiction   = $this->request->getVar('acc_jurisdiction');
			$acc_email          = $this->request->getVar('acc_email');
			$acct_tel           = $this->request->getVar('acct_tel');						
			$acct_wa_mobile     = $this->request->getVar('acct_wa_mobile');
			$acct_mobile        = $this->request->getVar('acct_mobile');
			$acct_fax           = $this->request->getVar('acct_fax');
			$acct_transport     = $this->request->getVar('acct_transport');			
			$acct_station       = $this->request->getVar('acct_station');
			$acct_distance      = $this->request->getVar('accy_distance');
			$acct_iec           = $this->request->getVar('acct_iec');
			$acct_prof_tax      = $this->request->getVar('acct_prof_tax');	
			$account_constitution = $this->request->getVar('account_constitution');	
			$vendor_code          = $this->request->getVar('vendor_code');	
			$rules = [				
				'account_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]]
				  			   
			       ];
			
            if(!$this->validate($rules)){
              $this->message_output->set_error($this->validator->listErrors());
            }else{				
			    $state_info    =  $this->CommonModel->get_state_info($country_id,$state_id);
				if($state_info)
				  $state_code    = $state_info['state_code'];
			   else
				 $state_code    = '';

				if($account_primary == 'Y'){
					$acc_grp_parent_id = $parent_group;
					$acc_grp_id = 0;
				}
				else{
					$acc_grp_parent_id = 0;
					$acc_grp_id = $account_group;
				}



				$insert_data   = [
						'comp_id'         => $this->company_id,
						'acc_name'        => ucwords(clean($account_name)), //remove special characters
						'acc_name_alias'  => $account_alias,
						'acc_name_print'  => $account_print_name,
						'acc_grp_id'      => $account_group,
						'acc_grp_parent_id' => $parent_group,
						'vendor_code'       => $vendor_code,
						//'acc_op_bal'      => $acct_opp_bal,
						//'acc_op_drcr'     => $acct_opp_bal_dr_cr,
						//'acc_prv_bal'     => $acct_prv_bal,
						//'acc_prv_drcr'    => $acct_prv_dr_cr,
						'acc_add1'        => $this->enc_string->nc_string($adrs1,'en'),
						'acc_add2'        => $this->enc_string->nc_string($adrs2,'en'),
						'acc_country'     => $country_id,
						'acc_symbol'      => $acc_symbol,
						'acc_state'       => $state_id,
						'acc_state_code'  => $state_code,
						'acc_city'        => $this->enc_string->nc_string($city,'en'),						
						'acc_pin'         => $this->enc_string->nc_string($pincode,'en'),
						'acc_gstin'       => $this->enc_string->nc_string($acct_gstin,'en'),
						'acc_aadhar'      => $this->enc_string->nc_string($acct_aadhar,'en'),
						'acc_tan'         => $this->enc_string->nc_string($acct_tan,'en'),
						'acc_pan'         => $this->enc_string->nc_string($acct_pan,'en'),
						'acc_jurisd'      => $this->enc_string->nc_string($acc_jurisdiction,'en'),
						'acc_email'       => $this->enc_string->nc_string($acc_email,'en'),
						'acc_tel'         => $this->enc_string->nc_string($acct_tel,'en'),
						'acc_wamobile'    => $this->enc_string->nc_string($acct_wa_mobile,'en'),
						'acc_mobile'      => $this->enc_string->nc_string($acct_mobile,'en'),
						'acc_fax'         => $this->enc_string->nc_string($acct_fax,'en'),
						'acc_transport'   => $acct_transport,
						'acc_station'     => $acct_station,						
						'acc_distance'    => $acct_distance,
						'acc_iec'         => $this->enc_string->nc_string($acct_iec,'en'),
						'acc_prof_tax'    => $this->enc_string->nc_string($acct_prof_tax,'en'),
						'acc_dealer_type' => $acc_dealer_type,
						'acc_const'=>$account_constitution
					    ];
			 $response = $this->AccountsModel->add_account($insert_data);		
			if(!$response['status']){
				  $this->message_output->set_error($response['message']);
			}
			else{
			    $account_id = $response['account_id'];

			    if($vendor_code == ''){
			    	$this->AccountsModel->update_vendor_code(['vendor_code' => $account_id],$account_id);
			    }
				
				// save account opening balance 
				if($acct_opp_bal_dr_cr=='dr'){
					$acc_op_bal_val = $acct_opp_bal;
				}
				else if($acct_opp_bal_dr_cr=='cr'){
					$acc_op_bal_val = '-'.$acct_opp_bal;
				}else
					$acc_op_bal_val = '0';
				
				if($acct_prv_dr_cr=='dr'){
					$acc_pr_bal_val = $acct_prv_bal;
				}
				else if($acct_prv_dr_cr=='cr'){
					$acc_pr_bal_val = '-'.$acct_prv_bal;
				}else
					$acc_pr_bal_val = '0';
				
				$opn_bal_data = array("acc_id"=>$account_id,"acc_op_bal"=>$acc_op_bal_val,"acc_py_bal"=>$acc_pr_bal_val,"bo_id"=>$bo_id);
				$this->AccountsModel->insert_acc_op_bal($opn_bal_data);
				
			    $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'add',
			            'log_field_id' => $account_id,
			            'log_field_name' => $account_name,
			            'log_field_type' => 'accounts',
			        ];
			    $this->LogModel->add_log($log);
				
				 /*****  Create individual table transaction on adding Account Table **************/
				$ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
				
			    $account_txn_table_name =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;
				$this->AccountsModel->CreateAccountTxnTable($account_txn_table_name);	
			    $file  =     WRITEPATH.'comp'.$this->company_id.'/acc.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $accounts_list  = $this->AccountsModel->company_all_accounts();
		            $comp_folder    = 'comp'.$this->company_id;
		            $f = fopen($file, 'a');
                    fwrite($f,$accounts_list);
			     }
			  
		     
			    return redirect()->to($this->base_url.'accounts/list');
				 die;	
			    }				 
	    	}					 									
	   }	 
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['group_main_dropdown']   = $this->AccountsModel->group_main_dropdown();
        $data['group_primary_dropdown']   = $this->AccountsModel->group_primary_dropdown();
        $data['CountryDropdown']       = $this->CommonModel->CountryDropdown();
		$data['CurrencyDropdown']       = $this->AccountsModel->currency_dropdown();
		$data['StatesDropdown']        = $this->CommonModel->StatesDropdown();
		$data['GSTTaxCategory']        = GSTTaxCategory();
		$data['GstTypes']              = GstTypes();
		$data['constitution']          = CompanyConstitutions();
		$data['DealerTypeDropdown']    = DealerTypes();
		$data['itc_eligibility']       = AccountItcEllig();
		$data['rcm_nature']            = AccountRcmNature();
		$data['base_url']              = $this->base_url;
	    return view($this->folder_path.'accounts/add',$data);		
    }
	
	public function modify($account_id)
    {
		if(!$account_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		$account_info = $this->AccountsModel->account_info($account_id);
		if(!$account_info)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		else{
			if($account_info['acc_grp_parent_id']==14)
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
			
		}
		
		if($this->request->getMethod() == 'post'  && $this->request->isAjax()){	
			$account_name       = $this->request->getVar('account_name'); 
			$account_alias      = $this->request->getVar('account_alias');
			$account_print_name = $this->request->getVar('account_print_name');
			$acc_symbol         = $this->request->getVar('acc_symbol'); 
			$account_primary    = $this->request->getVar('account_primary');
			$account_group      = $this->request->getVar('account_group');
			$parent_group       = $this->request->getVar('parent_group');			
			$acct_symbol        = $this->request->getVar('acct_symbol'); 
			$acct_opp_bal       = $this->request->getVar('acct_opp_bal');
			$acct_opp_bal_dr_cr = $this->request->getVar('acct_opp_bal_dr_cr');
			$acct_prv_bal       = $this->request->getVar('acct_prv_bal');	
			$acct_prv_dr_cr     = $this->request->getVar('acct_prv_dr_cr');			
			$adrs1              = $this->request->getVar('acc_adrs1');
			$adrs2              = $this->request->getVar('acc_adrs2');
			$country_id         = $this->request->getVar('acc_country_id');	 
			$state_id           = $this->request->getVar('acc_state_id');
			$city               = $this->request->getVar('acc_city');
			$pincode            = $this->request->getVar('acc_pincode');           
			$acc_dealer_type    = $this->request->getVar('acc_dealer_type');			
			$acct_gstin         = $this->request->getVar('acct_gstin');
			$acct_aadhar        = $this->request->getVar('acct_aadhar');
			$acct_tan           = $this->request->getVar('acct_tan');
			$acct_pan           = $this->request->getVar('acct_pan');
			$acc_jurisdiction   = $this->request->getVar('acc_jurisdiction');
			$acc_email          = $this->request->getVar('acc_email');
			$acct_tel           = $this->request->getVar('acct_tel');						
			$acct_wa_mobile     = $this->request->getVar('acct_wa_mobile');
			$acct_mobile        = $this->request->getVar('acct_mobile');
			$acct_fax           = $this->request->getVar('acct_fax');
			$acct_transport     = $this->request->getVar('acct_transport');			
			$acct_station       = $this->request->getVar('acct_station');
			$acct_distance      = $this->request->getVar('accy_distance');
			$acct_iec           = $this->request->getVar('acct_iec');
			$acct_prof_tax      = $this->request->getVar('acct_prof_tax');
			$account_constitution = $this->request->getVar('account_constitution');
			$vendor_code          = $this->request->getVar('vendor_code');			
			$rules = [				
				'account_name' => [
					'label'  => 'Name',
					'rules'  => 'required',
					'rules'  => "required",
					'errors' => [
						'required' => 'Please enter name',
					   ]]
				  			   
			       ];
			
            if(!$this->validate($rules)){
              // $this->message_output->set_error($this->validator->listErrors());
            	$errors = $this->validator->getErrors();
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
            }else{				
			    $state_info      =  $this->CommonModel->get_state_info($country_id,$state_id);
				if($state_info)
				  $state_code    = $state_info['state_code'];
			   else
				 $state_code    = '';

				if($account_primary == 'Y'){
					$acc_grp_parent_id = $parent_group;
					$acc_grp_id = 0;
				}
				else{
					$acc_grp_parent_id = 0;
					$acc_grp_id = $account_group;
				}

				// save account opening balance 
				if($acct_opp_bal_dr_cr=='dr'){
					$acc_op_bal_val = $acct_opp_bal;
				}
				else if($acct_opp_bal_dr_cr=='cr'){
					$acc_op_bal_val = -$acct_opp_bal;
				}else
					$acc_op_bal_val = 0;
				
				if($acct_prv_dr_cr=='dr'){
					$acc_pr_bal_val = $acct_prv_bal;
				}
				else if($acct_prv_dr_cr=='cr'){
					$acc_pr_bal_val = -$acct_prv_bal;
				}else
					$acc_pr_bal_val = 0;

				$stat = $this->AccountsModel->check_op_change_pnl($account_id, $acc_op_bal_val);
				if(!$stat){
					return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => ['Opening balance for accounts under Profit & Loss is restricted']]);
				}


				$update_data   = [
						'acc_name'        => ucwords(clean($account_name)),
						'acc_name_alias'  => ucwords(clean($account_alias)),
						'acc_name_print'  => ucwords(clean($account_print_name)),
						'acc_grp_id'      => $acc_grp_id,
						'acc_grp_parent_id' => $acc_grp_parent_id,
						//'acc_op_bal'      => $acct_opp_bal,
						//'acc_op_drcr'     => $acct_opp_bal_dr_cr,
						'acc_symbol'      => $acc_symbol,
						//'acc_prv_bal'     => $acct_prv_bal,
						//'acc_prv_drcr'    => $acct_prv_dr_cr,
						'acc_add1'        => $this->enc_string->nc_string($adrs1,'en'),
						'acc_add2'        => $this->enc_string->nc_string($adrs2,'en'),
						'acc_country'     => $country_id,
						'acc_state'       => $state_id,
						'acc_state_code'  => $state_code,
						'acc_city'        => $this->enc_string->nc_string($city,'en'),						
						'acc_pin'         => $this->enc_string->nc_string($pincode,'en'),
						'acc_gstin'       => $this->enc_string->nc_string($acct_gstin,'en'),
						'acc_aadhar'      => $this->enc_string->nc_string($acct_aadhar,'en'),
						'acc_tan'         => $this->enc_string->nc_string($acct_tan,'en'),
						'acc_pan'         => $this->enc_string->nc_string($acct_pan,'en'),
						'acc_jurisd'      => $this->enc_string->nc_string($acc_jurisdiction,'en'),
						'acc_email'       => $this->enc_string->nc_string($acc_email,'en'),
						'acc_tel'         => $this->enc_string->nc_string($acct_tel,'en'),
						'acc_wamobile'    => $this->enc_string->nc_string($acct_wa_mobile,'en'),
						'acc_mobile'      => $this->enc_string->nc_string($acct_mobile,'en'),
						'acc_fax'         => $this->enc_string->nc_string($acct_fax,'en'),
						'acc_transport'   => $acct_transport,
						'acc_station'     => $acct_station,						
						'acc_distance'    => $acct_distance,
						'acc_iec'         => $this->enc_string->nc_string($acct_iec,'en'),
						'acc_prof_tax'    => $this->enc_string->nc_string($acct_prof_tax,'en'),
						'acc_dealer_type' => $acc_dealer_type,
						'acc_const'       => $account_constitution,
						'vendor_code'     => $vendor_code
					    ];
			         $response = $this->AccountsModel->update_account($update_data,$account_id);
			        if(!$response['status']){
        				  // $this->message_output->set_error($response['message']);
        				  return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => [$response['message']]]);
        			}
        			else{
        			    $account_id = $response['account_id'];
						
						if($this->session->get('ses_boid')!='')
						   $bo_id =$this->session->get('ses_boid');
						else
							$bo_id =1;
		
				
						$opn_bal_data = array("acc_id"=>$account_id,"acc_op_bal"=>$acc_op_bal_val,"acc_py_bal"=>$acc_pr_bal_val,'bo_id'=>$bo_id);
			        	$this->AccountsModel->update_acc_op_bal($account_id,$opn_bal_data);
						
						
			            $ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
				        $account_txn_table_name =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;
				        $this->AccountsModel->update_txn_entries($account_txn_table_name, $acct_opp_bal, $acct_opp_bal_dr_cr);
				    
			         
			         
			         
    			         $log = [
    				            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
    				            'log_date' => date('Y-m-d'),
    				            'log_time' => date('H:i:s'),
    				            'log_action_tags' => 'update',
    				            'log_field_id' => $account_id,
    				            'log_field_name' => $account_name,
    				            'log_field_type' => 'accounts',
    				        ];
    				    $this->LogModel->add_log($log);
    				    
    				    
    				    $file  =     WRITEPATH.'comp'.$this->company_id.'/acc.json';
        			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
        			        file_put_contents($file, ""); 
        		            $accounts_list  = $this->AccountsModel->company_all_accounts();
        		            $comp_folder    = 'comp'.$this->company_id;
        		            $f = fopen($file, 'a');
                            fwrite($f,$accounts_list);
        			     }
			     
			     
    				    
    					 // return redirect()->to($this->base_url.'accounts/list');
						return json_encode(['status' => true, 'message' => 'Data Updated']);    				
        			}
				 			 
		    	}					 									
		}	 
		   
		
		if(!$account_info){
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$get_opn_balance_info = $this->AccountsModel->acc_opn_balance_info($account_id);
		if(!$get_opn_balance_info){
		  $sel_acc_op_bal = '0';
		  $sel_acc_op_type = '';
		  
		  $sel_acc_py_bal = '0';
		  $sel_acc_py_type = '';
		  
	     }
	   else{
		$sel_acc_op_bal  = abs($get_opn_balance_info['acc_op_bal']);
		$sel_acc_op_type = ($get_opn_balance_info['acc_op_bal']<0)?'cr':'dr';
		
		$sel_acc_py_bal  = abs($get_opn_balance_info['acc_py_bal']);
		$sel_acc_py_type = ($get_opn_balance_info['acc_py_bal']<0)?'cr':'dr';		
	    } 
		
        $data['sel_acc_op_bal']        = $sel_acc_op_bal;
		$data['sel_acc_op_type']       = $sel_acc_op_type;
        $data['sel_acc_py_bal']        = $sel_acc_py_bal;
        $data['sel_acc_py_type']       = $sel_acc_py_type;
		$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;	
        $data['group_main_dropdown']   = $this->AccountsModel->group_main_dropdown();
        $data['group_primary_dropdown']   = $this->AccountsModel->group_primary_dropdown();		
		$data['account_info']          = $this->AccountsModel->account_info($account_id);
        $data['CurrencyDropdown']      = $this->AccountsModel->currency_dropdown();		
        $data['CountryDropdown']       = $this->CommonModel->CountryDropdown();
		$data['StatesDropdown']        = $this->CommonModel->StatesDropdown();
		$data['constitution']          = CompanyConstitutions();
		$data['DealerTypeDropdown']    = DealerTypes();
		$data['itc_eligibility']       = AccountItcEllig();
		$data['rcm_nature']            = AccountRcmNature();
		$data['base_url']              = $this->base_url;
		$data['GSTTaxCategory']        = GSTTaxCategory();
		$data['GstTypes']              = GstTypes();
		$data['account_id']            = $account_id;		
		$data['enc_string']            = $this->enc_string;
	    return view($this->folder_path.'accounts/edit',$data);		
    }
	
	public function remove_accounts($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'accounts'); 
		 
		 $default_groups = [];//[1,2,3,4,5];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $account_id){
		     
		     $stat = true;
		     $name = $this->AccountsModel->get_account_name($account_id);

		     if($name != ''){
		     	if (in_array($account_id, $default_groups)){ 
		         $stat = false;
		         array_push($errors, 'Failed! Account "'.$name.'" belongs to Default Accounts');
			     }
			     if ($this->AccountsModel->check_account_with_voucher($account_id)){
			         $stat = false;
			         array_push($errors, 'Failed! Account "'.$name.'" has one or more associated Vouchers');
			     }
			     
			     if($stat)
			     {
			         $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'delete',
			            'log_field_id' => $account_id,
			            'log_field_name' => $name,
			            'log_field_type' => 'account group',
			        ];
		            $this->LogModel->add_log($log);
		            
		            $this->AccountsModel->remove_single_accounts($account_id);
			     }
		     }  
		 }
		 
		 
		 $file  =     WRITEPATH.'comp'.$this->company_id.'/acc.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $accounts_list  = $this->AccountsModel->company_all_accounts();
		            $comp_folder    = 'comp'.$this->company_id;
		            $f = fopen($file, 'a');
                    fwrite($f,$accounts_list);
			     }
			     
			     
		 if(count($errors)){
		         // $this->session->setFlashdata('error_array_message', $errors);
		         return json_encode(['status' => false, 'message' => 'Not Deleted', 'errors' => $errors]);
		  }
		 
		 
		 // return redirect()->to($this->base_url.'accounts/list');
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
		  	
	  }
	
	public function add_group()
    {
		  if($this->request->getMethod() == 'post'){	
		 		$rules = [				
					'group_name' => [
						'label'  => 'Group Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Group Name is required',
					   ],
				  ],
					'group_name_alias' => [
						'label'  => 'Alias Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Alias Name is required',
					  ],
				  ],
				  'primary_group' => [
						'label'  => 'Primary',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Primary is required'
					  ],
				  	],
			  ];
				$errors = [];
        if(!$this->validate($rules)){
        		$errors = $this->validator->getErrors();
        }

        $group_name       = $this->request->getVar('group_name'); 
				$group_name_alias = $this->request->getVar('group_name_alias');
				$primary_group    = $this->request->getVar('primary_group');

				if($primary_group=='Y'){
						$rules = [				
							'yes_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}
				else{
						$rules = [				
							'no_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}

				if(!$this->validate($rules)){
						$errors = $this->validator->getErrors();
        }


				if($primary_group=='Y'){
						$acc_grp_parent_id    = $this->request->getVar('yes_group_under');
						$under_acc_grp_id     = 0;
						$under_main_grp_id    = 0;

						if($this->AccountsModel->check_parent_restriction($acc_grp_parent_id, '2PRMACC')){
								$primary_groups = $this->AccountsModel->get_parimary_groups($acc_grp_parent_id);
								if(!empty($primary_groups) && count($primary_groups) > 0)
									$errors['yes_group_under'] = 'Parent Group can not have more than one Primary Groups';
						}
				}
				else{
						$no_group_under    = $this->request->getVar('no_group_under');
						$main_group_info     = $this->AccountsModel->main_group_info($no_group_under);
						if($main_group_info){
								$acc_grp_parent_id   = $main_group_info['acc_grp_parent_id'];
								$under_acc_grp_id  = $main_group_info['acc_grp_id'];

								if($main_group_info['acc_grp_primary']=='Y')
										$under_main_grp_id= $main_group_info['acc_grp_id'];
								else
										$under_main_grp_id= $main_group_info['under_main_grp_id'];
						}
						else{
							$errors['no_group_under'] = 'Something went wrong with Under Group';
						}
						
				}
        if(empty($errors))
        {
        		$insert_data   = [
			        'comp_id'            => $this->company_id,
							'acc_grp_name'       => ucwords(clean($group_name)),
							'acc_grp_alias'      => clean($group_name_alias),
							'acc_grp_primary'    => $primary_group,
							'acc_grp_parent_id'  => $acc_grp_parent_id,
							'under_acc_grp_id'   => $under_acc_grp_id,
							'under_main_grp_id'  => $under_main_grp_id,
				  	];
				  	$exists = $this->AccountsModel->add_group($insert_data);		
						if($exists==0){
								$this->message_output->set_error('Group name already exists.');				 
						}
						else{
								$log = [
										'uuid_aicountly' => $this->session->get('uuid_aicountly'),
										'log_date' => date('Y-m-d'),
										'log_time' => date('H:i:s'),
										'log_action_tags' => 'add',
										'log_field_id' => $exists,
										'log_field_name' => $group_name,
										'log_field_type' => 'account group',
								];
								$this->LogModel->add_log($log);

								return redirect()->to($this->base_url.'accounts/list_group');
			    	}
        }
        else{
        	$error = '<ul>';
        	foreach ($errors as $key => $value) {
        		$error .= '<li>'.$value.'</li>';
        	}
        	$error .= '</ul>';
        		$this->message_output->set_error($error);
        }
							 									
			}			
				 
			$data['message_output']   = $this->message_output;
			$data['session']          = $this->session;
			$data['folder_path']      = $this->folder_path;	
			$data['group_primary_dropdown']   = $this->AccountsModel->group_primary_dropdown();
			$data['base_url']         = $this->base_url;
			$data['group_main']       = $this->AccountsModel->group_main_dropdown();
			$data['user_groups_dropdown']       = $this->AccountsModel->group_main_dropdown();
			return view($this->folder_path.'accounts/add_group',$data);		
    }
	
	public function remove_groups($ids){
		if(!$ids)
			return redirect()->to($this->base_url.'accounts/list_group'); 
		 
		 $default_groups = [];
		 $errors = [];
		 $ids2 = explode(",",$ids);
		 foreach($ids2 as $account_id){
		     
		     $stat = true;
		     if (in_array($account_id, $default_groups)){ //check if group belongs to defaults
		         $stat = false;
		         array_push($errors, 'Failed! Account Group ID = '.$account_id.' belongs to Default Group');
		      }
		      if($this->AccountsModel->check_group_restriction($account_id, 'DELREST')){
							$stat = false;
		         	$account_name = $this->AccountsModel->get_account_group_name($account_id);
		         	array_push($errors, 'Failed! Group "'.$account_name.'" has been restricted to delete');
					}
					if($this->AccountsModel->sub_group_info($account_id)){
							$stat = false;
		         	$account_name = $this->AccountsModel->get_account_group_name($account_id);
		         	array_push($errors, 'Failed! Group "'.$account_name.'" has one or more associated sub groups');
					}
		     if ($this->AccountsModel->check_account_with_group($account_id)){ //check if account exist with that group
		         $stat = false;
		         $account_name = $this->AccountsModel->get_account_group_name($account_id);
		         array_push($errors, 'Failed! Account Group "'.$account_name.'" has one or more associated accounts');
		     }
		     if ($this->AccountsModel->check_bill_sundry_with_group($account_id)){ //check if account exist with that group
		         $stat = false;
		         $account_name = $this->AccountsModel->get_account_group_name($account_id);
		         array_push($errors, 'Failed! Account Group "'.$account_name.'" has one or more associated bill sundry accounts');
		     }
		     
		     if($stat)
		     {
		         $log = [
		            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
		            'log_date' => date('Y-m-d'),
		            'log_time' => date('H:i:s'),
		            'log_action_tags' => 'delete',
		            'log_field_id' => $account_id,
		            'log_field_name' => $this->AccountsModel->get_account_group_name($account_id),
		            'log_field_type' => 'account group',
		        ];
	            $this->LogModel->add_log($log);
	            
	            $this->AccountsModel->remove_single_groups($account_id);
		     }
		 }
		 if(count($errors)){
		         $this->session->setFlashdata('error_array_message', $errors);
		  }
		 // return redirect()->to($this->base_url.'accounts/list_group');
		  return json_encode(['status' => true, 'message' => 'Deleted', 'reload' => 1]);
	  }
	  
	public function modify_group($group_id)
    {
		if(!$group_id)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();	
		  
		if($this->request->getMethod() == 'post' && $this->request->isAjax()){	
		 		$rules = [				
					'group_name' => [
						'label'  => 'Group Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Group Name is required',
					   ],
				  ],
					'group_name_alias' => [
						'label'  => 'Alias Name',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Alias Name is required',
					  ],
				  ],
				  'primary_group' => [
						'label'  => 'Primary',
						'rules'  => 'required',
						'errors' => [
							'required' => 'Primary is required'
					  ],
				  	],
			  ];
				$errors = [];
	        if(!$this->validate($rules)){
	        		// $errors = $this->validator->getErrors();
        		$errors = $this->validator->getErrors();
        		return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }

	        $group_name       = $this->request->getVar('group_name'); 
				$group_name_alias = $this->request->getVar('group_name_alias');
				$primary_group    = $this->request->getVar('primary_group');

				if($primary_group=='Y'){
						$rules = [				
							'yes_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}
				else{
						$rules = [				
							'no_group_under' => [
								'label'  => 'Under',
								'rules'  => 'required',
								'errors' => [
									'required' => 'Under is required',
							   ],
						  ],
						];
				}

				if(!$this->validate($rules)){
						$errors = $this->validator->getErrors();
	        }

        	$old_group_data = $this->AccountsModel->main_group_info($group_id);


				if($primary_group=='Y'){
						$acc_grp_parent_id    = $this->request->getVar('yes_group_under');
						$under_acc_grp_id     = 0;
						$under_main_grp_id    = 0;

						if($old_group_data['acc_grp_primary'] == $primary_group && $old_group_data['acc_grp_parent_id'] == $acc_grp_parent_id)
						{
							// no problem
						}
						else
						{
							// check sub groups
							$sub_group_data = $this->AccountsModel->sub_group_info($group_id);
							if($sub_group_data){
									$errors['yes_group_under'] = 'One or more Sub Group exists under this Group';
							}
						}

						if($this->AccountsModel->check_parent_restriction($acc_grp_parent_id, '2PRMACC')){
								$primary_groups = $this->AccountsModel->get_parimary_groups($acc_grp_parent_id, $group_id);
								if(!empty($primary_groups) && count($primary_groups) > 0)
									$errors['yes_group_under'] = 'Parent Group can not have more than one Primary Groups';
						}
				}
				else{
						$no_group_under    = $this->request->getVar('no_group_under');
						$main_group_info     = $this->AccountsModel->main_group_info($no_group_under);
						if($main_group_info){
								$acc_grp_parent_id   = $main_group_info['acc_grp_parent_id'];
								$under_acc_grp_id  = $main_group_info['acc_grp_id'];

								if($main_group_info['acc_grp_primary']=='Y')
										$under_main_grp_id= $main_group_info['acc_grp_id'];
								else
										$under_main_grp_id= $main_group_info['under_main_grp_id'];

								if($old_group_data['acc_grp_primary'] == $primary_group && $old_group_data['under_acc_grp_id'] == $under_acc_grp_id)
								{
									// no problem
								}
								else
								{
									// check sub groups
									$sub_group_data = $this->AccountsModel->sub_group_info($group_id);
									if($sub_group_data){
											$errors['no_group_under'] = 'One or more Sub Group exists under this Group';
									}
								}
						}
						else{
							$errors['no_group_under'] = 'Something went wrong with Under Group';
						}
						
				}

				if($group_id == $under_acc_grp_id || $group_id == $under_main_grp_id){
						$errors['group_name'] = 'Something went wrong';
				}
				
	        if(empty($errors))
	        {

						$update_data = [
							'acc_grp_name'      => ucwords(clean($group_name)),
							'acc_grp_alias'    => clean($group_name_alias),
							'acc_grp_primary'  => $primary_group,
							'under_acc_grp_id' => $under_acc_grp_id,
							'acc_grp_parent_id'  => $acc_grp_parent_id,
							'under_main_grp_id'  => $under_main_grp_id,
						];			
           
            	$this->AccountsModel->update_group($update_data,$group_id);	
		        
		        $log = [
			            'uuid_aicountly' => $this->session->get('uuid_aicountly'),
			            'log_date' => date('Y-m-d'),
			            'log_time' => date('H:i:s'),
			            'log_action_tags' => 'update',
			            'log_field_id' => $group_id,
			            'log_field_name' => $group_name,
			            'log_field_type' => 'account group',
			      ];
			    	$this->LogModel->add_log($log);

					// return redirect()->to($this->base_url.'accounts/list_group');
			    	return json_encode(['status' => true, 'message' => 'Data Updated']);
			    	
	        }
	        else{
	        	// $error = '<ul>';
	        	// foreach ($errors as $key => $value) {
	        	// 	$error .= '<li>'.$value.'</li>';
	        	// }
	        	// $error .= '</ul>';
	        	// $this->message_output->set_error($error);
	        	return json_encode(['status' => false, 'message' => 'Validation Error', 'errors' => $errors]);
	        }
		   
		}	 
		   

			$main_group_info = $this->AccountsModel->main_group_info($group_id);
			if(!$main_group_info){
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}

			$data['get_info']         = $main_group_info;
			$data['message_output']   = $this->message_output;
			$data['session']          = $this->session;
			$data['folder_path']      = $this->folder_path;
			$data['group_id']         = $group_id;		
			$data['group_main']       = $this->AccountsModel->group_main_dropdown($group_id);
			$data['group_primary_dropdown']   = $this->AccountsModel->group_primary_dropdown();
			$data['base_url']         = $this->base_url;		
	    return view($this->folder_path.'accounts/edit_group',$data);		
    }


}