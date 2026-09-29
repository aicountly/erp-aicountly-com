<?php $header = array( 	'title' => 'Add Purchase Order' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
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

<div class="row pb-2">
  <div class="col-sm-6 order-1"><h3>Purchase Order</h3></div>  
  <div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
    <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
	<a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
      <a href="#"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">download</span></a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
      <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Facebook</a></li>
    <li><a class="dropdown-item" href="#">Twitter</a></li>
    <li><a class="dropdown-item" href="#">Instagram</a></li>
  </ul>
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-8 order-2 order-md-3">
    <select name="type" id="type" class="form-select d-inline-block" style="width:120px;">
        <option value="item" selected="selected">With Item</option>
        <option value="non_item">Without Item</option>
    </select>
 <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="itmtrackingCheck">
    <label class="form-check-label" for="itmtrackingCheck">Track Item</label>
   </div>
   
   
  <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="itmbtchCheck">
    <label class="form-check-label" for="itmbtchCheck">Item Batch</label>
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
	 <div class="form-check form-check-inline me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="taxInclusive" id="taxInclusive">
      <label class="form-check-label" for="taxInclusive">Tax Inclusive</label>
    </div>
	 <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="memoCheck" id="memoCheck">
      <label class="form-check-label" for="memoCheck">Memorandum</label>
    </div>
	
	<div class="row">
    <div class="col-md-3">
	<div class="input-group">
    <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:120px;">
      <?php foreach ($currency_list as $value) { ?>
      <option data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>">
      <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-primary">#</div>
	</div>
	</div></div>	
 </div> 
  <div class="col-md-4 text-md-end order-4 collapse listmenu" id="listmenu">
    <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
      <li> <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);">Apply Tax</a></li>
    </ul> 

    <a href="#" class="btn btn-success">Party Dashboard</a>
    <button class="btn btn-success m-1" type="button">Templates</button>
    <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
  </div> 
</div>

<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
 <div class="col-12">
  <div class="row m-0 p-0 align-items-center">
      
    
    <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Voucher No:</label>
           <input type="text" name="voucher" id="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled>
       </div>
    </div>
	  <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Bill No:</label>
          <?php if($voucher_bill_no!=''){ ?>
          <input type="text" name="billno" id="billno" value="<?php echo $voucher_bill_no;?>" class="form-control form-control-sm" disabled>
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
          echo form_dropdown('reverse_charges', $revrchrgs_lists, "0",' id="reverse_charges" class="sale_type form-select" '); ?>
         
        </div>
      </div>
	  
   <div class="col-md-2 col-6 card p-2">
    <div class="input-group">
     <span class="input-group-text">Series:</span>
     <select name="voucher_series" id="voucher_series" class="voucher_series form-select required" required>
			<?php
			foreach($voucher_series_dropdown as $vs_row){ ?>
               <option id="<?= $vs_row['comp_vch_series_id']?>" data-srsmethod="<?= $vs_row['comp_vch_method'] ?>"  value="<?= $vs_row['comp_vch_series_id'] ?>" <?php echo ($vs_row['comp_vch_series_id']=='11')?'selected':''?>><?= $vs_row['comp_vch_series'] ?></option>
            <?php } ?>
		  </select>
    </div><input type="hidden" name="unitdetail_array" id="unitdetail_array" value="">
   </div>
   
   <div class="col-md-2 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Date:</label> 
           <input type="text"  id="voucher_date" name="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm required">
       </div><input type="hidden" name="batchdata" id="batchdata" value="">
    </div>  
      
    <div class="col-md-2 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">Auto Price:</span>
            <?php 
			$lastprice_dropdown = array(''=>'','1'=>'MRP Based','2'=>'Last Price');
			echo form_dropdown('last_price', $lastprice_dropdown, '',' id="last_price" class="sale_type form-select" '); ?>
          </div>
        </div>
   <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Transporter:</label>
          <span id="transporter_div">
          <?php 
          $transporter_lists=array(""=>"Choose","0"=>"Add New");
          echo form_dropdown('transporter', $transporter_dropdown, "",' id="transporter" class="sale_type form-select" '); ?>
          </span>
		  
        </div>
      </div>
	  <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Party:</label> 
          <input list="party_ids" id="clone_party_id" name="clone_party_id" class="form-control form-control-sm required" required>
		  <datalist id="party_ids">
		    <?php
			foreach($party_dropdown as $value){ ?>
               <option data-dlrtype="<?php echo $value['dealer_type']?>" data-gstin="<?php echo $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_name'] ?>" >
            <?php } ?>
		  </datalist>
        </div>
    </div>
    <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">POS:</label>
          <?php echo form_dropdown('pos', $states_lists, "",' id="pos" class="sale_type form-select" '); ?>
        </div>
      </div>

   
   <input type="hidden" name="batchinfo_array" id="batchinfo_array" value="">
   
   
    
    <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
            <label class="input-group-text">MC:</label> 
            <?php echo form_dropdown('matrcntr_id', $matrcntr_dropdown, "1",' id="matrcntr_id" class="form-select required" '); ?>
        </div>
    </div>
     <div class="col-md-4 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Invoice Type :</label><label class="input-group-text" id="invoice_type_label"></label></div>
      </div>
	  <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Ecomerce Operator:</label>
          <?php echo form_dropdown('eco_id', $eco_dropdown, "",' id="eco_id" class="form-select" '); ?>
        </div>
      </div>
       <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
              <label class="input-group-text">Due Date:</label>
              <input type="text" name="due_date" id="due_date" value="" class="datepicker form-control form-control-sm" required>
            </div>
        </div>
   <div class="col-md-2 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Order No:</label> 
                <input type="text" name="order_no" class="form-control form-control-sm">
            </div>
        </div>
      <div class="col-md-9 col-12 card p-2">
          <div class="input-group">
              <label class="input-group-text">Narration:</label> 
              <textarea  name="narration" class="form-control form-control-sm"></textarea>
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
        <input type="hidden" name="itmsdata" id="itmsdata">
        <input type="hidden" name="bbbdata" id="bbbdata">
        <input type="hidden" name="ccdata" id="ccdata">
        <input type="hidden" name="prdata" id="prdata">
		<input type="hidden" name="gst_paidacc_id" id="gst_paidacc_id">
		<input type="hidden" name="bsd_comp_applytax_json" id="bsd_comp_applytax_json" value="">
        <input type="hidden" name="billsndrydata" id="billsndrydata">
        <input type="hidden" name="taxsummarydata" id="taxsummarydata">
        <input type="hidden" name="trackinginfo_array" id="trackinginfo_array" value="">
		<select style="display:none;" name="inward_challan_id" id="inward_challan_id" class="form-select">
						<option></option>			
					  </select>
        
  </div>
 </div>


   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12">
	<div class="row">
        <!-- Tax Summary -->
        <div class="col-md-8">
           <h4 class="text-center">Tax Summary</h4>
           <div id="taxgrid_search" style="margin:auto;"></div>  
		  
         
        </div>
       <!-- Tax Summary Ends -->
      
       <!-- Bill Summary Starts -->
       <div class="col-md-4">
           <h4 class="text-center">Bill Sundry</h4>
           <div id="billsundry_search" style="margin:auto;"></div> 
       </div>

    </div> 
	 <div class="col-12 text-center">
         <br><br>
		 
		<h3>Invoice Total : <span id="invoice_total">0</span></h3>
	 </div>
    
     <div class="col-12 text-center">
         <br><br>
         <input type="file" class="">
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <button type="reset"  class="btn btn-success btn-lg">Reset</button>
        <a href="#" class="btn btn-secondary btn-lg">Quit</a>
        
    </div>
        
    </div>
   <div class="modal fade pt-5" id="add_transporter_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Transporter Details</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					   <table class="table" cellspacing="0">
					  <tr>
					  <th>Transporter Id</th>
					  <th>GSTIN Id</th>
					  <th>Transporter Name</th>
					  <th>Transporter Doc No.</th>
					  <th>Transport Mode</th>
					  <th>Transport Distance</th>
					  <th>Transport Doc Date</th>
					  <th>Vehicle No</th>
					  <th>Vehicle Type</th>					  
					  </tr>
					  <tbody>
				 
					 
				  <tr>
					 <td><input type="text" name="transporter_id" id="transporter_id" value="" maxlength="255" class="form-control">
					</td>
					 <td><input type="text" name="gstin_id" id="gstin_id" value="" maxlength="255" class="form-control">
					</td>
					<td>
					<input type="text" name="transporter_name" value="" id="transporter_name" maxlength="255" class="form-control">
					</td>
					<td>
					<input type="text" name="transporter_doc_no" value="" id="transporter_doc_no" maxlength="255" class="form-control" >
					</td>
					<td>
					<?php 
					echo form_dropdown('transport_mode', $transport_modes, set_value('transport_mode'),'id="transport_mode" class="form-control w-75" ');
					?>
					</td>
					<td>
					<input type="text" name="transport_distance" id="transport_distance" value="" maxlength="255" class="form-control">
						</td>
					<td>
					<input type="text" name="transport_doc_date" id="transport_doc_date" value="" maxlength="255" class="form-control datepicker">
					</td>	
						
				<td>
				<input type="text" name="vehicle_no" id="vehicle_no" value="" maxlength="255" class="form-control">
						    
				<td><?php 
					echo form_dropdown('vehicle_type', $vehicle_types, set_value('vehicle_type'),'id="vehicle_type" class="form-control w-75" ');
					?></td>		</td>				
				
					</tr> 
					 </tbody>
					  <select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option  data-dlrtype="0" data-gstin="" data-is_bbb="0" value="">
						</option>			
					  </select>
					  
					  </table>
					  	<input type="hidden" name="invoice_type" id="invoice_type">
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="save_transport_detail">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
<div class="modal fade pt-5" id="update_transporter_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Transporter Details</h4>
                        <button type="button" class="btn-close close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					   <table class="table" cellspacing="0">
					  <tr>
					  <th>Transporter Doc No.</th>
					  <th>Transport Mode</th>
					  <th>Transport Distance</th>
					  <th>Transport Doc Date</th>
					  <th>Vehicle No</th>
					  <th>Vehicle Type</th>					  
					  </tr>
					  <tbody>			 
					 
				  <tr>
					<td>
					<input type="text" name="transporter_doc_no" value="" id="transporter_doc_no" maxlength="255" class="form-control" >
					</td>
					<td>
					<?php 
					echo form_dropdown('transport_mode', $transport_modes, set_value('transport_mode'),'id="transport_mode" class="form-control w-75" ');
					?>
					</td>
					<td>
					<input type="text" name="transport_distance" id="transport_distance" value="" maxlength="255" class="form-control">
						</td>
					<td>
					<input type="text" name="transport_doc_date" id="transport_doc_dates" value="" maxlength="255" class="form-control datepicker">
					</td>	
						
				<td>
				<input type="text" name="vehicle_no" id="vehicle_no" value="" maxlength="255" class="form-control">
						    
				<td><?php 
					echo form_dropdown('vehicle_type', $vehicle_types, set_value('vehicle_type'),'id="vehicle_type" class="form-control w-75" ');
					?></td>		</td>				
				
					</tr>
					 </tbody>
					  </table>
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" data-id="" data-bs-dismiss="modal">Save</button>
                      </div>
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
							<input type="text" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate"  onkeydown="return validatefcrate(event,this.value);" required>
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

<!-- The Modal -->
<div class="modal" id="billsModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Bill by Bill</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 text-center"><h4>Account: <span id="bills_account"></span></h4></div>
            <div class="col-md-6 text-center"><h4>Total: <span id="bills_total"></span>&nbsp;<span id="bills_drcr"></span></h4></div>
        </div>
        <div id="bill_by_bill_grid"></div>
        
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
        <h4 class="modal-title"><span id="cc_page"></span> Cost Centre</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
      </div>

      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_bill_by_bill">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>


<div class="modal" id="batchmodel" style="z-index: 9999">
                
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><span id="batch_page"></span> Item Batch Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">  
         <div class="row">
            <div class="col-md-3 text-center"><h4>Item: <span id="batch_item_name"></span></h4></div>
			 <div class="col-md-3 text-center"><h4>Qty: <span id="batch_item_qty"></span></h4></div>
			  <div class="col-md-3 text-center"><h4>Unit: <span id="batch_item_unit"></span></h4></div>
              <div class="col-md-3 text-center"><h4>Undefined: <span id="batch_item_balance"></span></h4></div>
            
        </div>	  
        <div id="item_batch_grid"></div>
		
		
		 <button  type="button" class="btn btn-primary prevBtn" onclick="readyBatchModel('prev')">Previous</button>
            <button type="button" class="btn btn-primary nextBtn" onclick="readyBatchModel('next')">Next</button>
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_batch">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>	


<div class="modal" id="trackingmodel" style="z-index: 9999">
                
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><span id="batch_page"></span> Item Tracking Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">  
         <div class="row">
            <div class="col-md-3 text-center"><h4>Item: <span id="tracking_item_name"></span></h4></div>
			 <div class="col-md-3 text-center"><h4>Qty: <span id="tracking_item_qty"></span></h4></div>
			  <div class="col-md-3 text-center"><h4>Unit: <span id="tracking_item_unit"></span></h4></div>
              <div class="col-md-3 text-center"><h4>Undefined: <span id="tracking_item_balance"></span></h4></div>
            
        </div>	  
        <div id="item_tracking_grid"></div>
		
		 <button  type="button" class="btn btn-primary prevBtn" onclick="readyTrackingModel('prev')">Previous</button>
            <button type="button" class="btn btn-primary nextBtn" onclick="readyTrackingModel('next')">Next</button>
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_tracking">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>	

<!-- List View The Modal Ends Here -->

<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=500;$i++)
    $tax_json_data[] =array("tax_item_id"=>"","id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'','total_tax'=>'');
?> 
<script>
function get_sundry_grid()
    {
        var data = []
        for(var i=1; i<=50; i++){
            data.push({"billsundry_id": '',"billsundry_name": '','billsundry_rate': '','billsundry_amount': '','billsundry_memoamnt':''});
        }
        return data;
    }
var billsundry_dataModel = {"data":get_sundry_grid()}

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
			  $("#pos").trigger('change');
			  show_taxsummary_items();
			  item_bsd_totals();
		    
	}else{	
	var opt      = $('#party_ids option[value="'+val+'"]');
	var opid     = (opt.attr('id'));
	var opstcode = (opt.data('statecode'));	
	var opgstin  = (opt.data('gstin'));
	var dlrtype  = (opt.data('dlrtype'));
	
	
	$("#pos").val(opstcode);	
	$("#grid_search").pqGrid("option", "editable", true);
	$("#billsundry_search").pqGrid("option", "editable", true);
	$("#party_id option").val(opt.attr('id'));
	$("#party_id option").attr("data-is_bbb",opt.attr('data-is_bbb'));
	$("#party_id option").attr("data-gstin",opgstin);	
	$("#party_id option").text($(this).val());	
	$("#party_id option").attr("data-dlrtype",dlrtype);	
	$('#party_id option[value="'+opt.attr('id')+'"]').prop('selected', true);
	invoice_type_changes();	
	$("#pos").trigger('change');
	show_taxsummary_items();
	item_bsd_totals();
	}     
});
    
});
var ugst_states       = ['35','04','26','25','31','38','34','97'];
var sdateFrom         = '<?php echo $fy_begndt;?>';
var bo_gstin_type     = '<?php echo $bo_gstin_type;?>';
var sdateTo           = '<?php echo $fy_end;?>';
var bsd_json_file     = <?php echo $bsd_json_file;?>;
var bo_state_code     = '<?php echo sprintf('%02d', $bo_state_code);?>';
var bst_notax         = <?php echo $bsd_json_file;?>;
var tax_json_data     = <?php echo json_encode($tax_json_data);?>;
var bills_method_list = <?php echo json_encode($bills_method_list);?>;
var unitslist         = <?php echo json_encode($units_list);?>;
var item_json_file    = <?php echo $item_json_file;?>;
</script>
<script src="<?php echo base_url();?>/public/js/purchase_with_item.js"></script>
<script src="<?php echo base_url();?>/public/js/purchase_with_item_grid.js"></script>
<style>
    .boldcell{font-weight:700;}
