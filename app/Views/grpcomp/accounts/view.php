<?php $header = array( 	'title' => 'Accounts' ); ?>
<?php echo view('includes/'.$folder_path.'header',$header); ?>



<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Accounts</h3></div>
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
              
                <a href="<?php echo $admin_url;?>accounts/add" target="_blank" class="clickable btn btn-success">Add Account</a>
                <a href="<?php echo $admin_url;?>accounts" target="_blank" class="clickable btn btn-success">Delete</a>        
                <a href="<?php echo $base_url;?>accounts/list_group" class="btn btn-success">Show Groups</a>
	         
               
               <div class="float-md-end d-inline-block">
			  <a href="javascript:void(0);" id="updategrid_changes" class="btn btn-info" style="display:none;">Update Changes</a>  
			   
		<a href="#" class="btn btn-success dropdown dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
		<ul class="dropdown-menu" style="">
         <li><a class="dropdown-item" href="javascript:void(0);" id="bulk_update_opn_balances">Update Opening balances</a></li>
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

<form class="form" action="" method="get" id="salefrm" autocomplete="off" novalidate>
    <?php echo form_dropdown('grpcmpid',$comp_list,$grpcmpid,'id="grpcmpid" class="form_control" ');?>
	<br><br>
  <div id="accounts_grid" style="margin:auto;"> </div> 
  </form>
<?php echo view('includes/'.$folder_path.'footer_scripts'); ?>
<script>
 $(function () {
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


         var changeStatus = function (ui) { 

                 var rd = ui.rowData;
                 var $inp = ui.$cell.find("input");
                 ui.rowData.isedited= '1' ;

        }
        var drcrlist     = [{"CR.":"CR."},{"DR.":"DR."}];

        var colModel = [            
            { title: "ACCOUNT ID", width: 100, dataIndx: "acc_id",editable: false,filterable:"no"},
            { title: "ACCOUNT NAME", width: 100, dataIndx: "account_name",editable: false,filterable:"yes"  },
			{ title: "VENDOR CODE", width: 100, dataIndx: "vendor_code",editable: false,filterable:"yes"  },
            { title: "GROUP", width: 100, dataIndx: "group_name",editable: false, filterable:"no"},
            { title: "BALANCE", width: 100, dataIndx: "op_bal",editable: false ,dataType: "float" ,filterable:"no",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}], 
			    editor: {                   
        	   	 type: "textbox",
                  init: changeStatus,
                  options: []
               }
            },
            { title: "Dr/Cr", width: 100, dataIndx: "bal_type",editable: false ,editor: {
                type: 'select',
                init: changeStatus,
                options: drcrlist
            }},
            
	    	];
         var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
			postData: {"grpcmpid":'<?php echo $grpcmpid;?>'},
            url: "<?php echo $base_url;?>accounts/ajax_accounts_view",
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
	     
		
	  $grid=  $("#accounts_grid").pqGrid(newObj);
      
    
    
   });
  


	$(document).on('click',".deletebtn",function(){
	
	window.location.href=baseurl+"/admin/accounts/export_excel";		
	
    }) 
	
			
	  </script>	</body></html>