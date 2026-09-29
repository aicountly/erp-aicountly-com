<?php 
helper('custom_helper');  // Load the helper
$commands = getCommandMappings();



$local_session      = \Config\Services::session();
 if($local_session->get('ses_company_id')!=''){
    $fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
	$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
  } 
 else{
   $fy_begndt  =  date('01-04-Y');
   $fy_end     =  date('01-04-Y');
  }  
 $allcompfy         =  $local_session->get('allcompfy');
 $allcompfym         =  $local_session->get('allcompfym');
 $current_bo          = $local_session->get('ses_boid');
 $ses_bostecd          = $local_session->get('ses_bostecd');
 $ses_comp_fy_id =  $local_session->get('ses_comp_fy_id');
 
 $transaction_pages = array(base_url().'/admin/sales/item',
						  base_url().'/admin//sales/non_item',	
                          base_url().'/admin/sales_order/item',
						  base_url().'/admin/sales_order/non_item',
						  base_url().'/admin/credit_note/item',						  
						  base_url().'/admin/credit_note/non_item',
						  base_url().'/admin/delivery_challan/add',
						  base_url().'/admin/quotations/item',
						  base_url().'/admin/quotations/non_item',
						  base_url().'/admin/purchase/item',
						  base_url().'/admin//purchase/non_item',
						  base_url().'/admin/purchase_order/item',
						  base_url().'/admin/purchase_order/non_item',
						  base_url().'/admin/debit_note/item',
						  base_url().'/admin/debit_note/non_item',
						  base_url().'/admin/inward_challan/add',
						  base_url().'/admin/purchase_requisition/with_amount', 
						  base_url().'/admin/purchase_requisition/without_amount',
						  base_url().'/admin/vouchers/invoice/9',						  
						  base_url().'/admin/vouchers/invoice/13',
						  base_url().'/admin/vouchers/invoice/1',
						  base_url().'/admin/vouchers/invoice/5',
						  base_url().'/admin/memorandum/invoice',
						  base_url().'/admin/stock_transfer/mc',
						  base_url().'/admin/physical_verification/add',
						  base_url().'/admin/stock_journal/invoice/19',
						  base_url().'/admin/stock_journal/invoice/20',
						  base_url().'/admin/stock_journal/invoice/18',
						  base_url().'/admin/consignment_packing',
						  base_url().'/admin/production/add',
						  base_url().'/admin/sales/edit',						  
						  base_url().'/admin/sales_order/edit',
						  base_url().'/admin/credit_note/edit',
						  base_url().'/admin/delivery_challan/edit',
						  base_url().'/admin/quotations/edit',
						  base_url().'/admin/purchase/edit',
						  base_url().'/admin/purchase_order/edit',
						  base_url().'/admin/debit_note/edit',
						  base_url().'/admin/inward_challan/edit',
						  base_url().'/admin/purchase_requisition/edit',
						  base_url().'/admin/vouchers/edit',
						  base_url().'/admin/stock_transfer/edit',
						  base_url().'/admin/production/edit',
						  base_url().'/admin/stock_journal/edit',
						  base_url().'/admin/consignment_packing/edit',
						  base_url().'/admin/sales/duplicate'
						  
						  ); 
	$current_url            = current_url();
	$cnfrm_on_addedit_pages = 0;	
	foreach ($transaction_pages as $url) {  
    if (strpos($current_url, $url) !== FALSE) { 
        $cnfrm_on_addedit_pages=1;        
    }
}

// check for purchase pages
$purchase_pages = array(  base_url().'/admin/purchase/item',
						  base_url().'/admin//purchase/non_item',
						  base_url().'/admin/purchase/edit'
						  ); 
	$cnfrm_purchase_pages = 0;	
	foreach ($purchase_pages as $url) {  
    if (strpos($current_url, $url) !== FALSE) { 
        $cnfrm_purchase_pages=1;        
    }
	}

?>
<style>
  #suggestionsDropdown li.active {
    background-color: #007bff;
    color: white;
  }
  .tempus-dominus-widget.dark {
  background-color: #fff !important;
  color: #212529 !important;
}

.tempus-dominus-widget .toolbar i {
  color: #212529 !important;
  font-size: 16px;
}

.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight).new,
.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight).old {
  color: hsla(33, 4%, 57%, 0.38);
}

.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight).active,
.tempus-dominus-widget.dark .date-container-months div:not(.no-highlight).active,
.tempus-dominus-widget.dark .date-container-years div:not(.no-highlight).active {
  background-color: var(--green);
  color: var(--green-soft);
}

.tempus-dominus-widget .date-container-days div:not(.no-highlight).today {
  color: var(--green-soft) !important;
}

.tempus-dominus-widget.dark .date-container-days .dow {
  color: #212529 !important;
}

.tempus-dominus-widget.dark .date-container-days div:not(.no-highlight):hover,
.tempus-dominus-widget.dark .date-container-months div:not(.no-highlight):hover,
.tempus-dominus-widget.dark .date-container-years div:not(.no-highlight):hover {
  background: var(--green-soft) !important;
  color: var(--green-hover) !important;
  border: 1px solid var(--green-hover) !important;
}

.tempus-dominus-widget.dark .toolbar div:hover {
  background: var(--green-soft);
  color: var(--green);
}

.tempus-dominus-widget {
  background: #fff !important;
  border: 1px solid var(--green) !important;
  border-radius: 8px;
  color: #212529 !important;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.tempus-dominus-widget .calendar-header,
.tempus-dominus-widget .decade,
.tempus-dominus-widget .year,
.tempus-dominus-widget .month {
  font-weight: 600;
  color: #212529;
}

.tempus-dominus-widget .calendar-days .day {
  font-size: 14px;
  width: 2.4rem;
  height: 2.4rem;
  line-height: 2.4rem;
  text-align: center;
  margin: 2px auto;
  border-radius: 50%;
  font-weight: 500;
  background-color: transparent !important;
  color: #212529 !important;
  transition: all 0.2s;
}

.tempus-dominus-widget .calendar-days .day:hover {
  background-color: var(--green-soft) !important;
  color: var(--green) !important;
}

.tempus-dominus-widget .calendar-days .day.active,
.tempus-dominus-widget .calendar-days .day.active:hover {
  background-color: var(--green) !important;
  color: #ffffff !important;
  font-weight: 600;
}

.tempus-dominus-widget .calendar-days .day.today:not(.active) {
  border: 2px solid var(--green);
  color: var(--green) !important;
  font-weight: 600;
  background-color: transparent !important;
}

.tempus-dominus-widget .btn-primary {
  background-color: var(--green);
  border-color: var(--green);
}

.tempus-dominus-widget .btn-primary:hover {
  background-color: var(--green-hover);
  border-color: var(--green-hover);
}  
</style>
<div id="suggestionsDropdown" 
     style="display: none; position: absolute; background: white; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; list-style-type: none; left: 33px; z-index: 1000;">
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Error message will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;"> <!-- fixed width and centered -->
    <div class="modal-content text-center shadow p-4" style="border-radius: 16px;">

      <!-- Icon -->
      <div class="text-danger" style="font-size: 64px;">
        <i class="bi bi-x-circle-fill"></i>
      </div>

      <!-- Title -->
      <h5 class="modal-title mt-3 mb-2" id="modalTitle">Search</h5>

      <!-- Body -->
     <!-- Body -->
    <div class="modal-body p-0" id="modalBody">
      <!-- <label for="crsInput" class="form-label">CRS Keyword</label>
      <input type="text" id="crsInput" class="form-control text-center" readonly /> -->
    </div>


      <!-- Button -->
      <div class="mt-4">
        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">Ok</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal for CRS Warning / Result -->
<div class="modal fade" id="crsModal" tabindex="-1" aria-labelledby="crsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="crsModalLabel">CRS Type Search</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="crsModalBody">
        <!-- Will be filled dynamically -->
      </div>
    </div>
  </div>
</div>









<script>

 
  function adjustCommandDropdown() {
    const dropdown = document.getElementById('suggestionsDropdown');
    const sidebar = document.getElementById('navVerticalNav');
    if (!dropdown || !sidebar) return;

    // Detect if sidebar is collapsed based on width
    const sidebarWidth = sidebar.offsetWidth;

    if (sidebarWidth < 80) {
      dropdown.style.left = '19px';
    } else {
      dropdown.style.left = '33px';
    }
  }

  // Adjust on page load
  document.addEventListener('DOMContentLoaded', adjustCommandDropdown);

  // Adjust when toggle is clicked
  const toggleBtn = document.querySelector('.navbar-vertical-toggle');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      setTimeout(adjustCommandDropdown, 300); // delay for UI animation
    });
  }

  // Optional: adjust on window resize
  window.addEventListener('resize', adjustCommandDropdown);


var cnfrm_purchase_pages=<?php echo $cnfrm_purchase_pages;?>;
function gotoback(){
	const previousURL = document.referrer;	
	//console.log(previousURL);
	if (previousURL!='' && (previousURL.includes("/edit/") || previousURL.includes("/modify/") )){
		try {
		if(previousURL!='')
			history.go(-2);
		 else 
		  window.location.href='<?php echo base_url();?>admin/dashboard';	 
		}catch (e) {
			window.location.href='<?php echo base_url();?>admin/dashboard';
			}
	} else {
		
		try {
		if(previousURL!='')
			history.go(-1);
	     else 
		  window.location.href='<?php echo base_url();?>admin/dashboard';
	  
		}catch (e) {
			window.location.href='<?php echo base_url();?>admin/dashboard';
			}
	}
}
var filter_conditions=[{ "contain": "Contains" },{ "equal": "Equal To" },{ "notequal": "Not Equal To" },{ "less": "Less Than" },{ "great": "Great Than" }];
function unit_territory(pos_id,taxmodel_array){
	
	// if pos exists in Unit Territory then replace SGST to UGST 
	if(taxmodel_array[2].dataIndx=="tax_amt_fcy"){
    if(ugst_states.indexOf(pos_id) !== -1){
	   taxmodel_array[6].title='UGST';	
	   }   
	   else
		 taxmodel_array[6].title='SGST';
	}
	else{
	if(ugst_states.indexOf(pos_id) !== -1){
	   taxmodel_array[5].title='UGST';	
	   }   
	   else
		 taxmodel_array[5].title='SGST';	
	}
	$("#taxgrid_search").pqGrid('refreshDataAndView');	
}
	
function invoice_type_changes(){
    var party_gstinval  =  $('#party_id option[value="'+$("#party_id option:first").val()+'"]').attr('data-gstin');
    var eco_id          = $('select[name="eco_id"] option:selected').val();
	var pos_code        = $('select[name="pos"] option:selected').val();
	var reverse_charges = $('select[name="reverse_charges"] option:selected').val();
	var supply_type     = $('select[name="supply_type"] option:selected').val();
	if(typeof eco_id=='undefined' || eco_id=='')
		 eco_id=0;
	
	if(typeof supply_type=='undefined' || supply_type=='')
		 supply_type=0;
	
    if(typeof reverse_charges=='undefined' || reverse_charges=='')
		 reverse_charges=0;
	
	if(party_gstinval!=''){
		$("#party_gstin_label").show();
		$(".party_gstin_name").html(party_gstinval);
		
	}else{$("#party_gstin_label").hide();
		$(".party_gstin_name").html("");
		$("#invoice_type_label").html("");
		$('input[name="invoice_type"]').val('');
		} 	 
	
     
	 
if(supply_type=='14' || supply_type=='15' || supply_type=='16' || supply_type=='2' || supply_type=='3' || supply_type=='4' || supply_type=='5' || supply_type=='13'){	 
	if(supply_type=='2'){
		 $('#invoice_type').val("EXPWP");			 
		 $('#invoice_type_label').html('EXPORTS WITH PAYMENT');		
	 }
	else  if(supply_type=='14'){
		 $('#invoice_type').val("NILSPLY");			 
		 $('#invoice_type_label').html('NILL RATED');		
	 }	
	else if(supply_type=='15'){
		 $('#invoice_type').val("EXMSPLY");			 
		 $('#invoice_type_label').html('EXEMPTED');		
	 }
	else if(supply_type=='16'){
		 $('#invoice_type').val("NONGSTS");			 
		 $('#invoice_type_label').html('NON GST SUPPLIES');		
	 }	
      else if(supply_type=='3'){
		 $('#invoice_type').val("EXPWOP");	  
		 $('#invoice_type_label').html('EXPORTS WITHOUT PAYMENT');	
	 }	
	  else if(supply_type=='4'){
		 $('#invoice_type').val("SEZWP");	 
		 $('#invoice_type_label').html('SEZ WITH PAYMENT');	
	 }
	 else  if(supply_type=='5'){
		 $('#invoice_type').val("SEZWOP");		  
		 $('#invoice_type_label').html('SEZ WITHOUT PAYMENT');	
	 } 
	 else if(supply_type=='13'){
		 $('#invoice_type').val("DE");		  
		 $('#invoice_type_label').html('DEEMED EXPORT');	
	 }
	else{
	  $('#invoice_type').val("CBW");	  
	  $('#invoice_type_label').html('CUSTOM BONDED WAREHOUSE (CBW)');
	 }
   }
   else{
	  if(party_gstinval!="" && eco_id=="0" && reverse_charges=="0"){
		$('#invoice_type').val("B2B");		
		$('#invoice_type_label').html('B2B REGULAR');
	}
	else if(party_gstinval!="" && eco_id=="0" && (reverse_charges=="1")){
		$('#invoice_type').val("B2BRCM");		
		$('#invoice_type_label').html('B2B REVERSE CHARGE');
	}
	else if(party_gstinval=="" && eco_id=="0"){
		var items_total=0;
		items_total += grid_total_item($('#grid_search'));
		items_total += grid_total_bsd($('#billsundry_search'));

		var voucher_date = $("#voucher_date").val();
		if(typeof voucher_date=='undefined')
		 var voucher_date = $("#sale_date").val();
	 
		var invoice_type = get_b2c_invoice_type(voucher_date,items_total,pos_code,bo_state_code);
		$('#invoice_type').val(invoice_type);
		$('#invoice_type_label').html(invoice_type);
	}
	else{
		$('#invoice_type').val("B2B");		
		$('#invoice_type_label').html('B2B REGULAR');
	   }
   }
		
}

/* function invoice_type_changes(){
    var party_gstinval  =  $('#party_id option[value="'+$("#party_id option:first").val()+'"]').attr('data-gstin');
    var eco_id          = $('select[name="eco_id"] option:selected').val();
	var reverse_charges = $('select[name="reverse_charges"] option:selected').val();
	var supply_type     = $('select[name="supply_type"] option:selected').val();
	if(typeof eco_id=='undefined' || eco_id=='')
		 eco_id=0;
	
	if(typeof supply_type=='undefined' || supply_type=='')
		 supply_type=0;
	
    if(typeof reverse_charges=='undefined' || reverse_charges=='')
		 reverse_charges=0;
	
	if(party_gstinval!=''){
		$("#party_gstin_label").show();
		$(".party_gstin_name").html(party_gstinval);
		
	}else{$("#party_gstin_label").hide();
		$(".party_gstin_name").html("");
		$("#invoice_type_label").html("");
		$('input[name="invoice_type"]').val('');
		} 	 
	if(reverse_charges=="1" && party_gstinval!='' && eco_id=="0"){
	 $('#invoice_type').val("B2BRCM");		
	 $('#invoice_type_label').html('B2B REVERSE CHARGE');			
	}else{
     if(supply_type=='14'){
		 $('#invoice_type').val("NILSPLY");			 
		 $('#invoice_type_label').html('NILL RATED');		
	 }	
	if(supply_type=='15'){
		 $('#invoice_type').val("EXMSPLY");			 
		 $('#invoice_type_label').html('EXEMPTED');		
	 }
	if(supply_type=='16'){
		 $('#invoice_type').val("NONGSTS");			 
		 $('#invoice_type_label').html('NON GST SUPPLIES');		
	 }	
		
	
	 if(supply_type=='2' || supply_type=='3' || supply_type=='4' || supply_type=='5' || supply_type=='13')
	 {
	 if(supply_type=='2'){
		 $('#invoice_type').val("EXPWP");			 
		 $('#invoice_type_label').html('EXPORTS WITH PAYMENT');		
	 }
     else if(supply_type=='3'){
		 $('#invoice_type').val("EXPWOP");	  
		 $('#invoice_type_label').html('EXPORTS WITHOUT PAYMENT');	
	 }
	 else if(supply_type=='13'){
		 $('#invoice_type').val("DE");	  
		 $('#invoice_type_label').html('DEEMED EXPORT');	
	 }
	 else if(supply_type=='4'){
		 $('#invoice_type').val("SEZWP");	 
		 $('#invoice_type_label').html('SEZ WITH PAYMENT');	
	 }
	 else if(supply_type=='5'){
		 $('#invoice_type').val("SEZWOP");		  
		 $('#invoice_type_label').html('SEZ WITHOUT PAYMENT');	
	 }else if(supply_type=='13'){
		 $('#invoice_type').val("DE");		  
		 $('#invoice_type_label').html('DEEMED EXPORT');	
	 }
	 else{
	  $('#invoice_type').val("CBW");	  
	  $('#invoice_type_label').html('CUSTOM BONDED WAREHOUSE (CBW)');
	 }	
		
	}else{
	if(party_gstinval!="" && eco_id=="0" && reverse_charges=="0"){
		$('#invoice_type').val("B2B");		
		$('#invoice_type_label').html('B2B REGULAR');
	}
	else if(party_gstinval!="" && eco_id=="0" && (reverse_charges=="1")){
		$('#invoice_type').val("B2BRCM");		
		$('#invoice_type_label').html('B2B REVERSE CHARGE');
	}
	else if(party_gstinval=="" && eco_id=="0"){
		var items_total=0;
		items_total += grid_total_item($('#grid_search'));
		items_total += grid_total_bsd($('#billsundry_search'));

		var voucher_date = $("#voucher_date").val();
		if(typeof voucher_date=='undefined')
		 var voucher_date = $("#sale_date").val();
	 
		var invoice_type = get_b2c_invoice_type(voucher_date,items_total);
		$('#invoice_type').val(invoice_type);
		$('#invoice_type_label').html(invoice_type);
	}
	else{
		$('#invoice_type').val("B2B");		
		$('#invoice_type_label').html('B2B REGULAR');
	   }
	  }
	}	
} */

function show_taxsummary_items_hsnwise(tax_hsn_sac,gridtype){
	if(typeof tax_hsn_sac=='undefined')
		return false;
	var currency_symbol = $('#currency_id').find(":selected").data("id"); 
	var currency_val = $('#currency_id').find(":selected").val(); 	
	var modal_body='';
	var footertotal=0;
	var footerfytotal=0;	
	var footer_igst_total=0;
	var footer_cess_total=0;
	var footer_tax_total=0;
    var footer_taxfcy_total=0;	
	var footer_cgst_total=0;
	var footer_sgst_total=0;
	var grid_items_data = $('#grid_search').pqGrid('option', 'dataModel.data');
    var pos_id = $("#pos").find(":selected").val();
   var grid_cols_data = $('#grid_search').pqGrid('option', 'colModel');
   var supply_type     = $('select[name="supply_type"] option:selected').val();
   if(grid_cols_data[2].dataIndx=="tax_amt_fcy"){
    if(ugst_states.indexOf(pos_id) !== -1)  
	   {
	   var condition_cols ='<th>CGST</th><th>UGST</th>';
	
	   }   
	   else
		var condition_cols ='<th>CGST</th><th>SGST</th>';
	}
	else{
	if(ugst_states.indexOf(pos_id) !== -1)  
	   {
	   var condition_cols ='<th>CGST</th><th>UGST</th>';
	
	   }   
	   else
		var condition_cols ='<th>CGST</th><th>SGST</th>';	
	}
	
	if(gridtype=='itm'){
	   if(currency_val>1)
		modal_body +='<table width="100%" class="table table-sm table-striped table-bordered"><tr><th>Item</th><th>Unit</th><th>Amount(FCY)</th><th>Amount</th><th>IGST</th>'+condition_cols+'<th>CESS</th><th>TOTAL TAX</th><th>TOTAL TAX(FCY)</th></tr>';
    else
		modal_body +='<table width="100%" class="table table-sm table-striped table-bordered"><tr><th>Item</th><th>Unit</th><th>Amount</th><th>IGST</th>'+condition_cols+'<th>CESS</th><th>TOTAL TAX</th></tr>';
    
	 var fcrates = $("#view_fcrates_modal #fcy_forex_rate").val();
	   $.each(grid_items_data, function(index,obj){
		 var item_hsn_sac = obj.item_hsn_sac;
		  if(tax_hsn_sac==item_hsn_sac){
			
			if(cnfrm_purchase_pages==1){
				var igst_rate_val = obj.igst_rate;
			}
			else{
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
			
			var show_footer_tax_total = '';	
			if(parseAmount(fcrates) >0){			
              	var itmamount =  formatAmount(obj.item_amountfc,currency_symbol);
				footerfytotal +=parseAmount(obj.item_amountfc);
				footertotal += parseAmount(obj.item_amountfc/fcrates);
				
				var cgst_tt = parseAmount(igst_rate_val/2*(obj.item_amountfc/fcrates)/100);
				var sgst_tt = parseAmount(igst_rate_val/2*(obj.item_amountfc/fcrates)/100)
				var igst_tt = parseAmount(igst_rate_val*(obj.item_amountfc/fcrates)/100);
				
				if(obj.cess_basis=="1"){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.item_amountfc/fcrates)/100);
					}
				else if(obj.cess_basis=="2" ){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.item_mrp*obj.item_qty)/100);	
					}
				else{
					 var cess_tt = parseAmount(0);
					}
					if(pos_id==bo_state_code){
					 footer_igst_total +=parseAmount(0);
					 footer_cess_total +=cess_tt;
					 footer_cgst_total +=cgst_tt;
					 footer_sgst_total +=sgst_tt;	
					}
					else{
					footer_igst_total +=igst_tt;
					footer_cess_total +=cess_tt;
					footer_cgst_total +=parseAmount(0);
					footer_sgst_total +=parseAmount(0);
					}
					
			}
            else {
				if($('#taxInclusive').is(":checked")){
					var itmrate = igst_rate_val;
						 var x= parseAmount((itmrate/100));
						 var y = parseAmount(x)+parseAmount(1);
						 var totalprice = parseAmount(obj.item_qty)*parseAmountPrice(obj.item_price,4);
					     var taxableamount = parseAmount(totalprice/y);
					var itmamount = formatAmount(taxableamount);
				}else
					var itmamount = formatAmount(obj.item_qty * parseAmountPrice(obj.item_price,4));
				
				footertotal +=parseAmount(obj.item_amount);
				var cgst_tt = parseAmount(igst_rate_val/2*(obj.item_amount)/100);
				var sgst_tt = parseAmount(igst_rate_val/2*(obj.item_amount)/100)
				var igst_tt = parseAmount(igst_rate_val*(obj.item_amount)/100);
				
				if(obj.cess_basis=="1"){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.item_amount)/100);
					}
				else if(obj.cess_basis=="2" ){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.item_mrp*obj.item_qty)/100);	
					}
				else{
					 var cess_tt = 0;
					}
					if(pos_id==bo_state_code){
					 footer_igst_total +=0;
					 footer_cess_total +=cess_tt;
					 footer_cgst_total +=cgst_tt;
					 footer_sgst_total +=sgst_tt;	
					}
					else{
					footer_igst_total +=igst_tt;
					footer_cess_total +=cess_tt;
					footer_cgst_total +=0;
					footer_sgst_total +=0;	
					}
					
					
			}
			
			var cess_basis = obj.cess_basis;
			var cess_rate  = obj.cess_rate;
			
			var total_tax = parseAmount(igst_tt)+parseAmount(cess_tt);
				

			footer_tax_total +=total_tax;
			
			
			
			if(pos_id==bo_state_code){
				var sgst=formatAmount(sgst_tt);
				var cgst=formatAmount(cgst_tt);
				var igst =formatAmount(0);
			}else{
				var sgst=formatAmount(0);
				var cgst=formatAmount(0);
				var igst =formatAmount(igst_tt);
			}
			
			if(supply_type==3 || supply_type==5 || supply_type==14 || supply_type==15 || supply_type==16)// no tax will applied on this type of supply types
		{
			var item_rate = igst=cgst=sgst=cess_tt=total_tax=0;
		}else{
			var item_rate = obj.igst_rate
		}
			
			
			if(currency_val>1){			
			footer_taxfcy_total +=total_tax*fcrates;
			modal_body += '<tr><td>'+obj.item_name+'('+parseAmount(item_rate)+'%)</td><td>'+obj.item_unit+'</td><td>'+itmamount+'</td><td>'+formatAmount(obj.item_amountfc/fcrates)+'</td><td>'+igst+'</td><td>'+cgst+'</td><td>'+sgst+'</td><td>'+formatAmount(cess_tt)+'</td><td>'+formatAmount(total_tax)+'</td><td>'+formatAmount(total_tax*fcrates,currency_symbol)+'</td></tr>';
		    }
			else{
			footer_taxfcy_total +=parseAmount(0);			
			modal_body += '<tr><td>'+obj.item_name+'('+parseAmount(item_rate)+'%)</td><td>'+obj.item_unit+'</td><td>'+itmamount+'</td><td>'+igst+'</td><td>'+cgst+'</td><td>'+sgst+'</td><td>'+formatAmount(cess_tt)+'</td><td>'+formatAmount(total_tax)+'</td></tr>';
		    }	
		  }
	   });
	   
	   
	   if(supply_type==3 || supply_type==5 || supply_type==14 || supply_type==15 || supply_type==16)// no tax will applied on this type of supply types
		{
			
		  var sgst=formatAmount(0);
				var cgst=formatAmount(0);
				var igst =formatAmount(0);		
           var show_footer_tax_total = formatAmount(0);
		}else
		{
			var show_footer_tax_total =  formatAmount(footer_tax_total);
		}
		
	   
	   if(currency_val>1){
		modal_body += '<tr><td colspan="10"><br></td></tr>';	    
	    modal_body += '<tr style="font-weight:800;font-size:15px;"><td></td><td></td><td>'+formatAmount(footerfytotal,currency_symbol)+'</td><td>'+formatAmount(footertotal)+'</td><td>'+formatAmount(footer_igst_total)+'</td><td>'+formatAmount(footer_cgst_total)+'</td><td>'+formatAmount(footer_sgst_total)+'</td><td>'+formatAmount(footer_cess_total)+'</td><td>'+show_footer_tax_total+'</td><td>'+formatAmount(footer_taxfcy_total,currency_symbol)+'</td></tr>';
	   }
	  else{
		modal_body += '<tr><td colspan="8"><br></td></tr>';  
	    modal_body += '<tr style="font-weight:800;font-size:15px;"><td></td><td></td><td>'+formatAmount(footertotal)+'</td><td>'+formatAmount(footer_igst_total)+'</td><td>'+formatAmount(footer_cgst_total)+'</td><td>'+formatAmount(footer_sgst_total)+'</td><td>'+formatAmount(footer_cess_total)+'</td><td>'+show_footer_tax_total+'</td></tr>';
	   }
			
	  $("#ItemsHSNModal .modal-body").html(modal_body);
	  if(currency_val>1){
	  $("#ItemsHSNModal .modal-title").html('HSN - '+tax_hsn_sac+"&nbsp;&nbsp;&nbsp;FCY Rate("+fcrates+")"); 
	  }else{
	   $("#ItemsHSNModal .modal-title").html('HSN - '+tax_hsn_sac); 	  
	  }
	  $("#ItemsHSNModal").modal("show");
	}

