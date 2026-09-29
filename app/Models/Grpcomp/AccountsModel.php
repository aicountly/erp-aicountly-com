<?php
namespace App\Models\Grpcomp;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Libraries\enc_string;
class AccountsModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->CommonModel   =  new CommonModel();	
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    } 
	  function currency_dropdown(){		      
	        $comp_currency_tbl = $this->company_id.'_compcurrcy_'.$this->session->get('ses_comp_fy_id');
			$data =  $this->db->table($comp_currency_tbl)->orderBy('comp_currency_id')->get()->getResultArray();
			$final_result = array();
			$final_result['']  = 'Choose';
			 if($data){
			   foreach($data as $row){
				  $final_result[$row['curr_symbol']] = $row['curr_symbol'];			   
					}
				}
		
		
	  return $final_result;	
     }
     
     
function get_bbb_groups_list()
    {
        
		$groups = [16,22];
		$array = $this->get_sub_group_ids_list($groups);
        
        return $array;
    }
function get_branch_primary_groups(){
	    $acc_grp_parent_id = 14;
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acctgroupn_tbl);
	    $builder->where('acc_grp_parent_id', $acc_grp_parent_id);
		$builder->where('LOWER(acc_grp_primary)', 'y');
		$result = $builder->get()->getResultArray();
		$all_ids = array();
		foreach($result as $row){
			$all_ids[]=$row['acc_grp_id'];
		}
		return $all_ids;
    }
    function get_cash_groups_list()
    {
        $groups = $this->get_branch_primary_groups();
        array_push($groups,23);
		
		
		//$groups = [23];
		$array = $this->get_sub_group_ids_list($groups);
        
        return $array;
    }
 
 
 function get_sub_group_ids_list($array){
    
    	if(!empty($array)){
    		$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    		$data = $this->db->table($tbl_name)
    					->select('acc_grp_id')
    					->whereIn('under_main_grp_id', $array)
    					->get()->getResultArray();
    	
    	
	    	if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}
    	}
    	return $array;
    }
    
  function get_cc_groups_list()
    {
      
		$p_groups = [7,11,13];
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

    function get_all_accounts()
    {
        
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select('acc_id as id,  acc_grp_id, acc_name as label, acc_name as value');
        $builder->where('comp_id', $this->company_id);		
		$result = $builder->get()->getResultArray();
        
		
		return $result;
    } 
    
     function company_all_accounts(){
    
       $bbb_groups = $this->get_bbb_groups_list();
	   $cc_groups = $this->get_cc_groups_list();
	   $cash_groups = $this->get_cash_groups_list();
      
      $acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
      $tablresult     =  $this->db->query("SELECT acc_name as label, acc_name as value, acc_id, acc_grp_id, acc_grp_parent_id FROM `".$acctmaster_tbl."` order by `acc_name`  ");
	  $data   = $tablresult->getResultArray();
	  $final_result = array();
	  foreach ($data as $key => $value) {
	   		$is_bbb = 0;
	       	if(in_array($value['acc_grp_id'], $bbb_groups)){
	           	$is_bbb = 1;
	       	}

	        $is_cc = 0;
	       	if(in_array($value['acc_grp_id'], $cc_groups) || in_array($value['acc_grp_parent_id'], [7,11,13])){
	          	$is_cc = 1;
	       	}

	       	$is_cash = 0;
	       	if(in_array($value['acc_grp_id'], $cash_groups)){
	          	$is_cash = 1;
	       	}
	       	
	     	$final_result[]    = array(
               			"label" => ucwords($value['label']),
               			'value' => $value['value'],
               			'id'    => $value['acc_id'],
               			'account_id'    => $value['acc_id'],
               			'acc_id'      =>$value['acc_id'],
						'is_sundry'  => '0',
						'is_acc'  => '1',
						'is_bbb'  => $is_bbb,
						'is_cc'	  => $is_cc,
						'is_cash'	  => $is_cash,
               	);	  	
	       	
	       	
	       	
	   	}
	   	
       return json_encode($final_result);
  }  
    
    function insert_acc_op_bal($data){
	    $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->table($accoppybal_tbl)->insert($data);	
       	// return  $this->db->insertID();

       	$bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)
        			->where('bills_ref_name', 'UNDEFINED')
        			->where('acc_id', $data['acc_id'])
        			->update(['bill_op_bal' => $data['acc_op_bal']]);	
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
   
    function update_acc_op_bal($account_id,$data){
		
	  	if($this->session->get('ses_boid')!='')
           $data['bo_id'] =$this->session->get('ses_boid');
	    else
		    $data['bo_id'] =1;
		
	  $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	  $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');

	  $get_account_info = $this->get_account_info($account_id);
	  if(in_array($get_account_info['acc_grp_parent_id'], [6,7,8,9,10,11,12,13])){
	  	if($data['acc_op_bal'] != 0)
	  		return 0;
	  }

	  if(in_array($get_account_info['acc_grp_id'], $this->get_pnl_groups())){
	  	if($data['acc_op_bal'] != 0)
	  		return 0;
	  }


	  $accoppybal = $this->db->table($accoppybal_tbl)->where('acc_id',$account_id)->get()->getRowArray();
	  $old_acc_op_bal = $accoppybal['acc_op_bal'];

	  if(in_array($get_account_info['acc_grp_id'], $this->get_bbb_groups()))
	  {
		  if($data['acc_op_bal'] >= $old_acc_op_bal){

		  	$diff = parseAmount($data['acc_op_bal']) - parseAmount($old_acc_op_bal);

		  	$billmaster = $this->db->table($bill_mst_tbl)->where('bills_ref_name', 'UNDEFINED')->where('acc_id', $account_id)->get()->getRowArray();
		  	$undefined_balance = $billmaster['bill_op_bal'] + $diff;

		  	$this->db->table($bill_mst_tbl)->where('bills_ref_name', 'UNDEFINED')->where('acc_id', $account_id)->update(['bill_op_bal' => $undefined_balance]);

		  	$this->update_bill_ref_txn_balance($account_id, true);
		  }

		  if($data['acc_op_bal'] < $old_acc_op_bal){
		  	$this->db->table($bill_mst_tbl)->where('bills_ref_name', 'UNDEFINED')->where('acc_id', $account_id)->update(['bill_op_bal' => $data['acc_op_bal']]);

		  	$this->db->table($bill_mst_tbl)->where('bills_ref_name !=', 'UNDEFINED')->where('acc_id', $account_id)->update(['bill_op_bal' => 0]);
		  	
		  	$this->update_bill_ref_txn_balance($account_id);
		  }
	  }

	  	$this->db->table($accoppybal_tbl)->where('acc_id',$account_id)->update($data);	
	 }

	function get_bbb_groups()
    {
        
		$groups = [16,22];
		$array = $this->get_sub_group_ids($groups);
        
        return $array;
    }

	public function update_bill_ref_txn_balance($acc_id, $undefined = false)
	{
	 	$bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');

	 	$builder = $this->db->table($bill_mst_tbl);
	 	$builder->where('acc_id', $acc_id);
	 	if($undefined)
	 		$builder->where('bills_ref_name', 'UNDEFINED');
	 	$result = $builder->get()->getResultArray();

	 	foreach ($result as $key => $value) {
	 		$this->update_bill_txn_balance($value['bills_ref_id']);
	 	}
	}

	public function update_bill_txn_balance($bills_ref_id)
    {
    	$bal = 0;
		$bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)
        				->where('bills_ref_id', $bills_ref_id)
        				->get()->getRowArray();
        if($data)
        	$bal = $data['bill_op_bal'];

    	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$bill_txn_tbl.' SET bills_txn_bal = CASE WHEN bills_txn_drcr = "C" THEN @bal:=@bal - bills_txn_amt WHEN bills_txn_drcr = "D" THEN @bal:=@bal + bills_txn_amt ELSE 0 END where bills_ref_id = '.$bills_ref_id.' order by bills_txn_date, bills_txn_id;');  
    }
   
    public function acc_opn_balance_info($acc_id){
       $accoppybal_tbl =$this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	   if($this->session->get('ses_boid')!='')
	return $this->db->table($accoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('acc_id', $acc_id)->get()->getRowArray(); 
	   else
       return $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();  
      }  
   
   	function load_group_childs($acc_grp_id,$comp_id){
		$ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
		
		$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		
		$builder = $this->db->table($tbl_name); 
		$builder->orderBy('acc_grp_id');         
		$builder->where('comp_id', $comp_id);
		$builder->where('under_acc_grp_id', $acc_grp_id);
		$builder->where('acc_grp_id !=', $acc_grp_id);	
		$data =  $builder->get()->getResultArray();
		$all_lists=array();
		 $final_amount=0;
		 $child_sum=0;
		if($data){
			foreach($data as $row){				
				$company_all_accounts  = $this->get_group_all_accounts($row['acc_grp_id'],$comp_id);
                 if($company_all_accounts){
		           foreach($company_all_accounts as $company_row){			   
					 $account_name       =  url_title($company_row['acc_name'],'',true);
					
					 $account_txn_table  = $comp_id.'_accnttxnnn_'.$company_row['acc_id'].'_'.$ses_comp_fy_id;
					 
					 $show_account_name  =  $company_row['acc_name'];					 
					 $builder = $this->db->table($account_txn_table); 
					
					 $builder->orderBy('acc_txn_id','DESC');
                     $builder->limit(1);					
					 $builder->where('comp_id', $comp_id);
					 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
					 $builder->where('acc_id', $company_row['acc_id']);                
					 $result = $builder->get()->getRowArray();					
				     if($result){		
					     $final_amount = $final_amount+$result['acc_bal'];  
						 } 	
					else{
					    $account_info = $this->account_info($company_row['acc_id']);  
					    $final_amount  = $account_info['acc_op_bal']; 
					 }
					 
					 }
	              }				
			
			 	  
			$child_sum += $this->load_group_childs($row['acc_grp_id'],$comp_id);			
			}
		}
		return $final_amount+$child_sum;
	}
	
	function load_parent_group_data($acc_grp_id,$comp_id){
	   
		$ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
		$tbl_name         = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		$builder          = $this->db->table($tbl_name); 
		$builder->orderBy('acc_grp_id');         
		$builder->where('comp_id', $comp_id);
		$builder->where('acc_grp_id', $acc_grp_id);	
		$data  =  $builder->get()->getResultArray();
		$all_lists=array();
	    $final_amount=0;
		if($data){
			foreach($data as $row){
				 $company_all_accounts  = $this->get_group_all_accounts($row['acc_grp_id'],$comp_id);
				 
				 if($company_all_accounts){
		           foreach($company_all_accounts as $company_row){			   
					 $account_name       =  url_title($company_row['acc_name'],'',true);
					 $account_txn_table  = $comp_id.'_accnttxnnn_'.$company_row['acc_id'].'_'.$ses_comp_fy_id;
					 $show_account_name  =  $company_row['acc_name'];
					 
					$builder = $this->db->table($account_txn_table); 
					$builder->orderBy('txn_id','DESC');         
					$builder->where('comp_id', $comp_id);
					 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
					$builder->where('acc_id', $company_row['acc_id']);  
                    $builder->limit(1);					
					$result = $builder->get()->getRowArray();
				
				    if($result){		
				        $final_amount = $final_amount+$result['acc_bal'];  
					    }
					 else{
					    $account_info = $this->account_info($company_row['acc_id']);  
					    $final_amount  = $account_info['acc_op_bal']; 
					 }
					 
					    
					 }
	              }
	      
			
			}
			
	 
		}
	
		
	  return $final_amount;
	}     
 
 function check_have_child_groups($acc_grp_id,$comp_id){
		$ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
		$tbl_name        = $comp_id.'_acctgroupn_'.$ses_comp_fy_id;
		$builder         = $this->db->table($tbl_name); 
		$builder->orderBy('acc_grp_id');         
		$builder->where('comp_id', $comp_id);
		$builder->where('under_acc_grp_id', $acc_grp_id); 
		$builder->where('acc_grp_id !=', $acc_grp_id);
		$result  =  $builder->get()->getResultArray();
		return $result;
	}
	
	
 public function groups_transactions($group_id,$comp_id){
		$AccountGroupMainList  = $this->group_main_dropdown();
		$ses_comp_fy_id        = $this->session->get('ses_comp_fy_id');
		$all_lists             = array();	
        $final_amount          = 0;		
		  if($group_id>0){				
			  $group_trans_data      = $this->group_trans_data($group_id,$comp_id);
			  
		
			  $company_all_accounts  = $this->get_group_all_accounts($group_id,$comp_id);
              if($company_all_accounts){
		         foreach($company_all_accounts as $company_row){			 
                    $account_id         = $company_row['acc_id'];				
					$account_name       =  url_title($company_row['acc_name'],'',true);
					$account_txn_table  = $comp_id.'_accnttxnnn_'.$company_row['acc_id'].'_'.$ses_comp_fy_id;
					$show_account_name  =  $company_row['acc_name'];
					$show_account_group =  $AccountGroupMainList[$company_row['acc_grp_id']];
					$builder = $this->db->table($account_txn_table); 
					$builder->orderBy('txn_id','DESC');   
					$builder->limit(1);			
					$builder->where('comp_id', $comp_id);
					 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
					$builder->where('acc_id', $account_id);                
					$result = $builder->get()->getRowArray();	
					if($result)	
					$all_lists[$account_id] = array('have_childs'=>0,'balance'=>$result['acc_bal'],'account_id'=>$account_id,'account_type'=>'acc','account_name'=>$show_account_name,'group_id'=>$group_id,'group_name'=>$show_account_group);
				   else{
					    $account_info = $this->account_info($company_row['acc_id']);  
					    $final_amount  = $account_info['acc_op_bal']; 
					   	$all_lists[$account_id] = array('have_childs'=>0,'balance'=>$final_amount,'account_id'=>$account_id,'account_type'=>'acc','account_name'=>$show_account_name,'group_id'=>$group_id,'group_name'=>$show_account_group);
				   
					    }
				      }
				   }	  
				 } 				
     	return array_merge($group_trans_data,$all_lists);
	}

// Get total balance of all the groups parent id given
	function get_group_total_balance($parent,$comp_id){	 
	   $parent_balance      = $this->load_parent_group_data($parent,$comp_id); 
	   $ses_comp_fy_id      = $this->session->get('ses_comp_fy_id'); 	
	   $child_group_balance = $this->load_group_childs($parent,$comp_id);
	   
	   return ($parent_balance+$child_group_balance);
	}
	
      function group_trans_data($sgroup_id,$comp_id){

	 $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id'); 	
	 $grouplist         = $this->check_have_child_groups($sgroup_id,$comp_id);
	 
	 $all_lists = array();    
	 $subgroup_acc_lists = array();
	 if($grouplist){
	 foreach($grouplist as $group_row){   
     $group_id              = $group_row['acc_grp_id'];
	 $show_account_group    = $group_row['acc_grp_name'];
	 
	 $total_balance  = $this->get_group_total_balance($group_row['acc_grp_id'],$comp_id);
	 $has_child_group = $this->check_have_child_groups($group_row['acc_grp_id'],$comp_id); 
				if(count($has_child_group)>0)
					$have_childs=1;
				 else
					$have_childs=0; 
				
	  $group_sums=array();
	   $company_all_accounts  = $this->get_group_all_accounts($group_row['acc_grp_id'],$comp_id);
	   
	   
	   if($company_all_accounts){
		        foreach($company_all_accounts as $company_row){			   
					 $account_name       =  url_title($company_row['acc_name'],'',true);
					  
					 $show_account_name  = $company_row['acc_name'];
					 $all_lists[$group_id] = array('balance'=>$total_balance,'account_type'=>'grp','have_childs'=>$have_childs,'account_name'=>$show_account_group,'group_id'=>$group_id,'group_name'=>$show_account_group);
				
					 }
						
	                }
	             }	
             }	
          
		
	   return $all_lists;
   }

   function bill_sundry_op_balance($id)
    {
    	$bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$result = $this->db->table($bsdoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('bill_sundry_id', $id)->get()->getRowArray();
		 else
	    $result = $this->db->table($bsdoppybal_tbl)->where('bill_sundry_id', $id)->get()->getRowArray();
	    return $result; 
    }
   
   
    function load_group_accounts_sum($group_id,$comp_id){
         $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
         $account_master_tbl = $comp_id.'_acctmaster_'.$ses_comp_fy_id;
         $tbl_name           = $comp_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
           $billsundry_tbl = $comp_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
           
         $sql    = $this->db->query("select `acc_grp_id`,`under_acc_grp_id`, `acc_grp_parent_id`, `acc_grp_name` from (select * from `".$tbl_name."` order by `under_acc_grp_id`, `acc_grp_id`) `".$tbl_name."` , (select @pv := '".$group_id."') initialisation where `acc_grp_id`!='".$group_id."' and find_in_set(`under_acc_grp_id`, @pv) > 0 and @pv := concat(@pv, ',', `acc_grp_id`);");
         $result = $sql->getResultArray();
         
        // echo $this->db->GetLastQuery();
         //echo'<br>';
         $sbgroup_ids = array();
         
         // check for bill sundry acccount
    		     
    		    
    		     
    		     
         $final_sum=0;
         if($result){
             foreach($result as $row){
                    $sub_group_id               = $row['acc_grp_id'];
                    $sbgroup_ids[$sub_group_id] = $sub_group_id;
                    
                    
                   
           	 $builder2 = $this->db->table($billsundry_tbl); 
        	 $builder2->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
        	 $builder2->where('acc_grp_id', $sub_group_id);
			 if($this->session->get('ses_boid')!='')
				$builder2->where('bo_id', $this->session->get('ses_boid'));
        	 $billsndry_result = $builder2->get()->getResultArray();
    		    
                    
                    $builder = $this->db->table($account_master_tbl); 
            		$builder->select(array('acc_id','acc_name','acc_op_bal','acc_grp_id'));
                    $builder->where('comp_id', $comp_id);		
            		$builder->where('acc_grp_id', $sub_group_id);
            		$result = $builder->get()->getResultArray();
            		$final_groups = array();
            		$credit=$debit=$final_account_balance=0;
    	     	    if($result){
    		        foreach($result as $row){
    		        
    		             $account_info       = $this->account_info($row['acc_id']);
    		             $account_id         = $account_info['acc_id'];
    					 $account_name       = $account_info['acc_name'];
    					
    					
    					 $account_txn_table = $comp_id.'_accnttxnnn_'.$row['acc_id'].'_'.$ses_comp_fy_id;
    					 $builder = $this->db->table($account_txn_table); 
    					 $builder->orderBy('acc_txn_id','DESC');
                         $builder->where('comp_id', $this->company_id);
						  // branch check 
						if($this->session->get('ses_boid')!='')
							$builder->where('bo_id', $this->session->get('ses_boid'));
    					 $builder->where('acc_id', $row['acc_id']);                
    					 $subresult = $builder->get()->getResultArray();
    					 
    					 
    				     if($subresult){		
    				          $final_account_balance = $subresult[0]['acc_bal']; 	
    				     }
    					  else{
    					      // fetch opening balance if added or not 
    					        $account_balance_type    = $account_info['acc_op_drcr']; //cr/dr
    					        if($account_balance_type=='cr'){
    					           $final_account_balance='-'.$account_info['acc_op_bal'];
    					        }
    					      else if($account_balance_type=='dr'){
    					           $final_account_balance = $account_info['acc_op_bal'];
    					           
    					        }
    					  	}
    					  	
    					  	$final_sum =$final_sum+$final_account_balance;
    		           }
    		     }
    		     else if($billsndry_result){
    		    foreach($billsndry_result as $bls_row){
    		         $account_info       = $this->billsundry_info($bls_row['bill_sundry_id']);
    		         $blsndry_id         = $account_info['bill_sundry_id'];
    				 $account_name       = $account_info['bill_sundry_name'];
    				 $balance_type ='DR.';      	
    		         $credit=$debit=0;
    		        $billsndry_txn_table  =  $comp_id.'_sundrytxnn_'.$bls_row['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
    		        $builder = $this->db->table($billsndry_txn_table);
    		        $builder->orderBy('sundry_txn_date','DESC');
        			$builder->orderBy('sundry_txn_id','DESC');         
        			$builder->where('comp_id', $comp_id);
        			$builder->where('bill_sundry_id', $bls_row['bill_sundry_id']);                
        			$txnnresult = $builder->get()->getResultArray();
        			if($txnnresult){
    				         foreach($txnnresult as $sbrow){
    					       $acc_txn_drcr=$sbrow['sundry_txn_drcr'];
    					       if($acc_txn_drcr=='c'){
    					         $credit=$credit+$sbrow['sundry_txn_amount'];
    					       
    					       }
    					      else  if($acc_txn_drcr=='d'){
    					         $debit=$debit+$sbrow['sundry_txn_amount'];
    					           }
    					           
    					      
    				            }
    				            if(isset($txnnresult[0])){
    					              $final_account_balance = $txnnresult[0]['sundry_bal'];
    					              if($final_account_balance<0)
    					                 $balance_type ='CR.';
    					               else
    					                 $balance_type ='DR.'; 
    				            }
    					     else
    					              $final_account_balance =0;
    					              
    					              	$final_sum =$final_sum+$final_account_balance;
    						 } 
    						 
    		    }
             
         }    
    		     
    		     
    		     
    		     
    		     
    		     
            }
            
        } 
         
    
         //get billsundry accounts list 
       /*
         if($sbgroup_ids){
           	 $billsundry_tbl = $comp_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
        	 $builder = $this->db->table($billsundry_tbl); 
        	 $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
        	 $builder->whereIn('acc_grp_id', $sbgroup_ids);
        	 $billsndry_result = $builder->get()->getResultArray();
        	 
        	 
         if($billsndry_result){
    		    foreach($billsndry_result as $bls_row){
    		         $account_info       = $this->billsundry_info($bls_row['bill_sundry_id']);
    		         $blsndry_id         = $account_info['bill_sundry_id'];
    				 $account_name       = $account_info['bill_sundry_name'];
    				 $balance_type ='DR.';      	
    		         $credit=$debit=0;
    		        $billsndry_txn_table  =  $comp_id.'_sundrytxnn_'.$bls_row['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
    		        $builder = $this->db->table($billsndry_txn_table);
    		        $builder->orderBy('sundry_txn_date','DESC');
        			$builder->orderBy('sundry_txn_id','DESC');         
        			$builder->where('comp_id', $comp_id);
        			$builder->where('bill_sundry_id', $bls_row['bill_sundry_id']);                
        			$txnnresult = $builder->get()->getResultArray();
        			if($txnnresult){
    				         foreach($txnnresult as $sbrow){
    					       $acc_txn_drcr=$sbrow['sundry_txn_drcr'];
    					       if($acc_txn_drcr=='c'){
    					         $credit=$credit+$sbrow['sundry_txn_amount'];
    					       
    					       }
    					      else  if($acc_txn_drcr=='d'){
    					         $debit=$debit+$sbrow['sundry_txn_amount'];
    					           }
    					           
    					      
    				            }
    				            if(isset($txnnresult[0])){
    					              $final_account_balance = $txnnresult[0]['sundry_bal'];
    					              if($final_account_balance<0)
    					                 $balance_type ='CR.';
    					               else
    					                 $balance_type ='DR.'; 
    				            }
    					     else
    					              $final_account_balance =0;
    					              
    					              	$final_sum =$final_sum+$final_account_balance;
    						 } 
    						 
    		    }
             
         }
         }
         
        */
         return $final_sum;
    }

    public function load_accounts_trial_balance2($group_id,$from_date,$to_date)
    {
   		$account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
   		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
   		$sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

	  	
	  	$final = [];
	  	$balance_total = 0;

		//check sub group
  		$result2 = $this->db->table($account_grp_tbl)->where('under_acc_grp_id',$group_id)->get()->getResultArray();
  		if($result2)
  		{
  			foreach ($result2 as $key2 => $value2) 
  			{
  				$entity_type = 'grp';
				$entity_name = $value2['acc_grp_name'];
				$entity_id = $value2['acc_grp_id'];

				$credit = '';
		  		$debit = '';
		  		$balance = '';
		  		$credit_total = 0;
		  		$debit_total = 0;
		  		$balance_total = 0;

  				//check accounts
			    $builder = $this->db->table($account_master_tbl); 
				$builder->select(array('acc_id','acc_name','acc_grp_id'));
				$builder->where('acc_grp_id', $value2['acc_grp_id']);
				$accounts = $builder->get()->getResultArray();
				if($accounts){
					foreach ($accounts as  $account) {

						//check all transaction
						$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
				    	$builder->where('acc_id', $account['acc_id']);
				    	$builder->where('acc_txn_date >=', $from_date);
				    	$builder->where('acc_txn_date <=', $to_date);
						 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				    	$builder->orderBy('acc_txn_date', 'asc');
				    	$builder->orderBy('acc_txn_id', 'asc');
				    	$transactions = $builder->get()->getResultArray();
				    	if($transactions)
				    	{
				    		foreach ($transactions as $transaction) {
				    			if($transaction['acc_txn_drcr'] == 'c')
									$credit_total += $transaction['acc_txn_amount'];
								if($transaction['acc_txn_drcr'] == 'd')
									$debit_total += $transaction['acc_txn_amount'];
				    		}
				    		
				    	}

				    	//check last transaction
						$builder = $this->db->table($acc_txn_tbl);
				    	$builder->where('acc_id', $account['acc_id']);
				    	$builder->where('acc_txn_date <=', $to_date);
						if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				    	$builder->orderBy('acc_txn_date', 'desc');
				    	$builder->orderBy('voucher_txn_id', 'desc');
				    	$builder->orderBy('acc_txn_id', 'desc');
				    	$builder->limit(1);
				    	$transaction = $builder->get()->getRowArray();
				    	if($transaction)
				    	{
				    		$balance_total += parseAmount($transaction['acc_bal']);
				    	}
				    	else
				    	{ 	//check opening balance
				    		$get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
				    		if($get_opn_balance_info)
				    			$balance_total += parseAmount($get_opn_balance_info['acc_op_bal']);
						}
					}
				}

  				//check sundry accounts
				$builder = $this->db->table($sundry_master_tbl); 
				$builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
				$builder->where('acc_grp_id', $value2['acc_grp_id']);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				$sundry_accounts = $builder->get()->getResultArray();
				if($sundry_accounts){
					foreach ($sundry_accounts as $sundry_account) {

						//check all transaction
						$bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($bs_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
						$builder->where('sundry_txn_date >=', $from_date);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->orderBy('sundry_txn_date', 'asc');
						$builder->orderBy('sundry_txn_id', 'asc');
						$transactions = $builder->get()->getResultArray();
						if($transactions)
				    	{
				    		foreach ($transactions as $transaction) {
				    			if($transaction['sundry_txn_drcr'] == 'c')
									$credit_total += $transaction['sundry_txn_amount'];
								if($transaction['sundry_txn_drcr'] == 'd')
									$debit_total += $transaction['sundry_txn_amount'];
				    		}
				    	}

				    	//check last transaction
				    	$builder = $this->db->table($bs_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('sundry_txn_id', 'desc');
						$builder->limit(1);
						$transaction = $builder->get()->getRowArray();
						if($transaction){
				    		$balance_total += parseAmount($transaction['sundry_bal']);
				    	}
				    	else{ //check opening balance
				    		$bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
				    		if($bill_sundry_op_balance){
				    			$balance_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
				    		}
				    		
				    	}
					}
				}

				if($credit_total > 0)
		  			$credit = formatAmount($credit_total);
		  		if($debit_total > 0)
		  			$debit = formatAmount($debit_total);

		    	$balance_type = '';
		  		if($balance_total < 0){
		  			$balance_type = 'CR';
		  			$balance = formatAmount(abs($balance_total));
		  		}
		  		if($balance_total > 0){
		  			$balance_type = 'DR';
		  			$balance = formatAmount($balance_total);
		  		}

		  		$final[] = [
		  			'entity_type' 	=> $entity_type,
		  			'entity_name'	=> $entity_name,
		  			'entity_id'		=> $entity_id,
		  			'credit'		=> $credit,
		  			'debit'			=> $debit,
		  			'balance'		=> $balance,
		  			'credit_total'	=> $credit_total,
		  			'debit_total'	=> $debit_total,
		  			'balance_total'	=> $balance_total,
		  			'balance_type'  => $balance_type
		  		];
  			}
  		}


  		//check accounts
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name','acc_grp_id'));
		$builder->where('acc_grp_id', $group_id);
		$accounts = $builder->get()->getResultArray(); 
		if($accounts){
			foreach ($accounts as $account) {
				$entity_type = 'acc';
				$entity_name = $account['acc_name'];
				$entity_id = $account['acc_id'];

				$credit = '';
		  		$debit = '';
		  		$balance = '';
		  		$credit_total = 0;
		  		$debit_total = 0;
		  		$balance_total = 0;

				//check all transaction
				$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('acc_id', $account['acc_id']);
		    	$builder->where('acc_txn_date >=', $from_date);
		    	$builder->where('acc_txn_date <=', $to_date);
				 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->orderBy('acc_txn_date', 'asc');
		    	$builder->orderBy('acc_txn_id', 'asc');
		    	$transactions = $builder->get()->getResultArray();
		    	if($transactions)
		    	{
		    		foreach ($transactions as $transaction) {
		    			if($transaction['acc_txn_drcr'] == 'c')
							$credit_total += $transaction['acc_txn_amount'];
						if($transaction['acc_txn_drcr'] == 'd')
							$debit_total += $transaction['acc_txn_amount'];
		    		}
		    		
		    	}

		    	//check last transaction
				$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('acc_id', $account['acc_id']);
		    	$builder->where('acc_txn_date <=', $to_date);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->orderBy('acc_txn_date', 'desc');
		    	$builder->orderBy('voucher_txn_id', 'desc');
		    	$builder->orderBy('acc_txn_id', 'desc');
		    	$builder->limit(1);
		    	$transaction = $builder->get()->getRowArray();
		    	if($transaction){
		    		$balance_total = parseAmount($transaction['acc_bal']);
		    	}
		    	else{ 	//check opening balance
		    		$get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
		    		if($get_opn_balance_info)
		    			$balance_total = parseAmount($get_opn_balance_info['acc_op_bal']);
				}

		    	if($credit_total > 0)
		  			$credit = formatAmount($credit_total);
		  		if($debit_total > 0)
		  			$debit = formatAmount($debit_total);

		  		$balance_total = parseAmount($balance_total);

		    	$balance_type = '';
		  		if($balance_total < 0){
		  			$balance_type = 'CR';
		  			$balance = formatAmount(abs($balance_total));
		  		}
		  		if($balance_total > 0){
		  			$balance_type = 'DR';
		  			$balance = formatAmount($balance_total);
		  		}

		  		$final[] = [
		  			'entity_type' 	=> $entity_type,
		  			'entity_name'	=> $entity_name,
		  			'entity_id'		=> $entity_id,
		  			'credit'		=> $credit,
		  			'debit'			=> $debit,
		  			'balance'		=> $balance,
		  			'credit_total'	=> $credit_total,
		  			'debit_total'	=> $debit_total,
		  			'balance_total'	=> $balance_total,
		  			'balance_type'  => $balance_type
		  		];

			}
		}

		//check sundry accounts
		$builder = $this->db->table($sundry_master_tbl); 
		$builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
		$builder->where('acc_grp_id', $group_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$sundry_accounts = $builder->get()->getResultArray();
		if($sundry_accounts){
			foreach ($sundry_accounts as $sundry_account) {

				$entity_type = 'bsd';
				$entity_name = $sundry_account['bill_sundry_name'];
				$entity_id = $sundry_account['bill_sundry_id'];

				$credit = '';
		  		$debit = '';
		  		$balance = '';
		  		$credit_total = 0;
		  		$debit_total = 0;
		  		$balance_total = 0;


				//check all transaction
				$bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($bs_txn_tbl);
				$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
				$builder->where('sundry_txn_date >=', $from_date);
				$builder->where('sundry_txn_date <=', $to_date);
				$builder->orderBy('sundry_txn_date', 'asc');
				$builder->orderBy('sundry_txn_id', 'asc');
				$transactions = $builder->get()->getResultArray();
				if($transactions)
		    	{
		    		foreach ($transactions as $transaction) {
		    			if($transaction['sundry_txn_drcr'] == 'c')
							$credit_total += $transaction['sundry_txn_amount'];
						if($transaction['sundry_txn_drcr'] == 'd')
							$debit_total += $transaction['sundry_txn_amount'];
		    		}
		    	}

		    	//check last transaction
		    	$builder = $this->db->table($bs_txn_tbl);
				$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
				$builder->where('sundry_txn_date <=', $to_date);
				$builder->orderBy('sundry_txn_date', 'desc');
				$builder->orderBy('sundry_txn_id', 'desc');
				$builder->limit(1);
				$transaction = $builder->get()->getRowArray();
				if($transaction){
		    		$balance_total = parseAmount($transaction['sundry_bal']);
		    	}
		    	else{ //check opening balance
		    		$bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
		    		if($bill_sundry_op_balance){
		    			$balance_total = parseAmount($bill_sundry_op_balance['bsd_op_bal']);
		    		}
		    	}

		    	if($credit_total > 0)
		  			$credit = formatAmount($credit_total);
		  		if($debit_total > 0)
		  			$debit = formatAmount($debit_total);

		    	$balance_type = '';
		  		if($balance_total < 0){
		  			$balance_type = 'CR';
		  			$balance = formatAmount(abs($balance_total));
		  		}
		  		if($balance_total > 0){
		  			$balance_type = 'DR';
		  			$balance = formatAmount($balance_total);
		  		}

		  		$final[] = [
		  			'entity_type' 	=> $entity_type,
		  			'entity_name'	=> $entity_name,
		  			'entity_id'		=> $entity_id,
		  			'credit'		=> $credit,
		  			'debit'			=> $debit,
		  			'balance'		=> $balance,
		  			'credit_total'	=> $credit_total,
		  			'debit_total'	=> $debit_total,
		  			'balance_total'	=> $balance_total,
		  			'balance_type'  => $balance_type
		  		];
			}
		}
	  	// echo "<pre>";print_r($final);exit;
	  	return $final;
   	}

   	public function load_accounts_trial_balance3($acc_grp_parent_id,$from_date,$to_date)
    {
   		$account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
   		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
   		$sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

	  	
	  	$final = [];
	  	$balance_total = 0;

  		$result =  $this->db->table($account_grp_tbl)->where('acc_grp_primary', 'Y')
	   													 ->where('acc_grp_parent_id', $acc_grp_parent_id)
	   													 ->get()->getResultArray();
  		if($result)
  		{
  			foreach ($result as $key => $value) 
  			{
  				$entity_type = 'grp';
				$entity_name = $value['acc_grp_name'];
				$entity_id = $value['acc_grp_id'];

				$credit = '';
		  		$debit = '';
		  		$balance = '';
		  		$credit_total = 0;
		  		$debit_total = 0;
		  		$balance_total = 0;

		  		//check sub group
		  		$result2 = $this->db->table($account_grp_tbl)->where('under_acc_grp_id',$entity_id)->get()->getResultArray();
		  		if($result2)
		  		{
		  			foreach ($result2 as $key2 => $value2) 
		  			{
		  				//check accounts
					    $builder = $this->db->table($account_master_tbl); 
						$builder->select(array('acc_id','acc_name','acc_grp_id'));
						$builder->where('acc_grp_id', $value2['acc_grp_id']);
						$accounts = $builder->get()->getResultArray();
						if($accounts){
							foreach ($accounts as  $account) {

								//check all transaction
								$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
								$builder = $this->db->table($acc_txn_tbl);
						    	$builder->where('acc_id', $account['acc_id']);
								
						    	$builder->where('acc_txn_date >=', $from_date);
								 // branch check 
					           if($this->session->get('ses_boid')!='')
					             $builder->where('bo_id', $this->session->get('ses_boid'));
						    	$builder->where('acc_txn_date <=', $to_date);
						    	$builder->orderBy('acc_txn_date', 'asc');
						    	$builder->orderBy('acc_txn_id', 'asc');
						    	$transactions = $builder->get()->getResultArray();
						    	if($transactions)
						    	{
						    		foreach ($transactions as $transaction) {
						    			if($transaction['acc_txn_drcr'] == 'c')
											$credit_total += $transaction['acc_txn_amount'];
										if($transaction['acc_txn_drcr'] == 'd')
											$debit_total += $transaction['acc_txn_amount'];
						    		}
						    		
						    	}

						    	//check last transaction
								$builder = $this->db->table($acc_txn_tbl);
						    	$builder->where('acc_id', $account['acc_id']);
						    	$builder->where('acc_txn_date <=', $to_date);
								if($this->session->get('ses_boid')!='')
					             $builder->where('bo_id', $this->session->get('ses_boid'));
						    	$builder->orderBy('acc_txn_date', 'desc');
						    	$builder->orderBy('voucher_txn_id', 'desc');
						    	$builder->orderBy('acc_txn_id', 'desc');
						    	$builder->limit(1);
						    	$transaction = $builder->get()->getRowArray();
						    	if($transaction){
						    		$balance_total += parseAmount($transaction['acc_bal']);
						    	}
						    	else{ 	//check opening balance
						    		$get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
						    		if($get_opn_balance_info)
						    			$balance_total += parseAmount($get_opn_balance_info['acc_op_bal']);
								}
							}
						}

		  				//check sundry accounts
						$builder = $this->db->table($sundry_master_tbl); 
						$builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
						$builder->where('acc_grp_id', $value2['acc_grp_id']);
						
						if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
						$sundry_accounts = $builder->get()->getResultArray();
						if($sundry_accounts){
							foreach ($sundry_accounts as $sundry_account) {

								//check all transaction
								$bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
								$builder = $this->db->table($bs_txn_tbl);
								$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
								$builder->where('sundry_txn_date >=', $from_date);
								$builder->where('sundry_txn_date <=', $to_date);
								$builder->orderBy('sundry_txn_date', 'asc');
								$builder->orderBy('sundry_txn_id', 'asc');
								$transactions = $builder->get()->getResultArray();
								if($transactions)
						    	{
						    		foreach ($transactions as $transaction) {
						    			if($transaction['sundry_txn_drcr'] == 'c')
											$credit_total += $transaction['sundry_txn_amount'];
										if($transaction['sundry_txn_drcr'] == 'd')
											$debit_total += $transaction['sundry_txn_amount'];
						    		}
						    	}
						    	//check last transaction
						    	$builder = $this->db->table($bs_txn_tbl);
								$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
								$builder->where('sundry_txn_date <=', $to_date);								
								$builder->orderBy('sundry_txn_date', 'desc');								
								$builder->orderBy('sundry_txn_id', 'desc');
								$builder->limit(1);
								$transaction = $builder->get()->getRowArray();
								if($transaction){
						    		$balance_total += parseAmount($transaction['sundry_bal']);
						    	}
						    	else{ //check opening balance
						    		$bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
						    		if($bill_sundry_op_balance){
						    			$balance_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
						    		}
						    	}
							}
						}
		  			}
		  		}

  				//check accounts
			    $builder = $this->db->table($account_master_tbl); 
				$builder->select(array('acc_id','acc_name','acc_grp_id'));
				$builder->where('acc_grp_id', $entity_id);
				$accounts = $builder->get()->getResultArray();
				if($accounts){
					foreach ($accounts as  $account) {

						//check all transaction
						$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
				    	$builder->where('acc_id', $account['acc_id']);
				    	$builder->where('acc_txn_date >=', $from_date);
				    	$builder->where('acc_txn_date <=', $to_date);
						 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				    	$builder->orderBy('acc_txn_date', 'asc');
				    	$builder->orderBy('acc_txn_id', 'asc');
				    	$transactions = $builder->get()->getResultArray();
				    	if($transactions)
				    	{
				    		foreach ($transactions as $transaction) {
				    			if($transaction['acc_txn_drcr'] == 'c')
									$credit_total += $transaction['acc_txn_amount'];
								if($transaction['acc_txn_drcr'] == 'd')
									$debit_total += $transaction['acc_txn_amount'];
				    		}
				    		
				    	}
				    	//check last transaction
						$builder = $this->db->table($acc_txn_tbl);
				    	$builder->where('acc_id', $account['acc_id']);
						if($this->session->get('ses_boid')!='')
					       $builder->where('bo_id', $this->session->get('ses_boid'));
				    	$builder->where('acc_txn_date <=', $to_date);
				    	$builder->orderBy('acc_txn_date', 'desc');
				    	$builder->orderBy('voucher_txn_id', 'desc');
				    	$builder->orderBy('acc_txn_id', 'desc');
				    	$builder->limit(1);
				    	$transaction = $builder->get()->getRowArray();
				    	if($transaction){
				    		$balance_total += parseAmount($transaction['acc_bal']);
				    	}
				    	else{ 	//check opening balance
				    		$get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
				    		if($get_opn_balance_info)
				    			$balance_total += parseAmount($get_opn_balance_info['acc_op_bal']);
						}
					}
				}

  				//check sundry accounts
				$builder = $this->db->table($sundry_master_tbl); 
				$builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
				$builder->where('acc_grp_id', $entity_id);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$sundry_accounts = $builder->get()->getResultArray();
				if($sundry_accounts){
					foreach ($sundry_accounts as $sundry_account) {

						//check all transaction
						$bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($bs_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
						$builder->where('sundry_txn_date >=', $from_date);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->orderBy('sundry_txn_date', 'asc');
						$builder->orderBy('sundry_txn_id', 'asc');
						$transactions = $builder->get()->getResultArray();
						if($transactions)
				    	{
				    		foreach ($transactions as $transaction) {
				    			if($transaction['sundry_txn_drcr'] == 'c')
									$credit_total += $transaction['sundry_txn_amount'];
								if($transaction['sundry_txn_drcr'] == 'd')
									$debit_total += $transaction['sundry_txn_amount'];
				    		}
				    	}

				    	//check last transaction
				    	$builder = $this->db->table($bs_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('sundry_txn_id', 'desc');
						$builder->limit(1);
						$transaction = $builder->get()->getRowArray();
						if($transaction){
				    		$balance_total += parseAmount($transaction['sundry_bal']);
				    	}
				    	else{ //check opening balance
				    		$bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
				    		if($bill_sundry_op_balance){
				    			$balance_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
				    		}
				    	}
					}
				}

				if($credit_total > 0)
		  			$credit = formatAmount($credit_total);
		  		if($debit_total > 0)
		  			$debit = formatAmount($debit_total);

		    	$balance_type = '';
		  		if($balance_total < 0){
		  			$balance_type = 'CR';
		  			$balance = formatAmount(abs($balance_total));
		  		}
		  		if($balance_total > 0){
		  			$balance_type = 'DR';
		  			$balance = formatAmount($balance_total);
		  		}

		  		$final[] = [
		  			'entity_type' 	=> $entity_type,
		  			'entity_name'	=> $entity_name,
		  			'entity_id'		=> $entity_id,
		  			'credit'		=> $credit,
		  			'debit'			=> $debit,
		  			'balance'		=> $balance,
		  			'credit_total'	=> $credit_total,
		  			'debit_total'	=> $debit_total,
		  			'balance_total'	=> $balance_total,
		  			'balance_type'  => $balance_type
		  		];
  			}
  		}

  		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');

	   	$result3 =  $this->db->table($account_master_tbl)
											 ->where('acc_grp_parent_id', $acc_grp_parent_id)
											 ->get()->getResultArray();
	  	if($result3) 
	  	{
	  		foreach ($result3 as $key3 => $value3) 
	  		{
		  		$entity_type = 'acc';
				$entity_name = $value3['acc_name'];
				$entity_id = $value3['acc_id'];

				$credit = '';
		  		$debit = '';
		  		$balance = '';
		  		$credit_total = 0;
		  		$debit_total = 0;
		  		$balance_total = 0;		

				//check all transaction
				$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value3['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('acc_id', $value3['acc_id']);
		    	$builder->where('acc_txn_date >=', $from_date);
		    	$builder->where('acc_txn_date <=', $to_date);
				 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->orderBy('acc_txn_date', 'asc');
		    	$builder->orderBy('acc_txn_id', 'asc');
		    	$transactions = $builder->get()->getResultArray();
		    	if($transactions)
		    	{
		    		foreach ($transactions as $transaction) {
		    			if($transaction['acc_txn_drcr'] == 'c')
							$credit_total += $transaction['acc_txn_amount'];
						if($transaction['acc_txn_drcr'] == 'd')
							$debit_total += $transaction['acc_txn_amount'];
		    		}
		    		
		    	}
		    	//check last transaction
				$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('acc_id', $value3['acc_id']);
		    	$builder->where('acc_txn_date <=', $to_date);
				if($this->session->get('ses_boid')!='')
				  $builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->orderBy('acc_txn_date', 'desc');
		    	$builder->orderBy('voucher_txn_id', 'desc');
		    	$builder->orderBy('acc_txn_id', 'desc');
		    	$builder->limit(1);
		    	$transaction = $builder->get()->getRowArray();
		    	if($transaction){
		    		$balance_total = parseAmount($transaction['acc_bal']);
		    	}
		    	else{ 	//check opening balance
		    		$get_opn_balance_info = $this->acc_opn_balance_info($value3['acc_id']);
		    		if($get_opn_balance_info)
		    			$balance_total = parseAmount($get_opn_balance_info['acc_op_bal']);
				}


		  		if($credit_total > 0)
		  			$credit = formatAmount($credit_total);
		  		if($debit_total > 0)
		  			$debit = formatAmount($debit_total);

		    	$balance_type = '';
		  		if($balance_total < 0){
		  			$balance_type = 'CR';
		  			$balance = formatAmount(abs($balance_total));
		  		}
		  		if($balance_total > 0){
		  			$balance_type = 'DR';
		  			$balance = formatAmount($balance_total);
		  		}

		  		$final[] = [
		  			'entity_type' 	=> $entity_type,
		  			'entity_name'	=> $entity_name,
		  			'entity_id'		=> $entity_id,
		  			'credit'		=> $credit,
		  			'debit'			=> $debit,
		  			'balance'		=> $balance,
		  			'credit_total'	=> $credit_total,
		  			'debit_total'	=> $debit_total,
		  			'balance_total'	=> $balance_total,
		  			'balance_type'  => $balance_type
		  		];
	  		}
	  	}
		
		if($this->session->get('ses_boid')!=''){
		$result4 =  $this->db->table($sundry_master_tbl)
						 ->where('acc_grp_parent_id', $acc_grp_parent_id)
				    	 ->where('bo_id', $this->session->get('ses_boid'))
					    ->get()->getResultArray();											 
		}
			
         else{
	  	$result4 =  $this->db->table($sundry_master_tbl)
											 ->where('acc_grp_parent_id', $acc_grp_parent_id)
											 ->get()->getResultArray();
		 }
	  	if($result4)  
	  	{
	  		foreach ($result4 as $key4 => $value4) 
	  		{
		  		$entity_type = 'bsd';
				$entity_name = $value4['bill_sundry_name'];
				$entity_id = $value4['bill_sundry_id'];

				$credit = '';
		  		$debit = '';
		  		$balance = '';
		  		$credit_total = 0;
		  		$debit_total = 0;
		  		$balance_total = 0;		

				//check all transaction
				$bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($bs_txn_tbl);
		    	$builder->where('bill_sundry_id', $value4['bill_sundry_id']);
		    	$builder->where('sundry_txn_date >=', $from_date);
		    	$builder->where('sundry_txn_date <=', $to_date);
		    	$builder->orderBy('sundry_txn_date', 'asc');
		    	$builder->orderBy('sundry_txn_id', 'asc');
		    	$transactions = $builder->get()->getResultArray();
		    	if($transactions)
		    	{
		    		foreach ($transactions as $transaction) {
		    			if($transaction['sundry_txn_drcr'] == 'c')
							$credit_total += $transaction['sundry_txn_amount'];
						if($transaction['sundry_txn_drcr'] == 'd')
							$debit_total += $transaction['sundry_txn_amount'];
		    		}
		    		
		    	}
		    	//check last transaction
				$builder = $this->db->table($bs_txn_tbl);
		    	$builder->where('bill_sundry_id', $value4['bill_sundry_id']);
		    	$builder->where('sundry_txn_date <=', $to_date);
		    	$builder->orderBy('sundry_txn_date', 'desc');
		    	$builder->orderBy('sundry_txn_id', 'desc');
		    	$builder->limit(1);
		    	$transaction = $builder->get()->getRowArray();
		    	if($transaction){
		    		$balance_total = parseAmount($transaction['sundry_bal']);
		    	}
		    	else{ 	//check opening balance
		    		$bill_sundry_op_balance = $this->bill_sundry_op_balance($value4['bill_sundry_id']);
		    		if($bill_sundry_op_balance)
		    			$balance_total = parseAmount($bill_sundry_op_balance['bsd_op_bal']);
				}


		  		if($credit_total > 0)
		  			$credit = formatAmount($credit_total);
		  		if($debit_total > 0)
		  			$debit = formatAmount($debit_total);

		    	$balance_type = '';
		  		if($balance_total < 0){
		  			$balance_type = 'CR';
		  			$balance = formatAmount(abs($balance_total));
		  		}
		  		if($balance_total > 0){
		  			$balance_type = 'DR';
		  			$balance = formatAmount($balance_total);
		  		}

		  		$final[] = [
		  			'entity_type' 	=> $entity_type,
		  			'entity_name'	=> $entity_name,
		  			'entity_id'		=> $entity_id,
		  			'credit'		=> $credit,
		  			'debit'			=> $debit,
		  			'balance'		=> $balance,
		  			'credit_total'	=> $credit_total,
		  			'debit_total'	=> $debit_total,
		  			'balance_total'	=> $balance_total,
		  			'balance_type'  => $balance_type
		  		];
	  		}
	  	}

	  	// echo "<pre>";print_r($final);exit;
	  	return $final;
   	}

     public function load_accounts_trial_balance($group_id,$comp_id){
		    $ses_comp_fy_id        = $this->session->get('ses_comp_fy_id');
		    $all_lists             = array();
		    $group_all_lists       = array();
		    $show_list = array();
	        $balance           = $this->load_group_accounts_sum($group_id,$comp_id);
		    
		     if($balance<0)
                     {
                       $credit=  str_replace("-",'',$balance);   
                       $balance_type ='CR.';  
                       $debit =0;
                       $show_group_balance = str_replace("-",'',$balance);
                     }
                     else{
                       $debit =$balance;  
                       $credit =0;
                       $balance_type ='DR.';  
                       $show_group_balance = $balance;
                     }
         
		    
		    $tbl_name         = $comp_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
		    $sql =$this->db->query("select `acc_grp_id`,`under_acc_grp_id`, `acc_grp_parent_id`, `acc_grp_name` from (select * from `".$tbl_name."` order by `under_acc_grp_id`, `acc_grp_id`) `".$tbl_name."` , (select @pv := '".$group_id."') initialisation where acc_grp_id!='".$group_id."' and find_in_set(`under_acc_grp_id`, @pv) > 0 and @pv := concat(@pv, ',', `acc_grp_id`);");
            $result = $sql->getResultArray();
           //echo $this->db->GetLastQuery();
            if($result){
              foreach($result as $grouprow){
                  
                  $have_childs = $this->check_have_child_groups($grouprow['acc_grp_id'],$comp_id);
                  if($have_childs)
                    $have_childs_val="1";
                else
                    $have_childs_val ="0";
                    
    				    
                  $group_all_lists[]=array("account_type"=>"grp","account_name"=>$grouprow['acc_grp_name'],"account_id"=>$grouprow['acc_grp_id'],
                                     "account_id"=>$grouprow['acc_grp_id'],"debit"=>$debit,"credit"=>$credit,'balance'=>$balance,
						             'show_group_balance'=>$show_group_balance,'balance_type'=>$balance_type,'have_childs'=>$have_childs_val);
              }    
                
                
            }
   
		  	$account_master_tbl = $comp_id.'_acctmaster_'.$ses_comp_fy_id;
    	    $builder = $this->db->table($account_master_tbl); 
    		$builder->select(array('acc_id','acc_name','acc_op_bal','acc_grp_id'));
            $builder->where('comp_id', $comp_id);		
    		$builder->where('acc_grp_id', $group_id);
    		$result = $builder->get()->getResultArray();
    		$final_groups= array();
        	$final_account_balance=0;
    		if($result){
    		    foreach($result as $row){
    		            	$credit=$debit=0;
    		             $account_info       = $this->account_info($row['acc_id']);
    		             $account_id         = $account_info['acc_id'];
    					 $account_name       = $account_info['acc_name'];
    					 $balance_type ='DR.';      	
    		             $account_txn_table = $comp_id.'_accnttxnnn_'.$row['acc_id'].'_'.$ses_comp_fy_id;
    					 $builder = $this->db->table($account_txn_table); 
						 
    					 $builder->orderBy('acc_txn_id','DESC');
                         $builder->where('comp_id', $this->company_id);
						  // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
    					 $builder->where('acc_id', $row['acc_id']);                
    					 $subresult = $builder->get()->getResultArray();					
    				     if($subresult){		
    				         
    				         foreach($subresult as $sbrow){
    				         
    					       
    					       $acc_txn_drcr=$sbrow['acc_txn_drcr'];
    					       if($acc_txn_drcr=='c'){
    					         $credit=$credit+$sbrow['acc_txn_amount'];
    					       
    					       }
    					      else  if($acc_txn_drcr=='d'){
    					         $debit=$debit+$sbrow['acc_txn_amount'];
    					           }
    					           
    					      
    				            }
    				            if(isset($subresult[0])){
    					              $final_account_balance = $subresult[0]['acc_bal'];
    					              if($final_account_balance<0)
    					                 $balance_type ='CR.';
    					               else
    					                 $balance_type ='DR.'; 
    				            }
    					     else
    					              $final_account_balance =0;
    						 } 	
    					  else{
    					      // fetch opening balance if added or not 
    					        $account_balance_type    = $account_info['acc_op_drcr']; //cr/dr
    					        if($account_balance_type=='cr'){
    					           $final_account_balance='-'.$account_info['acc_op_bal'];;
    					           $credit=$credit+$account_info['acc_op_bal'];;
    					           $balance_type ='CR.';
    					          
    					        }
    					      else if($account_balance_type=='dr'){
    					           $final_account_balance = $account_info['acc_op_bal'];
    					           $debit =$debit+$account_info['acc_op_bal'];;
    					           $balance_type ='DR.';
    					        }
    					  	}
    				    
    				    
    				    if($final_account_balance<0)
    				    $show_group_balance = str_replace("-",'',$final_account_balance);
    				    else
    				    $show_group_balance  = $final_account_balance;
    				    
    				    $row["account_type"]        = "acc";
    				    $row["account_name"]        = $account_name;
    				    $row["account_id"]          = $account_id;
    				    $row['balance']             = $final_account_balance;
    				    $row['show_group_balance']  = $show_group_balance;
    				    $row['balance_type']        = $balance_type;
    				    $row['have_childs']         = 0;
    					$row['credit']          = $credit;
    				    $row['debit']           = $debit;
    				    $final_groups[]=$row;	
    			
    		        
    		       }
    		    }
	
	
	
	         //get billsundry accounts list 
           	 $billsundry_tbl = $comp_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
        	 $builder = $this->db->table($billsundry_tbl); 
        	 $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
        	 $builder->where('acc_grp_id', $group_id);
			 if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
        	 $billsndry_result = $builder->get()->getResultArray();
        
        	// echo $this->db->GetLastQuery();
        	// die();
	         if($billsndry_result){
    		    foreach($billsndry_result as $bls_row){
    		         $account_info       = $this->billsundry_info($bls_row['bill_sundry_id']);
    		         $blsndry_id         = $account_info['bill_sundry_id'];
    				 $account_name       = $account_info['bill_sundry_name'];
    				 $balance_type ='DR.';      	
    		         $credit=$debit=0;
    		         $final_account_balance = 0;
    		        $billsndry_txn_table  =  $comp_id.'_sundrytxnn_'.$bls_row['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
    		        $builder = $this->db->table($billsndry_txn_table);
    		        $builder->orderBy('sundry_txn_date','DESC');
        			$builder->orderBy('sundry_txn_id','DESC');         
        			$builder->where('comp_id', $comp_id);
        			$builder->where('bill_sundry_id', $bls_row['bill_sundry_id']);                
        			$txnnresult = $builder->get()->getResultArray();

        			if($txnnresult){
    				         foreach($txnnresult as $sbrow){
    					       $acc_txn_drcr=$sbrow['sundry_txn_drcr'];
    					       if($acc_txn_drcr=='c'){
    					         $credit=$credit+$sbrow['sundry_txn_amount'];
    					       
    					       }
    					      else  if($acc_txn_drcr=='d'){
    					         $debit=$debit+$sbrow['sundry_txn_amount'];
    					           }
    					           
    					      
    				            
    				            if(isset($sbrow)){
    					              $final_account_balance = $sbrow['sundry_bal'];
    					              if($final_account_balance<0)
    					                 $balance_type ='CR.';
    					               else
    					                 $balance_type ='DR.'; 
    				            }
    					     else
    					              $final_account_balance =0;
    						 } 
    						 }
    						 
    					if($final_account_balance<0)
    				    	$show_group_balance = str_replace("-",'',$final_account_balance);
    				    else
    				    	$show_group_balance  = $final_account_balance;
    				    
    				    $bls_row["account_type"]        = "bsd";
    				    $bls_row["account_name"]        = $account_name;
    				    $bls_row["account_id"]          = $blsndry_id;
    				    $bls_row['balance']             = $final_account_balance;
    				    $bls_row['show_group_balance']  = $show_group_balance;
    				    $bls_row['balance_type']        = $balance_type;
    				    $bls_row['have_childs']         = 0;
    					$bls_row['credit']          	= $credit;
    				    $bls_row['debit']           	= $debit;
    				    $final_groups[]=$bls_row;				 
        					 
    		    }
	 	    
	 	}
	            
	            
	
	  $final_list = array();
	  $d = array_map(null, $group_all_lists, $final_groups); 
	  foreach($d as $dkey => $dsubarray){
        foreach($dsubarray as $sbkey => $sbrow){
            $final_list[] = $sbrow;
           }
     }
  
   		return $final_list;
	}
	
	 function billsundry_info($billsundry_id){	 
       $billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	   
	   if($this->session->get('ses_boid')!='')		
	   return $this->db->table($billsundry_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('bill_sundry_id', $billsundry_id)->get()->getRowArray();   	   	
	   else	   
       return $this->db->table($billsundry_tbl)->where('bill_sundry_id', $billsundry_id)->get()->getRowArray();   	   
   }
   
   
     function get_group_all_accounts($group_id,$company_id){
	    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name','acc_op_bal','acc_grp_id'));
        $builder->where('comp_id', $company_id);		
		$builder->where('acc_grp_id', $group_id);
		$result = $builder->get()->getResultArray();
		$final_groups= array();
		if($result){
		    foreach($result as $row){
		             $account_txn_table = $this->company_id.'_accnttxnnn_'.$row['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
					 $builder = $this->db->table($account_txn_table); 
					 $builder->orderBy('acc_txn_id','DESC');
                     $builder->limit(1);					
					 $builder->where('comp_id', $this->company_id);
					  // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
					 $builder->where('acc_id', $row['acc_id']);                
					 $result = $builder->get()->getRowArray();					
				     if($result){		
					     $final_account_balance = $result['acc_bal'];
					     
						 } 	
					  else{
					      // fetch opening balance if added or not 
					      	$account_info       = $this->account_info($row['acc_id']);
					      	$account_balance    = $account_info['acc_op_bal'];
					        $account_balance_type    = $account_info['acc_op_drcr']; //cr/dr
					        if( $account_balance_type=='cr')
					           $final_account_balance='-'.$account_balance;
					        else
					          $final_account_balance = $account_balance;
					  
					     
				    	}
				    	
				     
				    $row['final_group_sum'] = $final_account_balance;
				    $final_groups[]=$row;	
					 }
		        
		    }
       
		
	    return $final_groups;
   }
    
     
     public function get_account_name($id){
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($account_master_tbl)->select('acc_name')->where('acc_id', $id)->get()->getRowArray();
        $acc_name = $data['acc_name'] ?? '';
        return $acc_name;
     }

     public function get_account_info($id){
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($account_master_tbl)->where('acc_id', $id)->get()->getRowArray();
        return $data;
     }
     
    public function add_account($data){	
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');  
        
        $table = $this->db->table($account_master_tbl)->where('LOWER(acc_name)', strtolower(trim($data['acc_name'])))
                                            	      ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['acc_name'])))
                                            	       ->get()->getRowArray(); 
                                            	  
        if($table){
            return ['status' => false, 'message' => 'Name must be unique'];
        }
        
        $table = $this->db->table($account_master_tbl)->where('LOWER(acc_name)', strtolower(trim($data['acc_name_alias'])))
                                            	      ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['acc_name_alias'])))
                                            	      ->get()->getRowArray();
                                            	   
        if($table){
            return ['status' => false, 'message' => 'Alias must be unique'];
        }
        
        
        $this->db->table($account_master_tbl)->insert($data);		 
        $account_id = $this->db->insertID();
        
        $groups = $this->get_sundry_groups();
        
        if(in_array($data['acc_grp_id'],$groups))
        {
            $this->createBillMaster($account_id);
        }
        
        return ['status' => true, 'account_id' => $account_id];
    
    }
    
    function get_sub_group_ids($array){
    	$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($tbl_name)
    					->select('acc_grp_id')
    					->whereIn('under_main_grp_id', $array)
    					->get()->getResultArray();

    	if($data){
    		foreach($data as $key => $value) {
    			array_push($array, $value['acc_grp_id']);
    		}
    	}
    	return $array;
    }
     
    function get_sundry_groups()
    {
        
		$groups = [16,22];
		$array = $this->get_sub_group_ids($groups);
        
        return $array;
    
    }
    
    function createBillMaster($id)
    {
		if($this->session->get('ses_boid')!='')
        $bo_id = $this->session->get('ses_boid');
	   else 
		 $bo_id=1;  

        $data = [
                'bills_ref_name' => 'UNDEFINED',
                'acc_id'         => $id,
                'bills_status'    => 'undefined',
                'bill_due_date'  => $this->financialYearBeginningDate(),
				'bo_id'          => $bo_id
            ];
        
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)->insert($data);
    }
    
    function checkBillMaster($id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		 $data = $this->db->table($bill_mst_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('acc_id', $id)->where('bills_ref_name', 'UNDEFINED')->get()->getRowArray();
	  else 
        $data = $this->db->table($bill_mst_tbl)->where('acc_id', $id)->where('bills_ref_name', 'UNDEFINED')->get()->getRowArray();
        if($data){
            return true;
        }
        return false;
    }
    
    function financialYearBeginningDate()
    {	
        $month = date('m');
        if($month > 4)
        {
            $y  = date('Y');
            // $fy_start = "01-04-".$y;
            $fy_start = $y."-04-01-";
        }
        else
        {
            $y = date('Y', strtotime('-1 year'));
            // $fy_start = "01-04-".$y;
            $fy_start = $y."-04-01-";
        }
        return $fy_start;
    }
       
    function getmax_txn_val($array){
        foreach($array as $sub_array) {
          foreach($sub_array as $element) {
              if($element > $max) {
              $max = $element;
               }
           }
        }
        
      return $max;  
    }    


    public function update_txn_entries($table_name, $acc_op_bal, $acc_op_drcr){
    $this->db->transStart();
	    $builder = $this->db->table($table_name); 
		$builder->orderBy('acc_txn_id');	
        $result =  $builder->get()->getResultArray();
		$balance =0;
		$counter=0;
		$first_balance=0;
        if($result){
            
            if($acc_op_drcr == 'dr'){
                $balance = $acc_op_bal;
            }
            else if($acc_op_drcr == 'cr'){
                $balance = -$acc_op_bal;
            }
            
		   foreach($result as $row){
			   $counter=$counter+1;
			     $sel_voucher_typer = $row['acc_txn_drcr'];
			      if(strtolower($sel_voucher_typer)=='c'){
					  $acc_txn_drcr_amount = $row['acc_txn_amount'];
					  $amount_type ='c';					  
					}
					else if(strtolower($sel_voucher_typer)=='d'){
					  $acc_txn_drcr_amount = $row['acc_txn_amount'];
					  $amount_type  = 'd';					 
					} 
				if($amount_type=='c')
				$balance -= $acc_txn_drcr_amount;
			   else if($amount_type=='d')
				$balance +=  $acc_txn_drcr_amount;
		   
          $this->db->table($table_name)->where('acc_txn_id',$row['acc_txn_id'])->where('comp_id',$row['comp_id'])->where('acc_id',$row['acc_id'])->update(array('acc_bal'=>$balance));		   
		  if ($this->db->transStatus() === true) {
				$this->db->transComplete();
		   }

		   }				
		}    
        
    }

    public function last_transaction($acc_id,$date)
    {
    	$account_txn_table =  $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($account_txn_table); 
	    $builder->where('acc_id', $acc_id);
		$builder->where("acc_txn_date <", $date);
        $builder->orderBy('acc_txn_date','DESC');
        $builder->orderBy('voucher_txn_id', 'desc');
		 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
	  	$builder->orderBy('acc_txn_id','DESC');
	  	$builder->limit(1);
		$result = $builder->get()->getRowArray();

		return $result;
    }
    
    
    public function account_opn_balance($acc_id,$start_date,$op_bal_qty){

    	$account_txn_table =  $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
		$comp_id = $this->company_id;

           if(strtotime($start_date)>strtotime(date('Y-m-d')))
             $month_start_date = date('Y-m');
           else
            $month_start_date = date('Y-m', strtotime($start_date. ' -1 months'));
       
            $builder = $this->db->table($account_txn_table); 
            $builder->limit(1);
            $builder->orderBy('acc_txn_date','DESC');
            $builder->orderBy('voucher_txn_id', 'desc');
    	  	$builder->orderBy('acc_txn_id','DESC');
    		$builder->where('comp_id', $comp_id);
			 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
    	    $builder->where('acc_id', $acc_id);
    		$builder->where("DATE_FORMAT(`acc_txn_date`, '%Y-%m')", $month_start_date);
    		$result = $builder->get()->getRowArray();
    		
    		if($result){
    		    $item_qty_bal = $result['acc_bal'];
    		}
    		else if($op_bal_qty)
    		  $item_qty_bal   =$op_bal_qty;
    		 else
    		  $item_qty_bal   ='0';
    	   return $item_qty_bal;
          
          
          /*
          die(); 
      
      	  $account_info =  $this->account_info($acc_id);
      	  
    	  $fy_months_list   =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id); 
      	   
      	   $start_date     = $fy_months_list['fy_begndt'];
     	   $end_date       = $fy_months_list['fy_end'];
    	   $months_array   = array();
    	   $current        = strtotime( $start_date );
           $last           = strtotime( $end_date );
    	   while( $current <= $last ) {
    		    $selsdate = date( 'Y-m', $current );
    		    $seledate = date( 'Y-m-t', $current );
                $months_array[$selsdate] = $seledate;
                $current = strtotime( '+1 month', $current );
              }
    	  $months_list = array();
    	  $all_lists   = array();
    	  $month_last_balance = array();
    	  $sum=0;
    	  $debit=0;
    	  $credit=0;
		
		if($account_info['acc_op_drcr']=='cr')
   	      $balance = '-'.$account_info['acc_op_bal'];
   	   else
   	     $balance = $account_info['acc_op_bal'];
   	 
		 foreach($months_array as $month_start_date => $month_end_date){
		     $monthly_acc_sum    =  array();
    	 	$inner_sum=0;
    	 	$debit = $credit=$credit_qty=$debit_qty=$debit_val=$credit_val=0;
			$builder            =  $this->db->table($account_txn_table); 
    	  	$builder->orderBy('acc_txn_id');         
	     	$builder->where('comp_id', $comp_id);
	    	$builder->where('acc_id', $acc_id);
		    $builder->where("DATE_FORMAT(`acc_txn_date`, '%Y-%m')", $month_start_date);
    		$result = $builder->get()->getResultArray();
	        
	    	$all_debit = $all_credit=0;
	    
	    	if($result){		
		         foreach($result as $values){	
        		       	$monthly_acc_sum[$month_start_date][$values['acc_txn_id']] = $values['acc_bal'];
                        
        			     if($values['acc_txn_drcr']=='d'){
        					 $debit  = $debit+$values['acc_txn_amount'];
        					 $balance = $balance + $values['acc_txn_amount'];
        				   }
        				 if($values['acc_txn_drcr']=='c'){
        				   $credit = $credit+$values['acc_txn_amount'];
        				   $balance = $balance - $values['acc_txn_amount'];
        				  }
        			 
        				$all_lists[$month_start_date]= array('account_id'=>$values['acc_id'],'debit'=>$debit,'credit'=>$credit,'end_date'=>$month_end_date,'balance'=>$balance);
			    
        			  
			      }
        	     $max_txnid = max(array_keys($monthly_acc_sum[$month_start_date]));
			    
			   
			   
		    } 
		    else{
		  
		      $balance_forward =$balance;
		    
		    
		        if($balance_forward=='')
		      $balance_forward=0;
		      $all_lists[$month_start_date]= array('account_id'=>$acc_id,'acc_bal'=>0,'end_date'=>$month_end_date,'debit'=>0,'credit'=>0,'balance'=>$balance_forward);   
		     
		        
		    }  
		   
		
	 }

     return $all_lists;
	 	*/
	}
    
    
   	function accounts_monthly_balance($acc_id,$comp_id,$fy_months_list,$account_tablename){
   	  
   	   $account_info =  $this->account_info($acc_id);
   	    $acc_opn_balance_info  = $this->acc_opn_balance_info($acc_id);
		if($acc_opn_balance_info)
		$account_opening_balance = $acc_opn_balance_info['acc_op_bal'];
	    else 
			$account_opening_balance = 0; 
		
		
   	   /* if($account_info['acc_op_drcr']=='cr')
   	      $account_opening_balance = '-'.$account_info['acc_op_bal'];
   	   else
   	     $account_opening_balance = $account_info['acc_op_bal']; */
   	   
	   $ses_comp_fy_id = $this->session->get('ses_comp_fy_id'); 
	   $start_date     = $fy_months_list['fy_begndt'];
 	   $end_date       = $fy_months_list['fy_end'];
	   $months_array   = array();
	   $current        = strtotime( $start_date );
       $last           = strtotime( $end_date );
	   while( $current <= $last ) {
		    $selsdate = date( 'Y-m', $current );
		    $seledate = date( 'Y-m-t', $current );
            $months_array[$selsdate] = $seledate;
            $current = strtotime( '+1 month', $current );
          }
	  $months_list = array();
	  $all_lists = array();
	  $month_last_balance = array();	$sum=0;
	  $debit=0;
		$credit=0;
		
	 foreach($months_array as $month_start_date => $month_end_date){
		$account_txn_table  =  $comp_id.'_accnttxnnn_'.$acc_id.'_'.$ses_comp_fy_id;	 
		$builder            = $this->db->table($account_txn_table); 
	    $monthly_acc_sum= array();
	 	$inner_sum=0;
	 	$debit = $credit=0;
		$builder->orderBy('acc_txn_id');         
		$builder->where('comp_id', $comp_id);
		if($this->session->get('ses_boid')!='')
        $builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('acc_id', $acc_id);
		$builder->where("DATE_FORMAT(`acc_txn_date`, '%Y-%m')", $month_start_date);
		$result = $builder->get()->getResultArray();
		$all_debit = $all_credit=0;
		if($result){		
		   foreach($result as $values){	
		       	$monthly_acc_sum[$month_start_date][$values['acc_txn_id']]=$values['acc_bal'];
                $posted_on                           = $values['posted_on'];
			     if($values['acc_txn_drcr']=='d'){
					 $debit  = $debit+$values['acc_txn_amount'];
					 $account_opening_balance = $account_opening_balance + $values['acc_txn_amount'];
				   }
				else  if($values['acc_txn_drcr']=='c'){
				   $credit = $credit+$values['acc_txn_amount'];
				   $account_opening_balance = $account_opening_balance - $values['acc_txn_amount'];
				  }
			    else
			    $account_opening_balance =0;

				$start_date = date('Y-m-01', strtotime($month_start_date));
				 $all_lists[$month_start_date]= array('account_id'=>$values['acc_id'],'debit'=>$debit,'credit'=>$credit,'start_date'=>$start_date,'end_date'=>$month_end_date,'balance'=>$account_opening_balance);
			     $sum = $values['acc_bal'];
			     }
        	     $max_txnid = max(array_keys($monthly_acc_sum[$month_start_date]));
			   //  $all_lists[$month_start_date]['balance']= $monthly_acc_sum[$month_start_date][$max_txnid]; 
			   
			   
		    } 
		else{
		      $balance_forward =$account_opening_balance;
		      
	        if($balance_forward=='')
		      $balance_forward=0;

		  	$start_date = date('Y-m-01', strtotime($month_start_date));
		    
		    $all_lists[$month_start_date]= array('account_id'=>$acc_id,'acc_bal'=>0,'start_date'=>$start_date,'end_date'=>$month_end_date,'debit'=>0,'credit'=>0,'balance'=>$balance_forward);   
		   }  
  
		
	 }	
	 
	 $show_list = array();			
			if($all_lists){
				foreach($all_lists as $month => $row){
					$s_al_debit=$s_al_credit=$s_al_balance=0;
				
						$s_al_debit = $row['debit'];
						$s_al_credit = $row['credit'];
						$s_al_balance = $row['balance'];
						
						
						
						$new_balance = str_replace("-",'',$s_al_balance);
                        if($s_al_balance<0){
                        	$balance_type = 'CR.';
                        	}
                        else
                           $balance_type = 'DR.';

			            $show_list[$month] = array(
			            	"start_date"=>date('d-m-Y', strtotime($row['start_date'])),
			            	"end_date"=>date('d-m-Y', strtotime($row['end_date'])),
			            	"account_id"=>$row['account_id'],
			            	"month_name"=>date('F',strtotime($month)),
			            	'debit'=>formatAmount($s_al_debit),
			            	'credit'=>formatAmount($s_al_credit),
			            	'debit_total'=>$s_al_debit,
			            	'credit_total'=>$s_al_credit,
			            	'balance'=>formatAmount($new_balance),
			            	'balance_type'=>$balance_type);				
					 
				}
			}
	return $show_list;
   }
   
   public function get_narration($vch_txn_id,$txn_id){
        $comp_id         = $this->company_id;
        $narration_table =  $comp_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($narration_table); 
		$builder->where('vch_txn_id', $vch_txn_id);
		$builder->where('txn_id', $txn_id);
		return $builder->get()->getRowArray();
   }
   function get_voucher_narration_info($voucher_txn_id,$narr_type,$txn_id){
	 if($narr_type=='long'){
        $long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($long_narr_tbl)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		
	 }
	else  if($narr_type=='short'){
        $short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
	}		
	  
  }
   
   
   function load_list_groups($limit,$pq_curPage){
   
           if(isset($_POST["pq_filter"])){
	       $pq_filter     = $_POST["pq_filter"];
	       
	       $filter_data  = json_decode($_POST["pq_filter"],true);
	      
	 
	      $pq_filters   = $filter_data['data'][0];
	     
	     if(isset($pq_filters['condition']))
	     $condition     = $pq_filters['condition'];  
	     else
	      $condition     = '';
	      $search_text = strtolower($pq_filters['value']);
	      $dataIndx    = $pq_filters['dataIndx'];  
	       
	    }
	    else{
	      $pq_filter     ='';
	      $search_text   ='';
	      $dataIndx      ='';
	      $condition     = '';
	    }
	    
       
      $main_types = $this->group_main_dropdown();
	  $account_grp_tbl =  $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
	  $builder         =  $this->db->table($account_grp_tbl); 
	   if($dataIndx=='group_name'){
	    
    	    if($condition=='begin'){
    	         $builder->Like('LOWER(acc_grp_name)',$search_text,'after');
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like('LOWER(acc_grp_name)',$search_text,'both');
    	       
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike('LOWER(acc_grp_name)',$search_text,'both');
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like('LOWER(acc_grp_name)',$search_text,'before');
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where('( LOWER(acc_grp_name)="'.$search_text.'")'   );
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where('( LOWER(acc_grp_name)="'.$search_text.'")'   );
    	     }   
	     
	     }
	     else if($dataIndx=='print_name'){
	     if($condition=='begin'){
    	         $builder->Like('LOWER(acc_grp_alias)',$search_text,'after');
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like('LOWER(acc_grp_alias)',$search_text,'both');
    	       
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike('LOWER(acc_grp_alias)',$search_text,'both');
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like('LOWER(acc_grp_alias)',$search_text,'before');
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where('( LOWER(acc_grp_alias)="'.$search_text.'")'   );
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where('( LOWER(acc_grp_alias)="'.$search_text.'")'   );
    	     } 
    	} 
	  $total_records   =  $builder->countAllResults();
	  
	
	  
	  if($pq_curPage=='0') {$pq_curPage=1;}
	   $offset = ($limit * ($pq_curPage - 1));

      if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            
	    $base_url = base_url().'/'.getenv('AdminPath');     
        
        $builder->orderBy('acc_grp_name');	
        $builder->limit($limit,$offset); 
        if($dataIndx=='group_name'){
	    
    	    if($condition=='begin'){
    	         $builder->Like('LOWER(acc_grp_name)',$search_text,'after');
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like('LOWER(acc_grp_name)',$search_text,'both');
    	       
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike('LOWER(acc_grp_name)',$search_text,'both');
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like('LOWER(acc_grp_name)',$search_text,'before');
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where('( LOWER(acc_grp_name)="'.$search_text.'")'   );
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where('( LOWER(acc_grp_name)="'.$search_text.'")'   );
    	     }   
	     
	     }
	     else if($dataIndx=='print_name'){
	     if($condition=='begin'){
    	         $builder->Like('LOWER(acc_grp_alias)',$search_text,'after');
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like('LOWER(acc_grp_alias)',$search_text,'both');
    	       
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike('LOWER(acc_grp_alias)',$search_text,'both');
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like('LOWER(acc_grp_alias)',$search_text,'before');
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where('( LOWER(acc_grp_alias)="'.$search_text.'")'   );
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where('( LOWER(acc_grp_alias)="'.$search_text.'")'   );
    	     } 
    	}
		$result  = $builder->get()->getResultArray();  
		$records = array();	
		
        foreach($result as $values){
          if(isset($main_types[$values['under_acc_grp_id']])){
			   $acc_grp_under = $main_types[$values['under_acc_grp_id']];			 			
			}			
		   else
			$acc_grp_under ='';				   
		
			$records[] = array(	
			          'chkbx' =>'<input type="checkbox" name="group_id[]" class="checkbox groups_row" dataid="'.$values['acc_grp_id'].'" value="'.$values['acc_grp_id'].'"> ',
                      'acc_grp_id'=> $values['acc_grp_id'],
					  'group_name'=> ucwords($values['acc_grp_name']),
					  'print_name'=> $values['acc_grp_alias'],
					  'primary'=> ucwords($values['acc_grp_primary']),
					  'under'=> $acc_grp_under		
				   );  
		    }	
 	  	return [
			'total_records'	=> $total_records,
			'data'	=> $records
		];
   }
   
    public function load_accounts_ledger_export($account_id, $start_date, $end_date)
	{
		$tbl_name =  $account_txn_table =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
		$comp_id = $this->company_id;

		$VoucherMasterShortNames = VoucherMasterShortNames();
		$ses_comp_fy_id          = $this->session->get('ses_comp_fy_id');

		$account_info       = $this->account_info($account_id);
		$account_name       =  $account_info['acc_name'];
		$account_txn_table  =  $comp_id.'_accnttxnnn_'.$account_info['acc_id'].'_'.$ses_comp_fy_id;	
		
		
		$show_account_name  =  $account_info['acc_name'];		
				
		$final_result= array();

		$builder = $this->db->table($account_txn_table); 
		$builder->where('comp_id', $comp_id);					
		$builder->where('acc_id', $account_id);
		if($start_date!='' && $end_date!=''){
			$builder->where('acc_txn_date >=', $start_date);
			$builder->where('acc_txn_date <=', $end_date);
		}
		 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->orderBy('acc_txn_date', 'asc');
		$builder->orderBy('acc_txn_id', 'asc');
		$fresult = $builder->get()->getResultArray();
		//echo $this->db->GetLastQuery();

	   if($fresult){
		foreach($fresult as $frow){	

			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    	$builder = $this->db->table($comp_txn_tbl);
	    	$builder->where('voucher_txn_id', $frow['voucher_txn_id']);
	    	$builder->where('master_id_type', 'acc');
	    	$builder->where('master_id !=', $account_id);
	    	$builder->orderBy('txn_id', 'asc');
	    	$builder->limit(1);
	    	$comp_txns = $builder->get()->getRowArray();

	    	if($comp_txns){
	    		$account_info       = $this->account_info($comp_txns['master_id']);
				$account_name       =  $account_info['acc_name'];
	    	}


            $account_bal  = $frow['acc_bal'];					
			$voucher_type = $VoucherMasterShortNames[$frow['voucher_type_id']];
			if($frow['acc_txn_drcr']=='d'){
				$debit=$frow['acc_txn_amount'];
				$credit=0;
			}
			if($frow['acc_txn_drcr']=='c'){
				$debit=0;
				$credit=$frow['acc_txn_amount'];
			}						
			
				$new_balance = str_replace("-",'',$account_bal);
			$balance_type = '';	
			if($account_bal<0){
			  $balance_type = 'CR.';
			}
			if($account_bal>0){
			 $balance_type = 'DR.';
			 }
			$txn_id            = $frow['txn_id'];
			$voucher_date = '--DELETED--';
			$voucher_no = '--DELETED--';
			$acc_id = $account_id;
			
			
			// $narration = $this->get_narration($frow['voucher_txn_id'],$txn_id);
			// if($narration){$short_narration=$narration['vch_short_narr'];}else{$short_narration='';}

			$narration = $this->get_voucher_narration_info($frow['voucher_txn_id'],'long',0);
			if($narration){$short_narration=$narration['vch_narr'];}else{$short_narration='';}
			
			$voucher_cons_info = $this->get_voucher_info($frow['voucher_txn_id'],$comp_id);
			if($voucher_cons_info){
				$voucher_date      = date("d-m-Y", strtotime($voucher_cons_info['voucher_date']));
				$voucher_no        = $voucher_cons_info['comp_vch_no'];
				$acc_id = 0;
				
				$comp_vch_series_id  = $voucher_cons_info['comp_vch_series_id'];
			}
			
		
			$final_result[]    = array(
				'acc_id'		=> $acc_id,
				'voucher_no'		=> $voucher_no,
				'voucher_txn_id'	=> $frow['voucher_txn_id'],
				'comp_vch_series_id' => $comp_vch_series_id,
				'voucher_type_id'	=> $frow['voucher_type_id'],
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $voucher_type,
				'account_name'		=> $account_name,
				'credit'			=> !empty($credit) ? formatAmount($credit) : '',
				'debit'				=> !empty($debit) ? formatAmount($debit) : '',
				'balance'			=> !empty(abs($account_bal)) ? formatAmount(abs($account_bal)) : '',
				'balance_type'		=> $balance_type,
				'balance_total'		=> $account_bal,
				'credit_total'		=> $credit,
				'debit_total'		=> $debit,
				'short_narration'   => $short_narration
			);
		  }
	   }
	
		// return $final_result;
		return  $final_result;
	}
	
   
   	public function load_accounts_ledger($account_id, $limit, $start_date, $end_date,$pq_curPage, $search)
	{
		$tbl_name =  $account_txn_table =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
		$comp_id = $this->company_id;

		$VoucherMasterShortNames = VoucherMasterShortNames();
		$ses_comp_fy_id          = $this->session->get('ses_comp_fy_id');

		$account_info       = $this->account_info($account_id);
		$account_name       =  $account_info['acc_name'];
		$account_txn_table  =  $comp_id.'_accnttxnnn_'.$account_info['acc_id'].'_'.$ses_comp_fy_id;	
		
		
		$show_account_name  =  $account_info['acc_name'];		
				
		$final_result= array();

		$builder = $this->db->table($account_txn_table); 
		$builder->where('comp_id', $comp_id);					
		$builder->where('acc_id', $account_id);
		if($start_date!='' && $end_date!=''){
			$builder->where('acc_txn_date >=', $start_date);
			$builder->where('acc_txn_date <=', $end_date);
		}
		if($search != ''){
			$builder->groupStart();
			$builder->like('acc_txn_amount', $search);
			$builder->groupEnd();
		}
		$builder->orderBy('acc_txn_date', 'asc');
		$builder->orderBy('acc_txn_id', 'asc');
		$total_records = $builder->countAllResults();
        
        if($pq_curPage==0){$pq_curPage=1;}
        
       	$offset = ($limit * ($pq_curPage - 1));

     	if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
 
			
		$builder = $this->db->table($account_txn_table); 
		$builder->where('comp_id', $comp_id);					
		$builder->where('acc_id', $account_id);
		if($start_date!='' && $end_date!=''){
			$builder->where('acc_txn_date >=', $start_date);
			$builder->where('acc_txn_date <=', $end_date);
		}
		if($search != ''){
			$builder->groupStart();
			$builder->like('acc_txn_amount', $search);
			$builder->groupEnd();
		}
		$builder->limit($limit,$offset); 
		$builder->orderBy('acc_txn_date', 'asc');
		$builder->orderBy('acc_txn_id', 'asc');
		$fresult = $builder->get()->getResultArray();
		//echo $this->db->GetLastQuery();

	   if($fresult){
		foreach($fresult as $frow){	

			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    	$builder = $this->db->table($comp_txn_tbl);
	    	$builder->where('voucher_txn_id', $frow['voucher_txn_id']);
	    	$builder->where('master_id_type', 'acc');
	    	$builder->where('master_id !=', $account_id);
	    	$builder->orderBy('txn_id', 'asc');
	    	$builder->limit(1);
	    	$comp_txns = $builder->get()->getRowArray();

	    	if($comp_txns){
	    		$account_info       = $this->account_info($comp_txns['master_id']);
				$account_name       =  $account_info['acc_name'];
	    	}


            $account_bal  = $frow['acc_bal'];					
			$voucher_type = $VoucherMasterShortNames[$frow['voucher_type_id']];
			if($frow['acc_txn_drcr']=='d'){
				$debit=$frow['acc_txn_amount'];
				$credit=0;
			}
			if($frow['acc_txn_drcr']=='c'){
				$debit=0;
				$credit=$frow['acc_txn_amount'];
			}						
			
				$new_balance = str_replace("-",'',$account_bal);
			$balance_type = '';	
			if($account_bal<0){
			  $balance_type = 'CR.';
			}
			if($account_bal>0){
			 $balance_type = 'DR.';
			 }
			$txn_id            = $frow['txn_id'];
			$voucher_date = '--DELETED--';
			$voucher_no = '--DELETED--';
			$acc_id = $account_id;
			
			
			// $narration = $this->get_narration($frow['voucher_txn_id'],$txn_id);
			// if($narration){$short_narration=$narration['vch_short_narr'];}else{$short_narration='';}

			$narration = $this->get_voucher_narration_info($frow['voucher_txn_id'],'long',0);
			if($narration){$short_narration=$narration['vch_narr'];}else{$short_narration='';}
			
			$voucher_cons_info = $this->get_voucher_info($frow['voucher_txn_id'],$comp_id);
			if($voucher_cons_info){
				$voucher_date      = date("d-m-Y", strtotime($voucher_cons_info['voucher_date']));
				$voucher_no        = $voucher_cons_info['comp_vch_no'];
				$acc_id = 0;
				
				$comp_vch_series_id  = $voucher_cons_info['comp_vch_series_id'];
			}
			
		
			$final_result[]    = array(
				"chkbx" => "<label><input name='voucher_ids[]' class='checkbox hidden accounts_row voucher_row' data-id='".$frow['voucher_type_id'].'||'.$frow['voucher_txn_id']."' value='".$frow['voucher_type_id'].'||'.$frow['voucher_txn_id']."' type='checkbox' /></label>",
				'acc_id'		=> $acc_id,
				'voucher_no'		=> $voucher_no,
				'voucher_txn_id'	=> $frow['voucher_txn_id'],
				'comp_vch_series_id' => $comp_vch_series_id,
				'voucher_type_id'	=> $frow['voucher_type_id'],
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $voucher_type,
				'account_name'		=> $account_name,
				'credit'			=> !empty($credit) ? formatAmount($credit) : '',
				'debit'				=> !empty($debit) ? formatAmount($debit) : '',
				'balance'			=> !empty(abs($account_bal)) ? formatAmount(abs($account_bal)) : '',
				'balance_type'		=> $balance_type,
				'credit_total'		=> $credit,
				'debit_total'		=> $debit,
				'short_narration'   => $short_narration
			);
		  }
	   }
	
		// return $final_result;
		return [
			'total_records'	=> $total_records,
			'data'	=> $final_result
		];
	}

	public function load_accounts_ledger_condensed($account_id, $limit, $from_date, $to_date,$pq_curPage, $search)
	{
		$color_array = ['#f9f9f9','#ffffff'];
		$color_index = 0;

		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
		$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
		$long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
	    
		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl.' ctm1', 'ctm1.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm1.master_id_type = "acc"');
	    $builder->select('ctm1.master_id, ctm1.master_id_type');
	    $builder->join($acc_txn_tbl, $acc_txn_tbl.'.txn_id  = ctm1.txn_id');		
	    $builder->select('acc_id, acc_txn_date, acc_txn_drcr, acc_txn_amount, acc_bal');
	    $builder->join($comp_txn_tbl.' ctm2', 'ctm2.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.master_id_type = "nrr"', 'left');
	    $builder->join($long_narr_tbl, $long_narr_tbl.'.vch_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.txn_id = '.$long_narr_tbl.'.txn_id', 'left');
	    $builder->select('vch_narr');		
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);
      if($this->session->get('ses_boid')!='')
		$builder->where($acc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
	  if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
	
	
		if($search != ''){
			$builder->groupStart();
			$builder->like('acc_txn_date', $search);
			$builder->orLike('acc_txn_amount', $search);
			$builder->orLike('acc_bal', $search);
			$builder->orLike('comp_vch_type', $search);
			$builder->orLike('comp_vch_no', $search);
			$builder->orLike('vch_narr', $search);
			$builder->groupEnd();
		}

		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('acc_txn_id');
		$total_records = $builder->countAllResults();

		if($pq_curPage==0){$pq_curPage=1;}
       	$offset = ($limit * ($pq_curPage - 1));
     	if ($offset > $total_records){        
            $pq_curPage = ceil($total_records / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl.' ctm1', 'ctm1.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm1.master_id_type = "acc"');
	    $builder->select('ctm1.master_id, ctm1.master_id_type');
	    $builder->join($acc_txn_tbl, $acc_txn_tbl.'.txn_id  = ctm1.txn_id');		
	    $builder->select('acc_id, acc_txn_date, acc_txn_drcr, acc_txn_amount, acc_bal');
	    $builder->join($comp_txn_tbl.' ctm2', 'ctm2.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.master_id_type = "nrr"', 'left');
	    $builder->join($long_narr_tbl, $long_narr_tbl.'.vch_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND ctm2.txn_id = '.$long_narr_tbl.'.txn_id', 'left');
	    $builder->select('vch_narr');	
		$builder->where('acc_id', $account_id);				
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);
       if($this->session->get('ses_boid')!='')
		 $builder->where($acc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
	   if($this->session->get('ses_boid')!='')
		 $builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		if($search != ''){
			$builder->groupStart();
			$builder->like('acc_txn_date', $search);
			$builder->orLike('acc_txn_amount', $search);
			$builder->orLike('acc_bal', $search);
			$builder->orLike('comp_vch_type', $search);
			$builder->orLike('comp_vch_no', $search);
			$builder->orLike('vch_narr', $search);
			$builder->groupEnd();
		}
		
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('acc_txn_id');

		$builder->limit($limit,$offset); 
		$result = $builder->get()->getResultArray();

		// echo "<pre>";print_r($result);exit;
		$final_result = [];
	   if($result){
		foreach($result as $key => $value){

			$voucher_type_id = $value['voucher_type_id'];
			$voucher_type = $value['comp_vch_type'];
			$voucher_no = $value['comp_vch_no'];
			$voucher_date      = date("d-m-Y", strtotime($value['voucher_date']));
			$account_name = '';
			$short_narration = '';

			$debit = '';
	    	$credit = '';
	    	$credit_total = 0;
	    	$debit_total = 0;

	    	$account_bal  = '';
	    	$balance_type = '';

	    	if($value['acc_txn_drcr']=='d'){
				$debit = formatAmount($value['acc_txn_amount']);
				$debit_total = $value['acc_txn_amount'];
			}
			if($value['acc_txn_drcr']=='c'){
				$credit = formatAmount($value['acc_txn_amount']);
				$credit_total = $value['acc_txn_amount'];
			}

            					
			
			$balance_type = '';	
			if($value['acc_bal'] < 0){
				$account_bal = formatAmount(abs($value['acc_bal']));
			  	$balance_type = 'CR.';
			}
			if($value['acc_bal'] >= 0){
				$account_bal = formatAmount($value['acc_bal']);
			 	$balance_type = 'DR.';
			}

			$builder = $this->db->table($comp_txn_tbl);
	    	$builder->where('voucher_txn_id', $value['voucher_txn_id']);
	    	$builder->where('master_id_type', 'acc');
	    	$builder->where('master_id !=', $account_id);
	    	$builder->orderBy('txn_id', 'asc');
	    	$builder->limit(1);
	    	$comp_txns = $builder->get()->getRowArray();

	    	if($comp_txns){
	    		$account_info = $this->account_info($comp_txns['master_id']);
				$account_name =  $account_info['acc_name'] ?? '';
	    	}

			$final_result[]    = array(
				"chkbx" => "<label><input name='voucher_ids[]' class='checkbox hidden accounts_row voucher_row' data-id='".$value['voucher_type_id'].'||'.$value['voucher_txn_id']."' value='".$value['voucher_type_id'].'||'.$value['voucher_txn_id']."' type='checkbox' /></label>",

				'voucher_no'		=> $value['comp_vch_no'],
				'voucher_txn_id'	=> $value['voucher_txn_id'],
				'comp_vch_series_id' => $value['comp_vch_series_id'],
				'voucher_type_id'	=> $value['voucher_type_id'],
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $value['comp_vch_type'],
				'account_name'		=> $account_name,
				'credit'			=> $credit,
				'debit'				=> $debit,
				'balance'			=> $account_bal,
				'balance_type'		=> $balance_type,
				'credit_total'		=> $credit_total,
				'debit_total'		=> $debit_total,
				'short_narration'   => $value['vch_narr'],

			);
		  }
	   }
	
		// return $final_result;
		return [
			'total_records'	=> $total_records,
			'data'	=> $final_result
		];
	}    

	public function load_accounts_ledger_detailed($account_id, $limit, $from_date, $to_date,$pq_curPage, $search)
	{
		$color_array = ['#f9f9f9','#ffffff'];
		$color_index = 0;

		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
		$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    
		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($comp_txn_tbl.'.master_id, '.$comp_txn_tbl.'.master_id_type');
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
			$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` = '.$account_id.' AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);

		if($search != ''){
			$builder->groupStart();
			$builder->like('voucher_date', $search);
			$builder->orLike('comp_vch_type', $search);
			$builder->orLike('comp_vch_no', $search);
			$builder->groupEnd();
		}

		$builder->whereIn($voucher_tbl.'.voucher_type_id', [2,3,5,9,11,13,18]);
		$builder->where('voucher_tag !=', 'OPTIONL');
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('master_id_type');
		$total_records = $builder->countAllResults();

		if($pq_curPage==0){$pq_curPage=1;}
       	$offset = ($limit * ($pq_curPage - 1));
     	if ($offset > $total_records){        
            $pq_curPage = ceil($total_records / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($comp_txn_tbl.'.master_id, '.$comp_txn_tbl.'.master_id_type, '.$comp_txn_tbl.'.txn_id');
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
			$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` = '.$account_id.' AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);

		if($search != ''){
			$builder->groupStart();
			$builder->like('voucher_date', $search);
			$builder->orLike('comp_vch_type', $search);
			$builder->orLike('comp_vch_no', $search);
			$builder->groupEnd();
		}

		$builder->whereIn($voucher_tbl.'.voucher_type_id', [2,3,5,9,11,13,18]);
		$builder->where('voucher_tag !=', 'OPTIONL');
		
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('master_id_type');
		// $builder->orderBy('FIELD(master_id, "'.$account_id.'")');
		$builder->limit($limit,$offset); 
		$result = $builder->get()->getResultArray();

		// echo $this->db->GetLastQuery();
		//echo "<pre>";print_r($result);exit;
		$final_result = [];
	   if($result){
		foreach($result as $key => $value){

			$voucher_type_id = $value['voucher_type_id'];
			$voucher_type = $value['comp_vch_type'];
			$voucher_no = $value['comp_vch_no'];
			$voucher_date      = date("d-m-Y", strtotime($value['voucher_date']));
			$account_name = '';
			$short_narration = '';

			$debit = '';
	    	$credit = '';
	    	$credit_total = 0;
	    	$debit_total = 0;

	    	$account_bal  = '';
	    	$balance_type = '';
	    	$style = '';

			if($value['master_id_type'] == 'acc')
			{
				$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('txn_id', $value['txn_id']);
		    	$builder->where('acc_id', $value['master_id']);
				 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$acc_txns = $builder->get()->getRowArray();
		    	
		    	if($acc_txns){
		    		$account_info       = $this->account_info($acc_txns['acc_id']);
					$account_name       =  $account_info['acc_name'];

					if($acc_txns['acc_txn_drcr']=='d'){
						$debit = formatAmount($acc_txns['acc_txn_amount']);
						$debit_total = $acc_txns['acc_txn_amount'];
					}
					if($acc_txns['acc_txn_drcr']=='c'){
						$credit = formatAmount($acc_txns['acc_txn_amount']);
						$credit_total = $acc_txns['acc_txn_amount'];
					}

		            					
					
					$balance_type = '';	
					if($acc_txns['acc_bal'] < 0){
						$account_bal = formatAmount(abs($acc_txns['acc_bal']));
					  	$balance_type = 'CR.';
					}
					if($acc_txns['acc_bal'] >= 0){
						$account_bal = formatAmount($acc_txns['acc_bal']);
					 	$balance_type = 'DR.';
					}

					$narration = $this->get_voucher_narration_info($value['voucher_txn_id'],'short',$acc_txns['txn_id']);
					if($narration){$short_narration=$narration['vch_short_narr'];}

					if($acc_txns['acc_id'] == $account_id){
						$style = 'font-weight:500;';
					}
					else{
						$account_bal = '';
					 	$balance_type = '';
					}
		    	}
			}

			if($value['master_id_type'] == 'bsd')
			{
				$table = $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('txn_id', $value['txn_id']);
				$account = $builder->get()->getRowArray();
				if($account){
	                if($account['sundry_txn_drcr'] == 'c'){
	                 	$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name   	= $bill_sundry_info['bill_sundry_name'];

	                    $credit = formatAmount($account['sundry_txn_amount']);
						$credit_total = $account['sundry_txn_amount'];
	                 }
	                 if($account['sundry_txn_drcr'] == 'd'){
	                 	$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name   	= $bill_sundry_info['bill_sundry_name'];

	                    $debit = formatAmount($account['sundry_txn_amount']);
						$debit_total = $account['sundry_txn_amount'];
	              	}

	              	$balance_type = '';	
					if($account['sundry_bal'] < 0){
						$account_bal = formatAmount(abs($account['sundry_bal']));
					  	$balance_type = 'CR.';
					}
					if($account['sundry_bal'] >= 0){
						$account_bal = formatAmount($account['sundry_bal']);
					 	$balance_type = 'DR.';
					}

              	}
			}

			if($value['master_id_type'] == 'itm')
			{
				$table = $this->company_id.'_itemtxnnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where($table.'.txn_id', $value['txn_id']);
    			$builder->where('item_id', $value['master_id']);
				// branch check 
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$item_txn = $builder->get()->getRowArray();

                if($item_txn){
                	$item_name = '';
                	$item_unit = '';

                	$item_info = $this->get_item_name($value['master_id']);
                	if($item_info){
                		$item_name = $item_info['item_name'];

                		$itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                		
						// branch check 
				if($this->session->get('ses_boid')!=''){
				$itemtxnbal = $this->db->table($itemtxnbal_tbl)
									->where('itemtxnbal_id', $item_txn['itemtxnbal_id'])
									->where('bo_id', $this->session->get('ses_boid'))
									->get()->getRowArray();
				}else{
					
					$itemtxnbal = $this->db->table($itemtxnbal_tbl)
									->where('itemtxnbal_id', $item_txn['itemtxnbal_id'])
									->get()->getRowArray();
				}
						
						
						
						if($itemtxnbal){
							$item_unit_id = $itemtxnbal['item_unit'];
							$item_unit_info = $this->get_item_units_info($item_unit_id);
							if($item_unit_info){
								$item_unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
							}
						}
                	}
                	$item_price = 	formatAmount(parseAmount($item_txn['item_txn_amount'])/parseAmount($item_txn['item_txn_qty']));
                	$item_amount = formatAmount($item_txn['item_txn_amount']);

                	$account_name = $item_name.'&nbsp;&nbsp;&nbsp;'.$item_txn['item_txn_qty'].' '.$item_unit.' @ '.$item_price.' = '.$item_amount;

                	$short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
					$narration = $this->db->table($short_narr_tbl)
											->where('vch_txn_id',$value['voucher_txn_id'])
											->where('txn_id', $value['txn_id'])
											->get()->getRowArray();
					if($narration){
						$short_narration = $narration['vch_short_narr'];
					}

                	$style = 'font-weight:250;';
                }
			}

			if($value['master_id_type'] == 'nrr')
			{
				$long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
				$narration = $this->db->table($long_narr_tbl)->where('vch_txn_id',$value['voucher_txn_id'])->get()->getRowArray();
				if($narration){
					$short_narration = $narration['vch_narr'];
				}

				$style = 'font-weight:250;';
			}

			if(isset($result[$key-1]) && $result[$key-1]['voucher_txn_id'] == $value['voucher_txn_id']){
				$voucher_type = '';
				$voucher_no = '';
				$voucher_date = '';
			}

			if(isset($result[$key-1]) && $result[$key-1]['voucher_txn_id'] != $value['voucher_txn_id'])
				$color_index = !$color_index;
			$style .= 'background-color: '.$color_array[$color_index].';';


			$final_result[]    = array(
				"chkbx" => "<label><input name='voucher_ids[]' class='checkbox hidden accounts_row voucher_row' data-id='".$voucher_type_id.'||'.$value['voucher_txn_id']."' value='".$voucher_type_id.'||'.$value['voucher_txn_id']."' type='checkbox' /></label>",

				'voucher_no'		=> $voucher_no,
				'voucher_txn_id'	=> $value['voucher_txn_id'],
				'comp_vch_series_id' => $value['comp_vch_series_id'],
				'voucher_type_id'	=> $voucher_type_id,
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $voucher_type,
				'account_name'		=> $account_name,
				'credit'			=> $credit,
				'debit'				=> $debit,
				'balance'			=> $account_bal,
				'balance_type'		=> $balance_type,
				'credit_total'		=> $credit_total,
				'debit_total'		=> $debit_total,
				'short_narration'   => $short_narration,
				'pq_rowattr'	    => ['style' => $style],

			);
		  }
	   }
	
		// return $final_result;
		return [
			'total_records'	=> $total_records,
			'data'	=> $final_result
		];
	}

	public function load_accounts_ledger_detailed_export($account_id, $from_date, $to_date)
	{
		$color_array = ['#f9f9f9','#ffffff'];
		$color_index = 0;

		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
		$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($comp_txn_tbl.'.master_id, '.$comp_txn_tbl.'.master_id_type, '.$comp_txn_tbl.'.txn_id');
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` = '.$account_id.' AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);
		$builder->whereIn($voucher_tbl.'.voucher_type_id', [2,3,5,9,11,13,18]);
		$builder->where('voucher_tag !=', 'OPTIONL');
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->orderBy('master_id_type');
		$result = $builder->get()->getResultArray();

		$final_result = [];
	   if($result){
		foreach($result as $key => $value){

			$voucher_type_id = $value['voucher_type_id'];
			$voucher_type = $value['comp_vch_type'];
			$voucher_no = $value['comp_vch_no'];
			$voucher_date      = date("d-m-Y", strtotime($value['voucher_date']));
			$account_name = '';
			$short_narration = '';

			$debit = '';
	    	$credit = '';
	    	$credit_total = 0;
	    	$debit_total = 0;

	    	$account_bal  = '';
	    	$balance_type = '';
	    	$style = '';

			if($value['master_id_type'] == 'acc')
			{
				$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('txn_id', $value['txn_id']);
		    	$builder->where('acc_id', $value['master_id']);
				 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$acc_txns = $builder->get()->getRowArray();
		    	
		    	if($acc_txns){
		    		$account_info       = $this->account_info($acc_txns['acc_id']);
					$account_name       =  $account_info['acc_name'];

					if($acc_txns['acc_txn_drcr']=='d'){
						$debit = formatAmount($acc_txns['acc_txn_amount']);
						$debit_total = $acc_txns['acc_txn_amount'];
					}
					if($acc_txns['acc_txn_drcr']=='c'){
						$credit = formatAmount($acc_txns['acc_txn_amount']);
						$credit_total = $acc_txns['acc_txn_amount'];
					}	
					
					$balance_type = '';	
					if($acc_txns['acc_bal'] < 0){
						$account_bal = (abs($acc_txns['acc_bal']));
					  	$balance_type = 'CR.';
					}
					if($acc_txns['acc_bal'] >= 0){
						$account_bal = ($acc_txns['acc_bal']);
					 	$balance_type = 'DR.';
					}

					$narration = $this->get_voucher_narration_info($value['voucher_txn_id'],'short',$acc_txns['txn_id']);
					if($narration){$short_narration=$narration['vch_short_narr'];}

					if($acc_txns['acc_id'] == $account_id){
						
					}
					else{
						$account_bal = '';
					 	$balance_type = '';
					}
		    	}
			}

			if($value['master_id_type'] == 'bsd')
			{
				$table = $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('txn_id', $value['txn_id']);
				$account = $builder->get()->getRowArray();
				if($account){
	                if($account['sundry_txn_drcr'] == 'c'){
	                 	$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name   	= $bill_sundry_info['bill_sundry_name'];

	                    $credit = formatAmount($account['sundry_txn_amount']);
						$credit_total = $account['sundry_txn_amount'];
	                 }
	                 if($account['sundry_txn_drcr'] == 'd'){
	                 	$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name   	= $bill_sundry_info['bill_sundry_name'];

	                    $debit = formatAmount($account['sundry_txn_amount']);
						$debit_total = $account['sundry_txn_amount'];
	              	}

	              	$balance_type = '';	
					if($account['sundry_bal'] < 0){
						$account_bal = formatAmount(abs($account['sundry_bal']));
					  	$balance_type = 'CR.';
					}
					if($account['sundry_bal'] >= 0){
						$account_bal = formatAmount($account['sundry_bal']);
					 	$balance_type = 'DR.';
					}

              	}
			}

			if($value['master_id_type'] == 'itm')
			{
				$table = $this->company_id.'_itemtxnnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where($table.'.txn_id', $value['txn_id']);
    			$builder->where('item_id', $value['master_id']);
                $item_txn = $builder->get()->getRowArray();

                if($item_txn){
                	$item_name = '';
                	$item_unit = '';

                	$item_info = $this->get_item_name($value['master_id']);
                	if($item_info){
                		$item_name = $item_info['item_name'];

                		$itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                		$itemtxnbal = $this->db->table($itemtxnbal_tbl)
									->where('itemtxnbal_id', $item_txn['itemtxnbal_id'])
									->get()->getRowArray();
						if($itemtxnbal){
							$item_unit_id = $itemtxnbal['item_unit'];
							$item_unit_info = $this->get_item_units_info($item_unit_id);
							if($item_unit_info){
								$item_unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
							}
						}
                	}
                	$item_txn['item_txn_qty'] = $item_txn['item_txn_qty'] > 0 ? $item_txn['item_txn_qty'] : 1;

                	$item_price = 	formatAmount(parseAmount($item_txn['item_txn_amount'])/parseAmount($item_txn['item_txn_qty']));
                	$item_amount = formatAmount($item_txn['item_txn_amount']);

                	$account_name = $item_name.'&nbsp;&nbsp;&nbsp;'.$item_txn['item_txn_qty'].' '.$item_unit.' @ '.$item_price.' = '.$item_amount;

                	$short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
					$narration = $this->db->table($short_narr_tbl)
											->where('vch_txn_id',$value['voucher_txn_id'])
											->where('txn_id', $value['txn_id'])
											->get()->getRowArray();
					if($narration){
						$short_narration = $narration['vch_short_narr'];
					}
                }
			}

			if($value['master_id_type'] == 'nrr')
			{
				$long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
				$narration = $this->db->table($long_narr_tbl)->where('vch_txn_id',$value['voucher_txn_id'])->get()->getRowArray();
				if($narration){
					$short_narration = $narration['vch_narr'];
				}
			}

			if(isset($result[$key-1]) && $result[$key-1]['voucher_txn_id'] == $value['voucher_txn_id']){
				$voucher_type = '';
				$voucher_no = '';
				$voucher_date = '';
			}


			$final_result[]    = array(
				'voucher_no'		=> $voucher_no,
				'voucher_txn_id'	=> $value['voucher_txn_id'],
				'comp_vch_series_id' => $value['comp_vch_series_id'],
				'voucher_type_id'	=> $voucher_type_id,
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $voucher_type,
				'account_name'		=> $account_name,
				'credit'			=> $credit,
				'debit'				=> $debit,
				'balance'			=> $account_bal,
				'balance_type'		=> $balance_type,
				'credit_total'		=> $credit_total,
				'debit_total'		=> $debit_total,
				'short_narration'   => $short_narration,

			);
		  }
	   }
	
		return $final_result;
	}

	public function get_item_name($id){
         $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
         return $this->db->table($item_master_tbl)->select('item_name')->where('item_id', $id)->get()->getRowArray();
    }

    public function get_item_units_info($unit_id){	 
	
	 	$item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	  	return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->get()->getRowArray();   	   
    }
    function bill_sundry_info($id)
    {
    	$bill_sundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	    $result = $this->db->table($bill_sundry_tbl)->where('bill_sundry_id', $id)->get()->getRowArray();
	    return $result; 
    }
   
   public function load_accounts_memo_ledger_export($account_id, $from_date, $to_date)
	{
		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $account_memo_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');

		$voucher_type_id = 8;
		$comp_vch_type = '';

		$builder = $this->db->table($voucher_type_tbl);
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->where('voucher_type_id', $voucher_type_id);
	    $voucher_type = $builder->get()->getRowArray();
	    if($voucher_type){
	    	$comp_vch_type = $voucher_type['comp_vch_type'];
	    }

		$tbl_name =  $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');

		$builder = $this->db->table($account_memo_tbl);
	    $builder->select($account_memo_tbl.'.acc_type, '.$account_memo_tbl.'.acc_id, '. $account_memo_tbl.'.acc_txn_drcr, '. $account_memo_tbl.'.acc_bal, '. $account_memo_tbl.'.voucher_txn_id, '. $account_memo_tbl.'.acc_txn_amount');
	    
		$builder->where($account_memo_tbl.'.comp_id', $this->company_id);					
		$builder->where('acc_txn_date >=', $from_date);
		$builder->where('acc_txn_date <=', $to_date);
		$builder->where('acc_type', 'acc');
		$builder->where('acc_id', $account_id);
		$builder->orderBy('acc_txn_date');
		$builder->orderBy('acc_txn_id');		
		$result = $builder->get()->getResultArray();
		$final_result=array();
	   if($result){
		foreach($result as $value){	

	    	$builder = $this->db->table($account_memo_tbl);
	    	$builder->where('voucher_txn_id', $value['voucher_txn_id']);
	    	$builder->where('acc_type', 'acc');
	    	$builder->where('acc_id !=', $value['acc_id']);
	    	$builder->orderBy('acc_txn_id', 'asc');
	    	$builder->limit(1);
	    	$comp_txns = $builder->get()->getRowArray();

	    	$account_name = '';
	    	if($comp_txns){
	    		$account_info       = $this->account_info($comp_txns['acc_id']);
				$account_name       =  $account_info['acc_name'];
	    	}

	    	$debit = '';
	    	$credit = '';
	    	$credit_total = 0;
	    	$debit_total = 0;

	    	if($value['acc_txn_drcr']=='d'){
				$debit = formatAmount($value['acc_txn_amount']);
				$debit_total = $value['acc_txn_amount'];
			}
			if($value['acc_txn_drcr']=='c'){
				$credit = formatAmount($value['acc_txn_amount']);
				$credit_total = $value['acc_txn_amount'];
			}

            $account_bal  = '0.00';	
            $account_bal_total  = 0;					
			
			$balance_type = '';	
			if($value['acc_bal'] < 0){
				$account_bal = formatAmount(abs($value['acc_bal']));
				$account_bal_total = parseAmount(abs($value['acc_bal']));
			  	$balance_type = 'CR.';
			}
			if($value['acc_bal'] >= 0){
				$account_bal = formatAmount($value['acc_bal']);
				$account_bal_total = parseAmount($value['acc_bal']);
			 	$balance_type = 'DR.';
			}

			$narration = $this->get_voucher_narration_info($value['voucher_txn_id'],'long',0);
			if($narration){$short_narration=$narration['vch_narr'];}else{$short_narration='';}
			
			$voucher_cons_info = $this->get_voucher_info($value['voucher_txn_id'],$this->company_id);
			$voucher_date      = date("d-m-Y", strtotime($voucher_cons_info['voucher_date']));
			$voucher_no        = $voucher_cons_info['comp_vch_no'];
			$comp_vch_series_id  = $voucher_cons_info['comp_vch_series_id'];

		
			$final_result[]    = array(
				'voucher_no'		=> $voucher_no,
				'voucher_txn_id'	=> $value['voucher_txn_id'],
				'comp_vch_series_id' => $comp_vch_series_id,
				'voucher_type_id'	=> $voucher_type_id,
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $comp_vch_type,
				'account_name'		=> $account_name,
				'credit'			=> $credit,
				'debit'				=> $debit,
				'balance'			=> $account_bal,
				'balance_type'		=> $balance_type,
				'balance_total'		=> $account_bal_total,
				'credit_total'		=> $credit_total,
				'debit_total'		=> $debit_total,
				'short_narration'   => $short_narration
			);
		  }
	   }
	
		// return $final_result;
		return $final_result;
	}
	
	public function load_accounts_memo_ledger($account_id, $limit, $from_date, $to_date,$pq_curPage)
	{
		$final_result = [];
		
		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $account_memo_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');

		$voucher_type_id = 8;
		$comp_vch_type = '';

		$builder = $this->db->table($voucher_type_tbl);
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->where('voucher_type_id', $voucher_type_id);
	    $voucher_type = $builder->get()->getRowArray();
	    if($voucher_type){
	    	$comp_vch_type = $voucher_type['comp_vch_type'];
	    }

		$tbl_name =  $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');

		$builder = $this->db->table($account_memo_tbl);
	    $builder->select($account_memo_tbl.'.acc_type, '.$account_memo_tbl.'.acc_id, '. $account_memo_tbl.'.acc_txn_drcr, '. $account_memo_tbl.'.acc_bal, '. $account_memo_tbl.'.voucher_txn_id, '. $account_memo_tbl.'.acc_txn_amount');
	    
		$builder->where($account_memo_tbl.'.comp_id', $this->company_id);					
		$builder->where('acc_txn_date >=', $from_date);
		$builder->where('acc_txn_date <=', $to_date);
		$builder->where('acc_type', 'acc');
		$builder->where('acc_id', $account_id);
		$builder->orderBy('acc_txn_date');
		$builder->orderBy('acc_txn_id');
		$total_records = $builder->countAllResults();

		if($pq_curPage==0){$pq_curPage=1;}
       	$offset = ($limit * ($pq_curPage - 1));
     	if ($offset > $total_records){        
            $pq_curPage = ceil($total_records / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }

		$builder = $this->db->table($account_memo_tbl);
	    $builder->select($account_memo_tbl.'.acc_type, '.$account_memo_tbl.'.acc_id, '. $account_memo_tbl.'.acc_txn_drcr, '. $account_memo_tbl.'.acc_bal, '. $account_memo_tbl.'.voucher_txn_id, '. $account_memo_tbl.'.acc_txn_amount');
	    
		$builder->where($account_memo_tbl.'.comp_id', $this->company_id);					
		$builder->where('acc_txn_date >=', $from_date);
		$builder->where('acc_txn_date <=', $to_date);
		$builder->where('acc_type', 'acc');
		$builder->where('acc_id', $account_id);
		$builder->orderBy('acc_txn_date');
		$builder->orderBy('acc_txn_id');
		$builder->limit($limit,$offset); 
		$result = $builder->get()->getResultArray();

	   if($result){
		foreach($result as $value){	

	    	$builder = $this->db->table($account_memo_tbl);
	    	$builder->where('voucher_txn_id', $value['voucher_txn_id']);
	    	$builder->where('acc_type', 'acc');
	    	$builder->where('acc_id !=', $value['acc_id']);
	    	$builder->orderBy('acc_txn_id', 'asc');
	    	$builder->limit(1);
	    	$comp_txns = $builder->get()->getRowArray();

	    	$account_name = '';
	    	if($comp_txns){
	    		$account_info       = $this->account_info($comp_txns['acc_id']);
				$account_name       =  $account_info['acc_name'];
	    	}

	    	$debit = '';
	    	$credit = '';
	    	$credit_total = 0;
	    	$debit_total = 0;

	    	if($value['acc_txn_drcr']=='d'){
				$debit = formatAmount($value['acc_txn_amount']);
				$debit_total = $value['acc_txn_amount'];
			}
			if($value['acc_txn_drcr']=='c'){
				$credit = formatAmount($value['acc_txn_amount']);
				$credit_total = $value['acc_txn_amount'];
			}

            $account_bal  = '0.00';					
			
			$balance_type = '';	
			if($value['acc_bal'] < 0){
				$account_bal = formatAmount(abs($value['acc_bal']));
			  	$balance_type = 'CR.';
			}
			if($value['acc_bal'] >= 0){
				$account_bal = formatAmount($value['acc_bal']);
			 	$balance_type = 'DR.';
			}

			$narration = $this->get_voucher_narration_info($value['voucher_txn_id'],'long',0);
			if($narration){$short_narration=$narration['vch_narr'];}else{$short_narration='';}
			
			$voucher_cons_info = $this->get_voucher_info($value['voucher_txn_id'],$this->company_id);
			$voucher_date      = date("d-m-Y", strtotime($voucher_cons_info['voucher_date']));
			$voucher_no        = $voucher_cons_info['comp_vch_no'];
			$comp_vch_series_id  = $voucher_cons_info['comp_vch_series_id'];

		
			$final_result[]    = array(
				"chkbx" => "<label><input name='voucher_ids[]' class='checkbox hidden accounts_row voucher_row' data-id='".$voucher_type_id.'||'.$value['voucher_txn_id']."' value='".$voucher_type_id.'||'.$value['voucher_txn_id']."' type='checkbox' /></label>",

				'voucher_no'		=> $voucher_no,
				'voucher_txn_id'	=> $value['voucher_txn_id'],
				'comp_vch_series_id' => $comp_vch_series_id,
				'voucher_type_id'	=> $voucher_type_id,
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $comp_vch_type,
				'account_name'		=> $account_name,
				'credit'			=> $credit,
				'debit'				=> $debit,
				'balance'			=> $account_bal,
				'balance_type'		=> $balance_type,
				'credit_total'		=> $credit_total,
				'debit_total'		=> $debit_total,
				'short_narration'   => $short_narration
			);
		  }
	   }
	
		// return $final_result;
		return [
			'total_records'	=> $total_records,
			'data'	=> $final_result
		];
	}
   
	public function load_account_transactions($start_date,$end_date,$account_id)
	{
		$tbl_name =  $account_txn_table =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
		$comp_id = $this->company_id;

		$VoucherMasterShortNames = VoucherMasterShortNames();
		$ses_comp_fy_id          = $this->session->get('ses_comp_fy_id');

		$account_info       = $this->account_info($account_id);
		$account_name       =  url_title($account_info['acc_name'],'',true);
		$account_txn_table  =  $comp_id.'_accnttxnnn_'.$account_info['acc_id'].'_'.$ses_comp_fy_id;	
		
		
		$show_account_name  =  $account_info['acc_name'];		
				
		$final_result= array();
    
			
		$builder = $this->db->table($account_txn_table); 
		$builder->where('comp_id', $comp_id);					
		$builder->where('acc_id', $account_id);
		 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		if($start_date!='' && $end_date!=''){
			$builder->where('acc_txn_date >=', $start_date);
			$builder->where('acc_txn_date <=', $end_date);
		}
		$builder->orderBy('acc_txn_date', 'asc');
		$builder->orderBy('acc_txn_id', 'asc');
		$fresult = $builder->get()->getResultArray();
		

	   if($fresult){
		foreach($fresult as $frow){	
            $account_bal  = $frow['acc_bal'];					
			$voucher_type = $VoucherMasterShortNames[$frow['voucher_type_id']];
			if($frow['acc_txn_drcr']=='d'){
				$debit=$frow['acc_txn_amount'];
				$credit=0;
			}
			if($frow['acc_txn_drcr']=='c'){
				$debit=0;
				$credit=$frow['acc_txn_amount'];
			}						
			
				$new_balance = str_replace("-",'',$account_bal);
			$balance_type = '';	
			if($account_bal<0){
			  $balance_type = 'CR.';
			}
			if($account_bal>0){
			 $balance_type = 'DR.';
			 }
			$txn_id            = $frow['txn_id'];
			$voucher_date = '--DELETED--';
			$voucher_no = '--DELETED--';
			$acc_id = $account_id;
			
			
			$narration = $this->get_narration($frow['voucher_txn_id'],$txn_id);
			if($narration){$short_narration=$narration['vch_short_narr'];}else{$short_narration='';}
			
			
			$voucher_cons_info = $this->get_voucher_info($frow['voucher_txn_id'],$comp_id);
			if($voucher_cons_info){
				$voucher_date      = date("d-m-Y", strtotime($voucher_cons_info['voucher_date']));
				$voucher_no        = $voucher_cons_info['comp_vch_no'];
				$acc_id = 0;
				
				$comp_vch_series_id  = $voucher_cons_info['comp_vch_series_id'];
			}
			
		
			$final_result[]    = array(
				'acc_id'		=> $acc_id,
				'voucher_no'		=> $voucher_no,
				'voucher_txn_id'	=> $frow['voucher_txn_id'],
				'comp_vch_series_id' => $comp_vch_series_id,
				'voucher_type_id'	=> $frow['voucher_type_id'],
				'txn_date'			=> $voucher_date,
				'voucher_type'		=> $voucher_type,
				'account_name'		=> $account_name,
				'credit'			=> !empty($credit) ? formatAmount($credit) : '',
				'debit'				=> !empty($debit) ? formatAmount($debit) : '',
				'balance'			=> !empty(abs($account_bal)) ? formatAmount(abs($account_bal)) : '',
				'balance_type'		=> $balance_type,
				'credit_total'		=> $credit,
				'debit_total'		=> $debit,
				'short_narration'   => $short_narration
			);
		  }
	   }
	
		return $final_result;
	}

	public function load_group_transactions($start_date,$end_date,$group_id){

		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        $gresult = $this->db->table($account_master_tbl)->where('acc_grp_id', $group_id)->get()->getResultArray();
        $final_result= array();

        $VoucherMasterShortNames = VoucherMasterShortNames();
		$ses_comp_fy_id          = $this->session->get('ses_comp_fy_id');
		$comp_id = $this->company_id;

        foreach($gresult as $key => $value)
        {
        	$account_id = $value['acc_id'];
        	$account_name = $value['acc_name'];
        	$account_txn_table  =  $comp_id.'_accnttxnnn_'.$value['acc_id'].'_'.$ses_comp_fy_id;

        	$builder = $this->db->table($account_txn_table); 
			$builder->where('comp_id', $comp_id);					
			$builder->where('acc_id', $account_id);
			 // branch check 
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
			if($start_date!='' && $end_date!=''){
				$builder->where('acc_txn_date >=', $start_date);
				$builder->where('acc_txn_date <=', $end_date);
			}
			$builder->orderBy('acc_txn_date', 'asc');
			$builder->orderBy('acc_txn_id', 'asc');
			$fresult = $builder->get()->getResultArray();
			

		   if($fresult){
			foreach($fresult as $frow){	
	            $account_bal  = $frow['acc_bal'];					
				$voucher_type = $VoucherMasterShortNames[$frow['voucher_type_id']];
				if($frow['acc_txn_drcr']=='d'){
					$debit=$frow['acc_txn_amount'];
					$credit=0;
				}
				if($frow['acc_txn_drcr']=='c'){
					$debit=0;
					$credit=$frow['acc_txn_amount'];
				}						
				
					$new_balance = str_replace("-",'',$account_bal);
				if($account_bal<0){
				  $balance_type = 'CR.';
				}
				 else
				 $balance_type = 'DR.';
				 
				$txn_id            = $frow['txn_id'];

				$voucher_date = '--DELETED--';
				$voucher_no = '--DELETED--';
				$acc_id = $account_id;;
				$voucher_cons_info = $this->get_voucher_info($frow['voucher_txn_id'],$comp_id);
				if($voucher_cons_info){
					$voucher_date      = date("d-m-Y", strtotime($voucher_cons_info['voucher_date']));
					$voucher_no        = $voucher_cons_info['comp_vch_no'];
					$acc_id = 0;
				}
				
				
			
				$final_result[]    = array(
					'txn_id'			=>$txn_id,
					'acc_id'			=>$acc_id,
					'account_bal'		=>$new_balance,
					'voucher_no'		=>$voucher_no,
					'voucher_txn_id'	=>$frow['voucher_txn_id'],
					'voucher_type_id'	=>$frow['voucher_type_id'],
					'txn_date'			=>$voucher_date,
					'voucher_type'		=>$voucher_type,
					'account_name'		=> $account_name,
					'credit_total'		=>$credit,
					'debit_total'		=>$debit,
					'credit'			=> !empty($credit) ? formatAmount($credit) : '',
					'debit'				=> !empty($debit) ? formatAmount($debit) : '',
					'balance'			=> !empty(abs($account_bal)) ? formatAmount(abs($account_bal)) : '',
					'balance_type'		=>$balance_type,
					'show_group_balance'=>$new_balance
				);
			  }
		   }
        }
	
		return $final_result;
	}



public function get_voucher_info($voucher_txn_id,$comp_id){
	    $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
		$tbl_name         = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
		$builder          = $this->db->table($tbl_name); 
		$builder->where('comp_id', $comp_id);
		$builder->where('voucher_txn_id', $voucher_txn_id);
		$result =  $builder->get()->getRowArray();
		return $result;
	 }
	 
	   
	 public function remove_accounts($ids,$comp_id){
		$ses_comp_fy_id     =  $this->session->get('ses_comp_fy_id');
		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');  
		       
		$ids = explode(",",$ids);
        foreach($ids as $account_id){			
			$account_info       = $this->account_info($account_id);
			
			$accnt_name         =  $account_info['acc_name'];
			
		
			$account_name       =  url_title($accnt_name,'',true);
			$account_txn_table  =  $comp_id.'_'.strtolower($account_name).'_txn_'.$ses_comp_fy_id;
			$this->db->table($account_master_tbl)->where('acc_id',$account_id)->delete();
			
			try {
                $this->db->table($account_txn_table)->where('comp_id',$comp_id)->where('acc_id',$account_id)->delete();
            } catch (\Exception $e) {
                // exit($e->getMessage());
            }
			
            
		 }
		return TRUE;
	 } 
	 public function remove_single_accounts($id){
		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');  
        $this->db->table($account_master_tbl)->where('acc_id',$id)->delete();
		return TRUE;
	 }
	 function check_account_with_voucher($id)
     {
         $voucher_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
		  $data =  $this->db->table($voucher_tbl)->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'))->where('master_id', $id)->whereIn('master_id_type',['acc','aco'])->get()->getRowArray();		
		else
	     $data =  $this->db->table($voucher_tbl)->where('master_id', $id)->whereIn('master_id_type',['acc','aco'])->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }
	   
	 public function update_account($data,$account_id){
	    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $table = $this->db->table($account_master_tbl)->where('LOWER(acc_name)', strtolower(trim($data['acc_name'])))
	                                                   ->where('acc_id !=',$account_id)
                                                	   ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['acc_name'])))
                                                	   ->where('acc_id !=',$account_id)
                                                	   ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table($account_master_tbl)->where('LOWER(acc_name)', strtolower(trim($data['acc_name_alias'])))
	                                                   ->where('acc_id !=',$account_id)
                                                	   ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['acc_name_alias'])))
                                                	   ->where('acc_id !=',$account_id)
                                                	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	    }
	    
	    $this->db->table($account_master_tbl)->where('acc_id',$account_id)->update($data);
	    
	    $groups = $this->get_sundry_groups();
        
        if(in_array($data['acc_grp_id'],$groups))
        {
            $exists = $this->checkBillMaster($account_id);
            if(!$exists){
                $this->createBillMaster($account_id);
            }
        }
	    
		return ['status' => true, 'account_id' => $account_id];	
   } 

   public function update_vendor_code($data,$account_id){
   		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
   		$this->db->table($account_master_tbl)->where('acc_id',$account_id)->update($data);
   }	
   
   public function CreateAccountTxnTable($tablename){
	    $table_query ='CREATE TABLE IF NOT EXISTS `'.$tablename.'` (`acc_txn_id` bigint NOT NULL AUTO_INCREMENT , `comp_id` BIGINT(10) NOT NULL ,`acc_txn_date` DATE  NULL , `acc_txn_amount` DECIMAL(18,2)  NULL ,`acc_txn_drcr` VARCHAR(10)  NULL , `comp_vch_series_no` VARCHAR(100) NULL,`acc_id` BIGINT(10) NOT NULL ,`txn_id` BIGINT(10) NOT NULL, `voucher_type_id` BIGINT(10) NOT NULL , `acc_bal` DECIMAL(18,2) NULL,`voucher_txn_id` BIGINT(10) NULL, `bo_id` BIGINT DEFAULT 1,`posted_on` DATETIME NOT NULL,
					  PRIMARY KEY (`acc_txn_id`)) ENGINE = InnoDB;';
        $this->db->query($table_query);
       }
	   
	public function add_account_bank($CompanyCode,$data){
	    $company_bank_tbl = $this->company_id.'_compbanknn_'.$this->session->get('ses_comp_fy_id'); 
		$acc_bank_accno   = $this->enc_string->nc_string($data['acc_bank_accno'],'de');
		$acc_bankname     = $this->enc_string->nc_string($data['acc_bankname'],'de');		
		$exists = $this->db->table($company_bank_tbl)->where('comp_code',$CompanyCode)->where('acc_bank_accno',$acc_bank_accno)->where('LOWER(acc_bankname)', strtolower(trim($acc_bankname)))->get()->getRowArray(); 
	    if(!$exists){
		  $this->db->table($company_bank_tbl)->insert($data);	
	      }	   
	   }
	   
   public function add_account_contacts($CompanyCode,$data){
       
        $account_contact_tbl = $this->company_id.'_acctcontnn_'.$this->session->get('ses_comp_fy_id');
       
		$acc_cont_mobile = $this->enc_string->nc_string($data['acc_cont_mobile'],'de');
        $acc_cont_name1  = $this->enc_string->nc_string($data['acc_cont_name1'],'de');
		$exists = $this->db->table($account_contact_tbl)->where('comp_code',$CompanyCode)->where('acc_cont_mobile',$acc_cont_mobile)->where('LOWER(acc_cont_name1)', strtolower(trim($acc_cont_name1)))->get()->getRowArray(); 
	    if(!$exists){
		  $this->db->table($account_contact_tbl)->insert($data);	
	      }	   
	   }
   
   public function ajax_accounts_list(){
	    $dealer_types  = DealerTypes();
	    $comp_id       = $this->session->get('ses_company_id');
	    
	    
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
	    if($this->company_id){
	    
	   
	    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id'); 
	    $account_groupn_tabl     = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	    $account_opbalance_tabl     = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	    
	   
	    
	    $builder = $this->db->table($account_master_tbl); 
	   
       // echo $this->db->GetLastQuery();
        
    //    die();
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
        
        
         $builder->orderBy('acc_name');              
         if($dataIndx=='account_name'){
	    
    	    if($condition=='begin'){
    	         $builder->Like('LOWER(acc_name)',$search_text,'after');
    	         $builder->orLike('LOWER(acc_name_alias)',$search_text,'after');
    	         $builder->orLike('LOWER(acc_name_print)',$search_text,'after');
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like('LOWER(acc_name)',$search_text,'both');
    	        $builder->orLike('LOWER(acc_name_alias)',$search_text,'both');
    	        $builder->orLike('LOWER(acc_name_print)',$search_text,'both');
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike('LOWER(acc_name)',$search_text,'both');
    	        $builder->orNotLike('LOWER(acc_name_alias)',$search_text,'both');
    	        $builder->orNotLike('LOWER(acc_name_print)',$search_text,'both');
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like('LOWER(acc_name)',$search_text,'before');
    	        $builder->orLike('LOWER(acc_name_alias)',$search_text,'before');
    	        $builder->orLike('LOWER(acc_name_print)',$search_text,'before');
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where('( LOWER(acc_name)="'.$search_text.'" OR LOWER(acc_name_alias)="'.$search_text.'" OR LOWER(acc_name_print)="'.$search_text.'")'   );
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where('( LOWER(acc_name)="'.$search_text.'" OR LOWER(acc_name_alias)="'.$search_text.'" OR LOWER(acc_name_print)="'.$search_text.'")'   );
    	     }   
	     
	     }
	     else if($dataIndx=='group_name'){
	     if($condition=='begin'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "'.$search_text.'%" OR LOWER(acc_grp_alias) LIKE "'.$search_text.'%" ))  ');
    	         
    	    }
    	 if($condition=='contain'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "%'.$search_text.'%" OR LOWER(acc_grp_alias) LIKE "%'.$search_text.'%" ))  ');
    	         
    	    }    
	   if($condition=='notcontain'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) NOT LIKE "%'.$search_text.'%" OR LOWER(acc_grp_alias) NOT LIKE "%'.$search_text.'%" ))  ');
    	         
    	    } 
      if($condition=='end'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "%'.$search_text.'" OR LOWER(acc_grp_alias) LIKE "%'.$search_text.'" ))  ');
    	         
    	    }
      if($condition=='equal'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) = "'.$search_text.'" OR LOWER(acc_grp_alias) = "'.$search_text.'" ))  ');
    	         
    	    }
    	    
       if($condition=='notequal'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) != "'.$search_text.'" OR LOWER(acc_grp_alias) != "'.$search_text.'" ))  ');
    	         
    	    }
	    
	} 
	     else if($dataIndx=='op_bal'){
	        if($condition=='equal'){
				if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	        $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
    	         
    	    }
          elseif($condition=='notequal'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	         $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal) !=',(float)$search_text);
    	         
    	    }
    	  elseif($condition=='less'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	         $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal) <',(float)$search_text);
    	         
    	    }  
    	  elseif($condition=='great'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	         $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal) >',(float)$search_text);
    	         
    	    }  
	       
	   }
	   
	     else if($dataIndx=='bal_type'){
	        if($condition=='equal'){
				if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	        $builder->where($account_opbalance_tabl.'.acc_op_bal <',0);
    	         
    	    }
          elseif($condition=='notequal'){
			  if($this->session->get('ses_boid')!=''){
				$builder->where($account_opbalance_tabl.'.bo_id', $this->session->get('ses_boid'));
			    $builder->where('abs('.$account_opbalance_tabl.'.acc_op_bal)',(float)$search_text);
				}
				else
    	       $builder->where($account_opbalance_tabl.'.acc_op_bal >',0);
    	    }
    	   
	       
	   }
	     $builder->where('acc_grp_parent_id !=',14); // BO ACCOUNTS RESTRICTION TO NOT TO SHOW IN ACCOUNT GROUP MASTER INTERFACE / LIST.
	 	 $builder->where('comp_id', $comp_id);
	 	 //$builder->limit($pq_rPP,$offset);
	 	 $builder->join($account_opbalance_tabl, $account_master_tbl.'.acc_id = '.$account_opbalance_tabl.'.acc_id', 'left');
		 $result = $builder->get()->getResultArray();
        // echo $this->db->GetLastQuery();
       
         $records = array(); 	
		
        foreach($result as $values){
         
				
			if(isset($dealer_types[$values['acc_dealer_type']]))
			 $show_dealer =$dealer_types[$values['acc_dealer_type']];
		    else
			$show_dealer = '';
		
			if($values['acc_grp_id'] != 0){
				$group_info   =  $this->main_group_info($values['acc_grp_id']);
			   	if(isset($group_info['acc_grp_name']))
				    $show_group = $group_info['acc_grp_name'];
				else
					$show_group = '';
			}
			else{
				$group_parent_info   =  $this->group_parent_info($values['acc_grp_parent_id']);
			   	if(isset($group_parent_info['acc_grp_parent']))
				    $show_group = $group_parent_info['acc_grp_parent']. ' <i>(PARENT)</i>';
				else
					$show_group = '';
			}
			
				
			
			   $get_opn_balance_info = $this->acc_opn_balance_info($values['acc_id']);
			   if(!$get_opn_balance_info){
				   $acc_op_bal = '0';
				   $acc_op_type = '';
			   }
	          	else{
			      $acc_op_bal = abs($get_opn_balance_info['acc_op_bal']);
				  
				  $acc_op_type = ($get_opn_balance_info['acc_op_bal']<0)?'CR.':'DR.';
				}
			  
				$records[] = array(	
			          'chkbx' => '<input name="account_ids[]" class="checkbox accounts_row" data-id="'.$values['acc_id'].'" type="checkbox" value="'.$values['acc_id'].'">',           
 			          'acc_id'       => $values['acc_id'],
                      'account_name' => ucwords($values['acc_name']),
					  'vendor_code' => ($values['vendor_code']>0)?$values['vendor_code']:'',
					  'group_name'   => $show_group,
                      'op_bal'       => $acc_op_bal,					                    
                      'bal_type'     => $acc_op_type,
					  'isedited'     =>'0'
 					  ); 
		           }	 
	
	     if($pq_curPage==0){$pq_curPage=1;}
    	$offset = ($pq_rPP * ($pq_curPage - 1));
        $total_records = count($records);
     if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
            
            
	$data = 	 array_slice($records, $offset, $pq_rPP);
	if($records)
	  $totalrec = count($records);
	 else
	 $totalrec = 0;

		}
		else 
		{
			$totalrec=0;
			$pq_curPage=1;
			$data = array();
		}
       echo  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($data)."}"; 
        
        
     } 
     public function get_account_group_name($id){
         $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
      
         $data = $this->db->table($account_grp_tbl)->select('acc_grp_name')->where('acc_grp_id', $id)->get()->getRowArray();
        //  $acc_name = $this->enc_string->nc_string($data['acc_grp_name'],'de');
         return $data['acc_grp_name'];
     }
     
  public function add_group($data){
       $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	  
	   $exists = $this->db->table( $account_grp_tbl)->where('comp_id',trim($data['comp_id']))->where('LOWER(acc_grp_name)', strtolower(trim($data['acc_grp_name'])))->get()->getRowArray(); 
	    if($exists)
		 return "0";
	   else{
		  $this->db->table($account_grp_tbl)->insert($data);
		  $group_id = $this->db->insertID();
		  return $group_id;
	     }
      }   
   public function remove_groups($ids,$comp_id){
        $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	
		$ids = explode(",",$ids);
        foreach($ids as $group_id){			
			$this->db->table($account_grp_tbl)->where('acc_grp_id',$group_id)->delete();
		 }
		return TRUE;
	 } 	
	 
	public function remove_single_groups($id){
        $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	
		$this->db->table($account_grp_tbl)->where('acc_grp_id',$id)->delete();
		 
		return TRUE;
	 } 	
	 
	 
   public function update_group($update_data,$group_id){
        $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	    $this->db->table($account_grp_tbl)->where('acc_grp_id',$group_id)->update($update_data);
		return true;	
   } 	
   
   function group_primary_dropdown(){		      
             $account_grp_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');
			//// emit branch parent group id	  
			$data =  $this->db->table($account_grp_tbl)->where('acc_grp_parent_id !=',14)->orderBy('acc_grp_parent')->get()->getResultArray();
			$final_result = array();
			$final_result['']  = 'Choose';
			 if($data){
			   foreach($data as $row){
				  $final_result[$row['acc_grp_parent_id']] = ucwords($row['acc_grp_parent']);			   
					}
				}
		
		
	  return $final_result;	
     }

  function group_main_dropdown($group_id = 0){		      
             $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
				  
			$builder =  $this->db->table($account_grp_tbl);
			if($group_id != 0)
				$builder->where('acc_grp_id !=', $group_id);
            $builder->where('acc_grp_parent_id !=',14);  // emit branch parent group id
			$builder->orderBy('acc_grp_name');
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
	 
 

  function account_info($account_id){	 
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
   }
   
   function main_group_info($acc_grp_id){	 
      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_id', $acc_grp_id)->get()->getRowArray();   	   
   }
   function sub_group_info($acc_grp_id){	 
      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('under_acc_grp_id', $acc_grp_id)->get()->getResultArray();   	   
   }

   function check_group_restriction($acc_grp_id, $tag){	 
      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_id', $acc_grp_id)->where('restrictions', $tag)->get()->getRowArray();   	   
   }

   function group_parent_info($acc_grp_id){	 
      $account_grp_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_parent_id', $acc_grp_id)->get()->getRowArray();   	   
   }

   function check_parent_restriction($acc_grp_parent_id, $tag){	 
      $account_grp_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->where('restrictions', $tag)->get()->getRowArray();   	   
   }
   function get_parimary_groups($acc_grp_parent_id, $group_id = 0){	 
      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
      $builder = $this->db->table($account_grp_tbl);
      $builder->where('acc_grp_primary', 'Y');
      $builder->where('acc_grp_parent_id', $acc_grp_parent_id);
      if($group_id != 0)
      	$builder->where('acc_grp_id !=', $group_id);

  	  return $builder->get()->getResultArray();   	   
   }

   
   function check_account_with_group($group_id)
   {
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');  
	   $account_data = $this->db->table($account_master_tbl)->where('acc_grp_id',$group_id)->get()->getRowArray(); 
	    if(empty($account_data)){
	        return 0;
	    }
		return 1;
   }
   function check_bill_sundry_with_group($group_id)
   {
       $billsundry_tbl  = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');  
	   $account_data = $this->db->table($billsundry_tbl)->where('acc_grp_id',$group_id)->get()->getRowArray(); 
	    if(empty($account_data)){
	        return 0;
	    }
		return 1;
   }
   
   function check_account($account_name,$group_name)
   {
       $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
       
	   $group_data = $this->db->table( $account_grp_tbl)->where('comp_id',$this->company_id)->where('LOWER(acc_grp_name)', strtolower(trim($group_name)))->get()->getRowArray(); 
	    if(empty($group_data)){
	        return 0;
	    }
	   $group_id = $group_data['acc_grp_id'];
	   
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');  
	   $account_data = $this->db->table($account_master_tbl)->where('acc_name', trim($account_name))->where('acc_grp_id',$group_id)->get()->getRowArray(); 
	    if(empty($account_data)){
	        return 0;
	    }
		return 1;
   }
   
   function get_or_create_account_id($name,$group_name)
   {
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       if(trim($name) == ''){
           if($group_name == 'Sales'){
               $account_name = 'Sales Account';
           }
           else{ //Purchase
               $account_name = 'Purchase Account';
           }
       }
       else{
            $account_name = trim($name);   
       }
       
       if($group_name == 'Sales'){
           $group_id = 9;
       }
       else{ //Purchase
           $group_id = 8;
       }
       
       $account_data = $this->db->table($account_master_tbl)->where('acc_name', trim($account_name))->where('acc_grp_id',$group_id)->get()->getRowArray();
       if(!empty($account_data)){
	        return $account_data['acc_id'];
	   }
	   $data = [
	        'comp_id'           => $this->company_id,
	        'acc_name'          => $account_name,
	        'acc_name_alias'    => $account_name,
	        'acc_name_print'    => $account_name,
	        'acc_grp_id'        => $group_id,
	   ];
	   $this->db->table($account_master_tbl)->insert($data);		 
	   return $this->db->insertID();
       
   }
}