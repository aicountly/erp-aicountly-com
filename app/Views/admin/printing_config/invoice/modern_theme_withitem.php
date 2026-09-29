<?php $header = array( 	'title' => $title. ' Printing Configuration' ); ?>
<?php echo view('includes/header',$header);?>
<div class="row align-items-center">
		     <div class="col-md-6"><h3>Printing Configration(<?php echo ucwords($submaster_id);?>)</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
    	<a class="hideinline-md collapsed" data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>       
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">CSV</a></li>
            <li><a class="dropdown-item" href="#">Excel</a></li>
            <li><a class="dropdown-item" href="#">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       <a href="#" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>             
            <div class="listmenu collapse" id="listmenu" style="">
               <div class="float-md-end d-inline-block">    
			   <a href="#" class="btn btn-success">Show Preview</a>
			   <a href="#" class="btn btn-success">Publish</a>	
			   <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div> 
            </div> 

    </div>
	<style>
     body {  font-family:'Segoe UI';   font-size:8.5pt;  margin: 0 auto;   padding: 0;  line-height:25px; }
     * { margin:0; padding:0; box-sizing: border-box; -moz-box-sizing: border-box;     }
     p{padding:2px 0px;}
	 .invoice { width: 210mm;   margin:2mm auto;    background:white;  }
     .sub-page {  padding:.2cm; width:100%; }
	 table{border-collapse: collapse; width:100%; border:0px; font-family:'Segoe UI','Arial';  font-size:8.5pt;}
	 .bottom_bdr{border:1px solid #000;}
	 .bottom_bdr tr td{border-right:1px solid #000;border-bottom:1px solid #000; padding:4px 6px;}
	 .greyr_bdr{border: 1px solid #000;}
	 .greyr_bdr td{border-right:1px solid #000; padding:4px 6px;}
	 .partyinfo td{height:50px;}
	 .no_bdr tr td{border:0px; padding:2px 1px;}
	 #myBillingArea{background:#fff;}
  </style>
<div class="row align-items-start my-3">
  <div class="col-md-8">
      <div id="myBillingArea" class="invoice">

   <div class="sub-page">
  <table border="0" width="100%" class="bottom_bdr" style="font-family:'Segoe UI','Arial';  font-size:8.5pt;">
      <tr><?php
                if(GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1))
                   $title_value=GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1);
                 else
                  $title_value= 'TAX INVOICE'; 
                
                ?>
          <td colspan="4" style="text-align:center;"><span id="print_receipt_label" style="text-align:center;<?php echo GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id);?>"><?php echo $title_value;?></span></td>
      </tr>
	   <tr>
	   <td colspan="2" rowspan="2">
	 <table border="0" style="border:0" cellspacing="0" cellpadding="0">
	     <tr><td style="border:0; padding:0;"><img src="<?php echo base_url();?>/public/assets/img/logo.png" style="width:110px; float:left; margin-right:10px; height:auto;"></td>
	    <td style="border:0; padding:0;"><h4 style="margin-top:10px; line-height:25px;"><span style="<?php echo GetPrintLabelVal($config_codes_list[9],9,$voucher_series_id);?>" id="print_company_name">Company Pvt. Ltd.</span></h4>
	   <p>GSTIN: <span style="<?php echo GetPrintLabelVal($config_codes_list[6],6,$voucher_series_id);?>" id="print_company_gstin">#AI 2223333</span></p>
	   <p id="print_company_address" style="<?php echo GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id);?>">No. 10 Srinivasa Street, Anna Nagar, Tolgatel, Karachi</p>
	   <p>Phone: <span id="print_company_phone" style="<?php echo GetPrintLabelVal($config_codes_list[24],24,$voucher_series_id);?>">+91 24422 44444</span></p>
	   <p>Email: <span id="print_company_email" style="<?php echo GetPrintLabelVal($config_codes_list[25],25,$voucher_series_id);?>">info@glorekart.com</span></p></td></tr>
	 </table>
	     
	   </td> 
	    <td style="width:25%;"><table border="0" style="border:0" cellspacing="0" cellpadding="0">
	     <tr><td style="border:0; padding:0;">Date :</td><td style="border:0; padding:0;"><span id="print_date_fld" style="<?php echo GetPrintLabelVal($config_codes_list[108],108,$voucher_series_id);?>">10-01-2024</span></td></tr>
	     <tr><td style="border:0; padding:0;">Due Date :</td><td style="border:0; padding:0;"><span id="print_duedate_val" style="<?php echo GetPrintLabelVal($config_codes_list[109],109,$voucher_series_id);?>">05-22-2024</span></td></tr>
	     <tr><td style="border:0; padding:0;">Reference :</td><td style="border:0; padding:0;"><span id="print_reference_val" style="<?php echo GetPrintLabelVal($config_codes_list[110],110,$voucher_series_id);?>">Company XMP</span></td></tr>
	     <tr><td style="border:0; padding:0;">IRN :</td><td style="border:0; padding:0;"><span id="print_irn_val" style="<?php echo GetPrintLabelVal($config_codes_list[115],115,$voucher_series_id);?>">#12344566</span></td></tr>
	 </table> </td>
	     <td style="width:18%;"><br></td>
	     </tr>
	     <tr>
	     <td>Place of Supply:<br><span id="print_placeofsupply_val" style="<?php echo GetPrintLabelVal($config_codes_list[14],14,$voucher_series_id);?>">Noida</span> </td>
	     <td>Invoice No:<br><span id="print_invoiceno_val" style="<?php echo GetPrintLabelVal($config_codes_list[116],116,$voucher_series_id);?>">#AI 8</span> </td>
	     </tr>
	     <tr>
	       <td valign="top">Customer Details :<br>
	         <p><span id="print_partyname_val" style="<?php echo GetPrintLabelVal($config_codes_list[117],117,$voucher_series_id);?>">Subham Markfeed</span></p>
			 <p><span id="print_partyaddress_val" style="<?php echo GetPrintLabelVal($config_codes_list[118],118,$voucher_series_id);?>"><small> NO.10, SRINIVASA STREET, ANNA NAGAR, <br>TOLGATE, LITTLE KANCHIPURAM</small></span> </p>
			 <p>Phone: <span id="print_partyphone_val" style="<?php echo GetPrintLabelVal($config_codes_list[119],119,$voucher_series_id);?>">+91 24422 44444</span></p>
			 <p>Email: <span id="print_partyemail_val" style="<?php echo GetPrintLabelVal($config_codes_list[120],120,$voucher_series_id);?>">info@glorekart.com</span></p>
			 <p>GSTIN: <span id="print_partygstin_val" style="<?php echo GetPrintLabelVal($config_codes_list[121],121,$voucher_series_id);?>">125466</span></p>
	         </td>
		   <td valign="top">Dispatch From :<br>
	         <p><span id="print_dsptchfrm_locname_val" style="<?php echo GetPrintLabelVal($config_codes_list[146],146,$voucher_series_id);?>">Subham Markfeed</span></p>
			 <p><span id="print_dsptchfrm_adrs1_val" style="<?php echo GetPrintLabelVal($config_codes_list[136],136,$voucher_series_id);?>"><small> NO.10, SRINIVASA STREET, ANNA NAGAR, <br>TOLGATE, LITTLE KANCHIPURAM</small></span> </p>
						
	         </td>	         
	       <td valign="top">Ship To :<br>
			   <p><span id="print_shipto_locname_val" style="<?php echo GetPrintLabelVal($config_codes_list[137],137,$voucher_series_id);?>">Ship To Location</span></p>
			   <p><span id="print_shipto_adrs1_val" style="<?php echo GetPrintLabelVal($config_codes_list[150],150,$voucher_series_id);?>"><small> NO.10, SRINIVASA KANCHIPURAM</small></span> </p>
			   <p>Phone: <span id="print_shipto_phone_val" style="<?php echo GetPrintLabelVal($config_codes_list[151],151,$voucher_series_id);?>">+91 24422 44444</span></p>
			   <p>Email: <span id="print_shipto_email_val" style="<?php echo GetPrintLabelVal($config_codes_list[152],152,$voucher_series_id);?>">info@glorekart.com</span></p>
	       </td>
		   <td valign="top">Transporter Details :<br>
			   <p><span id="print_shipto_trnsport_val" style="<?php echo GetPrintLabelVal($config_codes_list[122],122,$voucher_series_id);?>">Transporter Name</span></p>
			   <p>Transporter Id: <span id="print_trnsport_id_val" style="<?php echo GetPrintLabelVal($config_codes_list[123],123,$voucher_series_id);?>">124154</span></p>
			   <p>Transport Mode: <span id="print_trnsport_mode_val" style="<?php echo GetPrintLabelVal($config_codes_list[124],124,$voucher_series_id);?>">R</span></p>
			   <p>Vehicle No: <span id="print_trnsport_vchlno_val" style="<?php echo GetPrintLabelVal($config_codes_list[125],125,$voucher_series_id);?>">PB08DZ2345</span></p>
		   </td>
	     </tr>	     
	   </table>

	   <table class="greyr_bdr" border="0" width="100%" style="margin:3px 0px;">
	       <tr style="font-weight:bold;">
	           <td>Sr.No.</td>
	           <td>Item</td>
	           <td>HSN/SAC</td>
	           <td>Rate/Item</td>
	           <td>Qty</td>
	           <td>Tax Value</td>
	           <td>Tax Amt</td> 
	           <td style="padding:6px; text-align:right;">Amount</td>
	       </tr>
	       <tr class="partyinfo">
	           <td class="print_item_srno" style="<?php echo GetPrintLabelVal($config_codes_list[127],127,$voucher_series_id);?>">1</td>
	           <td class="print_item_name" style="<?php echo GetPrintLabelVal($config_codes_list[126],126,$voucher_series_id);?>">Pay U / E Commers <p>- ID No: UN001 0033</p></td>
	           <td class="print_item_hsn" style="<?php echo GetPrintLabelVal($config_codes_list[128],128,$voucher_series_id);?>">87033044</td>
	            <td class="print_item_rate" style="<?php echo GetPrintLabelVal($config_codes_list[129],129,$voucher_series_id);?>">57,675.00</td>
	            <td class="print_item_qty" style="<?php echo GetPrintLabelVal($config_codes_list[130],130,$voucher_series_id);?>">2</td>
	            <td class="print_item_taxval" style="<?php echo GetPrintLabelVal($config_codes_list[131],131,$voucher_series_id);?>">20%</td>
	           <td class="print_item_taxamnt" style="<?php echo GetPrintLabelVal($config_codes_list[132],132,$voucher_series_id);?>">5,556</td>
	           <td class="print_item_amnt" style="padding:6px; text-align:right;<?php echo GetPrintLabelVal($config_codes_list[133],133,$voucher_series_id);?>">&#8377; 88,430.00</td>
	       </tr>
	      	       
	       <tr style="font-weight:bold;">
	           <td colspan="5" style="border-top:1px solid #000; border-right:1px solid #fff;">Total Items / Qty : 2 / 2.00</td>
	           <td colspan="2" style="border-top:1px solid #000; border-right:1px solid #fff;">Taxable Amount</td>
	           <td colspan="2" style="border-top:1px solid #000; text-align:right;"><span id="print_taxableamnt_val" style="<?php echo GetPrintLabelVal($config_codes_list[139],139,$voucher_series_id);?>">&#8377; 3340.00</span></td>
	       </tr>	    
	       <tr>
	           <td colspan="4" rowspan="2" style="border-top:1px solid #000;"><b>HSN Summary</b>
	           <table width="100%" style="margin:3px 0px;">
				   <tr style="font-weight:bold;">
					   <td  style="border-right:none;">HSN/SAC</td>
					   <td style="border-right:none;">TAX RATE</td>
					   <td  style="border-right:none;">IGST</td>
					   <td  style="border-right:none;">CGST</td>
					   <td  style="border-right:none;">SGST</td>
					   <td  style="border-right:none;">CESS</td>
					   <td  style="border-right:none;">TOTAL TAX</td> 					  
				   </tr>
				    <tr>
					   <td class="print_hsn_summary" style="border-right:none;<?php echo GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id);?>">312</td>
					    <td class="print_hsn_summary" style="border-right:none;<?php echo GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id);?>">20%</td>
					   <td class="print_hsn_summary" style="border-right:none;<?php echo GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id);?>">&#8377; 125</td>
					   <td class="print_hsn_summary" style="border-right:none;<?php echo GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id);?>">&#8377; 520</td>
					   <td class="print_hsn_summary" style="border-right:none;<?php echo GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id);?>">&#8377; 485</td>
					   <td class="print_hsn_summary" style="border-right:none;<?php echo GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id);?>">&#8377; 0</td>
					   <td class="print_hsn_summary" style="padding:6px; text-align:right;border-right:none;<?php echo GetPrintLabelVal($config_codes_list[86],86,$voucher_series_id);?>">&#8377; 4500.00</td>
				    </tr>
					
		      </table>			   
			   </td>
	           <td colspan="2" style="font-weight:bold; font-size:18px; border-top:1px solid #000; border-right:1px solid #fff;">
			     <table width="100%">
				 <tr>
				 <td style="border-right:none;">IGST:</td><td style="border-right:none;">&#8377; 4500.00</td>
				 </tr>
				 </table>
			   
			   TOTAL</td>
	           <td colspan="3" style="border-top:1px solid #000; padding:6px; text-align:right;" id="print_total_invoice_val" style="<?php echo GetPrintLabelVal($config_codes_list[138],138,$voucher_series_id);?>">&#8377; 12,777,3340.00</td>
	       </tr>
	        <tr>
	           <td colspan="5" style="padding:6px;" id="print_total_invoice_val_words" style="<?php echo GetPrintLabelVal($config_codes_list[140],140,$voucher_series_id);?>">Rs. Tweleve Lakh, Seventy Thousand, so on and so on...</td>
	       </tr>
	
	   </table>
	   <table class="bottom_bdr">
	    <tr>
	      <td colaspan="2" style="width:40%;"><b>Bank Details:</b>
	      <table border="0" class="no_bdr" width="100%" style="border:0" cellspacing="0" cellpadding="0">
	     <tr><td>Bank</td><td id="print_bank_name" style="<?php echo GetPrintLabelVal($config_codes_list[142],142,$voucher_series_id);?>">Yes Bank</td></tr>
	     <tr><td>Account</td><td id="print_bank_acc" style="<?php echo GetPrintLabelVal($config_codes_list[143],143,$voucher_series_id);?>">1222991200</td></tr>
	     <tr><td>IFSC</td><td id="print_bank_ifsc" style="<?php echo GetPrintLabelVal($config_codes_list[144],144,$voucher_series_id);?>">PNB0122</td></tr>
	     <tr><td>Branch</td><td id="print_bank_branch" style="<?php echo GetPrintLabelVal($config_codes_list[141],141,$voucher_series_id);?>">Pathankot Bypass, Jalandhar</td></tr>
	 </table>
	      </td>
	      <td style="width:12%;"><p style="font-size:11px; line-height:16px;">E-way QR Code:</p></td>
	      <td style="width:13%;"><p style="font-size:11px; line-height:16px;">Pay using UPI:</p></td>
	   <td style="width:35%; border-right:0px;text-align:right;"> <p style="padding-right:8px;text-align:right;" id="print_forcompanyname" style="<?php echo GetPrintLabelVal($config_codes_list[21],21,$voucher_series_id);?>">For Company Name.</p>
	  <p style="padding-top:10px;" id="print_signatory" style="<?php echo GetPrintLabelVal($config_codes_list[104],104,$voucher_series_id);?>">Authorised Signatory</p></td>
	       </tr>
	       <tr>
	      <td><span style="font-weight:600;">Terms & Conditions:</span>
	     <span id="print_terms_conditions" style="<?php echo GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id);?>">
			<?php
                if(GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1)){
                   echo GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1);
                 
                ?>
				
				<?php } else { ?>
                
		 <ol style="padding-left:15px;" id="print_terms_conditions" style="<?php echo GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id);?>"><li>Goods once sold cannot be taken back or exchanged</li>
	      <li>We are not the manufacurers, company will stand for warranty as their terms & conditions.</li>
	      <li>Interest @24% per itm will be changred for uncleared bills beyond 15 days.</li>
	      <li>Subject to local jurisdictions</li>
	      </ol>
				<?php } ?>
		  
		  </span></td>
	      <td colspan="2"><p style="line-height:16px;">Customer Sign:</p></td>
	      <td>Notes:<br>
	      <p>Thankyou for dealing with our company.</p>
	      </td>
	       </tr>
	   </table>
	   
    </div>

