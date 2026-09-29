<?php $header = array('title' => 'Stock Status'); ?>
<?php echo view('includes/header', $header); ?>
<?php
$show_opening = ($_GET['show_opening'] ?? '0') == '1' ? 1 : 0;
$show_inwards = ($_GET['show_inwards'] ?? '0') == '1' ? 1 : 0;
$show_outwards = ($_GET['show_outwards'] ?? '0') == '1' ? 1 : 0;
$include_nil = ($_GET['include_nil'] ?? '0') == '1' ? 1 : 0;
$filter_keyword = $_GET['filter_keyword'] ?? '';
$has_filter = !empty($filter_keyword);
?>
<style>
.pq-grid-cell,
.pq-grid-col {
    white-space: nowrap;
}
</style>

<div class="row pb-2">
  <div class="col-sm-6">
    <h3>Stock Status(Item Wise)</h3>
  </div>  
  <div class="col-sm-6 text-end">
    <div class="taskmenus">
      <a href="javascript:void(0)" id="refresh_grid">
        <span class="material-symbols-outlined">refresh</span>
      </a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span>
      </a> 
      <?php 
      $params = http_build_query([
        'mc_id'       => $mc_id,
        'val_id'      => $val_id,
        'from_date'   => $from_date,
        'to_date'     => $to_date,
      ]);
      ?>
      <a href="<?php echo base_url(); ?>/admin/export/inventory_status_print?<?php echo $params; ?>">
        <span class="material-symbols-outlined">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined">download</span>
        </span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel('csv');">CSV</a></li>
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel('excel');">Excel</a></li>
        <li><a class="dropdown-item" href="#">Document</a></li>
      </ul>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">share</span>
      </a> 
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Facebook</a></li>
        <li><a class="dropdown-item" href="#">Twitter</a></li>
        <li><a class="dropdown-item" href="#">Instagram</a></li>
      </ul>
    </div>
  </div> 
</div>

<div class="row mb-2 align-items-top">
  <div class="col-md-6">
    <form class="form needs-validation" method="get" id="salefrm" novalidate>
      <div class="input-group input-group-sm">
        <span class="input-group-text px-1">From</span>
        <input type="text" name="from_date" id="from_date" value="<?php echo $from_date; ?>" class="datepicker form-control" required style="width: 90px;">
        <span class="input-group-text px-1">To</span>
        <input type="text" name="to_date" id="to_date" value="<?php echo $to_date; ?>" class="datepicker form-control" required style="width: 90px;">
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
          <span class="material-symbols-outlined">event</span>
        </button>
        <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
      </div>
    </form>
  </div>    
  <div class="col-md-6 text-end">
    <div class="form-check form-check-inline me-2">
      <input class="form-check-input" type="checkbox" value="1" name="InwardsCheck" id="InwardsCheck" <?php echo $show_inwards ? 'checked' : ''; ?>>
      <label class="form-check-label" for="InwardsCheck">Inwards</label>
    </div>
    <div class="form-check form-check-inline me-2">
      <input class="form-check-input" type="checkbox" value="1" name="OutwardsCheck" id="OutwardsCheck" <?php echo $show_outwards ? 'checked' : ''; ?>>
      <label class="form-check-label" for="OutwardsCheck">Outwards</label>
    </div>
    <div class="form-check form-check-inline me-2">
      <input class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
      <label class="form-check-label" for="fg">Fixed Grid</label>
    </div>
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" value="1" id="itemhsnCheck">
      <label class="form-check-label" for="itemhsnCheck">Item HSN</label>
    </div>
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" value="1" id="opBalanceCheck" <?php echo $show_opening ? 'checked' : ''; ?>>
      <label class="form-check-label" for="opBalanceCheck">Opening Balance</label>
    </div> 
    <div class="form-check d-inline-block me-2">
      <input class="form-check-input" type="checkbox" value="1" id="ProfitCheck">
      <label class="form-check-label" for="ProfitCheck">Profit</label>
    </div>
    <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
    <ul class="dropdown-menu">
      <li>
        <a data-nil="1" class="dropdown-item addon_nil_balance" href="javascript: void(0)" style="<?php echo $include_nil ? 'display: none;' : ''; ?>">Include Nil Balances</a>
      </li>
      <li>
        <a data-nil="0" class="dropdown-item addon_nil_balance" href="javascript:void(0)" style="<?php echo $include_nil ? '' : 'display:none;'; ?>">Exclude Nil Balances</a>
      </li>
    </ul>
    <a href="<?php echo history_back(); ?>" class="btn btn-sm btn-outline-success float-end ms-2">Back</a>
  </div>
