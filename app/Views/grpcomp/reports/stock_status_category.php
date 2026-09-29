<?php $header = array( 	'title' => 'Stock Status' ); ?>
<?php echo view('includes/header',$header); ?>
  
  <div class="row pb-2">
    <div class="col-sm-6"><h3>Stock Status(Category Wise)</h3></div>  
        <div class="col-sm-6 text-end"><div class="taskmenus"><a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a> 

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
        <form class="form needs-validation" method="get" id="salefrm"  novalidate>
        <div class="input-group">
          <span class="input-group-text">As On</span>
          <input type="text" name="fromdate" id="fromdate" class="datepicker form-control p-2" value="<?php echo $fromdate;?>" autocomplete="off" required>
          <button type="submit" class="btn btn-success">GO</button>
        </div>
        </form>
    
    </div>    
    <div class="col-md-6 offset-md-2 text-end">
        <a href="<?php echo history_back();?>" class="btn btn-sm btn-outline-success float-end ms-2">Back</a>
        <a href="javascript:void(0);" onclick="return openview();" class="btn btn-sm btn-outline-success float-end ms-2">GO</a>
       
		<div class="dropdown float-end" style="width:220px;">
          <div class="input-group">
              <span class="input-group-text">View</span>
              <select name="view_type" id="view_type" class="form-select">
                  <option value="item_wise">Item Wise</option>
                  <option value="category_wise" selected>Category Wise</option>
                  <option value="group_wise">Group Wise</option>
                  <option value="material_center">Material Centre</option>
				  				  <option value="batch_wise">Batch Wise</option>
               </select>
          </div>  
        </div>
		<div class="dropdown float-end" style="width:150px;">
          <div class="input-group">
              <span class="input-group-text">Branch</span>
             <?php              
					echo form_dropdown('bo_id',$bo_dropdown,$ses_boid,' id="bo_id" class="form-select"');
					?>
          </div>  
        </div>
    </div>

</div>
	            

   <div id="grid_search" style="margin:auto;"> </div>  
   <?php
   $json_stock = json_encode($stock_status_items);
   ?>
<?php echo view('includes/footer_scripts'); ?>
<script>
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
            { title: "Stock Category", width: 180,   dataIndx: "stock_category" },
            { title: "No. of Items", width: 180,   dataIndx: "no_of_items" },
            { title: "Value", width: 180,  dataIndx: "item_value" }
	    	];
	     var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            postData : {'frmdate':'<?php echo $fromdate;?>'},
            url: "<?php echo base_url();?>/admin/reports/ajax_stock_status_category",
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
            pageModel: { type: 'local' },
            dataModel: dataModel,
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            colModel : colModel,
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
  	           var rowData    = ui.rowData;
  	           
  	           console.log(rowData);
		       var ajax   = rowData.ajax;
		       var item_id   = rowData.item_id;
		        var start_date   = rowData.start_date+"-01";
		         var end_date   = rowData.end_date;
		  var item_unit_id = rowData.item_unit_id;
		    window.location.href= baseurl+'/admin/reports/item_summary?item_id='+item_id+'&unitid='+item_unit_id;
	     }
	     
	    newObj.cellKeyDown= function(evt, ui) {
	           var rowData     = ui.rowData;
		    
		        var ajax   = rowData.ajax;
		         var item_id   = rowData.item_id;
		        var start_date   = rowData.start_date+"-01";
				  var item_unit_id = rowData.item_unit_id;
		         var end_date   = rowData.end_date;
		       if (evt.keyCode==13){
	            
					window.location.href= baseurl+'/admin/reports/item_summary?item_id='+item_id+'&unitid='+item_unit_id;
		       }
		   
	       }
	     
	     
        var $grid = $("#grid_search").pqGrid(newObj);

  function openview(){
	var type = $("#view_type").val();
	var bo_id = $("#bo_id").val();
     if(type == 'item_wise'){
         window.location.href = "<?php echo $base_url; ?>/reports/stock_status?bo_id="+bo_id;
     }
     else if(type == 'category_wise'){
         $(this).val('Item Wise');
         window.location.href = "<?php echo $base_url; ?>/reports/stock_status_category?bo_id="+bo_id;
     }
     else if(type == 'group_wise'){
         $(this).val('Item Wise');
         window.location.href = "<?php echo $base_url; ?>/reports/stock_status_group?bo_id="+bo_id;
     }
     else if(type == 'material_center'){
         $(this).val('Item Wise');
         window.location.href = "<?php echo $base_url; ?>/reports/stock_status_mc?bo_id="+bo_id;
     }
	  else if(type == 'batch_wise'){
         $(this).val('Batch Wise');
         window.location.href = "<?php echo $base_url; ?>/reports/stock_status_bw?bo_id="+bo_id;
     }
     else{
         e.preventDefault();
     }
	
   
   }
</script>
 </body>
</html>
