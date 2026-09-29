<?php $header = array(  'title' => 'Profit & Loss' ); ?>
<?php echo view('includes/header',$header); ?>
<div class="row mb-2">
  <div class="col-md-6">
    <h3 class="pb-0">Profit & Loss</h3>
    <p>
      <em>As the end of <?= $to_date ?>
      </em>
    </p>
    
  </div>
  
  <?php 
    $params = http_build_query([
      'view'          => $view,
      'format'        => $format,
      'nil_type'      => $nil_type, 
      'from_date'     => $from_date,
      'to_date'       => $to_date,
	  'consolidated'  => $consolidated
    ]);
    ?>
  <div class="col-md-6 text-end">
    <div class="taskmenus">
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span>
      </a>
      <a href="<?= base_url() ?>admin/export/profit_loss2_print?<?= $params ?>">
        <span class="material-symbols-outlined">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined">download</span>
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
          <span class="material-symbols-outlined">share</span>
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
      </li>
    </div>
  </div>
  
  
  <div class="col-md-5">
    <form class="form" method="get" id="salefrm2" autocomplete="off">    
      <input type="hidden" name="nil_type" value="<?= $nil_type ?>">
      <div class="input-group">
        
        <span class="input-group-text px-1">From</span>
        <input autocomplete="off" type="text" name="from_date" id="from_date" value="<?= $from_date ?>" class="datepicker form-control" style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input autocomplete="off"  type="text" name="to_date" id="to_date" value="<?= $to_date ?>" class="datepicker form-control" style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success btn-event" data-bs-toggle="modal" data-bs-target="#calendermodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        <input type="submit" class="btn btn-sm btn-success" value="GO">
        
      </div>
    </form>
  </div>
  
  
  <div class="col-md-7 text-end">
     <div class="form-check form-check-inline me-2">
    <input  class="form-check-input" type="checkbox" value="1" name="consolidated" id="consolidated" <?= ($consolidated == 1) ? 'checked' : '' ?>>
    <label class="form-check-label" for="consolidated">Consolidated In All Branches</label>
  </div>
    <div class="dropdown d-inline-block me-1" style="width:220px;">
      <div class="input-group input-group-sm input-group">
        <span class="input-group-text">Format</span>
        <select form="salefrm2" class="form-select" name="format"   onchange="this.form.submit()" >
          <option <?= ($format==1) ? 'selected' : '' ?> value="1">Horizontal</option>
          <option <?= ($format==2) ? 'selected' : '' ?> value="2">Vertical</option>
        </select>
        
      </div>
    </div>
    <div class="dropdown d-inline-block me-1" style="width:220px;">
      <div class="input-group input-group-sm input-group">
        <span class="input-group-text">View</span>
        <select form="salefrm2" class="form-select" name="view" onchange="this.form.submit()" >
          <option <?= ($view==1) ? 'selected' : '' ?> value="1">Schedules</option>
          <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
          <option <?= ($view==2) ? 'selected' : '' ?> value="2">Detailed</option>
        </select>
        
      </div>
    </div>
    <div class="dropdown float-end">
      <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> View </button>
      <ul class="dropdown-menu">
        
        <li>
          <a data-nil="0" <?= ($nil_type==1) ? 'style="display: none;"' : '' ?> class="dropdown-item addon_type nil_type" href="javascript:void(0)">Include Nil Balances</a>
        </li>
        <li>
          <a data-nil="1" <?= ($nil_type==0) ? 'style="display: none;"' : '' ?> class="dropdown-item addon_type nil_type" href="javascript:void(0)">Exclude Nil Balances</a>
        </li>
      </ul>
      <button class="btn btn-sm btn-success m-1" type="button">Templates</button>
      <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success m-1">Back</a>
    </div>
  </div>
  
  
</div>

<?= view('admin/reports/_recon_notes', ['notes' => $recon_notes ?? []]) ?>

<div class="modal fade mt-5 modal-lg" id="calendermodal" tabindex="-1" aria-labelledby="calendermodallabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="col-12 calccard card m-auto">
        <form class="form" method="get" id="salefrm2" autocomplete="off">         
          <input type="hidden" name="nil_type" id="nil_type" value="<?= $nil_type ?>">
          <div class="row p-4">
            <?php $fy_bgn_yr = date('Y',strtotime(session()->get('ses_company_fy_beginning'))); ?>
            <div class="comp_calender col-md-6">
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
                  <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:70%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?>
                  </button>
                  <button type="button" class="input-group-text" id="next_year">
                  <span class="material-symbols-outlined">arrow_forward_ios</span>
                  </button>
                </div>
                
                <div class="row align-items-center my-2">
                  <div class="col-md-2 fw-bold pe-0">From</div>
                  <div class="col-md-10">
                    <div class="calc-inputgroup">
                      <input type="text" class="form-control" fdprocessedid="cw7ydk" name="from_date" id="fromdate" value="<?= $from_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                  </div>
                </div>
                
                <div class="row align-items-center my-2">
                  <div class="col-md-2 fw-bold pe-0">To</div>
                  <div class="col-md-10">
                    <div class="calc-inputgroup">
                      <input type="text" class="form-control"  fdprocessedid="b20o4z" name="to_date" id="todate" value="<?= $to_date ?>" placeholder="dd-mm-yyyy" required>
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