</div>

<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
  
            <div class="col-12 calccard card m-auto">
                <form class="form" method="get" id="salefrm2" autocomplete="off">


                <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime(session()->get('ses_company_fy_beginning'))); ?>
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
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
                <button type="button" class="input-group-text" id="next_year"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">From</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control datepicker" fdprocessedid="cw7ydk" name="fromdate" id="fromdate" value="<?= $from_date ?>" placeholder="dd-mm-yyyy" required>
                    </div>
                </div>
            </div>
            
            <div class="row align-items-center my-2">
                <div class="col-md-2 fw-bold pe-0">To</div>
                <div class="col-md-10">
                    <div class="calc-inputgroup">
                        <input type="text" class="form-control datepicker"  fdprocessedid="b20o4z" name="todate" id="todate" value="<?= $to_date ?>" placeholder="dd-mm-yyyy" required>
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

<div class="row mb-2 align-items-top">
  <div class="col-md-6">
    <div class="d-flex align-items-center gap-2">
      <label for="filter_keyword" class="me-1 mb-0">Filter: </label>
      <input type="text" class="form-control" id="filter_keyword" placeholder="Enter your keyword" style="max-width: 220px;" value="<?php echo htmlspecialchars($_GET['filter_keyword'] ?? ''); ?>"/>
      <select id="filter_field" class="form-select" style="max-width: 140px;">
        <option value="item" <?php echo (($_GET['filter_field'] ?? '') === 'item') ? 'selected' : ''; ?>>Item</option>
        <option value="unit" <?php echo (($_GET['filter_field'] ?? '') === 'unit') ? 'selected' : ''; ?>>Unit</option>
      </select>
      <select id="filter_match" class="form-select" style="max-width: 150px;">
        <option value="contains" <?php echo (($_GET['filter_match'] ?? '') === 'contains') ? 'selected' : ''; ?>>Contains</option>
        <option value="equals" <?php echo (($_GET['filter_match'] ?? '') === 'equals') ? 'selected' : ''; ?>>Equals</option>
        <option value="starts_with" <?php echo (($_GET['filter_match'] ?? '') === 'starts_with') ? 'selected' : ''; ?>>Starts With</option>
      </select>
      <a href="javascript:void(0);" id="filter_go_btn" class="btn btn-sm btn-success">GO</a>
      <a href="javascript:void(0);" id="filter_clear_btn" class="btn btn-sm btn-outline-secondary" style="<?php echo $has_filter ? '' : 'display:none;'; ?>">Clear</a>
    </div>
  </div>
  <div class="col-md-6 text-end">
    <div class="dropdown d-inline-block" style="width: 200px;">
      <div class="input-group">
        <span class="input-group-text">Valuation</span>
        <select form="salefrm" name="val_id" id="val_id" class="form-select" onchange="this.form.submit()">
          <?php foreach ($valuation_list as $key => $value) { ?>
            <option value="<?php echo $value; ?>" <?php echo ($value == $val_id) ? "selected" : ""; ?>>
              <?php echo $value; ?>
            </option>
          <?php } ?>
        </select> 
      </div>  
    </div>
    <div class="dropdown float-end" style="margin-left: 12px;width:220px;">
      <div class="input-group">
        <span class="input-group-text">Sub View</span>
        <select name="subview_type" id="subview_type" class="form-select">
          <option value="item_wise" selected>Item Wise</option>
        </select>
      </div>  
    </div>
  </div>
