<?php
namespace App\Controllers\Admin;
use App\Models\Admin\PrintingConfigModel;
use App\Models\Admin\TransactionModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use App\Models\CommonModel;


class Printing_config extends BaseController
{
	function __construct()
	{  
		helper(['form', 'url','text']);
		$this->PrintingConfigModel    = new PrintingConfigModel();	
		$this->CommonModel       = new CommonModel();	
		$this->auth_session      = new auth_session();
		$this->TransactionModel  = new TransactionModel();
		$this->auth_session->user_restrict();
		$this->auth_session->role_restrict('CS');
		$this->base_url      = base_url().'/'.getenv('AdminPath');
		$this->folder_path   = getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
		$this->doc = new \DOMDocument();
		
		$this->tcpdf_fonts_array         = array(''=>'','Arial' => 'Arial','Verdana' => 'Verdana', 'Times New Roman' => 'Times New Roman');
		$this->tcpdf_fontssize_array     = array(''=>'','6'=>'6','8'=>'8','10'=>'10','12'=>'12','14'=>'14','16'=>'16','18'=>'18','20'=>'20','24'=>'24');
		//Transactions Array Data
		$this->trans_masters_array       = array("Sales","Purchases","Banking","Items");		
		$this->trans_icons_array         = array("Sales"=>base_url().'/public/assets/images/icon-sales.png',
										   "Purchases"=>base_url().'/public/assets/images/icon-purchase.png',
                                           "Banking"=>base_url().'/public/assets/images/icon-bank.png',
										   "Items"=>base_url().'/public/assets/images/icon-item.png'
										   );
		$this->trans_sub_master_array    = array("Sales" =>array("Sale Invoice","Sales Order","Credit Notes","Delivery Challan","Quotations"),
									   "Purchases" =>array("Purchase Invoice","Purchase Order","Debit Notes","Inward Challan","Purchase Requisition"),
									   "Banking"   =>array("Payments","Receipts","Contra","Journal","Memorandum"),
									   "Items"     =>array("Stock Transfer","Physical Verfication","Production Voucher","Stock Journal","Consignment Packing")
		                               );
	}
    
	public function index()
	{
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']        = $this->session;

		return view($this->folder_path.'printing_config/view',$data);		
	} 
	
  public function master_configuration($type="")
	{
		if($type==''){
		 return redirect()->to($this->base_url.'printing_config');
		 die;  
		}
		
		$masters_array     = array();
		$sub_master_array  = array();
		$icons_array       = array();
	    if($type=='transactions'){
          $masters_array       = $this->trans_masters_array;		
		  $sub_master_array    = $this->trans_sub_master_array;
		  $icons_array         = $this->trans_icons_array;
		}
		 //receipt voucher series
		$data['receipt_voucher_series']  = $this->TransactionModel->comp_voucher_series(13);
		$data['sale_voucher_series']     = $this->TransactionModel->comp_voucher_series(18);
		$data['sale_order_series']       = $this->TransactionModel->comp_voucher_series(19);
		$data['creditnote_series']       = $this->TransactionModel->comp_voucher_series(2);
		$data['quotations_series']       = $this->TransactionModel->comp_voucher_series(17);
		$data['purchase_series']         = $this->TransactionModel->comp_voucher_series(11);
		$data['purchase_order_series']         = $this->TransactionModel->comp_voucher_series(12);
		$data['debitnote_series']              = $this->TransactionModel->comp_voucher_series(3);
		$data['purchase_requisition_series']   = $this->TransactionModel->comp_voucher_series(21);
		
		$data['sub_master_array'] = $sub_master_array;
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url']         = $this->base_url;
		$data['session']          = $this->session;
		$data['masters_array']    = $masters_array;
		$data['icons_array']      = $icons_array;		
		$data['type']             = $type;
		return view($this->folder_path.'printing_config/master_configuration',$data);		
	}
 
