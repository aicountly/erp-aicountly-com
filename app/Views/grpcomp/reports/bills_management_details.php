<?php $header = array( 	'title' => 'Bill Wise Statement' ); ?>
<?php echo view('includes/header',$header); ?>

<?php
$local_session      = \Config\Services::session();
$fy_begndt = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end    = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
 

?>

<style>
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
    
    .pq-grid-cell.pq-side-icon > div:before {        
        width: 20px;
        height: 20px;
        color: #ccc;
        float: right;
    }
    
    .pq-grid-cell.pq-drop-icon > div:before {
        content: "▼";
    }
    
    .pq-grid-cell.pq-calendar > div:before {
        content: "\01F4C5";
    }
    .pq-select-search-input {
        padding: 1px 2px;
        border-width: 0;
        height:23px;
    }
    .pq-select-search-input {
        box-sizing: border-box;
        width: 100%;
        font-size: inherit;
    }
    
    .ui-datepicker-calendar tr, .ui-datepicker-calendar td, .ui-datepicker-calendar td a, .ui-datepicker-calendar th{
        font-size:inherit;
    }
    div.ui-datepicker{
        font-size:13px;
        width:inherit;
        height:inherit;
    }
    .ui-datepicker-title span{
        font-size:13px;
    }

</style>
<style>
/*for autocomplete inside bills grid*/
.ui-autocomplete {
    z-index:9999!important;
}
</style>

<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">Bill Wise Statement - Bill Details</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
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
       
    </div>
    </div>
</div>
<div class="row mb-2 align-items-top">
    <div class="col-lg-5">
        <form class="form needs-validation" method="get" id="salefrm" autocomplete="off" novalidate>
        <div class="input-group">
            <span class="input-group-text px-1">From</span>
            <input type="text" name="from_date" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
            
            <span class="input-group-text px-1">To</span>
            <input type="text" name="to_date" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
            
            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
             <span class="material-symbols-outlined">event</span>
            </button>
            
            <input type="submit" class="btn btn-sm btn-success" value="GO">
        </div>
        </form>
    </div>
    <div class="col-lg-7 text-end">
        
        
        <div class="dropdown float-end">
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item ontrashmode" href="javascript:void(0);">Trash Mode</a></li>
               
            </ul>
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
             <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
        </div>
    </div>
</div>
<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
  
            <div class="col-12 calccard card m-auto">
                <form class="form" method="get" id="salefrm2" autocomplete="off">
                <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime(session()->get('ses_company_fy_beginning'))); ?>
        <div class="calc col-md-6">
            <button type="button" data-month="4" class="btn btn-light month_btn">APR</button>
            <button type="button" data-month="7" class="btn btn-light month_btn">JUL</button>
            <button type="button" data-month="10" class="btn btn-light month_btn">OCT</button>
            <button type="button" data-month="1" class="btn btn-light month_btn">JAN</button>
            <button type="button" data-month="5" class="btn btn-light month_btn">MAY</button>
            <button type="button" data-month="8" class="btn btn-light month_btn">AUG</button>   
            <button type="button" data-month="11" class="btn btn-light month_btn">NOV</button> 
            <button type="button" data-month="2" class="btn btn-light month_btn">FEB</button>
            <button type="button" data-month="6" class="btn btn-light month_btn">JUN</button>
            <button type="button" data-month="9" class="btn btn-light month_btn">SEP</button>
            <button type="button" data-month="12" class="btn btn-light month_btn">DEC</button>
            <button type="button" data-month="3" class="btn btn-light month_btn">MAR</button>
            <button type="button" data-quater="1" class="btn btn-qlight quater_btn">Q1</button>
            <button type="button" data-quater="2" class="btn btn-qlight quater_btn">Q2</button>
            <button type="button" data-quater="3" class="btn btn-qlight quater_btn">Q3</button>
            <button type="button" data-quater="4" class="btn btn-qlight quater_btn">Q4</button>
            <button type="button" data-hyear="1" class="btn btn-hlight hyear_btn">H1</button>
            <button type="button" data-hyear="2" class="btn btn-hlight hyear_btn">H2</button>
            
            <span class="fw-bold d-inline-block px-4"><input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
        </div>
        <div class="col-md-6">
            <div class="input-group mb-3">
                <button type="button" class="input-group-text" id="prev_year"><span class="material-symbols-outlined">arrow_back_ios</span></button>
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:70%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
                <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">From</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control" fdprocessedid="cw7ydk" name="from_date" id="fromdate" value="<?= $from_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">To</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control"  fdprocessedid="b20o4z" name="to_date" id="todate" value="<?= $to_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            <p class="text-end"><button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button></p>
        
        </div> 
        
        <p class="text-center pt-4">
            <button type="submit" class="btn btn-lg btn-success">GO</button>
            <button type="button" class="btn btn-lg btn-secondary" data-bs-dismiss="modal">Quit</button>
        </p>  
        
    </div>
                 </form>
                 
            </div> 
        </div>
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
        <button type="button" class="btn btn-success">Save changes</button>
      </div>
    </div>
  </div>