</div>
      
  </div>
  
    <div class="col-6 col-md-4">
 <div class="accordion" id="printingnav">
 

  <div class="accordion-item">
      <button class="accordion-button" type="button" data-bs-toggle="" data-bs-target="#p3" aria-expanded="true">Labels</button>
    <form id="prntingfrm" method="post">
	<div id="p3" class="accordion-collapse" aria-labelledby="print3" data-bs-parent="#printingnav" style="">
      <div class="accordion-body">
      <p>
	    <div class="input-group mb-3">
	  <span class="input-group-text" id="basic-addon1">Label Type</span>
	 <?php echo form_dropdown("prnt_typeid",$receipt_labels_dropdown,'','id="prnt_typeid" class="form-control" required');?>
	</div>
	  
	    <div class="input-group mb-3" id="rcpt_label" style="display:none;">
		<input type="text" name="rcpt_title" id="rcpt_title" class="form-control" value="TAX INVOICE">
	  </div>
	    <div class="input-group mb-3" id="rcpt_terms_conditions_div" style="display:none;">
		<?php 
			$new_text_data='';
			$liValues=array();
		  if(GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1)){
                  $termsconds_data =GetPrintLabelVal($config_codes_list[105],105,$voucher_series_id,1);
				  preg_match_all('/<li>([\s\S]*)<\/li>/msU',$termsconds_data,$liValues); 
				  if(isset($liValues[1])){
					   foreach($liValues[1] as $rows){
						   $new_text_data .=$rows.PHP_EOL;
						   
					   }
				   } 				
		  } 
				   ?>
		<textarea  name="rcpt_terms_conditions" id="rcpt_terms_conditions" class="form-control"><?php echo $new_text_data;?></textarea>
	  </div>
	  
	   <div class="input-group mb-3">
	  <button type="button" data-id="bold" class="prntlabel_style badge text-bg-secondary"><strong>B</strong></button>&nbsp;
	  <button type="button" data-id="italic" class="prntlabel_style badge text-bg-secondary"><strong style="font-style:italic;">I</strong></button>&nbsp;
	  <button type="button" data-id="underline" class="prntlabel_style badge text-bg-secondary"><strong style="text-decoration:underline;">U</strong></button>
	  </div>
	
	  <div class="input-group mb-3">
	  <span class="input-group-text" id="basic-addon1">Font</span>
	 <?php echo form_dropdown("prnt_type_fontname",$fonts_array,'','id="prnt_type_fontname" class="form-control" required');?>
	</div>
   
	 <div class="input-group mb-3">
	  <span class="input-group-text" id="basic-addon1">Size</span>
	 <?php echo form_dropdown("prnt_type_fontsize",$fontsize_array,'','id="prnt_type_fontsize" class="form-control" required');?>
	</div> 
	  <button id="saveprntfrm" class="btn btn-success">SAVE</button></p>
     </div>
    </div>
	</form>
  </div>
  
    
  </div>
  
