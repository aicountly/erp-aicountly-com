<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class BillsundryModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }   
	
	
	 function company_all_bsd(){
      
	  $billsundry_tbl  = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	   $bsd_data  =  $this->db->table($billsundry_tbl)
	                      ->select('bill_sundry_name as label, bill_sundry_name as value, bill_sundry_id, acc_grp_id')
	   					  ->orderBy('bill_sundry_name','ASC')
	   					  ->get()->getResultArray();

	   
	   if($bsd_data){
		  foreach($bsd_data as $key => $row){
               $final_result[]    = array(
               			"label" => ucwords($row['label']),
               			'value' => $row['value'],
               			'id'    => $row['bill_sundry_id'],
               			'account_id'    => $row['bill_sundry_id'],
               			'acc_id'      =>$row['bill_sundry_id'],
						'is_sundry'  => '1',
						'is_acc'  => '0',
						'is_bbb'  => 0,
						'is_cc'	  => 0,
               		);	
		      }
        	}
      return json_encode($final_result);
  }
  
  	function group_primary_dropdown(){		      
             $account_grp_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');
				  
			$data =  $this->db->table($account_grp_tbl)->orderBy('acc_grp_parent')->get()->getResultArray();
			$final_result = array();
			$final_result['']  = 'Choose';
			 if($data){
			   foreach($data as $row){
				  $final_result[$row['acc_grp_parent_id']] = ucwords($row['acc_grp_parent']);			   
					}
				}
		
		
	  return $final_result;	
    }

	public function CreateBillsundryTxnTable($tablename){
	    $table_query ="CREATE TABLE `".$tablename."` (
					  `sundry_txn_id` bigint NOT NULL AUTO_INCREMENT,
					  `comp_id` bigint NOT NULL,
					  `sundry_txn_date` date DEFAULT NULL,
					  `sundry_txn_amount` decimal(18,2) DEFAULT NULL,
					  `sundry_txn_drcr` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
					  `sundry_txn_narr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
					  `comp_vch_name` varchar(50) DEFAULT NULL,
					  `comp_vch_series_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
					  `bill_sundry_id` bigint DEFAULT NULL,
					  `sundry_bal` decimal(18,2) DEFAULT NULL,
					  `voucher_txn_id` bigint DEFAULT NULL,
					  `voucher_type_id` bigint DEFAULT NULL,
					  `txn_id` bigint DEFAULT NULL,
					  `sundry_tag_rate` decimal(18,2) DEFAULT NULL,
					  PRIMARY KEY (`sundry_txn_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;";
        $this->db->query($table_query);
       }
	   
	   
	function group_main_dropdown(){		      
             $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
				  
			$data =  $this->db->table($account_grp_tbl)->orderBy('acc_grp_name')->get()->getResultArray();
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
    
    
    public function ajax_billsundry_list(){
        $billsundry_nature  =  billsundry_nature();
		
		if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
            
	    $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $item_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl); 
        $total_Records = $builder->countAll();
        
        $offset = ($pq_rPP * ($pq_curPage - 1));
      
     if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
        
        
        
         $builder->orderBy('bill_sundry_name');                
	 	
	 	 $builder->limit($pq_rPP,$offset);
		 $result = $builder->get()->getResultArray();
         // echo "<pre>";print_r($result);exit;
         $records=array(); 		
          foreach($result as $values){
              
              if($values['sundry_type']=='A')
                 $sundry_type ='Additive';
              else if($values['sundry_type']=='D')   
                 $sundry_type ='Substractive';
               else
				  $sundry_type =''; 
			  if(isset($billsundry_nature[$values['sundry_nature']]) && $values['sundry_nature']!='')
			   $billsundry_nature_val = $billsundry_nature[$values['sundry_nature']];
			   else
			   $billsundry_nature_val = '';
                
                if($values['acc_grp_id'] != 0) 
					$billsundry_group_name = $this->get_account_group_name($values['acc_grp_id']);
				else
					$billsundry_group_name = $this->get_parent_group_name($values['acc_grp_parent_id']).' (Parent)';
							
				$billsundry_balance = $this->billsundry_balance($values['bill_sundry_id']);

				$bsd_op_bal = '0.00';
				$bsd_op_bal_drcr = 'DR';

				if($billsundry_balance){
				  	if($billsundry_balance['bsd_op_bal'] < 0){
				  		 $bsd_op_bal = abs($billsundry_balance['bsd_op_bal']);
				  		 $bsd_op_bal_drcr = 'CR'; 
				  	}
				  	else{
				  			$bsd_op_bal = $billsundry_balance['bsd_op_bal'];
				  		 	$bsd_op_bal_drcr = 'DR';
				  	}
				  }
				 
				$records[] = array(	
			                      'checkbox'     =>'<input name="item_ids[]" class="checkbox items_row"  data-id="'.$values['bill_sundry_id'].'"  type="checkbox" value="'.$values['bill_sundry_id'].'">',
    							  'bill_sundry_id'     => $values['bill_sundry_id'],
    							  'billsndry_name'   => ucwords($values['bill_sundry_name']),
    							  'billsndry_type'  => $sundry_type,
    							  'billsndry_nature'    => $billsundry_nature_val,
								  'billsundry_group_name' => $billsundry_group_name,
								  'bsd_op_bal' => $bsd_op_bal,
								  'bsd_op_bal_drcr' => $bsd_op_bal_drcr,
				                 );  		           
		   }
       echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
     }
 
   public function add($data){	    
        $billsundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

      $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');    
	  $this->db->table($billsundry_master_tbl)->insert($data);
	  $billsundry_id         = $this->db->insertID();
	  return ['status' => true, 'billsundry_id' => $billsundry_id];
	     
     }

    public function addBalance($data)
    {
    	$bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id']= $bo_id;
    	$this->db->table($bsdoppybal_tbl)->insert($data);
    }

    function get_pnl_groups()
    {
        
		$p_groups = [6,7,8,9,10,11,12,13];
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

    function check_op_change_pnl($acc_grp_id, $acc_grp_parent_id, $balance)
    {
		  if(in_array($acc_grp_parent_id, [6,7,8,9,10,11,12,13])){
		  	if($balance != 0)
		  		return 0;
		  }

		  if(in_array($acc_grp_id, $this->get_pnl_groups())){
		  	if($balance != 0)
		  		return 0;
		  }

		return 1;
    }

    public function updateBalance($billsundry_id,$data)
    {
		$bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($bsdoppybal_tbl)->where('bill_sundry_id', $billsundry_id)->update($data);
    }

    public function billsundry_balance($billsundry_id)
    {
    	$bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
    	return $this->db->table($bsdoppybal_tbl)->where('bill_sundry_id', $billsundry_id)->get()->getRowArray();
    }

   public function remove_billsundry($billsundry_id){
    	$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($billsundry_tbl)->where('bill_sundry_id',$billsundry_id)->delete();
		
		return TRUE;
	 }
	 
   function check_billsundry_with_voucher($id)
     {
		 $sundrytxnn_table = $this->company_id.'_sundrytxnn_'.$id.'_'.$this->session->get('ses_comp_fy_id');
		 $data =  $this->db->table($sundrytxnn_table)->where('bill_sundry_id', $id)->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     } 
	     return 0;
     }

   public function billsundry_info($billsundry_id){
      $billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($billsundry_tbl)->where('bill_sundry_id',$billsundry_id)->get()->getRowArray();
   }   
   
   public function get_name($billsundry_id){
       
      $billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	  $row =  $this->db->table($billsundry_tbl)->select('bill_sundry_name')->where('bill_sundry_id',$billsundry_id)->get()->getRowArray();    
	  
	  return $row['bill_sundry_name'];
   }
   
   public function update_billsundry($update_data,$billsundry_id){
	    $billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	    $this->db->table($billsundry_tbl)->where('bill_sundry_id',$billsundry_id)->update($update_data);
	   
		return true;	
   }       
 
   public function get_account_group_name($id){
         $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($account_grp_tbl)->select('acc_grp_name')->where('acc_grp_id', $id)->get()->getRowArray();
          if($data)      
	     	return $data['acc_grp_name'];
	      else
			return '';   
     }

    public function get_parent_group_name($id){
         $account_grp_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($account_grp_tbl)->select('acc_grp_parent')->where('acc_grp_parent_id', $id)->get()->getRowArray();
          if($data)      
	     	return $data['acc_grp_parent'];
	      else
			return '';   
     }  
    
}