<?php $header = array(  'title' => 'Item Summary' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>

<div class="row mb-2">

  <div class="col-sm-6">
    <h3>Item Summary</h3>
  </div>
  <div class="col-sm-6 text-end">
  <div class="taskmenus">

    <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class=""><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
    <ul class="dropdown-menu" style="">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
    <li><a class="dropdown-item" href="#">Document</a></li>
    </ul>
     <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class=""><span class="material-symbols-outlined">share</span></a> 
        <ul class="dropdown-menu" style="">
      <li><a class="dropdown-item" href="#">Facebook</a></li>
      <li><a class="dropdown-item" href="#">Twitter</a></li>
      <li><a class="dropdown-item" href="#">Instagram</a></li>
    </ul>
    <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success ms-3" style="margin-top:-20px; position:relative;">Back</a>

</div>
 
</div>

<div class="row">
  <div class="col-md-6">
    <p><em>Item: <strong><?php echo $item_name;?></strong></em></p>
  </div>
  <div class="col-md-6 text-end">
    <p class=" pe-3">
      <em>Op. Qty: <strong><?php echo $item_qty;?></strong></em>
      <br>
      <em>Value: <strong><?php echo $item_value;?></strong></em>
    </p>
  </div>
</div> 

<div class="row">
  <div class="col-md-12">
    <form class="form needs-validation" method="get" id="salefrm"  novalidate>
      <input type="hidden" name="item_id" value="<?= $item_id ?>">
      <input type="hidden" name="mc_grp_id" value="<?= $mc_grp_id ?>">

      <div class="dropdown d-inline-block" style="width:220px;">
        <div class="input-group">
            <span class="input-group-text">Valuation</span>
            <select form="salefrm" name="val" id="val" class="form-select" onchange="this.form.submit()">

              <?php foreach ($valuation_list as $key => $value) {  ?>
                  <option value="<?= $key ?>" <?= ($key==$val)?"selected":"";?> ><?= $value ?></option>
              <?php } ?>
               
             </select>
        </div>  
      </div>

      <div class="dropdown d-inline-block" style="width:220px;">
        <div class="input-group">
            <span  class="input-group-text" onchange="this.form.submit()">Unit</span>
            <select form="salefrm" name="unit_id" id="unit_id" class="form-select" onchange="this.form.submit()" required>
               <option value=""></option>
               <?php foreach ($units_dropdown as $key => $value) { ?>
                 <option <?= ($value['id'] == $unit_id) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
               <?php } ?>
             </select>
        </div>  
      </div>

      <?php if($mc_grp_id == 0){ ?>
      <div class="dropdown d-inline-block" style="width:220px;">
        <div class="input-group">
            <span  class="input-group-text">MC</span>

            <select form="salefrm" name="mc_id" class="form-select" onchange="this.form.submit()">
              <option value="0">All MC</option>
              <?php foreach ($matrcntr_dropdown as $key => $value){ ?>
                 <option <?= ($value['id'] == $mc_id) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
               <?php } ?>
            </select>
        </div>  
      </div>
      <?php } ?>

      <?php if($mc_grp_id != 0){ ?>
          <div class="dropdown d-inline-block" style="width:220px;">
            <div class="input-group">
                <span  class="input-group-text">MC Group</span>

                <select form="salefrm" name="mc_grp_id" class="form-select" onchange="this.form.submit()">
                   <?php foreach ($matrcntr_grp_dropdown as $key => $value) { ?>
                     <option <?= ($value['id'] == $mc_grp_id) ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['label'] ?></option>
                   <?php } ?>
                </select>
            </div>  
          </div>
      <?php } ?>
      </form>
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

<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>
<script>

   $(document).keydown(function(e) {
        if (e.which == 45) { 
            $('#voucherCreationModal').modal('show');
        }
    });
  
        
var colModel = [
    { title: "Month (<?= grp_comp()->fy_name ?>)", align:"left", width: 180,   dataIndx: "month" },
   

    { title: "Debit", width: 140, align: "center", colModel: [
        { title: "Qty", align: "right",dataIndx: "debit_qty" ,
        render: function( ui ) {
            var rd = ui.rowData;
            return formatQty(rd.debit_qty);   

        },}, 
        { title: "Amount", align: "right",dataIndx: "debit_amount",
            render: function( ui ) {
            var rd = ui.rowData;
            return formatAmount(rd.debit_amount);   

            },
        },
    ] },
    { title: "Credit", width: 140, align: "center", colModel: [
        { title: "Qty", align: "right",dataIndx: "credit_qty" ,
        render: function( ui ) {
            var rd = ui.rowData;
            return formatQty(rd.credit_qty);   

        },}, 
        { title: "Amount", align: "right",dataIndx: "credit_amount",
            render: function( ui ) {
            var rd = ui.rowData;
            return formatAmount(rd.credit_amount);   

            },
        },
    ] },
    { title: "Balance", width: 140, align: "center", colModel: [
    { title: "Qty", align: "right",dataIndx: "balance_qty" ,
        render: function( ui ) {
            var rd = ui.rowData;
            return formatQty(rd.balance_qty);   

        },}, 
    { title: "Value", align: "right",dataIndx: "balance_amount",
        render: function( ui ) {
            var rd = ui.rowData;
            return formatValue(rd.balance_amount);   

        },
    },
    ] },

    { title: "Profit &nbsp;&nbsp;", align:"right", width: 20,   dataIndx: "profit",
        render: function( ui ) {
            var rd = ui.rowData;
            return formatAmount(rd.profit) + '&nbsp;&nbsp;';   

        },
    },
];
            
  var dataModel = {"data":<?php echo json_encode($summary);?>}
  var newObj = {
      scrollModel: { autoFit: true },
      height: 'flex',
      collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
      selectionModel: { type: 'row',mode:'single' },
      dataModel: dataModel,
      colModel : colModel,
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
  };
        
        
  newObj.rowDblClick = function(event, ui) {
    var rowData    = ui.rowData;

    var item_id     = <?= $item_id ?>;
    var from_date   = rowData.from_date;
    var to_date     = rowData.to_date;
    var unit_id     = <?= $unit_id ?>;
    var mc_id       = <?= $mc_id ?>;
    var mc_grp_id   = <?= $mc_grp_id ?>;
    var val         = $('select[name="val"]').val();

    window.location.href= baseurl+'/grpcomp/reports/item_ledger_detail?item_id='+item_id+'&from_date='+from_date+'&to_date='+to_date+'&unit_id='+unit_id+'&mc_grp_id='+mc_grp_id+'&mc_id='+mc_id+'&val='+val;
  }
 
  newObj.cellKeyDown= function(evt, ui) {
    var rowData     = ui.rowData;

    var item_id     = <?= $item_id ?>;
    var from_date   = rowData.from_date;
    var to_date     = rowData.to_date;
    var unit_id     = <?= $unit_id ?>;
    var mc_id       = <?= $mc_id ?>;
    var mc_grp_id   = <?= $mc_grp_id ?>;
    var val         = $('select[name="val"]').val();

    if (evt.keyCode==13){
     window.location.href= baseurl+'/grpcomp/reports/item_ledger_detail?item_id='+item_id+'&from_date='+from_date+'&to_date='+to_date+'&unit_id='+unit_id+'&mc_grp_id='+mc_grp_id+'&mc_id='+mc_id+'&val='+val;
    }
   
  }
         
  var $grid = $("#grid_search").pqGrid(newObj);

</script>
 </body>
</html>
