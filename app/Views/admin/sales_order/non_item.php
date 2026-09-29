<?php $header = array( 	'title' => 'Sales Order (Non-Item)' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row pb-2">
    <div class="col-sm-6 order-1"><h3>Sales Order (Non-Item)</h3></div>  
    <div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a> 
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
     <select name="type" id="type" class="form-select d-inline-block" style="width:160px;">
        <option value="item">Item</option>
        <option value="non_item" selected="selected">Non-Item</option>
      </select>
    <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:120px;">
      <?php foreach ($currency_list as $value) { ?>
      <option data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>">
      <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-primary">#</div>
</div>
	
   </div>
   
 <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
    <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
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
        <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);">Apply Tax</a>
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
			foreach($voucher_series_dropdown as $vs_row){ ?>
               <option id="<?= $vs_row['comp_vch_series_id']?>" data-srsmethod="<?= $vs_row['comp_vch_method'] ?>"  value="<?= $vs_row['comp_vch_series_id'] ?>" <?php echo ($vs_row['comp_vch_series_id']=='19')?'selected':''?>><?= $vs_row['comp_vch_series'] ?></option>
            <?php } ?>
		  </select>             
          </div>
        </div>
		 <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">Supply Type:</span>
           
            <?php echo form_dropdown('supply_type', $supply_type_list, '',' id="supply_type" class="sale_type form-select required" required '); ?>
           
            <div data-code="0" id="taxpopup" title="Add Description" alt="Add Description" class="btn btn-sm btn-primary SupplyDescModal">#</div>
          </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
              <label class="input-group-text">Date:</label>
              <input type="text" name="voucher_date" id="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm">
            </div>
        </div>  
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Voucher No:</label> 
                <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled>
            </div>
        </div>
      <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Bill No:</label>
		  <?php if($voucher_bill_no!=''){ ?>
		  <input type="text" name="billno" id="billno" value="<?php echo $voucher_bill_no;?>" class="form-control form-control-sm" disabled>
		  <?php } else{ ?>
          <input type="text" name="billno" id="billno" class="form-control form-control-sm">
		  <?php } ?>
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
               <option data-gstin="<?php echo $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_name'] ?>" >
            <?php } ?>
		  </datalist>
        </div>
    </div>
 <div class="col-md-2 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">POS:</label>
          <?php echo form_dropdown('pos', $states_lists, "",' id="pos" class="sale_type form-select required" '); ?>
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
	  <div class="col-md-4 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Invoice Type :</label><label class="input-group-text" id="invoice_type_label"></label>
		
			
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
	  <input type="hidden" name="invoice_type" id="invoice_type">
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Order No:</label> 
                <input type="text" name="order_no" class="form-control form-control-sm">
            </div>
        </div>

        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text px-2">Against:</label>
                <select name="against" id="against" class="selectwidget form-control">
                    <option value=""></option>
                    <option value="1">Something</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
              <label class="input-group-text">Due Date:</label>
              <input type="text" name="due_date" id="due_date" value="" class="datepicker form-control form-control-sm" required>
            </div>
        </div>
        <div class="col-md-12 col-12 card p-2">
            <div class="input-group">
              <label class="input-group-text">Narration:</label> 
              <textarea  name="narration" class="form-control form-control-sm"></textarea>
            </div>
        </div>
	 <input type="hidden" name="hsndata" id="hsndata">
     <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="bbbdata" id="bbbdata">
	  <input type="hidden" name="gst_paidacc_id" id="gst_paidacc_id">
	  <input type="hidden" name="bsd_comp_applytax_json" id="bsd_comp_applytax_json">
      <input type="hidden" name="prdata" id="prdata">
      <input type="hidden" name="billsndrydata" id="billsndrydata">
	  <input type="hidden" name="taxsummarydata" id="taxsummarydata">
      </div>
    </div>

   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12"><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-8">
           <h4 class="text-center">Tax Summary</h4>
           <div id="taxgrid_search" style="margin:auto;"></div>  
         
  </div><select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option  data-is_bbb="0" value="">
						</option>			
					  </select>
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
        <button type="reset" id="submitbtn" class="btn btn-success btn-lg">Reset</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
        
    </div>
        
    </div>
  <div class="modal fade pt-5" id="view_supplydesc_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Supply Type Description</h4>
                        <button type="button" class="btn-close hist_closemodal_window"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					   <table class="table" cellspacing="0">
					  <tr>
					  <th>Description</th>
					 				  
					  </tr>
					  <tbody>
					  
			          	<tr>
					<td>
					<textarea name="supply_type_desc" id="supply_type_desc"  cols="10" rows="10" class="form-control"></textarea>
						</td>
				
					</tr>
					 </tbody>
					  </table>
					  	 <select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option  data-is_bbb="0" value="">
						</option>			
					  </select>
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="save_supplybtn">Save</button>
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
					<input type="text" name="transport_doc_date" id="transport_doc_date" value="" maxlength="255" class="form-control datepicker">
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
					  </table>
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="save_transport_detail">Save</button>
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
							<input type="text" class="form-control" name="fcy_forex_rate" id="fcy_forex_rate" onkeydown="return validatefcrate(event,this.value);" required>
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
</form>
<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=500;$i++)
    $json_data[] =array("acc_id"=>'',"account_name"=>'','pq_cellattr'=> array('account_name'=>array('title'=>'')), 'description'=>'','amount'=>'');

