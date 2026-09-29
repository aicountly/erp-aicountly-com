<?php $header = array(  'title' => $voucher_name ); ?>
<?php echo view('includes/header',$header);
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
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
<div class="row mb-2">
    <div class="col-md-6 order-1"> <h3><?= $voucher_name ?></h3>   </div>
    <div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
    <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>
                <a href="javascript:void(0);"  data-id="<?php echo $voucher_type_id;?>" class="open_voucher_txn_history">
                    <span class="material-symbols-outlined">visibility</span>
                </a>
                <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
                    <span class="material-symbols-outlined">offline_bolt</span>
                </a>
                <a href="#">
                    <span class="material-symbols-outlined">print</span>
                </a>
                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="material-symbols-outlined">
                        <span class="material-symbols-outlined">download</span>
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
                <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
            </div>
        </div>
         <div class="col-md-6 order-2 order-md-3">
              <select form="salefrm" name="currency_id" class="form-select d-inline-block" style="width:160px;">
           <?php foreach ($currency_list as $value) { ?>
                <option <?= $value['comp_currency_id'] == $currency_id ? 'selected' : '' ?> value="<?= $value['comp_currency_id'] ?>"><?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
           <?php } ?>
       </select>
           <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" name="oCheck" id="oCheck" form="salefrm">
        <label class="form-check-label" for="oCheck">Optional</label>
    </div>
	 <div class="form-check d-inline-block me-2">
		  <input class="form-check-input" type="checkbox" form="salefrm" value="1" id="itmbtchCheck">
		  <label class="form-check-label" for="itmbtchCheck">Item Batch</label>
		</div>
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" value="1" id="nsCheck">
      <label class="form-check-label" for="nsCheck">Negative Stock</label>
    </div>
      </div>
     <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
    <button class="btn btn-success m-1" type="button">View</button>
    <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a>
    </div>       
    </div>
    
    
    <div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="moreoptionslable">Apps</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
            </button>
        </div>
        <div class="offcanvas-body">
            <div class="row">
                
                <div class="col-sm-6 border-end">
                    <h5 class="pb-3">Horizontal</h5>
                    
                    <p class="offcanvaoptions">
                        <i>Condensed</i>
                        <label class="starcheck">
                            <input type="checkbox" checked="checked">
                            <b class="checkmark">★</b>
                        </label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="swap">
                        </label>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="swap">
                        </label>
                    </p>
                    
                    <p class="offcanvaoptions">
                        <i>Detailed</i>
                        <label class="starcheck">
                            <input type="checkbox" checked="checked">
                            <b class="checkmark">★</b>
                        </label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="swap">
                        </label>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="swap">
                        </label>
                    </p>
                    
                    <p class="offcanvaoptions">
                        <i>All Labels</i>
                        <label class="starcheck">
                            <input type="checkbox" checked="checked">
                            <b class="checkmark">★</b>
                        </label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="swap">
                        </label>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="swap">
                        </label>
                    </p>
                </div>
                
                <div class="col-sm-6">
                    <h5 class="pb-3">Verticle</h5>
                    
                    <p class="offcanvaoptions">
                        <i>Verticle</i>
                        <label class="starcheck">
                            <input type="checkbox" checked="checked">
                            <b class="checkmark">★</b>
                        </label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="swap">
                        </label>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="swap">
                        </label>
                    </p>
                    
                    <p class="offcanvaoptions">
                        <i>Schudle</i>
                        <label class="starcheck">
                            <input type="checkbox" checked="checked">
                            <b class="checkmark">★</b>
                        </label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="swap">
                        </label>
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="swap">
                        </label>
                    </p>
                </div>
                
                <div class="col-sm-12 pt-3 border-top">
                    <p class="offcanvaoptions">
                        <i>Schedule</i>
                        <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap">
                    </label>
                    <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap">
                </label>
            </p>
            <p class="offcanvaoptions">
                <i>Ratio</i>
                <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap">
            </label>
            <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap">
        </label>
    </p>
    
    <p class="text-center pt-3">
        <a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
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


<div id="validation_errors"></div>
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <input type="hidden" name="vch_subtype_id" value="<?= $vch_subtype_id ?>">
<div class="col-12">

<div class="row m-0 p-0">

<div class="col-md-2 col-6 card p-2">
    <div class="input-group">
        <label class="input-group-text">Date:</label> <input type="text" id="voucher_date" name="voucher_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm" readonly>
    </div> 
