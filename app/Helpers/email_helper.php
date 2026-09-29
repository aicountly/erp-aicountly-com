<?php

function sendForgetPasswordEmail($enc_id, $email)
{
    
    $message = " 
                <html> 
                <head> 
                    <title>AICOUNTLY</title> 
                </head> 
                <body> 
                    <h1>AICOUNTLY</h1>
                     <p>Click <a href='".base_url()."/login/resetPassword/".$enc_id."'> here </a> to reset your password</p>
                </body> 
                </html>";
                
    $sub = "Account verification from AICOUNTLY";
    $headers = "From: " . "erp.aicountly.in\r\n";
    $headers .= "Reply-To: erp.aicountly.in\r\n";
    $headers .= "Return-Path: erp.aicountly.in\r\n";
      $headers .= "Organization: AICOUNTLY\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8\r\n";
      $headers .= "X-Priority: 3\r\n";
      $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
      $headers .= "BCC: ". "[]" ;
        $headers.= "CC: ". "[]";
    
    try{
        $retval = mail($email,$sub,$message,$headers);
        if($retval)
        {
            return 'Account created successfully. Check email for confirmation';
        }
    }
    
    catch(Exception $e)
    {
        return 'Account created successfully but problem with email '.$e->getMessage();
    }
}
function sendAccountConfirmationEmail($enc_id, $email)
{
    
    $message = " 
                <html> 
                <head> 
                    <title>AICOUNTLY</title> 
                </head> 
                <body> 
                    <h1>Your account is created successfully.</h1> 
                     <p>Click <a href='".base_url()."/register/confirm/".$enc_id."'> here </a> to confirm and complete the the registration</p>
                </body> 
                </html>";
                
    $sub = "Account verification from AICOUNTLY";
    $headers = "From: " . "erp.aicountly.in\r\n";
    $headers .= "Reply-To: erp.aicountly.in\r\n";
    $headers .= "Return-Path: erp.aicountly.in\r\n";
      $headers .= "Organization: AICOUNTLY\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8\r\n";
      $headers .= "X-Priority: 3\r\n";
      $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
      $headers .= "BCC: ". "[]" ;
        $headers.= "CC: ". "[]";
    
    try{
        $retval = mail($email,$sub,$message,$headers);
        if($retval)
        {
            return 'Account created successfully. Check email for confirmation';
        }
    }
    
    catch(Exception $e)
    {
        return 'Account created successfully but problem with email '.$e->getMessage();
    }
}
function sendPasswordEmail($pass, $email)
{
    
    $message = " 
                <html> 
                <head> 
                    <title>AICOUNTLY</title> 
                </head> 
                <body> 
                    <h1>Your registration is successful.</h1> 
                     <p>Your account password is ".$pass."</p>
                </body> 
                </html>";
                
    $sub = "Account Password from AICOUNTLY";
    
    $headers = "From: " . "erp.aicountly.in\r\n";
    $headers .= "Reply-To: erp.aicountly.in\r\n";
    $headers .= "Return-Path: erp.aicountly.in\r\n";
      $headers .= "Organization: AICOUNTLY\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8\r\n";
      $headers .= "X-Priority: 3\r\n";
      $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;
      $headers .= "BCC: ". "[]" ;
        $headers.= "CC: ". "[]";
    
    try{
        $retval = mail($email,$sub,$message,$headers);
        if($retval)
        {
            return 'Password sent';
        }
    }
    
    catch(Exception $e)
    {
        return 'Error- '.$e->getMessage();
    }
}

