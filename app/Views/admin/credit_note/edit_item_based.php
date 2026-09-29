<?php $header = array(  'title' => 'Update Credit Note(Item)' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));

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

$outsup_rev_chg='';
$pos_id ='';
$supply_type='';
$outsup_eco ='';
$bill_ref_no='';
 $inwsup_cr_note_outsup_id ='';
 

if($gstroutsup_info){
 $supply_type     = 0;
 $pos_id          = $gstroutsup_info['inwsup_pos'];
 $outsup_rev_chg  = $gstroutsup_info['inwsup_rev_chg'];
 $outsup_eco      = $gstroutsup_info['inwsup_eco'];
 $inwsup_cr_note_outsup_id = $gstroutsup_info['inwsup_cr_note_outsup_id'];
  $sess_cmp_suply_type = $gstroutsup_info['gst_supply_type'];
  $bill_ref_no         = $gstroutsup_info['inwsup_bill_ref_no'];
}

$vch_fcy_rate='';
if($vchfcyrate_info && isset($vchfcyrate_info['vch_fcy_rate']))
	$vch_fcy_rate = $vchfcyrate_info['vch_fcy_rate'];
$fcy_field_required='';
if($currency_id>1)
	$fcy_field_required='required';
?>
<style> 
.boldcell{font-weight:700;}
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
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>
<div class="row pb-2">
  <div class="col-md-6 order-1">
    <h3>Update Credit Note(Item)</h3>
  </div>
  <div class="col-md-6 order-3 order-md-2 text-end">
    <div class="taskmenus">
      <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu">
        <span class="material-symbols-outlined">filter_list</span>
      </a>
	   <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
      <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
      <a href="#"><span class="material-symbols-outlined">offline_bolt</span></a>
      <a href="javascript:void(0);" onClick="print_page();"><span class="material-symbols-outlined open-comingsoon">print</span></a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false"> <span class="material-symbols-outlined open-comingsoon">download</span> </a>
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
  <div class="row">
    <div class="col-md-6">
	 <div class="input-group">	 
	 <select name="type" id="type" class="form-select d-inline-block" style="width:120px;">
      <option value="with_item" selected="selected">With Item</option>
     
    </select>	
    <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:100px;">
      <?php foreach ($currency_list as $value) { ?>
      <option <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?> data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>">
      <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-success">#</div>
	</div>
	</div>
  </div>	 
   
	<!--   <div class="form-check d-inline-block me-2">
		  <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="itmtrackingCheck">
		  <label class="form-check-label" for="itmtrackingCheck">Track Item</label>
		</div>	
		
	-->
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="bbbCheck">
      <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
    </div>
	  <div class="form-check d-inline-block me-2">
		  <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="itmbtchCheck">
		  <label class="form-check-label" for="itmbtchCheck">Item Batch</label>
		</div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="prCheck">
      <label class="form-check-label" for="prCheck">Project Reporting</label>
    </div>
     <div class="form-check form-check-inline me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="taxInclusive" id="taxInclusive">
      <label class="form-check-label" for="taxInclusive">Tax Inclusive</label>
    </div>
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="nsCheck">
      <label class="form-check-label" for="nsCheck">Negative Stock</label>
    </div>
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="memoCheck" id="memoCheck">
      <label class="form-check-label" for="memoCheck">Memorandum</label>
    </div>
	
  </div>
  <div class="col-md-6 text-md-end order-4  collapse listmenu" id="listmenu">
    <a href="#" class="btn btn-success btn-sm dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
      <li>
        <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);" data-type="itm">Apply Tax</a>
      </li>       	  
    </ul>
    <button class="btn btn-success btn-sm m-1" type="button">Templates</button>
    <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm showinline-md">Back</a>
  </div>
</div>
<div id="validation_errors">
</div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
  <div class="col-12">
    <div class="row m-0 p-0 align-items-center">
      
      <div class="row">
        <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">Sales Type:</span>
            <?php echo form_dropdown('sale_type', $sub_type_dropdown, '',' id="sale_type" class="sale_type form-select required" required '); ?>
          </div>
        </div>
       <div class="col-md-3 col-6 card p-2" id="against_div">
          <div class="input-group">
            <span class="input-group-text">Against:</span>
			
			<?php 
			$preselect_full_id   = '';
$preselect_label     = '';

