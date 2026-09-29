<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables;
use App\Libraries\ERPtables;
use App\Libraries\cPanelApi;

class Script2Model extends Model	{
	public function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();
		$this->cpanel    = new cPanelApi();	
		$this->aicountly_db = $this->externaldb->aicountly_db();
		$this->db        =  $this->externaldb->get_company_db();
		$this->session       =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');
		$this->enc_string    =  new enc_string();
    }

    function all_companies()
    {

    	$result=$this->aicountly_db->table("aicountly_useraictly_univdb")
    							->where('useraictly_status', 1)
									->get()->getResultArray();

			foreach ($result as $key => $value) {
				// $this->create_tables($value['uuid']);
			}
    }

    function create_tables($uuid)
    {
			$external_db = $this->externaldb->connect_uuid_db($uuid);

			$database = 'aicountlyin_uuid_'.$uuid;
			$connection =  $this->cpanel->checkDataBaseMySQL($database);
			$connection = json_decode($connection);

			if($connection->status){
				try{
		      $external_db->query("CREATE TABLE `credkeysnn` (
					  `credkeysnn_id` bigint NOT NULL AUTO_INCREMENT,
					  `cred_id` bigint NOT NULL,
					  `cred_clientid` varchar(70) NOT NULL,
					  `cred_secret_key` varchar(100) NOT NULL,
					  PRIMARY KEY (`credkeysnn_id`),
					  KEY `cred indxing` (`cred_id`,`cred_clientid`)
					) ENGINE=InnoDB"); 
	      }
	      catch (\Exception $e) {
	       echo $e->getMessage() . '<br>';
	      }

	      try{
		      $external_db->query("CREATE TABLE `credential` (
						  `cred_id` bigint NOT NULL AUTO_INCREMENT,
						  `comp_id` bigint NOT NULL,
						  `cred_site` varchar(1) NOT NULL,
						  `cred_type` varchar(1) NOT NULL,
						  `cred_user` varchar(60) NOT NULL,
						  `cred_pass` varchar(60) NOT NULL,
						  `bo_id` bigint NOT NULL,
						  `cred_remark` varchar(255) NOT NULL,
						  PRIMARY KEY (`cred_id`),
						  KEY `cred indxing` (`comp_id`,`cred_site`,`cred_type`)
						) ENGINE=InnoDB"); 
	      }
	      catch (\Exception $e) {
	       echo $e->getMessage() . '<br>';
	      }

	      echo "uuid- ".$uuid."  Executed 2"."<br>";
			}
			else{
				echo '<br><br>'.'Database not exists uuid- '.$uuid.'<br>';
			}
    }

}