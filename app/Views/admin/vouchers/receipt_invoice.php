<?php $header = array(  'title' => $voucher_name .' Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
<?php  $comp_vch_series_no ='0'; ?>
<?php
$local_session    = \Config\Services::session();
$fy_begndt        = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end           = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>
<style>
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
</style>
<style>
.dropdown-menu li {
position: relative;
}
.dropdown-menu .dropdown-submenu {
display: none;
position: absolute;
left: 100%;
top: -10px;
}
.dropdown-menu .dropdown-submenu-left {
display: none;
position: absolute;
right: 100%;
top: -10px;
}
.dropdown-menu > li:hover > .dropdown-submenu {
display: block;
}
.dropdown-menu > li:hover > .dropdown-submenu-left {
display: block;
}
</style>
<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
z-index:9999!important;
}
.tdtext-left{text-align:left!important;}
</style>
<div class="row mb-2">
  <div class="col-md-6 order-1">
    <h3 id="heading">
    <?php echo $voucher_name;?>  Voucher</h3>
  </div>
  <div class="col-md-6 order-3 order-md-2 text-end">
    <div class="taskmenus">
      <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu">
        <span class="material-symbols-outlined">filter_list</span>
      </a>
      <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history">
        <span class="material-symbols-outlined">visibility</span>
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
          <a class="dropdown-item" href="#">CSV</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Excel</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Document</a>
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
      <a href="<?php echo history_back();?>" class="hideinline-md">
        <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
      </a>
    </div>
  </div>
  <div class="col-md-6 order-2 order-md-3">
    <?php if($voucher_type_id == 5){ ?>
    <select name="type" id="type" class="form-select bbbccoCheck d-inline-block" style="width:160px;">
      <option value="with_item" >With Item</option>
      <option value="without_item" selected="selected">Without Item</option>
    </select>
    <?php } ?>
    <div class="form-check form-check-inline bbbccCheck bbbccoCheck">
      <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
      <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
    </div>
    <div class="form-check form-check-inline bbbccCheck bbbccoCheck">
      <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
      <label class="form-check-label" for="ccCheck">Cost Centre</label>
    </div>
    <div class="form-check form-check-inline bbbccCheck bbbccoCheck">
      <input class="form-check-input" type="checkbox" value="1" id="prCheck">
      <label class="form-check-label" for="prCheck">Project Reporting</label>
    </div>
    
    <div class="form-check form-check-inline me-2 bbbccoCheck">
      <input class="form-check-input" type="checkbox" value="1" name="oCheck" id="oCheck" form="form1">
      <label class="form-check-label" for="oCheck">Optional</label>
    </div>
    <?php if($voucher_type_id == 5){ ?>
    <div class="form-check form-check-inline me-2">
      <input class="form-check-input" type="checkbox" value="1" name="rCheck" id="rCheck" form="form1">
      <label class="form-check-label" for="rCheck">Reverse Jounal</label>
    </div>
    <?php } ?>
	
	<div class="row">
    <div class="col-md-4">  
    <div class="input-group">
    <select form="form1" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:120px;">
      <?php foreach ($currency_list as $value) { ?>
      <option data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>">
      <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-primary">#</div>
	</div>
	
   </div>
   </div>
   
  </div>
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">

  <div class="dropdown d-inline-block me-1" style="width:220px;">
  <form class="form" method="get" id="salefrm2" autocomplete="off">
      <div class="input-group input-group-sm input-group">
        <span class="input-group-text">Format</span>
        <select form="salefrm2" class="form-select" name="format"  onchange="this.form.submit()">
          <option <?= ($format==1) ? 'selected' : '' ?> value="1"> Classic</option>
          <option <?= ($format==2) ? 'selected' : '' ?> value="2">Accounting Voucher</option>
        </select>
        
      </div>
      </form>
    </div>

    <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
    <ul class="dropdown-menu">
         <?php if($allbos){?>
     <li>
        <a class="dropdown-item" href="javascript:void(0);" id="other_bo_voucher">&laquo; Migrate Other BO</a>
         <a class="dropdown-item" href="javascript:void(0);" id="clear_other_bo_voucher" style="display:none;">&laquo; Clear Other BO</a>
        
      </li>	 <?php } ?>
      <!-- <li>
        <a class="dropdown-item" href="#">Action</a>
      </li> -->
    </ul>
    <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
  </div> </div>
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
    <a data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a>
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
      <button type="button" class="btn btn-success">Save changes</button>
    </div>
    </div>
  </div>
</div>
</div>
<div id="validation_errors">
</div>
<form class="form" action="<?php echo base_url().'/'.$folder_path.'vouchers/invoice/'.$voucher_type_id;?>" method="post" id="form1" autocomplete="off" novalidate>

<?php echo $message_output->run() ;?>

  <input type="hidden" name="voucherdata" id="voucherdata">
  <input type="hidden" name="bbbdata" id="bbbdata">
  <input type="hidden" name="ccdata" id="ccdata">
  <input type="hidden" name="prdata" id="prdata">
  <input type="hidden" name="migrate_voucher_txn_id" value="<?= $migrate_voucher_txn_id ?>">
<div class="col-md-12">
  <div class="row mx-0 p-0 mb-2">
    <div class="col-md-3 col-6 card p-2">
      <div class="input-group">
        <label class="input-group-text">Date: </label>
        <input type="text" name="voucher_date" id="voucher_date" class="datepicker form-control" placeholder="dd-mm-yyyy" value="<?php echo $voucher_date;?>" autocomplete="off" required>
      </div>
    </div>
    <div class="col-md-3 col-6 card p-2">
      <div class="input-group">
        <label class="input-group-text">Voucher Series:</label>
        <?php
        $sel_sersval = arrayfrstval($voucher_series_dropdown);
        echo form_dropdown('voucher_series', $voucher_series_dropdown,$sel_sersval,' id="voucher_series" class="voucher_series form-control" required ');
        ?>
      </div>
    </div>
    <div class="col-md-3 col-6 card p-2">
  <div class="input-group">

  <?php $pay_acc = ($voucher_type_id === 9) ? 'Payment' : 'Receipt'; ?>



	
          <label class="input-group-text"><?php echo $pay_acc; ?> via:</label>
		  
		  <input type="text" value="" list="party_ids" id="debit_acc" name="debit_acc" class="form-control form-control-sm required" onmouseover="focus();" required>
		  <datalist id="party_ids">
		    <?php foreach($data_groups as $value){ ?>
               <option data-gstin="" id="<?= $value['acc_id']?>" data-statecode="" data-is_bbb="" value="<?= $value['account_name'] ?>" >
            <?php } ?>
		  </datalist>		  		
         
        
  </div>
</div>
    <div class="col-md-3 col-6 card p-2">
      <div class="input-group">
        <label class="input-group-text">Voucher No: </label>
        <input type="text" name="voucher_no" form="form1" id="voucher_no" class="form-control" value="<?php echo $voucher_no;?>" disabled>
      </div>
    </div>
    <?php if($voucher_type_id == 5){ ?>
    <div class="col-md-3 col-6 card p-2 rDate" style="display: none">
      <div class="input-group">
        <label class="input-group-text">Reversal Date: </label>
        <input type="text" form="form1" name="reversal_date" id="reversal_date" class="datepickerReverseJournal form-control" placeholder="dd-mm-yyyy" value="<?php echo date("d-m-Y", strtotime("+1 day", time()));?>" autocomplete="off" required>
      </div>
    </div>
    <?php } ?>
    <div class="col-md-9 col-6 card p-2">
      <div class="input-group">
        <label class="input-group-text">Narration: </label>
        <textarea  form="form1" name="narration" class="form-control"><?= $narration ?></textarea>
      </div>
    </div>
	<div class="modal fade pt-5" id="view_fcrates_modal" tabindex="-1" aria-labelledby="proinfoLabel" data-keyboard="false" data-backdrop="static">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Forex Rates</h4>
                        <button type="button" class="btn-close clsoefcratemodal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="form-row align-items-center">  
						<div class="col-sm-12 my-1">
						  <label class="sr-only" for="inlineFormInputGroupUsername">1 INR</label>
						  <div class="input-group">
							<div class="input-group-prepend">
							  <div class="input-group-text" id="selcyrlabel">@</div>
							</div>
							<input type="text" form="form1" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate"  onkeydown="return validatefcrate(event,this.value);" required>
						  </div>
						</div>
						<div class="col-sm-12 my-1">
						  <label class="sr-only">Date</label>
						  <div class="input-group" name="fcy_voucher_date" id="fcy_voucher_date">&nbsp;</div>
						</div>
					   <div class="col-sm-12 my-1">
						  <label class="sr-only">Voucher No.</label>
						  <div class="input-group" name="fcy_voucher_no" id="fcy_voucher_no">&nbsp;</div>
						</div>
					</div>
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="save_forex_rates">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
				
    <div class="col-md-3 col-6 card p-2">
      <table class="table-sm" id="account_details_label">
        <tbody>
          <tr>
            <td>Name</td>
            <td class="acc_name"></td>
          </tr>
          <tr>
            <td>Balance</td>
            <td class="acc_bal"></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div id="vch_grid_search" style="margin:auto;">
  </div>
</div>



				

<div class="col-12 text-center pt-2">
<input type="button" id="submitbtn" name="submitbtn" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-lg btn-success mx-2">
<a href="<?php echo history_back();?>"  class="btn btn-lg btn-secondary mx-2">QUIT</a>
</div>
</form>

<!-- The Modal -->
<div class="modal" id="billsModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
      <h4 class="modal-title">
      <span id="bills_page">
      </span> Bill by Bill</h4>
      <button type="button" class="btn-close" data-bs-dismiss="modal">
      </button>
    </div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6 text-center">
          <h4>Account: <span id="bills_account">
          </span>
          </h4>
        </div>
        <div class="col-md-6 text-center">
          <h4>Total: <span id="bills_total">
          </span>&nbsp;<span id="bills_drcr">
        </span>
        </h4>
        </div>
      </div>
      <div id="bill_by_bill_grid">
      </div>
      <button type="button" class="btn btn-primary prevBtn" onclick="readyBillsModel('prev')">Previous</button>
      <button type="button" class="btn btn-primary nextBtn" onclick="readyBillsModel('next')">Next</button>

      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_bill_by_bill">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal" id="ccModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
      <h4 class="modal-title">
      <span id="cc_page">
      </span> Cost Centre</h4>
      <button type="button" class="btn-close" data-bs-dismiss="modal">
      </button>
    </div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6 text-center">
          <h4>Account: <span id="cc_account">
          </span>
          </h4>
        </div>
        <div class="col-md-6 text-center">
          <h4>Total: <span id="cc_total">
          </span>&nbsp;<span id="cc_drcr">
          </span>
          </h4>
        </div>
      </div>
      <div id="cc_grid">
      </div>
      <button type="button" class="btn btn-primary prevBtn" onclick="readyCcModel('prev')">Previous</button>
      <button type="button" class="btn btn-primary nextBtn" onclick="readyCcModel('next')">Next</button>

    </div>
    <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_cc">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal" id="prModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
      <h4 class="modal-title">
      <span id="pr_page">
      </span> Project Reporting</h4>
      <button type="button" class="btn-close" data-bs-dismiss="modal">
      </button>
    </div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6 text-center">
          <h4>Account: <span id="pr_account">
          </span>
          </h4>
        </div>
        <div class="col-md-6 text-center">
          <h4>Total: <span id="pr_total">
          </span>&nbsp;<span id="pr_drcr">
          </span>
          </h4>
        </div>
      </div>
      <div id="pr_grid">
      </div>
      <button type="button" class="btn btn-primary prevBtn" onclick="readyPrModel('prev')">Previous</button>
      <button type="button" class="btn btn-primary nextBtn" onclick="readyPrModel('next')">Next</button>

    </div>
    <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_pr">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<div class="modal" id="otherbovoucher_model" style="z-index: 9999">
<div class="modal-dialog modal-dialog-scrollable  modal-sm">
  <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
      <h4 class="modal-title">
      <span id="batch_page">
      </span> Choose Branch</h4>
      <button type="button" class="btn-close" data-bs-dismiss="modal">
      </button>
    </div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
	   <div class="col-md-12">
	  <?php if($allbos){ foreach($allbos as $boid => $boname){ ?>
	    <div class="form-check">
		  <input form="form1" class="form-check-input" type="radio" data-id="<?php echo $boid;?>" data-name="<?php echo $boname;?>" value="<?php echo $boid;?>" name="VchOthrBo" id="VchOthrBo<?php echo $boid;?>">
		  <label class="form-check-label" for="VchOthrBo<?php echo $boid;?>">
			<?php echo ucwords($boname);?>
		  </label>
		</div>
	  <?php } } ?>
	 </div>
    
      </div>
     
    </div>
    <!-- Modal footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-success" id="save_other_bo_voucher">Save</button>
      <button type="button" class="btn btn-danger" id="clear_other_bo_voucher">Clear</button>
    </div>
  </div>
</div>
</div>

<?php echo view('includes/footer_scripts');
$json_data = $migrate_account_transactions;
for($i=1;$i<=500;$i++)
$json_data[] = array("drcr"=>'','account_name'=>'','description'=>'','debit'=>'','credit'=>'','account_id'=>'','is_bbb'=>'','is_cc'=>'','is_cash'=>'','acc_type'=> '','igst_rate'=>'0','cess_rate'=>'0','item_hsn_sac'=>'','tax_cat_id'=>'0','supply_type'=>'','acc_short_code'=>'','cannotselected'=>'','cess_basis'=>'');
?>

<script>
var bo_gstin_type='<?php echo $bo_gstin_type;?>';
var goods_rate   ='1';
var services_rate='6';
function validatefcrate(e,a){return IsNumericFCRate(a,e.keyCode?e.keyCode:e.charCode)}	
var isShiftt = false;
function IsNumericFCRate(_,e){if(16==e&&(isShiftt=!0),(!(e>=48)||!(e<=57))&&8!=e&&!(e<=37)&&!(e<=39)&&(!(e>=96)||!(e<=105))&&190!=e&&110!=e||!1!=isShiftt||_.includes(".")&&(190==e||110==e))return!1;if(_.includes(".")){if(_.split(".")[1].length>=8&&(e>=96&&e<=105||e>=48&&e<=57))return!1}else if(_.length>=6&&(e>=96&&e<=105||e>=48&&e<=57))return!1;return!0}
$(".clsoefcratemodal").on("click",function(){
	$("#view_fcrates_modal #fcy_voucher_no").html("");
	$("#view_fcrates_modal #fcy_voucher_date").html("");
	$("#view_fcrates_modal").modal("hide")
	});
$("#save_forex_rates").on("click",function(){
	var currency_id    = $('select[name="currency_id"] option:selected').val();	
	var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
	if(currency_id >1 && fcy_forex_rate >0 ){			
	    var data = $('#vch_grid_search').pqGrid('option', 'dataModel.data');	
	    $.each(data, function(index,obj){
		if(obj.account_id != '' && obj.account_id != undefined)
		  {					  
	        
			var debit_amount        = parseAmount((obj.debitfc)/fcy_forex_rate);
			var credit_amount       = parseAmount((obj.creditfc)/fcy_forex_rate);
			
			data[index]['debit']    = debit_amount;
			data[index]['credit']   = credit_amount;
		  }
	  }); 
	 $('#vch_grid_search').pqGrid('option', 'dataModel.data', data);
     $('#vch_grid_search').pqGrid('refreshDataAndView');		
	 $("#view_fcrates_modal").modal("hide") 
	}
	else{
		alert_notification("Forex rate is required!!!");
	}		 
	
 });

  $("#view_fcrates_modal").on("hidden.bs.modal",function(){
	$("#view_fcrates_modal #fcy_voucher_no").html("");
	$("#view_fcrates_modal #fcy_voucher_date").html("")
  });
	
 function calculate_fc_rates(currency_id,colModelVch,currency_symbol){	
       if(parseInt(currency_id) >1){
                $("#view_fcrates_modal #selcyrlabel").html(currency_symbol);				
				$("#view_fcrates_modal #fcy_voucher_no").html($("#voucher_no").val());
				$("#view_fcrates_modal #fcy_voucher_date").html($("#voucher_date").val());				
			    $("#view_fcrates_modal").modal('show');	
				 
				colModelVch[5].hidden=false;
                colModelVch[5].width= 150;
				colModelVch[5].title='CREDIT('+currency_symbol+')'; 
				
				colModelVch[3].hidden=false;
                colModelVch[3].width= 150;
				colModelVch[3].title='DEBIT('+currency_symbol+')';
				
				$("#vch_grid_search").pqGrid( "option", "colModel", colModelVch );             
                $("#vch_grid_search").pqGrid( {autoFit: true} );
                $("#vch_grid_search").pqGrid("refreshCM");
                $("#vch_grid_search").pqGrid("refresh");
			 }
			 else{
				$("#view_fcrates_modal").modal('hide');				
				colModelVch[3].hidden=true;
                colModelVch[5].hidden=true;
				$("#vch_grid_search").pqGrid( "option", "colModel", colModelVch );             
                $("#vch_grid_search").pqGrid( {autoFit: true} );
                $("#vch_grid_search").pqGrid("refresh"); 
			 }	
}
/*********************  FC Rates  END          *********************/


    var voucher_type_id = <?= $voucher_type_id ?>;

    $(document).on('change', '#oCheck', function(){

        $('#bbbCheck').prop('checked', false);
        $('#ccCheck').prop('checked', false);
        $('#rCheck').prop('checked', false);

        if($(this).prop('checked'))
            $('.bbbccCheck').css('display', 'none');
        else
            $('.bbbccCheck').css('display', 'inline-block');
    });

    <?php if($voucher_type_id == 5){ ?>

    var voucher_no = <?= $voucher_no ?>;
    var reverse_voucher_no = <?= $reverse_voucher_no ?>;

    var voucher_series_dropdown = <?= json_encode($voucher_series_dropdown) ?>;
    var reverse_voucher_series_dropdown = <?= json_encode($reverse_voucher_series_dropdown) ?>;

    $(document).on('change', '#rCheck', function(){
        $('#bbbCheck').prop('checked', false);
        $('#ccCheck').prop('checked', false);
        $('#oCheck').prop('checked', false);

        if($(this).prop('checked')){
            $('input[name="voucher_no"]').val(reverse_voucher_no);
            set_voucher_series(reverse_voucher_series_dropdown);

            $('.bbbccoCheck').css('display', 'none');
            $('.rDate').css('display', 'block');

            $('#heading').text('Reverse Jounal Voucher');
            $('.open_voucher_txn_history').data('id', 16);
        }
        else{
            $('input[name="voucher_no"]').val(voucher_no);
            set_voucher_series(voucher_series_dropdown);

            $('.bbbccoCheck').css('display', 'inline-block');
            $('.rDate').css('display', 'none');

            $('#heading').text('Jounal Voucher');
            $('.open_voucher_txn_history').data('id', 5);
        }
    });

    function set_voucher_series(list)
    {
        var html = '';
        $.each(list, function(index, value){
            html += '<option value="'+index+'">'+value+'</option>';
        });
        $('select[name="voucher_series"]').html(html);
    }  

    <?php } ?>

    var intRegex = /^\d+$/;
    var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;


    var drcrlist     = [{"C":"C"},{"D":"D"}];

    var bbb_data = [];
    var bbb_accounts = [];

    var cc_data = [];
    var cc_accounts = [];

    var pr_data = [];
    var pr_accounts = [];

    var item_checked = [];

             
    $("#submitbtn").on("click",function(){
        bbb_data = [];
        bbb_accounts = [];
        cc_data = [];
        cc_accounts = [];

        item_checked    = [];

        var oCheck = $('oCheck').prop('checked');
        var rCheck = $('rCheck').prop('checked');

        var count_cash_c_side = 0;
        var count_cash_d_side = 0;
        var count_non_cash = 0;
        var count_contra_bsd = 0;
        
        var final_account_id = [];
        var data = $("#vch_grid_search").pqGrid('option', 'dataModel.data');
        
       
        var dateFrom = '<?php echo $fy_begndt;?>';
        var dateTo   = '<?php echo $fy_end;?>';
        var dateCheck  =  $("#voucher_date").val();

          const party = $('#debit_acc').val();

            var selectedOption = $('#party_ids option').filter(function () {
            return $(this).val() === party;
            });
  
        var debit_acc = selectedOption.attr('id'); 

        // console.log(debit_acc);
        


        // var debit_Text = $('#debit_acc option:selected').text();
        
        var d1 = dateFrom.split("-");
        var d2 = dateTo.split("-");
        var c  = dateCheck.split("-");
        
        var from_year = d1[2];  // -1 because months are from 0 to 11
        var to_year   = d2[2];
        var check_year = c[2];//, parseInt(c[1])-1, c[0]);
        
        if( (check_year==from_year) || (check_year==to_year))
            var datevalidate=1;
        else
            var datevalidate=0;
        
        
        var debitsum=0;
        var creditsum=0;
        
        for (var i = 0; i < data.length; i++) {

            var drcr             = data[i]['drcr'];
            var account_id       = data[i]['account_id'];
            var account_name     = data[i]['account_name'];
            var is_bbb           = data[i]['is_bbb'];
            var is_cc            = data[i]['is_cc'];
            var is_cash          = data[i]['is_cash'];
            var acc_type          = data[i]['acc_type'];
            var description      = data[i]['description'];
            var debit            = data[i]['debit'];
            var credit           = data[i]['credit'];
            
            if(account_id != '' && account_name != ''){

                debitsum  += parseAmount(debit);
                creditsum += parseAmount(credit);
                
                var item_data = {
                    "drcr": drcr,
                    "account_id":  account_id,
                    "acc_type":  acc_type,
                    "description" :description,
                    "debit" : parseAmount(debit),
                    "credit" : parseAmount(credit),

                    "is_bbb" : is_bbb,
                    "is_cc" : is_cc,
                }
                
                if(is_bbb == 1)
                {   
                    var amount = 0;
                    if(drcr == 'C')
                        amount = credit;
                    if(drcr == 'D')
                        amount = debit;
                    
                    if($('#bbbCheck').is(":checked"))
                    {
                        bbb_accounts.push({
                            "account_id":  account_id,
                            "account_name": account_name,
                            "drcr": drcr,
                            "amount": parseAmount(amount),
                            "grid": generateBillData(account_id)
                        }); 
                    }
                    
                }
                
                if(is_cc == 1)
                {   
                    var amount = 0;
                    if(drcr == 'C')
                        amount = credit;
                    if(drcr == 'D')
                        amount = debit;
                    
                    if($('#ccCheck').is(":checked"))
                    {
                        cc_accounts.push({
                            "account_id"    : account_id,
                            "account_name"  : account_name,
                            "acc_type"      : acc_type,
                            "drcr"          : drcr,
                            "amount"        : parseAmount(amount),
                            "grid"          : generateCcData(account_id,acc_type)
                        }); 
                    }
                }

                if($('#prCheck').is(":checked"))
                {
                    var amount = 0;
                    if(drcr == 'C')
                        amount = credit;
                    if(drcr == 'D')
                        amount = debit;

                    pr_accounts.push({
                        "account_id"    : account_id,
                        "account_name"  : account_name,
                        "drcr"          : drcr,
                        "acc_type"      : acc_type,
                        "amount"        : parseAmount(amount),
                        "grid"          : generatePrData(account_id)
                    }); 
                }

                if(is_cash == 1)
                {
                    if(drcr == 'C')
                        count_cash_c_side++;

                    if(drcr == 'D')
                        count_cash_d_side++;
                }
                else
                    count_non_cash++;

                if(voucher_type_id == 1 && acc_type == 'bsd')
                {
                    count_contra_bsd++;
                }
                
                item_checked.push(item_data);
            
            }
        } 


        // console.log(pr_accounts);
        var error_cash = 0;
        // if(voucher_type_id == 9 && count_cash_c_side == 0)
        //     error_cash = 1;
        // if(voucher_type_id == 13 && count_cash_d_side == 0)
        //     error_cash = 1;

        var error_cash_wrong_side = 0;
        if(voucher_type_id == 9 && count_cash_d_side > 0)
            error_cash_wrong_side = 1;
        if(voucher_type_id == 13 && count_cash_c_side > 0)
            error_cash_wrong_side = 1;

        var error_non_cash = 0;
        if(voucher_type_id == 1 && count_non_cash > 0)
            error_non_cash = 1;

        var error_contra_bsd = 0;
        if(voucher_type_id == 1 && count_contra_bsd > 0)
            error_contra_bsd = 1;
			
		var errros=0;
        var error_msg ='<table width="100%">';		

         if(datevalidate==0){
            error_msg +="<tr><td class='tdtext-left'>Voucher date is wrong.</td></tr>";
            errros=errros+1;    
        } 
		if($("#voucher_series").val()==''){
            error_msg +="<tr><td class='tdtext-left'>Voucher series is required.</td></tr>";
            errros=errros+1;       
        }

        if($("#debit_acc").val()==''){
            error_msg +="<tr><td class='tdtext-left'>Receipt Account is required.</td></tr>";
            errros=errros+1;       
        }
         if(item_checked.length==0){
            error_msg +="<tr><td class='tdtext-left'>Kindly fill the voucher data.</td></tr>";
            errros=errros+1;    
        }        
         if(error_cash){
            if(item_checked.length >0 && voucher_type_id == 9){
                error_msg +="<tr><td class='tdtext-left'>Atleast one Account from Cash & Cash Equivalent Group is required on credit side .</td></tr>";
				errros=errros+1;   
			}
            if(item_checked.length >0 && voucher_type_id == 13)
                error_msg +="<tr><td class='tdtext-left'>Atleast one Account from Cash & Cash Equivalent Group is required on debit side .</td></tr>";
               errros=errros+1;
            }
         if(error_cash_wrong_side){
            if(item_checked.length >0 && voucher_type_id == 9){
                error_msg +="<tr><td class='tdtext-left'>Account from Cash & Cash Equivalent Group is not allowed on debit side .</td></tr>";
				errros=errros+1;
			}
            if(item_checked.length >0 && voucher_type_id == 13){
                error_msg +="<tr><td class='tdtext-left'>Account from Cash & Cash Equivalent Group is not allowed on credit side !!!<br>";
				errros=errros+1;
			}
            
        }
         if(item_checked.length >0 && error_non_cash=="1"){
            error_msg +="<tr><td class='tdtext-left'>Accounts only from Cash & Cash Equivalent Group are allowed .</td></tr>";
            errros=errros+1;
        }
         if(item_checked.length >0 && error_contra_bsd=="1"){
            error_msg +="<tr><td class='tdtext-left'>Bill Sundry Accounts are not allowed .</td></tr>";
            errros=errros+1;
        }
        //  if(parseAmount(debitsum) != parseAmount(creditsum)){
        //     error_msg +="<tr><td class='tdtext-left'>voucher totals do not match.</td></tr>";
        //     errros=errros+1;   
        // } 
		 if($("#currency_id").val() >1 && $("#view_fcrates_modal #fcy_forex_rate").val()==''){
            error_msg +="<tr><td class='tdtext-left'>&nbsp;</td><td>Forex rate can not be blank.</td></tr>";
            errros=errros+1;  
        }
		error_msg +="</table>";
		if(errros>0){
		 alert_notification(error_msg);	
		 return false;
		}		
        else{
            show_loader();
            $("#voucherdata").val(JSON.stringify(item_checked));

            if(!rCheck && !oCheck && $('#bbbCheck').is(":checked") && bbb_accounts.length > 0){ // bbbstart
                readyBills();
            }
            else if(!rCheck && !oCheck && $('#ccCheck').is(":checked") && cc_accounts.length > 0){ //ccstart
                readyCc();
            }
            else if(!rCheck && !oCheck && $('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
            else{
                $("#form1").submit();
            }
                
        }
       
    });

    $(document).on('submit', '#form1', function(e){
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);

        // console.log(formData);

        var selectedText = $('#debit_acc').val();


        var selectedOption = $('#party_ids option').filter(function () {
            return $(this).val() === selectedText;
        });

        var accId = selectedOption.attr('id');

        // Append the ID to the FormData manually
        formData.append('debit_acc', accId);
        

        $.ajax({
            url: '<?php echo base_url().'/'.$folder_path.'vouchers/receipt_classic/'.$voucher_type_id;?>', 
            type: 'POST',
            data: formData,
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                // show_loader();
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    stop_loader();
                    window.location.href='<?php echo base_url();?>/admin/vouchers/invoice/<?php echo $voucher_type_id;?>'; 
                }
                else{
                    stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = '';
                        $.each(response.errors, function(index, value){
                            list += '<li>'+value+'</li>';
                        });

                        var html = '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button><ul>'+list+'</ul></div>';
                        $('#validation_errors').html(html);
                        window.scrollTo(0,0);
                    }  
                }
                
            },
            complete: function() {
                stop_loader();
                $('#submitbtn').attr('disabled', false);
            },
            error: function (jqXHR, exception) {
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
                } else {
                    error = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                alert_notification(error);
            },
        });

    });


     var rd_tm_obj;    
    function set_row_details_label()
    {
      if(rd_tm_obj){
        clearTimeout(rd_tm_obj);
      }
      rd_tm_obj = setTimeout(function(){
          var select_row = $("#vch_grid_search").pqGrid("selection", { type:'cell', method:'getSelection'});
          if(select_row && select_row[0])
          {
              var rd = select_row[0].rowData;
              if(rd.account_id){
                var acc_bal = rd.account_balance;
                if(acc_bal){
                  if(acc_bal < 0)
                    acc_bal = formatAmount(Math.abs(acc_bal)) + ' Cr';
                  else
                    acc_bal = formatAmount(acc_bal) + ' Dr';
                }
                else{
                  acc_bal = '0 Dr';
                }

                 $('#account_details_label .acc_name').text(rd.account_name);
                 $('#account_details_label .acc_bal').text(rd.account_balance);
              }
              else{
                  $('#account_details_label .acc_name').text('');
                 $('#account_details_label .acc_bal').text('');
              }
          }
          else{
                $('#account_details_label .acc_name').text('');
               $('#account_details_label .acc_bal').text('');
            }
      },500);   
    }

    $(function () {
 
        var accnt_balances = {};

        $(document).on('change', '#voucher_date', function(e){

            var voucher_date = $("#voucher_date").val();
            var account_id_array = [];

            $.each(accnt_balances, function(index, value){
                account_id_array.push(index);
            });
            
            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_all_account_balances', 
                type: 'POST',
                data: {voucher_date: voucher_date, account_id_array: account_id_array},
                dataType: "json",
                beforeSend: function() {
                    show_loader();
                },
                success: function (response) {
                    // console.log(response);
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    if(response.status){
                        if(response.account_balances.length > 0){
                            $.each(response.account_balances, function(index, obj){
                                if(accnt_balances.hasOwnProperty(obj.account_id)) {
                                    accnt_balances[obj.account_id] = obj.balance;
                                }
                            });
                            update_grid_account_balances();
                        }
                    }
                    
                },
                complete: function() {
                    stop_loader();
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
                    alert_notification(error);
                },
            });

        });

       function update_grid_account_balances()
       {
            var data = $("#vch_grid_search").pqGrid('option', 'dataModel.data');

            $.each(data, function(index,obj){
                if(obj.account_id != '' && obj.account_id != undefined)
                {
                    data[index]['account_balance'] = accnt_balances[obj.account_id];
                }
            });


            $("#vch_grid_search").pqGrid('option', 'dataModel.data', data);
            $("#vch_grid_search").pqGrid('refreshDataAndView');
       }


        function calculateSummary() {

        var debitTotal = 0,
            creditTotal = 0,
			creditfcTotal = 0,
			debitfcTotal = 0,			
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){

            if(row.account_id != '' && row.account_name != '')
            {
                if(row.debit == '' || typeof row.debit == 'undefined'){
                    var debit =0;
                }else
                 var debit =  row.debit;
                
                if(row.credit == '' || typeof row.credit == 'undefined'){
                    var credit =0;
                }else
                var  credit =  row.credit;
             
			 
			 if(row.debitfc == '' || typeof row.debitfc == 'undefined'){
                    var debitfc =0;
                }else
                 var debitfc =  row.debitfc;
                
                if(row.creditfc == '' || typeof row.creditfc == 'undefined'){
                    var creditfc =0;
                }else
                var  creditfc =  row.creditfc;
             
                 
                debitTotal  += parseAmount(debit);
                creditTotal += parseAmount(credit); 

				debitfcTotal  += parseAmount(debitfc);
                creditfcTotal += parseAmount(creditfc); 	
            }
        })
        var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
        var totalData = {
                drcr        : "Total",
                debit       : formatAmount(debitTotal),
                credit      : formatAmount(creditTotal),
				debitfc     : formatAmount(debitfcTotal,currency_symbol),
                creditfc    : formatAmount(creditfcTotal,currency_symbol),
                pq_rowcls   : 'grid_footer_color',
                summaryRow  : true
            }
            
            this.option('summaryData', [totalData]);
          }
         
         function disableTextRenderer(ui) {
            var //grid = $(this).pqGrid('getInstance').grid,
                grid = this,
                rowData = ui.rowData,
                rowIndx = ui.rowIndx,
                dataIndx = ui.dataIndx;

            if (grid.isEditableCell({ rowIndx: rowIndx, dataIndx: dataIndx }) == false) {
                //inject disabled class into read only cells.                
                grid.addClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
                
            }
            else {
                grid.removeClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
        };
        
        var autoCompleteEditor = function (uimain) {
            
            var $inp = uimain.$cell.find("input");
            var rd = uimain.rowData;
            var grid = this;
            
           var colindex = uimain.column.dataIndx;
           
            $inp.autocomplete({
                source: <?php echo $acc_bsd_json_file; ?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength:0,
                select: function(event, ui) {
                    event.preventDefault();
                   // console.log(ui.item);
          					rd.acc_short_code = ui.item.acc_short_code;
					if($("#voucher_date").val()==''){
					    alert_notification("Voucher date is required.");	
						  rd.account_name = '';
						  rd.account_id = '';
						  rd.is_cc = '';
						  rd.is_bbb = '';
						  rd.is_cash = '';
						  rd.acc_type = '';
						  rd.description = '';
						  rd.debit = '';
						  rd.credit = '';
						  rd.debitfc = '';
						  rd.creditfc = '';
					}		
							
          		   if(rd.acc_short_code=="sagstpd" || rd.acc_short_code=="sapnlap"){				
						alert_notification("This Is A System Generated A/C.");	
						rd.account_name = '';
						rd.acc_id = '';				
						return false;
					}
					if(rd.cess_basis=='2'){
						alert_notification("Tax category belongs non-advolerem gst cess which can't be calculated without inventory mrp");	
						rd.account_name = '';
						rd.acc_id = '';				
						return false;
					}
					
					
					
                    if(voucher_type_id == 9 && rd.drcr == 'D' && ui.item.is_cash == 1) // payment
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Cash & cash equivalent accounts are not allowed on debit');

                        return false;
                    }
                    else if(voucher_type_id == 13 && rd.drcr == 'C' && ui.item.is_cash == 1) // receipt
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Cash & cash equivalent accounts are not allowed on credit');

                        return false;
                    }
                    else if(voucher_type_id == 1 && ui.item.is_cash == 0) // contra
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Only cash & cash equivalent accounts are allowed');

                        return false;
                    }
                    else if(voucher_type_id == 1 && ui.item.is_sundry == 1) // contra
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Bill sundry accounts are not allowed');

                        return false;
                    }
                    else
                    {
                        $(this).val(ui.item.label);
                        rd.account_id = ui.item.acc_id;
                        rd.is_bbb = ui.item.is_bbb;
                        rd.is_cc = ui.item.is_cc;
                        rd.is_cash = ui.item.is_cash;

                        if(ui.item.is_sundry==1)                    
                            rd.acc_type = 'bsd';
                        else if(ui.item.is_acc==1)                   
                            rd.acc_type = 'acc';
						
						rd.igst_rate    = ui.item.igst_rate;
						rd.cess_rate    = ui.item.cess_rate;
						rd.item_hsn_sac = ui.item.item_hsn_sac;	
						rd.tax_cat_id   = ui.item.tax_cat_id;
						rd.supply_type  = ui.item.supply_type;	
						rd.acc_short_code = ui.item.acc_short_code;
						rd.cannotselected = ui.item.cannotselected;
						rd.cess_basis = ui.item.cess_basis;

                    }    
                }
                })./* focus(function () {
                    
                    $(this).autocomplete("search", "");
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                }) */focusout(function () {  
                    
					var enteredValue = $(this).val().toLowerCase();
				   var  source_data=<?php echo $acc_bsd_json_file; ?>;				  
				   const isValid = source_data.some(item => item.label.toLowerCase() === enteredValue);
					 if(rd.account_id == '' || isValid==false)
                      {   $(this).val('');
						  $(this).autocomplete("search", "");
                          rd.account_balance = 0;
                          rd.account_name = '';
                          rd.account_id = '';
                          rd.is_cc = '';
                          rd.is_bbb = '';
                          rd.is_cash = '';
                          rd.acc_type = '';
                      }
                    if(rd.account_id != '')
                    {     
                     if(accnt_balances.hasOwnProperty(rd.account_id) ==false ) {
                      var voucher_date = $("#voucher_date").val();
                      var return_response = async function () {
    try {
        // Use a Promise to wrap the AJAX request
        const accbalance = await new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "<?php echo $base_url; ?>ajax/accountbalance/" + rd.account_id + "/" + voucher_date,
                success: function (data) {
                    resolve(data);  // Resolve the Promise with the response data
                },
                error: function () {
                    reject("Error in AJAX request");  // Reject the Promise on error
                }
            });
        });

        // Once the response is resolved, you can use accbalance here
        //console.log("Account balance is: " + accbalance);
		
		
        return accbalance;
    } catch (error) {
        // Handle any errors that occur during the async operation
        console.error(error);
        return null;  // Return null or handle it as needed
    }
};

 // Usage example
