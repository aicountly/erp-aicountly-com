<?php $header = array(  'title' => 'Update Debit Note Invoice' ); ?>
<?php echo view('includes/header',$header); 
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
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
<?php
$vch_fcy_rate='';
if($vchfcyrate_info && isset($vchfcyrate_info['vch_fcy_rate']))
	$vch_fcy_rate = $vchfcyrate_info['vch_fcy_rate'];

$supply_type=0;
$pos_id ='';
$dr_note_inwsup_id='';
$outsup_eco='';
$outsup_rev_chg=0;

if($gstroutsup_info){
  $supply_type = 0;
  $pos_id      = $gstroutsup_info['outsup_pos'];
  $dr_note_inwsup_id = $gstroutsup_info['dr_note_inwsup_id'];
  $outsup_eco = $gstroutsup_info['outsup_eco'];
  $outsup_rev_chg = $gstroutsup_info['outsup_rev_chg'];
}

?>
<div class="row pb-2">
  <div class="col-md-6 order-1"><h3>Update Debit Note Invoice</h3></div>  
  <div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>      
 <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>

<a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
      <a href="#"><span class="material-symbols-outlined">offline_bolt</span></a> 

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
        <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
 </div>
 </div> 

<div class="col-md-6 order-2 order-md-3">
<div class="col-md-6">  
    <div class="input-group">
	 <select name="type" id="type" class="form-select d-inline-block" style="width:150px;">
   
        <option value="without_item" selected="selected">Invoice Based</option>
    </select>
    <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:120px;">
      <?php foreach ($currency_list as $value) { ?>
      <option data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>" <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?>>
      <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-success">#</div>
	</div>
	
   </div>
    <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
    <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
   </div>
   
    <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
    <label class="form-check-label" for="ccCheck">Cost Centre</label>
   </div>
   
   <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" value="1" id="prCheck">
      <label class="form-check-label" for="prCheck">Project Reporting</label>
    </div>
	
	 <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" value="1" form="salefrm" name="taxInclusive" id="taxInclusive">
      <label class="form-check-label" for="taxInclusive">Tax Inclusive</label>
    </div>
	
	
	 <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" value="1" form="salefrm" name="memoCheck" id="memoCheck">
      <label class="form-check-label" for="memoCheck">Memorandum</label>
    </div>

   
  </div>  

<div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
    <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
    <li>
        <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);" data-type="acc">Apply Tax</a>
      </li> 
  </ul>  
  <button class="btn btn-success m-1" type="button">Templates</button>
  <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
  </div> 
</div>

<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
 <div class="col-12">
  <div class="row m-0 p-0 align-items-center">
      
   <div class="col-md-3 col-6 card p-2">
    <div class="input-group">
     <span class="input-group-text">Series:</span>
	  <select name="voucher_series" id="voucher_series" class="voucher_series form-select required" required>
			<?php			
			foreach($voucher_series as $vch_series_info){ ?>
               <option id="<?php echo $vch_series_info['vch_series_id']; ?>" data-srsmethod="<?php echo $vch_series_info['vch_series_method']; ?>"  value="<?php echo $vch_series_info['vch_series_id']; ?>" <?php echo ($vch_series_id==$vch_series_info['vch_series_id']) ? 'selected' :'';?>><?= $vch_series_info['vch_series_name'] ?></option>
            <?php } ?>
		  </select>
    </div>
   </div>
   
   <?php 
			$preselect_full_id   = '';
$preselect_label     = '';

if (!empty($dr_note_inwsup_id)) {

    foreach ($against_dropdown as $key => $value) {

        // key = 2492||147885||R
        // match only by first part (bill_id)
        if (strpos($key, $dr_note_inwsup_id . '||') === 0) {

            $preselect_full_id = $key;     // 2492||147885||R
            $preselect_label   = $value;   // BILL REF NO...
            break;
        }
    }
}
			?>
			
			
   <div class="col-md-3 col-6 card p-2" id="against_div">
          <div class="input-group">
            <span class="input-group-text">Against:</span>
			<input list="purchase_voucher_ids" id="clone_purchase_voucher_id" name="clone_purchase_voucher_id" class="form-control form-control-sm required" required>
				<datalist id="purchase_voucher_ids">
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
          <label class="input-group-text">DR. Bill No:</label>
         <?php if($voucher_bill_no!=''){ ?>
		  <input type="text" name="billno" id="billno" value="<?php echo $voucher_bill_no;?>" class="form-control form-control-sm" disabled>
		  <?php } else{ ?>
          <input type="text" name="billno" id="billno" class="form-control form-control-sm">
		  <?php } ?>
        </div>
      </div> 
    
  	
   <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Date:</label> 
           <input type="text"  id="voucher_date" name="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm">
       </div>
    </div>  
      
   <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Voucher No:</label>
           <input type="text" name="voucher" id="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled>
       </div>
   </div>
      
   <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">ITC Eligibility:</label>
           <input type="text" id="itc_eligibility" name="itc_eligibility" class="form-control form-control-sm" disabled>
       </div>
   </div>
 
	  
   <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Party:</label> 
          <input list="party_ids" id="clone_party_id" name="clone_party_id" value="<?php echo $voucher_info['party_name'];?>" class="form-control form-control-sm required" onmouseover="focus();" required>
		  <datalist id="party_ids">
		    <?php
			foreach($party_dropdown as $value){ ?>
               <option data-is_sez="<?php echo $value['is_sez']?>" data-grpid="<?php echo $value['grpid']?>" data-gstin="<?php echo $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_name'] ?>" <?php echo ($value['acc_id']==$voucher_info['party_id'])?'selected':'';?>>
            <?php } ?>
		  </datalist>
        </div>
    </div>
 <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">POS:</label>
          <?php echo form_dropdown('pos', $states_lists, $pos_id,' id="pos" class="sale_type form-select required" '); ?>
        </div>
      </div>
    
	  <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Reverse Charges:</label>         
          <?php 
          $revrchrgs_lists=array("0"=>"No","1"=>"Yes","2"=>"ECO");
          echo form_dropdown('reverse_charges', $revrchrgs_lists, $outsup_rev_chg,' id="reverse_charges" class="sale_type form-select" ');
		  ?>         
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
	  <input type="hidden" name="invoice_type" id="invoice_type">
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
	  <input type="hidden" name="ccdata" id="ccdata">
      <input type="hidden" name="billsndrydata" id="billsndrydata">
       <input type="hidden" name="draft_vch_rec_id" id="draft_vch_rec_id" value="<?php echo $draft_vch_rec_id ?? 0;?>"> 
 <select style="display:none;" name="purchase_voucher_id" id="purchase_voucher_id" class="form-select">
	  <?php if ($dr_note_inwsup_id): ?>
        <option value="<?php echo $dr_note_inwsup_id; ?>" selected></option>
    <?php else: ?>
         <option  value=""></option>		
    <?php endif; ?>		
	</select>

 </div>
 </div>

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12">
	<div class="row">
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
	
    <div class="col-12 text-center pt-2">
	 <?php if ($rejected_vch == 1): ?>

    <!-- Rejected: Only QUIT -->
    <a href="javascript:main(0)" onclick="window.history.go(-1); return false;" 
       class="btn btn-lg btn-secondary mx-2">QUIT</a>

       <?php elseif ($pending_vch == 1): ?>

    <?php if ($Voucher_TxnApproval_UUID == $current_uuid): ?>
        <!-- Pending: Current user is approver -->
        <input type="button" id="approvesubmitbtn" name="approvesubmitbtn" 
               value="APPROVE" 
               title="<?= $_ENV['SAVEKEY_SRTCDE'];?>" 
               alt="<?= $_ENV['SAVEKEY_SRTCDE'];?>" 
               class="btn btn-lg btn-success mx-2">

        <input type="button" id="rejectsubmitbtn" name="rejectsubmitbtn" 
               value="REJECT" 
               title="<?= $_ENV['SAVEKEY_SRTCDE'];?>" 
               alt="<?= $_ENV['SAVEKEY_SRTCDE'];?>" 
               class="btn btn-lg btn-warning mx-2">
			   
		<a href="javascript:main(0)" onclick="window.history.go(-1); return false;" 
       class="btn btn-lg btn-secondary mx-2">QUIT</a>	   
    <?php else: ?>
        <!-- Pending: Other users -->
        <a href="javascript:main(0)" onclick="window.history.go(-1); return false;" 
           class="btn btn-lg btn-secondary mx-2">QUIT</a>
    <?php endif; ?>

