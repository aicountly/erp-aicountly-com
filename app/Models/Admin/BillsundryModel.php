<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\UUIDtables;
use App\Libraries\ERPtables;

class BillsundryModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->fy_id         = $this->session->get('ses_comp_fy_id');
       $this->bo_id         = $this->session->get('ses_boid'); 
       $this->billsundry_nature_no =[1=>'Round off (+)',2=>'Round off (-)',3=>'Discount (-)',4=>'Others'];
    }  
	
	public function checkTaxMasterBSD($io_type,$tax_sub_type){
		$data = $this->db->table('billsundry b')
                ->select('1')
                ->join('acctmaster a', 'a.bsd_id = b.bsd_id') // must have account
                ->where([
                    'b.cmp_id'            => $this->company_id,
                    'b.bsd_type'          => 1, //  ONLY TAXABLE
                    'b.tax_cat_type'      => 1,
                    'b.tax_cat_sub_type'  => $tax_sub_type,
                    'b.bsd_input_output'  => $io_type
                ])
                ->limit(1)
                ->get()
                ->getRowArray();
				
				
				return $data;
		
	}
	
   public function checkTaxMasterByType($bsd_type, $bsd_input_output, $tax_cat_type, $tax_cat_sub_type)
{
    return $this->db->table('billsundry b')
        ->select('1')
        ->join('acctmaster a', 'a.bsd_id = b.bsd_id') // ✅ INNER JOIN (must exist)
        ->where([
            'b.bsd_type'          => $bsd_type,
            'b.bsd_input_output'  => $bsd_input_output,
            'b.tax_cat_type'      => $tax_cat_type,
            'b.tax_cat_sub_type'  => $tax_cat_sub_type,
            'b.cmp_id'            => $this->company_id, // ✅ IMPORTANT
            'a.cmp_id'            => $this->company_id  // ✅ IMPORTANT
        ])
        ->limit(1) // ✅ only existence
        ->get()
        ->getRowArray(); // ✅ return single row or null
}


public function isMappedCorrectly($bsdRows, $duties_group, $fy_id)
{
    if (empty($bsdRows)) return false;

    $account_ids = array_filter(array_column($bsdRows, 'acc_id'));

    if (empty($account_ids)) {
        return false;
    }

    $rows = $this->db->table('undercrsmt')
        ->select('crs_mst_id, under_crs_mst_id')
        ->where('crs_mst_type', 14)
        ->whereIn('crs_mst_id', $account_ids)
        ->where('cmpfymastr_id', $fy_id)
        ->get()
        ->getResultArray();

    if (empty($rows)) {
        return false;
    }

    // 🔥 IMPORTANT CHANGE: ALL must match (not ANY)
    foreach ($account_ids as $acc_id) {

        $found = false;

        foreach ($rows as $row) {
            if (
                (int)$row['crs_mst_id'] === (int)$acc_id &&
                (int)$row['under_crs_mst_id'] === (int)$duties_group
            ) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            return false; // ❌ even one wrong → fail
        }
    }

    return true; // ✅ all correct
}
   function main_group_info($acc_grp_id){
     $builder = $this->db->table('accgrpmstn acgrpmst');
	 $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     $builder->where('acgrpmst.cmp_id',$this->company_id);
	 $builder->where('undercrsmt.crs_mst_id', $acc_grp_id);
	 return $builder->get()->getRowArray();   	   
   }
   
  /**
 * Check which bill sundry names exist for the current company.
 * Single query instead of looping 10 times.
 * 
 * @param array $names Array of lowercase bill sundry names
 * @return array Rows with acc_name that exist
 */