async function fetchBalance() {
    const balance = await return_response();
	rd.account_balance = balance;    
}

fetchBalance(); 
                        
                     }
                 
                    }
                   
                         
                    if(rd.account_id == '')
                    {
                        rd.account_balance = 0;
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                    }

                set_row_details_label();
            });
            
            $inp.keydown(function(e) {
               if(e.which == 13) 
               {
                    var event = $.Event( "keydown", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keypress", { which: 9 } );
                    $inp.trigger(event);
                    var event = $.Event( "keyup", { which: 9 } );
                    $inp.trigger(event);
                    
                    grid.isValid( { rowData: rd } );
              }
            });
        }
        
        function crdrEditor(ui) {
        
            var $inp = ui.$cell.find("select"),
                di = ui.dataIndx,
                rd = ui.rowData,               
                grid = this,
                rowIndx = ui.rowIndx;				
				
            $inp.on("change", function (evt) {
                var crdr = $(this).val();				
                if($("#voucher_date").val()==''){
					    alert_notification("Voucher date is required.");	
						  rd.account_name = '';
						  rd.account_id = '';
						  rd.is_cc = '';
						  rd.is_bbb = '';
						  rd.is_cash = '';
						  rd.acc_type = '';
						  rd.description = '';
						  rd.debit = '';
						  rd.credit = '';
						  rd.debitfc = '';
						  rd.creditfc = '';
					}
                if(crdr == '')
                {
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                    rd.description = '';
                    rd.debit = '';
					rd.debitfc = '';
                    rd.credit = '';
					 rd.creditfc = '';
                    grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
                     grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
					 
					 grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'creditfc', cls: 'grid_footer_color' });
                     grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debitfc', cls: 'grid_footer_color' });
                    
                }
                if(crdr == 'C'){
                   var amount = expected_drcr(grid,crdr);
				   var amountfc = expected_drcr_fc(grid,crdr);
                    rd.credit = amount > 0 ? amount : '';
					rd.creditfc = amountfc > 0 ? amountfc : '';
                    rd.debit = '';
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                    rd.description = '';

                   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
				   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'debitfc', cls: 'grid_footer_color' });
                   grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
				   grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'creditfc', cls: 'grid_footer_color' });

                    // grid.saveEditCell();
                    // grid.setSelection( {rowIndx: rowIndx,colIndx: 4} );
                }
                if(crdr == 'D'){
                    var amount = expected_drcr(grid,crdr);
					var amountfc = expected_drcr_fc(grid,crdr);
                    rd.credit = '';
                    rd.debit = amount > 0 ? amount : '';
					rd.debitfc = amountfc > 0 ? amountfc : '';
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                    rd.description = '';

                   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
				   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'creditfc', cls: 'grid_footer_color' });
                   grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
				    grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debitfc', cls: 'grid_footer_color' });

                   // grid.saveEditCell();
                   // grid.setSelection( {rowIndx: rowIndx,colIndx: 3} );
                }
                
            })
        };

        function expected_drcr(grid,drcr)
        {
            var debitTotal = 0;
            var creditTotal = 0;
            var data = grid.option('dataModel.data');


            data.forEach(function(row){
                if(row.debit == '' || typeof row.debit == 'undefined'){
                    var debit =0;
                }else
                 var debit =  row.debit;
                
                if(row.credit == '' || typeof row.credit == 'undefined'){
                    var credit =0;
                }else
                var  credit =  row.credit;
             
                 
                debitTotal  += parseAmount(debit);
                creditTotal += parseAmount(credit);
            });

            if(drcr == 'C'){
                return debitTotal - creditTotal;
            }
            if(drcr == 'D'){
                return creditTotal - debitTotal;
            }
            return 0;
        }
		function expected_drcr_fc(grid,drcr)
        {
            var debitfcTotal = 0;
            var creditfcTotal = 0;
            var data = grid.option('dataModel.data');


            data.forEach(function(row){
                if(row.debitfc == '' || typeof row.debitfc == 'undefined'){
                    var debitfc =0;
                }else
                 var debitfc =  row.debitfc;
                
                if(row.creditfc == '' || typeof row.creditfc == 'undefined'){
                    var creditfc =0;
                }else
                var  creditfc =  row.creditfc;
             
                 
                debitfcTotal  += parseAmount(debitfc);
                creditfcTotal += parseAmount(creditfc);
            });

            if(drcr == 'C'){
                return debitfcTotal - creditfcTotal;
            }
            if(drcr == 'D'){
                return creditfcTotal - debitfcTotal;
            }
            return 0;
        } 
        <?php
    if ($voucher_type_id == 9) {

        ?>
        

        var colModelVch = [
            

            
            { title: "PAID TO", sortable:false,dataIndx: "account_name", width: 100,dataType: "text",cls: 'pq-drop-icon pq-side-icon',
                editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: autoCompleteEditor,
                          options: [],
                      },
                      
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var account_balance = '0';
                    

                    if(typeof rd.account_id !== "undefined" && rd.account_id != '' && rd.account_name != '')
                    {      
                        // var account_balance = rd.account_balance;
                        // grid.addClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'pq-cell-red-tr pq-has-tooltip' });

                        // return '<span data-title-tooltip="  '+account_balance+' ">'+ui.cellData+'</span>';

                       return rd.account_name;
                    }
                    return '';
                }          
                       
            },
            
            { title: "SHORT NARRATION", sortable:false,width: 100, dataType: "string", dataIndx: "description",
                editable: function (ui) {
                   var account_id = ui.rowData['account_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                },
            },
			{ title: "DEBIT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "debitfc",dataType: "float",
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var account_id = ui.rowData['account_id'];
                    if (drcrval=='D' && account_id != '') {
                        return true;
                    }
                    return false;
                },
				editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");                        
                        $inp.on("change", function (evt){
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                                if(rd.debitfc != '' && parseAmount(rd.debitfc) >= 0)
                			    {
                				    if(parseInt(currency_id)>1){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.debit   = parseAmount((rd.debitfc)/fcy_forex_rate);	
                				     else
                				     	rd.debit   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.debitfc = 0;
                					}	
                                }else
									rd.debitfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'D')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        if(rd.debitfc >0){
                            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					    return formatAmount(parseAmount(rd.debitfc),currency_symbol);   
                        }
                        return '';
                    }
                }
				
              },
              {
                title: "AMOUNT",
                sortable: false,
                width: 100,
                align: "left",
                dataIndx: "debit",
                dataType: "float",
                validations: [
                    { type: 'gte', value: 0, msg: "should be > 0" }
                ],
                editable: function (ui) {
                    var account_id = ui.rowData['account_id'];
                    return account_id !== '';
                },
                render: function (ui) {
                    var rd = ui.rowData;
                    var grid = this;

                    grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });

                    rd.credit = ''; // clear credit always
                    if (rd.debit !== '') {
                        rd.debit = parseAmount(rd.debit);
                        return formatAmount(rd.debit);
                    }

                    return '';
                }
            },
			{ title: "CREDIT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "creditfc",dataType: "float",
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var account_id = ui.rowData['account_id'];
                    if (drcrval=='C' && account_id != '') {
                        return true;
                    }
                    return false;
                },
				editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");                        
                        $inp.on("change", function (evt){
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                                if(rd.creditfc != '' && parseAmount(rd.creditfc) >= 0)
                			    {
                				    if(parseInt(currency_id)>1){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.credit   = parseAmount((rd.creditfc)/fcy_forex_rate);	
                				     else
                				     	rd.credit   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.creditfc = 0;
                					}	
                                }else
									rd.creditfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'C')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        rd.debit = '';
                         if(rd.creditfc >0){
                            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					       return formatAmount(parseAmount(rd.creditfc),currency_symbol);   	
                        }
                        return '';
                    }
                }
				
              },
            // { title: "CREDIT", sortable:false,width: 100, align: "right",dataIndx: "credit",dataType: "float",
            //     validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
            //     editable: function (ui) {
            //        var drcrval = ui.rowData['drcr'];
            //        var account_id = ui.rowData['account_id'];
            //         if (drcrval=='C' && account_id != '') {
            //             return true;
            //         }
            //         return false;
            //     },
            //     render: function( ui ) {
            //         var rd = ui.rowData;
            //         var grid = this;
            //         if(rd.drcr == 'C')
            //         {
            //             grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
            //             rd.debit = '';
            //             if(rd.credit != ''){
            //                 rd.credit = parseAmount(rd.credit);
            //                 return formatAmount(rd.credit);   
            //             }
            //             return '';
            //         }
            //     }
            //  }
        ];

        <?php }else{ ?>
            var colModelVch = [
            

            
            { title: "RECEIVED FROM", sortable:false,dataIndx: "account_name", width: 100,dataType: "text",cls: 'pq-drop-icon pq-side-icon',
                editor: {                   
                      type: "textbox",
                      attr: "autocomplete='off'",
                          init: autoCompleteEditor,
                          options: [],
                      },
                      
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var account_balance = '0';
                    

                    if(typeof rd.account_id !== "undefined" && rd.account_id != '' && rd.account_name != '')
                    {      
                        // var account_balance = rd.account_balance;
                        // grid.addClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'pq-cell-red-tr pq-has-tooltip' });

                        // return '<span data-title-tooltip="  '+account_balance+' ">'+ui.cellData+'</span>';

                       return rd.account_name;
                    }
                    return '';
                }          
                       
            },
            
            { title: "SHORT NARRATION", sortable:false,width: 100, dataType: "string", dataIndx: "description",
                editable: function (ui) {
                   var account_id = ui.rowData['account_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                },
            },
			{ title: "DEBIT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "debitfc",dataType: "float",
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var account_id = ui.rowData['account_id'];
                    if (drcrval=='D' && account_id != '') {
                        return true;
                    }
                    return false;
                },
				editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");                        
                        $inp.on("change", function (evt){
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                                if(rd.debitfc != '' && parseAmount(rd.debitfc) >= 0)
                			    {
                				    if(parseInt(currency_id)>1){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.debit   = parseAmount((rd.debitfc)/fcy_forex_rate);	
                				     else
                				     	rd.debit   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.debitfc = 0;
                					}	
                                }else
									rd.debitfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'D')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        if(rd.debitfc >0){
                            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					    return formatAmount(parseAmount(rd.debitfc),currency_symbol);   
                        }
                        return '';
                    }
                }
				
              },
            //   {
            //     title: "AMOUNT",
            //     sortable: false,
            //     width: 100,
            //     align: "left",
            //     dataIndx: "debit",
            //     dataType: "float",
            //     validations: [
            //         { type: 'gte', value: 0, msg: "should be > 0" }
            //     ],
            //     editable: function (ui) {
            //         var account_id = ui.rowData['account_id'];
            //         return account_id !== '';
            //     },
            //     render: function (ui) {
            //         var rd = ui.rowData;
            //         var grid = this;

            //         grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });

            //         rd.credit = ''; // clear credit always
            //         if (rd.debit !== '') {
            //             rd.debit = parseAmount(rd.debit);
            //             return formatAmount(rd.debit);
            //         }

            //         return '';
            //     }
            // },
			{ title: "CREDIT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "creditfc",dataType: "float",
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var account_id = ui.rowData['account_id'];
                    if (drcrval=='C' && account_id != '') {
                        return true;
                    }
                    return false;
                },
				editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");                        
                        $inp.on("change", function (evt){
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                                if(rd.creditfc != '' && parseAmount(rd.creditfc) >= 0)
                			    {
                				    if(parseInt(currency_id)>1){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.credit   = parseAmount((rd.creditfc)/fcy_forex_rate);	
                				     else
                				     	rd.credit   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.creditfc = 0;
                					}	
                                }else
									rd.creditfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'C')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        rd.debit = '';
                         if(rd.creditfc >0){
                            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					       return formatAmount(parseAmount(rd.creditfc),currency_symbol);   	
                        }
                        return '';
                    }
                }
				
              },
              {
                title: "AMOUNT",
                sortable: false,
                width: 100,
                align: "left",
                dataIndx: "credit",
                dataType: "float",
                validations: [{ type: 'gt', value: 0, msg: "should be > 0" }],
                editable: true, // always editable
                render: function (ui) {
                    var rd = ui.rowData;
                    var grid = this;
                    grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                    rd.debit = ''; // always reset debit
                    if (rd.credit !== '') {
                    rd.credit = parseAmount(rd.credit);
                    return formatAmount(rd.credit);
                    }
                    return '';
                }
                }

        ];

            <?php } ?>
        
        
        var dataModelVch = {"data":<?php echo json_encode($json_data);?>}
      
        var newObjvch = {
           
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            hoverMode:'cell',
            pageModel: { type: 'local' },
            dataModel: dataModelVch,
            colModel: colModelVch,  
            change: calculateSummary,
            dataReady: calculateSummary, 
            numberCell: { show: true,width: 30, title: "#" },
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            wrap:false,
             create: function (evt, ui) {// make first row auto selected
            this.widget().pqTooltip();
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };

        newObjvch.cellKeyDown = function(evt, ui) {
            set_row_details_label();
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                });
                this.refreshDataAndView();
                return false;
           }
        }

        newObjvch.cellClick = function( event, ui ) {
            set_row_details_label();
          
        };

        var $grid = $("#vch_grid_search").pqGrid(newObjvch);
        
      $("#fcratespopup").on("click",function(){
			var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			var currency_id    = $('select[name="currency_id"] option:selected').val();
			calculate_fc_rates(currency_id,colModelVch,currency_symbol);  
		  }); 
	   $("#currency_id").on("change",function(){			
			if($(this).val()=="1"){
			 var data = $('#vch_grid_search').pqGrid('option', 'dataModel.data');

			 $.each(data, function(index,obj){
					if(obj.account_id != '' && obj.account_id != undefined )
						{
							if(bo_gstin_type=="1"){ // means Regular
							var igst_rate_val = obj.igst_rate;
						}
						else if(bo_gstin_type=="2"){ // means Composition
							if((obj.tax_exempted=='n') && (obj.supply_type=="1" || obj.supply_type=="3"))
							 var igst_rate_val = goods_rate;
							 else if(obj.tax_exempted=='n' && obj.supply_type=="2")
							 var igst_rate_val = services_rate;
							 else
								 var igst_rate_val =0; 
						     }						 
						  }
					});	
				
			    $('#vch_grid_search').pqGrid('option', 'dataModel.data', data);
                $('#vch_grid_search').pqGrid('refreshDataAndView');					
			}			
			
			   var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			   calculate_fc_rates($(this).val(),colModelVch,currency_symbol);			  
			 
			});
		  
     });
	 
	 
		  
		  
		  
     
     <?php if($voucher_type_id == 5){ ?>
     $(document).on('change', '[name="type"]', function(e){
         var type = $(this).val();
         e.preventDefault();
         if(type == 'with_item'){
            $(this).val('without_item');
            window.location.href = "<?php echo $base_url; ?>vouchers/item";
         }
         else if(type == 'without_item'){
             $(this).val('with_item');
             window.location.href = "<?php echo $base_url; ?>vouchers/invoice/<?= $voucher_type_id ?>";
         }
     })
    <?php } ?>