<?php else: ?>
         
        <input type="button" id="submitbtn" class="btn btn-success btn-lg" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
        <input type="button" id="submitbtn_drft" name="submitbtn_drft" value="SAVE AS DRAFT"   class="btn btn-lg btn-success mx-2">
		<button type="button" id="taxsummarybtn" class="btn btn-success btn-lg">TAXABLE SUMMARY</button>
        <a href="<?php echo history_back();?>"  class="btn btn-lg btn-secondary mx-2">QUIT</a>
        <a href="javascript:void(0);"  class="deletebtn btn btn-danger btn-lg">Delete</a>

    <?php endif; ?>
        
   </div>
        
    </div>
	
	<?php
	foreach ($party_dropdown as $party) {
    if ($party['acc_id'] == $voucher_info['party_id']) {
        $partyInfo = $party;
        break;
    }
}
	?>
	<select style="display:none;" name="party_id" id="party_id" class="form-select">
	<?php if ($partyInfo): ?>
        <option 
            data-grpid="<?php echo htmlspecialchars($partyInfo['grpid']); ?>" 
            data-is_bbb="<?php echo htmlspecialchars($partyInfo['is_bbb']); ?>" 
            selected 
            value="<?php echo htmlspecialchars($partyInfo['acc_id']); ?>">
            <?php echo htmlspecialchars($partyInfo['acc_name']); ?>
        </option>
    <?php else: ?>
        <option data-grpid="0" data-is_bbb="0"></option>
    <?php endif; ?>			
	 </select>
                
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
							<input type="text" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate" value="<?php echo $vch_fcy_rate;?>" onkeydown="return validatefcrate(event,this.value);" required>
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
                         <button type="button" class="btn btn-success" id="save_forex_rates">Save</button>
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
    <button type="button" class="btn-close" data-bs-dismiss="modal">
  </div>
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

<!-- List View The Modal Starts here -->
<div class="modal" id="listviewModal">
  <div class="modal-dialog  modal-md">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">List View</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
<ol class="list-group list-group-flush">
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">List Name 1 goes here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">List Name 2 goes here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">3rd List name here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
</ol>
 <p class="col-md-12 text-end mt-2"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> </p>
      </div>
           

    </div>
  </div>
</div>

<div class="modal fade  mr-0" id="ItemsHSNModal" data-backdrop="static">
  <div class="modal-dialog modal-dialog-scrollable modal-xl" >
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
	    <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<!-- List View The Modal Ends Here -->

<?php echo view('includes/footer_scripts'); 
$json_data = $account_transactions;
$total_items_txns = count($json_data);

for($i=1;$i<=(500-$total_items_txns);$i++)
    $json_data[] =array("acc_id"=>'',"account_name"=>'','pq_cellattr'=> array('account_name'=>array('title'=>'')), 'description'=>'','amount'=>'');



$billsundry_json_data = $bsd_transactions;
$total_bsd_txns = count($billsundry_json_data);
for($i=1;$i<=(500-$total_bsd_txns);$i++)
    $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_rate'=>'','billsundry_amount'=>'');
?>

<style>
    .boldcell{font-weight:700;}
</style>
<script>
const subTypes = <?php echo $SuppltSubSupplyTypes;?>;
var dateFrom = '<?php echo $fy_begndt;?>';
var dateTo   = '<?php echo $fy_end;?>';
var bsd_comp_applytax_json=[];
var bo_gstin_type='<?php echo $bo_gstin_type;?>';
var goods_rate   ='1';
var services_rate='6';
var taxes_list=<?php echo json_encode($taxes_list);?>;
var  get_total = 0;
var  get_fcy_total = 0;
var  total_tax_val = 0;
var  total_fcy_tax_val = 0;
var voucher_type_id    = <?php echo $voucher_type_id;?>;
var vchtyp_id          = <?php echo $voucher_type_id;?>;
$("#taxsummarybtn").on("click",function(){
    show_taxsummary_items_hsnwise("",'witm');
});
$('#sub_supply_type').on('change', function() {
 item_bsd_totals();
});


