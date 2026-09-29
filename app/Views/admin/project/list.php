<?php $header = array(  'title' => 'Projects' ); ?>
<?php echo view('includes/header',$header); ?>
<?php
$local_session      = \Config\Services::session();
$fy_begndt          = date('d-m-Y',strtotime($local_session->get('ses_company_fy_beginning')));
$fy_end             = date('31-03-Y', strtotime($fy_begndt. ' + 1 year'));
?>

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


<div class="row mb-2">
  <div class="col-md-6 order-1">
    <h3>Projects</h3>
  </div>
  <div class="col-md-6 order-3 order-md-2 text-end">
    <div class="taskmenus">
      <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu">
        <span class="material-symbols-outlined">filter_list</span>
      </a>
      <a href="javascript:void(0)" id="refresh_grid">
        <span class="material-symbols-outlined">refresh</span>
      </a>
      <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span>
      </a>
      <a href="#">
        <span class="material-symbols-outlined open-comingsoon">print</span>
      </a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">
          <span class="material-symbols-outlined ">download</span>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="<?= base_url() ?>admin/MasterExport/project?type=csv">CSV</a>
          </li>
          <li>
            <a class="dropdown-item" href="<?= base_url() ?>admin/MasterExport/project?type=excel">Excel</a>
          </li>
          <!-- <li>
            <a class="dropdown-item" href="#">Document</a>
          </li> -->
        </ul>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="material-symbols-outlined">share</span>
        </a>
        <ul class="dropdown-menu">
          <li>
              <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#emailModal">Email</a>
          </li>
        </ul>
      </li>
      <a href="<?php echo history_back();?>" class="hideinline-md">
        <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
      </a>
    </div>
  </div>
  <div class="col-md-8 order-2 order-md-3">    
    <a href="<?php echo base_url();?>admin/project/add"><button class="btn btn-success m-1" type="button">Add</button></a>
    <a href="javascript:void(0)" id="edit_project"><button class="btn btn-success m-1" type="button">Edit</button></a>
    <a href="javascript:void(0)" id="delete_project"><button class="btn btn-success m-1" type="button">Delete</button></a>
    <a href="<?php echo base_url();?>admin/project/groups"><button class="btn btn-success m-1" type="button">Groups</button> </a>
    
  </div>
  <div class="col-md-4 text-md-end order-4 collapse listmenu" id="listmenu">
     <div class="float-md-end d-inline-block">
				   <a href="#" class="btn btn-success dropdown dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
				  <ul class="dropdown-menu" style="">				  
					<li>
					  <a class="dropdown-item" href="javascript:void(0);" id="active_inactive">Active/Inactive</a>
					</li>				  
				  </ul>      
					<a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> 
               </div>
  </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="moreoptions" aria-labelledby="moreoptionslabel">
  <div class="offcanvas-header">
    <h4 class="offcanvas-title" id="moreoptionslable">Apps</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
    </button>
  </div>
  <div class="offcanvas-body">
    <div class="row">
      
      <div class="col-sm-6 border-end">
        <h5 class="pb-3">Horizontal</h5>
        
        <p class="offcanvaoptions">
          <i>Condensed</i>
          <label class="starcheck">
            <input type="checkbox" checked="checked">
            <b class="checkmark">★</b>
          </label>
          <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="swap">
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="swap">
          </label>
        </p>
        
        <p class="offcanvaoptions">
          <i>Detailed</i>
          <label class="starcheck">
            <input type="checkbox" checked="checked">
            <b class="checkmark">★</b>
          </label>
          <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="swap">
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="swap">
          </label>
        </p>
        
        <p class="offcanvaoptions">
          <i>All Labels</i>
          <label class="starcheck">
            <input type="checkbox" checked="checked">
            <b class="checkmark">★</b>
          </label>
          <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="swap">
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="swap">
          </label>
        </p>
      </div>
      
      <div class="col-sm-6">
        <h5 class="pb-3">Verticle</h5>
        
        <p class="offcanvaoptions">
          <i>Verticle</i>
          <label class="starcheck">
            <input type="checkbox" checked="checked">
            <b class="checkmark">★</b>
          </label>
          <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="swap">
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="swap">
          </label>
        </p>
        
        <p class="offcanvaoptions">
          <i>Schudle</i>
          <label class="starcheck">
            <input type="checkbox" checked="checked">
            <b class="checkmark">★</b>
          </label>
          <label class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="swap">
          </label>
          <label class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="swap">
          </label>
        </p>
      </div>
      
      <div class="col-sm-12 pt-3 border-top">
        <p class="offcanvaoptions">
          <i>Schedule</i>
          <label class="form-check">No<input class="form-check-input mx-1" name="schedule" type="radio" value="no" id="swap">
        </label>
        <label class="form-check">Yes<input class="form-check-input mx-1" name="schedule" type="radio" value="yes" id="swap">
      </label>
    </p>
    <p class="offcanvaoptions">
      <i>Ratio</i>
      <label class="form-check">No<input class="form-check-input mx-1" name="ratio" type="radio" value="no" id="swap">
    </label>
    <label class="form-check">Yes<input class="form-check-input mx-1" name="ratio" type="radio" value="yes" id="swap">
  </label>
