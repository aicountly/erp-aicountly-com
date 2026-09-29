<?php $header = array( 	'title' => 'Accounts Summary' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>


<div class="row"><div class="col-md-6">
<h3 class="pb-3">Account Summary</h3>
 <p><em>Account: <strong><?php echo $account_name;?></strong></em></p>
    </div>
    
    <div class="col-md-6 text-end">
        <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
       <p class="text-end p-3"><em>Opening Balance: <strong><?php echo $acc_opn_balance;?></strong></em></p>
    </div>
    </div>
 
    </div>
   
   <div id="grid_search" style="margin:auto;"> </div>   
 
 <?php


	$json_data =array();


 if($acc_months_list){ 
    foreach($acc_months_list as $key => $row){
		if($row){

	
	$json_data[] =array("from_date"=>$row['start_date'],"to_date"=>$row['end_date'],"account_id"=>$row['account_id'],"month_name"=>ucwords($row['month_name']),"debit"=>$row['debit'],"credit"=>$row['credit'],"debit_total"=>$row['debit_total'],"credit_total"=>$row['credit_total'],'balance'=>$row['balance'],'balance_type'=>$row['balance_type']);

 } } } 
  $json_data = json_encode($json_data);
  
  $fy_begndt = date('Y',strtotime($fy_months_list['fy_begndt']));
 $fy_end    = date('y',strtotime($fy_months_list['fy_end']));
 
 ?>
  

<?php echo view('includes/footer_scripts'); ?>
<script>
     $(function () {
         
         function calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;

        data.forEach(function(row){
            
            debitTotal += parseFloat(row.debit_total);
            creditTotal += parseFloat(row.credit_total);

        })

        var totalData = {
                month_name: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }

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
            { title: "MONTH(<?php echo $fy_begndt;?>-<?php echo $fy_end;?>)", dataIndx: "month_name", width: 100, render: filterRender,render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
            { title: "DEBIT(RS.)", width: 100, align: "right",dataIndx: "debit"},
            { title: "CREDIT(RS.)", width: 100, align: "right", dataIndx: "credit"},
            { title: "BALANCE(RS.)", width: 100, align: "right", dataIndx: "balance"},
            { title: "", width: 30, dataType: "string", dataIndx: "balance_type",}
		   
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
                  const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);

                    this.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                    
                    
                }else{
                    var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
                }
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
  	           var rowData      = ui.rowData;
		       var from_date         = rowData.from_date;
               var to_date         = rowData.to_date;
		       var account_id   = rowData.account_id;
		       set_page();
		       window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var from_date         = rowData.from_date;
                 var to_date         = rowData.to_date;
		         var account_id   = rowData.account_id;
		         if(evt.keyCode==13){
		             set_page();
	               window.location.href= baseurl+'/admin/accounts/ledger_detail/'+account_id+"?from_date="+from_date+"&to_date="+to_date;
		         }
		   
	       }
        
        var $grid = $("#grid_search").pqGrid(newObj);
     
     if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        $("#grid_search").pqGrid('loadState'); 
    }

    function set_page()
    {
       var select_row = $("#grid_search").pqGrid("selection", { type:'row', method:'getSelection'});
        if(select_row){
            const url = new URL(window.location.href);
            url.searchParams.set('rowIndx', select_row[0].rowIndx);
            window.history.replaceState(null, null, url);
        }
        else{
            const url = new URL(window.location.href);
            if(url.searchParams.has('rowIndx')){
                url.searchParams.delete('rowIndx');
                window.history.replaceState(null, null, url);  
            }
        } 
    }
    });

		  </script>	</body>
		  </html>