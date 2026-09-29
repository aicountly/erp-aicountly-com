<?php
function generateRandomStringVal($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
  	 return base64_encode($randomString);
}
function gstr_random_appkey() {
   $randomKey = openssl_random_pseudo_bytes(32); 
    return base64_encode($randomKey); 
}

function generateHMAC($data, $key){
    return hash_hmac('sha256', $data, $key);
}

function save_preference_string($reqpayload,$json_data){
     if($json_data['is_sandbox']==1)	
	   $publicKey      = file_get_contents('public/eway_public_key.pem');
     else
		$publicKey      = file_get_contents('public/eway_invoice_production_key.pem'); 
	
	$curl = curl_init();
	if($json_data['is_sandbox']==1)
	$curl_url = "https://uatapi.alankitgst.com/taxpayerapi/v1.0/returns";
    else
	$curl_url = "https://uatapi.alankitgst.com/taxpayerapi/v1.0/returns";
echo  $curl_url;

echo 
    ' Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
	' gstin: '.$json_data['Gstin'].'',
	' Content-Type: application/json',
	' ip-usr: '.getClientIP().'',
	' client-secret: '.$json_data['client_secret'].'',
	' txn: '.$json_data['random_rxn_id'].'',
	' clientid: '.$json_data['clientid'].'',
	' state-cd: '.$json_data['state-cd'].'',
	' username: '.$json_data['username'].'',
	' auth-token: '.$json_data['auth_token'].'',
	' ret_period: '.$json_data['ret_period'].''  
  ;
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode($reqpayload),
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
	'gstin: '.$json_data['Gstin'].'',
	'Content-Type: application/json',
	'ip-usr: '.getClientIP().'',
	'client-secret: '.$json_data['client_secret'].'',
	'txn: '.$json_data['random_rxn_id'].'',
	'clientid: '.$json_data['clientid'].'',
	'state-cd: '.$json_data['state-cd'].'',
	'username: '.$json_data['username'].'',
	'auth-token: '.$json_data['auth_token'].'',
	'ret_period: '.$json_data['ret_period'].'',   
  ),
 ));

$response = curl_exec($curl);
curl_close($curl);
return $response;		
}

function get_eway_accesstoken_string($json_data){
     if($json_data['is_sandbox']==1)	
	   $publicKey      = file_get_contents('public/eway_public_key.pem');
     else
		$publicKey      = file_get_contents('public/eway_invoice_production_key.pem'); 

    $requested_data = base64_encode('{
	"action": "'.$json_data['action'].'",
	"username": "'.$json_data['username'].'",
	"password": "'.$json_data['password'].'",
	"app_key": "'.$json_data['app_key'].'" 
}');
	//app_key = 32 length randon key
	openssl_public_encrypt($requested_data, $encrypted, $publicKey,OPENSSL_PKCS1_PADDING);		
 	$Data =  base64_encode($encrypted);	
	$curl = curl_init();
	if($json_data['is_sandbox']==1)
	$curl_url = "https://developers.eraahi.com/api/ewaybillapi/v1.03/auth";
    else
	$curl_url = "https://newewaybill.alankitgst.com/ewaybillgateway/v1.03/auth";

  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode(array("Data" => $Data),JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'Gstin: '.$json_data['Gstin'].'',
    'Content-Type: application/json'
    ),
 ));

$response = curl_exec($curl);
curl_close($curl);
return $response;		
}

