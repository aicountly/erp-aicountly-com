<?php
namespace App\Models\Admin;
use CodeIgniter\Model;

class ProjectModel extends Model	{

    public function __construct() {
       parent::__construct();        
	   $this->session       = \Config\Services::session();
	   $this->ses_comp_fy_id = $this->session->get('ses_comp_fy_id');
       $this->company_id    =  $this->session->get('ses_company_id');
	   $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	   $this->bo_id         =  $this->session->get('ses_boid');
    }

	function add_project_data($data)
	{  
	    $table = $this->db->table("projectmst")
	    		 ->where('LOWER(project_name)',strtolower(trim($data['project_name'])))
				 ->where('project_is_active',1)
				 ->where('cmp_id',$this->company_id)                
        	    ->get()->getRowArray(); 
	    if($table){
	        return "-1";
	    }		
		$this->db->table("projectmst")->insert($data);
		$project_id = $this->db->insertID();
        return $project_id;
	}

	function add_project_op_data($data)
	{
		$this->db->table("prjoppybal")->insert($data);
	}
	
   function insert_project_txn_entry($data)
	{
		$this->db->table("prjtxnmstn")->insert($data);
	}

	function update_project_data($data)
	{		
		$this->db->table("projectmst")
					->where('project_id',$data['project_id'])
					->update($data);
	}	
	
	public function update_project_txn_entry($txn_data,$account_id,$acc_txn_type){		
	    $accttxnmst_tbl = "prjtxnmstn";
	    $this->db->table($accttxnmst_tbl)->where('cmp_id',$this->company_id)
		->where('project_id',$account_id)->where('project_txn_type',$acc_txn_type)
		->where('vch_txn_id IS NULL')->update($txn_data);
		
   }
   
	function update_project_op_data($data)
	{
	
		$prjoppybal = $this->db->table("prjoppybal")
									->where('project_id',$data['project_id'])
									->where('project_txn_type',$data['project_txn_type'])
									->where('hobo_id',$this->bo_id)
									->get()->getRowArray();

		if($prjoppybal){
			$this->db->table("prjoppybal")
						->where('project_id',$data['project_id'])
						->where('project_txn_type',$data['project_txn_type'])
						->where('hobo_id',$this->bo_id)
						->update($data);
		}
		else{
			$this->add_project_op_data($data);
		}
	}

	function get_project_op_bal($project_id,$project_bal_type)
	{
		$bal = 0;
		$prjoppybal_tbl = $this->company_id.'_prjoppybal_'.$this->session->get('ses_comp_fy_id');

		$data = $this->db->table($prjoppybal_tbl)
					->where('project_id', $project_id)
					->where('project_bal_type', $project_bal_type)
					->where('bo_id', $this->bo_id)
					->get()->getRowArray();
		if($data){
			$bal = floatval($data['project_op_bal']);
		}
		else{
			$op_data = [
				'project_id' 			=> $project_id,
				'bo_id' 					=> $this->bo_id,
				'project_op_bal' 		=> 0,
				'project_py_bal' 		=> 0,
				'project_bal_type' 	=> $project_bal_type
			];
			$this->db->table($prjoppybal_tbl)->insert($op_data);
		}
		return $bal;
	}
	
