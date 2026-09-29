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
       <a href="<?php echo base_url();?>/admin/dashboard" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>             
            <div class="listmenu collapse" id="listmenu" style="">
               <div class="float-md-end d-inline-block">    
			   <a href="#" class="btn btn-success">Show Preview</a>
			   <a href="#" class="btn btn-success">Publish</a>	
			   <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div> 
              </div> 

    </div>
	
<div class="row align-items-start my-3">
  <div class="col-md-8">
      <div id="myBillingArea" class="invoice shadow-lg bg-white">
<div class="sub-page">
<style>
    * { margin:0;padding:0;box-sizing: border-box; -moz-box-sizing: border-box;}
     p{padding:2px 0px;}
	 .invoice { width: 210mm;margin:10mm auto;background:white;}
     .sub-page {padding:1cm; }
     @page { size: A4;margin: 0; }
     @media print {
	 html, body {width:210mm;  }
       .invoice {margin:0;  border: initial; border-radius:initial; width:initial; 	min-height:initial; box-shadow:initial;  	background:initial; page-break-after: always;    }
     }
	 table{border-collapse: collapse;}
	 table.maintable td{border:1px solid #000; padding:6px;}
	 table.inner-table td{border:0px; padding:0px;}
</style>
		<div id="validation_errors"></div>
    <table border="0" class="maintable" width="100%" style="width:100%; font-family:'Arial';font-size: 14px;">
    <tbody>
        <tr>
            <td colspan="2">
                 <div style="text-align:left; width:48%; padding-left:5px; float:left;"><span id="print_gst_fld" style="<?php echo GetPrintLabelVal($config_codes_list[6],6,$voucher_series_id);?>">GST</span> : <span style="<?php echo GetPrintLabelVal($config_codes_list[26],26,$voucher_series_id);?>" id="print_gst_val">xxxxxx</span></div>
	       <div style="text-align:right; width:52%; padding-right:5px; float:right;"><span id="print_cin_fld" style="<?php echo GetPrintLabelVal($config_codes_list[7],7,$voucher_series_id);?>">CIN</span> : <span style="<?php echo GetPrintLabelVal($config_codes_list[27],27,$voucher_series_id);?>" id="print_cin_val">xxxxxx</span></div>
                <?php
                if(GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1))
                   $title_value=GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id,1);
                 else
                  $title_value= 'R E C E I P T'; 
                
                ?>
                
                <p id="print_receipt_label" style="text-align:center;padding:0; margin:0;<?php echo GetPrintLabelVal($config_codes_list[8],8,$voucher_series_id);?>"><?php echo $title_value;?></p>
                <p id="print_company_name" style="line-height:42px; padding:0; margin:0; text-align:center;<?php echo GetPrintLabelVal($config_codes_list[9],9,$voucher_series_id);?>">company name here</p>
                <p id="print_company_address" style="text-align:center; padding:0 0 8px 0; line-height:20px; margin:0;<?php echo GetPrintLabelVal($config_codes_list[34],34,$voucher_series_id);?>">company address here</p>
            </td>
        </tr>
        <tr>
            <td style="width:44%;">
                <span id="print_receipt_fld" style="<?php echo GetPrintLabelVal($config_codes_list[10],10,$voucher_series_id);?>">Receipt No.</span> : <span style="padding-left:18px;<?php echo GetPrintLabelVal($config_codes_list[28],28,$voucher_series_id);?>" id="print_receipt_val">xxxxxx</span> 
            </td>
            <td style="width:56%;"> 
                <span id="print_rcptdate_fld" style="<?php echo GetPrintLabelVal($config_codes_list[11],11,$voucher_series_id);?>"> Dated</span> : <span style="padding-left:18px;<?php echo GetPrintLabelVal($config_codes_list[29],29,$voucher_series_id);?>" id="print_rcptdate_val"> xxxxxx</span> 
            </td>
        </tr>
        <tr>
            <td style="width:44%; vertical-align:top">
                <table class="inner-table" border="0" width="100%" style="font-size:14px;">
                   
                    <tr>
                        <td style="vertical-align:top; padding-right:10px;"><span id="print_party_fld" style="<?php echo GetPrintLabelVal($config_codes_list[12],12,$voucher_series_id);?>">Party</span>&nbsp;:</td>
                        <td id="print_party_val" style="<?php echo GetPrintLabelVal($config_codes_list[30],30,$voucher_series_id);?>">xxxxxx</td>
                    </tr>
                   
                </table>
            </td>
            <td style="width:56%;">
                <p><span style="width:150px; float:left;<?php echo GetPrintLabelVal($config_codes_list[13],13,$voucher_series_id);?>" id="print_party_idntno_fld">Party identification No </span>:&nbsp;&nbsp;<span style="<?php echo GetPrintLabelVal($config_codes_list[31],31,$voucher_series_id);?>" id="print_party_idntno_val">xxxxxx</span> </p>

                <p><span style="width:150px; float:left;<?php echo GetPrintLabelVal($config_codes_list[14],14,$voucher_series_id);?>" id="print_party_posno_fld">Place of Supply </span>:&nbsp;&nbsp;<span id="print_party_posno_val" style="<?php echo GetPrintLabelVal($config_codes_list[32],32,$voucher_series_id);?>">xxxxxx</span></p>

                <p style="margin:10px 0px;">
                    <span style="width:150px; float:left;<?php echo GetPrintLabelVal($config_codes_list[15],15,$voucher_series_id);?>" id="print_party_series_fld">Series </span>:&nbsp;&nbsp;<span style="<?php echo GetPrintLabelVal($config_codes_list[33],33,$voucher_series_id);?>" id="print_party_series_val">xxxxxx</span>
                </p>
                
                <p>
                    <span style="width:150px; float:left;<?php echo GetPrintLabelVal($config_codes_list[16],16,$voucher_series_id);?>" id="print_party_amount_fld">Amount </span> :&nbsp;&nbsp;<span style="<?php echo GetPrintLabelVal($config_codes_list[37],37,$voucher_series_id);?>" id="print_party_amount_val">₹xxxxxx</span>
                </p>
            </td>
        </tr>
        
        
        <tr>
            <td colspan="2"><span id="print_mode_fld">Mode</a></span> : 
                <span style="padding-left:29px;<?php echo GetPrintLabelVal($config_codes_list[17],17,$voucher_series_id);?>" id="print_mode_val">
                    xxxxxx
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="2"><span id="print_remarks_fld">Remarks</span> : <span id="print_remarks_val" style="padding-left:8px;<?php echo GetPrintLabelVal($config_codes_list[18],18,$voucher_series_id);?>">
                xxxxxx
            </span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p style="text-align:right; padding-right:8px;">
                    <strong style="text-align:right;<?php echo GetPrintLabelVal($config_codes_list[21],21,$voucher_series_id);?>" id="print_forcompany_val">FOR company name here</strong>
                </p>
                <h4 style="padding:6px 0px;<?php echo GetPrintLabelVal($config_codes_list[36],36,$voucher_series_id);?>" id="print_footer_amount">₹xxxxxx
                    
                </h4>
                <p>
                    <strong id="print_footer_amountinwords" style="<?php echo GetPrintLabelVal($config_codes_list[20],20,$voucher_series_id);?>">amount in words</strong>
                </p>
                <p style="padding:6px 0px">
                    <small>(Cheque Subject to Realisation)</small>
                </p>
                <p id="print_footer_signatory" style="text-align:right; padding-right:8px;<?php echo GetPrintLabelVal($config_codes_list[19],19,$voucher_series_id);?>">
                    Authorised Signatory
                </p>
            </td>
        </tr>
    </tbody>
    </table>
