<?php $header = array(  'title' => 'Bill Sundry Ledger' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
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
    <h3 class="pb-3">Bill Sundry Ledgers</h3>
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
        <span class="material-symbols-outlined">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined">download</span>
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
      <!-- <li><a id="trash" data-type="0" class="dropdown-item" href="javascript:void(0);">Enable Trash Mode</a></li> -->
    </ul>

    <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="#">Action</a></li>
      <li><a class="dropdown-item" href="#">Another action</a></li>
      <li><a class="dropdown-item" href="#">Something else here</a></li>
    </ul>

    <a href="<?php echo history_back();?>"  class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
  </div>
</div>
</div>
<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
 <div class="modal-dialog">
  <div class="modal-content">

    <div class="col-12 calccard card m-auto">
      <form class="form" method="post" action ="<?php echo base_url();?>/admin/reports/account_ledger" id="salefrm2" autocomplete="off">

        <input type="hidden" name="type" value="Bill Sundry Ledger">
        <input type="hidden" name="detail" value="Bill Sundry Account">

        <div class="col-md-12 p-2">
          <label>Bill Sundry</label>
          <input name="item" type="text" class="form-control borderdark" value="<?= $account_name ?>" required>
          <input type="hidden" name="id" value="<?= $account_id ?>">
        </div>

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
                  <input type="text" class="form-control" fdprocessedid="cw7ydk" name="fromdate" id="fromdate" value="<?= $from_date ?>" placeholder="dd-mm-yyyy" required>
                </div>
              </div>
            </div>
            
            <div class="row align-items-center my-2">
              <div class="col-md-2 fw-bold pe-0">To</div>
              <div class="col-md-10">
                <div class="calc-inputgroup">
                  <input type="text" class="form-control"  fdprocessedid="b20o4z" name="todate" id="todate" value="<?= $to_date ?>" placeholder="dd-mm-yyyy" required>
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
<div class="row">
  <div class="col-md-6">
    <p><em>Account: <strong><?php echo $account_name;?></strong></em></p>
  </div>
  <div class="col-md-6 text-end">
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#openingBalanceModel">
          Opening Balance
      </button>
 </div>
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin:auto;"> </div> 

<div class="row">

  <div class="col-md-12 text-end">
   <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#closingBalanceModel">
          Closing Balance
      </button>
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
                <th style="text-align: center;">Company</th>
                <th style="text-align: center;">Balance</th>
                <th style="text-align: center;"></th>
              </tr>
            </thead>
            <tbody>
              <?php if($opn_balance_list){ ?>
                <?php foreach($opn_balance_list as $key =>$value){ ?>

                  <tr <?= ($key == count($opn_balance_list)-1) ? 'class="table-secondary"' : '' ?>>
                    <td><?= $value['company'] ?></td>

                    <td style="text-align: right;"><?= $value['balance'] ?></td>
                    <td><?= $value['balance_type'] ?></td>
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

<!-- The Modal -->
<div class="modal fade" id="closingBalanceModel">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Closing Balances</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover">

            <thead>
              <tr>
                <th style="text-align: center;">Company</th>
                <th style="text-align: center;">Balance</th>
                <th style="text-align: center;"></th>
              </tr>
            </thead>
            <tbody>
              <?php if($clo_balance_list){ ?>
                <?php foreach($clo_balance_list as $key =>$value){ ?>

                  <tr <?= ($key == count($clo_balance_list)-1) ? 'class="table-secondary"' : '' ?>>
                    <td><?= $value['company'] ?></td>

                    <td style="text-align: right;"><?= $value['balance'] ?></td>
                    <td><?= $value['balance_type'] ?></td>
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
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="18">Sales Invoice</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="19">Sales Order</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="2">Credit Note</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="7">Delivery Challan</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="17">Quotations</a>
            </div>
          </div>
          <div class="col-md-6">
            <h4>Purchases-</h4>
            <div class="list-group">
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="11">Purchase Invoice</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="12">Purchase Order</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="3">Debit Note</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="6">Inward Challan</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="21">Purchase Requisition</a>
            </div>
          </div>


        </div>

        <div class="row p-2">
          <div class="col-md-6">
            <h4>Banking-</h4>
            <div class="list-group">
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="9">Payments</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="13">Receipts</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="1">Contra</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="5">Journal</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="8">Memorandum</a>
            </div>
          </div>
          <div class="col-md-6">
            <h4>Items-</h4>
            <div class="list-group">
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="15">Stock Transfer</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="10">Physical Verification</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="14">Production Voucher</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="20">Stock Journal</a>
              <a href="javascript:void(0)" class="list-group-item list-group-item-action voucher_transaction"  data-id="4">Consignment Packing</a>
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
      $('#voucherCreationModal').modal('show');
    }
  });

  var accounts_list = <?php echo json_encode($accounts_list) ?>;
  getList(accounts_list);

  function getList(list) 
  {
        $('input[name="item"]').off('blur'); // unbind event first
        
        if($('input[name="item"]').hasClass('ui-autocomplete-input')) {
          $('input[name="item"]').autocomplete("destroy");
        }
        
        $( 'input[name="item"]' ).autocomplete({
          source: list,
          minLength: 0,
          select: function( event, ui ) {
            $(this).val(ui.item.label);
            $('input[name="id"]').val(ui.item.id);
          }
        })
        .on('focus', function(){
          $(this).autocomplete( "search", "" );
          $('input[name="item"]').val('');
          $('input[name="id"]').val('');
        })
        .on('blur', function(){

          if($(this).val() != '' && $('input[name="id"]').val() == '')
          {
            var acc = $(this).val();
            var index = list.findIndex(function(obj) {
              var string = obj.label.toLowerCase();
              var text = acc.toLowerCase();
              return  string.includes(text);
            });
            if(index > -1){
              $(this).val(list[index].label);
              $('input[name="id"]').val(list[index].id);

            }
            else{
              $('input[name="item"]').val('');
              $('input[name="id"]').val('');
            }
          }
          if($('input[name="id"]').val() == '')
          {
            $('input[name="item"]').val('');
            $('input[name="id"]').val('');
          }
        });
      }



 


    $(function () {

  function calculateSummary() {
    var data = this.option('dataModel.data');
    var debit = 0;
    var credit = 0;

    data.forEach(function(row){
        debit += parseAmount(row.debit);
        credit += parseAmount(row.credit);
    })

    var totalData = {
        project_name      : "Total",
        debit       : parseAmount(debit),
        credit      : parseAmount(credit),
        balance     : 'empty',
        pq_rowcls : 'grid_footer_color',
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
  

  var colModel = [

  { title: "COMPANY", dataIndx: "comp_name", width: 50},
  { title: "DATE", dataIndx: "voucher_date", width: 50,
    render: function( ui ) {
        var rd = ui.rowData;
     
        var arr = rd.voucher_date.split("-");
        return arr[2]+'-'+arr[1]+'-'+arr[0];  
      
    }
  },
  { title: "VOUCHER", width: 50, dataIndx: "voucher_type"},
  { title: "VCH/BILL NO", width: 50, dataIndx: "voucher_no"},
  { title: "DEBIT "+ "("+ SYSTEM_CURRENCY + ")", width: 80, align: "right", dataIndx: "debit",
      render: function( ui ) {
            var rd = ui.rowData;
            if(rd.debit != 0)
              return formatAmount(rd.debit);
            return '';
               
        }
  },
  { title: "CREDIT "+ "("+ SYSTEM_CURRENCY + ")", width: 80, align: "right", dataIndx: "credit",
      render: function( ui ) {
            var rd = ui.rowData;
            if(rd.credit != 0)
              return formatAmount(rd.credit);
            return ''; 
        }
  },
  { title: "BALANCE "+ "("+ SYSTEM_CURRENCY + ")", width: 80, align: "right", dataIndx: "balance",
      render: function( ui ) {
          var rd = ui.rowData;
          if(rd.balance != 'empty')
            return formatAmount(rd.balance);
          return '';
               
        }
  },
  { title: "", width: 30, dataType: "string", dataIndx: "balance_type"},

  ];

  var dataModel = { data: <?= json_encode($transactions) ?>};

  var newObj = {
      scrollModel: { autoFit: true },
      collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
      height: 420,//'flex',
      selectionModel: { type: 'row',mode:'single' },
      dataModel: dataModel,
      dataReady: calculateSummary,
      colModel: colModel,  
      numberCell: { show: true },
      pageModel: { type: 'local' },
      filterModel: { mode: 'OR', type: "local" },
      editable: false,
      showTitle: false,
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
     dataReady:function(event,ui) {
    
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
                    if(column.dataIndx!='chkbx'){   
                    obj[column.dataIndx] = column.title;
                    opts.push(obj);
                    }
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
    var rd    = ui.rowData;
    var voucher_txn_id    = rd.voucher_txn_id;
    var voucher_type_id   = rd.voucher_type_id;
    var comp_id           = rd.comp_id;
    var comp_fy_id        = rd.comp_fy_id;
    var bo_id             = rd.bo_id;

    edit_voucher(voucher_txn_id, voucher_type_id, comp_id, comp_fy_id, bo_id);
  }
   
  newObj.cellKeyDown= function(evt, ui) {

    var rd    = ui.rowData;
    var voucher_txn_id    = rd.voucher_txn_id;
    var voucher_type_id   = rd.voucher_type_id;
    var comp_id           = rd.comp_id;
    var comp_fy_id        = rd.comp_fy_id;
    var bo_id             = rd.bo_id;
    if (evt.keyCode==13){
      edit_voucher(voucher_txn_id, voucher_type_id, comp_id, comp_fy_id, bo_id);
    }
  }

  var $grid = $("#grid_search").pqGrid(newObj);


});






</script> <style>.hidden{display:none;}</style></body></html>