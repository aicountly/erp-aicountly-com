<?php $header = array( 	'title' => 'Add Sale Invoice' ); ?>
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
<h3 class="pb-3">Sales Invoice</h3>
 <br>
<form class="form" method="post" id="salefrm">
    <div class="col-12">
        <div class="row m-0 p-0">
      <div class="col-md-2 col-6 card p-2"><label>Series:</label> <input type="text" name="series" class="form-control form-control-sm" autofocus=""></div>
      <div class="col-md-2 col-6 card p-2"><label>Date:</label> <input type="text" name="date" class="datepicker form-control form-control-sm" readonly></div>  
      <div class="col-md-2 col-6 card p-2"><label>Voucher:</label> <input type="text" name="voucher" class="form-control form-control-sm"></div>
      <div class="col-md-2 col-6 card p-2"><label>Party:</label> <input type="text" name="party" class="form-control form-control-sm"></div>
      <div class="col-md-4 col-6 card p-2"><label>Material Centre:</label> <input type="text" name="mc" class="form-control form-control-sm"></div>
      <div class="col-md-12 col-6 card p-2"><label>Narration:</label> <input type="text" name="narration" class="form-control form-control-sm"></div>
      <input type="hidden" name="itmsdata" id="itmsdata">

    </div></div>
   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br> <br><br> <br>
    <div class="col-12 text-center"><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-5">
           <h4>Tax Summary</h4>
           <div id="taxgrid_search" style="margin:auto;"></div>  
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-7">
           <h4>Bill Summary</h4>
        <div class="col-12 my-2 billgrid gridtable" style="height:200px; overflow: auto; max-width:100%">
    <div class="row head">
     <div class="col">S.No</div>
     <div class="col">Bill Sundry</div>
     <div class="col">Rate</div>
     <div class="col">Amount</div>
  </div>
   <div class="row">
     <div class="col">1</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">AI Group esdfscdsfsd</div>
     <div class="col">$ 2022</div>
  </div>
     <div class="row">
     <div class="col">1</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">AI Group esdfscdsfsd</div>
     <div class="col">$ 2022</div>
  </div>
     <div class="row">
     <div class="col">1</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">AI Group esdfscdsfsd</div>
     <div class="col">$ 2022</div>
  </div>
     <div class="row">
     <div class="col">1</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">AI Group esdfscdsfsd</div>
     <div class="col">$ 2022</div>
  </div>
     <div class="row">
     <div class="col">1</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">AI Group esdfscdsfsd</div>
     <div class="col">$ 2022</div>
  </div>
     <div class="row">
     <div class="col">1</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">AI Group esdfscdsfsd</div>
     <div class="col">$ 2022</div>
  </div>
     <div class="row">
     <div class="col">1</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">AI Group esdfscdsfsd</div>
     <div class="col">$ 2022</div>
  </div>
</div>
  </div>

    </div> 
    
    
    <div class="col-12 text-center my-2">
        <a href="#" class="btn btn-outline-success">Save</a> <a href="#" class="btn btn-outline-success">Save</a>
        <a href="#" class="btn btn-outline-success">Save</a> <a href="#" class="btn btn-outline-success">Save</a> 
        <a href="#" class="btn btn-outline-success">Save</a> <a href="#" class="btn btn-outline-success">Save</a>
        <a href="#" class="btn btn-outline-success">Save</a>
        
    </div>
     <div class="col-12 text-center">
        <button type="button" id="submitbtn" class="btn btn-success btn-lg">Save</button> <a href="#" class="btn btn-success btn-lg">Quit</a>
        
    </div>
        
    </div>
   
</form>
<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=50;$i++)
$json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','item_unit'=>'','item_price'=>'','item_amount'=>'');

$tax_json_data[] =array("id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'');

?>
<style>
    .boldcell{font-weight:700;}
</style>
<script>
$("#submitbtn").on("click",function(){
   var item_checked = [];
   var data = $("#grid_search").pqGrid('option', 'dataModel.data');
   for (var i = 0; i < data.length; i++) {
       var item_id     = data[i]['item_name'];
       var item_price  = data[i]['3'];
       var item_qty    = data[i]['item_qty'];
       var item_unit   =  data[i]['item_unit'];
       var item_amount = data[i]['4'];
       if(item_id!=''){
            item_checked.push({
                "item_id": item_id,
                "item_price": item_price,
                "item_qty" :item_qty,
                "item_unit" :item_unit,
                "item_amount" :item_amount
                 });
             
       }
   }
   $("#itmsdata").val(JSON.stringify(item_checked));
   
  $("#salefrm").submit();
});
/*
editor: {
                    type: "textbox",
                    init: autoCompleteEditor
                },
                validations: [
                    { type: 'minLen', value: 1 },
                    { type: function (ui) {
                        var value = ui.value,
                            _found = false;
                            
                            
                        
                    }
                    }
                ]
*/
     $(function () {
         
         var itemlist = <?php echo json_encode($items_list);?>;  
       var unitslist = <?php echo json_encode($units_list);?>; 
       
        var autoCompleteEditor = function (ui) {
            var $inp = ui.$cell.find("input");
            $inp.autocomplete({
                source: itemlist,
                selectItem: { on: true },
                highlightText: { on: true }, 
                minLength: 0
            }).focus(function () {
                          
                $(this).autocomplete("search", "");
            });
        }
        
        
        var colModel = [
            { title: "ITEM NAME", dataIndx: "item_name", width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {
                    type: 'select',
                    options: itemlist,
                    init: function (ui) {
                        ui.$cell.find("select").pqSelect();
                        setTimeout(function () {
                            ui.$cell.find("select").pqSelect('open');
                        })
                    }
                },render: function (ui) {
                    var option = ui.column.editor.options.find(function (obj) {
                        return (obj[ui.cellData] != null);
                    });
                    return option ? option[ui.cellData] : "";
                }
                
                
            },
            { title: "QUANTITY", width: 100, dataType: "number", dataIndx: "item_qty"},
            { title: "UNIT", width: 100, dataIndx: "item_unit",editor: {
                    type: 'select',
                    options: unitslist,
                    init: function (ui) {
                        ui.$cell.find("select").pqSelect();
                        setTimeout(function () {
                            ui.$cell.find("select").pqSelect('open');
                        })
                    }
                },render: function (ui) {
                    var option = ui.column.editor.options.find(function (obj) {
                        return (obj[ui.cellData] != null);
                    });
                    return option ? option[ui.cellData] : "";
                }
                
                
            },
            { title: "PRICE", width: 100, dataType: "float",format: '##,###.00'},
            { title: "AMOUNT", width: 100, dataType: "float",format: '##,###.00'},
            
		   
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
            height: 'flex',
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            dataModel: tax_dataModel,
            colModel: tax_colModel,  
            numberCell: { show: false },
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
                    grid.setSelection({ rowIndx: 0, focus: true });
                }
           };
          var $grid = $("#taxgrid_search").pqGrid(tax_newObj);
        
     });

 </script>	
</body>
</html>
