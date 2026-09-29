<?php $header = array( 	'title' => 'Account Ledger' ); ?>
<?php echo view('includes/header',$header); ?>
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
    <div class="col-md-6"><h3 class="pb-3">Account Ledgers</h3></div>
    <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
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

        <div class="dropdown d-inline-block me-1" style="width:220px;">

        <div class="form-check form-check-inline me-2">
            <input form="salefrm" class="form-check-input" <?= ($m==1) ? 'checked' : '' ?> type="checkbox" value="1" name="m" id="m" onchange="this.form.submit()">
            <label class="form-check-label" for="m">Memorandum</label>
        </div> 
          <div class="input-group input-group-sm input-group">
              <span class="input-group-text">View</span>
              <select form="salefrm" class="form-select" name="view" onchange="this.form.submit()">
                  <option <?= ($view==0) ? 'selected' : '' ?> value="0">Condensed</option>
                  <option <?= ($view==1) ? 'selected' : '' ?> value="1">Detailed</option>
              </select>
              
          </div>  
        </div>
        <div class="dropdown float-end">
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item ontrashmode" href="javascript:void(0);">Trash Mode</a></li>
               
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

                <input type="hidden" name="type" value="Account Ledger">
                <input type="hidden" name="detail" value="Account">

                <div class="col-md-12 p-2">
                    <label>Account</label>
                    <input name="item" type="text" class="form-control borderdark" value="<?= $show_account_name ?>" required>
                    <input type="hidden" name="id" value="<?= $account_id ?>">
                </div>

                <div class="row p-4">
        <?php $fy_bgn_yr = date('Y',strtotime(session()->get('ses_company_fy_beginning'))); ?>
        <div class="calc col-md-6">
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
                <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:70%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?></button>
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
        <p><em>Account: <strong><?php echo $show_account_name;?></strong></em></p>
    </div>
      <div class="col-md-6 text-end">
       <p class="text-end pe-3"><em>Opening Balance: <strong><?php echo $show_acc_opn_balance;?></strong></em></p>
    </div>
</div>

<div id="validation_errors"></div>
<br>
<div id="grid_search" style="margin:auto;"> </div>  

 
 

