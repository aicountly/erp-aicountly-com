<?php $header = array( 	'title' => 'Journal Voucher' ); ?>
<?php echo view('includes/header',$header); ?>

<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
 

?>
<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>
<div class="row mb-2">
    <div class="col-md-6 order-1"> <h3><?php echo $voucher_name;?>  Voucher</h3></div>
	
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
    <select name="type" id="type" class="form-select d-inline-block" style="width:160px;">
        <option value="with_item" selected="selected">With Item</option>
        <option value="without_item">Without Item</option>
    </select>
   <div class="form-check d-inline-block me-2">
    <input class="form-check-input" type="checkbox" value="1" id="bbbCheck">
    <label class="form-check-label" for="bbbCheck">Bill By Bill</label>
   </div>
   <div class="form-check d-inline-block me-2">
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
	<button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
    <ul class="dropdown-menu">
        <!-- <li><a class="dropdown-item" href="#">Action</a></li> -->
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
<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
        <div class="row m-0 p-0">
  
      <div class="col-md-2 col-6 card p-2">
	    <div class="input-group">
                <label class="input-group-text">Date:</label>
               <input type="text" name="sale_date" value="<?php echo $voucher_date;?>" class="datepicker form-control form-control-sm" readonly>
            </div>	  
	 </div>  
      <div class="col-md-2 col-6 card p-2">
	  <div class="input-group">
                <label class="input-group-text">Series:</label>
              <?php	
				echo form_dropdown('voucher_series', $voucher_series_dropdown, $voucher_series,' id="voucher_series" class="selectwidget voucher_series form-control required" ');
				?>
            </div>
		</div>
      <div class="col-md-2 col-6 card p-2">
	   <div class="input-group">
                <label class="input-group-text">Voucher No:</label>
               <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_no;?>" disabled>
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
	 <div class="col-md-3 col-6 card p-2">
        <div class="input-group">
            <label class="input-group-text">MC:</label> 
            <?php echo form_dropdown('matrcntr_id', $matrcntr_dropdown, $matrcntr_id,' id="matrcntr_id" class="form-select required" '); ?>
        </div>
    </div>	
      <div class="col-md-12 col-12 card p-2"><label>Narration:</label> <textarea  name="narration" class="form-control form-control-sm"><?= $narration ?></textarea></div>
      <input type="hidden" name="item_data_from" id="item_data_from">
      <input type="hidden" name="item_data_to" id="item_data_to">
	  
	  <input type="hidden" name="acc_data_from" id="acc_data_from">
      <input type="hidden" name="acc_data_to" id="acc_data_to">

      <input type="hidden" name="bbbdata" id="bbbdata">
      <input type="hidden" name="ccdata" id="ccdata">
	  
    </div></div>
   

   
   
    <br>
    <div class="col-12"><div class="row">
       <div class="col-md-6">
           <h4 class=" text-center">Inward Items</h4>
           <div id="grid_item1" style="margin:auto;"></div> 
		   <br>
		   <h4 class=" text-center">Accounts/Billsundry To Be Debited</h4>
           <div id="grid_acc1" style="margin:auto;"></div> 
		   <br>
		   <h4 id="total_left_side" class="text-center">&nbsp;</h4>
         
        </div>
      
        <div class="col-md-6">
            <h4 class=" text-center">Outward Items</h4>
            <div id="grid_item2" style="margin:auto;"></div> 
            <br>
            <h4 class=" text-center">Accounts/Billsundry To Be Credited</h4>
            <div id="grid_acc2" style="margin:auto;"></div> 
            <br>
            <h4 id="total_right_side" class="text-center">&nbsp;</h4>
        </div>
    </div> 
    
     <div class="col-12 text-center">
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-success btn-lg">Quit</a>
        <a href="javascript:main(0)"  class="deletebtn btn btn-lg btn-danger mx-2">Delete</a>
        
    </div>
        
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

    $json_data_item1 = $l_items;
    $json_data_item2 = $r_items;
    $json_data_acc1 = $l_accounts;
    $json_data_acc2 = $r_accounts;


 for($i=1;$i<=500;$i++){ 
  $json_data_item1[] = array("item_id"=>'',"item_name"=>'','item_qty'=>'','description'=>'','item_unit'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');

  $json_data_item2[] = array("item_id"=>'',"item_name"=>'','item_qty'=>'','description'=>'','item_unit'=>'','item_unit_id'=>'','item_price'=>'','item_amount'=>'');

  $json_data_acc1[] = array("acc_id"=> '',"acc_name"=> '','acc_type'=> '','acc_amount'=> '','is_bbb'=>'','is_cc'=>'');

  $json_data_acc2[] = array("acc_id"=> '',"acc_name"=> '','acc_type'=> '','acc_amount'=> '','is_bbb'=>'','is_cc'=>'');

 }
?>
<style>
    .boldcell{font-weight:700;}
</style>

<script>

$(".deletebtn").on('click',function(){
   confirm_delete(baseurl+"/admin/vouchers/delete/<?php echo $voucher_txn_id;?>");          
   return false
})
var unitslist = <?php echo json_encode($units_list);?>;

var bbb_data = [];
var bbb_accounts = [];

var cc_data = [];
var cc_accounts = [];


// ----------------------------------------------------------------------item common

    function calculateSummary_item() {
        var item_price_total = 0,
            item_qty_total = 0,
            item_amount_total=0,
            data = this.option('dataModel.data'),
            len  = data.length;

        data.forEach(function(row){
            if(row.item_id != '' && row.item_name != '' && row.item_price != '' && row.item_qty != '' && row.item_amount != '')
            {
                item_price_total  += parseAmount(row.item_price);
                item_qty_total    += parseInt(row.item_qty);
                item_amount_total += parseAmount(row.item_amount);
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
        grid_balances();
    }

    var autoCompleteEditor_unit = function (ui) {
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
            if(rd.item_unit_id == '' && rd.item_unit != '')
            {
                var index = unitslist.findIndex(function(obj) {

                    var string = obj.label.toLowerCase();
                    var text = rd.item_unit.toLowerCase();
                     
                    return string.includes(text);
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

    var autoCompleteEditor_item = function (ui) {
        var $inp = ui.$cell.find("input");
        var rd = ui.rowData;
        var element= {};

        $inp.autocomplete({
            source:   <?php echo $item_json_file; ?>,
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 0,
            select: function(event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);

                rd.item_id = ui.item.item_id;
                rd.item_name = ui.item.label;
                rd.item_unit = ui.item.item_unit;
                rd.item_unit_id = ui.item.item_unit_id;
             }  
        }).focus(function () {               
            $(this).autocomplete("search", "");
            rd.item_id = '';
            rd.item_name = '';
            rd.item_unit = '';
            rd.item_unit_id = '';
        }).focusout(function () {              
            if(rd.item_id == '')
            {
                rd.item_id = '';
                rd.item_name = '';
                rd.item_unit = '';
                rd.item_unit_id = '';
            }
        });
       
    }

// ----------------------------------------------------------------------item 1 start


    var colModel_item1 = [
        { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                type: "textbox",
                init: autoCompleteEditor_item,
                options: []
            },
        },
        { title: "QTY", width: 20, dataType: "float", dataIndx: "item_qty",
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

        { title: "UOM", dataIndx: "item_unit", width: 20,cls: 'pq-drop-icon pq-side-icon',
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
        { title: "AMOUNT", width: 20, align: "right",dataIndx: "item_amount",dataType: "float",editable: false,
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
            
    var dataModel_item1 = {"data":<?php echo json_encode($json_data_item1);?>}
        
        
    var newObj_item1 = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        height: 450,
        selectionModel: { type: 'row' },
        scrollModel: { autoFit: true },
        dataModel: dataModel_item1,
        colModel: colModel_item1,  
        numberCell: { show: true },
        editable: true,
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
        wrap:false,
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
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

// ----------------------------------------------------------------------item 2 start

    var colModel_item2 = [
        { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
                type: "textbox",
                init: autoCompleteEditor_item,
                options: []
            },
        },
        { title: "QTY", width: 20, dataType: "float", dataIndx: "item_qty",
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

        { title: "UOM", dataIndx: "item_unit", width: 20,cls: 'pq-drop-icon pq-side-icon',
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
        { title: "AMOUNT", width: 20, align: "right",dataIndx: "item_amount",dataType: "float",editable: false,
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
            
    var dataModel_item2 = {"data":<?php echo json_encode($json_data_item2);?>}
        
        
    var newObj_item2 = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        height: 450,
        selectionModel: { type: 'row' },
        scrollModel: { autoFit: true },
        dataModel: dataModel_item2,
        colModel: colModel_item2,  
        numberCell: { show: true },
        editable: true,
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
        wrap:false,
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

// ----------------------------------------------------------------------acc  common

    function calculateSummary_acc() {
        var acc_amount_total = 0,           
        data = this.option('dataModel.data'),
        len  = data.length;

        data.forEach(function(row){

            if(row.acc_id != '' && row.acc_name != '' && row.acc_amount != '')
            {
                acc_amount_total += parseAmount(row.acc_amount)
            }
        });

        var totalData = {
            acc_name: "Total",
            acc_type  : "",
            acc_amount: acc_amount_total,
            pq_rowcls: 'grid_footer_color',
            summaryRow: true
        }

        this.option('summaryData', [totalData]);
        grid_balances();
    }

    var autoCompleteEditor_acc = function (ui) {
        var rd = ui.rowData;
        var $inp = ui.$cell.find("input");
        var element= {};
        $inp.autocomplete({
            source:  <?php echo $acc_bsd_json_file;?>,
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 3,
            select: function(event, ui) {
                event.preventDefault();
                rd.acc_name = ui.item.label;
                rd.acc_id = ui.item.id;
                if(ui.item.is_sundry==1)                    
                    rd.acc_type = 'bsd';
                else if(ui.item.is_acc==1)                   
                    rd.acc_type = 'acc';
            
                rd.is_bbb = ui.item.is_bbb;
                rd.is_cc = ui.item.is_cc;

                $(this).val(ui.item.label);
             }
            
        }).focus(function () {
            $(this).autocomplete("search", "");
            rd.acc_name = '';
            rd.acc_id = '';
            rd.acc_type = '';

            rd.is_bbb = '';
            rd.is_cc = '';

        }).focusout(function () {  
            if(rd.acc_id == '')
            {
                rd.acc_name = '';
                rd.acc_id = '';
                rd.acc_type = '';

                rd.is_bbb = '';
                rd.is_cc = '';
            }
        });
    }

// ----------------------------------------------------------------------acc1 start

    var dataModel_acc1 = {"data":<?php echo json_encode($json_data_acc1);?>}

    var colModel_acc1 = [
        { title: "PARTICULARS", width: 100, dataType: "string", align: "left",dataIndx: "acc_name" ,
            editor: {                   
                type: "textbox",
                init: autoCompleteEditor_acc,
                options: []
            },             
        },
        { title: "AMOUNT", width: 20, align: "right",dataIndx: "acc_amount",dataType: "float",
            validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
            editable: function (ui) {
               var account_id = ui.rowData['acc_id'];
                if (account_id != '') {
                    return true;
                }
                return false;
            },
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.acc_amount != ''){
                    rd.acc_amount = parseAmount(rd.acc_amount);
                    return formatAmount(rd.acc_amount);   
                }
                return '';
            }
        },              
    ];
          
    var newObj_acc1 = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        height: 450,
        selectionModel: { type: 'row' },
        scrollModel: { autoFit: true },
        dataModel: dataModel_acc1,
        colModel: colModel_acc1,             
        numberCell: { show: true },
        change: calculateSummary_acc, 
        dataReady: calculateSummary_acc, 
        editable: true,
        editModel: {
            clicksToEdit: 1,
            keyUpDown: false
        },
        wrap:false,
        cellSave: function(evt, ui){
            this.refresh();
        },
        showTitle: true,
        create: function (evt, ui) { // make first row auto selected
            var grid = this,
            $select_row = $(".select-row"),
            data = ui.dataModel.data;
            grid.setSelection({ rowIndx: 0, focus: false });
        }
    };

    newObj_acc1.cellKeyDown = function(evt, ui) {
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

    $("#grid_acc1").pqGrid(newObj_acc1);

// ----------------------------------------------------------------------acc2 start

    var dataModel_acc2 = {"data":<?php echo json_encode($json_data_acc2);?>}

    var colModel_acc2 = [
        { title: "PARTICULARS", width: 100, dataType: "string", align: "left",dataIndx: "acc_name" ,
            editor: {                   
                type: "textbox",
                init: autoCompleteEditor_acc,
                options: []
            },             
        },
        { title: "AMOUNT", width: 20, align: "right",dataIndx: "acc_amount",dataType: "float",
            validations: [{ type: 'gte', value: 0, msg: "should be > 0"}],
            editable: function (ui) {
               var account_id = ui.rowData['acc_id'];
                if (account_id != '') {
                    return true;
                }
                return false;
            },
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.acc_amount != ''){
                    rd.acc_amount = parseAmount(rd.acc_amount);
                    return formatAmount(rd.acc_amount);   
                }
                return '';
            }
        },             
    ];
          
    var newObj_acc2 = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        height: 450,
        selectionModel: { type: 'row' },
        scrollModel: { autoFit: true },
        dataModel: dataModel_acc2,
        colModel: colModel_acc2,             
        numberCell: { show: true },
        change: calculateSummary_acc, 
        dataReady: calculateSummary_acc, 
        editable: true,
        editModel: {
            clicksToEdit: 1,
            keyUpDown: false
        },
        cellSave: function(evt, ui){
            this.refresh();
        },
        wrap:false,
        showTitle: true,
        create: function (evt, ui) {// make first row auto selected
            var grid = this,
            $select_row = $(".select-row"),
            data = ui.dataModel.data;
            grid.setSelection({ rowIndx: 0, focus: false });
        }
    };

    newObj_acc2.cellKeyDown = function(evt, ui) {
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

    $("#grid_acc2").pqGrid(newObj_acc2);

//-------------------------------------------------------------------------------

    function grid_total_item(obj)
    {
        var total = 0;
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');

            data.forEach(function(row){
                if(row.item_id != '' && row.item_id != undefined && row.item_amount != '' && row.item_amount != undefined)
                {
                    total += parseAmount(row.item_amount)
                }
            });
        }
        return total;
    }

    function grid_total_acc(obj)
    {
        var total = 0;
        if($(obj).pqGrid('instance')){ 

            var data = $(obj).pqGrid('option', 'dataModel.data');

            data.forEach(function(row){
                if(row.acc_id != '' && row.acc_id != undefined && row.acc_amount != '' && row.acc_amount != undefined)
                {
                    total += parseAmount(row.acc_amount)
                }
            });
        }
        return total;
    }

    function grid_balances(){
    
        var l_total = 0;
        var r_total = 0;

        l_total += grid_total_item($('#grid_item1'));
        l_total += grid_total_acc($('#grid_acc1'));

        r_total += grid_total_item($('#grid_item2'));
        r_total += grid_total_acc($('#grid_acc2'));
          
      
        
        $("#total_left_side").html(formatAmount(l_total));
        $("#total_right_side").html(formatAmount(r_total));
        
        if(parseAmount(l_total) == parseAmount(r_total) && parseAmount(l_total) > 0)
            return true;

        return false;
    }

    
    function grid_response_item(obj)
    {
        var item_checked = [];
        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');

            data.forEach(function(row){
                if(row.item_id != '' && row.item_name != '')
                {
        
                    var item_id       = row.item_id;
                    var item_name     = row.item_name;
                    var item_price    = parseAmount(row.item_price);
                    var item_qty      = row.item_qty;
                    var item_unit     = row.item_unit;
                    var item_unit_id  = row.item_unit_id;
                    var item_amount   = parseAmount(row.item_amount);

                    item_checked.push({
                        "item_id"           : item_id,
                        "item_price"        : item_price,
                        "item_qty"          : item_qty,
                        "item_unit"         : item_unit,
                        "item_unit_id"      : item_unit_id,
                        "item_total_amount" : item_amount
                    });
                    
                }
            });

            
        }
        return item_checked;
    }

    function grid_response_acc(obj, drcr)
    {
        var acc_checked = [];
        var bbb_check = $('#bbbCheck').is(":checked");
        var cc_check = $('#ccCheck').is(":checked");

        if($(obj).pqGrid('instance')){ 
            var data = $(obj).pqGrid('option', 'dataModel.data');

            data.forEach(function(row){
                if(row.acc_id != '' && row.acc_name != '')
                {
                    var acc_id        = row.acc_id;
                    var acc_name      = row.acc_name;
                    var acc_type      = row.acc_type;
                    var acc_amount    = parseAmount(row.acc_amount);

                    var is_bbb       = row.is_bbb;
                    var is_cc        = row.is_cc;

                    acc_checked.push({
                        "acc_id"        : acc_id,
                        "acc_name"      : acc_name,
                        "acc_type"      : acc_type,
                        "acc_amount"    : acc_amount,

                        "is_bbb" : is_bbb,
                        "is_cc" : is_cc,
                    });

                    if(bbb_check && is_bbb == 1)
                    { 
                        bbb_accounts.push({
                            "account_id":  acc_id,
                            "account_name": acc_name,
                            "drcr": drcr,
                            "amount": acc_amount,
                            "grid": generateBillData(acc_id)
                        }); 
                    }
            
                    if(cc_check && is_cc == 1)
                    { 
                        cc_accounts.push({
                            "account_id"    : acc_id,
                            "account_name"  : acc_name,
                            "drcr"          : drcr,
                            "amount"        : acc_amount,
                            "grid"          : generateCcData(acc_id)
                        }); 
                    }
                }
            });

        }
        return acc_checked;
    }

   $("#submitbtn").on("click",function(){

        bbb_data = [];
        bbb_accounts = [];

        cc_data = [];
        cc_accounts = [];

        var grid_item1 = grid_response_item($('#grid_item1'));
        var grid_item2 = grid_response_item($('#grid_item2'));

        var grid_acc1 = grid_response_acc($('#grid_acc1'), 'D');  //capital D
        var grid_acc2 = grid_response_acc($('#grid_acc2'), 'C');  //capital C

     
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
	    else if(!grid_balances()){
            alert_notification("Voucher Totals Wrong!!!");
            return false;    
        } 
        else{
            $("#item_data_from").val(JSON.stringify(grid_item1));
            $("#item_data_to").val(JSON.stringify(grid_item2));
    		 
    		$("#acc_data_from").val(JSON.stringify(grid_acc1));
            $("#acc_data_to").val(JSON.stringify(grid_acc2));
    	    
            show_loader();
        
            if($('#bbbCheck').is(":checked") && bbb_accounts.length > 0){ // bbbstart
                readyBills();
            }
            else if($('#ccCheck').is(":checked") && cc_accounts.length > 0){ //ccstart
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
                stop_loader();
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status){
                    alert_success(response.message);
                    window.history.back();
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
        $("#bbbdata").val(JSON.stringify(bbb_data));
        show_loader();

        if(cc_accounts.length > 0){
            readyCc();
        }
        else{
            $("#salefrm").submit();
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
        if(total != parseAmount(final_total)){
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
    
    if(final_drcr == 'C'){
        final_amount = -final_amount;
    }
    
    if(sum != parseAmount(final_amount) && count > 0){
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
        
        if(row.method != '' && row.reference != '' && row.amount != '' && row.drcr != '')
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
    
    if(final_drcr == 'C'){
        final_amount = -final_amount;
    }
    
    if(sum != parseAmount(final_amount) && count > 0){
        alert('Total Mismatch');
        return false;
    }
    
    return true;
}
   
var cc_list = [];
function readyCc()
{
    var item_checked = grid_response_item($('#grid_item1'));

    $.ajax({
        type: "POST",
        url: "<?php echo $base_url; ?>/vouchers/getPurchaseCc",
        data: { itmsdata : item_checked},
        datatype: "json",
        success: function(response){
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }
            if(response.status)
            {
                var item_pur_cc_accounts = response.data.cc_accounts;
                $.each(item_pur_cc_accounts, function(index,obj){
                    item_pur_cc_accounts[index]['grid'] = generateCcData(obj.account_id);

                    cc_accounts.push(item_pur_cc_accounts[index]);
                });

                cc_list     = response.data.cc_list;
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
 </script>	
</body>
</html>
