<?php $header = array( 	'title' => 'Bill Sundry' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>

<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Bill Sundry</h3></div>
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
            <a href="<?php echo $base_url;?>billsundry/add" class="btn btn-success">Add Bill Sundry</a>
            <a href="javascript:void(0);" class="editbtn btn btn-success">Edit</a>
            <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>  
           
            
            <div class="float-md-end d-inline-block">

                <a href="javascript:void(0);" id="updategrid_changes" class="btn btn-info" style="display:none;">Update Changes</a>  

                <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
                <ul class="dropdown-menu" style="">
                    <li><a class="dropdown-item" href="javascript:void(0);" id="bulk_update_opn_balances">Update Opening balances</a></li>    
                </ul>
                <a href="<?php echo history_back(); ?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> 
            </div> 
        </div></div>


				
           
    
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
    
    <div class="mt-5" id="billsundry_grid"  style="margin:auto;"></div>
    
   <?php 
      $json_items = array();
      
      ?>
  
<?php echo view('includes/footer_scripts'); ?>
<script>
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

        var changeStatus = function (ui) { 
             ui.rowData.isedited= '1' ;
        }
        
        var drcrlist     = [{"CR":"CR"},{"DR":"DR"}];
        
    var colModel = [

            { title: '<input name="select_all" id="select_all" value="1" type="checkbox">', width: 100, dataIndx: "checkbox" ,editable: false},
            // { title: "ID", width: 180, dataIndx: "bill_sundry_id"},
            { title: "Name", width: 180, dataIndx: "billsndry_name",editable: false,filterable:"no"},
			{ title: "Group", width: 180, dataIndx: "billsundry_group_name",editable: false,filterable:"no"},
            { title: "Type", width: 140, dataIndx: "billsndry_type",editable: false,filterable:"no"},
            { title: "Nature", width: 140, dataIndx: "billsndry_nature",editable: false,filterable:"no"} ,
            { title: "Op. Bal.", width: 140, dataIndx: "bsd_op_bal",editable: false,filterable:"no",dataType: "float",
                validations: [{ type: 'gte', value: 0, msg: "should be > 0"}], 
                editor: {                   
                 type: "textbox",
                  init: changeStatus,
                  options: []
               }
            } ,
            { title: "DR/CR", width: 140, dataIndx: "bsd_op_bal_drcr",editable: false,filterable:"no",
                editor: {
                    type: 'select',
                    init: changeStatus,
                    options: drcrlist
                },
            },
            
	    	];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            url: "<?php echo base_url();?>/admin/billsundry/ajax_billsundry",
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
            editable: true,
            editModel: { clicksToEdit: 1},
            numberCell: { show: true },
			wrap:false,
            showTitle: true,
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
		
		newObj.rowDblClick    = function(event, ui) {
  	     	var rowData        = ui.rowData;
		    var bill_sundry_id     = rowData.bill_sundry_id;			
		    window.location.href= baseurl+'/admin/billsundry/modify/'+bill_sundry_id;
	     } 
	     
	    newObj.cellKeyDown = function(evt, ui) {
			  var rowData        = ui.rowData;
			  var bill_sundry_id     = rowData.bill_sundry_id;			   
			  if (evt.keyCode==13){
				   window.location.href= baseurl+'/admin/billsundry/modify/'+bill_sundry_id;
			  }
			  
		  } 
	     
        var $grid = $("#billsundry_grid").pqGrid(newObj);
        $("#billsundry_grid").pqGrid('loadState'); 
       $(window).unload( function(){
       $("#billsundry_grid").pqGrid('saveState');
    });

    
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
		  alert_notification("First select a billsundry to edit!!");
	   }
	  else{
	   if(sel_id!='')
	    window.location.href=baseurl+"/admin/billsundry/modify/"+sel_id;
      else
	   return false;	
      }
    })
 


 $(document).on('click',".deletebtn",function(){
	var ischeckled =  $('.items_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a billsundry to delete!!");
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
			 confirm_delete(baseurl+"/admin/billsundry/remove/"+checkedVals.join(","));			
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


    $("#bulk_update_opn_balances").on("click",function(){
          var pq_grids = $("#billsundry_grid");        
          var colM=pq_grids.pqGrid( "option" , "colModel" );         
          colM[5].editable = true;
          colM[6].editable = true;
          pq_grids.pqGrid( "option", "colModel", colM);      
          
          $("#updategrid_changes").show();
    });

    $("#updategrid_changes").on("click",function(){
        var pq_grids = $("#billsundry_grid");
        var data = pq_grids.pqGrid('option', 'dataModel.data');   
        var accounts_balance_data = [];
        for (var j = 0; j < data.length; j++) {
            var bill_sundry_id        = data[j]['bill_sundry_id'];
            var bsd_op_bal        = data[j]['bsd_op_bal'];
            var bsd_op_bal_drcr      = data[j]['bsd_op_bal_drcr'];
            var isedited      = data[j]['isedited'];


            if(bill_sundry_id  && isedited == 1){
                accounts_balance_data.push({
                    "bill_sundry_id": bill_sundry_id,
                    "bsd_op_bal": bsd_op_bal,
                    "bsd_op_bal_drcr": bsd_op_bal_drcr,
                    "isedited":isedited
                });

            }   
        }
        if(accounts_balance_data.length > 0){
            $.ajax({
                url: '/admin/billsundry/update_all_op_balances', 
                type: 'POST',
                data: {bsd_array: accounts_balance_data},
                dataType: "json",
                beforeSend: function() {
                    show_loader();
                },
                success: function (response) {
                    stop_loader();

                    if(response.status){
                        alert_success(response.message);
                        $("#updategrid_changes").hide();
                        reset_grid();
                    }
                    else{
                        alert_notification(response.message);
                        $("#updategrid_changes").hide();
                        reset_grid();
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
        else{
            alert_notification('No changes has been made');
            $("#updategrid_changes").hide();
            reset_grid();
            
        }
    })

    function reset_grid()
    {
        var pq_grids = $("#billsundry_grid");        
        var colM=pq_grids.pqGrid( "option" , "colModel" );         
        colM[5].editable = false;
        colM[6].editable = false;
        pq_grids.pqGrid( "option", "colModel", colM);

        $("#billsundry_grid").pqGrid('refreshDataAndView');
    }

</script>

</body>
</html>