</p>

<p class="text-center pt-3">
  <a  data-bs-toggle="modal" data-bs-target="#moreoptionsmodal" class="btn btn-outline-success">View</a>
</p>

</div>


</div>

</div>
</div>
<!-- Modal -->
<div class="modal fade mt-5" id="moreoptionsmodal" tabindex="-1" aria-labelledby="moreoptionsmodalLabel" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="moreoptionsmodalLabel">App Options title</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
      </button>
    </div>
    <div class="modal-body">
    ...
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary">Save changes</button>
    </div>
  </div>
  </div>
</div>
</div>

<div id="validation_errors"></div>

<div id="grid_search" style="margin:auto;">
</div>


<?php echo view('includes/footer_scripts');
?>
<style>
.boldcell{font-weight:700;}
</style>
<script>
$(document).on('click',"#active_inactive",function(){
  var ischeckled =  $('.project_row:checked').length;  
     if(ischeckled==0){
      alert_notification("First select a project to active/inactive!!");
      }
      else{   
		var checkedVals = $('input[name="project_ids[]"]:checked').map(function() {
		   return this.value;
		}).get();
	
		 if(checkedVals!=''){       
		   var checkedVals_status = [...new Set($('input[name="project_ids[]"]:checked').map(function() {
			return $(this).attr("data-confirmstatus");
		}).get())];

		   var pss_status_val = [...new Set($('input[name="project_ids[]"]:checked').map(function() {
			return $(this).attr("data-acc_status_vl");
		}).get())];

	if(pss_status_val==0)
	   var mark_button_label ='MARK INACTIVE';
    else
      var mark_button_label ='MARK ACTIVE';
   
         Swal.fire({
            title: '',
            html: "ARE YOU SURE TO "+checkedVals_status+" THE PROJECT MASTER FOR F.Y. <?= company()->fy_short ?> <br /> <br />NOTE: <small>THIS PROJECT MASTER, WILL NOT BE CARRY FORWARD TO NEXT FINANCIAL YEAR</small> ",
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
              url: '<?php echo base_url(); ?>/admin/project/changestatus', 
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
					alert_success("Project status has been changed");
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
      url: '<?= base_url("admin/project/send_project_email") ?>',
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
        url: '<?= base_url("admin/accounts/search_emails ") ?>',
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



    $('input.grid_radio_btn').change(function() {

        var data_type = this.value;
        $( "#grid_search" ).pqGrid( "option", "dataModel.postData", function( ui ){
            return {data_type: data_type};
        } );

        $( "#grid_search" ).pqGrid( "refreshDataAndView" )
        
    });

    var colModel = [
        { dataIndx: "state", maxWidth: 30, minWidth: 30, align: "center", resizable: false,
            title: "",
            menuIcon: false,
            cls: 'pq-grid-number-cell', 
            sortable: false, 
            
            render: function( ui ) {
                var rd = ui.rowData;
                var grid = this;
           
                return rd.chkbx;
            }
        },
        { title: "PROJECT", align:"left", width: 180,   dataIndx: "project_name" },
        { title: "ALIAS", align:"left", width: 180,   dataIndx: "project_alias" },
        { title: "GROUP", align:"left", width: 180,   dataIndx: "project_grp_name" },
        { title: "LIABILITY", align: 'center', colModel: [
          { title: "OP BAL", align:"right", width: 100,   dataIndx: "lia_project_op_bal",
                render: function( ui ) {
                    var rd = ui.rowData;
                    return formatAmount(rd.lia_project_op_bal);   
                }},
          { title: "DR/CR", align:"left", width: 20,   dataIndx: "lia_project_op_drcr" }
          ]
        },

        { title: "ASSETS", align: 'center', colModel: [
          { title: "OP BAL", align:"right", width: 100,   dataIndx: "ast_project_op_bal",
                render: function( ui ) {
                    var rd = ui.rowData;
                    return formatAmount(rd.ast_project_op_bal);   
                }},
          { title: "DR/CR", align:"left", width: 20,   dataIndx: "ast_project_op_drcr" }
          ]
        },
		{ title: "STATUS", align:"left", width: 100,   dataIndx: "project_status" },
   
    ];
            
    var dataModel = {

        location : "remote",
        dataType : "json",
        method   : "POST",
        postData : {},
        url: "<?php echo base_url();?>admin/project/ajax_list",
        getData: function (dataJSON) {
            var data = dataJSON.data;

            gridDataModel = dataJSON.data;
            return { curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data };
        }
    };
     var newObj = {
        scrollModel: { autoFit: true },
        height: 'flex',
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
        selectionModel: { type: 'row',mode:'single' },
        pageModel: { type: 'local' },
        dataModel: dataModel,
        pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
        filterModel: { mode: 'OR', type: "remote" },
        colModel : colModel,
        editable: false,
        numberCell: { show: false },
        // pasteModel: { on: false },
        // menuIcon: true,
        wrap:false,
        showTitle: false,
        create: function (evt, ui) {// make first row auto selected
              var grid = this,
                $select_row = $(".select-row"),
                data = ui.dataModel.data;
                grid.setSelection({ rowIndx: 0, focus: true });

        },
        
      
        
       
    };

    newObj.rowDblClick = function(event, ui) {
        var rowData            = ui.rowData;
        var project_id            = rowData.project_id;

        window.location.href= baseurl+'admin/project/edit/'+project_id; 
 
    }
         
    newObj.cellKeyDown = function(evt, ui) {
       var rowData = ui.rowData;
       var project_id  = rowData.project_id;
      
       if (evt.keyCode==13){
            
          window.location.href= baseurl+'admin/project/edit/'+project_id;
              
       }
    }
      
         
    var $grid = $("#grid_search").pqGrid(newObj);

    // $(document).on('change', '.my_checkbox', function(){
    //     if(this.checked) {
    //         $('.my_checkbox').prop("checked", false);
    //         $(this).prop("checked", true);
    //     }
    // });

    $(document).on('click', '#edit_project', function(){

        var project_id = 0;
        $('.my_checkbox:checked').each(function() {
           project_id = $(this).val();
           return false;
        });

        if(project_id){
          window.location.href= baseurl+'admin/project/edit/'+project_id;   
        }
        else{
            alert_notification('First select project');
        }
    });


    $(document).on('click', '#delete_project', function(){

      var ischeckled =  $('.my_checkbox:checked').length;  
      if(ischeckled==0){
          alert_notification("First select a project to delete!!");
      }
      else{   
         var checkedVals = [];
         $('.my_checkbox:checked').each(function() {
             if(!checkedVals.includes(this.value)){
                 checkedVals.push(this.value)
             }
         });
        
         if(checkedVals.length)
             
             $.ajax({
                url: baseurl+"admin/project/delete", 
                type: 'POST',
                data: {project_id_arr: checkedVals},
                dataType: "json",
                
                beforeSend: function() {
                    show_loader();
                    $('#validation_errors').html('');
                },
                success: function (response) {
                    stop_loader();
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    stop_loader();

                    if(response.status){
                        alert_success(response.message);
                        $("#grid_search").pqGrid('refreshDataAndView');
                    }
                    else{
						stop_loader();
                        alert_notification(response.message);
                        $("#grid_search").pqGrid('refreshDataAndView');
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
								<strong>Error!</strong> <ul>${list}</ul>
								</div>
								<button type="button" class="btn-close" aria-label="Close"></button>
							</div>
                                `;
                                $('#validation_errors').html(html);
                                window.scrollTo(0,0);
                            }
                        }  
                    }
                    
                },
                complete: function() {
                    stop_loader();
                },
                error: function (jqXHR, exception) {
                    stop_loader();
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
         else
            return false;
       }
    });


</script> 
</body>
</html>