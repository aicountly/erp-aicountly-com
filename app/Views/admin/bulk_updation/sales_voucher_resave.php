<?php $header = array( 	'title' => 'Sales Vouchers Resave' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));

$bo_array = array();
if(count($branch_dropdown) >1){ 
$bo_array = array();
 foreach($branch_dropdown as $brow){
   $bo_array[$brow['bo_id']] = $brow['bo_name']; 
 }
}
?>      
<style>
    .gridtable .row{ display: grid; grid-template-columns:20% 20% 20% 20% 20%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
    .list-inline a{color:#000;}
    .list-inline a.active{color:#25b003;}
    .acesstabs{display:flex; position:relative; justify-content: space-around;}
    .acesstabs::after{height:2px; width:100%; background:#1d528c; position:absolute; top:30px; content:'';}
    .acesstabs a{width:60px; height:60px; background:#1d528c; font-weight:bold; z-index:1; color:#fff; border-radius:50%; text-align:center; line-height:60px; font-size:32px; } 
    .acesstabs a.active{ background:#25b003; color:#fff;}

    .myform .col-sm-6{padding-bottom:2px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select,input.custom-combobox-input {width:75%;}
    .myform .select2 {width:75%!important; }
	.ui-autocomplete {
max-height: 200px;
overflow-y: auto;
/* prevent horizontal scrollbar */
overflow-x: hidden;
}

.pq-grid-cell.pq-side-icon > div:before {
width: 20px;
height: 20px;
color: #ccc;
float: right;
}

.pq-grid-cell.pq-drop-icon > div:before {
content: "▼";
}

.pq-grid-cell.pq-calendar > div:before {
content: "\01F4C5";
}
.pq-select-search-input {
padding: 1px 2px;
border-width: 0;
height:23px;
}
.pq-select-search-input {
box-sizing: border-box;
width: 100%;
font-size: inherit;
}

.ui-datepicker-calendar tr, .ui-datepicker-calendar td, .ui-datepicker-calendar td a, .ui-datepicker-calendar th{
font-size:inherit;
}
div.ui-datepicker{
font-size:13px;
width:inherit;
height:inherit;
}
.ui-datepicker-title span{
font-size:13px;
}
.pq-sb-horiz-t .pq-sb-slider, .pq-sb-vert-t .pq-sb-slider, .pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{background-color: rgb(220, 254, 211) !important;}
.main-container {
            margin: 0 auto;
        }
:root {
            --primary-color: #25b003;
            --secondary-color: #31374a;
            --accent-color: #25b003;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
            --light-text: #64748b;
            --border-radius: 12px;
            --box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            --green: #25b003;
            --green-hover: #1a8602;
            --green-soft: #e6fbe6;
        }		
.filter-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 30px;
            border-top: 5px solid var(--accent-color);
        }
        
        .filter-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 20px;
            display: flex;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .filter-title i {
            background-color: var(--accent-color);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        
        .form-section {
            background-color: #f8fafc;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--light-text);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid #cbd5e1;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background-color: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        
        .input-group-text {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: var(--light-text);
            border-radius: 8px 0 0 8px;
        }
        
        .btn-action {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-back {
            background-color: #f1f5f9;
            color: var(--dark-text);
            border: 1px solid #cbd5e1;
        }
        
        .btn-back:hover {
            background-color: #e2e8f0;
        }
        
        .btn-apply {
            background: linear-gradient(135deg, var(--accent-color));
            color: white;
            border: none;
        }
        
        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px #1a8602;
        }
        
        /* Radio button styling */
        .radio-group {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }
        
        .radio-button {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .radio-button input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .radio-button label {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 6px;
            background-color: #f8fafc!important;
            border: 1px solid #cbd5e1!important;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            color: var(--light-text)!important;
        }
        
        .radio-button input[type="radio"]:checked + label {
            background-color: var(--green)!important;
            color: white!important;
            border-color: var(--secondary-color);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }
        
        .radio-button label i {
            margin-right: 6px;
        }
        
        /* Date picker custom styling */
        .date-input {
            position: relative;
        }
        
        .date-input i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
            
        }


        
.tempus-dominus-widget.dark {
  background-color: #fff !important;
  color: #212529 !important;
}

.tempus-dominus-widget .toolbar i {
  color: #212529 !important;
  font-size: 16px;
}

.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight).new,
.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight).old {
  color: hsla(33, 4%, 57%, 0.38);
}

.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight).active,
.tempus-dominus-widget.dark .date-container-months div:not(.no-highlight).active,
.tempus-dominus-widget.dark .date-container-years div:not(.no-highlight).active {
  background-color: var(--green);
  color: var(--green-soft);
}

.tempus-dominus-widget .date-container-days div:not(.no-highlight).today {
  color: var(--green-soft) !important;
}

.tempus-dominus-widget.dark .date-container-days .dow {
  color: #212529 !important;
}

.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight):hover,
.tempus-dominus-widget.dark .date-container-months div:not(.no-highlight):hover,
.tempus-dominus-widget.dark .date-container-years div:not(.no-highlight):hover {
  background: var(--green-soft) !important;
  color: var(--green-hover) !important;
  border: 1px solid var(--green-hover) !important;
}

.tempus-dominus-widget.dark .toolbar div:hover {
  background: var(--green-soft);
  color: var(--green);
}

.tempus-dominus-widget {
  background: #fff !important;
  border: 1px solid var(--green) !important;
  border-radius: 8px;
  color: #212529 !important;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.tempus-dominus-widget .calendar-header,
.tempus-dominus-widget .decade,
.tempus-dominus-widget .year,
.tempus-dominus-widget .month {
  font-weight: 600;
  color: #212529;
}

.tempus-dominus-widget .calendar-days .day {
  font-size: 14px;
  width: 2.4rem;
  height: 2.4rem;
  line-height: 2.4rem;
  text-align: center;
  margin: 2px auto;
  border-radius: 50%;
  font-weight: 500;
  background-color: transparent !important;
  color: #212529 !important;
  transition: all 0.2s;
}

.tempus-dominus-widget .calendar-days .day:hover {
  background-color: var(--green-soft) !important;
  color: var(--green) !important;
}

.tempus-dominus-widget .calendar-days .day.active,
.tempus-dominus-widget .calendar-days .day.active:hover {
  background-color: var(--green) !important;
  color: #ffffff !important;
  font-weight: 600;
}

.tempus-dominus-widget .calendar-days .day.today:not(.active) {
  border: 2px solid var(--green);
  color: var(--green) !important;
  font-weight: 600;
  background-color: transparent !important;
}

.tempus-dominus-widget .btn-primary {
  background-color: var(--green);
  border-color: var(--green);
}

.tempus-dominus-widget .btn-primary:hover {
  background-color: var(--green-hover);
  border-color: var(--green-hover);
}
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .step {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            
            .filter-card {
                padding: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
        }
</style>
        
<h3 class="pb-3">Sales Vouchers Resave</h3>

<div class="col-12">

 <p class="acesstabs" id="myTab" role="tablist" style="pointer-events: none;">

  <a class="" id="acess1-tab"  data-bs-toggle="tab" data-bs-target="#acess1-panel" type="button" role="tab" aria-controls="acess1-panel" aria-selected="true">1</a>
  <a class="active" id="acess2-tab" data-bs-toggle="tab" data-bs-target="#acess2-panel" type="button" role="tab" aria-controls="acess2-panel" aria-selected="false" tabindex="-1">2</a>
  <a class="" id="acess3-tab" data-bs-toggle="tab" data-bs-target="#acess3-panel" type="button" role="tab" aria-controls="acess3-panel" aria-selected="false" tabindex="-1">3</a>
 
</p>

 
	
<div class="tab-content border-0 accordion form-outline mb-4" id="myTabContent">

<!-- Access 2 Tab -->
<div class="tab-pane border-0 fade accordion-item active show" id="acess2-panel" role="tabpanel" aria-labelledby="acess2-tab" tabindex="0">
 <div class="container py-5 main-container">
      
        <div class="filter-card">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                <span>Filter Options</span>
            </div>
            
            <form>
                <!-- Date Range Section -->
            <div class="form-section">
            <div class="section-title">
                <i class="fas fa-calendar-alt"></i>
                <span>Date Range & Voucher Info</span>
            </div>

            <div class="row g-4 align-items-start">
                <!-- From Date -->
                <div class="col-md-4" style="display: flex; flex-direction: column; gap: 10px 0;">
                    <div >
                <label class="form-label">From Date:</label>
                <div class="date-input">
                    <input type="text" class="form-control datepicker"  id="from_date" name="from_date" value="<?php echo date('01-m-Y');?>"  placeholder="dd-mm-yyyy" />
                  
                </div>
                </div>

                <!-- To Date -->
                <div >
                <label class="form-label">To Date:</label>
                <div class="date-input">
                    <input type="text" class="form-control datepicker"  id="to_date" name="to_date" value="<?php echo date('d-m-Y');?>" placeholder="dd-mm-yyyy" />
                   
                </div>
                </div>
                </div>

                <!-- Date Condition -->
                <div class="col-md-1">
                <label class="form-label d-block">Condition:</label>
                <div class="radio-group d-flex flex-column">
                    <div class="radio-button mb-2">
                    <input type="radio" name="main_cond" id="main_cond-and" id="condition-and" value="and" checked>
                    <label for="main_cond-and"><i class="fas fa-link"></i>AND</label>
                    </div>
                    <div class="radio-button">
                    <input type="radio" name="main_cond" id="main_cond-or"  value="or" id="condition-or" >
                    <label for="main_cond-or" style="padding: 8px 20px;"><i class="fas fa-code-branch"></i>OR</label>
                    </div>
                </div>
                </div>

                <!-- Vch. Series -->
                <div class="col-md-3">
                <label class="form-label">Vch. Series:</label>
				
				<select name="voucher_series" id="voucher_series" class="voucher_series form-select required" required>
			<?php		
				if(isset($voucher_series_dropdown[0])){
				$defaultsel= $voucher_series_dropdown[0]['vch_series_id'];
			}else
				$defaultsel='';			
			foreach($voucher_series_dropdown as $vch_series_info){ ?>
               <option id="<?php echo $vch_series_info['vch_series_id']; ?>" data-srsmethod="<?php echo $vch_series_info['vch_series_method']; ?>"  value="<?php echo $vch_series_info['vch_series_id']; ?>" <?php echo ($defaultsel==$vch_series_info['vch_series_id'])?'selected':'';?>><?= $vch_series_info['vch_series_name'] ?></option>
            <?php } ?>
		  </select>
		                
                </div>

                <!-- Vch. Condition -->
                <div class="col-md-1">
                <label class="form-label d-block">Condition:</label>
                <div class="radio-group d-flex flex-column">
                    <div class="radio-button mb-2">
                    <input type="radio" name="series_cond" id="series_cond-and" value="and" name="logic-bill" >
                    <label for="series_cond-and"><i class="fas fa-link"></i>AND</label>
                    </div>
                    <div class="radio-button">
                    <input type="radio" name="series_cond" id="series_cond-or" value="or" checked>
                    <label for="series_cond-or" style="padding: 8px 20px;"><i class="fas fa-code-branch"></i>OR</label>
                    </div>
                </div>
                </div>

                <!-- Bill Ref No -->
                <div class="col-md-3">
                <label class="form-label">Bill Ref No:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                    <input type="text"  id="billno" name="billno" class="form-control" placeholder="Enter bill reference">
                </div>
                </div>
            </div>
            </div>

                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" onClick="window.location.href='<?php echo base_url();?>admin/bulk_updation'" class="btn btn-action btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </button>
                    <button type="button" id="filtersale_voucher" class="btn btn-action btn-apply">
                        <span>Apply Filter</span>
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<div class="tab-pane border-0 fade accordion-item" id="acess3-panel" role="tabpanel" aria-labelledby="acess3-tab" tabindex="0">
    <h4>Sales Transactions</h4>
  <div class="card p-4 mt-2">
  <iframe id="hiddenIframe" src="" ></iframe>
    <div class="col-md-12 myform pt-4 mx-auto">
	<form id="bulkfrm"  method="post">
      <input type="hidden" name="voucherdata" id="voucherdata">		
      <div class="mt-5" id="transactions_grid"  style="margin:auto;"></div>	 	  
	</form>
    </div>
	<div class="row">

    <div class="col-md-12">
      <span id="edt_desc" class="ms-2">Displaying 1 to 100 of 1000 records</span>
       </div>
       
   </div>
   
    <div class="col-md-12 pt-4">
      <p class="">
        <a href="javascript:void(0);" onclick="window.location.reload();return false;" class="btn btn-success m-2">« Back</a><a href="javascript:void(0);" id="nextrecords" class="btn btn-success float-end m-2">Next</a><a href="javascript:void(0);" id="prevrecords" class="btn btn-success float-end m-2">Prev</a><a href="javascript:void(0);" data-num="0" id="refresh_vouchers" class="btn btn-success float-end m-2">Refresh Vouchers</a>
      </p>
    </div>
  </div>
  

</div>



</div>
</div>  
<div id="errorMessage" class="error-message" style="display:none;"></div>
<style>
        /* Hide the iframe so it's invisible to the user */
        #hiddenIframe {			
			width: 0%;
            height:0;
        }
		.error-message {
            color: red;
            font-weight: bold;
        }
		
    </style>
	
	<!-- Modal for displaying progress -->
	
	<!-- The Modal -->
<div class="modal fade" id="voucherVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Vouchers Resave</h4>
     
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	  <p class="overall_status">Total Voucher Types: 25</p>
       <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sales)
		</div>
		
		<div>
			<p>Faults: <span class="faults">0</span></p>		
			<p>Timeouts: <span class="timeouts">0</span></p>
			<ul class="list-group voucher_list" id="master_verification_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>
		
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	    <button id="retryBtn" style="display: none;" class="btn btn-success">Refresh</button>
	   <button type="button" class="btn btn-danger close_btn" id="cancelbtn" data-bs-dismiss="modal">Close</button>
	 
	  </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>

 </body>
 
 <script>
/////////////////////////  Auto Refresh Code ////////////////////////
$(document).on("click", "#refresh_vouchers", function() {
    $('#voucherVerificationModal .current_status').text('Please wait while vouchers are being processed...');
    $('#voucherVerificationModal .progress-bar').css('width', '0%').text('0%');
    $('#voucherVerificationModal #master_verification_list').empty();
    $('#voucherVerificationModal .faults').text('0');

    var griddata = $("#transactions_grid").pqGrid('option', 'dataModel.data');
    var pagesToSave = [];
    var pagesVouchers = [];

    for (var i = 0; i < griddata.length; i++) {
        if (griddata[i]['voucher_txn_id']) {
            var pageurl = "<?php echo base_url(); ?>admin/sales/resave/" + griddata[i]['voucher_txn_id'];
            pagesToSave.push(pageurl);
            pagesVouchers.push(griddata[i]['bill_ref_no']);
        }
    }
    startAutoSaveProcess(pagesToSave, pagesVouchers);
});
var currentPageIndex = 0;
var totalPages = 0;
var faults_counter = 0;
var timeout_counter = 0;

var failedPages = [];
var failedVouchers = [];

// Start the full process
function startAutoSaveProcess(pagesToSave, pagesVouchers) {
    totalPages = pagesToSave.length;
    currentPageIndex = 0;
    faults_counter = 0;
    failedPages = [];
    failedVouchers = [];

    // Reset UI
    $('#voucherVerificationModal .overall_status').text("Total Vouchers: " + totalPages);
    $('#voucherVerificationModal .progress-bar').css('width', '0%').text('0%');
    $('#voucherVerificationModal .current_status').text('Starting...');
    $('#voucherVerificationModal .faults').text('0');
    $('#master_verification_list').empty();
    $('#retryBtn').hide();

    $('#voucherVerificationModal').modal('show');

    // Kick off process
    processNextVoucher(pagesToSave, pagesVouchers);
}

// Process one voucher at a time
function processNextVoucher(pagesToSave, pagesVouchers) {
    if (currentPageIndex >= totalPages) {
        // ✅ Done
        $('#voucherVerificationModal .progress-bar')
            .css('width', '100%').text('100%');
        $('#voucherVerificationModal .current_status').text("Processing completed.");

        if (failedPages.length > 0) {
            $('#retryBtn').show(); // show retry if some failed
        }
        return;
    }

    var pageurl   = pagesToSave[currentPageIndex];
    var voucherNo = pagesVouchers[currentPageIndex];

    $('#voucherVerificationModal .current_status')
        .text("Processing " + (currentPageIndex+1) + " / " + totalPages + " (Voucher: " + voucherNo + ")");

$.ajax({
    url: pageurl,
    type: "POST",
    dataType: "json",
    timeout: 20000, // 20s timeout
    success: function(res) {
		if(res.status){
		 $("#master_verification_list").append(
            "<li class='list-group-item list-group-item-success'>Voucher " + voucherNo + " processed successfully</li>"
        );
        updateProgressBar();
        currentPageIndex++;
        processNextVoucher(pagesToSave, pagesVouchers);	
			
		}else{
		    faults_counter++;
            failedPages.push(pageurl);
            failedVouchers.push(voucherNo);

            $("#master_verification_list").append(
                "<li class='list-group-item list-group-item-danger'>Voucher " + voucherNo + " failed ("+ res.message +")</li>"
            );
            $('#voucherVerificationModal .faults').text(faults_counter);

            updateProgressBar();
            currentPageIndex++;
            processNextVoucher(pagesToSave, pagesVouchers);	
		}
		
        
    },
    error: function(xhr, status, error) {
        if (status === "timeout") {
            // Timeout case → stop chain, show retry
            timeout_counter++;
            $('#voucherVerificationModal .timeouts').text(timeout_counter);

            $("#master_verification_list").append(
                "<li class='list-group-item list-group-item-warning'>Voucher " + voucherNo + " timed out</li>"
            );

            $('#voucherVerificationModal .current_status').text("Timeout at voucher " + voucherNo);
            $('#retryBtn').show().text("Resume from here"); // show resume button
        } else {
            // Normal failure → mark failed and continue
            faults_counter++;
            failedPages.push(pageurl);
            failedVouchers.push(voucherNo);

            $("#master_verification_list").append(
                "<li class='list-group-item list-group-item-danger'>Voucher " + voucherNo + " failed ("+ error +")</li>"
            );
            $('#voucherVerificationModal .faults').text(faults_counter);

            updateProgressBar();
            currentPageIndex++;
            processNextVoucher(pagesToSave, pagesVouchers);
        }
    }
});

}

// Progress bar update
function updateProgressBar() {
    var percentage = ((currentPageIndex+1) / totalPages) * 100;
    $('#voucherVerificationModal .progress-bar')
        .css('width', percentage + '%')
        .text(Math.round(percentage) + '%');
}

// Retry failed only
$(document).on("click", "#retryBtn", function() {
    $('#retryBtn').hide().text("Refresh"); // reset label for next time
    // continue with same voucher (don’t increment currentPageIndex)
    processNextVoucher(pagesToSave, pagesVouchers);
});

/////////////////////////  Auto Refresh Code ////////////////////////
 var item_checked   = [];
 $(document).on("click","#updategrid_changes",function(){
	var tpages         = $(this).attr("data-ajax");
    var nxtpage        = $(this).attr("data-role");
    var total_records  = $(this).attr("data-num");	
	var voucherdata    = $("#transactions_grid").pqGrid('option', 'dataModel.data');
	for (var i = 0; i < voucherdata.length; i++) {
		   var credit           = voucherdata[i]['credit'];
           var debit            = voucherdata[i]['debit'];
           var particulars      = voucherdata[i]['particulars'];
           var voucher_no       = voucherdata[i]['voucher_no'];
           var voucher_txn_id   = voucherdata[i]['voucher_txn_id'];
           var voucher_type     = voucherdata[i]['voucher_type'];
           var voucher_type_id  = voucherdata[i]['voucher_type_id'];
		   var master_id        = voucherdata[i]['master_id'];
		   var from_date        = voucherdata[i]['from_date'];
		   var to_date          = voucherdata[i]['to_date'];
		   var txn_id           = voucherdata[i]['txn_id'];
		   var voucher_sub_type = voucherdata[i]['voucher_sub_type'];
		   
		   
           var date = voucherdata[i]['date'];
		   item_checked.push({
                   "credit": parseAmount(credit),
                    "debit": parseAmount(debit),
                    "particulars" :particulars,
                    "voucher_no" :voucher_no,
                    "voucher_txn_id" :voucher_txn_id,
                    "voucher_type" :voucher_type,
                    "voucher_type_id" :voucher_type_id,
					"date" : date,
					"master_id":master_id,
					"from_date":from_date,
					"to_date":to_date,
					"txn_id":txn_id,
					"voucher_sub_type":voucher_sub_type
					
                });
	}
	
	$("#voucherdata").val(JSON.stringify(item_checked));
			commoncode();
	
			const nextTabLinkEl = $('#acess4-tab');
			const nextTab =new bootstrap.Tab(nextTabLinkEl);
			
			
			 nextTab.show();
	          
			load_sale_transactions();
     })
  
   async function load_sale_transactions() {
	   let start_time = performance.now();
        
          var interval   = setInterval(function(){
                var currenttime  = performance.now();
                var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
                 $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
                 $('#progressbar').attr('aria-valuenow',pct);
                 $('#progressbar .progress-bar').css("width", pct+"%");
                 $('#progressbar .progress-bar').text(pct+"%");
              
          }, 1000);
		  
		  var voucherdata = $("#voucherdata").val();
		let response = await fetch('<?= base_url() ?>/admin/bulk_updation/save_transactions',{
		   method: 'POST',
		   headers: {
			'Content-Type': 'application/json',
			},
		    body: JSON.stringify({
			  voucherdata: voucherdata,			  
			  })
		});
   let result = await response.json();
            requestTime = performance.now();
           if(result.status=="1"){
            clearInterval(interval);
			const nextTabLinkEl = $('#acess5-tab');
			const nextTab =new bootstrap.Tab(nextTabLinkEl);
			nextTab.show();
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");            
           } 
	   
   }
 $("#filtersale_voucher").on("click",function(){
	const nextTabLinkEl = $('#acess3-tab');
    const nextTab =new bootstrap.Tab(nextTabLinkEl);
    nextTab.show();	 
	accounts_master_list();
	 
 });
 function pagination_records(nxtpage,buttonid,button_action){
	 var from_date = $('#from_date').val();	
	 var to_date = $('#to_date').val();	
  $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_sales_transactions',
				  method: 'POST',
				  data: {"pq_curpage": nxtpage,"from_date":from_date,"to_date":to_date},
				  dataType: 'json',
				  timeout: 10000, // 10 secods
				  beforeSend: function() {
					$("#transactions_grid").pqGrid('showLoading');
				  },
				  success: function(dataJSON)
				   {
					  if(dataJSON.totalRecords=="0"){
				    $("#refresh_vouchers").hide();
					$("#prevrecords").hide();
				   }else{
					    $("#refresh_vouchers").show();
					$("#prevrecords").show();
				   }

					   $("#transactions_grid").pqGrid('hideLoading');
				    $("#transactions_grid").pqGrid('option','dataModel.data',dataJSON.data);
				     stop_loader();
					 if(button_action=='prev' && (parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0 )){
						$("#prevrecords").hide();	
						$("#nextrecords").show();						
						$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);								
					 }	
					 else if(button_action=='prev' && (parseInt(dataJSON.curPage)< parseInt(dataJSON.totalPages))){
					   $("#nextrecords").attr("data-ajax",dataJSON.totalPages);
						$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
						$("#nextrecords").show();
					 }						
					   else
						   if(dataJSON.totalRecords=="0"){
							    $("#prevrecords").hide();
						   }else
						    $("#prevrecords").show();
							if(dataJSON.curPage==dataJSON.totalPages){								
								$("#"+buttonid).attr("data-ajax",0);
								$("#"+buttonid).attr("data-role",0);
								$("#nextrecords").hide();																
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);								
							}
							else{								
								if(button_action=='next'){
								 $("#"+buttonid).html("Next »");
								}
								$("#"+buttonid).attr("data-ajax",dataJSON.totalPages);
								$("#"+buttonid).attr("data-role",parseInt(dataJSON.curPage)+1);
								if(parseInt(dataJSON.curPage)==1 || parseInt(dataJSON.curPage)==0){
									$("#prevrecords").hide();	
								}
								else
								 $("#prevrecords").show();								
							
								$("#prevrecords").attr("data-ajax",dataJSON.totalPages);
								$("#prevrecords").attr("data-role",parseInt(dataJSON.curPage)-1);
								
							}
							
							  var min = parseInt(dataJSON.offset) + 1;
							  var max = parseInt(dataJSON.offset) + parseInt(dataJSON.limit);
							  if(max > dataJSON.totalRecords){
								max = dataJSON.totalRecords;
							  }
		  
							if(dataJSON.curPage>1)
							  var nextpage =parseInt(dataJSON.renderRecods)+parseInt(1);
							else
							var nextpage =	parseInt(dataJSON.curPage);
						
							$("#edt_desc").html("Displaying "+min+" to "+max+" of "+dataJSON.totalRecords+" records");
					
					 $("#transactions_grid").pqGrid('refreshDataAndView');
					
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
	        }
			    });   
			
}
function commoncode(){	
   var pq_grids = $("#transactions_grid");
   var data = pq_grids.pqGrid('option', 'dataModel.data'); 
   var allcolumns = <?php echo json_encode($bo_array);?>;          		   
   var taxcatg_data = [];
    for (var j = 0; j < data.length; j++) {
	 var master_opp_bal=0;
     var tax_cat_id  = data[j]['tax_cat_id'];
      var acc_id      = data[j]['acc_id'];
	 var supplytype  = '';//data[j]['supplytype_id'];
			  
              if(acc_id!=''){
                taxcatg_data.push({
                        "tax_cat_id" : tax_cat_id,
                        "item_id"    : acc_id,
						"supplytype" : supplytype																						
                      });
                 }   
              }
				$("#accounts_grid").pqGrid('refreshDataAndView');	
		
}

 $(document).on("click","#nextrecords",function(){
	show_loader();
	var tpages = $(this).attr("data-ajax");
	var nxtpage = $(this).attr("data-role");
	 commoncode();
	 pagination_records(nxtpage,'nextrecords','next');
	 
});

