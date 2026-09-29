<?php
namespace App\Libraries;
use App\Libraries\externaldb;

class ERPtables { // 89 (85 + 4*x)
	
	public $comp_id;
	public $fy_id;
	public $db;
	public $drop_status;
	public $create_status;

	function __construct($comp_id,$fy_id){	

		helper(['custom','predefineERP']);

		$this->comp_id = $comp_id;
		$this->fy_id = $fy_id;

		$comp_code = erp_compcode_format($this->comp_id);
		$comp_code = strtolower($comp_code);
		$externaldb = new externaldb();
		$this->db = $externaldb->single_company_db($comp_code);
	}

	function default_tables($create_status = true,$drop_status = false)
	{
		$errors = [];
		
		$response = $this->userspecific_customization_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }
		
		$response = $this->company_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->company_tax_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->branch_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->print_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->voucher_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->voucher_series_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->currency_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->account_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->account_tax_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->item_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->unit_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->item_tax_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->barcode_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->consignment_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->mc_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->bill_sundry_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response =$this->bill_sundry_tax_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->bill_by_bill_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->cost_center_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response=$this->bill_of_material_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->project_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->new_tax_tables($create_status,$drop_status);
		foreach ($response as $error) { $errors[] = $error; }

		return $errors;
	}

	function default_data()
	{
		$errors = [];

		$response = $this->set_account_group_parent_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_account_group_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_account_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_bill_sundry_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_cost_center_group_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_cost_center_data();
		foreach ($response as $error) { $errors[] = $error; }

		// $response = $this->set_bill_by_bill_data();
		// foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_unit_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_item_category_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_item_group_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_mc_group_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_mc_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_voucher_type_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_voucher_series_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_voucher_subtype_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_project_group_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_project_data();
		foreach ($response as $error) { $errors[] = $error; }

		$response = $this->set_print_config_data();
		foreach ($response as $error) { $errors[] = $error; }
		
		$response = $this->set_currency_data();
		foreach ($response as $error) { $errors[] = $error; }
		
		
		$response = [];//$this->set_print_design_data();
		foreach ($response as $error) { $errors[] = $error; }

		return $errors;
	}

	//----------------------

	function new_tax_tables($create_status=true,$drop_status=false) // 13 tables
	{
	   $errors = [];
	   
	   $table = $this->comp_id.'_bufflogrec_'.$this->fy_id;
       try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
								 `buffer_log_rec_id` BIGINT NOT NULL AUTO_INCREMENT , 
								 `buff_rev_log_id` BIGINT NOT NULL , `cont_item_id_unit_avail_id` VARCHAR(100) NOT NULL ,
								 `method_id` INT NOT NULL , `buffer_ball_qty_rec` DECIMAL(18,4) NOT NULL ,
								 `buffer_bal_rate_rec` DECIMAL(18,4) NOT NULL , `valuation_id` BIGINT NOT NULL , 
								 `op_valuation_id` BIGINT NOT NULL , `voucher_txn_id` BIGINT NOT NULL, `item_id` BIGINT NOT NULL , 
								  PRIMARY KEY (`buffer_log_rec_id`), INDEX `logindex` (`buff_rev_log_id`, `cont_item_id_unit_avail_id`, `method_id`, `voucher_txn_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		} 
	   
	   
       $table = $this->comp_id.'_buffercalc_'.$this->fy_id;
       try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				`buffer_ball_id` BIGINT NOT NULL AUTO_INCREMENT ,
				`cont_item_id_unit_avail_id` VARCHAR(100) NOT NULL ,
				`method_id` INT NOT NULL , `txn_id` BIGINT NOT NULL ,
				`buffer_ball_qty` DECIMAL(18,4) NOT NULL ,
				`buffer_ball_rate` DECIMAL(18,8) NOT NULL ,
				`valuation_id` BIGINT NOT NULL ,`voucher_txn_id` BIGINT NOT NULL ,`op_valuation_id` BIGINT NOT NULL DEFAULT '0',
				 PRIMARY KEY (`buffer_ball_id`),
				 INDEX `buffercalcindex` (`cont_item_id_unit_avail_id`, `method_id`, `valuation_id`, `txn_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		} 		
		
		 $table = $this->comp_id.'_buffrevlog_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				`buff_rev_log_id` BIGINT NOT NULL AUTO_INCREMENT ,
				`cont_item_id_unit_avail_id` VARCHAR(100) NOT NULL ,
				`method_id` INT NOT NULL , `txn_id` BIGINT NOT NULL ,
				`buffer_ball_id` BIGINT NOT NULL ,
				`buff_ball_rev_qty` DECIMAL(18,4) NOT NULL ,
				`valuation_id` INT NOT NULL,`voucher_txn_id` BIGINT NOT NULL,`op_valuation_id` BIGINT NOT NULL DEFAULT '0' , PRIMARY KEY (`buff_rev_log_id`),
				INDEX `buffrevlog index` (`cont_item_id_unit_avail_id`, `method_id`, `buffer_ball_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}  

      $table = $this->comp_id.'_itemrepall_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."`(
			`itemvalrep_id` BIGINT NOT NULL AUTO_INCREMENT , 
			`item_id_unit_id` VARCHAR(50) NOT NULL , `voucher_txn_id` BIGINT NOT NULL , 
			`method_id` INT NOT NULL , `item_value` DECIMAL(18,8) NOT NULL , 
			`item_txn_id` BIGINT NOT NULL , `item_bal_qty` DECIMAL(18,4) NOT NULL , 
			`item_avail` INT NOT NULL , `item_unit` INT NOT NULL , `valuation_id` INT NOT NULL ,`item_txn_date` DATE NOT NULL,
			`profit` DECIMAL(18,8) NOT NULL DEFAULT '0',	
			PRIMARY KEY (`itemvalrep_id`), 
			INDEX `itemrepall indexing` (`item_id_unit_id`, `voucher_txn_id`, `method_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
	 $table = $this->comp_id.'_itemrepmcn_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."`(
			`itemrepmc_id` BIGINT NOT NULL AUTO_INCREMENT , 
			`item_id_unit_id` VARCHAR(50) NOT NULL , `voucher_txn_id` BIGINT NOT NULL , 
			`method_id` INT NOT NULL , `item_value` DECIMAL(18,8) NOT NULL , 
			`item_txn_id` BIGINT NOT NULL , `item_bal_qty` DECIMAL(18,4) NOT NULL , 
			`item_avail` INT NOT NULL , `item_unit` INT NOT NULL , `valuation_id` INT NOT NULL, `mat_cent_id` INT NOT NULL ,`item_txn_date` DATE NOT NULL,
			`bo_id` BIGINT NOT NULL, `profit` DECIMAL(18,8) NOT NULL DEFAULT '0',
			PRIMARY KEY (`itemrepmc_id`), 
			INDEX `itemrepmcn indexing` (`item_id_unit_id`, `voucher_txn_id`, `method_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}	

    $table = $this->comp_id.'_itemrepgrp_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."`(
			`itemrepgrp_id` BIGINT NOT NULL AUTO_INCREMENT , 
			`item_id_unit_id` VARCHAR(50) NOT NULL , `voucher_txn_id` BIGINT NOT NULL , 
			`method_id` INT NOT NULL , `item_value` DECIMAL(18,8) NOT NULL , 
			`item_txn_id` BIGINT NOT NULL , `item_bal_qty` DECIMAL(18,4) NOT NULL , 
			`item_avail` INT NOT NULL , `item_unit` INT NOT NULL , `valuation_id` INT NOT NULL, `item_grp_id` INT NOT NULL ,`item_txn_date` DATE NOT NULL,
			`bo_id` BIGINT NOT NULL, `profit` DECIMAL(18,8) NOT NULL DEFAULT '0',
			PRIMARY KEY (`itemrepgrp_id`), 
			INDEX `itemrepgrp indexing` (`item_id_unit_id`, `voucher_txn_id`, `method_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
 $table = $this->comp_id.'_itemrepcat_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."`(
			`itemrepcat_id` BIGINT NOT NULL AUTO_INCREMENT , 
			`item_id_unit_id` VARCHAR(50) NOT NULL , `voucher_txn_id` BIGINT NOT NULL , 
			`method_id` INT NOT NULL , `item_value` DECIMAL(18,8) NOT NULL , 
			`item_txn_id` BIGINT NOT NULL , `item_bal_qty` DECIMAL(18,4) NOT NULL , 
			`item_avail` INT NOT NULL , `item_unit` INT NOT NULL , `valuation_id` INT NOT NULL, `item_cat` INT NOT NULL ,`item_txn_date` DATE NOT NULL,
			`bo_id` BIGINT NOT NULL,`profit` DECIMAL(18,8) NOT NULL DEFAULT '0', 
			PRIMARY KEY (`itemrepcat_id`), 
			INDEX `itemrepcat indexing` (`item_id_unit_id`, `voucher_txn_id`, `method_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
	$table = $this->comp_id.'_itemrepprj_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."`(
			`itemrepprj_id` BIGINT NOT NULL AUTO_INCREMENT , 
			`item_id_unit_id` VARCHAR(50) NOT NULL , `voucher_txn_id` BIGINT NOT NULL , 
			`method_id` INT NOT NULL , `item_value` DECIMAL(18,8) NOT NULL , 
			`item_txn_id` BIGINT NOT NULL , `item_bal_qty` DECIMAL(18,4) NOT NULL , 
			`item_avail` INT NOT NULL , `item_unit` INT NOT NULL , `valuation_id` INT NOT NULL, `project_id` INT NOT NULL ,`item_txn_date` DATE NOT NULL,
			`bo_id` BIGINT NOT NULL,`profit` DECIMAL(18,8) NOT NULL DEFAULT '0', 
			PRIMARY KEY (`itemrepprj_id`), 
			INDEX `itemrepprj indexing` (`item_id_unit_id`, `voucher_txn_id`, `method_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}	
		
	$table = $this->comp_id.'_itemrepbon_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."`(
			`itemrepbo_id` BIGINT NOT NULL AUTO_INCREMENT , 
			`item_id_unit_id` VARCHAR(50) NOT NULL , `voucher_txn_id` BIGINT NOT NULL , 
			`method_id` INT NOT NULL , `item_value` DECIMAL(18,8) NOT NULL , 
			`item_txn_id` BIGINT NOT NULL , `item_bal_qty` DECIMAL(18,4) NOT NULL , 
			`item_avail` INT NOT NULL , `item_unit` INT NOT NULL , `valuation_id` INT NOT NULL, `bo_id` INT NOT NULL ,`item_txn_date` DATE NOT NULL,
			`profit` DECIMAL(18,8) NOT NULL DEFAULT '0',			
			PRIMARY KEY (`itemrepbo_id`), 
			INDEX `itemrepbo indexing` (`item_id_unit_id`, `voucher_txn_id`, `method_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}	

$table = $this->comp_id.'_itemrepbat_'.$this->fy_id;
        try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."`(
			`itemrepbat_id` BIGINT NOT NULL AUTO_INCREMENT , 
			`item_id_unit_id` VARCHAR(50) NOT NULL , `voucher_txn_id` BIGINT NOT NULL , 
			`method_id` INT NOT NULL , `item_value` DECIMAL(18,8) NOT NULL , 
			`item_txn_id` BIGINT NOT NULL , `item_bal_qty` DECIMAL(18,4) NOT NULL , 
			`item_avail` INT NOT NULL , `item_unit` INT NOT NULL , `valuation_id` INT NOT NULL, `batch_id` INT NOT NULL ,`item_txn_date` DATE NOT NULL,
			`bo_id` BIGINT NOT NULL, `profit` DECIMAL(18,8) NOT NULL DEFAULT '0',
			PRIMARY KEY (`itemrepbat_id`), 
			INDEX `itemrepbat indexing` (`item_id_unit_id`, `voucher_txn_id`, `method_id`, `valuation_id`)) ENGINE = InnoDB;");
			   }
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}








		$table = $this->comp_id.'_ewbmstreqn_'.$this->fy_id;  	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}
			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				  `ewb_id` bigint NOT NULL AUTO_INCREMENT,
				  `ewb_supply_type` varchar(10) NOT NULL DEFAULT '',
				  `ewb_sub_supply_type` varchar(10) DEFAULT NULL,
				  `ewb_sub_supply_desc` varchar(200) DEFAULT NULL,
				  `ewb_doc_type` varchar(20) DEFAULT NULL,
				  `voucher_txn_id` bigint NOT NULL,
				  `ewb_txn_type` varchar(10) NOT NULL,
				  `bo_id` bigint NOT NULL,
				  PRIMARY KEY (`ewb_id`),
				  KEY `supply index` (`ewb_supply_type`,`voucher_txn_id`,`ewb_txn_type`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_gstdispfrm_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`dispfrm_id` bigint NOT NULL AUTO_INCREMENT,
				  `ewb_id` bigint NOT NULL,
				  `einv_id` bigint NOT NULL,
				  `dispfrm_addr1` varchar(100) NOT NULL,
				  `dispfrm_addr2` varchar(100) NOT NULL,
				  `dispfrm_place` varchar(50) NOT NULL,
				  `dispfrm_pin` varchar(15) NOT NULL,
				  `dispfrm_state_code` varchar(3) NOT NULL,
				  PRIMARY KEY (`dispfrm_id`),
				  KEY `dispfrm indexing` (`dispfrm_state_code`,`ewb_id`,`einv_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_gstshipton_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`shipto_id` bigint NOT NULL AUTO_INCREMENT,
				  `ewb_id` bigint NOT NULL,
				  `einv_id` bigint NOT NULL,
				  `shipto_addr1` varchar(100) NOT NULL,
				  `shipto_addr2` varchar(100) NOT NULL,
				  `shipto_place` varchar(50) NOT NULL,
				  `shipto_pin` varchar(15) NOT NULL,
				  `shipto_state_code` varchar(3) NOT NULL,
				  `shipto_gstin` VARCHAR(30) NULL DEFAULT NULL,
				  `shipto_legal_name` VARCHAR(60) NULL DEFAULT NULL,
				  `shipto_trade_name` VARCHAR(60) NULL DEFAULT NULL,
				  PRIMARY KEY (`shipto_id`),
				  KEY `shiptoindex` (`ewb_id`,`einv_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_invvaldetn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					  `invvaldetn_id` bigint NOT NULL AUTO_INCREMENT,
					  `vch_txn_id` bigint NOT NULL,
					  `ewb_id` bigint NOT NULL,
					  `einv_id` bigint NOT NULL,
					  `ass_value` decimal(18,8) NOT NULL,
					  `igst_value` decimal(18,8) NOT NULL,
					  `cgst_value` decimal(18,8) NOT NULL,
					  `sgst_value` decimal(18,8) NOT NULL,
					  `cess_adv_val` decimal(18,8) NOT NULL,
					  `cess_nonadv_val` decimal(18,8) NOT NULL,
					  `disc_value` decimal(18,8) NOT NULL,
					  `othchg_value` decimal(18,8) NOT NULL,
					  `roff_value` decimal(18,8) NOT NULL,
					  `inv_value_inr` decimal(18,8) NOT NULL,
					  `inv_value_fcy` date NOT NULL,
					  PRIMARY KEY (`invvaldetn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_ewbmstcons_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`conso_ewb_id` bigint NOT NULL AUTO_INCREMENT,
				  `conso_ewb_no` bigint NOT NULL,
				  `conso_ewb_date` date NOT NULL,
				  `ewb_id` bigint NOT NULL,
				  `ewb_no` bigint NOT NULL,
				  PRIMARY KEY (`conso_ewb_id`),
				  KEY `MSTINDEXING` (`conso_ewb_no`,`ewb_id`,`ewb_no`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_ewbpartbdt_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`prtbid` bigint NOT NULL AUTO_INCREMENT,
					`gsttpt_id` bigint NOT NULL,
					`ewb_id` bigint NOT NULL,
					`conso_ewb_id` bigint NOT NULL,
					`trans_veh_no` varchar(20) NOT NULL,
					`trans_veh_type` varchar(2) NOT NULL,
					`trans_mode` smallint NOT NULL,
					`trans_doc_no` varchar(20) NOT NULL,
					`trans_doc_date` date NOT NULL,
					`trans_rsn_code` varchar(100) NOT NULL,
					`trans_rsn_rem` varchar(100) NOT NULL,
					`trans_dist` varchar(6) NOT NULL,
					`trans_frm_place` varchar(20) NOT NULL,
					`trans_frm_state_code` varchar(10) NOT NULL,
					`tpt_update_date` date NOT NULL,
					`veh_update_date` date NOT NULL,
					`voucher_txn_id` bigint NOT NULL,
					PRIMARY KEY (`prtbid`),
					KEY `transport indexing` (`gsttpt_id`,`trans_veh_no`,`trans_veh_type`,`trans_mode`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_gsttptmstn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`gsttpt_id` bigint NOT NULL AUTO_INCREMENT,
					`gsttpt_name` varchar(50) DEFAULT 'na',
					`gsttpt_gstin` varchar(20) DEFAULT 'na',
					`gsttpt_enrl_id` varchar(6) DEFAULT 'na',
					PRIMARY KEY (`gsttpt_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_ewbmastern_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`ewbmastern_id` bigint NOT NULL AUTO_INCREMENT,
					`ewb_id` bigint NOT NULL,
					`ewb_no` bigint NOT NULL,
					`ewb_date` date NOT NULL,
					`ewb_valid_dt` date NOT NULL,
					`ewb_update_valid_dt` date NOT NULL,
					`ewb_status` varchar(50) NOT NULL,
					PRIMARY KEY (`ewbmastern_id`),
					KEY `mastern indxing` (`ewb_id`,`ewb_no`,`ewb_date`,`ewb_valid_dt`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_ewbtrailnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`ewb_id` bigint NOT NULL,
				  `ewb_no` bigint NOT NULL,
				  `ewb_alert` varchar(10) NOT NULL,
				  `ewb_trail_type` varchar(10) NOT NULL,
				  `ewb_trail_date` date NOT NULL,
				  `ewb_trail_rsn_code` varchar(10) NOT NULL,
				  `ewb_trail_rsn_rem` varchar(10) NOT NULL,
				  PRIMARY KEY (`ewb_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_einvmstreq_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`einvreq_id` bigint NOT NULL AUTO_INCREMENT,
					`einv_id` bigint NOT NULL,
				  `einv_tax_schm` varchar(10) NOT NULL,
				  `einv_supply_type` VARCHAR(20) NULL DEFAULT NULL,
				  `einv_reg_rev` varchar(2) NOT NULL,
				  `einv_ecm_gstin` varchar(30)  NULL DEFAULT NULL,
				  `einv_igst_intra` varchar(30)  NULL DEFAULT NULL,
				  `einb_doc_type` varchar(3) NOT NULL,
				  `voucher_txn_id` bigint NOT NULL,
				  PRIMARY KEY (`einvreq_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_einvmaster_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`einv_id` bigint NOT NULL AUTO_INCREMENT,
				  `einv_no` varchar(30) NOT NULL,
				  `einv_date` date NOT NULL,
				  `einv_valid_dt` date NOT NULL,
				  `einv_alert` varchar(20) NOT NULL,
				  `voucher_txn_id` bigint NOT NULL,
				  `einv_status` varchar(50) NOT NULL,
				  PRIMARY KEY (`einv_id`),
				  KEY `enmst indexing` (`einv_no`,`einv_date`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_gstroutsup_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`outsup_id` bigint NOT NULL AUTO_INCREMENT,
				  `voucher_txn_id` bigint NOT NULL,
				  `outsup_pos` varchar(3) NOT NULL,
				  `outsup_bill_ref_no` varchar(20) DEFAULT NULL,
				  `outsup_rev_chg` varchar(1) NOT NULL DEFAULT '0',
				  `outsup_inv_type` VARCHAR(15) NOT NULL DEFAULT '0',
				  `outsup_dr_note` ENUM('1','0') NOT NULL DEFAULT '0',
				  `outsup_eco` INT NOT NULL DEFAULT '0',
				  PRIMARY KEY (`outsup_id`),
				   KEY `pos indexing` (`voucher_txn_id`,`outsup_rev_chg`,`outsup_bill_ref_no`,`outsup_eco`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_gstrinwsup_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`inwsup_id` bigint NOT NULL AUTO_INCREMENT,
				  `voucher_txn_id` bigint NOT NULL,
				  `inwsup_pos` varchar(3) NOT NULL,
				  `inwsup_bill_ref_no` varchar(20) DEFAULT NULL,
				  `inwsup_rev_chg` varchar(1) NOT NULL DEFAULT '0',
				  `inwsup_cr_note` ENUM('1','0') NOT NULL DEFAULT '0',
				  `inwsup_inv_type` VARCHAR(15) NOT NULL,
				  `inwsup_eco` ENUM('1','0') NOT NULL DEFAULT '0',
				  PRIMARY KEY (`inwsup_id`),
				  KEY `pos indexing` (`voucher_txn_id`,`inwsup_rev_chg`,`inwsup_bill_ref_no`,`inwsup_eco`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}
	
	function userspecific_customization_tables($create_status=true,$drop_status=false){
		$errors = [];
		$table = $this->comp_id.'_erpcmdline';  	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`erp_cmd_id` BIGINT NOT NULL AUTO_INCREMENT , `uuid` BIGINT NOT NULL , `erp_config_id` BIGINT NOT NULL , `erp_cmd` VARCHAR(100) NOT NULL , `erp_rpt_name` VARCHAR(150) NOT NULL , `erp_rpt_alias` VARCHAR(150) NOT NULL , PRIMARY KEY (`erp_cmd_id`), INDEX `cmdindex` (`uuid`, `erp_config_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
	
     $table = $this->comp_id.'_usr_prefnn';  	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`usr_pref_id` BIGINT NOT NULL AUTO_INCREMENT , `uuid` BIGINT NOT NULL , `usr_config_id` BIGINT NOT NULL , `usr_config_value` VARCHAR(150) NOT NULL , PRIMARY KEY (`usr_pref_id`), INDEX `prfnindex` (`uuid`, `usr_config_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
	$table = $this->comp_id.'_cfg_txnlmt';  	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`txn_limit_id` BIGINT NOT NULL AUTO_INCREMENT , `uuid` BIGINT NOT NULL , `voucher_series_id` BIGINT NOT NULL , `usr_config_id` BIGINT NOT NULL , `txn_limit_self` VARCHAR(50) NOT NULL , `accessprof_id` BIGINT NOT NULL , PRIMARY KEY (`txn_limit_id`), INDEX `txnlmnt_index` (`uuid`, `voucher_series_id`, `usr_config_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
	
	$table = $this->comp_id.'_txnlmtappr';  	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`txn_limit_id` BIGINT NOT NULL AUTO_INCREMENT , `txn_limit_max` DECIMAL(18,8) NOT NULL , `txn_limit_approver1` INT NOT NULL , `txn_limit_approver2` INT NOT NULL , `accessprof_id` BIGINT NOT NULL , PRIMARY KEY (`txn_limit_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}	
	
     $table = $this->comp_id.'_config_bde';  	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`usr_bde_id` INT NOT NULL , `uuid` BIGINT NOT NULL , `usr_config_id` BIGINT NOT NULL , `voucher_series_id` BIGINT NOT NULL , `usr_bde` VARCHAR(10) NOT NULL , `accessprof_id` INT NOT NULL , INDEX `config_bdeindex` (`uuid`, `usr_config_id`, `voucher_series_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
	$table = $this->comp_id.'_cfg_endisb';  	
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`endisb_id` BIGINT NOT NULL AUTO_INCREMENT , `usr_config_id` BIGINT NOT NULL , `usr_config_value` VARCHAR(50) NOT NULL , `usr_config_force` VARCHAR(50) NOT NULL , `accessprof_id` INT NOT NULL , PRIMARY KEY (`endisb_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}	
		return $errors;
	}
	
	function company_tables($create_status=true,$drop_status=false) // 9 tables
	{
		$errors = [];
		$table = $this->comp_id.'_cmpacsprof_'.$this->fy_id;  	
		try{
				// echo "<pre>";print_r($this->db);exit;
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cmpacsprof_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`cmpacsprof_name` VARCHAR(105) NOT NULL , 
					`cmpacsprof_desg` VARCHAR(100) NOT NULL , 
					PRIMARY KEY (`cmpacsprof_id`)) ENGINE = InnoDB;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		$table = $this->comp_id.'_cmpbankmst_'.$this->fy_id;  	
		try{
				// echo "<pre>";print_r($this->db);exit;
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_bank_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT NOT NULL , 
					`bo_id` BIGINT,
					`comp_bank_name` VARCHAR(150) NOT NULL ,
					`comp_bank_acc_no` VARCHAR(150) NOT NULL ,
					`comp_bank_ifsc` VARCHAR(150) NOT NULL ,
					`comp_bank_micr` VARCHAR(150) NOT NULL ,
					`comp_bank_branch` VARCHAR(150) NOT NULL ,
					`comp_bank_adrs` VARCHAR(255) NOT NULL, 
					`comp_bank_city` VARCHAR(255) NOT NULL ,
					`comp_bank_state` BIGINT NULL ,
					`comp_bank_country` BIGINT NOT NULL ,
					`comp_bank_pin_code` VARCHAR(20) NOT NULL ,
					`comp_bank_acc_type` VARCHAR(255) NOT NULL ,
					`mst_base_id` BIGINT NOT NULL ,
					PRIMARY KEY (`comp_bank_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		
		/* $table = $this->comp_id.'_usrcmdline';  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`usrerp_cmd_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`usrerp_config_id` BIGINT NOT NULL , 
					`usrerp_cmd` VARCHAR(150) NOT NULL ,
					`usrerp_rpt_name` VARCHAR(150) NOT NULL ,
					`usrerp_rpt_alias` VARCHAR(150) NOT NULL ,					
					PRIMARY KEY (`usrerp_cmd_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		} */
		
		$table = $this->comp_id.'_compnotesn_'.$this->fy_id; 	
		try{
				// echo "<pre>";print_r($this->db);exit;
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`note_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`note_master_id` BIGINT NOT NULL , 
					`note_master_type` VARCHAR(30) NOT NULL ,
					`note_heading` VARCHAR(150) NOT NULL ,
					`note_details` VARCHAR(150) NOT NULL ,					
					PRIMARY KEY (`note_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		

		$table = $this->comp_id.'_cmperplogs_'.$this->fy_id;  	
		try{
				// echo "<pre>";print_r($this->db);exit;
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`log_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`uuid_aicountly` BIGINT NOT NULL , 
					`user_aicountly_buisness_id` BIGINT NOT NULL , 
					`log_date` DATE NOT NULL , 
					`log_time` TIME NOT NULL , 
					`log_action_tags` VARCHAR(255) NOT NULL ,
					`log_field_id` BIGINT NOT NULL , 
					`log_field_name` VARCHAR(255) NOT NULL, 
					`log_field_type` VARCHAR(255) NOT NULL ,
					PRIMARY KEY (`log_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_tanmastern_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`tanmastern_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT NOT NULL , 
					`comp_fy_id` BIGINT NOT NULL , 
					`bo_id` BIGINT DEFAULT 1 , 
					`bo_tan` VARCHAR(50) NOT NULL , 
					`bo_tan_jurid` VARCHAR(50) NOT NULL , 
					`tan_wefdate` DATE NOT NULL , 
					`tan_inactivedate` DATE NOT NULL , 
					PRIMARY KEY (`tanmastern_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_gstinmastr_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`gstinmastr_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT NOT NULL , 
					`bo_id` BIGINT DEFAULT 1 , 
					`comp_gstin` VARCHAR(50) NOT NULL , 
					`comp_gst_jurid_st` VARCHAR(50) NOT NULL , 
					`comp_gst_jurid_ct` VARCHAR(50) NOT NULL , 
					`comp_gstin_type` INT NOT NULL , 
					`gstin_wefdate` DATE NOT NULL , 
					`gstin_inactivedate` DATE NOT NULL ,
					`gstin_legal_name` VARCHAR(100) NOT NULL DEFAULT '0',
					`gstin_trade_name` VARCHAR(100) NOT NULL DEFAULT '0',
					`gstin_state_code` VARCHAR(10) NOT NULL DEFAULT '0',
					`comp_gstin_sub_type` INT NOT NULL ,
					
					PRIMARY KEY (`gstinmastr_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpprofsnl_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_id` BIGINT NOT NULL , 
					`comp_ca_uuid_aicountly1` BIGINT NOT NULL , 
					`comp_ca_uuid_aicountly2` BIGINT NOT NULL , 
					`bo_id` BIGINT DEFAULT 1
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpprofstf_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_id` BIGINT NOT NULL , 
					`comp_profsemp_uuid_aicountly1` BIGINT NOT NULL , 
					`comp_profsemp_uuid_aicountly2` BIGINT NOT NULL, 
					`comp_profsemp_uuid_aicountly3` BIGINT NOT NULL, 
					`comp_profsemp_uuid_aicountly4` BIGINT NOT NULL, 
					`comp_profsemp_uuid_aicountly5` BIGINT NOT NULL , 
					`bo_id` BIGINT DEFAULT 1 
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}


		$table = $this->comp_id.'_compatnnnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_acc_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,								 
					`comp_acct_name1` varchar(70)  NULL,
					`comp_acct_name2` varchar(70)  NULL,
					`comp_acct_email` varchar(100)  NULL,
					`comp_acct_mobile` varchar(20)  NULL,
					`comp_acct_wamobile` varchar(20) NULL,
					`bo_id` BIGINT DEFAULT 1,	
					PRIMARY KEY (`comp_acc_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpauditor_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_auditor_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,								  
					`comp_ca_name` varchar(70)  NULL,
					`comp_ca_name2` varchar(70)  NULL,
					`comp_ca_email` varchar(100)  NULL,
					`comp_ca_mobile` varchar(20)  NULL,
					`comp_ca_wamobile` varchar(20) NULL,
					`comp_ca_mrn` varchar(50) NULL,
					`comp_ca_frn_name` varchar(60) NULL,
					`comp_ca_frn` varchar(60) NULL,
					PRIMARY KEY (`comp_auditor_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_compbanknn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_bank_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`acc_bankname` varchar(30)  NULL,
					`acc_bank_accno` varchar(30)  NULL,
					`acc_bankifsc` varchar(20)  NULL,
					`acc_bank_branch` varchar(70)  NULL,
					`acc_bank_city` varchar(70)  NULL,
					`acc_bank_state` int NULL,
					`acc_bank_country` int  NULL,
					PRIMARY KEY (`acc_bank_id`),
					KEY `bank accounts indexing` (`comp_id`,`acc_bank_accno`,`acc_bank_branch`) USING BTREE
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_onloadchks_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`onloadchk_id` BIGINT NOT NULL AUTO_INCREMENT, 
					`onloadchk_type` VARCHAR(7) NOT NULL ,
					`voucher_txn_id` BIGINT NOT NULL , 
					`db_chk` VARCHAR(255) NOT NULL, 
					`tb_chk` VARCHAR(255) NOT NULL , 
					`domain` VARCHAR(255) NOT NULL, 
					`field_chk` VARCHAR(255) NOT NULL, 
					`chk_trigger` VARCHAR(255) NOT NULL, 
					`chk_trigger_action` TEXT NOT NULL, 
					`chk_crs_id` BIGINT NOT NULL , 
					`chk_status` INT(1) NOT NULL , 
					PRIMARY KEY (`onloadchk_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}



		return $errors;
	}

	function company_tax_tables($create_status=true,$drop_status=false) // 7 tables
	{
		$errors = [];

		$table = $this->comp_id.'_cmpgtcscat_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cmp_tax_cat_id` BIGINT NOT NULL AUTO_INCREMENT,
					`comp_id` BIGINT NOT NULL,
					`cmp_cat_igst_rate` DECIMAL(18,8) DEFAULT 0,
					`cmp_cat_cess_rate` DECIMAL(18,8) DEFAULT 0,
					`cmp_cat_wef` DATE DEFAULT NULL,
					`tax_cat_payer_status` INT DEFAULT 0, 
					`tax_cat_purpose` VARCHAR(50),
					KEY `parameters indexing` (`comp_id`) USING BTREE,
					PRIMARY KEY (`cmp_tax_cat_id`)
					
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmptaxcatm_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cmp_tax_cat_id` BIGINT NOT NULL AUTO_INCREMENT,
					`comp_id` BIGINT NOT NULL,
					`cmp_tax_cat_act` VARCHAR(10),
					`cmp_tax_cat_name` VARCHAR(100),
					`cmp_tax_cat_section` VARCHAR(100),
					`cmp_tax_cat_basis`  INT,
					`cmp_tax_cat_type` VARCHAR(50), 
					KEY `parameters indexing` (`comp_id`) USING BTREE,
					PRIMARY KEY (`cmp_tax_cat_id`)
					
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpgstcatn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cmp_tax_cat_id` BIGINT NOT NULL,
					`comp_id` BIGINT NOT NULL,
					`cmp_tax_cat_igst` DECIMAL(18,8) DEFAULT 0,
					`cmp_tax_cat_cess` DECIMAL(18,8) DEFAULT 0,					
					`cmp_tax_cat_wef` DATE DEFAULT NULL,
					`cmp_tax_short_code` VARCHAR(10) NOT NULL, 
					`tax_cat_id` BIGINT NOT NULL, 
					`cmp_tax_cat_cess_basis` VARCHAR(1) DEFAULT 1,
					KEY `parameters indexing` (`comp_id`) USING BTREE					
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpgwhtcat_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cmp_tax_cat_id` BIGINT NOT NULL AUTO_INCREMENT,
					`comp_id` BIGINT NOT NULL,
					`cmp_tax_cat_igst_rate` DECIMAL(18,8) DEFAULT 0,
					`cmp_tax_cat_cess_rate` DECIMAL(18,8) DEFAULT 0,
					`cmp_tax_cat_wef` DATE DEFAULT NULL,
					`cmp_tax_cat_payee_status` INT DEFAULT 0, 
					`cmp_tax_cat_purpose` VARCHAR(50),
					KEY `parameters indexing` (`comp_id`) USING BTREE,
					PRIMARY KEY (`cmp_tax_cat_id`)
					
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpitcscat_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cmp_tax_cat_id` BIGINT NOT NULL AUTO_INCREMENT,
					`comp_id` BIGINT NOT NULL,
					`cmp_tax_cat_payee_cat` INT DEFAULT 0,
					`cmp_tax_cat_cons_thres` VARCHAR(50),
					`cmp_tax_cat_rate_tin` DECIMAL(18,8) DEFAULT 0,
					`cmp_tax_cat_rate_no_tin`VARCHAR(50),
					`cmp_tax_sur_limit` VARCHAR(50),
					`cmp_tax_cat_wef` DATE DEFAULT NULL,
					`cmp_tax_cat_payer_status` VARCHAR(2),
					`cmp_tax_cat_purpose` VARCHAR(50) ,
					KEY `parameters indexing` (`comp_id`) USING BTREE,
					PRIMARY KEY (`cmp_tax_cat_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpiwhtcat_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cmp_tax_cat_id` BIGINT NOT NULL AUTO_INCREMENT,
					`comp_id` BIGINT NOT NULL,
					`cmp_tax_cat_payee_cat` INT DEFAULT 0,
					`cmp_tax_single_thres` VARCHAR(50),
					`cmp_tax_cat_cons_thres` DECIMAL(18,8) DEFAULT 0,
					`cmp_tax_cat_rate_tin` DECIMAL(18,8) DEFAULT 0,
					`cmp_tax_cat_rate_no_tin` VARCHAR(50),
					`cmp_tax_cat_surcharge` DECIMAL(18,8) DEFAULT 0,
					`cmp_tax_cat_sur_limit` VARCHAR(20),
					`cmp_tax_cat_wef`  DATE DEFAULT NULL,
					`cmp_tax_cat_payee_status` VARCHAR(2),
					`cmp_tax_cat_purpose` VARCHAR(50),
					KEY `parameters indexing` (`comp_id`) USING BTREE,
					PRIMARY KEY (`cmp_tax_cat_id`) 
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

			// confirm
		$table = $this->comp_id.'_taxcatmstn_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`tax_cat_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`tax_cat_type` int NOT NULL DEFAULT '0',
					`tax_cat_name` varchar(70) NOT NULL,
					`tax_cat_section` int NOT NULL DEFAULT '0',
					PRIMARY KEY (`tax_cat_id`),
					KEY `tax catg indexing` (`comp_id`,`tax_cat_type`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function branch_tables($create_status=true,$drop_status=false) // 1 tables
	{
		$errors = [];

		$table = $this->comp_id.'_hobomaster_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bo_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT NOT NULL , 
					`bo_name` VARCHAR(150) NOT NULL , 
					`bo_alias` VARCHAR(150) NULL , 
					`acc_grp_id` BIGINT NOT NULL , 
					`bo_opdate` DATE NULL , 
					`bo_cldate` DATE NULL , 
					`bo_add1` VARCHAR(150) NULL , 
					`bo_add2` VARCHAR(150) NULL , 
					`bo_city` VARCHAR(100) NULL , 
					`bo_state` INT(10) NULL , 
					`bo_country` INT(10) NULL , 
					`bo_pin` VARCHAR(20) NULL , 
					`bo_zone` INT(10) NULL, 
					`acc_id` BIGINT(10) NULL,
					`bo_ho` INT NULL DEFAULT 1,
					`bo_state_code` VARCHAR(5) NULL ,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`bo_id`), 
					INDEX `boindexing` (`comp_id`, `bo_name`, `bo_country`),
					INDEX `group indexing` (`acc_grp_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function print_tables($create_status=true,$drop_status=false) // 2 tables
	{
		$errors = [];

		$table = $this->comp_id.'_prntconfig_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`prntconfig_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`vch_series_id` bigint NOT NULL,
					`usr_config_id` bigint NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					`erpprevaln_label_id` bigint NOT NULL,
					`prntdsg_style` text NULL,
					`prntdsg_value` text NULL,
					PRIMARY KEY (`prntconfig_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_prntdesign_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				    `prntdsg_id` BIGINT NOT NULL AUTO_INCREMENT ,
					`prntconfig_id` bigint NOT NULL,
					`erpprevaln_label_id` bigint NOT NULL,
					`prntconfig_style` text NOT NULL,
					`prntconfig_label` VARCHAR(150) NOT NULL,
					PRIMARY KEY (`prntdsg_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function voucher_tables($create_status=true,$drop_status=false)	// 5 tables
	{
		$errors = [];

		$table = $this->comp_id.'_vhtxnconso_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`voucher_txn_id` BIGINT NOT NULL AUTO_INCREMENT ,
					`comp_id` BIGINT NOT NULL , 
					`comp_vch_series_id` BIGINT NOT NULL ,
					`comp_vch_no` BIGINT NOT NULL ,
					`voucher_type_id` INT NOT NULL ,
					`vch_subtype_id` INT NOT NULL ,
					`voucher_date` DATE NULL ,
					`mat_cent_id` int NOT NULL,
					`voucher_tag` VARCHAR(30) NOT NULL,
					`bo_id` BIGINT DEFAULT 1,
					`currency_id` BIGINT DEFAULT 1,
					`vch_bill_ref_no` VARCHAR(30) DEFAULT 0,
					PRIMARY KEY (`voucher_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		
		$table = $this->comp_id.'_vchaddinfo_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
								`txn_id` bigint NOT NULL,
								`voucher_txn_id` bigint NOT NULL,
								`vch_txn_drcr` varchar(5) NOT NULL,
								`vch_txn_incl` decimal(18,8) NOT NULL,
								`txn_type` varchar(8) NOT NULL COMMENT 'itm,bsd',
								KEY `voucher indexing` (`txn_id`,`voucher_txn_id`)
							  ) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		

		$table = $this->comp_id.'_comptxnmst_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`txn_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`voucher_txn_id` bigint NOT NULL,
					`comp_vch_series_id` bigint NOT NULL,
					`master_id` bigint NOT NULL,
					`master_id_type` VARCHAR(10) NOT NULL, 
					PRIMARY KEY (`txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_vhtxntrail_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_vch_txn_trail_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`voucher_txn_id` bigint NOT NULL,
					`txn_id` bigint NOT NULL,
					PRIMARY KEY (`comp_vch_txn_trail_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_long_narrn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`vch_txn_id` BIGINT NOT NULL ,
					`txn_id` BIGINT NOT NULL ,
					`vch_narr` VARCHAR(300) NULL ,
					INDEX `long narration indexing` (`vch_txn_id`, `txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_short_narr_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`vch_txn_id` BIGINT NOT NULL ,
					`txn_id` BIGINT NOT NULL ,
					`vch_short_narr` VARCHAR(150) NULL ,
					INDEX `short narration indexing` (`vch_txn_id`, `txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_acctgstsum_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`tgsmid` bigint NOT NULL AUTO_INCREMENT,
					`acc_id` bigint NOT NULL,
					`acc_igst_rate` decimal(18,8) NOT NULL,
					`acc_igst` decimal(18,8) NOT NULL,
					`acc_cgst_rate` decimal(18,8) NOT NULL,
					`acc_cgst` decimal(18,8) NOT NULL,
					`acc_sgst_rate` decimal(18,8) NOT NULL,
					`acc_sgst` decimal(18,8) NOT NULL,
					`acc_cess_rate` decimal(18,8) NOT NULL,
					`acc_cess` decimal(18,8) NOT NULL,
					`acc_nonadv_cess_rate` decimal(18,8) NOT NULL,
					`acc_nonadv_cess` decimal(18,8) NOT NULL,
					`acc_hsn_sac` varchar(20) NOT NULL,
					`taxable_amt` DECIMAL(18,8) NOT NULL,
					`vch_txn_id` bigint NOT NULL,
					`txn_id` bigint NOT NULL,
					`acc_type` varchar(5) NOT NULL COMMENT 'item or  bill sundry',
					`total_tax` decimal(18,8) NOT NULL,
					`acc_cess_basis` VARCHAR(1) NOT NULL,
					`cmp_tax_short_code` VARCHAR(20) NOT NULL,
					`acc_txn_date` DATE NOT NULL,					
					PRIMARY KEY (`tgsmid`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function voucher_series_tables($create_status=true,$drop_status=false)	// 5 tables
	{
		$errors = [];

		$table = $this->comp_id.'_vchseriesa_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`vch_series_auto_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`comp_vch_series_id` bigint NOT NULL,
					`comp_vch_renum_freq` varchar(100) NULL,
					`comp_vch_prefix` varchar(100) NULL,
					`comp_vch_suffix` varchar(100) NULL,
					`comp_vch_start` varchar(100) NULL,										
					`comp_vch_no_length` varchar(100) NULL,
					`comp_vch_no_padding` varchar(100) NULL,
					PRIMARY KEY (`vch_series_auto_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_vchseriesm_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`vch_series_manual_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`comp_vch_blank` varchar(100) NULL,
					`comp_vch_series_id` bigint NOT NULL,
					PRIMARY KEY (`vch_series_manual_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpvchseri_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_vch_series_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`comp_vch_series` varchar(100) NULL,
					`comp_vch_prefix` varchar(100) NULL,
					`voucher_type_id` int NOT NULL,
					`comp_vch_suffix` varchar(100) NULL,
					`comp_vch_method` varchar(10) NULL,
					`mst_base_id` BIGINT NOT NULL,
					`comp_bank_id` BIGINT NOT NULL DEFAULT '0',
					PRIMARY KEY (`comp_vch_series_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cmpvchtype_'.$this->fy_id;
		try{
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`voucher_type_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`comp_vch_type` varchar(100) NOT NULL,
					PRIMARY KEY (`voucher_type_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_vchsubtype_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`vch_subtype_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`comp_vch_subtype` varchar(100) NOT NULL,
					`voucher_type_id` int(10) NOT NULL,
					PRIMARY KEY (`vch_subtype_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;	
	}

	function currency_tables($create_status=true,$drop_status=false) // 2 tables
	{
		$errors = [];

		$table = $this->comp_id.'_compcurrcy_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_currency_id` INT NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`curr_name` varchar(100) NOT NULL,								  
					`curr_symbol` varchar(10) NOT NULL,
					`curr_string` varchar(50) NOT NULL,
					`curr_sub_string` varchar(50) NOT NULL,
					`curr_initial` VARCHAR(50) NOT NULL,
					`forex_type` varchar(1) NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`comp_currency_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_forexrates_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`forex_rate_id` BIGINT NOT NULL AUTO_INCREMENT,
					`comp_currency_id` INT NOT NULL,								  
					`rate_per_inr` DECIMAL(18,8) NOT NULL,
					`rate_per_fcy` DECIMAL(18,8) NOT NULL,
					`curr_date` DATE NULL,
					`forex_type` varchar(1) NOT NULL,	
					PRIMARY KEY (`forex_rate_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function account_tables($create_status=true,$drop_status=false) // 6 tables
	{
		$errors = [];

		$table = $this->comp_id.'_grpparentn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_grp_parent_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`acc_grp_parent` varchar(70) NOT NULL,
					`acc_grp_restrict` varchar(7) NOT NULL,
					PRIMARY KEY (`acc_grp_parent_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_acctgroupn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_grp_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`acc_grp_name` varchar(60)  NULL,
					`acc_grp_alias` varchar(60) NULL,
					`acc_grp_primary` varchar(1)  NULL,
					`acc_grp_parent_id` bigint  NULL,
					`under_acc_grp_id` bigint NOT NULL,
					`under_main_grp_id` bigint NOT NULL,
					`restrictions` varchar(7)  NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`acc_grp_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_acctmaster_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`acc_name` varchar(200)  NULL,
					`acc_name_alias` varchar(100)  NULL,
					`acc_name_print` varchar(100)  NULL,
					`acc_grp_id` bigint NOT NULL,
					`acc_grp_parent_id` bigint NOT NULL,
					`acc_symbol` varchar(30)  NULL,				  
					`acc_aadhar` varchar(20)  NULL,
					`acc_tan` varchar(20)  NULL,
					`acc_pan` varchar(20)  NULL,
					`acc_jurisd` varchar(30)  NULL,
					`acc_email` varchar(100)  NULL,
					`acc_tel` varchar(20)  NULL,
					`acc_wamobile` varchar(20)  NULL,
					`acc_mobile` varchar(20)  NULL,
					`acc_fax` varchar(20)  NULL,
					`acc_transport` varchar(50)  NULL,
					`acc_station` varchar(50)  NULL,
					`acc_distance` varchar(30)  NULL,
					`acc_iec` varchar(30)  NULL,
					`acc_prof_tax` varchar(30)  NULL,
					`acc_pref_bank` int  NULL,
					`itc_eligibility` int  NULL,
					`rcm_nature` int  NULL,
					`acc_const` varchar(30)  NULL,
					`vendor_code` varchar(20) NULL,
					`bo_id` BIGINT DEFAULT 1,
					`mst_base_id` BIGINT NOT NULL,
					`acc_short_code` VARCHAR(15) NOT NULL DEFAULT 'usraccn' COMMENT 'user defined account, sagstpd system generated account',
					PRIMARY KEY (`acc_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		
		$table = $this->comp_id.'_acctgstfcy_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				      `acctgstfcy_id` BIGINT NOT NULL AUTO_INCREMENT ,
					  `acc_id` BIGINT NOT NULL , 
					  `vch_txn_id` BIGINT NOT NULL ,
					  `txn_id` BIGINT NOT NULL ,
					  `acc_igst_fcy` DECIMAL(18,8) NOT NULL DEFAULT '0' ,
					  `acc_cgst_fcy` DECIMAL(18,8) NOT NULL DEFAULT '0' , 
					  `acc_sgst_fcy` DECIMAL(18,8) NOT NULL DEFAULT '0' , 
					  `acc_cess_fcy` DECIMAL(18,8) NOT NULL DEFAULT '0' ,
					  `total_tax_fcy` DECIMAL(18,8) NOT NULL DEFAULT '0' ,
					  `acc_type` varchar(10) NULL,
					  `acc_taxable_val_fcy` decimal(18,8) DEFAULT 0,
					  PRIMARY KEY (`acctgstfcy_id`),
					  INDEX `gstfcy index` (`vch_txn_id`, `txn_id`)) ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		

		$table = $this->comp_id.'_acctcontnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`account_contact_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`acc_cont_name1` varchar(70) NULL,
					`acc_cont_name2` varchar(70)  NULL,
					`acc_cont_mobile` varchar(20) NULL,
					`acc_cont_tel` varchar(20)  NULL,
					`acc_cont_wamobile` varchar(20)  NULL,
					`acc_cont_email` varchar(100)  NULL,
					`acc_cont_desig` varchar(50)  NULL,
					PRIMARY KEY (`account_contact_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_acctaddmst_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_id` bigint NOT NULL ,
					`comp_id` bigint NOT NULL,
					`acc_add1` varchar(150) NULL,
					`acc_add2` varchar(150) NULL,
					`acc_city` varchar(50) NULL,
					`acc_pin` varchar(15) NULL,
					`acc_state` int  NULL,
					`acc_country` int  NULL,
					`acc_state_code` varchar(6)  NULL	
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_accttxnoth_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_oth_txn_id` BIGINT NOT NULL AUTO_INCREMENT ,
					`comp_id` BIGINT NOT NULL , 
					`acc_oth_txn_date` DATE NOT NULL , 
					`acc_oth_txn_amount` DECIMAL(18,8) NOT NULL , 
					`acc_oth_txn_drcr` VARCHAR(5) NOT NULL ,
					`voucher_type_id` BIGINT NOT NULL ,
					`comp_vch_series_no` VARCHAR(100) NOT NULL , 
					`acc_id` BIGINT NOT NULL ,
					`voucher_txn_id` BIGINT NOT NULL , 
					`acc_oth_txn_status` VARCHAR(10) NOT NULL ,
					`bo_id` BIGINT DEFAULT 1 ,
					`txn_id` BIGINT NOT NULL, 
					`acc_oth_txn_duedate` DATE NOT NULL , 
					`acc_oth_txn_tag` VARCHAR(100) NOT NULL , 
					`acc_oth_txn_fcy` DECIMAL(18,8) DEFAULT 0 ,
					PRIMARY KEY (`acc_oth_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_acctcrsref_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acct_txn_id` BIGINT NOT NULL , 
					`comp_id` BIGINT NOT NULL ,
					`acct_crs_id_type` varchar(40) DEFAULT NULL,
					`txn_id` BIGINT NOT NULL , 
					`voucher_txn_id` BIGINT NOT NULL ,
					`bo_id` BIGINT DEFAULT 1 , 
					`acc_cross_ref_type` VARCHAR(20) NOT NULL ,
					`acc_cross_ref_data` VARCHAR(200) NOT NULL , 
					`acc_cross_logdate` DATE NOT NULL
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_accoppybal_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_id` BIGINT,
					`acc_op_bal` DECIMAL(18,8),
					`acc_py_bal` DECIMAL(18,8),
					`bo_id` BIGINT DEFAULT 1,
					KEY `accoppybal indexing` (`acc_id`) USING BTREE
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_memotxnnnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_txn_id` BIGINT NOT NULL AUTO_INCREMENT, 
					`comp_id` BIGINT NOT NULL , 
					`acc_txn_date` DATE NOT NULL , 
					`acc_txn_amount` DECIMAL(18,8) NOT NULL , 
					`acc_txn_drcr` VARCHAR(10) NOT NULL , 
					`comp_vch_series_no` VARCHAR(10) NOT NULL , 
					`acc_id` BIGINT NOT NULL , 
					`acc_type` VARCHAR(3) NOT NULL, 
					`acc_bal` DECIMAL(18,8) NOT NULL , 
					`voucher_txn_id` BIGINT NOT NULL , 
					`bo_id` BIGINT DEFAULT 1, 
					PRIMARY KEY (`acc_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}
    
	function vchaddinfo_tables($drop_status,$create_status){
		$table = $this->comp_id.'_vchaddinfo_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
								`txn_id` bigint NOT NULL,
								`voucher_txn_id` bigint NOT NULL,
								`vch_txn_drcr` varchar(5) NOT NULL,
								`vch_txn_incl` decimal(18,8) NOT NULL,
								`txn_type` varchar(8) NOT NULL COMMENT 'itm,bsd',
								KEY `voucher indexing` (`txn_id`,`voucher_txn_id`)
							  ) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
	}
	
	function memo_txn_tables($drop_status,$create_status){
		$table = $this->comp_id.'_memotxnnnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`acc_txn_id` BIGINT NOT NULL AUTO_INCREMENT, 
					`comp_id` BIGINT NOT NULL , 
					`acc_txn_date` DATE NOT NULL , 
					`acc_txn_amount` DECIMAL(18,8) NOT NULL , 
					`acc_txn_drcr` VARCHAR(10) NOT NULL , 
					`comp_vch_series_no` VARCHAR(10) NOT NULL , 
					`acc_id` BIGINT NOT NULL , 
					`acc_type` VARCHAR(3) NOT NULL, 
					`acc_bal` DECIMAL(18,8) NOT NULL , 
					`voucher_txn_id` BIGINT NOT NULL , 
					`bo_id` BIGINT DEFAULT 1, 
					PRIMARY KEY (`acc_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
	   $table = $this->comp_id.'_itemtrackn_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`tracking_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`tracking_no` VARCHAR(50) NOT NULL , 
					`item_id` BIGINT NOT NULL , 
					`txn_id` BIGINT NOT NULL , 
					`unit_id` BIGINT NOT NULL DEFAULT '0',
					PRIMARY KEY (`tracking_id`), 
					INDEX (`tracking_no`, `txn_id`, `item_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}	
		
	}
	
	function account_tax_tables($create_status=true,$drop_status=false) // 1 tables
	{
		$errors = [];

		$table = $this->comp_id.'_acctaxmstn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_id` bigint NOT NULL ,
					`acc_id` bigint NOT NULL ,
					`tax_cat_id` INT  NULL,
					`cmp_tax_cat_id` INT  NULL,					
					`acc_supply_type` INT  NULL,
					`acc_hsn_sac` varchar(50)  NULL,
					`acc_tax_short_code` varchar(10)  NULL
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_acctgstmst_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){ 

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				    `acctgstids` BIGINT NOT NULL AUTO_INCREMENT,
					`acc_id` bigint NOT NULL,
					`acc_gstin` varchar(20) NOT NULL,
					`acc_legal_name` varchar(100) NOT NULL,
					`acc_trade_name` varchar(100) NOT NULL,
					`acc_dealer_type` int NOT NULL,
					`acc_status` int NOT NULL,
					`acc_wef` date NOT NULL,
					`acc_rev_chgs` int NOT NULL DEFAULT 0,
					`acc_eco` ENUM('1','0') NOT NULL DEFAULT '0',
					KEY `acc indexing` (`acc_id`,`acc_rev_chgs`) USING BTREE,
					PRIMARY KEY (`acctgstids`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	

	function item_tables($create_status=true,$drop_status=false) // 16 tables
	{
		$errors = [];
		
		$table = $this->comp_id.'_itmgrpprnt_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`item_grp_parent_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`item_grp_parent` varchar(100) NOT NULL,
					PRIMARY KEY (`item_grp_parent_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemgrpmst_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`item_grp_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`item_grp_name` varchar(60) NOT NULL,
					`item_grp_alias` varchar(60) NOT NULL,
					`item_grp_primary` varchar(60) NOT NULL,
					`item_grp_parent_id` bigint(10) NULL,
					`under_acc_grp_id` bigint(10) NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`item_grp_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemcatmst_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`icatgms_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`item_cat` varchar(100) NOT NULL,
					`item_cat_alias` varchar(60) NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`icatgms_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemmaster_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`item_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`item_name` varchar(100) NULL,								  
					`item_cat` int NULL,
					`item_grp_id` int NOT NULL,
					`item_alias` varchar(60)  NULL,
					`item_print` varchar(100)  NULL,
					`item_upc` varchar(60)  NULL,
					`item_unit` int NOT NULL,
					`tax_id` int NOT NULL,								 
					`item_sales_acc` varchar(10)  NULL,
					`item_pur_acc` varchar(10)  NULL,
					`bar_code` varchar(100)  NULL,
					`item_sku` varchar(30)  NULL,								 
					`valmethod_id` INT(1) DEFAULT 1,
					`bo_id` BIGINT DEFAULT 1,
					`mst_base_id` BIGINT NOT NULL,
					`bom_id` BIGINT(20) DEFAULT 0,
					`bom_item_container` DECIMAL(18,4) NOT NULL DEFAULT '0',
					PRIMARY KEY (`item_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itmparamtr_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`paramtr_id` BIGINT NOT NULL AUTO_INCREMENT,
					`item_id` BIGINT NOT NULL,
					`paramtr_name` VARCHAR(100),
					PRIMARY KEY (`paramtr_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_paramtrval_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`paramtr_id` BIGINT NOT NULL,
					`paramtr_val` VARCHAR(100)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

			// Table not required
		$table = $this->comp_id.'_itmaltunit_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`item_alt_unit_id` BIGINT NOT NULL AUTO_INCREMENT,
					`item_id` BIGINT NOT NULL,
					`comp_id` BIGINT NOT NULL,
					`item_alt_conv_fact` VARCHAR(10) NOT NULL,
					`item_alt_conv_unit_id` INT(10) NOT NULL,
					`item_alt_units_val` VARCHAR(10) NOT NULL,
					PRIMARY KEY (`item_alt_unit_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemdimens_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`item_id` BIGINT,
					`item_dmns_id` INT(2),
					`item_dmns_val` VARCHAR(20),
					`item_dmns_unit_id` INT(10)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itmbatchmt_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`batch_id` BIGINT NOT NULL AUTO_INCREMENT,
					`batch_no` VARCHAR(100),
					`batch_expiry` DATE,
					`batch_mfr` DATE,
					`item_id` BIGINT NULL,
					`batch_qty` VARCHAR(50) NOT NULL DEFAULT '0',
					`batch_unit` INT NOT NULL DEFAULT '0',
					`mst_base_id` BIGINT NOT NULL,
					KEY `itmbatchmt indexing` (`batch_no`,`batch_expiry`,`batch_mfr`) USING BTREE,
					PRIMARY KEY (`batch_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

			// not found in draw sql
		$table = $this->comp_id.'_itmtagging_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`tagging_id` BIGINT NOT NULL AUTO_INCREMENT,
					`tagging_no` VARCHAR(100),
					PRIMARY KEY (`tagging_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemtrackn_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`tracking_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`tracking_no` VARCHAR(50) NOT NULL , 
					`item_id` BIGINT NOT NULL , 
					`txn_id` BIGINT NOT NULL , 
					PRIMARY KEY (`tracking_id`), 
					INDEX (`tracking_no`, `txn_id`, `item_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_iteminfonn_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`item_id` int NOT NULL,
					`iteminfo_line` varchar(100) NULL,
					`iteminfo_val` varchar(100) NULL,
					`iteminfo_label` varchar(100) NULL
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemtxnoth_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`item_oth_txn_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT NOT NULL ,
					`item_oth_txn_date` DATE NOT NULL , 
					`item_oth_txn_amount` DECIMAL(18,8) NOT NULL ,
					`item_oth_txn_drcr` VARCHAR(10) NOT NULL , 
					`item_oth_txn_qty` VARCHAR(20) NOT NULL ,
					`comp_vch_series_no` VARCHAR(50) NOT NULL ,
					`item_id` BIGINT NOT NULL ,
					`item_unit` INT NOT NULL , 
					`voucher_txn_id` BIGINT NOT NULL ,
					`bo_id` BIGINT DEFAULT 1 , 
					`txn_id` BIGINT NOT NULL , 
					`mat_cent_id` BIGINT NOT NULL , 
					`voucher_type_id` BIGINT NOT NULL , 
					`item_oth_txn_tag` VARCHAR(50) NOT NULL,
					`batch_id` BIGINT NOT NULL DEFAULT '0',
					`item_oth_txn_fcy` DECIMAL(18,8) DEFAULT 0 ,
					PRIMARY KEY (`item_oth_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemcrsref_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`itm_crs_id` BIGINT NOT NULL ,
					`itm_crs_id_type` varchar(100) DEFAULT NULL, 
					`comp_id` BIGINT NOT NULL ,
					`item_id` bigint NOT NULL,
					`txn_id` BIGINT NOT NULL , 
					`voucher_txn_id` BIGINT NOT NULL ,
					`bo_id` BIGINT DEFAULT 1 , 
					`item_cross_ref_type` VARCHAR(20) NOT NULL ,
					`item_cross_ref_data` VARCHAR(100) NOT NULL , 
					`item_cross_logdate` DATE NOT NULL
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itmoppybal_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				    `oppybal_id` BIGINT NOT NULL AUTO_INCREMENT,
					`item_id` BIGINT,
					`comp_id` BIGINT,
					`item_unit` INT(10),
					`op_bal_qty` VARCHAR(100),
					`py_bal_qty` VARCHAR(100),
					`batch_id` BIGINT NOT NULL DEFAULT '0',
					`mat_cent_id` BIGINT DEFAULT 0,
					`bo_id` BIGINT DEFAULT 1,					
					PRIMARY KEY (`oppybal_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itmoppyval_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				    `op_valuation_id` BIGINT NOT NULL AUTO_INCREMENT,
					`item_id` BIGINT NOT NULL ,  
					`comp_id` BIGINT NOT NULL , 								 
					`item_unit` BIGINT NOT NULL ,
					`op_bal_val` DECIMAL(18,8) ,
					`py_bal_val` DECIMAL(18,8),
					`method_id` INT(1)	,
					`mat_cent_id` BIGINT DEFAULT 0,
					`bo_id` BIGINT DEFAULT 1,
					`batch_id` BIGINT DEFAULT 0,
					PRIMARY KEY (`op_valuation_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function unit_tables($create_status=true,$drop_status=false) // 1 tables
	{
		$errors = [];

		$table = $this->comp_id.'_itmunitmst_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`unit_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`item_unit` varchar(30) NOT NULL,
					`item_unit_alias` varchar(30) NOT NULL,
					`item_unit_print` varchar(30) NOT NULL,
					`item_unit_uqc` varchar(30) NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`unit_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		return $errors;
	}

	function item_tax_tables($create_status=true,$drop_status=false) // 2 tables
	{
		$errors = [];

		$table = $this->comp_id.'_itemvalmst_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`itemvalmst_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`item_id` bigint NOT NULL,
					`item_mrp` DECIMAL(18,8) NOT NULL,
					PRIMARY KEY (`itemvalmst_id`),
					KEY `value mst index` (`comp_id`,`item_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_itemtaxmst_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`comp_id` bigint NOT NULL,
					`item_id` bigint NOT NULL,
					`item_supply_type` INT NOT NULL,
					`item_hsn_sac` varchar(100) NULL,	
					`tax_cat_id` int NULL,
					`cmp_tax_cat_id` int NULL,					
					`item_tax_short_code` varchar(10)  NULL	
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function barcode_tables($create_status=true,$drop_status=false) // 3 tables
	{
		$errors = [];

		$table = $this->comp_id.'_barcodemst_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`barcode_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`barcode` VARCHAR(50) NOT NULL , 
					`barcode_standard` VARCHAR(50) NOT NULL , 
					`item_id` BIGINT  NULL ,
					`unit_id` BIGINT  NULL,
					`batch_id` BIGINT NULL,
					`tracking_id` BIGINT NULL,
					`barcode_wef` DATE NULL , 
					`barcode_expiry` DATE NULL , 
					`barcode_status` VARCHAR(1) NOT NULL DEFAULT 1 ,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`barcode_id`), 
					INDEX `barcodeindexing` (`barcode_standard`, `barcode_status`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_labelmastr_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`label_id` BIGINT NOT NULL AUTO_INCREMENT,
					`label_name` VARCHAR(100),
					`label_desc_id` INT(10),
					`label_size_height` VARCHAR(10),
					`label_size_width` VARCHAR(10),
					`label_size_length` VARCHAR(10),
					`label_standard` VARCHAR(30),
					`label_font_type` VARCHAR(20),
					`label_font_size` VARCHAR(10),
					`label_expiry` DATE,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`label_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_labeldescn_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`labeldesc_id` BIGINT NOT NULL AUTO_INCREMENT,
					`labeldesc_line` VARCHAR(150),
					`labeldesc_val` VARCHAR(50),
					PRIMARY KEY (`labeldesc_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function consignment_tables($create_status=true,$drop_status=false) // 11 tables
	{
		$errors = [];

		$table = $this->comp_id.'_pcklistmst_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`list_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`list_name` VARCHAR(150) NULL , 								 
					`comp_id` BIGINT NOT NULL ,
					`list_delivery` TEXT ,
					`list_status` VARCHAR(10),
					`list_consignee` VARCHAR(30),
					`list_type` VARCHAR(10),
					`list_against_ref` VARCHAR(30),
					`mc_id` BIGINT DEFAULT 0, 	
					INDEX `pcklistmst indexing` (`list_name`, `comp_id`,`list_type`,`list_consignee`),
					PRIMARY KEY (`list_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_listcumast_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cu_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`list_id` BIGINT NOT NULL ,
					`cu_label` VARCHAR(70) ,
					`cu_unit` BIGINT NOT NULL,
					`list_level_id` BIGINT NOT NULL,	
					INDEX `listcumast indexing` (`list_id`, `cu_unit`,`list_level_id`),
					PRIMARY KEY (`cu_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cupackingn_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`packing_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`list_id` BIGINT NOT NULL ,
					`cu_id` BIGINT NOT NULL,
					`cu_unit` BIGINT NOT NULL,
					`list_level_id` BIGINT NOT NULL,	
					`cu_status` VARCHAR(10),
					`pck_status` VARCHAR(10),
					INDEX `cupackingn indexing` (`list_id`,`cu_id`, `cu_unit`,`list_level_id`),
					PRIMARY KEY (`packing_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_pcklistqty_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`list_id` BIGINT NOT NULL , 
					`item_id` BIGINT NOT NULL , 								 
					`item_unit` BIGINT NOT NULL ,
					`item_qty_available` VARCHAR(30) ,
					`item_qty_initial` VARCHAR(30),	
					INDEX `pcklistqty indexing` (`list_id`, `item_id`,`item_unit`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_listpacked_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`list_id` BIGINT NOT NULL , 
					`item_id` BIGINT NOT NULL , 								 
					`item_unit` BIGINT NOT NULL ,
					`item_qty_packed` VARCHAR(30) ,
					`cu_id` BIGINT NOT NULL,
					`list_level_id` BIGINT NOT NULL,	
					INDEX `listpacked indexing` (`list_id`, `item_id`,`item_unit`,`cu_id`,`list_level_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_leveltrack_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`packing_id` BIGINT NOT NULL , 
					`list_id` BIGINT NOT NULL , 								 
					`nxt_packing_id` BIGINT NOT NULL ,
					`prev_packing_id` BIGINT NOT NULL,								 
					INDEX `listpacked indexing` (`packing_id`, `list_id`,`nxt_packing_id`,`prev_packing_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}



		$table = $this->comp_id.'_itemlabeln_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`label_id` BIGINT,
					`item_id` BIGINT,
					`unit_id` BIGINT,
					`batch_id` BIGINT,
					`tagging_id` BIGINT,
					`pcklist_id` BIGINT,
					KEY `itemlabeln indexing` (`item_id`,`unit_id`,`batch_id`,`tagging_id`,`pcklist_id`) USING BTREE
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_pckglistnn_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`list_id` BIGINT NOT NULL AUTO_INCREMENT,
					`list_name` VARCHAR(150),
					`comp_id` BIGINT,
					`list_delivery` DATE,
					`list_status` VARCHAR(10),
					`list_consignee` VARCHAR(30),
					`list_type` VARCHAR(20),
					`list_against_ref` VARCHAR(20),
					`list_level1_unitid` BIGINT,
					`list_level2_unitid` BIGINT,
					`list_level3_unitid` BIGINT,
					KEY `pckglistnn index` (`comp_id`,`list_type`) USING BTREE,
					PRIMARY KEY (`list_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_pckqtylist_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`list_id` BIGINT NOT NULL AUTO_INCREMENT,
					`item_id` BIGINT,
					`item_uom_id` BIGINT,
					`item_qty_available` VARCHAR(20),
					`item_qty_initial` VARCHAR(20),
					KEY `pckqtylist index` (`item_id`,`item_uom_id`) USING BTREE,
					PRIMARY KEY (`list_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_pcklistbeg_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`list_id` BIGINT,
					`pcklist_id` BIGINT,
					`item_id` BIGINT,
					`item_qty_packed` VARCHAR(30),
					`list_cu_label` VARCHAR(100),
					KEY `pcklistbeg index` (`list_id`,`pcklist_id`,`item_id`) USING BTREE
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_pcklistext_'.$this->fy_id;  	
		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`list_id` BIGINT,
					`pcklist_id` BIGINT,
					`item_id` BIGINT,
					`item_qty_packed` VARCHAR(30),
					`list_cu_label` VARCHAR(100),
					KEY `pcklistbeg index` (`list_id`,`pcklist_id`,`item_id`) USING BTREE
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function mc_tables($create_status=true,$drop_status=false) // 3 tables
	{
		$errors = [];

		$table = $this->comp_id.'_mcgrpmstnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`mc_grp_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint(10) NOT NULL,
					`mc_grp_name` varchar(60)  NULL,
					`mc_alias` varchar(60)  NULL,
					`mc_primary` bigint NULL,
					`under_mc_grp_id` bigint NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`mc_grp_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_mcmasternn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`mat_cent_id` bigint NOT NULL AUTO_INCREMENT,
					`comp_id` bigint NOT NULL,
					`mat_cent_name` varchar(60) NOT NULL,
					`mat_cent_grp_id` int NOT NULL,
					`mat_cent_alias` varchar(100) NOT NULL,
					`mat_cent_print` varchar(100) NOT NULL,
					`mat_cent_add1` varchar(100) NOT NULL,
					`mat_cent_add2` varchar(100) NOT NULL,
					`mat_cent_city` varchar(70) NOT NULL,
					`mat_cent_pin` varchar(20) NOT NULL,
					`mat_cent_state` int NOT NULL,
					`mat_cent_country` int NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`mat_cent_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function bill_sundry_tables($create_status=true,$drop_status=false) // 2 tables
	{
		$errors = [];

		$table = $this->comp_id.'_billsundry_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bill_sundry_id` BIGINT NOT NULL AUTO_INCREMENT, 
					`bill_sundry_name` varchar(80) NOT NULL, 
					`bill_sundry_alias` varchar(80) DEFAULT NULL, 
					`sundry_print_name` varchar(80) DEFAULT NULL,
					`sundry_nature` varchar(30) DEFAULT NULL, 
					`sundry_def_value` decimal(18,8) DEFAULT NULL, 
					`sundry_type` varchar(2) DEFAULT NULL,
					`sundry_calc_base` varchar(1) DEFAULT NULL,
					`sundry_calc_fed` varchar(1) DEFAULT NULL, 
					`acc_grp_id` int NOT NULL, 
					`acc_grp_parent_id` int NOT NULL, 
					`bo_id` BIGINT DEFAULT 1,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`bill_sundry_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_bsdoppybal_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bill_sundry_id` BIGINT,
					`bsd_op_bal` DECIMAL(18,8),
					`bsd_py_bal` DECIMAL(18,8),
					`bo_id` BIGINT DEFAULT 1,
					KEY `bsdoppybal indexing` (`bill_sundry_id`) USING BTREE
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function bill_sundry_tax_tables($create_status=true,$drop_status=false) // 1 tables
	{
		$errors = [];

		$table = $this->comp_id.'_bdstaxmstn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bill_sundry_id` BIGINT NOT NULL, 
					`tax_cat_id` BIGINT NOT NULL,
					`cmp_tax_cat_id` BIGINT NOT NULL,					 
					`bill_supply_type` varchar(1), 
					`bill_hsn_sac` varchar(100) ,
					`bill_tax_short_code` varchar(10) DEFAULT NULL,
					`bill_tax_account` varchar(10) DEFAULT 0,
					`bill_input_output` varchar(1) DEFAULT NULL ,
					`bill_tax_type` INT DEFAULT 0
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function bill_by_bill_tables($create_status=true,$drop_status=false) // 2 tables 
	{
		$errors = [];

		$table = $this->comp_id.'_billmaster_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bills_ref_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`bills_ref_name` VARCHAR(255) NOT NULL ,
					`acc_id` BIGINT NOT NULL , 
					`bills_status` VARCHAR(255) NOT NULL , 
					`bo_id` BIGINT DEFAULT 1 , 
					`bill_op_bal` DECIMAL(18,8) NOT NULL , 
					`bill_py_bal` DECIMAL(18,8) NOT NULL ,
					`bill_due_date` DATE NOT NULL ,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`bills_ref_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_billsoppyn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bills_ref_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`bo_id` BIGINT DEFAULT 1,
					`bills_op_bal` DECIMAL(18,8) NOT NULL , 
					`bills_py_bal` DECIMAL(18,8) NOT NULL ,
					UNIQUE (`bills_ref_id`, `bo_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_billstxnnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bills_txn_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`bills_ref_id` BIGINT NOT NULL ,
					`comp_id` BIGINT NOT NULL , 
					`acc_id` BIGINT NOT NULL , 
					`voucher_txn_id` BIGINT NOT NULL , 
					`voucher_type_id` BIGINT NOT NULL ,
					`comp_vch_series_id` BIGINT NOT NULL , 
					`bills_txn_date` DATE NOT NULL , 
					`bills_txn_drcr` VARCHAR(255) NOT NULL , 
					`bills_txn_amt` DECIMAL(18,8) NOT NULL , 
					`bills_txn_bal` DECIMAL(18,8) NOT NULL , 
					`bo_id` BIGINT DEFAULT 1,
					PRIMARY KEY (`bills_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function cost_center_tables($create_status=true,$drop_status=false) // 2 tables
	{
		$errors = [];

		$table = $this->comp_id.'_costctgrup_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cc_grp_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT NOT NULL , 
					`cc_grp_name` VARCHAR(255) NOT NULL ,
					`cc_grp_alias` VARCHAR(255) NOT NULL , 
					`under_cc_grp_id` BIGINT NOT NULL ,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`cc_grp_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_costctmstr_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cc_id` INT NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT NOT NULL , 
					`cc_name` VARCHAR(255) NOT NULL , 
					`cc_alias` VARCHAR(255) NOT NULL , 
					`cc_print` VARCHAR(255) NOT NULL , 
					`cc_grp_id` BIGINT NOT NULL , 
					`bo_id` BIGINT DEFAULT 1 , 
					`cc_op_bal` DECIMAL(18,8) NOT NULL , 
					`cc_op_drcr` VARCHAR(5) NOT NULL, 
					`cc_py_bal` DECIMAL(18,8) NOT NULL , 
					`cc_py_drcr` VARCHAR(5) NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`cc_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_costctoppy_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cc_id` INT NOT NULL AUTO_INCREMENT ,  
					`bo_id` BIGINT DEFAULT 1 , 
					`cc_op_bal` DECIMAL(18,8) NOT NULL , 
					`cc_py_bal` DECIMAL(18,8) NOT NULL ,
					UNIQUE (`cc_id`, `bo_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_costcttxnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cc_txn_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`cc_id` BIGINT NOT NULL , 
					`comp_id` BIGINT NOT NULL , 
					`acc_id` BIGINT NOT NULL ,
					`voucher_txn_id` BIGINT NOT NULL , 
					`voucher_type_id` BIGINT NOT NULL , 
					`comp_vch_series_id` BIGINT NOT NULL , 
					`cc_txn_date` DATE NOT NULL , 
					`cc_txn_drcr` VARCHAR(10) NOT NULL , 
					`cc_txn_amt` DECIMAL(18,8) NOT NULL , 
					`cc_txn_bal` DECIMAL(18,8) NOT NULL ,
					`bo_id` BIGINT NOT NULL DEFAULT 1,
					`acc_type` VARCHAR(3) NOT NULL ,	
					PRIMARY KEY (`cc_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function bill_of_material_tables($create_status=true,$drop_status=false) // 4 tables
	{
		$errors = [];

		$table = $this->comp_id.'_billofmatn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bom_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`bom_name` VARCHAR(100) NOT NULL ,
					`mst_base_id` BIGINT NOT NULL, 
					`bom_consm_pricing` VARCHAR(5) NOT NULL DEFAULT 'f' COMMENT 'f fixed, a auto pricing' ,
					`bom_grp_id` BIGINT NULL ,
					PRIMARY KEY (`bom_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		$table = $this->comp_id.'_bomgrpmstn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
				`bom_grp_id` BIGINT NOT NULL AUTO_INCREMENT , 
				`bom_grp_name` VARCHAR(50) NOT NULL , 
				`bom_grp_alias` VARCHAR(50) NOT NULL ,
				`bom_grp_primary` INT NOT NULL , 
				`under_bom_grp_id` BIGINT NOT NULL , 
				`mst_base_id` BIGINT NOT NULL , 
				PRIMARY KEY (`bom_grp_id`), 
				INDEX `bom index` (`bom_grp_name`, `under_bom_grp_id`)) 
				ENGINE = InnoDB;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_bominputnn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bom_input_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`bom_id` BIGINT NOT NULL ,
					`item_id` BIGINT NOT NULL , 
					`item_uom` VARCHAR(20) NOT NULL , 
					`item_qty` VARCHAR(20) NOT NULL ,
					`item_amt` DECIMAL(18,8) NOT NULL , 
					PRIMARY KEY (`bom_input_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_bomoutputn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bom_output_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`bom_id` BIGINT NOT NULL , 
					`item_id` BIGINT NOT NULL , 
					`item_uom` VARCHAR(30) NOT NULL , 
					`item_qty` VARCHAR(30) NOT NULL ,
					`item_usr_amt` DECIMAL(18,8) NOT NULL , 
					`item_amt` DECIMAL(18,8) NOT NULL , 
					`bom_output_type` VARCHAR(1) NOT NULL ,
					PRIMARY KEY (`bom_output_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_bomaddcost_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bom_addcost_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`bom_id` BIGINT NOT NULL ,
					`bom_addcost_acc_id` BIGINT NOT NULL ,
					`bom_addcost_type` VARCHAR(1) NOT NULL ,
					`bom_addcost_amt` DECIMAL(18,8) NOT NULL ,
					PRIMARY KEY (`bom_addcost_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	function project_tables($create_status=true,$drop_status=false) // 7 tables
	{
		$errors = [];

		$table = $this->comp_id.'_projectgrp_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`project_grp_id` INT NOT NULL AUTO_INCREMENT,
					`comp_id` BIGINT NOT NULL ,
					`project_grp_name` varchar(100) NOT NULL, 
					`project_grp_alias` varchar(100) NOT NULL,
					`under_project_grp_id` INT NOT NULL,
					`under_main_grp_id` INT NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`project_grp_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_projectmst_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`project_id` BIGINT NOT NULL AUTO_INCREMENT,
					`comp_id` BIGINT NOT NULL ,
					`project_name` varchar(100) NOT NULL, 
					`project_alias` varchar(100) NOT NULL,
					`project_print` varchar(100) NOT NULL,
					`project_grp_id` INT NOT NULL,
					`mst_base_id` BIGINT NOT NULL,
					PRIMARY KEY (`project_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_prjoppybal_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`project_id` BIGINT NOT NULL,
					`project_op_bal` DECIMAL(18,8) NOT NULL ,
					`project_py_bal` DECIMAL(18,8) NOT NULL,
					`project_bal_type` VARCHAR(3) NOT NULL, 
					`bo_id` BIGINT NOT NULL,
					UNIQUE (`project_id`,`bo_id`,`project_bal_type`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_projexptxn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`proj_txn_id` BIGINT NOT NULL AUTO_INCREMENT,
					`project_id` BIGINT NOT NULL,
					`comp_id` BIGINT NOT NULL ,
					`acc_id` BIGINT NOT NULL,
					`acc_type` VARCHAR(3) NOT NULL,
					`bo_id` INT NOT NULL,
					`voucher_txn_id` BIGINT NOT NULL,
					`voucher_type_id` BIGINT NOT NULL,
					`comp_vch_series_id` BIGINT NOT NULL,
					`proj_txn_date` DATE  NULL,
					`proj_txn_drcr` varchar(1) NOT NULL, 
					`proj_txn_amt` DECIMAL(18,8) NOT NULL,
					`proj_txn_bal` DECIMAL(18,8) NOT NULL,
					PRIMARY KEY (`proj_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_projrevtxn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`proj_txn_id` BIGINT NOT NULL AUTO_INCREMENT,
					`project_id` BIGINT NOT NULL,
					`comp_id` BIGINT NOT NULL ,
					`acc_id` BIGINT NOT NULL,
					`acc_type` VARCHAR(3) NOT NULL,
					`bo_id` INT NOT NULL,
					`voucher_txn_id` BIGINT NOT NULL,
					`voucher_type_id` BIGINT NOT NULL,
					`comp_vch_series_id` BIGINT NOT NULL,
					`proj_txn_date` DATE  NULL,
					`proj_txn_drcr` varchar(1) NOT NULL, 
					`proj_txn_amt` DECIMAL(18,8) NOT NULL,
					`proj_txn_bal` DECIMAL(18,8) NOT NULL,
					PRIMARY KEY (`proj_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_prjliabtxn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`proj_txn_id` BIGINT NOT NULL AUTO_INCREMENT,
					`project_id` BIGINT NOT NULL,
					`comp_id` BIGINT NOT NULL ,
					`acc_id` BIGINT NOT NULL,
					`acc_type` VARCHAR(3) NOT NULL,
					`bo_id` INT NOT NULL,
					`voucher_txn_id` BIGINT NOT NULL,
					`voucher_type_id` BIGINT NOT NULL,
					`comp_vch_series_id` BIGINT NOT NULL,
					`proj_txn_date` DATE  NULL,
					`proj_txn_drcr` varchar(1) NOT NULL, 
					`proj_txn_amt` DECIMAL(18,8) NOT NULL,
					`proj_txn_bal` DECIMAL(18,8) NOT NULL,
					PRIMARY KEY (`proj_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_projasttxn_'.$this->fy_id;  	
		try{

			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){

				$this->db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`proj_txn_id` BIGINT NOT NULL AUTO_INCREMENT,
					`project_id` BIGINT NOT NULL,
					`comp_id` BIGINT NOT NULL ,
					`acc_id` BIGINT NOT NULL,
					`acc_type` VARCHAR(3) NOT NULL,
					`bo_id` INT NOT NULL,
					`voucher_txn_id` BIGINT NOT NULL,
					`voucher_type_id` BIGINT NOT NULL,
					`comp_vch_series_id` BIGINT NOT NULL,
					`proj_txn_date` DATE  NULL,
					`proj_txn_drcr` varchar(1) NOT NULL, 
					`proj_txn_amt` DECIMAL(18,8) NOT NULL,
					`proj_txn_bal` DECIMAL(18,8) NOT NULL,
					PRIMARY KEY (`proj_txn_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

	//-----------------------
	function account_txn_tables($acc_id,$create_status=true,$drop_status=false) // 1 for each
	{
		$errors = [];

		$table = $this->comp_id.'_accnttxnnn_'.$acc_id.'_'.$this->fy_id;

		try{
			
			if($drop_status){
				$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table.'` (
					`acc_txn_id` bigint NOT NULL AUTO_INCREMENT , 
					`comp_id` BIGINT(10) NOT NULL ,
					`acc_txn_date` DATE  NULL , 
					`acc_txn_amount` DECIMAL(18,8)  NULL ,
					`acc_txn_drcr` VARCHAR(10)  NULL , 
					`comp_vch_series_no` VARCHAR(100) NULL,
					`acc_id` BIGINT(10) NOT NULL ,
					`txn_id` BIGINT(10) NOT NULL, 
					`voucher_type_id` BIGINT(10) NOT NULL , 
					`acc_bal` DECIMAL(18,8) NULL,
					`voucher_txn_id` BIGINT(10) NULL, 
					`bo_id` BIGINT DEFAULT 1,
					`posted_on` DATETIME NOT NULL,
					`acc_txn_fcy` DECIMAL(18,8) DEFAULT 0 ,
					PRIMARY KEY (`acc_txn_id`)
				) ENGINE = InnoDB;');
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		return $errors;
	}

  function bill_sundry_txn_tables($bsd_id,$create_status=true,$drop_status=false) // 1 for each
  {
  	$errors = [];

  	try{

  		$table = $this->comp_id.'_sundrytxnn_'.$bsd_id.'_'.$this->fy_id;

  		if($drop_status){
  			$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
  		}

  		if($create_status){
  			$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table.'` (
  				`sundry_txn_id` BIGINT NOT NULL AUTO_INCREMENT,
  				`comp_id` BIGINT NOT NULL,
  				`sundry_txn_date` DATE DEFAULT NULL,
  				`sundry_txn_amount` DECIMAL(18,8) NOT NULL,
  				`sundry_txn_drcr` VARCHAR(2) NOT NULL,
  				`sundry_txn_narr` TEXT ,
  				`comp_vch_name` VARCHAR(50) NOT NULL,
  				`comp_vch_series_no` VARCHAR(30)  NOT NULL,
  				`bill_sundry_id` BIGINT NOT NULL,
  				`sundry_bal` DECIMAL(18,8) NOT NULL,
  				`voucher_txn_id` BIGINT NOT NULL,
  				`voucher_type_id` BIGINT NOT NULL,
  				`txn_id` BIGINT DEFAULT NULL,
  				`sundry_tag_rate` DECIMAL(18,8) NOT NULL,
  				`bo_id` BIGINT DEFAULT 1 ,
				`sundry_txn_fcy` DECIMAL(18,8) DEFAULT 0,
  				PRIMARY KEY (`sundry_txn_id`)
  			) ENGINE = InnoDB;');
  		}
  	}
  	catch (\Exception $e) {
  		$errors[] = $e->getMessage();
  	}

  	return $errors;
  }

  function item_txn_tables($item_id,$create_status=true,$drop_status=false) // 2 for each
  {
  	$errors = [];

  	$table = $this->comp_id.'_itemtxnnnn_'.$item_id.'_'.$this->fy_id;

  	try{
  		
  		if($drop_status){
  			$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
  		}

  		if($create_status){
  			$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table.'` (
  				`item_txn_id` BIGINT AUTO_INCREMENT,
  				`comp_id` BIGINT,
  				`item_txn_date` DATE,
  				`item_txn_amount` DECIMAL(18,8),
  				`item_txn_drcr` VARCHAR(5),
  				`item_txn_qty` DECIMAL(18,4) NOT NULL,
  				`item_id` BIGINT,
  				`voucher_txn_id` BIGINT,
  				`bo_id` BIGINT DEFAULT 1,
  				`txn_id` BIGINT,
  				`mat_cent_id` BIGINT,
  				`voucher_type_id` INT(5),
  				`batch_id` BIGINT NOT NULL,
  				`tagging_id` BIGINT,
  				`item_unit` INT NOT NULL,
  				`item_bal_qty` DECIMAL(18,4) NOT NULL,
  				`item_avail` INT NOT NULL DEFAULT "1",
				`item_txn_fcy` DECIMAL(18,8) DEFAULT 0,
  				PRIMARY KEY (`item_txn_id`)
  			) ENGINE = InnoDB;');
  		}
  	}
  	catch (\Exception $e) {
  		$errors[] = $e->getMessage();
  	}

  	$table = $this->comp_id.'_itemtxnval_'.$item_id.'_'.$this->fy_id;

  	try{
  		
  		if($drop_status){
  			$this->db->query('DROP TABLE IF EXISTS `'.$table.'` ');
  		}

  		if($create_status){
  			$this->db->query('CREATE TABLE IF NOT EXISTS `'.$table.'` (
  				`valuation_id` BIGINT NOT NULL AUTO_INCREMENT, 
  				`item_id` BIGINT NOT NULL, 
  				`unit_id` BIGINT NOT NULL, 
  				`mat_cent_id` BIGINT NOT NULL,
  				`item_txn_id` BIGINT NOT NULL,
  				`voucher_txn_id` BIGINT NOT NULL,
  				`voucher_date` DATE NULL,
  				`method_id` INT(5) NOT NULL,
  				`item_value` DECIMAL(18,4) NOT NULL,
  				`profit` DECIMAL(18,8) NOT NULL,
  				`profit_string` VARCHAR(255) NOT NULL,
  				PRIMARY KEY (`valuation_id`)
  			) ENGINE = InnoDB;');
  		}
  	}
  	catch (\Exception $e) {
  		$errors[] = $e->getMessage();
  	}

  	return $errors;
  }

  //-------------------------------

  function account_master_txn_tables($create_status=true,$drop_status=false)
  {
  	$errors = [];
  	$data = [];

  	try{
  		$table = $this->comp_id.'_acctmaster_'.$this->fy_id; 	
  		$data = $this->db->table($table)
  		->select('acc_id')
  		->get()->getResultArray();
  	}
  	catch (\Exception $e) {
  		$errors[] = $e->getMessage();
  	}
	  	
  	foreach ($data as $key => $value) {

  		$response = $this->account_txn_tables($value['acc_id'],$create_status,$drop_status);
  		foreach ($response as $error) { $errors[] = $error; }
  	}

  	return $errors;
  }

  function bill_sundry_master_txn_tables($create_status=true,$drop_status=false)
  {
  	$errors = [];
  	$data = [];
  	
  	try{
  		$table = $this->comp_id.'_billsundry_'.$this->fy_id;
  		$data = $this->db->table($table)
  		->select('bill_sundry_id')
  		->get()->getResultArray();
  	}
  	catch (\Exception $e) {
  		$errors[] = $e->getMessage();
  	}

  	foreach ($data as $key => $value) {

  		$response = $this->bill_sundry_txn_tables($value['bill_sundry_id'],$create_status,$drop_status);
  		foreach ($response as $error) { $errors[] = $error; }
  	}


  	return $errors;
  }

  function item_master_txn_tables($create_status=true,$drop_status=false)
  {
  	$errors = [];
  	$data = [];

  	try{
  		$table = $this->comp_id.'_itemmaster_'.$this->fy_id;
  		$data = $this->db->table($table)
  		->select('item_id')
  		->get()->getResultArray();
  	}
  	catch (\Exception $e) {
  		$errors[] = $e->getMessage();
  	}

  	foreach ($data as $key => $value) {

  		$response = $this->item_txn_tables($value['item_id'],$create_status,$drop_status);
  		foreach ($response as $error) { $errors[] = $error; }
  		
  	}

  	return $errors;
  }


	//------------------------------------------------

  function set_branch_data($data=[], $truncate=FALSE)
  {
  	$errors = [];

  	$table = $this->comp_id.'_hobomaster_'.$this->fy_id; 

  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}
	
	try{
	$this->db->transStart();	
  	foreach ($data as $key => $value){  		
  		$this->db->table($table)->insert($value);  		
  	   }
     $this->db->transComplete();
	 }
  	catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
	 
  	return $errors;
  }

  function set_account_group_parent_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineAccountGroupParent();
  	}

  	$table = $this->comp_id.'_grpparentn_'.$this->fy_id;

  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_account_group_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineAccountGroup();
  	}

  	$table = $this->comp_id.'_acctgroupn_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_account_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineAccount();
  	}

  	$table = $this->comp_id.'_acctmaster_'.$this->fy_id;
  	$table2 = $this->comp_id.'_accoppybal_'.$this->fy_id; 	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  			$this->db->query("TRUNCATE TABLE ${table2};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);

  			$acc_id = $this->db->insertID();

  			$data2 = [
  				'acc_id' 			=> $acc_id, 
  				'acc_op_bal' 	=> 0,
  				'acc_py_bal' 	=> 0,
  				'bo_id'				=> 1
  			];
  			
  			$this->db->table($table2)->insert($data2);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_bill_sundry_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineBillSundry(); //Save Default Data In Table
  	}

  	$table = $this->comp_id.'_billsundry_'.$this->fy_id;
  	$table2 = $this->comp_id.'_bsdoppybal_'.$this->fy_id;
	$table3 = $this->comp_id.'_bdstaxmstn_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  			$this->db->query("TRUNCATE TABLE ${table2};");
			$this->db->query("TRUNCATE TABLE ${table3};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$this->db->table($table)->insert($value);

  			$acc_id = $this->db->insertID();

  			$data2 = [
  				'bill_sundry_id' 			=> $acc_id, 
  				'bsd_op_bal' 					=> 0,
  				'bsd_py_bal' 					=> 0,
  				'bo_id'								=> 1
  			];
  			
  			$this->db->table($table2)->insert($data2);
			
			
			if($value['sundry_nature']=='33' || $value['sundry_nature']=='34' || $value['sundry_nature']=='35' || $value['sundry_nature']=='195')
		      $bill_tax_type =$value['sundry_nature'];
		    else
			  $bill_tax_type =0;
		
		    $data3 = array("bill_sundry_id"=>$acc_id,
						             "cmp_tax_cat_id"=>"0","tax_cat_id"=>0,
									 "bill_supply_type"=>0,"bill_hsn_sac"=>"",
									 "bill_input_output"=>2,"bill_tax_short_code"=>"",
								     "bill_tax_account"=>1,
									 "bill_tax_type"=>$bill_tax_type);
			$this->db->table($table3)->insert($data3);
			
			
			
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_cost_center_group_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineCostCenterGroup();
  	}

  	$table = $this->comp_id.'_costctgrup_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_cost_center_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineCostCenter();
  	}

  	$table = $this->comp_id.'_costctmstr_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_bill_by_bill_data($data=[], $truncate=FALSE)
  {
  	$errors = [];

  	$table = $this->comp_id.'_billmaster_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_unit_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineUnits();
  	}

  	$table = $this->comp_id.'_itmunitmst_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_item_category_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineItemCategory();
  	}

  	$table = $this->comp_id.'_itemcatmst_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_item_group_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineItemGroup();
  	}

  	$table = $this->comp_id.'_itemgrpmst_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_mc_group_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineMCGroup();
  	}

  	$table = $this->comp_id.'_mcgrpmstnn_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_mc_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineMC();
  	}

  	$table = $this->comp_id.'_mcmasternn_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }


  function set_voucher_type_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineVoucherTypes();
  	}

  	$table = $this->comp_id.'_cmpvchtype_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_voucher_series_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineVoucherSeries();
  	}

  	$table = $this->comp_id.'_cmpvchseri_'.$this->fy_id;  	
	$table2 = $this->comp_id.'_vchseriesa_'.$this->fy_id; 
    $table3 = $this->comp_id.'_vchseriesm_'.$this->fy_id;  	 	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
			
			if($value['comp_vch_method']=="1"){ //automatic
			    $auto_table_data = array("comp_id"=>$this->comp_id,
				"comp_vch_series_id"=>$value["comp_vch_series_id"],
				"comp_vch_renum_freq"=>"0","comp_vch_start"=>"1",
				"comp_vch_no_padding"=>"0","comp_vch_prefix"=>"",
				"comp_vch_suffix"=>"","comp_vch_no_length"=>"0",
				);
				$this->db->table($table2)->insert($auto_table_data);				
			}
			if($value['comp_vch_method']=="0"){ //manual with bill no blank no
			    $manual_table_data = array("comp_id"=>$this->comp_id,
				"comp_vch_series_id"=>$value["comp_vch_series_id"],
				"comp_vch_blank"=>"0");
				$this->db->table($table3)->insert($manual_table_data);				
			}
			
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_voucher_subtype_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineVoucherSubTypes();
  	}

  	$table = $this->comp_id.'_vchsubtype_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_project_group_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineProjectGroup();
  	}

  	$table = $this->comp_id.'_projectgrp_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_project_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineProject();
  	}

  	$table = $this->comp_id.'_projectmst_'.$this->fy_id;
  	$table2 = $this->comp_id.'_prjoppybal_'.$this->fy_id; 	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  			$this->db->query("TRUNCATE TABLE ${table2};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);

  			$acc_id = $this->db->insertID();


  			$data2 = [
  				[
  					'project_id' 			=> $acc_id, 
  					'project_op_bal' 	=> 0,
  					'project_py_bal' 	=> 0,
  					'project_bal_type' 	=> 'lia',
  					'bo_id'				=> 1
  				],
  				[
  					'project_id' 			=> $acc_id, 
  					'project_op_bal' 	=> 0,
  					'project_py_bal' 	=> 0,
  					'project_bal_type' 	=> 'ast',
  					'bo_id'				=> 1
  				],
  				[
  					'project_id' 			=> $acc_id, 
  					'project_op_bal' 	=> 0,
  					'project_py_bal' 	=> 0,
  					'project_bal_type' 	=> 'exp',
  					'bo_id'				=> 1
  				],
  				[
  					'project_id' 			=> $acc_id, 
  					'project_op_bal' 	=> 0,
  					'project_py_bal' 	=> 0,
  					'project_bal_type' 	=> 'rev',
  					'bo_id'				=> 1
  				],
  			];
  			
  			$this->db->table($table2)->insertBatch($data2);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_print_config_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefinePrintConfig();
  	}

  	$table = $this->comp_id.'_prntconfig_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_print_design_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefinePrintDesign();
  	}

  	$table = $this->comp_id.'_prntdesign_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_currency_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineCurrency();
  	}

  	$table = $this->comp_id.'_compcurrcy_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_item_batch_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = predefineItemBatch();
  	}

  	$table = $this->comp_id.'_itmbatchmt_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	foreach ($data as $key => $value) {
  		try{
  			
  			$this->db->table($table)->insert($value);
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}

  	return $errors;
  }

  function set_comp_accountant_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = [];
  	}

  	$table = $this->comp_id.'_compatnnnn_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}
    try{
		$this->db->transStart();
		foreach ($data as $key => $value) {  		
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);  		
  	    }
		$this->db->transComplete();
	    }
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	return $errors;
  }

  function set_comp_auditor_data($data=[], $truncate=FALSE)
  {
  	$errors = [];
  	if(!$data){
  		$data = [];
  	}

  	$table = $this->comp_id.'_cmpauditor_'.$this->fy_id;  	
  	
  	if($truncate){
  		try{
  			$this->db->query("TRUNCATE TABLE ${table};");
  		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	}
		try{
			$this->db->transStart();
  	foreach ($data as $key => $value) {
  		
  			$value['comp_id'] = $this->comp_id;
  			$this->db->table($table)->insert($value);
  		
			}
			$this->db->transComplete();
		}
  		catch (\Exception $e) {
  			$errors[] = $e->getMessage();
  		}
  	return $errors;
  }



	//-------------------------------


  function drop_txn_tables()
  {
  	$errors = [];

  	$response = $this->account_master_txn_tables(false,true);
  	foreach ($response as $error) { $errors[] = $error; }

  	$response = $this->bill_sundry_master_txn_tables(false,true);
  	foreach ($response as $error) { $errors[] = $error; }

  	$response = $this->item_master_txn_tables(false,true);
  	foreach ($response as $error) { $errors[] = $error; }

  	return $errors;
  }


}