</div>
 
</div>	
  
<?php echo view('includes/footer_scripts'); ?>
<script>
$(".openmaster_div").on("click",function(){
  var divid = $(this).data("id");	  
  $(".subdivs").hide();
  $("#"+divid).show();  
});
$("#rcpt_title").on("change",function(){
  if($(this).val()=='')
    $("#print_receipt_label").html('R E C E I P T');	
  else	
   $("#print_receipt_label").html($(this).val());
});
$("#rcpt_title").keyup(function(event){
	$("#print_receipt_label").html($(this).val());
});


$("#rcpt_terms_conditions").on("change",function(){
  if($(this).val()=='')
    $("#print_terms_conditions").html('<ol style="padding-left:15px;"><li>Goods once sold cannot be taken back or exchanged</li><li>We are not the manufacurers, company will stand for warranty as their terms &amp; conditions.</li><li>Interest @24% per itm will be changred for uncleared bills beyond 15 days.</li><li>Subject to local jurisdictions</li></ol>');	
  else{
	var lines = $(this).val().split(/\n/);
	var paragrph='';
    for(var i=0;i<=lines.length;i++){
		if(lines[i]!='' && typeof lines[i] != "undefined"){
		  	paragrph +='<li>'+lines[i]+'</li>';
		}
	}
	$("#print_terms_conditions").html(paragrph);
  }
});


