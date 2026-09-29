<?php $header = array( 	'title' => 'eGSTR-1 Summary' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>
<style>
/*for autocomplete */
.ui-autocomplete {
    z-index:9999!important;
}
.pq-sb-horiz-t .pq-sb-slider, .pq-sb-vert-t .pq-sb-slider, .pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{background-color: rgb(220, 254, 211) !important;}
.antiqueWhite {
    background-color: #f9f9f9;
    color: #254475;
    font-weight: bold;
}

.ng-cloak, .x-ng-cloak, .ng-hide:not(.ng-hide-animate) {
    display: none !important;
}
</style>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">eGSTR-1 Summary</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
			<li><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       
    </div>
    </div>
</div>



<div class="row">
    <div class="col-md-6">
	<?php
	 $fltrtype_info = unobfuscate_link($fltrtype_label);
	 if($fltrtype_info[1]=='mnth'){
		 $filter_labels = date('M, Y',strtotime($from_date));
	 }
	 if($fltrtype_info[1]=='qtr'){
		 $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date));
	 }
	 if($fltrtype_info[1]=='yrs'){
		 $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date));
	 }

	?>
        <p><em>For: <strong><?php echo $filter_labels;?></strong></em></p>
    </div><div class="col-md-6 text-end">
       <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
    </div>
     
</div>