	public function get_project_list()
{
    // Pagination
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;

    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $offset = ($pq_curPage - 1) * $pq_rPP;

    $builder = $this->db->table("projectmst p");

    // SELECT — COALESCE used for PostgreSQL
    $builder->select("
        p.project_id,
        p.project_name,
        p.project_alias,
        pg.project_grp_name,
        u.crs_is_active,

        COALESCE(op.op_liab_amt, 0) AS op_liab_amt,
        COALESCE(op.op_asset_amt, 0) AS op_asset_amt,
        COALESCE(tx.liability_amt, 0) AS liab_amt,
        COALESCE(tx.asset_amt, 0) AS asset_amt,

        CASE 
            WHEN COALESCE(op.op_liab_amt, 0) < 0 THEN 'Cr'
            WHEN COALESCE(op.op_liab_amt, 0) > 0 THEN 'Dr'
            ELSE ''
        END AS op_liab_type,

        CASE 
            WHEN COALESCE(op.op_asset_amt, 0) < 0 THEN 'Cr'
            WHEN COALESCE(op.op_asset_amt, 0) > 0 THEN 'Dr'
            ELSE ''
        END AS op_asset_type
    ");

    // OPENING BALANCE SUBQUERY
    $builder->join("
        (
            SELECT 
                project_id,
                SUM(CASE WHEN project_txn_type = 1 THEN project_op_bal ELSE 0 END) AS op_liab_amt,
                SUM(CASE WHEN project_txn_type = 2 THEN project_op_bal ELSE 0 END) AS op_asset_amt
            FROM prjoppybal
            WHERE cmp_id = {$this->company_id}
              AND cmpfymastr_id = {$this->fy_id}
            GROUP BY project_id
        ) AS op",
        "op.project_id = p.project_id",
        "left"
    );

    // TRANSACTION TOTAL SUBQUERY
    $builder->join("
        (
            SELECT 
                project_id,
                SUM(CASE WHEN project_txn_type = 1 THEN project_txn_amt ELSE 0 END) AS liability_amt,
                SUM(CASE WHEN project_txn_type = 2 THEN project_txn_amt ELSE 0 END) AS asset_amt
            FROM prjtxnmstn
            WHERE txn_id IS NULL
              AND cmp_id = {$this->company_id}
            GROUP BY project_id
        ) AS tx",
        "tx.project_id = p.project_id",
        "left"
    );

    // UNDER CRS
    $builder->join(
        "undercrsmt u",
        "u.crs_mst_id = p.project_id 
         AND u.crs_mst_type = 11 
         AND u.cmpfymastr_id = {$this->fy_id}",
        "left"
    );

    // PROJECT GROUP MASTER
    $builder->join(
        "prjgrpmstn pg",
        "pg.project_grp_id = u.crs_mst_id",
        "left"
    );

    // FILTER
    $builder->where("p.cmp_id", $this->company_id);

    // PostgreSQL requires full GROUP BY
    $builder->groupBy("
        p.project_id, 
        p.project_name, 
        p.project_alias,
        pg.project_grp_name,
        u.crs_is_active,
        op.op_liab_amt,
        op.op_asset_amt,
        tx.liability_amt,
        tx.asset_amt
    ");

    // COUNT (clone query)
    $countBuilder = clone $builder;
    $total_Records = $countBuilder->countAllResults(false);

    // Adjust offset if beyond total
    if ($offset > $total_Records) {
        $pq_curPage = max(1, ceil($total_Records / $pq_rPP));
        $offset = ($pq_curPage - 1) * $pq_rPP;
    }

    $builder->orderBy("pg.project_grp_name, p.project_name");
    $builder->limit($pq_rPP, $offset);

    $result = $builder->get()->getResultArray();

    // FINAL RESULT FORMAT
    $records = [];
    foreach ($result as $row) {

        $confirmstatus = ($row['crs_is_active'] == 1) ? 'INACTIVE' : 'ACTIVE';
        $acc_status_vl = ($row['crs_is_active'] == 1) ? 0 : 1;

        $records[] = [
            'chkbx' => '<input name="project_ids[]" 
                            class="checkbox project_row" 
                            data-acc_status_vl="'.$acc_status_vl.'"  
                            data-confirmstatus="'.$confirmstatus.'" 
                            data-id="'.$row['project_id'].'" 
                            type="checkbox" value="'.$row['project_id'].'">',

            'project_id'          => $row['project_id'],
            'project_name'        => ucwords($row['project_name']),
            'project_alias'       => $row['project_alias'],
            'project_grp_name'    => $row['project_grp_name'],

            'lia_project_op_bal'  => $row['liab_amt'],
            'lia_project_op_drcr' => $row['op_liab_type'],

            'ast_project_op_bal'  => $row['asset_amt'],
            'ast_project_op_drcr' => $row['op_asset_type'],

            'acc_status_vl'       => $acc_status_vl,
            'project_status'      => ($row['crs_is_active'] == 1 ? 'ACTIVE' : 'INACTIVE'),
            'alert_acc_status'    => ($row['crs_is_active'] == 1 ? 'INACTIVE' : 'ACTIVE')
        ];
    }

    return json_encode([
        "totalRecords" => $total_Records,
        "curPage"      => $pq_curPage,
        "data"         => $records
    ]);
}

	public function get_project_data($project_id)
{
    $builder = $this->db->table("projectmst p");

    $builder->select("
        p.project_id,
        p.project_name,
        p.project_alias,
        p.project_print_name,

        pg.project_grp_name,
        pg.project_grp_id,

        COALESCE(op.op_liab_amt, 0) AS op_liab_amt,
        COALESCE(op.op_asset_amt, 0) AS op_asset_amt,

        COALESCE(op.py_liab_amt, 0) AS py_liab_amt,
        COALESCE(op.py_asset_amt, 0) AS py_asset_amt,

        COALESCE(tx.liability_amt, 0) AS liab_amt,
        COALESCE(tx.asset_amt, 0) AS asset_amt,

        CASE 
            WHEN COALESCE(op.py_liab_amt, 0) < 0 THEN 'Cr'
            WHEN COALESCE(op.py_liab_amt, 0) > 0 THEN 'Dr'
            ELSE ''
        END AS py_liab_type,

        CASE 
            WHEN COALESCE(op.py_asset_amt, 0) < 0 THEN 'Cr'
            WHEN COALESCE(op.py_asset_amt, 0) > 0 THEN 'Dr'
            ELSE ''
        END AS py_asset_type,

        CASE 
            WHEN COALESCE(op.op_liab_amt, 0) < 0 THEN 'Cr'
            WHEN COALESCE(op.op_liab_amt, 0) > 0 THEN 'Dr'
            ELSE ''
        END AS op_liab_type,

        CASE 
            WHEN COALESCE(op.op_asset_amt, 0) < 0 THEN 'Cr'
            WHEN COALESCE(op.op_asset_amt, 0) > 0 THEN 'Dr'
            ELSE ''
        END AS op_asset_type
    ");

    // Opening + Previous Year Balances
    $builder->join("
        (
            SELECT 
                project_id,
                SUM(CASE WHEN project_txn_type = 1 THEN project_op_bal ELSE 0 END) AS op_liab_amt,
                SUM(CASE WHEN project_txn_type = 2 THEN project_op_bal ELSE 0 END) AS op_asset_amt,

                SUM(CASE WHEN project_txn_type = 1 THEN project_py_bal ELSE 0 END) AS py_liab_amt,
                SUM(CASE WHEN project_txn_type = 2 THEN project_py_bal ELSE 0 END) AS py_asset_amt
            FROM prjoppybal
            WHERE cmp_id = {$this->company_id}
              AND cmpfymastr_id = {$this->fy_id}
            GROUP BY project_id
        ) AS op",
        "op.project_id = p.project_id",
        "left"
    );

    // Transaction Balances
    $builder->join("
        (
            SELECT 
                project_id,
                SUM(CASE WHEN project_txn_type = 1 THEN project_txn_amt ELSE 0 END) AS liability_amt,
                SUM(CASE WHEN project_txn_type = 2 THEN project_txn_amt ELSE 0 END) AS asset_amt
            FROM prjtxnmstn
            WHERE txn_id IS NULL
              AND cmp_id = {$this->company_id}
            GROUP BY project_id
        ) AS tx",
        "tx.project_id = p.project_id",
        "left"
    );

    // Ledger Assignment
    $builder->join(
        "undercrsmt u",
        "u.crs_mst_id = p.project_id
         AND u.crs_mst_type = 12
         AND u.cmpfymastr_id = {$this->fy_id}",
        "left"
    );

    // Project Group
    $builder->join(
        "prjgrpmstn pg",
        "pg.project_grp_id = u.crs_mst_id",
        "left"
    );

    // Filters
    $builder->where("p.cmp_id", $this->company_id);
    $builder->where("p.project_id", $project_id);

    // PostgreSQL requires ALL non-aggregated columns in GROUP BY
    $builder->groupBy("
        p.project_id,
        p.project_name,
        p.project_alias,
        p.project_print_name,
        pg.project_grp_name,
        pg.project_grp_id,
        op.op_liab_amt,
        op.op_asset_amt,
        op.py_liab_amt,
        op.py_asset_amt,
        tx.liability_amt,
        tx.asset_amt
    ");

    $result = $builder->get()->getRowArray();

    if ($result) {
        // Liability Opening
        $result['lia_project_op_bal']  = abs($result['op_liab_amt']);
        $result['lia_project_op_drcr'] = $result['op_liab_type'];

        // Liability Previous Year
        $result['lia_project_py_bal']  = abs($result['py_liab_amt']);
        $result['lia_project_py_drcr'] = $result['py_liab_type'];

        // Asset Opening
        $result['ast_project_op_bal']  = abs($result['op_asset_amt']);
        $result['ast_project_op_drcr'] = $result['op_asset_type'];

        // Asset Previous Year
        $result['ast_project_py_bal']  = abs($result['py_asset_amt']);
        $result['ast_project_py_drcr'] = $result['py_asset_type'];
    }

    return $result;
}


	function get_project_group_list()
	{
		 if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	    
		$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page		
		if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;		
		$builder = $this->db->table("prjgrpmstn");
		$builder->select("			
			prjgrpmstn.project_grp_name,prjgrpmstn.project_grp_alias,
            prjgrpmstn.project_grp_id,			
			undercrsmt.under_crs_mst_id,
			undercrsmt.crs_is_active
		");
      $builder->join(
			"undercrsmt",
			"undercrsmt.crs_mst_id = prjgrpmstn.project_grp_id 
			AND 
			undercrsmt.crs_mst_type = 12
			AND 
			undercrsmt.cmpfymastr_id = $this->fy_id
			",
			'left'
		); 	
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
		
		$builder->limit($pq_rPP, $offset);
        $result  = $builder->get()->getResultArray();
		$records = array();
		foreach($result as $values){
			$confirmstatus = ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE';	
		    $acc_status_vl  = ($values['crs_is_active']==1)?0:1;
		   
			   if ($values['under_crs_mst_id'] == 0) {
				$show_group = "";  
			} else {
				$show_group = $this->get_project_group_name($values['under_crs_mst_id']);
			}
			$records[] = array(	
			          'checkbox'=>'<input name="group_ids[]" class="checkbox groups_row" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'"  data-id="'.$values['project_grp_id'].'"  type="checkbox" value="'.$values['project_grp_id'].'">',
                      'project_grp_id'         => $values['project_grp_id'],
                      'project_grp_name'       => ucwords($values['project_grp_name']),					  
					  'under_project_grp_name' => $show_group,
                      'project_grp_alias'      => $values['project_grp_alias'],	
					  'acc_status_vl'          => ($values['crs_is_active']==1)?0:1,
					  'grp_status'             => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
					  'alert_acc_status'       => ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE',	
 					  );
		    }	 
	
	    echo  "{\"totalRecords\":" .$total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}";        
    
	}

  public function check_project_group_exists($id,$name){
     $data = $this->db->table("prjgrpmstn")->select('project_grp_name')->where('project_grp_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(project_grp_name)', strtolower($name))->get()->getRowArray();
     if($data)
        return true; 
        else
        return false;
     }	
	 
  public function changestatus_single_group($id,$status){
   $this->db->table("prjgrpmstn")->where('cmp_id',$this->company_id)->where("project_grp_id",$id)->update(["project_grp_is_active"=>$status]);
   $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",12)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]); 
   } 	 
  
  public function get_project_name($id){
         $data = $this->db->table("projectmst")->select('project_name')->where('project_id', $id)->get()->getRowArray();
         $item_name = $data['project_name'];
         return $item_name;
     }
	 
  public function check_project_exists($id,$name){
        $data = $this->db->table("projectmst")->select('project_name')->where('project_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(project_name)', strtolower($name))->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     } 
	 
  public function check_project_txn_exists($id){
        $data = $this->db->table("prjtxnmstn")->select('project_id')->where('vch_txn_id IS NOT NULL')->where('txn_id IS NOT NULL')->where('cmp_id',$this->company_id)->where('project_id',$id)->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
	 
	 public function check_project_opn_exists($id){
       $data = $this->db->table("prjoppybal")->select('project_id')->where('project_op_bal !=',0)->where('cmpfymastr_id',$this->fy_id)->where('cmp_id',$this->company_id)->where('project_id',$id)->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
         return true; 
        else
         return false;
     }
	 
	 public function changestatus_single_accounts($id,$status){
	  $this->db->table("projectmst")->where('cmp_id',$this->company_id)->where("project_id",$id)->update(["project_is_active"=>$status]);
	  $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",11)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]);
	 } 	
	 
	 public function get_project_group_name($id){
       $data = $this->db->table("prjgrpmstn")->select('project_grp_name')->where('cmp_id', $this->company_id)->where('project_grp_id', $id)->get()->getRowArray();
       return $data['project_grp_name'];
     }
	 
	function check_project_vouchers($project_id)
	{
		$status = false;
		$data = $this->db->table("prjtxnmstn")
				->where('project_id',$project_id)
				->whereIn('project_txn_type',[1,2]) //2 for Asset 1 for Liability 
				->where('vch_txn_id IS NOT NULL')
		        ->where('txn_id IS NOT NULL')
				->limit(1)
				->get()->getRowArray();
		if($data)
			$status = true;

		return $status;
	}

	function delete_project($project_id)
	{
	    $this->db->table("prjoppybal")->where('hobo_id',$this->bo_id)->where('cmpfymastr_id',$this->fy_id)->where('project_id',$project_id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("projectmst")->where('project_id',$project_id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("prjtxnmstn")->where('project_id',$project_id)->where('cmp_id',$this->company_id)->delete();
		$this->db->table("undercrsmt")->where('crs_mst_type',11)->where('crs_mst_id',$project_id)->where('cmp_id',$this->company_id)->delete();
		return TRUE;
	}

	function check_project_under_group($project_grp_id){
		$builder =  $this->db->table("prjgrpmstn");
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = prjgrpmstn.project_grp_id AND undercrsmt.crs_mst_type =12 AND undercrsmt.cmp_id =$this->company_id", 'left');
		$builder->select("prjgrpmstn.project_grp_id,prjgrpmstn.project_grp_name");
		$builder->where('prjgrpmstn.cmp_id',$this->company_id);
		$builder->where('undercrsmt.under_crs_mst_id', $project_grp_id);
		$builder->orderBy('prjgrpmstn.project_grp_name');
		$data = $builder->get()->getResultArray();				
	    if($data)
			return true;

		return false;
	}

	function get_project_groups($project_grp_id = 0)
	{
		$builder =  $this->db->table("prjgrpmstn");
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = prjgrpmstn.project_grp_id AND undercrsmt.crs_mst_type =12 AND undercrsmt.cmp_id =$this->company_id", 'left');
		$builder->select("prjgrpmstn.project_grp_id,prjgrpmstn.project_grp_name");
		$builder->where('prjgrpmstn.cmp_id',$this->company_id);
		if($project_grp_id != 0)
			$builder->where('undercrsmt.under_crs_mst_id !=', $project_grp_id);
		$builder->orderBy('prjgrpmstn.project_grp_name');
		$data = $builder->get()->getResultArray();				
	    return $data;		  
	}

	function get_project_group_data($project_grp_id)
	{
	  $builder =  $this->db->table("prjgrpmstn");	
	  $builder->join("undercrsmt", "undercrsmt.crs_mst_id = prjgrpmstn.project_grp_id AND undercrsmt.crs_mst_type =12 AND undercrsmt.cmp_id =$this->company_id", 'left');
	  $builder->where('project_grp_id',$project_grp_id);
	  $row = $builder->get()->getRowArray();
	  return $row;
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
	function add_project_group($data)
	{
		$this->db->table("prjgrpmstn")->insert($data);
		return  $this->db->insertID();
	}

	function update_project_group($data)
	{  
	    $table = $this->db->table("prjgrpmstn")
	    		->where('LOWER(project_grp_name)',strtolower(trim($data['project_grp_name'])))
				 ->where('cmp_id',$this->company_id)
                ->where('project_grp_id !=',$data['project_grp_id'])
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
		
		$this->db->table("prjgrpmstn")->where('project_grp_id', $data['project_grp_id'])->update($data);
		return ['status' => true, 'message' => ''];
	}

	function update_project_grp_main_ids($under_main_grp_id_old,$under_main_grp_id)
	{
		$projectgrp_tbl = $this->company_id.'_projectgrp_'.$this->ses_comp_fy_id;
		$this->db->table($projectgrp_tbl)
					->where('under_main_grp_id', $under_main_grp_id_old)
					->update(['under_main_grp_id' => $under_main_grp_id]);
	}

	function delete_project_group($project_grp_id)
	{
		$projectgrp_tbl = $this->company_id.'_projectgrp_'.$this->ses_comp_fy_id;
		$this->db->table($projectgrp_tbl)
					->where('project_grp_id', $project_grp_id)
					->delete();
	}

	function check_sub_groups($project_grp_id)
	{
		$projectgrp_tbl = $this->company_id.'_projectgrp_'.$this->ses_comp_fy_id;
		$builder = $this->db->table($projectgrp_tbl);
		$builder->where('under_project_grp_id',$project_grp_id);
		$builder->limit(1);
		$data = $builder->get()->getRowArray();

		if($data)
			return true;

		return false;
	}

	function check_project_name_exists($project_name,$project_id)
	{
		$builder = $this->db->table("projectmst");
		$builder->where('LOWER(project_name)',strtolower($project_name));
		$builder->where('project_is_active',1);
		if($project_id>0)
		$builder->where('project_id !=',$project_id);
		$data = $builder->get()->getRowArray();
		if($data)
			return true;
		return false;
	}

	function check_project_grp_name_exists($project_grp_name,$project_grp_id = 0)
	{   $builder = $this->db->table("prjgrpmstn");
		$builder->where('project_grp_name',$project_grp_name);
		if($project_grp_id != 0){
			$builder->where('project_grp_id !=',$project_grp_id);
		}
		$data = $builder->get()->getRowArray();
		if($data)
			return true;

		return false;
	}

	function check_series_vouchers($comp_vch_series_id)
	{
		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
    	$no_of_vouchers = $this->db->table($voucher_tbl)->where('comp_vch_series_id', $comp_vch_series_id)->countAllResults();

    	return $no_of_vouchers > 0 ? true : false;
	}

	function check_series_count($voucher_type_id)
	{
		$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->ses_comp_fy_id;
    	$count = $this->db->table($cmpvchseri_tbl)->where('voucher_type_id', $voucher_type_id)->countAllResults();

    	return $count == 1 ? true : false;
	}

}