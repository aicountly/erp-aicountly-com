<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;

class CostCentreModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->session       = \Config\Services::session();
	   $this->CommonModel     =  new CommonModel();
	    $this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
    }
    
    public function ajax_cc(){
		if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	    	    
		// Get pagination params safely
		$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
		
        $undercrsmt_tbl          = "undercrsmt";
	    $account_groupn_tabl     = 'ccgrpmstnn';		
		$builder = $this->db->table("ccmasternn");

		// Select fields
		$builder->select("
			ccmasternn.*, 
			ccoppybaln.cc_op_bal, 
			$undercrsmt_tbl.under_crs_mst_id,
			$undercrsmt_tbl.crs_is_active,
			$account_groupn_tabl.cc_grp_name AS cc_group_name			
		");

		// Joins
		$builder->join(
			"ccoppybaln",
			"ccoppybaln.cc_id = ccmasternn.cc_id AND " .
			"ccoppybaln.hobo_id = " . $this->db->escape($this->bo_id),
			'left'
		);
		
		$builder->join(
			$undercrsmt_tbl,
			"$undercrsmt_tbl.crs_mst_id = ccmasternn.cc_id 
			AND 
			$undercrsmt_tbl.crs_mst_type = 9
			AND 
			$undercrsmt_tbl.cmpfymastr_id = $this->fy_id
			",
			'left'
		);		
		$builder->join(
			$account_groupn_tabl,
			"$account_groupn_tabl.cc_grp_id = $undercrsmt_tbl.under_crs_mst_id",
			'left'
		);
		
		$builder->where("ccmasternn.cmp_id", $this->company_id);
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
		
		$builder->orderBy("ccmasternn.cc_name");
		$builder->limit($pq_rPP, $offset);
        $result  = $builder->get()->getResultArray();
		//echo $this->db->getlastquery();
		//die();
		$records=array(); 		
        foreach($result as $values){
            $cc_grp     = $values['cc_group_name'];          
            $cc_op_bal  = $values['cc_op_bal'];
            $cc_op_drcr = 'DR.';
            if($cc_op_bal < 0){
              $cc_op_drcr = 'CR.';
            }              
			$confirmstatus  = ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE';	
		    $acc_status_vl  = ($values['crs_is_active']==1)?0:1;
            $records[]      = array(	
                'checkbox'  =>'<input name="cc_ids[]" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'" class="checkbox cc_row"  data-id="'.$values['cc_id'].'"  type="checkbox" value="'.$values['cc_id'].'">',
                'cc_id'     => $values['cc_id'],
                'cc_name'   => ucwords($values['cc_name']),
                'cc_alias'  => $values['cc_alias'],
                'cc_grp'    => $cc_grp,
                'cc_op_bal'        => abs($cc_op_bal),
                'cc_op_drcr'       => $cc_op_drcr	,
				'acc_status_vl'    => ($values['crs_is_active']==1)?0:1,
				'cc_status'        => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
				'alert_acc_status' => ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE',	
                );  
           }
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
    }

    public function all_cc_export(){
        
        $tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id'); 
        $builder = $this->db->table($tbl); 
        $builder->orderBy('cc_name');                
        $result = $builder->get()->getResultArray();
        
        $records=array();       
        foreach($result as $values){
            $cc_group_info = $this->cc_group_info($values['cc_grp_id']);
            
            if(isset($cc_group_info['cc_grp_id']))
                $cc_grp = $cc_group_info['cc_grp_name'];
            else
                $cc_grp = ''; 
            
            $records[]  = array(    
 
                'cc_id'     => $values['cc_id'],
                'cc_name'   => ucwords($values['cc_name']),
                'cc_alias'  => $values['cc_alias'],
                'cc_grp'    => $cc_grp,
                'cc_op_bal'  => floatval($values['cc_op_bal']),
                'cc_op_drcr' => $values['cc_op_drcr']
                );  
        }
        

        return $records; 
    }

	 
    public function get_cc_name($id){
        $data = $this->db->table("ccmasternn")->select('cc_name')->where('cc_id', $id)->get()->getRowArray();
        $cc_name = $data['cc_name'];
        return $cc_name;
    }
   
  public function check_cc_exists($id,$name){
        $data = $this->db->table("ccmasternn")->select('cc_name')->where('cc_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(cc_name)', strtolower($name))->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
  public function check_cc_txn_exists($id){
        $data = $this->db->table("cmptxnmstn")->select('txn_id')->where('master_id',$id)->where('master_id_type','acc')->where('cmp_id',$this->company_id)->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     } 	
	
  public function check_cc_opn_exists($id){
       $data = $this->db->table("ccoppybaln")->select('cc_op_bal')->where('cc_op_bal !=',0)->where('cmpfymastr_id',$this->fy_id)->where('cmp_id',$this->company_id)->where('cc_id', $id)->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
         return true; 
        else
         return false;
     }
	public function changestatus_single_accounts($id,$status){
	  $this->db->table("ccmasternn")->where('cmp_id',$this->company_id)->where("cc_id",$id)->update(["cc_is_active"=>$status]);
	  $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",9)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]);
	 } 	 
	 
    public function add_cc($data){	    
        $table = $this->db->table("ccmasternn")
            ->where('cmp_id',$data['cmp_id'])
            ->where('LOWER(cc_name)', strtolower(trim($data['cc_name'])))
			->where('cc_is_active', 1)
            ->get()->getRowArray();                                               
        if($table){
            return "-1";
        }
        $this->db->table("ccmasternn")->insert($data);
        $cc_id = $this->db->insertID();
        return  $cc_id;	     
    }

    function insert_cc_op_bal($data)
    {
      $this->db->table("ccoppybaln")->insert($data);
    }
	
	function insert_cc_txn_entry($data){	    
	   	$this->db->table("cctxnmstnn")->insert($data);			
    }
	
	public function update_cc_txn_opbal_entry($txn_data,$cc_id){		
	    $this->db->table("cctxnmstnn")->where('cmp_id',$this->company_id)
		->where('cc_id',$cc_id)
		->where('vch_txn_id IS NULL')->where('txn_id IS NULL')->update($txn_data);
    }
   
    function update_cc_op_bal($data)
    {
      $exists = $this->db->table("ccoppybaln")
                          ->where('cc_id', $data['cc_id'])
                          ->where('hobo_id', $this->bo_id)
						  ->where('cmp_id', $data['cmp_id'])
						  ->where('cmpfymastr_id', $this->fy_id)
                          ->get()->getRowArray();
      if($exists){
		$updata  =array('cc_op_bal' => $data['cc_op_bal'],
                        'cc_py_bal' => $data['cc_py_bal']
						);
        $this->db->table("ccoppybaln")
                          ->where('cc_id', $data['cc_id'])
                          ->where('cmp_id', $data['cmp_id'])
						  ->where('hobo_id', $this->bo_id)
						  ->where('cmpfymastr_id', $this->fy_id)
                          ->update($updata);
        }
       else{
        $this->insert_cc_op_bal($data);
       }
    }

    function get_cc_op_bal($cc_id)
    {
      $costctoppy_tbl = $this->company_id.'_costctoppy_'.$this->session->get('ses_comp_fy_id');
      $balance = 0;

      $costctoppy = $this->db->table($costctoppy_tbl)
                          ->where('cc_id', $cc_id)
                          ->where('bo_id', $this->bo_id)
                          ->get()->getRowArray();
      if($costctoppy){
        $balance = floatval($costctoppy['cc_op_bal']);
      }
      return $balance;
    }

    function get_cc_op_bal_info($cc_id)
    {
      $costctoppy_tbl = $this->company_id.'_costctoppy_'.$this->session->get('ses_comp_fy_id');
      $balance = 0;

      $costctoppy = $this->db->table($costctoppy_tbl)
                          ->where('cc_id', $cc_id)
                          ->where('bo_id', $this->bo_id)
                          ->get()->getRowArray();
      
      return $costctoppy;
    }

    function delete_cc_op_bal($cc_id)
    {
      $costctoppy_tbl = $this->company_id.'_costctoppy_'.$this->session->get('ses_comp_fy_id');

      $this->db->table($costctoppy_tbl)
                          ->where('cc_id', $cc_id)
                          ->delete();
    }
 
    public function update_cc($data,$cc_id){
        $table = $this->db->table("ccmasternn")
          ->where('cmp_id',$this->company_id)
          ->where('LOWER(cc_name)', strtolower(trim($data['cc_name'])))
          ->where('cc_id !=',$cc_id)
          ->get()->getRowArray();              
        if($table){
            return ['status' => false, 'message' => 'Name must be unique'];
        }
        $this->db->table("ccmasternn")
                    ->where('cc_id',$cc_id)
                    ->update($data);

        return ['status' => true, 'cc_id' => $cc_id];	
    } 
   
    function get_cc_info($cc_id){	
	    
		$builder = $this->db->table("ccmasternn");	
        $builder->select("ccmasternn.*,ccoppybaln.cc_op_bal,ccoppybaln.cc_py_bal,undercrsmt.under_crs_mst_id");		
		$builder->join("ccoppybaln", "ccoppybaln.cc_id = ccmasternn.cc_id", 'left');
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = ccmasternn.cc_id AND undercrsmt.crs_mst_type =9", 'left');
		$builder->where("ccmasternn.cc_id", $cc_id);
		$row = $builder->get()->getRowArray();
		return $row;        
    }   
    
    public function remove_single_cc($id){
        $this->db->table("ccoppybaln")->where('hobo_id',$this->bo_id)->where('cmpfymastr_id',$this->fy_id)->where('cc_id',$id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("cctxnmstnn")->where('hobo_id',$this->bo_id)->where('cc_id',$id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("undercrsmt")->where('crs_mst_type',9)->where('crs_mst_id',$id)->where('cmp_id',$this->company_id)->delete();
	    $this->db->table("ccmasternn")->where('cc_id',$id)->where('cmp_id',$this->company_id)->delete();
		
		return TRUE;
    }
    
    function check_cc_with_voucher($id)
    {
        $data =  $this->db->table("cctxnmstnn")
                          ->where('cc_id', $id)
                          ->where('cmp_id',$this->company_id)
						  ->where('vch_txn_id IS NOT NULL')
						  ->where('txn_id IS NOT NULL')
                          ->get()->getRowArray();
        if($data){
          return 1;
        }
          return 0;
    }
    
    function check_group_with_group($id)
    {
		$builder =  $this->db->table("ccgrpmstnn");
	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id = ccgrpmstnn.cc_grp_id AND undercrsmt.crs_mst_type =10 AND undercrsmt.cmp_id =$this->company_id", 'left');
     	$builder->where("undercrsmt.under_crs_mst_id",$id);	       
        $data =  $builder->get()->getRowArray();
		if($data){
            return 1;
        }
        return 0;
    }
    
    function check_cc_with_group($id)
    {
		$builder =  $this->db->table("ccmasternn");
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = ccmasternn.cc_id AND undercrsmt.crs_mst_type =9 AND undercrsmt.cmp_id =$this->company_id", 'left');
     	$builder->where('ccmasternn.cmp_id',$this->company_id);
		$builder->where('undercrsmt.under_crs_mst_id',$id);	
		$data =  $builder->get()->getRowArray();
        if($data){
            return 1;
        }
        return 0;
    }
    
    public function remove_single_groups($id){
		$this->db->table("ccgrpmstnn")->where('cc_grp_id',$id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("undercrsmt")->where('crs_mst_type',10)->where('crs_mst_id',$id)->where('cmp_id',$this->company_id)->delete();
	    return TRUE;
    }
 	   
	public function get_cc_group_name($id){
        $data = $this->db->table("ccgrpmstnn")->select('cc_grp_name')->where('cc_grp_id', $id)->get()->getRowArray();
        $cc_group_name = $data['cc_grp_name'];
        return $cc_group_name;
    }   
    
	public function changestatus_single_group($id,$status){
	 $this->db->table("ccgrpmstnn")->where('cmp_id',$this->company_id)->where("cc_grp_id",$id)->update(["cc_grp_is_active"=>$status]);
	 $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",10)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]); 
    }
	
	function add_undercrsmt($txn_data){
	  	$this->db->table("undercrsmt")->insert($txn_data);	
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
    public function add_group($data){
	    $exists   = $this->db->table("ccgrpmstnn")
		             ->where('cmp_id',$data['cmp_id']) ->where('cc_grp_is_active',1)
					 ->where('LOWER(cc_grp_name)', strtolower(trim($data['cc_grp_name'])))->get()->getRowArray(); 
	    if($exists)
		    return 0;
	    else{
		    $this->db->table("ccgrpmstnn")->insert($data);
			$cc_grp_id = $this->db->insertID();
            return $cc_grp_id;
	     }
    }   
     
   public function update_group($update_data,$cc_grp_id){
	   $table = $this->db->table("ccgrpmstnn")
	    		->where('LOWER(cc_grp_name)',strtolower(trim($update_data['cc_grp_name'])))
				 ->where('cmp_id',$this->company_id)
                ->where('cc_grp_id !=',$cc_grp_id)
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	 $table = $this->db->table("ccgrpmstnn")->where('cmp_id',$this->company_id)->where('cc_grp_id',$cc_grp_id)->update($update_data);		
     if($table){	
	   return ['status' => true, 'account_id' => $cc_grp_id];
	 }
	 return ['status' => false,'message'=>'Something wrong'];	
   } 

   public function ajax_group_list(){
	    $builder = $this->db->table("ccgrpmstnn");
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = ccgrpmstnn.cc_grp_id AND undercrsmt.crs_mst_type =10 AND undercrsmt.cmp_id =$this->company_id", 'left');
		$builder->select('ccgrpmstnn.cc_grp_id,ccgrpmstnn.cc_grp_name,ccgrpmstnn.cc_grp_alias,undercrsmt.under_crs_mst_id,undercrsmt.crs_is_active');
		$builder->where('ccgrpmstnn.cmp_id',$this->company_id);
		$result  = $builder->get()->getResultArray(); 
        $records = array();		
        foreach($result as $values){
            $under_cc_grp = '';
            $confirmstatus = ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE';	
		    $acc_status_vl  = ($values['crs_is_active']==1)?0:1;			
            if($values['under_crs_mst_id'] > 0)
                $under_cc_grp = $this->get_cc_group_name($values['under_crs_mst_id']);
                        
            $records[] = array(	
			      'checkbox'          => '<input name="group_ids[]" class="checkbox groups_row" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'"  data-id="'.$values['cc_grp_id'].'"  type="checkbox" value="'.$values['cc_grp_id'].'">',
                  'cc_grp_id'         => $values['cc_grp_id'],
			  	  'cc_grp_name'       => $values['cc_grp_name'],
				  'cc_grp_alias'      => $values['cc_grp_alias'],
				  'under_cc_grp_id'   => $values['under_crs_mst_id'],
				  'under_cc_grp'      => $under_cc_grp,
				  'acc_status_vl'     => ($values['crs_is_active']==1)?0:1,
				  'group_status'      => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
				  'alert_acc_status'  => ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE',
	        ); 
        }	
        return $records;
    }

    public function all_cc_group_export(){
           
        $cc_grp_master_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');        
        $builder  = $this->db->table($cc_grp_master_tbl); 
        $builder->orderBy('cc_grp_name');           
        $result  = $builder->get()->getResultArray(); 
        $records = array();     
        foreach($result as $values){
            $under_cc_grp = '';
            
            if($values['under_cc_grp_id'] > 0)
                $under_cc_grp = $this->get_cc_group_name($values['under_cc_grp_id']);
            
            
            $records[] = array( 
                  'cc_grp_id'       => $values['cc_grp_id'],
                  'cc_grp_name'     => $values['cc_grp_name'],
                  'cc_grp_alias'    => $values['cc_grp_alias'],
                  'under_cc_grp_id' => $values['under_cc_grp_id'],
                  'under_cc_grp'    => $under_cc_grp
            ); 
        }   
        return $records;
    }
    
    function cc_group_info($cc_grp_id){
		$builder = $this->db->table('ccgrpmstnn');
	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id = ccgrpmstnn.cc_grp_id AND undercrsmt.crs_mst_type =10 AND undercrsmt.cmp_id =$this->company_id", 'left');
        $builder->where('ccgrpmstnn.cmp_id',$this->company_id);
	    $builder->where('undercrsmt.crs_mst_id', $cc_grp_id);
	    $row = $builder->get()->getRowArray();
		return $row;
     }   
    public function check_cc_group_exists($id,$name){
     $data = $this->db->table("ccgrpmstnn")->select('cc_grp_name')->where('cc_grp_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(cc_grp_name)', strtolower($name))->get()->getRowArray();
     if($data)
        return true; 
        else
        return false;
     }	
    function cc_group_dropdown($group_id = 0){
		$builder = $this->db->table("ccgrpmstnn");
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = ccgrpmstnn.cc_grp_id AND undercrsmt.crs_mst_type =10 AND undercrsmt.cmp_id =$this->company_id", 'left');
		$builder->select('ccgrpmstnn.cc_grp_id,ccgrpmstnn.cc_grp_name');
		$builder->where('ccgrpmstnn.cc_grp_id !=', $group_id);
		$data = $builder->get()->getResultArray();
		$final_result=[];
		$final_result['']='choose';
		if($data){
			foreach($data as $row){
                $final_result[$row['cc_grp_id']] = $row['cc_grp_name'];			   
            }
		}		
        return $final_result;	
    } 
 
}