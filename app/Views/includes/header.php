<?php
$local_session      = \Config\Services::session();
$common_model       = new \App\Models\CommonModel;
$uri                = service('uri');
if(trim($uri->getSegment(1))=="admin" && trim($uri->getSegment(2))=="dashboard" ){
 $hide_left_sidebar    = '';
}
elseif(trim($uri->getSegment(1))=="admin" && trim($uri->getSegment(2))==""){
 $hide_left_sidebar    = '';
}
else{
 $hide_left_sidebar    = 'class="navbar-vertical-collapsed"';
}

if($local_session->get('ses_company_id')!='')
 $is_disabled_link ='';
else
 $is_disabled_link ='class="disable"';

$s_ctab  = $local_session->get('s_ctab');

$bo_id   = $local_session->get('ses_boid');
$bo_name = $local_session->get('ses_boname');

$menuid = time();

  $cache = \Config\Services::cache();
  $cacheKey = 'company_list_for_user_' . $local_session->get('uuid');
  $AllCompanyList = $cache->get($cacheKey);
	
  $all_bo_lists   =  $common_model->all_bo_lists();
  $user_preferences_list = $common_model->user_preferences_list();
  $sel_branch     =  $user_preferences_list['usr_Pref_Branch'];
  $sel_fy         =  $user_preferences_list['usr_Pref_Fy'];
  $get_theme_pref =  $user_preferences_list['usr_theme_pref'];
  $configMap      = array_column($get_theme_pref, 'erp_config_value', 'erp_config_id');
  
  $default_theme_data   = 'default';
  if(!$configMap)
	  $themename='default';
  else{
  if($configMap[16]!='' && $configMap[16]=='orange'){
	  $themename = 'orng';
  }else{
	  $themename = $configMap[16];
  }
  
  }
  
  if(!isset($configMap[17]))
	   $default_font = 'normal';
    else
	$default_font  =$configMap[17];

if(!isset($configMap[18]))
	   $default_fontsize = 'zoomnormal';
    else
	$default_fontsize  =$configMap[18];


  $theme_value          = $configMap[16] ?? $default_theme_data;
  $theme_class          = 'theme_'.$themename; 
  
  $theme_fontfamily     = "font_".strtolower($default_font) ?? "";
  
   $theme_fontsize      = 'font-'.$default_fontsize ?? "zoomnormal";
  
   
 
  $ses_comp_fy_id =  $local_session->get('ses_comp_fy_id');  
  $all_fy_list    =  $common_model->all_fy_list();
  
  /************ User can add new fy if current fy end in 90 days *****/

  $show_new_fybutton = '';
  if($local_session->get('ses_company_fy_end')){
    $fyend           = strtotime($local_session->get('ses_company_fy_end'));
    $dayslastfyalert = strtotime(date('Y-m-d', strtotime('-90 day', $fyend)));
    
    if(strtotime(date('Y-m-d')) >=$dayslastfyalert)
      $show_new_fybutton='<li class="nav-item">
                    <a data-id="0" class="nav-link px-3 addnewfy" href="javascript:void(0);">
                    Add New FY</a>
                   </li>';
    } 
   $log_model       = new \App\Models\Admin\ERPLogModel;
   $loglist         = $log_model->get_logs();
   
   $notifiationlist         = $log_model->get_notifications();
   
        ?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr" <?php echo $hide_left_sidebar;?>>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>
      <?php echo $title;?> - <?php echo SITE_NAME;?>
    </title>
    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url();?>public/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url();?>public/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url();?>public/assets/img/favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>public/assets/img/favicon.ico">
    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@200" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/css/theme.min.css" type="text/css" id="style-default">
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/css/jquery-ui.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/grid/pqgrid.dev.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/grid/pqgrid.ui.dev.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/grid/themes/Office/pqgrid.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>public/responsive.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link rel="stylesheet" href="<?php echo base_url();?>public/assets/css/bootstrap-icons.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script> var baseurl ='<?php echo base_url();?>'; </script>
	<style>
	/* Modal header: lighter green */
	.datepicker-wrapper{ width:100%;}
	.rihgtsticky {
		background: #fff;
		border: 1.5px solid #25b003;
   }
   .rihgtsticky img{
		width: 24px;
		height: 24px;
   }
  .modal-heading {

    background-color: #fff!important;

    color: #fff;

    border-bottom: 1px solid rgba(0, 0, 0, 0.1);

  }
  
  #igstlabel,#cgstlabel,#sgstlabel,#cesslabel{display:none;}

  .modal-heading r .btn {

    color: #fff;

  }

  .modal-heading  .btn:hover {

    background-color: rgba(255, 255, 255, 0.1);

  }
 
  /* Form Labels and Links */

  .modal-body .form-label {

    color: #333;

    font-weight: 600;

  }

  .btn-link {

    color: rgb(37, 176, 3);

    font-weight: 500;

  }

  .btn-link:hover {

    color: #239f03;

    text-decoration: underline;

  }
 
  /* Form Input Focus */

  .form-focus:focus {

    border-color: rgb(37, 176, 3);

    box-shadow: 0 0 0 0.15rem rgba(37, 176, 3, 0.25);

  }
 
  /* Checkbox highlight */

  .select-check:checked {

    background-color: rgb(37, 176, 3);

    border-color: rgb(37, 176, 3);

  }
 
  /* Send Button */

  .btn-primary {

    background-color: rgb(37, 176, 3);

    border-color: rgb(37, 176, 3);

  }

  .btn-primary:hover {

    background-color: #28a003;

    border-color: #28a003;

  }
 
  /* Export label */

  .modal-body label[for^="export"] {

    color: #111;

    font-weight: 500;

  }
 
  .export-toggle .btn-check:checked + .btn {

    background-color: #25b003 !important;

    color: white !important;

    border-color: #25b003 !important;

  }
 
  .export-toggle .btn {

    background-color: white;

    color: #25b003;

    border: 2px solid #25b003;

    border-radius: 6px;

    font-weight: 600;

    min-width: 70px;

  }
 
  .form-focus:focus {

    border-color: black !important;

    box-shadow: none !important;

  }  

    .form-label {

    font-size: 12px !important;

    font-weight: 600;

  }
	</style>
<script>
/* (function () {
  // === Theme setup ===
  const themeCookieName = "selected_theme";
  const themeMap = {
    'red_theme': 'red',
    'orng_theme': 'orange',
    'sky_theme': 'sky',
    'purpl_theme': 'purpl',
    'yelow_theme': 'yelow',
    'part_theme': 'part',
    'grn_theme': 'grn',
    'blue_theme': 'blue',
    'main_theme': 'default'
  };

  // === Zoom setup ===
  const zoomCookieName = "zoom_font";
  const zoomClasses = ["zoomnormal_font", "zoomin_font", "zoommore_font"];

  // Cookie read helper
  function getCookie(name) {
    return document.cookie.split("; ").reduce((r, v) => {
      const parts = v.split("=");
      return parts[0] === name ? decodeURIComponent(parts[1]) : r;
    }, "");
  }

  // ✅ Apply saved theme
  const savedThemeClass = getCookie(themeCookieName);
  if (themeMap.hasOwnProperty(savedThemeClass)) {
    const themeName = themeMap[savedThemeClass];
    const reversedClass = "theme_" + savedThemeClass.replace("_theme", "");
    document.documentElement.classList.add(reversedClass);
    document.documentElement.setAttribute("data-theme", themeName);
  }

  // ✅ Apply saved zoom
  const savedZoomClass = getCookie(zoomCookieName);
  if (zoomClasses.includes(savedZoomClass)) {
    document.body.classList.add(savedZoomClass);
  }
})(); */