$(document).on("click",".deletebtn",function(){
	<?php if($draft_vch_rec_id>0){ ?> 
     confirm_delete_post(baseurl+"/admin/debit_note/delete_draft/<?php echo clean(obfuscate_link($voucher_txn_id));?>");        
	 <?php } else { ?>
     confirm_delete_post(baseurl+"admin/debit_note/delete",'<?php echo clean(obfuscate_link($voucher_txn_id));?>');         
	 <?php } ?>
    return false
   })

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
                voucher_date: voucher_date
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
    if (vouchermethod == 1) {		
		$.ajax({
            url: baseurl + "admin/ajax/GetAutoBillNo",
            type: "POST",
            dataType: "json",
            data: {
                voucher_type_id: "<?php echo $voucher_type_id;?>",
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
        $('input[name="billno"]').prop("disabled", false);
        stop_loader();
    }
    
});

/*********************  FC Rates   Start         *********************/


function calculate_fcChanged(currency_id,billsundry_colModel,colModel,currency_symbol){
     if(currency_id>1){
                $("#view_fcrates_modal #selcyrlabel").html(currency_symbol);				
				$("#view_fcrates_modal #fcy_voucher_no").html($("#voucher").val());
				$("#view_fcrates_modal #fcy_voucher_date").html($("#voucher_date").val());				
			    $("#view_fcrates_modal").modal('show');	
			    colModel[2].hidden=false;
                colModel[2].width= 180;
				colModel[2].title='AMOUNT('+currency_symbol+')';
				colModel[3].editable=false;
                $("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refreshCM");
                $("#grid_search").pqGrid("refresh");	
		}               
         else{	colModel[2].hidden=true;
                colModel[2].width= 180;
               	colModel[3].editable=true;
				$("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refreshCM");
                $("#grid_search").pqGrid("refresh");	
		    }
    }
    
function validatefcrate(e,a){return IsNumericFCRate(a,e.keyCode?e.keyCode:e.charCode)}	
var isShiftt = false;
function IsNumericFCRate(_,e){if(16==e&&(isShiftt=!0),(!(e>=48)||!(e<=57))&&8!=e&&!(e<=37)&&!(e<=39)&&(!(e>=96)||!(e<=105))&&190!=e&&110!=e||!1!=isShiftt||_.includes(".")&&(190==e||110==e))return!1;if(_.includes(".")){if(_.split(".")[1].length>=8&&(e>=96&&e<=105||e>=48&&e<=57))return!1}else if(_.length>=6&&(e>=96&&e<=105||e>=48&&e<=57))return!1;return!0}
$(".clsoefcratemodal").on("click",function(){
	$("#view_fcrates_modal #fcy_voucher_no").html("");
	$("#view_fcrates_modal #fcy_voucher_date").html("");
	$("#view_fcrates_modal").modal("hide");
	});	
$("#view_fcrates_modal").on("hidden.bs.modal",function(){
	$("#view_fcrates_modal #fcy_voucher_no").html("");
	$("#view_fcrates_modal #fcy_voucher_date").html("");
   });

/*********************  FC Rates  END          *********************/
function grid_total_item(obj,currency=''){
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
 
            data.forEach(function(row){
                if(row.account_name != '' && row.account_name != undefined && row.amount != '' && row.amount != undefined)
                {
				  if(currency=='€' || currency=='$'){
					total += parseAmount(row.amountfc); 
				 }
				 else if(currency=='memo'){
					total += parseAmount(row.memo_amt); 
				 }
				 else{
					total += parseAmount(row.amount);					
				 }
				}
            });
        }
        return total;
    }
	function grid_total_bsd(obj,currency=''){
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
			
            data.forEach(function(row){
                if(row.billsundry_id != '' && row.billsundry_id != undefined && row.billsundry_amount != '' && row.billsundry_amount != undefined)
                { 
				 if(currency=='€' || currency=='$'){
					total += parseAmount(row.billsundry_amountfc); 
				 }
				 else if(currency=='memo'){
					total += parseAmount(row.billsundry_memoamnt); 
				 }else{
					total += parseAmount(row.billsundry_amount);
				 }				 
				}
            });
        }
        return total;
    }
$(document).on("change","#reverse_charges",function(){
	show_loader();
	invoice_type_changes();
	 if($(this).val()==1 || $(this).val()==2){
		  
		var bsd_taxsummary_json=[];
		for(var i=1;i<=50;i++){		 
	     bsd_taxsummary_json.push({'bl_nature':'','is_tax_account':0,'tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	   }
	  $("#billsundry_search").pqGrid('option', 'dataModel.data', bsd_taxsummary_json);
	  $("#billsundry_search").pqGrid('refreshDataAndView');	  
	  stop_loader();
	 }else{
	   stop_loader();
	 }		 
 });	
 

$(function(){	
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
			 $("#party_id option").attr("data-gstin","");
			 $("#party_id option").attr("data-grpid","");
			 $("#party_id option").text('');			 
			 $('#invoice_type option[value=""]').prop('selected', true);
			 $('#invoice_type_label').html('');
			 invoice_type_changes();
			 refreshSubSupplyType();
			 $("#pos").val(default_pos).trigger('change');
		    
	}else{
	
	var opt = $('#party_ids option[value="'+val+'"]');
	var opid = (opt.attr('id'));
	var opstcode = (opt.data('statecode'));	
	var opgstin = (opt.data('gstin'));
	if (opstcode=='' || opstcode=='00') {
				opstcode = default_pos;
	}
	$("#pos").val(opstcode);	
	$("#grid_search").pqGrid("option", "editable", true);
	$("#billsundry_search").pqGrid("option", "editable", true);
	$("#party_id option").val(opt.attr('id'));
	$("#party_id option").attr("data-is_bbb",opt.attr('data-is_bbb'));
	$("#party_id option").attr("data-grpid",opt.attr('data-grpid'));
	$("#party_id option").attr("data-gstin",opgstin);	
	$("#party_id option").text($(this).val());	
	$('#party_id option[value="'+opt.attr('id')+'"]').prop('selected', true);
	invoice_type_changes();
	refreshSubSupplyType();
	$("#pos").val(opstcode).trigger('change');		
	}   
  });
});

var ugst_states        = ['35','04','26','25','31','38','34','97'];
var accounts_json_file = <?php echo ($accounts_json_file!='')?$accounts_json_file:'[]'; ?>;
var bsd_json_file  = <?php echo ($bsd_json_file!='')?$bsd_json_file:'[]';?>;
var bo_state_code  = '<?php echo sprintf('%02d', $bo_state_code);?>';
var billsundry_dataModel = {"data":<?php echo json_encode($billsundry_json_data);?>};
var isdrnote=1;

function getAgainstVoucherTag() {

    var val = $('#purchase_voucher_id').val();   // can be: "2492||147885||R" OR "2492"

    if (!val) return '';

    // Case 1: already full composite value (ADD MODE)
    if (val.indexOf('||') !== -1) {
        var parts = val.split('||');
        return parts[2] || '';   // R or C
    }

    // Case 2: only bill_id saved (EDIT MODE) → find from datalist
    var bill_id = val;

    // Find option whose id starts with "2492||"
    var opt = $('#purchase_voucher_ids option').filter(function () {
        var id = $(this).attr('id') || '';
        return id.startsWith(bill_id + '||');
    }).first();

    if (opt.length === 0) return '';

    var full_id = opt.attr('id');   // 2492||147885||R
    var parts = full_id.split('||');

    return parts[2] || '';   // R or C
}
</script>
<script src="<?php echo base_url();?>public/js/purchase_without_item.js"></script>
<script src="<?php echo base_url();?>public/js/purchase_without_item_grid.js"></script>
<script>
$(document).ready(function () {
    // get selected option from datalist
    let $selectedOption = $("#purchase_voucher_ids option[selected]");

    if ($selectedOption.length) {
        

        // fill the input textbox with the option value
        $("#clone_purchase_voucher_id").val($selectedOption.val());

        // set hidden select's option value = option id (voucher id)
        $("#purchase_voucher_id")
            .html(`<option value="${$selectedOption.attr("id")}" selected>${$selectedOption.val()}</option>`);
    }
});
let against_input = document.getElementById('clone_purchase_voucher_id');  
  against_input.addEventListener('keyup', function (e) {
  	var val = against_input.value;			
    var match =$('#purchase_voucher_ids option').filter(function(){
		return (this.value === val);               
    });
	if(match.length==0){		  	
	  $("#purchase_voucher_id option").val('');
	  $("#purchase_voucher_id option").text('');
	}else{
		
	var opt = $('#purchase_voucher_ids option[value="'+val+'"]');
	var opid = (opt.attr('id'));
	
	$("#purchase_voucher_id option").val(opt.attr('id'));
	$("#purchase_voucher_id option").text($(this).val());	
	$('#purchase_voucher_id option[value="'+opt.attr('id')+'"]').prop('selected', true);	
	}   
    
 });
 

