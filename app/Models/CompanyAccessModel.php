<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\externaldb;

class CompanyAccessModel extends Model{
  
   public function __construct() {
        parent::__construct();        
		$this->db            = \Config\Database::connect();		
        $this->session       = \Config\Services::session();
        $this->company_id    =  $this->session->get('ses_company_id');
        $this->user_id       =  $this->session->get('uuid');		
		$this->externaldb    =  new externaldb();
		$this->erp_db        =  $this->externaldb->erp_db();
        $this->sispl_uuid_db =  $this->externaldb->sispl_uuid_db();
        $this->aicountly_db  =  $this->externaldb->aicountly_db();
        $this->contactaic_db =  $this->externaldb->contactaic_db();
		$this->univaictly    =  $this->externaldb->univaictly_db();
		 $this->univerpaic_db = $this->externaldb->univerpaic_db(); 
		 	$this->uuidaictly    =  $this->externaldb->postgr_myaicountlydb();
     }
   
    //needs to be updated
    public function add_company_access($data)
    {
        $shared_user_uuid = $data['uuid_aictly_acs']; // whom to share
		$uuid_aictly_by = $data['uuid_aictly_by'];    // who is sharing
		$cmp_id = $data['cmp_id'];

		// 1. Check if current user owns the company
		$ownerCheck = $this->univaictly->table('cmpacsmstr')
			->where('cmp_id', $cmp_id)
			->where('uuid_acs_type', 1)                 // owner
			->where('uuid_aictly_by', $uuid_aictly_by)
			->get()
			->getRowArray();
		SaveErrorLog("1. Check if current user owns the company");
		SaveErrorLog($this->univaictly->getLastQuery());

		if (!$ownerCheck) {
			// User does not own the company
			return -2; 
		}

		// 2. Prevent sharing to self
		SaveErrorLog("2. Prevent sharing to self");
		if ($uuid_aictly_by == $shared_user_uuid) {
			// Cannot share company with yourself
			return -1;
		}

		// 3. Check if company already shared to this user
		SaveErrorLog("3. Check if company already shared to this user");
		$alreadyShared = $this->univaictly->table('cmpacsmstr')
			->where('cmp_id', $cmp_id)
			->where('uuid_acs_type', 0) 
			->where('uuid_aictly_acs',$shared_user_uuid )                
			->where('uuid_aictly_by', $uuid_aictly_by)
			->get()
			->getRowArray();

		SaveErrorLog($this->univaictly->getLastQuery());

		if ($alreadyShared) {
			// Already shared
			SaveErrorLog("// Already shared");
			return 0;
		}

		// 4. Insert new share record
		SaveErrorLog("4. Insert new share record");
		 $this->univaictly->table('cmpacsmstr')->insert($data);		
		 SaveErrorLog($this->univaictly->getLastQuery());
		 
         $insertId = $this->univaictly->insertID();					 
		return $insertId;
    }
   public function CheckCompanyShared($company){
	    $builder = $this->db->table('cmpacsmstr');
		$builder->select('COUNT(*) AS count');
		$builder->where('cmp_id', $cmp_id);
		$builder->where('uuid_aictly_by', $shared_to_uuid);
		$builder->where('uuid_acs_type', 0);  // shared company

		$result = $builder->get()->getRowArray();

		if ($result['count'] > 0) {
			echo "Company already shared to this user.";
		} else {
			echo "Company NOT shared to this user yet.";
		}
   }
    public function getCompanyDetails($company)
    {
        // --- IMPORTANT ---
        // Replace 'postgres_db' and 'mysql_db' with the actual names of your
        // database connection groups from app/Config/Database.php
        $pgDb = $this->db; // Your PostgreSQL connection
        $myDb = $this->univaictly;    // Your MySQL connection

        // Step 1: Query PostgreSQL to verify the company exists and is active.
        $pgBuilder = $myDb->table("cmpmastern");
        $pgBuilder->select("cmp_id"); // We only need to confirm it exists
        $pgBuilder->where('cmp_id', $company);
        $pgBuilder->where('cmp_status', 1);
        $companyExists = $pgBuilder->get()->getRowArray();

        // If the company doesn't exist or is not active in the main DB, stop here.
        if (!$companyExists) {
            return [];
        }

        // Step 2: Now that we know the company is valid, query MySQL to get the UUID.
        $myBuilder = $myDb->table("cmpacsmstr");
        $myBuilder->select("uuid_aictly_by as uuid");
        $myBuilder->where('cmp_id', $company);
        $result = $myBuilder->get()->getRowArray();

        // If a record was found in the MySQL access table, return it.
        if ($result) {
            return $result;
        }

        // Return an empty array if no matching record was found.
        return [];
    }
	
