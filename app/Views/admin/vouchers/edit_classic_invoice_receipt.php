<?php $header = array(  'title' => $voucher_name .' Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
<?php  $comp_vch_series_no ='0'; ?>
<?php
$local_session    = \Config\Services::session();
$fy_begndt        = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end           = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
$partyarray       = array();
 foreach ($party_dropdown as $value){
   $partyarray[$value['acc_id']]=array("grpid"=>$value['grpid'],"gstin"=>$value['gstin'],'name'=>$value['acc_name'],'is_bbb'=>$value['is_bbb']);
 }		
$params = http_build_query([
    'voucher_txn_id' => $voucher_txn_id,
    'voucher_date' => $voucher_date,  
    'voucher_name' => $voucher_name
]);
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
        <span class="material-symbols-outlined">filter_list</span></a>
      <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history">
        <span class="material-symbols-outlined">visibility</span></a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span></a>
      
       <a href="javascript:void(0);" onClick="print_page();"><span class="material-symbols-outlined">print</span> </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">   <span class="material-symbols-outlined open-comingsoon">download</span></a>
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
    
    <div class="form-check form-check-inline bbbccCheck bbbccoCheck">
      <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
      <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
    </div>
    <div class="form-check form-check-inline bbbccCheck bbbccoCheck">
      <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
      <label class="form-check-label" for="ccCheck">Cost Centre</label>
    </div>
	
	 <div class="form-check form-check-inline bbbccCheck bbbccoCheck">
      <input class="form-check-input" type="checkbox" value="1" id="sblgrCheck">
      <label class="form-check-label" for="sblgrCheck">Sub Ledger</label>
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
      <option <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?> data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>">
      <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-success">#</div>
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
	 <?php if(isset($_GET['duplc'])){}else{ ?>
	 <li>
        <a class="dropdown-item" href="<?php echo current_url();?>?duplc=1">&laquo; Duplicate</a>
		</li><?php } ?>
		
	<li><a class="dropdown-item" href="javascript:void(0);" id="replicavoucher" data-typeid="<?php echo $voucher_type_id;?>" data-type="<?php echo $voucher_name;?>" data-id="<?php echo $voucher_txn_id;?>">Replicate Voucher</a></li>	
      <li>
        <a class="dropdown-item" href="#">&laquo; Migrate</a>
        <ul class="dropdown-menu dropdown-submenu-left">
          <li>
            <a class="dropdown-item <?= ($voucher_type_id == 9) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/9?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Payment</a>
          </li>
          <li>
            <a class="dropdown-item <?= ($voucher_type_id == 13) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/13?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Receipt</a>
          </li>
          <li>
            <a class="dropdown-item <?= ($voucher_type_id == 1) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/1?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Contra</a>
          </li>
          <li>
            <a class="dropdown-item <?= ($voucher_type_id == 5) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/5?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Journal</a>
          </li>
        </ul>
      </li>
	  <?php if($allbos){?>
	  <li>
        <a class="dropdown-item" href="#">&laquo; Migrate BO</a>
        <ul class="dropdown-menu dropdown-submenu-left">
		 <?php foreach($allbos as $boid => $boname){?>
          <li>
            <a class="dropdown-item confirmbofirst" href="javascript:void(0);" data-ajax="<?php echo $voucher_txn_id;?>" data-name="<?php echo $boname;?>" data-id="<?php echo $boid;?>"><?php echo $boname;?></a>
          </li>
			 <?php } ?>          
        </ul>
      </li>
	  <?php } ?>
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
<form class="form" action="<?php echo base_url().$folder_path.'vouchers/edit/'.$voucher_type_id.'/'.$voucher_txn_id;?>" method="post" id="form1" autocomplete="off" novalidate>

<?php echo $message_output->run() ;?>

 <input type="hidden" name="voucherdata" id="voucherdata">
   <input type="hidden" name="btnid" id="btnid">
  <input type="hidden" name="bbbdata" id="bbbdata">
  <input type="hidden" name="ccdata" id="ccdata">
  <input type="hidden" name="prdata" id="prdata">
   <input type="hidden" name="sblgr_data" id="sblgr_data">
  <input type="hidden" name="vch_subtype_id" id="vch_subtype_id" value="<?php echo $vch_subtype_id;?>">
  <input type="hidden" name="voucher_type_id" id="voucher_type_id" value="<?php echo $voucher_type_id;?>">
  <input type="hidden" name="isdplc_vch" id="isdplc_vch" value="<?php echo $duplc;?>">
  <input type="hidden" name="draft_vch_rec_id" id="draft_vch_rec_id" value="<?php echo $draft_vch_rec_id ?? 0;?>">
 
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

  <?php $pay_acc = ($voucher_type_id === 9) ? 'Payment' : 'Receipt'; 
	  $creditAccounts = array_filter($account_transactions, function($account) {
		return isset($account['debit']) && floatval($account['debit']) > 0;
	});
	
	// Reindex the array (optional)
	$creditAccounts = array_values($creditAccounts);
	$debit_party_id='';
	$debit_party_txnid=99999;
	if(isset($creditAccounts[0])){
		$debit_party_id =  $creditAccounts[0]['account_id'];
		$debit_party_txnid =  $creditAccounts[0]['txn_id'];
	}
  
  
  if(isset($partyarray[$debit_party_id]['name']))
					 $party_name = $partyarray[$debit_party_id]['name'];
				 else
					$party_name = ''; 
  ?>



	
          <label class="input-group-text">Receipt via:</label>
		  
		  <input type="text" value="<?php echo $party_name;?>" list="party_ids" id="debit_acc" name="debit_acc" class="form-control form-control-sm required" onmouseover="focus();" required>
		  <datalist id="party_ids">
		    <?php foreach($data_groups as $value){ ?>
               <option  data-is_cash="<?= $value['is_cash']?>" data-dlrtype="<?php echo $value['dealer_type']?>" data-gstin="<?= $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['account_name'] ?>" <?php echo ($debit_party_id==$value['acc_id'])? 'selected':'';?>>
            <?php } ?>
		  </datalist>	
  		  
         
        
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
							<input type="text" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate" value="<?php echo $forexcrncy_rate;?>"  onkeydown="return validatefcrate(event,this.value);">
						  </div>
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
<?php if($draft_vch_rec_id>0){ ?>
<input type="button" id="fsubmitbtn" name="fsubmitbtn" value="FINALIZE"  class="btn btn-lg btn-success mx-2">
<?php } else{ ?>
<input type="button" id="submitbtn" name="submitbtn" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-lg btn-success mx-2">
<?php } ?>
<input type="button" id="submitbtn_drft" name="submitbtn_drft" value="SAVE AS DRAFT"   class="btn btn-lg btn-success mx-2">
<a href="javascript:main(0)" onclick="window.history.go(-1); return false;" class="btn btn-lg btn-secondary mx-2">QUIT</a>
    <a href="javascript:main(0)" class="deletebtn btn btn-lg btn-danger mx-2">Delete</a>
</div>
</form>

<!-- The Modal -->
<div class="modal" id="billsModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
     <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="bills_page">
      </span> Bill by Bill</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="bill_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close" data-bs-dismiss="modal">
  </div>
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
<div class="modal" id="sblgrModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="sblgr_page">
      </span> Sub Ledger</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="sblgr_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close" data-bs-dismiss="modal">
  </div>
</div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6 text-center">
          <h4>Account: <span id="sblgr_account">
          </span>
          </h4>
        </div>
        <div class="col-md-6 text-center">
          <h4>Total: <span id="sblgr_total">
          </span>&nbsp;<span id="sblgr_drcr">
        </span>
        </h4>
        </div>
      </div>
      <div id="sblgr_grid">
      </div>
      <button type="button" class="btn btn-primary prevBtn" onclick="readySblgrModel('prev')">Previous</button>
      <button type="button" class="btn btn-primary nextBtn" onclick="readySblgrModel('next')">Next</button>

      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_sblgr">Save</button>
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
   <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="pr_page">
      </span> Project Reporting</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="pr_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close close_pr_data" data-bs-dismiss="modal">
  </div>
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


$modified_data = [];

foreach ($account_transactions as $account) {
	if(!isset($account['creditfc']))
	  $account['creditfc']=0;
   if(!isset($account['debitfc']))
	  $account['debitfc']=0;
    // Move debit to credit if debit is > 0
	if(floatval($account['debit']) <= 0){
    if (floatval($account['credit']) > 0 || floatval($account['creditfc']) > 0) {
        $account['debit'] = parseAmount($account['credit']);
        $account['debitfc'] = parseAmount($account['creditfc']);
        $account['debit'] = 0;
        $account['debitfc'] = 0;
        $account['drcr'] = 'C';
    }

    // Keep only if final credit > 0
    if (floatval($account['credit']) > 0) {
        $modified_data[] = $account;
    }
	}
}

// Reindex the array (optional)
$json_data = array_values($modified_data);
$totaltxn  = 500-count($json_data);
for($i=1;$i<=$totaltxn;$i++){
	$json_data[] = array("srn"=>$i,"drcr"=>'','account_name'=>'','description'=>'','debit'=>'','credit'=>'','account_id'=>'','is_bbb'=>'','is_cc'=>'','is_cash'=>'','acc_type'=> '','igst_rate'=>'0','cess_rate'=>'0','item_hsn_sac'=>'','tax_cat_id'=>'0','supply_type'=>'','acc_short_code'=>'','cannotselected'=>'','cess_basis'=>'');
}

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
	if( fcy_forex_rate >0 ){			
	     var data = $('#vch_grid_search').pqGrid('option', 'dataModel.data');
console.log(data);		 
	     $.each(data, function(index,obj){
		if(obj.account_id != '' && obj.account_id != undefined){
			
			var debit_amount        = (obj.debitfc/fcy_forex_rate);
			
			console.log("debitfc amount--->"+obj.debitfc);
			console.log("fcy_forex_rate--->"+fcy_forex_rate);
			console.log("debit amount--->"+debit_amount);
			var credit_amount       = (obj.creditfc/fcy_forex_rate);
			if(parseAmount(debit_amount)!='' && parseAmount(debit_amount) > 0){
			data[index]['debit']    = debit_amount;
			}
		    if(parseAmount(credit_amount)!='' && parseAmount(credit_amount) >0){
			data[index]['credit']   = credit_amount;
			}
		  }
	  }); 
	  
	  console.log("----------------");	
	  console.log(data);	
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
       if(currency_symbol !='₹'){	
                $("#view_fcrates_modal #selcyrlabel").html(currency_symbol);				
				$("#view_fcrates_modal #fcy_voucher_no").html($("#voucher_no").val());
				$("#view_fcrates_modal #fcy_voucher_date").html($("#voucher_date").val());				
			    $("#view_fcrates_modal").modal('show');	
				 
				colModelVch[2].hidden=false;
                colModelVch[2].width= 150;
				colModelVch[2].title='AMOUNT('+currency_symbol+')'; 
				
				$("#vch_grid_search").pqGrid( "option", "colModel", colModelVch );             
                $("#vch_grid_search").pqGrid( {autoFit: true} );
                $("#vch_grid_search").pqGrid("refreshCM");
                $("#vch_grid_search").pqGrid("refresh");
			 }
			 else{
				$("#view_fcrates_modal").modal('hide');				
				colModelVch[2].hidden=true;
               
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
	var sblgr_data=[];
    var sblgr_accounts=[]; 
     function parseDate(str) {
    var parts = str.split("-");
    // Return a Date object in YYYY-MM-DD format
    return new Date(parts[2], parts[1] - 1, parts[0]);
}
             
    $("#submitbtn,#submitbtn_drft,#fsubmitbtn").on("click",function(){
        bbb_data = [];
		sblgr_data=[];
		sblgr_accounts  = [];
        bbb_accounts = [];
        cc_data = [];
        cc_accounts = [];

        item_checked    = [];
		var btn_id =  $(this).attr("id");

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

		let debit_acc = null;
		let debit_acc_is_cash = null;
		let credit_acc_is_bbb = null;

		if (typeof party === 'string') {
		  const $opt = $(`#party_ids option[value="${CSS.escape(party)}"]`);
		  if ($opt.length) {
			debit_acc = $opt.attr('id') || null;
			debit_acc_is_cash = $opt.data('is_cash') ?? null;
			credit_acc_is_bbb = $opt.data('is_bbb') ?? null;
		  }
		}

        
        var d1 = dateFrom.split("-");
        var d2 = dateTo.split("-");
        var c  = dateCheck.split("-");
        
        var from_year = d1[2];  // -1 because months are from 0 to 11
        var to_year   = d2[2];
        var check_year = c[2];//, parseInt(c[1])-1, c[0]);
        
        var fromDate = parseDate(dateFrom);
        var toDate   = parseDate(dateTo);
        var checkDate = parseDate(dateCheck);
        
        if (checkDate >= fromDate && checkDate <= toDate) {           
            var datevalidate = 1;
        } else {         
            var datevalidate = 0;           
        }
               
        var debitsum=0;
        var creditsum=0;
        var creditsum_fcy=0;
		 var debitsum_fcy=0;
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
			var txn_id           = data[i]['txn_id'];
			var is_sblgr         =1;
			
			if(typeof data[i]['creditfc'] == 'undefined'){
				var creditfc = 0;
			}else 
			 var creditfc =data[i]['creditfc'];	
			if(typeof data[i]['debitfc'] == 'undefined'){
				var debitfc = 0;
			}else
				var debitfc = data[i]['debitfc'];
            
            if(account_id != '' && account_name != ''){

                debitsum  += parseAmount(debit);
                creditsum += parseAmount(credit);
				creditsum_fcy += parseAmount(creditfc);
                
                var item_data = {
                    "drcr": drcr,
                    "account_id":  account_id,
					"account_name"  : account_name,
                    "acc_type":  acc_type,
                    "description" :description,
                    "debit" : parseAmount(0),
                    "credit" : parseAmount(credit),
					"creditfc" : parseAmount(creditfc),
					"debitfc" : parseAmount(debitsum_fcy),
                    "is_bbb" : is_bbb,
                    "is_cc" : is_cc,
					"is_cash" :is_cash,
					"is_sblgr":1,
					 "row_index": i,
                }
                
                if(is_bbb == 1)
                {   
                    var amount = 0;
					var amountfc = 0;
                    if(drcr == 'C'){
                        amount = credit;
						amountfc = creditfc
					}
                    if(drcr == 'D'){
                        amount = debit;
						amountfc = debitfc
						}
                    
                    if($('#bbbCheck').is(":checked"))
                    {
                        bbb_accounts.push({
							 "row_index": i,
                            "account_id":  account_id,
                            "account_name": account_name,
                            "drcr": drcr,
                            "amount": parseAmount(amount),
							"amountfc": parseAmount(amountfc),
                            "grid": generateBillData(account_id)
                        }); 
                    }
                    
                }
				
				if(is_sblgr == 1)
                {   
                    var amount = 0;
					var amountfc = 0;
                    if(drcr == 'C'){
                        amount = credit;
						amountfc = creditfc
					}
                    if(drcr == 'D'){
                        amount = debit;
						amountfc = debitfc
						}
                    
                    if($('#sblgrCheck').is(":checked"))
                    {
                        sblgr_accounts.push({
							 "row_index": i,
                            "account_id":  account_id,
                            "account_name": account_name,
                            "drcr": drcr,
                            "amount": parseAmount(amount),
							"amountfc": parseAmount(amountfc),
                            "grid": generateSblgrData(account_id,txn_id)
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
							 "row_index": i,
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
					var amountfc_pp=0;
                    if(drcr == 'C'){
                       amount = credit;
						amountfc_pp = creditfc;
					}
                    if(drcr == 'D'){
                        amount = debit;
						amountfc_pp=debitfc
						
					}

                    pr_accounts.push({
						 "row_index": i,
                        "account_id"    : account_id,
                        "account_name"  : account_name,
                        "drcr"          : drcr,
                        "acc_type"      : acc_type,
                        "amount"        : parseAmount(amount),
						"amountfc"      : parseAmount(amountfc_pp),
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
		
		// Receipt via:Dr side
		var long_narration = $("textarea[name=narration]").val();
		var craccount_name = $("input[name=credit_acc]").val();
		var is_sblgr =1;
		var item_dr_data = {
                    "drcr": 'D',
                    "account_id":  debit_acc,
					"account_name"  : craccount_name,
                    "acc_type":  'acc',
                    "description" :long_narration,
                    "debit" : parseAmount(creditsum),
                    "credit" : parseAmount(0),
					"creditfc" : parseAmount(0),
					"debitfc" : parseAmount(creditsum_fcy),
                   "is_bbb" : credit_acc_is_bbb,
                    "is_cc" : is_cc,
					"is_cash" :debit_acc_is_cash,
					 "row_index": <?php echo $debit_party_txnid ;?>,
                }
			item_checked.push(item_dr_data);
			
			if(is_sblgr == 1)
                {   
                  var  amount = parseAmount(creditsum);
					var amountfc=parseAmount(creditsum_fcy);
					
                    if($('#sblgrCheck').is(":checked"))
                    {
                        sblgr_accounts.push({
                            "account_id":  debit_acc,
                            "account_name": craccount_name,
                            "drcr": 'D',
                            "amount": parseAmount(amount),
							"amountfc": parseAmount(amountfc),
                            "grid": generateSblgrData(debit_acc),
							 "row_index": <?php echo $debit_party_txnid ;?>,
                        }); 
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
            error_msg +="<tr><td class='tdtext-left'>Voucher can't be saved beyond the financial year period..</td></tr>";
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
        /*
         if(item_checked.length >0 && error_contra_bsd=="1"){
            error_msg +="<tr><td class='tdtext-left'>Bill Sundry Accounts are not allowed .</td></tr>";
            errros=errros+1;
        }*/
		
		if($("#debit_acc").val()!='' && parseAmount(creditsum)==0){
             error_msg +="<tr><td class='tdtext-left'>voucher totals can not be zero.</td></tr>";
             errros=errros+1;   
         }
		
		 var fcrates         =  parseAmount($("#view_fcrates_modal #fcy_forex_rate").val());
		let currency_symbol =  $("#currency_id").find("option:selected").data("id"); // get selected symbol 
		
		 if(currency_symbol != "₹" && fcrates==''){
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
			$("#btnid").val(btn_id);
			
            if(!rCheck && !oCheck && $('#bbbCheck').is(":checked") && bbb_accounts.length > 0){ // bbbstart
                readyBills();
            }
			else if(!rCheck && !oCheck && $('#sblgrCheck').is(":checked") && sblgr_accounts.length > 0){ // sub ledger start
                readySblgr();
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

$(document).on("click",".deletebtn",function(){
      <?php if($draft_vch_rec_id>0){ ?> 
     confirm_delete(baseurl+"/admin/vouchers/delete_draft/<?php echo $voucher_txn_id;?>");        
	 <?php } else { ?>
	  confirm_delete(baseurl+"/admin/vouchers/delete/<?php echo $voucher_type_id;?>/<?php echo $voucher_txn_id;?>");
	 <?php } ?>        
     return false
  })
  
$(document).on('submit', '#form1', function(e){
        e.preventDefault();
        var form     = $(this);
        var formData = new FormData(this);
		if (formData.get('btnid') == 'submitbtn_drft' && formData.get('draft_vch_rec_id') != 0)
		   var editfrm_url = '<?php echo base_url().$folder_path.'vouchers/draft_edit/'.$voucher_txn_id;?>';
		else if (formData.get('btnid') == 'fsubmitbtn')
		   var editfrm_url = '<?php echo base_url().$folder_path.'vouchers/finalize_draft/'.$voucher_txn_id;?>';
			
		else
		   var editfrm_url = '<?php echo base_url().$folder_path.'vouchers/edit/'.$voucher_type_id.'/'.$voucher_txn_id;?>';	
        $.ajax({
            url: editfrm_url, 
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
                    //window.location.href='<?php echo base_url();?>/admin/vouchers/invoice/<?php echo $voucher_type_id;?>'; 
					window.location.href='<?php echo history_back();?>';
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
            
            /* $.ajax({
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
            }); */

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
             
			    if(typeof row.creditfc == 'undefined'){
                    var creditfc =0;
                }else
                var  creditfc =  row.creditfc;

                creditTotal += parseAmount(credit); 

                creditfcTotal += parseAmount(creditfc); 	
            }
        });
		
        var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
        var totalData = {
                account_name        : "Total",
				description: "",
               // creditfc    : formatAmount(creditfcTotal,currency_symbol),
				credit      : creditTotal,
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
                    /*
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
                    */
                    else
                    {
                        $(this).val(ui.item.label);
                        rd.account_id = ui.item.acc_id;
                        rd.is_bbb = ui.item.is_bbb;
                        rd.is_cc = ui.item.is_cc;
                        rd.is_cash = ui.item.is_cash;
						rd.drcr ='C';

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
                    {   return rd.account_name;
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
			{
                title: "AMOUNT",
                sortable: false,
                width: 100,
                align: "left",
                dataIndx: "creditfc",
				hidden:true,
                dataType: "float",
                validations: [{ type: 'gt', value: 0, msg: "should be > 0" }],
                editable: true, // always editable
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
                				    if(parseAmount(fcy_forex_rate) > 0){
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
                    
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        if(rd.creditfc >0){
                            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					    return formatAmount(parseAmount(rd.creditfc),currency_symbol);   
                        }
                        return '';
                    
                }
                },		
              {
                title: "AMOUNT(₹)",
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
	   $(document).on("change","#currency_id",function(){		
			let currency_symbol = $(this).find("option:selected").data("id"); // get selected symbol
				
			if (currency_symbol == "₹") {
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
			
			   calculate_fc_rates($(this).val(),colModelVch,currency_symbol);			  
			 
			});
		  
		 var currencysymbol = $('select[name="currency_id"] option:selected').attr('data-id');
		 var currency_idval = $('select[name="currency_id"] option:selected').val();
		 if (currencysymbol != "₹"){
		        colModelVch[2].hidden=false;
                colModelVch[2].width= 150;
				colModelVch[2].title='AMOUNT('+currencysymbol+')';
				
				$("#vch_grid_search").pqGrid( "option", "colModel", colModelVch );
				$("#vch_grid_search").pqGrid( {autoFit: true} );
				$("#vch_grid_search").pqGrid("refreshCM");
				$("#vch_grid_search").pqGrid("refresh");
		  }
		

		  //calculate_fc_rates(currency_idval,billsundry_colModel,colModel,currencysymbol,'1');
		  // reload_fc_rates(currency_idval);
		   
		   
		   
     });
	 
	 

//------------------------------------------------------------------------------ Bill By Bill  bbbstart

var saved_bill_txns = <?= json_encode($bbb_data) ?>;
  var hasBills = Object.keys(saved_bill_txns).some(function(key) {
  if (key !== "acc_id" && saved_bill_txns[key].bills_txn_list?.length > 0) {
    return true;
  }
  return false;
});
  if(hasBills){
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
					var amountfc   = obj2.amountfc;
                    var drcr       = obj2.drcr;
                    var due_date   = obj2.due_date;
                    var narration  = obj2.narration;

                    bbb_data.push({
                        "account_id": account_id,
                        "method"    : method,
                        "reference" : reference,
                        "reference_id" : reference_id,
                        "amount"    : parseAmount(amount),
						"amountfc"  : parseAmount(amountfc),
                        "drcr"      : drcr,
                        "due_date"  : due_date,
                        "narration" : narration
                    });
                }
            });
            
        });
    }

    function validateAllBillsData() {
    var isAnyGridValid = false;

    for (var i = 0; i < bbb_accounts.length; i++) {
        var obj = bbb_accounts[i];
        var final_amount = parseAmount(obj.amount);
        var final_drcr = obj.drcr;
        var final_total = (final_drcr === 'C') ? -final_amount : final_amount;

        var total = 0;
        var hasValidRow = false;

        for (var j = 0; j < obj.grid.length; j++) {
            var obj2 = obj.grid[j];
            var method = obj2.method;
            var reference = obj2.reference;
            var amount = obj2.amount;
            var drcr = obj2.drcr;

            if (method !== '' && reference !== '') {
                var parsedAmount = parseAmount(amount);
                var sub = (drcr === 'D') ? parsedAmount : -parsedAmount;
                total += sub;
                hasValidRow = true;
            }
        }

        if (hasValidRow) {
            if (parseFloat(total.toFixed(2)) !== parseFloat(final_total.toFixed(2))) {
                $('#bill_warning').text('Bill total mismatch for a filled grid.');
                return false;
            }

            isAnyGridValid = true;
            break; // valid one found and checked, stop further looping
        }
    }

    if (!isAnyGridValid) {
        $('#bill_warning').text('Please fill at least one bill grid.');
        return false;
    }

    return true;
}

    var billIndex = 0;

    function readyBills()
    {
        var account_id_array = bbb_accounts.map(function(obj) { return obj.account_id; });
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>vouchers/getAccountBillRefs",
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
            $('#bill_warning').text("Kindly fill the details correctly !!!"); 
            return false;
        }
        var final_amount = bbb_accounts[billIndex].amount;; 
        var final_drcr = bbb_accounts[billIndex].drcr;
        
        if(final_drcr == 'C'){
            final_amount = -final_amount;
        }
        
        if(sum != parseAmount(final_amount) && count > 0){
           $('#bill_warning').text("Total Mismatch");
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
			var amountfc = bbb_accounts[billIndex].amountfc;
            var drcr = bbb_accounts[billIndex].drcr;
            var page = (billIndex + 1) + '/' + bbb_accounts.length;
            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
            
            $('#bills_account').text(account_name);
            if (currency_symbol != "₹")
              $('#bills_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	       else 
		      $('#bills_total').html(formatAmount(amount));
            $('#bills_drcr').text(drcr + 'r');
            $('#bills_page').text(page);
            

            $('#billsModal').modal('show');
            if (currency_symbol != "₹"){
	             bill_colModel[2].hidden=false;
                bill_colModel[2].width= 150;
				bill_colModel[2].title='AMOUNT('+currency_symbol+')';
			    $("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
                $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
                $("#bill_by_bill_grid").pqGrid("refreshCM");
                $("#bill_by_bill_grid").pqGrid("refresh");				
			  }
			  else{
				 bill_colModel[2].hidden=true;
				 bill_colModel[2].width= 150;
				 bill_colModel[2].title='AMOUNT('+currency_symbol+')';  							
				 $("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
				 $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
				 $("#bill_by_bill_grid").pqGrid("refreshCM");
				 $("#bill_by_bill_grid").pqGrid("refresh");
			   } 
            $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', bbb_accounts[billIndex].grid);
            $("#bill_by_bill_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();//tempararily shift focus
            $("#bill_by_bill_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
        }
        
    }

                
    function generateBillData(account_id = 0)
  {
      var json = [];	  	       
      if(account_id>0){		
		 var i = saved_bill_txns.findIndex(function(o) {
             return o.acc_id == account_id;
          });
		  if(i >= 0){
		   $.each(saved_bill_txns[i].bills_txn_list, function(index, obj){
                  json.push({'method': 'Adjustment', 'reference': obj.bill_ref_name, 'reference_id': obj.bill_ref_id, 'amountfc': obj.bill_txn_fcy,'amount': obj.bill_txn_amt, 'drcr': obj.bill_txn_dr_cr,
                              'due_date': obj.bill_due_date, 'narration': obj.bill_txn_narr});
              });    
		  }			  
      }      
      for(var i=0;i<25;i++){
          json.push({'method': '', 'reference': '', 'reference_id': '','amountfc': '', 'amount': '', 'drcr': '', 'due_date': '', 'narration': ''});
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
			amountfcTotal=0,
			subfc=0,
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.method != '' && row.reference != '' && row.amount != '' && row.drcr != '')
            {
                sub = 0;
				subfc=0;
                if(row.drcr == 'D'){
                    sub = parseAmount(row.amount);
					subfc = parseAmount(row.amountfc);
                }
                if(row.drcr == 'C'){
                    sub = -parseAmount(row.amount);
					subfc = parseAmount(row.amountfc);
                }
                total  += sub;
				amountfcTotal +=subfc
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
      
        var totalData = {
			amountfc  : parseAmount(amountfcTotal),
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
		{ title: "AMOUNT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "amountfc",dataType: "float",
                editable: function (ui) {
				 var reference = ui.rowData['reference'];
				  if (reference != '') {
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
                                if(rd.amountfc != '' && parseAmount(rd.amountfc) >= 0)
                			    {
                				    if(parseAmount(fcy_forex_rate) > 0){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.amount   = parseAmount((rd.amountfc)/fcy_forex_rate);	
                				     else
                				     	rd.amount   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.amountfc = 0;
                					}	
                                }else
									rd.amountfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					if(rd.amountfc != ''){
					return formatAmount(parseAmount(rd.amountfc),currency_symbol);
					}
                }
				
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

var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
	
if(currency_symbol!='₹'){
	            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
	            bill_colModel[2].hidden=false;
                bill_colModel[2].width= 150;
				bill_colModel[2].title='AMOUNT('+currency_symbol+')';
				if($("#bill_by_bill_grid").pqGrid('instance')){   
			    $("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
                $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
                $("#bill_by_bill_grid").pqGrid("refreshCM");
                $("#bill_by_bill_grid").pqGrid("refresh");
				}
				
  }
  else{
	 bill_colModel[2].hidden=true;
                bill_colModel[2].width= 150;
				bill_colModel[2].title='AMOUNT('+currency_symbol+')';  
				if($("#bill_by_bill_grid").pqGrid('instance')){ 
				$("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
                $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
                $("#bill_by_bill_grid").pqGrid("refreshCM");
                $("#bill_by_bill_grid").pqGrid("refresh");
				}
  }


//------------------------------------------------------  Sub Ledger Start 

    var saved_sblgr_txns = <?= json_encode($sblgr_data) ?>;
    var hassblgrs = Object.keys(saved_sblgr_txns).some(function(key) {
  if (key !== "acc_id" && saved_sblgr_txns[key].sblgr_txn_list?.length > 0) {
    return true;
  }
  return false;
});
    if(hassblgrs){
        $('#sblgrCheck').prop('checked', true);
    }

    $("#save_sblgr").on("click",function(){
        var status = true;
        status = validateSblgr();
        if(!status){
            return;
        }
        status = validateAllSblgrData();
        if(!status){
            return;
        }
        if(status){
            saveSblgrData();
            $('#sblgrModal').modal('hide');
            $("#sblgr_data").val(JSON.stringify(sblgr_data));
            show_loader();

            var oCheck = $('oCheck').prop('checked');
            var rCheck = $('rCheck').prop('checked');

            if(!rCheck && !oCheck && $('#ccCheck').is(":checked") && cc_accounts.length > 0){ //ccstart
                readyCc();
            }
			else if(!rCheck && !oCheck && $('#bbbCheck').is(":checked") && bbb_accounts.length > 0){ //bbbstart
                readyBills();
            }
            else if(!rCheck && !oCheck && $('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
            else{
                $("#form1").submit();
            }
        }
    });

    function saveSblgrData()
    {
        $.each(sblgr_accounts, function(index,obj)
        {
            var account_id = obj.account_id;
             var rowindex_id = obj.row_index;
            $.each(obj.grid, function(index2, obj2)
            {
                if(obj2.sblgr_method != '' && obj2.sblgr_reference != '')
                {
                    var method     = obj2.sblgr_method;
                    var reference  = obj2.sblgr_reference;
                    var reference_id  = obj2.sblgr_reference_id;
                    var amount     = obj2.sblgr_amount;
					var amountfc   = obj2.sblgr_amountfc;
                    var drcr       = obj2.sblgr_drcr;
                    var due_date   = obj2.sblgr_due_date;
                    var narration  = obj2.sblgr_narration;

                    sblgr_data.push({
						"row_index": rowindex_id,
                        "account_id": account_id,
                        "method"    : method,
                        "reference" : reference,
                        "reference_id" : reference_id,
                        "amount"    : parseAmount(amount),
						"amountfc"  : parseAmount(amountfc),
                        "drcr"      : drcr,
                        "due_date"  : due_date,
                        "narration" : narration
                    });
                }
            });
            
        });
    }

   function validateAllSblgrData() {

    var forex_rate = parseAmount($("#fcy_forex_rate").val());

    for (var i = 0; i < sblgr_accounts.length; i++) {

        var obj = sblgr_accounts[i];

        var final_amount     = parseAmount(obj.amount);
        var final_amount_fc  = parseAmount(obj.amountfc || 0);
        var final_drcr       = obj.drcr;

        var final_total      = final_amount;     // receipt style
        var final_total_fc   = final_amount_fc;

        var total = 0;
        var total_fc = 0;

        var hasValidRow = false;

        for (var j = 0; j < obj.grid.length; j++) {

            var obj2      = obj.grid[j];
            var method    = obj2.sblgr_method;
            var reference = obj2.sblgr_reference;

            var amount    = parseAmount(obj2.sblgr_amount);
            var amount_fc = parseAmount(obj2.sblgr_amountfc);
            var drcr      = obj2.sblgr_drcr;

            // 🔴 better condition
            if (method !== '') {

                if (reference == '' || drcr == '') {
                    $('#sblgr_warning').text('Invalid sub ledger row.');
                    return false;
                }

                if (amount <= 0 && amount_fc <= 0) {
                    $('#sblgr_warning').text('Invalid amount in sub ledger.');
                    return false;
                }

                // FC → Base conversion
                if (amount <= 0 && amount_fc > 0) {
                    if (forex_rate > 0) {
                        amount = parseAmount(amount_fc / forex_rate);
                    }
                }

                // ✅ receipt → always positive aggregation
                total += amount;
                total_fc += amount_fc;

                hasValidRow = true;
            }
        }

        // 🔴 validate EACH account (no break)
        if (final_amount > 0) {

            if (!hasValidRow) {
                $('#sblgr_warning').text('Please fill sub ledger for all required accounts.');
                return false;
            }

            // ✅ STRICT BASE VALIDATION (1 paisa)
            if (Math.round(total * 100) !== Math.round(final_total * 100)) {
                $('#sblgr_warning').text('Sub ledger base total mismatch.');
                return false;
            }

            // ✅ STRICT FC VALIDATION
            if (final_total_fc != 0) {
                if (Math.round(total_fc * 100) !== Math.round(final_total_fc * 100)) {
                    $('#sblgr_warning').text('Sub ledger FC total mismatch.');
                    return false;
                }
            }
        }
    }

    return true;
}

    var sblgrIndex = 0;

    function readySblgr()
{
    var account_id_array = sblgr_accounts.map(obj => obj.account_id);

    $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>vouchers/getAccountSblgrRefs",
        data: {account_id_array: account_id_array},
        datatype: "json",

        success: function(response){

            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
            } catch(e){
                console.error("Invalid JSON response", response);
                return;
            }

            if(response.status)
            {
                // 🔴 SAFE MAP (by account_id)
                var refMap = {};

                $.each(response.data, function(i, obj){
                    refMap[obj.account_id] = obj.ref_list || [];
                });

                // Assign safely
                $.each(sblgr_accounts, function(i, acc){
                    acc.ref_list = refMap[acc.account_id] || [];
                });

                stop_loader();
                readySblgrModel();
            }
            else{
                console.warn("API returned false status");
            }
        },

        error: function(xhr){
            console.error("Sblgr AJAX failed", xhr);
        }
    });
}

    function validateSblgr()
{
    var sum = 0;
    var sum_fc = 0;

    var data = $("#sblgr_grid").pqGrid('option', 'dataModel.data');
    var error = 0;
    var count = 0;

    var forex_rate = parseAmount($("#fcy_forex_rate").val());

    for (var i = 0; i < data.length; i++) {

        var method        = data[i]['sblgr_method'];
        var reference     = data[i]['sblgr_reference'];
        var reference_id  = data[i]['sblgr_reference_id'];
        var amount        = parseAmount(data[i]['sblgr_amount']);
        var amount_fc     = parseAmount(data[i]['sblgr_amountfc']);
        var drcr          = data[i]['sblgr_drcr'];

        // 🔴 better condition
        if(method != '')
        {
            // basic validation
            if(reference == '' || drcr == ''){
                error = 1;
            }

            // allow base OR FC
            if(amount <= 0 && amount_fc <= 0){
                error = 1;
            }

            count++;

            // FC → Base conversion
            if(amount <= 0 && amount_fc > 0){
                if(forex_rate > 0){
                    amount = parseAmount(amount_fc / forex_rate);
                }
            }

            var sub = 0;
            var sub_fc = 0;

            if(drcr == 'D'){
                sub = amount;
                sub_fc = amount_fc;
            }
            if(drcr == 'C'){
                sub = -amount;
                sub_fc = -amount_fc;
            }

            sum += sub;
            sum_fc += sub_fc;
        }
    }

    if(error){
        $('#sblgr_warning').text("Kindly fill the details correctly !!!");
        return false;
    }

    // final expected values
    var final_amount     = parseAmount(sblgr_accounts[sblgrIndex].amount);
    var final_amount_fc  = parseAmount(sblgr_accounts[sblgrIndex].amountfc || 0);
    var final_drcr       = sblgr_accounts[sblgrIndex].drcr;

    if(final_drcr == 'C'){
        final_amount = -final_amount;
        final_amount_fc = -final_amount_fc;
    }

    // ✅ STRICT BASE VALIDATION (1 paisa)
    if(count > 0 && Math.round(sum * 100) !== Math.round(final_amount * 100)){
        $('#sblgr_warning').text("Base Amount Mismatch");
        return false;
    }

    // ✅ STRICT FC VALIDATION
    if(final_amount_fc != 0){
        if(Math.round(sum_fc * 100) !== Math.round(final_amount_fc * 100)){
            $('#sblgr_warning').text("FC Amount Mismatch");
            return false;
        }
    }

    return true;
}

    function readySblgrModel(step = '')
    {
        if(step == ''){
            sblgrIndex = 0;
        }
        else{
            var status = validateSblgr();
            if(!status){
                return;
            }
        }
        if(step == 'next'){
            if(sblgrIndex < (sblgr_accounts.length - 1)){
                sblgrIndex = sblgrIndex + 1;
            }
        }
        if(step == 'prev'){
            if(sblgrIndex > 0){
                sblgrIndex = sblgrIndex - 1;
            }
        }
        if(sblgr_accounts[sblgrIndex])
        {   var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
	      
            var account_name = sblgr_accounts[sblgrIndex].account_name;
            var amount = sblgr_accounts[sblgrIndex].amount;
			var amountfc = sblgr_accounts[sblgrIndex].amountfc;
            var drcr = sblgr_accounts[sblgrIndex].drcr;
            var page = (sblgrIndex + 1) + '/' + sblgr_accounts.length;
            
            $('#sblgr_account').text(account_name);
          if (currency_symbol != "₹") 
          $('#sblgr_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	      else 
		 $('#sblgr_total').html(formatAmount(amount));
	     
            $('#sblgr_drcr').text(drcr + 'r');
            $('#sblgr_page').text(page);
            

            $('#sblgrModal').modal('show');
			if (currency_symbol != "₹") {
	            sblgr_colModel[2].hidden=false;
                sblgr_colModel[2].width= 150;
				sblgr_colModel[2].title='AMOUNT('+currency_symbol+')';
			    $("#sblgr_grid").pqGrid( "option", "colModel", sblgr_colModel );             
                $("#sblgr_grid").pqGrid( {autoFit: true} );
                $("#sblgr_grid").pqGrid("refreshCM");
                $("#sblgr_grid").pqGrid("refresh");
				
			  }
			  else{
				sblgr_colModel[2].hidden=true;
				sblgr_colModel[2].width= 150;
				sblgr_colModel[2].title='AMOUNT('+currency_symbol+')'; 				
				$("#sblgr_grid").pqGrid( "option", "colModel", sblgr_colModel );             
				$("#sblgr_grid").pqGrid( {autoFit: true} );
				$("#sblgr_grid").pqGrid("refreshCM");
				$("#sblgr_grid").pqGrid("refresh");
			  }
          
			$("#sblgr_grid").pqGrid('option', 'dataModel.data', sblgr_accounts[sblgrIndex].grid);
			$("#sblgr_grid").pqGrid('refreshDataAndView');            
            $("#sblgr_grid").pqGrid('option', 'dataModel.data', sblgr_accounts[sblgrIndex].grid);
            $("#sblgr_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();//tempararily shift focus
            $("#sblgr_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
        }
    }
                
    function generateSblgrData(account_id, txn_id)
{
    var json = [];

    if(account_id){

        var i = saved_sblgr_txns.findIndex(function(o) {
            return String(o.acc_id) === String(account_id);
        });

        if(i >= 0){

            var sblgrs = saved_sblgr_txns[i].sblgr_txn_list;

            // ✅ PRIMARY MATCH → txn_id
            var matched = sblgrs.filter(function(obj){
                return obj.txn_id == txn_id;
            });

            // ✅ PUSH MATCHED
            if(matched.length > 0){

                $.each(matched, function(index, obj){
                    json.push({
                        'sblgr_method': 'Adjustment',
                        'sblgr_reference': obj.sub_acc_name,
                        'sblgr_reference_id': obj.sub_acc_id,
                        'sblgr_amountfc': obj.sub_acc_txn_fcy,
                        'sblgr_amount': parseFloat(obj.sub_acc_txn_amt) || 0,
                        'sblgr_drcr': obj.sub_acc_txn_dr_cr,
                        'sblgr_due_date': obj.sub_acc_txn_date,
                        'sblgr_narration': obj.sub_acc_txn_narr
                    });
                });
            }
        }
    }

    // empty rows
    for(var j=0;j<500;j++){
        json.push({
            'sblgr_method': '',
            'sblgr_reference': '',
            'sblgr_reference_id': '',
            'sblgr_amountfc': '',
            'sblgr_amount': '',
            'sblgr_drcr': '',
            'sblgr_due_date': '',
            'sblgr_narration': ''
        });
    }

    return json;
}
                
    function sblgr_dateEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,                
            grid = this;

        $inp.on("focusout", function (e) {
            var date = rd.sblgr_due_date;
            
            /* if(!isValidDate(date)){
              rd.due_date = '';
              e.preventDefault();
            } */
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
                
    function sblgr_methodEditor(ui) {
        var $inp = ui.$cell.find("select"),
            di = ui.dataIndx,
            rd = ui.rowData,               
            grid = this;
        
        $inp.on("change", function (evt) {
            var method = $(this).val();
            
            rd.sblgr_reference = '';
            rd.sblgr_reference_id = '';
            rd.sblgr_amount = '';
            rd.sblgr_drcr = '';
            rd.sblgr_due_date = '';
            rd.sblgr_narration = '';

            if(method != ''){
                var expected = expected_sblgr_amount(grid);
                rd.sblgr_amount = expected.sblgr_amount;
                rd.sblgr_drcr = expected.sblgr_drcr;
            }
        })
    };

    function expected_sblgr_amount(grid)
    {
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = sblgr_accounts[sblgrIndex].sblgr_amount; // 1000
        var master_drcr = sblgr_accounts[sblgrIndex].sblgr_drcr; // D

        if(master_drcr == 'D')
            amount = parseAmount(master_amount);
        if(master_drcr == 'C')
            amount = -parseAmount(master_amount);

        data.forEach(function(row){
            if(row.sblgr_amount != '' && row.sblgr_drcr != ''){
                if(row.sblgr_drcr == 'C')
                    amount  += parseAmount(row.sblgr_amount);
                if(row.sblgr_drcr == 'D')
                    amount  -= parseAmount(row.sblgr_amount);
            }
        });

        if(amount < 0){
            return {sblgr_amount: Math.abs(amount), sblgr_drcr: 'C'};
        }
        if(amount > 0){
            return {sblgr_amount: amount, sblgr_drcr: 'D'};
        }

        return {sblgr_amount: '', sblgr_drcr: ''};
    }
                
    function sblgr_referenceEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;
            
        if(rd.sblgr_method == 'Adjustment')
        {
            $inp.autocomplete({
                source:  sblgr_accounts[sblgrIndex].ref_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.sblgr_reference_id = ui.item.id;
                    rd.sblgr_reference = ui.item.label;
                    rd.sblgr_due_date = ui.item.due_date;
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.sblgr_reference = '';
                rd.sblgr_reference_id = '';
                rd.sblgr_due_date = '';
            }).focusout(function () {              
                if(rd.sblgr_reference != '' && rd.sblgr_reference_id == '')
                {
                    var index = sblgr_accounts[sblgrIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.reference.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val(sblgr_accounts[sblgrIndex].ref_list[index].value); 
                        $inp.trigger(downKeyEvent); 
                        $inp.trigger(downKeyEvent);
                        $inp.trigger(enterKeyEvent);
                    }
                    else{
                        rd.sblgr_reference = '';
                        rd.sblgr_reference_id = '';
                        rd.sblgr_due_date = '';
                    }
                }
            });
        }
        if(rd.sblgr_method == 'New Ref.')
        {
            $inp.on("focusout", function () {
                var reference = $(this).val();
                if(reference)
                {
                    var index = sblgr_accounts[sblgrIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.sblgr_reference = '';
                        
                    }
                }

            });
        }

        $inp.on("change", function (evt) {
            grid.refreshDataAndView();
        });
    }
                
    function calculateSblgrSummary() {
        var total = 0,
		    amountfcTotal=0,
            sub = 0,
			subfc=0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.sblgr_method != '' && row.sblgr_reference != '' && row.sblgr_amount != '' && row.sblgr_drcr != '')
            {
                sub = 0;
				subfc=0;
                if(row.sblgr_drcr == 'D'){
                    sub = parseAmount(row.sblgr_amount);
					subfc = parseAmount(row.sblgr_amountfc);
                }
                if(row.sblgr_drcr == 'C'){
                    sub = -parseAmount(row.sblgr_amount);
					subfc = parseAmount(row.sblgr_amountfc);
                }
                total  += sub;
				amountfcTotal +=subfc
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
     
        var totalData = {
			sblgr_amountfc  : parseAmount(amountfcTotal),
            sblgr_amount : '',
            sblgr_narration : formatAmount(total) + ' (' + drcr + ')',
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }
               
    var methods = <?php echo json_encode($bills_method_list); ?>;
    var drcrlist     = [{"":""},{"C":"C"},{"D":"D"}];


    var sblgr_dataModel = {"data":generateSblgrData()} 
    var sblgr_colModel = [
        { title: "METHOD", dataIndx: "sblgr_method", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: methods,
                init: sblgr_methodEditor
            },
        },
        { title: "REFERENCE", width: 100, dataIndx: "sblgr_reference" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                  type: "textbox",
                  init: sblgr_referenceEditor
            },
            editable: function (ui) {
               var method = ui.rowData['sblgr_method'];
                if (method != '') {
                    return true;
                }
                return false;
            },
        },
		{ title: "AMOUNT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "sblgr_amountfc",dataType: "float",
                editable: function (ui) {
				 var reference = ui.rowData['sblgr_reference'];
				  if (reference != '') {
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
                                if(rd.sblgr_amountfc != '' && parseAmount(rd.sblgr_amountfc) >= 0)
                			    {
                				    if(parseAmount(fcy_forex_rate) > 0){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.sblgr_amount   = parseAmount((rd.sblgr_amountfc)/fcy_forex_rate);	
                				     else
                				     	rd.sblgr_amount   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.sblgr_amountfc = 0;
                					}	
                                }else
									rd.sblgr_amountfc = 0;
									grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					if(rd.sblgr_amountfc != ''){
					return formatAmount(parseAmount(rd.sblgr_amountfc),currency_symbol);
					}
                }
				
              },
        { title: "AMOUNT", width: 100,  dataIndx: "sblgr_amount" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.sblgr_amount != ''){
                    rd.sblgr_amount = parseAmount(rd.sblgr_amount);
                    return formatAmount(rd.sblgr_amount);   
                }
                return '';
            },
            editable: function (ui) {
               var reference = ui.rowData['sblgr_reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "Dr/Cr", dataIndx: "sblgr_drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist
            },
            editable: function (ui) {
               var reference = ui.rowData['sblgr_reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "DUE DATE", dataIndx: "sblgr_due_date", width: 100 ,dataType: 'string',
            editor: {
                type: 'textbox',
                init: sblgr_dateEditor
            },
            editable: function (ui) {
               var reference = ui.rowData['sblgr_reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        },
        { title: "NARRATION", width: 100, dataType: "string", dataIndx: "sblgr_narration",
            editable: function (ui) {
               var reference = ui.rowData['sblgr_reference'];
                if (reference != '') {
                    return true;
                }
                return false;
            },
        }
    ];

    var sblgrObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, 
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: sblgr_dataModel,
        colModel: sblgr_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculateSblgrSummary,
        dataReady: calculateSblgrSummary,
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
                
    sblgrObj.cellKeyDown = function(evt, ui) {
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

    $("#sblgrModal").on('shown.bs.modal', function () {   
        if($("#sblgr_grid").pqGrid('instance')){     
            $("#sblgr_grid").pqGrid('refresh');
        }
        else
            $("#sblgr_grid").pqGrid(sblgrObj);
    });
	var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
	      
		if (currency_symbol != "₹") {
			sblgr_colModel[2].hidden=false;
			sblgr_colModel[2].width= 150;
			sblgr_colModel[2].title='AMOUNT('+currency_symbol+')';
			if($("#sblgr_grid").pqGrid('instance')){   
			 $("#sblgr_grid").pqGrid( "option", "colModel", sblgr_colModel );             
			 $("#sblgr_grid").pqGrid( {autoFit: true} );
			 $("#sblgr_grid").pqGrid("refreshCM");
			 $("#sblgr_grid").pqGrid("refresh");
			}
						
		  }
		  else{
			sblgr_colModel[2].hidden=true;
			sblgr_colModel[2].width= 150;
			sblgr_colModel[2].title='AMOUNT('+currency_symbol+')';  
			if($("#sblgr_grid").pqGrid('instance')){ 
			 $("#sblgr_grid").pqGrid( "option", "colModel", sblgr_colModel );             
			 $("#sblgr_grid").pqGrid( {autoFit: true} );
			 $("#sblgr_grid").pqGrid("refreshCM");
			 $("#sblgr_grid").pqGrid("refresh");
			}
		  }


//------------------------------------------------------- Cost Centre  ccstart

    var saved_cc_txns = <?= json_encode($cc_data) ?>;
    var hasCCs = Object.keys(saved_cc_txns).some(function(key) {
  if (key !== "acc_id" && saved_cc_txns[key].cc_txn_list?.length > 0) {
    return true;
  }
  return false;
});
  if(hasCCs){
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
			 var rowindex_id = obj.row_index;
            
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
						"row_index"   : rowindex_id,
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

    function validateAllCcData() {
    var isAnyGridValid = false;

    for (var i = 0; i < cc_accounts.length; i++) {
        var obj = cc_accounts[i];
        var final_amount = parseAmount(obj.amount);
        var final_drcr = obj.drcr;
        var final_total = (final_drcr === 'C') ? -final_amount : final_amount;

        var total = 0;
        var hasValidRow = false;

        for (var j = 0; j < obj.grid.length; j++) {
            var obj2 = obj.grid[j];
            if (obj2.cc_name !== '' && obj2.cc_id !== '') {
                var amt = parseAmount(obj2.cc_txn_amt);
                var drcr = obj2.cc_txn_drcr;

                var sub = (drcr === 'D') ? amt : -amt;
                total += sub;
                hasValidRow = true;
            }
        }

        if (hasValidRow) {
            if (parseFloat(total.toFixed(2)) !== parseFloat(final_total.toFixed(2))) {
                $('#cc_warning').text('Cost centre total mismatch for a filled grid.');
                return false;
            }

            isAnyGridValid = true;
            break; // validation passed for one filled grid, exit loop
        }
    }

    if (!isAnyGridValid) {
        $('#cc_warning').text('Please fill at least one cost centre grid.');
        return false;
    }

    return true;
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

    var saved_pr_txns = <?= json_encode($pr_data) ?>;	
	var hasPrs = Object.keys(saved_pr_txns).some(function(key) {
  if (key !== "acc_id" && saved_pr_txns[key].pr_txn_list?.length > 0) {
    return true;
  }
  return false;
});

    if(hasPrs){
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
                if(obj2.project_id!='' && obj2.project_name != '')
                { 
                    var project_id        = obj2.project_id;
                    var project_name      = obj2.project_name;
                    var proj_txn_amt       = obj2.proj_txn_amt;
					var proj_txn_amtfc    = obj2.proj_txn_amtfc;
                    var proj_txn_drcr      = obj2.proj_txn_drcr;
                    var proj_txn_narr      = obj2.proj_txn_narr;
                
                    pr_data.push({
                        "project_id"        : project_id,
                        "acc_id"            : acc_id,
                        "acc_type"          : acc_type,
                        "project_name"      : project_name,
                        "proj_txn_amt"      : parseAmount(proj_txn_amt),
						"proj_txn_amtfc"    : parseAmount(proj_txn_amtfc),
                        "proj_txn_drcr"     : proj_txn_drcr,
                        "proj_txn_narr"     : proj_txn_narr
                    });
                }
            });      
            
        });   
    }

    function validateAllPrData() {
    var isAnyGridValid = false;

    for (var i = 0; i < pr_accounts.length; i++) {
        var obj = pr_accounts[i];
        var final_amount = obj.amount;
        var final_drcr   = obj.drcr;
        var final_total  = (final_drcr === 'C') ? -final_amount : final_amount;

        var total = 0;
        var hasValidRow = false;

        for (var j = 0; j < obj.grid.length; j++) {
            var obj2 = obj.grid[j];
            if (obj2.project_name !== '' && obj2.project_id !== '') {
                var proj_txn_amt = parseAmount(obj2.proj_txn_amt);
                var proj_txn_drcr = obj2.proj_txn_drcr;
                var sub = (proj_txn_drcr === 'D') ? proj_txn_amt : -proj_txn_amt;
                total += sub;
                hasValidRow = true;
            }
        }

        if (hasValidRow) {
            // One valid grid found, now check if total matches
            if (parseFloat(total.toFixed(2)) !== parseFloat(final_total.toFixed(2))) {
               $('#pr_warning').text('Project total mismatch for a filled grid.');
                return false;
            }

            isAnyGridValid = true; // success condition met
            break; // no need to check further
        }
    }

    if (!isAnyGridValid) {
        $('#pr_warning').text('Please fill at least one project grid.');
        return false;
    }

    return true;
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
            $('#pr_warning').text("Kindly fill the details correctly !!!");
            return false;
        }
        var final_amount = pr_accounts[prIndex].amount;; 
        var final_drcr = pr_accounts[prIndex].drcr;
        
        if(final_drcr == 'C'){
            final_amount = -final_amount;
        }
        
        if(sum != final_amount && count > 0){
            $('#pr_warning').text('Total Mismatch');
            return false;
        }
        
        return true;
    }
       
    var pr_list = [];
    function readyPr()
    {
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>vouchers/getPr",
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
        {   var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
	      
            var account_name = pr_accounts[prIndex].account_name;
            var amount = pr_accounts[prIndex].amount;
			var amountfc     = pr_accounts[prIndex].amountfc;
            var drcr = pr_accounts[prIndex].drcr;
            var page = (prIndex + 1) + '/' + pr_accounts.length;
            
            $('#pr_account').text(account_name);
            if (currency_symbol != "₹") 
            $('#pr_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	      else 
		   $('#pr_total').html(formatAmount(amount));
            $('#pr_drcr').text(drcr + 'r');
            $('#pr_page').text(page);

            $('#prModal').modal('show');
			if (currency_symbol != "₹") {
			pr_colModel[1].hidden=false;
			pr_colModel[1].width= 150;
			pr_colModel[1].title='AMOUNT('+currency_symbol+')';
			$("#pr_grid").pqGrid( "option", "colModel", pr_colModel );             
			$("#pr_grid").pqGrid( {autoFit: true} );
			$("#pr_grid").pqGrid("refreshCM");
			$("#pr_grid").pqGrid("refresh");				
		    }
			else{
			 pr_colModel[1].hidden=true;
			 pr_colModel[1].width= 150;
			 pr_colModel[1].title='AMOUNT('+currency_symbol+')';  
			 $("#pr_grid").pqGrid( "option", "colModel", pr_colModel );             
			 $("#pr_grid").pqGrid( {autoFit: true} );
			 $("#pr_grid").pqGrid("refreshCM");
			 $("#pr_grid").pqGrid("refresh");
			}    
            
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
                    json.push({'project_id': obj.project_id, 'project_name': obj.project_name, 'proj_txn_amtfc': obj.project_txn_fcy,'proj_txn_amt': obj.proj_txn_amt, 'proj_txn_drcr': obj.project_txn_dr_cr, 'proj_txn_narr': obj.project_txn_narr});
                });
            }
        }
        
        for(var i=0;i<25;i++){
            json.push({'project_id': '', 'project_name': '', 'proj_txn_amtfc': '','proj_txn_amt': '', 'proj_txn_drcr': '', 'proj_txn_narr': ''});
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
			amountfcTotal=0,
			subfc=0,
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.project_id != '' && row.proj_txn_amt != '' && row.proj_txn_drcr != '')
            {
                sub = 0;
				subfc=0;
                if(row.proj_txn_drcr == 'D'){
                    sub = parseAmount(row.proj_txn_amt);
					subfc = parseAmount(row.proj_txn_amtfc);
                }
                if(row.proj_txn_drcr == 'C'){
                    sub = -parseAmount(row.proj_txn_amt);
					subfc = parseAmount(row.proj_txn_amtfc);
                }
                total  += sub;
				amountfcTotal +=subfc;
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        
        var totalData = {
			proj_txn_amtfc  : parseAmount(amountfcTotal),
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
		{ title: "AMOUNT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "proj_txn_amtfc",dataType: "float",
                editable: function (ui) {
				 var project_id = ui.rowData['project_id'];
				  if (project_id != '') {
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
                                if(rd.proj_txn_amtfc != '' && parseAmount(rd.proj_txn_amtfc) >= 0)
                			    {
                				    if(parseAmount(fcy_forex_rate) > 0){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.proj_txn_amt   = parseAmount((rd.proj_txn_amtfc)/fcy_forex_rate);	
                				     else
                				     	rd.proj_txn_amt   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.proj_txn_amtfc = 0;
                					}	
                                }else
									rd.proj_txn_amtfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					if(rd.proj_txn_amtfc >0 && rd.proj_txn_amtfc !=''){
					return formatAmount(parseAmount(rd.proj_txn_amtfc),currency_symbol);
					}
                }
				
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
	
      var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
				
	  if (currency_symbol != "₹"){
			pr_colModel[1].hidden=false;
			pr_colModel[1].width= 150;
			pr_colModel[1].title='AMOUNT('+currency_symbol+')';
			if($("#pr_grid").pqGrid('instance')){   
			$("#pr_grid").pqGrid( "option", "colModel", pr_colModel );             
			$("#pr_grid").pqGrid( {autoFit: true} );
			$("#pr_grid").pqGrid("refreshCM");
			$("#pr_grid").pqGrid("refresh");
			}
					
	  }
	  else{
			pr_colModel[1].hidden=true;
			pr_colModel[1].width= 150;
			pr_colModel[1].title='AMOUNT('+currency_symbol+')';  
			if($("#pr_grid").pqGrid('instance')){ 
			$("#pr_grid").pqGrid( "option", "colModel", pr_colModel );             
			$("#pr_grid").pqGrid( {autoFit: true} );
			$("#pr_grid").pqGrid("refreshCM");
			$("#pr_grid").pqGrid("refresh");
			}
	  }

function print_page() {	
    window.open(
        '<?php echo base_url(); ?>admin/VoucherPrint/<?php echo $voucher_txn_id;?>?p=1',
        '_blank'
    ).focus();
}
</script>
</body>
</html>