//------------------------------------------------------------------------------ Bill By Bill  bbbstart

    var saved_bill_txns = <?= json_encode($migrate_bbb_data) ?>;
    if(saved_bill_txns.length > 0){
        $('#bbbCheck').prop('checked', true);
    }

    $("#save_bill_by_bill").on("click",function(){
        var status = true;
        status = validateBills();
        if(!status){
            return;
        }
        status = validateAllBillsData();
        if(!status){
            return;
        }
        if(status){
            saveBillByBillData();
            $('#billsModal').modal('hide');
            $("#bbbdata").val(JSON.stringify(bbb_data));
            show_loader();

            var oCheck = $('oCheck').prop('checked');
            var rCheck = $('rCheck').prop('checked');

            if(!rCheck && !oCheck && $('#ccCheck').is(":checked") && cc_accounts.length > 0){ //ccstart
                readyCc();
            }
            else if(!rCheck && !oCheck && $('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
            else{
                $("#form1").submit();
            }
        }
    });

    function saveBillByBillData()
    {
        $.each(bbb_accounts, function(index,obj)
        {
            var account_id = obj.account_id;
            
            $.each(obj.grid, function(index2, obj2)
            {
                if(obj2.method != '' && obj2.reference != '')
                {
                    var method     = obj2.method;
                    var reference  = obj2.reference;
                    var reference_id  = obj2.reference_id;
                    var amount     = obj2.amount;
                    var drcr       = obj2.drcr;
                    var due_date   = obj2.due_date;
                    var narration  = obj2.narration;

                    bbb_data.push({
                        "account_id": account_id,
                        "method"    : method,
                        "reference" : reference,
                        "reference_id" : reference_id,
                        "amount"    : parseAmount(amount),
                        "drcr"      : drcr,
                        "due_date"  : due_date,
                        "narration" : narration
                    });
                }
            });
            
        });
    }

    function validateAllBillsData()
    {
        var status = true;
        $.each(bbb_accounts, function(index,obj){

            var final_amount = obj.amount;
            var final_drcr = obj.drcr;
            var final_total = parseAmount(final_amount);
            if(final_drcr == 'C'){
                final_total = -final_total;
            }
            
            var total = 0;
            $.each(obj.grid, function(index2, obj2){
                var method     = obj2.method;
                var reference  = obj2.reference;
                var amount     = obj2.amount;
                var drcr       = obj2.drcr;
                
                
                if(method!='' && reference!='')
                {   
                    var sub = 0;
                    if(drcr == 'D'){
                        sub = parseAmount(amount);
                    }
                    if(drcr == 'C'){
                        sub = -parseAmount(amount);
                    }
                    total += sub;
                }
            });
            if(total != final_total){
                alert('Please fill all the grids ');
                status = false;
                return status;
            }
        });
        return status;
    }

    var billIndex = 0;

    function readyBills()
    {
        var account_id_array = bbb_accounts.map(function(obj) { return obj.account_id; });
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/vouchers/getAccountBillRefs",
            data: {account_id_array: account_id_array},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status)
                {
                    $.each(response.data, function(index,obj)
                    {
                        bbb_accounts[index].ref_list = obj;
                    });
                    stop_loader();
                    readyBillsModel();
                }
            }
        });          
    }

    function validateBills()
    {
        var sum = 0;
        var data = $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data');
        var error = 0;
        var count = 0;
        
        for (var i = 0; i < data.length; i++) {
            var method     = data[i]['method'];
            var reference  = data[i]['reference'];
            var reference_id  = data[i]['reference_id'];
            var amount     = data[i]['amount'];
            var drcr       = data[i]['drcr'];;
            
            
            if(method!='' && reference!='')
            {
                count++;
                if(reference == '' || drcr == ''){
                    error = 1;
                }
                if(amount == '' || amount <= 0){
                    error = 1;
                }
                
                var sub = 0;
                if(drcr == 'D'){
                    sub = parseAmount(amount);
                }
                if(drcr == 'C'){
                    sub = -parseAmount(amount);
                }
                sum += sub;
                  
            }
        }
        // if(count == 0){
        //     error = 1;
        // }
        if(error){
            alert("Kindly fill the details correctly !!!");
            return false;
        }
        var final_amount = bbb_accounts[billIndex].amount;; 
        var final_drcr = bbb_accounts[billIndex].drcr;
        
        if(final_drcr == 'C'){
            final_amount = -final_amount;
        }
        
        if(sum != parseAmount(final_amount) && count > 0){
            alert('Total Mismatch');
            return false;
        }
        
        return true;
    }

    function readyBillsModel(step = '')
    {
        if(step == ''){
            billIndex = 0;
        }
        else{
            var status = validateBills();
            if(!status){
                return;
            }
        }
        if(step == 'next'){
            if(billIndex < (bbb_accounts.length - 1)){
                billIndex = billIndex + 1;
            }
        }
        if(step == 'prev'){
            if(billIndex > 0){
                billIndex = billIndex - 1;
            }
        }
        if(bbb_accounts[billIndex])
        {
            var account_name = bbb_accounts[billIndex].account_name;
            var amount = bbb_accounts[billIndex].amount;
            var drcr = bbb_accounts[billIndex].drcr;
            var page = (billIndex + 1) + '/' + bbb_accounts.length;
            
            $('#bills_account').text(account_name);
            $('#bills_total').html(formatAmount(amount));
            $('#bills_drcr').text(drcr + 'r');
            $('#bills_page').text(page);
            

            $('#billsModal').modal('show');
            
            $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', bbb_accounts[billIndex].grid);
            $("#bill_by_bill_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();//tempararily shift focus
            $("#bill_by_bill_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
        }
        
    }

                
    function generateBillData(account_id = 0)
    {
        var json = [];
        
        if(account_id != 0){
            var i = saved_bill_txns.findIndex(function(o) {
               return o.acc_id == account_id;
            });
            if(i >= 0){
                $.each(saved_bill_txns[i].bills_txn_list, function(index, obj){
                    json.push({'method': 'Adjustment', 'reference': obj.bills_ref_name, 'reference_id': obj.bills_ref_id, 'amount': obj.bills_txn_amt, 'drcr': obj.bills_txn_drcr,
                                'due_date': obj.bill_due_date, 'narration': obj.bills_txn_narr});
                });
            }
        }
        
        for(var i=0;i<25;i++){
            json.push({'method': '', 'reference': '', 'reference_id': '', 'amount': '', 'drcr': '', 'due_date': '', 'narration': ''});
        }
        return json;
    }
                
    function dateEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,                
            grid = this;

        $inp.on("focusout", function (e) {
            var date = rd.due_date;
            
            if(!isValidDate(date)){
              rd.due_date = '';
              e.preventDefault();
            }
        });
        
        $inp.inputmask("99/99/9999", {
            mask: "99-99-9999",
            alias: "date",
            placeholder: "dd-mm-yyyy",
            insertMode: false,
        }).datepicker({
            altFormat: "dd-mm-yyyy",
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear: true,
            minDate:'<?php echo $fy_begndt;?>',
            maxDate:'<?php echo $fy_end;?>',
            onClose: function () {
                this.focus();
            }

        });
    };
                
    function methodEditor(ui) {
        var $inp = ui.$cell.find("select"),
            di = ui.dataIndx,
            rd = ui.rowData,               
            grid = this;
        
        $inp.on("change", function (evt) {
            var method = $(this).val();
            
            rd.reference = '';
            rd.reference_id = '';
            rd.amount = '';
            rd.drcr = '';
            rd.due_date = '';
            rd.narration = '';

            if(method != ''){
                var expected = expected_bills_amount(grid);
                rd.amount = expected.amount;
                rd.drcr = expected.drcr;
            }
        })
    };

    function expected_bills_amount(grid)
    {
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = bbb_accounts[billIndex].amount; // 1000
        var master_drcr = bbb_accounts[billIndex].drcr; // D

        if(master_drcr == 'D')
            amount = parseAmount(master_amount);
        if(master_drcr == 'C')
            amount = -parseAmount(master_amount);

        data.forEach(function(row){
            if(row.amount != '' && row.drcr != ''){
                if(row.drcr == 'C')
                    amount  += parseAmount(row.amount);
                if(row.drcr == 'D')
                    amount  -= parseAmount(row.amount);
            }
        });

        if(amount < 0){
            return {amount: Math.abs(amount), drcr: 'C'};
        }
        if(amount > 0){
            return {amount: amount, drcr: 'D'};
        }

        return {amount: '', drcr: ''};
    }
                
    function referenceEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;
            
        if(rd.method == 'Adjustment')
        {
            $inp.autocomplete({
                source:  bbb_accounts[billIndex].ref_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.reference_id = ui.item.id;
                    rd.reference = ui.item.label;
                    rd.due_date = ui.item.due_date;
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.reference = '';
                rd.reference_id = '';
                rd.due_date = '';
            }).focusout(function () {              
                if(rd.reference != '' && rd.reference_id == '')
                {
                    var index = bbb_accounts[billIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.reference.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val(bbb_accounts[billIndex].ref_list[index].value); 
                        $inp.trigger(downKeyEvent); 
                        $inp.trigger(downKeyEvent);
                        $inp.trigger(enterKeyEvent);
                    }
                    else{
                        rd.reference = '';
                        rd.reference_id = '';
                        rd.due_date = '';
                    }
                }
            });
        }
        if(rd.method == 'New Ref.')
        {
            $inp.on("focusout", function () {
                var reference = $(this).val();
                if(reference)
                {
                    var index = bbb_accounts[billIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.reference = '';
                        
                    }
                }

            });
        }

        $inp.on("change", function (evt) {
            grid.refreshDataAndView();
        });
    }
                
    function calculateBillSummary() {
        var total = 0,
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.method != '' && row.reference != '' && row.amount != '' && row.drcr != '')
            {
                sub = 0;
                if(row.drcr == 'D'){
                    sub = parseAmount(row.amount);
                }
                if(row.drcr == 'C'){
                    sub = -parseAmount(row.amount);
                }
                total  += sub;
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        
        var totalData = {
            amount : '',
            narration : formatAmount(total) + ' (' + drcr + ')',
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }


                
    var methods = <?php echo json_encode($bills_method_list); ?>;
    var drcrlist     = [{"":""},{"C":"C"},{"D":"D"}];


    var bill_dataModel = {"data":generateBillData()} 
    var bill_colModel = [
        { title: "METHOD", dataIndx: "method", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: methods,
                init: methodEditor
            },
        },
        { title: "REFERENCE", width: 100, dataIndx: "reference" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                  type: "textbox",
                  init: referenceEditor
            },
            editable: function (ui) {
               var method = ui.rowData['method'];
                if (method != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "AMOUNT", width: 100,  dataIndx: "amount" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.amount != ''){
                    rd.amount = parseAmount(rd.amount);
                    return formatAmount(rd.amount);   
                }
                return '';
            },
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "Dr/Cr", dataIndx: "drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist
            },
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "DUE DATE", dataIndx: "due_date", width: 100 ,dataType: 'string',
            editor: {
                type: 'textbox',
                init: dateEditor
            },
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "NARRATION", width: 100, dataType: "string", dataIndx: "narration",
            editable: function (ui) {
               var reference = ui.rowData['reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        }
    ];

    var billsObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: bill_dataModel,
        colModel: bill_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculateBillSummary,
        dataReady: calculateBillSummary,
        editable: true,
        cellSave: function(evt, ui){
           this.refresh();
        },
        editModel: {
            clicksToEdit: 1,
                keyUpDown: false
            },
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
            var grid = this,
            $select_row = $(".select-row"),
            data = ui.dataModel.data;
            grid.setSelection({ rowIndx: 0, focus: true });
        }
    };
                
    billsObj.cellKeyDown = function(evt, ui) {
       var rowData = ui.rowData;
       var rowIndx = ui.rowIndx;

       if (evt.keyCode == 46){
            $.each(rowData, function(index,obj){
                rowData[index] = '';
            });
            this.refreshDataAndView();
            return false;
       }
    }        

    $("#billsModal").on('shown.bs.modal', function () {   
        if($("#bill_by_bill_grid").pqGrid('instance')){     
            $("#bill_by_bill_grid").pqGrid('refresh');
        }
        else
            $("#bill_by_bill_grid").pqGrid(billsObj);
    });


