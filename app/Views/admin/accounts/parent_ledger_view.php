<?php $header = array(  'title' => 'Account/Group Parent Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.myform .col-12{padding:6px 0px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}
</style>
<style>
/*for autocomplete */
.ui-autocomplete {
z-index:9999!important;
}
</style>
<div class="row mb-md-0 mb-3">
  <div class="col-md-6">
    <h3 class="pb-3">Account/Group Parent Ledgers</h3>
  </div>
  <div class="col-md-6 text-end">
    <div class="taskmenus">
      <a href="javascript:void(0)" id="refresh_grid">
        <span class="material-symbols-outlined">refresh</span>
      </a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span>
      </a>
      <a href="#">
        <span class="material-symbols-outlined open-comingsoon">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined ">download</span>
        </span>
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
        <span class="material-symbols-outlined">share</span>
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
      
    </div>
  </div>
</div>
<div class="row mb-2 align-items-top">
  <div class="col-lg-5">
    <form class="form needs-validation" method="get" id="salefrm" autocomplete="off" novalidate>
      <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
      <div class="input-group">
        <span class="input-group-text px-1">From</span>
        <input type="text" name="from_date" id="from_date" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input type="text" name="to_date" id="to_date" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        
        <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
      </div>
    </form>
  </div>
  <div class="col-lg-7 text-end">
    <div class="dropdown float-end">
      <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item ontrashmode" href="javascript:void(0);">Trash Mode</a>
        </li>
      </ul>
      <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="#">Action</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Another action</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Something else here</a>
        </li>
      </ul>
      <a href="<?php echo history_back();?>"  class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
    </div>
  </div>
</div>
<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="col-12 calccard card m-auto">
        <form class="form" method="get" action ="" id="salefrm2" autocomplete="off">
  
          <div class="col-md-12 p-2">
            <label>Account</label>
            <input id="item" type="text" class="form-control borderdark" value="<?= $parent_name ?>" required>
            <input type="hidden" id="parent_id" name="parent_id" value="<?= $parent_id ?>">
          </div>
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
              
              <span class="fw-bold d-inline-block px-4">
                <input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
              </div>
              <div class="col-md-6">
                <div class="input-group mb-3">
                  <button type="button" class="input-group-text" id="prev_year">
                  <span class="material-symbols-outlined">arrow_back_ios</span>
                  </button>
                  <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:70%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?>
                  </button>
                  <button type="button" class="input-group-text" id="next_year">
                  <span class="material-symbols-outlined">arrow_forward_ios</span>
                  </button>
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
                <p class="text-end">
                  <button type="button" class="input-group-text fw-bold ms-auto"  id="tilldate" required>TILL DATE</button>
                </p>
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
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
      </button>
    </div>
    <div class="offcanvas-body">
      <div class="row">
        
        <div class="col-sm-6 border-end">
          <h5 class="pb-3">Horizontal</h5>
          
          <p class="offcanvaoptions">
            <i>Condensed</i>
            <label class="starcheck">
              <input type="checkbox" checked="checked">
              <b class="checkmark">★</b>
            </label>
            <label class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="swap">
            </label>
            <label class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="swap">
            </label>
          </p>
          
          <p class="offcanvaoptions">
            <i>Detailed</i>
            <label class="starcheck">
              <input type="checkbox" checked="checked">
              <b class="checkmark">★</b>
            </label>
            <label class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="swap">
            </label>
            <label class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="swap">
            </label>
          </p>
          
          <p class="offcanvaoptions">
            <i>All Labels</i>
            <label class="starcheck">
              <input type="checkbox" checked="checked">
              <b class="checkmark">★</b>
            </label>
            <label class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="swap">
            </label>
            <label class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="swap">
            </label>
          </p>
        </div>
        
        <div class="col-sm-6">
          <h5 class="pb-3">Verticle</h5>
          <p class="offcanvaoptions">
            <i>Verticle</i>
            <label class="starcheck">
              <input type="checkbox" checked="checked">
              <b class="checkmark">★</b>
            </label>
            <label class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="swap">
            </label>
            <label class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="swap">
            </label>
          </p>
          <p class="offcanvaoptions">
            <i>Schudle</i>
            <label class="starcheck">
              <input type="checkbox" checked="checked">
              <b class="checkmark">★</b>
            </label>
            <label class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="swap">
            </label>
            <label class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="swap">
            </label>
          </p>
        </div>
        
        <div class="col-sm-12 pt-3 border-top">
          <p class="offcanvaoptions">
            <i>Schedule</i>
            <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap">
          </label>
          <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap">
        </label>
      </p>
      <p class="offcanvaoptions">
        <i>Ratio</i>
        <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap">
      </label>
      <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap">
    </label>
  </p>
  
  <p class="text-center pt-3">
    <a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a>
  </p>
  
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
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
      </button>
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
<div class="row">
  <div class="col-md-6">
    <p>
    <em>Group: <strong><?php echo $parent_name;?></strong></em>
    </p>
  </div>
  <div class="col-md-6 text-end">
    
  </div>
</div>
<div id="validation_errors">
</div>
<br>
<div id="grid_search" style="margin:auto;"> </div>
<!-- The Modal -->


<div class="modal" id="voucherCreationModal">
  <div class="modal-dialog modal-lg">
  <div class="modal-content">
  <!-- Modal Header -->
  <div class="modal-header">
    <h4 class="modal-title">Create Voucher</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal">
    </button>
  </div>
  <!-- Modal body -->
  <div class="modal-body">
    
    <div class="row p-2">
      <div class="col-md-6">
        <h4>Sales-</h4>
        <div class="list-group">
          <a href="<?= base_url() ?>/admin/sales/item?p=1" class="list-group-item list-group-item-action">Sales Invoice</a>
          <a href="<?= base_url() ?>/admin/sales_order/item?p=1" class="list-group-item list-group-item-action">Sales Order</a>
          <a href="<?= base_url() ?>/admin/credit_note/item?p=1" class="list-group-item list-group-item-action">Credit Note</a>
          <a href="<?= base_url() ?>/admin/delivery_challan/add?p=1" class="list-group-item list-group-item-action">Delivery Challan</a>
          <a href="<?= base_url() ?>/admin/quotations/item?p=1" class="list-group-item list-group-item-action">Quotations</a>
        </div>
      </div>
      <div class="col-md-6">
        <h4>Purchases-</h4>
        <div class="list-group">
          <a href="<?= base_url() ?>/admin/purchase/item?p=1" class="list-group-item list-group-item-action">Purchase Invoice</a>
          <a href="<?= base_url() ?>/admin/purchase_order/item?p=1" class="list-group-item list-group-item-action">Purchase Order</a>
          <a href="<?= base_url() ?>/admin/debit_note/item?p=1" class="list-group-item list-group-item-action">Debit Note</a>
          <a href="<?= base_url() ?>/admin/inward_challan/add?p=1" class="list-group-item list-group-item-action">Inward Challan</a>
          <a href="<?= base_url() ?>/admin/purchase_requisition/with_amount?p=1" class="list-group-item list-group-item-action">Purchase Requisition</a>
        </div>
      </div>
      
    </div>
    <div class="row p-2">
      <div class="col-md-6">
        <h4>Banking-</h4>
        <div class="list-group">
          <a href="<?= base_url() ?>/admin/vouchers/invoice/9?p=1" class="list-group-item list-group-item-action">Payments</a>
          <a href="<?= base_url() ?>/admin/vouchers/invoice/13?p=1" class="list-group-item list-group-item-action">Receipts</a>
          <a href="<?= base_url() ?>/admin/vouchers/invoice/1?p=1" class="list-group-item list-group-item-action">Contra</a>
          <a href="<?= base_url() ?>/admin/vouchers/invoice/5?p=1" class="list-group-item list-group-item-action">Journal</a>
          <a href="<?= base_url() ?>/admin/memorandum/invoice?p=1" class="list-group-item list-group-item-action">Memorandum</a>
        </div>
      </div>
      <div class="col-md-6">
        <h4>Items-</h4>
        <div class="list-group">
          <a href="<?= base_url() ?>/admin/stock_transfer/mc?p=1" class="list-group-item list-group-item-action">Stock Transfer</a>
          <a href="<?= base_url() ?>/admin/physical_verification/add?p=1" class="list-group-item list-group-item-action">Physical Verification</a>
          <a href="<?= base_url() ?>/admin/production/add?p=1" class="list-group-item list-group-item-action">Production Voucher</a>
          <a href="<?= base_url() ?>/admin/stock_journal/invoice/19?p=1" class="list-group-item list-group-item-action">Stock Journal</a>
          <a href="<?= base_url() ?>/admin/consignment_packing/?p=1" class="list-group-item list-group-item-action">Consignment Packing</a>
        </div>
      </div>
      
    </div>
  </div>
  </div>
  </div>
</div>
<style>.hidden{display:none;}</style>
<?php echo view('includes/footer_scripts'); ?>

<script>
    $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#voucherCreationModal').modal('show');
        }
    });

    var accounts_list = <?= json_encode($parent_list) ?>;
    getList(accounts_list);

    function getList(list) 
    {
        $('#item').off('blur'); // unbind event first
        
        if($('#item').hasClass('ui-autocomplete-input')) {
            $('#item').autocomplete("destroy");
        }
        
        $( '#item' ).autocomplete({
            source: list,
            minLength: 0,
            select: function( event, ui ) {
                $(this).val(ui.item.label);
                $('#parent_id').val(ui.item.id);
            }
        })
        .on('focus', function(){
            $(this).autocomplete( "search", "" );
            $('#item').val('');
            $('#parent_id').val('');
        })
        .on('blur', function(){
            
            if($(this).val() != '' && $('#parent_id').val() == '')
            {
                var acc = $(this).val();
                var index = list.findIndex(function(obj) {
                    var string = obj.label.toLowerCase();
                    var text = acc.toLowerCase();
                   return  string.includes(text);
                });
                if(index > -1){
                    $(this).val(list[index].label);
                    $('#parent_id').val(list[index].id);
                    
                }
                else{
                    $('#item').val('');
                    $('#parent_id').val('');
                }
            }
            if($('#parent_id').val() == '')
            {
                $('#item').val('');
                $('#parent_id').val('');
            }
        });
    }


         
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
                voucher_date: "Total",
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
                condition = '',//$toolbar.find(".filterCondition").val(),
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

    { title: "DATE", dataIndx: "voucher_date"},
    { title: "ACCOUNT",  dataIndx: "account_name"},
    { title: "GROUP", dataIndx: "group_name"},
    { title: "TYPE",  dataIndx: "voucher_type" },
    { title: "VCH NO",  dataIndx: "voucher_no"},
    { title: "DEBIT "+ "("+ SYSTEM_CURRENCY + ")", align: "right", dataIndx: "debit"},
    { title: "CREDIT "+ "("+ SYSTEM_CURRENCY + ")",  align: "right", dataIndx: "credit"},
    { title: "BALANCE "+ "("+ SYSTEM_CURRENCY + ")",  align: "right", dataIndx: "balance"},
    { title: "",  dataType: "string", dataIndx: "balance_type"},

  ];
       
  var dataModel = {
    location : "remote",
    dataType : "json",
    method   : "POST",
    postData : {'from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'parent_id':'<?php echo $parent_id;?>'},
    url: "<?php echo base_url();?>/admin/accounts/ajax_parent_ledger",
    getData: function (dataJSON) {
      var data = dataJSON.data;
      return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
    }
  };

  var loadStateSuccess;
  var newObj = {
      scrollModel: { autoFit: true },
      collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
      height: 'flex',
      selectionModel: { type: 'row',mode:'single' },
      dataModel: dataModel,
      dataReady: calculateSummary,
      change: calculateSummary,
      colModel: colModel,  
      numberCell: { show: false },
      filterModel: { mode: 'OR', type: "remote" },
      pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
      editable: false,
      showTitle: true,
      create: function (evt, ui) {// make first row auto selected
       // loadStateSuccess = this.loadState({ refresh: false });
      
       
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
          ]
      }
  };
        
        
  newObj.rowDblClick = function(event, ui) {
       
    var rowData    = ui.rowData;

    var voucher_txn_id   = rowData.voucher_txn_id;
    var voucher_type_id   = rowData.voucher_type_id;

    $("#grid_search").pqGrid('saveState');
    set_page();

    edit_voucher(voucher_txn_id, voucher_type_id);
   }
   
  newObj.cellKeyDown= function(evt, ui) {

    var rowData     = ui.rowData;
    var voucher_txn_id   = rowData.voucher_txn_id;
    var voucher_type_id  = rowData.voucher_type_id;

    if (evt.keyCode==13){

      $("#grid_search").pqGrid('saveState');
      set_page();

      edit_voucher(voucher_txn_id, voucher_type_id);
    }

  }
        
    var $grid = $("#grid_search").pqGrid(newObj);
       pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
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
    
</script> 
</body>
</html>