<?php $header = array( 	'title' => $voucher_name .' Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
<?php  $comp_vch_series_no ='0'; ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
 

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
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>

<div class="row mb-2">
    <div class="col-md-6 order-1"><h3><?php echo $voucher_name;?>  Voucher</h3> </div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   

                <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
            
            <a href="#"><span class="material-symbols-outlined">print</span></a>
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
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-6 order-2 order-md-3">
   <div class="form-check form-check-inline">
 <input class="form-check-input" type="checkbox" value="1" id="detailedCheck">
    <label class="form-check-label" for="detailedCheck">Detailed</label>
   </div><div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
    <label class="form-check-label" for="ccCheck">Cost Centre</label>
    </div>
     </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
    <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
    </ul>
        <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
</div> </div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel">
    <div class="offcanvas-header">
        <h4 class="offcanvas-title" id="moreoptionslable">Apps</h4>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
    <div class="row">
    
    <div class="col-sm-6 border-end"> 
        <h5 class="pb-3">Horizontal</h5>
        
        <p class="offcanvaoptions"><i>Condensed</i>
            <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
            <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
        </p>
        
        <p class="offcanvaoptions"><i>Detailed</i>
            <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
            <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
        </p>
        
        <p class="offcanvaoptions"><i>All Labels</i>
            <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
            <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
        </p>
    </div>
    
    <div class="col-sm-6"> 
    <h5 class="pb-3">Verticle</h5>
    
    <p class="offcanvaoptions"><i>Verticle</i>
        <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
        <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
    </p>
    
    <p class="offcanvaoptions"><i>Schudle</i>
        <label class="starcheck"><input type="checkbox" checked="checked"><b class="checkmark">★</b></label>
        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" id="swap"></label>
        <label class="form-check"><input class="form-check-input" type="checkbox" value="" id="swap"></label>
    </p>
    </div>
    
    <div class="col-sm-12 pt-3 border-top"> 
        <p class="offcanvaoptions"><i>Schedule</i>
            <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap"></label>
            <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap"></label>
        </p>
        <p class="offcanvaoptions"><i>Ratio</i>
            <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap"></label>
            <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap"></label>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    <div id="validation_errors"></div>

     <?php 
	   $attributes = " id='form1' name='form1' class='needs-validation' novalidate";
       echo form_open(base_url().'/'.$folder_path.'vouchers/add_voucher_transactions/'.$sel_voucher_id.'/'.$sel_voucher_name, $attributes);
     ?>      
	  <?php echo $message_output->run() ;?> 
	  <input type="hidden" name="voucherdata" id="voucherdata">
    <div class="col-md-12">
	<div class="row mx-0 p-0 mb-2">
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Date: </label>
                <input type="text" name="voucher_date" id="voucher_date" class="datepicker form-control" placeholder="yyyy-mm-dd" value="<?php echo date("d-m-Y");?>" autocomplete="off" required>
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
                <label class="input-group-text">Voucher No: </label>
                <input type="text" name="voucher_no" class="form-control" value="<?php echo $voucher_no;?>" disabled>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">GST Nature:</label>
                <select class="form-control">
                    <option>Choose</option>
                </select>
            </div>
        </div>
    
        <div class="col-md-12 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Narration: </label>
                <textarea  name="long_nareration" class="form-control"></textarea>
            </div>
        </div>
    </div>
    
    <div id="grid_search" style="margin:auto;"></div>  

    </div>
    
    <div class="col-12 text-center pt-2">
        <input type="button" id="submitbtn" name="submitbtn" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-lg btn-success mx-2">
       <a href="javascript:main(0)"  onclick="window.history.go(-1); return false;" class="btn btn-lg btn-secondary mx-2">QUIT</a>
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


            <button id="nextBill" type="button" class="btn btn-primary" onclick="readyBillsModel('prev')">Previous</button>
            <button id="prevBill" type="button" class="btn btn-primary" onclick="readyBillsModel('next')">Next</button>
        
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
        <h4 class="modal-title">Cost Centre</h4>
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
            <button type="button" class="btn btn-success" id="save_cc">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>

    </div>
  </div>
</div>

   
<?php echo view('includes/footer_scripts');
for($i=1;$i<=50;$i++)
 $json_data[] = array("drcr"=>'','account'=>'','short_narrator'=>'','debit'=>'','credit'=>'','account_id'=>'','is_sundry'=>'','is_cc'=>'');


?>
<script>
var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
var accobj =  new Map();
var accountslist = <?php echo json_encode($comp_accounts_dropdown);?>;  
var drcrlist     = [{"C":"C"},{"D":"D"}];

var sundry_accounts = [];
var cc_accounts = [];
var item_checked = [];
         
$("#submitbtn").on("click",function(){
    sundry_accounts = [];
    cc_accounts     = [];
    item_checked    = [];
    
    var final_account_id = [];
    var data = $("#grid_search").pqGrid('option', 'dataModel.data');
    
    var dateFrom = '<?php echo $fy_begndt;?>';
    var dateTo   = '<?php echo $fy_end;?>';
    var dateCheck  =  $("#voucher_date").val();
    var d1 = dateFrom.split("-");
    var d2 = dateTo.split("-");
    var c  = dateCheck.split("-");
    
    var from_year = d1[2];  // -1 because months are from 0 to 11
    var to_year   = d2[2];
    var check_year = c[2];//, parseInt(c[1])-1, c[0]);
    
    if( (check_year==from_year) || (check_year==to_year))
        var datevalidate=1;
    else
        var datevalidate=0;
    
    
    var debitsum=0;
    var creditsum=0;
    
    for (var i = 0; i < data.length; i++) {

        var drcr             = data[i]['drcr'];
        var account          = data[i]['account'];
        var is_sundry        = data[i]['is_sundry'];
        var is_cc            = data[i]['is_cc'];
        var short_narrator   = data[i]['short_narrator'];
        var debit            = (Math.round(data[i]['debit'] * 100) / 100).toFixed(2);
        var credit           = (Math.round(data[i]['credit'] * 100) / 100).toFixed(2);
        
		
		
		
        if(account!=''){
            if(debit=='')
                debit=0;
        
            if(credit=='')
                credit=0;
            
			//console.log(debit);
		//console.log(credit);
		
            
            debitsum  += parseFloat(debit);
            creditsum += parseFloat(credit);
            
            
            var item_data = {
                "drcr": drcr,
                "account_id":  accobj.get(account),
                "short_narrator" :short_narrator,
                "debit" :debit,
                "credit" :credit
            }
            
            if(is_sundry)
            {   
                var amt = 0;
                if(drcr == 'C')
                    amt = credit;
                if(drcr == 'D')
                    amt = debit;
                
                if($('#detailedCheck').is(":checked"))
                {
                    sundry_accounts.push({
                        "account_id":  accobj.get(account),
                        "account_name": account,
                        "drcr": drcr,
                        "amount": amt,
                        "grid": generateBillData()
                    }); 
                }
                else{
                    item_data.billsdata = [{ 
                        "account_id": accobj.get(account),
                        "method"    : 'Adjustment',
                        "reference" : 'UNDEFINED',
                        "reference_id" : 0,
                        "amount"    : amt,
                        "drcr"      : drcr,
                        "due_date"  : '',
                        "narration" : ''
                    }];
                }
                
            }
            
            if(is_cc)
            {   
                var amt = 0;
                if(drcr == 'C')
                    amt = credit;
                if(drcr == 'D')
                    amt = debit;
                
                if($('#ccCheck').is(":checked"))
                {
                    cc_accounts.push({
                        "account_id"    :  accobj.get(account),
                        "account_name"  : account,
                        "drcr"          : drcr,
                        "amount"        : amt,
                        "grid"          : generateCcData()
                    }); 
                }
                else{
                    item_data.ccdata = [{ 
                        "cc_id"       : 1,
                        "account_id"  : accobj.get(account),
                        "cc_name"     : 'UNDEFINED',
                        "cc_txn_amt"  : amt,
                        "cc_txn_drcr" : drcr,
                        "cc_txn_narr" : ''
                    }];
                }
                
            }
            
            item_checked.push(item_data);
        
        }
    } // end for loop
    debitsum = (Math.round(debitsum * 100) / 100).toFixed(2);
	creditsum = (Math.round(creditsum * 100) / 100).toFixed(2);
	
	
	//alert(debitsum+"---"+creditsum);
	//return false;
    if(item_checked.length==0 || $("#voucher_series").val()==''){
        alert_notification("Kindly fill the voucher data!!!");
        return false;   
    }
    else if(datevalidate==0){
        alert_notification("voucher date is worng!!!");
        return false;    
    }   
    
    else if(debitsum!=creditsum){
        alert_notification("voucher totals worng!!!");
        return false;    
    } 
    else{
        show_loader();
        if(sundry_accounts.length > 0){
            readyBills();
        }
        else if(cc_accounts.length > 0){
            readyCc();
        }
        else{
            $("#voucherdata").val(JSON.stringify(item_checked));
            $("#form1").submit();
        }
            
    }
   
});

    $(document).on('submit', '#form1', function(e){
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

//------------------------------------------------------------------------------ Bill By Bill


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
        show_loader();
        if(cc_accounts.length > 0){
            readyCc();
        }
        else{
            $("#voucherdata").val(JSON.stringify(item_checked));
            $("#form1").submit();
        }
    }
});

