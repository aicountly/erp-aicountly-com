<?php $header = array( 	'title' => $page_label.' Sales Order (Non-Item)' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));

if($billfrm_data){
   $from_gstin      = '<p>'.$billfrm_data['comp_gstin'].'</p>';
   $from_tradename  ='<p>'.$billfrm_data['gstin_trade_name'].'</p>';
   $from_statecode  ='<p>'.$billfrm_data['gstin_state_code'].'</p>';
   $from_country    ='<p>'.$billfrm_data['country'].'</p>';
   }
else{
	 $from_gstin     ='<p>N/A</p>';
	 $from_tradename ='<p>N/A</p>';
	 $from_statecode ='<p>N/A</p>';
	 $from_country   ='<p>N/A</p>';
   }
 $shipto_addr1       ='';
 $shipto_addr2       ='';
 $shipto_place       ='';
 $shipto_pin         ='';
 $shipto_state_code  ='';  
 $dispfrm_addr1      ='';
 $dispfrm_addr2      ='';
 $dispfrm_place      ='';
 $dispfrm_pin        ='';
 $dispfrm_state_code =''; 
 $billsundry_comp_json_data=[];
if($sundry_comp_transactions){
	foreach($sundry_comp_transactions as $bsd_comp_row){
		$billsundry_comp_json_data[]= array("billsundry_id"=>$bsd_comp_row['billsundry_id'],"billsundry_name"=>$bsd_comp_row['billsundry_name'],'bl_nature'=>$bsd_comp_row['bl_nature'],'is_tax_account'=>$bsd_comp_row['is_tax_account'],'billsundry_amount'=>$bsd_comp_row['billsundry_amount'],'billsundry_memoamnt'=>$bsd_comp_row['billsundry_memoamnt']);
	}
}
 if($get_ewbmstreqn_data){
	 $gstshipton_info        = $get_ewbmstreqn_data['gstshipton_info']; 
	  if($gstshipton_info){
		  $shipto_addr1      = $gstshipton_info['shipto_addr1'];
		  $shipto_addr2      = $gstshipton_info['shipto_addr2'];
		  $shipto_place      = $gstshipton_info['shipto_place'];
		  $shipto_pin        = $gstshipton_info['shipto_pin'];
		  $shipto_state_code = $gstshipton_info['shipto_state_code'];
	  }
	 $gstdispfrm_info         = $get_ewbmstreqn_data['gstdispfrm_info']; 
	  if($gstdispfrm_info){
		  $dispfrm_addr1      = $gstdispfrm_info['dispfrm_addr1'];
		  $dispfrm_addr2      = $gstdispfrm_info['dispfrm_addr2'];
		  $dispfrm_place      = $gstdispfrm_info['dispfrm_place'];
		  $dispfrm_pin        = $gstdispfrm_info['dispfrm_pin'];
		  $dispfrm_state_code = $gstdispfrm_info['dispfrm_state_code'];
	  }
    }  
      
if(!empty($gstroutsup_info)){
	$outsup_pos = $gstroutsup_info['outsup_pos'];
	$outsup_bill_ref_no = $gstroutsup_info['outsup_bill_ref_no'];
	$outsup_rev_chg     = $gstroutsup_info['outsup_rev_chg'];
	$outsup_inv_type    = $gstroutsup_info['outsup_inv_type'];
}
else{
    $outsup_pos =$outsup_bill_ref_no=$outsup_rev_chg=$outsup_inv_type='';
}
if($duplc=="1"){
$outsup_bill_ref_no = $voucher_bill_no;	
}
 $partyarray=array();
 foreach ($party_dropdown as $value){
   $partyarray[$value['acc_id']]=array("gstin"=>$value['gstin'],'name'=>$value['acc_name'],'is_bbb'=>$value['is_bbb']);
 }
 
if($istaxinc=="1") 
 $checkedlabel_tax =' checked="true"';
else
$checkedlabel_tax =" ";	 
			
if($ismemosale=="1") 
$checkedlabel =' checked="true"';
else
$checkedlabel =" ";		
?>
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
<div class="row pb-2">
    <div class="col-sm-6"><h3><?php echo $page_label;?> Sales Order (Non-Item)</h3></div>  
    <div class="col-sm-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a>
        <a href="#"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="javascript:void(0);" onclick="print_page()"><span class="material-symbols-outlined">print</span></a>
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
   
    </div>
    </div> 
