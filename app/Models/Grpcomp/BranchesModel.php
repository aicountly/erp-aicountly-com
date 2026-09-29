<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Libraries\enc_string;
class BranchesModel extends Model	{
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

    function get_cash_groups_list()
    {
        
		$groups = [23];
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
       return $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();  
      }  
   
	
   function bill_sundry_op_balance($id)
    {
    	$bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
	    $result = $this->db->table($bsdoppybal_tbl)->where('bill_sundry_id', $id)->get()->getRowArray();
	    return $result; 
    }
  
  function billsundry_info($billsundry_id){	 
       $billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
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
    
	public function get_branch_info($bo_id){
        $hobomaster_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($hobomaster_tbl)->where('bo_id', $bo_id)->get()->getRowArray();
        return $data;
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
        $hobomaster_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');  
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		
        $table = $this->db->table($hobomaster_tbl)->where('LOWER(bo_name)', strtolower(trim($data['bo_name'])))
                                            	      ->orWhere('LOWER(bo_alias)', strtolower(trim($data['bo_name'])))
                                            	       ->get()->getRowArray(); 
                                            	  
        if($table){
            return ['status' => false, 'message' => 'Branch name must be unique'];
        }
        
        $table = $this->db->table($hobomaster_tbl)->where('LOWER(bo_name)', strtolower(trim($data['bo_alias'])))
                                            	      ->orWhere('LOWER(bo_alias)', strtolower(trim($data['bo_alias'])))
                                            	      ->get()->getRowArray();
                                            	   
        if($table){
            return ['status' => false, 'message' => 'Branch alias must be unique'];
        }
        
		
		// check same bo name exisst in account master if exists deny to add 
		
		 $acctable = $this->db->table($account_master_tbl)->where('LOWER(acc_name)', strtolower(trim($data['bo_name'])))
                                            	      ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['bo_name'])))
                                            	       ->get()->getRowArray(); 
                                            	  
        if($acctable){
            return ['status' => false, 'message' => 'Account name must be unique'];
        }
        
        $acctable = $this->db->table($account_master_tbl)->where('LOWER(acc_name)', strtolower(trim($data['bo_alias'])))
                                            	      ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['bo_alias'])))
                                            	      ->get()->getRowArray();
                                            	   
        if($acctable){
            return ['status' => false, 'message' => 'Account alias must be unique'];
        }
		
		// ON CREATION OF EACH BO MASTER, IT WILL ALSO CREATE ACCOUNT MASTER UNDER THE NEW PARENT OF BRANCH ACCOUNTS
		$acc_data   = [
						'comp_id'         => $this->company_id,
						'acc_name'        => ucwords(clean($data['bo_name'])), //remove special characters
						'acc_name_alias'  => $data['bo_alias'],
						'acc_name_print'  => $data['bo_alias'],
						'acc_grp_id'      => $data['acc_grp_id'],
						'acc_grp_parent_id' => 14,
						'vendor_code'       =>'',						
						'acc_add1'        => $this->enc_string->nc_string($data['bo_add1'],'en'),
						'acc_add2'        => $this->enc_string->nc_string($data['bo_add2'],'en'),
						'acc_country'     => $data['bo_country'],
						'acc_symbol'      => '',
						'acc_state'       => $data['bo_state'],
						'acc_state_code'  => '',
						'acc_city'        => $this->enc_string->nc_string($data['bo_city'],'en'),						
						'acc_pin'         => $this->enc_string->nc_string($data['bo_pin'],'en'),
						'acc_gstin'       => '',
						'acc_aadhar'      => '',
						'acc_tan'         => '',
						'acc_pan'         => '',
						'acc_jurisd'      => '',
						'acc_email'       => '',
						'acc_tel'         => '',
						'acc_wamobile'    => '',
						'acc_mobile'      => '',
						'acc_fax'         => '',
						'acc_transport'   => '',
						'acc_station'     => '',						
						'acc_distance'    => '',
						'acc_iec'         => '',
						'acc_prof_tax'    => '',
						'acc_dealer_type' => '',
						'acc_const'       => ''
					    ];
        $this->db->table($account_master_tbl)->insert($acc_data);
        $account_id = $this->db->insertID();
		
		 $account_txn_table_name =  $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
		 $this->CreateAccountTxnTable($account_txn_table_name);
		 
		 
		
		
		$file  =     WRITEPATH.'comp'.$this->company_id.'/acc.json';
			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
			        file_put_contents($file, ""); 
		            $accounts_list  = $this->company_all_accounts();
		            $comp_folder    = 'comp'.$this->company_id;
		            $f = fopen($file, 'a');
                    fwrite($f,$accounts_list);
			     }
				 
				 
		
		
		$data['acc_id'] = $account_id;
        $this->db->table($hobomaster_tbl)->insert($data);		 
        $bo_id = $this->db->insertID();
		
		$accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
		 $opndata = array("acc_id"=>$account_id,"acc_op_bal"=>"0","acc_py_bal"=>"0","bo_id"=>$bo_id);
	   	$this->db->table($accoppybal_tbl)->insert($opndata);
		
        return ['status' => true, 'bo_id' => $bo_id];
    
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
        $data = [
                'bills_ref_name' => 'UNDEFINED',
                'acc_id'         => $id,
                'bills_status'    => 'undefined',
                'bill_due_date'  => $this->financialYearBeginningDate()
            ];
        
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)->insert($data);
    }
    
    function checkBillMaster($id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
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
            $fy_start = $y."-04-01-";
        }
        else
        {
            $y = date('Y', strtotime('-1 year'));          
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
	   $builder->where('acc_grp_parent_id',14);
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
	    $builder->where('acc_grp_parent_id',14);
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
	 
	  public function remove_single_branches($bo_id){
		$account_master_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');  
        $this->db->table($account_master_tbl)->where('bo_id',$bo_id)->delete();
		return TRUE;
	 }
	 
	 public function remove_single_accounts($id){
		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');  
        $this->db->table($account_master_tbl)->where('acc_id',$id)->delete();
		return TRUE;
	 }
	 
	 function mark_branch_ho($bo_id){
		 $hobomaster_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id'); 
		 $HO_info = $this->get_branch_info(1);  // Main HO Detail
		 if($HO_info){
		 $BO_info = $this->get_branch_info($bo_id);  // Main HO Detail
		 
		$updateBO_data = array('bo_name'=>$BO_info['bo_name'],'bo_alias'=>$BO_info['bo_alias'],'acc_grp_id'=>$BO_info['acc_grp_id'],'bo_opdate'=>$BO_info['bo_opdate'],'bo_cldate'=>$BO_info['bo_cldate'],
		                       'bo_add1'=>$BO_info['bo_add1'],'bo_add2'=>$BO_info['bo_add2'],'bo_city'=>$BO_info['bo_city'],'bo_state'=>$BO_info['bo_state'],
							   'bo_country'=>$BO_info['bo_country'],'bo_pin'=>$BO_info['bo_pin'],'bo_zone'=>$BO_info['bo_zone'],'acc_id'=>$BO_info['acc_id']); 
		  
	    $this->db->table($hobomaster_tbl)->where('bo_id', 1)->update($updateBO_data);	 
		
      
	    $updateHO_data = array('bo_name'=>$HO_info['bo_name'],'bo_alias'=>$HO_info['bo_alias'],'acc_grp_id'=>$HO_info['acc_grp_id'],'bo_opdate'=>$HO_info['bo_opdate'],'bo_cldate'=>$HO_info['bo_cldate'],
		                       'bo_add1'=>$HO_info['bo_add1'],'bo_add2'=>$HO_info['bo_add2'],'bo_city'=>$HO_info['bo_city'],'bo_state'=>$HO_info['bo_state'],
							   'bo_country'=>$HO_info['bo_country'],'bo_pin'=>$HO_info['bo_pin'],'bo_zone'=>$HO_info['bo_zone'],'acc_id'=>$HO_info['acc_id']); 
							   
      
	   $this->db->table($hobomaster_tbl)->where('bo_id', $bo_id)->update($updateHO_data);		
	   
		 }
		 else{
		 $this->db->table($hobomaster_tbl)->where('bo_id', $bo_id)->update(array('bo_id'=>'1'));	 
			 
		 }
	 }
	 
	 
	 function check_account_with_voucher($id)
     {
         $voucher_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($voucher_tbl)->where('master_id', $id)->whereIn('master_id_type',['acc','aco'])->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }
	   
	 public function update_account($data,$bo_id,$accid){
	    $hobomaster_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
		$accmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $table = $this->db->table($hobomaster_tbl)->where('LOWER(bo_name)', strtolower(trim($data['bo_name'])))
	                                                   ->where('bo_id !=',$bo_id)
                                                	   ->orWhere('LOWER(bo_alias)', strtolower(trim($data['bo_name'])))
                                                	   ->where('bo_id !=',$bo_id)
                                                	   ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table($hobomaster_tbl)->where('LOWER(bo_name)', strtolower(trim($data['bo_alias'])))
	                                                   ->where('bo_id !=',$bo_id)
                                                	   ->orWhere('LOWER(bo_alias)', strtolower(trim($data['bo_alias'])))
                                                	   ->where('bo_id !=',$bo_id)
                                                	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	    }
	    
		
		$acctable = $this->db->table($accmaster_tbl)->where('LOWER(acc_name)', strtolower(trim($data['bo_name'])))
	                                                   ->where('acc_id !=',$accid)
                                                	   ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['bo_name'])))
                                                	   ->where('acc_id !=',$accid)
                                                	   ->get()->getRowArray(); 
	    if($acctable){
	        return ['status' => false, 'message' => 'Account name must be unique'];
	    }
	    
	    $acctable = $this->db->table($accmaster_tbl)->where('LOWER(acc_name)', strtolower(trim($data['bo_alias'])))
	                                                   ->where('acc_id !=',$accid)
                                                	   ->orWhere('LOWER(acc_name_alias)', strtolower(trim($data['bo_alias'])))
                                                	   ->where('acc_id !=',$accid)
                                                	   ->get()->getRowArray();
	    if($acctable){
	        return ['status' => false, 'message' => 'Account alias must be unique'];
	    }
	    
		
		
	    $this->db->table($hobomaster_tbl)->where('bo_id',$bo_id)->update($data);
		
		
		// update account master data
		$update_account_data = [
						'acc_name'        => ucwords(clean($data['bo_name'])), //remove special characters
						'acc_name_alias'  => $data['bo_alias'],
						'acc_name_print'  => $data['bo_alias'],
						'acc_grp_id'      => $data['acc_grp_id'],
						'acc_add1'        => $this->enc_string->nc_string($data['bo_add1'],'en'),
						'acc_add2'        => $this->enc_string->nc_string($data['bo_add2'],'en'),
						'acc_country'     => $data['bo_country'],
						'acc_state'       => $data['bo_state'],
						'acc_city'        => $this->enc_string->nc_string($data['bo_city'],'en'),						
						'acc_pin'         => $this->enc_string->nc_string($data['bo_pin'],'en'),
						
					    ];
		$this->db->table($accmaster_tbl)->where('acc_id',$accid)->update($update_account_data);
		
	     $file  =     WRITEPATH.'comp'.$this->company_id.'/acc.json';
        			    if (is_dir(WRITEPATH.'comp'.$this->company_id)) {
        			        file_put_contents($file, ""); 
        		            $accounts_list  = $this->company_all_accounts();
        		            $comp_folder    = 'comp'.$this->company_id;
        		            $f = fopen($file, 'a');
                            fwrite($f,$accounts_list);
        			     }
	  
	    
		return ['status' => true, 'bo_id' => $bo_id];	
   } 

   public function update_vendor_code($data,$account_id){
   		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
   		$this->db->table($account_master_tbl)->where('acc_id',$account_id)->update($data);
   }	
   
   public function CreateAccountTxnTable($tablename){
	    $table_query ='CREATE TABLE IF NOT EXISTS `'.$tablename.'` (`acc_txn_id` bigint NOT NULL AUTO_INCREMENT , `comp_id` BIGINT(10) NOT NULL ,`acc_txn_date` DATE  NULL , `acc_txn_amount` DECIMAL(18,2)  NULL ,`acc_txn_drcr` VARCHAR(10)  NULL , `comp_vch_series_no` VARCHAR(100) NULL,`acc_id` BIGINT(10) NOT NULL ,`txn_id` BIGINT(10) NOT NULL, `voucher_type_id` BIGINT(10) NOT NULL , `acc_bal` DECIMAL(18,2) NULL,`voucher_txn_id` BIGINT(10) NULL, `bo_id` BIGINT(10) NULL,`posted_on` DATETIME NOT NULL,
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
   
   
   public function ajax_branch_list(){
	    	
	    $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    
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
	   
	    
	   
	    $account_master_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id'); 
	    $account_groupn_tabl     = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	    $account_opbalance_tabl     = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	    
	   
	    
	    $builder            = $this->db->table($account_master_tbl); 
	   
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
        
        
         $builder->orderBy('bo_name');              
         if($dataIndx=='bo_name'){
	    
    	    if($condition=='begin'){
    	         $builder->Like('LOWER(bo_name)',$search_text,'after');
    	         $builder->orLike('LOWER(bo_alias)',$search_text,'after');
    	        
    	    }
    	   else if( $condition=='contain'){
    	        $builder->Like('LOWER(bo_name)',$search_text,'both');
    	        $builder->orLike('LOWER(bo_alias)',$search_text,'both');
    	        
    	    } 
    	   else if( $condition=='notcontain'){
    	        $builder->notLike('LOWER(bo_name)',$search_text,'both');
    	        $builder->orNotLike('LOWER(bo_alias)',$search_text,'both');
    	      
    	    }   
    	   else if($condition=='end'){
    	        $builder->Like('LOWER(acc_name)',$search_text,'before');
    	        $builder->orLike('LOWER(bo_alias)',$search_text,'before');
    	  
    	    } 
    	   else if($condition=='equal'){
    	        $builder->where('( LOWER(bo_name)="'.$search_text.'" OR LOWER(bo_alias)="'.$search_text.'" )'   );
    	     }
    	    else if($condition=='notequal'){
    	        $builder->where('( LOWER(bo_name)="'.$search_text.'" OR LOWER(bo_alias)="'.$search_text.'" )'   );
    	     }   
	     
	     }
	     else if($dataIndx=='group_name'){
	     if($condition=='begin'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "'.$search_text.'%" OR LOWER(bo_grp_alias) LIKE "'.$search_text.'%" ))  ');
    	         
    	    }
    	 if($condition=='contain'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "%'.$search_text.'%" OR LOWER(bo_grp_alias) LIKE "%'.$search_text.'%" ))  ');
    	         
    	    }    
	   if($condition=='notcontain'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) NOT LIKE "%'.$search_text.'%" OR LOWER(bo_grp_alias) NOT LIKE "%'.$search_text.'%" ))  ');
    	         
    	    } 
      if($condition=='end'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) LIKE "%'.$search_text.'" OR LOWER(bo_grp_alias) LIKE "%'.$search_text.'" ))  ');
    	         
    	    }
      if($condition=='equal'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) = "'.$search_text.'" OR LOWER(bo_grp_alias) = "'.$search_text.'" ))  ');
    	         
    	    }
    	    
       if($condition=='notequal'){
    	         $builder->where('acc_grp_id IN (SELECT `acc_grp_id` FROM `'.$account_groupn_tabl.'` WHERE ( LOWER(acc_grp_name) != "'.$search_text.'" OR LOWER(bo_grp_alias) != "'.$search_text.'" ))  ');
    	         
    	    }
	    
	   } 
	    
	    $builder->where('comp_id', $comp_id);
	 	$result = $builder->get()->getResultArray();
        
         $records = array(); 	
		
        foreach($result as $values){  

            $state_info = $this->CommonModel->get_state_info($values['bo_country'],$values['bo_state']);	
			if($state_info)
				 $state_name = ucwords(strtolower($state_info['state_name']));
			 else
				 $state_name = '';
				 
			if($values['acc_grp_id'] != 0){
				$group_info   =  $this->main_group_info($values['acc_grp_id']);
			   	if(isset($group_info['acc_grp_name']))
				    $show_group = $group_info['acc_grp_name'];
				else
					$show_group = '';
			}
			/* else{
				$group_parent_info   =  $this->group_parent_info($values['bo_grp_primary']);
			   	if(isset($group_parent_info['acc_grp_parent']))
				    $show_group = $group_parent_info['acc_grp_parent']. ' <i>(PARENT)</i>';
				else
					$show_group = '';
			} */
			
			
			$checkbox_html ='<input name="batches_ids[]" class="checkbox batches_row" data-id="'.$values['bo_id'].'" type="checkbox" value="'.$values['bo_id'].'">';
				
				$records[] = array(	
			          'chkbx'         => $checkbox_html,           
 			          'acc_id'        => $values['bo_id'],
                      'account_name'  => ucwords($values['bo_name']),
					  'account_alias' => $values['bo_alias'],
					  'group_name'    => $show_group,
                      'city_name'     => $values['bo_city'],					                    
                      'state_name'    => $state_name,
					  'isedited'      => '0'
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


       echo  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($data)."}"; 
        
        
     } 
     public function get_account_group_name($id){
         $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($account_grp_tbl)->select('acc_grp_name')->where('acc_grp_id', $id)->get()->getRowArray();
         return $data['acc_grp_name'];
     }
     
  public function add_group($data){
       $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	  
	   $exists = $this->db->table( $account_grp_tbl)->where('LOWER(acc_grp_name)', strtolower(trim($data['acc_grp_name'])))->get()->getRowArray(); 
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
				  
			$data =  $this->db->table($account_grp_tbl)->orderBy('acc_grp_parent')->where('	acc_grp_parent_id','14')->get()->getResultArray();
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
            $builder->where('acc_grp_parent_id',14);
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
	 
 function branch_group_main_dropdown($group_id = 0){		      
             $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
				  
			$builder =  $this->db->table($account_grp_tbl);
			if($group_id != 0)
				$builder->where('acc_grp_id !=', $group_id);
             $builder->where('acc_grp_parent_id',14);
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
       $account_master_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($account_master_tbl)->where('bo_id', $account_id)->get()->getRowArray();   	   
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
   
}