//------------------------------------------------------- Cost Centre  ccstart

    var saved_cc_txns = <?= json_encode($migrate_cc_data) ?>;
    if(saved_cc_txns.length > 0){
        $('#ccCheck').prop('checked', true);
    }

    $("#save_cc").on("click",function(){
        var status = true;
        status = validateCc();
        if(!status){
            return;
        }
        status = validateAllCcData();
        if(!status){
            return;
        }
        if(status){
            saveCcData();
            $('#ccModal').modal('hide');
            
            $("#ccdata").val(JSON.stringify(cc_data));
            show_loader();

            var oCheck = $('oCheck').prop('checked');
            var rCheck = $('rCheck').prop('checked');
            
            if(!rCheck && !oCheck && $('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
            else{
                $("#form1").submit();
            }
        }
    });

    function saveCcData()
    {
        $.each(cc_accounts, function(index,obj)
        {
            var account_id  = obj.account_id;
            var acc_type    = obj.acc_type;
            
            $.each(obj.grid, function(index2, obj2)
            {
                if(obj2.cc_id!='' && obj2.cc_name != '')
                { 
                    var cc_id       = obj2.cc_id;
                    var cc_name     = obj2.cc_name;
                    var cc_txn_amt  = obj2.cc_txn_amt;
                    var cc_txn_drcr = obj2.cc_txn_drcr;
                    var cc_txn_narr = obj2.cc_txn_narr;
                
                    cc_data.push({
                        "cc_id"       : cc_id,
                        "account_id"  : account_id,
                        "acc_type"    : acc_type,
                        "cc_name"     : cc_name,
                        "cc_txn_amt"  : parseAmount(cc_txn_amt),
                        "cc_txn_drcr" : cc_txn_drcr,
                        "cc_txn_narr" : cc_txn_narr
                    });
                }
            });      
            
        });   
    }

    function validateAllCcData()
    {
        var status = true;
        $.each(cc_accounts, function(index,obj)
        {
            var final_amount = obj.amount;
            var final_drcr   = obj.drcr;
            var final_total  = final_amount;
            
            if(final_drcr == 'C'){
                final_total = -final_total;
            }
            
            var total = 0;
            $.each(obj.grid, function(index2, obj2)
            {
                if(obj2.cc_name != '' && obj2.cc_id != '')
                {
                    var cc_txn_amt     = obj2.cc_txn_amt;
                    var cc_txn_drcr    = obj2.cc_txn_drcr;

                    var sub = 0;
                    if(cc_txn_drcr == 'D'){
                        sub = parseAmount(cc_txn_amt);
                    }
                    if(cc_txn_drcr == 'C'){
                        sub = -parseAmount(cc_txn_amt);
                    }
                    total += sub;
                    
                }
                
            });
            if(total != final_total){
                alert('Please fill all the grids ');
                status = false;
                return status;
            }
        });
        return status;
    }

    function validateCc()
    {
        var sum = 0;
        var data = $("#cc_grid").pqGrid('option', 'dataModel.data');
        var error = 0;
        var count = 0;
        
        for (var i = 0; i < data.length; i++) 
        {
            if(data[i]['cc_name'] != '' && data[i]['cc_id'] != '')
            {
                count++;
                var cc_name         = data[i]['cc_name'];
                var cc_id           = data[i]['cc_id'];
                var cc_txn_amt      = data[i]['cc_txn_amt'];
                var cc_txn_drcr     = data[i]['cc_txn_drcr'];;
                
                if(cc_txn_amt == '' || cc_txn_amt <= 0){
                    error = 1;
                }
                
                
                if(cc_txn_drcr == 'D'){
                    sum += parseAmount(cc_txn_amt);
                }
                if(cc_txn_drcr == 'C'){
                    sum += -parseAmount(cc_txn_amt);
                }
            }
        }

        if(error){
            alert("Kindly fill the details correctly !!!");
            return false;
        }
        var final_amount = cc_accounts[ccIndex].amount;; 
        var final_drcr = cc_accounts[ccIndex].drcr;
        
        if(final_drcr == 'C'){
            final_amount = -final_amount;
        }
        
        if(sum != final_amount && count > 0){
            alert('Total Mismatch');
            return false;
        }
        
        return true;
    }
       
    var cc_list = [];
    function readyCc()
    {
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/vouchers/getCc",
            data: {},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status)
                {
                    cc_list = response.data;
                    stop_loader();
                    readyCcModel();
                }
            }
        });          
    }

    var ccIndex = 0;
    function readyCcModel(step = '')
    {
        if(step == ''){
            ccIndex = 0;
        }
        else{
            var status = validateCc();
            if(!status){
                return;
            }
        }
        if(step == 'next'){
            if(ccIndex < (cc_accounts.length - 1)){
                ccIndex = ccIndex + 1;
            }
        }
        if(step == 'prev'){
            if(ccIndex > 0){
                ccIndex = ccIndex - 1;
            }
        }
        if(cc_accounts[ccIndex])
        {
            var account_name = cc_accounts[ccIndex].account_name;
            var amount = cc_accounts[ccIndex].amount;
            var drcr = cc_accounts[ccIndex].drcr;
            var page = (ccIndex + 1) + '/' + cc_accounts.length;
            
            $('#cc_account').text(account_name);
            $('#cc_total').html(formatAmount(amount));
            $('#cc_drcr').text(drcr + 'r');
            $('#cc_page').text(page);

            $('#ccModal').modal('show');
            
            $("#cc_grid").pqGrid('option', 'dataModel.data', cc_accounts[ccIndex].grid);
            $("#cc_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();//tempararily shift focus
            $("#cc_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 3, focus: true });
        }   
    }
                
                
    function generateCcData(acc_id = 0,acc_type = '')
  {
      var json = [];
      
      if(acc_id != 0 && acc_type != ''){
          var i = saved_cc_txns.findIndex(function(o) {
             return o.acc_id == acc_id && o.acc_type == acc_type;
          });
          if(i >= 0){
              $.each(saved_cc_txns[i].cc_txn_list, function(index, obj){
                  json.push({'cc_id': obj.cc_id, 'cc_name': obj.cc_name, 'cc_txn_amt': obj.cc_txn_amt, 'cc_txn_drcr': obj.cc_txn_drcr, 'cc_txn_narr': obj.cc_txn_narr});
              });
          }
      }
      
      for(var i=0;i<25;i++){
          json.push({'cc_id': '', 'cc_name': '', 'cc_txn_amt': '', 'cc_txn_drcr': '', 'cc_txn_narr': ''});
      }
      return json;
  }

    function ccEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;
            

            $inp.autocomplete({
                source: cc_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.cc_id = ui.item.id;
                    rd.cc_name = ui.item.label;

                    var expected = expected_cc_amount(grid);
                    rd.cc_txn_amt = expected.amount;
                    rd.cc_txn_drcr = expected.drcr;
                }

            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.cc_id = '';
                rd.cc_name = '';
                rd.cc_txn_amt = '';
                rd.cc_txn_drcr = '';
            }).focusout(function () {              
                if(rd.cc_name != '' && rd.cc_id == '')
                {
                    var index = cc_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.cc_name.toLowerCase();
                    });
                    if(index > -1){
                        rd.cc_id = cc_list[index].id;
                        rd.cc_name = cc_list[index].label;

                        var expected = expected_cc_amount(grid);
                        rd.cc_txn_amt = expected.amount;
                        rd.cc_txn_drcr = expected.drcr;
                    }
                    else{
                        rd.cc_id = '';
                        rd.cc_name = '';
                        rd.cc_txn_amt = '';
                        rd.cc_txn_drcr = '';
                        rd.cc_txn_narr = '';
                        
                    }
                }
                if(rd.cc_name == '' && rd.cc_id == '')
                {
                    rd.cc_txn_amt = '';
                    rd.cc_txn_drcr = '';
                    rd.cc_txn_narr = '';
                }

                grid.refreshDataAndView();
            });       
    }

    function expected_cc_amount(grid)
    {
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = cc_accounts[ccIndex].amount;
        var master_drcr = cc_accounts[ccIndex].drcr;

        if(master_drcr == 'D')
            amount = parseAmount(master_amount);
        if(master_drcr == 'C')
            amount = -parseAmount(master_amount);

        data.forEach(function(row){
            if(row.cc_txn_amt != '' && row.cc_txn_drcr != ''){
                if(row.cc_txn_drcr == 'C')
                    amount  += parseAmount(row.cc_txn_amt);
                if(row.cc_txn_drcr == 'D')
                    amount  -= parseAmount(row.cc_txn_amt);
            }
        });

        if(amount < 0){
            return {amount: Math.abs(amount), drcr: 'C'};
        }
        if(amount > 0){
            return {amount: amount, drcr: 'D'};
        }

        return {amount: '', drcr: ''};
    }
                
    function calculateCcSummary() {
        var total = 0,
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.cc_id != '' && row.cc_txn_amt != '' && row.cc_txn_drcr != '')
            {
                sub = 0;
                if(row.cc_txn_drcr == 'D'){
                    sub = parseAmount(row.cc_txn_amt);
                }
                if(row.cc_txn_drcr == 'C'){
                    sub = -parseAmount(row.cc_txn_amt);
                }
                total  += sub;
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        
        var totalData = {
            cc_txn_amt : '',
            cc_txn_narr : formatAmount(total) + ' (' + drcr + ')',
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }
                
    var drcrlist2     = [{"C":"C"},{"D":"D"}];


    var cc_dataModel = {"data":generateCcData()} 
    var cc_colModel = [
        
        { title: "Cost Centre", width: 100, dataIndx: "cc_name" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                  type: "textbox",
                  init: ccEditor
            },
        },
        { title: "AMOUNT", width: 100,  dataIndx: "cc_txn_amt" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.cc_txn_amt != ''){
                    rd.cc_txn_amt = parseAmount(rd.cc_txn_amt);
                    return formatAmount(rd.cc_txn_amt);   
                }
                return '';
            },
            editable: function (ui) {
               var cc_id = ui.rowData['cc_id'];
                if (cc_id != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "Dr/Cr", dataIndx: "cc_txn_drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist2
            },
            editable: function (ui) {
               var cc_id = ui.rowData['cc_id'];
                if (cc_id != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "NARRATION", width: 100, dataType: "string", dataIndx: "cc_txn_narr",
            editable: function (ui) {
               var cc_id = ui.rowData['cc_id'];
                if (cc_id != '') {
                    return true;
                }
                return false;
            },
        }
    ];

    var ccObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: cc_dataModel,
        colModel: cc_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculateCcSummary,
        dataReady: calculateCcSummary,
        editable: true,
        cellSave: function(evt, ui){
           this.refresh();
        },
        editModel: {
            clicksToEdit: 1,
                keyUpDown: false
            },
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
            var grid = this,
            $select_row = $(".select-row"),
            data = ui.dataModel.data;
            grid.setSelection({ rowIndx: 0, focus: true });
        }
    };
                
    ccObj.cellKeyDown = function(evt, ui) {
       var rowData = ui.rowData;
       var rowIndx = ui.rowIndx;

       if (evt.keyCode == 46){
            $.each(rowData, function(index,obj){
                rowData[index] = '';
            });
            this.refreshDataAndView();
            return false;
       }
    }         

    $("#ccModal").on('shown.bs.modal', function () {   
        if($("#cc_grid").pqGrid('instance')){       
            $("#cc_grid").pqGrid('refresh');
        }
        else
            $("#cc_grid").pqGrid(ccObj);
    });


