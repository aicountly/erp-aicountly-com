<?php $header = array('title' => 'Accounts'); ?>
<?php echo view('includes/header',$header); ?>
<div class="row align-items-center">
  <div class="col-md-6">  <h3>Accounts</h3>
  </div>
  <div class="col-md-6 text-end">
    <div class="taskmenus">
      <a class="hideinline-md"  data-bs-toggle="collapse" href="#listmenu" role="button" aria-expanded="false" aria-controls="listmenu">
        <span class="material-symbols-outlined">filter_list</span></a>
      <a href="javascript:void(0)" id="refresh_grid">
        <span class="material-symbols-outlined">refresh</span></a>
       <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions">
        <span class="material-symbols-outlined">offline_bolt</span></a> 
      <a href="#">
        <span class="material-symbols-outlined open-comingsoon">print</span></a>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">download</span></a>
      <ul class="dropdown-menu">
        <li class="">
          <a class="dropdown-item " href="<?= base_url() ?>admin/MasterExport/accounts?type=csv">CSV</a>
        </li>
        <li>
          <a class="dropdown-item" target="_blank" href="<?= base_url() ?>admin/MasterExport/accounts?type=excel">EXCEL</a>
        </li>
		<li>
          <a class="dropdown-item" target="_blank" href="<?= base_url() ?>admin/MasterExport/accounts?type=pdf">PDF</a>
        </li>
        
      </ul>
      <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined">share</span>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="#" data-bs-toggle="modal" data-pageurl="<?php echo current_url() . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');?>" data-bs-target="#emailModal">Email</a>
        </li>
        <li>
          <a class="dropdown-item" href="#" data-bs-toggle="modal" data-pageurl="<?php echo current_url() . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');?>" data-bs-target="#smsModal">SMS</a>
        </li>
      </ul>
      <a href="<?php echo history_back();?>" class="hideinline-md">
        <span class="material-symbols-outlined">keyboard_double_arrow_left</span>
      </a>
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
      <button type="button" class="btn btn-success">Save changes</button>
    </div>
  </div>
  </div>
</div>
  
  <div class="collapse listmenu" id="listmenu">
    <a href="<?php echo $base_url;?>accounts/add" class="btn btn-success">Add Account</a>
    <a href="javascript:void(0);" class="deletebtn btn btn-success">Delete</a>
    <a href="<?php echo $base_url;?>accounts/list_group" class="btn btn-success">Show Groups</a>
    
	<div class="float-md-end d-inline-block">
      <a href="javascript:void(0);" id="updategrid_changes" class="btn btn-info" style="display:none;">Update Changes</a>
      
      <a href="#" class="btn btn-success dropdown dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">Add On</a>
      <ul class="dropdown-menu" style="">
	  <li id="snillacc">
          <a class="dropdown-item" href="javascript:void(0);" id="show_nill_accounts">Show Nill Accounts</a>
        </li>
		<li style="display:none;" id="hnillacc">
          <a class="dropdown-item" href="javascript:void(0);" id="hide_nill_accounts">Hide Nill Accounts</a>
        </li>
        <li>
          <a class="dropdown-item" href="javascript:void(0);" id="active_inactive">Active/Inactive</a>
        </li>
      <!--  <li>
          <a class="dropdown-item" href="javascript:void(0);" id="bulk_update_opn_balances">Update Opening balances</a>
        </li> -->
      </ul>
      <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md">« Back</a> </div>
    </div>
  </div>
  
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
			<strong>Error!</strong> <?php $errors = $session->getFlashdata('error_array_message'); ?>.
			</div>
			<button type="button" class="btn-close" aria-label="Close"></button>
		</div>
  
  
  <?php } ?>

  <div id="validation_errors"></div>
  <br><br>
