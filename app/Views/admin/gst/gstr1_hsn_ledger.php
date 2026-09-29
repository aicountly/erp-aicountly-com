<?php $header = ['title' => 'GSTR HSN Summary Report']; ?>
<?php echo view('includes/header', $header); ?>
<style>
. myform . col-12{padding:6px 0px;}
.myform label{width:25%; float:left;}
.myform . form-control, .myform select {width:75%;}
.ui-autocomplete { z-index:9999! important; }
. pq-sb-horiz-t . pq-sb-slider, .pq-sb-vert-t .pq-sb-slider,
.pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{
    background-color: rgb(220, 254, 211) !important;
}
</style>

<div class="row mb-md-0 mb-3">
  <div class="col-md-6">
    <h3 class="pb-3">GSTR HSN Summary Report<?php echo isset($show_table_no) && $show_table_no ?  ' - ' . $show_table_no : ''; ?></h3>
  </div>
  <div class="col-md-6 text-end">
    <div class="taskmenus">
      <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
      <a href="#"><span class="material-symbols-outlined">print</span></a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
        <li><a class="dropdown-item" href="javascript: void(0)" onclick="print_pdf();">Document</a></li>
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
      <div class="form-check form-check-inline me-2">
        <input class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
        <label class="form-check-label" for="fg">Fixed Grid</label>
      </div>
      <input type="hidden" name="fromdate" value="<?php echo date('Y-m-d', strtotime($from_date)); ?>">
      <input type="hidden" name="todate" value="<?php echo date('Y-m-d', strtotime($to_date)); ?>">
    </form>
  </div>
  <div class="col-lg-7 text-end">
    <div class="dropdown float-end">
      <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Voucher Series </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
      </ul>
      <a href="javascript:void(0);" onclick="history.back()" class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <?php $filter_labels = date('M, Y', strtotime($from_date)) . ' To ' . date('M, Y', strtotime($to_date)); ?>
    <p><em>For:  <strong><?php echo $filter_labels; ?></strong></em></p>
  </div>
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin: auto;"></div>

<?php echo view('includes/footer_scripts'); ?>
<script>
var pageview = '<?php echo $view ??  1; ?>';
var summary_view_page = '<?php echo $summary_view ??  1; ?>';
var gridInitialized = false;
var filterTimeout = null;

