<?php $header = array( 	'title' => 'Update Purchase Invoice' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));

$vch_fcy_rate ='';
if($vchfcyrate_info){
	$vch_fcy_rate	 = $vchfcyrate_info['vch_fcy_rate'];
}
if($gstrinwsup_info){
$revrchrgs = $gstrinwsup_info['inwsup_rev_chg'];
$billno    = $gstrinwsup_info['inwsup_bill_ref_no'];
$inwsup_pos = $gstrinwsup_info['inwsup_pos'];
$inwsup_inv_type = 0;//$gstrinwsup_info['inv_type_id'];
}
else{
$revrchrgs=0;
$inwsup_pos='';	
$billno ='';
$inwsup_inv_type='';	
}
 $billsundry_json_data = $bsd_transactions;
for($i=1;$i<=50;$i++)
    $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_rate'=>'','billsundry_amount'=>'','billsundry_amountfc'=>'');

$item_json_data = $item_transactions;
for($i=1;$i<=500;$i++){ 
  $item_json_data[] = array("item_id"=>'',"item_name"=>'','item_qty'=>'','description'=>'','item_unit_name'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');
 }
 
 $selected_account=[];
 foreach ($party_dropdown as $value) {
    if ($value['acc_id'] == $voucher_info['party_id']) {
        $selected_account = $value;
     
    }
}
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
</style>
<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
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
<script>
var forexcrncy_rate_sel = '<?php echo $forexcrncy_rate;?>';
var voucher_txn_id  ='<?php echo $voucher_txn_id;?>';
<?php if($ismemosale >0){ ?>
var is_memo_enabled=true;
<?php } else{ ?>
var is_memo_enabled=false;
<?php } ?>
<?php if($istaxinc >0){ ?>
var is_taxinc_enabled=true;
<?php } else{ ?>
var is_taxinc_enabled=false;
<?php } ?>
var default_pos    = '<?php echo $inwsup_pos;?>';
var vchtyp_id = '<?php echo $voucher_type_id;?>';
</script>
<div class="row pb-2">
    <div class="col-sm-6 order-1"><h3>Update Purchase Invoice</h3></div>  
  <div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
     <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
	<a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>    
    <a href="#"><span class="material-symbols-outlined">offline_bolt</span></a> 

     <a href="javascript:void(0);" onclick="print_page()"><span class="material-symbols-outlined">print</span></a>
     <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
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
  <a href="javascript:void(0);" onclick="window.history.back();" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-8 order-2 order-md-3">
   
   <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
        <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
     </div>
	   <div class="form-check d-inline-block me-2">
		  <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="itmbtchCheck">
		  <label class="form-check-label" for="itmbtchCheck">Item Batch</label>
		</div>
     <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
        <label class="form-check-label" for="ccCheck">Cost Centre</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" value="1" id="prCheck">
      <label class="form-check-label" for="prCheck">Project Reporting</label>
    </div>
	<?php
		if($istaxinc=="1") 
			 $checkedlabel_tax =' checked="true"';
		 else
		$checkedlabel_tax =" ";	 
			
		?>
<div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="taxInclusive" id="taxInclusive"<?php echo $checkedlabel_tax;?>>
      <label class="form-check-label" for="taxInclusive">Tax Inclusive</label>
    </div>
	
	<?php
		if($ismemosale=="1") 
			 $checkedlabel =' checked="true"';
		 else
		$checkedlabel =" ";	 
			
		?>
	<div class="form-check form-check-inline">
    <input class="form-check-input" form="salefrm" type="checkbox" value="1" name="memoCheck" id="memoCheck"<?php echo $checkedlabel;?>>
        <label class="form-check-label" for="memoCheck">Memorandum</label>
    </div>
	
	
    <div class="col-md-3 col-6">
          <div class="input-group">
            <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:120px;">
           <?php foreach ($currency_list as $value) { ?>
                <option <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?> data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>"><?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
           <?php } ?>
       </select>
            <div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-success">#</div>
          </div>
        </div>

		
</div>  
  <div class="col-md-4 text-md-end order-4 collapse listmenu" id="listmenu">
      <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
      <li> <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);" data-type="itm">Apply Tax</a></li>
    </ul>
	<a href="#" class="btn btn-success">Party Dashboard</a>
    <button class="btn btn-success m-1" type="button">Templates</button>
        <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-success showinline-md">Back</a>
  </div> 