</div>

<div id="grid_search" style="margin:auto;"></div>

<div id="valuation-footer" class="card mt-3" style="display: none;">
  <div class="card-body py-2">
    <div class="row align-items-center gy-2">
      <div class="col-md-4">
        <div class="d-flex align-items-center flex-wrap">
          <span class="fw-bold me-2">Total Valuation:</span>
          <span class="badge bg-primary fs-2 me-2" id="total-valuation-value">₹ 0.00</span>
          <span class="badge bg-secondary" id="valuation-method">DEFAULT</span>
        </div>
      </div>

      <div class="col-md-4">
        <div class="d-flex align-items-center flex-wrap">
          <span class="fw-bold me-2">Total Opening Value:</span>
          <span class="badge bg-info fs-2 text-dark" id="total-opening-value">₹ 0.00</span>
        </div>
      </div>

      <div class="col-md-4" id="adjusted-valuation-section" style="display:none;">
        <div class="d-flex align-items-center justify-content-md-end flex-wrap">
          <span class="fw-bold me-2">Adjusted Valuation (Filtered):</span>
          <span class="badge bg-success fs-2" id="adjusted-valuation-value">₹ 0.00</span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>
<script>
var latestPageOpeningTotal = 0;
var latestSummary = null;

function updateUrlParam(param, value) {
  var params = new URLSearchParams(window.location.search);
  if (value !== null && value !== '') {
    params.set(param, value);
  } else {
    params.delete(param);
  }
  return window.location.pathname + '?' + params.toString();
}

function setupReloadCheckbox(checkboxId, paramName) {
  $('#' + checkboxId).on('change', function() {
    var isChecked = $(this).is(':checked') ? '1' : '0';
    window.location.href = updateUrlParam(paramName, isChecked);
  });
}

function applyFilter() {
  var params = new URLSearchParams(window.location.search);
  var keywordInput = document.getElementById('filter_keyword');
  var fieldSelect = document.getElementById('filter_field');
  var matchSelect = document.getElementById('filter_match');

  var keyword = keywordInput ? keywordInput.value.trim() : '';
  var field = fieldSelect ? fieldSelect.value : 'item';
  var match = matchSelect ? matchSelect.value : 'contains';

  if (keyword !== '') {
    params.set('filter_keyword', keyword);
  } else {
    params.delete('filter_keyword');
  }
  params.set('filter_field', field);
  params.set('filter_match', match);

  window.location.href = window.location.pathname + '?' + params.toString();
}

function clearFilter() {
  var params = new URLSearchParams(window.location.search);
  params.delete('filter_keyword');
  params.delete('filter_field');
  params.delete('filter_match');
  window.location.href = window.location.pathname + '?' + params.toString();
}

