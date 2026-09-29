<?php $header = array( 	'title' => 'Packing List' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>

<div class="row">
		     <div class="col-md-6">  <h3>Packing List</h3></div>
		     <div class="col-md-6  order-3 order-md-2 text-end"><div class="taskmenus">
    <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">CSV</a></li>
            <li><a class="dropdown-item" href="#">Excel</a></li>
            <li><a class="dropdown-item" href="#">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       <a href="https://sandbox.aicountly.in/admin/bulk_updation" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>
             
    <div class="col-md-6 order-2 order-md-3">
            <a href="<?php echo $base_url;?>consignment_packing/add" class="btn btn-success">Add Packing List</a>
            <a href="<?php echo $base_url;?>consignment_packing/manage_list" class="btn btn-success">Manage List</a>          
            </div>
     <div class="col-md-6 text-md-end order-4 collapse listmenu" id="listmenu">
      <button class="btn btn-success m-1" type="button">View</button> 
       <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a>
   </div>
               </div>


				
           
    
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
    
    <div class="mt-5" id="items_grid"  style="margin:auto;"></div>
    
   <?php 
      $json_items = array();
      
      ?>
  
<?php echo view('includes/footer_scripts'); ?>
<script>

function delete_packing(pckingid){
	 Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          // window.location.href=path;
			window.location.href='<?php echo base_url();?>/admin/consignment_packing/delete_packing/'+pckingid;	
			}
	  });
}

function manage_packing(pckingid){
	window.location.href='<?php echo base_url();?>/admin/consignment_packing/manage_list/'+pckingid;
	console.log(pckingid);
}
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
            { title: "PACKING LIST NAME", width: 180, dataIndx: "list_name"},           
            { title: "PACKAGING AGAINST", width: 140, dataIndx: "packing_against" },
            { title: "TENTATIVE LEVEL", width: 140, dataIndx: "tenative_level" },			
			{ title: "ACTION", width: 180, dataIndx: "packing_action"},
	    	];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            url: "<?php echo base_url();?>/admin/consignment_packing/ajax_packing_list",
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
            }
        };
	     
		newObj.rowDblClick    = function(event, ui) {
  	     	var rowData        = ui.rowData;
		    var list_id     = rowData.list_id;			
		    window.location.href= baseurl+'/admin/consignment_packing/edit/'+list_id;
		    
		     
	     } 
	     
	    newObj.cellKeyDown = function(evt, ui) {
			  var rowData        = ui.rowData;
			  var list_id     = rowData.list_id;			   
			  if (evt.keyCode==13){
				   window.location.href= baseurl+'/admin/consignment_packing/edit/'+list_id;
			  }
			  
		  } 
		 
        var $grid = $("#items_grid").pqGrid(newObj);
        
        
     $(document).on('click','#select_all',function(){
        if(this.checked){
              $('.checkbox').each(function(){ this.checked = true; });
			  $(".editbtn").addClass("disabled");
			  $(".duplicatebtn").addClass("disabled");
        }else{
              $('.checkbox').each(function(){ this.checked = false; });
			  $(".editbtn").removeClass("disabled");
			  $(".duplicatebtn").removeClass("disabled");
           }
       });
	
   $(document).on('click',".editbtn",function(){
	 var sel_id = $('.selected_cell').data('id'); 
	 var ischeckled =  $('.items_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a item to edit!!");
	   }
	  else{
	   if(sel_id!='')
	    window.location.href=baseurl+"/admin/items/modify_item/"+sel_id;
      else
	   return false;	
      }
    })
 
 $(document).on('click',".deletebtn",function(){
	var ischeckled =  $('.items_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a item to delete!!");
	  }
      else{	  
			 var checkedVals = [];
			 $('.items_row:checked').each(function() {
			     if(!checkedVals.includes(this.value))
			     {
			         checkedVals.push(this.value)
			     }
	         });
		
		 if(checkedVals.length)
			 confirm_delete(baseurl+"/admin/items/remove_items/"+checkedVals.join(","));			
		 else
			return false;
	   }
    })
	
	
  $(document).on('click',".duplicatebtn",function(){
	 var sel_id = $('.selected_cell').data('id'); 
	 var ischeckled =  $('.items_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a item to duplicate!!");
	   }
	  else{
	   if(sel_id!='')
	    window.location.href=baseurl+"/admin/items/duplicate_item/"+sel_id;
      else
	   return false;	
      }
    })
 
 
	 $(document).on('click','.items_row', function(e) {   
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