</div>

<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
        <div class="row m-0 p-0 align-items-center">

        <div class="row">
		 
            <div class="col-md-3 col-6 card p-2">
                <div class="input-group">
                 <span class="input-group-text">Purchase Type:</span>
                 <input type="text" value="Purchase With Stock Inward" class="form-control form-control-sm" readonly>
                </div>
				 <input type="hidden" name="stored_supply_type_id" id="stored_supply_type_id" value="<?php echo $OverrideSupplyTypeId ?? 0;?>">
           </div>  
         <div class="col-md-3 col-6 card p-2" id="shsupply_type" style="display:none;">
          <div class="input-group">
            <span class="input-group-text">Supply Type:</span>           
            <?php echo form_dropdown('sub_supply_type', [], '',' id="sub_supply_type" class="sale_type form-select required" required '); ?>           
            
          </div>
        </div>		   
           
        </div>
	
		<div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Bill No:</label>
         <?php if($billno!=''){ ?>
          <input type="text" name="billno" id="billno" value="<?php echo $billno;?>" class="form-control form-control-sm" readonly>
		  <?php } else { ?>
		  <input type="text" name="billno" id="billno" class="form-control form-control-sm">		  
		  <?php } ?>
        </div>
      </div>
	  <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Reverse Charges:</label>
         
          <?php 
          $revrchrgs_lists=array("0"=>"No","1"=>"Yes","2"=>"ECO");
          echo form_dropdown('reverse_charges', $revrchrgs_lists, $revrchrgs,' id="reverse_charges" class="sale_type form-select" '); ?>
         
        </div>
      </div>
        <div class="col-md-2 col-6 card p-2">
            <div class="input-group">
                <span class="input-group-text">Series:</span>
               
		  <select name="voucher_series" id="voucher_series" class="voucher_series form-select required" required>
			<?php	
				 $sel_sersval = arrayfrstval($voucher_series);
			foreach($voucher_series as $vch_series_info){ ?>
               <option <?php ($vch_series_info['vch_series_id']==$voucher_series)? 'selected':'';?> id="<?php echo $vch_series_info['vch_series_id']; ?>" data-srsmethod="<?php echo $vch_series_info['vch_series_method']; ?>"  value="<?php echo $vch_series_info['vch_series_id']; ?>"><?= $vch_series_info['vch_series_name'] ?></option>
            <?php } ?>
		  </select>

            </div>
        </div>
        <div class="col-md-2 col-6 card p-2">
           <div class="input-group">
               <label class="input-group-text">Date:</label> 
               <input type="text" name="voucher_date" id="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm">
            </div>
        </div>  
       
   
		
		<div class="col-md-3 col-6 card p-2">
            <div class="input-group">
			    <label class="input-group-text">Party:</label> 
                <input type="text" list="party_ids" id="clone_party_id" name="clone_party_id" class="form-control form-control-sm required" onmouseover="focus();" value="<?php echo $voucher_info['party_name'];?>" required>
		  <datalist id="party_ids">
		    <?php foreach($party_dropdown as $value){ ?>
               <option data-is_sez="<?php echo $value['is_sez']?>" <?php echo ($voucher_info['party_id']==$value['acc_id'])?'selected':'';?> data-grpid="<?php echo $value['grpid']?>" data-gstin="<?php echo $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_name'] ?>" >
            <?php } ?>
		  </datalist>
            </div>
        </div>
		
        <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">POS:</label>
          <?php echo form_dropdown('pos', $states_lists,$inwsup_pos,' id="pos" class="sale_type form-select" '); ?>
        </div>
      </div>
        
        <div class="col-md-2 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">MC:</label>
                <?php
		   $sel_sersval_mc = arrayfrstval($matrcntr_dropdown);
		  echo form_dropdown('matrcntr_id', $matrcntr_dropdown, $voucher_info['mat_cent_id'],' id="matrcntr_id" class="form-select required" '); ?>
            </div>
        </div>
		
	  <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Ecomerce Operator:</label>
          <?php echo form_dropdown('eco_id', $eco_dropdown, "",' id="eco_id" class="form-select" '); ?>
        </div>
      </div>
	  
	  <div class="col-md-3 col-6 card p-2" id="party_gstin_label" style="display:none;">
      <table class="table-sm">
        <tbody>
          <tr>
            <td>Party GSTIN</td>
            <td class="party_gstin_name"></td>
          </tr>         
        </tbody>
      </table>
    </div>
        <div class="col-md-12 col-12 card p-2">
            <div class="input-group">
              <label class="input-group-text">Narration:</label> 
              <textarea  name="narration"  class="form-control form-control-sm"><?php echo $narration;?></textarea>
            </div>
        </div>
        <input type="hidden" name="itmsdata" id="itmsdata">
        <input type="hidden" name="bbbdata" id="bbbdata">
        <input type="hidden" name="ccdata" id="ccdata">
        <input type="hidden" name="prdata" id="prdata">
		<input type="hidden" name="btnid" id="btnid">
		 <input type="hidden" name="billsndrydata" id="billsndrydata">
		 <input type="hidden" name="itemsbatchdata" id="itemsbatchdata">	
		<input type="hidden" name="purchase_type" value="<?php echo $vch_subtype_id;?>">
		<input type="hidden" name="draft_vch_rec_id" id="draft_vch_rec_id" value="<?php echo $draft_vch_rec_id ?? 0;?>">
 
		<select style="display:none;" name="party_id" id="party_id" class="form-select">
		<?php if ($selected_account): ?>
        <option 
            value="<?php echo $selected_account['acc_id']; ?>"  
            data-grpid="<?php echo $selected_account['grpid']; ?>" 
            data-gstin="<?php echo $selected_account['gstin']; ?>" 
            data-is_bbb="<?php echo $selected_account['is_bbb']; ?>" 
            selected>
            <?php echo htmlspecialchars($selected_account['acc_name']); ?>
        </option>
    <?php else: ?>
        <option value="" data-grpid="" data-gstin="" data-is_bbb="0">Select Account</option>
    <?php endif; ?>
