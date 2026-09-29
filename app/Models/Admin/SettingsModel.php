<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables; 
use App\Libraries\ERPtables;

class SettingsModel extends Model	{

	// Cash & Cash Equivalents		23
	// Bank OC ODD					21
	// Trade Receivables			22 (sundry debitors)
	// Trade Payable				16 (sundry creditors)

	// PURCHASE 			7 (parent_id)
	// DIRECT EXPENSE		11 (parent_id)
	// INDIRECT EXPENSE		13 (parent_id)

    public function __construct() {
        parent::__construct();        
       $this->externaldb       = new externaldb();
	   $this->dberpunvrsl      = $this->externaldb->erp_db();
	   $this->univerpaic_db    = $this->externaldb->univerpaic_db();
	   $this->session          = \Config\Services::session();
	   $this->company_id       = $this->session->get('ses_company_id');
	   $this->enc_string       = new enc_string();
	   $this->aicountly_db     = $this->externaldb->aicountly_db();
	   $this->univaictly       = $this->externaldb->univaictly_db();
	   $this->myaicountly_uuidaictly          = $this->externaldb->postgr_myaicountlydb();
	   $this->bo_id            = $this->session->get('ses_boid');
	   $this->uuid             = $this->session->get('uuid');
    }

    function insert_profile($data){
       $this->univerpaic_db->table("erpacsprof")->insert($data);
       return true;
    }
    
	function ajax_check_profile_rights($url){		 
		 $hasRight  = true;
		 $site_urls = [1=>"admin/accounts/list",3=>"admin/accounts/add",4=>"admin/accounts/modify",5=>"admin/accounts/remove_accounts",
					   6=>"admin/accounts/list_group",7=>"admin/accounts/add_group",8=>"admin/accounts/modify_group",9=>"admin/accounts/remove_groups",
					   10=>'admin/vouchers/invoice/9',11=>'admin/vouchers/invoice/9',14=>'admin/vouchers/edit/9',12=>'admin/vouchers/delete/9',
					   15=>'admin/reports/profit_loss'
					  ];
		 $parsedUrl      = parse_url($url, PHP_URL_PATH);  // gives: /admin/accounts/add
		 $parsedUrl      = ltrim($parsedUrl, '/');         // remove leading slash
		 $serp_config_id = array_search($parsedUrl, $site_urls);				
		
		 $ses_cmp_prf_id =  $this->session->get('ses_cmp_prf_id');  // if NULL or 0 means full rights return 1
		 
		 if($ses_cmp_prf_id>0 && $serp_config_id !== false){
			  $builder = $this->univerpaic_db->table('erprightsn');
			  $builder->join("erpacsprof","erpacsprof.erp_acs_prof_id=erprightsn.erp_acs_prof_id",'left');
			  $builder->join("acsconfmap","acsconfmap.erp_acs_menu_id=erprightsn.erp_acs_menu_id",'left');
			  $builder->join("erprghtmst","erprghtmst.erp_usr_right_menu_id=erprightsn.erp_usr_right_menu_id",'left');
			  $builder->select('erprghtmst.erp_usr_right_type_id,acsconfmap.erp_config_id');
			  $builder->where('erpacsprof.cmp_id',$this->company_id);
			  $builder->where('erprightsn.erp_acs_prof_id',$ses_cmp_prf_id);
			  $builder->where('acsconfmap.erp_config_id',$serp_config_id);
			  $response = $builder->get()->getRowArray();
			 //echo $this->univerpaic_db->getlastquery();
			 // die();
			  
			  if($response){
			    $erp_usr_right_type_id = $response['erp_usr_right_type_id']; 
			    $erp_config_id         = $response['erp_config_id']; 
			    $rightsMatrix = [
								1 => [ // ACCOUNT MASTER PAGE
									1=>0, 2=>1, 3=>0, 4=>1, 5=>1,
								],
								3 => [ // ADD ACCOUNT MASTER
									1=>0, 2=>0, 3=>1, 4=>1, 5=>1,
								],
								4 => [ // EDIT ACCOUNT MASTER
									1=>0, 2=>0, 3=>0, 4=>0, 5=> 1,
								],
								5 => [ // DELETE ACCOUNT MASTER
									1 =>0, 2=>0, 3=>0, 4=>0, 5=>1,
								],										
								6 => [ // ACCOUNT GROUP MASTER PAGE
									1=>0, 2=>1, 3=>0, 4=>1, 5=>1,
								],
								7 => [ // ADD ACCOUNT GROUP MASTER
									1=>0, 2=>0, 3=>1, 4=>1, 5=>1,
								],
								8 => [ // EDIT ACCOUNT GROUP MASTER
									1=>0, 2=>0, 3=>0, 4=>0, 5=> 1,
								],
								9 => [ // DELETE ACCOUNT GROUP MASTER
									1 =>0, 2=>0, 3=>0, 4=>0, 5=>1,
								],
								
								10 => [ // PAYMENT VOUCHER PAGE
							    	1=>0, 2=>1, 3=>0, 4=>1, 5=>1,
								],
								11 => [ // ADD PAYMENT VOUCHER PAGE
							    	1=>0, 2=>1, 3=>0, 4=>1, 5=>1,
								],
								14 => [ // EDIT PAYMENT VOUCHER PAGE
									1=>0, 2=>0, 3=>0, 4=>0, 5=> 1,
								],
								12 => [ // DELETE PAYMENT VOUCHER PAGE
									1 =>0, 2=>0, 3=>0, 4=>0, 5=>1,
								],
								15 => [ // PROFIT & LOSS STATEMENT
									1 =>0, 2=>0, 3=>0, 4=>0, 5=>1,
								],
								
							];
			    $hasRight = !empty($rightsMatrix[$erp_config_id][$erp_usr_right_type_id]) ? true : false;			
					 
			 }
		 }
		 
		 return ['allowed' => $hasRight];
	}
	