</div>
<div class="row pb-2">
    <div class="col-md-7 order-2 order-md-3">
     <select name="type" id="type" class="form-select d-inline-block" style="width:170px;">
        <option value="item" selected="selected">Item</option>       
      </select> 
	   <select form="salefrm" name="currency_id" id="currency_id" class="form-select d-inline-block" style="width:170px;">
      <?php foreach ($currency_list as $value) { ?>
      <option <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?> data-id="<?= $value['curr_symbol'] ?>" value="<?= $value['comp_currency_id'] ?>"> <?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
      <?php } ?>
    </select>
	<div id="fcratespopup" title="Add Forex Rates" alt="Add Forex Rates" class="btn btn-sm btn-primary">#</div>

      <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="itmbtchCheck">
    <label class="form-check-label" for="itmbtchCheck">Item Batch</label>
   </div>
     <div class="form-check form-check-inline me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="taxInclusive" id="taxInclusive" <?php echo $checkedlabel_tax;?>>
      <label class="form-check-label" for="taxInclusive">Tax Inclusive</label>
    </div>
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="nsCheck">
      <label class="form-check-label" for="nsCheck">Negative Stock</label>
    </div>
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" form="salefrm" value="1" name="memoCheck" id="memoCheck" <?php echo $checkedlabel;?>>
      <label class="form-check-label" for="memoCheck">Memorandum</label>
    </div>

   
  </div>  
  <div class="col-md-5 text-md-end order-4 collapse listmenu" id="listmenu">
     <a href="#" class="btn btn-success btn-sm dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
      <li>
        <a class="dropdown-item" id="applytax_calculate" href="javascript:void(0);">Apply Tax</a>
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
               <option id="<?= $vs_row['comp_vch_series_id']?>" data-srsmethod="<?= $vs_row['comp_vch_method'] ?>"  value="<?= $vs_row['comp_vch_series_id'] ?>" <?php echo ($vs_row['comp_vch_series_id']==$voucher_series)?'selected':''?>><?= $vs_row['comp_vch_series'] ?></option>
            <?php } ?>
		  </select>  </div>
        </div>
		 <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
            <span class="input-group-text">Supply Type:</span>
           
            <?php echo form_dropdown('supply_type', $supply_type_list, $get_ewbmstreqn_data['ewb_supply_type'],' id="supply_type" class="sale_type form-select required" required '); ?>
           
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
                <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_no;?>" disabled>
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
          echo form_dropdown('reverse_charges', $revrchrgs_lists, $outsup_rev_chg,' id="reverse_charges" class="sale_type form-select" '); ?>
         
        </div>
      </div>
         <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Transporter:</label> 
                 <span id="transporter_div">
          <?php 
          echo form_dropdown('transporter', $transporter_dropdown, $transport_edit_id,' id="transporter" class="sale_type form-select" '); ?>
          </span>
		  <div data-code="0" id="transporterpopup" title="View Transporter" alt="View Transporter" class="btn btn-sm btn-primary">#</div>
            </div>
        </div>
		 <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Party:</label>
				<?php 
				if(isset($partyarray[$party_id]['name']))
					 $party_name = $partyarray[$party_id]['name'];
				 else
					$party_name = ''; 
				?>
                <input list="party_ids" id="clone_party_id" name="clone_party_id" value="<?php echo $party_name;?>" class="form-control form-control-sm required" required>
				  <datalist id="party_ids">
					<?php
					foreach($party_dropdown as $value){ ?>
					   <option data-gstin="<?php echo $value['gstin']?>" id="<?= $value['acc_id']?>" data-statecode="<?= $value['state_code'] ?>" data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_name'] ?>" >
					<?php } ?>
				  </datalist> 
					  
            </div>
			<input type="hidden" name="ewb_id" value="<?php echo $get_ewbmstreqn_data['ewb_id'] ?? '0';?>">
		
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">POS:</label> 
              <?php  echo form_dropdown('pos', $states_lists,$outsup_pos ,' id="pos" class="sale_type form-select" '); ?>
            </div>
        </div>
	   <div class="col-md-4 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Invoice Type :</label><label class="input-group-text" id="invoice_type_label"><?php echo $outsup_inv_type;?></label>
		
			
		 </div> 
      </div>
	  
	    <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
          <label class="input-group-text">Ecomerce Operator:</label>
          <?php echo form_dropdown('eco_id', $eco_dropdown, "",' id="eco_id" class="form-select" '); ?>
        </div>
      </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Order No:</label> 
                <input type="text" name="order_no" value="<?= $order_no ?>" class="form-control form-control-sm">
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
              <input type="text" name="due_date" id="due_date" value="<?= $due_date ?>" class="datepicker form-control form-control-sm" required>
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
               <textarea  name="narration" class="form-control form-control-sm"><?php if($get_narration_info){
				   echo $get_narration_info['vch_narr'];
				   
			   } ?></textarea>
            </div>
        </div>
		 <input type="hidden" name="hsndata" id="hsndata">
		<input type="hidden" name="itmsdata" id="itmsdata">
        <input type="hidden" name="type" value="non_item">
       <input type="hidden" name="gst_paidacc_id" id="gst_paidacc_id">
		<input type="hidden" name="bsd_comp_applytax_json" id="bsd_comp_applytax_json" value="">
        <input type="hidden" name="prdata" id="prdata">
        <input type="hidden" name="billsndrydata" id="billsndrydata">
		<input type="hidden" name="taxsummarydata" id="taxsummarydata">
        <input type="hidden" name="batchinfo_array" id="batchinfo_array" value="">
        <input type="hidden" name="trackinginfo_array" id="trackinginfo_array" value="">
 <input type="hidden" name="invoice_type" id="invoice_type" value="<?php echo $outsup_inv_type;?>">

      </div>
    </div>

   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br><select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option  data-is_bbb="<?php echo $partyarray[$party_id]['is_bbb'];?>" value="<?php echo $party_id;?>"><?php echo $partyarray[$party_id]['name'];?></option>			
					  </select> 
    <div class="col-12 "><div class="row">
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
        <a href="javascript:main(0)"  class="deletebtn btn btn-danger btn-lg">Delete</a>
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
					<textarea name="supply_type_desc" id="supply_type_desc"  cols="10" rows="10" class="form-control"><?php echo $get_ewbmstreqn_data['ewb_sub_supply_desc'];?></textarea>
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


