<?php $header = array( 	'title' => 'Purchase Order (Non-Item)' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

<div class="row pb-2">
    <div class="col-md-6 order-1"><h3>Purchase Order (Non-Item)</h3></div>  
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
     <select name="type" id="type" class="form-select" style="width:160px;">
        <option value="item">Item</option>
        <option value="non_item" selected="selected">Non-Item</option>
      </select>

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
        <div class="col-md-3 col-6 card p-2">
          <div class="input-group">
              <span class="input-group-text">Series:</span>
              <?php	echo form_dropdown('voucher_series', $voucher_series_dropdown, $voucher_series,' id="voucher_series" class="selectwidget voucher_series form-control required" '); ?>
          </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
              <label class="input-group-text">Date:</label>
              <input type="text" name="sale_date" id="sale_date" value="<?php echo $sale_date;?>" class="datepicker form-control form-control-sm">
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
                   <option value=""></option>
                   <?php foreach ($party_dropdown as $value) { ?>
                        <option <?= ($value['acc_id'] == $party_id) ? 'selected' : '' ?> value="<?= $value['acc_id'] ?>"><?= $value['acc_name'] ?></option>
                   <?php } ?>
               </select>
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
                </select>
            </div>
        </div>
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
              <label class="input-group-text">Due Date:</label>
              <input type="text" name="due_date" id="due_date" value="<?= $due_date ?>" class="datepicker form-control form-control-sm" required>
            </div>
        </div>
        <div class="col-md-12 col-12 card p-2">
            <div class="input-group">
              <label class="input-group-text">Narration:</label> 
             <textarea  name="narration" class="form-control form-control-sm"><?php if($get_narration_info){
				  echo $get_narration_info['vch_narr'];
			  } ?></textarea>
            </div>
        </div>
      <input type="hidden" name="type" value="non_item">
      <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="billsndrydata" id="billsndrydata">
      </div>
    </div>

   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12"><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-6">
           <h4 class="text-center">Tax Summary</h4>
           <div id="taxgrid_search" style="margin:auto;"></div>  
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-6">
           <h4 class="text-center">Bill Sundry</h4>
        <div id="billsundry_search" style="margin:auto;"></div> 
  </div>

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
   
</form>
<?php echo view('includes/footer_scripts'); 
    $json_data = $account_transactions;
for($i=1;$i<=50;$i++)
    $json_data[] =array("acc_id" => "","account_name"=>'','description'=>'','amount'=>'');

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
   
    var intRegex = /^\d+$/;
    var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

    
$(".deletebtn").on('click',function(){
   confirm_delete(baseurl+"/admin/purchase_order/delete/<?php echo $voucher_txn_id;?>,");         
   return false
})


   $("#submitbtn").on("click",function(){
        var item_checked = [];
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
                        "billsundry_amount" : parseAmount(billsundry_amount),
                     });
            }
            
           
        }
       
        for (var i = 0; i < data.length; i++) {
           var account_id     = data[i]['acc_id'];
           var account_name   = data[i]['account_name'];
           var description    = data[i]['description'];
           var amount         = data[i]['amount'];
           
            if(!amount)
                amount =0;
            final_amount=parseInt(final_amount)+parseInt(amount);
            
            
            if(account_id != '' && account_name != ''){
            item_checked.push({
                   "account_id": account_id,
                    "account_name": account_name,
                    "amount" :parseAmount(amount),
                    "description" :description
                 });
             
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
               
        //    console.log(item_checked);
            $("#itmsdata").val(JSON.stringify(item_checked));
            $("#billsndrydata").val(JSON.stringify(billsundry_item_checked));

            show_loader();
            $("#salefrm").submit();     
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
                source:  <?php echo $acc_json_file;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: false }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();
                    
                    rd.account_name = ui.item.label;
                    rd.acc_id =ui.item.acc_id;
                    $(this).val(ui.item.label);
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.account_name = '';
                rd.acc_id = '';
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
            pageModel: { type: 'local' },
            dataModel: tax_dataModel,
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
                source:  <?php echo $bsd_json_file;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength:0,
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
          
          $("#billsundry_search").pqGrid(tax_newObj2); 
          
        
     });
     
     $(document).on('change', '[name="type"]', function(e){
         var type = $(this).val();
         e.preventDefault();
         if(type == 'item'){
            $(this).val('non_item');
             window.location.href = "<?php echo $base_url; ?>purchase_order/item";
         }
         else if(type == 'non_item'){
             window.location.href = "<?php echo $base_url; ?>purchase_order/non_item";
         }
        
     })
 </script>	
</body>
</html>