if(gridtype=='woitm'){
	   if(currency_val>1)
		modal_body +='<table width="100%" class="table table-sm table-striped table-bordered"><tr><th>Particulars</th><th>Amount(FCY)</th><th>Amount</th><th>IGST</th>'+condition_cols+'<th>CESS</th><th>TOTAL TAX</th><th>TOTAL TAX(FCY)</th></tr>';
    else
		modal_body +='<table width="100%" class="table table-sm table-striped table-bordered"><tr><th>Particulars</th><th>Amount</th><th>IGST</th>'+condition_cols+'<th>CESS</th><th>TOTAL TAX</th></tr>';
    
	
	
	 var fcrates = $("#view_fcrates_modal #fcy_forex_rate").val();
	   $.each(grid_items_data, function(index,obj){
		 var item_hsn_sac = obj.item_hsn_sac;
		  if(tax_hsn_sac==item_hsn_sac){
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
			if(parseAmount(fcrates) >0){			
              	var itmamount =  formatAmount(obj.amountfc,currency_symbol);
				footerfytotal +=parseAmount(obj.amountfc);
				footertotal += parseAmount(obj.amountfc/fcrates);
				
				var cgst_tt = parseAmount(igst_rate_val/2*(obj.amountfc/fcrates)/100);
				var sgst_tt = parseAmount(igst_rate_val/2*(obj.amountfc/fcrates)/100)
				var igst_tt = parseAmount(igst_rate_val*(obj.amountfc/fcrates)/100);
				
				if(obj.cess_basis=="1"){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.amountfc/fcrates)/100);
					}
				else if(obj.cess_basis=="2" ){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.item_mrp*obj.item_qty)/100);	
					}
				else{
					 var cess_tt = parseAmount(0);
					}
					if(pos_id==bo_state_code){
					 footer_igst_total +=parseAmount(0);
					 footer_cess_total +=cess_tt;
					 footer_cgst_total +=cgst_tt;
					 footer_sgst_total +=sgst_tt;	
					}
					else{
					footer_igst_total +=igst_tt;
					footer_cess_total +=cess_tt;
					footer_cgst_total +=parseAmount(0);
					footer_sgst_total +=parseAmount(0);
					}
					
			}
            else {
				if($('#taxInclusive').is(":checked")){
					var itmrate = igst_rate_val;
						 var x= parseAmount((itmrate/100));
						 var y = parseAmount(x)+parseAmount(1);
						 var totalprice = parseAmount(obj.amounttxs);
					     var taxableamount = parseAmount(totalprice/y);
					var itmamount = formatAmount(taxableamount);
				}else
					var itmamount = formatAmount(parseAmountPrice(obj.amount,4));
				
				footertotal +=parseAmount(obj.amount);
				var cgst_tt = parseAmount(igst_rate_val/2*(obj.amount)/100);
				var sgst_tt = parseAmount(igst_rate_val/2*(obj.amount)/100)
				var igst_tt = parseAmount(igst_rate_val*(obj.amount)/100);
				
				if(obj.cess_basis=="1"){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.amount)/100);
					}
				else if(obj.cess_basis=="2" ){
					 var cess_tt = parseAmount(obj.cess_rate*(obj.item_mrp*obj.item_qty)/100);	
					}
				else{
					 var cess_tt = 0;
					}
					if(pos_id==bo_state_code){
					 footer_igst_total +=0;
					 footer_cess_total +=cess_tt;
					 footer_cgst_total +=cgst_tt;
					 footer_sgst_total +=sgst_tt;	
					}
					else{
					footer_igst_total +=igst_tt;
					footer_cess_total +=cess_tt;
					footer_cgst_total +=0;
					footer_sgst_total +=0;	
					}
					
					
			}
			
			var cess_basis = obj.cess_basis;
			var cess_rate  = obj.cess_rate;
			
			var total_tax = parseAmount(igst_tt)+parseAmount(cess_tt);
			footer_tax_total +=total_tax;
			
			if(pos_id==bo_state_code){
				var sgst=formatAmount(sgst_tt);
				var cgst=formatAmount(cgst_tt);
				var igst =formatAmount(0);
			}else{
				var sgst=formatAmount(0);
				var cgst=formatAmount(0);
				var igst =formatAmount(igst_tt);
			}
			
			if(currency_val>1){			
			footer_taxfcy_total +=total_tax*fcrates;
			modal_body += '<tr><td>'+obj.account_name+'('+parseAmount(obj.igst_rate)+'%)</td><td>'+itmamount+'</td><td>'+formatAmount(obj.amountfc/fcrates)+'</td><td>'+igst+'</td><td>'+cgst+'</td><td>'+sgst+'</td><td>'+formatAmount(cess_tt)+'</td><td>'+formatAmount(total_tax)+'</td><td>'+formatAmount(total_tax*fcrates,currency_symbol)+'</td></tr>';
		    }
			else{
			footer_taxfcy_total +=parseAmount(0);			
			modal_body += '<tr><td>'+obj.account_name+'('+parseAmount(obj.igst_rate)+'%)</td><td>'+itmamount+'</td><td>'+igst+'</td><td>'+cgst+'</td><td>'+sgst+'</td><td>'+formatAmount(cess_tt)+'</td><td>'+formatAmount(total_tax)+'</td></tr>';
		    }	
		  }
	   });
	   
	   
	   if(currency_val>1){
		modal_body += '<tr><td colspan="9"><br></td></tr>';	    
	    modal_body += '<tr style="font-weight:800;font-size:15px;"><td></td><td>'+formatAmount(footerfytotal,currency_symbol)+'</td><td>'+formatAmount(footertotal)+'</td><td>'+formatAmount(footer_igst_total)+'</td><td>'+formatAmount(footer_cgst_total)+'</td><td>'+formatAmount(footer_sgst_total)+'</td><td>'+formatAmount(footer_cess_total)+'</td><td>'+formatAmount(footer_tax_total)+'</td><td>'+formatAmount(footer_taxfcy_total,currency_symbol)+'</td></tr>';
	   }
	  else{
		modal_body += '<tr><td colspan="7"><br></td></tr>';  
	    modal_body += '<tr style="font-weight:800;font-size:15px;"><td></td><td>'+formatAmount(footertotal)+'</td><td>'+formatAmount(footer_igst_total)+'</td><td>'+formatAmount(footer_cgst_total)+'</td><td>'+formatAmount(footer_sgst_total)+'</td><td>'+formatAmount(footer_cess_total)+'</td><td>'+formatAmount(footer_tax_total)+'</td></tr>';
	   }
			
	  $("#ItemsHSNModal .modal-body").html(modal_body);
	   if(currency_val>1){
	  $("#ItemsHSNModal .modal-title").html('HSN - '+tax_hsn_sac+"&nbsp;&nbsp;&nbsp;FCY Rate("+fcrates+")"); 
	  }else{
	   $("#ItemsHSNModal .modal-title").html('HSN - '+tax_hsn_sac); 	  
	  }
	  $("#ItemsHSNModal").modal("show");
	}	
 	

}
function convertToYMD(dateString) {
  const [day, month, year] = dateString.split('-');  // Split the input string into day, month, and year
  return `${year}-${month}-${day}`;  // Reformat into YYYY-MM-DD
}

function get_b2c_invoice_type(invoice_date, invoice_value, pos_code, bo_state_code) {
    // Work out which threshold applies.
    const RULE_CHANGE_ON = new Date('2024-08-01');          // 1-Aug-2024
    const invDate        = new Date(
        typeof invoice_date === 'string' ? convertToYMD(invoice_date) : invoice_date
    );
    const threshold = invDate < RULE_CHANGE_ON ? 250000 : 100000;

    // Evaluate conditions.
    const isLarge       = invoice_value > threshold;        // value > 2.5 L or 1 L
    const isInterState  = pos_code != bo_state_code;       // POS ≠ branch state

	
	//console.log("invoice_value greather than  threshold => "+invoice_value+" >"+ threshold);
	//console.log("pos_code not eual to  bo_state_code => "+pos_code+" !="+ bo_state_code);
    // Return the type.
	//console.log("(isLarge && isInterState) ? 'B2CL' : 'B2CS' => "+(isLarge && isInterState) ? 'B2CL' : 'B2CS');
    return (isLarge && isInterState) ? 'B2CL' : 'B2CS';
}

if (navigator.connection) {
  //console.log(`Effective network type: ${navigator.connection.effectiveType}`);
 // console.log(`Downlink Speed: ${navigator.connection.downlink}Mb/s`);
 // console.log(`Round Trip Time: ${navigator.connection.rtt}ms`);
} else {
 // console.log('Navigator Connection API not supported');
}
var allcompfy ='<?php echo $allcompfy;?>'; 
var allcompfym ='<?php echo $allcompfym;?>'; 
var cnfrm_on_addedit_pages='<?php echo $cnfrm_on_addedit_pages;?>';
function isDecimal(num) {
         return (num % 1);
      }
function render_qty(str){	

if(str >0 && isDecimal(str)){
var countDecimals = function (value) {	
	if(value!=''){	
     if(Math.floor(value) === value) return 0;
      else{
		if(typeof value !== 'undefined' && isDecimal(value)){
			return value.toString().split(".")[1].length || 0; 
		}
       else{		
		return value;	   
	   }
	 
	  }
	}
	else
		return "0";
     }
if(str!='' && str >0){
var ttdecimals = countDecimals(str);
if(ttdecimals > 4)
	   return str.toFixed(4);
    else
		return str;	 
}
else
	return "";
}
else
	return str;
}


</script>
<style>

