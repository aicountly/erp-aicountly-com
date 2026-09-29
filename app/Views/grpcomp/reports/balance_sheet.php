<?php $header = array(  'title' => 'Balance Sheet' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
<div class="row mb-2">
  <div class="col-md-6">
    <h3 class="pb-0">Balance Sheet</h3>
    <p>
      <em>As the end of <?= $to_date ?>
      </em>
    </p>
  </div>
  
  <div class="col-md-6 text-end">
    <div class="taskmenus">
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span>
      </a>
      <a href="javascript:void(0)" onclick="print_grid();">
        <span class="material-symbols-outlined">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined">download</span>
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
      </li>
    </div>
  </div>
  
  
  <div class="col-md-4">
    <form class="form" method="get" id="salefrm2" autocomplete="off">
      <input type="hidden" name="nil_type" value="<?= $nil_type ?>">
      <div class="input-group">
        
        <span class="input-group-text px-1">From</span>
        <input autocomplete="off" type="text" name="from_date" value="<?= $from_date ?>" class="datepicker form-control" style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input autocomplete="off" type="text" name="to_date" value="<?= $to_date ?>" class="datepicker form-control" style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendermodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        <input type="submit" class="btn btn-sm btn-success" value="GO">
        
      </div>
    </form>
  </div>
  
  
  <div class="col-md-8 text-end">
    
    <div class="dropdown d-inline-block me-1" style="width:220px;">
      <div class="input-group input-group-sm input-group">
        <span class="input-group-text">Format</span>
        <select form="salefrm2" class="form-select" name="format"  onchange="this.form.submit()">
          <option <?= ($format==1) ? 'selected' : '' ?> value="1"> Horizontal</option>
          <option <?= ($format==2) ? 'selected' : '' ?> value="2">Vertical</option>
        </select>
        
      </div>
    </div>
    <div class="dropdown d-inline-block me-1" style="width:220px;">
      <div class="input-group input-group-sm input-group">
        <span class="input-group-text">View</span>
        <select form="salefrm2" class="form-select" name="view"  onchange="this.form.submit()">
          <option <?= ($view==1) ? 'selected' : '' ?> value="1"> Schedules</option>
          <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
          <option <?= ($view==2) ? 'selected' : '' ?> value="2">Detailed</option>
          <option <?= ($view==3) ? 'selected' : '' ?> value="3">Accounts</option>
        </select>
        
      </div>
    </div>
    <div class="dropdown float-end">
      <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
      <ul class="dropdown-menu">
        <li>
          <a data-nil="0" <?= ($nil_type==1) ? 'style="display: none;"' : '' ?> class="dropdown-item addon_type nil_type" href="javascript:void(0)">Include Nil Balances</a>
        </li>
        <li>
          <a data-nil="1" <?= ($nil_type==0) ? 'style="display: none;"' : '' ?> class="dropdown-item addon_type nil_type" href="javascript:void(0)">Exclude Nil Balances</a>
        </li>
      </ul>
      <button class="btn btn-sm btn-success m-1" type="button">Templates</button>
      <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success m-1">Back</a>
    </div>
  </div>
  
  
</div>

<div class="modal fade mt-5 modal-lg" id="calendermodal" tabindex="-1" aria-labelledby="calendermodallabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="col-12 calccard card m-auto">
        <form class="form" method="get" id="salefrm2" autocomplete="off">
          <input type="hidden" name="view" value="<?= $view ?>">
          <input type="hidden" name="nil_type" value="<?= $nil_type ?>">
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
              
              <span class="fw-bold d-inline-block px-4">
                <input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
              </div>
              <div class="col-md-6">
                <div class="input-group mb-3">
                  <button type="button" class="input-group-text" id="prev_year">
                  <span class="material-symbols-outlined">arrow_back_ios</span>
                  </button>
                  <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;">FY: 
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
    <button type="button" class="btn btn-primary">Save changes</button>
  </div>
  </div>
  </div>
</div>


<br>
<div id="grid_search" style="margin:auto;"> </div>
<div id="select_change_div">
</div>

<div class="row py-2">
  <div class="col-md-6">
    <a href="#" class="m-1 link-dark">Quick Access</a>
    <a href="<?php echo $base_url;?>reports/profit_loss" class="btn btn-success m-1">Profit & Loss</a>
    <a href="<?php echo $base_url;?>reports/trial_balance" class="btn btn-success m-1">Trial Balanace</a>
    <a href="#" class="btn btn-success m-1">Trading</a>
  </div>

  <div class="col-md-6 text-end">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    Download as
    </button>
    <ul class="dropdown-menu">
      <li>
        <a class="dropdown-item" href="javascript:void(0)" onclick="print_csv()">CSV</a>
      </li>
      <li>
        <a class="dropdown-item" href="javascript:void(0)" onclick="print_excel()">Excel</a>
      </li>
      <li>
        <a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf()">PDF</a>
      </li>
    </ul>
  </div>

</div>
<!-- The Modal -->
<div class="modal" id="masterCreationModal">
  <div class="modal-dialog modal-lg">
  <div class="modal-content">
  <!-- Modal Header -->
  <div class="modal-header">
    <h4 class="modal-title">Create Master</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal">
    </button>
  </div>
  <!-- Modal body -->
  <div class="modal-body">
    
    <div class="row p-2">
      <div class="col-md-6">
        <h4>Accounts-</h4>
        <div class="list-group">
          <a href="<?= base_url() ?>/grpcomp/accounts/add?p=1" class="list-group-item list-group-item-action">Account</a>
          <a href="<?= base_url() ?>/grpcomp/billsundry/add?p=1" class="list-group-item list-group-item-action">Bill Sundry</a>
          <a href="<?= base_url() ?>/grpcomp/accounts/add_group?p=1" class="list-group-item list-group-item-action">Account Groups</a>
        </div>
      </div>
      <div class="col-md-6">
        <h4>Items-</h4>
        <div class="list-group">
          <a href="<?= base_url() ?>/grpcomp/items/add_item?p=1" class="list-group-item list-group-item-action">Items</a>
          <a href="<?= base_url() ?>/grpcomp/items/add_group?p=1" class="list-group-item list-group-item-action">Item Groups</a>
          <a href="<?= base_url() ?>/grpcomp/items/add_category?p=1" class="list-group-item list-group-item-action">Stock Category</a>
        </div>
      </div>
      
    </div>
    <div class="row p-2">
      <div class="col-md-6">
        <h4>Material Centre-</h4>
        <div class="list-group">
          <a href="<?= base_url() ?>/grpcomp/material_centres/add_centres?p=1" class="list-group-item list-group-item-action">Material Centre</a>
          <a href="<?= base_url() ?>/grpcomp/material_centres/add_group?p=1" class="list-group-item list-group-item-action">Material Centre Groups</a>
          <a href="<?= base_url() ?>/grpcomp/material_centres/add_mc_store?p=1" class="list-group-item list-group-item-action">Material Centre Stores</a>
        </div>
      </div>
      
    </div>
  </div>
  </div>
  </div>
</div>

<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>
<style>
    .boldcell{font-weight:700;}
    
</style>
<script>

  $(document).keydown(function(e) {
      if (e.which == 45) { 
          $('#masterCreationModal').modal('show');
      }
  });


  $(document).on('click', '.nil_type', function(){
    var nil_type = $(this).data('nil');
    nil_type = nil_type == 1 ? 0 : 1;
    $('input[name="nil_type"]').val(nil_type);
    $('#salefrm2').submit();
  });

  var colModel = [
      { title: "LIABILITIES", dataIndx: "l_group_name", width: 100,render: function(ui){
              if( ui.rowData.summaryRow ){
                  return "<b>"+ui.cellData+"</b>";
              }
          } },
      { title: "AMT " + "("+SYSTEM_CURRENCY+")", width: 100,dataIndx: "l_balance", align: "right"},
      { title: "ASSETS", width: 100, dataIndx: "r_group_name" },
      { title: "AMT " + "("+SYSTEM_CURRENCY+")", width: 100, dataIndx: "r_balance", align: "right"}   
  ];
            
  var dataModel = {"data":<?= json_encode($data) ?>}
  var newObj = {
      scrollModel: { autoFit: true },
      collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
      height: 'flex',
      selectionModel: { type: 'cell',mode:'double', column: true },
      dataModel: dataModel,
      colModel: colModel,  
     
      numberCell: { show: false },
      filterModel: { mode: 'OR' },
      editable: false,
      showTitle: false,
      
      create: function (evt, ui) {// make first row auto selected
            var grid = this,
              $select_row = $(".select-row"),
              data = ui.dataModel.data;
             grid.setSelection({ rowIndx: 0, focus: true });
      }, 
  };

           
  newObj.cellDblClick = function(event, ui) {
    var rowData    = ui.rowData;
    var colmnData  = ui.column;
                
    if(colmnData.dataIndx=='l_group_name' || colmnData.dataIndx=='l_balance'){
        var l_group_id = rowData.l_group_id;
        var l_type = rowData.l_type;

        if(l_type == 'pnl')
            window.location.href= baseurl+'/grpcomp/reports/profit_loss?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
        if(l_type == 'opn')
            window.location.href= baseurl+'/grpcomp/reports/trial_balance?nil_type=1&view=2&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
        if(l_type == 'acc')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/account_ledger_detail/'+l_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/account_summary/'+l_group_id; 
            <?php } ?>
        if(l_type=='bsd')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_ledger/'+l_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_summary/'+l_group_id; 
            <?php } ?>
        if(l_type == 'grp')
            window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+l_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        if(l_type == 'prt')
            window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+l_group_id +'/1'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        if(l_type == 'stk')
            window.location.href= baseurl+'/grpcomp/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";  
    }
    if(colmnData.dataIndx=='r_group_name' || colmnData.dataIndx=='r_balance'){
        var r_group_id = rowData.r_group_id;
        var r_type = rowData.r_type;

        if(r_type == 'pnl')
            window.location.href= baseurl+'/grpcomp/reports/profit_loss?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
        if(r_type == 'opn')
            window.location.href= baseurl+'/grpcomp/reports/trial_balance?nil_type=1&view=2&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
        if(r_type == 'acc')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/account_ledger_detail/'+r_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/account_summary/'+r_group_id; 
            <?php } ?>
        if(r_type=='bsd')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_ledger/'+r_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_summary/'+r_group_id; 
            <?php } ?>
        if(r_type == 'grp')
            window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+r_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        if(r_type == 'prt')
            window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+r_group_id +'/1'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
        if(r_type == 'stk')
            window.location.href= baseurl+'/grpcomp/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";  
    }
  }   
    
  newObj.cellKeyDown= function(evt, ui) {
    var rowData     = ui.rowData;
    var colmnData  = ui.column;
    if (evt.keyCode==13)
    { 
      if(colmnData.dataIndx=='l_group_name' || colmnData.dataIndx=='l_balance'){
          var l_group_id = rowData.l_group_id;
          var l_type = rowData.l_type;

          if(l_type == 'pnl')
              window.location.href= baseurl+'/grpcomp/profit_loss?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
          if(l_type == 'opn')
              window.location.href= baseurl+'/grpcomp/reports/trial_balance?nil_type=1&view=2&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';

          if(l_type == 'acc')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/account_ledger_detail/'+l_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/account_summary/'+l_group_id; 
            <?php } ?>

          if(l_type=='bsd')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_ledger/'+l_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_summary/'+l_group_id; 
            <?php } ?>

          if(l_type == 'grp')
              window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+l_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
          if(l_type == 'prt')
              window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+l_group_id +'/1'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
          if(l_type == 'stk')
              window.location.href= baseurl+'/grpcomp/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";  
      }
      if(colmnData.dataIndx=='r_group_name' || colmnData.dataIndx=='r_balance'){
          var r_group_id = rowData.r_group_id;
          var r_type = rowData.r_type;

          if(r_type == 'pnl')
              window.location.href= baseurl+'/grpcomp/profit_loss?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
          if(r_type == 'opn')
              window.location.href= baseurl+'/grpcomp/reports/trial_balance?nil_type=1&view=2&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
          if(r_type == 'acc')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/account_ledger_detail/'+r_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/account_summary/'+r_group_id; 
            <?php } ?>
          if(r_type=='bsd')
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_ledger/'+r_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
            <?php } else { ?>
              window.location.href= baseurl+'/grpcomp/reports/bill_sundry_summary/'+r_group_id; 
            <?php } ?>
          if(r_type == 'grp')
              window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+r_group_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
          if(r_type == 'prt')
              window.location.href= baseurl+'/grpcomp/accounts/accounts_trial/'+r_group_id +'/1'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
          if(r_type == 'stk')
              window.location.href= baseurl+'/grpcomp/reports/stock_status'+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";   
      }
    }

    if (evt.keyCode==119){ 
      if(colmnData.dataIndx=='l_group_name' || colmnData.dataIndx=='l_balance'){
          var group_id = rowData.l_group_id;
          var type = rowData.l_type;

          if(type == 'acc')
              window.location.href= baseurl+'/grpcomp/accounts/modify/'+group_id;
          if(type == 'bsd')
              window.location.href= baseurl+'/grpcomp/billsundry/modify/'+group_id;
          if(type == 'grp')
              window.location.href= baseurl+'/grpcomp/accounts/modify_group/'+group_id;

      }

      if(colmnData.dataIndx=='r_group_name' || colmnData.dataIndx=='r_balance'){
          var group_id = rowData.r_group_id;
          var type = rowData.r_type;

          if(type == 'acc')
              window.location.href= baseurl+'/grpcomp/accounts/modify/'+group_id;
          if(type == 'bsd')
              window.location.href= baseurl+'/grpcomp/billsundry/modify/'+group_id;
          if(type == 'grp')
              window.location.href= baseurl+'/grpcomp/accounts/modify_group/'+group_id;

      }    
    }
            
  }
        
  var grid = $("#grid_search").pqGrid(newObj);
    
     
    // });
        
  function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
    if (!results) return '';
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }

  function print_grid()
  {
    $('#print').trigger('click');
  }
  function print_csv()
  {
    $('#csv').trigger('click');
  }
  function print_excel()
  {
    var nil_type  = getParameterByName('nil_type'); 
    var from_date = getParameterByName('from_date'); 
    var to_date   = getParameterByName('to_date'); 
    var view      = getParameterByName('view');             
    var stringparameters = "nil_type="+nil_type+"&from_date="+from_date+"&to_date="+to_date+"&view="+view;
    window.location.href= baseurl+"/grpcomp/export/balance_sheet?"+stringparameters;
   
  }
  function print_pdf()
  {
    $('#pdf').trigger('click');
  }

</script> 