public function getExistingBillSundryNames(array $names): array
{
    if (empty($names)) {
        return [];
    }

    // Build placeholders manually to use LOWER() safely with PostgreSQL
    $placeholders = implode(',', array_fill(0, count($names), '?'));

    $sql = "SELECT LOWER(bsm.acc_name) AS acc_name
            FROM acctmaster bsm
            WHERE bsm.bsd_id IS NOT NULL
              AND bsm.cmp_id = ?
              AND LOWER(bsm.acc_name) IN ({$placeholders})";

    $bindings = array_merge([$this->company_id], $names);

    return $this->db->query($sql, $bindings)->getResultArray();
}

   function validateBillSundryMaster($bill_sundry_name,$sundry_nature, $tax_account,$tax_supply, $tax_catd,$io_type,$cat_type,$sub_type,$sel_acc_id=0,$billsundry_id=0) {
    $acc_master_tbl = 'acctmaster';
	$billsundry_tbl = 'billsundry';	
	$error_message='';
	if($sel_acc_id==0){
	  //  Prevent Duplicate Bill Sundry Name
	$existing = $this->db->table($acc_master_tbl.' bsm')
        ->select('bsm.acc_name')
        ->where('LOWER(bsm.acc_name)', strtolower($bill_sundry_name))
		->where('bsm.bsd_id IS NOT NULL')
		->where('bsm.cmp_id',$this->company_id)
		->get()
        ->getRowArray();	
    if ($existing) {
        return [
            "status" => false,
            "message" => "A Bill Sundry '{$bill_sundry_name}' name already exists.",
            "conflict" => $existing
        ];
    }
	
	if($tax_account==1){ // Tax Account Yes
		//  1. Prevent Duplicate Bill Sundry for the Same Nature & same IO Type & same cat type & sub type
    
		$error_message = "A Bill Sundry already exists in same category & IO Types";
     	$existing = $this->db->table($acc_master_tbl.' bsm')
		->join($billsundry_tbl.' bsd', 'bsd.bsd_id = bsm.bsd_id', 'left')
        ->select('bsm.acc_name, bsd.bsd_input_output') 
        ->where('LOWER(bsm.acc_name)', strtolower($bill_sundry_name))			
        ->where('bsd.bsd_input_output', $io_type)
        ->where('bsd.tax_cat_type', $cat_type)
        ->where('bsd.tax_cat_sub_type', $sub_type)
		->where('bsm.cmp_id',$this->company_id)
        ->countAllResults();	
		if ($existing >0){
			return [
				"status" => false,
				"message" => $error_message,
				"conflict" => $existing
			];
		}
	}
	if($tax_account==0){// Tax Account No
		// 1. Restrict Duplicate Tax Account in Same Tax Category
	$existing_same_category = $this->db->table($acc_master_tbl.' bsm')
		->join($billsundry_tbl.' bsd', 'bsd.bsd_id = bsm.bsd_id', 'left')
		->where('bsm.tax_cat_mst_id', $tax_catd)
        ->where('LOWER(bsm.acc_name)', strtolower($bill_sundry_name))	  		
		->where('bsm.cmp_id',$this->company_id)		
        ->get()
        ->getRowArray();
    if ($existing_same_category) {
        return [
            "status" => false,
            "message" => "A Tax Account already exists for same Category ",
            "conflict" => $existing_same_category
        ];
    }
    
    }	
	}
	else{
		 //  Prevent Duplicate Bill Sundry Name
		$existing = $this->db->table($acc_master_tbl.' bsm')
			->select('bsm.acc_name')
			->where('LOWER(bsm.acc_name)', strtolower($bill_sundry_name))
			->where('bsm.bsd_id IS NOT NULL')
			->where('bsm.cmp_id',$this->company_id)
			->where('acc_id !=',$sel_acc_id)
			->get()
			->getRowArray();	
			
		if ($existing) {
			return [
				"status" => false,
				"message" => "A Bill Sundry '{$bill_sundry_name}' name already exists.",
				"conflict" => $existing
			];
		}
		
		if($tax_account==1){ // Tax Account Yes
			//  1. Prevent Duplicate Bill Sundry for the Same Nature & same IO Type
		
			$error_message = "A Bill Sundry already exists in same category & IO Types";
			$existing = $this->db->table($acc_master_tbl.' bsm')
			->join($billsundry_tbl.' bsd', 'bsd.bsd_id = bsm.bsd_id', 'left')
			->select('bsm.acc_name, bsd.bsd_input_output') 
			->where('LOWER(bsm.acc_name)', strtolower($bill_sundry_name))	
			->where('bsm.cmp_id',$this->company_id)
			->where('bsd.bsd_input_output', $io_type)
			->where('bsm.acc_id !=',$sel_acc_id)
			->countAllResults();	
			
			
			if ($existing >0){
				return [
					"status" => false,
					"message" => $error_message,
					"conflict" => $existing
				];
			}
		}
		if($tax_account==0){// Tax Account No
			// 1. Restrict Duplicate Tax Account in Same Tax Category
		$existing_same_category = $this->db->table($acc_master_tbl.' bsm')
			->join($billsundry_tbl.' bsd', 'bsd.bsd_id = bsm.bsd_id', 'left')
			->select('bsm.acc_name, bsd.bsd_input_output')  
			->where('LOWER(bsm.acc_name)', strtolower($bill_sundry_name))	
			->where('bsm.tax_cat_mst_id', $tax_catd)	
			->where('bsm.cmp_id',$this->company_id)			
			->where('bsm.acc_id !=',$sel_acc_id)
			->countAllResults();
		if ($existing_same_category>0) {
			return [
				"status" => false,
				"message" => "A Tax Account already exists for same Category",
				"conflict" => $existing_same_category
			];
		}
		
		}	
	}
	// If no conflicts found, allow creation
    return ["status" => true];
}

public function addbsd_data_vouchersave($data)
{
    $name = strtolower(trim($data['bsd_name']));

    // ================= STEP 1: CHECK EXACT TAXABLE (NAME + TYPE) =================
    $existing = $this->db->table('acctmaster am')
        ->select('am.acc_id, b.bsd_id')
        ->join('billsundry b', 'b.bsd_id = am.bsd_id')
        ->where([
            'am.cmp_id'            => $this->company_id,
            'LOWER(am.acc_name)'   => $name,
            'b.bsd_type'           => 1,
            'b.tax_cat_type'       => 1,
            'b.tax_cat_sub_type'   => $data['tax_cat_sub_type'],
            'b.bsd_input_output'   => $data['bsd_input_output']
        ])
        ->limit(1)
        ->get()
        ->getRowArray();

    if ($existing) {
        return [
            'status'        => true,
            'account_id'    => $existing['acc_id'],
            'billsundry_id' => $existing['bsd_id'],
            'action'        => 'existing'
        ];
    }

    // ================= STEP 2: CHECK NAME CONFLICT =================
    $nameExists = $this->db->table('acctmaster')
        ->where([
            'cmp_id' => $this->company_id,
            'LOWER(acc_name)' => $name
        ])
        ->get()
        ->getRowArray();

    // If same name exists → create new unique name
    $finalName = $data['bsd_name'];
    if ($nameExists) {
        $finalName = $data['bsd_name'];
    }

    // ================= STEP 3: INSERT billsundry =================
    $bsd_insert = [
        'cmp_id'           => $this->company_id,
        'bsd_type'         => 1,
        'bsd_input_output' => (int)$data['bsd_input_output'],
        'bsd_nature'       => $data['bsd_nature'] ?? 0,
        'tax_cat_type'     => 1,
        'tax_cat_sub_type' => (int)$data['tax_cat_sub_type']
    ];

    $this->db->table("billsundry")->insert($bsd_insert);
    $bsd_id = $this->db->insertID();

    // ================= STEP 4: INSERT acctmaster =================
    $acc_insert = [
        'cmp_id'         => $this->company_id,
        'acc_name'       => clean($finalName),
        'acc_alias'      => clean($finalName),
        'acc_print_name' => clean($finalName),
        'bsd_id'         => $bsd_id,
        'tax_cat_mst_id' => $data['tax_cat_mst_id'] ?? 0,
        'acc_is_active'  => 1,
        'acc_is_restrict'=> $data['acc_is_restrict'] ?? 1
    ];

    $this->db->table("acctmaster")->insert($acc_insert);
    $account_id = $this->db->insertID();

    // ================= STEP 5: INSERT acctmstdet =================
    $this->db->table('acctmstdet')->insert([
        'cmp_id'         => $this->company_id,
        'acc_id'         => $account_id,
        'acc_is_sys_acc' => 1,
        'acc_is_sez'     => 0
    ]);

    return [
        'status'        => true,
        'account_id'    => $account_id,
        'billsundry_id' => $bsd_id,
        'action'        => 'created',
        'name_used'     => $finalName
    ];
}