if (!empty($inwsup_cr_note_outsup_id)) {

    foreach ($against_dropdown as $key => $value) {

        // key = 2492||147885||R
        // match only by first part (bill_id)
        if (strpos($key, $inwsup_cr_note_outsup_id . '||') === 0) {

            $preselect_full_id = $key;     // 2492||147885||R
            $preselect_label   = $value;   // BILL REF NO...
            break;
        }
    }
}
			?>
			<input list="sale_voucher_ids" id="clone_sale_voucher_id" name="clone_sale_voucher_id" class="form-control form-control-sm required" required>
				<datalist id="sale_voucher_ids">
						<?php
						foreach($against_dropdown as $key => $value){ ?>
						   <option <?php echo ($preselect_full_id==$key)?'selected':'';?> id="<?= $key ?>" value="<?= $value ?>" >
						<?php } ?>
				</datalist>					  
            </div>
        </div>
		<input type="hidden" name="stored_supply_type_id" id="stored_supply_type_id" value="<?php echo $OverrideSupplyTypeId ?? 0;?>">
		 <div class="col-md-3 col-6 card p-2" id="shsupply_type" style="display:none;">
          <div class="input-group">
            <span class="input-group-text">Supply Type:</span>           
            <?php echo form_dropdown('sub_supply_type', [], '',' id="sub_supply_type" class="sale_type form-select required" required '); ?>           
          </div>
        </div>
        
		 <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Voucher No:</label>
          <input type="text" name="voucher" id="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled>
        </div>
      </div>
		 <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">CR. Bill No:</label>
		  <?php if($bill_ref_no!=''){ ?>
          <input type="text" name="billno" id="billno" value="<?php echo $bill_ref_no;?>" class="form-control form-control-sm" readonly>
		  <?php } else { ?>
		  <input type="text" name="billno" id="billno" class="form-control form-control-sm">		  
		  <?php } ?>
        </div>
      </div>
		 <!--<div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">Auto Price:</span>
            <?php 
			//$lastprice_dropdown = array(''=>'','1'=>'MRP Based','2'=>'Last Price');
			//echo form_dropdown('last_price', $lastprice_dropdown, '',' id="last_price" class="sale_type form-select" '); ?>
          </div>
        </div> -->	
        
      </div>
     
	  
	   
	  <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Reverse Charges:</label>
         
          <?php 
          $revrchrgs_lists=array("0"=>"No","1"=>"Yes","2"=>"ECO");
          echo form_dropdown('reverse_charges', $revrchrgs_lists, $outsup_rev_chg,' id="reverse_charges" class="sale_type form-select" '); ?>
         
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
      
      <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Date:</label>
          <input type="text"  id="voucher_date" name="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm">
        </div>
      </div>
      
     <!--
      <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Transporter:</label>
          <span id="transporter_div">
          <?php 
          $transporter_lists=array(""=>"Choose","0"=>"Add New");
          echo form_dropdown('transporter', $transporter_dropdown, "",' id="transporter" class="sale_type form-select" '); ?>
          </span>
		  
        </div>
      </div> -->
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
          <?php echo form_dropdown('pos', $states_lists, $pos_id,' id="pos" class="sale_type form-select required" required'); ?>
        </div>
      </div>
      
           
      <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">MC:</label>
          <?php
		   $sel_sersval_mc = arrayfrstval($matrcntr_dropdown);
		  echo form_dropdown('matrcntr_id', $matrcntr_dropdown, $voucher_info['mat_cent_id'],' id="matrcntr_id" class="form-select required" '); ?>
        </div>
      </div>  
   <?php if($bo_gstin_type==1){ ?>
<div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Ecomerce Operator:</label>
          <?php echo form_dropdown('eco_id', $eco_dropdown, $outsup_eco,' id="eco_id" class="form-select" '); ?>
        </div>
      </div>
	   <?php } ?>
	  
	   <?php if($bo_gstin_type==2){ ?>
		  <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">CMP Supply Type:</span>
            <?php echo form_dropdown('cmp_suply_type', $comp_supply_types_dropdown, $sess_cmp_suply_type,' id="cmp_suply_type" class="sale_type form-select" '); ?>           
          </div>
        </div>
	   <?php } ?>
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
          <textarea  name="narration" class="form-control form-control-sm"><?php echo $narration;?></textarea>
        </div>
      </div>

      <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="bbbdata" id="bbbdata">
	  <input type="hidden" name="prdata" id="prdata">
	  <input type="hidden" name="btnid" id="btnid">
	  <input type="hidden" name="billsndrydata" id="billsndrydata">	
	  <input type="hidden" name="itemsbatchdata" id="itemsbatchdata">	
	  <input type="hidden" name="draft_vch_rec_id" id="draft_vch_rec_id" value="<?php echo $draft_vch_rec_id ?? 0;?>">
     <select style="display:none;" name="sale_voucher_id" id="sale_voucher_id" class="form-select">
	  <?php if ($inwsup_cr_note_outsup_id): ?>
        <option value="<?php echo $inwsup_cr_note_outsup_id; ?>" selected></option>
    <?php else: ?>
         <option  value=""></option>		
    <?php endif; ?>		
	</select>	
    </div>
  </div>
  <div id="grid_search" style="margin:auto;">
  </div>
  
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
							<input type="text" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate" value="<?php echo $vch_fcy_rate;?>" onkeydown="return validatefcrate(event,this.value);" <?php echo $fcy_field_required;?>>
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
					   <input type="hidden" name="invoice_type" id="invoice_type">
			
</form>
<!-- The Modal -->

				
				
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

<!-- List View The Modal Ends Here -->
<?php echo view('includes/footer_scripts');?>
<script> 
const subTypes       = <?php echo $SuppltSubSupplyTypes;?>;
var dateFrom         = '<?php echo $fy_begndt;?>';
var dateTo           = '<?php echo $fy_end;?>';
var ugst_states      = <?php echo json_encode($ugst_states);?>;
var bo_gstin_type    = '<?php echo $bo_gstin_type;?>';
var goods_rate       = 1;
var services_rate    = 6;
var bo_state_code    = '<?php echo sprintf('%02d', $bo_state_code);?>';
var sundry_grid      = {"data":<?php echo json_encode($billsundry_json_data);?>};
var item_grid        = {"data":<?php echo json_encode($item_json_data);?>};
var item_json_file   = <?php echo $item_json_file;?>;
var bsd_json_file    = <?php echo $bsd_json_file;?>;
var unitslist        = <?php echo json_encode($units_list);?>;
var taxes_list       = <?php echo json_encode($taxes_list);?>;
var methods          = <?php echo json_encode($bills_method_list); ?>;
var drcrlist         = [{"":""},{"C":"C"},{"D":"D"}];
var bbb_data         = [];
var bbb_accounts     = [];
var saved_bill_txns  = [];
var item_checked     = [];
var pr_accounts      = [];
var batch_items      = [];
var isedit           = 1;
var voucher_type_id    = <?php echo $voucher_type_id;?>;
var vchtyp_id          = <?php echo $voucher_type_id;?>;
var default_pos    = '<?php echo $pos_id;?>';
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
</script>
<script src="<?php echo base_url();?>public/js/sale_with_item.js"></script>
<script>

$("#taxsummarybtn").on("click",function(){
    show_taxsummary_items_hsnwise("",'witm');
});

$('#sub_supply_type').on('change', function() {
 item_bsd_totals();
});

let against_input = document.getElementById('clone_sale_voucher_id');  
  against_input.addEventListener('keyup', function (e) {
  	var val = against_input.value;			
    var match =$('#sale_voucher_ids option').filter(function(){
		return (this.value === val);               
    });
	if(match.length==0){		  	
	  $("#sale_voucher_id option").val('');
	  $("#sale_voucher_id option").text('');
	}else{
		
	var opt = $('#sale_voucher_ids option[value="'+val+'"]');
	var opid = (opt.attr('id'));
	
	$("#sale_voucher_id option").val(opt.attr('id'));
	$("#sale_voucher_id option").text($(this).val());	
	$('#sale_voucher_id option[value="'+opt.attr('id')+'"]').prop('selected', true);
    item_bsd_totals();	
	}   
    
 });
document.addEventListener("DOMContentLoaded", function() {
    // get selected option from datalist
    let selectedOption = document.querySelector("#sale_voucher_ids option[selected]");
    if (selectedOption) {
        document.getElementById("clone_sale_voucher_id").value = selectedOption.value;
    }
});
$(document).on("click",".deletebtn",function(){
   confirm_delete_post(baseurl+"admin/credit_note/delete",'<?php echo clean(obfuscate_link($voucher_txn_id));?>');         
   return false
   })
$("#taxsummarybtn").on("click",function(){
    show_taxsummary_items_hsnwise("",'witm');
});

$('#supply_type').on('change', function() {
    let catType = $(this).val();
    let $subType = $('#sub_supply_type');

    // clear existing options
    $subType.empty().append('<option value="">-- Select Sub Type --</option>');

    if (catType && subTypes[catType]) {
        $.each(subTypes[catType], function(i, obj) {
            $subType.append($('<option>', {
                value: obj.id,
                text: obj.name
            }));
        });

        // auto-select if only one subType exists
        if (subTypes[catType].length === 1) {
            $subType.prop('selectedIndex', 1);
        }
    }
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
                voucher_type_id: "<?php echo $voucher_type_id;?>",
                series_id: series_id,
                voucher_date: voucher_date,
				voucher_txn_id:<?php echo $voucher_txn_id;?>
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
        $('input[name="billno"]').val("").prop("disabled", false);
        stop_loader();
    }
    
});