   public function company_profiles($company_id){
	$result =  $this->univerpaic_db->table('erpacsprof')
    							->select('erp_acs_prof_id,erp_acs_prof_name')
                                ->where('cmp_id', $company_id)                                
                                ->get()->getResultArray();
                                
	return $result;							
	
   }
   
   public function company_profile_info($company_id,$profile_id){
	$result =  $this->univerpaic_db->table('erpacsprof')
    							->select('erp_acs_prof_id,erp_acs_prof_name')
                                ->where('cmp_id', $company_id)  
                                ->where('erp_acs_prof_id', $profile_id)  
                                ->get()->getRowArray();
                                
	return $result;							
	
   }
   
   
    public function get_company_access_users($comp_id,$comp_type)
    {
		$uuid   =  $this->session->get('uuid');
    	$company_owner_info =  $this->univaictly->table('cmpacsmstr')
    							->select('uuid_aictly_by')
                                ->where('uuid_acs_type',1)
								->where('cmp_id', $comp_id) 
								->where('uuid_aictly_by', $uuid) 
                                ->get()->getRowArray();
                                
                
		$company_owner_id=0;						
		if($company_owner_info)
			$company_owner_id=$company_owner_info['uuid_aictly_by'];
			
		 $can_profile_change=0;
		if($company_owner_id == $uuid){
		    
		    $can_profile_change=1;
		}	
		
		$company_profiles  = $this->company_profiles($comp_id);
        
    	$result =  $this->univaictly->table('cmpacsmstr')
    							->select('cmp_acs_id,uuid_aictly_acs,uuid_aictly_by,erp_acs_prof_id')
                                ->where('(uuid_acs_type=0 OR uuid_acs_type=1 )')
								->where('cmp_id', $comp_id)                                
                                ->get()->getResultArray();

        $users  = [];
        foreach ($result as $key => $value) {
            
            if($value['erp_acs_prof_id']>0){
            $company_profile_info  = $this->company_profile_info($comp_id,$value['erp_acs_prof_id']);
            $profile_name = $company_profile_info['erp_acs_prof_name'] ?? "";
            }
            else{
                $profile_name='';
            }
            
			if(($value['uuid_aictly_acs']=='' || $value['uuid_aictly_acs'] ==NULL) && ($value['uuid_aictly_by']==$uuid)){
				$uuid_access = $value['uuid_aictly_by'];
				$is_owner ='Owner';
				
			}
			else{
				$uuid_access = $value['uuid_aictly_acs'];
				$is_owner ='';
			}
        	$result2 = $this->uuidaictly->table('useraictly')
        						->select('user_firstname, user_lastname, useraictly_status, aicountly_useraictly_univdb.uuid, user_regdemail')
                                ->where('uuid', $uuid_access)
                                ->get()->getRowArray();
            if($result2){
            	$result2['idaccess'] = $value['cmp_acs_id'];

                $result2['status'] = true;
                if($result2['useraictly_status'] != 1){
                    $result2['status'] = false;
                }
				$result2['is_owner']=$is_owner;
				$result2['profile_name']= $profile_name;
				$result2['company_profiles']= $company_profiles;
				$result2['can_profile_change'] = $can_profile_change;
				
            	$users[] = $result2;
            } 
        }
        return $users;
    }
    
    public function update_user_profile($uu_id,$profile_id){
        	$result = $this->univaictly->table('cmpacsmstr')
                                ->where('uuid_aictly_acs', $uu_id)
                                ->where('uuid_acs_type', 0)
                                ->update(['erp_acs_prof_id'=>$profile_id]);
        
    }
    
