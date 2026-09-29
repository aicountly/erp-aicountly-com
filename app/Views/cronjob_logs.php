<?php $header = ['title' => 'Cron Log']; ?>
<?php echo view('includes/header2', $header); ?>

<style>
    .status-ok { color: green; font-weight: bold; }
    .status-err { color: red; font-weight: bold; }
    .status-run { color: orange; font-weight: bold; }
    #log-section { margin-top: 30px; }
</style>
<div class="content"><div class="pb-5">

<div id="log-section">
    <h3>Item Snapshot Cron Log</h3>

    <label>Company: 
        <select name="company_id" id="company_id">
            <option value="">-- All Companies --</option>
            <?php foreach($companies as $cmp): ?>
                <option value="<?= $cmp['cmp_id'] ?>"><?= $cmp['cmp_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Run Date: <input type="date" id="run_date" value="<?= date('Y-m-d') ?>"></label>

    <button id="btnLoad">Load Log</button>
<br>
    <div id="search_grid"></div>
</div>
</div></div>

<?php echo view('includes/footer_scripts'); ?>
<script>
$(function() {

function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = $toolbar.find(".filterCondition").val(),
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
        
        
    var colModel = [

            { title: "Run ID", width: 60, dataIndx: "run_id" },
            { title: "Company ID", width: 80, dataIndx: "cmp_id" },           
            { title: "FY ID", width: 60, dataIndx: "cmpfymastr_id" },
            { title: "Item Unit", width: 120, dataIndx: "itm_id_unit_id" },
            { title: "Method", width: 60, dataIndx: "method" },
            { title: "Status", width: 60, dataIndx: "status", align:'center',
              render: function(ui){
                  var val = ui.cellData;
                  if(val=='OK') return '<span class="status-ok">OK</span>';
                  if(val=='ERR') return '<span class="status-err">ERR</span>';
                  if(val=='RUN') return '<span class="status-run">RUN</span>';
                  return val;
              }
            },
            { title: "Rows Written", width: 80, dataIndx: "rows_written", align:'right' },
            { title: "Start Time", width: 160, dataIndx: "start_ts" },
            { title: "End Time", width: 160, dataIndx: "end_ts" },
            { title: "Message", width: 300, dataIndx: "msg" }
           
        ];
        var dataModel = {
        location: "remote",
        dataType: "json",
        method: "POST",
        url: "/crondashboard/ajax_daily_log",
        postData: function() {
            return {
                run_date: $("#run_date").val(),
                company_id: $("#company_id").val()
            };
        },
        getData: function(dataJSON) {
            return {
                curPage: dataJSON.curPage || 1,
                totalRecords: dataJSON.totalRecords || 0,
                data: dataJSON.data || []
            };
        }
    };

        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR' },
            numberCell: { show: false },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".items_row"),
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

       
     var $grid = $("#search_grid").pqGrid(newObj);

         $("#search_grid").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#search_grid").pqGrid('saveState');
       });
    
    // Trigger load on button click or filters change
    $("#company_id, #run_date").on('change', function(){
        $grid.pqGrid("option", "pageModel.curPage", 1);
        $grid.pqGrid("refreshDataAndView");
    });
	
	$("#btnLoad").on('click', function(){
        $grid.pqGrid("option", "pageModel.curPage", 1);
        $grid.pqGrid("refreshDataAndView");
    });

});
</script>