</style>

<script>
$("#voucher_date").on("change",function(){
	invoice_type_changes();
	var voucher_date     = $(this).val();	 
	var vouchermethod = $('select[name="voucher_series"] option:selected').attr('data-srsmethod');
	var series_id = $('select[name="voucher_series"] option:selected').val();
	if(vouchermethod=="1"){
       $.ajax({
            url: baseurl+"/admin/ajax/GetAutoBillNo", 
            type: 'POST',
            dataType: "json",
            data:{'voucher_type_id':'<?php echo $voucher_type_id;?>','series_id':series_id,'voucher_date':voucher_date} , 
			async: false,
            cache: false,
            beforeSend: function() {},
            success: function (response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
            	$('input[name="billno"]').val(response.billno);
				
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
			 })
		$('input[name="billno"]').prop("disabled",true);
 }
	
});

$("#voucher_series").on("change",function(){
 show_loader();	
 var series_id     = $(this).val();	
 var voucher_date  = $("#voucher_date").val();
 var vouchermethod = $('select[name="voucher_series"] option:selected').attr('data-srsmethod');
 if(vouchermethod=="1"){
       $.ajax({
            url: baseurl+"/admin/ajax/GetAutoBillNo", 
            type: 'POST',
            dataType: "json",
            data:{'voucher_type_id':'<?php echo $voucher_type_id;?>','series_id':series_id,'voucher_date':voucher_date} , 
			async: false,
            cache: false,
            beforeSend: function() {},
            success: function (response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
            	$('input[name="billno"]').val(response.billno);
				
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
			 })
 $('input[name="billno"]').prop("disabled",true);
 }else{
  $('input[name="billno"]').prop("disabled",false);	
  $('input[name="billno"]').val("");  
 }
stop_loader();
	
})	

$("#save_transport_detail").on("click",function(){	
	
	var transporter_id         = $("#transporter_id").val();
	var gstin_id               = $("#gstin_id").val();
	var transporter_name       = $("#transporter_name").val();
	var transporter_doc_no     = $("#transporter_doc_no").val();	
	var transport_mode         = $("#transport_mode").val();
	var transport_distance     = $("#transport_distance").val();	
	var transport_doc_date     = $("#transport_doc_date").val();
	var vehicle_no             = $("#vehicle_no").val();	
    var vehicle_type           = $("#vehicle_type").val();
	var state_code             = $('select[name="pos"] option:selected').val();
	if(transporter_id=='' || transporter_name=='' || transporter_doc_no==''){
	  alert("Kindly fill the form!!");	
	  return false;		
	}
	else{
		$(this).prop("disabled", true);
	    var frmdata ={"gstin_id":gstin_id,"transporter_id":transporter_id,"transporter_name":transporter_name,"transporter_doc_no":transporter_doc_no,"isedit":"0","transport_mode":transport_mode,"transport_distance":transport_distance,"transport_doc_date":transport_doc_date,"vehicle_no":vehicle_no,"vehicle_type":vehicle_type,"state_code":state_code};	
	    $.ajax({
		  type: "POST",
		  url: baseurl+"/admin/ajax/ajax_save_transporter",
		  data: frmdata,
		  success:function(response){		
			if(response=="0"){
				alert_notification("Transport name already defined!!!");
				return false;
			}else{ 
			   $("#transporter_div").html(response);			 
			   $("#save_transport_detail").prop("disabled", true);
			   $("#add_transporter_modal").modal('hide');
			  }
			  
		    },
		  error: function (jqXHR, exception){
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
 
 $(document).on("change","#transporter",function(){

     if($(this).val()=='0'){
       $("#add_transporter_modal").modal("show"); 
     }else{
		 var transporter = $(this).val(); 
		 if(transporter >0 ){
			  $("#update_transporter_modal").modal("show"); 
			
		 }
      }
  })
  
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
    { title: "BATCH NO", dataIndx: "batch_no", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
			init: batchno_editor
        }
    },
	$.extend( true, {title: "MANUFACTURING DATE", width: 100, dataIndx: "manufacturing_date" ,cls: 'pq-drop-icon pq-side-icon',}, date_column),      
    $.extend( true, {title: "EXPIRY DATE", width: 100, dataIndx: "expiry_date" ,cls: 'pq-drop-icon pq-side-icon',}, date_column),
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
        
		
        $("#batchinfo_array").val(JSON.stringify(batch_data));
        show_loader();
        if($('#bbbCheck').is(":checked") && is_bbb){
            readyPurchaseBills(total);
        }
        else if($('#ccCheck').is(":checked")){
            readyPurchaseCc();
        }
        else if($('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
        else{
            $("#salefrm").submit();
        }
        
      }
});

function validateAllBatchData()
{
    var status = true;
    $.each(batch_items, function(index,obj)
    {
	   var master_qty  = obj.item_qty;
	
       var qtycount = 0;
        $.each(obj.grid, function(index2, obj2)
        {
            if(obj2.batch_no != '' && obj2.batch_qty != '')
            {
                 var batch_no      = obj2.batch_no;
				 var batch_qty     = obj2.batch_qty;
				 var batch_uom_id  = obj2.batch_uom_id;
			 	 qtycount += parseFloat(batch_qty);
            }
            
        });
        if(parseFloat(qtycount) > parseFloat(master_qty)){
           alert("Unit Qty mismatch!!!! ");
            status = false;
            return status;
        }
    });
	
    return status;
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
    					"manufacturing_date" : manufacturing_date					
                    });
				}
                 
            });   
            
            
			var batch_difference = (parseFloat(master_item_qty)-parseFloat(batch_item_qty));

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
            });
        }
    });
    
    
}

    function set_batch_items()
    {
        batch_items = [];

        if(item_checked.length > 0)
        {
            $.each(item_checked, function(index, object)
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
                       "item_unit" : object.item_unit,
                       "item_unit_id" : object.item_unit_id,
                       "grid": generateBatchGridData()  
                   });
                }
            });

            

            
        }
    }


	function readyBatches(){
	 var items_id_array = batch_items.map(function(obj) { return obj.item_id; });
	 $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>ajax/getItemBatch",
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
		
		
		var page = (batch_Index + 1) + '/' + batch_items.length;
        
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



/************************************************  iTEM BATCH WISE CODEING END  *****************************************************/

/*********************************************** ITEM Tracking CODEING START BY BHUPINDER   ******************************************************/
var tracking_data = [];
var tracking_items = [];
var tracking_Index=0;  

function generateTrackingGridData(){
	 var trackjson = [];
    for(var i=0;i<50;i++){
        trackjson.push({'tracking_method': '','tracking_no': '','tracking_id':'','tracking_qty':'1','tracking_uom':'','tracking_uom_id':''});
    }
    return trackjson;
	
}   

    function tracking_methodEditor(ui) {
    var $inp = ui.$cell.find("select"),
        di = ui.dataIndx,
        rd = ui.rowData,               
        grid = this;
    
    $inp.on("change", function (evt) {
        var method = $(this).val();
        rd.track_no = '';
        rd.tracking_id = '';       
    })
};
	
function trackingno_editor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData
       
        if(rd.batch_method == 'Adjustment')
        {
			 $inp.autocomplete({
                source:  tracking_items[tracking_Index].track_ref_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.tracking_id = ui.item.id;
                    rd.tracking_no = ui.item.label;
                    rd.tracking_qty = 1;
                                   
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.tracking_no = '';
                rd.tracking_id = ''; 
                        
            }).focusout(function () {              
                if(rd.tracking_no != '' && rd.tracking_id == '')
                {
                    var index = tracking_items[tracking_Index].track_ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.tracking_no.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val([tracking_Index].track_ref_list[index].value); 
                        $inp.trigger(downKeyEvent); 
                        $inp.trigger(downKeyEvent);
                        $inp.trigger(enterKeyEvent);
                    }
                    else{
                        rd.tracking_no = '';
                        rd.tracking_id = '';
                       
                    }
                }
            });
        }
        if(rd.tracking_method == 'New Ref.')
        {
            rd.tracking_id = 0;
            $inp.on("focusout", function () {
                var tracking_no = $(this).val();
                if(tracking_no)
                {
                    var index = tracking_items[tracking_Index].track_ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == batch_no.toLowerCase();
                    });
                    if(index > -1){
                        alert('Btach number alredy exists. To use this batch number change method to adjustment');
                        rd.tracking_no = '';
                        rd.tracking_id = 0;
                        
                        
                    }
                }

            });
        }
    }

 function calculateTrackingSummary() {
        var TrackitemQtyTotal = 0;
        var data = this.option('dataModel.data');

        data.forEach(function(row){
           
            if(row.tracking_qty != '' && row.tracking_no){
                TrackitemQtyTotal += parseFloat(row.tracking_qty);
            }
        })

        tracking_item_balance = parseFloat(tracking_items[tracking_Index].item_qty) - parseFloat(TrackitemQtyTotal);

        tracking_items[tracking_Index].tracking_balance = tracking_item_balance;
        $('#tracking_item_balance').text(tracking_item_balance);


        var TracktotalData = {
            tracking_no: "Total",
            tracking_qty  : TrackitemQtyTotal,
            pq_rowcls: 'grid_footer_color',
            summaryRow: true
        }
            
        this.option('summaryData', [TracktotalData]);
    } 