</div>
 
<div class="row px-1"><b>Bill: <em><?= $bills_ref_name ?> (<?= $account_name ?>)</em></b></div>

<div id="grid_search" style="margin:auto;"> </div> 


<!-- The Modal -->
<div class="modal" id="billsModal" style="z-index: 9999">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><span id="bills_page"></span> Bill by Bill</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 text-center"><h4>Account: <span id="bills_account"></span></h4></div>
            <div class="col-md-6 text-center"><h4>Total: <span id="bills_total"></span>&nbsp;<span id="bills_drcr"></span></h4></div>
        </div>
        <div id="bill_by_bill_grid"></div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save_bill_by_bill">Save</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button> 
      </div>

    </div>
  </div>
</div> 

<?php echo view('includes/footer_scripts'); ?>

<script>

 $(function () {

    function calculateSummary(grid) {
        var data = grid.option('dataModel.data');
        var debit_total = 0;
        var credit_total = 0;
 
        data.forEach(function(row){
            debit_total += parseAmount(row.debit_total);
            credit_total += parseAmount(row.credit_total);
        })

        var totalData = {
            ref_no      : "Total",
            debit       : formatAmount(debit_total),
            credit      : formatAmount(credit_total),
            
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        
        grid.option('summaryData', [totalData]);
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
            { title: "VOUCHER TYPE", align:"left", width: 180,   dataIndx: "voucher_type" },
            { title: "VOUCHER DATE", align:"left", width: 180,   dataIndx: "voucher_date" },
            { title: "DEBIT", align:"right", width: 180,   dataIndx: "debit" },
            { title: "CREDIT", align:"right", width: 180,   dataIndx: "credit" },
            { title: "BALANCE &nbsp;", align:"right", width: 180,   dataIndx: "balance"},
	    ];
	    	
     var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'bills_ref_id':'<?php echo $bills_ref_id;?>'},
            url: "<?php echo base_url();?>/admin/reports/ajax_bills_management_details",
                getData: function (dataJSON) {
                    var data = dataJSON.data;
                    return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
                }
           };
     var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            colModel : colModel,
            // dataReady : calculateSummary,
            editable: false,
            numberCell: { show: false },
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },
            dataReady:function(event,ui) {

                calculateSummary(this);
                var grid = this;

                const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);
                    grid.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                }
                else{
                    grid.setSelection({ rowIndx: grid.rowIndxOffset, focus: true });
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
        $("#grid_search").pqGrid('saveState');
        set_page();

        var rowData             = ui.rowData;
        var col_type            = rowData.vch_type;
        var ajax                = rowData.ajax;
        var voucher_txn_id      = rowData.voucher_txn_id;
        var voucher_type_id     = rowData.voucher_type_id;
        var bom_id            = rowData.bom_id;
        var bom_batches       = rowData.bom_batches;

        if(voucher_type_id=='-1')
            readyBills();
        else if(voucher_type_id=='0')
                window.location.href= baseurl+'/admin/reports/bills_management_details/<?= $bills_ref_id ?>/<?= $bill_type ?>?from_date=&to_date=<?= date('d-m-Y', strtotime('-1 day', strtotime($from_date))) ?>';
        else if(voucher_type_id=='18')
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
    
    newObj.cellKeyDown = function(evt, ui) {

        var rowData     = ui.rowData;
        var ajax   = rowData.ajax;
        var col_type = rowData.col_type;
        var voucher_txn_id   = rowData.voucher_txn_id;
        var voucher_type_id   = rowData.voucher_type_id;
        var bom_id            = rowData.bom_id;
        var bom_batches       = rowData.bom_batches;
        
        if (evt.keyCode==13){

            $("#grid_search").pqGrid('saveState');
            set_page();

            if(voucher_type_id=='-1')
                readyBills();
            else if(voucher_type_id=='0')
                window.location.href= baseurl+'/admin/reports/bills_management_details/<?= $bills_ref_id ?>/<?= $bill_type ?>?from_date=&to_date=<?= date('d-m-Y', strtotime('-1 day', strtotime($from_date))) ?>';
             else if(voucher_type_id=='18')
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
    $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#grid_search").pqGrid('saveState');
       });

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

    var op_balance = 0;
    var op_drcr = 'D';
    var bills_ref_list = [];
    account_id = <?= $acc_id ?>;

    function readyBills()
    {
        show_loader();

        $.ajax({
            type: "POST",
            url: "<?php echo $base_url; ?>/ajax/getAccountBills",
            data: {account_id: account_id},
            datatype: "json",
            success: function(response){
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if(response.status)
                {

                    stop_loader();
                    
                    op_balance = response.data.acc_op_bal;
                    op_drcr = response.data.acc_op_bal_drcr;
                    bills_ref_list = response.data.grid;
                    
                    $('#bills_account').text(response.data.acc_name);
                    $('#bills_total').html(formatAmount(response.data.acc_op_bal));
                    $('#bills_drcr').text(response.data.acc_op_bal_drcr + 'r');
                    

                    $('#billsModal').modal('show');
                    
                    $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data', generateBillData());
                    $("#bill_by_bill_grid").pqGrid('refreshDataAndView');

                    $('input[type="command-line"]').focus();//tempararily shift focus
                    $("#bill_by_bill_grid").pqGrid('setSelection', { rowIndx: 0,colIndx: 0, focus: true });
                }
            }
        });          
    }

    function generateBillData()
    {
        var json = [];
    
        $.each(bills_ref_list, function(index, obj){
            if(obj.bills_ref_name == 'UNDEFINED' || obj.bill_op_bal > 0)
            json.push(structuredClone(obj));
        });
     
        
        for(var i=0;i<25;i++){
            json.push({'bills_ref_id': '', 'bills_ref_name': '', 'bill_op_bal': '', 'bill_op_drcr': '', 'bill_due_date': ''});
        }
        return json;
    }

    function calculateBillSummary() {
        var total = 0,
            sub = 0,
            data = this.option('dataModel.data');
        
        data.forEach(function(row){
            
            if(row.bill_op_bal != '' && row.bill_op_drcr != '')
            {
                sub = 0;
                if(row.bill_op_drcr == 'D'){
                    sub = parseAmount(row.bill_op_bal);
                }
                if(row.bill_op_drcr == 'C'){
                    sub = -parseAmount(row.bill_op_bal);
                }
                total  += sub;
            }

        })
      
        var drcr = '';
        if(total > 0){
            drcr = 'D';
        }
        if(total < 0){
            drcr = 'C';
            total = Math.abs(total);
        }
        
        
        var totalData = {
            bills_ref_name : 'Total',
            bill_op_bal : total,
            bill_op_drcr : drcr,
            pq_rowcls : 'grid_footer_color',
            summaryRow: true
        }
        this.option('summaryData', [totalData]);
    }

    function dateEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,                
            grid = this;

        $inp.on("focusout", function (e) {
            var date = rd.due_date;
            
            if(!isValidDate(date)){
              rd.due_date = '';
              e.preventDefault();
            }
        });
        
        $inp.inputmask("99/99/9999", {
            mask: "99-99-9999",
            alias: "date",
            placeholder: "dd-mm-yyyy",
            insertMode: false,
        }).datepicker({
            altFormat: "dd-mm-yyyy",
            dateFormat: "dd-mm-yy",
            changeMonth: true,
            changeYear: true,
            minDate:'<?php echo $fy_begndt;?>',
            maxDate:'<?php echo $fy_end;?>',
            onClose: function () {
                this.focus();
            }

        });
    };

    function referenceEditor(ui) {
        var $inp = ui.$cell.find("input"),
            di = ui.dataIndx,
            rd = ui.rowData,
            grid = this;

        var data = this.option('dataModel.data');
        var grid_ref_ids = data.map(function(obj) { return obj.bills_ref_id; });

        var modified_ref_list = bills_ref_list.filter(function (el) {
          return !grid_ref_ids.includes(el.bills_ref_id) || rd.bills_ref_id == el.bills_ref_id;
        });
     

        $inp.autocomplete({
            source:  modified_ref_list,
            selectItem: { on: true }, //custom option
            highlightText: { on: true }, //custom option
            minLength: 0,
            select: function(event, ui) {
                rd.bills_ref_id = ui.item.bills_ref_id;
                rd.bills_ref_name = ui.item.bills_ref_name;
                rd.bill_due_date = ui.item.bill_due_date;

                var expected = expected_bills_amount(grid);
                rd.bill_op_bal = expected.amount;
                rd.bill_op_drcr = expected.drcr;
            }

        }).focus(function () {              
            $(this).autocomplete("search", "");
            rd.bills_ref_id = '';
            rd.bills_ref_name = '';
            rd.bill_due_date = '';
            rd.bill_op_bal = '';
            rd.bill_op_drcr = '';
        }).focusout(function () {              
            if(rd.bills_ref_name != '' && rd.bills_ref_id == '')
            {
                var index = modified_ref_list.findIndex(function(obj) {
                   return obj.bills_ref_name.toLowerCase() == rd.bills_ref_name.toLowerCase();
                });
                if(index > -1){
                    
                    var downKeyEvent = $.Event("keydown");
                    downKeyEvent.keyCode = $.ui.keyCode.DOWN;  // event for pressing "down" key
                
                    var enterKeyEvent = $.Event("keydown");
                    enterKeyEvent.keyCode = $.ui.keyCode.ENTER;  // event for pressing "enter" key

                    $inp.val(modified_ref_list.bills_ref_name); 
                    $inp.trigger(downKeyEvent); 
                    $inp.trigger(downKeyEvent);
                    $inp.trigger(enterKeyEvent);
                }
                else{
                    rd.bills_ref_id = '';
                    rd.bills_ref_name = '';
                    rd.bill_due_date = '';
                }
            }
        });

        $inp.on("change", function (evt) {
            grid.refreshDataAndView();
        });
    }

    function expected_bills_amount(grid)
    {
        var amount = 0;
        var data = grid.option('dataModel.data');

        var master_amount = op_balance;
        var master_drcr = op_drcr;

        if(master_drcr == 'D')
            amount = master_amount;
        if(master_drcr == 'C')
            amount = -master_amount;

        data.forEach(function(row){
            if(row.bill_op_bal != '' && row.bill_op_drcr != ''){
                if(row.bill_op_drcr == 'C')
                    amount  += parseAmount(row.bill_op_bal);
                if(row.bill_op_drcr == 'D')
                    amount  -= parseAmount(row.bill_op_bal);
            }
        });

        if(amount < 0){
            return {amount: Math.abs(amount), drcr: 'C'};
        }
        if(amount > 0){
            return {amount: amount, drcr: 'D'};
        }

        return {amount: '', drcr: ''};
    }

    var drcrlist     = [{"":""},{"C":"C"},{"D":"D"}];

    var bill_dataModel = {"data": []} 
    var bill_colModel = [
       
        { title: "REFERENCE", width: 100, dataIndx: "bills_ref_name" ,cls: 'pq-drop-icon pq-side-icon',
            editor: {                   
              type: "textbox",
              init: referenceEditor
            },
        },

        { title: "AMOUNT", width: 100,  dataIndx: "bill_op_bal" ,dataType: "float",
            render: function( ui ) {
                var rd = ui.rowData;
                if(rd.bill_op_bal != ''){
                    rd.bill_op_bal = parseAmount(rd.bill_op_bal);
                    return formatAmount(rd.bill_op_bal);   
                }
                return '';
            },
        },
        { title: "Dr/Cr", dataIndx: "bill_op_drcr", width: 100, cls: 'pq-drop-icon pq-side-icon',
            editor: {
                type: 'select',
                options: drcrlist
            },
        },
        { title: "DUE DATE", dataIndx: "bill_due_date", width: 100 ,dataType: 'string',
            editor: {
                type: 'textbox',
                init: dateEditor
            },
        },
    ];

    var billsObj = {
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 99999 } }, //disable maximize,toggle button.
        height: 'flex',
        selectionModel: { type: 'cell' }, 
        scrollModel: { autoFit: true },
        dataModel: bill_dataModel,
        colModel: bill_colModel,  
        pageModel: { type: 'local', rPP: 5 },
        numberCell: { show: true },
        change: calculateBillSummary,
        dataReady: calculateBillSummary,
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
                
                

    $("#billsModal").on('shown.bs.modal', function () {   
        if($("#bill_by_bill_grid").pqGrid('instance')){    
            $("#bill_by_bill_grid").pqGrid('refresh');
        }
        else{
            $("#bill_by_bill_grid").pqGrid(billsObj);
        }
    });

    $("#save_bill_by_bill").on("click",function(){
        var status = true;
        status = validateBills();
        if(!status){
            return;
        }
       
        if(status){
            var bills_json_array = responseBills();
            if(bills_json_array.length > 0)
            {
                show_loader();
                $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url; ?>/ajax/setAccountBills",
                    data: {bills_json_array: bills_json_array},
                    datatype: "json",
                    success: function(response){
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if(response.status)
                        {
                            stop_loader();
                            $("#grid_search").pqGrid('refreshDataAndView');
                            $('#billsModal').modal('hide');
                            alert_success(response.message);
                        }
                    }
                });
            }
        }
    });

    function validateBills()
    {
        var sum = 0;
        var data = $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data');

        
        for (var i = 0; i < data.length; i++) {
            var bills_ref_id     = data[i]['bills_ref_id'];
            var bill_op_bal  = data[i]['bill_op_bal'];
            var bill_op_drcr  = data[i]['bill_op_drcr'];
            
            
            if(bills_ref_id!='')
            {
                
                var sub = 0;
                if(bill_op_drcr == 'D'){
                    sub = bill_op_bal;
                }
                if(bill_op_drcr == 'C'){
                    sub = -bill_op_bal;
                }
                sum += parseAmount(sub);
                  
            }
        }

        var final_amount = op_balance;
        var final_drcr = op_drcr;
        
        if(final_drcr == 'C'){
            final_amount = -final_amount;
        }
    
        if(parseAmount(sum) != parseAmount(final_amount)){
            alert('Total Mismatch');
            return false;
        }
        
        return true;
    }

    function responseBills()
    {
        var sum = 0;
        var data = $("#bill_by_bill_grid").pqGrid('option', 'dataModel.data');

        var bills_json_array = [];

        for (var i = 0; i < data.length; i++) {
            var bills_ref_id     = data[i]['bills_ref_id'];
            var bill_op_bal  = data[i]['bill_op_bal'];
            var bill_op_drcr  = data[i]['bill_op_drcr'];
            var bill_due_date  = data[i]['bill_due_date'];
            
            
            if(bills_ref_id!='')
            {
                if(bill_op_drcr == 'C')
                    bill_op_bal = -parseAmount(bill_op_bal);
                else
                    bill_op_bal = parseAmount(bill_op_bal);

                bills_json_array.push({
                    'bills_ref_id' : bills_ref_id,
                    'bill_op_bal' : bill_op_bal,
                    'bill_due_date' : bill_due_date,
                });
                  
            }
        }
        
        return bills_json_array;
    }
      
});
  
</script>
<style>
.hidden{display:none;}
</style>
 </body>
</html>