    public function remove_access($idaccess)
    {
    	$result = $this->univaictly->table('cmpacsmstr')
                                ->where('cmp_acs_id', $idaccess)
                                ->delete();
    }

    
    public function update_contact($data)
    {
        $contact = $this->contactaic_db->table('contactaic_contactaic_univdb2')
        					->where('uuid',$data['uuid'])
        					->where('contact_uuid', $data['uuid_access'])
                            ->get()->getRowArray();
        
        if(!$contact){
            $this->add_contact($data['uuid'],$data['uuid_access']);
        }
        
        $contact = $this->contactaic_db->table('contactaic_contactaic_univdb2')
			        		->where('uuid',$data['uuid_access'])
			        		->where('contact_uuid', $data['uuid'])
			                ->get()->getRowArray();
        if(!$contact){
            $this->add_contact($data['uuid_access'],$data['uuid']);
        }
    }

    public function add_contact($uuid,$contact_uuid)
    {
        $user = $this->get_user($contact_uuid);
        $user_details = $this->get_user_details($contact_uuid);

        if($user){
            $data = [
              'contact_uuid'        => $contact_uuid,
              'contact_firstname'   => $user['user_firstname'],
              'contact_lastname'    => $user['user_lastname'],
              'contact_email'       => $user['user_regdemail'],
              'contact_mobile'      => $user['user_regdmobile'],
              'contact_wamobile'    => $user['user_wamobile'],
              'contact_email2'      => $user_details['user_email2'] ?? '',
              'contact_mobile2'     => $user_details['user_mobile2'] ?? '',
              'contact_landline'    => $user_details['user_landline'] ?? '',
              'contact_website'     => $user_details['user_website'] ?? '',
              'contact_cat_id'      => 0,
            ];

            $this->contactaic_db->table('contactaic_mycontacts_univdb')
                                ->insert($data);

            $contact_id = $this->contactaic_db->insertID();

            $data = [
                'contact_id'        => $contact_id,
                'uuid'              => $uuid,
                'contact_uuid'      => $contact_uuid,
                'contact_notes'     => '',
                'contact_cat_id'    => '',
            ];
            $this->contactaic_db->table('contactaic_contactaic_univdb2')
                                ->insert($data);
        }
    }

    public function get_user($id)
    {
        $response = $this->uuidaictly->table('useraictly')
                                ->where('uuid', $id)
                                ->get()->getRowArray();
        
        return $response;
    }

    public function get_user_details($id)
    {
        $response = $this->uuidaictly->table('useraicdet')
                                ->where('uuid', $id)
                                ->get()->getRowArray();
        
        return $response;
    }
	
	public function check_company_shared($uuid_aictly_acs, $comp_id)
	{
	    $status = '';
	    $uuid_mine = $this->session->get('uuid');
	    $data = $this->univaictly->table('cmpacsmstr')
	    						->where('cmp_id', $comp_id)
                                ->where('uuid_aictly_acs', $uuid_aictly_acs)
                                ->get()->getRowArray();
	    if($data)
	    {
	        $status = 'shared';
	    }
	    return $status;
	}
	
	function get_comp_fy_info($comp_id)
	{
		$result=$this->univaictly->table("cmpfymastr")
					->where('cmp_id',$comp_id)
					->get()->getRowArray();

		return $result;
	}
	
