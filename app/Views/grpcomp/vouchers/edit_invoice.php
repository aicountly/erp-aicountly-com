<?php $header = array( 	'title' => $voucher_name .' Voucher' ); ?>
<?php echo view('includes/header',$header); ?>
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


<div class="row pb-3">
    <div class="col-md-6 order-1"><h3><?php echo $voucher_name;?>  Voucher</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
                <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history"><span class="material-symbols-outlined">visibility</span></a> 
                <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
            
            <a href="javascript:void(0)" onclick="print_page()"><span class="material-symbols-outlined">print</span></a>
            <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">CSV</a></li>
                <li><a class="dropdown-item" href="#">Excel</a></li>
                <li><a class="dropdown-item" target="_blank" href="<?php echo base_url();?>/admin/GeneratePDF/receipt_preview/<?php echo $voucher_txn_id;?>">PDF</a></li>
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
       <div class="form-check form-check-inline bbbccCheck">
        <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
        <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
    </div>
    <div class="form-check form-check-inline bbbccCheck">
        <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
        <label class="form-check-label" for="ccCheck">Cost Centre</label>
    </div>
    
    <div class="form-check form-check-inline me-2">
        <input class="form-check-input" type="checkbox" value="1" name="oCheck" id="oCheck" form="form1" <?= ($oCheck == 1) ? 'checked' : '' ?>>
        <label class="form-check-label" for="oCheck">Optional</label>
    </div>
    <select form="form1" name="currency_id" class="form-select d-inline-block" style="width:160px;">
       <?php foreach ($currency_list as $value) { ?>
            <option <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?> value="<?= $value['comp_currency_id'] ?>"><?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
       <?php } ?>
   </select>
    </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
        <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
        <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="#">&laquo; Migrate</a>
              <ul class="dropdown-menu dropdown-submenu-left">
                <li><a class="dropdown-item <?= ($voucher_type_id == 9) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/9?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Payment</a></li>
                <li><a class="dropdown-item <?= ($voucher_type_id == 13) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/13?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Receipt</a></li>
                <li><a class="dropdown-item <?= ($voucher_type_id == 1) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/1?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Contra</a></li>
                <li><a class="dropdown-item <?= ($voucher_type_id == 5) ? 'd-none' : '' ?>" href="<?= $base_url ?>vouchers/invoice/5?v=<?= $voucher_txn_id ?>" onclick="return confirm('Are you sure ? ')">Journal</a></li>
              </ul>
            </li>
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
    echo form_open(base_url().'/'.$folder_path.'vouchers/edit/'.$voucher_txn_id, $attributes);
?>      
    <?php echo $message_output->run() ;?>  
    
    <input type="hidden" name="voucherdata" id="voucherdata">
    <input type="hidden" name="bbbdata" id="bbbdata">
    <input type="hidden" name="ccdata" id="ccdata">

    <div class="col-md-12">
	<div class="row mx-0 p-0 mb-2">
    <div class="row m-0 p-0">
	<div class="col-md-3 col-6 mb-3 card p-2">
	   <div class="input-group">
        <label class="input-group-text">Date: </label>
        <input type="text" name="voucher_date" id="voucher_date" class="datepicker form-control" value="<?php echo $voucher_date;?>">
        </div>
	  </div>
    <div class="col-md-3 col-6 mb-3 card p-2">
	  <div class="input-group">
                <label class="input-group-text">Voucher Series: </label>
                <?php	
            echo form_dropdown('voucher_series', $voucher_series_dropdown, $voucher_series,' id="voucher_series" class="voucher_series form-control"  ');
            ?>
            </div>
	  </div>
  <div class="col-md-3 col-6 mb-3 card p-2">
	  <div class="input-group">
                <label class="input-group-text">Voucher No: </label>
				<input type="text" name="voucher_no" class="form-control" value="<?php echo $voucher_no;?>" disabled>
                
            </div>
	  </div>
  <div class="col-md-3 col-6 mb-3 card p-2">
	  <div class="input-group">
                <label class="input-group-text">GST Nature: </label>
                <select class="form-control"><option>Choose</option></select>
            </div>
	  </div> 
        
    </div>
     <div class="col-md-12 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Narration: </label>
                <textarea  name="narration" class="form-control"><?php echo $narration ?></textarea>
            </div>
        </div>
 </div>
 <div id="grid_search" style="margin:auto;"></div>  
    </div>
 

   
 
 <div class="col-12 text-center pt-2">
        <input type="button" id="submitbtn" name="submitbtn" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" class="btn btn-lg btn-success mx-2">
        <a href="javascript:main(0)"  onclick="window.history.go(-1); return false;" class="btn btn-lg btn-secondary mx-2">QUIT</a>
        <a href="javascript:main(0)"  class="deletebtn btn btn-lg btn-danger mx-2">Delete</a>
 </div>