</div></div>
      
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
		<input type="text" name="rcpt_title" id="rcpt_title" class="form-control" value="<?php echo $title_value;?>">
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

$("#rcpt_title").keyup(function(event) {
	$("#print_receipt_label").html($(this).val());
});

/************************** Start ************************/
$("#prnt_typeid").on("change",function(){
 if($(this).val()=="8"){
	 $("#rcpt_label").show();
 }else{
	 $("#rcpt_label").hide();
	 $("#rcpt_title").val("");
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
  else if(label_type=="21")
      print_fontstyle_fun('print_forcompany_val',fontstyle);
  else if(label_type=="36")
      print_fontstyle_fun('print_footer_amount',fontstyle);
  else if(label_type=="20")
      print_fontstyle_fun('print_footer_amountinwords',fontstyle);
  else if(label_type=="19")
      print_fontstyle_fun('print_footer_signatory',fontstyle);
  else if(label_type=="7")
      print_fontstyle_fun('print_cin_fld',fontstyle);
   else if(label_type=="27")
	 print_fontstyle_fun('print_cin_val',fontstyle);   
   
});
/*************    Arial, Tiems New Roman, VErdana ******/
$("#prnt_type_fontname").on("change",function(){
	var fontname = $(this).find('option:selected').text();	
	var label_type = $("#prnt_typeid").find('option:selected').val();
	
	if(label_type=="8")
      print_fontname_fun('print_receipt_label',fontname);	 
   else if(label_type=="9")
      print_fontname_fun('print_company_name',fontname);	 
   else if(label_type=="34")
      print_fontname_fun('print_company_address',fontname);	 
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
  else if(label_type=="21")
      print_fontname_fun('print_forcompany_val',fontname);
  else if(label_type=="36")
      print_fontname_fun('print_footer_amount',fontname);
  else if(label_type=="20")
      print_fontname_fun('print_footer_amountinwords',fontname);
 else if(label_type=="19")
      print_fontname_fun('print_footer_signatory',fontname);
 else if(label_type=="7")
      print_fontname_fun('print_cin_fld',fontname);
   else if(label_type=="27")
	 print_fontname_fun('print_cin_val',fontname);   
	
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
  else if(label_type=="21")
      print_fontsize_fun('print_forcompany_val',fontsize);
  else if(label_type=="36")
      print_fontsize_fun('print_footer_amount',fontsize);
  else if(label_type=="20")
      print_fontsize_fun('print_footer_amountinwords',fontsize);
 else if(label_type=="19")
      print_fontsize_fun('print_footer_signatory',fontsize);
 else if(label_type=="7")
      print_fontsize_fun('print_cin_fld',fontsize);
   else if(label_type=="27")
	 print_fontsize_fun('print_cin_val',fontsize); 	 
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
  else if(label_type=="21")
    var fldval=   print_field_value('print_forcompany_val');
  else if(label_type=="36")
    var fldval=   print_field_value('print_footer_amount');
  else if(label_type=="20")
    var fldval=   print_field_value('print_footer_amountinwords');
  else if(label_type=="19")
	 var fldval=   print_field_value('print_footer_signatory'); 
   else if(label_type=="7")
    var fldval=   print_field_value('print_cin_fld');
   else if(label_type=="27")
	var fldval=  print_field_value('print_cin_val');
 return fldval;
}

function print_field_value(id){
	return $("#"+id).attr("style");
}

function print_fontname_fun(id,fontname){
	 $("#"+id).css("font-family",'' );
	 $("#"+id).css("font-family",fontname );
}

function print_fontsize_fun(id,fontsize){
	  $("#"+id).css("font-size",'')
	  $("#"+id).css("font-size", fontsize + "px");
}

function print_fontstyle_fun(id,fontstyle){
	if(fontstyle=='bold'){	
	  if($("#"+id).hasClass('font-weight-aplied')){
		$("#"+id).removeClass("font-weight-aplied");
		$("#"+id).css("font-weight", '');
	  } else{
		$("#"+id).addClass("font-weight-aplied");	    
	    $("#"+id).css("font-weight", 'bold');
	  }
	}
  else if(fontstyle=='italic'){	
	  if($("#"+id).hasClass('font-style-aplied')){
		$("#"+id).removeClass("font-style-aplied");
		$("#"+id).css("font-style", '');
	  } else{
		$("#"+id).addClass("font-style-aplied");	    
	    $("#"+id).css("font-style", 'italic');
	  }
	}	
  else if(fontstyle=='underline'){	
	  if($("#"+id).hasClass('text-decoration-aplied')){
		$("#"+id).removeClass("text-decoration-aplied");
		$("#"+id).css("text-decoration", '');
	  } else{
		$("#"+id).addClass("text-decoration-aplied");	    
	    $("#"+id).css("text-decoration", 'underline');
	  }
	}	
}
 var config_codes_list = <?php echo json_encode($config_codes_list);?>;
/************************** FUNCTION END ************************/
$("#prntingfrm").on("submit",function(){
	   
	    var erpprevaln_label_id = $("#prnt_typeid").find('option:selected').val();
        var fldstylevalue = get_field_style(erpprevaln_label_id);
        var form = $("#prntingfrm");
        var formData = new FormData(this);
		formData.append("fldstyle", fldstylevalue);
		formData.append("type", '<?php echo $type;?>');
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
