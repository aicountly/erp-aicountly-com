<?php $header = array( 	'title' => 'Bill of Material (BOM)' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
    .gridtable .row{  display: grid; grid-template-columns:10% 30% 30%  15% 15%;}
    .gridtable .foot.row{ grid-template-columns:100% ;}
</style>

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
		     <div class="col-md-6">  <h3>Bill of Material (BOM)</h3></div>
		     <div class="col-md-6 text-end"><div class="taskmenus">
       <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu"><span class="material-symbols-outlined">filter_list</span></a>
        <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a>
        <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
        <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined"><span class="material-symbols-outlined">download</span></span></a>
        <ul class="dropdown-menu">
            <li class=""><a class="dropdown-item" href="<?= base_url() ?>/admin/MasterExport/bill_of_material?type=csv">CSV</a></li>
            <li><a class="dropdown-item" target="_blank" href="<?= base_url() ?>/admin/MasterExport/bill_of_material?type=excel">Excel</a></li>
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
            <a href="<?php echo $base_url;?>billofmaterial/add" class="btn btn-success">Add Bill of Material (BOM)</a>
            <a href="javascript:void(0);" class="editbtn btn btn-success">Edit</a>
            <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>  
           <a href="<?php echo $base_url;?>billofmaterial/list_group" class="btn btn-success">BOM Groups</a>
               
               <div class="float-md-end d-inline-block"> <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div>
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
    
    <div class="mt-5" id="billofmaterial_grid"  style="margin:auto;"></div>
    
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
      url: '<?= base_url("admin/billofmaterial/send_bill_email") ?>',
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
        
        
    var colModel = [

            { title: '<input name="select_all" id="select_all" value="1" type="checkbox">', width: 100, dataIndx: "checkbox" },
            { title: "Name", width: 180, dataIndx: "bom_name"},
			{ title: "Group", width: 180, dataIndx: "group_name"},
           
            
	    	];
        var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
            url: "<?php echo base_url();?>/admin/billofmaterial/ajax_billofmaterial",
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
            numberCell: { show: false },
            editable: false,
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
                        type: 'select', cls: "filterColumn",
                        listener: filterhandler,
                        options: function (ui) {
                            var CM = ui.colModel;
                            var opts = [];
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
       var rowData      = ui.rowData;
	  
       var acc_id     = rowData.bom_id;
	 
      $("#grid_search").pqGrid('saveState');
       window.location.href= baseurl+'/admin/billofmaterial/modify/'+acc_id;
	  
    } 	
	     
     var $grid = $("#billofmaterial_grid").pqGrid(newObj);
        
    
        
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
		  alert_notification("First select a billofmaterial to edit!!");
	   }
	  else{
	   if(sel_id!='')
	    window.location.href=baseurl+"/admin/billofmaterial/modify/"+sel_id;
      else
	   return false;	
      }
    })
 
 $(document).on('click',".deletebtn",function(){
	var ischeckled =  $('.items_row:checked').length;  
	  if(ischeckled==0){
		  alert_notification("First select a billofmaterial to delete!!");
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
			 confirm_delete(baseurl+"/admin/billofmaterial/remove/"+checkedVals.join(","));			
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
			function set_page()
    {
       var select_row = $("#accounts_grid").pqGrid("selection", { type:'row', method:'getSelection'});
        if(select_row){
            const url = new URL(window.location.href);
            url.searchParams.set('rowIndx', select_row[0].rowIndx);
            window.history.replaceState(null, null, url);
			return select_row[0].rowIndx;
        }
         else{
            const url = new URL(window.location.href);
            if(url.searchParams.has('rowIndx')){
                url.searchParams.delete('rowIndx');
                window.history.replaceState(null, null, url);  
            }if(url.searchParams.has('duplc')){
                url.searchParams.delete('duplc');
                window.history.replaceState(null, null, url);  
            }
			return 0;
        }  		
    }
</script>

</body>
</html>