</select>
        </div>
    </div>
   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
  <div class="col-12"><div class="row">
        <!-- Tax Summary -->
        <div class="col-md-8">
           <h4 class="text-center">Bill Sundry</h4>
           <div id="billsundry_search" style="margin:auto;"></div> 
         
        </div>
       <!-- Tax Summary Ends -->
      
       <!-- Bill Summary Starts --> 
       <div class="col-md-4">
          
           <div id="calsummary" style="margin:auto;">
		     <div class="row">
		     <div class="col-md-6"><br></div>
			 <div class="col-md-6"><br> </div>
		   </div>
		   <div class="row mb-3">
		     <div class="col-md-5" id="servicelabel"><label>Value Of Service</label></div>
			 <div class="col-md-7" id="servoicevalue"></div>
		   </div>
		  <div class="row mb-3">
		     <div class="col-md-5" id="billsundrylabel"><label>Bill Sundry</label></div>
			 <div class="col-md-7" id="billsundryvalue"></div>
		   </div>
		 <div class="row mb-3">
		     <div class="col-md-5" id="totalsupplylabel"><label>Total Supply Value</label></div>
			 <div class="col-md-7" id="totalsupplyvalue"></div>
		   </div>
		   <div class="row mb-3">
			<div id="taxContainer"></div>
		  </div>  		  
		 <div class="row mb-3">
		     <div class="col-md-5"><label>Total Invoice Value</label></div>
			 <div class="col-md-7" id="invoice_total"></div>
		   </div>	
		   </div> 
       </div>

    </div> 
	
     <div class="col-12 text-center">
         <br><br>
         
        <input type="button" id="submitbtn" class="btn btn-success btn-lg" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <input type="button" id="submitbtn_drft" name="submitbtn_drft" value="SAVE AS DRAFT"   class="btn btn-lg btn-success mx-2">
		<button type="button" id="taxsummarybtn" class="btn btn-success btn-lg">TAXABLE SUMMARY</button>
        <a href="<?php echo history_back();?>"  class="btn btn-lg btn-secondary mx-2">QUIT</a>
        <a href="javascript:main(0)" class="deletebtn btn btn-lg btn-danger mx-2">Delete</a>
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
							<input type="text" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate" value="<?php echo $forexcrncy_rate;?>" onkeydown="return validatefcrate(event,this.value);" required>
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
</form>

<!-- The Bill By Bill Modal -->
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
      <button type="button" class="btn btn-success prevBtn" onclick="readyBillsModel('prev')">Previous</button>
      <button type="button" class="btn btn-success nextBtn" onclick="readyBillsModel('next')">Next</button>

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
    <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="cc_page">
      </span> Cost Centre</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="cc_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close close_cc_data" data-bs-dismiss="modal">
  </div>
