<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;

class SubledgerModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->session       = \Config\Services::session();
	   $this->CommonModel     =  new CommonModel();
	    $this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
    }
    
    public function ajax_subledger(){
		if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	    	    
		// Get pagination params safely
		$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
		
      	
		$builder = $this->db->table("subacctmst");
    	// Select fields
		$builder->select("
			subacctmst.*, 
			acctmaster.acc_name
		");
		// Joins
		$builder->join(
			"acctmaster",
			"acctmaster.acc_id = subacctmst.acc_id",
			'left'
		);
		$builder->where("acctmaster.cmp_id", $this->company_id);
		$builder->where("subacctmst.cmp_id", $this->company_id);
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
		
		$builder->orderBy("subacctmst.sub_acc_name");
		$builder->limit($pq_rPP, $offset);
        $result  = $builder->get()->getResultArray();
		//echo $this->db->getlastquery();
		//die();
		$records=array(); 		
        foreach($result as $values){
            
			$confirmstatus  = ($values['sub_acc_is_active']==1)?'INACTIVE':'ACTIVE';	
		    $acc_status_vl  = ($values['sub_acc_is_active']==1)?0:1;
            $records[]      = array(	
                'checkbox'  =>'<input name="ledger_ids[]" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'" class="checkbox ledger_row"  data-id="'.$values['sub_acc_id'].'"  type="checkbox" value="'.$values['sub_acc_id'].'">',
                'ledger_id'     => $values['sub_acc_id'],
                'ledger_name'   => ucwords($values['sub_acc_name']),
                'ledger_acc_name'       =>ucwords($values['acc_name']),
				'acc_status_vl'    => ($values['sub_acc_is_active']==1)?0:1,
				'ledger_status'        => ($values['sub_acc_is_active']==1)?'ACTIVE':'INACTIVE',
				'alert_acc_status' => ($values['sub_acc_is_active']==1)?'INACTIVE':'ACTIVE',	
                );  
           }
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
    }
	
	public function check_subledger_txn_exists($id){
        $data = $this->db->table("subacctxnm")->select('sub_acc_id')->where('vch_txn_id IS NOT NULL')->where('txn_id IS NOT NULL')->where('cmp_id',$this->company_id)->where('sub_acc_id', $id)->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
	 
    public function all_bb_export(){
        
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

	 
    public function get_subledger_name($id){
        $data = $this->db->table("subacctmst")->select('sub_acc_name')->where('cmp_id', $this->company_id)->where('sub_acc_id', $id)->get()->getRowArray();
        $cc_name = $data['sub_acc_name'];
        return $cc_name;
    }
  public function check_bbb_exists($id,$name){
        $data = $this->db->table("subacctmst")->select('sub_acc_name')->where('sub_acc_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(sub_acc_name)', strtolower($name))->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
 	
	public function changestatus_single_accounts($id,$status){
	  $this->db->table("subacctmst")->where('cmp_id',$this->company_id)->where("sub_acc_id",$id)->update(["sub_acc_is_active"=>$status]);
	 } 	 
	     
    function get_bbb_info($cc_id){	
	    
		$builder = $this->db->table("ccmasternn");	
        $builder->select("ccmasternn.*,ccoppybaln.cc_op_bal,ccoppybaln.cc_py_bal,undercrsmt.under_crs_mst_id");		
		$builder->join("ccoppybaln", "ccoppybaln.cc_id = ccmasternn.cc_id", 'left');
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = ccmasternn.cc_id AND undercrsmt.crs_mst_type =9", 'left');
		$builder->where("ccmasternn.cc_id", $cc_id);
		$row = $builder->get()->getRowArray();
		return $row;        
    }   
    
    
    function check_bbb_with_voucher($id)
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
    
    function check_bbb_with_group($id)
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
    
}