[data-title-tooltip]:hover:after {
    opacity: 1;
    transition: all 0.1s ease 0.5s;
    display: contents;
}
[data-title-tooltip]:after {
    content: attr(data-title-tooltip);
    position: absolute;
    bottom: 0px;
    left: 80%;
    padding: 2px 4px 4px 8px;
    color: #222;
    white-space: pre; 
    -moz-border-radius: 5px; 
    -webkit-border-radius: 5px;  
    border-radius: 5px;  
    -moz-box-shadow: 0px 0px 4px #222;  
    -webkit-box-shadow: 0px 0px 4px #222;  
    box-shadow: 0px 0px 4px #222;  
    background-image: -moz-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0, #f8f8f8),color-stop(1, #cccccc));
    background-image: -webkit-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -moz-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -ms-linear-gradient(top, #f8f8f8, #cccccc);  
    background-image: -o-linear-gradient(top, #f8f8f8, #cccccc);
    opacity: 0;
    z-index: 99999;
    display: none;
}
[data-title-tooltip] {
    position: relative;
}    
</style>
<style>
.loading {
  opacity: .5;
}
.hasDatepicker {
    position: relative;
    z-index: 99;
}
#loading-mask {
    position: fixed;
    z-index: 1051;
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    background-color: rgba(200,200,200,0.8);
  }
  
  
  .footer.position-fixed {
  position: fixed !important;
  bottom: 0;
  /*left: 254px;*/
  width: 100%;
  background: #f5f7fa;
  border-top: 1px solid var(--ui-navbar-footer-border-color);
  padding: 0 1.5rem;
  z-index: 999;
}


@media (min-width: 992px) {
    .navbar-vertical-collapsed .navbar-vertical.navbar-expand-lg .navbar-vertical-footer {
        width: calc(3.2rem - 1px)!important;
    }
}

</style>

<script>
// document.addEventListener("DOMContentLoaded", function() {   introJs().start(); });


/* function isValidDate(s) {
  var bits = s.split('-');
  var d = new Date(bits[2] + '-' + bits[1] + '-' + bits[0]);
  return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]));
} */

function LoadingSpinner (form, spinnerHTML) {
  form = form || document;
  var button;
  var spinner = document.createElement('div');
  spinner.innerHTML = spinnerHTML;
  spinner = spinner.firstChild;
  form.addEventListener('click', start);
  form.addEventListener('invalid', stop, true);
  function start (event) {
    if (button) {stop();}
    button = event.target;
    if (button.type === 'submit') {
      //LoadingSpinner.start(button, spinner);
    }
  }
  function stop () {
    LoadingSpinner.stop(button, spinner);
  }
  function destroy () {
    stop();
    form.removeEventListener('click', start);
    form.removeEventListener('invalid', stop, true);
  }
  return {start: start, stop: stop, destroy: destroy};
}

LoadingSpinner.start = function (element, spinner) {
  element.classList.add('loading');
  return document.body.appendChild(spinner);
}

LoadingSpinner.stop = function (element, spinner) {
  element.classList.remove('loading');
  return spinner.remove();
}

var loadingSpinnerHTML = '<div id="loading-mask" class="container-fluid"><div class="row" style="height:100%;"><div id="spinner" class="col d-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;display:block;" width="60px" height="60px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" alt="spinner loading icon" class="img-fluid my-auto"><g transform="translate(78,50)"><g transform="rotate(0)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="1"><animateTransform attributeName="transform" type="scale" begin="-1.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.75s"></animate></circle></g></g><g transform="translate(69.79898987322333,69.79898987322332)"><g transform="rotate(45)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.875"><animateTransform attributeName="transform" type="scale" begin="-1.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.5s"></animate></circle></g></g><g transform="translate(50,78)"><g transform="rotate(90)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.75"><animateTransform attributeName="transform" type="scale" begin="-1.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.25s"></animate></circle></g></g><g transform="translate(30.201010126776673,69.79898987322333)"><g transform="rotate(135)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.625"><animateTransform attributeName="transform" type="scale" begin="-1s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1s"></animate></circle></g></g><g transform="translate(22,50)"><g transform="rotate(180)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.5"><animateTransform attributeName="transform" type="scale" begin="-0.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.75s"></animate></circle></g></g><g transform="translate(30.201010126776666,30.201010126776673)"><g transform="rotate(225)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.375"><animateTransform attributeName="transform" type="scale" begin="-0.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.5s"></animate></circle></g></g><g transform="translate(49.99999999999999,22)"><g transform="rotate(270)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.25"><animateTransform attributeName="transform" type="scale" begin="-0.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.25s"></animate></circle></g></g><g transform="translate(69.79898987322332,30.201010126776666)"><g transform="rotate(315)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.125"><animateTransform attributeName="transform" type="scale" begin="0s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="0s"></animate></circle></g></g></svg></div></div></div>';
var exampleForm = document.querySelector('form');
var exampleLoader = new LoadingSpinner(exampleForm, loadingSpinnerHTML);

function stop_loader(){
    $("#loading-mask").remove();	
    $("body").find("a").unbind("click");
}

function show_loader(){
    var spinnerHTML = '<div id="loading-mask" class="container-fluid"><div class="row" style="height:100%;"><div id="spinner" class="col d-flex align-items-center justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;display:block;" width="60px" height="60px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" alt="spinner loading icon" class="img-fluid my-auto"><g transform="translate(78,50)"><g transform="rotate(0)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="1"><animateTransform attributeName="transform" type="scale" begin="-1.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.75s"></animate></circle></g></g><g transform="translate(69.79898987322333,69.79898987322332)"><g transform="rotate(45)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.875"><animateTransform attributeName="transform" type="scale" begin="-1.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.5s"></animate></circle></g></g><g transform="translate(50,78)"><g transform="rotate(90)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.75"><animateTransform attributeName="transform" type="scale" begin="-1.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1.25s"></animate></circle></g></g><g transform="translate(30.201010126776673,69.79898987322333)"><g transform="rotate(135)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.625"><animateTransform attributeName="transform" type="scale" begin="-1s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-1s"></animate></circle></g></g><g transform="translate(22,50)"><g transform="rotate(180)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.5"><animateTransform attributeName="transform" type="scale" begin="-0.75s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.75s"></animate></circle></g></g><g transform="translate(30.201010126776666,30.201010126776673)"><g transform="rotate(225)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.375"><animateTransform attributeName="transform" type="scale" begin="-0.5s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.5s"></animate></circle></g></g><g transform="translate(49.99999999999999,22)"><g transform="rotate(270)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.25"><animateTransform attributeName="transform" type="scale" begin="-0.25s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="-0.25s"></animate></circle></g></g><g transform="translate(69.79898987322332,30.201010126776666)"><g transform="rotate(315)"><circle cx="0" cy="0" r="6" fill="#522994" fill-opacity="0.125"><animateTransform attributeName="transform" type="scale" begin="0s" values="1.6600000000000001 1.6600000000000001;1 1" keyTimes="0;1" dur="2s" repeatCount="indefinite"></animateTransform><animate attributeName="fill-opacity" keyTimes="0;1" dur="2s" repeatCount="indefinite" values="1;0" begin="0s"></animate></circle></g></g></svg></div></div></div>';
       var spinner = document.createElement('div');
       spinner.innerHTML = spinnerHTML;
       spinner = spinner.firstChild;
       document.body.appendChild(spinner);
	  $("body").find("a").click(function (e) { e.preventDefault(); });
   }
</script>

<footer class="footer position-fixed">
  <div class="row g-0 justify-content-around align-items-center h-100">
    <div class="col-12 col-sm-auto text-center">
      <p class="mb-0 mt-2 mt-sm-0 text-900"><br class="d-sm-none" />Copyright &copy; 2023 aicountly | All rights reserved</p>
      <p><?= company()->fy_name.', ' ?> <?= company()->bo_name ?></p>
    </div>
    <div class="col-12 col-sm-auto text-center">
      <p class="mb-0 text-600"><a href="#!" class="text-500">Terms & Conditions</a></p>
    </div>
  </div>
</footer>

          
</div>
</div>
      
<div id="chat-container" class="support-chat-container">
  <div class="container-fluid support-chat">
    <div class="card bg-white">
      <div class="card-header d-flex flex-between-center px-4 py-2 border-bottom">
        <h5 class="mb-0 d-flex align-items-center gap-2"> <img class="rounded-circle " src="<?php echo base_url();?>public/assets/img/user.jpg" alt=""  style="width: 45px; height: 100%;">
        Name of the person</h5>
        <div class="btn-reveal-trigger">
          <a href="#!" class="link-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">forum</span></a>
          <ul class="dropdown-menu dropdown-end dropdown-md hoverlist">
            <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> Person Name <span class="material-symbols-outlined hovermenu float-end">close</span></a></li>
            <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> My Name goes here <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
            <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> New User name <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
          <li class="dropdown-item  border-top"> Add Participant</a>
          <input type="text" id="namelist" list="namelist" class="form-control">
        </li>
        <li class="dropdown-item  border-top">Start a New Chat</a></li>
      </ul>
      
      <a href="#!" class="link-secondary position-relative px-2" data-bs-toggle="dropdown" aria-expanded="false">
        <small class="position-absolute p-1 badge rounded-pill bg-danger" style="font-size:60%; right:-3px; top:-9px;">9+ </small><span class="material-symbols-outlined">group</span></a>
        <ul class="dropdown-menu dropdown-end dropdown-md hoverlist">
          <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> Person Name <span class="material-symbols-outlined hovermenu float-end">close</span></a></li>
          <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> My Name goes here <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
          <li><a class="dropdown-item p-1" href="#"> <img class="rounded-circle " src="<?php echo base_url();?>public/assets/img/user.jpg" alt=""  style="width:25px; height: 100%;"> New User name <span class="material-symbols-outlined hovermenu float-end">close</span></a></a></li>
        <li class="dropdown-item  border-top"> Add Participant</a>
        <input type="text" id="namelist" list="namelist" class="form-control">
      </li>
    </ul>
    
    <a href="#!" class="link-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">apps</span></a>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item p-2" href="#">Clear Chat</a></li><li><a class="dropdown-item p-2" href="#">Download Chat</a></li>
    </ul>
    <a href="#!" class="link-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">search</span></a>
    <div class="dropdown-menu p-0">
      <input type="text" class="form-control">
    </div>
    
    
    <a href="<?php echo base_url();?>chat" class="link-secondary px-2"><span class="material-symbols-outlined">screenshot_monitor</span></a>
    <a href="#!" class="link-secondary pin-chatbtn"><span class="material-symbols-outlined">push_pin</span></a>
  </div>
</div>
<div class="card-body chat p-0">
  <section class="p-0">
    <div class="row m-0">
      <div class="col-md-6 col-lg-5 col-xl-4 my-3">
        <div class="input-group mb-3">
          <input type="search" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-addon" />
          <span class="input-group-text border-0" id="search-addon"><span class="material-symbols-outlined">search</span></span>
        </div>
        <div data-mdb-perfect-scrollbar="true" style="position: relative; height:330px">
          <ul class="list-unstyled chatlist mb-0">
            <li class="p-2 border-bottom">
              <div class="d-flex align-items-center justify-content-between position-relative">
                <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="<?php echo base_url();?>public/assets/img/30.webp" alt=""></div>
                <div class="flex-1 me-sm-3">
                  <h5 class="text-black">Jessie Samson</h5>
                  <p class="text-800 fs--2 mb-0">Just 1 hour </p>
                </div>
                <span class="badge bg-danger">10</span>
                <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
                <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                  <a class="dropdown-item p-1" href="#!">Mark as Unread</a><a class="dropdown-item p-1" href="#!">Pin</a><a class="dropdown-item p-1" href="#!">Show history</a><a class="dropdown-item p-1" href="#!">Report to Admin</a>
                  <a class="dropdown-item p-1" href="#!">Block & Report</a><a class="dropdown-item p-1" href="#!">Delete Conversation</a></div>
                  
              </div>
            </li>
              
            <li class="p-2 border-bottom">
              <div class="d-flex align-items-center justify-content-between position-relative">
              <div class="avatar avatar-m status-away me-3"><img class="rounded-circle" src="<?php echo base_url();?>public/assets/img/30.webp" alt=""></div>
              <div class="flex-1 me-sm-3">
                <h5 class="text-black">Harman Singh Chadda</h5>
                <p class="text-800 fs--2 mb-0">2 Days</p>
              </div>
              <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
              <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                <a class="dropdown-item p-1" href="#!">Mark as Unread</a><a class="dropdown-item p-1" href="#!">Pin</a><a class="dropdown-item p-1" href="#!">Show history</a><a class="dropdown-item p-1" href="#!">Report to Admin</a>
                <a class="dropdown-item p-1" href="#!">Block & Report</a> <a class="dropdown-item p-1" href="#!">Delete Conversation</a></div>
                
              </div>
            </li>
                  
            <li class="p-2 border-bottom">
              <div class="d-flex align-items-center justify-content-between position-relative">
                <div class="avatar avatar-m status-offline me-3"><img class="rounded-circle" src="<?php echo base_url();?>public/assets/img/30.webp" alt=""></div>
                <div class="flex-1 me-sm-3">
                  <h5 class="text-black">Richa Kalia</h5>
                  <p class="text-800 fs--2 mb-0">August 7,2021 / 10:41 AM</p>
                </div>
                <a class="chatmenu p-0 dropdown" data-bs-toggle="dropdown"><span class="material-symbols-outlined">more_vert</a>
                <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                  <a class="dropdown-item p-1" href="#!">Mark as Unread</a>
                  <a class="dropdown-item p-1" href="#!">Pin</a>
                  <a class="dropdown-item p-1" href="#!">Show history</a>
                  <a class="dropdown-item p-1" href="#!">Report to Admin</a>
                  <a class="dropdown-item p-1" href="#!">Block & Report</a> 
                  <a class="dropdown-item p-1" href="#!">Delete Conversation</a>
                </div>
                        
              </div>
            </li>
          </ul>
      </div>
    </div>
    <div class="col-md-6 col-lg-7 col-xl-8">
      <div class="d-flex flex-column-reverse scrollbar h-100 p-3">
        <div class="card-body" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400px; overflow-y:scroll">
          <div class="d-flex flex-row justify-content-start">
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp" alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Hi
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-light">How are you ...???
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-light">What are you doing tomorrow? Can we come up a bar?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">23:58</p>
            </div>
          </div>
          <div class="divider d-flex align-items-center mb-4">
            <p class="text-center mx-3 mb-0">Today</p>
          </div>
          <div class="d-flex flex-row justify-content-end mb-4 pt-1">
            <div class="msg_send">
              <div class="msg bg-success">Hiii, I'm good.
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">How are you doing?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">Long time no see! Tomorrow   office. will be free on sunday.
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              
              
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:06</p>
            </div>
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          <div class="d-flex flex-row justify-content-start mb-4">
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Okay
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-light">We will go on  Sunday?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">00:07</p>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-end mb-4">
            <div class="msg_send">
              <div class="msg bg-success">That's awesome!
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">I will meet you Sandon Square  sharp at 10 AM
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <div class="msg bg-success">Is that okay?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:09</p>
            </div>
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          <div class="d-flex flex-row justify-content-start mb-4">
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Okay i will meet you on  Sandon Square
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">00:11</p>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-end mb-4">
            <div class="msg_send">
              <div class="msg bg-success">Do you have pictures of Matley Marriage?
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:11</p>
            </div>
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          <div class="d-flex flex-row justify-content-start mb-4">
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
            <div class="msg_receive">
              <div class="msg bg-light">Sorry I don't have. i changed my phone.
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all">done</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small ms-3 mb-3 rounded-3 text-muted">00:13</p>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-end">
            <div class="msg_send">
              <div class="msg bg-success">Okay then see you on sunday!!
                <input type="checkbox" class="form-check-input">
                <span class="chatoptions material-symbols-outlined" data-bs-toggle="dropdown" aria-expanded="false">expand_more</span>
                <span class="material-symbols-outlined done_all text-primary">done_all</span>
                <ul class="dropdown-menu">
                  <li><a href="#"><span class="material-symbols-outlined">edit_note</span>Edit</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">delete</span> Delete</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">star</span> Favourite</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">file_copy</span> Copy</a></li>
                  <li><a href="#"><span class="material-symbols-outlined">forward_to_inbox</span> Forward</a></li>
                </ul>
              </div>
              <p class="small me-3 mb-3 rounded-3 text-muted d-flex justify-content-end">00:15</p>
            </div>
            <img src="<?php echo base_url();?>public/assets/img/ava3-bg.webp"
            alt="avatar 1" style="width: 45px; height: 100%;">
          </div>
          
        </div>
      </div>
    </div>
  </section>
</div>
    <div class="card-footer d-flex align-items-center gap-2 border-top ps-3 pe-4 py-3">
      <div class="d-flex align-items-center flex-1 gap-3 border rounded-pill px-4">
        <input class="form-control outline-none border-0 flex-1 fs--1 px-0" type="text" placeholder="Write message" />
        <input type="file"  accept="image/*" name="image" id="file"  onchange="loadFile(event)" style="display: none;">
        <label for="file" style="cursor: pointer;"><i id="chatfile" class="material-symbols-outlined" />attachment</i></label>
        </div><button class="btn p-0 border-0 send-btn"><span class="material-symbols-outlined">send</span></button>
      </div>
    </div>
  </div>
  <button id="chat-btn" class="btn p-0 border border-200 btn-support-chat"><span class="fs-0 btn-text ">Chat</span><span class="fa-solid fa-circle text-success fs--1 ms-2"></span><span class="material-symbols-outlined text-primary fs-1">forum</span></button>
</div>

</main>
    
    
   <div class="rihgtsticky">
       <a href="<?php base_url() ?>/admin/office_tools" class="position-relative">
  <span class="material-symbols-outlined">calendar_month</span>
  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
    99+
  </span>
  </a>
  <a href="#" data-bs-toggle="modal" data-bs-target="#notesModal"><span class="material-symbols-outlined">sticky_note_2</span></a>
  <a href="#" data-bs-toggle="modal" data-bs-target="#calcModal"><span class="material-symbols-outlined">calculate</span></a>
   </div> 
   
   
<!-- Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!--<div class="modal-header">-->
      <!--  <h3 class="" id="notesModalLabel">Sticky Notes</h3>-->
      <!--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">-->
      <!--  </button>-->
      <!--</div>-->
      <div class="modal-header d-flex justify-content-between align-items-center flex-wrap">
          <h3 class="mb-0" id="notesModalLabel">Sticky Notes</h3>
        
          <div class="d-flex align-items-center gap-3">
            <!-- Radio Buttons -->
            <div class="form-check form-check-inline mb-0">
              <input class="form-check-input" type="radio" name="note_type" id="companyNote" value="company" checked>
              <label class="form-check-label" for="personalNote">Company Notes</label>
            </div>
            <div class="form-check form-check-inline mb-0">
              <input class="form-check-input" type="radio" name="note_type" id="myNote" value="my">
              <label class="form-check-label" for="teamNote">My Notes</label>
            </div>
        
            <!-- Close Button -->
            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>

      <div class="modal-body d-md-flex">
        <div class="col-md-4 col-lg-3 nav flex-column notespill me-3" id="v-notes-tab" role="tablist" aria-orientation="vertical">

        <div id="stickyNoteContainer"></div>

            
            <!-- Show More Button -->
            <div class="mt-2">
              <button id="showMoreNotes" class="btn btn-sm btn-secondary">Show More</button>
            </div>


        </div>
        <div class="col-md-8 col-lg-9 px-2 tab-content" id="v-notes-tabContent">
          <?php foreach (sticky_notes() as $key => $value) { ?>

            <div class="tab-pane fade <?= ($key == 0) ? 'show active' : '' ?>" id="v-note<?= $key+1 ?>" role="tabpanel" aria-labelledby="v-note<?= $key+1 ?>-tab" tabindex="<?= $key ?>" data-note-master-id="<?= esc($value['primary']) ?>">
              <div class="note-it" style="background:<?= $value['color'] ?>">
        

                <div class="note-heading" contenteditable="true" style="outline: 0px solid transparent;" data-maxlength="255">
                  <?= esc($value['heading']) ?>
              </div>

              <div class="note-id" contenteditable="true" style="outline: 0px solid transparent; display: none;">
                  <?= esc($value['primary']) ?>
              </div>

                <div class=""  style="outline: 0px solid transparent;">
                <div class="note-details" contenteditable="true" data-maxlength="10000">
                      <?= esc($value['heading']) ?>
                    </div>

                  <br>
                  <br>
                  
                  <span style="color: lightgreen">Great</span>.
                </div>
              </div>
              <div class="taskmenus"  style="display: flex;justify-content: flex-end;">
                    <a href="#" class="save-note">
                      <span class="material-symbols-outlined">task_alt</span>
                    </a>


                </a>
              </div>
            </div>

          <?php } ?>

        </div>
      </div>
    </div>
  </div>
</div>
  
<!-- Modal -->
<div class="modal fade" id="calcModal" tabindex="-1" aria-labelledby="calcModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="calculator" align="center">
        <div class="displayBox">
          <p class="displayText borderdark rounded shadow p-2" id="display">0</p>
        </div>
        <div class="row numberPad">
          <div class="col-md-9 numbers">
            <div class="d-flex">
              <button class="btn btn-hlight clear hvr-back-pulse" id="clear">C</button>
              <button class="btn btn-light btn-calc hvr-radial-out" id="sqrt">√</button>
              <button class="btn btn-light btn-calc hvr-radial-out hvr-radial-out" id="square">x<sup>2</sup></button>
            </div>
            <div class="d-flex">
              <button class="btn btn-light btn-calc hvr-radial-out" id="seven">7</button>
              <button class="btn btn-light btn-calc hvr-radial-out" id="eight">8</button>
              <button class="btn btn-light btn-calc hvr-radial-out" id="nine">9</button>
            </div>
            <div class="d-flex">
              <button class="btn btn-light btn-calc hvr-radial-out" id="four">4</button>
              <button class="btn btn-light btn-calc hvr-radial-out" id="five">5</button>
              <button class="btn btn-light btn-calc hvr-radial-out" id="six">6</button>
            </div>
            <div class="d-flex">
              <button class="btn btn-light btn-calc hvr-radial-out" id="one">1</button>
              <button class="btn btn-light btn-calc hvr-radial-out" id="two">2</button>
              <button class="btn btn-light btn-calc hvr-radial-out" id="three">3</button>
            </div>
            <div class="d-flex">
              <button class="btn btn-qlight btn-calc hvr-radial-out" id="plus_minus">&#177;</button>
              <button class="btn btn-qlight btn-calc hvr-radial-out" id="zero">0</button>
              <button class="btn btn-qlight btn-calc hvr-radial-out" id="decimal">.</button>
            </div>
          </div>
          <div class="col-md-3 ps-md-0 operationSide">
            <button id="divide" class="btn btn-qlight btn-operation hvr-fade">÷</button>
            <button id="multiply" class="btn btn-qlight btn-operation hvr-fade">×</button>
            <button id="subtract" class="btn btn-qlight btn-operation hvr-fade">−</button>
            <button id="add" class="btn btn-qlight btn-operation hvr-fade">+</button>
            <button id="equals" class="btn btn-success btn-operation equals hvr-back-pulse">=</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- The Modal -->




<div class="modal fade" id="FYModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <p class="overall_status"></p>
      
		<!-- Query Progress Status (top) -->
		<div class="query_status text-center mb-1" style="height:20px">Processing queries...</div>
		<div class="progress mb-3" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated query-progress" role="progressbar" style="width: 0%;">0%</div>
		</div>

		<!-- Module Progress Status (middle) -->
		<div class="module_status text-center mb-1" style="height:20px">Module 1 of 18: create_fy</div>
		<div class="progress mb-2" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated module-progress" role="progressbar" style="width: 0%;">0%</div>
		</div>

      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
	      <button type="button" class="btn btn-success" id="refreshbtndfy" style="display:none;">Refresh</button>&nbsp;
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<!-- The Modal -->
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


<div class="siteloader" style="display:none;">
  <div class="loader">
    <div class="progress w-100" id="progressbar" role="progressbar" aria-label="Loader Bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height: 18px">
      <div class="progress-bar progress-bar-striped bg-success progress-bar-animated"></div>
    </div>
  </div>
</div>

<!------ User photo      ---->
<!-- The Modal -->
<div class="modal" id="userphotoModal2">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">PHOTO</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

        <div class="row p-2">
          <div class="col-md-12 text-center" id="userphotopreview">
            <img id="userlogo2" src="<?= base_url().'/user/logo' ?>" alt="No Logo"  onerror="this.onerror=null;this.src='<?= base_url() ?>public/assets/img/user.jpg';" style="width: 250px; height: 250px;">
          </div>
        </div>

        <div id="userlogo_file_validation2"></div>

        

        <form id="userlogo_form2" action="<?= base_url() ?>user/upload_file" method="post" enctype="multipart/form-data">
          <input type="hidden" name="user_id" value="<?= auth()->id ?>">
          <input type="file" id="user_logo_file" name="user_logo_file" onChange="UserfilePreview(this)" class="form-control" accept="image/png, image/jpeg" required>
          
        </form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button form="userlogo_form2" id="user_logo_form_btn2" class="btn btn-sm btn-success" type="submit">Upload</button>
      </div>

    </div>
  </div>
</div>


    
    
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="<?php echo base_url();?>public/assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url();?>public/assets/js/popper.min.js"></script>
    <script src="<?php echo base_url();?>public/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>public/assets/js/aicountly.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="<?php echo base_url();?>public/assets/js/selectwidget.js"></script>
    <script src="<?php echo base_url();?>public/assets/js/calculate.js"></script>    
    <?php
		if(isset($below_js)){
		  foreach($below_js as $below_jsfiles){
		 ?>
		<script type="text/javascript" src="<?php echo base_url();?>public/<?php echo $below_jsfiles;?>"></script>
		<?php
		   }
		}
	?>
	
		
	<script src="
<?php echo base_url();?>public/assets/alert/sweetalert2.all.min.js
"></script>
<link href="<?php echo base_url();?>public/assets/alert/sweetalert2.min.css
" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>public/assets/js/tour.js"></script>
 
 <!--<link href="<?php echo base_url();?>public/assets/css/tempus-dominus.min.css" rel="stylesheet"/>
<script src="<?php echo base_url();?>public/assets/js/luxon.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>public/assets/js/tempus-dominus.min.js"></script>
-->
<script>
/* tempusDominus.DefaultOptions.localization.format = 'dd-MM-yyyy';
const pickerOpts = {
	useCurrent: false,
	
    display: {
      viewMode: 'calendar',
      components: {
        calendar: true,
        date:     true,
        month:    true,
        year:     true,
        decades:  true,
        clock:    false
      },
      buttons: { today: true, close: true }	  
    },
	localization: {
	  locale: 'en-GB',
	  format: 'dd-MM-yyyy'	
	  }    
  };
['sale_date','voucher_date', 'from_date', 'to_date', 'fromdate', 'todate'].forEach(id => {
  const el = document.getElementById(id);
  if (el) {
	  
   const picker = new tempusDominus.TempusDominus(el, pickerOpts);

   
  }
}); */

 $(document).ready(function () {
    $('#showMoreNotes').on('click', function () {
      $('.extra-note').removeClass('d-none');
      $(this).hide(); // Hide the Show More button after expanding
    });
  });

$(document).ready(function () {
    const $noteDetails = $('.note-details');
    const maxLength = parseInt($noteDetails.data('maxlength')) || 10000;

    $noteDetails.on('input', function () {
      let text = $(this).text();

      if (text.length > maxLength) {
        $(this).text(text.substring(0, maxLength));

        // Move cursor to the end
        const el = this;
        const range = document.createRange();
        const sel = window.getSelection();
        range.selectNodeContents(el);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      }
    });
  });

$(document).ready(function () {
    const $editable = $('.note-heading');
    const maxLength = parseInt($editable.data('maxlength')) || 255;

    $editable.on('input', function () {
      let text = $(this).text();

      if (text.length > maxLength) {
        $(this).text(text.substring(0, maxLength));

        // Move cursor to end
        const el = this;
        const range = document.createRange();
        const sel = window.getSelection();
        range.selectNodeContents(el);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      }
    });
  });



// Wait until DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  const dropdown = document.querySelector('.helpdropdown .dropdown-menu');

  // Use MutationObserver to watch for style changes
  const observer = new MutationObserver(() => {
    dropdown.style.transform = 'translate3d(-16px, 46.2px, 0px)';
  });

  observer.observe(dropdown, { attributes: true, attributeFilter: ['style'] });
});





  function forceFixPickerHeader() {
    const observer = new MutationObserver(() => {
      document.querySelectorAll('.picker-switch').forEach(header => {
        const titleAttr = header.getAttribute('title'); // should say "Select Month"
        const text = header.textContent.trim();         // e.g. "February 26"

        if (titleAttr?.toLowerCase().includes('month')) {
          const parts = text.split(" ");
          if (parts.length === 2 && parts[1].length === 2) {
            const fullYear = "20" + parts[1]; // Convert 26 → 2026
            const newHeader = `${parts[0]} ${fullYear}`;
            header.textContent = newHeader;

          }
        }
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }

  forceFixPickerHeader();
function showErrorAlert(message) {
  Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: message,
    confirmButtonText: 'OK'
  });
}



const $input = $('#commandInput');

if ($input.length > 0) {
  const inputOffset = $input.offset();
  const inputHeight = $input.outerHeight();
  const inputWidth  = $input.outerWidth();

  $('#suggestionsDropdown').css({
    'top': inputOffset.top + inputHeight + 'px',
    'left': '33px',
    'width': inputWidth + 'px'
  });
} else {
  console.warn('#commandInput not found on the page.');
}

const commandsData = <?php echo json_encode(array_values($commands)); ?>;

//   console.log(commandsData);


const baseUrl = <?php echo json_encode(base_url()) ?>;

let selectedChildId = null; 
let dropdownSelectedByEnter = false;

// console.log(dropdownSelectedByEnter);



function toTitleCase(str) {
  return str
    .toLowerCase()
    .split(/[\s_-]+/)
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}




$(document).ready(function() {
    // commandsData is already loaded from PHP

    // Function to render dropdown suggestions
    function renderDropdown(results) {

      // console.log(results);
      
       results.sort((a, b) => Number(b.priority) - Number(a.priority));

        if (results.length > 0) {
            let dropdownHtml = '';
            results.forEach(function(cmd) {
              dropdownHtml += `<li class="suggestion-item p-2" style="cursor:pointer; font-size:13px;" data-cmd="${cmd.cmd}" 
              onclick="${cmd.url ? `window.location.href='${baseUrl}${cmd.url}'` : `showErrorAlert('URL NOT DEFINED. PLS CONTACT SUPPORT.')`}">
              <strong>${cmd.cmd}</strong> - <i>${toTitleCase(cmd.name)}</i>${cmd.alias ? ` <i>(${toTitleCase(cmd.alias)})</i>` : ''}

            </li>`;
            });
            $('#suggestionsDropdown').html(dropdownHtml).css('display', 'block');
        } else {
            $('#suggestionsDropdown').html('<li class="text-muted p-2">No results found</li>').show();
        }
    }

    // On input field click → Show full list
    $('#commandInput').on('focus', function() {
      selectedIndex = -1;
        if (commandsData.length > 0) {
            renderDropdown(commandsData);
        }
    });

    let selectedIndex = -1; // Tracks the highlighted index

$('#commandInput').on('keydown', function(e) {
    const items = $('#suggestionsDropdown li.suggestion-item');
    const total = items.length;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (selectedIndex < total - 1) {
            selectedIndex++;
            items.removeClass('active');
            $(items[selectedIndex]).addClass('active');
            $('#commandInput').val($(items[selectedIndex]).data('cmd'));
        }
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (selectedIndex > 0) {
            selectedIndex--;
            items.removeClass('active');
            $(items[selectedIndex]).addClass('active');
            $('#commandInput').val($(items[selectedIndex]).data('cmd'));
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedIndex >= 0 && selectedIndex < total) {
          dropdownSelectedByEnter = true;
            $(items[selectedIndex]).trigger('click');

            setTimeout(() => {
            dropdownSelectedByEnter = false;
          }, 100);

        }
    }
});

$('#commandInput').on('input', function () {
    selectedIndex = -1;
    let input = $(this).val().toLowerCase().trim();
    searchTriggerPending = false;

    if (!input) return;

    const words = input.split(/\s+/);
    const endsWithSpace = /\s$/.test(input);

    const parentKeyword = words[0] || '';
    const searchKeyword = words[1] || '';
    const remaining = words.slice(1).join(" ").trim();

    const keywordRegex = new RegExp(`\\b${searchKeyword}\\b`, 'i');
    const parentRegex = new RegExp(`\\b${parentKeyword}\\b`, 'i');

    let exactMatches = [];
    let beginsWithMatches = [];
    let containsMatches = [];
if (words.length < 3 && commandsData.length > 0) {
  commandsData.forEach(function (cmd) {
    const cmdText = cmd.cmd.toLowerCase();
    const nameText = cmd.name.toLowerCase();
    const aliasText = cmd.alias.toLowerCase();
    const parentNameText = (cmd.parent_name || '').toLowerCase();

    const matchesParent = parentNameText === parentKeyword || parentNameText.includes(parentKeyword);

    // 1. Only 1 word typed (no space yet): Treat as child command input
    if (words.length === 1 && !endsWithSpace) {
        if (cmdText === parentKeyword || nameText === parentKeyword || aliasText === parentKeyword) {
            exactMatches.push(cmd);
        } else if (cmdText.startsWith(parentKeyword) || nameText.startsWith(parentKeyword) || aliasText.startsWith(parentKeyword)) {
            beginsWithMatches.push(cmd);
        } else if (parentRegex.test(cmdText) || parentRegex.test(nameText) || parentRegex.test(aliasText)) {
            containsMatches.push(cmd);
        }
    }

    // ✅ 2. One word + space → Show all children of that parent
else if (words.length === 1 && endsWithSpace) {
    if (
        matchesParent &&
        cmd.parent_name &&
        cmd.parent_name.toLowerCase() === parentKeyword &&
        cmd.cmd.toLowerCase() !== parentKeyword // ⛔ exclude the parent itself
    ) {
        containsMatches.push(cmd);
    }
}


    // 3. Two words: filter child commands of given parent
    else if (words.length === 2 && matchesParent) {
        if (cmdText === searchKeyword || nameText === searchKeyword || aliasText === searchKeyword) {
            exactMatches.push(cmd);
        } else if (cmdText.startsWith(searchKeyword) || nameText.startsWith(searchKeyword) || aliasText.startsWith(searchKeyword)) {
            beginsWithMatches.push(cmd);
        } else if (keywordRegex.test(cmdText) || keywordRegex.test(nameText) || keywordRegex.test(aliasText)) {
            containsMatches.push(cmd);
        }
    }
});


    const prioritizedResults = exactMatches.concat(beginsWithMatches, containsMatches);
    
    // Preserve rest of your original logic
    if (prioritizedResults.length > 0) {
        selectedChildId = prioritizedResults[0].crs_name || prioritizedResults[0].cmd_id || null;
        renderDropdown(prioritizedResults);
    } else {
        $('#suggestionsDropdown').hide();
    }

    
}

});



// Reusable CRS Fetcher
function fetchCRSResults(keyword, childId) {
    $.ajax({
        url: "<?= base_url('home/check_crs') ?>",
        type: "GET",
        dataType: "json",
        data: {
            keyword: keyword,
            child: childId
        },
        success: function (response) {

           if (!Array.isArray(response) || response.length === 0) {
        $('#crsDropdown').html(`
            <div class="list-group-item text-danger fw-bold">
                INCORRECT COMMAND LINE SEARCH
            </div>
        `);
        return;
    }

            let list = '';
            response.forEach(item => {
             
              // console.log(accId);
              
                list += `
                    <button type="button" class="list-group-item list-group-item-action"
                            data-id="${item.acc_id}" data-label="${item.acc_name}">
                        ${item.acc_name}
                    </button>`;
            });

            $('#crsDropdown').html(list);

            $('#crsDropdown .list-group-item').on('click', function () {
                $('#crsInput').val();
               const accId = $(this).data('id');
                $('#crsDropdown').hide();

                const today = new Date();

                // End date = today
                const endDate = today.toISOString().split('T')[0];  // format: YYYY-MM-DD

                // Start date = first day of the month two months ago
                const start = new Date(today.getFullYear(), today.getMonth() - 2, 1);
                const startDate = start.toISOString().split('T')[0];

                window.location.href = `<?= base_url(); ?>/admin/accounts/ledger_detail/${accId}?from_date=${startDate}&to_date=${endDate}`;
            });
        },
        error: function () {
            $('#crsDropdown').html('<div class="list-group-item text-danger">CRS search failed</div>');
        }
    });
}

// ✅ Separate keydown listener for Enter on 3rd word (CRS mode)
$('#commandInput').off('keydown.crsEnter').on('keydown.crsEnter', function (e) {
    const input = $(this).val().toLowerCase().trim();
    const words = input.split(/\s+/);
   const crsKeyword = words.length > 2 ? words.slice(1).join(" ").trim() : (words[1] || '');


    if (words.length >= 3 && e.key === "Enter") {
        e.preventDefault();

        if (crsKeyword.length < 3) {
            // ❌ Show error in your #searchModal
            $('#modalTitle').text('Invalid CRS Keyword');
            $('#modalBody').html("CRS Type must be at least <strong>3 characters</strong>.");
            $('#searchModal').modal('show');
            return;
        }

        // ✅ Show loading in the modal
       $('#modalTitle').text('COMMAND LINE MASTER SEARCH');
        $('#modalBody').html(`
          <div class="position-relative">
            <label for="crsInput" class="form-label mb-2">CRS Keyword</label>
            <input type="text" id="crsInput" class="form-control text-start" autocomplete="off" />
            <div id="crsDropdown" class="list-group position-absolute w-100 z-3 mt-1" style="max-height: 200px; overflow-y: auto; display: none;"></div>
          </div>
        `);
        $('#crsInput').val(crsKeyword);
        $('#searchModal').modal('show');

        $('#crsInput').off('input').on('input', function () {
          const typedKeyword = $(this).val().trim();
          if (typedKeyword.length >= 2) {
            fetchCRSResults(typedKeyword, selectedChildId);
          } else {
            $('#crsDropdown').html('<div class="list-group-item text-muted">Type at least 2 characters</div>');
          }
        });


        const searchKeyword = words.length > 1 ? words[1] : '';

        
    fetchCRSResults(crsKeyword, selectedChildId);

   


    }
});


$('#commandInput').on('keydown', function (e) {
    if (e.key !== 'Enter') return;

    e.preventDefault();

    const input = $(this).val().toLowerCase().trim();
    const words = input.split(/\s+/);

    const parentKeyword = words[0] || '';
    const possibleChild = words[1] || '';
    const remainingWords = words.slice(2).join(' ').trim();

    let isChildValid = false;

    // ✅ Check if second word is valid child of parent
    if (commandsData.length > 0) {
        commandsData.forEach(cmd => {
            const parentName = (cmd.parent_name || '').toLowerCase();
            const cmdText = cmd.cmd.toLowerCase();
            const nameText = cmd.name.toLowerCase();
            const aliasText = cmd.alias.toLowerCase();

            const parentMatch = parentName === parentKeyword || parentName.includes(parentKeyword);
            const isMatch = cmdText === possibleChild || nameText === possibleChild || aliasText === possibleChild;

            if (parentMatch && isMatch) {
                isChildValid = true;
            }
        });
    }

    const selectedChild = selectedChildId;
    const crsKeyword = isChildValid
        ? remainingWords
        : words.slice(1).join(' ').trim(); // if no child match, second word is CRS too

    // 🔐 Validate CRS length
    if (crsKeyword.length < 3 && !dropdownSelectedByEnter) {
        $('#modalTitle').text('Invalid CRS Keyword');
        $('#modalBody').html("CRS Type must be at least <strong>3 characters</strong>.");
        $('#searchModal').modal('show');
        return;
    }

    if(!dropdownSelectedByEnter){
  $('#modalTitle').text('COMMAND LINE MASTER SEARCH');
    $('#modalBody').html(`
        <label for="crsInput" class="form-label mb-2">CRS Keyword</label>
        <input type="text" id="crsInput" class="form-control text-center mb-3" value="${crsKeyword}" />
        <div id="crsDropdown" class="list-group"></div>
    `);
    $('#searchModal').modal('show');
    }
  

    // 🔁 Fetch results
    fetchCRSResults(crsKeyword, selectedChild);

    // ✍️ Listen on typing inside modal
    $(document).off('input', '#crsInput').on('input', '#crsInput', function () {
        const val = $(this).val().trim();
        if (val.length >= 3) {
            fetchCRSResults(val, selectedChild);
        } else {
            $('#crsDropdown').html('<div class="list-group-item text-muted">Enter at least 3 characters</div>');
        }
    });
});


function showErrorModal(message) {
    $('#modalTitle').text('Search Error');
    $('#modalBody').html(`<p class="text-danger">${message}</p>`);
    $('#searchModal').modal('show');
}



function searchCrsValueInModal(searchKeyword) {
    let exact = [];
    let begins = [];
    let contains = [];

    commandsData.forEach(cmd => {
        const crsText = (cmd.crs_name || '').toLowerCase();
        if (crsText === searchKeyword) {
            exact.push(cmd);
        } else if (crsText.startsWith(searchKeyword)) {
            begins.push(cmd);
        } else if (crsText.includes(searchKeyword)) {
            contains.push(cmd);
        }
    });

    const results = exact.concat(begins, contains);
    let resultHtml = `
        <div class="mb-3">
          <input type="text" class="form-control" id="crsSearchInput" value="${searchKeyword}" placeholder="Search CRS again...">
        </div>
        <ul class="list-group">
            ${results.map(r => `
                <li class="list-group-item">
                    <strong>${r.cmd}</strong> - ${r.name}<br>
                    <small>CRS: ${r.crs_name || '-'}</small>
                </li>
            `).join('')}
        </ul>
    `;

    $('#modalTitle').text('COMMAND LINE MASTER SEARCH');
    $('#modalBody').html(resultHtml);
    $('#searchModal').modal('show');

    // Optional: dynamic re-search inside modal
    $('#crsSearchInput').on('input', function () {
        searchCrsValueInModal($(this).val().toLowerCase());
    });
}


   
    $(document).on('click', '.suggestion-item', function() {
        const selectedCmd = $(this).data('cmd');
        $('#commandInput').val(selectedCmd);
        $('#suggestionsDropdown').hide();
    });

   
    $(document).click(function(e) {
        if (!$(e.target).closest('#commandInput, #suggestionsDropdown').length) {
            $('#suggestionsDropdown').hide();
        }
    });
});



  $(document).on('click', '.star-icon', function (e) {
    e.stopPropagation(); // prevent triggering parent <a>
    e.preventDefault();  // stop any href behavior

    const id = $(this).data('id');
    const compFyId = '<?= $local_session->get('ses_company_id') ?>';

   // console.log(compFyId);
    

    // Marking branch 
    $.ajax({
      url: '<?= base_url('admin/dashboard/markBranch') ?>',
      method: 'POST',
      data: {
      id: id,
      comp_id: compFyId
    },
      success: function (response) {
       // console.log('Star clicked!', response);
        // Optionally update UI or change star image

          // load_bolink(bo_id);

          location.reload();
          // $('.selbo_link').trigger('click');
      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
      }
    });
  });

  $(document).on('click', '.star-icon-fin', function (e) {
    e.stopPropagation(); // prevent triggering parent <a>
    e.preventDefault();  // stop any href behavior

    const id = $(this).data('id');
    const compFyId = '<?= $local_session->get('ses_company_id') ?>';

   // console.log(compFyId);
    

    // Marking Financial Year
    $.ajax({
      url: '<?= base_url('admin/dashboard/markFinancial') ?>',
      method: 'POST',
      data: {
      id: id,
      comp_id: compFyId
    },
      success: function (response) {
       // console.log('Star clicked!', response);
        // Optionally update UI or change star image

          // load_bolink(bo_id);
          location.reload();
          // $('.selfy_change').trigger('click');
      },
      error: function (xhr, status, error) {
        console.error('Error:', error);
      }
    });
  });


const allowedRegex = /^[a-zA-Z0-9\s]$/;
const sanitizeRegex = /[^a-zA-Z0-9\s]/g;
const maxChars = 5000;

function sanitizeContent(el) {
  let text = $(el).text().replace(sanitizeRegex, '');
  if (text.length > maxChars) {
    text = text.substring(0, maxChars);
    alert('Maximum 5000 characters allowed.');
  }
  $(el).text(text);
  placeCaretAtEnd(el);
}

function placeCaretAtEnd(el) {
  const range = document.createRange();
  const sel = window.getSelection();
  range.selectNodeContents(el);
  range.collapse(false);
  sel.removeAllRanges();
  sel.addRange(range);
}

// Prevent typing special characters
$(document).on('keydown', '.note-heading[contenteditable], .note-details[contenteditable]', function (e) {
  const key = e.key;
  const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Enter'];
  
  if (e.ctrlKey || e.metaKey || allowedKeys.includes(key)) return;
  
  if (!allowedRegex.test(key)) {
    e.preventDefault();
  }
});

// Clean anything that sneaks in (emoji, paste, IME, etc.)
$(document).on('input', '.note-heading[contenteditable], .note-details[contenteditable]', function () {
  sanitizeContent(this);
});

// Clean pasted content
$(document).on('paste', '.note-heading[contenteditable], .note-details[contenteditable]', function (e) {
  e.preventDefault();
  let text = (e.originalEvent || e).clipboardData.getData('text/plain');
  text = text.replace(sanitizeRegex, '').substring(0, maxChars);
  document.execCommand('insertText', false, text);
});



$(function () {
    $('#sortable-notes').sortable({
      axis: 'y', // vertical sorting
      containment: 'parent',
      update: function (event, ui) {
        let newOrder = [];
        $('#sortable-notes .note-it').each(function () {
          newOrder.push($(this).data('id'));
        });

     //   console.log('New order:', newOrder);

        // Optional: send order to backend
        // $.post('<?= base_url('admin/office_tools/saveNoteOrder') ?>', { order: newOrder }, function (res) {
        //   console.log('Order saved:', res);
        // });
      }
    });
  });


  let autoSaveTimer;
let countdownTimer;
let countdownValue = 10; // 30 seconds

$(document).ready(function () {
  
  // Add a countdown span inside each .taskmenus
  $('.taskmenus').each(function() {
    $(this).append('<span class="countdown-timer text-muted ms-2" style="font-size:12px;"></span>');
  });

  // Listen to typing in heading or details
  $('#v-notes-tabContent').on('input', '.tab-pane.active .note-heading, .tab-pane.active .note-details', function () {
      
      
    clearTimeout(autoSaveTimer);
    clearInterval(countdownTimer);

    const activePane = $('.tab-pane.active');
    const noteId = activePane.find('.note-id').text().trim();
    
    const countdownSpan = activePane.find('.countdown-timer');

    countdownValue = 10; // Reset timer to 30 seconds
    updateCountdownDisplay(countdownSpan);

    countdownTimer = setInterval(function () {
      countdownValue--;
      updateCountdownDisplay(countdownSpan);

      if (countdownValue <= 0) {
        clearInterval(countdownTimer);
      }
    }, 1000);

    autoSaveTimer = setTimeout(function () {
      const heading = activePane.find('.note-heading').text().trim();
      const details = activePane.find('.note-details').html().trim();
      const selectedNoteType = $('input[name="note_type"]:checked').val();

      $.ajax({
        url: '<?= base_url('admin/office_tools/saveNote') ?>',
        type: 'POST',
        dataType: 'json',
        data: {
          id: noteId,
          heading: heading,
          details: details,
          note_type: selectedNoteType
        },
        success: function (res) {
          countdownSpan.text('Saved ✅');
        },
        error: function (xhr, status, err) {
          countdownSpan.text('Save Failed ❌');
        }
      });
    }, 10000); // After 30 seconds
  });

});

// Function to update countdown timer display
function updateCountdownDisplay(spanElement) {
  if (countdownValue > 0) {
    spanElement.text(`Saving in ${countdownValue}s`);
  }
}

function fetchAndRenderNotes() {
 const selectedNoteType = $('input[name="note_type"]:checked').val();
 
 const noteColors = ['#DBFFD2', '#DBF3FB', '#FFF8D7', '#FBE2D2', '#FFEAED'];


  $.ajax({
    url: '<?= base_url('admin/office_tools/fetchNotes') ?>',
    type: 'POST',
    dataType: 'json',
    data: { note_type: selectedNoteType },
    success: function (res) {
  $('#stickyNoteContainer').empty();
  $('#v-notes-tabContent').empty();

  let visibleNoteCount = 0;

  if (Array.isArray(res) && res.length > 0) {
    // Sort notes by order
    res.sort(function (a, b) {
      const orderA = selectedNoteType === 'my' ? parseInt(a.aic_notes_order) : parseInt(a.erp_notes_order);
      const orderB = selectedNoteType === 'my' ? parseInt(b.aic_notes_order) : parseInt(b.erp_notes_order);
      return orderA - orderB;
    });

    res.forEach(function (note, index) {
      const heading = selectedNoteType === 'my' ? note.aic_notes_heading : note.erp_notes_heading;
      const details = selectedNoteType === 'my' ? note.aic_notes_details : note.erp_notes_details;
      const noteId = note.note_master_id;
      const color = note.color || noteColors[visibleNoteCount % noteColors.length];

      if ((heading && heading.trim()) || (details && details.trim())) {
        // Sidebar Note
        const noteHtml = `
          <a class="note-it v-note-it ${visibleNoteCount === 0 ? 'active' : ''} ${visibleNoteCount >= 5 ? 'd-none extra-note' : ''}"
             data-id="${noteId}"
             id="v-note${visibleNoteCount + 1}-tab"
             data-bs-toggle="pill"
             data-bs-target="#v-note${visibleNoteCount + 1}"
             role="tab"
             aria-controls="v-note${visibleNoteCount + 1}"
             aria-selected="true"
             style="background:${color}">
             ${heading}
          </a>`;
        $('#stickyNoteContainer').append(noteHtml);

        // Tab Pane
        const paneHtml = `
          <div class="tab-pane fade ${visibleNoteCount === 0 ? 'show active' : ''}" 
               id="v-note${visibleNoteCount + 1}" 
               role="tabpanel" 
               aria-labelledby="v-note${visibleNoteCount + 1}-tab" 
               data-note-master-id="${noteId}">
            <div class="note-it" style="background:${color}">
              <div class="note-heading" contenteditable="true" style="outline: none;" data-maxlength="255">${heading}</div>
              <div class="note-id" style="display: none;">${noteId}</div>
              <div class="note-details" contenteditable="true" data-maxlength="10000">${details}</div>
              <br><br>
              <span style="color: lightgreen">Great</span>.
            </div>
            <div class="taskmenus" style="display: flex; justify-content: flex-end;">
              <a href="#" class="save-note">
                <span class="material-symbols-outlined">task_alt</span>
              </a>
              <span class="countdown-timer text-muted ms-2" style="font-size:12px;"></span>
            </div>
          </div>`;
        $('#v-notes-tabContent').append(paneHtml);

        visibleNoteCount++;
      }
    });
  }

  // Fallback: no notes found → show blank default note
  if (visibleNoteCount === 0) {
    const emptySidebar = `
      <a class="note-it v-note-it active"
         data-id="1"
         id="v-note1-tab"
         data-bs-toggle="pill"
         data-bs-target="#v-note1"
         role="tab"
         aria-controls="v-note1"
         aria-selected="true"
         style="background:#f9f9f9">
         <span class="text-muted">New Note</span>
      </a>`;
    $('#stickyNoteContainer').html(emptySidebar);

    const emptyTabPane = `
      <div class="tab-pane fade show active" id="v-note1" role="tabpanel" aria-labelledby="v-note1-tab" data-note-master-id="1">
        <div class="note-it" style="background:#f9f9f9">
          <div class="note-heading" contenteditable="true" style="outline: none;" data-maxlength="255"></div>
          <div class="note-id" style="display: none;">1</div>
          <div class="note-details" contenteditable="true" data-maxlength="10000"></div>
          <br><br>
          <span style="color: lightgreen">Great</span>.
        </div>
        <div class="taskmenus" style="display: flex; justify-content: flex-end;">
          <a href="#" class="save-note">
            <span class="material-symbols-outlined">task_alt</span>
          </a>
          <span class="countdown-timer text-muted ms-2" style="font-size:12px;"></span>
        </div>
      </div>`;
    $('#v-notes-tabContent').html(emptyTabPane);
  }
  
  // Always add the "Add New Note" icon after loading notes
const addButton = `
  <div class="text-center mt-2">
    <button class="btn btn-sm btn-outline-primary add-note-icon" title="Add Note">
      <span class="material-symbols-outlined align-middle">add</span> Add Note
    </button>
  </div>`;
$('#stickyNoteContainer').append(addButton);

}

    
    ,
    error: function (xhr, status, err) {
      console.error('Error loading notes:', err);
      $('#stickyNoteContainer').html(`<div class="text-danger small">Failed to load notes.</div>`);
    }
  });
}

$('#notesModal').on('show.bs.modal', function () {
  fetchAndRenderNotes();
});


$('input[name="note_type"]').on('change', function () {
  fetchAndRenderNotes();
});

$(document).on('click', '.add-note-icon', function () {
  // Find max existing note_master_id
  let maxId = 0;
  $('#stickyNoteContainer .note-it').each(function () {
    const id = parseInt($(this).data('id'));
    if (!isNaN(id) && id > maxId) {
      maxId = id;
    }
  });
  const newId = maxId + 1;
  const index = $('#stickyNoteContainer .note-it').length + 1;

  // Sidebar Note
  const newSidebarNote = `
    <a class="note-it v-note-it"
       data-id="${newId}"
       id="v-note${index}-tab"
       data-bs-toggle="pill"
       data-bs-target="#v-note${index}"
       role="tab"
       aria-controls="v-note${index}"
       aria-selected="false"
       style="background:#f9f9f9">
       New Note ${index}
    </a>`;
  $(newSidebarNote).insertBefore('.add-note-icon').hide().fadeIn();

  // Tab Pane
  const newTabPane = `
    <div class="tab-pane fade" id="v-note${index}" role="tabpanel" aria-labelledby="v-note${index}-tab" data-note-master-id="${newId}">
      <div class="note-it" style="background:#f9f9f9">
        <div class="note-heading" contenteditable="true" style="outline: none;" data-maxlength="255">Note ${index}</div>
        <div class="note-id" style="display: none;">${newId}</div>
        <div class="note-details" contenteditable="true" data-maxlength="10000">Note ${index}</div>
        <br><br>
        <span style="color: lightgreen">Great</span>.
      </div>
      <div class="taskmenus" style="display: flex; justify-content: flex-end;">
        <a href="#" class="save-note">
          <span class="material-symbols-outlined">task_alt</span>
        </a>
        <span class="countdown-timer text-muted ms-2" style="font-size:12px;"></span>
      </div>
    </div>`;
  $('#v-notes-tabContent').append(newTabPane);

  // Activate new note
  $(`#v-note${index}-tab`).tab('show');
});


$(document).ready(function () {
    // Save note on button click
    $('.save-note').on('click', function (e) {
  e.preventDefault();

  const tabPane = $(this).closest('.tab-pane');
  const noteId = tabPane.data('note-master-id');
  const heading = tabPane.find('.note-heading').text().trim();
  const details = tabPane.find('.note-details').html().trim();
  const selectedNoteType = $('input[name="note_type"]:checked').val();

  if (!heading && !details) {
    alert('Note is empty!');
    return;
  }

  $.ajax({
    url: '<?= base_url('admin/office_tools/saveNote') ?>',
    type: 'POST',
    dataType: 'json',
    data: {
      id: noteId,
      heading: heading,
      details: details,
      note_type: selectedNoteType
    },
    success: function (res) {
        
        fetchAndRenderNotes();
        console.log(res.aic_notes_id);
      if (selectedNoteType === 'my' && res.aic_notes_id) {
        const tabPane = $('[data-note-master-id="' + res.note_master_id + '"]');

        if (res.aic_notes_heading && res.aic_notes_heading.trim() !== "") {
          tabPane.find('.note-heading h3').text(res.aic_notes_heading);
          // or use `.text()` directly if no h3 inside
        }

        if (res.aic_notes_details && res.aic_notes_details.trim() !== "") {
          tabPane.find('.note-details').html(res.aic_notes_details);
        }

        // Visual feedback
        tabPane.fadeOut(100).fadeIn(100);
      } else{
              const tabPane = $('[data-note-master-id="' + res.note_master_id + '"]');

        if (res.erp_notes_heading && res.erp_notes_heading.trim() !== "") {
          tabPane.find('.note-heading').text(res.erp_notes_heading);
         
        }

        if (res.erp_notes_details && res.erp_notes_details.trim() !== "") {
          tabPane.find('.note-details').html(res.erp_notes_details);
        }

       
        tabPane.fadeOut(100).fadeIn(100);
          
      }
    },
    error: function (xhr, status, err) {
      console.error('Save error:', err);
      alert('An error occurred while saving.');
    }
  });
});

  });

$(document).ready(function () {
    $('.v-note-it').on('click', function () {
      var noteId = $(this).data('id');
  
        const selectedNoteType = $('input[name="note_type"]:checked').val();

      // Optional: highlight for debugging
     // console.log('Note ID clicked:', noteId);

      $.ajax({
        url: '<?= base_url('admin/office_tools/stickyNote') ?>', 
        type: 'POST',
        data: { id: noteId, note_type : selectedNoteType },
        dataType: 'json',
        success: function (res) {
         if (selectedNoteType === 'my' && res.aic_notes_id) {
        const tabPane = $('[data-note-master-id="' + res.note_master_id + '"]');

        if (res.aic_notes_heading && res.aic_notes_heading.trim() !== "") {
          tabPane.find('.note-heading').text(res.aic_notes_heading);
         
        }

        if (res.aic_notes_details && res.aic_notes_details.trim() !== "") {
          tabPane.find('.note-details').html(res.aic_notes_details);
        }

       
        tabPane.fadeOut(100).fadeIn(100);
      }else{
              const tabPane = $('[data-note-master-id="' + res.note_master_id + '"]');

        if (res.erp_notes_heading && res.erp_notes_heading.trim() !== "") {
          tabPane.find('.note-heading').text(res.erp_notes_heading);
         
        }

        if (res.erp_notes_details && res.erp_notes_details.trim() !== "") {
          tabPane.find('.note-details').html(res.erp_notes_details);
        }

       
        tabPane.fadeOut(100).fadeIn(100);
          
      }
        },
        error: function (xhr, status, error) {
          console.error('AJAX error:', error);
        }
      });
    });
  });

$(".btn-success").filter(function() {
    return $(this).text().trim() === "Templates";
}).hide();


$(document).ready(function () {
    $('.open-comingsoon').on('click', function (e) {
      e.preventDefault();
	  

      $('#comingSoonModal').modal('show');
    });
  });

	function showtour(){
		 introJs().start();
		
	}
	
    function alert_notification(aler_message,button_label='Ok'){
	   Swal.fire({
        html: aler_message,
        icon: 'error',
		confirmButtonText: button_label
      })
	  return false;	
	}	

   function alert_success(aler_message){
     Swal.fire({
        html: aler_message,
        icon: 'success'
      })
    return false; 
  } 
    
  function alert_eway_timer(aler_message,timer){
	let timerInterval;
Swal.fire({
  html: aler_message,
  timer: timer,
  icon: 'success',
  timerProgressBar: true,
  didOpen: () => {
    Swal.showLoading();
    const timer = Swal.getPopup().querySelector("b");
    timerInterval = setInterval(() => {
     
    }, 100);
  },
  willClose: () => {
    clearInterval(timerInterval);

  }
})  
   
  }

   function confirm_voucher_delete(vouchers_array){
      Swal.fire({
        title: 'Are you sure?',
        text: "Restore possible not possible.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes Delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
        
       show_loader();
         
        $.post(baseurl+"/admin/reports/remove_daybook_vouchers",
          {
            vchrids: vouchers_array,
            token: "<?php echo date('ssYssHs');?>"
          },
          function(response, status){
            // console.log(response);
            if(typeof response == 'string'){
              response = JSON.parse(response);
            }
            stop_loader();
            if(response.status){
                alert_success(response.message);
				location.reload();
                $("#grid_search").pqGrid('refreshDataAndView');
                $('#trash').text('Enable Trash Mode'); 
                $('#trash').data('type', '0');
            
                
                if ( typeof hide_trash_checkbox  === 'function') {
                  hide_trash_checkbox();
                }
                var html = ``;
                if(response.errors.length)
                {
                    var list = ``;
                    $.each(response.errors, function(index, value){
                       list += `
                          <li>${value}</li>
                       `;
                    });
                    html = `
                        <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          <ul>
                              <li>${list}</li>
                          </ul>
                      </div>
                    `;
                }  
                $('#validation_errors').html(html);
             }
          });
        
        }
      });
	  return false;
    }


    
  function confirm_company_delete(path){
      Swal.fire({
        title: 'Are you sure?',
        text: "Company will be moved to recycle bin and can only be restored with in 30 days",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Delete entire company!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          window.location.href=path;
        }
      });
	  return false;
    }
    
 function confirm_exception_delete(vouchersarray){
       Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
         
       show_loader();
         
        $.post(baseurl+"/admin/reports/remove_exception_vouchers",
          {
            vchrids: vouchersarray,
            token: "<?php echo date('ssYssHs');?>"
          },
          function(data, status){
             if(data=="1")
                location.reload(); 
          });
        }
      });
	  return false;
	
    }      

  function confirm_delete(path){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          // window.location.href=path;

          $.ajax({
            url: path, 
            type: 'GET',
            data: {},
            dataType: "json",
            processData: false,
            cache: true,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('.deletebtn').attr('disabled', 'disabled');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    if(response.reload == 1)
                      window.location.reload();
                    else
                      window.history.back();
                }
                else{
                    alert_notification(response.message);
                    if(response.errors)
                    {
                        var list = ``;
                        $.each(response.errors, function(index, value){
                           list += `
                              <li>${value}</li>
                           `;
                        });
                        var html = `
						  <div class="alert-error-custom">
							<i class="bi bi-x-circle-fill"></i>
							<div>
							  <strong>Error!</strong> <ul>
														  <li>${list}</li>
													  </ul>
							</div>
							<button type="button" class="btn-close" aria-label="Close"></button>
						  </div>
                        `;
                        $('#validation_errors').html(html);
                    } 
                }
                
            },
            complete: function() {
                stop_loader();
                $('.deletebtn').attr('disabled', false);
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
      });
	  return false;
    }

	$(".needs-validation").on('submit', function (event) {
	    
	   

	    $(this).addClass('was-validated');
	    error=0;
	    
	     if ( typeof beforeItemSubmit  === 'function') {
            if(!beforeItemSubmit()){
                error = "1";
            }
        }
     	//console.log('flag 1');
        var datevalidate=1;
		/*
      if($('input').hasClass('datepicker')){
          
          $(".datepicker").each(function() {
           
         	var dateFrom   = '<?php echo $fy_begndt;?>';
            var dateTo     = '<?php echo $fy_end;?>';
            
            var dateCheck  =  $(this).val();
            var inputid    =  $(this).attr("id");
            
           
            var d1 = dateFrom.split("-");
            var d2 = dateTo.split("-");
            var c  = dateCheck.split("-");
            
            var from_year = d1[2];
            var to_year   = d2[2];
            var check_year = c[2];
			//console.log("("+check_year+"=="+from_year+") || ("+check_year+"=="+to_year+")");
            
            if( (check_year==from_year) || (check_year==to_year))
             var datevalidate=1;
            else
             var datevalidate=0;
             
              if(datevalidate=="0"){
              error= "1";
              $("#"+inputid).css('border-color','#ed2000');
              $("#"+inputid).css({'background-image':'url(<?php echo base_url();?>public/assets/images/error.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
              }else{
              	error="0";
                $("#"+inputid).removeAttr("style");  
               $("#"+inputid).css({'background-image':'url(<?php echo base_url();?>public/assets/images/success.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
              
               }
               
          });
          
      } */
      //console.log('flag 2');
       if($('select').hasClass('required')){
          $(".required ").each(function() {
           var txtbx_val = $(this).val();
           if(txtbx_val.length=="0"){
              error= "1";
              $(this).css('border-color','#ed2000');
               $(this).css({'background-image':'url(<?php echo base_url();?>public/assets/images/error.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
              }else{
              	error="0";
              	 $(this).css({'background-image':'url(<?php echo base_url();?>public/assets/images/success.svg)','background-repeat':'no-repeat','background-position':'right calc(0.3725em + 0.25rem) center','background-size':'calc(0.745em + 0.5rem) calc(0.745em + 0.5rem)'});
                 $(this).css('border-color','#25b003');
                $(this).parent('select').removeClass('required');  
                 }
             });
           }
           
      // console.log('flag 3'); 
        
	  if ($(this)[0].checkValidity() === false  ||  error=="1" ) {
		  //console.log('flag 4');
		event.preventDefault();
		event.stopPropagation();
	
            return false;
       
	  }
	  else if ($(this)[0].checkValidity() === true  && error=="1") {
		  //console.log('flag 5');
	       
	    event.preventDefault();
		event.stopPropagation();
            return false;
      
	  }
	  else
	  { //console.log('flag 6');
	    show_loader();
	    return true;
	      
	  }
	 // console.log('flag 7');
    return false;
	});
</script>
<script> //calander
  
    var fy_calender = <?= json_encode(fy_calender_js()) ?>;
    // console.log(fy_calender)
    setCalender()

    function setCalender()
    {
      var months = fy_calender.months;

      var html = ``;
      html += `<div class="row">`;

      var chunkSize = 3;
      for (var i = 0; i < months.length; i += chunkSize) {
          var chunk = months.slice(i, i + chunkSize);
          
          if(chunk.length == 1){
             html += `<div class="col-md-3 d-grid px-1"></div>`;
             html += `<div class="col-md-3 d-grid px-1"></div>`;
             html += `<div class="col-md-3 d-grid px-1"></div>`;
             html += `<div class="col-md-3 d-grid px-1">
                <button type="button" data-month="${chunk[0].month}" data-year="${chunk[0].year}" class="btn btn-light btn-block mb-1 month_btn">${chunk[0].label}</button>
             </div>`;
          }
          else{
            html += `<div class="col-md-3 d-grid px-1">`;
            chunk.forEach(function(obj){
              html += `<button type="button" data-month="${obj.month}" data-year="${obj.year}" class="btn btn-light btn-block mb-1 month_btn">${obj.label}</button>`;
            });
            html += `</div>`;
          }
          
      }
      html += `</div>`;

      var quarters = fy_calender.quarters;
      html += `<div class="row">`;
      quarters.forEach(function(obj){
        html += `<div class="col-md-3 d-grid px-1">`;
        html += `<button type="button" data-from_day="${obj.from_day}" data-from_month="${obj.from_month}" data-from_year="${obj.from_year}" data-to_day="${obj.to_day}" data-to_month="${obj.to_month}" data-to_year="${obj.to_year}" class="btn btn-qlight btn-block mb-1 quarter_btn">${obj.label}</button>`;
        html += `</div>`;
      });
      html += `</div>`;

      var half_years = fy_calender.half_years;
      html += `<div class="row">`;
      half_years.forEach(function(obj){
        html += `<div class="col-md-3 d-grid px-1">`;
        html += `<button type="button" data-from_day="${obj.from_day}" data-from_month="${obj.from_month}" data-from_year="${obj.from_year}" data-to_day="${obj.to_day}" data-to_month="${obj.to_month}" data-to_year="${obj.to_year}" class="btn btn-hlight btn-block mb-1 hyear_btn">${obj.label}</button>`;
        html += `</div>`;
      }); 
      html += `<div class="col-md-6 d-grid p-2 tillprdiv" id="tillprdiv"><span class="fw-bold d-inline-block px-4"><input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span></div>`;
      html += `</div>`;

      $('.comp_calender').html(html);
      $('.comp_calender').attr('data-from_day', fy_calender.from_day);
     $('.comp_calender').attr('data-from_month', fy_calender.from_month);
      $('.comp_calender').attr('data-from_year', fy_calender.from_year);
      $('.comp_calender').attr('data-to_day', fy_calender.to_day);
      $('.comp_calender').attr('data-to_month', fy_calender.to_month);
      $('.comp_calender').attr('data-to_year', fy_calender.to_year);

      set_fy_label();
      // set_fy_date();
    }

    function validate_from_date(datee){

        var d = datee.split("-");
        var from_date = new Date(d[2], d[1]-1, d[0]);

        var fy_from_day = $('.comp_calender').attr('data-from_day');
        var fy_from_month = $('.comp_calender').attr('data-from_month');
        var fy_from_year = $('.comp_calender').attr('data-from_year');

        var fy_from_date = new Date(parseInt(fy_from_year),(parseInt(fy_from_month)-1),parseInt(fy_from_day));
        
        from_date.setHours(0,0,0,0);
        fy_from_date.setHours(0,0,0,0);

        if(from_date > fy_from_date) {
            return datee;   
        }
        return fy_from_day+'-'+fy_from_month+'-'+fy_from_year;
    }
	
	function validatetodate(datee){
        var d = datee.split("-");
        var to_date = new Date(d[2], d[1]-1, d[0]);

        var fy_to_day = $('.comp_calender').attr('data-to_day');
        var fy_to_month = $('.comp_calender').attr('data-to_month');
        var fy_to_year = $('.comp_calender').attr('data-to_year');
        var fy_to_date = new Date(parseInt(fy_to_year),(parseInt(fy_to_month)-1),parseInt(fy_to_day));
        
        to_date.setHours(0,0,0,0);
        fy_to_date.setHours(0,0,0,0);
		//console.log(to_date+"<="+fy_to_date);
        if(to_date <= fy_to_date) {
            return datee;   
        }
        return false;
    }
	
	
    function validate_to_date(datee){
        var d = datee.split("-");
        var to_date = new Date(d[2], d[1]-1, d[0]);

        var fy_to_day = $('.comp_calender').attr('data-to_day');
        var fy_to_month = $('.comp_calender').attr('data-to_month');
        var fy_to_year = $('.comp_calender').attr('data-to_year');
        var fy_to_date = new Date(parseInt(fy_to_year),(parseInt(fy_to_month)-1),parseInt(fy_to_day));
        
        to_date.setHours(0,0,0,0);
        fy_to_date.setHours(0,0,0,0);

        if(to_date < fy_to_date) {
            return datee;   
        }
        return fy_to_day+'-'+fy_to_month+'-'+fy_to_year;
    } 
   
    function today()
    { 
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        
        today = dd + '-' + mm + '-' + yyyy;
        return today;
    }

    function set_fy_label()
    {
      var fy_from_year = $('.comp_calender').attr('data-from_year');
      var fy_to_year = $('.comp_calender').attr('data-to_year');

      var from_year = parseInt(fy_from_year);
      var to_year = parseInt(fy_to_year);

      if(from_year == to_year)
        $('#fy_year').text(`FY: ${from_year}`);
      else{
        var sub_from = from_year.toString().substr(0,2);
        var sub_to = to_year.toString().substr(0,2);
        if(sub_from == sub_to){
          $('#fy_year').text(`FY: ${from_year} - ${to_year.toString().substr(2,2)}`);
        }
        else
          $('#fy_year').text(`FY: ${from_year} - ${to_year}`);
      }
    }

    function set_fy_date()
    {
      var from_day = $('.comp_calender').attr('data-from_day');
      var from_month = $('.comp_calender').attr('data-from_month');
      var from_year = $('.comp_calender').attr('data-from_year');
      var from_date = from_day + '-' + from_month + '-' + from_year;

      var to_day = $('.comp_calender').attr('data-to_day');
      var to_month = $('.comp_calender').attr('data-to_month');
      var to_year = $('.comp_calender').attr('data-to_year');
      var to_date = to_day + '-' + to_month + '-' + to_year;

      $("#fromdate").val(from_date);
      $("#todate").val(to_date);
    }
    
    $(document).on("click","#next_year",function(){
        
      var fy_from_year = $('.comp_calender').attr('data-from_year');
      var fy_to_year = $('.comp_calender').attr('data-to_year');
        
      var from_year = parseInt(fy_from_year);
      var to_year = parseInt(fy_to_year);

      from_year++;
      to_year++;

      $('.comp_calender').attr('data-from_year', from_year);
      $('.comp_calender').attr('data-to_year', to_year);

        $(".month_btn").each(function() {
            var year = $(this).attr('data-year');
            year = parseInt(year);
            year++;
            $(this).attr('data-year',year);
        });

        $(".quarter_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year++;
            to_year++;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });

        $(".hyear_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year++;
            to_year++;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });

        set_fy_label();
        set_fy_date();
    });

    $(document).on("click","#prev_year",function(){
      var fy_from_year = $('.comp_calender').attr('data-from_year');
      var fy_to_year = $('.comp_calender').attr('data-to_year');
        
      var from_year = parseInt(fy_from_year);
      var to_year = parseInt(fy_to_year);

      from_year--;
      to_year--;

      $('.comp_calender').attr('data-from_year', from_year);
      $('.comp_calender').attr('data-to_year', to_year);

        $(".month_btn").each(function() {
            var year = $(this).attr('data-year');
            year = parseInt(year);
            year--;
            $(this).attr('data-year',year);
        });

        $(".quarter_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year--;
            to_year--;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });

        $(".hyear_btn").each(function() {
            var from_year = $(this).attr('data-from_year');
            var to_year = $(this).attr('data-to_year');
            from_year = parseInt(from_year);
            to_year = parseInt(to_year);
            from_year--;
            to_year--;
            $(this).attr('data-from_year',from_year);
            $(this).attr('data-to_year',to_year);
        });

        set_fy_label();
        set_fy_date();
    });

    $(document).on("click","#fy_year",function(){
        
        set_fy_date();
    })
    
    $(document).on("click",".month_btn",function(){
        var month = $(this).attr('data-month');
        var year = $(this).attr('data-year');

        var fd = '01';
        var ld = new Date(year, month, 0).getDate();
        //console.log("==="+ld);
        var y = year; 
        var m = month < 10 ? '0'+month : month;
        
        var from_date = fd + '-' + m + '-' + y;
        var to_date = ld + '-' + m + '-' + y;
        
        if($('#tillperiod').is(":checked")){
        var ldmonth = new Date(year, month, 0).getMonth();
		var ldyear = new Date(year, month, 0).getFullYear();
		if(ldmonth<9)
			var show_ldmonth = "0"+(parseInt(ldmonth)+parseInt(1));
		 else
			var show_ldmonth = (parseInt(ldmonth)+parseInt(1));
          var td = today();
          var arr = td.split('-');
          var ld = new Date(arr[2], arr[1], 0).getDate();
          //to_date = ld+ '-' + arr[1] + '-' + arr[2];
		 
		
		 from_dates = $("#fromdate").val();
		 
		 to_dates = ld+ '-' + show_ldmonth + '-' + ldyear;
		 
		 //to_date = validate_to_date(to_date);
		//console.log(to_dates);
		
		var d = to_dates.split("-");
        var to_date = new Date(d[2], d[1]-1, d[0]);
		
		var dd = from_dates.split("-");
        var from_date = new Date(dd[2], dd[1]-1, dd[0]);

		  if(new Date(to_date)>new Date(from_date)){
			
		    $("#fromdate").val(from_dates);		
		    $("#todate").val(to_dates);  
			$('#warning_message').remove();
		  }else{
			  // console.log('invalid');
			$("#fromdate").val(from_dates);	 	
		    $("#todate").val('');
		
		if($("#warning_message").length > 0 ){
		$('#warning_message').remove();
		}else{
		   $('#tilldate').after('<span id="warning_message" style="color:red;">Invalid To Date</span>');
		}
	  }
		  
        }else{
        from_date = validate_from_date(from_date);
		$("#fromdate").val(from_date);
		 to_date = validate_to_date(to_date);
		 $("#todate").val(to_date);
		}
	
		
		
        
        
        
    });
    
    $(document).on("click",".quarter_btn",function(){
        var from_day = $(this).attr('data-from_day');
        var from_month = $(this).attr('data-from_month');
        var from_year = $(this).attr('data-from_year');
        var to_day = $(this).attr('data-to_day');
        var to_month = $(this).attr('data-to_month');
        var to_year = $(this).attr('data-to_year');

        from_day < 10 ? '0'+from_day : from_day;
        from_month < 10 ? '0'+from_month : from_month;
        to_day < 10 ? '0'+to_day : to_day;
        to_month < 10 ? '0'+to_month : to_month;

        var from_date = from_day+'-'+from_month+'-'+from_year;
        var to_date = to_day+'-'+to_month+'-'+to_year;
        
        if($('#tillperiod').is(":checked")){
        
          var td = today();
          var arr = td.split('-');
          var ld = new Date(arr[2], arr[1], 0).getDate();
          to_date = ld+ arr[1] +'-' + arr[2];
        }

        from_date = validate_from_date(from_date);
        to_date = validate_to_date(to_date);
        
        $( "#fromdate" ).val(from_date);
        $( "#todate" ).val(to_date);
    });
    
    $(document).on("click",".hyear_btn",function(){
        var from_day = $(this).attr('data-from_day');
        var from_month = $(this).attr('data-from_month');
        var from_year = $(this).attr('data-from_year');
        var to_day = $(this).attr('data-to_day');
        var to_month = $(this).attr('data-to_month');
        var to_year = $(this).attr('data-to_year');

        from_day < 10 ? '0'+from_day : from_day;
        from_month < 10 ? '0'+from_month : from_month;
        to_day < 10 ? '0'+to_day : to_day;
        to_month < 10 ? '0'+to_month : to_month;

        var from_date = from_day+'-'+from_month+'-'+from_year;
        var to_date = to_day+'-'+to_month+'-'+to_year;
        
        if($('#tillperiod').is(":checked")){
        
          var td = today();
          var arr = td.split('-');
          var ld = new Date(arr[2], arr[1], 0).getDate();
          to_date = ld+ arr[1] +'-' + arr[2];
        }

        from_date = validate_from_date(from_date);
        to_date = validate_to_date(to_date);
        
        $( "#fromdate" ).val(from_date);
        $( "#todate" ).val(to_date);
    });
    
    $(document).on("click","#tilldate",function(){

        $('#tillperiod').prop('checked', false);
        
        if($("#fromdate").val() == '' || checkdate($("#fromdate").val())){
          var from_day = $('.comp_calender').attr('data-from_day');
          var from_month = $('.comp_calender').attr('data-from_month');
          var from_year = $('.comp_calender').attr('data-from_year');
          var from_date = from_day + '-' + from_month + '-' + from_year;
          $( "#fromyear" ).val(from_year);
        }
         
        var to_date = today();
        to_date = validate_to_date(to_date);
        $( "#todate" ).val(to_date);
    });
    
    
    function checkdate(datee)
    {
        var d = datee.split("-");

        var varDate = new Date(d[2], d[1]-1, d[0]);
        var today = new Date();
        
        varDate.setHours(0,0,0,0);
        today.setHours(0,0,0,0);

        if(varDate > today) {
            return true;   
        }
        return false;
    }