<!-- Modal -->
<div class="modal fade mt-5" id="moreoptionsmodal" tabindex="-1" aria-labelledby="moreoptionsmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="moreoptionsmodalLabel">App Options title</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
      </button>
    </div>
    <div class="modal-body">
      ...
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary">Save changes</button>
    </div>
    </div>
  </div>
</div>

  <br>
  <div id="grid_search" style="margin:auto;"> </div> 
<div class="row py-2">
	  <div class="col-md-6">
		<a href="#" class="m-1 link-dark">Quick Access</a>
		<a href="<?php echo $base_url;?>reports/balance_sheet" class="btn btn-success m-1">Balance Sheet</a>
		<a href="<?php echo $base_url;?>reports/trial_balance" class="btn btn-success m-1">Trial Balanace</a>
		<a href="#" class="btn btn-success m-1">Trading</a>
	  </div>
  </div>  
  
<!-- The Modal -->
<div class="modal" id="masterCreationModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
  <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Create Master</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal">
            </button>
        </div>
  <!-- Modal body -->
        <div class="modal-body">
            
            <div class="row p-2">
                  <div class="col-md-6">
                        <h4>Accounts-</h4>
                        <div class="list-group">
          <a href="<?= base_url() ?>/admin/accounts/add?p=1" class="list-group-item list-group-item-action">Account</a>
          <a href="<?= base_url() ?>/admin/billsundry/add?p=1" class="list-group-item list-group-item-action">Bill Sundry</a>
          <a href="<?= base_url() ?>/admin/accounts/add_group?p=1" class="list-group-item list-group-item-action">Account Groups</a>
                        </div>
                  </div>
                  <div class="col-md-6">
                        <h4>Items-</h4>
                        <div class="list-group">
          <a href="<?= base_url() ?>/admin/items/add_item?p=1" class="list-group-item list-group-item-action">Items</a>
          <a href="<?= base_url() ?>/admin/items/add_group?p=1" class="list-group-item list-group-item-action">Item Groups</a>
          <a href="<?= base_url() ?>/admin/items/add_category?p=1" class="list-group-item list-group-item-action">Stock Category</a>
                        </div>
                  </div>
                  
            </div>
            <div class="row p-2">
                  <div class="col-md-6">
                        <h4>Material Centre-</h4>
                        <div class="list-group">
          <a href="<?= base_url() ?>/admin/material_centres/add_centres?p=1" class="list-group-item list-group-item-action">Material Centre</a>
          <a href="<?= base_url() ?>/admin/material_centres/add_group?p=1" class="list-group-item list-group-item-action">Material Centre Groups</a>
          <a href="<?= base_url() ?>/admin/material_centres/add_mc_store?p=1" class="list-group-item list-group-item-action">Material Centre Stores</a>
                        </div>
                  </div>
                  
            </div>
        </div>
    </div>
  </div>
</div>

<div id="txn_myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" keyboard="true" backdrop="true" class="modal fade text-left">
            <div role="document" class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                 <div class="modal-header">
        <h4 class="modal-title">Voucher Transactions</h4>
        <button type="button" class="btn-close clostxnmodal" ></button>
      </div>		

					     
                    <div class="modal-body p-0"><br>
					<div class="container">
                     <div class="row justify-center-center">
						<div class="short_txn_buttons col-12 justify-content-center">
						<a href="javascript:void(0);" id="monthwisesummary_txnbtn" data-vchrtype="" data-grpid=""><button type="button" class="btn btn-outline-success" fdprocessedid="cx3o3a">MONTH WISE SUMMARY</button></a>
						<a href="javascript:void(0);" id="quarterwisesummary_txnbtn" data-vchrtype="" data-grpid=""><button type="button" class="btn btn-outline-success" fdprocessedid="cev8o">QUARTER WISE SUMMARY</button></a>
                       <a href="javascript:void(0);" id="accountgrpsummary_txnbtn" data-vchrtype="" data-grpid=""><button type="button" class="btn btn-outline-success " fdprocessedid="cx3o3a">ACCOUNT GROUP LIST</button></a>
						<a href="javascript:void(0);" id="ministate_txnbtn" data-vchrtype="" data-grpid=""><button type="button" class="btn btn-outline-success" fdprocessedid="cev8o">MINI STATEMENT</button></a>
                      
					  </div>
					  	<div class="col-12 justify-content-center">
							<div id="grid_tab_wise" style="margin-top:10px;"></div>
					   </div>
					  
				</div>
				</div>
				
      
     <br><br><br>
					</div>
                    
                </div>
            </div>
        </div>
<?php echo view('includes/footer_scripts'); ?>
<style>
    .boldcell{font-weight:700;}
    
</style>
<style>
/* Style the table cell */
.hover-cell {
    position: relative;  /* Positioning the icon inside the cell */
    padding-left: 20px;  /* Ensure space for the icon */
}

/* The icon that will be added on hover */
.hover-cell i {
    position: absolute;
    left: 5px;  /* Position the icon on the left side */
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;  /* Initially hidden */
    transition: opacity 0.3s;  /* Smooth transition */
	
}