//---------------------------------------------------- Project Reporting  prstart

    var saved_pr_txns = <?= json_encode($migrate_pr_data) ?>;
    if(saved_pr_txns.length > 0){
        $('#prCheck').prop('checked', true);
    }

    $("#save_pr").on("click",function(){
        var status = true;
        status = validatePr();
        if(!status){
            return;
        }
        status = validateAllPrData();
        if(!status){
            return;
        }
        if(status){
            savePrData();
            $('#prModal').modal('hide');
            
            $("#prdata").val(JSON.stringify(pr_data));
            show_loader();
            $("#form1").submit();
        }
    });

    function savePrData()
    {
        $.each(pr_accounts, function(index,obj)
        {
            var acc_id = obj.account_id;
            var acc_type = obj.acc_type;
            
            $.each(obj.grid, function(index2, obj2)
            {
                if(obj2.project_id!='' && obj2.cc_name != '')
                { 
                    var project_id        = obj2.project_id;
                    var project_name      = obj2.project_name;
                    var proj_txn_amt       = obj2.proj_txn_amt;
                    var proj_txn_drcr      = obj2.proj_txn_drcr;
                    var proj_txn_narr      = obj2.proj_txn_narr;
                
                    pr_data.push({
                        "project_id"        : project_id,
                        "acc_id"            : acc_id,
                        "acc_type"          : acc_type,
                        "project_name"      : project_name,
                        "proj_txn_amt"      : parseAmount(proj_txn_amt),
                        "proj_txn_drcr"     : proj_txn_drcr,
                        "proj_txn_narr"     : proj_txn_narr
                    });
                }
            });      
            
        });   
    }

    function validateAllPrData()
    {
        var status = true;
        $.each(pr_accounts, function(index,obj)
        {
            var final_amount = obj.amount;
            var final_drcr   = obj.drcr;
            var final_total  = final_amount;
            
            if(final_drcr == 'C'){
                final_total = -final_total;
            }
            
            var total = 0;
            $.each(obj.grid, function(index2, obj2)
            {
                if(obj2.project_name != '' && obj2.project_id != '')
                {
                    var proj_txn_amt     = obj2.proj_txn_amt;
                    var proj_txn_drcr    = obj2.proj_txn_drcr;

                    var sub = 0;
                    if(proj_txn_drcr == 'D'){
                        sub = parseAmount(proj_txn_amt);
                    }
                    if(proj_txn_drcr == 'C'){
                        sub = -parseAmount(proj_txn_amt);
                    }
                    total += sub;   
                }
            });
            if(total != final_total){
                alert('Please fill all the grids ');
                status = false;
                return status;
            }
        });
        return status;
    }

    function validatePr()
    {
        var sum = 0;
        var data = $("#pr_grid").pqGrid('option', 'dataModel.data');
        var error = 0;
        var count = 0;
        
        for (var i = 0; i < data.length; i++) 
        {
            if(data[i]['project_name'] != '' && data[i]['project_id'] != '')
            {
                count++;
                var project_name    = data[i]['project_name'];
                var project_id      = data[i]['project_id'];
                var proj_txn_amt      = data[i]['proj_txn_amt'];
                var proj_txn_drcr     = data[i]['proj_txn_drcr'];;
                
                if(proj_txn_amt == '' || proj_txn_amt <= 0){
                    error = 1;
                }
                
                
                if(proj_txn_drcr == 'D'){
                    sum += parseAmount(proj_txn_amt);
                }
                if(proj_txn_drcr == 'C'){
                    sum += -parseAmount(proj_txn_amt);
                }
            }
        }

        if(error){
            alert("Kindly fill the details correctly !!!");
            return false;
        }
        var final_amount = pr_accounts[prIndex].amount;; 
        var final_drcr = pr_accounts[prIndex].drcr;
        
        if(final_drcr == 'C'){
            final_amount = -final_amount;
        }
        
        if(sum != final_amount && count > 0){
            alert('Total Mismatch');
            return false;
        }
        
        return true;
    }
       
    var pr_list = [];
    function readyPr()
    {
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/ajax/getPr",
            data: {},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status)
                {
                    pr_list = response.data;
                    stop_loader();
                    readyPrModel();
                }
            }
        });          
    }

    var prIndex = 0;
    function readyPrModel(step = '')
    {
        if(step == ''){
            prIndex = 0;
        }
        else{
            var status = validatePr();
            if(!status){
                return;
            }
        }
        if(step == 'next'){
            if(prIndex < (pr_accounts.length - 1)){
                prIndex = prIndex + 1;
            }
        }
        if(step == 'prev'){
            if(prIndex > 0){
                prIndex = prIndex - 1;
            }
        }
        if(pr_accounts[prIndex])
        {
            var account_name = pr_accounts[prIndex].account_name;
            var amount = pr_accounts[prIndex].amount;
            var drcr = pr_accounts[prIndex].drcr;
            var page = (prIndex + 1) + '/' + pr_accounts.length;
            
            $('#pr_account').text(account_name);
            $('#pr_total').html(formatAmount(amount));
            $('#pr_drcr').text(drcr + 'r');
            $('#pr_page').text(page);

            $('#prModal').modal('show');
            
            $("#pr_grid").pqGrid('option', 'dataModel.data', pr_accounts[prIndex].grid);
            $("#pr_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();//tempararily shift focus
            $("#pr_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 3, focus: true });
        }   
    }
                
                
    function generatePrData(account_id = 0)
    {
        var json = [];
        
        if(account_id != 0){
            var i = saved_pr_txns.findIndex(function(o) {
               return o.acc_id == account_id;
            });
            if(i >= 0){
                $.each(saved_pr_txns[i].pr_txn_list, function(index, obj){
                    json.push({'project_id': obj.project_id, 'project_name': obj.project_name, 'proj_txn_amt': obj.proj_txn_amt, 'proj_txn_drcr': obj.proj_txn_drcr, 'proj_txn_narr': obj.proj_txn_narr});
                });
            }
        }
        
        for(var i=0;i<25;i++){
            json.push({'project_id': '', 'project_name': '', 'proj_txn_amt': '', 'proj_txn_drcr': '', 'proj_txn_narr': ''});
        }
        return json;
    }

    function prEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;
            

            $inp.autocomplete({
                source: pr_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.project_id = ui.item.id;
                    rd.project_name = ui.item.label;

                    var expected = expected_pr_amount(grid);
                    rd.proj_txn_amt = expected.amount;
                    rd.proj_txn_drcr = expected.drcr;
                }

            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.project_id = '';
                rd.project_name = '';
                rd.pr_txn_amt = '';
                rd.pr_txn_drcr = '';
            }).focusout(function () {              
                if(rd.project_name != '' && rd.project_id == '')
                {
                    var index = pr_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.project_name.toLowerCase();
                    });
                    if(index > -1){
                        rd.project_id = pr_list[index].id;
                        rd.project_name = pr_list[index].label;

                        var expected = expected_pr_amount(grid);
                        rd.pr_txn_amt = expected.amount;
                        rd.pr_txn_drcr = expected.drcr;
                    }
                    else{
                        rd.project_id = '';
                        rd.project_name = '';
                        rd.proj_txn_amt = '';
                        rd.proj_txn_drcr = '';
                        rd.proj_txn_narr = '';
                        
                    }
                }
                if(rd.project_name == '' && rd.project_id == '')
                {
                    rd.proj_txn_amt = '';
                    rd.proj_txn_drcr = '';
                    rd.proj_txn_narr = '';
                }

                grid.refreshDataAndView();
            });       
    }

    function expected_pr_amount(grid)
    {
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = pr_accounts[prIndex].amount;
        var master_drcr = pr_accounts[prIndex].drcr;

        if(master_drcr == 'D')
            amount = parseAmount(master_amount);
        if(master_drcr == 'C')
            amount = -parseAmount(master_amount);

        data.forEach(function(row){
            if(row.proj_txn_amt != '' && row.proj_txn_drcr != ''){
                if(row.proj_txn_drcr == 'C')
                    amount  += parseAmount(row.proj_txn_amt);
                if(row.proj_txn_drcr == 'D')
                    amount  -= parseAmount(row.proj_txn_amt);
            }
        });

        if(amount < 0){
            return {amount: Math.abs(amount), drcr: 'C'};
        }
        if(amount > 0){
            return {amount: amount, drcr: 'D'};
        }

        return {amount: '', drcr: ''};
    }
                
    function calculatePrSummary() {
        var total = 0,
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.project_id != '' && row.proj_txn_amt != '' && row.proj_txn_drcr != '')
            {
                sub = 0;
                if(row.proj_txn_drcr == 'D'){
                    sub = parseAmount(row.proj_txn_amt);
                }
                if(row.proj_txn_drcr == 'C'){
                    sub = -parseAmount(row.proj_txn_amt);
                }
                total  += sub;
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        
        var totalData = {
            proj_txn_amt : '',
            proj_txn_narr : formatAmount(total) + ' (' + drcr + ')',
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }
                


    var pr_dataModel = {"data":generatePrData()} 
    var pr_colModel = [
        
        { title: "Project", width: 100, dataIndx: "project_name" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                  type: "textbox",
                  init: prEditor
            },
        },
        { title: "AMOUNT", width: 100,  dataIndx: "proj_txn_amt" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.proj_txn_amt != ''){
                    rd.proj_txn_amt = parseAmount(rd.proj_txn_amt);
                    return formatAmount(rd.proj_txn_amt);   
                }
                return '';
            },
            editable: function (ui) {
               var project_id = ui.rowData['project_id'];
                if (project_id != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "Dr/Cr", dataIndx: "proj_txn_drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: [{"C":"C"},{"D":"D"}]
            },
            editable: function (ui) {
               var project_id = ui.rowData['project_id'];
                if (project_id != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "NARRATION", width: 100, dataType: "string", dataIndx: "proj_txn_narr",
            editable: function (ui) {
               var project_id = ui.rowData['project_id'];
                if (project_id != '') {
                    return true;
                }
                return false;
            },
        }
    ];

    var prObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: pr_dataModel,
        colModel: pr_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculatePrSummary,
        dataReady: calculatePrSummary,
        editable: true,
        cellSave: function(evt, ui){
           this.refresh();
        },
        editModel: {
            clicksToEdit: 1,
                keyUpDown: false
            },
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
            var grid = this,
            $select_row = $(".select-row"),
            data = ui.dataModel.data;
            grid.setSelection({ rowIndx: 0, focus: true });
        }
    };
                
    prObj.cellKeyDown = function(evt, ui) {
       var rowData = ui.rowData;
       var rowIndx = ui.rowIndx;

       if (evt.keyCode == 46){
            $.each(rowData, function(index,obj){
                rowData[index] = '';
            });
            this.refreshDataAndView();
            return false;
       }
    }         

    $("#prModal").on('shown.bs.modal', function () {   
        if($("#pr_grid").pqGrid('instance')){       
            $("#pr_grid").pqGrid('refresh');
        }
        else
            $("#pr_grid").pqGrid(prObj);
    });



</script>
</body>
</html>