function saveBillByBillData()
{
    $.each(sundry_accounts, function(index,obj)
    {
        var account_id = obj.account_id;
        var billsdata = [];
        
        $.each(obj.grid, function(index2, obj2)
        {
            if(obj2.method != '' && obj2.reference != '')
            {
                var method     = obj2.method;
                var reference  = obj2.reference;
                var reference_id  = obj2.reference_id;
                var amount     = obj2.amount;
                var drcr       = obj2.drcr;
                var due_date   = obj2.due_date;
                var narration  = obj2.narration;

                billsdata.push({
                    "account_id": account_id,
                    "method"    : method,
                    "reference" : reference,
                    "reference_id" : reference_id,
                    "amount"    : amount,
                    "drcr"      : drcr,
                    "due_date"  : due_date,
                    "narration" : narration
                });
            }
        });
        
        var i = item_checked.findIndex(function(o) {
           return o.account_id == account_id;
        });
        if(i >= 0){
            item_checked[i].billsdata = billsdata;
        }
    });
}

function validateAllBillsData()
{
    var status = true;
    $.each(sundry_accounts, function(index,obj){

        var final_amount = obj.amount;
        var final_drcr = obj.drcr;
        var final_total = final_amount;
        if(final_drcr == 'C'){
            final_total = -final_total;
        }
        
        var total = 0;
        $.each(obj.grid, function(index2, obj2){
            var method     = obj2.method;
            var reference  = obj2.reference;
            var amount     = obj2.amount;
            var drcr       = obj2.drcr;
            
            
            if(method!='' && reference!='')
            {   
                var sub = 0;
                if(drcr == 'D'){
                    sub = parseFloat(amount);
                }
                if(drcr == 'C'){
                    sub = -parseFloat(amount);
                }
                total += parseFloat(sub);
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

var billIndex = 0;

function readyBills()
{
    var account_id_array = sundry_accounts.map(function(obj) { return obj.account_id; });
    $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>/vouchers/getAccountBillRefs",
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
                    sundry_accounts[index].ref_list = obj;
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
                sub = parseFloat(amount);
            }
            if(drcr == 'C'){
                sub = -parseFloat(amount);
            }
            sum += parseFloat(sub);
              
        }
    }
    // if(count == 0){
    //     error = 1;
    // }
    if(error){
        alert("Kindly fill the details correctly !!!");
        return false;
    }
    var final_amount = sundry_accounts[billIndex].amount;; 
    var final_drcr = sundry_accounts[billIndex].drcr;
    
    if(final_drcr == 'C'){
        final_amount = -final_amount;
    }
    
    if(sum != final_amount && count > 0){
        alert('Total Mismatch');
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
        if(billIndex < (sundry_accounts.length - 1)){
            billIndex = billIndex + 1;
        }
    }
    if(step == 'prev'){
        if(billIndex > 0){
            billIndex = billIndex - 1;
        }
    }
    if(sundry_accounts[billIndex])
    {
        var account_name = sundry_accounts[billIndex].account_name;
        var amount = sundry_accounts[billIndex].amount;
        var drcr = sundry_accounts[billIndex].drcr;
        
        $('#bills_account').text(account_name);
        $('#bills_total').text(amount);
        $('#bills_drcr').text(drcr + 'r');
        

        $('#billsModal').modal('show');
        
        $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', sundry_accounts[billIndex].grid);
        $("#bill_by_bill_grid").pqGrid('refreshDataAndView');
    }
    
}

            
function generateBillData()
{
    var json = [];
    for(var i=0;i<50;i++){
        json.push({'method': '', 'reference': '', 'reference_id': '', 'amount': '', 'drcr': '', 'due_date': '', 'narration': ''});
    }
    return json;
}
            
function dateEditor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData,                
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
    //initialize the editor
    $inp
    .on("input", function (evt) {
        validate(this);
    })
    .datepicker({
        dateFormat:"yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        showAnim: '',
        onSelect: function () {
            this.firstOpen = true;
            validate(this);
        },
        beforeShow: function (input, inst) {
            return !this.firstOpen;
        },
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
    })
};
            
function referenceEditor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData
        
        if(rd.method == 'Adjustment')
        {
            $inp.autocomplete({
                source:  sundry_accounts[billIndex].ref_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
					rd.reference_id = ui.item.id;
					rd.reference = ui.item.label;
					rd.due_date = ui.item.bill_due_date;
				}

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.reference = '';
                rd.reference_id = '';
                rd.due_date = '';
            }).focusout(function () {              
                if(rd.reference != '' && rd.reference_id == '')
                {
                    var index = sundry_accounts[billIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.reference.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val(sundry_accounts[billIndex].ref_list[index].value); 
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
                    var index = sundry_accounts[billIndex].ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.reference = '';
                        
                    }
                }

            });
        }

}
            