 public function setdefault_template($type,$master_id=0,$submaster_id=0,$usr_config_value=0){
	
	if(empty($master_id) || empty($submaster_id)){
				 throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}	 
	$data['set_template'] ='with_item';
	$uuid        =  $this->session->get('uuid');
	$business_id =  $this->CommonModel->get_business_id();
	$data['get_default_template'] ='';
	if($usr_config_value>0){	
	   if($usr_config_value==100 || $usr_config_value==126){  // Banking => receipts
		   $usr_config_id = 266;
		   $info          = array('uuid'=>$uuid,'usr_config_id'=>$usr_config_id,'usr_config_value'=>array(100,126));
	   }
	   if($usr_config_value==270){ // Sales => sale invoice with item
		   $usr_config_id = 272;
		   $info          = array('uuid'=>$uuid,'usr_config_id'=>$usr_config_id,'usr_config_value'=>array(270));
		   $data['set_template'] ='with_item';
	   }
	   if($usr_config_value==271){ // Sales => sale invoice without item
   		   $usr_config_id = 273;
		   $info          = array('uuid'=>$uuid,'usr_config_id'=>$usr_config_id,'usr_config_value'=>array(271));
	       $data['set_template'] ='without_item';
	   }
	   
		// mark default template for user printing 
		$savdata = array("uuid"=>$uuid,"usr_config_id"=>$usr_config_id,"usr_config_value"=>$usr_config_value);
		$save_default_template        = $this->PrintingConfigModel->save_default_template($savdata);
		$data['get_default_template'] = $this->PrintingConfigModel->get_default_template($info);
	  
	}else{
	
	  if(trim($master_id)=='banking' && trim($submaster_id)=='receipts'){
		$usr_config_id = 266;
		$info          = array('uuid'=>$uuid,'usr_config_id'=>266,'usr_config_value'=>array(100,126));
		$data['get_default_template'] = $this->PrintingConfigModel->get_default_template($info);
	  }	
	if(trim($master_id)=='sales' && trim($submaster_id)=='sale invoice'){
		$usr_config_id = 272;
		$data['set_template'] ='with_item';
		$info                    = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
		$data['get_default_template'] = $this->PrintingConfigModel->get_default_template($info);
	  }	
	if(trim($master_id)=='sales' && trim($submaster_id)=='sale invoice without'){
		$usr_config_id = 273;
		$info                         = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
		$data['set_template'] ='without_item';
		$data['get_default_template'] = $this->PrintingConfigModel->get_default_template($info);
	  }	
	} 	
	
	$masters_array      = array();
	$sub_master_array   = array();
	$icons_array        = array();
	if($type=='transactions'){
          $masters_array       = $this->trans_masters_array;		
		  $sub_master_array    = $this->trans_sub_master_array;
		  $icons_array         = $this->trans_icons_array;
		}
		 //receipt voucher series
		$data['receipt_voucher_series']  = $this->TransactionModel->comp_voucher_series(13);
		$data['sub_master_array']= $sub_master_array;
		$data['message_output']  = $this->message_output;
		$data['folder_path']     = $this->folder_path;
		$data['base_url']        = $this->base_url;
		$data['session']         = $this->session;
		$data['masters_array']   = $masters_array;
		$data['type']            = $type;
		$data['master_id']       = $master_id;
		$data['submaster_id']    = $submaster_id; 
		$data['icons_array']     = $icons_array;		
		return view($this->folder_path.'printing_config/set_template',$data); 
 }
 
 public function save_template_changes(){
	  if($this->request->getMethod() == 'post'){
		 $this->PrintingConfigModel->save_template_changes();	
	    return json_encode(['status' => true, 'message' => 'Data Added']);
	  }
	 
	 
 }
 