function bsd_grid_response_item(bsd_grid){
	var billsundry_item_checked = [];
	var pr_accounts             = [];
	var billsundry_data  = bsd_grid.pqGrid('option', 'dataModel.data');
	var bsdtax_total=0;
	var bsd_total = 0; // pr
	for (var i = 0; i < billsundry_data.length; i++) {
	var billsundry_amount     = billsundry_data[i]['billsundry_amount'];
	var is_tax_account        = billsundry_data[i]['is_tax_account'];
	var billsundry_memoamnt   = billsundry_data[i]['billsundry_memoamnt'];
	var billsundry_amountfc     = billsundry_data[i]['billsundry_amountfc'];
	if(typeof  billsundry_memoamnt == 'undefined' ){
	 billsundry_memoamnt=0;	
	}
	if(billsundry_data[i]['billsundry_id']!=''){ 
		billsundry_item_checked.push({
			"billsundry_id": billsundry_data[i]['billsundry_id'],
			"billsundry_amount": parseAmount(billsundry_amount),
			"billsundry_fcy_amount": parseAmount(billsundry_amountfc),
			"memo_amount"   : billsundry_memoamnt,			
		 });
	if($('#prCheck').is(":checked")){
		var bsd_amt = parseAmount(billsundry_amount);
		var bsd_drcr = 'C';
		if(bsd_amt < 0){
			bsd_drcr = 'D';
			bsd_amt = Math.abs(bsd_amt);
		}
		pr_accounts.push({
			"account_id"    : billsundry_data[i]['billsundry_id'],
			"account_name"  : billsundry_data[i]['billsundry_name'],
			"drcr"          : bsd_drcr,
			"acc_type"      : 'bsd',
			"amount"        : parseAmount(bsd_amt),						
			"grid"          : generatePrData(billsundry_data[i]['billsundry_id'],'bsd')
		});
		bsd_total += parseAmount(billsundry_amount);
	 }
		 
     }
  }
 return {
        "billsundry_items": billsundry_item_checked,
        "pr_accounts":pr_accounts,
        "bsd_total": bsd_total
    };
}

function itm_grid_response_item(items_grid){
	var data              = items_grid.pqGrid('option', 'dataModel.data');
	var item_total        = 0; 
	var total             = 0;
	var blankunits        = 0;
	var final_item_amount = 0; 
	var item_checked = [];
	var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');

	for (var i = 0; i < data.length; i++) {
		var item_id        = data[i]['item_id'];
		var item_name      = data[i]['item_name'];
		var item_price     = data[i]['item_price'];
		var item_qty       = data[i]['item_qty'];
		var item_unit      = data[i]['item_unit_name'];
		var item_unit_id   = data[i]['item_unit_id'];
		var item_pur_acc   = data[i]['item_pur_acc'];
		var item_sales_acc = data[i]['item_sales_acc'];
		var tax_cat_id     = data[i]['tax_cat_id'];
		var tax_details    = data[i]['tax_details'];
		var igst_rate      = data[i]['igst_rate'];
		var description    = "";
		var currency_id    = $('select[name="currency_id"] option:selected').val();
		if(currency_symbol!='₹'){
		 if(typeof data[i]['fcrates'] != "undefined"){
			var fcrates =data[i]['fcrates'];					 	
		 }else{
			var fcrates =  $("#view_fcrates_modal #fcy_forex_rate").val();
		}
		var item_amount = parseAmount(data[i]['item_amountfc']/fcrates);
		var item_fcy_amount= parseAmount(data[i]['item_amountfc']);
		}else{
			var item_amount = data[i]['item_amount'];
			var item_fcy_amount= 0;
		}
		if(typeof data[i]['memo_amt'] == 'undefined' ){
		var memo_amt =0; 
		}else
		var memo_amt = data[i]['memo_amt'];  
	
		if(item_amount>0 && item_id!='' && item_unit_id==''){
			blankunits++;
		}

		if(typeof data[i]['item_amounttxs'] == 'undefined' ){
		var item_amounttxs = 0;
		}else
		var item_amounttxs = data[i]['item_amounttxs']; 

		if(!item_amount)
		  item_amount =0;

		if(!item_fcy_amount)
		  item_fcy_amount =0;
		 final_item_amount=parseAmount(final_item_amount)+parseAmount(item_amount);
		 if(item_id != '' && item_name != ''){
				item_checked.push({
					"item_id"    : item_id,
					"item_price" : parseAmountPrice(item_price,4),
					"item_qty"   :item_qty,
					"item_unit_name"  :item_unit,
					"item_unit_id"      :item_unit_id,
					"item_total_amount" :parseAmount(item_amount),
					"item_total_fcy_amount" :parseAmount(item_fcy_amount),
					"description"       :description,
					"item_drcr"         : 'D',
					"memo_amount"       : memo_amt,
					"txinc_amount"      : item_amounttxs,
					"item_pur_acc"      : item_pur_acc,
					"item_sales_acc"    : item_sales_acc,
					"tax_cat_id"        : tax_cat_id,
					"tax_details"       : tax_details,
					"igst_rate"         : igst_rate,
					"item_name"         : item_name	
				});
			  total += parseAmount(item_amount);
			  item_total += parseAmount(item_amount);
		   }
	 }
	 return {
        "item_checked" : item_checked,
        "total"        : total,
        "item_total"   : item_total,
		"blankunits"   : blankunits,
		"final_item_amount" : final_item_amount
      };
 }	


