<?php $header = array( 	'title' => 'Items' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>

<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Items</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
		<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>          
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
       <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>
             
             <div class="collapse listmenu" id="listmenu">
            <a href="<?php echo $admin_url;?>items/add_item" class="clickable btn btn-success" target="_blank">Add Item</a>
            <a href="<?php echo $admin_url;?>items/add_item" class="clickable btn btn-success" target="_blank">Edit</a>
            <a href="<?php echo $admin_url;?>items/add_item" class="clickable btn btn-success" target="_blank">Delete</a>            
            <a href="<?php echo $base_url;?>items/list_group" class="btn btn-success" >Item Groups</a>
            <a href="<?php echo $base_url;?>units/list" class="btn btn-success" >Item Units</a>
            <a href="<?php echo $base_url;?>items/stock_category" class="btn btn-success" >Stock Category</a>
               
               <div class="float-md-end d-inline-block"> <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div>
               </div> </div>


				
           
    
     <?php if ($session->getFlashdata('message')) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo $session->getFlashdata('message'); ?>
            </div>
    <?php } ?>
    <?php if ($session->getFlashdata('error_array_message')) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php $errors = $session->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
            </div>
    <?php } ?>
	
	<form class="form" action="" method="get" id="salefrm" autocomplete="off" novalidate>
    <br> <?php echo form_dropdown('grpcmpid',$comp_list,$grpcmpid,'id="grpcmpid" class="form_control" ');?>
	<br>
    <div class="mt-5" id="items_grid"  style="margin:auto;"></div>
    </form>
   <?php 
      $json_items = array();
      
      ?>
  
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
            { title: "Item Name", width: 180, dataIndx: "item_name"},
            { title: "Item Group", width: 140, dataIndx: "item_grp" },
            { title: "Unit", width: 140, dataIndx: "item_unit" },
            { title: "SKU", width: 140, dataIndx: "item_sku" },
			{ title: "UPC", width: 180, dataIndx: "item_upc"},            
	    	];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            url: "<?php echo $base_url;?>items/ajax_items",
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
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { mode: 'OR' },
            numberCell: { show: true },
            editable: false,
            showTitle: true,
            wrap:false,
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

		 
        var $grid = $("#items_grid").pqGrid(newObj);
       

          
</script>

</body>
</html>