function calculateBillSummary() {
    var total = 0,
        sub = 0,
        data = this.option('dataModel.data');
    
    data.forEach(function(row){
        
        if(row.method != '' && row.amount != '' && row.drcr != '')
        {
            sub = 0;
            if(row.drcr == 'D'){
                sub = parseFloat(row.amount);
            }
            if(row.drcr == 'C'){
                sub = -parseFloat(row.amount);
            }
            total  += parseFloat(sub);
        }
    })
    var drcr = 'Dr';
    if(total < 0){
        drcr = 'Cr';
        total = -total;
    }
    
    var totalData = {
        narration : total + ' (' + drcr + ')',
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
        }
    },
    { title: "REFERENCE", width: 100, dataIndx: "reference" ,cls: 'pq-drop-icon pq-side-icon',
        editor: {                   
    		  type: "textbox",
              init: referenceEditor
        },
    },
    { title: "AMOUNT", width: 100,  dataIndx: "amount" ,dataType: "float",format: '##,###.00'},
    { title: "Dr/Cr", dataIndx: "drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',editor: {
            type: 'select',
            options: drcrlist
        }
    },
    { title: "DUE DATE", dataIndx: "due_date", width: 100 ,dataType: 'date', format: 'yy-mm-dd',
        editor: {
	        type: 'textbox',
	        init: dateEditor
	    },
	    validations: [
            { type: 'regexp', value: '^[0-9]{4}[-][0-9]{2}[-][0-9]{2}$', msg: 'Not in yy-mm-dd format' }
        ]
        
    },
    { title: "NARRATION", width: 100, dataType: "string", dataIndx: "narration"}
];

