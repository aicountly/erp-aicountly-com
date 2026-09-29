<?php $header = array( 	'title' => 'Stock Category' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>	  

<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Stock Category</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
		<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>          
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li class="open-comingsoon"><a class="dropdown-item" href="#">CSV</a></li>
            <li><a class="dropdown-item" target="_blank" href="<?= base_url() ?>admin/MasterExport/item_category">Excel</a></li>
            <li class="open-comingsoon"><a class="dropdown-item" href="#">Document</a></li>
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
        <ul class="dropdown-menu open-comingsoon">
            <li><a class="dropdown-item" href="#">Facebook</a></li>
            <li><a class="dropdown-item" href="#">Twitter</a></li>
            <li><a class="dropdown-item" href="#">Instagram</a></li>
        </ul>
       <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>
             
             <div class="collapse listmenu" id="listmenu">
                <a href="<?php echo $base_url;?>items/add_category" class="btn btn-success">Add Category</a>
                <a href="javascript:void(0);" class="editbtn btn btn-success">Edit</a>
                <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>
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
			   
			   
			   
              
               </div> </div>

               

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
			<strong>Error!</strong> <?php $errors = $session->getFlashdata('error_array_message'); ?>
                <ul>
                <?php foreach($errors as $error) { ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
                </ul>
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		</div>
		
    <?php } ?>				
				
	<div class="mt-5" id="grid"></div>
    <?php 
	  $json_company = array();
	  if($category_list){ foreach($category_list as $row){     
		 $json_company[] = array("category_id"=>$row['category_id'],"checkbox"=>$row['checkbox'],"item_catg_status"=>$row['item_catg_status'],"item_cat_name"=>$row['item_catg'],"item_alias_name"=>$row['item_cat_alias']);
		 }
	  } 
	  $json_company = json_encode($json_company);
  ?>               
<?php echo view('includes/footer_scripts'); ?>
<script>
$(document).on('click',"#active_inactive",function(){
  var ischeckled =  $('.category_row:checked').length;  
     if(ischeckled==0){
      alert_notification("First select a category to active/inactive!!");
      }
      else{   
	  var checkedVals = $('input[name="category_ids[]"]:checked').map(function() {
       return this.value;
       }).get();
	
      if(checkedVals!=''){       
       var checkedVals_status = [...new Set($('input[name="category_ids[]"]:checked').map(function() {
		return $(this).attr("data-confirmstatus");
	  }).get())];

       var pss_status_val = [...new Set($('input[name="category_ids[]"]:checked').map(function() {
		return $(this).attr("data-acc_status_vl");
	   }).get())];

	if(pss_status_val==0)
	   var mark_button_label ='MARK INACTIVE';
    else
      var mark_button_label ='MARK ACTIVE';
   
         Swal.fire({
            title: '',
            html: "ARE YOU SURE TO "+checkedVals_status+" THE ITEM CATEGORY FOR F.Y. <?= company()->fy_short ?> <br /> <br />NOTE: <small>THIS ITEM CATEGORY, WILL NOT BE CARRY FORWARD TO NEXT FINANCIAL YEAR</small> ",
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
              url: '<?php echo base_url(); ?>/admin/items/changestatus_category', 
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
					alert_success("Item category status has been changed");
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
    })
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
            { title: '<input name="select_all" id="select_all" value="1" type="checkbox">',  dataIndx: "checkbox" },
            { title: "CATEGORY ", dataIndx: "item_cat_name"},
            { title: "ALIAS", dataIndx: "item_alias_name" },
			{ title: "STATUS", dataIndx: "item_catg_status" }            
	    	];
        var dataModel = {"data":<?php echo $json_company;?>};
        var newObj = {
            scrollModel: { autoFit: true },
			resizable: true,
            autoResize: true,
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { on: true, mode: "OR", header: false, type:'local' },
            numberCell: { show: true },
            editable: false,
            showTitle: true,
			wrap:false,
            create: function (evt, ui) {// make first row auto selected
                    var grid = this,
                    $select_row = $(".items_row"),
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
		    var category_id     = rowData.category_id;			
		    window.location.href= baseurl+'admin/items/modify_category/'+category_id;
	     } 
	     
	    newObj.cellKeyDown = function(evt, ui) {
			  var rowData        = ui.rowData;
			  var category_id     = rowData.category_id;			   
			  if (evt.keyCode==13){
				   window.location.href= baseurl+'admin/items/modify_category/'+category_id;
			  }
			  
		  } 
		 
		 
        var $grid = $("#grid").pqGrid(newObj);
		pq.grid("#grid_search", newObj);
            
			
        
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
	var ischeckled =  $('.category_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a stock category to delete!!");
	  }
      else{	  
			 var checkedVals = [];
			 $('.category_row:checked').each(function() {
			     if(!checkedVals.includes(this.value))
			     {
			         checkedVals.push(this.value)
			     }
	         });
		
		 if(checkedVals.length)
			 confirm_delete(baseurl+"admin/items/remove_catgeory/"+urlSafeBase64(checkedVals.join(",")));			
		 else
			return false;
	   }
    })
	
   $(document).on('click',".editbtn",function(){
	 var sel_id = $('.selected_cell').data('id');
     var  ischeckled =  $('.category_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a category to edit!!");
	  }
	  else{	
	   if(sel_id!='')
	    window.location.href=baseurl+"admin/items/modify_category/"+sel_id;
      else
	   return false;
	  }
    })
 
	 
	 $(document).on('click','.category_row', function(e) { 
            $(':checkbox').prop('checked', false);
		    $(".editbtn").removeClass("disabled");
            $(this).addClass('selected_cell').siblings().removeClass('selected_cell');
            if($(this).is(":checked"))
				$(this).prop('checked', false);		  
				else
				 $(this).prop('checked', true);	            	  		  
            });
		   
</script>