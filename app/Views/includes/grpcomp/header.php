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

$s_ctab = $local_session->get('s_ctab');
$menuid = time();

$common_model->update_user_taburl($s_ctab,$title,current_url(),$menuid);

$user_tabs           =  $local_session->get('menu_item');

$user_comp_list      =  $local_session->get('suser_comp_list');


$is_unmapped_company_exists = $common_model->is_unmapped_company_exists();
  // if($is_unmapped_company_exists>0)
	  // echo "Bhupi";
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
  <title><?php echo $title;?> - <?php echo SITE_NAME;?></title>
  <!-- ===============================================-->
  <!--    Favicons-->
  <!-- ===============================================-->
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url();?>/public/assets/img/favicon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url();?>/public/assets/img/favicon.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url();?>/public/assets/img/favicon.png">
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>/public/assets/img/favicon.ico">
  <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/css/datatables.css">
  <!-- ===============================================-->
  <!--    Stylesheets-->
  <!-- ===============================================-->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@200" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/css/theme.min.css" type="text/css" id="style-default">
  <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/css/jquery-ui.css" />
  <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/grid/pqgrid.dev.css" />
  <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/grid/pqgrid.ui.dev.css" />
  <link rel="stylesheet" href="<?php echo base_url();?>/public/assets/grid/themes/Office/pqgrid.css" />
  <script> var baseurl ='<?php echo base_url();?>'; </script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .table-sm>:not(caption)>*>* {
      padding: 0.05rem 0.05rem;
    }

  </style>
  <!-- Add IntroJs styles -->
  <link href="<?php echo base_url();?>/public/assets/css/tour.css" rel="stylesheet">
