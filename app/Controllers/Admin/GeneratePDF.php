<?php
namespace App\Controllers\Admin;
use App\Models\Admin\TransactionModel;
use App\Models\Admin\ReportsModel;
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
        $this->ReportsModel  = new ReportsModel();
		$this->TransactionModel  = new TransactionModel();			
		$this->auth_session  = new auth_session();		
		$this->CommonModel   =  new CommonModel();				
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
		$this->bsdnatures    = ['33'=>'CGST','34'=>'SGST','35'=>'IGST','36'=>'CESS (GST)','195'=>'UT Tax','38'=>'TDS (IT)','37'=>'TCS (IT)','40'=>'TDS (GST)','39'=>'TCS (GST)','199'=>'Other Taxes','45'=>'Round off (+)','192'=>'Round off (-)','193'=>'Discount (-)','196'=>'Loading & Unloading','198'=>'Packing','197'=>'Freight','194'=>'Others'];
        $this->table_lists_detailed = array("4A"=>"B2B REGULAR",
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
							   "9B"=>"CREDIT  NOTES REGISTERED",//"9B"=>"CREDIT / DEBIT NOTES REGISTERED",
							   "9B_Registered"=>"REGISTERED",
							   "9B_Unregistered"=>"UNREGISTERED",
							   "11A(1),11A(2)"=>"TAX LIABILITY (ADVANCES RECEIVED)",
							   "11B(1),11B(2)"=>"ADJUSTMENTS OF ADVANCES",
							   "12"=>"HSN WISE SUMMARY OF OUTWARD SUPPLIES",
							   "13"=>"DOCUMENT ISSUED" ,
							   "13_candoc"=>"CANCELLED DOCS" ,
							   "13_netissued"=>"NET ISSUED DOCS" ,
							   "14"=>"SUPPLIES MADE THROUGH ECO",
							   "15"=>"SUPPLIES U/S 9(5)");
	}

	/* public function receipt_pdf($voucher_txn_id)
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
   */
   
   public function sales($voucher_txn_id)
   {
	  $bo_id              = $this->session->get('ses_boid');	
   	  $company_info       = $this->CommonModel->get_company_info($this->company_id);
      $comp_ro_address    = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->CommonModel->get_comp_taxt_info($this->company_id,$bo_id);
	  
	  $data['company_name'] = $company_info['comp_name'];
	
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
	  if(isset($get_comp_taxt_info['comp_gstin']))
          $gst = $get_comp_taxt_info['comp_gstin'];
	  else
		  $gst ='';
	  
      $data['gst'] = $gst;
      $data['cin'] = $company_info['cin'];
      if(isset($comp_ro_address['comp_addr1']))
       $company_adrs1 = $comp_ro_address['comp_addr1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['comp_addr2']))
       $company_adrs2 = $comp_ro_address['comp_addr2'];
      else
		$company_adrs2 = ''; 
	    $company_city = $comp_ro_address['comp_addr2'];
	    $company_pin = $comp_ro_address['comp_addr2'];
	  
       $data['company_address']  = $company_adrs1;
	   $data['company_address2']  = $company_adrs2;
	   $data['company_city']  = $company_city;
	   $data['company_pin']  = $company_pin;

      $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));

      $get_voucher_series     = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

       $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';

      return view($this->folder_path.'vouchers/sales_pdf',$data);
   }
   
   public function sales_multi_labels($voucher_txn_ids){
	   $labels_array = array(1=>'For Recipient',2=>'Transporter Copy',3=>'Dealer Copy',4=>'Duplicate Copy');
	   $show_label='';
	   
	   $zip = new \ZipArchive();
	    $folder_path = WRITEPATH.'uploads/db_backup_temp';	  
	    $_name = 'print_labels_'.rand(1000,9999).'_'.time();
	    $zip_file_path =  $folder_path.'/'.$_name.'.zip';
        /* if($zip->open($zip_file_path, \ZipArchive::CREATE) == TRUE) {
			$content = 'multi printing vouchers';
			$zip->addFromString('notes.txt', $content);
			$zip->close();
	    }	 */
	   $zipfile = $_name;
	   $tcp=1;
	   if(isset($_GET['tcp'])){
		  $total_copies = $_GET['tcp']; 
		  if($total_copies==1)
			 $voucher_txn_ids =$voucher_txn_ids.',';
		  if($total_copies==2)
			 $voucher_txn_ids =$voucher_txn_ids.','.$voucher_txn_ids; 
		 if($total_copies==3)
			 $voucher_txn_ids =$voucher_txn_ids.','.$voucher_txn_ids.','.$voucher_txn_ids;
		 if($total_copies==4)
			 $voucher_txn_ids =$voucher_txn_ids.','.$voucher_txn_ids.','.$voucher_txn_ids.','.$voucher_txn_ids;
	  
		

	     }
	   
	     //,$zipfile 
		 $get_business_id   = $this->CommonModel->get_business_id();
		 $pdf=[];
		 $headers=[];
		 $footers=[];
	     $voucher_txn_ids       = rtrim($voucher_txn_ids,",");
		 $multi_voucher_txn_ids = array();
		 if($voucher_txn_ids){
		   $multi_voucher_txn_ids = explode(",",$voucher_txn_ids);
		 }
	    $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	    $tcpdf->SetTitle('Sale');
		$tcpdf->SetHeaderMargin(1,0,0,0);
		$tcpdf->SetFooterMargin(79,0,0,0);
		$tcpdf->setPrintHeader(false);
		$tcpdf->setPrintFooter(false);
		$tcpdf->setListIndentWidth(3);
		$tcpdf->SetAutoPageBreak(TRUE, 79);
		$tcpdf->SetDisplayMode(100);
		$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$tcpdf->SetFont('segoe_ui', '', '8.5pt');
		$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
		$tcpdf->SetFont('arial', '', '','',false);
		$tcpdf->SetFont('verdana', '', '','',false);
		$tcpdf->SetFont('times_new_roman', '', '','',false);
		$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
		 // use the font
		$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
		$labelcounters=1;
		 if($multi_voucher_txn_ids){
			   foreach($multi_voucher_txn_ids as $voucher_txn_id){
					    if(isset($_GET['prntlbls']) && $_GET['prntlbls']==1){
						   //1 = Original Copy
						   //2 = Transporter Copy
						   //3 = Dealer Copy
						   //4 = Duplicate Copy 	
						   $show_label=$labels_array[$labelcounters];
					   }
				        $voucher_info      = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);     
					   								
						$voucher_series_id = $voucher_info['comp_vch_series_id']; 						
						if($voucher_info['vch_subtype_id']==8){ 
						   $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(272);
						  if($defaultPrint_template==270){
						  $config_codes_list = array(8=>274,9=>276,6=>173,34=>287,24=>175,25=>303,108=>177,109=>178,110=>179,
													   115=>180,14=>181,116=>182,117=>183,118=>289,119=>184,120=>185,121=>186,
													   122=>191,123=>192,124=>193,125=>194,126=>196,127=>195,128=>198,129=>199,
													   130=>200,131=>205,132=>304,133=>203,134=>202,135=>305,146=>187,136=>291,
													   147=>292,148=>293,149=>294,145=>306,137=>188,150=>189,151=>189,152=>190,
													   153=>296,86=>204,138=>308,139=>310,140=>299,142=>213,143=>214,144=>283,
													   141=>284,104=>220,21=>281,105=>221,108=>177,106=>222);
						  $pdf_response =$this->sale_withitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,1,$show_label); 			  
						  $pdf[rand()] =$pdf_response['pdf_name'];
						 
						  }
						 }
						if($voucher_info['vch_subtype_id']==9){ 
						 $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(272);
						  if($defaultPrint_template==270){
						  $config_codes_list = array(8=>274,9=>276,6=>173,34=>287,24=>175,25=>303,108=>177,109=>178,110=>179,
													   115=>180,14=>181,116=>182,117=>183,118=>289,119=>184,120=>185,121=>186,
													   122=>191,123=>192,124=>193,125=>194,126=>196,127=>195,128=>198,129=>199,
													   130=>200,131=>205,132=>304,133=>203,134=>202,135=>305,146=>187,136=>291,
													   147=>292,148=>293,149=>294,145=>306,137=>188,150=>189,151=>189,152=>190,
													   153=>296,86=>204,138=>308,139=>310,140=>299,142=>213,143=>214,144=>283,
													   141=>284,104=>220,21=>281,105=>221,108=>177,106=>222);
						  }
						 $pdf_response= $this->sale_withoutstock_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,1,$show_label); 			  
						 $pdf[rand()] =$pdf_response['pdf_name'];
						}
					   if($voucher_info['vch_subtype_id']==0 ||  $voucher_info['vch_subtype_id']==10){
						  $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(273);
						  if($defaultPrint_template>0 && $defaultPrint_template==271){
						  $config_codes_list = array(8=>275,9=>225,6=>226,34=>288,24=>227,25=>228,108=>229,109=>230,110=>180,
													   115=>232,14=>233,116=>234,117=>2,118=>290,119=>236,120=>237,121=>238,
													   112=>278,111=>277,113=>279,114=>280,133=>244,154=>307,86=>245,138=>309,139=>311,
													   140=>300,142=>255,143=>256,144=>285,141=>286,104=>262,21=>282,105=>263,108=>229,106=>264);
						  }
						  $pdf_response = $this->sale_withoutitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,1,$show_label); 		  
						  $pdf[rand()] =$pdf_response['pdf_name'];
					   }
				$labelcounters++;	   
			   }
		   }

      $zip = new \ZipArchive();
	  $folder_path = WRITEPATH.'uploads/db_backup_temp';
	  
	
	 $zip_file_path =  $folder_path.'/'.$zipfile.'.zip';
     if($zip->open($zip_file_path, \ZipArchive::CREATE) == TRUE) {		 
		 foreach($pdf as $key => $file_name){
		    $file_path =  $folder_path.'/'.$file_name;
            $zip->addFile($file_path,$file_name);
		 }
		 $zip->close();			  
	  }
	   foreach($pdf as $key => $file_name){
		      $file_path =  $folder_path.'/'.$file_name;
			  if(file_exists($file_path))
		      unlink($file_path);
		 }	 
		$fullFilePath = $folder_path.'/'.$zipfile.'.zip';		
		 $fileName     = $zipfile.'.zip';
     echo json_encode(array("status"=>true,'file_name'=>$fileName,'zipfile'=>base_url().'/writable/uploads/db_backup_temp/'.$zipfile.'.zip'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);		 
     exit;
   }
   
   public function sales_multi_print($voucher_txn_ids,$zipfile){
	    
		 $get_business_id   = $this->CommonModel->get_business_id();
		 $pdf=[];
		 $headers=[];
		 $footers=[];
	     $voucher_txn_ids       = rtrim($voucher_txn_ids,",");
		 $multi_voucher_txn_ids = array();
		 if($voucher_txn_ids){
		   $multi_voucher_txn_ids = explode(",",$voucher_txn_ids);
		 }
	    $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	    $tcpdf->SetTitle('Sale');
		$tcpdf->SetHeaderMargin(1,0,0,0);
		$tcpdf->SetFooterMargin(79,0,0,0);
		$tcpdf->setPrintHeader(false);
		$tcpdf->setPrintFooter(false);
		$tcpdf->setListIndentWidth(3);
		$tcpdf->SetAutoPageBreak(TRUE, 79);
		$tcpdf->SetDisplayMode(100);
		$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$tcpdf->SetFont('segoe_ui', '', '8.5pt');
		$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
		$tcpdf->SetFont('arial', '', '','',false);
		$tcpdf->SetFont('verdana', '', '','',false);
		$tcpdf->SetFont('times_new_roman', '', '','',false);
		$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
		 // use the font
		$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
		
		 if($multi_voucher_txn_ids){
			   foreach($multi_voucher_txn_ids as $voucher_txn_id){
					    
				        $voucher_info      = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);     
					   								
						$voucher_series_id = $voucher_info['comp_vch_series_id']; 						
						if($voucher_info['vch_subtype_id']==8){ 
						   $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(272);
						  if($defaultPrint_template==270){
						  $config_codes_list = array(8=>274,9=>276,6=>173,34=>287,24=>175,25=>303,108=>177,109=>178,110=>179,
													   115=>180,14=>181,116=>182,117=>183,118=>289,119=>184,120=>185,121=>186,
													   122=>191,123=>192,124=>193,125=>194,126=>196,127=>195,128=>198,129=>199,
													   130=>200,131=>205,132=>304,133=>203,134=>202,135=>305,146=>187,136=>291,
													   147=>292,148=>293,149=>294,145=>306,137=>188,150=>189,151=>189,152=>190,
													   153=>296,86=>204,138=>308,139=>310,140=>299,142=>213,143=>214,144=>283,
													   141=>284,104=>220,21=>281,105=>221,108=>177,106=>222);
						  $pdf_response =$this->sale_withitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,1); 			  
						  $pdf[$voucher_txn_id] =$pdf_response['pdf_name'];
						 
						  }
						 }
						if($voucher_info['vch_subtype_id']==9){ 
						 $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(272);
						  if($defaultPrint_template==270){
						  $config_codes_list = array(8=>274,9=>276,6=>173,34=>287,24=>175,25=>303,108=>177,109=>178,110=>179,
													   115=>180,14=>181,116=>182,117=>183,118=>289,119=>184,120=>185,121=>186,
													   122=>191,123=>192,124=>193,125=>194,126=>196,127=>195,128=>198,129=>199,
													   130=>200,131=>205,132=>304,133=>203,134=>202,135=>305,146=>187,136=>291,
													   147=>292,148=>293,149=>294,145=>306,137=>188,150=>189,151=>189,152=>190,
													   153=>296,86=>204,138=>308,139=>310,140=>299,142=>213,143=>214,144=>283,
													   141=>284,104=>220,21=>281,105=>221,108=>177,106=>222);
						  }
						 $pdf_response= $this->sale_withoutstock_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,1); 			  
						 $pdf[$voucher_txn_id] =$pdf_response['pdf_name'];
						}
					   if($voucher_info['vch_subtype_id']==0 ||  $voucher_info['vch_subtype_id']==10){
						  $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(273);
						  if($defaultPrint_template>0 && $defaultPrint_template==271){
						  $config_codes_list = array(8=>275,9=>225,6=>226,34=>288,24=>227,25=>228,108=>229,109=>230,110=>180,
													   115=>232,14=>233,116=>234,117=>2,118=>290,119=>236,120=>237,121=>238,
													   112=>278,111=>277,113=>279,114=>280,133=>244,154=>307,86=>245,138=>309,139=>311,
													   140=>300,142=>255,143=>256,144=>285,141=>286,104=>262,21=>282,105=>263,108=>229,106=>264);
						  }
						  $pdf_response = $this->sale_withoutitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,1); 		  
						  $pdf[$voucher_txn_id] =$pdf_response['pdf_name'];
					   }
			   }
		   }

      $zip = new \ZipArchive();
	  $folder_path = WRITEPATH.'uploads/db_backup_temp';
	  
	
	 $zip_file_path =  $folder_path.'/'.$zipfile.'.zip';
     if($zip->open($zip_file_path, \ZipArchive::CREATE) == TRUE) {		 
		 foreach($pdf as $key => $file_name){
		    $file_path =  $folder_path.'/'.$file_name;
            $zip->addFile($file_path,$file_name);
		 }
		 $zip->close();			  
	  }
	  foreach($pdf as $key => $file_name){
		      $file_path =  $folder_path.'/'.$file_name;
		      unlink($file_path);
		 }	 

  echo json_encode(array("status"=>true,'zipfile'=>stripslashes(base_url().'/writable/uploads/db_backup_temp/'.$zipfile)));		 
   }
   
   
   public function sale_print($voucher_txn_id){
		$voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
     
	   $config_codes_list=array();
		if($voucher_info)
		$voucher_series_id =$voucher_info['comp_vch_series_id']; 
	    else
        die('N/A');			
		$get_business_id       = $this->CommonModel->get_business_id();
	    if($voucher_info['vch_subtype_id']==8){ 
		 $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(272);
	      if($defaultPrint_template==270){
		  $config_codes_list = array(8=>274,9=>276,6=>173,34=>287,24=>175,25=>303,108=>177,109=>178,110=>179,
			                           115=>180,14=>181,116=>182,117=>183,118=>289,119=>184,120=>185,121=>186,
									   122=>191,123=>192,124=>193,125=>194,126=>196,127=>195,128=>198,129=>199,
									   130=>200,131=>205,132=>304,133=>203,134=>202,135=>305,146=>187,136=>291,
									   147=>292,148=>293,149=>294,145=>306,137=>188,150=>189,151=>189,152=>190,
									   153=>296,86=>204,138=>308,139=>310,140=>299,142=>213,143=>214,144=>283,
									   141=>284,104=>220,21=>281,105=>221,108=>177,106=>222);
		  }
		  if(isset($_GET['p']) && $_GET['p']==1) 
				 $this->sale_withitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,true);  
		  else 
               $this->sale_withitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list); 			  
	    }
		if($voucher_info['vch_subtype_id']==9){ 
		 $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(272);
	      if($defaultPrint_template==270){
		  $config_codes_list = array(8=>274,9=>276,6=>173,34=>287,24=>175,25=>303,108=>177,109=>178,110=>179,
			                           115=>180,14=>181,116=>182,117=>183,118=>289,119=>184,120=>185,121=>186,
									   122=>191,123=>192,124=>193,125=>194,126=>196,127=>195,128=>198,129=>199,
									   130=>200,131=>205,132=>304,133=>203,134=>202,135=>305,146=>187,136=>291,
									   147=>292,148=>293,149=>294,145=>306,137=>188,150=>189,151=>189,152=>190,
									   153=>296,86=>204,138=>308,139=>310,140=>299,142=>213,143=>214,144=>283,
									   141=>284,104=>220,21=>281,105=>221,108=>177,106=>222);
		  }
		  if(isset($_GET['p']) && $_GET['p']==1) 
				 $this->sale_withoutstock_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,true,0);  
		  else 
               $this->sale_withoutstock_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,0); 			  
	    }
	   if($voucher_info['vch_subtype_id']==0 ||  $voucher_info['vch_subtype_id']==10){
		  $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(273);
	      if($defaultPrint_template>0 && $defaultPrint_template==271){
		  $config_codes_list = array(8=>275,9=>225,6=>226,34=>288,24=>227,25=>228,108=>229,109=>230,110=>180,
			                           115=>232,14=>233,116=>234,117=>2,118=>290,119=>236,120=>237,121=>238,
									   112=>278,111=>277,113=>279,114=>280,133=>244,154=>307,86=>245,138=>309,139=>311,
									   140=>300,142=>255,143=>256,144=>285,141=>286,104=>262,21=>282,105=>263,108=>229,106=>264);
		  }
		   if(isset($_GET['p']) && $_GET['p']==1) 
				 $this->sale_withoutitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,true,0);  
		  else 
              $this->sale_withoutitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,false,0); 		  
	   }
	   
	 die();
    }
	
	 public function purchasereq_print($voucher_txn_id){
	   
	   $item_transactions = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
	   if($item_transactions){
		 $this->purchasereq_withitem_preview2($voucher_txn_id); 
	     }	   
	   else{
		  $this->purchasereq_withoutitem_preview2($voucher_txn_id); 
	    }
    }
	
	public function quotation_print($voucher_txn_id){
	   $item_transactions = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
	   if($item_transactions){
		 $this->quotation_withitem_preview2($voucher_txn_id); 
	    }	   
		else{
	     $this->quotation_withoutitem_preview2($voucher_txn_id); 
	    }
    }
   
    public function saleordr_print($voucher_txn_id){	   
	   $item_transactions = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
	   if($item_transactions){
		 $this->saleordr_withitem_preview2($voucher_txn_id); 
	     }	   
	   else
		  $this->saleordr_withoutitem_preview2($voucher_txn_id); 	    
   }   
   
   public function purchase_print($voucher_txn_id){
	 
	   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);

	   if($voucher_info['vch_subtype_id']==5){
		 $this->purchase_withitem_preview2($voucher_txn_id); 
	     }	   
	   if($voucher_info['vch_subtype_id']==0 || $voucher_info['vch_subtype_id']==6 || $voucher_info['vch_subtype_id']==7){
		  $this->purchase_withoutitem_preview2($voucher_txn_id); 
	    }
   }
   public function purchaseordr_print($voucher_txn_id){
	   $item_transactions = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id);
	   if($item_transactions){
		 $this->purchaseordr_withitem_preview2($voucher_txn_id); 
	     }	   
	   else{
		 $this->purchaseordr_withoutitem_preview2($voucher_txn_id); 
	   }
   }
   public function crnote_print($voucher_txn_id){
	   
	   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);

	   if($voucher_info['vch_subtype_id']==1){
		 $this->crnote_withitem_preview2($voucher_txn_id); 
	     }	   
	   if($voucher_info['vch_subtype_id']==0 || $voucher_info['vch_subtype_id']==2 ){
		  $this->crnote_withoutitem_preview2($voucher_txn_id); 
	    }
   }
   public function drnote_print($voucher_txn_id){
	   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);

	   if($voucher_info['vch_subtype_id']==3 || $voucher_info['vch_subtype_id']==4){
		 $this->drnote_withitem_preview2($voucher_txn_id); 
	     }	   
	   else{
		  $this->drnote_withoutitem_preview2($voucher_txn_id); 
	    }
   }
   
   
   /*********  GSTR1 monthly Download Report **************/
   
   public function egstr_one_report(){
	 ob_start();     
	 $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	 $from_date     = $_GET['fromdate'] ?? '';
     $to_date       = $_GET['todate'] ?? '';   
     $from_date     = validate_fy_from_date($from_date);
     $to_date       = validate_fy_to_date($to_date);  
	 $from_date_ymd = date('Y-m-d', strtotime($from_date));
     $to_date_ymd   = date('Y-m-d', strtotime($to_date));
     $table_lists   = $this->table_lists_detailed;
     $response      = json_decode($this->ReportsModel->load_gstr_detailed($from_date_ymd,$to_date_ymd,$table_lists),true);   
     $pdf_nme  ='egstr1.pdf';	
	 $pdf='';
	 $header_html ='</h1><table width="100%" align="center"><strong>FORM GSTR-1</strong></table></h1>';
	 $footer_html ='</h1></h1>';
	
	 $tcpdf->SetTitle('eGSTR1');
	 $tcpdf->SetHeaderMargin(1,0,0,0);
	 $tcpdf->SetFooterMargin(79,0,0,0);
	 $tcpdf->setPrintHeader(false);
	 $tcpdf->setPrintFooter(false);
	 $tcpdf->setListIndentWidth(3);
	 $tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
	 $tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
	 $tcpdf->SetAutoPageBreak(TRUE, 79);
	 $tcpdf->SetDisplayMode(100);
	 $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	$tcpdf->SetFont('segoe_ui', '', '8.5pt');
	$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
	$tcpdf->SetFont('arial', '', '','',false);
	$tcpdf->SetFont('verdana', '', '','',false);
	$tcpdf->SetFont('times_new_roman', '', '','',false);
	$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
	 // use the font
	$tcpdf->SetMargins(1,0,1,0);
	$tcpdf->AddPage('P', 'A4'); 
	$inner_pdf='';
	
	 if(isset($response['data'])){
	foreach($response['data'] as $row){
	  	
		
	$exploded = explode("_",$row['tableno']);
	if(isset($exploded[1]))
	$tname=  $row['table_name'];
	else{
	$tname =  $row['tableno'].'-'.$row['table_name'];								
	 $inner_pdf.= ' <tr height="22">
              <td colspan="9" style="background-color:#d8f1d1; color: black;"><span style="font-family:dejavuserif;font-weight:bold;">&nbsp;&nbsp;'.$tname.'</span></td>
             </tr>';
	}
	 if(isset($exploded[1])){
		$tname=  '<span style="font-family:dejavuserif;">'.$row['table_name'].'</span>';
		$inner_pdf.='<tr class="">
         <td style="font-family:fontsfreenetsegoeuib;" colspan="2">&nbsp;&nbsp;'.$tname.'</td>
         <td align="center">'.$row['total_records'].'</td>
         <td class="verticalCenter text-center">Invoice</td>
		 <td class="verticalCenter text-right"><span style="font-family:dejavuserif;">'.$row['taxable_value'].'</span></td>
         <td class="verticalCenter text-right"><span style="font-family:dejavuserif;">'.$row['igst'].'</span></td>
         <td class="verticalCenter text-right"><span style="font-family:dejavuserif;">'.$row['cgst'].'</span></td>
         <td class="verticalCenter text-right"><span style="font-family:dejavuserif;">'.$row['sgst'].'</span></td>
         <td class="verticalCenter text-right"><span style="font-family:dejavuserif;">'.$row['cess'].'</span></td>
        </tr>';
									
	   }
	 else{
		$inner_pdf.='<tr class="">
                 <td style="font-family:fontsfreenetsegoeuib;" colspan="2">&nbsp;&nbsp;Total</td>
                 <td align="center">'.$row['total_records'].'</td>
                 <td>Invoice</td>
				 <td><span style="font-family:dejavuserif;">'.$row['taxable_value'].'</span></td>
                 <td><span style="font-family:dejavuserif;">'.$row['igst'].'</span></td>
                 <td><span style="font-family:dejavuserif;">'.$row['cgst'].'</span></td>
                 <td><span style="font-family:dejavuserif;">'.$row['sgst'].'</span></td>
                 <td><span style="font-family:dejavuserif;">'.$row['cess'].'</span></td>
                 </tr>';
	  }   
	
	}
	 }
	
	$pdf .='<section class="sec">
	<table width="790"  cellpadding="5" style="font-family:segoe_ui,arial;font-size:8.5pt;">

                            <thead style="background:#F8F8F8 ">
                                <tr style="background-color:#c9ece1; color: black;">

                                    <th style="display: table-cell;" rowspan="2" colspan="2"><span style="font-family:dejavuserif;">&nbsp;&nbsp;Description</span></th>
                                    <th  rowspan="2"><span style="font-family:dejavuserif;">No. of records</span></th>
                                    <th  style="padding-left: 19px;" rowspan="2"><span style="font-family:dejavuserif;">Document Type</span></th>

                                    <th  rowspan="2"><span style="font-family:dejavuserif;">Value (<span style="font-family:dejavuserif;">₹</span>)</span></th>
                                    <th><span style="font-family:dejavuserif;">Integrated tax (<span style="font-family:dejavuserif;">₹</span>)</span></th>
                                    <th><span style="font-family:dejavuserif;">Central tax (<span style="font-family:dejavuserif;">₹</span>)</span></th>
                                    <th><span style="font-family:dejavuserif;">State/UT tax (<span style="font-family:dejavuserif;">₹</span>)</span></th>
                                    <th><span style="font-family:dejavuserif;">Cess (<span style="font-family:dejavuserif;">₹</span>)</span></th>
                                </tr>
                            </thead>
                            <tbody>
					  '.$inner_pdf.'
					</tbody>
				</table> 
	</section>';
	  
	$delimiter  = '<section class="sec">';
	$chunks     = explode($delimiter, $pdf);

	$tcpdf->SetY(20); 
	 $final_chunks=array();
	foreach($chunks as $dd){
		if($dd!='')
		$final_chunks[]=$dd;
	}
	$cnt = count($final_chunks);

	$dimensions = [//PDF_UNIT defaults to millimeters
			"margins"   => $tcpdf->GetMargins(),
			"width"     => $tcpdf->getPageWidth(),
			"height"    => $tcpdf->getPageHeight(),
			"hh"        => $tcpdf->getCellHeight('8.75')
		];
		
	for ($i = 0; $i < $cnt; $i++) {
		  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
		  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
		  $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
		 /* if(round($total_page_height)-round($tcpdf->GetY()) >0)
		 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
		 */ if ($i < $cnt - 1) {       
			$tcpdf->AddPage('P', 'A4');
			
		 }
	 }


	$tcpdf->lastPage();
	 ob_end_clean();
	
	 $tcpdf->Output($pdf_nme, true);   
   }
   
   
   /**********  Sale Print Without Item ***************/
   public function sale_withoutitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,$browser_preview=false,$is_multi_print=0,$show_labels='')
   {   ob_start();     
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
	  
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	   $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		$main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	   	
	
	$pdf_nme  =time().url_title($data['invoice_no'],'-').'.pdf';	
	$pdf='';
	
	if(GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1))
	  $invoice_title = GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1);
	else
	 $invoice_title = 'TAX INVOICE';	
 
    if($show_labels!=''){
	 $header_title='<td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;"></td><td colspan="2" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$invoice_title.'</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'&nbsp;&nbsp;</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$invoice_title.'</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
         '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="'.GetPrintLabelVal($config_codes_list[9],9,$voucher_series_id).'">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[6],6,$voucher_series_id).'">'.$data['comp_gst'].'</span></td></tr>
					<tr><td colspan="2" style="'.GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id).'">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[24],24,$voucher_series_id).'">'.$data['phone'].'</span></td></tr>
					<tr><td colspan="2">Email:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[25],25,$voucher_series_id).'">'.$data['email'].'</span></td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" stye="'.GetPrintLabelVal($config_codes_list[108],108,$voucher_series_id).'">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;'.GetPrintLabelVal($config_codes_list[109],109,$voucher_series_id).'"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[110],110,$voucher_series_id).'"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[115],115,$voucher_series_id).'"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="'.GetPrintLabelVal($config_codes_list[14],14,$voucher_series_id).'">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="'.GetPrintLabelVal($config_codes_list[116],116,$voucher_series_id).'">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="2" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span>:
		  <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[117],117,$voucher_series_id).'">'.$party_name.'</span><br />&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[118],118,$voucher_series_id).'">'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'</span><br />&nbsp;&nbsp;Phone: <span style="'.GetPrintLabelVal($config_codes_list[119],119,$voucher_series_id).'">'.$party_phone.'</span><br />&nbsp;&nbsp;Email: <span style="'.GetPrintLabelVal($config_codes_list[120],120,$voucher_series_id).'">'.$party_email.'</span><br>&nbsp;&nbsp;GSTIN: <span style="'.GetPrintLabelVal($config_codes_list[121],121,$voucher_series_id).'">#'.$party_gstin.'</span></p>
		 </td>
		  <td colspan="2" align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[117],117,$voucher_series_id).'">'.$shipto_place.'</span><br />&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[118],118,$voucher_series_id).'">'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br /></span>&nbsp;&nbsp;Phone: <span style="'.GetPrintLabelVal($config_codes_list[119],119,$voucher_series_id).'">'.$party_phone.'</span><br />&nbsp;&nbsp;Email: <span style="'.GetPrintLabelVal($config_codes_list[120],120,$voucher_series_id).'">'.$party_email.'</span></p>
		  </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	if(GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1)){
                   $terms_conditions_texct=GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1);
	}
	else
		$terms_conditions_texct ='<ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol>';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left">
				  <table width="500" cellspacing="5">
				  <tr><td width="100" align="left">Bank</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[142],142,$voucher_series_id).'">'.$bank_name.'</span></td></tr>
				  <tr><td width="100" align="left">Account</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[143],143,$voucher_series_id).'">'.$bank_account_name.'</span></td></tr>
				   <tr><td width="100" align="left">IFSC</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[144],144,$voucher_series_id).'">'.$bank_ifsc_name.'</span></td></tr>
				    <tr><td width="100" align="left">Branch</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[141],141,$voucher_series_id).'">'.$bank_branch_name.'</span></td></tr>
				  </table>
				 </td>
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <span style="text-align:right;'.GetPrintLabelVal($config_codes_list[21],21,$voucher_series_id).'">For '.ucwords(strtolower($data['company_name'])).'</span>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p style="'.GetPrintLabelVal($config_codes_list[104],104,$voucher_series_id).'"><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p style="'.GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id).'">'.$terms_conditions_texct.'</p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$tax_row['tax_hsn_sac'].'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$igst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$cgst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$sgst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[111],111,$voucher_series_id).'" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[112],112,$voucher_series_id).'" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[112],112,$voucher_series_id).'" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[113],113,$voucher_series_id).'" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[114],114,$voucher_series_id).'" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[112],112,$voucher_series_id).'" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[112],112,$voucher_series_id).'" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[112],112,$voucher_series_id).'" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;'.GetPrintLabelVal($config_codes_list[114],114,$voucher_series_id).'" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	
	if(!empty(GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id))){
		$total_invoice_value_style =GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id).';font-weight:bold;';
	}else
	$total_invoice_value_style='font-size:11pt;font-weight:bold;font-family:fontsfreenetsegoeuib;';
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
     $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
   $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="'.$total_invoice_value_style.'"><span style="font-family:dejavuserif;">&#8377;</span><span style="'.$total_invoice_value_style.'">'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</span></td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';

	
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);

