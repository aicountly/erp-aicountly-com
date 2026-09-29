<?php $header = array( 	'title' => 'Units' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:5% 30% 30% 20% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>	  

    <h3 class="pb-3">Units</h3>
    <a href="<?php echo $admin_url;?>units/add" class="clickable btn btn-success" target="_blank">Add Unit</a>
    <a href="<?php echo $admin_url;?>" class="clickable btn btn-success" target="_blank">Edit</a>
    <a href="<?php echo $admin_url;?>" class="clickable btn btn-success" target="_blank">Delete</a>   
    <a href="<?php echo $base_url;?>items/list_items" class="btn btn-success">Items</a>
    <a href="<?php echo $base_url;?>items/list_group" class="btn btn-success">Item Groups</a>
    
    <?php if (session()->getFlashdata('message')) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo session()->getFlashdata('message'); ?>
            </div>
    <?php } ?>
    <?php if (session()->getFlashdata('error_array_message')) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php $errors = session()->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
            </div>
    <?php } ?>
	<form class="form" action="" method="get" id="salefrm" autocomplete="off" novalidate>
    <br><?php echo form_dropdown('grpcmpid',$comp_list,$grpcmpid,'id="grpcmpid" class="form_control" ');?>
	<br>
  <div class="mt-5" id="units_grid"></div>
 </form>
  
<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>	  
<script>
 $(".clickable").on("click",function(){
		   if($("#grpcmpid").val()=='')
		   {
			  alert_notification("Choose group company first!!!");
			  return false;
		   }
		   else
			   return true;
	   });
	   
	   $("#grpcmpid").on("change",function(){
		   if($(this).val()!=''){
			   $("#salefrm").submit();
		   }
		   
	   });
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
            { title: "Unit Name", width: 180, dataIndx: "item_unit" },
            { title: "Unit Alias", width: 140, dataIndx: "item_unit_alias" },
            { title: "Print Name", width: 140, dataIndx: "item_unit_print" },
            { title: "UQC", width: 140, dataIndx: "item_unit_uqc" },
            
	    	];
       var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
			postData: {"grpcmpid":'<?php echo $grpcmpid;?>'},
            url: "<?php echo $base_url;?>units/ajax_units_view",
             getData: function (dataJSON) {
				  var data = dataJSON.data;				
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              },
			   
           };
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel : colModel,
             filterModel: { on: true, mode: "OR", header: false, type:'local' },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".groups_row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            },load:function(event,ui) {
               
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
                        listener: { change: filterhandler }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler,
                        options: function (ui) {
                            var CM = ui.colModel;
                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                            if(column.dataIndx!='checkbox'){    
                                obj[column.dataIndx] = column.title;
                                opts.push(obj);
                              }
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
                            { "less": "Less Than" },
                            { "great": "Great Than" },
                            
                        ]
                    }
                ]
            },
        };
	    
        var $grid = $("#units_grid").pqGrid(newObj);
       
</script>

</body>
</html>