<?php $header = array(  'title' => 'Sales Order' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
if(isset($billfrm_data)){
   $from_gstin     = '<p>'.$billfrm_data['comp_gstin'].'</p>';
   $from_tradename ='<p>'.$billfrm_data['gstin_trade_name'] .'</p>';
   $from_statecode ='<p>'.$billfrm_data['gstin_state_code'].'</p>';
   $from_country   ='<p>'.$billfrm_data['country'].'</p>';
   }
else{
	 $from_gstin     ='<p>N/A</p>';
	 $from_tradename ='<p>N/A</p>';
	 $from_statecode ='<p>N/A</p>';
	 $from_country   ='<p>N/A</p>';
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
<div class="row pb-2">
    <div class="col-md-6 order-1"><h3>Sales Order</h3></div>  
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

 <div class="col-md-7 order-2 order-md-3">
     <select name="type" id="type" class="form-select d-inline-block" style="width:170px;">
        <option value="item" selected="selected">Item</option>
        <option value="non_item">Non-Item</option>
      </select> 
	   <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:170px;">
      <?php foreach ($currency_list as $value) { ?>
      <option data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>">
      <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-primary">#</div>

      <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="itmbtchCheck">
    <label class="form-check-label" for="itmbtchCheck">Item Batch</label>
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
  <div class="col-md-5 text-md-end order-4 collapse listmenu" id="listmenu">
     <a href="#" class="btn btn-success btn-sm dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
      <li>
        <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);">Apply Tax</a>
      </li>      
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
            <span class="input-group-text">Sub Supply Type:</span>
            <?php echo form_dropdown('tax_type_id', $tax_type_list, '',' id="tax_type_id" class="sale_type form-select required" required '); ?>           
            <div data-code="0" id="taxtypepopup" title="Add Description" alt="Add Description" class="btn btn-sm btn-primary">#</div>
          </div>
        </div>
		
		 <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">Auto Price:</span>
            <?php 
			$lastprice_dropdown = array(''=>'','1'=>'MRP Based','2'=>'Last Price');
			echo form_dropdown('last_price', $lastprice_dropdown, '',' id="last_price" class="sale_type form-select" '); ?>
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
		  <?php } else { ?>
		  <input type="text" name="billno" id="billno" class="form-control form-control-sm">		  
		  <?php } ?>
        </div>
      </div>
	  <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Reverse Charges:</label>
         
          <?php 
          $revrchrgs_lists=array("0"=>"No","1"=>"Yes","2"=>"ECO");
          echo form_dropdown('reverse_charges', $revrchrgs_lists, "0",' id="reverse_charges" class="sale_type form-select" '); ?>
         
        </div>
      </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">MC:</label>
                <?php echo form_dropdown('matrcntr_id', $matrcntr_dropdown, "1",' id="matrcntr_id" class="selectwidget form-control required" '); ?>
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
		  <input autocomplete="off" type="text" value="" list="party_ids" id="clone_party_id" name="clone_party_id" class="form-control form-control-sm required" required>
		  <datalist id="party_ids">
		    <?php foreach($party_dropdown as $value){ ?>
               <option data-gstin="<?php echo $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_name'] ?>" >
            <?php } ?>
		  </datalist>		  		
         
        </div>
      </div>  
      <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">POS:</label>
          <?php echo form_dropdown('pos', $states_lists, "",' id="pos" class="sale_type form-select required" required'); ?>
        </div>
      </div>
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
		<div class="col-md-4 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Invoice Type :</label><label class="input-group-text" id="invoice_type_label"></label>
		
			
		 </div>
      </div>   
	   <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
              <label class="input-group-text">Due Date:</label>
              <input type="text" name="due_date" id="due_date" value="" class="datepicker form-control form-control-sm" required>
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
        <div class="col-md-9 col-12 card p-2">
            <div class="input-group">
              <label class="input-group-text">Narration:</label> 
              <textarea  name="narration" class="form-control form-control-sm"></textarea>
            </div>
        </div>
       
	  <input type="hidden" name="hsndata" id="hsndata">	
      <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="bbbdata" id="bbbdata">
	   <input type="hidden" name="gst_paidacc_id" id="gst_paidacc_id">
      <input type="hidden" name="prdata" id="prdata">
	  <input type="hidden" name="bsd_comp_applytax_json" id="bsd_comp_applytax_json">
      <input type="hidden" name="billsndrydata" id="billsndrydata">
	  <input type="hidden" name="taxsummarydata" id="taxsummarydata">
      <input type="hidden" name="batchinfo_array" id="batchinfo_array" value="">
      <input type="hidden" name="trackinginfo_array" id="trackinginfo_array" value="">
	  
	  
      </div>
    </div>

   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12"><div class="row">
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
        <button type="reset" id="submitbtn" class="btn btn-success btn-lg">Reset</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
        
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
 <select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option  data-is_bbb="0" value="">
						</option>			
					  </select>
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
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" id="save_supplybtn">Save</button>
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


<div class="modal fade pt-5" id="add_reg_taxtype_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Tax Type(REGULAR)</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="row">
					   <div class="col-md-6">
					   <h5 class="pb-2">Bill From</h5>
					    <p class="col-12">
						 <label>FROM GSTIN</label>
					     <span class="billfrm_gstin"><?php echo $from_gstin;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM TRADE NAME</label>
					     <span class="billfrm_tradename"><?php echo $from_tradename;?></span>
                      </p> 
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					      <span class="billfrm_statecode"><?php echo $from_statecode;?></span>
                      </p>
					   <p class="col-12">
						 <label>FROM COUNTRY</label>
					      <span class="billfrm_country"><?php echo $from_country;?></span>
                      </p>
					  
					   </div>
					   <div class="col-md-6">
					    <h5 class="pb-2">Bill To</h5>
						  <p class="col-12">
						 <label>TO GSTIN</label>
					     <span class="billto_gstin"></span>
                      </p>
					  <p class="col-12">
						 <label>TO TRADE NAME</label>
					     <span class="billto_tradename"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					     <span class="billto_statecode"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO COUNTRY</label>
					     <span class="billto_country"></span>
                      </p> 
					   </div>
					   
					  </div>
					 </div>
                     
                    </div>
                  </div>
                </div>
<div class="modal fade pt-5" id="add_billtoshipto_taxtype_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Tax Type(BILL TO - SHIP TO)</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="row">
					   <div class="col-md-4">
					   <h5 class="pb-2">Bill From</h5>
					     <p class="col-12">
						 <label>FROM GSTIN</label>
					     <span class="billfrm_gstin"><?php echo $from_gstin;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM TRADE NAME</label>
					     <span class="billfrm_tradename"><?php echo $from_tradename;?></span>
                      </p> 
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					      <span class="billfrm_statecode"><?php echo $from_statecode;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM COUNTRY</label>
					      <span class="billfrm_country"><?php echo $from_country;?></span>
                      </p>
					  
					   </div>
					   <div class="col-md-4">
					    <h5 class="pb-2">Bill To</h5>
						 <p class="col-12">
						 <label>TO GSTIN</label>
					     <span class="billto_gstin"></span>
                      </p>
					  <p class="col-12">
						 <label>TO TRADE NAME</label>
					     <span class="billto_tradename"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					     <span class="billto_statecode"></span>
                      </p> 
					  <p class="col-12">
						 <label>TO COUNTRY</label>
					     <span class="billto_country"></span>
                      </p>
					   </div>
					   <div class="col-md-4">
					    <h5 class="pb-2">Ship To</h5>
						 <p class="col-12">
						 <label>TO ADDRESS 1</label>
					     <input type="text" name="shipto2_addr1"  value="" maxlength="255" class="form-control" >
                      </p>
					  <p class="col-12">
						 <label>TO ADDRESS 2</label>
					     <input type="text" name="shipto2_addr2" value="" maxlength="255" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>TO PLACE</label>
					     <input type="text" name="shipto2_place" value="" maxlength="255" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>TO PINCODE</label>
					     <input type="text" name="shipto2_pin" value="" maxlength="255" class="form-control" minlength="3" required="">
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					      <?php echo form_dropdown('shipto2_state_code', $states_lists, "",' class="sale_type form-select" '); ?>
                      </p> 
					   </div>
					   
					  </div>
					   
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                      </div>
                    </div>
                  </div>
                </div>	

<div class="modal fade pt-5" id="add_billfrmdisfrm_taxtype_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Tax Type(BILL FROM – DISPATCH FROM)</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="row">
					   <div class="col-md-4">
					   <h5 class="pb-2">Bill From</h5>
					    <p class="col-12">
						 <label>FROM GSTIN</label>
					     <span class="billfrm_gstin"><?php echo $from_gstin;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM TRADE NAME</label>
					     <span class="billfrm_tradename"><?php echo $from_tradename;?></span>
                      </p> 
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					      <span class="billfrm_statecode"><?php echo $from_statecode;?></span>
                      </p>
					    <p class="col-12">
						 <label>FROM COUNTRY</label>
					     <span class="billfrm_country"><?php echo $from_country;?></span>
                      </p>
					  
					   </div>
					   <div class="col-md-4">
					    <h5 class="pb-2">Dispatch From</h5>
						
			<?php  $matrcntr_dropdown['']='Choose MC';
			echo form_dropdown('mclistid3', $matrcntr_dropdown, "",' class="sale_type form-select" '); ?>			
						<p class="col-12">
						 <label>FROM ADDRESS 1</label>
					     <input type="text" name="dispfrm3_addr1" style="pointer-events:none;" readonly value="" maxlength="255" class="form-control">
                      </p>
					  <p class="col-12">
						 <label>FROM ADDRESS 2</label>
					     <input type="text" name="dispfrm3_addr2" style="pointer-events:none;" readonly value="" maxlength="255" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>FROM PLACE</label>
					     <input type="text" name="dispfrm3_place" style="pointer-events:none;" readonly value="" maxlength="255" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>FROM PINCODE</label>
					     <input type="text" name="dispfrm3_pin" style="pointer-events:none;" readonly value="" maxlength="255" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					     <?php echo form_dropdown('dispfrm3_state_code', $states_lists, "",'style="pointer-events:none;"  class="sale_type form-select" '); ?>
                     
                      </p> 
					   </div>
					   <div class="col-md-4">
					    <h5 class="pb-2">Bill To</h5>
						<p class="col-12">
						 <label>TO GSTIN</label>
					     <span class="billto_gstin"></span>
                      </p>
					  <p class="col-12">
						 <label>TO TRADE NAME</label>
					     <span class="billto_tradename"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					     <span class="billto_statecode"></span>
                      </p> 
					  <p class="col-12">
						 <label>TO COUNTRY</label>
					     <span class="billto_country"></span>
                      </p>
					   </div>
					   
					  </div>
					   
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
 <select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option  data-gstin="" data-is_bbb="0" value=""></option>			
					  </select>
					   <input type="hidden" name="invoice_type" id="invoice_type">
<div class="modal fade pt-5" id="add_bsbd_taxtype_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Tax Type(BILL TO – SHIP TO – BILL FROM – DISPATCH FROM)</h4>
                        <button type="button" class="btn-close close_btn"  data-bs-dismiss="modal"  aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
					  <div class="row">
					  <div class="col-md-3">
					    <h5 class="pb-2">Bill To</h5>
						<p class="col-12">
						 <label>TO GSTIN</label>
					     <span class="billto_gstin"></span>
                      </p>
					  <p class="col-12">
						 <label>TO TRADE NAME</label>
					     <span class="billto_tradename"></span>
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					     <span class="billto_statecode"></span>
                      </p> 
					  <p class="col-12">
						 <label>TO COUNTRY</label>
					     <span class="billto_country"></span>
                      </p>
					   </div>
					   <div class="col-md-3">
					    <h5 class="pb-2">Ship To</h5>
						 <p class="col-12">
						 <label>TO ADDRESS 1</label>
					     <input type="text" name="shipto4_addr1"  value="" maxlength="255" class="form-control" >
                      </p>
					  <p class="col-12">
						 <label>TO ADDRESS 2</label>
					     <input type="text" name="shipto4_addr2" value="" maxlength="255" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>TO PLACE</label>
					     <input type="text" name="shipto4_place" value="" maxlength="255" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>TO PINCODE</label>
					     <input type="text" name="shipto4_pin" value="" maxlength="255" class="form-control" minlength="3" required="">
                      </p> 
					   <p class="col-12">
						 <label>TO STATE CODE</label>
					      <?php echo form_dropdown('shipto4_state_code', $states_lists, "",' class="sale_type form-select" '); ?>
                      </p>  
					   </div>
					   
					   
					   
					   <div class="col-md-3">
					   <h5 class="pb-2">Bill From</h5>
					   <p class="col-12">
						 <label>FROM GSTIN</label>
					     <span class="billfrm_gstin"><?php echo $from_gstin;?></span>
                      </p>
					  <p class="col-12">
						 <label>FROM TRADE NAME</label>
					     <span class="billfrm_tradename"><?php echo $from_tradename;?></span>
                      </p> 
					  
					 
					  
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					      <span class="billfrm_statecode"><?php echo $from_statecode;?></span>
                      </p>
					  <p class="col-12">
						 <label>TO COUNTRY</label>
					     <span class="billfrm_country"><?php echo $from_country;?></span>
                      </p>
					  
					   </div>
					   <div class="col-md-3">
					    <h5 class="pb-2">Dispatch From</h5>
						<?php  $matrcntr_dropdown['']='Choose MC';
						echo form_dropdown('mclistid4', $matrcntr_dropdown, "",' class="sale_type form-select" '); ?>
						<p class="col-12">
						 <label>FROM ADDRESS 1</label>
					     <input type="text" name="dispfrm4_addr1" value="" maxlength="255" readonly style="pointer-events:none;" class="form-control">
                      </p>
					  <p class="col-12">
						 <label>FROM ADDRESS 2</label>
					     <input type="text" name="dispfrm4_addr2" value="" maxlength="255" readonly style="pointer-events:none;" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>FROM PLACE</label>
					     <input type="text" name="dispfrm4_place" value="" maxlength="255" readonly style="pointer-events:none;" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>FROM PINCODE</label>
					     <input type="text" name="dispfrm4_pin" value="" maxlength="255" readonly style="pointer-events:none;" class="form-control">
                      </p> 
					   <p class="col-12">
						 <label>FROM STATE CODE</label>
					     <?php echo form_dropdown('dispfrm4_state_code', $states_lists, "",' readonly style="pointer-events:none;" class="sale_type form-select" '); ?>
                     
                      </p> 
					   </div>
					  
					   
					  </div> 
					   
					  	
                            </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
</form>

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
<!-- List View The Modal Ends Here -->



<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=500;$i++)
$tax_json_data[] =array("tax_item_id"=>"","pq_cellattr"=>array("tax_amt"=>array("title"=>"")),"id"=>'',"cess_rate"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','cess'=>'','sgst'=>'','total_tax'=>'');

?>

<style>
    .boldcell{font-weight:700;}
</style>

<script>
var ugst_states   = ['35','04','26','25','31','38','34','97'];
var bst_taxables  = <?php echo $bsd_json_file;?>;
var bo_gstin_type = '<?php echo $bo_gstin_type;?>';
var goods_rate    = '1';
var services_rate = '6';
var bst_notax     = <?php echo $bsd_json_file;?>;
var bsd_json_file  = <?php echo $bsd_json_file;?>;
var bo_state_code = '<?php echo sprintf('%02d', $bo_state_code);?>';
var tax_dataModel = {"data":<?php echo json_encode($tax_json_data);?>};

function get_item_grid()
    {
        var data = []
        for(var i=1; i<=500; i++){
            data.push({"item_id": '',"item_name": '','pq_cellattr': { "item_name": { title: ""}},'item_qty': '','description': '','item_unit': '','item_unit_id': '','item_price': '','item_amount': '','item_amounttxs':''});
        }
        return data;
    }
    function get_sundry_grid()
    {
        var data = []
        for(var i=1; i<=50; i++){
            data.push({"billsundry_id": '',"billsundry_name": '','billsundry_rate': '','billsundry_amount': ''});
        }
        return data;
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
		show_taxsummary_items();
			stop_loader();
			
	 }		 
 });
