
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
    <style>
    .login-tabs{text-align:center;}
    .login-tabs a{color:var(--ui-gray-700); text-align:center; padding:0px 6px;}
    .login-tabs a.active{color:#165591;}
    .footer-links{color:#666; font-size:13px;}
        .footer-links a{color:var(--ui-gray-700); padding:2px 4px;}
         .verify p{display:block; width:100%;}
        .verify img{width:50px; margin-right:8px;}
        .verify a{color:#000;}
        
        #successToast {
  background-color: #1e7e34 !important; /* dark professional green */
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  font-weight: 500;
  max-width: 420px;
  font-size: 0.95rem;
  z-index: 1060; /* above navbar */
}

        
        </style>
  </head>

  <body class="bg-white">
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container-fluid">
	  <div class="container"><div class="row"><div class="col-md-8 mt-5 offset-md-2">
	  <div class="mainlogo text-center mb-5"><a href="<?php echo base_url();?>" ><img src="<?php echo base_url();?>/public/assets/img/logo.png" alt="aicountly" width="150"></a></div>
        
        
        
        <form action="<?php echo $base_url; ?>/register" method="post" class="needs-validation" novalidate autocomplete="off">
        <?php echo $message_output->run() ;?>
        
       <?php if ($session->getFlashdata('message')) { ?>
    <div id="successToast" class="toast align-items-center text-white bg-success border-0 position-fixed top-0 start-50 translate-middle-x mt-3 z-3"
         role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <?= $session->getFlashdata('message'); ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
<?php } ?>


<!-- Window 1 Starts here -->
  <div class="col-12 shadow-lg p-md-5 p-4 allDiv">
  <h4 class="text-center mb-3">REGISTER WITH US</h4><div class="col-4 offset-4 mb-3"><hr class="hr hr-blurry" /></div>
 
 <div class="row">
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="fname">First Name</label>  
             <input type="text" id="fname" name="user_firstname" class="form-control" value="<?php echo set_value('user_firstname') ?>" autofocus required />
             <div class="invalid-feedback">Please fill out this field.</div>
         </div>
    </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="lname">Last Name</label>  
             <input type="text" id="lname" name="user_lastname" class="form-control"  value="<?php echo set_value('user_lastname') ?>" required/>
             <div class="invalid-feedback">Please fill out this field.</div>
        </div>
    </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="mobile">Regd Mobile</label>  
             <input type="text" id="mobile" name="user_regdmobile" class="form-control" value="<?php echo set_value('user_regdmobile') ?>" required/>
             <div class="invalid-feedback">Please fill out this field.</div>
         </div>
    </div>
     <div class="col-md-6">
         <div class="form-group">
            <label class="form-label p-0" for="email">Regd Email</label>  
            <input type="email" id="email" name="user_regdemail" class="form-control" value="<?php echo set_value('user_regdemail') ?>" required/>
            <div class="invalid-feedback">Please fill out this field.</div>
         </div>
    </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="wamobile">Regd Whatsapp Mobile</label>  
             <input type="text" id="wamobile" name="user_wamobile" class="form-control" value="<?php echo set_value('user_wamobile') ?>" required/>
             <div class="invalid-feedback">Please fill out this field.</div>
        </div>
    </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="profile">User Profile</label>
             <select class="form-select" name="user_type_profs" data-default_value="<?php echo set_value('user_type_profs') ?>">
                <?php if(!empty($qualification_array)) { ?>
                <?php foreach($qualification_array as $key => $value) { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
                <?php } ?>
             </select>
             <div class="invalid-feedback">Please fill out this field.</div>
        </div>
     </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="add1">Add Line 1</label>  
             <input type="text" id="add1" name="user_add1" class="form-control" value="<?php echo set_value('user_add1') ?>"/>
             <div class="invalid-feedback">Please fill out this field.</div>
         </div>
     </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="add2">Add Line 2</label>  
             <input type="text" id="add2" name="user_add2" class="form-control" value="<?php echo set_value('user_add2') ?>"/>
             <div class="invalid-feedback">Please fill out this field.</div>
         </div>
     </div>
      <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="add1">State</label>  
             <select class="form-select" name="user_state" data-default_value="<?php echo set_value('user_state') ?>">
                <option value=""></option>
                <?php if(!empty($state_array)) { ?>
                <?php foreach($state_array as $key1 => $value1) { ?>
                    <option value="<?php echo $value1['state_id']; ?>"><?php echo $value1['state_name']; ?></option>
                <?php } ?>
                <?php } ?>
             </select>
             <div class="invalid-feedback">Please fill out this field.</div>
         </div>
     </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="add2">Pin Code</label>  
             <input type="text" id="pin" name="user_pin" class="form-control" value="<?php echo set_value('user_pin') ?>"/>
             <div class="invalid-feedback">Please fill out this field.</div>
         </div>
     </div>
     <div class="col-md-6">
         <div class="form-group">
             <label class="form-label p-0" for="city">City</label>  
             <input type="text" id="city" name="user_city" class="form-control" value="<?php echo set_value('user_city') ?>"/>
             <div class="invalid-feedback">Please fill out this field.</div>
        </div>
     </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label p-0" for="pin">Country</label>
                <select class="form-select" name="user_country" data-default_value="<?php echo set_value('user_country') ?>">
                <option value=""></option>
                <?php if(!empty($country_array)) { ?>
                <?php foreach($country_array as $key => $value) { ?>
                    <option value="<?php echo $value['countryid']; ?>"><?php echo $value['countryname']; ?></option>
                <?php } ?>
                <?php } ?>
             </select>
             <div class="invalid-feedback">Please fill out this field.</div>
            </div> 
        </div>
        <div class="col-md-6">
            <div class="form-group">
                 <label class="form-label p-0" for="city">Password</label>  
                 <input type="password" id="password" name="password" class="form-control" value="" required/>
                 <div class="invalid-feedback">Please fill out this field.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label p-0" for="confirm_password">Confirm Password</label>  
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" value="" required/>
                <div class="invalid-feedback">Please fill out this field.</div>
            </div>
        </div>
  
  <div class="text-center col-12">
    <button type="submit" class="btn btn-success btn-lg m-2" id="signin_btn">REGISTER</button>
    <a href="<?= base_url() ?>" class="btn btn-secondary btn-lg m-2" id="signin_btn">LOGIN</a>
  </div>
   </div>
</div>
</form>
</div>
 <!-- Window 1 Ends here --> 
 
 
 
 
 
 
 
 
 
 
 
 
  
 
 <div class="col-12 pt-4 pb-2 text-center">
     <p class="footer-links"><a target="_blank" href="https://aicountly.com/index.php">Home</a> | <a target="_blank" href="https://aicountly.com/pricing_policy.php">Pricing Policy</a> | <a target="_blank" href="https://aicountly.com/ipr_policy.php">IPR Policy</a> | <a target="_blank" href="https://aicountly.com/refund_policy.php">Refund Policy</a> | <a target="_blank" href="https://aicountly.com/security_policy.php">Security Policy</a> | <a target="_blank" href="https://aicountly.com/delivery_policy.php">Delivery Policy</a></p>
     
   <p class="pt-2 footer-links">All rights reserved to Aicountly, Terms & Conditions, Features, Support, Pricing and service options subject to change without notice.</p>  
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

    <script>
     $(document).ready(function() {
            function disableBack() {
                window.history.forward()
            }
            window.onload = disableBack();
            window.onpageshow = function(e) {
                if (e.persisted)
                    disableBack();
            }
            
    });  
    
   
    </script>
    <script>
        // Disable form submissions if there are invalid fields
        (function() {
          'use strict';
          window.addEventListener('load', function() {
            // Get the forms we want to add validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
              form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                  event.preventDefault();
                  event.stopPropagation();
                }
                form.classList.add('was-validated');
              }, false);
            });
          }, false);
        })();
    </script>
    
    <script>
        $(function() {
            
            var default_qualification = $('[name="user_type_profs"]').data('default_value');
            if(default_qualification){
                $('[name="user_type_profs"]').val(default_qualification);
            }
            
            var default_country = $('[name="user_country"]').data('default_value');
            if(default_country){
                $('[name="user_country"]').val(default_country);
            }
            
            var default_state = $('[name="user_state"]').data('default_value');
            if(default_state){
                $('[name="user_state"]').val(default_state);
            }
        });
    </script>
    
    
    <script>
  document.addEventListener('DOMContentLoaded', function () {
    const toastEl = document.getElementById('successToast');
    if (toastEl) {
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    }
  });
</script>

  </body>

</html>