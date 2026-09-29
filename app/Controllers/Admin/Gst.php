<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Libraries\externaldb;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\GstrReportModel;

class Gst extends BaseController{

  function __construct(){  
    helper(['form', 'url','text','server_timing']);
	$this->externaldb        = new externaldb();
    $this->CommonModel       = new CommonModel();		
    $this->GstrReportModel   = new GstrReportModel();
	$this->auth_session      = new auth_session();
    $this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
    $this->auth_session->role_restrict('CS');
    $this->base_url       = base_url().'/'.getenv('AdminPath');
    $this->folder_path    = getenv('AdminPath');
    $this->session    	  = \Config\Services::session();    
    $this->company_id     = $this->session->get('ses_company_id');
    $this->aicountly_db   = $this->externaldb->aicountly_db();
    $this->ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
	$this->bo_id = $this->session->get('ses_boid');
	$this->table_lists    = array("4A"=>"B2B REGULAR",
		                       "4B"=>"B2B REVERSE CHARGE",
		                       "5"=>"B2CL (LARGE)",
							   "6A"=>"EXPORTS",
							   "6B"=>"SUPPLIES MADE TO SEZ UNIT",
                               "6C"=>"DEEMED EXPORT (DE)",
							   "7"=>"B2CS",
							   "8"=>"NIL RATED, EXEMPTED AND NON GST",
							   "9A,9C"=>"AMENDED INVOICES",
							   "9B"=>"CREDIT NOTES",
							   "11A(1),11A(2)"=>"TAX LIABILITY (ADVANCES RECEIVED)",
							   "11B(1),11B(2)"=>"ADJUSTMENTS OF ADVANCES",
							   "12"=>"HSN WISE SUMMARY OF OUTWARD SUPPLIES",
							   "13"=>"DOCUMENT ISSUED" ,
							   "14"=>"SUPPLIES MADE THROUGH ECO",
							   "15"=>"SUPPLIES U/S 9(5)",
								"16"=>"UNDEFINED SUPPLIES"
								);
	$this->table_lists_detailed   = array("4A"=>"B2B REGULAR",
		                       "4B"=>"B2B REVERSE CHARGE",
		                       "5"=>"B2CL (LARGE)",
							   "6A"=>"EXPORTS",
							   "6A_EXPWP"=>"EXPWP",
							   "6A_EXPWOP"=>"EXPWOP",		
							   "6B"=>"SUPPLIES MADE TO SEZ UNIT",	
							   "6B_SEZWP"=>"SEZWP",
							   "6B_SEZWOP"=>"SEZWOP",
                               "6C"=>"DEEMED EXPORT (DE)",
							   "7"=>"B2CS",
							   "8"=>"NIL RATED, EXEMPTED AND NON GST",
							   "8_Nil"=>"NIL RATED",
							   "8_Exempted"=>"EXEMPTED",
							   "8_Non-GST"=>"NON GST",							   
							   "9A,9C"=>"AMENDED INVOICES",
							   "9B"=>"CREDIT NOTES REGISTERED",
							   "9B_Registered"=>"REGISTERED",
							   "9B_Unregistered"=>"UNREGISTERED",
							   "11A(1),11A(2)"=>"TAX LIABILITY (ADVANCES RECEIVED)",
							   "11B(1),11B(2)"=>"ADJUSTMENTS OF ADVANCES",
							   "12"=>"HSN WISE SUMMARY OF OUTWARD SUPPLIES",
							   "13"=>"DOCUMENT ISSUED" ,
							   "13_candoc"=>"CANCELLED DOCS" ,
							   "13_netissued"=>"NET ISSUED DOCS" ,
							   "14"=>"SUPPLIES MADE THROUGH ECO",
							   "15"=>"SUPPLIES U/S 9(5)",
							   "16"=>"UNDEFINED SUPPLIES");
	$this->table_lists2ab    = array("3"=>"B2B INVOICES",
		                       "4"=>"B2B LIABLE TO RCM",
		                       "5"=>"IMPORTS / SEZ SUPPLIES",
							   "6"=>"AMENDMENTS",
							   "7A"=>"INTERSTATE SUPPLIES",
                               "7B"=>"INTRA STATE SUPPLIES",
							   "8"=>"ISD CREDIT RECEIVED",
							   "9"=>"TDS / TCS CREDIT RECEIVED",
							   "10"=>"ADVANCE RECEIVED",
							   "11"=>"ITC REVERSALS AND RECLAIM",
							   "12"=>"ADDITIONS / REDUCTIONS FOR MISMATCH / OTHERS",
							   "13"=>"HSN SUMMARY"
							   );
	$this->table_lists_detailed2ab   = array("3"=>"B2B INVOICES",
		                       "4"=>"B2B LIABLE TO RCM",
		                       "4A_B2B"=>"B2B LIABLE u/RCM",
							   "4B_B2BUR"=>"B2BUR LIABLE u/RCM",
							   "4C_SRVC"=>"IMPORT OF SERVICE u/RCM",
							   "5"=>"IMPORTS / SEZ SUPPLIES",		
							   "5A_IMP"=>"IMPORTS",	
							   "5B_RCDSEZ"=>"RECEIVED FROM SEZ",
							   "6"=>"AMENDMENTS",
                               "6A_A"=>"SUPPLIES OTHER THAN IMP-GOODS / SEZ",
							   "6B_B"=>"SUPPLIES DUE TO IMP-GOODS / SEZ",
							   "6C_C"=>"DEBIT NOTES ORIGINAL",
							   "6D_D"=>"DEBIT NOTES AMENDED",
							   "7A"=>"INTERSTATE SUPPLIES",
							   "7A_1"=>"COMPOSITION DEALER",
							   "7A_2"=>"EXEMPT SUPPLIES",
							   "7A_3"=>"NIL RATED SUPPLIES",							   
							   "7A_4"=>"NON GST SUPPLIES",
							   "7B"=>"INTRA STATE SUPPLIES",
							   "7B_1"=>"COMPOSITION DEALER",
							   "7B_2"=>"EXEMPT SUPPLIES",
							   "7B_3"=>"NIL RATED SUPPLIES",
							   "7B_4"=>"NON GST SUPPLIES ",
							   "8"=>"ISD CREDIT RECEIVED",
							   "8A_ISDINV"=>"ISD INVOICE" ,
							   "8B_ISDCRD"=>"ISD CREDIT NOTE" ,
							   "9"=>"TDS / TCS CREDIT RECEIVED" ,
							   "9A_TDS"=>"GST TDS RECEIVED",
							   "9B_TCS"=>"GST TCS RECEIVED",
							   "10"=>"ADVANCE RECEIVED",
							   "11"=>"ITC REVERSALS AND RECLAIM",
							   "12"=>"ADDITIONS / REDUCTIONS FOR MISMATCH / OTHERS",
							   "13"=>"HSN SUMMARY"
							   );						   
    $this->gstsummary_table_lists    = array("otax"=>"OUTPUT TAX",
		                       "otax_cr"=>"OUTPUT TAX (CR. NOTE)",
		                         "total_otax"=>"TOTAL OUTPUT TAX",
								 ""=>"",
							   "itax"=>"INPUT TAX",
							   "itax_dr"=>"INPUT TAX (DR. NOTE)",
                               "total_itax"=>"TOTAL INPUT TAX"							   
							   );
  							   
  }
  
