<?php $header = array( 	'title' => 'Group Company' ); ?>
<?php echo view('includes/header2',$header); ?>

<style>
.avatar-xxl {
    height: 5rem;
    width: 5rem;
}

.avatar .avatar-name2 {
    font-size: 1rem;
    line-height: 1.2;
    height: 100%;
    width: 100%;
}
.avatar .avatar-name2>span {
    position: absolute;
    top: 53%;
    left: 50%;
    -webkit-transform: translate3d(-50%, -50%, 0);
    transform: translate3d(-50%, -50%, 0);
    font-weight: 600;
}
</style>

    <div class="row mb-4 myalltabs px-3 bg bg-success">
      <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
        <a href="<?php echo base_url();?>/home/companies" >
          <span id="#" class="btn tabslist">All Companies</span>
        </a>
        <a href="<?php echo base_url();?>/home/open_company" >
          <span id="#" class="btn tabslist">My Company</span>
        </a>
        <a href="<?php echo base_url();?>/sharedwithme" >
          <span id="#" class="btn tabslist">Shared With Me</span>
        </a>
        <a href="<?php echo base_url();?>/archivecompany" >
          <span id="#" class="btn tabslist">Archive Company</span>
        </a>
        <a href="<?php echo base_url();?>/groupcompany" >
          <span id="#" class="btn tabslist active">Group Company</span>
        </a>
      </div>
    </div>

<div class="content">
  <div class="pb-3"> 
    <div class="listmenu pb-4" id="listmenu">
     <a href="<?php echo base_url();?>/groupcompany/add" class="btn btn-success">New Group Company</a>
     <a href="javascript:void(0);" class="btn btn-success editcompanybtn">Edit Group Company</a>
     <a href="javascript:void(0);" class="btn btn-success delcompanybtn">Delete Group Company</a>
    </div>
  
    <div id="open_company_search"> </div> 
 
  </div>       
	
	<div class="modal fade" id="GroupMappingModal" tabindex="-1" aria-labelledby="GroupMappingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="GroupMappingModalLabel">Mapping Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="recipient-name" class="col-form-label">Maaster Mappping:</label>
            <input type="text" class="form-control" id="recipient-name">
          </div>
          <div class="mb-3">
            <label for="message-text" class="col-form-label">Criteria:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>

    <!-- Manage Access Modal Starts here --->
    <div class="offcanvas offcanvas-end offtop" tabindex="-1" id="manageaccess" aria-labelledby="manageaccessLabel" style="z-index: 9999">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="manageaccessLabel">Manage Access</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
        </button>
      </div>
      <div class="offcanvas-body">

        <ul class="list-group" id="access_user_list">
          <li class="list-group-item">
            <div class="align-items-center position-relative m-1">
            <div class="float-end">
              <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">Permission</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
              </ul>   
            </div>
            <div class="d-flex">
              <div class="avatar avatar-xl me-3">
                <div class="avatar-name2 color1 rounded-circle">
                  <span>BM</span>
                </div>
              </div>
              <div class="flex-1 me-sm-3">
                <h6 class="text-black">Bhupinder Mahey</h6>
                <p class="mb-0">bhupimahey@gmail.com</p>
                <span class="badge rounded-pill bg-success">Already Member</span>
              </div>
            </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="align-items-center position-relative m-1">
            <div class="float-end">
              <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">Permission</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
              </ul>   
            </div>
            <div class="d-flex">
              <div class="avatar avatar-xl me-3">
                <div class="avatar-name2 color2 rounded-circle">
                  <span>BM</span>
                </div>
              </div>
              <div class="flex-1 me-sm-3">
                <h6 class="text-black">Bhupinder Mahey</h6>
                <p class="mb-0">bhupimahey@gmail.com</p>
                <span class="badge rounded-pill bg-success">Already Member</span>
              </div>
            </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="align-items-center position-relative m-1">
            <div class="float-end">
              <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">Permission</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
              </ul>   
            </div>
            <div class="d-flex">
              <div class="avatar avatar-xl me-3">
                <div class="avatar-name2 color3 rounded-circle">
                  <span>BM</span>
                </div>
              </div>
              <div class="flex-1 me-sm-3">
                <h6 class="text-black">Bhupinder Mahey</h6>
                <p class="mb-0">bhupimahey@gmail.com</p>
                <span class="badge rounded-pill bg-success">Already Member</span>
              </div>
            </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="align-items-center position-relative m-1">
            <div class="float-end">
              <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">Permission</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
              </ul>   
            </div>
            <div class="d-flex">
              <div class="avatar avatar-xl me-3">
                <div class="avatar-name2 color4 rounded-circle">
                  <span>BM</span>
                </div>
              </div>
              <div class="flex-1 me-sm-3">
                <h6 class="text-black">Bhupinder Mahey</h6>
                <p class="mb-0">bhupimahey@gmail.com</p>
                <span class="badge rounded-pill bg-success">Already Member</span>
              </div>
            </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="align-items-center position-relative m-1">
            <div class="float-end">
              <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">Permission</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                <li><a class="dropdown-item" href="#">Dropdown link</a></li>
              </ul>   
            </div>
            <div class="d-flex">
              <div class="avatar avatar-xl me-3">
                <div class="avatar-name2 color5 rounded-circle">
                  <span>BM</span>
                </div>
              </div>
              <div class="flex-1 me-sm-3">
                <h6 class="text-black">Bhupinder Mahey</h6>
                <p class="mb-0">bhupimahey@gmail.com</p>
                <span class="badge rounded-pill bg-success">Already Member</span>
              </div>
            </div>
            </div>
          </li>

          
        </ul>
        
        <hr>
        <a href="#" class="btn btn-outline-success">Settings</a>
      </div>
    </div>
    <!-- Manage Access Modal Ends here ---> 

    <!-- The Modal -->
    <div class="modal" id="infoModal">
      <div class="modal-dialog">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Company Details</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">

            <p>
                <em>Size: </em>
                <span class="comp_size"></span>
            </p>

            <div class="table-responsive">
                <table class="table table-hover table-bordered vouchers_table">
                
                <thead>
                  <tr>
                    <th>FIN. YEAR(S)</th>
                    <th>Vouchers</th>
                  </tr>
               </thead>
               <tbody>
                 
                </tbody>
              </table>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          </div>

        </div>
      </div>
    </div>


