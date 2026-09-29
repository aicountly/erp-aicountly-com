<?php $header = array( 	'title' => 'Account Groups' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>
	  
<div class="row">
		     <div class="col-6">  <h3 class="pb-3">Group</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
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
             
             <div class="col-md-6">   <a href="<?php echo $admin_url;?>accounts/add_group" class="btn btn-success">Add Group</a>
                <a href="<?php echo $admin_url;?>accounts/list" class="editbtn btn btn-success">Edit</a>
                <a href="<?php echo $admin_url;?>accounts/list" class="deletebtn btn btn-success">Delete</a>   
                <a href="<?php echo $base_url;?>accounts/list" class="btn btn-success">Accounts</a>
                </div>
               <div class="col-md-6 text-end"> <a href="<?php echo history_back();?>" class="btn btn-outline-success btn-sm">« Back</a> </div> 
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
    <?php } 
    
    $message_output;
    ?>
  <form class="form" action="" method="get" id="salefrm" autocomplete="off" novalidate>
  <br> <?php echo form_dropdown('grpcmpid',$comp_list,$grpcmpid,'id="grpcmpid" class="form_control" ');?>
	<br>
    <div class="mt-5" id="account_group_grid"></div>
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
            { title: "Group Name", width: 180, dataIndx: "group_name",editable: false, },
            { title: "Print Name", width: 180, dataIndx: "print_name",editable: false },
            { title: "Primary", width: 140, dataIndx: "primary",filterable:"no",editable: false },
            { title: "Under", width: 140, dataIndx: "under",filterable:"no",editable: false },
            
	    	];
         var dataModel = {
            location : "remote",
            dataType : "json",
            method   : "POST",
            cache    : true,
            url: "<?php echo $base_url;?>accounts/ajax_list_groups",
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
            pageModel: { type: "remote", rPP: 10, },
            editable: true,
			editModel: { clicksToEdit: 1},
            showTitle: false,
            filterModel: { on: true, mode: "OR", header: true, type:'remote' },
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
                            if(column.dataIndx!='chkbx' && column.dataIndx!='primary' && column.dataIndx!='under'){    
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
            
            create: function (evt, ui) {// make first row auto selected
           
                  var grid = this,
                    $select_row = $(".groups_row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
                   
                   
            }
        };
	    
		newObj.rowDblClick    = function(event, ui) {
  	     	var rowData        = ui.rowData;
		    var acc_grp_id     = rowData.acc_grp_id;	
		     //set_page();
		    window.location.href= baseurl+'/admin/accounts/modify_group/'+acc_grp_id;
	     } 
	     
	    newObj.cellKeyDown = function(evt, ui) {
	        
			  var rowData        = ui.rowData;
			  var acc_grp_id     = rowData.acc_grp_id;			   
			  if (evt.keyCode==13){
			      // set_page();
				   window.location.href= baseurl+'/admin/accounts/modify_group/'+acc_grp_id;
			  }
			  
		  }

		  
        var $grid = $("#account_group_grid").pqGrid(newObj);
        $("#account_group_grid").pqGrid('loadState'); 
        $(window).unload( function(){
          $("#account_group_grid").pqGrid('saveState');
      });
        
    
      
    function set_page()
    {
       var select_row = $("#account_group_grid").pqGrid("selection", { type:'row', method:'getSelection'});
        if(select_row){
            const url = new URL(window.location.href);
            url.searchParams.set('rowIndx', select_row[0].rowIndx);
            window.history.replaceState(null, null, url);
        }
        else{
            const url = new URL(window.location.href);
            if(url.searchParams.has('rowIndx')){
                url.searchParams.delete('rowIndx');
                window.history.replaceState(null, null, url);  
            }
        } 
    }
    
    

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
	var ischeckled =  $('.groups_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a group to delete!!");
	  }
      else{	  
			 var checkedVals = [];
			 $('.groups_row:checked').each(function() {
			     if(!checkedVals.includes(this.value))
			     {
			         checkedVals.push(this.value)
			     }
	         });
		
		 if(checkedVals!=''){
			 confirm_delete(baseurl+"/admin/accounts/remove_groups/"+checkedVals.join(",")); 
		 }
		 else
			return false;
	   }
    })
   $(document).on('click',".editbtn",function(){
	 var sel_id = $('.selected_cell').data('id'); 
	 var ischeckled =  $('.groups_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a group to edit!!");
	   }
	  else{
	   if(sel_id!='')
	    window.location.href=baseurl+"/admin/accounts/modify_group/"+sel_id;
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