</form>
 
<!-- The Modal -->
<div class="modal" id="billsModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><span id="bills_page"></span> Bill by Bill</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 text-center"><h4>Account: <span id="bills_account"></span></h4></div>
            <div class="col-md-6 text-center"><h4>Total: <span id="bills_total"></span>&nbsp;<span id="bills_drcr"></span></h4></div>
        </div>
        <div id="bill_by_bill_grid"></div>


            <button id="nextBill" type="button" class="btn btn-primary prevBtn" onclick="readyBillsModel('prev')">Previous</button>
            <button id="prevBill" type="button" class="btn btn-primary nextBtn" onclick="readyBillsModel('next')">Next</button>
        
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


            <button id="nextBill" type="button" class="btn btn-primary prevBtn" onclick="readyCcModel('prev')">Previous</button>
            <button id="prevBill" type="button" class="btn btn-primary nextBtn" onclick="readyCcModel('next')">Next</button>
        
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

     
$json_data = $account_transactions;

for($i=1;$i<=20;$i++)
 $json_data[] = array("drcr"=>'','account_name'=>'','description'=>'','debit'=>'','credit'=>'','account_id'=>'','is_bbb'=>'','is_cc'=>'','is_cash'=>'','acc_type'=> '');

?>

<script>

    function print_page(){
        window.open('<?php echo base_url();?>/admin/GeneratePDF/receipt_preview/<?php echo $voucher_txn_id;?>?p=1', '_blank').focus();
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
</script>

<script>

var voucher_type_id = <?= $voucher_type_id ?>;

<?php if($oCheck == 1){ ?>
    $('.bbbccCheck').css('display', 'none');
<?php } ?>


$(document).on('change', '#oCheck', function(){

    $('#bbbCheck').prop('checked', false);
    $('#ccCheck').prop('checked', false);

    if($(this).prop('checked'))
        $('.bbbccCheck').css('display', 'none');
    else
        $('.bbbccCheck').css('display', 'inline-block');
});

    
$(".deletebtn").on('click',function(){
   confirm_delete(baseurl+"/admin/vouchers/delete/<?php echo $voucher_txn_id;?>");		    
   return false
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

   function update_grid_account_balances()
   {
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

var accountslist = [];  
var drcrlist     = [{"C":"C"},{"D":"D"}]; 

var bbb_data = [];
var bbb_accounts = [];

var cc_data = [];
var cc_accounts = [];

var item_checked = [];



$("#submitbtn").on("click",function(){
    bbb_data = [];
    bbb_accounts = [];

    cc_data = [];
    cc_accounts = [];

    item_checked    = [];

    var oCheck = $('oCheck').prop('checked');

    var count_cash_c_side = 0;
    var count_cash_d_side = 0;
    var count_non_cash = 0;
    var count_contra_bsd = 0;
    
    var final_account_id = [];
    var data             = $("#grid_search").pqGrid('option', 'dataModel.data');
    //console.log(data);
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
        var account_name     = data[i]['account_name'];
        var account_id       = data[i]['account_id'];
        var is_bbb           = data[i]['is_bbb'];
        var is_cc            = data[i]['is_cc'];
        var is_cash          = data[i]['is_cash'];
        var acc_type          = data[i]['acc_type'];
        var description      = data[i]['description'];
        var debit            = data[i]['debit'];
        var credit           = data[i]['credit'];

        if(account_id != undefined && account_id != ''){
            
            debitsum  += parseAmount(debit);
            creditsum += parseAmount(credit);
            
            var item_data = {
                "drcr": drcr,
                "account_id":  account_id,
                "acc_type":  acc_type,
                "description" :description,
                "debit" : parseAmount(debit),
                "credit" : parseAmount(credit),

                "is_bbb" : is_bbb,
                "is_cc" : is_cc,
            };
            
            if(is_bbb)
            {   
                var amount = 0;
                if(drcr == 'C')
                    amount = credit;
                if(drcr == 'D')
                    amount = debit;
                
                if($('#bbbCheck').is(":checked"))
                {
                    bbb_accounts.push({
                        "account_id":  account_id,
                        "account_name": account_name,
                        "drcr": drcr,
                        "amount": parseAmount(amount),
                        "grid": generateBillData(account_id)
                    }); 
                }  
            }
            
            if(is_cc)
            {   
                var amount = 0;
                if(drcr == 'C')
                    amount = credit;
                if(drcr == 'D')
                    amount = debit;
                
                if($('#ccCheck').is(":checked"))
                {
                    cc_accounts.push({
                        "account_id"    :  account_id,
                        "account_name"  : account_name,
                        "drcr"          : drcr,
                        "amount"        : parseAmount(amount),
                        "grid"          : generateCcData(account_id)
                    }); 
                }  
            }

            if(is_cash == 1)
            {
                if(drcr == 'C')
                    count_cash_c_side++;

                if(drcr == 'D')
                    count_cash_d_side++;
            }
            else
                count_non_cash++;

            if(voucher_type_id == 1 && acc_type == 'bsd')
            {
                count_contra_bsd++;
            }

            item_checked.push(item_data);
        
        }
    }

    var error_cash = 0;
    if(voucher_type_id == 9 && count_cash_c_side == 0)
        error_cash = 1;
    if(voucher_type_id == 13 && count_cash_d_side == 0)
        error_cash = 1;

    var error_cash_wrong_side = 0;
    if(voucher_type_id == 9 && count_cash_d_side > 0)
        error_cash_wrong_side = 1;
    if(voucher_type_id == 13 && count_cash_c_side > 0)
        error_cash_wrong_side = 1;

    var error_non_cash = 0;
    if(voucher_type_id == 1 && count_non_cash > 0)
        error_non_cash = 1;

    var error_contra_bsd = 0;
    if(voucher_type_id == 1 && count_contra_bsd > 0)
        error_contra_bsd = 1;
    
    if(item_checked.length==0 || $("#voucher_series").val()==''){
        alert_notification("Kindly fill the voucher data!!!");
        return false;   
    }
    else if(datevalidate==0){
        alert_notification("voucher date is worng!!!");
        return false;    
    }
    else if(error_cash){
        if(voucher_type_id == 9)
            alert_notification("Atleast one Account from Cash & Cash Equivalent Group is required on credit side !!!");
        if(voucher_type_id == 13)
            alert_notification("Atleast one Account from Cash & Cash Equivalent Group is required on debit side !!!");
        return false;
    }
    else if(error_cash_wrong_side){
        if(voucher_type_id == 9)
            alert_notification("Account from Cash & Cash Equivalent Group is not allowed on debit side !!!");
        if(voucher_type_id == 13)
            alert_notification("Account from Cash & Cash Equivalent Group is not allowed on credit side !!!");
        return false;
    }
    else if(error_non_cash){
            alert_notification("Accounts only from Cash & Cash Equivalent Group are allowed !!!");
        return false;
    }
    else if(error_contra_bsd){
        alert_notification("Bill Sundry Accounts are not allowed !!!");
        return false;
    }
    else if(debitsum!=creditsum){
        alert_notification("voucher totals worng!!!");
        return false;
    }    
    else{
        show_loader();
        $("#voucherdata").val(JSON.stringify(item_checked));

        if(!oCheck && $('#bbbCheck').is(":checked") && bbb_accounts.length > 0){ // bbbstart
            readyBills();
        }
        else if(!oCheck && $('#ccCheck').is(":checked") && cc_accounts.length > 0){ //ccstart
            readyCc();
        }
        else{
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
                    window.history.back();
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
         
        function calculateSummary() {

            var debitTotal = 0,
                creditTotal = 0,
                data = this.option('dataModel.data'),
                len  = data.length;

            data.forEach(function(row){

                if(row.account_id != '' && row.account_name != '')
                {
                    if(row.debit == '' || typeof row.debit == 'undefined'){
                        var debit =0;
                    }else
                     var debit =  row.debit;
                    
                    if(row.credit == '' || typeof row.credit == 'undefined'){
                        var credit =0;
                    }else
                    var  credit =  row.credit;
                 
                     
                    debitTotal  += parseAmount(debit);
                    creditTotal += parseAmount(credit);  
                }
            })

            var totalData = {
                    drcr      : "Total",
                    debit     : formatAmount(debitTotal),
                    credit    : formatAmount(creditTotal),
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
         
         
        var autoCompleteEditor = function (uimain) {
            
            var $inp = uimain.$cell.find("input");
            var rd = uimain.rowData;
            var grid = this;
            
           var colindex = uimain.column.dataIndx;
           
            $inp.autocomplete({
                source: <?php echo $acc_bsd_json_file; ?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength:0,
                select: function(event, ui) {
                    event.preventDefault();
                   // console.log(ui.item);

                    if(voucher_type_id == 9 && rd.drcr == 'D' && ui.item.is_cash == 1) // payment
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Cash & cash equivalent accounts are not allowed on debit');

                        return false;
                    }
                    else if(voucher_type_id == 13 && rd.drcr == 'C' && ui.item.is_cash == 1) // receipt
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Cash & cash equivalent accounts are not allowed on credit');

                        return false;
                    }
                    else if(voucher_type_id == 1 && ui.item.is_cash == 0) // contra
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Only cash & cash equivalent accounts are allowed');

                        return false;
                    }
                    else if(voucher_type_id == 1 && ui.item.is_sundry == 1) // contra
                    {
                        $(this).val('');
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                        grid.saveEditCell();
                        alert_notification('Bill sundry accounts are not allowed');

                        return false;
                    }
                    else
                    {
                        $(this).val(ui.item.label);
                        rd.account_id = ui.item.acc_id;
                        rd.is_bbb = ui.item.is_bbb;
                        rd.is_cc = ui.item.is_cc;
                        rd.is_cash = ui.item.is_cash;

                        if(ui.item.is_sundry==1)                    
                            rd.acc_type = 'bsd';
                        else if(ui.item.is_acc==1)                   
                            rd.acc_type = 'acc';
                    }
                    
                   
                     
                }
                }).focus(function () {
                    
                    $(this).autocomplete("search", "");
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                }).focusout(function () {  
                    
                    if(rd.account_id != '')
                    {     
                     if(accnt_balances.hasOwnProperty(rd.account_id) ==false ) {
                      var voucher_date = $("#voucher_date").val();
                      var return_response = function (){
                           var accbalance=0;
                            $.ajax({
                                'async': false,
                                'type': "GET",
                                'global': false,
                                'dataType': 'html',
                                'url': "<?php echo $base_url; ?>ajax/accountbalance/"+rd.account_id+"/"+voucher_date,
                                'success': function (data) {
                                    accbalance = data;
                                }
                            });
                            return accbalance;
                        }();
                        accnt_balances[rd.account_id] = return_response;
                     }
                    else
                     return_response =  accnt_balances[rd.account_id];
                    
                   
                       rd.account_balance = return_response;
                    }
                   
                         
                    if(rd.account_id == '')
                    {
                        rd.account_balance = 0;
                        rd.account_name = '';
                        rd.account_id = '';
                        rd.is_cc = '';
                        rd.is_bbb = '';
                        rd.is_cash = '';
                        rd.acc_type = '';
                    }
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
                grid = this,
                rowIndx = ui.rowIndx;

            
            
            $inp.on("change", function (evt) {
                var crdr = $(this).val();
                
                if(crdr == '')
                {
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                    rd.description = '';
                    rd.debit = '';
                    rd.credit = '';
                    grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
                     grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
                    
                }
                if(crdr == 'C'){
                   var amount = expected_drcr(grid,crdr);
                    rd.credit = amount > 0 ? amount : '';
                    rd.debit = '';
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                    rd.description = '';

                   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
                   grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });

                    // grid.saveEditCell();
                    // grid.setSelection( {rowIndx: rowIndx,colIndx: 4} );
                }
                if(crdr == 'D'){
                    var amount = expected_drcr(grid,crdr);
                    rd.credit = '';
                    rd.debit = amount > 0 ? amount : '';
                    rd.account_name = '';
                    rd.account_id = '';
                    rd.is_cc = '';
                    rd.is_bbb = '';
                    rd.is_cash = '';
                    rd.acc_type = '';
                    rd.description = '';

                   grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
                   grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });

                   // grid.saveEditCell();
                   // grid.setSelection( {rowIndx: rowIndx,colIndx: 3} );
                }
                
            })
        };

        function expected_drcr(grid,drcr)
        {
            var debitTotal = 0;
            var creditTotal = 0;
            var data = grid.option('dataModel.data');


            data.forEach(function(row){
                if(row.debit == '' || typeof row.debit == 'undefined'){
                    var debit =0;
                }else
                 var debit =  row.debit;
                
                if(row.credit == '' || typeof row.credit == 'undefined'){
                    var credit =0;
                }else
                var  credit =  row.credit;
             
                 
                debitTotal  += parseAmount(debit);
                creditTotal += parseAmount(credit);
            });

            if(drcr == 'C'){
                return debitTotal - creditTotal;
            }
            if(drcr == 'D'){
                return creditTotal - debitTotal;
            }
            return 0;
        }
        
        var colModel = [
        { title: "DR/CR", dataIndx: "drcr",cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist,
                init: crdrEditor
            }
        },
        { title: "ACCOUNT", dataIndx: "account_name", cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                type: "textbox",
                attr: "autocomplete='off'",
                init: autoCompleteEditor,
                options: [],
            },render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    var account_balance = '0';
                    if(typeof rd.account_id !== "undefined" && rd.account_id != '' && rd.account_name != '')
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
        
        { title: "SHORT NARRATION",  dataType: "string", dataIndx: "description",
            editable: function (ui) {
                   var account_id = ui.rowData['account_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                },
        },
        { title: "DEBIT", width: 20, align: "left",dataIndx: "debit",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var account_id = ui.rowData['account_id'];
                    if (drcrval=='D' && account_id != '') {
                        return true;
                    }
                    return false;
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'D')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'credit', cls: 'grid_footer_color' });
                        rd.credit = '';
                        if(rd.debit != ''){
                            rd.debit = parseAmount(rd.debit);
                            return formatAmount(rd.debit);   
                        }
                        return '';
                    }
                }
                
            },
            { title: "CREDIT", width: 20, align: "left",dataIndx: "credit",dataType: "float",
                validations: [{ type: 'gt', value: 0, msg: "should be > 0"}],
                editable: function (ui) {
                   var drcrval = ui.rowData['drcr'];
                   var account_id = ui.rowData['account_id'];
                    if (drcrval=='C' && account_id != '') {
                        return true;
                    }
                    return false;
                },
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
                    if(rd.drcr == 'C')
                    {
                        grid.removeClass({ rowIndx: ui.rowIndx, dataIndx: ui.dataIndx, cls: 'disabled' });
                        grid.addClass({ rowIndx: ui.rowIndx, dataIndx: 'debit', cls: 'grid_footer_color' });
                        rd.debit = '';
                        if(rd.credit != ''){
                            rd.credit = parseAmount(rd.credit);
                            return formatAmount(rd.credit);   
                        }
                        return '';
                    }
                    
                }
             }
        ];
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            change: calculateSummary, 
            dataReady: calculateSummary,
            numberCell: { show: true },
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            wrap:false,
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

        newObj.cellKeyDown = function(evt, ui) {
           var rowData = ui.rowData;
           var rowIndx = ui.rowIndx;

           if (evt.keyCode == 46){
                $.each(rowData, function(index,obj){
                    rowData[index] = '';
                })
                this.refreshDataAndView();
                return false;
           }
        }

        newObj.cellKeyDown = function(evt, ui) {
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
        
        var $grid = $("#grid_search").pqGrid(newObj);
        
        
     });
