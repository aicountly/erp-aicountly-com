<?php
namespace App\Models\Grpcomp;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Libraries\enc_string;

class CostCentreModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->CommonModel     =  new CommonModel();
	   $this->enc_string    = new enc_string();
    }
    
    public function ajax_cc(){		
		if($this->company_id){
        $comp_id         = $this->session->get('ses_company_id');
        $base_url        = base_url().'/'.getenv('AdminPath');
        
        $tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id'); 
        $builder         = $this->db->table($tbl); 
        $total_Records = $builder->countAll();
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
        {
            $pq_curPage = (int)$_POST["pq_curpage"];
            $pq_rPP     = (int)$_POST["pq_rpp"];
        } 
        
        $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
        $builder->orderBy('cc_name');                
        $builder->where('comp_id', $comp_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));

        $builder->limit($pq_rPP,$offset);
        $result = $builder->get()->getResultArray();
        
        $records=array(); 		
        foreach($result as $values){
            $cc_group_info = $this->cc_group_info($values['cc_grp_id']);
            
            if(isset($cc_group_info['cc_grp_id']))
                $cc_grp = $cc_group_info['cc_grp_name'];
            else
                $cc_grp = ''; 
            
            $records[]  = array(	
                'checkbox'     =>'<input name="cc_ids[]" class="checkbox cc_row"  data-id="'.$values['cc_id'].'"  type="checkbox" value="'.$values['cc_id'].'">',
                'cc_id'     => $values['cc_id'],
                'cc_name'   => ucwords($values['cc_name']),
                'cc_alias'  => $values['cc_alias'],
                'cc_grp'    => $cc_grp,
                'cc_op_bal'  => $values['cc_op_bal'],
                'cc_op_drcr' => $values['cc_op_drcr']
                );  
				}		
		}
		else{
			$total_Records= 0;
			$pq_curPage   = 1;
			$records      = array();
		}
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
    }

	 
    public function get_cc_name($id){
        $cc_master_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')		
		$data = $this->db->table($cc_master_tbl)->select('cc_name')->where('bo_id', $this->session->get('ses_boid'))->where('cc_id', $id)->get()->getRowArray();
	  else 
        $data = $this->db->table($cc_master_tbl)->select('cc_name')->where('cc_id', $id)->get()->getRowArray();
        $cc_name = $data['cc_name'];
        return $cc_name;
    }
   
    
   public function add_cc($data){	    
        $cc_master_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
         if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
        $table = $this->db->table($cc_master_tbl)->where('comp_id',$data['comp_id'])
                                                 ->where('LOWER(cc_name)', strtolower(trim($data['cc_name'])))
                                                 ->orWhere('comp_id',$data['comp_id'])
                                                 ->where('LOWER(cc_alias)', strtolower(trim($data['cc_name'])))
                                                 ->get()->getRowArray();
                                               
        if($table){
            return ['status' => false, 'message' => 'Name must be unique'];
        }
        
        $table = $this->db->table($cc_master_tbl)->where('comp_id',$data['comp_id'])
                                                 ->where('LOWER(cc_name)', strtolower(trim($data['cc_alias'])))
                                                 ->orWhere('comp_id',$data['comp_id'])
                                                 ->where('LOWER(cc_alias)', strtolower(trim($data['cc_alias'])))
                                                 ->get()->getRowArray();
                                               
        if($table){
            return ['status' => false, 'message' => 'Alias must be unique'];
        }
        
		$data['bo_id'] = $bo_id;
        $this->db->table($cc_master_tbl)->insert($data);
        $cc_id = $this->db->insertID();
        return ['status' => true, 'cc_id' => $cc_id];
	     
    }
 
    public function update_cc($data,$cc_id){
        $cc_master_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        if($this->session->get('ses_boid')!=''){
		$bo_id =  $this->session->get('ses_boid');
        $table = $this->db->table($cc_master_tbl)->where('comp_id',$this->company_id)
                                                ->where('LOWER(cc_name)', strtolower(trim($data['cc_name'])))
                                                ->where('cc_id !=',$cc_id)
                                                ->orWhere('comp_id',$this->company_id)
                                                ->where('LOWER(cc_alias)', strtolower(trim($data['cc_name'])))
                                                ->where('cc_id !=',$cc_id)
												->where('bo_id', $this->session->get('ses_boid'))
                                                ->get()->getRowArray();
		}
		else{
		$table = $this->db->table($cc_master_tbl)->where('comp_id',$this->company_id)
                                                ->where('LOWER(cc_name)', strtolower(trim($data['cc_name'])))
                                                ->where('cc_id !=',$cc_id)
                                                ->orWhere('comp_id',$this->company_id)
                                                ->where('LOWER(cc_alias)', strtolower(trim($data['cc_name'])))
                                                ->where('cc_id !=',$cc_id)
                                                ->get()->getRowArray();	
		}
                                               
        if($table){
            return ['status' => false, 'message' => 'Name must be unique'];
        }
		if($this->session->get('ses_boid')!=''){
		$bo_id =  $this->session->get('ses_boid');
        $table = $this->db->table($cc_master_tbl)->where('comp_id',$this->company_id)
                                                ->where('LOWER(cc_name)', strtolower(trim($data['cc_alias'])))
                                                ->where('cc_id !=',$cc_id)
                                                ->orWhere('comp_id',$this->company_id)
                                                ->where('LOWER(cc_alias)', strtolower(trim($data['cc_alias'])))
                                                ->where('cc_id !=',$cc_id)
												->where('bo_id', $this->session->get('ses_boid'))
                                                ->get()->getRowArray();
		}
		else{
		$table = $this->db->table($cc_master_tbl)->where('comp_id',$this->company_id)
                                                ->where('LOWER(cc_name)', strtolower(trim($data['cc_alias'])))
                                                ->where('cc_id !=',$cc_id)
                                                ->orWhere('comp_id',$this->company_id)
                                                ->where('LOWER(cc_alias)', strtolower(trim($data['cc_alias'])))
                                                ->where('cc_id !=',$cc_id)
                                                ->get()->getRowArray();	
			
		}
                                               
        if($table){
            return ['status' => false, 'message' => 'Alias must be unique'];
        }
        
        $this->db->table($cc_master_tbl)->where('cc_id',$cc_id)->update($data);		
        return ['status' => true, 'cc_id' => $cc_id];	
    } 
   
    function get_cc_info($cc_id){	 
        $cc_master_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')		
	    return $this->db->table($cc_master_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('cc_id', $cc_id)->where('comp_id', $this->company_id)->get()->getRowArray();   	   
        else 
        return $this->db->table($cc_master_tbl)->where('cc_id', $cc_id)->where('comp_id', $this->company_id)->get()->getRowArray();   	   
    }   
    
    public function remove_single_cc($id){
        $cc_master_tbl  = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')	
        $this->db->table($cc_master_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('cc_id',$id)->delete();
	    else 
	 	$this->db->table($cc_master_tbl)->where('cc_id',$id)->delete();
        return TRUE;
    }
    
    function check_cc_with_voucher($id)
    {
        $voucher_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
        $data =  $this->db->table($voucher_tbl)->where('master_id', $id)->where('master_id_type','itm')->get()->getRowArray();
        if(!empty($data)){
        return 1;
        }
        return 0;
    }
    
    function check_group_with_group($id)
    {
        $cc_grp_master_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
        $data =  $this->db->table($cc_grp_master_tbl)->where('under_cc_grp_id', $id)->get()->getRowArray();
        if(!empty($data)){
            return 1;
        }
        return 0;
    }
    
    function check_cc_with_group($id)
    {
        $cc_master_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $data =  $this->db->table($cc_master_tbl)->where('cc_grp_id', $id)->get()->getRowArray();
        if(!empty($data)){
            return 1;
        }
        return 0;
    }
    
    public function remove_single_groups($id){
        $ses_comp_fy_id   =  $this->session->get('ses_comp_fy_id');
        $cc_grp_master_tbl  =  $this->company_id.'_costctgrup_'.$ses_comp_fy_id;
        
        $this->db->table($cc_grp_master_tbl)->where('cc_grp_id',$id)->delete();			
        return TRUE;
    }

   
    public function CreateItemTxnTable($tablename){	          
	    $table_query ='CREATE TABLE IF NOT EXISTS `'.$tablename.'` (`item_txn_id` BIGINT NOT NULL , `comp_id` BIGINT NOT NULL ,`item_txn_date` DATE NOT NULL,`item_txn_amount` DECIMAL(18,2) NOT NULL ,`item_txn_drcr` VARCHAR(100) NOT NULL, `item_txn_qty` VARCHAR(20) NOT NULL ,`item_txn_narr` VARCHAR(100) NOT NULL, `comp_vch_series_no` VARCHAR(100) NOT NULL ,`voucher_type_id`  BIGINT NOT NULL, `item_id` BIGINT NOT NULL,`item_qty_bal` DECIMAL(18,2) NOT NULL,`item_value_bal` DECIMAL(18,2) NOT NULL,`voucher_txn_id` BIGINT NOT NULL ,`item_py_bal` DECIMAL(18,2) NOT NULL, `bo_id` BIGINT DEFAULT 1,`mat_cent_id` BIGINT NOT NULL,`txn_id` BIGINT NOT NULL,`item_remarks` TEXT NULL ,`item_remarks_id` TEXT NULL) ENGINE = InnoDB;';
	    $this->db->query($table_query);		
	}
	   
	public function get_cc_group_name($id){
        $cc_grp_master_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($cc_grp_master_tbl)->select('cc_grp_name')->where('cc_grp_id', $id)->get()->getRowArray();
        $cc_group_name = $data['cc_grp_name'];
        return $cc_group_name;
    }   
	   
    public function add_group($data){
	    $cc_grp_master_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
	    $cc_grp = $data['cc_grp_name'];
	    $exists   = $this->db->table($cc_grp_master_tbl)->where('comp_id',$data['comp_id'])->where('LOWER(cc_grp_name)', strtolower(trim($cc_grp)))->get()->getRowArray(); 
	    if($exists)
		    return "0";
	    else{
		    $this->db->table($cc_grp_master_tbl)->insert($data);
		    return $this->db->insertID();
	     }
    }   
     
   public function update_group($update_data,$group_id){
	    $cc_grp_master_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
	    $this->db->table($cc_grp_master_tbl)->where('cc_grp_id',$group_id)->update($update_data);		
		return true;	
   } 

   public function ajax_group_list(){
	   $records = array();		
	   if($this->company_id){
	    $comp_id  = $this->session->get('ses_company_id');
	    $base_url = base_url().'/'.getenv('AdminPath');  		
        $cc_grp_master_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');		
        $builder  = $this->db->table($cc_grp_master_tbl); 
        $builder->orderBy('cc_grp_name');
		$builder->where('comp_id', $comp_id);		 	
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
	   }
	   
	   	return [
			'total_records'	=> count($records),
			'data'	=> $records
		];
        
    }
     
    
	 
	
    function cc_group_info($cc_grp_id){
        $cc_grp_master_tbl =  $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id'); 	   
        return $this->db->table($cc_grp_master_tbl)->where('cc_grp_id', $cc_grp_id)->where('comp_id', $this->company_id)->get()->getRowArray();   	   
    }   
    
    function cc_group_dropdown($group_id = 0){
        $cc_grp_master_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
        
        $data =  $this->db->table($cc_grp_master_tbl)->where('cc_grp_id !=', $group_id)->orderBy('cc_grp_name','ASC')->get()->getResultArray();
        $final_result      = array();
        $final_result['']  = '';
        if($data){
            foreach($data as $row){
                $final_result[$row['cc_grp_id']] = $row['cc_grp_name'];			   
            }
        }
        return $final_result;	
    } 
 
}