<?php $header = array(  'title' => 'Reporting Table('.$table_id.') Summary' ); ?>
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
<div class="row mb-3">
  <div class="col-md-6 order-1 pb-1">
    <h3>Reporting Table(<?php echo $table_id;?>) Summary</h3>
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
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_csv();">CSV</a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_excel();">Excel</a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0)" onclick="print_pdf();">Document</a>
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
  
      <div class="dropdown float-end d-inline-block">
        <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add On </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="<?php echo base_url();?>/admin/testvaluationreport/reporting_table/itemrepbon">Reporting Table BO</a>
			<a class="dropdown-item" href="<?php echo base_url();?>/admin/testvaluationreport/reporting_table/itemrepmcn">Reporting Table MC</a>
			<a class="dropdown-item" href="<?php echo base_url();?>/admin/testvaluationreport/reporting_table/itemrepgrp">Reporting Table GRP</a>
			<a class="dropdown-item" href="<?php echo base_url();?>/admin/testvaluationreport/reporting_table/itemrepcat">Reporting Table CAT</a>
			<a class="dropdown-item" href="<?php echo base_url();?>/admin/testvaluationreport/reporting_table/itemrepprj">Reporting Table PRJ</a>
			<a class="dropdown-item" href="<?php echo base_url();?>/admin/testvaluationreport/reporting_table/itemrepbat">Reporting Table BAT</a>
			<a class="dropdown-item" href="<?php echo base_url();?>/admin/testvaluationreport/reporting_table/itemrepall">Reporting Table ALL</a>
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


<div id="validation_errors">
</div>
<div id="grid_search" style="margin:auto;"> </div>
<?php echo view('includes/footer_scripts'); ?>

<script>

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
		
    var colModel = <?php echo $colModels;?>;
        
     var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'table_id':'<?php echo $table_id;?>','from_date':'<?php echo $from_date;?>', 'to_date':'<?php echo $to_date;?>'},
            url: "<?php echo base_url();?>/admin/testvaluationreport/ajax_rptbl_transactions",
             getData: function (dataJSON) {
                var data = dataJSON.data;

                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
     var newObj = {

            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: true, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },           
            dataModel: dataModel,
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            filterModel: { on: true, mode: "OR", header: true, type:'remote' },
            numberCell: { show: false,width: 40, title: "#" },
            colModel : colModel,
            editable: false,           
             wrap:false,
            showTitle: false,            
        };
    var $grid = $("#grid_search").pqGrid(newObj);
   
 
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