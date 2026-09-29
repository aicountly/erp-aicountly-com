<?php $header = array( 	'title' => 'Branches' ); ?>
<?php echo view('includes/header',$header); ?>



<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Branches</h3></div>
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
               
                <a href="<?php echo $base_url;?>branches/add" class="btn btn-success">Add Branch</a>
                <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete Branch</a>        
                <a href="<?php echo $base_url;?>branches/list_group" class="btn btn-success">Branch Groups</a>
	           <a href="javascript:void(0);"  class="markhobtn btn btn-success">Mark as HO</a>
               
               <div class="float-md-end d-inline-block">
			  <a href="javascript:void(0);" id="updategrid_changes" class="btn btn-info" style="display:none;">Update Changes</a>  
			   
		<a href="#" class="btn btn-success dropdown dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
		<ul class="dropdown-menu" style="">
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

<form class="form" action="<?php echo base_url();?>/admin/branches/update_opn_balances" method="post" id="salefrm" autocomplete="off" novalidate>
  <div id="branches_grid" style="margin:auto;"> </div> 
  <input type="hidden" name="accbaldata" id="accbaldata">
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

			

         var changeStatus = function (ui) { 

                 var rd = ui.rowData;
                 var $inp = ui.$cell.find("input");
                 ui.rowData.isedited= '1' ;

        }
        var drcrlist     = [{"CR.":"CR."},{"DR.":"DR."}];

        var colModel = [
            {
                        title: '',
                        dataIndx: "chkbx",
                        maxWidth: 120,
                        minWidth: 120,
                        type: 'checkbox',
                        cb: {
                            all: false,
                            header: true,
                            check: "YES",
                            uncheck: "NO"
                        },
                        render: function (ui) { 
                            var rowdata = ui.rowData;
                            var cb = ui.column.cb,
                                cellData = ui.cellData,
                                checked = cb.check === cellData ? 'checked' : '',
                                disabled = this.isEditableCell(ui) ? "" : "disabled",
                                text = cb.check === cellData ? 'TRUE' : (cb.uncheck === cellData ? 'FALSE' : '<i>unknown</i>');
										
								
                            return {
								 text: "<label><input name='account_ids[]' class='branches_row' data-id='"+rowdata.acc_id+"' value='"+rowdata.acc_id+"' type='checkbox' " + checked + " /></label>",
                                style: (disabled ? "background:lightgray" : "")	
                            };
                        },
                        editor: false,
                        editable: function (ui) {
                            return !ui.rowData.disabled;
                        }
                    },
            { title: "HO/BO ID", width: 100, dataIndx: "acc_id",editable: false,filterable:"no"},
            { title: "HO/BO NAME", width: 100, dataIndx: "account_name",editable: false,filterable:"yes"  },
			{ title: "HO/BO ALIAS", width: 100, dataIndx: "account_alias",editable: false,filterable:"yes"  },
            { title: "CITY", width: 100, dataIndx: "city_name",editable: false, filterable:"no"},
			{ title: "STATE", width: 100, dataIndx: "state_name",editable: false, filterable:"no"},			
            
	    	];
         var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            url: "<?php echo base_url();?>/admin/branches/ajax_branches_view",
             getData: function (dataJSON) {
				  var data = dataJSON.data;				
                return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
              },
			   
           };
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
                            
                           // console.log(CM);
                            var opts =[];// [{ '': '[ All Fields ]'}];
                            for (var i = 0; i < CM.length; i++) {
                                var column = CM[i];
                                var obj = {};
                            if(column.dataIndx!='chkbx' && column.dataIndx!='acc_id'){    
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
            }
        };
	     
		newObj.rowDblClick    = function(event, ui) {
  	     	var rowData      = ui.rowData;
		    var acc_id     = rowData.acc_id;
		  // set_page();
		    window.location.href= baseurl+'/admin/branches/modify/'+acc_id;
	     } 
	     
	      newObj.cellKeyDown = function(evt, ui) {
			  	var rowData      = ui.rowData;
			    var acc_id     = rowData.acc_id;
			   	//console.log(rowData);
			  if (evt.keyCode==13){
			     //set_page();
				   window.location.href= baseurl+'/admin/branches/modify/'+acc_id;
			  }
			  
		  }
	  $grid=  $("#branches_grid").pqGrid(newObj);
       $("#branches_grid").pqGrid('loadState'); 
       $(window).unload( function(){
       $("#branches_grid").pqGrid('saveState');
    });
   
    
    
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
	
	
   $(document).on('click',".editbtn",function(){
	var ischeckled =  $('.branches_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a account to edit!!");
	  }
      else{	  
		 var sel_id = $('.selected_cell').data('id'); 
		 if(sel_id!='')
			window.location.href=baseurl+"/admin/branches/modify/"+sel_id;
		 else
			return false;
	   }
    }) 

	$(document).on('click',".deletebtn",function(){
	var ischeckled =  $('.branches_row:checked').length;

console.log(ischeckled);	
	  if(ischeckled==0){
		  alert_notification("First select a branch to delete!!");
	  }
      else{	  
		   var checkedVals = $('input[name="account_ids[]"]:checked').map(function() {
		   return this.value;
		}).get();
		
		 if(checkedVals!=''){
			 if(checkedVals=="1,1"){
				alert_notification("HO can not be deleted!!!");
				return false;
			  }else			 
			 confirm_delete(baseurl+"/admin/branches/remove_branches/"+checkedVals.join(","));			
		 }
		 else
			return false;
	   }
    }) 


$(document).on('click',".markhobtn",function(){
	var ischeckled =  $('.branches_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a branch mark to HO!!");
	  }
	  else{	  
		   var checkedVals = $('input[name="account_ids[]"]:checked').map(function() {
		   return this.value;
		}).get();
		
		 if(checkedVals!='')
			 confirm_delete(baseurl+"/admin/branches/mark_branch_ho/"+checkedVals.join(","));			
		 else
			return false;
	   }
    }) 

	
	
	$(document).on('click','.exportexcelbtn', function(e) { 
	window.location.href=baseurl+"/admin/branches/export_excel";
	});
  
  
  $("#updategrid_changes").on("click",function(){
	      var pq_grids = $("#branches_grid");
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
	  
	      var pq_grids = $("#branches_grid");
	      var data = pq_grids.pqGrid('option', 'dataModel.data');		 
		  var colM=pq_grids.pqGrid( "option" , "colModel" ); 		 
          colM[4].editable = true;
          colM[5].editable = true;
	      pq_grids.pqGrid( "option", "colModel", colM);		
		  var colM=pq_grids.pqGrid( "option" , "colModel" ); 
		  
		  $("#updategrid_changes").show();
	  
  });
 
	  $(document).on('click','.branches_row', function(e) { 
	  
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