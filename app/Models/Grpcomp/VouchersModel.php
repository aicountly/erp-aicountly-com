<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class VouchersModel extends Model	{

    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }
	
	
	public function getMasterData($voucherTxnId)
    {  $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	if($this->session->get('ses_boid')!='')     
	  return $this->db->table($comptxnmst_master)->where('bo_id', $this->session->get('ses_boid'))->where('master_id_type', 'acc')->where('voucher_txn_id', $voucherTxnId)->get()->getResultArray();
     else     
	 return $this->db->table($comptxnmst_master)->where('master_id_type', 'acc')->where('voucher_txn_id', $voucherTxnId)->get()->getResultArray();
    }
	public function getAccntTxnData($masterId,$voucher_txn_id)
    {  $acc_txntable =   $this->company_id.'_accnttxnnn_'.$masterId.'_'.$this->session->get('ses_comp_fy_id');
	   $builder = $this->db->table($acc_txntable);
	   $builder->select('SUM(acc_txn_amount) AS total_amount');
	   $builder->where('acc_txn_drcr','d');$builder->where('voucher_txn_id',$voucher_txn_id);
	   if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
	   $result = $builder->get()->getResult();
        return $result;
    }
	
	public function getAccountData($accountIds)
    {  $accmaster_table= $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        return $this->db->table($accmaster_table)->whereIn('acc_id', $accountIds)->get()->getResultArray();
    }
	
	
	function items_list($comp_id){
	   $item_master_tbl =  $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');	 
	   $data            =  $this->db->table($item_master_tbl)->where('comp_id', $comp_id)->orderBy('item_name','ASC')->get()->getResultArray();
	   $final_result    =  array();
	   $itemkeyval =array();
	   if($data){
		  foreach($data as $row){
		      $item_name      = $row['item_name'];
             // $final_result[] = array($row['item_id']=>$item_name);	
             
             $item_unit_info  = $this->item_unit_info($comp_id,$row['item_unit']); 
             if($item_unit_info)
              $item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
              else
              $item_unit_name ='' ;
              $final_result[]    = array("label"=>$item_name,"value"=>$row['item_id'],"item_unit_id"=>$row['item_unit'],'id'=>$row['item_id'],'item_unit'=>$item_unit_name);	
             $itemkeyval[$item_name]=$row['item_id'];
	        }
        }
      return array("items_array"=>$final_result,"item_name_array"=>$itemkeyval);	
     } 
   
    function item_unit_info($comp_id,$unit_id){
       $item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   return  $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getRowArray();  
         
     }
	 
	 function delete_narration_txn($voucher_txn_id){	 	
		$short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');	
	   
		$this->db->table($short_narr_tbl)->where('vch_txn_id', $voucher_txn_id)->delete();
	}
	
   function delete_all_narrations($voucher_txn_id){	 	
		$short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');	
		$long_narrn = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
	   
		$this->db->table($short_narr_tbl)->where('vch_txn_id', $voucher_txn_id)->delete();
		$this->db->table($long_narrn)->where('vch_txn_id', $voucher_txn_id)->delete();
	}
	
	 
    function units_dropdown($comp_id){
	   $item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   $data =  $this->db->table($item_unit_master_tbl)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getResultArray();
	   $final_result      = array();
	   if($data){
		  foreach($data as $row){
		      $item_name      = $this->enc_string->nc_string($row['item_unit'],'de');
              $final_result[] =array("label"=>$item_name,"value"=>$row['unit_id']);			   
	        }
        }
	  return $final_result;	
     }
	 
	public function add_voucher_cons_data($data){
		 $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		 
		  if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id'] = $bo_id;
	     $this->db->table($comp_vch_cons_tbl)->insert($data);	
		 return $this->db->insertID();	
	 }
   public function update_voucher_cons_data($data,$voucher_txn_id,$comp_id){
		 $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
		$this->db->table($comp_vch_cons_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('comp_id',$comp_id)->where('voucher_txn_id',$voucher_txn_id)->update($data);		 
         else 
		 $this->db->table($comp_vch_cons_tbl)->where('comp_id',$comp_id)->where('voucher_txn_id',$voucher_txn_id)->update($data);		 
	 }	 	 
  // update transactions under voucher
	public function update_voucher_trans($txn_id,$acc_id,$table_name,$comp_id,$update_data,$voucher_txn_id){
	  $this->db->table($table_name)->where('comp_id',$comp_id)->where('txn_id',$txn_id)->update($update_data);
	  
	  
	   // check row num datewise and update row num value in table 
	   $row_nums = $this->get_rownums($table_name);
	             if($row_nums){
	                 foreach($row_nums as $drow){
	                     $acc_txn_id = $drow['acc_txn_id'];
	                     $row_num    = $drow['row_num'];
	                     $txn_id     = $drow['txn_id'];
	                     $comp_id    = $drow['comp_id'];
	                     $updte_data = array("acc_txn_id"=>$row_num);
	                     
	                     $this->update_all_entries($table_name,$updte_data,$txn_id,$comp_id);
	                 }
	                   
	                    
	              } 
	  
	
   }	 
   
    public function acc_opn_balance_info($acc_id){
       $accoppybal_tbl =$this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();  
      }
	  
   // update account balances as per opnening balance cr/dr 
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
   
   
   	public function insert_voucher_trans($txn_id,$comp_id,$voucher_txn_id){
	  // insert comp_vch_txn_trail table for changing ion accounts sorting
	  $comp_vch_txn_trail = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	  $txn_trail_data = array("comp_id"=>$comp_id,"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
	  $this->db->table($comp_vch_txn_trail)->insert($txn_trail_data);
	   }	 
   
   
   public function remove_comp_vch_txn_trail($voucher_txn_id,$comp_id){
       $ses_comp_fy_id            =  $this->session->get('ses_comp_fy_id');
       $comp_vch_txn_trail_tbl    = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
       $this->db->table($comp_vch_txn_trail_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->delete(); 
      // echo $this->db->GetLastQuery();
   }
   
  
    public function get_voucher_no($voucher_id){
      $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
      $builder = $this->db->table($comp_vch_cons_tbl); 
      $builder->select('MAX(comp_vch_no) as max_comp_vch_no');
      $builder->where('comp_vch_series_id',$voucher_id); 
	  $builder->orderBy('voucher_txn_id','DESC');	
	  $result =  $builder->get()->getRowArray();
	 
      if($result){
          $max_comp_vch_no = $result['max_comp_vch_no'];
          return $max_comp_vch_no+1;
        }
      else
        return 1;
     }  
   
    public function remove_vouchers($voucher_type_id,$ids,$comp_id){
        
        $company_accounts = $this->comp_accounts_dropdown($comp_id);
       
		$ses_comp_fy_id            =  $this->session->get('ses_comp_fy_id');
		$comp_vch_txn_conso_tbl    = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
  	    $comp_vch_txn_trail_tbl    = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
  	    
  	   $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
  	   
		$ids                       = explode(",",$ids);
        foreach($ids as $voucher_txn_id){
			 $this->delete_narration_txn($voucher_txn_id);
			 
			
		    $this->db->table($comp_vch_txn_conso_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();		 
            $this->db->table($comp_vch_txn_trail_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->delete();
		    //$this->db->query("DROP TABLE `".$account_txn_table."` ");
	
		  $this->db->table($comptxnmst_master)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->where('master_id_type','acc')->delete();
		  
		  $this->delete_bills_txn($voucher_txn_id);
		  $this->delete_cc_txn($voucher_txn_id);
		  
		 if($company_accounts){
		     
		     foreach($company_accounts as $account_info){
		         $accound_id = $account_info['value'];
		         $account_table_name = $this->company_id.'_accnttxnnn_'.$accound_id.'_'.$this->session->get('ses_comp_fy_id');
		         $this->db->table($account_table_name)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->where('acc_id',$accound_id)->delete();
		         
		          // after deletion update table balances 
		         $row_nums = $this->get_rownums($account_table_name);
	             if($row_nums){
	                 foreach($row_nums as $drow){
	                     $acc_txn_id = $drow['acc_txn_id'];
	                     $row_num    = $drow['row_num'];
	                     $txn_id     = $drow['txn_id'];
	                     $comp_id    = $drow['comp_id'];
	                     $svoucher_txn_id  = $drow['voucher_txn_id'];
	                     
	                     // update voucher number first
	                     
	                     $voucher_number =  $this->update_voucherno_entries($comp_vch_txn_conso_tbl,$svoucher_txn_id,$comp_id);
	                     $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
	                     
	                     $this->update_all_entries($account_table_name,$updte_data,$txn_id,$comp_id);
	                     }
	                    $this->update_account_all_balances($account_table_name);
	                 
	                  } 
		         
		           }
		           
		        }
		        
		 	 }
		return TRUE;
	 }  
	 
   public function delete_transaction_byid($tn,$txnid,$comp_id,$sel_account_id){ 
     $this->db->table($tn)->where('txn_id',$txnid)->where('comp_id',$comp_id)->where('acc_id',$sel_account_id)->delete();
     echo $this->db->GetLastQuery();
   }	 

   	function update_account_balance($acc_id)
    {
        $bal=0; //include opening balance

        $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
		$balance = $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();
		if($balance){
			$bal  = $balance['acc_op_bal'];
		}

		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
		$result = $this->db->table($acc_txn_tbl)
        					->where('acc_id', $acc_id)
        					->orderBy('acc_txn_date', 'asc')
        					->orderBy('acc_txn_id', 'asc')
        					->get()->getResultArray();
							
		 if($result){
	        foreach($result as $key2 => $value){
	        	if($value['acc_txn_drcr'] == 'd')
	            	$bal += $value['acc_txn_amount'];
	            if($value['acc_txn_drcr'] == 'c')
	            	$bal += -$value['acc_txn_amount'];
	            
	            $this->db->table($acc_txn_tbl)->where('acc_txn_id', $value['acc_txn_id'])->update(['acc_bal' => $bal]);
	        } 
    	}  
    }
	 
   public function update_account_all_balances($table_name){
	   $this->db->transStart();
	    $builder = $this->db->table($table_name); 
		$builder->orderBy('acc_txn_id');	
        $result =  $builder->get()->getResultArray();
		$balance =0;
		$counter=0;
		$first_balance=0;
        if($result){
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
   
    public function get_account_balance($comp_id,$tbl_name,$account_id,$amount_type,$new_amount){
		$builder = $this->db->table($tbl_name); 
		$builder->orderBy('acc_txn_id','DESC');	
        $builder->limit('1'); 		
		$builder->where('comp_id', $comp_id);
		$builder->where('acc_id', $account_id);	
		$result =  $builder->get()->getRowArray();		
		 if(!$result)
		 { 
			$last_sums = 0;
			if($amount_type=='c')
				$balance = $last_sums - $new_amount;
			else if($amount_type=='d')
				$balance = $last_sums + $new_amount;
		 }	else{	
			if($amount_type=='c')
				$balance = $result['acc_bal'] - $new_amount;
			else if($amount_type=='d')
				$balance = $result['acc_bal'] + $new_amount;
			  }
		return $balance;	
		
		}	
		
 function update_narration($vch_txn_id,$txn_id,$narr_type,$narration_txt){
	  if($narr_type=='long'){
		  $long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
		  
		  $exists =  $this->db->table($long_narr_tbl)->where('vch_txn_id',$vch_txn_id)->where('txn_id', $txn_id)->get()->getRowArray();
          if($exists){
			$data          = array('vch_narr'=>trim($narration_txt));
			$this->db->table($long_narr_tbl)->where("vch_txn_id",$vch_txn_id)->update($data);  		  
		  }else{
			 $insert_data          = array('vch_narr'=>trim($narration_txt),'vch_txn_id'=>$vch_txn_id,'txn_id'=>$txn_id); 
			 $this->db->table($long_narr_tbl)->insert($insert_data);  
		  }
	  }
	  else  if($narr_type=='short'){
		$short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
		$data = array('vch_short_narr'=>trim($narration_txt));
		$this->db->table($short_narr_tbl)->where("txn_id",$txn_id)->where("vch_txn_id",$vch_txn_id)->update($data);  
	  }   
	 
 }		
		
  function get_narration_info($voucher_txn_id,$narr_type,$txn_id){
	 if($narr_type=='long'){
        $long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($long_narr_tbl)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		
	 }
	else  if($narr_type=='short'){
        $short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
	}		
	  
  }		
  
  function save_narration($vch_txn_id,$txn_id,$narr_type,$narration_txt){
	  
	  if($narr_type=='long'){
		  $long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
		  $data = array('vch_txn_id'=>$vch_txn_id,'txn_id'=>$txn_id,'vch_narr'=>trim($narration_txt));
		 $this->db->table($long_narr_tbl)->insert($data); 
	  }
	  else  if($narr_type=='short'){
		$short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
		$data = array('vch_txn_id'=>$vch_txn_id,'txn_id'=>$txn_id,'vch_short_narr'=>trim($narration_txt));
		$this->db->table($short_narr_tbl)->insert($data);  
	  }  
	  
  }	
   
   public function add_account_transactions($insert_table_name,$data,$txn_id){
       $data['txn_id'] = $txn_id;
       $account_total_sum =  $this->get_account_balance($data["comp_id"],$insert_table_name,$data["acc_id"],$data["acc_txn_drcr"],$data["acc_txn_amount"]);
	  $data['acc_bal'] = $account_total_sum;
	  $this->db->table($insert_table_name)->insert($data);
       
   }	 
   
   public function update_all_entries($table_name,$update_data,$txn_id,$comp_id){
       $this->db->table($table_name)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->update($update_data);	
   }
	 
    public function add_voucher_transactions($insert_table_name,$data,$voucher_txn_data,$voucher_txn_name,$voucher_txn_id,$billsdata,$ccdata){
       if(isset($data['acc_txn_narr']))
	   $last_acc_txn_narr = $data['acc_txn_narr'];
      else
	   $last_acc_txn_narr ='';
   
 if(isset($data['acc_txn_narr']))     
	 unset($data['acc_txn_narr']);
	  
		// insert into transaction master table also
        $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
        
        // voucher conso table 
        $vhtxnconso_table = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
        
        $comp_vch_series_id  = $voucher_txn_data['comp_vch_series_id'];
        // insert in comp_txn_master /////////////////////////////// 
        $txn_data = array("comp_id"=>$data["comp_id"],"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$data["acc_id"],"master_id_type"=>'acc');
        $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
        $txn_id =  $this->db->insertID();
		
		// save long narration and short narration 
	
		 $this->save_narration($voucher_txn_id,$txn_id,'short',$last_acc_txn_narr);  
		        
        // Add bills data
        if($billsdata)
        {
            foreach($billsdata as $key => $value)
            {
                $bill_ref_id = 0;
                if($value['method'] == 'New Ref.')
                {
                    $bill_master_data = [
                        'bills_ref_name' => $value['reference'],
                        'acc_id'         => $value['account_id'],
                        'bills_status'   => 'pending',
                        'bill_due_date'  => date("Y-m-d", strtotime($value['due_date'])),
                    ];
                    $bill_ref_id = $this->add_bill_master($bill_master_data);
                }
                if($value['method'] == 'Adjustment')
                {
                    if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
                        $bill_ref_id = $this->getUndefinedBillRefId($value['account_id']);  
                    }
                    else{
                        $bill_ref_id = $value['reference_id'];
                        $bill_master_data = [
                            'bill_due_date'  => date("Y-m-d", strtotime($value['due_date']))
                        ];
                        $this->update_bill_master($bill_ref_id, $bill_master_data);
                    }
                    
                }
                if($bill_ref_id)
                {
                    $bill_txn_data = [
                        'bills_ref_id'       => $bill_ref_id,
                        'comp_id'            => $this->company_id,
                        'acc_id'             => $value['account_id'],
                        'voucher_txn_id'     => $voucher_txn_id,
                        'voucher_type_id'    => $voucher_txn_data['voucher_type_id'],
                        'comp_vch_series_id' => $comp_vch_series_id,
                        'bills_txn_date'     => $voucher_txn_data['voucher_date'],
                        'bills_txn_drcr'     => $value['drcr'],
                        'bills_txn_amt'      => $value['amount'],
                        'bills_txn_bal'      => 0,                   
                    ];
                    $this->add_bill_txn($bill_txn_data);
					$txnid = $this->db->insertID();
					$this->save_voucher_narration($voucher_txn_id,$txnid,'short',$value['narration']);
                    
                    $this->update_bill_txn_balance($bill_ref_id);
                }
            }
        }
        
        if($ccdata)
        {
            foreach($ccdata as $key => $value)
            {
                $cc_txn_data = [
                    'cc_id'              => $value['cc_id'],
                    'comp_id'            => $this->company_id,
                    'acc_id'             => $value['account_id'],
                    'voucher_txn_id'     => $voucher_txn_id,
                    'voucher_type_id'    => $voucher_txn_data['voucher_type_id'],
                    'comp_vch_series_id' => $comp_vch_series_id,
                    'cc_txn_date'        => $voucher_txn_data['voucher_date'],
                    'cc_txn_drcr'        => $value['cc_txn_drcr'],
                    'cc_txn_amt'         => $value['cc_txn_amt'],
                    'cc_txn_bal'         => 0                   
                ];
                $this->add_cc_txn($cc_txn_data);
				$txnid = $this->db->insertID();
				$this->save_voucher_narration($voucher_txn_id,$txnid,'short',$value['cc_txn_narr']);
                
                $this->update_cc_txn_balance($value['cc_id']);
                
            }
        }
        
        // Add voucher transdactions trail 
        $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
        $voucher_trail_data = array("comp_id"=>$data["comp_id"],"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
        $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);	 
        
        $data['txn_id'] = $txn_id;	
        $account_total_sum =  $this->get_account_balance($data["comp_id"],$insert_table_name,$data["acc_id"],$data["acc_txn_drcr"],$data["acc_txn_amount"]);
        $data['acc_bal'] = $account_total_sum;
        
        
        
        unset($data['acc_txn_narr']);
        $this->db->table($insert_table_name)->insert($data);
        $acc_txn_id =  $this->db->insertID();		  
        
        
        // Update  comp_txn_master  
        $this->db->table($comp_txn_master_tbl)->where('txn_id',$txn_id)->update(array('voucher_txn_id'=>$voucher_txn_id));
        
        
        $final_sucvcess=0;
        // check row num datewise and update row num value in table 
        $row_nums = $this->get_rownums($insert_table_name);
        if($row_nums){
            foreach($row_nums as $drow){
                $acc_txn_id = $drow['acc_txn_id'];
                $row_num    = $drow['row_num'];
                $txn_id     = $drow['txn_id'];
                $comp_id    = $drow['comp_id'];
                $voucher_txn_id  = $drow['voucher_txn_id'];
                
                // update voucher number first
                
                $voucher_number =  $this->update_voucherno_entries($vhtxnconso_table,$voucher_txn_id,$comp_id);
                
                
                $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
                
                $this->update_all_entries($insert_table_name,$updte_data,$txn_id,$comp_id);
            }
            $this->update_account_all_balances($insert_table_name);
            $final_sucvcess++;
        }
    }
    
    
    public function update_voucherno_entries($tblname,$voucher_txn_id,$comp_id){
      // get voucher series id from voucher txn id 
        $tablerpow =    $this->db->table($tblname)->where('voucher_txn_id', $voucher_txn_id)->where('comp_id', $comp_id)->get()->getRowArray(); 
        $comp_vch_series_id  = $tablerpow['comp_vch_series_id'];
        
        
        $builder = $this->db->table($tblname);
        $builder->select('*,ROW_NUMBER() OVER (ORDER BY voucher_date) row_num');
        $builder->where("comp_vch_series_id",$comp_vch_series_id);
        $result = $builder->get()->getResultArray();
        
        if($result){
            foreach($result as $drow){
                $row_num         = $drow['row_num'];
                $svoucher_txn_id = $drow['voucher_txn_id'];
                if($row_num==0)
                    $new_row_num=1;
                else
                    $new_row_num=$row_num;
                    
                $update_voucher_num = array("comp_vch_no"=>$new_row_num);
                $this->db->table($tblname)->where('voucher_txn_id',$svoucher_txn_id)->where('comp_id',$comp_id)->update($update_voucher_num);	
            }
        }
        
      $restablerpow =  $this->db->table($tblname)->where('voucher_txn_id', $voucher_txn_id)->where('comp_id', $comp_id)->get()->getRowArray(); 
      return  $restablerpow['comp_vch_no'];   
        
    }
    
    public function get_rownums($tblname){
        $builder = $this->db->table($tblname);
        $builder->select('*,ROW_NUMBER() OVER (ORDER BY acc_txn_date) row_num');
        $result = $builder->get()->getResultArray();
        return $result;
     }
	
  
   public function ajax_vouchers_list($voucher_type_id,$comp_id){ 	   
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	
		$builder->where('voucher_type_id', $voucher_type_id);
		$result = $builder->get()->getResultArray();
       $records= array();
        foreach($result as $values){
		   $voucher_first_trans = $this->voucher_first_transaction($comp_id,$values['voucher_txn_id']);	
		    		   
		   if($voucher_first_trans){
			   foreach($voucher_first_trans as $trsnkey => $transvalues){				    	
				   $records[] = array(  
                          'voucher_txn_id'=> $values['voucher_txn_id'],                   
						  'txn_date'=>date('d-M-Y',strtotime($transvalues['txn_date']))	,
						  'account_name'=>$transvalues['account_name'],		
						  'debit'=>$transvalues['debit'],	
						  'credit'=>$transvalues['credit']			
				      );  
			      }
		        }            
		     }	 		
      return $records;
   }
  
   function sum_trail_debite_credit($comp_id,$voucher_txn_id,$txn_id){
	   
	    $vch_txn_trail_tbl = $comp_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	    $txn_rows          = array();
	    $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');	   
	    $builder           = $this->db->table($vch_txn_trail_tbl); 
        $builder->orderBy('txn_id');                
		$builder->where('comp_id', $comp_id);	
		$builder->where('voucher_txn_id', $voucher_txn_id);
		
		$result = $builder->get()->getResultArray();
		
		if($result){
		foreach($result as $row){	
			$txn_id   = $row['txn_id'];
			$data_list = $this->get_comp_all_acc_transactions($voucher_txn_id,$comp_id,$txn_id);
			
			$txn_rows[$voucher_txn_id][] = $data_list;
		    }
		}
		
		
		$all_credit_list=array();
		$all_debit_list=array();
		 $counter=0;
		foreach($txn_rows as $key =>  $txrow){		
		    if(!empty($txrow)){	
			
			   $credit=$debit=0;
			  
				 foreach($txrow as $keynew =>  $txrow_new){
					  if(!empty($txrow_new)){
                        $counter++;				
							
                            $credit=$credit+$txrow_new['0']['credit'];
							$debit=$debit+$txrow_new['0']['debit'];
							
							$all_credit_list[$counter][$key]['cr'] =$txrow_new['0']['credit'];
							$all_credit_list[$counter][$key]['dr'] = $txrow_new['0']['debit'];
					  }
				 }
			}
		}
	
	//echo'<pre>';
	//print_r($all_credit_list);
	/* $final_array = array();
	foreach($all_credit_list as $crvchtxn_id => $cr_sum_rows){
		$final_array[$crvchtxn_id]['credits'] = array_sum($cr_sum_rows);
	}
	foreach($all_debit_list as $drvchtxn_id => $dr_sum_rows){	
		//$final_array[$drvchtxn_id]['debits']  = array_sum($dr_sum_rows);
	}
	echo '<pre>';
	print_r($final_array);
		//$final_array = array("all_credit"=>$all_credit,"all_debit"=>$all_debit);
		//return $final_array; */
   }

   
   function voucher_first_transaction($comp_id,$voucher_txn_id){
	    $txn_rows=array();
	    $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');	  
	    $vch_txn_trail_tbl = $comp_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	    $builder           = $this->db->table($vch_txn_trail_tbl ); 
	    
        $builder->orderBy('txn_id');                
		$builder->where('comp_id', $comp_id);	
		$builder->where('voucher_txn_id', $voucher_txn_id);
		$result = $builder->get()->getRowArray();
		if($result){
			$txn_id = $result['txn_id'];
			$txn_rows = $this->get_comp_all_acc_transactions($voucher_txn_id,$comp_id,$txn_id);
			// get voucher all trail txn data with sum of credit & debit
           $trial_credit_debit = $this->sum_trail_debite_credit($comp_id,$voucher_txn_id,$txn_id);
			return $txn_rows;
		}else
			return $txn_rows;
     }
   
   function get_company_all_accounts($company_id){
	    $account_master_tbl = $company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name','acc_grp_id'));
        $builder->where('comp_id', $company_id);
		$result = $builder->get()->getResultArray();
	    return $result;
   }
   
   public function get_comp_all_acc_transactions($voucher_txn_id,$comp_id,$txn_id,$limit=''){
	   $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');
	   $list   = array();
	   $company_all_accounts = $this->get_company_all_accounts($comp_id);
	   if($company_all_accounts){
		   foreach($company_all_accounts as $company_row){
			    $account_id         =  $company_row['acc_id'];
				$show_account_name  =  $company_row['acc_name'];
			    $voucher_txn_table  =  $comp_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;	
			    
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('txn_id');         
				$builder->where('txn_id', $txn_id);		
				$builder->where('comp_id', $comp_id);
				$builder->where('acc_id', $company_row['acc_id']);
                
				$result = $builder->get()->getResultArray();
			    if($result){			
				   foreach($result as $values){				
					  $posted_on = $values['posted_on'];
					  if($values['acc_txn_drcr']=='d'){
						 $debit  = $values['acc_txn_amount'];
						 $credit = '0.00';
					   }
					   else if($values['acc_txn_drcr']=='c'){
						 $credit = $values['acc_txn_amount'];
						 $debit  = '0.00';
						 }
						$list[] = array("comp_id"=>$comp_id,"txn_id"=>$txn_id,"txn_date" =>$values['acc_txn_date'],'account_name'=>$show_account_name,'debit'=>$debit,'credit'=>$credit,'posted_on'=>$posted_on);
				       } 
				    }
				 }
	          }
	   return $list;
   }
  
   public function get_cc_txn_data($voucher_txn_id)
   {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $acc_mst_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $final = [];
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($acc_mst_tbl.'.acc_id');
        $builder->join($acc_mst_tbl, $acc_mst_tbl.'.acc_id ='.$cc_txn_tbl.'.acc_id');
        $builder->select($acc_mst_tbl.'.acc_name');
        $builder->where('voucher_txn_id', $voucher_txn_id);
        $builder->where($cc_txn_tbl.'.cc_id !=', 1);
        $builder->groupBy($acc_mst_tbl.'.acc_id');
        $builder->orderBy($acc_mst_tbl.'.acc_id', 'asc');
        $result_ = $builder->get()->getResultArray();
        
        foreach($result_ as $key_ => $value_)
        {
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select($cc_txn_tbl.'.cc_id, '.$cc_txn_tbl.'.acc_id, cc_txn_amt, cc_txn_drcr, cc_txn_narr, cc_txn_id');
            $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
            $builder->select($cc_mst_tbl.'.cc_name');
            
            $builder->where('voucher_txn_id', $voucher_txn_id);
            $builder->where($cc_txn_tbl.'.acc_id', $value_['acc_id']);
            $builder->where($cc_txn_tbl.'.cc_id !=', 1);
            $result = $builder->get()->getResultArray();
            
            $final[] = [
                    'acc_id' => $value_['acc_id'],
                    'acc_name' => $value_['acc_name'],
                    'cc_txn_list' => $result
                ];
        }
        
        
        // echo "<pre>";print_r($final);exit;
        return $final;
   }
   
    public function get_bills_txn_data($voucher_txn_id)
    {
        $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $acc_mst_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $final = [];
        $builder = $this->db->table($bill_txn_tbl);
        $builder->select($acc_mst_tbl.'.acc_id');
        $builder->join($acc_mst_tbl, $acc_mst_tbl.'.acc_id ='.$bill_txn_tbl.'.acc_id');
        $builder->select($acc_mst_tbl.'.acc_name');
        $builder->join($bill_mst_tbl, $bill_mst_tbl.'.bills_ref_id ='.$bill_txn_tbl.'.bills_ref_id');
        $builder->where('voucher_txn_id', $voucher_txn_id);
        $builder->where('bills_ref_name !=', 'UNDEFINED');
        $builder->groupBy($acc_mst_tbl.'.acc_id');
        $builder->orderBy($acc_mst_tbl.'.acc_id', 'asc');
        $result_ = $builder->get()->getResultArray();
        
        foreach($result_ as $key_ => $value_)
        {
            $builder = $this->db->table($bill_txn_tbl);
            $builder->select($bill_txn_tbl.'.bills_ref_id, '.$bill_txn_tbl.'.acc_id, bills_txn_amt, bills_txn_drcr, bills_txn_narr');
            $builder->join($bill_mst_tbl, $bill_mst_tbl.'.bills_ref_id ='.$bill_txn_tbl.'.bills_ref_id');
            $builder->select($bill_mst_tbl.'.bills_ref_name, bill_due_date');
            $builder->where('voucher_txn_id', $voucher_txn_id);
            $builder->where($bill_txn_tbl.'.acc_id', $value_['acc_id']);
            $builder->where('bills_ref_name !=', 'UNDEFINED');
            $result = $builder->get()->getResultArray();
            
            $final[] = [
                    'acc_id' => $value_['acc_id'],
                    'acc_name' => $value_['acc_name'],
                    'bills_txn_list' => $result
                ];
        }
        
        
        // echo "<pre>";print_r($final);exit;
        return $final;
    }
   
   public function ajax_vouchers_transactions_list($voucher_txn_id){ 
	    $comp_id                =  $this->session->get('ses_company_id');
	    $base_url               = base_url().'/'.getenv('AdminPath');
		$ses_comp_fy_id         = $this->session->get('ses_comp_fy_id');
		$voucher_txn_master_tbl = $comp_id.'_vhtxntrail_'.$ses_comp_fy_id;
		$builder                = $this->db->table($voucher_txn_master_tbl); 
        $builder->orderBy('comp_vch_txn_trail_id');      
        $builder->where('voucher_txn_id', $voucher_txn_id);			
		$builder->where('comp_id', $comp_id);	
		$result = $builder->get()->getResultArray(); 
	
		$all_records = array();		
        foreach($result as $values){			
			$txn_tables_result =  $this->all_transactions($comp_id,$values['txn_id']);
			 if($txn_tables_result)
			    $all_records[$values['txn_id']] = $txn_tables_result;
		     }	
			
 	    return $all_records;
     }
    function voucher_txn_ids($voucher_txn_id,$comp_id){
		$ses_comp_fy_id       = $this->session->get('ses_comp_fy_id');
		$voucher_trail_table  = $comp_id.'_vhtxntrail_'.$ses_comp_fy_id;	
		
		$builder = $this->db->table($voucher_trail_table); 
		$builder->orderBy('txn_id');         
		$builder->where('voucher_txn_id', $voucher_txn_id);		
		$builder->where('comp_id', $comp_id);
		$result = $builder->get()->getResultArray();
		return $result;
	}
   function all_transactions($comp_id,$txn_id){
       $groups = $this->get_sundry_groups();
	   $cc_groups = $this->get_cc_groups();
	   
	   $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');
	   $list   = array();
	   $company_all_accounts = $this->get_company_all_accounts($comp_id);
	   if($company_all_accounts){
		   foreach($company_all_accounts as $company_row){
			    $account_id         =  $company_row['acc_id'];
				$show_account_name  =  $company_row['acc_name'];
			    $voucher_txn_table  =  $comp_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;	
			   
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('txn_id');         
				$builder->where('txn_id', $txn_id);		
				$builder->where('comp_id', $comp_id);
				$builder->where('acc_id', $company_row['acc_id']);                
				$result = $builder->get()->getResultArray();
			    if($result){			
				   foreach($result as $values){	
				       $get_narration_info = $this->get_narration_info($values['voucher_txn_id'],'short',$values['txn_id']);
				       if($get_narration_info){
						   $short_narration = $get_narration_info ['vch_short_narr'];
					   }else
						   $short_narration = '';
				   
                        $account_id   = $values['acc_id'];	
                        $acc_txn_drcr = $values['acc_txn_drcr'];                        				  
                        $posted_on    = $values['posted_on'];
                        if($values['acc_txn_drcr']=='d'){
                            $debit  = $values['acc_txn_amount'];
                            $credit = '0.00';
                        }
                        else if($values['acc_txn_drcr']=='c'){
                            $credit = $values['acc_txn_amount'];
                            $debit  = '0.00';
                        }
                        
                        $acc_grp_id = $company_row['acc_grp_id'];
                        
                        
                        $is_sundry = 0;
                        if(in_array($acc_grp_id, $groups)){
                           $is_sundry = 1;
                        }
                        $is_cc = 0;
                        if(in_array($acc_grp_id, $cc_groups)){
                           $is_cc = 1;
                        }
                        
                        $list = array(
                            "acc_txn_narr"=>$short_narration,
                            "comp_id"=>$comp_id,
                            "txn_id"=>$txn_id,
                            "txn_date" =>$values['acc_txn_date'],
                            'account_id'=>$account_id,
                            'acc_txn_drcr'=>$acc_txn_drcr,
                            'account_name'=>$show_account_name,
                            'debit'=>$debit,
                            'credit'=>$credit,
                            'posted_on'=>$posted_on,
                            'is_sundry'=>$is_sundry,
                            'is_cc'=>$is_cc
                            );
				       } 
				    }
				 }
	          }
	   return $list;  
   }
   
 
   function get_company_accounts($comp_id){	
        $searchtext        = $_GET['term'];
       
	   $account_master_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($account_master_tbl)->where('( `acc_name` LIKE  "%'.$searchtext.'%" OR `acc_name_alias` LIKE  "%'.$searchtext.'%") ')->where('comp_id', $comp_id)->orderBy('acc_name','ASC')->get()->getResultArray();

	   $final_result      = array();
	   $groups = $this->get_sundry_groups();
	   $cc_groups = $this->get_cc_groups();
	   if($data){
		  foreach($data as $row){
		       $account_id        = $row['acc_id'];
		       $account_name      = $row['acc_name'];
		       $account_alias     = $row['acc_name_alias'];
		       
		       $is_sundry = 0;
		       if(in_array($row['acc_grp_id'], $groups)){
		           $is_sundry = 1;
		       }
		       $is_cc = 0;
		       if(in_array($row['acc_grp_id'], $cc_groups)){
		           $is_cc = 1;
		       }
		       
               $final_result[]    = array("label"=>$account_name,"value"=>$account_id,"is_sundry"=>$is_sundry,"is_cc"=>$is_cc);	
		      }
        }
        
      
	    return json_encode($final_result);  
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
    
    function get_cc_groups()
    {
        
		$p_groups = [7,11,19];
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
     
   
    function get_voucher_cons_info($voucher_txn_id,$comp_id){	 
	   $comp_voucher_type_tbl = $comp_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($comp_voucher_type_tbl)->where('voucher_txn_id', $voucher_txn_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    } 	
		
    function get_voucher_info($voucher_type_id,$comp_id){	 
	  $comp_voucher_type_tbl = $comp_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($comp_voucher_type_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }   
	
	function get_account_info($acc_id){	 
	  $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($account_master_tbl)->where('acc_id', $acc_id)->get()->getRowArray();   	   
    } 
	


	 function comp_voucher_series($comp_id,$voucher_type_id){
		 $comp_vch_series_tbl = $comp_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($comp_vch_series_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $comp_id)->orderBy('comp_vch_series','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['comp_vch_series_id']] = $row['comp_vch_series'];			   
	        }
        }
	  return $final_result;	
     } 
     
    function comp_accounts_byname($comp_id){
       $account_master_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   $data               =  $this->db->table($account_master_tbl)->where('comp_id', $comp_id)->orderBy('acc_name','ASC')->get()->getResultArray();
	   $final_result       = array();
	   if($data){
		  foreach($data as $row){
		       $account_id     = $row['acc_id'];
		       $account_name   = $row['acc_name'];
               $final_result[$account_name] = $account_id;	
	         }
	         
           }
	     return $final_result;  
    } 
	 
	function comp_accounts_dropdown($comp_id){		
	$account_master_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($account_master_tbl)->where('comp_id', $comp_id)->orderBy('acc_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   if($data){
		  foreach($data as $row){
		       $account_id        = $row['acc_id'];
		       $account_name      = $row['acc_name'];
               //$final_result[]  = array($account_id=>$account_name);	
               $final_result[]    = array("label"=>$account_name,"value"=>$account_id);	
		      
             // $final_result[] = array($account_id=>ucwords($this->enc_string->nc_string($row['acc_name'],'de')));			   
	        }
        }
	  return $final_result;	
     }
    function get_account_bill_refs($account_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)->select('bills_ref_id as id, bills_ref_name as label, bills_ref_name as value, bill_due_date, DATE_FORMAT(bill_due_date, "%d-%m-%Y") as due_date')->where('acc_id', $account_id)->where('bills_ref_name !=', 'UNDEFINED')->get()->getResultArray();
        
        return $data;
    }
    function get_cc()
    {
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($cc_mst_tbl)->select('cc_id as id, cc_name as label, cc_name as value')->where('cc_name !=', 'UNDEFINED')->get()->getResultArray();
        
        return $data;
    }
    
    function getUndefinedBillRefId($account_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)->select('bills_ref_id')->where('acc_id', $account_id)->where('bills_ref_name', 'UNDEFINED')->get()->getRowArray();
        
        return $data['bills_ref_id'];
    }
     
    function add_bill_master($data)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)->insert($data);
         
        return $this->db->insertID();
    }
    function update_bill_master($id,$data)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)->where('bills_ref_id', $id)->update($data);
    }
    function add_bill_txn($data)
    {
        $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_txn_tbl)->insert($data);
    }
    function delete_bills_txn($voucher_txn_id)
    {
    	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($bill_txn_tbl)->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();

    	$bills_ref_ids = [];
    	if($result){
	    	foreach($result as $key => $value){
	        	$bills_ref_ids[] = $value['bills_ref_id'];
	    	}
	    }

        $this->db->table($bill_txn_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();

        if($bills_ref_ids){
        	foreach($bills_ref_ids as  $bills_ref_id){
	        	$this->update_bill_txn_balance($bills_ref_id);
	        }
        }
    }
    function update_bill_txn_balance($bills_ref_id)
    {
        $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
        $result = $this->db->table($bill_txn_tbl)->where('bills_ref_id', $bills_ref_id)->orderBy('bills_txn_date', 'asc')->orderBy('bills_txn_id', 'asc')->get()->getResultArray();
        
        $bal = 0;
        if($result)
        {
            foreach($result as $key => $value)
            {
                if($value['bills_txn_drcr'] == 'D'){
                    $bal += $value['bills_txn_amt'];
                }
                if($value['bills_txn_drcr'] == 'C'){
                    $bal += -$value['bills_txn_amt'];
                }
                
                $data = ['bills_txn_bal' => $bal];
                $this->db->table($bill_txn_tbl)->where('bills_txn_id', $value['bills_txn_id'])->update($data);
            } 
        }
        
    }
    function update_all_bill_txn_balance()
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $result = $this->db->table($bill_mst_tbl)->get()->getResultArray();
        
        if($result){
            foreach($result as $key => $value){
                $this->update_bill_txn_balance($value['bills_ref_id']);
            }
        }
    }
    
    function add_cc_txn($data)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($cc_txn_tbl)->insert($data);
    }
    function update_cc_txn_balance($cc_id)
    {
        $bal = 0;
        
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $result = $this->db->table($cc_mst_tbl)->where('cc_id', $cc_id)->get()->getRowArray();
        
        if($result)
        {
            if($result['cc_op_drcr'] == 'dr')
                $bal = $result['cc_op_bal'];
            if($result['cc_op_drcr'] == 'cr')
                $bal = -$result['cc_op_bal'];
        }
        
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $result = $this->db->table($cc_txn_tbl)->where('cc_id', $cc_id)->orderBy('cc_txn_date', 'asc')->get()->getResultArray();
        
        
        if($result)
        {
            foreach($result as $key => $value)
            {
                if($value['cc_txn_drcr'] == 'D'){
                    $bal += $value['cc_txn_amt'];
                }
                if($value['cc_txn_drcr'] == 'C'){
                    $bal += -$value['cc_txn_amt'];
                }
                
                $data = ['cc_txn_bal' => $bal];
                $this->db->table($cc_txn_tbl)->where('cc_txn_id', $value['cc_txn_id'])->update($data);
            } 
        }
        
    }
    function update_all_cc_txn_balance()
    {
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $result = $this->db->table($cc_mst_tbl)->get()->getResultArray();
        
        if($result){
            foreach($result as $key => $value){
                $this->update_cc_txn_balance($value['cc_id']);
            }
        }
    }
    function delete_cc_txn($voucher_txn_id)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $result = $this->db->table($cc_txn_tbl)->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();
        $cc_ids = [];

        if($result){
		    foreach ($result as $key => $value) {
		    	$cc_ids[] = $value['cc_id'];
		    }
		}
        $this->db->table($cc_txn_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();

        if($cc_ids){
	        foreach ($cc_ids as $cc_id) {
	        	$this->update_cc_txn_balance($cc_id);
	        }
	    }
    }
    
    function delete_comp_txn($voucher_txn_id)
    {
        $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($comptxnmst_master)->where('voucher_txn_id', $voucher_txn_id)->delete();
    }
    function delete_acc_txn($account_id, $voucher_txn_id)
    {
        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($acc_txn_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();
    }
    function update_account($account_id)
    {
        $insert_table_name = $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
        $row_nums = $this->get_rownums($insert_table_name);
        if($row_nums){
            foreach($row_nums as $drow){
                $acc_txn_id = $drow['acc_txn_id'];
                $row_num    = $drow['row_num'];
                $txn_id     = $drow['txn_id'];
                $comp_id    = $drow['comp_id'];
                $voucher_txn_id  = $drow['voucher_txn_id'];
                
                // update voucher number first
                $vhtxnconso_table = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
                $voucher_number =  $this->update_voucherno_entries($vhtxnconso_table,$voucher_txn_id,$this->company_id);
                
                
                $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
                
                $this->update_all_entries($insert_table_name,$updte_data,$txn_id,$this->company_id);
            }
            $this->update_account_all_balances($insert_table_name);
        }
    }
}