/* Make the icon visible on hover */
.hover-cell:hover i {
    opacity: 1;
	background-image: url(<?php echo base_url();?>/public/assets/img/icon-view.png);
    background-repeat: no-repeat;
    background-position: 0px;
    width: 24px;
}
</style>
<script>
/**************                    *********************************/
/*************	Cell On Click Eye Functionality Start	*********************************/
/************ 						*********************************/	
let abortController;

$(document).on("click", ".clostxnmodal",function(){
	$("#monthwisesummary_txnbtn").attr({"data-grpid":0,"data-vchrtype":''});
	$("#quarterwisesummary_txnbtn").attr({"data-grpid":0,"data-vchrtype":''});
	$("#accountgrpsummary_txnbtn").attr({"data-grpid":0,"data-vchrtype":''});
	$("#ministate_txnbtn").attr({"data-grpid":0,"data-vchrtype":''});
	 if ($("#grid_tab_wise").pqGrid('instance')) { 
	$("#grid_tab_wise").pqGrid('hideLoading');
	}
	$("#txn_myModal").modal("hide");
	if (abortController) {
        abortController.abort();  // Aborts the fetch request
       // console.log('Fetch request aborted because modal was closed');
    }
	
  });				
  
$(document).on("click", "#monthwisesummary_txnbtn", function() {
	$(this).find("button").addClass("active");
    $("#quarterwisesummary_txnbtn").find("button").removeClass("active");
	$("#accountgrpsummary_txnbtn").find("button").removeClass("active");
	$("#ministate_txnbtn").find("button").removeClass("active");
		
	var fromdate = $("input[name=from_date]").val();
	var todate   = $("input[name=to_date]").val();
	var grpid      = $(this).attr("data-grpid");
	var type       = $(this).attr("data-vchrtype");
	if(type=='grp'){
	 load_account_group_monthly_info(grpid,type,fromdate,todate);
	}
   if(type=='acc' || type=='aop' || type=='bop'|| type=='bsd' ){
	 load_account_monthly_info(grpid,type,fromdate,todate);
   }
});

$(document).on("click", "#quarterwisesummary_txnbtn", function() {
	$(this).find("button").addClass("active");
    $("#monthwisesummary_txnbtn").find("button").removeClass("active");
	$("#accountgrpsummary_txnbtn").find("button").removeClass("active");
	$("#ministate_txnbtn").find("button").removeClass("active");
	
	var fromdate = $("input[name=from_date]").val();
	var todate   = $("input[name=to_date]").val();
	var grpid      = $(this).attr("data-grpid");
	var type       = $(this).attr("data-vchrtype");
	if(type=='grp'){
	 load_account_group_quarter_info(grpid,type,fromdate,todate);
	
	}
   if(type=='acc' || type=='aop' || type=='bop'|| type=='bsd' ){
	 load_account_quarter_info(grpid,type,fromdate,todate);
   }
});

$(document).on("click", "#accountgrpsummary_txnbtn", function() {
	$(this).find("button").addClass("active");
    $("#monthwisesummary_txnbtn").find("button").removeClass("active");
	$("#quarterwisesummary_txnbtn").find("button").removeClass("active");
	$("#ministate_txnbtn").find("button").removeClass("active");
	
	var fromdate = $("input[name=from_date]").val();
	var todate   = $("input[name=to_date]").val();
	var grpid      = $(this).attr("data-grpid");
	var type       = $(this).attr("data-vchrtype");
	
	if(type=='prt')
		var parent=1;
	else
		var parent=0;
	load_account_group_list_info(grpid,fromdate,todate,parent);
});

$(document).on("click", "#ministate_txnbtn", function() {
	$(this).find("button").addClass("active");
    $("#monthwisesummary_txnbtn").find("button").removeClass("active");
	$("#quarterwisesummary_txnbtn").find("button").removeClass("active");
	$("#accountgrpsummary_txnbtn").find("button").removeClass("active");
	
	var fromdate = $("input[name=from_date]").val();
	var todate   = $("input[name=to_date]").val();
	
	var type       = $(this).attr("data-vchrtype");
	var grpid      = $(this).attr("data-grpid");	
	
	if(type=='grp'){	
	 load_account_group_ministatement(grpid,'grp',fromdate,todate);
	}
  if(type=='acc' || type=='aop' || type=='bop'|| type=='bsd' ){
	 load_account_ministatement(grpid,'acc',fromdate,todate);
   }
});


$(document).on("click", ".show_txn_modal", function() {
	var dataIndx      = $(this).attr("data-dataIndx");
	var group_id      = $(this).attr("data-group_id");
	var type          = $(this).attr("data-type");
	var groupname     = $(this).attr("data-groupname");	
	$("#txn_myModal .modal-title").html(groupname);
	$("#txn_myModal").modal("show");
	
	$("#monthwisesummary_txnbtn").attr({"data-grpid":group_id,"data-vchrtype":type});
	$("#quarterwisesummary_txnbtn").attr({"data-grpid":group_id,"data-vchrtype":type});
	$("#accountgrpsummary_txnbtn").attr({"data-grpid":group_id,"data-vchrtype":type});
	$("#ministate_txnbtn").attr({"data-grpid":group_id,"data-vchrtype":type});
	
	$("#monthwisesummary_txnbtn").find("button").addClass("active");
    $("#quarterwisesummary_txnbtn").find("button").removeClass("active");
	$("#accountgrpsummary_txnbtn").find("button").removeClass("active");
	$("#ministate_txnbtn").find("button").removeClass("active");
	
	if(type=='grp'){
	 load_account_group_monthly_info(group_id);
	}
  if(type=='acc' || type=='aop' || type=='bop'|| type=='bsd' ){
	 load_account_monthly_info(group_id);
   }
	
  });
