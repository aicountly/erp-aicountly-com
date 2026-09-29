<?php $header = array( 	'title' => 'Rewrite Books' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.myform .col-12{padding:6px 0px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}
</style>
<div class="row">
	<div class="col-6">
		<h3 class="pb-3">Rewrite Books</h3>
	</div>
	<div class="col-md-6 text-end">
		<div class="taskmenus">
			<a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
				<span class="material-symbols-outlined">offline_bolt</span>
			</a>
			<a href="#">
				<span class="material-symbols-outlined">print</span>
			</a>
			<a href="#" data-bs-toggle="dropdown" aria-expanded="false">
				<span class="material-symbols-outlined">
					<span class="material-symbols-outlined">download</span>
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
				<span class="material-symbols-outlined">share</span>
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
		</div>
	</div>
	<div class="col-6">
		

	</div>
	<div class="col-6 text-end">
		<a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-outline-success btn-sm">« Back</a>
	</div>
</div>

<h4>Update Balances</h4>
<div class="row m-2">
	<button data-type="account" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Account Balances</button>
	<button data-type="bill_sundry" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Bill Sundry Balances</button>
	<button data-type="bill_by_bill" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Bill By Bill Balances</button>
	<button data-type="cost_center" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Cost Centre Balances</button>
	<button data-type="item" class="btn btn-success btn-sm mx-2 update_balance" style="max-width: 200px">Item Balances</button>
</div>
<br>
<h4>Diagnostic Tools</h4>
<div class="row m-2">
	<button id="voucher_verification" class="btn btn-success btn-sm mx-2" style="max-width: 200px">Voucher Verification</button>

	<button id="bbb_voucher_verification" class="btn btn-success btn-sm mx-2" style="max-width: 200px">Bill By Bill Voucher Verification</button>

	<button id="db_repair" class="btn btn-success btn-sm mx-2" style="max-width: 200px">Repair Database</button>
</div>


<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bbbVoucherVerificationModal">
  Open modal
</button> -->

<!-- The Modal -->
<div class="modal fade" id="updateBalancesModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Updating Account Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Capital Account)
		</div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="voucherVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Voucher Verification</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Voucher Types: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sales)
		</div>

		<div>
			<p>Faults: <span id="faults">0</span></p>
			<ul class="list-group voucher_list" id="voucher_verification_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>	

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="bbbVoucherVerificationModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Bill By Bill Voucher Verification</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Accounts: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-main" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25 (Sundry Creditors)
		</div>

		<p class="overall_sub_status">Total Vouchers: 215</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated progress-bar-sub" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_sub_status" style="height:20px">
			101 / 215 (Sales 22)
		</div>

		<div>
			<p>Faults: <span id="bbb_faults">0</span></p>
			<ul class="list-group voucher_list" id="bbb_voucher_verification_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>	

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="dbRepairModal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Database Repair</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

      	<p class="overall_status">Total Tables: 25</p>

        <div class="progress" style="height:20px">
		  <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width:50%">50%</div>
		</div>

		<div class="text-center my-2 current_status" style="height:20px">
			10 / 25
		</div>

		<div>
			<p>Faults: <span id="faults">0</span></p>
			<ul class="list-group" id="db_repair_list" style="max-height: 200px;overflow-y: auto">
			  
			</ul>
		</div>	

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger close_btn" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>
<script src="<?php echo base_url();?>/public/js/jquery.progressBarTimer.js" type="text/javascript" charset="utf-8">
</script>

<script>



$(document).ready(function() {
	$('#updateBalancesModal').modal({backdrop: 'static', keyboard: false});
});

var list = [];
var index = 0;
var type = '';

function get_modal_title(type)
{
	var title = '';
	if(type == 'account')
		title = 'Updating Account Balances';
	if(type == 'bill_sundry')
		title = 'Updating Bill Sundry Balances';
	if(type == 'bill_by_bill')
		title = 'Updating Bill By Bill Balances';
	if(type == 'cost_center')
		title = 'Updating Cost Center Balances';
	if(type == 'item')
		title = 'Updating Item Balances';

	return title;
}

var get_details_xhr;
$(document).on('click', '.update_balance', function(){
	type = $(this).data('type');
	list = [];
	index = 0;

	$('#updateBalancesModal .modal-title').text(get_modal_title(type));

	$('#updateBalancesModal .close_btn').text('Cancel');
    $('#updateBalancesModal .close_btn').removeClass('btn-success').addClass('btn-danger');

	$('#updateBalancesModal .overall_status').text('Please wait while details are being fetched...');
	$('#updateBalancesModal .current_status').text('');

	$('#updateBalancesModal .progress-bar').css('width', '0%');
	$('#updateBalancesModal .progress-bar').text('0%');

	$('#updateBalancesModal').modal('show');

	get_details_xhr = $.ajax({
        url: '/admin/rewritebooks/load_details', 
        type: 'POST',
        data: {type: type},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                list = response.list;
                var overall_status = 'Total Accounts: ' + list.length;
                $('#updateBalancesModal .overall_status').text(overall_status);
                update_balance();
            }
            else{
            	$('#updateBalancesModal .overall_status').text('Something Went wrong');
            	$('#updateBalancesModal .current_status').text(response.message);
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

var update_balance_xhr;
function update_balance()
{
	var progress = Math.round((index/list.length)*100);
	$('#updateBalancesModal .progress-bar').css('width', progress+'%');
	$('#updateBalancesModal .progress-bar').text(progress+'%');

	if(index < list.length)
	{
		var current_status = `${(index+1)} / ${list.length} (${list[index].name})`;
		$('#updateBalancesModal .current_status').text(current_status);

		// var overall_status = `${(index+1)} / ${list.length} (${list[index].name})`;
		// $('#updateBalancesModal .overall_status').text(overall_status);

		update_balance_xhr = $.ajax({
	        url: '/admin/rewritebooks/update_balance', 
	        type: 'POST',
	        data: {type: type, id: list[index].id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){
	                index++;
	                update_balance();
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
        $('#updateBalancesModal .current_status').text('All accounts are updated');

        $('#updateBalancesModal .close_btn').text('Done');
        $('#updateBalancesModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}

$("#updateBalancesModal").on('hide.bs.modal', function () {
	if(get_details_xhr)
		get_details_xhr.abort();  
    if(update_balance_xhr)
    	update_balance_xhr.abort();
});


var voucher_type_list = [];
var error_voucher_list = [];
var voucher_index = 0;

var get_voucher_type_details_xhr;
$(document).on('click', '#voucher_verification', function(){

	$('#voucherVerificationModal .close_btn').text('Cancel');
    $('#voucherVerificationModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	voucher_type_list = [];
	error_voucher_list = [];
	voucher_index = 0;
	$('#voucher_verification_list').html('');

	$('#voucherVerificationModal .modal-title').text('Voucher Verification');

	$('#voucherVerificationModal .overall_status').text('Please wait while details are being fetched...');
	$('#voucherVerificationModal .current_status').text('');

	$('#voucherVerificationModal .progress-bar').css('width', '0%');
	$('#voucherVerificationModal .progress-bar').text('0%');

	$('#faults').text('0');

	$('#voucherVerificationModal').modal('show');

	get_voucher_type_details_xhr = $.ajax({
        url: '/admin/rewritebooks/load_voucher_type_details', 
        type: 'POST',
        data: {},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                voucher_type_list = response.list;
                var overall_status = 'Total Voucher Types: ' + voucher_type_list.length;
                $('#voucherVerificationModal .overall_status').text(overall_status);
                verify_vouchers();
            }
            else{
            	$('#voucherVerificationModal .overall_status').text('Something Went wrong');
            	$('#voucherVerificationModal .current_status').text(response.message);
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

var verify_vouchers_xhr;
function verify_vouchers()
{
	var progress = Math.round((voucher_index/voucher_type_list.length)*100);
	$('#voucherVerificationModal .progress-bar').css('width', progress+'%');
	$('#voucherVerificationModal .progress-bar').text(progress+'%');

	if(voucher_index < voucher_type_list.length)
	{
		var current_status = `${(voucher_index+1)} / ${voucher_type_list.length} (${voucher_type_list[voucher_index].name})`;
		$('#voucherVerificationModal .current_status').text(current_status);

		verify_vouchers_xhr = $.ajax({
	        url: '/admin/rewritebooks/verify_vouchers', 
	        type: 'POST',
	        data: {id: voucher_type_list[voucher_index].id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){

	            	if(response.list.length > 0){
	            		$.each(response.list, function(index,object){
	            			error_voucher_list.push(object);
	            			$('#faults').text(error_voucher_list.length);

	            			var html = `
	            				<li class="list-group-item" data-id="${object.voucher_txn_id}">
								  	${voucher_type_list[voucher_index].name} Voucher No. ${object.comp_vch_no}

								  	<a href="javascript:void(0)" data-id="${object.voucher_txn_id}" data-string="${voucher_type_list[voucher_index].id}||${object.voucher_txn_id}" class="btn btn-sm btn-danger float-end mx-1 deleteVoucher">Delete</a>
								  	<a href="/admin/rewritebooks/edit/${object.voucher_txn_id}" target="_blank" data-id="${object.voucher_txn_id}" data-type="all" class="btn btn-sm btn-primary float-end mx-1 editVoucher">Edit</a>
								  	<span class="float-end mx-1">${object.voucher_date}</span>

								</li>
	            			`;
	            			$('#voucher_verification_list').prepend(html);
	            		});
	            	}

	                voucher_index++;
	                verify_vouchers();
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
        $('#voucherVerificationModal .current_status').text('All Voucher are Verified');

        $('#voucherVerificationModal .close_btn').text('Done');
        $('#voucherVerificationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}

$("#voucherVerificationModal").on('hide.bs.modal', function () {
	if(get_voucher_type_details_xhr)
		get_voucher_type_details_xhr.abort();  
    if(verify_vouchers_xhr)	
    	verify_vouchers_xhr.abort();
});


$(document).on('click', '.editVoucher', function(){
	var id = $(this).data('id');
	var account_id = $(this).data('account_id');
	var type = $(this).data('type');
	
	if($('.voucher_list').find('li[data-id="'+id+'"] button.refreshVoucher').length == 0){
		var html = `<button class="btn btn-sm btn-success float-end refreshVoucher" data-id="${id}" data-account_id="${account_id}" data-type="${type}">Refresh</button>`;
		$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);
	}
	

});

$(document).on('click', '.refreshVoucher', function(){
	var id = $(this).data('id');
	var type = $(this).data('type');
	var account_id = $(this).data('account_id');

	$.ajax({
        url: '/admin/rewritebooks/verify_single_voucher', 
        type: 'POST',
        data: {id: id, type: type, account_id: account_id},
        dataType: "json",
        beforeSend: function() {
            $('.refreshVoucher').css('pointer-events', 'none');
        },
        success: function (response) {
            
            if(response.status == 2){
  
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('a').remove();
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('button').remove();
				var html = `<span class="badge bg-danger float-end">Deleted</span>`;
				$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);

				var faults = parseInt($("#faults").text());
				if (faults > 0)
					$("#faults").text((faults-1));
				else
					$("#faults").text('0');
            }
            else if(response.status == 1){
  
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('a').remove();
        		$('.voucher_list').find('li[data-id="'+id+'"]').find('button').remove();
				var html = `<span class="badge bg-success float-end">Resolved</span>`;
				$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);

				var faults = parseInt($("#faults").text());
				if (faults > 0)
					$("#faults").text((faults-1));
				else
					$("#faults").text('0');
            }
            else{
            	$('.voucher_list').find('li[data-id="'+id+'"]').find('button').remove();
            }
            
        },
        complete: function() {
            $('.refreshVoucher').css('pointer-events', 'auto');
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
});

$(document).on('click', '.deleteVoucher', function(){
	var id = $(this).data('id');
	var string = $(this).data('string');

	$.ajax({
        url: '/admin/reports/remove_daybook_vouchers', 
        type: 'POST',
        data: {vchrids: [string], token: "<?php echo date('ssYssHs');?>"},
        dataType: "json",
        beforeSend: function() {
            $('.deleteVoucher').css('pointer-events', 'none');
        },
        success: function (response) {
            
            if(response.status){
            	if(response.errors.length == 0)
            	{
            		$('.voucher_list').find('li[data-id="'+id+'"]').find('a').remove();
					var html = `<span class="badge bg-danger float-end">Deleted</span>`;
					$('.voucher_list').find('li[data-id="'+id+'"]').prepend(html);

					var faults = parseInt($("#faults").text());
					if (faults > 0)
						$("#faults").text((faults-1));
					else
						$("#faults").text('0');
            	}

            	if(response.errors.length == 1)
            		alert(response.errors[0]);
            }
            
        },
        complete: function() {
            $('.deleteVoucher').css('pointer-events', 'auto');
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
});


var table_list = [];
var error_table_list = [];
var table_index = 0;

var get_db_details_xhr;
$(document).on('click', '#db_repair', function(){

	$('#dbRepairModal .close_btn').text('Cancel');
    $('#dbRepairModal .close_btn').removeClass('btn-success').addClass('btn-danger');
	
	table_list = [];
	error_table_list = [];
	table_index = 0;
	$('#voucher_verification_list').html('');

	$('#dbRepairModal .modal-title').text('Database Diagnose');

	$('#dbRepairModal .overall_status').text('Please wait while DB details are being fetched...');
	$('#dbRepairModal .current_status').text('');

	$('#dbRepairModal .progress-bar').css('width', '0%');
	$('#dbRepairModal .progress-bar').text('0%');

	$('#faults').text('0');

	$('#dbRepairModal').modal('show');

	get_db_details_xhr = $.ajax({
        url: '/admin/rewritebooks/load_db_details', 
        type: 'POST',
        data: {},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                table_list = response.list;
                
                var overall_status = 'Total Tables: ' + table_list.length;
                $('#dbRepairModal .overall_status').text(overall_status);
                diagnose_db();
            }
            else{
            	$('#dbRepairModal .overall_status').text('Something Went wrong');
            	$('#dbRepairModal .current_status').text(response.message);
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

var diagnose_db_xhr;
function diagnose_db()
{
	var progress = Math.round((table_index/table_list.length)*100);
	$('#dbRepairModal .progress-bar').css('width', progress+'%');
	$('#dbRepairModal .progress-bar').text(progress+'%');

	if(table_index < table_list.length)
	{
		var current_status = `${(table_index+1)} / ${table_list.length}`;
		$('#dbRepairModal .current_status').text(current_status);

		diagnose_db_xhr = $.ajax({
	        url: '/admin/rewritebooks/diagnose_db', 
	        type: 'POST',
	        data: {table_name: table_list[table_index]},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){

	            	if(response.list.length > 0){
	            		$.each(response.list, function(index,object){
	            			error_voucher_list.push(object);
	            			$('#faults').text(error_table_list.length);

	            			var html = `
	            				<li class="list-group-item" data-id="${object.voucher_txn_id}">
								  	${table_list[table_index]} Voucher No. ${object.comp_vch_no}

								  	<a href="javascript:void(0)" data-id="${object.voucher_txn_id}" data-string="${table_list[table_index].id}||${object.voucher_txn_id}" class="btn btn-sm btn-danger float-end mx-1 deleteVoucher">Delete</a>
								  	<a href="/admin/rewritebooks/edit/${object.voucher_txn_id}" target="_blank" data-id="${object.voucher_txn_id}" data-type="all" class="btn btn-sm btn-primary float-end mx-1 editVoucher">Edit</a>
								  	<span class="float-end mx-1">${object.voucher_date}</span>

								</li>
	            			`;
	            			$('#voucher_verification_list').prepend(html);
	            		});
	            	}

	                table_index++;
	                diagnose_db();
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
        $('#dbRepairModal .current_status').text('All Tables are Verified');

        $('#dbRepairModal .close_btn').text('Done');
        $('#dbRepairModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}

$("#dbRepairModal").on('hide.bs.modal', function () {
	if(get_db_details_xhr)
		get_db_details_xhr.abort();  
    if(diagnose_db_xhr)	
    	diagnose_db_xhr.abort();
});

var get_bbb_accounts_xhr;
var bbb_accounts_list = [];
var bbb_accounts_index = 0;

var bbb_error_voucher_list = [];

$(document).on('click', '#bbb_voucher_verification', function(){
	bbb_accounts_list = [];
	bbb_accounts_index = 0;
	bbb_error_voucher_list = [];
	$('#bbb_faults').text('0');
	$('.voucher_list').html('');

	$('#bbbVoucherVerificationModal .close_btn').text('Cancel');
    $('#bbbVoucherVerificationModal .close_btn').removeClass('btn-success').addClass('btn-danger');

	$('#bbbVoucherVerificationModal .overall_status').text('Please wait while details are being fetched...');
	$('#bbbVoucherVerificationModal .current_status').text('');

	$('#bbbVoucherVerificationModal .overall_sub_status').text('');
	$('#bbbVoucherVerificationModal .current_sub_status').text('');

	$('#bbbVoucherVerificationModal .progress-bar').css('width', '0%');
	$('#bbbVoucherVerificationModal .progress-bar').text('0%');

	$('#bbbVoucherVerificationModal').modal('show');

	get_bbb_accounts_xhr = $.ajax({
        url: '/admin/rewritebooks/get_bbb_accounts', 
        type: 'POST',
        data: {},
        dataType: "json",
        beforeSend: function() {
            // show_loader();
        },
        success: function (response) {
            
            if(response.status){
                bbb_accounts_list = response.list;
                var overall_status = 'Total Accounts: ' + bbb_accounts_list.length;
                $('#bbbVoucherVerificationModal .overall_status').text(overall_status);
                get_bbb_vouchers_list();
            }
            else{
            	$('#bbbVoucherVerificationModal .overall_status').text('Something Went wrong');
            	$('#bbbVoucherVerificationModal .current_status').text(response.message);
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

var get_bbb_vouchers_xhr;
var bbb_vouchers_list = [];
var bbb_vouchers_index = 0;

function get_bbb_vouchers_list()
{
	bbb_vouchers_list = [];
	bbb_vouchers_index = 0;

	var progress = Math.round((bbb_accounts_index/bbb_accounts_list.length)*100);
	$('#bbbVoucherVerificationModal .progress-bar-main').css('width', progress+'%');
	$('#bbbVoucherVerificationModal .progress-bar-main').text(progress+'%');

	if(bbb_accounts_index < bbb_accounts_list.length)
	{
		var current_status = `${(bbb_accounts_index+1)} / ${bbb_accounts_list.length} (${bbb_accounts_list[bbb_accounts_index].name})`;
		$('#bbbVoucherVerificationModal .current_status').text(current_status);

		$('#bbbVoucherVerificationModal .overall_sub_status').text('Please wait while details are being fetched...');
		$('#bbbVoucherVerificationModal .current_sub_status').text('');

		$('#bbbVoucherVerificationModal .progress-bar-sub').css('width', '0%');
		$('#bbbVoucherVerificationModal .progress-bar-sub').text('0%');

		get_bbb_vouchers_xhr = $.ajax({
	        url: '/admin/rewritebooks/get_bbb_vouchers', 
	        type: 'POST',
	        data: {account_id: bbb_accounts_list[bbb_accounts_index].id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status){
	                bbb_vouchers_list = response.list;
	                var overall_status = 'Total Vouchers: ' + bbb_vouchers_list.length;
	                $('#bbbVoucherVerificationModal .overall_sub_status').text(overall_status);
	                verify_bbb_vouchers();
	            }
	            else{
	            	$('#bbbVoucherVerificationModal .overall_sub_status').text('Total Vouchers: 0');
	            	$('#bbbVoucherVerificationModal .current_sub_status').text('All Voucher are Verified');

	            	bbb_accounts_index++;
	    			get_bbb_vouchers_list();
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
	}
	else
	{
		$('#bbbVoucherVerificationModal .current_status').text('All accounts are updated');

		$('#bbbVoucherVerificationModal .progress-bar-sub').css('width', '100%');
		$('#bbbVoucherVerificationModal .progress-bar-sub').text('100%');

        $('#bbbVoucherVerificationModal .close_btn').text('Done');
        $('#bbbVoucherVerificationModal .close_btn').removeClass('btn-danger').addClass('btn-success');
	}
}

var verify_bbb_vouchers_xhr;


function verify_bbb_vouchers()
{
	var progress = Math.round((bbb_vouchers_index/bbb_vouchers_list.length)*100);
	$('#bbbVoucherVerificationModal .progress-bar-sub').css('width', progress+'%');
	$('#bbbVoucherVerificationModal .progress-bar-sub').text(progress+'%');

	var progress2 = Math.round((bbb_accounts_index/bbb_accounts_list.length)*100);
	var progress3 = progress2 + Math.round(progress/bbb_accounts_list.length)

	$('#bbbVoucherVerificationModal .progress-bar-main').css('width', progress3+'%');
	$('#bbbVoucherVerificationModal .progress-bar-main').text(progress3+'%');

	if(bbb_vouchers_index < bbb_vouchers_list.length)
	{
		var current_status = `${(bbb_vouchers_index+1)} / ${bbb_vouchers_list.length} (${bbb_vouchers_list[bbb_vouchers_index].name})`;
		$('#bbbVoucherVerificationModal .current_sub_status').text(current_status);

		verify_bbb_vouchers_xhr = $.ajax({
	        url: '/admin/rewritebooks/verify_bbb_vouchers', 
	        type: 'POST',
	        data: {account_id: bbb_accounts_list[bbb_accounts_index].id, voucher_txn_id: bbb_vouchers_list[bbb_vouchers_index].id},
	        dataType: "json",
	        beforeSend: function() {
	            // show_loader();
	        },
	        success: function (response) {
	            
	            if(response.status == 1 || response.status == 2){
	                bbb_vouchers_index++;
	                verify_bbb_vouchers();
	            }
	            if(response.status == 0){
        			bbb_error_voucher_list.push(bbb_vouchers_list[bbb_vouchers_index].id);
        			$('#bbb_faults').text(bbb_error_voucher_list.length);

        			var html = `
        				<li class="list-group-item" data-id="${bbb_vouchers_list[bbb_vouchers_index].id}">
						  	${bbb_vouchers_list[bbb_vouchers_index].name}

						  	<a href="javascript:void(0)" data-id="${bbb_vouchers_list[bbb_vouchers_index].id}" data-string="${bbb_vouchers_list[bbb_vouchers_index].voucher_type_id}||${bbb_vouchers_list[bbb_vouchers_index].id}" class="btn btn-sm btn-danger float-end mx-1 deleteVoucher">Delete</a>
						  	<a href="/admin/rewritebooks/edit/${bbb_vouchers_list[bbb_vouchers_index].id}" target="_blank" data-id="${bbb_vouchers_list[bbb_vouchers_index].id}" data-account_id="${bbb_accounts_list[bbb_accounts_index].id}" data-type="bbb" class="btn btn-sm btn-primary float-end mx-1 editVoucher">Edit</a>
						  	<span class="float-end mx-1">${bbb_vouchers_list[bbb_vouchers_index].voucher_date}</span>

						</li>
        			`;
        			$('#bbb_voucher_verification_list').prepend(html);

        			bbb_vouchers_index++;
	                verify_bbb_vouchers();
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
        $('#bbbVoucherVerificationModal .current_sub_status').text('All Voucher are Verified');

        bbb_accounts_index++;
	    get_bbb_vouchers_list();
	}
}

$("#bbbVoucherVerificationModal").on('hide.bs.modal', function () {
	if(get_bbb_accounts_xhr)
		get_bbb_accounts_xhr.abort();
	if(get_bbb_vouchers_xhr)
		get_bbb_vouchers_xhr.abort();  
    if(verify_bbb_vouchers_xhr)	
    	verify_bbb_vouchers_xhr.abort();
});

			
</script>
</body>
</html>