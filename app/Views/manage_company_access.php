<?php $header = array( 	'title' => 'Open Company' ); ?>
<?php echo view('includes/header2',$header); ?>

    <div class="row mb-4 myalltabs px-3 bg bg-success">
      <div class="col-12" id="tabslistings" style="display:inline-block;width:auto;">
		<a href="<?php echo base_url();?>/home/open_company" ><span id="#" class="btn tabslist">My Company</span></a>
		<a href="<?php echo base_url();?>/sharedwithme" ><span id="#" class="btn tabslist">Shared With Me</span></a>
		<a href="<?php echo base_url();?>/archivecompany" ><span id="#" class="btn tabslist">Archive Company</span></a>
      </div>
    </div>

    <div class="content"><div class="pb-5"><style>
.list-inline a{color:#000;}
    .list-inline a.active{color:#25b003;}
    .acesstabs{display:flex; position:relative; justify-content: space-around;}
    .acesstabs::after{height:2px; width:100%; background:#1d528c; position:absolute; top:30px; content:'';}
  .acesstabs a{width:60px; height:60px; background:#1d528c; font-weight:bold; z-index:1; color:#fff; border-radius:50%; text-align:center; line-height:60px; font-size:32px; } 
  .acesstabs a.active{ background:#25b003; color:#fff;}
    
</style>


		       <h3 class="pb-3">Manage Company Access</h3>
            

<div class="col-12">
    
        <p class="acesstabs" id="myTab" role="tablist">
    <a class="active" id="acess1-tab" data-bs-toggle="tab" data-bs-target="#acess1-panel" type="button" role="tab" aria-controls="acess1-panel" aria-selected="true">1</a>
    <a class="" id="acess2-tab" data-bs-toggle="tab" data-bs-target="#acess2-panel" type="button" role="tab" aria-controls="acess2-panel" aria-selected="false" tabindex="-1">2</a>
    <a class="" id="acess3-tab" data-bs-toggle="tab" data-bs-target="#acess3-panel" type="button" role="tab" aria-controls="acess3-panel" aria-selected="false" tabindex="-1">3</a>
    <a class="" id="acess4-tab" data-bs-toggle="tab" data-bs-target="#acess4-panel" type="button" role="tab" aria-controls="acess4-panel" aria-selected="false" tabindex="-1">4</a>
    <a class="" id="acess5-tab" data-bs-toggle="tab" data-bs-target="#acess5-panel" type="button" role="tab" aria-controls="acess5-panel" aria-selected="false" tabindex="-1">5</a>    
</p>

<div class="tab-content border-0 accordion form-outline mb-4" id="myTabContent">

  <!-- Access 1 Tab -->   
  <div class="tab-pane  border-0 fade show active accordion-item" id="acess1-panel" role="tabpanel" aria-labelledby="acess1-tab" tabindex="0">
      
      <h5>Compaines</h5>
      
      <div class="mt-1" id="companies_grid"> </div>
      
        <?php 
//   $json_company = array();
//   if($company_list){ foreach($company_list as $company_row){
//      $json_company[] = array("id"=>$company_row['company_id'],"company_id"=>$company_row['company_id'],"companyname"=>$company_row['company_name'],"companycode"=>$company_row['company_code'],"finyear"=>$company_row['fy_begndt']);
//     }
//   } 

    $json_company1[0] = ['checkbox' => '<input type="checkbox">', 'company_name' => 'Manpreet Saini', 'company_code' => 'AI Group', 'manage' => '<a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#useraccess">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#advance">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="offcanvas" data-bs-target="#manageaccess" aria-controls="manageaccess">lock_person</span></a>'] ;
     $json_company1[1] = ['checkbox' => '<input type="checkbox">', 'company_name' => 'Manpreet Saini', 'company_code' => 'AI Group', 'manage' => '<a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#useraccess">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#advance">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="offcanvas" data-bs-target="#manageaccess" aria-controls="manageaccess">lock_person</span></a>'] ;
     $json_company1[2] = ['checkbox' => '<input type="checkbox">', 'company_name' => 'Manpreet Saini', 'company_code' => 'AI Group', 'manage' => '<a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#useraccess">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#advance">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="offcanvas" data-bs-target="#manageaccess" aria-controls="manageaccess">lock_person</span></a>'] ;
     
  $json_company1 = json_encode($json_company1);
  ?>
  
  <p class="text-end"><a class="btn btn-outline-success btnNext m-2" id="select_user">Select User »</a></p>
  
  </div>
   
    <!-- Access 2 Tab -->
  <div class="tab-pane border-0  fade accordion-item" id="acess2-panel" role="tabpanel" aria-labelledby="acess2-tab" tabindex="0">
   
   <h5>All Users</h5>
   
         <div class="mt-1" id="users_grid"> </div>
      
        <?php 
//   $json_company = array();
//   if($company_list){ foreach($company_list as $company_row){
//      $json_company[] = array("id"=>$company_row['company_id'],"company_id"=>$company_row['company_id'],"companyname"=>$company_row['company_name'],"companycode"=>$company_row['company_code'],"finyear"=>$company_row['fy_begndt']);
//     }
//   } 

    $json_company2[0] = ['user_name' => 'Manpreet Saini', 'user_email' => 'man@gmail.com', 'user_phone' => '9876543210', 'manage' => '<a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#useraccess">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#advance">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="offcanvas" data-bs-target="#manageaccess" aria-controls="manageaccess">lock_person</span></a>'] ;

    $json_company2[1] = ['user_name' => 'Manpreet Saini', 'user_email' => 'man@gmail.com', 'user_phone' => '9876543210', 'manage' => '<a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#useraccess">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#advance">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="offcanvas" data-bs-target="#manageaccess" aria-controls="manageaccess">lock_person</span></a>'] ;

    $json_company2[2] = ['user_name' => 'Manpreet Saini', 'user_email' => 'man@gmail.com', 'user_phone' => '9876543210', 'manage' => '<a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#useraccess">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="modal" data-bs-target="#advance">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined" data-bs-toggle="offcanvas" data-bs-target="#manageaccess" aria-controls="manageaccess">lock_person</span></a>'] ;

  $json_company2 = json_encode($json_company2);
  ?>
   
   <p class="text-end"><a href="#" class="btn btn-outline-success m-2 btnPrevious">« Select Company</a> <a href="#" class="btn btn-outline-success btnNext m-2">Set Permissions »</a></p>
   </div>
 
 
 
    <!-- Access 3 Tab -->
  <div class="tab-pane border-0  fade accordion-item" id="acess3-panel" role="tabpanel" aria-labelledby="acess3-tab" tabindex="0">
   <h5>User Advance Permissions</h5>
   
     <div class="card p-4 mt-2">

    <div class="row">
         <div class="col-md-2 border-end">
            <h5 class="pb-2"> Permissions</h5>
            <ul class="list-inline" id="myTab" role="tablist">
                <li><a class="active" id="t1-primetab" data-bs-toggle="tab" data-bs-target="#t1-primepanel" type="button" role="tab" aria-controls="t1-primepanel" aria-selected="true">Super1 Permissions</a></li>
                <li><a id="t2-primetab" data-bs-toggle="tab" data-bs-target="#t2-primepanel" type="button" role="tab" aria-controls="t2-primepanel" aria-selected="false" tabindex="-1">Prime Permissions</a></li>
                <li><a id="t3-primetab" data-bs-toggle="tab" data-bs-target="#t3-primepanel" type="button" role="tab" aria-controls="t3-primepanel" aria-selected="false" tabindex="-1">Sub Permissions</a></li>
                <li><a id="t4-primetab" data-bs-toggle="tab" data-bs-target="#t4-primepanel" type="button" role="tab" aria-controls="t4-primepanel" aria-selected="false" tabindex="-1">Tab Permissions</a></li>
                <li><a id="t5-primetab" data-bs-toggle="tab" data-bs-target="#t5-primepanel" type="button" role="tab" aria-controls="t5-primepanel" aria-selected="false" tabindex="-1">Content Permissions</a></li>
                
            </ul>
         </div>
         
          <div class="col-md-2 border-end">
            
      <div class="tab-content border-0 accordion form-outline p-0" id="myTabContent">      
      <div class="tab-pane fade show active p-0" id="t1-primepanel" role="tabpanel" aria-labelledby="t1-primetab" tabindex="0">
           <h5 class="pb-2">Super Permissions</h5>
        <ul class="list-inline" id="myTab" role="tablist">
                <li><a class="active" id="ts1-tab" data-bs-toggle="tab" data-bs-target="#ts1-subpanel" type="button" role="tab" aria-controls="ts1-subpanel" aria-selected="true">Super1 Permissions</a></li>
                <li><a id="ts2-tab" data-bs-toggle="tab" data-bs-target="#ts2-subpanel" type="button" role="tab" aria-controls="ts2-subpanel" aria-selected="false" tabindex="-1">Prim1 Permissions</a></li>
                <li><a id="ts3-tab" data-bs-toggle="tab" data-bs-target="#ts3-subpanel" type="button" role="tab" aria-controls="ts3-subpanel" aria-selected="false" tabindex="-1">Sub1 Permissions</a></li>
                
            </ul>
    </div>
    
     <div class="tab-pane fade p-0" id="t2-primepanel" role="tabpanel" aria-labelledby="t2-primetab" tabindex="0">
          <h5 class="pb-2">Prime Permissions</h5>
     <ul class="list-inline" id="myTab" role="tablist">
                <li><a class="active" id="ta1-tab" data-bs-toggle="tab" data-bs-target="#ta1-subpanel" type="button" role="tab" aria-controls="ta1-subpanel" aria-selected="true">Super2 Permissions</a></li>
                <li><a id="ta2-tab" data-bs-toggle="tab" data-bs-target="#ta2-subpanel" type="button" role="tab" aria-controls="ta2-subpanel" aria-selected="false" tabindex="-1">Prim2 Permissions</a></li>
                <li><a id="ta3-tab" data-bs-toggle="tab" data-bs-target="#ta3-subpanel" type="button" role="tab" aria-controls="ta3-subpanel" aria-selected="false" tabindex="-1">Sub2 Permissions</a></li>
            </ul>
    </div>
    
         <div class="tab-pane fade p-0" id="t3-primepanel" role="tabpanel" aria-labelledby="t3-primetab" tabindex="0">
              <h5 class="pb-2">Sub Permissions</h5>
        <ul class="list-inline" id="myTab" role="tablist">
                <li><a id="tb1-tab" data-bs-toggle="tab" data-bs-target="#tb1-subpanel" type="button" role="tab" aria-controls="tb1-subpanel" aria-selected="true">Super3 Permissions</a></li>
                <li><a id="tb2-tab" data-bs-toggle="tab" data-bs-target="#tb2-subpanel" type="button" role="tab" aria-controls="tb2-subpanel" aria-selected="true">Prim3 Permissions</a></li>
                <li><a id="tb3-tab" data-bs-toggle="tab" data-bs-target="#tb3-subpanel" type="button" role="tab" aria-controls="tb3-subpanel" aria-selected="true">Sub3 Permissions</a></li>
            </ul>
    </div>
    </div>
            
      
         </div>
         <div class="col-md-8">
             
         <div class="tab-content border-0 accordion active form-outline p-0" id="myTabContent"> 
             <!-- Tab 1 Content -->
          <div class="tab-pane fade show active p-0" id="ts1-subpanel" role="tabpanel" aria-labelledby="ts1-tab" tabindex="0">   
             <h5 class="pb-2">Super Advance Permissions</h5>
           <ul class="nav">

 <li class="nav-item d-block w-100 my-2">
 <div class="dropdown float-end">
  <a class="dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Editor </a>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Author</a></li>
    <li><a class="dropdown-item" href="#">Viewer</a></li>
    <li><a class="dropdown-item" href="#">Owner</a></li>
  </ul></div>
 <div class="avatar bg-info rounded-circle text-center float-start avatar-m status-online me-3 pt-1"><h4 class="text-white">M</h4></div>
<h6>My Company Name</h6>myemail@gmail.com
</li>


 <li class="nav-item d-block w-100 my-2">
 <div class="dropdown float-end">
  <a class="dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Editor </a>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Author</a></li>
    <li><a class="dropdown-item" href="#">Viewer</a></li>
    <li><a class="dropdown-item" href="#">Owner</a></li>
  </ul></div>
 <div class="avatar float-start avatar-m status-online me-3"><img class="rounded-circle" src="assets/img/30.webp" alt=""></div>
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
      </div>




 
 </div>
  
  $json_company3 = json_encode([]);
  ?>
  
  <p class="text-end"><a href="#" class="btn btn-outline-success m-2 btnPrevious">« Select Companies</a> <a href="#" class="btn btn-outline-success btnNext m-2">Preview »</a></p>
   </div>
  <!-- Access Tab 3 Ends here --->
  
  
  
  
  <!-- Access Tab 4 Starts here ---->

    <div class="tab-pane border-0  fade accordion-item" id="acess4-panel" role="tabpanel" aria-labelledby="acess4-tab" tabindex="0">
        <h5>Preview</h5>
        
        <!--<div class="mt-1" id="manage_roles_grid"> </div>-->
     
       
       <p class="text-end"><a href="#" class="btn btn-outline-success m-2 btnPrevious">« Set Permissions</a> <a href="#" class="btn btn-outline-success btnNext m-2">Finish »</a></p>
    </div>
    
<!-- Access Tab 4 Ends here --->


<!-- Access Tab 5 Starts here ------>

    <!-- Access 3 Tab -->
  <div class="tab-pane border-0 fade accordion-item" id="acess5-panel" role="tabpanel" aria-labelledby="acess5-tab" tabindex="0">

 <div class="card p-4 mt-2">
     <div class="m-5 p-5 text-center m-auto">
         <h2>SUCCESSFULL</h2>
         <p>&nbsp;<br><br></p>
         <a href="#" class="btn btn-success btn-lg">Go To Dashboard</a>
     </div>
     </div>
     
      <p class="text-end"><a href="#" class="btn btn-outline-success m-2 btnPrevious">« Preview</a></p>
     </div>
<!-- Access Tab 5 Ends here --------->


   
  <!-- Manage Access Modal Ends here --->  

        </div>
      </div>
      <div class="support-chat-container">
        <div class="container-fluid support-chat">
          <div class="card bg-white">
            <div class="card-header d-flex flex-between-center px-4 py-3 border-bottom">
              <h5 class="mb-0 d-flex align-items-center gap-2">Chat widget<span class="fa-solid fa-circle text-success fs--3"></span></h5>
              <div class="btn-reveal-trigger"><button class="btn btn-link p-0 dropdown-toggle dropdown-caret-none transition-none d-flex" type="button" id="support-chat-dropdown" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h text-900"></span></button>
                <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown"><a class="dropdown-item" href="#!">Request a callback</a><a class="dropdown-item" href="#!">Search in chat</a><a class="dropdown-item" href="#!">Show history</a><a class="dropdown-item" href="#!">Report to Admin</a><a class="dropdown-item btn-support-chat" href="#!">Close Support</a></div>
              </div>
            </div>
            <div class="card-body chat p-0">
              <div class="d-flex flex-column-reverse scrollbar h-100 p-3">
                <div class="mt-6">
                    <p class="client">I need help with something</p>
                  
                    <p class="user">I canâ€™t reorder a product I previously ordered</p>
                    <p class="user">How do I place an order?</p>
					<p class="client">My payment method not working</p>
                  </div>
                <div class="text-center mt-auto">
                  <div class="avatar avatar-3xl status-online"><img class="rounded-circle border border-3 border-white" src="assets/img/user.jpg" alt=""></div>
                  <h5 class="mt-2 mb-3">Eric</h5>
                  <p class="text-center text-black mb-0">Ask us anything â€“ weâ€™ll get back to you here or by email within 24 hours.</p>
                </div>
              </div>
            </div>
            <div class="card-footer d-flex align-items-center gap-2 border-top ps-3 pe-4 py-3">
              <div class="d-flex align-items-center flex-1 gap-3 border rounded-pill px-4">
			  <input class="form-control outline-none border-0 flex-1 fs--1 px-0" type="text" placeholder="Write message">
			  <input type="file" accept="image/*" name="image" id="file" onchange="loadFile(event)" style="display: none;">
<label for="file" style="cursor: pointer;"><i id="chatfile" class="material-symbols-outlined">attachment</i></label>
</div><button class="btn p-0 border-0 send-btn"><span class="material-symbols-outlined">send</span></button>
            </div>
          </div>
        </div><button class="btn p-0 border border-200 btn-support-chat"><span class="fs-0 btn-text ">Chat demo</span><span class="fa-solid fa-circle text-success fs--1 ms-2"></span><span class="material-symbols-outlined text-primary fs-1">forum</span></button>
      </div>
    </div></div> 
    
  <?php 
//   $json_company = array();
//   if($company_list){ foreach($company_list as $company_row){
//      $json_company[] = array("id"=>$company_row['company_id'],"company_id"=>$company_row['company_id'],"companyname"=>$company_row['company_name'],"companycode"=>$company_row['company_code'],"finyear"=>$company_row['fy_begndt']);
//     }
//   } 

    $json_company[0] = ['checkbox' => '<input type="checkbox">', 'users' => 'Manpreet Saini', 'settings' => 'AI Group', 'manage' => '<a href="#!"><span class="material-symbols-outlined">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined">lock_person</span></a>'] ;
     
     $json_company[1] = ['checkbox' => '<input type="checkbox">', 'users' => 'Manpreet Saini', 'settings' => 'AI Group', 'manage' => '<a href="#!"><span class="material-symbols-outlined">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined">lock_person</span></a>'] ;
     
     $json_company[2] = ['checkbox' => '<input type="checkbox">', 'users' => 'Manpreet Saini', 'settings' => 'AI Group', 'manage' => '<a href="#!"><span class="material-symbols-outlined">add_task </span></a> 
     <a href="#!"><span class="material-symbols-outlined">manage_history</span></a>
     <a href="#!"><span class="material-symbols-outlined">lock_person</span></a>'] ;
     
      
     
  $json_company = json_encode($json_company);
  ?>
    
   <?php echo view('includes/footer_scripts'); ?>
   
<script>
    
 
        
        var colModel1 = [
           
           { title: '<input type="checkbox">', width: 10, dataIndx: 'checkbox' },
           { title: "COMPANY NAME", width: 100, dataIndx: "company_name" },
            { title: "COMPANY CODE", width: 100, dataIndx: "company_code" },
            { title: "Manage", width: 100, dataIndx: "manage" },
            
	    	];
        var dataModel1 = {"data":<?php echo $json_company1;?>}
        var newObj1 = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel1,
            colModel : colModel1,
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row1"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            }
        };
        
	     
        // $("#companies_grid").pqGrid(newObj1);
        // --------------------------------------------------------------------------------------------------------------------------------
        var colModel2 = [
           
           { title: 'Name', width: 10, dataIndx: 'user_name' },
           { title: "Email", width: 100, dataIndx: "user_email" },
            { title: "Phone", width: 100, dataIndx: "user_phone" },
            { title: "Manage", width: 100, dataIndx: "manage" },
            
	    	];
        var dataModel2 = {"data":<?php echo $json_company2;?>}
        var newObj2 = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel2,
            colModel : colModel2,
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row2"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            }
        };
        
	     
        // $("#users_grid").pqGrid(newObj2);
        
   
           
        // -----------------------------------------------------------------------------------------------------------------------------------------
        
        var colModel = [
           
           { title: '<input type="checkbox">', width: 10, dataIndx: 'checkbox' },
           { title: "Users", width: 100, dataIndx: "users" },
            { title: "Settings", width: 100, dataIndx: "settings" },
            { title: "Manage", width: 100, dataIndx: "manage" },
            
	    	];
        var dataModel = {"data":<?php echo $json_company;?>}
        var newObj = {
            scrollModel: { autoFit: true },
            height: 'flex',
            collapsible: { on: false, collapsed: false, toggle: false, css: { zIndex: 1000 } }, //disable maximize,toggle button.
            selectionModel: { type: 'row',mode:'single' },
            pageModel: { type: 'local' },
            dataModel: dataModel,
            colModel : colModel,
            editable: false,
            showTitle: true,
            create: function (evt, ui) {// make first row auto selected
                  var grid = this,
                    $select_row = $(".select-row"),
                    data = ui.dataModel.data;
                   grid.setSelection({ rowIndx: 0, focus: true });
            }
        };
        
	     $("#companies_grid").pqGrid(newObj1);
	   //  $("#users_grid").pqGrid(newObj2);
	   //  $("#permission_grid").pqGrid(newObj3);
        // $("#manage_roles_grid").pqGrid(newObj);
           
    
    
    $(document).on('click','#acess1-tab', function(){
        $("#companies_grid").pqGrid(newObj1);
    });
    $(document).on('click','#acess2-tab, #select_user', function(){
        $("#users_grid").pqGrid(newObj2);
    });
    $(document).on('click','#acess3-tab', function(){
        //  $("#permission_grid").pqGrid(newObj3);
    });
    $(document).on('click','#acess4-tab', function(){
        // $("#manage_roles_grid").pqGrid(newObj);
    });
    
    // $("#myTab").tabs();
  
    // $("#myTab").on('tabsactivate', function () {
    //     alert("Tabs Activate Event Triggered!");
    // });
    
</script>
	
<script>
    $(document).ready(function(){
  $(".toogletnav").click(function(){
    $(".topnavbar").toggleClass("show-topnav");
  });
});
$(document).ready(function(){
  $(".navbar-vertical-toggle").click(function(){
    $("html").toggleClass("navbar-vertical-collapsed");
  });
});
$(document).ready(function(){
  $(".btn-support-chat").click(function(){
    $(".support-chat").toggleClass("show-chat");
  });
});

$(document).ready(function(){
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
$(document).ready(function(){
  $(".dropmenu").click(function(){
    $("body").toggleClass("sidetoogle");
  });
});

document.onkeydown = function(e) {
  switch (e.keyCode) {
    case 38:
      moveUp();
      break;
    case 39:
      moveRight();
      break;
    case 40:
      moveDown();
      break;
  }
};

function moveUp() {
  //Check if there is another link above, if no, go to bottom
  if ($(".selected").prev("div").length > 0) {
    $(".selected")
      .removeClass("selected")
      .prev("div")
      .addClass("selected")
      .focus();
  } 
  // else {
  //   $(".selected").removeClass("selected");
  //   $(".gridtable.focus div:last-child")
  //     .addClass("selected")
  //     .focus();
  // }
}

function moveDown() {
  //Check if there is another link under, if no, go to top
  if ($(".selected").next("div").length > 0) {
    $(".selected")
      .removeClass("selected")
      .next("div")
      .addClass("selected")
      .focus();
  } 
  // else {
  //   $(".selected").removeClass("selected");
  //   $(".gridtable.focus span")
  //     .next()
  //     .addClass("selected")
  //     .focus();
  // }
}

function moveRight() {
  $(".gridtable div")
    .blur()
    .removeClass("selected");

  if ($(".focus").next(".gridtable").length > 0) {
    $(".focus")
      .removeClass("focus")
      .next(".gridtable")
      .addClass("focus");
    $(".gridtable.focus div")
      .first()
      .addClass("selected")
      .focus();
  } else {
    $(".focus")
      .removeClass("focus");
    $(".gridtable")
      .first()
      .addClass("focus");
    $(".gridtable div")
      .first()
      .addClass("selected")
      .focus();
  }
}

//Remove .selected style on click outside
// $(document).on("blur", ".selected", function() {
//   $(this).removeClass("selected");
// });

//Start to Default Select
if ($(".selected").length === 0) {
  $(".gridtable")
    .first()
    .addClass("focus");
  $(".gridtable div")
    .first()
    .addClass("selected")
    .focus();
}
</script>