 public function print_settings($type,$master_id,$submaster_id,$user_config_id)
	{
		if($type=='' || $master_id=='' || $submaster_id==''){
		 return redirect()->to($this->base_url.'printing_config');
		 die;  
		}
		if($user_config_id==100 || $user_config_id==126){
	    $receipt_labels_array = array(''=>'','6'=>'Company GST Field','26'=>'Company GST Value','7'=>'Company CIN Field','27'=>'Company CIN Value','8'=>'Receipt','9'=>'Company Name','34'=>'Company Address','10'=>'Receipt No. Field','28'=>'Receipt No. Value','11'=>'Dated Field','29'=>'Dated Value',
                           '12'=>'Party Field','30'=>'Party Value','13'=>'Party identification No Field',
						   '31'=>'Party identification No Value','14'=>'Place of Supply Field','32'=>'Place of Supply Value','15'=>'Series Field','33'=>'Series Value','16'=>'Amount Field','37'=>'Amount Value','17'=>'Mode','18'=>'Long Narration',
						   '19'=>'Signatory','36'=>'Amount Total','20'=>'Amount In Words','21'=>'For Company Name','24'=>'Company Phone','25'=>'Company Email');
        
		if($user_config_id==100){
			$config_codes_list = array(6=>96,26=>97,7=>98,27=>99,8=>103,
			                           9=>101,34=>102,10=>267,28=>268,11=>105,29=>106,12=>107,30=>108,
									   13=>109,31=>110,14=>111,32=>112,15=>113,33=>114,16=>115,37=>116,17=>117,18=>118,19=>119,36=>120,20=>121,21=>301
									   );
			
		}
		   if($user_config_id==126){
			$config_codes_list = array(6=>122,26=>123,7=>124,27=>125,8=>269,
			                           9=>127,34=>128,28=>129,11=>130,29=>131,12=>132,30=>133,
									   13=>134,31=>135,14=>136,32=>137,15=>138,33=>139,16=>140,37=>141,17=>142,18=>143,19=>144,36=>145,20=>146,24=>147,25=>148,21=>302
									   );
			
		    }
		}
		
		if($user_config_id==270 || $user_config_id==271){
			
			if($user_config_id==270){
			$receipt_labels_array = array(''=>'','8'=>'title','9'=>'Company name','6'=>'Company gstin','34'=>'Company address','24'=>'company phone','25'=>'Company email','108'=>'invoice date','109'=>'invoice due date','110'=>'invoice reference','115'=>'Dated Field','14'=>' PLACE OF SUPPLY',
                           '116'=>'INVOICE NUMBER','117'=>'BILL TO NAME','118'=>'BILL TO ADDRESS',
						   '119'=>'BILL TO PHONE','120'=>'BILL TO E-MAIL','121'=>'BILL TO GSTIN','122'=>'TRANSPORTER NAME','123'=>'TRANSPORTER ID','124'=>'TRANSPORT MODE','125'=>'TRANSPORT VEHICLE NUMBER','126'=>'ITEM NAME','127'=>'ITEM SERIAL NUMBER',
						   '128'=>'ITEM HSN/SAC','129'=>' ITEM RATE','130'=>'ITEM QUANTITY','131'=>'ITEM TAX RATE','132'=>'ITEM TAX AMOUNT','133'=>'TOTAL TAXABLE AMOUNT','134'=>'ITEM TOTAL','135'=>'TOTAL QUANTITY','146'=>'DISPATCH FROM NAME','136'=>'DISPATCH FROM ADDRESS',
						   '147'=>'DISPATCH FROM PHONE','148'=>'DISPATCH FROM E-MAIL','149'=>'DISPATCH FROM GSTIN','145'=>'QUANTITY DETAILS','137'=>'SHIP TO NAME','150'=>'SHIP TO ADDRESS','151'=>'SHIP TO PHONE','152'=>'SHIP TO E-MAIL','153'=>'SHIP TO GSTIN',
						   '86'=>'HSN SUMMARY','138'=>'TOTAL INVOICE VALUE','139'=>'TOTAL TAX AMOUNT','140'=>'TOTAL INVOICE VALUE IN WORDS',
						   '142'=>'BANK NAME','143'=>'BANK ACCOUNT','144'=>'BANK IFSC','141'=>'BANK BRANCH','104'=>'AUTHORISED SIGNATORY',
						   '21'=>'FOR COMPANY NAME','105'=>'TERMS & CONDITIONS','108'=>'INVOICE DATE','106'=>'CUSTOMER SIGN');
        	
			$config_codes_list = array(8=>274,9=>276,6=>173,34=>287,24=>175,25=>303,108=>177,109=>178,110=>179,
			                           115=>180,14=>181,116=>182,117=>183,118=>289,119=>184,120=>185,121=>186,
									   122=>191,123=>192,124=>193,125=>194,126=>196,127=>195,128=>198,129=>199,
									   130=>200,131=>205,132=>304,133=>203,134=>202,135=>305,146=>187,136=>291,
									   147=>292,148=>293,149=>294,145=>306,137=>188,150=>189,151=>189,152=>190,
									   153=>296,86=>204,138=>308,139=>310,140=>299,142=>213,143=>214,144=>283,
									   141=>284,104=>220,21=>281,105=>221,108=>177,106=>222);
			
		}
		   if($user_config_id==271){
			$receipt_labels_array = array(''=>'','8'=>'title','9'=>'Company name','6'=>'Company gstin','34'=>'Company address','24'=>'company phone','25'=>'Company email','108'=>'invoice date','109'=>'invoice due date','110'=>'invoice reference','115'=>'Dated Field','14'=>' PLACE OF SUPPLY',
                           '116'=>'INVOICE NUMBER','117'=>'BILL TO NAME','118'=>'BILL TO ADDRESS',
						   '119'=>'BILL TO PHONE','120'=>'BILL TO E-MAIL','121'=>'BILL TO GSTIN','112'=>'ACCOUNT NAME','111'=>'ACCOUNT SERIAL NUMBER ','113'=>'ACCOUNT DESCRIPTION','114'=>'ACCOUNT WISE AMOUNT','133'=>'TOTAL TAXABLE AMOUNT','154'=>'TOTAL DESCRIPTION',						   						   
						   '86'=>'HSN SUMMARY','138'=>'TOTAL INVOICE VALUE','139'=>'TOTAL TAX AMOUNT','140'=>'TOTAL INVOICE VALUE IN WORDS',
						   '142'=>'BANK NAME','143'=>'BANK ACCOUNT','144'=>'BANK IFSC','141'=>'BANK BRANCH','104'=>'AUTHORISED SIGNATORY',
						   '21'=>'FOR COMPANY NAME','105'=>'TERMS & CONDITIONS','108'=>'INVOICE DATE','106'=>'CUSTOMER SIGN');
           
			$config_codes_list = array(8=>275,9=>225,6=>226,34=>288,24=>227,25=>228,108=>229,109=>230,110=>180,
			                           115=>232,14=>233,116=>234,117=>2,118=>290,119=>236,120=>237,121=>238,
									   112=>278,111=>277,113=>279,114=>280,133=>244,154=>307,86=>245,138=>309,139=>311,
									   140=>300,142=>255,143=>256,144=>285,141=>286,104=>262,21=>282,105=>263,108=>229,106=>264);
			
		    }
		}

		$receipt_labels_dropdown = $this->PrintingConfigModel->receipt_labels_dropdown($receipt_labels_array);
		$masters_array            = array();
		$sub_master_array         = array();
	    if($type=='transactions'){
          $masters_array          = $this->trans_masters_array;		
		  $sub_master_array       = $this->trans_sub_master_array;
	     } 		
		 
		$data['receipt_labels_dropdown'] = $receipt_labels_dropdown;
		$data['config_codes_list'] = $config_codes_list;
		$data['sub_master_array'] = $sub_master_array;
		$data['fonts_array']      = $this->tcpdf_fonts_array;
		$data['fontsize_array']   = $this->tcpdf_fontssize_array;
		$data['message_output']   = $this->message_output;
		$data['folder_path']      = $this->folder_path;
		$data['base_url']         = $this->base_url;
		$data['session']          = $this->session;
		$data['masters_array']    = $masters_array;
		$data['type']             = $type;
		$data['submaster_id']     = $submaster_id;
		$data['master_id']        = $master_id;
		$data['docobj']        = $this->doc;
		
		$data['user_config_id']   = $user_config_id;	
     if(($user_config_id==100 || $user_config_id==126) ){
	    if($submaster_id=='receipts'){
			$title                     ='Receipt';
			$data['voucher_series_id'] = 13;
			$data['title']             = $title;			
			if($user_config_id==100){
			    $prntconfig_id         = $this->TransactionModel->get_print_config_info(100,13);
			    $data['prntconfig_id'] = $prntconfig_id;
			  return view($this->folder_path.'printing_config/receipts/traditional_theme',$data);
			}
		    if($user_config_id==126){
		        $prntconfig_id         = $this->TransactionModel->get_print_config_info(126,13);
			    $data['prntconfig_id'] = $prntconfig_id;
			  return view($this->folder_path.'printing_config/receipts/modern_theme',$data);
		    }
		}
   }
    if(($user_config_id==270 || $user_config_id==271) ){	
		
	  if($master_id=='sales'){
			$title ='Sales';
			$data['voucher_series_id']   = 18;
			$data['title']   = $title;	
		
			if($user_config_id==270){
			    $prntconfig_id         = $this->TransactionModel->get_print_config_info(270,18);
			    $data['prntconfig_id'] = $prntconfig_id;
			  return view($this->folder_path.'printing_config/invoice/modern_theme_withitem',$data);
			}
		    if($user_config_id==271){
		         $prntconfig_id         = $this->TransactionModel->get_print_config_info(271,18);
			    $data['prntconfig_id'] = $prntconfig_id;
			  return view($this->folder_path.'printing_config/invoice/modern_theme_withoutitem',$data);
		    }
		}
		
	}
   
       			
	}	
	
}