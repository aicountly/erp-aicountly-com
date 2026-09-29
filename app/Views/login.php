
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
        
        </style>
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
          -webkit-appearance: none;
          margin: 0;
        }
        
        /* Firefox */
        input[type=number] {
          -moz-appearance: textfield;
        }
    </style>
  </head>

  <body class="bg-white">
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <div class="container-fluid">
	  <div class="container"><div class="row">
        <div class="form-outline mb-4" style="position: absolute;top: 50px;right: 0;width: 150px;">
          <select class="form-control">
            <option value="IN">Region: IN</option>
          </select>
        </div>
        <div class="col-lg-4 mt-5 offset-lg-4 col-md-6 offset-md-3">
	  <div class="mainlogo text-center mb-5">
	      <a href="<?php echo base_url();?>" ><img src="<?php echo base_url();?>/public/assets/img/logo.png" alt="aicountly" width="150"></a>
	  </div>
      
        <?php if ($session->getFlashdata('message')) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $session->getFlashdata('message'); ?>
            </div>
        <?php } ?>
        
        <div id="message">
            
        </div>

 
<!-- Window div_email Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_email">
    <h4 class="text-center mb-3">LOGIN TO DASHBOARD </h4>
    <div class="col-4 offset-4 mb-3">
        <hr class="hr hr-blurry" />
    </div>
    <p class="login-tabs" role="tablist">
        <a type="button" href="javascript:void(0)" class=" text-primary login_tab" data-type="email">Email Address</a>
        <a type="button" href="javascript:void(0)" class="login_tab" data-type="phone">Phone</a>
    </p>

    <div class="border-0 accordion form-outline mb-4" id="myTabContent">

        <div class="border-0 fade show active accordion-item">
            <label class="form-label p-0" for="email">Email address/ Username</label>  
            <input type="email" id="email" name="email" class="form-control" autocomplete="off" autofocus/>
        </div>
        
    </div>

    <div class="row mb-4">
        <div class="col d-flex justify-content-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="form2Example31" checked />
                <label class="form-check-label" for="form2Example31"> Remember me </label>
            </div>
        </div>

        <div class="col">
          <a href="javascript:void(0)" id="forgetPassword">Forgot password?</a>
        </div>
    </div>
  
    <div class="text-center">
        <button type="button" class="btn btn-success m-auto btn-lg mb-1" id="signin_btn">SIGN IN</button>
        <br> or <br>
	    <a type="button" href="<?php echo $login_link;?>" class="btn btn-outline-primary m-auto btn-lg">
	        <img src="<?php echo base_url();?>/public/assets/img/google.webp" width="20">
	        &nbsp;&nbsp; LOGIN WITH GOOGLE
	    </a>
        <p class="pt-3 small">
            By selecting Sign In or Sign in with Google, You agree to our Terms and have read and acknowledge our Privacy Statement.
        </p>
        <h5 class="text-center mt-4"> Not a member? <a href="<?php echo $base_url; ?>/register">Register</a> </h5>
    </div>
</div>
 <!-- Window div_email Ends here --> 
 

<!-- Window div_email_password Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_email_password" style="display: none">
    <h4 class="text-center mb-3">Enter Your Password </h4>
    <div class="col-4 offset-4 mb-3"><hr class="hr hr-blurry" /></div>
    
    <p class="text-center">
        Choose how to Sign in to <br><span id="howToSign">abc@gmail.com</span>
    </p>
    <p class="text-center">
        <a href="javascript:void(0)" class="useDiffAccount">Use of a different account</a>
    </p>
    
    <!-- Email input -->   
    <label class="form-label p-0" for="email">Password</label>
    <input type="password" id="password" name="password" class="form-control" autocomplete="off" autofocus />
    
    <div class="text-center pt-3">
        <button type="button" class="btn btn-success m-auto btn-lg mb-1" id="submitPassword">COUNTINUE</button>
        <br> or <br>
        <button type="button" id="textACode" class="btn btn-outline-primary m-auto btn-lg"></button>
        
        <h5 class="text-center mt-4"> <a href="javascript:void(0)" class="signDiffWay">Sign in a different way</a> </h5>
    </div>


</div>
 <!-- Window div_email_password Ends here --> 
 
 