    function get_owner_of_company(){
        
        $uuid   =  $this->session->get('uuid');
    	return $this->univaictly->table('cmpacsmstr')
    							->select('uuid_aictly_by')
                                ->where('uuid_acs_type',1)
								->where('cmp_id', $this->company_id) 
								->where('uuid_aictly_by', $uuid) 
                                ->countAllResults();
                                
        
    }
    
    function save_default_profiles_company(){       
            $cmp_id          = $this->company_id;
            $db = $this->univerpaic_db;
            $id_map_profiles = [];
            $id_map_menus    = [];
            $id_map_rights   = [];            
            if ($cmp_id) {                           
                /***************  Deletion *****************/
                // Step 1: get all profile ids for this company
                $builder = $db->table('erpacsprof');
                $builder->select('erp_acs_prof_id');
                $builder->where('cmp_id', $cmp_id);
                $query = $builder->get();
				if ($query) {
				        $profiles = $query->getResultArray();
						$profIds = array_column($profiles, 'erp_acs_prof_id');
						
						if($profIds){

						// Step 2: get all rights for these profiles
						$rights = $db->table('erprightsn')
									 ->select('erp_acs_menu_id, erp_usr_right_menu_id')
									 ->whereIn('erp_acs_prof_id', $profIds)
									 ->get()
									 ->getResultArray();

						if (!empty($rights)) {
							$menuIds = array_filter(array_column($rights, 'erp_acs_menu_id'));
							$usrRightMenuIds = array_filter(array_column($rights, 'erp_usr_right_menu_id'));
							
							// Step 4: delete rights
							$db->table('erprightsn')->whereIn('erp_acs_prof_id', $profIds)->delete();
						}

						// Step 5: delete profiles
						$db->table('erpacsprof')->where('cmp_id', $cmp_id)->delete();
						}
					}
				
				/**********   Insertrecord profile code***********/
				
				// 2. Get default profiles
				$builder = $db->table('erpacsprof');
				$builder->where('cmp_id IS NULL');
                $profiles = $builder->get()->getResultArray();
               if($profiles){
                // Insert profiles & map IDs
                
                foreach ($profiles as $profile) {
                    $old_id = $profile['erp_acs_prof_id'];
                    unset($profile['erp_acs_prof_id']);
                    $profile['cmp_id'] = $cmp_id;
                    
                    
                    $db->table('erpacsprof')->insert($profile);
                    $id_map_profiles[$old_id] = $db->insertID();
                }
            
            
            
                // 3. For each profile, copy rights
                foreach ($id_map_profiles as $old_prof_id => $new_prof_id) {
                    $rights = $db->table('erprightsn')
                                       ->where('erp_acs_prof_id', $old_prof_id)
                                       ->get()
                                       ->getResultArray();
                  if($rights){
                    foreach ($rights as $right) {
                        $old_menu_id = $right['erp_acs_menu_id'];
                        $old_right_menu_id = $right['erp_usr_right_menu_id'];
            
                        // Insert right with new profile id
                        unset($right['erp_rights_id']); // if primary key exists
                        $right['erp_acs_prof_id'] = $new_prof_id;
                        $db->table('erprightsn')->insert($right);
                        
                    }
                     
                  }
                }
                
              //When profiles are reset, remove all company access for users who have already accessed
		     $this->univaictly->table('cmpacsmstr')
                                       ->where('cmp_id', $this->company_id)
                                       ->where('uuid_acs_type',0)
                                       ->delete();
                
            }
             return true;
            }
    }

