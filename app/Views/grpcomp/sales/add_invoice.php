<?php $header = array(  'title' => 'Add Sale Invoice' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
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
</style>
<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>
<div class="row pb-2">
  <div class="col-md-6 order-1"><h3>Sale Invoice</h3></div>  
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
    <select name="type" id="type" class="form-select d-inline-block" style="width:160px;">
        <option value="with_item" selected="selected">With Item</option>
        <option value="without_item">Without Item</option>
    </select>
          <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="itmtrackingCheck">
    <label class="form-check-label" for="itmtrackingCheck">Track Item</label>
   </div>
   
  <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="itmbtchCheck">
    <label class="form-check-label" for="itmbtchCheck">Item Batch</label>
   </div>
   <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
    <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
   </div>

   <select form="salefrm" name="currency_id" class="form-select d-inline-block" style="width:160px;">
       <?php foreach ($currency_list as $value) { ?>
            <option value="<?= $value['comp_currency_id'] ?>"><?= $value['curr_name'] ?> (<?= $value['curr_symbol'] ?>)</option>
       <?php } ?>
   </select>
  </div>
  <div class="col-md-6 text-md-end order-4  collapse listmenu" id="listmenu">
    <a href="#" class="btn btn-success btn-sm dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
  </ul>  
      
  <button class="btn btn-success btn-sm m-1" type="button">Templates</button>
   <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm showinline-md">Back</a>
  </div> 
</div>
    <div id="validation_errors"></div>

<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
 <div class="col-12">
  <div class="row m-0 p-0 align-items-center">
      
    <div class="row">
        <div class="col-md-3 col-6 card p-2">
            <div class="input-group">
             <span class="input-group-text">Sales Type:</span>
             <?php echo form_dropdown('sale_type', $sub_type_dropdown, '',' id="sale_type" class="sale_type form-select required" required '); ?>
            </div>
       </div>
       <div class="col-md-3 col-6 card p-2" id="against_div"  style="display:none;">
            <div class="input-group">
             <span class="input-group-text">Against Challan:</span>
             <?php 
             $sale_invoice_dropdown = array();
             echo form_dropdown('delivery_challan_id', $sale_against_challan, '',' id="delivery_challan_id" class="delivery_challan_id form-select" '); ?>
            </div>
       </div>
       
    </div>
    
    <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Voucher No:</label>
           <input type="text" name="voucher" id="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled>
       </div>
    </div>
   <div class="col-md-3 col-6 card p-2">
    <div class="input-group">
     <span class="input-group-text">Series:</span>
     <?php echo form_dropdown('voucher_series', $voucher_series_dropdown, '18',' id="voucher_series" class="voucher_series form-select required" required '); ?>
    </div>
   </div>
   
   <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Date:</label> 
           <input type="text"  id="sale_date" name="sale_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm">
       </div>
    </div>  
      
   <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Sales Type:</label>
           <input type="text" id="salestype" name="salestype" class="form-control form-control-sm" disabled>
       </div>
   </div>
   <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Transporter:</label>
           <input type="text" name="transporter" class="form-control form-control-sm" disabled>
       </div>
   </div>
   <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">E-way:</label>
            <input type="text" name="eway" class="form-control form-control-sm" disabled>
       </div>
   </div>
   
   <div class="col-md-3 col-6 card p-2">
       <div class="input-group">
           <label class="input-group-text">Party:</label> 
           <select name="party_id" class="form-select required" required>
               <option data-is_bbb="0" value=""></option>
               <?php foreach ($party_dropdown as $value) { ?>
                    <option data-is_bbb="<?= $value['is_bbb'] ?>" value="<?= $value['acc_id'] ?>"><?= $value['acc_name'] ?></option>
               <?php } ?>
           </select>
        </div>
    </div>
    
    <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
            <label class="input-group-text">MC:</label> 
            <?php echo form_dropdown('matrcntr_id', $matrcntr_dropdown, "1",' id="matrcntr_id" class="form-select required" '); ?>
        </div>
    </div>
    
      
      <div class="col-md-12 col-12 card p-2">
          <div class="input-group">
              <label class="input-group-text">Narration:</label> 
              <textarea  name="narration" class="form-control form-control-sm"></textarea>
          </div>
      </div>
      <input type="hidden" name="itmsdata" id="itmsdata">
      <input type="hidden" name="bbbdata" id="bbbdata">
      <input type="hidden" name="billsndrydata" id="billsndrydata">
      <input type="hidden" name="batchinfo_array" id="batchinfo_array" value="">
       <input type="hidden" name="trackinginfo_array" id="trackinginfo_array" value="">
      
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
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>"	>Save</button>
        <button type="reset" class="btn btn-success btn-lg">Reset</button>
        <a href="#" class="btn btn-secondary btn-lg">Quit</a>
        
    </div>
        
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
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_bill_by_bill">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>


<!-- List View The Modal Starts here -->
<div class="modal" id="listviewModal">
  <div class="modal-dialog  modal-md">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">List View</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
<ol class="list-group list-group-flush">
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">List Name 1 goes here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">List Name 2 goes here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">3rd List name here</div>
      Content for list item
    </div>
    <span class="badge bg-primary rounded-pill">14</span>
  </li>
</ol>
           <p class="col-md-12 text-end mt-2"> <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button></p> 
      </div>

    </div>
  </div>
</div>
<!-- List View The Modal Ends Here -->


<div class="modal" id="batchmodel" style="z-index: 9999">
                
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><span id="batch_page"></span> Item Batch Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">  
         <div class="row">
            <div class="col-md-3 text-center"><h4>Item: <span id="batch_item_name"></span></h4></div>
			 <div class="col-md-3 text-center"><h4>Qty: <span id="batch_item_qty"></span></h4></div>
			  <div class="col-md-3 text-center"><h4>Unit: <span id="batch_item_unit"></span></h4></div>
              <div class="col-md-3 text-center"><h4>Undefined: <span id="batch_item_balance"></span></h4></div>
            
        </div>	  
        <div id="item_batch_grid"></div>
		
		 <button  type="button" class="btn btn-primary prevBtn" onclick="readyBatchModel('prev')">Previous</button>
            <button type="button" class="btn btn-primary nextBtn" onclick="readyBatchModel('next')">Next</button>
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_batch">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>	

<div class="modal" id="trackingmodel" style="z-index: 9999">
                
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><span id="batch_page"></span> Item Tracking Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">  
         <div class="row">
            <div class="col-md-3 text-center"><h4>Item: <span id="tracking_item_name"></span></h4></div>
			 <div class="col-md-3 text-center"><h4>Qty: <span id="tracking_item_qty"></span></h4></div>
			  <div class="col-md-3 text-center"><h4>Unit: <span id="tracking_item_unit"></span></h4></div>
              <div class="col-md-3 text-center"><h4>Undefined: <span id="tracking_item_balance"></span></h4></div>
            
        </div>	  
        <div id="item_tracking_grid"></div>
		
		 <button  type="button" class="btn btn-primary prevBtn" onclick="readyTrackingModel('prev')">Previous</button>
            <button type="button" class="btn btn-primary nextBtn" onclick="readyTrackingModel('next')">Next</button>
        
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_tracking">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div>	

<!-- List View The Modal Ends Here -->

<?php echo view('includes/footer_scripts'); 
// for($i=1;$i<=50;$i++)
//     $json_data[] =array("item_id"=>'',"item_name"=>'','item_qty'=>'','description'=>'','item_unit'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');

for($i=1;$i<=50;$i++)
    $tax_json_data[] =array("id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'');

// for($i=1;$i<=10;$i++)
//     $billsundry_json_data[] = array("billsundry_id"=>'',"billsundry_name"=>'','billsundry_rate'=>'','billsundry_amount'=>'');
?>

<style>
    .boldcell{font-weight:700;}
</style>

<script>
var item_qty_balance = {};
function get_item_balances(item_id,item_unit_id,rd){
	 console.log(item_id +","+item_unit_id);
	 var voucher_date = $("#sale_date").val();	
	 if(item_qty_balance[item_id] == undefined){
         item_qty_balance[item_id] = {};					
	 }
	 
	 if(item_qty_balance[item_id][item_unit_id] == undefined){	     
	   var balance_response = function (){
	    $.ajax({
						  'async': false,
						 'type': "GET",
						'global': false,
					   'dataType': 'html',
					  'url': "<?php echo $base_url; ?>ajax/itemqtybalance/"+item_id+"/"+voucher_date+"/"+item_unit_id,
					  'success': function (data) {
						accbalance = data;
					   }
					   });
					 return accbalance;
					 }();					
                   item_qty_balance[item_id][item_unit_id] =balance_response;
     }
     else{
       var balance_response = item_qty_balance[item_id][item_unit_id];
     }
	if(balance_response){		
	var parseitembalance = $.parseJSON(balance_response);
	if(typeof parseitembalance !=="undefined"){
		AvailQty = parseitembalance[rd.item_id].AvailQty;
		var PackQty = parseitembalance[rd.item_id].PackQty;
		var ObseQty = parseitembalance[rd.item_id].ObseQty;
		var IntrsQty = parseitembalance[rd.item_id].IntrsQty;
		var UnitName = parseitembalance[rd.item_id].unit_name;
		rd.AvailQty  = AvailQty;
		var balancetable = "<table style='width:100%;'><tr><td colspan='2'>"+UnitName+"</td></tr><tr><td>Available</td><td align='right'>"+AvailQty+"</td></tr><tr><td>Packed</td><td align='right'>"+PackQty+"</td></tr><tr><td>Obselete</td><td align='right'>"+ObseQty+"</td></tr><tr><td>In Transit</td><td align='right'>"+IntrsQty+"</td></tr></table>";	
		rd.pq_cellattr ={
			"item_name" : { "title":balancetable }
		};
		$("#grid_search").pqGrid('refreshDataAndView');		
		return balancetable;
	}
	else
	  return "";
	}  
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
 var methods = <?php echo json_encode($bills_method_list); ?>;
  var item_qty_balance = {};
   var trackingIndex=0;
  var item_qty_balance_b = {};
  $(document).on('change', '#sale_date', function(e){

        var voucher_date = $("#sale_date").val();
      

        $.each(item_qty_balance, function(index, value){
              $.each(value, function(indexn, valuen){
             item_qty_balance_b[index]=indexn
              });
        });
        
    
        $.ajax({
            url: '<?php echo $base_url; ?>ajax/get_all_item_balances', 
            type: 'POST',
            data: {voucher_date: voucher_date, item_id_array: item_qty_balance_b},
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
                            if(item_qty_balance.hasOwnProperty(obj.item_id)) {
                                item_qty_balance[obj.item_id][obj.item_unit_id] = obj.balance;
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

   });
  function update_grid_item_balances() {
        var data = $("#grid_search").pqGrid('option', 'dataModel.data');

        $.each(data, function(index,obj){
            if(obj.item_id != '' && obj.item_id != undefined && obj.item_unit_id != '' && obj.item_unit_id != undefined)
            {
                data[index]['item_balance'] = item_qty_balance[obj.item_id][[obj.item_unit_id]];
            }
        });


        $("#grid_search").pqGrid('option', 'dataModel.data', data);
        $("#grid_search").pqGrid('refreshDataAndView');
   }
   

$("#sale_type").on("change",function(){
    var challantype = $(this).val();
    $("#grid_search").pqGrid('option', 'dataModel.data',get_item_grid());
    $("#grid_search").pqGrid('refreshDataAndView');

    $("#billsundry_search").pqGrid('option', 'dataModel.data',get_sundry_grid());
    $("#billsundry_search").pqGrid('refreshDataAndView');


    if(challantype=='10'){
        $("#against_div").show();
        $('#delivery_challan_id').val('');
    }
    else
        $("#against_div").hide();


})

$("#delivery_challan_id").on("change",function(){
    var vouchertxnid = $(this).val();

    $('select[name="party_id"]').val('');
    $('select[name="matrcntr_id"]').val('');

    $("#grid_search").pqGrid('option', 'dataModel.data',get_item_grid());
    $("#grid_search").pqGrid('refreshDataAndView');

    $("#billsundry_search").pqGrid('option', 'dataModel.data',get_sundry_grid());
    $("#billsundry_search").pqGrid('refreshDataAndView');

    if(vouchertxnid!=''){
        
        show_loader();
         $.ajax({
             type: "GET",
             url: "<?php echo base_url();?>/admin/sales/ajax_delivery_challan/"+vouchertxnid,
             datatype: "json",
             success: function(response){
                 stop_loader();
                 if(typeof response === 'string') {
                    response = JSON.parse(response);
                 }
                 if(response.status)
                 {
                    $('select[name="party_id"]').val(response.party_id);
                    $('select[name="matrcntr_id"]').val(response.mat_cent_id);

                    if(response.item_transactions.length)
                    {
                        var item_transactions = response.item_transactions.concat(get_item_grid());
                        
                        $("#grid_search").pqGrid('option', 'dataModel.data',item_transactions);
                        $("#grid_search").pqGrid('refreshDataAndView');
                    }
                    if(response.sundry_transactions.length)
                    {
                        var sundry_transactions = response.sundry_transactions.concat(get_sundry_grid());
                      
                        $("#billsundry_search").pqGrid('option', 'dataModel.data',sundry_transactions);
                        $("#billsundry_search").pqGrid('refreshDataAndView');
                    }
                 }
                  
            }
         });
      
    
    }
    
})


var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;


var itemlist  = [];
var unitslist = <?php echo json_encode($units_list);?>; 

var bbb_data = [];
var bbb_accounts = {}; //json, only one account ie party
var item_checked = [];

/*********************************************** BATCH ITEM WISE CODEING START BY BHUPINDER   ******************************************************/
var batch_items = [];
var batch_data = [];
var batch_Index=0;
  
function generateBatchGridData(){
	 var batchjson = [];
    for(var i=0;i<50;i++){
        batchjson.push({'batch_method': 'adjustment','batch_no': '','batch_id':'', 'manufacturing_date': '','batch_qty':'','batch_uom':'','batch_uom_id':'', 'expiry_date': '','startDate':'<?php echo $fy_begndt;?>','endDate':'<?php echo $fy_end;?>'});
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
				// console.log(expiry_date);
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


	
function batchno_editor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData
        // console.log(batch_items);
       
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

                        $inp.val([billIndex].batch_ref_list[index].value); 
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
   
    { title: "BATCH NO", dataIndx: "batch_no", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
			init: batchno_editor
        }
    },
	$.extend( true, {title: "MANUFACTURING DATE", width: 100, dataIndx: "manufacturing_date" ,cls: 'pq-drop-icon pq-side-icon',}, date_column),      
    $.extend( true, {title: "EXPIRY DATE", width: 100, dataIndx: "expiry_date" ,cls: 'pq-drop-icon pq-side-icon',}, date_column),
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
        //console.log(batch_data);		
		
		
		
        $("#batchinfo_array").val(JSON.stringify(batch_data));
        show_loader();
        if($('#bbbCheck').is(":checked") && is_bbb){
            readyPurchaseBills(total);
        }
        else if($('#ccCheck').is(":checked")){
            readyPurchaseCc();
        }
        else{
            $("#salefrm").submit();
        }
        
      }
});

function validateAllBatchData()
{
    var status = true;
    $.each(batch_items, function(index,obj)
    {
	   var master_qty  = obj.item_qty;
	
       var qtycount = 0;
        $.each(obj.grid, function(index2, obj2)
        {
            if(obj2.batch_no != '' && obj2.batch_qty != '')
            {
                 var batch_no      = obj2.batch_no;
				 var batch_qty     = obj2.batch_qty;
				 var batch_uom_id  = obj2.batch_uom_id;
			 	 qtycount += parseFloat(batch_qty);
            }
            
        });
        if(parseFloat(qtycount) > parseFloat(master_qty)){
           alert("Unit Qty mismatch!!!! ");
            status = false;
            return status;
        }
    });
	
    return status;
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
            var batch_qty           = data[i]['batch_qty'];
            var batch_uom_id      = data[i]['batch_uom_id'];
          
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
	//console.log(batch_items);
	batch_data=[];
    var batch_item_qty = {};
	
    $.each(batch_items, function(index,obj)
    {   
        if(obj.item_id!='' && obj.item_name != '')
        {
            var item_id = obj.item_id;
            var item_unit = obj.item_unit;
            var item_unit_id = obj.item_unit_id;

    		var master_item_qty = obj.item_qty;

            
            var batch_item_qty = 0;
            
    		
            $.each(obj.grid, function(index2, obj2)
            {
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
    					"manufacturing_date" : manufacturing_date					
                    });
				}
                 
            });   
            
            
			var batch_difference = (parseFloat(master_item_qty)-parseFloat(batch_item_qty));

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
            });
        }
    });
    
    
}

    function set_batch_items()
    {
        batch_items = [];

        if(item_checked.length > 0)
        {
            $.each(item_checked, function(index, object)
            {
                var i = batch_items.findIndex(function(obj) {
                    return obj.item_id == object.item_id && obj.item_unit_id == object.item_unit_id;
                });

                if(i > -1){
                    batch_items[i].item_qty += parseFloat(object.item_qty);
                }
                else{
                    batch_items.push({
                      "item_id": object.item_id,
                      "item_name" : object.item_name,
                       "item_qty" : parseFloat(object.item_qty),
                       "item_balance" : parseFloat(object.item_qty),
                       "item_unit" : object.item_unit,
                       "item_unit_id" : object.item_unit_id,
                       "grid": generateBatchGridData()  
                   });
                }
            });

            

            
        }
    }


	function readyBatches(){
	 var items_id_array = batch_items.map(function(obj) { return obj.item_id; });
	 $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>ajax/getItemBatch",
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
        var item_name = batch_items[batch_Index].item_name;
        var item_unit = batch_items[batch_Index].item_unit;
		var item_qty = batch_items[batch_Index].item_qty;
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



/************************************************  iTEM BATCH WISE CODEING END  *****************************************************/


/*********************************************** ITEM Tracking CODEING START BY BHUPINDER   ******************************************************/
var tracking_data = [];
var tracking_items = [];
var tracking_Index=0;  

function generateTrackingGridData(){
	 var trackjson = [];
    for(var i=0;i<50;i++){
        trackjson.push({'tracking_method': '','tracking_no': '','tracking_id':'','tracking_qty':'1','tracking_uom':'','tracking_uom_id':''});
    }
    return trackjson;
	
}   

    function tracking_methodEditor(ui) {
    var $inp = ui.$cell.find("select"),
        di = ui.dataIndx,
        rd = ui.rowData,               
        grid = this;
    
    $inp.on("change", function (evt) {
        var method = $(this).val();
        rd.track_no = '';
        rd.tracking_id = '';       
    })
};
	
function trackingno_editor(ui) {
    var $inp = ui.$cell.find("input"),
        di = ui.dataIndx,
        rd = ui.rowData
        // console.log(batch_items);
        if(rd.batch_method == 'Adjustment')
        {
			 $inp.autocomplete({
                source:  tracking_items[tracking_Index].track_ref_list,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    rd.tracking_id = ui.item.id;
                    rd.tracking_no = ui.item.label;
                    rd.tracking_qty = 1;
                                   
                }

            }).focus(function () {              
                $(this).autocomplete("search", "");
                rd.tracking_no = '';
                rd.tracking_id = ''; 
                        
            }).focusout(function () {              
                if(rd.tracking_no != '' && rd.tracking_id == '')
                {
                    var index = tracking_items[tracking_Index].track_ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == rd.tracking_no.toLowerCase();
                    });
                    if(index > -1){
                        
                        var downKeyEvent = $.Event("keydown");
                        downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                    
                        var enterKeyEvent = $.Event("keydown");
                        enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                        $inp.val([tracking_Index].track_ref_list[index].value); 
                        $inp.trigger(downKeyEvent); 
                        $inp.trigger(downKeyEvent);
                        $inp.trigger(enterKeyEvent);
                    }
                    else{
                        rd.tracking_no = '';
                        rd.tracking_id = '';
                       
                    }
                }
            });
        }
        if(rd.tracking_method == 'New Ref.')
        {
            rd.tracking_id = 0;
            $inp.on("focusout", function () {
                var tracking_no = $(this).val();
                if(tracking_no)
                {
                    var index = tracking_items[tracking_Index].track_ref_list.findIndex(function(obj) {
                       return obj.label.toLowerCase() == batch_no.toLowerCase();
                    });
                    if(index > -1){
                        alert('Btach number alredy exists. To use this batch number change method to adjustment');
                        rd.tracking_no = '';
                        rd.tracking_id = 0;
                        
                        
                    }
                }

            });
        }
    }

 function calculateTrackingSummary() {
        var TrackitemQtyTotal = 0;
        var data = this.option('dataModel.data');

        data.forEach(function(row){
           
            if(row.tracking_qty != '' && row.tracking_no){
                TrackitemQtyTotal += parseFloat(row.tracking_qty);
            }
        })

        tracking_item_balance = parseFloat(tracking_items[tracking_Index].item_qty) - parseFloat(TrackitemQtyTotal);

        tracking_items[tracking_Index].tracking_balance = tracking_item_balance;
        $('#tracking_item_balance').text(tracking_item_balance);


        var TracktotalData = {
            tracking_no: "Total",
            tracking_qty  : TrackitemQtyTotal,
            pq_rowcls: 'grid_footer_color',
            summaryRow: true
        }
            
        this.option('summaryData', [TracktotalData]);
    } 