<input type="hidden" name="isdplc_vch" value="<?php echo $duplc;?>">
 <input type="hidden" name="vch_subtype_id" value="<?php echo $vch_subtype_id;?>">
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
					 <td id="transporter_id"></td>
					<td id="gstin_id"></td>
					<td id="transporter_name"></td>
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
                         <button type="button" class="btn btn-primary" data-id="" id="update_transport_detail">Update</button>
                      </div>
                    </div>
                  </div>
    </div>	
	
	<div class="modal fade pt-5" id="adup_transporter_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Transporter Details-</h4>
                        <button type="button" class="btn-close close_btn" data-bs-dismiss="modal" aria-label="Close"></button>
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
					 <td id="transporter_id"></td>
					<td id="gstin_id"></td>
					<td id="transporter_name"></td>
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
                         <button type="button" class="btn btn-primary" data-id="" id="savenew_transport_detail">Save</button>
                      </div>
                    </div>
                  </div>
    </div>				
</form>
<div class="modal fade pt-5" id="add_transporter_modal" tabindex="-1" aria-labelledby="proinfoLabel" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 id="proinfoLabel">Transporter Details</h4>
                        <button type="button" class="btn-close hist_closemodal_window"  aria-label="Close"></button>
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
				<?php
				if(isset($partyarray[$party_id]['is_bbb']))
					$is_bbb = $partyarray[$party_id]['is_bbb'];
				else
					$is_bbb =0;
				
				if(isset($partyarray[$party_id]['gstin']))
					$is_gstin = $partyarray[$party_id]['gstin'];
				else
					$is_gstin ="";
				?>
				<input type="text" name="vehicle_no" id="vehicle_no" value="" maxlength="255" class="form-control">
						 <select style="display:none;" name="party_id" id="party_id" class="form-select">
						<option  data-gstin="<?php echo $is_gstin?>" data-is_bbb="<?php echo $is_bbb;?>" value="<?php echo $party_id;?>"><?php echo $party_name;?></option>			
					  </select>   
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
<?php echo view('includes/footer_scripts'); 
    $json_data = $account_transactions;
	$total_items_txns = count($json_data);
for($i=1;$i<=(500-$total_items_txns);$i++)
    $json_data[] =array("acc_id" => "","account_name"=>'','description'=>'','amount'=>'',"pq_cellattr"=>array("account_name"=>array("title"=>"")));

 $tax_json_data = $tax_summary;
 $total_tax_txns = count($tax_json_data);
