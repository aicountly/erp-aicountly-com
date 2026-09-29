<?php $header = array(  'title' => 'Stock Status' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
  
  <div class="row pb-2">
    <div class="col-sm-6"><h3>Stock Status(Item Wise)</h3></div>  
        <div class="col-sm-6 text-end"><div class="taskmenus">

            <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
            <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

          <a href="#"><span class="material-symbols-outlined">print</span></a>
          <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
            <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= base_url('admin/export/stock_status?export_type=csv&mc_id='.$mc_id.'&val='.$val) ?>">CSV</a></li>
            <li><a class="dropdown-item" href="<?= base_url('admin/export/stock_status?export_type=excel&mc_id='.$mc_id.'&val='.$val) ?>">Excel</a></li>
            <li><a class="dropdown-item" href="#">Document</a></li>
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
    <div class="col-md-6">
        <form class="form needs-validation" method="get" id="salefrm"  novalidate>

        <div class="input-group input-group-sm">
            <span class="input-group-text px-1">From</span>
            <input type="text" name="from_date" value="<?php echo $from_date;?>" class="datepicker form-control" required  style="width:90px;">
            
            <span class="input-group-text px-1">To</span>
            <input type="text" name="to_date" value="<?php echo $to_date;?>" class="datepicker form-control" required style="width:90px;">
            
            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
             <span class="material-symbols-outlined">event</span>
            </button>
            
            <input type="submit" id="gofilter" class="btn btn-sm btn-success" value="GO">
        </div>
        
        </form>
    
    </div>    
    <div class="col-md-6 text-end">

        <div class="form-check d-inline-block me-2">
            <input class="form-check-input" type="checkbox" value="1" id="opBalanceCheck">
            <label class="form-check-label" for="opBalanceCheck">Opening Balance</label>
        </div>

        <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
        <ul class="dropdown-menu">
            <li><a data-nil="1" data-type="balance" class="dropdown-item addon_type nil" href="javascript:void(0)">Include Nil Balances</a></li>
            <li><a data-nil="0" data-type="balance" style="display: none;" class="dropdown-item addon_type nil" href="javascript:void(0)">Exclude Nil Balances</a></li>

            <li><a data-nil="1" data-type="transaction" class="dropdown-item addon_type nil" href="javascript:void(0)">Include Nil Transactions</a></li>
            <li><a data-nil="0" data-type="transaction" style="display: none;" class="dropdown-item addon_type nil" href="javascript:void(0)">Exclude Nil Transactions</a></li>
        </ul>

        <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success float-end ms-2">Back</a>
        <a href="javascript:void(0);" onclick="return openview();" class="btn btn-sm btn-outline-success float-end ms-2">GO</a>
    

        
         
    </div>

</div>

<div class="row mb-2 align-items-top">

    <div class="col-md-6">
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
              <span class="input-group-text">MC</span>
              <select form="salefrm" name="mc_id" id="mc_id" class="form-select" onchange="this.form.submit()">
                    <option value="0" >All MC</option>
                <?php foreach ($mc_list as $key => $value) {  ?>
                    <option value="<?= $value['id'] ?>" <?= ($value['id']==$mc_id)?"selected":"";?> ><?= $value['label'] ?></option>
                <?php } ?>
                 
               </select>
          </div>  
        </div>        
    </div>
    

    <div class="col-md-6 text-end">
        <div class="dropdown float-end" style="margin-left:12px;width:220px;">
          <div class="input-group">
              <span class="input-group-text">Sub View</span>
              <select name="subview_type" id="subview_type" class="form-select"><option value="item_wise" selected>Item Wise</option></select>
              
          </div>  
        </div>
        <div class="dropdown float-end" style="width:220px;">
          <div class="input-group">
              <span class="input-group-text">View</span>
              <select name="view_type" id="view_type" class="form-select">
                  <option value="item_wise" selected>Item Wise</option>
                  <option value="category_wise">Category Wise</option>
                  <option value="group_wise">Group Wise</option>
                  <option value="material_center">Material Centre</option>
                  <option value="batch_wise">Batch Wise</option>
               </select>
          </div>  
        </div>
    </div>
        
</div>

<div class="modal fade modal-lg" id="calendarmodal" tabindex="-1" aria-labelledby="calendarmodallabel" style="display: none;" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
  
            <div class="col-12 calccard card m-auto">
                <form class="form" method="get" id="salefrm2" autocomplete="off">
                    <input type="hidden" name="mc_id" value="<?= $mc_id ?>">
                    <input type="hidden" name="val" value="<?= $val ?>">

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

<div id="grid_search" style="margin:auto;"></div>
 

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

    var data_json = <?php echo json_encode($data);?>;

    $('.nil').css('display', 'none');
    $('.nil[data-nil='+0+'][data-type="balance"]').css('display', 'block');
    $('.nil[data-nil='+0+'][data-type="transaction"]').css('display', 'block');


    $(document).on('click', '.addon_type', function(){

        var nil = $(this).data('nil');
        var type = $(this).data('type');
        
        var data = data_json;

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
              return el.item_qty != 0;
            });
        }
        if(nil_transaction == 'block'){
            data = data.filter(function (el) {
              return el.transaction_status;
            }); 
        }

        $("#grid_search").pqGrid('option', 'dataModel.data', data);
        $("#grid_search").pqGrid('refreshDataAndView');
    });

   function openview(){
    var type = $("#view_type").val();
    
     if(type == 'item_wise'){
         var valuation_type = $('select[name="val"]').val();
         window.location.href = "<?php echo $base_url; ?>reports/stock_status?valuation_type="+valuation_type;
     }
     else if(type == 'category_wise'){
         $(this).val('Item Wise');
         window.location.href = "<?php echo $base_url; ?>reports/stock_status_category";
     }
     else if(type == 'group_wise'){
         $(this).val('Item Wise');
         window.location.href = "<?php echo $base_url; ?>reports/stock_status_group";
     }
     else if(type == 'material_center'){
         $(this).val('Item Wise');
         window.location.href = "<?php echo $base_url; ?>reports/stock_status_mc";
     }
      else if(type == 'batch_wise'){
         $(this).val('Batch Wise');
         window.location.href = "<?php echo $base_url; ?>reports/stock_status_bw";
     }
     else{
         e.preventDefault();
     }
    
   
   }
   
   function calculateSummary() {
    var total_op_valuation = 0,
        total_valuation = 0,
        total_profit = 0,
        data = this.option('dataModel.data');
    

        data.forEach(function(row){
          total_op_valuation += parseValue(row.op_item_value);
          total_valuation += parseValue(row.item_value);
          total_profit += parseAmount(row.profit); 
        }); 
    
        var totalData = {
            item_name : 'Total Valuation',
            op_item_qty : -1,
            op_item_value : parseValue(total_op_valuation),
            item_value : parseValue(total_valuation),
            profit : parseAmount(total_profit),
            item_qty : -1,      
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
            { title: "Item", width: 180,   dataIndx: "item_name" },
            { title: "Companies", width: 180,   dataIndx: "companies" },
            { title: "Unit", width: 40,   dataIndx: "unit_name" },

            { title: "Opening", align: 'center', hidden:true, colModel: [
                { title: "Qty",  width: 140,   dataIndx: "op_item_qty", align:"right", hidden:true ,
                    render: function( ui ) {
                        var rd = ui.rowData;
                            if(rd.op_item_qty != -1)
                                return formatQty(rd.op_item_qty);   
                            else
                                return "";
                    }},
                { title: "Value", width: 140,  dataIndx: "op_item_value", align:"right", hidden:true ,
                    render: function( ui ) {
                        var rd = ui.rowData;
                            return formatValue(rd.op_item_value);   
      
                    } },
            ]},
            { title: "Closing", align: 'center', colModel: [
                { title: "Qty",  width: 140,   dataIndx: "item_qty", align:"right" ,
                    render: function( ui ) {
                        var rd = ui.rowData;
                            if(rd.item_qty != -1)
                                return formatQty(rd.item_qty);   
                            else
                                return "";
                    }},
                { title: "Value", width: 140,  dataIndx: "item_value", align:"right",
                    render: function( ui ) {
                        var rd = ui.rowData;
                            return formatValue(rd.item_value);   
      
                    } },
            ]}, 
                
            { title: "Profit", width: 140,  dataIndx: "profit", align:"right",
                render: function( ui ) {
                    var rd = ui.rowData;
                        return formatAmount(rd.profit);   
  
                } },
        ];

        var dataModel = {"data": data_json}
           
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel : colModel,
            editable: false,
            dataReady: calculateSummary,
            numberCell: { show: true },
            filterModel: { mode: 'OR' },
            editable: false,
            showTitle: false,
            create: function (evt, ui) {// make first row auto selected
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
            var rowData    = ui.rowData;
              
            var item_id   = rowData.item_id;
            var unit_id = rowData.unit_id;
            var mc_id = $('select[name="mc_id"]').val();
            var val = $('select[name="val"]').val();

            var from_date = $('input[name="from_date"]').val();
            var to_date = $('input[name="to_date"]').val();
            
            <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
                window.location.href= baseurl+'/grpcomp/reports/item_ledger_detail?item_id='+item_id+'&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>&unit_id='+unit_id+'&mc_grp_id=0&mc_id='+mc_id+'&val='+val;
            <?php } else { ?>
                window.location.href= baseurl+'/grpcomp/reports/item_summary/'+item_id+'?unit_id='+unit_id+'&mc_id='+mc_id+'&val='+val; 
            <?php } ?>
         }
         
        newObj.cellKeyDown= function(evt, ui) {
               var rowData     = ui.rowData;

               var item_id   = rowData.item_id;
               var unit_id = rowData.unit_id;
               var mc_id = $('select[name="mc_id"]').val();
               var val = $('select[name="val"]').val();

                var from_date = $('input[name="from_date"]').val();
                var to_date = $('input[name="to_date"]').val();

               if (evt.keyCode==13){

                <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
                    window.location.href= baseurl+'/grpcomp/reports/item_ledger_detail?item_id='+item_id+'&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>&unit_id='+unit_id+'&mc_grp_id=0&mc_id='+mc_id+'&val='+val;
                <?php } else { ?>
                    window.location.href= baseurl+'/grpcomp/reports/item_summary/'+item_id+'?unit_id='+unit_id+'&mc_id='+mc_id+'&val='+val; 
                <?php } ?>

                   
               }
           
           }
         
         
        var $grid = $("#grid_search").pqGrid(newObj);
         // $("#grid_search").pqGrid('loadState'); 
         // $(window).unload( function(){
         // $("#grid_search").pqGrid('saveState');
       // });

        $("#opBalanceCheck").click(function(evt){
            var val = evt.target.checked;
            if(val==true){

              
              colModel[3].hidden=false;
              colModel[3].width= 280;
              console.log(colModel[3]);
              colModel[3]['colModel'][0].hidden = false;
              colModel[3]['colModel'][0].width = 140;
              colModel[3]['colModel'][1].hidden = false;
              colModel[3]['colModel'][1].width = 140;
              $("#grid_search").pqGrid( "option", "colModel", colModel );
             
              $("#grid_search").pqGrid( {autoFit: true} );
              $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
              
            }
          else{
              colModel[3].hidden=true;
              colModel[3].width= 280;
              colModel[3]['colModel'][0].hidden = true;
              colModel[3]['colModel'][0].width = 140;
              colModel[3]['colModel'][1].hidden = true;
              colModel[3]['colModel'][1].width = 140;

              $("#grid_search").pqGrid( "option", "colModel", colModel );
               $("#grid_search").pqGrid("refreshCM");
               $("#grid_search").pqGrid("refresh");
             }
        });
       
</script>
 </body>
</html>