function AccGrpcalculateSummary() {
            var data = this.option('dataModel.data');
            var debit_total = 0;
            var credit_total = 0;
            data.forEach(function(row){
                debit_total         += parseAmount(row.debit_total);
                credit_total        += parseAmount(row.credit_total);
            })
            var totalData = {
                month        : "Total",
                debit        : formatAmount(debit_total),
                credit       : formatAmount(credit_total),                
                pq_rowcls : 'grid_footer_color',
                summaryRow: true
            }            
            this.option('summaryData', [totalData]);
        }
	    var AccGrpcolModel = [
                { title: "MONTH (<?= fy_calender()->name ?>)", align:"left",   dataIndx: "month" },
                { title: "DEBIT", align:"right",  dataIndx: "debit" },
                { title: "CREDIT", align:"right",   dataIndx: "credit" },
                { title: "BALANCE &nbsp;&nbsp;", align:"right",  dataIndx: "balance"},
            ];
                
         var AccGrpdataModel = { data: []};

         var AccGrpnewObj = {
                scrollModel: { autoFit: true },
                height: 'flex',
				resizable: true,
                autoResize: true,
                collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
                selectionModel: { type: 'row',mode:'single' },
                dataModel: AccGrpdataModel,
                colModel : AccGrpcolModel,
                dataReady : AccGrpcalculateSummary,
                editable: false,
                numberCell: { show: false },
                showTitle: false,                
            };
        AccGrpnewObj.rowDblClick = function(event, ui) {
            var rowData     = ui.rowData;
            var from_date   = rowData.from_date;
            var to_date     = rowData.to_date;
			var group_id     = rowData.group_id;  
			//window.location.href= baseurl+'/admin/accounts/accounts_trial/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        }
        
        AccGrpnewObj.cellKeyDown = function(evt, ui) {
            var rowData     = ui.rowData;            
            var from_date   = rowData.from_date;
            var to_date     = rowData.to_date;
            if (evt.keyCode==13){
             // window.location.href= baseurl+'/admin/accounts/accounts_trial/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            }
        }
async function load_account_group_monthly_info(grpid) {
    try {
	    if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
      abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php echo base_url();?>/admin/reports/ajax_account_group_summary/' + grpid,{ signal });
        if (!response.ok) {
            alert('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data) {
            if ($("#grid_tab_wise").pqGrid('instance')) {  
               			
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'colModel', AccGrpcolModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				 $("#grid_tab_wise").pqGrid('hideLoading');	
            } else {
				
                $("#grid_tab_wise").pqGrid(AccGrpnewObj);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');	
            }
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
         //stop loader
    }
}


function Account_calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;
          data.forEach(function(row){            
            debitTotal += parseFloat(row.debit_total);
            creditTotal += parseFloat(row.credit_total);
        })
        var totalData = {
                month: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
        this.option('summaryData', [totalData]);       
    }
var Account_colModel = [
            { title: "MONTH (<?= fy_calender()->name ?>)", dataIndx: "month",render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
            { title: "DEBIT(RS.)",  align: "right",dataIndx: "debit"},
            { title: "CREDIT(RS.)",  align: "right", dataIndx: "credit"},
            { title: "BALANCE(RS.)", align: "right", dataIndx: "balance"},
            { title: "",  dataType: "string", dataIndx: "balance_type",}
		   
	 	    ];
        var Account_dataModel = {"data":[]}
        
        var Account_newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: Account_dataModel,
            dataReady: Account_calculateSummary,
            colModel: Account_colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: true,            
        };
        
        
        Account_newObj.rowDblClick = function(event, ui) {
  	           var rowData      = ui.rowData;
		       var from_date         = rowData.from_date;
               var to_date         = rowData.to_date;
		       var account_id   = rowData.account_id;
			   
			   
		           }
	     
	    Account_newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var from_date         = rowData.from_date;
                 var to_date         = rowData.to_date;
		         var account_id   = rowData.account_id;
		         if(evt.keyCode==13){
		           //  set_page();
	             //  window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
		         }
		   
	       }	
async function load_account_monthly_info(grpid) {
	
    try {
		if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
        abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php echo base_url();?>/admin/accounts/ajax_monthly_detail/' + grpid,{ signal });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data) {
			
            if ($("#grid_tab_wise").pqGrid('instance')) {  
                			
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'colModel', Account_colModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            } else {
					
                $("#grid_tab_wise").pqGrid(Account_newObj);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            }
			
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
       //stop loader
    }
}

   function AccMiniStmnt_calculateSummary(){           		
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            data = $("#grid_search").pqGrid( "pageData" ),
			len = data.length;			
        data.forEach(function(row){             
            debitTotal +=  parseAmount(row.debit_total);
            creditTotal +=  parseAmount(row.credit_total);
        })

        var totalData = {
                txn_date: "Total",
                voucher_type :"",
                voucher_no:"",
				account_name:"",
				short_narration:"",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
        this.option('summaryData', [totalData]);
     }  
	 
    var AccMiniStmnt_colModel = [
            { title: "DATE", dataIndx: "txn_date", width: 100,sortable:false },
            { title: "TYPE", width: 50, dataIndx: "voucher_type",sortable:false  },
            { title: "VCH/BILL NO", width: 90, dataIndx: "voucher_no",sortable:false },             
            { title: "ACCOUNT", width: 150, dataIndx: "account_name",sortable:false },
            { title: "LONG NARRATION", width: 150, dataIndx: "short_narration",sortable:false },  
            { title: "DEBIT "+ "("+ SYSTEM_CURRENCY + ")", width: 100, align: "right", dataIndx: "debit",sortable:false },
            { title: "CREDIT "+ "("+ SYSTEM_CURRENCY + ")", width: 100, align: "right", dataIndx: "credit",sortable:false },
			{ title: "AMOUNT "+ "("+ SYSTEM_CURRENCY + ")", width: 150, align: "right", dataIndx: "account_txn_amount",hidden:true,sortable:false },
			{ title: "BALANCE "+ "("+ SYSTEM_CURRENCY + ")", width: 150, align: "right", dataIndx: "balance",sortable:false },
            { title: "", width: 20, dataType: "string", dataIndx: "balance_type",sortable:false },		   
	 	    ];
   var AccMiniStmnt_newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: "420",			
			selectionModel: {type:'row',mode:'single'},
            dataModel: {"data":[]},
            dataReady: AccMiniStmnt_calculateSummary,			
			colModel: AccMiniStmnt_colModel,  
			wrap:false,
            numberCell: { show: true },
            editable: false,
            showTitle: false                        
        };		
     AccMiniStmnt_newObj.rowDblClick = function(event, ui) {             
  	            var rowData    = ui.rowData;
				var txn_bo_id = rowData.bo_id;
  	            var col_type = rowData.col_type;
  	            var ajax   = rowData.ajax;
		        var voucher_txn_id   = rowData.voucher_txn_id;
		        var voucher_type_id   = rowData.voucher_type_id;               
                if(txn_bo_id=='<?php echo $bo_id; ?>'){
             	edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);
				}
				else{
					alert_notification("You can edit from branch!!!");
					return false;
				}		       
	     }
	     
	    AccMiniStmnt_newObj.cellKeyDown= function(evt, ui) {	        
	             var rowData     = ui.rowData;
		         var txn_bo_id = rowData.bo_id;
		         var ajax   = rowData.ajax;
		         var col_type = rowData.col_type;
		         var voucher_txn_id   = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;
		        if (evt.keyCode==13){ 
				  if(txn_bo_id=='<?php echo $bo_id; ?>'){             
		           edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx); 
				  }	else{
					alert_notification("You can edit from branch!!!");
					return false;
				}					         
		     }		   
	       }	
		   
async function load_account_ministatement(grpid,type,fromdate,todate) {
	
    try {
		if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
       abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php echo base_url();?>/admin/accounts/ajax_accorgrp_ministatement/'+grpid+'/'+type+'/'+fromdate+'/'+todate,{ signal });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data ) {			
            if($("#grid_tab_wise").pqGrid('instance')) {
				
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
				$("#grid_tab_wise").pqGrid('option', 'colModel', AccMiniStmnt_colModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            }else{
                $("#grid_tab_wise").pqGrid(AccMiniStmnt_newObj);
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            }
			
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
      
    }
}


function AccGrpMiniStmnt_calculateSummary(){           		
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            data = $("#grid_search").pqGrid( "pageData" ),
			len = data.length;			
        data.forEach(function(row){             
            debitTotal +=  parseAmount(row.debit_total);
            creditTotal +=  parseAmount(row.credit_total);
        })

        var totalData = {
                txn_date: "Total",
                voucher_type :"",
                voucher_no:"",
				account_name:"",
				short_narration:"",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
        this.option('summaryData', [totalData]);
     }  
	 
    var AccGrpMiniStmnt_colModel = [
            { title: "DATE", dataIndx: "voucher_date",},
			{ title: "ACCOUNT", dataIndx: "account_name"},
			{ title: "TYPE",  dataIndx: "voucher_type" },
			{ title: "VCH NO",  dataIndx: "voucher_no"},
			{ title: "DEBIT "+ "("+ SYSTEM_CURRENCY + ")",  align: "right", dataIndx: "debit"},
			{ title: "CREDIT "+ "("+ SYSTEM_CURRENCY + ")",  align: "right", dataIndx: "credit"},
			{ title: "BALANCE "+ "("+ SYSTEM_CURRENCY + ")",  align: "right", dataIndx: "balance"},
			{ title: "",  dataType: "string", dataIndx: "balance_type"},	   
	 	    ];
   var AccGrpMiniStmnt_newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: "420",			
			selectionModel: {type:'row',mode:'single'},
            dataModel: {"data":[]},
            dataReady: AccGrpMiniStmnt_calculateSummary,			
			colModel: AccGrpMiniStmnt_colModel,  
			wrap:false,
            numberCell: { show: true },
            editable: false,
            showTitle: false                        
        };		
     AccGrpMiniStmnt_newObj.rowDblClick = function(event, ui) {             
  	            var rowData    = ui.rowData;
				var txn_bo_id = rowData.bo_id;
  	            var col_type = rowData.col_type;
  	            var ajax   = rowData.ajax;
		        var voucher_txn_id   = rowData.voucher_txn_id;
		        var voucher_type_id   = rowData.voucher_type_id;               
                if(txn_bo_id=='<?php echo $bo_id; ?>'){
             	edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);
				}
				else{
					alert_notification("You can edit from branch!!!");
					return false;
				}		       
	     }
	     
	    AccGrpMiniStmnt_newObj.cellKeyDown= function(evt, ui) {	        
	             var rowData     = ui.rowData;
		         var txn_bo_id = rowData.bo_id;
		         var ajax   = rowData.ajax;
		         var col_type = rowData.col_type;
		         var voucher_txn_id   = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;
		        if (evt.keyCode==13){ 
				  if(txn_bo_id=='<?php echo $bo_id; ?>'){             
		           edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx); 
				  }	else{
					alert_notification("You can edit from branch!!!");
					return false;
				}					         
		     }		   
	       }	