var tracking_dataModel = {'data':generateTrackingGridData()}; 

var tracking_colModel = [
    { title: "METHOD", dataIndx: "tracking_method", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: 'select',
            options: methods,
            init: tracking_methodEditor
        }
    },
    { title: "TRACKiNG NO", dataIndx: "tracking_no", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
			init: trackingno_editor
        }
    },

	 { title: "QTY", dataIndx: "tracking_qty", dataType: "float",width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
        },render: function (ui) {
				            var rd = ui.rowData;
				            var cellData=render_qty(ui.cellData); 
				            if(rd.tracking_no!=''){
							rd.tracking_qty=1;
						    return "1";
				            }
						     else{
						     return "";
						     	rd.tracking_qty=0;
						     }
						  },
	validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],					  
    }
   
];

var tracking_billsObj = {
    collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
    height: 'flex',
    selectionModel: { type: 'cell' },
    scrollModel: { autoFit: true },
    pageModel: { type: 'local', rPP: 5 },
    wrap:false,
    numberCell: { show: true },
    dataModel: tracking_dataModel,
    colModel: tracking_colModel,
    change: calculateTrackingSummary, 
    dataReady: calculateTrackingSummary,  
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
$("#trackingmodel").on('shown.bs.modal', function () {   
    if($("#item_tracking_grid").pqGrid('instance')){     
        $("#item_tracking_grid").pqGrid('refresh');
    }
    else
        $("#item_tracking_grid").pqGrid(tracking_billsObj);
});

$("#save_tracking").on("click",function(){
    var status = true;
    status = validateTracking();
    if(!status){
        return;
    }
    status = validateAllTrackingData();
    if(!status){
        return;
    }
    if(status){
        
      
        saveTrackingData();
        $('#trackingmodel').modal('hide');
       
		
		
        $("#trackinginfo_array").val(JSON.stringify(tracking_data));
        show_loader();
        if($('#bbbCheck').is(":checked") && is_bbb){
            readyPurchaseBills(total);
        }
        else if($('#ccCheck').is(":checked")){
            readyPurchaseCc();
        }
        else if($('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
        else{
            $("#salefrm").submit();
        }
        
      }
});

function validateAllTrackingData()
{
    var status = true;
    $.each(item_checked, function(index,obj)
    {
	   var tracking_master_qty  = obj.item_qty;
	
       var trackingqtycount = 0;
        $.each(obj.grid, function(index2, obj2)
        {
            if(obj2.tracking_no != '' && obj2.tracking_qty != '')
            {
                 var tracking_no      = obj2.tracking_no;
				 var tracking_qty     = obj2.tracking_qty;
				 var tracking_uom_id  = obj2.tracking_uom_id;
			 	 trackingqtycount += parseFloat(tracking_qty);
            }
            
        });
       
        if(parseFloat(trackingqtycount) > parseFloat(tracking_master_qty)){
           alert("Unit Qty mismatch!!!! ");
            status = false;
            return status;
        }
    });
	
    return status;
}

function validateTracking()
{
    var sum = 0;
    var data = $("#item_tracking_grid").pqGrid('option', 'dataModel.data');
    var error = 0;
    var trqtycount = 0;
    
    for (var i = 0; i < data.length; i++) 
    {
        if(data[i]['tracking_no'] != '' && data[i]['tracking_qty'] != '')
        {
           
            var tracking_no         = data[i]['tracking_no'];
            var tracking_qty           = data[i]['tracking_qty'];
            var tracking_uom_id      = data[i]['tracking_uom_id'];
          
            trqtycount += parseFloat(tracking_qty);
            
        }
    }
	
	var ttmaster_qty = item_checked[tracking_Index].item_qty;


   if(parseFloat(trqtycount) > parseFloat(ttmaster_qty)){
        alert("Unit Qty mismatch!!!! ");
        return false;
    }
    return true;
}

function saveTrackingData()
{
	
	tracking_data=[];
    var tracking_item_qty = {};
	
    $.each(tracking_items, function(index,obj)
    {   
        if(obj.item_id!='' && obj.item_name != '')
        {
            var item_id = obj.item_id;
            var item_unit = obj.item_unit;
            var item_unit_id = obj.item_unit_id;

    		var track_master_item_qty = obj.tracking_qty;

            
            var track_item_qty = 0;
            
    		
            $.each(obj.grid, function(index2, obj2)
            {
                var tracking_id            = obj2.tracking_id;
                var tracking_no            = obj2.tracking_no;
                var tracking_qty           = obj2.tracking_qty;
				var tracking_method        = obj2.tracking_method;
               
				if(tracking_no!=''){

					track_item_qty += tracking_qty;

                    tracking_data.push({
                        "tracking_id"        : tracking_id,
                        "item_id"            : item_id,
                        "tracking_no"        : tracking_no,
                        "tracking_qty"       : tracking_qty,
                        "tracking_uom"      : item_unit,
                        "tracking_uom_id"   : item_unit_id,
    					"tracking_method"    : tracking_method
                    });
				}
                 
            });   
            
            
			var track_difference = (parseFloat(track_master_item_qty)-parseFloat(track_item_qty));

	        tracking_data.push({
                "tracking_id"          : 0,
                "item_id"           : item_id,
                "tracking_no"          : 'UNDEFINED',
                "tracking_qty"         : track_difference,
                "tracking_uom"         : item_unit,
                "tracking_uom_id"      : item_unit_id,
                "tracking_method"      : 'Adjustment',
            });
        }
    });
    
    
}

    function set_tracking_items()
    {
        tracking_items = [];

        if(item_checked.length > 0)
        {
            $.each(item_checked, function(index, object)
            {
                var i = tracking_items.findIndex(function(obj) {
                    return obj.item_id == object.item_id && obj.item_unit_id == object.item_unit_id;
                });

                if(i > -1){
                    tracking_items[i].item_qty += parseFloat(object.item_qty);
                }
                else{
                    tracking_items.push({
                      "item_id": object.item_id,
                      "item_name" : object.item_name,
                       "item_qty" : parseFloat(object.item_qty),
                       "item_balance" : parseFloat(object.item_qty),
                       "item_unit" : object.item_unit,
                       "item_unit_id" : object.item_unit_id,
                       "grid": generateTrackingGridData()  
                   });
                }
            });
        }
       
    }


	function readyTracking(){
	 var items_id_array = tracking_items.map(function(obj) { return obj.item_id; });
	 $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>ajax/getItemTracking",
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
                    tracking_items[index].tracking_ref_list = obj;
                });
                stop_loader();
                readyTrackingModel();
            }
        }
    }); 
	
}