$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //75
 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
 if($is_multi_print==0){
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
 }
 if($is_multi_print==1){	
	$tcpdf->Output(WRITEPATH."uploads/db_backup_temp/".$pdf_nme,'F');	
	return array("pdf_name"=>$pdf_nme);
 }
}  
   
  
   /**********  Sale Print With Item ***************/
  public function sale_withitem_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,$browser_preview=false,$is_multi_print=0,$show_labels='')
   {  
      ob_start();
      $company_logo     = logo_src($this->company_id);	   
	  $bo_id            = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id); 
	 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
	  $get_einvmaster_info = $this->TransactionModel->get_einvmaster_info($voucher_txn_id); 	
	   if($get_einvmaster_info){	
	    $invoice_qr_data =$get_einvmaster_info['einv_QRCode'];		
	    require_once(APPPATH . 'Libraries/phpqrcode/qrlib.php');
		 $fsavename = APPPATH . 'Libraries/phpqrcode/temp/qr'.$voucher_txn_id.'.png';
		\QRcode::png($invoice_qr_data, $fsavename, 'L', 0,1);
		
		 $file_path = APPPATH . 'Libraries/phpqrcode/temp/qr'.$voucher_txn_id.'.png';
		 $invoice_qr_img= '<img  height="80" src="'.$file_path.'" />';
	   }else
		   $invoice_qr_img='';
	 
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	   
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
	$pdf_nme  =time().url_title($data['invoice_no'],'-').'.pdf';
	$pdf='';
	
	 if(GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1))
		$invoice_title = GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1);
	else 
		$invoice_title = 'TAX INVOICE';
	
	if($party_city!='' && $party_pin!='')
	  $locationpin = $party_city.', '.$party_pin;
    else if($party_city!='' && $party_pin=='')
	  $locationpin = $party_city;
  else if($party_pin!='')
	  $locationpin = $party_pin;
	else
		$locationpin='';
	$default_strong_head='font-weight:700;';
	
	if($show_labels!=''){
	 $header_title='<td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;"></td><td colspan="2" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$invoice_title.'</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'&nbsp;&nbsp;</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$invoice_title.'</td>';	
	}
	
    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="'.GetPrintLabelVal($config_codes_list[9],9,$voucher_series_id).'">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[6],6,$voucher_series_id).'">'.$data['comp_gst'].'</span></td></tr>
					<tr><td colspan="2" style="'.GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id).'">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[24],24,$voucher_series_id).'">'.$data['phone'].'</span></td></tr>
					<tr><td colspan="2">Email:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[25],25,$voucher_series_id).'">'.$data['email'].'</span></td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[108],108,$voucher_series_id).'">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;'.GetPrintLabelVal($config_codes_list[109],109,$voucher_series_id).'"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[110],110,$voucher_series_id).'"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[115],115,$voucher_series_id).'"></td>
                </tr>             
            </table> 
          </td>
          <td align="center"  style="border-left:0.5px solid #000;"><br />'.$invoice_qr_img.'
          
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="'.GetPrintLabelVal($config_codes_list[14],14,$voucher_series_id).'">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="'.GetPrintLabelVal($config_codes_list[116],116,$voucher_series_id).'">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[117],117,$voucher_series_id).'">'.$party_name.'</span>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[118],118,$voucher_series_id).'">'.$party_add1.' '.$party_add2.'&nbsp;&nbsp;'.$locationpin.'</span><br />&nbsp;&nbsp;Phone: <span style="'.GetPrintLabelVal($config_codes_list[119],119,$voucher_series_id).'">'.$party_phone.'</span><br />&nbsp;&nbsp;Email: <span style="'.GetPrintLabelVal($config_codes_list[120],120,$voucher_series_id).'">'.$party_email.'</span><br>&nbsp;&nbsp;GSTIN: <span style="'.GetPrintLabelVal($config_codes_list[121],121,$voucher_series_id).'">#'.$party_gstin.'</span><br></p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[146],146,$voucher_series_id).'">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[136],136,$voucher_series_id).'">'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'</span><br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[137],137,$voucher_series_id).'">'.$shipto_place.'</span><br />&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[150],150,$voucher_series_id).'">'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br /></span>&nbsp;&nbsp;Phone: <span style="'.GetPrintLabelVal($config_codes_list[151],151,$voucher_series_id).'">'.$party_phone.'</span><br />&nbsp;&nbsp;Email: <span style="'.GetPrintLabelVal($config_codes_list[152],152,$voucher_series_id).'">'.$party_email.'</span></p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[122],122,$voucher_series_id).'">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: <span style="'.GetPrintLabelVal($config_codes_list[123],123,$voucher_series_id).'">'.$transport_enrollment_id.'</span><br/>&nbsp;&nbsp;Transport Mode: <span style="'.GetPrintLabelVal($config_codes_list[124],124,$voucher_series_id).'">'.strtoupper($transport_veh_type).'</span><br />&nbsp;&nbsp;Vehicle No: <span style="'.GetPrintLabelVal($config_codes_list[125],125,$voucher_series_id).'">'.$transport_veh_no .'</span></p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[127],127,$voucher_series_id).'" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'"  width="170" height="22"  align="center">Item</th>
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'"  width="187" height="22"  align="center">Description</th>
	           <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[128],128,$voucher_series_id).'"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[129],129,$voucher_series_id).'" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[130],130,$voucher_series_id).'" width="60" height="22"  align="center">Qty</th>			 
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[132],132,$voucher_series_id).'"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	if(GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1)){
                   $terms_conditions_texct=GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1);
	}
	else
		$terms_conditions_texct ='<ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol>';
				   
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left">
				  <table width="500" cellspacing="5">
				  <tr><td width="100" align="left">Bank</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[142],142,$voucher_series_id).'">'.$bank_name.'</span></td></tr>
				  <tr><td width="100" align="left">Account</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[143],143,$voucher_series_id).'">'.$bank_account_name.'</span></td></tr>
				   <tr><td width="100" align="left">IFSC</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[144],144,$voucher_series_id).'">'.$bank_ifsc_name.'</span></td></tr>
				    <tr><td width="100" align="left">Branch</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[141],141,$voucher_series_id).'">'.$bank_branch_name.'</span></td></tr>
				  </table>				  
				 </td>                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <span style="'.GetPrintLabelVal($config_codes_list[21],21,$voucher_series_id).'text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</span>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br><span style="'.GetPrintLabelVal($config_codes_list[104],104,$voucher_series_id).'">Authorised Signatory</span>  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p style="'.GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id).'">'.$terms_conditions_texct.'</p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$y_before_header = $tcpdf->getY();
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$y_after_header = $tcpdf->getY();
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 