async function load_account_group_ministatement(grpid,type,fromdate,todate) {
	
    try {
		if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
       abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php echo base_url();?>/admin/accounts/ajax_accorgrp_ministatement/'+grpid+'/'+type+'/'+fromdate+'/'+todate,{ signal });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data ) {			
            if($("#grid_tab_wise").pqGrid('instance')) {
				
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
				$("#grid_tab_wise").pqGrid('option', 'colModel', AccGrpMiniStmnt_colModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            }else{
                $("#grid_tab_wise").pqGrid(AccGrpMiniStmnt_newObj);
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            }
			
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
      
    }
}



function AccountQr_calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;
          data.forEach(function(row){            
            debitTotal += parseFloat(row.debit_total);
            creditTotal += parseFloat(row.credit_total);
        })
        var totalData = {
                month: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
        this.option('summaryData', [totalData]);       
    }
var AccountQr_colModel = [
            { title: "QUARTER (<?= fy_calender()->name ?>)", dataIndx: "month", render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
            { title: "DEBIT(RS.)",  align: "right",dataIndx: "debit"},
            { title: "CREDIT(RS.)",  align: "right", dataIndx: "credit"},
            { title: "BALANCE(RS.)", align: "right", dataIndx: "balance"},
            { title: "",  dataType: "string", dataIndx: "balance_type",}
		   
	 	    ];
        var AccountQr_dataModel = {"data":[]}
        
        var AccountQr_newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: AccountQr_dataModel,
            dataReady: AccountQr_calculateSummary,
            colModel: AccountQr_colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: true,            
        };
        
        
        AccountQr_newObj.rowDblClick = function(event, ui) {
  	           var rowData      = ui.rowData;
		       var from_date         = rowData.from_date;
               var to_date         = rowData.to_date;
		       var account_id   = rowData.account_id;
		       //set_page();
		      // window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
	     }
	     
	    AccountQr_newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var from_date         = rowData.from_date;
                 var to_date         = rowData.to_date;
		         var account_id   = rowData.account_id;
		         if(evt.keyCode==13){
		           //  set_page();
	             //  window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
		         }
		   
	       }	