/************************** Start ************************/
$("#prnt_typeid").on("change",function(){
 if($(this).val()=="8"){
	 $("#rcpt_label").show();
	 $("#rcpt_terms_conditions_div").hide();
	 $("#rcpt_terms_conditions").val("");
 }else{
	 $("#rcpt_label").hide();
	 $("#rcpt_terms_conditions_div").hide();
	 $("#rcpt_title").val("");	 
   }	
   
   if($(this).val()=="105"){
	   $("#rcpt_label").hide();
	 $("#rcpt_terms_conditions_div").show();
 }else{	 
	 $("#rcpt_terms_conditions_div").hide();	 
   }	
   
	$("#prnt_type_fontname").val("");
	$("#prnt_type_fontsize").val("");
	
});
/*************    Bold ,Italic, Underline ******/
$(".prntlabel_style").on("click",function(){
   var label_type = $("#prnt_typeid").find('option:selected').val();
   var fontstyle = $(this).data('id');
   if(label_type=="8")
      print_fontstyle_fun('print_receipt_label',fontstyle);	 
   else if(label_type=="9")
      print_fontstyle_fun('print_company_name',fontstyle);	 
   else if(label_type=="34")
      print_fontstyle_fun('print_company_address',fontstyle);
 else if(label_type=="24")
      print_fontstyle_fun('print_company_phone',fontstyle);  
  else if(label_type=="108")
      print_fontstyle_fun('print_date_fld',fontstyle);
else if(label_type=="109")
      print_fontstyle_fun('print_duedate_val',fontstyle);
else if(label_type=="110")
      print_fontstyle_fun('print_reference_val',fontstyle);
else if(label_type=="115")
      print_fontstyle_fun('print_irn_val',fontstyle);
else if(label_type=="14")
      print_fontstyle_fun('print_placeofsupply_val',fontstyle);
else if(label_type=="116")
      print_fontstyle_fun('print_invoiceno_val',fontstyle);
else if(label_type=="117")
      print_fontstyle_fun('print_partyname_val',fontstyle);
else if(label_type=="118")
      print_fontstyle_fun('print_partyaddress_val',fontstyle);
else if(label_type=="119")
      print_fontstyle_fun('print_partyphone_val',fontstyle);
else if(label_type=="120")
      print_fontstyle_fun('print_partyemail_val',fontstyle);
else if(label_type=="121")
      print_fontstyle_fun('print_partygstin_val',fontstyle);
else if(label_type=="146")
      print_fontstyle_fun('print_dsptchfrm_locname_val',fontstyle);
else if(label_type=="136")
      print_fontstyle_fun('print_dsptchfrm_adrs1_val',fontstyle);
else if(label_type=="137")
      print_fontstyle_fun('print_shipto_locname_val',fontstyle);
else if(label_type=="150")
      print_fontstyle_fun('print_shipto_adrs1_val',fontstyle);
else if(label_type=="151")
      print_fontstyle_fun('print_shipto_phone_val',fontstyle);
else if(label_type=="152")
      print_fontstyle_fun('print_shipto_email_val',fontstyle);
else if(label_type=="122")
      print_fontstyle_fun('print_shipto_trnsport_val',fontstyle);
else if(label_type=="123")
      print_fontstyle_fun('print_trnsport_id_val',fontstyle);
else if(label_type=="124")
      print_fontstyle_fun('print_trnsport_mode_val',fontstyle);
else if(label_type=="125")
      print_fontstyle_fun('print_trnsport_vchlno_val',fontstyle);

else if(label_type=="25")
      print_fontstyle_fun('print_company_email',fontstyle);
  else if(label_type=="105")
      print_fontstyle_fun('print_terms_conditions',fontstyle);

  else if(label_type=="6")
      print_fontstyle_fun('print_company_gstin',fontstyle);	 
   else if(label_type=="10")
      print_fontstyle_fun('print_receipt_fld',fontstyle);	 
   else if(label_type=="28")
      print_fontstyle_fun('print_receipt_val',fontstyle);	 
   else if(label_type=="11")
      print_fontstyle_fun('print_rcptdate_fld',fontstyle);	 
   else if(label_type=="29")
      print_fontstyle_fun('print_rcptdate_val',fontstyle);	
   else if(label_type=="12")
      print_fontstyle_fun('print_party_fld',fontstyle);	 
   else if(label_type=="30")
      print_fontstyle_fun('print_party_val',fontstyle);	 
   else if(label_type=="13")
      print_fontstyle_fun('print_party_idntno_fld',fontstyle);	 
   else if(label_type=="31")
      print_fontstyle_fun('print_party_idntno_val',fontstyle);	 
   else if(label_type=="14")
      print_fontstyle_fun('print_party_posno_fld',fontstyle);	 
   else if(label_type=="32")
      print_fontstyle_fun('print_party_posno_val',fontstyle); 
   else if(label_type=="15")
      print_fontstyle_fun('print_party_series_fld',fontstyle);	 
   else if(label_type=="33")
      print_fontstyle_fun('print_party_series_val',fontstyle);	 
   else if(label_type=="16")
      print_fontstyle_fun('print_party_amount_fld',fontstyle);	 
   else if(label_type=="37")
      print_fontstyle_fun('print_party_amount_val',fontstyle); 
   else if(label_type=="17")
      print_fontstyle_fun('print_mode_val',fontstyle);	 
   else if(label_type=="18")
      print_fontstyle_fun('print_remarks_val',fontstyle);
   else if(label_type=="6")
      print_fontstyle_fun('print_gst_fld',fontstyle);
   else if(label_type=="26")
      print_fontstyle_fun('print_gst_val',fontstyle);
  else if(label_type=="19")
      print_fontstyle_fun('print_forcompany_val',fontstyle);
  else if(label_type=="36")
      print_fontstyle_fun('print_footer_amount',fontstyle);
  else if(label_type=="20")
      print_fontstyle_fun('print_footer_amountinwords',fontstyle);
  else if(label_type=="7")
      print_fontstyle_fun('print_cin_fld',fontstyle);
   else if(label_type=="27")
	 print_fontstyle_fun('print_cin_val',fontstyle);  
 
 else if(label_type=="127")
	 print_fontstyle_fun('print_item_srno',fontstyle,1); 
else if(label_type=="126")
	 print_fontstyle_fun('print_item_name',fontstyle,1);
else if(label_type=="128")
	 print_fontstyle_fun('print_item_hsn',fontstyle,1);
else if(label_type=="129")
	 print_fontstyle_fun('print_item_rate',fontstyle,1); 
else if(label_type=="130")
	 print_fontstyle_fun('print_item_qty',fontstyle,1);
 else if(label_type=="131")
	 print_fontstyle_fun('print_item_taxval',fontstyle,1);
 else if(label_type=="132")
	 print_fontstyle_fun('print_item_taxamnt',fontstyle,1);
 else if(label_type=="133")
	 print_fontstyle_fun('print_item_amnt',fontstyle,1);

else if(label_type=="86")
	 print_fontstyle_fun('print_hsn_summary',fontstyle,1);
 else if(label_type=="132")
	 print_fontstyle_fun('print_taxableamnt_val',fontstyle);
 
 else if(label_type=="138")
	 print_fontstyle_fun('print_total_invoice_val',fontstyle);
else if(label_type=="140")
	 print_fontstyle_fun('print_total_invoice_val_words',fontstyle);


else if(label_type=="142")
	 print_fontstyle_fun('print_bank_name',fontstyle);
else if(label_type=="143")
	 print_fontstyle_fun('print_bank_acc',fontstyle);
else if(label_type=="144")
	 print_fontstyle_fun('print_bank_ifsc',fontstyle);
else if(label_type=="141")
	 print_fontstyle_fun('print_bank_branch',fontstyle);

else if(label_type=="104")
	 print_fontstyle_fun('print_signatory',fontstyle);
 else if(label_type=="21")
	 print_fontstyle_fun('print_forcompanyname',fontstyle);
   
});
/*************    Arial, Tiems New Roman, VErdana ******/
$("#prnt_type_fontname").on("change",function(){
	var fontname = $(this).find('option:selected').val();	
	var label_type = $("#prnt_typeid").find('option:selected').val();
	
	if(label_type=="8")
      print_fontname_fun('print_receipt_label',fontname);	 
   else if(label_type=="9")
      print_fontname_fun('print_company_name',fontname);	 
   else if(label_type=="34")
      print_fontname_fun('print_company_address',fontname);
else if(label_type=="24")
      print_fontname_fun('print_company_phone',fontname); 
else if(label_type=="25")
      print_fontname_fun('print_company_email',fontname);  
 else if(label_type=="137")
      print_fontname_fun('print_shipto_locname_val',fontname);
else if(label_type=="150")
      print_fontname_fun('print_shipto_adrs1_val',fontname);
else if(label_type=="151")
      print_fontname_fun('print_shipto_phone_val',fontname);
else if(label_type=="152")
      print_fontname_fun('print_shipto_email_val',fontname);
else if(label_type=="122")
      print_fontname_fun('print_shipto_trnsport_val',fontname);
else if(label_type=="123")
      print_fontname_fun('print_trnsport_id_val',fontname);
else if(label_type=="124")
      print_fontname_fun('print_trnsport_mode_val',fontname);
else if(label_type=="125")
      print_fontname_fun('print_trnsport_vchlno_val',fontname);

  else if(label_type=="108")
      print_fontname_fun('print_date_fld',fontname);
else if(label_type=="109")
      print_fontname_fun('print_duedate_val',fontname);
else if(label_type=="110")
      print_fontname_fun('print_reference_val',fontname);
else if(label_type=="115")
      print_fontname_fun('print_irn_val',fontname);
else if(label_type=="14")
      print_fontname_fun('print_placeofsupply_val',fontname);
else if(label_type=="116")
      print_fontname_fun('print_invoiceno_val',fontname);
else if(label_type=="117")
      print_fontname_fun('print_partyname_val',fontname);
else if(label_type=="118")
      print_fontname_fun('print_partyaddress_val',fontname);
else if(label_type=="119")
      print_fontname_fun('print_partyphone_val',fontname);
else if(label_type=="120")
      print_fontname_fun('print_partyemail_val',fontname);
else if(label_type=="121")
      print_fontname_fun('print_partygstin_val',fontname);
else if(label_type=="146")
      print_fontname_fun('print_dsptchfrm_locname_val',fontname);
else if(label_type=="136")
      print_fontname_fun('print_dsptchfrm_adrs1_val',fontname); 
    else if(label_type=="105")
      print_fontname_fun('print_terms_conditions',fontname);
else if(label_type=="6")
      print_fontname_fun('print_company_gstin',fontname);	  
   else if(label_type=="10")
      print_fontname_fun('print_receipt_fld',fontname);	 
   else if(label_type=="28")
      print_fontname_fun('print_receipt_val',fontname);	 
   else if(label_type=="11")
      print_fontname_fun('print_rcptdate_fld',fontname);	 
   else if(label_type=="29")
      print_fontname_fun('print_rcptdate_val',fontname);	
   else if(label_type=="12")
      print_fontname_fun('print_party_fld',fontname);	 
   else if(label_type=="30")
      print_fontname_fun('print_party_val',fontname);	 
   else if(label_type=="13")
      print_fontname_fun('print_party_idntno_fld',fontname);	 
   else if(label_type=="31")
      print_fontname_fun('print_party_idntno_val',fontname);	 
   else if(label_type=="14")
      print_fontname_fun('print_party_posno_fld',fontname);	 
   else if(label_type=="32")
      print_fontname_fun('print_party_posno_val',fontname); 
   else if(label_type=="15")
      print_fontname_fun('print_party_series_fld',fontname);	 
   else if(label_type=="33")
      print_fontname_fun('print_party_series_val',fontname);	 
   else if(label_type=="16")
      print_fontname_fun('print_party_amount_fld',fontname);	 
   else if(label_type=="37")
      print_fontname_fun('print_party_amount_val',fontname); 
   else if(label_type=="17")
      print_fontname_fun('print_mode_val',fontname);	 
   else if(label_type=="18")
      print_fontname_fun('print_remarks_val',fontname);
   else if(label_type=="6")
      print_fontname_fun('print_gst_fld',fontname);
   else if(label_type=="26")
      print_fontname_fun('print_gst_val',fontname);
  else if(label_type=="19")
      print_fontname_fun('print_forcompany_val',fontname);
  else if(label_type=="36")
      print_fontname_fun('print_footer_amount',fontname);
  else if(label_type=="20")
      print_fontname_fun('print_footer_amountinwords',fontname);
  else if(label_type=="7")
      print_fontname_fun('print_cin_fld',fontname);
   else if(label_type=="27")
	 print_fontname_fun('print_cin_val',fontname); 

else if(label_type=="127")
	 print_fontname_fun('print_item_srno',fontname,1); 
else if(label_type=="126")
	 print_fontname_fun('print_item_name',fontname,1);
else if(label_type=="128")
	 print_fontname_fun('print_item_hsn',fontname,1);
else if(label_type=="129")
	 print_fontname_fun('print_item_rate',fontname,1); 
else if(label_type=="130")
	 print_fontname_fun('print_item_qty',fontname,1);
 else if(label_type=="131")
	 print_fontname_fun('print_item_taxval',fontname,1);
 else if(label_type=="132")
	 print_fontname_fun('print_item_taxamnt',fontname,1);
 else if(label_type=="133")
	 print_fontname_fun('print_item_amnt',fontname,1); 
else if(label_type=="86")
	 print_fontname_fun('print_hsn_summary',fontname,1);
else if(label_type=="132")
	 print_fontname_fun('print_taxableamnt_val',fontname);
 
 else if(label_type=="138")
	 print_fontname_fun('print_total_invoice_val',fontname);
 else if(label_type=="140")
	 print_fontname_fun('print_total_invoice_val_words',fontname);
 
 else if(label_type=="142")
	 print_fontname_fun('print_bank_name',fontname);
else if(label_type=="143")
	 print_fontname_fun('print_bank_acc',fontname);
else if(label_type=="144")
	 print_fontname_fun('print_bank_ifsc',fontname);
else if(label_type=="141")
	 print_fontname_fun('print_bank_branch',fontname);

else if(label_type=="104")
	 print_fontname_fun('print_signatory',fontname);
 else if(label_type=="21")
	 print_fontname_fun('print_forcompanyname',fontname);
 
});
/*************   2,4,6,8,10,12,14,16,18,20 ******/
$("#prnt_type_fontsize").on("change",function(){
	var label_type = $("#prnt_typeid").find('option:selected').val();
	var fontsize   = $(this).find('option:selected').text();
	
	if(label_type=="8")
      print_fontsize_fun('print_receipt_label',fontsize);	 
   else if(label_type=="9")
      print_fontsize_fun('print_company_name',fontsize);	 
   else if(label_type=="34")
      print_fontsize_fun('print_company_address',fontsize);	
  else if(label_type=="24")
      print_fontsize_fun('print_company_phone',fontsize); 
  else if(label_type=="25")
      print_fontsize_fun('print_company_email',fontsize);
  
  else if(label_type=="137")
      print_fontsize_fun('print_shipto_locname_val',fontsize);
else if(label_type=="150")
      print_fontsize_fun('print_shipto_adrs1_val',fontsize);
else if(label_type=="151")
      print_fontsize_fun('print_shipto_phone_val',fontsize);
else if(label_type=="152")
      print_fontsize_fun('print_shipto_email_val',fontsize);
else if(label_type=="122")
      print_fontsize_fun('print_shipto_trnsport_val',fontsize);
else if(label_type=="123")
      print_fontsize_fun('print_trnsport_id_val',fontsize);
else if(label_type=="124")
      print_fontsize_fun('print_trnsport_mode_val',fontsize);
else if(label_type=="125")
      print_fontsize_fun('print_trnsport_vchlno_val',fontsize);
  
  
  else if(label_type=="108")
      print_fontsize_fun('print_date_fld',fontsize);
else if(label_type=="109")
      print_fontsize_fun('print_duedate_val',fontsize);
else if(label_type=="110")
      print_fontsize_fun('print_reference_val',fontsize);
else if(label_type=="115")
      print_fontsize_fun('print_irn_val',fontsize);
else if(label_type=="14")
      print_fontsize_fun('print_placeofsupply_val',fontsize);
else if(label_type=="116")
     print_fontsize_fun('print_invoiceno_val',fontsize);
     
  else if(label_type=="117")
      print_fontsize_fun('print_partyname_val',fontsize);
else if(label_type=="118")
      print_fontsize_fun('print_partyaddress_val',fontsize);
else if(label_type=="119")
      print_fontsize_fun('print_partyphone_val',fontsize);
else if(label_type=="120")
      print_fontsize_fun('print_partyemail_val',fontsize);
else if(label_type=="121")
      print_fontsize_fun('print_partygstin_val',fontsize);
else if(label_type=="146")
      print_fontsize_fun('print_dsptchfrm_locname_val',fontsize);
else if(label_type=="136")
      print_fontsize_fun('print_dsptchfrm_adrs1_val',fontsize);
   
      else if(label_type=="105")
      print_fontsize_fun('print_terms_conditions',fontsize);
   else if(label_type=="6")
      print_fontsize_fun('print_company_gstin',fontsize);	  
   else if(label_type=="10")
      print_fontsize_fun('print_receipt_fld',fontsize);	 
   else if(label_type=="28")
      print_fontsize_fun('print_receipt_val',fontsize);	 
   else if(label_type=="11")
      print_fontsize_fun('print_rcptdate_fld',fontsize);	 
   else if(label_type=="29")
      print_fontsize_fun('print_rcptdate_val',fontsize);	
   else if(label_type=="12")
      print_fontsize_fun('print_party_fld',fontsize);	 
   else if(label_type=="30")
      print_fontsize_fun('print_party_val',fontsize);	 
   else if(label_type=="13")
      print_fontsize_fun('print_party_idntno_fld',fontsize);	 
   else if(label_type=="31")
      print_fontsize_fun('print_party_idntno_val',fontsize);	 
   else if(label_type=="14")
      print_fontsize_fun('print_party_posno_fld',fontsize);	 
   else if(label_type=="32")
      print_fontsize_fun('print_party_posno_val',fontsize); 
   else if(label_type=="15")
      print_fontsize_fun('print_party_series_fld',fontsize);	 
   else if(label_type=="33")
      print_fontsize_fun('print_party_series_val',fontsize);	 
   else if(label_type=="16")
      print_fontsize_fun('print_party_amount_fld',fontsize);	 
   else if(label_type=="37")
      print_fontsize_fun('print_party_amount_val',fontsize); 
   else if(label_type=="17")
      print_fontsize_fun('print_mode_val',fontsize);	 
   else if(label_type=="18")
      print_fontsize_fun('print_remarks_val',fontsize);
   else if(label_type=="6")
      print_fontsize_fun('print_gst_fld',fontsize);
   else if(label_type=="26")
      print_fontsize_fun('print_gst_val',fontsize);
  else if(label_type=="19")
      print_fontsize_fun('print_forcompany_val',fontsize);
  else if(label_type=="36")
      print_fontsize_fun('print_footer_amount',fontsize);
  else if(label_type=="20")
      print_fontsize_fun('print_footer_amountinwords',fontsize);
  else if(label_type=="7")
      print_fontsize_fun('print_cin_fld',fontsize);
   else if(label_type=="27")
	 print_fontsize_fun('print_cin_val',fontsize); 


else if(label_type=="127")
	 print_fontsize_fun('print_item_srno',fontsize,1); 
else if(label_type=="126")
	 print_fontsize_fun('print_item_name',fontsize,1);
else if(label_type=="128")
	 print_fontsize_fun('print_item_hsn',fontsize,1);
else if(label_type=="129")
	 print_fontsize_fun('print_item_rate',fontsize,1); 
else if(label_type=="130")
	 print_fontsize_fun('print_item_qty',fontsize,1);
 else if(label_type=="131")
	 print_fontsize_fun('print_item_taxval',fontsize,1);
 else if(label_type=="132")
	 print_fontsize_fun('print_item_taxamnt',fontsize,1);
 else if(label_type=="133")
	 print_fontsize_fun('print_item_amnt',fontsize,1);
 else if(label_type=="86")
	 print_fontsize_fun('print_hsn_summary',fontsize,1);
 else if(label_type=="132")
	 print_fontsize_fun('print_taxableamnt_val',fontsize);
  else if(label_type=="138")
	 print_fontsize_fun('print_total_invoice_val',fontsize);
  else if(label_type=="140")
	 print_fontsize_fun('print_total_invoice_val_words',fontsize);
 
  else if(label_type=="142")
	 print_fontsize_fun('print_bank_name',fontsize);
else if(label_type=="143")
	 print_fontsize_fun('print_bank_acc',fontsize);
else if(label_type=="144")
	 print_fontsize_fun('print_bank_ifsc',fontsize);
else if(label_type=="141")
	 print_fontsize_fun('print_bank_branch',fontsize);
 
 else if(label_type=="104")
	 print_fontsize_fun('print_signatory',fontsize);
 else if(label_type=="21")
	 print_fontsize_fun('print_forcompanyname',fontsize);
 
});