function formatNumberINR(num) {
  return parseFloat(num || 0).toLocaleString('en-IN', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

function parseValue(val) {
  if (val === null || val === undefined || val === '') return 0;
  if (typeof val === 'number') return val;
  val = String(val).replace(/,/g, '').replace(/₹/g, '').trim();
  var num = parseFloat(val);
  return isNaN(num) ? 0 : num;
}

function updateValuationFooter() {
  var footer = document.getElementById('valuation-footer');
  var totalValEl = document.getElementById('total-valuation-value');
  var methodEl = document.getElementById('valuation-method');
  var openingValEl = document.getElementById('total-opening-value');
  var adjustedSection = document.getElementById('adjusted-valuation-section');
  var adjustedValEl = document.getElementById('adjusted-valuation-value');

  if (!latestSummary) {
    footer.style.display = 'none';
    return;
  }

  footer.style.display = 'block';

  totalValEl.textContent = formatAmount(latestSummary.total_valuation || 0, '', 4);
  methodEl.textContent = latestSummary.method || 'DEFAULT';

  var openingValue = (latestSummary.total_opening_valuation !== undefined && latestSummary.total_opening_valuation !== null)
    ? latestSummary.total_opening_valuation
    : latestPageOpeningTotal;

  openingValEl.textContent = formatAmount(openingValue || 0, '', 4);

  if (latestSummary.has_filter && latestSummary.adjusted_valuation !== latestSummary.total_valuation) {
    adjustedSection.style.display = 'block';
    adjustedValEl.textContent = '₹ ' + formatNumberINR(latestSummary.adjusted_valuation);
  } else {
    adjustedSection.style.display = 'none';
  }
}

function refresh_grid() {
  var pq_grids = $('.pq-grid');
  if (pq_grids.length > 0) {
    $.each(pq_grids, function(index, pq_grid) {
      var grid_id = $(pq_grid).attr('id');
      localStorage.removeItem("pq-grid" + grid_id);
      $(pq_grid).pqGrid("reset", { group: true, filter: true, sort: true });
      var CM = $(pq_grid).pqGrid('option', 'colModel');
      for (var i = 0, len = CM.length; i < len; i++) {
        var column = CM[i];
        if (column.filter) {
          column.filter.value = null;
          column.filter.value2 = null;
          column.filter.cache = null;
        }
      }
      $(pq_grid).pqGrid('filter', { oper: 'replace', data: [] });
      $('.filterValue').val('');
      $(pq_grid).pqGrid('refreshHeader');
      $(pq_grid).pqGrid("setSelection", { rowIndx: 0 });
    });
  }
}

$(document).keydown(function(e) {
  if (e.which == 45) {
    $('#masterCreationModal').modal('show');
  }
});

$(document).on('click', '.addon_nil_balance', function() {
  var nil = $(this).data('nil');
  var includeNil = (nil == 1) ? '1' : '0';
  window.location.href = updateUrlParam('include_nil', includeNil);
});

function openview() {
  var type = $("#view_type").val();

  if (type == 'item_wise') {
    var valuation_type = $('select[name="val"]').val();
    window.location.href = "<?php echo $base_url; ?>reports/stock_status?valuation_type=" + valuation_type;
  } else if (type == 'category_wise') {
    window.location.href = "<?php echo $base_url; ?>reports/stock_status_category";
  } else if (type == 'group_wise') {
    window.location.href = "<?php echo $base_url; ?>reports/stock_status_group";
  } else if (type == 'material_center') {
    window.location.href = "<?php echo $base_url; ?>reports/stock_status_mc";
  } else if (type == 'batch_wise') {
    window.location.href = "<?php echo $base_url; ?>reports/stock_status_bw";
  }
}

function calculateSummary() {
  var dm = this.option('dataModel');
  var data = (dm && Array.isArray(dm.data)) ? dm.data : [];

  if (!data.length) {
    latestPageOpeningTotal = 0;
    this.option('summaryData', []);
    updateValuationFooter();
    return;
  }

  var total_op_valuation = 0;
  var total_valuation = 0;
  var total_profit = 0;
  var tt_item_qty_avail = 0;
  var tt_item_value_avail = 0;
  var tt_item_qty_packed = 0;
  var tt_item_value_packed = 0;
  var tt_item_qty_obse = 0;
  var tt_item_value_obse = 0;
  var tt_item_qty_intras = 0;
  var tt_item_value_intras = 0;
  var tt_inward_qty = 0;
  var tt_inward_amount = 0;
  var tt_outward_qty = 0;
  var tt_outward_amount = 0;
  var tt_op_item_qty = 0;

  data.forEach(function(row) {
    total_op_valuation += parseValue(row.op_item_value);
    total_valuation += parseValue(row.item_value);
    total_profit += parseValue(row.profit_raw);
    tt_item_qty_avail += parseValue(row.item_qty_avail);
    tt_item_value_avail += parseValue(row.item_value_avail);
    tt_item_qty_packed += parseValue(row.item_qty_packed);
    tt_item_value_packed += parseValue(row.item_value_packed);
    tt_item_qty_obse += parseValue(row.item_qty_obse);
    tt_item_value_obse += parseValue(row.item_value_obse);
    tt_item_qty_intras += parseValue(row.item_qty_intras);
    tt_item_value_intras += parseValue(row.item_value_intras);
    tt_inward_qty += parseValue(row.inward_qty);
    tt_inward_amount += parseValue(row.inward_amount);
    tt_outward_qty += parseValue(row.outward_qty);
    tt_outward_amount += parseValue(row.outward_amount);
    tt_op_item_qty += parseValue(row.op_item_qty);
  });

  latestPageOpeningTotal = total_op_valuation;

  var totalData = {
    item_name: 'PAGE TOTAL',
    op_item_qty: tt_op_item_qty,
    op_item_value: total_op_valuation,
    item_value: total_valuation,
    item_qty_avail: tt_item_qty_avail,
    item_value_avail: tt_item_value_avail,
    item_qty_packed: tt_item_qty_packed,
    item_value_packed: tt_item_value_packed,
    item_qty_obse: tt_item_qty_obse,
    item_value_obse: tt_item_value_obse,
    item_qty_intras: tt_item_qty_intras,
    item_value_intras: tt_item_value_intras,
    inward_qty: tt_inward_qty,
    inward_amount: tt_inward_amount,
    outward_qty: tt_outward_qty,
    outward_amount: tt_outward_amount,
    profit: total_profit,
    profit_raw: total_profit,
    summaryRow: true,
    pq_rowcls: 'grid_footer_color'
  };

  this.option('summaryData', [totalData]);
  updateValuationFooter();
}

var showOpening = <?php echo $show_opening; ?>;
var showInwards = <?php echo $show_inwards; ?>;
var showOutwards = <?php echo $show_outwards; ?>;

var colModel = [
  { title: "Item", dataIndx: "item_name" },
  { title: "HSN", dataIndx: "Itemhsn", hidden: true },
  { title: "Unit", dataIndx: "unit_name", align: "right" },
  { title: "OPENING", align: 'center', hidden: !showOpening, colModel: [
    { title: "OP. Qty", dataIndx: "op_item_qty", align: "right", hidden: !showOpening,
      render: function(ui) {
        var rd = ui.rowData;
        if (rd.summaryRow) return formatQty(rd.op_item_qty);
        if (rd.op_item_qty != -1) return formatQty(rd.op_item_qty);
        return "";
      }
    },
    { title: "Value", dataIndx: "op_item_value", align: "right", hidden: !showOpening,
      render: function(ui) {
        var rd = ui.rowData;
        return formatValue(rd.op_item_value);
      }
    }
  ]},
  { title: "INWARDS", align: 'center', hidden: !showInwards, colModel: [
    { title: "Qty", dataIndx: "inward_qty", align: "right", hidden: !showInwards,
      render: function(ui) {
        var rd = ui.rowData;
        if (rd.summaryRow) return formatQty(rd.inward_qty);
        if (rd.inward_qty != -1) return formatQty(rd.inward_qty);
        return "";
      }
    },
    { title: "Amount", dataIndx: "inward_amount", align: "right", hidden: !showInwards,
      render: function(ui) {
        var rd = ui.rowData;
        return formatAmount(rd.inward_amount,'',2);
      }
    }
  ]},
  { title: "OUTWARDS", align: 'center', hidden: !showOutwards, colModel: [
    { title: "Qty", dataIndx: "outward_qty", align: "right", hidden: !showOutwards,
      render: function(ui) {
        var rd = ui.rowData;
        if (rd.summaryRow) return formatQty(rd.outward_qty);
        if (rd.outward_qty != -1) return formatQty(rd.outward_qty);
        return "";
      }
    },
    { title: "Amount", dataIndx: "outward_amount", align: "right", hidden: !showOutwards,
      render: function(ui) {
        var rd = ui.rowData;
        return formatAmount(rd.outward_amount,'',2);
      }
    }
  ]},
  { title: "AVAILABLE", align: 'center', hidden: false, colModel: [
    { title: "CL. Qty", dataIndx: "item_qty_avail", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        if (rd.summaryRow) return formatQty(rd.item_qty_avail);
        if (rd.item_qty_avail != -1) return formatQty(rd.item_qty_avail);
        return "";
      }
    },
    { title: "Value", dataIndx: "item_value_avail", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        return formatValue(rd.item_value_avail);
      }
    }
  ]},
  { title: "PACKED", align: 'center', hidden: true, colModel: [
    { title: "CL. Qty", dataIndx: "item_qty_packed", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        if (rd.summaryRow) return formatQty(rd.item_qty_packed);
        if (rd.item_qty_packed != -1) return formatQty(rd.item_qty_packed);
        return "";
      }
    },
    { title: "Value", dataIndx: "item_value_packed", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        return formatValue(rd.item_value_packed);
      }
    }
  ]},
  { title: "OBSOLETE", align: 'center', hidden: true, colModel: [
    { title: "CL. Qty", dataIndx: "item_qty_obse", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        if (rd.summaryRow) return formatQty(rd.item_qty_obse);
        if (rd.item_qty_obse != -1) return formatQty(rd.item_qty_obse);
        return "";
      }
    },
    { title: "Value", dataIndx: "item_value_obse", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        return formatValue(rd.item_value_obse);
      }
    }
  ]},
  { title: "IN TRANSIT", align: 'center', hidden: true, colModel: [
    { title: "CL. Qty", dataIndx: "item_qty_intras", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        if (rd.summaryRow) return formatQty(rd.item_qty_intras);
        if (rd.item_qty_intras != -1) return formatQty(rd.item_qty_intras);
        return "";
      }
    },
    { title: "Value", dataIndx: "item_value_intras", align: "right",
      render: function(ui) {
        var rd = ui.rowData;
        return formatValue(rd.item_value_intras);
      }
    }
  ]},
  { title: "Method", dataIndx: "method" },
  { title: "Profit", dataIndx: "profit", width: 120, hidden: true, align: "right",
    render: function(ui) {
      var rd = ui.rowData;
      return formatAmount(rd.profit_raw,'',2);
    }
  }
];