	function ajax_all_companies_list() {
    $uuid = $this->session->get('uuid');
    if (!$uuid) {
        return []; // No user, no companies
    }

    $records = [];
    
    // --- Step 1: Connect to both databases ---
    // Assumes 'default' is your PostgreSQL connection in app/Config/Database.php
    $pgDb = $this->univaictly; 
    // Assumes 'mysql_db' is your MySQL connection group name
    $mysqlDb = $this->univaictly;
    
    // --- Step 2: Query MySQL to get company IDs and access rights ---
    $accessBuilder = $mysqlDb->table("cmpacsmstr acs");
    $accessBuilder->select("acs.cmp_id, acs.uuid_aictly_by, acs.uuid_acs_type");
    $accessBuilder->groupStart();
    $accessBuilder->where('acs.uuid_aictly_by', $uuid);  // Owned by user
    $accessBuilder->orWhere('acs.uuid_aictly_acs', $uuid); // Shared with user
    $accessBuilder->groupEnd();
    
    $accessResults = $accessBuilder->get()->getResultArray();
    
    // If user has no access to any companies, stop here.
    if (empty($accessResults)) {
        return [];
    }
    
    // Create a lookup map for company access details and collect IDs
    $accessMap = [];
    $companyIds = [];
    foreach ($accessResults as $access) {
        $accessMap[$access['cmp_id']] = $access;
        $companyIds[] = $access['cmp_id'];
    }
    // Remove duplicate IDs just in case
    $companyIds = array_unique($companyIds);

    // --- Step 3: Query PostgreSQL to get company details for the allowed IDs ---
    $companyBuilder = $pgDb->table("cmpmastern cmp");
    $companyBuilder->select("cmp.cmp_id, cmp.cmp_name, cmp.cmp_short_name");
    $companyBuilder->where('cmp.cmp_status', 1);
    $companyBuilder->whereIn('cmp.cmp_id', $companyIds); // Filter by allowed IDs
    $companyBuilder->orderBy('cmp.cmp_name', 'DESC');
    
    $companyResults = $companyBuilder->get()->getResultArray();

    // --- Step 4: Combine the data in PHP ---
    if (!empty($companyResults)) {
        foreach ($companyResults as $company) {
            $cmp_id = $company['cmp_id'];
            $accessInfo = $accessMap[$cmp_id] ?? null;

            if (!$accessInfo) {
                continue; // Skip if access info not found (should not happen)
            }

            // Determine company type
            $company_type = ($accessInfo['uuid_acs_type'] == 1) ? 'owner' : 'shared';
            
            // Get financial year info (your existing logic)
            $finyear = '';
            $comp_fy_info = $this->get_comp_fy_info($cmp_id);
            if ($comp_fy_info) {
                $bgn_date = date('d-m-Y', strtotime($comp_fy_info['fy_beg_date']));
                $end_date = date('d-m-Y', strtotime($comp_fy_info['fy_end_date']));
                $finyear = $bgn_date . ' -- ' . $end_date;
            }

            $records[] = [
                'encomp_id'          => obfuscate_link($cmp_id),
                'uuid_aicountly'     => $accessInfo['uuid_aictly_by'],
                'company_type'       => $company_type,
                'company_id'         => $cmp_id,
                'companyname'        => $company['cmp_name'],
                'company_short_name' => $company['cmp_short_name'],
                'companycode'        => erp_compcode_format($cmp_id),
                'finyear'            => $finyear,
            ];
        }
    }
    
    return $records;
}
	
	
	function user_company_list(){
	     $mycompanies  = $this->mycompanies();
	     $company_list = array();
	     if(!empty($mycompanies)){
	       $data = $this->aicountly_db->table('aicountly_cmpmastern_univdb')->whereIn("comp_id",$mycompanies)->get()->getResultArray();
	       if($data){
	         foreach($data as $row){
	               $company_list[] = array("company_name"=>$row['comp_name'],"comp_code"=>$row['comp_code'],"comp_id"=>$row['comp_id']); 
	            }  
	         }
	      }
	     
	     return json_encode($company_list);
	    
	}
	 
		
    
   public function mysharedcompanies(){
	    $uuid = 	 $this->session->get('uuid');
		 
        $my_companies= array();
        $builder = $this->aicountly_db->table("aicountly_cmpidacsnn_univdb"); 
        $builder->where('uuid_access',$uuid);
		$result = $builder->get()->getResultArray();
		if($result){
		    foreach($result as $row){
		        $my_companies[$row['comp_id']]=$row['comp_id'];
		        
		    }
		    
		    
		}
       
      return $my_companies; 
   } 


    public function shared_group_company_list(){
        
        $uuid = $this->session->get('uuid');

        $records = [];
        $builder = $this->aicountly_db->table("aicountly_cmpidacsnn_univdb");
        $builder->select('comp_id');
        $builder->where('uuid_access',$uuid);
        $builder->where('comp_type','grp');
        $builder->groupBy('comp_id');
        $builder->orderBy('comp_id','DESC');
        $result = $builder->get()->getResultArray();

        foreach ($result as $key => $value) {

            $builder = $this->aicountly_db->table("aicountly_grpmasternn_univdb");
            $builder->select('grpco_id, grpco_name');
            $builder->where('grpco_id',$value['comp_id']);
            $result2 = $builder->get()->getRowArray();

           if($result2){
                $records[] = array(
                    'encomp_id'        =>  obfuscate_link($result2['grpco_id']),
                    'ownership'        => 'shared',
                    'comp_type'     => 'grp',
                    'comp_id'       => $result2['grpco_id'],
                    'comp_name'     => $result2['grpco_name'],
                    'comp_code'     => 'grp'.str_pad($result2['grpco_id'], 7, '0', STR_PAD_LEFT),
                );
           }
        }
        
        return $records;          
    }   
        