function get_einvoice_accesstoken_string($json_data){  
if($json_data['is_sandbox']==1)
	$publicKey      = file_get_contents('public/einvoice_public_key.pem');
  else 
	$publicKey      = file_get_contents('public/einvoice_production_key.pem');  
    $requested_data = base64_encode('{
	"UserName": "'.$json_data['username'].'",
	"Password": "'.$json_data['password'].'",
	"AppKey": "'.$json_data['app_key'].'"
}');
	//app_key = 32 length randon key
	openssl_public_encrypt($requested_data, $encrypted, $publicKey,OPENSSL_PKCS1_PADDING);
		
	$Data =  base64_encode($encrypted);
	$curl = curl_init();
	if($json_data['is_sandbox']==1)
	$curl_url="https://developers.eraahi.com/eInvoiceGateway/eivital/v1.04/auth";
    else
	$curl_url ="https://www.alankitgst.com/eInvoiceGateway/eivital/v1.04/auth";

  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode(array("Data" => $Data),JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'Gstin: '.$json_data['Gstin'].'',
    'Content-Type: application/json'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;		
}

function encryptBySymmetricKey($dataB64, $sekB64){		
    $data = base64_decode($dataB64);                                                // the data to encrypt
    $sek = base64_decode($sekB64);                                                  // the SEK
    $encDataB64 = openssl_encrypt($data, "aes-256-ecb", $sek, 0);                   // the Base64 encoded ciphertext
    return $encDataB64;
}
function decryptBySymmetricKey($encrypted_response, $DecryptedSek){
	$options = 0;
	$ciphering = "aes-256-ecb";
	$decryption_key =  base64_decode($DecryptedSek);
	$decryption_iv = '';
	return $decryption=openssl_decrypt ($encrypted_response, $ciphering,$decryption_key, $options, $decryption_iv);
}
function random_rxn_id($length=15){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomKey = '';
    for ($i = 0; $i < $length; $i++) {
        $randomKey .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomKey;
}
function GetGSTR2BInfo($json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url ="https://uatapi.alankitgst.com/taxpayerapi/v1.0/returns/gstr2b?gstin=".$json_data['Gstin']."&rtnprd=".$json_data['ret_period']."&file_num=".$json_data['file_num']."&action=GET2B";
    else
	$curl_url ="https://gsp.alankitgst.com/taxpayerapi/v1.0/returns/gstr2b?gstin=".$json_data['Gstin']."&rtnprd=".$json_data['ret_period']."&file_num=".$json_data['file_num']."&action=GET2B";;

echo $curl_url;
echo '<br>';
echo 'Content-Type: application/json',
	'  Ocp-Apim-Subscription-Key:'.$json_data['SubscriptionKey'].'',
    '  ip-usr:'.getClientIP().'',
	'  client-secret:'.$json_data['client_secret'].'',
	'  username:'.$json_data['username'].'',
	'  clientid:'.$json_data['clientid'].'',
	'  state-cd:'.$json_data['state-cd'].'',
	'  auth-token:'.$json_data['auth_token'].'',
	'  ret_period:'.$json_data['ret_period'].'',
	'  gstin:'.$json_data['Gstin'].'',
	'  txn:'.$json_data['random_rxn_id'].'' ;
	echo '<br>';
$curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
	'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'ip-usr: '.getClientIP().'',
	'client-secret: '.$json_data['client_secret'].'',
	'username: '.$json_data['username'].'',
	'clientid: '.$json_data['clientid'].'',
	'state-cd: '.$json_data['state-cd'].'',
	'auth-token: '.$json_data['auth_token'].'',
	'ret_period: '.$json_data['ret_period'].'',
	'gstin: '.$json_data['Gstin'].'',
	'txn: '.$json_data['random_rxn_id'].''    
  ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;
	
}

function save_preference_gstrone($json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url ="https://uatapi.alankitgst.com/taxpayerapi/v1.0/returns";
    else
	$curl_url ="https://uatapi.alankitgst.com/taxpayerapi/v1.0/returns";
  $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode(array("action"=>$json_data['action'],"data"=>$reqpayload),JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),  
  CURLOPT_HTTPHEADER => array(
		'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
		'gstin: '.$json_data['Gstin'].'',
		'Content-Type: application/json',
		'ip-usr: '.getClientIP().'',
		'client-secret: '.$json_data['client_secret'].'',
		'txn: '.$json_data['random_rxn_id'].'',
		'clientid: '.$json_data['clientid'].'',
		'state-cd: '.$json_data['state-cd'].'',
		'username: '.$json_data['username'].'',
		'auth-token: '.$json_data['auth_token'].'',
		'ret_period: '.$json_data['ret_period'].'',
		 ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;
}

function GetGSTR2A_B2BInfo($json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url ="https://uatapi.alankitgst.com/taxpayerapi/v2.0/returns/gstr2a?gstin=".$json_data['Gstin']."&ret_period=".$json_data['ret_period']."&action=B2B";
    else
	$curl_url ="https://gsp.alankitgst.com/taxpayerapi/v2.0/returns/gstr2a?gstin=".$json_data['Gstin']."&ret_period=".$json_data['ret_period']."&action=B2B";
   
   $save_log='';
  $save_log .=$curl_url;
 $save_log .= "Content-Type:application/json,
	Ocp-Apim-Subscription-Key:".$json_data['SubscriptionKey'].",
    ip-usr:".getClientIP().",
	client-secret:".$json_data['client_secret'].",
	username:".$json_data['username'].",
	clientid:".$json_data['clientid'].",
	state-cd:".$json_data['state-cd'].",	
	auth-token:".$json_data['auth_token'].",
	ret_period:".$json_data['ret_period'].",
	gstin:".$json_data['Gstin'].",	
	txn:".$json_data['random_rxn_id'].""; 
	
$curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type:application/json',
	'Ocp-Apim-Subscription-Key:'.$json_data['SubscriptionKey'].'',
    'ip-usr:'.getClientIP().'',
	'client-secret: '.$json_data['client_secret'].'',
	'username:'.$json_data['username'].'',
	'clientid:'.$json_data['clientid'].'',
	'state-cd:'.$json_data['state-cd'].'',
	'auth-token:'.$json_data['auth_token'].'',
	'ret_period:'.$json_data['ret_period'].'',
	'gstin:'.$json_data['Gstin'].'',
	'txn:'.$json_data['random_rxn_id'].''    
  ),
));
$response = curl_exec($curl);
$save_log .= json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
SaveErrorLog($save_log);
curl_close($curl);
return $response;
	
}