<!--[if lte IE 8]>
    <link href="<?php //echo base_url();?>/public/assets/css/tour-ie.css" rel="stylesheet">
  <![endif]-->
  </head>
  <body>
    <script>
      var SYSTEM_CURRENCY = '<?= settings()->currency ?>';
      var is_unmpd_cmp_exs='<?php echo $is_unmapped_company_exists;?>';
    </script>
    <?php
    
    $comp_fy_year='N/A';
    $ses_company_gstin='N/A';
    $raw_compid ='';


    $folder_path       = getenv('GroupPath');
    $base_url          = base_url().'/'.$folder_path;
    $admin_url          = base_url().'/'.getenv('AdminPath');
    $f_name            = $local_session->get('f_name');
    $m_name            = $local_session->get('m_name');
    $l_name            = $local_session->get('l_name');
    $email_id          = $local_session->get('email'); 
    $s_name          = $local_session->get('ses_grp_company_name');
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
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navshortcut" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navshortcut">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">open_in_new</span><span class="nav-link-text">Shortcut</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navshortcut">
                      <li class="collapsed-nav-item-title d-none">Shortcuts</li>
                      <li class="nav-item"><a href="#">Task Shortcut</a></li>
                      <li class="nav-item"><a href="#">Task 2</a></li>
                      <li class="nav-item"><a href="#">3rd Task Shortcut</a></li>
                      <li class="nav-item"><a href="#">Task Shortcut</a></li>
                    </ul>
                  </div></div>
                  
                  <!-- Bookings Menu-->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navbooking" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navbooking">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">devices_other</span><span class="nav-link-text">My Office Tool</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navbooking">
                      <li class="collapsed-nav-item-title d-none">My Office Tool</li>
                      <li class="nav-item"><a href="<?php base_url() ?>/admin/office_tools">Calender</a></li>
                      <li class="nav-item"><a href="#" data-bs-toggle="modal" data-bs-target="#notesModal">Sticky Notes</a></li>
                      <li class="nav-item"><a href="#" data-bs-toggle="modal" data-bs-target="#calcModal">Calculator</a></li>
                    </ul>
                  </div></div>
                  <!-- Sales Menu-->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navsales" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navsales">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">shopping_cart_checkout</span><span class="nav-link-text">Sales</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navsales">
                      <li class="collapsed-nav-item-title d-none">Sales</li>
                      <li class="nav-item"><a href="#">Sales Dashboard</a></li>
                      <li class="nav-item"><a href="#">Sales  Invoice </a></li>
                      <li class="nav-item"><a href="#">Delivery Challan </a></li>
                      <li class="nav-item disable"><a href="#">Sales Order</a></li>
                      <li class="nav-item disable"><a href="#">Customer Estimate</a></li>
                      <li class="nav-item"><a href="#">Sales Returns</a></li>
                    </ul>
                  </div></div>
                  <!-- Sales Menu-->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navpurchase" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navpurchase">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">local_mall</span><span class="nav-link-text">Purchase</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navpurchase">
                      <li class="collapsed-nav-item-title d-none">Purchase</li>
                      <li class="nav-item"><a href="#">Purchase Dashboard</a></li>
                      <li class="nav-item"><a href="#">Inward Challan</a></li>
                      <li class="nav-item disable"><a href="#">Purchase Order</a></li>
                      <li class="nav-item disable"><a href="#">Purchase Estimate</a></li>
                      <li class="nav-item"><a href="#">Purchase Returns</a></li>
                    </ul>
                  </div></div>
                  <!-- Payment Menu-->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navpay" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navpay">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">redeem</span><span class="nav-link-text">Payments</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navpay">
                      <li class="collapsed-nav-item-title d-none">Payments</li>
                      <li class="nav-item"><a href="#">Cash Payments</a></li>
                      <li class="nav-item"><a href="#">Cash Deposit</a></li>
                      <li class="nav-item"><a href="#">Contra</a></li>
                      <li class="nav-item"><a href="#">Pending Payment</a></li>
                      <li class="nav-item"><a href="#">All Payment</a></li>
                      <li class="nav-item"><a href="#">Clear Payment</a></li>
                      <li class="nav-item"><a href="#">Bank Transfer</a></li>
                    </ul>
                  </div></div>
                  <!-- Report Menu-->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navreport" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navreport">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">post_add</span><span class="nav-link-text">Receipts</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navreport">
                      <li class="collapsed-nav-item-title d-none">Receipts</li>
                      <li class="nav-item"><a href="#">All Receipt</a></li>
                      <li class="nav-item"><a href="#">Cash Receipt</a></li>
                      <li class="nav-item"><a href="#">Bank Receipt</a></li>
                      <li class="nav-item"><a href="#">Pending Receipt</a></li>
                      <li class="nav-item"><a href="#">Settle Receipt</a></li>
                    </ul>
                  </div></div>
                  <!-- My Reports -->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navmyreport" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navmyreport">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">stack_star</span><span class="nav-link-text">My Favourite</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navmyreport">
                      <li class="collapsed-nav-item-title d-none">My Favourite</li>
                      <li class="nav-item"><a href="#">Tasks</a></li>
                    </ul>
                  </div></div>
                  <!-- E-Filing -->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator " href="#navefiling" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navefiling">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">inventory</span><span class="nav-link-text">E-Filing</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navefiling">
                      <li class="collapsed-nav-item-title d-none">E-Filing</li>
                      <li class="nav-item disable"><a href="#">GST Outward Supplies </a></li>
                      <li class="nav-item disable"><a href="#">GST Inward Supplies </a></li>
                      <li class="nav-item disable"><a href="#">GST Computation</a></li>
                      <li class="nav-item disable"><a href="#">TDS E-Filings</a></li>
                      <li class="nav-item disable"><a href="#">TCS E-Filings</a></li>
                      <li class="nav-item disable"><a href="#">Other E- Filing</a></li>
                    </ul>
                  </div></div>
                  <!-- E-Filing -->
                  <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator" href="#navedocs" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="navedocs">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">folder_copy</span><span class="nav-link-text">My Documents</span>
                    </div>
                  </a>
                  <div class="parent-wrapper label-1">
                    <ul class="nav collapse parent" data-bs-parent="#navVertical" id="navedocs">
                      <li class="collapsed-nav-item-title d-none">My Documents</li>
                      <li class="nav-item disable"><a href="#">All Documents</a></li>
                      <li class="nav-item disable"><a href="#">Shared With Me</a></li>
                      <li class="nav-item disable"><a href="#">My Shared Documents</a></li>
                    </ul>
                  </div></div>
                  <div class="nav-item-wrapper"><a class="nav-link" href="<?php base_url() ?>/admin/contacts" role="button">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined">perm_phone_msg</span><span class="nav-link-text">Contacts</span>
                    </div></a></div>
                  </li>
                </ul>
                
                <div class="company-logo text-center"><img class="rounded-circle" src="<?php echo base_url();?>/public/assets/img/user.jpg" alt="">
                </div>
              </div>
            </div>
            <div class="navbar-vertical-footer"><button class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center"><span class="arrow material-symbols-outlined fs-0">arrow_back</span><span class="navbar-vertical-footer-text ms-2">Collapsed View</span></button></div>
          </nav>
          <nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
            <div class="collapse navbar-collapse justify-content-between">
              <div class="navbar-logo">
                <button class="btn navbar-toggler navbar-toggler-humburger-icon m-0 hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navVerticalNav" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                <span class="toogletnav material-symbols-outlined d-lg-none d-block">apps</span>
                <a class="btn btn-white" data-bs-toggle="offcanvas" href="#sidenav" role="button" aria-controls="sidenav" style="padding:0px 8px"><span class="material-symbols-outlined dropmenu" style="line-height:42px;">lists</span></a>

                <a class="navbar-brand me-1 me-sm-3" href="<?php echo $base_url;?>dashboard"><img src="<?php echo base_url();?>/public/assets/img/logo.png" alt="aicountly" width="130" /></a>

              </div>
              <div class="topnavbar" style="overflow:inherit;">
                <ul class=" navbar-nav flex-row">

                  <li><a href="<?php echo $base_url;?>dashboard"><img src="<?php echo base_url();?>/public/assets/img/home.png"></a></li>

                  <li class="topnav topdropnav nav-item dropdown">
                    <a href="#!"  data-bs-toggle="dropdown" aria-expanded="false" id="mastertab"><i>M</i>asters</a>
                    <div class="submenu dropdown-menu" id="masterdiv_menu"><div class="row m-0">
                      <div class="col-md-3 col-6">
                        <ul>
                          <li><b><span class="material-symbols-outlined">home_work</span> Company</b></li>

                          <li><a href="<?php echo $base_url;?>accounts/list"><i>A</i>ccounts</a>

                            <li class="disable"><a href="#"><i>V</i>oucher</a></li>
                            <li class="disable"><a href="#"><i>B</i>udgets</a></li>
                            <li class="disable"><a href="#"><i>B</i>ill Sundry</a></li>
                            <li><a href="#"><i>T</i>ax Category</a></li>
                            <li class="disable"><a href="#"><i>S</i>ales Management</a></li>



                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">shelves</span> Stock</a></b></li>

                            <li><a href="<?php echo $base_url;?>items/list_items"><i>I</i>tems</a></li>
                            <li><a href="<?php echo $base_url;?>material_centres/list_centres"><i>M</i>aterial Centre</a></li>
                            <li class="disable"><a href="#"><i>B</i>ill of Material</a></li>
                            <li><a href="<?php echo $base_url;?>cost_centres/list"><i>C</i>ost Centre</a></li>
                            <li class="disable"><a href="#"><i>B</i>ar code / Label</a></li>

                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">settings_suggest</span> Configuration</b></li>
                            <li class="disable"><a href="#"><i>D</i>ashboard</a></li>
                            <li class="disable"><a href="#"><i>P</i>rinting Config. </a></li>
                            <li class="disable"><a href="#"><i>C</i>ommunication</a></li>
                            <li class="disable"><a href="#"><i>H</i>ardware</a></li>
                            <li class="disable"><a href="#"><i>D</i>igital Signature</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">tune</span> Utility</b></li>
                            <li class="disable"><a href="#"><i>D</i>ata Import / Export</a></li>
                            <li class="disable"><a href="#"><i>E</i>xception (Master) </a></li>
                            <li class="disable"><a href="#"><i>H</i>SN Update</a></li>


                            <li class="disable"><a href="#"><i>B</i>ackup</a></li>
                            <li class="disable"><a href="#"><i>B</i>ulk Updation</a></li>
                          </ul>
                        </div>
                      </div></div>
                    </li>
                    <li class="topnav topdropnav nav-item dropdown">
                      <a href="#!"  data-bs-toggle="dropdown" aria-expanded="false"><i>T</i>ransactions</a>
                      <div class="submenu dropdown-menu" id="transactionsdiv_menu"><div class="row m-0">
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">shopping_cart_checkout</span> Sales</b></li>


                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="18"><i>S</i>ale Invoice</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="19"><i>S</i>ales Order</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="2"><i>C</i>redit Note</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="7"><i>D</i>elivery Challan</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="17"><i>Q</i>uotations</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">local_mall</span> Purchases</a></b></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="11"><i>P</i>urchase Invoice</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="12"><i>P</i>urchase Order</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="3"><i>D</i>ebit Notes</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="6"><i>I</i>nward Challan</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="21"><i>P</i>urchase Requisition</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">account_balance</span> Banking</b></li>                         
                            <li>
                              <a href="javascript:void(0)" class="voucher_transaction" data-id="9"><i>P</i>ayments</a>
                            </li>
                            <li>
                              <a href="javascript:void(0)" class="voucher_transaction" data-id="13"><i>R</i>eceipts</a>
                            </li>
                            <li>
                              <a href="javascript:void(0)" class="voucher_transaction" data-id="1"><i>C</i>ontra</a>
                            </li>
                            <li>
                              <a href="javascript:void(0)" class="voucher_transaction" data-id="5"><i>J</i>ournal</a>
                            </li>
                            <li>
                              <a href="javascript:void(0)" class="voucher_transaction" data-id="8"><i>M</i>emorandum</a>
                            </li>

                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">autorenew</span> Items</b></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="15"><i>S</i>tock Transfer</a></li>
                            <li> <a href="javascript:void(0)" class="voucher_transaction" data-id="10"><i>P</i>hysical Verification</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="14"><i>P</i>roduction Voucher</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="20"><i>S</i>tock Journal</a></li>
                            <li><a href="javascript:void(0)" class="voucher_transaction" data-id="4"><i>C</i>onsignment Packing</a></li>
                          </ul>
                        </div>
                      </div></div>
                    </li>
                    <li class="topnav topdropnav nav-item dropdown">
                      <a href="#!" data-bs-toggle="dropdown" aria-expanded="false"><i>T</i>axes</a>
                      <div class="submenu dropdown-menu" id="taxesdiv_menu"><div class="row m-0">
                        <div class="col-md-3 col-6 disable">
                          <ul>
                            <li><b><span class="material-symbols-outlined">query_stats</span> GST</b></li>
                            <li><a href="#"><i>O</i>utward Supplies (R1/ CMP08)</a><a href="#"><i>G</i>STIN Portal (2A/2B)</a><a href="#"><i>I</i>nward Supplies (3B)</a><a href="#"><i>I</i>TC Returns</a><a href="#"><i>G</i>ST TDS/TCS Return</a><a href="#"><i>O</i>ther GST Returns</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6 disable">
                          <ul>
                            <li><b><span class="material-symbols-outlined">insights</span> TDS/TCS</a></b></li>
                            <li><a href="#"><i>F</i>orm 24Q (Salary)</a><a href="#"><i>F</i>orm 26Q (Other than salary )</a><a href="#"><i>F</i>orm 27Q (Non Resident)</a><a href="#"><i>F</i>orm 27EQ (TCS)</a><a href="#"><i>T</i>DS Certificates</a><a href="#"><i>T</i>CS Certificate</a><a href="#"><i>O</i>ther TDS/TCS Forms</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6 disable">
                          <ul>
                            <li><b><span class="material-symbols-outlined">pending_actions</span> Annual</a></b></li>
                            <li><a href="#"><i>G</i>STR-9</a></li>  <li><a href="#"><i>G</i>STR-4</a></li>
                            <li><a href="#"><i>I</i>ncome Tax</a></li>  <li><a href="#"><i>I</i>TC Tagging</a></li> <li><a href="#"><i>E</i>CL</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6 disable">
                          <ul>
                            <li><b><span class="material-symbols-outlined">universal_currency_alt</span> Others</b></li>
                            <li><a href="#"><i>E</i>PF - ECR</a></li> <li><a href="#"><i>E</i>SI Return</a></li>
                            <li><a href="#"><i>P</i>rof Tax</a></li> <li><a href="#"><i>S</i>FT/AIR</a></li> <li><a href="#"><i>E</i>xcise/VAT</a></li>
                          </ul>
                        </div>
                      </div></div>
                    </li>


                    <li class="topnav topdropnav nav-item dropdown">
                      <a href="#!" data-bs-toggle="dropdown" aria-expanded="false"><i>R</i>egister</a>
                      <div class="submenu dropdown-menu" id="registerdiv_menu"><div class="row m-0">
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">difference</span> Outward</b></li>
                            <li class="disable"><a href="#"><i>S</i>ales Register</a></li>
                            <li class="disable"><a href="#"><i>S</i>ales Return Register</a></li>
                            <li class="disable"><a href="#"><i>S</i>ales Order</a></li>
                            <li class="disable"><a href="#"><i>M</i>aterial Issue Register</a></li>
                            <li class="disable"><a href="#"><i>Q</i>uotation Register</a></li>
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">confirmation_number</span> Voucher</b></li>
                            <li class="disable"><a href="#"><i>P</i>ayment Register</a></li>
                            <li class="disable"><a href="#"><i>R</i>eceipt Register</a></li>
                            <li class="disable"><a href="#"><i>C</i></i>ontra Register</a></li>
                            <li class="disable"><a href="#"><i>J</i>ournal Register</a></li>
                            <li class="disable"><a href="#"><i>O</i>ther Acc. Register</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">book</span> Inventory</b></li>
                            <li class="disable"><a href="#"><i>S</i>tock Tranfer Reg</a></li>
                            <li class="disable"><a href="#"><i>P</i>hysical Verif. Reg</a></li>
                            <li class="disable"><a href="#"><i>P</i>roduction/Cons. Reg</a></li>
                            <li class="disable"><a href="#"><i>C</i>onsignment Packing Reg</a></li>
                            <li class="disable"><a href="#"><i>O</i>ther Stock Reg</a></li>
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">add_chart</span> Inwards</b></li>
                            <li class="disable"><a href="#">P</i>urchase Reg</a></li>
                            <li class="disable" ><a href="#"><i>P</i>urchase Return Reg</a></li>
                            <li class="disable"><a href="#"><i>P</i>urchase Order</a></li>
                            <li class="disable"><a href="#"><i>M</i>aterial Receipt Reg</a></li>
                            <li class="disable"><a href="#"><i>P</i>urchase Requisition</a></li>
                          </ul>
                        </div>

                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">calculate</span> Taxation</b></li>
                            <li class="disable"><a href="#"><i>G</i>ST Summary</a></li>
                            <li class="disable"><a href="#"><i>T</i>DS/TCS Summary</a></li>
                            <li class="disable"><a href="#"><i>P</i>artywise Summary</a></li>
                            <li class="disable"><a href="#"><i>S</i>upply In/Outward Reg</a></li>
                            <li class="disable"><a href="#"><i>I</i>TC Register</a></li>
                          </ul>
                        </div>

                      </div></div>
                    </li>
                    <li class="topnav topdropnav nav-item dropdown">
                      <a href="#!" data-bs-toggle="dropdown" aria-expanded="false"><i>R</i>eports</a>
                      <div class="submenu dropdown-menu" id="reportsdiv_menu"><div class="row m-0">
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">content_paste_search</span> Accounts</b></li>
                            <li><a href="<?php echo $base_url;?>reports/account_ledger" class="validlink"><i>A</i>ccount Ledger</a></li>
                            <li><a href="<?php echo $base_url;?>reports/account_summaries"><i>A</i>ccount Summary</a></li>
                            <li class="disable"><a href="#"><i>B</i>ills Management</a></li>
                            <li class="disable"><a href="#"><i>A</i>geing Report</a></li>
                            <li class="disable"><a href="#"><i>B</i>udget Report</a></li>
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul >
                            <li><b><span class="material-symbols-outlined">box_add</span> Stock</b></li>
                            <li><a href="<?php echo $base_url;?>reports/stock_ledger"><i>S</i>tock Ledger</a></li>
                            <li><a href="<?php echo $base_url;?>reports/stock_summary"><i>S</i>tock Summary</a></li>
                            <li><a href="<?php echo $base_url;?>reports/stock_status"><i>I</i>nventory Status</a></li>

                            <li class="disable"><a href="#"><i>J</i>ob Work Report</a></li>
                            <li class="disable"><a href="#"><i>O</i>ther Stock Reports</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li class="disable"><b><span class="material-symbols-outlined">book</span> Books</b></li>
                            <li class="disable"><a href="#"><i>D</i>ay Book</a></li>
                            <li class="disable"><a href="#"><i>C</i>ash/Bank Book</a></li>
                            <li class="disable"><a href="#"><i>C</i>ost Centre</a></li>
                            <li class="disable"><a href="#"><i>B</i>ank Reconciliation Statement (BRS)</a></li>
                            <li class="disable"><a href="#"><i>O</i>utstanding Reports</a></li>
                            <!--<li class="disable"><a href="#"><i>A</i>geing Report</a></li>-->
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul >
                            <li><b><span class="material-symbols-outlined">add_chart</span> Final Account</b></li>
                            <li><a href="<?php echo $base_url;?>reports/balance_sheet" class="validlink"><i>B</i>alance Sheet</a></li>
                            <li><a href="<?php echo $base_url;?>reports/profit_loss" class="validlink"><i>P</i>rofit & Loss Accounts</a></li>
                            <li><a href="<?php echo $base_url;?>reports/trial_balance" class="validlink"><i>T</i>rial Balance</a></li>
                            <li class="disable"><a href="#"><i>C</i>ash/Fund Flow</a></li>
                            <li class="disable"><a href="#"><i>D</i>epreciation Chart</a></li>
                            <li class="disable"><a href="#"><i>T</i>ax Summaries</a></li>
                          </ul>
                        </div>

                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">calculate</span> Utilities</b></li>
                            <li><a href="<?php echo $base_url;?>reports/exceptions_txn"><i>E</i>xception (Txn) </a></li>
                            <li class="disable"><a href="#"><i>I</i>nterest Calc. </a></li>
                            <li class="disable"><a href="#"><i>R</i>oyalty Calc. </a></li>
                            <li class="disable"><a href="#"><i>C</i>ommission Calc. </a></li>
                            <li class="disable"><a href="#"><i>S</i>cheme Reports</a></li>
                            <li class="disable"><a href="#"><i>C</i>onsignment Report</a></li>
                          </ul>
                        </div>

                      </div></div>
                    </li>
                    <li class="topnav topdropnav nav-item dropdown">
                      <a href="#!" data-bs-toggle="dropdown" aria-expanded="false"><i>M</i>IS</a>
                      <div class="submenu dropdown-menu" id="misdiv_menu"><div class="row m-0">
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">request_quote</span>Financial Statements</b></li>
                            <?php if($local_session->get('ses_company_id')!=''){ ?>
                              <li class="disable"><a href="#"><i>A</i>nnual Report</a></li>
                              <li class="disable"><a href="#"><i>P</i>rofitability Analysis</a></li>
                              <li class="disable"><a href="#"><i>R</i>atio Analysis</a></li>
                            <?php } else{ ?>
                            <?php } ?>
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">phonelink_setup</span> O/S Analysis</b></li>
                            <?php if($local_session->get('ses_company_id')!=''){ ?>
                              <li class="disable"><a href="#"><i>B</i>ills Receivable</a></li>
                              <li class="disable"><a href="#"><i>B</i>ills Payable</a></li>
                              <li class="disable"><a href="#"><i>A</i>geing Analysis</a></li>
                            <?php } else{ ?>
                            <?php } ?>
                          </ul>

                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">box_add</span>Stock</b></li>
                            <?php if($local_session->get('ses_company_id')!=''){ ?>
                              <li><a href="#"><i>I</i>nventory 360° View</a></li>
                              <li><a href="#"><i>S</i>tock in Hand</a></li>
                              <li><a href="#"><i>S</i>tock Exception</a></li>
                              <li><a href="#"><i>S</i>tock Valuation</a></li>
                            <?php } else{ ?>
                            <?php } ?>
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">add_chart</span><i>M</i>IS Report</b></li>
                            <li class="disable"><a href="#"><i>T</i>ax Exceptions</a></li>
                            <li class="disable"><a href="#"><i>C</i>ompliance Calendar</a></li>
                          </ul>
                        </div>

                      </div></div>
                    </li>
                    <li class="topnav topdropnav nav-item dropdown">
                      <a href="#!" data-bs-toggle="dropdown" aria-expanded="false"><i>A</i>udit</a>
                      <div class="submenu dropdown-menu" id="auditdiv_menu"><div class="row m-0">
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">domain</span> Company</b></li>
                            <li class="disable"><a href="#"><i>A</i>I Audit Report</a></li>
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">heap_snapshot_large</span> Income Tax</b></li>
                            <li class="disable"><a href="#"><i>I</i>T Audit</a></li>
                          </ul>
                        </div>
                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">content_paste_search</span> GST</b></li>
                            <li class="disable"><a href="#"><i>G</i>ST Audit</a></li>
                          </ul>
                        </div>


                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">summarize</span> TDS/TCS</b></li>
                            <li class="disable"><a href="#"><i>T</i>DS/TCS Audit</a></li>
                          </ul>
                        </div>

                        <div class="col-md-3 col-6">
                          <ul>
                            <li><b><span class="material-symbols-outlined">quick_reference</span> Others</b></li>
                            <li class="disable"><a href="#"><i>A</i>udit Trial</a></li>
                          </ul>
                        </div>

                      </div></div>
                    </li>

                  </ul>
                </div>

                <ul class="navbar-nav rightpanel navbar-nav-icons flex-row">
                 <li class="nav-item companynav me-md-3 me-lg-4">
                  <a class="nav-link button" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
                    <span class="usericon material-symbols-outlined d-md-none d-block float-start">account_circle</span>
                    <i class="d-md-block d-none float-start"><?= grp_comp()->alias ?></i><br>
                    <i class="d-md-block d-none float-start"><?= grp_comp()->fy_name ?></i> 
                    <span class="expand material-symbols-outlined d-md-block d-none">expand_more</span> 
                  </a>
                  <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navsetting">
                    <div class="card position-relative border-0">
                      <div class="card-body p-0">

                        <h6 class="mt-4 px-3 text-black">My Company Profile</h6>
                        
                        
                        
                        <div class="overflow-auto">
                          <ul class="nav d-flex flex-column mb-2 pb-1">
                            <li class="nav-item"><a class="nav-link px-3" href="<?php echo $admin_url;?>company/modify"> <span class="me-2 material-symbols-outlined">store</span>Company Master</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?php echo $base_url;?>mapping"> <span class="me-2 material-symbols-outlined">shield_lock</span>Data Mapping</a></li>



                          </li>