<!-- <div class="modal fade ShortcutModals" id="ShortcutModal" tabindex="-1" aria-labelledby="ShortcutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content shortmodel">
        <div class="modal-header">
          <h4 class="" id="ShortcutModalLabel"><img src="<?php echo base_url();?>/public/assets/img/icon5.png" style="width:80px;" alt=""/> Shortcuts for Account Master</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Check your list of Shortcuts to manage your Dashboard</p>
          <ul><li>Add/Edit or Delete a Company</li>
          <li>Manage your entire comapanies at single setp</li>
          </ul>
        </div>
      </div>
    </div>
  </div> -->
  <form class="form" action="<?php echo base_url();?>admin/accounts/update_opn_balances" method="post" id="salefrm" autocomplete="off" novalidate>
   
   <div id="accounts_grid" style="margin:auto;"> </div>
    <input type="hidden" name="accbaldata" id="accbaldata">
  </form>

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
      url: '<?= base_url("admin/accounts/send_account_email") ?>',
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

 $(document).on("click","#show_nill_accounts",function(){
		$( "#accounts_grid" ).pqGrid("option","dataModel.postData", {"show_hidden": 1 } );		 
	    $("#accounts_grid").pqGrid('refresh');  	   
        $("#accounts_grid").pqGrid('refreshDataAndView');
        $("#hnillacc").show(); $("#snillacc").hide();
			
      });$(document).on("click","#hide_nill_accounts",function(){
		$( "#accounts_grid" ).pqGrid("option","dataModel.postData", {"show_hidden": '' } );		 
	    $("#accounts_grid").pqGrid('refresh');  	   
        $("#accounts_grid").pqGrid('refreshDataAndView');
		
		$("#hnillacc").hide(); $("#snillacc").show();
     
			
      });

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
          

         var changeStatus = function (ui) { 

                 var rd = ui.rowData;
                 var $inp = ui.$cell.find("input");
                 ui.rowData.isedited= '1' ;

        }
        var drcrlist     = [{"CR.":"CR."},{"DR.":"DR."}];

        var colModel = [
            {
                        title: '', //<label><input id="select_all" type="checkbox"/> Select All </label>
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
                                text: "<label><input name='account_ids[]' class='accounts_row' data-acc_status_vl='"+rowdata.acc_status_vl+"' data-confirmstatus='"+rowdata.alert_acc_status+"'  data-id='"+rowdata.acc_id+"' value='"+rowdata.acc_id+"' type='checkbox' " + checked + " /></label>",
                                style: (disabled ? "background:lightgray" : "")
                            };
                        },
                        editor: false,
                        editable: function (ui) {
                            return !ui.rowData.disabled;
                        }
                    },
          { title: "ACCOUNT ID",  dataIndx: "acc_id",editable: false}, 
          { title: "ACCOUNT NAME", dataIndx: "account_name",editable: false,filterable:"yes"  },
          { title: "GROUP",  dataIndx: "group_name",editable: false, filterable:"no"},
           { title: "STATUS",  dataIndx: "acc_status",editable: false, filterable:"no"},
          { title: "OPN. BALANCE",  dataIndx: "op_bal",editable: false ,dataType: "float" ,filterable:"no",
              validations: [{ type: 'gte', value: 0, msg: "should be > 0"}], 
          editor: {                   
             type: "textbox",
                init: changeStatus,
                options: []
             }
          },
          { title: "DR/CR", width: 100, dataIndx: "bal_type",editable: false ,editor: {
              type: 'select',
              init: changeStatus,
              options: drcrlist
          }},
            
        ];
         var dataModel = {
            location: "remote",
            dataType: "json",
            method: "POST",
			url: "<?php echo base_url();?>admin/accounts/ajax_accounts_view",			
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
		var loadStateSuccess;   
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: "remote", rPP: 10, strRpp: "{0}" },
            dataModel: dataModel,
            colModel : colModel,
            filterModel: { on: true, mode: "OR", header: true, type:'remote' },
            numberCell: { show: true },            
            editable: true,
			dataReady: calculateSummary,
            editModel: { clicksToEdit: 1},
            showTitle: true,		
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
                              if(column.dataIndx!='chkbx'){ 

                                if(column.dataIndx=='account_name'){ 
                                  obj[column.dataIndx] = column.title;
                                  opts.unshift(obj);
                                }
                                else{
                                  obj[column.dataIndx] = column.title;
                                  opts.push(obj);
                                }  
                                  
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
                            { "begin": "Begins With" },                            ,
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
	   var acc_short_code     = rowData.acc_short_code;
      $("#grid_search").pqGrid('saveState');
          var select_rowindx =  set_page();
		window.location.href= baseurl+'admin/accounts/modify/'+acc_id+'?rowIndx='+select_rowindx;
	 	

	  /* if (acc_short_code=='usraccn'){
		window.location.href= baseurl+'admin/accounts/modify/'+acc_id+'?rowIndx='+select_rowindx;
	   }
	   else{
		   alert_notification('THIS IS A SYSTEM GENERATED A/C. ');
		   return false;
	   } */
    } 
       
    newObj.cellKeyDown = function(evt, ui) {
      var rowData      = ui.rowData;
      var acc_id     = rowData.acc_id;
	  var acc_short_code     = rowData.acc_short_code;
      //console.log(rowData);
	  if (evt.keyCode==13){
		if ( acc_short_code=='usraccn'){
       $("#grid_search").pqGrid('saveState');
               var select_rowindx =  set_page();
        window.location.href= baseurl+'admin/accounts/modify/'+acc_id+'?rowIndx='+select_rowindx;
      }else{
		   alert_notification('THIS IS A SYSTEM GENERATED A/C. ');
		   return false;
	   }  
		  
	  }
      
    }

       $grid  =  $("#accounts_grid").pqGrid(newObj);
	   pq.grid("#accounts_grid", newObj)
            .on("refresh refreshCell", function (evt, ui) {
                if (ui.source != 'flex') {
                    this.flex();
                }
            });
	  
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
  var ischeckled =  $('.accounts_row:checked').length;  
    if(ischeckled==0){
      alert_notification("First select a account to edit!!");
    }
      else{   
     var sel_id = $('.selected_cell').data('id'); 
     if(sel_id!='')
      window.location.href=baseurl+"admin/accounts/modify/"+sel_id;
     else
      return false;
     }
    }) 
    
    
    
 $(document).on('click',"#active_inactive",function(){
	 var select_row = $("#accounts_grid").pqGrid("selection", { type:'row', method:'getSelection'});
       var rowinfo = select_row[0].rowData;
	   if( rowinfo.acc_is_restrict==2 || rowinfo.acc_is_restrict==3){
	   alert_notification("THIS IS A SYSTEM GENERATED A/C.");
	   return false;
	   }
	   
	 
	 
    var ischeckled =  $('.accounts_row:checked').length;  
     if(ischeckled==0){
      alert_notification("First select a account to active/inactive!!");
      }
      else{   
       var checkedVals = $('input[name="account_ids[]"]:checked').map(function() {
       return this.value;
    }).get();
    
	
     if(checkedVals!=''){
         
         
       var checkedVals_status = [...new Set($('input[name="account_ids[]"]:checked').map(function() {
    return $(this).attr("data-confirmstatus");
}).get())];

       var pss_status_val = [...new Set($('input[name="account_ids[]"]:checked').map(function() {
    return $(this).attr("data-acc_status_vl");
}).get())];

if(pss_status_val==0)
   var mark_button_label ='MARK INACTIVE';
   
  else
  var mark_button_label ='MARK ACTIVE';
   
   
         Swal.fire({
            title: '',
            html: "ARE YOU SURE TO "+checkedVals_status+" THE ACCOUNT MASTER FOR F.Y. <?= company()->fy_short ?> <br /> <br />NOTE: <small>THIS ACCOUNT MASTER, WILL NOT BE CARRY FORWARD TO NEXT FINANCIAL YEAR</small> ",
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
              url: '<?php echo base_url(); ?>/admin/accounts/changestatus', 
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
					  alert_success("Account status has been changed");
                   
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
    }) 
    
    
    
    

  $(document).on('click',".deletebtn",function(){
	  var select_row = $("#accounts_grid").pqGrid("selection", { type:'row', method:'getSelection'});
       var rowinfo = select_row[0].rowData;
	   if( rowinfo.acc_is_restrict==2 || rowinfo.acc_is_restrict==3){
	   alert_notification("THIS IS A SYSTEM GENERATED A/C.");
	   return false;
	   }
	   
  var ischeckled =  $('.accounts_row:checked').length;  
     if(ischeckled==0){
      alert_notification("First select a account to delete!!");
      }
      else{   
       var checkedVals = $('input[name="account_ids[]"]:checked').map(function() {
       return this.value;
    }).get();
    
	
	
     if(checkedVals!=''){
		 var url = baseurl+"admin/accounts/remove_accounts/"+btoa(checkedVals.join(","));
		 let newUrl = url.replace(/=(?=[^=]*$)/, ""); // Removes only the last '='
		 confirm_delete(newUrl);      
	 }
     else
      return false;
     }
    }) 
  
  
  $(document).on('click','.exportexcelbtn', function(e) { 
   // window.location.href=baseurl+"admin/accounts/export_excel";
  });
  
  
  $("#updategrid_changes").on("click",function(){
        var pq_grids = $("#accounts_grid");
        var data = pq_grids.pqGrid('option', 'dataModel.data'); 
		var branchid    = $('select[name="branchid"] option:selected').val();
		if( typeof branchid =="undefined"){
				 var branchid = '<?php echo $bo_id;?>';  
			  }	
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
						"branchid"   : branchid,
						"isedited":isedited
                   });
                
            }   
       }
       
   $("#accbaldata").val(JSON.stringify(accounts_balance_data));
  show_loader();   
  $("#salefrm").submit();
  })
  
  
  
  $("#bulk_update_opn_balances").on("click",function(){
    
        var pq_grids = $("#accounts_grid");
        var data = pq_grids.pqGrid('option', 'dataModel.data');    
      var colM=pq_grids.pqGrid( "option" , "colModel" );     
          colM[5].editable = true;
          colM[6].editable = true;
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
</script>
</body>
</html>