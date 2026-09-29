<?php $header = array( 	'title' => 'Item Tracking Report - All Tracked Items' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>


<div class="row"><div class="col-md-6">
<h3 class="pb-3">Item Tracking Report - All Tracked Items</h3>
    </div>
    
    <div class="col-md-6 text-end">
        <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
      
    </div>
    </div>
 
    </div>
   
   <div id="grid_search" style="margin:auto;"> </div>   
 
 

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
            { title: "ITEM NAME", width: 100, align: "right",dataIndx: "item_name"},
            { title: "TRACKING NO", width: 100, align: "right", dataIndx: "track_no"},
			 { title: "MC", width: 100, align: "right", dataIndx: "track_mc"}
	 	    ];
         var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'item_id':'0','from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'view':'<?php echo $type;?>'},
            url: "<?php echo base_url();?>/admin/reports/ajax_item_tracking",
             getData: function (dataJSON) {
                var data = dataJSON.data;

                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
		   
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
            colModel: colModel,  
            numberCell: { show: true },
			pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            filterModel: { mode: 'OR' },
            editable: false,
			wrap:false,
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
		       var from_date    = rowData.from_date;
               var to_date    = rowData.to_date;
		       var item_id    = rowData.item_id;
		       set_page();
		       window.location.href= baseurl+'/admin/reports/item_tracking_ledger/'+item_id+"?from_date="+from_date+"&to_date="+to_date;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var from_date         = rowData.from_date;
                 var to_date         = rowData.to_date;
		         var item_id   = rowData.item_id;
		         if(evt.keyCode==13){
		             set_page();
	              window.location.href= baseurl+'/admin/reports/item_tracking_ledger/'+item_id+"?from_date="+from_date+"&to_date="+to_date;
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