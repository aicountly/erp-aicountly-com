<?php $header = array(  'title' => 'Purchase Order Summary' ); ?>
<?php echo view('includes/header',$header); ?>
<div class="row mb-md-0 mb-3">
    <div class="col-md-6">
        <h3 class="pb-3">Purchase Order Summary</h3>
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
        
    </div>
    <div class="col-lg-7 text-end">

        <div class="dropdown float-end">
            
            <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Add Ons </button>
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

<div id="grid_search" style="margin:auto;"> </div>

<?php echo view('includes/footer_scripts'); ?>
<style>
.hidden{display:none;}
</style>

<script>

     $(function () {
        
        function calculateSummary() {
            var data = this.option('dataModel.data');
            var debit_total = 0;
            var credit_total = 0;
            
                

            data.forEach(function(row){
                debit_total         += parseAmount(row.debit_total);
                credit_total        += parseAmount(row.credit_total);
            })

            var totalData = {
                month        : "Total",
                debit        : formatAmount(debit_total),
                credit       : formatAmount(credit_total),
                
                pq_rowcls : 'grid_footer_color',
                summaryRow: true
            }
            
            this.option('summaryData', [totalData]);
        }
        
        var colModel = [
                { title: "MONTH (<?= fy_calender()->name ?>)", align:"left",   dataIndx: "month" },
                { title: "DEBIT", align:"right",  dataIndx: "debit" },
                { title: "CREDIT", align:"right",   dataIndx: "credit" },
                { title: "BALANCE &nbsp;&nbsp;", align:"right",   dataIndx: "balance"},
            ];
                
         var dataModel = { data: <?= json_encode($summary) ?>};

         var newObj = {
                scrollModel: { autoFit: true },
                height: 'flex',
				resizable: true,
           autoResize: true,
                collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
                selectionModel: { type: 'row',mode:'single' },
                dataModel: dataModel,
                colModel : colModel,
                dataReady : calculateSummary,
                editable: false,
                numberCell: { show: false },
                showTitle: false,
                create: function (evt, ui) {// make first row auto selected
                      var grid = this,
                        $select_row = $(".select-row"),
                        data = ui.dataModel.data;
                       grid.setSelection({ rowIndx: 0, focus: true });
                },
            };
            

        newObj.rowDblClick = function(event, ui) {
            var rowData     = ui.rowData;
            var bsd_id       = rowData.bsd_id;
            var from_date   = rowData.from_date;
            var to_date     = rowData.to_date;
            
            window.location.href= baseurl+'/admin/registerlog/purchase_order_register?&from_date='+ from_date +'&to_date='+ to_date;
        }
        
        newObj.cellKeyDown = function(evt, ui) {
            var rowData     = ui.rowData;
            var bsd_id       = rowData.bsd_id;
            var from_date   = rowData.from_date;
            var to_date     = rowData.to_date;
            if (evt.keyCode==13){
                window.location.href= baseurl+'/admin/registerlog/purchase_order_register?&from_date='+ from_date +'&to_date='+ to_date;
            }
        }
             
        var $grid = $("#grid_search").pqGrid(newObj);
        pq.grid("#grid_search", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });

          
        });
      
</script>
</body>
</html>