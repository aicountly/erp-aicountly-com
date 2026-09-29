<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\UUIDtables;
use App\Models\CommonModel;
class BranchesModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->CommonModel   = new CommonModel();	
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');	 
	   $this->aicountly_db  = $this->externaldb->aicountly_db();	
	   $this->univaictly    =  $this->externaldb->univaictly_db();
    } 
 
   public function validate_addmore_branch_gstin($company_id, $gstin_number,$gstin_type,$wef_date,$branch_id)
    {   $hobomaster_tbl = 'hobomaster';  
        $account_master_tbl = 'acctmaster';	
		$gstinmastr_tbl = 'hobogstinm';
		$gstindetail_tbl = 'hobogstdet';
		
      if($gstin_number){	   
       if($this->session->get('ses_company_id')!=''){
		$fy_start = date('Y-m-d',strtotime($this->session->get('ses_company_fy_beginning')));
		$fy_end    = date('Y-03-31', strtotime($fy_start. ' + 1 year'));
	  } 
	 else{
	   $fy_start  =  date('Y-04-31');
	   $fy_end     =  date('Y-03-01');
	  } 
	  
        // Check if the same GSTIN exists overall 
        $query = $this->univaictly->table($gstinmastr_tbl)  
					->where('cmp_id', $company_id)
					->where('hobo_gstin', $gstin_number)
					->get();
        if ($query->getNumRows() > 0) {
			 return json_encode(['status' => false, 'message' => "GSTIN: ".$gstin_number." should be unique."]);
         }
		 
	//**Prevent Duplicate GSTIN in the Same Branch**
    $query = $this->univaictly->table($gstinmastr_tbl)
        ->where('hobo_id', $branch_id)
		 ->where('cmp_id', $company_id)
        ->where('hobo_gstin', $gstin_number)
        ->get();
    if ($query->getNumRows() > 0) {
        return json_encode(["status" => false, "message" => "This GSTIN is already used in the same branch."]);
    } 
	
	// **Ensure GSTIN is Not Used in Different Categories within the Same Branch or Across Branches**
    $query = $this->univaictly->table($gstinmastr_tbl)
        ->where('hobo_gstin', $gstin_number)
		 ->where('cmp_id', $company_id)
        ->where('hobo_gstin_type !=', $gstin_type)
        ->get();
    if ($query->getNumRows() > 0) {
        return json_encode(["status" =>false, "message" => "The same GSTIN cannot be used in different categories across branches."]);
    }
	
		 
	 // **Ensure "Regular" & "Composition" GSTINs Are Not Mixed in the Same Branch**
    $query = $this->univaictly->table($gstinmastr_tbl)
        ->where('cmp_id', $company_id)
		->where('hobo_id', $branch_id)
        ->whereIn('hobo_gstin_type', [1, 2])
        ->get();

      if ($query->getNumRows() > 0) {
         $existing_type = $query->getRow()->hobo_gstin_type;
          if (($existing_type == '1' && $gstin_type == '2') || ($existing_type == '2' && $gstin_type == '1')) {
            return json_encode(["status" => false, "message" => "You cannot mix 'Regular' and 'Composition' GSTIN types."]);
          }
      }
    
	
		// **Prevent Adding "Regular" or "Composition" GSTINs in the Same FY, Even if Outside WEF & Inactive Dates**
		$query = $this->univaictly->table($gstinmastr_tbl . ' hgn') // 'hgn' is an alias for hobogstinn
				->select('hgn.hobo_gstin_id') // Select a column just for counting, it's efficient
				->join($gstindetail_tbl . ' hgd', 'hgn.hobo_gstin_id = hgd.hobo_gstin_id', 'inner') // Join with hobogstdet
				->where('hgn.cmp_id', $company_id)
				->where('hgd.cmp_id', $company_id)
				->whereIn('hgn.hobo_gstin_type', ['1', '2']) // Filter by Regular or Composition
				->where('hgd.hobo_gstin_wef_act >=', $fy_start) // Check effective date is within FY
				->where('hgd.hobo_gstin_wef_act <=', $fy_end)
				->groupStart() // Start a grouping for the active/inactive logic
					->where('hgd.hobo_gstin_inact_date IS NULL') // Condition 1: GSTIN is active (no inactive date)
					->orWhere('hgd.hobo_gstin_inact_date >', $fy_end)   // Condition 2: Or, it becomes inactive AFTER the current FY
				->groupEnd() // End the grouping
				->get();

			if ($query->getNumRows() > 0) {
				return json_encode([
					"status" => false, 
					"message" => "A 'Regular' or 'Composition' GSTIN already exists for this Financial Year ($fy_start - $fy_end)."
				]);
			}
		
    // **Check for other GSTIN types (Other than "Regular" or "Composition")** 
	$queryOtherGstin = $this->univaictly->table("$gstinmastr_tbl hgn") // Alias hobogstinn as 'hgn'
		->join("$gstindetail_tbl hgd", "hgn.hobo_gstin_id = hgd.hobo_gstin_id", "inner") // Join with hobogstdet
		->where('hgn.cmp_id', $company_id)
		->whereNotIn('hgn.hobo_gstin_type', ['1', '2']) // Check types from the master table
		->where('hgd.hobo_gstin_wef_act >=', $fy_start) // Check dates from the detail table
		->where('hgd.hobo_gstin_wef_act <=', $fy_end)
		->groupStart()
			->where('hgd.hobo_gstin_inact_date IS NULL')      // GSTIN is still active
			->orWhere('hgd.hobo_gstin_inact_date >', $fy_end) // Or becomes inactive after this FY
		->groupEnd()
		->get();

	if ($queryOtherGstin->getNumRows() > 0) {
		return json_encode([
			"status" => false, 
			"message" => "Another GSTIN type already exists for this financial year. Mixing different GSTIN registration types is not allowed."
		]);
	}

     }

    // If all checks pass, return true
    return json_encode(['status' => true, 'message' =>'']);
   }
   
   
   public function validate_modify_branch_gstin($gstin_id, $gstin_number, $wef_date, $branch_id)
{   
    // Use the correct, fixed table name for your new schema
    $gstinmastr_tbl = 'hobogstinm';

    if ($gstin_number) {
        // --- Validation 1: Check if the GSTIN is a duplicate anywhere in the company, excluding the current record being edited ---
        $query = $this->univaictly->table($gstinmastr_tbl)           
            ->where('hobo_gstin', $gstin_number)       // Use the correct column name 'hobo_gstin'
            ->where('hobo_gstin_id !=', $gstin_id)     // Use the correct primary key 'hobo_gstin_id'
            ->where('cmp_id', $this->company_id)       // It's good practice to scope this to the company
            ->get();
            
        if ($query->getNumRows() > 0) {
            return json_encode([
                'status' => false, 
                'message' => "GSTIN: " . $gstin_number . " already exists and must be unique."
            ]);
        }
         
        // --- Validation 2: Check if the GSTIN is a duplicate within the same branch, excluding the current record ---
        $query = $this->univaictly->table($gstinmastr_tbl)
            ->where('hobo_id', $branch_id)            // Use the correct branch ID column 'hobo_id'
            ->where('hobo_gstin', $gstin_number)      // Use 'hobo_gstin'
            ->where('hobo_gstin_id !=', $gstin_id)    // Use 'hobo_gstin_id'
            ->get();
            
        if ($query->getNumRows() > 0) {
            return json_encode([
                "status" => false, 
                "message" => "This GSTIN is already assigned to this branch."
            ]);
        } 
    }

    // If all checks pass, return true
    return json_encode(['status' => true, 'message' => '']);
}
	
	function validate_gstin_inactive_date($gstin_id, $wef_date, $inactive_date)
{
    // Use empty() to check for '', null, or false. No validation needed if inactive date is not set.
    if (!empty($inactive_date)) {
        try {
            // Create DateTime objects for safe comparison.
            // setTime(0, 0) removes the time component to compare dates only.
            $wefDateObj = new \DateTime($wef_date);
            $wefDateObj->setTime(0, 0);

            $inactiveDateObj = new \DateTime($inactive_date);
            $inactiveDateObj->setTime(0, 0);

            $currentDateObj = new \DateTime('today'); // 'today' automatically sets time to 00:00:00

            // Check 1: Inactive date cannot be before the WEF date.
            if ($inactiveDateObj < $wefDateObj) {
                return json_encode([
                    "status" => false, 
                    "message" => "Inactive date cannot be before the 'With Effect From' date."
                ]);
            }

            // Check 2: Inactive date cannot be a future date.
            if ($inactiveDateObj > $currentDateObj) {
                return json_encode([
                    "status" => false, 
                    "message" => "Inactive date cannot be a future date."
                ]);
            }
            
        } catch (\Exception $e) {
            // This catches errors if the date strings are in an invalid format.
            return json_encode([
                "status" => false, 
                "message" => "Invalid date format provided. Please use YYYY-MM-DD."
            ]);
        }
    }

    // If no inactive date is provided, or if all checks pass, return true.
    return json_encode(["status" => true, "message" => '']);
}
	
	
	
	  
   
   function get_bbb_groups_list()
    {        
		$groups = [16,22];
		$array = $this->get_sub_group_ids_list($groups);        
        return $array;
    }

    function get_cash_groups_list()
    {        
		$groups = [23];
		$array = $this->get_sub_group_ids_list($groups);        
        return $array;
    }
 
 

    function check_op_change_pnl($account_id, $balance)
    {
    	 $get_account_info = $this->get_account_info($account_id);
		  if(in_array($get_account_info['acc_grp_parent_id'], [6,7,8,9,10,11,12,13])){
		  	if($balance != 0)
		  		return 0;
		  }

		  if(in_array($get_account_info['acc_grp_id'], $this->get_pnl_groups())){
		  	if($balance != 0)
		  		return 0;
		  }

		return 1;
    }
   
    

	function get_bbb_groups()
    {
        
		$groups = [16,22];
		$array = $this->get_sub_group_ids($groups);
        
        return $array;
    }

	
    
	public function get_branch_info($bo_id){       
        $data = $this->univaictly->table('hobomaster')->where('cmp_id', $this->company_id)->where('hobo_id', $bo_id)->get()->getRowArray();
        return $data;
     }
	      
   
	 public function get_compadrs_info(){
		    return $this->univaictly->table('cmpaddrmst')->where('cmp_id', $this->company_id)->get()->getResultArray();
	 }
	
	public function save_gstinmastr($data){
		if($data['hobo_gstin_type']!=''){
		$gstin_data   = [
						'hobo_id'              => (int)$data['hobo_id'],
						'cmp_id'               => (int)$data['cmp_id'],
						'hobo_gstin'           => $data['hobo_gstin'],
						'hobo_gstin_type'      => (int)$data['hobo_gstin_type'],
						'hobo_gstin_sub_type'  => (int)$data['hobo_gstin_sub_type'],
						'hobo_gstin_state_code'=> $data['hobo_gstin_state_code']
					    ];
        $this->univaictly->table("hobogstinm")->insert($gstin_data);
		$hobo_gstin_id  = $this->univaictly->insertID();
		
		if($data['hobo_gstin_legal_name']!='' && $data['hobo_gstin_trade_name']){
			
			$hobo_gstin_wef_dt   = trim($data['hobo_gstin_wef_act'])   === '' ? null : $data['hobo_gstin_wef_act'];   // YYYY-MM-DD
			$hobo_gstin_inact_dt = trim($data['hobo_gstin_inact_date']) === '' ? null : $data['hobo_gstin_inact_date']; // YYYY-MM-DD

		    $hobogstdet_data   = [
						'hobo_gstin_id'        => (int)$hobo_gstin_id,
						'hobo_id'              => (int)$data['hobo_id'],
						'cmp_id'               => (int)$data['cmp_id'],
						'hobo_gstin_jurisd_st' => $data['hobo_gstin_jurisd_st'],
						'hobo_gstin_jurisd_ct' => $data['hobo_gstin_jurisd_ct'],
						'hobo_gstin_legal_name'=> $data['hobo_gstin_legal_name'],
						'hobo_gstin_trade_name'=> $data['hobo_gstin_trade_name'],						
						'hobo_gstin_wef_act'   => $hobo_gstin_wef_dt,
						'hobo_gstin_inact_date'=> $hobo_gstin_inact_dt
					    ];
            $this->univaictly->table("hobogstdet")->insert($hobogstdet_data);
			
		  }
		}
	  } 
	  
    public function add_account($data){	
       $hobomaster_data   = [
						'cmp_id'        => $this->company_id,
						'hobo_name'     => ucwords(clean($data['hobo_name'])), //remove special characters
						'hobo_alias'    => $data['hobo_alias'],
						'hobo_op_date'  => $data['hobo_op_date'],
						'hobo_cl_date'  => $data['hobo_cl_date'],
						'hobo_zone'     => $data['hobo_zone'],
					    ];
		$exists = $this->univaictly->table("hobomaster")
		                ->where('cmp_id', $this->company_id)
    					->where('LOWER(hobo_name)',strtolower(clean($data['hobo_name'])))
    					->countAllResults();		
        if($exists>0)
		  return ['status' => false, 'bo_id' => 0,'message'=>'Branch already exists'];	
								
        $this->univaictly->table("hobomaster")->insert($hobomaster_data);
        $hobo_id = $this->univaictly->insertID();
		
		if($data['hobo_state']!='' && $data['hobo_pin_zip']!=''){
		$hoboadrs_data   = [
						'hobo_id'       => $hobo_id,
						'cmp_id'        => $this->company_id,
						'hobo_addr1'    => $data['hobo_addr1'],
						'hobo_addr2'    => $data['hobo_addr2'],
						'hobo_city'     => $data['hobo_city'],
						'hobo_state'    => $data['hobo_state'],
						'hobo_pin_zip'  => $data['hobo_pin_zip'],
						'hobo_country'  => $data['hobo_country'],
					    ];
        $this->univaictly->table("hoboaddrmt")->insert($hoboadrs_data);
		}
		
		if($data['hobo_tan']){
		$hobotan_data   = [
						'hobo_id'           => $hobo_id,
						'cmp_id'            => $this->company_id,
						'hobo_tan'          => $data['hobo_tan'],
						'hobo_tan_jurisd'   => $data['hobo_tan_jurisd'],
						'hobo_tan_wef_act'  => $data['hobo_tan_wef_act'],
						'hobo_tan_inact_date'=> $data['hobo_tan_inact_date']						
					    ];
        $this->univaictly->table("hobotanmst")->insert($hobotan_data);
		}
		
		
        return ['status' => true, 'bo_id' => $hobo_id];
    
    }

    function create_mst_base_id($id,$type)
    {
    	$uuid  = $this->session->get('comp_uuid');
  		$UUIDtables = new UUIDtables($uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
  		$mst_base_id = $UUIDtables->get_mst_base_id($id,$type);
  		return $mst_base_id;
    }

    function delete_comp_fy_mst_map($id,$type)
    {
    	$uuid  = $this->session->get('comp_uuid');
  		$UUIDtables = new UUIDtables($uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
  		$mst_base_id = $UUIDtables->delete_comp_fy_mst_map_by_id($id,$type);
    }
    
    
     
    function get_sundry_groups()
    {
        
		$groups = [16,22];
		$array = $this->get_sub_group_ids($groups);
        
        return $array;
    
    }
    
    
    function financialYearBeginningDate()
    {	
        $month = date('m');
        if($month > 4)
        {
            $y  = date('Y');          
            $fy_start = $y."-04-01-";
        }
        else
        {
            $y = date('Y', strtotime('-1 year'));          
            $fy_start = $y."-04-01-";
        }
        return $fy_start;
    }
      
	 
	 
	  public function remove_single_branches($bo_id){
		$this->univaictly->table('hobomaster')->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->delete();
		$this->univaictly->table('hobotanmst')->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->delete();
		$this->univaictly->table('hobogstinm')->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->delete();
		$this->univaictly->table('hobogstdet')->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->delete();
		$this->univaictly->table('hoboaddrmt')->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->delete();
		return TRUE;
	  }
	 
	
	 
	public function mark_branch_ho($bo_id)
		{
      // Mark the selected branch as HO
       $this->univaictly->table('hobomaster')
             ->where('hobo_id', $bo_id)
             ->update(['mark_ho' => '1']);

      // Unmark all other branches
       $this->univaictly->table('hobomaster')
             ->where('hobo_id !=', $bo_id)
             ->update(['mark_ho' => NULL]);
		}
	 
	 public function verify_gstin_vouchers_exists($bo_id,$gstinid){
	 $gstin_m_tbl   = 'hobogstinm';
    $gstin_d_tbl   = 'hobogstdet';
    $vch_tbl       = 'vchtxnconso';

    /* ============================
       FETCH GSTIN VALIDITY DATES
       ============================ */

    $gstin_response = $this->univaictly->table($gstin_m_tbl . ' m')
        ->select('d.hobo_gstin_wef_act, d.hobo_gstin_inact_date')
        ->join($gstin_d_tbl . ' d', 'd.hobo_gstin_id = m.hobo_gstin_id', 'left')
        ->where('m.hobo_id', $bo_id)
        ->where('m.hobo_gstin_id', $gstinid)
        ->get()
        ->getRowArray();

    if (!$gstin_response) {
        // GSTIN not found → safe to proceed
        return false;
    }

    /* ============================
       RESOLVE DATE RANGE
       ============================ */

    $wefdate = (!empty($gstin_response['hobo_gstin_wef_act'])) 
                ? $gstin_response['hobo_gstin_wef_act'] 
                : date('Y-m-d');

    if (!empty($gstin_response['hobo_gstin_inact_date'])) {
        $inactivedate = $gstin_response['hobo_gstin_inact_date'];
    } else {
        // still active → till today
        $inactivedate = date('Y-m-d');
    }

    /* ============================
       CHECK IF ANY VOUCHER EXISTS
       IN THIS GSTIN PERIOD
       ============================ */

    $count = $this->db->table($vch_tbl)
        ->where('hobo_id', $bo_id)
        ->where('vch_date >=', $wefdate)
        ->where('vch_date <=', $inactivedate)
        ->countAllResults();

    if ($count > 0) {
        // GSTIN already used in vouchers during this period
        return true;
    }

    return false;
	}  
	
	 function check_branch_gstins_voucher($bo_id)
     {
		 $exists=0;
		 $all_gstins = $this->SavedGSTINSList($bo_id);
         if($all_gstins){
			 foreach($all_gstins as $row){
				 $gstinid = $row['gstinmastr_id'];
				 $verify_vouchers = $this->verify_gstin_vouchers_exists($bo_id,$gstinid);
				 if($verify_vouchers)
					$exists++; 
			 }			 
		 }
		 
		if($exists >0){
	         return 1;
	     }
	     return 0;
     }
	 
	
	   
	 public function update_account($data,$bo_id,$accid){
	    $hobomaster_tbl = 'hobomaster';
		
	    $table = $this->univaictly->table($hobomaster_tbl)->where('LOWER(hobo_name)', strtolower(trim($data['hobo_name'])))
	                                                   ->where('hobo_id !=',$bo_id)
                                                	   ->where('cmp_id ',$this->company_id)
                                                	   ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->univaictly->table($hobomaster_tbl)->where('LOWER(hobo_alias)', strtolower(trim($data['hobo_alias'])))
	                                                   ->where('hobo_id !=',$bo_id)
                                                	   ->where('cmp_id ',$this->company_id)
                                                	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	    }
	    
		$data_hobo_data= array("hobo_name"=>$data["hobo_name"],"hobo_alias"=>$data["hobo_alias"],"hobo_op_date"=>$data["hobo_op_date"],
		                       "hobo_cl_date"=>$data["hobo_cl_date"],"hobo_zone"=>$data["hobo_zone"]);		
	    $this->univaictly->table($hobomaster_tbl)->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->update($data_hobo_data);
		
		
		$exists = 	$this->univaictly->table("hoboaddrmt")->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->get()->getRowArray();
		if($exists){
		$adrs_update_data   = [
						'hobo_addr1'  => $data['hobo_addr1'],
						'hobo_addr2'  => $data['hobo_addr2'],
						'hobo_city'   => $data['hobo_city'],
						'hobo_state'  => $data['hobo_state'],
						'hobo_pin_zip'=> $data['hobo_pin_zip'],						
						'hobo_country'=> $data['hobo_country']						
					    ];						
		$this->univaictly->table("hoboaddrmt")->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->update($adrs_update_data);
		}
		else{
		    $adrs_insert_data   = [
		                'cmp_id'      => $this->company_id,
		                'hobo_id'     => $bo_id,
						'hobo_addr1'  => $data['hobo_addr1'],
						'hobo_addr2'  => $data['hobo_addr2'],
						'hobo_city'   => $data['hobo_city'],
						'hobo_state'  => $data['hobo_state'],
						'hobo_pin_zip'=> $data['hobo_pin_zip'],						
						'hobo_country'=> $data['hobo_country']						
					    ];						
		$this->univaictly->table("hoboaddrmt")->insert($adrs_insert_data);
		}
		return ['status' => true, 'bo_id' => $bo_id];	
   } 
   public function update_tanmastern($data,$bo_id){
	 
	   $tan_update_data   = [
						'hobo_tan'  => $data['hobo_tan'],
						'hobo_tan_jurisd'  => $data['hobo_tan_jurisd'],
						'hobo_tan_wef_act'   => $data['hobo_tan_wef_act'],
						'hobo_tan_inact_date'  => $data['hobo_tan_inact_date']			
					    ];						
		$this->univaictly->table("hobotanmst")->where('cmp_id',$this->company_id)->where('hobo_id',$bo_id)->update($tan_update_data);
		return ['status' => true];	
   } 

	
	public function add_gstinmastr($hobogstinn_data, $hobogstdet_data) {		
		// Step 1: Insert into the main table 'hobogstinm'
		$this->univaictly->table('hobogstinm')->insert($hobogstinn_data);		
		// Step 2: Get the ID of the row you just inserted
		$new_hobo_gstin_id = $this->univaictly->insertID();		
		if ($new_hobo_gstin_id) {
			// Step 3: Add the new ID to the details array
			$hobogstdet_data['hobo_gstin_id'] = $new_hobo_gstin_id;
			
			// Step 4: Insert into the details table 'hobogstdet'
			$this->univaictly->table('hobogstdet')->insert($hobogstdet_data);
			
			return $new_hobo_gstin_id; // Return the new ID on success
		}
		
		return false; // Return false on failure
	}
	
	
	
	
	
	public function update_gstin_info($gstin_id, $hobogstinn_data, $hobogstdet_data)
{
    // Start a database transaction
   

    // 1. Update the main 'hobogstinm' table
    if (!empty($hobogstinn_data)) {
        $this->univaictly->table('hobogstinm')
                 ->where('hobo_gstin_id', $gstin_id)
                 ->update($hobogstinn_data);
    }

    // 2. Update the details 'hobogstdet' table
    if (!empty($hobogstdet_data)) {
        $this->univaictly->table('hobogstdet')
                 ->where('hobo_gstin_id', $gstin_id)
                 ->update($hobogstdet_data);
				//echo $this->db->getlastquery(); 
    }

    // Complete the transaction
   

    

    return true; // Transaction succeeded
}
	
	
   
  public function ajax_branch_list()
{
    $comp_id  = $this->session->get('ses_company_id');

    // Filters (kept but not used in this snippet)
    if (isset($_POST["pq_filter"])) {
        $filter_data = json_decode($_POST["pq_filter"], true);
        $pq_filters  = $filter_data['data'][0];
        $condition   = $pq_filters['condition'] ?? '';
        $search_text = strtolower($pq_filters['value']);
        $dataIndx    = $pq_filters['dataIndx'];
    } else {
        $search_text = '';
        $dataIndx    = '';
        $condition   = '';
    }

    // Lookup tables for state/country
    $stateRows = $this->aicountly_db
        ->table("aicountly_stateslist_univdb", false)
        ->select('state_id, state_name', false)
        ->get()
        ->getResultArray();
    $stateMap = [];
    foreach ($stateRows as $s) {
        $stateMap[$s['state_id']] = $s['state_name'];
    }

    $countryRows = $this->aicountly_db
        ->table("aicountly_countrylst_univdb", false)
        ->select('countryid, countryname', false)
        ->get()
        ->getResultArray();
    $countryMap = [];
    foreach ($countryRows as $c) {
        $countryMap[$c['countryid']] = $c['countryname'];
    }

    // Builder: left join so branches without address still appear
    $builder = $this->univaictly->table("hobomaster h");
    $builder->select([
        'h.hobo_id',
        'h.cmp_id',
        'h.mark_ho',
        'h.hobo_name',
        'h.hobo_alias',
        'h.hobo_op_date',
        'h.hobo_cl_date',
        'a.hobo_addr1',
        'a.hobo_addr2',
        'a.hobo_city',
        'a.hobo_pin_zip',
        'a.hobo_state',   // assumed to be state_id
        'a.hobo_country', // assumed to be countryid
    ]);
    $builder->join("hoboaddrmt a", "h.hobo_id = a.hobo_id AND a.cmp_id = " . (int)$comp_id, 'left');
    $builder->where('h.cmp_id', $comp_id);

    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $countBuilder  = clone $builder;
    $total_Records = $countBuilder->countAllResults(false);

    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_Records && $total_Records > 0) {
        $pq_curPage = (int)ceil($total_Records / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    $builder->orderBy('h.hobo_name');
    $builder->limit($pq_rPP, $offset);
    $result = $builder->get()->getResultArray();

    $records = [];
    foreach ($result as $values) {
        $checkbox_html = '<input name="batches_ids[]" class="checkbox batches_row" data-id="' . $values['hobo_id'] . '" type="checkbox" value="' . $values['hobo_id'] . '">';

        $stateName   = '';
        if (!empty($values['hobo_state']) && isset($stateMap[$values['hobo_state']])) {
            $stateName = $stateMap[$values['hobo_state']];
        }

        $cityName = $values['hobo_city'] ?? '';

        $name = ucwords($values['hobo_name']);
        if ((int)$values['mark_ho'] === 1) {
            $name = '<strong>' . $name . ' (HO)</strong>';
        }

        $records[] = [
            'chkbx'         => $checkbox_html,
            'acc_id'        => $values['hobo_id'],
            'account_name'  => $name,
            'account_alias' => $values['hobo_alias'],
            'city_name'     => $cityName,
            'state_name'    => $stateName,
            'isedited'      => ($values['mark_ho'] == 1) ? 0 : 1
        ];
    }

    echo "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($records) . "}";
}
	 
	 
   function account_info($account_id){	 
       $builder= $this->univaictly->table('hobomaster');
	   $builder->join('hoboaddrmt','hoboaddrmt.hobo_id=hobomaster.hobo_id','left');
	   $builder->where('hobomaster.hobo_id', $account_id);
	   $builder->where('hobomaster.cmp_id', $this->company_id);
	   $result =$builder->get()->getRowArray();  
       return 	$result;		  
    }
   
   function branch_gsttins_info($bo_id){
	 $gstinmastr_tbl = 'hobogstinm';  
	 $builder = $this->univaictly->table($gstinmastr_tbl);
	 $builder->join('hobogstdet','hobogstdet.hobo_id=hobogstinm.hobo_id','left');
	 $builder->where('hobogstinm.hobo_id', $bo_id);
	 $builder->where('hobogstinm.cmp_id', $this->company_id);
	 $result = $builder->get()->getResultArray();
	 return $result;
    }  
 
 function get_tan_info($bo_id){
	 $tanmastern_tbl = 'hobotanmst';  
	 return $this->univaictly->table($tanmastern_tbl)
	        ->where('hobo_id', $bo_id)
			 ->where('cmp_id', $this->company_id)
			->get()->getRowArray();
    } 
	
   
   
}