var tracking_dataModel = {'data':generateTrackingGridData()}; 

var tracking_colModel = [
    { title: "METHOD", dataIndx: "tracking_method", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: 'select',
            options: methods,
            init: tracking_methodEditor
        }
    },
    { title: "TRACKiNG NO", dataIndx: "tracking_no", width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
			init: trackingno_editor
        }
    },

	 { title: "QTY", dataIndx: "tracking_qty", dataType: "float",width: 100, cls: 'pq-drop-icon pq-side-icon',
        editor: {
            type: "textbox",
        },render: function (ui) {
				            var rd = ui.rowData;
				            var cellData=render_qty(ui.cellData); 
				            if(rd.tracking_no!=''){
							rd.tracking_qty=1;
						    return "1";
				            }
						     else{
						     return "";
						     	rd.tracking_qty=0;
						     }
						  },
	validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],					  
    }
   
];

var tracking_billsObj = {
    collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
    height: 'flex',
    selectionModel: { type: 'cell' },
    scrollModel: { autoFit: true },
    pageModel: { type: 'local', rPP: 5 },
    wrap:false,
    numberCell: { show: true },
    dataModel: tracking_dataModel,
    colModel: tracking_colModel,
    change: calculateTrackingSummary, 
    dataReady: calculateTrackingSummary,  
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
$("#trackingmodel").on('shown.bs.modal', function () {   
    if($("#item_tracking_grid").pqGrid('instance')){     
        $("#item_tracking_grid").pqGrid('refresh');
    }
    else
        $("#item_tracking_grid").pqGrid(tracking_billsObj);
});

$("#save_tracking").on("click",function(){
    var status = true;
    status = validateTracking();
    if(!status){
        return;
    }
    status = validateAllTrackingData();
    if(!status){
        return;
    }
    if(status){
        
      
        saveTrackingData();
        $('#trackingmodel').modal('hide');
        //console.log(batch_data);		
		
		
		
        $("#trackinginfo_array").val(JSON.stringify(tracking_data));
        show_loader();
        if($('#bbbCheck').is(":checked") && is_bbb){
            readyPurchaseBills(total);
        }
        else if($('#ccCheck').is(":checked")){
            readyPurchaseCc();
        }
        else{
            $("#salefrm").submit();
        }
        
      }
});

function validateAllTrackingData()
{
    var status = true;
    $.each(item_checked, function(index,obj)
    {
	   var tracking_master_qty  = obj.item_qty;
	
       var trackingqtycount = 0;
        $.each(obj.grid, function(index2, obj2)
        {
            if(obj2.tracking_no != '' && obj2.tracking_qty != '')
            {
                 var tracking_no      = obj2.tracking_no;
				 var tracking_qty     = obj2.tracking_qty;
				 var tracking_uom_id  = obj2.tracking_uom_id;
			 	 trackingqtycount += parseFloat(tracking_qty);
            }
            
        });
       
        if(parseFloat(trackingqtycount) > parseFloat(tracking_master_qty)){
           alert("Unit Qty mismatch!!!! ");
            status = false;
            return status;
        }
    });
	
    return status;
}

function validateTracking()
{
    var sum = 0;
    var data = $("#item_tracking_grid").pqGrid('option', 'dataModel.data');
    var error = 0;
    var trqtycount = 0;
    
    for (var i = 0; i < data.length; i++) 
    {
        if(data[i]['tracking_no'] != '' && data[i]['tracking_qty'] != '')
        {
           
            var tracking_no         = data[i]['tracking_no'];
            var tracking_qty           = data[i]['tracking_qty'];
            var tracking_uom_id      = data[i]['tracking_uom_id'];
          
            trqtycount += parseFloat(tracking_qty);
            
        }
    }
	
	var ttmaster_qty = item_checked[tracking_Index].item_qty;


   if(parseFloat(trqtycount) > parseFloat(ttmaster_qty)){
        alert("Unit Qty mismatch!!!! ");
        return false;
    }
    return true;
}

function saveTrackingData()
{
	//console.log(batch_items);
	tracking_data=[];
    var tracking_item_qty = {};
	
    $.each(tracking_items, function(index,obj)
    {   
        if(obj.item_id!='' && obj.item_name != '')
        {
            var item_id = obj.item_id;
            var item_unit = obj.item_unit;
            var item_unit_id = obj.item_unit_id;

    		var track_master_item_qty = obj.tracking_qty;

            
            var track_item_qty = 0;
            
    		
            $.each(obj.grid, function(index2, obj2)
            {
                var tracking_id            = obj2.tracking_id;
                var tracking_no            = obj2.tracking_no;
                var tracking_qty           = obj2.tracking_qty;
				var tracking_method        = obj2.tracking_method;
               
				if(tracking_no!=''){

					track_item_qty += tracking_qty;

                    tracking_data.push({
                        "tracking_id"        : tracking_id,
                        "item_id"            : item_id,
                        "tracking_no"        : tracking_no,
                        "tracking_qty"       : tracking_qty,
                        "tracking_uom"      : item_unit,
                        "tracking_uom_id"   : item_unit_id,
    					"tracking_method"    : tracking_method
                    });
				}
                 
            });   
            
            
			var track_difference = (parseFloat(track_master_item_qty)-parseFloat(track_item_qty));

	        tracking_data.push({
                "tracking_id"          : 0,
                "item_id"           : item_id,
                "tracking_no"          : 'UNDEFINED',
                "tracking_qty"         : track_difference,
                "tracking_uom"         : item_unit,
                "tracking_uom_id"      : item_unit_id,
                "tracking_method"      : 'Adjustment',
            });
        }
    });
    
    
}

    function set_tracking_items()
    {
        tracking_items = [];

        if(item_checked.length > 0)
        {
            $.each(item_checked, function(index, object)
            {
                var i = tracking_items.findIndex(function(obj) {
                    return obj.item_id == object.item_id && obj.item_unit_id == object.item_unit_id;
                });

                if(i > -1){
                    tracking_items[i].item_qty += parseFloat(object.item_qty);
                }
                else{
                    tracking_items.push({
                      "item_id": object.item_id,
                      "item_name" : object.item_name,
                       "item_qty" : parseFloat(object.item_qty),
                       "item_balance" : parseFloat(object.item_qty),
                       "item_unit" : object.item_unit,
                       "item_unit_id" : object.item_unit_id,
                       "grid": generateTrackingGridData()  
                   });
                }
            });
        }
       
    }


	function readyTracking(){
	 var items_id_array = tracking_items.map(function(obj) { return obj.item_id; });
	 $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>ajax/getItemTracking",
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
                    tracking_items[index].tracking_ref_list = obj;
                });
                stop_loader();
                readyTrackingModel();
            }
        }
    }); 
	
}