$(document).on("click","#prevrecords",function(){
	show_loader();
	var tpages = $(this).attr("data-ajax");
	var nxtpage = $(this).attr("data-role");
	 commoncode();
	 pagination_records(nxtpage,'prevrecords','prev');
	 
});
  
 
 
 function accounts_master_list()
   {	
     var from_date = $('#from_date').val();	
	 var to_date = $('#to_date').val();	
	 let main_cond = $('input[name="main_cond"]:checked').val();   // e.g. "yes" or "no"
	 
	 var voucher_series = $('#voucher_series').val();
	 let series_cond = $('input[name="series_cond"]:checked').val();   // e.g. "yes" or "no"
	 
	 
	 var billno = $('#billno').val();
	 var billref_cond = $('#billref_cond').val();
	 
	 
         $.ajax({
				  type: "POST",
				  url: '<?php echo base_url();?>admin/bulk_updation/ajax_sales_transactions',
				  method: 'POST',
				  data: {"pq_curpage": 1,"voucher_series":voucher_series,"billno":billno,"from_date":from_date,"to_date":to_date,"main_cond":main_cond,"series_cond":series_cond,"billref_cond":billref_cond},
				  dataType: 'json',
				  beforeSend: function() {
					show_loader();
				  },
				  success: function(dataJSON)
				   {     if(dataJSON.totalRecords=="0"){
				    $("#refresh_vouchers").hide();
					$("#prevrecords").hide();
				   }else{
					    $("#refresh_vouchers").show();
					$("#prevrecords").show();
				   }

				   stop_loader();
				   if(dataJSON.totalRecords>100){ 
				   $("#nextrecords").show();$("#prevrecords").show();}
				   else{
					   $("#nextrecords").hide();$("#prevrecords").hide();}
				    $("#transactions_grid").pqGrid('option','dataModel.data',dataJSON.data);
				    
				          if(dataJSON.curPage==dataJSON.totalPages){								
								$("#nextrecords").attr("data-ajax",0);
								$("#nextrecords").attr("data-role",0);
							}
							else{								
								$("#nextrecords").attr("data-ajax",dataJSON.totalPages);
								$("#nextrecords").attr("data-role",parseInt(dataJSON.curPage)+1);
								$("#prevrecords").hide();
							}
							$("#edt_desc").html("Displaying "+dataJSON.curPage+" to "+dataJSON.renderRecods+" of "+dataJSON.totalRecords+" records");
					 $("#updategrid_changes").attr("data-num",dataJSON.totalRecords);
					
					if(dataJSON.totalRecords==0)
						 $("#updategrid_changes").hide();
					 else
						  $("#updategrid_changes").show();
					
					 $("#transactions_grid").pqGrid('refreshDataAndView');
					
					}
			    });  
    var colModel = [
          { title: "DATE", width: 100, dataIndx: "date",editable: false}, 
          { title: "BILL NO.", width: 100, dataIndx: "bill_ref_no",editable: false},           
		  { title: "ACCOUNT", width: 100, dataIndx: "particulars",editable: false,filterable:"yes"  },
          { title: "AMOUNT", width: 100, dataIndx: "amount",dataType: "float" ,filterable:"no",editable: false},
          { title: "NARRATION", width: 100, dataIndx: "narration",editable: false},
		  ];
		var dataModel=[];   
        var newObj   = {
            scrollModel: { autoFit: true },
			height: 450,
			collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
			selectionModel: { type: 'cell',mode:'single' },
			pageModel: { type: null },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR', type: "remote" },
            numberCell: { show: false },
            editable: true,
            showTitle: true,
            wrap:false,
			editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            create: function (evt, ui) {
                  var grid = this,
                  $select_row = $(".items_row"),
                  data = ui.dataModel.data;
                  grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [              
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { change: filterhandler_items }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler_items,
                        options: function (ui) {
                            var CM = ui.colModel;                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                                if(column.dataIndx!='chkbx'){
                                  obj[column.dataIndx] = column.title;
                                  opts.push(obj);
                                }
                            }
                         return opts;
                        }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler_items,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            
                        ]
                    }
                ]
            }
        };
	 $grid = $("#transactions_grid").pqGrid(newObj);	
	
	function filterhandler_items(evt, ui) {
   
      var $toolbar = $grid.find('.pq-toolbar-search'),
          $value = $toolbar.find(".filterValue"),
          value = $value.val(),
          condition = '',
          dataIndx = '',
          filterObject;

      if (dataIndx == "") {
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
  }
 </script>
</html>