$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$tax_row['tax_hsn_sac'].'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$igst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$cgst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$sgst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[127],127,$voucher_series_id).'" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;font-family:dejavuserif;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;font-family:dejavuserif;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[128],128,$voucher_series_id).'" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[129],129,$voucher_series_id).'" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[130],130,$voucher_series_id).'" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[132],132,$voucher_series_id).'" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
  
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	if(!empty(GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id))){
		$total_invoice_value_style =GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id).';font-weight:bold;';
	}else
	$total_invoice_value_style='font-size:11pt;font-weight:bold;font-family:fontsfreenetsegoeuib;';
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="'.$total_invoice_value_style.'"><span style="font-family:dejavuserif;">&#8377;</span><span style="'.$total_invoice_value_style.'">'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</span></td></tr><tr><td colspan="2" align="left" style="'.GetPrintLabelVal($config_codes_list[140],140,$voucher_series_id).'"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';


	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
 
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';


 
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
   
 
$tcpdf->lastPage();
  ob_end_clean();
  
   if($is_multi_print==0){
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);	


 } 
 if($is_multi_print==1){	
	$tcpdf->Output(WRITEPATH."uploads/db_backup_temp/".$pdf_nme,'F');	
	return array("pdf_name"=>$pdf_nme);
 }
 
}  
/**********  Sale Print Without Stock Item ***************/
  public function sale_withoutstock_preview2($voucher_series_id,$voucher_txn_id,$config_codes_list,$browser_preview=false,$is_multi_print=0,$show_labels='')
   {  
      ob_start();
      $company_logo     = logo_src($this->company_id);	   
	  $bo_id            = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id); 
	 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	      
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
	$pdf_nme  =time().url_title($data['invoice_no'],'-').'.pdf';	
	$pdf='';
	
	if(GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1))
		$invoice_title = GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1);
	else
		$invoice_title = 'TAX INVOICE';
	
	if($party_city!='' && $party_pin!='')
	  $locationpin = $party_city.', '.$party_pin;
    else if($party_city!='' && $party_pin=='')
	  $locationpin = $party_city;
  else if($party_pin!='')
	  $locationpin = $party_pin;
	else
		$locationpin='';
	$default_strong_head='font-weight:700;';
	
	if($show_labels!=''){
	 $header_title='<td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;"></td><td colspan="2" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$invoice_title.'</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'&nbsp;&nbsp;</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$invoice_title.'</td>';	
	}
	
    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="'.GetPrintLabelVal($config_codes_list[9],9,$voucher_series_id).'">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[6],6,$voucher_series_id).'">'.$data['comp_gst'].'</span></td></tr>
					<tr><td colspan="2" style="'.GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id).'">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[24],24,$voucher_series_id).'">'.$data['phone'].'</span></td></tr>
					<tr><td colspan="2">Email:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[25],25,$voucher_series_id).'">'.$data['email'].'</span></td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[108],108,$voucher_series_id).'">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;'.GetPrintLabelVal($config_codes_list[109],109,$voucher_series_id).'"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[110],110,$voucher_series_id).'"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left" style="'.GetPrintLabelVal($config_codes_list[115],115,$voucher_series_id).'"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="'.GetPrintLabelVal($config_codes_list[14],14,$voucher_series_id).'">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="'.GetPrintLabelVal($config_codes_list[116],116,$voucher_series_id).'">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[117],117,$voucher_series_id).'">'.$party_name.'</span><br />&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[118],118,$voucher_series_id).'">'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$locationpin.'</span><br />&nbsp;&nbsp;Phone: <span style="'.GetPrintLabelVal($config_codes_list[119],119,$voucher_series_id).'">'.$party_phone.'</span><br />&nbsp;&nbsp;Email: <span style="'.GetPrintLabelVal($config_codes_list[120],120,$voucher_series_id).'">'.$party_email.'</span><br>&nbsp;&nbsp;GSTIN: <span style="'.GetPrintLabelVal($config_codes_list[121],121,$voucher_series_id).'">#'.$party_gstin.'</span><br></p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[146],146,$voucher_series_id).'">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[136],136,$voucher_series_id).'">'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'</span><br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[137],137,$voucher_series_id).'">'.$shipto_place.'</span><br />&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[150],150,$voucher_series_id).'">'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br /></span>&nbsp;&nbsp;Phone: <span style="'.GetPrintLabelVal($config_codes_list[151],151,$voucher_series_id).'">'.$party_phone.'</span><br />&nbsp;&nbsp;Email: <span style="'.GetPrintLabelVal($config_codes_list[152],152,$voucher_series_id).'">'.$party_email.'</span></p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[122],122,$voucher_series_id).'">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: <span style="'.GetPrintLabelVal($config_codes_list[123],123,$voucher_series_id).'">'.$transport_enrollment_id.'</span><br/>&nbsp;&nbsp;Transport Mode: <span style="'.GetPrintLabelVal($config_codes_list[124],124,$voucher_series_id).'">'.strtoupper($transport_veh_type).'</span><br />&nbsp;&nbsp;Vehicle No: <span style="'.GetPrintLabelVal($config_codes_list[125],125,$voucher_series_id).'">'.$transport_veh_no .'</span></p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[127],127,$voucher_series_id).'" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'"  width="170" height="22"  align="center">Item</th>
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'"  width="187" height="22"  align="center">Description</th>
	           <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[128],128,$voucher_series_id).'"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[129],129,$voucher_series_id).'" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[130],130,$voucher_series_id).'" width="60" height="22"  align="center">Qty</th>			 
			   <th style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;'.GetPrintLabelVal($config_codes_list[132],132,$voucher_series_id).'"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	if(GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1)){
                   $terms_conditions_texct=GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1);
	}
	else
		$terms_conditions_texct ='<ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol>';
				   
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left">
				  <table width="500" cellspacing="5">
				  <tr><td width="100" align="left">Bank</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[142],142,$voucher_series_id).'">'.$bank_name.'</span></td></tr>
				  <tr><td width="100" align="left">Account</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[143],143,$voucher_series_id).'">'.$bank_account_name.'</span></td></tr>
				   <tr><td width="100" align="left">IFSC</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[144],144,$voucher_series_id).'">'.$bank_ifsc_name.'</span></td></tr>
				    <tr><td width="100" align="left">Branch</td><td width="120" align="left"><span style="'.GetPrintLabelVal($config_codes_list[141],141,$voucher_series_id).'">'.$bank_branch_name.'</span></td></tr>
				  </table>				  
				 </td>                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <span style="'.GetPrintLabelVal($config_codes_list[21],21,$voucher_series_id).'text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</span>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br><span style="'.GetPrintLabelVal($config_codes_list[104],104,$voucher_series_id).'">Authorised Signatory</span>  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p style="'.GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id).'">'.$terms_conditions_texct.'</p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$tax_row['tax_hsn_sac'].'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$igst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$cgst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$sgst_tax_.'</td>
							 <td style="'.GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id).'">'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[127],127,$voucher_series_id).'" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;font-family:dejavuserif;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;font-family:dejavuserif;'.GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id).'" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[128],128,$voucher_series_id).'" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[129],129,$voucher_series_id).'" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[130],130,$voucher_series_id).'" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;'.GetPrintLabelVal($config_codes_list[132],132,$voucher_series_id).'" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
  
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	if(!empty(GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id))){
		$total_invoice_value_style =GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id).';font-weight:bold;';
	}else
	$total_invoice_value_style='font-size:11pt;font-weight:bold;font-family:fontsfreenetsegoeuib;';
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="'.$total_invoice_value_style.'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left" style="'.GetPrintLabelVal($config_codes_list[140],140,$voucher_series_id).'"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);

