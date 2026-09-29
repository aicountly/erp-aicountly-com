<?php $header = array( 	'title' => 'Inward Supply 2A/2B Report' ); ?>
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
</style>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">Inward Supply 2A/2B Report</h3></div>
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
<div class="row mb-2 align-items-top">
    <div class="col-lg-3">
        <form class="form needs-validation" method="get" id="salefrm" autocomplete="off" novalidate>
        <div class="form-check form-check-inline me-2">
            <input  class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
            <label class="form-check-label" for="fg">Fixed Grid</label>

        </div> 
		
		<input type="hidden" name="fromdate" value="<?php echo date('Y-m-d',strtotime($from_date));?>">
		<input type="hidden" name="todate" value="<?php echo date('Y-m-d',strtotime($to_date));?>">
       <input type="hidden" name="fltrtype" value="<?php echo $fltrtype;?>">
		
	   </form>
    </div>
    <div class="col-lg-9 text-end">
		 <div class="form-check form-check-inline me-2">
            <input  form="salefrm" class="form-check-input" type="radio" value="2" name="gstrradio" id="gstrradio2" onchange="this.form.submit()" <?php if($gstrradio=='2') echo 'checked';?>>
            <label class="form-check-label" for="gstrradio2">GSTR-2</label>
        </div> 
		 <div  class="form-check form-check-inline me-2">
            <input  form="salefrm" class="form-check-input" type="radio" value="2A" name="gstrradio" id="gstrradio2a" onchange="this.form.submit()" <?php if($gstrradio=='2A') echo 'checked';?>>
            <label class="form-check-label" for="gstrradio2a">GSTR-2A</label>
        </div> 
		 <div  class="form-check form-check-inline me-2">
            <input  form="salefrm" class="form-check-input" type="radio" value="2B" name="gstrradio" id="gstrradio2b" onchange="this.form.submit()" <?php if($gstrradio=='2B') echo 'checked';?>>
            <label class="form-check-label" for="gstrradio2b">GSTR-2B</label>
        </div> 
        <div class="dropdown d-inline-block me-1" style="width:220px;">
		 
          <div class="input-group input-group-sm input-group">		 
              <span class="input-group-text">View</span>
              <select form="salefrm" class="form-select" name="view" onchange="this.form.submit()">
                   <option value=""></option>
				  <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
                  <option <?= ($view==1) ? 'selected' : '' ?> value="1">Detailed</option>				 
              </select>
              
          </div>  
        </div>
		 
        <div class="dropdown float-end">
            
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
             <a href="<?php echo history_back();?>"  class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title" id="moreoptionslable">Apps</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <div class="row">
   
       <div class="col-sm-6 border-end"> 
       <h5 class="pb-3">Horizontal</h5>
       
      <p class="offcanvaoptions"><i>Condensed</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>Detailed</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>All Labels</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
       </div>
       
       <div class="col-sm-6"> 
       <h5 class="pb-3">Verticle</h5>
       
      <p class="offcanvaoptions"><i>Verticle</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
      
      <p class="offcanvaoptions"><i>Schudle</i>
      <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
      <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
      <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
      </p>
       </div>
       
        <div class="col-sm-12 pt-3 border-top"> 
      <p class="offcanvaoptions"><i>Schedule</i>
      <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap"></label>
      <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap"></label>
      </p>
      <p class="offcanvaoptions"><i>Ratio</i>
      <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap"></label>
      <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap"></label>
      </p>
      
      <p class="text-center pt-3"><a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a></p>
        
        </div>
       
          
      </div>
   
  </div>
</div>