var billsundry_autoComplete = function (ui) {
        var rd      = ui.rowData;
        var $inp    = ui.$cell.find("input");
        var element = {};
        $inp.autocomplete({
                source:  bsd_json_file,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {					
					// check if item choosed or not 
				
					event.preventDefault();
					var voucherDate = $("#voucher_date").val();
                    rd.tax_catg_id     = ui.item.tx_ct_id;
                    rd.billsundry_name = ui.item.label;
                    rd.billsundry_id   = ui.item.id;
                    
                  
                    var taxDetails = getTaxDetails(taxes_list,rd.tax_catg_id, voucherDate);
                   
                    if(taxDetails){
		               rd.tax_details  = taxDetails;
		               rd.igst_rate    = taxDetails.igst;
		         	   rd.cess_rate    = taxDetails.cess;
                    }
                    else{
                      rd.tax_details  = '';
		               rd.igst_rate    = 0;
		         	   rd.cess_rate    = 0;  
                    }
			
                    rd.bl_tax_rate     =  taxDetails.igst;
					rd.bl_hsn_sac      = ui.item.bl_hsn_sac;
					rd.bl_nature       = ui.item.bl_nature;
					rd.is_tax_account  = ui.item.is_tax_account;
					rd.bl_ipt_ott      = ui.item.bl_ipt_ott;
					
					if(ui.item.is_tax_account=="1" && bo_gstin_type=="2"){
						alert_notification("Composition Gstin Dealers Are Not Allowed To Add Tax In Sale Invoice");
						return false;
					}
					$(this).val(ui.item.label);										
			    	$("#billsundry_search").pqGrid('refreshDataAndView');
  				
	           }
             
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.billsundry_name = '';
                rd.billsundry_id = '';
            }).focusout(function () {              
                if(rd.billsundry_id == '')
                {
                    rd.billsundry_name = '';
                    rd.billsundry_id = '';
                }
			
				 
			var errors=0;			
			var bsddata = $("#billsundry_search").pqGrid('option', 'dataModel.data');
			 for (var i = 0; i < bsddata.length; i++) {											 
			   if( (bsddata[ i ]['billsundry_id'] == rd.billsundry_id) && (bsddata[ i ] != ui.rowData && rd.is_tax_account=="1") ) {
					errors=1;
					$("#billsundry_search").pqGrid( "updateRow",{ rowIndx: ui.rowIndx, row: {'billsundry_name': '','billsundry_amount': '' }});  
				   }
			    }
            });
           
        }
    
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
                // console.log(response);
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
					var vchrtxnid = response.vchrtxnid;
					var is_grpid = $('select[name="party_id"] option:selected').data('grpid');
					if($('#EINVOICECheck').is(":checked")){
					alert_eway_timer("Voucher saved.<br> Please wait while generating E-Invoice.",2000);
					show_loader();
					var ajaxresonse=  $.ajax({
						url: baseurl+"/admin/etaxapis/generate_einvoice", 
						type: 'POST',
						data: {vch_txn_id:vchrtxnid,token: "<?php echo date('ssYssHs');?>"},
						dataType: "json",
						beforeSend: function() {
							// show_loader();
						},
						success: function (response) {
							
							 stop_loader();
						     if (typeof response === 'string') {
							  response = JSON.parse(response);
							  }  
							 if(response.status){
								// eway success nd move to new page  to show eway button pdf and eway number
							   alert_success("E-Invoice Bill Generated<br>E-Invoice AckNo No: "+response.AckNo);
							   setTimeout(function () {
								  window.location.reload();
								}, 3000);
							 
							 }
							 else{
								if(response.errors)
									{
										var list = ``;
										$.each(response.errors, function(index, value){
											list += `<li>${value}</li>`;
										});

										var html = `<ul style="list-style: none;">${list}</ul>`;
										
									} 
									Swal.fire({
										html: html,
										icon: "warning",
									    showCancelButton: true,
									    confirmButtonText: "Edit Voucher",
									    cancelButtonText: "Cancel",
									  }).then((result) => {
									  if (result.isConfirmed) {										  
										  window.location.href="<?php echo base_url(); ?>/admin/sales/edit/"+vchrtxnid;
									    }
									  })
							  }	
						},
						complete: function() {
							// stop_loader();
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
						   
						},
					  });
					
					
					return false;
					}
								
					else{
						stop_loader();
                    alert_success(response.message);
                    <?php if(isset($_GET['p']) && $_GET['p'] == 1){ ?>
                        window.history.back();
                    <?php } else { ?>
                        window.location.reload();
                    <?php } ?>
					}
					
					
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

 $(function () {
        var colModel = [
            { title: "PARTICULARS",sortable:false, dataIndx: "account_name", width: 100,dataType: "string",cls: 'pq-drop-icon pq-side-icon',
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
                    if(typeof rd.acc_id !== "undefined" && rd.acc_id != '' && rd.account_name != '')
                    {      var account_balance = rd.account_balance;
                        if(typeof account_balance !=="undefined"){
                     grid.addClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'pq-cell-red-tr pq-has-tooltip' });
                   	rd.pq_cellattr ={
								"account_name" : { "title":account_balance }
							};
							return ui.cellData;
                    }
                    else 
                    return ui.cellData;
                    }
                }     
            },
            { title: "DESCRIPTION", sortable:false,width: 100, dataType: "string",editable: function (ui) {
                   var account_id = ui.rowData['acc_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                }, dataIndx: "description"},
				
			{ title: "AMOUNT(₹)", width: 20,sortable:false, align: "right",dataIndx: "amounttxs",hidden:true,
              	editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
							
							if($('#taxInclusive').is(":checked")){
								if(bo_gstin_type=="1"){ // means Regular
									var itmrate = rd.igst_rate;
								   } 
								   else if(bo_gstin_type=="2"){ // means Composition
									if((rd.tax_exempted=='n') && (rd.supply_type=="1" || rd.supply_type=="3"))
									 var itmrate = goods_rate;
									 else if(rd.tax_exempted=='n' && rd.supply_type=="2")
									 var itmrate = services_rate;
									 else
										 var itmrate =0; 
									  }
								
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 var totalprice = parseAmount(rd.amounttxs);
								 var taxableamount = parseAmount(totalprice/y);
								
								 rd.amount = taxableamount;
						 
							 }else{
								 rd.amount = parseAmount(rd.amounttxs);
								 
							 } 
							 grid.refreshDataAndView();
						
                        })
                    }
                },
				render: function( ui ) {
				    var rd = ui.rowData;
					if(rd.amounttxs  >0){
                        return formatAmount(parseAmount(rd.amounttxs));   
                    }
                    return '';
                }
              },	
           { title: "AMOUNT(₹)", width: 20,sortable:false, align: "right",dataIndx: "amountfc",hidden:true,dataType: "float",
           editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                              if(rd.amountfc != '' && parseAmount(rd.amountfc) >= 0)
                			  {
                				  
                                                var amount =parseAmount(rd.amountfc);
                							
                								
                						       if(parseInt(currency_id)>1){
                								 
                								  if(parseFloat(fcy_forex_rate)!=0)
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
						
					 
                        });
						
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.amountfc >0){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						 rd.amountfc =parseAmount(rd.amountfc);
					
							return formatAmount(parseAmount(rd.amountfc),currency_symbol);   
						
                    }
                    return '';
                }
              }, 
            { title: "AMOUNT(₹)", width: 20, sortable:false,align: "right",editable: function (ui) {
                   var account_id = ui.rowData['acc_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                },dataIndx: "amount",dataType: "float",
                  validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                  render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.amount  >0){
                        rd.amount = parseAmount(rd.amount);
						return formatAmount(rd.amount); 
						
                    }
                    return '';
                },
            },
			
			{ title: "MEMO(₹)", width: 20,sortable:false, align: "right",dataIndx: "memo_amt",hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.memo_amt >0){
						rd.memo_amt = parseAmount(rd.memo_amt);
						return formatAmount(parseAmount(rd.memo_amt));
                    }
                    return '';
                }
              },	
        ];
            
        var dataModel = {"data":<?php echo json_encode($json_data);?>};
        var is_editable  = <?php echo $is_voucher_editable;?>;
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            change: calculateSummary, 
            dataReady: calculateSummary,
            columnTemplate: { render: commentRender },
            colModel: colModel,  
            numberCell: { show: true },
            wrap:false,
            editable: is_editable,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
            this.widget().pqTooltip();
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };

        newObj.cellKeyDown = function(evt, ui) {
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                });
                this.refreshDataAndView();
				
                
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
                

        
        var billsundry_colModel = [
             { title: "BILL SUNDRY", width: 100, dataType: "string", align: "left",dataIndx: "billsundry_name" ,
                editor: {                   
                          type: "textbox",
                          init: billsundry_autoComplete,
                          options: []
                      }
              },
			  
			  { title: "AMOUNT(₹)", width: 100,sortable:false, align: "right",dataIndx: "billsundry_amountfc",hidden:true,dataType: "float",
           editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
                              if(rd.billsundry_amountfc != '' && parseAmount(rd.billsundry_amountfc) >= 0) {
                                    var amount =parseAmount(rd.billsundry_amountfc);
                					 if(parseInt(currency_id)>1){
                						if(parseFloat(fcy_forex_rate)!=0)
                							rd.billsundry_amount   = parseAmount((rd.billsundry_amountfc)/fcy_forex_rate);	
                						 else
                							rd.billsundry_amount   = parseAmount(0); 
                						}
                						else{
                						rd.billsundry_amountfc = 0;
                						}
                                }else
									rd.billsundry_amountfc = 0;
						
			            	grid.refreshDataAndView();
						
                        })
						
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_amountfc >0){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						 rd.billsundry_amountfc =parseAmount(rd.billsundry_amountfc);
					
							return formatAmount(parseAmount(rd.billsundry_amountfc),currency_symbol);   
						
                    }
                    return '';
                }
              }, 
            { title: "AMOUNT(₹)", width: 100, sortable:false,align: "right",editable: function (ui) {
                   var billsundry_id = ui.rowData['billsundry_id'];
                    if (billsundry_id != '') {
                        return true;
                    }
                    return false;
                },dataIndx: "billsundry_amount",dataType: "float",
                  validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                  render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_amount  >0){
                        rd.billsundry_amount = parseAmount(rd.billsundry_amount);
						return formatAmount(rd.billsundry_amount); 
						
                    }
                    return '';
                },editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd     = ui.rowData;
                        var grid   = this;
                        var $inp   = ui.$cell.find("input");
						                    
                        $inp.on("change", function (evt) {
						
                        })
                    }
                },
            },
			  
			
			{ title: "MEMO(₹)", width: 100, align: "right", dataIndx: "billsundry_memoamnt" ,hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.billsundry_memoamnt >0){
						rd.billsundry_memoamnt=parseAmount(rd.billsundry_memoamnt);
						 return formatAmount(parseAmount(rd.billsundry_memoamnt));   
						
                    }
					
                    return '';
                }
				
            },
        ];
          
        var bsd_newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 400,
            selectionModel: { type: 'row' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
              pageModel: { type: 'local' },
            numberCell: { show: true },
            wrap:false,
            change: billsundry_calculateSummary,
            dataReady: billsundry_calculateSummary,
            editable: true,
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            cellSave: function(evt, ui){
                   this.refresh();
               },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: false });
                }
        };
         bsd_newObj2.cellKeyDown = function(evt, ui) {
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                });
                this.refreshDataAndView();
				
				item_bsd_totals();
                
           }
        } 
         $("#billsundry_search").pqGrid(bsd_newObj2); 
		 
		 $("#save_forex_rates").on("click",function(){
            	var currency_id = $('select[name="currency_id"] option:selected').val();	
            	var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
            	if(currency_id >1 && fcy_forex_rate >0 ){
            			
            			 var data = $('#grid_search').pqGrid('option', 'dataModel.data');	
            			 $.each(data, function(index,obj){
            					if(typeof obj.acc_id !== "undefined" && obj.acc_id != '')
            						{
            						  
            						  var cr_amount   = parseAmount((obj.amountfc)/fcy_forex_rate);
            						 
            						  data[index]['amount']    = cr_amount;
            					 	  
            						  }
            					}); 
            						
            			 }			 
            			 $('#grid_search').pqGrid('option', 'dataModel.data', data);
                         $('#grid_search').pqGrid('refreshDataAndView');						
            		
            	
            		 $("#view_fcrates_modal").modal("hide");
		    });
	
		 $("#fcratespopup").on("click",function(){
			 var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			 var currency_id    = $('select[name="currency_id"] option:selected').val();
			 calculate_fc_rates(currency_id,billsundry_colModel,colModel,currency_symbol);  
			  
		  });
		  
      	   var currencysymbol = $('select[name="currency_id"] option:selected').attr('data-id');
		   var currency_idval = $('select[name="currency_id"] option:selected').val();
			if(currency_idval >1)	{		
			calculate_fc_rates(currency_idval,billsundry_colModel,colModel,currencysymbol,'1');
			reload_fc_rates(currency_idval);
			}	  
	 
	 if(is_taxinc_enabled=="1"){
				reload_taxinclusive();
		    }
	
	if(is_memo_enabled=="1")
			memoChanged(billsundry_colModel,colModel,true);
	
           
	 function reload_fc_rates(currency_idval){
				if(currency_idval=="1"){
			      var data = $('#grid_search').pqGrid('option', 'dataModel.data');	
			      $.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined )
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
						 if(is_taxinc_enabled=="1"){
							var itmrate = igst_rate_val;
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 var totalprice = parseAmount(obj.amounttxs);
								 var taxableamount = parseAmount(totalprice/y);
								 var tax_amt = parseAmount(taxableamount);	
								
								data[index]['amounttxs'] = parseAmount(obj.amounttxs);
								data[index]['amount']    = parseAmount(tax_amt); 
						      } else{							  
								data[index]['amount']    = parseAmount(obj.amount);
								data[index]['amounttxs'] = parseAmount(0);
						     }
						  }
					});
			    $('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');	
			   item_bsd_totals();
			}
			
			if(currency_idval >1 && $('#taxInclusive').is(":checked")){				 
				alert_notification("Tax Inclusive can not applied on foreign  currency!");
				$("#currency_id").val("1");
				return false;
			 }else{
			  var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			  calculate_fc_rates(currency_idval,billsundry_colModel,colModel,currency_symbol,"1");
			  item_bsd_totals();
			 }
			}
			
	  $(document).on("change","#currency_id",function(){			
			if($(this).val()==1){
			 var data = $('#grid_search').pqGrid('option', 'dataModel.data');	
			 $.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined )
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
						 if($('#taxInclusive').is(":checked")){
							var itmrate = igst_rate_val;
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 var totalprice = parseAmount(obj.amounttxs);
								 var taxableamount = parseAmount(totalprice/y);
								var tax_amt = parseAmount(taxableamount);
						
								
								data[index]['amounttxs'] = parseAmount(obj.amounttxs);
								data[index]['amount']    = parseAmount(tax_amt); 
						    } else{
							   
								data[index]['amount']    = parseAmount(obj.amount);
								data[index]['amounttxs'] = parseAmount(0); 
						     }
						  }
					});	
				
			$('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');	
			
				
				 var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			   calculate_fc_rates($(this).val(),billsundry_colModel,colModel,currency_symbol);
			 item_bsd_totals();
			  
			}			
			else if($(this).val() >1 && $('#taxInclusive').is(":checked")){				 
				alert_notification("Tax Inclusive can not applied on foreign  currency!");
				$(this).val("1");
				return false;
			 }else{
			   var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			   calculate_fc_rates($(this).val(),billsundry_colModel,colModel,currency_symbol);
			  item_bsd_totals();
			  }
			});
		 $(document).on("change","#memoCheck",function(){  // on change of state
		     if(this.checked) // if changed state is "CHECKED"
			  {
				memoChanged(billsundry_colModel,colModel,true);
			  }
			  else{
				memoChanged(billsundry_colModel,colModel,false);
			  }
		});
		
		function reload_taxinclusive(){				
				var data = $('#grid_search').pqGrid('option', 'dataModel.data');
				var voucherDate = $("#voucher_date").val();
				TaxInsChanged(billsundry_colModel,colModel,true);
				
				$.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined)
						{
						     						 
							 data[index]['amounttxs'] = parseAmount(obj.amounttxs);
							 data[index]['amount'] = parseAmount(obj.amount);
						  }
					});
			
				$('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');
			  
			    item_bsd_totals(); 
			} 
			
			
			
			
			
		$(document).on("change","#taxInclusive",function() { 
		 
		     if(this.checked) // if changed state is "CHECKED"
			  {				  
				if($('select[name="currency_id"] option:selected').val()>1){
					alert_notification("Tax Inclusive can not applied on foreign  currency!");
					$(this).prop("checked",false);
					return false;
				}  else{
				TaxInsChanged(billsundry_colModel,colModel,true);	
				
				var data = $('#grid_search').pqGrid('option', 'dataModel.data');
				
				$.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined)
						{
							if(bo_gstin_type=="1"){ // means Regular
							var tax_details = obj.tax_details;
							var igst_rate_val = tax_details.igst;
						}
						else if(bo_gstin_type=="2"){ // means Composition
							if((obj.tax_exempted=='n') && (obj.supply_type=="1" || obj.supply_type=="3"))
							 var igst_rate_val = goods_rate;
							 else if(obj.tax_exempted=='n' && obj.supply_type=="2")
							 var igst_rate_val = services_rate;
							 else
								 var igst_rate_val =0; 
						  }
							data[index]['amounttxs'] = parseAmount(obj.amount);	
							 var itmrate = igst_rate_val;
							 var x= parseAmount((itmrate/100));
							 var y = parseAmount(x)+parseAmount(1);
							 var totalprice = parseAmount(obj.amounttxs);
							 var taxableamount = parseAmount(totalprice/y);
							 var tax_amt = parseAmount(taxableamount);						 
							 data[index]['amounttxs'] = parseAmount(obj.amounttxs);
							 data[index]['amount'] = parseAmount(tax_amt);
						  }
					});
				}
				$('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');
			  }
			  else{
				TaxInsChanged(billsundry_colModel,colModel,false);
				
				var data = $('#grid_search').pqGrid('option', 'dataModel.data');
				$.each(data, function(index,obj){
					if(obj.acc_id != '' && obj.acc_id != undefined )
						{
						data[index]['amount'] = parseAmount(obj.amounttxs);
						data[index]['amounttxs'] = parseAmount(0);
							
				
						  }
					});
					
					$('#grid_search').pqGrid('option', 'dataModel.data', data);
                    $('#grid_search').pqGrid('refreshDataAndView');
			  }			
			item_bsd_totals(); 
		})	
		
		
    });
    
    
     
     $(document).on('change', '[name="type"]', function(e){
         var type = $(this).val();
         e.preventDefault();
         if(type == 'with_item'){
             $(this).val('without_item');
             window.location.href = "<?php echo $base_url; ?>sales/item";
         }
         else if(type == 'without_item'){
             window.location.href = "<?php echo $base_url; ?>sales/non_item";
         }
        
     })
