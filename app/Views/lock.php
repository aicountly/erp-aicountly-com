<?php $header = array('title' => 'LOCKED!!');  ?>
<?php echo view('includes/header',$header); ?>
<body class="error-page">
<div id="global-loader">
<div class="whirly-loader"> </div>
</div>

   <div class="main-wrapper">
  <div class="error-box">
<h1>LOCKED!!</h1>
<h3 class="h2 mb-3"><i class="fas fa-exclamation-circle"></i> Oops! Account Locked!</h3>
<p class="h4 font-weight-normal">A link has been sent at your email address to generate a new password. Please check your email account.</p>
<a href="javascript:void(0);" onclick="history.back()" class="btn btn-primary">Back</a>
</div>
   
   </div>
<?php echo view('includes/footer_scripts'); ?>