<div class="row">
    <div class="col-md-6">
	<?php
	$preview_valid=0;
	$fltrtype_info = unobfuscate_link($fltrtype);
	 if($fltrtype_info[1]=='mnth'){
		 $preview_valid=1;
		 $filter_labels = date('M, Y',strtotime($from_date));
	 }
	else if($fltrtype_info[1]=='qtr'){
		$preview_valid=1;
		 $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date));
	 }
	else if($fltrtype_info[1]=='yrs'){
		$preview_valid=0;
		 $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date));
	 }
  else if($from_date==$to_date){
	  $filter_labels = date('M, Y',strtotime($from_date));
	  $preview_valid=0;
  }
   else{
	   $filter_labels = date('M, Y',strtotime($from_date)) .' To '.date('M, Y',strtotime($to_date)); 
	   $preview_valid=0;
   }
   
    $filter_labels = date('d M, Y',strtotime($from_date)) .' To '.date('d M, Y',strtotime($to_date));

	?>
       <p><em>For: <strong><?php echo $filter_labels;?></strong></em></p>
    </div>
     
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin:auto;"> </div> 
<div class="col-12 text-center"> <br><br>
        
        <a data-type="2ab2b" data-fromdate="<?php echo $from_date;?>" data-todate="<?php echo $to_date;?>" href="javascript:void(0);"  class="getgstra_return btn btn-success btn-lg">Fetch GSTR-2A(B2B) </a>
         <a data-type="2b" data-fromdate="<?php echo $from_date;?>" data-todate="<?php echo $to_date;?>" href="javascript:void(0);"  class="getgstra_return btn btn-success btn-lg">Fetch GSTR-2B </a>
    
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
					</td><input type="hidden" id="returntype"value="">
					<input type="hidden" id="apptoken" name="apptoken" value="">
				
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
				
<?php echo view('includes/footer_scripts'); ?>
<script>
var frmdate = '<?php echo date('Y-m-d',strtotime($from_date));?>';
var todate = '<?php echo date('Y-m-d',strtotime($to_date));?>';
$(document).ready(function() {
	$('#add_otp_modal').modal({backdrop: 'static', keyboard: false});	
});
$(document).on("click",".getgstra_return",function(){
	var data_type=$(this).data("type");//"2ab2b"
	if(data_type=='2ab2b'){
		GenerateGstR2BApi_OTP(frmdate,todate,data_type);
	}
	if(data_type=='2b'){
		GenerateGstR2BApi_OTP(frmdate,todate,data_type);
	}
});


