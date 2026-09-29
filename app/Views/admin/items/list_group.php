<?php $header = array( 	'title' => 'Item Groups' ); ?>
<?php echo view('includes/header',$header); ?>

<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Item Groups</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
        	<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li class="open-comingsoon"><a class="dropdown-item" href="#">CSV</a></li>
            <li><a class="dropdown-item" target="_blank" href="<?= base_url() ?>admin/MasterExport/item_groups">Excel</a></li>
            <li class="open-comingsoon"><a class="dropdown-item" href="#">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>
             
             <div class="collapse listmenu" id="listmenu">
                <a href="<?php echo $base_url;?>items/add_group" class="btn btn-success">Add Group</a>
                <input type="button" class="editbtn btn btn-success" value="Edit">
                <input type="button" class="deletebtn btn btn-success" value="Delete">   
                <a href="<?php echo $base_url;?>items/list_items" class="btn btn-success">Items</a>
                
              <div class="float-md-end d-inline-block">
				   <a href="#" class="btn btn-success dropdown dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
				  <ul class="dropdown-menu" style="">				  
					<li>
					  <a class="dropdown-item" href="javascript:void(0);" id="active_inactive">Active/Inactive</a>
					</li>				  
				  </ul>      
					<a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> 
               </div>  
               </div></div>	  




    <?php if ($session->getFlashdata('message')) { ?>
	<div class="alert-success-custom">
			<i class="bi bi-check-circle-fill"></i>
			<div>
			  <strong>Success!</strong> <?php echo $session->getFlashdata('message'); ?>.
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		  </div>		  
    <?php } ?>
    <?php if ($session->getFlashdata('error_array_message')) { ?>
	 <div class="alert-error-custom">
			<i class="bi bi-x-circle-fill"></i>
			<div>
			<strong>Error!</strong><?php $errors = $session->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		</div>
		
		
	
		  
          
    <?php } ?>
  
    <div class="mt-5" id="item_group_grid"></div>
    <?php 
  $json_company = array();
  if($groups_list){ foreach($groups_list as $row){      
         $json_company[] = array("item_grp_id"=>$row['item_grp_id'],"checkbox"=>$row['checkbox'],"item_grp_status"=>$row['item_grp_status'],"item_grp_name"=>$row['item_grp_name'],"item_grp_alias"=>$row['item_grp_alias'],"item_grp_primary"=>$row['item_grp_primary']);
    }
  } 
  $json_company = json_encode($json_company);
  ?>
  