var pageModel = { type: "remote", rPP: 10, strRpp: "{0}" };

var dataModel = {
  location: "remote",
  dataType: "JSON",
  method: "POST",
  postData: {
    mc_id: "<?php echo $mc_id; ?>",
    val_id: "<?php echo $val_id; ?>",
    from_date: "<?php echo $from_date; ?>",
    to_date: "<?php echo $to_date; ?>",
    to_date_ymd: "<?php echo $to_date_ymd; ?>",
    profit_tag: 0,
    nill: <?php echo $include_nil; ?>,
    type: 'balance',
    filter_text: "<?php echo htmlspecialchars($_GET['filter_keyword'] ?? ''); ?>",
    filter_field: "<?php echo htmlspecialchars($_GET['filter_field'] ?? ''); ?>",
    filter_op: "<?php echo htmlspecialchars($_GET['filter_match'] ?? ''); ?>",
    show_opening: <?php echo $show_opening; ?>,
    show_inwards: <?php echo $show_inwards; ?>,
    show_outwards: <?php echo $show_outwards; ?>
  },
  url: "<?php echo base_url(); ?>admin/reports/load_stock_status",
  getData: function(dataJSON) {
    latestSummary = dataJSON.valuationSummary || null;
    return {
      curPage: dataJSON.curPage,
      totalRecords: dataJSON.totalRecords,
      data: dataJSON.data
    };
  }
};

