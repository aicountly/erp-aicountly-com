<?php
namespace App\Libraries;
use App\Libraries\externaldb;

class GRPtables { // 2
	
	public $grpco_id;
	public $grp_fy_id;
	public $db;
	public $drop_status;
	public $create_status;

	function __construct($grpco_id,$grp_fy_id){	

		helper(['custom','predefineERP']);

		$this->grpco_id = $grpco_id;
		$this->grp_fy_id = $grp_fy_id;

		$comp_code = grp_compcode_format($grpco_id);

		$externaldb = new externaldb();
		$this->db = $externaldb->single_company_db($comp_code);
	}
	
	function default_tables($create_status=true,$drop_status=false)
	{
		$errors = [];

		$table = 'grpcomstid';   	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`crs_master_id` BIGINT NOT NULL AUTO_INCREMENT, 
					`grpco_id` BIGINT NOT NULL,
					`crs_master_type` VARCHAR(10) NOT NULL,
					`crs_master_name` VARCHAR(255) NOT NULL,
					`comp_id_str` TEXT NOT NULL,
					`parent_id` BIGINT NOT NULL , 
					`parent` VARCHAR(255) NOT NULL, 
					PRIMARY KEY (`crs_master_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = 'grpmapping';  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`grpmp_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`grpco_id` BIGINT NOT NULL ,
					`crs_master_id` BIGINT NOT NULL , 
					`master_id` BIGINT NOT NULL ,
					`master_name` VARCHAR(255) NOT NULL ,
					`master_type` VARCHAR(10) NOT NULL , 
					`comp_id` BIGINT NOT NULL ,
					`parent_id` BIGINT NOT NULL , 
					`parent` VARCHAR(255) NOT NULL, 
					PRIMARY KEY (`grpmp_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}
}