function GetEwayBill($authtoken,$json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url ="https://developers.eraahi.com/api/ewaybillapi/v1.03/ewayapi/GetEwayBill?ewbNo=".$json_data['ewbno'];
    else
	$curl_url ="https://newewaybill.alankitgst.com/ewaybillgateway/v1.03/ewayapi/GetEwayBill?ewbNo=".$json_data['ewbno'];

$curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'Gstin: '.$json_data['Gstin'].'',
    'authtoken: '.$authtoken.'',
	'Content-Type: application/json'    
  ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;
	
}

function GetEInvoiceBill($authtoken,$json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url ="https://developers.eraahi.com/eInvoiceGateway/eicore/v1.03/Invoice/irn/".$json_data['irnno'];
    else
	$curl_url ="https://www.alankitgst.com/eInvoiceGateway/eicore/v1.03/Invoice/irn/".$json_data['irnno'];

$curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'Gstin: '.$json_data['Gstin'].'',
	'user_name: '.$json_data['username'].'',
    'authtoken: '.$authtoken.'',
	'Content-Type: application/json'    
  ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;
	
}

function generate_eway_partb($reqpayload,$authtoken,$json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url ="https://developers.eraahi.com/api/ewaybillapi/v1.03/ewayapi";
    else
	$curl_url ="https://newewaybill.alankitgst.com/ewaybillgateway/v1.03/ewayapi";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode(array("action"=>$json_data['action'],"data"=>$reqpayload),JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'Gstin: '.$json_data['Gstin'].'',
    'authtoken: '.$authtoken.'',
	'Content-Type: application/json'    
  ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;	
}

function generate_eway_bill($reqpayload,$authtoken,$json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url ="https://developers.eraahi.com/api/ewaybillapi/v1.03/ewayapi";
    else
	$curl_url ="https://newewaybill.alankitgst.com/ewaybillgateway/v1.03/ewayapi";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode(array("action"=>$json_data['action'],"data"=>$reqpayload),JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'Gstin: '.$json_data['Gstin'].'',
    'authtoken: '.$authtoken.'',
	'Content-Type: application/json'    
  ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;	
}


function generate_einvoice_irn($reqpayload,$authtoken,$json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url="https://developers.eraahi.com/eInvoiceGateway/eicore/v1.03/Invoice";
	else 
	$curl_url ="https://www.alankitgst.com/eInvoiceGateway/eicore/v1.03/Invoice";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
"Data":"'.$reqpayload.'"
}',
  CURLOPT_HTTPHEADER => array(
    'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
    'Gstin: '.$json_data['Gstin'].'',
    'Content-Type: application/json',
	'authtoken: '.$authtoken.'',
	'user_name: '.$json_data['UserName'].''
     ),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;	
}


function encryptAppKeyWithPublicKey($base64AppKey, $publicKeyPath) {
    $rawAppKey = base64_decode($base64AppKey);
    $publicKey = file_get_contents('public/'.$publicKeyPath);
    $pubKeyRes = openssl_pkey_get_public($publicKey);
    if (!$pubKeyRes) {
        throw new Exception("Failed to load public key.");
    }
    if (!openssl_public_encrypt($rawAppKey, $encryptedAppKey, $pubKeyRes)) {
        throw new Exception("Encryption failed.");
    }
    return base64_encode($encryptedAppKey);
}

function request_otp_gstrone($json_data){ 
    if($json_data['is_sandbox']==1){	
	   $publicKey      ='gstrone_public_key.pem';
       $curl_url="https://uatapi.alankitgst.com/taxpayerapi/v1.0/authenticate";
    }
     else{
		$publicKey      = 'gstrone_live_key.pem';
	    $curl_url ="https://gsp.alankitgst.com/taxpayerapi/v1.0/authenticate";
	 }
	 
   $encryptedAppKey = encryptAppKeyWithPublicKey($json_data['app_key'],$publicKey);
   $data = [
    "action" => $json_data['action'],
    "app_key" => $encryptedAppKey,
    "username" => $json_data['username']
   ];
   $save_log='';
  $save_log .='request_otp_gstrone<br>';  
  $save_log .= $curl_url.'<br>';
  
   $jsonString = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  $save_log .=$jsonString.'<br>';
  $dd= "Content-Type:application/json,
	Ocp-Apim-Subscription-Key:".$json_data['SubscriptionKey'].",
    ip-usr:".getClientIP().",
	client-secret:".$json_data['client_secret'].",
	username:".$json_data['username'].",
	clientid:".$json_data['clientid'].",
	state-cd:".$json_data['state-cd'].",
	txn:".$json_data['random_rxn_id']."";
	 $save_log .=$dd.'<br>';
  $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$jsonString,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
	'Ocp-Apim-Subscription-Key:'.$json_data['SubscriptionKey'].'',
    'ip-usr:'.getClientIP().'',
	'client-secret:'.$json_data['client_secret'].'',
	'username:'.$json_data['username'].'',
	'clientid:'.$json_data['clientid'].'',
	'state-cd:'.$json_data['state-cd'].'',
	'txn:'.$json_data['random_rxn_id'].''
  ),
));
$response = curl_exec($curl);
curl_close($curl);
$save_log .= json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
SaveErrorLog($save_log);

