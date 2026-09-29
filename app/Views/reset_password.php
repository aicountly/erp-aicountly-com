<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>aicountly</title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url();?>/public/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url();?>/public/assets/img/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url();?>/public/assets/img/favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>/public/assets/img/favicon.ico">
    <script src="<?php echo base_url();?>/public/assets/js/config.js"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="<?php echo base_url();?>/public/assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
  </head>

  <body class="bg-white">
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container-fluid">
	  <div class="container"><div class="row"><div class="col-lg-4 mt-5 offset-lg-4 col-md-6 offset-md-3">
	  <div class="mainlogo text-center mb-5"><img src="<?php echo base_url();?>/public/assets/img/logo.png" alt="aicountly" width="150"></div>
	  <div class="row mb-4" style="display:none;">
	  <div class="col-6 text-center"><img src="<?php echo base_url();?>/public/assets/img/logo1.jpg" alt="aicountly" width="140"></div>
	   <div class="col-6 text-center"><img src="<?php echo base_url();?>/public/assets/img/logo2.jpg" alt="aicountly" width="140"></div>
	  </div>
       <?php $attributes = array('id' => 'reset_password_form', 'autocomplete'=>'off','class' =>'');
			 echo form_open(base_url().'/login/resetPassword/'.$enc_id, $attributes); ?> 
			 <?php if ($session->getFlashdata('message')) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $session->getFlashdata('message'); ?>
            </div>
        <?php } ?>
        <?php if ($session->getFlashdata('error_message')) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $session->getFlashdata('error_message'); ?>
            </div>
        <?php } ?>
		 <?php echo $message_output->run() ;?>
  <!-- Email input -->
  <div class="col-12 shadow-lg p-4">
  <h4 class="text-center mb-3">Reset Password </h4><div class="col-4 offset-4 mb-3"><hr class="hr hr-blurry" /></div>
  <div class="form-outline mb-4">
  <label class="form-label p-0" for="email">Email</label>
    <input type="email" class="form-control" value="<?php echo $email ?>" readonly required/>
     </div>

  <!-- Password input -->
  <div class="form-outline mb-4">
    <label class="form-label p-0" for="password">Password</label>
    <input type="password" name="password" id="login-password" class="form-control" required/>
  </div>
  
  <div class="form-outline mb-4">
    <label class="form-label p-0" for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required/>
  </div>

  
  
  <!-- onclick = "window.location.href='<?php //echo base_url();?>/home/open_company';"-->
  <div class="text-center">
    <button type="submit" class="btn btn-success m-auto btn-lg mb-1" >SUBMIT</button>
  </div>

  <!-- Submit button -->
  <!-- Register buttons -->

</form>
</div>
  
      </div></div></div></div>
    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="<?php echo base_url();?>/public/assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url();?>/public/assets/js/popper.min.js"></script>
    <script src="<?php echo base_url();?>/public/assets/js/bootstrap.min.js"></script>
  </body>
</html>