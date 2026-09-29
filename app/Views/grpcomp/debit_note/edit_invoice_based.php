<?php $header = array( 	'title' => 'Update Debit Note' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>
<style>
    .salesgrid.gridtable input.form-control-plaintext{text-align:center;}
    .gridtable .row .col input,.gridtable .row .col select{border:0px;}
    .taxgrid .row{display: grid; grid-template-columns:20% 20% 20% 20% 20%;}
    .billgrid .row{ display: grid; grid-template-columns:10% 40% 25% 25%;}
    .gridtable .foot.row{ grid-template-columns:100%;}
    .form p, .gridtable p, .form-control-plaintext{margin:0px; padding:0px !important;}
    .form-control-sm{padding: .375rem .2rem;}
    .card p{margin-bottom:3px;}
</style>

<div class="row pb-2">
    <div class="col-md-6"><h3>Update Debit Note (Invoice)</h3></div>  
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
   <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
    <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
    </div>
    <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" value="1" id="ccCheck">
    <label class="form-check-label" for="ccCheck">Cost Centre</label>
    </div>

    <select form="salefrm" name="currency_id" class="form-select d-inline-block" style="width:160px;">
       <?php foreach ($currency_list as $value) { ?>
            <option <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?> value="<?= $value['comp_currency_id'] ?>"><?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
       <?php } ?>
   </select>
   </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
    <a href="#" class="btn btn-success">Party Dashboard</a>
    <button class="btn btn-success m-1" type="button">Templates</button>
    <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
  </div> 
</div>
<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
      <div class="row m-0 p-0 align-items-center">
        <div class="row">
            <div class="col-md-3 col-6 card p-2">
                <div class="input-group">
                  <label class="input-group-text">Date:</label>
                  <input type="text" name="sale_date" id="sale_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm" disabled>
                </div>
            </div>
            <div class="col-md-3 col-6 card p-2">
                <div class="input-group">
                    <label class="input-group-text px-2">Against:</label>
                    <input type="text" name="against" value="<?= $against ?>" class="form-control form-control-sm" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
              <span class="input-group-text">Series:</span>
              <?php	echo form_dropdown('voucher_series', $voucher_series_dropdown, $voucher_series,' id="voucher_series" class="selectwidget voucher_series form-control required" '); ?>
          </div>
        </div>  
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Voucher No:</label> 
                <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_no;?>" disabled>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Purchase Type:</label>
                <input type="text" name="purchasetype" class="form-control form-control-sm" disabled>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Party:</label>
                <select name="party_id" class="form-select required" required>
                   <option data-is_bbb="0" value=""></option>
                   <?php foreach ($party_dropdown as $value) { ?>
                        <option <?= ($value['acc_id'] == $party_id) ? 'selected' : '' ?>  data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_id'] ?>"><?= $value['acc_name'] ?></option>
                   <?php } ?>
               </select>
            </div>
        </div>
        <div class="col-md-9 col-12 card p-2">
            <div class="input-group">
              <label class="input-group-text">Narration:</label> 
             <textarea  name="narration" class="form-control form-control-sm"><?php if($get_narration_info){
				  echo $get_narration_info['vch_narr'];
			  } ?></textarea>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
                <label class="input-group-text">Dr. Note No:</label> 
                <input type="text" name="debit_no" class="form-control form-control-sm">
            </div>
        </div>

        <input type="hidden" name="itmsdata" id="itmsdata">
        <input type="hidden" name="bbbdata" id="bbbdata">
        <input type="hidden" name="ccdata" id="ccdata">
        <input type="hidden" name="billsndrydata" id="billsndrydata">
      </div>
    </div>

   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12"><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-5">
           <h4 class="text-center">Tax Summary</h4>
           <div id="taxgrid_search" style="margin:auto;"></div>  
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-7">
           <h4 class="text-center">Bill Sundry</h4>
        <div id="billsundry_search" style="margin:auto;"></div> 
  </div>

    </div> 
    
     <div class="col-12 text-center">
         <br><br>
         <input type="file" class="">
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <button type="reset" id="submitbtn" class="btn btn-success btn-lg">Reset</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-success btn-lg">Quit</a>
        <a href="javascript:void(0);"  class="deletebtn btn btn-danger btn-lg">Delete</a>
    </div>
        
    </div>
   
</form>

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

<?php echo view('includes/footer_scripts'); 
$json_data = $account_transactions;
for($i=1;$i<=50;$i++)
    $json_data[] =array("acc_id" => "","account_name"=>'','description'=>'','amount'=>'','is_cc'=>'');

for($i=1;$i<=10;$i++)
    $tax_json_data[] =array("id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'');

$billsundry_json_data = $sundry_transactions;
for($i=1;$i<=10;$i++)
    $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_rate'=>'','billsundry_amount'=>'');
?>

<style>
    .boldcell{font-weight:700;}
</style>

<script>
var accnt_balances = {};
$(document).on('change', '#sale_date', function(e){

        var voucher_date = $("#sale_date").val();
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
   
$(".deletebtn").on('click',function(){
   confirm_delete(baseurl+"/admin/debit_note/delete/<?php echo $voucher_txn_id;?>,");         
   return false
})
    var bbb_data = [];
    var bbb_accounts = {}; //json, only one account ie party

    var cc_data = [];
    var cc_accounts = [];

    var item_checked = [];

   $("#submitbtn").on("click",function(){

        cc_data = [];
        cc_accounts = [];

        bbb_data = [];
        bbb_accounts = {};

        var is_bbb = $('select[name="party_id"] option:selected').data('is_bbb');
        var total = 0;

        item_checked = [];
        var final_item_id =[];
        var billsundry_item_checked = [];
        var data = $("#grid_search").pqGrid('option', 'dataModel.data');
        var billsundry_data = $("#billsundry_search").pqGrid('option', 'dataModel.data');
        var final_amount ="0";
         
        var sdateFrom = '<?php echo $fy_begndt;?>';
        var sdateTo   = '<?php echo $fy_end;?>';
        var sdateCheck  =  $("#sale_date").val();
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
        
        for (var j = 0; j < billsundry_data.length; j++) {
            var billsundry_id       = billsundry_data[j]['billsundry_id'];
            var billsundry_name     = billsundry_data[j]['billsundry_name'];
            var billsundry_rate     = billsundry_data[j]['billsundry_rate'];
            var billsundry_amount   = billsundry_data[j]['billsundry_amount'];
            
            
            if(billsundry_id != '' && billsundry_name != ''){
                billsundry_item_checked.push({
                        "billsundry_id": billsundry_id,
                        "billsundry_name": billsundry_name,
                        "billsundry_rate": parseAmount(billsundry_rate),
                        "billsundry_amount" :parseAmount(billsundry_amount),
                     });
                total += parseAmount(billsundry_amount);
            }
            
           
        }
       
        for (var i = 0; i < data.length; i++) {
           var account_id     = data[i]['acc_id'];
           var is_cc            = data[i]['is_cc'];
           var account_name   = data[i]['account_name'];
           var description    = data[i]['description'];
           var amount         = data[i]['amount'];
           
            if(!amount)
                amount =0;
            final_amount=parseInt(final_amount)+parseInt(amount);
            
            
            if(account_id && account_name != ''){
                item_checked.push({
                   "account_id": account_id,
                    "account_name": account_name,
                    "amount" :parseAmount(amount),
                    "description" :description
                 });

                total += parseAmount(amount);
                if(is_cc)
                {   
                    var drcr = 'C';
                    
                    if($('#ccCheck').is(":checked"))
                    {   //before cc data
                        cc_accounts.push({  
                            "account_id"    : account_id,
                            "account_name"  : account_name,
                            "drcr"          : drcr,
                            "amount"        : parseAmount(amount),
                            "grid"          : generateCcData(account_id)
                        }); 
                    }
                    else{
                        cc_data.push({ 
                            "cc_id"       : 1,
                            "account_id"  : account_id,
                            "cc_name"     : 'UNDEFINED',
                            "cc_txn_amt"  : parseAmount(amount),
                            "cc_txn_drcr" : drcr,
                            "cc_txn_narr" : ''
                        });
                    }
                    
                }
             
            }
        }
          
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
          
        if( $("#voucher_series").val()==''  || selerror=='1'){
            alert_notification("Kindly fill the form properly!!!");
            return false;   
        }
          
        else if(item_checked.length==0 || $("#voucher_series").val()=='' || final_amount=='' || final_amount=='0'){
            alert_notification("Kindly fill the details correctly !!!");
            return false;   
        }
        else if(sdatevalidate==0){
            alert_notification("voucher date is worng!!!");
            return false;    
        } 
        else{

            show_loader();
            $("#itmsdata").val(JSON.stringify(item_checked));
            $("#billsndrydata").val(JSON.stringify(billsundry_item_checked));
            $("#ccdata").val(JSON.stringify(cc_data));

            if($('#bbbCheck').is(":checked") && is_bbb){
                readyDebitNoteBills(total);
            }
            else if($('#ccCheck').is(":checked") && cc_accounts.length > 0){
                readyCc();
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
            var total = 0,
                sub = 0,
                data = this.option('dataModel.data');
    
            data.forEach(function(row){
                
                if(row.amount == '' || typeof row.amount == 'undefined'){
                    sub = 0;
                }else{
                    sub = row.amount;
                } 
             
                total  += parseAmount(sub);
            })
    
            var totalData = {
                    drcr      : "Total",
                    amount    : total,
                    pq_rowcls : 'grid_footer_color',
                    summaryRow: true
                }
            
            this.option('summaryData', [totalData]);
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
            $inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/ajax/get_accounts",
                selectItem: { on: true }, //custom option
                highlightText: { on: false }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    
                    rd.account_name = ui.item.label;
                    rd.acc_id =ui.item.acc_id;
                    $(this).val(ui.item.label);
                    rd.is_cc = ui.item.is_cc;
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.account_name = '';
                rd.acc_id = '';
                rd.is_cc = '';
            }).focusout(function () {   
                 if(rd.acc_id != '')
                    {     
                     if(accnt_balances.hasOwnProperty(rd.acc_id) ==false ) {
                      var voucher_date = $("#sale_date").val();
                      var return_response = function (){
                           var accbalance=0;
                            $.ajax({
                                'async': false,
                                'type': "GET",
                                'global': false,
                                'dataType': 'html',
                                'url': "<?php echo $base_url; ?>ajax/accountbalance/"+rd.acc_id+"/"+voucher_date,
                                'success': function (data) {
                                    accbalance = data;
                                }
                            });
                            return accbalance;
                        }();
                        accnt_balances[rd.acc_id] = return_response;
                     }
                    else
                     return_response =  accnt_balances[rd.acc_id];
                    
                   
                       rd.account_balance = return_response;
                    }
                if(rd.acc_id == '')
                {
                    rd.account_name = '';
                    rd.acc_id = '';
                    rd.is_cc = '';
                }
            });
        }
        var colModel = [
            { title: "PARTICULARS", dataIndx: "account_name", width: 100,dataType: "string",cls: 'pq-drop-icon pq-side-icon',
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
            { title: "DESCRIPTION", width: 100, dataType: "string", dataIndx: "description",
                editable: function (ui) {
                   var account_id = ui.rowData['acc_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                },
            },

            { title: "AMOUNT", width: 20, align: "right",dataIndx: "amount",dataType: "float",
                editable: function (ui) {
                   var account_id = ui.rowData['acc_id'];
                    if (account_id != '') {
                        return true;
                    }
                    return false;
                },
                  validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
                  render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.amount != ''){
                        rd.amount = parseAmount(rd.amount);
                        return formatAmount(rd.amount);   
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
                return false;
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
        
        
        var tax_dataModel = {"data":<?php echo json_encode($tax_json_data);?>}
         var tax_colModel = [
            { title: "TAX RATE", dataIndx: "tax_rate",editable: false, width: 100},
            { title: "TAX AMT", width: 100, dataType: "float", dataIndx: "tax_amt"},
            { title: "IGST", width: 100,  dataIndx: "igst" ,dataType: "float",format: '##,###.00'},
            { title: "CGST", width: 100,  dataIndx: "cgst" ,dataType: "float",format: '##,###.00'},
            { title: "SGST", width: 100,  dataIndx: "sgst" ,dataType: "float",format: '##,###.00'}
            ];
         var tax_newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            dataModel: tax_dataModel,
            pageModel: { type: 'local' },
            colModel: tax_colModel,  
            numberCell: { show: true },
            wrap:false,
            editable: false,
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
          $("#taxgrid_search").pqGrid(tax_newObj);
          
          
         function billsundry_calculateSummary() {
            var itempriceTotal = 0,
                itemamountTotal = 0,
                itemqtyTotal=0,
                data = this.option('dataModel.data'),
                len  = data.length;

            data.forEach(function(row){
               
                if(row.billsundry_amount == '' || typeof row.billsundry_amount == 'undefined'){
                    var billsundry_amount =0;
                }else
                 var billsundry_amount =  row.billsundry_amount;
                 
               
                itemamountTotal += parseAmount(billsundry_amount);
               
            })

            var totalData = {
                    billsundry_name: "Total",
                    billsundry_rate  : "",
                    billsundry_amount: itemamountTotal,
                    pq_rowcls: 'grid_footer_color',
                    summaryRow: true
                }
                
                this.option('summaryData', [totalData]);
        }

        var billsundry_autoComplete = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var element= {};

        $inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/ajax/get_billsundry_items",
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 3,
                select: function(event, ui) {
                    event.preventDefault();
                    rd.billsundry_name = ui.item.label;
                    rd.billsundry_id = ui.item.id;
                     
                    $(this).val(ui.item.label);
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
            });
           
        }

        var billsundry_dataModel = {"data":<?php echo json_encode($billsundry_json_data);?>}
         var billsundry_colModel = [
             { title: "BILL SUNDRY", width: 100, dataType: "string", align: "left",dataIndx: "billsundry_name" ,
             editor: {                   
                          type: "textbox",
                          init: billsundry_autoComplete,
                          options: []
                      },
                      
            },
             { title: "RATE", width: 100,  align: "right",dataIndx: "billsundry_rate" ,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_rate != ''){
                        rd.billsundry_rate = parseAmount(rd.billsundry_rate);
                        return formatAmount(rd.billsundry_rate);   
                    }
                    return '';
                }
            },
             { title: "AMOUNT", width: 100, align: "right", dataIndx: "billsundry_amount" ,dataType: "float",
                render: function( ui ) {
                    var rd = ui.rowData;
                    if(rd.billsundry_amount != ''){
                        rd.billsundry_amount = parseAmount(rd.billsundry_amount);
                        return formatAmount(rd.billsundry_amount);   
                    }
                    return '';
                }
            },
            ];
          
          var tax_newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 420,
            selectionModel: { type: 'row' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
            pageModel: { type: 'local' },
            numberCell: { show: true },
            change: billsundry_calculateSummary, 
            draw:false,
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
          
          $("#billsundry_search").pqGrid(tax_newObj2); 
          
        
     });

    //------------------------------------------------------------------------------ Cost Centre 

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
            $("#salefrm").submit();
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
    function readyCc()
    {
        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/ajax/getCc",
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
            $('#cc_total').html(formatAmount(amount));
            $('#cc_drcr').text(drcr + 'r');
            

            $('#ccModal').modal('show');
            
            $("#cc_grid").pqGrid('option', 'dataModel.data', cc_accounts[ccIndex].grid);
            $("#cc_grid").pqGrid('refreshDataAndView');
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


        //------------------------------------------------------------------------------ Bill By Bill

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
    if(status){
        saveBillByBillData();
        $('#billsModal').modal('hide');
        show_loader();
        $("#bbbdata").val(JSON.stringify(bbb_data));

        if($('#ccCheck').is(":checked")){
            readyCc();
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

function readyDebitNoteBills(total_amount)
{
    var account_id = $('select[name="party_id"]').val();
    var account_name = $('select[name="party_id"] option:selected').text();

    bbb_accounts = {
        'account_id'    : account_id,
        'account_name'  : account_name,
        'drcr'          : 'D',
        'amount'        : parseAmount(total_amount),
        'grid'          : generateBillData(account_id)
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
    
    for(var i=0;i<50;i++){
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
     
 </script>	
</body>
</html>