$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //81

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
   
 
$tcpdf->lastPage();
  ob_end_clean();
 if($is_multi_print==0){ 
	if($browser_preview)
	$tcpdf->Output($pdf_nme,'I');
	else		
	 $tcpdf->Output($pdf_nme, true); 
 }
 if($is_multi_print==1){	
	$tcpdf->Output(WRITEPATH."uploads/db_backup_temp/".$pdf_nme,'F');	
	return array("pdf_name"=>$pdf_nme);
 }
}  


/**********  Purchase Print With Item ***************/
  public function purchase_withitem_preview2($voucher_txn_id,$browser_preview=false,$show_labels='')
   {   ob_start();
   
       
       $company_logo =logo_src($this->company_id);
	   
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id); 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
	$pdf_nme  =time().'purchase.pdf';	
	$pdf='';
	
	if($party_city!='' && $party_pin!='')
	  $locationpin = '<br>'.$party_city.', '.$party_pin;
    else if($party_city!='' && $party_pin=='')
	  $locationpin = '<br>'.$party_city;
  else if($party_pin!='')
	  $locationpin = '<br>'.$party_pin;
	else
		$locationpin='';
	
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}

    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'&nbsp;&nbsp;'.$locationpin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'<br></p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'<br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$shipto_place.'</span><br />&nbsp;&nbsp;'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'</p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: '.$transport_enrollment_id.'<br/>&nbsp;&nbsp;Transport Mode: '.strtoupper($transport_veh_type).'<br />&nbsp;&nbsp;Vehicle No: '.$transport_veh_no .'</p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="170" height="22"  align="center">Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="187" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="60" height="22"  align="center">Qty</th>			 
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left">
				  <table width="500" cellspacing="5">
				  <tr><td width="100" align="left">Bank</td><td width="120" align="left"><span style="">'.$bank_name.'</span></td></tr>
				  <tr><td width="100" align="left">Account</td><td width="120" align="left"><span style="">'.$bank_account_name.'</span></td></tr>
				   <tr><td width="100" align="left">IFSC</td><td width="120" align="left"><span style="">'.$bank_ifsc_name.'</span></td></tr>
				    <tr><td width="100" align="left">Branch</td><td width="120" align="left"><span style="">'.$bank_branch_name.'</span></td></tr>
				  </table>
				  
				  </td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td>'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	
	
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
  
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	if(!empty(GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id))){
		$total_invoice_value_style =GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id).';font-weight:bold;';
	}else
	$total_invoice_value_style='font-size:11pt;font-weight:bold;font-family:fontsfreenetsegoeuib;';

	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="'.$total_invoice_value_style.'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2


 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
  
$tcpdf->lastPage();
  ob_end_clean();
if($browser_preview)
$tcpdf->Output($pdf_nme,'I');
else		
 $tcpdf->Output($pdf_nme, true);  
}  

public function purchase_withoutitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();     
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	   $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		$main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	   	
	$pdf_nme  =time().'purchase-invc-without-item.pdf';	
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
         '.$header_title.' 
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="4" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Details:
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>            
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Purchase');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
}  

 /************  Purchase Order *******************/
 /**********  Purchase Print With Item ***************/
  public function purchaseordr_withitem_preview2($voucher_txn_id,$browser_preview=false)
   {    ob_start();
   
       $pdf_nme  =time().'purchase-order-withitem.pdf';
       $company_logo =logo_src($this->company_id);
	   
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
	  $party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
	
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id); 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  if($party_transaction['acc_oth_txn_duedate'])
	  $data['due_date'] = date('d-m-Y', strtotime($party_transaction['acc_oth_txn_duedate']));
      else
	  $data['due_date'] = ''; 
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  
	  
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
		
	$pdf='';
	
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;">'.$data['due_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'<br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$shipto_place.'</span><br />&nbsp;&nbsp;'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'</p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: '.$transport_enrollment_id.'<br/>&nbsp;&nbsp;Transport Mode: '.strtoupper($transport_veh_type).'<br />&nbsp;&nbsp;Vehicle No: '.$transport_veh_no .'</p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="170" height="22"  align="center">Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="187" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="60" height="22"  align="center">Qty</th>			 
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td>'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
 	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2


 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
  
$tcpdf->lastPage();
  ob_end_clean();
 if($browser_preview)
$tcpdf->Output($pdf_nme,'I');
else		
 $tcpdf->Output($pdf_nme, true);   
}  

public function purchaseordr_withoutitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();     
      $pdf_nme  =time().'purchase-order-without-item.pdf';
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
	  $party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
	
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  if($party_transaction['acc_oth_txn_duedate'])
	  $data['due_date'] = date('d-m-Y', strtotime($party_transaction['acc_oth_txn_duedate']));
      else
	  $data['due_date'] = ''; 
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	   $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		$main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	   	
		
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;">'.$data['due_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="4" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Details:
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>            
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Purchase');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	
	
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //75

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true); 
}  
 
   
  /**********  Purchase Print With Item ***************/
  public function purchasereq_withitem_preview2($voucher_txn_id,$browser_preview=false)
   {    ob_start();
   
       $pdf_nme  =time().'purchasereq-order-withitem.pdf';
       $company_logo =logo_src($this->company_id);
	   
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
	  $party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
	
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id); 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  if($party_transaction['acc_oth_txn_duedate'])
	  $data['due_date'] = date('d-m-Y', strtotime($party_transaction['acc_oth_txn_duedate']));
      else
	  $data['due_date'] = ''; 
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  
	  
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
		
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'<br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$shipto_place.'</span><br />&nbsp;&nbsp;'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'</p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: '.$transport_enrollment_id.'<br/>&nbsp;&nbsp;Transport Mode: '.strtoupper($transport_veh_type).'<br />&nbsp;&nbsp;Vehicle No: '.$transport_veh_no .'</p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="170" height="22"  align="center">Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="187" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="60" height="22"  align="center">Qty</th>			 
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td>'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
 	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2


 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
  
$tcpdf->lastPage();
  ob_end_clean();
 if($browser_preview)
$tcpdf->Output($pdf_nme,'I');
else		
 $tcpdf->Output($pdf_nme, true);   
}  

public function purchasereq_withoutitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();     
      $pdf_nme  =time().'purchasereq-order-without-item.pdf';
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
	  $party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
	
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  if($party_transaction['acc_oth_txn_duedate'])
	  $data['due_date'] = date('d-m-Y', strtotime($party_transaction['acc_oth_txn_duedate']));
      else
	  $data['due_date'] = ''; 
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	   $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		$main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	   	
		
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
         '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="4" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Details:
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>            
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Purchase');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	
	
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //75
 
 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true); 
}  
 
   
   /**********  Credit Note Print With Item ***************/
   public function crnote_withitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();
   
       $pdf_nme  =time().'credit-note-with-item.pdf';
       $company_logo =logo_src($this->company_id);
	   
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id); 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
	
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
		
	$pdf='';
	$show_labels='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
         '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'<br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$shipto_place.'</span><br />&nbsp;&nbsp;'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'</p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: '.$transport_enrollment_id.'<br/>&nbsp;&nbsp;Transport Mode: '.strtoupper($transport_veh_type).'<br />&nbsp;&nbsp;Vehicle No: '.$transport_veh_no .'</p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="170" height="22"  align="center">Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="187" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="60" height="22"  align="center">Qty</th>			 
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Credit Note');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td>'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	
	
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
  
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2
 

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
  
$tcpdf->lastPage();
  ob_end_clean();
if($browser_preview)
$tcpdf->Output($pdf_nme,'I');
else		
 $tcpdf->Output($pdf_nme, true);  
}  

  public function crnote_withoutitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();   
	  $pdf_nme  =time().'credit-note-without-item.pdf';
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstrinwsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['inwsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['inwsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['inwsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name']  = $company_info['comp_name'];
      $data['state_name']    = $state_name;
	  $data['invoice_no']    = $outsup_bill_ref_no;
      $data['email']         = $company_info['corp_email'];
      $data['phone']         = $company_info['corp_mobile'];	  
      if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
      $data['cin']      = $company_info['cin'];
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';	
	
	  $gsttpt_id        = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	    $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		  $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	   	
	
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="4" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Details:
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>            
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Credit Note');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //75

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
}  
   
  /**********  Debit Note Print With Item ***************/
   public function drnote_withitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();
     $pdf_nme  =time().'debit-note-with-item.pdf';
       
       $company_logo =logo_src($this->company_id);
	   
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions($voucher_txn_id); 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
		
	$pdf='';

	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	

    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
         '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'<br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$shipto_place.'</span><br />&nbsp;&nbsp;'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'</p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: '.$transport_enrollment_id.'<br/>&nbsp;&nbsp;Transport Mode: '.strtoupper($transport_veh_type).'<br />&nbsp;&nbsp;Vehicle No: '.$transport_veh_no .'</p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="170" height="22"  align="center">Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="187" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="60" height="22"  align="center">Qty</th>			 
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Credit Note');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td>'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	
	
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
  
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2


 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
  
$tcpdf->lastPage();
  ob_end_clean();
if($browser_preview)
$tcpdf->Output($pdf_nme,'I');
else		
 $tcpdf->Output($pdf_nme, true);  
}  

  public function drnote_withoutitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start(); 
			$pdf_nme  =time().'debit-note-without-item.pdf';  
			
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	   $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		$main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	  	
		
	$pdf='';
	$show_labels='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="4" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Details:
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>            
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Credit Note');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //75

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
}  
   /**********  Sale Order Print Without Item ***************/
   public function saleordr_withoutitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();     
      $pdf_nme  =time().'sale-order-without-item.pdf';	
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id);
	  $party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
					
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  if($party_transaction['acc_oth_txn_duedate'])
	  $data['due_date'] = date('d-m-Y', strtotime($party_transaction['acc_oth_txn_duedate']));
      else
	  $data['due_date'] = '';  
				
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	   $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		$main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	   	
	
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;">'.$data['due_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="4" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Details:
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>            
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale Order');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //75

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
}  
   
   /**********  Quotation Order Print Without Item ***************/
   public function quotation_withoutitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();     
      $pdf_nme  =time().'quotation-order-without-item.pdf';	
      $company_logo =logo_src($this->company_id);
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }	
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';  
	  
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];		  
	  }   
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['account_transactions'] = $this->TransactionModel->get_account_transactions_oth($voucher_txn_id);
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  $state_name=$state_code=$statecode='';
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	 if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	 if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  $currency_id               = $voucher_info['currency_id'];
	  $currency_info             = $this->TransactionModel->get_currency_info($currency_id);
      if($currency_info){
		 $currency_symbol        = $currency_info['curr_symbol'];
	  }
      else{
	   $currency_symbol        =  '₹';
      }
	  
	  $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
		
      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';
      $data['tax_summary'] = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
      $data['get_fcrates']  = $this->TransactionModel->get_fcrates($voucher_txn_id);

	 if($currency_id>1){
		$hsn_summary_colspan=2;  
		$total_particular_colspan=3;
	   $main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">'.$currency_symbol.'</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	  
		  
	  }
	  else{
		  $hsn_summary_colspan=1; 
          $total_particular_colspan=2; 		  
		$main_header_table ='<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="20" height="22"    align="center">&nbsp;#</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="217" height="22"  align="center">Particulars</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="300" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="200" height="22"  align="center">Amount<span style="font-family:dejavuserif;">(</span><span style="font-family:dejavuserif;">&#8377;</span><span style="font-family:dejavuserif;">)</span></th>
			   </tr>';	    
	  }
	   	
	
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
	$header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
         '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="1" cellpadding="1" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="1" cellpadding="1" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>
				 <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td  style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td colspan="4" style="font-family:segoe_ui,arial;border-right:0.6px solid #000;border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Details:
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			'.$main_header_table.'
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
    
	  $footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>            
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
 
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale Order');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',true);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0);
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.='<table  cellpadding="5" style="border:0.5px solid #000;">';


$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess'])).'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }
 

foreach ($data['account_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['amount'];
	$tqty =$tqty+1;
	 if($currency_id>1){
	  $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.formatAmount($item_row['amount'],false).'</td>
			    <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="100"  align="right"><span style="font-family:dejavuserif;">'.$currency_symbol.'</span>'.formatAmount($item_row['amount']*$data['get_fcrates'],false).'</td>
		</tr>';	
		
	 }
	 else{
	 $inner_pdf.='<tr nobr="true">
			  <td style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="20" align="center">'.$trcounter.'</td>
	           <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="217"  align="center">'.$item_row['account_name'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="300"  align="center">'.$item_row['description'].'</td>
			   <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="200"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['amount'])).'</td>
			   
		</tr>';		 
		 
	 }
	 
    }
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']] ?? 'N/A';
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr>
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
		
			
	  }
  }
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="520" cellpadding="4"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TAX RATE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;display:none;">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
 $total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;bold;;">Total Particulars :'.$trcounter.'</td>
					<td></td>
					<td colspan="'.$total_particular_colspan.'" style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-right:0.6px solid #000;" align="right">
					<table width="100%" align="left">
				      <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="3" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="'.$hsn_summary_colspan.'" style="border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';
					
	 
	
	$inner_pdf .=' </table>';
	
$pdf .='<section class="sec">
<table width="100%"  style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
                </td>
            </tr> 
			
    </table>
</section>';
  
$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //75

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);

$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
for ($i = 0; $i < $cnt; $i++) {
	  $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	  $tcpdf->WriteHTMLCell(0,0,-2.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 
      $total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
     if(round($total_page_height)-round($tcpdf->GetY()) >0)
	 $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "", 1, 'L', 0, 0, '', '', true);
	 if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
		
     }
 }


