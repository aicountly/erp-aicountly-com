<?php $header = array(  'title' => 'Forex Rates' ); ?>
<?php if(!session()->get('ses_company_id')){ ?>

<?php echo view('includes/header2',$header); ?>

<div class="row mb-4 myalltabs px-3 bg bg-success">
  <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
    <a href="<?php echo base_url();?>/home/open_company" ><span id="#" class="btn tabslist">My Company</span></a>
    <a href="<?php echo base_url();?>/sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
    <a href="<?php echo base_url();?>/archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
  </div>
</div>

<div class="content"><div class="pb-5">

<?php } else { ?>

<?php echo view('includes/header',$header); ?>

<?php } ?>

<style>
.gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
.gridtable .foot.row{ grid-template-columns:100% ;}
</style>


<div class="row align-items-center">
    <div class="col-md-6">
        <h3>Manage- <?= $currency['curr_name'] . ' (' . $currency['curr_symbol'] . ')' ?></h3>
    </div>
    <div class="col-md-6 text-end">
        <div class="taskmenus">
            <a href="<?php echo history_back();?>" class="hideinline-md">
                <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
            </a>
        </div>
    </div>
    
    <div class="collapse listmenu mt-1" id="listmenu">

        <a href="javascript:void(0);" class="btn btn-success" id="addForex">Add Forex Rate</a>
        <a href="javascript:void(0);" class="btn btn-success" id="updateForex">Update Forex Rate</a>

        <div class="float-md-end d-inline-block"> 
            

            <a href="javascript:void();" onclick="window.history.back()"  class="btn btn-outline-success btn-sm showinline-md">« Back</a>

        </div>
    </div>
</div>