 public function ajax_hsn_summary_list(){
	if($this->request->getMethod() == 'POST' && $this->request->isAjax()){
		$table_key     = $this->request->getVar('tablekey'); 
		$from_date     = $this->request->getVar('from_date'); 
		$to_date       = $this->request->getVar('to_date');		
		$from_date_ymd =  date('Y-m-d', strtotime($from_date));
        $to_date_ymd   =  date('Y-m-d', strtotime($to_date)); 		
		echo $response =  json_encode($this->GstrReportModel->ajax_hsnsummary_list($from_date_ymd,$to_date_ymd,$table_key)); 
	   }
  }
  
 public function hsn_summary_ledger(){
    $from_date   = $_GET['fromdate'] ?? '';
    $to_date     = $_GET['todate']   ?? '';
    $tableno_raw = $_GET['tableno']  ?? '';

    if(! $tableno_raw) {
        throw \CodeIgniter\Exceptions\PageNotFoundException:: forPageNotFound();
    }

    // Strip sequence prefix like "02_"
    $tableno = preg_replace('/^\d+_/', '', $tableno_raw);

    // Parse for display
    $show_table_no = 'NA';
    
    // Debug: check the tableno value
    // Format: hsn_out_ch_NA or hsn_in_ch_99 (ch = chapter mode indicator)
    // Format: hsn_out_hsn_998211 (hsn = exact hsn mode indicator)
    // Format: hsn_out_99_998211 (detailed:  chapter_hsn)
    
    // Pattern 1: hsn_{dir}_ch_{chapter} - chapter mode
    if (preg_match('/^hsn_(out|in)_ch_(.*)$/i', $tableno, $m)) {
        $chapter = strtoupper(trim($m[2]));
        if ($chapter === '' || $chapter === 'NA') {
            $show_table_no = 'CHAPTER NA';
        } else {
            $show_table_no = 'CHAPTER ' . $chapter;
        }
    }
    // Pattern 2: hsn_{dir}_hsn_{code} - exact hsn mode
    elseif (preg_match('/^hsn_(out|in)_hsn_(.+)$/i', $tableno, $m)) {
        $hsn = preg_replace('/\D/', '', $m[2]);
        $show_table_no = 'HSN ' . $hsn;
    }
    // Pattern 3: hsn_{dir}_{chapter}_{hsn} - detailed mode (from detailed view)
    elseif (preg_match('/^hsn_(out|in)_([^_]+)_(.+)$/i', $tableno, $m)) {
        $part2 = strtoupper(trim($m[2]));
        $part3 = strtoupper(trim($m[3]));
        
        // Check if part2 is "ch" or "hsn" (old format indicators)
        if ($part2 === 'CH') {
            // This is actually chapter mode:  hsn_out_ch_XX
            if ($part3 === '' || $part3 === 'NA') {
                $show_table_no = 'CHAPTER NA';
            } else {
                $show_table_no = 'CHAPTER ' . $part3;
            }
        } elseif ($part2 === 'HSN') {
            // This is exact HSN mode: hsn_out_hsn_XXXXXX
            $hsnDigits = preg_replace('/\D/', '', $part3);
            $show_table_no = 'HSN ' . $hsnDigits;
        } elseif ($part2 === 'NA') {
            // NA chapter
            $show_table_no = 'CHAPTER NA';
        } elseif ($part3 === 'NA' || $part3 === '') {
            // Chapter only
            $show_table_no = 'CHAPTER ' . $part2;
        } else {
            // Exact HSN from detailed view:  hsn_out_99_998211
            $hsnDigits = preg_replace('/\D/', '', $part3);
            $show_table_no = 'HSN ' . $hsnDigits;
        }
    }
    else {
        $show_table_no = strtoupper($tableno);
    }

    $from_date = validate_fy_from_date($from_date);
    $to_date   = validate_fy_to_date($to_date);

    $data = [
        'from_date'    => $from_date,
        'show_date'    => date('M, Y', strtotime($from_date)),
        'to_date'      => $to_date,
        'view'         => 1,
        'summary_view' => 1,
        'show_table_no'=> $show_table_no,
        'tableno_key'  => $tableno,
        'tableno'      => $tableno,
        'bo_id'        => $this->bo_id
    ];

    return view($this->folder_path.'gst/gstr1_hsn_ledger', $data);
}
  