<?php echo view('includes/footer_scripts'); ?>
<script>

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
             
              const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);

                    this.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                    
                    
                } 
             
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            data = this.option('dataModel.data'),
            len = data.length;


        data.forEach(function(row){ 
            
            debitTotal +=  parseAmount(row.debit_total);
            creditTotal +=  parseAmount(row.credit_total);

        })

        var totalData = {
                col_date: "Total",
                col_type :"",
                col_vchno:"",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                pq_rowcls: 'grid_footer_color',
                summaryRow: true
            }
            
            

        this.option('summaryData', [totalData]);
    }
    
    
        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = '',//$toolbar.find(".filterCondition").val(),
                dataIndx = '',//$toolbar.find(".filterColumn").val(),
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
             {
                        title: '<label><input id="select_all" class="hidden" type="checkbox"/></label>',dataIndx:"chkbx"
                    },
            { title: "DATE", dataIndx: "txn_date", width: 50},
            { title: "TYPE", width: 50, dataIndx: "voucher_type" },
            { title: "VCH/BILL NO", width: 50, dataIndx: "voucher_no"},
             
            { title: "ACCOUNT", width: 150, dataIndx: "account_name"},
            { title: "NARRATION", width: 150, dataIndx: "short_narration"},
  
             { title: "DEBIT "+ "("+ SYSTEM_CURRENCY + ")", width: 80, align: "right", dataIndx: "debit"},
             { title: "CREDIT "+ "("+ SYSTEM_CURRENCY + ")", width: 80, align: "right", dataIndx: "credit"},
             { title: "BALANCE "+ "("+ SYSTEM_CURRENCY + ")", width: 80, align: "right", dataIndx: "balance"},
             { title: "", width: 30, dataType: "string", dataIndx: "balance_type"},
		   
	 	    ];
       
        var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>', 'account_id':'<?php echo $account_id;?>', 'm':'<?php echo $m;?>', 'view':'<?php echo $view;?>'},
            url: "<?php echo base_url();?>/admin/accounts/ajax_accounts_ledger",
             getData: function (dataJSON) {
                var data = dataJSON.data;
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
        var loadStateSuccess;
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
            dataReady: calculateSummary,
            colModel: colModel,  
            numberCell: { show: false },
            filterModel: { mode: 'OR', type: "remote" },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
             loadStateSuccess = this.loadState({ refresh: false });
            
             
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
               /*  var grid = this;

                const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);
                    grid.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                }
                else{
                    grid.setSelection({ rowIndx: grid.rowIndxOffset, focus: true });
                } */
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
                    // { 
                    //     type: 'select', cls: "filterColumn",
                    //     listener: filterhandler,
                    //     options: function (ui) {
                    //         var CM = ui.colModel;
                    //         var opts = [{ '': '[ All Fields ]'}];
                    //         for (var i = 0; i < CM.length; i++) {
                    //             var column = CM[i];
                    //             var obj = {};
                    //             if(column.dataIndx!='chkbx'){   
                    //             obj[column.dataIndx] = column.title;
                    //             opts.push(obj);
                    //             }
                    //         }
                    //         return opts;
                    //     }
                    // },
                    // { 
                    //     type: 'select',                         
                    //     cls: "filterCondition",
                    //     listener: filterhandler,
                    //     options: [
                    //         { "begin": "Begins With" },
                    //         { "contain": "Contains" },
                    //         { "end": "Ends With" },
                    //         { "notcontain": "Does not contain" },
                    //         { "equal": "Equal To" },
                    //         { "notequal": "Not Equal To" },
                    //         { "empty": "Empty" },
                    //         { "notempty": "Not Empty" },
                    //         { "less": "Less Than" },
                    //         { "great": "Great Than" },
                    //         { "regexp": "Regex" }
                    //     ]
                    // }
                ]
            }
        };
        
        
        newObj.rowDblClick = function(event, ui) {
             
  	            var rowData    = ui.rowData;
  	            var col_type = rowData.col_type;
  	            var ajax   = rowData.ajax;
		        var voucher_txn_id   = rowData.voucher_txn_id;
		        var voucher_type_id   = rowData.voucher_type_id;

                $("#grid_search").pqGrid('saveState');
                set_page();

		        if(voucher_type_id=='18')
                     window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='11')
                     window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id; 
                 else if(voucher_type_id=='10')
                     window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id;
                 else if(voucher_type_id=='14')
                     window.location.href= baseurl+'/admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
                 else if(voucher_type_id=='6')
                     window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='7')
                     window.location.href= baseurl+'/admin/delivery_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='2')
                     window.location.href= baseurl+'/admin/credit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='3')
                     window.location.href= baseurl+'/admin/debit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='12')
                     window.location.href= baseurl+'/admin/purchase_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='15')
                     window.location.href= baseurl+'/admin/stock_transfer/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='17')
                     window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id
                 else if(voucher_type_id=='19')
                     window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='21')
                     window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='20')
                     window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;
                 else if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='8')
                     window.location.href= baseurl+'/admin/memorandum/edit/'+voucher_txn_id;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	        
	           var rowData     = ui.rowData;
		    
		        var ajax   = rowData.ajax;
		        var col_type = rowData.col_type;
		         var voucher_txn_id   = rowData.voucher_txn_id;
		         var voucher_type_id   = rowData.voucher_type_id;
		       if (evt.keyCode==13){

                $("#grid_search").pqGrid('saveState');
                set_page();
		           
		          if(voucher_type_id=='18')
                     window.location.href= baseurl+'/admin/sales/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='11')
                     window.location.href= baseurl+'/admin/purchase/edit/'+voucher_txn_id; 
                 else if(voucher_type_id=='10')
                     window.location.href= baseurl+'/admin/physical_verification/edit/'+voucher_txn_id+'/'+voucher_type_id;
                 else if(voucher_type_id=='14')
                     window.location.href= baseurl+'/admin/production/modify/'+voucher_txn_id+'/?bom_id='+bom_id;
                 else if(voucher_type_id=='6')
                     window.location.href= baseurl+'/admin/inward_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='7')
                     window.location.href= baseurl+'/admin/delivery_challan/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='2')
                     window.location.href= baseurl+'/admin/credit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='3')
                     window.location.href= baseurl+'/admin/debit_note/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='12')
                     window.location.href= baseurl+'/admin/purchase_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='15')
                     window.location.href= baseurl+'/admin/stock_transfer/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='17')
                     window.location.href= baseurl+'/admin/quotations/edit/'+voucher_txn_id
                 else if(voucher_type_id=='19')
                     window.location.href= baseurl+'/admin/sales_order/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='21')
                     window.location.href= baseurl+'/admin/purchase_requisition/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='20')
                     window.location.href= baseurl+'/admin/packing_unpacking/modify_stock_journal/'+voucher_txn_id+"/"+voucher_type_id;
                 else if(voucher_type_id=='1' || voucher_type_id=='5' || voucher_type_id=='9' || voucher_type_id=='13')
                     window.location.href= baseurl+'/admin/vouchers/edit/'+voucher_txn_id;
                 else if(voucher_type_id=='8')
                     window.location.href= baseurl+'/admin/memorandum/edit/'+voucher_txn_id;
		       }
		   
	       }
        
    var $grid = $("#grid_search").pqGrid(newObj);
        //$("#grid_search").pqGrid('loadState'); 
        //$(window).unload( function(){
       // $("#grid_search").pqGrid('saveState');
    //});
       
    function set_page()
    {
       var select_row = $("#grid_search").pqGrid("selection", { type:'row', method:'getSelection'});
        if(select_row){
            const url = new URL(window.location.href);
            url.searchParams.set('rowIndx', select_row[0].rowIndx);
            window.history.replaceState(null, null, url);
        }
        else{
            const url = new URL(window.location.href);
            if(url.searchParams.has('rowIndx')){
                url.searchParams.delete('rowIndx');
                window.history.replaceState(null, null, url);  
            }
        } 
    }
    
    $(".ontrashmode").on("click",function(){
        $(this).text('Delete Vouchers'); 
        $(".hidden").show();
        $(this).removeClass('ontrashmode');
        $(this).addClass('delete_vouchers');
    }) 
     
   $(document).on("click",".delete_vouchers",function(){
        $(".hidden").show();
     if( $('.voucher_row:checked').length=='0')  {
         alert_notification("First select voucher to delete!!");
         return false;
     }
   else{
           var vouchersarray=[];
           $('.voucher_row:checked').each(function(){
              vouchersarray.push($(this).val());
          });
          confirm_voucher_delete(vouchersarray);
            // window.location.reload();
      }
      
  })  
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
	 
			var from_date = $('#from_date').val(); 
			var to_date   = $('#to_date').val();
            var view   = $('select[name="view"]').val(); 
			var m      = $('#m').prop('checked') ? 1 : 0;

            if(view == 0){
                var stringparameters = "accid=<?php echo $account_id;?>&m="+m+"&from_date="+from_date+"&to_date="+to_date;
                window.location.href= baseurl+"/admin/export/account_ledger?"+stringparameters;
            }	
			if(view == 1){
                var stringparameters = "m="+m+"&from_date="+from_date+"&to_date="+to_date;
                window.location.href= baseurl+"/admin/export/account_ledger_detailed/<?php echo $account_id;?>?"+stringparameters;
            }
		    
           
        }

		</script>	<style>.hidden{display:none;}</style></body></html>