</div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 text-center"><h4>Account: <span id="cc_account"></span></h4></div>
            <div class="col-md-6 text-center"><h4>Total: <span id="cc_total"></span>&nbsp;<span id="cc_drcr"></span></h4></div>
        </div>
        <div id="cc_grid"></div>


            <button id="nextBill" type="button" class="btn btn-primary" onclick="readyCcModel('prev')">Previous</button>
            <button id="prevBill" type="button" class="btn btn-primary" onclick="readyCcModel('next')">Next</button>
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_cc" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Project Reporting Modal -->
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
      <button type="button" class="btn btn-success prevBtn" onclick="readyPrModel('prev')">Previous</button>
      <button type="button" class="btn btn-success nextBtn" onclick="readyPrModel('next')">Next</button>

    </div>
    <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="save_pr">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Item Batch Modal -->
<div class="modal" id="batchmodel" style="z-index: 9999">
<div class="modal-dialog  modal-xl">
  <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="batch_page">
      </span> Item Batch Details</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="batch_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close close_batch_data" data-bs-dismiss="modal">
  </div>
</div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-3 text-center">
          <h4>Item: <span id="batch_item_name">
          </span>
          </h4>
        </div>
        <div class="col-md-3 text-center">
          <h4>Qty: <span id="batch_item_qty">
          </span>
          </h4>
        </div>
        <div class="col-md-3 text-center">
          <h4>Unit: <span id="batch_item_unit">
          </span>
          </h4>
        </div>
        <div class="col-md-3 text-center">
          <h4>Undefined: <span id="batch_item_balance">
          </span>
          </h4>
        </div>
        
      </div>
      <div id="item_batch_grid"></div>
      
      <button  type="button" class="btn btn-success prevBtn" onclick="readyBatchModel('prev')">Previous</button>
      <button type="button" class="btn btn-success nextBtn" onclick="readyBatchModel('next')">Next</button>
      
    </div>
    <!-- Modal footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-success" id="save_batch">Save</button>
      <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
    </div>
  </div>
</div>
</div>
 
<?php echo view('includes/footer_scripts');?>
<script>
var ugst_states       = <?php echo json_encode($ugst_states);?>;
var bo_gstin_type     ='<?php echo $bo_gstin_type;?>';
var dateFrom         = '<?php echo $fy_begndt;?>';
var dateTo           = '<?php echo $fy_end;?>';
var bsd_json_file     = <?php echo $bsd_json_file;?>;
var bo_state_code     = '<?php echo sprintf('%02d', $bo_state_code);?>';
var unitslist         = <?php echo json_encode($units_list);?>;
var item_json_file    = <?php echo $item_json_file;?>;
var goods_rate        ='1';
var services_rate     ='6';
var taxes_list        = <?php echo json_encode($taxes_list);?>;
var bills_method_list = <?php echo json_encode($bills_method_list); ?>;
var drcrlist          = [{"":""},{"C":"C"},{"D":"D"}];
var billsundry_dataModel = {"data":<?php echo json_encode($billsundry_json_data);?>};
var item_grid     = {"data":<?php echo json_encode($item_json_data);?>};
var batch_items      = [];
$(window).on('load', function(){
 $('#reverse_charges option[value="'+$("#reverse_charges option:first").val()+'"]').trigger('change'); 
});

$('#sub_supply_type').on('change', function() {
 item_bsd_totals();
});


