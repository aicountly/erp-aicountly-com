<?php $header = array( 	'title' => 'Open Company' ); ?>
<?php echo view('includes/header2',$header); ?>

<?php
$set_search = (!empty($_GET['search'])) ? $_GET['search'] : '';

?>



    <div class="row mb-4 myalltabs px-3 bg bg-success">
      <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
		<a href="<?php echo base_url();?>/home/open_company" ><span id="#" class="btn tabslist">My Company</span></a>
		<a href="<?php echo base_url();?>/sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
		<a href="<?php echo base_url();?>/archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
      </div>
    </div>

    <div class="content">
       <div class="pb-5"><style>
.list-inline a{color:#000;}
    .list-inline a.active{color:#25b003;}
    .acesstabs{display:flex; position:relative; justify-content: space-around;}
    .acesstabs::after{height:2px; width:100%; background:#1d528c; position:absolute; top:30px; content:'';}
  .acesstabs a{width:60px; height:60px; background:#1d528c; font-weight:bold; z-index:1; color:#fff; border-radius:50%; text-align:center; line-height:60px; font-size:32px; } 
  .acesstabs a.active{ background:#25b003; color:#fff;}
    
</style>


		       <h3 class="pb-3">Transfer Ownership</h3>
            

<div class="col-12">
    
        <p class="acesstabs" id="myTab" role="tablist">
    <a class="active" id="acess1-tab" data-bs-toggle="tab" data-bs-target="#acess1-panel" type="button" role="tab" aria-controls="acess1-panel" aria-selected="true">1</a>
    <a class="" id="acess2-tab" data-bs-toggle="tab" data-bs-target="#acess2-panel" type="button" role="tab" aria-controls="acess2-panel" aria-selected="false" tabindex="-1">2</a>
    <a class="" id="acess3-tab" data-bs-toggle="tab" data-bs-target="#acess3-panel" type="button" role="tab" aria-controls="acess3-panel" aria-selected="false" tabindex="-1">3</a>
</p>

<div class="tab-content border-0 accordion form-outline mb-4" id="myTabContent">

  
   <!-- Access Tab 1 Starts here ---->

          <div class="tab-pane border-0 fade accordion-item active show" id="acess1-panel" role="tabpanel" aria-labelledby="acess1-tab" tabindex="0">
    
     
	  <div class="mt-5" id="mygrid"> </div>
	  
   <!--<div class="row head h5 border-bottom mb-2 pb-2">
     <div class="col"><input type="checkbox"> Compnay Code</div>
     <div class="col">Name</div>
     <div class="col">Ownered by</div>
  </div>
   <div class="row border-bottom py-2 selected">
     <div class="col"><input type="checkbox"> AI 0001</div>
     <div class="col">Manpreet Saini</div>
     <div class="col">Harry Peamber - A little description goes here</div>
  </div>
   <div class="row border-bottom py-2">
     <div class="col"><input type="checkbox"> CA 100203 </div>
     <div class="col">Manpreet Saini</div>
      <div class="col">Harry Peamber - A little description goes here</div>
  </div>
   <div class="row border-bottom py-2">
     <div class="col"><input type="checkbox"> PROFS 00212</div>
     <div class="col"> Manpreet Saini</div>
     <div class="col">Harry Peamber - A little description goes here</div>
  </div>
 -->
    
     
       
       <p class="text-end"><a href="#" class="btn btn-outline-success btnNext m-2">Manage Permissions »</a></p>
         </div>
    
<!-- Access Tab 1 Ends here ---> 
  
   

 
    <!-- Access 2 Tab -->
  <div class="tab-pane border-0 fade accordion-item" id="acess2-panel" role="tabpanel" aria-labelledby="acess2-tab" tabindex="0">
   <h5>Transfer Persmissions</h5>
 <div class="card p-4 mt-2">

    <div class="row">
         <div class="col-md-4 border-end">
            <h5 class="pb-2"> Permissions</h5>
            <ul class="list-inline list-group" id="myTab" role="tablist">
                <li><a class="active" id="t1-primetab" data-bs-toggle="tab" data-bs-target="#t1-primepanel" type="button" role="tab" aria-controls="t1-primepanel" aria-selected="true">AI Contly Group</a></li>
                <li><a id="t2-primetab" data-bs-toggle="tab" data-bs-target="#t2-primepanel" type="button" role="tab" aria-controls="t2-primepanel" aria-selected="false" tabindex="-1">Prime Core &amp; Company</a></li>
                <li><a id="t3-primetab" data-bs-toggle="tab" data-bs-target="#t3-primepanel" type="button" role="tab" aria-controls="t3-primepanel" aria-selected="false" tabindex="-1">AK Industrial Group</a></li>
                <li><a id="t4-primetab" data-bs-toggle="tab" data-bs-target="#t4-primepanel" type="button" role="tab" aria-controls="t4-primepanel" aria-selected="false" tabindex="-1">Taxation Logistics</a></li>
                <li><a id="t5-primetab" data-bs-toggle="tab" data-bs-target="#t5-primepanel" type="button" role="tab" aria-controls="t5-primepanel" aria-selected="false" tabindex="-1">Ram &amp; Sons Permissions</a></li>
                
            </ul>
         </div>
         
          <div class="col-md-8">
            
      <div class="tab-content border-0 accordion form-outline p-0" id="myTabContent">      
      <div class="tab-pane fade show active p-0" id="t1-primepanel" role="tabpanel" aria-labelledby="t1-primetab" tabindex="0">
           <h5 class="pb-2">AI Contly Group</h5>
           <p>Enter Registered Email of Transfer</p>
           <p class="col-md-6"><input type="text" name="remail" class="form-control"></p>
        <ul class="list-inline">
        <li class="list-group-item d-flex"><span class="col-6 fw-bold">Username :</span> <span class="col-6">MY Name goes here
        </span></li><li class="list-group-item d-flex"><span class="col-6 fw-bold">Registerd Email :</span> <span class="col-6">myemial@gmail.com</span></li>
        <li class="list-group-item d-flex"><span class="col-6 fw-bold">Regd WA Mobile :</span> <span class="col-6">+91 222 4444 5555</span></li>
            </ul>
    </div>
    
     <div class="tab-pane fade p-0" id="t2-primepanel" role="tabpanel" aria-labelledby="t2-primetab" tabindex="0">
          <h5 class="pb-2">Prime Core &amp; Company</h5>
    <p>Enter Registered Email of Transfer</p>
           <p class="col-md-6"><input type="text" name="remail" class="form-control"></p>
        <ul class="list-inline">
        <li class="list-group-item d-flex"><span class="col-6 fw-bold">Username :</span> <span class="col-6">MY Name goes here
        </span></li><li class="list-group-item d-flex"><span class="col-6 fw-bold">Registerd Email :</span> <span class="col-6">myemial@gmail.com</span></li>
        <li class="list-group-item d-flex"><span class="col-6 fw-bold">Regd WA Mobile :</span> <span class="col-6">+91 222 4444 5555</span></li>
            </ul>
    </div>
    
         <div class="tab-pane fade p-0" id="t3-primepanel" role="tabpanel" aria-labelledby="t3-primetab" tabindex="0">
              <h5 class="pb-2">AK Industrial Group</h5>
         <p>Enter Registered Email of Transfer</p>
           <p class="col-md-6"><input type="text" name="remail" class="form-control"></p>
        <ul class="list-inline">
        <li class="list-group-item d-flex"><span class="col-6 fw-bold">Username :</span> <span class="col-6">MY Name goes here
        </span></li><li class="list-group-item d-flex"><span class="col-6 fw-bold">Registerd Email :</span> <span class="col-6">myemial@gmail.com</span></li>
        <li class="list-group-item d-flex"><span class="col-6 fw-bold">Regd WA Mobile :</span> <span class="col-6">+91 222 4444 5555</span></li>
            </ul>
    </div>
    </div>
            
      
         </div>

      </div>




 
 </div>
       
        <p class="text-end"><a href="#" class="btn btn-outline-success m-2 btnPrevious">« Select Contact</a> <a href="#" class="btn btn-outline-success btnNext m-2">Finish »</a></p>  
  </div>
  <!-- Access Tab 3 Ends here --->
  
  
  


<!-- Access Tab 3 Starts here ------>

  <div class="tab-pane border-0 fade accordion-item" id="acess3-panel" role="tabpanel" aria-labelledby="acess3-tab" tabindex="0">

 <div class="card p-4 mt-2">
     <div class="m-5 p-5 text-center m-auto">
         <h2>TRANSFER SUCCESSFULL</h2>
         <p class="py-4">
         Note : Transfer Invite has been send to user to accept invitation within 24 hours 
         </p>
         <a href="#" class="btn btn-success btn-lg">Go To Dashboard</a>
     </div>
     </div>
     
      <p class="text-end"><a href="#" class="btn btn-outline-success m-2 btnPrevious">« Manage Permissions</a></p>
     </div>
<!-- Access Tab 3 Ends here --------->


   
  <!-- Manage Access Modal Ends here --->  

          
          


        </div>
      </div>
      
    </div>
     
     
  
           
   <?php echo view('includes/footer_scripts'); ?>		  
	
	<script>

    $(document).on('change', '#select_all_checkbox', function(){
        if(this.checked) 
            $('.select_one_checkbox').prop("checked", true);
        else
            $('.select_one_checkbox').prop("checked", false);
    });

    $(document).on('change', '.select_one_checkbox', function(){
        $('#select_all_checkbox').prop("checked", false);
    });

    $(document).on('change', '.select_checkbox', function(){
        var total_companies = $('.select_one_checkbox:checked').length;
        if(total_companies > 0)
          $('#multiple_company_access').attr('disabled', false);
        else
          $('#multiple_company_access').attr('disabled', 'disabled');
    });


     $(function () {
        
        var colModel = [
            { dataIndx: "state", maxWidth: 40, minWidth: 40, align: "center", resizable: false,
                title: '<input type="checkbox" value="" class="select_checkbox" id="select_all_checkbox">',
                menuIcon: false,
                cls: 'pq-grid-number-cell', 
                sortable: false, 
                
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
               
                    return '<input type="checkbox" data-ownership="'+rd.ownership+'" data-comp_type="'+rd.comp_type+'" value="'+rd.comp_id+'" class="select_checkbox select_one_checkbox">';
                }
            },

            { title: "COMPANY NAME", width: 100, dataIndx: "comp_name" },
            { title: "COMPANY CODE", width: 100, dataIndx: "comp_code" },
            { title: "MANAGE", width: 100, dataIndx: "manage", sortable: false,
              render: function (ui) {

                  var rowData = ui.rowData;
                  var html = ``;
                  
                  html += `<a href="javascript:void(0)" type='button' class='share_single_company mx-2' title="COMPANY ACCESS" alt="COMPANY ACCESS">
                          <img src="/public/assets/img/add_task.png" title="COMPANY ACCESS" alt="COMPANY ACCESS">
                      </a>`;
                  html += `<a href="javascript:void(0)" type='button' title="MANAGE PERMISSIONS" alt="MANAGE PERMISSIONS" class='user_permission mx-2'>
                          <img src="/public/assets/img/manage_history.png" title="MANAGE PERMISSIONS" alt="MANAGE PERMISSIONS">
                      </a>`;
                  html += `<a href="javascript:void(0)" type='button' title="MANAGE ACCESS" alt="MANAGE ACCESS" class='manage_user_access mx-2'>
                          <img src="/public/assets/img/lock_person.png" title="MANAGE ACCESS" alt="MANAGE ACCESS">
                      </a>`;
                  
                  return html;
              }, 
              postRender: function (ui) {
                  var rowIndx = ui.rowIndx,
                      grid = this,
                      $cell = grid.getCell(ui);

                  $cell.find(".share_single_company")
                  .bind("click", function (evt) {
                      share_single_company(rowIndx, grid);
                  });

                  $cell.find(".user_permission")
                  .bind("click", function (evt) {
                      user_permission(rowIndx, grid);
                  });

                  $cell.find(".manage_user_access")
                  .bind("click", function (evt) {
                      manage_user_access(rowIndx, grid);
                  });
              }
            },
            
        ];
        var dataModel = {"data":<?php echo json_encode($company_list);?>}
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
            showTitle: false,
            postRenderInterval: -1, //synchronous post rendering.
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
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
                        listener: { keyup: filterhandler }
                    },

                ]
            }
        };
        
       
        var $grid = $("#mygrid").pqGrid(newObj);
         
        function filterhandler(evt, ui) {

            var $toolbar = $grid.find('.pq-toolbar-search'),
                $value = $toolbar.find(".filterValue"),
                value = $value.val(),
                condition = 'contain',//$toolbar.find(".filterCondition").val(),
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

        function share_single_company(rowIndx, grid) {

          rd = grid.getRowData({ rowIndx: rowIndx });
          reset_company_access_modal();

          $('#useraccess .label').text(rd.comp_name);

          $('#useraccess input[name="comp_id"]').val(rd.comp_id);
          $('#useraccess input[name="comp_type"]').val(rd.comp_type);
          $('#useraccess input[name="ownership"]').val(rd.ownership);
          $('#useraccess input[name="type"]').val('0');
      
          $('#useraccess').modal('show');

            // grid.refreshRow({ rowIndx: rowIndx });
            
        }

        function user_permission(rowIndx, grid) {

          rd = grid.getRowData({ rowIndx: rowIndx });
      
          $('#advance').modal('show');

            // grid.refreshRow({ rowIndx: rowIndx });
            
        }

        function manage_user_access(rowIndx, grid) {

          rd = grid.getRowData({ rowIndx: rowIndx });

          var comp_id = rd.comp_id;
          var comp_type = rd.comp_type;

          $.ajax({
              url: '<?php echo $base_url ?>/company_access/get_company_access_users', 
              type: 'post',
              data: {comp_id:comp_id, comp_type:comp_type},
              datatype: "json",
              cache: false,
              beforeSend: function() {
                  $('.access_users_list_btn').css('pointer-events', 'none');
              },
              success: function (response) {
                  
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  // console.log(response); return;
                  if(response.status){
                      var html = ``;
                      $.each(response.data, function(index,value){
                          var color = get_random_color();
                          html += `
                          <li class="list-group-item">
                            <div class="align-items-center position-relative m-1">
                            <div class="float-end">
                              <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">Manage</a>
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item remove_access_btn" href="javascript:void(0)" data-idaccess="${value.idaccess}">Remove</a></li>
                                
                              </ul>   
                            </div>
                            <div class="d-flex">
                              <div class="avatar avatar-xl me-3">
                                <div class="avatar-name2 ${color} rounded-circle">
                                  <span>${get_capitals(value.user_firstname, value.user_lastname)}</span>
                                </div>
                              </div>
                              <div class="flex-1 me-sm-3">
                                <h6 class="text-black">${value.user_firstname + ' ' + value.user_lastname}</h6>
                                ${ (!value.status) ? '<span class="badge rounded-pill bg-warning">Not Registered</span>' : '' }
                                <p class="mb-0">${value.user_regdemail}</p>
                              </div>
                            </div>
                            </div>
                          </li>
                       `;
                      })
                      $('#access_user_list').html(html); 

                      const bsOffcanvas = new bootstrap.Offcanvas('#manageaccess');
                      bsOffcanvas.show(); 
                  }
                  
              },
              complete: function() {
                  $('.access_users_list_btn').css('pointer-events', '');
              },
              error: function (jqXHR, exception) {
                  if (jqXHR.status === 0) {
                      alert('Not connect.\n Verify Network.');
                  } else if (jqXHR.status == 404) {
                      alert('Requested page not found. [404]');
                  } else if (jqXHR.status == 500) {
                      alert('Internal Server Error [500].');
                  } else if (exception === 'parsererror') {
                      alert('Requested JSON parse failed.');
                  } else if (exception === 'timeout') {
                      alert('Time out error.');
                  } else if (exception === 'abort') {
                      alert('Ajax request aborted.');
                  } else {
                      alert('Uncaught Error.\n' + jqXHR.responseText);
                  }          
              },
          });
  
        }   
    });
    
    
    
    
    var contacts_array = [];

    function get_random_color() {
        var colors = ['color1','color2','color3','color4','color5','color6','color7','color8'];
         return colors[Math.floor((Math.random()*colors.length))];
    }
    function get_capitals(first_name, second_name)
    {
        var first = first_name != '' ? ( typeof first_name == 'number' ? first_name : first_name.charAt(0)) : '';
        var second = second_name != '' ? second_name.charAt(0) : '';
        return (first+second).toUpperCase();
    }
    function validateEmail(email) {
      var re = /\S+@\S+\.\S+/;
      return re.test(email);
    }
   
    
    function reset_company_access_modal()
    {
        $('#useraccess input[name="search_user"]').val('');
        $('#useraccess input[name="comp_id"]').val('');
        $('#useraccess input[name="comp_type"]').val('');
        $('#useraccess input[name="ownership"]').val('');
        $('#useraccess .label').text();

        $('#search_user_status').html('');
        $('#contact_list').html('');

        contacts_array = [];
    }
    $('#search_user_btn').click(function(){
        search_user();
    });
    
    $('[name="search_user"]').keypress(function (e) {
     var key = e.which;
     if(key == 13)  // the enter key code
      {
        search_user();
      }
    });
    function search_user()
    {
        var search = $('#useraccess input[name="search_user"]').val().trim();
        var comp_id = $('#useraccess input[name="comp_id"]').val();
        var comp_type = $('#useraccess input[name="comp_type"]').val();

        if(search)
        {
           $.ajax({
                url: '<?php echo $base_url ?>/company_access/contacts', 
                type: 'post',
                data: {search: search, comp_id:comp_id, comp_type:comp_type},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#useraccess input[name="search_user"]').attr('disabled', 'disabled');
                    $('#search_user_btn').attr('disabled', 'disabled');
                    $('#search_user_status').html('');
                },
                success: function (response) {
                    
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    // console.log(response); return;
                    if(response.status){
                        
                        $.each(response.data, function(index, object){
                            var i = contacts_array.findIndex(function(obj) {
                              return (obj.uuid == object.uuid && obj.user_regdemail == object.user_regdemail && obj.user_regdmobile == object.user_regdmobile);
                            });
                            if(i == -1){
                                object['color'] = get_random_color();
                                contacts_array.push(object);
                            }
                        });
                        load_contacts();

                        var html = `<small class="text-success">Contact list is updated</small>`;
                        $('#search_user_status').html(html);

                        $('[name="search_user"]').val(''); //focus not working
                        setTimeout(function(){
                            $('[name="search_user"]').focus();
                        });
                    }
                    else{
                        var html = `<small class="text-danger">No registered contact found</small>`;
                        $('#search_user_status').html(html);
                    }
                    
                },
                complete: function() {
                    $('#useraccess input[name="search_user"]').attr('disabled', false);
                    $('#search_user_btn').attr('disabled', false);
                },
                error: function (jqXHR, exception) {
                    if (jqXHR.status === 0) {
                        alert('Not connect.\n Verify Network.');
                    } else if (jqXHR.status == 404) {
                        alert('Requested page not found. [404]');
                    } else if (jqXHR.status == 500) {
                        alert('Internal Server Error [500].');
                    } else if (exception === 'parsererror') {
                        alert('Requested JSON parse failed.');
                    } else if (exception === 'timeout') {
                        alert('Time out error.');
                    } else if (exception === 'abort') {
                        alert('Ajax request aborted.');
                    } else {
                        alert('Uncaught Error.\n' + jqXHR.responseText);
                    }          
                },
            }); 
        }
        else{
            var html = `<small class="text-danger">Please enter valid email address</small>`;
            $('#search_user_status').html(html);
        }
    }
    function load_contacts()
    {
        $('#contact_list').html('');
        $.each(contacts_array, function(index,value){
            var html = `
            <li class="nav-item d-block w-100 p-1 mt-1">
                <div class="dropdown float-end">
                    <a class="dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Editor </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Author</a></li>
                        <li><a class="dropdown-item" href="#">Viewer</a></li>
                        <li><a class="dropdown-item" href="#">Owner</a></li>
                    </ul>
                    <button type="button" data-uuid="${value.uuid}" data-user_regdemail="${value.user_regdemail}" data-user_regdmobile="${value.user_regdmobile}" class="btn-close ms-2 remove_contact" aria-label="Close" href="javascript:void(0)"></button>
                </div>
                <div class="avatar avatar-l me-3 float-start">
                      <div class="avatar-name ${value.color} rounded-circle"><span>${get_capitals(value.user_firstname, value.user_lastname)}</span></div>
                </div>
                <h6>${value.user_firstname + ' ' + value.user_lastname}</h6>
                ${value.user_regdemail ? value.user_regdemail : ''}
                ${value.user_regdmobile ? value.user_regdmobile : ''}

                ${ (value.company_status == 'shared') ? '<span class="badge rounded-pill bg-success">Shared</span>' : '' }

                ${ (value.company_status == 'unregistered') ? '<span class="badge rounded-pill bg-danger">Not Registered</span>' : '' }

                ${ (value.company_status == 'mobile') ? '<span class="badge rounded-pill bg-success">Mobile</span>' : '' }

                ${ (value.uuid == '-1') ? '<input data-email="'+value.user_regdemail+'" type="text" maxlength="10"  name="user_regdmobile" class="form-control d-inline" placeholder="Mobile" style="width: 50%; height: 50%">' : '' }

                ${ (value.uuid == '-2') ? '<input data-mobile="'+value.user_regdmobile+'" type="text" name="user_regdemail" class="form-control d-inline" placeholder="Email" style="width: 50%; height: 50%">' : '' }
            </li>
         `;
        $('#contact_list').append(html);
        })
    }
    $(document).on('click', '.remove_contact', function(){
        var uuid = $(this).data('uuid');
        var index = contacts_array.findIndex(function(obj) {
          return obj.uuid == uuid;
        });
        if(index != -1){
            contacts_array.splice(index, 1);
            var html = `<small class="text-success">Contact removed.</small>`;
            $('#search_user_status').html(html);
        }
        load_contacts();
    });

    $(document).on('click','#company_access_done', function(){

        var type = $('#useraccess input[name="type"]').val();
        var company_id_array = [];
        if(type == ''){
            alert('something went wrong');
            return false;
        }
        if(type == 0){
            var comp_id = $('[name="comp_id"]').val();
            var comp_type = $('[name="comp_type"]').val();
            company_id_array.push({
                        comp_id : comp_id,
                        comp_type : comp_type,
                      });
        }
        if(type == 1){
            $('.select_one_checkbox:checked').each(function(){

                var comp_id = $(this).val();
                var comp_type = $(this).data('comp_type');
                
                company_id_array.push({
                        comp_id : comp_id,
                        comp_type : comp_type,
                      });
                
            });
        }

        if(company_id_array.length == 0){
            alert('something went wrong');
            return false;
        }
        
        // var uuid_array = contacts_array.map(function(obj) { return obj.uuid; });

        var final_contacts_array = structuredClone(contacts_array);
        if(final_contacts_array.length > 0)
        {
          
          var error = false; 
          $.each(final_contacts_array, function(index,object){
            if(object.uuid == -1){
              var mobile = $('input[data-email="'+object.user_regdemail+'"]').val();
              // if(!mobile.trim()){
              //   alert('Please provide mobile no for '+ object.user_regdemail);
              //   error = true;
              // }
              // else{
                final_contacts_array[index]['user_regdmobile'] = mobile.trim();
              // }
            }
          });
          if(error){ return false; }

          $.each(final_contacts_array, function(index,object){
            if(object.uuid == -2){
              var email = $('input[data-mobile="'+object.user_regdmobile+'"]').val();
              if(email.trim()){

                var email_array = final_contacts_array.map(function(obj) { return obj.user_regdemail; });
                if(!email_array.includes(email)){
                  final_contacts_array[index]['user_regdemail'] = email.trim();
                }
                else{
                  alert('Duplicate Email for '+object.user_regdmobile);
                  error = true;
                }
              }
            }
          });

          if(error){ return false; }
          
        
            $.ajax({
                url: '<?php echo $base_url ?>/company_access/add_access', 
                type: 'post',
                data: {company_id_array: company_id_array, contacts_array: final_contacts_array},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#company_access_done').attr('disabled', 'disabled');
                },
                success: function (response) {
                    
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    // console.log(response); return;
                    if(response.status == 200){
                        alert(response.message);
                        $('#useraccess').modal('hide');
                        reset_company_access_modal();
                        $('#company_checkbox_all').prop('checked', false).trigger('change'); 
                       
                    }
                    if(response.status == 400){
                        alert(response.message);
                    }
                    
                },
                complete: function() {
                    $('#company_access_done').attr('disabled', false);
                },
                error: function (jqXHR, exception) {
                    if (jqXHR.status === 0) {
                        alert('Not connect.\n Verify Network.');
                    } else if (jqXHR.status == 404) {
                        alert('Requested page not found. [404]');
                    } else if (jqXHR.status == 500) {
                        alert('Internal Server Error [500].');
                    } else if (exception === 'parsererror') {
                        alert('Requested JSON parse failed.');
                    } else if (exception === 'timeout') {
                        alert('Time out error.');
                    } else if (exception === 'abort') {
                        alert('Ajax request aborted.');
                    } else {
                        alert('Uncaught Error.\n' + jqXHR.responseText);
                    }          
                },
            });
        }
        else{
            var html = `<small class="text-danger">Please add some contacts first.</small>`;
            $('#search_user_status').html(html);
        }
    })

    

    

    $(document).on('change','#company_checkbox_all', function(){
        $('input[name="company_checkbox"]').prop('checked', this.checked);      
    });

    
    $(document).on('click','#multiple_company_access', function(){

        var count = $('.select_one_checkbox:checked').length;
        if(count > 0)
        {
            reset_company_access_modal();
            var label = count > 1 ? count+' Companies' : count+' Company';
            $('#useraccess .label').text(label);

            $('[name="type"]').val(1);
            $('#useraccess').modal('show');

        }
        else{
            alert('Please select company first');
        }
             
    });


  //------------------------------------------------------------------


    $(document).on('click','.remove_access_btn', function(){
        var obj = $(this);
        var idaccess = $(this).data('idaccess');

        if(confirm('Are you sure you want to remove access?')) {

          $.ajax({
              url: '<?php echo $base_url ?>/company_access/remove_access', 
              type: 'post',
              data: {idaccess:idaccess},
              datatype: "json",
              cache: false,
              beforeSend: function() {
                  $('.remove_access_btn').css('pointer-events', 'none');
              },
              success: function (response) {
                  
                  if (typeof response === 'string') {
                      response = JSON.parse(response);
                  }
                  if(response.status){
                      $(obj).closest("li.list-group-item").remove();
                  }
                  
              },
              complete: function() {
                  $('.remove_access_btn').css('pointer-events', 'auto');
              },
              error: function (jqXHR, exception) {
                  if (jqXHR.status === 0) {
                      alert('Not connect.\n Verify Network.');
                  } else if (jqXHR.status == 404) {
                      alert('Requested page not found. [404]');
                  } else if (jqXHR.status == 500) {
                      alert('Internal Server Error [500].');
                  } else if (exception === 'parsererror') {
                      alert('Requested JSON parse failed.');
                  } else if (exception === 'timeout') {
                      alert('Time out error.');
                  } else if (exception === 'abort') {
                      alert('Ajax request aborted.');
                  } else {
                      alert('Uncaught Error.\n' + jqXHR.responseText);
                  }          
              },
          });

        }
    });
</script> 
 <script>

     $(function () {
                
             $('.btnNext').click(function() {
              const nextTabLinkEl = $('.acesstabs .active').closest('a').next('a')[0];
              const nextTab = new bootstrap.Tab(nextTabLinkEl);
              nextTab.show();
            });
            
            $('.btnPrevious').click(function() {
              const prevTabLinkEl = $('.acesstabs .active').closest('a').prev('a')[0];
              const prevTab = new bootstrap.Tab(prevTabLinkEl);
              prevTab.show();
            }); 
     });

</script> 