<li class="nav-item">
  <a class="nav-link px-3" data-bs-toggle="collapse" href="#collapseExampleFY" role="button" aria-expanded="false" aria-controls="collapseExampleFY"> 
    <span class="me-2 material-symbols-outlined">edit_calendar</span>Change Financial Year <small class="ps-2">▽</small>
  </a> 
</li>

<div class="collapse" id="collapseExampleFY">


  <?php foreach (grp_comp()->fy_list as $key => $value) { ?>
    <li class="nav-item" title="<?= $value->fy_str ?>">
      <a class="nav-link px-3 <?= ($value->grp_fy_id == grp_comp()->fy_id) ? 'text-success' : '' ?>" href="<?= base_url() ?>/grpcomp/company/selectfy/<?= $value->grp_fy_id ?>">
          <?= $value->fy_name ?>         
      </a>
    </li>
  <?php } ?>
  

  <li class="nav-item">
    <a class="nav-link px-3" href="<?= base_url() ?>/grpcomp/company/nextfy">Add New FY</a>
  </li>
</div>


                          <li class="nav-item">
                            <a  class="nav-link px-3" data-bs-toggle="collapse" href="#collapseCompanies" role="button" aria-expanded="true" aria-controls="collapseCompanies"> 
                              <span class="me-2 material-symbols-outlined">edit_calendar</span>Companies<small class="ps-2">▽</small>
                            </a> 
                          </li>