$tcpdf->lastPage();
 ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
}  
   
  
   /**********  Sale Order Print With Item ***************/
  public function saleordr_withitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();
   
       $pdf_nme  =time().'sale-order-with-item.pdf';
       $company_logo =logo_src($this->company_id);
	   
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id);
	  $party_transaction = $this->TransactionModel->get_party_transaction_oth($voucher_txn_id);
		
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id); 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  if($party_transaction['acc_oth_txn_duedate'])
	  $data['due_date'] = date('d-m-Y', strtotime($party_transaction['acc_oth_txn_duedate']));
      else
	  $data['due_date'] = ''; 
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
		
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
         '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;">'.$data['due_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'<br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$shipto_place.'</span><br />&nbsp;&nbsp;'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'</p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: '.$transport_enrollment_id.'<br/>&nbsp;&nbsp;Transport Mode: '.strtoupper($transport_veh_type).'<br />&nbsp;&nbsp;Vehicle No: '.$transport_veh_no .'</p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="170" height="22"  align="center">Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="187" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="60" height="22"  align="center">Qty</th>			 
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td>'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	
	
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
  
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2

 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
  
$tcpdf->lastPage();
  ob_end_clean();
if($browser_preview)
$tcpdf->Output($pdf_nme,'I');
else		
 $tcpdf->Output($pdf_nme, true);  
}  

   /**********  Quotation Order Print With Item ***************/
  public function quotation_withitem_preview2($voucher_txn_id,$browser_preview=false)
   {   ob_start();
   
       $pdf_nme  =time().'quotation-with-item.pdf';
       $company_logo =logo_src($this->company_id);
	   
	  $bo_id = $this->session->get('ses_boid');	  
	  $get_party_info   = $this->TransactionModel->get_party_oth_info($voucher_txn_id);
	  $get_account_info = $this->TransactionModel->get_account_info($get_party_info['master_id']);
      $party_adrs_info  = $this->TransactionModel->get_account_adrs_info($get_party_info['master_id']);     
	  $bank_info        = $this->TransactionModel->get_bank_info($voucher_txn_id); 
      if($bank_info){
	  $bank_name           = $bank_info['comp_bank_name'];
	  $bank_account_name   = $bank_info['comp_bank_acc_no'];
	  $bank_ifsc_name      = $bank_info['comp_bank_ifsc'];
	  $bank_branch_name    = $bank_info['comp_bank_adrs'];
	  }else{
		$bank_name =$bank_account_name  = $bank_ifsc_name=$bank_branch_name=''; 
	  }
	  
	  
	  $party_gst_info = $this->TransactionModel->party_gst_info($get_party_info['master_id']);
      if( $party_gst_info)
	  $party_gstin   = $party_gst_info['acc_gstin'];
      else
		$party_gstin   = '';
	
	  $party_phone=$party_email=$party_name=$party_add1=$party_add2=$party_city=$party_pin=$party_state='';
	  if($get_account_info){
		  $party_phone = $get_account_info['acc_mobile'];
		  $party_name  = $get_account_info['acc_name'];
		  $party_email = $get_account_info['acc_email'];
	  }	 
	  
      if($party_adrs_info){
		  $party_add1    = $party_adrs_info['acc_add1'];
		  $party_add2    = $party_adrs_info['acc_add2'];
		  $party_city    = $party_adrs_info['acc_city'];
		  $party_pin     = $party_adrs_info['acc_pin'];
		  $party_state   = $party_adrs_info['acc_state'];
		  $party_country = $party_adrs_info['acc_country'];
	  }   
	  
	 
	  $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);
	  
	  //$comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->TransactionModel->get_cgstinmastr_info();
	  $data['item_transactions'] = $this->TransactionModel->get_item_transactions_oth($voucher_txn_id); 
	  $data['sundry_transactions'] = $this->TransactionModel->get_sundry_transactions_oth($voucher_txn_id, true);

	  $gstroutsup_info       = $this->TransactionModel->gstroutsup_info($voucher_txn_id);	
		
		if($gstroutsup_info){
			$outsup_pos = $gstroutsup_info['outsup_pos'];
			$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
			$outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
			
			$pos_state_info = $this->TransactionModel->pos_state_info(ltrim($outsup_pos,0));
		  
		  if($pos_state_info){
				 $state_name =$pos_state_info['state_name']; 
				 $state_code =$pos_state_info['state_code'];
				 $statecode  = sprintf( '%02d', $state_code);
			}
		
		}
		else{
			$outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$state_name=$statecode='';
		}
		
	  $get_ewbmstreqn_data = $this->TransactionModel->get_ewbmstreqn_data($voucher_txn_id);
	  $shipto_addr1      = '';
	  $shipto_addr2      = '';
	  $shipto_place      = '';
	  $shipto_pin        = '';
	  $shipto_state_code = '';
	  
	  $dispfrm_addr1 ='';
	  $dispfrm_addr2='';
	  $dispfrm_place='';
	  $dispfrm_pin ='';
	  $dispfrm_state_code='';
		  
	 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	  
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
	
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['state_name'] = $state_name;
	  $data['invoice_no'] = $outsup_bill_ref_no;
      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $comp_gst = '#'.$get_comp_taxt_info['comp_gstin'];
	  else
		  $comp_gst ='';
	  
      $data['comp_gst'] = $comp_gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
	 	$company_adrs2 = ''; 
	
	if(isset($comp_ro_address['bo_city']))
       $company_city = $comp_ro_address['bo_city'];
      else
	 	$company_city = '';
	
	if(isset($comp_ro_address['bo_pin']))
       $company_pin = $comp_ro_address['bo_pin'];
      else
	 	$company_pin = '';
	
	  $gsttpt_id = $this->TransactionModel->transport_single_info($voucher_txn_id);
	  $transporter_info = $this->TransactionModel->transporter_info($gsttpt_id);
	 
	  $transport_name = '';	  
	  $transport_veh_no = '';
	  $transport_veh_type = '';
	  $transport_gstin=''; 
	  $transport_enrollment_id = '';
	  
	  
	  if($transporter_info){
		$transport_name = $transporter_info['gsttpt_name'];	  
	    $transport_veh_no = $transporter_info['trans_veh_no'];
	    $transport_veh_type = $transporter_info['trans_veh_type'];
	    $transport_gstin=$transporter_info['gsttpt_gstin'];
	    $transport_enrollment_id = $transporter_info['gsttpt_enrl_id'];
	  }
	  
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	
      $data['company_city']      = $company_city;
      $data['company_pin']       = $company_pin;		  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));		
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $data['tax_summary']       = $this->TransactionModel->get_item_tax_oth($voucher_txn_id,0);
	  $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
		  
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
           $debit_transactions[] = $string; 
          }
      }	  
	  
    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
		
	$pdf='';
	if($show_labels!=''){
	 $header_title='<td colspan="3" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td><td style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;" align="right">'.$show_labels.'</td>';	
	}else{
	 $header_title ='<td colspan="4" style="border-bottom:0.5px solid #000;text-align:center;line-height:2.6;">TAX INVOICE</td>';	
	}
    $header_html ='<table  width="100%"  style="border-right:0.6px solid #000;border-left:0.5px solid #000;border-top:0.5px solid #000;border-bottom:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;">      
        <tr>
          '.$header_title.'
        </tr>
        <tr>
          <td colspan="2" rowspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">             
                <tr>
                  <td valign="middle" width="30%" align="left"><br /><br />
                    <img style="height:90px; width:auto;padding:0px;margin:0px;" src="'.$company_logo.'">
                  </td>
                  <td width="70%" valign="middle" style="font-family:segoe_ui,arial;">
                   <table width="100%" cellspacing="0" cellpadding="0" style="border-right:0.6px solid #000;">
					<tr><td colspan="2"><br /></td></tr>
					<tr><td colspan="2" style="font-family:fontsfreenetsegoeuib;font-weight:bold;font-size:18px;">'.ucwords(strtolower($data['company_name'])).'</td></tr>
					<tr><td colspan="2">GSTIN:&nbsp;'.$data['comp_gst'].'</td></tr>
					<tr><td colspan="2">'.$data['company_address'].'&nbsp;'.$data['company_address2'].'&nbsp;'.$data['company_city'].'&nbsp;'.$data['company_pin'].'</td></tr>
					<tr><td colspan="2">Phone:&nbsp;'.$data['phone'].'</td></tr>
					<tr><td colspan="2">Email:&nbsp;'.$data['email'].'</td></tr>					
				   </table>
                  </td>
                </tr>             
            </table>
          </td>
          <td>
            <table border="0" width="100%"  cellspacing="0" cellpadding="0" style="font-family:segoe_ui,arial;">
                <tr>
                  <td colspan="3">&nbsp;</td>				
                </tr>
				<tr>
                  <td width="40%">Date</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left">'.$data['voucher_date'].'</td>
                </tr>
                <tr>
                  <td width="40%">Due Date</td>
				  <td width="5%">:</td>
                  <td width="55%" style="text-align:left;"></td>
                </tr>
                <tr>
                  <td width="40%">Reference</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>               
                <tr>
                  <td width="40%">IRN</td>
				  <td width="5%">:</td>
                  <td width="55%" align="left"></td>
                </tr>             
            </table> 
          </td>
          <td align="center" style="border-left:0.5px solid #000;">
            
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;&nbsp;Place of Supply  :<br>
          <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.ucwords($data['state_name']).'</span> <br></td>
          <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;border-left:0.5px solid #000;border-bottom:0.5px solid #000;">&nbsp;Invoice No:<br>
            <span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">#'.$data['invoice_no'].'</span><br> 
          </td>
        </tr>
        <tr>
          <td style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Customer Details</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$party_name.'</span><br />&nbsp;&nbsp;'.$party_add1.' '.$party_add2.'<br>&nbsp;&nbsp;'.$party_city.', '.$party_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'<br>&nbsp;&nbsp;GSTIN: #'.$party_gstin.'</p>
		 </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Dispatch From</span> :
		  <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$dispfrm_place.'</span><br />&nbsp;&nbsp;'.$dispfrm_addr1.' '.$dispfrm_addr1.'&nbsp; '.$dispfrm_pin.'<br />&nbsp;&nbsp;<br /></p>
		  </td>
          <td align="left" style="font-weight:bold;border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Ship To</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$shipto_place.'</span><br />&nbsp;&nbsp;'.$shipto_addr1.' '.$shipto_addr2.'&nbsp; '.$shipto_pin.'<br />&nbsp;&nbsp;Phone: '.$party_phone.'<br />&nbsp;&nbsp;Email: '.$party_email.'</p>
		  </td>
          <td align="left" style="border-right:0.6px solid #000;border-top:0.5px solid #000;">&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Transporter Details</span> :
		   <p>&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">'.$transport_name.'</span><br />&nbsp;&nbsp;Transporter Id: '.$transport_enrollment_id.'<br/>&nbsp;&nbsp;Transport Mode: '.strtoupper($transport_veh_type).'<br />&nbsp;&nbsp;Vehicle No: '.$transport_veh_no .'</p>
		  </td>
        </tr>
		<tr>
		<td colspan="4"><table cellpadding="3" style="font-family:segoe_ui,arial;font-size:8.5pt;">
			<tr nobr="true">
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="40" height="22"  align="center">&nbsp;Sr.No.</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="170" height="22"  align="center">Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="187" height="22"  align="center">Description</th>
	           <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="90" height="22"  align="center">HSN/SAC</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="90" height="22"  align="right">Rate/Item</th>
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;" width="60" height="22"  align="center">Qty</th>			 
			   <th style="font-family:fontsfreenetsegoeuib;font-weight:bold;border-left:0.5px solid #000;border-top:0.5px solid #000;border-right:0.6px solid #000;"  width="100" height="22"  align="right">Amount</th>
			   </tr>
	  </table></td>
		</tr>
     
	 
	  
    </table>  ';
	
	
$footer_html = '<table width="737"  style="border:0.5px solid #000;font-family:segoe_ui,arial;font-size:8.5pt;"> 
        
		<tr>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" colaspan="2" width="40%;">
            
            <table border="0"  width="100%" cellspacing="2" cellpadding="2"> 
				<tr><td colspan="2" style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Bank Details :</td></tr>            
                <tr>
                  <td align="left"><p>Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_name.'<br />Account&nbsp;:&nbsp;'.$bank_account_name.'<br />IFSC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_ifsc_name.' <br />Branch&nbsp;&nbsp;&nbsp;:&nbsp;'.$bank_branch_name.' </p></td>
                 
                </tr>                             
            </table>
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="12%">
            <p style="font-size:11px; line-height:16px;">&nbsp;E-way QR Code:</p>
             
          </td>
          <td style="border-right:0.6px solid #000;border-bottom:0.5px solid #000;" width="13%">
            <p style="font-size:11px; line-height:16px;">&nbsp;Pay using UPI:</p>
            
          </td>
          <td style="border-bottom:0.5px solid #000;" width="35%" style="text-align:right;">
            <strong style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;text-align:right;">For '.ucwords(strtolower($data['company_name'])).'</strong>&nbsp;
         
          <p><br><br> &nbsp;&nbsp;</p>
          <p><br><br>Authorised Signatory  &nbsp;&nbsp;</p>
        </td>
      </tr>
      <tr>
        <td style="border-right:0.6px solid #000;" colaspan="2" width="40%">
          <span style="font-size:10pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">Terms &amp; Conditions:</span>
          <p><ol style="list-style-type: none;font-size:12px; line-height:16px;">
            <li style="margin: 10px 0;">We declare that this invoice shows the actual price of 
the goods & services described and that all particulars are true 
and correct.</li>
          </ol></p>
        </td>
        <td style="border-right:0.6px solid #000;" colspan="2" width="25%" valign="top">
          <p style="font-size:11px;font-family:fontsfreenetsegoeuib;font-weight:bold;">&nbsp;Customer Sign:</p>		  
         <br>
        </td>
        <td style="border-top:0.5px solid #000;border-right:0.6px solid #000;" width="35%" valign="top">
		<p><br /><br /><br /><br /></p>
          <p style="font-size:11px;fontsfreenetsegoeuib;font-weight:bold;">&nbsp;&nbsp;<span style="font-family:fontsfreenetsegoeuib;font-weight:bold;">Notes:</span><br>&nbsp;&nbsp;Thankyou for dealing with our company.</p>
        <br>
		</td>
      </tr>     
    </table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Sale');
$tcpdf->SetHeaderMargin(1,0,0,0);
$tcpdf->SetFooterMargin(79,0,0,0);
$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);
$tcpdf->SetAutoPageBreak(TRUE, 79);
$tcpdf->SetDisplayMode(100);
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
$tcpdf->SetFont('fontsfreenetsegoeuib', '', '','',false);
 // use the font
$tcpdf->SetMargins(1,0,1,0); //Left,Top,Right,$keepmargins 0,1
$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';

$inner_pdf.='<table  cellpadding="2" style="border:0.5px solid #000;">';

$trcounter=0;
$tqty=0;
$taxable_amount=0;
$bsd_inner='';

