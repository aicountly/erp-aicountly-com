<?php
namespace App\Libraries;
class enc_string {
 
 function __construct(){			
				
	 }		 
	function nc_string($simple_string,$type){
	       if(is_null($simple_string) || $simple_string=='')
			   return "";
	else{
			// Store the cipher method
			$ciphering = "AES-128-CTR";
			  
			// Use OpenSSl Encryption method
			$iv_length = openssl_cipher_iv_length($ciphering);
			$options = 0;
			  
			
			// Use openssl_encrypt() function to encrypt the data
			if($type=='en'){
			// Non-NULL Initialization Vector for encryption
			$encryption_iv = '1234567891011121';
			  
			// Store the encryption key
			$encryption_key = "GeeksforGeeks";
			  	
			$encryption = openssl_encrypt($simple_string, $ciphering,
						$encryption_key, $options, $encryption_iv);
			  
			  return $encryption;
			}
			if($type=='de'){
			// Non-NULL Initialization Vector for decryption
			$decryption_iv = '1234567891011121';
			  
			// Store the decryption key
			$decryption_key = "GeeksforGeeks";
			  
			// Use openssl_decrypt() function to decrypt the data
			$decryption=openssl_decrypt ($simple_string, $ciphering, 
					$decryption_key, $options, $decryption_iv);
			  
			// Display the decrypted string
			return $decryption;
			}
	}
		
	} 
}