$(function() {	
let input = document.getElementById('clone_party_id');
   let timeout = null;
  input.addEventListener('keyup', function (e) {   	
	var val = input.value;			
    var match =$('#party_ids option').filter(function(){
		return (this.value === val);               
    });
	if(match.length==0){
		$("#grid_search").pqGrid("option", "editable", false);
			 $("#billsundry_search").pqGrid("option", "editable", false);
			 $("#party_id option").val('');
			 $("#party_id option").attr("data-is_bbb",0);
			 $("#party_id option").attr("data-dlrtype",0);
			  $("#party_id option").attr("data-gstin","");
			 $("#party_id option").text('');			 
			  $('#invoice_type option[value=""]').prop('selected', true);
			  $('#invoice_type_label').html('');
			  invoice_type_changes();
			  refreshSubSupplyType();
			  
			  show_taxsummary_items();
			  item_bsd_totals();
			 $("#pos").val(default_pos).trigger('change');
		    
	}else{	
		var opt = $('#party_ids option[value="'+val+'"]');
		var opid = (opt.attr('id'));
		var opstcode = (opt.data('statecode'));	
		var opgstin = (opt.data('gstin'));
		var dlrtype = (opt.data('dlrtype'));
		if (opstcode=='' || opstcode=='00') {
			opstcode = default_pos;
		}	
		$("#pos").val(bo_state_code);	
		$("#grid_search").pqGrid("option", "editable", true);
		$("#billsundry_search").pqGrid("option", "editable", true);
		$("#party_id option").val(opt.attr('id'));
		$("#party_id option").attr("data-is_bbb",opt.attr('data-is_bbb'));
		$("#party_id option").attr("data-gstin",opgstin);	
		$("#party_id option").text($(this).val());	
		$("#party_id option").attr("data-dlrtype",dlrtype);	
		$('#party_id option[value="'+opt.attr('id')+'"]').prop('selected', true);
		invoice_type_changes();	
		refreshSubSupplyType();
		item_bsd_totals();
		$("#pos").val(bo_state_code).trigger('change');	
	}   
  
  });
    
});

/******************** Bill By Bill  Start ***********************/
var saved_bill_txns = <?= json_encode($bbb_data) ?>;
if(saved_bill_txns.length > 0){
    $('#bbbCheck').prop('checked', true);
}

/********************Cost Centre  Start ***********************/
var saved_cc_txns = <?= json_encode($cc_data) ?>;
if(saved_cc_txns.length > 0){
    $('#ccCheck').prop('checked', true);
}
    
	
/********************Project Reporting  Start ***********************/
var saved_pr_txns = <?= json_encode($pr_data) ?>; 
    if(saved_pr_txns.length > 0){
        $('#prCheck').prop('checked', true);
    } 
/********************Item Batch  Start ***********************/
var saved_batch_txns = <?= json_encode($item_batch_data) ?>;
if(saved_batch_txns.length > 0){
    $('#itmbtchCheck').prop('checked', true);
} 
var isedit=1;
</script>
<script src="<?php echo base_url();?>public/js/purchase_with_item.js?ver=<?php echo rand();?>"></script>
<script src="<?php echo base_url();?>public/js/purchase_with_item_grid.js?ver=<?php echo rand();?>"></script>
<style>.boldcell{font-weight:700;}</style>
<script>
    $(document).on("click",".deletebtn",function(){
       confirm_delete_post(baseurl+"admin/purchase/delete",'<?php echo clean(obfuscate_link($voucher_txn_id));?>');         
	
       return false
    });
$(function() {	
   let input = document.getElementById('clone_party_id');
   let timeout = null;
   input.addEventListener('keyup', function (e) {
  	var val = input.value;	
		
    var match =$('#party_ids option').filter(function(){
		return (this.value === val);               
    });
	if(match.length==0){
		$("#pos").val("");
		//input.value="";
		     $("#grid_search").pqGrid("option", "editable", false);
			 $("#billsundry_search").pqGrid("option", "editable", false);
			 $("#party_id option").val('');
			 $("#party_id option").attr("data-is_bbb",0);
			 $("#party_id option").attr("data-dlrtype",0);
			 $("#party_id option").attr("data-gstin","");
			 $("#party_id option").text('');
			 
			  $('#invoice_type option[value=""]').prop('selected', true);
			  $('#invoice_type_label').html('');
			  invoice_type_changes();
			  item_bsd_totals();
			  refreshSubSupplyType();
			  $("#pos").trigger('change');
			 
			  
			  
		    
	}else{
		
		console.log("dddddddddddddd");
	var opt = $('#party_ids option[value="'+val+'"]');
	var opid = (opt.attr('id'));
	var opstcode = (opt.data('statecode'));	
	var opgstin = (opt.data('gstin'));
	var dlrtype = (opt.data('dlrtype'));
	
	$("#pos").val(opstcode);	
	$("#grid_search").pqGrid("option", "editable", true);
	$("#billsundry_search").pqGrid("option", "editable", true);
	$("#party_id option").val(opt.attr('id'));
	$("#party_id option").attr("data-is_bbb",opt.attr('data-is_bbb'));
	$("#party_id option").attr("data-gstin",opgstin);
	$("#party_id option").attr("data-dlrtype",dlrtype);	
	$("#party_id option").text($(this).val());	
	$('#party_id option[value="'+opt.attr('id')+'"]').prop('selected', true);
	item_bsd_totals();
	invoice_type_changes();
	refreshSubSupplyType();
	
	$("#pos").trigger('change');
		
	}     
});
    
});	
$("#taxsummarybtn").on("click",function(){
    show_taxsummary_items_hsnwise("",'witm');
});