for($i=1;$i<=(500-$total_tax_txns);$i++)
    $tax_json_data[] =array("tax_item_id"=>"","id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'','total_tax'=>'',"pq_cellattr"=>array("tax_amt"=>array("title"=>"")));

$billsundry_json_data = $sundry_transactions;
for($i=1;$i<=50;$i++)
    $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_name'=>'','bl_nature'=>'','is_tax_account'=>'0','igst'=>'','sgst'=>'','cess'=>'','cgst'=>'','tax_rate'=>'','tax_amt'=>'','tax_hsn_sac'=>'','tax_item_id'=>'','billsundry_rate'=>'','billsundry_amount'=>'','total_tax'=>'');
?>

<style>
    .boldcell{font-weight:700;}
</style>

<script>
$(window).on('load', function() {
	$("#pos").trigger('change');
$('#reverse_charges option[value="'+$("#reverse_charges option:first").val()+'"]').trigger('change'); 
});

function print_page(){
        kkey = {};
        window.open('<?php echo base_url();?>/admin/GeneratePDF/saleordr_print/<?php echo $voucher_txn_id;?>?p=1', '_blank').focus();
    }
var kkey = {};
   $(document).keydown(function(e) {
       kkey[e.which] = true;
       if (kkey[17] && kkey[80]) { 
            print_page();
    }        
   });
 $(document).keyup(function(e) {
    delete kkey[e.which];
  });	
<?php if($ismemosale >0){ ?>
var is_memo_enabled="1";
<?php } else{ ?>
var is_memo_enabled="0";
<?php } ?>

<?php if($istaxinc >0){ ?>
var is_taxinc_enabled="1";
<?php } else{ ?>
var is_taxinc_enabled="0";
<?php } ?>

var forexcrncy_rate_sel = '<?php echo $forexcrncy_rate;?>';
var voucher_txn_id  ='<?php echo $voucher_txn_id;?>';

var ugst_states        = ['35','04','26','25','31','38','34','97'];
var billsundry_comp_json_data = <?php echo json_encode($billsundry_comp_json_data);?>;

var accounts_json_file = <?php echo $accounts_json_file; ?>;
var sdateFrom ='<?php echo $fy_begndt;?>';
var sdateTo   = '<?php echo $fy_end;?>';
var bsd_json_file = <?php echo $bsd_json_file;?>;
var bo_state_code = '<?php echo sprintf('%02d', $bo_state_code);?>';
var tax_json_data  = <?php echo json_encode($tax_json_data);?>;
var bo_gstin_type='<?php echo $bo_gstin_type;?>';
var goods_rate   ='1';
var services_rate='6';
$("#bsd_comp_applytax_json").val(JSON.stringify(billsundry_comp_json_data));
function calculate_fcChanged(currency_id,billsundry_colModel,colModel,currency_symbol,modalshowhide)
    {
	
     if(currency_id>1){
         if(modalshowhide=="1"){
			   	$("#view_fcrates_modal #fcy_forex_rate").val("");
                $("#view_fcrates_modal #selcyrlabel").html(currency_symbol);				
				$("#view_fcrates_modal #fcy_voucher_no").html($("#voucher").val());
				$("#view_fcrates_modal #fcy_voucher_date").html($("#voucher_date").val());				
			    $("#view_fcrates_modal").modal('show');			   
         }
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
    
var billsundry_dataModel = {"data":<?php echo json_encode($billsundry_json_data);?>}
</script>
<script src="<?php echo base_url();?>/public/js/sale_without_item.js"></script>
<script src="<?php echo base_url();?>/public/js/sale_without_item_grid.js"></script>
<script> 
function grid_total_item(obj,currency='')
    {
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
 
            data.forEach(function(row){
                if(row.account_name != '' && row.account_name != undefined && row.amount != '' && row.amount != undefined)
                {
				  if(currency=='€' || currency=='$'){
					total += parseAmount(row.item_amountfc); 
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
$(function(){	
	let input = document.getElementById('clone_party_id');
   let timeout = null;
  input.addEventListener('keyup', function (e) {
    //clearTimeout(timeout);
    //timeout = setTimeout(function () {		
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

    
$(document).on("click",".deletebtn",function(){
   confirm_delete(baseurl+"/admin/sales_order/delete/<?php echo $voucher_txn_id;?>,");         
   return false
})


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
						if(stcode==bo_state_code){							
						   if(rd.bl_nature=='33')	//CGST		
						       rd.billsundry_amount = parseFloat(cgst_total);
							else if(rd.bl_nature=='34')	//SGST		
						       rd.billsundry_amount = parseFloat(sgst_total);
							else if(rd.bl_nature=='35')	//IGST		
						       rd.billsundry_amount = 0; 
							else if(rd.bl_nature=='36')	//CESS		
						       rd.billsundry_amount = cess_total;    
							 else
								rd.billsundry_amount = 0; 	
						}else{
							if(rd.bl_nature=='33')	//CGST		
						       rd.billsundry_amount = 0;
							else if(rd.bl_nature=='34')	//SGST		
						       rd.billsundry_amount = 0;
							else if(rd.bl_nature=='35')	//IGST		
						       rd.billsundry_amount = parseFloat(igst_total);
							else if(rd.bl_nature=='36')	//CESS		
						       rd.billsundry_amount = cess_total;     
							else
								rd.billsundry_amount = 0; 	
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
                    stop_loader();
                    alert_success(response.message);
                    window.location.href='<?php echo history_back();?>';
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
            { title: "PARTICULARS", sortable:false,dataIndx: "account_name", width: 100,dataType: "string",cls: 'pq-drop-icon pq-side-icon',
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
                    return '<span data-title-tooltip="  '+account_balance+' ">'+ui.cellData+'</span>';
                    }
                    else 
                    return ui.cellData;
                    }
                  }  
                },
            { title: "DESCRIPTION",sortable:false, width: 100, dataType: "string", dataIndx: "description"},
			{ title: "AMOUNT(₹)", sortable:false,width: 20, align: "right",dataIndx: "amounttxs",hidden:true,
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
								 rd.amount = parseAmount(rd.amount);
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
            { title: "AMOUNT(₹)", sortable:false,width: 20, align: "right",dataIndx: "amountfc",hidden:true,dataType: "float",
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
                							
                						
                                }
              
						
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
					
					
							return formatAmount(parseAmount(rd.amountfc),currency_symbol);   
						
                    }
                    return '';
                }
              }, 
            { title: "AMOUNT(₹)",sortable:false, width: 20, align: "right",editable: function (ui) {
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
			{ title: "MEMO(₹)", sortable:false,width: 20, align: "right",dataIndx: "memo_amt",hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.memo_amt >0){
						return formatAmount(parseAmount(rd.memo_amt));
                    }
                    return '';
                }
              },  
         ];
        var dataModel = {"data":<?php echo json_encode($json_data);?>};
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            change: calculateSummary, 
            dataReady: calculateSummary,
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
				 $("#taxgrid_search").pqGrid('refreshDataAndView');                
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
						if(rd.is_tax_account=="0")							
						   show_taxsummary_items();						
						   refreshbsd();
                        })
                    }
                },
            },
			{ title: "AMOUNT(₹)", width: 100, align: "right", dataIndx: "billsundry_amountfc" ,hidden:true,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
					if(rd.billsundry_amount != ''){
						var currency_symbol = $('select[name="currency_id"] option:selected').attr('data-id');
						if(forexcrncy_rate_sel!=''){
						var fcy_forex_rate =forexcrncy_rate_sel;
						$("#view_fcrates_modal #fcy_forex_rate").val(forexcrncy_rate_sel)
						}
						else					
						var fcy_forex_rate = $("#view_fcrates_modal #fcy_forex_rate").val();  
						if(fcy_forex_rate!='' && $("#currency_id").val() >1){
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
            change: billsundry_calculateSummary,
            dataReady: billsundry_calculateSummary,
            editable: true,
            wrap:false,
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
		if(is_taxinc_enabled=="1")
           TaxInsChanged(billsundry_colModel,colModel,true);

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
				show_taxsummary_items();				
				refreshbsd();
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
		$('#memoCheck').on('change', function(){ // on change of state
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
			
				$('#grid_search').pqGrid('option', 'dataModel.data', data);
                $('#grid_search').pqGrid('refreshDataAndView');
			  
			    item_bsd_totals(); 
			} 
			
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
							var itmrate       = igst_rate_val;
							var x             = parseAmount((itmrate/100));
							var y             = parseAmount(x)+parseAmount(1);
							var totalprice    = parseAmount(obj.amounttxs);
							var taxableamount = parseAmount(totalprice/y);
							var tax_amt       = parseAmount(taxableamount);
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
	 $(window).on('load', show_taxsummary_items() );
 </script>	
</body>
</html>
