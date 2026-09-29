<!DOCTYPE html>
<html lang="en-US" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title><?php echo $title;?> - <?php echo SITE_NAME;?></title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url();?>/public/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url();?>/public/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url();?>/public/assets/img/favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>/public/assets/img/favicon.ico">


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100" rel="stylesheet">
    <link href="<?php echo base_url();?>/public/assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    
    <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/css/jquery-ui.css" />
    
    <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/grid/pqgrid.dev.css" />    
    <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/grid/pqgrid.ui.dev.css" />

    <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/grid/themes/Office/pqgrid.css" />
    

    <script> var baseurl ='<?php echo base_url();?>'; </script> 
    
    <style>
     .themered .text-success{color:#ff0000!important;}
        .themered .bg-success{background-color:#ff0000!important;}
        .themered .btn-success{background:#ff0000;}
                .themered .btn-success:hover{background:#e60000;}
        .themered .btn-outline-success{border-color:#ff0000;color:#ff0000!important;}
        .themered .btn-outline-success:hover{background:#ff0000;color:#fff!important;}

        .table-sm>:not(caption)>*>* {
            padding: 0.05rem 0.05rem;
        }
         
    </style>
  </head>

  <body>
  <?php
          $local_session      = \Config\Services::session(); 
        
             
        $user_comp_list   =  $local_session->get('suser_comp_list');	
	if($local_session->get('ses_company_id')!=''){
	  $user_id           = $local_session->get('ses_company_id');	
      $ses_company_code  = $local_session->get('ses_compl_company_code');
      $s_name            = ucwords($local_session->get('ses_company_short_name'));   
      $comp_fy_year      = $local_session->get('ses_company_fy_beginning'); 
      
      $ses_company_gstin = $local_session->get('ses_company_gstin'); 
      
      
      
      $folder_path       = getenv('AdminPath');
      $base_url          = base_url().'/'.$folder_path;   
      
      
    
      
      $raw_compid       = obfuscate_link($user_id);
	}
else{
    
   $comp_fy_year='N/A';
   $ses_company_gstin='N/A';
	$raw_compid ='';

}
	
	 $folder_path       = getenv('AdminPath');
      $base_url          = base_url().'/'.$folder_path; 
      
	  $f_name   = $local_session->get('f_name');	
      $m_name   = $local_session->get('m_name');	
	  $l_name   = $local_session->get('l_name');
	  $email_id = $local_session->get('email');	
	  
	  $business_id = (new App\Models\CommonModel)->get_business_id();
	  
	  
?>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container-fluid px-0" data-layout="container">
        <nav class="navbar navbar-vertical navbar-expand-lg">

          <div class="collapse navbar-collapse" id="navVerticalNav" style="background:#003f85;">
            <!-- scrollbar removed-->
            <div class="navbar-vertical-content p-0 erp-banner overflow-hidden text-center">
              
                     <img src="<?php echo base_url();?>/public/assets/img/erp-banner.jpg" alt="aicountly" class="h-100 w-auto" />
            
                    
				<!-- Bookings Menu-->
                
          </div>
          </div>
          <div class="navbar-vertical-footer"><button class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center"><span class="arrow material-symbols-outlined fs-0">arrow_back</span><span class="navbar-vertical-footer-text ms-2">Collapsed View</span></button></div>
        </nav>
        <nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
          <div class="collapse navbar-collapse justify-content-between">
            <div class="navbar-logo">
              <!--<button class="btn navbar-toggler navbar-toggler-humburger-icon m-0 hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navVerticalNav" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>-->
              <span class="toogletnav material-symbols-outlined d-lg-none d-block">apps</span>
             <?php if($local_session->get('ses_company_id')!=''){ ?>
			 <a class="navbar-brand me-1 me-sm-3" href="<?php echo $base_url;?>dashboard"><img src="<?php echo base_url();?>/public/assets/img/logo.png" alt="aicountly" width="130" /></a>
			 <?php } else{ ?>
			  <a class="navbar-brand me-1 me-sm-3" href="<?php echo base_url();?>/home/open_company"><img src="<?php echo base_url();?>/public/assets/img/logo.png" alt="aicountly" width="130" /></a>
			 <?php } ?>
            </div>            
             <div class="topnavbar">
            <ul class=" navbar-nav flex-row"> 
			 <?php if($local_session->get('ses_company_id')!=''){ ?>
			<li><a href="<?php echo $base_url;?>dashboard"><img src="<?php echo base_url();?>/public/assets/img/home.png"></a></li>
			 <?php } else { ?>
			 <li><a href="<?php echo base_url();?>/home/open_company"><img src="<?php echo base_url();?>/public/assets/img/home.png"></a></li>
			<?php } ?>
			<li class="topnav nav-item dropdown">
			<a href="#!"  data-bs-toggle="dropdown" aria-expanded="false">Company</a>
    			<div class="submenu px-2 dropdown-menu">
    			<ul>
        			<li class="disable"><a href="#">Backup</a></li>
                    <li class="disable"><a href="#">Restore</a></li>
                    <li><a href="<?php echo base_url();?>/company_access">Company Access</a></li>
                    <li><a href="<?php echo base_url();?>/home/transfer_ownership">Transfer Ownership</a></li>
                    <li class="disable"><a href="#">Archieve Data</a></li>
                    <li><a href="<?php echo base_url();?>/home/recyclebin">Recycle Bin</a></li>
    			</ul>
    			</div>    
			</li>
			<li class="topnav nav-item dropdown">
    			<a href="#!" data-bs-toggle="dropdown" aria-expanded="false">Users</a>
    			<div class="submenu px-2 dropdown-menu">
        			<ul>
            			<li class="disable"><a href="#">Manage Company Access</a></li>
                        <li class="disable"><a href="#">Create Users</a></li>
                        <li class="disable"><a href="#">Manage Users</a></li>
        			</ul>
    			</div>
			</li>
			<li class="topnav nav-item dropdown">
    			<a href="<?php echo base_url() ?>/home/my_account?page=people_sharing/contacts" target="_blank">Contacts</a>
			</li>
	
	
			</li>
			</ul>
			</div>
			
	          <ul class="navbar-nav rightpanel navbar-nav-icons flex-row">
                
			<li class="nav-item companynav me-md-4">
                <a class="nav-link button" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
                    <span class="usericon material-symbols-outlined d-md-none d-block float-start">account_circle</span><i class="d-md-block d-none float-start">
                       Business ID: <?php echo  ((!empty($business_id)) ? $business_id : 'N.A.') ?></i> <span class="expand material-symbols-outlined d-md-block d-none">expand_more</span> </a>
				 <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navsetting">
                  <div class="card position-relative border-0">
                    <div class="card-body p-3">
                        <h6 class="mt-4 pb-3 text-black">My Business ID: <?php echo  ((!empty($business_id)) ? $business_id : 'N.A.') ?></h6>
                        
                        <a href="<?php echo base_url() ?>/home/my_account?page=business_id" target="_blank" class="btn btn-outline-success w-100">Manage Businss Account</a>
                        
                        <!--<h5 class="text-success  text-bold pt-4 pb-1">Active Licence</h5>
                        <h6>No:  XXX XXX XXXX  <span class="float-end">Expiry 02/2028</span></h6>-->


                        <!--<ul class="list-group my-3">-->
                        <!--  <li class="list-group-item d-flex justify-content-between">Licence No: XXX XXX XXXX    <span class="">10/2020</span> </li>-->
                        <!--  <li class="list-group-item d-flex justify-content-between">  Licence No: XXX XXX XXXX    <span class="">10/2020</span> </li>-->
                        <!--  <li class="list-group-item d-flex justify-content-between"> Licence No: XXX XXX XXXX     <span class="">10/2020</span> </li>-->
                        <!--  <li class="list-group-item d-flex justify-content-between"> Licence No: XXX XXX XXXX     <span class="">10/2020</span> </li>-->
                        <!--  <li class="list-group-item d-flex justify-content-between"> Licence No: XXX XXX XXXX     <span class="">10/2020</span> </li>-->
                        <!--  <li class="list-group-item d-flex justify-content-between"> Licence No: XXX XXX XXXX     <span class="">10/2020</span> </li>  -->
                        <!--</ul>-->
                    <p class="text-center py-2"><a href="#" class="link-primary">View All Licence</a></p>





                    </div>
                    <div class="overflow-auto">
                     
                     

                      
                    </div>
                  </div>
                </div>
              </li>
			
			 <li class="nav-item navactivities">
                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="material-symbols-outlined">device_reset</span></a>
				 <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navsetting">
                  <div class="card position-relative border-0">
                    <div class="card-body p-0">
                        <h6 class="mt-4 px-3 text-black">Recent Activities</h6>
                      <div class="mb-3 mx-3"><input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Search Settings" /></div>
                    </div>
                    <div class="overflow-auto scrollbar" style="height:25rem;">
     <!--                 <ul class="nav d-flex flex-column mb-2 pb-1 myactivitylist">-->
     <!--                 <li class="nav-item"><a class="nav-link px-3" href="#!"> <b><i class="me-2 material-symbols-outlined">done_all</i>New Message from user</b><span>ðŸ“… August 7,2021 / 10:41 AM</span></a></li>-->
					<!-- <li class="nav-item"><a class="nav-link px-3" href="#!"> <b><i class="me-2 material-symbols-outlined">done_all</i>New Message from user</b><span>ðŸ“… August 7,2021 / 10:41 AM</span></a></li>-->
					<!--  <li class="nav-item"><a class="nav-link px-3" href="#!"> <b><i class="me-2 material-symbols-outlined">done_all</i>New Message from user</b><span>ðŸ“… August 7,2021 / 10:41 AM</span></a></li>-->
					<!--  <li class="nav-item"><a class="nav-link px-3" href="#!"> <b><i class="me-2 material-symbols-outlined">done_all</i>New Message from user</b><span>ðŸ“… August 7,2021 / 10:41 AM</span></a></li>-->
					<!--  <li class="nav-item"><a class="nav-link px-3" href="#!"> <b><i class="me-2 material-symbols-outlined">done_all</i>New Message from user</b><span>ðŸ“… August 7,2021 / 10:41 AM</span></a></li>-->
					<!--<li class="nav-item"><a class="nav-link px-3" href="#!"> <b><i class="me-2 material-symbols-outlined">done_all</i>New Message from user</b><span>ðŸ“… August 7,2021 / 10:41 AM</span></a></li>-->
     <!--                 </ul>-->
                    </div>
                  </div>
                </div>
              </li>
			
              <li class="nav-item settingsnav">
                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="material-symbols-outlined">settings_suggest</span></a>
              <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navsetting">
                 <div class="card position-relative border-0">
                    <div class="card-body p-0">
                        <h6 class="my-3 px-3 text-black">Theme Color <a href="#" class="text-muted float-end main_theme">Default</a></h6>
                       <div class="row px-4">
                            <p class="col-3"><a href="#" class="d-block h-100 red_theme">&nbsp;</a></p>
                            <p class="col-3"><a href="#" class="d-block h-100 orng_theme">&nbsp;</a></p>
                            <p class="col-3"><a href="#" class="d-block h-100 sky_theme">&nbsp;</a></p>
                            <p class="col-3"><a href="#" class="d-block h-100 purpl_theme">&nbsp;</a></p>
                            <p class="col-3"><a href="#" class="d-block h-100 yelow_theme">&nbsp;</a></p>
                            <p class="col-3"><a href="#" class="d-block h-100 part_theme">&nbsp;</a></p>
                            <p class="col-3"><a href="#" class="d-block h-100 grn_theme">&nbsp;</a></p>
                            <p class="col-3"><a href="#" class="d-block h-100 blue_theme">&nbsp;</a></p>
                        </div>
                        <hr>
                        <div class="row m-0  px-4">
                           <p class="row p-0"><label class="w-50">Font Family</label><select name="family" class="form-select form-select-sm w-50"><option>Noto Sans</option><option>Montserrat</option><option>Mooli</option><option>Roboto Slab</option></select></p>
                           <p class="row p-0"><label class="w-50">Font Size</label> <span class="w-50 btn-group p-0">
  <a href="#" class="btn btn-success zoomnormal p-2" aria-current="page">A</a><a href="#" class="btn btn-outline-success p-2 zoomin">A+</a><a href="#" class="btn btn-outline-success p-2 zoommore">A+</a> </span></p>
                        </div>
                        <hr>
                         <h6 class="my-3 px-3 text-black">Notification</h6>
                         <div class="row m-0 px-4 position-relative">
                             <div class="position-absolute left-0 top-0 right-0 bottom-0" style="background:rgba(255,255,255,0.4)"></div>
                         <p class="form-check form-switch"><label>Missed Activity Email</label><input class="form-check-input" type="checkbox" role="switch" name="missed_activity"></p>
                         <p class="form-check form-switch"><label>Show Preview Message</label><input class="form-check-input" type="checkbox" role="switch" name="preview_msg"></p>
                        <p class="form-check form-switch"><label>Desktop Notifiation</label><input class="form-check-input" type="checkbox" role="switch" name="desk_notice"></p>
                        <p class="form-check form-switch"><label>Sound Notifiation</label><input class="form-check-input" type="checkbox" role="switch" name="sound_notice"></p>
                         </div>
                          <hr>
                       <h6 class="my-3 px-3 text-black">Display & Sound</h6>
                       <div class="row m-0 px-4 position-relative">
                           <div class="position-absolute left-0 top-0 right-0 bottom-0" style="background:rgba(255,255,255,0.4)"></div>
                           <p class="row p-0"><label class="w-50">Notification Sound</label><select name="notify_sound" class="form-select form-select-sm w-50"><option>Option 1</option><option>Option 2</option></select></p>
                           <p class="row p-0"><label class="w-50">Audio Device</label><select name="device" class="form-select form-select-sm w-50"><option>Size 25</option><option>Size 50</option></select></p>
                           <p class="row p-0"><label class="w-50">Speaker</label><select name="speaker" class="form-select form-select-sm w-50"><option>Size 25</option><option>Size 50</option></select></p>
                        </div>
                        
                        <div class="d-grid py-2 px-3">
<a href="#" class="btn btn-outline-success btn-block disabled">View All</a><p class="my-3">&nbsp;</p></div>
                      
                    </div></div>
                </div>
			  
			  </li>
			  	
              <li class="nav-item dropdown notifynav">
                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="material-symbols-outlined">notifications</span></a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border border-300 navbar-dropdown-caret" id="navNotfication" aria-labelledby="navNotfication">
                  <div class="card position-relative border-0">
                    <div class="card-header p-2">
                      <div class="d-flex justify-content-between">
                        <h5 class="text-black mb-0">Notificatons</h5><button class="btn btn-link p-0 fs--1 fw-normal" type="button">Mark all as read</button>
                      </div>
                    </div>
                    <div class="card-body p-0">
                      <div class="scrollbar-overlay" style="height: 27rem;">
                        <div class="border-300">
                          
						  <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative read border-bottom">
                            <div class="d-flex align-items-center justify-content-between position-relative">
                              <div class="d-flex">
                                <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="<?php echo base_url();?>/public/assets/img/30.webp" alt="" /></div>
                                <div class="flex-1 me-sm-3">
                                  <h4 class="fs--1 text-black">Jessie Samson</h4>
                                  <p class="mb-0">ðŸ’¬ Mentioned you in a comment.<span class="ms-2 text-400 fw-bold fs--2">10m</span></p>
                                  <p class="text-800 fs--2 mb-0">ðŸ“… August 7,2021 / 10:41 AM</p>
                                </div>
                              </div>
                              <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm" type="button"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                
                              </div>
                            </div>
                          </div>
						  
                          <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative unread border-bottom">
                            <div class="d-flex align-items-center justify-content-between position-relative">
                              <div class="d-flex">
                                <div class="avatar avatar-m status-online me-3">
                                  <div class="avatar-name rounded-circle"><span>J</span></div>
                                </div>
                                 <div class="flex-1 me-sm-3">
                                  <h4 class="fs--1 text-black">Jessie Samson</h4>
                                  <p class="mb-0">ðŸ’¬ Mentioned you in a comment.<span class="ms-2 text-400 fw-bold fs--2">10m</span></p>
                                  <p class="text-800 fs--2 mb-0">ðŸ“… August 7,2021 / 10:41 AM</p>
                                </div>
                              </div>
                               <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm" type="button"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button></div>
                            </div>
                          </div>
                          
						  <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative unread border-bottom">
                            <div class="d-flex align-items-center justify-content-between position-relative">
                              <div class="d-flex">
                                <div class="avatar avatar-m status-offline me-3">
                                  <div class="avatar-name rounded-circle"><span>L</span></div>
                                </div>
                                 <div class="flex-1 me-sm-3">
                                  <h4 class="fs--1 text-black">Jessie Samson</h4>
                                  <p class="mb-0">ðŸ’¬ Mentioned you in a comment.<span class="ms-2 text-400 fw-bold fs--2">10m</span></p>
                                  <p class="text-800 fs--2 mb-0">ðŸ“… August 7,2021 / 10:41 AM</p>
                                </div>
                              </div>
                               <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm" type="button"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button></div>
                            </div>
                          </div>
                        </div>
                       	
                      </div>
                    </div>
					
                    <div class="card-footer p-0 border-top border-0">
                      <div class="my-2 text-center fw-bold fs--2 text-600"><a class="fw-bolder text-dark" href="pages/notifications.html">Notification history</a></div>
                    </div>
                  </div>
                </div>
              </li>
              <li class="nav-item dropdown helpnav">
                <a class="nav-link" id="navhelpdesk" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false"><span class="material-symbols-outlined">question_exchange</span></a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-helpdesk shadow border border-300" aria-labelledby="navhelpdesk">
                  <div class="card bg-white position-relative border-0">
                    <div class="card-body pt-3 px-3 pb-0 overflow-auto scrollbar" style="height:25rem;">
                       <div class="row text-center align-items-top gx-0 gy-0">
                  <div class="col-4"><a href="https://aicountly.com/help/learning/" target="_blank"><img src="<?php echo base_url();?>/public/assets/img/icon2.png" class="w-75" alt=""/><p>Countly Learning</p></a></div>           
                 <div class="col-4"><a href="#!" data-bs-toggle="modal" data-bs-target="#FaqModal"><img src="<?php echo base_url();?>/public/assets/img/e-sahayak-slogo.png" class="w-75" alt=""/><p>E-sahayak </p></a></div>
				<div class="col-4"><a href="https://aicountly.com/support/" target="_blank"><img src="<?php echo base_url();?>/public/assets/img/icon1.png" class="w-75" alt=""/><p>Help Desk</p></a></div>
				 <div class="col-4"><a href="https://aicountly.com/support/" target="_blank"><img src="<?php echo base_url();?>/public/assets/img/icon3.png" class="w-75" alt=""/><p>Support</p></a></div>
				 <div class="col-4"><a href="#!" onClick="showtour();"><img src="<?php echo base_url();?>/public/assets/img/icon4.png" class="w-75" alt=""/><p>Aicountly Tour</p></a></div>
				 <div class="col-4"><a href="#!" data-bs-toggle="modal" data-bs-target="#ShortcutModal"><img src="<?php echo base_url();?>/public/assets/img/icon5.png" class="w-75" alt=""/><p>Keybord Shortcuts</p></a></div>
				 <div class="col-4 offset-4"><a href="https://aicountly.com/help/community/" target="_blank"><img src="<?php echo base_url();?>/public/assets/img/icon6.png" class="w-75" alt=""/><p>Forums</p></a></div>
                    </div>
                    </div>
                  </div>
                </div>
              </li>
              
              <li class="nav-item dropdown usernav">
                <a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                  <div class="avatar avatar-l ">
                    <img class="rounded-circle " src="<?php echo base_url();?>/public/assets/img/user.jpg" alt="" />
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navbarDropdownUser">
                  <div class="card position-relative border-0">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-4 text-center pt-4 ps-5">
                                <div class="avatar-xl">
                                  <img class="rounded-circle" src="<?php echo base_url();?>/public/assets/img/user.jpg" alt="" />
                                </div>
                            </div>
                            <div class="col-8 pt-1">
                                <table id="dp_table" class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <td>User ID: </td>
                                            <td><?= auth()->id ?></td>
                                        </tr>
                                        <tr>
                                            <td>Org. ID: </td>
                                            <td>60020948365</td>
                                        </tr>
                                        <tr>
                                            <td>Email: </td>
                                            <td><?= auth()->email ?></td>
                                        </tr>
                                        <tr>
                                            <td>Phone: </td>
                                            <td>
                                                <?php if(auth()->phone){ ?>
                                                    <?= auth()->phone ?>
                                                <?php } else { ?>
                                                    <a href="<?php echo base_url() ?>/home/my_account?page=personal_info" target="_blank" class="badge bg-primary">Update</a>
                                                <?php } ?>

                                                </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>

                      <div class="mx-3 mt-1">
                        <a href="<?php echo base_url() ?>/home/my_account" target="_blank" class="btn btn-outline-success w-100">Manage My Aicountly Account</a>
                      </div>
                      <div class="mx-3 mt-1">
                        <input class="form-control form-control-sm" id="searchCompaniesFromHeader" type="search" placeholder="Search" />
                      </div>
                    
  <div class="overflow-auto scrollbar" style="height: 15rem;overflow: auto">
    <ul class="nav mb-2 pb-1" id="searchCompaniesFromHeader_ul">
    <?php if(user_company_list()){ ?>
    <?php foreach(user_company_list() as $value){ ?>
      <li class="nav-item">
        <a class="nav-link px-3 selcompany_link" data-comp_id="<?php echo $value['comp_id'];?>" data-comp_type="<?php echo $value['comp_type'];?>" href="javascript:void(0);">
        <h5><?php echo $value['comp_name'];?></h5>
          Organization: <?php echo $value['comp_code'];?>
        </a>
      </li>
    <?php } ?>
    <?php } ?>
    </ul>
  </div>
                    </div>
                    <div class="card-footer p-0 border-top">
                      <ul class="nav d-flex flex-column my-3">
                        <li><a class="nav-link px-3 text-dark" href="<?php echo base_url();?>/home/add_company"> + Add another company</a></li>
                      </ul>
                     
                      <div class="px-3"> 
					  <a class="btn d-flex flex-center w-100" href="<?php echo $base_url;?>signout">Sign out</a>
					  </div>
					   <div class="my-2 text-center">
                            <a class="text-dark mx-1" target="_blank" href="https://www.aicountly.com/security_policy.php">Privacy policy</a>&bull;
                            <a class="text-dark mx-1" target="_blank" href="https://www.aicountly.com/terms_of_use.php">Terms</a>&bull;
                            <a class="text-dark mx-1" target="_blank" href="https://www.aicountly.com/cookies_policy.php">Cookies</a>
                        </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
		  </div>
        </nav>
      