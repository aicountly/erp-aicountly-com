<?php $header = array( 	'title' => 'Exception (Txn)' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
<div class="row"><div class="col-md-6">
<h3 class="pb-3">Exception (Txn)</h3>
  
    </div>
    
    <div class="col-md-6 text-end">
         <a href="javascript:void(0);"  class="deletebtn btn btn-outline-danger btn-sm">« Delete</a>
         <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
    </div>
    </div>
    
    <br>
   <div id="grid_search" style="margin:auto;"> </div>  
   
 <?php
	$cc_counter =0; 
	$all_debit  =0;
	$all_credit =0;
	$json_data =array();
    $ac_trial_balance_list = array();
    if($ac_trial_balance_list){ 
    foreach($ac_trial_balance_list as $key => $row){
		if($row){
		   $cc_counter++;  
	
    	$all_debit =$all_debit+$row['debit'];
	    $all_credit =$all_credit+$row['credit'];
 	
		$json_data[] =array("account_id"=>$row['account_id'],"account_name"=>ucwords($row['account_name']),"debit"=>$row['debit'],"credit"=>$row['credit']);
 } } }
  $json_data = json_encode($json_data);
 ?>

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
            
           { title: '<input type="checkbox" id="select_all" class="hidden">', dataIndx: "checkbox",sortable: false },
            { title: "DATE", dataIndx: "date"},
             { title: "VOUCHER TYPE" ,dataType: "float",dataIndx: "voucher_type"},    
             { title: "PARTICULARS",  dataType: "float",dataIndx: "particulars"}, 
            { title: "DEBIT(Dr.)",  dataType: "float",dataIndx: "debit",format: '##,###.00'},
            { title: "CREDIT(Cr.)",  dataType: "float", dataIndx: "credit",format: '##,###.00'},
             { title: "DELETION DATE",  dataType: "float",dataIndx: "deletion_date"},    
           
		   
	 	    ];
          var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            //postData: {"tblename":"sdsdsd"},
            url: "<?php echo base_url();?>/admin/reports/ajax_exceptions_txns",
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
            load:function(event,ui) {
               
                $(this).one("pqgridrefresh", function(){
                    $(this).pqGrid( 'flex' );
                });
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
  	            var rowData             = ui.rowData;
  	            var acct_txn_id      = rowData.acc_oth_txn_id;
		        var voucher_type_id     = rowData.voucher_type_id;
		      
		      window.location.href= baseurl+'/admin/reports/exception_txn_items/'+acct_txn_id+"/"+voucher_type_id;
	     }
	     
         newObj.cellKeyDown = function(evt, ui) {
	           var rowData           = ui.rowData;
		       var voucher_txn_id    = rowData.voucher_txn_id;
		       var voucher_type_id   = rowData.voucher_type_id;
		       
		       if (evt.keyCode==13){
		           
		           window.location.href= baseurl+'/admin/reports/exception_txn_items/'+acct_txn_id+"/"+voucher_type_id;
		       }
		   
	       }
        
        var $grid = $("#grid_search").pqGrid(newObj);
		$('.pq-grid').on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });
         $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#grid_search").pqGrid('saveState');
       });
        
        
        
        

        $(document).on('click','#select_all',function(){
             if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;				
            });
			
             }else{
             $('.checkbox').each(function(){
                this.checked = false;
            })
		
           }
        });
        
         $(document).on('click',".deletebtn",function(){
         if($('.voucher_row:checked').length=='0')  {
             alert_notification("First select voucher to delete!!");
             return false;
         }
        else{
           var vouchersarray=[];
           $('.voucher_row:checked').each(function(){
              vouchersarray.push($(this).val());
          });
          
        confirm_exception_delete(vouchersarray);
      

      }
      
         }) 
         
         
          $(document).on('click',".restorebtn",function(){
         if($('.voucher_row:checked').length=='0')  {
             alert_notification("First select voucher to restore!!");
             return false;
         }
        else{
           var vouchersarray=[];
           $('.voucher_row:checked').each(function(){
              vouchersarray.push($(this).val());
          });
          
        //confirm_exception_delete(vouchersarray);
      

      }
      
         }) 
         
    /*
    if($("#grid_search").pqGrid('option', 'dataModel.data' ).length=="0"){
        
        $(".deletebtn").hide();
        $("#select_all").hide();
    } */
    
    });

</script> </body></html>