$data = json_decode($response, true);

// Add custom parameter
$data['appkeye'] = $json_data['app_key'];
$modifiedResponse = json_encode($data);
return $modifiedResponse;
}

function request_authtoken_gstrone($json_data){ 
    if($json_data['is_sandbox']==1){	
	   $publicKey      ='gstrone_public_key.pem';
       $curl_url="https://uatapi.alankitgst.com/taxpayerapi/v1.0/authenticate";
    }
    else{
		$publicKey      = 'gstrone_live_key.pem';
	    $curl_url ="https://gsp.alankitgst.com/taxpayerapi/v1.0/authenticate";
	 }

   SaveErrorLog("------yyyyyyy-----");
SaveErrorLog($json_data['app_key']);
SaveErrorLog("--yyyyy-----");
	 
	 
   $encryptedAppKey =encryptAppKeyWithPublicKey($json_data['app_key'],$publicKey);
   $data = [
    "action"   => $json_data['action'],
	"username" => $json_data['username'],
    "app_key"  => $encryptedAppKey,
	"otp"      => $json_data['otp']    
      ];
	  $save_log='';
	 $save_log .='request_authtoken_gstrone<br>';  
  $save_log .= $curl_url.'<br>';
 
  $jsonString = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  $save_log .=$jsonString.'<br>';
  $hh= " Content-Type:application/json,
	 Ocp-Apim-Subscription-Key:".$json_data['SubscriptionKey'].",
     ip-usr:".getClientIP().",
	 client-secret:".$json_data['client_secret'].",
	 username:".$json_data['username'].",
	 clientid:".$json_data['clientid'].",
	 state-cd:".$json_data['state-cd'].",
	 txn:".$json_data['random_rxn_id']."";
  $save_log .=	$hh.'<br>';
 $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $curl_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$jsonString,
  CURLOPT_HTTPHEADER => array(
   'Content-Type:application/json',
	'Ocp-Apim-Subscription-Key:'.$json_data['SubscriptionKey'].'',
    'ip-usr:'.getClientIP().'',
	'client-secret:'.$json_data['client_secret'].'',
	'username:'.$json_data['username'].'',
	'clientid:'.$json_data['clientid'].'',
	'state-cd:'.$json_data['state-cd'].'',
	'txn:'.$json_data['random_rxn_id'].''
    ),
  ));
	$response = curl_exec($curl);
	curl_close($curl);
	
	$save_log .=	json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'<br>';
	SaveErrorLog($save_log);
	return $response;
}