async function load_account_quarter_info(grpid,type) {
	
    try {
		if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
        abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php echo base_url();?>/admin/accounts/ajax_accorgrp_quarter_info/'+grpid+'/'+type,{ signal });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data) {			
            if ($("#grid_tab_wise").pqGrid('instance')) {                  			
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'colModel', AccountQr_colModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            } else {					
                $("#grid_tab_wise").pqGrid(AccountQr_newObj);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
              }
			
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
       //stop loader
    }
}


function AccountGrpQr_calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;
          data.forEach(function(row){            
            debitTotal += parseFloat(row.debit_total);
            creditTotal += parseFloat(row.credit_total);
        })
        var totalData = {
                month: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
        this.option('summaryData', [totalData]);       
    }
var AccountGrpQr_colModel = [
            { title: "QUARTER (<?= fy_calender()->name ?>)", dataIndx: "month", render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
            { title: "DEBIT(RS.)",  align: "right",dataIndx: "debit"},
            { title: "CREDIT(RS.)",  align: "right", dataIndx: "credit"},
            { title: "BALANCE(RS.)", align: "right", dataIndx: "balance"},
            { title: "",  dataType: "string", dataIndx: "balance_type",}
		   
	 	    ];
        var AccountGrpQr_dataModel = {"data":[]}
        
        var AccountGrpQr_newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: AccountGrpQr_dataModel,
            dataReady: AccountGrpQr_calculateSummary,
            colModel: AccountGrpQr_colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: true,            
        };
        
        
        AccountGrpQr_newObj.rowDblClick = function(event, ui) {
  	           var rowData      = ui.rowData;
		       var from_date         = rowData.from_date;
               var to_date         = rowData.to_date;
		       var account_id   = rowData.account_id;
		       //set_page();
		      // window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
	     }
	     
	    AccountGrpQr_newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var from_date         = rowData.from_date;
                 var to_date         = rowData.to_date;
		         var account_id   = rowData.account_id;
		         if(evt.keyCode==13){
		           //  set_page();
	             //  window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
		         }
		   
	       }	