function GenerateGstR2AApi(fromdate,todate){
	var actiontype = $(this).data("type");
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
	
	function GenerateGstR2BApi_OTP(fromdate,todate,data_type){
	var frmdata = {"fromdate":fromdate,"todate":todate,token: "<?php echo date('ssYssHs');?>"};	
	$.ajax({
		  type: "POST",
		  url: baseurl+"/admin/etaxapis/generate_otp_request",
		  data: frmdata,		  
		  timeout: 5000, // 5 secods
		  success:function(response){			 
			  stop_loader();
			  	 if (typeof response === 'string') {
                    response = JSON.parse(response);
                 }
                 if(response.status){
					 // when otp request completed show message to enter otp number and validte otp number set in session , 
					// now 
					 $("#add_otp_modal #returntype").val(data_type);
					 $("#add_otp_modal #apptoken").val(response.apptoken);
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
$(document).on("click","#validate_otp_request",function(){
	var otp = $("#add_otp_modal #otptoken").val();
	var returntype = $("#add_otp_modal #returntype").val();
	var apptoken = $("#add_otp_modal #apptoken").val();
	if(returntype == '2ab2b' || returntype == '2b'){
		 if(otp === '' || isNaN(otp)){
		alert_notification("Enter Valid OTP!!");
		
	}
	else{
		$('#add_otp_modal').modal("hide");
		show_loader();
	var frmdata = {"apptoken":apptoken,"returntype":returntype,"to_date":"<?php echo $to_date;?>","from_date":"<?php echo $from_date;?>","otp":otp,token: "<?php echo date('ssYssHs');?>"};	
	$.ajax({
		  type: "POST",
		  url: baseurl+"/admin/etaxapis/fetch_gstr2ab_return_info",
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
	 }
	else{
		alert_notification("Something went wrong!!");
		return false;
		}
	
	
});


function isvalid_dates(){
	var preview_valid ='<?php echo $preview_valid;?>';
	if(preview_valid=="0"){
		alert_notification("GSTR-1 Preview not allowed for this period!!!");
		return false;
	}
	return true;
}
var pageview ='<?php echo $view;?>';
$(function () {
       function calculateSummary() { 
          		
        // calculate summary from page wise data  
        var invoiceTotal = 0,
            taxableTotal = 0,
            igstTotal = 0,
            cgstTotal = 0,
            sgstTotal = 0,
            cessTotal = 0,
            taxTotal = 0,
           data = $("#grid_search").pqGrid( "pageData" ),
			len = data.length;
			
        data.forEach(function(row){             
            invoiceTotal +=  parseAmount(row.sm_invoice_value);
            taxableTotal +=  parseAmount(row.sm_taxable_value);
            igstTotal +=  parseAmount(row.sm_igst);
            cgstTotal +=  parseAmount(row.sm_cgst);
            sgstTotal +=  parseAmount(row.sm_sgst);
            cessTotal +=  parseAmount(row.sm_cess);
             taxTotal +=  parseAmount(row.sm_total_tax);

        })

        var totalData = {
                tableno: "Total",
                table_name :"",
                total_records:"",
				invoice_value:formatAmount(invoiceTotal),
				taxable_value:formatAmount(taxableTotal),
				igst :formatAmount(igstTotal),
                cgst:formatAmount(cgstTotal),
				sgst:formatAmount(sgstTotal),
				cess:formatAmount(cessTotal),
                total_tax: formatAmount(taxTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            
       
        this.option('summaryData', [totalData]);
		
    }  
         
    
    
        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = '',//$toolbar.find(".filterCondition").val(),
                dataIndx = '',//$toolbar.find(".filterColumn").val(),
                filterObject;

            if (dataIndx == "") {//search through all fields when no field selected.
                filterObject = [];
                var CM = $grid.pqGrid("getColModel");
                for (var i = 0, len = CM.length; i < len; i++) {
                    var dataIndx = CM[i].dataIndx;
                    filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
                }
            }
            else {//search through selected field.
                filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
            }
            $grid.pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            });
        }
        //filterRender to highlight matching cell text.
        function filterRender(ui) {
            var val = ui.cellData,
                filter = ui.column.filter;
            if (filter && filter.on && filter.value) {
                var condition = filter.condition,
                    valUpper = val.toUpperCase(),
                    txt = filter.value,
                    txt = (txt == null) ? "" : txt.toString(),
                    txtUpper = txt.toUpperCase(),
                    indx = -1;
                if (condition == "end") {
                    indx = valUpper.lastIndexOf(txtUpper);
                    //if not at the end
                    if (indx + txtUpper.length != valUpper.length) {
                        indx = -1;
                    }
                }
                else if (condition == "contain") {
                    indx = valUpper.indexOf(txtUpper);
                }
                else if (condition == "begin") {
                    indx = valUpper.indexOf(txtUpper);
                    //if not at the beginning.
                    if (indx > 0) {
                        indx = -1;
                    }
                }
                if (indx >= 0) {
                    var txt1 = val.substring(0, indx);
                    var txt2 = val.substring(indx, indx + txt.length);
                    var txt3 = val.substring(indx + txt.length);
                    return txt1 + "<span style='background:yellow;color:#333;'>" + txt2 + "</span>" + txt3;
                }
                else {
                    return val;
                }
            }
            else {
                return val;
            }
        }
        var colModel = [
            { title: "TABLE NO", dataIndx: "tableno", width: 100,sortable:false},
            { title: "TABLE NAME", width: 250, dataIndx: "table_name",sortable:false },
            { title: "NO. OF RECORDS", width: 100, dataIndx: "total_records",sortable:false},             
            { title: "INVOICE VALUE", width: 100, dataIndx: "invoice_value",sortable:false},
            { title: "TAXABLE VALUE", width: 100, dataIndx: "taxable_value",sortable:false},  
            { title: "IGST", width: 100, align: "right", dataIndx: "igst",sortable:false},
            { title: "CGST", width: 100, align: "right", dataIndx: "cgst",sortable:false},
			{ title: "SGST", width: 100, align: "right", dataIndx: "sgst",sortable:false},
			{ title: "CESS", width: 100, align: "right", dataIndx: "cess",sortable:false},
            { title: "TOTAL TAX", width: 100, dataType: "string", dataIndx: "total_tax",sortable:false},		   
	 	    ];
      
         var loadStateSuccess;
		 var minWidth='flex'; 
		 var scrollModel={ autoFit: true };
		
        var newObj = {
            scrollModel: scrollModel,
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: "flex",
			minWidth: minWidth,
			selectionModel: { type: 'row',mode:'single' },
            dataModel: <?php echo $response;?>,
            dataReady: calculateSummary,
			colModel: colModel,  
			wrap:false,
            numberCell: { show: false },
            filterModel: { mode: 'OR', type: "local" },
            pageModel: { type: "local", rPP: 100, strRpp: "{0}" },
            editable: false,
            showTitle: false,
            create: function (evt, ui) {// make first row auto selected
			var grid = this,
                $select_row = $(".select-row"),
                data = ui.dataModel.data;
				var rowIndx =0;
             loadStateSuccess = this.loadState({ refresh: false });
			 const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);
                    grid.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                }
                else{
                    grid.setSelection({ rowIndx: grid.rowIndxOffset, focus: true });
                } 
             
            },	
			
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { keyup: filterhandler }
                    },                    
                ]
            }
        };
        
        newObj.rowDblClick = function(event, ui) {
             $("#grid_search").pqGrid('saveState');
  	            var rowData    = ui.rowData;
				var txn_bo_id = rowData.bo_id;
  	            var col_type = rowData.col_type;
  	            var ajax   = rowData.ajax;
		        var htableno   = rowData.htableno;
		        var from_date   = rowData.from_date;
		        var to_date   = rowData.to_date;
				
				var select_rowindx =  set_page();
                window.location.href= baseurl+"/admin/gst/gstr2abledger?fromdate="+from_date+"&todate="+to_date+"&tableno="+htableno+"&rowIndx="+select_rowindx;
                      
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	        
	           var rowData     = ui.rowData;
		    var txn_bo_id = rowData.bo_id;
		        var ajax   = rowData.ajax;
		        var col_type = rowData.col_type;
		        var voucher_txn_id   = rowData.voucher_txn_id;
		        var voucher_type_id   = rowData.voucher_type_id;
		        if (evt.keyCode==13){               
                $("#grid_search").pqGrid('saveState');
				var txn_bo_id  = rowData.bo_id;
  	            var col_type   = rowData.col_type;
  	            var ajax       = rowData.ajax;
		        var htableno   = rowData.htableno;
		        var from_date  = rowData.from_date;
		        var to_date    = rowData.to_date;
				var select_rowindx =  set_page();
                window.location.href= baseurl+"/admin/gst/gstr2abledger?fromdate="+from_date+"&todate="+to_date+"&tableno="+htableno+"&rowIndx="+select_rowindx;
                }
		   
	       }
        
    var $grid = $("#grid_search").pqGrid(newObj);

    $("#grid_search").pqGrid('loadState');

    $(window).unload( function(){
      $("#grid_search").pqGrid('saveState');
    });
    
     
    });
	
	function set_page()
    {
       var select_row = $("#grid_search").pqGrid("selection", { type:'row', method:'getSelection'});
        if(select_row){
            const url = new URL(window.location.href);
            url.searchParams.set('rowIndx', select_row[0].rowIndx);
            window.history.replaceState(null, null, url);
			return select_row[0].rowIndx;
        }
         else{
            const url = new URL(window.location.href);
            if(url.searchParams.has('rowIndx')){
                url.searchParams.delete('rowIndx');
                window.history.replaceState(null, null, url);  
            }if(url.searchParams.has('duplc')){
                url.searchParams.delete('duplc');
                window.history.replaceState(null, null, url);  
            }
			return 0;
        }  		
    }
    
  $(document).on('change', '#fg', function(){
    if($(this).is(":checked")) {
      $("#grid_search").pqGrid('option', 'height', 420);
    }
    else{
      $("#grid_search").pqGrid('option', 'height', 'flex');
    }
    $("#grid_search").pqGrid('refreshDataAndView');
  });


$('input[name="gstrradio"]').on('change', function () {
    if($(this).is(":checked")) {
      $("#grid_search").pqGrid('option', 'height', 420);
    }
    else{
      $("#grid_search").pqGrid('option', 'height', 'flex');
    }
    $("#grid_search").pqGrid('refreshDataAndView');
  });
  


  
  function print_excel(){
			var from_date = '<?php echo $from_date;?>'; 
			var to_date   = '<?php echo $to_date;?>';
            var view      = '<?php echo $view;?>'; 
			
            if(view == 0){
                var stringparameters = "view="+view+"&from_date="+from_date+"&to_date="+to_date;
               // window.location.href= baseurl+"/admin/export/gstr_report_detail?"+stringparameters;
            }
			if(view == 1){
                var stringparameters = "view="+view+"&from_date="+from_date+"&to_date="+to_date;
              //  window.location.href= baseurl+"/admin/export/gstr_report_detail?"+stringparameters;
            }
        }

</script>	<style>.hidden{display:none;}</style></body></html>