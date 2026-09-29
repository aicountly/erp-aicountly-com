<?php $header = array( 	'title' => 'Stock Transfer' ); ?>
<?php echo view('includes/header',$header); ?>

<div class="row mb-2">
<div class="col-md-6 order-1"><h3>Stock Transfer (other BO)</h3></div>
<div class="col-md-6 order-3 order-md-2 text-end"><div class="taskmenus">
     <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
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
  <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>  
    </div>
    </div> 

 <div class="col-md-6 order-2 order-md-3"></div>  
  <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
      <button class="btn btn-success m-1" type="button">View</button> 
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


<form class="form" method="post" id="salefrm" autocomplete="off" novalidate>
    <div class="col-12">
        <div class="row m-0 p-0">

      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Transfer Type:</label>
        <select name="transfer_type" class="form-control form-control-sm">
            <option value="type1">Stock Transfer (other MC)</option>
            <option value="type2">Stock Transfer Receipt</option>
            <option selected value="type3">Stock Transfer (other BO)</option>
        </select>
      </div></div>
    
	 <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Date:</label> <input type="text" name="sale_date" value="<?php echo date('d-m-Y');?>" class="datepicker form-control form-control-sm" readonly></div></div>
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Series:</label>
      <?php	
        echo form_dropdown('voucher_series', $voucher_series_dropdown, '18',' id="voucher_series" class="selectwidget voucher_series form-control required" ');
		?></div></div>
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Voucher No:</label> <input type="text" name="voucher" class="form-control form-control-sm" value="<?php echo $voucher_auto_no;?>" disabled></div></div>
       
       <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">Transporter:</label> <input type="text" name="transporter" class="form-control form-control-sm"></div></div>
      
      <div class="col-md-2 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">E-Way:</label> <input type="text" name="e-way" class="form-control form-control-sm"></div></div>
      
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">From BO:</label> <input type="text" name="from_bo" class="form-control form-control-sm"></div></div>
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">From MC:</label> <input type="text" name="from_mc" class="form-control form-control-sm"></div></div>
      
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">To BO:</label> <input type="text" name="to_bo" class="form-control form-control-sm"></div></div>
      <div class="col-md-3 col-6 card p-2"><div class="input-group">
              <label class="input-group-text">To MC:</label> <input type="text" name="to_mc" class="form-control form-control-sm"></div></div>
      
     
      <div class="col-md-12 col-12 card p-2"><div class="input-group">
              <label class="input-group-text">Long Narration:</label>
			 <textarea  name="narration" class="form-control form-control-sm"></textarea></div></div>
      <input type="hidden" name="itmsdata" id="itmsdata">

    </div></div>
   

   <div id="grid_search" style="margin:auto;"></div>  
   
    <br>
    <div class="col-12 text-center"><div class="row">
       <!-- Tax Summary -->
       <div class="col-md-5">
           <h4>Tax Summary</h4>
           <div id="taxgrid_search" style="margin:auto;"></div>  
         
  </div>
       <!-- Tax Summary Ends -->
      
      <!-- Bill Summary Starts -->
       <div class="col-md-7">
           <h4>Bill Sundry</h4>
        <div id="billsundry_search" style="margin:auto;"></div> 
  </div>

    </div> 
    
     <div class="col-12 text-center">
         <br><br>
         <!--<input type="file" class="">-->
         
        <button type="button" id="submitbtn" class="btn btn-success btn-lg" title="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>" alt="<?php echo $_ENV['SAVEKEY_SRTCDE'];?>">Save</button>
        <button type="reset" id="submitbtn" class="btn btn-success btn-lg">Reset</button>
         <a href="javascript:void(0);" onclick="window.history.go(-1); return false;" class="btn btn-secondary btn-lg">Quit</a>
        
    </div>
        
    </div>
   
</form>
<?php echo view('includes/footer_scripts'); 
for($i=1;$i<=50;$i++)
$json_data[] =array("id"=>'',"item_name"=>'','item_qty'=>'','item_price'=>'','item_amount'=>'','received_item_qty'=>'','received_item_price'=>'','received_item_amount'=>'','diff_item_qty'=>'','diff_item_amount'=>'', 'reason' => '');

