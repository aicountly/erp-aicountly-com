<?php
namespace App\Models\Admin;

use CodeIgniter\Model;

class DisplayModel extends Model	{
	
     public function __construct() {
        parent::__construct();        
       $db             = \Config\Database::connect();	
	   $this->session  = \Config\Services::session();
	   $this->comp_code     =  $this->session->get('ses_company_code');
    }
 
   public function ajax_trialbalance_list($company_account_groups){	 
	   $base_url = base_url().'/'.getenv('AdminPath');      
      if(isset($_REQUEST['length']) && $_REQUEST['length']!=''){
	       $length = $_REQUEST['length'];	       
	     }
		else{
		  $length  = 10;		 
		}
	   if(isset($_REQUEST['start']) && $_REQUEST['start']!=''){
			$start  = $_REQUEST['start'];
	   }	
       else
           $start   = 0;	  	    
	    $builder = $this->db->table("account_master"); 	
        $builder->orderBy('acc_name');		  
        $iTotalRecords   = $builder->countAllResults(); 				
        $iDisplayLength  = intval($length);
		$iDisplayLength  = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
		$iDisplayStart   = intval($start);
		$sEcho           = intval($_REQUEST['draw']);
		$records         = array();
		$sorting_records = array();
		$records["data"] = array();
		$end             = $iDisplayStart + $iDisplayLength;
		$end             = $end > $iTotalRecords ? $iTotalRecords : $end;
		$id              = 0;
				
		$builder->limit($length,$start);
		$builder->orderBy('acc_name');
		$result = $builder->get()->getResultArray();
		$credit_sum='0';
		$debit_sum='0';
		foreach($result as $values){            
		    
			if(isset($company_account_groups[$values['acc_grp']]))
				 $show_group =$company_account_groups[$values['acc_grp']];
			 else
				 $show_grp ='';
			 
			$account_name = url_title($values['acc_name'],'',true);
			$txn_table    = 'acc'.$values['comp_code'].'_'.$account_name.'_txn'; 
			$account_credit_bit = $this->credit_debit_list($values['comp_code'],$values['acc_id'],$txn_table);
	        $id = ($id + 1);	   
			
			$debit_sum  = $credit_sum+$account_credit_bit['debit'];
			$credit_sum = $credit_sum+$account_credit_bit['credit'];
						
            $records["data"][] = array(
				$values['acc_name'],		
				$show_group,
				number_format($account_credit_bit['debit'],2),
				number_format($account_credit_bit['credit'],2),
			   );
		    }
			
		   $records["draw"]            = $sEcho;
		   $records["recordsTotal"]    = $iTotalRecords;
		   $records["credit_sum"]      = $credit_sum;
		   $records["debit_sum"]       = $debit_sum;
		   $records["recordsFiltered"] = $iTotalRecords;
		   return json_encode($records);
      
   }
   
   function credit_debit_list($comp_code,$account_id,$table_name){
	    $builder = $this->db->table($table_name); 
        $builder->orderBy('posted_on');                
		$builder->where('acc_name', $account_id);
		$builder->where('comp_code', $comp_code);
		$result = $builder->get()->getResultArray(); 
		$all_credit= 0;
		$all_debit = 0;		
		if($result){
			foreach($result as $row){
				if($row['acc_txn_drcr']=='c'){
					$all_credit=$all_credit+$row['acc_txn_amount'];
				}
				else if($row['acc_txn_drcr']=='d'){
					$all_debit=$all_debit+$row['acc_txn_amount'];					
				}
				
			}
			
		}
	 return array("credit"=>$all_credit,"debit"=>$all_debit);  
   
   }
    

}