<?php $header = array( 	'title' => 'Stock Transfer Other(BO) Register' ); ?>
<?php echo view('includes/header',$header); ?>
 
 <div class="row mb-md-0 mb-3">
    <div class="col-md-6"><h3 class="pb-3">Stock Transfer Other(BO) Register</h3></div>
   <div class="col-md-6 text-end">
       <div class="taskmenus">
    <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

    <a href="#"><span class="material-symbols-outlined">print</span></a>
   <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
    <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">CSV</a></li>
    <li><a class="dropdown-item" href="#">Excel</a></li>
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
    
<div class="col-md-4">
<div class="input-group">
    <span class="input-group-text px-1">From</span>
    <input type="text" name="fromdate" value="<?php echo $from_date;?>" class="datepicker form-control p-2" required  style="width:90px;">
    
    <span class="input-group-text px-1">To</span>
    <input type="text" name="todate" value="<?php echo $to_date;?>" class="datepicker form-control p-2" required style="width:90px;">
  <input type="button" class="btn btn-sm btn-success" value="GO" fdprocessedid="6tq9et">
  </div>


</div>
<div class="col-md-8">
<div class="dropdown d-sm-flex d-block float-end">
  <button class="btn btn-success btn-sm dropdown-toggle mt-0 m-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" fdprocessedid="cq5rpm"> View </button>
  <ul class="dropdown-menu" style="">
    <li><a class="dropdown-item" href="#">Action</a></li>
    <li><a class="dropdown-item" href="#">Another action</a></li>
    <li><a class="dropdown-item" href="#">Something else here</a></li>
  </ul>
  <p class="m-0"><a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success">Back</a></p>
</div><div class="dropdown float-end" style="width:180px;">
          <div class="input-group">
              <span class="input-group-text">Branch</span>
             <?php              
					echo form_dropdown('bo_id',$bo_dropdown,$ses_boid,' id="bo_id" form="salefrm" onchange="this.form.submit()" class="form-select"');
					?>
          </div>  
        </div></div>

</div>
 
          
  
<br>
   <div id="grid_search" style="margin:auto;"> </div>  
   <?php
   $array=array();
   $json = json_encode($array);
   ?>
<?php echo view('includes/footer_scripts'); ?>
<script>
var item_id    = '';
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
            { title: "Date", align:"left", width: 180,   dataIndx: "month_name" },
             { title: "Against Challan", align:"left", width: 180,   dataIndx: "month_name" },
              { title: "Item", align:"left", width: 180,   dataIndx: "month_name" },
                { title: "From BO", align:"left", width: 180,   dataIndx: "month_name" },
                  { title: "From MC", align:"left", width: 180,   dataIndx: "month_name" },
                  
                   { title: "To BO", align:"left", width: 180,   dataIndx: "month_name" },
             { title: "To MC", align:"left", width: 180,   dataIndx: "month_name" },
              { title: "Qty", align:"left", width: 180,   dataIndx: "month_name" },
                { title: "Price", align:"left", width: 180,   dataIndx: "month_name" },
                  { title: "Amount", align:"left", width: 180,   dataIndx: "month_name" },
              
           { title: "Description", align:"left", width: 180,   dataIndx: "month_name" },
	    	];
	    	
        var dataModel = {
            location: "remote",
            dataType: "json",
            postData: {item_id:item_id},
            method: "POST",
            url: "<?php echo base_url();?>/admin/reports/ajax_items_summary",
             getData: function (dataJSON) {
                var data = dataJSON.data;
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              }
           };
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            dataModel: dataModel,
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
  	           var rowData       = ui.rowData;
  	          
		       var ajax          = rowData.ajax;
		       var item_id       = rowData.item_id;
		        var start_date   = rowData.start_date+"-01";
		         var end_date    = rowData.end_date;
		    window.location.href= baseurl+'/admin/items/ledger_detail/'+item_id+'/'+start_date+'/'+end_date;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		    
		        var ajax   = rowData.ajax;
		         var item_id   = rowData.item_id;
		        var start_date   = rowData.start_date+"-01";
		         var end_date   = rowData.end_date;
		       if (evt.keyCode==13){
	              window.location.href= baseurl+'/admin/items/ledger_detail/'+item_id+'/'+start_date+'/'+end_date;
		       }
		   
	       }
	     
        var $grid = $("#grid_search").pqGrid(newObj);
        $("#grid_search").pqGrid('loadState'); 
         $(window).unload( function(){
         $("#grid_search").pqGrid('saveState');
       });
  
</script>
 </body>
</html>
