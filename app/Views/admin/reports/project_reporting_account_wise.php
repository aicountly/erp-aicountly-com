<?php $header = array(  'title' => 'Project Report Account Wise' ); ?>
<?php echo view('includes/header',$header); ?>
<div class="row mb-md-0 mb-3">
  <div class="col-md-6">
    <h3 class="pb-3">Project Report Account Wise</h3>
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
          <a class="dropdown-item" href="#">CSV</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Excel</a>
        </li>
        <li>
          <a class="dropdown-item" href="#">Document</a>
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
        <input type="text" name="from_date" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
        
        <span class="input-group-text px-1">To</span>
        <input type="text" name="to_date" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
        
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#calendarmodal">
        <span class="material-symbols-outlined">event</span>
        </button>
        
        <input type="submit" class="btn btn-sm btn-success" value="GO">
      </div>
    </form>
  </div>
  <div class="col-lg-7 text-end">
    
    <div class="form-check form-check-inline me-2">
      <input  class="form-check-input" type="checkbox" value="1" name="fg" id="fg">
      <label class="form-check-label" for="fg">Fixed Grid</label>
    </div>
    <div class="dropdown float-end">
      <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item ontrashmode" href="javascript:void(0);">Trash Mode</a>
        </li>
        
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
      <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success mt-0 m-1">Back</a>
    </div>
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
              
              <span class="fw-bold d-inline-block px-4">
                <input class="form-check-input mt-1 me-1" type="checkbox" value="1" id="tillperiod"> TILL PERIOD</span>
              </div>
              <div class="col-md-6">
                <div class="input-group mb-3">
                  <button type="button" class="input-group-text" id="prev_year">
                  <span class="material-symbols-outlined">arrow_back_ios</span>
                  </button>
                  <button type="button" class="input-group-text fw-bold" id="fy_year" style="width:68%; text-align: center; display: block;">FY: <?= $fy_bgn_yr ?> - <?= ($fy_bgn_yr+1) ?>
                  </button>
                  <button type="button" class="input-group-text" id="next_year">
                  <span class="material-symbols-outlined">arrow_forward_ios</span>
                  </button>
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

<div id="grid_search" style="margin:auto;"> </div>
<?php echo view('includes/footer_scripts'); ?>
<style>
.hidden{display:none;}
</style>

<script>

 $(function () {
     
    function calculateSummary() {
        var data = this.option('dataModel.data');
        var debit = 0;
        var credit = 0;

        data.forEach(function(row){
            debit         += parseAmount(row.debit);
            credit        += parseAmount(row.credit);
        })

        balance = debit - credit;
        if(balance < 0)
          balance_type = 'C';
        else
          balance_type = 'D';

        balance = Math.abs(balance);

        var totalData = {
            project_name      : "Total",
            debit             : parseAmount(debit),
            credit            : parseAmount(credit),
            balance           : parseAmount(balance),
            balance_type      : balance_type,
            
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
                dataIndx = "",//$toolbar.find(".filterColumn").val(),
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
            { title: "PROJECT", align:"left",   dataIndx: "project_name" },
            { title: "ACCOUNT", align:"left",  dataIndx: "acc_name" },
            { title: "DEBIT", align:"right",  dataIndx: "debit",
              render: function( ui ) {
                    var rd = ui.rowData;
                    return formatAmount(rd.debit);    
                }
            },
            { title: "CREDIT", align:"right",  dataIndx: "credit",
              render: function( ui ) {
                    var rd = ui.rowData;
                    return formatAmount(rd.credit);    
                }
            },
            { title: "BALANCE", align:"right",   dataIndx: "balance",
              render: function( ui ) {
                    var rd = ui.rowData;
                    return formatAmount(rd.balance);
                       
                }
            },
            { title: "",  dataIndx: "balance_type"},
      ];
        
     var dataModel = { data: <?= json_encode($data) ?>};
     var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
			 width: '100%',
			resizable: true,
           autoResize: true,
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            // pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            colModel : colModel,
            dataReady : calculateSummary,
            editable: false,
            numberCell: { show: false },
            showTitle: true,
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
        


       
    var $grid = $("#grid_search").pqGrid(newObj);
	pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });
   /*  $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#grid_search").pqGrid('saveState');
       });
 */

      
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
  
</script>
</body>
</html>