    public function access_profile_list() {
       $parents = $this->univerpaic_db
                    ->table("erpacsmenu a")
                    ->select("
                        a.erp_acs_menu_id,
                        a.erp_acs_prof_label,
                        a.erp_acs_prof_menu_level,
                        a.erp_acs_prof_menu_parent,
                        t.token_status
                    ")
                    ->join("erpotpaprv t", "t.erp_otp_aprv_id = a.erp_otp_aprv_id", "left")
                    ->where("a.erp_acs_prof_menu_level", 1)
                    ->where("a.erp_acs_prof_type", 2)
                    ->get()
                    ->getResultArray();
    
    
        foreach ($parents as &$parent) {
            // Select only necessary columns for level 2 children
            $children = $this->univerpaic_db
                        ->table("erpacsmenu a")
                        ->select(" a.erp_acs_menu_id,
                        a.erp_acs_prof_label,
                        a.erp_acs_prof_menu_level,
                        a.erp_acs_prof_menu_parent,
                        t.token_status,
                        t.erp_otp_aprv_id,")
                        ->join("erpotpaprv t", "t.erp_otp_aprv_id = a.erp_otp_aprv_id", "left")
                        ->where("erp_acs_prof_menu_level", 2)
                        ->where("a.erp_acs_prof_type", 2)
                        ->where("a.erp_acs_prof_menu_parent", $parent['erp_acs_menu_id'])
                        ->get()
                        ->getResultArray();
    
            $parent['children'] = $children;
        }
    
        return $parents;
    }

    public function back_date_list() {
        $parents = $this->univerpaic_db
                    ->table("erpacsmenu a")
                    ->select("
                        a.erp_acs_menu_id,
                        a.erp_acs_prof_label,
                        a.erp_acs_prof_menu_level,
                        a.erp_acs_prof_menu_parent,
                        t.token_status
                    ")
                    ->join("erpotpaprv t", "t.erp_otp_aprv_id = a.erp_otp_aprv_id", "left")
                    ->where("a.erp_acs_prof_menu_level", 1)
                    ->where("a.erp_acs_prof_type", 1)
                    ->get()
                    ->getResultArray();
    
    
        foreach ($parents as &$parent) {
            // Select only necessary columns for level 2 children
            $children = $this->univerpaic_db
                        ->table("erpacsmenu a")
                        ->select(" a.erp_acs_menu_id,
                        a.erp_acs_prof_label,
                        a.erp_acs_prof_menu_level,
                        a.erp_acs_prof_menu_parent,
                        t.token_status,
                        t.erp_otp_aprv_id,")
                        ->join("erpotpaprv t", "t.erp_otp_aprv_id = a.erp_otp_aprv_id", "left")
                        ->where("erp_acs_prof_menu_level", 2)
                        ->where("a.erp_acs_prof_type", 1)
                        ->where("a.erp_acs_prof_menu_parent", $parent['erp_acs_menu_id'])
                        ->get()
                        ->getResultArray();
    
            $parent['children'] = $children;
        }
      
    
        return $parents;
    }

    public function transaction_list() {
		$parents = $this->univerpaic_db
                    ->table("erpacsmenu a")
                    ->select("
                        a.erp_acs_menu_id,
                        a.erp_acs_prof_label,
                        a.erp_acs_prof_menu_level,
                        a.erp_acs_prof_menu_parent,
                        t.token_status
                    ")
                    ->join("erpotpaprv t", "t.erp_otp_aprv_id = a.erp_otp_aprv_id", "left")
                    ->where("a.erp_acs_prof_menu_level", 1)
                    ->where("a.erp_acs_prof_type", 3)
                    ->get()
                    ->getResultArray();
    
    
        foreach ($parents as &$parent) {
            // Select only necessary columns for level 2 children
            $children = $this->univerpaic_db
                        ->table("erpacsmenu a")
                        ->select(" a.erp_acs_menu_id,
                        a.erp_acs_prof_label,
                        a.erp_acs_prof_menu_level,
                        a.erp_acs_prof_menu_parent,
                        t.token_status,
                        t.erp_otp_aprv_id,")
                        ->join("erpotpaprv t", "t.erp_otp_aprv_id = a.erp_otp_aprv_id", "left")
                        ->where("erp_acs_prof_menu_level", 2)
                        ->where("a.erp_acs_prof_type", 3)
                        ->where("a.erp_acs_prof_menu_parent", $parent['erp_acs_menu_id'])
                        ->get()
                        ->getResultArray();
    
            $parent['children'] = $children;
        }
      
	  
        return $parents;
    }
    
    public function get_back_date_entries($erp_acs_menu_id,$profile_id){
        
        $backdate_entries_val = $this->univerpaic_db
            ->table("erpbdeacsn")
            ->where("erp_acs_menu_id", $erp_acs_menu_id)
            ->where("erp_acs_prof_id", $profile_id)
            ->get()
            ->getResultArray();
			//echo $this->univerpaic_db->getlastquery();
            $saved_backentries=[];
         if($backdate_entries_val){
             foreach($backdate_entries_val as $bckrow){
                 $saved_backentries[$bckrow['vch_series_id']] = $bckrow['erp_bde'];
             }
         }   
         
       return $saved_backentries; 
    }
	public function get_txnaprval_entries($erp_acs_menu_id,$profile_id){
        
        $backdate_entries_val = $this->univerpaic_db
            ->table("erptxnaprv")
            ->where("erp_acs_menu_id", $erp_acs_menu_id)
            ->where("erp_acs_prof_id", $profile_id)
            ->get()
            ->getResultArray();
			//echo $this->univerpaic_db->getlastquery();
			//SaveErrorLog($this->univerpaic_db->getlastquery());
            $saved_txnaprvl=[];
        if($backdate_entries_val){
             foreach($backdate_entries_val as $bckrow){
				 if($bckrow['txn_aprv_min_limit']!=NULL)
					$saved_txnaprvl[$bckrow['txn_aprv_uuid']] = parseAmount($bckrow['txn_aprv_min_limit']);
			     else 
					$saved_txnaprvl[$bckrow['txn_aprv_uuid']] = ''; 	 
             }
        }   
       return $saved_txnaprvl; 
    }
	
	public function get_saved_profile_types($erp_usr_right_menu_id, $erp_acs_menu_id){
    return $this->univerpaic_db
        ->table("erprightsn a")
        ->select("
            a.erp_rights_id,
            a.erp_acs_menu_id,
            a.erp_acs_prof_id,
            a.erp_usr_right_menu_id,
            b.erp_usr_right_type_id
        ")
        ->join("erprghtmst b", "b.erp_usr_right_menu_id = a.erp_usr_right_menu_id AND b.erp_acs_menu_id = a.erp_acs_menu_id", "left")
        ->where("a.erp_acs_menu_id", $erp_acs_menu_id)
        ->where("a.erp_usr_right_menu_id", $erp_usr_right_menu_id)
        ->get()
        ->getRowArray();
	 }
	public function delete_backdateentries($series_id,$erp_acs_prof_id){
		$this->univerpaic_db->table("erpbdeacsn")
			       ->where('vch_series_id',$series_id)
			       ->where('erp_acs_prof_id',$erp_acs_prof_id)
				   ->delete();
	}
	
	public function delete_txnaprvl_entries($txn_aprv_uuid,$erp_acs_menu_id,$erp_acs_prof_id){
		$this->univerpaic_db->table("erptxnaprv")
			       ->where('txn_aprv_uuid',$txn_aprv_uuid)
				   ->where('erp_acs_menu_id',$erp_acs_menu_id)
			       ->where('erp_acs_prof_id',$erp_acs_prof_id)
				   ->delete();
	}		
	 	 
	public function delete_permission($menu_id,$erp_acs_prof_id){
		$builder = $this->univerpaic_db->table('erprightsn r');
		$builder->join('erpacsprof p','p.erp_acs_prof_id = r.erp_acs_prof_id');
		$builder->where('p.cmp_id',$this->company_id);
		$builder->where('r.erp_acs_prof_id',$erp_acs_prof_id);
		$response = $builder->get()->getResultArray();  
		if($response){
			foreach($response as $row){
				$this->univerpaic_db->table("erprightsn")
			       ->where('erp_rights_id',$row['erp_rights_id'])
			       ->where('erp_acs_menu_id',$menu_id)
				   ->delete();
			}
		}
        return true;
         }
		
    public function add_permission($data){
              $this->univerpaic_db->table("erprightsn")->insert($data);
              return true;
    }
	
	public function add_backdate_entries($data){
              $this->univerpaic_db->table("erpbdeacsn")->insert($data);
              return true;
    }
	
	public function add_txnaprval_entries($data){
              $this->univerpaic_db->table("erptxnaprv")->insert($data);
              return true;
     }
	
	function get_erp_usr_right_menu_id($menu_id,$right_type_id){
       return $this->univerpaic_db->table("erprghtmst")->select('erp_usr_right_menu_id')
               ->where("erp_acs_menu_id",$menu_id)
               ->where("erp_usr_right_type_id",$right_type_id)
               ->get()
               ->getRowArray();
    }
	
	public function comp_vchseries_list(){
	    $builder = $this->db->table('vchseriesn vs');
        $builder->select('vs.vch_series_id, vs.vch_series_name, vt.vch_type_id, vt.vch_name');
        $builder->join('vchtypemst vt', 'vs.vch_type_id = vt.vch_type_id');
        $builder->where('vt.vch_type_status', 1); // only active voucher types
		$builder->where('vs.cmp_id', $this->company_id); 
        $builder->orderBy('vt.vch_name, vs.vch_series_name');
        $query = $builder->get();
        $results = $query->getResultArray();       
        $grouped = [];
        foreach ($results as $row) {
            $grouped[$row['vch_series_id']] = $row['vch_series_name'].' ('.$row['vch_name'].')';
        }         
       return $grouped; 	
	}
	
	public function comp_acces_users(){		
		$result =  $this->univaictly->table('cmpacsmstr')
    							->select('cmp_acs_id,uuid_aictly_acs,uuid_aictly_by,erp_acs_prof_id')
                                ->where('uuid_acs_type',0)
								->where('cmp_id', $this->company_id)                                
                                ->get()->getResultArray();
		$users  = [];
        foreach($result as $key => $value) {
			$uuid_access = $value['uuid_aictly_acs'];
			$result2     = $this->myaicountly_uuidaictly->table('useraictly')
        						->select('user_firstname, user_lastname, useraictly_status, uuid, user_regdemail')
                                ->where('uuid', $uuid_access)
                                ->get()->getRowArray();	
										
			$user_name   = $result2['user_firstname'].' '.$result2['user_lastname'];				
			$users[$uuid_access] ='<h6 class="text-black">'. $user_name.'</h6><em>'.$result2['user_regdemail'].'</em>';		
		}
       return $users; 
	}
	
    public function ajax_major_level_3($id,$profile_id) {
        $response =  $this->univerpaic_db
        ->table("erpacsmenu a")
        ->select("
            a.erp_acs_menu_id, 
            a.erp_acs_prof_type, 
            a.erp_acs_prof_label, 
            a.erp_acs_prof_menu_level, 
            a.erp_acs_prof_menu_parent,
            c.token_status,
            c.erp_otp_aprv_id,
			e.erp_usr_right_type_id,
			e.erp_usr_right_type_id AS erp_usr_right_type,		
			e.erp_usr_right_menu_id
        ")
        ->join("erpotpaprv c", "c.erp_otp_aprv_id = a.erp_otp_aprv_id", "left")
		->join("erprghtmst e", "e.erp_acs_menu_id = a.erp_acs_menu_id", "left")		
        ->where("a.erp_acs_prof_menu_level", 3)
        ->where("a.erp_acs_prof_menu_parent", $id)
        ->where("c.token_status", 1)
        ->get()
        ->getResultArray();
		// echo $this->univerpaic_db->getlastquery();
		//die(); 
		$final = [];
		foreach($response as $row) {
		   if($row['erp_acs_prof_type']==2){ 		    
				// Only fetch saved profile types if right_menu_id exists for ACCESS MANAGEMENT
				if (!empty($row['erp_usr_right_menu_id'])) {
					$row['saved_profile_types'] = $this->get_saved_profile_types(
						$row['erp_usr_right_menu_id'],
						$row['erp_acs_menu_id']
					);
				} else {
					$row['saved_profile_types'] = [];
				}
		   }		   
		   if($row['erp_acs_prof_type']==1){ 
		        $saved_bckentries = $this->get_back_date_entries(
								$row['erp_acs_menu_id'],
								$profile_id
				);
				if (!empty($saved_bckentries)) {
					$row['saved_bckentries'] = $saved_bckentries;
				} else {
					$row['saved_bckentries'] = [];
				} 
		   }
		   if($row['erp_acs_prof_type']==3){ 
		        $saved_txnaprvals = $this->get_txnaprval_entries(
								$row['erp_acs_menu_id'],
								$profile_id
				);
				if (!empty($saved_txnaprvals)) {
					$row['saved_txn_aprvals'] = $saved_txnaprvals;
				} else {
					$row['saved_txn_aprvals'] = [];
				} 
		   }
		   
			$final[] = $row;
		  }
 
		return $final;		
     }    

    public function ajax_major_access_profile(){                
         $get_owner_of_company = $this->get_owner_of_company(); // if >0 means curret user is owner of the company else not
        $builder = $this->univerpaic_db->table("erpacsprof");
        $builder->where('cmp_id',$this->company_id);
        // apply filters
        if (isset($_POST['pq_filter'])) {             
            $filter_data_info = json_decode($_POST['pq_filter'], true);
            if ($filter_data_info) {
                if (count($filter_data_info['data']) > 0) {
                    $fildwhere = 'like';
                    $fildLike  = 'like';
                } else {
                    $fildwhere = 'where'; 
                    $fildLike  = 'like';
                }
                foreach ($filter_data_info['data'] as $filter_data) {
                    $filter_column = $filter_data['dataIndx'];
                    $filter_val    = $filter_data['value'];             
                   /*
                    if ($filter_column == 'major_head_code') {
                        $fild_name = $this->tablefldprefix."mastcde";
                        $query = "substring({$fild_name},1,3)";
                        $builder->$fildwhere($query, $filter_val);
                    }
                    if ($filter_column == 'major_head_name') {
                        $fild_name = $this->tablefldprefix."mastcde_name";
                        $builder->$fildLike($fild_name, $filter_val);
                    }*/
                }
            }
        }
        
        // ✅ clone before running count
        $countBuilder = clone $builder;
        $total_Records = $countBuilder->countAllResults(false); // false => don't reset query
        
        // pagination values
        $domainid = $_POST["domainid"] ?? null;
        $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
        $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
        
        if ($pq_curPage == 0) {
            $pq_curPage = 1;
        }
        $offset = ($pq_rPP * ($pq_curPage - 1));
        if ($offset > $total_Records) {        
            $pq_curPage = ceil($total_Records / $pq_rPP);
            $offset = ($pq_rPP * ($pq_curPage - 1));
        }
        
        // ✅ use original builder for data query
        $builder->limit($pq_rPP, $offset);
       $result['data']   = $builder->get()->getResultArray();
       
       // print_r($result['data']);
       // die();
            
      $all_data_final2 = array();
       foreach($result['data'] as $dkey => $trow){
        //    $dataid = obfuscate_link($trow['token_id']);
       $dataid = $trow['erp_acs_prof_id'];
   
             $modal_call = ' onClick="callmodal(`'.$dataid.'`,2)"';

             $del_modal_call = ' onClick="backDate(`'.$dataid.'`,1)"';

             $trans_appr = ' onClick="trans_appr(`'.$dataid.'`,3)"';

         
        //  $del_modal_call =' onClick="call_delmod_modal(`'.$dataid.'`,`'.$del_id.'`,`del`)"';
         
        //  $level_1 = $this->level_1($trow['acsprof_menu_parent']);
        //  $menu = $this->level_1($trow['acsprof_menu_main']);
         
         
        //   if($trow['token_status']=='1'){
        //     $status ='Approved';  
        
       if($get_owner_of_company>0){
          $action = '<a href="javascript:void(0)" type="button" ' . $modal_call . ' class="share_single_company mx-2" title="ACCESS MANAGEMENT" alt="ACCESS MANAGEMENT">
        <img src="/public/assets/img/manage_history.png" title="ACCESS MANAGEMENT" alt="ACCESS MANAGEMENT">
        </a>&nbsp;
        <a href="javascript:void(0)" type="button" ' . $del_modal_call . ' title="BACK DATE ENTRY" alt="BACK DATE ENTRY" class="user_permission mx-2">
            <img src="/public/assets/img/add_task.png" title="BACK DATE ENTRY" alt="BACK DATE ENTRY">
        </a>&nbsp;
        <a href="javascript:void(0)" type="button" ' . $trans_appr . ' title="TRANSACTION APPROVAL" alt="TRANSACTION APPROVAL" class="manage_user_access mx-2">
            <img src="/public/assets/img/lock_person.png" title="TRANSACTION APPROVAL" alt="TRANSACTION APPROVAL">
        </a>'; 
           
       }
        else{
           $action = '<a href="javascript:void(0)" type="button" ' . $modal_call . ' class="mx-2 disabled" title="ACCESS MANAGEMENT" alt="ACCESS MANAGEMENT">
        <img src="/public/assets/img/manage_history.png" title="ACCESS MANAGEMENT" alt="ACCESS MANAGEMENT">
        </a>&nbsp;
        <a href="javascript:void(0)" type="button" ' . $del_modal_call . ' title="BACK DATE ENTRY" alt="BACK DATE ENTRY" class="user_permission mx-2">
            <img src="/public/assets/img/add_task.png" title="BACK DATE ENTRY" alt="BACK DATE ENTRY">
        </a>&nbsp;
        <a href="javascript:void(0)" type="button" ' . $trans_appr . ' title="TRANSACTION APPROVAL" alt="TRANSACTION APPROVAL" class="manage_user_access mx-2">
            <img src="/public/assets/img/lock_person.png" title="TRANSACTION APPROVAL" alt="TRANSACTION APPROVAL">
        </a>'; 
        }
        
        
    
    
           
        //     $type_status ='1';			 
        //   }	
        //   else{
        //     $status = 'Under Approval';  
        //     $action ='<a href="javascript:void(0);" '.$modal_call.' class="btn btn-sm btn-purple" data-id="'.$dataid.'" style="color:white">Send OTP</a>&nbsp;<a href="'.base_url().'/admin/UserProfile/level_3/modify/'.$del_id.'?domainid='.$domainid.'" class="btn btn-sm btn-purple" data-ajax="edit" style="color:white">Edit</a>&nbsp;<a href="javascript:void(0);" '.$del_modal_call.' class="btn btn-sm btn-purple" data-ajax="del" data-id="'.$dataid.'" style="color:white">Delete</a>';
        //     $type_status ='0';	
        //   }
          
           
             
                 $all_data_final2[]= array("cmpacsprof_id"=>$trow['erp_acs_prof_id'],
                                           "cmpacsprof_name"=>$trow['erp_acs_prof_name'],
                                           "cmpacsprof_desg"=>$trow['erp_acs_prof_desg'], "action"=>$action);
           }
   
  return  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_data_final2)."}";
 
   }

}