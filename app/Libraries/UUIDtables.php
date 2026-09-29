<?php
namespace App\Libraries;
use App\Libraries\externaldb;


class UUIDtables { // 2
	
	public $uuid;
	public $comp_id;
	public $fy_id;
	public $uuid_db;
	public $db;
	public $drop_status;
	public $create_status;

	function __construct($uuid,$comp_id=0,$fy_id = 0){	

		helper(['custom','predefineERP']);

		$this->uuid = $uuid;
		$this->comp_id = $comp_id;
		$this->fy_id = $fy_id;

		$externaldb = new externaldb();
		$this->uuid_db = $externaldb->connect_universal_uuid_db();

		$comp_code = erp_compcode_format($this->comp_id);
		$comp_code = strtolower($comp_code);

		$this->db = $externaldb->single_company_db($comp_code);
	}
	
	
	
	function default_tables($create_status=true,$drop_status=true) // 2 tables
	{
		$errors = [];
		// 23 masters
		$table = $this->comp_id.'_baseidgenrt';  	
		try{
			if($drop_status){
				$this->uuid_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->uuid_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`mst_base_id` BIGINT NOT NULL AUTO_INCREMENT ,
					`uuid` BIGINT NOT NULL ,	
					`comp_id` BIGINT NOT NULL ,
					`fy_id` BIGINT NOT NULL ,
					`mst_type` VARCHAR(10) NOT NULL ,					
					PRIMARY KEY (`mst_base_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $this->comp_id.'_cofymstmap';  	
		try{

			if($drop_status){
				$this->uuid_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->uuid_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`cofymst_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`uuid` BIGINT NOT NULL ,
					`comp_id` BIGINT NOT NULL , 
					`comp_fy_id` BIGINT NOT NULL , 
					`mst_type` VARCHAR(10) NOT NULL ,
					`mst_base_id` BIGINT NOT NULL ,  
					`mst_fy_id` BIGINT NOT NULL , 					
					PRIMARY KEY (`cofymst_id`)
				) ENGINE=InnoDB ;");
			}
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		
		$table = $this->comp_id.'_usrrights';  	
		try{
			if($drop_status){
				$this->uuid_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->uuid_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`usr_right_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`uuid` BIGINT NOT NULL ,
					`usr_config_id` BIGINT NOT NULL ,
					`voucher_series_id` BIGINT NOT NULL ,
					`usr_rights` VARCHAR(100) NOT NULL ,
					`accessprof_id` BIGINT NOT NULL ,
					PRIMARY KEY (`usr_right_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		$table = $this->comp_id.'_accessprof';  	
		try{
			if($drop_status){
				$this->uuid_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
			}

			if($create_status){
				$this->uuid_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`accessprof_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`uuid` BIGINT NOT NULL ,
					`accessprof_name` VARCHAR(100) NOT NULL ,
					`accessprof_desg` VARCHAR(100) NOT NULL ,	
					PRIMARY KEY (`accessprof_id`)
				) ENGINE=InnoDB ;");
			}

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		return $errors;
	}

	// 23 masters
	function update_base_id()
	{
		$this->create_branch_base_id();
		$this->create_currency_base_id();
		$this->create_voucher_series_base_id();
		
		$this->create_account_group_base_id();
		$this->create_account_base_id();
		$this->create_bill_sundry_base_id();
		$this->create_item_group_base_id();
		$this->create_item_category_base_id();
		
		$this->create_item_base_id();
		$this->create_item_batch_base_id();
		$this->create_unit_base_id();
		$this->create_mc_group_base_id();
		$this->create_mc_base_id();
		
		
		$this->create_bill_of_material_base_id();
		$this->create_barcode_base_id();
		$this->create_label_base_id();
		$this->create_cost_center_group_base_id();
		
		$this->create_cost_center_base_id();
		$this->create_bill_by_bill_base_id();
		$this->create_project_group_base_id();
		$this->create_project_base_id();
		$this->create_print_base_id();
		
	}

	function create_branch_base_id()
	{
		$table = $this->comp_id.'_hobomaster_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['bo_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'hobomaster');

				$this->db->table($table)
				->where('bo_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'hobomaster',$mst_base_id);
			}
		}
	}

	function create_currency_base_id()
	{
		$table = $this->comp_id.'_compcurrcy_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['comp_currency_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'compcurrcy');

				$this->db->table($table)
				->where('comp_currency_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'compcurrcy',$mst_base_id);
			}
		}
	}

	function create_voucher_series_base_id()
	{
		$table = $this->comp_id.'_cmpvchseri_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['comp_vch_series_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'cmpvchseri');

				$this->db->table($table)
				->where('comp_vch_series_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'cmpvchseri',$mst_base_id);
			}
		}
	}

	function create_account_group_base_id()
	{
		$table = $this->comp_id.'_acctgroupn_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['acc_grp_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'acctgroupn');

				$this->db->table($table)
				->where('acc_grp_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'acctgroupn',$mst_base_id);
			}
		}
	}

	function create_account_base_id()
	{
		$table = $this->comp_id.'_acctmaster_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['acc_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'acctmaster');

				$this->db->table($table)
				->where('acc_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'acctmaster',$mst_base_id);
			}
		}
	}

	function create_bill_sundry_base_id()
	{
		$table = $this->comp_id.'_billsundry_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['bill_sundry_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'billsundry');

				$this->db->table($table)
				->where('bill_sundry_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'billsundry',$mst_base_id);
			}
		}
	}

	function create_item_group_base_id()
	{
		$table = $this->comp_id.'_itemgrpmst_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['item_grp_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'itemgrpmst');

				$this->db->table($table)
				->where('item_grp_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'itemgrpmst',$mst_base_id);
			}
		}
	}
	function create_item_category_base_id()
	{
		$table = $this->comp_id.'_itemcatmst_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['icatgms_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'itemcatmst');

				$this->db->table($table)
				->where('icatgms_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'itemcatmst',$mst_base_id);
			}
		}
	}
	function create_item_base_id()
	{
		$table = $this->comp_id.'_itemmaster_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['item_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'itemmaster');

				$this->db->table($table)
				->where('item_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'itemmaster',$mst_base_id);
			}
		}
	}

	function create_item_batch_base_id()
	{
		$table = $this->comp_id.'_itmbatchmt_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['batch_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'itmbatchmt');

				$this->db->table($table)
				->where('batch_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'itmbatchmt',$mst_base_id);
			}
		}
	}

	function create_unit_base_id()
	{
		$table = $this->comp_id.'_itmunitmst_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['unit_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'itmunitmst');

				$this->db->table($table)
				->where('unit_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'itmunitmst',$mst_base_id);
			}
		}
	}
	function create_mc_group_base_id()
	{
		$table = $this->comp_id.'_mcgrpmstnn_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['mc_grp_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'mcgrpmstnn');

				$this->db->table($table)
				->where('mc_grp_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'mcgrpmstnn',$mst_base_id);
			}
		}
	}
	function create_mc_base_id()
	{
		$table = $this->comp_id.'_mcmasternn_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['mat_cent_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'mcmasternn');

				$this->db->table($table)
				->where('mat_cent_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'mcmasternn',$mst_base_id);
			}
		}
	}

	function create_bill_of_material_base_id()
	{
		$table = $this->comp_id.'_billofmatn_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['bom_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'billofmatn');

				$this->db->table($table)
				->where('bom_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'billofmatn',$mst_base_id);
			}
		}
	}
	function create_barcode_base_id()
	{
		$table = $this->comp_id.'_barcodemst_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['barcode_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'barcodemst');

				$this->db->table($table)
				->where('barcode_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'barcodemst',$mst_base_id);
			}
		}
	}
	function create_label_base_id()
	{
		$table = $this->comp_id.'_labelmastr_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['label_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'labelmastr');

				$this->db->table($table)
				->where('label_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'labelmastr',$mst_base_id);
			}
		}
	}

	function create_cost_center_group_base_id()
	{
		$table = $this->comp_id.'_costctgrup_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['cc_grp_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'costctgrup');

				$this->db->table($table)
				->where('cc_grp_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'costctgrup',$mst_base_id);
			}
		}
	}
	function create_cost_center_base_id()
	{
		$table = $this->comp_id.'_costctmstr_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['cc_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'costctmstr');

				$this->db->table($table)
				->where('cc_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'costctmstr',$mst_base_id);
			}
		}
	}
	function create_bill_by_bill_base_id()
	{
		$table = $this->comp_id.'_billmaster_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['bills_ref_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'billmaster');

				$this->db->table($table)
				->where('bills_ref_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'billmaster',$mst_base_id);
			}
		}
	}

	function create_project_group_base_id()
	{
		$table = $this->comp_id.'_projectgrp_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['project_grp_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'projectgrp');

				$this->db->table($table)
				->where('project_grp_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'projectgrp',$mst_base_id);
			}
		}
	}
	function create_project_base_id()
	{
		$table = $this->comp_id.'_projectmst_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['project_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'projectmst');

				$this->db->table($table)
				->where('project_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'projectmst',$mst_base_id);
			}
		}
	}
	function create_print_base_id()
	{
		$table = $this->comp_id.'_prntconfig_'.$this->fy_id;
		$result = $this->db->table($table)
		->get()->getResultArray();

		foreach ($result as $key => $value) {
			$id = $value['prntconfig_id'];

			if($value['mst_base_id'] == 0)
			{
				$mst_base_id = $this->get_mst_base_id($id,'prntconfig');

				$this->db->table($table)
				->where('prntconfig_id',$id)
				->update(['mst_base_id' => $mst_base_id]);
			}
			else{
				$mst_base_id = $value['mst_base_id'];
				$this->create_comp_fy_mst_map($id,'prntconfig',$mst_base_id);
			}
		}
	}

	function get_mst_base_id($mst_fy_id,$mst_type) // 32 acc
	{
		$table = $this->comp_id.'_cofymstmap';
		$cofymstmap = $this->uuid_db->table($table)
											->select('mst_base_id')
											->where('comp_id',$this->comp_id)
											->where('uuid',$this->uuid)
											->where('comp_fy_id',$this->fy_id)
											->where('mst_fy_id',$mst_fy_id)
											->where('mst_type',$mst_type)
											->get()->getRowArray();
		if($cofymstmap){
			return $cofymstmap['mst_base_id'];
		}

		return $this->create_mst_base_id($mst_fy_id,$mst_type);
		
	}

function bbget_mst_base_id($mst_fy_id,$mst_type) // 32 acc
	{
		$table = $this->comp_id.'_cofymstmap';
		$cofymstmap = $this->uuid_db->table($table)
											->select('mst_base_id')
											->where('comp_id',$this->comp_id)
											->where('uuid',$this->uuid)
											->where('comp_fy_id',$this->fy_id)
											->where('mst_fy_id',$mst_fy_id)
											->where('mst_type',$mst_type)
											->get()->getRowArray();
		if($cofymstmap){
			return $cofymstmap['mst_base_id'];
		}

		
		
	}


	function create_mst_base_id($mst_fy_id,$mst_type)
	{
		$data = [
			'comp_id'		=> $this->comp_id,
			'mst_type'	    => $mst_type,
			'fy_id'			=> $this->fy_id,
			'uuid'          => $this->uuid
		];
		$table = $this->comp_id.'_baseidgenrt';
		$this->uuid_db->table($table)->insert($data);
		$mst_base_id = $this->uuid_db->insertID();

		$this->create_comp_fy_mst_map($mst_fy_id,$mst_type,$mst_base_id);

		return $mst_base_id;
	}
	function create_comp_fy_mst_map($mst_fy_id,$mst_type,$mst_base_id,$fy_id=0)
	{
		if($fy_id == 0){
			$fy_id = $this->fy_id;
		} 

		$table = $this->comp_id.'_cofymstmap';

		$cofymstmap = $this->uuid_db->table($table)
											->select('mst_base_id')
											->where('comp_id',$this->comp_id)
											->where('comp_fy_id',$fy_id)
											->where('uuid',$this->uuid)
											->where('mst_base_id',$mst_base_id)
											->where('mst_fy_id',$mst_fy_id)
											->where('mst_type',$mst_type)
											->get()->getRowArray();
		if($cofymstmap){
			return $cofymstmap['mst_base_id'];
		}		
		$data = [
			'comp_id'		    => $this->comp_id,
			'comp_fy_id'		=> $fy_id,
			'mst_type'			=> $mst_type,
			'mst_base_id'		=> $mst_base_id,
			'mst_fy_id'			=> $mst_fy_id,
			'uuid'              => $this->uuid
		   ];		
		$this->uuid_db->table($table)->insert($data);
	}

	function delete_comp_fy_mst_map($fy_id = 0)
	{
		if($fy_id == 0){
			$fy_id = $this->fy_id;
		}

		$table = $this->comp_id.'_baseidgenrt';
		$this->uuid_db->table($table)
		->where('comp_id',$this->comp_id)
		->where('uuid',$this->uuid)
		->where('fy_id',$fy_id)
		->delete();

		$table = $this->comp_id.'_cofymstmap';
		$this->uuid_db->table($table)
		->where('comp_id',$this->comp_id)
		->where('uuid',$this->uuid)
		->where('comp_fy_id',$fy_id)
		->delete();
	}

	function delete_comp_fy_mst_map_by_id($mst_fy_id,$mst_type)
	{		
		$table = $this->comp_id.'_cofymstmap';
		$this->uuid_db->table($table)
							->where('comp_id',$this->comp_id)
							->where('uuid',$this->uuid)
							->where('comp_fy_id',$this->fy_id)
							->where('mst_fy_id',$mst_fy_id)
							->where('mst_type',$mst_type)
							->delete();
	}

	function check_comp_fy_mst_map($mst_fy_id,$mst_type,$fy_id)
	{		
		$table = $this->comp_id.'_cofymstmap';
		$cofymstmap = $this->uuid_db->table($table)
											->select('*')
											->where('comp_id',$this->comp_id)
											->where('uuid',$this->uuid)
											->where('comp_fy_id',$this->fy_id)
											->where('mst_fy_id',$mst_fy_id)
											->where('mst_type',$mst_type)
											->get()->getRowArray();
											
		if($cofymstmap){
			$mst_base_id = $cofymstmap['mst_base_id'];
			$cofymstmap2 = $this->uuid_db->table($table)
												->select('*')
												->where('comp_id',$this->comp_id)
												->where('uuid',$this->uuid)
												->where('comp_fy_id',$fy_id)
												->where('mst_base_id',$mst_base_id)
												->where('mst_type',$mst_type)
												->get()->getRowArray();
			
			if($cofymstmap2){
				return $cofymstmap2['mst_fy_id'];
			}
		}
		return 0;
	}


	function mst_base_id($mst_fy_id,$mst_type,$nxt_comp_fy_id=0,$nxt_mst_fy_id=0)
	{
		if($nxt_comp_fy_id != 0 && $nxt_mst_fy_id != 0){
			$table = $this->comp_id.'_cofymstmap';
			$cofymstmap = $this->uuid_db->table($table)
												->select('mst_base_id')
												->where('comp_id',$this->comp_id)
												->where('uuid',$this->uuid)
												->where('comp_fy_id',$nxt_comp_fy_id)
												->where('mst_fy_id',$nxt_mst_fy_id)
												->where('mst_type',$mst_type)
												->get()->getRowArray();
			if($cofymstmap){
				return $cofymstmap['mst_base_id'];
			}
			else{
				$cofymstmap2 = $this->uuid_db->table($table)
													->select('mst_base_id')
													->where('comp_id',$this->comp_id)
													->where('uuid',$this->uuid)
													->where('comp_fy_id',$this->fy_id)
													->where('mst_fy_id',$mst_fy_id)
													->where('mst_type',$mst_type)
													->get()->getRowArray();
				if($cofymstmap2){
					$this->create_comp_fy_mst_map($nxt_mst_fy_id,$mst_type,$cofymstmap2['mst_base_id'],$nxt_comp_fy_id);
					return $cofymstmap2['mst_base_id'];
				}

				$mst_base_id = $this->create_mst_base_id($mst_fy_id,$mst_type);
				$this->create_comp_fy_mst_map($nxt_mst_fy_id,$mst_type,$mst_base_id,$nxt_comp_fy_id);

				return $mst_base_id;
			}
		}
		else{
			$table = $this->comp_id.'_cofymstmap';
			$cofymstmap2 = $this->uuid_db->table($table)
												->select('mst_base_id')
												->where('comp_id',$this->comp_id)
												->where('uuid',$this->uuid)
												->where('comp_fy_id',$this->fy_id)
												->where('mst_fy_id',$mst_fy_id)
												->where('mst_type',$mst_type)
												->get()->getRowArray();
			if($cofymstmap2){
				return $cofymstmap2['mst_base_id'];
			}

			$mst_base_id = $this->create_mst_base_id($mst_fy_id,$mst_type);

			return $mst_base_id;
		}	
	}
}