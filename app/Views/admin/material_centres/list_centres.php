 <?php $header = array( 	'title' => 'Material Centres' ); ?>
 <?php echo view('includes/header',$header); ?>

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

<div class="row">
		     <div class="col-md-6">  <h3>Material Centres</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
				<a href="javascript:void(0)" id="refresh_grid"><span class="material-symbols-outlined">refresh</span></a>
				<a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a> 
				<a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a>
				<a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
				<a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
				<ul class="dropdown-menu">
					<li class=""><a class="dropdown-item" href="<?= base_url() ?>admin/MasterExport/material_centres?type=csv">CSV</a></li>
					<li><a class="dropdown-item" target="_blank" href="<?= base_url() ?>admin/MasterExport/material_centres?type=excel">Excel</a></li>
					<!-- <li class="open-comingsoon"><a class="dropdown-item" href="#">Document</a></li> -->
				  </ul>
				<a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined">share</span></a> 
				<ul class="dropdown-menu">
					 <li>
						 <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#emailModal">Email</a>
					</li>
				</ul>
			   <a href="<?php echo history_back();?>" class="hideinline-md"><span class="material-symbols-outlined">keyboard_double_arrow_left</span></a>
			</div>
			</div>
             
            <div class="collapse listmenu" id="listmenu">
                <a href="<?php echo $base_url;?>material_centres/add_centres" class="btn btn-success">Add Material Centre</a>
                <input type="button" class="editbtn btn btn-success" value="Edit">
                <input type="button"  class="btn btn-success" id="deletebtn_mc" value="Delete">   
                <a href="<?php echo $base_url;?>material_centres/group_list" class="btn btn-success">Show Groups</a>
                
                
                
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
			  <strong>Success!</strong>  <?php echo $session->getFlashdata('message'); ?>.
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
    
	<div class="mt-5" id="material_centre_grid"></div>
    <?php 
        $json_company = array();
        if($centre_list){ foreach($centre_list as $row){          
            $json_company[] = array("center_id"=>$row['center_id'],"checkbox"=>$row['checkbox'],"mc_status"=>$row['mc_status'],"mat_cent_name"=>$row['mat_cent_name'],"mat_cent_alias"=>$row['mat_cent_alias'],"mat_cent_print"=>$row['mat_cent_print'],"group_name"=>$row['group_name']);          
           }
        } 
       $json_company = json_encode($json_company);
      ?>	  
<?php echo view('includes/footer_scripts'); ?>	  
<script>
$(document).on('click',"#active_inactive",function(){
  var ischeckled =  $('.material_centres:checked').length;  
     if(ischeckled==0){
      alert_notification("First select a material centre to active/inactive!!");
      }
      else{   
	var checkedVals = $('input[name="mc_ids[]"]:checked').map(function() {
       return this.value;
    }).get();
	
     if(checkedVals!=''){       
       var checkedVals_status = [...new Set($('input[name="mc_ids[]"]:checked').map(function() {
		return $(this).attr("data-confirmstatus");
	}).get())];

       var pss_status_val = [...new Set($('input[name="mc_ids[]"]:checked').map(function() {
		return $(this).attr("data-acc_status_vl");
	}).get())];

	if(pss_status_val==0)
	   var mark_button_label ='MARK INACTIVE';
    else
      var mark_button_label ='MARK ACTIVE';
   
         Swal.fire({
            title: '',
            html: "ARE YOU SURE TO "+checkedVals_status+" THE MATERIAL CENTRE FOR F.Y. <?= company()->fy_short ?> <br /> <br />NOTE: <small>THIS MATERIAL CENTRE MASTER, WILL NOT BE CARRY FORWARD TO NEXT FINANCIAL YEAR</small> ",
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
              url: '<?php echo base_url(); ?>/admin/material_centres/changestatus_mc', 
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
					alert_success("Material centre status has been changed");
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
      url: '<?= base_url("admin/material_centres/send_listcenter_email") ?>',
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
           
            { title: '<input name="select_all" id="select_all" value="1" type="checkbox">', width: 100, dataIndx: "checkbox" },
            { title: "CENTRE NAME", width: 180, dataIndx: "mat_cent_name" },
            { title: "ALIAS NAME", width: 140, dataIndx: "mat_cent_alias" },
            { title: "PRINT NAME", width: 140, dataIndx: "mat_cent_print" },
            { title: "GROUP NAME", width: 140, dataIndx: "group_name" },
			{ title: "STATUS", width: 140, dataIndx: "mc_status" },
            
	    	];
        var dataModel = {"data":<?php echo $json_company;?>}
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
             filterModel: { on: true, mode: "OR", header: false, type:'local' },
            dataModel: dataModel,
            colModel : colModel,
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".material_centres"),
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
		    var center_id     = rowData.center_id;			
		    window.location.href= baseurl+'admin/material_centres/modify_centre/'+center_id;
	     } 
	     
	    newObj.cellKeyDown = function(evt, ui) {
			  var rowData        = ui.rowData;
			  var center_id     = rowData.center_id;			   
			  if (evt.keyCode==13){
				   window.location.href= baseurl+'admin/material_centres/modify_centre/'+center_id;
			  }
			  
		  } 
        var $grid = $("#material_centre_grid").pqGrid(newObj);
         $("#material_centre_grid").pqGrid('loadState'); 
       $(window).unload( function(){
       $("#material_centre_grid").pqGrid('saveState');
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

$("#deletebtn_mc").on("click",function(){
   
	var ischeckled =  $('.material_centres:checked').length; 

	  if(ischeckled==0){
		  alert_notification("First select a material centre to delete!!");
	  }
      else{	  
			var checkedVals = [];
			 $('.material_centres:checked').each(function() {
			     if(!checkedVals.includes(this.value))
			     {
			         checkedVals.push(this.value)
			     }
	         });
		
		 if(checkedVals!='')
			 confirm_delete(baseurl+"admin/material_centres/remove_centres/"+urlSafeBase64(checkedVals.join(",")));			
		 else
			return false;
	   }
    });
	
	
   $(document).on('click',".editbtn",function(){
	 var sel_id     = $('.selected_cell').data('id'); 
	 var ischeckled =  $('.material_centres:checked').length;  
	  if(ischeckled==0){ alert_notification("First select a center to edit!!"); }
	  else{
	   if(sel_id!='')
	     window.location.href=baseurl+"admin/material_centres/modify_centre/"+sel_id;
      else
	   return false;	
      }
    })
 
	 $(document).on('click','.material_centres', function(e) {   
            $(':checkbox').prop('checked', false);
		    $(".editbtn").removeClass("disabled");
            $(this).addClass('selected_cell').siblings().removeClass('selected_cell');
		    if($(this).is(":checked"))
				$(this).prop('checked', false);		  
				else
				 $(this).prop('checked', true);	            	  		  
            });
</script>