</div>
<div class="col-md-2 col-6 card p-2">
    <div class="input-group">
        <label class="input-group-text">Series:</label>
		 <select name="voucher_series" id="voucher_series" class="voucher_series form-select required" required>
			<?php			
			foreach($voucher_series as $vch_series_info){ ?>
               <option id="<?php echo $vch_series_info['vch_series_id']; ?>"  data-srsmethod="<?php echo $vch_series_info['vch_series_method']; ?>"  value="<?php echo $vch_series_info['vch_series_id']; ?>" <?php echo ($sel_vch_series==$vch_series_info['vch_series_id']) ? 'selected':'';?>><?= $vch_series_info['vch_series_name'] ?></option>
            <?php } ?>
		  </select>
        
    </div>
</div>
<div class="col-md-2 col-6 card p-2">
    <div class="input-group">
        <label class="input-group-text">Voucher No:</label> <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled>
    </div>
</div>

<div class="col-md-3 col-6 card p-2">
    <div class="input-group">
        <label class="input-group-text">Material Centre:</label>
        <?php
        echo form_dropdown('matrcntr_id', $matrcntr_dropdown, $matrcntr_id,' id="matrcntr_id" class="form-select required" ');
        ?>
    </div>
</div>

<div class="col-md-12 col-12 card p-2">
    <div class="input-group">
        <label class="input-group-text">Narration:</label>
        
        <textarea  name="narration" class="form-control"><?= $narration ?></textarea>
        
    </div>
    <input type="hidden" name="item_data_from" id="item_data_from">
    <input type="hidden" name="item_data_to" id="item_data_to">
	 <input type="hidden" name="btnid" id="btnid">
	  <input type="hidden" name="frombatchdata" id="frombatchdata">
 <input type="hidden" name="tobatchdata" id="tobatchdata">
</div>
</div>
</div>



<br>
<div class="col-12">
<div class="row">
<!-- Tax Summary -->
<div class="col-md-6">
    <h4 class="text-center">
        <?= ($vch_subtype_id == 18) ? 'Journal Transferred From' : '' ?>
        <?= ($vch_subtype_id == 19) ? 'Unpacking From' : '' ?>
        <?= ($vch_subtype_id == 20) ? 'Packing From' : '' ?>
    </h4>
    <div id="grid_item1" style="margin:auto;">
    </div>
    
</div>
<!-- Tax Summary Ends -->

<!-- Bill Summary Starts -->
<div class="col-md-6">
    <h4 class="text-center">
        <?= ($vch_subtype_id == 18) ? 'Journal Transferred To' : '' ?>
        <?= ($vch_subtype_id == 19) ? 'Packing To' : '' ?>
        <?= ($vch_subtype_id == 20) ? 'Unpacking To' : '' ?>
    </h4>
    <div id="grid_item2" style="margin:auto;">
    </div>
</div>
</div>

<div class="col-12 text-center">
<br>
<br>
<!--<input type="file" class="">-->

<input type="button" id="submitbtn" class="btn btn-success btn-lg" value="SAVE" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">
<input type="button" id="submitbtn_drft" name="submitbtn_drft" value="SAVE AS DRAFT"   class="btn btn-lg btn-success mx-2">
<a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
<a href="javascript:main(0)"  class="deletebtn btn btn-lg btn-danger mx-2">Delete</a>
</div>

</div>


<!-- Item Batch Modal -->
<div class="modal" id="batchmodel" style="z-index: 9999">
<div class="modal-dialog  modal-xl">
  <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header d-flex justify-content-between align-items-center">
  <!-- Left: Title -->
  <div class="flex-grow-1 text-start">
    <h4 class="modal-title">
      <span id="batch_page">
      </span> Item Batch Details</h4>
  </div>

  <!-- Center: Warning -->
  <div class="flex-grow-1 text-center">
    <div class="text-warning fw-bold" id="batch_warning">
      <!-- Optional: Warning text goes here -->
    </div>
  </div>

  <!-- Right: Close Button -->
  <div class="flex-grow-1 text-end">
    <button type="button" class="btn-close close_batch_data" data-bs-dismiss="modal">
  </div>
</div>
    <!-- Modal body -->
    <div class="modal-body">
      <div class="row">
        <div class="col-md-3 text-center">
          <h4>Item: <span id="batch_item_name">
          </span>
          </h4>
        </div>
        <div class="col-md-3 text-center">
          <h4>Qty: <span id="batch_item_qty">
          </span>
          </h4>
        </div>
        <div class="col-md-3 text-center">
          <h4>Unit: <span id="batch_item_unit">
          </span>
          </h4>
        </div>
        <div class="col-md-3 text-center">
          <h4>Undefined: <span id="batch_item_balance">
          </span>
          </h4>
        </div>
        
      </div>
      <div id="item_batch_grid"></div>
      
      <button  type="button" class="btn btn-success prevBtn" onclick="readyBatchModel('prev')">Previous</button>
      <button type="button" class="btn btn-success nextBtn" onclick="readyBatchModel('next')">Next</button>
      
    </div>
    <!-- Modal footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-success" id="save_batch">Save</button>
      <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
    </div>
  </div>