<?php echo view('includes/footer_scripts'); ?>		  
	
<script>

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
    
    var colModel = [
     
      { title: "GROUP CODE", width: 100, dataIndx: "comp_code" },
      { title: "GROUP NAME", width: 180, dataIndx: "comp_name" },
      { title: "GROUP ALIAS", width: 180, dataIndx: "comp_alias" },
      { title: "MEMBER COMPANIES", width: 140, dataIndx: "count",
        render: function( ui ) {
          var rd = ui.rowData;
          if(rd.comp_status == 1)
            return '<span class="badge rounded-pill bg-success">'+rd.count+'</span>';
          if(rd.comp_status == 0)
            return '<span class="badge rounded-pill bg-warning">'+rd.count+'</span>';
        }
      },
      { title: "ACTION", width: 100, dataIndx: "manage", sortable: false,
        render: function (ui) {

            var rowData = ui.rowData;
            var html = ``;
            
            html += `<a href="javascript:void(0)" type='button' class='view_company_details mx-2'>
                    <img src="<?= base_url() ?>/public/assets/img/icon-view.png">
                </a>`;
 
            html += `<a href="javascript:void(0)" type='button' class='manage_user_access mx-2'>
                    <img src="<?= base_url() ?>/public/assets/img/lock_person.png">
                </a>`;
            
            return html;
        }, 
        postRender: function (ui) {
            var rowIndx = ui.rowIndx,
                grid = this,
                $cell = grid.getCell(ui);

            $cell.find(".view_company_details")
            .bind("click", function (evt) {
                view_company_details(rowIndx, grid);
            });

            $cell.find(".manage_user_access")
            .bind("click", function (evt) {
                manage_user_access(rowIndx, grid);
            });
        }
      },
      
    ];
    var dataModel = { data: <?= json_encode($companies) ?>};
    var newObj = {
        scrollModel: { autoFit: true },
        height: 'flex',
        collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } },
        selectionModel: { type: 'row',mode:'single' },
        pageModel: { type: 'local' },
        dataModel: dataModel,
        colModel : colModel,
        numberCell: { show: true },
        filterModel: { mode: 'OR' },
        editable: false,
        showTitle: false,
        postRenderInterval: -1, //synchronous post 
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
                { 
                    type: 'select', cls: "filterColumn",
                    listener: filterhandler,
                    options: function (ui) {
                        var CM = ui.colModel;
                        var opts = [{ '': '[ All Fields ]'}];
                        for (var i = 0; i < CM.length; i++) {
                            var column = CM[i];
                            var obj = {};
                            obj[column.dataIndx] = column.title;
                            opts.push(obj);
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
    var encomp_id = rowData.encomp_id;
    select_company(encomp_id)
  } 
   
  newObj.cellKeyDown = function(evt, ui) {
  	var rowData      = ui.rowData;
    var encomp_id = rowData.encomp_id;

    if (evt.keyCode==13){
  	 select_company(encomp_id)
    } 
  }

  async function select_company(encomp_id)
  {
    $(".siteloader").show();
    let start_time = performance.now();

    var interval   = setInterval(function(){
    var currenttime  = performance.now();
    var pct =((currenttime - start_time) / currenttime*100).toFixed(2);
    $('#progressbar').css({width: pct+"%"}, {duration: 2000, easing: 'linear'});
    $('#progressbar').attr('aria-valuenow',pct);
    $('#progressbar .progress-bar').css("width", pct+"%");
    $('#progressbar .progress-bar').text(pct+"%");

    }, 1000); 

    let response = await fetch(baseurl+'/groupcompany/select_company/'+encomp_id);

    let result = await response.json();
    console.log(result);
    requestTime = performance.now();
    if(result){
      clearInterval(interval);
      $('#progressbar').css("width","100%");
      $('#progressbar .progress-bar').css("width", "100%");
      $('#progressbar .progress-bar').text("100%");

      if(result.status)
        window.open(baseurl+'/grpcomp', '_self').focus();
      else
        alert_notification(result.message);
    }
  }
  
  var $grid = $("#open_company_search").pqGrid(newObj);

  function view_company_details(rowIndx, grid) {

        rd = grid.getRowData({ rowIndx: rowIndx });

        var html = ``;

        $('#infoModal .modal-title').html(rd.comp_name);

        if(rd.comp_size == ''){
            html = `<span role="button" data-comp_id="${rd.comp_id}", data-comp_code="${rd.comp_code}" class="badge bg-primary view_size_btn">View</span>`;
        }
        else{
            html = `<span class="badge bg-success">${rd.comp_size}</span>`;
        }


        $('#infoModal .comp_size').html(html);

        html = ``;

        if(rd.fy_list.length){
            rd.fy_list.forEach(function(obj){
                html += `
                    <tr>
                        <td title="${obj.fy_str}" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            ${obj.fy_name}
                        </td>
                        <td>${obj.vouchers}</td>
                    </tr>
                `;
            })
        }

        $('#infoModal .vouchers_table tbody').html(html);
        initTooltip();
        $('#infoModal').modal('show');

        // grid.refreshRow({ rowIndx: rowIndx });
        
    }

    function manage_user_access(rowIndx, grid) {

      rd = grid.getRowData({ rowIndx: rowIndx });

      var comp_id = rd.comp_id;
      var comp_type = rd.comp_type;

      $.ajax({
        url: '<?= base_url() ?>/company_access/get_company_access_users', 
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

    $(document).on('click','.remove_access_btn', function(){
      var obj = $(this);
      var idaccess = $(this).data('idaccess');

      if(confirm('Are you sure you want to remove access?')) 
      {
        $.ajax({
            url: '<?= base_url() ?>/company_access/remove_access', 
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

    $(document).on('click','.view_size_btn', function(){
      var obj = $(this);

      var comp_id = $(this).data('comp_id');
      var comp_code = $(this).data('comp_code');

      $(obj).text('please wait...');

      $.ajax({
          url: '<?= base_url() ?>/home/get_comp_size', 
          type: 'post',
          data: {comp_id: comp_id, comp_code: comp_code},
          dataType: "json",
          cache: false,
          beforeSend: function() {
              $(obj).css('pointer-events', 'none');
          },
          success: function (response) {
              
              if(response.status){
                var comp_size = response.comp_size;
                if(comp_size != ''){
                  var html = `<span class="badge bg-success">${comp_size}</span>`;  
                }
                else{
                    var html = `<span class="badge bg-danger">Error</span>`;
                }
                
                $(obj).parent().html(html);

                var data = $("#open_company_search").pqGrid('option', 'dataModel.data');

                data.forEach(function(rd){
                    if(rd.comp_id == comp_id){
                      rd.comp_size = comp_size;  
                    }
                });
              }
              
          },
          complete: function() {
              $(obj).css('pointer-events', 'auto');
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

    });

    function initTooltip()
    {
        $("body").tooltip({ selector: '[data-toggle=tooltip]' });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    }
    
     
  $(".editcompanybtn").on("click",function(){

    var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});

    if(selectionArray.length==0){
      alert_notification("First select a group to edit!!");
    }else{	  
      var rowData = selectionArray[0]['rowData'];
      var encomp_id = rowData.encomp_id;

      window.location.href= baseurl+'/groupcompany/modify/'+encomp_id;
    }
  })

  $(".delcompanybtn").on("click",function(){
    var selectionArray = $("#open_company_search").pqGrid("selection", {type: 'row', method: 'getSelection'});
    if(selectionArray.length==0){
      alert_notification("First select a group to delete!!");
    }
    else{	  
      var rowData = selectionArray[0]['rowData'];
      var encomp_id = rowData.encomp_id;

      window.location.href= baseurl+'/groupcompany/remove_group/'+encomp_id;
    }
  });
   
});
    

</script> 