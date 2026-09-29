<?php $header = array( 	'title' => 'Item Summary' ); ?>
<?php echo view('includes/header',$header); ?>
<div class="row mb-2">
  <div class="col-sm-6"><h3>Item Summary</h3>
   <p><em>Item: <strong><?php echo $item_name;?></strong></em></p>
  </div>
  <div class="col-sm-6 text-end">
  <div class="taskmenus">
    <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined ">offline_bolt</span></a> 
    <?php 
    $params = http_build_query([
      'item_id'    => $item_id,
      'unit_id'    => $unit_id,
      'mc_id'      => $mc_id,
      'mc_grp_id'  => $mc_grp_id,
      'invtp_id'   => $invtp_id,
      'val_id'     => $val_id,
      'to_date'    => $to_date,
      ]);
    ?>
    <a href="<?= base_url() ?>/admin/export/item_summary_print?<?= $params ?>"><span class="material-symbols-outlined ">print</span></a>
  <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel('csv');">CSV</a></li>
                <li><a class="dropdown-item" href="javascript:void(0)" onclick="print_excel('excel');">Excel</a></li>
                <li><a class="dropdown-item" href="#" >Document</a></li>
            </ul>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class=""><span class="material-symbols-outlined open-comingsoon">share</span></a> 
      <ul class="dropdown-menu" style="">
    <li><a class="dropdown-item" href="#">Facebook</a></li>
    <li><a class="dropdown-item" href="#">Twitter</a></li>
    <li><a class="dropdown-item" href="#">Instagram</a></li>
  </ul>
  <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success ms-3" style="margin-top:-20px; position:relative;">Back</a>

<div class="text-end pe-3">
       
      <button type="button" class="btn btn-success btn-sm" id="totalopeningbtn">
          Opening Balance
      </button>
   
</div>
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

                <?php foreach ($valuation_list as  $value) {  ?>
                    <option value="<?= $value ?>" <?= ($value==$val_id)?"selected":"";?> ><?= $value ?></option>
                <?php } ?>
                 
               </select>
          </div>  
        </div>
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



</div>           

<br>
<div id="grid_search" style="margin:auto;"> </div>
<br>
<div class="row">
    <div class="col-md-12 text-end py-3">
 
	  <button id="totalclosingbtn" type="button" class="btn btn-success btn-sm">
          Closing Balance
      </button>


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
                        <th style="text-align: center;">Material Center</th>
                        <th style="text-align: center;">Unit</th>
                        <th style="text-align: center;">Quantity</th>
                        <th style="text-align: center;">Value</th>
                        <th style="text-align: center;">Method</th>
                    </tr>
                </thead>
                <tbody>
                    
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
                        <th style="text-align: center;">Value</th>
                        <th style="text-align: center;">Method</th>
                    </tr>
                </thead>
                <tbody>
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
function refresh_grid(){
	   var pq_grids = $('.pq-grid'); 
      if(pq_grids.length > 0){
        $.each(pq_grids, function(index, pq_grid){

          var grid_id = $(pq_grid).attr('id');
          localStorage.removeItem("pq-grid"+grid_id);
   
          $(pq_grid).pqGrid( "reset", { group: true, filter: true, sort: true } );

            var CM = $(pq_grid).pqGrid('option', 'colModel');
            for(var i=0, len = CM.length; i < len; i++){
                var column = CM[i];
                if(column.filter){
                    column.filter.value = null;
                    column.filter.value2 = null;
                    column.filter.cache = null;
                }
            }
            
            $(pq_grid).pqGrid('filter', {
              oper: 'replace',
              data: []
            });
            $('.filterValue').val('');
            $(pq_grid).pqGrid('refreshHeader');

            $(pq_grid).pqGrid( "setSelection", { rowIndx: 0 });

        })
      }
}

var item_id    ='<?php echo $item_id;?>';
var mc_id   ='<?php echo $mc_id;?>';
var unit_id   ='<?php echo $unit_id;?>';

var from_date   ='<?php echo $from_date;?>';
var to_date   ='<?php echo $to_date;?>';;