async function load_account_group_quarter_info(grpid,type) {
	
    try {
		if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
        abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php echo base_url();?>/admin/accounts/ajax_accorgrp_quarter_info/'+grpid+'/'+type,{ signal });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data) {			
            if ($("#grid_tab_wise").pqGrid('instance')) {                  			
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'colModel', AccountGrpQr_colModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            } else {					
                $("#grid_tab_wise").pqGrid(AccountGrpQr_newObj);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
              }
			
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
       //stop loader
    }
}




function AccountGrpLst_calculateSummary() {
        var opTotal = 0,
            debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            balanceType = '',
            data = this.option('dataModel.data'),
            len = data.length;

        data.forEach(function(row){
            
            debitTotal += row.debit_total;
            creditTotal += row.credit_total;
            balanceTotal += row.balance_total;
            opTotal += row.op_balance_total;
            
        })

        if(balanceTotal >= 0)
            balanceType = 'DR';
        if(balanceTotal < 0)
            balanceType = 'CR';

        var opBalance = '';
        if(opTotal >= 0)
            opBalance = formatAmount(opTotal) + ' DR';
        if(opTotal < 0)
            opBalance = formatAmount(Math.abs(opTotal)) + ' CR';

        balanceTotal = Math.abs(balanceTotal)

        var totalData = {
                entity_name: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                balance: formatAmount(balanceTotal),
                balance_type: balanceType,
                op_balance: opBalance,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }

        this.option('summaryData', [totalData]);       
    }
var AccountGrpLst_colModel = [
            { title: "ACCOUNT", dataIndx: "entity_name"},
            { title: "DEBIT",  align: "right",dataIndx: "debit"},
            { title: "CREDIT",  align: "right", dataIndx: "credit"},
            { title: "BALANCE", align: "right", dataIndx: "balance"},
            { title: "",  dataType: "string", dataIndx: "balance_type"}		   
	 	    ];
        var AccountGrpLst_dataModel = {"data":[]}
        
        var AccountGrpLst_newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: AccountGrpLst_dataModel,
            dataReady: AccountGrpLst_calculateSummary,
            colModel: AccountGrpLst_colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: true,            
        };
        
        
        AccountGrpLst_newObj.rowDblClick = function(event, ui) {
  	           var rowData      = ui.rowData;
		       var from_date         = rowData.from_date;
               var to_date         = rowData.to_date;
		       var account_id   = rowData.account_id;
		       //set_page();
		      // window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
	     }
	     
	    AccountGrpLst_newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var from_date         = rowData.from_date;
                 var to_date         = rowData.to_date;
		         var account_id   = rowData.account_id;
		         if(evt.keyCode==13){
		           //  set_page();
	             //  window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
		         }
		   
	       }	
async function load_account_group_list_info(grpid,fromdate,todate,parent) {
	 
    try {
		if ($("#grid_tab_wise").pqGrid('instance')) {
		  $("#grid_tab_wise").pqGrid('showLoading');
		}
        abortController = new AbortController();
        const { signal } = abortController;
        let response = await fetch('<?php echo base_url();?>/admin/accounts/ajax_accounts_trial_info/'+grpid+'/'+fromdate+'/'+todate+'/'+parent,{ signal });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        let result = await response.json();
        if (result && result.data) {			
            if ($("#grid_tab_wise").pqGrid('instance')) {                  			
                $("#grid_tab_wise").pqGrid('refresh');
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
				$("#grid_tab_wise").pqGrid('option', 'colModel', AccountGrpLst_colModel);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
            } else {					
                $("#grid_tab_wise").pqGrid(AccountGrpLst_newObj);
				$("#grid_tab_wise").pqGrid('option', 'height', 420);
                $("#grid_tab_wise").pqGrid('option', 'dataModel.data', result.data);
                $("#grid_tab_wise").pqGrid('refreshDataAndView');
				$("#grid_tab_wise").pqGrid('hideLoading');
              }
			
        } else {
            console.log('No data available for this group.');
        }
    } catch (error) {
        console.error('Error loading account group monthly info:', error);
    } finally {
       //stop loader
    }
}


$(document).on("mouseenter", ".hover-cell", function() {
    if($(this).html().trim() !== "&nbsp;"){
		var dataIndx = $(this).data("dataindx");
		var group_id = $(this).data("id");
		var type     = $(this).data("type");
		var groupname     = $(this).data("group_name");
		if (!['pnl', 'clo', 'opn'].includes(type) && typeof type != "undefined") {
        $(this).prepend('<i class="show_txn_modal" style="cursor:pointer" data-groupname="'+groupname+'" data-type="'+type+'" data-dataIndx="'+dataIndx+'" data-group_id="'+group_id+'">&nbsp;</i>');  // You can use any icon class here, e.g., from Font Awesome
		}
	}
});

$(document).on("mouseleave", ".hover-cell", function() {
    // Remove the icon when no longer hovering
    $(this).find("i").remove();
});