</script>
<style>
    .grid_footer_color{background: #DEE9EF;font-weight:bold}
    .yellowcolor{background-color:yellow;}
</style>
<script type='text/javascript'>


  $(document).ready(function(){
    $('#calcModal').modal({keyboard: false})

    $("#email").focus();
            
    var displayBox = document.getElementById('display')
    var hasEvaluated = false

            
  $(document).keyup(function(e) {
             // escape key pressed  
             
  //            if (document.activeElement && document.activeElement != document.body) {
  //   document.activeElement.blur();
  // }

      if (document.activeElement && $('#calcModal').has(document.activeElement).length) {
      document.activeElement.blur();
    }

        //     console.log(e.which);
			
    if(e.keyCode == '27'){
      if(cnfrm_on_addedit_pages=="1"){  
	   Swal.fire({
        title: 'Are you sure to go back?',
        text: "Voucher data will be lost",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          $( ".btn-outline-success" ).each(function( index ) {
          if($(this).text()=="Back" || $(this).text()=="« Back"){
            //window.open($(this).attr("href"), '_self').focus();           
			window.history.back();
          }
        });
        }
      });
	  }	  
	else{	
      if($('#calcModal').hasClass('show')){

        if($('#display').text() == '0'){
          $('#calcModal').modal('hide');
        }
        else{
          displayBox.innerHTML = '0';
        }
      }
      else{
        $( ".btn-outline-success" ).each(function( index ) {
          if($(this).text()=="Back" || $(this).text()=="« Back"){
            window.open($(this).attr("href"), '_self').focus();           
          }
        }); 
      }
	}
      e.stopPropagation();
	 
    }
                    
    if($('body').hasClass('modal-open')==true && $('#calcModal').hasClass('show') ){

    
      console.log(e.which);
      
      

      if(e.which=='97' || e.which=='49'){
        checkLength(displayBox.innerHTML)
        clickNumbers(1);
      }

      if(e.which=='98' || e.which=='50'){
        checkLength(displayBox.innerHTML)
        clickNumbers(2);   
      }

      if(e.which=='99'  || e.which=='51'){
        checkLength(displayBox.innerHTML)
        clickNumbers(3);         
      }

      if(e.which=='100'  || e.which=='52'){
        checkLength(displayBox.innerHTML)
        clickNumbers(4);  
      }
          
      if(e.which=='101'  || e.which=='53'){
        checkLength(displayBox.innerHTML)
        clickNumbers(5);   
      }
          
      if(e.which=='102'  || e.which=='54'){
        checkLength(displayBox.innerHTML)
        clickNumbers(6);  
      }
                 
      if(e.which=='103'  || e.which=='55'){
        checkLength(displayBox.innerHTML)
        clickNumbers(7);
      }

      // if(e.which=='104'  || e.which=='56'){
      //   checkLength(displayBox.innerHTML)
      //   clickNumbers(8); 
      // }
                 
      if(e.which=='105'  || e.which=='57'){
        checkLength(displayBox.innerHTML)
        clickNumbers(9);       
      } 

      if(e.which=='96'  || e.which=='48'){
        checkLength(displayBox.innerHTML)
        clickNumbers(0);          
      }  
                 
      if(e.which=='111'){
        evaluate()
        checkLength(displayBox.innerHTML)
        displayBox.innerHTML += '÷'            
      } 

      if(e.which=='106'){
        evaluate()
        checkLength(displayBox.innerHTML)
        displayBox.innerHTML += '×'            
      }

      // if(e.which=='56'){
      //   // alert();
      //   evaluate()
      //   checkLength(displayBox.innerHTML)
      //   displayBox.innerHTML += '×'            
      // }

      if (e.which == 187 && e.shiftKey) {
        evaluate();
        checkLength(displayBox.innerHTML);
        displayBox.innerHTML += '+';
      }

      if (e.which == 189 && e.shiftKey) {
        evaluate();
        checkLength(displayBox.innerHTML);
        displayBox.innerHTML += '-';
      }
// Handle Shift + 8 (which is '*')
if (e.which == 56 && e.shiftKey) {
  evaluate();
  checkLength(displayBox.innerHTML);
  displayBox.innerHTML += '×'; // or '*' if that's your operator
  return; // ✅ Prevent further processing (avoid 8 being added too)
}

// Now handle normal 8 (keyboard or numpad)
if (e.which == 104 || (e.which == 56 && !e.shiftKey)) {
  checkLength(displayBox.innerHTML);
  clickNumbers(8);
}


      if (e.which == 191 && e.shiftKey) {
        evaluate();
        checkLength(displayBox.innerHTML);
        displayBox.innerHTML += '/';
      }

      if(e.which=='109'){

        evaluate()
        checkLength(displayBox.innerHTML)
        displayBox.innerHTML += '-'           
      }  
      
      if(e.which=='13'){
                   
        evaluate()
        hasEvaluated = true              
      }    
                 
      if(e.which=='107'){
                   
        evaluate()
        checkLength(displayBox.innerHTML)
        displayBox.innerHTML += '+'           
      }   


      if (e.which == 8) {
  e.preventDefault();
  let current = displayBox.innerHTML;

  if (current.length > 1) {
    // Normal delete character
    displayBox.innerHTML = current.slice(0, -1);
  } else {
    // If last character removed, set to 0
    displayBox.innerHTML = '0';
  }
}



      if(e.which=='110'){

        checkLength(displayBox.innerHTML)
        if (displayBox.innerHTML.indexOf('.') === -1 ||
        (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('+') !== -1) ||
        (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('-') !== -1) ||
        (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('×') !== -1) ||
        (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('÷') !== -1)) {
          clickNumbers('.')
        }
                     
    }    



    }

  });
         
            
        /*   hot keys to menu */
        
       
        
        
       /*
        var keys = {};

        $(document).keydown(function(e) {
        
            keys[e.which] = true;
          
            if (e.which=='17') { 
                   if($(".topnavbar li").hasClass("hotkeys"))
                    $(".topnavbar li").removeClass("hotkeys");
                else{   
                for(var i=2;i<=8;i++){
                    $(".topnavbar li:nth-child("+i+")").addClass("hotkeys");
                   }
                }
            }
             
            if(e.which=='77' && e.altKey) { // Transaction div on press alt+m
                 $(".topnavbar #masterdiv_menu").show();
             }
             else
                $(".topnavbar #masterdiv_menu").hide();
            
            if(e.which=='84' && e.altKey) { // Transaction div on press alt+t
                $(".topnavbar #transactionsdiv_menu").show();
             }
             else
               $(".topnavbar #transactionsdiv_menu").hide();
            
            if(e.which=='88' && e.altKey) { // Taxes div on press alt+x
               $(".topnavbar #taxesdiv_menu").show();
             }
             else
               $(".topnavbar #taxesdiv_menu").hide();
            
            
            if(e.which=='84' && e.altKey) { // MIS div on press alt+x
               $(".topnavbar #misdiv_menu").show();
             }
            else
               $(".topnavbar #misdiv_menu").hide();
               
           if(e.which=='84' && e.altKey) { // Reports div on press alt+x
               $(".topnavbar #reportsdiv_menu").show();
             }
           else
               $(".topnavbar #reportsdiv_menu").hide();
               
          if(e.which=='84' && e.altKey) { // Register div on press alt+x
             $(".topnavbar #registerdiv_menu").show();
             }
          else
            $(".topnavbar #registerdiv_menu").hide();
               
          if(e.which=='84' && e.altKey) { // Audit div on press alt+x
               $(".topnavbar #auditdiv_menu").show();
             }
          else
              $(".topnavbar #auditdiv_menu").hide();      
               
                   
            if($(".topnavbar li").hasClass("hotkeys")){
              if(e.which=='77')
                 $(".topnavbar #masterdiv_menu").show();
              else
                 $(".topnavbar #masterdiv_menu").hide();
             }
            
            //if alt key on and then press any key to open div   
            if(e.altKey==true){
                if(e.which=='77')
                    $(".topnavbar #masterdiv_menu").show();
                 else
                 $(".topnavbar #masterdiv_menu").hide();
               
               if(e.which=='84')
                    $(".topnavbar #transactionsdiv_menu").show();
                 else
                 $(".topnavbar #transactionsdiv_menu").hide(); 
            }
        });
        /*
        $(document).keyup(function(e) {
          delete keys[e.which];
          $(".topdropnav").removeAttr('style');
        });
        */

  function clickNumbers (val) {
    if (displayBox.innerHTML === '0' || (hasEvaluated === true && !isNaN(displayBox.innerHTML))) {
      displayBox.innerHTML = val
    } else {
      displayBox.innerHTML += val
    }
    hasEvaluated = false
  }

        
$(document).ready(function () {
    
    
  // CHECK IF 0 IS PRESENT. IF IT IS, OVERRIDE IT, ELSE APPEND VALUE TO DISPLAY

  // PLUS MINUS
  $('#plus_minus').click(function () {
    if (eval(displayBox.innerHTML) > 0) {
      displayBox.innerHTML = '-' + displayBox.innerHTML
    } else {
      displayBox.innerHTML = displayBox.innerHTML.replace('-', '')
    }
  })

  // ON CLICK ON NUMBERS
  $('#clear').click(function () {
    displayBox.innerHTML = '0'
  //  $('#display').css('font-size', '80px')
  //  $('#display').css('margin-top', '110px')
    $('button').prop('disabled', false)
  })
  $('#one').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(1)
  })
  $('#two').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(2)
  })
  $('#three').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(3)
  })
  $('#four').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(4)
  })
  $('#five').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(5)
  })
  $('#six').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(6)
  })
  $('#seven').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(7)
  })
  $('#eight').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(8)
  })
  $('#nine').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(9)
  })
  $('#zero').click(function () {
    checkLength(displayBox.innerHTML)
    clickNumbers(0)
  })
  $('#decimal').click(function () {
    if (displayBox.innerHTML.indexOf('.') === -1 ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('+') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('-') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('×') !== -1) ||
      (displayBox.innerHTML.indexOf('.') !== -1 && displayBox.innerHTML.indexOf('÷') !== -1)) {
      clickNumbers('.')
    }
  })

  // OPERATORS
  $('#add').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '+'
  })
  $('#subtract').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '-'
  })
  $('#multiply').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '×'
  })
  $('#divide').click(function () {
    evaluate()
    checkLength(displayBox.innerHTML)
    displayBox.innerHTML += '÷'
  })
  $('#square').click(function () {
    var num = Number(displayBox.innerHTML)
    num = num * num
    checkLength(num)
    displayBox.innerHTML = num
  })
  $('#sqrt').click(function () {
    var num = parseFloat(displayBox.innerHTML)
    num = Math.sqrt(num)
    displayBox.innerHTML = Number(num.toFixed(5))
  })
  $('#equals').click(function () {
    evaluate()
    hasEvaluated = true
  })

  
  
  
  
  
  
    
})