<!-- Window div_email_diff Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_email_diff" style="display: none">
    <h4 class="text-center mb-3">Let's make sure you're here </h4><div class="col-4 offset-4 mb-3"><hr class="hr hr-blurry" /></div>
    
    <p class="text-center">Choose how you want to verify your identity</p>
    <p class="text-center useDiffAccount"><a href="javascript:void(0)">Sign in with different account</a></p>
    
    
    <div class="col-12 verify">
        <a href="javascript:void(0)" class="open_pass_div">
            <p>
                <img src="<?php echo base_url();?>/public/assets/img/p-verify.png">
                <span>Enter Password</span>
            </p>
        </a>
        <a href="javascript:void(0)" id="verifyPhone">
            <p>
                <img src="<?php echo base_url();?>/public/assets/img/m-verify.png">
                <span>Text a code to ******99</span>
            </p>
        </a>
        <a href="javascript:void(0)" id="verifyEmail">
            <p>
                <img src="<?php echo base_url();?>/public/assets/img/e-verify.png">
                <span>Email Verify</span>
            </p>
        </a>
    </div>
    
    <div class="text-center">
        <button type="button" class="btn btn-success m-auto btn-lg mb-1 open_pass_div">COUNTINUE</button>
        <br> or <br>
        <button type="button" class="btn btn-outline-primary m-auto btn-lg">Contact Customer Care</button>
        <br> or <br>
        <a type="button" href="<?php echo $login_link;?>" class="btn btn-outline-primary m-auto btn-lg">
            <img src="<?php echo base_url();?>/public/assets/img/google.webp" width="20">&nbsp;&nbsp; 
            LOGIN WITH GOOGLE
        </a>
    </div>

</div>
<!-- Window div_email_diff Ends here --> 
 
 
<!-- Window div_email_phone_otp Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_email_phone_otp" style="display: none">
    <h4 class="text-center mb-3">Check Your Phone </h4>
    <div class="col-4 offset-4 mb-3">
        <hr class="hr hr-blurry" />
    </div>
    
    <p class="text-center">
        Enter the 6 digit code We just sent to<br>
        <span id="user_phone">********98</span>
    </p>
    <p class="text-center">
        <a href="javascript:void(0)" class="useDiffAccount">Use of a different mobile number</a>
    </p>
    <p class="text-center">
        <img src="<?php echo base_url();?>/public/assets/img/m-verify.png"> 
    </p>
  
    <label class="form-label p-0" for="phone_verification_code">Verification Code</label>
    <input type="password" id="phone_verification_code" name="phone_verification_code" class="form-control"  autocomplete="off" />
    
    <div class="text-center pt-3">
        <button type="button" class="btn btn-success m-auto btn-lg mb-1" id="submitPhoneOtp">COUNTINUE</button>
        <br>  <br>
        <button type="button" class="btn btn-outline-primary m-auto btn-lg" id="resendPhoneOtp" disabled>I didn't get a message</button> 
        <h5 class="text-center mt-4"> <a href="javascript:void(0)" class="signDiffWay">Sign in a different way</a> </h5>
    </div>
</div>
 <!-- Window div_email_phone_otp Ends here --> 
 
 
<!-- Window div_email_otp Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_email_otp" style="display: none">
    <h4 class="text-center mb-3">Check Your Email</h4>
    <div class="col-4 offset-4 mb-3">
        <hr class="hr hr-blurry" />
    </div>
    
    <p class="text-center">
        Enter the 6 digit code We just sent to <br>
        <span id="user_email">abc@gmail.com</span>
    </p>
    <p class="text-center">
        <a href="javascript:void(0)" class="useDiffAccount">Use of a different account</a>
    </p>
    <p class="text-center">
        <img src="<?php echo base_url();?>/public/assets/img/e-verify.png"> 
    </p>
    
    <label class="form-label p-0" for="email_verification_code">Verification Code</label>
    <input type="password" id="email_verification_code" name="email_verification_code" class="form-control"  autocomplete="off" />
    
    <div class="text-center pt-3">
        <button type="button" class="btn btn-success m-auto btn-lg mb-1" id="submitEmailOtp">COUNTINUE</button>
        <br>  <br>
        <button type="button" class="btn btn-outline-primary m-auto btn-lg" id="resendEmailOtp" disabled="disabled">I didn't get any email</button>
        <h5 class="text-center mt-4"> <a  href="javascript:void(0)" class="signDiffWay">Sign in a different way</a> </h5>
    </div>