var billsObj = {
    collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
    height: 420,
    selectionModel: { type: 'cell' }, 
    scrollModel: { autoFit: true },
    dataModel: bill_dataModel,
    pageModel: { type: 'local' },
    colModel: bill_colModel,  
    pageModel: { type: 'local' },
    numberCell: { show: true },
    change: calculateBillSummary,
    dataReady: calculateBillSummary,
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
        var grid = this,
        $select_row = $(".select-row"),
        data = ui.dataModel.data;
        grid.setSelection({ rowIndx: 0, focus: true });
    }
};
            
            

$("#billsModal").on('shown.bs.modal', function () {   
    if($("#bill_by_bill_grid").pqGrid('instance')){    	
        $("#bill_by_bill_grid").pqGrid('refresh');
    }
    else
        $("#bill_by_bill_grid").pqGrid(billsObj);
});




//------------------------------------------------------------------------------ Cost Centre 

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
        
        $("#voucherdata").val(JSON.stringify(item_checked));
        show_loader();
        $("#form1").submit();
    }
});

function saveCcData()
{
    $.each(cc_accounts, function(index,obj)
    {
        var account_id = obj.account_id;
        var ccdata = [];
        
        $.each(obj.grid, function(index2, obj2)
        {
            if(obj2.cc_id!='' && obj2.cc_name != '')
            { 
                var cc_id       = obj2.cc_id;
                var cc_name     = obj2.cc_name;
                var cc_txn_amt  = obj2.cc_txn_amt;
                var cc_txn_drcr = obj2.cc_txn_drcr;
                var cc_txn_narr = obj2.cc_txn_narr;
            
                ccdata.push({
                    "cc_id"       : cc_id,
                    "account_id"  : account_id,
                    "cc_name"     : cc_name,
                    "cc_txn_amt"  : cc_txn_amt,
                    "cc_txn_drcr" : cc_txn_drcr,
                    "cc_txn_narr" : cc_txn_narr
                });
            }
        });
        
        var i = item_checked.findIndex(function(o) {
           return o.account_id == account_id;
        });
        if(i >= 0){
            item_checked[i].ccdata = ccdata;
        }
        
    });
    
    
}

