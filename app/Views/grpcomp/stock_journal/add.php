<?php $header = array( 	'title' => 'Stock Journal' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
  .salesgrid.gridtable .row{ display: grid; grid-template-columns:5% 40% 15% 10% 15% 15%;}
  .gridtable .row.head .col{padding:8px;}
  .salesgrid.gridtable input.form-control-plaintext{text-align:center;}
  .gridtable .row .col{padding:0px;}
  .gridtable .row .col input,.gridtable .row .col select{border:0px;}
  .taxgrid .row{display: grid; grid-template-columns:20% 20% 20% 20% 20%;}
  .billgrid .row{ display: grid; grid-template-columns:10% 40% 25% 25%;}
  .gridtable .foot.row{ grid-template-columns:100%;}
  .form p, .gridtable p, .form-control-plaintext{margin:0px; padding:0px !important;}
  .form-control-sm{padding: .375rem .2rem;}
  .card p{margin-bottom:3px;} 
</style>
<div class="row mb-2">
<div class="col-md-4 col-6"><h3 class="pb-3">Stock Journal</h3></div>
<div class="col-md-6 text-end order-md-2 order-3">
<div class="dropdown d-sm-flex d-block float-end">
  
  <button class="btn btn-success m-1" type="button">View V</button>   
   
<div class="taskmenus"><a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

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
</div>


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






<div class="col-md-2 col-6 text-end order-md-3 order-2"><a href="#" class="btn btn-outline-success">Back</a></div>

</div>


<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
        <div class="row m-0 p-0">
      
      <div class="col-md-3 col-6 card p-2"><label>Date:</label> <input type="text" name="sale_date" value="<?php echo date('Y-m-d');?>" class="datepicker form-control form-control-sm" readonly></div>  
      <div class="col-md-3 col-6 card p-2"><label>Series:</label>
      <?php	
        echo form_dropdown('voucher_series', $voucher_series_dropdown, $voucher_id,' id="voucher_series" class="selectwidget voucher_series form-control required" ');
		?></div>
      <div class="col-md-3 col-6 card p-2"><label>Voucher No:</label> <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled></div>
      
      <div class="col-md-3 col-6 card p-2"><label>Material Centre:</label><?php	
        echo form_dropdown('matrcntr_id', $matrcntr_dropdown, set_value('matrcntr_id'),' id="matrcntr_id" class="selectwidget form-control required" ');
		?></div>
	
      <div class="col-md-12 col-12 card p-2"><label>Narration:</label> <input type="text" name="narration" class="form-control form-control-sm"></div>
      <input type="hidden" name="itmsdatafrom" id="itmsdatafrom">
      <input type="hidden" name="itmsdatato" id="itmsdatato">
    </div></div>
   

   
   
    <br>
    <div class="col-12 text-center"><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-6">
           <h4>Journal Transferred From</h4>
           <div id="grid_search" style="margin:auto;"></div> 
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-6">
           <h4>Journal Transferred To</h4>
        <div id="grid_search2" style="margin:auto;"></div> 
  </div>

    </div> 
    
     <div class="col-12 text-center">
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg">Save</button>
        <a href="#" class="btn btn-success btn-lg">Quit</a>
        
    </div>
        
    </div>
   
</form>
<?php echo view('includes/footer_scripts');  
 for($i=1;$i<=50;$i++){ 
  $json_data[] =array("item_unit_id"=>"","item_id"=>"","id"=>'',"item_name"=>'','item_qty'=>'','short_narator'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');
  $json_to_data[] =array("id"=>'',"to_item_name"=>'','to_item_qty'=>'','to_short_narator'=>'','to_item_unit'=>'','to_item_price'=>'','to_item_amount'=>'');
 }
?>
<style>
    .boldcell{font-weight:700;}
</style>
<?php
$itemn_units_labels=array(); 
if($items_list['items_array']){
    foreach($items_list['items_array'] as $itmrr){
       $itemn_units_labels[trim($itmrr['label'])]= $itmrr['item_unit'];
    }
}

?>
<script>
var intRegex = /^\d+$/;
var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
var itemsobj = new Map();
var itemsobj_to = new Map();
 var itemunirsobj = new Map();
  var itemunirsobj_to = new Map();
    var itemlist  = [];
   
    <?php if($itemn_units_labels!=''){ ?>
    var unitslables =<?php echo json_encode($itemn_units_labels);?>;
    var unitslables_to =<?php echo json_encode($itemn_units_labels);?>;
    <?php } else { ?>
     var unitslables = [];
     var unitslables_to = [];
    <?php } ?>
   $("#submitbtn").on("click",function(){
     var item_checked = [];
      var item_checked_to = [];
     var final_item_id =[];
     
     var grid1 = $("#grid_search").pqGrid('option', 'dataModel.data');
     var grid2 = $("#grid_search2").pqGrid('option', 'dataModel.data');
     
     var response_grid1 = grid_from_data(grid1,item_checked);
     var response_grid2 = grid_to_data(grid2,item_checked_to);
  
     var response_grid1_info = $.parseJSON(response_grid1);
     var final_item_amouint_grid1 = response_grid1_info['to_amount'];
     if(final_item_amouint_grid1=="0")
     var grid1_items  =[];
     else
     var grid1_items  = response_grid1_info['item_checked'];
     
     var response_grid2_info = $.parseJSON(response_grid2);
     var final_item_amouint_grid2 = response_grid2_info['from_amount'];
      if(final_item_amouint_grid2=="0")
     var grid2_items  =[];
     else
     var grid2_items              = response_grid2_info['item_checked'];
     
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
      
       else if(grid1_items.length==0 || grid2_items.length==0 || $("#voucher_series").val()=='' || final_item_amouint_grid2=='' || final_item_amouint_grid2=='0' || final_item_amouint_grid1=='' || final_item_amouint_grid1=='0'){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
       else{
         $("#itmsdatafrom").val(JSON.stringify(grid1_items));
         $("#itmsdatato").val(JSON.stringify(grid2_items));
      show_loader();
      $("#salefrm").submit();     
       }
});

function grid_from_data(data,item_checked){
     
     var final_item_amouint ="0";
       for (var i = 0; i < data.length; i++) {
           var item_name     = data[i]['item_name'];
           var item_price    = data[i]['item_price'];
           var item_qty      = data[i]['item_qty'];
           var item_unit     = data[i]['item_unit'];
           var item_amount   = data[i]['item_amount'];
           
           if(!item_amount)
             item_amount =0;
             final_item_amouint=parseInt(final_item_amouint)+parseInt(item_amount);
            
            if(item_name!=''){
           
		  item_checked.push({
		           "item_id": itemsobj.get(item_name),
                    "item_price": item_price,
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "item_total_amount" :item_amount
                 });
             
            }
         }
    var final_response={item_checked:item_checked,from_amount:final_item_amouint};     
    return JSON.stringify(final_response);     
    
}
function grid_to_data(data,item_checked_to){
     
     var final_item_amouint ="0";
       for (var i = 0; i < data.length; i++) {
           var item_name     = data[i]['to_item_name'];
           var item_price    = data[i]['to_item_price'];
           var item_qty      = data[i]['to_item_qty'];
           var item_unit     = data[i]['to_item_unit'];
           var item_amount   = data[i]['to_item_amount'];
           
           if(!item_amount)
             item_amount =0;
             final_item_amouint = parseInt(final_item_amouint)+parseInt(item_amount);
            
            if(item_name!=''){
        		  item_checked_to.push({
        		           "item_id": itemsobj_to.get(item_name),
                            "item_price": item_price,
                            "item_qty" :item_qty,
                            "item_unit" :item_unit,
                            "item_total_amount" :item_amount
                            
                         });
               }
         }  
    var final_response={item_checked:item_checked_to,to_amount:final_item_amouint};     
    return JSON.stringify(final_response);       
    
}

     $(function () {
          
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
             
            itempriceTotal  += parseFloat(item_price);
            itemamountTotal += parseFloat(item_amount);
            itemqtyTotal    += parseInt(item_qty);
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
          
          function calculateSummary_to() {
        var itempriceTotal = 0,
            itemamountTotal = 0,
            itemqtyTotal=0,
            data_to = this.option('dataModel.data'),
            len  = data.length;

        data_to.forEach(function(row){
            if(row.to_item_price == '' || typeof row.to_item_price == 'undefined'){
                var item_price =0;
            }else
             var item_price =  row.to_item_price;
            
            if(row.to_item_qty == '' || typeof row.to_item_qty == 'undefined'){
                var item_qty =0;
            }else
            var  item_qty =  row.item_qty;
            
            if(row.to_item_amount == '' || typeof row.to_item_amount == 'undefined'){
                var item_amount =0;
            }else
             var item_amount =  row.to_item_amount;
             
            itempriceTotal  += parseFloat(item_price);
            itemamountTotal += parseFloat(item_amount);
            itemqtyTotal    += parseInt(item_qty);
        })

        var totalData_to = {
                item_name: "Total",
                item_qty  : itemqtyTotal,
                item_price: itempriceTotal,
                item_amount: itemamountTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData_to]);
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
        var element= {};
        var rd = ui.rowData;
		$inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/sales/ajax_company_items",
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 3,
                select: function(event, ui) {
                    console.log(ui.item);
					event.preventDefault();
					
					 rd.item_id = ui.item.item_id;
					 rd.item_unit_id = ui.item.item_unit_id;
				    //itemsobj[ui.item.label] = 
				    //itemunirsobj[ui.item.label] = ui.item.units;
				    var itemlabel =  ui.item.label;
				    var itemunts  =  ui.item.units;
				    var itmval    =  ui.item.value;
				    //itemunirsobj.set(itemlabel, itemunts);
				    itemsobj.set(itemlabel, itmval);
				    $(this).val(ui.item.label);
			     }
				
            }).focus(function () {
                //open the autocomplete upon focus               
                $(this).autocomplete("search", "");
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
                if(rd.item_unit_id == '')
                {
                    rd.item_unit = '';
                    rd.item_unit_id = '';
                }
            });
          }
          
         var autoCompleteEditor_to = function (ui) {
        var $inp = ui.$cell.find("input");
        var element= {};
        var rd = ui.rowData; 
		$inp.autocomplete({
                source:  "<?php echo base_url();?>/admin/sales/ajax_company_items",
                selectItem: { on: true }, //custom option
                highlightText: { on: true }, //custom option
                minLength: 3,
                select: function(event, ui) {
                    console.log(ui.item);
					event.preventDefault();
				    //itemsobj[ui.item.label] = 
				    //itemunirsobj[ui.item.label] = ui.item.units;
				    var itemlabel =  ui.item.label;
				     rd.item_id = ui.item.item_id;
				    var itemunts  =  ui.item.units;
				    var itmval    =  ui.item.value;
				    rd.item_unit = ui.item.item_unit;
                    rd.item_unit_id = ui.item.item_unit_id;
                    
				    
				    itemunirsobj_to.set(itemlabel, itemunts);
				    itemsobj_to.set(itemlabel, itmval);
				    $(this).val(ui.item.label);
			     }
				
            }).focus(function () {
                //open the autocomplete upon focus               
                $(this).autocomplete("search", "");
            });
           
        }
        var autoCompleteEditor3 = function (ui) {
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
                if(rd.item_unit_id == '')
                {
                    rd.item_unit = '';
                    rd.item_unit_id = '';
                }
            });
          }
  
        var colModel = [
                     { title: "ITEM NAME", dataIndx: "item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor,
                          options: itemlist
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
          { title: "QTY", width: 20, dataType: "integer", dataIndx: "item_qty"},
          { title: "UOM", dataIndx: "item_unit", width: 20,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		   type: "textbox",
                           init:autoCompleteEditor2,
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
              
            { title: "PRICE", width: 20, align: "right",dataIndx: "item_price",dataType: "float",format: '##,###.00'},
            { title: "AMOUNT", width: 20, align: "right",dataIndx: "item_amount",dataType: "float",format: '##,###.00',
		       formula: function (ui) {
                    var rd = ui.rowData;
                    if(rd.item_price >0){
                        var calamount = (rd.item_qty * rd.item_price);
                        
                            return calamount; 
                             
                         }
                }
		      },
	 	    ];
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel: colModel,  
            numberCell: { show: true },
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
        
        
        
        var colModel2 = [
                     { title: "ITEM NAME", dataIndx: "to_item_name", align: "left",width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          init: autoCompleteEditor_to,
                          options: itemlist
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
          { title: "QTY", width: 20, dataType: "integer", dataIndx: "to_item_qty"},
          { title: "UOM", dataIndx: "to_item_unit", width: 20,cls: 'pq-drop-icon pq-side-icon',editor: {                   
                		  type: "textbox",
                          options: unitslables_to,
                      },
                      formula: function (ui) {                           
                            var rd = ui.rowData;
                            if(rd.to_item_name)
                            return itemunirsobj_to.get(rd.to_item_name);
                        }, 
                      render: function (ui) {
                          var rd = ui.rowData;
                        
           		       	  var options = ui.column.editor.options,
	    		          cellData = ui.cellData;
		    		      for (var i = 0; i < options.length; i++) {
		    		          var option = options[i];
		    		       
		    		            if(rd.to_item_name){
                              return unitslables_to[rd.to_item_name];
                              
		    		            }
		    		           else if (option.label == cellData) {
		    		              return option.label;
		    		            }
		    		      }
    		   		  },
            },
              
            { title: "PRICE", width: 20, align: "right",dataIndx: "to_item_price",dataType: "float",format: '##,###.00'},
            { title: "AMOUNT", width: 20, align: "right",dataIndx: "to_item_amount",dataType: "float",format: '##,###.00',
		       formula: function (ui) {
                    var rd = ui.rowData;
                    if(rd.to_item_price >0){
                        var calamount = (rd.to_item_qty * rd.to_item_price);
                        
                            return calamount; 
                             
                         }
                }
		      },
	 	    ];
	 	var dataModel2 = {"data":<?php echo json_encode($json_to_data);?>}    
        var newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel2,
            colModel: colModel2,  
            numberCell: { show: true },
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
        var $grid2 = $("#grid_search2").pqGrid(newObj2);
     
});


 </script>	
</body>
</html>