/************************** End ************************/



/************************** FUNCTION START ************************/
function get_field_style(label_type){
	if(label_type=="8")
     var fldval= print_field_value('print_receipt_label');	 
   else if(label_type=="9")
     var fldval=  print_field_value('print_company_name');	 
   else if(label_type=="34")
     var fldval=  print_field_value('print_company_address');
  else if(label_type=="24")
     var fldval=  print_field_value('print_company_phone');  
   else if(label_type=="25")
      var fldval=print_field_value('print_company_email');
     else if(label_type=="105")
      var fldval=print_field_value('print_terms_conditions');
  
  
   else if(label_type=="137")
      var fldval=print_field_value('print_shipto_locname_val');
else if(label_type=="150")
      var fldval=print_field_value('print_shipto_adrs1_val');
else if(label_type=="151")
      var fldval=print_field_value('print_shipto_phone_val');
else if(label_type=="152")
      var fldval=print_field_value('print_shipto_email_val');
else if(label_type=="122")
      var fldval=print_field_value('print_shipto_trnsport_val');
else if(label_type=="123")
      var fldval=print_field_value('print_trnsport_id_val');
else if(label_type=="124")
      var fldval=print_field_value('print_trnsport_mode_val');
else if(label_type=="125")
      var fldval=print_field_value('print_trnsport_vchlno_val');
  
  
   else if(label_type=="108")
      var fldval=print_field_value('print_date_fld');
else if(label_type=="109")
      var fldval=print_field_value('print_duedate_val');
else if(label_type=="110")
      var fldval=print_field_value('print_reference_val');
else if(label_type=="115")
      var fldval=print_field_value('print_irn_val');
else if(label_type=="14")
      var fldval=print_field_value('print_placeofsupply_val');
else if(label_type=="116")
      var fldval=print_field_value('print_invoiceno_val');
  
  
    
  else if(label_type=="117")
      var fldval=print_field_value('print_partyname_val');
else if(label_type=="118")
      var fldval=print_field_value('print_partyaddress_val');
else if(label_type=="119")
      var fldval=print_field_value('print_partyphone_val');
else if(label_type=="120")
      var fldval=print_field_value('print_partyemail_val');
else if(label_type=="121")
      var fldval=print_field_value('print_partygstin_val');
else if(label_type=="146")
      var fldval=print_field_value('print_dsptchfrm_locname_val');
else if(label_type=="136")
      var fldval=print_field_value('print_dsptchfrm_adrs1_val');
  
  
  
else if(label_type=="6")
    var fldval=  print_field_value('print_company_gstin'); 
   else if(label_type=="10")
     var fldval=  print_field_value('print_receipt_fld');	 
   else if(label_type=="28")
     var fldval=  print_field_value('print_receipt_val');	 
   else if(label_type=="11")
     var fldval=  print_field_value('print_rcptdate_fld');	 
   else if(label_type=="29")
     var fldval=  print_field_value('print_rcptdate_val');	
   else if(label_type=="12")
     var fldval=  print_field_value('print_party_fld');	 
   else if(label_type=="30")
     var fldval=  print_field_value('print_party_val');	 
   else if(label_type=="13")
     var fldval=  print_field_value('print_party_idntno_fld');	 
   else if(label_type=="31")
     var fldval=  print_field_value('print_party_idntno_val');	 
   else if(label_type=="14")
     var fldval=  print_field_value('print_party_posno_fld');	 
   else if(label_type=="32")
     var fldval=  print_field_value('print_party_posno_val'); 
   else if(label_type=="15")
    var fldval=   print_field_value('print_party_series_fld');	 
   else if(label_type=="33")
     var fldval=  print_field_value('print_party_series_val');	 
   else if(label_type=="16")
     var fldval=  print_field_value('print_party_amount_fld');	 
   else if(label_type=="37")
    var fldval=   print_field_value('print_party_amount_val'); 
   else if(label_type=="17")
    var fldval=   print_field_value('print_mode_val');	 
   else if(label_type=="18")
    var fldval=   print_field_value('print_remarks_val');
   else if(label_type=="6")
    var fldval=   print_field_value('print_gst_fld');
   else if(label_type=="26")
    var fldval=   print_field_value('print_gst_val');
  else if(label_type=="19")
    var fldval=   print_field_value('print_forcompany_val');
  else if(label_type=="36")
    var fldval=   print_field_value('print_footer_amount');
  else if(label_type=="20")
    var fldval=   print_field_value('print_footer_amountinwords');
   else if(label_type=="7")
    var fldval=   print_field_value('print_cin_fld');
   else if(label_type=="27")
	var fldval=  print_field_value('print_cin_val');


else if(label_type=="127")
	 var fldval=  print_field_value('print_item_srno',1); 
else if(label_type=="126")
	 var fldval=  print_field_value('print_item_name',1);
else if(label_type=="128")
	 var fldval=  print_field_value('print_item_hsn',1);
else if(label_type=="129")
	 var fldval=  print_field_value('print_item_rate',1); 
else if(label_type=="130")
	 var fldval=  print_field_value('print_item_qty',1);
 else if(label_type=="131")
	 var fldval=  print_field_value('print_item_taxval',1);
 else if(label_type=="132")
	 var fldval=  print_field_value('print_item_taxamnt',1);
 else if(label_type=="133")
	 var fldval=  print_field_value('print_item_amnt',1);
 else if(label_type=="86")
	 var fldval=  print_field_value('print_hsn_summary',1);
  else if(label_type=="132")
	 var fldval=  print_field_value('print_taxableamnt_val');
 
   else if(label_type=="138")
	 var fldval=  print_field_value('print_total_invoice_val');
   else if(label_type=="140")
	 var fldval=  print_field_value('print_total_invoice_val_words');
 
   else if(label_type=="142")
	 var fldval=  print_field_value('print_bank_name');
else if(label_type=="143")
	 var fldval=  print_field_value('print_bank_acc');
else if(label_type=="144")
	 var fldval=  print_field_value('print_bank_ifsc');
else if(label_type=="141")
	 var fldval=  print_field_value('print_bank_branch');
  
  else if(label_type=="104")
	 var fldval=  print_field_value('print_signatory');
 else if(label_type=="21")
	 var fldval=  print_field_value('print_forcompanyname');
 
 return fldval;
}