<div class="row">
 <div class="col-md-1">
 </div>
    <div class="col-md-10">
      <table class="table table-bordered table-responsive" cellspacing="5" cellpadding="5">

                            <thead style="background:#F8F8F8 ">
                                <tr style="background-color:#c9ece1; color: black;">

                                    <th class="text-center verticalCenter" style="display: table-cell;" rowspan="2" colspan="2">Description
                                       
                                    </th>
                                    <th class="text-center verticalCenter" rowspan="2" data-ng-bind="trans.LBL_PDF_TABLE_HEAD1">No. of records</th>
                                    <th class="text-center verticalCenter" style="padding-left: 19px;padding-right: 19px;" rowspan="2" data-ng-bind="trans.HEAD_DOC_TYPE">Document Type</th>

                                    <th class="text-center verticalCenter" rowspan="2" data-ng-bind="trans.HEAD_TAX_VALUE">Value (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_INT_TAX">Integrated tax (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_CENTR_TAX">Central tax (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_STATE_UAT_TAX">State/UT tax (₹)</th>
                                    <th class="text-center verticalCenter" data-ng-bind="trans.LBL_CESS_TAX">Cess (₹)</th>
                                </tr>
                            </thead>
                            <tbody>

                               <?php if(isset($response['data'])){
								foreach($response['data'] as $row){ ?>
								<?php 
									  $exploded = explode("_",$row['tableno']);
									if(isset($exploded[1]))
									  $tname=  $row['table_name'];
								   else{
									  $tname =  $row['tableno'].'-'.$row['table_name'];								
									 echo ' <tr>
                                    <td class="antiqueWhite" colspan="9">'.$tname.'</td>
                                </tr>';
								   }
								   
								   if(isset($exploded[1])){
									  $tname=  $row['table_name'];
									  ?>
									  <tr class="">
                                    <td colspan="2"><?php echo $tname;?></td>
                                    <td class="verticalCenter text-center" data-ng-bind="b2b4adat.ttl_rec"><?php echo $row['total_records'];?></td>
                                    <td class="verticalCenter text-center">Invoice</td>
									<td class="verticalCenter text-right"><?php echo $row['taxable_value'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['igst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['sgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cess'];?></td>
                                    
                                </tr>
									  <?php
									  
								   }
								   else{
								?> 		
								
								<tr class="">
                                    <td colspan="2">Total</td>
                                    <td class="verticalCenter text-center" data-ng-bind="b2b4adat.ttl_rec"><?php echo $row['total_records'];?></td>
                                    <td class="verticalCenter text-center">Invoice</td>
									<td class="verticalCenter text-right"><?php echo $row['taxable_value'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['igst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['sgst'];?></td>
                                    <td class="verticalCenter text-right"><?php echo $row['cess'];?></td>
                                    
                                </tr>
								   <?php } ?>
								
							   <?php }} ?>
								
                            </tbody>
                        </table>
	  
    </div>
  <div class="col-md-1">
  </div>  
</div>
<div id="validation_errors"></div>
<div class="col-12 text-center">
       <br><br>
       <a href="javascript:void(0);"  onClick="print_page();" class="btn btn-success btn-lg">DOWNLOAD(PDF)</a>
       &nbsp;&nbsp; <a href="javascript:void(0);" id="generategstr1" data-fromdate="<?php echo obfuscate_link($from_date);?>" data-todate="<?php echo obfuscate_link($to_date);?>"  class="btn btn-success btn-lg">GENERATE JSON(GSTR-1)</a>
		<!--&nbsp;&nbsp; <a href="javascript:void(0);" id="generategstrones--" data-fromdate="<?php echo obfuscate_link($from_date);?>" data-todate="<?php echo obfuscate_link($to_date);?>"  class="btn btn-success btn-lg">GENERATE(GSTR-1) Online</a>-->
		&nbsp;&nbsp; <a href="javascript:void(0);" id="generategstriff" data-fromdate="<?php echo obfuscate_link($from_date);?>" data-todate="<?php echo obfuscate_link($to_date);?>"  class="btn btn-success btn-lg">GENERATE JSON(IFF)</a>
    </div>
	
<div class="modal fade pt-5" id="add_otp_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">OTP Details</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					   <table class="table" cellspacing="0">
					  <tr>
					  <th>Enter OTP</th>
					  </tr>
					  <tbody>
				     <tr>
					 <td><input type="text" name="otptoken" id="otptoken" value="" min="6" max="6" maxlength="6" class="form-control" required>
					</td>
				
					</tr>
					 </tbody>
					  </table>
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="validate_otp_request">Save</button>
                      </div>
                    </div>
                  </div>
                </div>

				<div class="modal fade pt-5" id="add_preference_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Preference Details</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					   PREFERENCE IS NOT UPDATED, PLS UPDATE PREFERENCE FIRST
					   <table class="table" cellspacing="0">
					  <tr>
					  <th>Choose</th>
					  </tr>
					  <tbody>
				     <tr>
					 <td>
					   <div class="form-check">
						  <input class="form-check-input" type="radio" name="preferences" id="preferencesm" checked>
						  <label class="form-check-label" for="preferencesm">
							Monthly
						  </label>
						</div>
						<div class="form-check">
						  <input class="form-check-input" type="radio" name="preferences" id="preferencesq">
						  <label class="form-check-label" for="preferencesq">
							QUARTERLY
						  </label>
						</div>
					</td>
				
					</tr>
					 </tbody>
					  </table>
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="validate_preference_request">Save</button>
                      </div>
                    </div>
                  </div>
                </div>	
	
<?php echo view('includes/footer_scripts'); ?>
<script>
$(document).ready(function() {
	$('#add_otp_modal').modal({backdrop: 'static', keyboard: false});	
});


$(document).on("click","#validate_preference_request",function(){
	$('#add_preference_modal').modal("hide");
	show_loader();
	var frmdata = {"to_date":"<?php echo $to_date;?>","from_date":"<?php echo $from_date;?>",token: "<?php echo date('ssYssHs');?>"};	
	$.ajax({
		  type: "POST",
		  url: baseurl+"/admin/etaxapis/save_gstrone_preferences",
		  data: frmdata,
		  dataType: "json",
		  timeout: 5000, // 5 secods
		  success:function(response){
			  stop_loader();
			  	 if (typeof response === 'string') {
                    response = JSON.parse(response);
                 }
                 if(response.status){
				    alert_success(response.message);
					
				}else{
					
				 if(response.errors){
					var list = ``;
										$.each(response.errors, function(index, value){
											list += `<li>${value}</li>`;
										});

										var html = `<ul style="list-style: none;"><em>${list}</em></ul>`;	
					alert_notification(html);
					return false;
					}
				}	  
		    },
		   error: function (jqXHR, exception){
			    stop_loader();
                var error_= '';
                if (jqXHR.status === 0) {
                    error = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    error = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } 
                alert_notification(error);
             },		  
        });
	
});

$(document).on("click","#validate_otp_request",function(){
	var otp = $("#add_otp_modal #otptoken").val();
	if(otp === '' || isNaN(otp)){
		alert_notification("Enter Valid OTP");
		
	}else{
		$('#add_otp_modal').modal("hide");
		show_loader();
	var frmdata = {"to_date":"<?php echo $to_date;?>","from_date":"<?php echo $from_date;?>","otp":otp,token: "<?php echo date('ssYssHs');?>"};	
	$.ajax({
		  type: "POST",
		  url: baseurl+"/admin/etaxapis/generate_gstrone",
		  data: frmdata,
		  dataType: "json",
		  timeout: 10000, // 5 secods
		  success:function(response){
			  stop_loader();
			  	 if (typeof response === 'string') {
                    response = JSON.parse(response);
                 }
                 if(response.status){
				    alert_success(response.message);
					
				}else{
					if(response.show_preference_modal=="1"){
						// show preference modal box and ask to select MONTHLY or QUARTERLY run preference api
						$("#add_preference_modal").modal("show");
						
					}
					
					
				 if(response.show_preference_modal=="0" && response.errors){
					var list = ``;
										$.each(response.errors, function(index, value){
											list += `<li>${value}</li>`;
										});

										var html = `<ul style="list-style: none;"><em>${list}</em></ul>`;	
					alert_notification(html);
					return false;
					}
				}	  
		    },
		   error: function (jqXHR, exception){
			    stop_loader();
                var error_= '';
                if (jqXHR.status === 0) {
                    error = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    error = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } 
                alert_notification(error);
             },		  
        });
		
	}
	
});


$(document).on("click","#generategstr1",function(){
	show_loader();
	var fltrtype = '<?php echo $fltrtype;?>';
	if(fltrtype=='yrs'){
		stop_loader();
	 alert_notification("Json Can Only Be Generated For Month / Quarter. Pls Change The Reporting Period.");	
	 return false;
	}else{
	 var fromdate  = $(this).attr("data-fromdate");
	 var todate    = $(this).attr("data-todate");
	 GenerateGstR1(fromdate,todate);
	}
});

$(document).on("click","#generategstrones",function(){
	show_loader();
	var fltrtype = '<?php echo $fltrtype;?>';
	if(fltrtype=='yrs'){
	 alert_notification("Json Can Only Be Generated For Month / Quarter. Pls Change The Reporting Period.");	
	 return false;
	}else{
	 var fromdate  = $(this).attr("data-fromdate");
	 var todate    = $(this).attr("data-todate");
	 GenerateGstR1Api(fromdate,todate);
	}
});

$(document).on("click","#generategstriff",function(){
	show_loader();
	var fltrtype = '<?php echo $fltrtype;?>';
	if(fltrtype=='yrs'){
	 alert_notification("Json Can Only Be Generated For Month / Quarter. Pls Change The Reporting Period.");	
	 return false;
	}else{
	 var fromdate  = $(this).attr("data-fromdate");
	 var todate    = $(this).attr("data-todate");
	 GenerateGstIFF(fromdate,todate);
	}
});

function GenerateGstR1(fromdate,todate){
	var frmdata = {"fromdate":fromdate,"todate":todate};	
	$.ajax({
		  type: "POST",
		  url: baseurl+"admin/etaxes/download_gstr1_json",
		  data: frmdata,
		  dataType: "json",
		  success:function(response){
			  stop_loader();
			  	 if (typeof response === 'string') {
                    response = JSON.parse(response);
                 }
                 if(response.status){
                    alert_success(response.message);
					var blob = new Blob([response.jsonfile], {
					type: 'application/json'
				  });
				  var link      = document.createElement('a');
				  link.href     = window.URL.createObjectURL(blob);
				  link.download = response.filename;
				  link.click();
				}else{
				// alert_notification(response.message);	
				 if(response.errors){

    let htmlErrors = '';

    // =============================
    // GENERAL ERRORS
    // =============================
    if(response.errors.general){
        htmlErrors += `
            <div class="mb-3">
                <h6 class="text-danger fw-bold">General Errors</h6>
                <ul>
        `;

        $.each(response.errors.general, function(i, err){
            htmlErrors += `<li>${err}</li>`;
        });

        htmlErrors += `</ul></div>`;
    }

    // =============================
    // INVOICE ERRORS
    // =============================
    if(response.errors.invoices){

        $.each(response.errors.invoices, function(inv, errs){

            htmlErrors += `
                <div class="mb-3 p-2 border rounded bg-light">
                    <h6 class="text-primary fw-bold mb-1">
                        Invoice: ${inv}
                    </h6>
                    <ul class="mb-0">
            `;

            $.each(errs, function(i, e){
                htmlErrors += `<li>${e}</li>`;
            });

            htmlErrors += `</ul></div>`;
        });
    }

    // =============================
    // HSN ERRORS
    // =============================
    if(response.errors.hsn){

        htmlErrors += `
            <div class="mb-3">
                <h6 class="text-warning fw-bold">HSN Errors</h6>
                <ul>
        `;

        $.each(response.errors.hsn, function(i, err){
            htmlErrors += `<li>${err}</li>`;
        });

        htmlErrors += `</ul></div>`;
    }

    let html = `
    <div class="modal fade" id="jsonErrorModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">

          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">GST JSON Validation Errors</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" style="max-height:500px; overflow:auto;">
            
            ${htmlErrors}

            <hr>

            <button class="btn btn-success" id="viewJsonBtn">
                View JSON File
            </button>

            <pre id="jsonPreview" style="display:none; max-height:400px; overflow:auto; background:#000; color:#0f0; padding:10px;"></pre>

          </div>
        </div>
      </div>
    </div>
    `;

    // remove old modal if exists
    $('#jsonErrorModal').remove();

    $('body').append(html);
    $('#jsonErrorModal').modal('show');

    $('#viewJsonBtn').off('click').on('click', function(){
        $('#jsonPreview').toggle().text(response.jsonfile);
    });
}
				}	  
		    },
		   error: function (jqXHR, exception){
			    stop_loader();
                var error_= '';
                if (jqXHR.status === 0) {
                    error = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    error = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } 
                alert_notification(error);
             },		  
        }); 
    }
	
	function GenerateGstR1Api(fromdate,todate){
	var frmdata = {"fromdate":fromdate,"todate":todate,token: "<?php echo date('ssYssHs');?>"};	
	$.ajax({
		  type: "POST",
		  url: baseurl+"/admin/etaxapis/generate_otp_request",
		  data: frmdata,
		  dataType: "json",
		  timeout: 5000, // 5 secods
		  success:function(response){
			  stop_loader();
			  	 if (typeof response === 'string') {
                    response = JSON.parse(response);
                 }
                 if(response.status){
					 // when otp request completed show message to enter otp number and validte otp number set in session , 
					// now 
					 $("#add_otp_modal").modal("show");
					 
                   // alert_success(response.message);
					
				}else{
				 if(response.errors){
					var list = ``;
										$.each(response.errors, function(index, value){
											list += `<li>${value}</li>`;
										});

										var html = `<ul style="list-style: none;"><em>${list}</em></ul>`;	
					alert_notification(html);
					return false;
					}
				}	  
		    },
		   error: function (jqXHR, exception){
			    stop_loader();
                var error_= '';
                if (jqXHR.status === 0) {
                    error = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    error = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } 
                alert_notification(error);
             },		  
        }); 
    }
	function GenerateGstIFF(fromdate,todate){
	var frmdata = {"fromdate":fromdate,"todate":todate};	
	$.ajax({
		  type: "POST",
		  url: baseurl+"admin/etaxes/download_gstr1iff_json",
		  data: frmdata,
		  dataType: "json",
		  success:function(response){
			  stop_loader();
			  	 if (typeof response === 'string') {
                    response = JSON.parse(response);
                 }
                 if(response.status){
                    alert_success(response.message);
					var blob = new Blob([response.jsonfile], {
					type: 'application/json'
				  });
				  var link      = document.createElement('a');
				  link.href     = window.URL.createObjectURL(blob);
				  link.download = response.filename;
				  link.click();
				}else{
				 alert_notification(response.message);	
				 if(response.errors)
                    {
                        var list = ``;
                        if(response.errors.length > 0){
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
				}	  
		    },
		   error: function (jqXHR, exception){
			    stop_loader();
                var error_= '';
                if (jqXHR.status === 0) {
                    error = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    error = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    error = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    error = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    error = 'Time out error.';
                } else if (exception === 'abort') {
                    error = 'Ajax request aborted.';
                } 
                alert_notification(error);
             },		  
        }); 
    }
function print_page() {
	var from_date = '<?php echo $from_date;?>'; 
	var to_date    = '<?php echo $to_date;?>';
    window.open(
        '<?php echo base_url(); ?>admin/eGSTR1Print?p=1&from_date='+from_date+'&to_date='+to_date,
        '_blank'
    ).focus();
}	
</script>
</body></html>