function evaluate() {
  // Clean unwanted characters
  displayBox.innerHTML = displayBox.innerHTML.replace(',', '')
    .replace('×', '*')
    .replace('÷', '/');

  let expression = displayBox.innerHTML.trim();

  // If expression is empty
  if (expression === '') {
    displayBox.innerHTML = '0';
    return;
  }

  try {
    let result = eval(expression);

    // If result is Infinity (division by zero), set to 0
    if (!isFinite(result)) {
      result = 0;
    }

    if (result.toString().includes('.')) {
      result = parseFloat(result).toFixed(5);
    }

    checkLength(result);
    displayBox.innerHTML = result;
  } catch (error) {
    console.error('Invalid Expression:', error);
    displayBox.innerHTML = '0';
  }
}






  // CHECK FOR LENGTH & DISABLING BUTTONS
  function checkLength (num) {
    if (num.toString().length > 7 && num.toString().length < 14) {
     // $('#display').css('font-size', '35px')
    } else if (num.toString().length > 16) {
      num = 'Infinity'
      $('button').prop('disabled', true)
      $('.clear').attr('disabled', false)
    }
  }

  // TRIM IF NECESSARY
  function trimIfNecessary () {                                                 file = 'standard ignore' // eslint-disable-line
    var length = displayBox.innerHTML.length
    if (length > 7 && length < 14) {
      //$('#display').css('font-size', '35px')
    } else if (length > 14) {
      displayBox.innerHTML = 'Infinity'
      $('button').prop('disabled', true)
      $('.clear').attr('disabled', false)
    }
  }

     $(document).on("click",".tabslist",function(){
            var tbid  =  $(this).data("id");   
            var tburl = $(this).data("url"); 
			 
			 window.location.replace(baseurl+"/admin/dashboard/makeactive/"+tbid);
          
             })   
        
           
		
		 $('form input').keydown(function(e){
             if(e.keyCode==13){       

                if($(':input:eq(' + ($(':input').index(this) + 1) + ')').attr('type')=='submit'){// check for submit button and submit form on enter press
                 return true;
                }

                $(':input:eq(' + ($(':input').index(this) + 1) + ')').focus();

               return false;
             }

            });
        });
   
       
   
        /*
     $(document).on("click",".dropdown-menu a",function(){
         show_loader();
        var pageclicked = $(this).attr('href');
        var pagetitle   = $(this).text();
        
        $.post(baseurl+"/admin/dashboard/update_tab",
          {
            pageclicked: pageclicked,
            pagetitle: pagetitle
          },
          function(data, status){
         stop_loader();
          });
  
  
      
     }); */
     
        
    </script>
    <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>     
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>public/assets/js/jquery.inputmask.bundle.min.js" ></script> 
    <script type="text/javascript" src="<?php echo base_url();?>public/assets/grid/jsZip-2.5.0/jszip.min.js" ></script>
    <script type="text/javascript" src="<?php echo base_url();?>public/assets/grid/pqgrid.dev.js" ></script>  
    <script src="<?php echo base_url();?>public/assets/grid/localize/pq-localize-en.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/grid/pqselect.dev.css" /> 
    <script src="<?php echo base_url();?>public/assets/grid/pqselect.dev.js"></script> 
    
    <script>

    // (2 DIGIT NUMERIC + 5 CHARACTERS + 4 NUMERIC + 4 ALPHANUMERIC) 
    $(".gstinMask").inputmask('Regex', { 
      regex: "^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z0-9]{4}$",
      casing:'upper'
    });
	 $(".datepickernr").inputmask("99/99/9999", {
        mask: "99-99-9999",
        alias: "date",
        placeholder: "dd-mm-yyyy",
        insertMode: false,
    }); 
	$('.datepickernr').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",		
	})
	
    $(".datepicker").inputmask("99/99/9999", {
        mask: "99-99-9999",
        alias: "date",
        placeholder: "dd-mm-yyyy",
        insertMode: false,
    });

    $('.datepicker').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo $fy_begndt;?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });

    $(".datepickerReverseJournal").inputmask("99/99/9999", {
        mask: "99-99-9999",
        alias: "date",
        placeholder: "dd-mm-yyyy",
        insertMode: false,
    });

    $('.datepickerReverseJournal').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo date("d-m-Y", strtotime("+1 day", time()));?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();

        if(!isValidDate(date)){
         // console.log('date not valid');
          $(this).val("");
        }
    });

    $(".datepicker2").inputmask("99/99/9999", {
        mask: "99-99-9999",
        alias: "date",
        placeholder: "DD-MM-YYYY",
        insertMode: false,
    });

    $('.datepicker2').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });



    function isValidDate(dateString)
    {
        // First check for the pattern
        if(!/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(dateString))
            return false;

        // Parse the date parts to integers
        var parts = dateString.split("-");
        var month = parseInt(parts[1], 10);
        var day   = parseInt(parts[0], 10);
        var year  = parseInt(parts[2], 10);
		
		
		<?php
		if($current_url!=base_url().'/home/add_company'){ ?>
		/* if(allcompfy!=''){
       if(allcompfy.indexOf(year)=="-1" || month == 0 || month > 12)
		   return false;
		} */
		<?php } ?>

        // Check the ranges of month and year
        /* if(year < 1000 || year > 3000 || month == 0 || month > 12){
			alert("wrong case");
            return false;
		} */

        var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

        // Adjust for leap years
        if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
            monthLength[1] = 29;

        // Check the range of the day
        return day > 0 && day <= monthLength[month - 1];
    };
    
    $(".datepicker").on('keydown', function (e) {
            IsNumeric(this, e.keyCode);
        });
        
    var isShift = false;
        var seperator = "/";
        function IsNumeric(input, keyCode) {
            if (keyCode == 16) {
                isShift = true;
            }
            //Allow only Numeric Keys.
            if (((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode <= 37 || keyCode <= 39 || (keyCode >= 96 && keyCode <= 105)) && isShift == false) {
                if ((input.value.length == 2 || input.value.length == 5) && keyCode != 8) {
                    input.value += seperator;
                }
                return true;
            }
            else {
                return false;
            }
        };    
    $(document).ready(function(){
        $(".dropmenu").click(function(){
               $("body").toggleClass("sidetoogle");
         });
    });


    

</script>
<script>

 $("#todate").datepicker({
            showOn: 'both',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>public/assets/images/caldender-icon.png',
            dateFormat: 'dd-mm-yy',
        });    
$("#fromdate").datepicker({
            showOn: 'both',
            buttonImageOnly: true,
            buttonImage: '<?php echo base_url();?>public/assets/images/caldender-icon.png',
            dateFormat: 'dd-mm-yy',
        });     
</script>
<?php 
if($local_session->get('ses_company_fy_beginning'))
$fy_bgn_yr = date('Y',strtotime($local_session->get('ses_company_fy_beginning'))); 
else
$fy_bgn_yr =date('Y');


?>

<script>
   /***************  FY Popup start    ****************/
   $(document).ready(function() {
	   
	$(document).on("click",".selfy_change",function(){
		 $(".dropdown-menu").removeClass('show');
		if($(this).hasClass("active")){}else{
			var fyidval =$(this).data("id");
			load_FYlink(fyidval);
    
     async function load_FYlink(fyidval) {
          show_loader();
         $(".siteloader").show();
           let start_time = performance.now();
        
          var interval   = setInterval(function(){
                var currenttime  = performance.now();
                var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
                 $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
                 $('#progressbar').attr('aria-valuenow',pct);
                 $('#progressbar .progress-bar').css("width", pct+"%");
                 $('#progressbar .progress-bar').text(pct+"%");
              
          }, 1000); 
          
       
         let response = await fetch(baseurl+'admin/change_company_fy/'+fyidval);
            
            let result = await response.json();
            requestTime = performance.now();
           if(result){
            clearInterval(interval);
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");
            window.location.href= baseurl+'admin/dashboard';
           } 
       }
			
		}

		
	});   
	$('#FYModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on("click","#refreshbtndfy", function(){
	var val_index = $(this).data("id");	
	import_fy_data(val_index);  
	$(this).hide();
	
});

   var FYindex = 0;
   var FYtype = '';
   var fy_id=''; 
   var FYListsLabels = [
        'Creating FY',
        'Company',
		'Vouchers',
        'Voucher Series',
        'Branch',
        'Currency',
        'Printing Configuration',
        'Accounts',        
        'Material Centers',
        'Bill Sundry',
        'Cost Centers',
        'Bill By Bill',
        'Bill Of Material',
        'Project Reporting',
        'Barcode',
		'Items',
        'Units',
        'Finalizing...' 
    ];
   var FYLists = [
        'create_fy',
        'company', 
        'vouchers',
        'voucher_series',
        'branch',
        'currency',
        'print',
        'accounts',        
        'material_center',
        'bill_sundry',
        'cost_center',
        'bill_by_bill',
        'bill_of_material',
        'project',
        'barcode',
		'items',
        'units',
        'final'
    ];
       
  function viewmodal_title(FYtype)
  {
  	var title = '';
    
  	if(FYtype == 'create_fy')
  		title = 'Creating Financial Year';
  	if(FYtype == 'company')
  		title = 'Updating Company Data';
    if(FYtype == 'vouchers')
      title = 'Updating Vouchers Data';
    if(FYtype == 'voucher_series')
      title = 'Updating Voucher Series Data';
    if(FYtype == 'branch')
      title = 'Updating Branch Data';
    if(FYtype == 'currency')
      title = 'Updating Currency Data';
    if(FYtype == 'print')
      title = 'Updating Printing Configuration Data';
    if(FYtype == 'accounts')
      title = 'Updating Accounts Data';
    if(FYtype == 'items')
      title = 'Updating Items Data';
    if(FYtype == 'units')
      title = 'Updating Units Data';
    if(FYtype == 'material_center')
      title = 'Updating Material Center Data';
    if(FYtype == 'bill_sundry')
      title = 'Updating Bill Sundry Data';
    if(FYtype == 'cost_center')
      title = 'Updating Cost Center Data';
    if(FYtype == 'bill_by_bill')
      title = 'Updating Bill By Bill Data';
    if(FYtype == 'bill_of_material')
      title = 'Updating Bill Of Material Data';
    if(FYtype == 'project')
      title = 'Updating Project Reporting Data';
    if(FYtype == 'barcode')
      title = 'Updating Barcode Data';
    if(FYtype == 'final')
      title = 'Finalizing Data';	

  	return title;
  }  

  var get_fy_details_xhr='';

  $(".retrycompfy").on("click",function(){

    var FYtype = 'company';
    var fy_id  = $(this).data("id");
    $('#FYModal .modal-title').text(viewmodal_title(FYtype));
    $('#FYModal .close_btn').text('Cancel');
    $('#FYModal .close_btn').removeClass('btn-success').addClass('btn-danger');
    $('#FYModal .overall_status').text('Please wait data is importing...');
    $('#FYModal .current_status').text('');
    $('#FYModal .progress-bar').css('width', '0%');
    $('#FYModal .progress-bar').text('0%');
    $('#FYModal').modal('show');
    import_fy_data(1);
    return false;
  });
   
   
  $(".addnewfy").on("click",function(){
    var FYtype = 'create_fy';
    $('#FYModal .modal-title').text(viewmodal_title(FYtype));
    $('#FYModal .close_btn').text('Cancel');
    $('#FYModal .close_btn').removeClass('btn-success').addClass('btn-danger');
    $('#FYModal .overall_status').text('Please wait data is importing...');
    $('#FYModal .current_status').text('');
    $('#FYModal .progress-bar').css('width', '0%');
    $('#FYModal .progress-bar').text('0%');
    $('#FYModal').modal('show');
    import_fy_data(0);
    return false;
  });
   
   
  var fy_update_xhr;

function import_fy_data(FYindex, batchState = {}) {
  var totalModules = FYLists.length;

  // Update module progress bar
  var moduleProgress = Math.round((FYindex / totalModules) * 100);
  $('#FYModal .module-progress').css('width', moduleProgress + '%').text(moduleProgress + '%');

  if (FYindex < totalModules) {
    var currentModule = FYLists[FYindex]; 
	var currentModuleLabel = FYListsLabels[FYindex];
    $('#FYModal .module_status').text(`Module ${FYindex + 1} of ${totalModules}: ${currentModuleLabel}`);
    $('#FYModal .query_status').text(`Processing queries in: ${currentModuleLabel}`);

    // Prepare data to send
    var postData = { type: currentModule };
    var timeout = 10000;

    // If current module is "item", use step and offset for batching
    if (currentModule === 'items') {
      postData.step = batchState.next_step || 0;  // Ensuring step is passed correctly
      postData.offset = batchState.next_offset || 0;  // Ensuring offset is passed correctly
      timeout = 20000; // Timeout increased for item processing
    }

    fy_update_xhr = $.ajax({
      url: baseurl+'admin/company/new_fy',
      type: 'POST',
      data: postData,
      dataType: "json",
      timeout: timeout,
      success: function (response) {
        if (response.percentage !== undefined) {
          let queryProgress = response.percentage;

          $('#FYModal .query-progress')
            .css('width', queryProgress + '%')
            .text(queryProgress + '%');

          // Continue batching if items are not finished
          if (currentModule === 'items' && response.status !== 'done') {
            setTimeout(() => {
              // Pass next_step and next_offset for continuous batching
              import_fy_data(FYindex, {
                next_step: response.next_step,  // Move to next step
                next_offset: response.next_offset  // Move to next offset
              });
			  
			  
			  
            }, 1000);
            return;
          }

          // Finished the module processing (either item or non-item)
          if (queryProgress >= 100 || response.status === 'done') {
            $('#FYModal .query_status').text('Query Completed');

            // Move to the next module if available
            setTimeout(() => {
              $('#FYModal .query-progress').css('width', '0%').text('0%');
              $('#FYModal .query_status').text('');

              FYindex++;
              import_fy_data(FYindex);  // Move to the next module
            }, 1000);
          } else {
            // Continue processing for non-item modules (if not finished)
            setTimeout(() => import_fy_data(FYindex), 800);
          }
        }
      },
      error: function (jqXHR, exception) {
        var error = '';
        if (jqXHR.status == 404) {
          error = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
          error = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
          error = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
          $("#FYModal #refreshbtndfy").attr("data-id", FYindex).show();
          error = 'Time out error.';
        } else if (jqXHR.status === 0 && exception === 'abort') {
          error = 'Ajax request aborted.';
        } else if (jqXHR.status === 0) {
          $("#FYModal #refreshbtndfy").attr("data-id", FYindex).show();
          error = 'Not connected. Verify Network.';
        } else {
          error = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        console.log(error);
      }
    });
  } else {
    // If all modules are completed
    $('#FYModal .query_status').text('');
	$('#FYModal .overall_status').text('');
    $('#FYModal .module_status').text('All modules imported successfully.');
    $('#FYModal .close_btn').text('Done').removeClass('btn-danger').addClass('btn-success');
	location.reload(); 
  }
}

  function import_fy_dataddd(FYindex){

  	var progress = Math.round((FYindex/FYLists.length)*100);
  	$('#FYModal .progress-bar').css('width', progress+'%'); 
  	$('#FYModal .progress-bar').text(progress+'%');
  	$('#FYModal .modal-title').text(viewmodal_title(FYLists[FYindex]));

  	if(FYindex < FYLists.length){

  		var current_status = `${(FYindex+1)} / ${FYLists.length} (${FYListsLabels[FYindex]})`;
  		$('#FYModal .current_status').text(current_status);

  		fy_update_xhr = $.ajax({
        url: baseurl+'admin/company/new_fy', 
        type: 'POST',
		timeout: 10000, // 10 secods
        data: {'type': FYLists[FYindex]},
        dataType: "json",
        beforeSend: function() {
    	    // show_loader();
        },
        success: function (response) {

           if(response.status){
             FYindex++;
			 $("#FYModal #refreshbtndfy").attr("data-id",FYindex);
             import_fy_data(FYindex);
           }
           else{
            alert(response.message);
          }
        },
        complete: function() {
    	    // stop_loader();
        },
        error: function (jqXHR, exception) {

          var error_= '';
          if (jqXHR.status == 404) {
           error = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
           error = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
           error = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
			  $("#FYModal #refreshbtndfy").attr("data-id",FYindex);								
			  $("#FYModal #refreshbtndfy").show();					
              error = 'Time out error.';
          } else if (jqXHR.status === 0 && exception === 'abort') {
           error = 'Ajax request aborted.';
          } else if (jqXHR.status === 0) {
			  $("#FYModal #refreshbtndfy").attr("data-id",FYindex);								
					$("#FYModal #refreshbtndfy").show();
           error = 'Not connect.\n Verify Network.';
          } else {
           error = 'Uncaught Error.\n' + jqXHR.responseText;
          }
  	            //alert(error);
        },
      });
  	}
  	else{
  		$("#FYModal #refreshbtndfy").attr("data-id",'');
  		$('#FYModal .overall_status').text('');
        $('#FYModal .current_status').text('All tables are imported');
        $('#FYModal .close_btn').text('Done');
        $('#FYModal .close_btn').removeClass('btn-danger').addClass('btn-success');
        location.reload(); 
    }
  }

  $("#FYModal").on('hide.bs.modal', function (){
	  if(get_fy_details_xhr)
		  get_fy_details_xhr.abort();  
    if(fy_update_xhr)
    	fy_update_xhr.abort();
   });

   
   /********************* FY Popup end         ****************/
   
   
    var key = {};
    var pq_index = 0;
    $(document).keydown(function(e) {
        key[e.which] = true;
        // console.log(e.which);
        
            
        if (key[16] && key[17]) { 
            
            var pq_grids = $('.pq-grid');
            if(pq_grids.length > 0){
                var pq_last_index = pq_index > 0 ? pq_index -1 : pq_grids.length;
                $(pq_grids[pq_last_index]).pqGrid('setSelection', null);
                
                $(pq_grids[pq_index]).pqGrid('setSelection', { rowIndx: 0, focus: true });
                $('html, body').scrollTop($(pq_grids[pq_index]).offset().top - 50);
                
                pq_index++;
                if(pq_index == pq_grids.length){
                    pq_index = 0;
                }
                e.preventDefault();
            }
        }
        if (e.which == 120) {

            if($('#billsModal').length && $('#billsModal').hasClass('show')){
              $('#save_bill_by_bill').trigger('click');
            }
            else if($('#ccModal').length && $('#ccModal').hasClass('show')){
              $('#save_cc').trigger('click');
            }
            else{
              $('#submitbtn').trigger('click');
              $('.swal2-confirm').focus();
            }
            
            e.preventDefault();
            e.stopPropagation();
        }

        if (key[16] && key[78]) { 
            bbbModel = $('#billsModal');
            if(bbbModel.length && bbbModel.hasClass('show')){
              $('#billsModal .nextBtn').trigger('click');
              e.preventDefault();
            }

            ccModel = $('#ccModal');
            if(ccModel.length && ccModel.hasClass('show')){
              $('#ccModal .nextBtn').trigger('click');
              e.preventDefault();
            }
        }
        if (key[16] && key[66]) {

            bbbModel = $('#billsModal');
            if(bbbModel.length && bbbModel.hasClass('show')){
              $('#billsModal .prevBtn').trigger('click');
              e.preventDefault();
            }

            ccModel = $('#ccModal');
            if(ccModel.length && ccModel.hasClass('show')){
              $('#ccModal .prevBtn').trigger('click');
              e.preventDefault();
            }
        }
        
    });
    $(document).keyup(function(e) {
        delete key[e.which];
    });

    function disabledEventPropagation(e){
    if(e){
      if(e.stopPropagation){
        e.stopPropagation();
      } else if(window.event){
        window.event.cancelBubble = true;
      }
    }
  }
</script>
<!--  Start Replica Voucher Modal -->
<script>
$(document).ready(function() {	
	$('#VoucherReplicaModal input[name="repltype"]').on('change', function(){ 
	   if(this.checked){  
		if($(this).val()=='daily'){
		 $("#daily_div").show();
		 $("#weekly_div").hide();
		 $("#monthly_div").hide();
		 $('#VoucherReplicaModal #dailystart').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo $fy_begndt;?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });
	$('#VoucherReplicaModal #dailyend').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo $fy_begndt;?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });
		}
		if($(this).val()=='weekly'){
		 $("#daily_div").hide();
		 $("#weekly_div").show();
		 $("#monthly_div").hide();
		 
		 $('#VoucherReplicaModal #weeklystart').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo $fy_begndt;?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });
	$('#VoucherReplicaModal #weeklyend').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo $fy_begndt;?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });
		 }
		 
		 if($(this).val()=='monthly'){
		 $("#daily_div").hide();
		 $("#weekly_div").hide();
		 $("#monthly_div").show();
		 
		 $('#VoucherReplicaModal #monthlystart').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo $fy_begndt;?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });
	$('#VoucherReplicaModal #monthlyend').datepicker({
        altFormat: "dd-mm-yyyy",
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        minDate:'<?php echo $fy_begndt;?>',
        maxDate:'<?php echo $fy_end;?>',

    }).on("change", function () {
        var date = $(this).val();
        if(!isValidDate(date)){
          $(this).val("");
        }
    });
		 }
		 
		 
       }			 
	});
	$('#VoucherReplicaModal').modal({backdrop: 'static', keyboard: false});
	
});
 $(document).on('click', '#replicavoucher', function(){
	voucher_type     = $(this).data('typeid');
	voucher_typename = $(this).data('type');
	$("#prgrloader").hide();
	$("#daily_div").show();
	$("#weekly_div").hide();
	$("#monthly_div").hide();
	$('#VoucherReplicaModal #ModalVoucherType').val(voucher_typename);
	$('#VoucherReplicaModal #vchfrm_type').val(voucher_type);
	$('#VoucherReplicaModal #vchfrm_txnid').val($(this).data('id'));	
	$('#VoucherReplicaModal .close_btn').text('Cancel');
    $('#VoucherReplicaModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	$('#VoucherReplicaModal .current_status').text('');
	$('#VoucherReplicaModal .progress-bar').css('width', '0%');
	$('#VoucherReplicaModal .progress-bar').text('0%');
	$('#VoucherReplicaModal').modal('show');	
 });
var get_details_xhr;
var list  = [];
var index = 0;
$(document).on('click', '#startreplica', function(){
    list = [];
	index = 0;
	$("#prgrloader").show();
	var repltype    = $("input[name='repltype']:checked").val();
	var vchtype     = $('#VoucherReplicaModal #vchfrm_type').val();
	var vchtxnid    = $('#VoucherReplicaModal #vchfrm_txnid').val();
	if(repltype=='daily'){
	 var startdate   = $('#VoucherReplicaModal #dailystart').val();
	 var enddate     = $('#VoucherReplicaModal #dailyend').val();
	 var dayname     = '';
	 var end_dayname = '';
	 var month_dayname_st = '';
	 var month_dayname_en = '';
	}
	if(repltype=='weekly'){
	 var startdate   = $('#VoucherReplicaModal #weeklystart').val();
	 var enddate     = $('#VoucherReplicaModal #weeklyend').val();
	 var dayname     = $('#VoucherReplicaModal #weeklystart_day').val();
	 var end_dayname = '';
	 var month_dayname_st = '';
	 var month_dayname_en = '';
	 
	}	
	if(repltype=='monthly'){
	 var startdate   = $('#VoucherReplicaModal #monthlystart').val();
	 var enddate     = $('#VoucherReplicaModal #monthlyend').val();
	 var dayname     = $('#VoucherReplicaModal #monthlystart_day').val();
	 var month_dayname_st = $('#VoucherReplicaModal #month_dayname_st').val();
	 var month_dayname_en = $('#VoucherReplicaModal #month_dayname_en').val();
	}
	var exclude_saturday = $("input[name='exclude_saturday']:checked").val();
	var exclude_sunday =$("input[name='exclude_sunday']:checked").val();
	
	get_details_xhr = $.ajax({
        url: baseurl+'admin/vouchers/replicate_vouchers', 
        type: 'POST',
        data: {"month_dayname_en":month_dayname_en,"month_dayname_st":month_dayname_st,"repltype":repltype,"dayname":dayname,"exclude_sunday":exclude_sunday,"exclude_saturday":exclude_saturday,"vchtype":vchtype,"vchtxnid": vchtxnid,"startdate":startdate,"enddate":enddate},
        dataType: "json",
        success: function (response) {            
            if(response.status){
                 list = response.list;
                var overall_status = 'Total Vouchers: ' + list.length;
                $('#VoucherReplicaModal .overall_status').text(overall_status);
                process_replication();
            }
            else{
            	$('#VoucherReplicaModal .overall_status').text('Something Went wrong');
            	$('#VoucherReplicaModal .current_status').text(response.message);
            }
        },
        complete: function() {
            // stop_loader();
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
            alert(error);
        },
    });
    
});
var update_replication_xhr;
function process_replication(){
 var vchtype  = $('#VoucherReplicaModal #vchfrm_type').val(); 
 var progress = Math.round((index/list.length)*100);
 $('#VoucherReplicaModal .progress-bar').css('width', progress+'%');
 $('#VoucherReplicaModal .progress-bar').text(progress+'%');
 if(index < list.length){
	var current_status = `${(index+1)} / ${list.length} (voucher no. ${list[index].name})`;
	$('#VoucherReplicaModal .current_status').text(current_status);
	update_replication_xhr = $.ajax({
	        url: baseurl+'admin/vouchers/add_repl_voucher_txn', 
	        type: 'POST',
	        data: {"vchno": vchtype, id: list[index].name,"vchtype": vchtype, id: list[index].id,'comptxn':list[index].comptxn,'voucher_date':list[index].voucher_date,'bo_id':list[index].bo_id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {	            
	            if(response.status){
	                index++;
	                process_replication();
	            }
	            else{
	            	alert(response.message);
	            }
	        },
	        complete: function() {
	            // stop_loader();
	        },
	        error: function (jqXHR, exception) {
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
	            alert(error);
	        },
	    });
	}		
	else{
        $('#VoucherReplicaModal .current_status').text("Voucher's Replicated Successfully.");
        $('#VoucherReplicaModal .close_btn').text('Done');
        $('#VoucherReplicaModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}	

</script>
<div class="modal fade" id="VoucherReplicaModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Replicate Voucher</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
		
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label">Voucher Type</label>
    <div class="col-sm-9">
      <input type="text" readonly class="form-control-plaintext" id="ModalVoucherType" value="email@example.com">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-3 col-form-label">Frequency</label>
    <div class="col-sm-9">
      <div class="row">
	<div class="col-md-3">
	  <div class="form-check">
		<input class="form-check-input" type="radio" name="repltype" id="repltype_daily" value="daily" checked>
		<label class="form-check-label" for="repltype_daily">
			Daily
		</label>
      </div>
	  </div>
	<div class="col-md-3">
	  <div class="form-check">
		<input class="form-check-input" type="radio" name="repltype" id="repltype_weekly" value="weekly">
		<label class="form-check-label" for="repltype_weekly">
			Weekly
		</label>
       </div>
	  </div>
	   <div class="col-md-3">
	  <div class="form-check">
		<input class="form-check-input" type="radio" name="repltype" id="repltype_monthly" value="monthly">
		<label class="form-check-label" for="repltype_monthly">
			Monthly
		</label>
</div>
	  </div>
	  
	  </div>
	
    </div>
  </div>
  
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-3 col-form-label">SPLIT</label>
    <div class="col-sm-9">
      <div class="row">
	<div class="col-md-3">
	  <div class="form-check">
		<input class="form-check-input" type="checkbox" name="repltype_split" id="repltype_split" value="split">
		<label class="form-check-label" for="repltype_split">
			
		</label>
      </div>
	  </div>
	  </div>	
    </div>
  </div>
   <div class="form-group row">
    <label for="inputPassword" class="col-sm-3 col-form-label">Basis</label>
    <div class="col-sm-9">
      <div class="row">
	<div class="col-md-3">
	  <div class="form-check">
		<input class="form-check-input" type="radio" name="repltype_basis" id="repltype_basis_fixed" value="fixed">
		<label class="form-check-label" for="repltype_basis_fixed">
			Fixed
		</label>
      </div>
	  </div>
	 <div class="col-md-3">
	  <div class="form-check">
		<input class="form-check-input" type="radio" name="repltype_basis" id="repltype_basis_range" value="range">
		<label class="form-check-label" for="repltype_basis_range">
			Range
		</label>
      </div>
	  </div> 
	  </div>	
    </div>
  </div>
   <input type="hidden" name="vchfrm_type" id="vchfrm_type" value="">
   <input type="hidden" name="vchfrm_txnid" id="vchfrm_txnid" value="">	     
    <div class="form-group row" id="daily_div">
	     <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">Start Date</label>
			<div class="col-sm-4">
			<input type="text" class="datepickernr form-control form-control-sm" readonly value="<?php echo date('d-m-Y');?>" id="dailystart">
			</div>
		</div>
		 <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">End Date</label>
			<div class="col-sm-4">
			<input type="text" class="datepickernr form-control form-control-sm" readonly value="<?php echo date('d-m-Y');?>" id="dailyend">
			</div>
		</div>
	  
	  </div>
	  
	  <div class="form-group row" id="weekly_div" style="display:none;">
	     <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">Start Date</label>
			<div class="col-sm-4">			
			<input type="text" class="datepickernr form-control form-control-sm" readonly value="<?php echo date('d-m-Y',strtotime('this Monday'));?>" id="weeklystart">
			</div>
		</div>
		 <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">End Date</label>
			<div class="col-sm-4">
			<input type="text" class="datepickernr form-control form-control-sm" readonly value="<?php echo date('d-m-Y',strtotime('next Sunday'));?>" id="weeklyend">
			</div>
		</div>
		 <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">Day</label>
			<div class="col-sm-4">
			<?php 
			$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
			?>
			<select id="weeklystart_day" class="form-control">
				<?php foreach($days as $day) { ?>	
				<option value="<?php echo $day;?>"><?php echo $day;?></option>
				<?php } ?>				
			</select>
			</div>
		</div>
	  
	  </div>
	  
	  <div class="form-group row" id="monthly_div" style="display:none;">
	     <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">Start Date</label>
			<div class="col-sm-4">
			<?php
			$monday    = strtotime('next Monday -1 week');
			$startweek = date('w', $monday)==date('w') ? strtotime(date("d-m-Y",$monday)." +7 days") : $monday;
			$lastweek  = strtotime(date("d-m-Y",$monday)." +6 days");

			?>
			<input type="text" class="datepickernr form-control form-control-sm" readonly value="<?php echo date('01-m-Y');?>" id="monthlystart">
			</div>
		</div>
		 <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">End Date</label>
			<div class="col-sm-4">
			<input type="text" class="datepickernr form-control form-control-sm" readonly value="<?php echo date('t-m-Y');?>" id="monthlyend">
			</div>
		</div>
		 <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label">Day Between</label>
			<div class="col-sm-4">
			 <div class="row">
			 <div class="col-md-5"><input type="number" class="form-control" min="1" max="30" value="" id="month_dayname_st">
			</div>
			<div class="col-md-2"><label  class="col-sm-2 col-form-label">TO</label></div>
			 <div class="col-md-5"><input type="number" class="form-control" min="1" max="30" value="" id="month_dayname_en">
			</div>
			 
			 </div>
			
			
			
			</div>
		</div>
	  
	  </div>
	  
 
 
 
 <div class="form-group row">
			<label for="dailystart" class="col-sm-2 col-form-label"></label>
			<div class="col-sm-4">
			 <div class="form-check">
		<input class="form-check-input" type="checkbox" name="exclude_saturday" id="exclude_saturday" value="saturday">
		<label class="form-check-label" for="exclude_saturday">
			Exclude Saturday
		</label>
</div>
			</div>
			<div class="col-sm-4">
			 <div class="form-check">
		<input class="form-check-input" type="checkbox" name="exclude_sunday" id="exclude_sunday" value="sunday">
		<label class="form-check-label" for="exclude_sunday">
			Exclude Sunday
		</label>
</div>
			</div>
		</div>	  
  <div class="form-group row"><br></div>

   <div class="form-group row" id="prgrloader" style="display:none;">
        <p class="overall_status">Total Accounts:0</p>
        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>
		<div class="text-center my-2 current_status" style="height:20px">			
		</div>
     </div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	    <button type="button" class="btn btn-success" id="startreplica">Replicate</button>
     
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>
<!--  End Replica Voucher Modal -->
<script>
function round(value, decimals) {
  var rspdata =  Number(Math.round(value+'e'+decimals)+'e-'+decimals); 
  return rspdata;
}

function parseAmountPrice(amount,decimal='')
{
	if(decimal=='')
		var decimal=2;
  if(typeof amount == 'string')
    amount = amount.replace(/,/g, '');

  if(amount == '' || amount == null || amount == undefined || isNaN(amount))
    return 0;
  var num = amount;
  num = round(num,decimal);
  return num;
}

function parseAmount(amount)
{
  if(typeof amount == 'string')
    amount = amount.replace(/,/g, '');

  if(amount == '' || amount == null || amount == undefined || isNaN(amount))
    return 0;

  var num = amount;
  num = parseFloat(num);
  num = Math.round(num * 100) / 100;
  num = num.toFixed(2);
  num = parseFloat(num);

  return num;
}

function formatAmount(amount,currency_symbol='',decimalval=''){
//if(amount>0)
// console.log("final amount =>"+amount+"===>"+decimalval);

	if(decimalval!='')
		var num = parseAmountPrice(amount,decimalval);
	else
     var num = parseAmount(amount);
 //if(amount>0)
// console.log("final price =>"+num);
    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
     
    var final_string = '0.00';
    if(num.includes("."))
    {
 
      var arr = num.split(".");
        var num1 = arr[0];
        var num2 = arr[1];
        
        var final1 = num1;
      if(num1.length > 3){
          var string = '';
          var last3String = num1.substring(num1.length-3, num1.length);
          var restString = num1.substring(0, num1.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final1 = string + last3String;
       }
       
       var final2 = num2;
       if(num2.length == 1){
          final2 = num2+'0';
       }

    final_string = final1 + '.' + final2;
    }
    else
    {
      final_string = num + '.00';
      if(num.length > 3){
          var string = '';
          var last3String = num.substring(num.length-3, num.length);
          var restString = num.substring(0, num.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final_string = string + last3String + '.00';
       }
    }
    
    
    // return '&#8377; ' + sign + final_string;
	if(currency_symbol!='')
    return currency_symbol + sign + final_string;
   else
	return '₹ ' + sign + final_string;   

}

function parseValue(amount)
{
  if(typeof amount == 'string')
    amount = amount.replace(/,/g, '');

  if(amount == '' || amount == null || amount == undefined || isNaN(amount))
    return 0;

  var num = amount;
  num = parseFloat(num);
  num = Math.round(num * 10000) / 10000;
  num = num.toFixed(4);
  num = parseFloat(num);

  return num;
}

function formatValue(amount)
{
  var num = parseValue(amount);

    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
     
    var final_string = '0.0000';
    if(num.includes("."))
    {
 
      var arr = num.split(".");
        var num1 = arr[0];
        var num2 = arr[1];
        
        var final1 = num1;
      if(num1.length > 3){
          var string = '';
          var last3String = num1.substring(num1.length-3, num1.length);
          var restString = num1.substring(0, num1.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final1 = string + last3String;
       }
       
       var final2 = '0000';
       if(num2.length == 1){
          final2 = num2+'000';
       }
       else if(num2.length == 2){
          final2 = num2+'00';
       }
       else if(num2.length == 3){
          final2 = num2+'0';
       }
       else{
          final2 = num2;
       }

    final_string = final1 + '.' + final2;
    }
    else
    {
      final_string = num + '.0000';
      if(num.length > 3){
          var string = '';
          var last3String = num.substring(num.length-3, num.length);
          var restString = num.substring(0, num.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final_string = string + last3String + '.0000';
       }
    }
    
    
    return '&#8377; ' + sign + final_string;

}

function parseQty(amount)
{
  if(typeof amount == 'string')
    amount = amount.replace(/,/g, '');

  if(amount == '' || amount == null || amount == undefined || isNaN(amount))
    return 0;

  var num = amount;
  num = parseFloat(num);
  // num = Math.round(num * 100) / 100;
  // num = num.toFixed(2);
  // num = parseFloat(num);

  return num;
}

function formatQty(amount)
{
  var num = parseAmount(amount);

    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
     
    var final_string = '0';
    if(num.includes("."))
    {
 
      var arr = num.split(".");
        var num1 = arr[0];
        var num2 = arr[1];
        
      var final1 = num1;
      if(num1.length > 3){
          var string = '';
          var last3String = num1.substring(num1.length-3, num1.length);
          var restString = num1.substring(0, num1.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final1 = string + last3String;
       }
       
       var final2 = num2;

      final_string = final1 + '.' + final2;
    }
    else
    {
      final_string = num;
      if(num.length > 3){
          var string = '';
          var last3String = num.substring(num.length-3, num.length);
          var restString = num.substring(0, num.length-3);

          restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

          for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
            if(i ==0)
              string += parseInt(restString.substr(o, 2))+',';
            else
              string += restString.substr(o, 2)+',';
          }
          final_string = string + last3String;
       }
    }
    
    
    return sign + final_string;
}

function formatForChart(amount)
{
  var num = parseAmount(amount);
  num = Math.round(num);

    var sign = '';
    if(num < 0){
        sign = '-';
        num = Math.abs(num);
    }
    num = num.toString();
    
    var final_string = num;
    if(num.length > 3){
        var string = '';
        var last3String = num.substring(num.length-3, num.length);
        var restString = num.substring(0, num.length-3);

        restString = ((restString.length)%2 == 1) ? '0'+restString : restString; //even

        for (let i = 0, o = 0; i < (restString.length / 2); ++i, o += 2) {
          if(i ==0)
            string += parseInt(restString.substr(o, 2))+',';
          else
            string += restString.substr(o, 2)+',';
        }
        final_string = string + last3String;
     }
    
    return sign + final_string;
}

var user_comp_list = <?= json_encode(user_company_list()) ?>;


$(document).on('keyup', '#searchCompaniesFromHeader', function(){
  
  var search = $(this).val();
  if(search != '')
  {
    var search_comp_list = user_comp_list.filter(function(obj){
      var string1 = obj.comp_name.toLowerCase();
      var string2 = obj.comp_code.toLowerCase();
      var text = search.toLowerCase();
       
      return string1.includes(text) || string2.includes(text);
    });
  }
  else{
    search_comp_list = user_comp_list;
  }
  
  
  var html = ``;
  $.each(search_comp_list, function(index, object){
    html += `
      <li class="nav-item">
        <a class="nav-link px-3 selcompany_link" data-comp_id="${object.comp_id}" data-comp_type="${object.comp_type}" href="javascript:void(0);">
          <h5>${object.comp_name}</h5>
          Organization: ${object.comp_code}
        </a>
      </li>
    `;
  });

  $('#searchCompaniesFromHeader_ul').html(html);
 
});

$(document).on("click",'.selbo_link',function(){
     $(".dropdown-menu").removeClass('show');	 
    var bo_id = $(this).data("id");
    
    load_bolink(bo_id);
    
     async function load_bolink(bo_id) {
        show_loader(); 
         $(".siteloader").show();
           let start_time = performance.now();
        
          var interval   = setInterval(function(){
                var currenttime  = performance.now();
                var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
                 $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
                 $('#progressbar').attr('aria-valuenow',pct);
                 $('#progressbar .progress-bar').css("width", pct+"%");
                 $('#progressbar .progress-bar').text(pct+"%");
              
          }, 1000); 
          
       
         let response = await fetch(baseurl+'admin/choose_branch/'+bo_id);
            
            let result = await response.json();
            requestTime = performance.now();
           if(result){
            clearInterval(interval);
            $('#progressbar').css("width","100%");
            $('#progressbar .progress-bar').css("width", "100%");
            $('#progressbar .progress-bar').text("100%");
           window.open(baseurl+'admin/dashboard', '_self').focus();
           } 
       }
  });
  
  



  $(document).on("click",'.selcompany_link',function(){
	 /*  $(".dropdown-menu").removeClass('show');	
	if($(this).is('[disabled=disabled]')){
		return false;
	} */
	var comp_id = $(this).data("comp_id");
    var comp_type = $(this).data("comp_type");
    if(comp_type == 'owner' || comp_type == 'shared')
      load_companylink(comp_id);

    if(comp_type == 'grp')
      load_group_companylink(comp_id);
    
     
  });

  async function load_companylink(company_id) {
      show_loader();
       $(".siteloader").show();
         let start_time = performance.now();
      
        var interval   = setInterval(function(){
              var currenttime  = performance.now();
              var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
               $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
               $('#progressbar').attr('aria-valuenow',pct);
               $('#progressbar .progress-bar').css("width", pct+"%");
               $('#progressbar .progress-bar').text(pct+"%");
            
        }, 1000); 
        
     
       let response = await fetch(baseurl+'admin/choose_company/'+company_id);
          
          let result = await response.json();
          requestTime = performance.now();
         if(result){
          clearInterval(interval);
          $('#progressbar').css("width","100%");
          $('#progressbar .progress-bar').css("width", "100%");
          $('#progressbar .progress-bar').text("100%");
          window.open(baseurl+'admin/dashboard', '_self').focus();
         } 
  }

  async function load_group_companylink(company_id) {
    show_loader();
       $(".siteloader").show();
         let start_time = performance.now();
      
        var interval   = setInterval(function(){
              var currenttime  = performance.now();
              var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
               $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
               $('#progressbar').attr('aria-valuenow',pct);
               $('#progressbar .progress-bar').css("width", pct+"%");
               $('#progressbar .progress-bar').text(pct+"%");
            
        }, 1000); 
        
     
       let response = await fetch(baseurl+'groupcompany/select_company/'+company_id+'/0');
          
          let result = await response.json();
          requestTime = performance.now();
         if(result){
          clearInterval(interval);
          $('#progressbar').css("width","100%");
          $('#progressbar .progress-bar').css("width", "100%");
          $('#progressbar .progress-bar').text("100%");
           window.open(baseurl+'grpcomp/dashboard', '_self').focus();
         } 
  }


    
    async function load_txn_history(vchrdate,vchrtypeid) {
       let response = await fetch('<?php echo base_url();?>admin/reports/load_vouher_txn_history/'+vchrdate+'/'+vchrtypeid);
      let result = await response.json();

      if(result)
      {
        
        if($("#voucher_txn_grid").pqGrid('instance')){       
          $("#voucher_txn_grid").pqGrid('refresh');
          $("#voucher_txn_grid").pqGrid('option', 'dataModel.data', result.data);
          $("#voucher_txn_grid").pqGrid('refreshDataAndView');
        }
        else{
          $("#voucher_txn_grid").pqGrid(historyObj);
          $("#voucher_txn_grid").pqGrid('option', 'dataModel.data', result.data);
          $("#voucher_txn_grid").pqGrid('refreshDataAndView');
        }
      }
    
}

$(document).on("click","#voucher_txn_btn",function(){
    
    var vchrdate = $(this).data("vchrdate")
     var vchrtypeid = $(this).data("vchrid"); 
    
     $("#voucher_txn_btn button").removeClass("active");
      $("#allvoucher_txn_btn button").removeClass("active");
      
      
       $("#voucher_txn_btn button").addClass("active");
       
       
      load_txn_history(vchrdate,vchrtypeid);
      
      
      
});


$(document).on("click","#allvoucher_txn_btn",function(){
    
    var vchrdate = $(this).data("vchrdate");
     var vchrtypeid = '0'; 
    
    
    $("#voucher_txn_btn button").removeClass("active");
      $("#allvoucher_txn_btn button").removeClass("active");
      
      
       $("#allvoucher_txn_btn button").addClass("active");
     
      load_txn_history(vchrdate,vchrtypeid);
      
      
      
});




$(document).on("click",".open_voucher_txn_history",function(){
    
    $("#voucher_txn_historyModal").modal('show');
    var voucher_date = $("#voucher_date").val();
     var sale_date = $("#sale_date").val();
      var purchase_date = $("#purchase_date").val();
      var production_date = $("#production_date").val();
  
    if (typeof voucher_date === "undefined" && typeof sale_date !== "undefined") {
        var vchrdate = $("#sale_date").val();
        }
   else if (typeof voucher_date === "undefined" && typeof purchase_date !== "undefined") {
        var vchrdate = $("#purchase_date").val();
        }
else if (typeof voucher_date === "undefined" && typeof production_date !== "undefined") {
        var vchrdate = $("#production_date").val();
        }        
    else
    var vchrdate = $("#voucher_date").val();
    
    
     var vchrtypeid = $(this).data("id"); 
     
      $("#voucher_txn_btn button").removeClass("active");
      $("#allvoucher_txn_btn button").removeClass("active");
      
      
       $("#voucher_txn_btn button").addClass("active");
    
     $("#voucher_txn_btn").attr({"data-vchrid":vchrtypeid,"data-vchrdate":vchrdate});    
     $("#allvoucher_txn_btn").attr({"data-vchrid":vchrtypeid,"data-vchrdate":vchrdate});  
    
   
    
    load_txn_history(vchrdate,vchrtypeid);
    
    
})


var jsonvchr = [];
for(var i=0;i<2;i++){
    jsonvchr.push({'date': '', 'particulars': '', 'voucher_type': '', 'voucher_no': '', 'debit': '', 'credit': ''});
}

var colModel = [
        { title: "DATE", align:"left", width: 180,   dataIndx: "date" },
        { title: "PARTICULARS", align:"left", width: 180,   dataIndx: "particulars" },
        { title: "VOUCHER TYPE", align:"left", width: 180,   dataIndx: "voucher_type" },
        { title: "VOUCHER NO.", align:"left", width: 180,   dataIndx: "voucher_no" },
        { title: "DEBIT", align:"right", width: 180,   dataIndx: "debit" },
        { title: "CREDIT", align:"right", width: 180,   dataIndx: "credit" },
    ];
var vchr_dataModel = {"data":jsonvchr} 
var historyObj = {
      scrollModel: { autoFit: true },
      height: 'flex',
      collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
      selectionModel: { type: 'row',mode:'single' },
      dataModel: vchr_dataModel,
      colModel : colModel,
      editable: false,
      numberCell: { show: false },
      wrap:false,
      showTitle: false,
      create: function (evt, ui) {// make first row auto selected
            var grid = this,
              $select_row = $(".select-row"),
              data = ui.dataModel.data;
              grid.setSelection({ rowIndx: 0, focus: true });
      },
     
  };

  historyObj.rowDblClick = function(event, ui) {
    var rowData             = ui.rowData;
    var col_type            = rowData.vch_type;
    var ajax                = rowData.ajax;
    var voucher_txn_id      = rowData.voucher_txn_id;
    var voucher_type_id     = rowData.voucher_type_id;
    var bom_id            = rowData.bom_id;
    var bom_batches       = rowData.bom_batches;
    if(voucher_type_id=='18')
      window.location.href= baseurl+'admin/sales/edit/'+voucher_txn_id;
    else if(voucher_type_id=='11')
      window.location.href= baseurl+'admin/purchase/edit/'+voucher_txn_id; 
    else if(voucher_type_id=='10')
      window.location.href= baseurl+'admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id;
    else if(voucher_type_id=='14')
      window.location.href= baseurl+'admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
    else if(voucher_type_id=='6')
      window.location.href= baseurl+'admin/inward_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='7')
      window.location.href= baseurl+'admin/delivery_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='2')
      window.location.href= baseurl+'admin/credit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='3')
      window.location.href= baseurl+'admin/debit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='12')
      window.location.href= baseurl+'admin/purchase_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='15')
      window.location.href= baseurl+'admin/stock_transfer/edit/'+voucher_txn_id;
    else if(voucher_type_id=='17')
      window.location.href= baseurl+'admin/quotations/edit/'+voucher_txn_id
    else if(voucher_type_id=='19')
      window.location.href= baseurl+'admin/sales_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='21')
      window.location.href= baseurl+'admin/purchase_requisition/edit/'+voucher_txn_id;
    else if(voucher_type_id=='20')
      window.location.href= baseurl+'admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;

    else if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
      window.location.href= baseurl+'admin/vouchers/edit/'+voucher_txn_id;

    else if(voucher_type_id=='8')
      window.location.href= baseurl+'admin/memorandum/edit/'+voucher_txn_id;

  }
       
  historyObj.cellKeyDown = function(evt, ui) {
    var rowData           = ui.rowData;
    var ajax              = rowData.ajax;
    var col_type          = rowData.col_type;
    var voucher_txn_id    = rowData.voucher_txn_id;
    var voucher_type_id   = rowData.voucher_type_id;
    var bom_id            = rowData.bom_id;
    var bom_batches       = rowData.bom_batches;
    if (evt.keyCode==13){

    if(voucher_type_id=='18')
      window.location.href= baseurl+'admin/sales/edit/'+voucher_txn_id;
    else if(voucher_type_id=='11')
      window.location.href= baseurl+'admin/purchase/edit/'+voucher_txn_id;  
    else if(voucher_type_id=='10')
      window.location.href= baseurl+'admin/physical_verification/edit/'+voucher_txn_id+"/"+voucher_type_id;
    else if(voucher_type_id=='14')
      window.location.href= baseurl+'admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
    else if(voucher_type_id=='6')
      window.location.href= baseurl+'admin/inward_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='7')
      window.location.href= baseurl+'admin/delivery_challan/edit/'+voucher_txn_id;
    else if(voucher_type_id=='2')
      window.location.href= baseurl+'admin/credit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='3')
      window.location.href= baseurl+'admin/debit_note/edit/'+voucher_txn_id;
    else if(voucher_type_id=='12')
      window.location.href= baseurl+'admin/purchase_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='15')
      window.location.href= baseurl+'admin/stock_transfer/edit/'+voucher_txn_id;
    else if(voucher_type_id=='17')
      window.location.href= baseurl+'admin/quotations/edit/'+voucher_txn_id;
    else if(voucher_type_id=='19')
      window.location.href= baseurl+'admin/sales_order/edit/'+voucher_txn_id;
    else if(voucher_type_id=='21')
      window.location.href= baseurl+'admin/purchase_requisition/edit/'+voucher_txn_id;
    else if(voucher_type_id=='20')
      window.location.href= baseurl+'admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;

    else  if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
      window.location.href= baseurl+'admin/vouchers/edit/'+voucher_txn_id;

    else if(voucher_type_id=='8')
      window.location.href= baseurl+'admin/memorandum/edit/'+voucher_txn_id;
    
    }

  }

  $('#refresh_grid').on('click', function(){

      var pq_grids = $('.pq-grid'); 
      if(pq_grids.length > 0){
        $.each(pq_grids, function(index, pq_grid){

          var grid_id = $(pq_grid).attr('id');
          localStorage.removeItem("pq-grid"+grid_id);
   
          $(pq_grid).pqGrid( "reset", { group: true, filter: true, sort: true } );

            var CM = $(pq_grid).pqGrid('option', 'colModel');
            for(var i=0, len = CM.length; i < len; i++){
                var column = CM[i];
                if(column.filter){
                    column.filter.value = null;
                    column.filter.value2 = null;
                    column.filter.cache = null;
                }
            }
            
            $(pq_grid).pqGrid('filter', {
              oper: 'replace',
              data: []
            });
            $('.filterValue').val('');
            $(pq_grid).pqGrid('refreshHeader');

            $(pq_grid).pqGrid( "setSelection", { rowIndx: 0 });

        })
      }

      $('#trash').text('Enable Trash Mode'); 
      $('#trash').data('type', '0');
      if ( typeof hide_trash_checkbox  === 'function') {
        hide_trash_checkbox();
      }
  })

  function edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx='0',is_duplicate='0')
  {
    if(voucher_type_id=='18'){
	  if(is_duplicate>'0')	
      window.location.href= baseurl+'admin/sales/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
	  window.location.href= baseurl+'admin/sales/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
       	  
	}
    else if(voucher_type_id=='11'){
	 if(is_duplicate>'0')		
	 window.location.href= baseurl+'admin/purchase/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1'; 
     else
	 window.location.href= baseurl+'admin/purchase/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx; 
     	 
	}
	
	else if(voucher_type_id=='10')
      window.location.href= baseurl+'admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='14')
      window.location.href= baseurl+'admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id+'&rowIndx='+select_rowindx;
    else if(voucher_type_id=='6')
      window.location.href= baseurl+'admin/inward_challan/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='7')
      window.location.href= baseurl+'admin/delivery_challan/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='2'){
	  if(is_duplicate>'0')	
      window.location.href= baseurl+'admin/credit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
	   window.location.href= baseurl+'admin/credit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;	  
  
	}
    else if(voucher_type_id=='3'){
    if(is_duplicate>'0')     
	 window.location.href= baseurl+'admin/debit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
    else
	 window.location.href= baseurl+'admin/debit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	
	}
	else if(voucher_type_id=='12'){
	 if(is_duplicate>'0')    	
      window.location.href= baseurl+'admin/purchase_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
     else
	  window.location.href= baseurl+'admin/purchase_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	 
	}
	
	else if(voucher_type_id=='15')
      window.location.href= baseurl+'admin/stock_transfer/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='16')
      window.location.href= baseurl+'admin/vouchers/reverse_journal/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='17'){
	if(is_duplicate>'0')	
      window.location.href= baseurl+'admin/quotations/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
    else
	  window.location.href= baseurl+'admin/quotations/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	
	
	}
	else if(voucher_type_id=='19'){
	  if(is_duplicate>'0') 	
      window.location.href= baseurl+'admin/sales_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
     else
       window.location.href= baseurl+'admin/sales_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
			 
	}

   else if(voucher_type_id=='21')
      window.location.href= baseurl+'admin/purchase_requisition/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='20')
      window.location.href= baseurl+'admin/stock_journal/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;

    else if(voucher_type_id=='1'  || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13' || voucher_type_id=='22')
    {
	  if(is_duplicate>'0')	
		window.location.href= baseurl+'admin/vouchers/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
		window.location.href= baseurl+'admin/vouchers/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
  
    
	}	
	
	
	else if(voucher_type_id=='8')
     window.location.href= baseurl+'admin/memorandum/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
  }
  
  function urlSafeBase64(str) {
    return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}
  
  function edit_draft_voucher(voucher_txn_id, voucher_type_id,select_rowindx='0',is_duplicate='0')
  {
    /* if(voucher_type_id=='18'){
	  if(is_duplicate>'0')	
      window.location.href= baseurl+'admin/sales/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
	  window.location.href= baseurl+'admin/sales/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
       	  
	}
    else if(voucher_type_id=='11'){
	 if(is_duplicate>'0')		
	 window.location.href= baseurl+'admin/purchase/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1'; 
     else
	 window.location.href= baseurl+'admin/purchase/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx; 
     	 
	}
	
	else if(voucher_type_id=='10')
      window.location.href= baseurl+'admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='14')
      window.location.href= baseurl+'admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id+'&rowIndx='+select_rowindx;
    else if(voucher_type_id=='6')
      window.location.href= baseurl+'admin/inward_challan/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='7')
      window.location.href= baseurl+'admin/delivery_challan/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='2'){
	  if(is_duplicate>'0')	
      window.location.href= baseurl+'admin/credit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
	   window.location.href= baseurl+'admin/credit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;	  
  
	}
    else if(voucher_type_id=='3'){
    if(is_duplicate>'0')     
	 window.location.href= baseurl+'admin/debit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
    else
	 window.location.href= baseurl+'admin/debit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	
	}
	else if(voucher_type_id=='12'){
	 if(is_duplicate>'0')    	
      window.location.href= baseurl+'admin/purchase_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
     else
	  window.location.href= baseurl+'admin/purchase_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	 
	}
	
	else if(voucher_type_id=='15')
      window.location.href= baseurl+'admin/stock_transfer/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='16')
      window.location.href= baseurl+'admin/vouchers/reverse_journal/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='17'){
	if(is_duplicate>'0')	
      window.location.href= baseurl+'admin/quotations/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
    else
	  window.location.href= baseurl+'admin/quotations/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	
	
	}
	else if(voucher_type_id=='19'){
	  if(is_duplicate>'0') 	
      window.location.href= baseurl+'admin/sales_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
     else
       window.location.href= baseurl+'admin/sales_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
			 
	}

   else if(voucher_type_id=='21')
      window.location.href= baseurl+'admin/purchase_requisition/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='20')
      window.location.href= baseurl+'admin/stock_journal/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
 */
     if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13' || voucher_type_id=='22')
    {
	  if(is_duplicate>'0')	
		window.location.href= baseurl+'admin/vouchers/draft_edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
		window.location.href= baseurl+'admin/vouchers/draft_edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
  
    
	}	
	
	
	//else if(voucher_type_id=='8')
     // window.location.href= baseurl+'admin/memorandum/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
  }
  
  function get_edit_voucher_url(voucher_txn_id, voucher_type_id,select_rowindx='0',is_duplicate='0')
  {
	  var pgurl='';
    if(voucher_type_id=='18'){
	  if(is_duplicate>'0')	
      pgurl= baseurl+'admin/sales/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
	 pgurl= baseurl+'admin/sales/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
       	  
	}
    else if(voucher_type_id=='11'){
	 if(is_duplicate>'0')		
	 wpgurl= baseurl+'admin/purchase/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1'; 
     else
	 pgurl= baseurl+'admin/purchase/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx; 
     	 
	}
	
	else if(voucher_type_id=='10')
      pgurl= baseurl+'admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='14')
      pgurl= baseurl+'admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id+'&rowIndx='+select_rowindx;
    else if(voucher_type_id=='6')
      pgurl= baseurl+'admin/inward_challan/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='7')
      pgurl= baseurl+'admin/delivery_challan/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='2'){
	  if(is_duplicate>'0')	
      pgurl= baseurl+'admin/credit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
	  pgurl= baseurl+'admin/credit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;	  
  
	}
    else if(voucher_type_id=='3'){
    if(is_duplicate>'0')     
	 pgurl= baseurl+'admin/debit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
    else
	 pgurl= baseurl+'admin/debit_note/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	
	}
	else if(voucher_type_id=='12'){
	 if(is_duplicate>'0')    	
      pgurl= baseurl+'admin/purchase_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
     else
	  pgurl= baseurl+'admin/purchase_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	 
	}
	
	else if(voucher_type_id=='15')
      pgurl= baseurl+'admin/stock_transfer/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='16')
      pgurl= baseurl+'admin/vouchers/reverse_journal/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='17'){
	if(is_duplicate>'0')	
      pgurl= baseurl+'admin/quotations/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
    else
	  pgurl= baseurl+'admin/quotations/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    	
	
	}
	else if(voucher_type_id=='19'){
	  if(is_duplicate>'0') 	
      pgurl= baseurl+'admin/sales_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
     else
       pgurl= baseurl+'admin/sales_order/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
			 
	}

   else if(voucher_type_id=='21')
      pgurl= baseurl+'admin/purchase_requisition/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
    else if(voucher_type_id=='20')
      pgurl= baseurl+'admin/stock_journal/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;

    else if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13' || voucher_type_id=='22')
    {
	  if(is_duplicate>'0')	
		pgurl= baseurl+'admin/vouchers/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx+'&duplc=1';
      else
		pgurl= baseurl+'admin/vouchers/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
  
    
	}	
	
	
	else if(voucher_type_id=='8')
      pgurl= baseurl+'admin/memorandum/edit/'+voucher_txn_id+'/?rowIndx='+select_rowindx;
  
   return pgurl;
  }
  
  
  $(document).on('click', '.submenu a', function(){
    show_loader();
    abort_all_ajax();

  });

  
  $(document).on('click', '#tabslistings span', function(){
    show_loader();
    abort_all_ajax();

  });
       
  function abort_all_ajax()
  {
    // $.each(xhrObjJson, function(index,object){
    //   object.abort();
    // })
  }



</script>

<script>

  function filePreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#logopreview').html('<img src="'+e.target.result+'" width="250" height="250"/>');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

  $(document).on('submit', '#logo_form2', function(event){
    event.preventDefault();

    var form = $(this);
    var formData = new FormData(form[0]);
    
    $.ajax({
      url : form.attr('action'),
      type : 'POST',
      data : formData,
      dataType : 'json',
      processData: false,
      contentType: false,
      beforeSend: function() {
          show_loader();
          $('#logo_form_btn2').attr('disabled', 'disabled');
          $('#logo_file_validation2').html('');
      },
      success : function(response) {
        if(response.status){
          alert_success("Logo updated successfully");
		  $("#logoModal2").modal("hide");
          $('#logo_form2').trigger("reset");
          $('#logo').attr('src', '<?= base_url() ?>admin/company/logo');
          $('#logo2').attr('src', '<?= base_url() ?>admin/company/logo');
          $('#comp_logo_sidebar').attr('src', '<?= base_url() ?>admin/company/logo');
        }
        else{
          alert(response.message);
          
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
            $('#logo_file_validation2').html(html);
          }  
        }
      },
      complete: function() {
        stop_loader();
        $('#logo_form_btn2').attr('disabled', false);
      },

    });
  });
  
  $(document).on("click","#other_bo_voucher",function(){
     
	  $("#otherbovoucher_model").modal("show");
	  $(this).hide();
	  $("#clear_other_bo_voucher").show();
  });
  $(document).on("click","#clear_other_bo_voucher",function(){
	  $(this).hide();
	  $("#other_bo_voucher").show();
	  
	  $('input[name=VchOthrBo]').attr('checked', false);
	  $('input[name=VchOthrBo]').prop('checked', false);
	  $('input[name=VchOthrBo]').removeAttr("checked");
	  $(".otherbotext_voucher").remove();
	   $("#otherbovoucher_model").modal("hide");
  });
  $(document).on("click","#otherbovoucher_model #save_other_bo_voucher",function(){
	  
	  if( $('input[name=VchOthrBo]:radio:checked').length > 0 ) {
		   $("#otherbovoucher_model").modal("hide");
        var VchOthrBoVal= $('input[name=VchOthrBo]:radio:checked').val();
		var VchOthrBoText= $('input[name=VchOthrBo]:radio:checked').data("name");
		
		if ($('.otherbotext_voucher').length === 0) {
		 $(".order-1").find("h3").append('<span class="otherbotext_voucher" style="float:right;font-size:15px;font-weight:normal;">Voucher is marked for '+VchOthrBoText+'</span>');	
		}else{
		$(".otherbotext_voucher").remove();
			$(".pb-2>.order-1").find("h3").append('<span class="otherbotext_voucher" style="float:right;font-size:15px;font-weight:normal;">Voucher is marked for '+VchOthrBoText+'</span>');
     	
			
		}
		
		}
    else {
		$("#otherbovoucher_model").modal("hide");
		alert_notification("Choose branch first.");
		
        var VchOthrBoVal = 0;
		var VchOthrBoText='';
    }
	
	
  });
  
  $(document).on("click",".confirmbofirst",function(){
	var current_bo='<?php echo $bo_name = $local_session->get('ses_boname');?>';
	var migrate_bo_name= $(this).data("name");
	var migrate_bo_id= $(this).data("id");
	var vchtxn_id = $(this).data("ajax");
	Swal.fire({
        title: 'Are you sure?',
        text: "you want to migrate this voucher from "+current_bo+" to  "+migrate_bo_name,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Ok',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
           if (result.value) {
				$.ajax({
              url: '<?php echo base_url(); ?>/admin/ajax/migrate_voucher_bo', 
              type: 'POST',
              data: {"vchtxn_id": vchtxn_id, "migrate_to_bo":migrate_bo_id},
              dataType: "json",
              beforeSend: function() {
                  show_loader();
              },
              success: function (response) {
                
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  if(response.status){
					  alert_success("Voucher migrated to branch "+migrate_bo_name);
                    window.setTimeout(function(){
							history.back();
					}, 5000);

					
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
	     });
});

function UserfilePreview(input) {
    if (!input.files || !input.files.length) return;   // nothing chosen

    const file      = input.files[0];
    const mimeOk    = ['image/jpeg', 'image/png'];     // allow-list
    /*  ⬇️  fall back to extension if browser doesn’t set MIME type   */
    const extOk     = ['jpg','jpeg','png'];
    const ext       = file.name.split('.').pop().toLowerCase();

    if ( !mimeOk.includes(file.type) && !extOk.includes(ext) ) {
        alert('Please choose a valid image file (jpg, png).');

        input.value = '';              // let the user pick the same name again
        return;
    }

     /* ─── load into memory ─── */
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {

            if (this.naturalWidth !== 150 || this.naturalHeight !== 150) {
                alert('Image must be exactly 150 × 150 pixels.');
                input.value = '';                
                return;
            }

            $('#userphotopreview').html(
                '<img src="'+ e.target.result +'" width="150" height="150" alt="Preview">'
            );
        };
        img.src = e.target.result;   // triggers img.onload
    };
    reader.readAsDataURL(file);
}

  $(document).on('submit', '#userlogo_form2', function(event){
    event.preventDefault();

    var form = $(this);
    var formData = new FormData(form[0]);
    
    $.ajax({
      url : form.attr('action'),
      type : 'POST',
      data : formData,
      dataType : 'json',
      processData: false,
      contentType: false,
      beforeSend: function() {
          show_loader();
          $('#user_logo_form_btn2').attr('disabled', 'disabled');
          $('#userlogo_file_validation2').html('');
      },
      success : function(response) {
        if(response.status){
          alert_success("Photo updated successfully");
		  $("#userphotoModal2").modal("hide");
          $('#user_logo_form2').trigger("reset");
          $('#user_logo_top').attr('src', '<?= base_url() ?>user/logo');
          $('#user_logo_sidebar').attr('src', '<?= base_url() ?>user/logo');
        }
        else{
          alert(response.message);
          
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
            $('#userlogo_file_validation2').html(html);
          }  
        }
      },
      complete: function() {
        stop_loader();
        $('#user_logo_form_btn2').attr('disabled', false);
      },

    });
  });
  $(document).on("click",".alert-error-custom .btn-close",function(){
	 $("#validation_errors").hide();
	  $(".alert-error-custom").hide();
 });
 
 $("#search-box").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "<?php echo base_url();?>admin/ajax/accounts_search",
                type: "GET",
                dataType: "json",
                data: {
                    term: request.term // send input value
                },
                success: function(data) {
                    response(data); // pass the result to autocomplete
                }
            });
        },
        minLength: 2, // start autocomplete after 2 characters
        select: function(event, ui) {
            // Optional: do something when item selected
           // console.log("Selected ID:", ui.item.id);
           // console.log("Selected Name:", ui.item.label);
            // Optionally store ID in a hidden field
            $('input[name="id"]').val(ui.item.id);
        }
    });
</script>


<?php if(comp()->id){ ?>
 <!-- The Modal -->
<div class="modal" id="logoModal2">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Logo</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

        <div class="row p-2">
          <div class="col-md-12 text-center" id="logopreview">
            <img id="logo2" src="<?= base_url().'/admin/company/logo/' ?>" alt="No Logo"  onerror="this.onerror=null;this.src='<?= base_url() ?>public/assets/img/default_logo.png';" style="width: 250px; height: 250px;">
          </div>
        </div>

        <div id="logo_file_validation2"></div>

        

        <form id="logo_form2" action="<?= base_url() ?>admin/company/upload_file" method="post" enctype="multipart/form-data">
          <input type="hidden" name="comp_id" value="<?= comp()->id ?>">
          <input type="file" id="logo_file" name="logo_file" onChange="filePreview(this)" class="form-control" accept="image/png, image/jpeg" required>
          
        </form>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button form="logo_form2" id="logo_form_btn2" class="btn btn-sm btn-success" type="submit">Upload</button>
      </div>

    </div>
  </div>
</div>

<?php } ?>

<!-- List View The Modal Starts here -->
<div class="modal" id="voucher_txn_historyModal">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Voucher Transactions</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <div class="row">
        <div class="col-12 col-xxl-8">
               <a href="javascript:void(0);" id="voucher_txn_btn" data-vchrdate="" data-vchrid=""><button type="button" class="btn btn-outline-success active " fdprocessedid="cx3o3a">Recent Transactions</button></a>
                    <a href="javascript:void(0);" id="allvoucher_txn_btn" data-vchrdate="" data-vchrid=""><button type="button" class="btn btn-outline-success" fdprocessedid="cev8o">All Voucher Transactions</button></a>
                      </div>
        </div> 
        <br>
       <div id="voucher_txn_grid"></div>
      
      </div>

     

    </div>
  </div>
</div>



<div class="modal fade" id="comingSoonModal" tabindex="-1" aria-labelledby="comingSoonLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <h5 class="modal-title" id="comingSoonLabel">🚧 Coming Soon</h5>
        <p>This feature is under construction.</p>
        <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
