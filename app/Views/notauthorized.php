<?php $header = array('title' => 'Not Authorized');  ?>
<?php echo view('includes/header',$header); ?>
  <!-- END: Head-->
  <body class="horizontal-layout horizontal-menu blank-page navbar-floating footer-static" data-open="hover" data-menu="horizontal-menu" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
      <div class="content-overlay"></div>
      <div class="header-navbar-shadow"></div>
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
       <div class="content-body">
                <!-- Not authorized-->
                <div class="misc-wrapper">
                    <div class="misc-inner p-2 p-sm-3">
                        <div class="w-100 text-center">
                            <h2 class="mb-1">You are not authorized! 🔐</h2>
                            <p class="mb-2">
                              
                            </p><a class="btn btn-primary mb-1 btn-sm-block waves-effect waves-float waves-light" href="<?php echo $back_to_dashboard;?>">Back to dashboard</a><img class="img-fluid" src="<?php echo base_url();?>/public/assets/img/not-authorized.svg" alt="Not authorized page">
                        </div>
                    </div>
                </div>
                <!-- / Not authorized-->
            </div>
      </div>
    </div>
    <!-- END: Content-->
    <!-- END: Content-->
<?php echo view('includes/footer_scripts'); ?>
<?php echo view('includes/footer'); ?>