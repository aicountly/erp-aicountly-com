<?php $header = array(  'title' => 'Item Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.myform .col-12{padding:6px 0px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}
</style>
<div class="row pb-2 align-items-center">
    <div class="col-sm-6"><h3>Item Ledger</h3></div>
    <div class="col-sm-6 text-end"><div class="taskmenus">
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a></li>
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

<div class="row mb-4 align-items-top">
<div class="col-md-5">
<form class="form needs-validation" method="post" action="<?php echo $base_url; ?>reports/stock_ledger" id="salefrm"  novalidate>
    <input type="hidden" name="item_id"  value="<?php echo $item_id ?>">
    <input type="hidden" name="unit_id"  value="<?php echo $unit_id; ?>">
    <input type="hidden" name="module" value="Item Account">
    <div class="input-group">
        <span class="input-group-text px-1">From</span>
        <input type="text" name="fromdate" value="<?= $from_date ?>" class="datepicker form-control p-2" required  style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input type="text" name="todate" value="<?= $to_date ?>" class="datepicker form-control p-2" required style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        
        <input type="submit" class="btn btn-sm btn-success" value="GO">
    </div>
</form>
</div>
<div class="col-md-7">
<div class="dropdown float-end">
    
    
    
    <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    </button>
    
    
    <ul class="dropdown-menu">
        
        <li><a class="dropdown-item" href="<?php echo base_url();?>/admin/items/ledger_detail?itmid=<?php echo $item_id;?>&frmdt=<?php echo $from_date;?>&todt=<?php echo $to_date;?>&untid=0&mcgrpid=0&mcid=0">All Units </a></li>

        <?php if($item_unit_list){ ?>
        <?php foreach($item_unit_list as $key =>$value){ ?>

        <li>
            <a class="dropdown-item" href="<?php echo base_url();?>/admin/items/ledger_detail?itmid=<?php echo $item_id;?>&frmdt=<?php echo $from_date;?>&todt=<?php echo $to_date;?>&untid=<?php echo $value['unit_id'];?>&mcgrpid=<?php echo $mc_grp_id;?>&mcid=<?php echo $mc_id;?>">
                <?= $value['unit_name'] ?>    
            </a>
        </li>
        
        <?php }  ?>
        <?php }  ?>
        
    </ul>
    
    
    <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">View</button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Stock Query </a></li>
        <li><a class="dropdown-item" href="#">Daily breakup</a></li>
        <li><a class="dropdown-item" href="#">Monthly Summary</a></li>
        <li><a class="dropdown-item" href="#">Movement Analysis</a></li>
    </ul>
    <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add On</button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Vouch No</a></li>
        <li><a class="dropdown-item" href="#">Optional Voucher </a></li>
        <li><a class="dropdown-item" href="#">Cancelled Voucher</a></li>
        <li><a class="dropdown-item" href="#">Post dated Voucher </a></li>
        <li><a class="dropdown-item" href="#">Narration</a></li>
        <li><a class="dropdown-item" href="#">Quantity</a></li>
        <li><a class="dropdown-item" href="#">Rate</a></li>
        <li><a class="dropdown-item" href="#">Value</a></li>
        <li><a class="dropdown-item" href="#">Gross value for outward</a></li>
        <li><a class="dropdown-item" href="#">Gross Value For Inward</a></li>
        <li><a class="dropdown-item" href="#">Gross Profit</a></li>
    </ul>
    <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success ms-2">« Back</a>

    </div>
    <div class="dropdown float-end">

    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="InwardAmountCheck">
        <label class="form-check-label" for="InwardAmountCheck">Inward Amount</label>
    </div>
    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="OutwardAmountCheck">
        <label class="form-check-label" for="OutwardAmountCheck">Outward Amount</label>
    </div>
    <div class="form-check d-inline-block me-2">
        <input class="form-check-input" type="checkbox" value="1" id="BalanceCheck">
        <label class="form-check-label" for="BalanceCheck">Balance</label>
    </div>

    <div class="d-inline-block me-2" style="width:220px;">
        <div class="input-group">
            <span class="input-group-text">Valuation Method</span>
            <select name="valuation_type" id="valuation_type" class="form-select">
                <option value="1" <?php echo ($val_id==1)?"selected":"";?>>AVG</option>
                <option value="2" <?php echo ($val_id==2)?"selected":"";?>>FIFO</option>
                <option value="3" <?php echo ($val_id==3)?"selected":"";?>>LIFO</option>
            </select>
        </div>
    </div>
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<p><em>Item: <strong><?php echo $item_name;?></strong></em></p>
</div>
<div class="col-md-6">
<?php
$date_filter = date("Y-m",strtotime($from_date));

?>
<div class="text-end pe-3">
    
    <?php if(count($opening_balance_list) == 0){ ?>

        <p><em>Opening: <strong><?php echo $opening_balance;?></strong></em></p>

    <?php } else{ ?>

      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#openingBalanceModel">
          Opening Balance
      </button>

    <?php } ?>
    
</div>
</div>
</div>


<div id="grid_search" style="margin:auto;"> </div>
<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    
    <div class="col-12 calccard card m-auto">
        <form class="form" method="post" id="salefrm2" autocomplete="off" action="<?php echo $base_url; ?>reports/stock_ledger">
            <input type="hidden" name="item_id"  value="<?php echo $item_id ?>">
            <input type="hidden" name="unit_id"  value="<?php echo $unit_id; ?>">
            <input type="hidden" name="module" value="Item Account">
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
                                <input type="text" class="form-control" fdprocessedid="cw7ydk" name="fromdate" id="fromdate" value="<?php echo date('d-m-Y', strtotime($from_date));?>" placeholder="dd-mm-yyyy" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row align-items-center my-2">
                        <div class="col-md-2 fw-bold pe-0">To</div>
                        <div class="col-md-10">
                            <div class="calc-inputgroup">
                                <input type="text" class="form-control"  fdprocessedid="b20o4z" name="todate" id="todate" value="<?php echo date('d-m-Y', strtotime($to_date));?>" placeholder="dd-mm-yyyy" required>
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



<!-- The Modal -->
<div class="modal fade" id="openingBalanceModel">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Opening Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">

                <thead>
                    <tr>
                        <th style="text-align: center;">Material Center</th>
                        <th style="text-align: center;">Unit</th>
                        <th style="text-align: center;">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($opening_balance_list){ ?>
                    <?php foreach($opening_balance_list as $key =>$value){ ?>

                        <tr>
                            <td><?= $value['mc_name'] ?></td>
                            <td><?= $value['unit_name'] ?></td>
                            <td style="text-align: right;"><?= $value['balance'] ?></td>
                        </tr>
                    
                    <?php }  ?>
                    <?php }  ?>

                    
                </tbody>
            </table>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>

<script>

$("#valuation_type").on("change",function(){
    
    
var url = new window.URL(document.location); 
url.searchParams.set("valuation_type", $(this).val());
    
    
    window.location.href=url;
});




     $(function () {
         
         function calculateSummary() { 
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
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
           
           if(typeof row.balance == 'undefined'){
                var balance =0;
            }else
             balance =  row.balance;
             
            
            debitTotal +=  parseFloat(debit);
            creditTotal +=  parseFloat(credit);
             balanceTotal += parseFloat(balance);
           
        })

        var totalData = {
                col_date: "Total",
                col_type :"",
                col_vchno:"",
                debit: debitTotal,
                credit: creditTotal,
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
       


        var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'item_id':'<?php echo $item_id;?>', 'unit_id':'<?php echo $unit_id;?>', 'mc_id':'<?php echo $mc_id;?>', 'mc_grp_id':'<?php echo $mc_grp_id;?>', 'val_id':'<?php echo $val_id;?>'},
            url: "<?php echo base_url();?>/admin/items/ajax_item_ledger",
             getData: function (dataJSON) {
                var data = dataJSON.data;
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
        };

        var colModel = [
             { title: "DATE", dataIndx: "voucher_date", width: 90, hidden:false },
             { title: "PARTICULARS", width: 200, dataIndx: "particulars",hidden:false},
             { title: "VCH No.", width: 100, dataIndx: "voucher_no",hidden:false},   
             { title: "VCH TYPE", width: 100, dataIndx: "voucher_type",hidden:false},
             

             { title: "INWARD", width: 100, dataType: "float",dataIndx: "inward_qty",hidden:false},
             { title: "INWARD AMOUNT", width: 100, align: "right", dataIndx: "inward_amount",hidden:true},

             { title: "OUTWARD", width: 100, dataType: "float", dataIndx: "outward_qty",hidden:false},
             { title: "OUTWARD AMOUNT", width: 100, align: "right",  dataIndx: "outward_amount",hidden:true},

             
             { title: "CLOSING", width: 100, dataType: "float", dataIndx: "closing_qty",hidden:false},
             { title: "UOM", width: 80, dataIndx: "unit_name",hidden: false},
             { title: "BALANCE", width: 100, dataType: "float",dataIndx: "valuation",hidden:true},
            ];
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            /* dataReady: calculateSummary, */
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
                var rowData           = ui.rowData;
                var col_type          = rowData.vch_type;
                var ajax              = rowData.ajax;
                var voucher_txn_id    = rowData.voucher_txn_id;
                var voucher_type_id   = rowData.voucher_type_id;
                 var vch_subtype_id   = rowData.vch_subtype_id;
                var bom_id            = rowData.bom_id;
                var bom_batches       = rowData.bom_batches;
                
                if(voucher_type_id=='18')
                     window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='11')
                     window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id;  
                    else if(voucher_type_id=='10')
                     window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+"/"+voucher_type_id;
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
                    else if(voucher_type_id=='16')
                     window.location.href= baseurl+'/admin/vouchers/reverse_journal/'+voucher_txn_id;
                    else if(voucher_type_id=='17')
                     window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='19')
                     window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='21')
                     window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='20')
                     window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;
                    
                    else  if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='8')
                     window.location.href= baseurl+'/admin/memorandum/edit/'+voucher_txn_id;
     
         }
         
        newObj.cellKeyDown= function(evt, ui) {
               var rowData     = ui.rowData;
            
            
                var col_type          = rowData.vch_type;
                var ajax              = rowData.ajax;
                var voucher_txn_id    = rowData.voucher_txn_id;
                var voucher_type_id   = rowData.voucher_type_id;
                  var vch_subtype_id   = rowData.vch_subtype_id;
                  
                var bom_id            = rowData.bom_id;
                var bom_batches       = rowData.bom_batches;
               if (evt.keyCode==13){
                   
                if(voucher_type_id=='18')
                     window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='11')
                     window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id;  
                    else if(voucher_type_id=='10')
                     window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+"/"+voucher_type_id;
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
                    else if(voucher_type_id=='16')
                     window.location.href= baseurl+'/admin/vouchers/reverse_journal/'+voucher_txn_id;
                    else if(voucher_type_id=='17')
                     window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='19')
                     window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='21')
                     window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='20')
                     window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;
                    
                    else  if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;
                    else if(voucher_type_id=='8')
                     window.location.href= baseurl+'/admin/memorandum/edit/'+voucher_txn_id;
        
               }
           
           }
        
        var grid = $("#grid_search").pqGrid(newObj);
     
        $("#InwardAmountCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){


              colModel[5].hidden=false;
              colModel[5].width= 200;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
              
            }
          else{
              colModel[5].hidden=true;
              colModel[5].width= 100;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
        });
        
        $("#OutwardAmountCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){
              colModel[7].hidden=false;
              colModel[7].width= 200;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
              $("#grid_search").pqGrid("refresh");
            }
          else{
              colModel[7].hidden=true;
              colModel[7].width= 100;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
        });
        
        $("#BalanceCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){
              colModel[10].hidden=false;
              colModel[10].width= 200;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
              $("#grid_search").pqGrid("refresh");
            }
          else{
              colModel[10].hidden=true;
              colModel[10].width= 100;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
        });
    
       
    
    });
function getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return '';
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

function print_excel()
        {
            var itmid      =  getParameterByName('itmid'); 
            var frmdt      =  getParameterByName('frmdt'); 
            var todt       =  getParameterByName('todt');  
            var untid      =  getParameterByName('untid');  
            var mcgrpid    =  getParameterByName('mcgrpid'); 
             var mcid      =  getParameterByName('mcid');
             var valuation_type =  getParameterByName('valuation_type') != '' ? getParameterByName('valuation_type') : 1;           
            var stringparameters = "exprt_type=excel&itmid="+itmid+"&frmdt="+frmdt+"&todt="+todt+"&untid="+untid+"&mcgrpid="+mcgrpid+"&mcid="+mcid+"&valuation_type="+valuation_type;
            
            window.location.href= baseurl+"/admin/export/item_ledger?"+stringparameters;
           
        }
function print_csv()
        {
            var itmid      =  getParameterByName('itmid'); 
            var frmdt      =  getParameterByName('frmdt'); 
            var todt       =  getParameterByName('todt');  
            var untid      =  getParameterByName('untid');  
            var mcgrpid    =  getParameterByName('mcgrpid'); 
             var mcid      =  getParameterByName('mcid');
             var valuation_type =  getParameterByName('valuation_type') != '' ? getParameterByName('valuation_type') : 1;           
            var stringparameters = "exprt_type=csv&itmid="+itmid+"&frmdt="+frmdt+"&todt="+todt+"&untid="+untid+"&mcgrpid="+mcgrpid+"&mcid="+mcid+"&valuation_type="+valuation_type;
            
            window.location.href= baseurl+"/admin/export/item_ledger?"+stringparameters;
           
        }       
          </script>
</body></html>