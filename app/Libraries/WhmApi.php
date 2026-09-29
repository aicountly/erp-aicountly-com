<?php
namespace App\Libraries;
class WhmApi {
    
    public function __construct($cpanelPort = '2083') {
		$this->whmHost = 'vps-f33ac204.vps.ovh.ca'; // hostname or IP
		$this->apiToken = 'YV7XVVGHCEWEAYZYCK426T4IO6ORGIVB'; // WHM root API token
		$this->cpanelUser = 'aicountlyin'; // cPanel account where DB will be created
    }
    
    /////////////// MYSQL CPANEL //////////////////
    
    public function createDataBaseMySQL($database) {
      $createDb = $this->callWHMAPI("https://$this->whmHost:2087/execute/Mysql/create_database?name=$database");

    
    }
    
    public function createUserMySQL($dbuser, $dbpass) {
		
		$createUser = $this->callWHMAPI("https://$this->whmHost:2087/execute/Mysql/create_user?name=$dbuser&password=$dbpass");
 
    }
    
    public function setPrivilegesMySQL($dbuser, $database) {
	
	  $grantPriv = $this->callWHMAPI("https://$this->whmHost:2087/execute/Mysql/set_privileges_on_database?&user=$dbuser&database=$database&privileges=ALL");

    }
   public function deleteDataBaseMySQL($database) {	
	  $grantPriv = $this->callWHMAPI("https://$this->whmHost:2087/execute/Mysql/delete_database?name=$database");
    }
   public function deleteUserMySQL($db_usernaem) {	
	  $grantPriv = $this->callWHMAPI("https://$this->whmHost:2087/execute/Mysql/delete_user?name=$db_usernaem");
    }
   
   
   // Function to call WHM API
	function callWHMAPI($url) {
		echo $url;
		die();
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			"Authorization: whm root:$this->apiToken",
			"cpanel_uapi_user: $this->cpanelUser"
		]);
		$result = curl_exec($curl);
		if (curl_errno($curl)) {
			die("cURL Error: " . curl_error($curl));
		}
		curl_close($curl);
		echo '<pre>';
		print_r($result);
		die();
	}
  }
?>