</script>
    <style> 
	.datepicker {
    border-radius: 0;
        padding: .8rem 1rem;
}
 .pq-grid-title-row,.pq-grid-header-search-row {
    background: #fff;
}
.input-group {
    flex-wrap: inherit;
}
	.alert-success-custom {
      position: relative;
      padding: 1.25rem 1.5rem;
      border: 1px solid #25b003;
      background-color: #e9fce9;
      color: #1e6c00;
      border-left: 6px solid #25b003;
      border-radius: 0.6rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      max-width: 600px;
      margin: auto;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .alert-success-custom i {
      font-size: 1.8rem;
      color: #25b003;
    }

    .alert-success-custom .btn-close {
      position: absolute;
      right: 1rem;
      top: 1rem;
    }
	.alert-error-custom {
      position: relative;
      padding: 1.25rem 1.5rem;
      border: 1px solid #dc3545;
      background-color: #fce9eb;
      color: #842029;
      border-left: 6px solid #dc3545;
      border-radius: 0.6rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      max-width: 600px;
      margin: auto;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .alert-error-custom i {
      font-size: 1.8rem;
      color: #dc3545;
    }

    .alert-error-custom .btn-close {
      position: absolute;
      right: 1rem;
      top: 1rem;
    }
    .table-sm>:not(caption)>*>* {
    padding: 0.05rem 0.05rem;
    }
	div.pq-grid * {
    font-size: 13px!important;
}


.hover-img {
  opacity: 0;
  transition: opacity 0.2s ease;
}

.hover-show-img:hover .hover-img,
.hover-img.active-star {
  opacity: 1; /* Always visible if selected or hovered */
}
#toolbarContainer{display:none!important;}

.note-heading {
  font-size: 1.5rem; /* Similar to h3 */
  font-weight: bold;
}

.star-icon {
  font-size: 14px;
  cursor: pointer;
  margin: 0 5px;
  /*-webkit-text-stroke: 1px black;*/
}

/* Theme-based star colors */
[data-theme="red"] .star-icon { color: #dd0026 !important; }
[data-theme="orange"] .star-icon { color: #ec7b2d !important; }
[data-theme="sky"] .star-icon { color: #2fa1da !important; }
[data-theme="purpl"] .star-icon { color: #d97cf8 !important; }
[data-theme="yelow"] .star-icon { color: #d3b000 !important; }
[data-theme="part"] .star-icon { color: #a2c42e !important; }
[data-theme="grn"] .star-icon { color: #5bbbb1 !important; }
[data-theme="blue"] .star-icon { color: #3874ff !important; }

/* Default fallback */
body:not([data-theme]) .star-icon,
[data-theme="default"] .star-icon {
  color: rgb(37 176 3) !important;
}




  /* === Modal Container Rounded === */
  .modal-content {
    border-radius: 14px;
    border: 2px solid rgb(37, 176, 3);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  /* === Title with Green Icon === */
  #modalTitle {
    font-size: 1.3rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  #modalTitle::before {
    content: "";
    display: inline-block;
    width: 22px;
    height: 22px;
   
  }

  /* === Input Field with Search Icon === */
  #crsInput {
    padding-left: 40px;
    background-image: url("https://cdn.jsdelivr.net/npm/bootstrap-icons/icons/search.svg");
    background-repeat: no-repeat;
    background-position: 12px center;
    background-size: 18px;
    border: 2px solid rgb(37, 176, 3);
    border-radius: 8px;
    height: 44px;
    font-size: 1.05rem;
    background-color: #f7fff7;
    transition: 0.3s border;
  }

  #crsInput:focus {
    outline: none;
    border-color: rgb(30, 146, 2);
    box-shadow: 0 0 0 2px rgba(37, 176, 3, 0.25);
  }

  /* === Label === */
  label[for="crsInput"] {
    font-weight: 600;
    margin-bottom: 6px;
    color: #333;
    display: block;
    font-size: 0.95rem;
  }

  /* === Dropdown List Items === */
  #crsDropdown .list-group-item {
    font-size: 0.96rem;
    padding: 10px 16px;
    font-weight: 500;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s ease;
    margin: 5px 0;
    border-top-width: 1px!important;
  }

  #crsDropdown .list-group-item:hover {
    background-color: rgba(37, 176, 3, 0.08);
    color: rgb(37, 176, 3);
  }

  #crsDropdown .text-muted {
    font-style: italic;
    font-size: 0.9rem;
    text-align: center;
    padding: 8px 0;
    color: #888;
  }
  /* ✅ Target your actual button structure inside .modal-content */
  #searchModal .modal-content .btn.btn-primary {
    background-color: rgb(37, 176, 3) !important;
    border-color: rgb(37, 176, 3) !important;
    color: #fff !important;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: none;
    transition: background-color 0.3s ease;
  }

  #searchModal .modal-content .btn.btn-primary:hover,
  #searchModal .modal-content .btn.btn-primary:focus {
    background-color: #2a9f05 !important;
    border-color: #2a9f05 !important;
    color: #fff !important;
  }

  #searchModal .modal-content .btn.btn-primary:active {
    background-color: #1e6c02 !important;
    border-color: #1e6c02 !important;
    color: #fff !important;
  }
  span.red{color: #ed2000;font-weight: 800;font-size: 13px}
    </style>
 
<link href="<?php echo base_url();?>public/assets/css/tour.css" rel="stylesheet">

  </head>
  <body class="<?php echo $theme_fontfamily;?> <?php echo $theme_fontsize;?> <?php echo $theme_class;?>" data-theme="<?php echo $theme_value;?>">
  
    <script>
    var SYSTEM_CURRENCY = '<?= settings()->currency ?>';
	
    </script>
<?php        
 if($local_session->get('ses_company_id')!=''){
  $user_id           = $local_session->get('ses_company_id');
  $ses_company_code  = $local_session->get('ses_compl_company_code');
  $s_name            = ucwords($local_session->get('ses_company_short_name'));
  $company_name      = ucwords($local_session->get('ses_company_name'));
  $comp_fy_year      = $local_session->get('ses_company_fy_beginning');
  $ses_company_gstin = $local_session->get('ses_company_gstin');
  $folder_path       = getenv('AdminPath');
  $base_url          = base_url().'/'.$folder_path;
  $raw_compid        = obfuscate_link($user_id);
 }
 else{
  $comp_fy_year='N/A';
  $ses_company_gstin='N/A';
  $raw_compid ='';
  $s_name            = "N/A";
  $company_name      ="N/A";
  $ses_company_code  ="N/A";
 }
      
 $folder_path       = getenv('AdminPath');
 $base_url          = base_url().$folder_path;
 $f_name            = $local_session->get('f_name');
 $m_name            = $local_session->get('m_name');
 $l_name            = $local_session->get('l_name');
 $email_id          = $local_session->get('email');
?>
<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
 <div class="container-fluid px-0" data-layout="container">


<nav class="navbar navbar-vertical navbar-expand-lg">
 <div class="collapse navbar-collapse" id="navVerticalNav">
  <!-- scrollbar removed-->
  <div class="navbar-vertical-content">
   <ul class="navbar-nav flex-column" id="navVerticalNav">
    <li class="nav-item">
     <!-- Bookings Menu-->
     <!-- <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navshortcut" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navshortcut">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">open_in_new</span>
        <span class="nav-link-text">Shortcut</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navshortcut">
        <li class="collapsed-nav-item-title d-none">Shortcuts</li>
        <li class="nav-item">
         <a href="#">Task Shortcut</a>
        </li>
        <li class="nav-item">
         <a href="#">Task 2</a>
        </li>
        <li class="nav-item">
         <a href="#">3rd Task Shortcut</a>
        </li>
        <li class="nav-item">
         <a href="#">Task Shortcut</a>
        </li>
       </ul>
      </div>
     </div> -->

     <!-- Bookings Menu-->
     <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navbooking" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navbooking">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">devices_other</span>
        <span class="nav-link-text">My Office Tool</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navbooking">
        <li class="collapsed-nav-item-title d-none">My Office Tool</li>
        <li class="nav-item">
         <a href="<?php echo base_url(); ?>office_tools">Calender</a>
        </li>
        <li class="nav-item">
         <a href="#" data-bs-toggle="modal" data-bs-target="#notesModal">Sticky Notes</a>
        </li>
        <li class="nav-item">
         <a href="#" data-bs-toggle="modal" data-bs-target="#calcModal">Calculator</a>
        </li>
       </ul>
      </div>
     </div>
     <!-- Sales Menu-->
     <!-- <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navsales" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navsales">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">shopping_cart_checkout</span>
        <span class="nav-link-text">Sales</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navsales">
        <li class="collapsed-nav-item-title d-none">Sales</li>
        <li class="nav-item">
         <a href="#">Sales Dashboard</a>
        </li>
        <li class="nav-item">
         <a href="#">Sales  Invoice </a>
        </li>
        <li class="nav-item">
         <a href="#">Delivery Challan </a>
        </li>
        <li class="nav-item disable">
         <a href="#">Sales Order</a>
        </li>
        <li class="nav-item disable">
         <a href="#">Customer Estimate</a>
        </li>
        <li class="nav-item">
         <a href="#">Sales Returns</a>
        </li>
       </ul>
      </div>
     </div> -->
     <!-- Sales Menu-->
     <!-- <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navpurchase" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navpurchase">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">local_mall</span>
        <span class="nav-link-text">Purchase</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navpurchase">
        <li class="collapsed-nav-item-title d-none">Purchase</li>
        <li class="nav-item">
         <a href="#">Purchase Dashboard</a>
        </li>
        <li class="nav-item">
         <a href="#">Inward Challan</a>
        </li>
        <li class="nav-item disable">
         <a href="#">Purchase Order</a>
        </li>
        <li class="nav-item disable">
         <a href="#">Purchase Estimate</a>
        </li>
        <li class="nav-item">
         <a href="#">Purchase Returns</a>
        </li>
       </ul>
      </div>
     </div> -->
     <!-- Payment Menu-->
     <!-- <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navpay" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navpay">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">redeem</span>
        <span class="nav-link-text">Payments</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navpay">
        <li class="collapsed-nav-item-title d-none">Payments</li>
        <li class="nav-item">
         <a href="#">Cash Payments</a>
        </li>
        <li class="nav-item">
         <a href="#">Cash Deposit</a>
        </li>
        <li class="nav-item">
         <a href="#">Contra</a>
        </li>
        <li class="nav-item">
         <a href="#">Pending Payment</a>
        </li>
        <li class="nav-item">
         <a href="#">All Payment</a>
        </li>
        <li class="nav-item">
         <a href="#">Clear Payment</a>
        </li>
        <li class="nav-item">
         <a href="#">Bank Transfer</a>
        </li>
       </ul>
      </div>
     </div> -->
     <!-- Report Menu-->
     <!-- <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navreport" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navreport">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">post_add</span>
        <span class="nav-link-text">Receipts</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navreport">
        <li class="collapsed-nav-item-title d-none">Receipts</li>
        <li class="nav-item">
         <a href="#">All Receipt</a>
        </li>
        <li class="nav-item">
         <a href="#">Cash Receipt</a>
        </li>
        <li class="nav-item">
         <a href="#">Bank Receipt</a>
        </li>
        <li class="nav-item">
         <a href="#">Pending Receipt</a>
        </li>
        <li class="nav-item">
         <a href="#">Settle Receipt</a>
        </li>
       </ul>
      </div>
     </div> -->
     <!-- My Reports -->
     <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navmyreport" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navmyreport">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">stack_star</span>
        <span class="nav-link-text">My Credentials</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navmyreport">
        <li class="collapsed-nav-item-title d-none">My Credentials</li>
        <li class="nav-item">
         <a href="<?php base_url() ?>/admin/my_credentials">All Credentials</a>
        </li>
        <li class="nav-item">
         <a href="<?php base_url() ?>/admin/my_credentials/gst">GST Cred.</a>
        </li>
        <li class="nav-item">
         <a href="#">TRACES Cred.</a>
        </li>
        <li class="nav-item">
         <a href="#">Income Tax Cred.</a>
        </li>
       </ul>
      </div>
     </div>
     <!-- E-Filing -->
     <!-- <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator " href="#navefiling" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navefiling">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">inventory</span>
        <span class="nav-link-text">E-Filing</span>
       </div>
      </a>
      <div class="parent-wrapper label-1">
       <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navefiling">
        <li class="collapsed-nav-item-title d-none">E-Filing</li>
        <li class="nav-item disable">
         <a href="#">GST Outward Supplies </a>
        </li>
        <li class="nav-item disable">
         <a href="#">GST Inward Supplies </a>
        </li>
        <li class="nav-item disable">
         <a href="#">GST Computation</a>
        </li>
        <li class="nav-item disable">
         <a href="#">TDS E-Filings</a>
        </li>
        <li class="nav-item disable">
         <a href="#">TCS E-Filings</a>
        </li>
        <li class="nav-item disable">
         <a href="#">Other E- Filing</a>
        </li>
       </ul>
      </div>
     </div> -->
     <!-- E-Filing -->
     <div class="nav-item-wrapper">
      <a class="nav-link dropdown-indicator" href="#navedocs" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navedocs">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">folder_copy</span>
        <span class="nav-link-text">My Documents</span>
       </div>
      </a>
     
     </div>
     <div class="nav-item-wrapper">
      <a class="nav-link" target="_blank" href="<?php base_url() ?>/home/contacts" role="button">
       <div class="d-flex align-items-center">
        <span class="material-symbols-outlined">perm_phone_msg</span>
        <span class="nav-link-text">Contacts</span>
       </div>
      </a>
     </div>
    </li>
   </ul>

   <div class="company-logo text-center">
    <a href="javascript:void(0)" id="change_logo" class="text-dark" style="float: right;" data-bs-toggle="modal" data-bs-target="#logoModal2">
     <span class="material-symbols-outlined" >
      edit
     </span>
    </a>
	<img id="comp_logo_sidebar" 
     data-src="<?= base_url() ?>admin/company/logo" 
     alt="Company Logo" onerror="this.onerror=null;this.src='<?= base_url() ?>public/assets/img/default_logo.png';">	
   <!-- <img id="comp_logo_sidebar" class="" loading="lazy" src="<?= base_url() ?>admin/company/logo" alt="" data-bs-toggle="modal" style="cursor:pointer;" data-bs-target="#logoModal2" onerror="this.onerror=null;this.src='<?= base_url() ?>public/assets/img/default_logo.png';">-->
   </div>
  </div>
 </div>
 <div class="navbar-vertical-footer">
  <button class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center">
   <span class="arrow material-symbols-outlined fs-0">arrow_back</span>
   <span class="navbar-vertical-footer-text ms-2">Collapsed View</span>
  </button>
 </div>
</nav>


<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
 <div class="collapse navbar-collapse justify-content-between">
  <div class="navbar-logo">
   <button class="btn navbar-toggler navbar-toggler-humburger-icon m-0 hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navVerticalNav" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
    <span class="navbar-toggle-icon">
     <span class="toggle-line">
     </span>
    </span>
   </button>
   <span class="toogletnav material-symbols-outlined d-lg-none d-block">apps</span>
   <a class="btn btn-white" data-bs-toggle="offcanvas" href="#sidenav" role="button" aria-controls="sidenav" style="padding:0px 8px">
    <span class="material-symbols-outlined dropmenu" style="line-height:42px;">lists</span>
   </a>
   <?php if($local_session->get('ses_company_id')!=''){ ?>
    <a class="navbar-brand me-1 me-sm-3" href="<?php echo $base_url;?>dashboard">
     <img src="<?php echo base_url();?>public/assets/img/logo.png" alt="aicountly" width="130" />
    </a>
   <?php } else{ ?>
    <a class="navbar-brand me-1 me-sm-3" href="<?php echo base_url();?>home/open_company">
     <img src="<?php echo base_url();?>public/assets/img/logo.png" alt="aicountly" width="130" />
    </a>
   <?php } ?>
  </div>

  <?php if(!grp_comp(false)->id){ ?>
  <div class="topnavbar" style="overflow:inherit;">
   <ul class=" navbar-nav flex-row">

    <?php if($local_session->get('ses_company_id')!=''){ ?>
    <li>
     <a href="<?php echo $base_url;?>dashboard">
     <img src="<?php echo base_url();?>public/assets/img/home.png">
     </a>
    </li>
    <?php } else { ?>
    <li>
     <a href="<?php echo base_url();?>home/open_company">
     <img src="<?php echo base_url();?>public/assets/img/home.png">
     </a>
    </li>
    <?php } ?>

    <li class="topnav topdropnav nav-item dropdown">
     <a href="#!"  data-bs-toggle="dropdown" aria-expanded="false" id="mastertab">
      <i>M</i>asters</a>
      <div class="submenu dropdown-menu" id="masterdiv_menu">
       <div class="row m-0">
        <div class="col-md-3 col-6">
         <ul>
          <li>
           <b>
            <span class="material-symbols-outlined">home_work</span> Company</b>
           </li>
           <?php if($local_session->get('ses_company_id')!=''){ ?>
            <li>
             <a href="<?php echo $base_url;?>accounts/list">
              <i>A</i>ccounts</a>

              <li>
               <a href="<?= $base_url;?>voucher_series">
                <i>V</i>oucher Series</a>
               </li>

               <li>
                <a href="<?php echo $base_url;?>billsundry">
                 <i>B</i>ill Sundry</a>
                </li>
                
                  <li>
                <a href="<?php echo $base_url;?>taxcategory">
                 <i>T</i>ax Category</a>
                </li>

                <!-- <li class="disable">
                 <a href="#">
                  <i>S</i>ales Management</a>
                 </li> -->

                <?php }  else { ?>
                 <li>
                  <a href="#">Accounts</a>
                 </li>
                 <li>
                  <a href="#">
                   <i>V</i>oucher</a>
                  </li>

                  <li>
                   <a href="#">
                    <i>B</i>ill Sundry</a>
                   </li>
                   <li>
                    <a href="#">
                     <i>T</i>ax Category</a>
                    </li>
                    <!-- <li>
                     <a href="#">
                      <i>S</i>ales Management</a>
                     </li> -->
                    <?php } ?>
                   </ul>
                  </div>
                  <div class="col-md-3 col-6">
                   <ul>
                    <li>
                     <b>
                      <span class="material-symbols-outlined">shelves</span> Stock</a>
                     </b>
                    </li>
                    <?php if($local_session->get('ses_company_id')!=''){ ?>
                     <li>
                      <a href="<?php echo $base_url;?>items/list_items">
                       <i>I</i>tems</a>
                      </li>
                      <li>
                       <a href="<?php echo $base_url;?>material_centres/list_centres">
                        <i>M</i>aterial Centre</a>
                       </li>
                       
                        <?php } else { ?>
                         <li>
                          <a href="#">Items</a>
                         </li>
                         <li>
                          <a href="#">Item classification</a>
                         </li>
                         <li>
                          <a href="#">Material Centre</a>
                         </li>
                         
                        <?php } ?>

                       </ul>
                      </div>
                      <div class="col-md-3 col-6">
                       <ul>
                        <li>
                         <b>
                          <span class="material-symbols-outlined">summarize</span> Reporting</b>
                         </li>
                         <!-- <li class="disable">
                          <a href="#"><i>B</i>udgets</a>
                         </li> -->

                         <li>
                          <a href="<?php echo $base_url;?>cost_centres/list">
                           <i>C</i>ost Centre</a>
                          </li>

                          <li>
                           <a href="<?php echo $base_url;?>project">
                            <i>P</i>roject</a>
                           </li>
						   
						     <li>
                           <a href="<?php echo $base_url;?>billbybill">
                            <i>B</i>ill By Bill</a>
                           </li> 
						      <li>
                           <a href="<?php echo $base_url;?>subledger">
                            <i>S</i>ub Ledger</a>
                           </li> 

                          </ul>
                         </div>
                         
                         <div class="col-md-3 col-6">
                       <ul>
                        <li>
                         <b>
                         <span class="material-symbols-outlined">tune</span>  Utility</b>
                         </li>
                         <!-- <li class="disable">
                          <a href="#"><i>B</i>udgets</a>
                         </li> -->

                        <li>
                                         <a href="<?php echo $base_url;?>bulk_updation">
                                          <i>B</i>ulk Updation</a>
                                         </li>

                          </ul>
                         </div>
                         
                                      </div>
                                     </div>
                                    </li>
    <li class="topnav topdropnav nav-item dropdown">
     <a href="#!"  data-bs-toggle="dropdown" aria-expanded="false">
      <i>T</i>ransactions</a>
      <div class="submenu dropdown-menu" id="transactionsdiv_menu">
       <div class="row m-0">
        <div class="col-md-3 col-6">
         <ul>
          <li>
           <b>
            <span class="material-symbols-outlined">shopping_cart_checkout</span> Sales</b>
           </li>


           <li>
            <a href="<?php echo $base_url;?>sales/item">
             <i>S</i>ale Invoice</a>
            </li>
             <li>
              <a href="<?php echo $base_url;?>credit_note/item">
               <i>C</i>redit Note</a>
              </li>
               </ul>
              </div>
              <div class="col-md-3 col-6">
               <ul>
                <li>
                 <b>
                  <span class="material-symbols-outlined">local_mall</span> Purchases</a>
                 </b>
                </li>
                <li>
				<a href="<?php echo $base_url;?>purchase/item">
                 <i>P</i>urchase Invoice</a>
                 </li>
              
                  <li>
                   <a href="<?php echo $base_url;?>debit_note/item">
                    <i>D</i>ebit Notes</a>
                   </li>
                  
                    </ul>
                   </div>
                   <div class="col-md-3 col-6">
                    <ul>
                     <li>
                      <b>
                       <span class="material-symbols-outlined">account_balance</span> Banking</b>
                      </li>
                      <?php if($local_session->get('ses_company_id')!=''){ ?>
                       <li>
                        <a href="<?php echo $base_url;?>vouchers/invoice/9">
                         <i>P</i>ayments</a>
                        </li>
                        <li>
                         <a href="<?php echo $base_url;?>vouchers/invoice/13">
                          <i>R</i>eceipts</a>
                         </li>
                         <li>
                          <a href="<?php echo $base_url;?>vouchers/invoice/1">
                           <i>C</i>ontra</a>
                          </li>
                          <li>
                           <a href="<?php echo $base_url;?>vouchers/invoice/5">
                            <i>J</i>ournal</a>
                           </li>
                           <li>
                            <a href="<?php echo $base_url;?>vouchers/memorandum/8">
                             <i>M</i>emorandum</a>
                            </li>
                           <?php } else{ ?>
                            <li>
                             <a href="#">Payments</a>
                             <a href="#">Receipts</a>
                             <a href="#">Contra</a>
                             <a href="#">Joined</a>
                            </li>
                           <?php } ?>
                          </ul>
                         </div>
                    <div class="col-md-3 col-6">
                          <ul>
                           <li>
                            <b>
                             <span class="material-symbols-outlined">autorenew</span> Items</b>
                            </li>
                           
                             <li>
                              <a href="<?php echo $base_url;?>physical_verification/add">
                               <i>P</i>hysical Verification</a>
                              </li>
                             
                               <li>
                                <a href="<?php echo $base_url;?>stock_journal/invoice/18">
                                 <i>S</i>tock Journal</a>
                                </li>
                              
                                </ul>
                               </div>
                    <div class="col-md-3 col-6">
                          <ul>
                           <li>
                            <b>
                             <span class="material-symbols-outlined">autorenew</span> Approvals</b>
                            </li>
                           
                             <li>
                              <a href="<?php echo $base_url;?>approvals/txn">
                               <i>T</i>xn Approval</a>
                              </li>
                             
                              
                                </ul>
                               </div>
                               
                              </div>
                             </div>
                            </li>
    <li class="topnav topdropnav nav-item dropdown">
     <a href="#!" data-bs-toggle="dropdown" aria-expanded="false">
      <i>G</i>ST</a>
      <div class="submenu dropdown-menu" id="taxesdiv_menu">
       <div class="row m-0">
        <div class="col-md-3 col-6">
         <ul>
          <li>
           <b>
            <span class="material-symbols-outlined">query_stats</span> PERIODIC</b>
           </li>
		   <li>
            <a href="<?php echo $base_url;?>gst/outwardsupplies">
             <i>O</i>utward Supplies (R1/ CMP08)</a>
			 </li>
			  <li>
            <a href="<?php echo $base_url;?>gst/gstsummary">
             <i>G</i>ST Summary</a>
			 </li>
			 
			  <li>           
             <a href="<?php echo $base_url;?>gst/inwrdspl_2a2b">
              <i>I</i>nward Supplies (2A/2B)</a>
              
                 </li>
           
                </ul>
               </div>

               <!-- <div class="col-md-3 col-6 disable">
                <ul>
                 <li>
                  <b>
                   <span class="material-symbols-outlined">pending_actions</span> Annual</a>
                  </b>
                 </li>
                 <li>
                  <a href="#">
                   <i>G</i>STR-9</a>
                  </li>  <li>
                   <a href="#">
                    <i>G</i>STR-4</a>
                   </li>
                   <li>
                    <a href="#">
                     <i>I</i>ncome Tax</a>
                    </li>  <li>
                     <a href="#">
                      <i>I</i>TC Tagging</a>
                     </li> 
                     <li>
                      <a href="#">
                       <i>E</i>CL</a>
                      </li>
                     </ul>
                    </div> -->



			<div class="col-md-3 col-6">
                <ul>
                 <li>
                  <b>
                   <span class="material-symbols-outlined">pending_actions</span>  E-Way / E-Invoice </a>
                  </b>
                 </li>
                 <li>
                  <a href="<?php echo $base_url;?>etaxes/eway">
                   Manage E-Way </a>
                  </li>  
				   <li>
                  <a href="<?php echo $base_url;?>etaxes/einvoice">
                   Manage E-Invoice </a>
                  </li>
                     </ul>
                    </div>

                   </div>
                  </div>
                 </li>
    <li class="topnav topdropnav nav-item dropdown">
     <a href="#!" data-bs-toggle="dropdown" aria-expanded="false">
      <i>T</i>DS / TCS</a>
      <div class="submenu dropdown-menu" id="taxesdiv_menu">
       <div class="row m-0">

        <div class="col-md-3 col-6 disable">
         <ul>
          <li>
           <b>
            <span class="material-symbols-outlined">insights</span> IT TDS/TCS</a>
           </b>
          </li>
          <li>
           <a href="#">
            <i>F</i>orm 24Q (Salary)</a>
            <a href="#">
             <i>F</i>orm 26Q (Other than salary )</a>
             <a href="#">
              <i>F</i>orm 27Q (Non Resident)</a>
              <a href="#">
               <i>F</i>orm 27EQ (TCS)</a>
             
                 </li>
				  </ul>
				    <ul><li><li>
					</ul>
					 </div>
					<div class="col-md-3 col-6 disable">
				   <ul>
				  <li>
           <b>
            <span class="material-symbols-outlined">insights</span> GST TDS / TCS </a>
           </b>
          </li>
          <li>
           <a href="#">
            <i>G</i>STR-7</a>
            <a href="#">
              <i>G</i>STR-8</a>
                 </li>
                </ul>
               </div>

              </div>
             </div>
            </li>
    <!-- <li class="topnav topdropnav nav-item dropdown">
     <a href="#!" data-bs-toggle="dropdown" aria-expanded="false">
      <i>S</i>tatutory</a>
      <div class="submenu dropdown-menu" id="taxesdiv_menu">
       <div class="row m-0">

        <div class="col-md-3 col-6 disable">
         <ul>
          <li>
           <b>
            <span class="material-symbols-outlined">universal_currency_alt</span> GST Returns</b>
           </li>
           <li>
            <a href="#">
             <i>G</i>STR-1 </a>
            </li> 
            <li>
             <a href="#">
              <i>G</i>STR-2A / 2B </a>
             </li>
             <li>
              <a href="#">
               <i>G</i>STR-3B </a>
              </li>
              <li>
               <a href="#">
                <i>G</i>STR CMP-08 </a>
               </li>
               <li>
                <a href="#">
                 <i>O</i>ther GST Returns</a>
                </li>
               </ul>
              </div>
              <div class="col-md-3 col-6 disable">
               <ul>
                <li>
                 <b>
                  <span class="material-symbols-outlined">universal_currency_alt</span> GST Summaries</b>
                 </li>
                 <li>
                  <a href="#">
                   <i>G</i>ST Computation </a>
                  </li> 

                 </ul>
                </div>

               </div>
              </div>
             </li> -->
    
    <li class="topnav topdropnav nav-item dropdown">
     <a href="#!" data-bs-toggle="dropdown" aria-expanded="false">
      <i>R</i>egister</a>
      <div class="submenu dropdown-menu" id="registerdiv_menu">
       <div class="row m-0">
        <div class="col-md-3 col-6">
         <ul>
          <li>
           <b>
            <span class="material-symbols-outlined">difference</span> Outward</b>
           </li>
           <li>
            <a href="<?php echo $base_url;?>registerlog/sale_register">
             <i>S</i>ales Register</a>
            </li>
           <li>
             <a href="<?php echo $base_url;?>registerlog/sale_return_register">
              <i>S</i>ales Return Register</a>
             </li>
               </ul>
              </div>


              <div class="col-md-3 col-6">
               <ul>
                <li>
                 <b>
                  <span class="material-symbols-outlined">confirmation_number</span> Voucher</b>
                 </li>
				 
                 <li>
                  <a href="<?php echo $base_url;?>registerlog/payment_register">
                   <i>P</i>ayment Register</a>
                  </li>
                  <li>
                   <a href="<?php echo $base_url;?>registerlog/receipt_register">
                    <i>R</i>eceipt Register</a>
                   </li>
                   <li>
                    <a href="<?php echo $base_url;?>registerlog/contra_register">
                     <i>C</i>ontra Register</a>
                    </li>
                    <li>
                     <a href="<?php echo $base_url;?>registerlog/journal_register">
                      <i>J</i>ournal Register</a>
                     </li>
					
                     <li>
                      <a href="<?php echo $base_url;?>registerlog/other_register">
                       <i>O</i>ther Acc. Register</a>
                      </li>
                     </ul>
                    </div>
                    <!-- <div class="col-md-3 col-6" >
                     <ul>
                      <li>
                       <b>
                        <span class="material-symbols-outlined">book</span> Inventory</b>
                       </li>
                      
                        <li>
                         <a href="<?php echo $base_url;?>registerlog/physicalverification">
                          <i>P</i>hysical Verif. Reg</a>
                         </li>
                           <li>
                         <a href="<?php echo $base_url;?>registerlog/others">
                          <i>O</i>ther Stock Reg</a>
                         </li>
                           </ul>
                          </div> -->


                           <div class="col-md-3 col-6">
                           <ul>
                            <li>
                             <b>
                              <span class="material-symbols-outlined">add_chart</span> Inwards</b>
                             </li>
                             <li>
                              <a href="<?php echo $base_url;?>registerlog/purchase_register">P</i>urchase Reg</a>
                             </li>
                             <li>
                              <a href="<?php echo $base_url;?>registerlog/purchase_return_register">
                               <i>P</i>urchase Return Reg</a>
                              </li>
                             
                                </ul>
                               </div> 

                               <!-- <div class="col-md-3 col-6">
                                <ul>
                                 <li>
                                  <b>
                                   <span class="material-symbols-outlined">calculate</span> Taxation</b>
                                  </li>
                                  <li class="disable">
                                   <a href="#">
                                    <i>G</i>ST Summary</a>
                                   </li>
                                   <li class="disable">
                                    <a href="#">
                                     <i>T</i>DS/TCS Summary</a>
                                    </li>
                                    <li class="disable">
                                     <a href="#">
                                      <i>P</i>artywise Summary</a>
                                     </li>
                                     <li class="disable">
                                      <a href="#">
                                       <i>S</i>upply In/Outward Reg</a>
                                      </li>
                                      <li class="disable">
                                       <a href="#">
                                        <i>I</i>TC Register</a>
                                       </li>
                                      </ul>
                                     </div> -->

                                    </div>
                                   </div>
                                  </li>
    <li class="topnav topdropnav nav-item dropdown">
     <a href="#!" data-bs-toggle="dropdown" aria-expanded="false">
      <i>R</i>eports</a>
      <div class="submenu dropdown-menu" id="reportsdiv_menu">
       <div class="row m-0">
        <div class="col-md-3 col-6">
         <ul>
          <li>
           <b>
            <span class="material-symbols-outlined">content_paste_search</span> Accounts</b>
           </li>
           <li>
            <a href="<?php echo $base_url;?>reports/account_ledger">
             <i>A</i>ccount Ledger</a>
            </li>
            <li>
             <a href="<?php echo $base_url;?>reports/account_summary">
              <i>A</i>ccount Summary</a>
             </li>
             <li>
              <a href="<?php echo $base_url;?>reports/bills_management">
               <i>B</i>ills Management</a>
              </li>
           
               </ul>
              </div>


              <div class="col-md-3 col-6">
               <ul >
                <li>
                 <b>
                  <span class="material-symbols-outlined">box_add</span> Stock</b>
                 </li>
                 <li>
                  <a href="<?php echo $base_url;?>reports/stock_ledger">
                   <i>S</i>tock Ledger</a>
                  </li>
                  <li>
                   <a href="<?php echo $base_url;?>stock_summary">
                    <i>S</i>tock Summary</a>
                   </li>
                   <li>
                    <a href="<?php echo $base_url;?>reports/stock_status">
                     <i>I</i>nventory Status</a>
                    </li>
                   <li>
                    <a href="<?php echo $base_url;?>reports/other_stock_report">
                     <i>O</i>ther Inventory Reports</a>
                    </li>
                     </ul>
                    </div>
                    <div class="col-md-3 col-6">
                     <ul>
                      <li>
                       <b>
                        <span class="material-symbols-outlined">book</span> Books</b>
                       </li>
                       <li>
                        <a href="<?php echo $base_url;?>reports/day_book">
                         <i>D</i>ay Book</a>
                        </li>
                     
                         <li>
                          <a href="<?php echo $base_url;?>reports/cost_centre">
                           <i>C</i>ost Centre</a>
                          </li>
                          

                           </ul>
                          </div>


                          <div class="col-md-3 col-6">
                           <ul >
                            <li>
                             <b>
                              <span class="material-symbols-outlined">add_chart</span> Final Account</b>
                             </li>
                             <li>
                              <a href="<?php echo $base_url;?>reports/balance_sheet">
                               <i>B</i>alance Sheet</a>
                              </li>
                              <li>
                               <a href="<?php echo $base_url;?>reports/profit_loss">
                                <i>P</i>rofit & Loss Accounts</a>
                               </li>
                               <li>
                                <a href="<?php echo $base_url;?>reports/trial_balance">
                                 <i>T</i>rial Balance</a>
                                </li>
                                <li>
                                 <!-- <a href="#">
                                  <i>C</i>ash/Fund Flow</a> -->
                                 </li>
                                 <!-- <li class="disable">
                                  <a href="#">
                                   <i>D</i>epreciation Chart</a>
                                  </li> -->
                                  <li>
                                   <a href="<?php echo $base_url;?>reports/project_reporting">
                                    <i>P</i>roject Reporting</a>
                                   </li>
                                  </ul>
                                 </div>

                                 <div class="col-md-3 col-6">
                                  <ul>
                                   <li>
                                    <b>
                                     <span class="material-symbols-outlined">calculate</span> Utilities</b>
                                    </li>
                                 
                                         </ul>
                                        </div>

                                       </div>
                                      </div>
                                     </li>
    <!-- <li class="topnav topdropnav nav-item dropdown">
    <a href="#!" data-bs-toggle="dropdown" aria-expanded="false">
     <i>M</i>IS</a>
     <div class="submenu dropdown-menu" id="misdiv_menu">
      <div class="row m-0">
       <div class="col-md-3 col-6">
        <ul>
         <li>
          <b>
           <span class="material-symbols-outlined">request_quote</span>Financial Statements</b>
          </li>
          <?php if($local_session->get('ses_company_id')!=''){ ?>
           <li class="disable">
            <a href="#">
             <i>A</i>nnual Report</a>
            </li>
            <li class="disable">
             <a href="#">
              <i>P</i>rofitability Analysis</a>
             </li>
             <li class="disable">
              <a href="#">
               <i>R</i>atio Analysis</a>
              </li>
             <?php } else{ ?>
             <?php } ?>
            </ul>
           </div>


           <div class="col-md-3 col-6">
            <ul>
             <li>
              <b>
               <span class="material-symbols-outlined">phonelink_setup</span> O/S Analysis</b>
              </li>
              <?php if($local_session->get('ses_company_id')!=''){ ?>
               <li class="disable">
                <a href="#">
                 <i>B</i>ills Receivable</a>
                </li>
                <li class="disable">
                 <a href="#">
                  <i>B</i>ills Payable</a>
                 </li>
                 <li class="disable">
                  <a href="#">
                   <i>A</i>geing Analysis</a>
                  </li>
                 <?php } else{ ?>
                 <?php } ?>
                </ul>

               </div>
               <div class="col-md-3 col-6">
                <ul>
                 <li>
                  <b>
                   <span class="material-symbols-outlined">box_add</span>Stock</b>
                  </li>
                  <?php if($local_session->get('ses_company_id')!=''){ ?>
                   <li>
                    <a href="#">
                     <i>I</i>nventory 360° View</a>
                    </li>
                    <li>
                     <a href="#">
                      <i>S</i>tock in Hand</a>
                     </li>
                     <li>
                      <a href="#">
                       <i>S</i>tock Exception</a>
                      </li>
                      <li>
                       <a href="#">
                        <i>S</i>tock Valuation</a>
                       </li>
                      <?php } else{ ?>
                      <?php } ?>
                     </ul>
                    </div>


                    <div class="col-md-3 col-6">
                     <ul>
                      <li>
                       <b>
                        <span class="material-symbols-outlined">add_chart</span>
                        <i>M</i>IS Report</b>
                       </li>
                       <li class="disable">
                        <a href="#">
                         <i>T</i>ax Exceptions</a>
                        </li>
                        <li class="disable">
                         <a href="#">
                          <i>C</i>ompliance Calendar</a>
                         </li>
                        </ul>
                       </div>

                      </div>
                     </div>
                    </li>
    <li class="topnav topdropnav nav-item dropdown">
     <a href="#!" data-bs-toggle="dropdown" aria-expanded="false">
     <i>A</i>udit</a>
     <div class="submenu dropdown-menu" id="auditdiv_menu">
      <div class="row m-0">
       <div class="col-md-3 col-6">
        <ul>
         <li>
          <b>
          <span class="material-symbols-outlined">domain</span> Company</b>
         </li>
         <li class="disable">
          <a href="#">
          <i>A</i>I Audit Report</a>
         </li>
        </ul>
       </div>


     <div class="col-md-3 col-6">
      <ul>
       <li>
        <b>
         <span class="material-symbols-outlined">heap_snapshot_large</span> Income Tax</b>
        </li>
        <li class="disable">
         <a href="#">
          <i>I</i>T Audit</a>
         </li>
        </ul>
       </div>
       <div class="col-md-3 col-6">
        <ul>
         <li>
          <b>
           <span class="material-symbols-outlined">content_paste_search</span> GST</b>
          </li>
          <li class="disable">
           <a href="#">
            <i>G</i>ST Audit</a>
           </li>
          </ul>
         </div>


         <div class="col-md-3 col-6">
          <ul>
           <li>
            <b>
             <span class="material-symbols-outlined">summarize</span> TDS/TCS</b>
            </li>
            <li class="disable">
             <a href="#">
              <i>T</i>DS/TCS Audit</a>
             </li>
            </ul>
           </div>

           <div class="col-md-3 col-6">
            <ul>
             <li>
              <b>
               <span class="material-symbols-outlined">quick_reference</span> Others</b>
              </li>
              <li class="disable">
               <a href="#">
                <i>A</i>udit Trial</a>
               </li>
              </ul>
             </div>

            </div>
           </div>
    </li> -->
   </ul>
  </div>

  <?php } else { ?>
    <a title="Back To Group Company"  class="text-center" href="<?= base_url() ?>grpcomp">
      <img src="<?php echo base_url();?>public/assets/img/grp_comp_img.jpg" alt="" style="width: 100%;height: 50px;">
    </a>
  <?php } ?>


  <div class="rightpanel">

   <div class="row">
    <div class="col-md-10 ms-auto">
     
     <div class="row ms-auto">
      <ul class="navbar-nav  navbar-nav-icons flex-row  ms-auto">
       <li class="nav-item navactivities">
         <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
          <span class="material-symbols-outlined">device_reset</span>
         </a>
         <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navsetting">
          <div class="card position-relative border-0">
           <div class="card-body p-0">
            <h6 class="mt-4 px-3 text-black">Recent Activities</h6>
            <div class="mb-3 mx-3">
             <input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Search Settings" />
            </div>
           </div>
           <div class="overflow-auto scrollbar" style="height:35rem;">
            <ul class="nav d-flex flex-column mb-2 pb-1 myactivitylist">
             <?php if(!empty($loglist)){ foreach($loglist as $key => $value){ ?>
              <li class="nav-item">
               <a class="nav-link px-3" href="javascript:void(0)"> <b> 
                <?php echo $value['erp_activity_log'] ?></b>
                <span> <?php echo $value['erp_activity_date_time'] ?> 
               </span>
              </a>
             </li>
            <?php }} ?>

           </ul>
           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin/dashboard/logs">View More</a>
          </div>
         </div>
        </div>
       </li>
       <li class="nav-item settingsnav">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
         <span class="material-symbols-outlined">settings_suggest</span>
        </a>
        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navsetting">
         <div class="card position-relative border-0">
          <div class="card-body p-0">
           <h6 class="my-3 px-3 text-black">Theme Color <a href="#" class="text-muted float-end main_theme">Default</a>
           </h6>
           <div class="row px-4">
            <p class="col-3">
             <a href="#" class="d-block h-100 red_theme">&nbsp;</a>
            </p>
            <p class="col-3">
             <a href="#" class="d-block h-100 orng_theme">&nbsp;</a>
            </p>
            <p class="col-3">
             <a href="#" class="d-block h-100 sky_theme">&nbsp;</a>
            </p>
            <p class="col-3">
             <a href="#" class="d-block h-100 purpl_theme">&nbsp;</a>
            </p>
            <p class="col-3">
             <a href="#" class="d-block h-100 yelow_theme">&nbsp;</a>
            </p>
            <p class="col-3">
             <a href="#" class="d-block h-100 part_theme">&nbsp;</a>
            </p>
            <p class="col-3">
             <a href="#" class="d-block h-100 grn_theme">&nbsp;</a>
            </p>
            <p class="col-3">
             <a href="#" class="d-block h-100 blue_theme">&nbsp;</a>
            </p>
           </div>
           <hr>
           <div class="row m-0  px-4">
            <p class="row p-0">
             <label class="w-50">Font Family</label>
             <select name="family" class="form-select form-select-sm w-50">
              <option data-id="" <?php echo ($theme_fontfamily=='')? 'selected' :'' ?>>Noto Sans</option>
              <option data-id="montserrat" <?php echo ($theme_fontfamily=='font_montserrat')? 'selected' :'' ?>>Montserrat</option>
              <option data-id="mooli" <?php echo ($theme_fontfamily=='font_mooli')? 'selected' :'' ?>>Mooli</option>
              <option data-id="roboto" <?php echo ($theme_fontfamily=='font_roboto')? 'selected' :'' ?>>Roboto Slab</option>
             </select>
            </p>
            <p class="row p-0">
             <label class="w-50">Font Size </label> <span class="w-50 btn-group p-0">
              <a href="#" data-size="zoomnormal" class="btn <?php echo ($theme_fontsize=='font-zoomnormal')?'btn-success':'btn-outline-success';?>  zoomnormal p-2" aria-current="page">A</a>
              <a href="#" data-size="zoomin" class="btn <?php echo ($theme_fontsize=='font-zoomin')?'btn-success':'btn-outline-success';?>  p-2 zoomin">A+</a>
              <a href="#" data-size="zoommore" class="btn <?php echo ($theme_fontsize=='font-zoommore')?'btn-success':'btn-outline-success';?>  p-2 zoommore">A++</a> </span>
             </p>
            </div>
            <hr>
            <!--<h6 class="my-3 px-3 text-black">Notification</h6>-->
            <!--<div class="row m-0 px-4 position-relative">-->
            <!-- <div class="position-absolute left-0 top-0 right-0 bottom-0" style="background:rgba(255,255,255,0.4)">-->
            <!-- </div>-->
            <!-- <p class="form-check form-switch">-->
            <!--  <label>Missed Activity Email</label>-->
            <!--  <input class="form-check-input" type="checkbox" role="switch" name="missed_activity">-->
            <!-- </p>-->
            <!-- <p class="form-check form-switch">-->
            <!--  <label>Show Preview Message</label>-->
            <!--  <input class="form-check-input" type="checkbox" role="switch" name="preview_msg">-->
            <!-- </p>-->
            <!-- <p class="form-check form-switch">-->
            <!--  <label>Desktop Notification</label>-->
            <!--  <input class="form-check-input" type="checkbox" role="switch" name="desk_notice">-->
            <!-- </p>-->
            <!-- <p class="form-check form-switch">-->
            <!--  <label>Sound Notification</label>-->
            <!--  <input class="form-check-input" type="checkbox" role="switch" name="sound_notice">-->
            <!-- </p>-->
            <!--</div>-->
            <!--<hr>-->
            <!--<h6 class="my-3 px-3 text-black">Display & Sound</h6>-->
            <!--<div class="row m-0 px-4 position-relative">-->
            <!-- <div class="position-absolute left-0 top-0 right-0 bottom-0" style="background:rgba(255,255,255,0.4)">-->
            <!-- </div>-->
            <!-- <p class="row p-0">-->
            <!--  <label class="w-50">Notification Sound</label>-->
            <!--  <select name="notify_sound" class="form-select form-select-sm w-50">-->
            <!--   <option>Option 1</option>-->
            <!--   <option>Option 2</option>-->
            <!--  </select>-->
            <!-- </p>-->
            <!-- <p class="row p-0">-->
            <!--  <label class="w-50">Audio Device</label>-->
            <!--  <select name="device" class="form-select form-select-sm w-50">-->
            <!--   <option>Size 25</option>-->
            <!--   <option>Size 50</option>-->
            <!--  </select>-->
            <!-- </p>-->
            <!-- <p class="row p-0">-->
            <!--  <label class="w-50">Speaker</label>-->
            <!--  <select name="speaker" class="form-select form-select-sm w-50">-->
            <!--   <option>Size 25</option>-->
            <!--   <option>Size 50</option>-->
            <!--  </select>-->
            <!-- </p>-->
            <!--</div>-->

            <div class="d-grid py-2 px-3">
             <a href="<?= base_url() ?>admin/settings/general" class="btn btn-outline-success btn-block">View All</a>
             <p class="my-3">&nbsp;</p>
            </div>

           </div>
          </div>
         </div>                    
       </li>
        <li class="nav-item dropdown notifynav">
        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
         <span class="material-symbols-outlined">notifications</span>
        </a>
        <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border border-300 navbar-dropdown-caret" id="navNotfication" aria-labelledby="navNotfication">
         <div class="card position-relative border-0">
          <div class="card-header p-2">
           <div class="d-flex justify-content-between">
            <h5 class="text-black mb-0">Notificatons</h5>
            <button class="btn btn-link p-0 fs--1 fw-normal" type="button">Mark all as read</button>
           </div>
          </div>
          <div class="card-body p-0">
           <div class="scrollbar-overlay" style="height: 27rem;">
            <div class="border-300">

 <?php if(!empty($notifiationlist)){ foreach($notifiationlist as $key => $value){ ?>
             <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative read border-bottom">
              <div class="d-flex align-items-center justify-content-between position-relative">
               <div class="d-flex">
               <!-- <div class="avatar avatar-m status-online me-3">
                 <img class="rounded-circle" src="<?php echo base_url();?>public/assets/img/30.webp" alt="" />
                </div>-->
                <div class="flex-1 me-sm-3">
                 <p class="mb-0"><a href="<?php echo base_url();?>admin/vouchers/edit/<?php echo $value['vch_type_id'];?>/<?php echo $value['vch_txn_id'];?>/notifread"><?php echo $value['notify_log'];?>.<span class="ms-2 text-400 fw-bold fs--2">
                     <?php 
                        $notify_time = new DateTime($value['notify_date_time']);
                         $current_time = new DateTime();
                        $interval = $current_time->diff($notify_time);
                        
                        $parts = [];
                        
                        // add only if non-zero
                        if ($interval->d > 0) {
                            $parts[] = $interval->d . ' day' . ($interval->d > 1 ? 's' : '');
                        }
                        if ($interval->h > 0) {
                            $parts[] = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '');
                        }
                        if ($interval->i > 0) {
                            $parts[] = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
                        }
                        
                        // if all are zero (less than a minute)
                        if (empty($parts)) {
                            $parts[] = 'just now';
                        } else {
                            $parts[] = 'ago';
                        }

                echo implode(' ', $parts); ?></span>
                 </p></a>
                 <p class="text-800 fs--2 mb-0"><?php  echo date('M d, Y h:i A',strtotime($value['notify_date_time']));?></p>
                </div>
               </div>
               <div class="font-sans-serif d-none d-sm-block">
                <button class="btn fs--2 btn-sm" type="button">
                 <span class="fas fa-ellipsis-h fs--2 text-900">
                 </span>
                </button>

               </div>
              </div>
             </div>
             <?php } } ?>
            </div>

           </div>
          </div>

          <div class="card-footer p-0 border-top border-0">
           <div class="my-2 text-center fw-bold fs--2 text-600">
            <a class="fw-bolder text-dark" href="#">Notification history</a>
           </div>
          </div>
         </div>
        </div>
       </li> 



       <li class="nav-item dropdown helpnav">
        <a class="nav-link" id="navhelpdesk" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">
         <span class="material-symbols-outlined">question_exchange</span>
        </a>
        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-helpdesk shadow border border-300" aria-labelledby="navhelpdesk">
         <div class="card bg-white position-relative border-0">
          <div class="card-body pt-3 px-3 pb-0 overflow-auto scrollbar" style="height:25rem;">
           <div class="row text-center align-items-top gx-0 gy-0">
            <div class="col-4">
             <a href="https://aicountly.com/help/learning/" target="_blank">
              <img src="<?php echo base_url();?>public/assets/img/icon2.png" class="w-75" alt=""/>
              <p>Countly Learning</p>
             </a>
            </div>
            <div class="col-4">
             <a href="#!" data-bs-toggle="modal" data-bs-target="#FaqModal">
              <img src="<?php echo base_url();?>public/assets/img/e-sahayak-slogo.png" class="w-75" alt=""/>
              <p>E-sahayak </p>
             </a>
            </div>
            <div class="col-4">
             <a href="https://aicountly.com/support/" target="_blank">
              <img src="<?php echo base_url();?>public/assets/img/icon1.png" class="w-75" alt=""/>
              <p>Help Desk</p>
             </a>
            </div>
            <div class="col-4">
             <a href="https://aicountly.com/support/" target="_blank">
              <img src="<?php echo base_url();?>public/assets/img/icon3.png" class="w-75" alt=""/>
              <p>Support</p>
             </a>
            </div>
            <div class="col-4">
             <a href="#!" onClick="showtour();">
              <img src="<?php echo base_url();?>public/assets/img/icon4.png" class="w-75" alt=""/>
              <p>Aicountly Tour</p>
             </a>
            </div>
            <div class="col-4">
             <a href="#!" data-bs-toggle="modal" data-bs-target="#ShortcutModal">
              <img src="<?php echo base_url();?>public/assets/img/icon5.png" class="w-75" alt=""/>
              <p>Keybord Shortcuts</p>
             </a>
            </div>
            <div class="col-4 offset-4">
             <a href="https://aicountly.com/help/community/" target="_blank">
              <img src="<?php echo base_url();?>public/assets/img/icon6.png" class="w-75" alt=""/>
              <p>Forums</p>
             </a>
            </div>
           </div>
          </div>
         </div>
        </div>
       </li>
      </ul>
     </div>

     <div class="row ms-auto">
       <ul class="navbar-nav  navbar-nav-icons flex-row  ms-auto" style="padding: 0;">
        <li class="nav-item companynav me-md-3 me-lg-4">
         <a class="nav-link button" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
         <span class="usericon material-symbols-outlined d-md-none d-block float-start">account_circle</span>
         <i class="d-md-block d-none float-start">
          <?php echo short_str($s_name);?>
            (<?= company()->fy_short ?> | <?= substr($bo_name, 0, 3) ?>)
          </i> 
          <span class="expand material-symbols-outlined d-md-block d-none">expand_more</span>
          
         </a>
          <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navsetting">
           <div class="card position-relative border-0">
            <div class="card-body p-0">
             <p class="mt-4 px-3 text-black text-center">
              <?php echo $company_name;?> <br>
              GSTIN: 123,
              <?php if($bo_name){ ?>
               Branch: <?php echo $bo_name; }  ?>,
               Comp ID: <?= $user_id ?>
              </p>
              <h6 class="mt-4 px-3 text-black">My Company Profile</h6>


              <style>
               .relativedrop{height:auto!important; width:100%!important; position:relative!important;}
              </style>

              <div class="overflow-auto">
               <ul class="nav d-flex flex-column mb-2 pb-1">
                <li class="nav-item">
                 <a class="nav-link px-3" href="<?php echo $base_url;?>company/modify"> <span class="me-2 material-symbols-outlined">store</span>Company Master</a>
                </li>
                <!-- <li class="nav-item">
                 <a class="nav-link px-3" href="#!"> <span class="me-2 material-symbols-outlined">shield_lock</span>Data Freezing</a>
                </li> -->
                <li class="nav-item">
                 <a class="nav-link px-3" data-bs-toggle="collapse" href="#collapseExampleFY" role="button" aria-expanded="false" aria-controls="collapseExampleFY"> <span class="me-2 material-symbols-outlined">edit_calendar</span>Change Finanical Year <small class="ps-2">▽</small>
                 </a> </li>
                 <div class="collapse" id="collapseExampleFY">
                  <?php 
				  
				  
				  if($all_fy_list){ foreach($all_fy_list as $fy_row){
					
					if($fy_row['is_imported']=='1'){ 
						$selfy_change=' selfy_change';
                   }else
					    $selfy_change=' retrycompfy';
			   
                    $isSelected = ($fy_row['cmpfymastr_id'] == $sel_fy);
                    $starClass = $isSelected ? 'active-star' : '';
                   if($fy_row['cmpfymastr_id'] == $ses_comp_fy_id){
                    $rowbg = 'background-color:#cbd0dd';
                    $activeclass=' active';
                    $fontbold ='font-weight:bold;';
                   }
                   else{
                    $rowbg ='';
                    $activeclass='';
                    $fontbold='';
                   }
                 
                   $profile['user_registration_user_login']=array(
                    "label"=>"First Name",
                    "description"=>"",
                    "type"=>"text","placeholder"=>"",
                    "field_key"=>"billing_first_name",
                    "required"=>"1","default"=>"");

                   
                    ?>
                    <li class="nav-item hover-show-img" style="<?php echo $rowbg;?>">
                     <a class="nav-link px-3 <?php echo $selfy_change;?>
                     <?php echo $activeclass;?>" style="<?php echo $fontbold;?>" data-id="<?php echo $fy_row['cmpfymastr_id'];?>" href="javascript:void(0);">
                     <?php echo date('Y',strtotime($fy_row['fy_beg_date']));?> - <?php echo date('y',strtotime($fy_row['fy_end_date']));?>(<?php echo date('d M, Y',strtotime($fy_row['fy_beg_date']));?> - <?php echo date('d M, Y',strtotime($fy_row['fy_end_date']));?>)
                     
                    </h6>
                   </a>
                      <i class="fa-solid fa-star star-icon" data-id=""></i>

                  </li>
                 <?php } } ?>
				 <?php echo $show_new_fybutton;?>		                 
                </div>


                <li class="nav-item">
                 <a class="nav-link px-3" href="<?php echo base_url();?>admin/rewritebooks"> <span class="me-2 material-symbols-outlined">source_notes</span>Rewrite Books</a>
                </li>
                
                <li class="nav-item">
                 <a class="nav-link px-3" href="<?php echo base_url();?>company_access"> <span class="me-2 material-symbols-outlined">history</span>Company Access</a>
                </li>
                <li class="nav-item">
                 <a class="nav-link px-3" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"> <span class="me-2 material-symbols-outlined">apartment</span>Offices & Branches <small class="ps-2">▽</small>
                 </a> </li>

                 <div class="collapse" id="collapseExample"> 
                 <?php if($all_bo_lists){
                          foreach ($all_bo_lists as $row) {

                            // Highlight row if selected
                            $isSelected = ($sel_branch == $row['hobo_id']);
                            if($row['hobo_id'] == $bo_id){
                              $rowbg = 'background-color:#cbd0dd';
                              $fontbold ='font-weight:bold;';
                             }
                             else{
                              $rowbg ='';
                              $fontbold ='';
                             } 
                           if($row['mark_ho']=='1')
                    $ho_label =' (HO)';
                   else 
                    $ho_label ='';
                   ?>
                     
                            <li class="nav-item hover-show-img" style="<?php echo $rowbg; ?>">
                            <a class="nav-link px-3 selbo_link" style="<?php echo $fontbold;?>" data-id="<?php echo $row['hobo_id'];?>" href="javascript:void(0);">
                     <?php echo $row['hobo_name'];?>
                     <?php echo $ho_label;?>
                    </a>

                            <i class="fa-solid fa-star star-icon" data-id=""></i>

                            </li>
                        <?php
				 } }
                        ?>


                 </div> 

                 <li class="nav-item">
                  <a class="nav-link px-3" href="<?php echo $base_url;?>dashboard/close_company?<?php echo $raw_compid;?>"> <span class="me-2 material-symbols-outlined">room_preferences</span>Close Company</a>
                 </li>
                </ul>

               </div>
              </div>
             </div>
            </div>
           </li>
       </ul>
     </div>
      
    </div>
    <div class="col-md-2 m-auto" style="padding: 0px;">
     <ul class="navbar-nav  navbar-nav-icons flex-row">
      <li class="nav-item dropdown usernav">
       <a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
       <div class="avatar avatar-l ">
	    <img id="user_logo_top" loading="lazy" class="rounded-circle " src="<?= base_url() ?>user/logo" alt="" onerror="this.src='<?php echo base_url();?>public/assets/img/user.jpg';"/>
       </div>
       </a>
       <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navbarDropdownUser">
        <div class="card position-relative border-0">
         <div class="card-body p-0">
          <div class="row">
           <div class="col-4 text-center pt-4 ps-5">
             <div class="avatar-xl">
			  <a href="javascript:void(0)" id="change_userphoto" class="text-dark" style="float: right;" data-bs-toggle="modal" data-bs-target="#userphotoModal2">
			 <span class="material-symbols-outlined">
			  edit
			 </span>
			</a>
              <img id="user_logo_sidebar" loading="lazy" class="rounded-circle" src="<?= base_url() ?>user/logo" onerror="this.src='<?php echo base_url();?>public/assets/img/user.jpg';" alt="" />
             </div>
           </div>
           <div class="col-8 pt-1">
            <table id="dp_table" class="table table-borderless table-sm">
             <tbody>
              <tr>
               <td>User ID: </td>
               <td>
                <?= auth()->id ?>
               </td>
              </tr>
              <tr>
               <td>Org. ID: </td>
               <td>60020948365</td>
              </tr>
              <tr>
               <td>Email: </td>
               <td>
                <?= auth()->email ?>
               </td>
              </tr>
              <tr>
               <td>Phone: </td>
               <td>
                <?php if(auth()->phone){ ?>
                <?= auth()->phone ?>
                <?php } else { ?>
                <a href="<?php echo base_url() ?>home/personal_info" target="_blank" class="badge bg-primary">Update</a>
                <?php } ?>
               </td>
              </tr>
             </tbody>
            </table>

            </div>
          </div>
          <div class="mx-3 mt-1">
           <a href="<?php echo base_url() ?>home/my_account" target="_blank" class="btn btn-outline-success w-100">Manage My Aicountly Account</a>
          </div>
          <div class="mx-3 mt-1">
           <input class="form-control form-control-sm" id="searchCompaniesFromHeader" type="search" autocomplete="off" placeholder="Search" />
          </div>

          <div class="overflow-auto scrollbar" style="height: 15rem;overflow: auto">
            <ul class="nav mb-2 pb-1" id="searchCompaniesFromHeader_ul">
            <?php if($AllCompanyList){ ?>
            <?php foreach($AllCompanyList as $value){ ?>
              <li class="nav-item">
                <a class="nav-link px-3 selcompany_link" data-comp_id="<?php echo $value['comp_id'];?>" data-comp_type="<?php echo $value['comp_type'];?>" href="javascript:void(0);">
                <h5>
                  <?php echo $value['company_name'];?>
              </h5>
                  Organization: <?php echo $value['comp_code'];?>
                </a>
              </li>
            <?php } ?>
            <?php } ?>
            </ul>
          </div>

          <div class="card-footer p-0 border-top">
           <ul class="nav d-flex flex-column my-3">
            <li>
             <a class="nav-link px-3 text-dark" href="<?php echo base_url();?>home/add_company"> + Add another company</a>
            </li>
           </ul>
           <hr />

           <div class="px-3"> 
            <a class="btn d-flex flex-center w-100" href="<?php echo base_url();?>admin/signout">Sign out</a>
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
   </div>

      
   
    
  </div>
 </div>
</nav>


<div class="row mb-4 myalltabs px-3 bg bg-success" style="margin-top: 4.1rem;position: fixed;">
 <div class="overflow-div" style="overflow-x: unset;">
<div class="d-flex align-items-center" style="position: relative; width: 220px; min-width: 340px;">

  <input type="text" id="commandInput" class="form-control" placeholder="Command Line" style="width: 100%;">

  <!-- Suggestions Dropdown -->
  <!-- <div id="suggestionsDropdown" 
       style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background: white; max-height: 200px; overflow-y: auto; border: 1px solid #ccc; z-index: 9999;">
  </div> -->

  <!-- Help Icon (separate, flex child) -->
  <div class="helpdropdown dropdown bg-white" style="left: -39px;height: 34px;">
    <span class="material-symbols-outlined" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      contact_support
    </span>
    <div class="dropdown-menu navbar-dropdown-caret" >
      Here you can type shortcode to open any window
    </div>
  </div>

</div>




  <div class="col-12 mx-1 w-auto overflow-auto">
   <a  href="<?php echo base_url();?>admin/dashboard" target="_blank"> <span class="btn" style="width:auto;"  role="button">+</span>
   </a>
  </div>
 
 </div> 
</div>
                        
                       
                        
<div class="offcanvas offcanvas-start sidenavbar" tabindex="-1" id="sidenav" aria-labelledby="sidenavLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sidenavLabel">Apps</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
    </button>
  </div>
  <div class="offcanvas-body">
    <div class="accordion" id="accordionApp">
      <div class="accordion-item">
               <a href="https://www.aicountly.com/help/community/" class="accordion-button collapsed">
          Community
        </a>
        <!-- <div id="appmenu1" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionApp">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <a href="#">An item</a>
            </li>
            <li class="list-group-item">
              <a href="#">A second item</a>
            </li>
            <li class="list-group-item">
              <a href="#">A third item</a>
            </li>
            <li class="list-group-item">
              <a href="#">A fourth item</a>
            </li>
          </ul>
        </div> -->
      </div>
      <div class="accordion-item">
       <a href="https://contacts.aicountly.com/" class="accordion-button collapsed">
          Contacts
        </a>

       <!-- <div id="appmenu2" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionApp"> -->
        <!-- <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <a href="#">An item</a>
          </li>
          <li class="list-group-item">
            <a href="#">A second item</a>
          </li>
          <li class="list-group-item">
            <a href="#">A third item</a>
          </li>
          <li class="list-group-item">
            <a href="#">A fourth item</a>
          </li>
        </ul> -->
        <!-- </div> -->
      </div>
      <div class="accordion-item">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appmenu3" aria-expanded="false" aria-controls="appmenu3">
         My Account
       </button>
       <div id="appmenu3" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionApp">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <a href="https://my.aicountly.com/admin/people_sharing">People & Sharing</a>
          </li>
          <li class="list-group-item">
            <a href="https://my.aicountly.com/admin/business_id">Business ID</a>
          </li>
        </ul>
        </div>
      </div>
    </div>   

  </div>
</div>
   <script>
window.addEventListener('load', function() {
    // Load logo after everything else is loaded
    const logo = document.getElementById('comp_logo_sidebar');
    if (logo && logo.dataset.src) {
        logo.src = logo.dataset.src;
    }
});
</script>                   
                        
<div class="content">
  <div class="pb-5">