<?php if ($session->getFlashdata('message')) { ?>
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="btn-close" data-bs-dismiss="alert">
    </button>
    <?php echo $session->getFlashdata('message'); ?>
</div>
<?php } ?>
<?php if ($session->getFlashdata('error_array_message')) { ?>
<div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
    <button type="button" class="btn-close" data-bs-dismiss="alert">
    </button>
    <?php $errors = $session->getFlashdata('error_array_message'); ?>
    <ul>
        <?php foreach($errors as $error) { ?>
        <li>
            <?php echo $error ?>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<div id="validation_errors"></div>

<div class="mt-5" id="search_grid"  style="margin:auto;">
</div>



<!-- The Modal -->
<div class="modal" id="addForexRate">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Forex Rates</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div id="validation_errors_addForexRate"></div>
        <form id="addForexRateForm" action="<?= $base_url ?>currency/add_forex_rates">

            <input type="hidden" name="company_id" value="<?= $company_id ?>">
            <input type="hidden" name="comp_currency_id" value="<?= $currency['comp_currency_id'] ?>">
            
            <div class="col-12">
                <label>Date</label>
                <input type="text" name="curr_date" value="" class="form-control datepicker2" required>
            </div>
            
            <div class="col-12">
                <label>Rate per</label>
                <select class="form-select" name="rate_type">
                    <?php foreach ($rates as $key => $value) { ?>
                        <option value="<?= $value ?>"><?= $value ?></option>
                    <?php } ?>
                </select>   
            </div>

            <div class="col-12">
                <label>Value</label>
                <input type="text" name="rate" value="" class="form-control" required>
            </div>
            <div class="col-12">
                <label>Conversion Value</label>
                <input type="text" name="conversion_rate" value="" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-success float-end mt-2">Add</button>
        </form>
      </div>


    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal" id="updateForexRate">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Forex Rates</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div id="validation_errors_updateForexRate"></div>
        <form id="updateForexRateForm" action="<?= $base_url ?>currency/update_forex_rates">

            <input type="hidden" name="company_id" value="<?= $company_id ?>">
            <input type="hidden" name="comp_currency_id" value="<?= $currency['comp_currency_id'] ?>">
            <input type="hidden" name="forex_rate_id" value="">
            
            <div class="col-12">
                <label>Date</label>
                <input type="text" name="curr_date" value="" class="form-control datepicker2" required>
            </div>
            
            <div class="col-12">
                <label>Rate per</label>
                <select class="form-select" name="rate_type">
                    <?php foreach ($rates as $key => $value) { ?>
                        <option value="<?= $value ?>"><?= $value ?></option>
                    <?php } ?>
                </select>   
            </div>

            <div class="col-12">
                <label>Value</label>
                <input type="text" name="rate" value="" class="form-control" required>
            </div>
            <div class="col-12">
                <label>Conversion Value</label>
                <input type="text" name="conversion_rate" value="" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-success float-end mt-2">Add</button>
        </form>
      </div>


    </div>
  </div>
</div>

</div></div>

<?php
$json_items = array();

?>

<?php echo view('includes/footer_scripts'); ?>
<script>
var forex_rate_array = [];

        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = $toolbar.find(".filterCondition").val(),
                dataIndx = '',//$toolbar.find(".filterColumn").val(),
                filterObject;

            if (dataIndx == "") {//search through all fields when no field selected.
                filterObject = [];
                var CM = $grid.pqGrid("getColModel");
                for (var i = 0, len = CM.length; i < len; i++) {
                    var dataIndx = CM[i].dataIndx;
                    filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
                }
            }
            else {//search through selected field.
                filterObject = [{ dataIndx: dataIndx, condition: condition, value: value}];
            }
            $grid.pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            });
        }
        
        
    var colModel = [

            { title: '', width: 100, dataIndx: "checkbox" },
            { title: "Date", width: 180, dataIndx: "curr_date"},
            { title: "Source", width: 180, dataIndx: "forex_type"},
            { title: "Rate per INR", width: 180, dataIndx: "rate_per_inr", align: "right"},
            { title: "Rate per FCY", width: 180, dataIndx: "rate_per_fcy", align: "right"},
           
        ];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            postData : {'company_id': '<?= $company_id ?>', 'comp_currency_id': '<?= $currency['comp_currency_id'] ?>' },
            url: "<?php echo base_url();?>/admin/currency/ajax_forex_rate_list",
             getData: function (dataJSON) {
                 forex_rate_array = dataJSON.data;
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: dataJSON.data };
              }
        };

        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR' },
            numberCell: { show: false },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".items_row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
            },
            toolbar: {
                cls: "pq-toolbar-search",
                items: [  
                    { 
                        type: 'textbox', 
                        label: 'Filter: ',
                        attr: 'placeholder="Enter your keyword"', 
                        cls: "filterValue",
                        listener: { keyup: filterhandler }
                    },
                    { 
                        type: 'select',                         
                        cls: "filterCondition",
                        listener: filterhandler,
                        options: [
                            { "begin": "Begins With" },
                            { "contain": "Contains" },
                            { "end": "Ends With" },
                            { "notcontain": "Does not contain" },
                            { "equal": "Equal To" },
                            { "notequal": "Not Equal To" },
                            { "empty": "Empty" },
                            { "notempty": "Not Empty" },
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            { "regexp": "Regex" }
                        ]
                    }
                ]
            }
        };

        newObj.rowDblClick    = function(event, ui) {
            var rowData      = ui.rowData;
            var forex_rate_id     = rowData.forex_rate_id;
         
            update_forex_rate(forex_rate_id)
        } 

        newObj.cellKeyDown = function(evt, ui) {
            var rowData      = ui.rowData;
            var forex_rate_id     = rowData.forex_rate_id;
            
            if (evt.keyCode==13){
                update_forex_rate(forex_rate_id)
            }
        }
         
     var $grid = $("#search_grid").pqGrid(newObj);

     

         $("#search_grid").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#search_grid").pqGrid('saveState');
       });
    
        
     $(document).on('click','#select_all',function(){
        if(this.checked){
              $('.checkbox').each(function(){ this.checked = true; });
              $(".editbtn").addClass("disabled");
              $(".duplicatebtn").addClass("disabled");
        }else{
              $('.checkbox').each(function(){ this.checked = false; });
              $(".editbtn").removeClass("disabled");
              $(".duplicatebtn").removeClass("disabled");
           }
       });
    
 
    $(document).on('click','.items_row', function(e) {   
        $(':checkbox').prop('checked', false);
        $("#updateForex").removeClass("disabled");

        $('.items_row').removeClass('selected_cell');
        $(this).addClass('selected_cell');

        if($(this).is(":checked"))
            $(this).prop('checked', false);       
        else
         $(this).prop('checked', true);                           
    });

    $(document).on('click', '#addForex', function(){

        $('#addForexRateForm')[0].reset();
        $('#addForexRate').modal('show');
    });

    $(document).on('submit', '#addForexRateForm', function(e){
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);

        $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: formData,
            // dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('#addForexRateForm button[type="submit"]').attr('disabled', 'disabled');
                $('#validation_errors_addForexRate').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                stop_loader();

                if(response.status){
                    alert_success(response.message);
                    // window.location.reload();
                    $('#addForexRate').modal('hide');
                    $('#search_grid').pqGrid('refreshDataAndView');
                }
                else{
                    alert_notification(response.message);
                    if(response.errors.length > 0)
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
                        $('#validation_errors_addForexRate').html(html);
                    }  
                }
                
            },
            complete: function() {
                stop_loader();
                $('#addForexRateForm button[type="submit"]').attr('disabled', false);
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

    function update_forex_rate(forex_rate_id)
    {
        var index = forex_rate_array.findIndex(function(object){
            return object.forex_rate_id == forex_rate_id;
        });

        if(index > -1){
            $('#validation_errors_updateForexRate').html('');

            $('#updateForexRateForm input[name="forex_rate_id"]').val(forex_rate_array[index]['forex_rate_id']);
            $('#updateForexRateForm input[name="curr_date"]').val(forex_rate_array[index]['curr_date']);
            $('#updateForexRateForm select[name="rate_type"]').val('INR');
            $('#updateForexRateForm input[name="rate"]').val(forex_rate_array[index]['rate_per_inr']);
            $('#updateForexRateForm input[name="conversion_rate"]').val(forex_rate_array[index]['rate_per_fcy']);

            $('#updateForexRate').modal('show');
        }
        else{
            alert_notification('Something went wrong');
        }
    }

    $(document).on('click', '#updateForex', function(){

        var sel_id = $('.selected_cell').data('id'); 
        var ischeckled =  $('.items_row:checked').length;  
        if(ischeckled==0){
            alert_notification("First select a forex rate to update!!");
        }
        else{
            if(sel_id == '')
                return false;
            else
                update_forex_rate(sel_id);
        }
    });

    $(document).on('submit', '#updateForexRateForm', function(e){
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);

        $.ajax({
            url: form.attr('action'), 
            type: 'POST',
            data: formData,
            // dataType: "json",
            processData: false,
            cache: false,
            contentType: false,
            beforeSend: function() {
                show_loader();
                $('#updateForexRateForm button[type="submit"]').attr('disabled', 'disabled');
                $('#validation_errors_updateForexRate').html('');
            },
            success: function (response) {
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                stop_loader();

                if(response.status){
                    alert_success(response.message);
                    // window.location.reload();
                    $('#updateForexRate').modal('hide');
                    $('#search_grid').pqGrid('refreshDataAndView');
                }
                else{
                    alert_notification(response.message);
                    if(response.errors.length > 0)
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
                        $('#validation_errors_updateForexRate').html(html);
                    }  
                }
                
            },
            complete: function() {
                stop_loader();
                $('#updateForexRateForm button[type="submit"]').attr('disabled', false);
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

    $('#addForexRateForm input[name="rate"]').on('keydown', function (e) {
        return IsNumericRate(this, e.keyCode);
         
    });
    $('#addForexRateForm input[name="rate"]').on('keyup', function (e) {

        var rate = $('input[name="rate"]').val();
        if(rate != '' && rate != 0){
            var conversion_rate = Math.round((1/rate) * 100000000) / 100000000;
            $('#addForexRateForm input[name="conversion_rate"]').val(conversion_rate);
        }
        else{
           $('#addForexRateForm input[name="conversion_rate"]').val(''); 
        }
    });

    $('#updateForexRateForm input[name="rate"]').on('keydown', function (e) {
        return IsNumericRate(this, e.keyCode);
         
    });
    $('#updateForexRateForm input[name="rate"]').on('keyup', function (e) {

        var rate = $('#updateForexRateForm input[name="rate"]').val();;
        if(rate != '' && rate != 0){
            var conversion_rate = Math.round((1/rate) * 100000000) / 100000000;
            $('#updateForexRateForm input[name="conversion_rate"]').val(conversion_rate);
        }
        else{
           $('#updateForexRateForm input[name="conversion_rate"]').val(''); 
        }
    });
        
    var isShiftt = false;
    function IsNumericRate(input, keyCode) {
        if (keyCode == 16) {
            isShiftt = true;
        }
        //Allow only Numeric Keys.
        if (((keyCode >= 48 && keyCode <= 57) || keyCode == 8 || keyCode <= 37 || keyCode <= 39 || (keyCode >= 96 && keyCode <= 105) || keyCode == 190 || keyCode == 110) && isShiftt == false) {

            if(input.value.includes(".") && (keyCode == 190 || keyCode == 110)){
                return false
            }
            if(input.value.includes(".")){
                var arr = input.value.split(".");

                if(arr[1].length >= 8 && ((keyCode >= 96 && keyCode <= 105) || (keyCode >= 48 && keyCode <= 57)))
                {
                    // console.log('flag2');
                    return false;
                }
            }
            else{
                if(input.value.length >= 6 && ((keyCode >= 96 && keyCode <= 105) || (keyCode >= 48 && keyCode <= 57))){
                    // console.log('flag3');
                    return false;
                }
            }

            return true;
        }
        else {
            return false;
        }
    };

</script>
</body>
</html>