for($i=1;$i<=500;$i++)
    $tax_json_data[] =array("tax_item_id"=>"","id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'','total_tax'=>'');

for($i=1;$i<=50;$i++)
    $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_rate'=>'','billsundry_amount'=>'');
?>

<style>
    .boldcell{font-weight:700;}
</style>

<script>
var bsd_comp_applytax_json=[];
var bo_gstin_type='<?php echo $bo_gstin_type;?>';
var goods_rate   ='1';
var services_rate='6';
$("#voucher_date").on("change",function(){
	var voucher_date     = $(this).val();	
	var vouchermethod = $('select[name="voucher_series"] option:selected').attr('data-srsmethod');
	var series_id = $('select[name="voucher_series"] option:selected').val();
	if(vouchermethod=="1"){
       $.ajax({
            url: baseurl+"/admin/ajax/GetAutoBillNo", 
            type: 'POST',
            dataType: "json",
            data:{'voucher_type_id':'19','series_id':series_id,'voucher_date':voucher_date} , 
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
            data:{'voucher_type_id':'19','series_id':series_id,'voucher_date':voucher_date} , 
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

/*********************  FC Rates   Start         *********************/

function calculate_fcChanged(currency_id,billsundry_colModel,colModel,currency_symbol)
    {
	
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
        else{
				 			
				colModel[2].hidden=true;
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
				show_taxsummary_items();				
				refreshbsd();
	
	$("#view_fcrates_modal").modal("hide")});
	
	
$("#view_fcrates_modal").on("hidden.bs.modal",function(){
	$("#view_fcrates_modal #fcy_voucher_no").html("");
	$("#view_fcrates_modal #fcy_voucher_date").html("");
   });
function calculate_fc_rates(currency_id,billsundry_colModel,colModel,currency_symbol){	
       if(parseInt(currency_id) >1){					
                $("#view_fcrates_modal #selcyrlabel").html(currency_symbol);				
				$("#view_fcrates_modal #fcy_voucher_no").html($("#voucher").val());
				$("#view_fcrates_modal #fcy_voucher_date").html($("#voucher_date").val());				
			    $("#view_fcrates_modal").modal('show');			   
			    billsundry_colModel[2].hidden=false;
                billsundry_colModel[2].width= 170;
				billsundry_colModel[2].title='AMOUNT('+currency_symbol+')';
                $("#billsundry_search").pqGrid( "option", "colModel", billsundry_colModel );             
                $("#billsundry_search").pqGrid( {autoFit: true} );               
                $("#billsundry_search").pqGrid("refresh");
                
			    colModel[2].hidden=false;
                colModel[2].width= 20;
				colModel[2].title='AMOUNT('+currency_symbol+')';
				
				
                $("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refreshCM");
                $("#grid_search").pqGrid("refresh");
			 }
			 else{
				$("#view_fcrates_modal").modal('hide');
				billsundry_colModel[2].hidden=true;
                billsundry_colModel[2].width= 170;
				$("#billsundry_search").pqGrid( "option", "colModel", billsundry_colModel);             
                $("#billsundry_search").pqGrid( {autoFit: true} );                
                $("#billsundry_search").pqGrid("refresh");	
                
                
				colModel[2].hidden=true;
                colModel[2].width= 20;
				
			
				
				$("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refresh"); 
			 }	
}
/*********************  FC Rates  END          *********************/
function grid_total_item(obj,currency='')
    {
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
	function grid_total_bsd(obj,currency='')
    {
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
	 if($(this).val()==1 || $(this).val()==2){
		  
		var bsd_taxsummary_json=[];
		for(var i=1;i<=50;i++){		 
	     bsd_taxsummary_json.push({'bl_nature':'','is_tax_account':0,'tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	   }
	  $("#billsundry_search").pqGrid('option', 'dataModel.data', bsd_taxsummary_json);
	  $("#billsundry_search").pqGrid('refreshDataAndView');
	  
	  stop_loader();
	 }else{
		show_taxsummary_items();
			stop_loader();
			
	 }		 
 });	
function grid_total_taxsumry(obj)
    {
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
 
            data.forEach(function(row){
                if(row.total_tax != '' && row.total_tax != undefined)
                {
                    total += parseAmount(row.total_tax)
                }
            });
        }
        return total;
    }

$(function(){	
	let input = document.getElementById('clone_party_id');
   let timeout = null;
  input.addEventListener('keyup', function (e) {
   // clearTimeout(timeout);
   // timeout = setTimeout(function () {		
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
			  $("#party_id option").attr("data-gstin","");
			 $("#party_id option").text('');
			 
			  $('#invoice_type option[value=""]').prop('selected', true);
			  $('#invoice_type_label').html('');
			  invoice_type_changes();
			  $("#pos").trigger('change');
		    
	}else{
		
		
	var opt = $('#party_ids option[value="'+val+'"]');
	var opid = (opt.attr('id'));
	var opstcode = (opt.data('statecode'));	
	var opgstin = (opt.data('gstin'));
	
	$("#pos").val(opstcode);	
	$("#grid_search").pqGrid("option", "editable", true);
	$("#billsundry_search").pqGrid("option", "editable", true);
	$("#party_id option").val(opt.attr('id'));
	$("#party_id option").attr("data-is_bbb",opt.attr('data-is_bbb'));
	$("#party_id option").attr("data-gstin",opgstin);	
	$("#party_id option").text($(this).val());	
	$('#party_id option[value="'+opt.attr('id')+'"]').prop('selected', true);
	invoice_type_changes();	
	$("#pos").trigger('change');
	}   
   // }, 1000);
  });

   
   });
var ugst_states        = ['35','04','26','25','31','38','34','97'];
var accounts_json_file = <?php echo $accounts_json_file; ?>;
var sdateFrom      ='<?php echo $fy_begndt;?>';
var sdateTo        = '<?php echo $fy_end;?>';
var bsd_json_file  = <?php echo $bsd_json_file;?>;
var bo_state_code  = '<?php echo sprintf('%02d', $bo_state_code);?>';
var tax_json_data  = <?php echo json_encode($tax_json_data);?>;
var billsundry_dataModel = {"data":<?php echo json_encode($billsundry_json_data);?>}
</script>
<script src="<?php echo base_url();?>/public/js/sale_without_item.js"></script>
<script src="<?php echo base_url();?>/public/js/sale_without_item_grid.js"></script>
<script>
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
			  $("#transporter_div").html(response);			 
			  $("#save_transport_detail").prop("disabled", true);
			  $("#add_transporter_modal").modal('hide');			  
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

$("#supply_type").on("change",function(){
	if($(this).val()=='2'){
		 $('#invoice_type').val("EXPWP");			 
		 $('#invoice_type_label').html('EXPORTS WITH PAYMENT');	

		 $("#reverse_charges").val(0);	
		 $('#reverse_charges option[value="0"]').prop('selected', true);
		 $('#reverse_charges').prop('disabled',true); 
	 }
     else if($(this).val()=='3'){
		 $('#invoice_type').val("EXPWOP");	  
		 $('#invoice_type_label').html('EXPORTS WITHOUT PAYMENT');
		 $("#reverse_charges").val(0);	
		 $('#reverse_charges option[value="0"]').prop('selected', true);
         $('#reverse_charges').prop('disabled',true);		 
	 }
	 else if($(this).val()=='13'){
		 $('#invoice_type').val("DE");	  
		 $('#invoice_type_label').html('DEEMED EXPORT');
		 $("#reverse_charges").val(0);	
		 $('#reverse_charges option[value="0"]').prop('selected', true);
		 $('#reverse_charges').prop('disabled',true);	
	 }
	 else if($(this).val()=='4'){
		 $('#invoice_type').val("SEZWP");	 
		 $('#invoice_type_label').html('SEZ WITH PAYMENT');	
		 $("#reverse_charges").val(0);	
		 $('#reverse_charges option[value="0"]').prop('selected', true);
		 $('#reverse_charges').prop('disabled',true);
	 }
	 else if($(this).val()=='5'){
		$('#invoice_type').val("SEZWOP");		  
		$('#invoice_type_label').html('SEZ WITHOUT PAYMENT');
		$("#reverse_charges").val(0);	
		$('#reverse_charges option[value="0"]').prop('selected', true);	
		$('#reverse_charges').prop('disabled',true);
	 }
	 else{
	  $('#invoice_type').val("CBW");	  
	  $('#invoice_type_label').html('CUSTOM BONDED WAREHOUSE (CBW)');
	  $("#reverse_charges").val(0);	
	  $('#reverse_charges').prop('disabled',false);
	 }
	 
    if($(this).val()=='12'){
       $("#view_supplydesc_modal").modal("show"); 
       $("#supply_type_desc").attr("required",true);        
    }else{
         $("#view_supplydesc_modal").modal("hide"); 
         $("#supply_type_desc").attr("required",false);        
       }
	if($(this).val()=='3' || $(this).val()=='5'){
		 var taxsummary_json=[];
		 var bsd_taxsummary_json=[];
	     for(var i=1;i<=50;i++){		 
	     taxsummary_json.push({'billsundry_name':'','bl_nature':'','tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess_basis":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	     }
        $("#taxgrid_search").pqGrid('option', 'dataModel.data',taxsummary_json);
        $("#taxgrid_search").pqGrid('refreshDataAndView');	
		 
		for(var i=1;i<=50;i++){		 
	     bsd_taxsummary_json.push({'bl_nature':'','is_tax_account':0,'tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	    }	
	    $("#billsundry_search").pqGrid('refreshDataAndView');
	    $("#taxgrid_search").pqGrid('option', 'dataModel.data', bsd_taxsummary_json);
		$("#billsundry_search").pqGrid('option', 'dataModel.data', bsd_taxsummary_json);
        $("#taxgrid_search").pqGrid('refreshDataAndView');
		$("#billsundry_search").pqGrid('refreshDataAndView');
	 }else{
		 show_taxsummary_items();
	 }    
  })
  
function commentRender(ui) {
            if (this.attr({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, attr: 'title' }).attr) {
                if (ui.column.align == 'right') {
                    return { cls: 'pq-comment pq-comment-left' };
                }
                else {
                    return { cls: 'pq-comment' };
                }
            }
        };
		
var billsundry_autoComplete = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var element= {};

        $inp.autocomplete({
                source:  bsd_json_file,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {					
					// check if item choosed or not 					
					event.preventDefault();
                    rd.tax_catg_id     = ui.item.tx_ct_id;
                    rd.billsundry_name = ui.item.label;
                    rd.billsundry_id   = ui.item.id;
                    rd.bl_tax_rate     = ui.item.bl_tx_igst_rte;
					rd.bl_hsn_sac      = ui.item.bl_hsn_sac;
					rd.bl_nature       = ui.item.bl_nature;
					rd.is_tax_account  = ui.item.is_tax_account;
					rd.bl_ipt_ott      = ui.item.bl_ipt_ott;
					
					if(ui.item.is_tax_account=="1" && bo_gstin_type=="2"){
						alert_notification("Composition Gstin Dealers Are Not Allowed To Add Tax In Sale Invoice");
						return false;
					}
					
					
                    $(this).val(ui.item.label);					
					
					var ttamount   =0;
					var cgst_total =0;
					var igst_total =0;
					var sgst_total =0;	
                    var cess_total =0;					
					var items_footer_total = $('#taxgrid_search').pqGrid('option', 'dataModel.data');	
										
					var item_footer_total=0;
					var i;
					for (i = 0; i < items_footer_total.length;i++) {
						if(items_footer_total[i]['tax_hsn_sac']!=''){
							cgst_total += parseFloat(items_footer_total[i]['cgst']);
							igst_total += parseFloat(items_footer_total[i]['igst']);
							sgst_total += parseFloat(items_footer_total[i]['sgst']);
							cess_total += parseFloat(items_footer_total[i]['cess']);
						  }
					   }
					 
					   var stcode = $('select[name="pos"] option:selected').val();
						
						if(rd.is_tax_account=="1" && (rd.bl_ipt_ott=="1" || rd.bl_ipt_ott=="2" ))
						{
						if(stcode==bo_state_code){	
                          if(ugst_states.includes(stcode)==true  && (rd.bl_nature=="33" || rd.bl_nature=="195"))
							{// if ut tax 
							if(rd.bl_nature=="33")
								rd.billsundry_amount = parseFloat(cgst_total);					   
							 if( rd.bl_nature=="195")
								rd.billsundry_amount = parseFloat(sgst_total);
							}
							else if(ugst_states.includes(stcode)==false && rd.bl_nature!="195" && (rd.bl_nature=="33" || rd.bl_nature=="34")) //CGST	 OR SGST
						    {
							if(rd.bl_nature=="33")
								rd.billsundry_amount = parseFloat(cgst_total);						   
							 if(rd.bl_nature=="34" )
								rd.billsundry_amount = parseFloat(sgst_total);
						    }
						  if(rd.bl_nature=="36"){ // CESS
							
							 rd.billsundry_amount = parseFloat(cess_total);
							} 
						   /* if(rd.bl_nature=='33')	//CGST		
						       rd.billsundry_amount = parseFloat(cgst_total);
							else if(rd.bl_nature=='34')	//SGST		
						       rd.billsundry_amount = parseFloat(sgst_total);
							else if(rd.bl_nature=='35')	//IGST		
						       rd.billsundry_amount = 0; 
							else if(rd.bl_nature=='36')	//CESS		
						       rd.billsundry_amount = cess_total;    
							 else
								rd.billsundry_amount = 0;  */	
						}else{
							if(rd.bl_nature=="35") //IGST
						    {
						    rd.billsundry_amount = parseFloat(igst_total);
							}
							
							
							
							/* if(rd.bl_nature=='33')	//CGST		
						       rd.billsundry_amount = 0;
							else if(rd.bl_nature=='34')	//SGST		
						       rd.billsundry_amount = 0;
							else if(rd.bl_nature=='35')	//IGST		
						       rd.billsundry_amount = parseFloat(igst_total);
							else if(rd.bl_nature=='36')	//CESS		
						       rd.billsundry_amount = cess_total;     
							else
								rd.billsundry_amount = 0;  */	
						 }
						
						 
						}
				
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
				
				if(rd.bl_ipt_ott=="0"){                     
                     rd.billsundry_name = '';
                     rd.billsundry_id = ''; 
                     rd.billsundry_amount='';
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
$(".SupplyDescModal").on("click",function(){    
    $("#view_supplydesc_modal").modal("show");
})

$("#save_supplybtn").on("click",function(){	   
	   var supplytype = $('select[name="supply_type"] option:selected').val();
       var supply_type_desc = $("#view_supplydesc_modal #supply_type_desc").val();       
       if(supplytype=='12'){ // if other selected description is required
           if(supply_type_desc==''){
               alert_notification("Description field is required!!!");
               return false;
           }
           else
           $("#view_supplydesc_modal").modal('hide'); 
       }else
        $("#view_supplydesc_modal").modal('hide');	 
   });
   
   $(".hist_closemodal_window").on("click",function(){
       var supplytype = $('select[name="supply_type"] option:selected').val();
       var supply_type_desc = $("#view_supplydesc_modal #supply_type_desc").val();       
       if(supplytype=='12'){ // if other selected description is required
           if(supply_type_desc==''){
               alert_notification("Description field is required!!!");
               return false;
           }
           else
           $("#view_supplydesc_modal").modal('hide'); 
       }else
        $("#view_supplydesc_modal").modal('hide');
    }); 		
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

   function update_grid_account_balances() {
        var data = $("#grid_search").pqGrid('option', 'dataModel.data');

        $.each(data, function(index,obj){
            if(obj.account_id != '' && obj.account_id != undefined)
            {
                data[index]['account_balance'] = accnt_balances[obj.account_id];
            }
        });


        $("#grid_search").pqGrid('option', 'dataModel.data', data);
        $("#grid_search").pqGrid('refreshDataAndView');
   }
   
    var intRegex = /^\d+$/;
    var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

    
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
       
        function disableTextRenderer(ui) {
            grid = this,
            rowData = ui.rowData,
            rowIndx = ui.rowIndx,
            dataIndx = ui.dataIndx;
            if (grid.isEditableCell({ rowIndx: rowIndx, dataIndx: dataIndx }) == false) {
                grid.addClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
            else {
                grid.removeClass({ rowIndx: rowIndx, dataIndx: dataIndx, cls: 'disabled' });
            }
        };
        
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
								  grid.refreshDataAndView();
						 
							 }else{
								 rd.amount = parseAmount(rd.amounttxs);
								 grid.refreshDataAndView();
							 } 
							 show_taxsummary_items(); 
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
					    show_taxsummary_items();							
							refreshbsd();	
					 
                        })
						
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
                },editor: {                   
                    type: "textbox",
                    init: function(ui){
                        var rd     = ui.rowData;
                        var grid   = this;
                        var $inp   = ui.$cell.find("input");
						                    
                        $inp.on("change", function (evt) {
							
						 show_taxsummary_items();
						 $("#taxgrid_search").pqGrid('refreshDataAndView');	
						 refreshbsd();
                        })
                    }
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
            
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            change: calculateSummary,   
             columnTemplate: { render: commentRender },
            colModel: colModel,  
            numberCell: { show: true },
            wrap:false,
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
				refreshbsd();
                
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
             { title: "AMOUNT", width: 100, align: "right", dataIndx: "billsundry_amount" ,dataType: "float",
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
						 refreshbsd();
                        })
                    }
                },
				
            },
			{ title: "AMOUNT(₹)", width: 100, align: "right", dataIndx: "billsundry_amountfc" ,hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.billsundry_amount >0){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val(); 
						if(fcy_forex_rate!='' && $("#currency_id").val() >1){
							rd.billsundry_amountfc=parseAmount(rd.billsundry_amount*fcy_forex_rate);
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
				$("#taxgrid_search").pqGrid('refreshDataAndView');
				item_bsd_totals();
                
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
		});
		
		
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
					if(obj.acc_id != '' && obj.acc_id != undefined)
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
			show_taxsummary_items();  
			item_bsd_totals(); 
		})	
		 
    });
     
     $(document).on('change', '[name="type"]', function(e){
         var type = $(this).val();
         e.preventDefault();
         if(type == 'item'){
            $(this).val('non_item');
             window.location.href = "<?php echo $base_url; ?>sales_order/item";
         }
         else if(type == 'non_item'){
             window.location.href = "<?php echo $base_url; ?>sales_order/non_item";
         }
        
     })
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

        
         if($('#prCheck').is(":checked") && pr_accounts.length > 0){ //prstart
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
        if(obj.method != '' && obj.reference != ''&& obj.reference_id != '')
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



function readySalesBills(total_amount)
{
    var account_id = $('select[name="party_id"]').val();
    var account_name = $('select[name="party_id"] option:selected').text();

    bbb_accounts = {
        'account_id'    : account_id,
        'account_name'  : account_name,
        'drcr'          : 'D',
        'amount'        : total_amount,
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
            sum += parseAmount(sub);
              
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

            
function generateBillData()
{
    var json = [];
    for(var i=0;i< 50; i++){
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

        var item_data = [];
        
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/ajax/getPr",
            data: {item_data : item_data},
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
 </script>	
</body>
</html>