var $grid;

var newObj = {
  scrollModel: { autoFit: true },
  height: 'flex',
  resizable: true,
  autoResize: true,
  columnAutoWidth: true,
  autoFit: true,
  collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
  selectionModel: { type: 'row', mode: 'single' },
  pageModel: pageModel,
  dataModel: dataModel,
  colModel: colModel,
  editable: false,
  showBottom: true,
  summaryData: [],
  dataReady: calculateSummary,
  numberCell: { show: true },
  filterModel: { on: true, mode: "OR", header: true, type: 'local' },
  showTitle: false,
  create: function(evt, ui) {
    this.setSelection({ rowIndx: 0, focus: true });
  }
};

newObj.cellDblClick = function(event, ui) {
            var rowData    = ui.rowData;
			console.log(rowData);
			var item_id =rowData.item_id;
			var unit_id =rowData.unit_id;
			var mc_grp_id='';var mc_id='';
			
           window.location.href= baseurl+'admin/items/ledger_detail/'+item_id+'?mc_id='+mc_id+'&mc_grp_id='+mc_grp_id+'&unit_id='+unit_id+'&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>';
             
            
	   }  

$grid = $("#grid_search").pqGrid(newObj);

function toggleColumnVisibility(checkboxId, parentTitle, childDataIndxArray) {
  $('#' + checkboxId).on('change', function() {
    var isChecked = $(this).is(':checked');
    var CM = $grid.pqGrid("option", "colModel");

    for (var i = 0; i < CM.length; i++) {
      var col = CM[i];

      if (parentTitle && col.title === parentTitle) {
        col.hidden = !isChecked;
        if (col.colModel && col.colModel.length > 0) {
          for (var j = 0; j < col.colModel.length; j++) {
            col.colModel[j].hidden = !isChecked;
          }
        }
        break;
      }

      if (childDataIndxArray && childDataIndxArray.length > 0) {
        if (childDataIndxArray.indexOf(col.dataIndx) !== -1) {
          col.hidden = !isChecked;
        }
      }
    }

    $grid.pqGrid("option", "colModel", CM);
    $grid.pqGrid("refreshCM");
    $grid.pqGrid("refresh");
  });
}

