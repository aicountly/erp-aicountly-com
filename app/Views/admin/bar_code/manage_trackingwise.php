<?php $header = array(  'title' => 'Manage Tracking Barcode' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>



<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Manage Tracking Barcode</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
    <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></a>
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
     </li>
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 


        
<div class="col-md-6 order-2 order-md-3">
    <form id="itemfrm" action="" method="POST"> 
    <div class="input-group">

        <span class="input-group-text px-1">Item Name: </span>
        <input id="item" type="text" class="form-control p-2" value="<?= $item_name ?>" required style="width:50px;">
        <input type="hidden" name="item_id" value="<?= $item_id ?>">

        <!-- <span class="input-group-text px-1"> Unit: </span>
        <select name="unit_id" class="form-control p-2" style="width:40px;" required>
            <option value=""></option>
       </select> -->

        <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
    </div>
    </form>

        

  
     
 </div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
        <button class="btn btn-success m-1 itm_grd_btn" type="button" style="display: none;" disabled>Auto Create</button>
 
        <a href="<?php echo history_back();?>" class="btn btn-outline-success showinline-md">Back</a>
  </div> 
</div>


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
      
      <p class="text-center pt-3"><a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a></p>
        
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
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>



</div> 

<div id="validation_errors"></div>

<div>
    <span>Filter: </span>
    <div class="form-check-inline">
      <input type="radio" class="form-check-input grid_radio_btn" id="all_barcodes" name="optradio" value="all_barcodes" checked>
      <label class="form-check-label" for="all_barcodes">All Records</label>
    </div>
    <div class="form-check-inline">
      <input type="radio" class="form-check-input grid_radio_btn" id="existing_barcodes" name="optradio" value="existing_barcodes">
      <label class="form-check-label" for="existing_barcodes">Existing Barcodes</label>
    </div>
    <div class="form-check-inline">
      <input type="radio" class="form-check-input grid_radio_btn" id="unmapped_barcodes" name="optradio" value="unmapped_barcodes">
      <label class="form-check-label" for="unmapped_barcodes">Unmapped Barcodes</label>
    </div>
</div>

<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12 text-center">
    
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <button type="reset" id="submitbtn" class="btn btn-success btn-lg">Reset</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
        
  
        
    </div>
   
</form>

<div class="modal" id="viewModel" style="z-index: 9999">
                
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Dummy</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body"> 
        <div class="col-md-12 text-center">
            <img style="width: 50%;" src="https://sandbox.aicountly.in/public/assets/img/barcode.jpeg">
        </div>
        
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" disabled>Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>

<style>
    .boldcell{font-weight:700;}
</style>
<
<script>

    var item_list = <?= $item_json_file ?>;

    getList(item_list);

    function getList(list) 
    {
        $('input[id="item"]').off('blur'); // unbind event first
        
        if($('input[id="item"]').hasClass('ui-autocomplete-input')) {
            $('input[id="item"]').autocomplete("destroy");
        }
        
        $('input[id="item"]').autocomplete({
            source: list,
            minLength: 0,
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                $('input[name="item_id"]').val(ui.item.item_id);
                // get_item_units(ui.item.item_id);
            }
        })
        .on('focus', function(){
            $(this).autocomplete( "search", "" );
            $('input[id="item"]').val('');
            $('input[name="item_id"]').val('');
            $('select[name="unit_id"]').html('<option value=""></option>');
            reset_grid();
        })
        .on('blur', function(){
            autoSetItem(list);
        })
    }

    function autoSetItem(list)
    {
        if($('input[id="item"]').val() != '' && $('input[name="item_id"]').val() == '')
        {
            var acc = $('input[id="item"]').val();

            var index = list.findIndex(function(obj) {
                var string = obj.label.toLowerCase();
                var text = acc.toLowerCase();
               return  string.includes(text);
            });

            if(index > -1){
                $('input[id="item"]').val(list[index].label);
                $('input[name="item_id"]').val(list[index].item_id);
                // get_item_units(list[index].item_id);
            }
            else{
                $('input[id="item"]').val('');
                $('input[name="item_id"]').val('');
                $('select[name="unit_id"]').html('<option value=""></option>');
                reset_grid();
            }
        }
        if($('input[name="item_id"]').val() == '')
        {
            $('input[id="item"]').val('');
            $('input[name="item_id"]').val('');
            $('select[name="unit_id"]').html('<option value=""></option>');
            reset_grid();
        }
    }

    function get_item_units(item_id)
    {  
        $('select[name="unit_id"]').html('<option value=""></option>');

        if(item_id)
        {
            $.ajax({
                url: '<?php echo $base_url; ?>ajax/get_item_unit_list', 
                type: 'POST',
                data: {item_id: item_id},
                dataType: "json",
                beforeSend: function() {
                    
                },
                success: function (response) {
                 
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    if(response.status){
                        if(response.list.length > 0){
                            var html = `<option value=""></option>`;
                            $.each(response.list, function(index, obj){
                                html += `<option value="${obj.unit_id}">${obj.unit_name}</option>`;
                            });
                            $('select[name="unit_id"]').html(html);
                        }
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

    
    $("#submitbtn").on("click",function(){

        var item_id = $('input[name="item_id"]').val();
        if(!item_id){
            alert_notification('Please select Item Name');
            return false;
        }
    
        var data = $("#grid_search").pqGrid('option', 'dataModel.data');
        var final_data = [];
        
        var error = 0;
        data.forEach(function(obj){
          
            if(!obj.barcode_id){

                var barcode_standard = obj.barcode_standard;
                var barcode = obj.barcode;
                var batch_id = obj.batch_id;
                var tracking_id = obj.tracking_id;

                if(!barcode || !barcode_standard){
                    error = 1;
                    return false;
                }

                final_data.push({
                    'item_id'   : item_id,
                    'batch_id'  :  batch_id,
                    'tracking_id'  :  tracking_id,
                    'barcode'  :  barcode,
                    'barcode_standard'  :  barcode_standard,
                });
            }
        });
        if(error){
            alert_notification('Please fill data properly');
            return false;
        }
     
        if(final_data.length == 0){
            alert_notification('Nothing to save');
            return false;
        }

        

        $.ajax({
            url: '<?php echo $base_url; ?>bar_code/add_bulk_barcode', 
            type: 'POST',
            data: {data: final_data},
            dataType: "json",
            beforeSend: function() {
                
            },
            success: function (response) {
             
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    $('#itemfrm').submit();
                    alert_success(response.message);
                }
                else{
                    alert_notification(response.message);
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
                grid.refreshRow({ rowIndx: rowIndx });
            },
        });
   
    });

    function reset_grid()
    {
        $('.itm_grd_btn').css('display', 'none');

        gridDataModel = [];
        $("#grid_search").pqGrid('option', 'dataModel.data', []);
        $("#grid_search").pqGrid('refreshDataAndView');
        $("#all_barcodes").prop("checked", true);
    }


    var gridDataModel = [];

    $(document).on('submit', '#itemfrm', function(e){
        e.preventDefault();

        var item_id = $('input[name="item_id"]').val();
        // var unit_id = $('select[name="unit_id"]').val();

        if(!item_id){
            alert_notification('Something went wrong! refresh the page')
            return false;
        }

        
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
                    $('.itm_grd_btn').css('display', 'inline-block');

                    gridDataModel = response.data;
                    $("#grid_search").pqGrid('option', 'dataModel.data', response.data);
                    $("#grid_search").pqGrid('refreshDataAndView');
                    $("#all_barcodes").prop("checked", true);
                }
                else{
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

    <?php if($item_id != 0) { ?>
        $("#itemfrm").trigger('submit');
    <?php } ?>


     $(function () {

        var barcode_types = <?= json_encode($types) ?>;

        var colModel = [
        { title: "TRACKING NO", dataIndx: "tracking_no", width: 100, editable: false},
        { title: "BATCH NO", dataIndx: "batch_no", width: 100, editable: false},
        { title: "MFG DATE", dataIndx: "batch_mfr", width: 100, editable: false},
        { title: "EXP DATE", dataIndx: "batch_expiry", width: 100, editable: false},
        { title: "BARCODE TYPE", dataIndx: "barcode_standard", width: 100,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
              type: "select",
              options: barcode_types,
            },
        },
        { title: "BARCODE", dataIndx: "barcode", width: 100,

        },
          
        { title: "ACTION", editable: false, minWidth: 50, sortable: false,
            render: function (ui) {

                var rowData = ui.rowData;
                var html = ``;

                if(!rowData.barcode_id)
                    html += `<a href="javascript:void(0)" type='button' class='add_btn mx-1'>
                                <img src="https://sandbox.aicountly.in/public/assets/img/icon-add.png">
                            </a>`;
                if(rowData.barcode_id)
                    html += `<a href="javascript:void(0)" type='button' class='edit_btn mx-1'>
                                <img src="https://sandbox.aicountly.in/public/assets/img/icon-done.png">
                            </a>
                            <a href="javascript:void(0)" type='button' class='view_btn mx-1'>
                                <img src="https://sandbox.aicountly.in/public/assets/img/icon-view.png">
                            </a>`;

                return html;
            },
            postRender: function (ui) {
                var rowIndx = ui.rowIndx,
                    grid = this,
                    $cell = grid.getCell(ui);

                $cell.find(".edit_btn")
                .bind("click", function (evt) {
                    update(rowIndx, grid);
                });

                $cell.find(".add_btn")
                .bind("click", function (evt) {
                    add(rowIndx, grid);
                });

                $cell.find(".view_btn")
                .bind("click", function (evt) {
                    view(rowIndx, grid);
                });
            }
        }           
        ];
            
        var dataModel = {"data": []}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
            height: 420,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            // change: calculateSummary,
            // dataReady: calculateSummary,   
            // columnTemplate: { render: commentRender },
            colModel: colModel,  
            numberCell: { show: true },
            filterModel: { mode: 'OR'},
            editable: true,
            cellSave: function(evt, ui){
                   this.refresh();
               },
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
            wrap:false,
            showTitle: true,
            postRenderInterval: -1, //synchronous post rendering.
            create: function (evt, ui) {// make first row auto selected
            this.widget().pqTooltip();
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });
              },

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

        $('input.grid_radio_btn').change(function() {

            if(this.value == 'existing_barcodes'){
                gridDataModel2 = gridDataModel.filter(function (el) {
                      return el.barcode_id ? true : false;
                });                
            }
                
            if(this.value == 'unmapped_barcodes'){
                gridDataModel2 = gridDataModel.filter(function (el) {
                      return !el.barcode_id ? true : false;
                });
            }
               
            if(this.value == 'all_barcodes'){
                gridDataModel2 = gridDataModel;
            }
                
         
            $("#grid_search").pqGrid('option', 'dataModel.data', gridDataModel2);
            $("#grid_search").pqGrid('refreshDataAndView');
            
        });
       

        function update(rowIndx, grid) {

            rowData = grid.getRowData({ rowIndx: rowIndx });
           
            var barcode_standard = rowData.barcode_standard;
            var barcode = rowData.barcode;
            var barcode_id = rowData.barcode_id;

            if(!barcode || !barcode_standard){
                alert_notification('Please fill data properly');
                return false;
            }

            $.ajax({
                url: '<?php echo $base_url; ?>bar_code/update_barcode', 
                type: 'POST',
                data: {barcode_id: barcode_id, barcode: barcode, barcode_standard: barcode_standard},
                dataType: "json",
                beforeSend: function() {
                    
                },
                success: function (response) {
                 
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    if(response.status){
                        alert_success(response.message);
                        grid.refreshRow({ rowIndx: rowIndx });
                    }
                    else{
                        alert_notification(response.message);
                        grid.refreshRow({ rowIndx: rowIndx });
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
                    grid.refreshRow({ rowIndx: rowIndx });
                },
            });
        }

        function add(rowIndx, grid) {

            rowData = grid.getRowData({ rowIndx: rowIndx });
           
            var barcode_standard = rowData.barcode_standard;
            var barcode = rowData.barcode;
            var batch_id = rowData.batch_id;
            var tracking_id = rowData.tracking_id;

            if(rowData.barcode_id){
                alert_notification('Somethings wrong');
                return false;
            }

            if(!barcode || !barcode_standard){
                alert_notification('Please fill data properly');
                return false;
            }

            var item_id = $('input[name="item_id"]').val();
            if(!item_id){
                alert_notification('Please select Item Name');
                return false;
            }

            $.ajax({
                url: '<?php echo $base_url; ?>bar_code/add_barcode', 
                type: 'POST',
                data: {item_id: item_id, batch_id: batch_id, tracking_id: tracking_id, barcode: barcode, barcode_standard: barcode_standard},
                dataType: "json",
                beforeSend: function() {
                    
                },
                success: function (response) {
                 
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    if(response.status){
                        alert_success(response.message);
                        rowData.barcode_id = response.barcode_id;
                        grid.refreshRow({ rowIndx: rowIndx });
                    }
                    else{
                        alert_notification(response.message);
                        grid.refreshRow({ rowIndx: rowIndx });
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
                    grid.refreshRow({ rowIndx: rowIndx });
                },
            });
        }

        function view(rowIndx, grid) {

            rowData = grid.getRowData({ rowIndx: rowIndx });
           
            $('#viewModel').modal('show');
        }


     });



 </script>  
</body>
</html>