//$('#voucher_date').focus();
$("#voucher_date").prop("selectedIndex", 1);

//------------------------------------------------------------------------------ Bill By Bill  bbbstart

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
        show_loader();
        $("#bbbdata").val(JSON.stringify(bbb_data));

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
                var drcr       = obj2.drcr;
                var due_date   = obj2.due_date;
                var narration  = obj2.narration;

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
        
    });
}

function validateAllBillsData()
{
    var status = true;
    $.each(bbb_accounts, function(index,obj){

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
                    sub = parseAmount(amount);
                }
                if(drcr == 'C'){
                    sub = -parseAmount(amount);
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

var billIndex = 0;

function readyBills()
{
    var account_id_array = bbb_accounts.map(function(obj) { return obj.account_id; });
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
                    bbb_accounts[index].ref_list = obj;
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
                sub = parseAmount(amount);
            }
            if(drcr == 'C'){
                sub = -parseAmount(amount);
            }
            sum += sub;
              
        }
    }
    // if(count == 0){
    //     error = 1;
    // }
    if(error){
        alert("Kindly fill the details correctly !!!");
        return false;
    }
    var final_amount = bbb_accounts[billIndex].amount;; 
    var final_drcr = bbb_accounts[billIndex].drcr;

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
        var drcr = bbb_accounts[billIndex].drcr;
        var page = (billIndex + 1) + '/' + bbb_accounts.length;
        
        $('#bills_account').text(account_name);
        $('#bills_total').html(formatAmount(amount));
        $('#bills_drcr').text(drcr + 'r');
        $('#bills_page').text(page);
        

        $('#billsModal').modal('show');
        
        $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', bbb_accounts[billIndex].grid);
        $("#bill_by_bill_grid").pqGrid('refreshDataAndView');

        $('input[type="command-line"]').focus();//tempararily shift focus
        $("#bill_by_bill_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
    }
    
}

            
function generateBillData(account_id = 0)
{
    var json = [];
    
    if(account_id != 0){
        var i = saved_bill_txns.findIndex(function(o) {
           return o.acc_id == account_id;
        });
        if(i >= 0){
            $.each(saved_bill_txns[i].bills_txn_list, function(index, obj){
                json.push({'method': 'Adjustment', 'reference': obj.bills_ref_name, 'reference_id': obj.bills_ref_id, 'amount': obj.bills_txn_amt, 'drcr': obj.bills_txn_drcr,
                            'due_date': obj.bill_due_date, 'narration': obj.bills_txn_narr});
            });
        }
    }
    
    for(var i=0;i<25;i++){
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
        console.log(date);
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
        
        if(row.method != '' && row.amount != '' && row.drcr != '')
        {
            sub = 0;
            if(row.drcr == 'D'){
                sub = parseAmount(row.amount);
            }
            if(row.drcr == 'C'){
                sub = -parseAmount(row.amount);
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
        amount : '',
        narration : formatAmount(total) + ' (' + drcr + ')',
        pq_rowcls : 'grid_footer_color',
        summaryRow: true
    }
    this.option('summaryData', [totalData]);
}


            
var methods = <?php echo json_encode($bills_method_list); ?>;
var drcrlist     = [{"C":"C"},{"D":"D"}];


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




//------------------------------------------------------------------------------ Cost Centre  ccstart

var saved_cc_txns = <?= json_encode($cc_data) ?>;
if(saved_cc_txns.length > 0){
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
        $("#form1").submit();
    }
});