    public function ajax_sharedcompany_list(){
		if(isset($_POST["pq_filter"])){
			$pq_filter     = $_POST["pq_filter"];	       
			$filter_data  = json_decode($_POST["pq_filter"],true);
			$pq_filters   = $filter_data['data'][0];
			$search_text = strtolower($pq_filters['value']);
			$dataIndx    = $pq_filters['dataIndx']; 
		}
		else{
			$pq_filter     ='';
			$search_text   ='';
			$dataIndx      ='';			
		  }	
		
		$uuid =  $this->session->get('uuid');
		$records = array();
		$db1 = 'erpaicountly_erpaictlyn';
		$db2 = 'erpaicountly_univaictly';

		$builder = $this->db->table("{$db1}.cmpmastern cmp");
		$builder->select("cmp.*, acs.uuid_aictly_acs,acs.uuid_aictly_by,acs.uuid_acs_type,acs.uuid_aictly_by as uuid");
		$builder->join("{$db2}.cmpacsmstr acs", "cmp.cmp_id = acs.cmp_id", 'left');
		$builder->where('acs.uuid_aictly_acs', $uuid);
		$builder->where('acs.uuid_acs_type', 0);
		$builder->where('cmp.cmp_status', 1);
		
		if ($search_text !== '') {
			$builder->groupStart();
			$builder->like('LOWER(cmp.cmp_name)', strtolower($search_text), 'both');
			$builder->orLike('LOWER(cmp.cmp_print_name)', strtolower($search_text), 'both');
			$builder->orLike('LOWER(cmp.cmp_short_name)', strtolower($search_text), 'both');
			$builder->groupEnd();
		 }		
		$builder->orderBy('cmp.cmp_name', 'DESC');
		$result = $builder->get()->getResultArray();
		
        if(!empty($result)){
		foreach($result as $values){
			$shared_by = '';
            $builder = $this->aicountly_db->table('aicountly_useraictly_univdb');
            $builder->select('user_firstname, user_lastname');
            $builder->where('uuid', $values['uuid_aictly_acs']);
            $userData = $builder->get()->getRowArray();
            if($userData){
                $shared_by = $userData['user_firstname'] . ' ' . $userData['user_lastname'];
            }
            $finyear      = '';
            $comp_fy_info = $this->get_comp_fy_info($values['cmp_id']);
            if($comp_fy_info){
                $bgn_date = $comp_fy_info['fy_beg_date'];
                $end_date = $comp_fy_info['fy_end_date'];
                $bgn_date = date('d-m-Y',strtotime($bgn_date));
                $end_date = date('d-m-Y',strtotime($end_date));
                $finyear  = $bgn_date.' -- '.$end_date;
             }

            $records[] = array(	
		              'company_id'=>$values['cmp_id'],
					  'companyname'=>$values['cmp_name'],
                      'company_short_name'=>$values['cmp_short_name'],
					  'companycode' => erp_compcode_format($values['cmp_id']),
					  'finyear'     => $finyear,
                      'shared_by'   => $shared_by
					   );    
 	       
		  } 	       
        }
		  else{
          $total_Records =0;
          $pq_curPage    =1;
          $records       =[];
            
        }
        $total_Records = count($records);
         if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
         
         if($pq_curPage==0){$pq_curPage=1;}
         	$offset = ($pq_rPP * ($pq_curPage - 1));
         if ($offset > $total_Records)
          {        
           $pq_curPage = ceil($total_Records / $pq_rPP);
           $offset = ($pq_rPP * ($pq_curPage - 1));
          } 
        
              
       $final_records = array_slice( $records, $offset, $pq_rPP );
	   
	  
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";  
        
    }
	
