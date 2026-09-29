<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!function_exists('sendEmail'))
{
	function sendEmail($mailConfig)
    {
     $postfields = array(
	               "sender"=> array("name"=>$mailConfig['mail_from_name'],"email"=>$mailConfig['mail_from_email']),
				   "to"    => array(array("name"=>$mailConfig['mail_to_name'],"email"=>$mailConfig['mail_to_email'])),
	               "subject" => $mailConfig['mail_subject'],
	               "htmlContent" =>$mailConfig['mail_body']
					); 
	   $postfields = json_encode($postfields);			
        $curl = curl_init();
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.brevo.com/v3/smtp/email",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postfields,
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "api-key: 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",
            "content-type: application/json"
        ),
    ));
	//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
    $response = curl_exec($curl);	
    $err = curl_error($curl);
    curl_close($curl);
    if($err) 
      return false;
    else
     return true;       
    }
}