function save_gstrone_data($json_data){
	if($json_data['is_sandbox']=="1")
	$curl_url="https://uatapi.alankitgst.com/taxpayerapi/v4.0/returns/gstr1";
	else 
	$curl_url ="https://uatapi.alankitgst.com/taxpayerapi/v4.0/returns/gstr1";
 echo '<br>';
echo ' Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
		' gstin: '.$json_data['Gstin'].'',
		' Content-Type: application/json',
		' ip-usr: '.getClientIP().'',
		' client-secret: '.$json_data['client_secret'].'',
		' txn: '.$json_data['random_rxn_id'].'',
		' clientid: '.$json_data['clientid'].'',
		' state-cd: '.$json_data['state-cd'].'',
		' username: '.$json_data['username'].'',
		' auth-token: '.$json_data['auth_token'].'',
		' ret_period: '.$json_data['ret_period'].''; 
		
		
   $curl = curl_init();
	  curl_setopt_array($curl, array(
	  CURLOPT_URL => $curl_url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'PUT',
	  CURLOPT_POSTFIELDS =>json_encode($json_data['request_payload']),
	  CURLOPT_HTTPHEADER => array(
		'Ocp-Apim-Subscription-Key: '.$json_data['SubscriptionKey'].'',
		'gstin: '.$json_data['Gstin'].'',
		'Content-Type: application/json',
		'ip-usr: '.getClientIP().'',
		'client-secret: '.$json_data['client_secret'].'',
		'txn: '.$json_data['random_rxn_id'].'',
		'clientid: '.$json_data['clientid'].'',
		'state-cd: '.$json_data['state-cd'].'',
		'username: '.$json_data['username'].'',
		'auth-token: '.$json_data['auth_token'].'',
		'ret_period: '.$json_data['ret_period'].'',
		 ),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	return $response;	
   }
   
  ?>