<?php $header = array('title' => 'Tax Category'); ?>
<?php echo view('includes/header',$header); ?>
<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Tax Category</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
    	<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>       
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">download</span></a>
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
                <a href="<?php echo $base_url;?>taxcategory/add" class="btn btn-success">Add Tax Category</a>
                <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete Tax Category</a>        
              	                  
               <div class="float-md-end d-inline-block">
			 
			   
		<a href="#" class="btn btn-success dropdown dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
		      <ul class="dropdown-menu" style="">
	 
        <li>
          <a class="dropdown-item" href="javascript:void(0);" id="active_inactive">Active/Inactive</a>
        </li>
     
      </ul>
			   <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div> 
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
    <div id="validation_errors"></div>
<br><br>

<form class="form" action="#" method="post" id="salefrm" autocomplete="off" novalidate>
  <div id="taxcategory_grid" style="margin:auto;"> </div> 
 
  </form>
<?php echo view('includes/footer_scripts'); ?>
<script>
 $(function () {
	   
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
            
            { title: "CATEGORY NAME", width: 100, dataIndx: "category_name",editable: false,filterable:"yes"  },
			{ title: "CATEGORY TYPE", width: 100, dataIndx: "category_type",editable: false,filterable:"yes"  },
            { title: "SECTION", width: 100, dataIndx: "section_name",editable: false,},
           	{ title: "RATE", width: 100, dataIndx: "rate",editable: false},			
		    { title: "STATUS", width: 100, dataIndx: "tc_status",editable: false}
	    	];
         var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            url: "<?php echo base_url();?>admin/taxcategory/ajax_category_view",
             getData: function (dataJSON) {
				  var data = dataJSON.data;				
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              },
			   
           };
           
        function calculateSummary() { 		
				var grid = this;
                const url = new URL(window.location.href);
                if(url.searchParams.has('rowIndx')){
                    var rowIndx = url.searchParams.get('rowIndx');
                    url.searchParams.delete('rowIndx');
                    window.history.replaceState(null, null, url);
                    grid.setSelection({ rowIndx: parseInt(rowIndx), focus: true });
                }
                else{
                    grid.setSelection({ rowIndx: grid.rowIndxOffset, focus: true });
                } 
		   }       
           
        var newObj = {
            scrollModel: { autoFit: true },
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            height: 'flex',
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            numberCell: { show: true},
            filterModel: { mode: 'OR', type: "remote" },
            editable: true,
            dataReady: calculateSummary,
			editModel: { clicksToEdit: 1},
            showTitle: true,
            
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
                        listener: { change: filterhandler }
                    },
                    { 
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler,
                        options: function (ui) {
                            var CM = ui.colModel;
                            
                         
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                            if(column.dataIndx!='chkbx' && column.dataIndx!='action' && column.dataIndx!='acc_id'){    
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
            }
        };
	     
		newObj.rowDblClick    = function(event, ui) {
  	     	var rowData      = ui.rowData;
		    var tax_cat_id     = rowData.tax_cat_id;
		  
		    window.location.href= baseurl+'admin/taxcategory/modify/'+tax_cat_id;
	     } 
	     
	      newObj.cellKeyDown = function(evt, ui) {
			  	var rowData      = ui.rowData;
			    var tax_cat_id     = rowData.tax_cat_id;
			
			  if (evt.keyCode==13){
			     
				   window.location.href= baseurl+'admin/taxcategory/modify/'+tax_cat_id;
			  }
			  
		  }
	  $grid=  $("#taxcategory_grid").pqGrid(newObj);
       $("#taxcategory_grid").pqGrid('loadState'); 
       $(window).unload( function(){
       $("#taxcategory_grid").pqGrid('saveState');
    });
   
    
    
   });
  


	$(document).on('click',".deletebtn",function(){
	    
	     var select_row = $("#taxcategory_grid").pqGrid("selection", { type:'row', method:'getSelection'});
	     var rowData = select_row[0].rowData;
	     var tax_cat_id = rowData.tax_cat_id;
	    
	  if(tax_cat_id=='' || tax_cat_id==0){
		  alert_notification("First select a category to delete!!");
	  }
      else{
		 confirm_delete(baseurl+"admin/taxcategory/remove_category/"+tax_cat_id);		
		
	   }
    });
    
     $(document).on('click',"#active_inactive",function(){
         var select_row = $("#taxcategory_grid").pqGrid("selection", { type:'row', method:'getSelection'});
	     var rowData = select_row[0].rowData;
	     var tax_cat_id = rowData.tax_cat_id;
	     
	      var checkedVals_status = rowData.alert_tc_status;
	      var pss_status_val   = rowData.tc_status_vl;
     if(tax_cat_id==0 || tax_cat_id==''){
      alert_notification("First select a tax category to active/inactive!!");
      }
      else{   
     
     if(tax_cat_id!=''){
         if(pss_status_val==0)
             var mark_button_label ='MARK INACTIVE';
         else
            var mark_button_label ='MARK ACTIVE';
   
         Swal.fire({
            title: '',
            html: "ARE YOU SURE TO "+checkedVals_status+" THE TAX CATEGORY MASTER FOR F.Y. <?= company()->fy_short ?> <br /> <br />NOTE: <small>THIS TAX CATEGORY MASTER, WILL NOT BE CARRY FORWARD TO NEXT FINANCIAL YEAR</small> ",
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: mark_button_label,
            customClass: {
              confirmButton: 'btn btn-success',
              cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
       
      
       	$.ajax({
              url: '<?php echo base_url(); ?>admin/taxcategory/changestatus', 
              type: 'POST',
              data: {"pss_status_val": pss_status_val, "accidids":tax_cat_id},
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
					  alert_success("Tax category status has been changed");
                   
					history.back();
				
					
                  }
                  else{
					 stop_loader();
                    alert_notification(response.message);
                    if(response.errors)
                    {
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
	

  $("#updategrid_changes").on("click",function(){
	      var pq_grids = $("#taxcategory_grid");
	      var data = pq_grids.pqGrid('option', 'dataModel.data');	
		   var accounts_balance_data = [];
		   for (var j = 0; j < data.length; j++) {
			   var acc_id     = data[j]['acc_id'];
              var op_bal      = data[j]['op_bal'];
              var bal_type      = data[j]['bal_type'];
			  var isedited      = data[j]['isedited'];
           
            
            if(acc_id && isedited == 1){
                accounts_balance_data.push({
                        "acc_id": acc_id,
                        "op_bal": op_bal,
                        "bal_type": bal_type,
						"isedited":isedited
                   });
                
            }   
		   }
	 $("#accbaldata").val(JSON.stringify(accounts_balance_data));
	show_loader();	 
	$("#salefrm").submit();
  })
  
  $("#bulk_update_opn_balances").on("click",function(){
	      var pq_grids = $("#taxcategory_grid");
	      var data = pq_grids.pqGrid('option', 'dataModel.data');		 
		  var colM=pq_grids.pqGrid( "option" , "colModel" ); 		 
          colM[4].editable = true;
          colM[5].editable = true;
	      pq_grids.pqGrid( "option", "colModel", colM);		
		  var colM=pq_grids.pqGrid( "option" , "colModel" ); 
		  
		  $("#updategrid_changes").show();
	  
  });
 
	  $(document).on('click','.accounts_row', function(e) { 
            $(':checkbox').prop('checked', false);
		    $(".editbtn").removeClass("disabled");
            $(this).addClass('selected_cell').siblings().removeClass('selected_cell');
		    if($(this).is(":checked"))
				$(this).prop('checked', false);		  
			else
				 $(this).prop('checked', true);	            	  		  
            });
			 var pq_grids = $('.pq-grid');
			 $(pq_grids[0]).pqGrid('setSelection', null);
	  </script>	</body></html>