<?php $header = array(  'title' => 'E-INVOICE Summary' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.pq-grid-center .pq-grid-cont:has(> .pq-grid-norows){
text-indent: -9999px;
background-image: url("/public/assets/images/no_records_found.png");
background-repeat:no-repeat;
background-position: center center;
background-size: 400px 100px;
}
</style>
<div class="row mb-3">
  <div class="col-md-6 order-1 pb-1">
    <h3>E-INVOICE Summary</h3>
  </div>
  <div class="col-md-6 order-3 order-lg-2 text-end">
    <div class="taskmenus">
      <a class="hideinline-lg"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu">
        <span class="material-symbols-outlined">filter_list</span>
      </a>
      <a href="javascript:void(0)" id="refresh_grid">
        <span class="material-symbols-outlined">refresh</span>
      </a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span>
      </a>
      <a href="#">
        <span class="material-symbols-outlined open-comingsoon">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined open-comingsoon">download</span>
        </span>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a>
        </li>
      </ul>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined open-comingsoon">share</span>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="#">Facebook</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Twitter</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Instagram</a>
        </li>
      </ul>
      <a href="#" class="hideinline-lg">
        <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
      </a>
    </div>
  </div>

  <div class="col-lg-4 col-md-8 order-2 order-lg-3">
    <form class="form needs-validation" method="get" id="salefrm" autocomplete="off" novalidate>
      <div class="input-group input-group-sm">
        <span class="input-group-text px-1">From</span>
        <input type="text" name="fromdate" id="mfromdate" value="<?php echo $from_date;?>" class="datepicker form-control" required  style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input type="text" name="todate" id="mtodate" value="<?php echo $to_date;?>" class="datepicker form-control" required style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        
        <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
      </div>
    </form>
  </div>

  <div class="col-lg-8 text-lg-end order-4  collapse listmenu-lg" id="listmenu">
  <div class="dropdown d-inline-block me-1" style="width:220px;">
      <div class="input-group">
        <span class="input-group-text px-1">Filter</span>
        <select class="form-select ps-1" name="filter_recods" id="filter_recods">
          <option  value="1" selected>ALL RECORDS</option>
           <option value="2">PENDING E-INVOICE</option>
           <option value="3">CANCELLED E-INVOICE</option>
		   <option value="4">E-INVOICE GENERATED</option>
        </select>
      </div>  </div>
      <div class="dropdown float-end d-inline-block">
        <button class="btn btn-success btn-sm mt-0 m-1" type="button" id="generateeinvoice" > Generate E-INVOICE </button>
         <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add On </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="javascript:void(0);" id="downloadjson">Download Json</a>
          </li>
          
        </ul>
        <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="#">Action</a>
          </li>
          <li>
            <a class="dropdown-item" href="#">Another action</a>
          </li>
          <li>
            <a class="dropdown-item" href="#">Something else here</a>
          </li>
        </ul>

        <div class="form-check form-check-inline me-2">
          <input  class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
          <label class="form-check-label" for="fg">Fixed Grid</label>
        </div>
        <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success showinline-lg">Back</a>
      </div>
    </div>
  </div>

<!-- The Modal -->
<div class="modal fade" id="ClearRepotingTablesModal" data-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Generating E-Way </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Tables: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25
		</div>
		
		<div class="text-center my-2 fault_status" style="display:none;">
			<div class="row mt-5">
      <div class="col-md-6">
        <h4>Validated
          (<span id="eway_valid_count">0</span>)
        </h4>
        <ul id="eway_validated_list" class="list-group" style="height:100px; overflow-y:auto"></ul>
      </div>
      
      <div class="col-md-6">
        <h4>Errors
          (<span id="eway_errors_count">2</span>)
        </h4>
        <ul id="eway_errors_list" class="list-group" style="height:100px; overflow-y:auto">
		<li class="list-group-item">Group 1- Primary "Y" or "N" invalid</li>
		<li class="list-group-item">Group 2- Primary "Y" or "N" invalid</li>
		</ul>
      </div>
      
         
    </div>
		</div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	    <button type="button" class="btn btn-success" id="refreshbtnrpt" style="display:none;">Refresh</button>&nbsp;
	   
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>


  <div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        
        <div class="col-12 calccard card m-auto">
          <form class="form" method="get" id="salefrm2" autocomplete="off">
            <input type="hidden" name="view" value="<?= $view ?>">
            <div class="row p-4">
              <div class="col-md-6 comp_calender">
                <button type="button" data-month="4" class="btn btn-light month_btn">APR</button>
                <button type="button" data-month="7" class="btn btn-light month_btn">JUL</button>
                <button type="button" data-month="10" class="btn btn-light month_btn">OCT</button>
                <button type="button" data-month="1" class="btn btn-light month_btn">JAN</button>
                <button type="button" data-month="5" class="btn btn-light month_btn">MAY</button>
                <button type="button" data-month="8" class="btn btn-light month_btn">AUG</button>
                <button type="button" data-month="11" class="btn btn-light month_btn">NOV</button>
                <button type="button" data-month="2" class="btn btn-light month_btn">FEB</button>
                <button type="button" data-month="6" class="btn btn-light month_btn">JUN</button>
                <button type="button" data-month="9" class="btn btn-light month_btn">SEP</button>
                <button type="button" data-month="12" class="btn btn-light month_btn">DEC</button>
                <button type="button" data-month="3" class="btn btn-light month_btn">MAR</button>
                <button type="button" data-quater="1" class="btn btn-qlight quater_btn">Q1</button>
                <button type="button" data-quater="2" class="btn btn-qlight quater_btn">Q2</button>
                <button type="button" data-quater="3" class="btn btn-qlight quater_btn">Q3</button>
                <button type="button" data-quater="4" class="btn btn-qlight quater_btn">Q4</button>
                <button type="button" data-hyear="1" class="btn btn-hlight hyear_btn">H1</button>
                <button type="button" data-hyear="2" class="btn btn-hlight hyear_btn">H2</button>
                
                <span class="fw-bold d-inline-block px-4">
                  <input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
                </div>
                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <button type="button" class="input-group-text" id="prev_year">
                    <span class="material-symbols-outlined">arrow_back_ios</span>
                    </button>
                    <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;">FY: </button>
                    <button type="button" class="input-group-text" id="next_year">
                    <span class="material-symbols-outlined">arrow_forward_ios</span>
                    </button>
                  </div>
                  
                  <div class="row align-items-center my-2">
                    <div class="col-md-2 fw-bold pe-0">From</div>
                    <div class="col-md-10">
                      <div class="calc-inputgroup">
                        <input type="text" class="form-control" fdprocessedid="cw7ydk" name="fromdate" id="fromdate" value="<?= $from_date ?>" placeholder="dd-mm-yyyy" required>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row align-items-center my-2">
                    <div class="col-md-2 fw-bold pe-0">To</div>
                    <div class="col-md-10">
                      <div class="calc-inputgroup">
                        <input type="text" class="form-control"  fdprocessedid="b20o4z" name="todate" id="todate" value="<?= $to_date ?>" placeholder="dd-mm-yyyy" required>
                      </div>
                    </div>
                  </div>
                  <p class="text-end">
                    <button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button>
                  </p>
                  
                </div>
                
                <p class="text-center pt-4">
                  <button type="submit" class="btn btn-lg btn-success">GO</button>
                  <button type="button" class="btn btn-lg btn-secondary" data-bs-dismiss="modal">Quit</button>
                </p>
                
              </div>
            </form>
            
          </div>
        </div>
      </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel">
      <div class="offcanvas-header">
        <h4 class="offcanvas-title" id="moreoptionslable">Apps</h4>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
        </button>
      </div>
      <div class="offcanvas-body">
        <div class="row">
          
          <div class="col-sm-6 border-end">
            <h5 class="pb-3">Horizontal</h5>
            
            <p class="offcanvaoptions">
              <i>Condensed</i>
              <label class="starcheck">
                <input type="checkbox" checked="checked">
                <b class="checkmark">★</b>
              </label>
              <label class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="swap">
              </label>
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="swap">
              </label>
            </p>
            
            <p class="offcanvaoptions">
              <i>Detailed</i>
              <label class="starcheck">
                <input type="checkbox" checked="checked">
                <b class="checkmark">★</b>
              </label>
              <label class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="swap">
              </label>
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="swap">
              </label>
            </p>
            
            <p class="offcanvaoptions">
              <i>All Labels</i>
              <label class="starcheck">
                <input type="checkbox" checked="checked">
                <b class="checkmark">★</b>
              </label>
              <label class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="swap">
              </label>
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="swap">
              </label>
            </p>
          </div>

          <div class="col-sm-6">
            <h5 class="pb-3">Verticle</h5>
            
            <p class="offcanvaoptions">
              <i>Verticle</i>
              <label class="starcheck">
                <input type="checkbox" checked="checked">
                <b class="checkmark">★</b>
              </label>
              <label class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="swap">
              </label>
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="swap">
              </label>
            </p>
            
            <p class="offcanvaoptions">
              <i>Schudle</i>
              <label class="starcheck">
                <input type="checkbox" checked="checked">
                <b class="checkmark">★</b>
              </label>
              <label class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="swap">
              </label>
              <label class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="swap">
              </label>
            </p>
          </div>
          
          <div class="col-sm-12 pt-3 border-top">
            <p class="offcanvaoptions">
              <i>Schedule</i>
              <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap">
            </label>
            <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap">
          </label>
        </p>
        <p class="offcanvaoptions">
          <i>Ratio</i>
          <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap">
        </label>
        <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap">
      </label>
    </p>
    
    <p class="text-center pt-3">
      <a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a>
    </p>
    
  </div>
  
  
</div>

</div>
</div>


<div id="validation_errors">
</div>
<div id="grid_search" style="margin:auto;"> </div>
<?php echo view('includes/footer_scripts'); ?>

<script>
$(document).on("change","#filter_recods",function(){
	var filterval = $(this).val();
	 $("#grid_search").pqGrid("option", "dataModel.postData", {
                "ewbval":filterval,from_date:"<?php echo $from_date;?>",to_date:"<?php echo $to_date;?>",view:"0"
    });
	$("#grid_search").pqGrid("refreshDataAndView");		
});
$(document).on("click","#downloadjson", function(){
	vouchers_listings = [];
	$('.eway_one_checkbox:checked').each(function () {
		var vchno    = $(this).attr("data-ajax");
		var vchtxnid = $(this).attr("data-id");
		vouchers_listings.push({
                   "table_id": vchtxnid,
                    "name" :vchno
                });
		});
	if(vouchers_listings.length==0){
		alert_notification("Choose voucher first to generate E-Invoice Json.");
		return false;
	}	
	else if(vouchers_listings.length >1){
		alert_notification("Choose only one voucher to generate E-Invoice Json.");
		return false;
	}else{
	 var vchtxnid = $('input[name="voucher_ids[]"]:checked').attr("data-id");
	 var fromdate = $('input[name="fromdate"]').val();
	 var todate   = $('input[name="todate"]').val();	 
	 $.ajax({
	        url: baseurl+"/admin/etaxes/generate_einvoice_json", 
	        type: 'POST',
	        data: {"todate":todate,"fromdate":fromdate,"vch_txn_id":vchtxnid,"token": "<?php echo date('ssYssHs');?>","table_id":vchtxnid},
	        dataType: "json",
			beforeSend: function() {
				show_loader();
	        },
	        success: function (response) {
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
				}
	        },
	        complete: function() {
	        },
	        error: function (jqXHR, exception) {
	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
				    error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {					
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    }); 	
	}	
});
$(document).on("click","#refreshbtnrpt", function(){
	var val_index = $(this).data("id");	
	clear_reprting_tables(val_index);
	$(this).hide();	
});

$(document).on('click', '.refresh_eway_status', function(){
	 show_loader();
	 var vhct_txn_id = $(this).data("id");
	 var ewayno      = $(this).data("ajax");
	 var tablelkey   = $(this).data("tablelkey");
	 var envid  = $(this).data("title");
	 $.ajax({
	        url: baseurl+"/admin/etaxapis/get_einvoice_detail", 
	        type: 'POST',
	        data: {'status':'update',vch_txn_id:vhct_txn_id,token: "<?php echo date('ssYssHs');?>","table_id": ewayno,"envid":envid,"tablelkey": tablelkey},
	        dataType: "json",
			timeout: 3000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
				stop_loader();
				if (typeof response === 'string') {
							  response = JSON.parse(response);
							  }		
	            if(response.status){	            
					$('#refresh_grid').trigger('click');	
				   alert_success("Status refreshed");
	            }
	            else{
	            	if(response.errors){
					var list = ``;
										$.each(response.errors, function(index, value){
											list += `<li>${value}</li>`;
										});

										var html = `<ul><em>${list}</em></ul>`;	
					 alert_notification(html);
					 return false;
					}
	            }
				 
	        },
	        complete: function() {
				stop_loader();
	        },
	        error: function (jqXHR, exception) {
				stop_loader();
	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
				    error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
				    error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    }); 
	 
	 
 });
 
$(function(){
	
	 $(document).on('change', '#eway_all_checkbox', function(){
        if(this.checked) 
            $('.eway_one_checkbox').prop("checked", true);
        else
            $('.eway_one_checkbox').prop("checked", false);
    });
	
	
	$('#ClearRepotingTablesModal').modal({backdrop: 'static', keyboard: false});	
	var get_rpt_tables_xhr='';
	var eway_valid_count=0;
	var eway_errors_count=0;
	var eway_validated_list='';
	var eway_errors_list='';
	var rpt_tables_list = [];
	
 $(document).on('click', '#generateeinvoice', function(){
	$("#eway_valid_count").html("0");
	$("#eway_errors_count").html("0");
	$("#eway_validated_list").html("");
	$("#eway_errors_list").html("");
	var eway_valid_count=0;
	var eway_errors_count=0;
	var eway_validated_list='';
	var eway_errors_list='';
	vouchers_listings = [];
	$('.eway_one_checkbox:checked').each(function () {
		var vchno    = $(this).attr("data-ajax");
		var vchtxnid = $(this).attr("data-id");
		vouchers_listings.push({
                   "table_id": vchtxnid,
                    "name" :vchno
                });
		});
		
	if(vouchers_listings.length==0){
		alert_notification("Choose voucher first to generate E-Invoice.");
		return false;
	}	
   else{		
	$('#ClearRepotingTablesModal .overall_status').show();
    $('#ClearRepotingTablesModal .current_status').show();
    $('#ClearRepotingTablesModal .progress-bar').show();
    $('#ClearRepotingTablesModal .progress').show();	
	$('#ClearRepotingTablesModal .close_btn').text('Cancel');
    $('#ClearRepotingTablesModal .close_btn').removeClass('btn-success').addClass('btn-danger');	
	
	error_eway_list = [];
	rpt_tables_index = 0;
	rpt_tables_list=[];
	$('#ClearRepotingTablesModal .modal-title').text('Generate E-Invoice');
	$('#ClearRepotingTablesModal .overall_status').text('Please wait while details are being fetched...');
	$('#ClearRepotingTablesModal .current_status').text('');
	$('#ClearRepotingTablesModal .progress-bar').css('width', '0%');
	$('#ClearRepotingTablesModal .progress-bar').text('0%');

	$('#ClearRepotingTablesModal').modal('show');
	get_rpt_tables_xhr = $.ajax({
        url: '<?php echo base_url();?>/admin/etaxes/load_einvoice_voucher_details', 
        type: 'POST',
        data: {"vouchers_listings":vouchers_listings},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {            
            if(response.status){
                rpt_tables_list = response.list;				
                var overall_status = 'Total Records: ' + rpt_tables_list.length;
                $('#ClearRepotingTablesModal .overall_status').text(overall_status);
                clear_reprting_tables(rpt_tables_index);
            }
            else{
            	$('#ClearRepotingTablesModal .overall_status').text('Something Went wrong');
            	$('#ClearRepotingTablesModal .current_status').text(response.message);
            }
        },
        complete: function() {
            // stop_loader();
        },
        error: function (jqXHR, exception) {

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
            } else {
                error = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            alert(error);
        },
    });
}	
});

var verify_rpt_tables_xhr;

function clear_reprting_tables(rpt_tables_index)
{   
    var eway_valid_count=0;
    var eway_errors_count=0;
	var progress = Math.round((rpt_tables_index/rpt_tables_list.length)*100);
	$('#ClearRepotingTablesModal .progress-bar').css('width', progress+'%');
	$('#ClearRepotingTablesModal .progress-bar').text(progress+'%');

	if(rpt_tables_index < rpt_tables_list.length){
		var current_status = `${(rpt_tables_index+1)} / ${rpt_tables_list.length} (${rpt_tables_list[rpt_tables_index].name})`;
		$('#ClearRepotingTablesModal .current_status').text(current_status);
		 verify_rpt_tables_xhr = $.ajax({
	        url: baseurl+"/admin/etaxapis/generate_einvoice", 
	        type: 'POST',
	        data: {vch_txn_id:rpt_tables_list[rpt_tables_index].table_id,token: "<?php echo date('ssYssHs');?>","table_id": rpt_tables_list[rpt_tables_index].table_id},
	        dataType: "json",
			timeout: 10000, // 10 secods
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
				if (typeof response === 'string') {
							  response = JSON.parse(response);
							  }		
	            if(response.status){
	                rpt_tables_index++;
					eway_valid_count++;		
					//eway_validated_list ='<li style="text-align:right;">AckNo: '+response.AckNo+',Irn: '+response.Irn+'</li>';
	               	eway_validated_list ='<table width="100%"><tr><td>AckNo No:'+response.AckNo+'</td></tr><tr><td>IRN No:'+response.Irn+'</td></tr></table>';
	               $("#eway_validated_list").html(eway_validated_list);
				   $("#eway_valid_count").html(eway_valid_count);
				   clear_reprting_tables(rpt_tables_index);
				   
	            }
	            else{
					if(response.errors){
					var list = ``;
										$.each(response.errors, function(index, value){
											list += `<li>${value}</li>`;
										});

										var html = `<ul style="list-style: none;"><em>${list}</em></ul>`;	
					 eway_errors_count++;
					 eway_errors_list ='<li style="text-align:right;">'+rpt_tables_list[rpt_tables_index].name+"<br />"+html+'</li>';	
					$("#eway_errors_list").html(eway_errors_list);
					$("#eway_errors_count").html(eway_errors_count);
					}
	            	
	            }
				 
	        },
	        complete: function() {
				$(".fault_status").show();				
	        },
	        error: function (jqXHR, exception) {
	            var error_= '';
	            if (jqXHR.status == 404) {
	                error = 'Requested page not found. [404]';
	            } else if (jqXHR.status == 500) {
	                error = 'Internal Server Error [500].';
	            } else if (exception === 'parsererror') {
	                error = 'Requested JSON parse failed.';
	            } else if (exception === 'timeout') {
					$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",rpt_tables_index);								
					$("#ClearRepotingTablesModal #refreshbtnrpt").show();
	                error = 'Time out error.';
	            } else if (jqXHR.status === 0 && exception === 'abort') {
	                error = 'Ajax request aborted.';
	            } else if (jqXHR.status === 0) {
					$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",rpt_tables_index);								
					$("#ClearRepotingTablesModal #refreshbtnrpt").show();
	                error = 'Not connect.\n Verify Network.';
	            } else {
	                error = 'Uncaught Error.\n' + jqXHR.responseText;
	            }
	            alert(error);
	        },
	    }); 
	}
	else{	
		$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",'');					
        $('#ClearRepotingTablesModal .current_status').text('Reporting Tables Cleared');
        $('#ClearRepotingTablesModal .close_btn').text('Done');
        $('#ClearRepotingTablesModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	 }
	
 }
$("#ClearRepotingTablesModal").on('hide.bs.modal', function () {
	if(get_rpt_tables_xhr)
		get_rpt_tables_xhr.abort();  
    if(verify_rpt_tables_xhr)	
    	verify_rpt_tables_xhr.abort();	
	$("#ClearRepotingTablesModal #refreshbtnrpt").attr("data-id",'');
});

});
 
 
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
    var colModel = [
	        { title:'<input type="checkbox" value="" class="eway_checkbox" id="eway_all_checkbox">', align:"left", width: 40,   dataIndx: "checkbox" },
            { title: "DATE", align:"left", width: 180,   dataIndx: "date" },
			{ title: "EINV NO.", align:"left", width: 180,   dataIndx: "einv_no" },
            { title: "PARTICULARS", align:"left", width: 180,   dataIndx: "particulars" },
            { title: "VOUCHER TYPE", align:"left", width: 180,   dataIndx: "voucher_type" },
            { title: "VCH/BILL No.", align:"left", width: 180,   dataIndx: "billno" },
            { title: "DEBIT", align:"right", width: 180,   dataIndx: "debit" },
            { title: "CREDIT", align:"right", width: 180,   dataIndx: "credit" },
			{ title: "E-INVOICE STATUS", align:"right", width: 180,   dataIndx: "einvoice_status" },
			{ title: "E-INVOICE EXPIRY", align:"right", width: 180,   dataIndx: "einvoice_expiry",render: function (ui) {
                    var rd = ui.rowData;
                    if(rd.is_einvoice_expired=="1")						
                    return '<span style="color:red;font-weight:bold;">'+ui.cellData+'</span>';
					else
					 return '<span style="color:green;font-weight:bold;">'+ui.cellData+'</span>';
				   
                }, },
			{ title: "ACTION", align:"right", width: 180,   dataIndx: "einvoice_action" },
        ];
        
     var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'view':'0'},
            url: "<?php echo base_url();?>/admin/etaxes/ajax_einvoice_transactions",
             getData: function (dataJSON) {
                var data = dataJSON.data;

                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
     var newObj = {

            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: true, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            filterModel: { mode: 'OR', type: "remote" },
            numberCell: { show: true,width: 40, title: "#" },
            colModel : colModel,
            editable: false,
            // numberCell: { show: false },
             wrap:false,
            showTitle: false,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });

            },
            
            dataReady:function(event,ui) {
                 var grid = this;

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
                var rowData             = ui.rowData;
                var col_type            = rowData.vch_type;
                var ajax                = rowData.ajax;
                var voucher_txn_id      = rowData.voucher_txn_id;
                var voucher_type_id     = rowData.voucher_type_id;
                var bom_id              = rowData.bom_id;
                var bom_batches         = rowData.bom_batches;

                $("#grid_search").pqGrid('saveState');
               var select_rowindx =  set_page();
			   
                edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);

       }
       
         newObj.cellKeyDown = function(evt, ui) {
             var rowData           = ui.rowData;
           var ajax              = rowData.ajax;
           var col_type          = rowData.col_type;
           var voucher_txn_id    = rowData.voucher_txn_id;
           var voucher_type_id   = rowData.voucher_type_id;
         var bom_id            = rowData.bom_id;
         var bom_batches       = rowData.bom_batches;
           if (evt.keyCode==13){
                    hide_trash_checkbox();
                $("#grid_search").pqGrid('saveState');
                 var select_rowindx =  set_page();

                edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);
           }
       
         }
       
    var $grid = $("#grid_search").pqGrid(newObj);

     $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){          
         $("#grid_search").pqGrid('saveState');
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
</script>

<style>
.hidden{display:none;}
</style>
</body>
</html>