<div class="collapse p-1" id="collapseCompanies">
  <table id="grp_comp_list" class="table table-bordered" style="width: 100%;">
    <thead>
      <tr>
        <th>Code</th>
        <th>Name</th>
        <th>FY</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (grp_comp()->list as $key => $value) { ?>
        <tr title="<?= $value->fy_str ?>">
          <td><?= $value->comp_code ?></td>
          <td><?= $value->comp_name ?></td>
          <td><?= $value->fy_name ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>



                          <li class="nav-item"><a class="nav-link px-3" href="<?php echo base_url();?>/groupcompany/close_company"> <span class="me-2 material-symbols-outlined">room_preferences</span>Close Company</a></li>
                        </ul>

                      </div>
                    </div>
                  </div>
                </div>		

                <li>
                  <li class="nav-item dropdown usernav"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
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
                        <div class="card-footer p-0 border-top">

                          <hr />

                          <div class="px-3"> 
                            <a class="btn d-flex flex-center w-100" href="<?php echo $base_url;?>signout">Sign out</a></div>
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
              <div class="row mb-4 myalltabs px-3 bg bg-success"><div class="overflow-div">


               <div class="input-group commandinput p-0" style="width:220px; min-width:180px; align-items: center;">
                <input type="command-line" placeholder="Command Line" class="form-control">
                <div class="helpdropdown dropdown dropdown-menu-end bg-white" style="border:1px solid #e3e3e3; border-radius:0px 5px 5px 0px;">
                 <span class="material-symbols-outlined" role="button" data-bs-toggle="dropdown" aria-expanded="false">contact_support</span>
                 <div class="dropdown-menu navbar-dropdown-caret">Here you can type shortcode to open any window</div>
               </div>
             </div>

             <div class="col-12 mx-1 w-auto overflow-auto" id="tabslistings">

              <?php


              $lastmenu = end($user_tabs);


              $total_user_tabs = count($user_tabs);
              if($user_tabs){

                foreach($user_tabs as $tab_row){ 

                  $tab_id   = $tab_row['menuid'];
                  $tburl    = $tab_row['url'];
                  $tabname  = $tab_row['name'];

                  if($s_ctab==$tab_id){
                    $sel_active ='active';
                  }
                  elseif($total_user_tabs=='1')
                   $sel_active ='active';
                 else
                  $sel_active ='';
                ?>
                <span id="tabs<?php echo $tab_id;?>" class="btn tabslist <?php echo $sel_active;?>" data-id="<?php echo $tab_id;?>" href="javascript:void(0)" data-url="<?php echo $tburl;?>"  role="button"><?php echo $tabname;?>
                <?php if($total_user_tabs >1){ ?>
                  <a href="<?php echo $base_url;?>dashboard/remove_tab/<?php echo $tab_id;?>" data-id="<?php echo $tab_id;?>">✖</a><?php } ?></span>

                  <?php

                }
              } 
              ?>
            </div>
            <?php if(count($user_tabs)<5){ ?>
              <a  href="<?php echo $base_url;?>dashboard/usertabs/create"> <span class="btn" style="width:auto;"  role="button">+</span></a>
            <?php } ?>
          </div> </div>



          <div class="offcanvas offcanvas-start sidenavbar" tabindex="-1" id="sidenav" aria-labelledby="sidenavLabel">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title" id="sidenavLabel">Apps</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
             <div class="accordion" id="accordionApp">
              <div class="accordion-item">
                <button class="accordion-button link-dark" type="button" data-bs-toggle="collapse" data-bs-target="#appmenu1" aria-expanded="true" aria-controls="appmenu1">
                  Applications
                </button>
                <div id="appmenu1" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionApp">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="#">An item</a></li>
                    <li class="list-group-item"><a href="#">A second item</a></li>
                    <li class="list-group-item"><a href="#">A third item</a></li>
                    <li class="list-group-item"><a href="#">A fourth item</a></li>
                  </ul>
                </div>
              </div>
              <div class="accordion-item">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appmenu2" aria-expanded="false" aria-controls="appmenu2">
                 Order
               </button>
               <div id="appmenu2" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionApp">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item"><a href="#">An item</a></li>
                  <li class="list-group-item"><a href="#">A second item</a></li>
                  <li class="list-group-item"><a href="#">A third item</a></li>
                  <li class="list-group-item"><a href="#">A fourth item</a></li>
                </ul>
              </div>
            </div>
            <div class="accordion-item">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#appmenu3" aria-expanded="false" aria-controls="appmenu3">
               Account
             </button>
             <div id="appmenu3" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionApp">
              <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="#">An item</a></li>
                <li class="list-group-item"><a href="#">A second item</a></li>
                <li class="list-group-item"><a href="#">A third item</a></li>
                <li class="list-group-item"><a href="#">A fourth item</a></li>
              </ul>
            </div>
          </div>
        </div>   

      </div>
    </div>


    <div class="content"><div class="pb-5">