<?php $header = array( 	'title' => 'BOM Profitability Report' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>


<div class="row"><div class="col-md-6">
<h3 class="pb-3">BOM Profitability Report</h3>
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
        var item_producedTotal = 0,
            byproduct_producedTotal = 0,
            item_consumedTotal = 0,
			additional_costTotal = 0,
			contributionTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;

        data.forEach(function(row){            
            item_producedTotal += parseFloat(row.item_produced_footer);
            byproduct_producedTotal += parseFloat(row.byproduct_produced_footer);			
			item_consumedTotal += parseFloat(row.item_consumed_footer);
            additional_costTotal += parseFloat(row.additional_cost_footer);			
			contributionTotal += parseFloat(row.contribution_footer);
        })

        var totalData = {
                bom_name: "Total",
                item_produced: formatAmount(item_producedTotal),
                byproduct_produced: formatAmount(byproduct_producedTotal),
				item_consumed: formatAmount(item_consumedTotal),
                additional_cost: formatAmount(additional_costTotal),
				 contribution: formatAmount(contributionTotal),
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
                dataIndx = '',//$toolbar.find(".filterColumn").val(),
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
            { title: "BOM NAME",  align: "left",dataIndx: "bom_name"},
			 { title: "BOM GROUP",  align: "left",dataIndx: "bom_grp_name"},
            { title: "TOTAL REVENUE", align: 'center',minWidth: 280, flexWidth: true,hidden:false, colModel: [
                { title: "ITEM PRODUCED",   dataIndx: "item_produced", align:"right" ,
                   },
                { title: "BY PRODUCT PRODUCED",   dataIndx: "byproduct_produced", align:"right",
                    },
            ]},
			{ title: "TOTAL COST", align: 'center',minWidth: 280, flexWidth: true,hidden:false, colModel: [
                { title: "ITEM CONSUMED",   dataIndx: "item_consumed", align:"right" ,
                   },
                { title: "ADDITIONAL COST",   dataIndx: "additional_cost", align:"right",
                    },
            ]},
			{ title: "CONTRIBUTION",  align: "right",dataIndx: "contribution"},
			{ title: "CONTRIBUTION %",  align: "right",dataIndx: "contribution_percent"}
	 	    ];
         var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'item_id':'<?php echo $item_id;?>','from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'view':'<?php echo $type;?>'},
            url: "<?php echo base_url();?>/admin/reports/ajax_bom_profitability",
             getData: function (dataJSON) {
                var data = dataJSON.data;

                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
		   
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
			resizable: true,
           autoResize: true,
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
            colModel: colModel,  
            numberCell: { show: true },
			pageModel: { type: "remote", rPP: 100, strRpp: "{0}" },
            filterModel: { on: true, mode: "OR", header: true, type:'local' },
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
                            var opts = [];
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
                            { "contain": "Contains" },
							{ "begin": "Begins With" },                            
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
		       var bom_id    = rowData.bom_id;
		       set_page();
		      window.location.href= baseurl+'/admin/billofmaterial/modify/'+bom_id;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		         var bom_id   = rowData.bom_id;
		         if(evt.keyCode==13){
		             set_page();
	              window.location.href= baseurl+'/admin/billofmaterial/modify/'+bom_id;
		         }
		   
	       }
        
        var $grid = $("#grid_search").pqGrid(newObj);
		pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });
     
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