$bo_state_code    =  $this->session->get('ses_bostecd');
$ugst_states      = ['35','04','26','25','31','38','34','97']; // unit territory states for ugst tax 
$tax_summary_inner='';
$sgst_ugst_label='SGST';
$hide_igst_val=0;
$hide_cgst=0;
$hide_sgst=0;
if($data['tax_summary']){
	foreach($data['tax_summary'] as $ks => $tax_row){
		
		if($statecode==$bo_state_code && in_array($statecode,$ugst_states)){
			$sgst_ugst_label='UGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode==$bo_state_code && !in_array($statecode,$ugst_states)){
			$sgst_ugst_label='SGST';
			$hide_igst_val=1;
			$hide_cgst=0;
			$hide_sgst=0;
		}
		elseif($statecode!=$bo_state_code){
			$hide_igst_val=0;
			$hide_cgst=1;
			$hide_sgst=1;
			
		}
		if($hide_cgst==1)
			$cgst_tax_='';
		else
			$cgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cgst']));
		
		if($hide_sgst==1)
			$sgst_tax_='';
		else
			$sgst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['sgst']));
		
		
		if($hide_igst_val==1)
			$igst_tax_='';
		else
			$igst_tax_='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['igst']));
		
		if($tax_row['cess']>0)
			$cess_tax='<span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['cess']));
		else
			$cess_tax='';
		$tax_summary_inner.='<tr nobr="true">
		                     <td>'.$tax_row['tax_hsn_sac'].'</td>
							 <td>'.$tax_row['tax_rate'].'%</td>
							 <td style="display:none;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['tax_amt'])).'</td>
							 <td>'.$igst_tax_.'</td>
							 <td>'.$cgst_tax_.'</td>
							 <td>'.$sgst_tax_.'</td>
							 <td>'.$cess_tax.'</td>
							 <td><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($tax_row['total_tax'])).'</td>							 
							 </tr>';
	  }
	
   }

foreach ($data['item_transactions'] as $key => $item_row){
	$trcounter++;
	$taxable_amount=$taxable_amount+$item_row['item_amount'];
	$tqty =$tqty+$item_row['item_qty'];
	
	  $inner_pdf.='<tr nobr="true">
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="40" align="left">'.$trcounter.'</td>
	           <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="170"  align="left">&nbsp;'.$item_row['item_name'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="187"  align="left">&nbsp;'.$item_row['description'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="center">&nbsp;'.$item_row['item_hsn'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="90" align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_price'])).'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="60" align="center">'.$item_row['item_qty'].'</td>
			   <td style="border-left:0.5px solid #000;border-right:0.6px solid #000;border-bottom:none;border-top:none;" width="100"  align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($item_row['item_amount'])).'</td>
		</tr>';	
    }
	
	
	
	
	$total_bsd_tax_amount=0;
	foreach($data['sundry_transactions'] as $kk => $bsd_row){
	$bsd_array      = $this->bsdnatures;
	$bl_nature_name = $bsd_array[$bsd_row['bl_nature']];
	
	if($bsd_row['billsundry_amount']){
		$total_bsd_tax_amount=$total_bsd_tax_amount+$bsd_row['billsundry_amount'];
		$bsd_inner .='<tr nobr="true">
				<td align="left">
				 <table width="100%" align="right">
				 <tr><td align="left;"><span style="font-family:dejavuserif;">'.$bl_nature_name.'</span>:</td><td align="right;" style="font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount(abs($bsd_row['billsundry_amount']))).'</td>
			     </tr>
				 </table>
				</td>
            </tr>';
			
	  }
  }
  
	
	$total_amount_with_tax = $total_bsd_tax_amount+$taxable_amount;
	$total_amount_with_tax_words = getIndianCurrency(parseAmount($total_amount_with_tax)); 
    $hsnsummary_table ='<table width="100%"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;">HSN Summary<br></td></tr>
<tr><td><table width="530" cellpadding="2"><tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">HSN/SAC</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TAX RATE</td><td style="display:none;font-family:fontsfreenetsegoeuib;font-weight:bold">TAXABLE VALUE</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">IGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CGST</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">'.$sgst_ugst_label.'</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">CESS</td><td style="font-family:fontsfreenetsegoeuib;font-weight:bold">TOTAL TAX</td></tr>'.$tax_summary_inner.'</table></td></tr></table>';
$total_amount_with_words='<table width="100%"><tr><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;">TOTAL</td><td align="left" style="font-size:11pt;font-family:fontsfreenetsegoeuib;font-weight:bold;"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($total_amount_with_tax)).'</td></tr><tr><td colspan="2" align="left"><br><br>'.$total_amount_with_tax_words.'</td></tr></table>';
	

	$inner_pdf.='<tr nobr="true">
					<td colspan="2" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" colspan="2">Total Items / Qty :'.$trcounter.' / '.$tqty.'</td>
					<td colspan="2" style="border-top:0.5px solid #000;">&nbsp;</td>
					<td colspan="3" style="border-top:0.5px solid #000;font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">
					<table width="100%" align="left">
				    <tr><td style="font-family:fontsfreenetsegoeuib;font-weight:bold;" align="left">Taxable Amount</td><td align="right"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',formatAmount($taxable_amount)).'</td></tr>
				    </table></td>					
					</tr>
					<tr>
					<td colspan="4" style="border-top:0.5px solid #000;border-right:0.6px solid #000;">'.$hsnsummary_table.'</td>
					<td colspan="3" style="border-top:0.5px solid #000;border-right:0.6px solid #000;" align="left">
					<table width="100%" cellspacing="5">'.$bsd_inner.'<tr><td></td></tr>
					<tr><td>'.$total_amount_with_words.'</td></tr>
					</table>
					</td>					
					</tr>';	
	
	$inner_pdf .=' </table>';
	
	
  
	$dimensions = [//PDF_UNIT defaults to millimeters
        "margins"   => $tcpdf->GetMargins(),
        "width"     => $tcpdf->getPageWidth(),
        "height"    => $tcpdf->getPageHeight(),
		"hh"        => $tcpdf->getCellHeight('8.75')
    ];
	
$headerfont = $tcpdf->getHeaderFont();
$kk = $tcpdf->getScaleFactor();
$cell_height = $tcpdf->getCellHeight($headerfont[2] / $kk);
	
$pdf .='<section class="sec">
<table width="100%" style="font-family:segoe_ui,arial;font-size:8.5pt;">      
            <tr>
			    <td>
                  '.$inner_pdf.'
				  
                </td>
            </tr>	
			
    </table>
</section>';

$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //74.2


 $final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {
	   $tcpdf->SetMargins(1,$tcpdf->GetY()+0.4,1,0);
	   $tcpdf->WriteHTMLCell(0,0,-2.7, $tcpdf->GetY()+1,$delimiter.$final_chunks[$i], 0, 1,'',0,'',1);
	// $cell_height.'=='.$tcpdf->getLastH().'==>'.$dimensions['height'].'>>'.$dimensions['hh'].'--'.($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
	//echo '<br>last height '.   $last_height =$tcpdf->getLastH();
	//echo '<br>total page height '.$dimensions['height'];
	//echo '<br>getScaleFactor '.$tcpdf->getScaleFactor();
	//echo '<br>getFooterMargin '.$tcpdf->getFooterMargin();
	//echo '<br>getHeaderMargin '.$tcpdf->getHeaderMargin();
	//echo '<br>getY '.$tcpdf->GetY();
	//echo '<br>getY '.$delimiter.$final_chunks[$i];
	$total_page_height=$dimensions['height']-($dimensions["margins"]["top"]+$dimensions["margins"]["bottom"]);
   //echo '<br>height after remove header foter margin'.$total_page_height;	
 
	 if((round($total_page_height)-round($tcpdf->GetY())) >0)
	  $tcpdf->MultiCell(208,($total_page_height-$tcpdf->GetY()), "".$cell_height, 1, 'L', 0, 0, '', '', true);
     if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');
      }
	  
	  
 }  
  
$tcpdf->lastPage();
  ob_end_clean();
if($browser_preview)
$tcpdf->Output($pdf_nme,'I');
else		
 $tcpdf->Output($pdf_nme, true);  
}  

   
/************************              ************/
function toPixels($value){
            //pt=point=0.352777778 mm, mm=millimeter=2.8346456675057350125948125904915 points, cm=centimeter=28.346456675057350125948125904915 points, in=inch=72 points=25.4mm
            switch(PDF_UNIT){//http://www.unitconversion.org/unit_converter/typography.html
                case "pt": return $value * 1.328352013;
                case "mm": return $value * 3.779527559;
                case "in": return $value * 96;
                case "cm": return $value * 37.795275591;
            }
            return "TEST";
        }
		
  public function sale_receipt_preview2($voucher_txn_id,$browser_preview=false)
  {   ob_start();
      
	   if($this->session->get('ses_boid')!='')
			$bo_id = $this->session->get('ses_boid');
		else 
		   $bo_id =1; 
	   
	   $comp_ro_address = $this->CommonModel->get_company_address_info($this->company_id,'1');
	  $get_comp_taxt_info = $this->CommonModel->get_comp_taxt_info($this->company_id,$bo_id);
	 
	 
	 
      $prntconfig_id         = $this->TransactionModel->get_print_config_info(18,13);	  
	  $data['prntconfig_id'] = $prntconfig_id;
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];

      $data['email'] = $company_info['corp_email'];
      $data['phone'] = $company_info['corp_mobile'];
	  
   if(isset($get_comp_taxt_info['comp_gstin']))
          $gst = $get_comp_taxt_info['comp_gstin'];
	  else
		  $gst ='';
	  
      $data['gst'] = $gst;
	  
     
      $data['cin'] = $company_info['cin'];
	  
	  
	  if(isset($comp_ro_address['comp_addr1']))
       $company_adrs1 = $comp_ro_address['comp_addr1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['comp_addr2']))
       $company_adrs2 = $comp_ro_address['comp_addr2'];
      else
		$company_adrs2 = ''; 
	
      $data['company_address']  = $company_adrs1;
      $data['company_address2']  = $company_adrs2;
	  
      $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));

      $get_voucher_series = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

      $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';

      $account_transactions = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions = [];
      $debit_transactions = [];
      $amount = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $credit_transactions[] = [
                'party' => $string,
                'amount' => formatAmount($value['credit']),
              ]; 
          }
          if($value['debit'] != ''){
              $string = $value['account_name'];

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }

              $debit_transactions[] = $string; 
          }
      }
	

    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions'] = implode(', ', $debit_transactions);

    $data['amount'] = formatAmount($amount, false);
    $data['amount_words'] = getIndianCurrency(parseAmount($amount)); 
	
	$pdf_nme  ='receipt.pdf';
	
	$pdf='';

  $header_html ='<table  width="100%;" style="width:100%;  position:relative;">
       <tr>
           <td colspan="2">
		   <table width="100%;" border="0"  style="text-align:center; border-collapse:collapse;  width:100%; color:#000;   font-size:8.5pt;">
			   <tr>
				   <td style="border-top:8px solid #0fae2c;"></td>
				   <td style="border-top:8px solid #1d5182; position:relative;"></td>
			   </tr>
	   </table>
	   </td>
       </tr>    
	   <tr>
	   <td>
	   <img style="height:70px; width:auto;padding:0px;margin:0px;" src="images/logo.png"><p style="padding:0px;margin-top:0px;margin-bottom:0px;"><span style="'.GetPrintLabelVal($prntconfig_id,9).'">'.$data['company_name'].'</span><br><br><span style="'.GetPrintLabelVal($prntconfig_id,34).'">'.$data['company_address'].'</span> <br><span style="font-size:9pt;">Phone:</span> <span style="'.GetPrintLabelVal($prntconfig_id,24).'">'.$data['phone'].'</span> <br><span style="font-size:9pt;">Email:</span> <span style="'.GetPrintLabelVal($prntconfig_id,25).'">'.$data['email'].'</span><br></p>
	   </td> 
	     <td style="padding:0 .3cm; text-align:right;">
	       <p style="margin:0px; padding:0px;'.GetPrintLabelVal($prntconfig_id,8).'">'.GetPrintLabelVal($prntconfig_id,8,1).'</p><br>
	        <h4 style="margin:0; padding:0;"><span style="'.GetPrintLabelVal($prntconfig_id,28).'">#'.$data['voucher_no'].'</span> </h4>
	        <p style="margin:0; padding:0;"><span style="padding-left:12px;'.GetPrintLabelVal($prntconfig_id,11).'">Dated No.</span> : <span style="'.GetPrintLabelVal($prntconfig_id,29).'">'.$data['voucher_date'].'</span> </p>
	   </td> 
	  </tr>
	  <tr>
	      <td colspan="2" background-color:#000000;><table cellpadding="6" border="0" width="100%" style="color:#1d5182; font-size:9.2pt; font-weight:bold; border-top:1px solid #000;">
	       <tr>
	           <td height="22">
			   <span style="'.GetPrintLabelVal($prntconfig_id,6).'">GST</span> : <span style="'.GetPrintLabelVal($prntconfig_id,26).'">'.$data['gst'].'</span></td>
	           <td height="22"  style="text-align:right;">
			   <span style="'.GetPrintLabelVal($prntconfig_id,7).'">CIN</span> : <span style="'.GetPrintLabelVal($prntconfig_id,27).'">'.$data['cin'].'</span></td>
	       </tr>
	      
	   </table></td>
	  </tr>
	  
	  <tr>
	      <td colspan="2" background-color:#000000;><table border="0"  cellpadding="7" width="100%" style="font-weight:bold; background-color: #000000; color:#fff;font-size:9.2pt; font-weight:bold;">
	       <tr>
	           <td height="22"  width="60%"  align="left">&nbsp;<span style="'.GetPrintLabelVal($prntconfig_id,12).'">Party</span></td>
	           <td height="22"  width="20%"  align="center"><span style="'.GetPrintLabelVal($prntconfig_id,14).'">Place of Supply</span></td>
	           <td height="22"  width="20%"  align="right"><span style="'.GetPrintLabelVal($prntconfig_id,16).'">Amount</span></td>
	       </tr>
	   </table></td>
	  </tr>	  </table>
	   ';
	  
 $footer_html = '<table border="0" width="100%" style="font-size:8.5pt;"><tr>
                <td>
                    <table style="margin:0px 0px 0px 1%; width:100%; border-top:1px solid #000; font-size:8.5pt;">
	       <tbody><tr>
	       <td colspan="2" style="padding:.1cm .3cm;">
	           <p>Mode : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="padding-left:29px;'.GetPrintLabelVal($prntconfig_id,17).'">'.$data['debit_transactions'].'</span><br>Remarks : <span style="padding-left:8px;'.GetPrintLabelVal($prntconfig_id,18).'">'.$data['narration'].'</span></p>
	       </td>
	       </tr>
	       <tr><td>&nbsp;<br></td></tr>
	       <tr style="padding-top:30px;">
	           <td style="padding-bottom:20px;">
			   <p>
			   <span style="'.GetPrintLabelVal($prntconfig_id,36).'"><span style="font-family:dejavuserif;">&#8377;</span>'.strip_tags($data['amount']).'</span>
			   <br><span style="'.GetPrintLabelVal($prntconfig_id,20).'">'.$data['amount_words'].'</span>
			   <br><small>(Cheque Subject to Realisation)</small>
			   
			   </p>
	    </td>
	   <td> <p style="text-align:right; padding-right:8px;"><strong style="text-align:right;">For '.$data['company_name'].'</strong></p>
	  <p style="text-align:right; padding-top:50px;"><strong style="text-align:right;">Authorised Signatory</strong></p>
	  </td>
	       </tr>
		   <tr><td  colspan="2"> </td></tr>
		   <tr><td style="border-top:3px solid #1d5182;" colspan="2">&nbsp;<br></td></tr>
	   </tbody></table>
                </td>
            </tr></table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Receipt');
