<?php $header = array(  'title' => 'Trial Balance' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>


<div class="row mb-2"><div class="col-md-6">
  <h3 class="pb-0">Trial Balance</h3>
  <p><em>As the end of <?= $to_date ?></em></p>
</div>

<div class="col-md-6 text-end">
  <div class="taskmenus">
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="javascript:void(0)" onclick="print_grid();"><span class="material-symbols-outlined">print</span></a>
    <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></a>
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
  </li>
</div>
</div>


<div class="col-md-5">
  <form class="form" method="get" id="salefrm2" autocomplete="off">
   <input type="hidden" name="nil_type" id="nil_type" value="<?= $nil_type ?>">
   <div class="input-group">
    <span class="input-group-text px-1">From</span>
    <input autocomplete="off" type="text" name="from_date" id="from_date" value="<?= $from_date ?>" class="datepicker form-control" style="width:90px;">

    <span class="input-group-text px-1">To</span>
    <input autocomplete="off" type="text" name="to_date" id="to_date" value="<?= $to_date ?>" class="datepicker form-control" style="width:90px;">

    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendermodal">
     <span class="material-symbols-outlined">event</span>
   </button>
   <input type="submit" class="btn btn-sm btn-success" value="GO">
 </div>
</form>
</div>


<div class="col-md-7 text-end">

  <div class="dropdown d-inline-block me-1" style="width:220px;"> 
    <div class="input-group input-group-sm input-group">
      <span class="input-group-text">View</span>
      <select form="salefrm2" class="form-select" name="view" id="view" onchange="this.form.submit()">
        <option <?= ($view==0) ? 'selected' : '' ?> value="0">Groups</option>
        <option <?= ($view==1) ? 'selected' : '' ?> value="1">Accounts</option>
        <option <?= ($view==2) ? 'selected' : '' ?> value="2">Opening Balance</option>
      </select>

    </div>  
  </div>

  <div class="dropdown float-end">
    <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
    <ul class="dropdown-menu">
      <li><a data-nil="1" data-type="balance" class="dropdown-item addon_type nil" href="javascript:void(0)">Include Nil Balances</a></li>
      <li><a data-nil="0" data-type="balance" style="display: none;" class="dropdown-item addon_type nil" href="javascript:void(0)">Exclude Nil Balances</a></li>
      <li><a data-nil="1" data-type="transaction" class="dropdown-item addon_type nil" href="javascript:void(0)">Include Nil Transactions</a></li>
      <li><a data-nil="0" data-type="transaction" style="display: none;" class="dropdown-item addon_type nil" href="javascript:void(0)">Exclude Nil Transactions</a></li>
    </ul>
    <button class="btn btn-sm btn-success m-1" type="button">Templates</button>   
    <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success m-1">Back</a>
  </div></div>


</div>

<div class="modal fade mt-5 modal-lg" id="calendermodal" tabindex="-1" aria-labelledby="calendermodallabel" style="display: none;" aria-hidden="true">
 <div class="modal-dialog">
  <div class="modal-content">

    <div class="col-12 calccard card m-auto">
      <form class="form" method="get" id="salefrm2" autocomplete="off">
        <input type="hidden" name="view" id="view"  value="<?= $view ?>">
        <input type="hidden" name="nil_type" id="nil_type" value="<?= $nil_type ?>">

        <div class="row p-4">

          <div class="comp_calender col-md-6">
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
              <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;"></button>
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
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div id="grid_search" style="margin:auto;"> </div> 
  
<!-- The Modal -->
<div class="modal" id="masterCreationModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create Master</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        
        <div class="row p-2">
            <div class="col-md-6">
                <h4>Accounts-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>/admin/accounts/add?p=1" class="list-group-item list-group-item-action">Account</a>
                  <a href="<?= base_url() ?>/admin/billsundry/add?p=1" class="list-group-item list-group-item-action">Bill Sundry</a>
                  <a href="<?= base_url() ?>/admin/accounts/add_group?p=1" class="list-group-item list-group-item-action">Account Groups</a>
                </div>
            </div>
            <div class="col-md-6">
                <h4>Items-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>/admin/items/add_item?p=1" class="list-group-item list-group-item-action">Items</a>
                  <a href="<?= base_url() ?>/admin/items/add_group?p=1" class="list-group-item list-group-item-action">Item Groups</a>
                  <a href="<?= base_url() ?>/admin/items/add_category?p=1" class="list-group-item list-group-item-action">Stock Category</a>
                </div>
            </div>

            
        </div>

        <div class="row p-2">
            <div class="col-md-6">
                <h4>Material Centre-</h4>
                <div class="list-group">
                  <a href="<?= base_url() ?>/admin/material_centres/add_centres?p=1" class="list-group-item list-group-item-action">Material Centre</a>
                  <a href="<?= base_url() ?>/admin/material_centres/add_group?p=1" class="list-group-item list-group-item-action">Material Centre Groups</a>
                  <a href="<?= base_url() ?>/admin/material_centres/add_mc_store?p=1" class="list-group-item list-group-item-action">Material Centre Stores</a>
                </div>
            </div>
            
        </div>

      </div>

    </div>
  </div>
</div> 
 
<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>
<script>

    $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#masterCreationModal').modal('show');
        }
    });
    
    var data_json = <?php echo json_encode($trial_balance_list);?>;
    console.log(data_json)
    var begin_json = structuredClone(data_json).filter(function (el) {
      return el.credit != '' || el.debit != '';
    })

    $('.nil').css('display', 'none');

    $('.nil').css('display', 'none');
    $('.nil[data-nil='+1+'][data-type="balance"]').css('display', 'block');
    $('.nil[data-nil='+0+'][data-type="transaction"]').css('display', 'block');


    $(document).on('click', '.addon_type', function(){

        var nil = $(this).data('nil');
        var type = $(this).data('type');
        var data = structuredClone(data_json);


        if(nil == 1){
            $('.nil[data-type="'+type+'"]').css('display', 'none');
            $('.nil[data-type="'+type+'"][data-nil="0"]').css('display', 'block');
        }
        if(nil == 0){
            $('.nil[data-type="'+type+'"]').css('display', 'none');
            $('.nil[data-type="'+type+'"][data-nil="1"]').css('display', 'block');
        }

        var nil_balance = $('.nil[data-type="balance"][data-nil="1"]').css('display');
        var nil_transaction = $('.nil[data-type="transaction"][data-nil="1"]').css('display');

        if(nil_balance == 'block'){
            data = data.filter(function (el) {
              return el.credit != '' || el.debit != '';
            })
        }
        if(nil_transaction == 'block'){
            data = data.filter(function (el) {
              return el.transaction_status == 1;
            }); 
        }

        $("#grid_search").pqGrid('option', 'dataModel.data', data);
        $("#grid_search").pqGrid('refreshDataAndView');
    });


     $(function () {
         
         function calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;
           
        data.forEach(function(row){
            
            debitTotal += row.debit_total;
            creditTotal += row.credit_total;
           
        })

        var totalData = {
                group_name: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color', 
                summaryRow: true,
               
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

             { title: "GROUP/ ACCOUNT/ BSD", dataIndx: "group_name", width: 100},
             { title: "PARENT", width: 100, dataIndx: "parent"},
             { title: "COMPANIES", width: 100, dataIndx: "companies"},
             { title: "DEBIT "+ "("+SYSTEM_CURRENCY+")", width: 100, align: "right", dataIndx: "debit"},
             { title: "CREDIT "+ "("+SYSTEM_CURRENCY+")", width: 100, align: "right", dataIndx: "credit"}
       
        ];

        var dataModel = {"data": begin_json}
        
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
    var rowData   = ui.rowData;
    var ajax      = rowData.ajax;
    var group_id  = rowData.group_id;
    var type      = rowData.type;

    if(type == 'opn')
        window.location.href= baseurl+'/grpcomp/accounts/list';

    if(type == 'grp')
      window.location.href= baseurl+'/grpcomp/reports/accounts_trial/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";

    if(type == 'acc')
      <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
        window.location.href= baseurl+'/grpcomp/reports/account_ledger_detail/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
      <?php } else { ?>
        window.location.href= baseurl+'/grpcomp/reports/account_summary/'+group_id; 
      <?php } ?> 

    if(type=='bsd')
      <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
        window.location.href= baseurl+'/grpcomp/reports/bill_sundry_ledger/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
      <?php } else { ?>
        window.location.href= baseurl+'/grpcomp/reports/bill_sundry_summary/'+group_id; 
      <?php } ?>

    }

  newObj.cellKeyDown= function(evt, ui) {
    var rowData    = ui.rowData;
    var ajax      = rowData.ajax;
    var group_id   = rowData.group_id;
    var type  = rowData.type;

    if (evt.keyCode==13){

      if(type == 'opn')
        window.location.href= baseurl+'/grpcomp/accounts/list';

      if(type == 'grp')
        window.location.href= baseurl+'/grpcomp/reports/accounts_trial/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";

      if(type == 'acc')
        <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
          window.location.href= baseurl+'/grpcomp/reports/account_ledger_detail/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        <?php } else { ?>
          window.location.href= baseurl+'/grpcomp/reports/account_summary/'+group_id; 
        <?php } ?> 

      if(type=='bsd')
        <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
          window.location.href= baseurl+'/grpcomp/reports/bill_sundry_ledger/'+group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        <?php } else { ?>
          window.location.href= baseurl+'/grpcomp/reports/bill_sundry_summary/'+group_id; 
        <?php } ?>
    }

    // if (evt.keyCode==119){

    //   if(type == 'acc' || type == 'aop')
    //     window.location.href= baseurl+'/grpcomp/accounts/modify/'+group_id;
    //   if(type == 'bsd' || type == 'bop')
    //     window.location.href= baseurl+'/grpcomp/billsundry/modify/'+group_id;
    //   if(type == 'grp')
    //     window.location.href= baseurl+'/grpcomp/accounts/modify_group/'+group_id;
    // }

  }
        
  var $grid = $("#grid_search").pqGrid(newObj);
    
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
      var nil_type  = $('#nil_type').val(); 
      var from_date = $('#from_date').val(); 
      var to_date   = $('#to_date').val(); 
      var view      = $('#view').val();       
      var stringparameters = "view="+view+"&nil_type="+nil_type+"&from_date="+from_date+"&to_date="+to_date;
      
        window.location.href= baseurl+"/admin/export/trial_balance?"+stringparameters;
           
        }
    
 </script>  
