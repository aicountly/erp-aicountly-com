<?php $header = array(  'title' => 'Company Lists' ); ?>
<?php echo view('includes/header2',$header); ?>
<?php
$set_search = (!empty($_GET['search'])) ? $_GET['search'] : '';
?>
<style>
#mygrid{box-shadow:none; border:0px; background:transparent;}
tr.pq-grid-oddRow{background:#fff;}
.pq-grid-number-col, .pq-grid-number-cell, .pq-grid-col, .pq-grid-cell{border-right:0px!important;}
/*.pq-grid-footer{background:rgba(37,176,3,1);}
.pq-grid-title-row th, .pq-grid-footer{background:rgba(37,176,3,1); color:#e3e3e3;}
*/
.taskmenus span{padding:0px; font-size:25px;}
.list-inline a{color:#000;}
.list-inline a.active{color:#25b003;}
.accesslist{position:relative; margin-top:20px;}
.accesslist .collapse.show{position:absolute; left:0; right:0; top:0; background:#fff; height:100vh; z-index:999;}
.accesslist a{color:#000; text-decoration:none;}

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
    <a href="<?php echo base_url();?>companies" >
      <span id="#" class="btn tabslist">All Companies</span>
    </a>
    <a href="<?php echo base_url();?>my_companies" >
      <span id="#" class="btn tabslist">My Company</span>
    </a>
    <a href="<?php echo base_url();?>sharedwithme" >
      <span id="#" class="btn tabslist">Shared With Me</span>
    </a>
    
  </div>
</div>
<div class="content">
  <div class="pb-5">
  <style>
.list-inline a{color:#000;}
.list-inline a.active{color:#25b003;}
.acesstabs{display:flex; position:relative; justify-content: space-around;}
.acesstabs::after{height:2px; width:100%; background:#1d528c; position:absolute; top:30px; content:'';}
.acesstabs a{width:60px; height:60px; background:#1d528c; font-weight:bold; z-index:1; color:#fff; border-radius:50%; text-align:center; line-height:60px; font-size:32px; } 
.acesstabs a.active{ background:#25b003; color:#fff;}
    
</style>
    <h3 class="pb-3">Company Access</h3>
    <div class="row">
	<div class="col-12" style="max-width:96%;">
      <!--<div class="col-lg-4 col-md-6">
        <div class="listmenu pb-2" id="listmenu">
          <button href="javascript:void(0)" id="multiple_company_access" class="btn btn-success" disabled>Give Access</button>
          <a href="javascript:void(0);" class="btn btn-success disabled">Manage Access</a>
        </div>
      </div>-->
 
  
    <div class="mt-5" id="mygrid"> </div>
      </div> </div>
    <!-- Give Business Modal Starts here -->
    <div class="modal fade" id="create_business" tabindex="-1" aria-labelledby="useraccessLabel" aria-hidden="true" style="z-index: 9999">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Business ID</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            
            <p>
              Your Business ID not yet created, kindly create from My Account
              <a target="_blank" href="<?php echo base_url() ?>home/business?page=business_id" class="btn btn-sm btn-outline-info">click here</a>
              <br>
              If done, then refresh the page. 
            </p>
            
          </div>
          
        </div>
      </div>
    </div>
    <!-- Give Business Modal Ends here -->
    
    <!-- Give Access Modal Starts here -->
    <div class="modal fade" id="useraccess" tabindex="-1" aria-labelledby="useraccessLabel" aria-hidden="true" style="z-index: 9999">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Company Access</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">

            <?php if(!$business_id) { ?>

            <p>
              Your Business ID not yet created, kindly create from My Account
              <a target="_blank" href="<?php echo base_url() ?>home/business?page=business_id" class="btn btn-sm btn-outline-info">click here</a>
              <br>
              If done, then refresh the page.
            </p>

            <?php } else { ?>


            <input type="hidden" name="ownership" value="">
            <input type="hidden" name="comp_type" value="">
            <input type="hidden" name="comp_id" value="">
            <input type="hidden" name="type" value="">
            <input type="hidden" name="cmpprfle" id="cmpprfle" value="">
            <h5><em class="label text-success"></em></h5>

            <h5 class="p-2 bg-light">Search</h5>
            <div class="input-group mb-1">
              <input name="search_user" type="email" placeholder="Enter Email/ Mobile/ Contact Group" class="form-control">
              <button id="search_user_btn" class="btn btn-success" type="button">Go</button>
            </div>
            <div id="search_user_status">
            </div>
            <h5 class="p-2 bg-light mt-2">Share with People</h5>
            <ul class="nav" id="contact_list">
              
            </ul>
            
            <hr>
			
            <div class="dropdown">
              <a class="dropdown-toggle btn btn-outline-secondary" id="profile_label" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Choose Profile </a>
              <ul class="dropdown-menu" id="profileListUl">
			  <?php if($company_profiles){ 
			    foreach($company_profiles as $row){?>
					<li>
                  <a class="dropdown-item set_cmp_profile" data-id="<?php echo $row['erp_acs_prof_id'];?>" href="javascript:void(0);"><?php echo $row['erp_acs_prof_name'];?></a>
                </li>
				<?php }
			  }else{
			  
			  ?>
                <li>
                  <a class="dropdown-item set_cmp_profile" data-id="0" href="javascript:void(0);">No Profile Listed</a>
                </li>
				
			  <?php } ?>
              </ul>
            </div>
            <small>Only people with this access can open file</small>
            
            <?php } ?>
            
          </div>

          <?php if($business_id) { ?>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="company_access_done">DONE</button>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <!-- Give Access Modal Ends here -->
    <!-- Company User Access Modal Starts here -->
    <div class="modal fade" id="access_users_list_modal" tabindex="-1" aria-labelledby="useraccessLabel" aria-hidden="true" style="z-index: 9999">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Company Access Users</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <ul class="nav" id="access_users_list">
              
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- Give Access Modal Ends here -->
    

    
    <!-- Advance Permissins Modal Starts here -->
    <div class="modal fade modal-xl" id="advance" tabindex="-1" aria-labelledby="advanceLabel" aria-hidden="true" style="z-index: 9999">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Share 001 Key Credentials with 002</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">

            <?php if(!$business_id) { ?>

            <p>
              Your Business ID not yet created, kindly create from My Account
              <a target="_blank" href="<?php echo base_url() ?>home/my_account?page=business_id" class="btn btn-sm btn-outline-info">click here</a>
              <br>
              If done, then refresh the page.
            </p>

            <?php } else { ?>
            <div class="row">
              <div class="col-lg-2 col-6 border-end">
                <h5 class="pb-2"> Permissions</h5>
                <ul class="list-inline" id="myTab" role="tablist">
                  <li>
                    <a class="active" id="t1-primetab" data-bs-toggle="tab" data-bs-target="#t1-primepanel" type="button" role="tab" aria-controls="t1-primepanel" aria-selected="true">Super1 Permissions</a>
                  </li>
                  <li>
                    <a id="t2-primetab" data-bs-toggle="tab" data-bs-target="#t2-primepanel" type="button" role="tab" aria-controls="t2-primepanel" aria-selected="false" tabindex="-1">Prime Permissions</a>
                  </li>
                  <li>
                    <a id="t3-primetab" data-bs-toggle="tab" data-bs-target="#t3-primepanel" type="button" role="tab" aria-controls="t3-primepanel" aria-selected="false" tabindex="-1">Sub Permissions</a>
                  </li>
                  <li>
                    <a id="t4-primetab" data-bs-toggle="tab" data-bs-target="#t4-primepanel" type="button" role="tab" aria-controls="t4-primepanel" aria-selected="false" tabindex="-1">Tab Permissions</a>
                  </li>
                  <li>
                    <a id="t5-primetab" data-bs-toggle="tab" data-bs-target="#t5-primepanel" type="button" role="tab" aria-controls="t5-primepanel" aria-selected="false" tabindex="-1">Content Permissions</a>
                  </li>
                  
                </ul>
              </div>
              
              <div class="col-lg-2 col-6 border-end">
                
                <div class="tab-content border-0 accordion form-outline p-0" id="myTabContent">
                  <div class="tab-pane fade show active p-0" id="t1-primepanel" role="tabpanel" aria-labelledby="t1-primetab" tabindex="0">
                    <h5 class="pb-2">Super Permissions</h5>
                    <ul class="list-inline" id="myTab" role="tablist">
                      <li>
                        <a class="active" id="ts1-tab" data-bs-toggle="tab" data-bs-target="#ts1-subpanel" type="button" role="tab" aria-controls="ts1-subpanel" aria-selected="true">Super1 Permissions</a>
                      </li>
                      <li>
                        <a id="ts2-tab" data-bs-toggle="tab" data-bs-target="#ts2-subpanel" type="button" role="tab" aria-controls="ts2-subpanel" aria-selected="false" tabindex="-1">Prim1 Permissions</a>
                      </li>
                      <li>
                        <a id="ts3-tab" data-bs-toggle="tab" data-bs-target="#ts3-subpanel" type="button" role="tab" aria-controls="ts3-subpanel" aria-selected="false" tabindex="-1">Sub1 Permissions</a>
                      </li>
                      
                    </ul>
                  </div>
                  
                  <div class="tab-pane fade p-0" id="t2-primepanel" role="tabpanel" aria-labelledby="t2-primetab" tabindex="0">
                    <h5 class="pb-2">Prime Permissions</h5>
                    <ul class="list-inline" id="myTab" role="tablist">
                      <li>
                        <a class="active" id="ta1-tab" data-bs-toggle="tab" data-bs-target="#ta1-subpanel" type="button" role="tab" aria-controls="ta1-subpanel" aria-selected="true">Super2 Permissions</a>
                      </li>
                      <li>
                        <a id="ta2-tab" data-bs-toggle="tab" data-bs-target="#ta2-subpanel" type="button" role="tab" aria-controls="ta2-subpanel" aria-selected="false" tabindex="-1">Prim2 Permissions</a>
                      </li>
                      <li>
                        <a id="ta3-tab" data-bs-toggle="tab" data-bs-target="#ta3-subpanel" type="button" role="tab" aria-controls="ta3-subpanel" aria-selected="false" tabindex="-1">Sub2 Permissions</a>
                      </li>
                    </ul>
                  </div>
                  
                  <div class="tab-pane fade p-0" id="t3-primepanel" role="tabpanel" aria-labelledby="t3-primetab" tabindex="0">
                    <h5 class="pb-2">Sub Permissions</h5>
                    <ul class="list-inline" id="myTab" role="tablist">
                      <li>
                        <a id="tb1-tab" data-bs-toggle="tab" data-bs-target="#tb1-subpanel" type="button" role="tab" aria-controls="tb1-subpanel" aria-selected="true">Super3 Permissions</a>
                      </li>
                      <li>
                        <a id="tb2-tab" data-bs-toggle="tab" data-bs-target="#tb2-subpanel" type="button" role="tab" aria-controls="tb2-subpanel" aria-selected="true">Prim3 Permissions</a>
                      </li>
                      <li>
                        <a id="tb3-tab" data-bs-toggle="tab" data-bs-target="#tb3-subpanel" type="button" role="tab" aria-controls="tb3-subpanel" aria-selected="true">Sub3 Permissions</a>
                      </li>
                    </ul>
                  </div>
                </div>
                
                
              </div>
              <div class="col-lg-8">
                
                <div class="tab-content border-0 accordion active form-outline p-0" id="myTabContent">
                  <!-- Tab 1 Content -->
                  <div class="tab-pane fade show active p-0" id="ts1-subpanel" role="tabpanel" aria-labelledby="ts1-tab" tabindex="0">
                    <h5 class="pb-2">Super Advance Permissions</h5>
                    <ul class="nav">
                      <li class="nav-item d-block w-100 my-2">
                        <div class="dropdown float-end">
                          <a class="dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Editor </a>
                          <ul class="dropdown-menu">
                            <li>
                              <a class="dropdown-item" href="#">Author</a>
                            </li>
                            <li>
                              <a class="dropdown-item" href="#">Viewer</a>
                            </li>
                            <li>
                              <a class="dropdown-item" href="#">Owner</a>
                            </li>
                          </ul>
                        </div>
                        <div class="avatar bg-info rounded-circle text-center float-start avatar-m status-online me-3 pt-1">
                          <h4 class="text-white">M</h4>
                        </div>
                        <h6>My Company Name</h6>myemail@gmail.com
                      </li>
                      <li class="nav-item d-block w-100 my-2">
                        <div class="dropdown float-end">
                          <a class="dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Editor </a>
                          <ul class="dropdown-menu">
                            <li>
                              <a class="dropdown-item" href="#">Author</a>
                            </li>
                            <li>
                              <a class="dropdown-item" href="#">Viewer</a>
                            </li>
                            <li>
                              <a class="dropdown-item" href="#">Owner</a>
                            </li>
                          </ul>
                        </div>
                        <div class="avatar float-start avatar-m status-online me-3">
                          <img class="rounded-circle" src="assets/img/30.webp" alt="">
                        </div>
                        <h6>My Company Name</h6>myemail@gmail.com
                      </li>
                      
                    </ul>

                  </div>
                  <!-- Tab1 Ends here --->
                  
                  <!-- Tab 1 Content -->
                  <div class="tab-pane fade p-0" id="ts2-subpanel" role="tabpanel" aria-labelledby="ts2-tab" tabindex="0">
                    <h5 class="pb-2">Prime Permissions</h5>
                    
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                      and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
                      It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    </p>
                    
                  </div>
                  <!-- Tab2 Ends here --->
                  
                  <!-- Tab 3 Content -->
                  <div class="tab-pane fade p-0" id="ts3-subpanel" role="tabpanel" aria-labelledby="ts3-tab" tabindex="0">
                    <h5 class="pb-2">Sub Permissions</h5>
                    
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                      and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
                      It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    </p>
                    
                  </div>
                  <!-- Tab3 Ends here --->
                  
                </div>
                
                
                
                
              </div>
            <?php } ?>
            </div>

            <?php if($business_id) { ?>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary">Copy Link</button>
              <button type="button" class="btn btn-primary">DONE</button>
            </div>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>
    <!-- Advance Permissins Modal Ends here -->
    
    
    
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
    
    
    
  </div>
  <?php echo view('includes/footer_scripts'); ?>	

   <script>


    $(document).on('click', '.set_cmp_profile', function(){
		var cmpprofile_id = $(this).data("id");
		var prfl_name = $(this).text();
		$("#cmpprfle").val(cmpprofile_id);		
		$("#profile_label").html(prfl_name);		
		
	});

    $(document).on('change', '#select_all_checkbox', function(){
        if(this.checked) 
            $('.select_one_checkbox').prop("checked", true);
        else
            $('.select_one_checkbox').prop("checked", false);
    });

   /*  $(document).on('change', '.select_one_checkbox', function(){
        $('#select_all_checkbox').prop("checked", false);
    }); */

    $(document).on('change', '.select_checkbox', function(){
        var total_companies = $('.select_one_checkbox:checked').length;
        if(total_companies > 0)
          $('#multiple_company_access').attr('disabled', false);
        else
          $('#multiple_company_access').attr('disabled', 'disabled');
    });


     $(function () {
        
        var colModel = [
           /* { dataIndx: "state", maxWidth: 40, minWidth: 40, align: "center", resizable: false,
                title: '',
                menuIcon: false,
                cls: 'pq-grid-number-cell', 
                sortable: false, 
                
                render: function( ui ) {
                    var rd = ui.rowData;
                    var grid = this;
               
                    return '<input type="checkbox" data-ownership="'+rd.company_type+'" data-comp_type="'+rd.company_type+'" value="'+rd.company_id+'" class="select_checkbox select_one_checkbox">';
                }
            },*/

            { title: "COMPANY NAME", width: 100, dataIndx: "companyname" },
            { title: "COMPANY CODE", width: 100, dataIndx: "companycode" },
            { title: "MANAGE", width: 100, dataIndx: "manage", sortable: false,
              render: function (ui) {

                  var rowData = ui.rowData;
                  var html = ``;
                  
                  html += `<a href="javascript:void(0)" type='button' class='share_single_company mx-2' title="COMPANY ACCESS" alt="COMPANY ACCESS">
                          <img src="/public/assets/img/add_task.png" title="COMPANY ACCESS" alt="COMPANY ACCESS">
                      </a>`;
                 /* html += `<a href="javascript:void(0)" type='button' title="MANAGE PERMISSIONS" alt="MANAGE PERMISSIONS" class='user_permission mx-2'>
                          <img src="/public/assets/img/manage_history.png" title="MANAGE PERMISSIONS" alt="MANAGE PERMISSIONS">
                      </a>`;*/
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

         var rd = grid.getRowData({ rowIndx: rowIndx });
         
         
          reset_company_access_modal();

          $('#useraccess .label').text(rd.comp_name);
          
          
          $.ajax({
              url: '<?php echo $base_url ?>company_access/get_company_profiles', 
              type: 'POST',
              data: {comp_id:rd.company_id, comp_type:rd.company_type},
              datatype: "json",
              cache: false,
              beforeSend: function() {
                  $('.access_users_list_btn').css('pointer-events', 'none');
              },
              success: function (profiles) {
                 var profiles = JSON.parse(profiles);
                  let html = "";

                // check if array has records
                if (profiles.length > 0) {
                    profiles.forEach(p => {
                        html += `
                            <li>
                                <a class="dropdown-item set_cmp_profile" data-id="${p.profile_id}" href="javascript:void(0);">
                                    ${p.profile_name}
                                </a>
                            </li>
                        `;
                    });
                } else {
                    html = `
                        <li>
                            <a class="dropdown-item set_cmp_profile" data-id="0" href="javascript:void(0);">
                                No Profile Listed
                            </a>
                        </li>
                    `;
                }
                    
                    $("#profileListUl").html(html);
              }
          });
          
          
          

          $('#useraccess input[name="comp_id"]').val(rd.company_id);
          $('#useraccess input[name="comp_type"]').val(rd.company_type);
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

          var comp_id = rd.company_id;
          var comp_type = rd.company_type;
		  

          $.ajax({
              url: '<?php echo $base_url ?>company_access/get_company_access_users', 
              type: 'POST',
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
						  
						  var can_profile_change = value.can_profile_change;
                          var color = get_random_color();
						  if(value.is_owner!='')
						  var is_owner = "("+value.is_owner+")";
					     else 
							 var is_owner ='';
						 var company_profiles_list = value.company_profiles;
						if(value.profile_name!='')
						  var profile_name = "<strong>"+value.profile_name+"</strong>";
					     else 
							 var profile_name ='';
						
					  var profileDropdownHtml = '';	
					  if (company_profiles_list.length > 0 && profile_name!='') {
					      
					      if(can_profile_change==1 && is_owner==''){
					          profileDropdownHtml = `<div class="float-start">
                              <a href="#" class="dropdown dropdown-toggle link-secondary" data-bs-toggle="dropdown" aria-expanded="false">${profile_name}</a>
                              <ul class="dropdown-menu">`;
							  
							company_profiles_list.forEach(function(profile) {
								profileDropdownHtml += `
									<li>
										<a class="dropdown-item profile_select_btn" 
										   href="javascript:void(0)" 
										  data-idaccess="${profile.erp_acs_prof_id}" data-id="${value.uuid}">
											${profile.erp_acs_prof_name}
										</a>
									</li>
								`;
							});
							profileDropdownHtml+= `</ul>   
                            </div>`;
					          
					      }else{
					          
					         profileDropdownHtml ='<h6 class="text-black">'+profile_name+'</h6>'; 
					      }
						   
						   
					  }	
					  
					  
							 
							 
							 
							 
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
                                <h6 class="text-black">${value.user_firstname + ' ' + value.user_lastname}${is_owner}</h6>
                                ${ (!value.status) ? '<span class="badge rounded-pill bg-warning">Not Registered</span>' : '' }
                                <p class="mb-0">${value.user_regdemail}</p>
								 ${profileDropdownHtml}
							
							
                               
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
                url: '<?php echo $base_url ?>company_access/contacts', 
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

                       // var html = `<small class="text-success">Contact list is updated</small>`;
                        //$('#search_user_status').html(html);

                        $('[name="search_user"]').val(''); //focus not working
                        setTimeout(function(){
                            $('[name="search_user"]').focus();
                        });
                    }
                    else{
                        var html = `<small class="text-danger">${response.message}</small>`;
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
		var cmpprfle = $("#cmpprfle").val();	
        var type = $('#useraccess input[name="type"]').val();
        var company_id_array = [];
        if(type == ''){
            alert('kindly select company!!');
            return false;
        }
		else if(cmpprfle=='' || cmpprfle==0){
			 alert('choose profile');
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
          
          var cmpprfle = $("#cmpprfle").val();
            $.ajax({
                url: '<?php echo $base_url ?>company_access/add_access', 
                type: 'post',
                data: {company_id_array: company_id_array, contacts_array: final_contacts_array,prflid:cmpprfle},
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
                        alert_success(response.message);
                        $('#useraccess').modal('hide');
                        reset_company_access_modal();
                        $('#company_checkbox_all').prop('checked', false).trigger('change'); 
                       
                    }
                    if(response.status == 400){
						$("#useraccess #search_user_status").html(`<h6 class="text-danger">${response.message}</h6>`);
                        //alert_notification(response.message);
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

        var count = parseInt($('.select_one_checkbox:checked').length);
		if(count>2)
			count=count-1;
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


 $(document).on('click','.profile_select_btn', function(){
     
      var idaccess = $(this).data('idaccess');
       var uid = $(this).data('id');
       
      $.ajax({
              url: '<?php echo $base_url ?>company_access/change_access', 
              type: 'post',
              data: {idaccess:idaccess,uid:uid},
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
                     alert_success("Profile has been changed successfully.");
                     location.reload();
                  }
                  else
                  alert_notification("Unable to assign profile, try after some time");
              },
              complete: function() {
                 
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



    $(document).on('click','.remove_access_btn', function(){
        var obj = $(this);
        var idaccess = $(this).data('idaccess');

        if(confirm('Are you sure you want to remove access?')) {

          $.ajax({
              url: '<?php echo $base_url ?>company_access/remove_access', 
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