$tcpdf->SetHeaderMargin(4.5,0,0,0);
$tcpdf->SetFooterMargin(63,0,0,0);


$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);

$tcpdf->SetAutoPageBreak(TRUE, 63);
$tcpdf->SetDisplayMode(90);

$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
 // use the font

$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.=' <table border="0"  width="99%" cellpadding="5" align="left" style="font-size:8.5pt;">';
$trcounter=0;
foreach ($credit_transactions as $key => $credit_transaction){
	$trcounter++;
	
	if($trcounter%2==0){
	$inner_pdf.='<tr style="background-color:#f3f3f3;">
	           <td style="border-right:1px solid #000;" width="55%"><p style="'.GetPrintLabelVal($prntconfig_id,30).'">'.$credit_transaction['party'].'</p></td>
	           <td style="border-right:1px solid #000;"  width="21.3%"><p style="'.GetPrintLabelVal($prntconfig_id,32).'"></p></td>
	           <td width="20%" align="right"><p style="'.GetPrintLabelVal($prntconfig_id,37).'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',$credit_transaction['amount']).'</p></td>
			</tr>';

	} else{
	  $inner_pdf.='<tr>
	           <td style="border-right:1px solid #000;" width="55%"><p style="'.GetPrintLabelVal($prntconfig_id,30).'">'.$credit_transaction['party'].'</p></td>
	           <td style="border-right:1px solid #000;"  width="21.3%"><p style="'.GetPrintLabelVal($prntconfig_id,32).'"></p></td>
	           <td width="20%" align="right"><p style="'.GetPrintLabelVal($prntconfig_id,37).'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',$credit_transaction['amount']).'</p></td>
			</tr>';	
	}		
			
    }
	$inner_pdf .=' </table>';
	
	
	
$pdf .='<section class="sec"><table border="0" width="100%" cellpadding="1" style="font-size:8.5pt;">
        <tbody>
            <tr>
			    <td align="left">
                  '.$inner_pdf.'
                </td>
            </tr>				
			<tr>
	      <td> <table  border="0" width="95%" style="border-top:1px solid #000;">
	              <tr> <td colspan="3"></td></tr>
		      </table>
		   </td>
	      </tr>	
	  	<tr>
	      <td> <table  border="0" width="95%" >
	              <tr> 
				  <td width="60%" style="font-weight:bold;">Total:</td>
				  <td width="20%"></td>
				  <td width="18%" align="right" style="'.GetPrintLabelVal($prntconfig_id,36).'"><span style="font-family:dejavuserif;">&#8377;</span>'.$data['amount'].'</td>
				  </tr>
		      </table>
		   </td>
	      </tr>
		<tr>
	      <td> <table  border="0" width="97%" >
	              <tr> 
				  <td width="60%" style="border-bottom:1px solid #000;"></td>
				  <td width="20%" style="border-bottom:1px solid #000;"></td>
				  <td width="18%" style="border-bottom:1px solid #000;"></td>
				  </tr>
		      </table>
		   </td>
	      </tr>		
        </tbody>
    </table>
</section>';


 $delimiter = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //81

$final_chunks=array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {	
	    $tcpdf->SetMargins(-4, $tcpdf->GetY(), 0, 0);		
	   $tcpdf->WriteHTMLCell(0, 0, 7.5, $tcpdf->GetY(),$delimiter . $final_chunks[$i], 0, 1);	 	
	
    if ($i < $cnt - 1) {       
        $tcpdf->AddPage('P', 'A4');		
    }
}

$tcpdf->lastPage();
ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
}
   
   public function print_receipt($voucher_txn_id){
       	//266 config id for receipt   
	   $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
	   
		if($voucher_info)
		$voucher_series_id =$voucher_info['comp_vch_series_id']; 
	    else
        die('N/A');			
		$get_business_id       = $this->CommonModel->get_business_id();
	    $defaultPrint_template = $this->TransactionModel->GetDefaultPrintingTemplate(266);
	    if($defaultPrint_template==100){ 
		 $config_codes_list = array(6=>96,26=>97,7=>98,27=>99,8=>103,
			                           9=>101,34=>102,10=>267,28=>268,11=>105,29=>106,12=>107,30=>108,
									   13=>109,31=>110,14=>111,32=>112,15=>113,33=>114,16=>115,37=>116,17=>117,18=>118,19=>119,36=>120,20=>121,21=>301
									   );
		  if(isset($_GET['p']) && $_GET['p']==1) 
				 $this->traditional_receipt_preview($voucher_series_id,$voucher_txn_id,$config_codes_list,true);  
		  else 
               $this->traditional_receipt_preview($voucher_series_id,$voucher_txn_id,$config_codes_list); 			  
	   }if($defaultPrint_template==126){
		   $config_codes_list = array(6=>122,26=>123,7=>124,27=>125,8=>269,
			                           9=>127,34=>128,28=>129,11=>130,29=>131,12=>132,30=>133,
									   13=>134,31=>135,14=>136,32=>137,15=>138,33=>139,16=>140,37=>141,17=>142,18=>143,19=>144,36=>145,20=>146,24=>147,25=>148,21=>302
									   );
		   if(isset($_GET['p']) && $_GET['p']==1) 
				 $this->modern_receipt_preview($voucher_series_id,$voucher_txn_id,$config_codes_list,true);  
		  else 
              $this->modern_receipt_preview($voucher_series_id,$voucher_txn_id,$config_codes_list); 		  
	   }
	   
	 die();  
   }
   
  public function traditional_receipt_preview($voucher_series_id,$voucher_txn_id,$config_codes_list,$browser_preview=false)
   {

  	 // ob_start();
	  $bo_id = $this->session->get('ses_boid');	 
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);	
      $company_info = $this->CommonModel->get_company_info($this->company_id);  
      $comp_ro_address = $this->CommonModel->get_comp_ho_adrs_info($bo_id);	  
	  $branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($bo_id);	  
	 
	 $get_comp_taxt_info = $this->CommonModel->get_comp_taxt_info($this->company_id,$bo_id);
	 
	  $data['company_name'] = $company_info['comp_name'];
      $data['email']        = $company_info['corp_email'];
      $data['phone']        = $company_info['corp_mobile'];
       if(isset($branch_gstin_info['comp_gstin']))
          $gst = $branch_gstin_info['comp_gstin'];
	  else
		  $gst ='';
	  
      $data['gst'] = $gst;
      $data['cin'] = $company_info['cin'];
	  
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
		$company_adrs2 = ''; 
	
     
      $data['company_address']  = $company_adrs1;
	  $data['company_address2']  = $company_adrs2;

      $voucher_info = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));

      $get_voucher_series     = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series'] = $get_voucher_series['comp_vch_series'] ?? '';

       $narration = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration'] = $narration ?? '';

      $account_transactions = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions  = [];
      $debit_transactions   = [];
      $amount               = 0;

      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small> (${short_narration})</small>";
              }

              $credit_transactions[] = $string; 
          }
          if($value['debit'] != ''){
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small> (${short_narration})</small>";
              }

              $debit_transactions[] = $string; 
          }
      }

      $data['credit_transactions'] = $credit_transactions;
      $data['debit_transactions'] = implode(', ', $debit_transactions);
      $data['amount'] = formatAmount($amount, false);
      $data['amount_words'] = getIndianCurrency(parseAmount($amount));	
      $pdf_nme  ='receipt.pdf';
	  $pdf      ='';
	
	  $inner_table='';
	  foreach ($credit_transactions as $key => $credit_transaction){
		if($credit_transaction!=''){
		if($key==0){
		    $inner_table .='<tr>
                        <td align="left" width="20%"><span style="'.GetPrintLabelVal($config_codes_list[12],12,$voucher_series_id).'">Party</span>&nbsp;:</td>
                        <td align="left" width="80%"><span style="'.GetPrintLabelVal($config_codes_list[30],30,$voucher_series_id).'">'.$credit_transaction.'</span></td>
                    </tr>';	
		}
		else{
		  $inner_table .='<tr>
                        <td align="left" width="20%"></td>
                        <td align="left" width="80%"><span style="'.GetPrintLabelVal($config_codes_list[30],30,$voucher_series_id).'">'.$credit_transaction.'</span></td>
                    </tr>';	
		   }
		}	
	}	
	
	if(GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1))
	  $receipt_title =GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1);
	 else
	 $receipt_title ='R E C E I P T';	 
	
   	$pdf  .='<section class="sec">
	<table border="1" cellpadding="6" style="border:1px solid #000; padding:6px;" width="97%" style="font-size: 10pt;">
    <tbody>
        <tr>
            <td colspan="2">
			<table width="100%" cellpadding="6">
			<tr>
			<td align="left">&nbsp;&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[6],6,$voucher_series_id).'">GST</span> :<span style="'.GetPrintLabelVal($config_codes_list[26],26,$voucher_series_id).'">'.$data['gst'].'</span></td>
			<td align="right"><span style="'.GetPrintLabelVal($config_codes_list[7],7,$voucher_series_id).'">CIN</span> : <span style="'.GetPrintLabelVal($config_codes_list[27],27,$voucher_series_id).'"> '.$data['cin'].'</span>&nbsp;&nbsp;</td>
			</tr>
			</table>
                <p style="text-align:center;">
                   <span style="'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$receipt_title.'</span><br>                
                <span style="line-height:42px;text-align:center; '.GetPrintLabelVal($config_codes_list[9],9,$voucher_series_id).'">
                '.$data['company_name'].'
                </span><br>
                <span style="text-align:center; padding:0 0 8px 0; '.GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id).'">
                    '.$data['company_address'].' 
					</span><br>
				<span style="text-align:center; padding:0 0 8px 0; '.GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id).'">
                    '.$data['company_address2'].' 
					</span><br>	
                </p>
            </td>
        </tr>
        <tr>
		<td border="1">
		<table   width="100%" style="width:100%;  font-size: 10pt;">
		<tr>
		 <td valign="top" width="50%;" align="left"><span style="'.GetPrintLabelVal($config_codes_list[10],10,$voucher_series_id).'">Receipt No.</span> : <span style="'.GetPrintLabelVal($config_codes_list[28],28,$voucher_txn_id).'">'.$data['voucher_no'].'</span></td> 		 
		</tr>
		</table>
		</td>
		<td border="1">
		<table  width="100%" style="width:100%;  font-size: 10pt;">
		<tr>
            <td valign="top" width="50%;" align="left"><span style="'.GetPrintLabelVal($config_codes_list[11],11,$voucher_series_id).'">Dated</span> : <span style="'.GetPrintLabelVal($config_codes_list[29],29,$voucher_txn_id).'">'.$data['voucher_date'].'</span></td>
		</tr>
		</table>
		</td>
		</tr>
      <tr>
	<td border="1" valign="top" width="50%;" align="left">
	<table width="100%" style="width:100%;  font-size: 10pt;">
	'.$inner_table.'	
	</table>    </td>
	<td border="1" valign="top" width="50%;" align="left">
	<table cellpadding="6"  width="100%" style="width:100%;  font-size: 10pt;">
	<tr><td><span style="'.GetPrintLabelVal($config_codes_list[13],13,$voucher_series_id).'">Party identification No</span></td><td>:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[31],31,$voucher_series_id).'"></span></td></tr>
	<tr><td><span style="'.GetPrintLabelVal($config_codes_list[14],14,$voucher_series_id).'">Place of Supply</span></td><td>:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[32],32,$voucher_series_id).'"></span></td></tr>
	<tr><td><span style="'.GetPrintLabelVal($config_codes_list[15],15,$voucher_series_id).'">Series</span></td><td>:&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[33],33,$voucher_series_id).'">'.$data['voucher_series'].'</span></td></tr>
	<tr><td><span style="'.GetPrintLabelVal($config_codes_list[16],16,$voucher_series_id).'">Amount</span></td><td>:&nbsp;<span style="font-family:dejavuserif;">&#8377;</span><span style="'.GetPrintLabelVal($config_codes_list[37],37,$voucher_series_id).'">'.$data['amount'].'</span></td></tr>
	</table>
	</td>
	</tr>   
		</tbody>
	</table>
	<table border="1" cellpadding="6" style="border-collapse: collapse;" width="97%" style=" font-size: 10pt;"><tr>
            <td colspan="2">Mode : <span style="padding-left:29px;'.GetPrintLabelVal($config_codes_list[17],17,$voucher_series_id).'">
                    '.$data['debit_transactions'].'
                </span>
            </td>
        </tr>
        <tr>
           <td colspan="2">Remarks : <span style="padding-left:8px;'.GetPrintLabelVal($config_codes_list[18],18,$voucher_series_id).'">'.$data['narration'].'</span></td>
        </tr>
        <tr>
            <td colspan="2">
			<table cellpadding="6">
			<tr>
			<td valign="top"><span style="font-family:dejavuserif;">&#8377;</span><span style="padding-left:8px;'.GetPrintLabelVal($config_codes_list[36],36,$voucher_series_id).'">'.$data['amount'].'</span>
                <p style="'.GetPrintLabelVal($config_codes_list[20],20,$voucher_series_id).'">'.$data['amount_words'].'</p></td>
			<td align="right"> <span style="text-align:right;'.GetPrintLabelVal($config_codes_list[21],21,$voucher_series_id).'">FOR '.$data['company_name'].'</span></td>
			</tr>
			</table>            
                <p>
                    <small>(Cheque Subject to Realisation)</small>
                </p>
                <p style="text-align:right;">
                    <strong style="text-align:right;'.GetPrintLabelVal($config_codes_list[19],19,$voucher_series_id).'">Authorised Signatory</strong>
                </p>
            </td>
        </tr></table>';	
	$pdf.='</section>';	
	
	$tcpdf->SetTitle('Receipt');
	$tcpdf->SetHeaderMargin(0,0,0,0);
	$tcpdf->SetFooterMargin(0,0,0,0);
	$tcpdf->setPrintHeader(false);
	$tcpdf->setPrintFooter(false);
	$tcpdf->setListIndentWidth(3);
	$header_html='';
	$footer_html='';
	$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
	$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);	 
	$tcpdf->SetAutoPageBreak(TRUE,3);
	$tcpdf->SetDisplayMode(90);
	$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$tcpdf->SetFont('segoe_ui', '', '8.5pt');
	$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
    $tcpdf->SetFont('arial', '', '','',false);
	$tcpdf->SetFont('verdana', '', '','',false);
	$tcpdf->SetFont('times_new_roman', '', '','',false);
	$tcpdf->AddPage('P', 'A4'); 
	$delimiter = '<section class="sec">';
	$chunks = explode($delimiter, $pdf);
	 // if to show dotted line use code
	//$style = array('width' => 0.5, 'cap' => 'round', 'dash' => '2,2,2,2', 'phase' => 0, 'color' => array(0, 0, 0));
	$tcpdf->SetY(85); 
	$final_chunks=array();
	foreach($chunks as $dd){
		if($dd!='')
		$final_chunks[]=$dd;
	}
	$tcpdf->Ln();
	$cnt = count($final_chunks);
	for ($i = 0; $i < $cnt; $i++) {	
	   $tcpdf->SetMargins(3, 5, 0, 0);		
	   $tcpdf->WriteHTMLCell(0, 0, 5, 7,$delimiter . $final_chunks[$i], 0, 0);	
       if ($i < $cnt - 1) {       
	     $tcpdf->AddPage('P', 'A4');		
       }
    }  
   // $tcpdf->Line(7, 20, 200, 20, $style);
	$tcpdf->lastPage();
	ob_end_clean();
	if($browser_preview){
		$tcpdf->Output($pdf_nme,'I');
	// return view($this->folder_path.'vouchers/receipt_pdf', $data);
	}
	else
	 $tcpdf->Output($pdf_nme, true);		   
  }
  
  public function modern_receipt_preview($voucher_series_id,$voucher_txn_id,$config_codes_list,$browser_preview=false)
  {   ob_start();
      
	   if($this->session->get('ses_boid')!='')
			$bo_id = $this->session->get('ses_boid');
		else 
		   $bo_id =1; 
	   
	  $comp_ro_address    = $this->CommonModel->get_comp_ho_adrs_info($bo_id);	 
	  $get_comp_taxt_info = $this->CommonModel->get_comp_taxt_info($this->company_id,$bo_id);	 
      $branch_gstin_info = $this->TransactionModel->get_branch_gstin_info($bo_id);
	  
	  $tcpdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
	  $company_info         = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];
      $data['email']        = $company_info['corp_email'];
      $data['phone']        = $company_info['corp_mobile'];	  
      if(isset($branch_gstin_info['comp_gstin']))
        $gst = $branch_gstin_info['comp_gstin'];
	  else
		$gst ='';
	  
      $data['gst'] = $gst;
      $data['cin'] = $company_info['cin'];
	  if(isset($comp_ro_address['bo_add1']))
       $company_adrs1 = $comp_ro_address['bo_add1'];
      else
		$company_adrs1 = '';  
	
	  if(isset($comp_ro_address['bo_add2']))
       $company_adrs2 = $comp_ro_address['bo_add2'];
      else
		$company_adrs2 = ''; 
	
      $data['company_address']   = $company_adrs1;
      $data['company_address2']  = $company_adrs2;	  
      $voucher_info              = $this->TransactionModel->get_voucher_cons_info($voucher_txn_id);	  
      $data['voucher_series']    = $voucher_info['comp_vch_series_id'];
      $data['voucher_no']        = $voucher_info['comp_vch_no'];
      $data['voucher_date']      = date('d-m-Y', strtotime($voucher_info['voucher_date']));
      $get_voucher_series        = $this->TransactionModel->get_voucher_series($voucher_info['comp_vch_series_id']);
      $data['voucher_series']    = $get_voucher_series['comp_vch_series'] ?? '';
      $narration                 = $this->TransactionModel->get_voucher_long_narration($voucher_txn_id);
      $data['narration']         = $narration ?? '';
      $account_transactions      = $this->TransactionModel->get_all_account_transactions($voucher_txn_id);
      $credit_transactions       = [];
      $debit_transactions        = [];
      $amount                    = 0;
      foreach ($account_transactions as $key => $value) {

          if($value['credit'] != ''){
              $amount += parseAmount($value['credit']);
              $string = '<strong>'.$value['account_name'].'</strong>';

              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
              $credit_transactions[] = [
                'party'   => $string,
                'amount'  => formatAmount($value['credit']),
              ]; 
          }
          if($value['debit'] != ''){
              $string             = $value['account_name'];
              if(!empty(trim($value['description']))){
                  $short_narration = trim($value['description']);
                  $string .= "<small>(${short_narration})</small>";
              }
              $debit_transactions[] = $string; 
           }
      }	

    $data['credit_transactions'] = $credit_transactions;
    $data['debit_transactions']  = implode(', ', $debit_transactions);
    $data['amount']              = formatAmount($amount, false);
    $data['amount_words']        = getIndianCurrency(parseAmount($amount)); 	
	$pdf_nme                     ='receipt.pdf';	
	$pdf                         ='';
	
	if(GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1))
	  $receipt_title =GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1);
	 else
	 $receipt_title ='RECEIPT';
 
 
    $header_html ='<table  width="100%;" style="width:100%;  position:relative;">
       <tr>
           <td colspan="2">
		   <table width="100%;" border="0"  style="text-align:center; border-collapse:collapse;  width:100%; color:#000;   font-size:8.5pt;">
			   <tr>
				   <td style="border-top:8px solid #0fae2c;"></td>
				   <td style="border-top:8px solid #1d5182; position:relative;"></td>
			   </tr>
	   </table>
	   </td>
       </tr>    
	   <tr>
	   <td>
	   <img style="height:70px; width:auto;padding:0px;margin:0px;" src="images/logo.png"><p style="padding:0px;margin-top:0px;margin-bottom:0px;"><span style="'.GetPrintLabelVal($config_codes_list[9],9,$voucher_series_id).'">'.$data['company_name'].'</span><br><br><span style="'.GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id).'">'.$data['company_address'].'</span><br><span style="'.GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id).'">'.$data['company_address2'].'</span> <br><span style="font-size:9pt;">Phone:</span> <span style="'.GetPrintLabelVal($config_codes_list[24],24,$voucher_series_id).'">'.$data['phone'].'</span> <br><span style="font-size:9pt;">Email:</span> <span style="'.GetPrintLabelVal($config_codes_list[25],25,$voucher_series_id).'">'.$data['email'].'</span><br></p>
	   </td> 
	     <td style="padding:0 .3cm; text-align:right;">
	       <p style="margin:0px; padding:0px;'.GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id).'">'.$receipt_title.'</p><br>
	        <h4 style="margin:0; padding:0;"><span style="'.GetPrintLabelVal($config_codes_list[28],28,$voucher_series_id).'">#'.$data['voucher_no'].'</span> </h4>
	        <p style="margin:0; padding:0;"><span style="padding-left:12px;'.GetPrintLabelVal($config_codes_list[11],11,$voucher_series_id).'">Dated No.</span> : <span style="'.GetPrintLabelVal($config_codes_list[29],29,$voucher_series_id).'">'.$data['voucher_date'].'</span> </p>
	   </td> 
	  </tr>
	  <tr>
	      <td colspan="2" background-color:#000000;><table cellpadding="6" border="0" width="100%" style="color:#1d5182; font-size:9.2pt; font-weight:bold; border-top:1px solid #000;">
	       <tr>
	           <td height="22">
			   <span style="'.GetPrintLabelVal($config_codes_list[6],6,$voucher_series_id).'">GST</span> : <span style="'.GetPrintLabelVal($config_codes_list[26],26,$voucher_series_id).'">'.$data['gst'].'</span></td>
	           <td height="22"  style="text-align:right;">
			   <span style="'.GetPrintLabelVal($config_codes_list[7],7,$voucher_series_id).'">CIN</span> : <span style="'.GetPrintLabelVal($config_codes_list[27],27,$voucher_series_id).'">'.$data['cin'].'</span></td>
	       </tr>
	      
	   </table></td>
	  </tr>
	  
	  <tr>
	      <td colspan="2" background-color:#000000;><table border="0"  cellpadding="7" width="100%" style="font-weight:bold; background-color: #000000; color:#fff;font-size:9.2pt; font-weight:bold;">
	       <tr>
	           <td height="22"  width="60%"  align="left">&nbsp;<span style="'.GetPrintLabelVal($config_codes_list[12],12,$voucher_series_id).'">Party</span></td>
	           <td height="22"  width="20%"  align="center"><span style="'.GetPrintLabelVal($config_codes_list[14],14,$voucher_series_id).'">Place of Supply</span></td>
	           <td height="22"  width="20%"  align="right"><span style="'.GetPrintLabelVal($config_codes_list[16],16,$voucher_series_id).'">Amount</span></td>
	       </tr>
	   </table></td>
	  </tr>	  </table>
	   ';
	  
 $footer_html = '<table border="0" width="100%" style="font-size:8.5pt;"><tr>
                <td>
                    <table style="margin:0px 0px 0px 1%; width:100%; border-top:1px solid #000; font-size:8.5pt;">
	       <tbody><tr>
	       <td colspan="2" style="padding:.1cm .3cm;">
	           <p>Mode : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="padding-left:29px;'.GetPrintLabelVal($config_codes_list[17],17,$voucher_series_id).'">'.$data['debit_transactions'].'</span><br>Remarks : <span style="padding-left:8px;'.GetPrintLabelVal($config_codes_list[18],18,$voucher_series_id).'">'.$data['narration'].'</span></p>
	       </td>
	       </tr>
	       <tr><td>&nbsp;<br></td></tr>
	       <tr style="padding-top:30px;">
	           <td style="padding-bottom:20px;">
			   <p>
			   <span style="'.GetPrintLabelVal($config_codes_list[36],36,$voucher_series_id).'"><span style="font-family:dejavuserif;">&#8377;</span>'.strip_tags($data['amount']).'</span>
			   <br><span style="'.GetPrintLabelVal($config_codes_list[20],20,$voucher_series_id).'">'.$data['amount_words'].'</span>
			   <br><small>(Cheque Subject to Realisation)</small>
			   
			   </p>
	    </td>
	   <td> <p style="text-align:right; padding-right:8px;'.GetPrintLabelVal($config_codes_list[21],21,$voucher_series_id).'">For '.$data['company_name'].'</p>
	  <p style="text-align:right; padding-top:50px;"><strong style="text-align:right;'.GetPrintLabelVal($config_codes_list[19],19,$voucher_series_id).'">Authorised Signatory</strong></p>
	  </td>
	       </tr>
		   <tr><td  colspan="2"> </td></tr>
		   <tr><td style="border-top:3px solid #1d5182;" colspan="2">&nbsp;<br></td></tr>
	   </tbody></table>
                </td>
            </tr></table>';
	