	function get_company_info($company_id){	 
	  return $this->aicountly_db->table('aicountly_cmpmastern_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
   }
   
    public function get_by_email($email)
    {
        $uuid = $this->session->get('uuid');
        $builder = $this->uuidaictly->table('useraictly');
        $builder->select('user_firstname, user_lastname, uuid, user_regdemail');
        //$builder->where('uuid != ', $uuid);
        $builder->where('user_regdemail', $email);        
		$result = $builder->get()->getRowArray();		
        return $result;
    }
    public function get_by_phone($phone)
    {
        $uuid = $this->session->get('uuid');
        $builder = $this->uuidaictly->table('useraictly');
        $builder->select('user_firstname, user_lastname, uuid, user_regdemail');
        $builder->where('uuid != ', $uuid);
        $builder->where('user_regdmobile', $phone);
        
		$result = $builder->get()->getResultArray();
		
        return $result;
    }


    public function get_by_category($category)
    {
    	$uuid= $this->session->get('uuid');
        $categoryData =  $this->contactaic_db->table('contactaic_contaiccat_univdb ')
						->select('contact_cat_id')
						->where('uuid', $uuid)
						->where('LOWER(contact_category)', strtolower($category))
						->get()->getRowArray();

        $final = [];
        if($categoryData)
        {
        	$category_id = $categoryData['contact_cat_id'];

	        $result = $this->contactaic_db->table('contactaic_contactaic_univdb2')
                    ->select('contact_id, contact_uuid')
                    ->like('contact_cat_id', $category_id)
                    ->where('uuid', $uuid)
                    ->get()->getResultArray();
            
            foreach ($result as $key => $value) {
                
                $result2 = $this->contactaic_db->table('contactaic_mycontacts_univdb')
                    ->select('contact_uuid as uuid, contact_firstname as user_firstname, contact_lastname as user_lastname, contact_email as user_regdemail')
                    ->where('contact_id', $value['contact_id'])
                    ->get()->getRowArray();

                // $result2 = $this->aicountly_db->table('aicountly_useraictly_univdb')
                //         ->select('uuid, user_firstname, user_lastname,  user_regdemail') 
                //         ->where('uuid', $value['uuid_contactid'])
                //         ->get()->getRowArray();

                if($result2){ $final[] = $result2; }          
            }
        }
        return $final;
    }


    public function check_contact_status($data)
    {
	    $status = '';
	    $contact = $this->contactaic_db->table('contactaic_contactaic_univdb ')
	    			->select('contact_status')
	    			->where('uuid_aicountly',$data['uuid'])
                    ->where('uuid_contactid', $data['uuid_access'])
	                ->orWhere('uuid_aicountly',$data['uuid_access'])
                    ->where('uuid_contactid', $data['uuid'])
	                ->get()->getRowArray();

		if($contact){
		    $status = $contact['contact_status'];
		}
        return $status;
    }
	
    public function createAccount($email,$mobile)
    {
        $nstatus = true;

       if(!empty($email) && !empty($mobile))
       {
          $builder = $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
          $builder->where('uuid_regdemail', $email);
          $builder->where('uuid_project', 1);
          $result = $builder->get()->getRowArray();
       }
       else if(empty($mobile))
       {
          $builder = $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
          $builder->where('uuid_regdemail', $email);
          $builder->where('uuid_regdmobile', '');
          $builder->where('uuid_project', 1);
          $result = $builder->get()->getRowArray();
       }
       else{
          $builder = $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
          $builder->where('uuid_regdemail', '');
          $builder->where('uuid_regdmobile', $mobile);
          $builder->where('uuid_project', 1);
          $result = $builder->get()->getRowArray();
       }

        if(!empty($result))
            return $result['uuid'];


        $data = [
            'uuid_regdemail'    => $email,
            'uuid_regdmobile'   => $mobile,
            'uuid_regdwa'       => '',
            'uuid_project'      => 1,
        ]; 
        
        $this->sispl_uuid_db->table('sispluuid_uuidgenert_univdb')
                            ->insert($data);
        $uuid = $this->sispl_uuid_db->insertID();

        $useraictly_data = [
            'uuid'                 => $uuid,
            'user_firstname'       => '',
            'user_midname'         => '',
            'user_lastname'        => '',
            'user_name'            => '',
            'user_pass'            => '',
            'user_regdmobile'      => $mobile,
            'user_regdemail'       => $email,
            'user_wamobile'        => '',
            'user_aicountly_business_id'    => '',
            'user_pin'             => '',
            'user_gender'          => '',
            'user_type_profs'      => '',
            'user_dob'             => '',
            'auth_code'            => '',
            'auth_time'            => '', 
            'useraictly_status'    => 3,
        ];

        $this->aicountly_db->table('aicountly_useraictly_univdb')
                                ->insert($useraictly_data);
  
        return $uuid;
    }
}