public function addbsd_data($data){	   
    $builder   =  $this->db->table("acctmaster") ; 	      
	$builder->join(
			"undercrsmt",
			" undercrsmt.crs_mst_id = acctmaster.acc_id 
			AND 
			 undercrsmt.crs_mst_type = 14
			AND 
			undercrsmt.cmpfymastr_id = $this->fy_id
			AND 
			undercrsmt.cmp_id = $this->company_id
			",
			'left'
		);   	
		$builder->where('LOWER(acctmaster.acc_name)', strtolower(trim($data['bsd_name'])));
		$builder->where('acctmaster.acc_is_active',1);
		$builder->where('acctmaster.cmp_id',$this->company_id);
		$builder->where('acctmaster.bsd_id IS NOT NULL'); 	   
		$table = $builder->get()->getRowArray();     	      
        if($table){
            return ['status' => false, 'message' => 'Name must be unique'];
        }        
                
         $builder   =  $this->db->table("acctmaster") ; 	      
		 $builder->join(
			"undercrsmt",
			" undercrsmt.crs_mst_id = acctmaster.acc_id 
			AND 
			 undercrsmt.crs_mst_type = 14
			AND 
			undercrsmt.cmpfymastr_id = $this->fy_id
			AND 
			undercrsmt.cmp_id = $this->company_id
			",
			'left'
		);   	
		$builder->where('LOWER(acctmaster.acc_alias)', strtolower(trim($data['bsd_alias'])));
		$builder->where('acctmaster.acc_is_active',1);
		$builder->where('acctmaster.bsd_id IS NOT NULL');
		$builder->where('acctmaster.cmp_id',$this->company_id);
		$table = $builder->get()->getRowArray(); 
         	   
        if($table){
            return ['status' => false, 'message' => 'Alias must be unique'];
        }
    $bsd_insert_data   = [
		                'cmp_id'           => (int)$this->company_id,
						'bsd_type'         => (int)$data['bsd_type'],
						'bsd_input_output' => (int)$data['bsd_input_output'],
						'bsd_nature'       => (int)$data['bsd_nature'],
						'tax_cat_type'     => (int)$data['tax_cat_type'],
						'tax_cat_sub_type' => (int)$data['tax_cat_sub_type']						
						];
	  $this->db->table("billsundry")->insert($bsd_insert_data);
	  $billsundry_id         =  $this->db->insertID();
	  
	  $acc_insert_data   = [
							'cmp_id'            => (int)$this->company_id,
							'acc_name'          => clean($data['bsd_name']),
							'acc_alias'         => clean($data['bsd_alias']),
							'acc_print_name'    => clean($data['bsd_print_name']),							
							'contact_id'        => (int)0,
							'bsd_id'            => (int)$billsundry_id,
							'tax_cat_mst_id'   =>  (int)$data['tax_cat_mst_id'] ?? 0,
							'acc_is_active'    => 1,
							'acc_is_restrict'  => $data['acc_is_restrict'] ?? 0							
							];
        $this->db->table("acctmaster")->insert($acc_insert_data);		 
        $account_id = $this->db->insertID();
		
		// insert into account details
			$pldetailData = [
                'cmp_id'          => $this->company_id,
                'acc_id'          => $account_id,
                'acc_is_sys_acc'  => 1,
                'acc_is_sez'      => 0				
            ];
            $this->db->table('acctmstdet')->insert($pldetailData);
			
	  
      if($data['bsd_type']==0){
    	 $insert_bsdconfign= array("cmp_id"=>$this->company_id,"bsd_id"=>$billsundry_id,"bsd_def_val"=>0,
    	                           "bsd_base"=>$data['bsd_base'],
    	                           "bsd_taxable_type"=>$data['bsd_taxable_type'],
								   "bsd_hsn_sac"=>$data['bsd_hsn_sac']);
         $this->db->table("bsdconfign")->insert($insert_bsdconfign);
      }
	  return ['status' => true, 'account_id' => $account_id,'billsundry_id' => $billsundry_id];
	     
     }
    
    function insert_acc_op_bal($data){
	    $accoppybal_tbl = "accoppybal";
	   	$this->db->table($accoppybal_tbl)->insert($data);			
    }
    
    function insert_acc_txn_entry($txn_data){
	  	$this->db->table("accttxnmst")->insert($txn_data);	
    }
    
   function add_undercrsmt($txn_data){
	  	$this->db->table("undercrsmt")->insert($txn_data);	
    }
	
	public function get_dutiestaxes_group_info($group_name = 'Duties & Taxes')
		{
			$builder = $this->db->table('accgrpmstn');

			$builder->select('acc_grp_id')
				->where('cmp_id', $this->company_id) 
				->where("
					LOWER(REPLACE(acc_grp_name, '&amp;', '&')) = 
					LOWER(REPLACE(" . $this->db->escape($group_name) . ", '&amp;', '&'))
				", null, false)
				->limit(1);

			$result = $builder->get()->getRowArray();

			return $result['acc_grp_id'] ?? 0;
		}
	 
	function tax_category_dropdown($tax_cat_type=''){
	    $builder = $this->db->table("taxcatmstn t")
            ->select("t.tax_cat_is_active,t.tax_cat_mst_id, t.tax_cat_name, t.tax_cat_type, t.tax_cat_section, r.tax_cat_rate")
            ->join("taxcatrate r", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "left")
            ->where("t.cmp_id", $this->company_id)
            ->where("t.tax_cat_is_active", 1);
         if($tax_cat_type!='')   
            $builder->where("t.tax_cat_type", $tax_cat_type);
            $data  = $builder->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
		      $rate = parseAmount($row['tax_cat_rate']);
              $final_result[$row['tax_cat_mst_id']] = strtoupper(strtolower($row['tax_cat_name']));		   
	        }
        }
	 
	  return $final_result;	
     }
	
	 function get_tax_info($bill_sundry_id){
	   $bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
       $response = $this->db->table($bdstaxmstn_tbl)->where('bill_sundry_id', $bill_sundry_id)->get()->getRowArray();   	   
	   if($response)
		   return $response;
	   else 
		    return false;
    }
	
	function bsdconfign_info($bsd_id){
	  return $this->db->table("bsdconfign")->where('cmp_id',$this->company_id)->where('bsd_id', $bsd_id)->get()->getRowArray();   	  
	}
	
	function taxcatrate($tax_cat_mst_id){
	   $builder =  $this->db->table("taxcatrate");
	   $builder->where('cmp_id',$this->company_id);
	   $builder->where('tax_cat_mst_id', $tax_cat_mst_id);
	   $response = $builder->get()->getRowArray();   	  
	   if($response)
		   return $response;
	   else
		   return false;
	}
	
	 function get_cash_groups_list()
      {
        $groups = [23,21];
		$array = $this->get_sub_group_ids_list($groups); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','cash','left')
				->where('cmp_id', $this->company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
        return array_unique($array);
      }
	  
	  function get_bbb_groups_list()
      {
        $groups = [16,22];
		$array = $this->get_sub_group_ids_list($groups); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')				
				->like('LOWER(acc_grp_name)','trade payable','left')
				->orlike('LOWER(acc_grp_name)','trade receivables','left')
				->where('cmp_id', $this->company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
		
        return array_unique($array);
      }
	  function get_cc_groups_list()
      {
        $groups = [7,11,13];
		$array = $this->get_sub_group_ids_list($groups); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','purchase','left')
				->like('LOWER(acc_grp_name)','direct expenses','left')
				->like('LOWER(acc_grp_name)','indirect expenses','left')
				->where('cmp_id', $this->company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
        return array_unique($array);
      }
	 
function AccountTaxInfo($tax_cat_id){
        $response = $this->db->table('taxcatmstn tc')
                            ->select("
                                MAX(CASE WHEN ts.tax_cat_sub_type = 1 THEN ts.tax_cat_rate END) as igst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 2 THEN ts.tax_cat_rate END) as cgst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 3 THEN ts.tax_cat_rate END) as sgst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 4 THEN ts.tax_cat_rate END) as ugst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 5 THEN ts.tax_cat_rate END) as cess
                            ")
                            ->join('taxcatrate ts', 'ts.tax_cat_mst_id = tc.tax_cat_mst_id', 'left')
                            ->where('tc.tax_cat_mst_id', $tax_cat_id)
                            ->where('tc.tax_cat_is_active', 1)
                            ->get()
                            ->getRowArray();
                            
        return  $response ;        
        
    }	 
	 function company_all_bsd(){
		 $this->fy_id         = $this->session->get('ses_comp_fy_id');
	    $this->company_id    = $this->session->get('ses_company_id');
		 $cess_basis         = 1;	
	    $bbb_groups  = $this->get_bbb_groups_list();
	    $cc_groups   = $this->get_cc_groups_list();
	    $cash_groups = $this->get_cash_groups_list();
	    $final_result    = array();
        $company_id      = $this->company_id;
        $undercrsmt_tbl  = "undercrsmt";
		$acctmaster_tbl  = "acctmaster";
		$bsdmaster_tbl   = "billsundry";
	    $builder = $this->db->table($acctmaster_tbl);
		$builder->join($undercrsmt_tbl, "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id AND $undercrsmt_tbl.crs_mst_type =14", 'left');
		$builder->join($bsdmaster_tbl, "$bsdmaster_tbl.bsd_id = $acctmaster_tbl.bsd_id", 'left');
		
		$builder->select([
			"$undercrsmt_tbl.crs_mst_parent_id as acc_grp_parent_id",
			"$undercrsmt_tbl.under_crs_mst_id as acc_grp_id",
			"$acctmaster_tbl.acc_name as label",
			"$acctmaster_tbl.acc_name as value",
			"$acctmaster_tbl.acc_id",
			"$bsdmaster_tbl.bsd_id",
			"$acctmaster_tbl.tax_cat_mst_id",
			"$bsdmaster_tbl.bsd_input_output",
			"$bsdmaster_tbl.bsd_nature",
			"$bsdmaster_tbl.bsd_type"
			
		]);
		$builder->where("$undercrsmt_tbl.crs_mst_parent_id !=",14);
		$builder->where("$acctmaster_tbl.cmp_id ",$this->company_id);
        $builder->where("$undercrsmt_tbl.cmpfymastr_id ",$this->fy_id);		
		$builder->orderBy("$acctmaster_tbl.acc_name");
		$bsd_data = $builder->get()->getResultArray();		
	    $final_result = array();
	   if($bsd_data){
		  foreach($bsd_data as $key => $row){		
		    $bsd_input_output = $row['bsd_input_output'];    
		    $bsdconfign_info  = $this->bsdconfign_info($row['bsd_id']);    
		    $tax_cat_mst_id   = $row['tax_cat_mst_id'];
		    $bill_supply_type = $bsdconfign_info['bsd_taxable_type'] ?? '';
		    $hsn_sac          = $bsdconfign_info['bsd_hsn_sac'] ?? '';
		    $taxcatrate_info  = $this->taxcatrate($tax_cat_mst_id);
			if($taxcatrate_info){
		    $tax_rate         = $taxcatrate_info['tax_cat_rate'] ?? '';
		   }
			else{
			$tax_rate          ='';	
			}
			
			$account_tax_info = $this->AccountTaxInfo($tax_cat_mst_id);
	     if($account_tax_info){
	          $igst_rate  = $account_tax_info['igst']; 
	          $cess_rate  = $account_tax_info['cess']; 
	          $cgst_rate  = $account_tax_info['cgst'];
	          $sgst_rate  = $account_tax_info['sgst'];
	     } else{
	          $igst_rate  = 0; 
	          $cess_rate  = 0; 
	          $cgst_rate  = 0;
	          $sgst_rate  = 0;
	       }   
			
		    $is_bbb = 0;
	       	if(in_array($row['acc_grp_id'], $bbb_groups) || in_array($row['acc_grp_parent_id'], [16,22,4,5] )){
	           	$is_bbb = 1;
	       	}
	       	
	       $is_cc = 0;
	       	if(in_array($row['acc_grp_id'], $cc_groups) || in_array($row['acc_grp_parent_id'], [7,11,13])){
	          	$is_cc = 1;
	       	}
       	
	       	$is_cash = 0;
	       	if(in_array($row['acc_grp_id'], $cash_groups)){
	          	$is_cash = 1;
	       	}	    

        $final_result[] = array(
               			"label"         => ucwords($row['label']),
               			'value'         => $row['value'],
               			'id'            => $row['acc_id'],
               			'account_id'    => $row['acc_id'],
               			'acc_id'        => $row['acc_id'],
						'is_sundry'     => '1',
						'is_acc'        => '0',
						'is_bbb'        => 0,
						'is_cc'	        => $is_cc,						
						'tx_ct_id'	    => $tax_cat_mst_id,
						'bl_sply_tpe'	=> $bill_supply_type,
						'bl_hsn_sac'	=> $hsn_sac,
						'bl_tx_rte'     => parseAmount($tax_rate),
						'bl_ipt_ott'	=> $bsd_input_output,
						'bl_nature'     => $row['bsd_nature'],
						'tax_cat_id'     => $tax_cat_mst_id ?? 0 ,
						'is_tax_account'=> ($row['bsd_type']==1) ? 1 : 0
               		  );	
		           }
        	}
			 return json_encode($final_result, JSON_PRETTY_PRINT);
	  //CreateJsonFile($this->fy_id,$this->company_id,'bsd',json_encode($final_result));		    
     }
   
	   function get_sub_group_ids_list($array){    	
		if(!empty($array)){
			$builder = $this->db->table("accgrpmstn acgrpmst");
			
			// 1. The join condition is now cleaner.
			$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type = 2", 'left');
			
			// 2. Use a separate "where" clause for the company ID. This is safer and prevents syntax errors.
			// CodeIgniter's query builder will correctly add this to the "ON" clause for a LEFT JOIN.
			$builder->where('undercrsmt.cmp_id', $this->company_id);
			
			$builder->select('acgrpmst.acc_grp_id');
			$builder->whereIn('undercrsmt.under_main_id', $array);
			
			$data = $builder->get()->getResultArray();

			if($data){
				foreach($data as $key => $value) {
					// Use array_column to get all IDs at once, which is more efficient.
					$new_ids = array_column($data, 'acc_grp_id');
					// Merge the new IDs into the original array.
					$array = array_merge($array, $new_ids);
					// Break the loop as we've processed all results.
					break; 
				}
			}
		}
		return $array;
	}       
  
  	function group_primary_dropdown(){		      
        $data =  $this->db->table("grpparentn")->where('acc_grp_parent_id !=',14)->orderBy('acc_grp_parent_name')->get()->getResultArray();
		$final_result = array();
		$final_result['']  = 'Choose';
		if($data){
		  foreach($data as $row){
		    $final_result[$row['acc_grp_parent_id']] = ucwords($row['acc_grp_parent_name']);			   
		  }
		}
	  return $final_result;	
     }
     
     
     public function crsmst_info($account_id){
	   $undercrsmt_tbl = "undercrsmt";	 
	 return  $this->db->table($undercrsmt_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('crs_mst_id',$account_id)
			   ->where('crs_mst_type',14)
			   ->where('cmpfymastr_id ',$this->fy_id)
        	   ->get()->getRowArray();  
   }

	function group_main_dropdownnn($group_id = 0){		      
            $builder =  $this->db->table("accgrpmstn acgrpmst");
			$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     		$builder->select("undercrsmt.crs_mst_parent_id as acc_grp_parent_id,acgrpmst.acc_grp_id,acgrpmst.acc_grp_name");
     		$builder->where('acgrpmst.cmp_id',$this->company_id);
			if($group_id != 0)
				$builder->where('undercrsmt.under_crs_mst_id !=', $group_id);
            $builder->where('undercrsmt.crs_mst_parent_id !=',14);  // emit branch parent group id
			$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
			$builder->orderBy('acgrpmst.acc_grp_name');
			$data = $builder->get()->getResultArray();				
	  return $data;	
     }
     
	function group_main_dropdown($group_id = 0){		      
            $builder =  $this->db->table("accgrpmstn acgrpmst");
			$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     		$builder->where('acgrpmst.cmp_id',$this->company_id);
			if($group_id != 0)
				$builder->where('undercrsmt.under_crs_mst_id !=', $group_id);
            $builder->where('undercrsmt.crs_mst_parent_id !=',14);  // emit branch parent group id
			$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
			$builder->orderBy('acgrpmst.acc_grp_name');
			$data = $builder->get()->getResultArray();
			$final_result = array();
			$final_result['']  = 'Choose';
			if($data){
			   foreach($data as $row){
					  $final_result[$row['acc_grp_id']] = ucwords($row['acc_grp_name']);			   
					}
				}
	  return $final_result;	
     }
    
    public function billsundry_accounts_dropdown(){
        $company_id = $this->company_id;
        $account_master_tbl = $company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name'));
        $builder->where('comp_id', $company_id);		
		$builder->whereIn('acc_grp_id',array('8','12','14','9','10','11','13') );
		$result = $builder->get()->getResultArray(); 
		$final_list= array();
		$final_list['']='Choose';
		if($result){
		    foreach($result as $row){
		        
		        $final_list[$row['acc_id']]=$row['acc_name'];
		    }
		    
		}
	   return $final_list;	
    }
    
    function get_branch_groups_list()
    {
      
		$p_groups = [14];
		$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($tbl_name)
	   					 ->select('acc_grp_id')
	   					 ->whereIn('acc_grp_parent_id', $p_groups)
	   					 ->get()->getResultArray();
        $final = [];
        if($data){
        	$final = array_column($data, 'acc_grp_id');
        }
        
        return $final;
    }
    
    public function acc_opn_balance_info($acc_id){
       $accoppybal_tbl ='accoppybal';
	   return $this->db->table($accoppybal_tbl)
	         ->where('hobo_id', $this->session->get('ses_boid'))
			 ->where('cmp_id', $this->company_id)
			 ->where('bsd_id', $acc_id)->get()->getRowArray(); 
	  } 
	  
    public function ajax_billsundry_list(){
        $account_master_tbl      = 'acctmaster'; 
		$account_groupn_tabl     = 'accgrpmstn';
		$account_opbalance_tabl  = 'accoppybal';
        $group_parent_tbl        = 'grpparentn';
		$undercrsmt_tbl          = "undercrsmt";	
		$taxcatrate_tbl          = "taxcatrate";		
		$billsundry_tbl          = "billsundry";
		
		if(isset($_POST["pq_filter"])){
	       $pq_filter     = $_POST["pq_filter"];	       
	       $filter_data  = json_decode($_POST["pq_filter"],true);
	      $pq_filters   = $filter_data['data'][0];	    
	       if(isset($pq_filters['condition']))
	         $condition   = $pq_filters['condition'];
	      else
		     $condition='';
	      $search_text = strtolower($pq_filters['value']);
	      $dataIndx    = $pq_filters['dataIndx'];  	       
	    }
	    else{
	      $pq_filter     ='';
	      $search_text   ='';
	      $dataIndx      ='';
	      $condition='';
	    }
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	    	    
		// Get pagination params safely
		$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page

		
		if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;
		
		$builder = $this->db->table($account_master_tbl);

		// Select fields
		$builder->select("
			$account_master_tbl.*, 
			$account_opbalance_tabl.acc_op_bal, 
			$undercrsmt_tbl.under_crs_mst_id,
			$undercrsmt_tbl.crs_mst_parent_id,
			$undercrsmt_tbl.crs_is_active,
			$account_groupn_tabl.acc_grp_name AS acc_group_name,
			$group_parent_tbl.acc_grp_parent_name AS acc_parent_group_name,
			$billsundry_tbl.bsd_type,
			$billsundry_tbl.bsd_nature,
			$billsundry_tbl.bsd_input_output		   
		");

		// Joins
    $builder->join(
        $account_opbalance_tabl,
        "{$account_opbalance_tabl}.acc_id = {$account_master_tbl}.acc_id
         AND {$account_opbalance_tabl}.cmpfymastr_id = {$this->fy_id}
         AND {$account_opbalance_tabl}.hobo_id = " . $this->db->escape($this->bo_id),
        'left'
    );
		
		$builder->join(
        $undercrsmt_tbl,
        "{$undercrsmt_tbl}.crs_mst_id = {$account_master_tbl}.acc_id
         AND {$undercrsmt_tbl}.crs_mst_type = 14
         AND {$undercrsmt_tbl}.cmpfymastr_id = {$this->fy_id}
         AND {$undercrsmt_tbl}.cmp_id = {$this->company_id}",
        'left'
    );
		// Apply filters
		//$builder->where("$undercrsmt_tbl.crs_mst_parent_id !=",14);

		$builder->join(
			$account_groupn_tabl,
			"$account_groupn_tabl.acc_grp_id = $undercrsmt_tbl.under_crs_mst_id",
			'left'
		);
		
		$builder->join(
			$billsundry_tbl,
			"$billsundry_tbl.bsd_id = $account_master_tbl.bsd_id",
			'left'
		);

		$builder->join(
			$group_parent_tbl,
			"$group_parent_tbl.acc_grp_parent_id = $undercrsmt_tbl.crs_mst_parent_id",
			'left'
		);
		
		$builder->where("$account_master_tbl.cmp_id", $this->company_id);
		$builder->where("$account_master_tbl.bsd_id IS NOT NULL");
		$builder->where("{$undercrsmt_tbl}.cmpfymastr_id IS NOT NULL");
        $builder->where("{$undercrsmt_tbl}.cmpfymastr_id", $this->fy_id);
		
		$countBuilder = clone $builder;
		$total_Records = $countBuilder->countAllResults(false);

		
		$offset = ($pq_rPP * ($pq_curPage - 1));
		if ($offset > $total_Records) {
			$pq_curPage = ceil($total_Records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}
		if ($offset < 0) {
			$offset = 0;
		}
		$builder->orderBy("$account_master_tbl.acc_name");
	
		$builder->limit($pq_rPP, $offset);
        $result  = $builder->get()->getResultArray();
		
        $records=[];
		foreach($result as $values){
			   if ($values['under_crs_mst_id'] == 0) {
				$show_group = $values['acc_parent_group_name'];  // from grpparentn
			} else {
				$show_group = $values['acc_group_name'];         // from accgrpmstn
			}
	      
	             $billsndry_type =($values['bsd_type']==1)?'Taxable Account':'Non Taxable Account';
	             
	             $billsundry_nature_no = $this->billsundry_nature_no;

                if (array_key_exists($values['bsd_nature'], $billsundry_nature_no)) {
                    $billsndry_nature = $billsundry_nature_no[$values['bsd_nature']];
                } else {
                    $billsndry_nature = '';
					
					
                }
				
				$bsd_input_output ='';
				if($values['bsd_input_output']==1)
				$bsd_input_output = 'Input';
				else if($values['bsd_input_output']==2)
				$bsd_input_output = 'Output';
                
				 $records[] = array(	
			          'chkbx' => '<input name="account_ids[]" class="checkbox accounts_row" data-confirmstatus= "'.($values['crs_is_active']==1)?'INACTIVE':'ACTIVE'.'" data-id="'.$values['acc_id'].'" type="checkbox" value="'.$values['acc_id'].'">',           
 			          'bsd_id'       => $values['bsd_id'],
 			          'acc_id'       => $values['acc_id'],
                      'billsndry_name' => ($bsd_input_output > 0)? ucwords($values['acc_name']).' - '.$bsd_input_output:ucwords($values['acc_name']),					  
					  'billsundry_group_name'   => $show_group,
                      'bsd_op_bal'       => formatAmount(abs($values['acc_op_bal'])),
					  'op_bal_export'       => abs($values['acc_op_bal']),	
                      'bsd_op_bal_drcr'     => ($values['acc_op_bal']<0)?'CR.':'DR.',//$acc_op_type,
					  'isedited'     => ($values['acc_is_restrict']==0 )? 1:0,
					  'count_txn'    => 0,	
					  'acc_is_restrict' => $values['acc_is_restrict'],	
					  'acc_status_vl'   => ($values['acc_is_active']==1)?0:1,
					  'acc_status'   => ($values['acc_is_active']==1)?'ACTIVE':'INACTIVE',
					  'alert_acc_status'   => ($values['acc_is_active']==1)?'INACTIVE':'ACTIVE',
					  'billsndry_type' =>$billsndry_type,'billsndry_nature'=>$billsndry_nature
 					  );				
				
		           }	 
     return  "{\"totalRecords\":" .$total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}";        
   	 }

    
   
   public function remove_billsundry($billsundry_id){
       
    	$this->db->table("acctmaster")->where('cmp_id',$this->company_id)->where('bsd_id',$billsundry_id)->delete();
    	$this->db->table("billsundry")->where('cmp_id',$this->company_id)->where('bsd_id',$billsundry_id)->delete();
    	$this->db->table("accoppybal")->where('cmp_id',$this->company_id)->where('bsd_id',$billsundry_id)->delete();
    	$this->db->table("bsdconfign")->where('cmp_id',$this->company_id)->where('bsd_id',$billsundry_id)->delete();
		return TRUE;
	 }
	 
  public function check_billsundry_opn_exists($id){
        $acctmaster_tbl  = "acctmaster";
		$accoppybal_tbl  = "accoppybal";
		
		$builder = $this->db->table($accoppybal_tbl);
		$builder->join($acctmaster_tbl, "$acctmaster_tbl.acc_id = $accoppybal_tbl.acc_id", 'left');
		$builder->where("$acctmaster_tbl.bsd_id", $id);
		$builder->where("$acctmaster_tbl.cmp_id",$this->company_id);
		$builder->where("$accoppybal_tbl.acc_op_bal >",0);
		$builder->where("$accoppybal_tbl.cmpfymastr_id", $this->fy_id);
		$builder->where("$accoppybal_tbl.hobo_id", $this->bo_id);
		$data = $builder->get()->getRowArray(); 
		 if($data)
        return true; 
        else
        return false;
     }
     
   function check_billsundry_with_voucher($id)
     {          
		$acctmaster_tbl  = "acctmaster";
		$accttxnmst_tbl  = "accttxnmst";
		$builder         = $this->db->table($accttxnmst_tbl);
		$builder->join($acctmaster_tbl, "$acctmaster_tbl.acc_id = $accttxnmst_tbl.acc_id", 'left');
		$builder->where("$acctmaster_tbl.acc_id", $id);
		$builder->where("$accttxnmst_tbl.acc_txn_amt >",0);
		$builder->where("$acctmaster_tbl.cmp_id",$this->company_id);
		$builder->where("$accttxnmst_tbl.hobo_id", $this->bo_id);
		$response = $builder->get()->getRowArray();
		if($response){
	         return 1;
	     } 
	     return 0;
     }

  public function billsundry_info($billsundry_id){
    $billsundry_tbl  = "billsundry";
    $acctmaster_tbl  = "acctmaster";
    $undercrsmt_tbl  = "undercrsmt";
    
    $cmpFyId = (int)$this->fy_id; // ✅ current financial year

    $builder = $this->db->table($billsundry_tbl);

    $builder->join(
        $acctmaster_tbl,
        "$acctmaster_tbl.bsd_id = $billsundry_tbl.bsd_id",
        'left'
    );

    // ✅ FIX: Added cmpfymastr_id condition
    $builder->join(
        $undercrsmt_tbl,
        "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id 
         AND $undercrsmt_tbl.crs_mst_type = 14
         AND $undercrsmt_tbl.cmpfymastr_id = {$cmpFyId}",
        'left'
    );

    $builder->where("$billsundry_tbl.bsd_id", $billsundry_id);
    $builder->where("$acctmaster_tbl.bsd_id IS NOT NULL");

    $response = $builder->get()->getRowArray(); 

    if($response){
        $response['bsdconfign_info'] = $this->bsdconfign_info($response['bsd_id']); 
    } else {
        $response = false;
    }

    return $response;  
}
   
   public function check_billsundry_exists($id,$name){
        $data = $this->db->table("acctmaster")->select('acc_name')->where('bsd_id IS NOT NULL')->where('acc_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(acc_name)', strtolower($name))->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
     
   public function changestatus_single_account($id,$acc_id,$status){
    $this->db->table("acctmaster")->where('cmp_id',$this->company_id)->where("bsd_id",$id)->update(["acc_is_active"=>$status]);
    $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$acc_id)->where("crs_mst_type",14)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]);
   
    } 
   
   public function get_name($billsundry_id){
      $row =  $this->db->table("acctmaster")->select('acc_name')->where('bsd_id',$billsundry_id)->get()->getRowArray();  
     
	  if($row)
	  return $row['acc_name'];
	  else
	  return false;
   }
   
   public function update_undercrsmt($data,$account_id,$crs_mst_type){		
	    $undercrsmt_tbl = "undercrsmt";	 
		$table = $this->db->table($undercrsmt_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('crs_mst_id',$account_id)
			   ->where('crs_mst_type',$crs_mst_type)
			   ->where('cmpfymastr_id ',$this->fy_id)
        	   ->get()->getRowArray(); 
		if($table){
		 $updata =array("under_crs_mst_id"=>$data['under_crs_mst_id'],"crs_mst_parent_id"=>$data['crs_mst_parent_id'],
		                "under_main_id"=>$data['under_main_id']);	
		 $this->db->table($undercrsmt_tbl)
		      ->where('cmp_id',$this->company_id)
			  ->where('crs_mst_id',$account_id)
			  ->where('crs_mst_type',$crs_mst_type)
			  ->where('cmpfymastr_id',$this->fy_id)->update($updata);	
		}else{
		 $this->db->table($undercrsmt_tbl)->insert($data);		
		}
		return ['status' => true, 'account_id' => $account_id];	
   } 
   
   public function update_billsundry($data,$billsundry_id,$account_id){
        
		
		
		$upd_data  = array("acc_name"=>clean($data["bsd_name"]),
		                   "acc_alias"=>clean($data["bsd_alias"]),
                           "acc_print_name"=>clean($data["bsd_print_name"]),
						   'tax_cat_mst_id'=> (int)$data['tax_cat_mst_id'] ?? 0
                           );	   
	    $this->db->table("acctmaster")
		      ->where('cmp_id',$this->company_id)
			  ->where('bsd_id',$billsundry_id)->update($upd_data);
			  
		$bsd_up_data   = [
		                'bsd_type'         => (int)$data['bsd_type'],
						'bsd_nature'       => (int)$data['bsd_nature'],
						'bsd_input_output' => (int)$data['bsd_input_output']
						];
	    $this->db->table("billsundry")->where('bsd_id',$billsundry_id)->update($bsd_up_data);
		
		$bsdcnfg_up_data   = [
		                'bsd_base'         => (int)$data['bsd_base'],
						'bsd_taxable_type' => (int)$data['bsd_taxable_type'],
						'bsd_hsn_sac'      => $data['bsd_hsn_sac']						
						];
	    $this->db->table("bsdconfign")->where('bsd_id',$billsundry_id)->update($bsdcnfg_up_data);
		return ['status' => true, 'account_id' => $account_id];	
   }      
   public function update_acc_txn_opbal_entryssss($txn_data,$account_id,$acc_txn_type){		
	    $accttxnmst_tbl = "accttxnmst";
	    $this->db->table($accttxnmst_tbl)->where('cmp_id',$this->company_id)
		->where('acc_id',$account_id)->where('acc_txn_type',$acc_txn_type)
		->where('vch_txn_id IS NULL')->update($txn_data);
		
   }
   public function update_acc_txn_opbal_entry($txn_data, $account_id, $acc_txn_type)
	{
		$accttxnmst_tbl = "accttxnmst";

		// Check if record already exists
		$existing = $this->db->table($accttxnmst_tbl)
			->where('cmp_id', $this->company_id)
			->where('acc_id', $account_id)
			->where('acc_txn_type', $acc_txn_type)
			->where('vch_txn_id IS NULL')
			->get()
			->getRow();

		if ($existing) {
			// Record exists — update it
			$this->db->table($accttxnmst_tbl)
				->where('cmp_id', $this->company_id)
				->where('acc_id', $account_id)
				->where('acc_txn_type', $acc_txn_type)
				->where('vch_txn_id IS NULL')
				->update($txn_data);
		} else {
			// Record does not exist — insert new
			$txn_data['cmp_id']        = $this->company_id;
			$txn_data['acc_id']        = $account_id;
			$txn_data['acc_txn_type']  = $acc_txn_type;
			$txn_data['hobo_id']       = $this->bo_id;
			$txn_data['acc_txn_date']  = date('Y-m-d', strtotime(validate_fy_from_date('')));
			$txn_data['acc_txn_fcy']   = 0;
			// vch_txn_id is intentionally left NULL for opening balance entries

			$this->db->table($accttxnmst_tbl)->insert($txn_data);
		}
	}
   public function update_opbal_entryaaaa($data,$account_id,$bsd_id){		
	    $accoppybal_tbl = "accoppybal";
	    $this->db->table($accoppybal_tbl)->where('cmp_id',$this->company_id)
		->where('hobo_id',$this->bo_id)->where('cmpfymastr_id',$this->fy_id)
		->where('bsd_id',$bsd_id)
		->where('acc_id',$account_id)->update($data);   
   }  
   
  public function update_opbal_entry($data, $account_id, $bsd_id)
	{
		$accoppybal_tbl = "accoppybal";

		// Check if record already exists
		$existing = $this->db->table($accoppybal_tbl)
			->where('cmp_id', $this->company_id)
			->where('hobo_id', $this->bo_id)
			->where('cmpfymastr_id', $this->fy_id)
			->where('bsd_id', $bsd_id)
			->where('acc_id', $account_id)
			->get()
			->getRow();

		if ($existing) {
			// Record exists — update it
			$this->db->table($accoppybal_tbl)
				->where('cmp_id', $this->company_id)
				->where('hobo_id', $this->bo_id)
				->where('cmpfymastr_id', $this->fy_id)
				->where('bsd_id', $bsd_id)
				->where('acc_id', $account_id)
				->update($data);
		} else {
			// Record does not exist — insert new
			$data['cmp_id']         = $this->company_id;
			$data['hobo_id']        = $this->bo_id;
			$data['cmpfymastr_id']  = $this->fy_id;
			$data['bsd_id']         = $bsd_id;
			$data['acc_id']         = $account_id;

			$this->db->table($accoppybal_tbl)->insert($data);
		}
	}
    
}