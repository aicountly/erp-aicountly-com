<?php $header = array('title' => 'GSTR Ledger Report'); ?>
<?php echo view('includes/header', $header); ?>
<style>
. myform .col-12{padding:6px 0px;}
.myform label{width:25%; float:left;}
.myform .form-control, .myform select {width:75%;}
</style>
<style>
/*for autocomplete */
.ui-autocomplete {
    z-index:9999! important;
}
. pq-sb-horiz-t . pq-sb-slider, .pq-sb-vert-t .pq-sb-slider, .pq-sb-horiz-t .pq-sb-btn, .pq-sb-vert-t .pq-sb-btn{background-color:  rgb(220, 254, 211) !important;}
</style>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">GSTR Ledger Report <?php echo isset($show_table_no) ? '- ' . $show_table_no : ''; ?></h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript: void(0)" id="refresh_grid" onclick="$('#grid_search').pqGrid('refreshDataAndView');"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined open-comingsoon">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a></li>
            <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a></li>
            <li><a class="dropdown-item" href="javascript: void(0)" onclick="print_pdf();">Document</a></li>
        </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
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
        <div class="dropdown d-inline-block me-1" style="width:220px;">
        </div>
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
      <p class="text-center pt-3"><a data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a></p>
        </div>
      </div>
  </div>
</div>

<div class="row">
    <div class="col-md-6">
        <p><em>For:  <strong><?php echo $show_date; ?></strong></em></p>
    </div>
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin: auto;"> </div> 
<div class="col-12 text-center">
    <br><br>
</div>

<?php echo view('includes/footer_scripts'); ?>
<script>
var pageview = '<?php echo $view; ?>';
var gridInitialized = false;

$(function () {
    
    function calculateSummary() { 
        var grid = this;

        // Only set selection on first load
        if (!gridInitialized) {
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

        // Calculate summary from page wise data  
        var invoiceTotal = 0,
            taxableTotal = 0,
            igstTotal = 0,
            cgstTotal = 0,
            sgstTotal = 0,
            cessTotal = 0,
            taxTotal = 0,
            data = $("#grid_search").pqGrid("pageData"),
            len = data.length;
            
        data.forEach(function(row) {             
            invoiceTotal += parseAmount(row.sm_invoice_value);
            taxableTotal += parseAmount(row.sm_taxable_value);
            igstTotal += parseAmount(row.sm_igst);
            cgstTotal += parseAmount(row.sm_cgst);
            sgstTotal += parseAmount(row.sm_sgst);
            cessTotal += parseAmount(row.sm_cess);
            taxTotal += parseAmount(row.sm_total_tax);
        });

        var totalData = {
            date: "Total",
            party: "",
            pos: "",
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

    // Debounce function to prevent multiple rapid calls
    var filterTimeout = null;
    function filterhandler(evt, ui) {
        // Clear previous timeout
        if (filterTimeout) {
            clearTimeout(filterTimeout);
        }
        
        // Set new timeout - wait 500ms before filtering
        filterTimeout = setTimeout(function() {
            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(". filterValue"),
                value = $value.val(),
                condition = '',
                dataIndx = '',
                filterObject;

            if (dataIndx == "") {
                filterObject = [];
                var CM = $grid.pqGrid("getColModel");
                for (var i = 0, len = CM.length; i < len; i++) {
                    var dataIndx = CM[i].dataIndx;
                    filterObject.push({ dataIndx: dataIndx, condition: condition, value: value });
                }
            } else {
                filterObject = [{ dataIndx: dataIndx, condition: condition, value: value }];
            }
            $grid.pqGrid("filter", {
                oper: 'replace',
                data: filterObject
            });
        }, 500);
    }

    var colModel = [
        { title: "DATE", dataIndx: "date", width:  120 },
        { title: "PARTY", width: 200, dataIndx: "party" },  
         { title: "GSTIN", width: 100, dataIndx: "gstin" },  		
        { title: "POS", width: 150, dataIndx: "pos" },             
        { title: "INVOICE VALUE", width: 130, align: "right", dataIndx:  "invoice_value" },
        { title: "TAXABLE VALUE", width: 130, align:  "right", dataIndx: "taxable_value" },  
        { title: "IGST", width: 100, align: "right", dataIndx: "igst" },
        { title: "CGST", width: 100, align:  "right", dataIndx: "cgst" },
        { title: "SGST", width:  100, align: "right", dataIndx: "sgst" },
        { title: "CESS", width: 100, align: "right", dataIndx: "cess" },
        { title: "TOTAL TAX", width: 120, align: "right", dataIndx: "total_tax" }
    ];
  
    var minWidth = 'flex'; 
    var scrollModel = { autoFit: true };
    
    var dataModel = {
        location: "remote",
        dataType: "json",
        method: "POST",
        postData: {
            "from_date": "<?php echo $from_date; ?>",
            "to_date": "<?php echo $to_date; ?>",
            "tableno_key": "<?php echo $tableno_key; ?>",
            "tableno":  "<?php echo $tableno; ?>"
        },
        url: "<?php echo base_url(); ?>admin/gst/ajax_gstr_transactions",
        getData: function (dataJSON) {
            var data = dataJSON.data || [];
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
        pageModel: { type: "remote", rPP: 20, strRpp: "{0}" },
        dataModel: dataModel,
        dataReady: calculateSummary,
        colModel: colModel,  
        wrap: false,
        numberCell: { show: true },
        filterModel: { mode: 'OR', type: "local" },          
        editable: false,
        showTitle: false,
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
</script>
<style>. hidden{display:none;}</style>
</body>
</html>