<?php $header = array( 	'title' => 'Bill Sundry' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>
 <div id="validation_errors"></div> 
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-md">
    <div class="modal-content border-0 shadow rounded">
      <div class="modal-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="modal-title fw-bold" id="emailModalLabel">New Email</h6>
        <div>
          <button type="button" class="btn btn-sm me-1" title="Minimize"><i class="bi bi-dash"></i></button>
          <button type="button" class="btn btn-sm me-1" title="Maximize"><i class="bi bi-arrows-fullscreen"></i></button>
          <button type="button" class="btn btn-sm" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
        </div>
      </div>
      <div class="modal-body px-4 py-3">
        <form id="emailForm">
        <div class="mb-3 position-relative" id="emailFieldContainer">
          <label class="form-label fw-bold">To</label>
          <div id="emailWrapper" class="form-control d-flex flex-wrap gap-2 py-1 px-2" style="min-height: 44px;">
            <input type="text" id="emailTo" class="border-0 flex-grow-1" style="outline: none;" autocomplete="off" placeholder="Type email..."/>
          </div>
          <ul id="emailSuggestions" class="list-group position-absolute bg-white border shadow-sm w-100" style="top: 100%; z-index: 1055; display: none; max-height: 200px; overflow-y: auto;"></ul>
        </div>

          <div class="mb-2 position-relative">
            <label class="form-label fw-semibold mb-0">Cc</label>
            <div id="ccWrapper" class="form-control d-flex flex-wrap gap-2 py-1 px-2" style="min-height: 44px;">
              <input type="text" id="ccInput" class="border-0 flex-grow-1" style="outline: none;" autocomplete="off" placeholder="Type email..." />
            </div>
            <ul id="ccSuggestions" class="list-group position-absolute bg-white border shadow-sm w-100" style="top: 100%; z-index: 1055; display: none; max-height: 200px; overflow-y: auto;"></ul>
          </div>

    
          <div class="mb-2 position-relative d-none" id="bccContainer">
            <label class="form-label fw-semibold mb-0">Bcc</label>
            <div id="bccWrapper" class="form-control d-flex flex-wrap gap-2 py-1 px-2" style="min-height: 44px;">
              <input type="text" id="bccInput" class="border-0 flex-grow-1" style="outline: none;" autocomplete="off" placeholder="Type email..." />
            </div>
            <ul id="bccSuggestions" class="list-group position-absolute bg-white border shadow-sm w-100" style="top: 100%; z-index: 1055; display: none; max-height: 200px; overflow-y: auto;"></ul>
          </div>


            <button type="button" class="btn btn-sm btn-link ps-0" onclick="$('#bccContainer').removeClass('d-none'); $(this).hide();">+ Bcc</button>

          <div class="mb-2">
            <label class="form-label fw-semibold mb-0">Subject</label>
            <input type="text" class="form-control form-control-sm" placeholder="Email Subject" name="subject" required>
          </div>

          <div class="mb-3">
            <textarea class="form-control" rows="5" placeholder="Write your message..." name="message" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold d-block">Select Export Format:</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="exportCsv" name="export_format[]" value="csv">
              <label class="form-check-label" for="exportCsv">CSV</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="exportXlsx" name="export_format[]" value="xlsx">
              <label class="form-check-label" for="exportXlsx">XLSX</label>
            </div>
            <!-- <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="exportPdf" name="export_format[]" value="pdf">
              <label class="form-check-label" for="exportPdf">PDF</label>
            </div> -->
          </div>


          <div class="text-end">
            <button type="submit" class="btn btn-primary btn-sm">Send</button>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row align-items-center">
		     <div class="col-md-6">  <h3>Bill Sundry</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
		<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>   
        <a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>          
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li class=""><a class="dropdown-item" href="<?= base_url() ?>admin/MasterExport/bill_sundry?type=csv">CSV</a></li>
            <li><a class="dropdown-item" target="_blank" href="<?= base_url() ?>/admin/MasterExport/bill_sundry?type=excel">Excel</a></li>
            <!-- <li class="open-comingsoon"><a class="dropdown-item" href="#">Document</a></li> -->
          </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
        <ul class="dropdown-menu ">
           <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#emailModal">Email</a>
         </li>
        </ul>
       <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
    </div>
    </div>
             
             <div class="collapse listmenu" id="listmenu">
            <a href="<?php echo $base_url;?>billsundry/add" class="btn btn-success">Add Bill Sundry</a>
            <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>  
           
            
            <div class="float-md-end d-inline-block">

                <a href="javascript:void(0);" id="updategrid_changes" class="btn btn-info" style="display:none;">Update Changes</a>  

                <a href="#" class="btn btn-success dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
                <ul class="dropdown-menu" style="">
                    <li>
          <a class="dropdown-item" href="javascript:void(0);" id="active_inactive">Active/Inactive</a>
        </li>
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


   $('#emailForm').on('submit', function (e) {
    e.preventDefault();

     const hasToEmails = $('#emailWrapper input[type="hidden"][name="emails[]"]').length > 0;

  if (!hasToEmails) {
    Swal.fire({
      icon: 'warning',
      title: 'Missing Recipient',
      text: 'Please enter at least one email in the "To" field.',
    });
    return;
  }

    const formData = $(this).serialize();

    $.ajax({
      url: '<?= base_url("admin/billsundry/send_billsundry_email") ?>',
      method: 'POST',
      data: formData,
      success: function (response) {
        if (response.status === 'success') {
          Swal.fire({
      title: 'Success!',
      text: 'Email sent successfully!',
      icon: 'success',
      confirmButtonText: 'OK'
        }).then(() => {
          location.reload();
          
        });
         
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('Something went wrong while sending the email.');
      }
    });
  });


  
$(document).ready(function () {
  function setupEmailField(wrapperId, inputId, suggestionId, inputName) {
    const $input = $(`#${inputId}`);
    const $suggestions = $(`#${suggestionId}`);
    const $wrapper = $(`#${wrapperId}`);
    let activeIndex = -1;

    function addEmailTag(email) {
      const tag = $(`
        <div class="d-flex align-items-center px-2 py-1 rounded-pill bg-light border" style="font-size: 0.875rem;">
          <span class="me-2">${email}</span>
          <button type="button" class="btn btn-sm btn-link text-dark p-0 lh-1" style="font-size: 1.2rem;">&times;</button>
          <input type="hidden" name="${inputName}[]" value="${email}">
        </div>
      `);
      tag.find('button').on('click', function () {
        tag.remove();
      });
      tag.insertBefore($input);
      $input.val('');
      activeIndex = -1;
      $suggestions.hide();
    }

    // Handle input for AJAX suggestions
    $input.on('input', function () {
      const query = $input.val();
      if (query.length < 2) return $suggestions.hide();

      $.ajax({
        url: '<?= base_url("admin/accounts/search_emails") ?>',
        method: 'GET',
        data: { q: query },
        dataType: 'json',
        success: function (data) {
          let html = '';
          data.forEach(email => {
            html += `<li class="list-group-item list-group-item-action email-suggestion">${email}</li>`;
          });
          $suggestions.html(html).show();
          activeIndex = -1;
        }
      });
    });

    // Suggestion click
    $(document).on('click', `#${suggestionId} .email-suggestion`, function () {
      const email = $(this).text();
      addEmailTag(email);
    });

    // Keyboard controls: Enter, Up, Down
    $input.on('keydown', function (e) {
      const items = $suggestions.find('.email-suggestion');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (activeIndex < items.length - 1) {
          activeIndex++;
        } else {
          activeIndex = 0;
        }
        items.removeClass('active');
        items.eq(activeIndex).addClass('active');
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (activeIndex > 0) {
          activeIndex--;
        } else {
          activeIndex = items.length - 1;
        }
        items.removeClass('active');
        items.eq(activeIndex).addClass('active');
      } else if (e.key === 'Enter') {
        e.preventDefault();
        const activeItem = items.eq(activeIndex);
        const email = activeItem.length ? activeItem.text() : $input.val().trim().replace(/,$/, '');
        if (email) addEmailTag(email);
      } else if (e.key === ',') {
        e.preventDefault();
        const email = $input.val().trim().replace(/,$/, '');
        if (email) addEmailTag(email);
      }
    });

    // Hide on click outside
    $(document).on('click', function (e) {
      if (!$(e.target).closest(`#${wrapperId}`).length) {
        $suggestions.hide();
      }
    });
  }

  // Init To, Cc, Bcc
  setupEmailField('emailWrapper', 'emailTo', 'emailSuggestions', 'emails');
  setupEmailField('ccWrapper', 'ccInput', 'ccSuggestions', 'cc');
  setupEmailField('bccWrapper', 'bccInput', 'bccSuggestions', 'bcc');
});

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

            //{ title: '<input name="select_all" id="select_all" value="1" type="checkbox">', width: 100, dataIndx: "checkbox" ,editable: false},
            // { title: "ID", width: 180, dataIndx: "bill_sundry_id"},
            { title: "NAME", width: 180, dataIndx: "billsndry_name",editable: false,filterable:"no"},
			{ title: "GROUP", width: 180, dataIndx: "billsundry_group_name",editable: false,filterable:"no"},
            { title: "TYPE", width: 140, dataIndx: "billsndry_type",editable: false,filterable:"no"},
            { title: "NATURE", width: 140, dataIndx: "billsndry_nature",editable: false,filterable:"no"} ,
             { title: "STATUS", width: 140, dataIndx: "acc_status",editable: false,filterable:"no"} ,
            { title: "OP. BAL.", width: 140, dataIndx: "bsd_op_bal",editable: false,filterable:"no",dataType: "float",
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
            url: "<?php echo base_url();?>admin/billsundry/ajax_billsundry",
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
            dataReady: calculateSummary,
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
                            { "contain": "Contains" },
							{ "begin": "Begins With" },
                            
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
		    var bsd_id     = rowData.bsd_id;			
		    window.location.href= baseurl+'admin/billsundry/modify/'+bsd_id;
	     } 
	     
	    newObj.cellKeyDown = function(evt, ui) {
			  var rowData        = ui.rowData;
			  var bsd_id     = rowData.bsd_id;			   
			  if (evt.keyCode==13){
				   window.location.href= baseurl+'admin/billsundry/modify/'+bsd_id;
			  }
			  
		  } 
	     
        var $grid = $("#billsundry_grid").pqGrid(newObj);
       

    $(document).on('click',"#active_inactive",function(){
		 
		
		
         var select_row = $("#billsundry_grid").pqGrid("selection", { type:'row', method:'getSelection'});
	     var rowData = select_row[0].rowData;
	     var bsd_id = rowData.bsd_id;
	     if( rowData.acc_is_restrict==1){
	   alert_notification("THIS IS A SYSTEM GENERATED A/C.");
	   return false;
	   }
	   
	      var checkedVals_status = rowData.alert_acc_status;
	      var pss_status_val   = rowData.acc_status_vl;
     if(bsd_id==0 || bsd_id==''){
      alert_notification("First select a billsundry to active/inactive!!");
      }
      else{   
     
     if(bsd_id!=''){
         if(pss_status_val==0)
             var mark_button_label ='MARK INACTIVE';
         else
            var mark_button_label ='MARK ACTIVE';
   
         Swal.fire({
            title: '',
            html: "ARE YOU SURE TO "+checkedVals_status+" THE BILLSUNDRY MASTER FOR F.Y. <?= company()->fy_short ?> <br /> <br />NOTE: <small>THIS BILLSUNDRY MASTER, WILL NOT BE CARRY FORWARD TO NEXT FINANCIAL YEAR</small> ",
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
              url: '<?php echo base_url(); ?>admin/billsundry/changestatus', 
              type: 'POST',
              data: {"pss_status_val": pss_status_val, "accidids":bsd_id},
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
					  alert_success("Billsunndry status has been changed");
                   
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
	
   


 $(document).on('click',".deletebtn",function(){
      var select_row = $("#billsundry_grid").pqGrid("selection", { type:'row', method:'getSelection'});
	     var rowData = select_row[0].rowData;
	     var acc_id = rowData.bsd_id;
	     
		 if( rowData.acc_is_restrict==1){
	   alert_notification("THIS IS A SYSTEM GENERATED A/C.");
	   return false;
	   }
	   
	     if(acc_id=='' || acc_id==0){
		  alert_notification("First select a bill sundry to delete!!");
	     }    
      else{
		 confirm_delete(baseurl+"admin/billsundry/remove_billsundry/"+acc_id);		
		
	   }
	   
    });
	

 
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