 public function outwardsupplies(){
       
       if($this->request->getMethod() == 'POST'){	      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
        }        
        $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;        
        return view($this->folder_path.'gst/outwardsupplies',$data);  
    }
	
 public function index(){
       
       if($this->request->getMethod() == 'POST'){	      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
          } 
       
        $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;        
        return view($this->folder_path.'gst/gstsummary',$data);  
    }
	
 public function inwrdspl_2a2b(){
       
       if($this->request->getMethod() == 'post'){	      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
          } 
       
        $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;        
        return view($this->folder_path.'gst/inwrdspl_2a2b',$data);  
    }
 public function inwrdspl_3b(){
       
       if($this->request->getMethod() == 'post'){	      
            $type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
          } 
       
        $data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['base_url']              = $this->base_url;        
        return view($this->folder_path.'gst/inwrdspl_3b',$data);  
    }
	
 public function gstsummary_report_detail() {        
    $view          = (int)($this->request->getGet('view') ?? 0);
    $summary_view  = (int)($this->request->getGet('summary_view') ?? 0);
    $from_date     = $this->request->getGet('fromdate') ?? '';
    $to_date       = $this->request->getGet('todate') ?? '';
    $fltrtypes     = $_GET['fltrtype'] ?? '';
   
    if ($fltrtypes == '') {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    
    $fltrtypes_info = unobfuscate_link($fltrtypes);
    if (! isset($fltrtypes_info[1])) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    
    if ($from_date == '') {
        $from_date = validate_fy_from_date($from_date);
    }
    if ($to_date == '') {
        $to_date = validate_fy_to_date($to_date);
    }
    
    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd   = date('Y-m-d', strtotime($to_date));
    $table_lists   = $this->gstsummary_table_lists;
    $response      = array();
    
    // Tax Summary - Detailed View
    if ($summary_view == 0 && $view == 1) {
        $response = json_encode($this->GstrReportModel->load_gstsummary_detailed($from_date_ymd, $to_date_ymd));
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
        $response = json_encode(array(
            "totalRecords" => count($response_final),
            "curPage"      => "1",
            "data"         => $response_final
        ));
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
        
        $response = json_encode($master_array);
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
        
        $response = json_encode($master_array);
    }
    
   
	$data = [
        'from_date'    => $from_date,
        'show_date'    => date('M, Y', strtotime($from_date)),
        'to_date'      => $to_date,
        'view'         => $view,
        'summary_view' => $summary_view,
        'response'     => $response,
        'account_id'   => 0,
        'fltrtype'     => $fltrtypes
    ];
    
    $data['bo_id'] = $this->bo_id;
    
    if ($view == 1) {
        return view($this->folder_path . 'gst/gstsummary_report_detailed', $data);
    } else {
        return view($this->folder_path . 'gst/gstsummary_report', $data);
    }
}
   
 public function inwrdspl_2a2b_detail(){        
		$view           = !empty($_GET['view']) ? $_GET['view'] : 0;
		$summary_view   = !empty($_GET['summary_view']) ? $_GET['summary_view'] : 0;
		$from_date = $_GET['fromdate'] ?? '';
		$to_date   = $_GET['todate'] ?? '';
		$fltrtypes = $_GET['fltrtype'] ?? '';
		$gstrradio = $_GET['gstrradio'] ?? '2';
	    if($fltrtypes=='')
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
	    $fltrtypes_info = unobfuscate_link($fltrtypes);
       if(!isset($fltrtypes_info[1]))
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		$from_date     = validate_fy_from_date($from_date);
        $to_date       = validate_fy_to_date($to_date);		
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
		if($view == 1){
		$table_lists   = $this->table_lists_detailed2ab;	
		}else{
		$table_lists   = $this->table_lists2ab;
		}
	    $response      = array();	 
		
       if($view == 1){
		 $response = json_decode($this->GstrReportModel->load_gstr2ab_detailed($from_date_ymd,$to_date_ymd,$table_lists),true);
		 $response_final = array();
		 if(isset($response['data'])){
		 foreach($response['data'] as $row){
			  $exploded = explode("_",$row['tableno']);
			   if(isset($exploded[1])){
				  $tname=  "   » ".$row['table_name'];
				  $tableno ="";
			      }
				  else{
				  $tname =  "<strong>".$row['table_name']."</strong>";
				  $tableno =$row['tableno'];		
				  }
				  
			$response_final[]=array("from_date"=>$from_date_ymd,"to_date"=>$to_date_ymd,"htableno"=>$row['tableno'],"tableno"=>$tableno,"table_name"=>$tname,"total_records"=>$row['total_records'],
			                        "invoice_value"=>$row['invoice_value'],"taxable_value"=>$row['taxable_value'],"igst"=>$row['igst'],"cgst"=>$row['cgst'],
									"sgst"=>$row['sgst'],"cess"=>$row['cess'],"total_tax"=>$row['total_tax']
									);						
			 
		      }
		 }
		 
		 $response=json_encode(array("totalRecords"=>count($response_final),"curPage"=>"1","data"=>$response_final));
		 }
	  else
		$response = $this->GstrReportModel->load_gstr2ab_condensed($from_date_ymd,$to_date_ymd,$table_lists);
      
	  
	    $data = [
			'from_date'			   => $from_date,
			'show_date'            => date('M, Y',strtotime($from_date)),
			'to_date'			   => $to_date,
			'view'			       => $view,
			'response'             => $response,
			'account_id'           => 0,
			'fltrtype'             => $fltrtypes,
			'gstrradio'            => $gstrradio
			
	         ];		
	  $data['bo_id'] = $this->session->get('ses_boid');   
      if($view == 1)	  
      return view($this->folder_path.'gst/inwrdspl_2a2b_report_detailed',$data); 
	else 
     return view($this->folder_path.'gst/inwrdspl_2a2b_report',$data); 
   }
   
 public function inwrdspl_3b_detail(){        
		$view           = !empty($_GET['view']) ? $_GET['view'] : 0;
		$summary_view   = !empty($_GET['summary_view']) ? $_GET['summary_view'] : 0;
		$from_date = $_GET['fromdate'] ?? '';
		$to_date   = $_GET['todate'] ?? '';
		$fltrtypes = $_GET['fltrtype'] ?? '';
		$gstrradio = $_GET['gstrradio'] ?? '2';
	    if($fltrtypes=='')
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
	    $fltrtypes_info = unobfuscate_link($fltrtypes);
       if(!isset($fltrtypes_info[1]))
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		$from_date     = validate_fy_from_date($from_date);
        $to_date       = validate_fy_to_date($to_date);		
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
		if($view == 1){
		$table_lists   = $this->table_lists_detailed2ab;	
		}else{
		$table_lists   = $this->table_lists2ab;
		}
	    $response      = array();	 
		
       if($view == 1){
		 $response = json_decode($this->GstrReportModel->load_gstr2ab_detailed($from_date_ymd,$to_date_ymd,$table_lists),true);
		 $response_final = array();
		 if(isset($response['data'])){
		 foreach($response['data'] as $row){
			  $exploded = explode("_",$row['tableno']);
			   if(isset($exploded[1])){
				  $tname=  "   » ".$row['table_name'];
				  $tableno ="";
			      }
				  else{
				  $tname =  "<strong>".$row['table_name']."</strong>";
				  $tableno =$row['tableno'];		
				  }
				  
			$response_final[]=array("from_date"=>$from_date_ymd,"to_date"=>$to_date_ymd,"htableno"=>$row['tableno'],"tableno"=>$tableno,"table_name"=>$tname,"total_records"=>$row['total_records'],
			                        "invoice_value"=>$row['invoice_value'],"taxable_value"=>$row['taxable_value'],"igst"=>$row['igst'],"cgst"=>$row['cgst'],
									"sgst"=>$row['sgst'],"cess"=>$row['cess'],"total_tax"=>$row['total_tax']
									);						
			 
		      }
		 }
		 
		 $response=json_encode(array("totalRecords"=>count($response_final),"curPage"=>"1","data"=>$response_final));
		 }
	  else
		$response = $this->GstrReportModel->load_gstr2ab_condensed($from_date_ymd,$to_date_ymd,$table_lists);
      
	  
	    $data = [
			'from_date'			   => $from_date,
			'show_date'            => date('M, Y',strtotime($from_date)),
			'to_date'			   => $to_date,
			'view'			       => $view,
			'response'             => $response,
			'account_id'           => 0,
			'fltrtype'             => $fltrtypes,
			'gstrradio'            => $gstrradio
			
	         ];		
	  $data['bo_id'] = $this->session->get('ses_boid');   
      if($view == 1)	  
      return view($this->folder_path.'gst/inwrdspl_2a2b_report_detailed',$data); 
	else 
     return view($this->folder_path.'gst/inwrdspl_2a2b_report',$data); 
   }	
	
	 
 public function ajax_gstr_transactions(){
    $pq_curPage      = (int)($_POST["pq_curpage"] ?? 1);
    $limit           = (int)($_POST["pq_rpp"] ?? 10);
    $from_date       = date('Y-m-d', strtotime($this->request->getVar('from_date')));
    $to_date         = date('Y-m-d', strtotime($this->request->getVar('to_date')));
    $tableno_key     = $this->request->getVar('tableno_key');
    $tableno         = $this->request->getVar('tableno');
    
    $state_data      = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
    $states_dropdown = array();
    foreach($state_data as $strow){
        $state_code = sprintf('%02d', $strow['state_code']);    
        $states_dropdown[$state_code] = $strow['state_name'];
    }
    
    echo json_encode($this->GstrReportModel->load_gstr_listings($from_date, $to_date, $tableno, $states_dropdown, $limit, $pq_curPage));
}
public function ajax_gstr1_transactions(){
    $pq_curPage      = (int)($_POST["pq_curpage"] ?? 1);
    $limit           = (int)($_POST["pq_rpp"] ?? 10);
    $from_date       = date('Y-m-d', strtotime($this->request->getVar('from_date')));
    $to_date         = date('Y-m-d', strtotime($this->request->getVar('to_date')));
    $tableno_key     = $this->request->getVar('tableno_key');
    $tableno         = $this->request->getVar('tableno');
    
    $state_data      = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
    $states_dropdown = array();
    foreach($state_data as $strow){
        $state_code = sprintf('%02d', $strow['state_code']);    
        $states_dropdown[$state_code] = $strow['state_name'];
    }
    
    echo json_encode($this->GstrReportModel->load_gstr1_listings($from_date, $to_date, $tableno, $states_dropdown, $limit, $pq_curPage));
}

 
 protected function getTableDisplayNameGSTR1(string $tableno): string
{
    if (isset($this->table_lists_detailed[$tableno])) {
        return $this->table_lists_detailed[$tableno];
    }
    if (isset($this->table_lists[$tableno])) {
        return $this->table_lists[$tableno];
    }
    return '';
}
 public function gstr1_ledger(){    
    $from_date = $_GET['fromdate'] ?? '';
    $to_date   = $_GET['todate'] ??  '';
    $tableno   = $_GET['tableno'] ??  '';
    
    if(! $tableno)
        throw \CodeIgniter\Exceptions\PageNotFoundException:: forPageNotFound();
    
    // Get display name for the table
    $show_table_no = $this->getTableDisplayNameGSTR1($tableno);
    
    // Parse tableno for key
    $tableno_key = $tableno;
    
    $from_date = validate_fy_from_date($from_date);
    $to_date   = validate_fy_to_date($to_date);
    
    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
    
    $data = [
        'from_date'     => $from_date,
        'show_date'     => date('M, Y', strtotime($from_date)) . ' - ' . date('M, Y', strtotime($to_date)),
        'to_date'       => $to_date,
        'view'          => 1,
        'show_table_no' => $show_table_no,
        'tableno_key'   => $tableno_key,
        'tableno'       => $tableno,
        'bo_id'         => $this->bo_id  
    ];      
  
    return view($this->folder_path . 'gst/gstr1_ledger', $data);     
}

public function ledger(){    
    $from_date = $_GET['fromdate'] ?? '';
    $to_date   = $_GET['todate'] ??  '';
    $tableno   = $_GET['tableno'] ??  '';
    
    if(! $tableno)
        throw \CodeIgniter\Exceptions\PageNotFoundException:: forPageNotFound();
    
    // Get display name for the table
    $show_table_no = $this->getTableDisplayName($tableno);
    
    // Parse tableno for key
    $tableno_key = $tableno;
    
    $from_date = validate_fy_from_date($from_date);
    $to_date   = validate_fy_to_date($to_date);
    
    $from_date_ymd = date('Y-m-d', strtotime($from_date));
    $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
    
    $data = [
        'from_date'     => $from_date,
        'show_date'     => date('M, Y', strtotime($from_date)) . ' - ' . date('M, Y', strtotime($to_date)),
        'to_date'       => $to_date,
        'view'          => 1,
        'show_table_no' => $show_table_no,
        'tableno_key'   => $tableno_key,
        'tableno'       => $tableno,
        'bo_id'         => $this->bo_id  
    ];      
  
    return view($this->folder_path . 'gst/gstr_ledger', $data);     
}

/**
 * Get display name for table number
 */
private function getTableDisplayName(string $tableno): string
{
    $tab = strtolower(trim($tableno));
    
    // Check if it's a rate-specific row
    if (preg_match('/^(otax|otax_cr|itax|itax_dr)_rate_(\d+)_([0-9.]+)_([0-9.]+)$/i', $tab, $matches)) {
        $category = strtolower($matches[1]);
        $gstType = (int)$matches[2];
        $gstRate = (float)$matches[3];
        $cessRate = (float)$matches[4];
        
        $categoryName = match($category) {
            'otax' => 'OUTPUT TAX',
            'otax_cr' => 'OUTPUT TAX (CR.  NOTE)',
            'itax' => 'INPUT TAX',
            'itax_dr' => 'INPUT TAX (DR. NOTE)',
            default => 'GST'
        };
        
        // Build rate label
        $rate = rtrim(rtrim(number_format($gstRate, 2), '0'), '.');
        $cess = rtrim(rtrim(number_format($cessRate, 2), '0'), '.');
        
        if ($gstRate == 0 && $gstType == 1) {
            $rateLabel = 'NIL RATED SUPPLY';
        } elseif ($gstType == 2) {
            $rateLabel = "GST @ {$rate}% (Composition)";
            if ($cessRate > 0) {
                $rateLabel .= " + Cess @ {$cess}%";
            }
        } else {
            $rateLabel = "GST @ {$rate}%";
            if ($cessRate > 0) {
                $rateLabel .= " + Cess @ {$cess}%";
            }
        }
        
        return "{$categoryName} - {$rateLabel}";
    }
    
    // Header rows
    if (strpos($tab, 'otax_cr') === 0) {
        return 'OUTPUT TAX (CR. NOTE)';
    } elseif (strpos($tab, 'otax') === 0) {
        return 'OUTPUT TAX';
    } elseif (strpos($tab, 'itax_dr') === 0) {
        return 'INPUT TAX (DR. NOTE)';
    } elseif (strpos($tab, 'itax') === 0) {
        return 'INPUT TAX';
    } elseif ($tab === 'total_otax') {
        return 'TOTAL OUTPUT TAX';
    } elseif ($tab === 'total_itax') {
        return 'TOTAL INPUT TAX';
    }
    
    return 'GST Ledger';
}
 
 public function ajax_gstr2ab_transactions(){
	 $pq_curPage      = (int)$_POST["pq_curpage"];
	 $limit           = (int)$_POST["pq_rpp"];
	 $from_date       = date('Y-m-d',strtotime($this->request->getVar('from_date')));
	 $to_date         = date('Y-m-d',strtotime($this->request->getVar('to_date')));
     $tableno_key     = $this->request->getVar('tableno_key');
	 $tableno         = $this->request->getVar('tableno');
	 $state_data      = $this->aicountly_db->table('aicountly_stateslist_univdb')->get()->getResultArray();
     $states_dropdown = array();
	 foreach($state_data as $strow){
	  $state_code = sprintf('%02d',$strow['state_code']);	
	  $states_dropdown[$state_code]=$strow['state_name'];
	 }
	 echo json_encode($this->GstrReportModel->load_gstr2ab_listings($from_date,$to_date,$tableno,$states_dropdown,$limit,$pq_curPage));
 }
 public function gstr2abledger(){        
		
		$from_date = $_GET['fromdate'] ?? '';
		$to_date   = $_GET['todate'] ?? '';
		$tableno   = $_GET['tableno'] ?? '';
		if(!$tableno)
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		$table_lists = $this->table_lists_detailed;
        if(isset($table_lists[$tableno]))
         $show_table_no = $table_lists[$tableno];
         else
         $show_table_no ='';
	 
	    $expl_table = explode("_",$tableno);
		if(isset($expl_table[1]))
			$tableno_key = $expl_table[1];
		 else
			$tableno_key = $expl_table[0]; 
		
   
		$from_date = validate_fy_from_date($from_date);
        $to_date = validate_fy_to_date($to_date);
		
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
		
	    $data = [
			'from_date'		=> $from_date,
			'show_date'     => date('M, Y',strtotime($from_date)),
			'to_date'		=> $to_date,
			'view'		    => 1,
			'show_table_no' => $show_table_no,
			'tableno_key'   => $tableno_key,
			'tableno'       => $tableno,
			'bo_id'         => $this->session->get('ses_boid')  
	       ];		
	  
     return view($this->folder_path.'gst/gstr2ab_ledger',$data);     
   }
   
 public function gstr1_report_detail(){   
		$view      = !empty($_GET['view']) ? $_GET['view'] : 0;
		$from_date = $_GET['fromdate'] ?? '';
		$to_date   = $_GET['todate']   ?? '';
		$fltrtypes = $_GET['fltrtype'] ?? '';
	    if($fltrtypes=='')
		 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
	     $fltrtypes_info = unobfuscate_link($fltrtypes);
        if(!isset($fltrtypes_info[1]))
		throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		
		$from_date = validate_fy_from_date($from_date);
        $to_date   = validate_fy_to_date($to_date);
		
		$from_date_ymd = date('Y-m-d', strtotime($from_date));
        $to_date_ymd   = date('Y-m-d', strtotime($to_date)); 
		if($view == 1){
		$table_lists   =$this->table_lists_detailed;	
		}else{
		$table_lists   =$this->table_lists;
		}
	    $response      = array();	 
	
       if($view == 1){
		 $response = $this->GstrReportModel->load_gstr1_detailed($from_date_ymd,$to_date_ymd,$table_lists);
		 $response_final = array();
		 if(isset($response['data'])){
		 foreach($response['data'] as $row){
			  $exploded = explode("_",$row['tableno']);
			   if(isset($exploded[1])){
				  $tname=  "   » ".$row['table_name'];
				  $tableno ="";
			      }
				  else{
				  $tname =  "<strong>".$row['table_name']."</strong>";
				  $tableno =$row['tableno'];		
				  }
				  
			$response_final[]=array("from_date"=>$from_date_ymd,"to_date"=>$to_date_ymd,"htableno"=>$row['tableno'],"tableno"=>$tableno,"table_name"=>$tname,"total_records"=>$row['total_records'],
			                        "invoice_value"=>$row['invoice_value'],"taxable_value"=>$row['taxable_value'],"igst"=>$row['igst'],"cgst"=>$row['cgst'],
									"sgst"=>$row['sgst'],"cess"=>$row['cess'],"total_tax"=>$row['total_tax'],"sm_invoice_value"=>$row['sm_invoice_value'],"sm_taxable_value"=>$row['sm_taxable_value'],
									"sm_igst"=>$row['sm_igst'],"sm_cgst"=>$row['sm_cgst'],"sm_sgst"=>$row['sm_sgst'],"sm_cess"=>$row['sm_cess'],"sm_total_tax"=>$row['sm_total_tax']								
									
									);						
			 
		      }
		 }
		 
		 $response=["totalRecords"=>count($response_final),"curPage"=>"1","data"=>$response_final];
		 }
	   else
		 $response = $this->GstrReportModel->load_gstr1_condensed($from_date_ymd, $to_date_ymd, $table_lists);
	  
	    $data = [
			'from_date'			   => $from_date,
			'show_date'            => date('M, Y',strtotime($from_date)),
			'to_date'			   => $to_date,
			'view'			       => $view,
			'response'             => json_encode($response),
			'account_id'           => 0,
			'fltrtype'             => $fltrtypes,
			
	         ];		
	  $data['bo_id'] = $this->session->get('ses_boid');  
      if($view == 1)	  
      return view($this->folder_path.'gst/gstr1_detailed_report',$data); 
	else 
     return view($this->folder_path.'gst/gstr1_condensed_report',$data);     
   }
}