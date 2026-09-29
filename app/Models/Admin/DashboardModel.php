<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class DashboardModel extends Model	{
	 public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->dbextr        =  $this->externaldb->get_company_db();
	   $this->dberpunvrsl   =  $this->externaldb->erp_db();
	   $this->session       =  \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->user_id       =  $this->session->get('uuid_aicountly');
	   $this->enc_string    =  new enc_string();
    }
	
  function shared_companies_list(){
	   $uuid =  $this->session->get('uuid');
	   $uuid_aicountly = 	 $this->session->get('uuid_aicountly');
	   $records = array();
	   
       $builder = $this->dberpunvrsl->table("aictlyerp_compidgenr_univdb");
       $builder->select('aictlyerp_compidgenr_univdb.uuid_aicountly, "owner" as "company_type" ');
       $builder->join('aictlyerp_compmastern_univdb', 'aictlyerp_compidgenr_univdb.comp_id = aictlyerp_compmastern_univdb.comp_id');
       $builder->select('aictlyerp_compmastern_univdb.comp_id, aictlyerp_compmastern_univdb.comp_name, aictlyerp_compmastern_univdb.comp_short_name, aictlyerp_compmastern_univdb.comp_code, aictlyerp_compmastern_univdb.fy_begndt');
       $builder->where('aictlyerp_compidgenr_univdb.uuid',$uuid);
       $builder->where('aictlyerp_compmastern_univdb.comp_db_status','active');
       $builder->where('aictlyerp_compidgenr_univdb.uuid_aicountly',$uuid_aicountly);
       $builder->orderBy('comp_code','DESC');
       $result = $builder->get()->getResultArray();
        if(!empty($result)){
          foreach($result as $values){
          $records[] = array(
                      'encomp_id'       =>  obfuscate_link($values['comp_id']),
                      'uuid_aicountly'   => $values['uuid_aicountly'],
                      'company_type'     => 'owner',
		              'label'            => $values['comp_name'],
					  'value'            => $values['comp_name'],
					  'company_code'     => $values['comp_code'],
					   );    
 	       
		  } 	       
        }
        
       $builder = $this->dberpunvrsl->table("aictlyerp_cmpidacsnn_univdb");
       $builder->select('aictlyerp_cmpidacsnn_univdb.uuid_aicountly, "shared" as "company_type" ');
       $builder->join('aictlyerp_compmastern_univdb', 'aictlyerp_cmpidacsnn_univdb.comp_id = aictlyerp_compmastern_univdb.comp_id');
       $builder->select('aictlyerp_compmastern_univdb.comp_id, aictlyerp_compmastern_univdb.comp_name, aictlyerp_compmastern_univdb.comp_short_name, aictlyerp_compmastern_univdb.comp_code, aictlyerp_compmastern_univdb.fy_begndt');
       $builder->where('aictlyerp_cmpidacsnn_univdb.uuid_access',$uuid_aicountly);
       $builder->where('aictlyerp_compmastern_univdb.comp_db_status','active');
       $builder->orderBy('comp_code','DESC');
       $result = $builder->get()->getResultArray();
        if(!empty($result)){
            foreach($result as $values){
            $records[] = array(
                      'encomp_id'        => obfuscate_link($values['comp_id']),
                      'uuid_aicountly'   => $values['uuid_aicountly'],
                      'company_type'     => 'shared',		             
					  'label'            => $values['comp_name'],
					  'value'            => $values['comp_name'],
					  'company_code'     => $values['comp_code'],
					   );     
 	       
		  } 	       
        }
        
		return $records; 
	   
   }	
	function getcompany_info($company_id){	 
	  return $this->dberpunvrsl->table('aictlyerp_compmastern_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
   }
   
   function get_company_info($company_id){	 
	  return $this->dberpunvrsl->table('aictlyerp_compmastern_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
   }
 
   function UpdateMasterGroup($data,$id){	 
	  return $this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb')->where('crs_master_id', $id)->update($data);   	   
   }
    function RemoveMappingGroup($id){	 
	  return $this->dberpunvrsl->table('aictlyerp_grpmapping_univdb')->where('grpmpid', $id)->delete();   	   
   }
     function RemoveMaster($id){	 
	  return $this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb')->where('crs_master_id', $id)->delete();   	   
   }
   
   function isgroupmapped($crs_master_id,$master_type){
	 return $this->dberpunvrsl->table('aictlyerp_grpmapping_univdb')->where('master_type', $master_type)->where('crs_master_id', $crs_master_id)->get()->getRowArray();   	     
	   
   }
   function InsertMapGroupWithCompany($data){
	 return $this->dberpunvrsl->table('aictlyerp_grpmapping_univdb')->insert($data);   	     
	   
   }
   
 public function MasterCriteriaLists($crs_master_type){	
     $builder = $this->dberpunvrsl->table('aictlyerp_grpmasternn_univdb');
	 $builder->select('aictlyerp_grpcomstid_univdb.comp_id,aictlyerp_grpcomstid_univdb.crs_master_id,aictlyerp_grpcomstid_univdb.crs_master_name');
	 $builder->join('aictlyerp_grpcomstid_univdb','aictlyerp_grpcomstid_univdb.grpco_id=aictlyerp_grpmasternn_univdb.grpco_id');
	 $builder->where('aictlyerp_grpmasternn_univdb.uuid',$this->session->get('uuid'));	 
	 $builder->where('aictlyerp_grpcomstid_univdb.crs_master_type',$crs_master_type);
	 $builder->orderBy('aictlyerp_grpcomstid_univdb.crs_master_id');
	 $response =  $builder->get()->getResultArray(); 
	 
	return $response; 
 }
 public function groupmst_info($crs_master_id,$crs_master_type){
	return $this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb')->where('crs_master_type', $crs_master_type)->where('crs_master_id', $crs_master_id)->get()->getRowArray();   	    
 }

 public function member_master_info($master_id,$comp_id,$master_type){
	 $company_info   = $this->get_company_info($comp_id);
     $external_db      = $this->externaldb->single_company_db($company_info['comp_code']);
	 $member_company_name = 	$company_info['comp_name'];  
	 if($master_type=='acc'){		  
		/********************************  Accounts Master Data *************************/	
		  $acctmaster_tbl   = $company_info['comp_id'].'_acctmaster_'.$company_info['comp_fy_id'];		  
		  $acctmasterresult =  $external_db->query("SELECT acc_id ,acc_name FROM `".$acctmaster_tbl."` WHERE `acc_id`='".$master_id."' order by `acc_name`  ");
		  $account_data     = $acctmasterresult->getRowArray();	
		  $master_name      = $account_data['acc_name'];
		  $member_master_id = $account_data['acc_id'];
		  }
	if($master_type=='accgrp'){		  
		/********************************  Accounts Group Data *************************/	
		 $acctgroupn_tbl     = $company_info['comp_id'].'_acctgroupn_'.$company_info['comp_fy_id'];		 
		  $acctgroupnresult  = $external_db->query("SELECT acc_grp_id ,acc_grp_name FROM `".$acctgroupn_tbl."` WHERE `acc_grp_id`='".$master_id."' order by `acc_grp_name`  ");
		  $acctgroupn_data   = $acctgroupnresult->getRowArray();
		  $master_name       = $acctgroupn_data['acc_grp_name'];
		  $member_master_id  = $acctgroupn_data['acc_grp_id'];
	 } 
	 if($master_type=='itm'){		  
		/********************************  Item Data *************************/	
		  $itemmaster_tbl   = $company_info['comp_id'].'_itemmaster_'.$company_info['comp_fy_id'];		
		  $itmmasterresult  =  $external_db->query("SELECT item_id ,item_name FROM `".$itemmaster_tbl."`  WHERE `item_id`='".$master_id."' order by `item_name`  ");
		  $items_data       = $itmmasterresult->getRowArray();
		  $master_name      = $items_data['item_name'];
		  $member_master_id  = $items_data['item_id'];
	 } 
	 
	if($master_type=='itmgrp'){		  
		/********************************  Item Group Data *************************/	
		  $itemgrpmst_tbl    = $company_info['comp_id'].'_itemgrpmst_'.$company_info['comp_fy_id'];		 
		  $itemgrpmstresult  = $external_db->query("SELECT item_grp_id ,item_grp_name FROM `".$itemgrpmst_tbl."` WHERE `item_grp_id`='".$master_id."' order by `item_grp_name`  ");
		  $itemgrpmst_data   = $itemgrpmstresult->getRowArray();	
		  $master_name       = $itemgrpmst_data['item_grp_name'];
		  $member_master_id  = $itemgrpmst_data['item_grp_id'];
	 }  	 
	
	if($master_type=='itemcat'){		  
		/********************************  Stock Category Data *************************/	
		  $itemcatmst_tbl   = $company_info['comp_id'].'_itemcatmst_'.$company_info['comp_fy_id'];		 
		  $itemcatmstresult  =  $external_db->query("SELECT icatgms_id ,item_cat FROM `".$itemcatmst_tbl."` WHERE `icatgms_id`='".$master_id."' order by `item_cat`  ");
		  $itemcatmst_data       = $itemcatmstresult->getRowArray();	
		  $master_name       = $itemcatmst_data['item_cat'];
		  $member_master_id  = $itemcatmst_data['icatgms_id'];
	 }  
	
    if($master_type=='mcmst'){		  
		/********************************  Material Centre Data *************************/	
		 $mcmasternn_tbl     = $company_info['comp_id'].'_mcmasternn_'.$company_info['comp_fy_id'];		 
		  $mcmasternnresult  = $external_db->query("SELECT mat_cent_id ,mat_cent_name FROM `".$mcmasternn_tbl."`  WHERE `mat_cent_id`='".$master_id."' order by `mat_cent_name`  ");
		  $mcmasternn_data   = $mcmasternnresult->getRowArray();		
		  $master_name       = $mcmasternn_data['mat_cent_name'];
		  $member_master_id  = $mcmasternn_data['mat_cent_id'];
	 } 
	 
	if($master_type=='mcgrp'){		  
		/********************************  Material Centre Group Data *************************/	
		 $mcgrpmstnn_tbl   = $company_info['comp_id'].'_mcgrpmstnn_'.$company_info['comp_fy_id'];		 
		  $mcgrpmstnnresult  =  $external_db->query("SELECT mc_grp_id ,mc_grp_name FROM `".$mcgrpmstnn_tbl."`  WHERE `mc_grp_id`='".$master_id."' order by `mc_grp_name`  ");
		  $mcgrpmstnn_data       = $mcgrpmstnnresult->getResultArray();		
		  $master_name = $mcgrpmstnn_data['mc_grp_name'];
		  $member_master_id  = $mcgrpmstnn_data['mc_grp_id'];
	 } 
	 
	 if($master_type=='costct'){		  
		/********************************  Cost Centre Data *************************/	
		 $costctmstr_tbl   = $company_info['comp_id'].'_costctmstr_'.$company_info['comp_fy_id'];		 
		  $costctmstrresult  =  $external_db->query("SELECT cc_id ,cc_name FROM `".$costctmstr_tbl."` WHERE `cc_id`='".$master_id."' order by `cc_name`  ");
		  $costctmst_data       = $costctmstrresult->getResultArray();		
		  $master_name       = $costctmst_data['cc_name'];
		  $member_master_id  = $costctmst_data['cc_id'];
	 } 
	 
	  if($master_type=='costctgrp'){		  
		/********************************  Cost Centre Group Data *************************/	
		  $costctgrup_tbl   = $company_info['comp_id'].'_costctgrup_'.$company_info['comp_fy_id'];		 
		  $costctgrupresult  =  $external_db->query("SELECT cc_grp_id ,cc_grp_name FROM `".$costctgrup_tbl."` WHERE `cc_grp_id`='".$master_id."' order by `cc_grp_name`  ");
		  $costctgrup_data    = $costctgrupresult->getResultArray();		
		  $master_name       = $costctgrup_data['cc_grp_name'];
		  $member_master_id  = $costctgrup_data['cc_grp_id'];
	 } 
	 $response = array('member_company_master_name'=>$master_name,'member_company_name'=>$member_company_name,
	                   'member_company_master_id'=>$member_master_id);
	 return $response;	
 }
 
 public function member_masterid_info($master_name,$comp_id,$master_type){
	 $master_name    = strtolower($master_name); 
	 $company_info   = $this->get_company_info($comp_id);
     $external_db      = $this->externaldb->single_company_db($company_info['comp_code']);
	 $member_company_name = 	$company_info['comp_name'];  
	 if($master_type=='acc'){		  
		/********************************  Accounts Master Data *************************/	
		  $acctmaster_tbl   = $company_info['comp_id'].'_acctmaster_'.$company_info['comp_fy_id'];		  
		  $acctmasterresult =  $external_db->query("SELECT acc_id ,acc_name FROM `".$acctmaster_tbl."` WHERE LOWER(`acc_name`)='".$master_name."' order by `acc_name`  ");
		  $account_data     = $acctmasterresult->getRowArray();	
		  $master_name      = $account_data['acc_name'];
		  $member_master_id = $account_data['acc_id'];
		  }
	if($master_type=='accgrp'){		  
		/********************************  Accounts Group Data *************************/	
		 $acctgroupn_tbl     = $company_info['comp_id'].'_acctgroupn_'.$company_info['comp_fy_id'];		 
		  $acctgroupnresult  = $external_db->query("SELECT acc_grp_id ,acc_grp_name FROM `".$acctgroupn_tbl."` WHERE LOWER(`acc_grp_name`)='".$master_name."' order by `acc_grp_name`  ");
		  $acctgroupn_data   = $acctgroupnresult->getRowArray();
		  $master_name       = $acctgroupn_data['acc_grp_name'];
		  $member_master_id  = $acctgroupn_data['acc_grp_id'];
	 } 
	 if($master_type=='itm'){		  
		/********************************  Item Data *************************/	
		  $itemmaster_tbl   = $company_info['comp_id'].'_itemmaster_'.$company_info['comp_fy_id'];		
		  $itmmasterresult  =  $external_db->query("SELECT item_id ,item_name FROM `".$itemmaster_tbl."`  WHERE LOWER(`item_name`)='".$master_name."' order by `item_name`  ");
		  $items_data       = $itmmasterresult->getRowArray();
		  $master_name      = $items_data['item_name'];
		  $member_master_id  = $items_data['item_id'];
	 } 
	 
	if($master_type=='itmgrp'){		  
		/********************************  Item Group Data *************************/	
		  $itemgrpmst_tbl    = $company_info['comp_id'].'_itemgrpmst_'.$company_info['comp_fy_id'];		 
		  $itemgrpmstresult  = $external_db->query("SELECT item_grp_id ,item_grp_name FROM `".$itemgrpmst_tbl."` WHERE LOWER(`item_grp_name`)='".$master_name."'  order by `item_grp_name`  ");
		  $itemgrpmst_data   = $itemgrpmstresult->getRowArray();	
		  $master_name       = $itemgrpmst_data['item_grp_name'];
		  $member_master_id  = $itemgrpmst_data['item_grp_id'];
	 }  	 
	
	if($master_type=='itemcat'){		  
		/********************************  Stock Category Data *************************/	
		  $itemcatmst_tbl   = $company_info['comp_id'].'_itemcatmst_'.$company_info['comp_fy_id'];		 
		  $itemcatmstresult  =  $external_db->query("SELECT icatgms_id ,item_cat FROM `".$itemcatmst_tbl."` WHERE LOWER(`item_cat`)='".$master_name."' order by `item_cat`  ");
		  $itemcatmst_data       = $itemcatmstresult->getRowArray();	
		  $master_name       = $itemcatmst_data['item_cat'];
		  $member_master_id  = $itemcatmst_data['icatgms_id'];
	 }  
	
    if($master_type=='mcmst'){		  
		/********************************  Material Centre Data *************************/	
		 $mcmasternn_tbl     = $company_info['comp_id'].'_mcmasternn_'.$company_info['comp_fy_id'];		 
		  $mcmasternnresult  = $external_db->query("SELECT mat_cent_id ,mat_cent_name FROM `".$mcmasternn_tbl."`  WHERE LOWER(`mat_cent_name`)='".$master_name."' order by `mat_cent_name`  ");
		  $mcmasternn_data   = $mcmasternnresult->getRowArray();		
		  $master_name       = $mcmasternn_data['mat_cent_name'];
		  $member_master_id  = $mcmasternn_data['mat_cent_id'];
	 } 
	 
	if($master_type=='mcgrp'){		  
		/********************************  Material Centre Group Data *************************/	
		 $mcgrpmstnn_tbl   = $company_info['comp_id'].'_mcgrpmstnn_'.$company_info['comp_fy_id'];		 
		  $mcgrpmstnnresult  =  $external_db->query("SELECT mc_grp_id ,mc_grp_name FROM `".$mcgrpmstnn_tbl."`  WHERE LOWER(`mc_grp_name`)='".$master_name."' order by `mc_grp_name`  ");
		  $mcgrpmstnn_data       = $mcgrpmstnnresult->getResultArray();		
		  $master_name = $mcgrpmstnn_data['mc_grp_name'];
		  $member_master_id  = $mcgrpmstnn_data['mc_grp_id'];
	 } 
	 
	 if($master_type=='costct'){		  
		/********************************  Cost Centre Data *************************/	
		 $costctmstr_tbl   = $company_info['comp_id'].'_costctmstr_'.$company_info['comp_fy_id'];		 
		  $costctmstrresult  =  $external_db->query("SELECT cc_id ,cc_name FROM `".$costctmstr_tbl."` WHERE LOWER(`cc_name`)='".$master_name."' order by `cc_name`  ");
		  $costctmst_data       = $costctmstrresult->getResultArray();		
		  $master_name       = $costctmst_data['cc_name'];
		  $member_master_id  = $costctmst_data['cc_id'];
	 } 
	 
	  if($master_type=='costctgrp'){		  
		/********************************  Cost Centre Group Data *************************/	
		  $costctgrup_tbl   = $company_info['comp_id'].'_costctgrup_'.$company_info['comp_fy_id'];		 
		  $costctgrupresult  =  $external_db->query("SELECT cc_grp_id ,cc_grp_name FROM `".$costctgrup_tbl."` WHERE LOWER(`cc_grp_name`)='".$master_name."' order by `cc_grp_name`  ");
		  $costctgrup_data    = $costctgrupresult->getResultArray();		
		  $master_name       = $costctgrup_data['cc_grp_name'];
		  $member_master_id  = $costctgrup_data['cc_grp_id'];
	 } 
	 $response = array('member_company_master_name'=>$master_name,'member_company_name'=>$member_company_name,
	                   'member_company_master_id'=>$member_master_id);
	 return $response;	
 }
 
 public function MasterMappedData($crs_master_type){	
     $builder = $this->dberpunvrsl->table('aictlyerp_grpmapping_univdb');
	 $builder->where('aictlyerp_grpmapping_univdb.master_type',$crs_master_type);
	 $builder->orderBy('aictlyerp_grpmapping_univdb.crs_master_id');
	 $response =  $builder->get()->getResultArray(); 
	 $all_master_sel = array();
	 $key=0;
	 if($response){
		foreach($response as $row){
			
			$crs_master_id   = $row['crs_master_id'];
			$master_type     = $row['master_type'];
			$master_id       = $row['master_id'];
			$comp_id         = $row['comp_id']; 
			$grpmpid         = $row['grpmpid'];
			
			$master_info     = $this->groupmst_info($crs_master_id,$master_type);
			$member_master_info     = $this->member_master_info($master_id,$comp_id,$master_type);
			
			$group_company_master_name = ucwords($master_info['crs_master_name']);
			$all_master_sel[]=array("master_id"=>$row['crs_master_id'],
		                        "group_company_master_name"=>$group_company_master_name,
		                        "member_company_master_name"=>$member_master_info['member_company_master_name'],
								"member_company_name"=>$member_master_info['member_company_name'],
								"action"=>'<a href="javascript:void(0);" onClick="update_group_master_row(\''.$key.'\',\''.$grpmpid.'\',\''.$crs_master_id.'\');"><img src="'.base_url().'/public/assets/img/icon-done.png"></a>&nbsp;<a href="javascript:void(0);" onClick="remove_group_master_row(\''.$key.'\',\''.$grpmpid.'\',\''.$crs_master_id.'\');"><img src="'.base_url().'/public/assets/img/icon-close.png"></a>');
		$key++;	
		} 
		 
	 }
	return $all_master_sel; 
 }
 public function group_companies_list(){
	 $builder = $this->dberpunvrsl->table('aictlyerp_grpmasternn_univdb');
	 $builder->join('aictlyerp_grpcompacs_univdb','aictlyerp_grpcompacs_univdb.grpco_id=aictlyerp_grpmasternn_univdb.grpco_id');
	 $builder->where('aictlyerp_grpmasternn_univdb.uuid',$this->session->get('uuid'));	
	 $response =  $builder->get()->getResultArray();
	 $final_companies=array();	 
	 if($response){
		 foreach($response as $row){
			 $comp_id = $row['comp_id'];
			 $companyinfo = $this->getcompany_info($comp_id);
					$final_companies[]= array(
					  'comp_id' => $comp_id,
                      'label'   => $companyinfo['comp_name'],
					  'value'   => $companyinfo['comp_name'],					  
					  );
			 
		 }
		 
	 }
	 return $final_companies;
 }
 public function MasterUnMappedData($crs_master_type){	
  // if comp_id empty then show in unmapped group master , if any record exists in comp_id then it will show in unmapped member master view
     $builder = $this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb');
	 $builder->where('aictlyerp_grpcomstid_univdb.crs_master_type',$crs_master_type);
	 $builder->where('aictlyerp_grpcomstid_univdb.comp_id','');
	 $builder->orderBy('aictlyerp_grpcomstid_univdb.crs_master_id');
	 $response =  $builder->get()->getResultArray(); 
	 $all_master_sel = array();
	 $key=0;
	 $final_companies= array();
	 if($response){
		foreach($response as $row){
			
			$crs_master_id   = $row['crs_master_id'];
			$master_type     = $row['crs_master_type'];
			$grpco_id     = $row['grpco_id'];
			$crs_master_name = ucwords($row['crs_master_name']);
			$comp_ids        = ltrim($row['comp_id'],",");
			$comp_id         = rtrim($comp_ids,",");
			$final_companies=array();
			if($comp_id){
				$comp_idx = explode(",",$comp_id);
				foreach($comp_idx as $comp_id){
					
					
				   }
				
			}
			
			// check crs_master_id not exists in aictlyerp_grpmapping_univdb then fetch records as unmapped 
			//$isgroupmapped   = $this->isgroupmapped($crs_master_id,$master_type);
				
			$all_master_sel[]=array("master_id"=>$row['crs_master_id'],
		                        "group_company_master_name"=>$crs_master_name,"master_type"=>$master_type,
		                        "member_company_master_name"=>"","grpco_id"=>$grpco_id,
								"member_company_name"=>"","final_companies"=>$final_companies,
								"action"=>'<a href="javascript:void(0);" onClick="map_group_master_row(\''.$key.'\',\''.$crs_master_id.'\');"><img src="'.base_url().'/public/assets/img/icon-done.png"></a>&nbsp;<a href="javascript:void(0);" onClick="remove_master_row(\''.$key.'\',\''.$crs_master_id.'\');"><img src="'.base_url().'/public/assets/img/icon-close.png"></a>');	
			$key++;	
			
			
		} 
		 
	 }
	return $all_master_sel; 
 }
 
 
 public function set_current_tab($sel_tab_id){    
      $this->session->set('s_ctab',$sel_tab_id);
	  return TRUE;
 }
 
  public function add_tab($data){
 
    $menu_code ='100';
    $ses_menu_item  = $this->session->get('menu_item');
    
    $itemArray = array(time()=>array('name'=>'Dashboard','url'=>base_url().'?menuid='.time(),'quantity'=>'1','menuid'=>time()));
       if(!empty($ses_menu_item)) {
			if(in_array($menu_code,array_keys($ses_menu_item))) {
				foreach($ses_menu_item as $k => $v) {
					$ses_menu_item[$k]["quantity"] = 1;
				}
			} else {
				$this->session->set('menu_item', array_merge($ses_menu_item,$itemArray));
			}
		} else {
		    $this->session->set('menu_item', $itemArray);
		}
		
		$ses_menu_item  = $this->session->get('menu_item');
		$last_item      = end($ses_menu_item);		
	    $sel_menuid     = $last_item['menuid'];	
	
	    $this->session->set('s_ctab',$sel_menuid);
    }
   

   public function remove_tab($sel_tab_id){
	    if($this->session->get('menu_item'))	   
       $ses_menu_item    = $this->session->get('menu_item'); 
     else
		 $ses_menu_item    =array();
       
	 
	   
	   $search_tab       = getPrevKey($sel_tab_id, $ses_menu_item);	 
       if(isset($search_tab[0]))  
		 $last_tab_key =  $search_tab[0]-1;
       else
        $last_tab_key  =0;		   
	  
       if(!empty($ses_menu_item)) {
		foreach($ses_menu_item as $k => $v) {
			if($sel_tab_id == $v['menuid'])
				unset($ses_menu_item[$k]);				
			
	    	}
       }
	 
	    if(!empty($ses_menu_item))  {     
        $this->session->set('menu_item', $ses_menu_item);	
		
        $all_menus = $this->session->get('menu_item');
		$tabs_listings ='';
		$sel_active ='';
		
		if(isset($ses_menu_item[$last_tab_key])){
		$last_item     = $ses_menu_item[$last_tab_key];	
        $last_item_url  = $ses_menu_item[$last_tab_key]['url'];	
		}else{
		$last_item     = end($ses_menu_item);	
        $last_item_url  = end($ses_menu_item)['url'];		
			
		}
		
		$seltabid = $last_item['menuid'];
		
		$this->session->set('s_ctab',$seltabid);
		return $last_item_url;
	   }
		else
			return base_url().'/admin/dashboard';
		
	 }   
	 
  
   function get_group_all_accounts($group_ids){
	   if(count($group_ids) >0){
	    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder =  $this->dbextr->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name','acc_op_bal','acc_grp_id'));
        $builder->where('comp_id', $this->company_id);		
		$builder->whereIn('acc_grp_id', $group_ids);
		$result = $builder->get()->getResultArray();
	    return $result;
	   }
	   else
		   return array();
   }	 
	 
  public function profitloss_chart_values(){
    //  $fyid = $this->session->get('ses_comp_fy_id');
    //  if($fyid==0)
    //   return array();
     
    //   $start_date  = date("Y-01-01");
    //   $end_date    = date("Y-m-d");
    //   $company_id  = $this->company_id;
    //  // Get all groups sub groups ids 
    //  $parent_groups = array("8","12","9","11","14","13");
    //  $all_group_ids = array();
    //  $account_grp_tbl = $company_id.'_acctgroupn_'.$fyid;
    //  foreach($parent_groups as $parent_id){
    //     $sql =$this->dbextr->query("select `acc_grp_id`, `under_acc_grp_id`, `acc_grp_name` from (select * from `".$account_grp_tbl."` order by `under_acc_grp_id`, `acc_grp_id`) `".$account_grp_tbl."` , (select @pv := '".$parent_id."') initialisation where find_in_set(`under_acc_grp_id`, @pv) > 0 and @pv := concat(@pv, ',', `acc_grp_id`);");
    //     $result = $sql->getResultArray();
    //     foreach($result as $row)
    //       {
    //        $all_group_ids[$row['acc_grp_id']]= $row['acc_grp_id'];
            
    //       }
    //   }
    //  $final_months = array();
    //  //Get accounts of all groups
    // $all_accounts = $this->get_group_all_accounts($all_group_ids); 
    // $final_sum=0;
    // foreach($all_accounts as $key => $accrow){
       
    //    $account_table_name = $company_id.'_accnttxnnn_'.$accrow['acc_id'].'_'.$fyid;
    //    $sql     =  $this->dbextr->query("SELECT * FROM `".$account_table_name."` WHERE `acc_txn_date` >='".$start_date."' AND `acc_txn_date`<='".$end_date."' AND `acc_id`='".$accrow['acc_id']."' AND `comp_id`='".$this->company_id."'  ");
    //    $result  =  $sql->getResultArray();
    //    foreach($result as $row){
    //       $month_name =  date('m',strtotime($row['acc_txn_date']));
    //       $final_sum  =  $final_sum+$row['acc_bal'];
     
    //      if($final_sum<0)
    //        $final_months[$month_name]  = str_replace("-",'',$final_sum);
    //      else
    //       $final_months[$month_name]   ='-'.$final_sum;
    //      }
        
    //    } 
    
   
    // $final_amount = array();  
    //  for ($i = 1; $i <= date('m'); $i++){
    //       if($i<10)
    //        $i='0'.$i;
           
    //     if(array_key_exists($i,$final_months))
    //      $final_amount[$i]=$final_months[$i];
    //      else
    //      $final_amount[$i]=0;
         
    //  }
    
     return [];//$final_amount;  
      
  }  
	 
   public function revenue_chart_values(){
    //   $fyid = $this->session->get('ses_comp_fy_id');
    //    if($fyid==0)
    //   return array();
    //   $start_date  = date("Y-01-01");
    //   $end_date    = date("Y-m-d");
    //  $company_id = $this->company_id;
    //  // Get all groups sub groups ids 
    //  $parent_groups = array("9","11","13");
    //  $all_group_ids = array();
    //   $account_grp_tbl = $company_id.'_acctgroupn_'.$fyid;
      
    //  foreach($parent_groups as $parent_id){
    //     $sql =$this->dbextr->query("select `acc_grp_id`, `under_acc_grp_id`, `acc_grp_name` from (select * from `".$account_grp_tbl."` order by `under_acc_grp_id`, `acc_grp_id`) `".$account_grp_tbl."` , (select @pv := '".$parent_id."') initialisation where find_in_set(`under_acc_grp_id`, @pv) > 0 and @pv := concat(@pv, ',', `acc_grp_id`);");
    //     $result = $sql->getResultArray();
    //     foreach($result as $row)
    //       {
    //        $all_group_ids[$row['acc_grp_id']]= $row['acc_grp_id'];
            
    //       }
    //   }
    //  $final_months = array();
    //  //Get accounts of all groups
    // $all_accounts = $this->get_group_all_accounts($all_group_ids); 
 
    // $final_sum=0;
    // foreach($all_accounts as $key => $accrow){
       
       
    //    $account_table_name = $company_id.'_accnttxnnn_'.$accrow['acc_id'].'_'.$fyid;
       
    //    $sql =  $this->dbextr->query("SELECT * FROM `".$account_table_name."` WHERE `acc_txn_date` >='".$start_date."' AND `acc_txn_date`<='".$end_date."' AND `acc_id`='".$accrow['acc_id']."' AND `comp_id`='".$this->company_id."'  ");
    //    $result = $sql->getResultArray();
    //    foreach($result as $row){
           
    //       $month_name =  date('m',strtotime($row['acc_txn_date']));
           
    //       $final_sum = $final_sum+$row['acc_bal'];
       
    //     if($final_sum<0)
    //        $final_months[$month_name]  = str_replace("-",'',$final_sum);
    //      else
    //       $final_months[$month_name]   ='-'.$final_sum;
    //      }
        
    //    } 
     
    //  $final_amount = array();  
    //  for ($i = 1; $i <= date('m'); $i++){
    //      if($i<10)
    //        $i='0'.$i;
    //      $show_month_name = date("F",$i);
    //     if(array_key_exists($i,$final_months))
    //      $final_amount[$i]=$final_months[$i];
    //      else
    //      $final_amount[$i]='0';
         
    //  }
    
     return [];//$final_amount; 
      
  }

  public function markBranch($id, $comp_id)
  {
	  $builder = $this->dberpunvrsl->table("aictlyerp_unverppref_univdb");
  
	  // Check if entry exists for this company and config ID
	  $existing = $builder
				  ->where('defusr_config_id', 375)
				  ->where('comp_id', $comp_id)
				  ->get()
				  ->getRow();
  
	  if ($existing) {
		  // Update the branch ID
		  $builder
			  ->where('defusr_config_id', 375)
			  ->where('comp_id', $comp_id)
			  ->update(['defusr_config_value' => $id]);
  
		  return ['updated' => true, 'bo_id' => $id];
	  }
  
	  // Insert new if not found
	  $insertData = [
		  'defusr_config_id'    => 375,
		  'defusr_config_value' => $id,
		  'comp_id'             => $comp_id
	  ];
  
	  $builder->insert($insertData);
  
	  return ['inserted' => true, 'bo_id' => $id];
  }
  

  public function markFinancial($id, $comp_id)
  {
	  $builder = $this->dberpunvrsl->table("aictlyerp_unverppref_univdb");
  
	  // Check if entry exists for this company and config ID
	  $existing = $builder
				  ->where('defusr_config_id', 374)
				  ->where('comp_id', $comp_id)
				  ->get()
				  ->getRow();
  
	  if ($existing) {
		  // Update the branch ID
		  $builder
			  ->where('defusr_config_id', 374)
			  ->where('comp_id', $comp_id)
			  ->update(['defusr_config_value' => $id]);
  
		  return ['updated' => true, 'bo_id' => $id];
	  }
  
	  // Insert new if not found
	  $insertData = [
		  'defusr_config_id'    => 374,
		  'defusr_config_value' => $id,
		  'comp_id'             => $comp_id
	  ];
  
	  $builder->insert($insertData);
  
	  return ['inserted' => true, 'bo_id' => $id];
  }


public function checkBranch($comp_id)
{
    $builder = $this->dberpunvrsl->table("aictlyerp_unverppref_univdb");

    $result = $builder
                ->select('defusr_config_value')
                ->where('defusr_config_id', 375)
                ->where('comp_id', $comp_id)
                ->get()
                ->getRow();

    return $result ? $result->defusr_config_value : null;
}




}