function validateAllCcData()
{
    var status = true;
    $.each(cc_accounts, function(index,obj)
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
            if(obj2.cc_name != '' && obj2.cc_id != '')
            {
                var cc_txn_amt     = obj2.cc_txn_amt;
                var cc_txn_drcr    = obj2.cc_txn_drcr;

                var sub = 0;
                if(cc_txn_drcr == 'D'){
                    sub = parseFloat(cc_txn_amt);
                }
                if(cc_txn_drcr == 'C'){
                    sub = -parseFloat(cc_txn_amt);
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
                sum += parseFloat(cc_txn_amt);
            }
            if(cc_txn_drcr == 'C'){
                sum += -parseFloat(cc_txn_amt);
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
        
        $('#cc_account').text(account_name);
        $('#cc_total').text(amount);
        $('#cc_drcr').text(drcr + 'r');
        

        $('#ccModal').modal('show');
        
        $("#cc_grid").pqGrid('option', 'dataModel.data', cc_accounts[ccIndex].grid);
        $("#cc_grid").pqGrid('refreshDataAndView');
    }
    
}
            
            
function generateCcData()
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
        rd = ui.rowData;
        

        $inp.autocomplete({
            source: cc_list,
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 0,
            select: function(event, ui) {
				rd.cc_id = ui.item.id;
				rd.cc_name = ui.item.label;
			}

        }).focus(function () {
            $(this).autocomplete("search", "");
            rd.cc_id = '';
            rd.cc_name = '';
        }).focusout(function () {              
            if(rd.cc_name != '' && rd.cc_id == '')
            {
                var index = cc_list.findIndex(function(obj) {
                   return obj.label.toLowerCase() == rd.cc_name.toLowerCase();
                });
                if(index > -1){
                    rd.cc_id = cc_list[index].id;
                    rd.cc_name = cc_list[index].label;
                }
                else{
                    rd.cc_id = '';
                    rd.cc_name = '';
                    rd.cc_txn_amt = '';
                    rd.cc_txn_drcr = '';
                    rd.cc_txn_narr = '';
                    $("#cc_grid").pqGrid('refreshDataAndView');
                }
            }
            if(rd.cc_name == '' && rd.cc_id == '')
            {
                rd.cc_txn_amt = '';
                rd.cc_txn_drcr = '';
                rd.cc_txn_narr = '';
                $("#cc_grid").pqGrid('refreshDataAndView');
            }
        });
        
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
                sub = parseFloat(row.cc_txn_amt);
            }
            if(row.cc_txn_drcr == 'C'){
                sub = -parseFloat(row.cc_txn_amt);
            }
            total  += parseFloat(sub);
        }
    })
    var drcr = 'Dr';
    if(total < 0){
        drcr = 'Cr';
        total = -total;
    }
    
    var totalData = {
        cc_txn_narr : total + ' (' + drcr + ')',
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
    { title: "AMOUNT", width: 100,  dataIndx: "cc_txn_amt" ,dataType: "float",format: '##,###.00'},
    { title: "Dr/Cr", dataIndx: "cc_txn_drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',editor: {
            type: 'select',
            options: drcrlist2
        }
    },
    { title: "NARRATION", width: 100, dataType: "string", dataIndx: "cc_txn_narr"}
];