/************** Start Of  Show Total Opening Button   ***************************/
$(document).on("click","#totalopeningbtn",function(){
	show_loader();
	var valuation_method = $("#val").val();
	fetchItemOpeningTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method);
}); 

async function fetchItemOpeningTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method) {
    try {
        const response = await fetch(`<?php echo base_url();?>admin/items/get_item_opening_totals/${item_id}?from_date=${from_date}&to_date=${to_date}&mc_id=${mc_id}&unit_id=${unit_id}&val_id=${valuation_method}`);
        const result = await response.json();
		stop_loader();
        if (result.status === 'success') {
            populateOpeningModal(result.data);
            $('#openingBalanceModel').modal('show');  // Show the modal
        } else {
            console.error('Failed to fetch summary totals');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}
function populateOpeningModal(data) {
    let tbody = $('#openingBalanceModel tbody');
    tbody.empty();  // Clear existing rows

    // Group data by mat_cent_name
    let groupedData = data.reduce((acc, row) => {
        if (!acc[row.mat_cent_name]) {
            acc[row.mat_cent_name] = [];
        }
        acc[row.mat_cent_name].push(row);
        return acc;
    }, {});

    // Iterate grouped data and build rows
    for (const [matCentName, rows] of Object.entries(groupedData)) {
        rows.forEach((row, index) => {
            let tr = '<tr>';
            
            // Add MC name with rowspan only on first unit of each MC
            if (index === 0) {
                tr += `<td style="text-align: center;" rowspan="${rows.length}">${matCentName}</td>`;
            }
            tr += `
                <td style="text-align: center;">${row.unit_name}</td>
                <td style="text-align: right;">${parseAmount(row.opening_qty)}</td>
                <td style="text-align: right;">${formatAmount(row.opening_valuation_value)}</td>
                <td style="text-align: right;">${row.valuation_method_name}</td>
            </tr>`;

            tbody.append(tr);
        });
    }
}
/**************  End Of  Show Opening Balance Button   ***************************/

/************** Start Of  Show Total Closing Button   ***************************/
$(document).on("click","#totalclosingbtn",function(){
	show_loader();
	var valuation_method = $("#val").val();
	fetchItemClosingTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method);
}); 

async function fetchItemClosingTotals(item_id,from_date, to_date, mc_id, unit_id,valuation_method) {
    try {
        const response = await fetch(`<?php echo base_url();?>admin/items/get_item_closing_totals/${item_id}?from_date=${from_date}&to_date=${to_date}&mc_id=${mc_id}&unit_id=${unit_id}&val_id=${valuation_method}`);
        const result = await response.json();
		stop_loader();
        if (result.status === 'success') {
            populateClosingModal(result.data);
            $('#closingBalanceModel').modal('show');  // Show the modal
        } else {
            console.error('Failed to fetch summary totals');
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}
function populateClosingModal(data) {
    let tbody = $('#closingBalanceModel tbody');
    tbody.empty();  // Clear existing rows

    // Group data by mat_cent_name
    let groupedData = data.reduce((acc, row) => {
        if (!acc[row.mat_cent_name]) {
            acc[row.mat_cent_name] = [];
        }
        acc[row.mat_cent_name].push(row);
        return acc;
    }, {});

    // Iterate grouped data and build rows
    for (const [matCentName, rows] of Object.entries(groupedData)) {
        rows.forEach((row, index) => {
            let tr = '<tr>';
            
            // Add MC name with rowspan only on first unit of each MC
            if (index === 0) {
                tr += `<td style="text-align: center;" rowspan="${rows.length}">${matCentName}</td>`;
            }
            tr += `
                <td style="text-align: center;">${row.unit_name}</td>
                <td style="text-align: right;">${parseAmount(row.closing_qty)}</td>
                <td style="text-align: right;">${formatAmount(row.closing_value)}</td>
                <td style="text-align: right;">${row.method_name}</td>
            </tr>`;

            tbody.append(tr);
        });
    }
}
/**************  End Of  Show Closing Balance Button   ***************************/

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
        
		function calculateSummary() { 		 
               
         var debitQtyTotoal = 0,
            debitAmountTotoal = 0,
            creditQtyTotoal = 0,
            creditAmountTotoal = 0,
            balanceQtyTotoal = 0,
            balanceAmountTotoal = 0,
            pnlTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;
			
        data.forEach(function(row){            
            debitQtyTotoal += parseQty(row.debit_qty);
            debitAmountTotoal += parseAmount(row.debit_amount);
            creditQtyTotoal += parseQty(row.credit_qty);
            creditAmountTotoal += parseAmount(row.credit_amount);
            balanceQtyTotoal += parseQty(row.balance_qty);
            balanceAmountTotoal += parseValue(row.balance_amount);
            pnlTotal += parseAmount(row.profit);
           
        })
        var totalData = {

                month: "Total",
                debit_qty: debitQtyTotoal,
                debit_amount: debitAmountTotoal,
                credit_qty : creditQtyTotoal,
                credit_amount: creditAmountTotoal,
                balance_qty: balanceQtyTotoal,
                balance_amount : balanceAmountTotoal,
                profit : pnlTotal,
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
          this.option('summaryData', [totalData]);
    }
	
var colModel = [
    { title: "Month (<?= fy_calender()->name ?>)", align:"left",   dataIndx: "month" },
    { title: "UOM", align:"left",   dataIndx: "unit_name" },

    { title: "Debit",  align: "center", colModel: [
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
    { title: "Credit",  align: "center", colModel: [
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
    { title: "Balance",  align: "center", colModel: [
    { title: "Qty", align: "right",dataIndx: "balance_qty" ,
        render: function( ui ) {
            var rd = ui.rowData;
            return rd.balance_qty;   

        },}, 
    { title: "Value", align: "right",dataIndx: "balance_value",
        render: function( ui ) {
            var rd = ui.rowData;
            return formatValue(rd.balance_value);   

        },
    },
    ] },

   /*  { title: "Profit &nbsp;&nbsp;", align:"right",    dataIndx: "profit",
        render: function( ui ) {
            var rd = ui.rowData;
            return formatAmount(rd.profit) + '&nbsp;&nbsp;';   

        },
    }, */
];
	    	
			
        var dataModel = {"data":<?php echo json_encode($summary['rows']);?>}
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
			resizable: true,
           autoResize: true,
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
			dataReady: calculateSummary,
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
  	           var rowData    = ui.rowData;
  	           
		       var item_id     = rowData.item_id;
		       var from_date   = rowData.from_date;
		       var to_date     = rowData.to_date;
			   var unit_id     = rowData.unit_id;
               var mc_id       = rowData.mc_id;
               var mc_grp_id   = rowData.mc_grp_id;
               var val      = $('select[name="val"]').val();

		    window.location.href= baseurl+'admin/items/ledger_detail/'+item_id+'?from_date='+from_date+'&to_date='+to_date+'&unit_id='+unit_id+'&mc_grp_id='+mc_grp_id+'&mc_id='+mc_id+'&val='+val;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		    
		       var item_id     = rowData.item_id;
               var from_date  = rowData.from_date;
               var to_date    = rowData.to_date;
               var unit_id      = rowData.unit_id;
               var mc_id      = rowData.mc_id;
               var mc_grp_id      = rowData.mc_grp_id;
               var val      = $('select[name="val"]').val();

		       if (evt.keyCode==13){
	             window.location.href= baseurl+'admin/items/ledger_detail/'+item_id+'?from_date='+from_date+'&to_date='+to_date+'&unit_id='+unit_id+'&mc_grp_id='+mc_grp_id+'&mc_id='+mc_id+'&val='+val;
		       }
		   
	       }
	     
        var $grid = $("#grid_search").pqGrid(newObj);
        pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });

  function print_excel(){		
			
            var stringparameters = "item_id=<?php echo $item_id;?>&invtp_id=<?php echo $invtp_id;?>&unit_id=<?php echo $unit_id;?>&mc_grp_id=<?php echo $mc_grp_id;?>&mc_id=<?php echo $mc_id;?>";
             window.location.href= baseurl+"admin/export/item_summary?"+stringparameters;
          
        }
</script>
 </body>
</html>