// Define formatQty function (was missing - caused the error)
function formatQty(n) {
    var num = parseFloat(n) || 0;
    return num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

$(function () {
    function calculateSummary() {
        var data = $("#grid_search").pqGrid("pageData") || [],
            invoiceTotal = 0, taxableTotal = 0,
            igstTotal = 0, cgstTotal = 0, sgstTotal = 0,
            cessTotal = 0, taxTotal = 0, qtyTotal = 0;

        data.forEach(function(row) {
            invoiceTotal += parseAmount(row.sm_invoice_value);
            taxableTotal += parseAmount(row.sm_taxable_value);
            igstTotal    += parseAmount(row.sm_igst);
            cgstTotal    += parseAmount(row.sm_cgst);
            sgstTotal    += parseAmount(row.sm_sgst);
            cessTotal    += parseAmount(row.sm_cess);
            taxTotal     += parseAmount(row.sm_total_tax);
            qtyTotal     += parseFloat(row.sm_qty || 0);
        });

        var totalData = {
            hsn_sac:  'TOTAL',
            date: '',
            party:  '',
            qty: formatQty(qtyTotal),
            invoice_value: formatAmount(invoiceTotal),
            taxable_value: formatAmount(taxableTotal),
            igst: formatAmount(igstTotal),
            cgst: formatAmount(cgstTotal),
            sgst: formatAmount(sgstTotal),
            cess: formatAmount(cessTotal),
            total_tax: formatAmount(taxTotal),
            pq_rowcls: 'grid_footer_color',
            summaryRow: true
        };
        this.option('summaryData', [totalData]);
    }

    function filterhandler(evt, ui) {
        if (filterTimeout) clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(". filterValue"),
                value = $value.val(),
                filterObject = [],
                CM = $grid.pqGrid("getColModel");
            for (var i = 0, len = CM.length; i < len; i++) {
                filterObject.push({ dataIndx: CM[i].dataIndx, condition: '', value: value });
            }
            $grid.pqGrid("filter", { oper: 'replace', data: filterObject });
        }, 400);
    }

    var colModel = [
        { title: "HSN/SAC",      width: 150, dataIndx:  "hsn_sac", sortable: false },
        { title: "DATE",         width: 100, dataIndx: "date", sortable: false },
        { title: "PARTY",        width: 180, dataIndx: "party", sortable: false },
        { title: "QTY",          width: 80,  dataIndx: "qty", align: "right", sortable: false },
        { title: "INVOICE VALUE",width: 120, dataIndx: "invoice_value", align:  "right", sortable: false },
        { title: "TAXABLE VALUE",width: 120, dataIndx: "taxable_value", align: "right", sortable: false },
        { title: "IGST",         width: 100, dataIndx: "igst", align: "right", sortable: false },
        { title:  "CGST",         width:  100, dataIndx: "cgst", align: "right", sortable: false },
        { title: "SGST/UTGST",   width: 110, dataIndx: "sgst", align: "right", sortable: false },
        { title:  "CESS",         width:  90,  dataIndx: "cess", align: "right", sortable: false },
        { title: "TOTAL TAX",    width: 120, dataIndx: "total_tax", align:  "right", sortable: false }
    ];

    var minWidth = 'flex';
    var scrollModel = { autoFit: true };

    var dataModel = {
        location: "remote",
        dataType: "json",
        method: "POST",
        postData: {
            "to_date": "<?php echo $to_date; ?>",
            "from_date": "<?php echo $from_date; ?>",
            "tablekey": "<?php echo $tableno_key; ?>"
        },
        url: "<?php echo base_url(); ?>/admin/gst/ajax_hsn_summary_list",
        getData: function(dataJSON) {
            var data = dataJSON. data || [];
            return {
                curPage: dataJSON.curPage || 1,
                totalRecords: dataJSON.totalRecords || 0,
                data: data
            };
        }
    };

    var newObj = {
        scrollModel: scrollModel,
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
        height: "flex",
        minWidth: minWidth,
        selectionModel: { type: 'row', mode: 'single' },
        dataModel: dataModel,
        dataReady: calculateSummary,
        colModel: colModel,
        wrap: false,
        numberCell: { show: true },
        filterModel: { mode: 'OR', type: "local" },
        pageModel: { type: "remote", rPP: 100, strRpp: "{0}" },
        editable: false,
        showTitle: false,
        create: function(evt, ui) {
            var grid = this;
            if (! gridInitialized) {
                gridInitialized = true;
                const url = new URL(window.location.href);
                if (url.searchParams.has('rowIndx')) {
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);
                    grid.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                } else {
                    grid.setSelection({ rowIndx: 0, focus: true });
                }
            }
        },
        toolbar: {
            cls: "pq-toolbar-search",
            items: [
                {
                    type: 'textbox',
                    label: 'Filter:  ',
                    attr: 'placeholder="Enter your keyword"',
                    cls: "filterValue",
                    listener: { keyup: filterhandler }
                }
            ]
        }
    };
	
	newObj.rowDblClick = function(event, ui) {
        var rowData         = ui.rowData;
        var txn_bo_id       = rowData.bo_id;
        var voucher_txn_id  = rowData.voucher_txn_id;
        var voucher_type_id = rowData.voucher_type_id;
        
        if (txn_bo_id == '<?php echo $bo_id; ?>') {
            var select_rowindx = set_page();
            edit_voucher(voucher_txn_id, voucher_type_id, select_rowindx);
        } else {
            alert_notification("You can edit from branch!! !");
            return false;
        }
    };
     
    newObj.cellKeyDown = function(evt, ui) {           
        var rowData = ui.rowData;
        var txn_bo_id = rowData.bo_id;
        var voucher_txn_id = rowData.voucher_txn_id;
        var voucher_type_id = rowData. voucher_type_id;
        
        if (evt.keyCode == 13) {                 
            if (txn_bo_id == '<?php echo $bo_id; ?>') {
                var select_rowindx = set_page();
                edit_voucher(voucher_txn_id, voucher_type_id, select_rowindx); 
            } else {
                alert_notification("You can edit from branch!!!");
                return false;
            }
        }
    };

    var $grid = $("#grid_search").pqGrid(newObj);

    $("#refresh_grid").on("click", function() {
        $("#grid_search").pqGrid('refreshDataAndView');
    });

function set_page() {
    var select_row = $("#grid_search").pqGrid("selection", { type: 'row', method: 'getSelection' });
    if (select_row && select_row.length > 0) {
        const url = new URL(window.location. href);
        url.searchParams.set('rowIndx', select_row[0]. rowIndx);
        window.history.replaceState(null, null, url);
        return select_row[0].rowIndx;
    } else {
        const url = new URL(window.location.href);
        if (url.searchParams.has('rowIndx')) {
            url.searchParams.delete('rowIndx');
            window.history.replaceState(null, null, url);     
        }
        return 0;
    } 
}

    $(document).on('change', '#fg', function() {
        if ($(this).is(":checked")) {
            $("#grid_search").pqGrid('option', 'height', 420);
        } else {
            $("#grid_search").pqGrid('option', 'height', 'flex');
        }
        $("#grid_search").pqGrid('refreshDataAndView');
    });
});
</script>
<style>. hidden{display:none;}</style>
</body>
</html>