<?php echo view('includes/footer_scripts'); ?>	  
<script>
$(document).on('click',"#active_inactive",function(){
  var ischeckled =  $('.groups_row:checked').length;  
     if(ischeckled==0){
      alert_notification("First select a group to active/inactive!!");
      }
      else{   
	var checkedVals = $('input[name="group_ids[]"]:checked').map(function() {
       return this.value;
    }).get();
	
     if(checkedVals!=''){       
       var checkedVals_status = [...new Set($('input[name="group_ids[]"]:checked').map(function() {
		return $(this).attr("data-confirmstatus");
	}).get())];

       var pss_status_val = [...new Set($('input[name="group_ids[]"]:checked').map(function() {
		return $(this).attr("data-acc_status_vl");
	}).get())];

	if(pss_status_val==0)
	   var mark_button_label ='MARK INACTIVE';
    else
      var mark_button_label ='MARK ACTIVE';
   
         Swal.fire({
            title: '',
            html: "ARE YOU SURE TO "+checkedVals_status+" THE ITEM GROUP FOR F.Y. <?= company()->fy_short ?> <br /> <br />NOTE: <small>THIS ITEM MASTER, WILL NOT BE CARRY FORWARD TO NEXT FINANCIAL YEAR</small> ",
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: mark_button_label,
            customClass: {
              confirmButton: 'btn btn-primary',
              cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
       	$.ajax({
              url: '<?php echo base_url(); ?>/admin/items/changestatus_group', 
              type: 'POST',
              data: {"pss_status_val": pss_status_val, "accidids":urlSafeBase64(checkedVals.join(","))},
              dataType: "json",
              beforeSend: function() {
                  show_loader();
              },
              success: function (response) {
                 stop_loader();
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  if(response.status){
					alert_success("Item group status has been changed");
					history.back();
                  }
                  else{
					 stop_loader();
                     alert_notification(response.message);
                     if(response.errors){
                        var list = ``;
                        if(response.errors.length > 0){
                        	$.each(response.errors, function(index, value){
	                            list += `<li>${value}</li>`;
	                        });
	                        var html = `
	                            <div class="alert-error-custom">
								<i class="bi bi-x-circle-fill"></i>
								<div>
								<strong>Error!</strong>  <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							</div>
	                        `;
	                        $('#validation_errors').html(html);
	                        
	                        $("#validation_errors").show();
	                         $(".alert-error-custom").show();
	                        window.scrollTo(0,0);
                        }
                    }  
                }
                  
              },
              complete: function() {
                  stop_loader();
              },
              error: function (jqXHR, exception) {
                  var error_= '';
                  if (jqXHR.status === 0) {
                      error = 'Not connect.\n Verify Network.';
                  } else if (jqXHR.status == 404) {
                      error = 'Requested page not found. [404]';
                  } else if (jqXHR.status == 500) {
                      error = 'Internal Server Error [500].';
                  } else if (exception === 'parsererror') {
                      error = 'Requested JSON parse failed.';
                  } else if (exception === 'timeout') {
                      error = 'Time out error.';
                  } else if (exception === 'abort') {
                      error = 'Ajax request aborted.';
                  } else {
                      error = 'Uncaught Error.\n' + jqXHR.responseText;
                  }
                  alert_notification(error);
              },           
            });
        }
   
      });
	  return false;
	 }
     else
      return false;
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
            { title: '<input type="checkbox" id="select_all">', width: 10, dataIndx: "checkbox" },
            { title: "GROUP NAME", width: 100, dataIndx: "item_grp_name" },
            { title: "ALIAS NAME", width: 100, dataIndx: "item_grp_alias" },
            { title: "PRIMARY", width: 100, dataIndx: "item_grp_primary" }, 
			{ title: "STATUS", width: 100, dataIndx: "item_grp_status" },	
	    	];
        var dataModel = {"data":<?php echo $json_company;?>}
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel : colModel,
            numberCell: { show: true },
			 wrap:false,
             filterModel: { on: true, mode: "OR", header: false, type:'local' },
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".groups_row"),
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
                            if(column.dataIndx!='checkbox' && column.dataIndx!='item_grp_primary' ){    
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
		    var item_grp_id     = rowData.item_grp_id;			
		    window.location.href= baseurl+'admin/items/modify_group/'+item_grp_id;
	     } 	     
	    newObj.cellKeyDown = function(evt, ui) {
			  var rowData        = ui.rowData;
			  var item_grp_id     = rowData.item_grp_id;			   
			  if (evt.keyCode==13){
				   window.location.href= baseurl+'admin/items/modify_group/'+item_grp_id;
			  }
		  } 
	     
        var $grid = $("#item_group_grid").pqGrid(newObj);
         $("#item_group_grid").pqGrid('loadState'); 
        $(window).unload( function(){
          $("#item_group_grid").pqGrid('saveState');
      });
        
        
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
	
	
	$(".deletebtn").on("click",function(){
	    
	  	var ischeckled =  $('.groups_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a item group to delete!!");
	  }
      else{	  
			var checkedVals = [];
			 $('.groups_row:checked').each(function() {
			     if(!checkedVals.includes(this.value))
			     {
			         checkedVals.push(this.value)
			     }
	         });
		
		 if(checkedVals.length)
			 confirm_delete(baseurl+"admin/items/remove_groups/"+urlSafeBase64(checkedVals.join(",")));			
		 else
			return false;
	   }  
	});
	
	
   $(document).on('click',".editbtn",function(){
	 var sel_id = $('.selected_cell').data('id'); 
	 var ischeckled =  $('.groups_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a group to edit!!");
	   }
	  else{
	   if(sel_id!='')
	    window.location.href=baseurl+"admin/items/modify_group/"+sel_id;
      else
	   return false;	
      }
    })
 
	 $(document).on('click','.groups_row', function(e) {   
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