function readyTrackingModel(step = '')
{
    if(step == ''){
        tracking_Index = 0;
    }
    else{
        var status = validateTracking();
        if(!status){
            return;
        }
    }
    
    if(step == 'next'){
        if(tracking_Index < (tracking_items.length - 1)){
            tracking_Index = tracking_Index + 1;
        }
    }
    if(step == 'prev'){
        if(tracking_Index > 0){
            tracking_Index = tracking_Index - 1;
        }
    }
    
    if(tracking_items[tracking_Index])
    {
       
        var item_name = tracking_items[tracking_Index].item_name;
        var item_unit = tracking_items[tracking_Index].item_unit;
		var item_qty = tracking_items[tracking_Index].item_qty;
        var track_item_balance = tracking_items[tracking_Index].item_balance;
		
		
		var page = (tracking_Index + 1) + '/' + tracking_items.length;
        
        $('#tracking_item_name').text(item_name);
		$('#tracking_item_qty').text(item_qty);
		$('#tracking_item_unit').text(item_unit);
		$('#tracking_item_balance').text(track_item_balance);
      
        $('#tracking_page').text(page);
        $('#trackingmodel').modal('show');
        $("#item_tracking_grid").pqGrid('option', 'dataModel.data', tracking_items[tracking_Index].grid);
        $("#item_tracking_grid").pqGrid('refreshDataAndView');

        $('input[type="command-line"]').focus();//tempararily shift focus
        $("#item_tracking_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
    }
    
}   



/************************************************  iTEM tracking WISE CODEING END  *****************************************************/
    
function generateBillData()
{
    var json = [];
    for(var i=0;i<50;i++){
        json.push({'method': '', 'reference': '', 'reference_id': '', 'amount': '', 'drcr': '', 'due_date': '', 'narration': ''});
    }
    return json;
}
   var is_bbb = 0;

   var pr_data = [];
   var pr_accounts = [];
   var blankunits=0;
   
    $("#submitbtn").on("click",function(){
        var voucher_series = $('select[name="voucher_series"] option:selected').val();		
	    if(bo_gstin_type=="2"){		
		var is_gstin_composition="1";
		 $.ajax({
            url: baseurl+"/admin/ajax/create_gstpaid_acc", 
            type: 'GET',
            dataType: "json",
            processData: false, 
			async: false,
            cache: false,
            contentType: false,
            beforeSend: function() {show_loader();},
            success: function (response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
            	 $("#gst_paidacc_id").val(response.acc_id);
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
			 })
	
		}else
			var is_gstin_composition="0";
        pr_data = [];
        pr_accounts = [];
        cc_data = [];
		batch_data=[];
        cc_accounts = [];
        batch_items = [];
		new_batch_items = [];
        bbb_data = [];
        bbb_accounts = {};
       var tax_total = 0;
        var bsd_acc_total = 0;
        tracking_items = [];
        blankunits=0;
        
        var total = 0;
        is_bbb = $('select[name="party_id"] option:selected').data('is_bbb');

        item_checked = [];
        var billsundry_item_checked = [];
        var final_item_id =[];
		var tax_item_checked = [];
        var data = $("#grid_search").pqGrid('option', 'dataModel.data');
        var billsundry_data = $("#billsundry_search").pqGrid('option', 'dataModel.data');
        var taxitems_data = $("#taxgrid_search").pqGrid('option', 'dataModel.data');
         
        var final_item_amouint ="0";
        var sdateFrom = '<?php echo $fy_begndt;?>';
        var sdateTo   = '<?php echo $fy_end;?>';
        var sdateCheck  =  $("#voucher_date").val();
        var sd1 = sdateFrom.split("-");
        var sd2 = sdateTo.split("-");
        var sc  = sdateCheck.split("-");
        
        var sfrom_year = sd1[2];  // -1 because months are from 0 to 11
        var sto_year   = sd2[2];
        var scheck_year = sc[2];//, parseInt(c[1])-1, c[0]);
        
        if( (scheck_year==sfrom_year) || (scheck_year==sto_year))
            var sdatevalidate=1;
        else
            var sdatevalidate=0;
     
   
       var bsdtax_total=0;
       var bsd_total = 0; // pr
	   
	   var itemstotal=0;
    itemstotal += grid_total_item($('#grid_search'));		
    itemstotal += grid_total_bsd($('#billsundry_search'));	
	   
	  var party_gstinval =  $('#party_id option[value="'+$("#party_id option:first").val()+'"]').attr('data-gstin');
	   if(party_gstinval==""){
		$('#invoice_type').val("B2C");
		$('#invoice_type_label').html('B2C');
	   }
	  
	  
		for (var j = 0; j < billsundry_data.length; j++) {
            var billsundry_id     = billsundry_data[j]['billsundry_id'];
            var is_cc             = billsundry_data[j]['is_cc'];
            var billsundry_name   = billsundry_data[j]['billsundry_name'];
            var billsundry_rate   = billsundry_data[j]['billsundry_rate'];
            var billsundry_amount = billsundry_data[j]['billsundry_amount'];
            var is_tax_account    = billsundry_data[j]['is_tax_account'];
			var billsundry_memoamnt   = billsundry_data[j]['billsundry_memoamnt'];
		  var billsundry_amountfc   = billsundry_data[j]['billsundry_amountfc'];
            if(billsundry_id != '' && billsundry_name != '')
            {
                billsundry_item_checked.push({
                  "billsundry_id": billsundry_id,
                  "is_cc"        : is_cc,
                  "billsundry_name": billsundry_name,
                  "billsundry_rate": parseAmount(billsundry_rate),
                  "billsundry_amount" :parseAmount(billsundry_amount),
				  "billsundry_fcy_amount": parseAmount(billsundry_amountfc),
				  "memo_amount"   : billsundry_memoamnt,
                });

                //total += parseAmount(billsundry_amount);
             
                if($('#ccCheck').is(":checked") && is_cc){
                  var bsd_amt = billsundry_amount;
                  var bsd_drcr = 'D';
                  if(bsd_amt < 0){
                      bsd_drcr = 'C';
                      bsd_amt = Math.abs(bsd_amt);
                  } 

                  cc_accounts.push({
                      "account_id"    : billsundry_id,
                      "account_name"  : billsundry_name,
                      "drcr"          : bsd_drcr,
                      "acc_type"      : 'bsd',
                      "amount"        : parseAmount(bsd_amt),
                      "grid"          : generateCcData(billsundry_id,'bsd')
                  });
                }

                if($('#prCheck').is(":checked")){
                  var bsd_amt = billsundry_amount;
                  var bsd_drcr = 'C';
                  if(bsd_amt < 0){
                      bsd_drcr = 'D';
                      bsd_amt = Math.abs(bsd_amt);
                  }

                  pr_accounts.push({
                      "account_id"    : billsundry_id,
                      "account_name"  : billsundry_name,
                      "drcr"          : bsd_drcr,
                      "acc_type"      : 'bsd',
                      "amount"        : parseAmount(bsd_amt),
                      "grid"          : generatePrData(billsundry_id,'bsd')
                  });

                  bsd_total += parseAmount(billsundry_amount);
                }
                //if(is_tax_account=="1"){
                   bsdtax_total += parseAmount(billsundry_amount);
                //}
            }

        }
		
		
		/*  tax summary calculation        */
		for (var i = 0; i < taxitems_data.length; i++) {
          var cess_val     = taxitems_data[i]['cess'];
           var cgst_val     = taxitems_data[i]['cgst'];
           var igst_val    = taxitems_data[i]['igst'];
           var sgst_val    = taxitems_data[i]['sgst'];
           var tax_amt_val   = taxitems_data[i]['tax_amt'];
           var tax_hsn_sac_val   = taxitems_data[i]['tax_hsn_sac'];
           var tax_item_id_val = taxitems_data[i]['tax_item_id'];
           var tax_rate_val = taxitems_data[i]['tax_rate'];
		    var cess_rate_val = taxitems_data[i]['cess_rate'];
		   var total_tax_val = taxitems_data[i]['total_tax'];
		   var cess_basis    = taxitems_data[i]['cess_basis'];
		   
		   var tax_cat_id    = taxitems_data[i]['tax_cat_id'];
		    if(typeof taxitems_data[i]['tax_cat_id'] == 'undefined' ){
			  var tax_cat_id="";
		   }
		   
		   
		   if(typeof taxitems_data[i]['bl_nature'] == 'undefined' ){
			  var is_item="1";
			   var is_bsd="0";
		   }
		   else{
			 var  is_item="0";
			 var  is_bsd="1";
		     }
		   
		   
           if(tax_item_id_val!=''){
			   var currency_id = $('select[name="currency_id"] option:selected').val();
			if(currency_id>1){			
			   var fcrates =  $("#view_fcrates_modal #fcy_forex_rate").val();
			
			}else
				var fcrates =0;
            tax_item_checked.push({
                   "cess_val": parseAmount(cess_val),
                    "cgst_val": parseAmount(cgst_val),
                    "igst_val" :parseAmount(igst_val),
                    "sgst_val" :parseAmount(sgst_val),
                    "tax_amt_val" :parseAmount(tax_amt_val),
					"cess_fcy_val": parseAmount(cess_val*fcrates),
                    "cgst_fcy_val": parseAmount(cgst_val*fcrates),
                    "igst_fcy_val" :parseAmount(igst_val*fcrates),
                    "sgst_fcy_val" :parseAmount(sgst_val*fcrates),
					"total_tax_fcy_val" :parseAmount(total_tax_val*fcrates),
                    "tax_hsn_sac_val" :tax_hsn_sac_val,
                    "tax_item_id_val" :tax_item_id_val,					
					"tax_rate_val" :tax_rate_val,
					"cess_rate_val" :cess_rate_val,	
					"is_item":is_item,
					"is_bsd":is_bsd,
					"cess_basis":cess_basis,
					"total_tax_val" :parseAmount(total_tax_val),
					"tax_cat_id":tax_cat_id
                });
				tax_total += parseAmount(total_tax_val);
		   }

        }
		
        var item_total = 0; // pr
        for (var i = 0; i < data.length; i++) {
           var item_id     = data[i]['item_id'];
           var item_name     = data[i]['item_name'];
           var item_price  = data[i]['item_price'];
           var item_qty    = data[i]['item_qty'];
           var item_unit   = data[i]['item_unit'];
           var item_unit_id   = data[i]['item_unit_id'];
           var description = data[i]['description'];
           var currency_id = $('select[name="currency_id"] option:selected').val();
			if(currency_id>1){
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
		   
		   if(item_unit_id=='' && item_name!='')
			   blankunits =parseInt(blankunits)+parseInt(1);
		   
		   if(typeof data[i]['memo_amt'] == 'undefined' ){
			  var memo_amt =0; 
		   }else
			 var memo_amt = data[i]['memo_amt'];  
		   
		   
		    if(typeof data[i]['item_amounttxs'] == 'undefined' ){
				var item_amounttxs = 0;
			}else
				var item_amounttxs = data[i]['item_amounttxs']; 
			
	
           if(!item_amount)
             item_amount =0;

		 if(!item_fcy_amount)
             item_fcy_amount =0;
             final_item_amouint=parseInt(final_item_amouint)+parseInt(item_amount);
            
            if(item_id != '' && item_name != ''){

		        item_checked.push({
		           "item_id": item_id,
                   "item_name": item_name,
                    "item_price": parseAmount(item_price),
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "item_unit_id" :item_unit_id,
                    "item_total_amount" :parseAmount(item_amount),
					"item_total_fcy_amount" :parseAmount(item_fcy_amount),
                    "description" :description,
                    "item_drcr" : 'D',
					"memo_amount" : memo_amt,
					"txinc_amount" : item_amounttxs,
                });

                total += parseAmount(item_amount);
                item_total += parseAmount(item_amount);
            }
        }

        if($('#prCheck').is(":checked")){
            var party_total = bsd_total + item_total;
            var party_id = $('select[name="party_id"]').val();
            var party_name = $('select[name="party_id"] option:selected').text();

            pr_accounts.unshift({
                "account_id"    : party_id,
                "account_name"  : party_name,
                "drcr"          : 'C',
                "acc_type"      : 'acc',
                "amount"        : parseAmount(party_total),
                "grid"          : generatePrData(party_id,'acc')
            });
        }

        if($('#itmbtchCheck').is(":checked"))
             set_batch_items();
             
       if($('#itmtrackingCheck').is(":checked"))
             set_tracking_items();
      
      // var voucher_diff = parseAmount(tax_total)-parseAmount(bsdtax_total);
	   var bsdtax_total_match = parseAmount(Math.abs(bsdtax_total))+parseAmount(0.01);
	   var taxsm_total_match = parseAmount(tax_total);
	  var reverse_charges = $('select[name="reverse_charges"] option:selected').val();
	   var matchparty =    $('#party_ids option').filter(function(){
		   return ($("#clone_party_id").val() === this.value);               
          });
		if(parseAmount(bsdtax_total_match) >= parseAmount(taxsm_total_match))
          var restricted_total=0;
		else if(reverse_charges==1 || reverse_charges==2){
		var restricted_total=0; 
	   }
		else
	      var restricted_total=1;
	  
	   
	   
	  
        var selerror=0;
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
         var erros_msg ='';
		 var errors=0;
		 if( $("#voucher_series").val()==''){
            erros_msg +="Kindly fill the voucher series!!!";
            errors=errors+1;
        }
		else if($("#voucher_date").val()==''){
            erros_msg +="Kindly fill the date!!!";
            errors=errors+1; 
        }
		else if($("#matrcntr_id").val()==''){
            erros_msg +="Kindly fill the MC!!!";
            errors=errors+1;    
        }
		
		else if($("#clone_party_id").val()!='' && matchparty.length==0){
			 erros_msg +='Party field is not valid';   
			 errors=errors+1; 
		   }
		   
		else if($("#pos").val()==''){
            erros_msg +="Kindly fill the POS!!!";
            errors=errors+1;    
        }
		
       else if(selerror=='1'){
            erros_msg +="Kindly fill the form properly!!!";
           errors=errors+1;   
        }	  
       else if(item_checked.length==0 || final_item_amouint=='' || final_item_amouint=='0'){
            erros_msg +="Kindly fill the items data!!!";
            errors=errors+1;   
        }
	else if(blankunits>0){
		alert_notification("Item's unit cannot be blank!!!");
               return false;  
	  } 	
	else if(reverse_charges=="2" && $("#eco_id").val()==''){
		  alert_notification("Ecomerce Operator field is required!!!");
          return false;  		
		   }	
	 else if(is_gstin_composition=="0" && restricted_total=='1'){
			alert_notification("Tax Summary & Bill Sundry Totals's mismatch!!!");
            return false;
		} 
		if(errors>0){
			alert_notification(erros_msg);
			return false;
		 }		 
        else{
           
            show_loader();
            $("#itmsdata").val(JSON.stringify(item_checked));
            $("#billsndrydata").val(JSON.stringify(billsundry_item_checked));
			$("#taxsummarydata").val(JSON.stringify(tax_item_checked));
			if($('#itmtrackingCheck').is(":checked") && tracking_items.length>0){
                readyTracking();
            }
			else if($('#itmbtchCheck').is(":checked") && batch_items.length>0){
                readyBatches();
            }	
            else if($('#bbbCheck').is(":checked") && is_bbb){
                readyPurchaseBills(total);
            }
            else if($('#ccCheck').is(":checked")){
                readyPurchaseCc();
            }
            else if($('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
                readyPr();
            }
            else{
                $("#salefrm").submit();
            }   
        }
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
              
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    <?php if(isset($_GET['p']) && $_GET['p'] == 1){ ?>
                        window.history.back();
                    <?php } else { ?>
                        window.location.reload();
                    <?php } ?>
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
                            <div class="alert alert-danger alert-dismissible">
                              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <ul>${list}</ul>
                            </div>
                        `;
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


    function get_item_grid()
    {
        var data = []
        for(var i=1; i<=500; i++){
            data.push({"item_id": '',"item_name": '', 'pq_cellattr': { item_name: { title: ""}},'pq_cellattr': { item_unit: { title: ""}},'item_qty': '','description': '','item_unit': '','item_unit_id': '','item_price': '','item_amount': ''});
        }
        return data;
    }
    

     $(function () {
     
        var colModel = [
                { title: "ITEM NAME",sortable:false, dataIndx: "item_name", width: 100,cls: 'pq-drop-icon pq-side-icon',
                    editor: {             
                          type: "textbox",
                          init: autoCompleteEditor,
                          options: []
                    },
              },
              { title: "QUANTITY",sortable:false,dataIndx: "item_qty", width: 100, dataType: "float",
                 
                validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '')  
                        return true;              
                    return false;
                },
                editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        $inp.on("change", function (evt) {
						    
						var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
              if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmount(rd.item_price) >= 0)
			  {
				  
                                var amount = rd.item_qty * parseAmount(rd.item_price);
								if($("#last_price").val()=="1"){
					var amount_tax = parseAmount(rd.item_mrp*rd.item_qty);					
				 }else
					 var amount_tax = amount;
				 				
						       if(parseInt(currency_id)>1){
								  rd.item_amountfc=parseAmount(amount);
								  if(parseFloat(fcy_forex_rate)!=0)
								    rd.item_amount   = parseAmount((rd.item_qty*rd.item_price)/fcy_forex_rate);	
							     else
									rd.item_amount   = parseAmount(0); 
								 
							   }
							   else{
								  rd.item_amountfc = 0;
								  rd.item_amount   = parseAmount((rd.item_qty*rd.item_price));								  
								}
							
								 if($('#taxInclusive').is(":checked")){
									 rd.item_amounttxs = amount_tax;
								 }
                }
                if(rd.item_qty == '' && rd.item_price != '' && parseAmount(rd.item_price) >= 0)
				{
                    rd.item_qty = 1;
					if($('#taxInclusive').is(":checked")){
                      rd.item_amounttxs = parseAmount(rd.item_price);  
					}
					else{
						rd.item_amount = parseAmount(rd.item_price);	
					}
                                     
                }				
							
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
								 var totalprice = parseAmount(rd.item_qty)*parseAmount(rd.item_price);
								 var taxableamount = parseAmount(totalprice/y);
								 rd.item_amount = taxableamount;
								 grid.refreshDataAndView();
							 } 
					 grid.refreshDataAndView();
					 show_taxsummary_items();							
							refreshbsd();
                        })
                    }
                },
                render: function (ui) {
                    var rd = ui.rowData;
                    var cellData=render_qty(ui.cellData); 
                    rd.item_qty=cellData;
                    
                    return cellData;
                },
              },
              
              { title: "SHORT NARRATION",sortable:false, width: 100, dataType: "string", dataIndx: "description",
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
                        return true;
                    }
                    return false;
                },
              },
              { title: "UNIT", sortable:false,dataIndx: "item_unit", width: 100,cls: 'pq-drop-icon pq-side-icon',
                    editor: {                   
                          type: "textbox",
                          init:autoCompleteEditor2,
                          options: [],
                      },
                    editable: function (ui) {
                       var item_id = ui.rowData['item_id'];
                        if (item_id != '') {
                            return true;
                        }
                        return false;
                    },
                       
                    
            },
            { title: "PRICE",sortable:false, width: 20, align: "right",dataIndx: "item_price",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
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
                        
                        $inp.on("change", function (evt) {
								var currency_id = $('select[name="currency_id"] option:selected').val();						
								var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
              if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmount(rd.item_price) >= 0)
			  {
				  
                                var amount = rd.item_qty * parseAmount(rd.item_price);
								if($("#last_price").val()=="1"){
					var amount_tax = parseAmount(rd.item_mrp*rd.item_qty);					
				 }else
					 var amount_tax = amount;
				 
								
						       if(parseInt(currency_id)>1){
								  rd.item_amountfc=parseAmount(amount);
								  if(parseFloat(fcy_forex_rate)!=0)
								    rd.item_amount   = parseAmount((rd.item_qty*rd.item_price)/fcy_forex_rate);	
							     else
									rd.item_amount   = parseAmount(0); 
								 
							   }
							   else{
								  rd.item_amountfc = 0;
								  rd.item_amount   = parseAmount((rd.item_qty*rd.item_price));								  
								}
							
								 if($('#taxInclusive').is(":checked")){
									 rd.item_amounttxs = amount_tax;
									 
								 }
                }
                if(rd.item_qty == '' && rd.item_price != '' && parseAmount(rd.item_price) >= 0)
				{
                                rd.item_qty = 1;
								if($('#taxInclusive').is(":checked")){
                                  rd.item_amounttxs = parseAmount(rd.item_price);  
								}
								else{
								 rd.item_amount = parseAmount(rd.item_price);	
								}
                                     
                }
				
				
				
				  
							
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
								 var totalprice = parseAmount(rd.item_qty)*parseAmount(rd.item_price);
								 var taxableamount = parseAmount(totalprice/y);
								 rd.item_amount = taxableamount;
								 grid.refreshDataAndView();
							 } 
					 grid.refreshDataAndView();
					 show_taxsummary_items();							
							refreshbsd();
                        })
						
                    }
                },
                render: function( ui ) {
					
                    var rd = ui.rowData;
					
                    if(rd.item_price != ''){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						var currency_id = $('select[name="currency_id"] option:selected').val();
						 rd.item_price = parseAmount(rd.item_price);
						if(currency_id >1){							
							 return formatAmount(rd.item_price,currency_symbol); 
						}else{
                       
                        return formatAmount(rd.item_price);   
						}
                    }
                    return '';
                }
            },
			{ title: "AMOUNT(₹)",sortable:false, width: 20, align: "right",dataIndx: "item_amounttxs",hidden:true,
              	editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
							
							if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_amounttxs != '' && parseAmount(rd.item_amounttxs) >= 0){
                              show_taxsummary_items(); 
							  var price = parseAmount(rd.item_amounttxs) / rd.item_qty;
                                rd.item_price = price; 
                                grid.refreshDataAndView();        
                            }
                            if(rd.item_qty == '' && rd.item_amounttxs != '' && parseAmount(rd.item_amounttxs) >= 0){
                                 show_taxsummary_items(); 
								rd.item_qty = 1;;
                                rd.item_price = parseAmount(rd.item_amounttxs); 
                                grid.refreshDataAndView();        
                            }			
                            
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
								 var totalprice = parseAmount(rd.item_qty)*parseAmount(rd.item_price);
								 var taxableamount = parseAmount(totalprice/y);
								 rd.item_amount = taxableamount;
								  grid.refreshDataAndView();
						 
							 }else{
								 rd.item_amount = parseAmount(rd.item_price*rd.item_qty);
								 grid.refreshDataAndView();
							 } 
                        })
                    }
                },
				render: function( ui ) {
					
                    var rd = ui.rowData;
					if(rd.item_amounttxs >0){
                        return formatAmount(parseAmount(rd.item_amounttxs));   
                    }
                    return '';
                }
              },
			{ title: "AMOUNT(₹)",sortable:false, width: 20, align: "right",dataIndx: "item_amountfc",hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_price != ''){
						var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
						
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						var currency_id = $('select[name="currency_id"] option:selected').val();
						
						rd.item_amountfc = rd.item_qty*rd.item_price;
						return formatAmount(parseAmount(rd.item_qty*rd.item_price),currency_symbol);   
						
                    }
                    return '';
                }
              },  
            { title: "AMOUNT(₹)",sortable:false, width: 20, align: "right",dataIndx: "item_amount",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
					var currency_id = $('select[name="currency_id"] option:selected').val();
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '' && currency_id =='1') {
                        return true;
                    }
					if(currency_id>1){
					 return false;	
					}
					
                    return false;
                },
                editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd = ui.rowData;
                        var grid = this;
                        var $inp = ui.$cell.find("input");
                        
                        $inp.on("change", function (evt) {
							
                            if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                                var price = parseAmount(rd.item_amount) / rd.item_qty;
                                rd.item_price = price; 
                                grid.refreshDataAndView();        
                            }
                            if(rd.item_qty == '' && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                                rd.item_qty = 1;;
                                rd.item_price = parseAmount(rd.item_amount); 
                                grid.refreshDataAndView();        
                            }
							show_taxsummary_items();							
							refreshbsd();
                        });
                    }
                },
                render: function( ui ) {
                    var rd = ui.rowData;
					var currency_id = $('select[name="currency_id"] option:selected').val();
						
					if(rd.item_amount >0){						
						 return formatAmount(rd.item_amount);						
                    }
                    return '';
                }
              },
			
			{ title: "MEMO(₹)",sortable:false, width: 20, align: "right",dataIndx: "memo_amt",hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.memo_amt >0){
						return formatAmount(parseAmount(rd.memo_amt));
                    }
                    return '';
                }
              },
	 	    ];
	 	    
        var dataModel = {"data": get_item_grid()}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
			refreshHeader: function(){ 
				 if($("#grid_search").pqGrid('instance')){   	
				var $cell = $("#grid_search").pqGrid( 'getCellHeader' , {colIndx: 0}); 
				$cell.attr("title", "Type minimum 3 characters to search item").tooltip();
				 }
        	},
            dataModel: dataModel,
            change: calculateSummary, 
            dataReady: calculateSummary, 
            colModel: colModel,  
            numberCell: { show: true },
            editable: true,
            wrap:false,
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
				show_taxsummary_items();
				 $("#taxgrid_search").pqGrid('refreshDataAndView');
                return false;
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
     
         
         var billsundry_colModel = [
             { title: "BILL SUNDRY", width: 100, dataType: "string", align: "left",dataIndx: "billsundry_name" ,
                editor: {                   
        		  type: "textbox",
                  init: billsundry_autoComplete,
                  options: []
              },
					 
					  
			 },
            
             { title: "AMOUNT(₹)", width: 100, align: "right", dataIndx: "billsundry_amount" ,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_amount >0){
                        rd.billsundry_amount = parseAmount(rd.billsundry_amount);
                        return formatAmount(rd.billsundry_amount);   
                    }
                    return '';
                },
				editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd     = ui.rowData;
                        var grid   = this;
                        var $inp   = ui.$cell.find("input");
						                    
                        $inp.on("change", function (evt) {							
						 show_taxsummary_items();
						 $("#taxgrid_search").pqGrid('refreshDataAndView');							 
                        })
                    }
                },
				
            },{ title: "AMOUNT(₹)", width: 100, align: "right", dataIndx: "billsundry_amountfc" ,hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.billsundry_amount != ''){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val(); 
						if(fcy_forex_rate!='' && $("#currency_id").val() >1){
						 rd.billsundry_amountfc = parseAmount(rd.billsundry_amount*fcy_forex_rate);	
						 return formatAmount(parseAmount(rd.billsundry_amount*fcy_forex_rate),currency_symbol);   
						}
                    }
					
                    return '';
                }
				
            },
			{ title: "MEMO(₹)", width: 100, align: "right", dataIndx: "billsundry_memoamnt" ,hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.billsundry_memoamnt >0){
						rd.billsundry_memoamnt =parseAmount(rd.billsundry_memoamnt);
						 return formatAmount(parseAmount(rd.billsundry_memoamnt));   
						
                    }
					
                    return '';
                }
				
            },
            ];
          
          var bsd_newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'row' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
            pageModel: { type: 'local' },
            numberCell: { show: true },
            change: billsundry_calculateSummary,
            dataReady: billsundry_calculateSummary,  
            wrap:false,
            editable: false,
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
				show_taxsummary_items();
				refreshbsd();		
				$("#taxgrid_search").pqGrid('refreshDataAndView');
				item_bsd_totals();
                return false;
           }
        }
          
          $("#billsundry_search").pqGrid(bsd_newObj2);
		 $("#fcratespopup").on("click",function(){
			var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			var currency_id    = $('select[name="currency_id"] option:selected').val();
			calculate_fc_rates(currency_id,billsundry_colModel,colModel,currency_symbol);  
		  });
		$("#currency_id").on("change",function(){
			
			if($(this).val()=="1"){
			 var data = $('#grid_search').pqGrid('option', 'dataModel.data');	
			 $.each(data, function(index,obj){
					if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
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
								 var totalprice = parseAmount(obj.item_qty)*parseAmount(obj.item_price);
								 var taxableamount = parseAmount(totalprice/y);
								var tax_amt = parseAmount(taxableamount);
						
								data[index]['item_price']     = obj.item_price;
								data[index]['item_amounttxs'] = parseAmount(obj.item_price*obj.item_qty);
								data[index]['item_amount']    = parseAmount(tax_amt); 
						    } else{
							    data[index]['item_price']     = obj.item_price;
								data[index]['item_amount']    = parseAmount(obj.item_price*obj.item_qty);
								data[index]['item_amounttxs'] = parseAmount(0); 
								
								
						     }
								
							
				
						  }
					});	
				
			$('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');	
				show_taxsummary_items();				
				refreshbsd();
			}
			
			 if($(this).val() >1 && $('#taxInclusive').is(":checked")){
				 
				alert_notification("Tax Inclusive can not applied on foreign  currency!");
				$(this).val("1");
				return false;
			 }else{
			  var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			  calculate_fc_rates($(this).val(),billsundry_colModel,colModel,currency_symbol);
			  item_bsd_totals();
			 }
			 
			 
			 
			});	
			
			$('#memoCheck').on('change', function(){ // on change of state
		     if(this.checked) // if changed state is "CHECKED"
			  {
				memoChanged(billsundry_colModel,colModel,true);
			  }
			  else{
				memoChanged(billsundry_colModel,colModel,false);
			  }
			item_bsd_totals();  
		})
		
		$('#taxInclusive').on('change', function(){ // on change of state
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
					if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
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
				
							if($("#last_price").val()=="1"){
								data[index]['item_price'] = obj.item_mrp;
								
								var itmrate = igst_rate_val;
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 var totalprice = parseAmount(obj.item_qty)*parseAmount(obj.item_mrp);
								 var taxableamount = parseAmount(totalprice/y);
								var tax_amt = parseAmount(taxableamount);
					
								data[index]['item_amounttxs'] = parseAmount(obj.item_mrp*obj.item_qty);
								data[index]['item_amount'] = parseAmount(tax_amt);
							}
							else{
								var itmrate = igst_rate_val;
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 var totalprice = parseAmount(obj.item_qty)*parseAmount(obj.item_price);
								 var taxableamount = parseAmount(totalprice/y);
								var tax_amt = parseAmount(taxableamount);
						
								data[index]['item_price'] = obj.item_price;
								data[index]['item_amounttxs'] = parseAmount(obj.item_price*obj.item_qty);
								data[index]['item_amount'] = parseAmount(tax_amt);
							}
				
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
					if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
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
				
							if($("#last_price").val()=="1"){
								data[index]['item_price'] = obj.item_mrp;
								
								data[index]['item_amount'] = parseAmount(obj.item_mrp*obj.item_qty);
								data[index]['item_amounttxs'] = parseAmount(0);
							}
							else{
								data[index]['item_price'] = obj.item_price;
								data[index]['item_amount'] = parseAmount(obj.item_price*obj.item_qty);
								data[index]['item_amounttxs'] = parseAmount(0);
							}
				
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
             window.location.href = "<?php echo $base_url; ?>/purchase/item";
         }
         else if(type == 'without_item'){
             $(this).val('with_item');
             window.location.href = "<?php echo $base_url; ?>/purchase/non_item";
         }
        
     })

         //------------------------------------------------------------------------------------------------------Cost Center
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
            if($('#prCheck').is(":checked") && pr_accounts.length > 0){
                readyPr();
            }
            else{
                $("#salefrm").submit();
            }
        }
    });

    function saveCcData()
    {
        $.each(cc_accounts, function(index,obj)
        {
            var account_id = obj.account_id;
            var acc_type = obj.acc_type;
            
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
            var final_total  = parseAmount(final_amount);
            
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
                        sub = cc_txn_amt;
                    }
                    if(cc_txn_drcr == 'C'){
                        sub = -cc_txn_amt;
                    }
                    total += parseAmount(sub);
                    
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

        final_amount = parseAmount(final_amount);
        
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
    function readyPurchaseCc()
    {
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/purchase/getPurchaseCc",
            data: { itmsdata : item_checked},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status)
                {

                    var itm_cc_accounts = response.data.cc_accounts;
                    $.each(itm_cc_accounts, function(index,obj){
                        itm_cc_accounts[index]['grid'] = generateCcData();
                        cc_accounts.push(itm_cc_accounts[index]);
                    });

                    cc_list     = response.data.cc_list;
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
            $("#cc_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
        }
        
    }
                
                
    function generateCcData(acc_id = 0, acc_type = '')
    {
        var json = [];
        for(var i=0;i<50;i++){
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
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.cc_id != '' && row.cc_txn_amt != '' && row.cc_txn_drcr != '')
            {
                sub = 0;
                if(row.cc_txn_drcr == 'D'){
                    sub = row.cc_txn_amt;
                }
                if(row.cc_txn_drcr == 'C'){
                    sub = -row.cc_txn_amt;
                }
                total  += parseAmount(sub);
            }
        })
        var drcr = 'Dr';
        if(total < 0){
            drcr = 'Cr';
            total = -total;
        }
        
        var totalData = {
            cc_txn_narr : formatAmount(total) + ' (' + drcr + ')',
            cc_txn_amt : '',
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
            grid.setSelection({ rowIndx: 0, focus: false });
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

    //-------------------------------------------------------------------------------------------------------------------------------------

    //------------------------------------------------------------------------------ Bill By Bill


$("#save_bill_by_bill").on("click",function(){
    var status = true;
    status = validateBills();
    if(!status){
        return;
    }
    if(status){
        saveBillByBillData();
        $('#billsModal').modal('hide');
        show_loader();
        $("#bbbdata").val(JSON.stringify(bbb_data));

        if($('#ccCheck').is(":checked")){
            readyPurchaseCc();
        }
        else if($('#prCheck').is(":checked") && pr_accounts.length > 0){
            readyPr();
        }
        else{ 
            $("#salefrm").submit(); 
        }
    }
});

function saveBillByBillData()
{
    
    var account_id = bbb_accounts.account_id;
    
    
    $.each(bbb_accounts.grid, function(index, obj)
    {
        if(obj.method != '' && obj.reference != '')
        {
            var method     = obj.method;
            var reference  = obj.reference;
            var reference_id  = obj.reference_id;
            var amount     = obj.amount;
            var drcr       = obj.drcr;
            var due_date   = obj.due_date;
            var narration  = obj.narration;

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
   
}




function readyPurchaseBills(total_amount)
{
    var account_id = $('select[name="party_id"]').val();
    var account_name = $('select[name="party_id"] option:selected').text();

    bbb_accounts = {
        'account_id'    : account_id,
        'account_name'  : account_name,
        'drcr'          : 'C',
        'amount'        : parseAmount(total_amount),
        'grid'          : generateBillData()
    }

    $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>/ajax/getAccountBillRefs",
        data: {account_id_array: [account_id]},
        datatype: "json",
        success: function(response){
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if(response.status)
            {
                bbb_accounts.ref_list = response.data[0];
                
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
    if(count == 0){
        error = 1;
    }
    if(error){
        alert("Kindly fill the details correctly !!!");
        return false;
    }
    var final_amount = parseAmount(bbb_accounts.amount); 
    var final_drcr = bbb_accounts.drcr;
    
    if(final_drcr == 'C'){
        final_amount = -final_amount;
    }
    
    if(sum != final_amount && count > 0){
        alert('Total Mismatch');
        return false;
    }
    
    return true;
}


function readyBillsModel()
{
    if(bbb_accounts)
    {
        var account_name = bbb_accounts.account_name;
        var amount = bbb_accounts.amount;
        var drcr = bbb_accounts.drcr;
        
        $('#bills_account').text(account_name);
        $('#bills_total').html(formatAmount(amount));
        $('#bills_drcr').text(drcr + 'r');
        

        $('#billsModal').modal('show');
        
        $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', bbb_accounts.grid);
        $("#bill_by_bill_grid").pqGrid('refreshDataAndView');
    }
    
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
          grid.refreshDataAndView();
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

    var master_amount = bbb_accounts.amount;
    var master_drcr = bbb_accounts.drcr;

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
                source:  bbb_accounts.ref_list,
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
                    var index = bbb_accounts.ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.reference.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val(bbb_accounts.ref_list[index].value); 
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
                    var index = bbb_accounts.ref_list.findIndex(function(obj) {
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
            total  += parseAmount(sub);
        }
    })
    var drcr = 'Dr';
    if(total < 0){
        drcr = 'Cr';
        total = -total;
    }
    
    var totalData = {
        narration : formatAmount(total) + ' (' + drcr + ')',
        amount : '',
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
    pageModel: { type: 'local', rPP: 5 },
    wrap:false,
    numberCell: { show: true },
    dataModel: bill_dataModel,
    colModel: bill_colModel,
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

//--------------------------------------------- Project Reporting  prstart

    var saved_pr_txns = <?= json_encode([]) ?>;
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

        var item_data = item_checked;
        
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/ajax/getPr",
            data: {item_data : item_data, type: 'PURCHASE'},
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

$(document).on('change', '[name="type"]', function(e){
         var type = $(this).val();
         e.preventDefault();
         if(type == 'item'){
             window.location.href = "<?php echo $base_url; ?>purchase_order/item";
         }
         else if(type == 'non_item'){
            $(this).val('item');
             window.location.href = "<?php echo $base_url; ?>purchase_order/non_item";
         }
        
     })
 </script>
</body>
</html>
