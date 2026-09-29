<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables;
use App\Libraries\ERPtables;
use App\Libraries\cPanelApi;

class ScriptModel extends Model	{
	public function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		$this->db      		 =  $this->externaldb->get_company_db();		
		$this->session       =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');
		$this->enc_string    =  new enc_string();
		$this->cpanelapi     =  new cPanelApi();
    }
	function startsWith($string, $startString) { 
      $len = strlen($startString); 
      return (substr($string, 0, $len) === $startString); 
    } 	
	
	function default_tables_insert($uuid_unv_db,$company_id,$table_name,$short_table_name,$table_result,$uuid){
	 if($short_table_name=='baseidgenrt'){
		if($table_result){
			$table_result= json_decode($table_result,true);
			 if($table_result){
			  foreach($table_result as $rows){
			   $inserted_data = array('uuid'=>$uuid,'comp_id'=>$company_id,'fy_id'=>$rows['fy_id'],'mst_type'=>$rows['mst_type']);	
			   $uuid_unv_db->table($table_name)->insert($inserted_data);	
			  }
			}			 
		 } 
	   } 
	 
	 if($short_table_name=='cofymstmap'){
		if($table_result){
			$table_result= json_decode($table_result,true);
            if($table_result){			
			foreach($table_result as $rows){
			   $cinserted_data = array('uuid'=>$uuid,'comp_id'=>$company_id,'comp_fy_id'=>$rows['comp_fy_id'],'mst_type'=>$rows['mst_type'],'mst_base_id'=>$rows['mst_base_id'],'mst_fy_id'=>$rows['mst_fy_id']);	
			   $uuid_unv_db->table($table_name)->insert($cinserted_data);
			 }	
			}
			
            		
		 } 
	   }	 
		
	 }
	
	function create_default_tables($uuid_unv_db,$comp_id) // 2 tables
	{
		$errors = [];
		// 23 masters
		$table = $comp_id.'_baseidgenrt';  	
		try{			
				$uuid_unv_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`mst_base_id` BIGINT NOT NULL AUTO_INCREMENT ,
					`uuid` BIGINT NOT NULL ,	
					`comp_id` BIGINT NOT NULL ,
					`fy_id` BIGINT NOT NULL ,
					`mst_type` VARCHAR(10) NOT NULL ,					
					PRIMARY KEY (`mst_base_id`)
				) ENGINE=InnoDB ;");
			
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}

		$table = $comp_id.'_cofymstmap';  	
		try{			
			$uuid_unv_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
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
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
				
		$table = $comp_id.'_usrrights';  	
		try{
				$uuid_unv_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`usr_right_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`uuid` BIGINT NOT NULL ,
					`usr_config_id` BIGINT NOT NULL ,
					`voucher_series_id` BIGINT NOT NULL ,
					`usr_rights` VARCHAR(100) NOT NULL ,
					`accessprof_id` BIGINT NOT NULL ,
					PRIMARY KEY (`usr_right_id`)
				) ENGINE=InnoDB ;");
			

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		$table = $comp_id.'_accessprof';  	
		try{
				$uuid_unv_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`accessprof_id` BIGINT NOT NULL AUTO_INCREMENT , 
					`uuid` BIGINT NOT NULL ,
					`accessprof_name` VARCHAR(100) NOT NULL ,
					`accessprof_desg` VARCHAR(100) NOT NULL ,	
					PRIMARY KEY (`accessprof_id`)
				) ENGINE=InnoDB ;");
			

		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}
		
		return $errors;
	}
	function paginateArray($arr, $pageSize, $pageNumber) {
    // Calculate starting index of the current page
    $startIndex = ($pageNumber - 1) * $pageSize;

    // Slice the array based on page size and page number
    return array_slice($arr, $startIndex, $pageSize);
}
function getPaginationInfo($arr, $pageSize) {
    $totalItems = count($arr);
    $totalPages = ceil($totalItems / $pageSize);
    return ['totalItems' => $totalItems, 'totalPages' => $totalPages];
}
    function uuid_dbs_convert(){
		//$uuid_unv_db = $this->externaldb->connect_universal_uuid_db();
		//echo $response    = $this->cpanelapi->TotalDataBaseMySQL();
		//die();
		$response    = '{"errors":null,"data":[{"users":["aicountlyin_uaicountly"],"disk_usage":4643464,"database":"aicountlyin_aicountly"},{"users":["aicountlyin_contactaic"],"disk_usage":589824,"database":"aicountlyin_contactaic"},{"disk_usage":1017816,"database":"aicountlyin_erp","users":["aicountlyin_uerp"]},{"users":["aicountlyin_uerp0000000"],"database":"aicountlyin_erp0000000","disk_usage":1225771},{"users":["aicountlyin_uerp0000001"],"disk_usage":5529347,"database":"aicountlyin_erp0000001"},{"disk_usage":15415196,"database":"aicountlyin_erp0000002","users":["aicountlyin_uerp0000002"]},{"database":"aicountlyin_erp0000003","disk_usage":37238937,"users":["aicountlyin_uerp0000003"]},{"users":["aicountlyin_uerp0000004"],"database":"aicountlyin_erp0000004","disk_usage":18325625},{"users":["aicountlyin_uerp0000005"],"database":"aicountlyin_erp0000005","disk_usage":11559694},{"users":["aicountlyin_uerp0000006"],"disk_usage":8605469,"database":"aicountlyin_erp0000006"},{"users":["aicountlyin_uerp0000007"],"disk_usage":3006201,"database":"aicountlyin_erp0000007"},{"users":["aicountlyin_uerp0000009"],"database":"aicountlyin_erp0000009","disk_usage":6756459},{"users":["aicountlyin_uerp0000010"],"disk_usage":50321536,"database":"aicountlyin_erp0000010"},{"disk_usage":3261777,"database":"aicountlyin_erp0000011","users":["aicountlyin_uerp0000011"]},{"users":["aicountlyin_uerp0000012"],"database":"aicountlyin_erp0000012","disk_usage":622988791},{"disk_usage":11454133,"database":"aicountlyin_erp0000013","users":["aicountlyin_uerp0000013"]},{"database":"aicountlyin_erp0000014","disk_usage":5808709,"users":["aicountlyin_uerp0000014"]},{"users":["aicountlyin_uerp0000015"],"disk_usage":3176866,"database":"aicountlyin_erp0000015"},{"users":["aicountlyin_uerp0000016"],"database":"aicountlyin_erp0000016","disk_usage":3873970},{"disk_usage":3242402,"database":"aicountlyin_erp0000017","users":["aicountlyin_uerp0000017"]},{"database":"aicountlyin_erp0000018","disk_usage":6143610,"users":["aicountlyin_uerp0000018"]},{"disk_usage":3893396,"database":"aicountlyin_erp0000019","users":["aicountlyin_uerp0000019"]},{"database":"aicountlyin_erp0000020","disk_usage":3340706,"users":["aicountlyin_uerp0000020"]},{"disk_usage":3411406,"database":"aicountlyin_erp0000021","users":["aicountlyin_uerp0000021"]},{"disk_usage":3406242,"database":"aicountlyin_erp0000022","users":["aicountlyin_uerp0000022"]},{"users":["aicountlyin_uerp0000023"],"disk_usage":3571858,"database":"aicountlyin_erp0000023"},{"disk_usage":4041707,"database":"aicountlyin_erp0000024","users":["aicountlyin_uerp0000024"]},{"database":"aicountlyin_erp0000025","disk_usage":15577717,"users":["aicountlyin_uerp0000025"]},{"users":["aicountlyin_uerp0000026"],"disk_usage":3537314,"database":"aicountlyin_erp0000026"},{"disk_usage":15877924,"database":"aicountlyin_erp0000027","users":["aicountlyin_uerp0000027"]},{"database":"aicountlyin_erp0000028","disk_usage":5240381,"users":["aicountlyin_uerp0000028"]},{"users":["aicountlyin_uerp0000029"],"disk_usage":5274089,"database":"aicountlyin_erp0000029"},{"users":["aicountlyin_uerp0000030"],"database":"aicountlyin_erp0000030","disk_usage":4860056},{"disk_usage":5010154,"database":"aicountlyin_erp0000031","users":["aicountlyin_uerp0000031"]},{"users":["aicountlyin_uerp0000032"],"database":"aicountlyin_erp0000032","disk_usage":4409893},{"users":["aicountlyin_uerp0000033"],"disk_usage":3766690,"database":"aicountlyin_erp0000033"},{"disk_usage":3799458,"database":"aicountlyin_erp0000034","users":["aicountlyin_uerp0000034"]},{"database":"aicountlyin_erp0000035","disk_usage":3832226,"users":["aicountlyin_uerp0000035"]},{"users":["aicountlyin_uerp0000036"],"database":"aicountlyin_erp0000036","disk_usage":3864994},{"users":["aicountlyin_uerp0000037"],"disk_usage":11139832,"database":"aicountlyin_erp0000037"},{"users":["aicountlyin_uerp0000038"],"database":"aicountlyin_erp0000038","disk_usage":3689543},{"disk_usage":3722311,"database":"aicountlyin_erp0000039","users":["aicountlyin_uerp0000039"]},{"database":"aicountlyin_erp0000044","disk_usage":3755079,"users":["aicountlyin_uerp0000044"]},{"users":["aicountlyin_uerp0000045"],"disk_usage":4028634,"database":"aicountlyin_erp0000045"},{"users":["aicountlyin_uerp0000048"],"database":"aicountlyin_erp0000048","disk_usage":4155361},{"users":["aicountlyin_uerp0000049"],"database":"aicountlyin_erp0000049","disk_usage":4186059},{"users":["aicountlyin_uerp0000051"],"database":"aicountlyin_erp0000051","disk_usage":5407289},{"database":"aicountlyin_erp0000052","disk_usage":4157006,"users":["aicountlyin_uerp0000052"]},{"users":["aicountlyin_uerp0000053"],"database":"aicountlyin_erp0000053","disk_usage":4189774},{"users":["aicountlyin_uerp0000054"],"disk_usage":8770218,"database":"aicountlyin_erp0000054"},{"users":["aicountlyin_uerp0000055"],"database":"aicountlyin_erp0000055","disk_usage":9088900},{"users":["aicountlyin_uerp0000056"],"database":"aicountlyin_erp0000056","disk_usage":2715050},{"database":"aicountlyin_erp0000057","disk_usage":2715214,"users":["aicountlyin_uerp0000057"]},{"users":["aicountlyin_uerp0000058"],"database":"aicountlyin_erp0000058","disk_usage":8949755},{"users":["aicountlyin_uerp0000059"],"disk_usage":7194025,"database":"aicountlyin_erp0000059"},{"users":["aicountlyin_uerp0000076"],"disk_usage":20511203,"database":"aicountlyin_erp0000076"},{"disk_usage":17553605,"database":"aicountlyin_erp0000077","users":["aicountlyin_uerp0000077"]},{"users":["aicountlyin_uerp0000078"],"disk_usage":4241875,"database":"aicountlyin_erp0000078"},{"disk_usage":2769310,"database":"aicountlyin_erp0000079","users":["aicountlyin_uerp0000079"]},{"users":["aicountlyin_uerp0000080"],"database":"aicountlyin_erp0000080","disk_usage":6042533},{"users":["aicountlyin_uerp0000089"],"database":"aicountlyin_erp0000089","disk_usage":2690651},{"database":"aicountlyin_erp0000090","disk_usage":2989416,"users":["aicountlyin_uerp0000090"]},{"users":["aicountlyin_uerp0000091"],"database":"aicountlyin_erp0000091","disk_usage":3039149},{"users":["aicountlyin_uerp0000092"],"disk_usage":2806992,"database":"aicountlyin_erp0000092"},{"disk_usage":2690651,"database":"aicountlyin_erp0000093","users":["aicountlyin_uerp0000093"]},{"users":["aicountlyin_uerp0000098"],"database":"aicountlyin_erp0000098","disk_usage":3974142},{"users":["aicountlyin_uerp0000099"],"database":"aicountlyin_erp0000099","disk_usage":3101985},{"database":"aicountlyin_erp0000100","disk_usage":5691172,"users":["aicountlyin_uerp0000100"]},{"users":["aicountlyin_uerp0000103"],"database":"aicountlyin_erp0000103","disk_usage":18639725},{"database":"aicountlyin_erp0000104","disk_usage":12580075,"users":["aicountlyin_uerp0000104"]},{"disk_usage":10064445,"database":"aicountlyin_erp0000105","users":["aicountlyin_uerp0000105"]},{"users":["aicountlyin_uerp0000106"],"disk_usage":11590702,"database":"aicountlyin_erp0000106"},{"database":"aicountlyin_erp0000112","disk_usage":4488570,"users":["aicountlyin_uerp0000112"]},{"database":"aicountlyin_erp0000113","disk_usage":3317149,"users":["aicountlyin_uerp0000113"]},{"database":"aicountlyin_erp0000114","disk_usage":4140964,"users":["aicountlyin_uerp0000114"]},{"users":["aicountlyin_uerp0000115"],"disk_usage":4513227,"database":"aicountlyin_erp0000115"},{"users":["aicountlyin_uerp0000116"],"database":"aicountlyin_erp0000116","disk_usage":3712867},{"users":["aicountlyin_uerp0000117"],"database":"aicountlyin_erp0000117","disk_usage":3580088},{"users":["aicountlyin_uerp0000118"],"database":"aicountlyin_erp0000118","disk_usage":3149662},{"database":"aicountlyin_erp0000119","disk_usage":2786861,"users":["aicountlyin_uerp0000119"]},{"users":["aicountlyin_uerp0000122"],"disk_usage":3317493,"database":"aicountlyin_erp0000122"},{"users":["aicountlyin_uerp0000123"],"disk_usage":201728,"database":"aicountlyin_erp0000123"},{"users":["aicountlyin_uerp0000127"],"database":"aicountlyin_erp0000127","disk_usage":201728},{"disk_usage":1246595,"database":"aicountlyin_erp0000128","users":["aicountlyin_uerp0000128"]},{"disk_usage":1246595,"database":"aicountlyin_erp0000132","users":["aicountlyin_uerp0000132"]},{"users":["aicountlyin_uerp0000136"],"database":"aicountlyin_erp0000136","disk_usage":18688267},{"users":["aicountlyin_uerp0000137"],"disk_usage":58711082,"database":"aicountlyin_erp0000137"},{"users":["aicountlyin_uerp0000138"],"database":"aicountlyin_erp0000138","disk_usage":19188282},{"users":["aicountlyin_uerp0000139"],"database":"aicountlyin_erp0000139","disk_usage":6038590},{"users":["aicountlyin_uerp0000140"],"disk_usage":2737116,"database":"aicountlyin_erp0000140"},{"database":"aicountlyin_erp0000141","disk_usage":3240222,"users":["aicountlyin_uerp0000141"]},{"users":["aicountlyin_uerp0000151"],"database":"aicountlyin_erp0000151","disk_usage":7667712},{"users":["aicountlyin_uerp0000157"],"database":"aicountlyin_erp0000157","disk_usage":3424256},{"users":["aicountlyin_uerp0000158"],"disk_usage":6684672,"database":"aicountlyin_erp0000158"},{"database":"aicountlyin_erp0000159","disk_usage":3489792,"users":["aicountlyin_uerp0000159"]},{"database":"aicountlyin_erp0000160","disk_usage":3948544,"users":["aicountlyin_uerp0000160"]},{"users":["aicountlyin_uerp0000161"],"disk_usage":3817472,"database":"aicountlyin_erp0000161"},{"database":"aicountlyin_erp0000162","disk_usage":6176768,"users":["aicountlyin_uerp0000162"]},{"disk_usage":4898816,"database":"aicountlyin_erp0000163","users":["aicountlyin_uerp0000163"]},{"database":"aicountlyin_erp0000164","disk_usage":4227072,"users":["aicountlyin_uerp0000164"]},{"disk_usage":3522560,"database":"aicountlyin_erp0000165","users":["aicountlyin_uerp0000165"]},{"users":["aicountlyin_uerp0000166"],"disk_usage":3473408,"database":"aicountlyin_erp0000166"},{"database":"aicountlyin_erp0000167","disk_usage":44122112,"users":["aicountlyin_uerp0000167"]},{"database":"aicountlyin_erp0000168","disk_usage":8454144,"users":["aicountlyin_uerp0000168"]},{"users":["aicountlyin_uerp0000169"],"disk_usage":3473408,"database":"aicountlyin_erp0000169"},{"disk_usage":3784704,"database":"aicountlyin_erp0000171","users":["aicountlyin_uerp0000171"]},{"users":["aicountlyin_uerp0000172"],"database":"aicountlyin_erp0000172","disk_usage":3440640},{"users":["aicountlyin_uerp0000173"],"disk_usage":3670016,"database":"aicountlyin_erp0000173"},{"users":["aicountlyin_uerp0000177"],"database":"aicountlyin_erp0000177","disk_usage":43499520},{"disk_usage":3375104,"database":"aicountlyin_erp0000183","users":["aicountlyin_uerp0000183"]},{"users":["aicountlyin_uerp0000184"],"database":"aicountlyin_erp0000184","disk_usage":3358720},{"users":["aicountlyin_uerp0000185"],"database":"aicountlyin_erp0000185","disk_usage":3358720},{"disk_usage":3702784,"database":"aicountlyin_erp0000190","users":["aicountlyin_uerp0000190"]},{"users":["aicountlyin_uerp0000191"],"disk_usage":3522560,"database":"aicountlyin_erp0000191"},{"database":"aicountlyin_erp0000192","disk_usage":3620864,"users":["aicountlyin_uerp0000192"]},{"users":["aicountlyin_uerp0000193"],"database":"aicountlyin_erp0000193","disk_usage":6979584},{"disk_usage":3358720,"database":"aicountlyin_erp0000194","users":["aicountlyin_uerp0000194"]},{"users":["aicountlyin_uerp0000196"],"database":"aicountlyin_erp0000196","disk_usage":3686400},{"users":["aicountlyin_uerp0000197"],"database":"aicountlyin_erp0000197","disk_usage":5619712},{"users":["aicountlyin_uerp0000198"],"disk_usage":3358720,"database":"aicountlyin_erp0000198"},{"users":["aicountlyin_uerp0000200"],"database":"aicountlyin_erp0000200","disk_usage":3440640},{"users":["aicountlyin_uerp0000201"],"database":"aicountlyin_erp0000201","disk_usage":3522560},{"disk_usage":3670016,"database":"aicountlyin_erp0000202","users":["aicountlyin_uerp0000202"]},{"users":["aicountlyin_uerp0000203"],"disk_usage":3653632,"database":"aicountlyin_erp0000203"},{"database":"aicountlyin_erp0000204","disk_usage":3538944,"users":["aicountlyin_uerp0000204"]},{"database":"aicountlyin_erp0000205","disk_usage":3702784,"users":["aicountlyin_uerp0000205"]},{"users":["aicountlyin_uerp0000206"],"disk_usage":3358720,"database":"aicountlyin_erp0000206"},{"users":["aicountlyin_uerp0000207"],"disk_usage":5177344,"database":"aicountlyin_erp0000207"},{"users":["aicountlyin_uerp0000210"],"disk_usage":3375104,"database":"aicountlyin_erp0000210"},{"disk_usage":3440640,"database":"aicountlyin_erp0000213","users":["aicountlyin_uerp0000213"]},{"users":["aicountlyin_uerp0000214"],"disk_usage":3489792,"database":"aicountlyin_erp0000214"},{"database":"aicountlyin_erp0000215","disk_usage":4194304,"users":["aicountlyin_uerp0000215"]},{"database":"aicountlyin_erp0000218","disk_usage":3358720,"users":["aicountlyin_uerp0000218"]},{"database":"aicountlyin_erp0000219","disk_usage":3358720,"users":["aicountlyin_uerp0000219"]},{"users":["aicountlyin_uerp0000220"],"database":"aicountlyin_erp0000220","disk_usage":3358720},{"database":"aicountlyin_erp0000221","disk_usage":3883008,"users":["aicountlyin_uerp0000221"]},{"users":["aicountlyin_uerp0000222"],"disk_usage":3407872,"database":"aicountlyin_erp0000222"},{"disk_usage":3801088,"database":"aicountlyin_erp0000223","users":["aicountlyin_uerp0000223"]},{"disk_usage":3473408,"database":"aicountlyin_erp0000224","users":["aicountlyin_uerp0000224"]},{"database":"aicountlyin_erp0000225","disk_usage":7340032,"users":["aicountlyin_uerp0000225"]},{"disk_usage":6586368,"database":"aicountlyin_erp0000226","users":["aicountlyin_uerp0000226"]},{"users":["aicountlyin_uerp0000227"],"disk_usage":4292608,"database":"aicountlyin_erp0000227"},{"users":["aicountlyin_uerp0000228"],"disk_usage":3457024,"database":"aicountlyin_erp0000228"},{"users":["aicountlyin_uerp0000229"],"disk_usage":3391488,"database":"aicountlyin_erp0000229"},{"users":["aicountlyin_uerp0000230"],"database":"aicountlyin_erp0000230","disk_usage":3391488},{"users":["aicountlyin_uerp0000231"],"disk_usage":3391488,"database":"aicountlyin_erp0000231"},{"disk_usage":3375104,"database":"aicountlyin_erp0000257","users":["aicountlyin_uerp0000257"]},{"users":["aicountlyin_uerp0000260"],"database":"aicountlyin_erp0000260","disk_usage":3440640},{"users":["aicountlyin_ugrp0000009"],"disk_usage":114688,"database":"aicountlyin_grp0000009"},{"users":["aicountlyin_ugrp0000014"],"disk_usage":114688,"database":"aicountlyin_grp0000014"},{"disk_usage":212992,"database":"aicountlyin_grp0000016","users":["aicountlyin_ugrp0000016"]},{"users":["aicountlyin_hr"],"disk_usage":370496,"database":"aicountlyin_hr"},{"users":["aicountlyin_hrms0000153"],"disk_usage":0,"database":"aicountlyin_hrms0000153"},{"users":["aicountlyin_uourpeoplen"],"database":"aicountlyin_ourpeoplen","disk_usage":16384},{"database":"aicountlyin_pos","disk_usage":277168500,"users":["aicountlyin_pos"]},{"disk_usage":16384,"database":"aicountlyin_profsindia","users":["aicountlyin_uprofsindia"]},{"database":"aicountlyin_sisplDB","disk_usage":22626304,"users":["aicountlyin_sisplUser"]},{"users":["aicountlyin_usispl_admin"],"disk_usage":32768,"database":"aicountlyin_sispl_admin"},{"disk_usage":573440,"database":"aicountlyin_sispl_uuid","users":["aicountlyin_usispl_uuid"]},{"disk_usage":753664,"database":"aicountlyin_uuid_1","users":["aicountlyin_uuid_1_usr"]},{"users":["aicountlyin_uuid_1001_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1001"},{"users":["aicountlyin_uuid_1013_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1013"},{"disk_usage":65536,"database":"aicountlyin_uuid_1019","users":["aicountlyin_uuid_1019_usr"]},{"database":"aicountlyin_uuid_1042","disk_usage":65536,"users":["aicountlyin_uuid_1042_usr"]},{"users":["aicountlyin_uuid_1043_usr"],"database":"aicountlyin_uuid_1043","disk_usage":65536},{"users":["aicountlyin_uuid_1044_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1044"},{"database":"aicountlyin_uuid_1045","disk_usage":65536,"users":["aicountlyin_uuid_1045_usr"]},{"database":"aicountlyin_uuid_1046","disk_usage":65536,"users":["aicountlyin_uuid_1046_usr"]},{"users":["aicountlyin_uuid_1047_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1047"},{"database":"aicountlyin_uuid_1048","disk_usage":65536,"users":["aicountlyin_uuid_1048_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1049","users":["aicountlyin_uuid_1049_usr"]},{"database":"aicountlyin_uuid_1050","disk_usage":65536,"users":["aicountlyin_uuid_1050_usr"]},{"database":"aicountlyin_uuid_1051","disk_usage":65536,"users":["aicountlyin_uuid_1051_usr"]},{"database":"aicountlyin_uuid_1052","disk_usage":65536,"users":["aicountlyin_uuid_1052_usr"]},{"users":["aicountlyin_uuid_1053_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1053"},{"database":"aicountlyin_uuid_1054","disk_usage":65536,"users":["aicountlyin_uuid_1054_usr"]},{"users":["aicountlyin_uuid_1055_usr"],"database":"aicountlyin_uuid_1055","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1056","users":["aicountlyin_uuid_1056_usr"]},{"database":"aicountlyin_uuid_1057","disk_usage":65536,"users":["aicountlyin_uuid_1057_usr"]},{"database":"aicountlyin_uuid_1058","disk_usage":65536,"users":["aicountlyin_uuid_1058_usr"]},{"database":"aicountlyin_uuid_1059","disk_usage":65536,"users":["aicountlyin_uuid_1059_usr"]},{"users":["aicountlyin_uuid_1060_usr"],"database":"aicountlyin_uuid_1060","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1061","users":["aicountlyin_uuid_1061_usr"]},{"users":["aicountlyin_uuid_1062_usr"],"database":"aicountlyin_uuid_1062","disk_usage":65536},{"users":["aicountlyin_uuid_1063_usr"],"database":"aicountlyin_uuid_1063","disk_usage":65536},{"users":["aicountlyin_uuid_1064_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1064"},{"disk_usage":65536,"database":"aicountlyin_uuid_1065","users":["aicountlyin_uuid_1065_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1066","users":["aicountlyin_uuid_1066_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1067","users":["aicountlyin_uuid_1067_usr"]},{"users":["aicountlyin_uuid_1068_usr"],"database":"aicountlyin_uuid_1068","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1069","users":["aicountlyin_uuid_1069_usr"]},{"users":["aicountlyin_uuid_1070_usr"],"database":"aicountlyin_uuid_1070","disk_usage":65536},{"users":["aicountlyin_uuid_1071_usr"],"database":"aicountlyin_uuid_1071","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1072","users":["aicountlyin_uuid_1072_usr"]},{"users":["aicountlyin_uuid_1073_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1073"},{"database":"aicountlyin_uuid_1074","disk_usage":65536,"users":["aicountlyin_uuid_1074_usr"]},{"users":["aicountlyin_uuid_1075_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1075"},{"users":["aicountlyin_uuid_1076_usr"],"database":"aicountlyin_uuid_1076","disk_usage":65536},{"users":["aicountlyin_uuid_1077_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1077"},{"users":["aicountlyin_uuid_1078_usr"],"database":"aicountlyin_uuid_1078","disk_usage":65536},{"users":["aicountlyin_uuid_1079_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1079"},{"users":["aicountlyin_uuid_1080_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1080"},{"database":"aicountlyin_uuid_1081","disk_usage":65536,"users":["aicountlyin_uuid_1081_usr"]},{"database":"aicountlyin_uuid_1082","disk_usage":65536,"users":["aicountlyin_uuid_1082_usr"]},{"users":["aicountlyin_uuid_1083_usr"],"database":"aicountlyin_uuid_1083","disk_usage":65536},{"database":"aicountlyin_uuid_1084","disk_usage":65536,"users":["aicountlyin_uuid_1084_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1085","users":["aicountlyin_uuid_1085_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1086","users":["aicountlyin_uuid_1086_usr"]},{"database":"aicountlyin_uuid_1087","disk_usage":65536,"users":["aicountlyin_uuid_1087_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1088","users":["aicountlyin_uuid_1088_usr"]},{"database":"aicountlyin_uuid_1089","disk_usage":65536,"users":["aicountlyin_uuid_1089_usr"]},{"users":["aicountlyin_uuid_11_usr"],"database":"aicountlyin_uuid_11","disk_usage":163840},{"users":["aicountlyin_uuid_1107_usr"],"database":"aicountlyin_uuid_1107","disk_usage":65536},{"users":["aicountlyin_uuid_1108_usr"],"database":"aicountlyin_uuid_1108","disk_usage":65536},{"users":["aicountlyin_uuid_1109_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1109"},{"users":["aicountlyin_uuid_111_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_111"},{"disk_usage":65536,"database":"aicountlyin_uuid_1110","users":["aicountlyin_uuid_1110_usr"]},{"users":["aicountlyin_uuid_1111_usr"],"database":"aicountlyin_uuid_1111","disk_usage":65536},{"database":"aicountlyin_uuid_1112","disk_usage":65536,"users":["aicountlyin_uuid_1112_usr"]},{"database":"aicountlyin_uuid_1113","disk_usage":65536,"users":["aicountlyin_uuid_1113_usr"]},{"users":["aicountlyin_uuid_1114_usr"],"database":"aicountlyin_uuid_1114","disk_usage":65536},{"users":["aicountlyin_uuid_1115_usr"],"database":"aicountlyin_uuid_1115","disk_usage":65536},{"users":["aicountlyin_uuid_112_usr"],"database":"aicountlyin_uuid_112","disk_usage":131072},{"users":["aicountlyin_uuid_1128_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1128"},{"database":"aicountlyin_uuid_1129","disk_usage":65536,"users":["aicountlyin_uuid_1129_usr"]},{"database":"aicountlyin_uuid_113","disk_usage":131072,"users":["aicountlyin_uuid_113_usr"]},{"database":"aicountlyin_uuid_1130","disk_usage":65536,"users":["aicountlyin_uuid_1130_usr"]},{"users":["aicountlyin_uuid_1131_usr"],"database":"aicountlyin_uuid_1131","disk_usage":65536},{"users":["aicountlyin_uuid_1132_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1132"},{"users":["aicountlyin_uuid_1133_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1133"},{"disk_usage":65536,"database":"aicountlyin_uuid_1134","users":["aicountlyin_uuid_1134_usr"]},{"database":"aicountlyin_uuid_1135","disk_usage":65536,"users":["aicountlyin_uuid_1135_usr"]},{"users":["aicountlyin_uuid_1136_usr"],"database":"aicountlyin_uuid_1136","disk_usage":65536},{"users":["aicountlyin_uuid_1137_usr"],"database":"aicountlyin_uuid_1137","disk_usage":65536},{"users":["aicountlyin_uuid_1138_usr"],"database":"aicountlyin_uuid_1138","disk_usage":65536},{"users":["aicountlyin_uuid_1139_usr"],"database":"aicountlyin_uuid_1139","disk_usage":65536},{"users":["aicountlyin_uuid_114_usr"],"database":"aicountlyin_uuid_114","disk_usage":131072},{"database":"aicountlyin_uuid_1140","disk_usage":65536,"users":["aicountlyin_uuid_1140_usr"]},{"database":"aicountlyin_uuid_1141","disk_usage":65536,"users":["aicountlyin_uuid_1141_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1142","users":["aicountlyin_uuid_1142_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1143","users":["aicountlyin_uuid_1143_usr"]},{"users":["aicountlyin_uuid_1144_usr"],"database":"aicountlyin_uuid_1144","disk_usage":65536},{"database":"aicountlyin_uuid_1145","disk_usage":65536,"users":["aicountlyin_uuid_1145_usr"]},{"database":"aicountlyin_uuid_1146","disk_usage":65536,"users":["aicountlyin_uuid_1146_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1147","users":["aicountlyin_uuid_1147_usr"]},{"users":["aicountlyin_uuid_115_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_115"},{"database":"aicountlyin_uuid_116","disk_usage":163840,"users":["aicountlyin_uuid_116_usr"]},{"database":"aicountlyin_uuid_1167","disk_usage":65536,"users":["aicountlyin_uuid_1167_usr"]},{"disk_usage":131072,"database":"aicountlyin_uuid_117","users":["aicountlyin_uuid_117_usr"]},{"users":["aicountlyin_uuid_1171_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1171"},{"users":["aicountlyin_uuid_1172_usr"],"database":"aicountlyin_uuid_1172","disk_usage":65536},{"users":["aicountlyin_uuid_1173_usr"],"database":"aicountlyin_uuid_1173","disk_usage":65536},{"users":["aicountlyin_uuid_1174_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1174"},{"database":"aicountlyin_uuid_1175","disk_usage":65536,"users":["aicountlyin_uuid_1175_usr"]},{"database":"aicountlyin_uuid_1176","disk_usage":65536,"users":["aicountlyin_uuid_1176_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1177","users":["aicountlyin_uuid_1177_usr"]},{"users":["aicountlyin_uuid_1178_usr"],"database":"aicountlyin_uuid_1178","disk_usage":65536},{"users":["aicountlyin_uuid_1179_usr"],"database":"aicountlyin_uuid_1179","disk_usage":65536},{"users":["aicountlyin_uuid_118_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_118"},{"users":["aicountlyin_uuid_1180_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1180"},{"database":"aicountlyin_uuid_1181","disk_usage":65536,"users":["aicountlyin_uuid_1181_usr"]},{"database":"aicountlyin_uuid_1182","disk_usage":65536,"users":["aicountlyin_uuid_1182_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1199","users":["aicountlyin_uuid_1199_usr"]},{"database":"aicountlyin_uuid_12","disk_usage":131072,"users":["aicountlyin_uuid_12_usr"]},{"users":["aicountlyin_uuid_1200_usr"],"database":"aicountlyin_uuid_1200","disk_usage":65536},{"users":["aicountlyin_uuid_1201_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1201"},{"database":"aicountlyin_uuid_1202","disk_usage":65536,"users":["aicountlyin_uuid_1202_usr"]},{"users":["aicountlyin_uuid_1203_usr"],"database":"aicountlyin_uuid_1203","disk_usage":65536},{"users":["aicountlyin_uuid_1204_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1204"},{"database":"aicountlyin_uuid_1205","disk_usage":65536,"users":["aicountlyin_uuid_1205_usr"]},{"database":"aicountlyin_uuid_1206","disk_usage":65536,"users":["aicountlyin_uuid_1206_usr"]},{"database":"aicountlyin_uuid_1207","disk_usage":65536,"users":["aicountlyin_uuid_1207_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1208","users":["aicountlyin_uuid_1208_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1209","users":["aicountlyin_uuid_1209_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1210","users":["aicountlyin_uuid_1210_usr"]},{"users":["aicountlyin_uuid_1211_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1211"},{"users":["aicountlyin_uuid_1212_usr"],"database":"aicountlyin_uuid_1212","disk_usage":65536},{"users":["aicountlyin_uuid_1213_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1213"},{"users":["aicountlyin_uuid_1214_usr"],"database":"aicountlyin_uuid_1214","disk_usage":65536},{"users":["aicountlyin_uuid_1227_usr"],"database":"aicountlyin_uuid_1227","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1245","users":["aicountlyin_uuid_1245_usr"]},{"database":"aicountlyin_uuid_1247","disk_usage":65536,"users":["aicountlyin_uuid_1247_usr"]},{"users":["aicountlyin_uuid_1248_usr"],"database":"aicountlyin_uuid_1248","disk_usage":65536},{"users":["aicountlyin_uuid_1249_usr"],"database":"aicountlyin_uuid_1249","disk_usage":65536},{"disk_usage":131072,"database":"aicountlyin_uuid_125","users":["aicountlyin_uuid_125_usr"]},{"users":["aicountlyin_uuid_1250_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1250"},{"users":["aicountlyin_uuid_1251_usr"],"database":"aicountlyin_uuid_1251","disk_usage":65536},{"users":["aicountlyin_uuid_1252_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1252"},{"database":"aicountlyin_uuid_1253","disk_usage":65536,"users":["aicountlyin_uuid_1253_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1254","users":["aicountlyin_uuid_1254_usr"]},{"users":["aicountlyin_uuid_1255_usr"],"database":"aicountlyin_uuid_1255","disk_usage":65536},{"users":["aicountlyin_uuid_1256_usr"],"database":"aicountlyin_uuid_1256","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1257","users":["aicountlyin_uuid_1257_usr"]},{"users":["aicountlyin_uuid_1258_usr"],"database":"aicountlyin_uuid_1258","disk_usage":65536},{"users":["aicountlyin_uuid_1259_usr"],"database":"aicountlyin_uuid_1259","disk_usage":65536},{"database":"aicountlyin_uuid_1260","disk_usage":65536,"users":["aicountlyin_uuid_1260_usr"]},{"users":["aicountlyin_uuid_1261_usr"],"database":"aicountlyin_uuid_1261","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1262","users":["aicountlyin_uuid_1262_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1263","users":["aicountlyin_uuid_1263_usr"]},{"database":"aicountlyin_uuid_1264","disk_usage":65536,"users":["aicountlyin_uuid_1264_usr"]},{"users":["aicountlyin_uuid_1265_usr"],"database":"aicountlyin_uuid_1265","disk_usage":65536},{"users":["aicountlyin_uuid_1266_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1266"},{"users":["aicountlyin_uuid_1267_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1267"},{"users":["aicountlyin_uuid_1268_usr"],"database":"aicountlyin_uuid_1268","disk_usage":65536},{"users":["aicountlyin_uuid_1269_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1269"},{"disk_usage":65536,"database":"aicountlyin_uuid_1270","users":["aicountlyin_uuid_1270_usr"]},{"database":"aicountlyin_uuid_1271","disk_usage":65536,"users":["aicountlyin_uuid_1271_usr"]},{"users":["aicountlyin_uuid_1272_usr"],"database":"aicountlyin_uuid_1272","disk_usage":65536},{"users":["aicountlyin_uuid_1273_usr"],"database":"aicountlyin_uuid_1273","disk_usage":65536},{"users":["aicountlyin_uuid_1274_usr"],"database":"aicountlyin_uuid_1274","disk_usage":65536},{"users":["aicountlyin_uuid_1275_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1275"},{"users":["aicountlyin_uuid_1277_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1277"},{"users":["aicountlyin_uuid_1278_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1278"},{"users":["aicountlyin_uuid_1279_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1279"},{"users":["aicountlyin_uuid_1280_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1280"},{"disk_usage":65536,"database":"aicountlyin_uuid_1281","users":["aicountlyin_uuid_1281_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1282","users":["aicountlyin_uuid_1282_usr"]},{"users":["aicountlyin_uuid_1283_usr"],"database":"aicountlyin_uuid_1283","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1284","users":["aicountlyin_uuid_1284_usr"]},{"users":["aicountlyin_uuid_1285_usr"],"database":"aicountlyin_uuid_1285","disk_usage":65536},{"users":["aicountlyin_uuid_1286_usr"],"database":"aicountlyin_uuid_1286","disk_usage":65536},{"users":["aicountlyin_uuid_1287_usr"],"database":"aicountlyin_uuid_1287","disk_usage":65536},{"users":["aicountlyin_uuid_1288_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1288"},{"users":["aicountlyin_uuid_1289_usr"],"database":"aicountlyin_uuid_1289","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1290","users":["aicountlyin_uuid_1290_usr"]},{"users":["aicountlyin_uuid_1291_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1291"},{"users":["aicountlyin_uuid_1292_usr"],"database":"aicountlyin_uuid_1292","disk_usage":65536},{"users":["aicountlyin_uuid_1293_usr"],"database":"aicountlyin_uuid_1293","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1294","users":["aicountlyin_uuid_1294_usr"]},{"database":"aicountlyin_uuid_1295","disk_usage":65536,"users":["aicountlyin_uuid_1295_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1296","users":["aicountlyin_uuid_1296_usr"]},{"database":"aicountlyin_uuid_1297","disk_usage":65536,"users":["aicountlyin_uuid_1297_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1326","users":["aicountlyin_uuid_1326_usr"]},{"users":["aicountlyin_uuid_1327_usr"],"database":"aicountlyin_uuid_1327","disk_usage":65536},{"database":"aicountlyin_uuid_1328","disk_usage":65536,"users":["aicountlyin_uuid_1328_usr"]},{"database":"aicountlyin_uuid_1329","disk_usage":65536,"users":["aicountlyin_uuid_1329_usr"]},{"database":"aicountlyin_uuid_1330","disk_usage":65536,"users":["aicountlyin_uuid_1330_usr"]},{"users":["aicountlyin_uuid_1331_usr"],"database":"aicountlyin_uuid_1331","disk_usage":65536},{"users":["aicountlyin_uuid_1332_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1332"},{"database":"aicountlyin_uuid_1333","disk_usage":65536,"users":["aicountlyin_uuid_1333_usr"]},{"database":"aicountlyin_uuid_1334","disk_usage":65536,"users":["aicountlyin_uuid_1334_usr"]},{"database":"aicountlyin_uuid_1335","disk_usage":65536,"users":["aicountlyin_uuid_1335_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1336","users":["aicountlyin_uuid_1336_usr"]},{"users":["aicountlyin_uuid_1337_usr"],"database":"aicountlyin_uuid_1337","disk_usage":65536},{"database":"aicountlyin_uuid_1338","disk_usage":65536,"users":["aicountlyin_uuid_1338_usr"]},{"users":["aicountlyin_uuid_1339_usr"],"database":"aicountlyin_uuid_1339","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1340","users":["aicountlyin_uuid_1340_usr"]},{"database":"aicountlyin_uuid_1341","disk_usage":65536,"users":["aicountlyin_uuid_1341_usr"]},{"database":"aicountlyin_uuid_1342","disk_usage":65536,"users":["aicountlyin_uuid_1342_usr"]},{"database":"aicountlyin_uuid_1343","disk_usage":65536,"users":["aicountlyin_uuid_1343_usr"]},{"database":"aicountlyin_uuid_1344","disk_usage":65536,"users":["aicountlyin_uuid_1344_usr"]},{"users":["aicountlyin_uuid_1345_usr"],"database":"aicountlyin_uuid_1345","disk_usage":65536},{"database":"aicountlyin_uuid_1346","disk_usage":65536,"users":["aicountlyin_uuid_1346_usr"]},{"database":"aicountlyin_uuid_1347","disk_usage":65536,"users":["aicountlyin_uuid_1347_usr"]},{"database":"aicountlyin_uuid_1348","disk_usage":65536,"users":["aicountlyin_uuid_1348_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1349","users":["aicountlyin_uuid_1349_usr"]},{"users":["aicountlyin_uuid_1350_usr"],"database":"aicountlyin_uuid_1350","disk_usage":65536},{"database":"aicountlyin_uuid_1351","disk_usage":65536,"users":["aicountlyin_uuid_1351_usr"]},{"database":"aicountlyin_uuid_1352","disk_usage":65536,"users":["aicountlyin_uuid_1352_usr"]},{"users":["aicountlyin_uuid_1354_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1354"},{"database":"aicountlyin_uuid_1356","disk_usage":65536,"users":["aicountlyin_uuid_1356_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1357","users":["aicountlyin_uuid_1357_usr"]},{"users":["aicountlyin_uuid_1358_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1358"},{"database":"aicountlyin_uuid_1359","disk_usage":65536,"users":["aicountlyin_uuid_1359_usr"]},{"database":"aicountlyin_uuid_1360","disk_usage":65536,"users":["aicountlyin_uuid_1360_usr"]},{"users":["aicountlyin_uuid_1361_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1361"},{"users":["aicountlyin_uuid_1362_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1362"},{"users":["aicountlyin_uuid_1363_usr"],"database":"aicountlyin_uuid_1363","disk_usage":65536},{"database":"aicountlyin_uuid_1364","disk_usage":65536,"users":["aicountlyin_uuid_1364_usr"]},{"users":["aicountlyin_uuid_1365_usr"],"database":"aicountlyin_uuid_1365","disk_usage":65536},{"database":"aicountlyin_uuid_1366","disk_usage":65536,"users":["aicountlyin_uuid_1366_usr"]},{"users":["aicountlyin_uuid_1367_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1367"},{"disk_usage":65536,"database":"aicountlyin_uuid_1368","users":["aicountlyin_uuid_1368_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1369","users":["aicountlyin_uuid_1369_usr"]},{"database":"aicountlyin_uuid_1370","disk_usage":65536,"users":["aicountlyin_uuid_1370_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1371","users":["aicountlyin_uuid_1371_usr"]},{"database":"aicountlyin_uuid_1372","disk_usage":65536,"users":["aicountlyin_uuid_1372_usr"]},{"database":"aicountlyin_uuid_1373","disk_usage":65536,"users":["aicountlyin_uuid_1373_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1374","users":["aicountlyin_uuid_1374_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1375","users":["aicountlyin_uuid_1375_usr"]},{"users":["aicountlyin_uuid_1376_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1376"},{"disk_usage":65536,"database":"aicountlyin_uuid_1377","users":["aicountlyin_uuid_1377_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1378","users":["aicountlyin_uuid_1378_usr"]},{"users":["aicountlyin_uuid_1379_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1379"},{"database":"aicountlyin_uuid_1380","disk_usage":65536,"users":["aicountlyin_uuid_1380_usr"]},{"database":"aicountlyin_uuid_1381","disk_usage":65536,"users":["aicountlyin_uuid_1381_usr"]},{"users":["aicountlyin_uuid_1382_usr"],"database":"aicountlyin_uuid_1382","disk_usage":65536},{"users":["aicountlyin_uuid_1383_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1383"},{"users":["aicountlyin_uuid_1384_usr"],"database":"aicountlyin_uuid_1384","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1385","users":["aicountlyin_uuid_1385_usr"]},{"users":["aicountlyin_uuid_1386_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1386"},{"database":"aicountlyin_uuid_1387","disk_usage":65536,"users":["aicountlyin_uuid_1387_usr"]},{"users":["aicountlyin_uuid_1403_usr"],"database":"aicountlyin_uuid_1403","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1404","users":["aicountlyin_uuid_1404_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1405","users":["aicountlyin_uuid_1405_usr"]},{"users":["aicountlyin_uuid_1406_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1406"},{"users":["aicountlyin_uuid_1407_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1407"},{"database":"aicountlyin_uuid_1408","disk_usage":65536,"users":["aicountlyin_uuid_1408_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1409","users":["aicountlyin_uuid_1409_usr"]},{"database":"aicountlyin_uuid_141","disk_usage":131072,"users":["aicountlyin_uuid_141_usr"]},{"database":"aicountlyin_uuid_1410","disk_usage":65536,"users":["aicountlyin_uuid_1410_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1411","users":["aicountlyin_uuid_1411_usr"]},{"users":["aicountlyin_uuid_1412_usr"],"database":"aicountlyin_uuid_1412","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1457","users":["aicountlyin_uuid_1457_usr"]},{"users":["aicountlyin_uuid_1476_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1476"},{"database":"aicountlyin_uuid_1477","disk_usage":65536,"users":["aicountlyin_uuid_1477_usr"]},{"users":["aicountlyin_uuid_1478_usr"],"database":"aicountlyin_uuid_1478","disk_usage":65536},{"users":["aicountlyin_uuid_1479_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1479"},{"users":["aicountlyin_uuid_1480_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1480"},{"users":["aicountlyin_uuid_1481_usr"],"database":"aicountlyin_uuid_1481","disk_usage":65536},{"database":"aicountlyin_uuid_1482","disk_usage":65536,"users":["aicountlyin_uuid_1482_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1483","users":["aicountlyin_uuid_1483_usr"]},{"database":"aicountlyin_uuid_1484","disk_usage":65536,"users":["aicountlyin_uuid_1484_usr"]},{"users":["aicountlyin_uuid_1485_usr"],"database":"aicountlyin_uuid_1485","disk_usage":65536},{"users":["aicountlyin_uuid_1486_usr"],"database":"aicountlyin_uuid_1486","disk_usage":65536},{"users":["aicountlyin_uuid_1487_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1487"},{"users":["aicountlyin_uuid_1488_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1488"},{"disk_usage":65536,"database":"aicountlyin_uuid_1490","users":["aicountlyin_uuid_1490_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1491","users":["aicountlyin_uuid_1491_usr"]},{"users":["aicountlyin_uuid_1492_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1492"},{"database":"aicountlyin_uuid_1493","disk_usage":65536,"users":["aicountlyin_uuid_1493_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1494","users":["aicountlyin_uuid_1494_usr"]},{"database":"aicountlyin_uuid_1496","disk_usage":65536,"users":["aicountlyin_uuid_1496_usr"]},{"users":["aicountlyin_uuid_1501_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1501"},{"disk_usage":65536,"database":"aicountlyin_uuid_1502","users":["aicountlyin_uuid_1502_usr"]},{"users":["aicountlyin_uuid_1511_usr"],"database":"aicountlyin_uuid_1511","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1518","users":["aicountlyin_uuid_1518_usr"]},{"database":"aicountlyin_uuid_1519","disk_usage":65536,"users":["aicountlyin_uuid_1519_usr"]},{"database":"aicountlyin_uuid_1533","disk_usage":65536,"users":["aicountlyin_uuid_1533_usr"]},{"database":"aicountlyin_uuid_1534","disk_usage":65536,"users":["aicountlyin_uuid_1534_usr"]},{"users":["aicountlyin_uuid_1535_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1535"},{"database":"aicountlyin_uuid_1536","disk_usage":65536,"users":["aicountlyin_uuid_1536_usr"]},{"users":["aicountlyin_uuid_1537_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1537"},{"users":["aicountlyin_uuid_1538_usr"],"database":"aicountlyin_uuid_1538","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1539","users":["aicountlyin_uuid_1539_usr"]},{"database":"aicountlyin_uuid_1540","disk_usage":65536,"users":["aicountlyin_uuid_1540_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1541","users":["aicountlyin_uuid_1541_usr"]},{"database":"aicountlyin_uuid_1542","disk_usage":65536,"users":["aicountlyin_uuid_1542_usr"]},{"users":["aicountlyin_uuid_1543_usr"],"database":"aicountlyin_uuid_1543","disk_usage":65536},{"users":["aicountlyin_uuid_1546_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1546"},{"disk_usage":65536,"database":"aicountlyin_uuid_1548","users":["aicountlyin_uuid_1548_usr"]},{"users":["aicountlyin_uuid_1551_usr"],"database":"aicountlyin_uuid_1551","disk_usage":65536},{"users":["aicountlyin_uuid_1560_usr"],"database":"aicountlyin_uuid_1560","disk_usage":65536},{"database":"aicountlyin_uuid_1574","disk_usage":65536,"users":["aicountlyin_uuid_1574_usr"]},{"users":["aicountlyin_uuid_1575_usr"],"database":"aicountlyin_uuid_1575","disk_usage":65536},{"users":["aicountlyin_uuid_1576_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1576"},{"users":["aicountlyin_uuid_1577_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1577"},{"database":"aicountlyin_uuid_1578","disk_usage":65536,"users":["aicountlyin_uuid_1578_usr"]},{"database":"aicountlyin_uuid_1579","disk_usage":65536,"users":["aicountlyin_uuid_1579_usr"]},{"users":["aicountlyin_uuid_1580_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1580"},{"disk_usage":65536,"database":"aicountlyin_uuid_1581","users":["aicountlyin_uuid_1581_usr"]},{"users":["aicountlyin_uuid_1582_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1582"},{"users":["aicountlyin_uuid_1583_usr"],"database":"aicountlyin_uuid_1583","disk_usage":65536},{"database":"aicountlyin_uuid_1584","disk_usage":65536,"users":["aicountlyin_uuid_1584_usr"]},{"users":["aicountlyin_uuid_1585_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1585"},{"disk_usage":65536,"database":"aicountlyin_uuid_1586","users":["aicountlyin_uuid_1586_usr"]},{"users":["aicountlyin_uuid_1587_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1587"},{"users":["aicountlyin_uuid_1588_usr"],"database":"aicountlyin_uuid_1588","disk_usage":65536},{"users":["aicountlyin_uuid_1589_usr"],"database":"aicountlyin_uuid_1589","disk_usage":65536},{"users":["aicountlyin_uuid_1590_usr"],"database":"aicountlyin_uuid_1590","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1591","users":["aicountlyin_uuid_1591_usr"]},{"users":["aicountlyin_uuid_1593_usr"],"database":"aicountlyin_uuid_1593","disk_usage":65536},{"database":"aicountlyin_uuid_1643","disk_usage":65536,"users":["aicountlyin_uuid_1643_usr"]},{"database":"aicountlyin_uuid_1644","disk_usage":65536,"users":["aicountlyin_uuid_1644_usr"]},{"database":"aicountlyin_uuid_1645","disk_usage":65536,"users":["aicountlyin_uuid_1645_usr"]},{"users":["aicountlyin_uuid_1646_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1646"},{"database":"aicountlyin_uuid_1647","disk_usage":65536,"users":["aicountlyin_uuid_1647_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1648","users":["aicountlyin_uuid_1648_usr"]},{"users":["aicountlyin_uuid_1649_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1649"},{"users":["aicountlyin_uuid_1650_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1650"},{"database":"aicountlyin_uuid_1651","disk_usage":65536,"users":["aicountlyin_uuid_1651_usr"]},{"database":"aicountlyin_uuid_1652","disk_usage":65536,"users":["aicountlyin_uuid_1652_usr"]},{"users":["aicountlyin_uuid_1653_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1653"},{"users":["aicountlyin_uuid_1654_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1654"},{"database":"aicountlyin_uuid_1655","disk_usage":65536,"users":["aicountlyin_uuid_1655_usr"]},{"database":"aicountlyin_uuid_1656","disk_usage":65536,"users":["aicountlyin_uuid_1656_usr"]},{"database":"aicountlyin_uuid_1657","disk_usage":65536,"users":["aicountlyin_uuid_1657_usr"]},{"database":"aicountlyin_uuid_1658","disk_usage":65536,"users":["aicountlyin_uuid_1658_usr"]},{"users":["aicountlyin_uuid_1659_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1659"},{"users":["aicountlyin_uuid_1660_usr"],"database":"aicountlyin_uuid_1660","disk_usage":65536},{"database":"aicountlyin_uuid_17","disk_usage":131072,"users":["aicountlyin_uuid_17_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_170","users":["aicountlyin_uuid_170_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1703","users":["aicountlyin_uuid_1703_usr"]},{"database":"aicountlyin_uuid_171","disk_usage":65536,"users":["aicountlyin_uuid_171_usr"]},{"users":["aicountlyin_uuid_1710_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1710"},{"users":["aicountlyin_uuid_1711_usr"],"database":"aicountlyin_uuid_1711","disk_usage":65536},{"users":["aicountlyin_uuid_1712_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1712"},{"users":["aicountlyin_uuid_1713_usr"],"database":"aicountlyin_uuid_1713","disk_usage":65536},{"database":"aicountlyin_uuid_1714","disk_usage":65536,"users":["aicountlyin_uuid_1714_usr"]},{"users":["aicountlyin_uuid_1715_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1715"},{"users":["aicountlyin_uuid_1716_usr"],"database":"aicountlyin_uuid_1716","disk_usage":65536},{"database":"aicountlyin_uuid_1717","disk_usage":65536,"users":["aicountlyin_uuid_1717_usr"]},{"users":["aicountlyin_uuid_1718_usr"],"database":"aicountlyin_uuid_1718","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1719","users":["aicountlyin_uuid_1719_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1720","users":["aicountlyin_uuid_1720_usr"]},{"users":["aicountlyin_uuid_1721_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1721"},{"users":["aicountlyin_uuid_1722_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1722"},{"disk_usage":65536,"database":"aicountlyin_uuid_1723","users":["aicountlyin_uuid_1723_usr"]},{"users":["aicountlyin_uuid_1724_usr"],"database":"aicountlyin_uuid_1724","disk_usage":65536},{"database":"aicountlyin_uuid_1725","disk_usage":65536,"users":["aicountlyin_uuid_1725_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1726","users":["aicountlyin_uuid_1726_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1727","users":["aicountlyin_uuid_1727_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1728","users":["aicountlyin_uuid_1728_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1729","users":["aicountlyin_uuid_1729_usr"]},{"users":["aicountlyin_uuid_1730_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1730"},{"users":["aicountlyin_uuid_1731_usr"],"database":"aicountlyin_uuid_1731","disk_usage":65536},{"database":"aicountlyin_uuid_1732","disk_usage":65536,"users":["aicountlyin_uuid_1732_usr"]},{"users":["aicountlyin_uuid_1733_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1733"},{"users":["aicountlyin_uuid_1734_usr"],"database":"aicountlyin_uuid_1734","disk_usage":65536},{"database":"aicountlyin_uuid_1735","disk_usage":65536,"users":["aicountlyin_uuid_1735_usr"]},{"database":"aicountlyin_uuid_1736","disk_usage":65536,"users":["aicountlyin_uuid_1736_usr"]},{"users":["aicountlyin_uuid_1737_usr"],"database":"aicountlyin_uuid_1737","disk_usage":65536},{"users":["aicountlyin_uuid_1738_usr"],"database":"aicountlyin_uuid_1738","disk_usage":65536},{"database":"aicountlyin_uuid_1739","disk_usage":65536,"users":["aicountlyin_uuid_1739_usr"]},{"disk_usage":131072,"database":"aicountlyin_uuid_174","users":["aicountlyin_uuid_174_usr"]},{"users":["aicountlyin_uuid_1740_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1740"},{"users":["aicountlyin_uuid_1741_usr"],"database":"aicountlyin_uuid_1741","disk_usage":65536},{"database":"aicountlyin_uuid_1744","disk_usage":65536,"users":["aicountlyin_uuid_1744_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1745","users":["aicountlyin_uuid_1745_usr"]},{"users":["aicountlyin_uuid_176_usr"],"database":"aicountlyin_uuid_176","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1761","users":["aicountlyin_uuid_1761_usr"]},{"users":["aicountlyin_uuid_1762_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1762"},{"database":"aicountlyin_uuid_1763","disk_usage":65536,"users":["aicountlyin_uuid_1763_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1764","users":["aicountlyin_uuid_1764_usr"]},{"database":"aicountlyin_uuid_1765","disk_usage":65536,"users":["aicountlyin_uuid_1765_usr"]},{"users":["aicountlyin_uuid_1766_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1766"},{"disk_usage":65536,"database":"aicountlyin_uuid_1767","users":["aicountlyin_uuid_1767_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1768","users":["aicountlyin_uuid_1768_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1769","users":["aicountlyin_uuid_1769_usr"]},{"database":"aicountlyin_uuid_177","disk_usage":65536,"users":["aicountlyin_uuid_177_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1770","users":["aicountlyin_uuid_1770_usr"]},{"users":["aicountlyin_uuid_1771_usr"],"database":"aicountlyin_uuid_1771","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1772","users":["aicountlyin_uuid_1772_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1773","users":["aicountlyin_uuid_1773_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1774","users":["aicountlyin_uuid_1774_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1775","users":["aicountlyin_uuid_1775_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1776","users":["aicountlyin_uuid_1776_usr"]},{"users":["aicountlyin_uuid_1777_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1777"},{"disk_usage":65536,"database":"aicountlyin_uuid_1778","users":["aicountlyin_uuid_1778_usr"]},{"users":["aicountlyin_uuid_1781_usr"],"database":"aicountlyin_uuid_1781","disk_usage":65536},{"users":["aicountlyin_uuid_1782_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1782"},{"database":"aicountlyin_uuid_1783","disk_usage":65536,"users":["aicountlyin_uuid_1783_usr"]},{"database":"aicountlyin_uuid_1787","disk_usage":65536,"users":["aicountlyin_uuid_1787_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1788","users":["aicountlyin_uuid_1788_usr"]},{"users":["aicountlyin_uuid_1789_usr"],"database":"aicountlyin_uuid_1789","disk_usage":65536},{"database":"aicountlyin_uuid_1790","disk_usage":65536,"users":["aicountlyin_uuid_1790_usr"]},{"users":["aicountlyin_uuid_1791_usr"],"database":"aicountlyin_uuid_1791","disk_usage":65536},{"users":["aicountlyin_uuid_1792_usr"],"database":"aicountlyin_uuid_1792","disk_usage":65536},{"database":"aicountlyin_uuid_1793","disk_usage":65536,"users":["aicountlyin_uuid_1793_usr"]},{"users":["aicountlyin_uuid_1794_usr"],"database":"aicountlyin_uuid_1794","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1795","users":["aicountlyin_uuid_1795_usr"]},{"users":["aicountlyin_uuid_1796_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1796"},{"disk_usage":65536,"database":"aicountlyin_uuid_1797","users":["aicountlyin_uuid_1797_usr"]},{"database":"aicountlyin_uuid_1798","disk_usage":65536,"users":["aicountlyin_uuid_1798_usr"]},{"database":"aicountlyin_uuid_1799","disk_usage":65536,"users":["aicountlyin_uuid_1799_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_180","users":["aicountlyin_uuid_180_usr"]},{"users":["aicountlyin_uuid_1800_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1800"},{"database":"aicountlyin_uuid_1801","disk_usage":65536,"users":["aicountlyin_uuid_1801_usr"]},{"users":["aicountlyin_uuid_1802_usr"],"database":"aicountlyin_uuid_1802","disk_usage":65536},{"users":["aicountlyin_uuid_1803_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1803"},{"disk_usage":65536,"database":"aicountlyin_uuid_1804","users":["aicountlyin_uuid_1804_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1805","users":["aicountlyin_uuid_1805_usr"]},{"users":["aicountlyin_uuid_1806_usr"],"database":"aicountlyin_uuid_1806","disk_usage":65536},{"users":["aicountlyin_uuid_1807_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1807"},{"disk_usage":65536,"database":"aicountlyin_uuid_1808","users":["aicountlyin_uuid_1808_usr"]},{"users":["aicountlyin_uuid_1809_usr"],"database":"aicountlyin_uuid_1809","disk_usage":65536},{"users":["aicountlyin_uuid_1810_usr"],"database":"aicountlyin_uuid_1810","disk_usage":65536},{"users":["aicountlyin_uuid_1811_usr"],"database":"aicountlyin_uuid_1811","disk_usage":65536},{"users":["aicountlyin_uuid_1812_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1812"},{"database":"aicountlyin_uuid_1819","disk_usage":65536,"users":["aicountlyin_uuid_1819_usr"]},{"database":"aicountlyin_uuid_182","disk_usage":65536,"users":["aicountlyin_uuid_182_usr"]},{"users":["aicountlyin_uuid_1827_usr"],"database":"aicountlyin_uuid_1827","disk_usage":65536},{"database":"aicountlyin_uuid_1828","disk_usage":65536,"users":["aicountlyin_uuid_1828_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1829","users":["aicountlyin_uuid_1829_usr"]},{"users":["aicountlyin_uuid_183_usr"],"database":"aicountlyin_uuid_183","disk_usage":65536},{"users":["aicountlyin_uuid_1830_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1830"},{"users":["aicountlyin_uuid_1831_usr"],"database":"aicountlyin_uuid_1831","disk_usage":65536},{"users":["aicountlyin_uuid_1832_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1832"},{"database":"aicountlyin_uuid_184","disk_usage":65536,"users":["aicountlyin_uuid_184_usr"]},{"users":["aicountlyin_uuid_1845_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1845"},{"disk_usage":65536,"database":"aicountlyin_uuid_1847","users":["aicountlyin_uuid_1847_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1853","users":["aicountlyin_uuid_1853_usr"]},{"users":["aicountlyin_uuid_1857_usr"],"database":"aicountlyin_uuid_1857","disk_usage":65536},{"database":"aicountlyin_uuid_1860","disk_usage":65536,"users":["aicountlyin_uuid_1860_usr"]},{"users":["aicountlyin_uuid_1861_usr"],"database":"aicountlyin_uuid_1861","disk_usage":65536},{"users":["aicountlyin_uuid_1862_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1862"},{"database":"aicountlyin_uuid_1863","disk_usage":65536,"users":["aicountlyin_uuid_1863_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1864","users":["aicountlyin_uuid_1864_usr"]},{"users":["aicountlyin_uuid_1865_usr"],"database":"aicountlyin_uuid_1865","disk_usage":65536},{"database":"aicountlyin_uuid_1866","disk_usage":65536,"users":["aicountlyin_uuid_1866_usr"]},{"users":["aicountlyin_uuid_1867_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1867"},{"users":["aicountlyin_uuid_1868_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1868"},{"database":"aicountlyin_uuid_1882","disk_usage":65536,"users":["aicountlyin_uuid_1882_usr"]},{"users":["aicountlyin_uuid_189_usr"],"database":"aicountlyin_uuid_189","disk_usage":65536},{"database":"aicountlyin_uuid_1890","disk_usage":65536,"users":["aicountlyin_uuid_1890_usr"]},{"users":["aicountlyin_uuid_1924_usr"],"database":"aicountlyin_uuid_1924","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1925","users":["aicountlyin_uuid_1925_usr"]},{"users":["aicountlyin_uuid_1926_usr"],"database":"aicountlyin_uuid_1926","disk_usage":65536},{"database":"aicountlyin_uuid_1927","disk_usage":65536,"users":["aicountlyin_uuid_1927_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1928","users":["aicountlyin_uuid_1928_usr"]},{"users":["aicountlyin_uuid_1929_usr"],"database":"aicountlyin_uuid_1929","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1930","users":["aicountlyin_uuid_1930_usr"]},{"users":["aicountlyin_uuid_1931_usr"],"database":"aicountlyin_uuid_1931","disk_usage":65536},{"database":"aicountlyin_uuid_1932","disk_usage":65536,"users":["aicountlyin_uuid_1932_usr"]},{"users":["aicountlyin_uuid_1933_usr"],"database":"aicountlyin_uuid_1933","disk_usage":65536},{"users":["aicountlyin_uuid_1935_usr"],"database":"aicountlyin_uuid_1935","disk_usage":65536},{"users":["aicountlyin_uuid_1936_usr"],"database":"aicountlyin_uuid_1936","disk_usage":65536},{"database":"aicountlyin_uuid_1937","disk_usage":65536,"users":["aicountlyin_uuid_1937_usr"]},{"users":["aicountlyin_uuid_1938_usr"],"database":"aicountlyin_uuid_1938","disk_usage":65536},{"users":["aicountlyin_uuid_1939_usr"],"database":"aicountlyin_uuid_1939","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1940","users":["aicountlyin_uuid_1940_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1941","users":["aicountlyin_uuid_1941_usr"]},{"users":["aicountlyin_uuid_1942_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1942"},{"users":["aicountlyin_uuid_1943_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1943"},{"users":["aicountlyin_uuid_1944_usr"],"database":"aicountlyin_uuid_1944","disk_usage":65536},{"users":["aicountlyin_uuid_1945_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1945"},{"users":["aicountlyin_uuid_1946_usr"],"database":"aicountlyin_uuid_1946","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_1947","users":["aicountlyin_uuid_1947_usr"]},{"users":["aicountlyin_uuid_1948_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1948"},{"disk_usage":65536,"database":"aicountlyin_uuid_1949","users":["aicountlyin_uuid_1949_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1950","users":["aicountlyin_uuid_1950_usr"]},{"database":"aicountlyin_uuid_1951","disk_usage":65536,"users":["aicountlyin_uuid_1951_usr"]},{"users":["aicountlyin_uuid_1952_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1952"},{"users":["aicountlyin_uuid_1953_usr"],"database":"aicountlyin_uuid_1953","disk_usage":65536},{"users":["aicountlyin_uuid_1954_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1954"},{"users":["aicountlyin_uuid_1955_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1955"},{"users":["aicountlyin_uuid_1956_usr"],"database":"aicountlyin_uuid_1956","disk_usage":65536},{"users":["aicountlyin_uuid_1957_usr"],"database":"aicountlyin_uuid_1957","disk_usage":65536},{"users":["aicountlyin_uuid_1984_usr"],"database":"aicountlyin_uuid_1984","disk_usage":65536},{"users":["aicountlyin_uuid_1985_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1985"},{"database":"aicountlyin_uuid_1986","disk_usage":65536,"users":["aicountlyin_uuid_1986_usr"]},{"database":"aicountlyin_uuid_1987","disk_usage":65536,"users":["aicountlyin_uuid_1987_usr"]},{"users":["aicountlyin_uuid_1988_usr"],"database":"aicountlyin_uuid_1988","disk_usage":65536},{"users":["aicountlyin_uuid_1989_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1989"},{"disk_usage":65536,"database":"aicountlyin_uuid_1990","users":["aicountlyin_uuid_1990_usr"]},{"users":["aicountlyin_uuid_1991_usr"],"database":"aicountlyin_uuid_1991","disk_usage":65536},{"database":"aicountlyin_uuid_1992","disk_usage":65536,"users":["aicountlyin_uuid_1992_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1993","users":["aicountlyin_uuid_1993_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_1994","users":["aicountlyin_uuid_1994_usr"]},{"users":["aicountlyin_uuid_1995_usr"],"database":"aicountlyin_uuid_1995","disk_usage":65536},{"users":["aicountlyin_uuid_1996_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1996"},{"database":"aicountlyin_uuid_1997","disk_usage":65536,"users":["aicountlyin_uuid_1997_usr"]},{"users":["aicountlyin_uuid_1998_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1998"},{"users":["aicountlyin_uuid_1999_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_1999"},{"users":["aicountlyin_uuid_2_usr"],"database":"aicountlyin_uuid_2","disk_usage":131072},{"users":["aicountlyin_uuid_2000_usr"],"database":"aicountlyin_uuid_2000","disk_usage":65536},{"database":"aicountlyin_uuid_2001","disk_usage":65536,"users":["aicountlyin_uuid_2001_usr"]},{"database":"aicountlyin_uuid_2002","disk_usage":65536,"users":["aicountlyin_uuid_2002_usr"]},{"users":["aicountlyin_uuid_2003_usr"],"database":"aicountlyin_uuid_2003","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2004","users":["aicountlyin_uuid_2004_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2005","users":["aicountlyin_uuid_2005_usr"]},{"database":"aicountlyin_uuid_2006","disk_usage":65536,"users":["aicountlyin_uuid_2006_usr"]},{"users":["aicountlyin_uuid_2007_usr"],"database":"aicountlyin_uuid_2007","disk_usage":65536},{"users":["aicountlyin_uuid_2008_usr"],"database":"aicountlyin_uuid_2008","disk_usage":65536},{"users":["aicountlyin_uuid_2009_usr"],"database":"aicountlyin_uuid_2009","disk_usage":65536},{"users":["aicountlyin_uuid_2010_usr"],"database":"aicountlyin_uuid_2010","disk_usage":65536},{"users":["aicountlyin_uuid_2013_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2013"},{"disk_usage":65536,"database":"aicountlyin_uuid_2014","users":["aicountlyin_uuid_2014_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2032","users":["aicountlyin_uuid_2032_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2038","users":["aicountlyin_uuid_2038_usr"]},{"users":["aicountlyin_uuid_2043_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2043"},{"database":"aicountlyin_uuid_2044","disk_usage":65536,"users":["aicountlyin_uuid_2044_usr"]},{"users":["aicountlyin_uuid_2045_usr"],"database":"aicountlyin_uuid_2045","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2046","users":["aicountlyin_uuid_2046_usr"]},{"database":"aicountlyin_uuid_2047","disk_usage":65536,"users":["aicountlyin_uuid_2047_usr"]},{"users":["aicountlyin_uuid_2048_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2048"},{"disk_usage":65536,"database":"aicountlyin_uuid_2049","users":["aicountlyin_uuid_2049_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2050","users":["aicountlyin_uuid_2050_usr"]},{"users":["aicountlyin_uuid_2051_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2051"},{"database":"aicountlyin_uuid_2052","disk_usage":65536,"users":["aicountlyin_uuid_2052_usr"]},{"users":["aicountlyin_uuid_2053_usr"],"database":"aicountlyin_uuid_2053","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2054","users":["aicountlyin_uuid_2054_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2055","users":["aicountlyin_uuid_2055_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2056","users":["aicountlyin_uuid_2056_usr"]},{"database":"aicountlyin_uuid_2057","disk_usage":65536,"users":["aicountlyin_uuid_2057_usr"]},{"users":["aicountlyin_uuid_2058_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2058"},{"users":["aicountlyin_uuid_2059_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2059"},{"disk_usage":65536,"database":"aicountlyin_uuid_2060","users":["aicountlyin_uuid_2060_usr"]},{"database":"aicountlyin_uuid_2061","disk_usage":65536,"users":["aicountlyin_uuid_2061_usr"]},{"users":["aicountlyin_uuid_2062_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2062"},{"database":"aicountlyin_uuid_2063","disk_usage":65536,"users":["aicountlyin_uuid_2063_usr"]},{"users":["aicountlyin_uuid_2064_usr"],"database":"aicountlyin_uuid_2064","disk_usage":65536},{"users":["aicountlyin_uuid_2065_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2065"},{"users":["aicountlyin_uuid_2067_usr"],"database":"aicountlyin_uuid_2067","disk_usage":65536},{"database":"aicountlyin_uuid_2068","disk_usage":65536,"users":["aicountlyin_uuid_2068_usr"]},{"users":["aicountlyin_uuid_2069_usr"],"database":"aicountlyin_uuid_2069","disk_usage":65536},{"users":["aicountlyin_uuid_2078_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2078"},{"users":["aicountlyin_uuid_2088_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2088"},{"users":["aicountlyin_uuid_2089_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2089"},{"users":["aicountlyin_uuid_2090_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2090"},{"users":["aicountlyin_uuid_2091_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2091"},{"users":["aicountlyin_uuid_2092_usr"],"database":"aicountlyin_uuid_2092","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2093","users":["aicountlyin_uuid_2093_usr"]},{"users":["aicountlyin_uuid_2094_usr"],"database":"aicountlyin_uuid_2094","disk_usage":65536},{"users":["aicountlyin_uuid_2095_usr"],"database":"aicountlyin_uuid_2095","disk_usage":65536},{"database":"aicountlyin_uuid_2096","disk_usage":65536,"users":["aicountlyin_uuid_2096_usr"]},{"users":["aicountlyin_uuid_2097_usr"],"database":"aicountlyin_uuid_2097","disk_usage":65536},{"users":["aicountlyin_uuid_2098_usr"],"database":"aicountlyin_uuid_2098","disk_usage":65536},{"users":["aicountlyin_uuid_2099_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2099"},{"disk_usage":131072,"database":"aicountlyin_uuid_21","users":["aicountlyin_uuid_21_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2102","users":["aicountlyin_uuid_2102_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2106","users":["aicountlyin_uuid_2106_usr"]},{"users":["aicountlyin_uuid_2108_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2108"},{"users":["aicountlyin_uuid_2110_usr"],"database":"aicountlyin_uuid_2110","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2120","users":["aicountlyin_uuid_2120_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2123","users":["aicountlyin_uuid_2123_usr"]},{"database":"aicountlyin_uuid_2131","disk_usage":65536,"users":["aicountlyin_uuid_2131_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2132","users":["aicountlyin_uuid_2132_usr"]},{"users":["aicountlyin_uuid_2133_usr"],"database":"aicountlyin_uuid_2133","disk_usage":65536},{"users":["aicountlyin_uuid_2134_usr"],"database":"aicountlyin_uuid_2134","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2135","users":["aicountlyin_uuid_2135_usr"]},{"database":"aicountlyin_uuid_2136","disk_usage":65536,"users":["aicountlyin_uuid_2136_usr"]},{"database":"aicountlyin_uuid_2137","disk_usage":65536,"users":["aicountlyin_uuid_2137_usr"]},{"users":["aicountlyin_uuid_2138_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2138"},{"users":["aicountlyin_uuid_2139_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2139"},{"users":["aicountlyin_uuid_2140_usr"],"database":"aicountlyin_uuid_2140","disk_usage":65536},{"users":["aicountlyin_uuid_2141_usr"],"database":"aicountlyin_uuid_2141","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2142","users":["aicountlyin_uuid_2142_usr"]},{"database":"aicountlyin_uuid_2143","disk_usage":65536,"users":["aicountlyin_uuid_2143_usr"]},{"database":"aicountlyin_uuid_2144","disk_usage":65536,"users":["aicountlyin_uuid_2144_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2145","users":["aicountlyin_uuid_2145_usr"]},{"users":["aicountlyin_uuid_2146_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2146"},{"users":["aicountlyin_uuid_2150_usr"],"database":"aicountlyin_uuid_2150","disk_usage":65536},{"database":"aicountlyin_uuid_2152","disk_usage":65536,"users":["aicountlyin_uuid_2152_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2160","users":["aicountlyin_uuid_2160_usr"]},{"database":"aicountlyin_uuid_2164","disk_usage":65536,"users":["aicountlyin_uuid_2164_usr"]},{"database":"aicountlyin_uuid_2165","disk_usage":65536,"users":["aicountlyin_uuid_2165_usr"]},{"users":["aicountlyin_uuid_2167_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2167"},{"users":["aicountlyin_uuid_2168_usr"],"database":"aicountlyin_uuid_2168","disk_usage":65536},{"users":["aicountlyin_uuid_2169_usr"],"database":"aicountlyin_uuid_2169","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2170","users":["aicountlyin_uuid_2170_usr"]},{"users":["aicountlyin_uuid_2171_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2171"},{"users":["aicountlyin_uuid_2172_usr"],"database":"aicountlyin_uuid_2172","disk_usage":65536},{"database":"aicountlyin_uuid_2173","disk_usage":65536,"users":["aicountlyin_uuid_2173_usr"]},{"users":["aicountlyin_uuid_2176_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2176"},{"database":"aicountlyin_uuid_2177","disk_usage":65536,"users":["aicountlyin_uuid_2177_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2200","users":["aicountlyin_uuid_2200_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2201","users":["aicountlyin_uuid_2201_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2202","users":["aicountlyin_uuid_2202_usr"]},{"users":["aicountlyin_uuid_2205_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2205"},{"disk_usage":65536,"database":"aicountlyin_uuid_2206","users":["aicountlyin_uuid_2206_usr"]},{"users":["aicountlyin_uuid_2207_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2207"},{"users":["aicountlyin_uuid_2208_usr"],"database":"aicountlyin_uuid_2208","disk_usage":65536},{"database":"aicountlyin_uuid_2209","disk_usage":65536,"users":["aicountlyin_uuid_2209_usr"]},{"database":"aicountlyin_uuid_2210","disk_usage":65536,"users":["aicountlyin_uuid_2210_usr"]},{"database":"aicountlyin_uuid_2211","disk_usage":65536,"users":["aicountlyin_uuid_2211_usr"]},{"database":"aicountlyin_uuid_2212","disk_usage":65536,"users":["aicountlyin_uuid_2212_usr"]},{"users":["aicountlyin_uuid_2213_usr"],"database":"aicountlyin_uuid_2213","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2214","users":["aicountlyin_uuid_2214_usr"]},{"database":"aicountlyin_uuid_2215","disk_usage":65536,"users":["aicountlyin_uuid_2215_usr"]},{"users":["aicountlyin_uuid_2216_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2216"},{"users":["aicountlyin_uuid_2217_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2217"},{"users":["aicountlyin_uuid_2218_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2218"},{"users":["aicountlyin_uuid_2219_usr"],"database":"aicountlyin_uuid_2219","disk_usage":65536},{"database":"aicountlyin_uuid_2220","disk_usage":65536,"users":["aicountlyin_uuid_2220_usr"]},{"database":"aicountlyin_uuid_2221","disk_usage":65536,"users":["aicountlyin_uuid_2221_usr"]},{"users":["aicountlyin_uuid_2222_usr"],"database":"aicountlyin_uuid_2222","disk_usage":65536},{"users":["aicountlyin_uuid_2223_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2223"},{"database":"aicountlyin_uuid_2224","disk_usage":65536,"users":["aicountlyin_uuid_2224_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2225","users":["aicountlyin_uuid_2225_usr"]},{"users":["aicountlyin_uuid_2226_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2226"},{"database":"aicountlyin_uuid_2227","disk_usage":65536,"users":["aicountlyin_uuid_2227_usr"]},{"database":"aicountlyin_uuid_2228","disk_usage":65536,"users":["aicountlyin_uuid_2228_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2232","users":["aicountlyin_uuid_2232_usr"]},{"database":"aicountlyin_uuid_2234","disk_usage":65536,"users":["aicountlyin_uuid_2234_usr"]},{"users":["aicountlyin_uuid_2235_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2235"},{"users":["aicountlyin_uuid_2236_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2236"},{"database":"aicountlyin_uuid_2237","disk_usage":65536,"users":["aicountlyin_uuid_2237_usr"]},{"users":["aicountlyin_uuid_2238_usr"],"database":"aicountlyin_uuid_2238","disk_usage":65536},{"database":"aicountlyin_uuid_2239","disk_usage":65536,"users":["aicountlyin_uuid_2239_usr"]},{"users":["aicountlyin_uuid_2240_usr"],"database":"aicountlyin_uuid_2240","disk_usage":65536},{"database":"aicountlyin_uuid_2241","disk_usage":65536,"users":["aicountlyin_uuid_2241_usr"]},{"users":["aicountlyin_uuid_2242_usr"],"database":"aicountlyin_uuid_2242","disk_usage":65536},{"users":["aicountlyin_uuid_2244_usr"],"database":"aicountlyin_uuid_2244","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2248","users":["aicountlyin_uuid_2248_usr"]},{"users":["aicountlyin_uuid_2249_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2249"},{"database":"aicountlyin_uuid_2250","disk_usage":65536,"users":["aicountlyin_uuid_2250_usr"]},{"users":["aicountlyin_uuid_2251_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2251"},{"database":"aicountlyin_uuid_2252","disk_usage":65536,"users":["aicountlyin_uuid_2252_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2253","users":["aicountlyin_uuid_2253_usr"]},{"database":"aicountlyin_uuid_2254","disk_usage":65536,"users":["aicountlyin_uuid_2254_usr"]},{"database":"aicountlyin_uuid_2255","disk_usage":65536,"users":["aicountlyin_uuid_2255_usr"]},{"users":["aicountlyin_uuid_2256_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2256"},{"disk_usage":65536,"database":"aicountlyin_uuid_2257","users":["aicountlyin_uuid_2257_usr"]},{"users":["aicountlyin_uuid_2258_usr"],"database":"aicountlyin_uuid_2258","disk_usage":65536},{"users":["aicountlyin_uuid_2259_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2259"},{"users":["aicountlyin_uuid_2260_usr"],"database":"aicountlyin_uuid_2260","disk_usage":65536},{"users":["aicountlyin_uuid_2261_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2261"},{"users":["aicountlyin_uuid_2262_usr"],"database":"aicountlyin_uuid_2262","disk_usage":65536},{"users":["aicountlyin_uuid_2263_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2263"},{"users":["aicountlyin_uuid_2264_usr"],"database":"aicountlyin_uuid_2264","disk_usage":65536},{"users":["aicountlyin_uuid_2265_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2265"},{"users":["aicountlyin_uuid_2266_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2266"},{"users":["aicountlyin_uuid_2267_usr"],"database":"aicountlyin_uuid_2267","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2268","users":["aicountlyin_uuid_2268_usr"]},{"users":["aicountlyin_uuid_2269_usr"],"database":"aicountlyin_uuid_2269","disk_usage":65536},{"users":["aicountlyin_uuid_2270_usr"],"database":"aicountlyin_uuid_2270","disk_usage":65536},{"database":"aicountlyin_uuid_2271","disk_usage":65536,"users":["aicountlyin_uuid_2271_usr"]},{"users":["aicountlyin_uuid_2272_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2272"},{"users":["aicountlyin_uuid_2273_usr"],"database":"aicountlyin_uuid_2273","disk_usage":65536},{"database":"aicountlyin_uuid_2274","disk_usage":65536,"users":["aicountlyin_uuid_2274_usr"]},{"database":"aicountlyin_uuid_2275","disk_usage":65536,"users":["aicountlyin_uuid_2275_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2276","users":["aicountlyin_uuid_2276_usr"]},{"database":"aicountlyin_uuid_2277","disk_usage":65536,"users":["aicountlyin_uuid_2277_usr"]},{"users":["aicountlyin_uuid_2278_usr"],"database":"aicountlyin_uuid_2278","disk_usage":65536},{"users":["aicountlyin_uuid_2279_usr"],"database":"aicountlyin_uuid_2279","disk_usage":65536},{"users":["aicountlyin_uuid_2280_usr"],"database":"aicountlyin_uuid_2280","disk_usage":65536},{"database":"aicountlyin_uuid_2281","disk_usage":65536,"users":["aicountlyin_uuid_2281_usr"]},{"users":["aicountlyin_uuid_2282_usr"],"database":"aicountlyin_uuid_2282","disk_usage":65536},{"users":["aicountlyin_uuid_2283_usr"],"database":"aicountlyin_uuid_2283","disk_usage":65536},{"database":"aicountlyin_uuid_2284","disk_usage":65536,"users":["aicountlyin_uuid_2284_usr"]},{"users":["aicountlyin_uuid_2285_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2285"},{"users":["aicountlyin_uuid_2286_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2286"},{"disk_usage":65536,"database":"aicountlyin_uuid_2287","users":["aicountlyin_uuid_2287_usr"]},{"database":"aicountlyin_uuid_2288","disk_usage":65536,"users":["aicountlyin_uuid_2288_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2289","users":["aicountlyin_uuid_2289_usr"]},{"users":["aicountlyin_uuid_2290_usr"],"database":"aicountlyin_uuid_2290","disk_usage":65536},{"users":["aicountlyin_uuid_2291_usr"],"database":"aicountlyin_uuid_2291","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2292","users":["aicountlyin_uuid_2292_usr"]},{"users":["aicountlyin_uuid_2293_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2293"},{"disk_usage":65536,"database":"aicountlyin_uuid_2294","users":["aicountlyin_uuid_2294_usr"]},{"users":["aicountlyin_uuid_2295_usr"],"database":"aicountlyin_uuid_2295","disk_usage":65536},{"users":["aicountlyin_uuid_2296_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2296"},{"database":"aicountlyin_uuid_2297","disk_usage":65536,"users":["aicountlyin_uuid_2297_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2298","users":["aicountlyin_uuid_2298_usr"]},{"database":"aicountlyin_uuid_2299","disk_usage":65536,"users":["aicountlyin_uuid_2299_usr"]},{"users":["aicountlyin_uuid_23_usr"],"database":"aicountlyin_uuid_23","disk_usage":131072},{"database":"aicountlyin_uuid_2300","disk_usage":65536,"users":["aicountlyin_uuid_2300_usr"]},{"users":["aicountlyin_uuid_2301_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2301"},{"users":["aicountlyin_uuid_2302_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2302"},{"users":["aicountlyin_uuid_2303_usr"],"database":"aicountlyin_uuid_2303","disk_usage":65536},{"database":"aicountlyin_uuid_2304","disk_usage":65536,"users":["aicountlyin_uuid_2304_usr"]},{"users":["aicountlyin_uuid_2305_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2305"},{"disk_usage":65536,"database":"aicountlyin_uuid_2306","users":["aicountlyin_uuid_2306_usr"]},{"users":["aicountlyin_uuid_2307_usr"],"database":"aicountlyin_uuid_2307","disk_usage":65536},{"users":["aicountlyin_uuid_2308_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2308"},{"users":["aicountlyin_uuid_2309_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2309"},{"users":["aicountlyin_uuid_2310_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2310"},{"users":["aicountlyin_uuid_2311_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2311"},{"database":"aicountlyin_uuid_2312","disk_usage":65536,"users":["aicountlyin_uuid_2312_usr"]},{"database":"aicountlyin_uuid_2313","disk_usage":65536,"users":["aicountlyin_uuid_2313_usr"]},{"users":["aicountlyin_uuid_2314_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2314"},{"users":["aicountlyin_uuid_2315_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2315"},{"disk_usage":65536,"database":"aicountlyin_uuid_2316","users":["aicountlyin_uuid_2316_usr"]},{"database":"aicountlyin_uuid_2317","disk_usage":65536,"users":["aicountlyin_uuid_2317_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2318","users":["aicountlyin_uuid_2318_usr"]},{"users":["aicountlyin_uuid_2319_usr"],"database":"aicountlyin_uuid_2319","disk_usage":65536},{"database":"aicountlyin_uuid_2320","disk_usage":65536,"users":["aicountlyin_uuid_2320_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2321","users":["aicountlyin_uuid_2321_usr"]},{"users":["aicountlyin_uuid_2322_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2322"},{"users":["aicountlyin_uuid_2323_usr"],"database":"aicountlyin_uuid_2323","disk_usage":65536},{"database":"aicountlyin_uuid_2324","disk_usage":65536,"users":["aicountlyin_uuid_2324_usr"]},{"users":["aicountlyin_uuid_2325_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2325"},{"users":["aicountlyin_uuid_2326_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2326"},{"disk_usage":65536,"database":"aicountlyin_uuid_2327","users":["aicountlyin_uuid_2327_usr"]},{"database":"aicountlyin_uuid_2328","disk_usage":65536,"users":["aicountlyin_uuid_2328_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2329","users":["aicountlyin_uuid_2329_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2330","users":["aicountlyin_uuid_2330_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2331","users":["aicountlyin_uuid_2331_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2332","users":["aicountlyin_uuid_2332_usr"]},{"database":"aicountlyin_uuid_2333","disk_usage":65536,"users":["aicountlyin_uuid_2333_usr"]},{"database":"aicountlyin_uuid_2334","disk_usage":65536,"users":["aicountlyin_uuid_2334_usr"]},{"users":["aicountlyin_uuid_2335_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2335"},{"database":"aicountlyin_uuid_2336","disk_usage":65536,"users":["aicountlyin_uuid_2336_usr"]},{"database":"aicountlyin_uuid_2337","disk_usage":65536,"users":["aicountlyin_uuid_2337_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2341","users":["aicountlyin_uuid_2341_usr"]},{"users":["aicountlyin_uuid_2342_usr"],"database":"aicountlyin_uuid_2342","disk_usage":65536},{"database":"aicountlyin_uuid_2343","disk_usage":65536,"users":["aicountlyin_uuid_2343_usr"]},{"users":["aicountlyin_uuid_2345_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2345"},{"database":"aicountlyin_uuid_2346","disk_usage":65536,"users":["aicountlyin_uuid_2346_usr"]},{"users":["aicountlyin_uuid_2347_usr"],"database":"aicountlyin_uuid_2347","disk_usage":65536},{"users":["aicountlyin_uuid_2348_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2348"},{"users":["aicountlyin_uuid_2349_usr"],"database":"aicountlyin_uuid_2349","disk_usage":65536},{"users":["aicountlyin_uuid_2350_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2350"},{"disk_usage":65536,"database":"aicountlyin_uuid_2351","users":["aicountlyin_uuid_2351_usr"]},{"users":["aicountlyin_uuid_2352_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2352"},{"disk_usage":65536,"database":"aicountlyin_uuid_2353","users":["aicountlyin_uuid_2353_usr"]},{"database":"aicountlyin_uuid_2354","disk_usage":65536,"users":["aicountlyin_uuid_2354_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2355","users":["aicountlyin_uuid_2355_usr"]},{"users":["aicountlyin_uuid_2356_usr"],"database":"aicountlyin_uuid_2356","disk_usage":65536},{"users":["aicountlyin_uuid_2357_usr"],"database":"aicountlyin_uuid_2357","disk_usage":65536},{"users":["aicountlyin_uuid_2358_usr"],"database":"aicountlyin_uuid_2358","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2359","users":["aicountlyin_uuid_2359_usr"]},{"users":["aicountlyin_uuid_2360_usr"],"database":"aicountlyin_uuid_2360","disk_usage":65536},{"users":["aicountlyin_uuid_2361_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2361"},{"users":["aicountlyin_uuid_2362_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2362"},{"users":["aicountlyin_uuid_2363_usr"],"database":"aicountlyin_uuid_2363","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2364","users":["aicountlyin_uuid_2364_usr"]},{"database":"aicountlyin_uuid_2365","disk_usage":65536,"users":["aicountlyin_uuid_2365_usr"]},{"database":"aicountlyin_uuid_2366","disk_usage":65536,"users":["aicountlyin_uuid_2366_usr"]},{"users":["aicountlyin_uuid_2367_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2367"},{"disk_usage":65536,"database":"aicountlyin_uuid_2368","users":["aicountlyin_uuid_2368_usr"]},{"users":["aicountlyin_uuid_2369_usr"],"database":"aicountlyin_uuid_2369","disk_usage":65536},{"users":["aicountlyin_uuid_2370_usr"],"database":"aicountlyin_uuid_2370","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2371","users":["aicountlyin_uuid_2371_usr"]},{"users":["aicountlyin_uuid_2372_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2372"},{"database":"aicountlyin_uuid_2373","disk_usage":65536,"users":["aicountlyin_uuid_2373_usr"]},{"users":["aicountlyin_uuid_2374_usr"],"database":"aicountlyin_uuid_2374","disk_usage":65536},{"users":["aicountlyin_uuid_2375_usr"],"database":"aicountlyin_uuid_2375","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2376","users":["aicountlyin_uuid_2376_usr"]},{"users":["aicountlyin_uuid_2377_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2377"},{"disk_usage":65536,"database":"aicountlyin_uuid_2379","users":["aicountlyin_uuid_2379_usr"]},{"users":["aicountlyin_uuid_2381_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2381"},{"database":"aicountlyin_uuid_2382","disk_usage":65536,"users":["aicountlyin_uuid_2382_usr"]},{"database":"aicountlyin_uuid_2383","disk_usage":65536,"users":["aicountlyin_uuid_2383_usr"]},{"users":["aicountlyin_uuid_2384_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2384"},{"database":"aicountlyin_uuid_2385","disk_usage":65536,"users":["aicountlyin_uuid_2385_usr"]},{"users":["aicountlyin_uuid_2386_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2386"},{"disk_usage":65536,"database":"aicountlyin_uuid_2387","users":["aicountlyin_uuid_2387_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2388","users":["aicountlyin_uuid_2388_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2389","users":["aicountlyin_uuid_2389_usr"]},{"users":["aicountlyin_uuid_2392_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2392"},{"users":["aicountlyin_uuid_2393_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2393"},{"users":["aicountlyin_uuid_2394_usr"],"database":"aicountlyin_uuid_2394","disk_usage":65536},{"users":["aicountlyin_uuid_2395_usr"],"database":"aicountlyin_uuid_2395","disk_usage":65536},{"users":["aicountlyin_uuid_2396_usr"],"database":"aicountlyin_uuid_2396","disk_usage":65536},{"database":"aicountlyin_uuid_2397","disk_usage":65536,"users":["aicountlyin_uuid_2397_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2398","users":["aicountlyin_uuid_2398_usr"]},{"users":["aicountlyin_uuid_2399_usr"],"database":"aicountlyin_uuid_2399","disk_usage":65536},{"database":"aicountlyin_uuid_24","disk_usage":131072,"users":["aicountlyin_uuid_24_usr"]},{"users":["aicountlyin_uuid_2400_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2400"},{"users":["aicountlyin_uuid_2401_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2401"},{"users":["aicountlyin_uuid_2402_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2402"},{"users":["aicountlyin_uuid_2403_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2403"},{"disk_usage":65536,"database":"aicountlyin_uuid_2404","users":["aicountlyin_uuid_2404_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2405","users":["aicountlyin_uuid_2405_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2406","users":["aicountlyin_uuid_2406_usr"]},{"database":"aicountlyin_uuid_2407","disk_usage":65536,"users":["aicountlyin_uuid_2407_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2408","users":["aicountlyin_uuid_2408_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2409","users":["aicountlyin_uuid_2409_usr"]},{"database":"aicountlyin_uuid_2410","disk_usage":65536,"users":["aicountlyin_uuid_2410_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2411","users":["aicountlyin_uuid_2411_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2412","users":["aicountlyin_uuid_2412_usr"]},{"users":["aicountlyin_uuid_2413_usr"],"database":"aicountlyin_uuid_2413","disk_usage":65536},{"users":["aicountlyin_uuid_2414_usr"],"database":"aicountlyin_uuid_2414","disk_usage":65536},{"users":["aicountlyin_uuid_2415_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2415"},{"users":["aicountlyin_uuid_2416_usr"],"database":"aicountlyin_uuid_2416","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2417","users":["aicountlyin_uuid_2417_usr"]},{"users":["aicountlyin_uuid_2418_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2418"},{"users":["aicountlyin_uuid_2419_usr"],"database":"aicountlyin_uuid_2419","disk_usage":65536},{"users":["aicountlyin_uuid_2420_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2420"},{"disk_usage":65536,"database":"aicountlyin_uuid_2421","users":["aicountlyin_uuid_2421_usr"]},{"users":["aicountlyin_uuid_2422_usr"],"database":"aicountlyin_uuid_2422","disk_usage":65536},{"users":["aicountlyin_uuid_2423_usr"],"database":"aicountlyin_uuid_2423","disk_usage":65536},{"database":"aicountlyin_uuid_2424","disk_usage":65536,"users":["aicountlyin_uuid_2424_usr"]},{"users":["aicountlyin_uuid_2425_usr"],"database":"aicountlyin_uuid_2425","disk_usage":65536},{"database":"aicountlyin_uuid_2426","disk_usage":65536,"users":["aicountlyin_uuid_2426_usr"]},{"database":"aicountlyin_uuid_2427","disk_usage":65536,"users":["aicountlyin_uuid_2427_usr"]},{"users":["aicountlyin_uuid_2428_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2428"},{"users":["aicountlyin_uuid_2429_usr"],"database":"aicountlyin_uuid_2429","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2430","users":["aicountlyin_uuid_2430_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2431","users":["aicountlyin_uuid_2431_usr"]},{"users":["aicountlyin_uuid_2432_usr"],"database":"aicountlyin_uuid_2432","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2433","users":["aicountlyin_uuid_2433_usr"]},{"users":["aicountlyin_uuid_2434_usr"],"database":"aicountlyin_uuid_2434","disk_usage":65536},{"users":["aicountlyin_uuid_2435_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2435"},{"database":"aicountlyin_uuid_2436","disk_usage":65536,"users":["aicountlyin_uuid_2436_usr"]},{"database":"aicountlyin_uuid_2437","disk_usage":65536,"users":["aicountlyin_uuid_2437_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2438","users":["aicountlyin_uuid_2438_usr"]},{"users":["aicountlyin_uuid_2439_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2439"},{"disk_usage":65536,"database":"aicountlyin_uuid_2440","users":["aicountlyin_uuid_2440_usr"]},{"users":["aicountlyin_uuid_2441_usr"],"database":"aicountlyin_uuid_2441","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2442","users":["aicountlyin_uuid_2442_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2443","users":["aicountlyin_uuid_2443_usr"]},{"database":"aicountlyin_uuid_2444","disk_usage":65536,"users":["aicountlyin_uuid_2444_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2445","users":["aicountlyin_uuid_2445_usr"]},{"users":["aicountlyin_uuid_2446_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2446"},{"users":["aicountlyin_uuid_2447_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2447"},{"users":["aicountlyin_uuid_2448_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2448"},{"disk_usage":65536,"database":"aicountlyin_uuid_2449","users":["aicountlyin_uuid_2449_usr"]},{"users":["aicountlyin_uuid_2450_usr"],"database":"aicountlyin_uuid_2450","disk_usage":65536},{"database":"aicountlyin_uuid_2451","disk_usage":98304,"users":["aicountlyin_uuid_2451_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2452","users":["aicountlyin_uuid_2452_usr"]},{"users":["aicountlyin_uuid_2453_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2453"},{"users":["aicountlyin_uuid_2454_usr"],"database":"aicountlyin_uuid_2454","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2455","users":["aicountlyin_uuid_2455_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2456","users":["aicountlyin_uuid_2456_usr"]},{"users":["aicountlyin_uuid_2457_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2457"},{"database":"aicountlyin_uuid_2458","disk_usage":65536,"users":["aicountlyin_uuid_2458_usr"]},{"database":"aicountlyin_uuid_2459","disk_usage":65536,"users":["aicountlyin_uuid_2459_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2460","users":["aicountlyin_uuid_2460_usr"]},{"users":["aicountlyin_uuid_2461_usr"],"database":"aicountlyin_uuid_2461","disk_usage":65536},{"database":"aicountlyin_uuid_2462","disk_usage":65536,"users":["aicountlyin_uuid_2462_usr"]},{"users":["aicountlyin_uuid_2463_usr"],"database":"aicountlyin_uuid_2463","disk_usage":65536},{"users":["aicountlyin_uuid_2464_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2464"},{"users":["aicountlyin_uuid_2465_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2465"},{"users":["aicountlyin_uuid_2466_usr"],"database":"aicountlyin_uuid_2466","disk_usage":65536},{"users":["aicountlyin_uuid_2467_usr"],"database":"aicountlyin_uuid_2467","disk_usage":65536},{"users":["aicountlyin_uuid_2468_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2468"},{"users":["aicountlyin_uuid_2469_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2469"},{"disk_usage":65536,"database":"aicountlyin_uuid_2470","users":["aicountlyin_uuid_2470_usr"]},{"users":["aicountlyin_uuid_2471_usr"],"database":"aicountlyin_uuid_2471","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2472","users":["aicountlyin_uuid_2472_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2473","users":["aicountlyin_uuid_2473_usr"]},{"database":"aicountlyin_uuid_2474","disk_usage":65536,"users":["aicountlyin_uuid_2474_usr"]},{"users":["aicountlyin_uuid_2475_usr"],"database":"aicountlyin_uuid_2475","disk_usage":65536},{"database":"aicountlyin_uuid_2476","disk_usage":65536,"users":["aicountlyin_uuid_2476_usr"]},{"users":["aicountlyin_uuid_2477_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2477"},{"users":["aicountlyin_uuid_2478_usr"],"database":"aicountlyin_uuid_2478","disk_usage":65536},{"users":["aicountlyin_uuid_2479_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2479"},{"database":"aicountlyin_uuid_2480","disk_usage":65536,"users":["aicountlyin_uuid_2480_usr"]},{"users":["aicountlyin_uuid_2481_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2481"},{"users":["aicountlyin_uuid_2482_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2482"},{"users":["aicountlyin_uuid_2483_usr"],"database":"aicountlyin_uuid_2483","disk_usage":65536},{"users":["aicountlyin_uuid_2484_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2484"},{"database":"aicountlyin_uuid_2485","disk_usage":65536,"users":["aicountlyin_uuid_2485_usr"]},{"database":"aicountlyin_uuid_2486","disk_usage":65536,"users":["aicountlyin_uuid_2486_usr"]},{"database":"aicountlyin_uuid_2487","disk_usage":65536,"users":["aicountlyin_uuid_2487_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2489","users":["aicountlyin_uuid_2489_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2490","users":["aicountlyin_uuid_2490_usr"]},{"database":"aicountlyin_uuid_2491","disk_usage":65536,"users":["aicountlyin_uuid_2491_usr"]},{"database":"aicountlyin_uuid_2492","disk_usage":65536,"users":["aicountlyin_uuid_2492_usr"]},{"users":["aicountlyin_uuid_2493_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2493"},{"users":["aicountlyin_uuid_2494_usr"],"database":"aicountlyin_uuid_2494","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2495","users":["aicountlyin_uuid_2495_usr"]},{"database":"aicountlyin_uuid_2496","disk_usage":65536,"users":["aicountlyin_uuid_2496_usr"]},{"users":["aicountlyin_uuid_2497_usr"],"database":"aicountlyin_uuid_2497","disk_usage":65536},{"users":["aicountlyin_uuid_2498_usr"],"database":"aicountlyin_uuid_2498","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2499","users":["aicountlyin_uuid_2499_usr"]},{"users":["aicountlyin_uuid_25_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_25"},{"users":["aicountlyin_uuid_2500_usr"],"database":"aicountlyin_uuid_2500","disk_usage":65536},{"users":["aicountlyin_uuid_2501_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2501"},{"users":["aicountlyin_uuid_2502_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2502"},{"disk_usage":65536,"database":"aicountlyin_uuid_2503","users":["aicountlyin_uuid_2503_usr"]},{"users":["aicountlyin_uuid_2504_usr"],"database":"aicountlyin_uuid_2504","disk_usage":65536},{"users":["aicountlyin_uuid_2505_usr"],"database":"aicountlyin_uuid_2505","disk_usage":65536},{"users":["aicountlyin_uuid_2506_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2506"},{"database":"aicountlyin_uuid_2507","disk_usage":65536,"users":["aicountlyin_uuid_2507_usr"]},{"users":["aicountlyin_uuid_2508_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2508"},{"users":["aicountlyin_uuid_2509_usr"],"database":"aicountlyin_uuid_2509","disk_usage":65536},{"users":["aicountlyin_uuid_2510_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2510"},{"users":["aicountlyin_uuid_2511_usr"],"database":"aicountlyin_uuid_2511","disk_usage":65536},{"users":["aicountlyin_uuid_2512_usr"],"database":"aicountlyin_uuid_2512","disk_usage":65536},{"database":"aicountlyin_uuid_2513","disk_usage":65536,"users":["aicountlyin_uuid_2513_usr"]},{"users":["aicountlyin_uuid_2514_usr"],"database":"aicountlyin_uuid_2514","disk_usage":65536},{"database":"aicountlyin_uuid_2515","disk_usage":65536,"users":["aicountlyin_uuid_2515_usr"]},{"users":["aicountlyin_uuid_2516_usr"],"database":"aicountlyin_uuid_2516","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2517","users":["aicountlyin_uuid_2517_usr"]},{"database":"aicountlyin_uuid_2518","disk_usage":65536,"users":["aicountlyin_uuid_2518_usr"]},{"users":["aicountlyin_uuid_2519_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2519"},{"users":["aicountlyin_uuid_2520_usr"],"database":"aicountlyin_uuid_2520","disk_usage":65536},{"users":["aicountlyin_uuid_2521_usr"],"database":"aicountlyin_uuid_2521","disk_usage":65536},{"database":"aicountlyin_uuid_2522","disk_usage":65536,"users":["aicountlyin_uuid_2522_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2523","users":["aicountlyin_uuid_2523_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2524","users":["aicountlyin_uuid_2524_usr"]},{"database":"aicountlyin_uuid_2525","disk_usage":65536,"users":["aicountlyin_uuid_2525_usr"]},{"database":"aicountlyin_uuid_2526","disk_usage":65536,"users":["aicountlyin_uuid_2526_usr"]},{"users":["aicountlyin_uuid_2527_usr"],"database":"aicountlyin_uuid_2527","disk_usage":65536},{"users":["aicountlyin_uuid_2528_usr"],"database":"aicountlyin_uuid_2528","disk_usage":65536},{"users":["aicountlyin_uuid_2529_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2529"},{"users":["aicountlyin_uuid_2530_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2530"},{"database":"aicountlyin_uuid_2531","disk_usage":65536,"users":["aicountlyin_uuid_2531_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2532","users":["aicountlyin_uuid_2532_usr"]},{"users":["aicountlyin_uuid_2533_usr"],"database":"aicountlyin_uuid_2533","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2534","users":["aicountlyin_uuid_2534_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2535","users":["aicountlyin_uuid_2535_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2536","users":["aicountlyin_uuid_2536_usr"]},{"users":["aicountlyin_uuid_2537_usr"],"database":"aicountlyin_uuid_2537","disk_usage":65536},{"users":["aicountlyin_uuid_2538_usr"],"database":"aicountlyin_uuid_2538","disk_usage":65536},{"users":["aicountlyin_uuid_2539_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2539"},{"users":["aicountlyin_uuid_2540_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2540"},{"disk_usage":65536,"database":"aicountlyin_uuid_2541","users":["aicountlyin_uuid_2541_usr"]},{"users":["aicountlyin_uuid_2542_usr"],"database":"aicountlyin_uuid_2542","disk_usage":65536},{"users":["aicountlyin_uuid_2543_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2543"},{"users":["aicountlyin_uuid_2544_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2544"},{"database":"aicountlyin_uuid_2545","disk_usage":65536,"users":["aicountlyin_uuid_2545_usr"]},{"users":["aicountlyin_uuid_2546_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2546"},{"disk_usage":65536,"database":"aicountlyin_uuid_2547","users":["aicountlyin_uuid_2547_usr"]},{"users":["aicountlyin_uuid_2548_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2548"},{"users":["aicountlyin_uuid_2549_usr"],"database":"aicountlyin_uuid_2549","disk_usage":65536},{"users":["aicountlyin_uuid_2550_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2550"},{"database":"aicountlyin_uuid_2551","disk_usage":65536,"users":["aicountlyin_uuid_2551_usr"]},{"users":["aicountlyin_uuid_2552_usr"],"database":"aicountlyin_uuid_2552","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2553","users":["aicountlyin_uuid_2553_usr"]},{"users":["aicountlyin_uuid_2555_usr"],"database":"aicountlyin_uuid_2555","disk_usage":65536},{"users":["aicountlyin_uuid_2556_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2556"},{"users":["aicountlyin_uuid_2557_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2557"},{"disk_usage":65536,"database":"aicountlyin_uuid_2558","users":["aicountlyin_uuid_2558_usr"]},{"users":["aicountlyin_uuid_2559_usr"],"database":"aicountlyin_uuid_2559","disk_usage":65536},{"users":["aicountlyin_uuid_2560_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2560"},{"database":"aicountlyin_uuid_2561","disk_usage":65536,"users":["aicountlyin_uuid_2561_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2562","users":["aicountlyin_uuid_2562_usr"]},{"users":["aicountlyin_uuid_2563_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2563"},{"users":["aicountlyin_uuid_2564_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2564"},{"disk_usage":65536,"database":"aicountlyin_uuid_2565","users":["aicountlyin_uuid_2565_usr"]},{"database":"aicountlyin_uuid_2566","disk_usage":65536,"users":["aicountlyin_uuid_2566_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2567","users":["aicountlyin_uuid_2567_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2568","users":["aicountlyin_uuid_2568_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2569","users":["aicountlyin_uuid_2569_usr"]},{"database":"aicountlyin_uuid_2570","disk_usage":65536,"users":["aicountlyin_uuid_2570_usr"]},{"users":["aicountlyin_uuid_2571_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2571"},{"users":["aicountlyin_uuid_2572_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2572"},{"disk_usage":65536,"database":"aicountlyin_uuid_2573","users":["aicountlyin_uuid_2573_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2574","users":["aicountlyin_uuid_2574_usr"]},{"database":"aicountlyin_uuid_2575","disk_usage":65536,"users":["aicountlyin_uuid_2575_usr"]},{"database":"aicountlyin_uuid_2576","disk_usage":65536,"users":["aicountlyin_uuid_2576_usr"]},{"database":"aicountlyin_uuid_2577","disk_usage":65536,"users":["aicountlyin_uuid_2577_usr"]},{"users":["aicountlyin_uuid_2578_usr"],"database":"aicountlyin_uuid_2578","disk_usage":65536},{"users":["aicountlyin_uuid_2579_usr"],"database":"aicountlyin_uuid_2579","disk_usage":65536},{"users":["aicountlyin_uuid_2580_usr"],"database":"aicountlyin_uuid_2580","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2581","users":["aicountlyin_uuid_2581_usr"]},{"users":["aicountlyin_uuid_2583_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2583"},{"database":"aicountlyin_uuid_2584","disk_usage":65536,"users":["aicountlyin_uuid_2584_usr"]},{"users":["aicountlyin_uuid_2585_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2585"},{"users":["aicountlyin_uuid_2586_usr"],"database":"aicountlyin_uuid_2586","disk_usage":65536},{"users":["aicountlyin_uuid_2587_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2587"},{"users":["aicountlyin_uuid_2588_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2588"},{"users":["aicountlyin_uuid_2589_usr"],"database":"aicountlyin_uuid_2589","disk_usage":65536},{"users":["aicountlyin_uuid_2590_usr"],"database":"aicountlyin_uuid_2590","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2591","users":["aicountlyin_uuid_2591_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2592","users":["aicountlyin_uuid_2592_usr"]},{"users":["aicountlyin_uuid_2593_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2593"},{"users":["aicountlyin_uuid_2594_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2594"},{"disk_usage":65536,"database":"aicountlyin_uuid_2595","users":["aicountlyin_uuid_2595_usr"]},{"users":["aicountlyin_uuid_2596_usr"],"database":"aicountlyin_uuid_2596","disk_usage":65536},{"database":"aicountlyin_uuid_2597","disk_usage":65536,"users":["aicountlyin_uuid_2597_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2598","users":["aicountlyin_uuid_2598_usr"]},{"database":"aicountlyin_uuid_2599","disk_usage":65536,"users":["aicountlyin_uuid_2599_usr"]},{"users":["aicountlyin_uuid_26_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_26"},{"database":"aicountlyin_uuid_2600","disk_usage":65536,"users":["aicountlyin_uuid_2600_usr"]},{"users":["aicountlyin_uuid_2601_usr"],"database":"aicountlyin_uuid_2601","disk_usage":65536},{"users":["aicountlyin_uuid_2602_usr"],"database":"aicountlyin_uuid_2602","disk_usage":65536},{"users":["aicountlyin_uuid_2603_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2603"},{"disk_usage":65536,"database":"aicountlyin_uuid_2604","users":["aicountlyin_uuid_2604_usr"]},{"users":["aicountlyin_uuid_2605_usr"],"database":"aicountlyin_uuid_2605","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2606","users":["aicountlyin_uuid_2606_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2607","users":["aicountlyin_uuid_2607_usr"]},{"users":["aicountlyin_uuid_2608_usr"],"database":"aicountlyin_uuid_2608","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2609","users":["aicountlyin_uuid_2609_usr"]},{"users":["aicountlyin_uuid_2610_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2610"},{"database":"aicountlyin_uuid_2611","disk_usage":65536,"users":["aicountlyin_uuid_2611_usr"]},{"users":["aicountlyin_uuid_2612_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2612"},{"disk_usage":65536,"database":"aicountlyin_uuid_2613","users":["aicountlyin_uuid_2613_usr"]},{"users":["aicountlyin_uuid_2614_usr"],"database":"aicountlyin_uuid_2614","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2615","users":["aicountlyin_uuid_2615_usr"]},{"database":"aicountlyin_uuid_2616","disk_usage":65536,"users":["aicountlyin_uuid_2616_usr"]},{"users":["aicountlyin_uuid_2617_usr"],"database":"aicountlyin_uuid_2617","disk_usage":65536},{"users":["aicountlyin_uuid_2618_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2618"},{"database":"aicountlyin_uuid_2619","disk_usage":65536,"users":["aicountlyin_uuid_2619_usr"]},{"users":["aicountlyin_uuid_2620_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2620"},{"database":"aicountlyin_uuid_2621","disk_usage":65536,"users":["aicountlyin_uuid_2621_usr"]},{"users":["aicountlyin_uuid_2622_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2622"},{"users":["aicountlyin_uuid_2623_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2623"},{"database":"aicountlyin_uuid_2624","disk_usage":65536,"users":["aicountlyin_uuid_2624_usr"]},{"users":["aicountlyin_uuid_2625_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2625"},{"users":["aicountlyin_uuid_2626_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2626"},{"users":["aicountlyin_uuid_2627_usr"],"database":"aicountlyin_uuid_2627","disk_usage":65536},{"database":"aicountlyin_uuid_2628","disk_usage":65536,"users":["aicountlyin_uuid_2628_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2629","users":["aicountlyin_uuid_2629_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2630","users":["aicountlyin_uuid_2630_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2631","users":["aicountlyin_uuid_2631_usr"]},{"database":"aicountlyin_uuid_2632","disk_usage":65536,"users":["aicountlyin_uuid_2632_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2633","users":["aicountlyin_uuid_2633_usr"]},{"users":["aicountlyin_uuid_2634_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2634"},{"database":"aicountlyin_uuid_2635","disk_usage":65536,"users":["aicountlyin_uuid_2635_usr"]},{"users":["aicountlyin_uuid_2636_usr"],"database":"aicountlyin_uuid_2636","disk_usage":65536},{"database":"aicountlyin_uuid_2637","disk_usage":65536,"users":["aicountlyin_uuid_2637_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2638","users":["aicountlyin_uuid_2638_usr"]},{"users":["aicountlyin_uuid_2639_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2639"},{"users":["aicountlyin_uuid_2640_usr"],"database":"aicountlyin_uuid_2640","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2641","users":["aicountlyin_uuid_2641_usr"]},{"users":["aicountlyin_uuid_2642_usr"],"database":"aicountlyin_uuid_2642","disk_usage":65536},{"users":["aicountlyin_uuid_2643_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2643"},{"disk_usage":65536,"database":"aicountlyin_uuid_2644","users":["aicountlyin_uuid_2644_usr"]},{"users":["aicountlyin_uuid_2645_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2645"},{"disk_usage":65536,"database":"aicountlyin_uuid_2646","users":["aicountlyin_uuid_2646_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2647","users":["aicountlyin_uuid_2647_usr"]},{"database":"aicountlyin_uuid_2648","disk_usage":65536,"users":["aicountlyin_uuid_2648_usr"]},{"database":"aicountlyin_uuid_2649","disk_usage":65536,"users":["aicountlyin_uuid_2649_usr"]},{"users":["aicountlyin_uuid_2650_usr"],"database":"aicountlyin_uuid_2650","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2651","users":["aicountlyin_uuid_2651_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2652","users":["aicountlyin_uuid_2652_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2653","users":["aicountlyin_uuid_2653_usr"]},{"users":["aicountlyin_uuid_2654_usr"],"database":"aicountlyin_uuid_2654","disk_usage":65536},{"users":["aicountlyin_uuid_2655_usr"],"database":"aicountlyin_uuid_2655","disk_usage":65536},{"users":["aicountlyin_uuid_2656_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2656"},{"disk_usage":65536,"database":"aicountlyin_uuid_2657","users":["aicountlyin_uuid_2657_usr"]},{"database":"aicountlyin_uuid_2658","disk_usage":65536,"users":["aicountlyin_uuid_2658_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2659","users":["aicountlyin_uuid_2659_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2660","users":["aicountlyin_uuid_2660_usr"]},{"users":["aicountlyin_uuid_2661_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2661"},{"disk_usage":65536,"database":"aicountlyin_uuid_2662","users":["aicountlyin_uuid_2662_usr"]},{"users":["aicountlyin_uuid_2663_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2663"},{"users":["aicountlyin_uuid_2664_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2664"},{"users":["aicountlyin_uuid_2665_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2665"},{"users":["aicountlyin_uuid_2666_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2666"},{"database":"aicountlyin_uuid_2667","disk_usage":65536,"users":["aicountlyin_uuid_2667_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2668","users":["aicountlyin_uuid_2668_usr"]},{"users":["aicountlyin_uuid_2669_usr"],"database":"aicountlyin_uuid_2669","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2670","users":["aicountlyin_uuid_2670_usr"]},{"users":["aicountlyin_uuid_2671_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2671"},{"users":["aicountlyin_uuid_2672_usr"],"database":"aicountlyin_uuid_2672","disk_usage":65536},{"users":["aicountlyin_uuid_2673_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2673"},{"database":"aicountlyin_uuid_2674","disk_usage":65536,"users":["aicountlyin_uuid_2674_usr"]},{"users":["aicountlyin_uuid_2675_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2675"},{"database":"aicountlyin_uuid_2676","disk_usage":65536,"users":["aicountlyin_uuid_2676_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2677","users":["aicountlyin_uuid_2677_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2678","users":["aicountlyin_uuid_2678_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2679","users":["aicountlyin_uuid_2679_usr"]},{"database":"aicountlyin_uuid_2680","disk_usage":65536,"users":["aicountlyin_uuid_2680_usr"]},{"database":"aicountlyin_uuid_2681","disk_usage":65536,"users":["aicountlyin_uuid_2681_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2682","users":["aicountlyin_uuid_2682_usr"]},{"database":"aicountlyin_uuid_2683","disk_usage":65536,"users":["aicountlyin_uuid_2683_usr"]},{"users":["aicountlyin_uuid_2684_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2684"},{"database":"aicountlyin_uuid_2685","disk_usage":65536,"users":["aicountlyin_uuid_2685_usr"]},{"database":"aicountlyin_uuid_2686","disk_usage":65536,"users":["aicountlyin_uuid_2686_usr"]},{"database":"aicountlyin_uuid_2687","disk_usage":65536,"users":["aicountlyin_uuid_2687_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2688","users":["aicountlyin_uuid_2688_usr"]},{"users":["aicountlyin_uuid_2689_usr"],"database":"aicountlyin_uuid_2689","disk_usage":65536},{"database":"aicountlyin_uuid_2690","disk_usage":65536,"users":["aicountlyin_uuid_2690_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2691","users":["aicountlyin_uuid_2691_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2692","users":["aicountlyin_uuid_2692_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2693","users":["aicountlyin_uuid_2693_usr"]},{"database":"aicountlyin_uuid_2694","disk_usage":65536,"users":["aicountlyin_uuid_2694_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2695","users":["aicountlyin_uuid_2695_usr"]},{"users":["aicountlyin_uuid_2696_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2696"},{"database":"aicountlyin_uuid_2697","disk_usage":65536,"users":["aicountlyin_uuid_2697_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2698","users":["aicountlyin_uuid_2698_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2699","users":["aicountlyin_uuid_2699_usr"]},{"database":"aicountlyin_uuid_27","disk_usage":131072,"users":["aicountlyin_uuid_27_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2700","users":["aicountlyin_uuid_2700_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2701","users":["aicountlyin_uuid_2701_usr"]},{"users":["aicountlyin_uuid_2702_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2702"},{"users":["aicountlyin_uuid_2703_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2703"},{"users":["aicountlyin_uuid_2704_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2704"},{"database":"aicountlyin_uuid_2705","disk_usage":65536,"users":["aicountlyin_uuid_2705_usr"]},{"users":["aicountlyin_uuid_2706_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2706"},{"disk_usage":65536,"database":"aicountlyin_uuid_2707","users":["aicountlyin_uuid_2707_usr"]},{"database":"aicountlyin_uuid_2708","disk_usage":65536,"users":["aicountlyin_uuid_2708_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2709","users":["aicountlyin_uuid_2709_usr"]},{"users":["aicountlyin_uuid_2710_usr"],"database":"aicountlyin_uuid_2710","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2711","users":["aicountlyin_uuid_2711_usr"]},{"database":"aicountlyin_uuid_2712","disk_usage":65536,"users":["aicountlyin_uuid_2712_usr"]},{"database":"aicountlyin_uuid_2713","disk_usage":65536,"users":["aicountlyin_uuid_2713_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2714","users":["aicountlyin_uuid_2714_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2715","users":["aicountlyin_uuid_2715_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2716","users":["aicountlyin_uuid_2716_usr"]},{"database":"aicountlyin_uuid_2717","disk_usage":65536,"users":["aicountlyin_uuid_2717_usr"]},{"database":"aicountlyin_uuid_2718","disk_usage":65536,"users":["aicountlyin_uuid_2718_usr"]},{"users":["aicountlyin_uuid_2719_usr"],"database":"aicountlyin_uuid_2719","disk_usage":65536},{"users":["aicountlyin_uuid_2720_usr"],"database":"aicountlyin_uuid_2720","disk_usage":65536},{"database":"aicountlyin_uuid_2721","disk_usage":65536,"users":["aicountlyin_uuid_2721_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2722","users":["aicountlyin_uuid_2722_usr"]},{"database":"aicountlyin_uuid_2723","disk_usage":65536,"users":["aicountlyin_uuid_2723_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2724","users":["aicountlyin_uuid_2724_usr"]},{"users":["aicountlyin_uuid_2725_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2725"},{"disk_usage":65536,"database":"aicountlyin_uuid_2726","users":["aicountlyin_uuid_2726_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2727","users":["aicountlyin_uuid_2727_usr"]},{"users":["aicountlyin_uuid_2728_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2728"},{"users":["aicountlyin_uuid_2729_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2729"},{"database":"aicountlyin_uuid_2730","disk_usage":65536,"users":["aicountlyin_uuid_2730_usr"]},{"users":["aicountlyin_uuid_2731_usr"],"database":"aicountlyin_uuid_2731","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2732","users":["aicountlyin_uuid_2732_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2733","users":["aicountlyin_uuid_2733_usr"]},{"users":["aicountlyin_uuid_2734_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2734"},{"users":["aicountlyin_uuid_2735_usr"],"database":"aicountlyin_uuid_2735","disk_usage":65536},{"database":"aicountlyin_uuid_2736","disk_usage":65536,"users":["aicountlyin_uuid_2736_usr"]},{"users":["aicountlyin_uuid_2737_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2737"},{"users":["aicountlyin_uuid_2738_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2738"},{"users":["aicountlyin_uuid_2739_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2739"},{"users":["aicountlyin_uuid_2740_usr"],"database":"aicountlyin_uuid_2740","disk_usage":65536},{"database":"aicountlyin_uuid_2741","disk_usage":65536,"users":["aicountlyin_uuid_2741_usr"]},{"users":["aicountlyin_uuid_2742_usr"],"database":"aicountlyin_uuid_2742","disk_usage":65536},{"users":["aicountlyin_uuid_2743_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2743"},{"users":["aicountlyin_uuid_2744_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2744"},{"database":"aicountlyin_uuid_2745","disk_usage":65536,"users":["aicountlyin_uuid_2745_usr"]},{"users":["aicountlyin_uuid_2746_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2746"},{"users":["aicountlyin_uuid_2747_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2747"},{"disk_usage":65536,"database":"aicountlyin_uuid_2748","users":["aicountlyin_uuid_2748_usr"]},{"database":"aicountlyin_uuid_2749","disk_usage":65536,"users":["aicountlyin_uuid_2749_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2750","users":["aicountlyin_uuid_2750_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2751","users":["aicountlyin_uuid_2751_usr"]},{"database":"aicountlyin_uuid_2752","disk_usage":65536,"users":["aicountlyin_uuid_2752_usr"]},{"users":["aicountlyin_uuid_2753_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2753"},{"users":["aicountlyin_uuid_2754_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2754"},{"users":["aicountlyin_uuid_2755_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2755"},{"database":"aicountlyin_uuid_2756","disk_usage":65536,"users":["aicountlyin_uuid_2756_usr"]},{"users":["aicountlyin_uuid_2757_usr"],"database":"aicountlyin_uuid_2757","disk_usage":65536},{"database":"aicountlyin_uuid_2758","disk_usage":65536,"users":["aicountlyin_uuid_2758_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2759","users":["aicountlyin_uuid_2759_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2760","users":["aicountlyin_uuid_2760_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2761","users":["aicountlyin_uuid_2761_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2762","users":["aicountlyin_uuid_2762_usr"]},{"users":["aicountlyin_uuid_2763_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2763"},{"users":["aicountlyin_uuid_2764_usr"],"database":"aicountlyin_uuid_2764","disk_usage":65536},{"database":"aicountlyin_uuid_2765","disk_usage":65536,"users":["aicountlyin_uuid_2765_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2766","users":["aicountlyin_uuid_2766_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2767","users":["aicountlyin_uuid_2767_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2768","users":["aicountlyin_uuid_2768_usr"]},{"users":["aicountlyin_uuid_2769_usr"],"database":"aicountlyin_uuid_2769","disk_usage":65536},{"database":"aicountlyin_uuid_2770","disk_usage":65536,"users":["aicountlyin_uuid_2770_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2771","users":["aicountlyin_uuid_2771_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2772","users":["aicountlyin_uuid_2772_usr"]},{"database":"aicountlyin_uuid_2773","disk_usage":65536,"users":["aicountlyin_uuid_2773_usr"]},{"database":"aicountlyin_uuid_2774","disk_usage":65536,"users":["aicountlyin_uuid_2774_usr"]},{"users":["aicountlyin_uuid_2775_usr"],"database":"aicountlyin_uuid_2775","disk_usage":65536},{"users":["aicountlyin_uuid_2776_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2776"},{"users":["aicountlyin_uuid_2777_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2777"},{"disk_usage":65536,"database":"aicountlyin_uuid_2778","users":["aicountlyin_uuid_2778_usr"]},{"users":["aicountlyin_uuid_2779_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2779"},{"users":["aicountlyin_uuid_2780_usr"],"database":"aicountlyin_uuid_2780","disk_usage":65536},{"database":"aicountlyin_uuid_2781","disk_usage":65536,"users":["aicountlyin_uuid_2781_usr"]},{"database":"aicountlyin_uuid_2782","disk_usage":65536,"users":["aicountlyin_uuid_2782_usr"]},{"database":"aicountlyin_uuid_2783","disk_usage":65536,"users":["aicountlyin_uuid_2783_usr"]},{"users":["aicountlyin_uuid_2784_usr"],"database":"aicountlyin_uuid_2784","disk_usage":65536},{"users":["aicountlyin_uuid_2785_usr"],"database":"aicountlyin_uuid_2785","disk_usage":65536},{"users":["aicountlyin_uuid_2786_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2786"},{"database":"aicountlyin_uuid_2787","disk_usage":65536,"users":["aicountlyin_uuid_2787_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2788","users":["aicountlyin_uuid_2788_usr"]},{"database":"aicountlyin_uuid_2789","disk_usage":65536,"users":["aicountlyin_uuid_2789_usr"]},{"users":["aicountlyin_uuid_2790_usr"],"database":"aicountlyin_uuid_2790","disk_usage":65536},{"database":"aicountlyin_uuid_2791","disk_usage":65536,"users":["aicountlyin_uuid_2791_usr"]},{"database":"aicountlyin_uuid_2792","disk_usage":65536,"users":["aicountlyin_uuid_2792_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2793","users":["aicountlyin_uuid_2793_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2794","users":["aicountlyin_uuid_2794_usr"]},{"database":"aicountlyin_uuid_2795","disk_usage":65536,"users":["aicountlyin_uuid_2795_usr"]},{"users":["aicountlyin_uuid_2796_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2796"},{"users":["aicountlyin_uuid_2797_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2797"},{"database":"aicountlyin_uuid_2798","disk_usage":65536,"users":["aicountlyin_uuid_2798_usr"]},{"database":"aicountlyin_uuid_2799","disk_usage":65536,"users":["aicountlyin_uuid_2799_usr"]},{"users":["aicountlyin_uuid_28_usr"],"database":"aicountlyin_uuid_28","disk_usage":131072},{"disk_usage":65536,"database":"aicountlyin_uuid_2800","users":["aicountlyin_uuid_2800_usr"]},{"users":["aicountlyin_uuid_2801_usr"],"database":"aicountlyin_uuid_2801","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2802","users":["aicountlyin_uuid_2802_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2803","users":["aicountlyin_uuid_2803_usr"]},{"users":["aicountlyin_uuid_2804_usr"],"database":"aicountlyin_uuid_2804","disk_usage":65536},{"database":"aicountlyin_uuid_2805","disk_usage":65536,"users":["aicountlyin_uuid_2805_usr"]},{"users":["aicountlyin_uuid_2806_usr"],"database":"aicountlyin_uuid_2806","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2807","users":["aicountlyin_uuid_2807_usr"]},{"users":["aicountlyin_uuid_2808_usr"],"database":"aicountlyin_uuid_2808","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2809","users":["aicountlyin_uuid_2809_usr"]},{"users":["aicountlyin_uuid_2810_usr"],"database":"aicountlyin_uuid_2810","disk_usage":65536},{"users":["aicountlyin_uuid_2811_usr"],"database":"aicountlyin_uuid_2811","disk_usage":65536},{"users":["aicountlyin_uuid_2812_usr"],"database":"aicountlyin_uuid_2812","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2813","users":["aicountlyin_uuid_2813_usr"]},{"database":"aicountlyin_uuid_2814","disk_usage":65536,"users":["aicountlyin_uuid_2814_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2815","users":["aicountlyin_uuid_2815_usr"]},{"users":["aicountlyin_uuid_2816_usr"],"database":"aicountlyin_uuid_2816","disk_usage":65536},{"users":["aicountlyin_uuid_2817_usr"],"database":"aicountlyin_uuid_2817","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2818","users":["aicountlyin_uuid_2818_usr"]},{"database":"aicountlyin_uuid_2819","disk_usage":65536,"users":["aicountlyin_uuid_2819_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2820","users":["aicountlyin_uuid_2820_usr"]},{"database":"aicountlyin_uuid_2821","disk_usage":65536,"users":["aicountlyin_uuid_2821_usr"]},{"database":"aicountlyin_uuid_2822","disk_usage":65536,"users":["aicountlyin_uuid_2822_usr"]},{"users":["aicountlyin_uuid_2823_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2823"},{"users":["aicountlyin_uuid_2824_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2824"},{"disk_usage":65536,"database":"aicountlyin_uuid_2825","users":["aicountlyin_uuid_2825_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2826","users":["aicountlyin_uuid_2826_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2827","users":["aicountlyin_uuid_2827_usr"]},{"users":["aicountlyin_uuid_2828_usr"],"database":"aicountlyin_uuid_2828","disk_usage":65536},{"users":["aicountlyin_uuid_2829_usr"],"database":"aicountlyin_uuid_2829","disk_usage":65536},{"users":["aicountlyin_uuid_2830_usr"],"database":"aicountlyin_uuid_2830","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2831","users":["aicountlyin_uuid_2831_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2832","users":["aicountlyin_uuid_2832_usr"]},{"users":["aicountlyin_uuid_2833_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2833"},{"database":"aicountlyin_uuid_2834","disk_usage":65536,"users":["aicountlyin_uuid_2834_usr"]},{"users":["aicountlyin_uuid_2835_usr"],"database":"aicountlyin_uuid_2835","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2836","users":["aicountlyin_uuid_2836_usr"]},{"users":["aicountlyin_uuid_2837_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2837"},{"users":["aicountlyin_uuid_2838_usr"],"database":"aicountlyin_uuid_2838","disk_usage":65536},{"database":"aicountlyin_uuid_2839","disk_usage":65536,"users":["aicountlyin_uuid_2839_usr"]},{"users":["aicountlyin_uuid_284_usr"],"database":"aicountlyin_uuid_284","disk_usage":65536},{"users":["aicountlyin_uuid_2840_usr"],"database":"aicountlyin_uuid_2840","disk_usage":65536},{"database":"aicountlyin_uuid_2841","disk_usage":65536,"users":["aicountlyin_uuid_2841_usr"]},{"users":["aicountlyin_uuid_2842_usr"],"database":"aicountlyin_uuid_2842","disk_usage":65536},{"database":"aicountlyin_uuid_2843","disk_usage":65536,"users":["aicountlyin_uuid_2843_usr"]},{"database":"aicountlyin_uuid_2844","disk_usage":65536,"users":["aicountlyin_uuid_2844_usr"]},{"database":"aicountlyin_uuid_2845","disk_usage":65536,"users":["aicountlyin_uuid_2845_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2846","users":["aicountlyin_uuid_2846_usr"]},{"users":["aicountlyin_uuid_2847_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2847"},{"disk_usage":65536,"database":"aicountlyin_uuid_2848","users":["aicountlyin_uuid_2848_usr"]},{"users":["aicountlyin_uuid_2849_usr"],"database":"aicountlyin_uuid_2849","disk_usage":65536},{"database":"aicountlyin_uuid_2850","disk_usage":65536,"users":["aicountlyin_uuid_2850_usr"]},{"users":["aicountlyin_uuid_2851_usr"],"database":"aicountlyin_uuid_2851","disk_usage":65536},{"users":["aicountlyin_uuid_2852_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2852"},{"database":"aicountlyin_uuid_2853","disk_usage":65536,"users":["aicountlyin_uuid_2853_usr"]},{"users":["aicountlyin_uuid_2854_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2854"},{"users":["aicountlyin_uuid_2855_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2855"},{"users":["aicountlyin_uuid_2856_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2856"},{"users":["aicountlyin_uuid_2857_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2857"},{"users":["aicountlyin_uuid_2858_usr"],"database":"aicountlyin_uuid_2858","disk_usage":65536},{"database":"aicountlyin_uuid_2859","disk_usage":65536,"users":["aicountlyin_uuid_2859_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2860","users":["aicountlyin_uuid_2860_usr"]},{"users":["aicountlyin_uuid_2861_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2861"},{"users":["aicountlyin_uuid_2862_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2862"},{"disk_usage":65536,"database":"aicountlyin_uuid_2863","users":["aicountlyin_uuid_2863_usr"]},{"database":"aicountlyin_uuid_2864","disk_usage":65536,"users":["aicountlyin_uuid_2864_usr"]},{"database":"aicountlyin_uuid_2865","disk_usage":65536,"users":["aicountlyin_uuid_2865_usr"]},{"database":"aicountlyin_uuid_2866","disk_usage":65536,"users":["aicountlyin_uuid_2866_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2867","users":["aicountlyin_uuid_2867_usr"]},{"users":["aicountlyin_uuid_2868_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2868"},{"users":["aicountlyin_uuid_2869_usr"],"database":"aicountlyin_uuid_2869","disk_usage":65536},{"database":"aicountlyin_uuid_2870","disk_usage":65536,"users":["aicountlyin_uuid_2870_usr"]},{"users":["aicountlyin_uuid_2871_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2871"},{"users":["aicountlyin_uuid_2872_usr"],"database":"aicountlyin_uuid_2872","disk_usage":65536},{"users":["aicountlyin_uuid_2873_usr"],"database":"aicountlyin_uuid_2873","disk_usage":65536},{"users":["aicountlyin_uuid_2874_usr"],"database":"aicountlyin_uuid_2874","disk_usage":65536},{"users":["aicountlyin_uuid_2875_usr"],"database":"aicountlyin_uuid_2875","disk_usage":65536},{"users":["aicountlyin_uuid_2876_usr"],"database":"aicountlyin_uuid_2876","disk_usage":65536},{"users":["aicountlyin_uuid_2877_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2877"},{"disk_usage":65536,"database":"aicountlyin_uuid_2878","users":["aicountlyin_uuid_2878_usr"]},{"database":"aicountlyin_uuid_2879","disk_usage":65536,"users":["aicountlyin_uuid_2879_usr"]},{"database":"aicountlyin_uuid_2880","disk_usage":65536,"users":["aicountlyin_uuid_2880_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2881","users":["aicountlyin_uuid_2881_usr"]},{"database":"aicountlyin_uuid_2882","disk_usage":65536,"users":["aicountlyin_uuid_2882_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2883","users":["aicountlyin_uuid_2883_usr"]},{"users":["aicountlyin_uuid_2884_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2884"},{"users":["aicountlyin_uuid_2885_usr"],"database":"aicountlyin_uuid_2885","disk_usage":65536},{"database":"aicountlyin_uuid_2886","disk_usage":65536,"users":["aicountlyin_uuid_2886_usr"]},{"users":["aicountlyin_uuid_2887_usr"],"database":"aicountlyin_uuid_2887","disk_usage":65536},{"database":"aicountlyin_uuid_2888","disk_usage":65536,"users":["aicountlyin_uuid_2888_usr"]},{"database":"aicountlyin_uuid_2889","disk_usage":65536,"users":["aicountlyin_uuid_2889_usr"]},{"database":"aicountlyin_uuid_2890","disk_usage":65536,"users":["aicountlyin_uuid_2890_usr"]},{"database":"aicountlyin_uuid_2891","disk_usage":65536,"users":["aicountlyin_uuid_2891_usr"]},{"users":["aicountlyin_uuid_2892_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2892"},{"disk_usage":65536,"database":"aicountlyin_uuid_2893","users":["aicountlyin_uuid_2893_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2894","users":["aicountlyin_uuid_2894_usr"]},{"users":["aicountlyin_uuid_2895_usr"],"database":"aicountlyin_uuid_2895","disk_usage":65536},{"users":["aicountlyin_uuid_2896_usr"],"database":"aicountlyin_uuid_2896","disk_usage":65536},{"users":["aicountlyin_uuid_2897_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2897"},{"users":["aicountlyin_uuid_2898_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2898"},{"users":["aicountlyin_uuid_2899_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2899"},{"users":["aicountlyin_uuid_29_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_29"},{"users":["aicountlyin_uuid_2900_usr"],"database":"aicountlyin_uuid_2900","disk_usage":65536},{"users":["aicountlyin_uuid_2901_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2901"},{"users":["aicountlyin_uuid_2902_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2902"},{"database":"aicountlyin_uuid_2903","disk_usage":65536,"users":["aicountlyin_uuid_2903_usr"]},{"users":["aicountlyin_uuid_2904_usr"],"database":"aicountlyin_uuid_2904","disk_usage":65536},{"database":"aicountlyin_uuid_2905","disk_usage":65536,"users":["aicountlyin_uuid_2905_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2906","users":["aicountlyin_uuid_2906_usr"]},{"users":["aicountlyin_uuid_2907_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2907"},{"users":["aicountlyin_uuid_2908_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2908"},{"users":["aicountlyin_uuid_2909_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2909"},{"users":["aicountlyin_uuid_2910_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2910"},{"database":"aicountlyin_uuid_2911","disk_usage":65536,"users":["aicountlyin_uuid_2911_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2912","users":["aicountlyin_uuid_2912_usr"]},{"users":["aicountlyin_uuid_2913_usr"],"database":"aicountlyin_uuid_2913","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2914","users":["aicountlyin_uuid_2914_usr"]},{"database":"aicountlyin_uuid_2915","disk_usage":65536,"users":["aicountlyin_uuid_2915_usr"]},{"users":["aicountlyin_uuid_2916_usr"],"database":"aicountlyin_uuid_2916","disk_usage":65536},{"users":["aicountlyin_uuid_2917_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2917"},{"disk_usage":65536,"database":"aicountlyin_uuid_2918","users":["aicountlyin_uuid_2918_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2919","users":["aicountlyin_uuid_2919_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2920","users":["aicountlyin_uuid_2920_usr"]},{"users":["aicountlyin_uuid_2921_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2921"},{"disk_usage":65536,"database":"aicountlyin_uuid_2922","users":["aicountlyin_uuid_2922_usr"]},{"users":["aicountlyin_uuid_2923_usr"],"database":"aicountlyin_uuid_2923","disk_usage":65536},{"users":["aicountlyin_uuid_2924_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2924"},{"users":["aicountlyin_uuid_2925_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2925"},{"users":["aicountlyin_uuid_2926_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2926"},{"users":["aicountlyin_uuid_2927_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2927"},{"users":["aicountlyin_uuid_2928_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2928"},{"users":["aicountlyin_uuid_2929_usr"],"database":"aicountlyin_uuid_2929","disk_usage":65536},{"users":["aicountlyin_uuid_2930_usr"],"database":"aicountlyin_uuid_2930","disk_usage":65536},{"users":["aicountlyin_uuid_2931_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2931"},{"database":"aicountlyin_uuid_2932","disk_usage":65536,"users":["aicountlyin_uuid_2932_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2933","users":["aicountlyin_uuid_2933_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2934","users":["aicountlyin_uuid_2934_usr"]},{"database":"aicountlyin_uuid_2935","disk_usage":65536,"users":["aicountlyin_uuid_2935_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2936","users":["aicountlyin_uuid_2936_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2937","users":["aicountlyin_uuid_2937_usr"]},{"database":"aicountlyin_uuid_2938","disk_usage":65536,"users":["aicountlyin_uuid_2938_usr"]},{"users":["aicountlyin_uuid_2939_usr"],"database":"aicountlyin_uuid_2939","disk_usage":65536},{"users":["aicountlyin_uuid_2940_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2940"},{"users":["aicountlyin_uuid_2941_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2941"},{"users":["aicountlyin_uuid_2942_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2942"},{"database":"aicountlyin_uuid_2943","disk_usage":65536,"users":["aicountlyin_uuid_2943_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2944","users":["aicountlyin_uuid_2944_usr"]},{"users":["aicountlyin_uuid_2945_usr"],"database":"aicountlyin_uuid_2945","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2946","users":["aicountlyin_uuid_2946_usr"]},{"users":["aicountlyin_uuid_2947_usr"],"database":"aicountlyin_uuid_2947","disk_usage":65536},{"database":"aicountlyin_uuid_2948","disk_usage":65536,"users":["aicountlyin_uuid_2948_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2949","users":["aicountlyin_uuid_2949_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2950","users":["aicountlyin_uuid_2950_usr"]},{"database":"aicountlyin_uuid_2951","disk_usage":65536,"users":["aicountlyin_uuid_2951_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2952","users":["aicountlyin_uuid_2952_usr"]},{"users":["aicountlyin_uuid_2953_usr"],"database":"aicountlyin_uuid_2953","disk_usage":65536},{"users":["aicountlyin_uuid_2954_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2954"},{"users":["aicountlyin_uuid_2955_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2955"},{"users":["aicountlyin_uuid_2956_usr"],"database":"aicountlyin_uuid_2956","disk_usage":65536},{"users":["aicountlyin_uuid_2957_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2957"},{"database":"aicountlyin_uuid_2958","disk_usage":65536,"users":["aicountlyin_uuid_2958_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2959","users":["aicountlyin_uuid_2959_usr"]},{"users":["aicountlyin_uuid_2960_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2960"},{"users":["aicountlyin_uuid_2961_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2961"},{"users":["aicountlyin_uuid_2962_usr"],"database":"aicountlyin_uuid_2962","disk_usage":65536},{"database":"aicountlyin_uuid_2963","disk_usage":65536,"users":["aicountlyin_uuid_2963_usr"]},{"users":["aicountlyin_uuid_2964_usr"],"database":"aicountlyin_uuid_2964","disk_usage":65536},{"database":"aicountlyin_uuid_2965","disk_usage":65536,"users":["aicountlyin_uuid_2965_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2966","users":["aicountlyin_uuid_2966_usr"]},{"users":["aicountlyin_uuid_2967_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2967"},{"users":["aicountlyin_uuid_2968_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2968"},{"users":["aicountlyin_uuid_2969_usr"],"database":"aicountlyin_uuid_2969","disk_usage":65536},{"users":["aicountlyin_uuid_2970_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2970"},{"users":["aicountlyin_uuid_2971_usr"],"database":"aicountlyin_uuid_2971","disk_usage":65536},{"users":["aicountlyin_uuid_2972_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2972"},{"disk_usage":65536,"database":"aicountlyin_uuid_2973","users":["aicountlyin_uuid_2973_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2974","users":["aicountlyin_uuid_2974_usr"]},{"users":["aicountlyin_uuid_2975_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2975"},{"users":["aicountlyin_uuid_2976_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2976"},{"disk_usage":65536,"database":"aicountlyin_uuid_2977","users":["aicountlyin_uuid_2977_usr"]},{"users":["aicountlyin_uuid_2978_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2978"},{"disk_usage":65536,"database":"aicountlyin_uuid_2979","users":["aicountlyin_uuid_2979_usr"]},{"users":["aicountlyin_uuid_2980_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2980"},{"users":["aicountlyin_uuid_2981_usr"],"database":"aicountlyin_uuid_2981","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_2982","users":["aicountlyin_uuid_2982_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2983","users":["aicountlyin_uuid_2983_usr"]},{"users":["aicountlyin_uuid_2984_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2984"},{"database":"aicountlyin_uuid_2985","disk_usage":65536,"users":["aicountlyin_uuid_2985_usr"]},{"database":"aicountlyin_uuid_2986","disk_usage":65536,"users":["aicountlyin_uuid_2986_usr"]},{"users":["aicountlyin_uuid_2987_usr"],"database":"aicountlyin_uuid_2987","disk_usage":65536},{"users":["aicountlyin_uuid_2988_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2988"},{"users":["aicountlyin_uuid_2989_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2989"},{"users":["aicountlyin_uuid_2990_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2990"},{"disk_usage":65536,"database":"aicountlyin_uuid_2991","users":["aicountlyin_uuid_2991_usr"]},{"users":["aicountlyin_uuid_2992_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2992"},{"database":"aicountlyin_uuid_2993","disk_usage":65536,"users":["aicountlyin_uuid_2993_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_2994","users":["aicountlyin_uuid_2994_usr"]},{"users":["aicountlyin_uuid_2995_usr"],"database":"aicountlyin_uuid_2995","disk_usage":65536},{"users":["aicountlyin_uuid_2996_usr"],"database":"aicountlyin_uuid_2996","disk_usage":65536},{"users":["aicountlyin_uuid_2997_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_2997"},{"users":["aicountlyin_uuid_2998_usr"],"database":"aicountlyin_uuid_2998","disk_usage":65536},{"database":"aicountlyin_uuid_2999","disk_usage":65536,"users":["aicountlyin_uuid_2999_usr"]},{"database":"aicountlyin_uuid_3","disk_usage":131072,"users":["aicountlyin_uuid_3_usr"]},{"disk_usage":131072,"database":"aicountlyin_uuid_30","users":["aicountlyin_uuid_30_usr"]},{"users":["aicountlyin_uuid_3000_usr"],"database":"aicountlyin_uuid_3000","disk_usage":65536},{"database":"aicountlyin_uuid_3001","disk_usage":65536,"users":["aicountlyin_uuid_3001_usr"]},{"database":"aicountlyin_uuid_3002","disk_usage":65536,"users":["aicountlyin_uuid_3002_usr"]},{"users":["aicountlyin_uuid_3003_usr"],"database":"aicountlyin_uuid_3003","disk_usage":65536},{"users":["aicountlyin_uuid_3004_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3004"},{"users":["aicountlyin_uuid_3005_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3005"},{"users":["aicountlyin_uuid_3006_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3006"},{"database":"aicountlyin_uuid_3007","disk_usage":65536,"users":["aicountlyin_uuid_3007_usr"]},{"users":["aicountlyin_uuid_3008_usr"],"database":"aicountlyin_uuid_3008","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_3009","users":["aicountlyin_uuid_3009_usr"]},{"database":"aicountlyin_uuid_3010","disk_usage":65536,"users":["aicountlyin_uuid_3010_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_3011","users":["aicountlyin_uuid_3011_usr"]},{"database":"aicountlyin_uuid_3012","disk_usage":65536,"users":["aicountlyin_uuid_3012_usr"]},{"users":["aicountlyin_uuid_3013_usr"],"database":"aicountlyin_uuid_3013","disk_usage":65536},{"users":["aicountlyin_uuid_3014_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3014"},{"users":["aicountlyin_uuid_3015_usr"],"database":"aicountlyin_uuid_3015","disk_usage":65536},{"database":"aicountlyin_uuid_3016","disk_usage":65536,"users":["aicountlyin_uuid_3016_usr"]},{"users":["aicountlyin_uuid_3017_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3017"},{"database":"aicountlyin_uuid_3019","disk_usage":65536,"users":["aicountlyin_uuid_3019_usr"]},{"database":"aicountlyin_uuid_3020","disk_usage":65536,"users":["aicountlyin_uuid_3020_usr"]},{"users":["aicountlyin_uuid_3021_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3021"},{"disk_usage":65536,"database":"aicountlyin_uuid_3022","users":["aicountlyin_uuid_3022_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_3023","users":["aicountlyin_uuid_3023_usr"]},{"users":["aicountlyin_uuid_3024_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3024"},{"database":"aicountlyin_uuid_3025","disk_usage":65536,"users":["aicountlyin_uuid_3025_usr"]},{"database":"aicountlyin_uuid_3026","disk_usage":65536,"users":["aicountlyin_uuid_3026_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_3027","users":["aicountlyin_uuid_3027_usr"]},{"users":["aicountlyin_uuid_3028_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3028"},{"disk_usage":65536,"database":"aicountlyin_uuid_3029","users":["aicountlyin_uuid_3029_usr"]},{"users":["aicountlyin_uuid_3030_usr"],"database":"aicountlyin_uuid_3030","disk_usage":65536},{"users":["aicountlyin_uuid_3031_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3031"},{"users":["aicountlyin_uuid_3032_usr"],"database":"aicountlyin_uuid_3032","disk_usage":65536},{"users":["aicountlyin_uuid_3033_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3033"},{"disk_usage":65536,"database":"aicountlyin_uuid_3034","users":["aicountlyin_uuid_3034_usr"]},{"database":"aicountlyin_uuid_3035","disk_usage":65536,"users":["aicountlyin_uuid_3035_usr"]},{"users":["aicountlyin_uuid_3036_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_3036"},{"disk_usage":65536,"database":"aicountlyin_uuid_3037","users":["aicountlyin_uuid_3037_usr"]},{"users":["aicountlyin_uuid_304_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_304"},{"database":"aicountlyin_uuid_31","disk_usage":131072,"users":["aicountlyin_uuid_31_usr"]},{"users":["aicountlyin_uuid_313_usr"],"database":"aicountlyin_uuid_313","disk_usage":65536},{"database":"aicountlyin_uuid_32","disk_usage":131072,"users":["aicountlyin_uuid_32_usr"]},{"database":"aicountlyin_uuid_326","disk_usage":65536,"users":["aicountlyin_uuid_326_usr"]},{"users":["aicountlyin_uuid_327_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_327"},{"disk_usage":65536,"database":"aicountlyin_uuid_328","users":["aicountlyin_uuid_328_usr"]},{"users":["aicountlyin_uuid_329_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_329"},{"users":["aicountlyin_uuid_33_usr"],"database":"aicountlyin_uuid_33","disk_usage":131072},{"disk_usage":65536,"database":"aicountlyin_uuid_330","users":["aicountlyin_uuid_330_usr"]},{"database":"aicountlyin_uuid_331","disk_usage":65536,"users":["aicountlyin_uuid_331_usr"]},{"users":["aicountlyin_uuid_332_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_332"},{"users":["aicountlyin_uuid_333_usr"],"database":"aicountlyin_uuid_333","disk_usage":65536},{"users":["aicountlyin_uuid_334_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_334"},{"users":["aicountlyin_uuid_335_usr"],"database":"aicountlyin_uuid_335","disk_usage":65536},{"users":["aicountlyin_uuid_336_usr"],"database":"aicountlyin_uuid_336","disk_usage":65536},{"users":["aicountlyin_uuid_337_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_337"},{"users":["aicountlyin_uuid_338_usr"],"database":"aicountlyin_uuid_338","disk_usage":65536},{"database":"aicountlyin_uuid_339","disk_usage":65536,"users":["aicountlyin_uuid_339_usr"]},{"disk_usage":131072,"database":"aicountlyin_uuid_34","users":["aicountlyin_uuid_34_usr"]},{"users":["aicountlyin_uuid_340_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_340"},{"users":["aicountlyin_uuid_341_usr"],"database":"aicountlyin_uuid_341","disk_usage":65536},{"users":["aicountlyin_uuid_342_usr"],"database":"aicountlyin_uuid_342","disk_usage":65536},{"users":["aicountlyin_uuid_343_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_343"},{"database":"aicountlyin_uuid_344","disk_usage":65536,"users":["aicountlyin_uuid_344_usr"]},{"database":"aicountlyin_uuid_345","disk_usage":65536,"users":["aicountlyin_uuid_345_usr"]},{"users":["aicountlyin_uuid_346_usr"],"database":"aicountlyin_uuid_346","disk_usage":65536},{"users":["aicountlyin_uuid_347_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_347"},{"disk_usage":65536,"database":"aicountlyin_uuid_348","users":["aicountlyin_uuid_348_usr"]},{"database":"aicountlyin_uuid_349","disk_usage":65536,"users":["aicountlyin_uuid_349_usr"]},{"database":"aicountlyin_uuid_35","disk_usage":131072,"users":["aicountlyin_uuid_35_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_350","users":["aicountlyin_uuid_350_usr"]},{"users":["aicountlyin_uuid_351_usr"],"database":"aicountlyin_uuid_351","disk_usage":65536},{"database":"aicountlyin_uuid_352","disk_usage":65536,"users":["aicountlyin_uuid_352_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_353","users":["aicountlyin_uuid_353_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_354","users":["aicountlyin_uuid_354_usr"]},{"users":["aicountlyin_uuid_355_usr"],"database":"aicountlyin_uuid_355","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_356","users":["aicountlyin_uuid_356_usr"]},{"users":["aicountlyin_uuid_357_usr"],"database":"aicountlyin_uuid_357","disk_usage":65536},{"database":"aicountlyin_uuid_358","disk_usage":65536,"users":["aicountlyin_uuid_358_usr"]},{"database":"aicountlyin_uuid_359","disk_usage":65536,"users":["aicountlyin_uuid_359_usr"]},{"database":"aicountlyin_uuid_36","disk_usage":131072,"users":["aicountlyin_uuid_36_usr"]},{"users":["aicountlyin_uuid_360_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_360"},{"users":["aicountlyin_uuid_361_usr"],"database":"aicountlyin_uuid_361","disk_usage":65536},{"users":["aicountlyin_uuid_362_usr"],"database":"aicountlyin_uuid_362","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_363","users":["aicountlyin_uuid_363_usr"]},{"database":"aicountlyin_uuid_364","disk_usage":65536,"users":["aicountlyin_uuid_364_usr"]},{"users":["aicountlyin_uuid_365_usr"],"database":"aicountlyin_uuid_365","disk_usage":65536},{"users":["aicountlyin_uuid_366_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_366"},{"disk_usage":65536,"database":"aicountlyin_uuid_367","users":["aicountlyin_uuid_367_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_368","users":["aicountlyin_uuid_368_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_369","users":["aicountlyin_uuid_369_usr"]},{"database":"aicountlyin_uuid_370","disk_usage":65536,"users":["aicountlyin_uuid_370_usr"]},{"database":"aicountlyin_uuid_371","disk_usage":65536,"users":["aicountlyin_uuid_371_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_372","users":["aicountlyin_uuid_372_usr"]},{"users":["aicountlyin_uuid_373_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_373"},{"database":"aicountlyin_uuid_374","disk_usage":65536,"users":["aicountlyin_uuid_374_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_375","users":["aicountlyin_uuid_375_usr"]},{"users":["aicountlyin_uuid_376_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_376"},{"users":["aicountlyin_uuid_377_usr"],"database":"aicountlyin_uuid_377","disk_usage":65536},{"users":["aicountlyin_uuid_378_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_378"},{"users":["aicountlyin_uuid_379_usr"],"database":"aicountlyin_uuid_379","disk_usage":65536},{"users":["aicountlyin_uuid_380_usr"],"database":"aicountlyin_uuid_380","disk_usage":65536},{"users":["aicountlyin_uuid_381_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_381"},{"database":"aicountlyin_uuid_382","disk_usage":65536,"users":["aicountlyin_uuid_382_usr"]},{"database":"aicountlyin_uuid_383","disk_usage":65536,"users":["aicountlyin_uuid_383_usr"]},{"database":"aicountlyin_uuid_384","disk_usage":65536,"users":["aicountlyin_uuid_384_usr"]},{"users":["aicountlyin_uuid_385_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_385"},{"database":"aicountlyin_uuid_386","disk_usage":65536,"users":["aicountlyin_uuid_386_usr"]},{"users":["aicountlyin_uuid_387_usr"],"database":"aicountlyin_uuid_387","disk_usage":65536},{"users":["aicountlyin_uuid_388_usr"],"database":"aicountlyin_uuid_388","disk_usage":65536},{"users":["aicountlyin_uuid_389_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_389"},{"disk_usage":65536,"database":"aicountlyin_uuid_390","users":["aicountlyin_uuid_390_usr"]},{"users":["aicountlyin_uuid_391_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_391"},{"database":"aicountlyin_uuid_392","disk_usage":65536,"users":["aicountlyin_uuid_392_usr"]},{"users":["aicountlyin_uuid_393_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_393"},{"database":"aicountlyin_uuid_394","disk_usage":65536,"users":["aicountlyin_uuid_394_usr"]},{"database":"aicountlyin_uuid_395","disk_usage":65536,"users":["aicountlyin_uuid_395_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_396","users":["aicountlyin_uuid_396_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_397","users":["aicountlyin_uuid_397_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_398","users":["aicountlyin_uuid_398_usr"]},{"users":["aicountlyin_uuid_399_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_399"},{"users":["aicountlyin_uuid_4_usr"],"database":"aicountlyin_uuid_4","disk_usage":229376},{"users":["aicountlyin_uuid_400_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_400"},{"users":["aicountlyin_uuid_401_usr"],"database":"aicountlyin_uuid_401","disk_usage":65536},{"users":["aicountlyin_uuid_402_usr"],"database":"aicountlyin_uuid_402","disk_usage":65536},{"users":["aicountlyin_uuid_403_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_403"},{"users":["aicountlyin_uuid_404_usr"],"database":"aicountlyin_uuid_404","disk_usage":65536},{"database":"aicountlyin_uuid_405","disk_usage":65536,"users":["aicountlyin_uuid_405_usr"]},{"users":["aicountlyin_uuid_406_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_406"},{"users":["aicountlyin_uuid_407_usr"],"database":"aicountlyin_uuid_407","disk_usage":65536},{"users":["aicountlyin_uuid_408_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_408"},{"database":"aicountlyin_uuid_409","disk_usage":65536,"users":["aicountlyin_uuid_409_usr"]},{"users":["aicountlyin_uuid_410_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_410"},{"database":"aicountlyin_uuid_411","disk_usage":65536,"users":["aicountlyin_uuid_411_usr"]},{"users":["aicountlyin_uuid_412_usr"],"database":"aicountlyin_uuid_412","disk_usage":65536},{"users":["aicountlyin_uuid_413_usr"],"database":"aicountlyin_uuid_413","disk_usage":65536},{"database":"aicountlyin_uuid_414","disk_usage":65536,"users":["aicountlyin_uuid_414_usr"]},{"users":["aicountlyin_uuid_415_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_415"},{"database":"aicountlyin_uuid_416","disk_usage":65536,"users":["aicountlyin_uuid_416_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_417","users":["aicountlyin_uuid_417_usr"]},{"users":["aicountlyin_uuid_418_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_418"},{"database":"aicountlyin_uuid_419","disk_usage":65536,"users":["aicountlyin_uuid_419_usr"]},{"database":"aicountlyin_uuid_420","disk_usage":65536,"users":["aicountlyin_uuid_420_usr"]},{"users":["aicountlyin_uuid_421_usr"],"database":"aicountlyin_uuid_421","disk_usage":65536},{"users":["aicountlyin_uuid_422_usr"],"database":"aicountlyin_uuid_422","disk_usage":65536},{"users":["aicountlyin_uuid_423_usr"],"database":"aicountlyin_uuid_423","disk_usage":65536},{"database":"aicountlyin_uuid_424","disk_usage":65536,"users":["aicountlyin_uuid_424_usr"]},{"database":"aicountlyin_uuid_425","disk_usage":65536,"users":["aicountlyin_uuid_425_usr"]},{"users":["aicountlyin_uuid_426_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_426"},{"users":["aicountlyin_uuid_427_usr"],"database":"aicountlyin_uuid_427","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_428","users":["aicountlyin_uuid_428_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_429","users":["aicountlyin_uuid_429_usr"]},{"database":"aicountlyin_uuid_430","disk_usage":65536,"users":["aicountlyin_uuid_430_usr"]},{"database":"aicountlyin_uuid_431","disk_usage":65536,"users":["aicountlyin_uuid_431_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_432","users":["aicountlyin_uuid_432_usr"]},{"users":["aicountlyin_uuid_433_usr"],"database":"aicountlyin_uuid_433","disk_usage":65536},{"users":["aicountlyin_uuid_434_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_434"},{"disk_usage":65536,"database":"aicountlyin_uuid_435","users":["aicountlyin_uuid_435_usr"]},{"users":["aicountlyin_uuid_436_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_436"},{"users":["aicountlyin_uuid_437_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_437"},{"disk_usage":65536,"database":"aicountlyin_uuid_438","users":["aicountlyin_uuid_438_usr"]},{"users":["aicountlyin_uuid_439_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_439"},{"database":"aicountlyin_uuid_440","disk_usage":65536,"users":["aicountlyin_uuid_440_usr"]},{"users":["aicountlyin_uuid_441_usr"],"database":"aicountlyin_uuid_441","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_442","users":["aicountlyin_uuid_442_usr"]},{"users":["aicountlyin_uuid_443_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_443"},{"database":"aicountlyin_uuid_444","disk_usage":65536,"users":["aicountlyin_uuid_444_usr"]},{"users":["aicountlyin_uuid_445_usr"],"database":"aicountlyin_uuid_445","disk_usage":65536},{"users":["aicountlyin_uuid_446_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_446"},{"disk_usage":65536,"database":"aicountlyin_uuid_447","users":["aicountlyin_uuid_447_usr"]},{"database":"aicountlyin_uuid_448","disk_usage":65536,"users":["aicountlyin_uuid_448_usr"]},{"users":["aicountlyin_uuid_449_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_449"},{"users":["aicountlyin_uuid_450_usr"],"database":"aicountlyin_uuid_450","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_451","users":["aicountlyin_uuid_451_usr"]},{"database":"aicountlyin_uuid_452","disk_usage":65536,"users":["aicountlyin_uuid_452_usr"]},{"users":["aicountlyin_uuid_453_usr"],"database":"aicountlyin_uuid_453","disk_usage":65536},{"users":["aicountlyin_uuid_454_usr"],"database":"aicountlyin_uuid_454","disk_usage":65536},{"database":"aicountlyin_uuid_455","disk_usage":65536,"users":["aicountlyin_uuid_455_usr"]},{"database":"aicountlyin_uuid_456","disk_usage":65536,"users":["aicountlyin_uuid_456_usr"]},{"database":"aicountlyin_uuid_457","disk_usage":65536,"users":["aicountlyin_uuid_457_usr"]},{"users":["aicountlyin_uuid_458_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_458"},{"users":["aicountlyin_uuid_459_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_459"},{"users":["aicountlyin_uuid_46_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_46"},{"disk_usage":65536,"database":"aicountlyin_uuid_460","users":["aicountlyin_uuid_460_usr"]},{"users":["aicountlyin_uuid_461_usr"],"database":"aicountlyin_uuid_461","disk_usage":65536},{"users":["aicountlyin_uuid_462_usr"],"database":"aicountlyin_uuid_462","disk_usage":65536},{"users":["aicountlyin_uuid_463_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_463"},{"users":["aicountlyin_uuid_464_usr"],"database":"aicountlyin_uuid_464","disk_usage":65536},{"database":"aicountlyin_uuid_465","disk_usage":65536,"users":["aicountlyin_uuid_465_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_466","users":["aicountlyin_uuid_466_usr"]},{"users":["aicountlyin_uuid_467_usr"],"database":"aicountlyin_uuid_467","disk_usage":65536},{"users":["aicountlyin_uuid_468_usr"],"database":"aicountlyin_uuid_468","disk_usage":65536},{"users":["aicountlyin_uuid_469_usr"],"database":"aicountlyin_uuid_469","disk_usage":65536},{"users":["aicountlyin_uuid_470_usr"],"database":"aicountlyin_uuid_470","disk_usage":65536},{"database":"aicountlyin_uuid_471","disk_usage":65536,"users":["aicountlyin_uuid_471_usr"]},{"users":["aicountlyin_uuid_472_usr"],"database":"aicountlyin_uuid_472","disk_usage":65536},{"users":["aicountlyin_uuid_473_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_473"},{"users":["aicountlyin_uuid_474_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_474"},{"users":["aicountlyin_uuid_475_usr"],"database":"aicountlyin_uuid_475","disk_usage":65536},{"users":["aicountlyin_uuid_476_usr"],"database":"aicountlyin_uuid_476","disk_usage":65536},{"users":["aicountlyin_uuid_477_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_477"},{"users":["aicountlyin_uuid_478_usr"],"database":"aicountlyin_uuid_478","disk_usage":65536},{"database":"aicountlyin_uuid_479","disk_usage":65536,"users":["aicountlyin_uuid_479_usr"]},{"database":"aicountlyin_uuid_480","disk_usage":65536,"users":["aicountlyin_uuid_480_usr"]},{"database":"aicountlyin_uuid_481","disk_usage":65536,"users":["aicountlyin_uuid_481_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_482","users":["aicountlyin_uuid_482_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_483","users":["aicountlyin_uuid_483_usr"]},{"users":["aicountlyin_uuid_484_usr"],"database":"aicountlyin_uuid_484","disk_usage":65536},{"users":["aicountlyin_uuid_485_usr"],"database":"aicountlyin_uuid_485","disk_usage":65536},{"users":["aicountlyin_uuid_486_usr"],"database":"aicountlyin_uuid_486","disk_usage":65536},{"database":"aicountlyin_uuid_487","disk_usage":65536,"users":["aicountlyin_uuid_487_usr"]},{"database":"aicountlyin_uuid_488","disk_usage":65536,"users":["aicountlyin_uuid_488_usr"]},{"users":["aicountlyin_uuid_489_usr"],"database":"aicountlyin_uuid_489","disk_usage":65536},{"database":"aicountlyin_uuid_49","disk_usage":131072,"users":["aicountlyin_uuid_49_usr"]},{"users":["aicountlyin_uuid_490_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_490"},{"database":"aicountlyin_uuid_491","disk_usage":65536,"users":["aicountlyin_uuid_491_usr"]},{"users":["aicountlyin_uuid_492_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_492"},{"users":["aicountlyin_uuid_493_usr"],"database":"aicountlyin_uuid_493","disk_usage":65536},{"users":["aicountlyin_uuid_494_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_494"},{"database":"aicountlyin_uuid_495","disk_usage":65536,"users":["aicountlyin_uuid_495_usr"]},{"database":"aicountlyin_uuid_496","disk_usage":65536,"users":["aicountlyin_uuid_496_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_497","users":["aicountlyin_uuid_497_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_498","users":["aicountlyin_uuid_498_usr"]},{"database":"aicountlyin_uuid_499","disk_usage":65536,"users":["aicountlyin_uuid_499_usr"]},{"users":["aicountlyin_uuid_5_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_5"},{"users":["aicountlyin_uuid_50_usr"],"database":"aicountlyin_uuid_50","disk_usage":131072},{"database":"aicountlyin_uuid_500","disk_usage":65536,"users":["aicountlyin_uuid_500_usr"]},{"users":["aicountlyin_uuid_501_usr"],"database":"aicountlyin_uuid_501","disk_usage":65536},{"users":["aicountlyin_uuid_502_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_502"},{"users":["aicountlyin_uuid_503_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_503"},{"users":["aicountlyin_uuid_504_usr"],"database":"aicountlyin_uuid_504","disk_usage":65536},{"users":["aicountlyin_uuid_505_usr"],"database":"aicountlyin_uuid_505","disk_usage":65536},{"database":"aicountlyin_uuid_506","disk_usage":65536,"users":["aicountlyin_uuid_506_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_507","users":["aicountlyin_uuid_507_usr"]},{"users":["aicountlyin_uuid_508_usr"],"database":"aicountlyin_uuid_508","disk_usage":65536},{"users":["aicountlyin_uuid_509_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_509"},{"disk_usage":131072,"database":"aicountlyin_uuid_51","users":["aicountlyin_uuid_51_usr"]},{"database":"aicountlyin_uuid_510","disk_usage":65536,"users":["aicountlyin_uuid_510_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_511","users":["aicountlyin_uuid_511_usr"]},{"users":["aicountlyin_uuid_512_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_512"},{"users":["aicountlyin_uuid_513_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_513"},{"database":"aicountlyin_uuid_514","disk_usage":65536,"users":["aicountlyin_uuid_514_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_515","users":["aicountlyin_uuid_515_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_516","users":["aicountlyin_uuid_516_usr"]},{"database":"aicountlyin_uuid_517","disk_usage":65536,"users":["aicountlyin_uuid_517_usr"]},{"users":["aicountlyin_uuid_518_usr"],"database":"aicountlyin_uuid_518","disk_usage":65536},{"users":["aicountlyin_uuid_519_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_519"},{"users":["aicountlyin_uuid_52_usr"],"database":"aicountlyin_uuid_52","disk_usage":131072},{"disk_usage":65536,"database":"aicountlyin_uuid_520","users":["aicountlyin_uuid_520_usr"]},{"users":["aicountlyin_uuid_521_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_521"},{"users":["aicountlyin_uuid_522_usr"],"database":"aicountlyin_uuid_522","disk_usage":65536},{"users":["aicountlyin_uuid_523_usr"],"database":"aicountlyin_uuid_523","disk_usage":65536},{"users":["aicountlyin_uuid_524_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_524"},{"users":["aicountlyin_uuid_525_usr"],"database":"aicountlyin_uuid_525","disk_usage":65536},{"database":"aicountlyin_uuid_526","disk_usage":65536,"users":["aicountlyin_uuid_526_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_527","users":["aicountlyin_uuid_527_usr"]},{"users":["aicountlyin_uuid_528_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_528"},{"users":["aicountlyin_uuid_529_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_529"},{"users":["aicountlyin_uuid_53_usr"],"database":"aicountlyin_uuid_53","disk_usage":131072},{"users":["aicountlyin_uuid_530_usr"],"database":"aicountlyin_uuid_530","disk_usage":65536},{"database":"aicountlyin_uuid_531","disk_usage":65536,"users":["aicountlyin_uuid_531_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_532","users":["aicountlyin_uuid_532_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_533","users":["aicountlyin_uuid_533_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_534","users":["aicountlyin_uuid_534_usr"]},{"database":"aicountlyin_uuid_535","disk_usage":65536,"users":["aicountlyin_uuid_535_usr"]},{"users":["aicountlyin_uuid_536_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_536"},{"users":["aicountlyin_uuid_537_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_537"},{"users":["aicountlyin_uuid_538_usr"],"database":"aicountlyin_uuid_538","disk_usage":65536},{"users":["aicountlyin_uuid_539_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_539"},{"users":["aicountlyin_uuid_54_usr"],"database":"aicountlyin_uuid_54","disk_usage":229376},{"disk_usage":65536,"database":"aicountlyin_uuid_540","users":["aicountlyin_uuid_540_usr"]},{"database":"aicountlyin_uuid_541","disk_usage":65536,"users":["aicountlyin_uuid_541_usr"]},{"users":["aicountlyin_uuid_542_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_542"},{"users":["aicountlyin_uuid_543_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_543"},{"database":"aicountlyin_uuid_544","disk_usage":65536,"users":["aicountlyin_uuid_544_usr"]},{"users":["aicountlyin_uuid_545_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_545"},{"users":["aicountlyin_uuid_546_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_546"},{"disk_usage":65536,"database":"aicountlyin_uuid_547","users":["aicountlyin_uuid_547_usr"]},{"users":["aicountlyin_uuid_548_usr"],"database":"aicountlyin_uuid_548","disk_usage":65536},{"users":["aicountlyin_uuid_549_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_549"},{"database":"aicountlyin_uuid_55","disk_usage":65536,"users":["aicountlyin_uuid_55_usr"]},{"users":["aicountlyin_uuid_550_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_550"},{"database":"aicountlyin_uuid_551","disk_usage":65536,"users":["aicountlyin_uuid_551_usr"]},{"database":"aicountlyin_uuid_552","disk_usage":65536,"users":["aicountlyin_uuid_552_usr"]},{"database":"aicountlyin_uuid_553","disk_usage":65536,"users":["aicountlyin_uuid_553_usr"]},{"users":["aicountlyin_uuid_554_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_554"},{"users":["aicountlyin_uuid_555_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_555"},{"database":"aicountlyin_uuid_556","disk_usage":65536,"users":["aicountlyin_uuid_556_usr"]},{"database":"aicountlyin_uuid_557","disk_usage":65536,"users":["aicountlyin_uuid_557_usr"]},{"database":"aicountlyin_uuid_558","disk_usage":65536,"users":["aicountlyin_uuid_558_usr"]},{"users":["aicountlyin_uuid_559_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_559"},{"users":["aicountlyin_uuid_56_usr"],"disk_usage":196608,"database":"aicountlyin_uuid_56"},{"database":"aicountlyin_uuid_560","disk_usage":65536,"users":["aicountlyin_uuid_560_usr"]},{"database":"aicountlyin_uuid_561","disk_usage":65536,"users":["aicountlyin_uuid_561_usr"]},{"users":["aicountlyin_uuid_562_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_562"},{"users":["aicountlyin_uuid_563_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_563"},{"database":"aicountlyin_uuid_564","disk_usage":65536,"users":["aicountlyin_uuid_564_usr"]},{"database":"aicountlyin_uuid_565","disk_usage":65536,"users":["aicountlyin_uuid_565_usr"]},{"users":["aicountlyin_uuid_566_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_566"},{"users":["aicountlyin_uuid_567_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_567"},{"database":"aicountlyin_uuid_568","disk_usage":65536,"users":["aicountlyin_uuid_568_usr"]},{"database":"aicountlyin_uuid_569","disk_usage":65536,"users":["aicountlyin_uuid_569_usr"]},{"users":["aicountlyin_uuid_57_usr"],"database":"aicountlyin_uuid_57","disk_usage":131072},{"disk_usage":65536,"database":"aicountlyin_uuid_570","users":["aicountlyin_uuid_570_usr"]},{"users":["aicountlyin_uuid_571_usr"],"database":"aicountlyin_uuid_571","disk_usage":65536},{"users":["aicountlyin_uuid_572_usr"],"database":"aicountlyin_uuid_572","disk_usage":65536},{"database":"aicountlyin_uuid_573","disk_usage":65536,"users":["aicountlyin_uuid_573_usr"]},{"database":"aicountlyin_uuid_574","disk_usage":65536,"users":["aicountlyin_uuid_574_usr"]},{"database":"aicountlyin_uuid_575","disk_usage":65536,"users":["aicountlyin_uuid_575_usr"]},{"users":["aicountlyin_uuid_576_usr"],"database":"aicountlyin_uuid_576","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_577","users":["aicountlyin_uuid_577_usr"]},{"database":"aicountlyin_uuid_578","disk_usage":65536,"users":["aicountlyin_uuid_578_usr"]},{"users":["aicountlyin_uuid_579_usr"],"database":"aicountlyin_uuid_579","disk_usage":65536},{"database":"aicountlyin_uuid_580","disk_usage":65536,"users":["aicountlyin_uuid_580_usr"]},{"users":["aicountlyin_uuid_581_usr"],"database":"aicountlyin_uuid_581","disk_usage":65536},{"users":["aicountlyin_uuid_582_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_582"},{"users":["aicountlyin_uuid_583_usr"],"database":"aicountlyin_uuid_583","disk_usage":65536},{"users":["aicountlyin_uuid_584_usr"],"database":"aicountlyin_uuid_584","disk_usage":65536},{"database":"aicountlyin_uuid_585","disk_usage":65536,"users":["aicountlyin_uuid_585_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_586","users":["aicountlyin_uuid_586_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_587","users":["aicountlyin_uuid_587_usr"]},{"users":["aicountlyin_uuid_588_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_588"},{"users":["aicountlyin_uuid_589_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_589"},{"users":["aicountlyin_uuid_590_usr"],"database":"aicountlyin_uuid_590","disk_usage":65536},{"users":["aicountlyin_uuid_591_usr"],"database":"aicountlyin_uuid_591","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_592","users":["aicountlyin_uuid_592_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_593","users":["aicountlyin_uuid_593_usr"]},{"database":"aicountlyin_uuid_594","disk_usage":65536,"users":["aicountlyin_uuid_594_usr"]},{"users":["aicountlyin_uuid_595_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_595"},{"database":"aicountlyin_uuid_596","disk_usage":65536,"users":["aicountlyin_uuid_596_usr"]},{"users":["aicountlyin_uuid_597_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_597"},{"users":["aicountlyin_uuid_598_usr"],"database":"aicountlyin_uuid_598","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_599","users":["aicountlyin_uuid_599_usr"]},{"users":["aicountlyin_uuid_6_usr"],"database":"aicountlyin_uuid_6","disk_usage":1703936},{"users":["aicountlyin_uuid_600_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_600"},{"users":["aicountlyin_uuid_601_usr"],"database":"aicountlyin_uuid_601","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_602","users":["aicountlyin_uuid_602_usr"]},{"users":["aicountlyin_uuid_603_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_603"},{"disk_usage":65536,"database":"aicountlyin_uuid_604","users":["aicountlyin_uuid_604_usr"]},{"database":"aicountlyin_uuid_605","disk_usage":65536,"users":["aicountlyin_uuid_605_usr"]},{"users":["aicountlyin_uuid_606_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_606"},{"database":"aicountlyin_uuid_607","disk_usage":65536,"users":["aicountlyin_uuid_607_usr"]},{"users":["aicountlyin_uuid_608_usr"],"database":"aicountlyin_uuid_608","disk_usage":65536},{"users":["aicountlyin_uuid_609_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_609"},{"database":"aicountlyin_uuid_610","disk_usage":65536,"users":["aicountlyin_uuid_610_usr"]},{"database":"aicountlyin_uuid_611","disk_usage":65536,"users":["aicountlyin_uuid_611_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_612","users":["aicountlyin_uuid_612_usr"]},{"users":["aicountlyin_uuid_613_usr"],"database":"aicountlyin_uuid_613","disk_usage":65536},{"users":["aicountlyin_uuid_614_usr"],"database":"aicountlyin_uuid_614","disk_usage":65536},{"users":["aicountlyin_uuid_615_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_615"},{"users":["aicountlyin_uuid_616_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_616"},{"database":"aicountlyin_uuid_617","disk_usage":65536,"users":["aicountlyin_uuid_617_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_618","users":["aicountlyin_uuid_618_usr"]},{"users":["aicountlyin_uuid_619_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_619"},{"users":["aicountlyin_uuid_620_usr"],"database":"aicountlyin_uuid_620","disk_usage":65536},{"database":"aicountlyin_uuid_621","disk_usage":65536,"users":["aicountlyin_uuid_621_usr"]},{"users":["aicountlyin_uuid_622_usr"],"database":"aicountlyin_uuid_622","disk_usage":65536},{"users":["aicountlyin_uuid_623_usr"],"database":"aicountlyin_uuid_623","disk_usage":65536},{"users":["aicountlyin_uuid_624_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_624"},{"database":"aicountlyin_uuid_625","disk_usage":65536,"users":["aicountlyin_uuid_625_usr"]},{"database":"aicountlyin_uuid_626","disk_usage":65536,"users":["aicountlyin_uuid_626_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_627","users":["aicountlyin_uuid_627_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_628","users":["aicountlyin_uuid_628_usr"]},{"database":"aicountlyin_uuid_629","disk_usage":65536,"users":["aicountlyin_uuid_629_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_630","users":["aicountlyin_uuid_630_usr"]},{"users":["aicountlyin_uuid_631_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_631"},{"disk_usage":65536,"database":"aicountlyin_uuid_632","users":["aicountlyin_uuid_632_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_633","users":["aicountlyin_uuid_633_usr"]},{"users":["aicountlyin_uuid_634_usr"],"database":"aicountlyin_uuid_634","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_635","users":["aicountlyin_uuid_635_usr"]},{"users":["aicountlyin_uuid_636_usr"],"database":"aicountlyin_uuid_636","disk_usage":65536},{"database":"aicountlyin_uuid_637","disk_usage":65536,"users":["aicountlyin_uuid_637_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_638","users":["aicountlyin_uuid_638_usr"]},{"database":"aicountlyin_uuid_639","disk_usage":65536,"users":["aicountlyin_uuid_639_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_640","users":["aicountlyin_uuid_640_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_641","users":["aicountlyin_uuid_641_usr"]},{"users":["aicountlyin_uuid_642_usr"],"database":"aicountlyin_uuid_642","disk_usage":65536},{"database":"aicountlyin_uuid_643","disk_usage":65536,"users":["aicountlyin_uuid_643_usr"]},{"database":"aicountlyin_uuid_644","disk_usage":65536,"users":["aicountlyin_uuid_644_usr"]},{"users":["aicountlyin_uuid_645_usr"],"database":"aicountlyin_uuid_645","disk_usage":65536},{"database":"aicountlyin_uuid_646","disk_usage":65536,"users":["aicountlyin_uuid_646_usr"]},{"users":["aicountlyin_uuid_647_usr"],"database":"aicountlyin_uuid_647","disk_usage":65536},{"database":"aicountlyin_uuid_648","disk_usage":65536,"users":["aicountlyin_uuid_648_usr"]},{"database":"aicountlyin_uuid_649","disk_usage":65536,"users":["aicountlyin_uuid_649_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_650","users":["aicountlyin_uuid_650_usr"]},{"users":["aicountlyin_uuid_651_usr"],"database":"aicountlyin_uuid_651","disk_usage":65536},{"users":["aicountlyin_uuid_652_usr"],"database":"aicountlyin_uuid_652","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_653","users":["aicountlyin_uuid_653_usr"]},{"users":["aicountlyin_uuid_654_usr"],"database":"aicountlyin_uuid_654","disk_usage":65536},{"users":["aicountlyin_uuid_655_usr"],"database":"aicountlyin_uuid_655","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_656","users":["aicountlyin_uuid_656_usr"]},{"users":["aicountlyin_uuid_657_usr"],"database":"aicountlyin_uuid_657","disk_usage":65536},{"database":"aicountlyin_uuid_658","disk_usage":65536,"users":["aicountlyin_uuid_658_usr"]},{"users":["aicountlyin_uuid_659_usr"],"database":"aicountlyin_uuid_659","disk_usage":65536},{"users":["aicountlyin_uuid_660_usr"],"database":"aicountlyin_uuid_660","disk_usage":65536},{"users":["aicountlyin_uuid_661_usr"],"database":"aicountlyin_uuid_661","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_662","users":["aicountlyin_uuid_662_usr"]},{"users":["aicountlyin_uuid_663_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_663"},{"users":["aicountlyin_uuid_664_usr"],"database":"aicountlyin_uuid_664","disk_usage":65536},{"users":["aicountlyin_uuid_665_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_665"},{"users":["aicountlyin_uuid_666_usr"],"database":"aicountlyin_uuid_666","disk_usage":65536},{"database":"aicountlyin_uuid_667","disk_usage":65536,"users":["aicountlyin_uuid_667_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_668","users":["aicountlyin_uuid_668_usr"]},{"users":["aicountlyin_uuid_669_usr"],"database":"aicountlyin_uuid_669","disk_usage":65536},{"database":"aicountlyin_uuid_670","disk_usage":65536,"users":["aicountlyin_uuid_670_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_671","users":["aicountlyin_uuid_671_usr"]},{"database":"aicountlyin_uuid_672","disk_usage":65536,"users":["aicountlyin_uuid_672_usr"]},{"database":"aicountlyin_uuid_673","disk_usage":65536,"users":["aicountlyin_uuid_673_usr"]},{"users":["aicountlyin_uuid_674_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_674"},{"disk_usage":65536,"database":"aicountlyin_uuid_675","users":["aicountlyin_uuid_675_usr"]},{"users":["aicountlyin_uuid_676_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_676"},{"users":["aicountlyin_uuid_677_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_677"},{"database":"aicountlyin_uuid_678","disk_usage":65536,"users":["aicountlyin_uuid_678_usr"]},{"users":["aicountlyin_uuid_679_usr"],"database":"aicountlyin_uuid_679","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_680","users":["aicountlyin_uuid_680_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_681","users":["aicountlyin_uuid_681_usr"]},{"users":["aicountlyin_uuid_682_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_682"},{"users":["aicountlyin_uuid_683_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_683"},{"users":["aicountlyin_uuid_684_usr"],"database":"aicountlyin_uuid_684","disk_usage":65536},{"database":"aicountlyin_uuid_685","disk_usage":65536,"users":["aicountlyin_uuid_685_usr"]},{"users":["aicountlyin_uuid_686_usr"],"database":"aicountlyin_uuid_686","disk_usage":65536},{"database":"aicountlyin_uuid_687","disk_usage":65536,"users":["aicountlyin_uuid_687_usr"]},{"users":["aicountlyin_uuid_688_usr"],"database":"aicountlyin_uuid_688","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_689","users":["aicountlyin_uuid_689_usr"]},{"users":["aicountlyin_uuid_690_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_690"},{"disk_usage":65536,"database":"aicountlyin_uuid_691","users":["aicountlyin_uuid_691_usr"]},{"users":["aicountlyin_uuid_692_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_692"},{"disk_usage":65536,"database":"aicountlyin_uuid_693","users":["aicountlyin_uuid_693_usr"]},{"database":"aicountlyin_uuid_694","disk_usage":65536,"users":["aicountlyin_uuid_694_usr"]},{"users":["aicountlyin_uuid_695_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_695"},{"users":["aicountlyin_uuid_696_usr"],"database":"aicountlyin_uuid_696","disk_usage":65536},{"users":["aicountlyin_uuid_697_usr"],"database":"aicountlyin_uuid_697","disk_usage":65536},{"users":["aicountlyin_uuid_698_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_698"},{"database":"aicountlyin_uuid_699","disk_usage":65536,"users":["aicountlyin_uuid_699_usr"]},{"disk_usage":15777792,"database":"aicountlyin_uuid_7","users":["aicountlyin_uuid_7_usr"]},{"database":"aicountlyin_uuid_70","disk_usage":131072,"users":["aicountlyin_uuid_70_usr"]},{"database":"aicountlyin_uuid_700","disk_usage":65536,"users":["aicountlyin_uuid_700_usr"]},{"database":"aicountlyin_uuid_701","disk_usage":65536,"users":["aicountlyin_uuid_701_usr"]},{"users":["aicountlyin_uuid_702_usr"],"database":"aicountlyin_uuid_702","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_703","users":["aicountlyin_uuid_703_usr"]},{"users":["aicountlyin_uuid_704_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_704"},{"disk_usage":65536,"database":"aicountlyin_uuid_705","users":["aicountlyin_uuid_705_usr"]},{"users":["aicountlyin_uuid_706_usr"],"database":"aicountlyin_uuid_706","disk_usage":65536},{"users":["aicountlyin_uuid_707_usr"],"database":"aicountlyin_uuid_707","disk_usage":65536},{"users":["aicountlyin_uuid_708_usr"],"database":"aicountlyin_uuid_708","disk_usage":65536},{"users":["aicountlyin_uuid_709_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_709"},{"users":["aicountlyin_uuid_71_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_71"},{"users":["aicountlyin_uuid_710_usr"],"database":"aicountlyin_uuid_710","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_711","users":["aicountlyin_uuid_711_usr"]},{"users":["aicountlyin_uuid_712_usr"],"database":"aicountlyin_uuid_712","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_713","users":["aicountlyin_uuid_713_usr"]},{"users":["aicountlyin_uuid_714_usr"],"database":"aicountlyin_uuid_714","disk_usage":65536},{"database":"aicountlyin_uuid_715","disk_usage":65536,"users":["aicountlyin_uuid_715_usr"]},{"users":["aicountlyin_uuid_716_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_716"},{"users":["aicountlyin_uuid_717_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_717"},{"disk_usage":65536,"database":"aicountlyin_uuid_718","users":["aicountlyin_uuid_718_usr"]},{"users":["aicountlyin_uuid_719_usr"],"database":"aicountlyin_uuid_719","disk_usage":65536},{"users":["aicountlyin_uuid_72_usr"],"database":"aicountlyin_uuid_72","disk_usage":131072},{"users":["aicountlyin_uuid_720_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_720"},{"users":["aicountlyin_uuid_721_usr"],"database":"aicountlyin_uuid_721","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_722","users":["aicountlyin_uuid_722_usr"]},{"users":["aicountlyin_uuid_723_usr"],"database":"aicountlyin_uuid_723","disk_usage":65536},{"users":["aicountlyin_uuid_724_usr"],"database":"aicountlyin_uuid_724","disk_usage":65536},{"users":["aicountlyin_uuid_725_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_725"},{"users":["aicountlyin_uuid_726_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_726"},{"disk_usage":65536,"database":"aicountlyin_uuid_727","users":["aicountlyin_uuid_727_usr"]},{"database":"aicountlyin_uuid_728","disk_usage":65536,"users":["aicountlyin_uuid_728_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_729","users":["aicountlyin_uuid_729_usr"]},{"disk_usage":131072,"database":"aicountlyin_uuid_73","users":["aicountlyin_uuid_73_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_730","users":["aicountlyin_uuid_730_usr"]},{"database":"aicountlyin_uuid_731","disk_usage":65536,"users":["aicountlyin_uuid_731_usr"]},{"users":["aicountlyin_uuid_732_usr"],"database":"aicountlyin_uuid_732","disk_usage":65536},{"users":["aicountlyin_uuid_733_usr"],"database":"aicountlyin_uuid_733","disk_usage":65536},{"users":["aicountlyin_uuid_734_usr"],"database":"aicountlyin_uuid_734","disk_usage":65536},{"users":["aicountlyin_uuid_735_usr"],"database":"aicountlyin_uuid_735","disk_usage":65536},{"users":["aicountlyin_uuid_736_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_736"},{"database":"aicountlyin_uuid_737","disk_usage":65536,"users":["aicountlyin_uuid_737_usr"]},{"database":"aicountlyin_uuid_738","disk_usage":65536,"users":["aicountlyin_uuid_738_usr"]},{"users":["aicountlyin_uuid_739_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_739"},{"disk_usage":163840,"database":"aicountlyin_uuid_74","users":["aicountlyin_uuid_74_usr"]},{"database":"aicountlyin_uuid_740","disk_usage":65536,"users":["aicountlyin_uuid_740_usr"]},{"database":"aicountlyin_uuid_741","disk_usage":65536,"users":["aicountlyin_uuid_741_usr"]},{"users":["aicountlyin_uuid_742_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_742"},{"users":["aicountlyin_uuid_743_usr"],"database":"aicountlyin_uuid_743","disk_usage":65536},{"users":["aicountlyin_uuid_744_usr"],"database":"aicountlyin_uuid_744","disk_usage":65536},{"users":["aicountlyin_uuid_745_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_745"},{"users":["aicountlyin_uuid_746_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_746"},{"disk_usage":65536,"database":"aicountlyin_uuid_747","users":["aicountlyin_uuid_747_usr"]},{"users":["aicountlyin_uuid_748_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_748"},{"disk_usage":65536,"database":"aicountlyin_uuid_749","users":["aicountlyin_uuid_749_usr"]},{"users":["aicountlyin_uuid_75_usr"],"database":"aicountlyin_uuid_75","disk_usage":131072},{"database":"aicountlyin_uuid_750","disk_usage":65536,"users":["aicountlyin_uuid_750_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_751","users":["aicountlyin_uuid_751_usr"]},{"database":"aicountlyin_uuid_752","disk_usage":65536,"users":["aicountlyin_uuid_752_usr"]},{"database":"aicountlyin_uuid_753","disk_usage":65536,"users":["aicountlyin_uuid_753_usr"]},{"database":"aicountlyin_uuid_754","disk_usage":65536,"users":["aicountlyin_uuid_754_usr"]},{"users":["aicountlyin_uuid_755_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_755"},{"disk_usage":65536,"database":"aicountlyin_uuid_756","users":["aicountlyin_uuid_756_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_757","users":["aicountlyin_uuid_757_usr"]},{"users":["aicountlyin_uuid_758_usr"],"database":"aicountlyin_uuid_758","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_759","users":["aicountlyin_uuid_759_usr"]},{"database":"aicountlyin_uuid_76","disk_usage":131072,"users":["aicountlyin_uuid_76_usr"]},{"database":"aicountlyin_uuid_760","disk_usage":65536,"users":["aicountlyin_uuid_760_usr"]},{"users":["aicountlyin_uuid_761_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_761"},{"disk_usage":65536,"database":"aicountlyin_uuid_762","users":["aicountlyin_uuid_762_usr"]},{"users":["aicountlyin_uuid_763_usr"],"database":"aicountlyin_uuid_763","disk_usage":65536},{"database":"aicountlyin_uuid_764","disk_usage":65536,"users":["aicountlyin_uuid_764_usr"]},{"users":["aicountlyin_uuid_765_usr"],"database":"aicountlyin_uuid_765","disk_usage":65536},{"database":"aicountlyin_uuid_766","disk_usage":65536,"users":["aicountlyin_uuid_766_usr"]},{"users":["aicountlyin_uuid_767_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_767"},{"users":["aicountlyin_uuid_768_usr"],"database":"aicountlyin_uuid_768","disk_usage":65536},{"users":["aicountlyin_uuid_769_usr"],"database":"aicountlyin_uuid_769","disk_usage":65536},{"users":["aicountlyin_uuid_77_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_77"},{"database":"aicountlyin_uuid_770","disk_usage":65536,"users":["aicountlyin_uuid_770_usr"]},{"users":["aicountlyin_uuid_771_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_771"},{"database":"aicountlyin_uuid_772","disk_usage":65536,"users":["aicountlyin_uuid_772_usr"]},{"users":["aicountlyin_uuid_773_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_773"},{"database":"aicountlyin_uuid_774","disk_usage":65536,"users":["aicountlyin_uuid_774_usr"]},{"users":["aicountlyin_uuid_775_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_775"},{"users":["aicountlyin_uuid_776_usr"],"database":"aicountlyin_uuid_776","disk_usage":65536},{"database":"aicountlyin_uuid_777","disk_usage":65536,"users":["aicountlyin_uuid_777_usr"]},{"users":["aicountlyin_uuid_778_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_778"},{"users":["aicountlyin_uuid_779_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_779"},{"database":"aicountlyin_uuid_78","disk_usage":131072,"users":["aicountlyin_uuid_78_usr"]},{"users":["aicountlyin_uuid_780_usr"],"database":"aicountlyin_uuid_780","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_781","users":["aicountlyin_uuid_781_usr"]},{"users":["aicountlyin_uuid_782_usr"],"database":"aicountlyin_uuid_782","disk_usage":65536},{"users":["aicountlyin_uuid_783_usr"],"database":"aicountlyin_uuid_783","disk_usage":65536},{"users":["aicountlyin_uuid_784_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_784"},{"database":"aicountlyin_uuid_785","disk_usage":65536,"users":["aicountlyin_uuid_785_usr"]},{"users":["aicountlyin_uuid_786_usr"],"database":"aicountlyin_uuid_786","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_787","users":["aicountlyin_uuid_787_usr"]},{"database":"aicountlyin_uuid_788","disk_usage":65536,"users":["aicountlyin_uuid_788_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_789","users":["aicountlyin_uuid_789_usr"]},{"users":["aicountlyin_uuid_79_usr"],"disk_usage":131072,"database":"aicountlyin_uuid_79"},{"database":"aicountlyin_uuid_790","disk_usage":65536,"users":["aicountlyin_uuid_790_usr"]},{"users":["aicountlyin_uuid_791_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_791"},{"disk_usage":65536,"database":"aicountlyin_uuid_792","users":["aicountlyin_uuid_792_usr"]},{"users":["aicountlyin_uuid_793_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_793"},{"users":["aicountlyin_uuid_794_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_794"},{"database":"aicountlyin_uuid_795","disk_usage":65536,"users":["aicountlyin_uuid_795_usr"]},{"database":"aicountlyin_uuid_796","disk_usage":65536,"users":["aicountlyin_uuid_796_usr"]},{"users":["aicountlyin_uuid_797_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_797"},{"users":["aicountlyin_uuid_798_usr"],"database":"aicountlyin_uuid_798","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_799","users":["aicountlyin_uuid_799_usr"]},{"users":["aicountlyin_uuid_8_usr"],"disk_usage":163840,"database":"aicountlyin_uuid_8"},{"database":"aicountlyin_uuid_80","disk_usage":131072,"users":["aicountlyin_uuid_80_usr"]},{"users":["aicountlyin_uuid_800_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_800"},{"database":"aicountlyin_uuid_801","disk_usage":65536,"users":["aicountlyin_uuid_801_usr"]},{"users":["aicountlyin_uuid_802_usr"],"database":"aicountlyin_uuid_802","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_803","users":["aicountlyin_uuid_803_usr"]},{"users":["aicountlyin_uuid_804_usr"],"database":"aicountlyin_uuid_804","disk_usage":65536},{"users":["aicountlyin_uuid_805_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_805"},{"database":"aicountlyin_uuid_806","disk_usage":65536,"users":["aicountlyin_uuid_806_usr"]},{"users":["aicountlyin_uuid_807_usr"],"database":"aicountlyin_uuid_807","disk_usage":65536},{"users":["aicountlyin_uuid_808_usr"],"database":"aicountlyin_uuid_808","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_809","users":["aicountlyin_uuid_809_usr"]},{"disk_usage":131072,"database":"aicountlyin_uuid_81","users":["aicountlyin_uuid_81_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_810","users":["aicountlyin_uuid_810_usr"]},{"users":["aicountlyin_uuid_811_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_811"},{"users":["aicountlyin_uuid_812_usr"],"database":"aicountlyin_uuid_812","disk_usage":65536},{"users":["aicountlyin_uuid_813_usr"],"database":"aicountlyin_uuid_813","disk_usage":65536},{"database":"aicountlyin_uuid_814","disk_usage":65536,"users":["aicountlyin_uuid_814_usr"]},{"users":["aicountlyin_uuid_815_usr"],"database":"aicountlyin_uuid_815","disk_usage":65536},{"users":["aicountlyin_uuid_816_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_816"},{"users":["aicountlyin_uuid_817_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_817"},{"disk_usage":65536,"database":"aicountlyin_uuid_818","users":["aicountlyin_uuid_818_usr"]},{"users":["aicountlyin_uuid_819_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_819"},{"disk_usage":131072,"database":"aicountlyin_uuid_82","users":["aicountlyin_uuid_82_usr"]},{"users":["aicountlyin_uuid_820_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_820"},{"users":["aicountlyin_uuid_821_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_821"},{"database":"aicountlyin_uuid_822","disk_usage":65536,"users":["aicountlyin_uuid_822_usr"]},{"users":["aicountlyin_uuid_823_usr"],"database":"aicountlyin_uuid_823","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_824","users":["aicountlyin_uuid_824_usr"]},{"database":"aicountlyin_uuid_825","disk_usage":65536,"users":["aicountlyin_uuid_825_usr"]},{"database":"aicountlyin_uuid_826","disk_usage":65536,"users":["aicountlyin_uuid_826_usr"]},{"database":"aicountlyin_uuid_827","disk_usage":65536,"users":["aicountlyin_uuid_827_usr"]},{"database":"aicountlyin_uuid_828","disk_usage":65536,"users":["aicountlyin_uuid_828_usr"]},{"database":"aicountlyin_uuid_829","disk_usage":65536,"users":["aicountlyin_uuid_829_usr"]},{"disk_usage":131072,"database":"aicountlyin_uuid_83","users":["aicountlyin_uuid_83_usr"]},{"users":["aicountlyin_uuid_830_usr"],"database":"aicountlyin_uuid_830","disk_usage":65536},{"users":["aicountlyin_uuid_831_usr"],"database":"aicountlyin_uuid_831","disk_usage":65536},{"users":["aicountlyin_uuid_832_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_832"},{"disk_usage":65536,"database":"aicountlyin_uuid_833","users":["aicountlyin_uuid_833_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_834","users":["aicountlyin_uuid_834_usr"]},{"users":["aicountlyin_uuid_835_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_835"},{"users":["aicountlyin_uuid_836_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_836"},{"users":["aicountlyin_uuid_837_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_837"},{"users":["aicountlyin_uuid_838_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_838"},{"database":"aicountlyin_uuid_839","disk_usage":65536,"users":["aicountlyin_uuid_839_usr"]},{"database":"aicountlyin_uuid_84","disk_usage":131072,"users":["aicountlyin_uuid_84_usr"]},{"database":"aicountlyin_uuid_840","disk_usage":65536,"users":["aicountlyin_uuid_840_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_841","users":["aicountlyin_uuid_841_usr"]},{"users":["aicountlyin_uuid_842_usr"],"database":"aicountlyin_uuid_842","disk_usage":65536},{"users":["aicountlyin_uuid_843_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_843"},{"disk_usage":65536,"database":"aicountlyin_uuid_844","users":["aicountlyin_uuid_844_usr"]},{"database":"aicountlyin_uuid_845","disk_usage":65536,"users":["aicountlyin_uuid_845_usr"]},{"users":["aicountlyin_uuid_846_usr"],"database":"aicountlyin_uuid_846","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_847","users":["aicountlyin_uuid_847_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_848","users":["aicountlyin_uuid_848_usr"]},{"database":"aicountlyin_uuid_849","disk_usage":65536,"users":["aicountlyin_uuid_849_usr"]},{"database":"aicountlyin_uuid_85","disk_usage":131072,"users":["aicountlyin_uuid_85_usr"]},{"users":["aicountlyin_uuid_850_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_850"},{"users":["aicountlyin_uuid_851_usr"],"database":"aicountlyin_uuid_851","disk_usage":65536},{"users":["aicountlyin_uuid_852_usr"],"database":"aicountlyin_uuid_852","disk_usage":65536},{"users":["aicountlyin_uuid_853_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_853"},{"database":"aicountlyin_uuid_854","disk_usage":65536,"users":["aicountlyin_uuid_854_usr"]},{"users":["aicountlyin_uuid_855_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_855"},{"database":"aicountlyin_uuid_856","disk_usage":65536,"users":["aicountlyin_uuid_856_usr"]},{"users":["aicountlyin_uuid_857_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_857"},{"users":["aicountlyin_uuid_858_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_858"},{"users":["aicountlyin_uuid_859_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_859"},{"database":"aicountlyin_uuid_86","disk_usage":131072,"users":["aicountlyin_uuid_86_usr"]},{"users":["aicountlyin_uuid_860_usr"],"database":"aicountlyin_uuid_860","disk_usage":65536},{"users":["aicountlyin_uuid_861_usr"],"database":"aicountlyin_uuid_861","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_862","users":["aicountlyin_uuid_862_usr"]},{"users":["aicountlyin_uuid_863_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_863"},{"users":["aicountlyin_uuid_864_usr"],"database":"aicountlyin_uuid_864","disk_usage":65536},{"database":"aicountlyin_uuid_865","disk_usage":65536,"users":["aicountlyin_uuid_865_usr"]},{"database":"aicountlyin_uuid_866","disk_usage":65536,"users":["aicountlyin_uuid_866_usr"]},{"database":"aicountlyin_uuid_867","disk_usage":65536,"users":["aicountlyin_uuid_867_usr"]},{"database":"aicountlyin_uuid_868","disk_usage":65536,"users":["aicountlyin_uuid_868_usr"]},{"users":["aicountlyin_uuid_869_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_869"},{"users":["aicountlyin_uuid_87_usr"],"database":"aicountlyin_uuid_87","disk_usage":131072},{"users":["aicountlyin_uuid_870_usr"],"database":"aicountlyin_uuid_870","disk_usage":65536},{"users":["aicountlyin_uuid_871_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_871"},{"database":"aicountlyin_uuid_872","disk_usage":65536,"users":["aicountlyin_uuid_872_usr"]},{"users":["aicountlyin_uuid_873_usr"],"database":"aicountlyin_uuid_873","disk_usage":65536},{"users":["aicountlyin_uuid_874_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_874"},{"users":["aicountlyin_uuid_875_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_875"},{"users":["aicountlyin_uuid_876_usr"],"database":"aicountlyin_uuid_876","disk_usage":65536},{"users":["aicountlyin_uuid_877_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_877"},{"users":["aicountlyin_uuid_878_usr"],"database":"aicountlyin_uuid_878","disk_usage":65536},{"database":"aicountlyin_uuid_879","disk_usage":65536,"users":["aicountlyin_uuid_879_usr"]},{"users":["aicountlyin_uuid_88_usr"],"disk_usage":229376,"database":"aicountlyin_uuid_88"},{"users":["aicountlyin_uuid_880_usr"],"database":"aicountlyin_uuid_880","disk_usage":65536},{"database":"aicountlyin_uuid_881","disk_usage":65536,"users":["aicountlyin_uuid_881_usr"]},{"database":"aicountlyin_uuid_882","disk_usage":65536,"users":["aicountlyin_uuid_882_usr"]},{"database":"aicountlyin_uuid_883","disk_usage":65536,"users":["aicountlyin_uuid_883_usr"]},{"users":["aicountlyin_uuid_886_usr"],"database":"aicountlyin_uuid_886","disk_usage":65536},{"users":["aicountlyin_uuid_887_usr"],"database":"aicountlyin_uuid_887","disk_usage":65536},{"users":["aicountlyin_uuid_888_usr"],"database":"aicountlyin_uuid_888","disk_usage":65536},{"users":["aicountlyin_uuid_889_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_889"},{"disk_usage":131072,"database":"aicountlyin_uuid_89","users":["aicountlyin_uuid_89_usr"]},{"database":"aicountlyin_uuid_890","disk_usage":65536,"users":["aicountlyin_uuid_890_usr"]},{"users":["aicountlyin_uuid_891_usr"],"database":"aicountlyin_uuid_891","disk_usage":65536},{"database":"aicountlyin_uuid_892","disk_usage":65536,"users":["aicountlyin_uuid_892_usr"]},{"users":["aicountlyin_uuid_893_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_893"},{"disk_usage":65536,"database":"aicountlyin_uuid_894","users":["aicountlyin_uuid_894_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_895","users":["aicountlyin_uuid_895_usr"]},{"users":["aicountlyin_uuid_897_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_897"},{"database":"aicountlyin_uuid_898","disk_usage":65536,"users":["aicountlyin_uuid_898_usr"]},{"users":["aicountlyin_uuid_899_usr"],"database":"aicountlyin_uuid_899","disk_usage":65536},{"database":"aicountlyin_uuid_900","disk_usage":65536,"users":["aicountlyin_uuid_900_usr"]},{"database":"aicountlyin_uuid_901","disk_usage":65536,"users":["aicountlyin_uuid_901_usr"]},{"users":["aicountlyin_uuid_902_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_902"},{"disk_usage":65536,"database":"aicountlyin_uuid_903","users":["aicountlyin_uuid_903_usr"]},{"users":["aicountlyin_uuid_904_usr"],"database":"aicountlyin_uuid_904","disk_usage":65536},{"users":["aicountlyin_uuid_905_usr"],"database":"aicountlyin_uuid_905","disk_usage":65536},{"database":"aicountlyin_uuid_906","disk_usage":65536,"users":["aicountlyin_uuid_906_usr"]},{"database":"aicountlyin_uuid_907","disk_usage":65536,"users":["aicountlyin_uuid_907_usr"]},{"users":["aicountlyin_uuid_908_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_908"},{"users":["aicountlyin_uuid_909_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_909"},{"users":["aicountlyin_uuid_920_usr"],"database":"aicountlyin_uuid_920","disk_usage":65536},{"database":"aicountlyin_uuid_921","disk_usage":65536,"users":["aicountlyin_uuid_921_usr"]},{"users":["aicountlyin_uuid_922_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_922"},{"disk_usage":65536,"database":"aicountlyin_uuid_923","users":["aicountlyin_uuid_923_usr"]},{"users":["aicountlyin_uuid_924_usr"],"database":"aicountlyin_uuid_924","disk_usage":65536},{"users":["aicountlyin_uuid_925_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_925"},{"users":["aicountlyin_uuid_926_usr"],"database":"aicountlyin_uuid_926","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_927","users":["aicountlyin_uuid_927_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_928","users":["aicountlyin_uuid_928_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_929","users":["aicountlyin_uuid_929_usr"]},{"users":["aicountlyin_uuid_930_usr"],"database":"aicountlyin_uuid_930","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_931","users":["aicountlyin_uuid_931_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_932","users":["aicountlyin_uuid_932_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_933","users":["aicountlyin_uuid_933_usr"]},{"database":"aicountlyin_uuid_934","disk_usage":65536,"users":["aicountlyin_uuid_934_usr"]},{"users":["aicountlyin_uuid_935_usr"],"database":"aicountlyin_uuid_935","disk_usage":65536},{"database":"aicountlyin_uuid_936","disk_usage":65536,"users":["aicountlyin_uuid_936_usr"]},{"users":["aicountlyin_uuid_937_usr"],"database":"aicountlyin_uuid_937","disk_usage":65536},{"disk_usage":65536,"database":"aicountlyin_uuid_938","users":["aicountlyin_uuid_938_usr"]},{"users":["aicountlyin_uuid_939_usr"],"database":"aicountlyin_uuid_939","disk_usage":65536},{"database":"aicountlyin_uuid_940","disk_usage":65536,"users":["aicountlyin_uuid_940_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_941","users":["aicountlyin_uuid_941_usr"]},{"users":["aicountlyin_uuid_942_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_942"},{"database":"aicountlyin_uuid_943","disk_usage":65536,"users":["aicountlyin_uuid_943_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_944","users":["aicountlyin_uuid_944_usr"]},{"users":["aicountlyin_uuid_945_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_945"},{"users":["aicountlyin_uuid_946_usr"],"database":"aicountlyin_uuid_946","disk_usage":65536},{"users":["aicountlyin_uuid_947_usr"],"database":"aicountlyin_uuid_947","disk_usage":65536},{"users":["aicountlyin_uuid_948_usr"],"database":"aicountlyin_uuid_948","disk_usage":65536},{"database":"aicountlyin_uuid_949","disk_usage":65536,"users":["aicountlyin_uuid_949_usr"]},{"disk_usage":65536,"database":"aicountlyin_uuid_950","users":["aicountlyin_uuid_950_usr"]},{"users":["aicountlyin_uuid_951_usr"],"disk_usage":65536,"database":"aicountlyin_uuid_951"},{"users":["aicountlyin_uuid_952_usr"],"database":"aicountlyin_uuid_952","disk_usage":65536},{"database":"aicountlyin_uuid_987","disk_usage":65536,"users":["aicountlyin_uuid_987_usr"]},{"database":"aicountlyin_uuid_erpaic","disk_usage":65536,"users":["aicountlyin_uuid_erpaic"]}],"warnings":null,"messages":null,"status":1,"metadata":{"transformed":1}}';
	    $response    = json_decode($response);
	    $dataArray      = $response->data;
		
// Set the page size (items per page)
$pageSize = 100;

// Get the current page number from the query parameter (default is 1)
$pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get paginated data for the current page
$paginatedData = $this->paginateArray($dataArray, $pageSize, $pageNumber);

// Get pagination info (total pages, total items)
$paginationInfo = $this->getPaginationInfo($dataArray, $pageSize);

// Display paginated data
echo "<h2>Paginated Data (Page $pageNumber)</h2>";
echo "<ul>";
//echo "<pre>";print_r($paginatedData);exit;
if($paginatedData){
	       	        
	        foreach($paginatedData as $key => $row){
	             $row_val =(array)$row;
				 if($this->startsWith($row_val['database'], "aicountlyin_uuid_")){
					 $erp_databases = $row_val['database'];
					 $username      = $row_val['users'][0];	
					
							
				     if($erp_databases!='aicountlyin_uuid_erpaic'){
						    $uuid          = trim(str_replace('aicountlyin_uuid_','',$erp_databases));
							$uuid_db       = $this->externaldb->connect_uuid_db($uuid);
							echo "DROP DATABASE `".$row_val['database']."`";
							echo '<br>';
							//$query_result  = $uuid_db->query("DROP DATABASE `".$row_val['database']."`");
							//$query_result  = $uuid_db->query("SHOW TABLES FROM `".$row_val['database']."`");
							//$tables_info   = $query_result->getResultArray(); 							
							/* if($tables_info){
							foreach($tables_info as $tblrow){
							$table_name       = array_values($tblrow)[0];
							
							if($table_name=='cofymstmap' || $table_name=='credential' || $table_name=='baseidgenrt'|| $table_name=='credkeysnn'){}
								else{
								 $expld_tablname   = explode("_",$table_name);
								 $company_id       = $expld_tablname[0];
								 $short_table_name = $expld_tablname[1];	
								  $this->create_default_tables($uuid_unv_db,$company_id);
								 $table_result     = $uuid_db->query("SELECT * FROM `".$table_name."`")->getResultArray();
								 if($table_result){	
                                    $table_results = json_encode($table_result); 								 
									$this->default_tables_insert($uuid_unv_db,$company_id,$table_name,$short_table_name,$table_results,$uuid);
								 }
								
								}
								
								
								
																
								
								}    
							} */
				        }
			        }
		        }
		
	        }
echo "</ul>";

// Display pagination links
echo "<div>";
echo "Total Items: " . $paginationInfo['totalItems'] . " | ";
echo "Total Pages: " . $paginationInfo['totalPages'] . "<br>";

if ($pageNumber > 1) {
    echo "<a href='?page=" . ($pageNumber - 1) . "'>Previous</a> | ";
}
if ($pageNumber < $paginationInfo['totalPages']) {
    echo "<a href='?page=" . ($pageNumber + 1) . "'>Next</a>";
}
echo "</div>";


		
		
	}
	
	
    function all_companies()
    {
    	$aicountly_db = $this->externaldb->aicountly_db();

	    $my_companies= array();
        $builder = $aicountly_db->table("aicountly_compidgenr_univdb"); 
        $builder->select('aicountly_compidgenr_univdb.*, aicountly_cmpmastern_univdb.comp_code, comp_fy_id');
        $builder->join('aicountly_cmpmastern_univdb', 'aicountly_cmpmastern_univdb.comp_id = aicountly_compidgenr_univdb.comp_id');
        // $builder->where('uuid',$uuid);
        // $builder->where('uuid_aicountly',$uuid_aicountly);
		$result = $builder->get()->getResultArray();
		
		if($result){  //15 comp has 2 fy's
		    foreach($result as $row){

          $uuid = $row['uuid'];
          // echo "<br>".$uuid;

		    	$result2 = $aicountly_db->table("aicountly_cmpfymastr_univdb")
		    			->select('comp_fy_id')
		    			->where('comp_id', $row['comp_id'])
		    			->get()->getResultArray();

            //echo "-------------------------------<br>";

		    	foreach($result2 as $row2){
					
					//echo '<a href="https://sandbox.aicountly.in/admin/script/txntables_addfields/'.$row['comp_id'].'/'.$row2['comp_fy_id'].'/'.$row['comp_code'].'">'.$row['comp_id'].'--'.$row2['comp_fy_id'].'</a>';
//echo'<br>';
          // if($row['comp_id'] == 1){
          //   // main fy
            // if($row['comp_fy_id'] == $row2['comp_fy_id'] ){

              
              
          //     // $this->reset_mstbaseid_table($row['comp_id'], $row['comp_code'],$row2['comp_fy_id']);
          //     // $this->main_fy($uuid,$row['comp_id'],$row['comp_fy_id']);
              
            

            // }

          //   // other fy
          //   if($row['comp_fy_id'] != $row2['comp_fy_id'] ){
              
          //     // $this->reset_mstbaseid_table($row['comp_id'], $row['comp_code'],$row2['comp_fy_id']);
          //     // $this->copy_mstbaseid_table($row['comp_id'], $row['comp_code'], $row['comp_fy_id'], $row2['comp_fy_id'],$uuid);

          //     // $this->other_fy_log($row['comp_id'], $row['comp_code'], $row2['comp_fy_id'],$uuid);

          //     // $this->other_fy($row['comp_id'], $row['comp_code'], $row2['comp_fy_id'],$uuid);
          //   }
          // }
             

		    		 if($row['comp_id'] == 3){
				
               // $this->new_account_tax($row['comp_id'], $row['comp_code'],$row2['comp_fy_id'],$uuid);
               //$this->update_accountmaster($row['comp_id'], $row['comp_code'],$row2['comp_fy_id'],$uuid);
               //$this->create_table_vchaddinfo($row['comp_id'], $row['comp_code'],$row2['comp_fy_id'],$uuid);
               
			 $this->modify_voucher_series_am($row['comp_id'], $row['comp_code'],$row2['comp_fy_id'],$uuid);
              //$this->create_bdstaxmstn_table($row['comp_id'], $row['comp_code'],$row2['comp_fy_id'],$uuid);
			  
			 // $this->modify_gstr_one($row['comp_id'], $row['comp_code'],$row2['comp_fy_id'],$uuid);
              
		    		 }
		    		
		    	}
		       	
		    }  
		}
    }
	
	
	function modify_gstr_one($comp_id,$comp_code,$comp_fy_id,$uuid){
		
	$external_db         = $this->externaldb->single_company_db($comp_code);	
	$acctgstsum_table    = $comp_id.'_acctgstsum_'.$comp_fy_id;
    $gstrinwsup_tbl      = $comp_id.'_gstrinwsup_'.$comp_fy_id;	
	$gstroutsup_tbl      = $comp_id.'_gstroutsup_'.$comp_fy_id;
	$acctgstmst_tbl      = $comp_id.'_acctgstmst_'.$comp_fy_id;	
	$comp_txn_master_tbl = $comp_id.'_comptxnmst_'.$comp_fy_id;
	$itemtaxmst_tbl      = $comp_id.'_itemtaxmst_'.$comp_fy_id;
	$cmpgstcatn_tbl      = $comp_id.'_cmpgstcatn_'.$comp_fy_id;	 
	$vhtxnconso_tbl      = $comp_id.'_vhtxnconso_'.$comp_fy_id;
	$ewbmstreqn_tbl      = $comp_id.'_ewbmstreqn_'.$comp_fy_id;
	
	try{
		  $data = $external_db->table($comp_txn_master_tbl)->get()->getResultArray();
		  $master_accounts=array();
		  foreach($data as $row){			
			 $master_id       = $row['master_id'];			 
			 $master_id_type  = $row['master_id_type'];
			 $voucher_txn_id  = $row['voucher_txn_id'];
			 $voucher_type_row  = $external_db->table($vhtxnconso_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray(); 
			 
			 if($voucher_type_row){
				 $voucher_date      = $voucher_type_row['voucher_date'];
			
			 if($master_id_type=='itm'){
				 $item_id   = $row['master_id'];
				 $voucher_txn_id  = $row['voucher_txn_id'];
				 $txn_id    = $row['txn_id'];		
				 $tax_data  = $external_db->table($itemtaxmst_tbl)
	   					->where('comp_id',$comp_id)
						->where('item_id',$item_id)
						->orderBy('cmp_tax_cat_id','DESC')						
	   					->get()->getRowArray();
										
				   if($tax_data){			
				     $tax_rates =  $external_db->table($cmpgstcatn_tbl)->where('cmp_tax_cat_id',$tax_data['cmp_tax_cat_id'])->get()->getRowArray();
	                 if($tax_rates){
					   $cmp_tax_short_code = $tax_rates['cmp_tax_short_code'];				     
					   $pddata  = array("cmp_tax_short_code"=>$cmp_tax_short_code,'acc_txn_date'=>$voucher_date);
					     echo  "update `".$acctgstsum_table."` set `cmp_tax_short_code`='".$cmp_tax_short_code."',`acc_txn_date`='".$voucher_date."' where `txn_id`='".$txn_id."'  ";
					     echo '<br>';
					   $external_db->table($acctgstsum_table)->where('txn_id',$txn_id)->update($pddata);
					 }
				     
				   }
			    }
		  }
		       }
		  
		    $ddata = $external_db->table($acctgstsum_table)->get()->getResultArray();
		    foreach($ddata as $rr){
			  $voucher_txn_id  = $rr['vch_txn_id'];				
			  $party_info = $external_db->table($comp_txn_master_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('master_id_type','acc')->orderBy('txn_id')->limit(1)->get()->getRowArray();			
			  $ewbmstreqn_row    = $external_db->table($ewbmstreqn_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray(); 
			  $voucher_type_row  = $external_db->table($vhtxnconso_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray(); 
			  if($voucher_type_row){				
			  $voucher_type      = $voucher_type_row['voucher_type_id'];
			  $voucher_date      = $voucher_type_row['voucher_date'];
			  
			  if($ewbmstreqn_row)
			  $supply_type       = $ewbmstreqn_row['ewb_supply_type'];					
		      else
			  $supply_type    ="1";	  
			  $party_gstin_data  = $external_db->table($acctgstmst_tbl)->where('acc_id',$party_info['master_id'])->get()->getRowArray(); 
			  echo $party_info['master_id'].'<pre>';
			  print_r($party_gstin_data);
			  
			  if($party_gstin_data){
			   $party_gstin      = trim($party_gstin_data['acc_gstin']);
			   }
			  else 
			   $party_gstin      = "";
		    echo '--'.$party_gstin;
			echo '<br>';
			$gstroutsup_info     = $external_db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
			
		   if($voucher_type=='18' || $voucher_type=='3'){ // sale & debit
			if(isset($gstroutsup_info) && $party_gstin!='' && $supply_type=='1' && $gstroutsup_info['outsup_rev_chg']=="0"){
				 $invoice_type = 'B2B';				
			   }
			else if(isset($gstroutsup_info) && $party_gstin!='' && $gstroutsup_info['outsup_rev_chg']=="1" && $supply_type=='1'){  
			  	 $invoice_type = 'B2BRCM';							  
			  }			
			 else  if(isset($gstroutsup_info) && $party_gstin=='' && $supply_type=='1' && $gstroutsup_info['outsup_rev_chg']=="0"){
				 $invoice_type = 'B2C';				 
			  }
			 else if($supply_type=='2'){
				$invoice_type = 'EXPWP'; 
			 }			 
			 elseif($supply_type=='3'){
				$invoice_type = 'EXPWOP'; 
			 }
			 elseif($supply_type=='13'){
				$invoice_type = 'DE'; 
			 }	
			 elseif($supply_type=='4'){
				$invoice_type = 'SEZWP'; 
			 }
			 elseif($supply_type=='5'){
				$invoice_type = 'SEZWOP'; 
			 }else
				$invoice_type = 'CBW';  
            
			 echo  "update `".$gstroutsup_tbl."` set `outsup_inv_type`='".$invoice_type."' where `voucher_txn_id`='".$voucher_txn_id."' ";
			 echo '<br>';
			if($voucher_type=='3') 
			 $psddata=array("outsup_inv_type"=>$invoice_type,'outsup_dr_note'=>'1');
		    else
			 $psddata=array("outsup_inv_type"=>$invoice_type,'outsup_dr_note'=>'0');	
		 
			$external_db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($psddata);
					 	
			}
			else if($voucher_type=='11' || $voucher_type=='2'){ // purchase & credit
			$gstrinwsup_info     = $external_db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
			if(isset($gstrinwsup_info) && $party_gstin!='' && $supply_type=='1' && $gstrinwsup_info['inwsup_rev_chg']=="0"){
				 $invoice_type = 'B2B';				
			   }
			else if(isset($gstrinwsup_info) && $supply_type=='1' && $party_gstin!=''&& $gstrinwsup_info['inwsup_rev_chg']=="1"){  
			  	 $invoice_type = 'B2BRCM';							  
			  }					
			 else if(isset($gstrinwsup_info) && $party_gstin=='' && $supply_type=='1' && $gstrinwsup_info['inwsup_rev_chg']=="0"){
				 $invoice_type = 'B2C';				 
			  }
			 else if($supply_type=='2'){
				$invoice_type = 'EXPWP'; 
			 }			 
			 elseif($supply_type=='3'){
				$invoice_type = 'EXPWOP'; 
			 }
			 elseif($supply_type=='13'){
				$invoice_type = 'DE'; 
			 }	
			 elseif($supply_type=='4'){
				$invoice_type = 'SEZWP'; 
			 }
			 elseif($supply_type=='5'){
				$invoice_type = 'SEZWOP'; 
			 }else
				$invoice_type = 'CBW';  
            
			 echo  "update `".$gstrinwsup_tbl."` set `inwsup_inv_type`='".$invoice_type."' where `voucher_txn_id`='".$voucher_txn_id."' ";
			 echo '<br>';
			 if($voucher_type=='2')
			   $pwddata = array("inwsup_inv_type"=>$invoice_type,'inwsup_cr_note'=>'1');
		     else
			  $pwddata = array("inwsup_inv_type"=>$invoice_type,'inwsup_cr_note'=>'0');	 
			 $external_db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($pwddata);
			}
			 }
		  
	    }
		  
			  
	 }
	  catch (\Exception $e) {
        echo $e->getMessage() . '<br />';
      }
      echo "<h1>company-".$comp_id."  Executed"."</h1><br>";
	 
	}
	
	
	
	function txntables_fields($comp_id,$comp_fy_id,$comp_code){
		$external_db    = $this->externaldb->single_company_db($comp_code);
	    
		$data           =  $external_db->query("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%_itemtxnnnn_%_".$comp_fy_id."'")->getResultArray();  
        if($data){
		  foreach($data as  $key => $row){
			   $table_name = $row['TABLE_NAME'];
			   echo "ALTER TABLE `".$table_name."` CHANGE `item_txn_fcy` `item_txn_fcy` DECIMAL(18,8) NULL DEFAULT '0';";
			   echo '<br>';
			   try{
			    $external_db->query("ALTER TABLE `".$table_name."` CHANGE `item_txn_fcy` `item_txn_fcy` DECIMAL(18,8) NULL DEFAULT '0';");
			   }
			   catch (\Exception $e) {
					echo $e->getMessage();
				}
		    }	
		}
	   /*
	   $data           =  $external_db->query("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%_sundrytxnn_%_".$comp_fy_id."'")->getResultArray();  
        if($data){
		  foreach($data as  $key => $row){
			   $table_name = $row['TABLE_NAME'];
			   echo "ALTER TABLE `".$table_name."` ADD `sundry_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `bo_id`;";
			   echo '<br>';
			   try{
			    $external_db->query("ALTER TABLE `".$table_name."` ADD `sundry_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `bo_id`;");
			   }
			   catch (\Exception $e) {
					echo $e->getMessage();
				}
		    }	
		}
		
	 $data           =  $external_db->query("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%_accnttxnnn_%_".$comp_fy_id."'")->getResultArray();  
        if($data){
		  foreach($data as  $key => $row){
			   $table_name = $row['TABLE_NAME'];
			   echo "ALTER TABLE `".$table_name."` ADD `acc_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `posted_on`;";
			   echo '<br>';
			   try{
			    $external_db->query("ALTER TABLE `".$table_name."` ADD `acc_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `posted_on`;");
			   }
			   catch (\Exception $e) {
					echo $e->getMessage();
				}
		    }	
		}

       $data           =  $external_db->query("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%_accttxnoth_".$comp_fy_id."'")->getResultArray();  
        if($data){
		  foreach($data as  $key => $row){
			   $table_name = $row['TABLE_NAME'];
			   echo "ALTER TABLE `".$table_name."` ADD `acc_oth_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `acc_oth_txn_tag`;";
			   echo '<br>';
			   try{
			    $external_db->query("ALTER TABLE `".$table_name."` ADD `acc_oth_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `acc_oth_txn_tag`;");
			   }
			   catch (\Exception $e) {
					echo $e->getMessage();
				}
		    }	
		}
		
	$data           =  $external_db->query("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%_itemtxnoth_".$comp_fy_id."'")->getResultArray();  
        if($data){
		  foreach($data as  $key => $row){
			   $table_name = $row['TABLE_NAME'];
			   echo "ALTER TABLE `".$table_name."` ADD `item_oth_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `batch_id`;";
			   echo '<br>';
			   try{
			    $external_db->query("ALTER TABLE `".$table_name."` ADD `item_oth_txn_fcy` DECIMAL(18,8) DEFAULT 0 AFTER `batch_id`;");
			   }
			   catch (\Exception $e) {
					echo $e->getMessage();
				}
		    }	
		} */	
		
		
	}
	
	function modify_voucher_series_am($comp_id,$comp_code,$comp_fy_id,$uuid){	
	die();
		  $blockAfterItemId = [
			'unit_id'        => 'BIGINT NOT NULL DEFAULT 0',
			'mat_cent_id'    => 'BIGINT NOT NULL DEFAULT 0',
			'item_txn_id'    => 'BIGINT NOT NULL DEFAULT 0',
			'voucher_txn_id' => 'BIGINT NOT NULL DEFAULT 0',
			'voucher_date'   => 'DATE NULL'
		  ];

		$blockAfterItemValue = [
			'profit'        => 'DECIMAL(18,8) NOT NULL DEFAULT 0.00000000',
			'profit_string' => 'VARCHAR(255) NOT NULL DEFAULT \'\''
		];
	
	
	  $default_db_name ='aicountlyin_'.$comp_code; 
	  $external_db = $this->externaldb->single_company_db($comp_code);	
	  // $table       = $comp_id.'_itemtaxmst_'.$comp_fy_id;	
      // $table2       = $comp_id.'_cmpgstcatn_'.$comp_fy_id;

	  // $table1       = $comp_id.'_itemrepall_'.$comp_fy_id;	
      // $table2       = $comp_id.'_cmpgstcatn_'.$comp_fy_id;	 	   
        try{		
				$response = $external_db->query("SHOW TABLES LIKE '%_itemtxnvaln_%_%'");
		$tables = array_column($response->getResultArray(), array_key_first($response->getResultArray()[0] ?? []));
	    $fixed = []; 
		foreach ($tables as $table) {
			
			
				
			echo  $updatequery ="ALTER TABLE `".$table."` DROP `itemtxnbal_id`, DROP `avg_cost` ";
				echo '<br>';
			$external_db->query($updatequery);
			//echo '<br>';	
			
			
		}
				
				
		/* $response = $external_db->query("SHOW TABLES LIKE '3_itemtxnvaln_%_%'");
		$tables = array_column($response->getResultArray(), array_key_first($response->getResultArray()[0] ?? []));
	    $fixed = []; 
		foreach ($tables as $table) {
        // Step 2: Count columns
        $colCount = $external_db->query("
            SELECT COUNT(*) AS total 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = '{$default_db_name}' 
              AND TABLE_NAME = '{$table}'
        ")->getRow()->total;

        if ((int)$colCount === 6) {
            echo "⚠️  Fixing table: {$table} <br />";

            // Step 3: Get existing column names
            $columnsResult = $external_db->query("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = '{$default_db_name}' 
                  AND TABLE_NAME = '{$table}'
            ")->getResultArray();

            $existingCols = array_column($columnsResult, 'COLUMN_NAME');

			 
			$prev = 'item_id';
			foreach ($blockAfterItemId as $col => $def) {
				if (! in_array($col, $existingCols, true)) {
					echo $qee= "ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$def} AFTER `{$prev}`";
					$external_db->query($qee);
					echo "   ➕ {$col} (after {$prev})<br />";
				}
				$prev = $col;   
			}

			
			$prev = 'item_value';
			foreach ($blockAfterItemValue as $col => $def) {
				if (! in_array($col, $existingCols, true)) {
				echo	$qsss="ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$def} AFTER `{$prev}`";
					$external_db->query($qsss);
					echo "   ➕ {$col} (after {$prev})\n";
				}
				$prev = $col;
			}
		

            $fixed[] = $table;
        }
    }  */
		 
		 /* echo 'Company-'.$comp_id.'  FY id -'.$comp_fy_id;
	echo '<br>';
	$update_query=0;
	 $response = $external_db->table($table)->select('cmp_tax_cat_id')->where('tax_cat_id','19')->get()->getResultArray();
	
	echo $external_db->getlastquery();
	echo '<br>';
	if($response){		
	 foreach($response as $row){
		$cmp_tax_cat_id = $row['cmp_tax_cat_id'];
		 $response2 = $external_db->table($table2)->where('cmp_tax_cat_id',$cmp_tax_cat_id)->where('tax_cat_id','19')->get()->getRowArray();
	     if($response2){
			 $cmp_tax_cat_igst = $response2['cmp_tax_cat_igst'];
			 if($cmp_tax_cat_igst==0){
				 $update_query++;
			 }
		 }
	}	 
	 if($update_query>0){
		 $table3= $comp_id.'_cmptaxcatm_'.$comp_fy_id;	 	
		 $response3 = $external_db->table($table3)->where('cmp_tax_cat_name','Undefined')->get()->getRowArray();
	     if($response3){
		 $cmp_tax_cat_id = $response3['cmp_tax_cat_id'];
		 
	    echo  $updatequery ="UPDATE `".$table."` SET `tax_cat_id`='17',`cmp_tax_cat_id`='".$cmp_tax_cat_id."' WHERE  `tax_cat_id`='19' ";
			$external_db->query($updatequery);
			echo '<br>';	
		   }
	   }
	}  */
	
		
	/* echo 'Company-'.$comp_id.'  FY id -'.$comp_fy_id;
	echo '<br>';
	$update_query=0;
	 $response = $external_db->table($table)->select('cmp_tax_cat_id')->where('tax_cat_id','19')->get()->getResultArray();
	
	echo $external_db->getlastquery();
	echo '<br>';
	if($response){		
	 foreach($response as $row){
		$cmp_tax_cat_id = $row['cmp_tax_cat_id'];
		 $response2 = $external_db->table($table2)->where('cmp_tax_cat_id',$cmp_tax_cat_id)->where('tax_cat_id','19')->get()->getRowArray();
	     if($response2){
			 $cmp_tax_cat_igst = $response2['cmp_tax_cat_igst'];
			 if($cmp_tax_cat_igst==0){
				 $update_query++;
			 }
		 }
	}	 
	 if($update_query>0){
		 $table3= $comp_id.'_cmptaxcatm_'.$comp_fy_id;	 	
		 $response3 = $external_db->table($table3)->where('cmp_tax_cat_name','Undefined')->get()->getRowArray();
	     if($response3){
		 $cmp_tax_cat_id = $response3['cmp_tax_cat_id'];
		 
	    echo  $updatequery ="UPDATE `".$table."` SET `tax_cat_id`='17',`cmp_tax_cat_id`='".$cmp_tax_cat_id."' WHERE  `tax_cat_id`='19' ";
			$external_db->query($updatequery);
			echo '<br>';	
		   }
	   }
	} */
	
	
	
	
	
	
	
	
		
			//$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
          // $external_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`usr_pref_id` BIGINT NOT NULL AUTO_INCREMENT , `uuid` BIGINT NOT NULL , `usr_config_id` BIGINT NOT NULL , `usr_config_value` VARCHAR(150) NOT NULL , PRIMARY KEY (`usr_pref_id`), INDEX `prfnindex` (`uuid`, `usr_config_id`)) ENGINE = InnoDB;");
				
		//	$external_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (`usr_pref_id` BIGINT NOT NULL AUTO_INCREMENT , `uuid` BIGINT NOT NULL , `usr_config_id` BIGINT NOT NULL , `usr_config_value` VARCHAR(150) NOT NULL , PRIMARY KEY (`usr_pref_id`), INDEX `prfnindex` (`uuid`, `usr_config_id`)) ENGINE = InnoDB;");
		 // $external_db->query("ALTER TABLE `".$table."` CHANGE `einv_supply_type` `einv_supply_type` VARCHAR(20) NULL DEFAULT NULL;");     	    	 
		 // $external_db->query("ALTER TABLE `".$table."` CHANGE `einv_ecm_gstin` `einv_ecm_gstin` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL;");     	    	 
		 // $external_db->query("ALTER TABLE `".$table."` CHANGE `einv_igst_intra` `einv_igst_intra` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL;");     	    	 
		
		 }
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}		

      /* try{
			  
        $exists = $external_db->table($table)
                      ->where('acc_name','Profit & Loss Appropriation')
                      ->get()->getRowArray();

         if(!$exists){
            
            $external_db->table($table)
                      ->insert($data);
            $acc_id = $external_db->insertID();
          
            $ERPtables = new ERPtables($comp_id,$comp_fy_id);
            $response = $ERPtables->account_txn_tables($acc_id);

            
            $external_db->table($table)
                  ->where('acc_id', $acc_id)
                  ->update(['mst_base_id' => $mst_base_id]);
           } 
         }	
		catch (\Exception $e) {
			echo $e->getMessage();
		} 	 */	
		
		/* try{
		  $external_db->query("ALTER TABLE `".$table1."` CHANGE `container_item_id_unit_id` `cont_item_id_unit_avail_id` VARCHAR(100) NOT NULL;");
     	    	 
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		} 
	 try{
		  $external_db->query("ALTER TABLE `".$table2."` CHANGE `container_item_id_unit_id` `cont_item_id_unit_avail_id` VARCHAR(100) NOT NULL;");
     	    	 
		}
		catch (\Exception $e) {
			$errors[] = $e->getMessage();
		}  */ 			
		 
		 
		//echo 'ALTER TABLE `'.$table2.'` DROP COLUMN comp_vch_end, DROP COLUMN comp_vch_warning_no, DROP COLUMN comp_vch_warning_msg;';
		//echo '<br>';		
		
		//$external_db->query('ALTER TABLE `'.$table3.'` CHANGE `vch_bill_ref_no` `vch_bill_ref_no` BIGINT NOT NULL DEFAULT "0";');	 
	//$external_db->query("ALTER TABLE " . $table2 . " MODIFY COLUMN `comp_vch_prefix` VARCHAR(100) NULL DEFAULT NULL ");
	//$external_db->query('ALTER TABLE `'.$table.'` DROP COLUMN comp_vch_duplicate;');
	/* $response = $external_db->table($table4)->get()->getResultArray();
	if($response){
	foreach($response as $row){
		$comp_vch_series_id = $row['comp_vch_series_id'];
		$inser_d = array("comp_id"=>$comp_id,"comp_vch_blank"=>"0","comp_vch_series_id"=>$comp_vch_series_id);
		$external_db->table($table)->insert($inser_d);
		echo '<pre>';
		print_r($inser_d);
		echo '<hr>';
	}	
		
	} */
		//$external_db->query('ALTER TABLE `'.$table2.'` DROP COLUMN comp_vch_end, DROP COLUMN comp_vch_warning_no, DROP COLUMN comp_vch_warning_msg;');
	  //$external_db->query('ALTER  TABLE `'.$table.'` RENAME COLUMN `comp_vch_series` TO `comp_vch_series_id`;');	 
	
	 
      //echo "company-".$comp_id."  Executed"."<br>";

	}
	
	function modify_billsundry_data($comp_id,$comp_code,$comp_fy_id,$uuid){
		 $external_db    = $this->externaldb->single_company_db($comp_code);
		 $table = $comp_id.'_billsundry_'.$comp_fy_id;
		 $table3 = $comp_id.'_bdstaxmstn_'.$comp_fy_id;
		 $table4 = $comp_id.'_hobomaster_'.$comp_fy_id;
		 
		 $hobomaster = $external_db->table($table4)
                      ->where('bo_ho','1')
                      ->get()->getRowArray();
		 $bo_id    = 	$hobomaster['bo_id'];	
         $master_name   = 'UT Tax (UGST)';
         $sundry_nature = 195; 		 
         $billsundry_data   = [
						'bill_sundry_name'     => $master_name,
						'bill_sundry_alias'    => $master_name,
						'sundry_print_name'    => $master_name,
						'sundry_nature'        => $sundry_nature,
						'sundry_def_value'     => 0,
						'sundry_type'          => 1,
						'sundry_calc_base'     => 0,
						'sundry_calc_fed'      => 0,
						'acc_grp_id'           => 19,
						'acc_grp_parent_id'    => 0,
						'bo_id'                => $bo_id						
					  ];
	  
	   try{
		   $exists = $external_db->table($table)
                      ->where('bill_sundry_name',$master_name)
                      ->get()->getRowArray();
		   /* if($exists){
			  $bill_sundry_id = $exists['bill_sundry_id'];
			  
			  $exists3 = $external_db->table($table3)
                      ->where('bill_sundry_id',$bill_sundry_id)
                      ->get()->getRowArray();
					  
			 if(!$exists3){
				$data3 = array("bill_sundry_id"=>$bill_sundry_id,
						   "cmp_tax_cat_id"=>"0","tax_cat_id"=>0,
						   "bill_supply_type"=>0,"bill_hsn_sac"=>"",
						   "bill_input_output"=>2,"bill_tax_short_code"=>"",
						   "bill_tax_account"=>1,
						   "bill_tax_type"=>$sundry_nature);
						   echo '<pre>';
						   print_r($data3);
	           $external_db->table($table3)->insert($data3);
			  }				 
					  
			} */
			
           if(!$exists){
		   
			$external_db->table($table)
                      ->insert($billsundry_data);
            $billsundry_id = $external_db->insertID();
          
            $ERPtables = new ERPtables($comp_id,$comp_fy_id);
            $response = $ERPtables->bill_sundry_txn_tables($billsundry_id);

            $UUIDtables = new UUIDtables($uuid,$comp_id,$comp_fy_id);
            $mst_base_id = $UUIDtables->get_mst_base_id($billsundry_id,'billsundry');
            
            $external_db->table($table)
                  ->where('bill_sundry_id', $billsundry_id)
                  ->update(['mst_base_id' => $mst_base_id]);
				  
			$data3 = array("bill_sundry_id"=>$billsundry_id,
						   "cmp_tax_cat_id"=>"0","tax_cat_id"=>0,
						   "bill_supply_type"=>0,"bill_hsn_sac"=>"",
						   "bill_input_output"=>2,"bill_tax_short_code"=>"",
						   "bill_tax_account"=>1,
						   "bill_tax_type"=>$sundry_nature);
	       $external_db->table($table3)->insert($data3);
		  
		 } 
	   }
	   catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      echo "company-".$comp_id."  Executed"."<br>";
	   
	 
			
	}
	
    function new_account_tax($comp_id,$comp_code,$comp_fy_id,$uuid)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_acctmaster_'.$comp_fy_id;
      $data = [
        'comp_id'           => $comp_id,
        'acc_name'          => 'GST PAID A/C',
        'acc_name_alias'    => 'GST PAID A/C',
        'acc_name_print'    => 'GST PAID A/C',
        'acc_grp_id'        => 0,
        'acc_grp_parent_id' => 13,
        'acc_short_code'    => 'sagstpd'
      ];

      try{
        $exists = $external_db->table($table)
                      ->where('acc_name','GST PAID A/C')
                      ->get()->getRowArray();

        if(!$exists){
            
            $external_db->table($table)
                      ->insert($data);
            $acc_id = $external_db->insertID();
          
            $ERPtables = new ERPtables($comp_id,$comp_fy_id);
            $response = $ERPtables->account_txn_tables($acc_id);

            $UUIDtables = new UUIDtables($uuid,$comp_id,$comp_fy_id);
            $mst_base_id = $UUIDtables->get_mst_base_id($acc_id,'acctmaster');
            
            $external_db->table($table)
                  ->where('acc_id', $acc_id)
                  ->update(['mst_base_id' => $mst_base_id]);
        }
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      echo "company-".$comp_id."  Executed"."<br>";
    }

    function new_voucher_type($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_cmpvchtype_'.$comp_fy_id;

      try{
        $external_db->query('INSERT INTO ' . $table . ' (voucher_type_id, comp_id, comp_vch_type) value(22,'.$comp_id.',"System Generated")');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      echo "company-".$comp_id."  Executed"."<br>";
    }

    function alter_acctmaster($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_acctmaster_'.$comp_fy_id;
      
      try{
          $external_db->query('ALTER TABLE ' . $table . ' ADD `acc_short_code` VARCHAR(15) NOT NULL DEFAULT "usraccn" COMMENT "user defined account, sagstpd system generated account" AFTER `mst_base_id`');
        }
        catch (\Exception $e) {
         echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query('UPDATE ' . $table . ' set `acc_short_code` = "usraccn"');
        }
        catch (\Exception $e) {
         echo $e->getMessage() . '<br>';
      }
      
      echo "company-".$comp_id."  Executed"."<br>";
    }

    function alter_tanmastern($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_tanmastern_'.$comp_fy_id;
      
      try{
          $external_db->query('ALTER TABLE ' . $table . ' DROP  `bo_it_jurid`');
        }
        catch (\Exception $e) {
         echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN `bo_tan_jurid` VARCHAR(50) NOT NULL');
        }
        catch (\Exception $e) {
         echo $e->getMessage() . '<br>';
      }
      
      echo "company-".$comp_id."  Executed"."<br>";
    }

    function update_hobomaster($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_hobomaster_'.$comp_fy_id;

      try{
        // $result = $external_db->query('select * from ' . $table . ' where acc_grp_id = 1 AND acc_id = 3 ')->getResultArray();
        // echo "<pre>";print_r($result);

        // $external_db->query('update ' . $table . ' set acc_id = 3 where acc_grp_id = 1 AND acc_id = 1 ');
        
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      echo "company-".$comp_id."  Executed"."<br>";
    }

function update_accountmaster($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_acctaddmst_'.$comp_fy_id;

      try{
        // $result = $external_db->query('select * from ' . $table . ' where acc_grp_id = 1 AND acc_id = 3 ')->getResultArray();
        // echo "<pre>";print_r($result);
       //echo 'update ' . $table . ' set acc_state ="31" where acc_state IS NULL ';
        //echo '<br>';
        $external_db->query('update ' . $table . ' set acc_state ="31" where acc_state IS NULL ');
        
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      echo "company-".$comp_id."  Executed"."<br>";
    }
	
	

    function update_item_valmethod($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_itemmaster_'.$comp_fy_id;

      try{
        $external_db->query('UPDATE ' . $table . ' SET valmethod_id = 1 where valmethod_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      echo "company-".$comp_id."  Executed"."<br>";
    }


    function alter_cost_center_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_costcttxnn_'.$comp_fy_id;
      try{
          $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN `acc_type` VARCHAR(3) NOT NULL');
        }
        catch (\Exception $e) {
         echo $e->getMessage() . '<br>';
      }
      try{
            $external_db->query('UPDATE ' . $table . ' SET acc_type = "acc"');
          }
          catch (\Exception $e) {
         echo $e->getMessage();
      }
      echo "company-".$comp_id."  Executed"."<br>";
    }
    
	function create_table_vchaddinfo($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_vchaddinfo_'.$comp_fy_id;    
      try{
          $external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query("CREATE TABLE `".$table."` (
            `txn_id` bigint NOT NULL,
            `voucher_txn_id` bigint NOT NULL,
            `vch_txn_drcr` varchar(5) NOT NULL,
            `vch_txn_incl` decimal(18,8) NOT NULL,
            `txn_type` varchar(8) NOT NULL COMMENT 'itm,bsd',            
            KEY `voucher indexing` (`txn_id`,`voucher_txn_id`)
          ) ENGINE=InnoDB ;"); 
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }
	
	
    function create_new_ewbpartbdt_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_ewbpartbdt_'.$comp_fy_id;    
      try{
          $external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query("CREATE TABLE `".$table."` (
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
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_new_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_acctgstmst_'.$comp_fy_id;    
      try{
          $external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query("CREATE TABLE `".$table."` (
            `acc_id` bigint NOT NULL,
            `acc_gstin` varchar(20) NOT NULL,
            `acc_legal_name` varchar(100) NOT NULL,
            `acc_trade_name` varchar(100) NOT NULL,
            `acc_dealer_type` int NOT NULL,
            `acc_status` int NOT NULL,
            `acc_wef` date NOT NULL,
            `acc_rev_chgs` int NOT NULL DEFAULT 0,
            KEY `acc indexing` (`acc_id`,`acc_rev_chgs`) USING BTREE
          ) ENGINE=InnoDB ;"); 
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      //--------------

      $table = $comp_id.'_billsundry_'.$comp_fy_id;    
      try{
          $external_db->query('ALTER TABLE `'.$table.'` DROP `sundry_calc_subtype` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query('ALTER TABLE `'.$table.'` DROP `sundry_calc_type`  ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      try{
          $external_db->query('ALTER TABLE `'.$table.'` MODIFY `sundry_type` VARCHAR(2) NOT NULL  ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      //-------------

      $table = $comp_id.'_cmpgstcatn_'.$comp_fy_id;    
      try{
          $external_db->query('ALTER TABLE `'.$table.'` ADD `cmp_tax_cat_cess_basis` VARCHAR(1) NOT NULL DEFAULT 1 AFTER `tax_cat_id` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      //---------------

      $table = $comp_id.'_acctmaster_'.$comp_fy_id;    
      try{
          $external_db->query('ALTER TABLE `'.$table.'` DROP `acc_gstin` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query('ALTER TABLE `'.$table.'` DROP `acc_dealer_type` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      //-----------------

      $table = $comp_id.'_gstinmastr_'.$comp_fy_id;    
      try{
          $external_db->query('ALTER TABLE `'.$table.'` ADD `gstin_legal_name` VARCHAR(100) NOT NULL DEFAULT 0 AFTER `gstin_inactivedate`, ADD `gstin_trade_name` VARCHAR(100) NOT NULL DEFAULT 0 AFTER `gstin_legal_name`, ADD `gstin_state_code` VARCHAR(10) NOT NULL DEFAULT 0 AFTER `gstin_trade_name`');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      try{
          $external_db->query('ALTER TABLE `'.$table.'` CHANGE `comp_gst_jurid_st` `comp_gst_jurid_st` VARCHAR(50) NOT NULL, CHANGE `comp_gst_jurid_ct` `comp_gst_jurid_ct` VARCHAR(50) NOT NULL');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      //-------------

      $table = $comp_id.'_acctgstsum_'.$comp_fy_id;    
      try{
          $external_db->query('ALTER TABLE `'.$table.'`  ADD `acc_cess_basis` VARCHAR(1) NOT NULL AFTER `total_tax` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      //--------------

      $table = $comp_id.'_itemvalmst_'.$comp_fy_id;    
      try{
          $external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query("CREATE TABLE `".$table."` (
            `itemvalmst_id` bigint NOT NULL AUTO_INCREMENT,
            `comp_id` bigint NOT NULL,
            `item_id` bigint NOT NULL,
            `item_mrp` DECIMAL(18,8) NOT NULL,
            PRIMARY KEY (`itemvalmst_id`),
            KEY `value mst index` (`comp_id`,`item_id`)
          ) ENGINE=InnoDB ;"); 
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      //---------------------------

      $errors = [];
      $ERPtables = new ERPtables($comp_id,$comp_fy_id);

      $response = $ERPtables->new_tax_tables(true,true);
      foreach($response as $error){ $errors[] = $error; }
      echo "<pre>";print_r($errors);

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function drop_mc_store_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_mcstoremst_'.$comp_fy_id;
      try{
          $external_db->query('DROP TABLE ' . $table . '');
        }
      catch (\Exception $e) {
          echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function alter_project_op_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_prjoppybal_'.$comp_fy_id;
      // try{
      //     $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN `project_bal_type` VARCHAR(3) NOT NULL');
      //   }
      // catch (\Exception $e) {
      //     echo $e->getMessage() . '<br>';
      // }
      try{
          $external_db->query('ALTER TABLE ' . $table . ' DROP INDEX project_id, ADD UNIQUE KEY `project_id` (`project_id`,`bo_id`,`project_bal_type`)');
        }
      catch (\Exception $e) {
          echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function alter_item_val_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);
      try{
        
        $table = $comp_id.'_itemmaster_'.$comp_fy_id;
        $result_ = $external_db->table($table)->get()->getResultArray();

        foreach ($result_ as $key_ => $value_) {
          $acc_id = $value_['item_id'];

          $sub_table = $comp_id.'_itemtxnval_'.$acc_id.'_'.$comp_fy_id;

          try{
              $external_db->query('ALTER TABLE ' . $sub_table . ' ADD COLUMN profit_string VARCHAR(255) NOT NULL');
          }
          catch (\Exception $e) {
              echo $e->getMessage() . '<br>';
          }

        }
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_acctgstsum_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);


      $table = $comp_id.'_acctgstsum_'.$comp_fy_id;    
      try{
          $external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }
      try{
          $external_db->query("CREATE TABLE `".$table."` (
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
            `acc_type` varchar(5) NOT NULL COMMENT 'item or  billsundry',
            `total_tax` decimal(18,8) NOT NULL,
            PRIMARY KEY (`tgsmid`)
          ) ENGINE=InnoDB ;");
        
      }
      catch (\Exception $e) {
       echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function get_bbb_opn_bal($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_billmaster_'.$comp_fy_id;
      $table2 = $comp_id.'_billsoppyn_'.$comp_fy_id;
      $final = [];

      try{
        $result = $external_db->table($table)
                            ->select('bills_ref_id')
                            ->orderBy('bills_ref_id','asc')
                            ->get()->getResultArray();
        echo "<pre>";print_r($result);
        $result = $external_db->table($table2)
                            ->select('bills_ref_id,count(*)')
                            ->groupBy('bills_ref_id')
                            ->orderBy('bills_ref_id','asc')
                            ->get()->getResultArray();
        echo "<pre>";print_r($result);exit;
        $final = [];
        foreach ($result as $key => $value) {
          
          $status = 1;
          $balance = floatval($value['bill_op_bal']);

          foreach ($final as $key2 => $value2) {
            if($status){
              if($value['acc_id'] == $value2['acc_id'] && $value['bills_ref_name'] == $value2['bills_ref_name']){
                $status = 0;

                $op_data = [
                  'bills_ref_id'  => $final[$key2]['bills_ref_id'],
                  'bo_id'         => $value['bo_id'],
                  'bills_op_bal'  => floatval($value['bill_op_bal']),
                  'bills_py_bal'  => 0,
                ];
                $final[$key2]['op_data'][] = $op_data;
              }
            }
          }

          if($status){
            $op_data = [
              'bills_ref_id'  => $value['bills_ref_id'],
              'bo_id'         => $value['bo_id'],
              'bills_op_bal'  => floatval($value['bill_op_bal']),
              'bills_py_bal'  => 0,
            ];

            $final[] = [
              'bills_ref_id'  => $value['bills_ref_id'],
              'bills_ref_name'=> $value['bills_ref_name'],
              'acc_id'        => $value['acc_id'],
              'bill_due_date' => $value['bill_due_date'],
              'op_data'       => [$op_data],
            ];
          }
        }
        // echo "<pre>";print_r($final);exit;

        
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function set_bbb_opn_bal($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_billmaster_'.$comp_fy_id;
      $table2 = $comp_id.'_billsoppyn_'.$comp_fy_id;
      $final = [];

      try{
        $result = $external_db->table($table)
                            ->orderBy('bills_ref_id','asc')
                            ->get()->getResultArray();
        // echo "<pre>";print_r($result);exit;
        $final = [];
        foreach ($result as $key => $value) {
          
          $status = 1;
          $balance = floatval($value['bill_op_bal']);

          foreach ($final as $key2 => $value2) {
            if($status){
              if($value['acc_id'] == $value2['acc_id'] && $value['bills_ref_name'] == $value2['bills_ref_name']){
                $status = 0;

                $op_data = [
                  'bills_ref_id'  => $final[$key2]['bills_ref_id'],
                  'bo_id'         => $value['bo_id'],
                  'bills_op_bal'  => floatval($value['bill_op_bal']),
                  'bills_py_bal'  => 0,
                ];
                $final[$key2]['op_data'][] = $op_data;
              }
            }
          }

          if($status){
            $op_data = [
              'bills_ref_id'  => $value['bills_ref_id'],
              'bo_id'         => $value['bo_id'],
              'bills_op_bal'  => floatval($value['bill_op_bal']),
              'bills_py_bal'  => 0,
            ];

            $final[] = [
              'bills_ref_id'  => $value['bills_ref_id'],
              'bills_ref_name'=> $value['bills_ref_name'],
              'acc_id'        => $value['acc_id'],
              'bill_due_date' => $value['bill_due_date'],
              'op_data'       => [$op_data],
            ];
          }
        }
        // echo "<pre>";print_r($final);exit;

        foreach ($final as $key => $value) {
          if(!empty($value['op_data'])){
            $op_data = $value['op_data'];

            foreach ($op_data as  $data) {
              $exists = $external_db->table($table2)
                            ->where('bills_ref_id',$data['bills_ref_id'])
                            ->where('bo_id',$data['bo_id'])
                            ->get()->getRowArray();
              if($exists){
                $external_db->table($table2)
                            ->where('bills_ref_id',$data['bills_ref_id'])
                            ->where('bo_id',$data['bo_id'])
                            ->update($data);
              } 
              else{
                $external_db->table($table2)->insert($data);
              }
            }
              
          }
        }
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function delete_bbb($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_billmaster_'.$comp_fy_id;
      $table2 = $comp_id.'_billsoppyn_'.$comp_fy_id;
      $final = [];

      try{
        $result = $external_db->table($table)
                            ->orderBy('bills_ref_id','asc')
                            ->get()->getResultArray();
        $final = [];
        $array = [];
        foreach ($result as $key => $value) {
          
          $status = 1;
          $balance = floatval($value['bill_op_bal']);

          foreach ($final as $key2 => $value2) {
            if($status){
              if($value['acc_id'] == $value2['acc_id'] && $value['bills_ref_name'] == $value2['bills_ref_name']){
                $status = 0;
                $array[] = $value['bills_ref_id'];  
                
              }
            }
          }

          if($status){

            $final[] = [
              'bills_ref_id'  => $value['bills_ref_id'],
              'bills_ref_name'=> $value['bills_ref_name'],
              'acc_id'        => $value['acc_id'],
              'bill_due_date' => $value['bill_due_date'],
            ];
          }
        }
        // echo "<pre>";print_r($array);
        foreach ($array as $key => $value) {
          $external_db->table($table)
                      ->where('bills_ref_id',$value)
                      ->delete();
        }
        // echo "<pre>";print_r($result);
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function missing_tables($comp_id, $comp_code, $comp_fy_id, $uuid)
    {
      $errors = [];
      $ERPtables = new ERPtables($comp_id,$comp_fy_id);
      // $response = $ERPtables->default_tables(true,false);
      // foreach($response as $error){ $errors[] = $error; }

      $response = $ERPtables->project_tables(true,true);
      foreach($response as $error){ $errors[] = $error; }
      $response = $ERPtables->set_project_data();
      foreach($response as $error){ $errors[] = $error; }
      $response = $ERPtables->set_project_group_data();
      foreach($response as $error){ $errors[] = $error; }

      $UUIDtables = new UUIDtables($uuid,$comp_id,$comp_fy_id);
      $UUIDtables->create_project_base_id();

      echo "<pre>";print_r($errors); 

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function branch_account($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_hobomaster_'.$comp_fy_id;
      try{
        $result = $external_db->table($table)->get()->getResultArray();
        foreach ($result as $key => $value) {
          if(!$value['acc_id']){

            // $external_db->table($table)
            //               ->where('bo_id',$value['bo_id'])
            //               ->update(['acc_id' => 3]);

            // echo '<br>comp_fy_id '.$comp_fy_id;
            // echo '<br>acc_id '.$value['acc_id'];
            // echo "---";

            // $table = $comp_id.'_acctmaster_'.$comp_fy_id;
            // $account = $external_db->table($table)
            //               ->where('acc_id',3)
            //               ->get()->getRowArray();
            // if(!$account){
            //   $data = [
            //       'acc_id'          => 3,
            //       'comp_id'         => 0,
            //       'acc_name'        => 'HO',
            //       'acc_name_alias'  => 'HO',
            //       'acc_name_print'  => 'HO',
            //       'acc_grp_id'      => $value['acc_grp_id'],
            //       'acc_grp_parent_id' => 14,
            //     ];

            //   $external_db->table($table)->insert($data);
            // }
            // else{
            //   $data = [
            //       'acc_id'          => 3,
            //       'comp_id'         => 0,
            //       'acc_name'        => 'HO',
            //       'acc_name_alias'  => 'HO',
            //       'acc_name_print'  => 'HO',
            //       'acc_grp_id'      => $value['acc_grp_id'],
            //       'acc_grp_parent_id' => 14,
            //     ];

            //   $external_db->table($table)
            //               ->where('acc_id', 3)
            //               ->update($data);
            // }
          }

        }
        echo "<pre>";print_r($result);
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function main_fy($uuid,$comp_id,$fy_id)
    {
      $UUIDtables = new UUIDtables($uuid,$comp_id,$fy_id);
      $UUIDtables->default_tables();
      $UUIDtables->update_base_id();

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function other_fy_log($comp_id, $comp_code, $comp_fy_id,$uuid)
    {
      
      
      $external_db = $this->externaldb->single_company_db($comp_code);


      $array = [
        'hobomaster' => ['id' => 'bo_id', 'name' => 'bo_name'],
        'compcurrcy' => ['id' => 'comp_currency_id', 'name' => 'curr_name'],
        'cmpvchseri' => ['id' => 'comp_vch_series_id', 'name' => 'comp_vch_series'],

        'acctgroupn' => ['id' => 'acc_grp_id', 'name' => 'acc_grp_name'],
        'acctmaster' => ['id' => 'acc_id', 'name' => 'acc_name'],
        'billsundry' => ['id' => 'bill_sundry_id', 'name' => 'bill_sundry_name'],
        'projectgrp' => ['id' => 'project_grp_id', 'name' => 'project_grp_name'],
        'projectmst' => ['id' => 'project_id', 'name' => 'project_name'],

        'itemgrpmst' => ['id' => 'item_grp_id', 'name' => 'item_grp_name'],
        'itemcatmst' => ['id' => 'icatgms_id', 'name' => 'item_cat'],
        'itemmaster' => ['id' => 'item_id', 'name' => 'item_name'],
        'itmbatchmt' => ['id' => 'batch_id', 'name' => 'batch_no'],
        'itmunitmst' => ['id' => 'unit_id', 'name' => 'item_unit'],

        'mcgrpmstnn' => ['id' => 'mc_grp_id', 'name' => 'mc_grp_name'],
        'mcmasternn' => ['id' => 'mat_cent_id', 'name' => 'mat_cent_name'],
        'mcstoremst' => ['id' => 'mc_store_id', 'name' => 'mc_store_name'],
        'barcodemst' => ['id' => 'barcode_id', 'name' => 'barcode'],
        'labelmastr' => ['id' => 'label_id', 'name' => 'label_name'],

        'costctgrup' => ['id' => 'cc_grp_id', 'name' => 'cc_grp_name'],
        'costctmstr' => ['id' => 'cc_id', 'name' => 'cc_name'],
        'billmaster' => ['id' => 'bills_ref_id', 'name' => 'bills_ref_name'],
        'billofmatn' => ['id' => 'bom_id', 'name' => 'bom_name'],
        'prntconfig' => ['id' => 'prntconfig_id', 'name' => 'prntconfig_id'],
      ];

      foreach ($array as $key_ => $value_) {

        $table = $comp_id.'_'.$key_.'_'.$comp_fy_id;
        try{
          $result = $external_db->table($table)
                                ->get()->getResultArray();

          foreach ($result as $key => $value) {
            $id = $value[$value_['id']];
            $name = $value[$value_['name']];
            $mst_base_id = $value['mst_base_id'];

            if($mst_base_id == 0){
              
              echo '<br>Table-'. $table;
              echo '<br>id-'. $id;
              echo '<br>name-'. $name;

            }
          }
        }
        catch (\Exception $e) {
          echo $e->getMessage() . '<br>';
        }
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function other_fy($comp_id, $comp_code, $comp_fy_id,$uuid)
    {
      
      
      $external_db = $this->externaldb->single_company_db($comp_code);


      $array = [
        'hobomaster' => ['id' => 'bo_id', 'name' => 'bo_name'],
        'compcurrcy' => ['id' => 'comp_currency_id', 'name' => 'curr_name'],
        'cmpvchseri' => ['id' => 'comp_vch_series_id', 'name' => 'comp_vch_series'],

        'acctgroupn' => ['id' => 'acc_grp_id', 'name' => 'acc_grp_name'],
        'acctmaster' => ['id' => 'acc_id', 'name' => 'acc_name'],
        'billsundry' => ['id' => 'bill_sundry_id', 'name' => 'bill_sundry_name'],
        'projectgrp' => ['id' => 'project_grp_id', 'name' => 'project_grp_name'],
        'projectmst' => ['id' => 'project_id', 'name' => 'project_name'],

        'itemgrpmst' => ['id' => 'item_grp_id', 'name' => 'item_grp_name'],
        'itemcatmst' => ['id' => 'icatgms_id', 'name' => 'item_cat'],
        'itemmaster' => ['id' => 'item_id', 'name' => 'item_name'],
        'itmbatchmt' => ['id' => 'batch_id', 'name' => 'batch_no'],
        'itmunitmst' => ['id' => 'unit_id', 'name' => 'item_unit'],

        'mcgrpmstnn' => ['id' => 'mc_grp_id', 'name' => 'mc_grp_name'],
        'mcmasternn' => ['id' => 'mat_cent_id', 'name' => 'mat_cent_name'],
        'mcstoremst' => ['id' => 'mc_store_id', 'name' => 'mc_store_name'],
        'barcodemst' => ['id' => 'barcode_id', 'name' => 'barcode'],
        'labelmastr' => ['id' => 'label_id', 'name' => 'label_name'],

        'costctgrup' => ['id' => 'cc_grp_id', 'name' => 'cc_grp_name'],
        'costctmstr' => ['id' => 'cc_id', 'name' => 'cc_name'],
        'billmaster' => ['id' => 'bills_ref_id', 'name' => 'bills_ref_name'],
        'billofmatn' => ['id' => 'bom_id', 'name' => 'bom_name'],
        'prntconfig' => ['id' => 'prntconfig_id', 'name' => 'prntconfig_id'],
      ];

      foreach ($array as $key_ => $value_) {

        $table = $comp_id.'_'.$key_.'_'.$comp_fy_id;
        try{
          $result = $external_db->table($table)
                                ->get()->getResultArray();

          foreach ($result as $key => $value) {
            $id = $value[$value_['id']];
            $name = $value[$value_['name']];
            $mst_base_id = $value['mst_base_id'];

            if($mst_base_id == 0){
              $UUIDtables = new UUIDtables($uuid,$comp_id,$comp_fy_id);
              $mst_base_id= $UUIDtables->get_mst_base_id($id,$key_);

              $external_db->table($table)
                                ->where($value_['id'], $id)
                                ->update(['mst_base_id' => $mst_base_id]);
            }
          }
        }
        catch (\Exception $e) {
          echo $e->getMessage() . '<br>';
        }
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function copy_mstbaseid_table($comp_id, $comp_code, $comp_fy_id, $comp_fy_id2,$uuid)
    {
      $UUIDtables = new UUIDtables($uuid,$comp_id,$comp_fy_id);
      $UUIDtables->delete_comp_fy_mst_map($comp_fy_id2);

      $external_db = $this->externaldb->single_company_db($comp_code);


      $array = [
        'hobomaster' => ['id' => 'bo_id', 'name' => 'bo_name'],
        'compcurrcy' => ['id' => 'comp_currency_id', 'name' => 'curr_name'],
        'cmpvchseri' => ['id' => 'comp_vch_series_id', 'name' => 'comp_vch_series'],

        'acctgroupn' => ['id' => 'acc_grp_id', 'name' => 'acc_grp_name'],
        'acctmaster' => ['id' => 'acc_id', 'name' => 'acc_name'],
        'billsundry' => ['id' => 'bill_sundry_id', 'name' => 'bill_sundry_name'],
        'projectgrp' => ['id' => 'project_grp_id', 'name' => 'project_grp_name'],
        'projectmst' => ['id' => 'project_id', 'name' => 'project_name'],

        'itemgrpmst' => ['id' => 'item_grp_id', 'name' => 'item_grp_name'],
        'itemcatmst' => ['id' => 'icatgms_id', 'name' => 'item_cat'],
        'itemmaster' => ['id' => 'item_id', 'name' => 'item_name'],
        'itmbatchmt' => ['id' => 'batch_id', 'name' => 'batch_no'],
        'itmunitmst' => ['id' => 'unit_id', 'name' => 'item_unit'],

        'mcgrpmstnn' => ['id' => 'mc_grp_id', 'name' => 'mc_grp_name'],
        'mcmasternn' => ['id' => 'mat_cent_id', 'name' => 'mat_cent_name'],

        'barcodemst' => ['id' => 'barcode_id', 'name' => 'barcode'],
        'labelmastr' => ['id' => 'label_id', 'name' => 'label_name'],

        'costctgrup' => ['id' => 'cc_grp_id', 'name' => 'cc_grp_name'],
        'costctmstr' => ['id' => 'cc_id', 'name' => 'cc_name'],
        'billmaster' => ['id' => 'bills_ref_id', 'name' => 'bills_ref_name'],
        'billofmatn' => ['id' => 'bom_id', 'name' => 'bom_name'],
        'prntconfig' => ['id' => 'prntconfig_id', 'name' => 'prntconfig_id'],
      ];

      foreach ($array as $key_ => $value_) {

        $table = $comp_id.'_'.$key_.'_'.$comp_fy_id;
        $table2 = $comp_id.'_'.$key_.'_'.$comp_fy_id2;
        try{
          $result = $external_db->table($table)
                                ->get()->getResultArray();

          foreach ($result as $key => $value) {
            $id = $value[$value_['id']];
            $name = $value[$value_['name']];
            $mst_base_id = $value['mst_base_id'];

            $result2 = $external_db->table($table2)
                                ->where($value_['id'], $id)
                                ->where($value_['name'], $name)
                                ->get()->getRowArray();
            if($result2){
              $id2 = $result2[$value_['id']];
              $external_db->table($table2)
                          ->where($value_['id'],$id2)
                          ->update(['mst_base_id' => $mst_base_id]);

              $UUIDtables->create_comp_fy_mst_map($id2,$key_,$mst_base_id,$comp_fy_id2);
            }
          }
        }
        catch (\Exception $e) {
          echo $e->getMessage() . '<br>';
        }
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function reset_mstbaseid_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_hobomaster_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_compcurrcy_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_cmpvchseri_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_acctgroupn_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_acctmaster_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_billsundry_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itemgrpmst_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itemcatmst_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itemmaster_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itmbatchmt_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itmunitmst_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_mcgrpmstnn_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_mcmasternn_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_mcstoremst_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_billofmatn_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_barcodemst_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_labelmastr_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_costctgrup_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_costctmstr_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_billmaster_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_projectgrp_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_projectmst_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_prntconfig_'.$comp_fy_id;
      try{
        $external_db->query('Update ' . $table . ' set mst_base_id = 0');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function alter_mstbaseid_table($comp_id, $comp_code, $comp_fy_id)
    {
      $external_db    = $this->externaldb->single_company_db($comp_code);

      $table = $comp_id.'_hobomaster_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_compcurrcy_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_cmpvchseri_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_acctgroupn_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_acctmaster_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_billsundry_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itemgrpmst_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itemcatmst_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itemmaster_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itmbatchmt_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_itmunitmst_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_mcgrpmstnn_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_mcmasternn_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_mcstoremst_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_billofmatn_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_barcodemst_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_labelmastr_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_costctgrup_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_costctmstr_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_billmaster_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_projectgrp_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }
      $table = $comp_id.'_projectmst_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      $table = $comp_id.'_prntconfig_'.$comp_fy_id;
      try{
        $external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN mst_base_id BIGINT NOT NULL');
      }
      catch (\Exception $e) {
        echo $e->getMessage() . '<br>';
      }

      echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_project_reporting_table($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_projectgrp_'.$comp_fy_id;
  		try{
  				$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
        	$external_db->query('CREATE TABLE `'.$table.'` (
                              `project_grp_id` INT NOT NULL AUTO_INCREMENT,
                              `comp_id` BIGINT NOT NULL ,
                              `project_grp_name` varchar(100) NOT NULL, 
                              `project_grp_alias` varchar(100) NOT NULL,
                               `under_project_grp_id` INT NOT NULL,
                               `under_main_grp_id` INT NOT NULL,
                               `mst_base_id` BIGINT NOT NULL,
                              PRIMARY KEY (`project_grp_id`)
                              ) ENGINE=InnoDB');
        }
        catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}
			$table = $comp_id.'_projectgrp_'.$comp_fy_id;
  		try{
        	$external_db->query("TRUNCATE TABLE ${table};");
        	$external_db->query('INSERT INTO `'.$table.'`  (`comp_id`, `project_grp_name`, `project_grp_alias`, `under_project_grp_id`, `under_main_grp_id`) VALUES ('.$comp_id.',"MAIN","MAIN",0,0);');
        }
        catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}
			//--------------
			$table = $comp_id.'_projectmst_'.$comp_fy_id;
  		try{
  				$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
        	$external_db->query('CREATE TABLE `'.$table.'` (
                              `project_id` BIGINT NOT NULL AUTO_INCREMENT,
                              `comp_id` BIGINT NOT NULL ,
                              `project_name` varchar(100) NOT NULL, 
                              `project_alias` varchar(100) NOT NULL,
                              `project_print` varchar(100) NOT NULL,
                              `project_grp_id` INT NOT NULL,
                              `mst_base_id` BIGINT NOT NULL,
                              PRIMARY KEY (`project_id`)
                              ) ENGINE=InnoDB');
        }
        catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}
			try{
        	$external_db->query("TRUNCATE TABLE ${table};");

        	$external_db->query('INSERT INTO `'.$table.'`  (`comp_id`, `project_name`, `project_alias`, `project_print`, `project_grp_id`) VALUES ('.$comp_id.',"UNDEFINED_EXP","UNDEFINED_EXP","UNDEFINED_EXP",1);');
					$external_db->query('INSERT INTO `'.$table.'`  (`comp_id`, `project_name`, `project_alias`, `project_print`, `project_grp_id`) VALUES ('.$comp_id.',"UNDEFINED_REV","UNDEFINED_REV","UNDEFINED_REV",1);');
					$external_db->query('INSERT INTO `'.$table.'`  (`comp_id`, `project_name`, `project_alias`, `project_print`, `project_grp_id`) VALUES ('.$comp_id.',"UNDEFINED_LIA","UNDEFINED_LIA","UNDEFINED_LIA",1);');
					$external_db->query('INSERT INTO `'.$table.'`  (`comp_id`, `project_name`, `project_alias`, `project_print`, `project_grp_id`) VALUES ('.$comp_id.',"UNDEFINED_AST","UNDEFINED_AST","UNDEFINED_AST",1);');
      }
      catch (\Exception $e) {
	   		echo $e->getMessage() . '<br>';
			}

			//--------------
			$table = $comp_id.'_prjoppybal_'.$comp_fy_id;
  		try{
  				$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
        	$external_db->query('CREATE TABLE `'.$table.'` (
                              `project_id` BIGINT NOT NULL,
                              `project_op_bal` DECIMAL(18,8) NOT NULL ,
                              `project_py_bal` DECIMAL(18,8) NOT NULL, 
                              `bo_id` BIGINT NOT NULL
                              ) ENGINE=InnoDB');
        }
      catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}
			//--------------
			$table = $comp_id.'_projexptxn_'.$comp_fy_id;
  		try{
  				$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');	
        	$external_db->query('CREATE TABLE `'.$table.'` (
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
                              ) ENGINE=InnoDB');
        }
        catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}
			//--------------
			$table = $comp_id.'_projrevtxn_'.$comp_fy_id;
  		try{
  				$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
        	$external_db->query('CREATE TABLE `'.$table.'` (
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
                              ) ENGINE=InnoDB');
        }
        catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}
			//--------------
			$table = $comp_id.'_prjliabtxn_'.$comp_fy_id;
  		try{
  				$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
        	$external_db->query('CREATE TABLE `'.$table.'` (
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
                              ) ENGINE=InnoDB');
        }
        catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}
			//--------------
			$table = $comp_id.'_projasttxn_'.$comp_fy_id;
  		try{
  				$external_db->query('DROP TABLE IF EXISTS `'.$table.'` ');
        	$external_db->query('CREATE TABLE `'.$table.'` (
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
                              ) ENGINE=InnoDB');
        }
        catch (\Exception $e) {
		   		echo $e->getMessage() . '<br>';
			}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_bdstaxmstn_table($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_bdstaxmstn_'.$comp_fy_id;
  		try{
        	$external_db->query("CREATE TABLE IF NOT EXISTS `".$table."` (
					`bill_sundry_id` BIGINT NOT NULL, 
					`cmp_tax_cat_id` BIGINT NOT NULL,
					`tax_cat_id` BIGINT NOT NULL, 
					`bill_supply_type` varchar(1), 
					`bill_hsn_sac` varchar(100) ,
					`bill_input_output` varchar(1) DEFAULT NULL ,
					`bill_tax_short_code` varchar(10) DEFAULT NULL,
					`bill_tax_account` varchar(10) DEFAULT 0,
					`bill_tax_type` INT DEFAULT 0
				) ENGINE=InnoDB ;");
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_itemtaxmst_table($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemtaxmst_'.$comp_fy_id;
  		try{
        	$external_db->query('CREATE TABLE `'.$table.'` (
								  `comp_id` bigint NOT NULL,
								  `item_id` bigint NOT NULL,
								  `item_supply_type` INT NOT NULL,
								  `item_hsn_sac` varchar(100) NULL,				  
								  `tax_cat_id` int NULL,
								  `item_tax_igst_rate` DECIMAL(18,2) NULL,
								  `item_tax_cess_rate` DECIMAL(18,2) NULL,
								  `item_tax_wef` DATE  NULL,
								  `item_tax_upto` DATE  NULL,
								  `item_tax_basis` varchar(20)  NULL			  
								) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_acctaxmstn_table($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_acctaxmstn_'.$comp_fy_id;
  		try{
        	$external_db->query('CREATE TABLE `'.$table.'` (
									  `comp_id` bigint NOT NULL , 
									  `acc_id` bigint NOT NULL ,
									  `tax_cat_id` INT  NULL,
									  `acc_supply_type` INT  NULL,
									  `acc_hsn_sac` varchar(50)  NULL,
									  `acc_igst_rate` DECIMAL(18,2)  NULL,
									  `acc_cess_rate` DECIMAL(18,2)  NULL,
									  `acc_tax_wef` DATE  NULL,					  
									  `acc_tax_basis` varchar(20)  NULL,	
									  `acc_tax_upto` DATE  NULL	 
									) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_acctaddmst_table($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_acctaddmst_'.$comp_fy_id;
  		try{
        	$external_db->query('CREATE TABLE `'.$table.'` (
									  `acc_id` bigint NOT NULL ,
									  `comp_id` bigint NOT NULL,
									  `acc_add1` varchar(150)  NULL,
									  `acc_add2` varchar(150)  NULL,
									  `acc_city` varchar(50)  NULL,
									  `acc_pin` varchar(15)  NULL,
									  `acc_state` int  NULL,
									  `acc_country` int  NULL,	  
									  `acc_state_code` varchar(6)  NULL			  
									) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function modify_all_tables($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	try{
        	$result = $external_db->query('SHOW TABLES')->getResultArray();
        	foreach ($result as $key => $array) {

        		$value = reset( $array);
				$value = current( $array);
				$value = end( $array);

        		try{
        			$external_db->query('UPDATE ' . $value . ' SET comp_id = '.$comp_id);
        			// echo $value. '<br>';
        		}
        		catch (\Exception $e) {
				   echo $value. '-- '.$e->getMessage() . '<br>';
				}
        	}
        	// echo "<pre>";print_r($result);exit;
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		echo 'Comp- '.$comp_id .' executed'. '<br>';
    }

    function alter_grp_parent_restrict($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_grpparentn_'.$comp_fy_id;
  		try{
        	$external_db->query('ALTER TABLE `'.$table.'` RENAME COLUMN restrictions to acc_grp_restrict');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_taxcatmstn_table($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_taxcatmstn_'.$comp_fy_id;
  		try{
        	$external_db->query('CREATE TABLE `'.$table.'` (
							  `tax_cat_id` bigint NOT NULL AUTO_INCREMENT,
							  `comp_id` bigint NOT NULL,
							  `tax_cat_type` int NOT NULL DEFAULT "0",
							  `tax_cat_name` varchar(70) NOT NULL,
							  `tax_cat_section` int NOT NULL DEFAULT "0",
							  PRIMARY KEY (`tax_cat_id`),
							  KEY `tax catg indexing` (`comp_id`,`tax_cat_type`)
							) ENGINE=MyISAM;');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_itemvalmst_table($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemvalmst_'.$comp_fy_id;
  		try{
        	$external_db->query('CREATE TABLE `'.$table.'` (
								  `itemvalmst_id` bigint NOT NULL AUTO_INCREMENT,
								  `comp_id` bigint NOT NULL,
								  `item_id` bigint NOT NULL,
								  `item_hsn` varchar(8) NOT NULL,
								  `tax_cat_id` bigint NOT NULL,
								  PRIMARY KEY (`itemvalmst_id`),
								  KEY `value mst index` (`comp_id`,`item_id`,`tax_cat_id`)
								) ENGINE=InnoDB;');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function create_print_config($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_prntconfig_'.$comp_fy_id;
  		try{
        	$external_db->query('CREATE TABLE IF NOT EXISTS `'.$table.'` (
				  `prntconfig_id` bigint NOT NULL AUTO_INCREMENT,
				  `comp_id` bigint NOT NULL,
				  `vch_series_id` bigint NOT NULL,
				  `usr_config_id` bigint NOT NULL,
          `mst_base_id` bigint NOT NULL,
				  PRIMARY KEY (`prntconfig_id`)) 
					ENGINE=InnoDB;');
        }
          catch (\Exception $e) {
  		   echo $e->getMessage() . '<br>';
  		}

		  $table = $comp_id.'_prntdesign_'.$comp_fy_id;
  		try{
        	$external_db->query('CREATE TABLE IF NOT EXISTS `'.$table.'` (
					  `prntconfig_id` bigint NOT NULL,
					  `erpprevaln_label_id` bigint NOT NULL,
					  `prntconfig_style` text NOT NULL
					) ENGINE=InnoDB ;');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		  }

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function alter_print_config($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_prntconfig_'.$comp_fy_id;
  		try{
        	$external_db->query('ALTER TABLE `'.$table.'` MODIFY prntconfig_id bigint NOT NULL AUTO_INCREMENT');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		  }

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function insert_print_config($comp_id, $comp_code, $comp_fy_id)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_prntconfig_'.$comp_fy_id;
  		try{
  			$external_db->query("TRUNCATE TABLE ${table};");
        	$external_db->query('INSERT INTO `'.$table.'`  (`prntconfig_id`, `comp_id`, `vch_series_id`, `usr_config_id`) VALUES (1,'.$comp_id.',13,17);');
  			$external_db->query('INSERT INTO `'.$table.'`  (`prntconfig_id`, `comp_id`, `vch_series_id`, `usr_config_id`) VALUES (2,'.$comp_id.',13,18);');
          }
          catch (\Exception $e) {
  		   echo $e->getMessage() . '<br>';
  		}

  		$table = $comp_id.'_prntdesign_'.$comp_fy_id;
  		try{
  			$external_db->query("TRUNCATE TABLE ${table};");
          	$external_db->query("INSERT INTO `".$table."` 
          		(`prntconfig_id`, `erpprevaln_label_id`, `prntconfig_style`) VALUES
  				(1,	6,	'font-weight: bold; font-family: Verdana; font-size: 12px;'),
  				(1,	7,	'font-weight: bold; font-family: Verdana; font-size: 12px;'),
  				(1,	8,	'text-align: center; padding: 0px; margin: 0px; font-family: Verdana; text-decoration: underline; font-weight: bold; font-size: 12px;'),
  				(1,	9,	'line-height: 42px; padding: 0px; margin: 0px; text-align: center; font-weight: bold; font-family: Verdana; font-size: 16px;'),
  				(1,	10,	'font-family: Verdana; font-size: 12px;'),
  				(1,	11,	'font-family: Verdana; font-size: 12px;'),
  				(1,	12,	'font-family: Verdana; font-size: 12px;'),
  				(1,	13,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
  				(1,	14,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
  				(1,	15,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
  				(1,	16,	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'),
  				(1,	17,	'padding-left: 29px; font-family: Verdana; font-size: 12px;'),
  				(1,	18,	'padding-left: 8px; font-family: Verdana; font-size: 12px;'),
  				(1,	19,	'text-align: right; font-family: Verdana; font-size: 14px;'),
  				(1,	20,	'font-family: Verdana; font-size: 14px; font-weight: bold;'),
  				(1,	26,	'padding-left: 10px; font-weight: bold; font-family: Verdana; font-size: 12px;'),
  				(1,	27,	'padding-left: 10px; font-weight: bold; font-family: Verdana; font-size: 12px;'),
  				(1,	28,	'padding-left: 18px; font-family: Verdana; font-size: 12px;'),
  				(1,	29,	'padding-left: 18px; font-family: Verdana; font-size: 12px;'),
  				(1,	30,	'font-family: Verdana; font-size: 12px;'),
  				(1,	31,	'font-family: Verdana; font-size: 12px;'),
  				(1,	32,	'font-family: Verdana; font-size: 12px;'),
  				(1,	33,	'font-family: Verdana; font-size: 12px;'),
  				(1,	34,	'text-align: center; padding: 0px 0px 8px; line-height: 20px; margin: 0px; font-family: Verdana; font-size: 12px;'),
  				(1,	36,	'padding: 6px 0px; font-family: Verdana; font-size: 14px;'),
  				(1,	37,	'font-family: Verdana; font-size: 12px;'),
  				(2,	6,	'font-family: Verdana; font-size: 12px;'),
  				(2,	26,	'padding-left: 10px; font-family: Verdana; font-size: 12px;'),
  				(2,	7,	'font-family: Verdana; font-size: 12px;'),
  				(2,	27,	'padding-left: 10px; font-family: Verdana; font-size: 12px;'),
  				(2,	8,	'font-family: Verdana; font-size: 24px; text-decoration: underline;'),
  				(2,	9,	'margin-top: 15px; line-height: 42px; font-family: Verdana; font-size: 14px;'),
  				(2,	34,	'font-family: Verdana; font-size: 12px;'),
  				(2,	28,	'font-family: Verdana; font-size: 12px; font-weight: bold;'),
  				(2,	11,	'font-family: Verdana; font-size: 12px;'),
  				(2,	29,	'padding-left: 12px; font-family: Verdana; font-size: 12px;'),
  				(2,	12,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 56%; font-family: Verdana; font-size: 12px;'),
  				(2,	30,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 56%; font-family: Verdana; font-size: 12px;'),
  				(2,	13,	'undefined'),
  				(2,	31,	'undefined'),
  				(2,	14,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 24%; font-family: Verdana; font-size: 12px;'),
  				(2,	32,	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 24%; font-family: Verdana; font-size: 12px;'),
  				(2,	15,	'undefined'),
  				(2,	33,	'undefined'),
  				(2,	16,	'padding: 6px; text-align: right; width: 20%; font-family: Verdana; font-size: 12px;'),
  				(2,	37,	'padding: 6px; text-align: right; width: 20%; font-family: Verdana; font-size: 12px;'),
  				(2,	17,	'padding-left: 29px; font-family: Verdana; font-size: 12px;'),
  				(2,	18,	'padding-left: 8px; font-family: Verdana; font-size: 12px;'),
  				(2,	19,	'text-align: right; font-family: Verdana; font-size: 14px;'),
  				(2,	36,	'font-family: Verdana; font-size: 14px;'),
  				(2,	20,	'font-family: Verdana; font-size: 12px;'),
  				(2,	24,	'font-family: Verdana; font-size: 12px;'),
  				(2,	25,	'font-family: Verdana; font-size: 12px;');");
          }
          catch (\Exception $e) {
  		   echo $e->getMessage() . '<br>';
  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }



    

    //----------------------------------------------------------------------------------------------

    function create_barcode_table($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_barcodemst_'.$comp_id;
  		try{
        	$external_db->query('CREATE TABLE `'.$table.'` (`barcode_id` BIGINT NOT NULL AUTO_INCREMENT, `barcode` VARCHAR(255) NOT NULL,`barcode_standard` VARCHAR(25) NOT NULL,`item_id` BIGINT NOT NULL,`unit_id` INT NOT NULL,`batch_id` BIGINT NOT NULL,`tracking_id` BIGINT NOT NULL,`barcode_wef` DATE NULL,`barcode_exp` DATE NULL,`barcode_status` TINYINT NOT NULL, PRIMARY KEY (`barcode_id`)) ENGINE=MyISAM;');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }


    function alter_tables_for_bo_id($comp_id, $comp_code, $comp_fy_id) //total = 17
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_vhtxnconso_'.$comp_fy_id;
    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		//-----------------------------------------------------------------------------------------

    	$table = $comp_id.'_acctmaster_'.$comp_fy_id;

    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
	  		$result_ = $external_db->table($table)->get()->getResultArray();

	  		foreach ($result_ as $key_ => $value_) {
	  			$acc_id = $value_['acc_id'];

	  			$sub_table = $comp_id.'_accnttxnnn_'.$acc_id.'_'.$comp_fy_id;
		        try{
		        	$external_db->query('ALTER TABLE ' . $sub_table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
		        }
		        catch (\Exception $e) {
				   echo $e->getMessage() . '<br>';
				}

				try{
		        	$external_db->query('UPDATE ' . $sub_table . ' SET bo_id = 1');
		        }
		        catch (\Exception $e) {
				   echo $e->getMessage() . '<br>';
				}
	  		}

  		}
  		catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

    	$table = $comp_id.'_accoppybal_'.$comp_fy_id;
    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		//------------------------------------------------------------------------------------

		$table = $comp_id.'_billsundry_'.$comp_fy_id;

		try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
	  		$result_ = $external_db->table($table)->get()->getResultArray();

	  		foreach ($result_ as $key_ => $value_) {
	  			$acc_id = $value_['bill_sundry_id'];

	  			$sub_table = $comp_id.'_sundrytxnn_'.$acc_id.'_'.$comp_fy_id;
		        try{
		        	$external_db->query('ALTER TABLE ' . $sub_table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
		        }
		        catch (\Exception $e) {
				   echo $e->getMessage() . '<br>';
				}

				try{
		        	$external_db->query('UPDATE ' . $sub_table . ' SET bo_id = 1');
		        }
		        catch (\Exception $e) {
				   echo $e->getMessage() . '<br>';
				}
	  		}

	  		}
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		$table = $comp_id.'_bsdoppybal_'.$comp_fy_id;
    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		//----------------------------------------------------

		$table = $comp_id.'_costctmstr_'.$comp_fy_id;
    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		$table = $comp_id.'_costcttxnn_'.$comp_fy_id;
    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		//----------------------------------------------------------------------------------

		$table = $comp_id.'_billmaster_'.$comp_fy_id;
    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		$table = $comp_id.'_billstxnnn_'.$comp_fy_id;
    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		//-----------------------------------------------------------------------------

		$table = $comp_id.'_itemmaster_'.$comp_fy_id;
		try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
	  		$result_ = $external_db->table($table)->get()->getResultArray();

	  		foreach ($result_ as $key_ => $value_) {
	  			$acc_id = $value_['item_id'];

	  			$sub_table = $comp_id.'_itemtxnnnn_'.$acc_id.'_'.$comp_fy_id;
		        try{
		        	$external_db->query('ALTER TABLE ' . $sub_table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
		        }
		        catch (\Exception $e) {
				   echo $e->getMessage() . '<br>';
				}

				try{
		        	$external_db->query('UPDATE ' . $sub_table . ' SET bo_id = 1');
		        }
		        catch (\Exception $e) {
				   echo $e->getMessage() . '<br>';
				}

	  		}
  		}
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		$table = $comp_id.'_itemtxnoth_'.$comp_fy_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		$table = $comp_id.'_itmoppybal_'.$comp_fy_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		$table = $comp_id.'_itmoppyval_'.$comp_fy_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}
		

		echo "company-".$comp_id."  Executed"."<br>";
    }

    function update_item_master_val_method_id($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemmaster_'.$comp_id;

    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN valmethod_id INT(1) NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

  		try{
        	$external_db->query('UPDATE ' . $table . ' SET valmethod_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}
		

		echo "company-".$comp_id."  Executed"."<br>";
  	} 

  	function modify_conso_for_currency($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_vhtxnconso_'.$comp_id;

    	try{
        	$external_db->query('ALTER TABLE ' . $table . ' ADD COLUMN currency_id INT(1) NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}

 		try{
        	$external_db->query('UPDATE ' . $table . ' SET currency_id = 1');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
		}
		

		echo "company-".$comp_id."  Executed"."<br>";
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

  	function update_item_opval($comp_id, $comp_code) // one time script already executed
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itmoppyval_'.$comp_id;

  		try{
        	$external_db->query('UPDATE ' . $table . ' SET method_id = 3 where method_id = 2');
        	$external_db->query('UPDATE ' . $table . ' SET method_id = 2 where method_id = 1');
        	$external_db->query('UPDATE ' . $table . ' SET method_id = 1 where method_id = 0');
        }
        catch (\Exception $e) {
		   echo $e->getMessage() . '<br>';
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

  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function modify_item_val_table($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['item_id'];

  			$sub_table = $comp_id.'_itemtxnval_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' ADD voucher_txn_id BIGINT NOT NULL');
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
	        	$external_db->query('CREATE TABLE `'.$sub_table.'` (`valuation_id` BIGINT NOT NULL AUTO_INCREMENT, `item_id` BIGINT NOT NULL,`item_txn_id` BIGINT NOT NULL,`voucher_txn_id` BIGINT NOT NULL,`method_id` INT(5) NOT NULL,`item_value` DECIMAL(18,4) NOT NULL, PRIMARY KEY (`valuation_id`)) ENGINE=MyISAM;');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function drop_old_item_val_bal($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['item_id'];

  			$sub_table = $comp_id.'_itemtxnnnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE `'.$sub_table.'` DROP COLUMN itemtxnbal_id ');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}

  			// $sub_table = $comp_id.'_itemtxnvaln_'.$acc_id.'_'.$comp_id;
	        // try{
	        // 	$external_db->query('DROP TABLE `'.$sub_table.'` ');
	        // }
	        // catch (\Exception $e) {
			//    echo $e->getMessage() . '<br>';
			// }

			// $sub_table = $comp_id.'_itemtxnbal_'.$acc_id.'_'.$comp_id;
	        // try{
	        // 	$external_db->query('DROP TABLE `'.$sub_table.'` ');
	        // }
	        // catch (\Exception $e) {
			//    echo $e->getMessage() . '<br>';
			// }
  		}

  		for($i=1; $i<=100; $i++ ){
  			$sub_table = $comp_id.'_itemtxnvaln_'.$i.'_'.$comp_id;
	        try{
	        	$external_db->query('DROP TABLE `'.$sub_table.'` ');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}

			$sub_table = $comp_id.'_itemtxnbal_'.$i.'_'.$comp_id;
	        try{
	        	$external_db->query('DROP TABLE `'.$sub_table.'` ');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage() . '<br>';
			}
  		}

  		echo "company-".$comp_id."  Executed"."<br>";
    }

    function default_item_txn_unit($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_itemmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['item_id'];

  			$sub_table = $comp_id.'_itemtxnnnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->table($sub_table)->where('item_unit', 0)->update(['item_unit' => 11]);
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

    function modify_bo_ids($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_acctmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['acc_id'];

  			$sub_table = $comp_id.'_accnttxnnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}
  		}

  		$sub_table = $comp_id.'_accoppybal_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		$table = $comp_id.'_accttxnoth_'.$comp_id;
  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$table = $comp_id.'_acctcrsref_'.$comp_id;
  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
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
	        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}

  		}

  		$table = $comp_id.'_itemtxnoth_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$table = $comp_id.'_itmoppybal_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$table = $comp_id.'_itmoppyval_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		//--------------------

  		$table = $comp_id.'_billmaster_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$sub_table = $comp_id.'_billstxnnn_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}
  		

  		//--------------------

  		$table = $comp_id.'_costctmstr_'.$comp_id;

  		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$sub_table = $comp_id.'_costcttxnn_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $sub_table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		//--------------------
		
		$table = $comp_id.'_billsundry_'.$comp_id;

		try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['bill_sundry_id'];

  			$sub_table = $comp_id.'_sundrytxnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}
  		}

  		$sub_table = $comp_id.'_bsdoppybal_'.$comp_id;
        try{
        	$external_db->query('ALTER TABLE ' . $table . ' MODIFY COLUMN bo_id BIGINT NOT NULL DEFAULT "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}
  		


  		echo "company-".$comp_id."  Executed"."<br>";       
    }

    function update_bo_ids($comp_id, $comp_code)
    {
    	$external_db    = $this->externaldb->single_company_db($comp_code);

    	$table = $comp_id.'_acctmaster_'.$comp_id;
  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['acc_id'];

  			$sub_table = $comp_id.'_accnttxnnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('UPDATE ' . $sub_table . ' SET bo_id = "1"');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}
  		}

  		$sub_table = $comp_id.'_accoppybal_'.$comp_id;
        try{
        	$external_db->query('UPDATE ' . $sub_table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		$table = $comp_id.'_accttxnoth_'.$comp_id;
  		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$table = $comp_id.'_acctcrsref_'.$comp_id;
  		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
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
	        	$external_db->query('UPDATE ' . $sub_table . ' SET bo_id = "1"');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}

  		}

  		$table = $comp_id.'_itemtxnoth_'.$comp_id;

  		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$table = $comp_id.'_itmoppybal_'.$comp_id;

  		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$table = $comp_id.'_itmoppyval_'.$comp_id;

  		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		//--------------------

  		$table = $comp_id.'_billmaster_'.$comp_id;

  		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$sub_table = $comp_id.'_billstxnnn_'.$comp_id;
        try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}
  		

  		//--------------------

  		$table = $comp_id.'_costctmstr_'.$comp_id;

  		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		$sub_table = $comp_id.'_costcttxnn_'.$comp_id;
        try{
        	$external_db->query('UPDATE ' . $sub_table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

		//--------------------
		
		$table = $comp_id.'_billsundry_'.$comp_id;

		try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
		}

  		$result_ = $external_db->table($table)->get()->getResultArray();

  		foreach ($result_ as $key_ => $value_) {
  			$acc_id = $value_['bill_sundry_id'];

  			$sub_table = $comp_id.'_sundrytxnn_'.$acc_id.'_'.$comp_id;
	        try{
	        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
	        }
	        catch (\Exception $e) {
			   echo $e->getMessage();
			}
  		}

  		$sub_table = $comp_id.'_bsdoppybal_'.$comp_id;
        try{
        	$external_db->query('UPDATE ' . $table . ' SET bo_id = "1"');
        }
        catch (\Exception $e) {
		   echo $e->getMessage();
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

		$account_grp_tbl = $comp_id.'_acctgroupn_'.$comp_id;
		try{
			$group = $external_db->table($account_grp_tbl)
							->where('acc_grp_name', "Main")
							->where('acc_grp_primary', 'Y')
							->where('acc_grp_parent_id', 14)
							->get()->getRowArray();
			if($group){
				$bo_acc_grpp_id = $group['acc_grp_id'];
			}
			else{
				$external_db->query('INSERT INTO '.$account_grp_tbl.' (comp_id, acc_grp_name, acc_grp_alias, acc_grp_primary, acc_grp_parent_id, under_acc_grp_id, under_main_grp_id, restrictions) VALUES ("'.$comp_id.'", "Main", "Main", "Y", "14", "0", "0", "");');		
		  		$bo_acc_grpp_id = $external_db->insertID();
			}

			$external_db->query("TRUNCATE TABLE ${hobomaster_tbl};");
			$external_db->query('INSERT INTO '.$hobomaster_tbl.' (comp_id,bo_name,bo_alias,acc_grp_id,bo_add1,bo_add2,bo_city,bo_state,bo_country,bo_pin) VALUES ("'.$comp_id.'","HO", "HO", "'.$bo_acc_grpp_id.'","","","","","","");');

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