<?php $header = array( 	'title' => 'Account/Group List' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
 .myform .col-12{padding:6px 0px;}
    .myform label{width:25%; float:left;}
    .myform .form-control, .myform select {width:75%;}
</style>

 <div class="row mb-2"><div class="col-md-6">
    <h3 class="pb-0">Account/Group List</h3>
    
    </div>
    
        <div class="col-md-6 text-end">
<div class="taskmenus">
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="javascript:void(0)" onclick="print_grid();"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></a>
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
     </li>
</div>
    </div>
    
    
    <div class="col-md-5">
        <form class="form" method="get" id="salefrm2" autocomplete="off">
            
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
    
    
    <div class="col-md-7 text-end">
<div class="dropdown float-end">
  <button class="btn btn-sm btn-success dropdown-toggle m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
  <ul class="dropdown-menu">
        <li><a data-nil="1" class="dropdown-item addon_type nil" href="javascript:void(0)">Include Nil Balances</a></li>
        <li><a data-nil="0" style="display: none;" class="dropdown-item addon_type nil" href="javascript:void(0)">Exclude Nil Balances</a></li>
  </ul>
  <button class="btn btn-sm btn-success m-1" type="button">Templates</button>   
    <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a>
</div></div>
    
    
    </div>
    
<div class="modal fade mt-5 modal-lg" id="calendermodal" tabindex="-1" aria-labelledby="calendermodallabel" style="display: none;" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
  
            <div class="col-12 calccard card m-auto">
                <form class="form" method="get" id="salefrm2" autocomplete="off">
                   
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
    <br>
<p><em>Group: <strong><?php echo $name;?></strong></em></p>

<div id="grid_search" style="margin:auto;"> </div>
 <?php
	$cc_counter =0; 
    $json_data =array();
    if($ac_trial_balance_list){ 
     foreach($ac_trial_balance_list as $key => $row){
		if($row){
			$json_data[] =array("entity_id"=>$row['entity_id'],"entity_name"=>ucwords($row['entity_name']),"debit"=>$row['debit'],"credit"=>$row['credit'],"debit_total"=>$row['debit_total'],"credit_total"=>$row['credit_total'],
			                    "entity_type"=>$row['entity_type'],"balance"=>$row['balance'],"balance_total"=>$row['balance_total'],"balance_type"=>$row['balance_type']);
    } } } 
 
  $json_data = json_encode($json_data);
 ?>
 </div>  

<?php echo view('includes/footer_scripts'); ?>
<script>

    var data_json = <?php echo $json_data;?>;

    var begin_json = data_json.filter(function (el) {
      return el.credit != '' || el.debit != ''
    })
    $('.nil').css('display', 'none');
    $('.nil[data-nil='+0+']').css('display', 'block');


    $(document).on('click', '.addon_type', function(){

        var nil_type = $(this).data('nil');
        
        var data = [];

        if(nil_type == 1){
            $("#nil_type").val("1");
            data = data_json;
            $('.nil').css('display', 'none');
            $('.nil[data-nil='+0+']').css('display', 'block');
        }
        if(nil_type == 0){
            $("#nil_type").val("0");
            var data = data_json.filter(function (el) {
              return el.credit != '' || el.debit != ''
            });
            $('.nil').css('display', 'none');
            $('.nil[data-nil='+1+']').css('display', 'block');
        }

        $("#grid_search").pqGrid('option', 'dataModel.data', data);
        $("#grid_search").pqGrid('refreshDataAndView');
    });

     $(function () {
         
         function calculateSummary() {
        var debitTotal = 0,
            creditTotal = 0,
            balanceTotal = 0,
            balanceType = '',
            data = this.option('dataModel.data'),
            len = data.length;

        data.forEach(function(row){
            
            debitTotal += row.debit_total;
            creditTotal += row.credit_total;
            balanceTotal += row.balance_total;

            
        })

        if(balanceTotal >= 0)
            balanceType = 'DR';
        if(balanceTotal < 0)
            balanceType = 'CR';

        balanceTotal = Math.abs(balanceTotal)

        var totalData = {
                entity_name: "Total",
                debit: formatAmount(debitTotal),
                credit: formatAmount(creditTotal),
                balance: formatAmount(balanceTotal),
                balance_type: balanceType,
                pq_rowcls: 'grid_footer_color',
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
            { title: "ACCOUNT", dataIndx: "entity_name", width: 100, render: filterRender,render: function(ui){
                    if( ui.rowData.summaryRow ){
                        return "<b>"+ui.cellData+"</b>";
                    }
                } },
            { title: "DEBIT", width: 80, align: 'right',dataIndx: "debit"},
            { title: "CREDIT", width: 80, align: 'right', dataIndx: "credit"},
            { title: "BALANCE", width: 80, align: 'right', dataIndx: "balance"},
            { title: "", width: 80, dataType: "float", dataIndx: "balance_type",align:'left'},
		   
	 	    ];
        var dataModel = {"data":<?php echo $json_data;?>}
        
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
           /* pageModel: { type: 'local' }, */
           
            dataModel: dataModel,
          
            dataReady: calculateSummary,
            colModel: colModel,  
            
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
          	var rowData       = ui.rowData;
		     var entity_id   = rowData.entity_id;
		     var entity_type = rowData.entity_type; 
		     var have_childs  = rowData.have_childs;
		     
		    
		     if(entity_type=='grp')
		     window.location.href= baseurl+'/admin/accounts/accounts_trial/'+entity_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
             if(entity_type=='bsd')
                <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
                    window.location.href= baseurl+'/admin/reports/bill_sundry_ledger/'+entity_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                <?php } else { ?>
                    window.location.href= baseurl+'/admin/reports/bill_sundry_summary/'+entity_id; 
                <?php } ?>
		     if(entity_type=='acc')
                 <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
                    window.location.href= baseurl+'/admin/accounts/ledger_detail/'+entity_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                 <?php } else { ?>
                    window.location.href= baseurl+'/admin/accounts/monthly_detail/'+entity_id; 
                 <?php } ?>
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		    
		        var entity_id   = rowData.entity_id;
		        var entity_type = rowData.entity_type; 
		        
		       if (evt.keyCode==13){
	               if(entity_type=='grp')
		                window.location.href= baseurl+'/admin/accounts/accounts_trial/'+entity_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                    
                    if(entity_type=='bsd')
                        <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
                            window.location.href= baseurl+'/admin/reports/bill_sundry_ledger/'+entity_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                        <?php } else { ?>
                            window.location.href= baseurl+'/admin/reports/bill_sundry_summary/'+entity_id; 
                        <?php } ?>
                        
                    if(entity_type=='acc')
                        <?php if(date('d-m',strtotime($from_date)) != '01-04'){ ?>
                            window.location.href= baseurl+'/admin/accounts/ledger_detail/'+entity_id+"?from_date=<?= $from_date ?>&to_date=<?= $to_date ?>";
                        <?php } else { ?>
                            window.location.href= baseurl+'/admin/accounts/monthly_detail/'+entity_id; 
                        <?php } ?>
		       }
               if (evt.keyCode==119){
                
                    if(entity_type == 'acc')
                        window.location.href= baseurl+'/admin/accounts/modify/'+entity_id;
                    if(entity_type == 'bsd')
                        window.location.href= baseurl+'/admin/billsundry/modify/'+entity_id;
                    if(entity_type == 'grp')
                        window.location.href= baseurl+'/admin/accounts/modify_group/'+entity_id;

                }
		   
	       }
        
        var $grid = $("#grid_search").pqGrid(newObj);
    
    });

</script> 