$tax_json_data[] =array("id"=>'',"tax_rate"=>'','tax_amt'=>'','igst'=>'','cgst'=>'','sgst'=>'');
$billsundry_json_data[] =array("billsrno"=>'',"billsundry_name"=>'','billrate'=>'','billamount'=>'');
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
    var itemlist  = <?php echo json_encode($items_list['items_array']);?>;  
    var unitslist = <?php echo json_encode($units_list);?>; 
    var unitslables = <?php echo json_encode($itemn_units_labels);?>;
   $("#submitbtn").on("click",function(){
     var item_checked = [];
     var final_item_id =[];
     var data = $("#grid_search").pqGrid('option', 'dataModel.data');
     var final_item_amouint ="0";
   
       for (var i = 0; i < data.length; i++) {
           var item_name     = data[i]['item_name'];
           var item_price  = data[i]['item_price'];
           var item_qty    = data[i]['item_qty'];
           var item_unit   = data[i]['item_unit'];
           var short_narator = data[i]['short_narator'];
           var item_amount = data[i]['item_amount'];
           
           if(!item_amount)
             item_amount =0;
             final_item_amouint=parseInt(final_item_amouint)+parseInt(item_amount);
            
            if(item_name!=''){
          
              for(var k = 0;k < itemlist.length; k++){
                var item_option = itemlist[k];
                if (item_option.label == item_name) {
                         final_item_id.push(item_option.value);
                }
             }
           
		  item_checked.push({
		           "item_id": final_item_id[i],
                    "item_price": item_price,
                    "item_qty" :item_qty,
                    "item_unit" :item_unit,
                    "item_total_amount" :item_amount,
                    "short_narator" :short_narator
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
      
       else if(item_checked.length==0 || $("#voucher_series").val()=='' || final_item_amouint=='' || final_item_amouint=='0'){
          alert_notification("Kindly fill the items data!!!");
          return false;   
       }
       else{
        $("#itmsdata").val(JSON.stringify(item_checked));
        
      show_loader();
      $("#salefrm").submit();     
       }
});

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
		
        //initialize the editor
        $inp.autocomplete({
                source: itemlist,
                selectItem: { on: true }, //custom option
                highlightText: { on: false }, //custom option
                minLength: 0,
                select: function(event, ui) {
					event.preventDefault();
					$(this).val(ui.item.label);
				}
            }).focus(function () {
                //open the autocomplete upon focus               
                $(this).autocomplete("search", "");
            });
           
        }
        var autoCompleteEditor2 = function (ui) {
            var $inp = ui.$cell.find("input");
			$inp.autocomplete({
                source: unitslist,
                selectItem: { on: true }, //custom option
                highlightText: { on: false }, //custom option
                minLength: 0,
                select: function(event, ui) {
                    
					event.preventDefault();
					$(this).val(ui.item.label);
					
				}
            }).focus(function () {
                $(this).autocomplete("search", "");
            });
           
        }
        
      

        var colModel = [
                     { title: "ITEM NAME",sortable:false, dataIndx: "item_name", width: 100,cls: 'pq-drop-icon pq-side-icon',editor: {                   
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
            { title: "QTY",sortable:false, width: 20, dataType: "integer", dataIndx: "item_qty"},
            { title: "PRICE",sortable:false, width: 20, align: "right",dataIndx: "item_price",dataType: "float",format: '##,###.00'},
            { title: "AMOUNT", sortable:false,width: 20, align: "right",dataIndx: "item_amount",dataType: "float",format: '##,###.00',
		       formula: function (ui) {
                    var rd = ui.rowData;
                    if(rd.item_price >0){
                        var calamount = (rd.item_qty * rd.item_price);
                        
                            return calamount; 
                             
                         }
                }
		      },
		      { title: "Received", width: 60, align: "center", colModel: [
                { title: "Qty", align: "center",dataIndx: "received_item_qty" }, 
                { title: "Price", align: "center",dataIndx: "received_item_qty" }, 
                { title: "AMOUNT", align: "center",dataIndx: "received_item_value"}
                ] },
                
              { title: "Difference", width: 40, align: "center", colModel: [
                { title: "Qty", align: "center",dataIndx: "diff_item_qty" },  
                { title: "AMOUNT", align: "center",dataIndx: "diff_item_value"}
                ] },
              { title: "Reason", align: "center", width: 100, dataIndx: "reason"},
	 	    ];
	 	    
        var dataModel = {"data":<?php echo json_encode($json_data);?>}
        
        
        var newObj = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            change: calculateSummary,   
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
           
           
          $("#taxgrid_search").pqGrid(tax_newObj);
         var billsundry_dataModel = {"data":<?php echo json_encode($tax_json_data);?>}
         var billsundry_colModel = [
             { title: "SR NO", dataIndx: "billsrno",editable: false, width: 100},
             { title: "BILL SUNDRY", width: 100, dataType: "string", dataIndx: "billsundry_name"},
             { title: "RATE", width: 100,  dataIndx: "billrate" ,dataType: "float",format: '##,###.00'},
             { title: "AMOUNT", width: 100,  dataIndx: "billamount" ,dataType: "float",format: '##,###.00'},
            ];
          
          var tax_newObj2 = {
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'cell' },
            scrollModel: { autoFit: true },
            dataModel: billsundry_dataModel,
            colModel: billsundry_colModel,  
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
          
             $("#billsundry_search").pqGrid(tax_newObj2); 
          
        
     });
     
     $(document).on('change', '[name="transfer_type"]', function(){
         var type = $(this).val();
         if(type == 'type1'){
             window.location.href = "<?php echo $base_url ?>stock_transfer/add";
         }
         if(type == 'type2'){
             window.location.href = "<?php echo $base_url ?>stock_transfer/add2";
         }
         if(type == 'type3'){
             window.location.href = "<?php echo $base_url ?>stock_transfer/add3";
         }
     });
 </script>	
</body>
</html>