function readyTrackingModel(step = '')
{
    if(step == ''){
        tracking_Index = 0;
    }
    else{
        var status = validateTracking();
        if(!status){
            return;
        }
    }
    
    if(step == 'next'){
        if(tracking_Index < (tracking_items.length - 1)){
            tracking_Index = tracking_Index + 1;
        }
    }
    if(step == 'prev'){
        if(tracking_Index > 0){
            tracking_Index = tracking_Index - 1;
        }
    }
    
    if(tracking_items[tracking_Index])
    {
        console.log(tracking_items[tracking_Index]);
        
       
        var item_name = tracking_items[tracking_Index].item_name;
        var item_unit = tracking_items[tracking_Index].item_unit;
		var item_qty = tracking_items[tracking_Index].item_qty;
        var track_item_balance = tracking_items[tracking_Index].item_balance;
		
		
		var page = (tracking_Index + 1) + '/' + tracking_items.length;
        
        $('#tracking_item_name').text(item_name);
		$('#tracking_item_qty').text(item_qty);
		$('#tracking_item_unit').text(item_unit);
		$('#tracking_item_balance').text(track_item_balance);
      
        $('#tracking_page').text(page);
        $('#trackingmodel').modal('show');
        $("#item_tracking_grid").pqGrid('option', 'dataModel.data', tracking_items[tracking_Index].grid);
        $("#item_tracking_grid").pqGrid('refreshDataAndView');

        $('input[type="command-line"]').focus();//tempararily shift focus
        $("#item_tracking_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
    }
    
}   



/************************************************  iTEM tracking WISE CODEING END  *****************************************************/    
    
   $("#submitbtn").on("click",function(){
    bbb_data = [];
    bbb_accounts = {};
 batch_items = [];
    var is_bbb = $('select[name="party_id"] option:selected').data('is_bbb');
    var total = 0;
 tracking_items = [];
      item_checked = [];
     var billsundry_item_checked = [];
     var final_item_id =[];
     var data = $("#grid_search").pqGrid('option', 'dataModel.data');
     var billsundry_data = $("#billsundry_search").pqGrid('option', 'dataModel.data');
     
     
     var final_item_amouint ="0";
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
        var billsundry_id     = billsundry_data[j]['billsundry_id'];
        var billsundry_name     = billsundry_data[j]['billsundry_name'];
        var billsundry_rate     = billsundry_data[j]['billsundry_rate'];
        var billsundry_amount     = billsundry_data[j]['billsundry_amount'];
        
        if(billsundry_id != '' && billsundry_name != ''){
            billsundry_item_checked.push({
                    "billsundry_id": billsundry_id,
                    "billsundry_name": billsundry_name,
                    "billsundry_rate": parseAmount(billsundry_rate),
                    "billsundry_amount" : parseAmount(billsundry_amount),
                 });

            total += parseAmount(billsundry_amount);
        }
        
       
   }
   
       var stock_qtyalert=0;
       var item_total_qty={};
   
       for (var i = 0; i < data.length; i++) {
           var item_id     = data[i]['item_id'];
           var item_name     = data[i]['item_name'];
           var item_price  = data[i]['item_price'];
           var item_qty    = data[i]['item_qty'];
           var item_unit   = data[i]['item_unit'];
           var item_unit_id   = data[i]['item_unit_id'];
           var description = data[i]['description'];
           var item_amount = data[i]['item_amount'];
           var avail_item_qty = data[i]['AvailQty'];
           
           
           if(!item_amount)
             item_amount =0;
             final_item_amouint=parseAmount(final_item_amouint)+parseAmount(item_amount);
            
            if(item_id != '' && item_name != ''){
              
               if(item_total_qty[item_id] == undefined){
                    item_total_qty[item_id] = {};
                    item_total_qty[item_id][item_unit_id] = item_qty;
                }
                else{
                    if(item_total_qty[item_id][item_unit_id] == undefined) 
                        item_total_qty[item_id][item_unit_id] = item_qty;
                    else
                        item_total_qty[item_id][item_unit_id] += item_qty; 
                }
				
				if(item_total_qty[item_id][item_unit_id] > avail_item_qty)
			       stock_qtyalert++; // should be less than or equal to available stock
			       
                item_checked.push({
                   "item_id": item_id,
                    "item_price": parseAmount(item_price),
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "item_unit_id" :item_unit_id,
                    "item_total_amount" :parseAmount(item_amount),
                    "description" :description,

                });

                total += parseAmount(item_amount);
             
            }
         }
      if($('#itmbtchCheck').is(":checked"))
             set_batch_items();
             
              if($('#itmtrackingCheck').is(":checked"))
             set_tracking_items();
      
      
      
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
       else if(stock_qtyalert >0 ){
		  alert_notification("Qty should be less than or equal to stock available!!!");
          return false;  
	  }
       else if(item_checked.length==0 || $("#voucher_series").val()=='' || final_item_amouint=='' || final_item_amouint=='0'){
          alert_notification("Kindly fill the items data!!!");
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
            if($('#itmtrackingCheck').is(":checked") && tracking_items.length>0){
                readyTracking();
            }
			else if($('#itmbtchCheck').is(":checked") && batch_items.length>0){
                readyBatches();
            }
           else if($('#bbbCheck').is(":checked") && is_bbb){
                readySalesBills(total);
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


    function get_item_grid()
    {
        var data = []
        for(var i=1; i<=50; i++){
            data.push({"item_id": '',"item_name": '','pq_cellattr': { "item_name": { title: ""}},'item_qty': '','description': '','item_unit': '','item_unit_id': '','item_price': '','item_amount': ''});
        }
        return data;
    }
    function get_sundry_grid()
    {
        var data = []
        for(var i=1; i<=50; i++){
            data.push({"billsundry_id": '',"billsundry_name": '','billsundry_rate': '','billsundry_amount': ''});
        }
        return data;
    }

     $(function () {
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
       function calculateSummary() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
            itemqtyTotal=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
            if(row.item_price == '' || typeof row.item_price == 'undefined'){
                var item_price =0;
            }else
             var item_price =  row.item_price;
            
            if(row.item_qty == '' || typeof row.item_qty == 'undefined'){
                var item_qty =0;
            }else
            var  item_qty =  row.item_qty;
            
            if(row.item_amount == '' || typeof row.item_amount == 'undefined'){
                var item_amount =0;
            }else
             var item_amount =  row.item_amount;
             
            itemqtyTotal    += parseFloat(item_qty);
            itempriceTotal  += parseAmount(item_price);
            itemamountTotal += parseAmount(item_amount);
            
        })

        var totalData = {
                item_name: "Total",
                item_qty  : itemqtyTotal,
                item_price: itempriceTotal,
                item_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
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
        var element= {};

        $inp.autocomplete({
                source:  <?php echo $item_json_file;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    event.preventDefault();

                    rd.item_id = ui.item.item_id;
                    rd.item_name = ui.item.label;
                    rd.item_unit = ui.item.item_unit;
                    rd.item_unit_id = ui.item.item_unit_id;
                    $(this).val(ui.item.label); 
                 }  
            }).focus(function () {               
                $(this).autocomplete("search", "");
                rd.item_id = '';
                rd.item_name = '';
            }).focusout(function () {   
                if(rd.item_id!='' && rd.item_unit_id!=''){					
					get_item_balances(rd.item_id,rd.item_unit_id,rd);
				}       
                if(rd.item_id == '')
                {
                    rd.item_id = '';
                    rd.item_name = '';
                }
            });
           
        }
        var autoCompleteEditor2 = function (ui) {
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
                    rd.item_unit = ui.item.label;
                    rd.item_unit_id =ui.item.id;
                }
            }).focus(function () {
                $(this).autocomplete("search", "");
                rd.item_unit = '';
                rd.item_unit_id = '';
            }).focusout(function () {     
                if(rd.item_id!='' && rd.item_unit_id!=''){	
					//console.log("unit selected---");			   
					get_item_balances(rd.item_id,rd.item_unit_id,rd);
				}  
                if(rd.item_unit_id == '')
                {
                    var index = unitslist.findIndex(function(obj) {
    
                        var string = obj.label.toLowerCase();
                        var text = rd.item_unit.toLowerCase();
                         
                        return text != '' ? string.includes(text) : false;
                    });
                    if(index > -1){
                        rd.item_unit = unitslist[index].label;
                        rd.item_unit_id = unitslist[index].id;
                        
                    }
                    else{
                        rd.item_unit = '';
                        rd.item_unit_id = '';
                    }
                }
            });
          }
		  
        var colModel = [
                     { title: "ITEM NAME", dataIndx: "item_name", width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                          type: "textbox",
                          init: autoCompleteEditor,
                          options: []
                      }
              },
              { title: "QUANTITY",dataIndx: "item_qty", width: 100, dataType: "float",
                 
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
              { title: "SHORT NARRATION", width: 100, dataType: "string", dataIndx: "description",
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
                        return true;
                    }
                    return false;
                    },
                },
              { title: "UNIT", dataIndx: "item_unit", width: 100,cls: 'pq-drop-icon pq-side-icon',
                editable: function (ui) {
                   var item_id = ui.rowData['item_id'];
                    if (item_id != '') {
                        return true;
                    }
                    return false;
                },
                    editor: {                   
                          type: "textbox",
                          init:autoCompleteEditor2,
                          options: [],
                      },
                       
                     render: function (ui) {
                          var options = ui.column.editor.options,
                          cellData = ui.cellData;
                          for (var i = 0; i < options.length; i++) {
                              var option = options[i];
                                if (option.label == cellData) {
                                  return option.label;
                                }
                          }
                      },
            },
            { title: "PRICE", width: 20, align: "right",dataIndx: "item_price",dataType: "float",
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
                            if(rd.item_qty != '' && rd.item_qty > 0 && rd.item_price != '' && parseAmount(rd.item_price) >= 0){
                                var amount = rd.item_qty * parseAmount(rd.item_price);
                                rd.item_amount = amount;  
                                grid.refreshDataAndView(); 

                            }
                            if(rd.item_qty == '' && rd.item_price != '' && parseAmount(rd.item_price) >= 0){
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
                        rd.item_price = parseAmount(rd.item_price);
                        return formatAmount(rd.item_price);   
                    }
                    return '';
                }
            },
            { title: "AMOUNT", width: 20, align: "right",dataIndx: "item_amount",dataType: "float",
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
                                rd.item_price = price; 
                                grid.refreshDataAndView();        
                            }
                            if(rd.item_qty == '' && rd.item_amount != '' && parseAmount(rd.item_amount) >= 0){
                                rd.item_qty = 1;;
                                rd.item_price = parseAmount(rd.item_amount); 
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
            
        var dataModel = {"data": get_item_grid()}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 450,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            wrap:false,
             numberCell: { show: true },
            dataModel: dataModel,
            change: calculateSummary,
            dataReady: calculateSummary,   
            columnTemplate: { render: commentRender },
            colModel: colModel,  
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
                return false;
           }
        }
        var $grid = $("#grid_search").pqGrid(newObj);
     



        
        var tax_dataModel = {"data":<?php echo json_encode($tax_json_data);?>}
         var tax_colModel = [
            { title: "TAX RATE", dataIndx: "tax_rate",editable: false, width: 100},
            { title: "AMT", width: 100, dataType: "float", dataIndx: "tax_amt"},
            { title: "IGST", width: 100,  dataIndx: "igst" ,dataType: "float",format: '##,###.00'},
            { title: "CGST", width: 100,  dataIndx: "cgst" ,dataType: "float",format: '##,###.00'},
            { title: "SGST", width: 100,  dataIndx: "sgst" ,dataType: "float",format: '##,###.00'}
            ];
         var tax_newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 400,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            wrap:false,
            numberCell: { show: true },
            dataModel: tax_dataModel,
            colModel: tax_colModel,
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
          
    var billsundry_autoComplete = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var element= {};

        $inp.autocomplete({
                source:  <?php echo $bsd_json_file;?>,
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 0,
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
        
         var billsundry_dataModel = {"data":get_sundry_grid()}
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
            height: 400,
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            wrap:false,
            numberCell: { show: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
            change: billsundry_calculateSummary, 
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
     
     $(document).on('change', '[name="type"]', function(e){
         var type = $(this).val();
         e.preventDefault();
         if(type == 'with_item'){
             window.location.href = "<?php echo $base_url; ?>/sales/item";
         }
         else if(type == 'without_item'){
             $(this).val('with_item');
             window.location.href = "<?php echo $base_url; ?>/sales/non_item";
         }
        
     })

        //------------------------------------------------------------------------------ Bill By Bill


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

        
        $("#salefrm").submit(); 
        
    }
});

function saveBillByBillData()
{
    
    var account_id = bbb_accounts.account_id;
    
    
    $.each(bbb_accounts.grid, function(index, obj)
    {
        if(obj.method != '' && obj.reference != '' && obj.reference_id != '')
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



function readySalesBills(total_amount)
{
    var account_id = $('select[name="party_id"]').val();
    var account_name = $('select[name="party_id"] option:selected').text();

    bbb_accounts = {
        'account_id'    : account_id,
        'account_name'  : account_name,
        'drcr'          : 'D',
        'amount'        : total_amount,
        'grid'          : generateBillData()
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
            sum += parseAmount(sub);
              
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
