<?php $header = array( 	'title' => 'Units' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:5% 30% 30% 20% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>	  
<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Units</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
		<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>       
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>   
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">CSV</a></li>
            <li><a class="dropdown-item" target="_blank" href="<?= base_url() ?>admin/MasterExport/items">Excel</a></li>
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
			 
			    <a href="<?php echo $base_url;?>units/add" class="btn btn-success">Add Unit</a>
				<a href="javascript:void(0);" class="editbtn btn btn-success">Edit</a>
				<a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>   
				<a href="<?php echo $base_url;?>items/list_items" class="btn btn-success">Items</a>
				<a href="<?php echo $base_url;?>items/list_group" class="btn btn-success">Item Groups</a>
			  
			 
			 <div class="float-md-end d-inline-block"> <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div> 
			 </div>
			 </div>
			 
			 
			 
             

     
    <?php if (session()->getFlashdata('message')) { ?>
	<div class="alert-success-custom">
			<i class="bi bi-check-circle-fill"></i>
			<div>
			  <strong>Success!</strong> <?php echo session()->getFlashdata('message'); ?>.
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		  </div>
		  
    <?php } ?>
    <?php if (session()->getFlashdata('error_array_message')) { ?>
		<div class="alert-error-custom">
				<i class="bi bi-x-circle-fill"></i>
				<div>
				<strong>Error!</strong> <?php $errors = session()->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
				</div>
				<button type="button" class="btn-close" aria-label="Close"></button>
			</div>
           
    <?php } ?>
  <div class="mt-5" id="units_grid"></div>


    <?php 
  $json_units = array();
  if($units_list){ 
      foreach($units_list as $units_row){
         $json_units[] = array("checkbox"=>'<input name="units_ids[]" class="checkbox units_row"  data-id="'.$units_row['unit_id'].'"  type="checkbox" value="'.$units_row['unit_id'].'">',
                               "item_unit"=>$units_row['item_unit'],
                               "item_unit_alias"=>$units_row['item_unit_alias'],
                               "item_unit_print"=>$units_row['item_unit_print'],
                               "item_unit_uqc"=>$units_row['item_unit_uqc'],
							   "unit_id"=>$units_row['unit_id']
                              );
        }
    } 
   $json_units = json_encode($json_units);
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
            { title: '<input name="select_all" id="select_all" value="1" type="checkbox">', width: 100, dataIndx: "checkbox" },
            { title: "Unit Name", width: 180, dataIndx: "item_unit" },
            { title: "Unit Alias", width: 140, dataIndx: "item_unit_alias" },
            { title: "Print Name", width: 140, dataIndx: "item_unit_print" },
            { title: "UQC", width: 140, dataIndx: "item_unit_uqc" }
	    	];
        var dataModel = {"data":<?php echo $json_units;?>}
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
                            { "contain": "Contains" },
							{ "begin": "Begins With" },
                            
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
	     newObj.rowDblClick    = function(event, ui) {
  	     	var rowData        = ui.rowData;
		    var unit_id     = rowData.unit_id;			
		    window.location.href= baseurl+'admin/units/modify/'+unit_id;
	     } 
	     
	    newObj.cellKeyDown = function(evt, ui) {
			  var rowData        = ui.rowData;
			  var unit_id     = rowData.unit_id;			   
			  if (evt.keyCode==13){
				   window.location.href= baseurl+'admin/units/modify/'+unit_id;
			  }
			  
		  } 
        var $grid = $("#units_grid").pqGrid(newObj);
        
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
	
	 $(document).on('click',".deletebtn",function(){
	var ischeckled =  $('.units_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a item unit to delete!!");
	  }
      else{	  
			 var checkedVals = $('input[name="units_ids[]"]:checked').map(function() {
			return this.value;
		}).get();
		
		 if(checkedVals!='')
			 confirm_delete(baseurl+"admin/units/remove_units/"+urlSafeBase64(checkedVals.join(",")));			
		 else
			return false;
	   }
    })
	
   $(document).on('click',".editbtn",function(){
	 var sel_id = $('.selected_cell').data('id'); 
	 var ischeckled =  $('.units_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a unit to edit!!");
	   }
	  else{
	   if(sel_id!='')
	    window.location.href=baseurl+"admin/units/modify/"+sel_id;
      else
	   return false;	
      }
    })
 
	 $(document).on('click','.units_row', function(e) {   
            $(':checkbox').prop('checked', false);
		    $(".editbtn").removeClass("disabled");
            $(this).addClass('selected_cell').siblings().removeClass('selected_cell');
		    if($(this).is(":checked"))
				$(this).prop('checked', false);		  
				else
				 $(this).prop('checked', true);	            	  		  
            });
</script>

</body>
</html>