$("#voucher_date").on("change",function(){
	var voucher_date     = $(this).val();	
	var vouchermethod = $('select[name="voucher_series"] option:selected').attr('data-srsmethod');
	var series_id = $('select[name="voucher_series"] option:selected').val();
	invoice_type_changes();
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
		  
function grid_total_item(obj,currency='')
    {
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
 
            data.forEach(function(row){
                if(row.item_id != '' && row.item_id != undefined && row.item_amount != '' && row.item_amount != undefined)
                {
				  if(currency=='€' || currency=='$'){
					total += parseAmount(row.item_amountfc); 
				 }
				 else if(currency=='memo'){
					total += parseAmount(row.memo_amt); 
				 }
				 else{
					total += parseAmount(row.item_amount);					
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
let inputd = document.getElementById('clone_party_id');
   let timeout = null;
  inputd.addEventListener('keyup', function (e) {
    //clearTimeout(timeout);
    //timeout = setTimeout(function () {		
	var val = inputd.value;	
		
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
    //}, 6000);
});
	
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
var billto_info='';

$(document).on("change",'[name="mclistid3"]',function(){
	$('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr1"]').val('');
	$('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr2"]').val('');
	$('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_place"]').val('');
	$('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_pin"]').val('');
	$('#add_billfrmdisfrm_taxtype_modal select[name="dispfrm3_state_code"]').val('');
	 var mc3_id = $(this).val();
	 if(mc3_id=='custom'){
	         $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr1"]').removeAttr('style readonly');
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr2"]').removeAttr('style readonly');
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_place"]').removeAttr('style readonly');
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_pin"]').removeAttr('style readonly');
			 $('#add_billfrmdisfrm_taxtype_modal select[name="dispfrm3_state_code"]').removeAttr('style readonly');
	}else{
	if(mc3_id>0){
	$('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr1"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr2"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_place"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_pin"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_billfrmdisfrm_taxtype_modal select[name="dispfrm3_state_code"]').attr({'style':'pointer-events:none;','ready':true});	
	 $.get(baseurl+"/admin/ajax/mc_info/"+mc3_id, function(mc_res_data){
		 if(mc_res_data){
			  var mc_res_data	= $.parseJSON(mc_res_data);
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr1"]').val(mc_res_data.mat_cent_add1);
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_addr2"]').val(mc_res_data.mat_cent_add2);
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_place"]').val(mc_res_data.mat_cent_city);
			 $('#add_billfrmdisfrm_taxtype_modal input[name="dispfrm3_pin"]').val(mc_res_data.mat_cent_pin);
			 $('#add_billfrmdisfrm_taxtype_modal select[name="dispfrm3_state_code"]').val(mc_res_data.state_code);
		 }
		 
	 });
}}
});

$(document).on("change",'[name="mclistid4"]',function(){
	 $('#add_bsbd_taxtype_modal input[name="dispfrm4_addr1"]').val('');
	 $('#add_bsbd_taxtype_modal input[name="dispfrm4_addr2"]').val('');
	 $('#add_bsbd_taxtype_modal input[name="dispfrm4_place"]').val('');
	 $('#add_bsbd_taxtype_modal input[name="dispfrm4_pin"]').val('');
	 $('#add_bsbd_taxtype_modal select[name="dispfrm4_state_code"]').val('');
	
	
	 var mc4_id = $(this).val();
	 if(mc4_id=='custom'){
	         $('#add_bsbd_taxtype_modal input[name="dispfrm4_addr1"]').removeAttr('style readonly');
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_addr2"]').removeAttr('style readonly');
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_place"]').removeAttr('style readonly');
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_pin"]').removeAttr('style readonly');
			 $('#add_bsbd_taxtype_modal select[name="dispfrm4_state_code"]').removeAttr('style readonly');
	}else{
	if(mc4_id>0){
		$('#add_bsbd_taxtype_modal input[name="dispfrm4_addr1"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_addr2"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_place"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_pin"]').attr({'style':'pointer-events:none;','ready':true});
			 $('#add_bsbd_taxtype_modal select[name="dispfrm4_state_code"]').attr({'style':'pointer-events:none;','ready':true});
	 $.get(baseurl+"/admin/ajax/mc_info/"+mc4_id, function(mc_res_data){
		 if(mc_res_data){
			  var mc_res_data	= $.parseJSON(mc_res_data);
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_addr1"]').val(mc_res_data.mat_cent_add1);
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_addr2"]').val(mc_res_data.mat_cent_add2);
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_place"]').val(mc_res_data.mat_cent_city);
			 $('#add_bsbd_taxtype_modal input[name="dispfrm4_pin"]').val(mc_res_data.mat_cent_pin);
			 $('#add_bsbd_taxtype_modal select[name="dispfrm4_state_code"]').val(mc_res_data.state_code);
		 }
		 
	 });
}}
});

$(document).on("click","#taxtypepopup",function(){
	 var party_id = $('#party_id').val();

	 if(party_id>0){
	 $.get(baseurl+"/admin/ajax/party_gst_info/"+party_id, function(res_data){
      var party_gst_info	= $.parseJSON(res_data);
	 
	 var billto_gstin     = party_gst_info.acc_gstin;
	 var billto_tradename = party_gst_info.acc_trade_name;
	 var billto_statecpde = party_gst_info.statecode;
	 var billto_country   = party_gst_info.country;
	 $(".billto_gstin").html("<p>"+billto_gstin+"</p>");
	 $(".billto_tradename").html("<p>"+billto_tradename+"</p>");
	 $(".billto_statecode").html("<p>"+billto_statecpde+"</p>");
	 $(".billto_country").html("<p>"+billto_country+"</p>");
	  
  });
	 }
	 
	 var tax_type_id = $('select[name="tax_type_id"] option:selected').val();
	 if(tax_type_id=='1'){
		$("#add_reg_taxtype_modal").modal("show"); 
	 }
	if(tax_type_id=='2'){
		$("#add_billtoshipto_taxtype_modal").modal("show");
	}
	if(tax_type_id=='3'){
		$("#add_billfrmdisfrm_taxtype_modal").modal("show");
	}
	if(tax_type_id=='4'){
		$("#add_bsbd_taxtype_modal").modal("show");
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

var tax_colModel = [
            { title: "HSN / SAC", dataIndx: "tax_hsn_sac",editable: false, width: 100},            
			{ title: "TAX RATE", dataIndx: "tax_rate",editable: false, width: 100,render: function( ui ) {
                    var rd = ui.rowData;
					 return rd.tax_rate_string;
                                      
                }
			 },
            { title: "TAXABLE VALUE", width: 100, dataType: "float", dataIndx: "tax_amt",cls: 'pq-drop-icon pq-side-icon',render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.tax_amt != ''){
                        rd.tax_amt = parseAmount(rd.tax_amt);
                        return formatAmount(rd.tax_amt);   
                    }
                    return '';
                }
			},
            { title: "IGST", width: 100,  dataIndx: "igst" ,dataType: "float",format: '##,###.00',render: function( ui ) {
                    var rd = ui.rowData;					
					var party_stcode = $('select[name="pos"] option:selected').val();
                   
					if(rd.igst != '' && party_stcode==bo_state_code){
                         return '';
                    }else if(rd.igst != '' && party_stcode!=bo_state_code){
                        rd.igst = parseAmount(rd.igst);
                        return formatAmount(rd.igst);   
                    }else
						 return '';
                   
                }},
            { title: "CGST", width: 100,  dataIndx: "cgst" ,dataType: "float",format: '##,###.00',render: function( ui ) {
                    var rd = ui.rowData;
					var party_stcode = $('select[name="pos"] option:selected').val();
                   
                    if(rd.cgst != '' && party_stcode==bo_state_code){
                        rd.cgst = parseAmount(rd.cgst);
                        return formatAmount(rd.cgst);   
                    }
					else if(rd.cgst != '' && party_stcode!=bo_state_code){
                        return ''; 
                    }else
						return '';
                    
                }},
            { title: "SGST", width: 100,  dataIndx: "sgst" ,hidden :false,dataType: "float",format: '##,###.00',render: function( ui ) {
                    var rd = ui.rowData;
					var party_stcode = $('select[name="pos"] option:selected').val();
                   
                    if(rd.sgst != '' && party_stcode==bo_state_code){
                        rd.sgst = parseAmount(rd.sgst);
                        return formatAmount(rd.sgst);   
                    }
					else if(rd.sgst != '' && party_stcode!=bo_state_code){
                       return '';
                    }else
                    return '';
                }},
				
			 { title: "CESS", width: 100,  dataIndx: "cess" ,dataType: "float",format: '##,###.00',render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.cess >0){                       
                        return formatAmount(rd.cess);   
                    }
					else
                    return '';
                }},
			{ title: "TOTAL TAX", width: 100,  dataIndx: "total_tax" ,dataType: "float",format: '##,###.00',render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.total_tax != ''){
                        rd.total_tax = parseAmount(rd.total_tax);
                        return formatAmount(rd.total_tax);   
                    }
                    return ''; 
                }}				
            ];	
function item_bsd_totals(){
        var items_total=0;
		var items_fy_total=0;
		var items_memo_total=0; 
        var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
		
        items_total += grid_total_item($('#grid_search'));		
        items_total += grid_total_bsd($('#billsundry_search'));
       
		items_fy_total += grid_total_item($('#grid_search'),currency_symbol);
		items_fy_total += grid_total_bsd($('#billsundry_search'),currency_symbol);
		
		
		items_memo_total += grid_total_item($('#grid_search'),'memo');
		items_memo_total += grid_total_bsd($('#billsundry_search'),'memo');
			
		$("#invoice_total").html(formatAmount(items_total)+" , "+formatAmount(items_fy_total,currency_symbol)+" , "+formatAmount(items_memo_total));
		
}
/***********************  Start Memorandum ************************/
function memoChanged(billsundry_colModel,colModel,ischecked)
    {	
     if(ischecked){
				 billsundry_colModel[3].hidden=false;
                billsundry_colModel[3].width= 170;
				billsundry_colModel[3].title='MEMO(₹)';
                $("#billsundry_search").pqGrid( "option", "colModel", billsundry_colModel );             
                $("#billsundry_search").pqGrid( {autoFit: true} );
                $("#billsundry_search").pqGrid("refreshCM");
                $("#billsundry_search").pqGrid("refresh"); 
			    colModel[8].hidden=false;
                colModel[8].width= 180;
				colModel[8].title='MEMO(₹)';
                $("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refreshCM");
                $("#grid_search").pqGrid("refresh");	
		}               
        else{
				 billsundry_colModel[3].hidden=true;
                billsundry_colModel[3].width= 170;
				$("#billsundry_search").pqGrid( "option", "colModel", billsundry_colModel);             
                $("#billsundry_search").pqGrid( {autoFit: true} );
                $("#billsundry_search").pqGrid("refreshCM");
                $("#billsundry_search").pqGrid("refresh"); 				
				colModel[8].hidden=true;
                colModel[8].width= 180;
				$("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refreshCM");
                $("#grid_search").pqGrid("refresh");	
		}
            
    }	
/*********************  Memorandum  END          *********************/
/***********************  Start Tax Inclusive ************************/
function TaxInsChanged(billsundry_colModel,colModel,ischecked)
    {	
     if(ischecked){
				colModel[5].hidden=false;
                colModel[5].width= 180;
				colModel[5].title='AMOUNT(₹)';
				
				colModel[7].width= 180;
				colModel[7].title='AMOUNT INC.(₹)';
				colModel[7].editable=false;
				
                $("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
               $("#grid_search").pqGrid('refreshDataAndView');	
		}               
        else{
							
				colModel[5].hidden=true;
                colModel[5].width= 180;
				
				colModel[7].width= 180;
				colModel[7].title='AMOUNT(₹)';
				colModel[7].editable=true;
				
				$("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid('refreshDataAndView');	
		}
		show_taxsummary_items();
		item_bsd_totals();
            
    }	
/*********************  Tax Inclusive  END          *********************/
/*********************  FC Rates   Start         *********************/
function validatefcrate(e,a){return IsNumericFCRate(a,e.keyCode?e.keyCode:e.charCode)}	
var isShiftt = false;
function IsNumericFCRate(_,e){if(16==e&&(isShiftt=!0),(!(e>=48)||!(e<=57))&&8!=e&&!(e<=37)&&!(e<=39)&&(!(e>=96)||!(e<=105))&&190!=e&&110!=e||!1!=isShiftt||_.includes(".")&&(190==e||110==e))return!1;if(_.includes(".")){if(_.split(".")[1].length>=8&&(e>=96&&e<=105||e>=48&&e<=57))return!1}else if(_.length>=6&&(e>=96&&e<=105||e>=48&&e<=57))return!1;return!0}
$(".clsoefcratemodal").on("click",function(){$("#view_fcrates_modal #fcy_voucher_no").html(""),$("#view_fcrates_modal #fcy_voucher_date").html(""),$("#view_fcrates_modal").modal("hide")});
$("#save_forex_rates").on("click",function(){
	var currency_id = $('select[name="currency_id"] option:selected').val();	
	var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
	if(currency_id >1 && fcy_forex_rate >0 && $('#taxInclusive').is(":checked")==false){
			
			 var data = $('#grid_search').pqGrid('option', 'dataModel.data');	
			 $.each(data, function(index,obj){
					if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
						{
						  
						  var item_amount   = parseAmount((obj.item_qty*obj.item_price)/fcy_forex_rate);
						  data[index]['item_price']     = obj.item_price;
						  data[index]['item_amount']    = item_amount;
					 	  data[index]['item_amounttxs'] = parseAmount(0);
						  }
					}); 
			 }
			 
			 $('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');	
				show_taxsummary_items();				
				refreshbsd();
	
	$("#view_fcrates_modal").modal("hide")});
$("#view_fcrates_modal").on("hidden.bs.modal",function(){""==$("#view_fcrates_modal #fcy_forex_rate").val()&&($("#currency_id option").removeAttr("selected").filter('[value="1"]').attr("selected",!0),$("#view_fcrates_modal #fcy_forex_rate").val(""),$("#view_fcrates_modal #fcy_voucher_no").html(""),$("#view_fcrates_modal #fcy_voucher_date").html(""))});



function calculate_fc_rates(currency_id,billsundry_colModel,colModel,currency_symbol)
{	
 if(parseInt(currency_id) >1){	
				//$("#view_fcrates_modal #fcy_forex_rate").val("");
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
			    colModel[6].hidden=false;
                colModel[6].width= 180;
				colModel[6].title='AMOUNT('+currency_symbol+')';
				
				colModel[4].width= 180;
				colModel[4].title='PRICE('+currency_symbol+')';
				
				
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
				colModel[6].hidden=true;
                colModel[6].width= 180;
				
				colModel[4].width= 180;
				colModel[4].title='PRICE';
				
				$("#grid_search").pqGrid( "option", "colModel", colModel );             
                $("#grid_search").pqGrid( {autoFit: true} );
                $("#grid_search").pqGrid("refresh"); 
			 }	
}
/*********************  FC Rates  END          *********************/	
	
function show_taxsummary_items(){
	invoice_type_changes();
		var supplytype = $('select[name="supply_type"] option:selected').val();
		/* if(supplytype=='3' || supplytype=='5')// no tax will applied on this type of supply types
		{
		 var taxsummary_json=[];
		 var bsd_taxsummary_json=[];
	     for(var i=1;i<=50;i++){		 
	     taxsummary_json.push({'tax_cat_id':'','billsundry_name':'','bl_nature':'','tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess_basis":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	     }
        $("#taxgrid_search").pqGrid('option', 'dataModel.data',taxsummary_json);
        $("#taxgrid_search").pqGrid('refreshDataAndView');	
		 
		for(var i=1;i<=50;i++){		 
	     bsd_taxsummary_json.push({'tax_cat_id':'','bl_nature':'','is_tax_account':0,'tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	    }	
	    $("#billsundry_search").pqGrid('refreshDataAndView');
	    $("#taxgrid_search").pqGrid('option', 'dataModel.data', bsd_taxsummary_json);
        $("#taxgrid_search").pqGrid('refreshDataAndView');	
		return false;	
		} */
	    var item_taxsummary_json = {};
		var tooltip_taxsummary_json = {};
		 var hsn_taxsummary_json = {};
	    var total_tax=0;
	    var items_total_amnt=0;
	    var stcode      = $('select[name="pos"] option:selected').val();
        var data        = $('#grid_search').pqGrid('option', 'dataModel.data');
	    var currency_id = $('select[name="currency_id"] option:selected').val();
					//console.log(data);
					
	   $.each(data, function(index,obj){
				
            if(obj.tax_cat_id != '' && obj.item_id != '' && obj.item_id != undefined)
            {	
		      items_total_amnt=parseAmount(items_total_amnt)+parseAmount(obj.item_amount);
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
				}
				else{	
				if(parseInt(currency_id)==1){
					var tax_amt = obj.item_qty * parseAmount(obj.item_price);
                       
			   }  else{
					var tax_amt = (obj.item_amount);
				  } 

				}
				 
				
				var cgst_tt = parseAmount(igst_rate_val/2*(tax_amt)/100);
				var sgst_tt = parseAmount(igst_rate_val/2*(tax_amt)/100)
				var igst_tt = parseAmount(igst_rate_val*(tax_amt)/100);
				if(obj.cess_basis=="1" && tax_amt>0){
				 var cess_tt = parseAmount(obj.cess_rate*(tax_amt)/100);
				 var show_mrp_label= '';
				}
			    else if(obj.cess_basis=="2"  && tax_amt>0){
				 var cess_tt = parseAmount(obj.cess_rate*(obj.item_mrp*obj.item_qty)/100);	
				 var show_mrp_label= ' MRP';
				}
				else{
				 var cess_tt = 0;	 
				 var show_mrp_label='';
				}
				
				if(supplytype=='3' || supplytype=='5' || supplytype=='14' || supplytype=='15' || supplytype=='16')// no tax will applied on this type of supply types
		{   var cgst_tt=0;
		var sgst_tt =0;
		var igst_tt =0;
			tax_rate_string ='ZERO RATED';
		}
		else{
				if(parseAmount(igst_tt) >0 && parseAmount(cess_tt) >0 && parseAmount(obj.cess_rate)>0)
					tax_rate_string = parseAmount(igst_rate_val)+"% + "+parseAmount(obj.cess_rate)+"% "+show_mrp_label;
				else
				   tax_rate_string = parseAmount(igst_rate_val)+"%";	
		}
		
				var sel_party = $('select[name="pos"] option:selected').val();
				
				 if(supplytype=='3' || supplytype=='5' || supplytype=='14' || supplytype=='15' || supplytype=='16')// no tax will applied on this type of supply types
		{
			total_tax=0;
		}else{
				if(sel_party==bo_state_code){
				var igst_tt =0;
				 total_tax = parseAmount(sgst_tt)+parseAmount(cgst_tt)+parseAmount(cess_tt);
				}
				else{
					 var cgst_tt=0;
		        var sgst_tt =0;
				 total_tax = parseAmount(igst_tt)+parseAmount(cess_tt);	
				}				
		}
		
				if(hsn_taxsummary_json[obj.item_id]){
                    if(hsn_taxsummary_json[obj.item_id][obj.item_unit_id]){						
						hsn_taxsummary_json[obj.item_id][obj.item_unit_id].push({'item_unit':obj.item_unit,'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':tax_amt,'tax_rate':igst_rate_val,'tax_rate_string':tax_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst_tt,'cess':cess_tt,'item_qty':obj.item_qty,'item_price':obj.item_price,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax});						
					 
					  }
					  else{
					   hsn_taxsummary_json[obj.item_id][obj.item_unit_id]=[{'item_unit':obj.item_unit,'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':tax_amt,'tax_rate':igst_rate_val,'tax_rate_string':tax_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst_tt,'cess':cess_tt,'item_qty':obj.item_qty,'item_price':obj.item_price,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax}];                          
					    				  
					  }
				}else{
				    hsn_taxsummary_json[obj.item_id]={
                                [obj.item_unit_id] : [{'item_unit':obj.item_unit,'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':tax_amt,'tax_rate':igst_rate_val,'tax_rate_string':tax_rate_string,'cess_rate':obj.cess_rate,'cess_basis':obj.cess_basis,'igst':igst_tt,'cess':cess_tt,'item_qty':obj.item_qty,'item_price':obj.item_price,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax}]
                     };		
				   
				}
				
                if(item_taxsummary_json[obj.tax_cat_id]){
                    if(item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]){						
						item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac].push({'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':tax_amt,'tax_rate':igst_rate_val,'tax_rate_string':tax_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst_tt,'cess':cess_tt,'item_qty':obj.item_qty,'item_price':obj.item_price,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax});						
						tooltip_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac].push({'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_item_name':obj.item_name,'item_qty':obj.item_qty,'item_price':obj.item_price});						
					  
					  }
					  else{
					   item_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]=[{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':tax_amt,'tax_rate':igst_rate_val,'tax_rate_string':tax_rate_string,'cess_basis':obj.cess_basis,'cess_rate':obj.cess_rate,'igst':igst_tt,'cess':cess_tt,'item_qty':obj.item_qty,'item_price':obj.item_price,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax}];                          
					   tooltip_taxsummary_json[obj.tax_cat_id][obj.item_hsn_sac]=[{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_item_name':obj.item_name,'item_qty':obj.item_qty,'item_price':obj.item_price}];
                        				  
					  }
				}else{
				    item_taxsummary_json[obj.tax_cat_id]={
                                [obj.item_hsn_sac] : [{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_hsn_sac':obj.item_hsn_sac,'tax_amt':tax_amt,'tax_rate':igst_rate_val,'tax_rate_string':tax_rate_string,'cess_rate':obj.cess_rate,'cess_basis':obj.cess_basis,'igst':igst_tt,'cess':cess_tt,'item_qty':obj.item_qty,'item_price':obj.item_price,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax}]
                     };		
				   tooltip_taxsummary_json[obj.tax_cat_id] ={
                                [obj.item_hsn_sac] : [{'tax_cat_id':obj.tax_cat_id,'tax_item_id':obj.item_id,'tax_item_name':obj.item_name,'item_qty':obj.item_qty,'item_price':obj.item_price}]
                     };				
				}
			}
		});
		
		var taxsummary_json = [];		
		$.each(item_taxsummary_json, function(index,obj){
			//console.log("main loop"+index+"==="+obj);
			var tax_hsn_sac='';
			var tax_rate ='';
			var tax_rate_string='';
			var cess_rate ='';
			var cgst ='';
			var sgst ='';
			var igst ='';	
            var cess ='';			
			var tax_amt='';
			var cess_basis  = '';
			var tax_cat_id ='';
			var tax_item_id = '';
			var total_tax   = 0;		
			var taxable_tooltp_html="";
			var taxable_tooltp_array=[];
			taxable_tooltp_html +='<table width="500px;"><tr><td>ITEM LIST</td></tr>';
			$.each(obj, function(index1,obj1){//categ
				var sum_tax_amt=0;
				var sum_igst=0;
				var sum_sgst=0;
				var sum_cgst=0;
				var sum_tax_rate=0;
				var sum_tax_string=0;
				var sum_cess=0;
				var sum_tax=0;
				
				$.each(obj1, function(index2,obj2){ //hsn
				tax_item_id = obj2.tax_item_id;
				
				/***  Tax Summary ToolTip ****/			
				
				if(typeof tooltip_taxsummary_json[index] !="undefined"){
					var taxsummary_tooltip_data = tooltip_taxsummary_json[index][index1];
					$.each(taxsummary_tooltip_data, function(tpindex,tprow){
						taxable_tooltp_array[tprow.tax_item_id]=[{'tax_item_id':tprow.tax_item_id,'tax_item_name':tprow.tax_item_name,'item_qty':tprow.item_qty,'item_price':tprow.item_price}];
					 
					});
				}
				
				tax_amt  = obj2.tax_amt;				
				igst     = obj2.igst;
				sgst     = obj2.sgst;
				cgst     = obj2.cgst;
				tax_rate = obj2.tax_rate;
				tax_rate_string = obj2.tax_rate_string;
				cess_rate = obj2.cess_rate;
				total_tax = obj2.total_tax;
				cess      = obj2.cess;
				cess_basis = obj2.cess_basis;
				tax_cat_id = obj2.tax_cat_id,
				
				sum_tax_amt   += parseFloat(tax_amt);
				sum_igst      += parseFloat(igst);
				sum_sgst      += parseFloat(sgst);
				sum_cgst      += parseFloat(cgst);
				sum_tax_rate  += parseFloat(tax_rate);				
				tax_hsn_sac   = obj2.tax_hsn_sac;
				sum_cess      += parseFloat(cess);
				sum_tax       += parseFloat(total_tax);
				})
				if(taxable_tooltp_array){
					$.each(taxable_tooltp_array, function(tpindex,tprow){
						$.each(tprow, function(tpindex1,tprow1){
							taxable_tooltp_html +='<tr><td>'+tprow1.tax_item_name+'</td></tr>';	
						});
					});
				}				
				taxable_tooltp_html +='</table>';				
				taxsummary_json.push({'tax_cat_id':index,'billsundry_name':'','bl_nature':'','tax_item_id':tax_item_id,"tax_hsn_sac":tax_hsn_sac,"tax_amt":sum_tax_amt,"cess_rate":cess_rate,"cess_basis":cess_basis,"tax_rate":tax_rate,"tax_rate_string":tax_rate_string,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
				});		
		});	 
		
		
		
	for(var i=1;i<=50;i++){		 
	  taxsummary_json.push({'tax_cat_id':'','billsundry_name':'','bl_nature':'','tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess_basis":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	 }
	
	$("#taxgrid_search").pqGrid('option', 'dataModel.data', taxsummary_json);
    $("#taxgrid_search").pqGrid('refreshDataAndView');			
	
	 var reverse_charges = $('select[name="reverse_charges"] option:selected').val();
	 if(reverse_charges==0){
	  show_taxsummary_bsdcng(item_taxsummary_json,tooltip_taxsummary_json);	
	  apply_tax();
	  }
	  $("#hsndata").val(JSON.stringify(hsn_taxsummary_json));
	}
	
function refreshbsd(){
	var stcode = $('select[name="pos"] option:selected').val();
	var data = $('#taxgrid_search').pqGrid('option', 'dataModel.data');
	var ap_cgst_total    =0;
	var ap_igst_total    =0;
	var ap_sgst_total    =0;
	var ap_cess_total    =0;
	
	$.each(data, function(index,data){
		ap_cgst_total +=data.cgst;
		ap_igst_total +=data.igst;
		ap_sgst_total +=data.sgst; 
		ap_cess_total +=data.cess;
	});
var bsd_applytax_json=[];
var bsdata = $('#billsundry_search').pqGrid('option', 'dataModel.data');
$.each(bsdata, function(index,row){				
			  		if(stcode==bo_state_code){
						 if(row.bl_nature=="33") //CGST	 OR SGST
						   {
							 $("#billsundry_search").pqGrid( "updateRow",{ rowIndx: index, row: {'billsundry_amount': ap_cgst_total}});	
						   }
						 if( row.bl_nature=="34" || row.bl_nature=="195") //CGST	 OR SGST
						   {
						     $("#billsundry_search").pqGrid( "updateRow",{ rowIndx: index, row: {'billsundry_amount': ap_sgst_total}});	
						   }
						 
		            }else{
						if( row.bl_nature=="35") //CGST	 OR SGST
						   {
						     $("#billsundry_search").pqGrid( "updateRow",{ rowIndx: index, row: {'billsundry_amount': ap_igst_total}});	
						   }
					}
				if( row.bl_nature=="36") //CESS
						   {
						     $("#billsundry_search").pqGrid( "updateRow",{ rowIndx: index, row: {'billsundry_amount': ap_cess_total}});	
						   }	
					
					
		       
		    });

    $("#billsundry_search").pqGrid('refreshDataAndView');	
		
}
/***************** Refresh Grid On Refresh Button  ****************/
$("#refresh_grid").on("click",function(){
	 refreshbsd();
	
});
/*****************************************************************/

var updted_bsd_file =[]; 
/*************** start of applytax_calculate  *********************/
function apply_tax(){
	var create_auto_accounts= 0;
	var stcode              = $('select[name="pos"] option:selected').val();
	var bsd_applytax_json   = [];
	var bsd_comp_applytax_json   = [];
	
	var items_footer_total  = $( "#taxgrid_search" ).pqGrid( "option", "summaryData" );
	var item_footer_total   = 0;
	var ap_cgst_total       = 0;
	var ap_igst_total       = 0;
	var ap_sgst_total       = 0;
	var ap_cess_total       = 0;
	
	// taxsummary all tax account No items daat and put them into bill sundry data 
	var taxsmdata = $('#taxgrid_search').pqGrid('option', 'dataModel.data');
	
	$.each(taxsmdata, function(index,rows){
		if(rows.is_tax_account=="0" && rows.bl_nature!=''){			
		bsd_applytax_json.push({'billsundry_id':rows.tax_item_id,'billsundry_amount':rows.tax_amt,'billsundry_name':rows.billsundry_name,'bl_nature':rows.bl_nature,'is_tax_account':0,'tax_item_id':rows.tax_item_id,"bl_hsn_sac":rows.tax_hsn_sac,"tax_amt":rows.tax_amt,"b_tx_cess_rte":rows.b_tx_cess_rte,"bl_tax_rate":rows.tax_rate,"igst":rows.tax_rate,"cess":0,"cgst":0,"sgst":0,'total_tax':0});	
		}
	});
	var i;
	for(i = 0; i < items_footer_total.length;i++) {
	    item_footer_total = parseFloat(items_footer_total[i]['total_tax']);
		ap_cgst_total += items_footer_total[i]['cgst'];
		ap_igst_total += items_footer_total[i]['igst'];
		ap_sgst_total += items_footer_total[i]['sgst'];	
		ap_cess_total += items_footer_total[i]['cess'];			
	   }	   
	      
    if(item_footer_total >0){	 
    async function loadBsdFile() {
    try {
        const response = await fetch(baseurl + "/admin/billsundry/load_bsd_file", {
            method: 'GET',
            headers: {
                // Any additional headers you might need
            },
            cache: 'no-cache', // Avoid caching the request
        });

        // Check if the response is successful
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }

        // Parse the JSON response
        const data = await response.json();

        // Return the data to be used outside the function
        return data;

    } catch (error) {
        console.error("Error fetching the BSD file: ", error);
        return null;  // Return null in case of error
    }
}

// To call the function and store the result
async function getData() {
    const updted_bsd_file = await loadBsdFile();
     $.each(updted_bsd_file, function(index,row){	
	    /************          ***************************/
		if(row.is_tax_account=="1" && (row.bl_ipt_ott=="1" || row.bl_ipt_ott=="2" ))
		    {
				if(stcode==bo_state_code){	
				if(ugst_states.includes(stcode)==true  && (row.bl_nature=="33" || row.bl_nature=="195"))
					 {// if ut tax 									
							 var sum_tax_amt="";
							 var tax_rate = "";
							 var sum_igst = 0;
							 var sum_cess = ap_cess_total;							 
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
							 
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;						   
							 if( row.bl_nature=="195")
								var billsundry_amount = ap_sgst_total;
							   bsd_comp_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						  } 
						  else if(ugst_states.includes(stcode)==false && row.bl_nature!="195" && (row.bl_nature=="33" || row.bl_nature=="34")) //CGST	 OR SGST
						   {
							
							 var sum_tax_amt="";
							 var tax_rate="";
							 var sum_igst=0;
							 var sum_cess=ap_cess_total;
							 
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
							 
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;						   
							 if(row.bl_nature=="34" )
								var billsundry_amount = ap_sgst_total;
							
						     bsd_comp_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						  
						}
		            }else{
							
						if(row.bl_nature=="35") //IGST
						   {
						     var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = ap_igst_total;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;							
							 var billsundry_amount = ap_igst_total;					     
							
						     bsd_comp_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						   } 
					} 
						 
					    if(row.bl_nature=="36"){ // CESS
							 var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = 0;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;
							 var billsundry_amount = ap_cess_total;
							 if(ap_cess_total >0)	
								bsd_comp_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
							}
							$("#bsd_comp_applytax_json").val(JSON.stringify(bsd_comp_applytax_json));
		            }
		/*************         **************************/
		if(bo_gstin_type=="1" && row.is_tax_account=="1" && (row.bl_ipt_ott=="1" || row.bl_ipt_ott=="2" )){
			create_auto_accounts=create_auto_accounts+1;
			   
				if(stcode==bo_state_code){	
				if(ugst_states.includes(stcode)==true  && (row.bl_nature=="33" || row.bl_nature=="195"))
					 {// if ut tax 									
							 var sum_tax_amt="";
							 var tax_rate = "";
							 var sum_igst = 0;
							 var sum_cess = ap_cess_total;							 
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
							 
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;						   
							 if( row.bl_nature=="195")
								var billsundry_amount = ap_sgst_total;
							   bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						  } 
						  else if(ugst_states.includes(stcode)==false && row.bl_nature!="195" && (row.bl_nature=="33" || row.bl_nature=="34")) //CGST	 OR SGST
						   {
						
							 var sum_tax_amt="";
							 var tax_rate="";
							 var sum_igst=0;
							 var sum_cess=ap_cess_total;
							 
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
							 
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;						   
							 if(row.bl_nature=="34" )
								var billsundry_amount = ap_sgst_total;
							
						     bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						  
						}
		            }else{
							
						if(row.bl_nature=="35") //IGST
						   {
						     var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = ap_igst_total;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;							
							 var billsundry_amount = ap_igst_total;					     
							
						     bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						   } 
					} 
						 
					    if(row.bl_nature=="36"){ // CESS
							 var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = 0;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;
							 var billsundry_amount = ap_cess_total;
							 if(ap_cess_total >0)	
								bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
							}
		            }
		    });
	
	for(var i=1;i<=50;i++){		 
	   bsd_applytax_json.push({'billsundry_id':'','billsundry_amount':'','billsundry_name':'','bl_nature':'','tax_item_id':"","tax_hsn_sac":"","tax_amt":"","tax_rate":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	}
	//if(bo_gstin_type=="1"){
	  $("#billsundry_search").pqGrid('option', 'dataModel.data', bsd_applytax_json);
      $("#billsundry_search").pqGrid('refreshDataAndView');	
	//}
}

// Call the function to retrieve and store the data
getData();
	
	 
		
		
  }	
 
}

$("#applytax_calculate").on("click",function(){
	if(bo_gstin_type=="1"){
	var create_auto_accounts= 0;
	var stcode              = $('select[name="pos"] option:selected').val();
	var bsd_applytax_json   = [];			
	var items_footer_total  = $( "#taxgrid_search" ).pqGrid( "option", "summaryData" );
	var item_footer_total   = 0;
	var ap_cgst_total       = 0;
	var ap_igst_total       = 0;
	var ap_sgst_total       = 0;
	var ap_cess_total       = 0;
	
	// taxsummary all tax account No items daat and put them into bill sundry data 
	var taxsmdata = $('#taxgrid_search').pqGrid('option', 'dataModel.data');
	
	$.each(taxsmdata, function(index,rows){
		if(rows.is_tax_account=="0" && rows.bl_nature!=''){			
		bsd_applytax_json.push({'billsundry_id':rows.tax_item_id,'billsundry_amount':rows.tax_amt,'billsundry_name':rows.billsundry_name,'bl_nature':rows.bl_nature,'is_tax_account':0,'tax_item_id':rows.tax_item_id,"bl_hsn_sac":rows.tax_hsn_sac,"tax_amt":rows.tax_amt,"b_tx_cess_rte":rows.b_tx_cess_rte,"bl_tax_rate":rows.tax_rate,"igst":rows.tax_rate,"cess":0,"cgst":0,"sgst":0,'total_tax':0});	
		}
	});
	var i;
	for(i = 0; i < items_footer_total.length;i++) {
	    item_footer_total = parseFloat(items_footer_total[i]['total_tax']);
		ap_cgst_total += items_footer_total[i]['cgst'];
		ap_igst_total += items_footer_total[i]['igst'];
		ap_sgst_total += items_footer_total[i]['sgst'];	
		ap_cess_total += items_footer_total[i]['cess'];			
	   }	   
	      
    if(item_footer_total >0){	
        var supplytype = $('select[name="supply_type"] option:selected').val();
		if(supplytype=='3' || supplytype=='5')// no tax will applied on this type of supply types
		{
		alert_notification('The Supply Type Is Export – Without Payment Or Sez – Without Payment And Hence The Taxes Are Not Leviable!!');	
		return false;
		}			
	 
	 $.ajax({
            url: baseurl+"/admin/billsundry/load_bsd_file", 
            type: 'GET',
            processData: false,
            cache: false,
			async:true,						
            contentType: false,
            beforeSend: function() {
               
            },
            success: function (response) {
				
				if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
				
				updted_bsd_file=response; 
			}
	});
	 
		 $.each(updted_bsd_file, function(index,row){	
	   //bsd_all_items.forEach(function(row){		 
		if(row.is_tax_account=="1" && (row.bl_ipt_ott=="1" || row.bl_ipt_ott=="2" ))
		{
			
			if(stcode==bo_state_code && ugst_states.includes(stcode)==true && row.bl_nature!="195")
			create_auto_accounts=0;
		    else
			 create_auto_accounts=create_auto_accounts+1;	
			  		if(stcode==bo_state_code){
						 if(ugst_states.includes(stcode)==true  && (row.bl_nature=="33" || row.bl_nature=="195")) //CGST	 OR UGST
						   {
							 var sum_tax_amt="";
							 var tax_rate="";
							 var sum_igst=0;
							 var sum_cess=ap_cess_total;
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;
						   
							 if(row.bl_nature=="195")
								var billsundry_amount = ap_sgst_total;
							
						   
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
						     bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						 }
						 else if(ugst_states.includes(stcode)==false && row.bl_nature!="195" && (row.bl_nature=="33" || row.bl_nature=="34")) //CGST	 OR SGST
						   {
							 var sum_tax_amt="";
							 var tax_rate="";
							 var sum_igst=0;
							 var sum_cess=ap_cess_total;
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;
						   
							 if(row.bl_nature=="34")
								var billsundry_amount = ap_sgst_total;
							
						   
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
						     bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						 }
		            }else{
						if(row.bl_nature=="35") //IGST
						   {
						     var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = ap_igst_total;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;							
							 var billsundry_amount = ap_igst_total;					     
							
						     bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						   } 
						 } 
						 
					    if(row.bl_nature=="36"){ // CESS
							  var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = 0;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;
							 var billsundry_amount = ap_cess_total;
							if(ap_cess_total >0 ) 
							bsd_applytax_json.push({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
							}
		        }
				
			
				
		    });
	
	
	
	for(var i=1;i<=50;i++){		 
	   bsd_applytax_json.push({'billsundry_id':'','billsundry_amount':'','billsundry_name':'','bl_nature':'','tax_item_id':"","tax_hsn_sac":"","tax_amt":"","tax_rate":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	}
	$("#billsundry_search").pqGrid('option', 'dataModel.data', bsd_applytax_json);
    $("#billsundry_search").pqGrid('refreshDataAndView');	
	
	if(create_auto_accounts >0){
		alert_success("Tax Applied");
	}
	
	if(create_auto_accounts==0){
		// create here
		var okmessage = confirm("Are you sure to create tax account!!!");
		if(okmessage){
		var formData = {"stcode":stcode,"bo_state_code":bo_state_code};	
		$.ajax({
            url: baseurl+"/admin/billsundry/auto_create_taxaccount", 
            type: 'POST',
            data: formData,
            dataType: "json",
            beforeSend: function() {
                show_loader();                
                $('#validation_errors').html('');
            },
            success: function (response) {
                
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
					var bsd_st_bo_same = response.allbsd_lists;
					$.each(bsd_st_bo_same, function(index,row){
					if(row.is_tax_account=="1" && (row.bl_ipt_ott=="1" || row.bl_ipt_ott=="2" ))
					{
						if(stcode==bo_state_code){
						  if(ugst_states.includes(stcode)==true  && (row.bl_nature=="33" || row.bl_nature=="195")) //CGST	 OR UGST
						   {
							 var sum_tax_amt="";
							 var tax_rate="";
							 var sum_igst=0;
							 var sum_cess=ap_cess_total;
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;
						   
							 if(row.bl_nature=="195")
								var billsundry_amount = ap_sgst_total;
							
						   
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
						     bsd_applytax_json.unshift({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						  
						   }
						 else if(ugst_states.includes(stcode)==false && row.bl_nature!="195" && (row.bl_nature=="33" || row.bl_nature=="34")) //CGST	 OR SGST
						   {
							 var sum_tax_amt="";
							 var tax_rate="";
							 var sum_igst=0;
							 var sum_cess=ap_cess_total;
							 if(row.bl_nature=="33")
								var billsundry_amount = ap_cgst_total;
						   
							 if(row.bl_nature=="34")
								var billsundry_amount = ap_sgst_total;
							
						   
							 var sum_cgst = ap_cgst_total;
						     var sum_sgst = ap_sgst_total;
							 var sum_tax  = 0;
						     bsd_applytax_json.unshift({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						   }
						}
						else{
						if(row.bl_nature=="35") //IGST
						   {
						     var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = ap_igst_total;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;							
							 var billsundry_amount = ap_igst_total;					     
							
						     bsd_applytax_json.unshift({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
						   } 
						 } 
						 
					    if(row.bl_nature=="36"){ // CESS
							  var sum_tax_amt = "";
							 var tax_rate = "";
							 var sum_igst = 0;
							 var sum_cess = ap_cess_total;
							 var sum_cgst = 0;
							 var sum_sgst = 0;
							 var sum_tax  = 0;
							 var billsundry_amount = ap_cess_total;
							if(ap_cess_total >0 ) 
							bsd_applytax_json.unshift({'billsundry_id':row.id,'billsundry_amount':billsundry_amount,'billsundry_name':row.value,'bl_nature':row.bl_nature,'is_tax_account':1,'tax_item_id':row.id,"tax_hsn_sac":"","tax_amt":sum_tax_amt,"tax_rate":tax_rate,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
							}
		                }
						
					});
					
				for(var i=1;i<=50;i++){		 
					bsd_applytax_json.push({'billsundry_id':'','billsundry_amount':'','billsundry_name':'','bl_nature':'','tax_item_id':"","tax_hsn_sac":"","tax_amt":"","tax_rate":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
				}	
				$("#billsundry_search").pqGrid('option', 'dataModel.data', bsd_applytax_json);
				$("#billsundry_search").pqGrid('refreshDataAndView');				
				}
				apply_tax();	
			}	
			,
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
   		 return true;			
		}
		else
			return false;
		
	 }	
	
		
  }	
  else{
	alert_notification("Kindly add taxable item's to apply tax!!!");
    return false;   
  }
	}
});


/*************** end of applytax_calculate  &*********************/

	
	/************* Start Bill Sundry Item show in Tax Summary group with same catg + hsn code             ************/
	function show_taxsummary_bsdcng(item_taxsummary_json,tooltip_taxsummary_json){
		var total_tax = 0;
        var data      = $('#billsundry_search').pqGrid('option', 'dataModel.data');
		var stcode    = $('select[name="pos"] option:selected').val();
		var cgst_total=0;
		var igst_total=0;
		var sgst_total=0;	
        var cess_total=0;	
		var billsundry_total_amnt =0;	
		var taxgrid_data = $('#taxgrid_search').pqGrid('option', 'dataModel.data');
	    $.each(taxgrid_data, function(index,obj){				
             	 cgst_total +=obj.cgst;
				 sgst_total +=obj.sgst;
				 igst_total +=obj.igst;	
				 cess_total +=obj.cess;			
		  });
		$.each(data, function(index,obj){	
          billsundry_total_amnt=parseAmount(billsundry_total_amnt)+parseAmount(obj.billsundry_amount);			
          if(obj.is_tax_account == '0' && obj.billsundry_id != '' && obj.billsundry_amount!='' && obj.billsundry_id != undefined)
		  {
				var cgst_tt = parseAmount(obj.bl_tax_rate/2*(obj.billsundry_amount)/100);
				var sgst_tt = parseAmount(obj.bl_tax_rate/2*(obj.billsundry_amount)/100)
				var igst_tt = parseAmount(obj.bl_tax_rate*(obj.billsundry_amount)/100);
				
				
				var cess_tt = parseAmount(obj.b_tx_cess_rte*(obj.billsundry_amount)/100); 
				if(parseAmount(obj.billsundry_amount) > parseAmount(cess_tt))
					var cess_tt = parseAmount(obj.billsundry_amount);
				
					
				
				var sel_party = $('select[name="pos"] option:selected').val();
				if(sel_party==bo_state_code){
				 total_tax = parseFloat(sgst_tt)+parseFloat(cgst_tt)+parseFloat(cess_tt);
				}
				else{
				 total_tax = parseFloat(igst_tt)+parseFloat(cess_tt);	
				}					
				
				if(parseAmount(igst_tt) >0 && parseAmount(cess_tt) >0)
					tax_rate_string = parseAmount(obj.bl_tax_rate)+"% + "+parseAmount(obj.b_tx_cess_rte)+"%";
				else
				   tax_rate_string = parseAmount(obj.bl_tax_rate)+"%";	
			   
			   
                if(item_taxsummary_json[obj.tax_catg_id]){
                    if(item_taxsummary_json[obj.tax_catg_id][obj.bl_hsn_sac]){						
						item_taxsummary_json[obj.tax_catg_id][obj.bl_hsn_sac].push({'tax_cat_id':obj.tax_catg_id,'cess_basis':'1','billsundry_name':obj.billsundry_name,'bl_nature':obj.bl_nature,'is_tax_account':obj.is_tax_account,'tax_item_id':obj.billsundry_id,'tax_hsn_sac':obj.bl_hsn_sac,'tax_amt':(obj.billsundry_amount),'cess_rate':obj.b_tx_cess_rte,'tax_rate':obj.bl_tax_rate,"tax_rate_string":tax_rate_string,'igst':igst_tt,'cess':cess_tt,'item_qty':0,'item_price':0,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax});						
					  }
					  else{
					   item_taxsummary_json[obj.tax_catg_id][obj.bl_hsn_sac]=[{'tax_cat_id':obj.tax_catg_id,'cess_basis':'1','billsundry_name':obj.billsundry_name,'bl_nature':obj.bl_nature,'is_tax_account':obj.is_tax_account,'tax_item_id':obj.billsundry_id,'tax_hsn_sac':obj.bl_hsn_sac,'tax_amt':(obj.billsundry_amount),'cess_rate':obj.b_tx_cess_rte,'tax_rate':obj.bl_tax_rate,"tax_rate_string":tax_rate_string,'igst':igst_tt,'cess':cess_tt,'item_qty':0,'item_price':0,'cgst':cgst_tt,'sgst':sgst_tt,'total_tax':total_tax}];                          
						  
					  }
				}else{
				    item_taxsummary_json[obj.tax_catg_id]={
                                [obj.bl_hsn_sac] : [{'tax_cat_id':obj.tax_catg_id,'cess_basis':'1','billsundry_name':obj.billsundry_name,'bl_nature':obj.bl_nature,'is_tax_account':obj.is_tax_account,'tax_item_id':obj.billsundry_id,'tax_hsn_sac':obj.bl_hsn_sac,'tax_amt':(obj.billsundry_amount),'cess_rate':obj.b_tx_cess_rte,'tax_rate':obj.bl_tax_rate,"tax_rate_string":tax_rate_string,'igst':igst_tt,'cess':0,'item_qty':0,'item_price':0,'cgst':cess_tt,'sgst':sgst_tt,'total_tax':total_tax}]
                            };	
					
				}
			}
			else if(bo_gstin_type=="1"){
			  // if tax account already added in grid				 
			  if(stcode==bo_state_code){						
						   if(obj.bl_nature=='33')	//CGST		
						       var billsundry_amount = parseFloat(cgst_total);
							else if(obj.bl_nature=='34')	//SGST		
						       var billsundry_amount = parseFloat(sgst_total);
							else if(obj.bl_nature=='35')	//IGST		
						       var billsundry_amount = parseFloat(igst_total); 
							else if(obj.bl_nature=='36')	//CESS		
						       var billsundry_amount = parseFloat(cess_total);    
							 else
								var billsundry_amount = 0; 	
						}else{
							if(obj.bl_nature=='33')	//CGST		
						       var billsundry_amount = 0;
							else if(obj.bl_nature=='34')	//SGST		
						       var billsundry_amount = 0;
							else if(obj.bl_nature=='35')	//IGST		
						       var billsundry_amount = parseFloat(igst_total);
							else if(obj.bl_nature=='36')	//CESS		
						       var billsundry_amount = parseFloat(cess_total);   
							else
								var billsundry_amount = 0; 	
						 }
						 
			
				$("#billsundry_search").pqGrid( "updateRow",{ rowIndx: index, row: {'billsundry_amount': billsundry_amount}});		 
				
			  }
		});
		
		
		var bsd_taxsummary_json = [];
//console.table("bsd-->");
//console.table(item_taxsummary_json);		
		$.each(item_taxsummary_json, function(index,obj){
			
			//console.log("case 1=>"+index);
			var tax_hsn_sac='';
			var billsundry_name='';
			var tax_rate ='';
			var tax_rate_string='';
			var cess_rate ='';
			var cgst ='';
			var sgst ='';
			var igst ='';
			var cess ='';
			var tax_amt='';
			var tax_item_id='';
			var is_tax_account='';
			var bl_nature='';
			var total_tax=0;
			var cess_basis='';
			var tax_cat_id='';
			var taxable_tooltp_html="";
			var taxable_tooltp_array=[];
			taxable_tooltp_html +='<table width="500px;"><tr><td>ITEM LIST</td></tr>';
			
			$.each(obj, function(index1,obj1){//categ
			//console.log("case 2=>"+index1);
				var sum_tax_amt=0;
				var sum_igst=0;
				var sum_sgst=0;
				var sum_cgst=0;
				var sum_tax_rate=0;
				var sum_cess=0;
				var sum_tax=0;
				$.each(obj1, function(index2,obj2){ //hsn
				//console.log("case 3=>"+index2);
				tax_item_id    = obj2.tax_item_id;
				/***  Tax Summary ToolTip ****/			
				
				if(typeof tooltip_taxsummary_json[index] !="undefined"){
					var taxsummary_tooltip_data = tooltip_taxsummary_json[index][index1];
					$.each(taxsummary_tooltip_data, function(tpindex,tprow){
						taxable_tooltp_array[tprow.tax_item_id]=[{'tax_item_id':tprow.tax_item_id,'tax_item_name':tprow.tax_item_name,'item_qty':tprow.item_qty,'item_price':tprow.item_price}];
					 
					});
				}
				
				is_tax_account = obj2.is_tax_account;
				bl_nature  = obj2.bl_nature;
				tax_amt    = obj2.tax_amt;	
				if (typeof obj2.igst !== "undefined") 	
				   igst       = obj2.igst;
			    else
				  igst       = 0;
			  
				if (typeof obj2.sgst !== "undefined") 	
				sgst       = obj2.sgst;
			  else
				sgst       =0;
			
			if (typeof obj2.cgst !== "undefined")
				cgst       = obj2.cgst;
			else
				cgst       = 0;
			
			if (typeof obj2.tax_rate !== "undefined"){
				tax_rate   = obj2.tax_rate;
			    tax_rate_string = obj2.tax_rate_string;
			}
			 else{
				tax_rate   = 0;
				tax_rate_string='';
			 }
			
			if (typeof obj2.cess_rate !== "undefined")
				cess_rate   = obj2.cess_rate;
			 else
				cess_rate   = 0;
			
			if (typeof obj2.total_tax !== "undefined")
				total_tax  = obj2.total_tax;
			else
				total_tax  = 0;
			
			if (typeof obj2.cess !== "undefined")
				cess       = obj2.cess;
			else
				cess      = 0;
			
			if (typeof obj2.cess_basis !== "undefined")
				cess_basis = obj2.cess_basis;
			else
				cess_basis = 0;
			
			   tax_cat_id =obj2.tax_catg_id;
				
				
				sum_tax_amt   += parseFloat(tax_amt);
				sum_igst      += parseFloat(igst);
				sum_sgst      += parseFloat(sgst);
				sum_cgst      += parseFloat(cgst);
				sum_tax_rate  += parseFloat(tax_rate);
				tax_hsn_sac       = obj2.tax_hsn_sac;
				billsundry_name   = obj2.billsundry_name;
				sum_cess      += parseFloat(cess);
				sum_tax       += parseFloat(total_tax);
				
				})
				
				if(taxable_tooltp_array){
					$.each(taxable_tooltp_array, function(tpindex,tprow){
						$.each(tprow, function(tpindex1,tprow1){
							taxable_tooltp_html +='<tr><td>'+tprow1.tax_item_name+'</td></tr>';	
						});
					});
				}				
				taxable_tooltp_html +='</table>';
				bsd_taxsummary_json.push({'tax_cat_id':index,"cess_basis":cess_basis,'billsundry_name':billsundry_name,'bl_nature':bl_nature,'is_tax_account':is_tax_account,'tax_item_id':tax_item_id,"tax_hsn_sac":tax_hsn_sac,"tax_amt":sum_tax_amt,"cess_rate":cess_rate,"tax_rate":tax_rate,"tax_rate_string":tax_rate_string,"igst":sum_igst,"cess":sum_cess,"cgst":sum_cgst,"sgst":sum_sgst,'total_tax':sum_tax});	
				});		
		  
		});
		for(var i=1;i<=50;i++){		 
	     bsd_taxsummary_json.push({'tax_cat_id':'','bl_nature':'','is_tax_account':0,'tax_item_id':"","tax_hsn_sac":"","tax_amt":"","cess_rate":"","tax_rate":"","tax_rate_string":"","igst":"","cess":"","cgst":"","sgst":"",'total_tax':""});	 
	   }	
	 
	
	  $("#billsundry_search").pqGrid('refreshDataAndView');
	  $("#taxgrid_search").pqGrid('option', 'dataModel.data', bsd_taxsummary_json);
      $("#taxgrid_search").pqGrid('refreshDataAndView');
	}
	
	
	/*************  End Bill Sundry Item show in Tax Summary group with same catg + hsn code             ************/
	
	
	
    var item_qty_balance_json = {};

    function get_item_last_balance(rowData)
    {
        var status = true;
        if(item_qty_balance_json[rowData.item_id]){
            if(item_qty_balance_json[rowData.item_id][rowData.item_unit_id]){
                rowData.pq_cellattr ={
                    "item_name" : { "title":get_tooltip_html(item_qty_balance_json[rowData.item_id][rowData.item_unit_id]) },
                    "item_unit" : { "title":get_tooltip_html(item_qty_balance_json[rowData.item_id][rowData.item_unit_id]) },
                };
                status = false;
            }
        }
        
        if(status)
        {show_loader();
            rowData.pq_cellattr ={
                "item_name" : { "title":"Fetching quantity, please wait!" },
                "item_unit" : { "title":"Fetching quantity, please wait!" },
            };

            var mc_id = $("#matrcntr_id").val();
            var date = $("#voucher_date").val();

            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_item_last_qty_balance', 
                type: 'POST',
                data: {item_id: rowData.item_id, unit_id: rowData.item_unit_id, mc_id: mc_id, date: date, voucher_txn_id: <?= $voucher_txn_id ?? 0 ?>},
                dataType: "json",
				async:true,
                beforeSend: function() {
                    
                },
                success: function (response) {
					stop_loader();
                    if(response.status){
						
						if($("#last_price").val()=="2"){
						   rowData.item_price = response.balance.LastPrice;
						   rowData.LastPrice  = response.balance.LastPrice;
						}else
						  rowData.item_price = '';

                        if(item_qty_balance_json[rowData.item_id]){
                            item_qty_balance_json[rowData.item_id][rowData.item_unit_id] = response.balance;
                        }
                        else{
                            item_qty_balance_json[rowData.item_id] = {
                                [rowData.item_unit_id] : response.balance,
                            };
                        }
                     
                        rowData.pq_cellattr ={
                            "item_name" : { "title":get_tooltip_html(response.balance) },
                            "item_unit" : { "title":get_tooltip_html(response.balance) },
                        };
						
                        $("#grid_search").pqGrid('refreshDataAndView');
                        
                    }
                    
                },
                complete: function() {
                    
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
			

        }
    }

function get_taxsumrytooltip_html(obj){
        var html = "<table style='width:100%;'><tr><td>ITEM</td><td>AMOUNT</td></tr>"+obj+"</table>";
        return html;
    }
	function get_tooltip_html(obj){
        var html = `
            <table style='width:100%;'>
                <tr>
                    <td colspan='2'>${obj.unit_name}</td>
                </tr>
                <tr>
                    <td>Available</td>
                    <td align='right'>${obj.AvailQty}</td>
                </tr>
                <tr>
                    <td>Packed</td>
                    <td align='right'>${obj.PackQty}</td>
                </tr>
                <tr>
                    <td>Obselete</td>
                    <td align='right'>${obj.ObseQty}</td>
                </tr>
                <tr>
                    <td>In Transit</td>
                    <td align='right'>${obj.IntrsQty}</td>
                </tr>
            </table>`;
        return html;
    }
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
get_all_item_last_qty_balances();

    function get_all_item_last_qty_balances()
    {

        var date = $("#voucher_date").val();
        var mc_id = $("#matrcntr_id").val();
        
      
        var items_data = [];
        $.each(item_qty_balance_json, function(index, obj){
            $.each(obj, function(index2, obj2){
                items_data.push({
                    "item_id" : index,
                    "unit_id" : index2,
                    "mc_id" : mc_id,
                    "date" : date,
                });
            });
        });

        if(items_data.length > 0){
            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_all_item_last_qty_balances', 
                type: 'POST',
                data: {items_data: items_data, voucher_txn_id: <?= $voucher_txn_id ?? 0 ?>},
                dataType: "json",
                beforeSend: function() {
                    show_loader();
                },
                success: function (response) {
                   
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    if(response.status){
                        if(response.item_balances.length > 0){
                            $.each(response.item_balances, function(index, obj){
                                if(item_qty_balance_json.hasOwnProperty(obj.item_id)) {
                                    item_qty_balance_json[obj.item_id][obj.unit_id] = obj.balance;
                                }
                            });
                            update_grid_item_balances();
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
        }

    }

    $('#voucher_date').change(function(e){
        get_all_item_last_qty_balances();
    });
	
	$('#last_price').change(function(e){
       get_all_item_last_qty_balances();		
    });

    $('#matrcntr_id').change(function(e){
        get_all_item_last_qty_balances();
    });		
var methods = <?php echo json_encode($bills_method_list); ?>;
 var item_qty_balance = {};
  var item_qty_balance_b = {};
  $(document).on('change', '#voucher_date', function(e){

        var voucher_date = $("#voucher_date").val();
      

        $.each(item_qty_balance, function(index, value){
              $.each(value, function(indexn, valuen){
             item_qty_balance_b[index]=indexn
              });
        });
        
    
        $.ajax({
            url: '<?php echo $base_url; ?>ajax/get_all_item_balances', 
            type: 'POST',
            data: {voucher_date: voucher_date, item_id_array: item_qty_balance_b},
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
                    if(response.item_balances.length > 0){
                        $.each(response.item_balances, function(index, obj){
                            if(item_qty_balance.hasOwnProperty(obj.item_id)) {
                                item_qty_balance[obj.item_id][obj.item_unit_id] = obj.balance;
                            }
                        });
                        update_grid_item_balances();
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
  function update_grid_item_balances() {
        var data = $('#grid_search').pqGrid('option', 'dataModel.data');
        $.each(data, function(index,obj){			
            if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
            {
				if($("#last_price").val()=="1"){
					data[index]['item_price'] = obj.item_mrp;
					data[index]['LastPrice'] =0;
					
					data[index]['item_amount'] = parseAmount(obj.item_mrp*obj.item_qty);
				}
				else{
					data[index]['item_price'] = obj.item_price;
					data[index]['LastPrice'] =0;
					data[index]['item_amount'] = parseAmount(obj.item_price*obj.item_qty);
				}
				
                if(item_qty_balance_json[obj.item_id]){
                    if(item_qty_balance_json[obj.item_id][obj.item_unit_id]){
					 if($("#last_price").val()=="2"){
					  data[index]['item_price'] = item_qty_balance_json[obj.item_id][obj.item_unit_id]['LastPrice'];
					  data[index]['item_amount'] = parseAmount(item_qty_balance_json[obj.item_id][obj.item_unit_id]['LastPrice']*obj.item_qty);
					  data[index]['LastPrice'] = item_qty_balance_json[obj.item_id][obj.item_unit_id]['LastPrice'];
				    }else{
					data[index]['item_price'] = obj.item_price;
					data[index]['LastPrice'] = 0;
					data[index]['item_amount'] = parseAmount(obj.item_price*obj.item_qty);
				    }
				
				if($('#taxInclusive').is(":checked")){
								if(bo_gstin_type=="1"){ // means Regular
									var itmrate = obj.igst_rate;
								   } 
								   else if(bo_gstin_type=="2"){ // means Composition
									if((obj.tax_exempted=='n') && (obj.supply_type=="1" || obj.supply_type=="3"))
									 var itmrate = goods_rate;
									 else if(obj.tax_exempted=='n' && obj.supply_type=="2")
									 var itmrate = services_rate;
									 else
										 var itmrate =0; 
									  }
								 var x= parseAmount((itmrate/100));
								 var y = parseAmount(x)+parseAmount(1);
								 if($("#last_price").val()=="1"){ // mrp
								  var item_price_val = obj.item_mrp;
								 }
								 if($("#last_price").val()=="2"){ // last price
								 var item_price_val = item_qty_balance_json[obj.item_id][obj.item_unit_id]['LastPrice'];
								 }
								 
								 var totalprice = parseAmount(obj.item_qty)*parseAmount(item_price_val);
								 var taxableamount = parseAmount(totalprice/y);
								 data[index]['item_amount'] = taxableamount;
								 data[index]['item_amounttxs'] = parseAmount(obj.item_qty)*parseAmount(item_price_val);
						 
							 }
				
                        data[index]['pq_cellattr']  = {
                            "item_name" : { "title":get_tooltip_html(item_qty_balance_json[obj.item_id][obj.item_unit_id]) },
                            "item_unit" : { "title":get_tooltip_html(item_qty_balance_json[obj.item_id][obj.item_unit_id]) },
                        };
                    }
                }
               
            }
        });
        $('#grid_search').pqGrid('option', 'dataModel.data', data);
        $('#grid_search').pqGrid('refreshDataAndView');
		show_taxsummary_items();
    }
   
var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;


var itemlist  = [];
var unitslist = <?php echo json_encode($units_list);?>; 
var item_checked = [];

/*********************************************** BATCH ITEM WISE CODEING START BY BHUPINDER   ******************************************************/
var batch_items = [];
var batch_data = [];
var batch_Index=0;
  
function generateBatchGridData(){
	 var batchjson = [];
    for(var i=0;i<50;i++){
        batchjson.push({'batch_method': 'adjustment','batch_no': '','batch_id':'', 'manufacturing_date': '','batch_qty':'','batch_uom':'','batch_uom_id':'', 'expiry_date': '','startDate':'<?php echo $fy_begndt;?>','endDate':'<?php echo $fy_end;?>'});
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
				// console.log(expiry_date);
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


	
function batchno_editor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData
        // console.log(batch_items);
       
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

                        $inp.val([billIndex].batch_ref_list[index].value); 
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
        //console.log(batch_data);		
		
		
		
        $("#batchinfo_array").val(JSON.stringify(batch_data));
        show_loader();
        if($('#bbbCheck').is(":checked") && is_bbb){
            readyPurchaseBills(total);
        }
        else if($('#ccCheck').is(":checked")){
            readyPurchaseCc();
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
	//console.log(batch_items);
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
    var is_gstin_composition="0";
	var blankunits=0;
   $("#submitbtn").on("click",function(){
	    var isbillno_required=0;
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
	    blankunits=0;
		var tax_total = 0;
		var tax_item_checked = [];
      item_checked = [];
     var billsundry_item_checked = [];
     var final_item_id =[];
     var data = $("#grid_search").pqGrid('option', 'dataModel.data');
     var billsundry_data = $("#billsundry_search").pqGrid('option', 'dataModel.data');
      batch_items = [];
     
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
     
	 var taxitems_data    = $("#taxgrid_search").pqGrid('option', 'dataModel.data');
        var supplytype       = $('select[name="supply_type"] option:selected').val();
        var supply_type_desc = $("#view_supplydesc_modal #supply_type_desc").val();
   var bsdtax_total=0;
   for (var j = 0; j < billsundry_data.length; j++) {
        var billsundry_id     = billsundry_data[j]['billsundry_id'];
        var billsundry_name     = billsundry_data[j]['billsundry_name'];
        var billsundry_rate     = billsundry_data[j]['billsundry_rate'];
        var billsundry_amount     = billsundry_data[j]['billsundry_amount'];
		var billsundry_memoamnt   = billsundry_data[j]['billsundry_memoamnt'];
        var billsundry_amountfc     = billsundry_data[j]['billsundry_amountfc'];
        if(billsundry_id != '' && billsundry_name != ''){
            billsundry_item_checked.push({
                    "billsundry_id": billsundry_id,
                    "billsundry_name": billsundry_name,                    
                    "billsundry_amount" :parseAmount(billsundry_amount),
					"memo_amount"   : billsundry_memoamnt,	
					"billsundry_fcy_amount": parseAmount(billsundry_amountfc),
                 });
        }
        
     bsdtax_total += parseAmount(billsundry_amount);   
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
			if(typeof data[i]['fcrates'] != "undefined"){
			  var fcrates =data[i]['fcrates'];					 	
			 }else{
			   var fcrates =  $("#view_fcrates_modal #fcy_forex_rate").val();
			}
			}
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
					"tax_cat_id" :tax_cat_id
                });
				tax_total += parseAmount(total_tax_val);
		   }

        }
		
       var stock_qtyalert=0;
       var item_total_qty={};
       var total=0;
	   var item_total=0;
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
           var voucher_type_id = data[i]['voucher_type_id'];
           var voucher_txn_id = data[i]['voucher_txn_id'];
            var avail_item_qty = data[i]['AvailQty'];
			 var valmethod_id =   data[i]['valmethod_id'];
           
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
		 
             final_item_amouint=parseAmount(final_item_amouint)+parseAmount(item_amount);
            
			
            if(item_id != '' && item_name != ''){
                if(item_total_qty[item_id] == undefined){
                    item_total_qty[item_id] = {};
                    item_total_qty[item_id][item_unit_id] = item_qty;
                }
                else{
                    if(item_total_qty[item_id][item_unit_id] == undefined) 
                        item_total_qty[item_id][item_unit_id] = item_qty;
                    else
                        item_total_qty[item_id][item_unit_id] += item_qty; 
                }
            
                

				
				if(item_total_qty[item_id][item_unit_id] > avail_item_qty)
			       stock_qtyalert++; // should be less than or equal to available stock
                item_checked.push({
                   "item_id": item_id,
                   "item_name" :item_name,
                    "item_price": parseAmount(item_price),
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "item_unit_id" :item_unit_id,
                    "item_total_amount" :parseAmount(item_amount),
					"item_total_fcy_amount" :parseAmount(item_fcy_amount),
                    "description" :description,
                    "voucher_type_id":voucher_type_id,
                    "voucher_txn_id" : voucher_txn_id,
					"memo_amount" : memo_amt,
					"txinc_amount" : item_amounttxs,
					"valmethod_id":valmethod_id
                });
				total += parseAmount(item_amount);
                item_total += parseAmount(item_amount);
             
            }
         }
		 
	var stock_status_errors = 0;

        if(!$('#nsCheck').is(":checked"))
        {
            var final_item_qty_json = {};
            $.each(item_checked, function(index, object){
                if(final_item_qty_json[object.item_id]){
                    if(final_item_qty_json[object.item_id][object.item_unit_id])
                        final_item_qty_json[object.item_id][object.item_unit_id] += parseFloat(object.item_qty);
                    else
                        final_item_qty_json[object.item_id][object.item_unit_id] = parseFloat(object.item_qty);
                }
                else
                    final_item_qty_json[object.item_id] = {[object.item_unit_id] : parseFloat(object.item_qty)};
            });
            
            $.each(final_item_qty_json, function(index, object){
                $.each(object, function(index2, object2){
                    var item_id = index;
                    var unit_id = index2;
                    var quantity = object2;

                    if(item_qty_balance_json[item_id]){
                        if(item_qty_balance_json[item_id][unit_id]){
                            if(item_qty_balance_json[item_id][unit_id]['AvailQty'] >= quantity){
                                
                            }
                            else
                                stock_status_errors++;
                        }
                        else
                            stock_status_errors++;
                    }
                    else
                        stock_status_errors++;
                })
            });
        }	 
		 
      if($('#itmbtchCheck').is(":checked"))
             set_batch_items();
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
      var matchparty =    $('#party_ids option').filter(function(){
		   return ($("#clone_party_id").val() === this.value);               
          });
	 var bsdtax_total_match = parseAmount(Math.abs(bsdtax_total))+parseAmount(0.01);
	   var taxsm_total_match = parseAmount(tax_total);
	   
		if(parseAmount(bsdtax_total_match) >= parseAmount(taxsm_total_match))
          var restricted_total=0;
		else
	      var restricted_total=1;
	  
	   if(supplytype=='3' || supplytype=='5')// no tax will applied on this type of supply types
		{
		 var restricted_total=0;	
		}
		var reverse_charges = $('select[name="reverse_charges"] option:selected').val();
	   if(reverse_charges==1 || reverse_charges==2){
		var restricted_total=0; 
	   }	  
      var shwerrros='';
		   if( $("#voucher_series").val()==''){
			   shwerrros +='Series field is required<br>';
			   
		   }if( $("#voucher_date").val()==''){
			   shwerrros +='Date field is required<br>';
			   
		   }if( $("#clone_party_id").val()==''){
			   shwerrros +='Party field is required<br>';
		   }
		  
		   if($("#clone_party_id").val()!='' && matchparty.length==0){
			 shwerrros +='Party field is not valid<br>';   
		   }
		   
		   if( $("#pos").val()==''){
			   shwerrros +='POS field is required<br>';
		   }if( $("#matrcntr_id").val()==''){
			   shwerrros +='MC field is required<br>';
		   }  
		   
      
      if( shwerrros!='' || selerror=='1'){
          alert_notification(shwerrros);
          return false;   
       } 	   
       else if(supplytype=='12'){ // if supply type other selected description is required
           if(supply_type_desc==''){
               alert_notification("Supply Type Description field is required!!!");
               return false;
           }
       }
       else if(stock_status_errors >0 ){
          alert_notification("Qty should be less than or equal to stock available!!!");
          return false;  
       }	
	  else if( $("#currency_id").val() >1 && $("#view_fcrates_modal #fcy_forex_rate").val()==''){
          alert_notification("Kindly fill forex rates!!!");
          return false;   
       }	
       else if(item_checked.length==0 ||  final_item_amouint=='' || final_item_amouint=='0'){
          alert_notification("Kindly fill the item's data!!!");
          return false;   
       }
	   else if(blankunits>0){
		alert_notification("Item's unit cannot be blank!!!");
               return false;  
	  } 	  
	   //is_gstin_composition=="0" &&
	  else if(is_gstin_composition=="0" && restricted_total=='1'){
			alert_notification("Tax Summary & Bill Sundry Totals's mismatch!!!");
            return false;
		}
        else if(sdatevalidate==0){
            alert_notification("Voucher date is wrong!!!");
          return false;    
      } 
	  else if(reverse_charges=="2" && $("#eco_id").val()==''){
		  alert_notification("Ecomerce Operator field is required!!!");
          return false;  		
		   }
       else{
		   show_loader();
            $("#itmsdata").val(JSON.stringify(item_checked));
            $("#billsndrydata").val(JSON.stringify(billsundry_item_checked));
			$("#taxsummarydata").val(JSON.stringify(tax_item_checked));
			
           
            if($('#itmbtchCheck').is(":checked") && batch_items.length>0){
                readyBatches();
            }else{
			 stop_loader();	
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
       function billsundry_calculateSummary() {
        var itempriceTotal = 0,
            bitemamountTotal = 0,
			bitemamountmemoTotal=0,
			bitemamountfcTotal=0,
            itemqtyTotal=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){  
            if(row.billsundry_amount == '' || typeof row.billsundry_amount == 'undefined'){
                var billsundry_amount =0;
            }else
             var billsundry_amount =  row.billsundry_amount;
		 
		 
		 if(row.billsundry_amountfc == '' || typeof row.billsundry_amountfc == 'undefined'){
                var billsundry_amountfc =0;
            }else
             var billsundry_amountfc =  row.billsundry_amountfc;
		 
		 
		 
		 if(row.billsundry_memoamnt == '' || typeof row.billsundry_memoamnt == 'undefined'){
                var billsundry_memoamnt =0;
            }else
             var billsundry_memoamnt =  row.billsundry_memoamnt;
		 
           //if(row.is_tax_account=="1")
			 bitemamountTotal += parseAmount(billsundry_amount);
		      bitemamountmemoTotal += parseAmount(billsundry_memoamnt);
			  bitemamountfcTotal += parseAmount(billsundry_amountfc);
         })
        var totalData = {
                billsundry_name: "Total",                
                billsundry_amount: bitemamountTotal,
				billsundry_amountfc: bitemamountfcTotal,
				billsundry_memoamnt: bitemamountmemoTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
           
			
            this.option('summaryData', [totalData]);
			item_bsd_totals();
          }     
       function calculateSummary() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
			itemamountIncTotal=0,
			itemamountFCTotal=0,
			itemMemoTotal=0,
            itemqtyTotal=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
            if(row.item_price == '' || typeof row.item_price == 'undefined'){
                var item_price =0;
            }else
             var item_price =  row.item_price;
            
            if(row.item_amountfc == '' || typeof row.item_amountfc == 'undefined'){
                var item_amountfc =0;
            }else
            var  item_amountfc =  row.item_amountfc;
		
		   if(row.item_qty == '' || typeof row.item_qty == 'undefined'){
                var item_qty =0;
            }else
            var  item_qty =  row.item_qty;
		
		
            
            if(row.item_amount == '' || typeof row.item_amount == 'undefined'){
                var item_amount =0;
            }else
             var item_amount =  row.item_amount;
		 
		
		 if(row.item_amounttxs == '' || typeof row.item_amounttxs == 'undefined'){
                var item_amounttxs =0;
            }else
             var item_amounttxs =  row.item_amounttxs;
		 
		if(row.memo_amt == '' || typeof row.memo_amt == 'undefined'){
                var item_memo_amt =0;
            }else
             var item_memo_amt =  row.memo_amt;
		
             
            itemqtyTotal    += parseFloat(item_qty);
            itempriceTotal  += parseAmount(item_price);
            itemamountTotal += parseAmount(item_amount);
			itemamountIncTotal += parseAmount(item_amounttxs);			
			itemamountFCTotal += parseAmount(item_amountfc);
			itemMemoTotal+= parseAmount(item_memo_amt);
            
        })
		var totalData = {
                item_name: "Total",
                item_qty  : itemqtyTotal,
                item_price: itempriceTotal,
                item_amount: itemamountTotal,				
				item_amounttxs: itemamountIncTotal,
				item_amountfc :itemamountFCTotal,
				memo_amt :itemMemoTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }			
            this.option('summaryData', [totalData]);			
			item_bsd_totals();			
			return totalData;
          }
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
         
        var autoCompleteEditor = function (ui) {
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var element= {};
            var grid = this;
            var voucher_date = $("#voucher_date").val();
            var mc_id = $("#matrcntr_id").val();
            var party_id = $('#party_id').val();
			
			
            if(party_id == ''){
                grid.saveEditCell();
                alert_notification('Enter Party first!');

                return false
            }
            if(voucher_date == ''){
                grid.saveEditCell();
                alert_notification('Enter Voucher Date first!');

                return false
            }
            if(mc_id == ''){
                grid.saveEditCell();
                alert_notification('Enter Material Center first!');

                return false
            }

            $inp.autocomplete({
                    source:  <?php echo $item_json_file;?>,
                    selectItem: { on: true }, //custom option
                    highlightText: { on: true }, //custom option
                    minLength: 3,
                    select: function(event, ui) {
						
                        event.preventDefault();
                        rd.item_id        = ui.item.item_id;
                        rd.item_name      = ui.item.label;
                        rd.item_unit      = ui.item.item_unit;
                        rd.item_unit_id   = ui.item.item_unit_id;						
					    rd.igst_rate      = ui.item.igst_rate;
						rd.cess_rate      = ui.item.cess_rate;
						rd.item_hsn_sac   = ui.item.item_hsn_sac;	
						rd.tax_cat_id     = ui.item.tax_cat_id;
						rd.cess_basis     = ui.item.cess_basis;
						rd.item_mrp       = ui.item.item_mrp;
						rd.supply_type    = ui.item.supply_type;
                        rd.tax_exempted   = ui.item.tax_exempted;						
						rd.item_amounttxs = 0;
						rd.item_sales_acc = ui.item.item_sales_acc;
						rd.item_pur_acc   = ui.item.item_pur_acc;
						
						if($("#last_price").val()=="1"){
						  rd.item_price = ui.item.item_mrp;
						}
						else if($("#last_price").val()=="2"){
							if(item_qty_balance_json[rd.item_id]){
							rd.item_price = item_qty_balance_json[rd.item_id][rd.item_unit_id]['LastPrice'];
					    }
						  
						}else
						  rd.item_price = "";
					  
					   
                        $(this).val(ui.item.label); 
                        get_item_last_balance(rd);
						
						
						
							
						
						show_taxsummary_items();						
						refreshbsd();						
                     }  
                })./* focus(function () {               
                    $(this).autocomplete("search", "");
                    rd.item_id = '';
                    rd.item_name = '';
                    rd.item_unit = '';
                    rd.item_unit_id = '';					
                    rd.pq_cellattr = {};
                }). */focusout(function () {   
				var enteredValue = $(this).val().toLowerCase();
				   var  source_data=<?php echo $item_json_file; ?>;				  
				   const isValid = source_data.some(item => item.label.toLowerCase() === enteredValue);
				   if(rd.item_id == '' || isValid==false)
                    {   $(this).val('');
						  $(this).autocomplete("search", "");
                        rd.item_id = '';
                        rd.item_name = '';
                        rd.item_unit = '';
                        rd.item_unit_id = '';						
                        rd.pq_cellattr = {};
                    }
			if(rd.item_sales_acc=="0" || rd.item_pur_acc=="0"){						
							$(this).val('');
							$(this).autocomplete("search", "");
							rd.item_id = '';
							rd.item_name = '';
							rd.item_unit = '';
							rd.item_unit_id = '';						
							rd.pq_cellattr = {};
							alert_notification('Item Sales Account/Purchase Account is missing!');
						}	   
			 if(rd.cess_basis=="0" && $('#taxInclusive').is(":checked")){
				grid.saveEditCell();
                alert_notification('MRP based item is not allowed in Tax Inclusive!');

						rd.item_id = '';
                        rd.item_name = '';
                        rd.item_unit = '';
                        rd.item_unit_id = '';						
                        rd.pq_cellattr = {};
				
						}
				
                    
                });      
        }
        var autoCompleteEditor2 = function (ui) {
            var rd = ui.rowData;
            var $inp = ui.$cell.find("input");

            var voucher_date = $("#voucher_date").val();
            var mc_id = $("#matrcntr_id").val();
            if(voucher_date == ''){
                grid.saveEditCell();
                alert_notification('Enter Voucher Date first!');

                return false
            }
            if(mc_id == ''){
                grid.saveEditCell();
                alert_notification('Enter Material Center first!');

                return false
            }

            $inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    rd.item_unit = ui.item.label;
                    rd.item_unit_id =ui.item.id;

                    get_item_last_balance(rd); 
					show_taxsummary_items();					
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.item_unit = '';
                rd.item_unit_id = '';
                rd.pq_cellattr = {};
            }).focusout(function () {       
                if(rd.item_unit_id == '')
                {
                    var index = unitslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.item_unit.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){
                        rd.item_unit = unitslist[index].label;
                        rd.item_unit_id = unitslist[index].id;
                        
                        get_item_last_balance(rd);
                    }
                    else{
                        rd.item_unit = '';
                        rd.item_unit_id = '';
                        rd.pq_cellattr = {};
                    }
                }
            });
        }

        var colModel = [
                       {  title: "ITEM NAME", dataIndx: "item_name",sortable:false,width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                          type: "textbox",
                          init: autoCompleteEditor,
                          options: []
                       }
              },
              { title: "QUANTITY",dataIndx: "item_qty",sortable:false, width: 100, dataType: "float",
                 
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
              { title: "SHORT NARRATION", width: 100,sortable:false, dataType: "string", dataIndx: "description",
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
                        return true;
                    }
                    return false;
                    },
                },
              { title: "UNIT", dataIndx: "item_unit", sortable:false,width: 100,cls: 'pq-drop-icon pq-side-icon',
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
                        return true;
                    }
                    return false;
                },
                    editor: {                   
                          type: "textbox",
                          init:autoCompleteEditor2,
                          options: [],
                      },
                       
                     render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },
            },
            { title: "PRICE", width: 20, align: "right",sortable:false,dataIndx: "item_price",dataType: "float",
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
			{ title: "AMOUNT(₹)", width: 20, sortable:false,align: "right",dataIndx: "item_amounttxs",hidden:true,
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
				/*if($("#last_price").val()=="1"){
					rd.item_amounttxs = parseAmount(rd.item_mrp*rd.item_qty);					
				 }
				 else{
					rd.item_amounttxs = parseAmount(rd.item_price*rd.item_qty);
			 	  } */
				  /*
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
								 
						 
							 }
					  */
					
					if(rd.item_amounttxs != ''){
                        return formatAmount(parseAmount(rd.item_amounttxs));   
                    }
                    return '';
                }
              },
			{ title: "AMOUNT(₹)", width: 20, sortable:false,align: "right",dataIndx: "item_amountfc",hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.item_price != ''){
						var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();
						
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						var currency_id = $('select[name="currency_id"] option:selected').val();
						
						rd.item_amountfc = rd.item_qty*rd.item_price;
						/*if(parseInt(currency_id) >1){	
						  if(fcy_forex_rate!='')
							rd.item_amount   = parseAmount((rd.item_qty*rd.item_price)/fcy_forex_rate);
						   else
							 rd.item_amount   = parseAmount(0);  
						
						}else{
						  	rd.item_amount   = parseAmount(rd.item_qty*rd.item_price);	
						} */
						return formatAmount(parseAmount(rd.item_qty*rd.item_price),currency_symbol);   
						
                    }
                    return '';
                }
              },  
            { title: "AMOUNT(₹)", width: 20, sortable:false,align: "right",dataIndx: "item_amount",dataType: "float",
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
						/* 
						if($('#taxInclusive').is(":checked")){													
						 return formatAmount(rd.item_amount); 	
						}
						else
						return formatAmount(rd.item_price*rd.item_qty);
						*/
                    }
                    return '';
                }
              },
			
			{ title: "MEMO(₹)", width: 20, sortable:false,align: "right",dataIndx: "memo_amt",hidden:true,dataType: "float",
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
            height: 450,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            wrap:false,
            numberCell: { show: true },
			refreshHeader: function(){ 
				 if($("#grid_search").pqGrid('instance')){   	
				var $cell = $("#grid_search").pqGrid( 'getCellHeader' , {colIndx: 0}); 
				$cell.attr("title", "Type minimum 3 characters to search item").tooltip();
				 }
        	},
            dataModel: dataModel,
            change: calculateSummary,
            dataReady: calculateSummary,   
            columnTemplate: { render: commentRender },
            colModel: colModel,  
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
				refreshbsd();				
				$("#taxgrid_search").pqGrid('refreshDataAndView');
                
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
     function TaxcalculateSummary() {
            var total_taxTotal = 0, 
                total_igst =0,
				total_cess = 0,
				total_cgst =0,
				total_sgst =0,
				total_tax_amt =0,	
                data = this.option('dataModel.data'),
                len  = data.length;

            data.forEach(function(row){
				
                if(row.total_tax == '' || typeof row.total_tax == 'undefined'){
                  var total_tax=0;
                 }else
                  var total_tax = row.total_tax; 
			  
				total_cgst       += parseAmount(row.cgst);		
                total_taxTotal  += parseAmount(total_tax);
				total_sgst  += parseAmount(row.sgst);  
                total_igst += parseAmount(row.igst);
				total_cess += parseAmount(row.cess);
				total_tax_amt +=parseAmount(row.tax_amt);
              })
			
            var totalData = {
                    tax_hsn_sac: "Total",
					tax_rate:"",
					sgst:     total_sgst,
					cgst:     total_cgst,
					igst:     total_igst,
					cess: total_cess,
					tax_amt: total_tax_amt,
                    total_tax: total_taxTotal,
                    pq_rowcls: 'grid_footer_color',
                    summaryRow: true
                }
			
              this.option('summaryData', [totalData]);
           }



        
        var tax_newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 400,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            wrap:false,
            numberCell: { show: true },
			change: TaxcalculateSummary, 
            dataReady: TaxcalculateSummary,
			columnTemplate: { render: commentRender },
            dataModel: tax_dataModel,
            colModel: tax_colModel,
            editable: false,
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
           
           tax_newObj.rowDblClick = function(event, ui) {
                var rowData     = ui.rowData;
                var tax_hsn_sac = rowData.tax_hsn_sac;               
				show_taxsummary_items_hsnwise(tax_hsn_sac,'itm');
            }
            tax_newObj.cellKeyDown = function(evt, ui) {
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
       
        $("#taxgrid_search").pqGrid(tax_newObj);
		
		/**************   Party Change Code             *****************/	
	 $("#fcratespopup").on("click",function(){
			var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
			var currency_id    = $('select[name="currency_id"] option:selected').val();
			calculate_fc_rates(currency_id,billsundry_colModel,colModel,currency_symbol);  
		  });
		  
	$("#pos").on("change", function() {
		var pos_id = $(this).val();		
	    unit_territory(pos_id,tax_colModel);
     });
          
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
        
         var billsundry_dataModel = {"data":get_sundry_grid()}
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
				
            },
			{ title: "AMOUNT(₹)", width: 100, align: "right", dataIndx: "billsundry_amountfc" ,hidden:true,dataType: "float",
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
         if(type == 'item'){
             window.location.href = "<?php echo $base_url; ?>sales_order/item";
         }
         else if(type == 'non_item'){
            $(this).val('item');
             window.location.href = "<?php echo $base_url; ?>sales_order/non_item";
         }
        
     })
     
 </script>  
</body>
</html>