$("#voucher_date").on("change", function () {
    invoice_type_changes();
    let voucher_date  = $(this).val();
    let $series       = $('select[name="voucher_series"] option:selected');
    let vouchermethod = $series.data("srsmethod");
    let series_id     = $series.val();
    var data = $("#grid_search").pqGrid('option', 'dataModel.data');
    $.each(data, function(index, obj) {
          if (obj.acc_id != '' && obj.acc_id != undefined) {
              /*
             console.log(taxes_list);
             console.log("----");
             console.log(obj.tax_cat_id);
             console.log("----");
             console.log(voucher_date); 
              */
			var taxDetails = getTaxDetails(taxes_list,obj.tax_cat_id, voucher_date);
		     // rd.tax_details  = taxDetails;
              data[index]['tax_details'] = taxDetails;
          }
      });
   // console.log("date changed and afer date grid datra ");
  //  console.log(data);

      $("#grid_search").pqGrid('option', 'dataModel.data', data);
      $("#grid_search").pqGrid('refreshDataAndView');
    
    if (vouchermethod == "1") {
        $.ajax({
            url: baseurl + "admin/ajax/GetAutoBillNo", 
            type: "POST",
            dataType: "json",
            data: {
                voucher_type_id: "<?php echo $voucher_type_id;?>",
                series_id: series_id,
                voucher_date: voucher_date,
				voucher_txn_id:<?php echo $voucher_txn_id;?>
            },
            success: function (response) {
                if (response && response.billno) {
                    
                    $('input[name="billno"]').val(response.billno).prop("disabled", true);
                    item_bsd_totals();
                }
            },
            complete: function () {
                stop_loader();
            },
            error: function (jqXHR, exception) {
                let error = "";
                if (jqXHR.status === 0) {
                    error = "Not connected. Verify Network.";
                } else if (jqXHR.status == 404) {
                    error = "Requested page not found [404].";
                } else if (jqXHR.status == 500) {
                    error = "Internal Server Error [500].";
                } else if (exception === "parsererror") {
                    error = "Requested JSON parse failed.";
                } else if (exception === "timeout") {
                    error = "Time out error.";
                } else if (exception === "abort") {
                    error = "Ajax request aborted.";
                } else {
                    error = "Uncaught Error:\n" + jqXHR.responseText;
                }
                alert_notification(error);
            }
        });
    
     
    }else{
      item_bsd_totals(); 
    }
    
});

$("#voucher_series").on("change", function () {
    show_loader();
    let series_id     = $(this).val();
    let voucher_date  = $("#voucher_date").val();
    let vouchermethod = $("#voucher_series option:selected").data("srsmethod");
    if (vouchermethod == "1") {		
		$.ajax({
            url: baseurl + "admin/ajax/GetAutoBillNo",
            type: "POST",
            dataType: "json",
            data: {
                voucher_type_id: "18",
                series_id: series_id,
                voucher_date: voucher_date
            },
            success: function (response) {
                if (response && response.billno) {
                    tax_details = response.rates;
                    $('input[name="billno"]').val(response.billno).prop("disabled", true);
                    item_bsd_totals()
                }
            },
            complete: function () {
                stop_loader();
            },
            error: function (jqXHR, exception) {
                let error = "";
                if (jqXHR.status === 0) {
                    error = "Not connected. Verify Network.";
                } else if (jqXHR.status == 404) {
                    error = "Requested page not found [404].";
                } else if (jqXHR.status == 500) {
                    error = "Internal Server Error [500].";
                } else if (exception === "parsererror") {
                    error = "Requested JSON parse failed.";
                } else if (exception === "timeout") {
                    error = "Time out error.";
                } else if (exception === "abort") {
                    error = "Ajax request aborted.";
                } else {
                    error = "Uncaught Error:\n" + jqXHR.responseText;
                }
                alert_notification(error);
            }
        });
       
    } else {
        item_bsd_totals();       
        stop_loader();
    }    
});

  
 </script>	
</body>
</html>