$(document).ready(function() {
  toggleColumnVisibility("itemhsnCheck", null, ["Itemhsn"]);
  toggleColumnVisibility("ProfitCheck", null, ["profit"]);

  setupReloadCheckbox("opBalanceCheck", "show_opening");
  setupReloadCheckbox("InwardsCheck", "show_inwards");
  setupReloadCheckbox("OutwardsCheck", "show_outwards");

  $('#fg').on('change', function() {
    if ($(this).is(":checked")) {
      $grid.pqGrid('option', 'height', 420);
    } else {
      $grid.pqGrid('option', 'height', 'flex');
    }
    $grid.pqGrid('refresh');
  });

  $('#refresh_grid').on('click', function() {
    refresh_grid();
  });

  $('#filter_go_btn').on('click', function() {
    applyFilter();
  });

  $('#filter_clear_btn').on('click', function() {
    clearFilter();
  });

  $('#filter_keyword').on('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      applyFilter();
    }
  });
});

function print_excel() {
  var from_date = $('#from_date').val();
  var to_date = $('#to_date').val();
  var InwardsCheck = $('#InwardsCheck').is(':checked') ? $('#InwardsCheck').val() : 0;
  var OutwardsCheck = $('#OutwardsCheck').is(':checked') ? $('#OutwardsCheck').val() : 0;
  var itemhsnCheck = $('#itemhsnCheck').is(':checked') ? $('#itemhsnCheck').val() : 0;
  var opBalanceCheck = $('#opBalanceCheck').is(':checked') ? $('#opBalanceCheck').val() : 0;
  var ProfitCheck = $('#ProfitCheck').is(':checked') ? $('#ProfitCheck').val() : 0;
  var stringparameters = "inw=" + InwardsCheck + "&outw=" + OutwardsCheck + "&hsn=" + itemhsnCheck + "&opbal=" + opBalanceCheck + "&prft=" + ProfitCheck + "&from_date=" + from_date + "&to_date=" + to_date;
  window.location.href = baseurl + "admin/export/stock_status?" + stringparameters;
}
</script>
</body>
</html>