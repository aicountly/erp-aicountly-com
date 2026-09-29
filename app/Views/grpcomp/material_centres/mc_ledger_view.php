<?php $header = array( 	'title' => 'MC Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>

<div class="row"><div class="col-md-6">
<h3 class="pb-3">MC Ledger</h3>
 <p><em>Material Centre: <strong><?php echo $mc_name;?></strong></em></p>
 
    </div>
    
    <div class="col-md-6 text-end">
        <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
      
    </div>
    </div>
    
    
    <br>
   <div id="grid_search" style="margin:auto;"> </div>  

 <?php
  $json_data = json_encode($mc_transactions);
 
 ?>

<?php echo view('includes/footer_scripts'); ?>
<script>
     $(function () {
         
        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = $toolbar.find(".filterCondition").val(),
                dataIndx = $toolbar.find(".filterColumn").val(),
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
        //filterRender to highlight matching cell text.
        function filterRender(ui) {
            var val = ui.cellData,
                filter = ui.column.filter;
            if (filter && filter.on && filter.value) {
                var condition = filter.condition,
                    valUpper = val.toUpperCase(),
                    txt = filter.value,
                    txt = (txt == null) ? "" : txt.toString(),
                    txtUpper = txt.toUpperCase(),
                    indx = -1;
                if (condition == "end") {
                    indx = valUpper.lastIndexOf(txtUpper);
                    //if not at the end
                    if (indx + txtUpper.length != valUpper.length) {
                        indx = -1;
                    }
                }
                else if (condition == "contain") {
                    indx = valUpper.indexOf(txtUpper);
                }
                else if (condition == "begin") {
                    indx = valUpper.indexOf(txtUpper);
                    //if not at the beginning.
                    if (indx > 0) {
                        indx = -1;
                    }
                }
                if (indx >= 0) {
                    var txt1 = val.substring(0, indx);
                    var txt2 = val.substring(indx, indx + txt.length);
                    var txt3 = val.substring(indx + txt.length);
                    return txt1 + "<span style='background:yellow;color:#333;'>" + txt2 + "</span>" + txt3;
                }
                else {
                    return val;
                }
            }
            else {
                return val;
            }
        }
        var colModel = [
            { title: "DATE", dataIndx: "voucher_date", width: 100, render: filterRender,render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
             { title: "VCH TYPE", align:"center", width: 100, dataIndx: "comp_vch_type"},   
             { title: "VCH No.", align:"center", width: 50, dataIndx: "comp_vch_no"},
             { title: "ACCOUNT", align:"center", width: 100, dataIndx: "account_name"},
             { title: "ITEM", align:"center", width: 100, dataIndx: "item_name"},
             { title: "QTY IN", align:"center", width: 50, dataIndx: "qty_in"},
             { title: "QTY OUT", align:"center", width: 50, dataIndx: "qty_out"},
             { title: "UNIT", align:"center", width: 10, dataIndx: "item_unit"},
             { title: "PRICE", align:"center", width: 10, dataType: "float", dataIndx: "price"},
             { title: "AMOUNT", align:"center", width: 100, dataType: "float", dataIndx: "amount"},
             { title: "CLOSING QTY", align:"center", width: 100, dataIndx: "closing_qty"},
             { title: "CLOSING BAL", align:"center", width: 100, dataType: "float", dataIndx: "closing_bal"},
            ];
        var dataModel = {"data":<?php echo $json_data;?>}
        
        var newObj = {
            
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            // dataReady: calculateSummary,
            colModel: colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            pageModel: { type: 'local' },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
             var grid = this,
                $select_row = $(".select-row"),
                data = ui.dataModel.data;
                grid.setSelection({ rowIndx: 0, focus: true });
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
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler,
                        options: function (ui) {
                            var CM = ui.colModel;
                            var opts = [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                                obj[column.dataIndx] = column.title;
                                opts.push(obj);
                            }
                            return opts;
                        }
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
        
        
        newObj.rowDblClick = function(event, ui) {
  	            var rowData             = ui.rowData;
  	            var col_type            = rowData.vch_type;
  	            var ajax                = rowData.ajax;
		        var voucher_txn_id      = rowData.voucher_txn_id;
		        var voucher_type_id     = rowData.voucher_type_id;
		       
		         if(voucher_type_id=='18')
		             window.location.href= baseurl+'/admin/sales/edit_sale_invoice/'+voucher_txn_id+"/"+voucher_type_id;
		         else if(voucher_type_id=='11')
	                 window.location.href= baseurl+'/admin/purchase/edit_purchase_invoice/'+voucher_txn_id+"/"+voucher_type_id; 
                 else if(voucher_type_id=='10')
	                 window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+"/"+voucher_type_id;
	             else if(voucher_type_id=='7')
	                 window.location.href= baseurl+'/admin/delivery_challan/edit_challan/'+voucher_txn_id+"/"+voucher_type_id;
	             else if(voucher_type_id=='6')
	                 window.location.href= baseurl+'/admin/inward_challan/edit_challan/'+voucher_txn_id+"/"+voucher_type_id;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		    
		        var ajax   = rowData.ajax;
		        var col_type = rowData.col_type;
		         var voucher_txn_id   = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;
		       if (evt.keyCode==13){
		           
		            if(voucher_type_id=='18')
		             window.location.href= baseurl+'/admin/sales/edit_sale_invoice/'+voucher_txn_id+"/"+voucher_type_id;
		            else if(voucher_type_id=='11')
	                 window.location.href= baseurl+'/admin/purchase/edit_purchase_invoice/'+voucher_txn_id+"/"+voucher_type_id; 
	                else if(voucher_type_id=='10')
	                 window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+"/"+voucher_type_id;
	                else if(voucher_type_id=='7')
	                 window.location.href= baseurl+'/admin/delivery_challan/edit_challan/'+voucher_txn_id+"/"+voucher_type_id;
	                else if(voucher_type_id=='6')
	                 window.location.href= baseurl+'/admin/inward_challan/edit_challan/'+voucher_txn_id+"/"+voucher_type_id;
		       }
		   
	       }
        
        var $grid = $("#grid_search").pqGrid(newObj);
    
    });

		  </script>	</body></html>