<?php $header = array( 	'title' => 'Exception (Txn) Items' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
<div class="row"><div class="col-md-6">
<h3 class="pb-3">Exception (Txn) Entries</h3>
  
    </div>
    
    <div class="col-md-6 text-end">
        
         <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
    </div>
    </div>
    
    <br>
   <div id="grid_search" style="margin:auto;"> </div>  
  

    </div>  

<?php echo view('includes/footer_scripts'); ?>
<script>
     $(function () {
         
         function calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;

        data.forEach(function(row){
            
            if(typeof row.debit == 'undefined'){
                var debit =0;
            }else
             debit =  row.debit;
            
            if(typeof row.credit == 'undefined'){
                var credit =0;
            }else
             credit =  row.credit;
             
            
            debitTotal += debit;
            creditTotal += credit;
           
        })

        var totalData = {
                account_name: "Total",
                debit: debitTotal,
                credit: creditTotal,
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
                dataIndx = "",//$toolbar.find(".filterColumn").val(),
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
        var colModel = [
            
            { title: "DATE", dataIndx: "date", width: 50},
            { title: "VOUCHER BILL NO", width: 100, dataType: "float",dataIndx: "voucher_no"},    
            { title: "ACCOUNT", width: 200, dataType: "float",dataIndx: "particulars"}, 
            { title: "DEBIT(Dr.)", width: 70, dataType: "float",dataIndx: "debit",format: '##,###.00'},
            { title: "CREDIT(Cr.)", width: 70, dataType: "float", dataIndx: "credit",format: '##,###.00'},
            ];
          var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            //postData: {"tblename":"sdsdsd"},
            url: "<?php echo base_url();?>/admin/reports/ajax_get_exception_txn_items/<?php echo $acct_txn_id;?>/<?php echo $voucher_type_id;?>",
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
            numberCell: { show: false },
            filterModel: { mode: 'OR' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
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
        
      
        var $grid = $("#grid_search").pqGrid(newObj);

        
    
    
    });

</script> </body></html>