// set default monospaced font
//$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set title of pdf
$tcpdf->SetTitle('Receipt');
$tcpdf->SetHeaderMargin(4.5,0,0,0);
$tcpdf->SetFooterMargin(63,0,0,0);


$tcpdf->setPrintHeader(true);
$tcpdf->setPrintFooter(true);
$tcpdf->setListIndentWidth(3);
$tcpdf->setHeaderData(0, 0,0, '', $tc=array(0,0,0), $lc=array(0,0,0),$header_html);
$tcpdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0),$footer_html);

$tcpdf->SetAutoPageBreak(TRUE, 63);
$tcpdf->SetDisplayMode(90);

$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$tcpdf->SetFont('segoe_ui', '', '8.5pt');
$tcpdf->SetFont('dejavuserif', '', '8.5pt','',false);	
$tcpdf->SetFont('arial', '', '','',false);
$tcpdf->SetFont('verdana', '', '','',false);
$tcpdf->SetFont('times_new_roman', '', '','',false);
 // use the font

$tcpdf->AddPage('P', 'A4'); 
$inner_pdf='';
$inner_pdf.=' <table border="0"  width="99%" cellpadding="5" align="left" style="font-size:8.5pt;">';
$trcounter=0;
foreach ($credit_transactions as $key => $credit_transaction){
	$trcounter++;	
	if($trcounter%2==0){
	$inner_pdf.='<tr style="background-color:#f3f3f3;">
	           <td style="" width="55%"><p style="'.GetPrintLabelVal($config_codes_list[30],30,$voucher_series_id).'">'.$credit_transaction['party'].'</p></td>
	           <td style=""  width="21.3%"><p style="'.GetPrintLabelVal($config_codes_list[32],32,$voucher_series_id).'"></p></td>
	           <td width="20%" align="right"><p style="'.GetPrintLabelVal($config_codes_list[37],37,$voucher_series_id).'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',$credit_transaction['amount']).'</p></td>
			</tr>';

	} else{
	  $inner_pdf.='<tr>
	           <td width="55%" border="0"><p style="'.GetPrintLabelVal($config_codes_list[30],30,$voucher_series_id).'">'.$credit_transaction['party'].'</p></td>
	           <td width="21.3%" border="0"><p style="'.GetPrintLabelVal($config_codes_list[32],32,$voucher_series_id).'"></p></td>
	           <td width="20%" border="0" align="right"><p style="'.GetPrintLabelVal($config_codes_list[37],37,$voucher_series_id).'"><span style="font-family:dejavuserif;">&#8377;</span>'.str_replace('&#8377;','',$credit_transaction['amount']).'</p></td>
			</tr>';	
	    }	
     }
	
	$inner_pdf .=' </table>';
	
	
	
$pdf .='<section class="sec"><table border="0" width="100%" cellpadding="1" style="font-size:8.5pt;">
        <tbody>
            <tr>
			    <td align="left">
                  '.$inner_pdf.'
                </td>
            </tr>				
			<tr>
	      <td> <table  border="0" width="95%" style="border-top:1px solid #000;">
	              <tr> <td colspan="3"></td></tr>
		      </table>
		   </td>
	      </tr>	
	  	<tr>
	      <td> <table  border="0" width="95%" >
	              <tr> 
				  <td width="60%" style="font-weight:bold;">Total:</td>
				  <td width="20%"></td>
				  <td width="18%" align="right" style="'.GetPrintLabelVal($config_codes_list[36],36,$voucher_series_id).'"><span style="font-family:dejavuserif;">&#8377;</span>'.$data['amount'].'</td>
				  </tr>
		      </table>
		   </td>
	      </tr>
		<tr>
	      <td> <table  border="0" width="97%" >
	              <tr> 
				  <td width="60%" style="border-bottom:1px solid #000;"></td>
				  <td width="20%" style="border-bottom:1px solid #000;"></td>
				  <td width="18%" style="border-bottom:1px solid #000;"></td>
				  </tr>
		      </table>
		   </td>
	      </tr>		
        </tbody>
    </table>
</section>';


$delimiter  = '<section class="sec">';
$chunks     = explode($delimiter, $pdf);
$headerHeight = $tcpdf->headerHeight;
$tcpdf->SetY($headerHeight); //81
$final_chunks = array();
foreach($chunks as $dd){
	if($dd!='')
	$final_chunks[]=$dd;
}
$cnt = count($final_chunks);
for ($i = 0; $i < $cnt; $i++) {	
    $tcpdf->SetMargins(-4, $tcpdf->GetY(), 0, 0);		
    $tcpdf->WriteHTMLCell(0, 0, 7.5, $tcpdf->GetY()+10,$delimiter . $final_chunks[$i], 0, 1);	 	
	if ($i < $cnt - 1) {       
      $tcpdf->AddPage('P', 'A4');		
     } 
 }

$tcpdf->lastPage();
ob_end_clean();
if($browser_preview)
  $tcpdf->Output($pdf_nme,'I');
else		
  $tcpdf->Output($pdf_nme, true);
}
  
/* public function receipt_preview2_old($voucher_txn_id)
  {
    // return $this->receipt_pdf($voucher_txn_id);

      $company_info = $this->CommonModel->get_company_info($this->company_id);
      $data['company_name'] = $company_info['comp_name'];

      $data['email'] = $this->enc_string->nc_string($company_info['corp_email'],'de');
      $data['phone'] = $this->enc_string->nc_string($company_info['corp_mobile'],'de');

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

              $credit_transactions[] = [
                'party' => $string,
                'amount' => parseAmount($value['credit']),
              ]; 
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

      return view($this->folder_path.'vouchers/receipt_pdf2', $data);
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
  } */


}