</div>
 <!-- Window div_email_otp Ends here --> 
 
 
<!-- Window 6 Starts here -->
  <div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div6" style="display: none">
  <h4 class="text-center mb-3">Enter Your Email </h4><div class="col-4 offset-4 mb-3"><hr class="hr hr-blurry" /></div>
 
    <p class="text-center">Enter your email address to send password link</p>

  <!-- Email input -->   
  <label class="form-label p-0" for="email">Email</label>
    <input type="text" id="forget_password_email" class="form-control" autofocus  autocomplete="off" />

    <p id="error_text6" class="text-danger errors"></p>
  <div class="text-center pt-3">
    <button type="button" class="btn btn-success m-auto btn-lg mb-1" id="submitEmail">COUNTINUE</button>
  </div>
  <p class="text-center"><br><span class="useDiffAccount"><a href="javascript:void(0)">Login Page</a></span></p>

</form>
</div>
 <!-- Window 6 Ends here -->
 
 
 
<!-- Window div_phone Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_phone" style="display: none">
    <h4 class="text-center mb-3">LOGIN TO DASHBOARD </h4>
    <div class="col-4 offset-4 mb-3">
        <hr class="hr hr-blurry" />
    </div>
    <p class="login-tabs" role="tablist">
        <a type="button" href="javascript:void(0)" class="login_tab" data-type="email">Email Address</a>
        <a type="button" href="javascript:void(0)" class="text-primary login_tab" data-type="phone">Phone</a>
    </p>

    <div class="border-0 accordion form-outline mb-4">

        <div class="border-0  fade show accordion-item">
            <label class="form-label p-0" for="phone">Phone</label> 
            <input type="tel" id="phone" name="phone" class="form-control" maxlength="15" autocomplete="off" onkeydown="javascript: return ['Backspace','Delete','ArrowLeft','ArrowRight'].includes(event.code) ? true : !isNaN(Number(event.key)) && event.code!=='Space'"/>
        </div>
        
    </div>

  
    <div class="text-center">
        <button type="button" class="btn btn-success m-auto btn-lg mb-1" id="send_mobile_otp">Send OTP</button>
        
        <br> or <br>
	    <a type="button" href="<?php echo $login_link;?>" class="btn btn-outline-primary m-auto btn-lg">
	        <img src="<?php echo base_url();?>/public/assets/img/google.webp" width="20">
	        &nbsp;&nbsp; LOGIN WITH GOOGLE
	    </a>
        <p class="pt-3 small">
            By selecting Sign In or Sign in with Google, You agree to our Terms and have read and acknowledge our Privacy Statement.
        </p>
        <h5 class="text-center mt-4"> Not a member? <a href="<?php echo $base_url; ?>/register">Register</a> </h5>
    </div>
</div>
 <!-- Window div_phone Ends here -->  
 
<!-- Window div_phone_otp Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_phone_otp" style="display: none">
    <h4 class="text-center mb-3">Check Your Phone </h4>
    <div class="col-4 offset-4 mb-3">
        <hr class="hr hr-blurry" /></div>
 
    <p class="text-center">
        Enter the 6 digit code We just sent to<br>
        <span id="user_phone_text">********98</span>
    </p>
    <p class="text-center">
        <a href="javascript:void(0)" class="diff_phone">Use of a different mobile number</a>
    </p>
    <p class="text-center">
        <img src="<?php echo base_url();?>/public/assets/img/m-verify.png"> 
    </p>

   
    <label class="form-label p-0" for="phone_otp">Verification Code</label>
    <input type="password" id="phone_otp" name="phone_otp" class="form-control"  autocomplete="off" />


    <div class="text-center pt-3">
        <button type="button" class="btn btn-success m-auto btn-lg mb-1" id="submit_mobile_otp">COUNTINUE</button>
        <br>  <br>
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-outline-primary btn-lg" id="resend_mobile_otp" disabled="disabled">Resend OTP</button>
        </div>
    </div>