let SAFE_ITEM_MASTER = [];
let SAFE_ACCOUNT_MASTER = [];
$("#submitbtn,#submitbtn_drft").on("click",function(){
	 show_loader();
	fetch('/admin/load_recent_items')
        .then(res => res.json())
        .then(data => {

            SAFE_ITEM_MASTER    = data.items || [];
            
            item_json_file   = SAFE_ITEM_MASTER;
			
			
        bbb_data     = [];
        bbb_accounts = [];
        pr_data      = [];
        pr_accounts  = [];
		batch_items  = [];
		var btn_id   =  $(this).attr("id");
        var grid_items_response = itm_grid_response_item($('#grid_search'));
		var grid_items = grid_items_response.item_checked;
		item_checked = grid_items; // ✅ ADD THIS LINE!
		// CHECK TAX DIFFERENCE FIRST
    var ok = checkTaxRateDifference(grid_items,SAFE_ITEM_MASTER,SAFE_ACCOUNT_MASTER);

    if (!ok) {
        return false;   // stop submit if mismatch found
    }
       
       
		var item_total = grid_items_response.item_total;
		var item_total_fcy = grid_items_response.item_total_fcy;
		var blankunits = grid_items_response.blankunits;
		var final_item_amount = grid_items_response.final_item_amount;
        var grid_billsundary_response = bsd_grid_response_item($('#billsundry_search'));
        var grid_billsundary = grid_billsundary_response.billsundry_items;
         pr_accounts         = grid_billsundary_response.pr_accounts;
		var bsd_total        =  grid_billsundary_response.bsd_total;
		var bsd_total_fcy     =  grid_billsundary_response.bsd_total_fcy;
				
		var dateFrom = '<?php echo $fy_begndt;?>';
        var dateTo   = '<?php echo $fy_end;?>';
        var dateCheck  =  $("#voucher_date").val();
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
		var matchparty =    $('#party_ids option').filter(function(){
		   return ($("#clone_party_id").val() === this.value);               
          });
		var is_bbb = $('select[name="party_id"] option:selected').data('is_bbb'); 
		if($('#prCheck').is(":checked")){
            var party_total = bsd_total + item_total;
            var party_id    = $('select[name="party_id"]').val();
			var party_total_fcy = bsd_total_fcy + item_total_fcy;
            var party_name  = $('select[name="party_id"] option:selected').text();
            pr_accounts.unshift({
                "account_id"    : party_id,
                "account_name"  : party_name,
                "drcr"          : 'D',
                "acc_type"      : 'acc',
                "amount"        : parseAmount(party_total),
				"amountfc"      : parseAmount(party_total_fcy),
                "grid"          : generatePrData(party_id,'acc')
             });
         }
		 
		 if($('#itmbtchCheck').is(":checked"))
           set_batch_items(grid_items);

        var selerror   = 0;
        if($('select').hasClass('required')){
                $(".required ").each(function() {
                    var txtbx_val = $(this).val();
                    if(txtbx_val.length=="0"){
                        selerror=1;
                        $(this).css('border-color','#ed2000');
                    }else{
                        $('.custom-combobox input').removeAttr("style");  
                        $(this).parent('select').removeClass('required');  
                        $(this).css("border-color","#cccccc");
                    }
                });
           }
      
	   const $sub = $('#sub_supply_type, select[name="sub_supply_type"]').first();
		  if ($sub.length) {
			const needsSub = $sub.is(':visible') && !$sub.prop('disabled') && $sub.find('option').length > 1;
			$sub.prop('required', needsSub);
			if (needsSub && !$.trim($sub.val() || '')){
				 alert_notification("Supply Type field is required");
			     return false;  
			}
		  }
		  
	      if( $("#voucher_date").val()==''){
			 alert_notification('Date field is required.');
			  return false;  
		   }
		  else if( $("#clone_party_id").val()==''){
			 alert_notification('Party field is required.');
			 return false;  
		   }		  
		  else if($("#clone_party_id").val()!='' && matchparty.length==0){
			alert_notification('Party field is not valid.'); 
			return false;  			 
		   }	
	  	 else if(blankunits>0){
			alert_notification("Item's unit cannot be blank.");
            return false;  
		  } 	  
		 else if( $("#pos").val()==''){
			alert_notification('POS field is required');
			return false;  
		   }		   
		   else if( $("#matrcntr_id").val()==''){
			 alert_notification('MC field is required.');
			 return false;  
		   }
          else if( $("#voucher_series").val()==''){
            alert_notification("Kindly fill the form properly.");
            return false;   
          } 
          else if(final_item_amount == 0){
            alert_notification("Kindly fill the items data.");
            return false;   
          }
		  else if(datevalidate==0){
            alert_notification("Voucher can't be saved beyond the financial year period.");
             return false;      
          } 
        else{
			var supplyCtx = applySupplyTypeToItems(grid_items, final_item_amount, bsd_total);
             var ov = evaluateInvoiceOverrideAndSetHidden();
			 // 2) Set overall tax-required flag
			 var taxRequired = evaluateTaxRequiredAndSetHidden(grid_items, ov);
			if (ov.override) {
			  // === IF override = 1 → mark all items and SKIP per-txn classification ===
			  for (var i = 0; i < grid_items.length; i++) {
				grid_items[i].supply_type_id   = ov.id;
				grid_items[i].supply_type_name = ov.name;
			  }
			  // Nothing else to do per txn (your pseudocode: CONTINUE)

			} else {
			  // === No override → run your existing per-txn classification ===
			  // If you already have a function like classifyTxn(...), call it here.
			  // Example (assuming you have 'classifyTxn' + ctx prepared):
			  
			  var pos_code      = (typeof getPOSCode === 'function') ? getPOSCode() : '';
			  var interstate    = !(typeof isOutsideIndia === 'function' && isOutsideIndia(pos_code)) &&
								  (String(bo_state_code).padStart(2,'0') !== String(pos_code).padStart(2,'0'));

			  // detect registration
			  var reg = false;
			  var $partyOpt = $('#party_ids option').filter(function(){
				return ($(this).attr('value')||'').toLowerCase() === ($('#clone_party_id').val()||'').toLowerCase();
			  }).first();
			  var gstin = ($partyOpt.attr('data-gstin') || '').trim();
			  if (gstin) reg = true;
			  if ($partyOpt.attr('data-is_bbb') === '1') reg = false; // example tweak if you store BBB/URD info

			  /**************  Modify *****************/
			    var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
				var fcy_rate = parseFloat($("#view_fcrates_modal #fcy_forex_rate").val()) || 0;
// INR
var isNoTax = (
    bo_gstin_type == '2' || 
    tax_required_flag == 0
);
				var taxBreakup = {};
				var items_tax_total = grid_total_item_taxes(
					$('#grid_search'),
					'',
					bo_state_code,
					pos_code,
					ugst_states,
					'itm',
					taxBreakup
				);

				items_tax_total += grid_total_item_taxes(
					$('#billsundry_search'),
					'',
					bo_state_code,
					pos_code,
					ugst_states,
					'bsd',
					taxBreakup
				);
				var base_total = grid_total_item($('#grid_search'), '') 
							   + grid_total_bsd($('#billsundry_search'), '');

			
// INR
var invoice_incl_gst = isNoTax 
    ? base_total 
    : (base_total + items_tax_total);

				// FC
				var base_total_fc = grid_total_item($('#grid_search'), currency_symbol) 
								  + grid_total_bsd($('#billsundry_search'), currency_symbol);

				var items_tax_total_fc = (fcy_rate > 0) ? (items_tax_total * fcy_rate) : 0;

				
				// FC
var invoice_incl_gst_fc = isNoTax
    ? base_total_fc
    : (base_total_fc + items_tax_total_fc);
			  
			  

			  var ctx = {
				recipient_is_registered: reg,
				interstate: interstate,
				invoice_incl_gst: invoice_incl_gst,
				invoice_incl_gst_fc: invoice_incl_gst_fc
			  };
			  for (var j = 0; j < grid_items.length; j++) {
				var r = (typeof classifyTxn === 'function')
						? classifyTxn(grid_items[j], ctx)
						: { id: SUPPLY_TYPE_IDS['B2CS'], name: 'B2CS' }; // fallback
				grid_items[j].supply_type_id   = r.id;
				grid_items[j].supply_type_name = r.name;
			   }
			 }
             $("#itmsdata").val(JSON.stringify(grid_items));
             $("#billsndrydata").val(JSON.stringify(grid_billsundary));
    	     $("#btnid").val(btn_id);
             show_loader();
			 $.ajax({
				url: baseurl + "admin/ajax/checkTaxMasters",
				type: "POST",
				data: {},
				dataType: "json",
				success: function(response) {
					stop_loader();
					if (response.missing.length > 0) {
						let msg = "Missing Tax Masters:\n" + 
								  "\n\nDo you want to create them?";
						Swal.fire({
							title: '',
							text: msg,
							icon: 'error',
							showCancelButton: true,
							confirmButtonText: 'Yes Create it!',
							customClass: {
							  confirmButton: 'btn btn-success',
							  cancelButton: 'btn btn-outline-danger ms-1'
							},
							buttonsStyling: false
						  }).then(function (result) {
								if (result.value) {
									$.post(baseurl + "admin/ajax/createDefaultTaxMasters", {}, function(r){
											if (!r.status) {
												alert_notification(r.message);
												stop_loader();
												return; // stop further execution
											}
										
												if($('#bbbCheck').is(":checked") && is_bbb){
													readyBills(invoice_incl_gst,invoice_incl_gst_fc);
												}
												else if($('#itmbtchCheck').is(":checked") && batch_items.length>0){
													readyBatches();
												}
												else if($('#prCheck').is(":checked") && pr_accounts.length > 0 ){
													readyPr();
												}
												else{
													$("#salefrm").submit();
												}
											});
								}else{
									alert_notification("Please fix Tax Masters before saving voucher.");
									return false;
								}
					  }); 
					} else {
						if($('#bbbCheck').is(":checked") && is_bbb){
							readyBills(invoice_incl_gst,invoice_incl_gst_fc);
						}
						else if($('#itmbtchCheck').is(":checked") && batch_items.length>0){
							readyBatches();
						}
						else if($('#prCheck').is(":checked") && pr_accounts.length > 0 ){
							readyPr();
						}
						else{
							$("#salefrm").submit();
						}
					}
				}
			});
			 
			 
            
    
        }
     })
        .catch(err => {
            alert('Failed to load latest master data');
            console.error(err);
			stop_loader();
			return false;
        });
		
		
        
    });

    $(document).on('submit', '#salefrm', function(e){
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);

        $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: formData,
            dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('#submitbtn').attr('disabled', 'disabled');
                $('#validation_errors').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    window.location.reload();
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
	                            <div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong>  <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							</div>
	                        `;
                        $('#validation_errors').html(html);
						$('#validation_errors').show();
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
	function print_page() {
    window.open(
        '<?php echo base_url(); ?>admin/CreditNotePrint/<?php echo $voucher_txn_id; ?>?p=1',
        '_blank'
    ).focus();
}







/******************** Bill By Bill  Start ***********************/
var saved_bill_txns = <?= json_encode($bbb_data) ?>;
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
        if($('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
            readyPr();
        }
        else{
            $("#salefrm").submit();
        }
    }
});

function saveBillByBillData(){
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
function readyBills(total, total_fc){
   var account_id = $('select[name="party_id"]').val();
   var account_name = $('select[name="party_id"] option:selected').text();   
	bbb_accounts.push({
		account_id   : account_id,
		account_name : account_name,
		drcr         : 'C',
		amount: total,           // INR
        amountfc: total_fc || 0, // FC
		grid         : generateBillData(account_id)
	});
		
    $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>vouchers/getAccountBillRefs",
        data: {account_id_array: [account_id]},
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

function validateBills() {
    var sum = 0;
    var data = $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data');
    var count = 0;

    for (var i = 0; i < data.length; i++) {

        var method    = data[i]['method'];
        var reference = data[i]['reference'];
        var amount    = data[i]['amount'];
        var drcr      = data[i]['drcr'];

        var parsedAmount = parseAmount(amount);

        // 🚨 Detect partial row (very important)
        if ((method !== '' || reference !== '' || amount !== '' || drcr !== '') &&
            (method === '' || reference === '' || drcr === '')) {
            $('#bill_warning').text("Incomplete bill entry detected.");
            return false;
        }

        if (method !== '' && reference !== '') {

            count++;

            // 🚨 Amount validation
            if (parsedAmount <= 0) {
                $('#bill_warning').text("Amount must be greater than zero.");
                return false;
            }

            var sub = 0;

            // ✅ Core logic (Credit Note handling)
            if (drcr === 'D') {
                sub = parsedAmount;
            } else if (drcr === 'C') {
                sub = -parsedAmount;
            }

            sum += sub;
        }
    }

    if (count === 0) {
        $('#bill_warning').text("Please fill at least one bill entry.");
        return false;
    }

    // ✅ Final amount handling
    var final_amount = parseAmount(bbb_accounts[billIndex].amount);
    var final_drcr   = bbb_accounts[billIndex].drcr;

    if (final_drcr === 'C') {
        final_amount = -final_amount;
    }

    // ✅ Tolerance comparison (important for split values)
    if (Math.abs(sum - final_amount) > 0.01) {
        $('#bill_warning').text("Total Mismatch");
        return false;
    }

    return true;
}

function readyBillsModel(step = ''){
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
		var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
	    var account_name = bbb_accounts[billIndex].account_name;
        var amount = bbb_accounts[billIndex].amount;
		var amountfc = bbb_accounts[billIndex].amountfc;
        var drcr   = bbb_accounts[billIndex].drcr;
        var page   = (billIndex + 1) + '/' + bbb_accounts.length;        
       $('#bills_account').text(account_name);
          if (currency_symbol != "₹") 
          $('#bills_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	      else 
		 $('#bills_total').html(formatAmount(amount));
	 
        $('#bills_drcr').text(drcr + 'r');
        $('#bills_page').text(page);
        $('#billsModal').modal('show');
		 if (currency_symbol != "₹") {
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
            
function generateBillData(account_id = 0){
        var json = [];        
        if(account_id != 0){
           var i = saved_bill_txns.findIndex(function(o) {
               return o.acc_id == account_id;
            });
            if(i >= 0){
                $.each(saved_bill_txns[i].bills_txn_list, function(index, obj){
                    json.push({'method': 'Adjustment', 'reference': obj.bill_ref_name, 'reference_id': obj.bill_ref_id, 'amount': obj.bill_txn_amt,'amountfc': obj.bill_txn_fcy, 'drcr': obj.bill_txn_dr_cr,
                              'due_date': obj.bill_due_date, 'narration': obj.bill_txn_narr});
                });
            }
        }
        
        for(var i=0;i<25;i++){
            json.push({'method': '', 'reference': '', 'reference_id': '', 'amount': '','amountfc': '', 'drcr': '', 'due_date': '', 'narration': ''});
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
        // console.log(date);
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

function expected_bills_amount(grid){
    var amount = 0;
    var data = grid.option('dataModel.data');

    var master_amount = bbb_accounts[billIndex].amount; // 1000
    var master_drcr = bbb_accounts[billIndex].drcr; // D

    if(master_drcr == 'D')
        amount = master_amount;
    if(master_drcr == 'C')
        amount = -master_amount;

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
                        alert_notification('Refernce alredy exists. To use this reference change method to adjustment');
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
    
    var totalData = {
		amountfc  : parseAmount(amountfcTotal),
        amount : '',
        narration : formatAmount(total) + ' (' + drcr + ')',
        pq_rowcls : 'grid_footer_color',
        summaryRow: true
    }
    this.option('summaryData', [totalData]);
}

var bill_dataModel = {"data":generateBillData()} 
var bill_colModel = [
    { title: "METHOD", dataIndx: "method", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: 'select',
            options: methods,
            init: methodEditor
        }
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
                				    if(paeseAmount(fcy_forex_rate) > 0){
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
        grid.setSelection({ rowIndx: 0, focus: false });
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
let currency_symbol = $(this).find("option:selected").data("id"); // get selected symbol
			
if (currency_symbol != "₹") {
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

/******************** Bill By Bill  End ***********************/

 /*************  Start Of Project Reporting  **********/

    var saved_pr_txns = <?= json_encode($pr_data) ?>; 
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
            $("#salefrm").submit();
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
        var item_data = item_checked;      
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>vouchers/getPr",
            data: {item_data : item_data, type: 'SALE'},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status)
                {
                    var item_accounts = response.item_accounts;
                    if(item_accounts){
                        $.each(item_accounts, function(index,obj){
                            item_accounts[index]['grid'] = generatePrData(obj.account_id,'acc');

                            pr_accounts.push(item_accounts[index]);
                        });   
                    }
 
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
            var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
            var account_name = pr_accounts[prIndex].account_name;
            var amount = pr_accounts[prIndex].amount;
            var amountfc = pr_accounts[prIndex].amountfc || 0; // ✅ GET FC AMOUNT
            var drcr = pr_accounts[prIndex].drcr;
            var page = (prIndex + 1) + '/' + pr_accounts.length;
            
            $('#pr_account').text(account_name);
            
            // ✅ FIX: SHOW BOTH FC AND INR LIKE BILL BY BILL
            if(currency_symbol != '₹' && amountfc > 0) {
                $('#pr_total').html(formatAmount(amountfc, currency_symbol) + " &nbsp; " + formatAmount(amount));
            } else {
                $('#pr_total').html(formatAmount(amount));
            }

            $('#pr_drcr').text(drcr + 'r');
            $('#pr_page').text(page);

            $('#prModal').modal('show');
            
            // ✅ FIX: SHOW/HIDE FC COLUMN INSIDE PR GRID
            if(currency_symbol != '₹') {
                pr_colModel[1].hidden = false;
                pr_colModel[1].width = 150;
                pr_colModel[1].title = 'AMOUNT(' + currency_symbol + ')';
                
                $("#pr_grid").pqGrid("option", "colModel", pr_colModel);             
                $("#pr_grid").pqGrid({autoFit: true});
                $("#pr_grid").pqGrid("refreshCM");
                $("#pr_grid").pqGrid("refresh");
            } else {
                pr_colModel[1].hidden = true;
                pr_colModel[1].width = 150;
                pr_colModel[1].title = 'AMOUNT(' + currency_symbol + ')';
                
                $("#pr_grid").pqGrid("option", "colModel", pr_colModel);             
                $("#pr_grid").pqGrid({autoFit: true});
                $("#pr_grid").pqGrid("refreshCM");
                $("#pr_grid").pqGrid("refresh");
            }
            
            // Load Data
            $("#pr_grid").pqGrid('option', 'dataModel.data', pr_accounts[prIndex].grid);
            $("#pr_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();
            $("#pr_grid").pqGrid('setSelection', { rowIndx: 0, colIndx: 3, focus: true });
        }   
    }
         
                
                
    function generatePrData(acc_id=0,acc_type='')
    {
        var json = [];

        if(acc_id != 0 && acc_type != ''){
            var i = saved_pr_txns.findIndex(function(o) {
               return o.acc_id == acc_id && o.acc_type == acc_type;
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
/**************  End of Project Reporting ********************/

/**************  Start of Batch Item ********************/
var saved_batch_txns = <?= json_encode($item_batch_data) ?>;
if(saved_batch_txns.length > 0){
    $('#itmbtchCheck').prop('checked', true);
}

    var batch_items = [];
    var batch_data  = [];
    var batch_Index = 0;  
 function generateBatchGridData(item_id = 0,item_unit_id = 0){
       var batchjson = [];	 
	 if(item_id != 0 && item_unit_id != 0){
		
        var i = saved_batch_txns.findIndex(function(o) {
           return o.item_id == item_id && o.item_unit_id == item_unit_id;
        });
		
		
        if(i >= 0){
            $.each(saved_batch_txns[i].grid, function(index, obj){
               batchjson.push({'batch_method': obj.batch_method ,'batch_no': obj.batch_no, 'batch_id':obj.batch_id,  'manufacturing_date': obj.manufacturing_date, 'batch_qty': obj.batch_qty, 'expiry_date': obj.expiry_date, 'startDate':'<?php echo $fy_begndt;?>','endDate':'<?php echo $fy_end;?>'});
            });
        }
	 }	 
    for(var i=0;i<25;i++){
        batchjson.push({'batch_method': '','batch_no': '','batch_id':'', 'manufacturing_date': '','batch_qty':'','batch_uom':'','batch_uom_id':'', 'expiry_date': '','startDate':'<?php echo $fy_begndt;?>','endDate':'<?php echo $fy_end;?>'});
    }
	
	 return batchjson;
    }
   
    function dateEditorNew(ui) {            
            var $inp = ui.$cell.find("input"),
                di = ui.dataIndx,
                rd = ui.rowData,
                minDate, maxDate,
                startDate = rd.startDate,
                endDate = rd.endDate,                
                grid = this,
                validate = function (that) {
                    var valid = grid.isValid({
                        dataIndx: ui.dataIndx,
                        value: $inp.val(),
                        rowIndx: ui.rowIndx
                    }).valid;
                    if (!valid) {
                        that.firstOpen = false;
                    }
                };

            //calculate minDate and maxDate.
            if(di == "startDate"){
                maxDate = rd.endDate;
            }
            else if(di == "endDate"){
                minDate = rd.startDate;
            }
      
        $inp.on("focusout", function (e) {
        var expiry_date = rd.expiry_date;
        
        if(!isValidDate(expiry_date)){
          rd.expiry_date = '';
          e.preventDefault();
        }
      });
  
    //initialize the editor
       $inp.inputmask("99/99/9999", {
       mask: "99-99-9999",
       alias: "date",
       placeholder: "dd-mm-yyyy",
       insertMode: false,
      })
      .datepicker({
        altFormat: "dd-mm-yyyy",
                dateFormat: "dd-mm-yy",
          minDate: minDate,
                maxDate: maxDate,
                changeMonth: true,
                changeYear: true,
                showAnim: '',
                onSelect: function () {
                    this.firstOpen = true;
                    //validate(this);
                },
                beforeShow: function (input, inst) {
                    return !this.firstOpen;
                },
                onClose: function () {
                    this.focus();
                }
            });
        };

    var units_auto_complete = function (ui) { 
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        $inp.autocomplete({
            source: unitslist,
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 0,
            select: function(event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                rd.batch_uom = ui.item.label;
                rd.batch_uom_id =ui.item.id;
            }
        }).focus(function () {
            $(this).autocomplete("search", "");
            rd.batch_uom = '';
            rd.batch_uom_id = '';
        }).focusout(function () {   
            if(rd.batch_uom_id == '')
            {
                var index = unitslist.findIndex(function(obj) {

                    var string = obj.label.toLowerCase();
                    var text = rd.batch_uom.toLowerCase();
                     
                    return text != '' ? string.includes(text) : false;
                });
                if(index > -1){
                    rd.batch_uom = unitslist[index].label;
                    rd.batch_uom_id = unitslist[index].id;
                    
                }
                else{
                    rd.batch_uom = '';
                    rd.batch_uom_id = '';
                }
            }
        });
    }


  
    function batchEditor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData,
        grid = this;
        
        if(rd.batch_method == 'Adjustment')
        {
            $inp.autocomplete({
                source:  batch_items[batch_Index].batch_ref_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.batch_id = ui.item.id;
                    rd.batch_no = ui.item.label;
                    rd.manufacturing_date = ui.item.batch_mfr;
					rd.expiry_date = ui.item.batch_expiry;   
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.batch_no = '';
                rd.batch_id = '';
                rd.manufacturing_date = '';
				rd.expiry_date = '';
            }).focusout(function () {              
                if(rd.batch_no != '' && rd.batch_id == '')
                {
                    var index = batch_items[batch_Index].batch_ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.batch_no.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val(batch_items[batch_Index].batch_ref_list[index].value); 
                        $inp.trigger(downKeyEvent); 
                        $inp.trigger(downKeyEvent);
                        $inp.trigger(enterKeyEvent);
                    }
                    else{
                        rd.batch_no = '';
                        rd.batch_id = '';
                        rd.manufacturing_date = '';
						rd.expiry_date = '';
                    }
                }
            });
        }
        if(rd.batch_method == 'New Ref.')
        {
            $inp.on("focusout", function () {
                var reference = $(this).val();
                if(reference)
                {
                    var index = batch_items[batch_Index].batch_ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert_notification('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.batch_id = '';
						rd.batch_no = '';
                        
                    }
                }

            });
        }

        $inp.on("change", function (evt) {
            grid.refreshDataAndView();
        });

}

    function calculateBatchSummary() {
        var itemQtyTotal = 0;
        var data = this.option('dataModel.data');

        data.forEach(function(row){
           
            if(row.batch_qty != ''){
                itemQtyTotal += parseFloat(row.batch_qty);
            }
        })

        item_balance = parseFloat(batch_items[batch_Index].item_qty) - parseFloat(itemQtyTotal);

        batch_items[batch_Index].item_balance = item_balance;
        $('#batch_item_balance').text(item_balance);


        var totalData = {
            batch_no: "Total",
            batch_qty  : itemQtyTotal,
            pq_rowcls: 'grid_footer_color',
            summaryRow: true
        }
            
        this.option('summaryData', [totalData]);
    } 

    var batch_dataModel = {'data':generateBatchGridData()}; 
    var date_column = { 
            dataType: 'string',           
        editor: {
            type: 'textbox',
            init: dateEditorNew
        }
    };
    var batch_colModel = [  
   { title: "METHOD", dataIndx: "batch_method", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: 'select',
            options: methods,
            init: batch_methodEditor
        }
    },
    { title: "BATCH NO", width: 100, dataIndx: "batch_no" ,cls: 'pq-drop-icon pq-side-icon',
        editor: {                   
              type: "textbox",
              init: batchEditor
        },
        editable: function (ui) {
           var method = ui.rowData['batch_method'];
            if (method != '') {
                return true;
            }
            return false;
        },
    },
  $.extend( true, {title: "MANUFACTURING DATE", width: 100, dataIndx: "manufacturing_date" ,cls: 'pq-drop-icon pq-side-icon',editable: function (ui) {
           var batch_method = ui.rowData['batch_method'];
            if (isedit == 0 && batch_method != 'Adjustment') {
            return true; // always editable in Add mode
			} else if (isedit == 1 && batch_method!='' && batch_method != 'Adjustment') {
				return true; // editable in Edit mode if not Adjustment
			}		
			return false;
        },}, date_column),      
    $.extend( true, {title: "EXPIRY DATE", width: 100, dataIndx: "expiry_date" ,cls: 'pq-drop-icon pq-side-icon',editable: function (ui) {
           var batch_method = ui.rowData['batch_method'];
            if (isedit == 0 && batch_method != 'Adjustment') {
            return true; // always editable in Add mode
			} else if (isedit == 1 && batch_method!='' && batch_method != 'Adjustment') {
				return true; // editable in Edit mode if not Adjustment
			}		
			return false;
        },}, date_column),
   { title: "QTY", dataIndx: "batch_qty", dataType: "float",width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
        },render: function (ui) {
                    var rd = ui.rowData;
                    var cellData=render_qty(ui.cellData); 
              rd.item_qty=cellData;
                return cellData;
              },
      validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],            
    }
   
    ];

    var batch_billsObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
        height: 'flex',
        selectionModel: { type: 'cell' },
        scrollModel: { autoFit: true },
        pageModel: { type: 'local', rPP: 5 },
        wrap:false,
        numberCell: { show: true },
        dataModel: batch_dataModel,
        colModel: batch_colModel,
        change: calculateBatchSummary, 
        dataReady: calculateBatchSummary,  
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
            grid.setSelection({ rowIndx: 0, focus: false });
        }
    };
    $("#batchmodel").on('shown.bs.modal', function () {   
        if($("#item_batch_grid").pqGrid('instance')){     
            $("#item_batch_grid").pqGrid('refresh');
        }
        else
            $("#item_batch_grid").pqGrid(batch_billsObj);
    });

    $("#save_batch").on("click",function(){
        var status = true;
        status = validateBatch();
        if(!status){
            return;
        }
        status = validateAllBatchData();
        if(!status){
            return;
        }
        if(status){
            saveBatchData();
            $('#batchmodel').modal('hide');
            $("#itemsbatchdata").val(JSON.stringify(batch_data));
            show_loader();
            if($('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
            else{
                $("#salefrm").submit();
            }
            
          }
    });
function batch_methodEditor(ui) {
    var $inp = ui.$cell.find("select"),
        di = ui.dataIndx,
        rd = ui.rowData,               
        grid = this;
    
    $inp.on("change", function (evt) {
        var method = $(this).val();
        rd.batch_no = '';        
    })
};
    function validateAllBatchData() {
    var isAnyBatchValid = false;
    for (var i = 0; i < batch_items.length; i++) {
        var obj = batch_items[i];
        var master_qty = parseFloat(obj.item_qty);
        var qtycount = 0;
        var hasValidRow = false;
        for (var j = 0; j < obj.grid.length; j++) {
            var obj2 = obj.grid[j];
            var batch_no = obj2.batch_no;
            var batch_qty = obj2.batch_qty;
            var batch_uom_id = obj2.batch_uom_id;

            if (batch_no !== '' && batch_qty !== '') {
                qtycount += parseFloat(batch_qty);
                hasValidRow = true;
            }
        }
        if (hasValidRow) {
            if (parseFloat(qtycount.toFixed(2)) !== parseFloat(master_qty.toFixed(2))) {
                $('#batch_warning').text('Batch qty mismatch for a filled grid.');
                return false;
            }
            isAnyBatchValid = true;
            break; // one valid batch found and verified
        }
    }
    if (!isAnyBatchValid) {
        $('#batch_warning').text('Please fill at least one batch grid.');
        return false;
    }
    return true;
}

    function validateBatch()
    {
        var sum = 0;
        var data = $("#item_batch_grid").pqGrid('option', 'dataModel.data');
        var error = 0;
        var qtycount = 0;
        
        for (var i = 0; i < data.length; i++) 
        {
            if(data[i]['batch_no'] != '' && data[i]['batch_qty'] != '')
            {
               
                var batch_no         = data[i]['batch_no'];
                var batch_qty           = data[i]['batch_qty'];
                var batch_uom_id      = data[i]['batch_uom_id'];
              
                qtycount += parseFloat(batch_qty);
                
            }
        }
      
      var master_qty = batch_items[batch_Index].item_qty;
       

       if(parseFloat(qtycount) > parseFloat(master_qty)){
            alert("Unit Qty mismatch!!!! ");
            return false;
        }
        return true;
    }

    function saveBatchData()
    {
      
      batch_data=[];
        var batch_item_qty = {};
      
        $.each(batch_items, function(index,obj)
        {   
            if(obj.item_id!='' && obj.item_name != '')
            {
                var item_id = obj.item_id;
                var item_unit = obj.item_unit;
                var item_unit_id = obj.item_unit_id;

				var master_item_qty = obj.item_qty;

                
                var batch_item_qty = 0;
                
            
                $.each(obj.grid, function(index2, obj2)
                {
                    var batch_id            = obj2.batch_id;
                    var batch_no            = obj2.batch_no;
                    var batch_qty           = obj2.batch_qty;
                    var expiry_date         = obj2.expiry_date;
                    var manufacturing_date  = obj2.manufacturing_date;
					var batch_method        = obj2.batch_method;
						   
					if(batch_no!=''){

					  batch_item_qty += batch_qty;

								batch_data.push({
									"batch_id"           : batch_id,
									"item_id"            : item_id,
									"batch_no"           : batch_no,
									"batch_qty"          : batch_qty,
									"batch_uom"          : item_unit,
									"batch_uom_id"       : item_unit_id,
									"expiry_date"        : expiry_date,
									"batch_method"       : batch_method,
									"manufacturing_date" : manufacturing_date,
									"drcr_type"          : 1							
								});
					}
                     
                });   
                
                
          /* var batch_difference = (parseFloat(master_item_qty)-parseFloat(batch_item_qty));

              batch_data.push({
                    "batch_id"          : 0,
                    "item_id"           : item_id,
                    "batch_no"          : 'UNDEFINED',
                    "batch_qty"         : batch_difference,
                    "batch_uom"         : item_unit,
                    "batch_uom_id"      : item_unit_id,
                    "expiry_date"       : '',
                    "batch_method"      : 'Adjustment',
                    "manufacturing_date": '',                   
                }); */
            }
        });
        
        
    }

    function set_batch_items(grid_items)
    {
        batch_items = [];
		
        if(grid_items.length > 0)
        {
            $.each(grid_items, function(index, object)
            {
                var i = batch_items.findIndex(function(obj) {
                    return obj.item_id == object.item_id && obj.item_unit_id == object.item_unit_id;
                });

                if(i > -1){
                    batch_items[i].item_qty += parseFloat(object.item_qty);
                }
                else{
                    batch_items.push({
                       "item_id": object.item_id,
                       "item_name" : object.item_name,
                       "item_qty" : parseFloat(object.item_qty),
                       "item_balance" : parseFloat(object.item_qty),
                       "item_unit" : object.item_unit_name,
                       "item_unit_id" : object.item_unit_id,
                       "grid": generateBatchGridData(object.item_id,object.item_unit_id)  
                   });
                }
            });
        }
    }


  function readyBatches(){
  var items_id_array = batch_items.map(function(obj) {
    return {
        item_id: obj.item_id,
        item_unit_id: obj.item_unit_id
    };
});
   $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>vouchers/getItemBatch",
        data: {items_id_array: items_id_array},
        datatype: "json",
        success: function(response){
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if(response.status)
            {
                $.each(response.data, function(index,obj)
                {
                    batch_items[index].batch_ref_list = obj;
                });
                stop_loader();
                readyBatchModel();
            }
        }
    }); 
  
    }

    function readyBatchModel(step = '')
    {
        if(step == ''){
            batch_Index = 0;
        }
        else{
            var status = validateBatch();
            if(!status){
                return;
            }
        }
        
        if(step == 'next'){
            if(batch_Index < (batch_items.length - 1)){
                batch_Index = batch_Index + 1;
            }
        }
        if(step == 'prev'){
            if(batch_Index > 0){
                batch_Index = batch_Index - 1;
            }
        }
		
        if(batch_items[batch_Index])
        {
            var item_name = batch_items[batch_Index].item_name;
            var item_unit = batch_items[batch_Index].item_unit;
            var item_qty = batch_items[batch_Index].item_qty;
            var item_balance = batch_items[batch_Index].item_balance;
        
        
			var page = (batch_Index + 1) + '/' + batch_items.length
			
            $('#batch_item_name').text(item_name);
			$('#batch_item_qty').text(item_qty);
			$('#batch_item_unit').text(item_unit);
			$('#batch_item_balance').text(item_balance);
          
            $('#batch_page').text(page);
            $('#batchmodel').modal('show');
            $("#item_batch_grid").pqGrid('option', 'dataModel.data', batch_items[batch_Index].grid);
            $("#item_batch_grid").pqGrid('refreshDataAndView');

            $('input[type="command-line"]').focus();//tempararily shift focus
            $("#item_batch_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
        }
        
    }  

/********************End Of Batch Items   *****************************************/



</script>

</body>
</html>