function saveCcData()
{
    $.each(cc_accounts, function(index,obj)
    {
        var account_id = obj.account_id;
        
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
                    sub = parseAmount(cc_txn_amt);
                }
                if(cc_txn_drcr == 'C'){
                    sub = -parseAmount(cc_txn_amt);
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
        var page = (ccIndex + 1) + '/' + cc_accounts.length;
        
        $('#cc_account').text(account_name);
        $('#cc_total').html(formatAmount(amount));
        $('#cc_drcr').text(drcr + 'r');
        $('#cc_page').text(page);
        

        $('#ccModal').modal('show');
        
        $("#cc_grid").pqGrid('option', 'dataModel.data', cc_accounts[ccIndex].grid);
        $("#cc_grid").pqGrid('refreshDataAndView');

        $('input[type="command-line"]').focus();//tempararily shift focus
        $("#cc_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 3, focus: true });
    }   
}
            
            
function generateCcData(account_id = 0)
{
    var json = [];
    
    if(account_id != 0){
        var i = saved_cc_txns.findIndex(function(o) {
           return o.acc_id == account_id;
        });
        if(i >= 0){
            $.each(saved_cc_txns[i].cc_txn_list, function(index, obj){
                json.push({'cc_id': obj.cc_id, 'cc_name': obj.cc_name, 'cc_txn_amt': obj.cc_txn_amt, 'cc_txn_drcr': obj.cc_txn_drcr, 'cc_txn_narr': obj.cc_txn_narr});
            });
        }
    }
    
    for(var i=0;i<25;i++){
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
                sub = parseAmount(row.cc_txn_amt);
            }
            if(row.cc_txn_drcr == 'C'){
                sub = -parseAmount(row.cc_txn_amt);
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

</script>