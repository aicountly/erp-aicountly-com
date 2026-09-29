<?php $header = array( 	'title' => 'Group Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>

<div class="row"><div class="col-md-6">
<h3 class="pb-3">Group Ledgers</h3>
 <p><em>Group: <strong><?php echo $name;?></strong></em></p>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
    </div>
    </div>
    
    <br>
   <div id="grid_search" style="margin:auto;"> </div>  

 <?php


$json_data =array();
 if($account_transactions){ 
    foreach($account_transactions as $key => $row){
		if($row){
 
	
	$json_data[] =array("voucher_txn_id"=>$row['voucher_txn_id'],"voucher_type_id"=>$row['voucher_type_id'],
		                    "col_date"=>ucwords($row['txn_date']),"col_type"=>ucwords($row['voucher_type']),
		                    "col_vchno"=>$row['voucher_no'],"debit"=>$row['debit'],"credit"=>$row['credit'],
		                    "balance"=>$row['balance'],"balance_type"=>$row['balance_type'],'show_balance'=>$row['show_group_balance'],
                            'account_name'=>$row['account_name'],'credit_total'=>$row['credit_total'],'debit_total'=>$row['debit_total'],
                            'acc_id'=> $row['acc_id']
		                    );
		                    
	 } } } 
  $json_data = json_encode($json_data);
  
 ?>
 
 

<?php echo view('includes/footer_scripts'); ?>
<script>
     $(function () {
         
         function calculateSummary() { 
        var debitTotal = 0,
            creditTotal = 0,

            data = this.option('dataModel.data'),
            len = data.length;


        data.forEach(function(row){
            
             
            
            debitTotal +=  parseAmount(row.debit_total);
            creditTotal +=  parseAmount(row.credit_total);
  
           
        })

        var totalData = {
                col_date: "Total",
                col_type :"",
                col_vchno:"",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            console.log(totalData);

        this.option('summaryData', [totalData]);
    }
    
    
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
            { title: "DATE", dataIndx: "col_date", width: 100, render: filterRender,render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
             { title: "ACCOUNT", width: 100, dataIndx: "account_name"},
             { title: "TYPE", width: 100, dataIndx: "col_type"},
             { title: "VCH/BILL NO", width: 100, dataIndx: "col_vchno"},
             { title: "DEBIT(RS.)", width: 100, align: "right", dataIndx: "debit"},
             { title: "CREDIT(RS.)", width: 100, align: "right", dataIndx: "credit"},
             { title: "BALANCE(RS.)", width: 100, align: "right", dataIndx: "balance"},
             { title: "", width: 30, dataType: "string", dataIndx: "balance_type"}
		   
	 	    ];
        var dataModel = {"data":<?php echo $json_data;?>}
        
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
            colModel: colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
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
  	           var rowData    = ui.rowData;
  	           var col_type = rowData.col_type;
  	           var ajax   = rowData.ajax;
		        var voucher_txn_id   = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;

                 if((voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13') && (rowData.acc_id != undefined && rowData.acc_id != 0&& rowData.acc_id != ''))
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id+'/'+rowData.acc_id;

		        if(voucher_type_id=='18')
                     window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='11')
                     window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id; 
                 else if(voucher_type_id=='10')
                     window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id;
                 else if(voucher_type_id=='14')
                     window.location.href= baseurl+'/admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
                 else if(voucher_type_id=='6')
                     window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='7')
                     window.location.href= baseurl+'/admin/delivery_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='2')
                     window.location.href= baseurl+'/admin/credit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='3')
                     window.location.href= baseurl+'/admin/debit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='12')
                     window.location.href= baseurl+'/admin/purchase_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='15')
                     window.location.href= baseurl+'/admin/stock_transfer/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='17')
                     window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id
                 else if(voucher_type_id=='19')
                     window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='21')
                     window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='20')
                     window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;
                 else if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		    
		        var ajax   = rowData.ajax;
		        var col_type = rowData.col_type;
		         var voucher_txn_id   = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;
		       if (evt.keyCode==13){
		           
                   if((voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13') && (rowData.acc_id != undefined && rowData.acc_id != 0&& rowData.acc_id != ''))
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id+'/'+rowData.acc_id;

		          if(voucher_type_id=='18')
                     window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='11')
                     window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id; 
                 else if(voucher_type_id=='10')
                     window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id;
                 else if(voucher_type_id=='14')
                     window.location.href= baseurl+'/admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
                 else if(voucher_type_id=='6')
                     window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='7')
                     window.location.href= baseurl+'/admin/delivery_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='2')
                     window.location.href= baseurl+'/admin/credit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='3')
                     window.location.href= baseurl+'/admin/debit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='12')
                     window.location.href= baseurl+'/admin/purchase_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='15')
                     window.location.href= baseurl+'/admin/stock_transfer/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='17')
                     window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id
                 else if(voucher_type_id=='19')
                     window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='21')
                     window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='20')
                     window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;
                 else if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;
		       }
		   
	       }
        
        var $grid = $("#grid_search").pqGrid(newObj);
    
    });

		  </script>	</body></html>