/***************  Start Bill By Bill *************************/
var saved_bill_txns = <?= json_encode($bbb_data) ?>;
  var hasBills = Object.keys(saved_bill_txns).some(function(key) {
  if (key !== "acc_id" && saved_bill_txns[key].bills_txn_list?.length > 0) {
    return true;
  }
  return false;
});

 <?php if($istaxinc){ ?>
 var is_taxinc_enabled="1";
 $('#taxInclusive').prop('checked', true);
 <?php } else{?>
 var is_taxinc_enabled="0";
 <?php } ?>
 
 <?php if($ismemosale){ ?>
 var is_memo_enabled="1";
 $('#memoCheck').prop('checked', true);
 <?php } else{?>
 var is_memo_enabled="0";
 <?php } ?>
 
    if(hasBills){
     $('#bbbCheck').prop('checked', true);
    }  
    var billIndex = 0;
    var bbb_accounts=[];
	
    function readyBills(total_amount,total_amountfc){
		billIndex    = 0;
		bbb_accounts = [];
		bbb_data     = [];
		var account_id = $('select[name="party_id"]').val();
        var account_name = $('select[name="party_id"] option:selected').text();
		bbb_accounts.push({
			'account_id'    : account_id,
			'account_name'  : account_name,
			'drcr'          : 'D',
			'amount'        : total_amount,
			'amountfc'      : total_amountfc,
			'grid'          : generateBillData(account_id)
		});	
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
        var final_amount_fc = parseAmount(obj.amountfc || 0);
        var final_drcr = obj.drcr;

        // Normalize final amount
        var final_total = (final_drcr === 'D') ? final_amount : -final_amount;
        var final_total_fc = (final_drcr === 'D') ? final_amount_fc : -final_amount_fc;

        var total = 0;
        var total_fc = 0;

        var hasValidRow = false;

        for (var j = 0; j < obj.grid.length; j++) {

            var row = obj.grid[j];

            var method = row.method;
            var reference = row.reference;

            if (method !== '' && reference !== '') {

                var amount = parseAmount(row.amount);
                var amount_fc = parseAmount(row.amountfc || 0);
                var drcr = row.drcr;

                // Normalize row values
                var sub = (drcr === 'D') ? amount : -amount;
                var sub_fc = (drcr === 'D') ? amount_fc : -amount_fc;

                total += sub;
                total_fc += sub_fc;

                hasValidRow = true;
            }
        }

        if (hasValidRow) {

            // INR validation
            if (parseFloat(total.toFixed(2)) !== parseFloat(final_total.toFixed(2))) {
                $('#bill_warning').text('Bill total mismatch (INR).');
                return false;
            }


            isAnyGridValid = true;
        }
    }

    if (!isAnyGridValid) {
        $('#bill_warning').text('Please fill at least one bill grid.');
        return false;
    }

    return true;
}	


    function validateBills() {

    var sum = 0;
    var sum_fc = 0;

    var data = $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data');

    var error = 0;
    var count = 0;

    for (var i = 0; i < data.length; i++) {

        var row = data[i];

        var method    = row.method;
        var reference = row.reference;
        var amount    = parseAmount(row.amount);
        var amount_fc = parseAmount(row.amountfc || 0);
        var drcr      = row.drcr;

        if (method !== '' && reference !== '') {

            count++;

            if (!drcr || amount <= 0) {
                error = 1;
            }

            // INR
            var sub = (drcr === 'D') ? amount : -amount;
            sum += sub;

            // FC
            var sub_fc = (drcr === 'D') ? amount_fc : -amount_fc;
            sum_fc += sub_fc;
        }
    }

    if (count === 0) {
        $('#bill_warning').text("Please fill at least one bill row.");
        return false;
    }

    if (error) {
        $('#bill_warning').text("Kindly fill the details correctly !!!");
        return false;
    }

    // Final voucher amount
    var final_amount = parseAmount(bbb_accounts[billIndex].amount);
    var final_amount_fc = parseAmount(bbb_accounts[billIndex].amountfc || 0);
    var final_drcr = bbb_accounts[billIndex].drcr;

    // Normalize
    var final_total = (final_drcr === 'D') ? final_amount : -final_amount;
    var final_total_fc = (final_drcr === 'D') ? final_amount_fc : -final_amount_fc;

    // 🔥 IMPORTANT: Allow rounding tolerance (0.01)
    var diff = Math.abs(sum - final_total);
    var diff_fc = Math.abs(sum_fc - final_total_fc);

    if (diff > 0.01) {
        $('#bill_warning').text("Total mismatch (INR). You can split amounts but total must match.");
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
            if($("#currency_id").val() >1)
              $('#bills_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	       else 
		      $('#bills_total').html(formatAmount(amount));	     
            $('#bills_drcr').text(drcr + 'r');
            $('#bills_page').text(page);
            $('#billsModal').modal('show');			
			if($("#currency_id").val() >1){
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
                    json.push({'method': 'Adjustment', 'reference': obj.bill_ref_name, 'reference_id': obj.bill_ref_id, 'amountfc': obj.bill_txn_fcy,'amount': obj.bill_txn_amt, 'drcr': obj.bill_txn_dr_cr,
                              'due_date': obj.bill_due_date, 'narration': obj.bill_txn_narr});
                });
            }
        }
        
        for(var i=0;i<500;i++){
            json.push({'method': '', 'reference': '', 'reference_id': '', 'amountfc': '', 'amount': '', 'drcr': '', 'due_date': '', 'narration': ''});
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
            sub = 0,
			subfc=0,
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
                				    if(parseInt(currency_id)>1){
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

		if($("#currency_id").val() >1){
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
			 bill_colModel[2].hidden = true;
			 bill_colModel[2].width  = 150;
			 bill_colModel[2].title  = 'AMOUNT('+currency_symbol+')';  
			if($("#bill_by_bill_grid").pqGrid('instance')){ 
			 $("#bill_by_bill_grid").pqGrid( "option", "colModel", bill_colModel );             
			 $("#bill_by_bill_grid").pqGrid( {autoFit: true} );
			 $("#bill_by_bill_grid").pqGrid("refreshCM");
			 $("#bill_by_bill_grid").pqGrid("refresh");
			}
		  }
		  
/***********  End of Bill By Bill **********************/	

/************ Start of Project Reporting  ************************/	 

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
            if($("#currency_id").val() >1)
            $('#pr_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	      else 
		   $('#pr_total').html(formatAmount(amount));
            $('#pr_drcr').text(drcr + 'r');
            $('#pr_page').text(page);

            $('#prModal').modal('show');
			if($("#currency_id").val() >1){
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
        
        for(var i=0;i<500;i++){
            json.push({'project_id': '', 'project_name': '','proj_txn_amtfc': '', 'proj_txn_amt': '', 'proj_txn_drcr': '', 'proj_txn_narr': ''});
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
            sub = 0,
			subfc=0,
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
                				    if(parseInt(currency_id)>1){
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


/************** End of Project Reporting *************************/


/************ Start of Cost Center  ************************/	 
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
          
          
          if(!oCheck && $('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
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
                  var cc_id         = obj2.cc_id;
                  var cc_name       = obj2.cc_name;
                  var cc_txn_amt    = obj2.cc_txn_amt;
				  var cc_txn_amtfc  = obj2.cc_txn_amtfc;
                  var cc_txn_drcr   = obj2.cc_txn_drcr;
                  var cc_txn_narr   = obj2.cc_txn_narr;
              
                  cc_data.push({
                      "cc_id"       : cc_id,
                      "account_id"  : account_id,
                      "acc_type"    : acc_type,
                      "cc_name"     : cc_name,
                      "cc_txn_amt"  : parseAmount(cc_txn_amt),
					  "cc_txn_amtfc"  : parseAmount(cc_txn_amtfc),
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
              var cc_txn_drcr     = data[i]['cc_txn_drcr'];
              
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
         $('#cc_warning').text("Kindly fill the details correctly !!!");  
          return false;
      }
      var final_amount = cc_accounts[ccIndex].amount;; 
      var final_drcr = cc_accounts[ccIndex].drcr;
      
      if(final_drcr == 'C'){
          final_amount = -final_amount;
      }
      
      if(sum != final_amount && count > 0){
         $('#cc_warning').text('Total Mismatch');
          return false;
      }
      
      return true;
  }
     
  var cc_list = [];
  function readyCc()
  {
      $.ajax({
          type: "POST",
          url: "<?php echo $base_url; ?>vouchers/getCc",
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
		  var amountfc     = cc_accounts[ccIndex].amountfc;
          var drcr = cc_accounts[ccIndex].drcr;
          var page = (ccIndex + 1) + '/' + cc_accounts.length;
		  var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
          
          $('#cc_account').text(account_name);
         if($("#currency_id").val() >1)
            $('#cc_total').html(formatAmount(amountfc,currency_symbol)+ " &nbsp; "+formatAmount(amount));
	        else 
		     $('#cc_total').html(formatAmount(amount));
          $('#cc_drcr').text(drcr + 'r');
          $('#cc_page').text(page);
          

          $('#ccModal').modal('show');
          if($("#currency_id").val() >1){
			cc_colModel[1].hidden=false;
			cc_colModel[1].width= 150;
			cc_colModel[1].title='AMOUNT('+currency_symbol+')';
			$("#cc_grid").pqGrid( "option", "colModel", cc_colModel );             
			$("#cc_grid").pqGrid( {autoFit: true} );
			$("#cc_grid").pqGrid("refreshCM");
			$("#cc_grid").pqGrid("refresh");				
		    }
			else{
			 cc_colModel[1].hidden=true;
			 cc_colModel[1].width= 150;
			 cc_colModel[1].title='AMOUNT('+currency_symbol+')';  
			 $("#cc_grid").pqGrid( "option", "colModel", cc_colModel );             
			 $("#cc_grid").pqGrid( {autoFit: true} );
			 $("#cc_grid").pqGrid("refreshCM");
			 $("#cc_grid").pqGrid("refresh");
			} 
          $("#cc_grid").pqGrid('option', 'dataModel.data', cc_accounts[ccIndex].grid);
          $("#cc_grid").pqGrid('refreshDataAndView');

          $('input[type="command-line"]').focus();//tempararily shift focus
          $("#cc_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 3, focus: true });
      }   
  }
              
              
  function generateCcData(acc_id = 0,acc_type = '')
  {
      var json = [];

      if(acc_id != 0 ){
          var i = saved_cc_txns.findIndex(function(o) {
             return o.acc_id == acc_id;
          });
          if(i >= 0){
             $.each(saved_cc_txns[i].cc_txn_list, function(index, obj){
                  json.push({'cc_name': obj.cc_name,'cc_id': obj.cc_id,'cc_txn_amtfc': obj.cc_txn_fcy,  'cc_txn_amt': obj.cc_txn_amt, 'cc_txn_drcr': obj.cc_txn_dr_cr, 'cc_txn_narr': obj.cc_txn_narr});
              });
          }
      }
      
      for(var i=0;i<500;i++){
          json.push({'cc_id': '', 'cc_name': '','cc_txn_amtfc':'', 'cc_txn_amt': '', 'cc_txn_drcr': '', 'cc_txn_narr': ''});
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
          amount = master_amount;
      if(master_drcr == 'C')
          amount = -master_amount;

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
	      amountfcTotal=0,
          sub = 0,
		  subfc=0,
          data = this.option('dataModel.data');
      
      data.forEach(function(row){
          
          if(row.cc_id != '' && row.cc_txn_amt != '' && row.cc_txn_drcr != '')
          {
              sub = 0;
			  subfc=0;
              if(row.cc_txn_drcr == 'D'){
                  sub = parseAmount(row.cc_txn_amt);
				  subfc = parseAmount(row.cc_txn_amtfc);
              }
              if(row.cc_txn_drcr == 'C'){
                  sub = -parseAmount(row.cc_txn_amt);
				  subfc = parseAmount(row.cc_txn_amtfc);
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
		  cc_txn_amtfc  : parseAmount(amountfcTotal),
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
	  { title: "AMOUNT(₹)", sortable:false,width: 100, align: "right",hidden:true,dataIndx: "cc_txn_amtfc",dataType: "float",
                editable: function (ui) {
				 var cc_id = ui.rowData['cc_id'];
				  if (cc_id != '') {
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
                                if(rd.cc_txn_amtfc != '' && parseAmount(rd.cc_txn_amtfc) >= 0)
                			    {
                				    if(parseInt(currency_id)>1){
                					  if(parseFloat(fcy_forex_rate)!=0 && parseFloat(fcy_forex_rate) >0)
                					    rd.cc_txn_amt   = parseAmount((rd.cc_txn_amtfc)/fcy_forex_rate);	
                				     else
                				     	rd.cc_txn_amt   = parseAmount(0);                 								 
                				     }
                				   else{
                					  rd.cc_txn_amtfc = 0;
                					}	
                                }else
									rd.cc_txn_amtfc = 0;
							grid.refreshDataAndView();
                        })
                    }
                },
				
				render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
					if(rd.cc_txn_amtfc >0 && rd.cc_txn_amtfc !=''){
					return formatAmount(parseAmount(rd.cc_txn_amtfc),currency_symbol);
					}
                }
				
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
      pageModel: { type: 'local', rPP: 5  },
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
  var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
				
	  if($("#currency_id").val() >1){
			cc_colModel[1].hidden=false;
			cc_colModel[1].width= 150;
			cc_colModel[1].title='AMOUNT('+currency_symbol+')';
			if($("#cc_grid").pqGrid('instance')){   
			$("#cc_grid").pqGrid( "option", "colModel", cc_colModel );             
			$("#cc_grid").pqGrid( {autoFit: true} );
			$("#cc_grid").pqGrid("refreshCM");
			$("#cc_grid").pqGrid("refresh");
			}
					
	  }
	  else{
			cc_colModel[1].hidden=true;
			cc_colModel[1].width= 150;
			cc_colModel[1].title='AMOUNT('+currency_symbol+')';  
			if($("#cc_grid").pqGrid('instance')){ 
			$("#cc_grid").pqGrid( "option", "colModel", cc_colModel );             
			$("#cc_grid").pqGrid( {autoFit: true} );
			$("#cc_grid").pqGrid("refreshCM");
			$("#cc_grid").pqGrid("refresh");
			}
	  }




/************ End of Cost Center  ************************/	 
 </script>
</body>
</html>
