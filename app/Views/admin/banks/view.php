<?php $header = array( 	'title' => 'Banks' ); ?>
<?php echo view('includes/header',$header); ?>



<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Banks</h3></div>
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
               
                <a href="<?php echo $base_url;?>banks/add" class="btn btn-success">Add Bank</a>
                <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete Bank</a> 
               
               <div class="float-md-end d-inline-block">
			    
		<a href="#" class="btn btn-success dropdown dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
		<ul class="dropdown-menu" style="">
        </ul>
			   <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div> 
              </div> 

    </div> 
	    
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
                </ul>.
    </div>
    <button type="button" class="btn-close" aria-label="Close"></button>
  </div>
			
		
    <?php } ?>
    <div id="validation_errors"></div><br>
	<div id="banks_grid"></div>
	
<br><br>

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
            {
                        title: '',
                        dataIndx: "chkbx",
                        maxWidth: 50,
                        minWidth: 50,
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
								 text: "<label><input name='banks_ids[]' class='banks_row' data-id='"+rowdata.comp_bank_id+"' value='"+rowdata.comp_bank_id+"' type='checkbox' " + checked + " /></label>",
                                style: (disabled ? "background:lightgray" : "")	
                            };
                        },
                        editor: false,
                        editable: function (ui) {
                            return !ui.rowData.disabled;
                        }
                    },
            { title: "BANK NAME", width: 100, dataIndx: "bank_name",editable: false,filterable:"no"},
            { title: "ACC. No.", width: 100, dataIndx: "account_no",editable: false,filterable:"yes"  },
			{ title: "IFSC CODE", width: 100, dataIndx: "ifsc_code",editable: false,filterable:"yes"  },
            { title: "MICR", width: 100, dataIndx: "micr",editable: false, filterable:"no"},
			{ title: "BRANCH", width: 100, dataIndx: "branch",editable: false, filterable:"no"},			
            { title: "CITY", width: 100, dataIndx: "city",editable: false, filterable:"no"},			
            { title: "STATE", width: 100, dataIndx: "state",editable: false, filterable:"no"},			
            { title: "PIN", width: 100, dataIndx: "pin",editable: false, filterable:"no"},			
            { title: "ADDRESS", width: 100, dataIndx: "address",editable: false, filterable:"no"}
	    	];
         var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            url: "<?php echo base_url();?>admin/banks/ajax_banks_view",
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
		    var comp_bank_id     = rowData.comp_bank_id;
		  // set_page();
		    window.location.href= baseurl+'admin/banks/modify/'+comp_bank_id;
	     } 
	     
	      newObj.cellKeyDown = function(evt, ui) {
			  	var rowData      = ui.rowData;
			    var comp_bank_id     = rowData.comp_bank_id;
			   	//console.log(rowData);
			  if (evt.keyCode==13){
			     //set_page();
				   window.location.href= baseurl+'admin/banks/modify/'+comp_bank_id;
			  }
			  
		  }
	  $grid=  $("#banks_grid").pqGrid(newObj);
       $("#banks_grid").pqGrid('loadState'); 
       $(window).unload( function(){
       $("#banks_grid").pqGrid('saveState');
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
	$(document).on('click',".deletebtn",function(){
	var ischeckled =  $('.banks_row:checked').length;

	  if(ischeckled==0){
		  alert_notification("First select a bank to delete!!");
	  }
      else{	  
		   var checkedVals = $('input[name="banks_ids[]"]:checked').map(function() {
		   return this.value;
		}).get();
		
		 if(checkedVals!=''){
			 
			 $.ajax({
						type: "POST",
						url: baseurl+"admin/banks/remove_banks",
						data: {
							bnkid: checkedVals,
							token: "<?php echo rand(); ?>"
						},
						dataType: "json", // optional: if your server returns JSON
						success: function(response) {						
								if(response.status){
									location.reload();
								}
								else{
								alert_notification(response.message);
								if(response.errors)
								{
									var list = ``;
									$.each(response.errors, function(index, value){
									   list += `
										  <li>${value}</li>
									   `;
									});
									var html = `
									<div class="alert-error-custom">
    <i class="bi bi-x-circle-fill"></i>
    <div>
      <strong>Error!</strong>${list}.
    </div>
    <button type="button" class="btn-close" aria-label="Close"></button>
  </div>
									`;
									$('#validation_errors').html(html);
								} 	
								}
								
							},
							error: function(xhr, status, error) {
								alert_notification(response.message);
								if(response.errors)
								{
									var list = ``;
									$.each(response.errors, function(index, value){
									   list += `
										  <li>${value}</li>
									   `;
									});
									var html = `
										<div class="alert alert-danger alert-dismissible fade show mt-2" style="max-height: 150px;overflow-y: auto;">
										  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
										  <ul>
											  <li>${list}</li>
										  </ul>
									  </div>
									`;
									$('#validation_errors').html(html);
								} 
							}
						});
			 		
		 }
		 else
			return false;
	   }
    }) 
	
	$(document).on('click','.exportexcelbtn', function(e) { 
		//window.location.href=baseurl+"/admin/branches/export_excel";
	});
  
     $(document).on('click','.banks_row', function(e) { 
	  
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