function print_field_value(id,isclass=0){
	if(isclass>0)
	 return $("."+id).attr("style");	
	else	
	return $("#"+id).attr("style");
}

function print_fontname_fun(id,fontname,isclass=0){
	if(isclass>0){
	 $("."+id).css("font-family",'' );
	 $("."+id).css("font-family",fontname );	
	}
	else{
	 $("#"+id).css("font-family",'' );
	 $("#"+id).css("font-family",fontname );
	}
}

function print_fontsize_fun(id,fontsize,isclass=0){
	if(isclass>0){
	  $("."+id).css("font-size",'')
	  $("."+id).css("font-size", fontsize + "px");	
	}
	else{
	  $("#"+id).css("font-size",'')
	  $("#"+id).css("font-size", fontsize + "px");
	}
}

function print_fontstyle_fun(id,fontstyle,isclass=0){
	if(fontstyle=='bold'){	
	 if(isclass>0){
		if($("."+id).hasClass('font-weight-aplied')){
		$("."+id).removeClass("font-weight-aplied");
		$("."+id).css("font-weight", '');
	  } else{
		$("."+id).addClass("font-weight-aplied");	    
	    $("."+id).css("font-weight", 'bold');
	  } 
	 }
	 else{
	if($("#"+id).hasClass('font-weight-aplied')){
		$("#"+id).removeClass("font-weight-aplied");
		$("#"+id).css("font-weight", '');
	  } else{
		$("#"+id).addClass("font-weight-aplied");	    
	    $("#"+id).css("font-weight", 'bold');
	  }	 
	 }
	
	  
	}
  else if(fontstyle=='italic'){	
     if(isclass>0){
		if($("."+id).hasClass('font-style-aplied')){
		$("."+id).removeClass("font-style-aplied");
		$("."+id).css("font-style", '');
	  } else{
		$("."+id).addClass("font-style-aplied");	    
	    $("."+id).css("font-style", 'italic');
	  } 
	 }
	 else{
		if($("#"+id).hasClass('font-style-aplied')){
		$("#"+id).removeClass("font-style-aplied");
		$("#"+id).css("font-style", '');
	  } else{
		$("#"+id).addClass("font-style-aplied");	    
	    $("#"+id).css("font-style", 'italic');
	  } 
	 }
  
	  
	}	
  else if(fontstyle=='underline'){	
     if(isclass>0){
	 if($("."+id).hasClass('text-decoration-aplied')){
		$("."+id).removeClass("text-decoration-aplied");
		$("."+id).css("text-decoration", '');
	  } else{
		$("."+id).addClass("text-decoration-aplied");	    
	    $("."+id).css("text-decoration", 'underline');
	  }	 
	 }
	 else{
	 if($("#"+id).hasClass('text-decoration-aplied')){
		$("#"+id).removeClass("text-decoration-aplied");
		$("#"+id).css("text-decoration", '');
	  } else{
		$("#"+id).addClass("text-decoration-aplied");	    
	    $("#"+id).css("text-decoration", 'underline');
	  }	 
	 }
	 
	  
	}	
}
 var config_codes_list = <?php echo json_encode($config_codes_list);?>;
