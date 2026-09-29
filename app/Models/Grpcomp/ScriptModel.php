<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class ScriptModel extends Model	{
	public function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		$this->db        =  $this->externaldb->get_company_db();
		$this->session       =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');
		$this->enc_string    =  new enc_string();
    }

    function all_companies()
    {
    	$erp_db = $this->externaldb->erp_db();

        // $uuid            =  $this->session->get('uuid');
	    // $uuid_aicountly  = 	 $this->session->get('uuid_aicountly');
	    $my_companies= array();
        $builder = $erp_db->table("aictlyerp_compidgenr_univdb"); 
        $builder->select('aictlyerp_compidgenr_univdb.*, aictlyerp_compmastern_univdb.comp_code');
        $builder->join('aictlyerp_compmastern_univdb', 'aictlyerp_compmastern_univdb.comp_id = aictlyerp_compidgenr_univdb.comp_id');
        // $builder->where('uuid',$uuid);
        // $builder->where('uuid_aicountly',$uuid_aicountly);
		$result = $builder->get()->getResultArray();

		if($result){
		    foreach($result as $row){
		       
		        if($row['comp_id'] == 1){
		        	// $this->modify_item_transactions($row['comp_id'], $row['comp_code']);
		        	// $this->migrate_item_transactions($row['comp_id'], $row['comp_code']);

		        	// $this->create_item_val_table($row['comp_id'], $row['comp_code']);
		        	// $this->modify_item_opbalval($row['comp_id'], $row['comp_code']);
		        }
		    
		    }
		    
		    
		}
    }

    function modify_item_opbalval($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itmoppyval_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD batch_id BIGINT NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}
		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN op_bal_val DECIMAL(18,4) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}
		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN py_bal_val DECIMAL(18,4) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		echo "company-".$comp_id."  Executed"."<br>";
  	}


    function modify_decimals($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_acctmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['acc_id'];

  			$sub_table = $comp_id.'_accnttxnnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN acc_txn_amount DECIMAL(18,8) NOT NULL, MODIFY COLUMN acc_bal DECIMAL(18,8) NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}
  		}

  		$sub_table = $comp_id.'_accoppybal_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN acc_op_bal DECIMAL(18,8) NOT NULL, MODIFY COLUMN acc_py_bal DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		$table = $comp_id.'_accttxnoth_'.$comp_id;
  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN acc_oth_txn_amount DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		//--------------------

  		$table = $comp_id.'_itemmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['item_id'];

  			$sub_table = $comp_id.'_itemtxnnnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN item_txn_amount DECIMAL(18,8) NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}

  		}

  		$table = $comp_id.'_itemtxnoth_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN item_oth_txn_amount DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		//--------------------

  		$table = $comp_id.'_billmaster_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bill_op_bal DECIMAL(18,8) NOT NULL, MODIFY COLUMN bill_py_bal DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$sub_table = $comp_id.'_billstxnnn_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN bills_txn_amt DECIMAL(18,8) NOT NULL, MODIFY COLUMN bills_txn_bal DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}
  		

  		//--------------------

  		$table = $comp_id.'_costctmstr_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN cc_op_bal DECIMAL(18,8) NOT NULL, MODIFY COLUMN cc_py_bal DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$sub_table = $comp_id.'_costcttxnn_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN cc_txn_amt DECIMAL(18,8) NOT NULL, MODIFY COLUMN cc_txn_bal DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		//--------------------
		
		$table = $comp_id.'_billsundry_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['bill_sundry_id'];

  			$sub_table = $comp_id.'_sundrytxnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN sundry_txn_amount DECIMAL(18,8) NOT NULL, MODIFY COLUMN sundry_bal DECIMAL(18,8) NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}
  		}

  		$sub_table = $comp_id.'_bsdoppybal_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN bsd_op_bal DECIMAL(18,8) NOT NULL, MODIFY COLUMN bsd_py_bal DECIMAL(18,8) NOT NULL');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}
  		


  		echo "company-".$comp_id."  Executed"."<br>";       
    }

    function modify_item_transactions($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['item_id'];

  			$sub_table = $comp_id.'_itemtxnnnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' ADD item_unit INT NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
			try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' ADD item_bal_qty DECIMAL(18,4) NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
			try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' ADD item_avail INT NOT NULL DEFAULT "1"');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
			try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN item_txn_qty DECIMAL(18,4) NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
			try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN batch_id BIGINT NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}

			$sub_table2 = $comp_id.'_itemtxnvaln_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $sub_table2 . ' ADD item_txn_id BIGINT NOT NULL');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}

  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_item_val_table($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['item_id'];

  			$sub_table = $comp_id.'_itemtxnval_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('CREATE TABLE `'.$sub_table.'` (`valuation_id` BIGINT NOT NULL AUTO_INCREMENT, `item_id` BIGINT NOT NULL,`item_txn_id` BIGINT NOT NULL,`method_id` INT(5) NOT NULL,`item_value` DECIMAL(18,4) NOT NULL, PRIMARY KEY (`valuation_id`)) ENGINE=MyISAM;');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function migrate_item_transactions($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['item_id'];

  			try{
	  			$itemtxnnnn_tbl = $comp_id.'_itemtxnnnn_'.$acc_id.'_'.$comp_id;
		        $result = $external_db->table($itemtxnnnn_tbl)->get()->getResultArray();

		        foreach ($result as $key => $value) {

		        	try{
			        	$itemtxnbal_tbl = $comp_id.'_itemtxnbal_'.$acc_id.'_'.$comp_id;
			        	$data = $external_db->table($itemtxnbal_tbl)
								        	->where('item_txn_id', $value['item_txn_id'])
								        	->get()->getRowArray();

			        	if($data){
			        		$update_data = [
			        			'item_unit' => $data['item_unit'],
			        			'item_bal_qty' => $data['item_bal_qty'],
			        			'item_avail' => $data['item_avail'],
			        			'batch_id' => $data['batch_id'],
			        		];

			        		$external_db->table($itemtxnnnn_tbl)
						        		->where('item_txn_id', $value['item_txn_id'])
						        		->update($update_data);    
			        	}
		        	}
			        catch (\Exception $e) {
					   echo $e->getMessage() . '<br>';
					}
		        }
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_currency($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$compcurrcy_tbl = $comp_id.'_compcurrcy_'.$comp_id; 
    	$forexrates_tbl = $comp_id.'_forexrates_'.$comp_id; 

    	try{ 
        	$external_db->query("DROP TABLE `".$compcurrcy_tbl."` ");

    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}
		try{ 
        	$external_db->query("DROP TABLE `".$forexrates_tbl."` ");

    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

    	try{ 
        	$external_db->query("CREATE TABLE `".$compcurrcy_tbl."` (
		                          `comp_currency_id` INT NOT NULL AUTO_INCREMENT , `comp_id` BIGINT NOT NULL , `curr_name` VARCHAR(150) NOT NULL , `curr_symbol` VARCHAR(10) NOT NULL , `curr_string` VARCHAR(50) NOT NULL , `curr_sub_string` VARCHAR(50) NOT NULL , `curr_initial` VARCHAR(50) NOT NULL , `forex_type` VARCHAR(1) NOT NULL , 
								  PRIMARY KEY (`comp_currency_id`)) ENGINE = MyISAM;"
								);

    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		try{ 
        	$external_db->query('INSERT INTO '.$compcurrcy_tbl.' (comp_currency_id, comp_id, curr_name, curr_symbol, curr_string, curr_sub_string, curr_initial, forex_type) VALUES ("1", "'.$comp_id.'", "Rupee", "₹", "", "", "INR","M");');

    	}
    	catch (\Exception $e) {
		  	echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		try{ 
        	$external_db->query("CREATE TABLE `".$forexrates_tbl."` (
		                          `forex_rate_id` BIGINT NOT NULL AUTO_INCREMENT ,`comp_currency_id` INT NOT NULL , `rate_per_inr` DECIMAL(18,8) NOT NULL , `rate_per_fcy` DECIMAL(18,8) NOT NULL , `curr_date` DATE NULL, `forex_type` VARCHAR(1) NOT NULL , 
								  PRIMARY KEY (`forex_rate_id`)) ENGINE = MyISAM;"
								);

    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }


    function create_hobo($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$hobomaster_tbl = $comp_id.'_hobomaster_'.$comp_id; 

    	try{ 
        	$external_db->query("CREATE TABLE `".$hobomaster_tbl."` (
		                          `bo_id` BIGINT NOT NULL AUTO_INCREMENT , `comp_id` BIGINT NOT NULL , `bo_name` VARCHAR(150) NOT NULL , `bo_alias` VARCHAR(150) NULL , `acc_grp_id` BIGINT NOT NULL , `bo_opdate` DATE NULL , `bo_cldate` DATE NULL , 
								  `bo_add1` VARCHAR(150) NULL , `bo_add2` VARCHAR(150) NULL , 
								  `bo_city` VARCHAR(100) NULL , `bo_state` INT(10) NULL , `bo_country` INT(10) NULL , `bo_pin` VARCHAR(20) NULL , `bo_zone` INT(10) NULL ,
								  PRIMARY KEY (`bo_id`), INDEX `boindexing` (`comp_id`, `bo_name`, `bo_country`),
								  INDEX `group indexing` (`acc_grp_id`)) ENGINE = InnoDB;"
								);

    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }

    function get_capital($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$acctgroupn_tbl = $comp_id.'_acctgroupn_'.$comp_id;
    	$account_master_tbl = $comp_id.'_acctmaster_'.$comp_id;

    	try{
    		$result = $external_db->query('select count(*) from '.$account_master_tbl.' where acc_grp_id = 1')->getResultArray();
    		echo "<pre>";print_r($result);

    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }


    function update_od_occ_ac($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$acctgroupn_tbl = $comp_id.'_acctgroupn_'.$comp_id;

    	try{
    		$external_db->query('UPDATE '.$acctgroupn_tbl.' SET acc_grp_parent_id = "4" where acc_grp_id = "21";');
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }

    function get_inventories($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$acctgroupn_tbl = $comp_id.'_acctgroupn_'.$comp_id;

    	try{
    		$result = $external_db->query('select * from '.$acctgroupn_tbl.' where acc_grp_id = 21')->getResultArray();
    		echo "<pre>";print_r($result);

    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }

    function add_od_occ_ac($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$acctgroupn_tbl = $comp_id.'_acctgroupn_'.$comp_id;

    	try{
    		$external_db->query('DELETE FROM '.$acctgroupn_tbl.' where acc_grp_id = "21";');

    		$external_db->query('INSERT INTO '.$acctgroupn_tbl.' (acc_grp_id, comp_id, acc_grp_name, acc_grp_alias, acc_grp_primary, acc_grp_parent_id, under_acc_grp_id, under_main_grp_id, restrictions) VALUES ("21", "'.$comp_id.'", "BANK OD / OCC A/c", "BANK OD / OCC A/c", "Y", "4", "0", "0", "DELREST");');
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }

    function createTables($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$pcklistmst_tbl = $comp_id.'_pcklistmst_'.$comp_id;

    	try{
    		$external_db->query("CREATE TABLE IF NOT EXISTS `".$pcklistmst_tbl."` (
	                             `list_id` BIGINT NOT NULL AUTO_INCREMENT , `list_name` VARCHAR(150) NULL ,			 
								 `comp_id` BIGINT NOT NULL ,`list_delivery` TEXT ,`list_status` VARCHAR(10),
								 `list_consignee` VARCHAR(30),`list_type` VARCHAR(10),`list_against_ref` VARCHAR(30),	
								  INDEX `pcklistmst indexing` (`list_name`, `comp_id`,`list_type`,`list_consignee`),
								  PRIMARY KEY (`list_id`)) ENGINE = InnoDB;");
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		$listpacked_tbl = $comp_id.'_listpacked_'.$comp_id;

    	try{
    		$external_db->query("CREATE TABLE IF NOT EXISTS `".$listpacked_tbl."` (
	                             `list_id` BIGINT NOT NULL , `item_id` BIGINT NOT NULL , 								 
								 `item_unit` BIGINT NOT NULL ,`item_qty_packed` VARCHAR(30) ,`cu_id` BIGINT NOT NULL,
								 `list_level_id` BIGINT NOT NULL,	
								  INDEX `listpacked indexing` (`list_id`, `item_id`,`item_unit`,`cu_id`,`list_level_id`)
								  ) ENGINE = InnoDB;");
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		$pcklistqty_tbl = $comp_id.'_pcklistqty_'.$comp_id;

    	try{
    		$external_db->query("CREATE TABLE IF NOT EXISTS `".$pcklistqty_tbl."` (
	                             `list_id` BIGINT NOT NULL , `item_id` BIGINT NOT NULL , 								 
								 `item_unit` BIGINT NOT NULL ,`item_qty_available` VARCHAR(30) ,`item_qty_initial` VARCHAR(30),	
								  INDEX `pcklistqty indexing` (`list_id`, `item_id`,`item_unit`)
								  ) ENGINE = InnoDB;");
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		$listcumast_tbl = $comp_id.'_listcumast_'.$comp_id;

    	try{
    		$external_db->query("CREATE TABLE IF NOT EXISTS `".$listcumast_tbl."` (
	                             `cu_id` BIGINT NOT NULL AUTO_INCREMENT , `list_id` BIGINT NOT NULL ,
								 `cu_label` VARCHAR(70) ,`cu_unit` BIGINT NOT NULL,
								 `list_level_id` BIGINT NOT NULL,	
								  INDEX `listcumast indexing` (`list_id`, `cu_unit`,`list_level_id`),
								  PRIMARY KEY (`cu_id`)) ENGINE = InnoDB;");
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		$cupackingn_tbl = $comp_id.'_cupackingn_'.$comp_id;

    	try{
    		$external_db->query("CREATE TABLE IF NOT EXISTS `".$cupackingn_tbl."` (
	                             `packing_id` BIGINT NOT NULL AUTO_INCREMENT , `list_id` BIGINT NOT NULL ,
								 `cu_id` BIGINT NOT NULL,`cu_unit` BIGINT NOT NULL,`list_level_id` BIGINT NOT NULL,	
								 `cu_status` VARCHAR(10),`pck_status` VARCHAR(10),
								  INDEX `cupackingn indexing` (`list_id`,`cu_id`, `cu_unit`,`list_level_id`),
								  PRIMARY KEY (`packing_id`)) ENGINE = InnoDB;");
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		$leveltrack_tbl = $comp_id.'_leveltrack_'.$comp_id;

    	try{
    		$external_db->query("CREATE TABLE IF NOT EXISTS `".$leveltrack_tbl."` (
	                             `packing_id` BIGINT NOT NULL , `list_id` BIGINT NOT NULL , 								 
								 `nxt_packing_id` BIGINT NOT NULL ,`prev_packing_id` BIGINT NOT NULL,
								  INDEX `listpacked indexing` (`packing_id`, `list_id`,`nxt_packing_id`,`prev_packing_id`)
								  ) ENGINE = InnoDB;");
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }

    function add_bill_sundry_parent($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$bill_sundry_tbl = $comp_id.'_billsundry_'.$comp_id;

    	try{
    		$external_db->query('ALTER TABLE `'.$bill_sundry_tbl.'` ADD `acc_grp_parent_id` INT NOT NULL ;');
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}


		echo "company-".$comp_id."  Executed"."<br>";
    }

    function voucher_types($comp_id, $comp_code) 
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

		$comp_vch_cons_tbl = $comp_id.'_vhtxnconso_'.$comp_id;
		$voucher_type_tbl = $comp_id.'_cmpvchtype_'.$comp_id;

		try{
	    	$builder = $external_db->table($comp_vch_cons_tbl);
		    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$comp_vch_cons_tbl.'.voucher_type_id');
		    $builder->select($voucher_type_tbl.'.comp_vch_type');
		    $result = $builder->get()->getResultArray();

		    $final = [];
		    foreach ($result as $key => $value) {
		    	if (!in_array($value['comp_vch_type'], $final)){
		    		$final[] = $value['comp_vch_type'];
		    	}
		    }
		    echo "<pre>";print_r($final);echo "</pre>";
		    echo "company-".$comp_id."  Executed"."<br>";

		}
	    catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

  		
    }

    function itmoppyval($comp_id, $comp_code) 
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

		$itmoppyval_table =  $comp_id.'_itmoppyval_'.$comp_id;

		try{
			$external_db->query("CREATE TABLE `${itmoppyval_table}` (
	                             `item_id` BIGINT NOT NULL ,  `comp_id` BIGINT NOT NULL , 
								 `item_unit` BIGINT NOT NULL ,`op_bal_val` DECIMAL(18,2) ,`py_bal_val` DECIMAL(18,2),
								 `method_id` INT(1),		
								  INDEX `short opvv indexing` (`item_id`, `item_unit`,`comp_id`,`method_id`)) ENGINE = InnoDB;");
		}
		catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function drop_bill_sundry_op($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$bill_sundry_tbl = $comp_id.'_billsundry_'.$comp_id;

    	try{
    		$external_db->query('ALTER TABLE `'.$bill_sundry_tbl.'` DROP COLUMN `sundry_op_bal`;');
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		try{
    		$external_db->query('ALTER TABLE `'.$bill_sundry_tbl.'` DROP COLUMN `sundry_py_bal`;');
    	}
    	catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

		echo "company-".$comp_id."  Executed"."<br>";
    }



    function bill_sundry($comp_id, $comp_code) //executed
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$bill_sundry_tbl = $comp_id.'_billsundry_'.$comp_id;

		$bsdoppybal_table =  $comp_id.'_bsdoppybal_'.$comp_id;

		try{
			$external_db->query("CREATE TABLE `".$bsdoppybal_table."` (`bill_sundry_id` BIGINT, `bsd_op_bal` DECIMAL(18,2), `bsd_py_bal` DECIMAL(18,2), KEY `bsdoppybal indexing` (`bill_sundry_id`) USING BTREE ) ENGINE=MyISAM;");
		}
		catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage()."<br>";
		}

	
		$external_db->query("TRUNCATE TABLE ${bsdoppybal_table};");
    	
    	$result = $external_db->table($bill_sundry_tbl)->get()->getResultArray();

  		foreach ($result as $key => $value) {

  			try{

	  			
	  			$external_db->query('INSERT INTO `'.$comp_id.'_bsdoppybal_'.$comp_id.'` (bill_sundry_id,bsd_op_bal,bsd_py_bal) VALUES ("'.$value['bill_sundry_id'].'", "0","0");');

  				echo "company-".$comp_id."  Executed for bill_sundry_id ".$value['bill_sundry_id']."<br>";
    		}

    		catch (\Exception $e) {
			  echo "company-".$comp_id." bill_sundry_id ".$value['bill_sundry_id']."  Error- ".$e->getMessage()."<br>";
			}
  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function narration($comp_id, $comp_code) //executed
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$comp_vch_cons_tbl = $comp_id.'_vhtxnconso_'.$comp_id;
    	$result = $external_db->table($comp_vch_cons_tbl)->get()->getResultArray();

    	foreach ($result as $key => $value) {
    		$long_narr_tbl = $comp_id.'_long_narrn_'.$comp_id;
			$narration = $external_db->table($long_narr_tbl)->where('vch_txn_id',$value['voucher_txn_id'])->get()->getRowArray();
			if($narration){

				$insert_data = [
		              "comp_id"             => $value['comp_id'],
		              "comp_vch_series_id"  => $value['comp_vch_series_id'],
		              "voucher_txn_id"      => $value['voucher_txn_id'],
		              "master_id"           => 0,
		              'master_id_type'      => 'nrr'
		        ];

		        $comp_txn_master_tbl = $comp_id.'_comptxnmst_'.$comp_id;

		        $external_db->table($comp_txn_master_tbl)->where('master_id_type','nrr')->where('voucher_txn_id',$value['voucher_txn_id'])->delete();

			   	$external_db->table($comp_txn_master_tbl)->insert($insert_data);	
		       	$txn_id = $external_db->insertID();

		      	$external_db->table($long_narr_tbl)->where('vch_txn_id',$value['voucher_txn_id'])
		      				->update(['txn_id'=> $txn_id]);
		      	
			}
    	}

    	echo "company-".$comp_id."  Executed"."<br>";

    }

    function new_field($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);
    	try{
    	
  			$table =  $comp_id.'_vhtxnconso_'.$comp_id;
  			$external_db->query('ALTER TABLE `'.$table.'` CHANGE `voucher_tag` `voucher_tag` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;');

			echo "company-".$comp_id."  Executed";

		}

  		catch (\Exception $e) {
		  echo "company-".$comp_id." Error- ".$e->getMessage();
		}	

		echo "<br>";
    }

    function new_field_master($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);
    	$master_tbl = $comp_id.'_itemmaster_'.$comp_id; //assume fy_id same as comp_id
    	
  
  			
	  		$result = $external_db->table($master_tbl)->get()->getResultArray();

	  		foreach ($result as $key => $value) {

	  			try{

		  			$table =  $comp_id.'_itemtxnbal_'.$value['item_id'].'_'.$comp_id;
		  			$external_db->query('UPDATE `'.$table.'` SET `item_avail` = "1" where `item_avail` = "0";');

	  				echo "company-".$comp_id."  Executed for item_id ".$value['item_id']."<br>";
        		}

        		catch (\Exception $e) {
				  echo "company-".$comp_id." item_id ".$value['item_id']."  Error- ".$e->getMessage()."<br>";
				}
	  		}
  
		echo "<br>";
    }

    


    function set_account_txn_rows()
    {
	    	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  		$result_ = $this->db->table($account_master_tbl)->get()->getResultArray();

	  		foreach ($result_ as $key_ => $value_) {
	  			$acc_id = $value_['acc_id'];

	  			$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
         
	         	$builder = $this->db->table($acc_txn_tbl);
		        $builder->select('*');
		        $builder->orderBy('posted_on', 'asc');
		        $result = $builder->get()->getResultArray();

		        // echo "<pre>";print_r($result);exit;
		        $this->db->query('TRUNCATE TABLE ' . $acc_txn_tbl);

		        try{
		        	$this->db->query('ALTER TABLE ' . $acc_txn_tbl . ' MODIFY acc_txn_id INT AUTO_INCREMENT PRIMARY KEY');
		        }
		        catch (\Exception $e) {
				  // echo $e->getMessage();
				}
		      
		        if($result){
	             	foreach($result as $value){
	             		unset($value['acc_txn_id']);
	                 	$this->db->table($acc_txn_tbl)->insert($value);
	                 	
	             	}
	            }

	            $bal=0; //include opening balance
	            $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
     			$balance = $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();
     			if($balance){
     				$bal  = $balance['acc_op_bal'];
     			}

				$result2 = $this->db->table($acc_txn_tbl)
		        					->where('acc_id', $acc_id)
		        					->orderBy('acc_txn_date', 'asc')
		        					->orderBy('acc_txn_id', 'asc')
		        					->get()->getResultArray();
									
				 if($result2){
			        foreach($result2 as $key2 => $value2){
			        	if($value2['acc_txn_drcr'] == 'd')
			            	$bal += $value2['acc_txn_amount'];
			            if($value2['acc_txn_drcr'] == 'c')
			            	$bal += -$value2['acc_txn_amount'];
			            
			            $this->db->table($acc_txn_tbl)->where('acc_txn_id', $value2['acc_txn_id'])->update(['acc_bal' => $bal]);
			        } 
		    	}
	  		}        
    }
 
    public function add_parent_accounts()
    {
    		$account_grpprnt_tbl =  $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');;

    		$this->db->query('TRUNCATE TABLE ' . $account_grpprnt_tbl);

    		try{
    			$this->db->query('ALTER TABLE '.$account_grpprnt_tbl.' ADD `restrictions` VARCHAR(7) NOT NULL AFTER `acc_grp_parent` ');
    		}
    		catch (\Exception $e) {
		  // echo $e->getMessage();
		}

    		$AccountGroupTypes    = AccountGroupTypes();
		foreach($AccountGroupTypes as $key => $value){

			$this->db->query('INSERT INTO '.$account_grpprnt_tbl.' (acc_grp_parent_id,comp_id,acc_grp_parent,restrictions) VALUES ("'.$value['acc_grp_parent_id'].'","'.$this->company_id.'", "'.$value['grp_name'].'", "'.$value['restrictions'].'");');		
			$acc_grp_parent_id = $this->db->insertID();	
		}
    }

    public function add_account_groups()
    {
    		$table = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');

    		$this->db->query('TRUNCATE TABLE ' . $table);
    		try{
    			$this->db->query('ALTER TABLE '.$table.' ADD `restrictions` VARCHAR(7) NOT NULL AFTER `under_main_grp_id` ');
    		}
    		catch (\Exception $e) {
		  // echo $e->getMessage();
		}

    		
    		// ALTER TABLE `1_acctgroupn_1` ADD `restrictions` VARCHAR(7) NOT NULL AFTER `under_main_grp_id

    		$records = [];

    		// OWNER'S FUND 1
    			// Capital Account
			// Reserves & Surplus

    		$records[] = ['acc_grp_id' => 1, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Capital Account', 'acc_grp_alias' => 'Capital Account', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 1, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
    		$records[] = ['acc_grp_id' => 2, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Reserves & Surplus', 'acc_grp_alias' => 'Reserves & Surplus', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 1, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

    		// NON CURRENT LIABILITIES	2
				// Long Term Borrowings
				// Deferred Tax Liabilities
				// Other Long Term Liabilities
				// Long Term Provisions

    		$records[] = ['acc_grp_id' => 3, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Long Term Borrowings', 'acc_grp_alias' => 'Long Term Borrowings', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
    		$records[] = ['acc_grp_id' => 4, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Deferred Tax Liabilities', 'acc_grp_alias' => 'Deferred Tax Liabilities', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
    		$records[] = ['acc_grp_id' => 5, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Other Long Term Liabilities', 'acc_grp_alias' => 'Other Long Term Liabilities', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
    		$records[] = ['acc_grp_id' => 6, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Long Term Provisions', 'acc_grp_alias' => 'Long Term Provisions', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

    		// NON CURRENT ASSETS	3
				// Fixed Assets
				// Intangible Assets
				// Capital Work In Progress
				// Intangible Assets Under Development
				// Non Current Investments
				// Deferred Tax Assets
				// Long Term Loans & Advances
				// Other Non Current Assets

		$records[] = ['acc_grp_id' => 7, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Fixed Assets', 'acc_grp_alias' => 'Fixed Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 8, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Intangible Assets', 'acc_grp_alias' => 'Intangible Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 9, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Capital Work In Progress', 'acc_grp_alias' => 'Capital Work In Progress', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 10, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Intangible Assets Under Development', 'acc_grp_alias' => 'Intangible Assets Under Development', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		$records[] = ['acc_grp_id' => 11, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Non Current Investments', 'acc_grp_alias' => 'Non Current Investments', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 12, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Deferred Tax Assets', 'acc_grp_alias' => 'Deferred Tax Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 13, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Long Term Loans & Advances', 'acc_grp_alias' => 'Long Term Loans & Advances', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 14, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Other Non Current Assets', 'acc_grp_alias' => 'Other Non Current Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// CURRENT LIABILITIES	4
			// Short Term Borrowings
			// Trade Payable - 16
			// Other Current Liabilities
			// Short Term Provisions
			// Duties & Taxes

		$records[] = ['acc_grp_id' => 15, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Short Term Borrowings', 'acc_grp_alias' => 'Short Term Borrowings', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 16, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Trade Payable', 'acc_grp_alias' => 'Trade Payable', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'];
		$records[] = ['acc_grp_id' => 17, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Other Current Liabilities', 'acc_grp_alias' => 'Other Current Liabilities', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 18, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Short Term Provisions', 'acc_grp_alias' => 'Short Term Provisions', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 19, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Duties & Taxes', 'acc_grp_alias' => 'Duties & Taxes', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// CURRENT ASSETS	5
			// Current Investments
			// Inventories
			// Trade Receivables - 21
			// Cash & Cash Equivalents - 22
			// Short Term Loans & Advances
			// Other Current Assets

		$records[] = ['acc_grp_id' => 20, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Current Investments', 'acc_grp_alias' => 'Current Investments', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 21, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Inventories', 'acc_grp_alias' => 'Inventories', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 22, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Trade Receivables', 'acc_grp_alias' => 'Trade Receivables', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'];

		$records[] = ['acc_grp_id' => 23, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Cash & Cash Equivalents', 'acc_grp_alias' => 'Cash & Cash Equivalents', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'];
		$records[] = ['acc_grp_id' => 24, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Short Term Loans & Advances', 'acc_grp_alias' => 'Short Term Loans & Advances', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		$records[] = ['acc_grp_id' => 25, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Other Current Assets', 'acc_grp_alias' => 'Other Current Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// OPENING STOCK	6
			// Opening Stock
		$records[] = ['acc_grp_id' => 26, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Opening Stock', 'acc_grp_alias' => 'Opening Stock', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 6, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// PURCHASE	7
			// Purchase
		$records[] = ['acc_grp_id' => 27, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Purchase', 'acc_grp_alias' => 'Purchase', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 7, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// SALES	8
			// Sales
		$records[] = ['acc_grp_id' => 28, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Sales', 'acc_grp_alias' => 'Sales', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 8, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		
		// CLOSING STOCK	9
			// Closing Stock
		$records[] = ['acc_grp_id' => 29, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Closing Stock', 'acc_grp_alias' => 'Closing Stock', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 9, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];
		

		// DIRECT INCOME	10
			// Direct Income
		$records[] = ['acc_grp_id' => 30, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Direct Income', 'acc_grp_alias' => 'Direct Income', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 10, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// DIRECT EXPENSE	11
			// Direct Expense
		$records[] = ['acc_grp_id' => 31, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Direct Expense', 'acc_grp_alias' => 'Direct Expense', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 11, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// INDIRECT INCOME	12
			// Indirect Income
		$records[] = ['acc_grp_id' => 32, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Indirect Income', 'acc_grp_alias' => 'Indirect Income', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 12, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		// INDIRECT EXPENSE	13
			// Indirect Expense
		$records[] = ['acc_grp_id' => 33, 'comp_id' => $this->company_id, 'acc_grp_name' => 'Indirect Expense', 'acc_grp_alias' => 'Indirect Expense', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 13, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''];

		foreach($records as $value){

			$this->db->query('INSERT INTO '.$table.' (acc_grp_id, comp_id, acc_grp_name, acc_grp_alias, acc_grp_primary, acc_grp_parent_id, under_acc_grp_id, under_main_grp_id, restrictions) VALUES ("'.$value['acc_grp_id'].'", "'.$value['comp_id'].'", "'.$value['acc_grp_name'].'", "'.$value['acc_grp_alias'].'", "'.$value['acc_grp_primary'].'", "'.$value['acc_grp_parent_id'].'", "'.$value['under_acc_grp_id'].'", "'.$value['under_main_grp_id'].'", "'.$value['restrictions'].'");');		
				
		}
    }

    public function update_account_groups()
    {
		$table =  $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');;

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=22 WHERE acc_grp_id= 16;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=16 WHERE acc_grp_id= 17;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=17 WHERE acc_grp_id= 4;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=26 WHERE acc_grp_id= 7;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=7 WHERE acc_grp_id= 5;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=3 WHERE acc_grp_id= 2;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=25 WHERE acc_grp_id= 6;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=27 WHERE acc_grp_id= 8;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=28 WHERE acc_grp_id= 9;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=29 WHERE acc_grp_id= 10;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=30 WHERE acc_grp_id= 11;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=31 WHERE acc_grp_id= 12;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=32 WHERE acc_grp_id= 13;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=33 WHERE acc_grp_id= 14;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=23 WHERE acc_grp_id= 15;');
    		
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=19 WHERE acc_grp_id= 18;');	

    }
    public function update_sundry_groups()
    {
		$table =  $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');;

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=22 WHERE acc_grp_id= 16;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=16 WHERE acc_grp_id= 17;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=17 WHERE acc_grp_id= 4;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=26 WHERE acc_grp_id= 7;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=7 WHERE acc_grp_id= 5;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=3 WHERE acc_grp_id= 2;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=25 WHERE acc_grp_id= 6;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=27 WHERE acc_grp_id= 8;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=28 WHERE acc_grp_id= 9;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=29 WHERE acc_grp_id= 10;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=30 WHERE acc_grp_id= 11;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=31 WHERE acc_grp_id= 12;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=32 WHERE acc_grp_id= 13;');

    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=33 WHERE acc_grp_id= 14;');
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=23 WHERE acc_grp_id= 15;');
    		
    		$this->db->query('UPDATE '.$table.' SET acc_grp_id=19 WHERE acc_grp_id= 18;');	
    		
    }

}