</div>
<!-- Window div_phone_otp Ends here --> 

<!-- Window div_phone_users Starts here -->
<div class="col-12 shadow-lg p-md-5 p-4 allDiv" id="div_phone_users" style="display: none">
    <h4 class="text-center mb-3">Select Account </h4>
    <div class="col-4 offset-4 mb-3">
        <hr class="hr hr-blurry" /></div>
    <p class="text-center">
        <a href="javascript:void(0)" class="diff_phone">Use of a different mobile number</a>
    </p>

    <div class="list-group" id="users_list">

    </div>


</div>
<!-- Window div_phone_users Ends here --> 
 
 
 
  
 
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
    
    var user_email = '';
    var user_phone = '';
        
    $(document).on('click','#signin_btn',function(){

        var email = $('input[name="email"]').val();
        
        if(email != '')
        {
            $.ajax({
                url: '/login/checkUser',
                type: 'post',
                data: {email: email},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#signin_btn').attr('disabled', 'disabled');
                    $('#message').html('');
                },
                success: function (response) {
                    // console.log(response);
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if(response.status == 200){
                        user_email = response.data.email;
                        user_phone = response.data.phone;
                        
                        $('.allDiv').css('display','none');
                        $('#div_email_password').css('display','block');
                        $('input[name="password"]').focus();

                        $('#textACode').text('Text a code to '+protect_email(user_email));
                        $('#howToSign').text(protect_email(user_email));

                    }
                    if(response.status == 400){
                        display_message('alert-danger', response.message);
                        $('input[name="email"]').focus();
                    }
                    
                },
                complete: function() {
                    $('#signin_btn').attr('disabled', false);
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
        else
        {
            var message = 'Please enter valid email address';
            display_message('alert-danger', message);
            $('input[name="email"]').focus();
        }       
    });
    
    $(document).on('click','#submitPassword',function(){
        
        var password = $('input[name="password"]').val().trim();
        if(password != '')
        {
            $.ajax({
                url: '/login/checkPassword', 
                type: 'post',
                data: {email: user_email, password: password},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#submitPassword').attr('disabled', 'disabled');
                    $('#message').html('');
                },
                success: function (response) {
                    // console.log(response);
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if(response.status == 200){
                       
                        window.location.href='/home/companies';
                    }
                    if(response.status == 400){
                        display_message('alert-danger', response.message);
                        $('input[name="password"]').focus();
                    }
                    
                },
                complete: function() {
                    $('#submitPassword').attr('disabled', false);
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
        else
        {
            var message = 'Please enter password';
            display_message('alert-danger', message);
            $('input[name="password"]').focus();
        }
    });
    
    $(document).on('click','.signDiffWay',function(){
        $('#message').html('');
        
        if(user_phone){
            $('#verifyPhone').css('display', 'block');
            $('#verifyPhone span').text('Text a code to '+protect_phone(user_phone));
        }
        else{
            $('#verifyPhone').css('display', 'none');
        }
        $('#verifyEmail span').text('Verify Email '+protect_email(user_email));
        
        $('.allDiv').css('display','none');
        $('#div_email_diff').css('display','block');
    });
    
    $(document).on('click','.useDiffAccount',function(){
        $('#message').html('');
        $('input[name="email"]').val('');
        $('input[name="phone"]').val('');
        $('input[name="password"]').val('');

        user_email = '';
        user_phone = '';
        
        $('.allDiv').css('display','none');
        $('#div_email').css('display','block');
    });
        
    $(document).on('click','.open_pass_div',function(){
        $('.allDiv').css('display','none');
        $('#div_email_password').css('display','block');
        $('input[name="password"]').val('').focus();
    });
   
    $(document).on('click','#verifyEmail,#textACode',function(){
        if(user_email != '')
        {
            $.ajax({
                url: '/login/sendEmailOtp', 
                type: 'post',
                data: {email: user_email},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#textACode').attr('disabled', 'disabled');
                    $('#verifyEmail').attr('disabled', 'disabled');
                    $('#message').text('');
                },
                success: function (response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if(response.status == 200){
                       
                        $('.allDiv').css('display','none');
                        $('#div_email_otp').css('display','block');
                        $('input[name="email_verification_code"]').val('').focus();
                        $('#user_email').text(protect_email(user_email));
                        enable_resend_email_otp_button();
                        
                    }
                    if(response.status == 400){
                        display_message('alert-danger', response.message);
                    }
                    
                },
                complete: function() {
                    $('#textACode').attr('disabled', false);
                    $('#verifyEmail').attr('disabled', false);
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
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_email').css('display', 'block');
            var message = 'Please enter valid email address';
            display_message('alert-danger', message);
            $('input[name="email"]').focus();
        }
    });
    
    $(document).on('click','#resendEmailOtp',function(){
        if(user_email != '')
        {
            $.ajax({
                url: '/login/reSendEmailOtp', 
                type: 'post',
                data: {email: user_email},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#resendEmailOtp').attr('disabled', 'disabled');
                    $('#message').text('');
                },
                success: function (response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if(response.status == 200){
                        display_message('alert-success', response.message);
                        $('input[name="email_verification_code"]').val('').focus();
                    }
                    if(response.status == 400){
                        display_message('alert-danger', response.message);
                    }
                },
                complete: function() {
                    enable_resend_email_otp_button();
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
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_email').css('display', 'block');
            var message = 'Please enter valid email address';
            display_message('alert-danger', message);
            $('input[name="email"]').focus();
        }
    });
    
    var timeobj2;
    function enable_resend_email_otp_button()
    {
        var time = 10;
        $('#resendEmailOtp').text('Resend Email in '+time + 's');
        time--;
        clearInterval(timeobj2);
        timeobj2 = setInterval(function(){

            $('#resendEmailOtp').text('Resend Email in '+time + 's');
            if(time <= 0){
                clearInterval(timeobj2);
                $('#resendEmailOtp').text('Resend Email');
                $('#resendEmailOtp').attr('disabled', false);
              }
            time--;
        }, 1000);
    }
    
    $(document).on('click','#verifyPhone',function(){
        if(user_email != '')
        {
            if(user_phone != '')
            {
                $.ajax({
                    url: '/login/sendPhoneOtp', 
                    type: 'post',
                    data: {email: user_email, phone: user_phone},
                    datatype: "json",
                    cache: false,
                    beforeSend: function() {
                        $('#verifyPhone').attr('disabled', 'disabled');
                        $('#message').text('');
                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if(response.status == 200){
                           
                            $('.allDiv').css('display','none');
                            $('#div_email_phone_otp').css('display','block');
                            $('input[name="phone_verification_code"]').val('').focus();
                            $('#user_phone').text(protect_phone(user_phone));
                            enable_resend_phone_otp_button();
                            
                        }
                        if(response.status == 400){
                            display_message('alert-danger', response.message);
                        }
                        
                    },
                    complete: function() {
                        $('#verifyPhone').attr('disabled', false);
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
            else
            {
                var message = 'Failed! There is problem with mobile no.';
                display_message('alert-danger', message);
            }
        }
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_email').css('display', 'block');
            var message = 'Please enter valid email address';
            display_message('alert-danger', message);
            $('input[name="email"]').focus();
        }
    });
    
    $(document).on('click','#resendPhoneOtp',function(){
        if(user_email != '')
        {
            if(user_phone != '')
            {
                $.ajax({
                    url: '/login/reSendPhoneOtp', 
                    type: 'post',
                    data: {email: user_email, phone: user_phone},
                    datatype: "json",
                    cache: false,
                    beforeSend: function() {
                        $('#resendPhoneOtp').attr('disabled', 'disabled');
                        $('#message').text('');
                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if(response.status == 200){
                           
                            display_message('alert-success', response.message);
                            $('input[name="phone_verification_code"]').val('').focus();
                            
                        }
                        if(response.status == 400){
                            display_message('alert-danger', response.message);
                        }
                        
                    },
                    complete: function() {
                        enable_resend_phone_otp_button();
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
            else
            {
                var message = 'Failed! There is problem with mobile no.';
                display_message('alert-danger', message);
            }
        }
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_email').css('display', 'block');
            var message = 'Please enter valid email address';
            display_message('alert-danger', message);
            $('input[name="email"]').focus();
        }
    });
    
    var timeobj3;
    function enable_resend_phone_otp_button()
    {
        var time = 10;
        $('#resendPhoneOtp').text('Resend Message in '+time + 's');
        time--;
        clearInterval(timeobj3);
        timeobj3 = setInterval(function(){

            $('#resendPhoneOtp').text('Resend Message in '+time + 's');
            if(time <= 0){
                clearInterval(timeobj3);
                $('#resendPhoneOtp').text('Resend Message');
                $('#resendPhoneOtp').attr('disabled', false);
              }
            time--;
        }, 1000);
    }
    
    $(document).on('click','#submitEmailOtp',function(){
        
        if(user_email != '')
        {
            var otp = $('input[name="email_verification_code"]').val().trim();
            if(otp != '')
            {
                $.ajax({
                    url: '/login/matchEmailOtp', 
                    type: 'post',
                    data: {email: user_email, otp: otp},
                    datatype: "json",
                    cache: false,
                    beforeSend: function() {
                        $('#submitEmailOtp').attr('disabled', 'disabled');
                        $('#message').html('');
                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if(response.status == 200){
                            window.location.href='/home/companies';
                        }
                        if(response.status == 400){
                            
                        }
                        
                    },
                    complete: function() {
                        $('#submitEmailOtp').attr('disabled', false);
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
            else
            {
                var message = 'Please enter verification code';
                display_message('alert-danger', message);
                $('input[name="email_verification_code"]').focus();
            }
        }
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_email').css('display', 'block');
            var message = 'Please enter valid email address';
            display_message('alert-danger', message);
            $('input[name="email"]').focus();
        }
    });
    
    $(document).on('click','#submitPhoneOtp',function(){
        
        if(user_email != '')
        {
            if(user_phone != '')
            {
                var otp = $('input[name="phone_verification_code"]').val().trim();
                if(otp != '')
                {
                    $.ajax({
                        url: '/login/matchPhoneOtp', 
                        type: 'post',
                        data: {email: user_email, phone: user_phone, otp: otp},
                        datatype: "json",
                        cache: false,
                        beforeSend: function() {
                            $('#submitPhoneOtp').attr('disabled', 'disabled');
                            $('#message').html('');
                        },
                        success: function (response) {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            
                            if(response.status == 200){
                                window.location.href='/home/companies';
                            }
                            if(response.status == 400){
                                
                            }
                            
                        },
                        complete: function() {
                            $('#submitPhoneOtp').attr('disabled', false);
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
                else
                {
                    var message = 'Please enter verification code';
                    display_message('alert-danger', message);
                    $('input[name="phone_verification_code"]').focus();
                }
            }
            else
            {
                var message = 'Failed! There is problem with mobile no.';
                display_message('alert-danger', message);
            }
        }
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_email').css('display', 'block');
            var message = 'Please enter valid email address';
            display_message('alert-danger', message);
            $('input[name="email"]').focus();
        }
    });
    
    
        
        
        
    
        
    
        
        
        
        
        
        

         $(document).on('click','#forgetPassword',function(){
                $('.errors').html('');
                $('#email').val('');
                $('#phone').val('');
                $('#password').val('');
        
                $('#phone_verification_code').val();
                $('#email_verification_code').val();
                
                $('.allDiv').css('display','none');
                $('#div6').css('display','block');
                
                
        });
        
        $(document).on('click','#submitEmail',function(){
            
            var email = $('#forget_password_email').val().trim();
            if(email == ''){
                alert('Email is required');
                return;
            }
            if(email != '')
            {
                $.ajax({
                        url: '/login/forgetPassword', 
                        type: 'post',
                        data: {email: email},
                        datatype: "json",
                        cache: false,
                        beforeSend: function() {
                            $('#submitEmail').attr('disabled', 'disabled');
                            $('#error_text6').text('');
                        },
                        success: function (response) {
                            // console.log(response);
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            
                            if(response.status == 200){
                               
                                window.location.reload();
                            }
                            if(response.status == 400){
                                $('#error_text6').text(response.message);
                            }
                            
                        },
                        complete: function() {
                            $('#submitEmail').attr('disabled', false);
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
        
    $('input[name="password"]').keyup(function(event) {
        if (event.which === 13)
        {
            event.preventDefault();
            $('#submitPassword').click();
        }
    });
    
    $('input[name="email"]').keyup(function(event) {
        if (event.which === 13)
        {
            event.preventDefault();
            $('#signin_btn').click();
        }
    });
   
    
    
    $('input[name="phone"]').keyup(function(event) {
        if (event.which === 13)
        {
            event.preventDefault();
            $('#send_mobile_otp').trigger('click');
        }
    });
    
    $('input[name="phone_otp"]').keyup(function(event) {
        if (event.which === 13)
        {
            event.preventDefault();
            $('#submit_mobile_otp').trigger('click');
        }
    });
    
    $(document).on('click', '.login_tab', function(){
        
        var type = $(this).data('type');
        $('.allDiv').css('display', 'none');
        if(type == 'email'){
            $('#div_email').css('display', 'block');
            $('input[name="email"]').val('').focus();
        }
        if(type == 'phone'){
            $('#div_phone').css('display', 'block');
            $('input[name="phone"]').val('').focus();
        }
    });
    
    $(document).on('click','#send_mobile_otp',function(){
        var phone = $('input[name="phone"]').val().trim();
        
        if(phone != '')
        {
            $.ajax({
                url: '/login/sendMobileOtp', 
                type: 'post',
                data: {phone: phone},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#send_mobile_otp').attr('disabled', 'disabled');
                    $('#message').html('');
                },
                success: function (response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if(response.status == 200){
                        $('.allDiv').css('display', 'none');
                        $('#div_phone_otp').css('display', 'block');
                        $('#user_phone_text').text(protect_phone(phone));
                        $('input[name="phone_otp"]').focus();
                        enable_resend_otp_button();
                    }
                    if(response.status == 400){
                        var message = 'Please enter valid phone number';
                        display_message('alert-danger', response.message);
                        $('input[name="phone"]').focus();
                    }
                    
                },
                complete: function() {
                    $('#send_mobile_otp').attr('disabled', false);
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
        else
        {
            var message = 'Please enter valid phone number';
            display_message('alert-danger', message);
            $('input[name="phone"]').focus();
        }
    });
    
    $(document).on('click','.diff_phone',function(){
        
        $('.allDiv').css('display', 'none');
        $('#div_phone').css('display', 'block');
        $('input[name="phone"]').val('').focus();
        $('input[name="phone_otp"]').val('').focus();
        
    });
    
    $(document).on('click','#resend_mobile_otp',function(){
        var phone = $('input[name="phone"]').val().trim();
        
        if(phone != '')
        {
            $.ajax({
                url: '/login/reSendMobileOtp', 
                type: 'post',
                data: {phone: phone},
                datatype: "json",
                cache: false,
                beforeSend: function() {
                    $('#resend_mobile_otp').attr('disabled', 'disabled');
                    $('#message').html('');
                },
                success: function (response) {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if(response.status == 200){
                        display_message('alert-success', response.message);
                        $('input[name="phone_otp"]').focus();
                    }
                    if(response.status == 400){
                        display_message('alert-danger', response.message);
                        $('input[name="phone_otp"]').focus();
                    }
                    
                },
                complete: function() {
                    enable_resend_otp_button();
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
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_phone').css('display', 'block');
            var message = 'Please enter valid phone number';
            display_message('alert-danger', message);
            $('input[name="phone"]').focus();
        }
    });
    
    function get_capitals(first_name, second_name)
    {
        var first = first_name != '' ? first_name.charAt(0) : '';
        var second = second_name != '' ? second_name.charAt(0) : '';
        return (first+second).toUpperCase();
    }
    
    $(document).on('click','#submit_mobile_otp', function(){
        var phone = $('input[name="phone"]').val().trim();
        
        if(phone != '')
        {
            var phone_otp = $('input[name="phone_otp"]').val().trim();
            if(phone_otp != '')
            {
                $.ajax({
                    url: '/login/matchMobileOtp', 
                    type: 'post',
                    data: {phone: phone, phone_otp:phone_otp},
                    datatype: "json",
                    cache: false,
                    beforeSend: function() {
                        $('#submit_mobile_otp').attr('disabled', 'disabled');
                        $('#message').html('');
                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if(response.status == 200){
                            
                            if(response.type == 1)
                            {
                                window.location.reload();
                            }
                            if(response.type == 2)
                            {
                                var html = ``;
                                $.each(response.data, function(index, obj){
                                    html += `
                                        <a data-id="${obj.id}" href="javascript:void(0)" class="list-group-item list-group-item-action active_users">
                                            <div class="d-flex align-items-center justify-content-between position-relative">
                                                <div class="avatar avatar-l me-3">
                                                    <div class="avatar-name rounded-circle">
                                                        <span>${get_capitals(obj.user_firstname, obj.user_lastname)}</span>
                                                    </div>
                                                </div>
                                                <div class="flex-1 me-sm-3">
                                                  <h4 class="fs--1 text-black">${obj.name}</h4>
                                                  <p class="mb-0">${obj.email}</p>
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                });
                                $('#users_list').html(html);
                                
                                $('.allDiv').css('display', 'none');
                                $('#div_phone_users').css('display', 'block');
                            }
                            
                        }
                        if(response.status == 400){
                            display_message('alert-danger', response.message);
                            $('input[name="phone_otp"]').focus();
                        }
                        
                    },
                    complete: function() {
                        $('#submit_mobile_otp').attr('disabled', false);
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
            else
            {
                var message = 'Please enter valid otp';
                display_message('alert-danger', message);
                $('input[name="phone_otp"]').focus();
            }
        }
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_phone').css('display', 'block');
            var message = 'Please enter valid phone number';
            display_message('alert-danger', message);
            $('input[name="phone"]').focus();
        }
    })
    
    $(document).on('click','.active_users', function(){
        var phone = $('input[name="phone"]').val().trim();
        
        if(phone != '')
        {
            var phone_otp = $('input[name="phone_otp"]').val().trim();
            if(phone_otp != '')
            {
                var id = $(this).data('id');
                $.ajax({
                    url: '/login/loginUser', 
                    type: 'post',
                    data: {phone: phone, phone_otp:phone_otp, id:id},
                    datatype: "json",
                    cache: false,
                    beforeSend: function() {
                        $('.active_users').css('pointer-events','none');
                        $('#message').html('');
                    },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if(response.status == 200){
                            window.location.reload();
                        }
                        if(response.status == 400){
                            display_message('alert-danger', response.message);
                        }
                        
                    },
                    complete: function() {
                        $('.active_users').css('pointer-events','');
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
            else
            {
                $('.allDiv').css('display', 'none');
                $('#div_phone_otp').css('display', 'block');
                var message = 'Please enter valid otp';
                display_message('alert-danger', message);
                $('input[name="phone_otp"]').focus();
            }
        }
        else
        {
            $('.allDiv').css('display', 'none');
            $('#div_phone').css('display', 'block');
            var message = 'Please enter valid phone number';
            display_message('alert-danger', message);
            $('input[name="phone"]').focus();
        }
    })
    
    var timeobj;
    function enable_resend_otp_button()
    {
        var time = 10;
        $('#resend_mobile_otp').text('Resend OTP in '+time + 's');
        time--;
        clearInterval(timeobj);
        timeobj = setInterval(function(){

            $('#resend_mobile_otp').text('Resend OTP in '+time + 's');
            if(time <= 0){
                clearInterval(timeobj);
                $('#resend_mobile_otp').text('Resend OTP');
                $('#resend_mobile_otp').attr('disabled', false);
              }
            time--;
        }, 1000);
    }
    
    function display_message(alert_class, message)
    {
        var html = `
            <div class="alert ${alert_class} alert-dismissible">
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              ${message}
            </div>
        `;
        $('#message').html(html);
    }
    
    protect_email = function (email) {
        var avg, splitted, part1, part2;
        splitted = email.split("@");
        part1 = splitted[0];
        avg = part1.length / 2;
        part1 = part1.substring(0, (part1.length - avg));
        part2 = splitted[1];
        return part1 + "...@" + part2;
    };
    
    protect_phone = function (phone) {
        let endpart = phone.substr(phone.length - 2);
        result = endpart.padStart(phone.length - 2,"*");
        return result;
    }

    </script>
  </body>

</html>