/************************** FUNCTION END ************************/
$("#prntingfrm").on("submit",function(){
	    var erpprevaln_label_id = $("#prnt_typeid").find('option:selected').val();
        var fldstylevalue = get_field_style(erpprevaln_label_id);
        var form = $("#prntingfrm");
		var rcpt_terms_conditions = $("#rcpt_terms_conditions").val();
        var formData = new FormData(this);
		formData.append("fldstyle", fldstylevalue);
		formData.append("type", '<?php echo $type;?>');
		formData.append("terms_conditions", rcpt_terms_conditions);
		formData.append("master_id", '<?php echo $master_id;?>');
		formData.append("submaster_id", '<?php echo $submaster_id;?>');
		formData.append("erpprevaln_label_id", erpprevaln_label_id);
		formData.append("usr_config_id", config_codes_list[erpprevaln_label_id]);
		formData.append("vch_srs_id", '<?php echo $voucher_series_id;?>');
        $.ajax({
            url: "<?php echo $base_url;?>printing_config/save_template_changes", 
            type: 'POST',
            data: formData,
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('#saveprntfrm').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);                  
                }
                else{
                    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
						var list = ``;
                        $.each(response.errors, function(index, value){
                            list += `<li>${value}</li>`;
                        });

                        var html = `
                            <div class="alert alert-danger alert-dismissible">
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <ul>${list}</ul>
                            </div>
                        `;
                        $('#validation_errors').html(html);
                        window.scrollTo(0,0);
					}
				}
		     },
			 complete: function() {
                stop_loader();
				form.trigger('reset');
				$("#prnt_typeid").val("");
                $('#saveprntfrm').attr('disabled', false);
            },
		});
		
	return false;		
});
</script>
</body>
</html>