<?php $header = array( 	'title' => 'Group Balance' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>
<div class="row"><div class="col-md-6">
<h3 class="pb-3">Group Balance</h3>
  <p><em>Group: <strong><?php echo $group_info['acc_grp_name'];?></strong></em></p>

    </div>
    
    <div class="col-md-6 text-end">
        <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
    
    </div>
    </div>
    <br>
   <div id="grid_search" style="margin:auto;"> </div>  
 
 <?php
	$cc_counter =0; 
    $json_data = array();
 
   if($groups_list){ 
    foreach($groups_list as $key => $row){
		if($row){
		    if($row['balance']<0){
		         $credit = str_replace("-",'',$row['balance']);
		         $balance_type = 'CR.';
		         $debit = 0;
		         
		      }
		      else{
		        $credit = 0;
		        $debit = $row['balance'];
		        $balance_type = 'DR.';
		       
		      }
		    if(isset($row['account_id']))
		     $account_id=$row['account_id'];
		     else
		     $account_id ='';
			$json_data[] =array("account_id"=>$account_id,"group_id"=>$row['group_id'],"account_name"=>ucwords($row['account_name']),
			                    "debit"=>$debit,"credit"=>$credit,"balance_type"=>$balance_type,"account_type"=>$row['account_type'],
			                    "have_childs"=>$row['have_childs']);
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
            { title: "ACCOUNT/GROUP", dataIndx: "account_name",  render: filterRender,render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
            { title: "TYPE",  dataType: "float",dataIndx: "account_type"},
            { title: "DEBIT",  dataType: "float", dataIndx: "debit",format: '##,###.00'},
            { title: "CREDIT", dataType: "float", dataIndx: "credit",format: '##,###.00'},
            { title: "",  dataType: "float", dataIndx: "balance_type",align:'left'},
		   
	 	    ];
        var dataModel = {"data":<?php echo $json_data;?>}
        
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
           /* pageModel: { type: 'local' }, */
           
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
  	     	var rowData    = ui.rowData;
		     var account_id   = rowData.account_id;
		     var group_id     = rowData.group_id;
		     var have_childs  = rowData.have_childs;
		     
		     if(have_childs>0)
		    window.location.href= baseurl+'/admin/accounts/group_report/'+group_id;
		    else
		    window.location.href= baseurl+'/admin/accounts/accounts_trial/'+group_id; 
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		    
		        var account_id   = rowData.account_id;
		     var group_id     = rowData.group_id;
		     var have_childs  = rowData.have_childs;
		       if (evt.keyCode==13){
	               if(have_childs>0)
		    window.location.href= baseurl+'/admin/accounts/group_report/'+group_id;
		    else
		    window.location.href= baseurl+'/admin/accounts/accounts_trial/'+group_id; 
		       }
		   
	       }
        
        var $grid = $("#grid_search").pqGrid(newObj);
    pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });
    });

</script> 