/**************                    *********************************/
/*************	Cell On Click Eye Functionality End	*********************************/
/************ 						*********************************/	
	$(document).on('change', '#consolidated', function(){
    var nil_type  = '<?php echo $nil_type;?>'; 
	var from_date = '<?php echo $from_date;?>'; 
	var to_date   = '<?php echo $to_date;?>'; 
	var view      = '<?php echo $view;?>'; 	
	var format    = '<?php echo $format;?>'; 	
    if($(this).is(":checked")) {        
      var stringparameters = "consolidated=1&view="+view+"&nil_type="+nil_type+"&from_date="+from_date+"&to_date="+to_date+"&format="+format;
       window.location.href= baseurl+"admin/reports/profit_loss?"+stringparameters;
    }
    else{
      window.location.href= baseurl+"admin/reports/profit_loss";
    }   
  });
  
    $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#masterCreationModal').modal('show');
        }
    });
    
  $("#changeview").on("change",function(){
    
        var nil_type  = $("#nil_type").val(); 
      var from_date = $("#from_date").val(); 
      var to_date   = $("#to_date").val(); 
      var view      = $(this).val(); 
      
    window.location.href= baseurl+"/admin/reports/profit_loss?view="+view+"&nil_type="+nil_type+"&from_date="+from_date+"&to_date="+to_date;
  });

    $(document).on('click', '.nil_type', function(){

        var nil_type = $(this).data('nil');
        nil_type = nil_type == 1 ? 0 : 1;
        $('input[name="nil_type"]').val(nil_type);
        $('#salefrm2').submit();
    });

     $(function () {
       
        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = $toolbar.find(".filterCondition").val(),
                dataIndx = $toolbar.find(".filterColumn").val(),
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
            
            { title: "",  dataIndx: "group_name",sortable:false  },
            { title: "",  align:"right" , dataIndx: "balance",sortable:false }
        ];
            
         var dataModel = {"data":<?php echo json_encode($data);?>}
        
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
			resizable: true,
           autoResize: true,
            selectionModel: { type: 'row',mode:'single', column: true },
           /* pageModel: { type: 'local' }, */
           
            dataModel: dataModel,
          
          
            colModel: colModel,  
            
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: false,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                  $select_row = $(".select-row"),
                  data = ui.dataModel.data;
                  // grid.setSelection({ rowIndx: 0, focus: true });
            },

        };
        
        newObj.cellDblClick = function(event, ui) {
            var rowData    = ui.rowData;
            var colmnData  = ui.column;

            var group_id = rowData.group_id;
            var type = rowData.type;

            if(type == 'acc')
			{
				
                    window.location.href= baseurl+'admin/accounts/ledger_detail/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
			}
            if(type=='bsd')
			{ 
                    window.location.href= baseurl+'admin/reports/bill_sundry_ledger/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
			}
            if(type == 'grp')
                 window.location.href= baseurl+'admin/accounts/accounts_trial/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            if(type == 'prt')
                window.location.href= baseurl+'admin/accounts/accounts_trial/'+group_id +'/1'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            if(type == 'clo')
                 window.location.href= baseurl+'admin/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            if(type == 'opn')
                 window.location.href= baseurl+'admin/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        }   
    
        newObj.cellKeyDown= function(evt, ui) {
           var rowData     = ui.rowData;
           var colmnData  = ui.column;
           if (evt.keyCode==13){
                var group_id = rowData.group_id;
                var type = rowData.type;

                if(type == 'acc')
				{
                        window.location.href= baseurl+'admin/accounts/ledger_detail/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
				}
                if(type=='bsd')
				{
                        window.location.href= baseurl+'admin/reports/bill_sundry_ledger/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
				} 
                if(type == 'grp')
                     window.location.href= baseurl+'admin/accounts/accounts_trial/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                if(type == 'prt')
                    window.location.href= baseurl+'admin/accounts/accounts_trial/'+group_id +'/1'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                if(type == 'clo')
                     window.location.href= baseurl+'admin/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                if(type == 'opn')
                        window.location.href= baseurl+'admin/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
           }
            if (evt.keyCode==119){
                
                if(type == 'acc')
                    window.location.href= baseurl+'admin/accounts/modify/'+group_id;
                if(type == 'bsd')
                    window.location.href= baseurl+'admin/billsundry/modify/'+group_id;
                if(type == 'grp')
                    window.location.href= baseurl+'admin/accounts/modify_group/'+group_id;
                    
            }            
     }
        
        var $grid = $("#grid_search").pqGrid(newObj);
		pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });
    
    });
    function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
    if (!results) return '';
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }

    // The export links carry exactly the parameters this page was built with (view, format, nil_type,
// consolidated and the FY-clamped dates) - not whatever happens to be in the address bar - so the file
// is produced from the same request as the grid.
var EXPORT_QS = <?= json_encode((string)($export_qs ?? ''), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
function export_report(kind)
{
    var qs = EXPORT_QS !== '' ? EXPORT_QS : window.location.search.substring(1);
    window.location.href = baseurl + "admin/export/profit_loss?" + qs + "&export=" + kind;
}
function print_csv()   { export_report('csv'); }
function print_excel() { export_report('excel'); }
</script> 