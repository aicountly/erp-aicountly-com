<?php $header = array( 	'title' => 'Accounts Summary' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
 .myform label{width:25%; float:left;}
 .myform .form-control, .myform select {width:75%;}
</style>
<div class="row mb-2">
  <div class="col-md-6">
    <h3 class="pb-0">Account Summary</h3>
    <p>
     <em>Account: <strong><?php echo $account_name;?></strong></em>
    </p>
  </div>

  <?php 
    $params = http_build_query([
      'acc_opn_balance'          => $acc_opn_balance,
      'account_name'        => $account_name,
      'summary'      => $summary,
       'view'         => $view,
    ]);
    ?>
  
  <div class="col-md-6 text-end">
    <div class="taskmenus">
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined open-comingsoon">offline_bolt</span>
      </a>
      <a href="<?= base_url() ?>admin/export/monthly_summary_print?<?= $params ?>
      " >
        <span class="material-symbols-outlined ">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined open-comingsoon">download</span>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a>
          </li> 
          <li>
            <a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a>
          </li>
        </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="material-symbols-outlined open-comingsoon">share</span>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="#">Facebook</a>
          </li>
          <li>
            <a class="dropdown-item" href="#">Twitter</a>
          </li>
          <li>
            <a class="dropdown-item" href="#">Instagram</a>
          </li>
        </ul>
      </li>
    </div>
	<p> <em>Opening Balance: <strong><?php echo $acc_opn_balance;?></strong></em></p>
  </div>
  
  
  <div class="col-md-4">
    
  </div>
  
  
  <div class="col-md-8 text-end">
    <form class="form" method="get" id="salefrm2" autocomplete="off">
    <div class="dropdown d-inline-block me-1" style="width:220px;">
      <div class="input-group input-group-sm input-group">
        <span class="input-group-text">View</span>
        <select form="salefrm2" class="form-select" name="view"  id="view">
          <option <?= ($view==1) ? 'selected' : '' ?> value="1"> Monthly</option>
          <option <?= ($view==2) ? 'selected' : '' ?> value="2">Quaterly</option>
          <option <?= ($view==3) ? 'selected' : '' ?> value="3">Half Yearly</option>
          <option  value="acc_ledger">Account Ledger</option>
        </select>
        
      </div>
    </div>
	 <div class="dropdown float-end">
      <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success m-1">Back</a>
    </div>
	</form>
   
  </div>
  
  
</div>


   <div id="grid_search" style="margin:auto;"> </div>   
 
  
 <!-- The Modal -->
<div class="modal" id="voucherCreationModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create Voucher</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        
        <div class="row p-2">
            <div class="col-md-6">
                <h4>Sales-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/sales/item?p=1" class="list-group-item list-group-item-action">Sales Invoice</a>
                  <a href="<?= base_url() ?>admin/sales_order/item?p=1" class="list-group-item list-group-item-action">Sales Order</a>
                  <a href="<?= base_url() ?>admin/credit_note/item?p=1" class="list-group-item list-group-item-action">Credit Note</a>
                  <a href="<?= base_url() ?>admin/delivery_challan/add?p=1" class="list-group-item list-group-item-action">Delivery Challan</a>
                  <a href="<?= base_url() ?>admin/quotations/item?p=1" class="list-group-item list-group-item-action">Quotations</a>
                </div>
            </div>
            <div class="col-md-6">
                <h4>Purchases-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/purchase/item?p=1" class="list-group-item list-group-item-action">Purchase Invoice</a>
                  <a href="<?= base_url() ?>admin/purchase_order/item?p=1" class="list-group-item list-group-item-action">Purchase Order</a>
                  <a href="<?= base_url() ?>admin/debit_note/item?p=1" class="list-group-item list-group-item-action">Debit Note</a>
                  <a href="<?= base_url() ?>admin/inward_challan/add?p=1" class="list-group-item list-group-item-action">Inward Challan</a>
                  <a href="<?= base_url() ?>admin/purchase_requisition/with_amount?p=1" class="list-group-item list-group-item-action">Purchase Requisition</a>
                </div>
            </div>

            
        </div>

        <div class="row p-2">
            <div class="col-md-6">
                <h4>Banking-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/vouchers/invoice/9?p=1" class="list-group-item list-group-item-action">Payments</a>
                  <a href="<?= base_url() ?>admin/vouchers/invoice/13?p=1" class="list-group-item list-group-item-action">Receipts</a>
                  <a href="<?= base_url() ?>admin/vouchers/invoice/1?p=1" class="list-group-item list-group-item-action">Contra</a>
                  <a href="<?= base_url() ?>admin/vouchers/invoice/5?p=1" class="list-group-item list-group-item-action">Journal</a>
                  <a href="<?= base_url() ?>admin/memorandum/invoice?p=1" class="list-group-item list-group-item-action">Memorandum</a>
                </div>
            </div>
            <div class="col-md-6">
                <h4>Items-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>admin/stock_transfer/mc?p=1" class="list-group-item list-group-item-action">Stock Transfer</a>
                  <a href="<?= base_url() ?>admin/physical_verification/add?p=1" class="list-group-item list-group-item-action">Physical Verification</a>
                  <a href="<?= base_url() ?>admin/production/add?p=1" class="list-group-item list-group-item-action">Production Voucher</a>
                  <a href="<?= base_url() ?>admin/stock_journal/invoice/19?p=1" class="list-group-item list-group-item-action">Stock Journal</a>
                  <a href="<?= base_url() ?>admin/consignment_packing/?p=1" class="list-group-item list-group-item-action">Consignment Packing</a>
                </div>
            </div>
            
        </div>

      </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts');
//$from_date = date("Y-m-01", strtotime("first day of last month"));
?>
<script>
var page_view = '<?php echo $view;?>';

$(document).on("change","#view",function(){
    
    	if($(this).val()=="acc_ledger"){
    	    window.location.href="<?php echo base_url();?>admin/accounts/ledger_detail/<?php echo $account_id;?>?from_date=<?php echo $from_date;?>&to_date<?php echo $to_date;?>";
    	    
    	    
    	}else{
    

 $("#salefrm2").submit();
    	}
	
});





    $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#voucherCreationModal').modal('show');
        }
    });

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
                month: "Total",
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
		
		if(page_view==1){
		 var colModel_title ="MONTH (<?= fy_calender()->name ?>)";	
		}
		else if(page_view==2){
			var colModel_title ="QUATERLY (<?= fy_calender()->name ?>)";	
		}
		else if(page_view==3){
			var colModel_title ="HALF YEARLY (<?= fy_calender()->name ?>)";	
		}
		else
		 var colModel_title ="MONTH (<?= fy_calender()->name ?>)";	
	 
        var colModel = [
            { title: colModel_title, dataIndx: "month", render: filterRender,render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
            { title: "DEBIT(RS.)",  align: "right",dataIndx: "debit"},
            { title: "CREDIT(RS.)",  align: "right", dataIndx: "credit"},
            { title: "BALANCE(RS.)", align: "right", dataIndx: "balance"},
            { title: "",  dataType: "string", dataIndx: "balance_type",}
		   
	 	    ];
		
        var dataModel = {"data":<?php echo json_encode($summary);?>};        
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
                           // var opts = [{ '': '[ All Fields ]'}];
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
			  
		       var from_dates         =  rowData.from_date;
               var to_dates         =   rowData.to_date;
		       var account_id   = rowData.account_id;
		       set_page();
		       window.location.href= '<?php echo base_url();?>admin/accounts/ledger_detail/'+account_id+"?from_date="+from_dates+"&to_date="+to_dates;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	             var rowData     = ui.rowData;
		          var from_dates         =  rowData.from_date;
               var to_dates         =   rowData.to_date;
		         var account_id   = rowData.account_id;
		         if(evt.keyCode==13){
		             set_page();
	               window.location.href= '<?php echo base_url();?>admin/accounts/ledger_detail/'+account_id+"?from_date="+from_dates+"&to_date="+to_dates;
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
	
	
	function print_excel(){	
			
           var stringparameters = "acc_id=<?php echo $account_id;?>&view=<?php echo $view;?>";
             window.location.href= baseurl+"admin/export/account_summary?"+stringparameters;
			
        }

		  </script>	</body>
		  </html>