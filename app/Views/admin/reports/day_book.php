<?php $header = array(  'title' => 'Day Book' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
.pq-grid-center .pq-grid-cont:has(> .pq-grid-norows){
text-indent: -9999px;
background-image: url("/public/assets/images/no_records_found.png");
background-repeat:no-repeat;
background-position: center center;
background-size: 400px 100px;
}
</style>

<?php 

$params = http_build_query([
'from_date'         => $from_date,
'to_date'         => $to_date,
]);
?>

<div class="row mb-3">
  <div class="col-md-6 order-1 pb-1">
    <h3>Day Book</h3>
  </div>
  <div class="col-md-6 order-3 order-lg-2 text-end">
    <div class="taskmenus">
      <a class="hideinline-lg"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu">
        <span class="material-symbols-outlined">filter_list</span>
      </a>
      <a href="javascript:void(0)" id="refresh_grid">
        <span class="material-symbols-outlined">refresh</span>
      </a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span>
      </a>
      <!-- <a href="<?= base_url() ?>/admin/export/daybook_print?<?= $params ?>">
        <span class="material-symbols-outlined ">print</span>
      </a> -->

      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">print</span>
      </a>

      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="<?= base_url() ?>/admin/export/daybook_print?<?= $params ?>">Date Wise</a>
        </li>
        <li>
          <a class="dropdown-item" href="<?= base_url() ?>/admin/export/daybook_print_list?<?= $params ?>">List View</a>
        </li>
      </ul>



      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined ">download</span>
        </span>
      </a>
      <ul class="dropdown-menu">
        <li class="open-comingsoon">
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a>
        </li>
        <li class="open-comingsoon">
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
      <a href="#" class="hideinline-lg">
        <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
      </a>
    </div>
  </div>

  <div class="col-lg-5 col-md-8 order-2 order-lg-3">
    <form class="form needs-validation" method="get" id="salefrm" autocomplete="off" novalidate>
      <div class="input-group input-group-sm">
        <span class="input-group-text px-1">From</span>
        <input type="text" name="fromdate" id="mfromdate" value="<?php echo $from_date;?>" class="datepicker form-control" required  style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input type="text" name="todate" id="mtodate" value="<?php echo $to_date;?>" class="datepicker form-control" required style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        
        <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
      </div>
    </form>
  </div>

  <div class="col-lg-7 text-lg-end order-4  collapse listmenu-lg" id="listmenu">
    <div class="dropdown d-inline-block me-1" style="width:180px;">
      <div class="input-group">
        <span class="input-group-text px-1">View</span>
        <select form="salefrm" class="form-select ps-1" name="view" onchange="this.form.submit()">
          <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
          
        </select>
      </div>  </div>
      
      <div class="dropdown float-end d-inline-block">
        <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
        <ul class="dropdown-menu">
         <!--  <li>
            <a id="trash" data-type="0" class="dropdown-item" href="javascript:void(0);">Enable Trash Mode</a>
          </li>
          <li>
            <a id="duplicate" data-type="0" class="dropdown-item" href="javascript:void(0);">Duplicate Voucher</a>
          </li> -->
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

        <div class="form-check form-check-inline me-2">
          <input  class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
          <label class="form-check-label" for="fg">Fixed Grid</label>
        </div>
        <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success showinline-lg">Back</a>
      </div>
    </div>
  </div>

  <div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        
        <div class="col-12 calccard card m-auto">
          <form class="form" method="get" id="salefrm2" autocomplete="off">
            <input type="hidden" name="view" value="<?= $view ?>">
            <div class="row p-4">

              <div class="col-md-6 comp_calender">
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
                    <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;">FY: </button>
                    <button type="button" class="input-group-text" id="next_year">
                    <span class="material-symbols-outlined">arrow_forward_ios</span>
                    </button>
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

<div id="validation_errors">
</div>
<div id="grid_search" style="margin:auto;"> </div>
<?php echo view('includes/footer_scripts'); ?>

<script>

    $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#voucherCreationModal').modal('show');
        }
    });

    $(document).on('change', '#delete_all_checkbox', function(){
        if(this.checked) 
            $('.delete_one_checkbox').prop("checked", true);
        else
            $('.delete_one_checkbox').prop("checked", false);
    });

    $(document).on('change', '.delete_one_checkbox', function(){
        $('#delete_all_checkbox').prop("checked", false);
    });

    function show_trash_checkbox()
    {
        var colModel = $("#grid_search").pqGrid('option', 'colModel');
        colModel[0].hidden = false;

        $("#grid_search").pqGrid( "option", "colModel", colModel );
        $("#grid_search").pqGrid( "option", { numberCell: {show: false}} );
        $("#grid_search").pqGrid( {autoFit: true} );
        $("#grid_search").pqGrid("refreshCM");
        $("#grid_search").pqGrid("refresh");

        $('.delete_checkbox').prop("checked", false); 
    }

    function hide_trash_checkbox() // written in footer
    {
        var colModel = $("#grid_search").pqGrid('option', 'colModel');
        colModel[0].hidden = true;

        $("#grid_search").pqGrid( "option", "colModel", colModel );
        $("#grid_search").pqGrid( "option", { numberCell: { show: true,width: 40, title: "#" }} );
        $("#grid_search").pqGrid("refreshCM");
        $("#grid_search").pqGrid("refresh");

        $('.delete_checkbox').prop("checked", false); 
    }
   
   
   
   
   
    $(document).on("click","#trash",function(){
        var type = $(this).data('type');
        if(type == 0){
            $(this).text('Delete Vouchers'); 
            $(".hidden").show();
            $(this).data('type', '1');
            alert_success("Trash mode enabled");
            show_trash_checkbox();
        }
        if(type == 1){
            
            if( $('.delete_one_checkbox:checked').length=='0'){
                alert_notification("First select voucher to delete!!");
                return false;
            }
            else{
                var vouchers_array=[];
                $('.delete_one_checkbox:checked').each(function(){
                    vouchers_array.push($(this).val());
                });
                
                confirm_voucher_delete(vouchers_array);
            }
        }
     });

 $(function () {
     
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


            { dataIndx: "state", sortable:false,  align: "center", resizable: false, hidden:true,
                title: '<input type="checkbox" value="" class="delete_checkbox" id="delete_all_checkbox">',
                menuIcon: false,
                cls: 'pq-grid-number-cell', 
                sortable: false, 
                
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
               
                    return '<input type="checkbox" value="'+rd.voucher_txn_id+'" class="delete_checkbox delete_one_checkbox">';
                }
            },
            
            { title: "DATE", align:"left",   dataIndx: "date",sortable:false },
            { title: "PARTICULARS", align:"left",   dataIndx: "particulars",sortable:false  },
            { title: "VOUCHER TYPE", align:"left",    dataIndx: "voucher_type",sortable:false  },
            { title: "VOUCHER NO.", align:"left",   dataIndx: "voucher_no",sortable:false  },
            { title: "DEBIT", align:"right",   dataIndx: "debit" ,sortable:false },
            { title: "CREDIT", align:"right",   dataIndx: "credit" ,sortable:false },
        ];
        
     var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'view':'<?php echo $view;?>'},
            url: "<?php echo base_url();?>/admin/reports/ajax_day_book",
             getData: function (dataJSON) {
                var data = dataJSON.data;

                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
     var newObj = {

            scrollModel: { autoFit: true },
            height: 'flex',
			resizable: true,
           autoResize: true,
            collapsible: { on: false, collapsed: false, toggle: true, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            filterModel: { on: true, mode: "OR", header: true, type:'remote' },
            numberCell: { show: true,width: 40, title: "#" },
            colModel : colModel,
            editable: false,
            // numberCell: { show: false },
             wrap:false,
            showTitle: false,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                    grid.setSelection({ rowIndx: 0, focus: true });

            },
            
            dataReady:function(event,ui) {
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
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler,
                        options: function (ui) {
                            var CM = ui.colModel;
                            var opts = [];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
							  if(column.dataIndx!='state' && column.dataIndx!='particulars'&& column.dataIndx!='debit'&& column.dataIndx!='credit'){
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
                var col_type            = rowData.vch_type;
                var ajax                = rowData.ajax;
                var voucher_txn_id      = rowData.voucher_txn_id;
                var voucher_type_id     = rowData.voucher_type_id;
                var bom_id              = rowData.bom_id;
                var bom_batches         = rowData.bom_batches;

                hide_trash_checkbox();
                $("#grid_search").pqGrid('saveState');
               var select_rowindx =  set_page();
			   
                edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);

       }
       
         newObj.cellKeyDown = function(evt, ui) {
             var rowData           = ui.rowData;
           var ajax              = rowData.ajax;
           var col_type          = rowData.col_type;
           var voucher_txn_id    = rowData.voucher_txn_id;
           var voucher_type_id   = rowData.voucher_type_id;
         var bom_id            = rowData.bom_id;
         var bom_batches       = rowData.bom_batches;
           if (evt.keyCode==13){
                    hide_trash_checkbox();
                $("#grid_search").pqGrid('saveState');
                 var select_rowindx =  set_page();

                edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx);
           }
       
         }
       
    var $grid = $("#grid_search").pqGrid(newObj);
	/* pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });
*/
     $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){
            hide_trash_checkbox();
         $("#grid_search").pqGrid('saveState');
       }); 
       
       
    

    function set_page()
    {
       var select_row = $("#grid_search").pqGrid("selection", { type:'row', method:'getSelection'});
        if(select_row){
            const url = new URL(window.location.href);
            url.searchParams.set('rowIndx', select_row[0].rowIndx);
            window.history.replaceState(null, null, url);
			return select_row[0].rowIndx;
        }
         else{
            const url = new URL(window.location.href);
            if(url.searchParams.has('rowIndx')){
                url.searchParams.delete('rowIndx');
                window.history.replaceState(null, null, url);  
            }if(url.searchParams.has('duplc')){
                url.searchParams.delete('duplc');
                window.history.replaceState(null, null, url);  
            }
			return 0;
        }  		
    }
	
	$(document).on("click","#duplicate",function(){
	    Swal.fire({
        title: 'Are you sure to duplicate voucher?',
        text: "",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {       
         var select_row          = $("#grid_search").pqGrid("selection", { type:'row', method:'getSelection'});       
		 var rowData             = select_row[0].rowData;
         var col_type            = rowData.vch_type;
         var ajax                = rowData.ajax;
         var voucher_txn_id      = rowData.voucher_txn_id;
         var voucher_type_id     = rowData.voucher_type_id;
         var bom_id              = rowData.bom_id;
         var bom_batches         = rowData.bom_batches;
         var select_rowindx      = set_page();	
         edit_voucher(voucher_txn_id, voucher_type_id,select_rowindx,1);  						
        }
      });	 
   });
   
    
     $(document).on('click','#select_all',function(){

        if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;        
            });
      $(".editbtn").addClass("disabled");
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
      $(".editbtn").removeClass("disabled");
           }
        });
    });

  $(document).on('change', '#fg', function(){
    if($(this).is(":checked")) {
      $("#grid_search").pqGrid('option', 'height', 420);
    }
    else{
      $("#grid_search").pqGrid('option', 'height', 'flex');
    }
    $("#grid_search").pqGrid('refreshDataAndView');
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
      var m  = $('#m').val(); 
      var from_date = '<?php echo $from_date;?>'; 
      var to_date   = '<?php echo $to_date;?>'; 
      var view      = '<?php echo $view;?>';       
      var stringparameters = "view="+view+"&from_date="+from_date+"&to_date="+to_date;
      
        window.location.href= baseurl+"/admin/export/daybook?"+stringparameters;
           
        } 
</script>

<style>
.hidden{display:none;}
</style>
</body>
</html>