var ccObj = {
    collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
    height: 'flex',
    selectionModel: { type: 'cell' }, 
    scrollModel: { autoFit: true },
    dataModel: cc_dataModel,
    colModel: cc_colModel,  
    pageModel: { type: 'local' },
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
            
            

$("#ccModal").on('shown.bs.modal', function () {   
    if($("#cc_grid").pqGrid('instance')){    	
        $("#cc_grid").pqGrid('refresh');
    }
    else
        $("#cc_grid").pqGrid(ccObj);
});
              


 $(function () {
     
     function calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
            if(row.debit == '' || typeof row.debit == 'undefined'){
                var debit =0;
            }else
             var debit =  row.debit;
            
            if(row.credit == '' || typeof row.credit == 'undefined'){
                var credit =0;
            }else
            var  credit =  row.credit;
         
             
            debitTotal  += parseFloat(debit);
            creditTotal += parseFloat(credit);
        })

        var totalData = {
                drcr      : "Total",
                debit     : debitTotal,
                credit    : creditTotal,
                pq_rowcls : 'grid_footer_color',
                summaryRow: true
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
         
        var autoCompleteEditor = function (ui) {
            var $inp = ui.$cell.find("input");
            var rd = ui.rowData;
            var grid = this;
            $inp.autocomplete({
                // source:  "<?php echo base_url();?>/admin/ajax/get_accounts",
                source: function( request, response ) {
                    $.ajax({
                      url: "<?php echo base_url();?>/admin/ajax/get_accounts",
                      dataType: "json",
                      data: { 
                        term: request.term,
                        voucher_type_id: '<?= $voucher_type_id ?>',
                      },
                      success: function( data ) {
                        response( data );
                      }
                    });
                  },
                selectItem: { on: true }, //custom option
                highlightText: { on: false }, //custom option
                minLength: 3,
                select: function(event, ui) {
					event.preventDefault();
                    // console.log(ui.item);
					var acclabel=ui.item.label;
					var accvalue = ui.item.acc_id;
				    accobj.set(acclabel, accvalue);
				   	$(this).val(ui.item.label);
				   	rd.is_sundry = ui.item.is_sundry;
				   	rd.is_cc = ui.item.is_cc;
				  }
              }).focus(function () {
                $(this).autocomplete("search", "");
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
                grid = this;
            
            $inp.on("change", function (evt) {
                var crdr = $(this).val();
                
                if(crdr == '')
                {
                    rd.account = '';
                    rd.short_narrator = '';
                    rd.debit = '';
                    rd.credit = '';
                    
                }
            })
        };
        
        var colModel = [
            
             { title: "DR/CR", dataIndx: "drcr", width: 20,cls: 'pq-drop-icon pq-side-icon',editor: {
                    type: 'select',
                    options: drcrlist,
                    init: crdrEditor
                }
            },
            
            { title: "ACCOUNT", dataIndx: "account", width: 100,dataType: "string",cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                		  attr: "autocomplete='off'",
                          init: autoCompleteEditor,
                          options: accountslist,
                      },
                    validations: [
                    { 
                      type: function (ui) {
                       var value = ui.value;
                       if(value != '')
                       {
                           var acntnval = accobj.get(value);
                           if(typeof acntnval === 'undefined'){
                             ui.msg = "account is not valid";
                             return false;  
                           }
                          else if(value==''){
                             ui.msg = "account is required";
                             return false;   
                           }
                          else if(intRegex.test(value) || floatRegex.test(value)) {
                              ui.msg = value + " not a valid account";
                              return false;
                           }
                          else{
                            ui.msg='';
                            return true;
                           }
                       }
                       
                        
                        }, icon: 'ui-icon-info'
                      }
                    ]
            },
            { title: "SHORT NARRATION", width: 100, dataType: "string", dataIndx: "short_narrator"},
            
            { title: "DEBIT", width: 20, align: "left",dataIndx: "debit",dataType: "float",format: '##,###.00',
            validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
            editable: function (ui) {
                   var drcrval     = ui.rowData['drcr'];
                   var debit_val   = ui.rowData['debit'];
                   var credit_val  = ui.rowData['credit'];
                   
                   if(debit_val!='' || credit_val!=''){
                        //  console.log("de"+debit_val);
                        //  console.log("cr" +credit_val);
                   
                      }
                    
                    if (drcrval=='C' || credit_val!='') {
                        return false;
                    }
                    else if (drcrval=='') {
                        return false;
                    }
                    else {
                        return true;
                    }
                },
                render: disableTextRenderer
                
            },
            { title: "CREDIT", width: 20, align: "left",dataIndx: "credit",dataType: "float",format: '##,###.00',
            validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
            editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                    if (drcrval=='D') {
                        return false;
                    }
                    else if (drcrval=='') {
                        return false;
                    }
                    else {
                        return true;
                    }
                },
                render: disableTextRenderer},
            
		   
	 	    ];
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 450,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            dataModel: dataModel,
            colModel: colModel,  
            change: calculateSummary, 
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
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              }
        };
        var $grid = $("#grid_search").pqGrid(newObj);
        
        
     });
     
     


</script>

</body>
</html>