</div>
</div>
</form>
<?php echo view('includes/footer_scripts');
    $json_data_item1 = $from_transactions;
    $json_data_item2 = $to_transactions;

 for($i=1;$i<=50;$i++){ 
  $json_data_item1[] = array("item_id"=>'',"item_name"=>'','item_qty'=>'','description'=>'','item_unit_name'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');

  $json_data_item2[] = array("item_id"=>'',"item_name"=>'','item_qty'=>'','description'=>'','item_unit_name'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');
 }
?>
<style>
.boldcell{font-weight:700;}
</style>


<script>
var taxes_list        =<?php echo json_encode($taxes_list);?>;
var unitslist         = <?php echo json_encode($units_list);?>; 
var saved_batch_txns  = <?= json_encode($item_batch_data) ?>;
var methods           = <?php echo json_encode($method_list); ?>;
var isedit            = 1;
var batch_items       = [];

$("#voucher_date").on("change", function () {
    let voucher_date  = $(this).val();
    var data = $("#grid_item1").pqGrid('option', 'dataModel.data');
    $.each(data, function(index, obj) {
          if (obj.item_id != '' && obj.item_id != undefined) {
              /*
             console.log(taxes_list);
             console.log("----");
             console.log(obj.tax_cat_id);
             console.log("----");
             console.log(voucher_date); 
              */
			var taxDetails = getTaxDetails(taxes_list,obj.tax_cat_id, voucher_date);
		     // rd.tax_details  = taxDetails;
              data[index]['tax_details'] = taxDetails;
          }
      });
      $("#grid_item1").pqGrid('option', 'dataModel.data', data);
      $("#grid_item1").pqGrid('refreshDataAndView');
	  
	  
	  var datatwo = $("#grid_item2").pqGrid('option', 'dataModel.data');
     $.each(datatwo, function(index2, obj2) {
          if (obj2.item_id != '' && obj2.item_id != undefined) {
              /*
             console.log(taxes_list);
             console.log("----");
             console.log(obj.tax_cat_id);
             console.log("----");
             console.log(voucher_date); 
              */
			var taxDetails = getTaxDetails(taxes_list,obj2.tax_cat_id, voucher_date);
		     // rd.tax_details  = taxDetails;
              datatwo[index2]['tax_details'] = taxDetails;
          }
      });
      $("#grid_item2").pqGrid('option', 'dataModel.data', datatwo);
      $("#grid_item2").pqGrid('refreshDataAndView');	  
   });
$(document).on("click",".deletebtn",function(){
	 confirm_delete_post(baseurl+"admin/stock_journal/delete",'<?php echo clean(obfuscate_link($voucher_txn_id));?>');         
	  return false
    })

    /* var item_qty_balance_json = {};

    $.each(<?= json_encode($from_transactions) ?>, function(index,object){
        if(item_qty_balance_json[object.item_id]){
            item_qty_balance_json[object.item_id][object.item_unit_id] = 0;
        }
        else{
            item_qty_balance_json[object.item_id] = {};
            item_qty_balance_json[object.item_id] = {[object.item_unit_id] : 0};
        }
        
    });

    get_all_item_last_qty_balances(); */

    function get_all_item_last_qty_balances()
    {

        var date = $("#voucher_date").val();
        var mc_id = $("#matrcntr_id").val();
        console.log('dfdfd');
      
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
                    // console.log(response);
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

   /*  $('#voucher_date').change(function(e){
        get_all_item_last_qty_balances();
    });

    $('#matrcntr_id').change(function(e){
        get_all_item_last_qty_balances();
    }); */



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
        {
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

                    if(response.status){

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

                        $("#grid_item1").pqGrid('refreshDataAndView');
                        
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

    function update_grid_item_balances() {
        var data = $('#grid_item1').pqGrid('option', 'dataModel.data');

        $.each(data, function(index,obj){
            if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
            {
                if(item_qty_balance_json[obj.item_id]){
                    if(item_qty_balance_json[obj.item_id][obj.item_unit_id]){

                        data[index]['pq_cellattr']  = {
                            "item_name" : { "title":get_tooltip_html(item_qty_balance_json[obj.item_id][obj.item_unit_id]) },
                            "item_unit" : { "title":get_tooltip_html(item_qty_balance_json[obj.item_id][obj.item_unit_id]) },
                        };
                    }
                }
               
            }
        });


        $('#grid_item1').pqGrid('option', 'dataModel.data', data);
        $('#grid_item1').pqGrid('refreshDataAndView');
    }
   
    
		
    $("#submitbtn,#submitbtn_drft").on("click",function(){
        var grid_item1 = grid_response_item($('#grid_item1'),2);
        var grid_item2 = grid_response_item($('#grid_item2'),1);
		var btn_id     =  $(this).attr("id");
        var stock_status_errors = 0;
		batch_items  = [];
        if($('#oCheck').is(":checked") || $('#nsCheck').is(":checked")){}
        else
        {
            /* var final_item_qty_json = {};
            $.each(grid_item1, function(index, object){
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
            }); */
        }

	   if($('#itmbtchCheck').is(":checked")){
			var grid_items = [].concat(grid_item1, grid_item2);
           set_batch_items(grid_items);
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
        else if(grid_item1.length == '0' && grid_item2.length == '0' ){
            alert_notification("Kindly fill the items data!!!");
            return false;   
        }
        /* else if(stock_status_errors > 0){
            alert_notification("Qty should be less than or equal to stock available!!!");
            return false;  
        } */
        else{
            $("#item_data_from").val(JSON.stringify(grid_item1));
            $("#item_data_to").val(JSON.stringify(grid_item2));
			$("#btnid").val(btn_id);	
            $("#btnid").val(btn_id);
			if($('#itmbtchCheck').is(":checked") && batch_items.length>0){
			   readyBatches();
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
				
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
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
	                            <div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong>  <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							</div>
	                        `;
                        $('#validation_errors').html(html);
						 $('#validation_errors').show();
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
/************  Start Of Journal Transferred From    *******************/
     function grid_response_item(obj,drcr=''){
        var item_checked = [];
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');
            data.forEach(function(row){
                if(row.item_id != '' && row.item_name != ''){        
                    var item_id        = row.item_id;
                    var item_name      = row.item_name;
                    var item_price     = parseAmountPrice(row.item_price,4);
                    var item_qty       = row.item_qty;
                    var item_unit      = row.item_unit_name;
                    var item_unit_id   = row.item_unit_id;
                    var item_amount    = parseAmount(row.item_amount);					
					var tax_cat_id     = row.tax_cat_id;
					var item_sales_acc = row.item_sales_acc;
					var item_pur_acc   = row.item_pur_acc;
					var tax_details    = row.tax_details;
                    item_checked.push({
                        "item_id"           : item_id,
                        "item_price"        : item_price,
                        "item_qty"          : item_qty,
                        "item_unit_name"    : item_unit,
                        "item_unit_id"      : item_unit_id,
                        "item_total_amount" : item_amount,
						"tax_cat_id"        : tax_cat_id,
						"item_sales_acc"    : item_sales_acc,
						"item_pur_acc"      : item_pur_acc,
						"tax_details"       : tax_details,
						"drcr_type"         : drcr
                    });
                    
                }
            });
        }
        return item_checked;
    }

    function calculateSummary_item() {
        var item_price_total = 0,
            item_qty_total = 0,
            item_amount_total=0,
            data = this.option('dataModel.data'),
            len  = data.length;
            // console.log(data);
        data.forEach(function(row){
            if(row.item_id != '' && row.item_name != '' && row.item_price != '' && row.item_qty != '' && row.item_amount != '')
            {
                item_price_total  += parseAmount(row.item_price);
                item_qty_total    += parseInt(row.item_qty);
                item_amount_total += parseAmount(row.item_amount);
                // console.log(row);
            }
        })

        if(item_qty_total == 0){
            item_qty_total = '';
        }

        var totalData = {
                item_name: "Total",
                item_qty  : item_qty_total,
                item_price: item_price_total,
                item_amount: item_amount_total,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
        this.option('summaryData', [totalData]);
    }

    var autoCompleteEditor_unit = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var grid = this;
        var grid_id = this.bindings[0].id;

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
                rd.item_unit_name = ui.item.label;
                rd.item_unit_id =ui.item.id;

                if(grid_id == 'grid_item1'){
                    //get_item_last_balance(rd);
                }
            }
        }).focus(function () {
            $(this).autocomplete("search", "");
            rd.item_unit_name = '';
            rd.item_unit_id = '';
            rd.pq_cellattr = {};
        }).focusout(function () {              
            if(rd.item_unit_id == '' && rd.item_unit_name != '')
            {
                var index = unitslist.findIndex(function(obj) {

                    var string = obj.label.toLowerCase();
                    var text = rd.item_unit_name.toLowerCase();
                     
                    return string.includes(text);
                });
                if(index > -1){
                    rd.item_unit_name = unitslist[index].label;
                    rd.item_unit_id = unitslist[index].id;

                    if(grid_id == 'grid_item1'){

                       // get_item_last_balance(rd); 
                    }

                }
                else{
                    rd.item_unit_name = '';
                    rd.item_unit_id = '';
                    rd.pq_cellattr = {};
                }
            }
        });
    }

    var autoCompleteEditor_item = function (ui) {
        var $inp = ui.$cell.find("input");
        var rd = ui.rowData;
        var element= {};
        var grid = this;
        var grid_id = this.bindings[0].id;

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
            source:  <?php echo $item_json_file; ?>,
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 0,
            select: function(event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);

                rd.item_id        = ui.item.item_id;
                rd.item_name      = ui.item.label;
                rd.item_unit_name = ui.item.item_unit_name;
                rd.item_unit_id   = ui.item.item_unit_id;				
				rd.item_pur_acc   = ui.item.item_pur_acc;
				rd.item_sales_acc = ui.item.item_sales_acc;				
				rd.tax_cat_id     = ui.item.tax_cat_id;
				
				var taxDetails = getTaxDetails(taxes_list,rd.tax_cat_id, voucher_date);
				 rd.tax_details  = taxDetails;
				rd.tax_cat_id     = ui.item.tax_cat_id;
				
                if(grid_id == 'grid_item1'){

                    //get_item_last_balance(rd); 
                }
             }  
        }).focusout(function () { 
			var enteredValue = $(this).val().toLowerCase();
				   var  source_data=<?php echo $item_json_file; ?>;				  
				   const isValid = source_data.some(item => item.label.toLowerCase() === enteredValue);
           
			if(rd.item_pur_acc == ''){
				alert_notification('Purchase account is missing for this item!');
				$(this).val('');
				$(this).autocomplete("search", "");
                rd.item_id = '';
                rd.item_name = '';
                rd.item_unit_name = '';
                rd.item_unit_id = '';
                rd.pq_cellattr = {};
            }	
			if(rd.item_sales_acc == ''){
				alert_notification('Sales account is missing for this item!');
				$(this).val('');
				$(this).autocomplete("search", "");
                rd.item_id = '';
                rd.item_name = '';
                rd.item_unit_name = '';
                rd.item_unit_id = '';
                rd.pq_cellattr = {};
            }

		   if(rd.item_id == '' || isValid==false)
            {   $(this).val('');
				$(this).autocomplete("search", "");
                rd.item_id = '';
                rd.item_name = '';
                rd.item_unit = '';
                rd.item_unit_id = '';
                rd.pq_cellattr = {};
            }
        });
       
    }
    var colModel_item1 = [
        { title: "ITEM NAME",sortable:false, dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                type: "textbox",
                init: autoCompleteEditor_item,
                options: []
            },
        },
        { title: "QTY", sortable:false,width: 20, dataType: "integer", dataIndx: "item_qty",
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
                        if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmountPrice(rd.item_price,4) >= 0){
                            var amount = rd.item_qty * parseAmountPrice(rd.item_price,4);
                            rd.item_amount = amount;  
                            grid.refreshDataAndView();     
                        } 
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

        { title: "UOM", sortable:false,dataIndx: "item_unit_name", width: 20,cls: 'pq-drop-icon pq-side-icon',
            editable: function (ui) {
               var item_id = ui.rowData['item_id'];
                if (item_id != '') {
                    return true;
                }
                return false;
            },
            editor: {                   
                type: "textbox",
                init:autoCompleteEditor_unit,
                options: [],
            },
        },

        { title: "PRICE",sortable:false, width: 20, align: "right",dataIndx: "item_price",dataType: "float",
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
                        if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmountPrice(rd.item_price,4) >= 0){
                                var amount = rd.item_qty * parseAmountPrice(rd.item_price,4);
                                rd.item_amount = amount;  
                                grid.refreshDataAndView(); 
                        }
                        if(rd.item_qty == '' && rd.item_price != '' && parseAmountPrice(rd.item_price,4) >= 0){
                            rd.item_qty = 1;
                            rd.item_amount = parseAmountPrice(rd.item_price,4);  
                            grid.refreshDataAndView();     
                        }
                    })
                }
            },
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.item_price != ''){
                    rd.item_price = parseAmountPrice(rd.item_price,4);
                    return formatAmount(rd.item_price,'',4);   
                }
                return '';
            }
        },
        { title: "AMOUNT", sortable:false,width: 20, align: "right",dataIndx: "item_amount",dataType: "float",editable: false,
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
                        if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                            var price = parseAmount(rd.item_amount) / rd.item_qty;
							
                            rd.item_price = parseAmountPrice(price,4); 
                                    
                        }
                        else if(rd.item_qty == '' && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                            rd.item_qty = 1;;
                            rd.item_price = parseAmountPrice(rd.item_amount,4); 
                                    
                        }
						grid.refreshDataAndView();
						
                    });
                }
            },
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.item_amount != ''){
                    rd.item_amount = parseAmount(rd.item_amount);
                    return formatAmount(rd.item_amount);   
                }
                return '';
            }
        },
    ];
            
    var dataModel_item1 = {"data":<?php echo json_encode($json_data_item1);?>};
    var newObj_item1 = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        height: 450,
        selectionModel: { type: 'row' },
        scrollModel: { autoFit: true },
        dataModel: dataModel_item1,
        colModel: colModel_item1,  
        numberCell: { show: true },
        pageModel: { type: 'local' },
        editable: true,
        showSummary:true,
        columnTemplate: { render: commentRender },
        wrap:false,
        change:calculateSummary_item,
        dataReady:calculateSummary_item,
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

    newObj_item1.cellKeyDown = function(evt, ui) {
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

    var $grid_item1 = $("#grid_item1").pqGrid(newObj_item1);
/************  End Of Journal Transferred From    *******************/

/************  Start Of Journal Transferred To   *******************/
    var colModel_item2 = [
        { title: "ITEM NAME", sortable:false,dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                type: "textbox",
                init: autoCompleteEditor_item,
                options: []
            },
        },
        { title: "QTY", sortable:false,width: 20, dataType: "float", dataIndx: "item_qty",
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
                        if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmount(rd.item_price) >= 0){
                            var amount = rd.item_qty * parseAmount(rd.item_price);
                            rd.item_amount = amount;  
                            grid.refreshDataAndView();     
                        } 
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

        { title: "UOM",sortable:false, dataIndx: "item_unit_name", width: 20,cls: 'pq-drop-icon pq-side-icon',
            editable: function (ui) {
               var item_id = ui.rowData['item_id'];
                if (item_id != '') {
                    return true;
                }
                return false;
            },
            editor: {                   
                type: "textbox",
                init:autoCompleteEditor_unit,
                options: [],
            },
        },

        { title: "PRICE",sortable:false, width: 20, align: "right",dataIndx: "item_price",dataType: "float",
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
                        if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmountPrice(rd.item_price,4) >= 0){
                            var amount = rd.item_qty * parseAmountPrice(rd.item_price,4);
                            rd.item_amount = parseAmount(amount);  
                            grid.refreshDataAndView(); 

                        }
                        if(rd.item_qty == '' && rd.item_price != '' && parseAmountPrice(rd.item_price,4) >= 0){
                            rd.item_qty = 1;
                            rd.item_amount = parseAmount(rd.item_price);  
                            grid.refreshDataAndView();     
                        }
                    })
                }
            },
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.item_price != ''){
                    rd.item_price = parseAmountPrice(rd.item_price,4);
                    return formatAmount(rd.item_price,'',4);   
                }
                return '';
            }
        },
        { title: "AMOUNT",sortable:false, width: 20, align: "right",dataIndx: "item_amount",dataType: "float",editable: false,
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
                    if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                            var price = parseAmount(rd.item_amount) / rd.item_qty;
                            rd.item_price = parseAmountPrice(price,4); 
                            grid.refreshDataAndView();        
                        }
                        if(rd.item_qty == '' && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                            rd.item_qty = 1;;
                            rd.item_price = parseAmountPrice(rd.item_amount,4); 
                            grid.refreshDataAndView();        
                        }
                    });
                }
            },
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.item_amount != ''){
                    rd.item_amount = parseAmount(rd.item_amount);
                    return formatAmount(rd.item_amount);   
                }
                return '';
            }
        },
    ];
            
    var dataModel_item2 = {"data":<?php echo json_encode($json_data_item2);?>};
    var newObj_item2 = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        height: 450,
        selectionModel: { type: 'row' },
        scrollModel: { autoFit: true },
        dataModel: dataModel_item2,
        colModel: colModel_item2,  
        numberCell: { show: true },
        pageModel: { type: 'local' },
        editable: true,
        wrap:false,
        showSummary:true,
        change:calculateSummary_item,
        dataReady:calculateSummary_item,
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

    newObj_item2.cellKeyDown = function(evt, ui) {
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

    var $grid_item2 = $("#grid_item2").pqGrid(newObj_item2);

/**************  Start of Batch Item ********************/
if(saved_batch_txns.length > 0){
    $('#itmbtchCheck').prop('checked', true);
}
    var batch_items = [];
    var batch_data  = [];
    var batch_Index = 0;  
    function generateBatchGridData(item_id = 0,item_unit_id = 0){
       var batchjson = [];	 
	   if(item_id != 0 && item_unit_id != 0){
		
        var i = saved_batch_txns.findIndex(function(o) {
           return o.item_id == item_id && o.item_unit_id == item_unit_id;
        });
		
		
        if(i >= 0){
            $.each(saved_batch_txns[i].grid, function(index, obj){
               batchjson.push({'batch_method': obj.batch_method ,'batch_no': obj.batch_no, 'batch_id':obj.batch_id,  'manufacturing_date': obj.manufacturing_date, 'batch_qty': obj.batch_qty, 'expiry_date': obj.expiry_date, 'startDate':'<?php echo $fy_begndt;?>','endDate':'<?php echo $fy_end;?>'});
            });
        }
	 }	 
        for(var i=0;i<25;i++){
            batchjson.push({'batch_method': '','batch_no': '','batch_id':'', 'manufacturing_date': '','batch_qty':'','batch_uom':'','batch_uom_id':'', 'expiry_date': '','startDate':'<?php echo $fy_begndt;?>','endDate':'<?php echo $fy_end;?>'});
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


  function batchEditor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData,
        grid = this;
        
        if(rd.batch_method == 'Adjustment')
        {
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

                        $inp.val(batch_items[batch_Index].batch_ref_list[index].value); 
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
        if(rd.batch_method == 'New Ref.')
        {
            $inp.on("focusout", function () {
                var reference = $(this).val();
                if(reference)
                {
                    var index = batch_items[batch_Index].batch_ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == reference.toLowerCase();
                    });
                    if(index > -1){
                        alert_notification('Refernce alredy exists. To use this reference change method to adjustment');
                        rd.batch_id = '';
						rd.batch_no = '';
                        
                    }
                }

            });
        }

        $inp.on("change", function (evt) {
            grid.refreshDataAndView();
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
   { title: "METHOD", dataIndx: "batch_method", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: 'select',
            options: methods,
            init: batch_methodEditor
        }
    },
	{ title: "BATCH NO", width: 100, dataIndx: "batch_no" ,cls: 'pq-drop-icon pq-side-icon',
        editor: {                   
              type: "textbox",
              init: batchEditor
        },
        editable: function (ui) {
           var method = ui.rowData['batch_method'];
            if (method != '') {
                return true;
            }
            return false;
        },
    },
    
  $.extend( true, {title: "MANUFACTURING DATE", width: 100, dataIndx: "manufacturing_date" ,cls: 'pq-drop-icon pq-side-icon',editable: function (ui) {
           var batch_method = ui.rowData['batch_method'];
            if (isedit == 0 && batch_method != 'Adjustment') {
            return true; // always editable in Add mode
			} else if (isedit == 1 && batch_method!='' && batch_method != 'Adjustment') {
				return true; // editable in Edit mode if not Adjustment
			}		
			return false;
        }}, date_column),      
    $.extend( true, {title: "EXPIRY DATE", width: 100, dataIndx: "expiry_date" ,cls: 'pq-drop-icon pq-side-icon',editable: function (ui) {
           var batch_method = ui.rowData['batch_method'];
            if (isedit == 0 && batch_method != 'Adjustment') {
            return true; // always editable in Add mode
			} else if (isedit == 1 && batch_method!='' && batch_method != 'Adjustment') {
				return true; // editable in Edit mode if not Adjustment
			}		
			return false;
        }}, date_column),
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
			var cr_data = batch_data.filter(function (row) {
				return row.drcr_type === 2;
			});

			var dr_data = batch_data.filter(function (row) {
				return row.drcr_type === 1;
			});
			if (cr_data.length > 0) {
				$("#frombatchdata").val(JSON.stringify(cr_data));
			}

			if (dr_data.length > 0) {
				$("#tobatchdata").val(JSON.stringify(dr_data));
			}
			
			show_loader();
            $("#salefrm").submit();
          }
    });

    function validateAllBatchData() {
    var isAnyBatchValid = false;
	var errorMessage = "";
    for (var i = 0; i < batch_items.length; i++) {
        var obj = batch_items[i];
        var master_qty = parseFloat(obj.item_qty);
        var qtycount = 0;
        var hasValidRow = false;
		var batchNos = []; // array to store keys
        for (var j = 0; j < obj.grid.length; j++) {
            var obj2 = obj.grid[j];
            var batch_no = obj2.batch_no;
            var batch_qty = obj2.batch_qty;
            var batch_uom_id = obj2.batch_uom_id;

            if (batch_no !== '' && batch_qty !== '') {
				 var key = batch_no.toString().trim().toLowerCase();
				/* console.log(key);
				console.log(batchNos);
				if (batchNos.length > 0 && batchNos.includes(key)) {
					console.log("Duplicate found for batch:", key);
					errorMessage = 'Duplicate batch found for Item ' + obj.item_name + ' & Unit ' + obj.item_unit_name;
				} else {
					batchNos.push(key); // store the batch key
					console.log("fresh add-->")
					console.log(batchNos);
				} */
                qtycount += parseFloat(batch_qty);
                hasValidRow = true;
            }
        }
		
        if (hasValidRow) {
            if (parseFloat(qtycount.toFixed(2)) !== parseFloat(master_qty.toFixed(2))) {
                $('#batch_warning').text('Batch qty mismatch for a filled grid.');
                return false;
            }
            isAnyBatchValid = true;
            break; // one valid batch found and verified
        }
    }
	if (errorMessage != "") {
        $('#batch_warning').text(errorMessage);
        return false;
    }
    if (!isAnyBatchValid) {
        $('#batch_warning').text('Please fill at least one batch grid.');
        return false;
    }
		
	return true;
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
                var batch_qty        = data[i]['batch_qty'];
                var batch_uom_id     = data[i]['batch_uom_id'];
              
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
      
      batch_data=[];
        var batch_item_qty = {};
      
        $.each(batch_items, function(index,obj)
        {   
            if(obj.item_id!='' && obj.item_name != '')
            {
                var item_id         = obj.item_id;
                var item_unit       = obj.item_unit;
                var item_unit_id    = obj.item_unit_id;
                var master_item_qty = obj.item_qty;
				var drcr_type       = obj.drcr_type;

                
                var batch_item_qty = 0;
                
            
         $.each(obj.grid, function(index2, obj2)      {
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
						    "manufacturing_date" : manufacturing_date,
						    "drcr_type"          : drcr_type		
                        });
            }
                     
                });   
                
                
          /* var batch_difference = (parseFloat(master_item_qty)-parseFloat(batch_item_qty));

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
                }); */
            }
        });
    }

    function set_batch_items(grid_items)
    {
        batch_items = [];
		/* console.log("=============grid_items==========");
		console.log(grid_items);
return false; */
        if(grid_items.length > 0)
        {
            $.each(grid_items, function(index, object)
            {
                var i = batch_items.findIndex(function(obj) {
                    return obj.item_id == object.item_id && obj.item_unit_id == object.item_unit_id;
                });

                if(i > -1){
                    batch_items[i].item_qty += parseFloat(object.item_qty);
                }
                else{
                    batch_items.push({
                       "item_id"       : object.item_id,
                       "item_name"     : object.item_name,
                       "item_qty"      : parseFloat(object.item_qty),
                       "item_balance"  : parseFloat(object.item_qty),
                       "item_unit"     : object.item_unit_name,
                       "item_unit_id"  : object.item_unit_id,
					   "drcr_type"     : object.drcr_type,
                       "grid": generateBatchGridData(object.item_id,object.item_unit_id)  
                   });
                }
            });
        }
    }

  function readyBatches(){
   var items_id_array = batch_items.map(function(obj) {
    return {
        item_id: obj.item_id,
        item_unit_id: obj.item_unit_id
    };
});

   $.ajax({
        type: "POST",
        url: baseurl+"admin/vouchers/getItemBatch",
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
            var item_name    = batch_items[batch_Index].item_name;
            var item_unit    = batch_items[batch_Index].item_unit;
			var item_qty     = batch_items[batch_Index].item_qty;
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
	function batch_methodEditor(ui) {
		var $inp = ui.$cell.find("select"),
			di = ui.dataIndx,
			rd = ui.rowData,               
			grid = this;
		
		$inp.on("change", function (evt) {
			var method = $(this).val();
			rd.batch_no = '';        
		})
	}
/********************End Of Batch Items   *****************************************/
 </script>

</body>
</html>